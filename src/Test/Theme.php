<?php
/**
 * Created by PhpStorm.
 * User: ryankanner
 * Date: 10/1/18
 * Time: 2:31 PM
 */

namespace WPDumb\Test;


use WPDumb\Migration;

class Theme extends Migration {

	public function __construct( $config ) {
		parent::__construct( [
			'name' => 'Theme Test',
			'type' => 'theme',
			'theme_name' => 'wp-mason',
			'version' => 'any',
			'runner' => 'async',
		] );
	}

	public function up( $batch = 0 ) {
		wp_cache_flush();
	}

	public function down( $batch = 0 ) {}
}
