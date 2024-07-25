<?php
class C_Community_Report_Model extends JI_Model
{
    public $tbl = 'c_community_report';
    public $tbl_as = 'ccr';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';
    public $tbl3 = 'c_community';
    public $tbl3_as = 'ccom';

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
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.c_community_id", "=", "$this->tbl3_as.id");
        return $composites;
    }

    /**
     * Insert into database
     * @param array $di name value pair describes column and value to insert into table
     */
    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    /**
     * Insert into database, wiht ignore option (slower)
     * @param array $di name value pair describes column and value to insert into table
     */
    // public function set2($di)
    // {
    //     if (!is_array($di)) {
    //         return 0;
    //     }
    //     return $this->db->insert_ignore($this->tbl, $di, 0, 0);
    // }

    /**
     * Update table c_produk by user id and c_produk id
     * @param  integer $nation_code [description]
     * @param  integer $b_user_id   ID from b_user
     * @param  integer $id          ID from c_produk
     * @param  array $du          name value pairs for edited column in a row
     * @return bool              1 success, 0 failed
     */
    // public function update($nation_code, $b_user_id, $id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where('nation_code', $nation_code);
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where('id', $id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    /**
     * Update table c_produk by id
     * @param  integer $nation_code [description]
     * @param  integer $id          ID from c_produk
     * @param  array $du          name value pairs for edited column in a row
     * @return bool              1 success, 0 failed
     */
    // public function update2($nation_code, $id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where('nation_code', $nation_code);
    //     $this->db->where('id', $id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    /**
     * Update multiple rows on c_produk
     * @param  integer $nation_code [description]
     * @param  integer $b_user_id   ID from b_user
     * @param  string $ids          ID(s) from c_produk separated by commas
     * @param  array $du          name value pairs for edited column in a row
     * @return bool              1 success, 0 failed
     */
    // public function updateMass($nation_code, $b_user_id, $ids, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where_in("id", $ids);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    // public function updateByUserId($nation_code, $b_user_id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where('b_user_id', $b_user_id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }
    // public function updateByUserIdAlamatId($nation_code, $b_user_id, $b_user_alamat_id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where("b_user_alamat_id", $b_user_alamat_id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }
    // public function del($nation_code, $id, $b_user_id)
    // {
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('id', $id);
    //     $this->db->where('b_user_id', $b_user_id);
    //     return $this->db->delete($this->tbl);
    // }
    // public function deleteMass($nation_code, $b_user_id, $pids)
    // {
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where_in("id", $pids);
    //     return $this->db->delete($this->tbl);
    // }

    public function getTblAs()
    {
        return $this->tbl_as;
    }
    public function getTbl2As()
    {
        return $this->tbl2_as;
    }

    public function countAll($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl3_as.is_published", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_report", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_take_down", $this->db->esc('0'));
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getAll($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_direction="ASC", $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.id", "block_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.c_community_id", "custom_id", 0);
        $this->db->select_as("'community'", "type", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl3_as.is_published", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_report", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_take_down", $this->db->esc('0'));
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
        $this->db->order_by($sort_col, $sort_direction)->page($page, $page_size);
        return $this->db->get('object', 0);
    }

    // public function getByIdForCheckout($nation_code, $pid)
    // {
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl2_as.nama,'')", "kategori", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_id", "b_user_alamat_id", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_fnama_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl3_as.fcm_token,'')", "b_user_fcm_token_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl3_as.device,'')", "b_user_device_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.id,'0')", "b_berat_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.nama,'')", "b_berat_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.icon,'media/icon/default-icon.png')", "b_berat_icon", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.alamat,'')", "alamat1", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.alamat2,'')", "alamat2", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.latitude,'0.0')", "latitude", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.longitude,'0.0')", "longitude", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.provinsi,'')", "provinsi", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kabkota,'')", "kabkota", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kecamatan,'')", "kecamatan", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kelurahan,'')", "kelurahan", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kodepos,'')", "kodepos", 0);
    //     $this->db->select_as("COALESCE($this->tbl23_as.nama,'')", "negara", 0);
    //     $this->db->select_as("$this->tbl_as.nama", "nama", 0);
    //     $this->db->select_as("$this->tbl_as.brand", "brand", 0);
    //     $this->db->select_as("$this->tbl_as.deskripsi_singkat", "deskripsi_singkat", 0);
    //     $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
    //     $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
    //     $this->db->select_as("$this->tbl_as.berat", "berat", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_long", "dimension_long", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_width", "dimension_width", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_height", "dimension_height", 0);
    //     $this->db->select_as("$this->tbl_as.stok", "stok", 0);
    //     $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
    //     $this->db->select_as("$this->tbl_as.foto", "foto", 0);
    //     $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
    //     $this->db->select_as("$this->tbl_as.vehicle_types", "vehicle_types", 0);
    //     $this->db->select_as("$this->tbl_as.courier_services", "courier_services", 0);
    //     $this->db->select_as("$this->tbl_as.services_duration", "services_duration", 0);
    //     $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
    //     $this->db->select_as("(0)", "is_liked", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
    //     $this->db->join_composite($this->tbl20, $this->tbl20_as, $this->__joinTbl20(), 'left');
    //     $this->db->join_composite($this->tbl23, $this->tbl23_as, $this->__joinTbl23(), 'left');
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($pid));
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     return $this->db->get_first('', 0);
    // }

    // public function getOwnedById($nation_code, $pid)
    // {
    //     $this->db->select_as("DISTINCT $this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_id", "b_user_alamat_id_seller", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_fnama_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.id,'0')", "b_berat_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.nama,'')", "b_berat_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.icon,'media/icon/default-icon.png')", "b_berat_icon", 0);
    //     $this->db->select_as("COALESCE($this->tbl2_as.nama,'')", "kategori", 0);
    //     $this->db->select_as("COALESCE($this->tbl2_as.image_icon,'')", "kategori_icon", 0);
    //     $this->db->select_as("$this->tbl_as.nama", "nama", 0);
    //     $this->db->select_as("$this->tbl_as.brand", "brand", 0);
    //     $this->db->select_as("$this->tbl_as.deskripsi_singkat", "deskripsi_singkat", 0);
    //     $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
    //     $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
    //     $this->db->select_as("$this->tbl_as.berat", "berat", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_long", "dimension_long", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_width", "dimension_width", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_height", "dimension_height", 0);
    //     $this->db->select_as("$this->tbl_as.stok", "stok", 0);
    //     $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
    //     $this->db->select_as("$this->tbl_as.foto", "foto", 0);
    //     $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
    //     $this->db->select_as("$this->tbl_as.vehicle_types", "vehicle_types", 0);
    //     $this->db->select_as("$this->tbl_as.courier_services", "courier_services", 0);
    //     $this->db->select_as("$this->tbl_as.services_duration", "services_duration", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.alamat,'')", "alamat1", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl20_as.alamat2").',"")', "alamat2", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.latitude,'0.0')", "latitude", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.longitude,'0.0')", "longitude", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.provinsi,'')", "provinsi", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kabkota,'')", "kabkota", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kecamatan,'')", "kecamatan", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kelurahan,'')", "kelurahan", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kodepos,'')", "kodepos", 0);
    //     $this->db->select_as("COALESCE($this->tbl23_as.nama,'')", "negara", 0);
    //     $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
    //     $this->db->select_as("$this->tbl_as.is_published", "is_published", 0);
    //     $this->db->select_as("$this->tbl_as.is_visible", "is_visible", 0);
    //     $this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
    //     $this->db->select_as("(0)", "is_liked", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
    //     $this->db->join_composite($this->tbl20, $this->tbl20_as, $this->__joinTbl20(), 'left');
    //     $this->db->join_composite($this->tbl23, $this->tbl23_as, $this->__joinTbl23(), 'left');
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($pid));
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     //$this->db->where_as("$this->tbl_as.is_visible",1);
    //     //$this->db->where_as("$this->tbl_as.is_active",1);
    //     return $this->db->get_first();
    // }

    // public function getByIdActive($nation_code, $pid)
    // {
    //     $this->db->select_as("DISTINCT $this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl2_as.nama,'')", "kategori", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_fnama_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.id,'0')", "b_berat_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.nama,'')", "b_berat_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.icon,'media/icon/default-icon.png')", "b_berat_icon", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.alamat,'')", "alamat1", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.alamat2,'')", "alamat2", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.latitude,'0.0')", "latitude", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.longitude,'0.0')", "longitude", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.provinsi,'')", "provinsi", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kabkota,'')", "kabkota", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kecamatan,'')", "kecamatan", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kelurahan,'')", "kelurahan", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kodepos,'')", "kodepos", 0);
    //     $this->db->select_as("COALESCE($this->tbl23_as.nama,'')", "negara", 0);
    //     $this->db->select_as("$this->tbl_as.nama", "nama", 0);
    //     $this->db->select_as("$this->tbl_as.brand", "brand", 0);
    //     $this->db->select_as("$this->tbl_as.deskripsi_singkat", "deskripsi_singkat", 0);
    //     $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
    //     $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
    //     $this->db->select_as("$this->tbl_as.berat", "berat", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_long", "dimension_long", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_width", "dimension_width", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_height", "dimension_height", 0);
    //     $this->db->select_as("$this->tbl_as.stok", "stok", 0);
    //     $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
    //     $this->db->select_as("$this->tbl_as.foto", "foto", 0);
    //     $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
    //     $this->db->select_as("$this->tbl_as.vehicle_types", "vehicle_types", 0);
    //     $this->db->select_as("$this->tbl_as.courier_services", "courier_services", 0);
    //     $this->db->select_as("$this->tbl_as.services_duration", "services_duration", 0);
    //     $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
    //     $this->db->select_as("$this->tbl_as.is_published", "is_published", 0);
    //     $this->db->select_as("(0)", "is_liked", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
    //     $this->db->join_composite($this->tbl20, $this->tbl20_as, $this->__joinTbl20(), 'left');
    //     $this->db->join_composite($this->tbl23, $this->tbl23_as, $this->__joinTbl23(), 'left');
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($pid));
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    //     return $this->db->get_first();
    // }
    // public function getByKategoriId($nation_code, $b_kategori_ids, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $keyword="")
    // {
    //     $this->db->select_as("DISTINCT $this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl2_as.nama,'')", "kategori", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_fnama_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.id,'0')", "b_berat_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.nama,'')", "b_berat_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.icon,'media/icon/default-icon.png')", "b_berat_icon", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.alamat,'')", "alamat1", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.alamat2,'')", "alamat2", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.latitude,'0.0')", "latitude", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.longitude,'0.0')", "longitude", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.provinsi,'')", "provinsi", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kabkota,'')", "kabkota", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kecamatan,'')", "kecamatan", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kelurahan,'')", "kelurahan", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kodepos,'')", "kodepos", 0);
    //     $this->db->select_as("COALESCE($this->tbl23_as.nama,'')", "negara", 0);
    //     $this->db->select_as("$this->tbl_as.nama", "nama", 0);
    //     $this->db->select_as("$this->tbl_as.brand", "brand", 0);
    //     $this->db->select_as("$this->tbl_as.deskripsi_singkat", "deskripsi_singkat", 0);
    //     $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
    //     $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
    //     $this->db->select_as("$this->tbl_as.berat", "berat", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_long", "dimension_long", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_width", "dimension_width", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_height", "dimension_height", 0);
    //     $this->db->select_as("$this->tbl_as.stok", "stok", 0);
    //     $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
    //     $this->db->select_as("$this->tbl_as.foto", "foto", 0);
    //     $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
    //     $this->db->select_as("$this->tbl_as.vehicle_types", "vehicle_types", 0);
    //     $this->db->select_as("$this->tbl_as.courier_services", "courier_services", 0);
    //     $this->db->select_as("$this->tbl_as.services_duration", "services_duration", 0);
    //     $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
    //     $this->db->select_as("(0)", "is_liked", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
    //     $this->db->join_composite($this->tbl20, $this->tbl20_as, $this->__joinTbl20(), 'left');
    //     $this->db->join_composite($this->tbl23, $this->tbl23_as, $this->__joinTbl23(), 'left');
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", '1', 'AND', '=');
    //     $this->db->where_as("$this->tbl_as.is_visible", '1', 'AND', '=');
    //     $this->db->where_as("$this->tbl_as.is_active", '1', 'AND', '=');
    //     if (is_array($b_kategori_ids) && count($b_kategori_ids)) {
    //         $this->db->where_in("$this->tbl_as.b_kategori_id", $b_kategori_ids);
    //     }
    //     if (strlen($keyword)>0) {
    //         $this->db->where_as("$this->tbl_as.nama", $keyword, 'or', '%like%', 1, 0);
    //         $this->db->where_as("$this->tbl2_as.nama", $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.deskripsi", $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
    //     }
    //     $this->db->order_by($sort_col, $sort_direction)->page($page, $page_size);
    //     return $this->db->get('object', 0);
    // }

    // public function countByKategoriId($nation_code, $b_kategori_ids, $keyword="")
    // {
    //     $this->db->select_as("COUNT(*)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
    //     $this->db->join_composite($this->tbl20, $this->tbl20_as, $this->__joinTbl20(), 'left');
    //     $this->db->join_composite($this->tbl23, $this->tbl23_as, $this->__joinTbl23(), 'left');
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_visible", '1', 'AND', '=');
    //     $this->db->where_as("$this->tbl_as.is_active", '1', 'AND', '=');
    //     if (is_array($b_kategori_ids) && count($b_kategori_ids)) {
    //         $this->db->where_in("$this->tbl_as.b_kategori_id", $b_kategori_ids);
    //     }
    //     if (strlen($keyword)>0) {
    //         $this->db->where_as("$this->tbl_as.nama", $keyword, 'or', '%like%', 1, 0);
    //         $this->db->where_as("$this->tbl2_as.nama", $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.deskripsi", $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
    //     }
    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }
    // public function getByUserId($nation_code, $b_user_id, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $keyword="")
    // {
    //     $this->db->select_as("DISTINCT $this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl2_as.nama,'')", "kategori", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama"), ',"")', "b_user_fnama_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.id,'0')", "b_berat_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.nama,'')", "b_berat_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.icon,'media/icon/default-icon.png')", "b_berat_icon", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.alamat,'')", "alamat1", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.alamat2,'')", "alamat2", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.latitude,'0.0')", "latitude", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.longitude,'0.0')", "longitude", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.provinsi,'')", "provinsi", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kabkota,'')", "kabkota", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kecamatan,'')", "kecamatan", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kelurahan,'')", "kelurahan", 0);
    //     $this->db->select_as("COALESCE($this->tbl20_as.kodepos,'')", "kodepos", 0);
    //     $this->db->select_as("COALESCE($this->tbl23_as.nama,'')", "negara", 0);
    //     $this->db->select_as("$this->tbl_as.nama", "nama", 0);
    //     $this->db->select_as("$this->tbl_as.brand", "brand", 0);
    //     $this->db->select_as("$this->tbl_as.deskripsi_singkat", "deskripsi_singkat", 0);
    //     $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
    //     $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
    //     $this->db->select_as("$this->tbl_as.berat", "berat", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_long", "dimension_long", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_width", "dimension_width", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_height", "dimension_height", 0);
    //     $this->db->select_as("$this->tbl_as.stok", "stok", 0);
    //     $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
    //     $this->db->select_as("$this->tbl_as.foto", "foto", 0);
    //     $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
    //     $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
    //     $this->db->select_as("(0)", "is_liked", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
    //     $this->db->join_composite($this->tbl20, $this->tbl20_as, $this->__joinTbl20(), 'left');
    //     $this->db->join_composite($this->tbl23, $this->tbl23_as, $this->__joinTbl23(), 'left');
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", '1', 'AND', '=');
    //     $this->db->where_as("$this->tbl_as.is_visible", '1', 'AND', '=');
    //     $this->db->where_as("$this->tbl_as.is_active", '1', 'AND', '=');
    //     $this->db->where_as("COALESCE($this->tbl_as.b_user_id,0)", $this->db->esc($b_user_id), 'AND', '=');
    //     if (strlen($keyword)>0) {
    //         $this->db->where_as("$this->tbl_as.nama", $keyword, 'or', '%like%', 1, 0);
    //         $this->db->where_as("$this->tbl2_as.nama", $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.deskripsi", $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
    //     }
    //     $this->db->order_by($sort_col, $sort_direction)->page($page, $page_size);
    //     return $this->db->get('object', 0);
    // }

    // public function countByUserId($nation_code, $b_user_id, $keyword="")
    // {
    //     $this->db->select_as("COUNT(*)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
    //     $this->db->join_composite($this->tbl20, $this->tbl20_as, $this->__joinTbl20(), 'left');
    //     $this->db->join_composite($this->tbl23, $this->tbl23_as, $this->__joinTbl23(), 'left');
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", '1', 'AND', '=');
    //     $this->db->where_as("$this->tbl_as.is_visible", '1', 'AND', '=');
    //     $this->db->where_as("$this->tbl_as.is_active", '1', 'AND', '=');
    //     $this->db->where_as("COALESCE($this->tbl_as.b_user_id,0)", $this->db->esc($b_user_id), 'AND', '=');
    //     if (strlen($keyword)>0) {
    //         $this->db->where_as("$this->tbl_as.nama", $keyword, 'or', '%like%', 1, 0);
    //         $this->db->where_as("$this->tbl2_as.nama", $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.deskripsi", $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
    //     }
    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }
    // public function getMyProduk($nation_code, $b_user_id, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $keyword="", $is_published="")
    // {
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl2_as.nama,'')", "kategori", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_fnama_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
    //     $this->db->select_as("'0'", "b_berat_id", 0);
    //     $this->db->select_as("''", "b_berat_nama", 0);
    //     $this->db->select_as("'media/icon/default-icon.png'", "b_berat_icon", 0);
    //     $this->db->select_as("$this->tbl_as.nama", "nama", 0);
    //     $this->db->select_as("$this->tbl_as.brand", "brand", 0);
    //     $this->db->select_as("$this->tbl_as.deskripsi_singkat", "deskripsi_singkat", 0);
    //     $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
    //     $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
    //     $this->db->select_as("$this->tbl_as.berat", "berat", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_long", "dimension_long", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_width", "dimension_width", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_height", "dimension_height", 0);
    //     $this->db->select_as("$this->tbl_as.stok", "stok", 0);
    //     $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
    //     $this->db->select_as("$this->tbl_as.foto", "foto", 0);
    //     //create obokobok
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl20_as.alamat2").',"")', "alamat2", 0);

        
    //     $this->db->select_as("$this->tbl_as.is_published", "is_published", 0);
    //     $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
    //     $this->db->select_as("(0)", "is_liked", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     //create obokobok
    //     $this->db->join_composite($this->tbl20, $this->tbl20_as, $this->__joinTbl20(), 'left');

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("COALESCE($this->tbl_as.b_user_id,0)", $this->db->esc($b_user_id), 'AND', '=');
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'), 'AND', '=');
    //     if (strlen($is_published)>0) {
    //         $is_published = (int) $is_published;
    //         $this->db->where_as("$this->tbl_as.is_published", $this->db->esc($is_published), 'AND', '=');
    //     }
    //     if (strlen($keyword)>0) {
    //         $this->db->where_as("$this->tbl_as.nama", $keyword, 'or', '%like%', 1, 0);
    //         $this->db->where_as("$this->tbl2_as.nama", $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.deskripsi", $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
    //     }
    //     $this->db->order_by($sort_col, $sort_direction)->page($page, $page_size);
    //     return $this->db->get('object', 0);
    // }

    // public function countMyProduct($nation_code, $b_user_id, $keyword="", $is_published="")
    // {
    //     $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_active", '1', 'AND', '=');
    //     $this->db->where_as("COALESCE($this->tbl_as.b_user_id,0)", $this->db->esc($b_user_id), 'AND', '=');
    //     if (strlen($is_published)>0) {
    //         $is_published = (int) $is_published;
    //         $this->db->where_as("$this->tbl_as.is_published", $this->db->esc($is_published), 'AND', '=');
    //     }
    //     if (strlen($keyword)>0) {
    //         $this->db->where_as("$this->tbl_as.nama", $keyword, 'or', '%like%', 1, 0);
    //         $this->db->where_as("$this->tbl2_as.nama", $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.deskripsi", $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
    //     }
    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }

    // public function setTerjuals($pids)
    // {
    //     if (is_array($pids) && count($pids)) {
    //         $sql = '';
    //         //building multi query
    //         foreach ($pids as $pid) {
    //             $sql .= 'UPDATE '.$this->tbl.' SET terjual = terjual + '.$pid->qty.', stok = stok - '.$pid->qty.' WHERE id = '.$pid->id.';';
    //             $sql .= 'UPDATE '.$this->tbl.' SET sales_rate = ((sales_count / terjual)*100) WHERE id = '.$pid->id.';';
    //         }
    //         $this->db->query_multi($sql);
    //     }
    // }
    // public function getByProdukIds($ids)
    // {
    //     $this->db->where_in('id', $ids);
    //     return $this->db->get();
    // }
    // public function getByIds($nation_code, $ids)
    // {
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_in('id', $ids);
    //     return $this->db->get();
    // }
    // public function getByIdsActive($nation_code, $ids)
    // {
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as('is_active', $this->db->esc(1));
    //     $this->db->where_in('id', $ids);
    //     return $this->db->get();
    // }
    // public function getActiveByUserIdAndIds($nation_code, $b_user_id, $ids)
    // {
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where('is_active', 1);
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where_in('id', $ids);
    //     return $this->db->get();
    // }
    // public function getByIdsForCart($nation_code, $ids)
    // {
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_nama_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl3_as.image,'default.png')", "b_user_image_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl2_as.nama,'')", "kategori", 0);
    //     $this->db->select_as("COALESCE($this->tbl2_as.is_fashion,'0')", "is_fashion", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_in("$this->tbl_as.id", $ids);
    //     $this->db->order_by("$this->tbl_as.courier_services", "desc");
    //     $this->db->order_by("$this->tbl_as.is_include_delivery_cost", "asc");
    //     $this->db->order_by("$this->tbl2_as.is_fashion", "asc");
    //     return $this->db->get('', 0);
    // }
    // public function getHomePage($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_dir="asc", $keyword="", $harga_jual_min="", $harga_jual_max="", $b_kondisi_ids=array(), $b_kategori_ids=array(), $kecamatan="")
    // {
    //     $this->db->flushQuery();
    //     $this->db->cache_save = 0;
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_kategori_id", "b_kategori_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl2_as.nama,'')", "kategori", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_fnama_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_kondisi_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.nama,'')", "b_kondisi_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.icon,'media/icon/default-icon.png')", "b_kondisi_icon", 0);
    //     $this->db->select_as("$this->tbl_as.nama", "nama", 0);
    //     $this->db->select_as("$this->tbl_as.brand", "brand", 0);
    //     $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
    //     $this->db->select_as("$this->tbl_as.berat", "berat", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_long", "dimension_long", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_width", "dimension_width", 0);
    //     $this->db->select_as("$this->tbl_as.dimension_height", "dimension_height", 0);
    //     $this->db->select_as("$this->tbl_as.stok", "stok", 0);
    //     $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
    //     $this->db->select_as("$this->tbl_as.foto", "foto", 0);
    //     $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
    //     $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
    //     $this->db->select_as("(0)", "is_liked", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", '1');
    //     $this->db->where_as("$this->tbl_as.is_visible", '1');
    //     $this->db->where_as("$this->tbl_as.is_active", '1');
    //     //by Donny Dennison
    //     //show product even the stock is 0 from Mr. Jackie
    //     // $this->db->where_as("$this->tbl_as.stok", $this->db->esc(0), "AND", ">");

    //     //advanced filter
    //     if (strlen($harga_jual_min)>0 && strlen($harga_jual_max)>0) {
    //         $this->db->between("($this->tbl_as.harga_jual)", '("'.$harga_jual_min.'")', '("'.$harga_jual_max.'")', 0);
    //     } elseif (strlen($harga_jual_min)==0 && strlen($harga_jual_max)>0) {
    //         $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_max), "AND", "<=");
    //     } elseif (strlen($harga_jual_min)>0 && strlen($harga_jual_max)==0) {
    //         $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_min), "AND", ">=");
    //     }
    //     if (count($b_kondisi_ids)>0) {
    //         $this->db->where_in("$this->tbl_as.b_kondisi_id", $b_kondisi_ids);
    //     }
    //     if (count($b_kategori_ids)>0) {
    //         $this->db->where_in("$this->tbl_as.b_kategori_id", $b_kategori_ids);
    //     }
    //     //end advanced filter

    //     if (strlen($keyword)>0) {
    //         $this->db->where_as("$this->tbl_as.nama", $keyword, 'or', '%like%', 1, 0);
    //         $this->db->where_as("$this->tbl2_as.nama", $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.deskripsi", $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
    //     }

    //     //by Donny Dennison - 13-07-2020 16:08
    //     //edit stok allow 0 and if edit stok more than 0 then change cdate to newest
    //     // $this->db->order_by($sort_col, $sort_dir);
    //     $this->db->order_by("$this->tbl_as.".$sort_col, $sort_dir);
        
    //     $this->db->page($page, $page_size);
    //     return $this->db->get('object', 0);
    // }

    // public function countHomePage($nation_code, $keyword="", $harga_jual_min="", $harga_jual_max="", $b_kondisi_ids=array(), $b_kategori_ids=array(), $kecamatan="")
    // {
    //     $this->db->flushQuery();
    //     $this->db->cache_save = 0;
    //     $this->db->select_as("COUNT(*)", 'total', 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');

    //     //advanced filter
    //     if (strlen($harga_jual_min)>0 && strlen($harga_jual_max)>0) {
    //         $this->db->between("($this->tbl_as.harga_jual)", '("'.$harga_jual_min.'")', '("'.$harga_jual_max.'")', 0);
    //     } elseif (strlen($harga_jual_min)==0 && strlen($harga_jual_max)>0) {
    //         $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_max), "AND", "<=");
    //     } elseif (strlen($harga_jual_min)>0 && strlen($harga_jual_max)==0) {
    //         $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_min), "AND", ">=");
    //     }
    //     if (count($b_kondisi_ids)>0) {
    //         $this->db->where_in("$this->tbl_as.b_kondisi_id", $b_kondisi_ids);
    //     }
    //     if (count($b_kategori_ids)>0) {
    //         $this->db->where_in("$this->tbl_as.b_kategori_id", $b_kategori_ids);
    //     }
    //     //end advanced filter

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", '1');
    //     $this->db->where_as("$this->tbl_as.is_visible", '1');
    //     $this->db->where_as("$this->tbl_as.is_active", '1');
    //     if (strlen($keyword)>0) {
    //         $this->db->where_as("$this->tbl_as.nama", $keyword, 'or', '%like%', 1, 0);
    //         $this->db->where_as("$this->tbl2_as.nama", $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.deskripsi", $keyword, 'or', '%like%');
    //         $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
    //     }
    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }
    // public function substractStok($nation_code, $c_produk_id, $qty)
    // {
    //     $qty = (int) $qty;
    //     $sql = "UPDATE `$this->tbl` SET `stok` = (`stok`-$qty) WHERE nation_code = ".$this->db->esc($nation_code)." AND id = $c_produk_id;";
    //     return $this->db->exec($sql);
    // }
    // public function addStok($nation_code, $c_produk_id, $qty)
    // {
    //     $qty = (int) $qty;
    //     $sql = "UPDATE `$this->tbl` SET `stok` = (`stok`+$qty) WHERE nation_code = ".$this->db->esc($nation_code)." AND id = $c_produk_id;";
    //     return $this->db->exec($sql);
    // }
    // public function getByIdRaw($nation_code, $id)
    // {
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("id", $this->db->esc($id));
    //     return $this->db->get_first('', 0);
    // }
    // public function getByUserIdAlamatId($nation_code, $b_user_id, $b_user_alamat_id)
    // {
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where("b_user_alamat_id", $b_user_alamat_id);
    //     return $this->db->get();
    // }
    // public function checkTakeDown($nation_code, $id)
    // {
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.reported_status", $this->db->esc("takedown"));
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
    //     return $this->db->get_first('', 0);
    // }

    // //by Donny Dennison - 28 july 2020 11:39
    // // check the address if there is product using this address then cannot delete
    // public function getActiveByUserIdAlamatId($nation_code, $b_user_id, $b_user_alamat_id)
    // {
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where("b_user_alamat_id", $b_user_alamat_id);
    //     $this->db->where_as("$this->tbl_as.is_published", '1');
    //     $this->db->where_as("$this->tbl_as.is_visible", '1');
    //     $this->db->where_as("$this->tbl_as.is_active", '1');
    //     return $this->db->get();
    // }

    // //by Donny Dennison - 29 july 2020 - 15:47
    // //prevent insert product duplication
    // public function getActiveByUserIdProductNameWeightDimensionPrice($nation_code, $b_user_id, $product_name, $weight, $dimension_long, $dimension_width, $dimension_height, $price)
    // {
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where("nama", $product_name);
    //     $this->db->where("berat", $weight);
    //     $this->db->where("dimension_long", $dimension_long);
    //     $this->db->where("dimension_width", $dimension_width);
    //     $this->db->where("dimension_height", $dimension_height);
    //     $this->db->where("harga_jual", $price);
    //     $this->db->where_as("$this->tbl_as.is_published", '1');
    //     $this->db->where_as("$this->tbl_as.is_visible", '1');
    //     $this->db->where_as("$this->tbl_as.is_active", '1');
    //     return $this->db->get();
    // }

}
