<?php
/**
 * Front page
 *
 * @return void
 */

use CPI\Models\CPIPost;

$context = Timber::get_context();

// Get post
$post = new CPIPost();
$context['article'] = $post;

if (!$post->validMicro()) {
    $context['article'] = $post;
} else {
    wp_redirect($post->link);
    die();
}

// Get CPIPost topic
$context['term'] = $post->articleTopic();

// Get author(s) for CPIPost
$context['article_authors'] = $post->articleAuthors();

// Set relatedPosts
$relatedPosts = [];
$relatedPostQuery = $post->recircPosts();
foreach ($relatedPostQuery as $relatedPost) {
    $relatedPosts[] = $relatedPost;
}
$context['relatedPosts'] = $relatedPosts;
wp_reset_postdata();

// Render view.
Timber::render('pages/article.twig', $context);
