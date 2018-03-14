<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Admin view class.
 */
class WDSeoAdminView {
  /**
   * WDSeoAdminView constructor.
   */
  public function __construct() {}

  /**
   * Generate form.
   *
   * @param string $content
   * @param array  $attr
   *
   * @return string Form html.
   */
  protected function form($content = '', $attr = array()) {
    ob_start();
    // Form.
    $action = isset($attr['action']) ? esc_attr($attr['action']) : '';
    $method = isset($attr['method']) ? esc_attr($attr['method']) : 'post';
    $name = isset($attr['name']) ? esc_attr($attr['name']) : WD_SEO_PREFIX . '_form';
    $id = isset($attr['id']) ? esc_attr($attr['id']) : '';
    $class = isset($attr['class']) ? esc_attr($attr['class']) : WD_SEO_PREFIX . '_form';
    $style = isset($attr['style']) ? esc_attr($attr['style']) : '';
    ?><div class="wrap">
    <?php
    // Message container.
    echo WD_SEO_HTML::message(WD_SEO_Library::get('msg'));
    ?>
      <form
          <?php echo $action ? 'action="' . $action . '"' : ''; ?>
          <?php echo $method ? 'method="' . $method . '"' : ''; ?>
          <?php echo $name ? ' name="' . $name . '"' : ''; ?>
          <?php echo $id ? ' id="' . $id . '"' : ''; ?>
          <?php echo $class ? ' class="' . $class . '"' : ''; ?>
          <?php echo $style ? ' style="' . $style . '"' : ''; ?>
      ><?php
      echo $content;
      // Add nonce to form.
      wp_nonce_field(WD_SEO_NONCE, WD_SEO_NONCE);
      ?></form>
    </div><?php
    return ob_get_clean();
  }

  /**
   * Generate title.
   *
   * @param string $title
   *
   * @return string Title html.
   */
  protected function title($title = '') {
    ob_start();
    ?><div class="wd-page-title">
      <h2><?php echo $title; ?></h2>
    </div><?php
    return ob_get_clean();
  }

  /**
   * Generate buttons.
   *
   * @param array $buttons
   * @param bool $single
   * @param array $parent
   *
   * @return array Buttons html.
   */
  protected function buttons($buttons = array(), $single = FALSE, $parent = array()) {
    ob_start();
    if ( !$single ) {
      $parent_id = isset($parent['id']) ? esc_attr($parent['id']) : '';
      $parent_class = isset($parent['class']) ? esc_attr($parent['class']) : 'wd-buttons';
      $parent_style = isset($parent['style']) ? esc_attr($parent['style']) : '';
      ?>
    <div
      <?php echo $parent_id ? 'id="' . $parent_id . '"' : ''; ?>
      <?php echo $parent_class ? ' class="' . $parent_class . '"' : ''; ?>
      <?php echo $parent_style ? ' style="' . $parent_style . '"' : ''; ?>
      >
      <?php
    }
    foreach ($buttons as $button) {
      $title = isset($button['title']) ? esc_attr($button['title']) : '';
      $value = isset($button['value']) ? esc_attr($button['value']) : '';
      $name = isset($button['name']) ? esc_attr($button['name']) : '';
      $id = isset($button['id']) ? esc_attr($button['id']) : '';
      $class = isset($button['class']) ? esc_attr($button['class']) : '';
      $style = isset($button['style']) ? esc_attr($button['style']) : '';
      $onclick = isset($button['onclick']) ? esc_attr($button['onclick']) : '';
      $disabled = isset($button['disabled']) ? esc_attr($button['disabled']) : '';
      ?><button type="submit"
               <?php echo $value ? ' value="' . $value . '"' : ''; ?>
               <?php echo $name ? ' name="' . $name . '"' : ''; ?>
               <?php echo $id ? ' id="' . $id . '"' : ''; ?>
               class="wd-button <?php echo $class; ?>"
               <?php echo $style ? ' style="' . $style . '"' : ''; ?>
               <?php echo $onclick ? ' onclick="' . $onclick . '"' : ''; ?>
               <?php echo $disabled ? ' disabled="' . $disabled . '"' : ''; ?>
         ><?php echo $title; ?></button><?php
    }
    if ( !$single ) {
      ?>
    </div>
      <?php
    }
    return ob_get_clean();
  }
}
