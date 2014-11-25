<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * MediaQ User Class
 *
 * This class provides methods to interact with the user
 *
 * @package
 * @subpackage
 * @category
 * @author	
 * @link
 */
class User extends CI_Controller {

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
        $this->load->model('user_model');
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

    /**
     * Log in
     *
     * One of the two urls requested
     *
     * 1. [base_url]/index.php/user
     * 2. [base_url]/index.php/user/login
     *
     * @access	public
     * @param	string $username the user's username (can be used after create account)
     * @return	void
     */
    public function login($username = '') {
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Validation rules are in application/config/form_validation.php
        if ($this->form_validation->run('login') === FALSE) {
            $this->_json_response(FALSE);
        } else {
            $username = $this->input->post('username');

            // Log 'Login'
            log_message('info', date("Y-m-d H:i:s") . "\tUser logged in (session created): " . $username);

            $this->_json_response($this->session->userdata('userid'));
        }
    }
    
    /**
     * Get user info
     *
     *
     * [base_url]/index.php/user/get_userinfo
     *
     * @access	public
     * @param	void
     * @return	user info : username, last name, first name, phone, email
     */
    public function get_userinfo() {
    	if(!$this->session->userdata('signed_in')){
    		$this->_json_response(FALSE);
    		return;
    	}
    	// get user info
    	$this->_json_response($this->user_model->get_userinfo());
    }
    
    /**
     * update user info
     *
     *
     * [base_url]/index.php/user/update_userinfo
     *
     * @access	public
     * @param	lastname, firstname, phone_number,
     * @return	void
     */
    public function update_userinfo() {
    	if(!$this->session->userdata('signed_in')){
    		$this->_json_response(FALSE);
    		return;
    	}
    	// get user info
    	$firstname = $this->input->post('firstname');
    	$lastname = $this->input->post('lastname');
    	$email = $this->input->post('email');
    	 
    	$this->_json_response($this->user_model->update_userinfo($firstname,$lastname,$email));
    }
    
    /**
     * Update channel id
     *
     * One of the two urls requested
     *
     * 1. [base_url]/index.php/user
     * 2. [base_url]/index.php/user/update_channelid
     *
     * @access	public
     * @param	string $username the user's username (can be used after create account)
     * @return	void
     */
    public function update_channelid() {
    	$this->load->helper('form');
    	$this->load->library('form_validation');
    
    	if(!$this->session->userdata('signed_in')){
    		$this->_json_response(FALSE);
    		return;
    	}
//     	if ($this->form_validation->run('update_channelid') == FALSE){
//     		$this->_json_response(FALSE);
//     	}else{
    		$channelid = $this->input->post('channelid');
    		$this->user_model->update_channelid($channelid);
    		$this->_json_response($this->session->userdata('userid'));
//     	}
    }

    /**
     * Callback validation to Check password with DB
     *
     * If authentication is successful create a new session and fill it up
     *
     * @access	private (URL inaccessible)
     * @param	string $password the user's password
     * @return	true if the user is authenticated, otherwise false
     */
    function _authenticate_user($password) {
        // Field validation succeeded.  Validate against database

        $username = $this->input->post('username');

        // Query the database
        $row = $this->user_model->get_user($username);
                
        if ($row) {
            $user_id = $row->userid;
            $username = $row->username;
            $avatar = $row->avatar;
            $fullname = $row->firstname . ' ' . $row->lastname;
            $db_password = $row->password;
            $salt = $row->salt;
            $password = hash('sha512', $password . $salt);
            // Passwords must match
            if ($db_password == $password) {
            // Create a session
                $sess_array = array(
                    'userid' => $user_id,
                    'username' => $username,
                    'avatar' => $avatar,
                    'fullname' => $fullname,
                    'signed_in' => True
                );
                    
                log_message('debug', var_export($sess_array, True));

                $this->session->set_userdata($sess_array);
                return TRUE;
            }
            // Validation failed
            else {
            // Set failed validation message
                $this->form_validation->set_message('_authenticate_user', 'Invalid username or password');
                return FALSE;
            }
            
        } else {
            // Set failed validation message
            $this->form_validation->set_message('_authenticate_user', 'Invalid username or password');
            return FALSE;
        }
    }

