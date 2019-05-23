<ul class="updated-posts">
	<?php
	// Show recently modified posts
	$recently_updated_posts = new WP_Query( array(
        'post_type'      => 'post',
        'posts_per_page' => -1,
		'orderby'        => 'modified',
		'no_found_rows'  => true // speed up query when we don't need pagination
    ) );

    $deadline = DateTime::createFromFormat('Y-m-d H:i:s', '2018-11-03 13:00:00');

	if ( $recently_updated_posts->have_posts() ) :
        while( $recently_updated_posts->have_posts() ) : $recently_updated_posts->the_post(); /** ?> */
        $modified_date = get_the_modified_date('Y-m-d H:i:s');
        $mod_date_time = DateTime::createFromFormat('Y-m-d H:i:s', $modified_date);

        if ($mod_date_time < $deadline) :
            $postID = get_the_ID();
            wp_delete_post($postID, true); // force delete the post
        else :

            ?><li><a href="<?php the_permalink(); ?>" title="<?php esc_attr( get_the_title() ); ?>"><?php the_title(); ?></a><?php echo get_the_modified_date('Y-m-d H:i:s'); ?></li>
        <?php endif ?>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>
</ul>
