<?php

if (!function_exists('tz_get_event_dates')) {
	/**
	 * Returns the dates associated with an event as an associative array 
	 * containing 'start' and 'end' as the available keys. If $format is not 
	 * provided, DateTime objects are returned, otherwise the date is returned as 
	 * a formatted string. Formats follow PHPs date() format.
	 * 
	 * @access public
	 * @param  int    $post_id The post ID of the event
	 * @param  string $format  PHP style date format string. Leave blank for DateTime objects.
	 * @return array           Associative array of event dates 
	 */
	function tz_get_event_dates($post_id, $format = '') {
		return TZ_Event::get_event_dates($post_id, $format);
	}
}

if (!function_exists('tz_get_the_dates')) {
	function tz_get_the_dates($format = '') {
		return TZ_Event::get_the_dates($post_id, $format);
	}
}

if (!function_exists('tz_get_event_start_date')) {
	function tz_get_event_start_date($post_id, $format = '') {
		return TZ_Event::get_event_start_date($post_id, $format);
	}
}

if (!function_exists('tz_get_event_end_date')) {
	function tz_get_event_end_date($post_id, $format = '') {
		return TZ_Event::get_event_end_date($post_id, $format);
	}
}

if (!function_exists('tz_get_the_start_date')) {
	function tz_get_the_start_date($format = '') {
		return TZ_Event::get_the_start_date($format);
	}
}

if (!function_exists('tz_get_the_end_date')) {
	function tz_get_the_end_date($format = '') {
		return TZ_Event::get_the_end_date($format);
	}
}

if (!function_exists('tz_the_start_date')) {
	function tz_the_start_date($format = '') {
		return TZ_Event::the_start_date($format);
	}
}

if (!function_exists('tz_the_end_date')) {
	function tz_the_end_date($format = '') {
		return TZ_Event::the_end_date($format);
	}
}

if (!function_exists('tz_event_is_all_day')) {
	function tz_event_is_all_day($post_id) {
		return TZ_Event::event_is_all_day($post_id);
	}
}

if (!function_exists('tz_is_all_day')) {
	function tz_is_all_day() {
		return TZ_Event::is_all_day();
	}
}

if (!function_exists('tz_event_is_same_day')) {
	function tz_event_is_same_day($post_id) {
		return TZ_Event::event_is_same_day($post_id);
	}
}

if (!function_exists('tz_is_same_day')) {
	function tz_is_same_day() {
		return TZ_Event::is_same_day();
	}
}

if (!function_exists('tz_get_event_location')) {
	function tz_get_event_location($post_id) {
		return TZ_Event::get_event_location($post_id);
	}
}

if (!function_exists('tz_get_location')) {
	function tz_get_location() {
		return TZ_Event::get_location();
	}
}
