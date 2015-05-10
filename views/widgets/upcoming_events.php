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
							<?php if(tmm_events_get_option('tmm_events_date_format') === '1'){ ?>
							<span class="date"><?php echo date("d", $event['start_mktime']) ?>, </span>
							<span class="month"><?php echo ucfirst(date("F", $event['start_mktime'])); ?></span>
							<?php } else { ?>
							<span class="month"><?php echo ucfirst(date("F", $event['start_mktime'])); ?></span>
							<span class="date"><?php echo date("d", $event['start_mktime']) ?>, </span>
							<?php } ?>
							<span class="date"><?php echo date("Y", $event['start_mktime']) ?></span>
						</p>
					</div>
				</li>
			<?php } ?>
		<?php }else{ ?>
			<div><?php _e('There is no events added yet!', TMM_EVENTS_PLUGIN_TEXTDOMAIN); ?></div>
		<?php } ?>
	</ul>

</div><!--/ .widget-->
