<?php
class D_Wishlist_Model extends JI_Model
{
    public $tbl = 'd_wishlist';
    public $tbl_as = 'dwl';
    public $tbl2 = 'c_produk';
    public $tbl2_as = 'cp';
    public $tbl3 = 'b_user';
    public $tbl3_as = 'bu';
    public $tbl4 = 'b_kategori';
    public $tbl4_as = 'bk';
    public $tbl5 = 'b_kondisi';
    public $tbl5_as = 'bko';
    public $tbl6 = 'b_berat';
    public $tbl6_as = 'bb';
    public $tbl7 = 'c_produk_detail_automotive';//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    public $tbl7_as = 'cpda';//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30

    // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
    // public $tbl8 = 'b_kategori';//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    // public $tbl8_as = 'bk_par';//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $cps   = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.c_produk_id", "=", "$this->tbl2_as.id");
        return $cps;
    }

    private function __joinTbl3()
    {
        $cps   = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl3_as.id");
        return $cps;
    }

    private function __joinTbl4()
    {
        $cps   = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl2_as.b_kategori_id", "=", "$this->tbl4_as.id");
        return $cps;
    }

    private function __joinTbl5()
    {
        $cps   = array();
        $cps[] = $this->db->composite_create("$this->tbl5_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl5_as.id", "=", "$this->tbl2_as.b_kondisi_id");
        return $cps;
    }

    private function __joinTbl6()
    {
        $cps   = array();
        $cps[] = $this->db->composite_create("$this->tbl6_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl6_as.id", "=", "COALESCE($this->tbl2_as.b_berat_id,0)");
        return $cps;
    }

    /**
     * Composite join for multiple PK on table 7
     * @return array composites join
     */
    private function __joinTbl7()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl7_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl2_as.id", "=", "$this->tbl7_as.c_produk_id");
        return $composites;
    }

    // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
    // /**
    //  * Composite join for multiple PK on table 8
    //  * @return array composites join
    //  */
    // private function __joinTbl8()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl4_as.parent_nation_code", "=", "$this->tbl8_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl4_as.parent_b_kategori_id", "=", "$this->tbl8_as.id");
    //     return $composites;
    // }

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

    public function update($nation_code, $c_produk_id, $b_user_id, $id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("c_produk_id", $c_produk_id);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }

    public function del($nation_code, $b_user_id, $c_produk_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("c_produk_id", $c_produk_id);
        return $this->db->delete($this->tbl);
    }

    public function getAll($nation_code, $b_user_id, $page=0, $page_size=10, $sort_col="id", $sort_direction="desc", $keyword="", $pelanggan, $language_id=2)
    {
        $this->db->flushQuery();
        $this->db->select_as("DISTINCT ($this->tbl2_as.id)", "id", 0);

        // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl8_as.id, $this->tbl2_as.b_kategori_id)", "b_kategori_id", 0);
        // $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl8_as.nama, $this->tbl4_as.nama)", "kategori", 0);
        $this->db->select_as("$this->tbl2_as.b_kategori_id", "b_kategori_id", 0);
        // $this->db->select_as("$this->tbl4_as.nama", "kategori", 0);
        $this->db->select_as("IF($language_id = 4 AND $this->tbl4_as.thailand IS NOT NULL AND $this->tbl4_as.thailand != '', $this->tbl4_as.thailand, IF($language_id = 3 AND $this->tbl4_as.korea IS NOT NULL AND $this->tbl4_as.korea != '', $this->tbl4_as.korea, IF($language_id = 2 AND $this->tbl4_as.indonesia IS NOT NULL AND $this->tbl4_as.indonesia != '', $this->tbl4_as.indonesia, $this->tbl4_as.nama)))", "kategori");

        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "b_user_fnama_seller", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.image,'-')", "b_user_image_seller", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.id,'0')", "b_kondisi_id", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.nama,'-')", "b_kondisi_nama", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.id,'0')", "b_berat_id", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.nama,'-')", "b_berat_nama", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.icon,'media/icon/default-icon.png')", "b_berat_icon", 0);

        $this->db->select_as("COALESCE($this->tbl7_as.model,'')", "c_produk_detail_automotive_model", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        $this->db->select_as("COALESCE($this->tbl7_as.color,'')", "c_produk_detail_automotive_color", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        $this->db->select_as("COALESCE($this->tbl7_as.year,'')", "c_produk_detail_automotive_year", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30

        // START by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl2_as.b_kategori_id, '')", "sub_kategori_id", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl4_as.nama, '')", "sub_kategori", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl4_as.image_icon, '')", "sub_kategori_icon", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl4_as.image_cover, '')", "sub_kategori_icon_selected", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // END by Muhammad Sofi - 15 November 2021 10:17
        
        $this->db->select_as("$this->tbl2_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl2_as.brand", "brand", 0);
        $this->db->select_as("$this->tbl2_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.berat", "berat", 0);
        $this->db->select_as("$this->tbl2_as.dimension_long", "dimension_long", 0);
        $this->db->select_as("$this->tbl2_as.dimension_width", "dimension_width", 0);
        $this->db->select_as("$this->tbl2_as.dimension_height", "dimension_height", 0);
        $this->db->select_as("$this->tbl2_as.stok", "stok", 0);
        $this->db->select_as("$this->tbl2_as.satuan", "satuan", 0);
        $this->db->select_as("$this->tbl2_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl2_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl2_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
        
        // if(isset($pelanggan->id)){
        //     $this->db->select_as("IF( (SELECT COUNT(*) FROM e_likes WHERE type = 'product' AND custom_id = $this->tbl2_as.id AND b_user_id = '$pelanggan->id' AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_liked", 0);
        // }else{
            $this->db->select_as("(0)", "is_liked", 0);
        // }

        $this->db->select_as("COALESCE($this->tbl2_as.b_user_alamat_id,0)", "b_user_alamat_id", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.b_lokasi_id,0)", "b_lokasi_id", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl2_as.alamat2"), "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl2_as.kelurahan, ', ', $this->tbl2_as.kabkota)", "alamat2", 0);
        $this->db->select_as("$this->tbl2_as.kodepos", "kodepos", 0);
        $this->db->select_as("$this->tbl2_as.latitude", "latitude", 0);
        $this->db->select_as("$this->tbl2_as.longitude", "longitude", 0);
        // $this->db->select_as("'-'", "provinsi", 0);
        // $this->db->select_as("'-'", "kabkota", 0);
        // $this->db->select_as("'-'", "kecamatan", 0);
        // $this->db->select_as("'-'", "kelurahan", 0);
        // $this->db->select_as("'-'", "negara", 0);

        //by Donny Dennison - 17 december 2020 11:09
        //add new product type(meetup)
        $this->db->select_as("$this->tbl2_as.product_type", "product_type", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), 'left');
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30

        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        if (strlen($keyword)>1) {
            $this->db->where_as("$this->tbl2_as.nama", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl2_as.kondisi", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl2_as.deskripsi", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $this->db->order_by($sort_col, $sort_direction)->page($page, $page_size);
        return $this->db->get("object", 0);
    }

    public function countAll($nation_code, $b_user_id, $keyword="", $sdate="", $edate="")
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT($this->tbl_as.id)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2());
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3());
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4());
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        if (strlen($keyword)>1) {
            $this->db->where_as("$this->tbl2_as.nama", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl2_as.kondisi", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl2_as.deskripsi", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getByUserId($b_user_id, $page=0, $pagesize=10, $sortCol="id", $sortDir="desc", $keyword="", $sdate="", $edate="")
    {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl2_as.id", "id", 0);

        // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl8_as.id, $this->tbl2_as.b_kategori_id)", "b_kategori_id", 0);
        // $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl8_as.nama, $this->tbl4_as.nama)", "kategori", 0);
        $this->db->select_as("$this->tbl2_as.b_kategori_id", "b_kategori_id", 0);
        $this->db->select_as("$this->tbl4_as.nama", "kategori", 0);
        
        $this->db->select_as("$this->tbl3_as.id", "b_user_id_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "b_user_fnama_seller", 0);
        $this->db->select_as("$this->tbl3_as.image", "b_user_image_seller", 0);

        $this->db->select_as("COALESCE($this->tbl7_as.model,'')", "c_produk_detail_automotive_model", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        $this->db->select_as("COALESCE($this->tbl7_as.color,'')", "c_produk_detail_automotive_color", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        $this->db->select_as("COALESCE($this->tbl7_as.year,'')", "c_produk_detail_automotive_year", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // START by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl2_as.b_kategori_id, '')", "sub_kategori_id", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl4_as.nama, '')", "sub_kategori", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl4_as.image_icon, '')", "sub_kategori_icon", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl4_as.image_cover, '')", "sub_kategori_icon_selected", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // END by Muhammad Sofi - 15 November 2021 10:17
        
        $this->db->select_as("$this->tbl2_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl2_as.deskripsi_singkat", "deskripsi_singkat", 0);
        $this->db->select_as("$this->tbl2_as.deskripsi", "deskripsi", 0);
        $this->db->select_as("$this->tbl2_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.dimension_long", "dimension_long", 0);
        $this->db->select_as("$this->tbl2_as.dimension_width", "dimension_width", 0);
        $this->db->select_as("$this->tbl2_as.dimension_height", "dimension_height", 0);
        $this->db->select_as("$this->tbl2_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl2_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl2_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
        $this->db->select_as("$this->tbl2_as.is_published", "is_published", 0);

        // $this->db->select_as($this->__decrypt("$this->tbl2_as.alamat2"), "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl2_as.kelurahan, ', ', $this->tbl2_as.kabkota)", "alamat2", 0);
        $this->db->select_as("$this->tbl2_as.kodepos", "kodepos", 0);
        $this->db->select_as("$this->tbl2_as.latitude", "latitude", 0);
        $this->db->select_as("$this->tbl2_as.longitude", "longitude", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2());
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3());
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4());
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
        // $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        if (strlen($keyword)>1) {
            $this->db->where_as("$this->tbl2_as.nama", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl2_as.kondisi", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl2_as.deskripsi", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get("object", 0);
    }

    public function countByUserId($b_user_id, $keyword="", $sdate="", $edate="")
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2());
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3());
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4());
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        if (strlen($keyword)>1) {
            $this->db->where_as("$this->tbl2_as.nama", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl2_as.kondisi", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl2_as.deskripsi", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getById($nation_code, $b_user_id, $c_produk_id, $id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("c_produk_id", $c_produk_id);
        $this->db->where("id", $id);
        return $this->db->get_first();
    }

    public function delByUserId($nation_code, $b_user_id, $c_produk_id)
    {
        $this->db->where("$this->tbl_as.nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("c_produk_id", $c_produk_id);
        return $this->db->delete($this->tbl);
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
    public function delAllByProdukIds($nation_code, $c_produk_ids)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where_in("c_produk_id", $c_produk_ids);
        return $this->db->delete($this->tbl, 0);
    }
}
