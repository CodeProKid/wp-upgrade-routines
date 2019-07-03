<?php

namespace WPDumb;


class Invoke {

	private $settings = [];

	private $obj;

	private $direction = '';

	public function __construct( $settings, $direction ) {
		$this->settings = $settings;
		$this->direction = $direction;

		if ( ! empty( $settings['obj'] ) ) {
			$this->obj = $settings['obj'];
		} else {
			// Throw error
		}

		$runner_type = ( ! empty( $this->settings['runner'] ) ) ? $this->settings['runner'] : 'async';
		if ( method_exists( $this, $runner_type ) ) {
			$this->$runner_type();
		} else {
			//Throw error
		}

	}

	private function async() {

	}

	private function sync() {
		new Runner( $this->obj, $this->settings['name'], $this->direction );
	}

	private function cron() {
		wp_schedule_single_event( time(), 'wp_dumb_run_migration_cron', [ $this->obj, $this->direction ] );
	}

}
