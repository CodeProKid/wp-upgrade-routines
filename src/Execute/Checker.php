<?php

namespace WPDumb\Execute;


class Checker {

	const PROJECT_OPTION = 'wp_dumb_project_version';

	const THEME_OPTION = 'wp_dumb_theme_option';

	const PLUGIN_OPTION = 'wp_dumb_plugin_option';

	private $transient_timeout = 0;

	private $upgrades = [];

	private $downgrades = [];

	public function __construct() {
		$this->transient_timeout = apply_filters( 'wp_dumb_defer_check_timeout', 15 * MINUTE_IN_SECONDS );
	}

	public function run() {
		$this->check_project_version();
		$this->check_theme_version();
		$this->check_plugin_versions();
		$this->process_changes();
	}

	public function check_project_version() {

		if ( true === apply_filters( 'wp_dumb_defer_project_version_check', false ) ) {
			if ( false === $this->check_transient( 'project' ) ) {
				return;
			}
		}

		$current_version = Utils::get_project_version();
		$diff = Utils::version_compare( self::PROJECT_OPTION, $current_version );

		$this->check_changes( $diff, 'project', $current_version );

	}

	public function check_theme_version() {

		if ( true === apply_filters( 'wp_dumb_defer_theme_version_check', false ) ) {
			if ( false === $this->check_transient( 'theme' ) ) {
				return;
			}
		}

		$parent_theme_current = Utils::get_parent_theme_version();
		$parent_theme_diff = Utils::version_compare( self::THEME_OPTION, $parent_theme_current, 'parent' );
		$this->check_changes( $parent_theme_diff, 'theme', $parent_theme_current, 'parent' );

		if ( false !== $child_theme_current = Utils::get_child_theme_version() ) {
			$child_theme_diff = Utils::version_compare( self::THEME_OPTION, $child_theme_current, 'child' );
			$this->check_changes( $child_theme_diff, 'theme', $child_theme_current, 'child' );
		}

	}

	public function check_plugin_versions() {

		if ( true === apply_filters( 'wp_dumb_defer_plugin_version_check', true ) ) {
			if ( false === $this->check_transient( 'plugin' ) ) {
				return;
			}
		}

		$plugins = get_option( 'active_plugins' );

		if ( empty( $plugins ) || ! is_array( $plugins ) ) {
			return;
		}

		foreach ( $plugins as $plugin ) {
			$current_plugin_version = Utils::get_plugin_version( $plugin );
			if ( false === $current_plugin_version ) {
				continue;
			}
			$plugin_diff = Utils::version_compare( self::PLUGIN_OPTION, $current_plugin_version );
			$this->check_changes( $plugin_diff, 'plugin', $current_plugin_version, $plugin );
		}

	}

	private function check_transient( $type ) {

		$key = 'wp_dumb_check_' . $type;

		if ( false !== get_transient( $key ) ) {
			return false;
		} else {
			set_transient( $key, 'no', $this->transient_timeout );
			return true;
		}

	}

	private function check_changes( $value, $type, $version, $key = '' ) {

		switch ( $value ) {
			case -1:
				if ( empty( $key ) ) {
					$this->downgrades[ $type ] = $version;
				} else {
					$this->downgrades[ $type ][ $key ] = $version;
				}
				break;
			case 1:
				if ( empty( $key ) ) {
					$this->upgrades[ $type ] = $version;
				} else {
					$this->upgrades[ $type ][ $key ] = $version;
				}
				break;
		}

	}

	private function process_changes() {

		if ( ! empty( $this->upgrades ) || ! empty( $this->downgrades ) ) {
			do_action( 'wp_dumb_upgrade_downgrade_happened', $this->upgrades, $this->downgrades );
		}

		if ( ! empty( $this->upgrades ) && is_array( $this->upgrades ) ) {
			do_action( 'wp_dumb_run_upgrade', $this->upgrades );
		}

		if ( ! empty( $this->downgrades ) && is_array( $this->downgrades ) ) {
			do_action( 'wp_dumb_run_downgrade', $this->downgrades );
		}

	}

}
