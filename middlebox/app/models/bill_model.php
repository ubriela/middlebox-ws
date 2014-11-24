<?php

class Bill_model extends CI_Model {
	public function __construct() {
		$this->load->database();
	}

	function get_bills_by_userid($userid, &$time) {
		// before getting updated bills, get the current time of the mysql server
		$time = $this->db->query("SELECT NOW()");
		$time = get_object_vars($time->result()[0])["NOW()"];
		
		// retrieve bills
		$this->db->where('creatorid', $userid);
		$this->db->where('isdeleted', '0');
		$query = $this->db->get('bills');
		
		if ($query != false) {
			$output = array();
			$output['num_rows'] = $query->num_rows();
			$count = 0;
			foreach ($query->result() as $bill) {
				// retrieve emailrequests
				$this->db->where('billid', $bill->billid);
				$this->db->where('isdeleted', '0');
				$query2 = $this->db->get('emailrequests');
				if ($query2 != false && $query2->num_rows() > 0){
					$bill = (array)$bill;
					$bill['emailrequests'] = $query2->result();
					$bill = (object)$bill;
				}
				$output[$count] = $bill;
				$count++;
			}
			return $output;
		} else
			return false;
	}
	
	function get_bills_by_userid_bydone($userid, &$time, $isdone) {
		// before getting updated bills, get the current time of the mysql server
		$time = $this->db->query("SELECT NOW()");
		$time = get_object_vars($time->result()[0])["NOW()"];
		
		// retrieve bills
		$this->db->where('creatorid', $userid);
		$this->db->where('isdone', $isdone);
		$this->db->where('isdeleted', '0');
		$query = $this->db->get('bills');
		
		if ($query != false) {
			$output = array();
			$output['num_rows'] = $query->num_rows();
			
			$count = 0;
			foreach ($query->result() as $bill) {
				// retrieve emailrequests
				$this->db->where('billid', $bill->billid);
				$this->db->where('isdeleted', '0');
				$query2 = $this->db->get('emailrequests');
				if ($query2 != false && $query2->num_rows() > 0){
					$bill = (array)$bill;
					$bill['emailrequests'] = $query2->result();
					$bill = (object)$foo;
				}
				$output[$count] = $bill;
				$count++;
			}
			return $output;
		} else
			return false;
	}
	
	function get_bill_by_billid($billid) {
		$this->db->where('billid', $billid);
		$query = $this->db->get('bills');
		
		if ($query != false && $query->num_rows() > 0) {
			return get_object_vars($query->result()[0]);
		} else 
			return false;
	}
	
	function get_updated_bills($userid, $last_sync_time, &$time) {
		// before getting updated bills, get the current time of the mysql server
		$time = $this->db->query("SELECT NOW()");
		$time = get_object_vars($time->result()[0])["NOW()"];
		
		$output = array();
		//----- get new bills
		$this->db->select('*');
		$this->db->from('bills');
		$this->db->where('creatorid', $userid);
		$this->db->where('isdeleted = 0');
		$this->db->where('createdtime > ', $last_sync_time);
		$new_list = $this->db->get();
		if ($new_list != false) {
			$output['num_rows'] = $new_list->num_rows();
		}
		
		if ($new_list != false && $new_list->num_rows() > 0) {
			$new_list2 = array();
			$count = 0;
			foreach ($new_list->result() as $bill) {
				// retrieve emailrequests
				$this->db->where('billid', $bill->billid);
				$this->db->where('isdeleted', '0');
				$query2 = $this->db->get('emailrequests');
				if ($query2 != false && $query2->num_rows() > 0){
					$bill = (array)$bill;
					$bill['emailrequests'] = $query2->result();
					$bill = (object)$bill;
				}
				$new_list2[$count] = $bill;
				$count++;
			}
			$output["new"] = $new_list2;
		}
		
		// get updated bills
		$this->db->select('*');
		$this->db->from('bills');
		$this->db->where('creatorid', $userid);
		$this->db->where('createdtime <= ', $last_sync_time);
		$this->db->where('lastupdatetime > ', $last_sync_time);
		$this->db->where('isdeleted = 0');
		$update_list = $this->db->get();
		
		if ($update_list != false && $update_list->num_rows() > 0) {
			$update_list2 = array();
			$count = 0;
			foreach ($update_list->result() as $bill) {
				// retrieve new emailrequests
				$this->db->where('billid', $bill->billid);
				$this->db->where('isdeleted', '0');
				$this->db->where('requestdate > ', $last_sync_time);
				
				$query2 = $this->db->get('emailrequests');
				if ($query2 != false && $query2->num_rows() > 0){
					$bill = (array)$bill;
					$bill['new_emailrequests'] = $query2->result();
					$bill = (object)$bill;
				}
				
				// retrieve updated emailrequests
				$this->db->where('billid', $bill->billid);
				$this->db->where('isdeleted', '0');
				$this->db->where('requestdate <= ', $last_sync_time);
				$this->db->where('lastupdatetime > ', $last_sync_time);
				
				$query3 = $this->db->get('emailrequests');
				if ($query3 != false && $query3->num_rows() > 0){
					$bill = (array)$bill;
					$bill['updated_emailrequests'] = $query3->result();
					$bill = (object)$bill;
				}
				
				// retrieve deleted emailrequests
				$this->db->where('billid', $bill->billid);
				$this->db->where('isdeleted', '1');
				$this->db->where('requestdate <= ', $last_sync_time);
				$this->db->where('lastupdatetime > ', $last_sync_time);
				
				$query4 = $this->db->get('emailrequests');
				if ($query4 != false && $query4->num_rows() > 0){
					$bill = (array)$bill;
					$bill['deleted_emailrequests'] = $query4->result();
					$bill = (object)$bill;
				}
				
				$update_list2[$count] = $bill;
				$count++;
			}
			$output["updated"] = $update_list2;
		}
		
		// get deleted bills
		$this->db->select('billid');
		$this->db->from('bills');
		$this->db->where('creatorid', $userid);
		$this->db->where('isdeleted = 1');
		$this->db->where('createdtime <= ', $last_sync_time);
		$this->db->where('lastupdatetime > ', $last_sync_time);
		$delete_list = $this->db->get();
		
		if ($delete_list != false && $delete_list->num_rows() > 0) {
			$output["deleted"] = $delete_list->result();
		}
		
		return $output;
	}
	
