<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Overview controller class.
 */
class WDSeosearch_analyticsController extends WDSeoAdminController {
  private $search_analytics;
  private $device;
  /**
   * @var int How many queries get from Google search analytics (-1 for all).
   */
  private $row_limit = -1;

  public function __construct( $page = NULL, $task = NULL ) {
    $this->is_active = WDSeo()->is_active(false);
    if (!$this->is_active) {
      $this->row_limit = 10;
    }
    $this->search_analytics = $this->search_analytics();
    parent::__construct($page, $task);
  }

  private function search_analytics() {
    $this->device = WD_SEO_Library::get('device', 'desktop');

    $crawl = new WD_SEO_CRAWL;
    $paged = WD_SEO_Library::get('paged', 1);
    $search = WD_SEO_Library::get('s', '');
    $country = WD_SEO_Library::get('country', '');

    $search_analytics = $crawl->search_analytics($this->device, $search, $country);

    if ( !isset($search_analytics['error']) && $search_analytics !== FALSE && $search_analytics !== 0 ) {
      $search_analytics_arr = array();
      // Sort.
      usort($search_analytics, array($this, 'sort'));
      if ( $this->row_limit !== -1 ) {
        $search_analytics = array_slice($search_analytics, 0, $this->row_limit);
      }
      // Paged.
      $search_analytics_arr['queries'] = array_slice($search_analytics, ($paged - 1) * WD_SEO_HTML::$total_in_page, WD_SEO_HTML::$total_in_page);
      // Total.
      $search_analytics_arr['count'] = count($search_analytics);

      return $search_analytics_arr;
    }

    return $search_analytics;
  }

  /**
   * Display.
   */
  public function display() {
    // Get the page view if exist or global view otherwise.
    $view_class = class_exists($this->class_prefix . 'View') ? $this->class_prefix . 'View' : WD_SEO_CLASS_PREFIX . 'AdminView';
    $view = new $view_class();

    echo $view->display($this->search_analytics, $this->filters(), $this->is_active);
  }

  /**
   * Sort.
   *
   * @param $a
   * @param $b
   *
   * @return int
   */
  public function sort( $a, $b ) {
    $orderby = WD_SEO_Library::get('orderby', 'impressions');
    $order = WD_SEO_Library::get('order', 'desc');

    // For Clicks, Impressions, CTR, Position.
    $result = (int) $a->$orderby < (int) $b->$orderby;

    return ($order === 'desc') ? $result : (!$result);
  }

