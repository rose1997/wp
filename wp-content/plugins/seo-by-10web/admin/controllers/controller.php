<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Admin controller class.
 */
class WDSeoAdminController {
  /**
   * Action to do.
   */
  protected $task;
  /**
   * Current page slug.
   */
  protected $page;
  /**
   * Class prefix.
   */
  protected $class_prefix;
  /**
   * Current page url.
   */
  protected $current_url;
  /**
   * Otions.
   */
  protected $options;
  /**
   * Model.
   */
  protected $model;

  /**
   * WDSeoAdminController constructor.
   *
   * @param null $page
   * @param null $task
   */
  public function __construct($page = null, $task = null) {
    if ($page === null) {
      return;
    }
    $this->page = $page;

    $this->current_url = add_query_arg(array('page' => $this->page), admin_url('admin.php'));

    if ($task === null) {
      $task = WD_SEO_Library::get('task', 'display');
    }
    $this->task = $task;

    // Check nonce on actions.
    if ($task != 'display') {
      check_admin_referer(WD_SEO_NONCE, WD_SEO_NONCE);
    }

    // Change menu slug to class name.
    $this->class_prefix = str_replace(WD_SEO_PREFIX . '_', WD_SEO_CLASS_PREFIX, $page);

    $msg = WD_SEO_Library::get('msg');

    // Get options.
    $this->options = $msg == 3 ? new WD_SEO_Options(true) : WDSeo()->options;

    // Get the page model if exist or global model otherwise.
    $model_class = class_exists($this->class_prefix . 'Model') ? $this->class_prefix . 'Model' : WD_SEO_CLASS_PREFIX . 'AdminModel';
    $this->model = new $model_class();

    if ( method_exists($this, $task) ) {
      $this->$task();
    }
  }

  /**
   * Display.
   */
  public function display() {
    // Get the page view if exist or global view otherwise.
    $view_class = class_exists($this->class_prefix . 'View') ? $this->class_prefix . 'View' : WD_SEO_CLASS_PREFIX . 'AdminView';
    $view = new $view_class();

    echo $view->display($this->options);
  }

  /**
   * Save current options.
   */
  public function save() {
    $message_id = $this->model->store();
    wp_redirect(add_query_arg(array('msg' => $message_id), $this->current_url));
    exit;
  }

  /**
   * Uninstall plugin.
   */
  public function uninstall() {
    $uninstall_status = WD_SEO_Library::get('uninstall_status');

    if ( $uninstall_status == '1' ) {
      $this->model->uninstall();
    }
    $view_class = $this->class_prefix . 'View';
    $view = new $view_class;
    echo $view->display($uninstall_status);
  }


  /**
   * Reset.
   */
  public function reset() {
    $message_id = 3;
    wp_redirect(add_query_arg(array('msg' => $message_id), $this->current_url));
    exit;
  }

  /**
   * Cancel.
   */
  public function cancel() {
    wp_redirect($this->current_url);
    exit;
  }
}
