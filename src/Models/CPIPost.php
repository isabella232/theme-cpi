<?php
/**
 * Functionality for Article Posts
 */

namespace CPI\Models;

use Carbon\Carbon;

use Timber\Timber;
use Timber\Post;
use Timber\PostQuery;
use Timber\Term;
use Timber\Image;

use CPI\Repositories\RepositoryFactory;
use CPI\Models\CPIAuthor;
use CPI\Models\CPIPartner;
use CPI\Models\CPITopic;

class CPIPost extends Post
{
    /**
     * Constructs instance of CPIPost
     *
     * @param mixed $pid - defaults to null
     *
     * @return \CPIPost
     */
    public function __construct($pid = null)
    {
        parent::__construct($pid);

        // Set redirect URL for link depending on microsite
        if (!empty($this->microsite_url)) {
            // if ($this->microsite && !empty($this->microsite_url)) {
            $this->link = $this->microsite_url;
        } else if (!empty($this->legacy_url)) {
            $legacyURL = $this->legacy_url;
            $microDomain = (bool)strpos($legacyURL, 'apps.publicintegrity.org');
            if ($this->microsite || $microDomain) {
                $this->link = $this->legacy_url;
            }
        }
    }

    /**
     * Returns collection of CPIAuthor objects for Authors
     *
     * @return array $authors
     */
    public function articleAuthors()
    {
        $authors = $this->get_field('authors');
        if (!empty($authors)) {
            if (is_array($authors)) {
                return array_map(
                    function ($author) {
                        return new CPIAuthor($author);
                    },
                    $authors
                );
            } else {
                return new CPIAuthor($authors);
            }
        }
    }

    /**
     * Formats publish date
     *
     * @return string $date
     */
    public function formatUpdated()
    {
        // Return if updated_date field is empty
        if (empty($this->get_field('updated_date'))) {
            return;
        }

        $pubDate = date_create_from_format('Y-m-d H:i:s', $this->date('Y-m-d H:i:s'));
        $upDate = date_create_from_format('Y-m-d H:i:s', $this->get_field('updated_date'));

        if (date_format($upDate, 'F j, Y g\:i a') === date_format($pubDate, 'F j, Y g\:i a')) {
            return;
        } else {
            // Checks if month, day, and year are same for updated and published dates
            if (date_format($upDate, 'F j, Y') === date_format($pubDate, 'F j, Y')) {
                return 'Today at ' . date_format($upDate, 'g\:i a') . ' EST';
            } else {
                return date_format($upDate, 'F j, Y \a\t g\:i a') . ' ET';
            }
        }
    }

    /**
     * Returns collection of CPIPartner objects for Partners
     *
     * @return array $partners
     */
    public function articlePartners()
    {
        $partners = $this->get_field('article_partners');
        if ($partners) {
            if (is_array($partners)) {
                return array_map(
                    function ($partner) {
                        return new CPIPartner($partner);
                    },
                    $partners
                );
            } else {
                return new CPIPartner($partners);
            }
        }
    }

    /**
     * Returns string from Partnership Description field
     *
     * @return string $partnership_description
     */
    public function partnershipDescription()
    {
        $partnership_description = $this->get_field('partnership_description');
        if (!empty($partnership_description) || !$partnership_description) {
            return $partnership_description;
        }
    }

    /**
     * Formats author(s) names for displaying in views
     *
     * @return string $formatted_display
     */
    public function formattedAuthors()
    {
        $authors = $this->articleAuthors();
        if (!empty($authors)) {
            $formatted_display = '';
            foreach ($authors as $i => $author) {
                $author_name = $author->formattedName();
                $total = count($authors);
                if ($total > 2 && $i != ($total - 1)) {
                    $formatted_display .= "<strong>$author_name</strong>, ";
                } elseif ($total === 2 && $i != ($total - 1)) {
                    $formatted_display .= "<strong>$author_name</strong> ";
                } elseif ($total >= 2 && $i === ($total - 1)) {
                    $formatted_display .= "and <strong>$author_name</strong>";
                } else {
                    $formatted_display .= "<strong>$author_name</strong>";
                }
            }
            return $formatted_display;
        }
    }

    /**
     * Returns Topic as CPITopic
     *
     * @return \CPITopic
     */
    public function articleTopic()
    {
        // TODO: refactor?
        $topics = $this->categories();

        if (is_array($topics) && !empty($topics)) {
            foreach ($topics as $term) {
                if (($term->parent) && !($term->children)) {
                    return new CPITopic($term);
                }
            }

            return new CPITopic($topics[0]);
        }
    }

