<?php
/**
 * Index page default template.
 *
 * @package    WordPress
 * @subpackage critick
 */

get_header();

global $wp_query;

$posts_per_page = get_option( 'posts_per_page' );
$paged          = ! empty( $wp_query->query['paged'] ) ? $wp_query->query['paged'] : 1;
$posts          = new WP_Query( [
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => $posts_per_page,
	'paged'          => $paged,
] );
?>

	<div class="container">
		<div class="main-wrap">
			<main class="main">
				<h2 class="section-title"><?php _e( 'Статьи', 'critick' ) ?></h2>

				<?php
				if ( $posts->have_posts() ) {
					while ( $posts->have_posts() ) {
						$posts->the_post();
						get_template_part( 'template-parts/preview', null, [ 'id' => get_the_ID() ] );
					}

					$max_page = $posts->max_num_pages ?? 1;

					echo paginate_links( [
						'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
						'format'    => '?paged=%#%',
						'current'   => max( 1, $paged ),
						'total'     => $max_page,
						'mid_size'  => 0,
						'end_size'  => 3,
						'prev_text' => '<span class="page-numbers-text">' . __( 'Назад', 'critick' ) . '</span><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<g clip-path="url(#clip0_4_1115)">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M12.6809 1.66598L6.59843 8.01937L12.6422 14.4118L11.0537 16L3.30593 8.05818L11.0156 -5.34786e-05L12.6809 1.66598Z" fill="black"/>
							</g>
							<defs>
							<clipPath id="clip0_4_1115">
							<rect width="16" height="16" fill="white" transform="matrix(4.37114e-08 -1 -1 -4.37114e-08 16 16)"/>
							</clipPath>
							</defs>
							</svg>',
						'next_text' => '<span class="page-numbers-text">' . __( 'Вперед', 'critick' ) . '</span><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<g clip-path="url(#clip0_4_1115)">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M12.6809 1.66598L6.59843 8.01937L12.6422 14.4118L11.0537 16L3.30593 8.05818L11.0156 -5.34786e-05L12.6809 1.66598Z" fill="black"/>
							</g>
							<defs>
							<clipPath id="clip0_4_1115">
							<rect width="16" height="16" fill="white" transform="matrix(4.37114e-08 -1 -1 -4.37114e-08 16 16)"/>
							</clipPath>
							</defs>
							</svg>',
						'type'      => 'list',
					] );

					wp_reset_query();
				} else {
					esc_html_e( 'Posts not found.', 'critick' );
				}
				?>
			</main>

			<?php get_sidebar() ?>
		</div>
	</div>

<?php
get_footer();

