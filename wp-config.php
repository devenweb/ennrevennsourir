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
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

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
define( 'AUTH_KEY',          '~!F9-]v,3=j]gnMX4Xl o:V8cH.K9R86dUa71V0mo!:Cf[Glds@ -trwh#omve9V' );
define( 'SECURE_AUTH_KEY',   'UWUQ-zq}PpFuOB>Rvhc!|qb/bhFn,z7-0.YBIF#ttuKEsVTHM;]$m=F^y,,nP*&7' );
define( 'LOGGED_IN_KEY',     'Ya5V]Z9o?aLqAi*8^jwz68Ut <]tFj3cw24>9 RjZJ(u~-0YB6?gW4Iy{TA{p@Y_' );
define( 'NONCE_KEY',         ';P1f3o|NJF)^cR^WEut,[iH&**.v6}K:3c>yZOodeA-L)VhJ}TPeM77# 6 3[p$3' );
define( 'AUTH_SALT',         'I/w( $m|MRq;K|@D^m.v)<Y;dSc61lgI&H ,&gXI5/<Xsv82.vPRQ/t-%,UCARx4' );
define( 'SECURE_AUTH_SALT',  'jsHpLodQ=0G []8Ck--iu=o7&~jJhyx/mu&jDD#Kyr;ev&Yi|u/}0HK$}Lj1.ZcY' );
define( 'LOGGED_IN_SALT',    '?g1z(a?WrV?`$K@xmRSxwH$_4c0f;-&a}(-o4[C: Q$@JbVL]-_B,U`ykX!KhR4h' );
define( 'NONCE_SALT',        'B$Q(ymCW`<c}F[#]t)x%Of{en}$P4R:sL37ZSk~@bQkBSO=V8TCT; (/D1Vn3IS|' );
define( 'WP_CACHE_KEY_SALT', '!2V.i;byt1w`Jnz]ZIR.3(c;Mmdoxc*l|fh@L6)1Y.gFX<RVa_`+KQMT72-t)eA+' );
define( 'WP_CACHE', false );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */

// ============================================================================
// SECURITY HARDENING - Added 2026-01-29
// ============================================================================

// Disable file editing in WordPress admin (prevents backdoor creation)
define( 'DISALLOW_FILE_EDIT', true );

// Allow plugin/theme updates (but not file editing)
define( 'DISALLOW_FILE_MODS', false );

// Force SSL for admin area (enable when using HTTPS)
// define( 'FORCE_SSL_ADMIN', true );

// Auto-update WordPress core (minor versions only for security)
define( 'WP_AUTO_UPDATE_CORE', 'minor' );

// Limit post revisions to reduce database bloat
define( 'WP_POST_REVISIONS', 5 );

// Increase autosave interval to reduce server load (5 minutes)
define( 'AUTOSAVE_INTERVAL', 300 );

// Increase memory limit if needed (default is 40M)
// define( 'WP_MEMORY_LIMIT', '256M' );
// define( 'WP_MAX_MEMORY_LIMIT', '512M' );

// ============================================================================
// ENVIRONMENT-BASED DEBUG CONFIGURATION
// ============================================================================

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

// Define environment type FIRST before using it
define( 'WP_ENVIRONMENT_TYPE', 'local' );

// Environment-based debug settings
if ( WP_ENVIRONMENT_TYPE === 'production' ) {
	// Production: Disable all debugging
	define( 'WP_DEBUG', false );
	define( 'WP_DEBUG_LOG', false );
	define( 'WP_DEBUG_DISPLAY', false );
	@ini_set( 'display_errors', 0 );
} elseif ( WP_ENVIRONMENT_TYPE === 'staging' ) {
	// Staging: Log errors but don't display
	define( 'WP_DEBUG', true );
	define( 'WP_DEBUG_LOG', true );
	define( 'WP_DEBUG_DISPLAY', false );
	@ini_set( 'display_errors', 0 );
} else {
	// Local/Development: Full debugging
	define( 'WP_DEBUG', true );
	define( 'WP_DEBUG_LOG', true );
	define( 'WP_DEBUG_DISPLAY', false );
	@ini_set( 'display_errors', 0 );
}
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
