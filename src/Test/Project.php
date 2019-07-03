<?php
/**
 * Created by PhpStorm.
 * User: ryankanner
 * Date: 10/1/18
 * Time: 2:31 PM
 */

namespace WPDumb\Test;


use WPDumb\Migration;

class Project extends Migration {

	public function __construct() {
		parent::__construct( [
			'name' => 'Project Test',
			'version' => '1.0.0',
			'type' => 'project',
			'batch_size' => 100,
			'blocking' => true,
			'runner' => 'cron',
		] );
	}

	public function up( $batch = 0 ) {
		$posts = $this->query( $batch );
		$posts = $posts->posts;
		if ( ! empty( $posts ) && is_array( $posts ) ) {
			foreach ( $posts as $post ) {
				update_post_meta( $post, 'wp_dumb_project_meta', 'updated' );
			}
		}
	}

	public function down( $batch = 0 ) {
		$posts = $this->query( $batch );
		$posts = $posts->posts;
		if ( ! empty( $posts ) && is_array( $posts ) ) {
			foreach ( $posts as $post ) {
				delete_post_meta( $post, 'wp_dumb_project_meta' );
			}
		}
	}

	private function query( $batch ) {

		$args = [
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => $this->get_batch_size(),
			'offset' => $this->get_offset( $batch ),
			'fields' => 'ids',
		];

		return new \WP_Query( $args );

	}

}