	function get_updated_bills_bydone($userid, $last_sync_time, &$time, $isdone) {
		// before getting updated bills, get the current time of the mysql server
		$time = $this->db->query("SELECT NOW()");
		$time = get_object_vars($time->result()[0])["NOW()"];
		
		$output = array();
		//----- get new bills
		$this->db->select('*');
		$this->db->from('bills');
		$this->db->where('creatorid', $userid);
		$this->db->where('isdeleted = 0');
		$this->db->where('isdone', $isdone);
		$this->db->where('createdtime > ', $last_sync_time);
		$new_list = $this->db->get();
		if ($new_list != false) {
			$output['num_rows'] = $new_list->num_rows();
		}
		
		if ($new_list != false && $new_list->num_rows() > 0) {
			$new_list2 = array();
			$count = 0;
			foreach ($new_list->result() as $bill) {
				// retrieve emailrequests
				$this->db->where('billid', $bill->billid);
				$this->db->where('isdeleted', '0');
				$query2 = $this->db->get('emailrequests');
				if ($query2 != false && $query2->num_rows() > 0){
					$bill = (array)$bill;
					$bill['emailrequests'] = $query2->result();
					$bill = (object)$bill;
				}
				$new_list2[$count] = $bill;
				$count++;
			}
			$output["new"] = $new_list2;
		}
		
		// get updated bills
		$this->db->select('*');
		$this->db->from('bills');
		$this->db->where('creatorid', $userid);
		$this->db->where('isdone', $isdone);
		$this->db->where('createdtime <= ', $last_sync_time);
		$this->db->where('lastupdatetime > ', $last_sync_time);
		$this->db->where('isdeleted = 0');
		$update_list = $this->db->get();
		
		if ($update_list != false && $update_list->num_rows() > 0) {
			$update_list2 = array();
			$count = 0;
			foreach ($update_list->result() as $bill) {
				// retrieve new emailrequests
				$this->db->where('billid', $bill->billid);
				$this->db->where('isdeleted', '0');
				$this->db->where('requestdate > ', $last_sync_time);
				
				$query2 = $this->db->get('emailrequests');
				if ($query2 != false && $query2->num_rows() > 0){
					$bill = (array)$bill;
					$bill['new_emailrequests'] = $query2->result();
					$bill = (object)$bill;
				}
				
				// retrieve updated emailrequests
				$this->db->where('billid', $bill->billid);
				$this->db->where('isdeleted', '0');
				$this->db->where('requestdate <= ', $last_sync_time);
				$this->db->where('lastupdatetime > ', $last_sync_time);
				
				$query2 = $this->db->get('emailrequests');
				if ($query2 != false && $query2->num_rows() > 0){
					$bill = (array)$bill;
					$bill['updated_emailrequests'] = $query2->result();
					$bill = (object)$bill;
				}
				
				// retrieve deleted emailrequests
				$this->db->where('billid', $bill->billid);
				$this->db->where('isdeleted', '1');
				$this->db->where('requestdate <= ', $last_sync_time);
				$this->db->where('lastupdatetime > ', $last_sync_time);
				
				$query2 = $this->db->get('emailrequests');
				if ($query2 != false && $query2->num_rows() > 0){
					$bill = (array)$bill;
					$bill['deleted_emailrequests'] = $query2->result();
					$bill = (object)$bill;
				}
				
				$update_list2[$count] = $bill;
				$count++;
			}
			$output["updated"] = $update_list2;
		}
		
		// get deleted bills
		$this->db->select('billid');
		$this->db->from('bills');
		$this->db->where('creatorid', $userid);
		$this->db->where('isdeleted', '1');
		$this->db->where('isdone', $isdone);
		$this->db->where('createdtime <= ', $last_sync_time);
		$this->db->where('lastupdatetime > ', $last_sync_time);
		$delete_list = $this->db->get();
		
		if ($delete_list != false && $delete_list->num_rows() > 0) {
			$output["deleted"] = $delete_list;
		}
		
		return $output;
	}
	
