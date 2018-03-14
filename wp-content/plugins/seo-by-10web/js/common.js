/**
 * Add image.
 *
 * @param e
 * @param that
 * @param change
 */
function wdseo_add_image(e, that, change, callback) {
  e.preventDefault();
  var custom_uploader;
  var arg1 = arguments[4];
  var arg2 = arguments[5];
  // If the uploader object has already been created, reopen the dialog.
  if (custom_uploader) {
    custom_uploader.open();
    return;
  }

  custom_uploader = wp.media.frames.file_frame = wp.media({
    title: wdseo.choose_image,
    library : { type : 'image'},
    button: { text: (change === false ? wdseo.add_image : wdseo.change_image)},
    multiple: false
  });

  // When a file is selected, grab the URL.
  custom_uploader.on('select', function() {
    attachment = custom_uploader.state().get('selection').first().toJSON();
    var image_url = attachment.url;
    var thumb_url = (attachment.sizes && attachment.sizes.thumbnail)  ? attachment.sizes.thumbnail.url : image_url;

    if (change === false) {
      // Add thumbnail.
      jQuery(that).parent().find(".thumb-template")
                  .clone()
                  .insertBefore(that)
                  .removeClass("thumb-template")
                  .css({backgroundImage: "url('" + thumb_url + "')"})
                  .attr("data-image-url", image_url)
                  .attr("data-id", attachment.id);

      // Set thumbnail change and remove actions.
      jQuery(".wdseo-change-image").on("click", function (event) {
        wdseo_add_image(event, this, true, callback, arg1, arg2);
      });
      jQuery(".wdseo-delete-image").on("click", function (event) {
        wdseo_remove_image(event, this, callback, arg1, arg2);
      });
    }
    else {
      // Change thumbnail url.
      jQuery(that).closest(".thumb")
                  .css({backgroundImage: "url('" + thumb_url + "')"})
                  .attr("data-image-url", image_url)
                  .attr("data-id", attachment.id);
    }

    // Update image ids.
    var ids_cont = jQuery('#' + arg1);
    var ids_obj = new Array();
    jQuery(ids_cont).parent().find(".image-cont").not(".thumb-template").each(function() {
      var id = jQuery(this).attr('data-id');
      if (jQuery.inArray(id, ids_obj) == -1) {
        ids_obj.push(id);
      }
    });

    ids_str = ids_obj.join(",");
    ids_cont.val(ids_str);
    if (typeof callback == 'function') {
      callback(arg1, arg2);
    }
  });
  // Open the uploader dialog.
  custom_uploader.open();
}

/**
 * Remove the image.
 *
 * @param e
 * @param that
 */
function wdseo_remove_image(e, that, callback) {
  var img = jQuery(that).closest(".thumb");
  var img_id = img.data("id");
  var ids_cont = img.parent().find(".image-ids");
  img.remove();

  var ids_str = ids_cont.val();
  if (ids_str) {
    var ids_obj = ids_str.split(",");
    var index = ids_obj.indexOf(String(img_id));
    if (index > -1) {
      ids_obj.splice(index, 1);
      ids_str = ids_obj.join(",");
      ids_cont.val(ids_str);
    }
  }
  if (typeof callback == 'function') {
    var arg1 = arguments[3];
    var arg2 = arguments[4];
    callback(arg1, arg2);
  }
}
/**
 * Select given object text.
 */
function wdseo_selectText() {
  var range, jq;
  jq = jQuery(this);
  el = jq[0];

  if (jq.is(':input')) {
    jq.focus().select();
  }
  else if (document.selection) {
    range = document.body.createTextRange();
    range.moveToElementText(el);
    range.select();
  }
  else if (window.getSelection) {
    range = document.createRange();
    range.selectNode(el);
    window.getSelection().addRange(range);
  }
}
/**
 * Set placeholders to object with special class.
 */
