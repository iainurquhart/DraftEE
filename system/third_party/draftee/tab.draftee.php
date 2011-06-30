<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Draftee_tab {

	function __construct()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
	}
	function publish_tabs($channel_id, $entry_id = '')
	{
		$this->EE->lang->loadfile('draftee');
		$settings = array();
		$selected = array();
		$existing_files = array();

		$query = $this->EE->db->get('draftee_drafts');
		$settings[] = array(
				'field_id'		=> 'draftee_field_ids',
				'field_label'		=> 'Drafts',
				'field_required'	=> 'n',
				'field_data'		=> 'Field Data',				
				'field_fmt'		=> '',
				'field_maxl'		=> 10,
				'field_instructions' 	=> 'Field Instructions',
				'field_show_fmt'	=> 'n',
				'field_pre_populate'	=> 'n',
				'field_text_direction'	=> 'ltr',
				'field_type' 		=> 'text'
			);
		
		return ($settings);
	}

	function validate_publish($params)
	{
		return FALSE;
	}

	function publish_data_db($params)
	{
		// Remove existing
//		$this->EE->db->where('entry_id', $params['entry_id']);

	}

	function publish_data_delete_db($params)
	{
		// Remove existing
		$this->EE->db->where_in('entry_id', $params['entry_ids']);
		$this->EE->db->delete('draftee_drafts'); 
	}

}

?>
