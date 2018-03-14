<?php
/**
 * Plugin Name: SEO by 10Web
 * Description: WordPress SEO by 10Web plugin lets you enhance your website rank easily, increase its search engine visibility and improve SEO.
 * Version: 1.0.1
 * Author: webdorado
 * Author URI: https://web-dorado.com/
 * License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 *
 */

defined('ABSPATH') || die('Access Denied');

/**
 * Main Class.
 *
 * @class WDSeo
 * @version	1.0.1
 */
final class WDSeo {
  /**
   * The single instance of the class.
   */
  protected static $_instance = null;

  /**
   * Admin pages.
   */
  private $pages = array();

  /**
   * Options instance.
   *
   * @var WD_SEO_Options
   */
  public $options = null;

  /**
   * @var Notices count.
   */
  public $notices;

  /**
   * Main WDSeo Instance.
   *
   * Ensures only one instance is loaded or can be loaded.
   *
   * @static
   * @return WDSeo - Main instance.
   */
  public static function instance() {
    if ( is_null( self::$_instance ) ) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  /**
   * WDSeo Constructor.
   */
  public function __construct() {
    $this->define_constants();

    $this->add_actions();
    $this->includes();

    // Notices count.
    $this->notices = WD_SEO_Library::get_notices_count();
  }

  /**
   * Define Constants.
   */
  private function define_constants() {
    define('WD_SEO_DIR', WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)));
    define('WD_SEO_URL', plugins_url(plugin_basename(dirname(__FILE__))));
    define('WD_SEO_NAME', plugin_basename(dirname(__FILE__)));
    define('WD_SEO_PREFIX', 'wdseo');
    define('WD_SEO_NICENAME', __( 'SEO by 10Web', WD_SEO_PREFIX ));
    define('WD_SEO_PRO', TRUE);
    define('WD_SEO_VERSION', '1.0.1');
    define('WD_SEO_DB_VERSION', '1.0.1');
    define('WD_SEO_CLASS_PREFIX', 'WDSeo');
    define('WD_SEO_PERMISSION', 'manage_options');
    define('WD_SEO_NONCE', 'nonce_wdseo');
    define('WD_SEO_SERVER', 'https://ma.web-dorado.com/api/');// ToDo: This is not the right one.
    define('WD_SEO_REST_API_CRAWL', site_url() . '/?rest_route=/wdseo/v1/crawl'); // Not using rest_url not to be depended of permalink structure.
  }

  /**
   * Include required files.
   */
  private function includes() {
    // Include files depend on their priority.
    require_once( wp_normalize_path( WD_SEO_DIR . '/framework/library.php' ) );
    WD_SEO_Library::require_dir(WD_SEO_DIR . '/framework');
    if (is_admin()) {
      require_once(wp_normalize_path(WD_SEO_DIR . '/admin/controllers/controller.php'));
      require_once(wp_normalize_path(WD_SEO_DIR . '/admin/models/model.php'));
      require_once(wp_normalize_path(WD_SEO_DIR . '/admin/views/view.php'));
      WD_SEO_Library::require_dir(WD_SEO_DIR . '/admin');
    }
    else {
      require_once(wp_normalize_path(WD_SEO_DIR . '/site/site.php'));
    }
  }

  /**
   * Add actions.
   */
  private function add_actions() {
    // Add menu.
    add_action('admin_menu', array( $this, 'admin_menu' ));

    // Add admin menu bar.
    add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 95 );

    // Add topics.
    add_action('admin_notices', array( 'WD_SEO_Library', 'topic' ), 11);

    // Add seo metaboxes to posts.
    add_action('add_meta_boxes', array( $this, 'add_meta_boxes_posts' ));
    $this->add_meta_boxes_taxonomies();

    // Save meta info with post.
    add_action('save_post', array( $this, 'save_meta_boxes' ), 10, 2);
    add_action('edited_terms', array($this, 'save_taxonomy_meta_fields'), 10, 2);

    // Autoupdate sitemap on post and taxanomy edit.
    add_action('save_post', array( $this, 'autoupdate_sitemap' ), 11, 2);
    add_action('delete_post', array( $this, 'autoupdate_sitemap' ), 11, 2);
    add_action('edit_term', array( $this, 'autoupdate_sitemap' ), 11, 3);
    add_action('delete_term', array( $this, 'autoupdate_sitemap' ), 11, 3);

    // Register meta box scripts and styles.
    add_action('admin_enqueue_scripts', array($this, 'register_scripts'));

    // Register scripts.
    add_action('wp_enqueue_scripts', array($this, 'register_common_scripts'));

    // Add init action
    add_action('init', array( $this, 'init' ));
    add_action('init', array( $this, 'compile_sitemap' ));

    // Set redirect after WP object is set up.
    add_action('wp', array( $this, 'set_redirect'));

    // Set auto crawl interval.