function wdseo_set_placeholder() {
  jQuery(".wd-has-placeholder").on("click", function () {
    // Remove all open placeholder containers.
    jQuery(".wd-placeholder-cont").remove();

    // Current field.
    var that = jQuery(this);

    // Set only one time.
    if (that.parent().find(".wd-placeholder-cont").length > 0) {
      return;
    }

    // Insert placeholder container after the current field.
    var placeholder_cont = jQuery(".wd-placeholder-cont-template")
      .clone()
      .insertAfter(this)
      .removeClass("wd-placeholder-cont-template")
      .addClass("wd-placeholder-cont");
    placeholder_cont.show();

    // Show button and set onclick event to show placeholders.
    var button = placeholder_cont.find(".wd-placeholder-btn");
    button.show();
    button.on("click", function () {
      // Show placeholder and set onclick events.
      var placeholder = jQuery(this).closest(".wd-placeholder-cont").find(".wd-placeholder");
      placeholder.show();
      placeholder.find("div").on("click", function () {
        var placeholder_value = jQuery(this).attr("data-value");
        var that_id = document.getElementById(that.attr("id"));
        if (document.selection) {
          that_id.focus();
          sel = document.selection.createRange();
          sel.text = placeholder_value;
        }
        else if (that_id.selectionStart || that_id.selectionStart == '0') {
          var startPos = that_id.selectionStart;
          var endPos = that_id.selectionEnd;
          that_id.value = that_id.value.substring(0, startPos)
            + placeholder_value
            + that_id.value.substring(endPos, that_id.value.length);
        }
        else {
          that_id.value += placeholder_value;
        }

        // Set preview.
        that.trigger('change');
      });
    });

    // Remove placeholder container on blur.
    jQuery("*").on("click", function (event) {
      if (!jQuery(event.target).hasClass('wd-has-placeholder')
        && !jQuery(event.target).hasClass('wd-placeholder-btn')) {
        jQuery(".wd-placeholder-cont").remove();
      }
    });
  });
}

/**
 * Add preview event to visible elements.
 */
