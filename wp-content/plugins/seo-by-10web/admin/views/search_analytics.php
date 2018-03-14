<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Settings page view class.
 */
class WDSeosearch_analyticsView extends WDSeoAdminView {
  /**
   * Display page.
   */
  public function display($crawl_errors, $filters, $is_active) {
    wp_enqueue_style(WD_SEO_PREFIX . '_select2');
    wp_enqueue_script(WD_SEO_PREFIX . '_select2');

    ob_start();
    echo $this->body($crawl_errors, $filters, $is_active);
    // Pass the content to form.
    echo $this->form(ob_get_clean());
  }

  /**
   * Generate page body.
   *
   * @param string $authorization_url
   *
   * @return string Body html.
   */
  private function body($search_analytics, $filters, $is_active) {
    if ( isset($search_analytics['error']) ) {
      if ( isset($search_analytics['message']) ) {
        echo WD_SEO_HTML::message(0, $search_analytics['message'], 'error');
      }

      return;
    }
    elseif ( $search_analytics === FALSE ) {
      echo WD_SEO_HTML::message(0, __('There is no data.', WD_SEO_PREFIX), 'error');

      return;
    }
    $total = isset($search_analytics['count']) ? $search_analytics['count'] : 0;
    $search_analytics = isset($search_analytics['queries']) ? $search_analytics['queries'] : array();

    $page = WD_SEO_Library::get('page');
    $orderby = WD_SEO_Library::get('orderby', 'impressions');
    $order = WD_SEO_Library::get('order', 'desc');
    $device = WD_SEO_Library::get('device', 'desktop');
    $country = WD_SEO_Library::get('country');

    $page_url = add_query_arg(array(
      'page' => $page,
      'device' => $device,
      'country' => $country,
    ), admin_url('admin.php'));

    $devices = array(
      'desktop' => __('Desktop', WD_SEO_PREFIX),
      'mobile' => __('Mobile', WD_SEO_PREFIX),
      'tablet' => __('Tablet', WD_SEO_PREFIX),
    );

    ob_start();
    ?>
    <div>
      <div class="wd-load-tabs">
        <ul class="wdseo-tabs">
          <?php
          foreach ( $devices as $key => $value ) {
            ?>
            <li class="tabs<?php echo $key == $device ? ' ui-tabs-active' : ''; ?>">
              <a href="<?php echo add_query_arg(array('device' => $key), $page_url); ?>" class="wdseo-tablink">
                <?php echo $value; ?>
              </a>
            </li>
            <?php
          }
          ?>
        </ul>
        <div class="wdseo-section">
          <?php
          echo WD_SEO_HTML::pagination($total, TRUE, $filters);
          ?>
          <table class="adminlist table table-striped wp-list-table widefat fixed pages">
            <thead>
            <tr>
              <th class="col_queries column-primary"><?php _e('Queries', WD_SEO_PREFIX); ?></th>
              <?php echo WD_SEO_HTML::ordering('clicks', $orderby, $order, __('Clicks', WD_SEO_PREFIX), $page_url, 'col_clicks wd-left', $is_active); ?>
              <?php echo WD_SEO_HTML::ordering('impressions', $orderby, $order, __('Impressions', WD_SEO_PREFIX), $page_url, 'col_impressions wd-left', $is_active); ?>
              <?php echo WD_SEO_HTML::ordering('ctr', $orderby, $order, __('CTR', WD_SEO_PREFIX), $page_url, 'col_ctr wd-left', $is_active); ?>
              <?php echo WD_SEO_HTML::ordering('position', $orderby, $order, __('Position', WD_SEO_PREFIX), $page_url, 'col_position wd-left', $is_active); ?>
            </tr>
            </thead>
            <tbody>
            <?php
            if ( !empty($search_analytics) ) {
              foreach ( $search_analytics as $key => $search_analytic ) {
                $alternate = (!isset($alternate) || $alternate == 'alternate') ? '' : 'alternate';
                ?>
                <tr id="tr_<?php echo $key; ?>" class="row<?php echo $key % 2; ?> <?php echo $alternate; ?>">
                  <td class="col_queries column-primary" data-colname="<?php _e('Queries', WD_SEO_PREFIX); ?>">
                    <?php echo $search_analytic->keys[0]; ?>
                    <button class="toggle-row" type="button">
                      <span class="screen-reader-text"><?php _e('Show more details', WD_SEO_PREFIX); ?></span>
                    </button>
                  </td>
                  <td class="col_clicks wd-left" data-colname="<?php _e('Clicks', WD_SEO_PREFIX); ?>">
                    <?php echo $search_analytic->clicks; ?>
                  </td>
                  <td class="col_impressions wd-left" data-colname="<?php _e('Impressions', WD_SEO_PREFIX); ?>">
                    <?php echo $search_analytic->impressions; ?>
                  </td>
                  <td class="col_ctr wd-left" data-colname="<?php _e('CTR', WD_SEO_PREFIX); ?>">
                    <?php echo round($search_analytic->ctr * 100, 2); ?>%
                  </td>
                  <td class="col_position wd-left" data-colname="<?php _e('Position', WD_SEO_PREFIX); ?>">
                    <?php echo round($search_analytic->position, 1); ?>
                  </td>
                </tr>
                <?php
              }
            }
            else {
              echo WD_SEO_HTML::no_items('items');
            }
            ?>
            </tbody>
          </table>
          <?php
          echo WD_SEO_HTML::pagination($total);
          ?>
        </div>
      </div>
    </div>
    <?php

    return ob_get_clean();
  }
}
