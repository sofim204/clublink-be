<?php
class G_Leaderboard_Point_Area_Model extends JI_Model
{
    public $tbl = 'g_leaderboard_point_area';
    public $tbl_as = 'glpa';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';
    public $tbl3 = 'b_user_alamat';
    public $tbl3_as = 'bua';
    // public $tbl4 = 'b_user_alamat_location';
    // public $tbl4_as = 'bual';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    /**
     * Composite join for multiple PK on table 2
     * @return array composites join
     */
    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
        return $composites;
    }

    private function __joinTbl3()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl2_as.id", "=", "$this->tbl3_as.b_user_id");
        $composites[] = $this->db->composite_create("1", "=", "$this->tbl3_as.is_default");
        return $composites;
    }

    // private function __joinTbl4()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl3_as.nation_code", "=", "$this->tbl4_as.nation_code");
    //     $composites[] = $this->db->composite_create("SUBSTR($this->tbl3_as.kodepos,1,2)", "=", "$this->tbl4_as.postal_sector");
    //     return $composites;
    // }

    // public function getTblAs()
    // {
    //     return $this->tbl_as;
    // }

    // public function getLastId($nation_code, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta")
    // {
    //     $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
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

    /**
     * Insert into database
     * @param array $di name value pair describes column and value to insert into table
     */
    // public function set($di)
    // {
    //     if (!is_array($di)) {
    //         return 0;
    //     }
    //     return $this->db->insert($this->tbl, $di, 0, 0);
    // }

    // public function updateTotal($nation_code, $b_user_id, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta", $parameter, $operator, $total)
    // {
    //     return $this->db->exec("UPDATE `$this->tbl` SET $parameter = IF('$operator' = '+', $parameter $operator $total, IF($parameter = 0,0,$parameter $operator $total))
    //         WHERE nation_code = '$nation_code' AND b_user_id = '$b_user_id' AND b_user_alamat_location_kelurahan  = '$kelurahan' AND b_user_alamat_location_kecamatan  = '$kecamatan' AND b_user_alamat_location_kabkota  = '$kabkota' AND b_user_alamat_location_provinsi  = '$provinsi';");
    // }

    // public function update($nation_code, $b_user_id, $id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where('id', $id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    // public function del($nation_code, $id, $b_user_id)
    // {
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('id', $id);
    //     $this->db->where('b_user_id', $b_user_id);
    //     return $this->db->delete($this->tbl);
    // }

//     public function getById($nation_code, $pid)
//     {
//         $this->db->select_as("$this->tbl_as.id", "id", 0);

//         // by Muhammad Sofi - 15 November 2021 10:17 | remark code produk_detail automotive
//         // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl8_as.id, $this->tbl_as.b_kategori_id)", "b_kategori_id", 0);
//         // $this->db->select_as("IF(STRCMP($this->tbl2_as.utype, 'kategori'), $this->tbl8_as.nama, $this->tbl2_as.nama)", "kategori", 0);
//         $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);
//         $this->db->select_as("$this->tbl2_as.nama", "kategori", 0);

//         $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
//         $this->db->select_as("$this->tbl_as.b_user_alamat_id", "b_user_alamat_id", 0);
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
//         // End by Muhammad Sofi - 15 November 2021 10:17
        
//         // by Muhammad Sofi - 4 November 2021 10:00
//         // remark code
//         // $this->db->select_as("COALESCE($this->tbl20_as.alamat,'')", "alamat1", 0);
//         $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl20_as.alamat2").',"")', "alamat2", 0);
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
//         $this->db->select_as("$this->tbl_as.vehicle_types", "vehicle_types", 0);
//         $this->db->select_as("$this->tbl_as.courier_services", "courier_services", 0);
//         $this->db->select_as("$this->tbl_as.services_duration", "services_duration", 0);
//         $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
//         $this->db->select_as("(0)", "is_liked", 0);

//         //by Donny Dennison - 30 july 2020 19:25
//         // change seller fee percent to 5% if product id is more than 78 and product cdate is less than 31 august 2020
//         $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);

//         //by Donny Dennison - 22 september 2021 15:01
//         //revamp-profile
//         $this->db->select_as("$this->tbl_as.product_type", "product_type", 0);

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

