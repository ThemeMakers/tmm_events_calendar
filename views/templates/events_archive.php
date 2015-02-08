<?php
$year = (int) $_GET['yy'];
$month = $_GET['mm'];
$day = $_GET['dd'];
$start = @mktime(0, 0, 0, $month, $day, $year, -1);
$end = @mktime(0, 0, 0, $month, $day+1, $year, -1);
?>

<div id="events_listing"></div>
<br /><br />
<div class="events_listing_navigation wp-pagenavi" style="clear: both"></div>

<script type="text/javascript">
    var app_event_listing = null;
    jQuery(function() {
		jQuery(".page-header-bg>div").html('<h1 class="font-small"><?php echo $month,'-',$day,'-',$year; ?></h1>');
        app_event_listing = new THEMEMAKERS_EVENT_EVENTS_LISTING();
        app_event_listing.init(<?php echo $start; ?>, <?php echo $end; ?>, 0);
    });
</script>