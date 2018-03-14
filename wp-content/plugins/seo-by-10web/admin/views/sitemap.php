<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Settings page view class.
 */
class WDSeositemapView extends WDSeoAdminView {
  /**
   * Display page.
   */
  public function display($options, $post_types, $taxonomies) {
    wp_enqueue_style(WD_SEO_PREFIX . '_select2');
    wp_enqueue_script(WD_SEO_PREFIX . '_select2');
    ob_start();
    echo $this->header();
    echo $this->body($options, $post_types, $taxonomies);

    // Pass the content to form.
    echo $this->form(ob_get_clean());
  }

  /**
   * Page header.
   *
   * @return string Generated html.
   */
  private function header() {
    ob_start();
    echo $this->title(__('Sitemap', WD_SEO_PREFIX));
    $buttons = array(
      'save' => array(
        'title' => __('Save', WD_SEO_PREFIX),
        'value' => 'save',
        'name' => 'task',
        'class' => 'button-primary',
      ),
      'reset' => array(
        'title' => __('Reset', WD_SEO_PREFIX),
        'value' => 'reset',
        'name' => 'task',
        'class' => 'button-secondary',
      ),
      'cancel' => array(
        'title' => __('Cancel', WD_SEO_PREFIX),
        'value' => 'cancel',
        'name' => 'task',
        'class' => 'button-secondary',
      ),
    );
    echo $this->buttons($buttons);
    return ob_get_clean();
  }

