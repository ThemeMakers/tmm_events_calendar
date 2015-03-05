<?php
global $wp_locale;

$now = current_time('timestamp');
$month_deep = isset($instance['month_deep']) ? (int) $instance['month_deep'] : 0;
$count = isset($instance['count']) ? (int) $instance['count'] : 1;
$events = TMM_Event::get_soonest_event($now, $count, $month_deep);
$thumb_size = '350*275';

if (is_array($events) && !empty($events)) {
	?>

	<div class="widget widget_upcoming_events">

		<?php if (!empty($instance['title'])){ ?>
			<h3 class="widget-title"><?php echo $instance['title']; ?></h3>
		<?php } ?>

		<ul>

			<?php
			foreach ($events as $event) {
				$thumb = (class_exists('TMM_Helper') && $event['featured_image_src']) ? TMM_Helper::resize_image($event['featured_image_src'], $thumb_size) : '';
				$day = date('d', $event['start_mktime']);
				$month = $wp_locale->get_month_abbrev( date('F', $event['start_mktime']) );
				?>

				<li>
					<div class="event">
						<span class="event-date"><?php echo $day; ?><b><?php echo $month; ?></b></span>
						<div class="event-media">
							<div class="item-overlay">
								<img src="<?php echo $thumb; ?>" alt="<?php echo $event['title']; ?>">
							</div>
							<div class="event-content">
								<h4 class="event-title">
									<a href="<?php echo $event['url'] ?>"><?php echo $event['title'] ?></a>
								</h4>
								<?php if ($event['post_excerpt']){ ?>
									<div class="event-text">
										<?php echo $event['post_excerpt'] ?>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</li>

			<?php
			}
			?>

		</ul>

	</div><!--/ .widget_upcoming_events-->

<?php
}
?>


