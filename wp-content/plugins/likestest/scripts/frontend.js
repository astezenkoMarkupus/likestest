const ajaxUrl = ajaxData.ajaxUrl

document.addEventListener( 'DOMContentLoaded', () => {
	'use strict'

	vote()
} )

/**
 * Custom AJAX request.
 *
 * @param	{Object}	formData	Data for fetch body.
 * @param	{Object}	args		Object of additional fetch settings.
 * @returns	{Array}					Response data array.
 */
const customAjaxRequest = async ( formData = {}, args = {} ) => {
	let response = await fetch( ajaxUrl, {
		method: 'post',
		body: formData,
		...args
	} )

	return await response.json()
}

const vote = () => {
	const buttons = document.querySelectorAll( '.js-likes .c-btn' )

	if( ! buttons.length ) return

	console.log( buttons )

	buttons.forEach( button => {
		button.addEventListener( 'click', e => {
			e.preventDefault()

			const
				wrap     = button.closest( '.js-likes' ),
				postId   = button.dataset.id,
				action   = button.classList.contains( 'likes-plus' )
					? 'likestest_ajax_vote_plus'
					: 'likestest_ajax_vote_minus',
				formData = new FormData()

			button.classList.add( 'loading' )
			formData.append( 'action', action )
			formData.append( 'id', postId )

			customAjaxRequest( formData ).then( res => {
				button.classList.remove( 'loading' )

				if( res ){
					switch( res.success ){
						case true:
							const likesEl = wrap.querySelector( '.likes-count' )

							likesEl.innerHTML = res.data.likesCount

							alert( res.data.msg )
							break

						default:
							alert( res.data.msg )
					}
				}
			} )
		} )
	} )
}