<?php

class TMM_Event {

	public static $event_repeatings = array();
	public static $gmt_offset = "";

	public static function init() {
		self::$gmt_offset = get_option('gmt_offset');

		self::$event_repeatings = array(
			'no_repeat' => __('No repeating', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			'week' => __('Week', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			'2week' => __('2 weeks', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			'3week' => __('3 weeks', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			'month' => __('Month', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			'year' => __('Year', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
		);
	}
	
	public static function save_post($data = array()) {
		if ( (!empty($data) && isset($data['post_id'])) || (!empty($_POST) && isset($_POST['thememakers_meta_saving'])) ) {
			
			if(!empty($data) && isset($data['post_id'])){
				$post_id = $data['post_id'];
			}else{
				global $post;
				$post_id = $post->ID;
			}
			
			$post_type = get_post_type($post_id);
			if ($post_type == 'event') {
								
				$fields = array(
					'event_date' => '',
					'event_end_date' => '',
					'event_hh' => 0,
					'event_mm' => 0,
					'event_end_hh' => 0,
					'event_end_mm' => 0,
					'event_repeating' => '',
					'event_repeating_week' => '',
					'hide_event_place' => 1,
					'event_allday' => 0,
					'event_place_address' => '',
					'event_map_zoom' => '',
					'event_map_latitude' => '',
					'event_map_longitude' => '',
					'google_calendar_event_id' => false,
				);
				
				foreach($fields as $key => &$value){
					$temp = $value;
					if(isset($_POST[$key])){
						$temp = $_POST[$key];
					}else if(isset($data[$key]) && !empty($data[$key])){
						$temp = $data[$key];
					}
					$value = is_numeric($value) ? (int) $temp : $temp;
					update_post_meta($post_id, $key, $value);
				}

				if(!empty($fields['event_date']) || !empty($fields['event_end_date'])){
					if(!empty($fields['event_date'])){
						$date = explode("-", $fields['event_date']);
						$event_mktime = strtotime($date[0] . '-' . $date[1] . '-' . $date[2] . " " . $fields['event_hh'] . ":" . $fields['event_mm']);
						update_post_meta($post_id, "ev_mktime", $event_mktime);
					}
					if(!empty($fields['event_end_date'])){
						$date_end = explode("-", $fields['event_end_date']);
						$event_end_mktime = strtotime($date_end[0] . '-' . $date_end[1] . '-' . $date_end[2] . " " . $fields['event_end_hh'] . ":" . $fields['event_end_mm']);
						update_post_meta($post_id, "ev_end_mktime", $event_end_mktime);
					}
				}
			}
		}
	}

	public static function show_edit_columns_content($column) {
		global $post;
        
        $ev_mktime = (int) get_post_meta($post->ID, 'ev_mktime', true);
        $ev_end_mktime = (int) get_post_meta($post->ID, 'ev_end_mktime', true);
        $event_duration_sec = TMM_Event::get_event_duration($ev_mktime, $ev_end_mktime);

		switch ($column) {
			case "place":
				echo "<h3>" . get_post_meta($post->ID, 'event_place_address', true) . "</h3>";
				$lat = get_post_meta($post->ID, 'event_map_latitude', true);
				$lng = get_post_meta($post->ID, 'event_map_longitude', true);
				echo '<img src="http://maps.googleapis.com/maps/api/staticmap?center=' . $lat . ',' . $lng . '&zoom=' . get_post_meta($post->ID, 'event_map_zoom', true) . '&size=350x250&markers=color:red|label:P|' . $lat . ',' . $lng . '&sensor=false">';
				break;
			case "description":
				the_excerpt();
				break;
			case "ev_mktime":
				$repeats_every = get_post_meta($post->ID, 'event_repeating', true);
				$ev_days = self::get_event_days($post->ID);
                $ev_date = self::get_event_date($ev_mktime);
                $ev_end_date = self::get_event_date($ev_end_mktime);
                if ($ev_date != $ev_end_date || $repeats_every != "no_repeat"){
                    $ev_date .= ' - ' . $ev_end_date;
                }
				$event_start_time = self::get_event_time($ev_mktime, true);
				$event_end_time = self::get_event_time($ev_end_mktime, true);
                ?>

                <div><strong><?php echo $ev_days; ?></strong></div>
                <div>
                    <strong>
                        <?php echo $event_start_time . ' - ' . $event_end_time; ?>
                        <span class="zones"><?php echo self::get_timezone_string(); ?></span>
                    </strong>
                </div>
                
                <div>(<?php echo $ev_date; ?>)</div>
                
                <?php 
				break;
			case "event_repeating":
				$current_event_repeating = get_post_meta($post->ID, 'event_repeating', true);
				if(!empty($current_event_repeating)){
					echo self::$event_repeatings[$current_event_repeating];
				}
				break;
			case "ev_duration":
                $hh = $event_duration_sec[0];
				$mm = $event_duration_sec[1];
				echo '<i>' . $hh . ":" . $mm . '</i>';
				break;
			case "ev_cat":
				echo get_the_term_list($post->ID, 'events-categories', '', ', ', '');
				break;
		}
	}

	public static function show_edit_columns($columns) {
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => __("Title", TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			"place" => __("Place", TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			"description" => __("Description", TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			"ev_mktime" => __("Date", TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			"ev_duration" => __("Duration", TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			"ev_cat" => __("Category", TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			"event_repeating" => __("Repeating", TMM_EVENTS_PLUGIN_TEXTDOMAIN),
		);

		return $columns;
	}

	public static function event_sortable_columns($columns) {
		$columns['ev_mktime'] = 'ev_mktime';
		return $columns;
	}

	public static function event_column_orderby($query) {
		if (!is_admin())
			return;

		$orderby = $query->get('orderby');

		if ('ev_mktime' == $orderby) {
			$query->set('meta_key', 'ev_mktime');
			$query->set('orderby', 'ev_mktime');
		}

		return $query;
	}

	public static function get_events($start, $end, $category = 0) {
		global $wpdb;
		$start = (int) $start;
		$end = (int) $end;
		$category = (int) $category;

		$current_year = (int) date('Y', $start);
		$current_month = (int) date('m', $start) + 1;
		
		$data = array();
		//$google_events = self::sync_Google_calendar_events();//for update
						
		$result = $wpdb->get_results("
			SELECT SQL_CALC_FOUND_ROWS  p.ID , p.post_title, p.post_excerpt
			FROM {$wpdb->posts} p ".
			($category > 0 ? "INNER JOIN {$wpdb->term_relationships} tr ON (p.ID = tr.object_id) " : "")			 
			."INNER JOIN {$wpdb->postmeta}  pm
				ON (p.ID = pm.post_id) 
			INNER JOIN {$wpdb->postmeta} AS mt1 
				ON (p.ID = mt1.post_id) 
			WHERE ".
				($category > 0 ? "tr.term_taxonomy_id IN ( {$category} ) " : "1=1 ")
				." 
				AND p.post_type = 'event'
				AND ((p.post_status = 'publish'))
				AND (
					(pm.meta_key = 'event_repeating' AND CAST(pm.meta_value AS CHAR) != 'no_repeat')
					OR
					(mt1.meta_key = 'ev_end_mktime' AND CAST(mt1.meta_value AS CHAR) > '{$start}')
				) 
			GROUP BY p.ID 
			ORDER BY p.post_date DESC
		", OBJECT_K);

		if (!empty($result)) {
			foreach ($result as $post) {
				$events_data = array();
				$post_meta = get_post_meta($post->ID);
				$start_date = (int) $post_meta['ev_mktime'][0];
				$end_date = (int) $post_meta['ev_end_mktime'][0];
				$place_address = $post_meta['event_place_address'][0];
				$repeating = $post_meta['event_repeating'][0];
				$featured_image_src = '';
				$duration_sec = TMM_Event::get_event_duration($start_date, $end_date);
				$duration_sec = $duration_sec[2];
				
                if($end && $start_date > $end){
					continue;
				}

				$featured_image_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail');

				if ($featured_image_src) {
					$featured_image_src = $featured_image_src[0];
				} else {
					$featured_image_src = '';
				}

				if ($repeating !== 'no_repeat') {
					$event_year = (int) date('Y', $start_date);
					$event_month = (int) date('m', $start_date);
							
					switch ($repeating) {
						case 'week':
						case '2week':
						case '3week':
							//if ($current_year > $event_year || ($current_year == $event_year && $current_month >= $event_month-1) ) {
								$repeating_week = unserialize($post_meta['event_repeating_week'][0]);
								$start_day_number = (int) date('N', $start_date);/* mon-1, .., sun-7 */
								$day_duration_sec = 60 * 60 * 24;
								$diff = 7 - $start_day_number;
								$tmp_start = $start;
																
								if(is_array($repeating_week) && count($repeating_week)){
									
									foreach ($repeating_week as $key => $value) {
										$value = $value + 1;/* mon-1, .., sun-7 */
										$day_distance = $diff + $value;
										$day_distance = ($day_distance >= 7) ? $day_distance - 7 : $day_distance;
										$tmp_start = $start_date + $day_duration_sec*$day_distance;
										$temp_date = ($end_date > $end) ? $end : $end_date;
										$i = 1;
										$k = 2;
										$j = 3;
										while($tmp_start < $temp_date){
											$skip = false;
											if($repeating === '2week' && $i%2 == 0){
												$skip = true;
											}
											if($repeating === '3week'){
												if($i == $k){
													$k += 3;
													$skip = true;
												}
												if($i == $j){
													$j += 3;
													$skip = true;
												}
											}
											if(!$skip){
												$events_data[] = array(
													'start' => $tmp_start,
													'end' => $tmp_start + $duration_sec,
												);
											}
											$tmp_start += $day_duration_sec*7;
											$i++;
										}
									}
								}
							//}
							break;
						case 'month':
							//if ($current_year > $event_year || ($current_year == $event_year && $current_month >= $event_month-1) ) {
								if($current_month > $event_month){
									$start_date = mktime((int) date('H', $start_date), (int) date('i', $start_date), 0, $current_month-1, (int) date('j', $start_date), $current_year, -1);
									if($start_date <= $end_date){
										$events_data[] = array(
											'start' => $start_date,
											'end' => $start_date + $duration_sec,
										);
									}
								}
								if($current_month >= $event_month){
									$start_date = mktime((int) date('H', $start_date), (int) date('i', $start_date), 0, $current_month, (int) date('j', $start_date), $current_year, -1);
									if($start_date <= $end_date){
										$events_data[] = array(
											'start' => $start_date,
											'end' => $start_date + $duration_sec,
										);
									}
								}
								if($current_month >= $event_month-1){
									$start_date = mktime((int) date('H', $start_date), (int) date('i', $start_date), 0, $current_month+1, (int) date('j', $start_date), $current_year, -1);
									if($start_date <= $end_date){
										$events_data[] = array(
											'start' => $start_date,
											'end' => $start_date + $duration_sec,
										);
									}
								}
							//}
							break;
						case 'year':
							//if ($current_year >= $event_year && $current_month == $event_month) {
								$start_date = mktime((int) date('H', $start_date), (int) date('i', $start_date), 0, (int) date('n', $start_date), (int) date('j', $start_date), $current_year, -1);
								$events_data[] = array(
									'start' => $start_date,
									'end' => $start_date + $duration_sec,
								);
							//}
							break;
						default:
							break;
					}
				}else{
					$events_data[] = array(
						'start' => $start_date,
						'end' => $end_date,
					);
				}
				
				foreach($events_data as $key => $value){
					
					if($value['end'] < $start){
						continue;
					}
					
					$data[] = array(
						'id' => uniqid(),
						'post_id' => $post->ID,
						'title' => $post->post_title,
						'start' => date("Y-m-d H:i", $value['start']),
						'end' => date("Y-m-d H:i", $value['end']),
						'start_mktime' => $value['start'],
						'end_mktime' => $value['end'],
						'event_place_address' => $place_address,
						'featured_image_src' => $featured_image_src,
						'post_excerpt' => $post->post_excerpt,
						'allDay' => 0,
						'url' => get_permalink($post->ID),
					);
				}
			}
		}
		return $data;
	}
	
	public static function sync_Google_calendar_events() {
		$data = array();
		$google_events = TMM_GoogleCalendar::getEventsList();
		$custom_events = get_posts(array(
			'numberposts'     => -1,
			'meta_key' => 'google_calendar_event_id',
			'meta_value' => '',
			'meta_compare' => '!=',
			'post_type'       => 'event',
			'post_status'     => 'publish'
		));
		
		//return $custom_events;
		
		if(!empty($google_events)){
			
			foreach($google_events as $key => $value){
				$fields = array();
				$new_post_id = false;
				
				$new_post = array(
					'post_title' => isset($value->summary) ? $value->summary : '',
					'post_name'  => isset($value->summary) ? sanitize_title($value->summary) : '',
					//'post_content' => isset($value->description) ? $value->description : '',
					'post_excerpt' => isset($value->description) ? $value->description : '',
					'post_status'  => 'publish',
					'post_type'    => 'event',
				);

				$new_post_id = wp_insert_post( $new_post );
				
				if($new_post_id){
					$start = '';
					$end = '';
					$start_hh = 0;
					$start_mm = 0;
					$end_hh = 0;
					$end_mm = 0;
					//$duration_sec = '';
					if(isset($value->start->date)){
						$start = $value->start->date;
					}else if(isset($value->start->dateTime)){
						$temp_time = strtotime($value->start->dateTime);
						$start = date('Y-m-d', $temp_time);
						$start_hh = date('h', $temp_time);
						$start_mm = date('i', $temp_time);
					}
					if(isset($value->end->date)){
						$end = $value->end->date;
					}else if(isset($value->end->dateTime)){
						$temp_time = strtotime($value->end->dateTime);
						$end = date('Y-m-d', $temp_time);
						$end_hh = date('h', $temp_time);
						$end_mm = date('i', $temp_time);
					}
					$start_time = strtotime($start);
					$end_time = strtotime($end);
					
					$fields['post_id'] = $new_post_id;
					$fields['event_date'] = $start;
					$fields['event_end_date'] = $end;
					$fields['event_hh'] = $start_hh;
					$fields['event_mm'] = $start_mm;
					$fields['event_end_hh'] = $end_hh;
					$fields['event_end_mm'] = $end_mm;
					$fields['event_place_address'] = isset($value->location) ? $value->location : '';
					$fields['google_calendar_event_id'] = $value->id;
					self::save_post($fields);
					$data[] = $fields;
				}
			}
		}
		return $data;
	}
	
	//ajax
	public static function get_calendar_data() {
		$data = self::get_events($_REQUEST['start'], $_REQUEST['end']);
		echo json_encode($data);
		exit;
	}

	//ajax
	public static function get_widget_calendar_data() {
		$data = self::get_events($_REQUEST['start'], $_REQUEST['end']);
		$now = current_time('timestamp');

		$buffer = array();
		$result = array();

		if (!empty($data)) {
			foreach ($data as $value) {
				$start_day = (int) date('z', $value['start_mktime']);
				$end_day = (int) date('z', $value['end_mktime']);
				$ic = $end_day - $start_day + 1;
				for($i=0;$i<$ic;$i++){
					$temp_date = $value['start_mktime'] + 86400*$i;
					$temp_date = date('Y-m-d', $temp_date);
					$buffer[$temp_date] = isset($buffer[$temp_date]) ? $buffer[$temp_date] + 1 : 1;
				}
			}
			
			foreach ($buffer as $date => $count) {
				$tmp = array();
				$tmp['id'] = (string) uniqid();
				$tmp['title'] = (string) $count;
				$tmp['start'] = $date;
				$tmp['start_mktime'] = strtotime($date, $now);
				$tmp['end'] = $date;
				$tmp['allDay'] = 0;

				$date_array = explode("-", $date);
				$tmp['url'] = home_url() . "/event?yy=" . $date_array[0] . "&mm=" . $date_array[1] . "&dd=" . $date_array[2];

				$result[] = $tmp;
			}
		}

		echo json_encode($result);
		exit;
	}

	public static function get_soonest_event($start, $count = 1, $distance = 0, $category = 0, $delay = 0) {

		if (!$distance) {
			$distance = 1 * 60 * 60 * 24 * 31; //1 month by default
		} else {
			$distance = $distance * 60 * 60 * 24 * 31;
		}

		$end = $start + $distance;
		$now = current_time('timestamp') - $delay * 3600;

		$data = self::get_events($start, $end, $category);

		$buffer = array();

		if (!empty($data)) {

			foreach ($data as $key => $value) {
				if ($value['start_mktime'] > $now) {
					if ($distance > 0) {
						if ($value['start_mktime'] > $start + $distance) {
							continue;
						}
					}
					$buffer[$value['start_mktime']] = $value;
				}
			}
		}


		$key_buffer = array();
		if (!empty($buffer)) {
			foreach ($buffer as $key => $value) {
				$key_buffer[] = $key;
			}
		}


		if (!empty($key_buffer)) {
			sort($key_buffer, SORT_NUMERIC);
			$result = array();
			for ($i = 0; $i < $count; $i++) {
				if (isset($key_buffer[$i])) {
					$result[] = $buffer[$key_buffer[$i]];
				} else {
					break;
				}
			}

			return $result;
		}



		return array();
	}

	private static function compare_events($a, $b) {
		return $a['start_mktime'] - $b['start_mktime'];
	}

	public static function get_event_days($post_id) {
		$is_repeat = get_post_meta($post_id, 'event_repeating', true);
		$days = array();
		$result = '';
		$ev_mktime = (int) get_post_meta($post_id, 'ev_mktime', true);
		
		if($is_repeat !== 'no_repeat'){
			$repeating_days = get_post_meta($post_id, 'event_repeating_week', true);
            if(is_array($repeating_days)){
                foreach ($repeating_days as $v) {
                    $days[] = TMM_Helper::get_days_of_week($v);
                }
            }
		}else{
			$days[] = TMM_Helper::get_days_of_week(date('N', $ev_mktime)-1);
		}
		
		for($i=0,$ic=count($days);$i<$ic;$i++) {
			if($i > 0){
				$result .= ', ';
			}
			$result .= $days[$i];
		}

		return $result;
	}
    
	//ajax
	public static function get_events_listing() {
		$request_start = 0;
		$request_end = 0;
		$category = 0;
		$is_ajax = 0;
		
		if (isset($_REQUEST['is_ajax'])) {
			$is_ajax = (int) $_REQUEST['is_ajax'];
		}
		if (isset($_REQUEST['start'])) {
			$request_start = (int) $_REQUEST['start'];
		}
		if (isset($_REQUEST['end'])) {
			$request_end = (int) $_REQUEST['end'];
		}
		if (isset($_REQUEST['category'])) {
			$category = (int) $_REQUEST['category'];
		}

		$now = current_time('timestamp');

		$start = ($request_start != 0 ? $request_start : $now);
		$days_in_curr_month = date('t', @mktime(0, 0, 0, date("m", $start), 1, date("Y", $start)));
		
		$distance = 60 * 60 * 24 * $days_in_curr_month - 1;
		if($request_end == 0){
			$end = $start + $distance;
		}else{
			$end = $request_end;
		}
		if ($request_start == 0) {//current month
			$distance = $end - $start;
		}
		
		$events = self::get_events($start, $end, $category);

		//events filtering
		$filtered_events = array();
		if (!empty($events)) {
			foreach ($events as $key => $value) {
				if ($value['end_mktime'] < $start) {
					unset($events[$key]);
					continue;
				}
				if ($_REQUEST['end'] > 0 && $value['start_mktime'] > $_REQUEST['end']) {
					unset($events[$key]);
					continue;
				}

				$filtered_events[] = $value;

			}
		}

		usort($filtered_events, function($a, $b){
			return ($a['start_mktime'] < $b['start_mktime']) ? -1 : 1;
		});

		$events = $filtered_events;

		$args = array();
		$args['events'] = $events;
		$result = array();
		$result['html'] = TMM::draw_free_page(TMM_EVENTS_PLUGIN_PATH . '/views/templates/events_list_part.php', $args);
		$result['count'] = count($events);

		$result['year'] = date("Y", $start);
		$result['month'] = TMM_Helper::get_monts_names(date("m", $start) - 1);
		$result['month_num'] = date("m", $start);

		$result['next_time'] = $end + 1;
		$result['prev_time'] = $end - $distance - 1;

		if ($result['prev_time'] < $now) {
			$result['prev_time'] = $now;
		}

		$result['prev_time'] = strtotime(date("Y", $result['prev_time']) . '-' . date("m", $result['prev_time']) . '-' . 1 . " " . 00 . ":" . 00 . ":" . 00, $now);

		if ($is_ajax) {
			echo json_encode($result);
			exit;
		}else{
			return $result['html'];
		}
	}

	public static function get_timezone_string() {
		$hide_time_zone = TMM::get_option("events_hide_time_zone");

		if ($hide_time_zone == 1) {
			return "";
		}

		$current_offset = self::$gmt_offset;
		$tzstring = get_option('timezone_string');

		if (false !== strpos($tzstring, 'Etc/GMT'))
			$tzstring = '';

		if (empty($tzstring)) { // Create a UTC+- zone if no timezone string exists
			if (0 == $current_offset)
				$tzstring = 'UTC+0';
			elseif ($current_offset < 0)
				$tzstring = 'UTC' . $current_offset;
			else
				$tzstring = 'UTC+' . $current_offset;
		}

		return "(" . $tzstring . ")";
	}

	//format: 2013-03-01 17:11  YYYY-mm-dd H:i
	public static function convert_time_to_zone_time($time) {
		$gmt_offset = (int) self::$gmt_offset;
		$mk_time = strtotime($time);
		$mk_time+=($gmt_offset * (-1) * 3600);
		$time_converted = date('Y-m-d', $mk_time) . " " . date('H', $mk_time) . ":" . date('i', $mk_time);
		return $time_converted;
	}
    
    public static function get_event_duration($start, $end) {
		$duration = array('00', '00', 0);
        if($end > $start){
            $start_h = date('H', $start);
            $start_m = date('i', $start);
            $end_h = date('H', $end);
            $end_m = date('i', $end);
            $diff = mktime($end_h, $end_m) - mktime($start_h, $start_m);
            $duration[0] = $diff >= 3600 ? (int) ($diff / 3600) : 0;
            $duration[1] = (int) (($diff % 3600) / 60);
			if($duration[0] < 10){
				$duration[0] = '0' . $duration[0];
			}
			if($duration[1] < 10){
				$duration[1] = '0' . $duration[1];
			}
			$duration[2] = $diff;
        }
        
		return $duration;
	}
	
	public static function get_event_date($timestamp) {
		$date = '';
		$month = ucfirst(TMM_Helper::get_short_monts_names(date('n', $timestamp) - 1));
		$day = date('d', (int) $timestamp);
		$year = date('Y', (int) $timestamp);
		
		if(TMM::get_option('events_date_format') === '1'){
			$date = $day . ' ' . $month . ', ' . $year;
		}else{
			$date = $month . ' ' . $day . ', ' . $year;
		}
        
		return $date;
	}
	
	public static function get_event_time($timestamp, $hide_time_zone = false) {
		$time = '';
		$time_format = '';
		
		if($timestamp){
			if(TMM::get_option('events_time_format') === '1'){
				$time_format = 'h:i A';
			}else{
				$time_format = 'H:i';
			}
			$time = date($time_format, $timestamp);
			if(TMM::get_option('events_hide_time_zone') === '0' && !$hide_time_zone){
				$time .= ' ' . TMM_Event::get_timezone_string();
			}
		}
		
		return $time;
	}

}
