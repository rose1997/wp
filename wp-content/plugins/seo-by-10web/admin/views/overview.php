<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Settings page view class.
 */
class WDSeooverviewView extends WDSeoAdminView {
  /**
   * Display page.
   *
   * @param $options
   * @param $authorization_url
   * @param $issues
   * @param $moz_url_metrics
   * @param $recommends_problems
   */
  public function display($options, $authorization_url, $issues, $moz_url_metrics, $recommends_problems) {
    ob_start();
    echo $this->body($options, $authorization_url, $issues, $moz_url_metrics, $recommends_problems);

    // Pass the content to form.
    echo $this->form(ob_get_clean());
  }

  /**
   * Generate page body.
   *
   * @param $options
   * @param $authorization_url
   * @param $issues
   * @param $moz_url_metrics
   * @param $recommends_problems
   *
   * @return string Body html.
   */
  private function body($options, $authorization_url, $issues, $moz_url_metrics, $recommends_problems) {
    $fix_url = add_query_arg(array('page' => WD_SEO_PREFIX . '_search_console'), admin_url('admin.php'));
    $search_analytics_url = add_query_arg(array('page' => WD_SEO_PREFIX . '_search_analytics'), admin_url('admin.php'));
    ob_start();
    ?>
    <div class="wd-table">
      <div class="wd-table-col wd-table-col-50 wd-table-col-left">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('Run SEO analysis of your site', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <?php
            if ( isset($issues['error']) ) {
              if ( isset($issues['message']) ) {
                echo WD_SEO_HTML::message(0, $issues['message'], 'error');
              }
              if ( !isset($issues['interrupt']) ) {
                ?>
            <span class="wd-group">
              <input type="hidden" name="authorization_url" value="<?php echo $authorization_url; ?>" />
                  <?php
                  $buttons = array(
                    'get_google_authorization_code' => array(
                      'title' => __('Get Google Authorization Code', WD_SEO_PREFIX),
                      'value' => 'get_google_authorization_code',
                      'name' => 'get_google_authorization_code',
                      'class' => 'button-primary',
                    ),
                  );
                  echo $this->buttons($buttons, TRUE);
                  ?>
                  <p class="description"><?php _e('To allow to fetch your Google Search Console information, please enter your Google Authorization Code.', WD_SEO_PREFIX); ?></p>
            </span>
            <span class="wd-group">
              <input type="text" name="code" value="" />
              <p class="description"><?php _e('Enter your Google Authorization Code and press the Authenticate button.', WD_SEO_PREFIX); ?></p>
            </span>
            <span class="wd-group wd-right">
              <?php
              $buttons = array(
                'authenticate' => array(
                  'title' => __('Authenticate', WD_SEO_PREFIX),
                  'value' => 'authenticate',
                  'name' => 'task',
                  'class' => 'button-primary authenticate-btn wdseo-hide',
                ),
              );
              echo $this->buttons($buttons, TRUE);
              ?>
            </span>
                <?php
              }
            }
            else {
              ?>
            <span class="wd-group">
              <?php
              $buttons = array(
                'reauthenticate' => array(
                  'title' => __('Reauthenticate with Google', WD_SEO_PREFIX),
                  'value' => 'reauthenticate',
                  'name' => 'task',
                  'class' => 'button-secondary',
                ),
              );
              echo $this->buttons($buttons, TRUE);
              ?>
            </span>
            <span class="wd-group wd-center">
              <?php
              $total_errors = 0;
              foreach ( $issues as $platform => $categories ) {
                foreach ( $categories as $category => $category_arr ) {
                  $total_errors += $issues[$platform][$category]['errors']['count'];
                  ?>
              <div class="wd-overview-item">
                <strong class=""><?php echo $issues[$platform][$category]['errors']['count']; ?></strong>
                <?php
                if ( $platform == 'web' ) {
                  $platform_title = __('Desktop', WD_SEO_PREFIX);
                }
                elseif ( $platform == 'smartphoneonly' ) {
                  $platform_title = __('Mobile', WD_SEO_PREFIX);
                }
                else {
                  $platform_title = $platform;
                }
                ?>
                <strong><?php echo ucfirst($platform_title); ?></strong>
                <span>
                  <?php
                  echo sprintf(__('%s URLs'), $issues[$platform][$category]['title']);
                  ?>
                </span>
              </div>
                  <?php
                }
              }
              if ( $total_errors == 0 ) {
                ?>
              <div class="wd-overview-item wd-full-width">
                <strong><?php _e('No errors detected', WD_SEO_PREFIX); ?></strong>
              </div>
                <?php
              }
              ?>
            </span>
            <span class="wd-group">
              <span>
                <a class="button-primary wd-left" href="<?php echo $search_analytics_url; ?>">
                  <?php _e('Search analytics', WD_SEO_PREFIX); ?>
                </a>
              </span>
              <?php
              if ( $total_errors > 0 ) {
                ?>
              <span class="wd-float-right">
                <a class="button-primary" href="<?php echo $fix_url; ?>">
                  <?php _e('Fix issues', WD_SEO_PREFIX); ?>
                </a>
              </span>
                <?php
              }
              ?>
            </span>
              <?php
            }
            ?>
          </div>
        </div>
        <?php
        if ( $moz_url_metrics === FALSE ) {
          ?>
          <div class="wd-box-section">
            <div class="wd-box-title">
              <strong><?php _e('SEO Moz Account', WD_SEO_PREFIX); ?></strong>
            </div>
            <div class="wd-box-content">
            <span class="wd-group">
              <?php echo sprintf(__('%s to gain access to reports that will tell you how your site stacks up against the competition with all of the important SEO measurement tools - ranking, links, and much more.', WD_SEO_PREFIX), '<a href="http://moz.com/products/api" target="_blank">' . __('Sign-up for a free account', WD_SEO_PREFIX) . '</a>'); ?>
            </span>
              <span class="wd-group">
              <label class="wd-label" for="access-id"><?php _e('Access ID', WD_SEO_PREFIX); ?></label>
              <input id="access-id" name="wd_settings[moz_access_id]" value="<?php echo $options->moz_access_id; ?>" type="text" />
            </span>
              <span class="wd-group">
              <label class="wd-label" for="secret-key"><?php _e('Secret Key', WD_SEO_PREFIX); ?></label>
              <input id="secret-key" name="wd_settings[moz_secret_id]" value="<?php echo $options->moz_secret_id; ?>" type="text" />
            </span>
              <span class="wd-group wd-right">
              <?php
              $buttons = array(
                'save' => array(
                  'title' => __('Authenticate', WD_SEO_PREFIX),
                  'value' => 'save',
                  'name' => 'task',
                  'class' => 'button-primary',
                ),
              );
              echo $this->buttons($buttons, TRUE);
              ?>
            </span>
            </div>
          </div>
          <?php
        }
        else {
          ?>
          <div class="wd-box-section">
            <div class="wd-box-title">
              <strong><?php _e('SEO MOZ statistics', WD_SEO_PREFIX); ?></strong>
            </div>
            <div class="wd-box-content">
            <span class="wd-group">
              <input type="hidden" name="wd_settings[moz_access_id]" value="" />
              <input type="hidden" name="wd_settings[moz_secret_id]" value="" />
              <?php
              $buttons = array(
                'save' => array(
                  'title' => __('Reauthenticate with MOZ', WD_SEO_PREFIX),
                  'value' => 'save',
                  'name' => 'task',
                  'class' => 'button-secondary',
                ),
              );
              echo $this->buttons($buttons, TRUE);
              ?>
            </span>
              <?php
              if ( isset($moz_url_metrics['error']) ) {
                if ( isset($moz_url_metrics['message']) ) {
                  echo WD_SEO_HTML::message(0, $moz_url_metrics['message'], 'error');
                }
              }
              else {
                foreach ( $moz_url_metrics as $response_field => $urlMetric ) {
                  $alternate = (!isset($alternate) || $alternate == 'alternate') ? '' : 'alternate';
                  ?>
                  <span class="wd-group wd-moz-metric <?php echo $alternate; ?>">
              <label class="wd-label">
                <span><?php echo $urlMetric['title']; ?></span>
                <span class="wd-float-right wd-font-weight-normal"><?php echo $urlMetric['value']; ?></span>
              </label>
              <p class="description"><?php echo $urlMetric['description']; ?></p>
            </span>
                  <?php
                }
              }
              ?>
            </div>
          </div>
          <?php
        }
        ?>
      </div>
      <div class="wd-table-col wd-table-col-50 wd-table-col-right">
        <?php
        // Problems box.
        ?>
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('Problems', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <?php
            if ( !empty($recommends_problems['problems']) ) {
              foreach ( $recommends_problems['problems'] as $key => $values ) {
                foreach ( $values as $val ) {
                  ?>
                  <span class="wd-group">
              <div class="error notice notice-error">
                <p><?php echo $val['message']; ?></p>
              </div>
            </span>
                  <?php
                }
              }
            }
            else {
              ?>
              <span class="wd-group wd-center">
              <div class="wd-overview-item wd-full-width">
                <strong><?php _e('No problems found', WD_SEO_PREFIX); ?></strong>
              </div>
            </span>
              <?php
            }
            ?>
          </div>
        </div>
        <?php
        // Recommendations box.
        if ( !empty($recommends_problems['recommends']) ) {
          ?>
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('Recommendations', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <?php
            foreach ( $recommends_problems['recommends'] as $key => $values ) {
              foreach ( $values as $val ) {
                ?>
            <span class="wd-group">
              <div class="notice notice-warning is-dismissible" data-value="<?php echo $val['key']; ?>">
                <p><?php echo $val['message']; ?></p>
              </div>
            </span>
                <?php
              }
            }
            ?>
          </div>
        </div>
          <?php
        }
        ?>
      </div>
    </div>
    <?php

    return ob_get_clean();
  }
}
