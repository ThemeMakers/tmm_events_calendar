<?php
get_header();

global $post;

$thumb_type = ($_REQUEST['sidebar_position'] == 'no_sidebar') ? 'single-thumb' : 'single-thumb-small';
$thumb_size = ($_REQUEST['sidebar_position'] == 'no_sidebar') ? '950*545' : '610*350';
$thumb = class_exists('TMM_Helper') ? TMM_Helper::get_post_featured_image($post->ID, $thumb_size) : '';

if(have_posts()){
	while (have_posts()) {
		the_post();

		$event_allday = get_post_meta($post->ID, 'event_allday', true);
		$hide_event_place = get_post_meta($post->ID, 'hide_event_place', true);
		$event_place_address = get_post_meta($post->ID, 'event_place_address', true);
		$event_place_phone = get_post_meta($post->ID, 'event_place_phone', true);
		$event_place_website = get_post_meta($post->ID, 'event_place_website', true);
		$repeats_every = get_post_meta($post->ID, 'event_repeating', true);

		$ev_mktime = (int) get_post_meta($post->ID, 'ev_mktime', true);
		$ev_end_mktime = (int) get_post_meta($post->ID, 'ev_end_mktime', true);

		$events_show_duration = tmm_events_get_option('tmm_single_event_show_duration');
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

		$next_post = get_next_post();
		$prev_post = get_previous_post();

		$event_organizer_phone = get_post_meta($post->ID, 'event_organizer_phone', true);
		$event_organizer_website = get_post_meta($post->ID, 'event_organizer_website', true);
		$event_organizer_name = get_post_meta($post->ID, 'event_organizer_name', true);

		$css_classes = 'entry event';

		if (!$thumb || !has_post_thumbnail()) {
			$css_classes .= ' no-image';
		}

		$events_button_page = tmm_events_get_option('tmm_single_event_button_page');
		$events_button_url = '';

		if ($events_button_page === '0') {
			$events_button_url = home_url() . '/' . get_post_type();
		} else if ($events_button_page !== '-1') {
			$events_button_url = get_permalink($events_button_page);
		}

		if ($repeats_every !== 'no_repeat') {

			$tmp_date = '';

			if (isset($_GET['date'])) {
				$tmp_date = explode('-', $_GET['date']);
			} else if (isset($wp_query->query_vars['date'])) {
				$tmp_date = explode('-', $wp_query->query_vars['date']);
			}

			if (is_array($tmp_date) && !empty($tmp_date[0]) && !empty($tmp_date[1]) && !empty($tmp_date[2])) {
				if(tmm_events_get_option('tmm_events_date_format') === '1'){
					$ev_mktime = mktime(0, 0, 0 , $tmp_date[1], $tmp_date[0], $tmp_date[2]);
				}else{
					$ev_mktime = mktime(0, 0, 0 , $tmp_date[0], $tmp_date[1], $tmp_date[2]);
				}

				$ev_end_mktime = $ev_mktime;
			}

		}

		$event_date = TMM_Event::get_event_date($ev_mktime);
		$event_end_date = TMM_Event::get_event_date($ev_end_mktime);
		$day = date('d', $ev_mktime);
		$month = tmm_get_short_month_name( date('n', $ev_mktime) );
		?>

		<?php if ($events_button_url) { ?>
			<a href="<?php echo esc_url($events_button_url); ?>" class="button default"><?php _e('All Events', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></a>
			<br><br><br>
		<?php } ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class($css_classes); ?>>

			<?php if (has_post_thumbnail() && $thumb) { ?>

				<div class="work-item">
					<a href="<?php echo esc_url( TMM_Helper::get_post_featured_image($post->ID, '') ); ?>" class="single-image">
						<figure class="add-border">
							<?php tmm_post_thumbnail($thumb_type); ?>
						</figure>
					</a>
				</div><!--/ .bordered-->

			<?php } ?>

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
						<span><?php echo esc_html($event_place_address); ?></span>
					</div>
				<?php } ?>
				<?php if (!empty($event_place_phone)){ ?>
					<div class="event-phone">
						<strong><?php _e('Phone', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?>: </strong>
						<span><?php echo esc_html($event_place_phone); ?></span>
					</div>
				<?php } ?>
				<?php if (!empty($event_place_website)){ ?>
					<div class="event-website">
						<strong><?php _e('Website', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?>: </strong>
						<a target="_blank" href="<?php echo esc_url($event_place_website); ?>"><?php echo esc_url($event_place_website); ?></a>
					</div>
				<?php } ?>

				<?php if (!empty($event_organizer_name)){ ?>
					<div class="event-person">
						<strong><?php _e('Contact Person', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?>: </strong>
						<span><?php echo esc_html($event_organizer_name); ?></span>
					</div>
				<?php } ?>
				<?php if (!empty($event_organizer_phone)){ ?>
					<div class="event-phone">
						<strong><?php _e('Organizer Phone', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?>: </strong>
						<span><?php echo esc_html($event_organizer_phone); ?></span>
					</div>
				<?php } ?>
				<?php if (!empty($event_organizer_website)){ ?>
					<div class="event-website">
						<strong><?php _e('Organizer Website', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?>: </strong>
						<a target="_blank" href="<?php echo esc_url($event_organizer_website); ?>"><?php echo esc_url($event_organizer_website); ?></a>
					</div>
				<?php } ?>

			</div><!--/ .event-details-->

			<?php if (!$hide_event_place) { ?>
				<div class="map">
					<?php
					if (class_exists('TMM_Content_Composer')) {
						$event_map_longitude = (float) get_post_meta($post->ID, 'event_map_longitude', true);
						$event_map_latitude = (float) get_post_meta($post->ID, 'event_map_latitude', true);
						$event_map_zoom = (int) get_post_meta($post->ID, 'event_map_zoom', true);
						echo do_shortcode('[google_map width="550" height="330" latitude="' . $event_map_latitude . '" longitude="' . $event_map_longitude . '" zoom="' . $event_map_zoom . '" controls="" enable_scrollwheel="0" map_type="ROADMAP" enable_marker="1" enable_popup="0"][/google_map]');
					}
					?>
				</div>
			<?php } ?>
			<br />

			<?php
			the_content();

			if(function_exists('tmm_link_pages')){
				tmm_link_pages();
			}else{
				wp_link_pages();
			}

			if(function_exists('tmm_layout_content')){
				tmm_layout_content(get_the_ID(), 'default');
			}
			?>

		</article><!--/ .event-->

		<?php if($prev_post || $next_post){ ?>

			<?php if($prev_post){ ?>

				<a href="<?php echo get_the_permalink($prev_post->ID); ?>" class="js_prev_event_post button default" style="">&larr; <?php _e('Previous Event', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></a>

			<?php } ?>

			<?php if($next_post){ ?>

				<a href="<?php echo get_the_permalink($next_post->ID); ?>" class="js_next_event_post button default"><?php _e('Next Event', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?> &rarr;</a>

			<?php } ?>

			<br><br>

		<?php } ?>

	<?php
	}
}

?>

<div class="clear"></div>

<?php
if ( (!isset($_REQUEST['disable_blog_comments']) || !$_REQUEST['disable_blog_comments']) && tmm_events_get_option('tmm_single_event_show_comments') !== '0' ) {
	comments_template();
}

get_footer();