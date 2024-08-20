<?php

/**
 * Plugin Name: Likes Test
 * Description: Test task.
 * Version: 0.1
 * Author: Andrei Stezenko
 * Author URI: https://github.com/astezenkoMarkupus/
 */

require 'vendor/autoload.php';

class LikesTest {
	private wpdb $db;

	const PLUGIN_VERSION      = '0.0.1';
	const PLUGIN_DB_VERSION   = '0.0.1';
	const PAGE_TEMPLATES_PATH = WP_PLUGIN_DIR . '/likestest/page-templates';

	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;

		register_activation_hook( __FILE__, [ $this, 'likestest_activation' ] );
		register_deactivation_hook( __FILE__, [ $this, 'likestest_deactivation' ] );

		add_action( 'admin_menu', [ $this, 'add_admin_page' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts(): void {
		wp_enqueue_style( 'likestest-admin', plugins_url( 'styles/admin.css', __FILE__ ), [], self::PLUGIN_VERSION );
		wp_enqueue_script( 'likestest-admin', plugins_url( 'scripts/admin.js', __FILE__ ), [], self::PLUGIN_VERSION,
			true );
		wp_localize_script( 'likestest-admin', 'ajaxData', [ 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ] );
	}

	/**
	 * Plugin activated.
	 *
	 * @return void
	 */
	public function likestest_activation(): void {
		$db_table_name   = "{$this->db->prefix}likestest";
		$charset_collate = $this->db->get_charset_collate();

		if ( ( $this->db->get_var( "show tables like '$db_table_name'" ) != $db_table_name ) ||
		     ( get_option( sanitize_title( 'likestest_db_version' ) !== self::PLUGIN_DB_VERSION ) ) ) {
			$sql = "CREATE TABLE $db_table_name (
	            id int(10) unsigned NOT NULL AUTO_INCREMENT,
	            ip varchar(64) NOT NULL,
	            post_id int(10) NOT NULL,
	            timestamp int(10) NOT NULL,
	            PRIMARY KEY (id)
	        ) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			add_option( 'likestest_db_version', self::PLUGIN_DB_VERSION );
		}
	}

	/**
	 * Plugin deactivated.
	 *
	 * @return void
	 */
	public function likestest_deactivation(): void {
		//
	}

	/**
	 * Add the plugin page to the WP Admin menu.
	 *
	 * @return void
	 */
	public function add_admin_page(): void {
		add_menu_page( 'LikesTest Settings', 'LikesTest', 'manage_options', 'likestest', [
			$this,
			'render_template',
		] );
	}

	/**
	 * Show Admin page layout.
	 *
	 * @return void
	 */
	public function render_template(): void {
		$template_path = self::PAGE_TEMPLATES_PATH . '/page.php';

		if ( ! is_readable( $template_path ) ) {
			return;
		}

		include $template_path;
	}

	private function getPosts(): string {
		$posts = $this->db->get_results( "SELECT * FROM {$this->db->prefix}likestest" );

		$res = '';

		foreach ( $posts as $p ) {
			$res .= '<div class="likestest-post">';
			$res .= '</div>';
		}

		return $res;
	}
}

new LikesTest();
