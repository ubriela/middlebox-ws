<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Hieu Nguyen
 */

require APPPATH.'/libraries/REST_Controller.php';

class Users extends REST_Controller {
	function __construct() {
		parent::__construct();
		
		// loading the database
		$this->load->model('user_model');
		
		// using session
		$this->load->library('session');
		
		// form validation
		$this->load->library('form_validation');
	}
	
	/**
	 * Create a new account
	 */
	public function signup_post() {
		// check validation	
		if ($this->form_validation->run('signup') == false) {
			$this->response(array('status' => 'fail', 'msg' => validation_errors()), 400);
		}
		
		// get data
		$username = $this->post('username');
		$email = $this->post('email');
		$password = $this->post('password');
		
// 		if ($this->user_model->check_username_exists($username)) {
// 			$this->response(array('status' => 'fail', 'msg' => 'Username is used'), 409);
// 		}
// 		
// 		if ($this->user_model->check_email_exists($username)) {
// 			$this->response(array('status' => 'fail', 'msg' => 'Email is used'), 409);
// 		}
		
		$user_data = $this->user_model->create_user($username, $email, $password, $time);
		if ($user_data == false)
			$this->response(array('status' => 'fail', 'msg' => 'Fail to create user'), 500);
		else {
			$s_user_data = array(
				'userid' => $user_data['userid'],
				'username' => $user_data['username'],
				'email' => $user_data['email']
			);
			
			$this->session->set_userdata('user_data', $s_user_data);
			$this->response(array('status' => 'success', 'msg' => '', 'time' => $time, 'data' => $user_data), 200);
		}
	}
	
	/**
	 * Login to the system
	 */
	public function login_post() {	
		// get data
		if ($this->form_validation->run('login_username') == true) {
			$username_or_email = $this->post('username');
		} else if ($this->form_validation->run('login_email') == true) {
			$email = $this->post('email');
		} else {
			return $this->response(array('status' => 'fail', 'msg' => validation_errors()), 400);
		}
		
		$password = $this->post('password');
		
		// get user
		$user = $this->user_model->get_user($username_or_email);
		
		// user not found
		if ($user === null) {
			$this->response(array('status' => 'fail', 'msg' => 'User is not found'), 404);
			return;
		}
		
		// check if password matches
		$pw = hash('sha512', $password . $user['salt']);
		
		if ($pw === $user['password']) {
			$data = $this->user_model->login($user['userid'],$time);
			// prepare session data			
			if ($data != false) {
				$user_data = array(
					'userid' => $data['userid'],
					'username' => $data['username'],
					'email' => $data['email']
				);
			
				$this->session->set_userdata('user_data', $user_data);
			
// 				$data = array(
// 					'friendship' => 'owner',
//	 				'userid' => $user['userid'],
// 					'username' => $user['username'],
// 					'email' => $user['email'],
// 					'alias' => $user['alias'],
// 					'avatar' => $user['avatar'],
// 					'jointime' => $user['jointime'],
// 					'lastupdatetime' => $user['lastupdatetime'],
// 					'lastlogintime' => $user['lastlogintime'],			
// 				);
			
				// return HTTP OK
				$this->response(array('status' => 'success', 'msg' => '', 'time' => $time, 'data' => $data), 200);
			}
			else {
				$this->response(array('status' => 'fail', 'msg' => "Can't update database"), 500);
			}
		} else {
			$this->response(array('status' => 'fail', 'msg' => 'Incorrect password'), 409);		
		}		
	}
	

	/**
	 * Log out.
	 */
	public function logout_get() {
		if (!$this->_check_authorization())
			return $this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 401);	
	
		// performs log out
		// remove session
		$array_items = array('userid' => '','username' => '','email' => '');
		$this->session->unset_userdata($array_items);
	