  /**
   * Generate page body.
   *
   * @param object $options
   *
   * @return string Body html.
   */
  private function body($options, $post_types, $taxonomies) {
    ob_start();
    $sitemap_dir = $options->get_sitemap_dir();
    $sitemap_path = $sitemap_dir['path'] . $sitemap_dir['index_name'];
    $sitemap_url = $sitemap_dir['url'] . $sitemap_dir['index_name'];
    ?>
    <div class="wd-table">
      <div class="wd-table-col wd-table-col-50 wd-table-col-left">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('XML SITEMAP', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-group">
              <label class="wd-label"><?php _e('Generate XML Sitemap', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($options->sitemap, 1); ?> id="wd-sitemap-1" class="wd-radio" value="1" name="wd_settings[sitemap]" type="radio" />
              <label class="wd-label-radio" for="wd-sitemap-1"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($options->sitemap, 0); ?> id="wd-sitemap-0" class="wd-radio" value="0" name="wd_settings[sitemap]" type="radio" />
              <label class="wd-label-radio" for="wd-sitemap-0"><?php _e('No', WD_SEO_PREFIX); ?></label>
            </span>
            <?php
            if ( !$options->sitemap ) {
              echo WD_SEO_HTML::message(0, __('Sitemap will not be published until you switch the option.', WD_SEO_PREFIX), 'error');
            }
            ?>
            <span class="wd-group">
              <label class="wd-label"><?php _e('Your Sitemap is located at', WD_SEO_PREFIX); ?></label>
              <div class="wd-block-content wd-select-all">
                <?php echo $sitemap_path; ?>
              </div>
            </span>
            <span class="wd-group">
              <?php echo sprintf(__('Your Sitemap URL is %s', WD_SEO_PREFIX), '<a href="' . $sitemap_url . '" target="_blank">' . $sitemap_url . '</a>'); ?>
            </span>
          </div>
        </div>
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('SEARCH ENGINES', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-group">
              <label class="wd-label" for="wd-google-verification"><?php _e('Google Site Verification', WD_SEO_PREFIX); ?></label>
              <p class="description"><?php echo $options->google_verification_msg; ?></p>
            </span>
            <span class="wd-group">
              <label class="wd-label" for="wd-bing-verification"><?php _e('Bing Site Verification Code', WD_SEO_PREFIX); ?></label>
              <input type="text" id="wd-bing-verification" name="wd_settings[bing_verification]" value="<?php echo $options->bing_verification; ?>" />
              <p class="description"><?php echo sprintf(__('Click %shere%s to get your site verificaion code.', WD_SEO_PREFIX), '<a href="https://www.bing.com/webmaster/home/mysites" target="_blank">', '</a>'); ?></p>
            </span>
            <span class="wd-group">
              <label class="wd-label" for="wd-yandex-verification"><?php _e('Yandex Site Verification Code', WD_SEO_PREFIX); ?></label>
              <input type="text" id="wd-yandex-verification" name="wd_settings[yandex_verification]" value="<?php echo $options->yandex_verification; ?>" />
              <p class="description"><?php echo sprintf(__('Click %shere%s to get your site verificaion code.', WD_SEO_PREFIX), '<a href="https://webmaster.yandex.com/sites/" target="_blank">', '</a>'); ?></p>
            </span>
            <span class="wd-group">
              <label class="wd-label"><?php _e('Notify Search Engines When My Sitemap Updates', WD_SEO_PREFIX); ?></label>
              <input value="0" name="wd_settings[notify_google]" type="hidden" /><?php //hidden input with same name to have empty value. ?>
              <input <?php checked($options->notify_google, 1); ?> id="wd-notify-google" class="wd-radio" value="1" name="wd_settings[notify_google]" type="checkbox" />
              <label class="wd-label-radio" for="wd-notify-google"><?php _e('Google', WD_SEO_PREFIX); ?></label>
              <input value="0" name="wd_settings[notify_bing]" type="hidden" /><?php //hidden input with same name to have empty value. ?>
              <input <?php checked($options->notify_bing, 1); ?> id="wd-notify-bing" class="wd-radio" value="1" name="wd_settings[notify_bing]" type="checkbox" />
              <label class="wd-label-radio" for="wd-notify-bing"><?php _e('Bing', WD_SEO_PREFIX); ?></label>
            </span>
          </div>
        </div>
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('SITEMAP INFO', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <?php
            if ( $options->sitemap_items_count > 0 ) {
              ?>
            <span class="wd-group">
              <?php echo sprintf(_n('Sitemap contains %d item.', 'Sitemap contains %d items.', $options->sitemap_items_count, WD_SEO_PREFIX), $options->sitemap_items_count); ?>
            </span>
              <?php
            }
            ?>
            <span class="wd-group">
              <?php
              if ( isset($options->sitemap_last_modified->date)
                && isset($options->sitemap_last_modified->time) ) {
                if ( $options->sitemap_items_count == -1 ) {
                  echo sprintf(__('Sitemap was deleted on %s at %s.', WD_SEO_PREFIX), $options->sitemap_last_modified->date, $options->sitemap_last_modified->time);
                }
                else {
                  echo sprintf(__('It was last updated on %s at %s.', WD_SEO_PREFIX), $options->sitemap_last_modified->date, $options->sitemap_last_modified->time);
                }
              }
              else {
                _e('Sitemap is not generated yet.', WD_SEO_PREFIX);
              }
              ?>
            </span>
            <span class="wd-group">
              <?php
              $buttons = array(
                'update_sitemap' => array(
                  'title' => __('Manually update', WD_SEO_PREFIX),
                  'value' => 'update_sitemap',
                  'name' => 'task',
                  'class' => 'button-primary',
                ),
              );
              echo $this->buttons($buttons, TRUE);
              ?>
              <?php
              $buttons = array(
                'update_sitemap' => array(
                  'title' => __('Delete Sitemap', WD_SEO_PREFIX),
                  'value' => 'delete',
                  'name' => 'task',
                  'class' => 'button-secondary',
                ),
              );
              if ( $options->sitemap_items_count == -1 ) {
                $buttons['update_sitemap']['disabled'] = 'disabled';
              }
              echo $this->buttons($buttons, TRUE);
              ?>
            </span>
          </div>
        </div>
      </div>
      <div class="wd-table-col wd-table-col-50 wd-table-col-right">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('EXCLUDES', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-group">
              <label class="wd-label"><?php _e('Exclude Post Types', WD_SEO_PREFIX); ?></label>
              <input value="" name="wd_settings[exclude_post_types][]" type="hidden" /><?php //hidden input with same name to have empty value. ?>
              <select id="wd-exclude-post-types" multiple="multiple" name="wd_settings[exclude_post_types][]" style="width: 100%;"><?php //style="width: 100%;" is written here to make select2 responsive ?>
                <?php
                foreach ($post_types as $item => $label) {
                  ?>
                  <option value="<?php echo esc_attr($item); ?>" <?php selected(true, in_array($item, $options->exclude_post_types)); ?>><?php echo esc_html($label['name']); ?></option>
                  <?php
                }
                ?>
              </select>
            </span>
            <span class="wd-group">
              <label class="wd-label"><?php _e('Exclude Taxonomies', WD_SEO_PREFIX); ?></label>
              <input value="" name="wd_settings[exclude_taxonomies][]" type="hidden" /><?php //hidden input with same name to have empty value. ?>
              <select id="wd-exclude-taxonomies" multiple="multiple" name="wd_settings[exclude_taxonomies][]" style="width: 100%;"><?php //style="width: 100%;" is written here to make select2 responsive ?>
                <?php
                foreach ($taxonomies as $item => $label) {
                  ?>
                  <option value="<?php echo esc_attr($item); ?>" <?php selected(true, in_array($item, $options->exclude_taxonomies)); ?>><?php echo esc_html($label['name']); ?></option>
                  <?php
                }
                ?>
              </select>
            </span>
            <span class="wd-group">
              <label class="wd-label" for="wd-exclude-posts"><?php _e('Exclude Posts', WD_SEO_PREFIX); ?></label>
              <input type="text" id="wd-exclude-posts" name="wd_settings[exclude_posts]" value="<?php echo $options->exclude_posts; ?>" />
              <p class="description"><?php _e('You can exclude posts from the sitemap by entering a comma separated string with the Post ID\'s (e.g. 1,2,99,100).', WD_SEO_PREFIX); ?></p>
            </span>
          </div>
        </div>
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('OPTIONS', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-group">
              <label class="wd-label"><?php _e('Include image items with the sitemap', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->sitemap_image, 1); ?> id="wd-sitemap_image-1" class="wd-radio" value="1" name="wd_settings[sitemap_image]" type="radio" />
              <label class="wd-label-radio" for="wd-sitemap_image-1"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->sitemap_image, 0); ?> id="wd-sitemap_image-0" class="wd-radio" value="0" name="wd_settings[sitemap_image]" type="radio" />
              <label class="wd-label-radio" for="wd-sitemap_image-0"><?php _e('No', WD_SEO_PREFIX); ?></label>
            </span>
            <span class="wd-group">
              <label class="wd-label"><?php _e('Include stylesheet with the generated sitemap', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->sitemap_stylesheet, 1); ?> id="wd-sitemap_stylesheet-1" class="wd-radio" value="1" name="wd_settings[sitemap_stylesheet]" type="radio" />
              <label class="wd-label-radio" for="wd-sitemap_stylesheet-1"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->sitemap_stylesheet, 0); ?> id="wd-sitemap_stylesheet-0" class="wd-radio" value="0" name="wd_settings[sitemap_stylesheet]" type="radio" />
              <label class="wd-label-radio" for="wd-sitemap_stylesheet-0"><?php _e('No', WD_SEO_PREFIX); ?></label>
            </span>
            <span class="wd-group">
              <label class="wd-label" for="wd-limit"><?php _e('Max entries per sitemap', WD_SEO_PREFIX); ?></label>
              <input type="text" id="wd-limit" name="wd_settings[limit]" value="<?php echo $options->limit; ?>" />
              <p class="description"><?php _e('Maximum number of entries per sitemap page. Lower this to prevent memory issues on some installs.', WD_SEO_PREFIX); ?></p>
            </span>
            <span class="wd-group">
              <label class="wd-label"><?php _e('Autoupdate sitemap', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->autoupdate_sitemap, 1); ?> id="wd-autoupdate_sitemap-1" class="wd-radio" value="1" name="wd_settings[autoupdate_sitemap]" type="radio" />
              <label class="wd-label-radio" for="wd-autoupdate_sitemap-1"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->autoupdate_sitemap, 0); ?> id="wd-autoupdate_sitemap-0" class="wd-radio" value="0" name="wd_settings[autoupdate_sitemap]" type="radio" />
              <label class="wd-label-radio" for="wd-autoupdate_sitemap-0"><?php _e('No', WD_SEO_PREFIX); ?></label>
              <p class="description"><?php _e('Autoupdate sitemap on posts/pages edit', WD_SEO_PREFIX); ?></p>
            </span>
          </div>
        </div>
      </div>
    </div>
    <?php
    return ob_get_clean();
  }
}
