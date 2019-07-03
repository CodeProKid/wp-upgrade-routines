<?php

namespace WPDumb;


abstract class Migration {

	public static $settings = [];

	public function __construct( $config ) {
		self::$settings = $config;
	}

	abstract public function up( $batch = 0 );

	abstract public function down( $batch = 0 );

	protected function get_batch_size() {
		$default = ! empty( self::$settings['batch_size'] ) ? absint( self::$settings['batch_size'] ) : 100;
		/**
		 * @TODO: Add util for dynamically figuring out the batch size
		 */
		return $default;
	}

	protected function get_offset( $batch ) {
		return $this->get_batch_size() * $batch;
	}

}
