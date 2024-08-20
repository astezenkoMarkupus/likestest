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

		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_action( 'wp_ajax_likestest_ajax_vote_plus', [ $this, 'likestest_ajax_vote_plus' ] );
		add_action( 'wp_ajax_likestest_ajax_vote_plus', [ $this, 'likestest_ajax_vote_plus' ] );
		add_action( 'wp_ajax_likestest_ajax_vote_minus', [ $this, 'likestest_ajax_vote_minus' ] );
		add_action( 'wp_ajax_likestest_ajax_vote_minus', [ $this, 'likestest_ajax_vote_minus' ] );
	}

	public function admin_enqueue_scripts(): void {
		wp_enqueue_style( 'likestest-admin', plugins_url( 'styles/admin.css', __FILE__ ), [], self::PLUGIN_VERSION );
		wp_enqueue_script( 'likestest-admin', plugins_url( 'scripts/admin.js', __FILE__ ), [], self::PLUGIN_VERSION,
			true );
		wp_localize_script( 'likestest-admin', 'ajaxData', [ 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ] );
	}

	public function enqueue_scripts(): void {
		wp_enqueue_script( 'likestest', plugins_url( 'scripts/frontend.js', __FILE__ ), [], self::PLUGIN_VERSION,
			true );
		wp_localize_script( 'likestest', 'ajaxData', [ 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ] );
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
	            type tinyint(1) NOT NULL,
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

	public function likestest_ajax_vote_plus(): void {
		$post_id = isset( $_POST['id'] ) ? (int) $_POST['id'] : null;

		if ( ! $post_id ) {
			wp_send_json_error( [ 'msg' => __( 'Неверные данные', 'likestest' ) ] );
		}

		// If plus already exists - remove row.
		if ( $this->check_if_already_voted_by_ip( $post_id, 1 ) ) {
			$this->remove_liked_post_row( $post_id );
			wp_send_json_success( [
				'msg'        => __( 'Голос снят', 'likestest' ),
				'likesCount' => $this->get_post_likes_count( $post_id ),
			] );
		}

		// If minus exists - update 0 to 1 in DB.
		if ( $this->check_if_already_voted_by_ip( $post_id ) ) {
			$this->update_liked_post_row( $post_id, 1 );
			wp_send_json_success( [
				'msg'        => __( 'Жаль, что Вам не понравился этот пост :(', 'likestest' ),
				'likesCount' => $this->get_post_likes_count( $post_id ),
			] );
		}

		// If we are here - there's no row in DB, let's add it!
		$insert = $this->db->insert( "{$this->db->prefix}likestest", [
			'ip'        => $this->get_ip_address(),
			'post_id'   => $post_id,
			'timestamp' => time(),
			'type'      => 1,
		] );

		if ( ! $insert ) {
			wp_send_json_error( [ 'msg' => __( 'Ошибка при записи данных', 'likestest' ) ] );
		}

		wp_send_json_success( [
			'msg'        => __( 'Рады, что Вам понравился этот пост :)', 'likestest' ),
			'likesCount' => $this->get_post_likes_count( $post_id ),
		] );
	}

	public function likestest_ajax_vote_minus(): void {
		$post_id = isset( $_POST['id'] ) ? (int) $_POST['id'] : null;

		if ( ! $post_id ) {
			wp_send_json_error( [ 'msg' => __( 'Неверные данные', 'likestest' ) ] );
		}

		// If minus already exists - remove row.
		if ( $this->check_if_already_voted_by_ip( $post_id ) ) {
			$this->remove_liked_post_row( $post_id );
			wp_send_json_success( [
				'msg'        => __( 'Голос снят', 'likestest' ),
				'likesCount' => $this->get_post_likes_count( $post_id ),
			] );
		}

		// If plus exists - update 1 to 0 in DB.
		if ( $this->check_if_already_voted_by_ip( $post_id, 1 ) ) {
			$this->update_liked_post_row( $post_id );
			wp_send_json_success( [
				'msg'        => __( 'Жаль, что Вам не понравился этот пост :(', 'likestest' ),
				'likesCount' => $this->get_post_likes_count( $post_id ),
			] );
		}

		// If we are here - there's no row in DB, let's add it!
		$insert = $this->db->insert( "{$this->db->prefix}likestest", [
			'ip'        => $this->get_ip_address(),
			'post_id'   => $post_id,
			'timestamp' => time(),
			'type'      => 0,
		] );

		if ( ! $insert ) {
			wp_send_json_error( [ 'msg' => __( 'Ошибка при записи данных', 'likestest' ) ] );
		}

		wp_send_json_success( [
			'msg'        => __( 'Жаль, что Вам не понравился этот пост :(', 'likestest' ),
			'likesCount' => $this->get_post_likes_count( $post_id ),
		] );
	}

	private function get_post_likes_count( int $post_id ): int {
		$query_plus        = $this->db->prepare( "SELECT COUNT(*) FROM {$this->db->prefix}likestest WHERE post_id = %d AND type = 1",
			$post_id );
		$likes_count_plus  = $this->db->get_var( $query_plus );
		$likes_count_plus  = $likes_count_plus ?: 0;
		$query_minus       = $this->db->prepare( "SELECT COUNT(*) FROM {$this->db->prefix}likestest WHERE post_id = %d AND type = 0",
			$post_id );
		$likes_count_minus = $this->db->get_var( $query_minus );
		$likes_count_minus = $likes_count_minus ?: 0;

		return $likes_count_plus - $likes_count_minus;
	}

	private function get_ip_address(): string {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}

	private function check_if_already_voted_by_ip( int $post_id, int $type = 0 ): bool {
		$query = $this->db->prepare( "SELECT * FROM {$this->db->prefix}likestest WHERE post_id = %d AND ip = %s AND type = %d",
			$post_id, $this->get_ip_address(), $type );
		$res   = $this->db->get_var( $query );

		return (bool) $res;
	}

	private function update_liked_post_row( int $post_id, int $type = 0 ): void {
		$this->db->update( "{$this->db->prefix}likestest", [ 'type' => $type ], [
			'post_id' => $post_id,
			'ip'      => $this->get_ip_address(),
		] );
	}

	private function remove_liked_post_row( int $post_id ): void {
		$this->db->delete( "{$this->db->prefix}likestest", [
			'post_id' => $post_id,
			'ip'      => $this->get_ip_address(),
		] );
	}
}

new LikesTest();
