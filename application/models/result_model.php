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
        $this->db->group_by(array("cellular", "exp_type", "result desc"));
        //$this->db->where('userid', $userid);
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
    public function summary_stat() {
        $this->db->select('simOperator, cellular, simCountryISO, count(distinct networkType) as networkTypeCount, count(distinct resid) as exp, count(distinct devideid) as deviceCountDistinct');
        $this->db->from('phones, results');
        $this->db->where('phones.username = results.username');
        $this->db->group_by(array("cellular"));
        $query = $this->db->get();
        
        return $query->result_array();
    }
}