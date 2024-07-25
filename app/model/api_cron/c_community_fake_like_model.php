<?php
class C_Community_Fake_Like_Model extends JI_Model
{
    public $tbl = 'c_community_fake_like';
    public $tbl_as = 'ccfl';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    // private function __joinTbl2()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
    //     return $composites;
    // }

    // public function getLastId($nation_code, $b_user_id, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta")
    // {
    //     $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //     $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //     $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
    //     $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
    //     $d = $this->db->get_first('', 0);
    //     if (isset($d->last_id)) {
    //         return (int) $d->last_id;
    //     }
    //     return 0;
    // }

    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    public function update($nation_code, $c_community_id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where_as('nation_code', $this->db->esc($nation_code));
        $this->db->where('c_community_id', $c_community_id);
        return $this->db->update($this->tbl, $du, 0);
    }

    // public function del($nation_code, $id, $b_user_id)
    // {
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('id', $id);
    //     $this->db->where('b_user_id', $b_user_id);
    //     return $this->db->delete($this->tbl);
    // }

    public function getTblAs()
    {
        return $this->tbl_as;
    }

//     public function getByUserId($nation_code, $b_user_id, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $keyword="")
//     {
//         $this->db->select_as("DISTINCT $this->tbl_as.id", "id", 0);

//         // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
//         // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl8_as.id, $this->tbl_as.b_kategori_id)", "b_kategori_id", 0);
//         // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl8_as.nama, $this->tbl2_as.nama)", "kategori", 0);
//         $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);
//         $this->db->select_as("$this->tbl2_as.nama", "kategori", 0);

//         $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        
//         //by Donny Dennison - 28 august 2020 15:14
//         //add new api for best shop in homepage
//         // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama"), ',"")', "b_user_fnama_seller", 0);
//         $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_fnama_seller", 0);

//         $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
//         $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
//         $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
//         $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
//         $this->db->select_as("COALESCE($this->tbl5_as.id,'0')", "b_berat_id", 0);
//         $this->db->select_as("COALESCE($this->tbl5_as.nama,'')", "b_berat_nama", 0);
//         $this->db->select_as("COALESCE($this->tbl5_as.icon,'media/icon/default-icon.png')", "b_berat_icon", 0);

//         // START by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
//         // $this->db->select_as("COALESCE($this->tbl7_as.model,'')", "c_produk_detail_automotive_model", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
//         // $this->db->select_as("COALESCE($this->tbl7_as.color,'')", "c_produk_detail_automotive_color", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
//         // $this->db->select_as("COALESCE($this->tbl7_as.year,'')", "c_produk_detail_automotive_year", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
//         // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl_as.b_kategori_id, '')", "sub_kategori_id", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
//         // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.nama, '')", "sub_kategori", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
//         // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.image_icon, '')", "sub_kategori_icon", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
//         // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl2_as.image_cover, '')", "sub_kategori_icon_selected", 0);//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
//         // END by Muhammad Sofi - 15 November 2021 10:17
        
//         // by Muhammad Sofi - 4 November 2021 10:00
//         // remark code
//         // $this->db->select_as("COALESCE($this->tbl20_as.alamat,'')", "alamat1", 0);
//         $this->db->select_as("COALESCE($this->tbl20_as.alamat2,'')", "alamat2", 0);
//         $this->db->select_as("COALESCE($this->tbl20_as.latitude,'0.0')", "latitude", 0);
//         $this->db->select_as("COALESCE($this->tbl20_as.longitude,'0.0')", "longitude", 0);
//         $this->db->select_as("COALESCE($this->tbl20_as.provinsi,'')", "provinsi", 0);
//         $this->db->select_as("COALESCE($this->tbl20_as.kabkota,'')", "kabkota", 0);
//         $this->db->select_as("COALESCE($this->tbl20_as.kecamatan,'')", "kecamatan", 0);
//         $this->db->select_as("COALESCE($this->tbl20_as.kelurahan,'')", "kelurahan", 0);
//         $this->db->select_as("COALESCE($this->tbl20_as.kodepos,'')", "kodepos", 0);
//         $this->db->select_as("COALESCE($this->tbl23_as.nama,'')", "negara", 0);
//         $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        
//         //by Donny Dennison - 15 November 2021 16:28
//         //change car and motorcycle to main category
//         // $this->db->select_as("$this->tbl_as.brand", "brand", 0);
//         $this->db->select_as("IF($this->tbl_as.product_type != 'Automotive', $this->tbl_as.brand, IF($this->tbl10_as.nama IS NULL, $this->tbl_as.brand, $this->tbl10_as.nama))", "brand", 0);

//         $this->db->select_as("$this->tbl_as.deskripsi_singkat", "deskripsi_singkat", 0);
//         $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
//         $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
//         $this->db->select_as("$this->tbl_as.berat", "berat", 0);
//         $this->db->select_as("$this->tbl_as.dimension_long", "dimension_long", 0);
//         $this->db->select_as("$this->tbl_as.dimension_width", "dimension_width", 0);
//         $this->db->select_as("$this->tbl_as.dimension_height", "dimension_height", 0);
//         $this->db->select_as("$this->tbl_as.stok", "stok", 0);
//         $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
//         $this->db->select_as("$this->tbl_as.foto", "foto", 0);
//         $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
//         $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
//         $this->db->select_as("(0)", "is_liked", 0);
//         $this->db->from($this->tbl, $this->tbl_as);
//         $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
//         $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
//         $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
//         $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
//         $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
//         // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
//         // $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), 'left');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
//         $this->db->join_composite($this->tbl20, $this->tbl20_as, $this->__joinTbl20(), 'left');
//         $this->db->join_composite($this->tbl23, $this->tbl23_as, $this->__joinTbl23(), 'left');

//         //by Donny Dennison - 15 November 2021 16:28
//         //change car and motorcycle to main category
//         $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');

//         $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
//         $this->db->where_as("$this->tbl_as.is_published", '1', 'AND', '=');
//         $this->db->where_as("$this->tbl_as.is_visible", '1', 'AND', '=');
//         $this->db->where_as("$this->tbl_as.is_active", '1', 'AND', '=');
//         $this->db->where_as("COALESCE($this->tbl_as.b_user_id,0)", $this->db->esc($b_user_id), 'AND', '=');
//         if (strlen($keyword)>0) {
//             $this->db->where_as("$this->tbl_as.nama", $keyword, 'or', '%like%', 1, 0);
//             $this->db->where_as("$this->tbl2_as.nama", $keyword, 'or', '%like%');
//             // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
//             // $this->db->where_as("$this->tbl8_as.nama", $keyword, 'or', '%like%');//automotive detail & sub kategori by Rendi Fajrianto - 15 october 2020 16:30
//             $this->db->where_as("$this->tbl_as.deskripsi", $keyword, 'or', '%like%');

//             //by Donny Dennison - 15 November 2021 16:28
//             //change car and motorcycle to main category
//             $this->db->where_as("$this->tbl10_as.nama", $keyword, 'or', '%like%');

//             $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
//         }
//         $this->db->order_by($sort_col, $sort_direction)->page($page, $page_size);
//         return $this->db->get('object', 0);
//     }

