<?php
$now = current_time('timestamp');
$start = strtotime(date("Y", $now) . '-' . date("m", $now) . '-' . 01, $now);
$end = mktime(0, 0, 0, date("m", $start)+1, 1, date("Y", $start));

$options = array(
	'start' => $start,
	'end' => $end,
	'category' => 0,
	'order' => 'DESC',
	'count' => false,
);

if (isset($category)) {
	$options['category'] = $category;
}

if (isset($sorting)) {
	$options['order'] = $sorting;
}

if (isset($count)) {
	$options['count'] = $count;
}

?>

<h3 class="widget-title"><span id="events_listing_month"></span>&nbsp;<span id="events_listing_year"></span></h3>

<?php
if (TMM::get_option("tmm_events_show_categories_select") == 1){

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

<div class="events_listing_wrap">

	<div id="events_listing"></div>

	<div class="infscr-loading_wrap">
		<div id="infscr-loading">
			<div id="facebookG">
				<div id="blockG_1" class="facebook_blockG">
				</div>
				<div id="blockG_2" class="facebook_blockG">
				</div>
				<div id="blockG_3" class="facebook_blockG">
				</div>
			</div>
		</div>
	</div>

</div>

<div class="pagenavbar">
	<div class="events_listing_navigation pagenavi" style="display:none;clear: both"></div>
</div><!--/ .pagenavbar-->

<a href="#" class="js_prev_events_page button default" style="display: none;"><?php _e('Previous Events', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></a>&nbsp;
<a href="#" class="js_next_events_page button default"><?php _e('Next Events', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></a>

<script type="text/javascript">
    jQuery(function() {
        var app_event_listing = new THEMEMAKERS_EVENT_EVENTS_LISTING();
        app_event_listing.init(<?php echo json_encode($options); ?>);
    });
</script>
