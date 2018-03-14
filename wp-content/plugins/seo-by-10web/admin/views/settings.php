<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Settings page view class.
 */
class WDSeosettingsView extends WDSeoAdminView {
  /**
   * Display page.
   */
  public function display($options) {
    ob_start();
    echo $this->header();
    echo $this->body($options);

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
    echo $this->title(__('Options', WD_SEO_PREFIX));
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
  private function body($options) {
    ob_start();
    ?>
    <div class="wd-table">
      <div class="wd-table-col wd-table-col-50 wd-table-col-left">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('User permissions', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-group">
              <label class="wd-label"><?php _e('Show SEO metabox to role', WD_SEO_PREFIX); ?></label>
              <select name="wd_settings[meta_role]">
                <?php wp_dropdown_roles( $options->meta_role ); ?>
              </select>
            </span>
            <!--<span class="wd-group">
              <label class="wd-label"><?php /*_e('Show Moz metabox to roles', WD_SEO_PREFIX); */?></label>
              <select name="wd_settings[moz_role]">
                <?php /*wp_dropdown_roles( $options->moz_role ); */?>
              </select>
            </span>-->
          </div>
        </div>
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php echo sprintf(__('Uninstall %s plugin', WD_SEO_PREFIX), WD_SEO_NICENAME); ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-group">
              <a class="button button-secondary" href="<?php echo add_query_arg(array( 'page' => WD_SEO_PREFIX . '_uninstall', ), admin_url('admin.php')); ?>"><?php _e('Uninstall', WD_SEO_PREFIX); ?></a>
            </span>
          </div>
        </div>
      </div>
      <div class="wd-table-col wd-table-col-50 wd-table-col-right">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('Defaults', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-group">
              <label class="wd-label"><?php _e('Default Redirection type', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->redirections, 1); ?> id="wd-redirections-301" class="wd-radio" value="1" name="wd_settings[redirections]" type="radio" />
              <label class="wd-label-radio" for="wd-redirections-301"><?php _e('Permanent (301)', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->redirections, 0); ?> id="wd-redirections-302" class="wd-radio" value="0" name="wd_settings[redirections]" type="radio" />
              <label class="wd-label-radio" for="wd-redirections-302"><?php _e('Temporary (302)', WD_SEO_PREFIX); ?></label>
            </span>
            <span class="wd-group">
              <label class="wd-label"><?php _e('Meta information optimization', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($options->meta, 1); ?> id="wd-meta-1" class="wd-radio" value="1" name="wd_settings[meta]" type="radio" />
              <label class="wd-label-radio" for="wd-meta-1"><?php _e('Yes', WD_SEO_PREFIX); ?></label>
              <input <?php echo checked($options->meta, 0); ?> id="wd-meta-0" class="wd-radio" value="0" name="wd_settings[meta]" type="radio" />
              <label class="wd-label-radio" for="wd-meta-0"><?php _e('No', WD_SEO_PREFIX); ?></label>
            </span>
            <!--<span class="wd-group">
              <label class="wd-label" for="autocrawl-interval"><?php //_e('Auto crawl interval', WD_SEO_PREFIX); ?></label>
              <input class="wd-int" id="autocrawl-interval" name="wd_settings[autocrawl_interval]" value="<?php //echo $options->autocrawl_interval; ?>" type="text" size="4" />&nbsp;<?php //_e('day', WD_SEO_PREFIX); ?>
              <p class="description"><?php //_e('Set 0 to disable auto crawl.', WD_SEO_PREFIX); ?></p>
            </span>-->
          </div>
        </div>
      </div>
    </div>

    <?php
    return ob_get_clean();
  }
}
