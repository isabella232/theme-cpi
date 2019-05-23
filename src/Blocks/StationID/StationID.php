<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 */
namespace CPI\Blocks\StationID;

use Timber\Timber;

class StationID
{
    /**
     * Constructor
     */
    public function __construct()
    {
        register_block_type(
            'cpi/station-id',
            array(
                'render_callback' => array($this,'renderStationId'),
            )
        );
    }



    /**
     * Get the Station ID text and render corrosponding template
     *
     * @param array  $attributes Gutenberg attributes
     * @param string $content    Gutenberg content
     *
     * @return string Rendered content
     */
    public function renderStationId($attributes, $content)
    {
        $stationIdText = get_field('who_we_are', 'option');
        $stationIdLink = get_field('about_us_page_link', 'option');
        $stationIdPage = null;
        if (is_array($stationIdLink) && sizeof($stationIdLink) > 0) {
            $stationIdPage = Timber::get_post($stationIdLink[0]->ID);
        }

        ob_start();
        Timber::render('templates/components/station-id.twig', ['stationIdText'=>$stationIdText,'stationIdPage'=>$stationIdPage]);
        return ob_get_clean();
    }
}
