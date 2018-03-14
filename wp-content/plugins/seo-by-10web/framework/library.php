<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Library class.
 */
class WD_SEO_Library {
  /**
   * @var array SEO and Analytics plugins recommend to deactivate.
   */
  public static $seo_plugins = array( 'wordpress-seo', 'all-in-one-seo-pack' );
  public static $analytics_plugins = array( 'google-analytics-for-wordpress', 'google-analytics-dashboard-for-wp' );
  /**
   * Get request value.
   *
   * @param string $key
   * @param string $default_value
   * @param bool $esc_html
   *
   * @return string|array
   */
  public static function get($key, $default_value = '', $esc_html = true) {
    if (isset($_GET[$key])) {
      $value = $_GET[$key];
    }
    elseif (isset($_POST[$key])) {
      $value = $_POST[$key];
    }
    elseif (isset($_REQUEST[$key])) {
      $value = $_POST[$key];
    }
    else {
      $value = '';
    }
    if (!$value) {
      $value = $default_value;
    }
    if (is_array($value)) {
      array_walk_recursive($value, array('self', 'validate_data'), $esc_html);
    }
    else {
      self::validate_data($value, $esc_html);
    }
    return $value;
  }

  /**
   * Validate data.
   *
   * @param $value
   * @param $esc_html
   */
  private static function validate_data(&$value, $esc_html) {
    $value = stripslashes($value);
    if ($esc_html) {
      $value = esc_html($value);
    }
  }

  /**
   * Verify nonce for given page.
   *
   * @param string $page
   */
  public static function verify_nonce( $page ) {
    $nonce_verified = FALSE;
    if ( isset($_GET[WD_SEO_NONCE]) && wp_verify_nonce($_GET[WD_SEO_NONCE], $page) ) {
      $nonce_verified = TRUE;
    }
    if ( !$nonce_verified ) {
      die(__('Sorry, your nonce did not verify.', WD_SEO_PREFIX));
    }
  }

  /**
   * Require php files in specified folder.
   *
   * @param string $dir_path
   */
  public static function require_dir($dir_path) {
    $files = scandir($dir_path);
    foreach ($files as $file) {
      if (($file == '.') || ($file == '..')) {
        continue;
      }
      $file = $dir_path . '/' . $file;

      if (is_dir($file) == TRUE) {
        self::require_dir($file);
      }
      else {
        if ((is_file($file) == TRUE)
          && (pathinfo($file, PATHINFO_EXTENSION) == 'php')) {
          require_once wp_normalize_path( $file );
        }
      }
    }
  }

  /**
   * Get special pages.
   *
   * @return array.
   */
  public static function get_special_pages() {
    $options = array(
      'home' => array(
        'name' => __('Homepage', WD_SEO_PREFIX),
        'exclude_fields' => array('date', 'metabox'),
        'defaults' => array(
          'meta_title' => '%%sitename%%',
          'meta_description' => '%%sitedesc%%',
          'opengraph_title' => '%%sitename%%',
          'opengraph_description' => '%%sitedesc%%',
          'twitter_title' => '%%sitename%%',
          'twitter_description' => '%%sitedesc%%',
        ),
      ),
      'search' => array(
        'name' => __('Search page', WD_SEO_PREFIX),
        'exclude_fields' => array('meta_keywords', 'index', 'follow', 'date', 'robots_advanced', 'metabox', 'opengraph'),
        'defaults' => array(
          'meta_title' => '%%sitename%%',
          'meta_description' => '%%searchphrase%%',
        ),
      ),
      '404' => array(
        'name' => __('404 page', WD_SEO_PREFIX),
        'exclude_fields' => array('meta_keywords', 'index', 'follow', 'date', 'robots_advanced', 'metabox', 'opengraph'),
        'defaults' => array(
          'meta_title' => '%%sitename%%',
          'meta_description' => __('Page not found.', WD_SEO_PREFIX),
        ),
      ),
    );
    return $options;
  }

