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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'theluxebagsdb' );

/** MySQL database username */
define( 'DB_USER', 'theluxebagsdbuser' );

/** MySQL database password */
define( 'DB_PASSWORD', 'bh78VbJltO0RAslywc' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'd2>[bR&:Da[FDB0h:C6A9*W49$;wq`[|8>r)pMpUmJSP?cc=1h%<GC,VfCMD_(sN' );
define( 'SECURE_AUTH_KEY',   'j[yV]U;.-mcDzFRxfQaCyg6Xmjeh0f3 PFTqI;<%d?pD>n@9aojYZ)G!!xpFK~/2' );
define( 'LOGGED_IN_KEY',     'I~%x?M`v=|$QG[zq*%fd0X[ulr MDEQ8(WMYthu_k/]+SRv1{|%hb8cl)})<&9RT' );
define( 'NONCE_KEY',         'N5<K;B#V56)=fBWQSGX)v9bN&9]|9eeu])i@-q9gFk[O2|;~kkZ3v0]SO,i8b+xL' );
define( 'AUTH_SALT',         '~j};]2npW3f1fY>I*Od)L}- dfX/hT3)[lq>[2?<SO@Z_C{Ih7nig-;:OO_Th&6p' );
define( 'SECURE_AUTH_SALT',  '*ZbfLCb[cbw]ZK|)/6E! }<l@XB!c=PZgyft>#t5ge$Pl2>)}$P*T w8Va-pe/vX' );
define( 'LOGGED_IN_SALT',    'RnzV5Ig?0?CEJoVW6OaceS)9)Kb{I<p[?^aE:*]pq #UBC{}aMFjaFq`*6w,$dPb' );
define( 'NONCE_SALT',        '/9/b~.*tG]tj3YEK-}o<YW,WVjhQ&A%>zn`.%:S]%^pp4fPm/P+XpkiXe}0Us,Wm' );
define( 'WP_CACHE_KEY_SALT', '}u&BR=eu`H^)9{8!/fko>&4IyfjLM*Am?+t4^QaK:j^6NbOBpm^9]WtuqEV#5pII' );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




define( 'FS_METHOD', 'direct' );
define( 'WP_MEMORY_LIMIT', '256M' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
