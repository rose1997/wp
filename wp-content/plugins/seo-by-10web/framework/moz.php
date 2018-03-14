<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Class WD_SEO_MOZ.
 */
class WD_SEO_MOZ {
  /**
   * @var WD_SEO_Options.
   */
  protected $options;
  /**
   * @var string Moz access id and secret key.
   */
  private $accessID;
  private $secretKey;
  /**
   * @var string|void Site URL.
   */
  private $siteUrl;
  /**
   * @var string The bit flags you want returned.
   */
  private $cols;
  /**
   * @var int Expires times.
   */
  private $expires;
  /**
   * @var string Signature.
   */
  private $urlSafeSignature;

  private $urlMetric;

  public function __construct() {
    // Get options.
    $this->options = WDSeo()->options;

    // Get Moz access id and secret key from options.
    $this->accessID = $this->options->moz_access_id;
    $this->secretKey = $this->options->moz_secret_id;

    if ( !$this->accessID && !$this->secretKey ) {
      return FALSE;
    }

    // Get site URL.
    $this->siteUrl = urlencode(get_bloginfo('url'));

    $this->urlMetric = array(
      'uu' => array(
        'title' => __('Canonical URL', WD_SEO_PREFIX),
        'description' => __('The canonical form of the URL', WD_SEO_PREFIX),
        'bit_flag' => 4,
      ),
      'ueid' => array(
        'title' => __('External Equity Links', WD_SEO_PREFIX),
        'description' => __('The number of external equity links to the URL', WD_SEO_PREFIX),
        'bit_flag' => 32,
      ),
      'uid' => array(
        'title' => __('Links', WD_SEO_PREFIX),
        'description' => __('The number of links (equity or nonequity or not, internal or external) to the URL', WD_SEO_PREFIX),
        'bit_flag' => 2048,
      ),
      'umrp' => array(
        'title' => __('MozRank: URL', WD_SEO_PREFIX),
        'description' => __('The MozRank of the URL, in both the normalized 10-point score', WD_SEO_PREFIX),
        'bit_flag' => 16384,
      ),
      'fmrp' => array(
        'title' => __('MozRank: Subdomain', WD_SEO_PREFIX),
        'description' => __('The MozRank of the URL\'s subdomain, in both the normalized 10-point score', WD_SEO_PREFIX),
        'bit_flag' => 32768,
      ),
      'us' => array(
        'title' => __('HTTP Status Code', WD_SEO_PREFIX),
        'description' => __('The HTTP status code recorded by Mozscape for this URL, if available', WD_SEO_PREFIX),
        'bit_flag' => 536870912,
      ),
      'upa' => array(
        'title' => __('Page Authority', WD_SEO_PREFIX),
        'description' => __('A normalized 100-point score representing the likelihood of a page to rank well in search engine results', WD_SEO_PREFIX),
        'bit_flag' => 34359738368,
      ),
      'pda' => array(
        'title' => __('Domain Authority', WD_SEO_PREFIX),
        'description' => __('A normalized 100-point score representing the likelihood of a domain to rank well in search engine results', WD_SEO_PREFIX),
        'bit_flag' => 68719476736,
      ),
      'ulc' => array(
        'title' => __('Time last crawled', WD_SEO_PREFIX),
        'description' => __('The time and date on which Mozscape last crawled the URL, returned in Unix epoch format', WD_SEO_PREFIX),
        'bit_flag' => 144115188075855872,
      ),
    );

    // The bit flags you want returned.
    // Learn more here: https://moz.com/help/guides/moz-api/mozscape/api-reference/url-metrics
    $this->cols = 0;
    foreach ( $this->urlMetric as $urlMetric ) {
      $this->cols += $urlMetric['bit_flag'];
    }

    // Set your expires times for several minutes into the future.
    // An expires time excessively far in the future will not be honored by the Mozscape API.
    $this->expires = time() + 300;

    // Put each parameter on a new line.
    $stringToSign = $this->accessID . "\n" . $this->expires;

    // Get the "raw" or binary output of the hmac hash.
    $binarySignature = hash_hmac('sha1', $stringToSign, $this->secretKey, TRUE);

    // Base64-encode it and then url-encode that.
    $this->urlSafeSignature = urlencode(base64_encode($binarySignature));
  }

  /**
   * Get URL metrics.
   *
   * @return array|bool
   */
  public function get_url_metrics() {
    if ( !$this->accessID && !$this->secretKey ) {
      return FALSE;
    }

    $requestUrl = "http://lsapi.seomoz.com/linkscape/url-metrics/{$this->siteUrl}";

    $args = array(
      'Cols' => $this->cols,
      'AccessID' => $this->accessID,
      'Expires' => $this->expires,
      'Signature' => $this->urlSafeSignature,
    );

    $requestUrl = add_query_arg($args, $requestUrl);

    $response = wp_remote_get( $requestUrl );

    if ( !is_wp_error( $response ) ) {
      $urlMetrics = json_decode(wp_remote_retrieve_body($response));
      if ( !is_object($urlMetrics) ) {
        $message = sprintf(__('Unable to retrieve data from the Moz API. Error: %s.', WD_SEO_PREFIX), $urlMetrics);
        return array( 'error' => TRUE, 'message' => $message );
      }
      elseif ( isset($urlMetrics->status) && $urlMetrics->status == "401" ) {
        $message = isset($urlMetrics->status) ? $urlMetrics->error_message : __('Looking for requested info...', WD_SEO_PREFIX);
        return array( 'error' => TRUE, 'message' => $message );
      }
      else {
        foreach ( $this->urlMetric as $response_field => $urlMetric ) {
          if ( !isset($urlMetrics->$response_field) || $urlMetrics->$response_field === "" ) {
            unset($this->urlMetric[$response_field]);
          }
          else {
            $this->urlMetric[$response_field]['value'] = $urlMetrics->$response_field;
          }
        }
      }
      return $this->urlMetric;
    }
    else {
      return FALSE;
    }
  }
}
