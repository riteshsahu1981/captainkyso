<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'captainkyso_new');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         '_sY5K-Wr<s3AQuWKFD .?|eQME:b2a7b>*Q4FAntOO@&^&-c^skp`j,jfE*? bx@');
define('SECURE_AUTH_KEY',  'sj[a4LI~z0lgXKSszhV VWyGtA6N&gECXr&,6w`?U>Gh]$)o:U*+v,`+BA=;a7qm');
define('LOGGED_IN_KEY',    'mHXp0.gs)/2%)v}FjvhWR]AcAq[JI8l5Ml*>u1-*zPJnq0_IU^3xd1j)KF1!y}vM');
define('NONCE_KEY',        'jL4=>R`@;5@r,107h8`HI2X,cg6-n3MKyYi;eb`CbWGc&U5z};^G-7o9_h)YK RR');
define('AUTH_SALT',        ':b~9>,o%_IucOG/GGQf;aSF^p!7n5Yb6oDFVFb~Ox){l56/X3UornHi^r`kp!y[[');
define('SECURE_AUTH_SALT', '4a&KM-mlM[m=qhnu<l;| NK@H2)&p1FuG{!/i+cMDe8(cjw)+%~K^P?9{:<H)n-a');
define('LOGGED_IN_SALT',   'r@%pwstLth!8uw]k02,</w#0[_Y59A&)-[lA2kZEWJQyH7B}0>s}]aqUA[?$/XYw');
define('NONCE_SALT',       '?>p7+MzC^*_.27@-l%@s#!=z)`4yRn!H?&(07yJBfrC9<^5;xrgF}AX5E=a&!XX>');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
