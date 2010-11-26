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
			
			// no entry id
			// we just hide the whole field for now.
			// @todo
			if(!$vars['entry_id'] || isset($this->EE->session->cache[$this->settings['field_id']]['displayed']))
				return '<style type="text/css">div#hold_field_'.$this->field_id.' {display: none;}</style>';
			
			// set this just incase some fool adds several of these fields
			$this->EE->session->cache[$this->settings['field_id']]['displayed'] = TRUE;
			
			
			// set some vars
			$vars['theme_base'] = $this->theme_base;
			$vars['ajax_base'] = $this->ajax_base.'create_draft';
			$vars['publish_base'] = $this->publish_base;
			$vars['parent_id'] = '';
			
			// will hold an array of drafts for the current entry
			// @todo
			$vars['drafts'] = array();
			
			// is this entry a draft of another entry...
			$query = $this->EE->db->get_where('draftee_drafts', array('draft_id' => $vars['entry_id']));
			if ($query->num_rows() > 0)
			{
				foreach ($query->result() as $row)
				{
					$vars['parent_id'] = $row->parent_id;
				}
				
				return $this->EE->load->view('field_is_child', $vars, TRUE);
				
			}

			
			
			return $this->EE->load->view('field', $vars, TRUE);

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