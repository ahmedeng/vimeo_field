<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vimeo_field_upd {

	var $version = '1.0';
	
	function __construct()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
	}
	




	// --------------------------------------------------------------------

	/**
	 * Module Installer
	 *
	 * @access	public
	 * @return	bool
	 */	
	function install()
	{
		$this->EE->load->dbforge();

		$data = array(
			'module_name' => 'vimeo_field' ,
			'module_version' => $this->version,
			'has_cp_backend' => 'n',
			'has_publish_fields' => 'n'
		);

		$this->EE->db->insert('modules', $data);


		$data = array(
		'class'     => 'vimeo_field' ,
		'method'    => 'ajax_log_player_events'
	      );

	      ee()->db->insert('actions', $data);

	      $this->EE->load->model('field_model');
	      $this->EE->field_model->insert_field_group('Vimeo player events log');
	      $groups=$this->EE->field_model->get_field_groups();
	      foreach($groups->result_array() as $row)
	      {
		  if($row['group_name']=='Vimeo player events log')
		  {
		    $group_id=$row['group_id'];
		    break;
		  }
	      }	
	      
	      
	      ee()->load->library('api'); 
	      ee()->api->instantiate('channel_structure');
	      
	      $data = array(
		  'channel_title' => 'Vimeo player events log',
		  'channel_name'  => 'vimeo_player_events_log',
		  'field_group' => $group_id,
		  'channel_url' => ee()->functions->fetch_site_index().'vimeo_player_events_log',
		  'status_group'  => 1
	      );

	      if (ee()->api_channel_structure->create_channel($data) === FALSE)
	      {
		  show_error('An Error Occurred Creating the Channel');
	      }
	      ee()->load->library('api'); 
	      ee()->api->instantiate('channel_fields');
	      $field_data = array(
		  'group_id' => $group_id,
		  'field_name' => 'vimeo_entry_id',
		  'field_label' => 'Entry id',
		  'field_type' => 'text',
		  'field_order' => 10,
		  'site_id' => 1,
		  'field_required' => 'y',
		  'field_search' => 'y',
		  'field_is_hidden' => 'n',
		  'field_instructions' => '',
		  'field_maxl' => 128,
		  'text_field_fmt' => 'none',
		  'text_field_show_fmt' => 'n',
		  'text_field_text_direction' => 'ltr',
		  'text_field_content_type' => 'all',
		  'text_field_show_smileys' => 'n',
		  'text_field_show_glossary' => 'n',
		  'text_field_show_spellcheck' => 'n',
		  'text_field_show_file_selector' => 'n',
	      );
	      ee()->api_channel_fields->update_field($field_data);
	      
	      $field_data['field_name']='vimeo_member';
	      $field_data['field_label']='Member';
	      ee()->api_channel_fields->update_field($field_data);
	      
	      $field_data['field_name']='vimeo_channel_name';
	      $field_data['field_label']='channel';
	      ee()->api_channel_fields->update_field($field_data);
	      
	      $field_data['field_name']='vimeo_field_name';
	      $field_data['field_label']='field';
	      ee()->api_channel_fields->update_field($field_data);
	      
	      $field_data['field_name']='vimeo_video';
	      $field_data['field_label']='Vimeo video';
	      ee()->api_channel_fields->update_field($field_data);
	      
	      $field_data['field_name']='vimeo_event';
	      $field_data['field_label']='Event';
	      ee()->api_channel_fields->update_field($field_data);
	      
	  
		return TRUE;
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * Module Uninstaller
	 *
	 * @access	public
	 * @return	bool
	 */
	function uninstall()
	{		
		
		$this->EE->db->where('module_name', 'vimeo_field'); 
		$this->EE->db->delete('modules');

		$this->EE->db->where('class', 'vimeo_field');
		$this->EE->db->delete('actions');

		$this->EE->load->model('field_model');
		$groups=$this->EE->field_model->get_field_groups();
		foreach($groups->result_array() as $row)
		{
		    if($row['group_name']=='Vimeo player events log')
		    {
		      $group_id=$row['group_id'];
		      break;
		    }
		}
		$this->EE->field_model->delete_field_groups($group_id);
		
		ee()->load->library('api'); 
		ee()->api->instantiate('channel_structure');
		$channels=ee()->api_channel_structure->get_channels();
		foreach($channels->result_array() as $row)
		{
		    if($row['channel_name']=='vimeo_player_events_log')
		    {
		      $channel_id=$row['channel_id'];
		      break;
		    }
		}
		ee()->api_channel_structure->delete_channel((int) $channel_id);
		
		return TRUE;
	}



	// --------------------------------------------------------------------

	/**
	 * Module Updater
	 *
	 * @access	public
	 * @return	bool
	 */	
	
	function update($current='')
	{
		return TRUE;
	}
	
}
/* END Class */

/* End of file upd.download.php */
/* Location: ./system/expressionengine/third_party/modules/vimeo_field/upd.vimeo_field.php */