jQuery(document).ready(function () {
  // Add onclick event to add image button.
  jQuery(".wdseo-add-image").on("click", function (event) {
    wdseo_add_image(event, this, false, wdseo_set_preview_image, jQuery(this).closest('.wd-group').find('.image-ids').attr('id'), jQuery(this).closest('.wdseo-section').find('.wdseo-social-image').attr('id'));
  });

  // Add change and delete actions to existing images.
  jQuery(".wdseo-change-image").on("click", function (event) {
    wdseo_add_image(event, this, true, wdseo_set_preview_image, jQuery(this).closest('.wd-group').find('.image-ids').attr('id'), jQuery(this).closest('.wdseo-section').find('.wdseo-social-image').attr('id'));
  });
  jQuery(".wdseo-delete-image").on("click", function (event) {
    wdseo_remove_image(event, this, wdseo_set_preview_image, jQuery(this).closest('.wd-group').find('.image-ids').attr('id'), jQuery(this).closest('.wdseo-section').find('.wdseo-social-image').attr('id'));
  });

  // Select given object text.
  jQuery(".wdseo_form").on("click", ".wd-select-all", wdseo_selectText);

  // OpenGraph tabs for each post type.
  jQuery('.wdseo_tabs').each(function () {
    jQuery(this).tabs();
  });

  // Select box.
  if (jQuery(".wd-select2").length > 0) {
    jQuery(".wd-select2").each(function () {
      jQuery(this).select2({
        tags: true,
        selectOnClose: true,
        width: '100%',
        dropdownParent: jQuery(this).parent(),
      });
    });
  }

  // Show/hide twitter fields.
  jQuery('.wd-use-twitter').on("click", function () {
    wdseo_show_hide_elements(jQuery(this).is(':checked'), jQuery(this).closest('.wdseo-section').find('.wd-twitter-field'));
  });
  jQuery('.wd-use-twitter').each(function () {
    wdseo_show_hide_elements(jQuery(this).is(':checked'), jQuery(this).closest('.wdseo-section').find('.wd-twitter-field'));
  });

  wdseo_set_placeholder_values();

  // Set placeholders.
  wdseo_set_placeholder();

  // Set preview.
  wdseo_add_preview_event();
});

/**
 * Set placeholders values.
 *
 */
function wdseo_set_placeholder_values() {
  /**
   * post name
   */
  jQuery("#title").on("keyup keypress blur change", function () {
    wdseo.placeholders["%%title%%"] = jQuery("#title").val();
    wdseo_set_preview_all();
  });

  /**
   * excerpt
   */
  jQuery("#excerpt").on("keyup keypress blur change", function () {
    wdseo.placeholders["%%excerpt_only%%"] = jQuery("#excerpt").val();
    wdseo.placeholders["%%excerpt%%"] = jQuery("#excerpt").val() ? jQuery("#excerpt").val() : wdseo_remove_shortcodes_from_text(wdseo_strip_html(tinyMCE.get('content').getContent()), wdseo.shortcodes);
    wdseo_set_preview_all();
  });

  /**
   * post content
   */
  jQuery( document ).on( 'tinymce-editor-init', function( event, editor ) {
    tinyMCE.get('content').on('keyup keypress blur change', function(e) {
      wdseo.placeholders["%%excerpt%%"] = jQuery("#excerpt").val() ? jQuery("#excerpt").val() : wdseo_remove_shortcodes_from_text(wdseo_strip_html(tinyMCE.get('content').getContent()), wdseo.shortcodes);
      wdseo_set_preview_all();
    });
  });

  jQuery("#content").on("keyup keypress blur change", function () {
    wdseo.placeholders["%%excerpt%%"] = jQuery("#excerpt").val() ? jQuery("#excerpt").val() : wdseo_remove_shortcodes_from_text(wdseo_strip_html(jQuery("#content").val()), wdseo.shortcodes);
    wdseo_set_preview_all();
  });

  /**
   * post tags
   */
  jQuery("#post_tag .tagadd, #post_tag .ntdelbutton, #tagcloud-post_tag a").on("click", function () {
    wdseo.placeholders["%%tag%%"] = wdseo_get_tags();
    wdseo_set_preview_all();
    jQuery("#post_tag .ntdelbutton").on("click", function () {
      wdseo.placeholders["%%tag%%"] = wdseo_get_tags();
      wdseo_set_preview_all();
    });
  });

  /**
   * post categories
   */
  jQuery("#category-all ul.categorychecklist li input").on("change", function () {
    wdseo.placeholders["%%category%%"] = wdseo_get_categories();
    wdseo_set_preview_all();
  });

  /**
   * taxonomy name
   */
  jQuery("#edittag #name").on("keyup keypress blur change", function () {
    wdseo.placeholders["%%term_title%%"] = jQuery("#edittag #name").val();
    wdseo_set_preview_all();
  });

  /**
   * taxonomy description
   */
  jQuery("#edittag #description").on("keyup keypress blur change", function () {
    wdseo.placeholders["%%term_description%%"] = jQuery("#edittag #description").val();
    wdseo_set_preview_all();
  });

  /**
   * featured image remove
   */
  jQuery("#postimagediv").on("click", "#remove-post-thumbnail", function () {
    wdseo_set_preview_image("wdseo_opengraph_images", "wdseo_og_image", '');
    wdseo_set_preview_image("wdseo_twitter_images", "wdseo_twitter_image", '');
  });

  /**
   * featured image select, change
   */
  var featuredImage = wp.media.featuredImage.frame();
  featuredImage.on( "select", function() {
    var imageDetails = featuredImage.state().get( "selection" ).first().attributes;
    wdseo_set_preview_image("wdseo_opengraph_images", "wdseo_og_image", imageDetails.url);
    wdseo_set_preview_image("wdseo_twitter_images", "wdseo_twitter_image", imageDetails.url);
  } );
}

