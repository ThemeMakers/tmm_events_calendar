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