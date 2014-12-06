<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Result extends CI_Controller {

    /**
     * Constructor
     *
     * Loads user model and libraries. They are available for all methods
     *
     * @access	public
     * @return	void
     */
    public function __construct() {
        parent::__construct();

        $this->load->database();

        // Load user model
        $this->load->model('result_model');
        $this->load->helper('cookie');

        // Load user agent library
        $this->load->helper('json_response');
        $this->load->helper('uuid_helper');
    }
    /**
     * Default function executed when [base_url]/index.php/user is requested
     *
     * @access	public
     * @return	void
     */
    public function index() {
        $this->login();
    }

    public function store_result($username = '') {
        
        $username= $this->input->post('username');
        $exp_type = $this->input->post('exp_type');
        $result = $this->input->post('result');
        $metadata = str_replace(',', '-', $this->input->post('metadata'));
        $cellular = $this->input->post('cellular');
        $address = $this->input->post('address');
        
        $result_data = array(
            "resid" => "",
            'username' => $username,
            'exp_type' => $exp_type,
            'result' => $result,
            'metadata' => $metadata,
            'cellular' => $cellular,
            'address' => $address
        );
                        
        // query the database
        $success = $this->result_model->store_result($result_data);

        if ($success) {
            $this->_json_response(TRUE);
        } else {
            $this->_json_response(FALSE);
        }
    }
    
        /**
     * Sends the data in JSON format
     *
     * Used when the respond is for mobile application or AJAX requests
     *
     * @access	private
     * @param	object	$data contains the object to be sent as JSON
     * @return	void
     */
    private function _json_response($data) {

        $this->output->set_content_type('application/json');

        if (!empty($data)) {
            $this->output->set_output(json_encode(array('status' => 'success', "msg" => $data)));
        } else {
            $this->output->set_output(json_encode(array('status' => 'error', "msg" => '0')));
        }
    }
}
