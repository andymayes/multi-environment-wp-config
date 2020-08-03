<?php
/**
 * Configuration setup
 *
 * Environment-specific overrides go in their respective
 * config/env/{{WP_ENV}}.php file.
 *
 */

// Define env if set via environment variable
if (getenv('WP_ENV') !== false):
    define('WP_ENV', preg_replace('/[^a-z]/', '', getenv('WP_ENV')));
endif;

// Define ENV from hostname if not set via environment variable
if (!defined('WP_ENV')):
    if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) && !empty($_SERVER['HTTP_X_FORWARDED_HOST'])):
        $hostname = strtolower(filter_var($_SERVER['HTTP_X_FORWARDED_HOST'], FILTER_SANITIZE_STRING));
    else:
        $hostname = strtolower(filter_var($_SERVER['HTTP_HOST'], FILTER_SANITIZE_STRING));
    endif;
endif;

// Load environments
require __DIR__ . '/config-env.php';

// Load credentials
if (file_exists( __DIR__ . '/credentials.php')) :
    require __DIR__ . '/credentials.php';
endif;

// Merge environment arrays
$envs = array_merge_recursive($env, $env_cred);

// Set env constants from environemnt variables
if (defined('WP_ENV')):
    // Set db credentials within environment variables
    define('WP_ENV_DB_HOST', getenv('WP_ENV_DB_HOST'));
    define('WP_ENV_DB_NAME', getenv('WP_ENV_DB_NAME'));
    define('WP_ENV_DB_USER', getenv('WP_ENV_DB_USER'));
    define('WP_ENV_DB_PASSWORD', getenv('WP_ENV_DB_PASSWORD'));

    if (isset($envs[WP_ENV])):
        define('WP_ENV_DOMAIN', $envs[WP_ENV]['domain']);
        define('WP_ENV_WP_PATH', trim($envs[WP_ENV]['wppath'], '/'));
        define('WP_ENV_SITE_PATH', trim($envs[WP_ENV]['sitepath'], '/'));
        define('WP_ENV_SSL', (bool) $envs[WP_ENV]['ssl']);
    endif;
else:

    // Detect environment from hostname
    foreach ($envs as $environment => $env_vars):
        $domain = $env_vars['domain'];

        if (!is_array($domain)):
            $domain = [$domain];
        endif;
        foreach ($domain as $domain_name):
            if ($hostname === $domain_name):
                if (!defined('WP_ENV')):
                    define('WP_ENV', preg_replace('/[^a-z]/', '', $environment));
                endif;
                define('WP_ENV_DOMAIN', $domain_name);
                if (isset($env_vars['ssl'])):
                    define('WP_ENV_SSL', (bool)$env_vars['ssl']);
                endif;
                if (isset($env_vars['wppath'])):
                    define('WP_ENV_WP_PATH', trim($env_vars['wppath'], '/'));
                endif;
                if (isset($env_vars['sitepath'])):
                    define('WP_ENV_SITE_PATH', trim($env_vars['sitepath'], '/'));
                endif;
                define('WP_ENV_DB_HOST', $env_vars['host']);
                define('WP_ENV_DB_NAME', $env_vars['name']);
                define('WP_ENV_DB_USER', $env_vars['user']);
                define('WP_ENV_DB_PASSWORD', $env_vars['password']);
                break;
            endif;
        endforeach;
    endforeach;
endif;

if (!defined('WP_ENV_SSL')):
    define('WP_ENV_SSL', false);
endif;
if (WP_ENV_SSL && (!defined('FORCE_SSL_ADMIN'))):
    define('FORCE_SSL_ADMIN', true);
endif;

/**
 * Define WordPress Site URLs
 */
$protocol  = (WP_ENV_SSL) ? 'https://' : 'http://';
$wppath    = (defined('WP_ENV_WP_PATH')) ? '/' . trim(WP_ENV_WP_PATH, '/') : '';
$sitepath  = (defined('WP_ENV_SITE_PATH')) ? '/' . trim(WP_ENV_SITE_PATH, '/') : '';

if (!defined('WP_SITEURL')):
    define('WP_SITEURL', $protocol . trim(WP_ENV_DOMAIN, '/') . $wppath);
endif;
if (!defined('WP_HOME')):
    define('WP_HOME', $protocol . trim(WP_ENV_DOMAIN, '/') . $sitepath);
endif;

/**
 * Load config
 */
$env_config = __DIR__ . '/env';

// 1st - Load default config
require $env_config . '/default.php';

// 2nd - Load config file for current environment
require $env_config . '/' . WP_ENV . '.php';