//         $this->db->where_as("$this->tbl_as.id", $this->db->esc($pid));
//         $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
//         $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
//         return $this->db->get_first('', 0);
//     }

    // public function getByUserId($nation_code, $b_user_id, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta")
    // {
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_nama", 0);
    //     $this->db->select_as("$this->tbl2_as.image", "b_user_image", 0);
    //     $this->db->select_as("COALESCE(SUM($this->tbl_as.total_post),0)", "total_post", 0);
    //     $this->db->select_as("COALESCE(SUM($this->tbl_as.total_point),0)", "total_point", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kelurahan", "kelurahan", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kecamatan", "kecamatan", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_kabkota", "kabkota", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_provinsi", "provinsi", 0);
        
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //     $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //     $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
    //     $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));

    //     return $this->db->get_first('object', 0);
    // }

    // public function countAll($nation_code, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta", $type="All")
    // {
    //     $this->db->select_as("COUNT(DISTINCT $this->tbl_as.b_user_id)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));

    //     if($type == 'neighborhood'){

    //         $this->db->where_as("LOWER($this->tbl3_as.kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //         $this->db->where_as("LOWER($this->tbl3_as.kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //         $this->db->where_as("LOWER($this->tbl3_as.kabkota)", $this->db->esc(strtolower($kabkota)));
    //         $this->db->where_as("LOWER($this->tbl3_as.provinsi)", $this->db->esc(strtolower($provinsi)));

    //     }else if($type == 'district'){

    //         $this->db->where_as("LOWER($this->tbl3_as.kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //         $this->db->where_as("LOWER($this->tbl3_as.kabkota)", $this->db->esc(strtolower($kabkota)));
    //         $this->db->where_as("LOWER($this->tbl3_as.provinsi)", $this->db->esc(strtolower($provinsi)));

    //     }else if($type == 'city'){
            
    //         $this->db->where_as("LOWER($this->tbl3_as.kabkota)", $this->db->esc(strtolower($kabkota)));
    //         $this->db->where_as("LOWER($this->tbl3_as.provinsi)", $this->db->esc(strtolower($provinsi)));

    //     }else if($type == 'province'){
            
    //         $this->db->where_as("LOWER($this->tbl3_as.provinsi)", $this->db->esc(strtolower($provinsi)));

    //     }
        
    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }
    
    public function getAll($nation_code, $page=0, $page_size=0, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta", $type="All")
    {
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_nama", 0);
        $this->db->select_as("$this->tbl2_as.image", "b_user_image", 0);
        $this->db->select_as("COALESCE(SUM($this->tbl_as.total_post),0)", "total_post", 0);
        $this->db->select_as("COALESCE(SUM($this->tbl_as.total_point),0)", "total_point", 0);
        $this->db->select_as("ROW_NUMBER() OVER(ORDER BY COALESCE(SUM($this->tbl_as.total_point),0) DESC)", "ranking", 0);
        
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));

        if($type == 'neighborhood'){

            $this->db->where_as("LOWER($this->tbl3_as.kelurahan)", $this->db->esc(strtolower($kelurahan)));
            $this->db->where_as("LOWER($this->tbl3_as.kecamatan)", $this->db->esc(strtolower($kecamatan)));
            $this->db->where_as("LOWER($this->tbl3_as.kabkota)", $this->db->esc(strtolower($kabkota)));
            $this->db->where_as("LOWER($this->tbl3_as.provinsi)", $this->db->esc(strtolower($provinsi)));

        }else if($type == 'district'){

            $this->db->where_as("LOWER($this->tbl3_as.kecamatan)", $this->db->esc(strtolower($kecamatan)));
            $this->db->where_as("LOWER($this->tbl3_as.kabkota)", $this->db->esc(strtolower($kabkota)));
            $this->db->where_as("LOWER($this->tbl3_as.provinsi)", $this->db->esc(strtolower($provinsi)));

        }else if($type == 'city'){
            
            $this->db->where_as("LOWER($this->tbl3_as.kabkota)", $this->db->esc(strtolower($kabkota)));
            $this->db->where_as("LOWER($this->tbl3_as.provinsi)", $this->db->esc(strtolower($provinsi)));

        }else if($type == 'province'){
            
            $this->db->where_as("LOWER($this->tbl3_as.provinsi)", $this->db->esc(strtolower($provinsi)));

        }

        $this->db->group_by("$this->tbl_as.b_user_id");
        $this->db->order_by("total_point", "DESC");
        
        if($page != 0){
            $this->db->page($page, $page_size);
        }

        return $this->db->get('object', 0);
    }

}
