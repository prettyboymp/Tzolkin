<?php

final class TZ_Event {

	private static $name = 'tz_event';
	private static $slug = 'calendar';

	private static $nonce      = 'tz-nonce';
	private static $all_day    = 'tz-all-day';
	private static $start_date = 'tz-start-date';
	private static $start_time = 'tz-start-time';
	private static $end_date   = 'tz-end-date';
	private static $end_time   = 'tz-end-time';

	private static $start_meta   = 'tz_start';
	private static $end_meta     = 'tz_end';
	private static $all_day_meta = 'tz_all_day';
	private static $mysql_format = 'Y-m-d H:i:s';

	private static $year_tag  = 'tz_year';
	private static $month_tag = 'tz_month';
	private static $day_tag   = 'tz_day';
	
	private static $register_args = array(
	  'public'             => true,
	  'publicly_queryable' => true,
	  'show_ui'            => true, 
	  'show_in_menu'       => true, 
	  'query_var'          => true,
	  'capability_type'    => 'post',
	  'has_archive'        => true, 
	  'hierarchical'       => false,
	  'menu_position'      => 5,
	  'rewrite'            => array('with_front' => false),

	  'labels' => array(
			'name'               => 'Calendar',
			'singular_name'      => 'Event',
			'add_new'            => 'Add New',
			'all_items'          => 'All Events',
			'add_new_item'       => 'Add New Event',
			'edit_item'          => 'Edit Event',
			'new_item'           => 'New Event',
			'view_item'          => 'View Event',
			'search_items'       => 'Search Events',
			'not_found'          => 'No events found',
			'not_found_in_trash' => 'No events found in Trash',
			'parent_item_colon'  => 'Parent Event',
			'menu_name'          => 'Events'
	  ),

	  'supports' => array(
	  	'title', 
	  	'editor', 
	  	'thumbnail', 
	  	'excerpt', 
	  	'revisions'
	  )
	);

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// THEME HELPER METHODS
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	private static function get_event_date($post_id, $key, $format = '') {
		$format = trim($format);
		$date_str = get_post_meta($post_id, $key, true);
		if (!$date_str) return $format ? '' : null;
		$date = new DateTime($date_str);
		return $format ? $date->format($format) : $date;
	}

	private static function get_the_date($key, $format = '') {
		global $post;
		return self::get_event_date($post->ID, $key, $format);
	}

	private static function the_date($key, $format = '') {
		$format = trim($format);
		if (!$format) $format = get_option('date_format');
		echo self::get_the_date($key, $format);
	}

	public static function get_event_dates($post_id, $format = '') {
		return array(
			'start' => self::get_event_date($post_id, self::$start_meta, $format),
			'end'   => self::get_event_date($post_id, self::$end_meta, $format)
		);
	}

	public static function get_the_dates($format = '') {
		global $post;
		return self::get_event_dates($post->ID, $format);
	}

	public static function get_event_start_date($post_id, $format = '') {
		return self::get_event_date($post_id, self::$start_meta, $format);
	}

	public static function get_event_end_date($post_id, $format = '') {
		return self::get_event_date($post_id, self::$end_meta, $format);
	}

	public static function get_the_start_date($format = '') {
		return self::get_the_date(self::$start_meta, $format);
	}

	public static function get_the_end_date($format = '') {
		return self::get_the_date(self::$end_meta, $format);
	}

	public static function the_start_date($format = '') {
		return self::the_date(self::$start_meta, $format);
	}

	public static function the_end_date($format = '') {
		return self::the_date(self::$end_meta, $format);
	}

	public static function event_is_all_day($post_id) {
		return (bool)get_post_meta($post_id, self::$all_day_meta, true);
	}

