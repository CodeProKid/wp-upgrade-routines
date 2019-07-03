<?php

namespace WPDumb;


class Runner {

	public function __construct( $instance, $name, $type ) {
		$this->do_migration( $instance, $name, $type );
	}

	public function do_migration( $instance, $name, $type ) {
		$result = $instance->$type( Utils::get_batch_num( $name ) );
		if ( true === $result ) {
			Utils::finish_migration( $name );
		} else if ( true === is_int( $result ) ) {
			Utils::inc_batch_num( $name );
			/**
			 * @TODO replace $instance with entire settings array
			 */
			new Invoke( $instance, $type );
		}
	}

}