    /**
     * Registers a new user. Provides the form to register.
     *
     * When form submitted it checks if username or email already exists
     *
     * Upon successful registration, user is redirected to login page
     *
     * @access	public
     * @return	void
     */
    public function register() {
        $this->load->helper('form');
        $this->load->library('form_validation');

        // Validation rules are in application/config/form_validation.php
        if ($this->form_validation->run('account') === FALSE) {
            $this->_json_response(FALSE);
        } else {

                    
            log_message("error", "ok");
        
            // Send welcome email
            //$this->_send_welcome_email_confirmation();

            $username = $this->input->post('username');

            // Log 'Create Account'
            log_message('info', date("Y-m-d H:i:s") . "\tCreated a new account with username: " . $username);

            $this->_json_response(TRUE);
        }
    }

    /**
     * Callback validation to check the DB if the username already exists
     *
     * Sets error message for username if is not unique
     *
     * @access	private (URL inaccessible)
     * @param	string $password the user's password
     * @return	true if the username and email is unique, otherwise false
     */
    function _user_exists($password) {
        $username = $this->input->post('username');
        $email = $this->input->post('email');
                
        // query the database
        $user_exists = $this->user_model->user_exists($username);
        $email_exists = $this->user_model->user_exists($email);

        if ($user_exists && $email_exists) {
            // Set failed validation message
            $this->form_validation->set_message('_username_exists', 'Username already exists');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    // --------------------------------------------------------------------

    /**
     * Callback to create a new user
     *
     * If form validation was successful the user is created, otherwise error messages are shown
     *
     * @access	private (URL inaccessible)
     * @param	string $password the user's password
     * @return	true if the account is created, otherwise false
     */
    function _create_user($password) {
        $this->load->helper('form');
        // Load the uuid helper
        $this->load->helper('uuid');

        $username = $this->input->post('username');
        $email = $this->input->post('email');
        $channelid = $this->input->post('channelid');

        // query the database
        $success = $this->user_model->create_user($username, $password, $email, $channelid);

        if ($success) {
            return TRUE;
        } else {
            // Set failed validation message
            $this->form_validation->set_message('_create_user', 'Username already taken');
            return FALSE;
        }
    }

    /**
     * Sends welcome email to new created user
     *
     * @access	private
     * @return	void
     */
    private function _send_welcome_email_confirmation() {

        $this->load->library('email');

        $username = $this->input->post('username');
        $to_email = $this->input->post('email');
        $from_email = EMAIL;
        $from_name = APP_NAME;
        $subject = 'Welcome to ' . APP_NAME;

        $data['username'] = $username;
        $data['title'] = APP_NAME;

        // Load welcome email template
        $message = $this->load->view('email_templates/welcome_email', $data, TRUE);

        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from($from_email, $from_name);
        $this->email->to($to_email);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->send();

        log_message('info', $this->email->print_debugger());
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

    /**
     * Forgot Password
     *
     * @access	public
     * @return	void
     */
    public function forgot_password_submit() {
        $this->load->helper('form');
        $this->load->library('form_validation');
        if (!$this->session->userdata('signed_in')) {
            if ($this->form_validation->run('forgot_password') === FALSE) {
                json_response($this, "error", validation_errors());
            } else {
                json_response($this, "success", "<strong>You will receive an e-mail shortly.</strong>");
            }
        } else {
            json_response($this, "error", "Already logged in.");
        }
    }

    /**
     * Forgot Password Form
     *
     * @access	public
     * @return	void
     */
    public function forgot_password() {
        $this->load->library('form_validation');

        $this->_json_response(FALSE);
    }

    /**
     * Callback validation to check if email exists
     *
     * @access	private (URL inaccessible)
     * @param	string $email the user's email
     * @return	true if the user's email exists, otherwise false
     */
    function _no_email_exists($email) {
        // query the database
        $exists = $this->user_model->email_exists($email);

        if ($exists) {
            return TRUE;
        } else {
            // Set failed validation message
            $this->form_validation->set_message('_no_email_exists', 'Email does not exist');
            return FALSE;
        }
    }

    /**
     * Callback validation to generate reset password
     *
     * @access	private (URL inaccessible)
     * @param	string $email the user's email
     * @return	true if the reset password was generated successfully, otherwise false
     */
    function _generate_reset_password($email) {
        $this->load->helper('string');

        //  Generate random alpha-numeric string with lower and uppercase characters
        $rand_string = random_string('alnum', 6);

        if ($this->user_model->set_reset_password($email, $rand_string)) {
            $this->_send_reset_password_email($email, $rand_string);
            return TRUE;
        } else {
            // Set failed validation message
            $this->form_validation->set_message('_generate_reset_password', 'Reset unsuccessful. Please try again later');
            return FALSE;
        }
    }

    // --------------------------------------------------------------------

    /**
     * Send email to reset the forgotten password
     *
     * @access	private
     * @param	string 	$email the user's email
     * @param 	string	$reset_pass
     * @return	void
     */
    private function _send_reset_password_email($email, $reset_pass) {

        $this->load->library('email');

        $title = APP_NAME;

        $to_email = $email;
        $from_email = EMAIL;
        $from_name = APP_NAME;
        $subject = APP_NAME . ' Password Reset';

        $data['reset_pass'] = $reset_pass;

        // Load password reset email template
        $message = $this->load->view('email_templates/password_reset_email', $data, TRUE);

        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from($from_email, $from_name);
        $this->email->to($to_email);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->send();

        log_message('info', $this->email->print_debugger());
    }

    // --------------------------------------------------------------------

    /**
     * Provides form and ability for user to reset password given a correct $temp_pass
     *
     * @access	public
     * @param	string $temp_pass used to reset and identify user
     * @return	void
     */
    public function reset_password($temp_pass = FALSE) {

        $this->load->helper('form');
        $this->load->library('form_validation');

        if (!$temp_pass or $this->session->userdata('signed_in')) {
            redirect('home', 'refresh');
        } else {
            $user = $this->user_model->check_reset_password($temp_pass);

            if ($user) {
                if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                    $this->_json_response(FALSE);
                } else {
                    if ($this->form_validation->run('reset_password') === FALSE) {
                        json_response($this, "error", validation_errors());
                    } else {
                        json_response($this, "success", "<strong>Your password has been successfully reset.</strong>");
                    }
                }
            } else {
                $this->_json_response(FALSE);
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Callback validation to reset password and clear temporary password
     *
     * @access	private
     * @param	string 	$password, user's new password
     * @return	void
     */
    function _reset_password($password) {

        //Get username from session (user will be logged in)
        $session_data = $this->session->userdata('reset_pass');

        $change_successful = $this->user_model->change_password($password, $session_data);

        if (!$change_successful) {
            $this->form_validation->set_message('_reset_password', 'Password not changed. Please try again later.');
        } else {
            $this->user_model->set_reset_password($session_data['UserId'], NULL);
            $this->session->unset_userdata('reset_pass');
        }

        return $change_successful;
    }
    public function getAllinfo(){
        $flag = $this->user_model->get_Alluserinfo();
        $this->_json_response($flag);
    }
    public function checkusername(){
        $username = $_POST['username'];
        $user_exists = $this->user_model->user_exists($username);
        if(!$user_exists){
            $this->_json_response(true);
        }else{
            $this->_json_response(false);
        }
    }
    

}
