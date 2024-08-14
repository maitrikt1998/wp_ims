<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */
define('FS_METHOD', 'direct');
// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_project' );

/** Database username */
define( 'DB_USER', 'sammy' );

/** Database password */
define( 'DB_PASSWORD', 'password' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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

define( 'AUTH_KEY',         'e,3FA#q:3L9%Zbu^4*xpQkcFj_f-Lp1kv0ZB;rxa~[D^{kbpE|vTL|UTH{Siw??j' );
define( 'SECURE_AUTH_KEY',  'tT=romg{rH5&?*hwiV8$[i^&4smN8}FYY]Gg-4z/~!5l*O!Upz#6P7q4.}=%9ytF' );
define( 'LOGGED_IN_KEY',    'M9P1H&GZ#=q~F{jAXzNY[UX2 *<_ %xZAF9t>T=NU2UH%O&[vRWl`cPPB22Hw~Qx' );
define( 'NONCE_KEY',        'a^Llc{Bz,/-!+;-oq/c@6h~HAjTJolWQ1C_@guD*%K;j>exDdF}5;f8E%ETMSgLa' );
define( 'AUTH_SALT',        ';d-LWBh,)?~411*`8wTiKbH43sr_Ki3ne|;8]w/LO[6bkCG|.U:P:gh-uM10a7X[' );
define( 'SECURE_AUTH_SALT', 'xqrFRK)]uKhPH$E+z2g<(XVcGm9Z#|4,YkV<]dab/AWPgQ_rDxY$-WwOb*+AAs]2' );
define( 'LOGGED_IN_SALT',   ']+m:)6=w*uiv3K2e=Ov?L^Q#wiy*LZ*{Tw{:RdeHV>K-!3I oowtJ#Mnl?kK:}e;' );
define( 'NONCE_SALT',       ',<+,1Cy:&]0:+-VJbgV)Y1LA-XM13mhD4|I_><wY(:Y8/-l>>s(~rAj*>W.Y;%NF' );

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
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