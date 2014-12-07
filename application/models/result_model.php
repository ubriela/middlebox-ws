<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Result_model extends CI_Model {
    public function store_result($result_data) {
        $success = TRUE;

        // Generate unique id from application/helpers/uuid_helper.php
        $uuid = gen_uuid();
        $result_data['resid'] = $uuid;
        
        // Transaction
        $this->db->trans_start();
        if (!$this->db->insert('results', $result_data))
            $success = FALSE;
        $this->db->trans_complete();

        return $success;
    }
    
    public function summary_all() {
        $this->db->select('cellular, exp_type, result, count(*) as count');
        $this->db->from('results');
        $this->db->group_by(array("cellular", "exp_type"));
        //$this->db->where('userid', $userid);
        $query = $this->db->get();
        
        return $query->result_array();
//        
//        if($query->num_rows()>0){
//            $array = array();
//            foreach ($query->result_array() as $row)
//                {
//                    $userid = $row['userid'];
//                    $row['numrequest'] = $this->get_num_taskrequests($userid);
//                    $row['numresponse'] = $this->get_num_taskresponses($userid);
//                    $array[]=$row;
//                }
//            return $array;
//        }else{
//            return false;
//        }
    }
}