<?php
/**
 * Displays an admin message.
 */

abstract class Admin_Message {

	private $message;

	/**
	 * Make a new message.
	 *
	 * @param string $message The message to be displayed. Will be escaped.
	 */
	public function __construct( $message ) {
		$this->message = esc_html( $message );
	}

	/**
	 * Show the message.
	 */
	public function show() {
		add_action( 'all_admin_notices', array( $this, 'get_output' ) );
	}

	/**
	 * Print the message.
	 */
	public function get_output() {
		$class = $this->get_class();
		echo '<div class="' . $class . '"><p>' . $this->message . '</p></div>';
	}

	/**
	 * Get the class of the message.
	 *
	 * @return string The class.
	 */
	abstract public function get_class();

}