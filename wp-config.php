<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'admin' );

/** Database password */
define( 'DB_PASSWORD', 'admin' );

/** Database hostname */
define( 'DB_HOST', 'mysql' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'C+oA|-UpFp$CMN;%!e);XY,Bng[W.  -C1&kYFP+Vmq.;afV~`@XT,(7P1;;5a`[');
define('SECURE_AUTH_KEY',  'kS=rs.:E{+Jo1=+74%qC%9VP.4b/9%]=G$j%gl|EMsJL>)mXj/N5sANJACQz[(Jo');
define('LOGGED_IN_KEY',    'Nwl-7&Oi-reUBh`{sL7lST4d5;BIF:N1Gg>a3BtgLWJZ+#(uQv/S):Ri92NI=%vp');
define('NONCE_KEY',        'w|N`h[cj15qLF+]u+hdi@^+YLrR&w2N1K#Lv*Z&j?a~+eyvH$g_{:8sa$2a~c]B ');
define('AUTH_SALT',        '6N5U|lLR`> GYOg!=z(x/nWL=@l=yT>M^WqI4VB(N@X1|R70an,ucpz.$ s)1#}=');
define('SECURE_AUTH_SALT', '@o4@aya3Ni(,jCt-}7gmG<5x%pg~*E&Vp9K*s!hZ|--oaLvBqCv(pJ79|dvx~.z`');
define('LOGGED_IN_SALT',   '.io$jb1P&#K5yz89: xQ#g5a|RJpJ+=MPJYv`%-4q}jpT2W8auf;nDS(.UIvEBB,');
define('NONCE_SALT',       'Vl`Cp;6_+TJ~~u|BAXq,Ebt+<7s2|Uu#1< m&oSr+JGDZz>MFt;q^1rl.obZrT&r');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
