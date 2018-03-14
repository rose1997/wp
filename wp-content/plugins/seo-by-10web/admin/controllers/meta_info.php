<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Meta information controller class.
 */
class WDSeometa_infoController extends WDSeoAdminController {
  /**
   * Display.
   */
  public function display() {
    // Get the page view if exist or global view otherwise.
    $view_class = class_exists($this->class_prefix . 'View') ? $this->class_prefix . 'View' : WD_SEO_CLASS_PREFIX . 'AdminView';
    $view = new $view_class();

    echo $view->display($this->options, WD_SEO_Library::get_page_types());
  }
}