function wdseo_add_preview_event() {
  jQuery(".wd-set-preview-title").each(function () {
    if (!jQuery(this).is(':hidden')) {
      wdseo_set_preview(jQuery(this), ".wd-preview-title a");
    }
  });
  jQuery(".wd-set-preview-description").each(function () {
    if (!jQuery(this).is(':hidden')) {
      wdseo_set_preview(jQuery(this), ".wd-preview-description");
    }
  });
  jQuery(".wd-preview-date").hide();
  jQuery(".wd-set-preview-date:visible:checked").each(function () {
    if (jQuery(this).val() == 1) {
      jQuery(".wd-preview-date").show();
    }
    else {
      jQuery(".wd-preview-date").hide();
    }
  });

  jQuery(".wd-set-preview-og-title").each(function () {
    if (!jQuery(this).closest('.wd-box-content').is(':hidden')) {
      wdseo_set_preview(jQuery(this), ".wd-preview-og-title a");
    }
  });
  jQuery(".wd-set-preview-og-description").each(function () {
    if (!jQuery(this).closest('.wd-box-content').is(':hidden')) {
      wdseo_set_preview(jQuery(this), ".wd-preview-og-description");
    }
  });

  jQuery(".wd-set-preview-twitter-title").each(function () {
    if (!jQuery(this).closest('.wd-box-content').is(':hidden')) {
      wdseo_set_preview(jQuery(this), ".wd-preview-twitter-title a");
    }
  });
  jQuery(".wd-set-preview-twitter-description").each(function () {
    if (!jQuery(this).closest('.wd-box-content').is(':hidden')) {
      wdseo_set_preview(jQuery(this), ".wd-preview-twitter-description");
    }
  });
  jQuery(".image-ids").each(function () {
    if (!jQuery(this).closest('.wd-box-content').is(':hidden')) {
      var source_id = jQuery(this).attr('id');
      if (source_id.endsWith("opengraph_images")) {
        var destination_id = "wdseo_og_image";
      }
      else {
        var destination_id = "wdseo_twitter_image";
      }
      wdseo_set_preview_image(source_id, destination_id);
    }
  });

  jQuery(".wd-set-preview-title").on("keyup keypress blur change", function () {
    wdseo_set_preview(jQuery(this), ".wd-preview-title a");
  });
  jQuery(".wd-set-preview-description").on("keyup keypress blur change", function () {
    wdseo_set_preview(jQuery(this), ".wd-preview-description");
  });
  jQuery(".wd-set-preview-date").on("keyup keypress blur change", function () {
    if (jQuery(this).filter(':checked').val() == 1) {
      jQuery(".wd-preview-date").show();
    }
    else {
      jQuery(".wd-preview-date").hide();
    }
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

/**
 * Show/hide elements.
 */
function wdseo_show_hide_elements(condition_when_hide, fields_to_toggle) {
  if (condition_when_hide) {
    fields_to_toggle.hide();
  }
  else {
    fields_to_toggle.show();
  }
}

/**
 * Set preview.
 *
 * @param obj
 * @param set_to
 */
function wdseo_set_preview(obj, set_to) {
  var content = obj.val() ? obj.val() : (obj.attr('placeholder') != undefined ? obj.attr('placeholder') : '');
  if (content == '') {
    content = obj.attr('data-default') != undefined ? obj.attr('data-default') : '';
  }
  for (var placeholder in wdseo.placeholders) {
    content = content.replace(new RegExp(placeholder, 'g'), wdseo.placeholders[placeholder]);
  }
  jQuery(set_to).html(content);
}

/**
 * Set preview for social images.
 *
 * @param source
 * @param destination
 * @param featured_image_url
 */
function wdseo_set_preview_image(source, destination, featured_image_url) {
  var image_url = '';
  jQuery('.wd-social-preview #' + destination).hide();
  jQuery('#' + source).parent().children(".thumb:not(.thumb-template)").each(function () {
    image_url = jQuery(this).attr("data-image-url");
    wdseo_set_image_preview(image_url, destination);
    return false;
  });
  if (!image_url) {
    if (featured_image_url == undefined) {
      featured_image_id = jQuery('#_thumbnail_id').val();
      if (featured_image_id) {
        image_url = true;
        wp.media.attachment(featured_image_id).fetch().then(function () {
          image_url = wp.media.attachment(featured_image_id).get('url');
          wdseo_set_image_preview(image_url, destination);
        });
      }
    }
    else {
      image_url = featured_image_url;
      wdseo_set_image_preview(featured_image_url, destination);
    }
  }
  if (!image_url) {
    image_url = jQuery('#' + source).attr('data-default');
    wdseo_set_image_preview(image_url, destination);
  }
}

/**
 * Set preview image as background image.
 *
 * @param url
 * @param destination
 */
function wdseo_set_image_preview(url, destination) {
  if (url) {
    jQuery('#' + destination).css("background-image", 'url("' + url + '")');
    jQuery('.wd-social-preview #' + destination).show();
  }
  else {
    jQuery('.wd-social-preview #' + destination).hide();
  }
}

/**
 * Remove registered shortcodes from string.
 *
 * @param text
 * @param shortcode_names
 * @returns {*}
 */
function wdseo_remove_shortcodes_from_text(text, shortcode_names) {
  return wp.shortcode.replace(shortcode_names.join('|'), text, function() { return ' ';});
}

/**
 * Strip html tags from text. p tag is allowed to keep line breacks from tinyMce.
 *
 * @param html
 * @returns {*}
 */
function wdseo_strip_html(html) {
  html = html.replace(/<p>/g, "||p||");
  html = html.replace(/<\/p>/g, "||/p||");
  html = jQuery('<div />').html(html).text();
  html = html.replace(/\|\|p\|\|/g, "<p>");
  html = html.replace(/\|\|\/p\|\|/g, "</p>");
  return html;
}
