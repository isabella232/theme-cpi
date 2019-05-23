<?php
/**
 * Default Page Template
 */
use CPI\Models\CPIPost;

$context = Timber::get_context();
$context['post'] = Timber::get_post();


// Even if we're on a subpage, we want the directory to list every page that descends
// https://css-tricks.com/snippets/wordpress/find-id-of-top-most-parent-page/
if ($post->post_parent)	{
	$ancestors=get_post_ancestors($post->ID);
	$root=count($ancestors)-1;
	$topMostPageID  = $ancestors[$root];
} else {
	$topMostPageID  = $post->ID;
}

$pageDirectoryObjects = get_pages([
    'child_of' => $topMostPageID,
    'sort_column' => 'menu_order'
]);


// Create array of CPI Post objects,
// store in context as pageDirectory
if ($pageDirectoryObjects) {
    // include the topmost page itself at the top
    $context['pageDirectory'][] =  new CPIPost($topMostPageID);
    foreach ($pageDirectoryObjects as $pageObject  ) {
        $context['pageDirectory'][] = new CPIPost($pageObject);
    }
}

// sidebar heading
$pageSidebarHead = get_field('page_sidebar_heading', $topMostPageID) ?: 'In this section';
$context['pageSidebarHead'] = $pageSidebarHead;


Timber::render(['pages/basic-page.twig'], $context);
