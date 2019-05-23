<?php
/**
 * Repository entity for retrieving post type terms.
 */
namespace CPI\Repositories;

use CPI\Repositories\PostTypeRepository;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_Term;

use Timber\Image;
use Timber\Term;

use CPI\Models\CPIPost;
use CPI\Models\CPIAuthor;
use CPI\Models\CPIPartner;
use CPI\Models\CPITopic;

class TermRepository extends Repository
{
    /**
     * Get the category's thumbnail image. Returns null if thumbnail not found. $term
     * can either be int (term ID), object (Term || WP_Term) or string (term slug).
     *
     * @param mixed $obj Object passed in
     *
     * @return Repository
     */
    public function getImage($obj)
    {
        // Clear old result sets.
        $this->reset();
        $image = null;

        if ($obj instanceof CPIPost) {
            $imgArg = $obj->thumbnail;
        } else if ($obj instanceof CPIAuthor) {
            $imgArg = $obj->picture;
        } else if ($obj instanceof CPIPartner) {
            $imgArg = $obj->partner_icon;
        } else if ($obj instanceof CPITopic) {
            $imgArg = $obj->image_attachment;
        } else if ($obj instanceof Term) {
            $imgArg = $obj->thumbnail;
        } else if ($term instanceof WP_Term) {
            $imgArg = $term->thumbnail;
        } else if ($obj instanceof WP_Post) {
            $imgArg = $obj->featured_image;
        }

        if ($imgArg) {
            $image = new Image($imgArg);
        }

        return $image;
    }
}
