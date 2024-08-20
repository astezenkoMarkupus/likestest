<?php
/**
 * Likes component layout.
 *
 * @package    WordPress
 * @subpackage critick
 */

if ( ! $post_id = $args['id'] ?? null ) {
	return;
}

$likes_count = crit_get_post_likes_count( $post_id );
?>

<div class="c-likes js-likes">
	<button class="c-btn likes-plus" data-id="<?php echo esc_attr( $post_id ) ?>">
		<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
			<g clip-path="url(#clip0_5_204)">
				<path d="M11 22C17.0751 22 22 17.0751 22 11C22 4.92487 17.0751 0 11 0C4.92487 0 0 4.92487 0 11C0 17.0751 4.92487 22 11 22Z" fill="currentColor"/>
				<path d="M11 5.71997V16.72" stroke="white" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M16.5 11H5.5" stroke="white" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
			</g>
			<defs>
				<clipPath id="clip0_5_204">
					<rect width="22" height="22" fill="white"/>
				</clipPath>
			</defs>
		</svg>
	</button>

	<span class="likes-count"><?php echo esc_attr( $likes_count ) ?></span>

	<button class="c-btn likes-minus" data-id="<?php echo esc_attr( $post_id ) ?>">
		<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
			<g clip-path="url(#clip0_5_209)">
				<path d="M11 22C17.0751 22 22 17.0751 22 11C22 4.92487 17.0751 0 11 0C4.92487 0 0 4.92487 0 11C0 17.0751 4.92487 22 11 22Z" fill="currentColor"/>
				<path d="M16.72 11H5.28" stroke="white" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
			</g>
			<defs>
				<clipPath id="clip0_5_209">
					<rect width="22" height="22" fill="white"/>
				</clipPath>
			</defs>
		</svg>
	</button>
</div>

