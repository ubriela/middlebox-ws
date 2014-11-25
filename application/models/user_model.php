<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class User_model extends CI_Model {
	
	/**
	 * Finds and returns the user details based on userid
	 *
	 * @access	public
	 * @param	string $username_or_email the user's username or email
	 * @return	the user details, otherwise FALSE
	 */
	public function get_userinfo() {
	
		$userid = $this->session->userdata('userid');
		
		$this->db->select('username, avatar, firstname, lastname, phone_number, email, created_date, userid');
		$this->db->from('users');
		$this->db->where('userid', $userid);
		$this->db->limit(1);
	
		$query = $this->db->get();
	
		if ($query->num_rows() == 1) {
			// Return the row
            $row = $query->result_array()[0];
            $row['numrequest'] = $this->get_num_taskrequests($userid);
            $row['numresponse'] = $this->get_num_taskresponses($userid);
			return $row;
		} else {
			return FALSE;
		}
	}
    public function get_Alluserinfo(){
        $this->db->select('username, avatar, firstname, lastname, phone_number, email, created_date, userid');
		$this->db->from('users');
		//$this->db->where('userid', $userid);
		$query = $this->db->get();
        if($query->num_rows()>0){
            $array = array();
            foreach ($query->result_array() as $row)
                {
                    $userid = $row['userid'];
                    $row['numrequest'] = $this->get_num_taskrequests($userid);
                    $row['numresponse'] = $this->get_num_taskresponses($userid);
                    $array[]=$row;
                }
            return $array;
        }else{
            return false;
        }
    }

	/**
	 * Update user info
	 *
     * @access	public
     * @return	true if is successfully set
     */
    public function update_userinfo($firstname, $lastname, $email){
    	$id = $this->session->userdata('userid');
   		$this->db->set('firstname',$firstname);
   		$this->db->set('lastname',$lastname);
   		$this->db->set('email',$email);
   		$this->db->where('userid',$id);
   		if ($this->db->update('users')){
   			return TRUE;
   		}else {
   			return FALSE;
   		}
    
    }
	
    /**
     * Finds and returns the user details based on the username or email
     *
     * @access	public
     * @param	string $username_or_email the user's username or email
     * @return	the user details, otherwise FALSE
     */
    public function get_user($username_or_email) {

        $this->db->select('userid, username, avatar, firstname, lastname, password, salt, channelid');
        $this->db->from('users');
        $this->db->where('username', $username_or_email);
        $this->db->or_where('email', $username_or_email);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            // Return the row
            return $query->row();
        } else {
            return FALSE;
        }
    }

    /**
     * Checks if a username is in DB
     *
     * @access	public
     * @param	string $username_or_email
     * @return	TRUE if user already exists, otherwise FALSE
     */
    public function user_exists($username_or_email) {
        $this->db->where('username', $username_or_email);
        $this->db->or_where('email', $username_or_email);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Creates a new user
     *
     * @access	public
     * @param	string $username the user's username
     * @param	string $password the user's password
     * @param	string $email the user's email
     * @param 	string $channelid channelid for push notification service
     * @return	TRUE if user creation was successfull, otherwise FALSE
     */
    public function create_user($username, $password, $email, $channelid = FALSE) {

        // Generate unique id from application/helpers/uuid_helper.php
        $uuid = gen_uuid();

        // Create a random salt
        $random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));

        // Create salted password (Careful not to over season)
        $password = hash('sha512', $password . $random_salt);

        // Get the date
        $date_now = date("Y-m-d H:i:s");
        
        //set NULL to channelid
        if ($channelid === FALSE) {
        	$channelid = NULL;
        }

        $user_data = array(
            'userid' => $uuid,
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'created_date' => $date_now,
            'last_login_date' => $date_now,
            'last_activity_date' => $date_now,
            'last_password_change_date' => $date_now,
            'salt' => $random_salt,
        	'channelid' => $channelid
        );


        $success = TRUE;

        // Transaction
        $this->db->trans_start();
        if (!$this->db->insert('users', $user_data))
            $success = FALSE;
        $this->db->trans_complete();

        return $success;
    }

    /**
     * Set user as active
     *
     * @access	public
     * @param	array $email_or_userid with either userid or email
     * @return	TRUE if is successfully set
     */
    public function activate_account($email_or_userid) {

        $user_data = array(
            'isactive' => 1
        );

        $this->db->where('userid', $email_or_userid);
        $this->db->or_where('email', $email_or_userid);

        $this->db->update('users', $user_data);

        return ($this->db->affected_rows() > 0);
    }

    /**
     * Check whether an user is active or not
     *
     * @access	public
     * @param	array $email_or_userid with either userid or email
     * @return	TRUE if is successfully set
     */
    public function is_active($email_or_userid) {

        $this->db->select('isactive');
        $this->db->from('users');
        $this->db->where('userid', $email_or_userid);
        $this->db->or_where('email', $email_or_userid);

        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            $query = $query->row();
            return (int) $query->isactive;
        } else {
            return 0;
        }
    }

    /**
     * Checks if a email is in DB
     *
     * @access	public
     * @param	string $email the user's email
     * @return	TRUE if email already exists, otherwise FALSE
     */
    public function email_exists($email) {

        if ($this->get_user($email)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Set new temporary password to reset user's password
     *
     * @access	public
     * @param	array $email_or_userid with either userid or email
     * @param 	string $reset_pass user's temporary reset password
     * @return	TRUE if reset password is successfully set
     */
    public function set_reset_password($email_or_userid, $reset_pass) {

        $user_data = array(
            'reset_password' => $reset_pass
        );

        $this->db->where('userid', $email_or_userid);
        $this->db->or_where('email', $email_or_userid);

        $this->db->update('users', $user_data);

        return ($this->db->affected_rows() > 0);
    }

    // --------------------------------------------------------------------

    /**
     * Check user's temporary reset password
     *
     * @access	public
     * @param 	string $password user's temporary reset password
     * @return	return user is password exists, FALSE otherwise
     */
    public function check_reset_password($password) {
        $this->db->select('userid');
        $this->db->from('users');
        $this->db->where('reset_password', $password);
        $this->db->limit(1);

        $query = $this->db->get();


        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return FALSE;
        }
    }

    // --------------------------------------------------------------------

    /**
     * Change user's password
     *
     * @access	public
     * @param	string $password the user's password
     * @param 	session data array, containing user info
     * @return	TRUE if password is successfully changed, FALSE otherwise
     */
    public function change_password($password, $session_data) {
        $userid = $session_data['userid'];

        // Create a random salt
        $random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));

        // Create salted password (Careful not to over season)
        $password = hash('sha512', $password . $random_salt);

        $user_data = array(
            'password' => $password,
            'salt' => $random_salt
        );

        $this->db->where("userid", $userid);
        $this->db->update('users', $user_data);

        return ($this->db->affected_rows() > 0);
    }

    // --------------------------------------------------------------------

    /**
     * Creates a new user
     *
     * @access	public
     * @param 	string $username the user's username
     * @param	string $password the user's password
     * @return	TRUE if password is correct, FALSE otherwise
     */
    public function check_password($username, $password) {

        // query the database for user info
        $row = $this->user_model->get_user($username);

        if ($row) {

            $db_password = $row->Password;
            $salt = $row->Salt;
            $password = hash('sha512', $password . $salt);

            // Check that password match
            if ($db_password == $password) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
    public function get_num_taskrequests($userid){
            //$userid = $this->session->userdata('userid');
            $this->db->where('requesterid',$userid);
            return $this->db->count_all_results('tasks');
           
    }
    public function get_num_taskresponses($userid){
        //$userid = $this->session->userdata('userid');
            $this->db->where('workerid',$userid);
            return $this->db->count_all_results('responses');
        
    }
   
    /**
     * set user's channelid:
     *
     * @access	public
     * @return	true if is successfully set
     */
     
    public function update_channelid($channelid){
    	$id = $this->session->userdata('userid');

    	$this->db->set('channelid',$channelid);
    	$this->db->where('userid',$id);
    	$this->db->update('users');
    
    }
    
}
