<?php

namespace WPDumb;

function register_migration( $migration ) {
	global $wp_dumb_migration_factory;

	$wp_dumb_migration_factory->register( $migration );
}

function unregister_migration( $migration ) {
	global $wp_dumb_migration_factory;

	$wp_dumb_migration_factory->unregister( $migration );
}

function get_migrations() {
	global $wp_dumb_migration_factory;
	return $wp_dumb_migration_factory;
}

function migration_has_run( $migration_name ) {}

function migration_is_running( $migration_name ) {}
