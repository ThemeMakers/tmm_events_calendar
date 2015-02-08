<?php $inique_id = uniqid(); ?>

<div id='calendar_<?php echo $inique_id ?>' style="width: 100%;"></div>

<script type="text/javascript">
    var calendar_<?php echo $inique_id ?> = null;
    jQuery(function() {
        var arguments = {
            header: {
                left: "prev,next today",
                center: "title",
                right: "month,agendaWeek,agendaDay"
            },
			first_day:<?php echo get_option('start_of_week'); ?>
        };
        calendar_<?php echo $inique_id ?> = new THEMEMAKERS_EVENT_CALENDAR("#calendar_<?php echo $inique_id ?>", arguments, false, "<?php echo TMM_Event::get_timezone_string() ?>");
        calendar_<?php echo $inique_id ?>.init();
        
    });
</script>