  /**
   * Get post types.
   *
   * @return array.
   */
  public static function get_post_types() {
    $post_types = get_post_types(array(
                                   'public' => TRUE,
                                   'show_ui' => TRUE,
                                 ));
    $exclude_types = array('revision', 'nav_menu_item', 'attachment');
    foreach ( $post_types as $post_type ) {
      if ( in_array($post_type, $exclude_types) ) {
        continue;
      }
      $options[$post_type] = array();
      $obj = get_post_type_object($post_type);
      $options[$post_type]['name'] = $obj->label;
      $options[$post_type]['exclude_fields'] = array();
      $options[$post_type]['defaults'] = array(
        'meta_title' => '%%title%%',
        'meta_description' => '%%excerpt%%',
        'opengraph_title' => '%%title%%',
        'opengraph_description' => '%%excerpt%%',
        'twitter_title' => '%%title%%',
        'twitter_description' => '%%excerpt%%',
      );
    }

    return $options;
  }

  /**
   * Get taxanomies.
   *
   * @return array.
   */
  public static function get_taxanomies() {
    $options = array();
    $taxanomies = get_taxonomies(array(
                                   'public' => true,
                                   'show_ui' => true,
                                 ));
    $exclude_taxanomies = array('nav_menu', 'link_category', 'post_format');
    foreach ( $taxanomies as $taxonomy ) {
      if ( in_array($taxonomy, $exclude_taxanomies) ) {
        continue;
      }
      $options[$taxonomy] = array();
      $obj = get_taxonomy($taxonomy);
      $options[$taxonomy]['name'] = $obj->label;
      $options[$taxonomy]['exclude_fields'] = array('date');
      $options[$taxonomy]['defaults'] = array(
        'meta_title' => '%%term_title%%',
        'meta_description' => '%%term_description%%',
        'opengraph_title' => '%%term_title%%',
        'opengraph_description' => '%%term_description%%',
        'twitter_title' => '%%term_title%%',
        'twitter_description' => '%%term_description%%',
      );
    }

    return $options;
  }

  /**
   * Get archives.
   *
   * @return array.
   */
  public static function get_archives() {
    $options = array(
      'author_archive' => array(
        'name' => __('Author archive', WD_SEO_PREFIX),
        'description' => __('Author archives could in some cases be seen as duplicate content. So you must manually to add noindex,follow to it so it doesn\'t show up in the search results.', WD_SEO_PREFIX),
        'exclude_fields' => array('date', 'metabox'),
        'defaults' => array(
          'meta_title' => '%%name%%',
          'meta_description' => '%%name%%\'s posts',
          'opengraph_title' => '%%name%%',
          'opengraph_description' => '%%name%%\'s posts',
          'twitter_title' => '%%name%%',
          'twitter_description' => '%%name%%\'s posts',
        ),
      ),
      'date_archive' => array(
        'name' => __('Date archive', WD_SEO_PREFIX),
        'description' => __('Date archives could in some cases be seen as duplicate content. So you must manually to add noindex,follow to it so it doesn\'t show up in the search results.', WD_SEO_PREFIX),
        'exclude_fields' => array('date', 'metabox'),
        'defaults' => array(
          'meta_title' => '%%currentdate%%',
          'meta_description' => 'Posts of %%currentdate%%',
          'opengraph_title' => '%%currentdate%%',
          'opengraph_description' => 'Posts of %%currentdate%%',
          'twitter_title' => '%%currentdate%%',
          'twitter_description' => 'Posts of %%currentdate%%',
        ),
      ),
    );

    return $options;
  }

  /**
   * Get all types of pages.
   *
   * @return array.
   */
  public static function get_page_types() {
    return array(
      'special_pages' => array(
        'title' => __('Special pages', WD_SEO_PREFIX),
        'types' => WD_SEO_Library::get_special_pages(),
      ),
      'post_types' => array(
        'title' => __('Post types', WD_SEO_PREFIX),
        'types' => WD_SEO_Library::get_post_types(),
      ),
      'taxanomies' => array(
        'title' => __('Taxanomies', WD_SEO_PREFIX),
        'types' => WD_SEO_Library::get_taxanomies(),
      ),
      'archives' => array(
        'title' => __('Archives', WD_SEO_PREFIX),
        'types' => WD_SEO_Library::get_archives(),
      ),
    );
  }

