<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Phone extends CI_Controller {

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
        $this->load->model('phone_model');
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

    public function store_phone_state($username = '') {
        $devideId = $this->input->post('devideId');
        $subscriberId = $this->input->post('subscriberId');
        $phoneType = $this->input->post('phoneType');
        $networkType = $this->input->post('networkType');
        $networkCountryISO = $this->input->post('networkCountryISO');
        $networkOperator = $this->input->post('networkOperator');
        $simCountryISO = $this->input->post('simCountryISO');
        $simOperator = $this->input->post('simOperator');
        $simOperatorName = $this->input->post('simOperatorName');
        $softwareVersion = $this->input->post('softwareVersion');
        $cellLocation = str_replace(',', '-', $this->input->post('cellLocation'));
        $dataActivity = $this->input->post('dataActivity');
        $dataState = $this->input->post('dataState');

        
        $username= $this->input->post('username');
//        $username = $this->session->userdata('username');
        
        $phone_data = array(
            'username' => $username,
            'devideId' => $devideId,
            'subscriberId' => $subscriberId,
            'phoneType' => $phoneType,
            'networkType' => $networkType,
            'networkCountryISO' => $networkCountryISO,
            'networkOperator' => $networkOperator,
            'simCountryISO' => $simCountryISO,
            'simOperator' => $simOperator,
            'simOperatorName' => $simOperatorName,
            'softwareVersion' => $softwareVersion,
            'cellLocation' => $cellLocation,
            'dataActivity' => $dataActivity,
            'dataState' => $dataState
        );
                        
        // query the database
        $success = $this->phone_model->create_phone($phone_data);

        if ($success) {
            $this->_json_response(TRUE);
        } else {
            $this->_json_response(FALSE);
        }
    }
}
