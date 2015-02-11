<?php
$pages = new WP_Query(array(
	'post_type' => 'page',
	'posts_per_page' => '1',
	'orderby' => 'date',
	'order' => 'DESC',
	'meta_query' => array(
		array(
			'key' => '_wp_page_template',
			'value' => 'template-events.php',
			'compare' => '=='
		)
	),
));

$events_list_page = false;
if($pages && count($pages->posts)){ 
	$events_list_page = $pages->posts[0];
}

if(is_object($events_list_page) && $events_list_page->ID){
	?>

	<a href="<?php echo get_the_permalink($events_list_page->ID); ?>" class="button default" style="">&larr; <?php _e('Back to Events', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></a>
	<br><br><br>

	<?php
}

$thumb_type = ($_REQUEST['sidebar_position'] == 'no_sidebar') ? 'single-thumb' : 'single-thumb-small';
$thumb_size = tmm_get_image_size($thumb_type);

global $post;

if(have_posts()){
	while (have_posts()) {
		the_post();
?>

		<article id="post-<?php the_ID(); ?>" <?php post_class("entry event"); ?>>

            <?php if (has_post_thumbnail()) { ?>
            
            <div class="work-item">
                <figure class="add-border">
                    <?php tmm_post_thumbnail($thumb_type); ?>
                </figure>
            </div><!--/ .bordered-->
            
            <?php } ?>

			<?php
				$event_allday = get_post_meta($post->ID, 'event_allday', true);
				$hide_event_place = get_post_meta($post->ID, 'hide_event_place', true);
				$event_place_address = get_post_meta($post->ID, 'event_place_address', true);
				
				$ev_mktime = (int) get_post_meta($post->ID, 'ev_mktime', true);
				$event_date = TMM_Event::get_event_date($ev_mktime);
				
				$ev_end_mktime = (int) get_post_meta($post->ID, 'ev_end_mktime', true);
				$event_end_date = TMM_Event::get_event_date($ev_end_mktime);
				
				$repeats_every = get_post_meta($post->ID, 'event_repeating', true);
				$events_show_duration = TMM::get_option('events_show_duration');
				if($events_show_duration){
					$event_duration_sec = TMM_Event::get_event_duration($ev_mktime, $ev_end_mktime);
					$duration_hh = $event_duration_sec[0];
					$duration_mm = $event_duration_sec[1];
				}

				if($event_allday == 1){
					$event_start_time = '';
					$event_end_time = '';
				}else{
					$event_start_time = TMM_Event::get_event_time($ev_mktime);
					$event_end_time = TMM_Event::get_event_time($ev_end_mktime);
				}
				$e_category = get_the_term_list($post->ID, 'events-categories', '', ', ', '');
			?>

			<div class="event-details">
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
				<?php if (!empty($e_category)){ ?>
				<div class="event-venue">
					<strong><?php _e('Category', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></strong>
					<span><?php echo $e_category; ?></span>
				</div>
				<?php } ?>
				<?php if ($repeats_every != "no_repeat"){ ?>
					<div class="e-repeats">
						<strong><?php _e('Repeats every', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?>:</strong>
						<span><?php echo TMM_Event::$event_repeatings[$repeats_every] ?></span>
					</div>
				<?php } ?>
				<?php if (!empty($event_place_address)){ ?>
					<div class="event-address">
						<strong><?php _e('Address', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?>: </strong>
						<span><?php echo $event_place_address ?></span>
					</div>
				<?php } ?>
			</div><!--/ .event-details-->

			<?php if (!$hide_event_place): ?>
				<div class="map">
					<?php
					if(class_exists('TMM_Ext_Shortcodes')){
						$event_map_longitude = get_post_meta($post->ID, 'event_map_longitude', true);
						$event_map_latitude = get_post_meta($post->ID, 'event_map_latitude', true);
						$event_map_zoom = get_post_meta($post->ID, 'event_map_zoom', true);
						echo do_shortcode('[google_map width="550" height="330" latitude="' . $event_map_latitude . '" longitude="' . $event_map_longitude . '" zoom="' . $event_map_zoom . '" controls="" enable_scrollwheel="0" map_type="ROADMAP" enable_marker="1" enable_popup="0"][/google_map]');
					}
					?>
				</div>
			<?php endif; ?>
			<br />
			<!--<div class="nine columns offset-by-one alpha omega" style="padding-left: 0;">-->
			<div class="">

				<?php 
				the_content();

				if(function_exists('tmm_link_pages')){
					tmm_link_pages();
				}else{
					wp_link_pages();
				}

				if(class_exists('TMM_Ext_LayoutConstructor')){
					TMM_Ext_LayoutConstructor::draw_front(get_the_ID());
				}
				?>

			</div><!--/ .nine-columns-->

		</article><!--/ .entry-->

	<?php
	}
}

$next_post = get_next_post();
$prev_post = get_previous_post();

if($prev_post){
	?>
	<a href="<?php echo get_the_permalink($prev_post->ID); ?>" class="js_prev_event_post button default" style="">&larr; <?php _e('Previous Event', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></a>
	<?php
}

if($next_post){
	?>
	<a href="<?php echo get_the_permalink($next_post->ID); ?>" class="js_next_event_post button default"><?php _e('Next Event', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?> &rarr;</a>
	<?php
}
?>

<div class="clear"></div><br><br>