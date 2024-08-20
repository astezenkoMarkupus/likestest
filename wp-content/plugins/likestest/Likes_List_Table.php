<?php


class Likes_List_Table extends WP_List_Table {
	public function __construct() {
		parent::__construct( [
			'singular' => 'post',
			'plural'   => 'posts',
			'ajax'     => false,
		] );
	}

	public function get_columns(): array {
		return [
			'title' => _x( 'Запись', 'Column label', 'wp-list-table-example' ),
			'up'    => _x( 'За', 'Column label', 'wp-list-table-example' ),
			'down'  => _x( 'Против', 'Column label', 'wp-list-table-example' ),
		];
	}

	protected function get_sortable_columns(): array {
		return [
			'title' => [ 'title', false ],
			'up'    => [ 'up', false ],
			'down'  => [ 'down', false ],
		];
	}

	protected function column_default( $item, $column_name ) {
		return match ( $column_name ) {
			'title', 'up', 'down' => $item[ $column_name ],
			default               => print_r( $item, true ),
		};
	}

	function prepare_items(): void {
		$per_page = 5;
		$columns  = $this->get_columns();
		$hidden   = [];
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = [ $columns, $hidden, $sortable ];
		$data                  = $this->get_likes_data();

		usort( $data, [ $this, 'usort_reorder' ] );

		$current_page = $this->get_pagenum();
		$total_items  = count( $data );
		$data         = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
		$this->items  = $data;

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page ),
		] );
	}

	protected function usort_reorder( $a, $b ): int {
		$orderby = ! empty( $_REQUEST['orderby'] ) ? wp_unslash( $_REQUEST['orderby'] ) : 'title';
		$order   = ! empty( $_REQUEST['order'] ) ? wp_unslash( $_REQUEST['order'] ) : 'asc';
		$result  = strcmp( $a[ $orderby ], $b[ $orderby ] );

		return ( 'asc' === $order ) ? $result : - $result;
	}

	private function get_likes_data(): array {
		$data  = [];
		$posts = get_posts( [
			'post_type'   => 'post',
			'post_status' => 'any',
			'numberposts' => - 1,
		] );

		if ( empty( $posts ) ) {
			return [];
		}

		foreach ( $posts as $p ) {
			$post_id = $p->ID;
			$data[]  = [
				'title' => '<a href="' . get_the_permalink( $post_id ) . '" target="_blank">' . $p->post_title . '</a>',
				'up'    => $this->get_likes( $post_id ),
				'down'  => $this->get_dislikes( $post_id ),
			];
		}

		return $data;
	}

	private function get_likes( $post_id ): int {
		global $wpdb;

		$query_plus       = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}likestest WHERE post_id = %d AND type = 1",
			$post_id );
		$likes_count_plus = $wpdb->get_var( $query_plus );

		return $likes_count_plus ?: 0;
	}

	private function get_dislikes( $post_id ): int {
		global $wpdb;

		$query_minus       = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}likestest WHERE post_id = %d AND type = 0",
			$post_id );
		$likes_count_minus = $wpdb->get_var( $query_minus );

		return $likes_count_minus ?: 0;
	}
}