<?php

/* 
 * Upcoming Events Widget
 */

class TMM_UpcomingEventsWidget extends WP_Widget {

    //Widget Setup
    function __construct() {
        //Basic settings
        $settings = array('classname' => __CLASS__, 'description' => __('Featured event', TMM_EVENTS_PLUGIN_TEXTDOMAIN));

        //Creation
        $this->WP_Widget(__CLASS__, __('ThemeMakers Featured Event', TMM_EVENTS_PLUGIN_TEXTDOMAIN), $settings);
    }

    //Widget view
    function widget($args, $instance) {
        $args['instance'] = $instance;
        echo TMM::draw_free_page(TMM_EVENTS_PLUGIN_PATH . 'views/widgets/upcoming_events.php', $args);
    }

    //Update widget
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['count'] = $new_instance['count'];
        $instance['event_type'] = $new_instance['event_type'];
        $instance['event_list'] = $new_instance['event_list'];
        $instance['month_deep'] = $new_instance['month_deep'];
        return $instance;
    }

    //Widget form
    function form($instance) {
        //Defaults
        $defaults = array(
            'title' => __('Upcoming Events', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
            'count' => 3,
            'event_type' => 0,
            'event_list' => '',
            'month_deep'=>1
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        $args = array();
        $args['instance'] = $instance;
        $args['widget'] = $this;
        echo TMM::draw_free_page(TMM_EVENTS_PLUGIN_PATH . 'views/widgets/upcoming_events_form.php', $args);
    }

}