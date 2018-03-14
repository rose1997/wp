<?php

class WDSeo_Site {
  /**
   * Options instance.
   *
   * @var WD_SEO_Options
   */
  public $options = null;

  /**
   * Placeholders.
   *
   * @array
   */
  public $placeholders = array();

  /**
   * @var null
   */
  public $object = null;

  /**
   * WDSeo_Site constructor.
   */
  public function __construct() {
    $this->options = new WD_SEO_Options();

    $this->init();
  }

  /**
   * WDSeo_Site init.
   */
  private function init() {
    $this->og_type = 'article';
    // The sequence is very important as Category can be identified as archive
    // if is_archive() will be checked before is_category().
    if (is_front_page() || is_home()) {
      //home
      $type = 'home';
      $this->og_type = 'website';
      $object = $this->options->metas->home;
    }
    else {
      if (is_category() || is_tag() || is_tax()) {
        //taxonomy
        $type = 'taxonomy';
        $queried_object = get_queried_object();
        $object = new WD_SEO_Postmeta($queried_object->term_id, $queried_object->taxonomy, 'site_values');
      }
      else {
        if (is_search()) {
          //search
          $type = 'search';
          $object = $this->options->metas->search;
        }
        else {
          if (is_author()) {
            //author archive
            $type = 'author_archive';
            $object = $this->options->metas->author_archive;
          }
          else {
            if (is_archive()) {
              //date archive
              $type = 'date_archive';
              $object = $this->options->metas->date_archive;
            }
            else {
              if (is_404()) {
                //404
                $type = '404';
                $object = $this->options->metas->{404};
              }
              else {
                if (is_singular()) {
                  //post
                  $type = 'post';
                  $queried_object = get_queried_object();
                  $object = new WD_SEO_Postmeta($queried_object->ID, 'post', 'site_values');
                }
                else {
                  return;
                }
              }
            }
          }
        }
      }
    }

    $redirection_status = $this->options->redirections == '1' ? 301 : 302;
    if (isset($object->redirect_url) && $object->redirect_url) {
      wp_redirect($object->redirect_url, $redirection_status);
      exit;
    }

    if ($this->options->meta) {
      $this->placeholders = WD_SEO_Library::get_placeholders(true);
      $this->object = $object;
      $this->type = $type;

      add_action('wp_head', array($this, 'head'), 10, 1);
      add_filter('pre_get_document_title', array($this, 'title'), 15);
      add_filter('wp_title', array($this, 'title'), 10, 3);

      // Remove wordpress action, that we're going to replace.
      remove_action('wp_head', 'rel_canonical');

      // Remove date from posts.
      if (isset($this->object->date) && !$this->object->date) {
        add_action('loop_start', array($this, 'remove_post_dates'));
      }
    }
  }

  function remove_post_dates() {
    add_filter('the_date', '__return_false');
    add_filter('the_time', '__return_false');
    add_filter('the_modified_date', '__return_false');
    add_filter('get_the_date', '__return_false');
    add_filter('get_the_time', '__return_false');
    add_filter('get_the_modified_date', '__return_false');
  }

  public function head() {
    $this->canonical();
    $this->meta_description();
    $this->robots();
    $this->meta_keywords();
    $this->meta_opengraph();
    $this->meta_twitter();

    // Verification codes.
    $this->webmaster_tools_authentication();
  }

  /**
   * Output Search engines verification codes to front page.
   */
  public function webmaster_tools_authentication() {
    // Get Google verification code when authorizing with Google.
    if ( $this->options->google_site_verification ) {
      echo $this->options->google_site_verification;
    }
    // Bing.
    if ($this->options->bing_verification) {
      echo '<meta name="msvalidate.01" content="' . esc_attr($this->options->bing_verification) . '" />' . "\n";
    }
    // Yandex.
    if ($this->options->yandex_verification) {
      echo '<meta name="yandex-verification" content="' . esc_attr($this->options->yandex_verification) . '" />' . "\n";
    }
  }

  /**
   * Output canonical url.
   */
  public function canonical() {
    if (isset($this->object->canonical_url) && $this->object->canonical_url) {
      $canonical = $this->object->canonical_url;
    }
    else {
      switch ($this->type) {
        case 'home': {
          $canonical = home_url();
          break;
        }
        case 'taxonomy': {
          $queried_object = get_queried_object();
          $canonical = get_term_link($queried_object, $queried_object->taxonomy);
          break;
        }
        case 'search': {
          $canonical = get_search_link();
          break;
        }
        case 'author_archive': {
          $canonical = get_author_posts_url(get_query_var('author'), get_query_var('author_name'));
          break;
        }
        case 'date_archive': {
          if (is_date()) {
            if (is_day()) {
              $canonical = get_day_link(get_query_var('year'), get_query_var('monthnum'), get_query_var('day'));
            }
            elseif (is_month()) {
              $canonical = get_month_link(get_query_var('year'), get_query_var('monthnum'));
            }
            elseif (is_year()) {
              $canonical = get_year_link(get_query_var('year'));
            }
          }
          break;
        }
        case 'post': {
          $queried_object = get_queried_object();
          $canonical = get_permalink($queried_object->ID);
          break;
        }
      }
    }
    if (!empty($canonical)) {
      echo '<link rel="canonical" href="' . esc_attr($canonical) . '" />' . "\n";
      $this->canonical = $canonical;
    }
  }

