const ajaxUrl = ajaxData.ajaxUrl

document.addEventListener( 'DOMContentLoaded', () => {
	'use strict'

	fetchPosts()
	searchByTitle()
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
		method	: 'post',
		body	: formData,
		...args
	} )

	return await response.json()
}

const fetchPosts = () => {
	const form = document.querySelector( '.testplugin-form' )

	if( ! form ) return

	form.addEventListener( 'submit', e => {
		e.preventDefault()

		const
			btn = form.querySelector( 'button' ),
			itemsWrap = document.querySelector( '.testplugin-todos-items' ),
			formData = new FormData()

		btn.classList.add( 'loading' )
		formData.append( 'action', 'testplugin_ajax_load_todos' )
		customAjaxRequest( formData ).then( res => {
			btn.classList.remove( 'loading' )

			if( res ){
				switch( res.success ){
					case true:
						if( itemsWrap ) itemsWrap.innerHTML = res.data.todos
						break

					default:
						console.error( res.data.msg )
				}
			}
		} )
	} )
}

const searchByTitle = () => {
	const form = document.querySelector( '.testplugin-form-search' )

	if( ! form ) return

	form.addEventListener( 'submit', e => {
		e.preventDefault()

		const
			btn = form.querySelector( 'button' ),
			itemsWrap = document.querySelector( '.testplugin-todos-items' ),
			formData = new FormData( form )

		btn.classList.add( 'loading' )
		formData.append( 'action', 'testplugin_ajax_search_todos' )
		customAjaxRequest( formData ).then( res => {
			btn.classList.remove( 'loading' )

			if( res ){
				switch( res.success ){
					case true:
						if( itemsWrap ) itemsWrap.innerHTML = res.data.todos
						break

					default:
						console.error( res.data.msg )
				}
			}
		} )
	} )
}