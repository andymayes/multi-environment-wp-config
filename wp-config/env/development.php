<?php
/**
 * Development environment config settings
 *
 * Enter any WordPress config settings that are specific to this environment
 * in this file.
 *
 */

/** MySQL hostname */
define( 'DB_HOST', WP_ENV_DB_HOST );

/** The name of the database for WordPress */
define( 'DB_NAME', WP_ENV_DB_NAME );

/** MySQL database username */
define( 'DB_USER', WP_ENV_DB_USER );

/** MySQL database password */
define( 'DB_PASSWORD', WP_ENV_DB_PASSWORD );

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
define( 'WP_DEBUG', true );
