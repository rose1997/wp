<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Settings page view class.
 */
class WDSeosearch_consoleView extends WDSeoAdminView {
  /**
   * Thickbox parameters.
   */
  private $width = 300;
  private $height = 200;

  /**
   * Display page.
   */
  public function display($crawl_errors, $filters) {
    wp_enqueue_style('jquery-ui-tooltip');
    wp_enqueue_script('jquery-ui-tooltip');

    ob_start();
    echo $this->body($crawl_errors, $filters);

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
  private function body($crawl_errors, $filters) {
    if ( $crawl_errors === FALSE ) {
      echo WD_SEO_HTML::message(0, __('To allow to fetch your Google Search Console information, please Authorized with Google.', WD_SEO_PREFIX), 'error');
      return;
    }
    if ( $crawl_errors === 0 ) {
      echo WD_SEO_HTML::message(0, __('No errors detected.', WD_SEO_PREFIX));
      return;
    }

    add_thickbox();

    $page = WD_SEO_Library::get('page');
    $orderby = WD_SEO_Library::get('orderby', 'pageUrl');
    $order = WD_SEO_Library::get('order', 'asc');
    $platform = WD_SEO_Library::get('platform', 'web');
    $category = WD_SEO_Library::get('category', 'notFound');

    // Get first platform if current not found.
    if ( !array_key_exists($platform, $crawl_errors) ) {
      foreach ( $crawl_errors as $key => $crawl_error ) {
        $platform = $key;
        break;
      }
    }
    // Get first category if current not found.
    if ( !array_key_exists($category, $crawl_errors[$platform]) ) {
      foreach ( $crawl_errors[$platform] as $key => $crawl_error ) {
        $category = $key;
        break;
      }
    }

    $page_url = add_query_arg(array(
      'page' => $page,
      'platform' => $platform,
      'category' => $category,
    ), admin_url('admin.php'));

    $categories = $crawl_errors[$platform];
    $category_arr = $categories[$category];

    ob_start();
    ?>
    <div>
      <div class="wd-load-tabs">
        <ul class="wdseo-tabs">
          <?php
          foreach ( $crawl_errors as $_platform => $_categories ) {
            $tooltip = (!empty($_categories['tooltip-info'])) ? $_categories['tooltip-info'] : FALSE;
            ?>
            <li class="tabs<?php echo $_platform == $platform ? ' ui-tabs-active' : ''; ?>">
              <a href="<?php echo add_query_arg(array('platform' => $_platform), $page_url); ?>" class="wdseo-tablink">
                <?php
                if ( $_platform == 'web' ) {
                  $platform_title = __('Desktop', WD_SEO_PREFIX);
                }
                elseif ( $_platform == 'smartphoneonly' ) {
                  $platform_title = __('Mobile', WD_SEO_PREFIX);
                }
                else {
                  $platform_title = $_platform;
                }
                echo ucfirst($platform_title);
                ?>
                <?php echo ($tooltip) ? '<i class="dashicons dashicons-editor-help" data-wdseo-tooltip-key="' . $_platform . '"></i>' : ''; ?>
              </a>
              <?php if ( $tooltip ) { ?>
                <div id="wdseo-tooltip-info-<?php echo $_platform ?>" class="wdseo-hide"><p><?php echo $tooltip; ?></p>
                </div>
              <?php } ?>
            </li>
            <?php
          }
          ?>
        </ul>
        <div class="wdseo-section">
          <ul class="wdseo-tabs">
            <?php
            if ( !empty($categories['tooltip-info']) ) {
              unset($categories['tooltip-info']);
            }
            foreach ( $categories as $_category => $_category_arr ) {
              $tooltip = (!empty($_category_arr['tooltip-info'])) ? $_category_arr['tooltip-info'] : FALSE;
              ?>
              <li class="tabs<?php echo $_category == $category ? ' ui-tabs-active' : ''; ?>">
                <a href="<?php echo add_query_arg(array(
                  'platform' => $platform,
                  'category' => $_category,
                ), $page_url); ?>" class="wdseo-tablink">
                  <?php echo $_category_arr['title']; ?>
                  <?php echo ($tooltip) ? '<i class="dashicons dashicons-editor-help" data-wdseo-tooltip-key="' . $_category . '"></i>' : ''; ?>
                </a>
                <?php if ( $tooltip ) { ?>
                  <div id="wdseo-tooltip-info-<?php echo $_category ?>" class="wdseo-hide">
                    <p><?php echo $tooltip; ?></p></div>
                <?php } ?>
              </li>
              <?php
            }
            ?>
          </ul>
          <div class="wdseo-section">
            <?php
            $total = isset($category_arr['total']) ? $category_arr['total'] : 0;
            echo WD_SEO_HTML::pagination($total, TRUE, $filters);
            ?>
            <table class="adminlist table table-striped wp-list-table widefat fixed pages">
              <thead>
              <tr>
                <?php echo WD_SEO_HTML::ordering('pageUrl', $orderby, $order, __('URL', WD_SEO_PREFIX), $page_url, 'column-primary'); ?>
                <th class="col_redirect_url">
                  <?php _e('Redirect URL', WD_SEO_PREFIX); ?>
                </th>
                <?php echo WD_SEO_HTML::ordering('last_crawled', $orderby, $order, __('Last crawled', WD_SEO_PREFIX), $page_url, 'col_last_crawled'); ?>
                <?php echo WD_SEO_HTML::ordering('first_detected', $orderby, $order, __('First detected', WD_SEO_PREFIX), $page_url, 'col_first_detected'); ?>
              </tr>
              </thead>
              <tbody>
              <?php
              if ( isset($category_arr['value']) && !empty($category_arr['value']) ) {
                foreach ( $category_arr['value'] as $key => $category_arr_value ) {
                  $alternate = (!isset($alternate) || $alternate == 'alternate') ? '' : 'alternate';
                  $category_arr_value['pageUrl'] = esc_html(trim($category_arr_value['pageUrl'], '/'));
                  ?>
                  <tr id="tr_<?php echo $key; ?>" class="row<?php echo $key % 2; ?> <?php echo $alternate; ?>">
                    <td class="col_pageUrl column-primary" data-colname="<?php _e('URL', WD_SEO_PREFIX); ?>">
                      <?php
                      if ( isset($category_arr_value['state'])
                        && $category_arr_value['state'] == 'marked_as_fixed' ) {
                        $redirect_action = __('Edit redirect', WD_SEO_PREFIX);
                        ?>
                        <span class="marked-as-fixed-icon dashicons dashicons-yes"></span>
                        <?php
                      }
                      else {
                        $redirect_action = __('Create redirect', WD_SEO_PREFIX);
                      }
                      ?>
                      <?php echo $category_arr_value['pageUrl']; ?>
                      <div class="row-actions">
                        <span>
                          <a class="thickbox"
                             title="<?php echo $redirect_action; ?>"
                             onclick="set_thickbox_href(this, event, <?php echo WDSeo()->is_active(); ?>)"
                             data-width="<?php echo $this->width; ?>"
                             data-height=""
                             data-inlineId="create_redirect"
                             data-url="<?php echo $category_arr_value['pageUrl']; ?>"
                             data-redirect-url="<?php echo (isset($category_arr_value['redirect_url']) ? $category_arr_value['redirect_url'] : ''); ?>">
                            <?php echo $redirect_action; ?>
                          </a>
                          |
                        </span>
                        <?php
                        if ( isset($category_arr_value['state'])
                          && $category_arr_value['state'] == 'marked_as_fixed' ) {
                          ?>
                          <span class="marked-as-fixed">
                          <?php _e('Marked as fixed', WD_SEO_PREFIX); ?>
                        </span>
                          <?php
                        }
                        else {
                          ?>
                          <span>
                          <a href="#" onclick="mark_as_fixed(this, event, <?php echo WDSeo()->is_active(); ?>)"
                             data-url="<?php echo $category_arr_value['pageUrl']; ?>">
                           <?php _e('Mark as fixed', WD_SEO_PREFIX); ?>
                          </a>
                        </span>
                          <?php
                        }
                        ?>
                      </div>
                      <button class="toggle-row" type="button">
                        <span class="screen-reader-text"><?php _e('Show more details', WD_SEO_PREFIX); ?></span>
                      </button>
                    </td>
                    <td class="col_redirect_url" data-colname="<?php _e('Redirect URL', WD_SEO_PREFIX); ?>">
                      <?php echo (isset($category_arr_value['redirect_url']) ? $category_arr_value['redirect_url'] : ''); ?>
                    </td>
                    <td class="col_last_crawled" data-colname="<?php _e('Last crawled', WD_SEO_PREFIX); ?>">
                      <?php echo date(get_option('date_format'), strtotime($category_arr_value['last_crawled'])); ?>
                    </td>
                    <td class="col_first_detected" data-colname="<?php _e('First detected', WD_SEO_PREFIX); ?>">
                      <?php echo date(get_option('date_format'), strtotime($category_arr_value['first_detected'])); ?>
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
    </div>
    <?php
    echo $this->create_redirect_body();

    return ob_get_clean();
  }

  /**
   * Create redirect popup html.
   *
   * @return string
   */
  private function create_redirect_body() {
    ob_start();
    ?>
    <div id="create_redirect" class="hidden">
      <div class="wd-table">
        <span class="wd-group">
          <label class="wd-label" for="redirect_url"><?php _e('Redirect URL', WD_SEO_PREFIX); ?></label>
          <input id="redirect_url" name="redirect_url" value="" type="text" />
          <input name="url" value="" type="hidden" />
          <p class="description"><?php _e('Enter absolute URL.', WD_SEO_PREFIX); ?></p>
        </span>
        <span class="wd-group wd-right">
          <?php
          $buttons = array(
            'create_redirect' => array(
              'title' => __('Save', WD_SEO_PREFIX),
              'value' => 'create_redirect',
              'name' => 'task',
              'class' => 'button-primary',
              'onclick' => 'create_redirect()',
            ),
          );
          echo $this->buttons($buttons, TRUE);
          ?>
        </span>
      </div>
    </div>
    <?php

    return ob_get_clean();
  }
}
