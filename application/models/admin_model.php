<?php

/**
 * Admin model
 * @author Nguyen
 *
 */
class Admin_model extends CI_Model {
	public function __construct() {
		$this->load->database();
	}
	
	private function _startsWith($haystack, $needle)
	{
		return $needle === "" || strpos($haystack, $needle) === 0;
	}
	
	private function _endsWith($haystack, $needle)
	{
		return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
	}
	
	
	/**
	 * Backup databases (download directly to the client)
	 */
	public function backup_database() {
		// TODO
	}
}