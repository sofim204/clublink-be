<?php
class G_Blacklist_Model extends JI_Model
{
    public $tbl = 'g_blacklist';
    public $tbl_as = 'gb';
   
    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    // private function __joinTbl2()
    // {
    //     $cps   = array();
    //     $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
    //     $cps[] = $this->db->composite_create("$this->tbl_as.c_produk_id", "=", "$this->tbl2_as.id");
    //     return $cps;
    // }

    // public function getTableAlias()
    // {
    //     return $this->tbl_as;
    // }

    // public function getLastId($cdate, $mobile_type="") {
    //     $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $d = $this->db->get_first('', 0);
    //     if (isset($d->last_id)) {
    //         return $d->last_id;
    //     }
    //     return 0;
    // }

    // public function getLatestVisit($nation_code, $mobile_type)
    // {
    //     $this->db->flushQuery();
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", 'id', 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.mobile_type", $mobile_type);
    //     $this->db->order_by("$this->tbl_as.cdate","desc");
    //     return $this->db->get_first("", 0);
    // }

    // public function set($di) {
    //     return $this->db->insert($this->tbl, $di, 0, 0);
    // }

    // public function update($nation_code, $mobile_type)
    // {

    //     $dateNow = date('Y-m-d');

    //     $this->db->where('nation_code', $nation_code);
    //     $this->db->where('mobile_type', $mobile_type);
    //     $this->db->where('cdate', $dateNow);
    //     $du = array();
    //     $du['`total_visit`'] = '`total_visit` + 1';
    //     return $this->db->update_as($this->tbl,$du,0);

    // }

    // public function del($nation_code, $b_user_id, $c_produk_id)
    // {
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("b_user_id", $b_user_id);
    //     $this->db->where("c_produk_id", $c_produk_id);
    //     return $this->db->delete($this->tbl);
    // }

    public function getAll($nation_code)
    {
        $this->db->select_as("$this->tbl_as.*,$this->tbl_as.id", "id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        return $this->db->get("object", 0);
    }

    // public function countAll($nation_code, $b_user_id, $keyword="", $sdate="", $edate="")
    // {
    //     $this->db->flushQuery();
    //     $this->db->select_as("COUNT($this->tbl_as.id)", "jumlah", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2());
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3());
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4());
    //     $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
    //     $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), 'left');
    //     $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.b_user_id", $b_user_id);
    //     if (strlen($keyword)>1) {
    //         $this->db->where_as("$this->tbl2_as.nama", $keyword, "OR", "%like%", 1, 0);
    //         $this->db->where_as("$this->tbl2_as.kondisi", $keyword, "OR", "%like%", 0, 0);
    //         $this->db->where_as("$this->tbl2_as.deskripsi", $keyword, "OR", "%like%", 0, 1);
    //     }
    //     $d = $this->db->get_first("object", 0);
    //     if (isset($d->jumlah)) {
    //         return $d->jumlah;
    //     }
    //     return 0;
    // }

    // public function getById($nation_code, $b_user_id, $c_produk_id, $id)
    // {
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("b_user_id", $b_user_id);
    //     $this->db->where("c_produk_id", $c_produk_id);
    //     $this->db->where("id", $id);
    //     return $this->db->get_first();
    // }

    // public function delByUserId($nation_code, $b_user_id, $c_produk_id)
    // {
    //     $this->db->where("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where("b_user_id", $b_user_id);
    //     $this->db->where("c_produk_id", $c_produk_id);
    //     return $this->db->delete($this->tbl);
    // }

    public function check($nation_code, $type, $text)
    {
        $this->db->select_as("$this->tbl_as.*,$this->tbl_as.id", "id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
        $this->db->where_as("$this->tbl_as.text", $this->db->esc($text));
        return $this->db->get_first("object", 0);
    }

}
