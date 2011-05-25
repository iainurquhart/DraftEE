<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  Draftee Fieldtype for ExpressionEngine 2
 *
 * @package		ExpressionEngine
 * @subpackage	Fieldtypes
 * @category	Fieldtypes
 * @author    	Iain Urquhart <shout@iain.co.nz>
 * @copyright 	Copyright (c) 2010 Iain Urquhart
 * @license   	http://creativecommons.org/licenses/MIT/  MIT License
*/

	class Draftee_ft extends EE_Fieldtype
	{
		var $info = array(
			'name'		=> 'Draftee',
			'version'	=> '0.001'
		);

		public function Draftee_ft()
		{
			parent::EE_Fieldtype();
			// include_once PATH_THIRD.'taxonomy/libraries/MPTtree.php';
			$this->EE->lang->loadfile('draftee');
			$this->theme_base = $this->EE->config->item('theme_folder_url').'third_party/draftee_assets/';
			$this->ajax_base = str_replace("&amp;", "&", BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=draftee'.AMP.'method=');
			
			//C=content_publish&M=entry_form&channel_id=1
			$this->publish_base = BASE.AMP.'C=content_publish'.AMP.'M=entry_form'.AMP;
		}	

		public function display_field($data)
		{
			
			$vars = array();
			
			$vars['entry_id'] 	= $this->EE->input->get('entry_id');
			$vars['channel_id'] = $this->EE->input->get('channel_id');
			$vars['field_id'] = $this->field_id;
			$vars['theme_base'] = $this->theme_base;
            $vars['group_id'] = $this->EE->session->userdata('group_id');
			
			// no entry id
			// we just hide the whole field for now.
			// @todo
			if(!$vars['entry_id'] || isset($this->EE->session->cache[$this->settings['field_id']]['displayed']))
				return $this->EE->load->view('field_is_new', $vars, TRUE);
			
			// set this just incase some fool adds several of these fields
			$this->EE->session->cache[$this->settings['field_id']]['displayed'] = TRUE;
			
			$this->EE->load->model('channel_entries_model');

			// set some vars
			
			$vars['ajax_base'] = $this->ajax_base;
			$vars['publish_base'] = $this->publish_base;
			$vars['parent_id'] = '';
			$vars['date_mismatch'] = '';
			$date_fmt = ($this->EE->session->userdata('time_format') != '') ? $this->EE->session->userdata('time_format') : $this->EE->config->item('time_format');
			if ($date_fmt == 'us')
			{
				$vars['date_format'] = '%m/%d/%y %h:%i %a';
			}
			else
			{
				$vars['date_format'] = '%Y-%m-%d %H:%i';
			}
			
			// is this entry a draft of another entry...
			$query = $this->EE->db->get_where('draftee_drafts', array('draft_id' => $vars['entry_id']));
			if ($query->num_rows() > 0)
			{
				
				foreach ($query->result() as $row)
				{
					$vars['parent_id'] = $row->parent_id;
				}
				
				$parent_query = $this->EE->channel_entries_model->get_entry($vars['parent_id'], $vars['channel_id']);
				$vars['parent_data'] = $parent_query->row_array();
				
				$parent_query = $this->EE->channel_entries_model->get_entry($vars['entry_id'], $vars['channel_id']);
				$vars['this_data'] = $parent_query->row_array();
				
				// has the parent entry been edited since the draft was created?
				if((isset($vars['parent_data']['edit_date']) && isset($vars['this_data']['edit_date'])) && $vars['parent_data']['edit_date'] > $vars['this_data']['edit_date'])
				{
					$vars['date_mismatch'] = TRUE;
				}
				
				if(count($vars['parent_data']) > 0)
				return $this->EE->load->view('field_is_child', $vars, TRUE);
			}
			
			// so it's not a draft then
			// Do we have any drafts for this entry? 
			
			$vars['drafts'] = array();
			$query = $this->EE->db->get_where('draftee_drafts', array('parent_id' => $vars['entry_id']));
			if ($query->num_rows() > 0)
			{
				foreach ($query->result() as $draft)
				{
					$vars['drafts'][] = $this->_get_draft_entry($draft->draft_id);
				}
			}
			return $this->EE->load->view('field', $vars, TRUE);

		}
		
		
		
		
		
		// returns an array with all data from
		// exp_channel_data, exp_channel_titles and member data
		// from an entry_id
		private function _get_draft_entry($entry_id)
		{
			if(!$entry_id)
				return false;
			
			$this->EE->db->select('*');
			$this->EE->db->from('channel_titles');
			$this->EE->db->where('channel_titles.entry_id', $entry_id);
			$this->EE->db->where('channel_titles.status', 'Draft');
			$this->EE->db->join('channel_data', 'channel_titles.entry_id = channel_data.entry_id');
			$this->EE->db->join('members', 'channel_titles.author_id = members.member_id');
			
			$results = $this->EE->db->get();
			
			return $results->row_array();

		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		public function replace_tag($data, $params = FALSE, $tagdata = FALSE)
		{

		}
		
		public function save($data)
		{
				$this->cache['data'][$this->settings['field_id']] = $data;
		}
		
		function post_save($data)
		{

			$data = $this->cache['data'][$this->settings['field_id']];
			
			if(!$data)
			{
				return NULL;
			}

		}
		
		public function validate($data)
		{
			return TRUE;
		}
		
		public function save_settings($data)
		{
			return array(
				'tree_id'				=> $this->EE->input->post('tree_id')
				);
		}

		public function display_settings($data)
		{
			
			if(!isset($data['tree_id']))
			{
				$data['tree_id'] = '';
			}
			
			$tree_id = array(
              'name'        => 'tree_id',
              'id'          => 'tree_id',
              'value'       => $data['tree_id'],
              'maxlength'   => '100',
              'size'        => '50',
              'style'       => 'width:50%',
            );
			
 			$this->EE->table->add_row(
 				$this->EE->lang->line('select_tree'),
				form_input($tree_id)
 			);
 			
 		}			

		function install()
		{
			//nothing
		}

		function unsinstall()
		{
			//nothing
		}

		
	}
	//END CLASS
	
/* End of file ft.taxonomy.php */
