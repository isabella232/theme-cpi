<?php

/**
 * Functionality for Author Terms
 */

namespace CPI\Models;

use Timber\Timber;
use Timber\Post;
use Timber\Term;
use Timber\Image;

use CPI\Repositories\RepositoryFactory;
use CPI\Models\CPIPost;
use CPI\Models\CPIPartner;

class CPIAuthor extends Term
{
    /**
     * Constructs instance of CPIAuthor
     *
     * @param mixed $pid - defaults to null
     *
     * @return \CPIAuthor
     */
    public function __construct($pid = null)
    {
        parent::__construct($pid);
    }

    /**
     * Formats Author's name
     *
     * @return string
     */
    public function formattedName()
    {
        $first_name = $this->first_name;
        $last_name = $this->last_name;
        $name = $this->name;

        if ($first_name && $last_name) {
            return "$first_name $last_name";
        } else if ($name) {
            return $name;
        }
    }

    /**
     * Get Author's Articles
     *
     * @param array  $q_args    - query arguments
     * @param string $postClass - default to CPIPost
     *
     * @return array  $posts     - CPIPost collection
     */
    public function getArticles(array $q_args, $postClass = 'CPIPost')
    {
        $postTypeRepo = RepositoryFactory::get(RepositoryFactory::POST_TYPE);
        return $postTypeRepo->latestPosts($q_args, CPIPost::class)->get();
    }

    /**
     * Set Partner Organization as CPIPartner
     *
     * @return \CPIPartner
     */
    public function partnerOrg()
    {
        // TODO: refactor after creating TermRepository
        // $taxRepo = RepositoryFactory::get(RepositoryFactory::TERM);
        return new CPIPartner($this->partner_organization);
    }

    /**
     * Returns Picture as TimberImage
     *
     * @return \Image
     */
    public function getPicture()
    {
        $termRepo = RepositoryFactory::get(RepositoryFactory::TERM);
        return $termRepo->getImage($this);
    }
}
