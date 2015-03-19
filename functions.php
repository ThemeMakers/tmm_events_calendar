<?php
/**
 * General functions
 */

if (!function_exists('tmm_locate_template')) {
	function tmm_locate_template($path, $data = array(), $echo = true) {
		@extract($data);

		if (!$echo) {
			ob_start();
		}

		include $path;

		if (!$echo) {
			return ob_get_clean();
		}
	}
}

if (!function_exists('tmm_events_get_option')) {
	function tmm_events_get_option($option) {
		if (class_exists('TMM')) {
			return TMM::get_option($option);
		} else {
			return get_option($option);
		}
	}
}

if (!function_exists('tmm_get_days_of_week')) {
	function tmm_get_days_of_week($num) {
		$days = array(
			0 => __('Sunday', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			1 => __('Monday', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			2 => __('Tuesday', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			3 => __('Wednesday', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			4 => __('Thursday', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			5 => __('Friday', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			6 => __('Saturday', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
		);

		return $days[$num];
	}
}

if (!function_exists('tmm_get_months_names')) {
	function tmm_get_months_names($num) {
		$months = array(
			0 => __('January', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			1 => __('February', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			2 => __('March', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			3 => __('April', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			4 => __('May', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			5 => __('June', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			6 => __('July', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			7 => __('August', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			8 => __('September', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			9 => __('October', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			10 => __('November', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			11 => __('December', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
		);

		return $months[$num];
	}
}

if (!function_exists('tmm_get_short_months_names')) {
	function tmm_get_short_months_names($num) {
		$months = array(
			0 => __('jan', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			1 => __('feb', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			2 => __('mar', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			3 => __('apr', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			4 => __('may', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			5 => __('jun', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			6 => __('jul', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			7 => __('aug', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			8 => __('sep', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			9 => __('oct', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			10 => __('nov', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
			11 => __('dec', TMM_EVENTS_PLUGIN_TEXTDOMAIN),
		);

		return $months[$num];
	}
}