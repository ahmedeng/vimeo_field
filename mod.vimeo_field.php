<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vimeo_field {

	var $return_data	= '';
	

	
	function __construct()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();

	}
	
	public function index()
	{exit();
	  ee()->output->set_output("XXXXXXXXXXXXXXXXXXXX");
	}
	
	public function ajax_log_player_events()
	{
	  $entry_id = ee()->input->get_post('entry_id');
	  $entry_id=urldecode($entry_id);
	  
	  $event = ee()->input->get_post('event');
	  $event=urldecode($event);
	  
	  $vurl = ee()->input->get_post('vurl');
	  $vurl=urldecode($vurl);
	  
	  $channel_name = ee()->input->get_post('channel_id');
	  $channel_name=urldecode($channel_name);
	  $this->EE->db->select( 'channel_name' );
	  $this->EE->db->where( 'channel_id', $channel_name);
	  $query = $this->EE->db->get( 'exp_channels' );
	  $row = $query->row_array();
	  $channel_name=$row["channel_name"];
	  
	  $field_name = ee()->input->get_post('field_id');
	  $field_name=urldecode($field_name);
	  $this->EE->db->select( 'field_name' );
	  $this->EE->db->where( 'field_id', $field_name);
	  $query = $this->EE->db->get( 'exp_channel_fields' );
	  $row = $query->row_array();
	  $field_name=$row["field_name"];
	  
	  $member=ee()->session->userdata('username');
	  
	  $title="Vimeo player event-".$entry_id;
	  
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
	  $fields=$this->_get_fields($group_id);
	  
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
	      
	  ee()->load->library('api');
	  ee()->api->instantiate('channel_entries');
	  ee()->api->instantiate('channel_fields');

	  $data = array(
	      'title'         => $title,
	      $fields['vimeo_entry_id']['name']    => $entry_id,
	      $fields['vimeo_entry_id']['fmt']    => 'none',
	      $fields['vimeo_member']['name']    => $member,
	      $fields['vimeo_member']['fmt']    => 'none',
	      $fields['vimeo_video']['name']    => $vurl,
	      $fields['vimeo_video']['fmt']    => 'none',
	      $fields['vimeo_event']['name']    => $event,
	      $fields['vimeo_event']['fmt']    => 'none',
	      $fields['vimeo_channel_name']['name']    => $channel_name,
	      $fields['vimeo_channel_name']['fmt']    => 'none',
	      $fields['vimeo_field_name']['name']    => $field_name,
	      $fields['vimeo_field_name']['fmt']    => 'none',
	  );
//print_r($data);
	  ee()->api_channel_fields->setup_entry_settings($channel_id, $data);

	  if (ee()->api_channel_entries->submit_new_entry($channel_id, $data) === FALSE)
	  {
	      show_error('An Error Occurred Creating the Entry');
	  }
	  ee()->output->set_output("XXXXXXXXXX");
	  
	}
	
	function _get_fields($group_id)
	{
	    $this->EE->db->select( 'field_name, field_id' );
	    $this->EE->db->where( 'group_id', $group_id);
	    $this->EE->db->order_by( 'field_order' );
	    $query = $this->EE->db->get( 'exp_channel_fields' );
	    
	    $data["unique_fields"] = array();
	    
	    if( $query->num_rows() > 0 ) {
		foreach( $query->result_array() as $row ) {
		  
		    $data["unique_fields"][ $row["field_name"] ]['name'] = 'field_id_'.$row["field_id"];
		    $data["unique_fields"][ $row["field_name"] ]['fmt'] = 'field_ft_'.$row["field_id"];
		}
	    }
	    return $data["unique_fields"];
	}
}

/* End of file mod.vimeo_field.php */
/* Location: ./system/expressionengine/third_party/vimeo_field/mod.vimeo_field.php */ 
