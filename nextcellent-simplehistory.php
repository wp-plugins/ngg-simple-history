<?php
/**
 * --COPYRIGHT NOTICE------------------------------------------------------------------------------
 *
 * This file is part of NextCellent Simple History.
 *
 * NextCellent Simple History is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * NextCellent Simple History is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NextCellent Simple History.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ------------------------------------------------------------------------------------------------
 *
 * @wordpress-plugin
 * Plugin Name:     NextCellent Simple History
 * Plugin URI:      https://bitbucket.org/niknetniko/ngg-simplehistory
 * Description:     Adds NextCellent support for Simple History.
 * Version:         1.1.1
 * Author:          niknetniko
 * Text Domain:     ngg-simple-history
 * Domain Path:     /lang
 * License:         GPL-2.0+
 */

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
/**
 * Load support libraries
 */
require_once( __DIR__ . '/lib/ngg-admin-error.php' );
require_once( __DIR__ . '/lib/ngg-admin-notice.php' );

class NGG_Simple_History {

	private static $instance = null;
	private $plugin_path;
	private $plugin_url;

	/**
	 * Creates or returns an instance of this class.
	 */
	public static function get_instance() {
		// If an instance hasn't been created and set to $instance create an instance and set it to $instance.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Initializes the plugin by setting localization, hooks, filters, and administrative functions.
	 */
	private function __construct() {
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->plugin_url  = plugin_dir_url( __FILE__ );
		load_plugin_textdomain( 'ngg-simple-history', false, basename( dirname( __FILE__ ) ) . '/lang' );
		$this->run_plugin();
	}

	public function get_plugin_url() {
		return $this->plugin_url;
	}

	public function get_plugin_path() {
		return $this->plugin_path;
	}

	/**
	 * Check the PHP version.
	 *
	 * @return bool If the version is OK or not.
	 */
	private function check_php_version() {
		if ( version_compare( phpversion(), "5.3", "<" ) ) {
			$notice = new Admin_Error( printf( __( 'NGG History requires PHP 5.3 or later (you have version %s).',
				'ngg-simple-history' ), phpversion() ), 'error' );
			$notice->show();

			return false;
		} else {
			return true;
		}
	}

	/**
	 * Check whether Simple History is activated or not.
	 *
	 * @return bool True if it is.
	 */
	private function check_for_simple_history() {
		if ( is_plugin_active( 'simple-history/index.php' ) && class_exists( 'nggLoader' ) ) {
			return true;
		} else {
			$notice = new Admin_Error( __( 'NGG History requires SimpleHistory and NextCellent in order to work.',
				'ngg-simple-history' ) );
			$notice->show();

			return false;
		}
	}

	public function check_requisites() {
		return $this->check_php_version() && $this->check_for_simple_history();
	}

	/**
	 * Place code for your plugin's functionality here.
	 */
	private function run_plugin() {

		if ( $this->check_requisites() ) {
			add_filter( "simple_history/loggers_files", array( $this, "load_files" ) );
		}
	}

	/**
	 * Add our loggers.
	 *
	 * @access private
	 *
	 * @param $files array The already loaded files.
	 *
	 * @return array The result.
	 */
	public function load_files( $files ) {
		$files = array_merge( $files, $this->get_loggers() );

		return $files;
	}

	/**
	 * Get all our loggers.
	 *
	 * @return array The absolute paths to the loggers.
	 */
	private function get_loggers() {
		return array(
			__DIR__ . "/loggers/NextCellentLogger.php",
		);
	}
}

NGG_Simple_History::get_instance();