  /**
   * Generate placeholder container template.
   *
   * @return string
   */
  public static function placeholder_template() {
    ob_start();
    ?>
    <div class="wd-placeholder-cont-template">
      <div class="wd-placeholder">
        <?php
        foreach (WD_SEO_Library::get_placeholders() as $item => $label) {
          ?>
        <div data-value="<?php echo esc_attr($item); ?>"
             title="<?php _e('Click to insert', WD_SEO_PREFIX); ?>">
          <?php echo esc_html($label); ?>
        </div>
          <?php
        }
        ?>
      </div>
      <span class="wd-placeholder-btn button-primary"><?php _e('Insert placeholder', WD_SEO_PREFIX); ?></span>
    </div>
    <?php
    return ob_get_clean();
  }

  /**
   * Return placeholders.
   *
   * @return array
   */
  public static function get_placeholders($get_values = false) {
    $options = array(
      "%%date%%" => $get_values ? '' : __('Date of the post/page', WD_SEO_PREFIX),
      "%%title%%" => $get_values ? '' : __('Title of the post/page', WD_SEO_PREFIX),
      "%%sitename%%" => $get_values ? '' : __('Site\'s name', WD_SEO_PREFIX),
      "%%sitedesc%%" => $get_values ? '' : __('Site\'s tagline / description', WD_SEO_PREFIX),
      "%%excerpt%%" => $get_values ? '' : __('Post/page excerpt (or auto-generated if it does not exist)', WD_SEO_PREFIX),
      "%%excerpt_only%%" => $get_values ? '' : __('Post/page excerpt (without auto-generation)', WD_SEO_PREFIX),
      "%%tag%%" => $get_values ? '' : __('Current tag/tags', WD_SEO_PREFIX),
      "%%category%%" => $get_values ? '' : __('Post categories (comma separated)', WD_SEO_PREFIX),
      "%%category_description%%" => $get_values ? '' : __('Category description', WD_SEO_PREFIX),
      "%%tag_description%%" => $get_values ? '' : __('Tag description', WD_SEO_PREFIX),
      "%%term_description%%" => $get_values ? '' : __('Term description', WD_SEO_PREFIX),
      "%%term_title%%" => $get_values ? '' : __('Term name', WD_SEO_PREFIX),
      "%%modified%%" => $get_values ? '' : __('Post/page modified time', WD_SEO_PREFIX),
      "%%id%%" => $get_values ? '' : __('Post/page ID', WD_SEO_PREFIX),
      "%%name%%" => $get_values ? '' : __('Post/page author\'s \'nicename\'', WD_SEO_PREFIX),
      "%%userid%%" => $get_values ? '' : __('Post/page author\'s userid', WD_SEO_PREFIX),
      "%%searchphrase%%" => $get_values ? '' : __('Current search phrase', WD_SEO_PREFIX),
      "%%currenttime%%" => $get_values ? '' : __('Current time', WD_SEO_PREFIX),
      "%%currentdate%%" => $get_values ? '' : __('Current date', WD_SEO_PREFIX),
      "%%currentmonth%%" => $get_values ? '' : __('Current month', WD_SEO_PREFIX),
      "%%currentyear%%" => $get_values ? '' : __('Current year', WD_SEO_PREFIX),
      "%%page%%" => $get_values ? '' : __('Current page number (i.e. page 2 of 4)', WD_SEO_PREFIX),
      "%%pagetotal%%" => $get_values ? '' : __('Current page total', WD_SEO_PREFIX),
      "%%pagenumber%%" => $get_values ? '' : __('Current page number', WD_SEO_PREFIX),
      "%%caption%%" => $get_values ? '' : __('Attachment caption', WD_SEO_PREFIX),
    );
    if ($get_values) {
      $screen = is_admin() ? get_current_screen() : get_queried_object();
      $is_post = is_admin() ? !$screen->taxonomy && $screen->post_type : $screen && 'WP_Post' == get_class($screen);
      $is_taxonomy = is_admin() ? $screen->taxonomy && !$screen->post_type : $screen && 'WP_Term' == get_class($screen);

      global $wp_query;
      $date_format = get_option("date_format");
      $pagenum = get_query_var('paged');
      if ($pagenum === 0) {
        $pagenum = ($wp_query->max_num_pages > 1) ? 1 : '';
      }

      $options['%%sitename%%'] = get_bloginfo("name");
      $options['%%sitedesc%%'] = get_bloginfo("description");
      $options['%%searchphrase%%'] = esc_html(get_query_var('s'));
      $options['%%currenttime%%'] = date('H:i');
      $options['%%currentdate%%'] = date($date_format);
      $options['%%currentmonth%%'] = date('F');
      $options['%%currentyear%%'] = date('Y');
      $options['%%page%%'] = (get_query_var('paged') != 0) ? 'Page ' . get_query_var('paged') . ' of ' . $wp_query->max_num_pages : '';
      $options['%%pagetotal%%'] = ($wp_query->max_num_pages > 1) ? $wp_query->max_num_pages : '';
      $options['%%pagenumber%%'] = $pagenum;

      if ( $is_post ) {
        global $post;
        if (!empty( $post )) {
          $posttags = get_the_tags();
          $posttags_names = array();
          if ($posttags) {
            foreach ($posttags as $tag) {
              $posttags_names[] = $tag->name;
            }
          }
          $posttags = implode(', ', $posttags_names);

          $postcategories = get_the_category();
          $postcategories_names = array();
          if ($postcategories) {
            foreach ($postcategories as $category) {
              $postcategories_names[] = $category->name;
            }
          }
          $postcategories = implode(', ', $postcategories_names);

          $author_id = !empty($post->post_author) ? $post->post_author : get_query_var('author');
          $author_name = get_the_author_meta('display_name', $author_id);
          $options['%%date%%'] = mysql2date($date_format, $post->post_date);
          $options['%%title%%'] = $post->post_title;
          $options['%%excerpt%%'] = $post->post_excerpt ? $post->post_excerpt : wp_strip_all_tags(strip_shortcodes($post->post_content));
          $options['%%excerpt_only%%'] = $post->post_excerpt;
          $options['%%tag%%'] = $posttags;
          $options['%%category%%'] = $postcategories;
          $options['%%modified%%'] = mysql2date($date_format, $post->post_modified);
          $options['%%id%%'] = $post->ID;
          $options['%%name%%'] = $author_name;
          $options['%%userid%%'] = $author_id;
          $options['%%caption%%'] = $post->post_excerpt;
        }
      }
      else if ( $is_taxonomy ) {
        $term_id = isset( $_REQUEST['tag_ID'] ) ? (int) $_REQUEST['tag_ID'] : $screen->term_id;
        if ($term_id) {
          $term = get_term($term_id, $screen->taxonomy);
          if (!is_wp_error($term)) {
            $options['%%term_title%%'] = $term->name;
            $options['%%term_description%%'] = $term->description;
            $options['%%category%%'] = $term->name;
            $options['%%category_description%%'] = $term->description;
            $options['%%tag%%'] = $term->name;
            $options['%%tag_description%%'] = $term->description;
          }
        }
      }
    }

    return $options;
  }

