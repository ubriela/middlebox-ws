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
        
        $result_data = array(
            'username' => $username,
            'exp_type' => $exp_type,
            'result' => $result,
            'metadata' => $metadata
        );
                        
        // query the database
        $success = $this->result_model->store_result($result_data);

        if ($success) {
            $this->_json_response(TRUE);
        } else {
            $this->_json_response(FALSE);
        }
    }
}
