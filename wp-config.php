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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'tutorstok-new' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'Admin@123#@!' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',          '>0*<NWBK]`XLi1B> QY^YL:V2-R0b_MmY-tjVLdN9hD-ML]JmXrIs#2lRLj5!UH_' );
define( 'SECURE_AUTH_KEY',   'FdcX0_E~Okz FiOqh@W4[|J`4j~]n[oGJmPD;$pBHf8P+Z&XsIQ$nU=d#D WbZ(r' );
define( 'LOGGED_IN_KEY',     '=<6+y@,PLG0FaDSfoCQ!/&B3^]joC(`OX#j>_n2?+~@+zfbUNy5Hk2d^`kkU2,|m' );
define( 'NONCE_KEY',         'a$-yDL]=(*/zg|*ufwjZ}fFc}QAb(P{4*xOA;k~Dr[GRUYMD?>1g1M#OLH|BE-~l' );
define( 'AUTH_SALT',         '}gm{@06tpCd0wpF7{VF!-<x=^WZOSX?pwW*Z`4dC`0s<(%s>=Jrx_Y2EHJ1X<H1e' );
define( 'SECURE_AUTH_SALT',  'Vu?>^=gXn,kK)N&DqcaBl=ML<iEeityUj@cLW#-5|$x3YhB-68/mz9tY#NUD6g{F' );
define( 'LOGGED_IN_SALT',    'O2,Hz7^J@On=%6rGoEm}^>N$Xkq.,88N(^6P`QSP,DtWk_X?X;:83Tu>RO8R[FG1' );
define( 'NONCE_SALT',        ';leRG.Pp<xQNvG0h/8S30O`8C mo0&X*Z~x89KVX$1)IV~@N,L<}Y=J:H{I`IW;{' );
define( 'WP_CACHE_KEY_SALT', '32@a>J+os>_QM@1gW[^/@/a&PFFPqq,=D27R0LUi:+B3<sD[kASup`:w%C<#{<=1' );
define( 'FS_METHOD', 'direct' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
