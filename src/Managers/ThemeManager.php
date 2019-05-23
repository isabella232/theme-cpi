<?php
/**
 * Bootstraps WordPress theme related functions, most importantly enqueuing javascript and styles.
 */
namespace CPI\Managers;

use Timber;
use Timber\Menu as TimberMenu;

use CPI\Models;
use CPI\Models\CPITopic;

class ThemeManager
{
    private $managers = [];

    /**
     * Constructor
     *
     * @param array $managers Array of managers
     */
    public function __construct(array $managers)
    {
        $this->managers = $managers;
        add_filter('timber/context', array($this, 'addIsHomeToContext'));
        add_filter('timber/context', array($this, 'addACFOptionsToContext'));
        add_filter('timber/context', array($this, 'addHostNametoContext'));
        add_filter('timber/context', array($this, 'addIsGoogleTestToContext'));
        add_filter('timber/context', array($this, 'addThemeVersion'));
        add_filter('timber/context', array($this, 'addMenusToContext'));

        add_action('admin_enqueue_scripts', array($this,'enqueueAdminScripts'));
        add_action('wp_dashboard_setup', array($this, 'addDocumentationWidget'));
        add_action('admin_menu', array($this, 'addDocumentationMenuItem'));
        add_action('admin_init', array($this,'register_menus'));
        add_action('admin_init', array($this,'redirectToDocs'), 1);
        add_action('admin_init', array( $this, 'sync_fields_with_json' ));

        add_action('init', array($this, 'registerOptions'), 1, 3);

        add_filter('acf/fields/relationship/query', array($this,'acf_post_relationship_query'), 10, 3);
        add_filter('acf/fields/post_object/query', array($this,'acf_post_object_query'), 10, 3);
        add_filter('gutenberg_can_edit_post_type', array($this,'disableGutenbergOnSpecificPosts'), 10, 2);

        add_filter('algolia_template_locations', array($this, 'algolia_template_locations'), 10, 2);
    }

    /**
     * Runs initialization tasks.
     *
     * @return void
     */
    public function run()
    {
        if (count($this->managers) > 0) {
            foreach ($this->managers as $manager) {
                $manager->run();
            }
        }


        add_action('wp_enqueue_scripts', [$this, 'enqueue'], 999);
        add_theme_support('post-thumbnails');
        add_theme_support('menus');
        add_theme_support('align-wide');

    }

    /**
     * Enqueue javascript using WordPress
     *
     * @return void
     */
    public function enqueue()
    {
        // don't use WordPress jquery in production. (admin bar and wordpress debug bar needs it in development)
        if (WP_ENV != 'development') {
            wp_deregister_script('jquery');
        }

        // Remove default Gutenberg CSS
        wp_deregister_style('wp-block-library');

        // enqueue vendor script output from webpack
        wp_enqueue_script('manifest', CPI_THEME_URL . '/dist/manifest.js', array(), CPI_THEME_VERSION, true);
        wp_enqueue_script('vendor', CPI_THEME_URL . '/dist/vendor.js', array(), CPI_THEME_VERSION, true);

        // enqueue custom js file, with cache busting
        wp_enqueue_script('script.js', CPI_THEME_URL . '/dist/app.js', array(), CPI_THEME_VERSION, true);

        // this is needed for the Algolia search template, but we include our own copy
        wp_deregister_script('wp-util');
    }

    /**
     * Enqueue JS and CSS for WP admin panel
     *
     * @return void
     */
    public function enqueueAdminScripts()
    {
        wp_enqueue_style('admin-styles', CPI_THEME_URL . '/dist/admin.css');

        wp_enqueue_script('manifest', CPI_THEME_URL . '/dist/manifest.js');
        wp_enqueue_script('vendor', CPI_THEME_URL . '/dist/vendor.js');
        wp_enqueue_script('admin.js', CPI_THEME_URL . '/dist/admin.js');
    }


    /**
     * Adds ability to access array of ACF options fields in a twig field
     *
     * @param array $context Timber context
     *
     * @return array
     */
    public function addACFOptionsToContext($context)
    {
        $context["options"] = get_fields('option');
        return $context;
    }


    /**
     * Adds ability to check if we are on the homepage in a twig file
     *
     * @param array $context Timber context
     *
     * @return array
     */
    public function addIsHomeToContext($context)
    {
        $context['is_home'] = is_home();

        return $context;
    }

