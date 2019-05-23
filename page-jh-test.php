<ul class="updated-posts">
	<?php
	// Show recently modified posts
	$recently_updated_posts = new WP_Query( array(
		'post_type'      => 'post',
		'posts_per_page' => 1000,
		'orderby'        => 'modified',
		'no_found_rows'  => true // speed up query when we don't need pagination
	) );
	?><table><?php
	if ( $recently_updated_posts->have_posts() ) :
		while( $recently_updated_posts->have_posts() ) : $recently_updated_posts->the_post(); ?>
			<tr><td><a href="<?php the_permalink(); ?>" title="<?php esc_attr( get_the_title() ); ?>"><?php the_title(); ?></a></td>
			<td><?php echo get_the_date() ?></td>
			<td><?php echo get_the_modified_date(); ?></td>
			<td><?php echo get_the_modified_time(); ?></td>
	</tr>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>
</ul>
