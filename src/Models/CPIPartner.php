<?php
/**
 * Functionality for Partner Terms
 */
namespace CPI\Models;

use Timber\Timber;
use Timber\Post;
use Timber\Term;
use Timber\Image;

use CPI\Repositories\RepositoryFactory;
use CPI\Models\CPIPost;

class CPIPartner extends Term
{
    /**
     * Constructs instance of CPIPartner
     *
     * @param mixed $pid - defaults to null
     *
     * @return \CPIPartner
     */
    public function __construct($pid = null)
    {
        parent::__construct($pid);
    }

    /**
     * Returns Partner Icon as TimberImage
     *
     * @return \Image
     */
    public function getIcon()
    {
        $termRepo = RepositoryFactory::get(RepositoryFactory::TERM);
        return $termRepo->getImage($this);
    }
}
