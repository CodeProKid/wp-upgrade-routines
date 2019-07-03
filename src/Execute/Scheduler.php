<?php
namespace WPDumb\Execute;


use function WPDumb\get_migrations;

class Scheduler {

	public function setup() {
		add_action( 'wp_dumb_run_upgrade', [ $this, 'run_upgrades' ] );
		add_action( 'wp_dumb_run_downgrade', [ $this, 'run_downgrades' ] );
	}

	public function run_upgrades( $upgrades ) {
		foreach ( $upgrades as $type => $upgrade ) {
			$migrations = $this->get_migration_by_type( $type );
			if ( ! empty( $migrations ) && is_array( $migrations ) ) {
				foreach ( $migrations as $migration ) {

				}
			}
		}
	}

	public function run_downgrades( $downgrades ) {

	}

	private function find_project_updates( $type, $version ) {

	}

	private function get_migration_by_type( $type ) {
		$migrations = get_migrations();
		if ( empty( $migrations ) || ! is_array( $migrations ) ) {
			return [];
		}

		return wp_list_filter( $migrations, [ 'type', $type ] );

	}

}