	function create_bill($creatorid, $billdesc, $amount, $tip, &$time) {
		// Prepare the data to insert
		$time = $this->db->query("SELECT NOW()");
		$time = get_object_vars($time->result()[0])["NOW()"];
		$bill_data = array(
			'creatorid' => $creatorid,
			'billdesc' => $billdesc,
			'amount' => $amount,
			'createdtime' => $time,
			'lastupdatetime' => $time,
			'tip' => $tip,
			'isdone' => false,
			'isdeleted' => false
		);
		
		// Transaction
		$this->db->trans_start();
		$this->db->insert('bills', $bill_data);
		$id = $this->db->insert_id();
		if ($id != 0) {
			$this->db->where('billid', $id);
			$query = $this->db->get('bills');
			
			if ($query == true && $query->num_rows() > 0) {
				$bill_data = get_object_vars($query->result()[0]);
			} else {
				log_message('info', 'In bill_model.php createbill() mysql error.');
			}
		}
		$this->db->trans_complete();
		
		
		if ($this->db->trans_status() === false) {
			log_message('info', 'In bill_model.php createbill() transaction failed.'.$id);
			return false;
		} else {		
			return $bill_data;
		}		
	}
	
	function update_bill($billid, $billdesc, $amount, $tip, &$time) {
		$time = $this->db->query("SELECT NOW()");
		$time = get_object_vars($time->result()[0])["NOW()"];
		$update_data = array('lastupdatetime' => $time);
		if ($billdesc != false)
			$update_data['billdesc'] = $billdesc;
		if ($amount != false)
			$update_data['amount'] = $amount;
		if ($tip != false)
			$update_data['tip'] = $tip;
		
		$this->db->trans_start();
		
		// update bill
		$this->db->where('billid', $billid);		
		$this->db->update('bills', $update_data);
		
		// get latest state of the bill item
		$this->db->where('billid', $billid);
		$query = $this->db->get('bills');
		
		$this->db->trans_complete();
		
		if ($this->db->trans_status() == false || $query == false || $query->num_rows() == 0) {
			return false;
		} else {
			return get_object_vars($query->result()[0]);
		}
		
		return result;
	}
	
	function delete_bill($billid, &$time) {
		$time = $this->db->query("SELECT NOW()");
		$time = get_object_vars($time->result()[0])["NOW()"];
		$update_data = array(
			'isdeleted' => '1',
			'lastupdatetime' => $time
		);
		
		$this->db->trans_start();
		
		// update bill
		$this->db->where('billid', $billid);		
		$this->db->update('bills', $update_data);
		
		// get latest state of the bill item
		$this->db->where('billid', $billid);
		$query = $this->db->get('bills');
		
		$this->db->trans_complete();
		
		if ($this->db->trans_status() == false || $query == false || $query->num_rows() == 0) {
			return false;
		} else {
			return get_object_vars($query->result()[0]);
		}
	}
	
	function finish_bill($billid,&$time) {
		$time = $this->db->query("SELECT NOW()");
		$time = get_object_vars($time->result()[0])["NOW()"];
		$update_data = array(
			'isdone' => '1',
			'lastupdatetime' => $time
		);
		
		$this->db->trans_start();
		
		// update bill
		$this->db->where('billid', $billid);		
		$this->db->update('bills', $update_data);
		
		// get latest state of the bill item
		$this->db->where('billid', $billid);
		$query = $this->db->get('bills');
		
		$this->db->trans_complete();
		
		if ($this->db->trans_status() == false || $query == false || $query->num_rows() == 0) {
			return false;
		} else {
			return get_object_vars($query->result()[0]);
		}
		
		return result;
	}
	
