<?php
/**
 * Template Name: Home Page
*/

use Timber\Image;

use CPI\Repositories\PostTypeRepository;
use CPI\Models\CPIPost;

$context = Timber::get_context();
$page = Timber::get_post();
$context['page'] = $page;

$homeTopperType = $page->home_topper_type;
// $homeTopperType = $page->get_field('home_topper_type');
$context['home_topper_type'] = $homeTopperType;

// TODO: if time allows, refactor
if ($homeTopperType === 'datapoint' && $page->linked_page()) {
    // Set HomeTopper - Linked Page
    $linkedPage = new CPIPost($page->linked_page()[0]);
    $illustration = !empty($page->data_point_illustration) ? new Image($page->data_point_illustration) : null;
    $context['home_topper_linked_page'] = $linkedPage;
    $context['home_topper_image'] = $illustration;
    $context['home_topper_headline'] = $page->get_field('data_point_headline');
    $context['home_topper_dek'] = $page->get_field('data_point_dek');
    $context['home_topper_link'] = $linkedPage->link();
    $context['home_topper_link_text'] = 'page' == $linkedPage->post_type ? $linkedPage->title() : "Read more";

} else if ($homeTopperType === 'article' && $page->topper_article()) {
    // Set Home Topper - Topper Article
    $topperArticle = new CPIPost($page->topper_article()[0]);
    $context['home_topper_article'] = $topperArticle;
    $context['home_topper_image'] = $topperArticle->getThumbnail();
    $context['home_topper_topic'] = $topperArticle->articleTopic();
    $context['home_topper_headline'] = $topperArticle->title();
    $context['home_topper_dek'] = $topperArticle->subtitle();
    $context['home_topper_link'] = $topperArticle->link();
    $context['home_topper_link_text'] = 'Read';
}

// Set Home Features Posts
$homeFeatures = get_field('home_features');
if (is_array($homeFeatures) && !empty($homeFeatures)) {
    $context['homeFeatures'] = array_map(
        function ($post) {
            return new CPIPost($post);
        }, $homeFeatures
    );
}

// Set Home Impact
if (!empty($page->get_field('home_impact'))) {
    $context['home_impact'] = $page->get_field('home_impact');
}

// Set Home Partners Logos
$partnerLogos = $page->get_field('home_partners_logos');
if (!empty($partnerLogos)) {
    $context['home_partners_logos'] = array_map(
        function($logo) {
            return new Image($logo['partner_logo']);
        }, $partnerLogos
    );
}

// Render view.
Timber::render('pages/home.twig', $context);
