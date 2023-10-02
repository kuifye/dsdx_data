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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'P78mrfVS' );

/** Database hostname */
define( 'DB_HOST', 'Localhost' );

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
define( 'AUTH_KEY',         '#?4k@Yrxcw.%;ot?artlp3bO7t!?9T$ )?>w  BI}.5a[I~.RFQb6b88PKW]d#(&' );
define( 'SECURE_AUTH_KEY',  'Oz&^wSO!q=@_Ik!5dwaSm5BHKJyO+d6SG7 #[Vd4QWF0rhJ_Ft%sMGO~Q]5wz9hW' );
define( 'LOGGED_IN_KEY',    '*?]#@:@nGL-0KxoH#?^X{6?d+W1^,.k8 $ANc}nXEfJ Dai{ u>1BZ+ZPE,2lTup' );
define( 'NONCE_KEY',        'uq^opsLW4dO35jKiQ#.vpu3M^YxB4dF|SDw ! ZkOi6S4uIaeJcJJ6>K`rZ&P3@0' );
define( 'AUTH_SALT',        '19W:v%Kj 14B |m`biF,k&/KfSUBRl>WdP`Gcnj%QtQTE,&!>saEG^h}<bO6/UEo' );
define( 'SECURE_AUTH_SALT', 'F3Y}h+]=VfnE:HfmUc`I:p1HVv0-3`cm^IWx<E`Cu0S9Rf0U# )C*tiO+8sBBCzz' );
define( 'LOGGED_IN_SALT',   'np&C/u7TU8Sm2fxySBYSy|[*u9c$.QHWDTv)FE?AGew+h#I%LJ`r`V^K@e;nnX>Z' );
define( 'NONCE_SALT',       'WRS3/pgk$;/M84&S-F%e*>(lX8/.jvmyPx7NcqxpBI3W;229z W@(>0.DQaw;1).' );

define("FS_METHOD", "direct");
define("FS_CHMOD_DIR", 0777);
define("FS_CHMOD_FILE", 0777);

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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
