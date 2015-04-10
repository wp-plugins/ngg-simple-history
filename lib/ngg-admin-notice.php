<?php
/**
 * Displays an admin notice message.
 */

require_once( 'ngg-admin-message.php' );

class Admin_Notice extends Admin_Message {

	/**
	 * Get the class of the message.
	 *
	 * @return string The class.
	 */
	public function get_class() {
		return 'updated';
	}
}