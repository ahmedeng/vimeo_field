<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vimeo_field_ft extends EE_Fieldtype {

    var $info = array(
        'name'      => 'Vimeo field',
        'version'   => '1.0'
    );

    
    function install()
    {
	// Somewhere in Oregon ...
	return array(
	  
	);
    }
    
    
    function display_settings($data)
    {
      $val=isset($data['player_width']) ? $data['player_width'] : '640';      
      $player_width=(set_value('player_width')!='') ? set_value('player_width') : $val;
            
      ee()->table->add_row(
			'Player width',
			'<h3><font color="red">'.form_error('player_width').'</font></h3>'.form_input(array('id'=>'player_width','name'=>'player_width', 'size'=>4,'value'=>set_value('player_width', $player_width)))
		);
		
      $val=isset($data['player_height']) ? $data['player_height'] : '360';      
      $player_height=(set_value('player_height')!='') ? set_value('player_height') : $val;
      
      ee()->table->add_row(
			'Player height',
			'<h3><font color="red">'.form_error('player_height').'</font></h3>'.form_input(array('id'=>'player_height','name'=>'player_height', 'size'=>4,'value'=>set_value('player_height', $player_height)))
		);
	
      $log_event = ( ! isset($data['log_event'])) ? 'y' :$data['log_event'];
      ee()->table->add_row(
			'Log vimeo player events to auto created channel  "Vimeo player events log"',
			form_checkbox('log_event', 'y', ($log_event=='y'))
		);
	
      $add_jquery = ( ! isset($data['add_jquery'])) ? 'y' :$data['add_jquery'];
      ee()->table->add_row(
			'Auto adding jquery that needed to be added before using this field in your template if you want to log vimeo player events',
			form_checkbox('add_jquery', 'y', ($add_jquery=='y'))
		);
		
      $autoplay = ( ! isset($data['autoplay'])) ? 'n' :$data['autoplay'];
      ee()->table->add_row(
			'Auto play',
			form_checkbox('autoplay', 'n', ($autoplay=='y'))
		);
	
      $loop = ( ! isset($data['loop'])) ? 'n' :$data['loop'];
      ee()->table->add_row(
			'Play the video again automatically when it reaches the end.',
			form_checkbox('loop', 'y', ($loop=='y'))
		);
	
      
    }
    
    
    function validate_settings($data)
    {
      ee()->form_validation->set_rules('player_height', 'player height', 'required|is_natural_no_zero');
      ee()->form_validation->set_rules('player_width', 'player width', 'required|integer');      
    }
    
    
    // --------------------------------------------------------------------
	
    /**
      * Save Settings
      *
      * @access	public
      * @return settings
      *
      */
    function save_settings($data)
    {
      return array(
			'player_width'	=> ee()->input->post('player_width'),
			'player_height'	=> ee()->input->post('player_height'),
			'log_event'			=> (ee()->input->post('log_event') == 'y') ? 'y': 'n',
			'loop'			=> (ee()->input->post('loop') == 'y') ? 'y': 'n',
			'autoplay'			=> (ee()->input->post('autoplay') == 'y') ? 'y': 'n',
			'add_jquery'			=> (ee()->input->post('add_jquery') == 'y') ? 'y': 'n',
		);
    }
    
    
    // --------------------------------------------------------------------

    function display_field($data)
    {
	$field = array(
		    'name'		=> $this->field_name,
		    'value'		=> $data,		    
	    );

	$vimeo_player=$this->_get_vimeo_player($data);
	return form_input($field)."\r\n".$vimeo_player;
    }
    
    // Curl helper function
      function _curl_get($url) {
	  $curl = curl_init($url);
	  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	  $return = curl_exec($curl);
	  curl_close($curl);
	  return $return;
      }

    function _get_vimeo_player($vurl)
    {
      $oembed_endpoint = 'http://vimeo.com/api/oembed';

      // Grab the video url from the url, or use default
      $video_url = $vurl;

      // Create the URLs
      $xml_url = $oembed_endpoint . '.xml?url=' . rawurlencode($video_url).
      '&api='.rawurlencode(1).
      '&player_id='.rawurlencode('vimeo-player_'.$this->content_id).
      '&width='.rawurlencode($this->settings['player_width']).
      '&height='.rawurlencode($this->settings['player_height']);

      $autoplay=$this->settings['autoplay'];
      if($autoplay=='y'){
	echo $xml_url.='&autoplay=true';
      }
      $loop=$this->settings['loop'];
      if($loop=='y'){
	echo $xml_url.='&loop=true';
      }
      
      // Load in the oEmbed XML
      $vimeo_player="";
      $vimeo_xml=$this->_curl_get($xml_url);
      if(strtolower($vimeo_xml)=="404 not found"){
	$vimeo_player="Vimeo video not found";
      }
      else{
	$oembed = simplexml_load_string($vimeo_xml);
	$vimeo_player=html_entity_decode($oembed->html);
	$vimeo_player=str_replace('<iframe','<iframe class="vimeo" id="vimeo-player_'.$this->content_id.'" ',$vimeo_player);
      }
      return $vimeo_player;
      
      
    }
    
    
    function replace_tag($data, $params = array(), $tagdata = FALSE)
    {
      $js="\r\n";
      $log_event=$this->settings['log_event'];
      
      $add_jquery=$this->settings['add_jquery'];
      
      if($log_event=='y'){
	$this->EE->db->select( 'action_id' );
	$this->EE->db->where('class', 'vimeo_field');
	$this->EE->db->where('method', 'ajax_log_player_events');
	$query = $this->EE->db->get( 'exp_actions' );
	$row = $query->row_array();
	$action_id = $row["action_id"];
	
	static $script_on_page = FALSE;
	if ( ! $script_on_page)
	{
	  if($add_jquery=='y')
	    	  $js.='<script type="text/javascript" src="'.URL_THIRD_THEMES.'/vimeo_field/js/jquery.js"></script>';

	  $js.='<script type="text/javascript" src="'.URL_THIRD_THEMES.'/vimeo_field/js/froogaloop.js"></script>';
	  $js.='<script type="text/javascript" src="'.URL_THIRD_THEMES.'/vimeo_field/js/vimeo_player_events.js"></script>';
	  $script_on_page = TRUE;
	}
	$base_url = ee()->functions->fetch_site_index(0, 0).QUERY_MARKER.'ACT='.$action_id;
	  
	$js.="\r\n".'<script type="text/javascript">';
	$js.='
	event_info={"vurl": "'.$data.'","entry_id":"'.$this->content_id.'","channel_id":"'.$this->row['channel_id'].'","field_id":"'.$this->field_id.'"};
	events_info["vimeo-player_'.$this->content_id.'"]=event_info;
	baseUrl = "'.$base_url.'";
	';
	$js.='</script>';
	  //unset($this->EE);print_r($this);exit();	      
      }
      
      $vimeo_player=$this->_get_vimeo_player($data);
      return "\r\n".$vimeo_player.$js;


    }
    
    
}
// END Vimeo_field_ft class

/* End of file ft.vimeo_field.php */
/* Location: ./system/expressionengine/third_party/php_field/ft.vimeo_field.php */