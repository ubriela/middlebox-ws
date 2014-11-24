<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Bills extends REST_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('bill_model');
		
		$this->load->library('session');
		// form validation
		$this->load->library('form_validation');
	}
	
	function create_bill_post() {
		if ($this->_check_authorization() == false) {
			//log_message('info', 'Create bill: Unauthorized.');			
			return $this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 401);	
		}
		// check validation	
		if ($this->form_validation->run('create_bill') == false) {
			$this->response(array('status' => 'fail', 'msg' => validation_errors()), 400);
		}
		
		$billdesc = $this->post('billdesc');
		$amount = $this->post('amount');
		$tip = $this->post('tip');
		
		if ($billdesc == false || $amount == false || $tip == false) {
			//log_message('info', 'Create bill: Bad request.');
			$this->response(array('status' => 'fail', 'msg' => 'Bad request'), 400);
		}
		
		// get user id from session
		$user_data = $this->session->userdata('user_data');
		$creatorid = $user_data['userid'];
		
		$bill_data = $this->bill_model->create_bill($creatorid, $billdesc, $amount, $tip, $time);
		
		if ($bill_data == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Fail to update database'), 409);
		} else {
			$this->response(array('status' => 'success', 'msg' => '','time' => $time, 'data' => $bill_data), 200);
		}
	}
	
	function update_bill_post() {
		if (!$this->_check_authorization())
			return $this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 401);	
		
		// check validation	
		if ($this->form_validation->run('update_bill') == false) {
			$this->response(array('status' => 'fail', 'msg' => validation_errors()), 400);
		}
		// get userid
		$user_data = $this->session->userdata('user_data');
		$userid = $user_data['userid'];
		
		$billid = $this->post('billid');
		$billdesc = $this->post('billdesc');
		$amount = $this->post('amount');
		$tip = $this->post('tip');
		
		if ($billid == false)
			$this->response(array('status' => 'fail', 'msg' => 'Missing BillID'), 400);
		
		if ($userid == false)
			$this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 400);

		$bill = $this->bill_model->get_bill_by_billid($billid);
		
		if ($bill == false || $bill['isdeleted'] == 1) {
			$this->response(array('status' => 'fail', 'msg' => 'The bill is not found'), 404);
			return;
		}
		if ($bill['isdone'] == 1) {
			$this->response(array('status' => 'fail', 'msg' => 'The bill is already finished'), 409);
			return;
		}

		// bill exists. only update when the request user is the creator of the bill
		if ($userid != $bill['creatorid']) {
			$this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 403);
		} else {			
			$result = $this->bill_model->update_bill($billid, $billdesc, $amount, $tip, $time);
			
			if ($result == false)
				$this->response(array('status' => 'fail', 'msg' => 'Fail to update database'), 409);
			else 
				$this->response(array('status' => 'success', 'msg' => '', 'time' => $time, 'data' => $result ), 200);
		}
	}
	
	function finish_bill_post() {
		if (!$this->_check_authorization())
			$this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 401);
		
		// check validation	
		if ($this->form_validation->run('finish_bill') == false) {
			$this->response(array('status' => 'fail', 'msg' => validation_errors()), 400);
		}
		// get userid
		$user_data = $this->session->userdata('user_data');
		$userid = $user_data['userid'];
		
		$billid = $this->post('billid');
		
		if ($billid == false)
			$this->response(array('status' => 'fail', 'msg' => 'Missing BillID'), 400);
		
		if ($userid == false)
			$this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 400);

		$bill = $this->bill_model->get_bill_by_billid($billid);
		
		if ($bill == false || $bill['isdeleted'] == 1) {
			$this->response(array('status' => 'fail', 'msg' => 'The bill is not found'), 404);
			return;
		}

		// bill exists. only update when the request user is the creator of the bill
		if ($userid != $bill['creatorid']) {
			$this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 403);
		} else {			
			if ($bill['isdone'] == 1)
				$this->response(array('status' => 'success', 'msg' => '', 'data' => $bill), 200);
			else {
				$result = $this->bill_model->finish_bill($billid,$time);
			
				if ($result == false)
					$this->response(array('status' => 'fail', 'msg' => 'Fail to update database'), 409);
				else 
					$this->response(array('status' => 'success', 'msg' => '', 'time' => $time, 'data' => $result), 200);
			}
		}
	}
	
	function delete_bill_post() {
		if (!$this->_check_authorization())
			$this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 401);
		// check validation	
		if ($this->form_validation->run('delete_bill') == false) {
			$this->response(array('status' => 'fail', 'msg' => validation_errors()), 400);
		}
		
		// get userid
		$user_data = $this->session->userdata('user_data');
		$userid = $user_data['userid'];
		$billid = $this->post('billid');
		
		if ($billid == false)
			$this->response(array('status' => 'fail', 'msg' => 'Missing BillID'), 400);
		
		if ($userid == false)
			$this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 400);
		
		$bill = $this->bill_model->get_bill_by_billid($billid);
		
		if ($bill == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Bill is not found'), 404);
			return;
		}

		if ($userid != $bill['creatorid']) {
			$this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 403);
		} else if ($bill['isdeleted'] == 1) {
			$this->response(array('status' => 'fail', 'msg' => 'Bill is not found'), 200);
		} else {
			$result = $this->bill_model->delete_bill($billid,$time);
			
			if ($result == false)
				$this->response(array('status' => 'fail', 'msg' => 'Fail to update database'), 409);
			else
				$this->response(array('status' => 'success', 'msg' => '', 'time' => $time), 200);
		}
	}
	
	/** functions to support synchronization **/
	
	function fetch_bills_post() {
		if (!$this->_check_authorization())
			$this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 401);
		// check validation	
		if ($this->form_validation->run('fetch_bills') == false) {
			$this->response(array('status' => 'fail', 'msg' => validation_errors()), 400);
		}
			
		$last_sync_time = $this->post('last_sync_time');
		$isdone = $this->post('isdone');
		$user_data = $this->session->userdata('user_data');
		$userid = $user_data['userid'];
		
		if ($userid == false)
			$this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 400);
		
		if ($last_sync_time == false) {		// last sync time is not known, get all the bills
			if ($isdone == false)
				$bills = $this->bill_model->get_bills_by_userid($userid, $time);
			else
				$bills = $this->bill_model->get_bills_by_userid_bydone($userid, $time, $isdone);
		} else {
			if ($isdone == '')
				$bills = $this->bill_model->get_updated_bills($userid, $last_sync_time, $time);
			else
				$bills = $this->bill_model->get_updated_bills_bydone($userid, $last_sync_time, $time, $isdone);
		}
		
		if ($bills == false)
			$this->response(array('status' => 'fail', 'msg' => 'Fail to query database'), 404);
		else {
			unset($bills['num_rows']);
			$this->response(array('status' => 'success', 'msg' => '', 'time' => $time, 'data' => $bills), 200);
		}
	}
	
	//-----------------------------------------------------------------------------------
	//------------------------- EMAIL REQUESTS ------------------------------------------
	//----------------------------------------------------------------------------------- 
	function create_email_request_post() {
		if ($this->_check_authorization() == false) {
			log_message('info', 'Create email request: Unauthorized.');			
			return $this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 401);	
		}
		// check validation	
		if ($this->form_validation->run('create_email_request') == false) {
			$this->response(array('status' => 'fail', 'msg' => validation_errors()), 400);
		}
		
		$billid = $this->post('billid');
		$email = $this->post('email');
		$fullname = $this->post('fullname');
		
		if ($billid == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Missing BillID'), 400);
			return;
		}
		if ($email == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Missing Email'), 400);
			return;
		}
		if ($fullname == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Missing Fullname'), 400);
			return;
		}
		
		// get user id from session
		$user_data = $this->session->userdata('user_data');
		$userid = $user_data['userid'];
		
		// check bill
		$bill = $this->bill_model->get_bill_by_billid($billid);
		
		if ($bill == false || $bill['isdeleted'] == 1){
			$this->response(array('status' => 'fail', 'msg' => 'Bill is not found'), 404);
			return;
		}
			
		if ($bill['isdone'] == 1) {
			$this->response(array('status' => 'fail', 'msg' => 'Bill is already finished'), 409);
			return;
		}

		// bill exists. only update when the request user is the creator of the bill
		if ($userid != $bill['creatorid']) {
			$this->response(array('status' => 'fail', 'msg' => 'Missing Email'), 403);
			return;
		}
		
		// check email request
		$erequest = $this->bill_model->get_email_request($billid, $email);
		if ($erequest == false) {
			$erequest = $this->bill_model->create_email_request($billid, $email, $fullname,$time);
		}
		else {
			if ($erequest["isdeleted"] == 0) {
				$this->response(array('status' => 'fail', 'msg' => 'Email request is already created'), 409);
				return;
			}
			else
				$erequest = $this->bill_model->update_email_request($billid, $email, $fullname,0,$time);
		}
		
		if ($erequest == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Fail to update database'), 409);
		} else {
			$this->response(array('status' => 'success', 'msg' => '','time' => $time,'data' => $erequest), 200);
		}
	}
	
	function update_email_request_post() {
		if ($this->_check_authorization() == false) {
			log_message('info', 'Update email request: Unauthorized.');			
			return $this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 401);	
		}
		// check validation	
		if ($this->form_validation->run('update_email_request') == false) {
			$this->response(array('status' => 'fail', 'msg' => validation_errors()), 400);
		}
		
		$billid = $this->post('billid');
		$email = $this->post('email');
		$fullname = $this->post('fullname');
		
		
		if ($billid == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Missing BillID'), 400);
			return;
		}
		if ($email == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Missing Email'), 400);
			return;
		}
		if ($fullname == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Missing Fullname'), 400);
			return;
		}
		
		// get user id from session
		$user_data = $this->session->userdata('user_data');
		$userid = $user_data['userid'];
		
		// check bill
		$bill = $this->bill_model->get_bill_by_billid($billid);
		
		if ($bill == false || $bill['isdeleted'] == 1){
			$this->response(array('status' => 'fail', 'msg' => 'Bill is not found'), 404);
			return;
		}
			
		if ($bill['isdone'] == 1) {
			$this->response(array('status' => 'fail', 'msg' => 'Bill is already finished'), 409);
			return;
		}

		// bill exists. only update when the request user is the creator of the bill
		if ($userid != $bill['creatorid']) {
			$this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 403);
			return;
		}
		
		// check email request
		$erequest = $this->bill_model->get_email_request($billid, $email);
		if ($erequest == false || $erequest["isdeleted"] == 1) {
			$this->response(array('status' => 'fail', 'msg' => 'Email request is not found'), 404);
			return;
		}
		else {
			$erequest = $this->bill_model->update_email_request($billid, $email, $fullname,0,$time);
		}
		if ($erequest == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Fail to update database'), 409);
		} else {
			$this->response(array('status' => 'success', 'msg' => '', 'time' => $time,'data' => $erequest), 200);
		}
	}
	
	
	function delete_email_request_post() {
		if ($this->_check_authorization() == false) {
			log_message('info', 'Delete email request: Unauthorized.');			
			return $this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 401);	
		}
		// check validation	
		if ($this->form_validation->run('delete_email_request') == false) {
			$this->response(array('status' => 'fail', 'msg' => validation_errors()), 400);
		}
		
		$billid = $this->post('billid');
		$email = $this->post('email');
		
		if ($billid == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Missing BillID'), 400);
			return;
		}
		if ($email == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Missing Email'), 400);
			return;
		}
		
		// get user id from session
		$user_data = $this->session->userdata('user_data');
		$userid = $user_data['userid'];
		
		// check bill
		$bill = $this->bill_model->get_bill_by_billid($billid);
		
		if ($bill == false || $bill['isdeleted'] == 1){
			$this->response(array('status' => 'fail', 'msg' => 'Bill is not found'), 404);
			return;
		}
			
		if ($bill['isdone'] == 1) {
			$this->response(array('status' => 'fail', 'msg' => 'Bill is already finished'), 409);
			return;
		}

		// bill exists. only update when the request user is the creator of the bill
		if ($userid != $bill['creatorid']) {
			$this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 403);
			return;
		}
		
		// check email request
		$erequest = $this->bill_model->get_email_request($billid, $email);
		if ($erequest == false || $erequest["isdeleted"] == 1) {
			$this->response(array('status' => 'fail', 'msg' => 'Email request is not found'), 404);
			return;
		}
		else {
			$erequest = $this->bill_model->update_email_request($billid, $email, $erequest['fullname'],1,$time);
		}
		
		if ($erequest == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Fail to update database'), 409);
		} else {
			$this->response(array('status' => 'success', 'msg' => '','time' => $time), 200);
		}
	}
	
	
	function confirm_email_request_post() {
		if ($this->_check_authorization() == false) {
			log_message('info', 'Confirm email request: Unauthorized.');			
			return $this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 401);	
		}
		// check validation	
		if ($this->form_validation->run('confirm_email_request') == false) {
			$this->response(array('status' => 'fail', 'msg' => validation_errors()), 400);
		}
		
		$billid = $this->post('billid');
		$email = $this->post('email');
		

		if ($billid == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Missing BillID'), 400);
			return;
		}
		if ($email == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Missing Email'), 400);
			return;
		}
		
		// get user id from session
		$user_data = $this->session->userdata('user_data');
		$userid = $user_data['userid'];
		
		// check bill
		$bill = $this->bill_model->get_bill_by_billid($billid);
		
		if ($bill == false || $bill['isdeleted'] == 1){
			$this->response(array('status' => 'fail', 'msg' => 'Bill is not found'), 404);
			return;
		}
			
		if ($bill['isdone'] == 1) {
			$this->response(array('status' => 'fail', 'msg' => 'Bill is already finished'), 409);
			return;
		}

		// bill exists. only update when the request user is the creator of the bill
		if ($userid != $bill['creatorid']) {
			$this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 403);
			return;
		}
		
		// check email request
		$erequest = $this->bill_model->get_email_request($billid, $email);
		if ($erequest == false || $erequest["isdeleted"] == 1) {
			$this->response(array('status' => 'fail', 'msg' => 'Email request is not found'), 404);
			return;
		}
		else {
			$erequest = $this->bill_model->confirm_email_request($billid, $email,$time);
		}
		
		if ($erequest == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Fail to update database'), 409);
		} else {
			$this->response(array('status' => 'success', 'msg' => '','time' => $time), 200);
		}
	}
	
	function create_emails_request_post() {
		if ($this->_check_authorization() == false) {
			log_message('info', 'Create emails request: Unauthorized.');			
			return $this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 401);	
		}
		// check validation	
		if ($this->form_validation->run('create_emails_request') == false) {
			$this->response(array('status' => 'fail', 'msg' => validation_errors()), 400);
		}
		
		$billid = $this->post('billid');
		$emails_ob = $this->post('emails');
		$emails = json_decode($emails_ob);
		

		if ($billid == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Missing BillID'), 400);
			return;
		}
		if ($emails == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Missing Emails'), 400);
			return;
		}
		
		// get user id from session
		$user_data = $this->session->userdata('user_data');
		$userid = $user_data['userid'];
		
		// check bill
		$bill = $this->bill_model->get_bill_by_billid($billid);
		
		if ($bill == false || $bill['isdeleted'] == 1){
			$this->response(array('status' => 'fail', 'msg' => 'Bill is not found'), 404);
			return;
		}
			
		if ($bill['isdone'] == 1) {
			$this->response(array('status' => 'fail', 'msg' => 'Bill is already finished'), 409);
			return;
		}

		// bill exists. only update when the request user is the creator of the bill
		if ($userid != $bill['creatorid']) {
			$this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 403);
			return;
		}
		
		$fail_list = array();
		$succ_list = array();
		foreach ($emails as $email_full) {
			if (!property_exists($email_full,"email") || !property_exists($email_full,"fullname"))
				$this->response(array('status' => 'fail', 'msg' => 'Bad emails list'), 400);
			$email = $email_full->email;
			$fullname = $email_full->fullname;
			
			$erequest = $this->bill_model->get_email_request($billid, $email);
			if ($erequest == false) {
				$erequest = $this->bill_model->create_email_request($billid, $email, $fullname,$time);
				
				if ($erequest == false)
					array_push($fail_list,array('msg' => 'Fail to create the email request', 'email' => $email));
				else 
					array_push($succ_list, $erequest);
			}
			else {
				if ($erequest["isdeleted"] == 0) {
					array_push($fail_list,array('msg' => 'Email request already exists', 'email' => $email));
				}
				else {
					$erequest = $this->bill_model->update_email_request($billid, $email, $fullname,0,$time);
					if ($erequest == false)
						array_push($fail_list,array('msg' => 'Fail to create the email request', 'email' => $email));
					else 
						array_push($succ_list, $erequest);
				}
			}
		
		}
		$time = $this->bill_model->get_time();
		$output = array('time' => $time);
		if (count($fail_list) > 0) $output["fail_list"] = $fail_list;
		if (count($succ_list) > 0) $output["succ_list"] = $succ_list;
		$this->response($output, 200);
	}
	
	function delete_emails_request_post() {
		if ($this->_check_authorization() == false) {
			log_message('info', 'Delete emails request: Unauthorized.');			
			return $this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 401);	
		}
		// check validation	
		if ($this->form_validation->run('delete_emails_request') == false) {
			$this->response(array('status' => 'fail', 'msg' => validation_errors()), 400);
		}
		
		$billid = $this->post('billid');
		$emails_ob = $this->post('emails');
		$emails = json_decode($emails_ob);
		
		if ($billid == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Missing BillID'), 400);
			return;
		}
		if ($emails == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Missing Emails'), 400);
			return;
		}
		
		// get user id from session
		$user_data = $this->session->userdata('user_data');
		$userid = $user_data['userid'];
		
		// check bill
		$bill = $this->bill_model->get_bill_by_billid($billid);
		
		if ($bill == false || $bill['isdeleted'] == 1){
			$this->response(array('status' => 'fail', 'msg' => 'Bill is not found'), 404);
			return;
		}
			
		if ($bill['isdone'] == 1) {
			$this->response(array('status' => 'fail', 'msg' => 'Bill is already finished'), 409);
			return;
		}

		// bill exists. only update when the request user is the creator of the bill
		if ($userid != $bill['creatorid']) {
			$this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 403);
			return;
		}
		
		$fail_list = array();
		$succ_list = array();
		foreach ($emails as $email) {
			$erequest = $this->bill_model->get_email_request($billid, $email);
			if ($erequest == false || $erequest["isdeleted"] == 1) {
				array_push($fail_list,array('msg' => 'Email request is not found', 'email' => $email));
			}
			else {
				$erequest = $this->bill_model->update_email_request($billid, $email, $erequest['fullname'],1,$time);
				
				if ($erequest == false)
					array_push($fail_list,array('msg' => 'Fail to update database', 'email' => $email));
				else 
					array_push($succ_list,$email);
			}
		}
		$time = $this->bill_model->get_time();
		$output = array('time' => $time);
		if (count($fail_list) > 0) $output["fail_list"] = $fail_list;
		if (count($succ_list) > 0) $output["succ_list"] = $succ_list;
		$this->response($output, 200);
	}
	
	function confirm_emails_request_post() {
		if ($this->_check_authorization() == false) {
			log_message('info', 'Confirm emails request: Unauthorized.');			
			return $this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 401);	
		}
		// check validation	
		if ($this->form_validation->run('confirm_emails_request') == false) {
			$this->response(array('status' => 'fail', 'msg' => validation_errors()), 400);
		}
		
		$billid = $this->post('billid');
		$emails_ob = $this->post('emails');
		$emails = json_decode($emails_ob);
		

		if ($billid == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Missing BillID'), 400);
			return;
		}
		if ($emails == false) {
			$this->response(array('status' => 'fail', 'msg' => 'Missing Emails'), 400);
			return;
		}
		
		// get user id from session
		$user_data = $this->session->userdata('user_data');
		$userid = $user_data['userid'];
		
		// check bill
		$bill = $this->bill_model->get_bill_by_billid($billid);
		
		if ($bill == false || $bill['isdeleted'] == 1){
			$this->response(array('status' => 'fail', 'msg' => 'Bill is not found'), 404);
			return;
		}
			
		if ($bill['isdone'] == 1) {
			$this->response(array('status' => 'fail', 'msg' => 'Bill is already finished'), 409);
			return;
		}

		// bill exists. only update when the request user is the creator of the bill
		if ($userid != $bill['creatorid']) {
			$this->response(array('status' => 'fail', 'msg' => 'Unauthorized'), 403);
			return;
		}
		
		$fail_list = array();
		$succ_list = array();
		foreach ($emails as $email) {
			$erequest = $this->bill_model->get_email_request($billid, $email);
			if ($erequest == false || $erequest["isdeleted"] == 1) {
				array_push($fail_list,array('msg' => 'Email request is not found', 'email' => $email));
			}
			else {
				$erequest = $this->bill_model->confirm_email_request($billid, $email,$time);
				if ($erequest == false)
					array_push($fail_list,array('msg' => 'Fail to update database', 'email' => $email));
				else 
					array_push($succ_list,$email);
			}
		
		}
		$time = $this->bill_model->get_time();
		$output = array('time' => $time);
		if (count($fail_list) > 0) $output["fail_list"] = $fail_list;
		if (count($succ_list) > 0) $output["succ_list"] = $succ_list;
		$this->response($output, 200);
	}
	private function _check_authorization() {
		$user_data = $this->session->userdata('user_data');
	
		if ($user_data == false)
			return false;
		else {
			$sess_user_id = $user_data['userid'];
				
			if ($sess_user_id == false) {				
				return false;
			} else {
				return true;
			}
		}
	}
}
