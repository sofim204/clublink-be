<?php
class G_General_Location_Highlight_Status_Model extends JI_Model {
    var $tbl = 'g_general_location_highlight_status';
    var $tbl_as = 'gwn';
	var $tbl2 = 'g_highlight_community';
	var $tbl2_as = 'ghc';

    public function __construct() {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function trans_start() {
        $r = $this->db->autocommit(0);
        if ($r) {
            return $this->db->begin();
        }
        return false;
    }

    public function trans_commit() {
        return $this->db->commit();
    }

    public function trans_rollback() {
        return $this->db->rollback();
    }

    public function trans_end() {
        return $this->db->autocommit(1);
    }

    public function getTblAs() {
        return $this->tbl_as;
    }

    public function getByLocation($nation_code, $location='') {
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_kelurahan", "status_kelurahan", 0);
        $this->db->select_as("$this->tbl_as.status ", "status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_alamat_location_kelurahan", $this->db->esc($location));
        return $this->db->get_first('object', 0);
    }
}