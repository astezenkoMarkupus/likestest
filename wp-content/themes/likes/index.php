<?php
/**
 * Index page default template.
 *
 * @package    WordPress
 * @subpackage critick
 */

get_header();

$posts = get_posts( [
	'post_type'   => 'post',
	'post_status' => 'publish',
] );
?>

	<div class="container">
		<div class="main-wrap">
			<main class="main">
				<h2 class="section-title"><?php _e( 'Статьи', 'critick' ) ?></h2>

				<?php
				if ( ! empty( $posts ) ) {
					foreach ( $posts as $p ) {
						$post_id = $p->ID;
						get_template_part( 'template-parts/preview', null, [ 'id' => $post_id ] );
					}
				} else {
					esc_html_e( 'Posts not found.', 'critick' );
				}

				if ( get_next_posts_link() ) {
					next_posts_link( '' );
				}
				?>
			</main>

			<?php get_sidebar() ?>
		</div>
	</div>

<?php
get_footer();

