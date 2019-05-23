<?php
/**
 * Mostly involved with cleaning up default WordPress cruft.
 */
namespace CPI\Managers;

use CPI\Blocks;

class GutenbergManager
{

    /**
     * Runs initialization tasks.
     *
     * @return void
     */
    public function run()
    {
        add_filter('allowed_block_types', array($this,'disableBlocks'));

        new \CPI\Blocks\Blocks();
    }

    /**
     * Only allow the Gutenberg blocks we actually needed
     *
     * @param array $allowed_blocks Allowed blocks
     *
     * @return void
     */
    public function disableBlocks($allowed_blocks)
    {
        return array(
            'core/block',
            'core/image',
            'core/paragraph',
            'core/heading',
            'core/list',
            'core/subhead',
            'core/quote',
            'core/audio',
            'core/video',
            'core/table',
            'core/freeform',
            'core/html',
            'core/preformatted',
            'core/pullquote',
            'core/separator',
            'core/embed',
            'core-embed/twitter',
            'core-embed/youtube',
            'core-embed/facebook',
            'core-embed/instagram',
            'core-embed/soundcloud',
            'core-embed/spotify',
            'core-embed/vimeo',
            'core-embed/issuu',
            'core-embed/imgur',
            'core-embed/reddit',
            'core-embed/scribd',
            'core-embed/slideshare',
            'core-embed/tumblr',
            'cpi/station-id',
            'acf/cpigallery',
            'acf/emailsignup',
            'acf/relatedarticles'
        );
    }
}
