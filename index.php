<?php
/**
 * Plugin Name: ThemeMakers Events
 * Plugin URI: http://webtemplatemasters.com
 * Description: Events calendar, events list
 * Author: ThemeMakers
 * Author URI: http://themeforest.net/user/ThemeMakers
 * Version: 1.0
*/

define('TMM_EVENTS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('TMM_EVENTS_PLUGIN_URI', plugin_dir_url(__FILE__));
define('TMM_EVENTS_PLUGIN_TEXTDOMAIN', 'tmm_events');

function tmm_events_plugin_autoloader($class) {
	if(file_exists(TMM_EVENTS_PLUGIN_PATH. '/classes/' . $class . '.php')){
		include_once TMM_EVENTS_PLUGIN_PATH. '/classes/' . $class . '.php';
	}
}
spl_autoload_register('tmm_events_plugin_autoloader');

load_plugin_textdomain(TMM_EVENTS_PLUGIN_TEXTDOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages');

add_action("init", array('TMM_EventsPlugin', 'register'));
add_action("admin_init", array('TMM_EventsPlugin', 'admin_init'));
add_action('admin_enqueue_scripts', array('TMM_EventsPlugin', 'admin_head'));
add_action('wp_enqueue_scripts', array('TMM_EventsPlugin', 'wp_head'));
add_action('save_post', array('TMM_Event', 'save_post'));

/* ajax callbacks */
add_action('wp_ajax_nopriv_app_events_get_calendar_data', array('TMM_Event', 'get_calendar_data'));
add_action('wp_ajax_nopriv_app_events_get_widget_calendar_data', array('TMM_Event', 'get_widget_calendar_data'));
add_action('wp_ajax_nopriv_app_events_get_events_listing', array('TMM_Event', 'get_events_listing'));
add_action('wp_ajax_app_events_get_calendar_data', array('TMM_Event', 'get_calendar_data'));
add_action('wp_ajax_app_events_get_widget_calendar_data', array('TMM_Event', 'get_widget_calendar_data'));
add_action('wp_ajax_app_events_get_events_listing', array('TMM_Event', 'get_events_listing'));

/* register widgets */
function tmm_register_events_widgets() {
	register_widget('TMM_SoonestEventWidget');
	register_widget('TMM_EventsCalendarWidget');
	register_widget('TMM_UpcomingEventsWidget');
}
 
add_action( 'widgets_init', 'tmm_register_events_widgets' );

/* on plugin activation */
register_activation_hook( __FILE__, array('TMM_EventsPlugin', 'flush_rewrite_rules') );