//    add_filter('cron_schedules', array( $this, 'autocrawl_interval' ));
//    add_action(WD_SEO_PREFIX . '_schedule', array( 'WD_SEO_CRAWL', '__construct' ));

    // Plugin activate/deactivate.
    register_activation_hook(__FILE__, array( $this, 'activate' ));
    register_deactivation_hook(__FILE__, array( $this, 'deactivate' ));

    add_action( 'rest_api_init', function () {
      register_rest_route( 'wdseo/v1', '/crawl', array(
        'methods' => 'GET',
        'callback' => array( 'WD_SEO_Library', 'generate_crawl_data' ),
      ) );
    } );

    // Schedule events for errors in api requests.
    add_action( WD_SEO_PREFIX . '_error_on_request_create_cron', array( $this, 'error_on_request_create_cron' ));
    add_action( WD_SEO_PREFIX . '_error_on_request_remove_cron', array( $this, 'error_on_request_remove_cron' ));
  }

  /**
   * Call cron starter.
   */
  public function error_on_request_create_cron() {
    WD_SEO_Library::create_cron();
  }

  /**
   * Call cron stopper.
   */
  public function error_on_request_remove_cron() {
    WD_SEO_Library::remove_cron();
  }

  /**
   * Add menu items.
   */
  public function admin_menu() {
    // Add notices to menu title.
    $notices = $this->notices;
    $counter = '';
    if ( $notices['count'] > 0 ) {
      $counter = sprintf(' <span class="update-plugins count-%s"><span class="plugin-count" aria-hidden="true">%s</span><span class="screen-reader-text">' . _n(' notification', ' notifications', $notices['count'], WD_SEO_PREFIX) . '</span></span>', $notices['count'], $notices['count'], $notices['count']);
    }
    $this->pages = array();
    $this->pages[WD_SEO_PREFIX . '_overview'] = array( 'title' => __( 'Overview', WD_SEO_PREFIX ) . $counter, 'permission' => WD_SEO_PERMISSION );

    // Add Search analytics and Search console menus, if the site verified for Google Search console.
    if ($this->options->google_site_verification != '') {
      $this->pages[WD_SEO_PREFIX . '_search_analytics'] = array(
        'title' => __('Search analytics', WD_SEO_PREFIX),
        'permission' => WD_SEO_PERMISSION
      );
      $this->pages[WD_SEO_PREFIX . '_search_console'] = array(
        'title' => __('Search console', WD_SEO_PREFIX),
        'permission' => WD_SEO_PERMISSION
      );
    }

    $this->pages[WD_SEO_PREFIX . '_meta_info'] = array( 'title' => __( 'Meta information', WD_SEO_PREFIX ), 'permission' => WD_SEO_PERMISSION );
    $this->pages[WD_SEO_PREFIX . '_sitemap'] = array( 'title' => __( 'Sitemap', WD_SEO_PREFIX ), 'permission' => WD_SEO_PERMISSION );
    $this->pages[WD_SEO_PREFIX . '_settings'] = array( 'title' => __( 'Settings', WD_SEO_PREFIX ), 'permission' => WD_SEO_PERMISSION );

    $this->pages[WD_SEO_PREFIX . '_uninstall'] = array( 'parent' => FALSE, 'title' => __( 'Uninstall', WD_SEO_PREFIX ), 'permission' => WD_SEO_PERMISSION );

    // Do not show notifications count on menu page if a submenu is open.
    if (array_key_exists(WD_SEO_Library::get('page'), $this->pages)) {
      $counter = '';
    }
    // Add plugin main menu.
    add_menu_page( WD_SEO_NICENAME . $counter, WD_SEO_NICENAME . $counter, WD_SEO_PERMISSION, WD_SEO_PREFIX . '_overview', array( $this, 'menu_page' ), WD_SEO_URL . '/images/icons/logo.png', null );

    // Add plugin submenus.
    foreach ($this->pages as $page_handle => $page) {
      $admin_menu = add_submenu_page((isset($page['parent']) && $page['parent'] === FALSE ? NULL :  WD_SEO_PREFIX . '_overview'), $page['title'], $page['title'], $page['permission'], $page_handle, array( $this, 'menu_page' ));

      // Add styles/scripts to all admin pages.
      add_action('admin_print_scripts-' . $admin_menu, array($this, 'register_admin_scripts'));
      add_action('admin_print_styles-' . $admin_menu, array($this, 'register_admin_styles'));
    }
  }

  /**
   * Add item to admin bar menu.
   */
  public function admin_bar_menu() {
    // Add notices to menu bar.
    wp_print_styles(WD_SEO_PREFIX . '_common');

    // Do not show menu to users without permission.
    if ( !current_user_can( 'edit_posts' ) ) {
      return;
    }

    $notices = $this->notices;

    if ( $notices['count'] > 0 ) {
      $counter_screen_reader_text = array();
      if ( $notices['recommends_count'] > 0 ) {
        $counter_screen_reader_text[] = sprintf(_n( '%s recommendation', '%s recommendations', $notices['recommends_count'], WD_SEO_PREFIX ), $notices['recommends_count']);
        $recommends_counter = sprintf(' <div class="wp-core-ui wp-ui-notification wd-counter"><span aria-hidden="true">%d</span></div>', $notices['recommends_count']);
      }
      if ( $notices['problems_count'] > 0 ) {
        $counter_screen_reader_text[] = sprintf(_n( '%s problem', '%s problems', $notices['problems_count'], WD_SEO_PREFIX ), $notices['problems_count']);
        $problems_counter = sprintf(' <div class="wp-core-ui wp-ui-notification wd-counter"><span aria-hidden="true">%d</span></div>', $notices['problems_count']);
      }
      $counter_screen_reader_text = implode(', ', $counter_screen_reader_text);
      $counter = sprintf(' <div class="wp-core-ui wp-ui-notification wd-counter"><span aria-hidden="true">%d</span><span class="screen-reader-text">%s</span></div>', $notices['count'], $counter_screen_reader_text);

      $overview_page_url = add_query_arg(array('page' => WD_SEO_PREFIX . '_overview'), admin_url('admin.php'));

      global $wp_admin_bar;
      $args = array(
        'id' => WD_SEO_PREFIX . '-menu',
        'title' => WD_SEO_NICENAME . $counter,
        'href' => $overview_page_url,
      );
      $wp_admin_bar->add_menu($args);

      if ( $notices['recommends_count'] > 0 ) {
        $args = array(
          'parent' => WD_SEO_PREFIX . '-menu',
          'id' => WD_SEO_PREFIX . '-recommendations',
          'title' => __('Recommendations', WD_SEO_PREFIX) . $recommends_counter,
          'href' => $overview_page_url,
        );
        $wp_admin_bar->add_menu($args);
      }
      if ( $notices['problems_count'] > 0 ) {
        $args = array(
          'parent' => WD_SEO_PREFIX . '-menu',
          'id' => WD_SEO_PREFIX . '-problems',
          'title' => __('Problems', WD_SEO_PREFIX) . $problems_counter,
          'href' => $overview_page_url,
        );
        $wp_admin_bar->add_menu($args);
      }
    }
  }

  /**
   * Menu page.
   */
  public function menu_page() {
    if ( !function_exists('current_user_can') || !current_user_can(WD_SEO_PERMISSION) ) {
      die('Access Denied');
    }

    $page = WD_SEO_Library::get('page');
    if ( array_key_exists($page, $this->pages) ) {
      // Change menu slug to class name.
      $class_prefix = str_replace(WD_SEO_PREFIX . '_', WD_SEO_CLASS_PREFIX, $page);
      $controller_class = class_exists($class_prefix . 'Controller') ? $class_prefix . 'Controller' : WD_SEO_CLASS_PREFIX . 'AdminController';
      new $controller_class($page);
    }
  }

  /**
   * Register admin styles.
   */
  public function register_admin_styles() {
    wp_enqueue_style(WD_SEO_PREFIX . '_admin');
  }

  /**
   * Register admin scripts.
   */
  public function register_admin_scripts() {
    wp_enqueue_script(WD_SEO_PREFIX . '_common');
    wp_enqueue_script(WD_SEO_PREFIX . '_admin');
  }

  /**
   * Register common scripts.
   */
  public function register_common_scripts() {
    wp_register_style(WD_SEO_PREFIX . '_common', WD_SEO_URL . '/css/common.css', FALSE, WD_SEO_VERSION);
  }

  /**
   * Register admin scripts for post.
   */
  public function register_scripts() {
    wp_register_script(WD_SEO_PREFIX . '_common', WD_SEO_URL . '/js/common.js', array('jquery'), WD_SEO_VERSION);
    wp_register_script(WD_SEO_PREFIX . '_admin', WD_SEO_URL . '/js/admin.js', array('jquery'), WD_SEO_VERSION);
    wp_register_script(WD_SEO_PREFIX . '_wdseo', WD_SEO_URL . '/js/wdseo.js', array('jquery'), WD_SEO_VERSION);
    $localize = array(
      "add_image" => __('Add image', WD_SEO_PREFIX),
      "change_image" => __('Change image', WD_SEO_PREFIX),
      "choose_image" =>  __('Choose image', WD_SEO_PREFIX),
      "placeholders" =>  WD_SEO_Library::get_placeholders(),
      "nonce" => WD_SEO_NONCE,
      "free_version" => __('This functionality is disabled in free version.', WD_SEO_PREFIX),
    );
    wp_localize_script( WD_SEO_PREFIX . '_admin', 'wdseo', $localize);
    $localize["placeholders"] = WD_SEO_Library::get_placeholders(true);
    global $shortcode_tags;
    $localize["shortcodes"] = array_keys($shortcode_tags);
    wp_localize_script( WD_SEO_PREFIX . '_wdseo', 'wdseo', $localize);

    wp_register_script(WD_SEO_PREFIX . '_select2', WD_SEO_URL . '/js/external/select2.min.js', array('jquery'), '4.0.3');

    wp_register_style(WD_SEO_PREFIX . '_admin', WD_SEO_URL . '/css/admin.css', FALSE, WD_SEO_VERSION);
    wp_register_style(WD_SEO_PREFIX . '_common', WD_SEO_URL . '/css/common.css', FALSE, WD_SEO_VERSION);
    wp_register_style(WD_SEO_PREFIX . '_select2', WD_SEO_URL . '/css/external/select2.min.css', FALSE, '4.0.3');
  }

  /**
   * Add meta boxes to all post types.
   */
  public function add_meta_boxes_posts() {
    if ($this->options->current_user_can_view('meta_role')) {
      foreach (get_post_types() as $post_type) {
        if (isset($this->options->metas->$post_type->metabox) && $this->options->metas->$post_type->metabox) {
          add_meta_box(WD_SEO_PREFIX . '_seo_metabox', WD_SEO_NICENAME, array('WDSeometaboxController', 'display'), $post_type, 'normal', 'high', array('type' => 'post'));
        }
      }
    }
  }

  /**
   * Save meta boxes for all post types.
   */
  public function save_meta_boxes() {
    if (is_admin() && $this->options->current_user_can_view('meta_role')) {
      WDSeometaboxController::save();
    }
  }

  /**
   * Add meta boxes to all taxonomies edit pages.
   */
  public function add_meta_boxes_taxonomies() {
    foreach (get_taxonomies() as $taxonomy) {
      add_action($taxonomy . '_edit_form', array($this, 'taxonomy_edit_meta_field'), 10, 2);
    }
  }

  /**
   * Show meta boxes in taxonomy edit page.
   *
   * @param $term
   */
  public function taxonomy_edit_meta_field( $term ) {
    if ($this->options->current_user_can_view('meta_role')) {
      if (isset($this->options->metas->{$term->taxonomy}->metabox) && $this->options->metas->{$term->taxonomy}->metabox) {
        WDSeometaboxController::display($term, 'taxonomy');
      }
    }
  }

  /**
   * Save meta boxes values for taxonomies.
   *
   * @param $term_id
   * @param $taxonomy
   */
  public function save_taxonomy_meta_fields( $term_id, $taxonomy ) {
    if (is_admin() && $this->options->current_user_can_view('meta_role')) {
      if (isset($this->options->metas->$taxonomy->metabox) && $this->options->metas->$taxonomy->metabox) {
        WDSeometaboxController::save( $term_id, $taxonomy );
      }
    }
  }

  /**
   * Autoupdate Sitemap on post and taxanomy edit.
   */
  public function autoupdate_sitemap() {
    if ($this->options->autoupdate_sitemap) {
      new WD_SEO_XML();
    }
  }

  /**
   * Wordpress init actions.
   */
  public function init() {
    ob_start();
    $this->options = new WD_SEO_Options();
  }

  /**
   * Compile Sitemap.
   */
  public function compile_sitemap() {
    if ( $this->options->sitemap ) {
      foreach ( $this->options->sitemap_files as $sitemap_name => $sitemap_file ) {
        $path = $sitemap_file->path;
        if ( preg_match('~' . preg_quote('/' . $sitemap_name) . '~', $_SERVER['REQUEST_URI']) ) {
          if ( !file_exists($path) ) {
            $sitemap = new WD_SEO_XML();
          }
          if ( isset($sitemap->error) && $sitemap->error ) {
            wp_die(__('The sitemap file was not found.', WD_SEO_PREFIX));
          }
          else {
            header('Content-Type: text/xml');
            readfile($path);
            die;
          }
        }
      }
    }
  }

  /**
   * Set redirects.
   *
   * @return bool
   */
  public function set_redirect() {
//    $protocol = is_ssl() ? 'https:' : 'http:';
//    $domain = $_SERVER['HTTP_HOST'];
//    $request = $_SERVER['REQUEST_URI'];

    global $wp;
    //    $current_url = $protocol . '//' . $domain . $request;
    $current_relative_url = trailingslashit(add_query_arg($_SERVER['QUERY_STRING'], '', trailingslashit($wp->request)));

    $crawlerrors = get_option(WD_SEO_PREFIX . '_crawlerrors');
    $crawlerrors = json_decode($crawlerrors, true);
    if ( $crawlerrors ) {
      foreach ( $crawlerrors as $categories ) {
        foreach ( $categories as $issues ) {
          foreach ( $issues['value'] as $issue ) {
            if ( isset($issue['redirect_url'])
              && $issue['redirect_url']
              && $current_relative_url == trailingslashit($issue['pageUrl']) ) {
              wp_redirect($issue['redirect_url']);
              die;
            }
          }
        }
      }
    }
  }

  /**
   * Set auto crawl interval.
   *
   * @param $schedules
   *
   * @return mixed
   */
  public function autocrawl_interval( $schedules ) {
//    $schedules[WD_SEO_PREFIX . '_autocrawl_interval'] = array(
//      'interval' => isset($this->options->autocrawl_interval) ? 3600 * (int) $this->options->autocrawl_interval : 'daily',
//      'display' => __( 'Auto crawl interval.', WD_SEO_PREFIX ),
//    );
//
//    return $schedules;
  }

  /**
   * Plugin activate.
   *
   * @return mixed
   */
  public function activate() {
//    wp_schedule_event(time(), WD_SEO_PREFIX . '_autocrawl_interval', WD_SEO_PREFIX . '_schedule');
    if (!get_option(WD_SEO_PREFIX . '_initial_version')) {
      add_option(WD_SEO_PREFIX . '_initial_version', WD_SEO_DB_VERSION, '', 'no');
    }
  }

  /**
   * Plugin deactivate.
   *
   * @return mixed
   */
  public function deactivate() {
//    wp_clear_scheduled_hook(WD_SEO_PREFIX . '_schedule');
  }

  public function is_active($free_is_full = true) {
    $initial_version = get_option(WD_SEO_PREFIX . '_initial_version');
    // The second part must be replaced by (version_compare($initial_version, '__version__') === -1) when free version is released.
    return (version_compare('2.0.0', WD_SEO_VERSION, '<=') || $free_is_full && version_compare($initial_version, '1.0.0', '<=')) ? 1 : 0;
  }
}
/**
 * Main instance of WDSeo.
 *
 * @return WDSeo The main instance to prevent the need to use globals.
 */
function WDSeo() {
  return WDSeo::instance();
}

WDSeo();
