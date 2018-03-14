<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Overview controller class.
 */
class WDSeosearch_consoleController extends WDSeoAdminController {
  private $crawl_errors;
  private $platform;
  private $category;

  public function __construct( $page = NULL, $task = NULL ) {
    $crawl_errors = get_option(WD_SEO_PREFIX . '_crawlerrors');
    if ( $crawl_errors ) {
      $this->crawl_errors = json_decode($crawl_errors, TRUE);

      $this->platform = WD_SEO_Library::get('platform', 'web');
      $this->category = WD_SEO_Library::get('category', 'notFound');

      // Get first platform if current not found.
      if ( !array_key_exists($this->platform, $this->crawl_errors) ) {
        foreach ( $crawl_errors as $key => $crawl_error ) {
          $this->platform = $key;
          break;
        }
      }
      // Get first category if current not found.
      if ( !array_key_exists($this->category, $this->crawl_errors[$this->platform]) ) {
        foreach ( $this->crawl_errors[$this->platform] as $key => $crawl_error ) {
          $this->category = $key;
          break;
        }
      }
    }
    else {
      if ($crawl_errors === "0") {
        $this->crawl_errors = 0;
      }
      else {
        $this->crawl_errors = FALSE;
      }
    }

    parent::__construct($page, $task);
  }

  /**
   * Display.
   *
   * @param null $crawl_errors
   */
  public function display( $crawl_errors = NULL ) {
    // Get the page view if exist or global view otherwise.
    $view_class = class_exists($this->class_prefix . 'View') ? $this->class_prefix . 'View' : WD_SEO_CLASS_PREFIX . 'AdminView';
    $view = new $view_class();

    if ( $crawl_errors === NULL ) {
      $crawl_errors = $this->get_crawl_errors();
    }

    echo $view->display($crawl_errors, $this->filters());
  }

  private function get_crawl_errors() {
    $crawl_errors = $this->crawl_errors;
    $paged = WD_SEO_Library::get('paged', 1);
    $search = WD_SEO_Library::get('s', '');
    $state = WD_SEO_Library::get('state', '');

    if ( $crawl_errors ) {
      foreach ( $crawl_errors as $platform => $categories ) {
        $platform_tooltip = '';
        if ( $platform == 'web' ) {
          $platform_tooltip = __('Errors that occurred when your site was crawled by Googlebot.', WD_SEO_PREFIX);
        }
        if ( $platform == 'smartphoneonly' ) {
          $platform_tooltip = __('Errors that occurred only when your site was crawled by Googlebot (errors didn&rsquo;t appear for desktop).', WD_SEO_PREFIX);
        }

        foreach ( $categories as $category => $category_arr ) {
          // Search and filter.
          foreach ( $category_arr['value'] as $key => $value ) {
            if ( isset($value['pageUrl']) ) {
              $value['pageUrl'] = trim($value['pageUrl'], '/');
              if ( $search ) {
                if ( strpos($value['pageUrl'], $search) === FALSE ) {
                  unset($category_arr['value'][$key]);
                }
              }
              if ( ($state == 'marked_as_fixed' && (!isset($value['state']) || $value['state'] != $state))
                || ($state != 'marked_as_fixed' && isset($value['state']) && $value['state'] == 'marked_as_fixed') ) {
                unset($category_arr['value'][$key]);
              }
            }
          }
          // Set tooltip message.
          if ( $category == 'authPermissions' ) {
            $crawl_errors[$platform][$category]['tooltip-info'] = sprintf(__('Server requires authentication or is blocking Googlebot from accessing the site. %s', WD_SEO_PREFIX), '<a href="//support.google.com/webmasters/answer/2409441?ctx=MCE&amp;ctx=AD" target="_blank">' . __('Learn more', WD_SEO_PREFIX) . '</a>');
          }
          if ( $category == 'notFound' ) {
            $crawl_errors[$platform][$category]['tooltip-info'] = sprintf(__('URL points to a non-existent page. %s', WD_SEO_PREFIX), '<a href="//support.google.com/webmasters/bin/answer.py?answer=2409439&amp;ctx=MCE&amp;ctx=NF" target="_blank">' . __('Learn more', WD_SEO_PREFIX) . '</a>');
          }
          if ( $category == 'serverError' ) {
            $crawl_errors[$platform][$category]['tooltip-info'] = sprintf(__('Shows any instances when Googlebot&rsquo;s request timed out or the site is blocking Google. %s', WD_SEO_PREFIX), '<a href="//support.google.com/webmasters/answer/2409437" target="_blank">' . __('Learn more', WD_SEO_PREFIX) . '</a>');
          }
          if ( $category == 'soft404' ) {
            $crawl_errors[$platform][$category]['tooltip-info'] = sprintf(__('The target URL doesn&rsquo;t exist, but your server is not returning a 404 (file not found) error. %s', WD_SEO_PREFIX), '<a href="//support.google.com/webmasters/answer/2409443" target="_blank">' . __('Learn more', WD_SEO_PREFIX) . '</a>');
          }
          // Sort.
          usort($category_arr['value'], array( $this, 'sort_crawl_errors' ));
          $crawl_errors[$platform][$category]['value'] = $category_arr['value'];
          // Total.
          $crawl_errors[$platform][$category]['total'] = count($category_arr['value']);
          // Paged.
          $crawl_errors[$platform][$category]['value'] = array_slice($crawl_errors[$platform][$category]['value'], ($paged - 1) * WD_SEO_HTML::$total_in_page, WD_SEO_HTML::$total_in_page);
        }
        $crawl_errors[$platform]['tooltip-info'] = $platform_tooltip;
      }
    }
    return $crawl_errors;
  }

