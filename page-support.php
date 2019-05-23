<?php
/**
 *
 * Template Name: Support Us
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 */


$context = Timber::get_context();

// get page
$context['page'] = Timber::get_post();

// Render view
Timber::render( 'pages/support.twig', $context );
