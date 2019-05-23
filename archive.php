<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 */

use CPI\Models\CPIPost;
use CPI\Models\CPITopic;

global $paged;
if (!isset($paged) || !$paged) {
    $paged = 1;
}

$context = Timber::get_context();
$templates = ['pages/archive.twig', 'pages/index.twig'];
$query_args = null;
$per_page = 8;

$context['title'] = 'Archive';
$context['description'] = false;

if (is_day()) {
    $context['title'] = 'Archive: ' . get_the_date('D M Y');
} else if (is_month()) {
    $context['title'] = 'Archive: ' . get_the_date('M Y');
} else if (is_year()) {
    $context['title'] = 'Archive: ' . get_the_date('Y');
} else if (is_tag()) {
    $context['title'] = single_tag_title('', false);
    $query_args = array(
        'posts_per_page' => $per_page,
        'post_status' => 'publish',
        'tag' => get_query_var('tag'),
        'paged' => $paged,
    );
} else if (is_category()) {
    $term = new CPITopic();
    $query_args = array(
        'posts_per_page' => $per_page,
        'post_status' => 'publish',
        'cat' => $term->id,
        'paged' => $paged,
    );

    if ($term->parent) {
        $context['parent_term'] = new CPITopic($term->parent);
    }

    // Set array of IDs of Featured Posts for query arguments
    $featured_posts_ids = $term->featuredPostsIDs;

        if (!empty($featured_posts_ids)) {
        	// Argument for Posts query that excludes Featured Posts
        	$query_args['post__not_in'] = $featured_posts_ids;
    }

    $context['term'] = $term;

    array_unshift($templates, 'pages/archive-' . get_query_var('cat') . '.twig');
} else if (is_post_type_archive()) {
    $context['title'] = post_type_archive_title('', false);
    array_unshift($templates, 'pages/archive-' . get_post_type() . '.twig');
}

// Make Post queries based on whether Term has Featured Posts
if (!empty($featured_posts_ids)) {
    $context['featured_posts'] = $term->featuredPosts();
}

$context['posts'] = $query_args ? new Timber\PostQuery($query_args, CPIPost::class) : new Timber\PostQuery(null, CPIPost::class);

// Render view
Timber::render( $templates, $context );
