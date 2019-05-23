<?php
/**
 * The template for displaying Author pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 */
use Timber\Post;

use CPI\Models\CPIPost;
use CPI\Models\CPIAuthor;

$context    = Timber::get_context();
$query_args = null;
$per_page = 8;

if ( is_tax('author') ) {
    $term = new CPIAuthor();
    $query_args = array(
        'posts_per_page'  => $per_page,
        'post_status'     => 'publish',
        'authors'         => $term->slug,
        'paged'           => $paged
    );

    // TODO: refactor in line with CPITopic changes
    if ($term->featured_articles) {
        // Set array of IDs of Featured Posts for query arguments
        $featured_posts_ids = [];
        // Check for array in case featuredPosts is single object
        if (is_array($term->featured_articles)) {
            foreach ($term->featured_articles as $post) {
                $featured_posts_ids[] = $post->ID;
            }
        } else {
            $featured_posts_ids[] = $term->featured_articles->ID;
        }

        if (!empty($featured_posts_ids)) {
            // Arguments for Featured Posts query
            $featured_query_args = array(
                'posts_per_page' => $per_page,
                'post_status' => 'publish',
                'cat' => $term->id,
                'paged' => $paged,
                'post__in' => $featured_posts_ids
            );
            // Arguments for Posts query that excludes Featured Posts
            $query_args['post__not_in'] = $featured_posts_ids;
        }
    }

    $context['author'] = $term;
}

// Make Post queries based on whether Author term has Featured Posts
if (!empty($featured_query_args)) {
    // TODO: move featuredPosts method to TermRepo
    $context['featured_posts'] = new Timber\PostQuery($featured_query_args, CPIPost::class);
}

$context['posts'] = $term->getArticles($query_args, CPIPost::class);

// Render view
Timber::render( 'pages/author.twig', $context );