    // public function countAll($nation_code, $kelurahan="", $kecamatan="", $kabkota="", $provinsi="", $b_user_id, $plusorminus, $custom_id="", $custom_type, $custom_type_sub, $dateCompare)
    // {
    //     $this->db->select_as("COUNT(*)", "total", 0);
        
    //     $this->db->from($this->tbl, $this->tbl_as);
        
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        
    //     if($kelurahan != ""){
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
    //     }

    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));

    //     if($custom_id != ""){
    //         $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
    //     }

    //     $this->db->where_as("$this->tbl_as.plusorminus", $this->db->esc($plusorminus));
    //     $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
    //     $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));
    //     $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($dateCompare));
        
    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }

    // public function getAll($nation_code, $kelurahan="", $kecamatan="", $kabkota="", $provinsi="", $b_user_id="", $plusorminus="", $custom_id="", $custom_type="", $custom_type_sub="", $dateCompare)
    // {
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kelurahan", "kelurahan", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kecamatan", "kecamatan", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kabkota", "kabkota", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_provinsi", "provinsi", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

    //     if($kelurahan != ""){
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
    //         $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
    //     }

    //     if($b_user_id != ""){
    //         $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     }

    //     if($custom_id != ""){
    //         $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
    //     }

    //     if($plusorminus != ""){
    //         $this->db->where_as("$this->tbl_as.plusorminus", $this->db->esc($plusorminus));
    //     }

    //     if($custom_type != ""){
    //         $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type));
    //     }

    //     if($custom_type_sub != ""){
    //         $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub));
    //     }

    //     $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($dateCompare),"AND","<=");
    //     $this->db->where_as("$this->tbl_as.is_calculated", $this->db->esc("0"));

    //     $this->db->order_by("CONCAT($this->tbl_as.b_user_id, '-', $this->tbl_as.custom_type, '-', $this->tbl_as.custom_type_sub)", "ASC");

    //     $this->db->limit("200");
    //     return $this->db->get('object', 0);
    // }

    public function checkAlreadyInDB($nation_code, $c_community_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.c_community_id", "c_community_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.c_community_id", $this->db->esc($c_community_id));

        return $this->db->get_first('object', 0);
    }

}
