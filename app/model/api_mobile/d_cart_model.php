<?php
class D_Cart_Model extends JI_Model
{
    public $tbl = 'd_cart';
    public $tbl_as = 'dc';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';
    public $tbl3 = 'c_produk';
    public $tbl3_as = 'cp';
    public $tbl4 = 'b_kategori';
    public $tbl4_as = 'bk';
    public $tbl5 = 'b_berat';
    public $tbl5_as = 'bbi';
    public $tbl6 = 'b_kondisi';
    public $tbl6_as = 'bki';
    public $tbl7 = 'b_user';
    public $tbl7_as = 'bu2';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $cps   = array();
        $cps[] = $this->db->composite_create("$this->tbl3_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl3_as.b_user_id", "=", "$this->tbl2_as.id");
        return $cps;
    }

    private function __joinTbl3()
    {
        $cps   = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.c_produk_id", "=", "$this->tbl3_as.id");
        return $cps;
    }

    private function __joinTbl4()
    {
        $cps   = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl3_as.b_kategori_id", "=", "$this->tbl4_as.id");
        return $cps;
    }

    private function __joinTbl5()
    {
        $cps   = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl5_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl3_as.b_berat_id", "=", "$this->tbl5_as.id");
        return $cps;
    }

    private function __joinTbl6()
    {
        $cps   = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl6_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl3_as.b_kondisi_id", "=", "$this->tbl6_as.id");
        return $cps;
    }


    private function __joinTbl7()
    {
        $cps   = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl7_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl3_as.b_user_id", "=", "$this->tbl7_as.id");
        return $cps;
    }
    public function getTableAlias()
    {
        return $this->tbl_as;
    }

    public function getLastId($nation_code, $c_produk_id, $b_user_id)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("c_produk_id", $c_produk_id);
        $this->db->where("nation_code", $nation_code);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }

    public function set($di=array())
    {
        $this->db->flushQuery();
        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    public function update($nation_code, $b_user_id, $c_produk_id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("c_produk_id", $c_produk_id);
        return $this->db->update($this->tbl, $du, 0);
    }

    public function del($nation_code, $b_user_id, $c_produk_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("c_produk_id", $c_produk_id);
        $this->db->where("b_user_id", $b_user_id);
        return $this->db->delete($this->tbl);
    }

    public function getAll($nation_code, $b_user_id, $page=0, $pagesize=1000, $sortCol="id", $sortDir="desc", $keyword="", $sdate="", $edate="")
    {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl3_as.id", "id", 0);
        $this->db->select_as("$this->tbl3_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl3_as.b_kategori_id", "b_kategori_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_cart_id", 0);
        $this->db->select_as("$this->tbl4_as.nama", "kategori", 0);
        $this->db->select_as("$this->tbl2_as.id", "b_user_id_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama_seller", 0);
        $this->db->select_as("$this->tbl2_as.image", "b_user_image_seller", 0);
        $this->db->select_as("$this->tbl3_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl3_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl3_as.dimension_long", "dimension_long", 0);
        $this->db->select_as("$this->tbl3_as.dimension_width", "dimension_width", 0);
        $this->db->select_as("$this->tbl3_as.dimension_height", "dimension_height", 0);
        $this->db->select_as("$this->tbl3_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl3_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl3_as.stok", "stok", 0);
        $this->db->select_as("$this->tbl3_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
        $this->db->select_as("$this->tbl3_as.is_published", "is_published", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->select_as("$this->tbl2_as.is_active", "b_user_is_active", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3()); //required at first
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2());
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
        if (strlen($keyword)>1) {
            $this->db->where_as("$this->tbl2_as.nama", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl2_as.kondisi", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get("object", 0);
    }

    public function countAll($nation_code, $b_user_id, $keyword="", $sdate="", $edate="")
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3());
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2());
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
        if (strlen($keyword)>1) {
            $this->db->where_as("$this->tbl2_as.nama", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl2_as.kondisi", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getById($nation_code, $b_user_id, $c_produk_id)
    {
        $this->db->select_as("$this->tbl3_as.id", "id", 0);
        $this->db->select_as("$this->tbl3_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl3_as.b_kategori_id", "b_kategori_id", 0);
        $this->db->select_as("$this->tbl4_as.nama", "kategori", 0);
        $this->db->select_as("$this->tbl2_as.id", "b_user_id_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama_seller", 0);
        $this->db->select_as("$this->tbl2_as.image", "b_user_image_seller", 0);
        $this->db->select_as("$this->tbl3_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl3_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl3_as.dimension_long", "dimension_long", 0);
        $this->db->select_as("$this->tbl3_as.dimension_width", "dimension_width", 0);
        $this->db->select_as("$this->tbl3_as.dimension_height", "dimension_height", 0);
        $this->db->select_as("$this->tbl3_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl3_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl3_as.stok", "stok", 0);
        $this->db->select_as("$this->tbl3_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
        $this->db->select_as("$this->tbl3_as.is_published", "is_published", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);

        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3());
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2());
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.c_produk_id", $c_produk_id);
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
        return $this->db->get_first('', 0);
    }

    public function check($nation_code, $b_user_id, $c_produk_id)
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("$this->tbl_as.nation_code", $nation_code);
        $this->db->where("$this->tbl_as.b_user_id", $b_user_id);
        $this->db->where("$this->tbl_as.c_produk_id", $c_produk_id);
        $d = $this->db->get_first("", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }
    public function getByUserId($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl3_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl3_as.id", "id", 0);
        $this->db->select_as("$this->tbl3_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl3_as.b_user_alamat_id", "b_user_alamat_id", 0);
        $this->db->select_as("$this->tbl3_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_cart_id", 0);
        $this->db->select_as("$this->tbl3_as.brand", "brand", 0);
        $this->db->select_as("$this->tbl3_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl3_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl3_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl3_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl3_as.stok", "stok", 0);
        $this->db->select_as("$this->tbl3_as.satuan", "satuan", 0);
        $this->db->select_as("$this->tbl3_as.berat", "berat", 0);
        $this->db->select_as("$this->tbl3_as.dimension_long", "dimension_long", 0);
        $this->db->select_as("$this->tbl3_as.dimension_width", "dimension_width", 0);
        $this->db->select_as("$this->tbl3_as.dimension_height", "dimension_height", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3());
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2());
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6());
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
        $this->db->order_by("$this->tbl_as.cdate", "DESC");
        return $this->db->get();
    }
    public function getSellers($nation_code, $b_user_id, $c_produk_ids=array())
    {
        $this->db->select_as("$this->tbl7_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl7_as.id", "id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl7_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl7_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl7_as.telp"), "telp", 0);
        $this->db->select_as("$this->tbl7_as.image", "image", 0);
        $this->db->select_as("$this->tbl7_as.intro_teks", "intro_teks", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3());
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7()); //user seller
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl7_as.is_active", $this->db->esc(1));
        $this->db->group_by("$this->tbl7_as.id");
        if (is_array($c_produk_ids) && count($c_produk_ids)>0) {
            $this->db->where_in("$this->tbl_as.c_produk_id", $c_produk_ids);
        }
        $this->db->order_by("$this->tbl_as.cdate", "DESC");
        return $this->db->get();
    }
    public function getCartProductByProductIds($nation_code, $b_user_id, $c_produk_ids)
    {
        $this->db->select_as("$this->tbl3_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl3_as.id", "id", 0);
        $this->db->select_as("$this->tbl3_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl3_as.b_user_alamat_id", "b_user_alamat_id", 0);
        $this->db->select_as("$this->tbl3_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_cart_id", 0);
        $this->db->select_as("$this->tbl3_as.brand", "brand", 0);
        $this->db->select_as("$this->tbl3_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl3_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl3_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl3_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl3_as.stok", "stok", 0);
        $this->db->select_as("$this->tbl3_as.satuan", "satuan", 0);
        $this->db->select_as("$this->tbl3_as.berat", "berat", 0);
        $this->db->select_as("$this->tbl3_as.dimension_long", "dimension_long", 0);
        $this->db->select_as("$this->tbl3_as.dimension_width", "dimension_width", 0);
        $this->db->select_as("$this->tbl3_as.dimension_height", "dimension_height", 0);
        $this->db->select_as("$this->tbl3_as.courier_services", "courier_services", 0);
        $this->db->select_as("$this->tbl3_as.services_duration", "services_duration", 0);
        $this->db->select_as("$this->tbl3_as.berat", "produk_berat", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3());
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2());
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6());
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
        if (is_array($c_produk_ids) && count($c_produk_ids)>0) {
            $this->db->where_in("$this->tbl_as.c_produk_id", $c_produk_ids);
        }
        return $this->db->get();
    }
    public function delByProductIds($nation_code, $b_user_id, $c_produk_ids)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where_in("c_produk_id", $c_produk_ids);
        return $this->db->delete($this->tbl, 0);
    }
    public function delAllByProdukIds($nation_code, $c_produk_ids)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where_in("c_produk_id", $c_produk_ids);
        return $this->db->delete($this->tbl, 0);
    }
    // public function syncQty(){
    //   $sql = 'UPDATE d_cart c JOIN c_produk p ON c.c_produk_id = p.id SET c.qty = p.stok WHERE c.qty > p.stok';
    //   return $this->db->exec($sql);
    // }
}
