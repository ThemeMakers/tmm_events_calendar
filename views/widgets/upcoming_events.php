<div class="widget widget_upcoming_events">

    <?php if (!empty($instance['title'])): ?>
        <h3 class="widget-title"><?php echo $instance['title']; ?></h3>
    <?php endif; ?>

    <?php $data = TMM_Event::get_soonest_event(current_time('timestamp'), (int)$instance['count'], (int)$instance['month_deep']); ?>

    <ul class="clearfix">
        <?php if (!empty($data)){ ?>
            <?php foreach ($data as $event) { ?>
                <li>
                    <div class="post-content">
                        <a class="post-title" href="<?php echo $event['url'] ?>"><?php echo $event['title'] ?></a>
                        <p>
                            <span class="month"><?php echo ucfirst(date("F", $event['start_mktime'])); ?></span>
                            <span class="date"><?php echo date("d", $event['start_mktime']) ?>, </span>
                            <span class="date"><?php echo date("Y", $event['start_mktime']) ?></span>
							<?php //echo ucfirst( TMM_Helper::get_short_monts_names(date("n", $event['start_mktime']) - 1) ); ?>
                            <?php /*<span class="time"><?php echo date((TMM::get_option("events_time_format") == 1 ? "h:i A" : "H:i"), $event['start_mktime']) ?> - <?php echo date((TMM::get_option("events_time_format") == 1 ? "h:i A" : "H:i"), $event['end_mktime']) ?></span>
                            <span class="timezone"><?php echo TMM_Event::get_timezone_string() ?></span>*/ ?>
                        </p>
                    </div>
                </li>
            <?php } ?>
        <?php }else{ ?>
				<div><?php _e('There is no events added yet!', TMM_THEME_TEXTDOMAIN); ?></div>
        <?php } ?>
    </ul>

</div><!--/ .widget-->
