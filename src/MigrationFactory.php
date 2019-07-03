<?php

namespace WPDumb;


class MigrationFactory {

	private $migrations = [];

	public function __construct() {
		add_action( 'migrations_init', [ $this, '_register_migrations' ], 999 );
	}

	private function hash_object( $migration ) {
		return spl_object_hash( $migration );
	}

	public function register( $migration ) {
		if ( $migration instanceof Migration ) {
			$this->migrations[ $this->hash_object( $migration ) ] = $migration;
		} else {
			$this->migrations[ $migration ] = new $migration();
		}

	}

	public function unregister( $migration ) {
		if ( $migration instanceof Migration ) {
			unset( $this->migrations[ $this->hash_object( $migration ) ] );
		} else {
			unset( $this->migrations[ $migration ] );
		}
	}

	public function _register_migrations() {
		global $wp_dumb_registered_migrations;

		if ( ! empty( $this->migrations ) ) {
			foreach ( $this->migrations as $migration ) {
				$settings = $migration::$settings;
				$wp_dumb_registered_migrations[ $settings->name ] = $settings;
				$wp_dumb_registered_migrations[ 'obj' ] = $migration;
			}
		}
	}

}