    /**
     * Return Article topper style
     *
     * @return string $style
     */
    public function topperStyleArticle()
    {
        // TODO: change to setTopperStyles, which change topperStyleArticle and topperStyleTease?
        $thumbnail = $this->getThumbnail;

        if ($thumbnail && ($thumbnail->width >= 600 && $thumbnail->height >= 600)) {
            if ($this->get_field('topper_style')) {
               return $this->get_field('topper_style')['value'];
            } else {
                return 'light';
            }
        } else {
            return 'no-image';
        }
    }

    /**
     * Return Tease topper style
     *
     * @return string $style
     */
    public function topperStyleTease()
    {
        $thumbnail = $this->getThumbnail;

        if ($thumbnail && ($thumbnail->width >= 600 && $thumbnail->height >= 600)) {
            if ($this->get_field('topper_style') === 'no-image') {
                return 'no-image';
            } else {
                return 'image';
            }
        } else {
            return 'no-image';
        }
    }

    /**
     * Returns Thumbnail as TimberImage
     *
     * @return \Image
     */
    public function getThumbnail()
    {
        $termRepo = RepositoryFactory::get(RepositoryFactory::TERM);
        return $termRepo->getImage($this);
    }


    /**
     * Get Article's place in a Series
     *
     * @return int
     */
    public function seriesPlace()
    {
        $featuredPosts = $this->articleTopic()->featuredPosts();
        if (!empty($featuredPosts) && count($featuredPosts) > 1) {
            foreach ($featuredPosts as $index => $post) {
                if ($post->ID === $this->ID) {
                    return $index + 1;
                }
            }
        }
    }

    /**
     * Get number of Articles in Article's Series
     *
     * @return int
     */
    public function seriesTotal()
    {
        $featuredPosts = $this->articleTopic()->featuredPosts();
        if (!empty($featuredPosts) && count($featuredPosts) > 1) {
            return count($featuredPosts);
        }
    }

    /**
     * Redirects to microsites on single views
     *
     * @return void
     */
    public function validMicro()
    {
        $microCheck = !empty($this->get_field('microsite_url'));
        $legacyURL = $this->get_field('legacy_url');
        $legacyCheck = (bool)strpos($legacyURL, 'apps.publicintegrity.org');

        return $microCheck ?: $legacyCheck;
    }

    /**
     * Get recirc CPIPosts for single view
     *
     * @return array $recircPosts Array is empty if no posts are found for recirculation
     */
    public function recircPosts()
    {
        global $post;
        $recirc = [];
        $recircIDs = [];

        $topic = $this->articleTopic();
        if ($topic) {
            $tax = $topic->taxonomy();
            $excludedIDs = $topic->otherTermIDs();
        } else {
            $tax = 'category';
        }

        $temp = $post;
        $getPrev = false;

        // TODO: cache results?
        // Set recirdIDs based on number of previous posts, i.e. if only
        // 1 previous post found, then get next post, and if no previous posts
        // found, get 2 next posts
        for ($i = 0; $i < 2; ++$i) {
            // Get adjacent post based on whether previous post has been added
            if ($getPrev) {
                $post = get_adjacent_post(true, $excludedIDs, true, $tax);
            } else {
                $post = get_adjacent_post(true, $excludedIDs, false, $tax);
            }

            if (!empty($post)) {
                if ($post->post_title !== $temp->post_title) {

                    // If previous post has been added, prepend post to $recirc
                    if ($getPrev) {
                        $recircIDs[] = $post->ID;
                    } else {
                        array_unshift($recircIDs, $post->ID);
                    }
                    setup_postdata($post);
                }
            } else {
                // If next post not found, reset $post and get previous
                $post = $temp;
                setup_postdata($post);
                $post = get_adjacent_post(true, $excludedIDs, true, $tax);
                if (!empty($post)) {
                    if ($post->post_title !== $temp->post_title) {
                        $recircIDs[] = $post->ID;
                        setup_postdata($post);
                        $getPrev = true;
                    }
                }
            }
        }

        $post = $this;
        setup_postdata($post);

        if (!empty($recircIDs)) {
            $args = array(
                'post_status' => 'published',
                'post__in' => $recircIDs
            );

            $recirc = new PostQuery($args, CPIPost::class);
        }

        return $recirc;
    }
}
