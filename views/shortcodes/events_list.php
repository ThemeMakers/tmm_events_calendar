<?php
$now = current_time('timestamp');
$start = strtotime(date("Y", $now) . '-' . date("m", $now) . '-' . 01, $now);
$end = mktime(0, 0, 0, date("m", $start)+1, 1, date("Y", $start));
$show_period_selector = 1;
$period_options = array(
	'0'=>'March 2015',
	'1'=>'April 2015',
);

$options = array(
	'start' => $start,
	'end' => $end,
	'category' => 0,
	'order' => 'DESC',
	'count' => 5,
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

if ($options['count'] > 0) {
	?>

	<h3 class="widget-title"><span id="events_listing_month"></span>&nbsp;<span id="events_listing_year"></span></h3>

	<?php if ($show_period_selector){ ?>

		<fieldset class="input-block">
			<select id="app_event_listing_categories" autocomplete="off">
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