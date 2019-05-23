<?php
/**
 * Functionality for Topic Terms
 */

namespace CPI\Models;

use Timber\Timber;
use Timber\Post;
use Timber\PostQuery;
use Timber\Term;
use Timber\Image;

use CPI\Repositories\RepositoryFactory;
use CPI\Models\CPIPost;

class CPITopic extends Term
{
    /**
     * Constructs instance of CPITopic
     *
     * @param mixed $pid - defaults to null
     *
     * @return \CPITopic
     */
    public function __construct($pid = null)
    {
        parent::__construct($pid);

        // Set empty array for $menuChildren
        $this->menuChildren = array();

        // Set array of term IDs of other related categories
        if (!isset($this->otherTermIDs)) {
            $this->otherTermIDs = [];
        }
    }

    /**
     * Returns an array of other term IDs from this topic's hierarchy
     *
     * @return array
     */
    public function otherTermIDs()
    {
        $mainTopics = get_terms('category', array('parent' => 0));
        $parent = $this->topParent();
        $children = $parent->children();
        $allIDs = [];

        if (!empty($children) && is_array($children)) {
            foreach ($children as $child) {
                if ($this->term_id !== $child->term_id) {
                    array_push($allIDs, $child->term_id);
                }
            }
        }

        if (!empty($mainTopics)) {
            foreach ($mainTopics as $topic) {
                if ($parent->term_id !== $topic->term_id) {
                    array_push($allIDs, $topic->term_id);
                }
            }
        }

        return $allIDs;

    }

    /**
     * Find and return top-most topic in this topic's hierarchy
     *
     * @return \CPITopic
     */
    public function topParent()
    {
        $term = $this;

        // limit check against nested levels to 5
        for ($i = 0; $i < 5; $i++) {
            if (!$term->parent) {
                return $term;
            } else {
                $term = new CPITopic($term->parent);
            }
        }
    }

    /**
     * Returns array of IDs for Featured Posts
     *
     * @return array $featuredPostsIDs
     */
    public function featuredPostsIDs()
    {
        $featuredPosts = $this->featured_posts();
        $featuredIDs = array();

        if (!empty($featuredPosts) && is_array($featuredPosts)) {
            $featuredIDs = array_map(
                function ($post) {
                    return $post->ID;
                },
                $featuredPosts
            );
        }

        return $featuredIDs;
    }

    /**
     * Returns Featured Posts as collection of CPIPosts
     *
     * @param array $q_args Array of query arguments
     *
     * @return array $featuredPost(s)
     */
    public function featuredPosts($q_args = [])
    {
        if (empty($q_args)) {
            $featuredPosts = $this->featuredPostsIDs();

            if (is_array($featuredPosts) && !empty($featuredPosts)) {
                global $paged;
                if (!isset($paged) || !$paged) {
                    $paged = 1;
                }

                // Set query args for series
                if ($this->series) {
                    $q_args = array(
                        'posts_per_page' => 8,
                        'post_status' => 'publish',
                        'cat' => $this->id,
                        'paged' => $paged,
                        'post__in' => $featuredPosts,
                        'order' => 'ASC',
                        'orderby' => 'post_date'
                    );
                } else {
                    $q_args = array(
                        'posts_per_page' => 8,
                        'post_status' => 'publish',
                        'cat' => $this->id,
                        'paged' => $paged,
                        'post__in' => $featuredPosts,
                        'orderby' => 'post__in'
                    );
                }

                return new PostQuery($q_args, CPIPost::class);
            }
        } else {
            return new PostQuery($q_args, CPIPost::class);
        }
    }

    /**
     * Returns Featured Authors as collection of CPIAuthors
     *
     * @return array $featuredAuthor(s)
     */
    public function featuredAuthors()
    {
        $featuredAuthors = $this->featured_authors();
        if (!empty($featuredAuthors)) {
            if (is_array($featuredAuthors)) {
                return array_map(
                    function ($featuredAuthor) {
                        return new CPIAuthor($featuredAuthor);
                    },
                    $featuredAuthors
                );
            } else {
                return new CPIAuthor($featuredAuthors);
            }
        }
    }

    // TODO: getIllustration? Add field for illustration image?
    /**
     * Returns Image Attachment as TimberImage
     *
     * @return \Image
     */
    public function getImage()
    {
        $termRepo = RepositoryFactory::get(RepositoryFactory::TERM);
        return $termRepo->getImage($this);
    }
}
