<p>
    <label for="<?php echo $widget->get_field_id('title'); ?>"><?php _e('Title', TMM_EVENTS_PLUGIN_TEXTDOMAIN) ?>:</label>
    <input class="widefat" type="text" id="<?php echo $widget->get_field_id('title'); ?>" name="<?php echo $widget->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
</p>

<p>
    <label for="<?php echo $widget->get_field_id('count'); ?>"><?php _e('Count', TMM_EVENTS_PLUGIN_TEXTDOMAIN) ?>:</label>
    <input class="widefat" type="text" id="<?php echo $widget->get_field_id('count'); ?>" name="<?php echo $widget->get_field_name('count'); ?>" value="<?php echo $instance['count']; ?>" />
</p>

<p>
    <label for="<?php echo $widget->get_field_id('month_deep'); ?>"><?php _e('Upcoming events data parsing', TMM_EVENTS_PLUGIN_TEXTDOMAIN) ?>:</label>
    <select id="<?php echo $widget->get_field_id('month_deep'); ?>" name="<?php echo $widget->get_field_name('month_deep'); ?>" class="widefat">
        <?php for ($i = 1; $i <= 12; $i++) : ?>
            <option <?php echo($instance['month_deep'] == $i ? "selected" : "") ?> value="<?php echo $i ?>"><?php echo $i ?> <?php _e('month', TMM_EVENTS_PLUGIN_TEXTDOMAIN) ?><?php if($i>1) echo 's' ?></option>
        <?php endfor; ?>
    </select>
</p>