	public static function is_all_day() {
		global $post;
		return event_is_all_day($post->ID);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// POST META MAGIC!
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public static function is_tzolkin() {
		if (!is_admin()) return false;
	  
	  if( isset($_GET['post_type']) ) $post_type = $_GET['post_type']; 
		elseif( isset($_GET['post']) && isset($_GET['action']) && 
		$_GET['action'] === 'edit') $post_type = get_post_type($_GET['post']); 

		return self::$name === $post_type;
	}

	public static function enqueue_scripts() {
		if (!self::is_tzolkin()) return;
		
		wp_enqueue_script(
			'tz-jquery-ui-datetime', 
			TZ_URL.'resources/jquery-ui-timepicker.js', 
			array('jquery-ui-datepicker', 'jquery-ui-slider')
		);

		wp_enqueue_script(
			'tz-event', 
			TZ_URL.'resources/tz-event.js', 
			array('tz-jquery-ui-datetime')
		);
	}

	public static function enqueue_styles() {
		if (!self::is_tzolkin()) return;
		wp_enqueue_style('tz-jquery-ui', TZ_URL.'resources/jquery-ui-smoothness.css');
		wp_enqueue_style('tz-styles', TZ_URL.'resources/jquery-ui-timepicker.css');
	}

	public static function add_meta_box() {
		add_meta_box(
			'tz-event-meta',
			'Event Details',
			array(__CLASS__, 'event_meta_box'),
			self::$name,
			'side',
			'default'
		);
	}
	
	public static function event_meta_box($event, $box) { 

		$all_day = (bool)get_post_meta($event->ID, self::$all_day_meta, true);
		
		$start_str = get_post_meta($event->ID, self::$start_meta, true);
		$end_str = get_post_meta($event->ID, self::$end_meta, true);
		
		$start = new DateTime($start_str ? $start_str : current_time('mysql'));
		$end = new DateTime($end_str ? $end_str : current_time('mysql'));

		wp_nonce_field(self::$name, self::$nonce, false);
		?>
			<p>
				<label for="<?php echo self::$all_day; ?>">
					<input type="checkbox" name="<?php echo self::$all_day; ?>" id="<?php echo self::$all_day; ?>" 
					 value="<?php echo self::$all_day; ?>" <?php echo $all_day ? 'checked="checked" ' : ''; ?>/>
					All Day Event
				</label>
			</p>
			<p>
				<label for="<?php echo self::$start_date; ?>">Start Date/Time</label><br/>
				<input type="text" name="<?php echo self::$start_date; ?>" id="<?php echo self::$start_date; ?>" 
				 class="tz-input tz-date" value="<?php esc_attr_e($start->format('m/d/Y h:i A')); ?>" />
				<input type="text" name="<?php echo self::$start_time; ?>" id="<?php echo self::$start_time; ?>" 
				 class="tz-input tz-time" value="<?php esc_attr_e($start->format('h:i A')); ?>" />
			</p>
			<p>
				<label for="<?php echo self::$end_date; ?>">End Date/Time</label><br/>
				<input type="text" name="<?php echo self::$end_date; ?>" id="<?php echo self::$end_date; ?>" 
				 class="tz-input tz-date" value="<?php esc_attr_e($end->format('m/d/Y h:i A')); ?>" />
				<input type="text" name="<?php echo self::$end_time; ?>" id="<?php echo self::$end_time; ?>" 
				 class="tz-input tz-time" value="<?php esc_attr_e($end->format('h:i A')); ?>" />
			</p>
		<?php
	}

	public static function save_post($post_id, $post) {
		if (!isset($_POST[self::$nonce])) return $post_id;
		$all_day = isset($_POST[self::$all_day]);
		$start = new DateTime(sprintf('%s %s', $_POST[self::$start_date], $_POST[self::$start_time]));
		$end = new DateTime(sprintf('%s %s', $_POST[self::$end_date], $_POST[self::$end_time]));

		if ($start > $end) 
			$end = clone $start;

		if ($all_day) {
			$start->setTime(0,0,0);
			$end->setTime(23,59,59);
		}

		update_post_meta($post_id, self::$all_day_meta, $all_day);
		update_post_meta($post_id, self::$start_meta, $start->format(self::$mysql_format));
		update_post_meta($post_id, self::$end_meta, $end->format(self::$mysql_format));

		return $post_id;
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// REWRITE LOGIC
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public static function tags() {
		return array(
			self::$year_tag,
			self::$month_tag,
			self::$day_tag
		);
	}

	public static function add_rewrite_tags() {
		foreach(self::tags() as $tag) 
			add_rewrite_tag(sprintf('%%%s%%', $tag), '([^&]+)');
	}

	public static function add_rewrite_rules() {
		global $wp_rewrite;

		$rules = array();
		$regex = self::$slug;
		$redirect = 'index.php?post_type='.self::$name;

		foreach(self::tags() as $index => $tag) {
			$i = $index;
			$regex = sprintf('%s/(\d+)', $regex);
			$redirect = sprintf('%s&%s=$%d', $redirect, $tag, ++$i);

			$rules[sprintf('%s/?$', $regex)] = $redirect;
			$rules[sprintf('%s/page/?(\d+)/?$', $regex)] = sprintf('%s&paged=$%d', $redirect, ++$i);
		}

		$rules = array_reverse($rules);
		foreach($rules as $regex => $redirect) {
			if (0 < preg_match_all('@\$([0-9])@', $redirect, $matches))
				for ($i = 0; $i < count($matches[0]); ++$i)
					$redirect = str_replace($matches[0][$i], '$matches['.$matches[1][$i].']', $redirect);

			$wp_rewrite->add_rule($regex, $redirect, 'top');
		}
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// TITLE INTERCEPTION
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	public static function wp_title($title, $sep, $seplocation) {
		global $wp_query;
		if (!is_archive()) return $title;
		if (!is_array($wp_query->query)) return $title;
		if (!array_key_exists('post_type', $wp_query->query)) return $title;
		if (self::$name !== $wp_query->query['post_type']) return $title;

		$title_format = "%s %s %s";
		$year_format = "Y";
		$month_format = "F Y";
		$day_format = "F j, Y";

		extract(self::parse_date($wp_query));
		$date = new DateTime();
		$date->setDate
		(
			$year > 0 ? $year : 1,
			$month > 0 && $month < 13 ? $month : 1,
			$day > 0 && $day < 32 ? $day : 1
		);

		if ($year <= 0) return $title;
		elseif ($month <= 0 || $month > 12) return sprintf($title_format, $date->format($year_format), $sep, $title);
		elseif ($day <= 0 || $day > 31) return sprintf($title_format, $date->format($month_format), $sep, $title);
		else return sprintf($title_format, $date->format($day_format), $sep, $title);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// WP QUERY ACTION INTERCEPTIONS
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public static function parse_date($query) {
		$output = array(
			'year' => 0,
			'month' => 0,
			'day' => 0
		);

		if (
			array_key_exists(self::$year_tag, $query->query) 
			&& is_numeric($query->query[self::$year_tag])
		)
			$output['year'] = (int)$query->query[self::$year_tag];

		if (
			array_key_exists(self::$month_tag, $query->query) 
			&& is_numeric($query->query[self::$month_tag])
		)
			$output['month'] = (int)$query->query[self::$month_tag];

		if (
			array_key_exists(self::$day_tag, $query->query) 
			&& is_numeric($query->query[self::$day_tag])
		)
			$output['day'] = (int)$query->query[self::$day_tag];

		return $output;
	}

	public static function pre_get_posts($query) {
		if (!is_archive() || is_admin()) return;
		if (!is_array($query->query)) return;
		if (!array_key_exists('post_type', $query->query)) return;
		if (self::$name !== $query->query['post_type']) return;

		self::order_by_event_date($query);
		self::add_date_filter($query);
	}

	public static function order_by_event_date(&$query) {
		$query->set('meta_key', self::$start_meta);
		$query->set('orderby', self::$start_meta);
	}

	public static function add_date_filter(&$query) {
		$start = null;
		$end   = null;

		$year_format  = '%d-01-01 00:00:00';
		$month_format = '%d-%d-01 00:00:00';
		$day_format   = '%d-%d-%d 00:00:00';
		$mysql_format = 'Y-m-d H:i:s';

		extract(self::parse_date($query));

		//get search start and end dates for the events based on query variables
		//if no

		if ($year <= 0) {
			self::exclude_old_events($query);
			return;
		} 
		else if ($month <= 0 || $month > 12) { //year archive
			$start = new DateTime(sprintf($year_format, $year));
			$end = clone $start;
			$end->modify('+1 year');
			$end->modify('-1 second');
		}
		else if ($day <= 0 || $day > 31) { // month archive
			$start = new DateTime(sprintf($month_format, $year, $month));
			$end = clone $start;
			$end->modify('+1 month');
			$end->modify('-1 second');
		}
		else { //day archive
			$start = new DateTime(sprintf($day_format, $year, $month, $day));
			$end = clone $start;
			$end->modify('+1 day');
			$end->modify('-1 second');
		}

		//add meta query arrays

		$meta_query = $query->meta_query;
		if (!is_array($meta_query)) $meta_query = array();

		//start date must be before search end date
		$meta_query[] = array(
			'key'     => self::$end_meta,
			'value'   => $start->format($mysql_format),
			'compare' => '>=',
			'type'    => 'DATETIME'
		);

		//end date must be before search start date
		$meta_query[] = array(
			'key'     => self::$start_meta,
			'value'   => $end->format($mysql_format),
			'compare' => '<=',
			'type'    => 'DATETIME'
		);

		$query->set('meta_query', $meta_query);
	}

	public static function exclude_old_events(&$query) {
		$meta_query = $query->meta_query;
		if (!is_array($meta_query)) $meta_query = array();

		$now = new DateTime();
		$meta_query[] = array(
			'key'     => self::$end_meta,
			'value'   => $now->format('Y-m-d H:i:s'),
			'compare' => '>=',
			'type'    => 'DATETIME'
		);

		$query->set('meta_query', $meta_query);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// REGISTRATION & INITIALIZATION
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	public static function register() {
		$args = self::$register_args;
		$args['rewrite']['slug'] = self::$slug;
		$args = apply_filters('tz_register_args', $args);

		register_post_type(self::$name, $args);
	}

	public static function initialize() {
		add_action('init', array(__CLASS__, 'add_rewrite_tags'));
		add_action('delete_option_rewrite_rules', array(__CLASS__, 'add_rewrite_rules'));
		add_action('init', array(__CLASS__, 'register'));
		add_action('pre_get_posts', array(__CLASS__, 'pre_get_posts'));
		add_action('add_meta_boxes', array(__CLASS__, 'add_meta_box'));
		add_action('admin_init', array(__CLASS__, 'enqueue_scripts'));
		add_action('admin_init', array(__CLASS__, 'enqueue_styles'));
		add_filter('wp_title', array(__CLASS__, 'wp_title'), 10, 3);
		add_action('save_post', array(__CLASS__, 'save_post'), 10, 2);
	}

}
