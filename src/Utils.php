<?php
/**
 * Created by PhpStorm.
 * User: ryankanner
 * Date: 9/17/18
 * Time: 11:09 AM
 */

namespace WPDumb;


class Utils {

	public static function get_batch_num( $migration_name ) {
		return absint( get_option( 'wp_dumb_migration_batch_' . sanitize_key( $migration_name ), 1 ) );
	}

	public static function finish_migration( $migration_name ) {
		delete_option( 'wp_dumb_migration_batch_' . sanitize_key( $migration_name ) );
	}

	public static function inc_batch_num( $migration_name ) {
		$current_batch = self::get_batch_num( $migration_name );
		update_option( 'wp_dumb_migration_batch_' . sanitize_key( $migration_name ), $current_batch + 1, false );
	}

}
