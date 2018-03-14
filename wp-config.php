<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'sduser');

/** MySQL database password */
define('DB_PASSWORD', 'rose0327');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'X{#O4v9t%R,FRwGBRp|RZUK~Y[++6jtW?Ttr>7[BR?LTJu~|/ 0IdB9YYaOabI|L');
define('SECURE_AUTH_KEY',  ',.w .@sbpR-:inLJJ/muMf7ppM&TZ4IZ$R9IlQW)>:|{A,K}L3kT5Jken/Zwb_:t');
define('LOGGED_IN_KEY',    '|oS]L=!uWd|-`uZVb-zSn9>M@s[;VvU2U}=B82fU;hY=bhMz78)(k`8y{R6!abX5');
define('NONCE_KEY',        '25<.8x+EXoO;^<rTe?$>YNu/$nxy|luJsM&OTXLWg7[CxA`*oz 93[!eH;GRd_Qv');
define('AUTH_SALT',        'Cvh=r*F@6B_-<sNbAhJ9u+($+|[rEbSZNG%qXAk2(xJoiMq%q {<Q]zJx_YUqN;j');
define('SECURE_AUTH_SALT', ':ztV{B0ca.SD/k_hdF8+gz:56Ik`%>Nq|G+&2Bbt=o/qncqe8?p+o5rdL2/^C`h,');
define('LOGGED_IN_SALT',   'q6l~qkM$@Pe)zWU!U_SU~EXEGb.=<r-eUQo27Z(|(!.+V[C~UlKZ3:_7:P[TzKIK');
define('NONCE_SALT',       'oDvG-3gduLdo4x{E$V[uOOxt !{!ge0nm[Gi MJ41&nud,=l_=X+h&o>F3@.Vwst');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
