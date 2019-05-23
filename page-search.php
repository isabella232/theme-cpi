<?php
/**
 * Template Name: Search
 */
use CPI\Repositories\PostTypeRepository;
use CPI\Models\CPIPost;
$context = Timber::get_context();
$context['post'] = Timber::get_post();

// Get Home Features Posts
$homeID = get_page_by_title( 'Home' )->ID;
$homeFeatures = get_field('home_features', $homeID);

 // limit to 4
 $homeFeatures = array_slice($homeFeatures, 4);
if (is_array($homeFeatures) && !empty($homeFeatures)) {
    $context['homeFeatures'] = array_map(
        function ($post) {
            return new CPIPost($post);
        }, $homeFeatures
    );
}

Timber::render(['pages/search.twig'], $context);
