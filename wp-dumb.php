<?php
/**
 * Plugin Name:     WP Data Upgrade Migrations, Bro
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PHP Library to facilitate data migrations
 * Author:          Ryan Kanner, Digital First Media
 * Author URI:      YOUR SITE HERE
 * Text Domain:     wpdumb
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         WPDumb
 */

// ensure the wp environment is loaded properly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPDumb' ) ) {

	class WPDumb {

		/**
		 * Stores the instance of the WPDumb class
		 *
		 * @var Object $instance
		 * @access private
		 */
		private static $instance;

		/**
		 * Retrieves the instance of the WPDumb class
		 *
		 * @access public
		 * @return Object|WPDumb
		 * @throws exception
		 */
		public static function instance() {

			/**
			 * Make sure we are only instantiating the class once
			 */
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPDumb ) ) {
				self::$instance = new WPDumb();
				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->run();
				self::$instance->actions_filters();
			}

			/**
			 * Action that fires after we are done setting things up in the plugin. Extensions of
			 * this plugin should instantiate themselves on this hook to make sure the framework
			 * is available before they do anything.
			 *
			 * @param WPDumb $instance Instance of the current WPDumb class
			 */
			do_action( 'wp_dumb_init', self::$instance );

			return self::$instance;

		}

		/**
		 * Sets up the constants for the plugin to use
		 *
		 * @access private
		 * @return void
		 */
		private function setup_constants() {

			// Plugin version.
			if ( ! defined( 'WP_DUMB_VERSION' ) ) {
				define( 'WP_DUMB_VERSION', '1.0.0' );
			}

			// Plugin Folder Path.
			if ( ! defined( 'WP_DUMB_PLUGIN_DIR' ) ) {
				define( 'WP_DUMB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'WP_DUMB_PLUGIN_URL' ) ) {
				define( 'WP_DUMB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File.
			if ( ! defined( 'WP_DUMB_PLUGIN_FILE' ) ) {
				define( 'WP_DUMB_PLUGIN_FILE', __FILE__ );
			}

		}

		/**
		 * Load the autoloaded files as well as the access functions
		 *
		 * @access private
		 * @return void
		 * @throws Exception
		 */
		private function includes() {

			if ( file_exists( WP_DUMB_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
				require_once( WP_DUMB_PLUGIN_DIR . 'vendor/autoload.php' );
			} else {
				throw new Exception( __( 'Could not find autoloader file to include all files' ) );
			}

			/**
			 * Require non-autoloaded files
			 */
			require_once( WP_DUMB_PLUGIN_DIR . 'template-tags.php' );

		}

		/**
		 * Instantiate the main classes we need for the plugin
		 *
		 * @access private
		 * @return void
		 */
		private function run() {

			$_GLOBALS['$wp_dumb_upgrade_factory'] = new \WPDumb\MigrationFactory();

			add_action( 'init', function() {
				$checker_obj = new \WPDumb\Execute\Checker();
				$checker_obj->run();
			} );

			add_action( 'wp_dumb_upgrade_downgrade_happened', function() {
				$scheduler_obj = new \WPDumb\Execute\Scheduler();
				$scheduler_obj->setup();
			} );

			if ( defined( 'WP_CLI' ) && true === WP_CLI ) {
				WP_CLI::add_command( 'migration', '\WPDumb\CLI' );
			}

		}

		public function actions_filters() {
			add_action( 'init', function() {
				do_action( 'migrations_init' );
			}, 999 );

			/**
			 * NOTE: This is just for testing purposes, will need to remove this.
			 */
			add_action( 'migrations_init', function() {
				\WPDumb\register_migration( '\WPDumb\Test\Plugin' );
				\WPDumb\register_migration( '\WPDumb\Test\Project' );
				\WPDumb\register_migration( '\WPDumb\Test\Theme' );
			} );
		}

	}

}

/**
 * Function to instantiate the WPDumb class
 *
 * @return Object|WPDumb Instance of the WPDumb object
 * @access public
 * @throws exception
 */
function wp_dumb_init() {

	/**
	 * Returns an instance of the WPDumb class
	 */
	return \WPDumb::instance();

}

/**
 * Setup the class early within the after_setup_theme class so the access functions are available
 * for other plugins to use.
 */
add_action( 'plugins_loaded', 'wp_dumb_init', 1 );
