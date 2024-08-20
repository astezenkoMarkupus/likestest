<?php
/**
 * Single post preview.
 *
 * @package    WordPress
 * @subpackage critick
 */

$post_id     = $args['id'] ?? get_the_ID();
$author_id   = get_post_field( 'post_author', $post_id );
$author_name = get_the_author_meta( 'display_name', $author_id );
?>

<article class="preview post-<?php echo esc_attr( $post_id ) ?>">
	<div class="preview-thumb">
		<?php
		if ( has_post_thumbnail( $post_id ) ) {
			get_template_part( 'components/image', null, [
				'data' => crit_prepare_image_data( get_post_thumbnail_id( $post_id ), 'preview', [], [ 'is_lazy' => 1 ] ),
			] );
		}
		?>
	</div>

	<div class="preview-body">
		<h3 class="preview-title">
			<?php echo get_the_title( $post_id ) ?>
		</h3>

		<?php
		if ( has_excerpt( $post_id ) ) {
			echo '<div class="preview-excerpt">' . get_the_excerpt( $post_id ) . '</div>';
		}
		?>

		<div class="preview-bottom">
			<div class="preview-author">
				<?php printf( __( '<span>Автор:</span> %s', 'critick' ), $author_name ) ?>
			</div>

			<?php echo do_shortcode( '[likestest post_id=' . $post_id . ']' ) ?>
		</div>
	</div>
</article><!-- .preview -->

