<?php
class F_Visitor_Model extends JI_Model
{
    public $tbl = 'f_visitor';
    public $tbl_as = 'fv';
    public $tbl2 = 'f_visitor_history';
	public $tbl2_as = 'fvh';
    public $tbl3 = 'b_user_alamat';
	public $tbl3_as = 'bua';
   
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

    public function getLastId($cdate, $mobile_type="") {
        $this->db->select_as("COALESCE(MAX($this->tbl2_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl2, $this->tbl2_as);
        $this->db->where_as("DATE($this->tbl2_as.cdate)", "DATE('$cdate')", 'AND', '=');
        $this->db->where_as("$this->tbl2_as.mobile_type", $this->db->esc($mobile_type));
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }

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

    public function addLog($di) {
        $this->db->flushQuery();
        return $this->db->insert($this->tbl2, $di, 0, 0);
    }

    public function update($nation_code, $mobile_type)
    {

        $dateNow = date('Y-m-d');

        $this->db->where('nation_code', $nation_code);
        $this->db->where('mobile_type', $mobile_type);
        $this->db->where('cdate', $dateNow);
        $du = array();
        $du['`total_visit`'] = '`total_visit` + 1';
        return $this->db->update_as($this->tbl,$du,0);

        // return $this->db->exec('UPDATE '.$this->tbl.' SET total_visit = total_visit + 1 WHERE nation_code = '.$nation_code.' AND mobile_type = "'.$mobile_type.'" AND cdate = "'.$dateNow.'"');

    }

    // public function del($nation_code, $b_user_id, $c_produk_id)
    // {
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("b_user_id", $b_user_id);
    //     $this->db->where("c_produk_id", $c_produk_id);
    //     return $this->db->delete($this->tbl);
    // }

    // public function getAll($nation_code, $b_user_id, $page=0, $page_size=10, $sort_col="id", $sort_direction="desc", $keyword="", $sdate="", $edate="")
    // {
    //     $this->db->flushQuery();
    //     $this->db->select_as("DISTINCT ($this->tbl2_as.id)", "id", 0);
    //     $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl8_as.id, $this->tbl2_as.b_kategori_id)", "b_kategori_id", 0);
    //     $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl8_as.nama, $this->tbl4_as.nama)", "kategori", 0);
    //     $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_seller", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "b_user_fnama_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl3_as.image,'-')", "b_user_image_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.id,'0')", "b_kondisi_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.nama,'-')", "b_kondisi_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.id,'0')", "b_berat_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.nama,'-')", "b_berat_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.icon,'media/icon/default-icon.png')", "b_berat_icon", 0);
    //     $this->db->select_as("COALESCE($this->tbl7_as.model,'')", "c_produk_detail_automotive_model", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->select_as("COALESCE($this->tbl7_as.color,'')", "c_produk_detail_automotive_color", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->select_as("COALESCE($this->tbl7_as.year,'')", "c_produk_detail_automotive_year", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl2_as.b_kategori_id, '')", "sub_kategori_id", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl4_as.nama, '')", "sub_kategori", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl4_as.image_icon, '')", "sub_kategori_icon", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl4_as.image_cover, '')", "sub_kategori_icon_selected", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->select_as("$this->tbl2_as.nama", "nama", 0);
    //     $this->db->select_as("$this->tbl2_as.brand", "brand", 0);
    //     $this->db->select_as("$this->tbl2_as.harga_jual", "harga_jual", 0);
    //     $this->db->select_as("$this->tbl2_as.berat", "berat", 0);
    //     $this->db->select_as("$this->tbl2_as.dimension_long", "dimension_long", 0);
    //     $this->db->select_as("$this->tbl2_as.dimension_width", "dimension_width", 0);
    //     $this->db->select_as("$this->tbl2_as.dimension_height", "dimension_height", 0);
    //     $this->db->select_as("$this->tbl2_as.stok", "stok", 0);
    //     $this->db->select_as("$this->tbl2_as.satuan", "satuan", 0);
    //     $this->db->select_as("$this->tbl2_as.foto", "foto", 0);
    //     $this->db->select_as("$this->tbl2_as.thumb", "thumb", 0);
    //     $this->db->select_as("$this->tbl2_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
    //     $this->db->select_as("(1)", "is_liked", 0);

    //     $this->db->select_as("COALESCE($this->tbl2_as.b_user_alamat_id,0)", "b_user_alamat_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl2_as.b_lokasi_id,0)", "b_lokasi_id", 0);
    //     $this->db->select_as("'-'", "alamat", 0);
    //     $this->db->select_as("'-'", "alamat2", 0);
    //     $this->db->select_as("'0.0'", "latitude", 0);
    //     $this->db->select_as("'0.0'", "longitude", 0);
    //     $this->db->select_as("'-'", "provinsi", 0);
    //     $this->db->select_as("'-'", "kabkota", 0);
    //     $this->db->select_as("'-'", "kecamatan", 0);
    //     $this->db->select_as("'-'", "kelurahan", 0);
    //     $this->db->select_as("'-'", "kodepos", 0);
    //     $this->db->select_as("'-'", "negara", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
    //     $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), 'left');
    //     $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30

    //     $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.b_user_id", $b_user_id);
    //     if (strlen($keyword)>1) {
    //         $this->db->where_as("$this->tbl2_as.nama", $keyword, "OR", "%like%", 1, 0);
    //         $this->db->where_as("$this->tbl2_as.kondisi", $keyword, "OR", "%like%", 0, 0);
    //         $this->db->where_as("$this->tbl2_as.deskripsi", $keyword, "OR", "%like%", 0, 1);
    //     }
    //     $this->db->order_by($sort_col, $sort_direction)->page($page, $page_size);
    //     return $this->db->get("object", 0);
    // }

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

    // public function getByUserId($b_user_id, $page=0, $pagesize=10, $sortCol="id", $sortDir="desc", $keyword="", $sdate="", $edate="")
    // {
    //     $this->db->flushQuery();
    //     $this->db->select_as("$this->tbl2_as.id", "id", 0);
    //     $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl8_as.id, $this->tbl2_as.b_kategori_id)", "b_kategori_id", 0);
    //     $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl8_as.nama, $this->tbl4_as.nama)", "kategori", 0);
    //     $this->db->select_as("$this->tbl3_as.id", "b_user_id_seller", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "b_user_fnama_seller", 0);
    //     $this->db->select_as("$this->tbl3_as.image", "b_user_image_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl7_as.model,'')", "c_produk_detail_automotive_model", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->select_as("COALESCE($this->tbl7_as.color,'')", "c_produk_detail_automotive_color", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->select_as("COALESCE($this->tbl7_as.year,'')", "c_produk_detail_automotive_year", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl2_as.b_kategori_id, '')", "sub_kategori_id", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl4_as.nama, '')", "sub_kategori", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl4_as.image_icon, '')", "sub_kategori_icon", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->select_as("IF(STRCMP($this->tbl4_as.utype, 'kategori'), $this->tbl4_as.image_cover, '')", "sub_kategori_icon_selected", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->select_as("$this->tbl2_as.nama", "nama", 0);
    //     $this->db->select_as("$this->tbl2_as.deskripsi_singkat", "deskripsi_singkat", 0);
    //     $this->db->select_as("$this->tbl2_as.deskripsi", "deskripsi", 0);
    //     $this->db->select_as("$this->tbl2_as.harga_jual", "harga_jual", 0);
    //     $this->db->select_as("$this->tbl2_as.dimension_long", "dimension_long", 0);
    //     $this->db->select_as("$this->tbl2_as.dimension_width", "dimension_width", 0);
    //     $this->db->select_as("$this->tbl2_as.dimension_height", "dimension_height", 0);
    //     $this->db->select_as("$this->tbl2_as.foto", "foto", 0);
    //     $this->db->select_as("$this->tbl2_as.thumb", "thumb", 0);
    //     $this->db->select_as("$this->tbl2_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
    //     $this->db->select_as("$this->tbl2_as.is_published", "is_published", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2());
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3());
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4());
    //     $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
    //     $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.b_user_id", $b_user_id);
    //     if (strlen($keyword)>1) {
    //         $this->db->where_as("$this->tbl2_as.nama", $keyword, "OR", "%like%", 1, 0);
    //         $this->db->where_as("$this->tbl2_as.kondisi", $keyword, "OR", "%like%", 0, 0);
    //         $this->db->where_as("$this->tbl2_as.deskripsi", $keyword, "OR", "%like%", 0, 1);
    //     }
    //     $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
    //     return $this->db->get("object", 0);
    // }

    // public function countByUserId($b_user_id, $keyword="", $sdate="", $edate="")
    // {
    //     $this->db->flushQuery();
    //     $this->db->select_as("COUNT(*)", "jumlah", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2());
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3());
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4());
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

    // public function check($nation_code, $b_user_id, $c_produk_id)
    // {
    //     $this->db->flushQuery();
    //     $this->db->select_as("COUNT(*)", "jumlah", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where("$this->tbl_as.b_user_id", $b_user_id);
    //     $this->db->where("$this->tbl_as.c_produk_id", $c_produk_id);
    //     $d = $this->db->get_first("", 0);
    //     if (isset($d->jumlah)) {
    //         return $d->jumlah;
    //     }
    //     return 0;
    // }
    // public function delAllByProdukIds($nation_code, $c_produk_ids)
    // {
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where_in("c_produk_id", $c_produk_ids);
    //     return $this->db->delete($this->tbl, 0);
    // }
}
