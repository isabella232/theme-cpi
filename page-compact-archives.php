<?php
/**
 *
 * Template Name: Compact Archives
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 */


$context = Timber::get_context();

// get page
$context['page'] = Timber::get_post();


$posts = new Timber\PostQuery(array(
  'posts_per_page' => 8,
  'post_status'    => 'publish',
  'paged'          => $paged
));

$context['posts'] = $posts;

// Render view
Timber::render( 'pages/archive.twig', $context );