    /**
     * Get host name and add to context
     *
     * @param array $context Timber context
     *
     * @return array
     */
    public function addHostNametoContext($context)
    {
        $context['hostname'] = $this->get_host_name();

        return $context;
    }

    /**
     * Checks if the Google Optimize Test checkbox is enabled. If so,
     * the page hiding code from Google will be enabled in the head.
     *
     * @param array $context Timber context
     *
     * @return array
     */
    public function addIsGoogleTestToContext($context)
    {
        $context['isGoogleOptimizeTest'] = (get_field('google_optimize_test', get_the_ID()));

        return $context;
    }

    /**
     * Get host name
     *
     * @return string host name
     */
    private function get_host_name()
    {
        $hostName = $_SERVER['HTTP_HOST'];
        if (empty($hostName)) {
            $hostName = $_SERVER['SERVER_NAME'];
        }
        if (empty($hostName)) {
            $hostName = $_SERVER['VIRTUAL_HOST'];
        }

        return $hostName;
    }

    /**
     * Register nav menus
     *
      @return void
     */
    public function register_menus()
    {
        register_nav_menus(
            array(
                'nav_topics_menu' => 'Navigation Topics Menu',
                'nav_pages_menu' => 'Navigation Pages Menu',
                'masthead_links_menu' => 'Masthead Link',
                )
        );
    }


    /**
     * Registers and adds menus to context
     *
     * @param array $context Timber context
     *
     * @return array
     */
    public function addMenusToContext($context)
    {
        $navTopicsMenu = new Timber\Menu('nav_topics_menu');
        $navItems = $navTopicsMenu->items;

        if (is_array($navItems) && !empty($navItems)) {
            $navCPITopics = array_map(
                function ($item) {
                    if ($item->object === "category") {
                        $term = Timber::get_term((int)$item->object_id, '', CPITopic::class);
                        $term->menuChildren = $item->children;
                        return $term;
                    } else {
                        return $item;
                    }
                },
                $navItems
            );
        }

        if (!empty($navCPITopics)) {
            $context['nav_topics_menu_items'] = $navCPITopics;
        } else {
            // temp
            $context['nav_topics_menu'] = $navTopicsMenu->items;
        }

        $context['nav_pages_menu'] = new Timber\Menu('nav_pages_menu');
        $context['masthead_links_menu'] = new Timber\Menu('masthead_links_menu');

        return $context;
    }


    /**
     * Adds timestamp of last modified time of CSS file to context in order
     * to allow for automatic cache busting.
     *
     * @param array $context Timber context
     *
     * @return array
     */
    public function cssLastModified($context)
    {
        $context['css_last_modified'] = filemtime(get_stylesheet_directory() . '/dist/app.css');

        return $context;
    }

    /**
     * Add theme's version to Timber context
     *
     * @param array $context Timber context
     *
     * @return void
     */
    public function addThemeVersion($context)
    {
        $context['theme_version'] = wp_get_theme()->get('Version');

        return $context;
    }

    /**
     * Adds a widget to the dashboard with a link to editor docs
     *
     * @return void
     */
    public function addDocumentationWidget()
    {
        wp_add_dashboard_widget(
            'custom_dashboard_widget',
            'Editor Documentation',
            function () {
                echo "<p><a href='/wp-content/themes/cpi/documentation/index.html' target='_blank' rel='noopener noreferrer'>View</a> the editor documentation</p>";
            }
        );
    }

    /**
     * Adds a menu item to WP admin that links to editor docs
     *
     * @return void
     */
    public function addDocumentationMenuItem()
    {
        add_menu_page('Editor Docs', 'Editor Docs', 'manage_options', 'link-to-docs', array($this,'redirectToDocs'), 'dashicons-admin-links', 100);
    }

    /**
     * To have an external link to the docs we need this weird function
     *
     * @return void
     */
    public function redirectToDocs()
    {
        $menu_redirect = isset($_GET['page']) ? $_GET['page'] : false;
        if ($menu_redirect == 'link-to-docs') {
            header('Location: https://' . $_SERVER['HTTP_HOST'] . '/wp-content/themes/cpi/documentation');
            exit();
        }
    }

