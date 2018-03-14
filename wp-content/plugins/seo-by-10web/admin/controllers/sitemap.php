<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Meta information controller class.
 */
class WDSeositemapController extends WDSeoAdminController {
  /**
   * Display.
   */
  public function display() {
    // Get the page view if exist or global view otherwise.
    $view_class = class_exists($this->class_prefix . 'View') ? $this->class_prefix . 'View' : WD_SEO_CLASS_PREFIX . 'AdminView';
    $view = new $view_class();

    $this->options->google_verification_msg = ($this->options->google_site_verification == '' ? sprintf(__('You should %s with Google to verify your site.', WD_SEO_PREFIX), '<a target="_blank" href="' . add_query_arg(array('page' => WD_SEO_PREFIX . '_overview'), admin_url('admin.php')) . '">' . __('authenticate', WD_SEO_PREFIX) . '</a>') : __('Google site verification has already been done.', WD_SEO_PREFIX));

    echo $view->display($this->options, WD_SEO_Library::get_post_types(), WD_SEO_Library::get_taxanomies());
  }

  /**
   * Save current options and generate Sitemap.
   */
  public function save() {
    $message_id = $this->model->store();

    // Generate Sitemap.
    new WD_SEO_XML();

    wp_redirect(add_query_arg(array('msg' => $message_id), $this->current_url));
    exit;
  }

  /**
   * Generate Sitemap XML.
   */
  public function update_sitemap() {
    // Generate Sitemap.
    $sitemap = new WD_SEO_XML();
    $message_id = $sitemap->error ? 5 : 4;
    wp_redirect(add_query_arg(array('msg' => $message_id), $this->current_url));
    exit;
  }

  /**
   * Remove Sitemaps.
   */
  public function delete() {
    $sitemap_dir = $this->options->get_sitemap_dir();
    WD_SEO_Library::remove_directory($sitemap_dir['path']);
    if ( !file_exists($sitemap_dir['path']) ) {
      $this->options->sitemap_last_modified = array(
        'date' => current_time(get_option('date_format')),
        'time' => current_time(get_option('time_format')),
      );
      $this->options->sitemap_items_count = -1;
      update_option(WD_SEO_PREFIX . '_options', json_encode($this->options), 'no');
      $message_id = 7;
    }
    else {
      $message_id = 5;
    }
    wp_redirect(add_query_arg(array('msg' => $message_id), $this->current_url));
    exit;
  }
}
