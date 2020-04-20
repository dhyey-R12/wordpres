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
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'U`[>Ur6Acq$*OhhPdbc1Fu%U4ko7OXTUHK[f?enCKqERyQVoxtwZ{[{rT$8XLcnO' );
define( 'SECURE_AUTH_KEY',  'O5cJC+#[v9/j)@{B5;!nSN-sM?AEL%}z0evF&>.OS@1v)xSPW272F7#;@,BZg,q5' );
define( 'LOGGED_IN_KEY',    '[z=eFUhb+:]>VNFq/QC{T0-k{xAe!O^Hh=%p%a#9/u 6awJ#HGe:7-2~dSKc{)=#' );
define( 'NONCE_KEY',        '6Y/H8Tip}*bTX8{U#r acv!Hwl$(pZvl#E&( V.Y7e=Y(FN<*7L9eI7h~I!bJpKf' );
define( 'AUTH_SALT',        'g+kKrzn<^}y]gU+txgX=;1[?$4wAU`Rsd(eXUAph>n=_ R]%^*r.gic?QLI%|[[7' );
define( 'SECURE_AUTH_SALT', '#y8IXZgn9Or%YtEhd>a((m^<ugW`[.7V:8E0gGN[N3OnpPw8S:/S1ulo@-y-9vr}' );
define( 'LOGGED_IN_SALT',   'q04%l8J?P4d#!8fJOQhg{u]R?kyaLX[M4)(nx]5F;Cz%;JnR!JB`Ep/8rc/{=WyW' );
define( 'NONCE_SALT',       'p(4 1b<cl{D<9(-t^=6z.c&Ht^qpHq$BpItqtS(2o>|B7]dt*Hp#R0>&fh2n{;H,' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
