<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Settings page view class.
 */
class WDSeometaboxView {
  /**
   * Display meta box.
   */
  public static function display($options, $options_defaults) {
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script(WD_SEO_PREFIX . '_common');
    wp_enqueue_script(WD_SEO_PREFIX . '_wdseo');
    wp_enqueue_style(WD_SEO_PREFIX . '_admin');
    wp_enqueue_style(WD_SEO_PREFIX . '_select2');
    wp_enqueue_script(WD_SEO_PREFIX . '_select2');
    wp_enqueue_media();
    $current_url = esc_url($options->url);
    ob_start();
    ?>
    <div class="wdseo_tabs">
      <ul class="wdseo-tabs">
        <li class="tabs">
          <a href="#wdseo_tab_keywords_content" class="wdseo-tablink"><?php _e('Keywords', WD_SEO_PREFIX); ?></a>
        </li>
        <li class="tabs">
          <a href="#wdseo_tab_settings_content" class="wdseo-tablink"><?php _e('Settings', WD_SEO_PREFIX); ?></a>
        </li>
        <li class="tabs">
          <a href="#wdseo_tab_opengraph_content" class="wdseo-tablink"><?php _e('Facebook / OpenGraph', WD_SEO_PREFIX); ?></a>
        </li>
        <li class="tabs">
          <a href="#wdseo_tab_twitter_content" class="wdseo-tablink"><?php _e('Twitter', WD_SEO_PREFIX); ?></a>
        </li>
      </ul>
      <div id="wdseo_tab_keywords_content" class="wdseo-section wd-table wd-preview">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('Preview', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <div class="wd-preview">
              <div class="wd-preview-title">
                <h3>
                  <a href="<?php echo $current_url; ?>" target="_blank"></a>
                </h3>
              </div>
              <div class="wd-preview-url">
                <a href="<?php echo $current_url; ?>" target="_blank">
                  <?php echo $current_url; ?>
                </a>
              </div>
              <?php
              if ($options_defaults->date) {
                ?>
                <div class="wd-preview-date show">
                  <?php echo mysql2date(get_option("date_format"), get_the_date("M d, Y")); ?>
                </div>
                <?php
              }
              ?>
              <div class="wd-preview-description"></div>
            </div>
          </div>
        </div>
        <span class="wd-group">
          <label class="wd-label" for="wdseo_meta_title"><?php _e('Meta title', WD_SEO_PREFIX); ?></label>
          <input class="wd-has-placeholder wd-set-preview-title" id="wdseo_meta_title" name="wd_settings[meta_title]" value="<?php echo $options->meta_title; ?>" placeholder="<?php echo $options_defaults->meta_title; ?>" data-default="%%title%%" type="text" />
        </span>
        <span class="wd-group">
          <label class="wd-label" for="wdseo_meta_description"><?php _e('Meta description', WD_SEO_PREFIX); ?></label>
          <textarea class="wd-has-placeholder wd-set-preview-description" id="wdseo_meta_description" name="wd_settings[meta_description]" placeholder="<?php echo $options_defaults->meta_description; ?>" data-default="%%excerpt%%"><?php echo $options->meta_description; ?></textarea>
        </span>
        <span class="wd-group">
          <label class="wd-label" for="wdseo_meta_keywords"><?php _e('Keywords', WD_SEO_PREFIX); ?></label>
          <select class="wd-select2 wd-hide-droprown" id="wdseo_meta_keywords" name="wd_settings[meta_keywords][]" multiple data-placeholder="<?php echo implode(', ', $options_defaults->meta_keywords); ?>">
          <?php
          foreach ( $options->meta_keywords as $keyword ) {
            ?>
            <option <?php selected(true, true); ?> data-select2-tag="true" value="<?php echo $keyword; ?>"><?php echo $keyword; ?></option>
            <?php
          }
          ?>
          </select>
        </span>
      </div>
      <div id="wdseo_tab_settings_content" class="wdseo-section wd-table">
        <span class="wd-group">
          <label class="wd-label" for="wdseo_canonical_url"><?php _e('Canonical URL', WD_SEO_PREFIX); ?></label>
          <input id="wdseo_canonical_url" name="wd_settings[canonical_url]" value="<?php echo $options->canonical_url; ?>" type="text"/>
        </span>
        <?php
        if ( isset( $options->redirect_url ) ) {
          ?>
          <span class="wd-group">
            <label class="wd-label" for="wdseo_redirect_url"><?php _e('Redirect URL', WD_SEO_PREFIX); ?></label>
            <input id="wdseo_redirect_url" name="wd_settings[redirect_url]" value="<?php echo $options->redirect_url; ?>" type="text"/>
          </span>
          <?php
        }
        ?>
        <span class="wd-group">
          <label class="wd-label"><?php _e('Meta robots', WD_SEO_PREFIX); ?></label>
          <input <?php checked($options->index, 1); ?> id="wdseo_index1" class="wd-radio" value="1" name="wd_settings[index]" type="radio" />
          <label class="wd-label-radio" for="wdseo_index1"><?php _e('Index', WD_SEO_PREFIX); ?></label>
          <input <?php checked($options->index, 0); ?> id="wdseo_index0" class="wd-radio" value="0" name="wd_settings[index]" type="radio" />
          <label class="wd-label-radio" for="wdseo_index0"><?php _e('No index', WD_SEO_PREFIX); ?></label>
          <input <?php checked($options->index, ''); ?> id="wdseo_index" class="wd-radio" value="" name="wd_settings[index]" type="radio" />
          <label class="wd-label-radio" for="wdseo_index"><?php printf(__('Inherit (currently: %s)', WD_SEO_PREFIX), ($options_defaults->index ? __('Index', WD_SEO_PREFIX) : __('No index', WD_SEO_PREFIX))); ?></label>
        </span>
        <span class="wd-group">
          <input <?php checked($options->follow, 1); ?> id="wdseo_follow1" class="wd-radio" value="1" name="wd_settings[follow]" type="radio" />
          <label class="wd-label-radio" for="wdseo_follow1"><?php _e('Follow', WD_SEO_PREFIX); ?></label>
          <input <?php checked($options->follow, 0); ?> id="wdseo_follow0" class="wd-radio" value="0" name="wd_settings[follow]" type="radio" />
          <label class="wd-label-radio" for="wdseo_follow0"><?php _e('No follow', WD_SEO_PREFIX); ?></label>
          <input <?php checked($options->follow, ''); ?> id="wdseo_follow" class="wd-radio" value="" name="wd_settings[follow]" type="radio" />
          <label class="wd-label-radio" for="wdseo_follow"><?php printf(__('Inherit (currently: %s)', WD_SEO_PREFIX), ($options_defaults->follow ? __('Follow', WD_SEO_PREFIX) : __('No follow', WD_SEO_PREFIX))); ?></label>
        </span>
        <span class="wd-group">
          <input value="0" name="wd_settings[robots_advanced][]" type="hidden" /><?php //hidden input with same name to have empty value. ?>
          <input <?php checked(1, in_array('noodp', $options->robots_advanced)); ?> id="wd-meta-advanced-noodp" class="wd-radio" value="noodp" name="wd_settings[robots_advanced][]" type="checkbox" />
          <label class="wd-label-radio" for="wd-meta-advanced-noodp"><?php _e('NO ODP', WD_SEO_PREFIX); ?></label><br />
          <input <?php checked(1, in_array('noimageindex', $options->robots_advanced)); ?> id="wd-meta-advanced-noimageindex" class="wd-radio" value="noimageindex" name="wd_settings[robots_advanced][]" type="checkbox" />
          <label class="wd-label-radio" for="wd-meta-advanced-noimageindex"><?php _e('No Image Index', WD_SEO_PREFIX); ?></label><br />
          <input <?php checked(1, in_array('noarchive', $options->robots_advanced)); ?> id="wd-meta-advanced-noarchive" class="wd-radio" value="noarchive" name="wd_settings[robots_advanced][]" type="checkbox" />
          <label class="wd-label-radio" for="wd-meta-advanced-noarchive"><?php _e('No Archive', WD_SEO_PREFIX); ?></label><br />
          <input <?php checked(1, in_array('nosnippet', $options->robots_advanced)); ?> id="wd-meta-advanced-nosnippet" class="wd-radio" value="nosnippet" name="wd_settings[robots_advanced][]" type="checkbox" />
          <label class="wd-label-radio" for="wd-meta-advanced-nosnippet"><?php _e('No Snippet', WD_SEO_PREFIX); ?></label><br />
          <input <?php checked(1, in_array('', $options->robots_advanced)); ?> id="wd-meta-advanced-inherit" class="wd-radio" value="" name="wd_settings[robots_advanced][]" type="checkbox" />
          <?php
          $robots_advanced_parent_values = str_replace(array('noodp', 'noimageindex', 'noarchive', 'nosnippet'), array(__('NO ODP', WD_SEO_PREFIX), __('No Image Index', WD_SEO_PREFIX), __('No Archive', WD_SEO_PREFIX), __('No Snippet', WD_SEO_PREFIX)), ltrim(implode(', ', $options_defaults->robots_advanced), '0, '));
          ?>
          <label class="wd-label-radio" for="wd-meta-advanced-inherit"><?php printf(__('Inherit (Overwrites current values. Currently: %s)', WD_SEO_PREFIX), $robots_advanced_parent_values ? $robots_advanced_parent_values : __('None', WD_SEO_PREFIX)); ?></label>
        </span>
      </div>
      <div id="wdseo_tab_opengraph_content" class="wdseo-section wd-table wd-preview">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('Preview', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <div class="wd-social-preview wd-og-preview">
              <div id="wdseo_og_image" class="wdseo-social-image"></div>
              <div class="wdseo-social-body">
                <div class="wd-preview-social-title wd-preview-og-title">
                  <a href="<?php echo $current_url; ?>" target="_blank"></a>
                </div>
                <div class="wd-preview-social-description wd-preview-og-description"></div>
                <div class="wd-preview-social-url wd-preview-og-url">
                  <a href="<?php echo $current_url; ?>" target="_blank">
                    <?php echo $current_url; ?>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <span class="wd-group">
          <label class="wd-label" for="wdseo_opengraph_title"><?php _e('OpenGraph title', WD_SEO_PREFIX); ?></label>
          <input class="wd-has-placeholder wd-set-preview-og-title" id="wdseo_opengraph_title" name="wd_settings[opengraph_title]" value="<?php echo $options->opengraph_title; ?>" placeholder="<?php echo $options_defaults->opengraph_title; ?>" data-default="%%title%%" type="text"/>
        </span>
        <span class="wd-group">
          <label class="wd-label" for="wdseo_opengraph_description"><?php _e('OpenGraph description', WD_SEO_PREFIX); ?></label>
          <textarea class="wd-has-placeholder wd-set-preview-og-description" id="wdseo_opengraph_description" name="wd_settings[opengraph_description]" placeholder="<?php echo $options_defaults->opengraph_description; ?>" data-default="%%excerpt%%"><?php echo $options->opengraph_description; ?></textarea>
        </span>
        <span class="wd-group">
          <label class="wd-label"><?php _e('OpenGraph images', WD_SEO_PREFIX); ?></label>
          <div>
            <input class="image-ids" id="wdseo_opengraph_images" name="wd_settings[opengraph_images]" value="<?php echo $options->opengraph_images; ?>" data-default="<?php echo wp_get_attachment_url($options_defaults->opengraph_images); ?>" type="hidden"/>
            <?php
            // Get saved images ids.
            $attachment_ids = explode(',', $options->opengraph_images);
            // Add template to images array.
            $attachment_ids[] = 'thumb-template';
            foreach ($attachment_ids as $attachment_id) {
              if ($attachment_id) {
                ?>
            <div class="image-cont thumb<?php echo $attachment_id == 'thumb-template' ? ' ' . $attachment_id : ''; ?>"
                  <?php
                  if ($attachment_id != 'thumb-template') {
                    ?>
                    data-id="<?php echo $attachment_id; ?>"
                    data-image-url="<?php echo wp_get_attachment_url($attachment_id); ?>"
                    style="background-image: url('<?php echo wp_get_attachment_thumb_url($attachment_id); ?>')"
                    <?php
                  }
                  ?>>
              <div class="thumb-overlay">
                <div class="thumb-buttons">
                  <span class="wdseo-change-image" title="<?php _e('Change image', WD_SEO_PREFIX); ?>"></span>
                  <span class="wdseo-delete-image" title="<?php _e('Remove image', WD_SEO_PREFIX); ?>"></span>
                </div>
              </div>
            </div>
                <?php
              }
            }
            ?>
            <div class="image-cont wdseo-add-image" title="<?php _e('Add image', WD_SEO_PREFIX); ?>"></div>
          </div>
        </span>
      </div>
      <div id="wdseo_tab_twitter_content" class="wdseo-section wd-table">
        <span class="wd-group">
          <input value="0" name="wd_settings[use_og_for_twitter]" type="hidden" /><?php //hidden input with same name to have empty value. ?>
          <input <?php checked($options->use_og_for_twitter, 1); ?> id="wd-use-twitter" class="wd-radio wd-use-twitter" value="1" name="wd_settings[use_og_for_twitter]" type="checkbox" />
          <label class="wd-label-radio" for="wd-use-twitter"><?php _e('Same as OpenGraph', WD_SEO_PREFIX); ?></label>
        </span>
        <div class="wd-box-section wd-twitter-field wd-preview">
          <div class="wd-box-title">
            <strong><?php _e('Preview', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <div class="wd-social-preview wd-twitter-preview">
              <div id="wdseo_twitter_image" class="wdseo-social-image"></div>
              <div class="wdseo-social-body">
                <div class="wd-preview-social-title wd-preview-twitter-title">
                  <a href="<?php echo $current_url; ?>" target="_blank"></a>
                </div>
                <div class="wd-preview-social-description wd-preview-twitter-description"></div>
                <div class="wd-preview-social-url wd-preview-twitter-url">
                  <a href="<?php echo $current_url; ?>" target="_blank">
                    <?php echo $current_url; ?>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <span class="wd-group wd-twitter-field">
          <label class="wd-label" for="wdseo_twitter_title"><?php _e('Twitter title', WD_SEO_PREFIX); ?></label>
          <input class="wd-has-placeholder wd-set-preview-twitter-title" id="wdseo_twitter_title" name="wd_settings[twitter_title]" value="<?php echo $options->twitter_title; ?>" placeholder="<?php echo $options_defaults->twitter_title; ?>" data-default="%%title%%" type="text"/>
        </span>
          <span class="wd-group wd-twitter-field">
          <label class="wd-label" for="wdseo_twitter_description"><?php _e('Twitter description', WD_SEO_PREFIX); ?></label>
          <textarea class="wd-has-placeholder wd-set-preview-twitter-description" id="wdseo_twitter_description" name="wd_settings[twitter_description]" placeholder="<?php echo $options_defaults->twitter_description; ?>" data-default="%%excerpt%%"><?php echo $options->twitter_description; ?></textarea>
        </span>
        <span class="wd-group wd-twitter-field">
          <label class="wd-label"><?php _e('Twitter images', WD_SEO_PREFIX); ?></label>
          <div>
            <input class="image-ids" id="wdseo_twitter_images" name="wd_settings[twitter_images]" value="<?php echo $options->twitter_images; ?>" data-default="<?php echo wp_get_attachment_url($options_defaults->twitter_images); ?>" type="hidden"/>
            <?php
            // Get saved images ids.
            $attachment_ids = explode(',', $options->twitter_images);
            // Add template to images array.
            $attachment_ids[] = 'thumb-template';
            foreach ($attachment_ids as $attachment_id) {
              if ($attachment_id) {
                ?>
                <div class="image-cont thumb<?php echo $attachment_id == 'thumb-template' ? ' ' . $attachment_id : ''; ?>"
                  <?php
                  if ($attachment_id != 'thumb-template') {
                    ?>
                    data-id="<?php echo $attachment_id; ?>"
                    data-image-url="<?php echo wp_get_attachment_url($attachment_id); ?>"
                    style="background-image: url('<?php echo wp_get_attachment_thumb_url($attachment_id); ?>')"
                    <?php
                  }
                  ?>>
              <div class="thumb-overlay">
                <div class="thumb-buttons">
                  <span class="wdseo-change-image" title="<?php _e('Change image', WD_SEO_PREFIX); ?>"></span>
                  <span class="wdseo-delete-image" title="<?php _e('Remove image', WD_SEO_PREFIX); ?>"></span>
                </div>
              </div>
            </div>
                <?php
              }
            }
            ?>
            <div class="image-cont wdseo-add-image" title="<?php _e('Add image', WD_SEO_PREFIX); ?>"></div>
          </div>
        </span>
      </div>
    </div>
    <?php
    // Placeholder template.
    echo WD_SEO_Library::placeholder_template();
    echo ob_get_clean();
  }
}
