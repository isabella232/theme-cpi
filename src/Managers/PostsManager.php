<?php
/**
 * Bootstraps settings and configurations for post types and taxonomies.
 */
namespace CPI\Managers;

class PostsManager
{

    /**
     * Runs initialization tasks.
     *
     * @return void
     */
    public function run()
    {
    }


    /**
     * Register post types in WordPress
     *
     * @return void
     */
    // public function registerPostTypes()
    // {

    // Author post type
    // $authorLabels = ['name' => __('Authors'),
    //                  'singular_name' => __('Author'),
    //                  'add_new_item' => __('Add New Author')];

    // register_post_type(
    //     'author',
    //     ['labels' => $authorLabels,
    //     'public' => true,
    //     'has_archive' => true,
    //     'menu_position' => 5,
    //     'rewrite' => ['slug' => 'authors'],
    //     'supports' => ['editor', 'excerpt', 'title', 'thumbnail'],
    //     'taxonomies' => []]
    // );

    // Press Release post type.
    // $pressReleaseLabels = ['name' => __('Press Release'),
    //                        'singular_name' => __('Press Release'),
    //                        'add_new_item' => __('Add New Press Release')];

    // register_post_type(
    //     'pressrelease',
    //     ['labels' => $pressReleaseLabels,
    //     'public' => true,
    //     'has_archive' => true,
    //     'menu_position' => 20,
    //     'rewrite' => ['slug' => 'press-releases'],
    //     'supports' => ['editor', 'excerpt', 'title', 'thumbnail'],
    //     'taxonomies' => []]
    // );
    // }
}
