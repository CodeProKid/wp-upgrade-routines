<?php

namespace WPDumb\Test;


use WPDumb\Migration;

class Plugin extends Migration {

	public function __construct() {
		parent::__construct( [
			'name' => 'Plugin Test',
			'version' => '1.1.0',
			'type' => 'plugin',
			'plugin_slug' => 'dfm-transients/dfm-transients.php',
			'blocking' => false,
			'runner' => 'sync',
		] );
	}

	public function up( $batch = 1 ) {
		update_option( 'wp_dumb_plugin_test', 'test value' );
	}

	public function down( $batch = 1 ) {
		delete_option( 'wp_dumb_plugin_test' );
	}
}
