<?php 

if (!empty($events)){
	
	foreach ($events as $event){ ?>

		<article class="entry event post">

			<?php
			$hide_event_place = get_post_meta($event['post_id'], 'hide_event_place', true);
			$event_place_address = get_post_meta($event['post_id'], 'event_place_address', true);
			
			$event_date = TMM_Event::get_event_date($event['start_mktime']);
			
			$ev_end_mktime = (int) get_post_meta($event['post_id'], 'ev_end_mktime', true);
			$event_end_date = TMM_Event::get_event_date($ev_end_mktime);
			
			$repeats_every = get_post_meta($event['post_id'], 'event_repeating', true);
			$events_show_duration = TMM::get_option('events_show_duration');
			if($events_show_duration){
				$event_duration_sec = TMM_Event::get_event_duration($event['start_mktime'], $ev_end_mktime);
				$duration_hh = $event_duration_sec[0];
				$duration_mm = $event_duration_sec[1];
			}
			$event_start_time = TMM_Event::get_event_time($event['start_mktime']);
			$event_end_time = TMM_Event::get_event_time($ev_end_mktime);
			?>

			<div class="entry-meta">
				<h2 class="entry-title"><a href="<?php echo $event['url']; ?>"><?php echo $event['title']; ?></a></h2>
			</div><!--/ .entry-meta-->

			<div class="entry-body row_container">

				<div class="event-desc nine columns">
					
					<div class="work-item">
						<a href="<?php echo $event['url']; ?>">
							<figure>
								<img src="<?php echo TMM_Helper::resize_image($event['featured_image_src'], '500*260'); ?>" alt="<?php echo $event['title']; ?>">
							</figure>
						</a>
					</div>
					
					<p><?php echo $event['post_excerpt']; ?></p>					
				</div><!--/ .event-desc-->
				<div class="event-details five columns">
					
					<div class="event-start">
						<strong><?php _e('Start', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></strong>
						<span><?php echo $event_date.' '.$event_start_time; ?></span>
					</div>
					
					<div class="event-end">
						<strong><?php _e('End', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></strong>
						<span><?php echo $event_end_date.' '.$event_end_time; ?></span>
					</div>
					
					<?php if($events_show_duration){ ?>
					<div class="event-duration">
						<strong><?php _e('Duration', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></strong>
						<span><?php echo $duration_hh . ":" . $duration_mm; ?></span>
					</div>
					<?php } ?>
					
					<?php if ($repeats_every != "no_repeat"){ ?>
						<div class="e-repeats">
							<strong><?php _e('Repeats every', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?>:</strong>
							<span><?php echo TMM_Event::$event_repeatings[$repeats_every]; ?></span>
						</div>
					<?php } ?>
					
					<?php if (!empty($event_place_address)): ?>
						<div class="event-address">
							<strong><?php _e('Address', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?>: </strong>
							<span><?php echo $event_place_address; ?></span>
						</div>
					<?php endif; ?>
						
					<?php if (!$hide_event_place) : ?>
						<div class="gmaps">

							<figure class="custom-frame">
								<?php
								$event_map_longitude = get_post_meta($event['post_id'], 'event_map_longitude', true);
								$event_map_latitude = get_post_meta($event['post_id'], 'event_map_latitude', true);
								$event_map_zoom = get_post_meta($event['post_id'], 'event_map_zoom', true);
								$map_size = '300x150';
								echo '<img src="http://maps.googleapis.com/maps/api/staticmap?center=' . $event_map_latitude . ',' . $event_map_longitude . '&zoom=' . $event_map_zoom . '&size='.$map_size.'&markers=color:red|label:P|' . $event_map_latitude . ',' . $event_map_longitude . '&sensor=false">';
								?>               
							</figure>

						</div>
					<?php endif; ?>
				</div><!--/ .event-details-->
				
				<div class="clear"></div>
			</div><!--/ .entry-body -->

		</article><!--/ .entry-->

	<?php	
	}
}else{
	_e('NO EVENTS', TMM_EVENTS_PLUGIN_TEXTDOMAIN);
}
?>
