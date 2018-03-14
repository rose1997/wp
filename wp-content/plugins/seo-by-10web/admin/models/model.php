<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Admin model class.
 */
class WDSeoAdminModel {
  /**
   * Store current options in DB.
   *
   * @return int Message id.
   */
  public function store($settings = FALSE) {
    if (!$settings) {
      $settings = WD_SEO_Library::get('wd_settings');
    }
    $options = WDSeo()->options;
    $initial_options = $options;
    foreach ( $settings as $key => $setting ) {
      $options->$key = $setting;
    }
    $save = update_option(WD_SEO_PREFIX . '_options', json_encode($options), 'no');

    return $save || ($initial_options === $options) ? 1 : 2;
  }

  /**
   * Delete plugin data from DB and delete Sitemap XMLs.
   */
  public function uninstall() {
    $options = WDSeo()->options;
    $sitemap_dir = $options->get_sitemap_dir();
    if ( is_dir($sitemap_dir['path']) ) {
      WD_SEO_Library::remove_directory($sitemap_dir['path']);
    }
    delete_option(WD_SEO_PREFIX . '_initial_version');
    delete_option(WD_SEO_PREFIX . '_options');
    delete_option(WD_SEO_PREFIX . '_crawlerrors');
    delete_option(WD_SEO_PREFIX . '_disabled_notices');
    global $wpdb;
    $wpdb->query('DELETE FROM ' . $wpdb->prefix . 'postmeta WHERE meta_key LIKE "' . WD_SEO_PREFIX . '_%"');
    $wpdb->query('DELETE FROM ' . $wpdb->prefix . 'termmeta WHERE meta_key LIKE "' . WD_SEO_PREFIX . '_%"');
    /*WD_SEO_Library::remove_cron();*/ // ToDo: Remove comments when seo service is ready.
  }
}