  /**
   * Replace placeholders with values.
   *
   * @param $string
   * @param $placeholders
   *
   * @return mixed
   */
  public static function replace_placeholders( $string, $placeholders ) {
    foreach ( $placeholders as $var => $ph ) {
      $string = str_replace($var, $ph, $string);
    }

    return $string;
  }

  /**
   *
   *
   * @param $text
   * @param int $length
   * @param string $ending
   * @param bool $exact
   * @param bool $considerHtml
   *
   * @return bool|string
   */
  public static function truncate_html($text, $length = 100, $ending = '', $exact = false, $considerHtml = true) {
    if ( $considerHtml ) {
      // If the plain text is shorter than the maximum length, return the whole text
      if ( strlen(preg_replace('/<.*?>/', '', $text)) <= $length ) {
        return $text;
      }
      // Splits all html-tags to scanable lines.
      preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
      $total_length = strlen($ending);
      $open_tags = array();
      $truncate = '';
      foreach ( $lines as $line_matchings ) {
        // If there is any html-tag in this line, handle it and add it (uncounted) to the output.
        if ( !empty($line_matchings[1]) ) {
          // If it's an "empty element" with or without xhtml-conform closing slash.
          if ( preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1]) ) {
            // Do nothing
            // if tag is a closing tag.
          }
          else if ( preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings) ) {
            // Delete tag from $open_tags list.
            $pos = array_search($tag_matchings[1], $open_tags);
            if ( $pos !== FALSE ) {
              unset($open_tags[$pos]);
            }
            // If tag is an opening tag.
          }
          else {
            if ( preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings) ) {
              // Add tag to the beginning of $open_tags list.
              array_unshift($open_tags, strtolower($tag_matchings[1]));
            }
          }
          // Add html-tag to $truncate'd text.
          $truncate .= $line_matchings[1];
        }
        // Calculate the length of the plain text part of the line; handle entities as one character.
        $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
        if ( $total_length + $content_length > $length ) {
          // The number of characters which are left.
          $left = $length - $total_length;
          $entities_length = 0;
          // search for html entities
          if ( preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE) ) {
            // Calculate the real length of all entities in the legal range.
            foreach ( $entities[0] as $entity ) {
              if ( $entity[1] + 1 - $entities_length <= $left ) {
                $left--;
                $entities_length += strlen($entity[0]);
              }
              else {
                // No more characters left.
                break;
              }
            }
          }
          $truncate .= substr($line_matchings[2], 0, $left+$entities_length);
          // Maximum lenght is reached, so get off the loop.
          break;
        }
        else {
          $truncate .= $line_matchings[2];
          $total_length += $content_length;
        }
        // If the maximum length is reached, get off the loop.
        if ( $total_length >= $length ) {
          break;
        }
      }
    }
    else {
      if ( strlen($text) <= $length ) {
        return $text;
      }
      else {
        $truncate = substr($text, 0, $length - strlen($ending));
      }
    }
    // if the words shouldn't be cut in the middle...
    if ( !$exact ) {
      // ...search the last occurance of a space...
      $spacepos = strrpos($truncate, ' ');
      if (isset($spacepos)) {
        // ...and cut the text in this position
        //$truncate = substr($truncate, 0, $spacepos);
        $truncate = join("", array_slice( preg_split("//u", $truncate, -1, PREG_SPLIT_NO_EMPTY), 0, $spacepos));
      }
    }
    // Add the defined ending to the text.
    $truncate .= $ending;
    if ( $considerHtml ) {
      // Close all unclosed html-tags.
      foreach ( $open_tags as $tag ) {
        $truncate .= '</' . $tag . '>';
      }
    }

    return $truncate;
  }

  /**
   * Get recommends and problems list.
   */
  public static function get_recommends_problems($for_count = FALSE) {
    $data = array();

    // Get all plugins.
    if ( ! function_exists( 'get_plugins' ) ) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $plugins = get_plugins();

    if ( !empty($plugins) ) {
      $data['recommends'] = array();
      $link = add_query_arg(array( 'page' => 'wdseo_overview' ), admin_url('admin.php'));

      // Get disabled notices.
      $option_name = WD_SEO_PREFIX . '_disabled_notices';
      $option = get_option($option_name);
      if ( $option ) {
        $disabled_notices = json_decode($option, TRUE);
      }
      else {
        $disabled_notices = array();
      }

      foreach ( $plugins as $key => $val ) {
        if ( is_plugin_active($key) ) {
          // Check SEO and Analytics plugins.
          if ( (empty($disabled_notices) || !array_key_exists($val['TextDomain'], $disabled_notices) )
            && (in_array($val['TextDomain'], WD_SEO_Library::$seo_plugins) || in_array($val['TextDomain'], WD_SEO_Library::$analytics_plugins)) ) {
            if ( !$for_count ) {
              $link = add_query_arg(array( 'task' => 'deactivate', 'plugin' => $val['TextDomain'] ), $link);
              $link = wp_nonce_url($link, WD_SEO_NONCE, WD_SEO_NONCE);
            }
            if ( in_array($val['TextDomain'], WD_SEO_Library::$seo_plugins) ) {
              $message = sprintf(__('Please note that having more than one SEO plugin activated can be misleading for Search Engines, therefore we recommend deactivating other such plugins. %s %s plugin.', WD_SEO_PREFIX), '<a href="' . $link . '">' . __('Deactivate', WD_SEO_PREFIX) . '</a>', $val['Name']);
            }
            elseif ( in_array($val['TextDomain'], WD_SEO_Library::$analytics_plugins) ) {
              $message = sprintf(__('To avoid any possible conflict with %s, we recommend to deactivate any analytics plugins you have installed on your WordPress. You can use the Google Analytics WD instead, which has been fully tested with this plugin. %s %s plugin.', WD_SEO_PREFIX), WD_SEO_NICENAME, '<a href="' . $link . '">' . __('Deactivate', WD_SEO_PREFIX) . '</a>', $val['Name']);
            }
            $plugin_info = array(
              'key' => $val['TextDomain'],
              'domain' => $val['TextDomain'],
              'name' => $val['Name'],
              'message' => $message,
              'link' => $link,
            );

            $data['recommends']['plugins'][] = $plugin_info;
          }
        }
      }
    }

    // Tagline setting.
    $blog_description = get_bloginfo('description');
    if ( $blog_description == __('Just another WordPress site')
      || $blog_description == 'Just another WordPress site' ) {
      $message = sprintf(__('Please note that you need to %s to have your website recognized by search engines.', WD_SEO_PREFIX), '<a target="_blank" href="' . admin_url('/options-general.php') . '">' . __('change the default tagline', WD_SEO_PREFIX) . '</a>');
      $data['problems']['general_settings']['tagline'] = array(
        'key' => WD_SEO_PREFIX . '_tagline',
        'name' => __('Tagline', WD_SEO_PREFIX),
        'message' => $message,
        'link' => admin_url('/options-general.php'),
      );
    }

    // Permalink setting.
    if ( strpos(get_option('permalink_structure'), '/%postname%/') === FALSE ) {
      $message = sprintf(__('Please note that you need to include your post name in the %s to make it more descriptive.', WD_SEO_PREFIX), '<a target="_blank" href="' . admin_url('/options-permalink.php') . '">' . __('permalink', WD_SEO_PREFIX) . '</a>');
      $data['problems']['options_permalink']['postname'] = array(
        'key' => WD_SEO_PREFIX . '_postname',
        'name' => __('Permalinks', WD_SEO_PREFIX),
        'message' => $message,
        'link' => admin_url('/options-permalink.php'),
      );
    }

    // Reading setting.
    if ( get_option('blog_public') == 0 ) {
      $message = sprintf(__('Your blog is not publicly visible. Go to %s and uncheck the “Discourage search engines from indexing this site" option.', WD_SEO_PREFIX), '<a target="_blank" href="' . admin_url('/options-reading.php') . '">' . __('Settings->Reading->Search Engine Visibility', WD_SEO_PREFIX) . '</a>');
      $data['problems']['reading_settings']['postname'] = array(
        'key' => WD_SEO_PREFIX . '_blog_public',
        'name' => __('Search Engine Visibility', WD_SEO_PREFIX),
        'message' => $message,
        'link' => admin_url('/options-reading.php'),
      );
    }

    // Discussion setting.
    if ( get_option('page_comments') == 1 ) {
      $message = sprintf(__('To avoid issues associated with duplicate content, you are recommended to disable %s.', WD_SEO_PREFIX), '<a target="_blank" href="' . admin_url('/options-discussion.php') . '">' . __('comment pagination', WD_SEO_PREFIX) . '</a>');
      $data['problems']['discussion_settings']['postname'] = array(
        'key' => WD_SEO_PREFIX . '_page_comments',
        'name' => __('Break comments into pages', WD_SEO_PREFIX),
        'message' => $message,
        'link' => admin_url('/options-discussion.php'),
      );
    }

    return $data;
  }

  /**
   * Get notices count.
   *
   * @return array
   */
  public static function get_notices_count() {
    $notices = WD_SEO_Library::get_recommends_problems(TRUE);

    $recommends_count = 0;
    $problems_count = 0;

    if ( isset($notices['recommends']) && isset($notices['recommends']['plugins']) ) {
      $recommends_count = count($notices['recommends']['plugins']);
    }
    if ( isset($notices['problems']) ) {
      $problems_count = count($notices['problems']);
    }
    $notices_count = $recommends_count + $problems_count;

    return array(
      'recommends_count' => $recommends_count,
      'problems_count' => $problems_count,
      'count' => $notices_count,
    );
  }

  /**
   * Remove directory with its content.
   *
   * @param $path
   */
  public static function remove_directory($path) {
    if (is_dir($path)) {
      $del_folder = scandir($path);

      foreach ($del_folder as $file) {
        if ($file != '.' and $file != '..') {
          self::remove_directory($path . '/' . $file);
        }
      }
      rmdir($path);
    }
    else {
      unlink($path);
    }
  }

  public static function generate_crawl_data( WP_REST_Request $request ) {
    $pass = $request->get_header('tenweb-authorization');
    if ( !class_exists('TenwebServices') || !TenwebServices::check_user_password($pass) ) {
      return new WP_Error( 'not_authorized', 'Not authorized', array( 'status' => 403 ) );
    }

    $data = array();

    $crawl = new WD_SEO_CRAWL;
    $crawl_errors = $crawl->get_crawl_errors();
    if (!isset($crawl_errors['error'])) {
      foreach ($crawl_errors as $platform => $categories) {
        foreach ($categories as $category => $category_arr) {
          $data['crawl_errors'][$platform][$category] = $crawl_errors[$platform][$category]['errors']['count'];
        }
      }
    }

    $moz = new WD_SEO_MOZ;
    $moz_url_metrics = $moz->get_url_metrics();
    $data['moz_url_metrics'] = $moz_url_metrics;

    $search_analytics = $crawl->search_analytics();
    $data['search_analytics'] = $search_analytics;

    // Create the response object
    $response = new WP_REST_Response( $data );
    return $response;
  }

  /**
   * Create cron on seo server.
   */
  public static function create_cron() {
    if ( defined( 'TENWEB_INCLUDES_DIR' ) ) {
      include_once TENWEB_INCLUDES_DIR . '/class-tenweb-services.php';
      if (true === TenwebServices::manager_ready()) {
        $args = array('url' => WD_SEO_REST_API_CRAWL, 'domain_id' => TenwebServices::get_domain_id());
        $response = TenwebServices::do_request(WD_SEO_SERVER . 'createcron', json_encode($args));
        $response = is_wp_error( $response ) ? null : json_decode($response);
        if ( !isset($response['cron_task_id']) ) {
          wp_schedule_single_event(time() + HOUR_IN_SECONDS, WD_SEO_PREFIX . '_error_on_request_create_cron');
        }
        else {
          update_option(WD_SEO_PREFIX . '_cron_id', $response['cron_task_id']);
          wp_clear_scheduled_hook(WD_SEO_PREFIX . '_error_on_request_create_cron');
        }
      }
    }
  }

  /**
   * Remove cron on seo server.
   */
  public static function remove_cron() {
    $cron_id = get_option(WD_SEO_PREFIX . '_cron_id');
    $domain_id = TenwebServices::get_domain_id();
    if ( defined( 'TENWEB_INCLUDES_DIR' ) ) {
      include_once TENWEB_INCLUDES_DIR . '/class-tenweb-services.php';
      if (true === TenwebServices::manager_ready()) {
        $response = TenwebServices::do_request(WD_SEO_SERVER . 'domain/' . $domain_id . '/removecron/' . $cron_id);
        if ( is_wp_error( $response ) ) {
          wp_schedule_single_event(time() + HOUR_IN_SECONDS, WD_SEO_PREFIX . '_error_on_request_remove_cron');
        }
        else {
          delete_option(WD_SEO_PREFIX . '_cron_id');
          wp_clear_scheduled_hook(WD_SEO_PREFIX . '_error_on_request_remove_cron');
        }
      }
    }
  }

  /**
   * User manual and support forum links.
   *
   * @return string
   */
  public static function topic() {
    $page = isset($_GET['page']) ? $_GET['page'] : '';
    $user_guide_link = 'http://docs.10web.io/docs/seo-by-10web/';
    $support_forum_link = 'https://wordpress.org/support/plugin/seo-by-10web';
    $support_icon = WD_SEO_URL . '/images/icons/support.png';
    $prefix = 'wdseo';
    switch ($page) {
      case 'wdseo_overview': {
        $help_text = 'configuring initial settings of the plugin';
        $user_guide_link .= 'overview';
        break;
      }
      case 'wdseo_search_analytics': {
        $help_text = 'provides statistics taken from Google Search Console';
        $user_guide_link .= 'search-analytics';
        break;
      }
      case 'wdseo_search_console': {
        $help_text = 'provides statistics taken from Google Search Console';
        $user_guide_link .= 'search-analytics';
        break;
      }
      case 'wdseo_meta_info': {
        $help_text = 'manage and modify the meta information of your website';
        $user_guide_link .= 'meta-information';
        break;
      }
      case 'wdseo_sitemap': {
        $help_text = 'construct the sitemap for your website';
        $user_guide_link .= 'sitemap';
        break;
      }
      case 'wdseo_settings': {
        $help_text = 'configure general options';
        $user_guide_link .= 'settings';
        break;
      }
      default: {
        return '';
        break;
      }
    }
    ob_start();
    ?>
    <style>
      .wd_topic {
        background-color: #ffffff;
        border: none;
        box-sizing: border-box;
        clear: both;
        color: #6e7990;
        font-size: 14px;
        font-weight: bold;
        line-height: 44px;
        padding: 0 0 0 15px;
        vertical-align: middle;
        width: 98%;
      }
      .wd_topic .wd_help_topic {
        float: left;
      }
      .wd_topic .wd_help_topic a {
        color: #0073aa;
      }
      .wd_topic .wd_help_topic a:hover {
        color: #00A0D2;
      }
      .wd_topic .wd_support {
        float: right;
        margin: 0 10px;
      }
      .wd_topic .wd_support img {
        vertical-align: middle;
      }
      .wd_topic .wd_support a {
        text-decoration: none;
        color: #6E7990;
      }
      .wd_topic .wd_pro {
        float: right;
        padding: 0;
      }
      .wd_topic .wd_pro a {
        border: none;
        box-shadow: none !important;
        text-decoration: none;
      }
      .wd_topic .wd_pro img {
        border: none;
        display: inline-block;
        vertical-align: middle;
      }
      .wd_topic .wd_pro a,
      .wd_topic .wd_pro a:active,
      .wd_topic .wd_pro a:visited,
      .wd_topic .wd_pro a:hover {
        background-color: #D8D8D8;
        color: #175c8b;
        display: inline-block;
        font-size: 11px;
        font-weight: bold;
        padding: 0 10px;
        vertical-align: middle;
      }
    </style>
    <div class="update-nag wd_topic">
      <?php
      if ($help_text) {
        ?>
        <span class="wd_help_topic">
      <?php echo sprintf(__('This section allows you to %s.', $prefix), $help_text); ?>
          <a target="_blank" href="<?php echo $user_guide_link; ?>">
        <?php _e('Read More in User Manual', $prefix); ?>
      </a>
    </span>
        <?php
      }
      if ( TRUE ) {
        ?>
        <span class="wd_support">
      <a target="_blank" href="<?php echo $support_forum_link; ?>">
        <img src="<?php echo $support_icon; ?>" />
        <?php _e('Support Forum', $prefix); ?>
      </a>
    </span>
        <?php
      }
      ?>
    </div>
    <?php
    echo ob_get_clean();
  }
}

