jQuery(document).ready(function () {
  if (typeof jQuery(document).tooltip != "undefined") {
    jQuery(document).tooltip({
      show: null,
      items: "[data-wdseo-tooltip-key]",
      content: function () {
        var element = jQuery(this);
        if (element.is("[data-wdseo-tooltip-key]")) {
          var tooltip_key = element.attr('data-wdseo-tooltip-key');
          var html = jQuery('#wdseo-tooltip-info-' + tooltip_key).html();
          return html;
        }
      },
      open: function (event, ui) {
        if (typeof(event.originalEvent) === 'undefined') {
          return false;
        }
        var $id = jQuery(ui.tooltip).attr('id');
        // close any lingering tooltips
        jQuery('div.ui-tooltip').not('#' + $id).remove();
      },
      close: function (event, ui) {
        ui.tooltip.hover(function () {
            jQuery(this).stop(true).fadeTo(400, 1);
          },
          function () {
            jQuery(this).fadeOut('400', function () {
              jQuery(this).remove();
            });
          });
      },
      position: {
        my: "center top+30",
        at: "center top",
        using: function (position, feedback) {
          jQuery(this).css(position);
          jQuery("<div>")
          .addClass("tooltip-arrow")
          .addClass(feedback.vertical)
          .addClass(feedback.horizontal)
          .appendTo(this);
        }
      }
    });
  }

  change_post_type("." + jQuery("select[name='wd_settings[types]']").val());

  // Add onchange event to page types list.
  jQuery("select[name='wd_settings[types]']").on('change', function () {
    change_post_type("." + jQuery(this).val());
  });

  // Add onclick event to Get Google Authorization Code button.
  jQuery("[name='get_google_authorization_code']").on("click", function (event) {
    window.open(jQuery("[name='authorization_url']").val(), "", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width=600, height=500, top=" + (screen.height / 2 - 250) + ", left=" + (screen.width / 2 - 300));
    return false;
  });

  // Add onclick event to add image button.
  jQuery(".wdseo-add-image").on("click", function (event) {
    var source_id = jQuery(this).closest('.wd-group').find('.image-ids').attr('id');
    if (source_id.endsWith("opengraph_images")) {
      var destination_id = "wdseo_og_image";
    }
    else {
      var destination_id = "wdseo_twitter_image";
    }
    wdseo_add_image(event, this, false, wdseo_set_preview_image, source_id, destination_id);
  });

  // Add change and delete actions to existing images.
  jQuery(".wdseo-change-image").on("click", function (event) {
    var source_id = jQuery(this).closest('.wd-group').find('.image-ids').attr('id');
    if (source_id.endsWith("opengraph_images")) {
      var destination_id = "wdseo_og_image";
    }
    else {
      var destination_id = "wdseo_twitter_image";
    }
    wdseo_add_image(event, this, true, wdseo_set_preview_image, source_id, destination_id);
  });
  jQuery(".wdseo-delete-image").on("click", function (event) {
    var source_id = jQuery(this).closest('.wd-group').find('.image-ids').attr('id');
    if (source_id.endsWith("opengraph_images")) {
      var destination_id = "wdseo_og_image";
    }
    else {
      var destination_id = "wdseo_twitter_image";
    }
    wdseo_remove_image(event, this, wdseo_set_preview_image, source_id, destination_id);
  });

  // Select given object text.
  jQuery(".wdseo_form").on("click", ".wd-select-all", wdseo_selectText);

  // Select box.
  if (jQuery("#wd-exclude-post-types, #wd-exclude-taxonomies").length > 0) {
    jQuery("#wd-exclude-post-types, #wd-exclude-taxonomies").select2({
      closeOnSelect: false,
    });
  }
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

  if (jQuery("select[name='country']").length > 0) {
    jQuery("select[name='country']").select2({
      tags: false,
      selectOnClose: true,
    });
  }

  // Add tabs.
  jQuery(".wdseo_tabs").each(function () {
    jQuery(this).tabs();
  });

  // Set no items row width.
  jQuery(".colspanchange").attr("colspan", jQuery(".wdseo_form table.adminlist>thead>tr>th:visible").length);

  // Show/hide twitter fields.
  jQuery('.wd-use-twitter').on("click", function () {
    wdseo_show_hide_elements(jQuery(this).is(':checked'), jQuery(this).closest('.wdseo-section').find('.wd-twitter-field'));
    wdseo_show_hide_elements(jQuery(this).is(':checked'), jQuery('.wd-box-section.wd-twitter-field'));
  });
  jQuery('.wd-use-twitter').each(function () {
    wdseo_show_hide_elements(jQuery(this).is(':checked'), jQuery(this).closest('.wdseo-section').find('.wd-twitter-field'));
  });

  // Set placeholders.
  wdseo_set_placeholder();

  // Set preview.
  wdseo_add_preview_event();

  // Change notice status.
  jQuery("body").on("click", "button.notice-dismiss", function() {
    change_notice_status(this);
  });

  // Add search event to search input.
  jQuery("input[name='s']").on("keypress", function (event) {
    var key_code = (event.keyCode ? event.keyCode : event.which);
    if (key_code == 13) {
      search();
      return false;
    }
  });

  // Show/hide Google Authenticate button depend on Authorization Code input value.
  jQuery(".wd-group input[name='code']").on("click change keyup", function () {
    if ( jQuery(this).val() != "" ) {
      jQuery(".authenticate-btn").removeClass("wdseo-hide");
    }
    else {
      jQuery(".authenticate-btn").addClass("wdseo-hide");
    }
  });
});

/**
 * Show parameters for given type.
 *
 * @param obj
 */
function change_post_type(obj) {
  jQuery(".wd-type").hide();
  jQuery(obj).show();

  wdseo_show_hide_elements(jQuery(obj).find('.wdseo-social').length == 0, jQuery('.wd-social-section'));
  // Show/hide twitter preview.
  wdseo_show_hide_elements(jQuery(obj).find('.wd-use-twitter').length == 0 || jQuery(obj).find('.wd-use-twitter').is(':checked'), jQuery('.wd-box-section.wd-twitter-field'));

  // Set preview.
  wdseo_add_preview_event();
}
/**
 * Set href for given thickbox class.
 *
 * @param that
 */
function set_thickbox_href(that, event, is_active) {
  if (!is_active) {
    alert(wdseo.free_version);
    event.stopPropagation();
    return false;
  }
  // Container id which content will be shown in popup.
  var inlineId = jQuery(that).attr("data-inlineId");

  // URL will be redirected.
  var url = jQuery(that).attr("data-url");
  jQuery("#" + inlineId + " input[type='hidden']").val(url);
  var redirect_url = jQuery(that).attr("data-redirect-url");
  jQuery("#redirect_url").val(redirect_url);

  var width = Math.min(jQuery(that).attr("data-width"), jQuery(window).width() - 30);
  // Get container height if height is not set.
  var height = jQuery(that).attr("data-height") != "" ? jQuery(that).attr("data-height") : jQuery("#" + inlineId).height() + 15;
  height = Math.min(height, jQuery(window).height() - 50);

  jQuery(that).attr("href", "#TB_inline&width=" + width + "&height=" + height + "&inlineId=" + inlineId);
}

/**
 * Submit form to save redirect URL.
 */
function create_redirect() {
  var url = jQuery("input[name='url']").val();
  var redirect_url = jQuery("input[name='redirect_url']").val();

  var form = jQuery("form[name='wdseo_form']");
  jQuery("<input />").attr("type", "hidden")
  .attr("name", "url")
  .attr("value", url)
  .appendTo(form);
  jQuery("<input />").attr("type", "hidden")
  .attr("name", "redirect_url")
  .attr("value", redirect_url)
  .appendTo(form);
  jQuery("<input />").attr("type", "hidden")
  .attr("name", "task")
  .attr("value", "create_redirect")
  .appendTo(form);
  form.submit();
}

/**
 * Mark as fixed.
 *
 * @param that
 */
function mark_as_fixed(that, event, is_active) {
  if (!is_active) {
    alert(wdseo.free_version);
    event.stopPropagation();
    return false;
  }
  var url = jQuery(that).attr("data-url");
  var form = jQuery("form[name='wdseo_form']");
  jQuery("<input />").attr("type", "hidden")
  .attr("name", "url")
  .attr("value", url)
  .appendTo(form);
  jQuery("<input />").attr("type", "hidden")
  .attr("name", "task")
  .attr("value", "mark_as_fixed")
  .appendTo(form);
  form.submit();
}

/**
 * Filter.
 */
function filter(that) {
  var form = jQuery("form[name='wdseo_form']");

  form.attr("action", window.location + "&" + jQuery(that).attr("name") + "=" + jQuery(that).val());

  form.submit();
}

/**
 * Search.
 */
function search() {
  var form = jQuery("form[name='wdseo_form']");

  form.attr("action", window.location + "&paged=1&s=" + jQuery("input[name='s']").val());

  form.submit();
}

/**
 * Change notice status.
 *
 * @param key
 */
function change_notice_status(that) {
  var post_data = {};
  post_data['task'] = 'dismiss';
  post_data['key'] = jQuery(that).parent(".notice").data("value");
  post_data[wdseo.nonce] = jQuery("#" + wdseo.nonce).val();

  jQuery.post(
    window.location,
    post_data,
    function (data, textStatus, errorThrown) {
      location.reload();
    });
}