<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<div class="widget widget_upcoming_events">


	<?php
	$start = current_time('timestamp');
	if (isset($delay)) {
		$start = $start - $delay * 3600;
	}else{
		$delay=0;
	}
	
	$category_obj = get_term_by('id', (int) $category, 'events-categories');
	if($category && $category_obj){
		$category = $category_obj->term_taxonomy_id;
	}

	$data = TMM_Event::get_soonest_event($start, $count, $deep, $category, $delay);
	if ($sorting == 'DESC') {
		$data = array_reverse($data);
	}
	?>

    <ul class="clearfix">
        <?php if (!empty($data)){ ?>
            <?php foreach ($data as $event) { ?>
                <li>
                    <div class="post-content">
						<h5 class="title">
							<a href="<?php echo $event['url'] ?>"><?php echo $event['title'] ?></a>
						</h5>
                        <p>
                            <span class="month"><?php echo ucfirst(date("F", $event['start_mktime'])); ?></span>
                            <span class="date"><?php echo date("d", $event['start_mktime']) ?>, </span>
                            <span class="date"><?php echo date("Y", $event['start_mktime']) ?></span>
                            <?php /*<span class="time"><?php echo date((TMM::get_option("events_time_format") == 1 ? "h:i A" : "H:i"), $event['start_mktime']) ?> - <?php echo date((TMM::get_option("events_time_format") == 1 ? "h:i A" : "H:i"), $event['end_mktime']) ?></span>
                            <span class="timezone"><?php echo TMM_Event::get_timezone_string() ?></span>*/ ?>
                        </p>
						<div><?php echo wp_trim_words( get_the_excerpt(), 10, ' ...' ); ?></div>
                    </div>
                </li>
            <?php } ?>
        <?php }else{ ?>
				<div><?php _e('There is no events added yet!', TMM_THEME_TEXTDOMAIN); ?></div>
        <?php } ?>
    </ul>

</div><!--/ .widget-->

