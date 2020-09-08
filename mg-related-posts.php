<?php
/*
Plugin Name: MG Related Posts
Description: Another Related Posts plugin
Version:     1.0
Author:      Mauricio Gelves
Author URI:  https://maugelves.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: mgrp
Domain Path: /languages
*/

// We don't want hackers in our plugin.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );



/**
 * Get related posts for a specific post.
 *
 * @param $post_id
 * @param int $post_count
 */
function mg_get_related_posts( $post_id, $post_count = 3 ) {

	$taxonomies = get_object_taxonomies( get_post_type( $post_id ) );

	if( empty( $taxonomies ) ) return;

	$args = [
		'posts_per_page' => $post_count,
		'tax_query'      => [
			'relation' => 'OR',
		]
	];

	foreach ($taxonomies as $taxonomy):
		$terms[ $taxonomy ] = get_the_terms( $post_id, $taxonomy );

		if( ! empty( $terms[$taxonomy] ) ):

			$terms_array = [];
			foreach( $terms[$taxonomy] as $term ):

				array_push( $terms_array, $term->slug );

			endforeach;

			array_push( $args ['tax_query'], [
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $terms_array,
			] );

		endif;

	endforeach;


	$posts = new WP_Query( $args );

	if( $posts->have_posts() ): ?>
		<ul class="mgrp">
			<?php
			while( $posts->have_posts() ): $posts->the_post(); ?>
				<li class="mgrp__item">
					<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</li>
			<?php endwhile;
			wp_reset_postdata();
			?>
		</ul><!-- end .mgrp -->

	<?php
	endif;

}