	function request_money($requesteeid, $billid) {
		$bill_request = array(
			'billid' => $billid,
			'requesteeid' => $requesteeid,
			'isconfirmed' => false,
			'isdeleted' => false
		);

		$this->db->trans_start();		// start transaction
		
		// check whether a row exist
		$this->db->select('*');
		$this->db->from('billrequests');
		$this->db->where('billid', $billid);
		$this->db->where('requesteeid', $requesteeid);
		$this->db->where('isdeleted', true);
		
		$result = $this->db->get();
		
		if ($result == false) {		
			$this->db->insert('billrequests', $bill_request);
			
			if ($this->db->affected_rows() > 0) {
				// get the id of the recent inserted-row
				$id = $this->db->insert_id();
				
				return $id;
			} else {
				return false;
			}
		} else {
			//$this->db->
		}
	}
	
	//-----------------------------------------------------------------------------------
	function get_email_request($billid, $email){
		$this->db->where('billid', $billid);
		$this->db->where('email', $email);
		$query = $this->db->get('emailrequests');
		
		if ($query != false && $query->num_rows() > 0) {
			return get_object_vars($query->result()[0]);
		} else 
			return false;
	}
	
	function create_email_request($billid, $email,$fullname,&$time) {
			// Prepare the data to insert
		$time = $this->db->query("SELECT NOW()");
		$time = get_object_vars($time->result()[0])["NOW()"];
		$erequest_data = array(
			'billid' => $billid,
			'email' => $email,
			'fullname' => $fullname,
			'requestdate' => $time,
			'lastupdatetime' => $time,
			'isconfirmed' => false,
			'isdeleted' => false
		);
		$bill_data = array(
			'lastupdatetime' => $time
		);
		// Transaction
		$this->db->trans_start();
		$this->db->insert('emailrequests', $erequest_data);
		
		$this->db->where('billid', $billid);
		$this->db->where('email', $email);
		$query = $this->db->get('emailrequests');
			
		if ($query == true && $query->num_rows() > 0) {
			$erequest_data = get_object_vars($query->result()[0]);
				
			$this->db->where('billid', $billid);
			$this->db->update('bills', $bill_data);
		} else {
			log_message('info', 'In bill_model.php create_email_request() mysql error.');
		}
		$this->db->trans_complete();
		
		
		if ($this->db->trans_status() === false) {
			log_message('info', 'In bill_model.php create_email_request() transaction failed. ('.$billid.','.$email.')');
			return false;
		} else {
			return $erequest_data;
		}		
	}	
		
	function update_email_request($billid, $email, $fullname, $isdeleted,&$time) {
		$time = $this->db->query("SELECT NOW()");
		$time = get_object_vars($time->result()[0])["NOW()"];
		$update_data = array(
			'fullname' => $fullname,
			'isdeleted' => $isdeleted,
			'lastupdatetime' => $time
		);
		$bill_data = array(
			'lastupdatetime' => $time
		);
		
		$this->db->trans_start();
		
		// update bill
		$this->db->where('billid', $billid);
		$this->db->where('email', $email);		
		$this->db->update('emailrequests', $update_data);
		
		$this->db->where('billid', $billid);
		$this->db->update('bills', $bill_data);
		
		// get latest state of the email request item
		$this->db->where('billid', $billid);
		$this->db->where('email', $email);
		$query = $this->db->get('emailrequests');
		
		$this->db->trans_complete();
		
		if ($this->db->trans_status() == false || $query == false || $query->num_rows() == 0) {
			return false;
		} else {
			return get_object_vars($query->result()[0]);
		}
		
		return result;
	}
	
	function confirm_email_request($billid, $email,&$time) {
		$time = $this->db->query("SELECT NOW()");
		$time = get_object_vars($time->result()[0])["NOW()"];
		$update_data = array(
			'isconfirmed' => '1',
			'lastupdatetime' => $time
		);
		$bill_data = array(
			'lastupdatetime' => $time
		);
		
		$this->db->trans_start();
		
		// update bill
		$this->db->where('billid', $billid);
		$this->db->where('email', $email);		
		$this->db->update('emailrequests', $update_data);
		
		$this->db->where('billid', $billid);
		$this->db->update('bills', $bill_data);
		
		// get latest state of the email request item
		$this->db->where('billid', $billid);
		$this->db->where('email', $email);
		$query = $this->db->get('emailrequests');
		
		$this->db->trans_complete();
		
		if ($this->db->trans_status() == false || $query == false || $query->num_rows() == 0) {
			return false;
		} else {
			return get_object_vars($query->result()[0]);
		}
		
		return result;
	}
	
	function get_time() {
		$time = $this->db->query("SELECT NOW()");
		$time = get_object_vars($time->result()[0])["NOW()"];
		return $time;
	}
}