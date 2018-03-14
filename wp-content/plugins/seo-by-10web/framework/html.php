<?php
defined('ABSPATH') || die('Access Denied');

/**
 * HTML class to create necessary HTML templates.
 */
class WD_SEO_HTML {
  public static $total_in_page = 20;

  /**
   * Generate message by message id.
   *
   * @param int $message_id
   * @param string $message
   * @param string $type
   *
   * @return mixed|string|void
   */
  public static function message($message_id, $message = '', $type = 'updated') {
    switch ( $message_id ) {
      case 0: {
        break;
      }
      case 1: {
        $message = __("The changes are saved.", WD_SEO_PREFIX);
        $type = 'updated';
        break;
      }
      case 2: {
        $message = __("Failed to save changes.", WD_SEO_PREFIX);
        $type = 'error';
        break;
      }
      case 3: {
        $message = __("You must save changes.", WD_SEO_PREFIX);
        $type = 'error';
        break;
      }
      case 4: {
        $message = __("Sitemap XML generated successfully.", WD_SEO_PREFIX);
        $type = 'updated';
        break;
      }
      case 5: {
        $message = __("Failed.", WD_SEO_PREFIX);
        $type = 'error';
        break;
      }
      case 6: {
        $message = __("Plugin succesfully deactivated.", WD_SEO_PREFIX);
        $type = 'updated';
        break;
      }
      case 7: {
        $message = __("Sitemap successfully deleted.", WD_SEO_PREFIX);
        $type = 'updated';
        break;
      }
      default: {
        $message = '';
        break;
      }
    }
    if ($message) {
      ob_start();
      ?><div class="<?php echo $type; ?> below-h2">
      <p>
        <strong><?php echo $message; ?></strong>
      </p>
      </div><?php
      $message = ob_get_clean();
    }
    return $message;
  }

  /**
   * Ordering.
   *
   * @param        $id
   * @param        $orderby
   * @param        $order
   * @param        $text
   * @param        $page_url
   * @param string $additional_class
   *
   * @return string
   */
  public static function ordering($id, $orderby, $order, $text, $page_url, $additional_class = '', $is_active = true) {
    $class = array(
      'manage-column',
      ($orderby == $id ? 'sorted': 'sortable'),
      $order,
      $additional_class,
      'col_' . $id,
    );
    $order = (($orderby == $id) && ($order == 'asc')) ? 'desc' : 'asc';
    ob_start();
    ?>
    <th id="<?php echo $id; ?>" class="<?php echo implode(' ', $class); ?>">
      <?php
      if ($is_active) {
        ?>
        <a href="<?php echo add_query_arg(array('orderby' => $id, 'order' => $order), $page_url); ?>"
           title="<?php _e('Click to sort by this item', WD_SEO_PREFIX); ?>">
          <span><?php echo $text; ?></span><span class="sorting-indicator"></span>
        </a>
      <?php
      }
      else {
      ?>
        <span><?php echo $text; ?></span>
      <?php
      }
      ?>
    </th>
    <?php
    return ob_get_clean();
  }

  /**
   * No items.
   *
   * @param $title
   *
   * @return string
   */
  public static function no_items($title) {
    $title = ($title != '') ? strtolower($title) : 'items';
    ob_start();
    ?><tr class="no-items">
    <td class="colspanchange" colspan="0"><?php echo sprintf(__('No %s found.', WD_SEO_PREFIX), $title); ?></td>
    </tr><?php
    return ob_get_clean();
  }

  /**
   * Pagination.
   *
   * @param      $total
   * @param bool $search
   * @param bool $filter
   *
   * @return string
   */
  public static function pagination($total, $search = FALSE, $filter = FALSE) {
    $paged = WD_SEO_Library::get('paged', 1);
    $args = array(
      'base' => add_query_arg( 'paged', '%#%' ),
      'format' => '',
      'show_all' => FALSE,
      'end_size' => 1,
      'mid_size' => 1,
      'prev_next' => TRUE,
      'prev_text' => '&laquo;',
      'next_text' => '&raquo;',
      'total' => ceil($total / self::$total_in_page),
      'current' => $paged,
    );
    $page_links = paginate_links( $args );

    ob_start();
    ?>
    <div class="tablenav">
      <?php
      if ( $search ) {
        echo self::search();
      }
      if ( $filter ) {
        echo self::filter($filter);
      }
      ?>
      <div class="tablenav-pages">
        <span class="displaying-num"><?php printf( _n( '%s item', '%s items', $total, WD_SEO_PREFIX ), $total ); ?></span><?php
        if ( $page_links && self::$total_in_page < $total ) {
          echo $page_links;
        }
        ?></div>
    </div>
    <?php

    return ob_get_clean();
  }

  /**
   * Filter.
   *
   * @return string
   */
  public static function search() {
    $search = WD_SEO_Library::get('s', '');
    ob_start();
    ?>
    <p class="search-box">
      <input id="post-search-input" name="s" value="<?php echo $search; ?>" type="search" />
      <input class="button" value="<?php _e('Search', WD_SEO_PREFIX); ?>" type="button" onclick="search()" />
    </p>
    <?php

    return ob_get_clean();
  }

  /**
   * Search.
   *
   * @return string
   */
  public static function filter($filters) {
    ob_start();
    $is_active = WDSeo()->is_active();
    ?>
    <div class="alignleft actions">
      <?php
      foreach ($filters as $filter => $filter_arr) {
        ?>
        <select name="<?php echo $filter; ?>" onchange="filter(this)">
          <?php
          $filter_value = WD_SEO_Library::get($filter, '');
          foreach ($filter_arr as $key => $value) {
            ?>
            <option value="<?php echo $key; ?>"<?php if (!$is_active) echo ' disabled="disabled" title="' . __('This functionality is disabled in free version.', WD_SEO_PREFIX) . '"'; ?>
              <?php selected($filter_value, $key); ?>><?php echo $value; ?></option>
            <?php
          }
          ?>
        </select>
        <?php
      }
      ?>
    </div>
    <?php

    return ob_get_clean();
  }
}