/**
 * Set preview.
 *
 */
function wdseo_set_preview_all() {
  wdseo_set_preview(jQuery(".wd-set-preview-title"), jQuery(".wd-preview-title a"));
  wdseo_set_preview(jQuery(".wd-set-preview-description"), jQuery(".wd-preview-description"));
  wdseo_set_preview(jQuery(".wd-set-preview-og-title"), jQuery(".wd-preview-og-title a"));
  wdseo_set_preview(jQuery(".wd-set-preview-og-description"), jQuery(".wd-preview-og-description"));
  wdseo_set_preview(jQuery(".wd-set-preview-twitter-title"), jQuery(".wd-preview-twitter-title a"));
  wdseo_set_preview(jQuery(".wd-set-preview-twitter-description"), jQuery(".wd-preview-twitter-description"));
}

/**
 * Get categories.
 *
 */
function wdseo_get_categories() {
  var category_names = [];
  jQuery("#category-all ul.categorychecklist li input:checked").each(function () {
    category_names.push(jQuery.trim(jQuery(this).parent()[0].innerText));
  });
  return category_names.join(', ');
}

/**
 * Get tags.
 *
 */
function wdseo_get_tags() {
  var tag_names = [];
  jQuery("#post_tag .tagchecklist>span").each(function () {
    var element = jQuery(this).clone();
    element.children().remove();
    tag_names.push(jQuery.trim(element[0].innerText));
  });
  return tag_names.join(', ');
}

/**
 * Add preview event to visible elements.
 */
function wdseo_add_preview_event() {
  jQuery(".wd-set-preview-title").each(function () {
    wdseo_set_preview(jQuery(this), ".wd-preview-title a");
  });
  jQuery(".wd-set-preview-description").each(function () {
    wdseo_set_preview(jQuery(this), ".wd-preview-description");
  });

  jQuery(".wd-set-preview-og-title").each(function () {
    wdseo_set_preview(jQuery(this), ".wd-preview-og-title a");
  });
  jQuery(".wd-set-preview-og-description").each(function () {
    wdseo_set_preview(jQuery(this), ".wd-preview-og-description");
  });
  wdseo_set_preview_image("wdseo_opengraph_images", "wdseo_og_image");

  jQuery(".wd-set-preview-twitter-title").each(function () {
    wdseo_set_preview(jQuery(this), ".wd-preview-twitter-title a");
  });
  jQuery(".wd-set-preview-twitter-description").each(function () {
    wdseo_set_preview(jQuery(this), ".wd-preview-twitter-description");
  });
  wdseo_set_preview_image("wdseo_twitter_images", "wdseo_twitter_image");

  jQuery(".wd-set-preview-title").on("keyup keypress blur change", function () {
    wdseo_set_preview(jQuery(this), ".wd-preview-title a");
  });
  jQuery(".wd-set-preview-description").on("keyup keypress blur change", function () {
    wdseo_set_preview(jQuery(this), ".wd-preview-description");
  });

  jQuery(".wd-set-preview-og-title").on("keyup keypress blur change", function () {
    wdseo_set_preview(jQuery(this), ".wd-preview-og-title a");
  });
  jQuery(".wd-set-preview-og-description").on("keyup keypress blur change", function () {
    wdseo_set_preview(jQuery(this), ".wd-preview-og-description");
  });

  jQuery(".wd-set-preview-twitter-title").on("keyup keypress blur change", function () {
    wdseo_set_preview(jQuery(this), ".wd-preview-twitter-title a");
  });
  jQuery(".wd-set-preview-twitter-description").on("keyup keypress blur change", function () {
    wdseo_set_preview(jQuery(this), ".wd-preview-twitter-description");
  });
}
