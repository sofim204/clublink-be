<?php
class B_User_Wish_Product_Model extends JI_Model
{
    public $tbl = 'b_user_wish_product';
    public $tbl_as = 'buwp';
    public $tbl2 = 'c_produk';
    public $tbl2_as = 'cp';
    public $tbl3 = 'b_user';
    public $tbl3_as = 'bu';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.c_produk_id", "=", "$this->tbl2_as.id");
        return $composites;
    }

    private function __joinTbl3()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl3_as.id");
        return $composites;
    }

    public function getLastId($nation_code, $b_user_id)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return (int) $d->last_id;
        }
        return 0;
    }

    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    // public function delete($nation_code, $c_produk_id, $b_user_id="")
    // {
    //     $this->db->where("nation_code",$nation_code);
    //     $this->db->where("c_produk_id",$c_produk_id);
    //     if($b_user_id){
    //         $this->db->where("b_user_id",$b_user_id);
    //     }
    //     return $this->db->delete($this->tbl);
    // }

    // public function countAll($nation_code, $b_user_id="")
    // {
    //     $this->db->exec("SET NAMES 'UTF8MB4'");
    //     $this->db->select_as("COUNT($this->tbl_as.c_produk_id)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));

    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }

    // public function getAll($nation_code, $page=1, $page_size=10, $b_user_id="")
    // {
    //     $this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'");
    //     $this->db->select_as("$this->tbl_as.c_produk_id", "c_produk_id", 0);
    //     $this->db->select_as("$this->tbl2_as.nama", "nama", 0);
    //     $this->db->select_as("$this->tbl2_as.harga_jual", "harga_jual", 0);
    //     $this->db->select_as("$this->tbl2_as.product_type", "product_type", 0);
    //     $this->db->select_as("$this->tbl2_as.foto", "foto", 0);
    //     $this->db->select_as("$this->tbl2_as.thumb", "thumb", 0);
    //     $this->db->select_as("$this->tbl2_as.stok", "stok", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        
    //     $this->db->page($page, $page_size);
    //     $this->db->order_by("$this->tbl_as.cdate","DESC");

    //     return $this->db->get('object', 0);
    // }

    public function getByProductIDUserID($nation_code, $c_produk_id, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.c_produk_id", "c_produk_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.c_produk_id", $this->db->esc($c_produk_id));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));

        return $this->db->get_first('object', 0);
    }

}
