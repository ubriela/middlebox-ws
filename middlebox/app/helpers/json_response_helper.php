<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Generates a universally unique identifier (uuid)
 *
 * @access	public
 * @return	string
 */

if ( ! function_exists('json_response')){

	function json_response($that, $status, $msg){
		$that->output->set_content_type('application/json');
		$that->output->set_output(json_encode(array('status' => $status, "msg"=> $msg)));
	}
}

/* End of file json_response_helper.php */
/* Location: ./application/helpers/json_response_helper.php */
