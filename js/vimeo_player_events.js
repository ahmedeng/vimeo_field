events_info={};
jQuery(document).ready(function() {
	
    // Enable the API on each Vimeo video
    jQuery("iframe.vimeo").each(function(){
	Froogaloop(this).addEvent("ready", ready);
    });
    
    function ready(playerID){
	// Add event listerns
	// http://vimeo.com/api/docs/player-js#events
	Froogaloop(playerID).addEvent("play", onPlay);
	Froogaloop(playerID).addEvent("pause", onPause);
	Froogaloop(playerID).addEvent("finish", onFinish);
	Froogaloop(playerID).addEvent("seek", seek);
	
	// Fire an API method
	// http://vimeo.com/api/docs/player-js#reference
	//Froogaloop(playerID).api("play");
    }
    function onPlay(playerID){
	$.get(baseUrl+"&vurl="+events_info[playerID]["vurl"]+"&event=play&entry_id="+events_info[playerID]["entry_id"]
	  +"&channel_id="+events_info[playerID]["channel_id"]+"&field_id="+events_info[playerID]["field_id"]);
    }
    function onPause(playerID){
	$.get(baseUrl+"&vurl="+events_info[playerID]["vurl"]+"&event=pause&entry_id="+events_info[playerID]["entry_id"]
	  +"&channel_id="+events_info[playerID]["channel_id"]+"&field_id="+events_info[playerID]["field_id"]);
    }
    function onFinish(playerID){
	$.get(baseUrl+"&vurl="+events_info[playerID]["vurl"]+"&event=finish&entry_id="+events_info[playerID]["entry_id"]
	  +"&channel_id="+events_info[playerID]["channel_id"]+"&field_id="+events_info[playerID]["field_id"]);
    }
    function seek() {
	$.get(baseUrl+"&vurl="+events_info[playerID]["vurl"]+"&event=seek&entry_id="+events_info[playerID]["entry_id"]
	  +"&channel_id="+events_info[playerID]["channel_id"]+"&field_id="+events_info[playerID]["field_id"]);
    }

});