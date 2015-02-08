<?php
if(!isset($queried_object) || !is_object($queried_object)){
	$queried_object = get_queried_object();
}
$category = (int) $queried_object->term_taxonomy_id;
$now = current_time('timestamp');
$start = strtotime(date("Y", $now) . '-' . date("m", $now) . '-' . 01, $now);
$end = $start + 86400*365;
?>

<div id="events_listing"></div>
<br /><br />
<div class="events_listing_navigation wp-pagenavi" style="display:none;clear: both"></div>

<script type="text/javascript">
    var app_event_listing = null;
    jQuery(function() {
        app_event_listing = new THEMEMAKERS_EVENT_EVENTS_LISTING();
        app_event_listing.init(<?php echo $start; ?>, false, <?php echo $category; ?>);
    });
</script>