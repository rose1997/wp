<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Settings page view class.
 */
class WDSeouninstallView extends WDSeoAdminView {
  /**
   * Display page.
   *
   * @param $uninstall_status
   */
  public function display( $uninstall_status ) {
    ob_start();
    echo $this->header();
    if ( $uninstall_status == '1' ) {
      echo $this->deactivate();
    }
    else {
      echo $this->uninstall();
    }
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
    ?>
    <h1 id="title_uninstall"><?php echo sprintf(__('Uninstall %s', WD_SEO_PREFIX), WD_SEO_NICENAME); ?></h1>
    <?php
    return ob_get_clean();
  }

  /**
   * Page Uninstall.
   *
   * @return string Generated html.
   */
  public function uninstall() {
    ob_start();
    ?>
    <div class="wd-table">
      <div class="wd-table-col wd-table-col-100 wd-table-col-left">
        <div class="wd-box-section">
          <div class="wd-box-content">
            <span class="wd-group">
              <div class="goodbye-text support_team_cont">
                <?php _e('Before uninstalling the plugin, please Contact our', WD_SEO_PREFIX); ?>
                <a href="https://web-dorado.com/support/contact-us.html" target='_blank'><?php _e('support team', WD_SEO_PREFIX); ?></a>.
                <?php _e('We\'ll do our best to help you out with your issue. ', WD_SEO_PREFIX); ?>
                <?php _e('We value each and every user and value what’s right for our users in everything we do.', WD_SEO_PREFIX); ?>
                <br>
                <?php _e(' However, if anyway you have made a decision to uninstall the plugin, please take a minute to ', WD_SEO_PREFIX); ?>
                <a href="https://web-dorado.com/support/contact-us.html" target='_blank'><?php _e('Contact us', WD_SEO_PREFIX); ?></a>
                <?php _e('and tell what you didn\'t like for our plugins further improvement and development. Thank you !!!', WD_SEO_PREFIX); ?>
              </div>
              <div class="goodbye-text red">
                <?php echo sprintf(__('Note, that uninstalling %s will remove all data on the plugin.', WD_SEO_PREFIX), WD_SEO_NICENAME); ?>
                <br />
                <?php _e('Please make sure you don\'t have any important information before you proceed.', WD_SEO_PREFIX); ?>
              </div>
              <p>
                <?php echo sprintf(__('Deactivating %s plugin does not remove any data that may have been created.', WD_SEO_PREFIX), WD_SEO_NICENAME); ?>
                <?php _e('To completely remove this plugin, you can uninstall it here.', WD_SEO_PREFIX); ?>
              </p>
              <p class="red warning">
                <strong><?php _e('WARNING:', WD_SEO_PREFIX); ?></strong>
                <?php _e('Once uninstalled, this cannot be undone.', WD_SEO_PREFIX); ?>
                <?php _e('You should use a Database Backup plugin of WordPress to back up all the data first.', WD_SEO_PREFIX); ?>
              </p>
            </span>
          </div>
        </div>
        <div class="wd-box-section">
          <div class="wd-box-content">
            <span class="wd-group">
              <p class="wd-center">
                <?php echo sprintf(__('Do you really want to uninstall %s?', WD_SEO_PREFIX), WD_SEO_NICENAME); ?>
              </p>
              <p class="wd-center">
                <input type="checkbox" name="uninstall_status" id="check_yes" value="1" />&nbsp;<label for="check_yes"><?php _e('Yes', WD_SEO_PREFIX); ?> </label>
              </p>
              <p class="wd-center">
                <input type="submit" value="<?php _e('Uninstall', WD_SEO_PREFIX); ?>" title="<?php _e('Uninstall', WD_SEO_PREFIX); ?>" name="task" class="button-primary" onclick="if (check_yes.checked) {
                  if (!confirm('<?php echo addslashes(sprintf(__("You are About to Uninstall %s plugin from WordPress. This action is not reversible.", WD_SEO_PREFIX), WD_SEO_NICENAME)); ?>')) {
                  return false;
                  }
                  }
                  else {
                  return false;
                  }" />
              </p>
            </span>
          </div>
        </div>
      </div>
    </div>
    <?php
    return ob_get_clean();
  }

  /**
   * Page Uninstall part deactivate.
   *
   * @return string Generated html.
   */
  public function deactivate() {
    $deactivate_url = wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . WD_SEO_NAME . '/seo-by-10web.php', 'deactivate-plugin_' . WD_SEO_NAME . '/seo-by-10web.php');
    ob_start();
    ?>
    <div class="wd-table">
      <div class="wd-table-col-100">
        <div class="wd-box-section">
          <div class="wd-box-content">
            <span class="wd-group">
              <input type="hidden" value="deactivate" name="task" class="button-primary" />
              <strong>
                <a href="<?php echo $deactivate_url; ?>" data-uninstall="1"><?php _e("Click Here", WD_SEO_PREFIX); ?></a>
                <?php echo sprintf(__('to finish the uninstallation and %s plugin will be deactivated automatically.', WD_SEO_PREFIX), WD_SEO_NICENAME); ?>
              </strong>
            </span>
          </div>
        </div>
      </div>
    </div>
    <?php

    return ob_get_clean();
  }
}
