<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Class WD_SEO_Options.
 */
class WD_SEO_Options {
  /**
   * @var string wordpress standart roles administrator/subscriber/contributor/author/editor
   *             custom roles
   */
  public $meta_role = 'administrator'; // Show SEO metabox to users with permission.
  public $moz_role = 'administrator'; // Show MOZ metabox to users with permission.
  /**
   * @var int 1/0
   */
  public $redirections = 1; // Default redirection type.
  public $meta = 1; // Meta information optimization.
  public $sitemap = 1; // Generate XML Sitemap.
  public $notify_google = 0; // Notify Google search engine when sitemap updates.
  public $notify_bing = 0; // Notify Bing search engine when sitemap updates.
  public $notify_yandex = 0; // Notify Yandex search engine when sitemap updates.
  public $sitemap_image = 0; // Include image items with the sitemap.
  public $sitemap_stylesheet = 1; // Include stylesheet with the sitemap.
  public $autoupdate_sitemap = 0; // Update sitemap on posts/pages edit/delete.
  /**
   * @var string
   */
  public $moz_access_id = ''; // MOZ account access id.
  public $moz_secret_id = ''; // MOZ account secret key.
  public $bing_verification = ''; // Bing Site Verification Code.
  public $yandex_verification = ''; // Yandex Site Verification Code.
  public $exclude_posts = ''; // Comma separated string with the Post ID's.
  /**
   * @var array
   */
  public $exclude_post_types = array(); // Excluded post types array().
  public $exclude_taxonomies = array(); // Excluded taxonomies array().
  /**
   * @var string special pages home/search/404
   *             wordpress standart post types post/page
   *             custom post types
   *             wordpress standart taxanomies category/post tag
   *             custom taxanomies
   *             archives author_archive/date_archive
   */
  public $types = 'home'; // Page types.
  /**
   * @var string How frequently the page is likely to change. always/hourly/daily/weekly/monthly/yearly/never
   */
  public $changefreq = 'weekly';
  /**
   * @var int Maximum number of entries per sitemap page.
   */
  public $limit = 1000;
  /**
   * @var int Sitemap items count.
   */
  public $sitemap_items_count = 0;
  /**
   * @var array Sitemap last modified date and time.
   */
  public $sitemap_last_modified = array();
  /**
   * @var array Created Sitemap files.
   */
  public $sitemap_files = array();
  /**
   * @var string Access token for Google search console.
   */
  public $access_token = array();
  /**
   * @var string Google site verification meta.
   */
  public $google_site_verification = '';
  /**
   * @var int Auto crawl interval (day).
   */
  public $autocrawl_interval = 1;

  /**
   * WD_SEO_Options constructor.
   *
   * @param bool $reset
   */
  public function __construct( $reset = false ) {

    // Add default parameters for metas.
    $this->metas = new stdClass();

    // Get options from db.
    $options = get_option(WD_SEO_PREFIX . '_options');
    if ($options) {
      $options = json_decode($options);
      if (!$reset) {
        if (isset($options)) {
          foreach ($options as $name => $value) {
            $this->$name = $value;
          }
        }
      }
    }

    foreach ( WD_SEO_Library::get_page_types() as $group_type => $group ) {
      foreach ( $group['types'] as $type => $type_arr ) {
        if (!isset($this->metas->$type)) {
          $this->metas->$type = new stdClass();
        }
        if (!in_array('meta_title', $type_arr['exclude_fields']) && !isset($this->metas->$type->meta_title)) {
          $this->metas->$type->meta_title = isset($type_arr['defaults']['meta_title']) ? $type_arr['defaults']['meta_title'] : '';
        }
        if (!in_array('meta_description', $type_arr['exclude_fields']) && !isset($this->metas->$type->meta_description)) {
          $this->metas->$type->meta_description = isset($type_arr['defaults']['meta_description']) ? $type_arr['defaults']['meta_description'] : '';
        }
        if (!in_array('meta_keywords', $type_arr['exclude_fields']) && !isset($this->metas->$type->meta_keywords)) {
          $this->metas->$type->meta_keywords = array();
        }
        if (!in_array('index', $type_arr['exclude_fields']) && !isset($this->metas->$type->index)) {
          $this->metas->$type->index = 1;
        }
        if (!in_array('follow', $type_arr['exclude_fields']) && !isset($this->metas->$type->follow)) {
          $this->metas->$type->follow = 1;
        }
        if (!in_array('date', $type_arr['exclude_fields']) && !isset($this->metas->$type->date)) {
          $this->metas->$type->date = 1;
        }
        if (!in_array('opengraph', $type_arr['exclude_fields'])) {
          if (!isset($this->metas->$type->opengraph_title)) {
            $this->metas->$type->opengraph_title = isset($type_arr['defaults']['opengraph_title']) ? $type_arr['defaults']['opengraph_title'] : '';
          }
          if (!isset($this->metas->$type->opengraph_description)) {
            $this->metas->$type->opengraph_description = isset($type_arr['defaults']['opengraph_description']) ? $type_arr['defaults']['opengraph_description'] : '';
          }
          if (!isset($this->metas->$type->opengraph_images)) {
            $this->metas->$type->opengraph_images = '';
          }
          if (!isset($this->metas->$type->use_og_for_twitter)) {
            $this->metas->$type->use_og_for_twitter = 1;
          }
          if (!isset($this->metas->$type->twitter_title)) {
            $this->metas->$type->twitter_title = isset($type_arr['defaults']['twitter_title']) ? $type_arr['defaults']['twitter_title'] : '';
          }
          if (!isset($this->metas->$type->twitter_description)) {
            $this->metas->$type->twitter_description =isset($type_arr['defaults']['twitter_description']) ? $type_arr['defaults']['twitter_description'] :  '';
          }
          if (!isset($this->metas->$type->twitter_images)) {
            $this->metas->$type->twitter_images = '';
          }
        }
        if (!in_array('metabox', $type_arr['exclude_fields']) && !isset($this->metas->$type->metabox)) {
          $this->metas->$type->metabox = 1;
        }
        if (!in_array('robots_advanced', $type_arr['exclude_fields']) && !isset($this->metas->$type->robots_advanced)) {
          $this->metas->$type->robots_advanced = array();
        }
      }
    }
  }

  /**
   * Return sitemap PATH and URL.
   *
   * @return array
   */
  public function get_sitemap_dir() {
    $upload_dir = wp_upload_dir();
    $sitemap_folder = $upload_dir['basedir'] . '/' . WD_SEO_PREFIX . '_sitemaps';

    $path = wp_normalize_path( trailingslashit( $sitemap_folder ) );
    $url = trailingslashit( get_bloginfo( 'url' ) );
    $name = 'sitemap.xml';
    $index_name = 'index-' . $name;

    return array(
      'path' => $path,
      'url' => $url,
      'name' => $name,
      'index_name' => $index_name,
    );
  }

  /**
   * Current user can view section.
   *
   * @param $section one of following (meta_role, moz_role)
   * @return bool
   */
  public function current_user_can_view($section) {
    $user_role_capabilities = array(
      'subscriber' => 'read',
      'contributor' => 'edit_posts',
      'author' => 'publish_posts',
      'editor' => 'edit_pages',
      'administrator' => 'manage_options',
    );
    $capability = isset($user_role_capabilities[$this->$section]) ? $user_role_capabilities[$this->$section] : $this->$section;
    return current_user_can($capability);
  }
}
