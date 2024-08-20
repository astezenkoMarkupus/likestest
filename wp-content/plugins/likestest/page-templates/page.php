<div id="likestest-admin" class="wrap">
	<div id="icon-tools" class="icon32"><br></div>

	<h2 class="likestest-title"><?php _e( 'Posts Likes' ) ?></h2>

	<form id="likes-filter" method="get">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php $this->listTable->display() ?>
	</form>
</div>

