<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 */
namespace CPI\Blocks\Gallery;

use Timber\Timber;

class Gallery
{
    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('acf/init', array($this, 'createGalleryBlock'));
    }

    /**
     * Uses ACF function to register a custom blocks
     *
     * @return void
     */
    public function createGalleryBlock()
    {
        // check function exists
        if (function_exists('acf_register_block')) {

        // register a testimonial block
            acf_register_block(
                array(
                    'name'              => 'cpiGallery',
                    'title'             => __('CPI Gallery'),
                    'description'       => __('A custom gallery block.'),
                    'render_callback'   => array($this, 'renderGalleryBlock'),
                    'category'          => 'widgets',
                    'icon'              => array('background' => '#ecf6f6',  'src' => 'images-alt2'),
                    'keywords'          => array( 'gallery', 'images' ),
                    'mode'              => 'edit'
                )
            );
        }
    }

    /**
     * Renders the custom gallery block in preview and front endDate
     *
     * @param array  $block      The block settings and attributes.
     * @param string $content    The block content (emtpy string).
     * @param bool   $is_preview True during AJAX preview.
     *
     * @return void
     */
    public function renderGalleryBlock($block, $content, $is_preview)
    {
            $context['gallery_caption']= get_field('gallery_caption');
            $context['images'] = array_map(
                function ($image) {
                return new \TimberImage($image['image']);
                },
                get_field('images')
            );
        if ($is_preview) {
            echo "Preview mode is not supported for galleries. Please change to Edit mode by clicking the pencil icon in the toolbar above.";
        } else {
            Timber::render('templates/components/gutenberg-gallery.twig', $context);
        }
    }
}
