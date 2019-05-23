<?php
/**
 * Generic 404 page controller.
 *
 */


// Page title.
$context = Timber::get_context();
$context['wp_title'] = 'Error 404: Page not found';

// Render view.
Timber::render('pages/404.twig', $context);
