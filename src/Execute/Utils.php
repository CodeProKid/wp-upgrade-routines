<?php

namespace WPDumb\Execute;


class Utils {

	private static $stored_versions = [];

	private static $current_versions = [];

	public static function get_project_version() {

		if ( empty( self::$current_versions['project'] ) ) {
			$filepath = false;

			if ( false !== $custom_file_path = apply_filters( 'wp_dumb_custom_project_version_path', false ) ) {
				$filepath = $custom_file_path;
			} elseif ( file_exists( trailingslashit( ABSPATH ) . 'package.json' ) ) {
				$filepath = trailingslashit( ABSPATH ) . 'composer.json';
			} elseif ( file_exists( trailingslashit(WP_CONTENT_DIR ) . 'package.json' ) ) {
				$filepath = trailingslashit( WP_CONTENT_DIR ) . 'package.json';
			}

			if ( ! empty( $filepath ) && is_readable( $filepath ) ) {
				if ( false !== $data = file_get_contents( $filepath ) ) {
					$data = json_decode( $data, true );
					if ( false !== $data && ! empty( $data['version'] ) ) {
						self::$current_versions['project'] = $data['version'];
					}
				}
			}
		}

		return ( isset( self::$current_versions['project'] ) ) ? self::$current_versions['project'] : false;

	}

	public static function get_parent_theme_version() {
		return self::get_theme_version( get_template() );
	}

	public static function get_child_theme_version() {

		$child_theme = get_stylesheet();
		if ( get_template() === $child_theme ) {
			return false;
		}

		return self::get_theme_version( $child_theme );

	}

	private static function get_theme_version( $theme_name ) {

		if ( empty( self::$current_versions['themes'][ $theme_name ] ) ) {
			$data = wp_get_theme( $theme_name );
			self::$current_versions['themes'][ $theme_name ] = ( isset( $data['version'] ) ) ? $data['version'] : false;
		}

		return self::$current_versions['themes'][ $theme_name ];

	}

	public static function get_plugin_version( $plugin_slug ) {

		if ( empty( self::$current_versions['plugins'][ $plugin_slug ] ) ) {
			$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . $plugin_slug;
			if ( ! file_exists( $plugin_path ) ) {
				return false;
			}

			$data = get_plugin_data( $plugin_path );
			self::$current_versions['plugins'][ $plugin_slug ] = ( isset( $data['Version'] ) ) ? $data['Version'] : '';
		}

		return ( isset( self::$current_versions['plugins'][ $plugin_slug ] ) ) ? self::$current_versions['plugins'][ $plugin_slug ] : false;

	}

	public static function version_compare( $option_key, $current_version, $slug = '' ) {
		/**
		 * Remove code below. Keeping here until we get things working a bit better, ended up moving
		 * all of this into the get_stored_version() method
		 */
//		$stored_version = get_option( $option_key );
//		$stored_version = self::get_stored_version( $option_key, $current_version, $slug );
//
//		if ( empty( $stored_version ) ) {
//
//			if ( empty( $slug ) ) {
//				$data = $current_version;
//			} else {
//				$data = [ $slug => $current_version ];
//			}
//
//			update_option( $option_key, $data, true );
//
//			// Nothing was stored to compare to, so the version essentially hasn't changed
//			return 0;
//
//		}
//
//		if ( ! empty( $slug ) ) {
//			if ( ! empty( $stored_version[ $slug ] ) ) {
//				$stored_version = $stored_version[ $slug ];
//			} else {
//				$data = $stored_version[ $slug ] = $current_version;
//				update_option( $option_key, $data, true );
//				return 0;
//			}
//		}

		$stored_version = self::get_stored_version( $option_key, $current_version, $slug );
		return version_compare( $stored_version, $current_version );

	}

	public static function get_stored_version( $option_key, $current_version, $slug = '' ) {

		if ( ! empty( $slug ) ) {
			$cached = ( isset( self::$stored_versions[ $option_key ][ $slug ] ) ) ? self::$stored_versions[ $option_key ][ $slug ] : false;
		} else {
			$cached = ( isset( self::$stored_versions[ $option_key ] ) ) ? self::$stored_versions[ $option_key ] : false;
		}

		if ( false === $cached ) {
			$stored_version = get_option( $option_key );
			if ( empty( $stored_version ) ) {
				if ( empty( $slug ) ) {
					self::$stored_versions[ $option_key ] = $current_version;
					$data = $current_version;
				} else {
					self::$stored_versions[ $option_key ][ $slug ] = $current_version;
					$data = [ $slug => $current_version ];
				}
				update_option( $option_key, $data, true );
				return $current_version;
			} else {
				if ( ! empty( $slug ) ) {
					if ( empty( self::$stored_versions[ $option_key ][ $slug ] ) ) {
						self::$stored_versions[ $option_key ][ $slug ] = $current_version;
						update_option( $option_key, self::$stored_versions[ $option_key ] );
					} else {
						$cached = self::$stored_versions[ $option_key ][ $slug ];
					}
				} else {
					$cached = self::$stored_versions[ $option_key ];
				}
			}
		}

		return $cached;

	}

}
