<?php
/**
 * Repository entity for retrieving post type objects.
 */
namespace CPI\Repositories;

use WP_Query;

use Carbon\Carbon;

use Timber\Post;
use Timber\PostCollection;
use Timber\PostQuery;

class PostTypeRepository extends Repository
{
    const POST_TYPES = ['post']; // Main post types

    /**
     * Returns a chronological list of latest "Post" (articles) posts for a given category.
     * Default $limit is 10.
     *
     * @param string|array $slug    Category slug
     * @param integer      $limit   Number of posts to return
     * @param array        $exclude Array of post ids to exclude
     * @param integer      $paged   Enable paging
     *
     * @return Repository
     */
    public function articlesByCategorySlug($slug, $limit = 10, $exclude = [], $paged = 0)
    {

        // Set sane defaults so we don't do full table scans.
        if ($limit <= 0 || $limit > 1000) {
            $limit = 1000;
        }

        // Note the + symbol. See https://codex.wordpress.org/Class_Reference/WP_Query#Category_Parameters
        if (is_array($slug)) {
            $slug = implode('+', $slug);
        }

        $params = ['posts_per_page' => (int)$limit,
                   'category_name' => $slug,
                   'post_type' => 'post',
                   'post_status' => 'publish',
                   'orderby' => 'date',
                   'order' => 'DESC'];

        if (is_array($exclude) && count($exclude) > 0) {
            $params['post__not_in'] = $exclude;
        }

        if ((int)$paged > 0) {
            $params['paged'] = $paged;
        }

        return $this->query($params);
    }

    /**
     * Returns list of "Posts" between the specified date ranges. $endDate defaults to now if
     * null.
     *
     * @param Carbon  $startDate Start date
     * @param Carbon  $endDate   End date
     * @param integer $paged     Enable paging
     * @param integer $limit     Limit
     * @param \Post   $postClass Class of Post object to returned by query
     *
     * @return Repository
     */
    public function articlesByDateRange(Carbon $startDate, Carbon $endDate = null, $paged = 0, $limit = 10, $postClass = 'Post')
    {
        if ($endDate == null) {
            $endDate = Carbon::now();
        }

        $dateQuery = [
            'after' => ['year' => $startDate->year, 'month' => $startDate->month, 'day' => $startDate->day],
            'before' => ['year' => $endDate->year, 'month' => $endDate->month, 'day' => $endDate->day],
            'inclusive' => true
        ];

        $params = ['date_query' => $dateQuery,
                   'posts_per_page' => $limit,
                   'post_type' => 'post',
                   'post_status' => 'publish',
                   'orderby' => 'date',
                   'order' => 'DESC'];

        if ((int)$paged > 0) {
            $params['paged'] = $paged;
        }

        return $this->query($params, $postClass);
    }

    /**
     * Returns a chronological list of latest posts across all *public* post types. This
     * acts as a "firehose" of new content so to speak.
     *
     * @param array   $params    Array of query arguments
     * @param string  $postClass Class to be used in query
     * @param integer $limit     Number of posts to return
     * @param array   $postType  WordPress post types
     * @param array   $exclude   IDs of posts to exclude
     * @param integer $paged     Enable pagination
     *
     * @return Repository
     */
    public function latestPosts($params = [], $postClass = 'Post', $limit = 10, $postType = self::POST_TYPES, array $exclude = [], $paged = 0)
    {

        // Set sane defaults so we don't do full table scans.
        if ($limit <= 0 || $limit > 1000) {
            $limit = 1000;
        }

        if (empty($params)) {
            $params = ['posts_per_page' => (int)$limit,
                   'post_status' => 'publish',
                   'orderby' => 'date',
                   'order' => 'DESC'];
        }

        if (count($exclude) > 0) {
            $params['post__not_in'] = $exclude;
        }

        if ((int)$paged > 0) {
            $params['paged'] = $paged;
        }

        return $this->query($params, $postClass);
    }
}
