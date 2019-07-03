<?php

namespace WPDumb;


class Handler {

	const API_NAMESPACE = 'wpdumb/v1';

	const ENDPOINT_RUN = 'migration';

	public function setup() {
		add_action( 'rest_api_init', [ $this, 'register_rest_endpoint' ] );
	}

	public function register_rest_endpoint() {

		register_rest_route(
			self::API_NAMESPACE,
			'/' . self::ENDPOINT_RUN . '/(?P<migration>[\w|-]+)',
			[
				'methods' => 'PUT',
				'callback' => [ $this, 'run_migration' ],
				'permission_callback' => [ $this, 'check_rest_permissions' ],
				'show_in_index' => false,
			]
		);

	}

	/**
	 * @param \WP_REST_Request $request The incoming request object
	 *
	 * @return bool|\WP_Error
	 */
	public function check_rest_permissions( $request ) {

		$body = json_decode( $request->get_body(), true );

		if (
			! defined( 'WP_DUMB_MIGRATION_SECRET' ) ||
			! isset( $body['secret'] ) ||
			! hash_equals( WP_DUMB_MIGRATION_SECRET, $body['secret'] )
		) {
			return new \WP_Error( 'no-secret', __( 'Secret must be defined and passed to the request for the migration to run' ) );
		}

		return true;

	}
}
