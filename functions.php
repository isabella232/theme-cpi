<?php
/**
 * WP Theme constants and setup functions
 *
 * @package ThemeScaffold
 */

require_once 'vendor/autoload.php';

/**
 * Use Dotenv to set required environment variables and load .env file in root
 */
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

/**
 * Set up our global environment constant and load its config first
 * Default: production
 */
define('WP_ENV', getenv('WP_ENV') ?: 'production');
$timber = new Timber\Timber();
Timber::$dirname = array('templates');
// Cache twig in staging and production.
if (WP_ENV != 'development') {
    Timber::$cache = true;
}

use CPI\Managers\GutenbergManager;
use CPI\Managers\PostsManager;
use CPI\Managers\ThemeManager;
use CPI\Managers\TaxonomiesManager;
use CPI\Managers\WordPressManager;
use CPI\Services\Redirects;

define('CPI_THEME_URL', get_stylesheet_directory_uri());
define('CPI_THEME_PATH', dirname(__FILE__) . '/');
define('CPI_DOMAIN', get_site_url());
define('CPI_SITE_NAME', get_bloginfo('name'));
define('CPI_THEME_VERSION', wp_get_theme()->get('Version'));
define('ALGOLIA_INDEX_NAME_PREFIX', determineAlgoliaPrefix());

/**
 * Use a different Algolia index depending on if we're production, test,
 * dev, or local -- based on URL.
 * @return string prefix
 */
function determineAlgoliaPrefix()
{
    if (strpos($_SERVER['HTTP_HOST'], 'publicintegrity.org') !== false) {
        return "live_wp_";
    } elseif (strpos($_SERVER['HTTP_HOST'], 'live-public-integrity.pantheonsite.io') !== false) {
        return "live_wp_";
    } elseif ($_SERVER['HTTP_HOST'] == 'test-public-integrity.pantheonsite.io') {
        return "test_wp_";
    } elseif ($_SERVER['HTTP_HOST'] == 'dev-public-integrity.pantheonsite.io') {
        return "test_wp_";
    } else {
        return "test_wp_";
    }
}

// Set up ACF options page
if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title'  => 'Site Settings',
        'menu_title'  => 'Site Settings',
        'menu_slug'   => 'site-settings'
      ));
}

// Allow SVG uploads
function cc_mime_types($mimes)
{
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

add_action(
    'after_setup_theme',
    function () {
        $managers = [
            new GutenbergManager(),
            new PostsManager(),
            new TaxonomiesManager(),
            new WordPressManager()
    ];
        $themeManager = new ThemeManager($managers);
        $themeManager->run();
        new Redirects();
    }
);


add_action('pre_get_posts', 'archive_modify_query_limit_posts');

function archive_modify_query_limit_posts($query)
{
    // Check if on frontend and main query is modified
    if (! is_admin() && $query->is_main_query() && $query->is_archive()) {
        $query->set('posts_per_page', 8);
    }
}

function deactivate_plugin_conditional()
{
    if (is_plugin_active('search-by-algolia-instant-relevant-results/algolia.php') && (WP_ENV != 'development')) {
        deactivate_plugins('search-by-algolia-instant-relevant-results/algolia.php');
    }
}
add_action('admin_init', 'deactivate_plugin_conditional');