    /**
     * Add ACF options page to WordPress
     *
     * @return void
     */
    public function registerOptions()
    {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page(
                array(
                'page_title'  => 'Site Settings',
                'menu_title'  => 'Site Settings',
                'menu_slug'   => 'site-settings'
                )
            );
        }
    }

    /**
     * Modify ACF relationship field to show most recent posts instead of alpha
     *
     * @param array  $args    Args
     * @param string $field   Field
     * @param int    $post_id Post ID
     *
     * @return void
     */
    public function acf_post_relationship_query($args, $field, $post_id)
    {
        // order returned query collection by date, starting with most recent
        $url = wp_get_referer();
        $parts = parse_url($url);
        parse_str($parts['query'], $query);

        if (key_exists('taxonomy', $query) && key_exists('tag_ID', $query)) {
            $args['tax_query'] = array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'category',
                    'field' => 'term_id',
                    'terms' => intval($query['tag_ID'])
                ),
                array(
                    'taxonomy' => 'author',
                    'field' => 'term_id',
                    'terms' => intval($query['tag_ID'])
                )
            );
        }

        $args['order'] = 'DESC';
        $args['orderby'] = 'relevance';

        return $args;
    }

    /**
     * Modify ACF post_object select fields Post query
     *
     * @param array $args    Args
     * @param array $field   Field Array
     * @param int   $post_id Post ID
     *
     * @return void
     */
    public function acf_post_object_query($args, $field, $post_id)
    {
        $url = wp_get_referer();
        $parts = parse_url($url);
        parse_str($parts['query'], $query);

        if (key_exists('taxonomy', $query) && key_exists('tag_ID', $query)) {
            $args['tax_query'] = array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'category',
                    'field' => 'term_id',
                    'terms' => intval($query['tag_ID'])
                ),
                array(
                    'taxonomy' => 'author',
                    'field' => 'term_id',
                    'terms' => intval($query['tag_ID'])
                )
            );
        }

        $args['order'] = 'DESC';
        $args['orderby'] = 'relevance';

        return $args;
    }

    /**
     * Templates and Page IDs without editor
     *
     * @param int $id Post ID
     *
     * @return bool If post's id is in excluded array
     */
    public function postsToExcludeGutenberg($id = false)
    {
        $excluded_templates = array(
            'page-about.php',
            'page-home.php',
            'page-support.php'
        );
        $excluded_ids = array(
            // get_option( 'page_on_front' )
        );
        if (empty($id)) {
            return false;
        }
        $id = intval($id);
        $template = get_page_template_slug($id);

        return in_array($id, $excluded_ids) || in_array($template, $excluded_templates);
    }

    /**
     * Disable Gutenberg by template
     *
     * @param bool   $can_edit  Whether Gutenberg is used
     * @param string $post_type Custom post type
     *
     * @return bool Whether to enable Gutenberg
     */
    public function disableGutenbergOnSpecificPosts($can_edit, $post_type)
    {
        if (! (is_admin() && !empty($_GET['post']))) {
            return $can_edit;
        }
        if ($this->postsToExcludeGutenberg($_GET['post'])) {
            $can_edit = false;
        }
        return $can_edit;
    }

    /**
     * Automatically sync any JSON field configuration.
     *
     * @return null
     */
    public function sync_fields_with_json()
    {
        if (defined('DOING_AJAX') || defined('DOING_CRON')) {
            return;
        }
        if (! function_exists('acf_get_field_groups')) {
            return;
        }
        $groups = acf_get_field_groups();
        if (empty($groups)) {
            return;
        }
        $sync = array();
        foreach ($groups as $group) {
            $local    = acf_maybe_get($group, 'local', false);
            $modified = acf_maybe_get($group, 'modified', 0);
            $private  = acf_maybe_get($group, 'private', false);
            if ($local !== 'json' || $private) {
                // ignore DB / PHP / private field groups
                continue;
            }
            if (! $group['ID']) {
                $sync[ $group['key'] ] = $group;
            } elseif ($modified && $modified > get_post_modified_time('U', true, $group['ID'], true)) {
                $sync[ $group['key'] ] = $group;
            }
        }
        if (empty($sync)) {
            return;
        }
        foreach ($sync as $key => $v) {
            if (acf_have_local_fields($key)) {
                $sync[ $key ]['fields'] = acf_get_local_fields($key);
            }
            acf_import_field_group($sync[ $key ]);
        }
    }

    /**
     * Tell Algolia to use our own template instead of their default
     *
     * @param array  $locations Locations
     * @param string $file      File
     *
     * @return array
     */
    public function algolia_template_locations(array $locations, $file)
    {
        if ($file === 'instantsearch.php') {
            $locations[] = 'page-search.php';
        }

        return $locations;
    }
}