		return $this->response(array('status' => 'sucess', 'msg' => ''), 200);	
	}
	
	/**
	 * Sets avatar
	 */
	public function setavatar_post() {
		if (!$this->_check_authorization()) {
			return $this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 401);	
		}
		
		// get user id from session
		$userid = $this->session->userdata('user_data')['userid'];
		
		// change the uploaded file name
		$data = $_FILES['avatar'];
		
		if ($data == false || $data['name'] == false)
			return $this->response(array('status' => 'fail', 'msg' => 'Fail to upload file'), 401);	
		
		$file_name = $data['name'];
		$ext = pathinfo($file_name, PATHINFO_EXTENSION);
		
		if (preg_match("/\.(?i)(jpg|png|gif|bmp|jpeg)/", $file_name, $match) == false)
			return $this->response(array('status' => 'fail', 'msg' => 'Unsupported file format'), 401);	

		$new_file_name = $userid.'_avt.'.pathinfo($file_name, PATHINFO_EXTENSION);
		
		// set upload directory
		$upload_dir = 'app/files/avatars/';
		$upload_file = $upload_dir.$new_file_name;
		
		// move upload file
		if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_file)) {
			// update database
			$user_data = $this->user_model->set_avatar($userid, $new_file_name, $time);
			if ($user_data != false) {
				return $this->response(array('status' => 'success', 'msg' => '', 'time' => $time, 'data' => $user_data), 200);
			}
		}
		
		return $this->response(array('status' => 'fail', 'msg' => 'Fail to update database'), 500);
	}
	
	/**
	 * Sets alias. If new alias is identical with the old alias, 
	 * this function will return false.
	 */
	public function setalias_post() {
		if (!$this->_check_authorization()) {
			return $this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 401);	
		}
		
		// get user id from session
		$userid = $this->session->userdata('user_data')['userid'];
				
		if ($this->form_validation->run('set_alias') == false)
			return $this->response(array('status' => 'fail', 'msg' => validation_errors()), 400);
		
		$alias = $this->post('alias');
		
		$user_data = $this->user_model->set_alias($userid, $alias, $time);
		if ($user_data != false)
			return $this->response(array('status' => 'success', 'msg' => '', 'time' => $time, 'data' => $user_data), 200);
		
		return $this->response(array('status' => 'fail', 'msg' => 'Fail to update database'), 500);
	}
	
	public function changepass_post() {
		if (!$this->_check_authorization()) {
			return $this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 401);	
		}

		// get user id from session
		$userid = $this->session->userdata('user_data')['userid'];

		if ($this->form_validation->run('change_password') == false)
			return $this->response(array('status' => 'fail', 'msg' => validation_errors()), 400);

		$oldpass = $this->post('oldpass');
		$newpass = $this->post('newpass');
		
		if ($oldpass === $newpass)
			return $this->response(array('status' => 'fail', 'msg' => 'New password is the same as the old one'), 409);
		
		$user_data = $this->user_model->change_password($userid, $oldpass, $newpass, $time);
		if ($user_data != false)
			return $this->response(array('status' => 'success', 'msg' => '', 'time' => $time, 'data' => $user_data), 200);
		else
			return $this->response(array('status' => 'fail', 'msg' => 'Fail to update database'), 500);
	}
	
	/**
	 * Gets information of a user.
	 * Currently required user to log in in order to use this function
	 */
	public function getuser_post() {
		if (!$this->_check_authorization()) {
			return $this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 401);	
		}		
		
		// runs validation to validate the user input
		if ($this->form_validation->run('get_user') == false)
			return $this->response(array('status' => 'fail', 'msg' => validation_errors()), 400);
		
		$requesterid = $this->session->userdata('user_data')['userid'];
		$userid = $this->post('userid');
		
		$user = $this->user_model->get_user_by_id($userid);
		
		if ($user == null)
			return $this->response(array('status' => 'fail', 'msg' => 'User is not found'), 404);
		
		if ($user['userid'] == $requesterid) {
			$status = 'owner';
		} else	
			// gets the friendship status between these two user
			$status = $this->user_model->check_friendship($requesterid, $user['userid']);

		$data = array(
			'friendship' => $status,
			'userid' => $user['userid'],
			'username' => $user['username'],
			'email' => $user['email'],
			'alias' => $user['alias'],
			'avatar' => $user['avatar'],
			'jointime' => $user['jointime'],
			'lastupdatetime' => $user['lastupdatetime'],
			'lastlogintime' => $user['lastlogintime'],			
		);
		
		return $this->response(array('status' => 'success', 'msg' => '', 'data' => $data), 200);
	}
	
	/**
	 * Invites another member to become friend
	 */
	public function invitefriend_post() {
		if (!$this->_check_authorization()) {
			$this->response(null, 404);
		}
		
		if ($this->form_validation->run('invite_friend') == false) {
			$this->response(null, 400);
		}
				
		$inviterid = $this->session->userdata('user_data')['userid'];
		$inviteeid = $this->post('inviteeid');
		
		if ($this->user_model->invite_friend($inviterid, $inviteeid) == false) {
			$this->response(null, 409);
		} else {
			$this->response(null, 200);
		}
	}
	
	/**
	 * The invitee accepts friend invitation.
	 */
	public function accept_friend_get() {
		if (!$this->_check_authorization()) {
			$this->response(null, 404);
		}
		
		if ($this->form_validation->run('accept_reject_friend') == false) {
			$this->response(null, 400);
		}
		
		$inviterid = $this->post('inviterid');
		$inviteeid = $this->session->userdata('user_data')['userid'];

		if ($this->user_model->accept_friend($inviterid, $inviteeid) == false) {
			$this->response(null, 409);
		} else {
			$this->response(null, 200);
		}		
	}
	
	/**
	 * The invitee rejects the friend invitation.
	 */
	public function reject_friend_get() {
		if (!$this->_check_authorization()) {
			$this->response(null, 404);
		}
		
		if ($this->form_validation->run('accept_reject_friend') == false) {
			$this->response(null, 400);
		}
		
		$inviterid = $this->post('inviterid');
		$inviteeid = $this->session->userdata('user_data')['userid'];
		
		if ($this->user_model->reject_friend($inviterid, $inviteeid) == false) {
			$this->response(null, 409);
		} else {
			$this->response(null, 200);
		}		
	}
	
	/**
	 * A user thaws the friendship with one of his friends.
	 */
	public function unfriend_get() {
		if (!$this->_check_authorization()) {
			$this->response(null, 404);
		}
		
		if ($this->form_validation->run('unfriend') == false) {
			$this->response(null, 400);
		}
		
		$friendid2 = $this->post('inviterid');
		$friendid1 = $this->session->userdata('user_data')['userid'];
		
		if ($this->user_model->unfriend($friendid1, $friendid2) == false) {
			$this->response(null, 409);
		} else {
			$this->response(null, 200);
		}		
	}
	
	private function _check_authorization() {
		$user_data = $this->session->userdata('user_data');
		
		if ($user_data == false)
			return false;
		else {
			$sess_user_id = $user_data['userid'];
			
			if ($sess_user_id == false || preg_match('/^[1-9][0-9]*$/D', $sess_user_id) == false)
				return false;
			else
				return true;
		}
	}
	
	public function test_put() {
		$type = $this->put("type");
		
		$this->response($type, 200);
	}
	
	public function test_delete($type) {
		$this->response($type, 200);
	}
	
}