  /**
   * Output title.
   *
   * @param $title
   * @return string|void
   */
  public function title($title) {
    if (!empty($this->object->meta_title)) {
      $meta_title = WD_SEO_Library::replace_placeholders($this->object->meta_title, $this->placeholders);
      if (!empty($meta_title)) {
        return esc_attr(strip_tags($meta_title));
      }
    }
    return $title;
  }

  /**
   * Output meta description.
   */
  public function meta_description() {
    if (!empty($this->object->meta_description)) {
      $meta_description = WD_SEO_Library::replace_placeholders($this->object->meta_description, $this->placeholders);
      if (!empty($meta_description)) {
        echo '<meta name="description" content="' . esc_attr(strip_tags($meta_description)) . '" />' . "\n";
      }
    }
  }

  /**
   * Output meta robots.
   */
  public function robots() {
    $robots = '';
    if (isset($this->object->index)) {
      $robots = $this->object->index ? 'index,' : 'noindex,';
    }
    if (isset($this->object->follow)) {
      $robots .= $this->object->follow ? 'follow,' : 'nofollow,';
    }
    if (isset($this->object->robots_advanced)) {
      $robots_advanced = ltrim(implode(',', $this->object->robots_advanced), '0,');
      $robots .= $robots_advanced;
    }
    $robots = rtrim($robots, ',');
    if ('index,follow' != $robots) {
      echo '<meta name="robots" content="' . esc_attr($robots) . '"/>' . "\n";
    }
  }

  /**
   * Output meta keywords.
   */
  public function meta_keywords() {
    if (!empty($this->object->meta_keywords) && is_array($this->object->meta_keywords)) {
      $meta_keywords = implode(',', $this->object->meta_keywords);
      if (!empty($meta_keywords)) {
        echo '<meta name="keywords" content="' . esc_attr($meta_keywords) . '" />' . "\n";
      }
    }
  }

  /**
   * Output open graph meta.
   */
  public function meta_opengraph() {
    $opengraph_title = '';
    if (!empty($this->object->opengraph_title)) {
      $opengraph_title = WD_SEO_Library::replace_placeholders($this->object->opengraph_title, $this->placeholders);
      if (!empty($opengraph_title)) {
        echo '<meta property="og:title" content="' . esc_attr($opengraph_title) . '" />' . "\n";
      }
    }
    if (!empty($this->canonical)) {
      echo '<meta property="og:url" content="' . esc_attr($this->canonical) . '" />' . "\n";
    }
    if (!empty($this->og_type)) {
      echo '<meta property="og:type" content="' . esc_attr($this->og_type) . '" />' . "\n";
    }
    if (!empty($this->object->opengraph_description)) {
      $opengraph_description = WD_SEO_Library::replace_placeholders($this->object->opengraph_description, $this->placeholders);
      if (!empty($opengraph_description)) {
        echo '<meta property="og:description" content="' . esc_attr($opengraph_description) . '" />' . "\n";
      }
    }
    if (!empty($this->object->opengraph_images)) {
      $attachment_ids = explode(',', $this->object->opengraph_images);
      foreach ($attachment_ids as $id) {
        $image = wp_get_attachment_image_src($id, 'original');
        if (!empty($image)) {
          echo '<meta property="og:image" content="' . esc_attr($image[0]) . '" />' . "\n";
          echo '<meta property="og:image:width" content="' . esc_attr($image[1]) . '" />' . "\n";
          echo '<meta property="og:image:height" content="' . esc_attr($image[2]) . '" />' . "\n";
          $image_alt = get_post_meta( $id, '_wp_attachment_image_alt', true);
          if (empty($image_alt) && !empty($opengraph_title)) {
            $image_alt = $opengraph_title;
          }
          if (!empty($image_alt)) {
            echo '<meta property="og:image:alt" content="' . esc_attr($image_alt) . '" />' . "\n";
          }
        }
      }
    }
  }

  /**
   * Output open graph meta.
   */
  public function meta_twitter() {
    if (isset($this->object->use_og_for_twitter)) {
      $opengraph_title = $this->object->use_og_for_twitter ? $this->object->opengraph_title : $this->object->twitter_title;
      if (!empty($opengraph_title)) {
        $opengraph_title = WD_SEO_Library::replace_placeholders($opengraph_title, $this->placeholders);
        if (!empty($opengraph_title)) {
          echo '<meta name="twitter:title" content="' . esc_attr($opengraph_title) . '" />' . "\n";
        }
      }
      $opengraph_description = $this->object->use_og_for_twitter ? $this->object->opengraph_description : $this->object->twitter_description;
      if (!empty($this->object->opengraph_description)) {
        $opengraph_description = WD_SEO_Library::replace_placeholders($opengraph_description, $this->placeholders);
        if (!empty($opengraph_description)) {
          echo '<meta name="twitter:description" content="' . esc_attr($opengraph_description) . '" />' . "\n";
        }
      }
      $opengraph_images = $this->object->use_og_for_twitter ? $this->object->opengraph_images : $this->object->twitter_images;
      if (!empty($opengraph_images)) {
        $attachment_ids = explode(',', $opengraph_images);
        foreach ($attachment_ids as $id) {
          $image = wp_get_attachment_url($id);
          if (!empty($image)) {
            echo '<meta name="twitter:image" content="' . esc_attr($image) . '" />' . "\n";
          }
        }
      }
    }
  }
}

function WDSWDSeo_Site() {
  new WDSeo_Site();
}
add_action('wp', 'WDSWDSeo_Site');
