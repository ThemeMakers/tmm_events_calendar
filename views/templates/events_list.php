<?php
$now = current_time('timestamp');
$start = strtotime(date("Y", $now) . '-' . date("m", $now) . '-' . 01, $now);
$end = mktime(0, 0, 0, date("m", $start)+1, 1, date("Y", $start));
?>

<h3 class="widget-title"><span id="events_listing_month"></span>&nbsp;<span id="events_listing_year"></span></h3>

<?php
if (TMM::get_option("events_listing_show_categories") == 1){

	$args = array(
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => true,
		'exclude' => array(),
		'exclude_tree' => array(),
		'include' => array(),
		'fields' => 'all',
		'hierarchical' => true
	);
	$categories = get_terms(array('events-categories'), $args);
	
?>
	<div class="sel">
		<select id="app_event_listing_categories" autocomplete="off">
			<option value="0"><?php _e('All categories', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></option>
				<?php
				if (!empty($categories)){
					foreach ($categories as $cat){
						?>
						<option value="<?php echo $cat->term_taxonomy_id ?>"><?php echo $cat->name ?></option>
						<?php
					}
				}
				?>
		</select>
	</div>

	<br /><br />
	
<?php } ?>

<?php
$pages = new WP_Query(array(
	'post_type' => 'page',
	'posts_per_page' => '1',
	'orderby' => 'date',
	'order' => 'DESC',
	'meta_query' => array(
		array(
			'key' => '_wp_page_template',
			'value' => 'template-calendar.php',
			'compare' => '=='
		)
	),
));

$events_calendar_page = false;
if($pages && count($pages->posts)){ 
	$events_calendar_page = $pages->posts[0];
}

if(is_object($events_calendar_page) && $events_calendar_page->ID){
	?>

	<a href="<?php echo get_the_permalink($events_calendar_page->ID); ?>" class="button default" style=""><?php _e('Calendar', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></a>
	<br><br>

	<?php
}
?>

<div id="events_listing"></div>
<br />

<a href="#" class="js_prev_events_page button default" style="display: none;"><?php _e('Previous Events', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></a>&nbsp;
<a href="#" class="js_next_events_page button default"><?php _e('Next Events', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></a>
<br />
<div class="events_listing_navigation wp-pagenavi" style="display:none;clear: both"></div>
<br />

<script type="text/javascript">
    var app_event_listing = null;
    jQuery(function() {
        app_event_listing = new THEMEMAKERS_EVENT_EVENTS_LISTING();
        app_event_listing.init(<?php echo $start; ?>, <?php echo $end; ?>, 0);
    });
</script>
