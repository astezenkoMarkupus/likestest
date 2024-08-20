<div id="testplugin-admin" class="wrap">
	<div id="icon-tools" class="icon32"><br></div>

	<h2 class="testplugin-title"><?php _e( 'TestPlugin Settings' ) ?></h2>

	<?php
	if( ! empty( $_GET['updated'] ) ){
		?>
		<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
			<p>
				<strong><?php _e('Settings saved.') ?></strong>
			</p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text">Dismiss this notice.</span>
			</button>
		</div>
		<?php
	}
	?>

	<form class="form testplugin-form">
		<fieldset>
			<button class="button">
				<?php _e( 'Sync ToDos', 'testplugin' ) ?>
				<span>...</span>
			</button>
		</fieldset>
	</form>

	<form class="form testplugin-form-search">
		<fieldset>
			<label>
				<input type="text" name="title">
			</label>
			<button class="button">
				<?php _e( 'Search by Title', 'testplugin' ) ?>
				<span>...</span>
			</button>
		</fieldset>
	</form>

	<div class="testplugin-todos-list">
		<h2>ToDos:</h2>
		<div class="testplugin-todos-heading">
			<div>ID</div>
			<div>User ID</div>
			<div>ToDo ID</div>
			<div>Title</div>
			<div>Completed</div>
		</div>
		<div class="testplugin-todos-items"></div>
	</div>
</div>

