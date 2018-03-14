<?php
defined('ABSPATH') || die('Access Denied');

/**
 * Admin controller class.
 */
class WDSeosettingsController extends WDSeoAdminController {
  /**
   * Display.
   */
  public function display() {
    add_filter( 'editable_roles', array( $this, 'filter_user_roles' ) );
    parent::display();
  }

  /**
   * Remove roles that do not have `edit_posts` capability from list.
   *
   * @param $all_roles
   * @return mixed
   *
   */
  public function filter_user_roles( $all_roles ) {
    foreach ( $all_roles as $name => $role ) {
      if (!isset($role['capabilities']['edit_posts'])) {
        unset($all_roles[$name]);
      }
    }
    return $all_roles;
  }
}
