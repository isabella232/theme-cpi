<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 */
namespace CPI\Blocks;

class Blocks
{
    /**
     * Enqueue assets needed for block
     */
    public function __construct()
    {
        add_action('enqueue_block_editor_assets', array($this,'enqueueBlockEditorAssets'));

        new StationID\StationID();


        new EmailSignUp\EmailSignUp();
        new RelatedArticles\RelatedArticles();
        new Gallery\Gallery();
    }

    /**
     * Enqueue Gutenberg block assets for backend editor.
     *
     * @return null
     */
    public function enqueueBlockEditorAssets()
    {
        // Scripts.
        wp_enqueue_script(
            'block-js', // Handle.
            CPI_THEME_URL . '/dist/blocks.build.js', // Block.block.build.js: We register the block here. Built with Webpack.
            array( 'wp-blocks', 'wp-i18n', 'wp-element' ), // Dependencies, defined above.
            true // Enqueue the script in the footer.
        );

        // For StationID block
        $whoWeAre = get_field('who_we_are', 'option');
        wp_localize_script('block-js', 'whoWeAre', $whoWeAre);

        // Styles.
        wp_enqueue_style(
            'block-editor-css', // Handle.
            CPI_THEME_URL . '/dist/block-editor.build.css', // Block editor CSS.
            array( 'wp-edit-blocks' ) // Dependency to include the CSS after it.
        );
    }
}
