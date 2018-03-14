<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Class WD_SEO_XML.
 */
class WD_SEO_CRAWL {
  /**
   * @var WD_SEO_Options.
   */
  protected $options;

  public $client;
  public $siteUrl;
  public $client_id = '538107981002-3ud1kcvie45ti7mvvdr11tp657kpitfo.apps.googleusercontent.com' ;
  public $client_secret = 'jc-G0cMVxyaSsoMPfpTzO9hG';
  public $redirect_uri = 'urn:ietf:wg:oauth:2.0:oob';
  public $scopes = array(
    'https://www.googleapis.com/auth/webmasters',
    'https://www.googleapis.com/auth/siteverification',
  );
  public $access_type = 'offline';
  public $approval_prompt = 'force';

  private $crawl_errors;
  /**
   * @var string Google client library version.
   */
  private $libver = '2.1.2';

  public function __construct() {
    // Get options.
    $this->options = WDSeo()->options;

    $this->siteUrl = home_url();

    $crawl_errors = get_option(WD_SEO_PREFIX . '_crawlerrors');
    if ( $crawl_errors != 'null' && $crawl_errors ) {
      $this->crawl_errors = json_decode($crawl_errors, TRUE);
    }
    else {
      $this->crawl_errors = array(
        'web' => array(
          'authPermissions' => array(
            'title' => __('Access denied', WD_SEO_PREFIX),
            'value' => '',
            'errors' => array( 'count' => 0 ),
          ),
          'notFollowed' => array(
            'title' => __('Not Followed', WD_SEO_PREFIX),
            'value' => '',
            'errors' => array( 'count' => 0 ),
          ),
          'notFound' => array(
            'title' => __('Not Found', WD_SEO_PREFIX),
            'value' => '',
            'errors' => array( 'count' => 0 ),
          ),
          'other' => array(
            'title' => __('Other', WD_SEO_PREFIX),
            'value' => '',
            'errors' => array( 'count' => 0 ),
          ),
          'serverError' => array(
            'title' => __('Server Error', WD_SEO_PREFIX),
            'value' => '',
            'errors' => array( 'count' => 0 ),
          ),
          'soft404' => array(
            'title' => __('Soft 404', WD_SEO_PREFIX),
            'value' => '',
            'errors' => array( 'count' => 0 ),
          ),
        ),
//        'mobile' => array(),
        'smartphoneonly' => array(
          'authPermissions' => array(
            'title' => __('Access denied', WD_SEO_PREFIX),
            'value' => '',
            'errors' => array( 'count' => 0 ),
          ),
          'flashContent' => array(
            'title' => __('Flash Content', WD_SEO_PREFIX),
            'value' => '',
            'errors' => array( 'count' => 0 ),
          ),
          'manyToOneRedirect' => array(
            'title' => __('Many To One Redirect', WD_SEO_PREFIX),
            'value' => '',
            'errors' => array( 'count' => 0 ),
          ),
          'notFollowed' => array(
            'title' => __('Not Followed', WD_SEO_PREFIX),
            'value' => '',
            'errors' => array( 'count' => 0 ),
          ),
          'notFound' => array(
            'title' => __('Not Found', WD_SEO_PREFIX),
            'value' => '',
            'errors' => array( 'count' => 0 ),
          ),
          'other' => array(
            'title' => __('Other', WD_SEO_PREFIX),
            'value' => '',
            'errors' => array( 'count' => 0 ),
          ),
          'serverError' => array(
            'title' => __('Server Error', WD_SEO_PREFIX),
            'value' => '',
            'errors' => array( 'count' => 0 ),
          ),
          'roboted' => array(
            'title' => __('Roboted', WD_SEO_PREFIX),
            'value' => '',
            'errors' => array( 'count' => 0 ),
          ),
          'soft404' => array(
            'title' => __('Soft 404', WD_SEO_PREFIX),
            'value' => '',
            'errors' => array( 'count' => 0 ),
          ),
        ),
      );
    }

    require_once(wp_normalize_path(WD_SEO_DIR . '/google/vendor/autoload.php'));

    $client = new Google_Client();

    if ( version_compare($client::LIBVER, $this->libver, '<') ) {
      $message = sprintf(__('One of your plugins is using an old version of Google client library. The plugin requires Google Client Library version %s or higher', WD_SEO_PREFIX), $this->libver);
      $client = array( 'error' => TRUE, 'message' => $message, 'interrupt' => TRUE );
    }

    $this->client = $client;
  }

  /**
   * Authorize.
   *
   * @return bool
   */
  public function authorize() {
    $code = WD_SEO_Library::get('code');
    if ( $code ) {
      try {
        $this->client->setClientId($this->client_id);
        $this->client->setClientSecret($this->client_secret);
        $this->client->setRedirectUri($this->redirect_uri);

        $this->client->authenticate($code);
        $access_token = $this->client->getAccessToken();
        /*WD_SEO_Library::create_cron();*/ // ToDo: Remove comments when seo service is ready.
      }
      catch ( \Exception $e ) {
        return array('error' => TRUE, 'message' => $e->getMessage());
      }

      // Save access token.
      $this->options->access_token = $access_token;
      update_option(WD_SEO_PREFIX . '_options', json_encode($this->options), 'no');

      return TRUE;
    }
    else {
      return $this->set_google_client();
    }
  }

