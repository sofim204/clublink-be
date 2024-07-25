<?php
class B_User_Wish_Product_Model extends JI_Model
{
    public $tbl = 'b_user_wish_product';
    public $tbl_as = 'buwp';
    // public $tbl2 = 'c_produk';
    // public $tbl2_as = 'cp';

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

    // private function __joinTbl2()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl_as.c_produk_id", "=", "$this->tbl2_as.id");
    //     return $composites;
    // }

    // public function set($di)
    // {
    //     if (!is_array($di)) {
    //         return 0;
    //     }
    //     return $this->db->insert($this->tbl, $di, 0, 0);
    // }

    public function delete($nation_code, $c_produk_id, $b_user_id="")
    {
        $this->db->where("nation_code",$nation_code);
        $this->db->where("c_produk_id",$c_produk_id);
        if($b_user_id){
            $this->db->where("b_user_id",$b_user_id);
        }
        return $this->db->delete($this->tbl);
    }

    // public function countAll($nation_code, $b_user_id="")
    // {
    //     $this->db->exec("SET NAMES 'UTF8MB4'");
    //     $this->db->select_as("COUNT($this->tbl_as.c_produk_id)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));

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
    //     $this->db->select_as("$this->tbl2_as.foto", "foto", 0);
    //     $this->db->select_as("$this->tbl2_as.thumb", "thumb", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        
    //     $this->db->page($page, $page_size);

    //     return $this->db->get('object', 0);
    // }
}
