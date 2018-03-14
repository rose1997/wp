<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Overview controller class.
 */
class WDSeooverviewController extends WDSeoAdminController {
  /**
   * @var WD_SEO_CRAWL
   */
  private $crawl;
  /**
   * @var WD_SEO_MOZ
   */
  private $moz;

  public function __construct($page = null, $task = null) {
    $this->crawl = new WD_SEO_CRAWL;
    $this->moz = new WD_SEO_MOZ;
    parent::__construct($page, $task);
  }

  /**
   * Display.
   *
   * @param null $crawl_errors
   */
  public function display($crawl_errors = NULL) {
    // Get the page view if exist or global view otherwise.
    $view_class = class_exists($this->class_prefix . 'View') ? $this->class_prefix . 'View' : WD_SEO_CLASS_PREFIX . 'AdminView';
    $view = new $view_class();

    if ( $crawl_errors === NULL ) {
      $crawl_errors = $this->crawl->get_crawl_errors();
    }

    // Get recommendations and problems notices.
    $notices = WD_SEO_Library::get_recommends_problems();

	  echo $view->display($this->options, $this->crawl->authorization_url(), $crawl_errors, $this->moz->get_url_metrics(), $notices);
  }

  /**
   * Authenticate.
   */
  public function authenticate() {
    $this->display();
    $overview_page_url = add_query_arg(array('page' => WD_SEO_PREFIX . '_overview'), admin_url('admin.php'));
    wp_redirect($overview_page_url);
    exit;
  }

  /**
   * Reauthenticate with google.
   */
  public function reauthenticate() {
    $this->crawl->reauthenticate();
    $overview_page_url = add_query_arg(array('page' => WD_SEO_PREFIX . '_overview'), admin_url('admin.php'));
    wp_redirect($overview_page_url);
    exit;
  }

  /**
   * Deactivate the given plugin.
   */
  public function deactivate() {
    $message_id = 5;

    // Get plugin to deactivate.
    $plugin = WD_SEO_Library::get('plugin', '');
    if ( $plugin ) {
      $plugins = get_plugins();
      if ( !empty($plugins) ) {
        foreach ( $plugins as $key => $val ) {
          if ( $val['TextDomain'] == $plugin
            && ( in_array($val['TextDomain'], WD_SEO_Library::$seo_plugins) || in_array($val['TextDomain'], WD_SEO_Library::$analytics_plugins) )
            && is_plugin_active($key) ) {
            deactivate_plugins($key);
            $this->dismiss($val['TextDomain']);
            $message_id = 6;
          }
        }
      }
    }

    wp_redirect(add_query_arg(array('msg' => $message_id), $this->current_url));
    exit;
  }

  /**
   * Dismiss notification.
   */
  public function dismiss($key = '') {
    if ( $key == '' ) {
      // If not after deactivation.
      $key = WD_SEO_Library::get('key', '');
    }

    if ( $key ) {
      $option_name = WD_SEO_PREFIX . '_disabled_notices';
      $option = get_option($option_name);
      if ( $option ) {
        $notices = json_decode($option, TRUE);
      }
      else {
        $notices = array();
      }

      $notices[$key] = array(
        'update_time' => time(),
      );
      $option = json_encode($notices);
      update_option($option_name, $option, 'no');
    }
  }

  /**
   * Authenticate/Reauthenticate with MOZ.
   */
  public function save() {
    $url = $this->current_url;
    $message_id = $this->model->store();
    if ( $message_id == 2 ) {
      $url = add_query_arg(array('msg' => $message_id), $url);
    }

    wp_redirect($url);
    exit;
  }
}
