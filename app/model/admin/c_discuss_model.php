<?php
class C_discuss_model extends JI_Model
{
    public $tbl = 'f_discussion';
    public $tbl_as = 'fd';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';
    public $tbl3 = 'c_produk';
    public $tbl3_as = 'cp';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
        return $cps;
    }

    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.product_id", "=", "$this->tbl3_as.id");
        return $cps;
    }

    public function getTableAlias()
    {
        return $this->tbl_as;
    }

    public function getTableAlias2()
    {
        return $this->tbl2_as;
    }

    public function getTableAlias3()
    {
        return $this->tbl3_as;
    }

    public function getById($nation_code,$id)
    {
        $parent=0;
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"),'b_user_fnama');
        $this->db->select_as("$this->tbl_as.user_type", "user_type", 0);
        //$this->db->select_as("IF(COALESCE($this->tbl_as.b_user_id,0)=0,COALESCE($this->tbl4_as.nama,'-'),COALESCE(".$this->__decrypt("$this->tbl2_as.fnama").",'-'))", "b_user_fnama", 0);
        $this->db->select_as("$this->tbl_as.product_id", "product_id", 0);
        $this->db->select_as("$this->tbl3_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.text", "message", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.parent_f_discussion_id", $this->db->esc($parent), "AND", ">", 0, 0);
        return $this->db->get_first('',0);
    }

    public function wadidaw()
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(1), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc(0), "AND", "=", 0, 0);
        return $this->db->get_first('',0);
    }
}
