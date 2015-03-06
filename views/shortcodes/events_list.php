<?php
$now = current_time('timestamp');
$start = strtotime(date("Y", $now) . '-' . date("m", $now) . '-' . 01, $now);
$end = mktime(0, 0, 0, date("m", $start)+1, 1, date("Y", $start));
$period_options = array();

$options = array(
	'start' => $start,
	'end' => $end,
	'category' => 0,
	'order' => 'DESC',
	'count' => 5,
	'show_period_selector' => 0,
);

if (isset($category)) {
	$options['category'] = $category;
}

if (isset($sorting)) {
	$options['order'] = $sorting;
}

if (isset($count)) {
	$options['count'] = $count ? $count : 0;
}

if (isset($show_period_selector)) {
	$options['show_period_selector'] = $show_period_selector;
}

if ($options['show_period_selector'] && isset($period_selector_amount)) {
	global $wp_locale;
	$next_timestamp = $start;

	for ($i=0, $ic=$period_selector_amount; $i<$ic; $i++) {
		$month_name = $wp_locale->get_month( date('m', $next_timestamp) );
		$year = date('Y', $next_timestamp);
		$period_options[$next_timestamp] = $month_name . ' ' . $year;

		$next_timestamp = strtotime("next month", $next_timestamp);
	}
}

if ($options['count'] > 0) {
	?>

	<h3 class="widget-title"><span id="events_listing_month"></span>&nbsp;<span id="events_listing_year"></span></h3>

	<?php if ($options['show_period_selector'] && !empty($period_options)) { ?>

		<fieldset class="input-block">
			<select id="event_listing_period" autocomplete="off">
				<?php foreach ($period_options as $key => $value){ ?>
					<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
				<?php } ?>
			</select>
		</fieldset>

	<?php } ?>

	<div id="events_listing"></div>

	<div class="pagenavbar">
		<div class="events_listing_navigation pagenavi" style="display:none;clear: both"></div>
	</div><!--/ .pagenavbar-->

	<script type="text/javascript">
	    jQuery(function() {
	        var app_event_listing = new THEMEMAKERS_EVENT_EVENTS_LISTING();
	        app_event_listing.init(<?php echo json_encode($options); ?>);
	    });
	</script>

	<?php
}