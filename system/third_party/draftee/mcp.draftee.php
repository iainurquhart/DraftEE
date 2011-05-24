<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Basic workflow module for entries.
 *
 * @package		Draftee
 * @subpackage	ThirdParty
 * @category	Modules
 * @author		Iain
 * @link		http://localhost:8888/draftee/
 */
class Draftee_mcp
{
	var $base;			// the base url for this module
	var $form_base;		// base url for forms
	var $module_name = "draftee";

    /**
     * @var Devkit_code_completion
     */
    var $EE;

	function Draftee_mcp( $switch = TRUE )
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
		$this->base	 	 = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->module_name;
		$this->form_base = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->module_name;

        // uncomment this if you want navigation buttons at the top
/*		$this->EE->cp->set_right_nav(array(
				'home'			=> $this->base,
				'some_language_key'	=> $this->base.AMP.'method=some_method_here',
			));
*/
	}


	function create_draft()
	{
		$entry_id = $this->EE->input->get('entry_id');
		$channel_id = $this->EE->input->get('channel_id');

		// go get all the entry data for this entry.
		$this->EE->db->select('*');
		$this->EE->db->from('channel_titles');
		$this->EE->db->where('channel_titles.entry_id', $entry_id);
		$this->EE->db->join('channel_data', 'channel_titles.entry_id = channel_data.entry_id');

		$query = $this->EE->db->get();

		// right, have we got the info
		if ($query->num_rows() > 0)
		{

			// prep the api lib
			$this->EE->load->library('api');
			$this->EE->api->instantiate('channel_entries');

			foreach ($query->result() as $row)
			{

				$data = (array) $row;

				// ditch the entry_id for the insert
				unset($data['entry_id']);

				// prefix with [DRAFT], if it doesn't already :) one day I'll learn regex
				$data['title'] = str_replace('[DRAFT] ', '', $data['title']);
				$data['title'] = '[DRAFT] '.$data['title'];

				// we'll update the entry date to now,
				// when we move the data back to the parent owner, we'll leave original value
				$data['entry_date'] = $this->EE->localize->now;
				$data['author_id'] = $this->EE->session->userdata('member_id');

				// making an assumption on status for now
				// @todo move to setting maybe?
				$data['status'] = 'Draft';

				if ($this->EE->api_channel_entries->submit_new_entry($channel_id, $data) === FALSE)
				{
					$resp['msg'] = 'could not create entry!';
				}
				else
				{
					$resp['msg'] = 'entry_created';
					// get the entry id from the submit_new_entry
					$resp['draft_entry_id'] = $this->EE->api_channel_entries->entry_id;
					$resp['draft_channel_id'] = 1;

					// log the draft into draftee_drafts
					$key_data = array(
						'id' => NULL,
		               	'parent_id' => $row->entry_id,
		               	'parent_last_edit' => $row->edit_date,
		               	'draft_id' => $resp['draft_entry_id'],
		               	'pushed' => 0
		            );
		            $this->EE->db->insert('draftee_drafts', $key_data);

					// and we're away!
					$this->EE->output->send_ajax_response($resp);
				}
			}
		}
		else
		{
			// @todo
			$resp['msg'] = 'Something messed up';
			$this->EE->output->send_ajax_response($resp);
		}

	}

	function publish_draft()
	{

		$entry_id 		= $this->EE->input->get('entry_id');
		$channel_id 	= $this->EE->input->get('channel_id');
		$parent_id 		= $this->EE->input->get('parent_id');
		$close_drafts 	= $this->EE->input->get('close_drafts');

		if(!$entry_id || !$channel_id || !$parent_id)
		{
			$resp['msg'] = 'error';
			$this->EE->output->send_ajax_response($resp);
		}


		$this->EE->db->select('*');
		$this->EE->db->from('channel_titles');
		$this->EE->db->where('channel_titles.entry_id', $entry_id);
		$this->EE->db->join('channel_data', 'channel_titles.entry_id = channel_data.entry_id');
		$query = $this->EE->db->get();

		// right, have we got the info
		if ($query->num_rows() > 0)
		{

			// prep the api lib
			$this->EE->load->library('api');
			$this->EE->api->instantiate('channel_entries');

			foreach ($query->result() as $row)
			{
				$data = (array) $row;

				$data['title'] = str_replace('[DRAFT] ', '', $data['title']);

				// get rid of a bunch of stuff that is outside of draftee jurisdiction
				unset($data['entry_id']);
				unset($data['author_id']);
				unset($data['status']);
				unset($data['view_count_one']);
				unset($data['view_count_two']);
				unset($data['view_count_three']);
				unset($data['view_count_four']);
			    unset($data['dst_enabled']);
			    unset($data['year']);
			    unset($data['month']);
			    unset($data['day']);
			    unset($data['url_title']);
			    unset($data['recent_comment_date']);
			    unset($data['comment_total']);
				unset($data['versioning_enabled']);

				// print_r($data);

				$this->EE->api_channel_entries->update_entry($parent_id, $data);

			}
		}

		if($close_drafts)
		{
			$update_ids = array();
			$this->EE->db->select('*');
			$this->EE->db->from('draftee_drafts');
			$this->EE->db->where('parent_id', $parent_id);
			$query = $this->EE->db->get();

			if ($query->num_rows() > 0)
			{
				foreach ($query->result() as $row)
				{
					$update_ids[] = $row->draft_id;
				}
			}

			// print_r($update_ids);

            $data = array(
               'status' => 'closed'
            );

            $this->EE->db->where_in('entry_id', $update_ids);
			$this->EE->db->update('channel_titles', $data);

		}


		$resp['msg'] = 'entry_updated';
		$this->EE->output->send_ajax_response($resp);
	}


	function index()
	{
		$vars = array();
		return $this->content_wrapper('index', 'welcome', $vars);
	}


	function content_wrapper($content_view, $lang_key, $vars = array())
	{
		$vars['content_view'] = $content_view;
		$vars['_base'] = $this->base;
		$vars['_form_base'] = $this->form_base;
		$this->EE->cp->set_variable('cp_page_title', lang($lang_key));
		$this->EE->cp->set_breadcrumb($this->base, lang('draftee_module_name'));

		return $this->EE->load->view('_wrapper', $vars, TRUE);
	}
}

/* End of file mcp.draftee.php */
/* Location: ./system/expressionengine/third_party/draftee/mcp.draftee.php */
/* Generated by DevKit for EE - develop addons faster! */
