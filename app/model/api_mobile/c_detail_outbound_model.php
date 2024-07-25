<?php
class C_Detail_Outbound_Model extends JI_Model
{
    public $tbl = 'c_detail_outbounding';
    public $tbl_as = 'codm';


    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function trans_start()
    {
        $r = $this->db->autocommit(0);
        if ($r) {
            return $this->db->begin();
        }
        return false;
    }
    public function trans_commit()
    {
        return $this->db->commit();
    }
    public function trans_rollback()
    {
        return $this->db->rollback();
    }
    public function trans_end()
    {
        return $this->db->autocommit(1);
    }

    public function getByIdP($nation_code, $id, $typeP)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.c_outbound_id", $this->db->esc($id), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.type", $this->db->esc($typeP), "AND", "=", 0, 0);
        return $this->db->get();
    }

    public function getByIdS($nation_code, $id, $typeS)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.c_outbound_id", $this->db->esc($id), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.type", $this->db->esc($typeS), "AND", "=", 0, 0);
        return $this->db->get();
    }

    public function getByIdO($nation_code, $id, $typeO)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.c_outbound_id", $this->db->esc($id), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.type", $this->db->esc($typeO), "AND", "=", 0, 0);
        return $this->db->get();
    }
}