  /**
   * Add property to Google search console.
   * Get site verification code and insert as meta.
   * Verify property.
   *
   * @return array|bool
   */
  public function verify() {
    try {
      $siteUrl = $this->siteUrl;
      $service = new Google_Service_Webmasters($this->client);
      // Add property to Google search console.
      $service->sites->add($siteUrl);
      $service = new Google_Service_SiteVerification($this->client);
      $gettokenRequest = new Google_Service_SiteVerification_SiteVerificationWebResourceGettokenRequest();
      $gettokenRequestSite = new Google_Service_SiteVerification_SiteVerificationWebResourceGettokenRequestSite();
      $gettokenRequest->setSite($gettokenRequestSite);
      $gettokenRequestSite->setIdentifier($siteUrl);
      $gettokenRequestSite->setType('SITE');
      $gettokenRequest->setVerificationMethod('META');
      // Get Google site verification code.
      $token = $service->webResource->getToken($gettokenRequest)->token;
      $this->options->google_site_verification = $token;
      update_option(WD_SEO_PREFIX . '_options', json_encode($this->options), 'no');
      // Verify property.
      $resource = new Google_Service_SiteVerification_SiteVerificationWebResourceResource();
      $resourceSite = new Google_Service_SiteVerification_SiteVerificationWebResourceResourceSite();
      $resource->setSite($resourceSite);
      $resourceSite->setType('SITE');
      $resourceSite->setIdentifier($siteUrl);
      $service->webResource->insert('META', $resource);

      return TRUE;
    } catch (Google_Service_Exception $e) {
      $errors = $e->getErrors();
      $message = isset($errors[0]["message"]) ? $errors[0]["message"] : __('Something goeas wrong', WD_SEO_PREFIX);

      return array('error' => TRUE, 'message' => $message);
    }
  }

  /**
   * Get crawl errors.
   *
   * @return array|bool
   */
  public function get_crawl_errors() {
    if ( !is_object($this->client) && isset($this->client['error']) ) {
      return $this->client;
    }
    // Get access token or refresh access token.
    $authorized = $this->authorize();
    if ( !$authorized ) {
      // At first time.
      return array( 'error' => TRUE );
    }
    elseif ( isset($authorized['message']) ) {
      return array('error' => TRUE, 'message' => $authorized['message']);
    }
    elseif ( $authorized !== 1 ) {
      // Add and verify the property on Google search console
      // only on authorization.
      $verified = $this->verify();
      if ( $verified !== TRUE ) {
        return $verified;
      }
    }

    try {
      $siteUrl = $this->siteUrl;

      $service = new Google_Service_Webmasters($this->client);

      $urlcrawlerrors = $this->crawl_errors;

      foreach ( $urlcrawlerrors as $platform => $categories ) {
        foreach ( $categories as $category => $category_arr ) {
          $urlcrawlerrorssamples = $service->urlcrawlerrorssamples->listUrlcrawlerrorssamples($siteUrl, $category, $platform);
          if ( isset($urlcrawlerrorssamples['modelData']['urlCrawlErrorSample']) ) {
            $urlcrawlerrors[$platform][$category]['value'] = $urlcrawlerrorssamples['modelData']['urlCrawlErrorSample'];
            $urlcrawlerrorscounts = $service->urlcrawlerrorscounts->query($siteUrl, array('category' => $category, 'platform' => $platform));
            if ( isset($urlcrawlerrorscounts['modelData']['countPerTypes'][0]['entries'][0]) ) {
              $urlcrawlerrors[$platform][$category]['errors'] = $urlcrawlerrorscounts['modelData']['countPerTypes'][0]['entries'][0];
            }
          }
          else {
            unset($urlcrawlerrors[$platform][$category]);
          }
        }
      }

      // Save errors to DB.
      $this->save($urlcrawlerrors);

      return $urlcrawlerrors;
    } catch (Google_Service_Exception $e) {
      $errors = $e->getErrors();
      $message = isset($errors[0]["message"]) ? $errors[0]["message"] : __('Something goeas wrong.', WD_SEO_PREFIX);

      return array('error' => TRUE, 'message' => $message);
    }
  }

