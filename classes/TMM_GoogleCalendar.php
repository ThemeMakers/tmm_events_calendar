<?php

/* 
 * Google Calendar class
 * uses Google Calendar API
 */

class TMM_GoogleCalendar{
    
    private static $api_key;


    public static function init(){
		$api_key = 'AIzaSyBm67QlIFxjuE34SG9Yy45mQizI4zVieUc';
		self::$api_key = $api_key;
    }
	
	public static function getEventsList(){
		$header = array(
			'X-JavaScript-User-Agent:  Google APIs Explorer',
		);
		$url = 'https://www.googleapis.com/calendar/v3/calendars/st.prog.1986@gmail.com/events?key='.self::$api_key;
        $result = self::doCurlRequest(false, $url, $header);
		$result = json_decode($result);
		return $result->items;
	}
	
    private static function doCurlRequest($is_post = true, $url, $header, $fields = array()){
		$fields_string = http_build_query($fields);
        
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, $is_post);
		if($is_post){
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        if(!empty($header)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
		curl_setopt($ch, CURLOPT_CAINFO, TMM_EVENTS_PLUGIN_PATH . '/cacert.pem');
       
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}