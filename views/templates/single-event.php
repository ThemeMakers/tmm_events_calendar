<?php
get_header();

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

$thumb_size = '745*450';
$thumb = class_exists('TMM_Helper') ? TMM_Helper::get_post_featured_image($post->ID, $thumb_size) : '';

global $post;
global $wp_locale;

if(have_posts()){
	while (have_posts()) {
		the_post();

		$event_allday = get_post_meta($post->ID, 'event_allday', true);
		$hide_event_place = get_post_meta($post->ID, 'hide_event_place', true);
		$event_place_address = get_post_meta($post->ID, 'event_place_address', true);

		$ev_mktime = (int) get_post_meta($post->ID, 'ev_mktime', true);
		$event_date = TMM_Event::get_event_date($ev_mktime);

		$day = date('d', $ev_mktime);
		$month = $wp_locale->get_month_abbrev( date('F', $ev_mktime) );

		$ev_end_mktime = (int) get_post_meta($post->ID, 'ev_end_mktime', true);
		$event_end_date = TMM_Event::get_event_date($ev_end_mktime);

		$repeats_every = get_post_meta($post->ID, 'event_repeating', true);
		$events_show_duration = TMM::get_option('tmm_events_show_duration');
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
?>

		<div id="post-<?php the_ID(); ?>" <?php post_class('event'); ?>>

			<?php if (has_post_thumbnail() && $thumb) { ?>

				<span class="event-date"><?php echo $day; ?><b><?php echo $month; ?></b></span>

				<div class="event-media">
					<img src="<?php echo $thumb; ?>" alt="<?php echo $post->post_title; ?>" />
				</div>

			<?php } ?>

			<h3 class="event-title"><?php echo $post->post_title; ?></h3>

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

			<div class="event-details boxed">
				<dl>
					<h3><?php _e('Details', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></h3>
					<dt><?php _e('Start', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></dt>
					<dd><?php echo $event_date.' '.$event_start_time; ?></dd>

					<dt><?php _e('End', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></dt>
					<dd><?php echo $event_end_date.' '.$event_end_time; ?></dd>

					<?php if (!empty($e_category)) { ?>
					<dt><?php _e('Category', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></dt>
					<dd><?php echo $e_category; ?></dd>
					<?php } ?>

					<?php if ($events_show_duration) { ?>
					<dt><?php _e('Duration', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></dt>
					<dd><?php echo $duration_hh . ":" . $duration_mm; ?></dd>
					<?php } ?>
				</dl>

				<dl>
					<h3><?php _e('Organizer', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></h3>
					<dt>Phone</dt>
					<dd>12345678</dd>

					<dt>Website</dt>
					<dd><a href="#">http://evanto.com</a></dd>
				</dl>

			</div><!--/ .event-details-->

			<div class="row collapse">

				<div class="large-6 columns">
					<div class="event-details boxed">
						<dl>
							<h3><?php _e('Venue', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></h3>
							<dt>Phone</dt>
							<dd>12345678</dd>

							<?php if (!empty($event_place_address)) { ?>
							<dt><?php _e('Address', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></dt>
							<dd>
								<?php echo $event_place_address ?>
							</dd>
							<?php } ?>

							<dt>Website</dt>
							<dd>http://evanto.com</dd>
						</dl>
					</div>
				</div>

				<?php if (!$hide_event_place) { ?>
				<div class="large-6 columns">
					<div class="event-map">
						<div id="map_address" class="google_map">
							<?php
							if (class_exists('TMM_Content_Composer')) {
								$event_map_longitude = get_post_meta($post->ID, 'event_map_longitude', true);
								$event_map_latitude = get_post_meta($post->ID, 'event_map_latitude', true);
								$event_map_zoom = get_post_meta($post->ID, 'event_map_zoom', true);
								echo do_shortcode('[google_map width="375" height="260" latitude="' . $event_map_latitude . '" longitude="' . $event_map_longitude . '" zoom="' . $event_map_zoom . '" controls="" enable_scrollwheel="0" map_type="ROADMAP" enable_marker="1" enable_popup="0"][/google_map]');
							}
							?>
						</div>
					</div>

				</div>
				<?php } ?>

			</div><!--/ .row-->

			<?php if($prev_post || $next_post){ ?>

				<div class="single-post-nav">

					<?php if($prev_post){ ?>

						<a href="<?php echo get_the_permalink($prev_post->ID); ?>" class="prev">
							<?php _e('Previous article', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?>
							<b><?php echo $prev_post->post_title; ?></b>
						</a>

					<?php } ?>

					<?php if($next_post){ ?>

						<a href="<?php echo get_the_permalink($next_post->ID); ?>" class="next">
							<?php _e('Next article', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?>
							<b><?php echo $next_post->post_title; ?></b>
						</a>

					<?php } ?>

				</div><!--/ .single-post-nav-->

			<?php } ?>

		</div><!--/ .event-->

		<?php
		if(is_object($events_list_page) && $events_list_page->ID){
			?>

			<a href="<?php echo get_the_permalink($events_list_page->ID); ?>" class="back-link"><?php _e('All events', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></a>

			<?php
		}
		?>

	<?php
	}
}

?>

<div class="clear"></div>

<?php
if ( !isset($_REQUEST['disable_blog_comments']) || !$_REQUEST['disable_blog_comments'] ) {
	comments_template();
}

get_footer();