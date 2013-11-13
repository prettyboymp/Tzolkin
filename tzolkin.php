<?php
/*
	Plugin Name: Tzolkin - Events Calendar
	Plugin URI: https://github.com/Clark-Nikdel-Powell/Tzolkin
	Version: 0.1
	Description: Event Calendar plugin for WordPress including iCAL support and advanced properties.
	Author: Chris Roche & Taylor Gorman
	Author URI: http://www.clarknikdelpowell.com

	Copyright 2012  Chris Roche & Taylor Gorman (email : wordpress@clarknikdelpowell.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

////////////////////////////////////////////////////////////////////////////////
// PLUGIN CONSTANT DEFINITIONS
////////////////////////////////////////////////////////////////////////////////

//FILESYSTEM CONSTANTS
define('TZ_PATH', plugin_dir_path(__FILE__));
define('TZ_URL', plugin_dir_url(__FILE__));

////////////////////////////////////////////////////////////////////////////////
// PLUGIN DEPENDENCIES
////////////////////////////////////////////////////////////////////////////////

require_once TZ_PATH.'tz-event.php';
require_once TZ_PATH.'tz-functions.php';
require_once TZ_PATH.'tz-admin.php';

////////////////////////////////////////////////////////////////////////////////
// ROOT PLUGIN CLASS
////////////////////////////////////////////////////////////////////////////////

final class TZ_Tzolkin {

	public static function activation() {
		add_action('init', array('TZ_Event', 'register'));
		flush_rewrite_rules();
	}

	public static function deactivation() {
		flush_rewrite_rules();
	}

	public static function uninstall() { /* PLUGIN DELETION LOGIC HERE */ }

	public static function initialize() {
		TZ_Event::initialize();
		TZ_Admin::initialize();
	}

}

////////////////////////////////////////////////////////////////////////////////
// PLUGIN INITIALIZATION
////////////////////////////////////////////////////////////////////////////////

register_activation_hook(__FILE__, array('TZ_Tzolkin', 'activation'));
register_deactivation_hook(__FILE__, array('TZ_Tzolkin', 'deactivation'));
register_uninstall_hook(__FILE__, array('TZ_Tzolkin', 'uninstall'));
TZ_Tzolkin::initialize();