  /**
   * Sort errors.
   *
   * @param $a
   * @param $b
   *
   * @return int
   */
  public function sort_crawl_errors( $a, $b ) {
    $orderby = WD_SEO_Library::get('orderby', 'pageUrl');
    $order = WD_SEO_Library::get('order', 'asc');

    $result = strcmp( $a[ $orderby ], $b[ $orderby ] );

    return ( $order === 'asc' ) ? $result : ( - $result );
  }

  /**
   * Authenticate.
   */
  public function authenticate() {
    $this->display();
  }

  /**
   * Reauthenticate with google.
   */
  public function reauthenticate() {
//    $this->crawl->reauthenticate();

    $this->display(FALSE);
  }

  /**
   * Set redirect URL.
   */
  public function create_redirect() {
    $url = WD_SEO_Library::get('url', '');
    $redirect_url = WD_SEO_Library::get('redirect_url', '');
    if ( $url
      && isset($this->crawl_errors[$this->platform][$this->category]['value']) ) {
      foreach ( $this->crawl_errors[$this->platform][$this->category]['value'] as $key => $value ) {
        // esc_url to prevent special chars.
        if ( esc_url(trim($value['pageUrl'], '/')) == esc_url($url) ) {
          $this->crawl_errors[$this->platform][$this->category]['value'][$key]['state'] = 'redirected';
          $this->crawl_errors[$this->platform][$this->category]['value'][$key]['redirect_url'] = esc_url($redirect_url);
        }
      }

      $this->store();

      $this->mark_as_fixed();
    }
  }

  /**
   * Mark as fixed.
   */
  public function mark_as_fixed() {
    $url = WD_SEO_Library::get('url');
    if ( $url
      && isset($this->crawl_errors[$this->platform][$this->category]['value']) ) {
      foreach ( $this->crawl_errors[$this->platform][$this->category]['value'] as $key => $value ) {
        // esc_url to prevent special chars.
        if ( esc_url(trim($value['pageUrl'], '/')) == esc_url($url) ) {
          $crawl = new WD_SEO_CRAWL;
          $crawl->get_crawl_errors();
          $service = new Google_Service_Webmasters($crawl->client);
          $service->urlcrawlerrorssamples->markAsFixed($crawl->siteUrl, ($value['pageUrl']), $this->category, $this->platform);
          $this->crawl_errors[$this->platform][$this->category]['value'][$key]['state'] = 'marked_as_fixed';

          // Increase issues count.
          if ( $this->crawl_errors[$this->platform][$this->category]['errors']['count'] > 0 ) {
            --$this->crawl_errors[$this->platform][$this->category]['errors']['count'];
          }
        }
      }

      $this->store();
    }

    $this->display();
  }

  /**
   * Filters.
   *
   * @return array
   */
  public function filters() {
    $filters = array(
      'state' => array(
        '' => __('To be fixed',  WD_SEO_PREFIX),
//        'redirected' => __('Redirected',  WD_SEO_PREFIX),
        'marked_as_fixed' => __('Marked as fixed',  WD_SEO_PREFIX),
      ),
    );

    return $filters;
  }

  /**
   * Save errors to DB.
   */
  public function store() {
    update_option(WD_SEO_PREFIX . '_crawlerrors', json_encode($this->crawl_errors));
  }
}
