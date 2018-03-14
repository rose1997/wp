<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Settings page view class.
 */
class WDSeometa_infoView extends WDSeoAdminView {
  /**
   * Display page.
   */
  public function display($options, $post_types) {
    ob_start();
    echo $this->header();
    echo $this->body($options, $post_types);

    // Pass the content to form.
    echo $this->form(ob_get_clean());
  }

  /**
   * Page header.
   *
   * @return string Generated html.
   */
  private function header() {
    wp_enqueue_style(WD_SEO_PREFIX . '_select2');
    wp_enqueue_script(WD_SEO_PREFIX . '_select2');
    ob_start();
    echo $this->title(__('Meta information', WD_SEO_PREFIX));
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
   * @param array $groups
   *
   * @return string Body html.
   */
  private function body($options, $groups) {
    // Add all scripts, styles necessary to use media library.
    wp_enqueue_media();
    wp_enqueue_script('jquery-ui-tabs');
    ob_start();
    ?>
    <div class="wd-table">
      <div class="wd-table-col wd-table-col-50 wd-table-col-left">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('Pages', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <span class="wd-group">
              <select name="wd_settings[types]"><?php
                foreach ( $groups as $group ) {
                  ?>
                  <optgroup label="<?php echo $group['title']; ?>">
                  <?php
                  foreach ( $group['types'] as $type => $type_arr ) {
                    ?>
                    <option <?php selected($options->types, $type); ?> value="<?php echo $type; ?>"><?php echo $type_arr['name']; ?></option>
                    <?php
                  }
                  ?>
                </optgroup>
                  <?php
                }
                ?>
              </select>
              <p class="description"><?php _e('Choose page type to set meta information.', WD_SEO_PREFIX); ?></p>
            </span>
          </div>
        </div>
        <?php
        foreach ( $groups as $group_type => $group ) {
          foreach ( $group['types'] as $type => $type_arr ) {
            ?>
        <div class="wd-box-section wd-type <?php echo $type; ?>">
          <div class="wd-box-title">
            <strong><?php echo $type_arr['name']; ?></strong>
          </div>
          <div class="wd-box-content">
            <?php
            if ( isset($type_arr['description']) && $type_arr['description'] != '' ) {
              ?>
            <span class="wd-group">
              <p class="description"><?php echo $type_arr['description']; ?></p>
            </span>
              <?php
            }
            if (!in_array('meta_title', $type_arr['exclude_fields'])) {
              ?>
            <span class="wd-group">
              <label class="wd-label" for="<?php echo $type; ?>_meta_title"><?php _e('Meta title', WD_SEO_PREFIX); ?></label>
              <input class="wd-has-placeholder wd-set-preview-title" id="<?php echo $type; ?>_meta_title" name="wd_settings[metas][<?php echo $type; ?>][meta_title]" value="<?php echo $options->metas->$type->meta_title; ?>" type="text" />
            </span>
              <?php
            }
            if (!in_array('meta_description', $type_arr['exclude_fields'])) {
              ?>
            <span class="wd-group">
              <label class="wd-label" for="<?php echo $type; ?>_meta_description"><?php _e('Meta description', WD_SEO_PREFIX); ?></label>
              <textarea class="wd-has-placeholder wd-set-preview-description" id="<?php echo $type; ?>_meta_description" name="wd_settings[metas][<?php echo $type; ?>][meta_description]"><?php echo $options->metas->$type->meta_description; ?></textarea>
            </span>
              <?php
            }
            if (!in_array('meta_keywords', $type_arr['exclude_fields'])) {
              ?>
            <span class="wd-group">
              <label class="wd-label" for="<?php echo $type; ?>_meta_keywords"><?php _e('Keywords', WD_SEO_PREFIX); ?></label>
              <select class="wd-select2 wd-hide-droprown" id="<?php echo $type; ?>_meta_keywords" name="wd_settings[metas][<?php echo $type; ?>][meta_keywords][]" multiple>
              <?php
                foreach ( $options->metas->$type->meta_keywords as $keyword ) {
                ?>
                  <option <?php selected(true, true); ?> value="<?php echo $keyword; ?>" data-select2-tag="true"><?php echo $keyword; ?></option>
                <?php
                }
              ?>
              </select>
            </span>
              <?php
            }
            if (!in_array('index', $type_arr['exclude_fields'])) {
              ?>
            <span class="wd-group">
              <label class="wd-label"><?php _e('Meta robots', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->metas->$type->index, 1); ?> id="<?php echo $type; ?>_index1" class="wd-radio" value="1" name="wd_settings[metas][<?php echo $type; ?>][index]" type="radio" />
              <label class="wd-label-radio" for="<?php echo $type; ?>_index1"><?php _e('Index', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->metas->$type->index, 0); ?> id="<?php echo $type; ?>_index0" class="wd-radio" value="0" name="wd_settings[metas][<?php echo $type; ?>][index]" type="radio" />
              <label class="wd-label-radio" for="<?php echo $type; ?>_index0"><?php _e('No index', WD_SEO_PREFIX); ?></label>
            </span>
              <?php
            }
            if (!in_array('follow', $type_arr['exclude_fields'])) {
              ?>
            <span class="wd-group">
              <input <?php checked($options->metas->$type->follow, 1); ?> id="<?php echo $type; ?>_follow1" class="wd-radio" value="1" name="wd_settings[metas][<?php echo $type; ?>][follow]" type="radio" />
              <label class="wd-label-radio" for="<?php echo $type; ?>_follow1"><?php _e('Follow', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->metas->$type->follow, 0); ?> id="<?php echo $type; ?>_follow0" class="wd-radio" value="0" name="wd_settings[metas][<?php echo $type; ?>][follow]" type="radio" />
              <label class="wd-label-radio" for="<?php echo $type; ?>_follow0"><?php _e('No follow', WD_SEO_PREFIX); ?></label>
            </span>
              <?php
            }
            if (!in_array('robots_advanced', $type_arr['exclude_fields'])) {
              ?>
            <span class="wd-group">
              <input value="0" name="wd_settings[metas][<?php echo $type; ?>][robots_advanced][]" type="hidden" /><?php //hidden input with same name to have empty value. ?>
              <input <?php checked(1, in_array('noodp', $options->metas->$type->robots_advanced)); ?> id="<?php echo $type; ?>_wd-meta-advanced-noodp" class="wd-radio" value="noodp" name="wd_settings[metas][<?php echo $type; ?>][robots_advanced][]" type="checkbox" />
              <label class="wd-label-radio" for="<?php echo $type; ?>_wd-meta-advanced-noodp"><?php _e('NO ODP', WD_SEO_PREFIX); ?></label><br />
              <input <?php checked(1, in_array('noimageindex', $options->metas->$type->robots_advanced)); ?> id="<?php echo $type; ?>_wd-meta-advanced-noimageindex" class="wd-radio" value="noimageindex" name="wd_settings[metas][<?php echo $type; ?>][robots_advanced][]" type="checkbox" />
              <label class="wd-label-radio" for="<?php echo $type; ?>_wd-meta-advanced-noimageindex"><?php _e('No Image Index', WD_SEO_PREFIX); ?></label><br />
              <input <?php checked(1, in_array('noarchive', $options->metas->$type->robots_advanced)); ?> id="<?php echo $type; ?>_wd-meta-advanced-noarchive" class="wd-radio" value="noarchive" name="wd_settings[metas][<?php echo $type; ?>][robots_advanced][]" type="checkbox" />
              <label class="wd-label-radio" for="<?php echo $type; ?>_wd-meta-advanced-noarchive"><?php _e('No Archive', WD_SEO_PREFIX); ?></label><br />
              <input <?php checked(1, in_array('nosnippet', $options->metas->$type->robots_advanced)); ?> id="<?php echo $type; ?>_wd-meta-advanced-nosnippet" class="wd-radio" value="nosnippet" name="wd_settings[metas][<?php echo $type; ?>][robots_advanced][]" type="checkbox" />
              <label class="wd-label-radio" for="<?php echo $type; ?>_wd-meta-advanced-nosnippet"><?php _e('No Snippet', WD_SEO_PREFIX); ?></label>
            </span>
              <?php
            }
            if (!in_array('date', $type_arr['exclude_fields'])) {
              ?>
            <span class="wd-group">
              <label class="wd-label"><?php _e('Date in snippet preview', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->metas->$type->date, 1); ?> id="<?php echo $type; ?>_date1" class="wd-radio wd-set-preview-date" value="1" name="wd_settings[metas][<?php echo $type; ?>][date]" type="radio" />
              <label class="wd-label-radio" for="<?php echo $type; ?>_date1"><?php _e('Show', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->metas->$type->date, 0); ?> id="<?php echo $type; ?>_date0" class="wd-radio wd-set-preview-date" value="0" name="wd_settings[metas][<?php echo $type; ?>][date]" type="radio" />
              <label class="wd-label-radio" for="<?php echo $type; ?>_date0"><?php _e('Hide', WD_SEO_PREFIX); ?></label>
            </span>
              <?php
            }
            if (!in_array('metabox', $type_arr['exclude_fields'])) {
              ?>
            <span class="wd-group">
              <label class="wd-label"><?php _e('SEO metabox', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->metas->$type->metabox, 1); ?> id="<?php echo $type; ?>_metabox1" class="wd-radio" value="1" name="wd_settings[metas][<?php echo $type; ?>][metabox]" type="radio" />
              <label class="wd-label-radio" for="<?php echo $type; ?>_metabox1"><?php _e('Show', WD_SEO_PREFIX); ?></label>
              <input <?php checked($options->metas->$type->metabox, 0); ?> id="<?php echo $type; ?>_metabox0" class="wd-radio" value="0" name="wd_settings[metas][<?php echo $type; ?>][metabox]" type="radio" />
              <label class="wd-label-radio" for="<?php echo $type; ?>_metabox0"><?php _e('Hide', WD_SEO_PREFIX); ?></label>
            </span>
              <?php
            }
            if (!in_array('opengraph', $type_arr['exclude_fields'])) {
              ?>
              <div id="<?php echo $type; ?>_tabs" class="wdseo_tabs wdseo-social">
                <ul class="wdseo-tabs">
                  <li class="tabs">
                    <a href="#<?php echo $type; ?>_tab_opengraph_content" class="wdseo-tablink"><?php _e('Facebook / OpenGraph', WD_SEO_PREFIX); ?></a>
                  </li>
                  <li class="tabs">
                    <a href="#<?php echo $type; ?>_tab_twitter_content" class="wdseo-tablink"><?php _e('Twitter', WD_SEO_PREFIX); ?></a>
                  </li>
                </ul>
                <div id="<?php echo $type; ?>_tab_opengraph_content" class="wdseo-section wd-table">
                  <span class="wd-group">
                    <label class="wd-label" for="<?php echo $type; ?>_opengraph_title"><?php _e('OpenGraph title', WD_SEO_PREFIX); ?></label>
                    <input class="wd-has-placeholder wd-set-preview-og-title" id="<?php echo $type; ?>_opengraph_title" name="wd_settings[metas][<?php echo $type; ?>][opengraph_title]" value="<?php echo $options->metas->$type->opengraph_title; ?>" type="text"/>
                  </span>
                  <span class="wd-group">
                    <label class="wd-label" for="<?php echo $type; ?>_opengraph_description"><?php _e('OpenGraph description', WD_SEO_PREFIX); ?></label>
                    <textarea class="wd-has-placeholder wd-set-preview-og-description" id="<?php echo $type; ?>_opengraph_description" name="wd_settings[metas][<?php echo $type; ?>][opengraph_description]"><?php echo $options->metas->$type->opengraph_description; ?></textarea>
                  </span>
                  <span class="wd-group">
                    <label class="wd-label"><?php _e('OpenGraph images', WD_SEO_PREFIX); ?></label>
                    <div>
                      <input class="image-ids" id="<?php echo $type; ?>_opengraph_images" name="wd_settings[metas][<?php echo $type; ?>][opengraph_images]" value="<?php echo $options->metas->$type->opengraph_images; ?>" type="hidden"/>
                      <?php
                      // Get saved images ids.
                      $attachment_ids = explode(',', $options->metas->$type->opengraph_images);
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
                <div id="<?php echo $type; ?>_tab_twitter_content" class="wdseo-section wd-table">
                  <span class="wd-group">
                    <input value="0" name="wd_settings[metas][<?php echo $type; ?>][use_og_for_twitter]" type="hidden" /><?php //hidden input with same name to have empty value. ?>
                    <input <?php checked($options->metas->$type->use_og_for_twitter, 1); ?> id="wd-use-twitter-<?php echo $type; ?>" class="wd-radio wd-use-twitter" value="1" name="wd_settings[metas][<?php echo $type; ?>][use_og_for_twitter]" type="checkbox" />
                    <label class="wd-label-radio" for="wd-use-twitter-<?php echo $type; ?>"><?php _e('Same as OpenGraph', WD_SEO_PREFIX); ?></label>
                  </span>
                  <span class="wd-group wd-twitter-field">
                    <label class="wd-label" for="<?php echo $type; ?>_twitter_title"><?php _e('Twitter title', WD_SEO_PREFIX); ?></label>
                    <input class="wd-has-placeholder wd-set-preview-twitter-title" id="<?php echo $type; ?>_twitter_title" name="wd_settings[metas][<?php echo $type; ?>][twitter_title]" value="<?php echo $options->metas->$type->twitter_title; ?>" type="text"/>
                  </span>
                  <span class="wd-group wd-twitter-field">
                    <label class="wd-label" for="<?php echo $type; ?>_twitter_description"><?php _e('Twitter description', WD_SEO_PREFIX); ?></label>
                    <textarea class="wd-has-placeholder wd-set-preview-twitter-description" id="<?php echo $type; ?>_twitter_description" name="wd_settings[metas][<?php echo $type; ?>][twitter_description]"><?php echo $options->metas->$type->twitter_description; ?></textarea>
                  </span>
                  <span class="wd-group wd-twitter-field">
                    <label class="wd-label"><?php _e('Twitter images', WD_SEO_PREFIX); ?></label>
                    <div>
                      <input class="image-ids" id="<?php echo $type; ?>_twitter_images" name="wd_settings[metas][<?php echo $type; ?>][twitter_images]" value="<?php echo $options->metas->$type->twitter_images; ?>" type="hidden"/>
                      <?php
                      // Get saved images ids.
                      $attachment_ids = explode(',', $options->metas->$type->twitter_images);
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
            }
            ?>
          </div>
        </div>
            <?php
          }
        }
        // Placeholder template.
        echo WD_SEO_Library::placeholder_template();
        $site_url = esc_url(site_url());
        ?>
      </div>
      <div class="wd-table-col wd-table-col-50 wd-table-col-right">
        <div class="wd-box-section">
          <div class="wd-box-title">
            <strong><?php _e('Preview', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <div class="wd-preview">
              <div class="wd-preview-title">
                <h3>
                  <a href="<?php echo $site_url; ?>" target="_blank"></a>
                </h3>
              </div>
              <div class="wd-preview-url">
                <a href="<?php echo $site_url; ?>" target="_blank">
                  <?php echo $site_url; ?>
                </a>
              </div>
              <div class="wd-preview-date">
                <?php echo date("M d, Y"); ?>
              </div>
              <div class="wd-preview-description"></div>
            </div>
          </div>
        </div>
        <div class="wd-box-section wd-social-section">
          <div class="wd-box-title">
            <strong><?php _e('Facebook / OpenGraph', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <div class="wd-social-preview wd-og-preview">
              <div id="wdseo_og_image" class="wdseo-social-image"></div>
              <div class="wdseo-social-body">
                <div class="wd-preview-social-title wd-preview-og-title">
                  <a href="<?php echo $site_url; ?>" target="_blank"></a>
                </div>
                <div class="wd-preview-social-description wd-preview-og-description"></div>
                <div class="wd-preview-social-url wd-preview-og-url">
                  <a href="<?php echo $site_url; ?>" target="_blank">
                    <?php echo $site_url; ?>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="wd-box-section wd-social-section wd-twitter-field">
          <div class="wd-box-title">
            <strong><?php _e('Twitter', WD_SEO_PREFIX); ?></strong>
          </div>
          <div class="wd-box-content">
            <div class="wd-social-preview wd-twitter-preview">
              <div id="wdseo_twitter_image" class="wdseo-social-image"></div>
              <div class="wdseo-social-body">
                <div class="wd-preview-social-title wd-preview-twitter-title">
                  <a href="<?php echo $site_url; ?>" target="_blank"></a>
                </div>
                <div class="wd-preview-social-description wd-preview-twitter-description"></div>
                <div class="wd-preview-social-url wd-preview-twitter-url">
                  <a href="<?php echo $site_url; ?>" target="_blank">
                    <?php echo $site_url; ?>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php
    return ob_get_clean();
  }
}
