<?php
/**
 * Basic factory for generating repository objects.
 */
namespace CPI\Repositories;

class RepositoryFactory
{
    const POST_TYPE = 'post_type';
    const TERM = 'term';
    /**
     * Returns correct repository
     *
     * @param mixed $which - Constant used to determine Repository
     *
     * @return \Repository
     */
    public static function get($which)
    {
        switch ($which) {
            case self::POST_TYPE:
                return new PostTypeRepository();
            case self::TERM:
                return new TermRepository();
        }
    }
}