  /**
   * Set google client.
   *
   * @return bool
   */
  private function set_google_client() {
    $access_token = (array) $this->options->access_token;
    if ( !empty($access_token) ) {
      $this->client->setAccessToken($access_token);
      if ( $this->client->isAccessTokenExpired() ) {
        $this->client->setClientId($this->client_id);
        $this->client->setClientSecret($this->client_secret);
        $this->client->setRedirectUri($this->redirect_uri);
        $refresh_token = $access_token['refresh_token'];
        $this->client->refreshToken($refresh_token);
      }

      return 1;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Get authorization URL.
   *
   * @return string
   */
  public function authorization_url() {
    if ( !is_object($this->client) && isset($this->client['error']) ) {
      return '';
    }

    $this->client->setClientId($this->client_id);
    $this->client->setClientSecret($this->client_secret);
    $this->client->setRedirectUri($this->redirect_uri);
    $this->client->addScope($this->scopes);
    $this->client->setApprovalPrompt($this->approval_prompt);
    $this->client->setAccessType($this->access_type);

    return $this->client->createAuthUrl();
  }

  /**
   * Reauthenticate with google.
   *
   * @return array
   */
  public function reauthenticate() {
    $this->options->access_token = array();
    $this->options->google_site_verification = '';
    update_option(WD_SEO_PREFIX . '_options', json_encode($this->options), 'no');
    /*WD_SEO_Library::remove_cron();*/ // ToDo: Remove comments when seo service is ready.
  }

  /**
   * Save errors to DB.
   *
   * @param $new_values
   */
  private function save($new_values) {
    $initial_values = get_option(WD_SEO_PREFIX . '_crawlerrors');
    if ( $initial_values != 'null' && $initial_values ) {
      $initial_values = json_decode($initial_values, TRUE);
    }
    else {
      $initial_values = array();
    }

    // Leave in array only fixed issues.
    foreach ( $initial_values as $platform => $categories ) {
      foreach ( $categories as $category ) {
        if ( isset($category['value']) ) {
          foreach ( $category['value'] as $issue_key => $issue ) {
            if ( !isset($issue['state']) || $issue['state'] != 'marked_as_fixed' ) {
              unset($category['value'][$issue_key]);
            }
          }
        }
      }
    }

    // Remove empty platforms.
    foreach ( $this->crawl_errors as $platform => $category ) {
      if ( empty($new_values[$platform]) ) {
        unset($new_values[$platform]);
      }
    }

    $issues = array_replace_recursive($initial_values, $new_values);
    if ( !empty($issues) ) {
      // If issues founded.
      update_option(WD_SEO_PREFIX . '_crawlerrors', json_encode($issues));
    }
    else {
      update_option(WD_SEO_PREFIX . '_crawlerrors', 0);
    }
  }

  /**
   * Get Google search analytics results.
   *
   * @param string $device (desktop/mobile/tablet)
   * @param bool   $query
   * @param string $country (specified by 3-letter country code (ISO 3166-1 alpha-3))
   *
   * @return array|Google_Client|mixed
   */
  public function search_analytics($device = 'desktop', $keyword = FALSE, $country = '') {
    if ( !is_object($this->client) && isset($this->client['error']) ) {
      return $this->client;
    }
    // Get access token or refresh access token.
    $authorized = $this->authorize();
    if ( !$authorized ) {
      // At first time.
      return array( 'error' => TRUE );
    }
    elseif ( $authorized !== 1 ) {
      // Add and verify the property on Google search console
      // only on authorization.
      $verified = $this->verify();
      if ( $verified !== TRUE ) {
        return $verified;
      }
    }
    try {
      $service = new Google_Service_Webmasters($this->client);
      $query = new \Google_Service_Webmasters_SearchAnalyticsQueryRequest();

      // Search Analytics query parameters:
      // https://developers.google.com/webmaster-tools/search-console-api-original/v3/searchanalytics/query#dimensionFilterGroups.filters.dimension
      $query->setStartDate(date('Y-m-d', strtotime('-92 days')));
      $query->setEndDate(date('Y-m-d', strtotime('-2 days')));
      $query->setSearchType('web');
      $query->setDimensions(['query']);
      //  $query->setRowLimit($this->search_analytics_row_limit);
      $query->setAggregationType("byProperty");

      $searchAnalyticsDimensionFilterGroup = new Google_Service_Webmasters_ApiDimensionFilterGroup();
      $filters = array();
      $filters[] = array(
        'dimension' => 'device',
        'operator' => 'equals',
        'expression' => strtoupper($device),
      );
      if ( $keyword ) {
        $filters[] = array(
          'dimension' => 'query',
          'operator' => 'contains',
          'expression' => $keyword,
        );
      }
      if ( $country ) {
        $filters[] = array(
          'dimension' => 'country',
          'operator' => 'equals',
          'expression' => $country,
        );
      }
      $dimension_filters = array();
      foreach ($filters as $filter) {
        $dimensionFilter = new Google_Service_Webmasters_ApiDimensionFilter();
        $dimensionFilter->setDimension($filter['dimension']);
        $dimensionFilter->setOperator($filter['operator']);
        $dimensionFilter->setExpression($filter['expression']);
        $dimension_filters[] = $dimensionFilter;
      }
      $searchAnalyticsDimensionFilterGroup->setFilters($dimension_filters);
      $searchAnalyticsDimensionFilterGroup->setGroupType('and');
      $query->setDimensionFilterGroups(array($searchAnalyticsDimensionFilterGroup));

      $search_analytics = $service->searchanalytics->query($this->siteUrl, $query);

      return isset($search_analytics->rows) ? $search_analytics->rows : 0;
    }
    catch ( Google_Service_Exception $e ) {
      $errors = $e->getErrors();
      $message = isset($errors[0]["message"]) ? $errors[0]["message"] : __('Something goes wrong.', WD_SEO_PREFIX);

      return array( 'error' => TRUE, 'message' => $message );
    }
  }
}
