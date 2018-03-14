<?php
/**
 * Plugin Name:     Event Calendar WD
 * Plugin URI:      https://web-dorado.com/products/wordpress-event-calendar-wd.html
 * Description:     Event Calendar WD is an easy event management and planning tool with advanced features.
 * Version:         1.1.11
 * Author:          WebDorado
 * Author URI:      https://web-dorado.com/wordpress-plugins-bundle.html
 * License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

require_once('ecwd_class.php');

if (!defined('ECWD_MAIN_FILE')) {
  define('ECWD_MAIN_FILE', plugin_basename(__FILE__));
}

if (!defined('ECWD_DIR')) {
  define('ECWD_DIR', dirname(__FILE__));

}
if (!defined('ECWD_URL')) {
  define('ECWD_URL', plugins_url(plugin_basename(dirname(__FILE__))));
}

if (!defined('ECWD_VERSION')) {
  define('ECWD_VERSION', "1.1.11");
}

if (!defined('ECWD_PLUGIN_MAIN_FILE')) {
  define('ECWD_PLUGIN_MAIN_FILE', __FILE__);
}

if (!defined('ECWD_MENU_SLUG')) {
  define('ECWD_MENU_SLUG', "edit.php?post_type=ecwd_event");
}

add_action('plugins_loaded', array('ECWD', 'get_instance'));

if (is_admin()) {
  require_once('ecwd_admin_class.php');

  add_action('init', array( 'ECWD_Admin', 'check_silent_update' ));

  register_activation_hook(__FILE__, array('ECWD_Admin', 'global_activate'));
  register_uninstall_hook(__FILE__, array('ECWD_Admin', 'uninstall'));
  add_action('plugins_loaded', array('ECWD_Admin', 'get_instance'));
  add_action('init', array('ECWD_Admin', 'ecwd_freemius'), 9);
}


