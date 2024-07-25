<?php
class C_discuss_model extends JI_Model
{
    public $tbl = 'f_discussion';
    public $tbl_as = 'fd';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';
    public $tbl3 = 'c_produk';
    public $tbl3_as = 'cp';

    //by Donny Dennison - 21 January 2021 10:32
    //show last report cdate
    public $tbl4 = 'f_discussion_report';
    public $tbl4_as = 'fdr';

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

    //by Donny Dennison - 21 January 2021 10:32
    //show last report cdate
    private function __joinTbl4()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl4_as.f_discussion_id");
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

    //by Donny Dennison - 21 January 2021 10:32
    //show last report cdate
    public function getTableAlias4()
    {
        return $this->tbl4_as;
    }

    public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="", $sortDir="ASC", $keyword="")
    {
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
        $this->db->select_as("$this->tbl_as.is_take_down", "takedown", 0);
        $this->db->select_as("$this->tbl_as.is_active", "active", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.parent_f_discussion_id", $this->db->esc(0), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(0), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.parent_f_discussion_id", $this->db->esc(0), "AND", "=", 0, 0);
        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl3_as.nama", addslashes($keyword), "AND", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.parent_f_discussion_id", $this->db->esc(0), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(0), "OR", "=", 0, 0);
            $this->db->where_as("COALESCE($this->tbl_as.text,'-')", addslashes($keyword), "AND", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.parent_f_discussion_id", $this->db->esc(0), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(0), "OR", "=", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "AND", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.parent_f_discussion_id", $this->db->esc(0), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(0), "AND", "=", 0, 0);
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get('', 0);
    }

    public function countAll($nation_code, $keyword="")
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.parent_f_discussion_id", $this->db->esc(0), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(0), "AND", "=", 0, 0);
        if (strlen($keyword)>1) {
            $this->db->where_as("$this->tbl3_as.nama", addslashes($keyword), "AND", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.parent_f_discussion_id", $this->db->esc(0), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(0), "OR", "=", 0, 0);
            $this->db->where_as("COALESCE($this->tbl_as.text,'-')", addslashes($keyword), "AND", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.parent_f_discussion_id", $this->db->esc(0), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(0), "OR", "=", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "AND", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.parent_f_discussion_id", $this->db->esc(0), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(0), "AND", "=", 0, 0);
        }
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getByIds($nation_code, $id, $page=0, $pagesize=10, $sortCol="", $sortDir="ASC", $keyword="")
    {
        $parent=0;
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"),'b_user_fnama');
        $this->db->select_as("$this->tbl_as.user_type", "user_type", 0);
        $this->db->select_as("$this->tbl_as.user_type", "user_type", 0);
        $this->db->select_as("$this->tbl_as.is_take_down", "takedown", 0);
        $this->db->select_as("$this->tbl_as.is_active", "active", 0);
        //$this->db->select_as("IF(COALESCE($this->tbl_as.b_user_id,0)=0,COALESCE($this->tbl4_as.nama,'-'),COALESCE(".$this->__decrypt("$this->tbl2_as.fnama").",'-'))", "b_user_fnama", 0);
        $this->db->select_as("$this->tbl_as.product_id", "product_id", 0);
        $this->db->select_as("$this->tbl3_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.text", "message", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.is_active", "active", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.parent_f_discussion_id", $this->db->esc($id), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(0), "AND", "=", 0, 0);
        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl3_as.nama", addslashes($keyword), "AND", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.parent_f_discussion_id", $this->db->esc($id), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(0), "OR", "=", 0, 0);
            $this->db->where_as("COALESCE($this->tbl_as.text,'-')", addslashes($keyword), "AND", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.parent_f_discussion_id", $this->db->esc($id), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(0), "OR", "=", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "AND", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.parent_f_discussion_id", $this->db->esc($id), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(0), "AND", "=", 0, 0);
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get('',0);
    }

    public function countAlls($nation_code, $id, $keyword)
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.parent_f_discussion_id", $this->db->esc($id), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(0), "AND", "=", 0, 0);
        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl3_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl_as.text,'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.parent_f_discussion_id", $this->db->esc($id), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(0), "AND", "=", 0, 0);
        }
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getByIdReport($nation_code, $page=0, $pagesize=10, $sortCol="", $sortDir="ASC", $keyword="")
    {
        $parent=0;
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"),'b_user_fnama');
        $this->db->select_as("$this->tbl_as.user_type", "user_type", 0);
        $this->db->select_as("$this->tbl_as.parent_f_discussion_id", "parent_id", 0);
        $this->db->select_as("$this->tbl_as.is_take_down", "takedown", 0);
        $this->db->select_as("$this->tbl_as.is_active", "active", 0);
        //$this->db->select_as("IF(COALESCE($this->tbl_as.b_user_id,0)=0,COALESCE($this->tbl4_as.nama,'-'),COALESCE(".$this->__decrypt("$this->tbl2_as.fnama").",'-'))", "b_user_fnama", 0);
        $this->db->select_as("$this->tbl_as.product_id", "product_id", 0);
        $this->db->select_as("$this->tbl3_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.text", "message", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);

        //by Donny Dennison - 21 January 2021 10:32
        //show last report cdate
        $this->db->select_as("MAX($this->tbl4_as.cdate)", "last_report_cdate", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");

        //by Donny Dennison - 21 January 2021 10:32
        //show last report cdate
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "inner");

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(1), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc(0), "AND", "=", 0, 0);
        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl3_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc(0), "AND", "=", 0, 0);
            $this->db->where_as("COALESCE($this->tbl_as.text,'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc(0), "AND", "=", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "AND", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc(0), "AND", "=", 0, 0);
        }

        //by Donny Dennison - 21 January 2021 10:32
        //show last report cdate
        $this->db->group_by("$this->tbl_as.id");

        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get('',0);
    }

    public function countAllReport($nation_code, $keyword='')
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(1), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc(0), "AND", "=", 0, 0);
        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl3_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc(0), "AND", "=", 0, 0);
            $this->db->where_as("COALESCE($this->tbl_as.text,'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc(0), "AND", "=", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_report", $this->db->esc(1), "AND", "=", 0, 0);
            $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc(0), "AND", "=", 0, 0);
        }
        $d = $this->db->get_first('', 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getByIdEdit($nation_code,$ids)
    {
        $parent=0;
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"),'b_user_fnama');
        $this->db->select_as("$this->tbl_as.user_type", "user_type", 0);
        $this->db->select_as("$this->tbl_as.user_type", "user_type", 0);
        $this->db->select_as("$this->tbl_as.is_take_down", "takedown", 0);
        $this->db->select_as("$this->tbl_as.is_active", "active", 0);
        //$this->db->select_as("IF(COALESCE($this->tbl_as.b_user_id,0)=0,COALESCE($this->tbl4_as.nama,'-'),COALESCE(".$this->__decrypt("$this->tbl2_as.fnama").",'-'))", "b_user_fnama", 0);
        $this->db->select_as("$this->tbl_as.product_id", "product_id", 0);
        $this->db->select_as("$this->tbl3_as.nama", "product", 0);
        $this->db->select_as("$this->tbl_as.text", "text", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($ids), "AND", "=", 0, 0);
        return $this->db->get_first();
    }

    public function getById($nation_code, $id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->get_first();
    }

    public function getByIdNotif($nation_code, $id)
    {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl3_as.nama", "product", 0);
        $this->db->select_as("$this->tbl_as.text", "text", 0);
        $this->db->select_as("$this->tbl_as.product_id", "product_id", 0);
        $this->db->select_as("$this->tbl_as.parent_f_discussion_id", "parent_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        return $this->db->get_first();
    }

    public function takedown($nation_code, $id, $takedown)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, array("is_take_down"=>1,"take_down_date"=>'NOW()',"is_report"=>0), 0);
    }

    public function active($nation_code, $id, $takedown)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, array("is_take_down"=>0,"is_report"=>0), 0);
    }

    public function ignore($nation_code, $id, $takedown)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, array("is_report"=>0), 0);
    }

    public function update($nation_code, $id,$du)
    {
        if(!is_array($du)) return 0;
        $this->db->where("nation_code",$nation_code);
            $this->db->where("id",$id);
        return $this->db->update($this->tbl,$du,0);
    }

    public function trans_start(){
        $r = $this->db->autocommit(0);
        if($r) return $this->db->begin();
        return false;
    }

    public function trans_commit(){
        return $this->db->commit();
    }

    public function trans_rollback(){
        return $this->db->rollback();
    }

    public function trans_end(){
        return $this->db->autocommit(1);
    }

    public function countAllChild($nation_code, $parent_id, $product_id){
        $this->db->flushQuery();
        $this->db->select_as("COUNT(*)","jumlah",0);
        $this->db->from($this->tbl,$this->tbl_as);
        $this->db->where("nation_code",$nation_code,"AND","=",0,0);
        $this->db->where("parent_f_discussion_id",$parent_id,"AND","=",0,0);
        $this->db->where("product_id",$product_id,"AND","=",0,0);
        $this->db->where("is_active", '1');
        $this->db->where("is_take_down", $this->db->esc('0'));
        $d = $this->db->get_first("object",0);
        if(isset($d->jumlah)) return $d->jumlah;
        return 0;
    }

    public function updateByParentDiscussionId($nation_code, $parent_discussion_id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where('parent_f_discussion_id', $parent_discussion_id);
        $this->db->where("is_active", 1);
        return $this->db->update($this->tbl, $du, 0);
    }
    
}