  /**
   * Filters.
   *
   * @return array
   */
  public function filters() {
    $filters = array(
      'country' => array(
        '' => __('Worldwide',  WD_SEO_PREFIX),
        'ABW' => __('Aruba',  WD_SEO_PREFIX),
        'AFG' => __('Afghanistan',  WD_SEO_PREFIX),
        'AGO' => __('Angola',  WD_SEO_PREFIX),
        'AIA' => __('Anguilla',  WD_SEO_PREFIX),
        'ALA' => __('Åland Islands',  WD_SEO_PREFIX),
        'ALB' => __('Albania',  WD_SEO_PREFIX),
        'AND' => __('Andorra',  WD_SEO_PREFIX),
        'ARE' => __('United Arab Emirates',  WD_SEO_PREFIX),
        'ARG' => __('Argentina',  WD_SEO_PREFIX),
        'ARM' => __('Armenia',  WD_SEO_PREFIX),
        'ASM' => __('American Samoa',  WD_SEO_PREFIX),
        'ATA' => __('Antarctica',  WD_SEO_PREFIX),
        'ATF' => __('French Southern Territories',  WD_SEO_PREFIX),
        'ATG' => __('Antigua and Barbuda',  WD_SEO_PREFIX),
        'AUS' => __('Australia',  WD_SEO_PREFIX),
        'AUT' => __('Austria',  WD_SEO_PREFIX),
        'AZE' => __('Azerbaijan',  WD_SEO_PREFIX),
        'BDI' => __('Burundi',  WD_SEO_PREFIX),
        'BEL' => __('Belgium',  WD_SEO_PREFIX),
        'BEN' => __('Benin',  WD_SEO_PREFIX),
        'BES' => __('Bonaire, Sint Eustatius and Saba',  WD_SEO_PREFIX),
        'BFA' => __('Burkina Faso',  WD_SEO_PREFIX),
        'BGD' => __('Bangladesh',  WD_SEO_PREFIX),
        'BGR' => __('Bulgaria',  WD_SEO_PREFIX),
        'BHR' => __('Bahrain',  WD_SEO_PREFIX),
        'BHS' => __('Bahamas',  WD_SEO_PREFIX),
        'BIH' => __('Bosnia and Herzegovina',  WD_SEO_PREFIX),
        'BLM' => __('Saint Barthélemy',  WD_SEO_PREFIX),
        'BLR' => __('Belarus',  WD_SEO_PREFIX),
        'BLZ' => __('Belize',  WD_SEO_PREFIX),
        'BMU' => __('Bermuda',  WD_SEO_PREFIX),
        'BOL' => __('Bolivia, Plurinational State of',  WD_SEO_PREFIX),
        'BRA' => __('Brazil',  WD_SEO_PREFIX),
        'BRB' => __('Barbados',  WD_SEO_PREFIX),
        'BRN' => __('Brunei Darussalam',  WD_SEO_PREFIX),
        'BTN' => __('Bhutan',  WD_SEO_PREFIX),
        'BVT' => __('Bouvet Island',  WD_SEO_PREFIX),
        'BWA' => __('Botswana',  WD_SEO_PREFIX),
        'CAF' => __('Central African Republic',  WD_SEO_PREFIX),
        'CAN' => __('Canada',  WD_SEO_PREFIX),
        'CCK' => __('Cocos (Keeling) Islands',  WD_SEO_PREFIX),
        'CHE' => __('Switzerland',  WD_SEO_PREFIX),
        'CHL' => __('Chile',  WD_SEO_PREFIX),
        'CHN' => __('China',  WD_SEO_PREFIX),
        'CIV' => __('Côte d\'Ivoire',  WD_SEO_PREFIX),
        'CMR' => __('Cameroon',  WD_SEO_PREFIX),
        'COD' => __('Congo, the Democratic Republic of the',  WD_SEO_PREFIX),
        'COG' => __('Congo',  WD_SEO_PREFIX),
        'COK' => __('Cook Islands',  WD_SEO_PREFIX),
        'COL' => __('Colombia',  WD_SEO_PREFIX),
        'COM' => __('Comoros',  WD_SEO_PREFIX),
        'CPV' => __('Cape Verde',  WD_SEO_PREFIX),
        'CRI' => __('Costa Rica',  WD_SEO_PREFIX),
        'CUB' => __('Cuba',  WD_SEO_PREFIX),
        'CUW' => __('Curaçao',  WD_SEO_PREFIX),
        'CXR' => __('Christmas Island',  WD_SEO_PREFIX),
        'CYM' => __('Cayman Islands',  WD_SEO_PREFIX),
        'CYP' => __('Cyprus',  WD_SEO_PREFIX),
        'CZE' => __('Czech Republic',  WD_SEO_PREFIX),
        'DEU' => __('Germany',  WD_SEO_PREFIX),
        'DJI' => __('Djibouti',  WD_SEO_PREFIX),
        'DMA' => __('Dominica',  WD_SEO_PREFIX),
        'DNK' => __('Denmark',  WD_SEO_PREFIX),
        'DOM' => __('Dominican Republic',  WD_SEO_PREFIX),
        'DZA' => __('Algeria',  WD_SEO_PREFIX),
        'ECU' => __('Ecuador',  WD_SEO_PREFIX),
        'EGY' => __('Egypt',  WD_SEO_PREFIX),
        'ERI' => __('Eritrea',  WD_SEO_PREFIX),
        'ESH' => __('Western Sahara',  WD_SEO_PREFIX),
        'ESP' => __('Spain',  WD_SEO_PREFIX),
        'EST' => __('Estonia',  WD_SEO_PREFIX),
        'ETH' => __('Ethiopia',  WD_SEO_PREFIX),
        'FIN' => __('Finland',  WD_SEO_PREFIX),
        'FJI' => __('Fiji',  WD_SEO_PREFIX),
        'FLK' => __('Falkland Islands (Malvinas)',  WD_SEO_PREFIX),
        'FRA' => __('France',  WD_SEO_PREFIX),
        'FRO' => __('Faroe Islands',  WD_SEO_PREFIX),
        'FSM' => __('Micronesia, Federated States of',  WD_SEO_PREFIX),
        'GAB' => __('Gabon',  WD_SEO_PREFIX),
        'GBR' => __('United Kingdom',  WD_SEO_PREFIX),
        'GEO' => __('Georgia',  WD_SEO_PREFIX),
        'GGY' => __('Guernsey',  WD_SEO_PREFIX),
        'GHA' => __('Ghana',  WD_SEO_PREFIX),
        'GIB' => __('Gibraltar',  WD_SEO_PREFIX),
        'GIN' => __('Guinea',  WD_SEO_PREFIX),
        'GLP' => __('Guadeloupe',  WD_SEO_PREFIX),
        'GMB' => __('Gambia',  WD_SEO_PREFIX),
        'GNB' => __('Guinea-Bissau',  WD_SEO_PREFIX),
        'GNQ' => __('Equatorial Guinea',  WD_SEO_PREFIX),
        'GRC' => __('Greece',  WD_SEO_PREFIX),
        'GRD' => __('Grenada',  WD_SEO_PREFIX),
        'GRL' => __('Greenland',  WD_SEO_PREFIX),
        'GTM' => __('Guatemala',  WD_SEO_PREFIX),
        'GUF' => __('French Guiana',  WD_SEO_PREFIX),
        'GUM' => __('Guam',  WD_SEO_PREFIX),
        'GUY' => __('Guyana',  WD_SEO_PREFIX),
        'HKG' => __('Hong Kong',  WD_SEO_PREFIX),
        'HMD' => __('Heard Island and McDonald Islands',  WD_SEO_PREFIX),
        'HND' => __('Honduras',  WD_SEO_PREFIX),
        'HRV' => __('Croatia',  WD_SEO_PREFIX),
        'HTI' => __('Haiti',  WD_SEO_PREFIX),
        'HUN' => __('Hungary',  WD_SEO_PREFIX),
        'IDN' => __('Indonesia',  WD_SEO_PREFIX),
        'IMN' => __('Isle of Man',  WD_SEO_PREFIX),
        'IND' => __('India',  WD_SEO_PREFIX),
        'IOT' => __('British Indian Ocean Territory',  WD_SEO_PREFIX),
        'IRL' => __('Ireland',  WD_SEO_PREFIX),
        'IRN' => __('Iran, Islamic Republic of',  WD_SEO_PREFIX),
        'IRQ' => __('Iraq',  WD_SEO_PREFIX),
        'ISL' => __('Iceland',  WD_SEO_PREFIX),
        'ISR' => __('Israel',  WD_SEO_PREFIX),
        'ITA' => __('Italy',  WD_SEO_PREFIX),
        'JAM' => __('Jamaica',  WD_SEO_PREFIX),
        'JEY' => __('Jersey',  WD_SEO_PREFIX),
        'JOR' => __('Jordan',  WD_SEO_PREFIX),
        'JPN' => __('Japan',  WD_SEO_PREFIX),
        'KAZ' => __('Kazakhstan',  WD_SEO_PREFIX),
        'KEN' => __('Kenya',  WD_SEO_PREFIX),
        'KGZ' => __('Kyrgyzstan',  WD_SEO_PREFIX),
        'KHM' => __('Cambodia',  WD_SEO_PREFIX),
        'KIR' => __('Kiribati',  WD_SEO_PREFIX),
        'KNA' => __('Saint Kitts and Nevis',  WD_SEO_PREFIX),
        'KOR' => __('Korea, Republic of',  WD_SEO_PREFIX),
        'KWT' => __('Kuwait',  WD_SEO_PREFIX),
        'LAO' => __('Lao People\'s Democratic Republic',  WD_SEO_PREFIX),
        'LBN' => __('Lebanon',  WD_SEO_PREFIX),
        'LBR' => __('Liberia',  WD_SEO_PREFIX),
        'LBY' => __('Libya',  WD_SEO_PREFIX),
        'LCA' => __('Saint Lucia',  WD_SEO_PREFIX),
        'LIE' => __('Liechtenstein',  WD_SEO_PREFIX),
        'LKA' => __('Sri Lanka',  WD_SEO_PREFIX),
        'LSO' => __('Lesotho',  WD_SEO_PREFIX),
        'LTU' => __('Lithuania',  WD_SEO_PREFIX),
        'LUX' => __('Luxembourg',  WD_SEO_PREFIX),
        'LVA' => __('Latvia',  WD_SEO_PREFIX),
        'MAC' => __('Macao',  WD_SEO_PREFIX),
        'MAF' => __('Saint Martin (French part)',  WD_SEO_PREFIX),
        'MAR' => __('Morocco',  WD_SEO_PREFIX),
        'MCO' => __('Monaco',  WD_SEO_PREFIX),
        'MDA' => __('Moldova, Republic of',  WD_SEO_PREFIX),
        'MDG' => __('Madagascar',  WD_SEO_PREFIX),
        'MDV' => __('Maldives',  WD_SEO_PREFIX),
        'MEX' => __('Mexico',  WD_SEO_PREFIX),
        'MHL' => __('Marshall Islands',  WD_SEO_PREFIX),
        'MKD' => __('Macedonia, the former Yugoslav Republic of',  WD_SEO_PREFIX),
        'MLI' => __('Mali',  WD_SEO_PREFIX),
        'MLT' => __('Malta',  WD_SEO_PREFIX),
        'MMR' => __('Myanmar',  WD_SEO_PREFIX),
        'MNE' => __('Montenegro',  WD_SEO_PREFIX),
        'MNG' => __('Mongolia',  WD_SEO_PREFIX),
        'MNP' => __('Northern Mariana Islands',  WD_SEO_PREFIX),
        'MOZ' => __('Mozambique',  WD_SEO_PREFIX),
        'MRT' => __('Mauritania',  WD_SEO_PREFIX),
        'MSR' => __('Montserrat',  WD_SEO_PREFIX),
        'MTQ' => __('Martinique',  WD_SEO_PREFIX),
        'MUS' => __('Mauritius',  WD_SEO_PREFIX),
        'MWI' => __('Malawi',  WD_SEO_PREFIX),
        'MYS' => __('Malaysia',  WD_SEO_PREFIX),
        'MYT' => __('Mayotte',  WD_SEO_PREFIX),
        'NAM' => __('Namibia',  WD_SEO_PREFIX),
        'NCL' => __('New Caledonia',  WD_SEO_PREFIX),
        'NER' => __('Niger',  WD_SEO_PREFIX),
        'NFK' => __('Norfolk Island',  WD_SEO_PREFIX),
        'NGA' => __('Nigeria',  WD_SEO_PREFIX),
        'NIC' => __('Nicaragua',  WD_SEO_PREFIX),
        'NIU' => __('Niue',  WD_SEO_PREFIX),
        'NLD' => __('Netherlands',  WD_SEO_PREFIX),
        'NOR' => __('Norway',  WD_SEO_PREFIX),
        'NPL' => __('Nepal',  WD_SEO_PREFIX),
        'NRU' => __('Nauru',  WD_SEO_PREFIX),
        'NZL' => __('New Zealand',  WD_SEO_PREFIX),
        'OMN' => __('Oman',  WD_SEO_PREFIX),
        'PAK' => __('Pakistan',  WD_SEO_PREFIX),
        'PAN' => __('Panama',  WD_SEO_PREFIX),
        'PCN' => __('Pitcairn',  WD_SEO_PREFIX),
        'PER' => __('Peru',  WD_SEO_PREFIX),
        'PHL' => __('Philippines',  WD_SEO_PREFIX),
        'PLW' => __('Palau',  WD_SEO_PREFIX),
        'PNG' => __('Papua New Guinea',  WD_SEO_PREFIX),
        'POL' => __('Poland',  WD_SEO_PREFIX),
        'PRI' => __('Puerto Rico',  WD_SEO_PREFIX),
        'PRK' => __('Korea, Democratic People\'s Republic of',  WD_SEO_PREFIX),
        'PRT' => __('Portugal',  WD_SEO_PREFIX),
        'PRY' => __('Paraguay',  WD_SEO_PREFIX),
        'PSE' => __('Palestinian Territory, Occupied',  WD_SEO_PREFIX),
        'PYF' => __('French Polynesia',  WD_SEO_PREFIX),
        'QAT' => __('Qatar',  WD_SEO_PREFIX),
        'REU' => __('Réunion',  WD_SEO_PREFIX),
        'ROU' => __('Romania',  WD_SEO_PREFIX),
        'RUS' => __('Russian Federation',  WD_SEO_PREFIX),
        'RWA' => __('Rwanda',  WD_SEO_PREFIX),
        'SAU' => __('Saudi Arabia',  WD_SEO_PREFIX),
        'SDN' => __('Sudan',  WD_SEO_PREFIX),
        'SEN' => __('Senegal',  WD_SEO_PREFIX),
        'SGP' => __('Singapore',  WD_SEO_PREFIX),
        'SGS' => __('South Georgia and the South Sandwich Islands',  WD_SEO_PREFIX),
        'SHN' => __('Saint Helena, Ascension and Tristan da Cunha',  WD_SEO_PREFIX),
        'SJM' => __('Svalbard and Jan Mayen',  WD_SEO_PREFIX),
        'SLB' => __('Solomon Islands',  WD_SEO_PREFIX),
        'SLE' => __('Sierra Leone',  WD_SEO_PREFIX),
        'SLV' => __('El Salvador',  WD_SEO_PREFIX),
        'SMR' => __('San Marino',  WD_SEO_PREFIX),
        'SOM' => __('Somalia',  WD_SEO_PREFIX),
        'SPM' => __('Saint Pierre and Miquelon',  WD_SEO_PREFIX),
        'SRB' => __('Serbia',  WD_SEO_PREFIX),
        'SSD' => __('South Sudan',  WD_SEO_PREFIX),
        'STP' => __('Sao Tome and Principe',  WD_SEO_PREFIX),
        'SUR' => __('Suriname',  WD_SEO_PREFIX),
        'SVK' => __('Slovakia',  WD_SEO_PREFIX),
        'SVN' => __('Slovenia',  WD_SEO_PREFIX),
        'SWE' => __('Sweden',  WD_SEO_PREFIX),
        'SWZ' => __('Swaziland',  WD_SEO_PREFIX),
        'SXM' => __('Sint Maarten (Dutch part)',  WD_SEO_PREFIX),
        'SYC' => __('Seychelles',  WD_SEO_PREFIX),
        'SYR' => __('Syrian Arab Republic',  WD_SEO_PREFIX),
        'TCA' => __('Turks and Caicos Islands',  WD_SEO_PREFIX),
        'TCD' => __('Chad',  WD_SEO_PREFIX),
        'TGO' => __('Togo',  WD_SEO_PREFIX),
        'THA' => __('Thailand',  WD_SEO_PREFIX),
        'TJK' => __('Tajikistan',  WD_SEO_PREFIX),
        'TKL' => __('Tokelau',  WD_SEO_PREFIX),
        'TKM' => __('Turkmenistan',  WD_SEO_PREFIX),
        'TLS' => __('Timor-Leste',  WD_SEO_PREFIX),
        'TON' => __('Tonga',  WD_SEO_PREFIX),
        'TTO' => __('Trinidad and Tobago',  WD_SEO_PREFIX),
        'TUN' => __('Tunisia',  WD_SEO_PREFIX),
        'TUR' => __('Turkey',  WD_SEO_PREFIX),
        'TUV' => __('Tuvalu',  WD_SEO_PREFIX),
        'TWN' => __('Taiwan, Province of China',  WD_SEO_PREFIX),
        'TZA' => __('Tanzania, United Republic of',  WD_SEO_PREFIX),
        'UGA' => __('Uganda',  WD_SEO_PREFIX),
        'UKR' => __('Ukraine',  WD_SEO_PREFIX),
        'UMI' => __('United States Minor Outlying Islands',  WD_SEO_PREFIX),
        'URY' => __('Uruguay',  WD_SEO_PREFIX),
        'USA' => __('United States',  WD_SEO_PREFIX),
        'UZB' => __('Uzbekistan',  WD_SEO_PREFIX),
        'VAT' => __('Holy See (Vatican City State)',  WD_SEO_PREFIX),
        'VCT' => __('Saint Vincent and the Grenadines',  WD_SEO_PREFIX),
        'VEN' => __('Venezuela, Bolivarian Republic of',  WD_SEO_PREFIX),
        'VGB' => __('Virgin Islands, British',  WD_SEO_PREFIX),
        'VIR' => __('Virgin Islands, U.S.',  WD_SEO_PREFIX),
        'VNM' => __('Viet Nam',  WD_SEO_PREFIX),
        'VUT' => __('Vanuatu',  WD_SEO_PREFIX),
        'WLF' => __('Wallis and Futuna',  WD_SEO_PREFIX),
        'WSM' => __('Samoa',  WD_SEO_PREFIX),
        'YEM' => __('Yemen',  WD_SEO_PREFIX),
        'ZAF' => __('South Africa',  WD_SEO_PREFIX),
        'ZMB' => __('Zambia',  WD_SEO_PREFIX),
        'ZWE' => __('Zimbabwe',  WD_SEO_PREFIX),
      ),
    );

    return $filters;
  }
}
