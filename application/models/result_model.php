<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Result_model extends CI_Model {
    public function store_result($result_data) {
        $success = TRUE;

        // Transaction
        $this->db->trans_start();
        if (!$this->db->insert('results', $result_data))
            $success = FALSE;
        $this->db->trans_complete();

        return $success;
    }
}