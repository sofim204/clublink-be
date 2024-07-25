<?php
class B_User_Alamat_Location_model extends JI_Model
{
    public $tbl = 'b_user_alamat_location';
    public $tbl_as = 'bual';
    // public $tbl2 = 'a_negara';
    // public $tbl2_as = 'an';
    // public $tbl3 = 'b_lokasi';
    // public $tbl3_as = 'bl';
    // public $tbl4 = 'b_kodepos';
    // public $tbl4_as = 'bkp';
    // public $tbl30 = 'common_code';
    // public $tbl30_as = 'cc';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    // private function __joinTbl3()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl3_as.nation_code", "=", "$this->tbl_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl3_as.id", "=", "COALESCE($this->tbl_as.b_lokasi_id,0)");
    //     return $composites;
    // }

    // private function __joinTbl4()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl4_as.nation_code", "=", "$this->tbl_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl4_as.id", "=", "COALESCE($this->tbl_as.b_kodepos_id,0)");
    //     return $composites;
    // }

    // //by Donny Dennison - 1 july 2021 14:42
    // //add-general-location-in-address
    // private function __joinTbl5()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl5_as.nation_code", "=", "$this->tbl_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl5_as.postal_sector", "=", "SUBSTR($this->tbl_as.kodepos,1,2)");
    //     return $composites;
    // }

    public function getLastId($nation_code)
    {
        // $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        // $this->db->from($this->tbl, $this->tbl_as);
        // $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        // $d = $this->db->get_first('', 0);
        // if (isset($d->last_id)) {
        //     return $d->last_id;
        // }
        // return 0;
        $sql ="SELECT COALESCE(MAX(id),0)+1 AS id FROM `".$this->tbl."` WHERE id >= (SELECT COALESCE(MAX(id),0) FROM `".$this->tbl."` WHERE nation_code = '".$nation_code."') AND nation_code = '".$nation_code."' FOR UPDATE;";
        return $this->db->query($sql)[0]->id;
    }

    // public function getAddressType($nation_code)
    // {
    //     $this->db->from($this->tbl30, $this->tbl30_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("classified", "address");
        
    //     //by Donny Dennison - 17 juni 2020 20:18
    //     // request by Mr Jackie dont show basic address and billing address option
    //     $this->db->where("code", "A0",'AND','!=');
    //     $this->db->where("code", "A1",'AND','!=');

    //     $this->db->where("use_yn", "y");
    //     return $this->db->get('', 0);
    // }

    // public function getByUserId($negara, $b_user_id)
    // {
    //     //negara is object obtained from single row from a_negara.
    //     if (!isset($negara->nation_code)) {
    //         trigger_error("b_user_alamat_id::getByUserId requires negara object obtained from single row from a_negara");
    //         die();
    //     }
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
    //     $this->db->select_as("$this->tbl_as.address_status", "address_status_code", 0);
    //     $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status", 0);
    //     $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status_type", 0);
    //     $this->db->select_as("$this->tbl_as.judul", "judul", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_nama"), "penerima_nama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_telp"), "penerima_telp", 0);
    //     // by Muhammad Sofi - 4 November 2021 10:00
    //     // remark code
    //     // $this->db->select_as("$this->tbl_as.alamat", "alamat", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
    //     $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
    //     $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
    //     $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
    //     $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
    //     $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
    //     $this->db->select_as("$this->tbl_as.negara", "negara", 0);
    //     $this->db->select_as("$this->tbl_as.latitude", "latitude", 0);
    //     $this->db->select_as("$this->tbl_as.longitude", "longitude", 0);
    //     $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status_type", 0);
    //     $this->db->select_as("$this->tbl_as.catatan", "catatan", 0);
    //     $this->db->select_as("$this->tbl_as.is_default", "is_default", 0);

    //     //by Donny Dennison - 1 july 2021 14:42
    //     //add-general-location-in-address
    //     $this->db->select_as("IF($this->tbl5_as.custom_name IS NULL OR $this->tbl5_as.custom_name = '', $this->tbl5_as.original_name, $this->tbl5_as.custom_name)", "general_location", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join($this->tbl30, $this->tbl30_as, "code", $this->tbl_as, "address_status", "left");

    //     //by Donny Dennison - 1 july 2021 14:42
    //     //add-general-location-in-address
    //     $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($negara->nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $b_user_id);
    //     $this->db->where_as("$this->tbl_as.is_active", 1);
    //     $this->db->order_by("$this->tbl_as.is_default", "desc");
    //     $this->db->order_by("$this->tbl_as.id", "desc");
    //     $this->db->group_by("$this->tbl_as.id");
    //     return $this->db->get('', 0);
    // }

    // public function getById($nation_code, $b_user_id, $id)
    // {
    //     // by Muhammad Sofi - 4 November 2021 10:00
    //     // remark code
    //     // $this->db->select_as("$this->tbl_as.*,$this->tbl_as.alamat", "alamat1", 0);
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.catatan", "address_notes", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_nama"), "penerima_nama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_telp"), "penerima_telp", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("b_user_id", $b_user_id);
    //     $this->db->where("id", $id);
    //     return $this->db->get_first();
    // }
    // public function getByIdUserId($nation_code, $b_user_id, $id)
    // {
    //     $this->db->select_as("DISTINCT $this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
    //     $this->db->select_as("$this->tbl_as.address_status", "address_status_code", 0);
    //     $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status", 0);
    //     $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status_type", 0);
    //     $this->db->select_as("$this->tbl_as.judul", "judul", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_nama"), "penerima_nama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_telp"), "penerima_telp", 0);
    //     // by Muhammad Sofi - 4 November 2021 10:00
    //     // remark code
    //     // $this->db->select_as("$this->tbl_as.alamat", "alamat", 0);
    //     // $this->db->select_as("$this->tbl_as.alamat", "alamat1", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
    //     $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
    //     $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
    //     $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
    //     $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
    //     $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
    //     $this->db->select_as("$this->tbl_as.negara", "negara", 0);
    //     $this->db->select_as("$this->tbl_as.latitude", "latitude", 0);
    //     $this->db->select_as("$this->tbl_as.longitude", "longitude", 0);
    //     $this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
    //     $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status_type", 0);
    //     $this->db->select_as("$this->tbl_as.catatan", "catatan", 0);
    //     $this->db->select_as("$this->tbl_as.is_default", "is_default", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join($this->tbl30, $this->tbl30_as, "code", $this->tbl_as, "address_status", "left");
    //     $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where("$this->tbl_as.b_user_id", $b_user_id);
    //     $this->db->where("$this->tbl_as.id", $id);
    //     $this->db->where("$this->tbl_as.is_active", 1);
    //     return $this->db->get_first('', 0);
    // }

    public function set($di)
    {
        // if (isset($di['penerima_nama'])) {
        //     if (strlen($di['penerima_nama'])) {
        //         $di['penerima_nama'] = $this->__encrypt($di['penerima_nama']);
        //     }
        // }
        // if (isset($di['penerima_telp'])) {
        //     if (strlen($di['penerima_telp'])) {
        //         $di['penerima_telp'] = $this->__encrypt($di['penerima_telp']);
        //     }
        // }
        // if (isset($di['alamat2'])) {
        //     if (strlen($di['alamat2'])) {
        //         $di['alamat2'] = $this->__encrypt($di['alamat2']);
        //     }
        // }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }
    
    // public function update($nation_code, $b_user_id, $id, $du)
    // {
    //     if (isset($du['penerima_nama'])) {
    //         if (strlen($du['penerima_nama'])) {
    //             $du['penerima_nama'] = $this->__encrypt($du['penerima_nama']);
    //         }
    //     }
    //     if (isset($du['penerima_telp'])) {
    //         if (strlen($du['penerima_telp'])) {
    //             $du['penerima_telp'] = $this->__encrypt($du['penerima_telp']);
    //         }
    //     }
    //     if (isset($du['alamat2'])) {
    //         if (strlen($du['alamat2'])) {
    //             $du['alamat2'] = $this->__encrypt($du['alamat2']);
    //         }
    //     }
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("b_user_id", $b_user_id);
    //     $this->db->where("id", $id);
    //     return $this->db->update($this->tbl, $du);
    // }
    // public function updateByUserId($nation_code, $b_user_id, $du)
    // {
    //     //by Donny Dennison - 27 october 2021 11:00
    //     //if edit profile then also change address penerima_nama and penerima_telp
    //     //START by Donny Dennison - 27 october 2021 11:00

    //     if (isset($du['penerima_nama'])) {
    //         if (strlen($du['penerima_nama'])) {
    //             $du['penerima_nama'] = $this->__encrypt($du['penerima_nama']);
    //         }
    //     }
    //     if (isset($du['penerima_telp'])) {
    //         if (strlen($du['penerima_telp'])) {
    //             $du['penerima_telp'] = $this->__encrypt($du['penerima_telp']);
    //         }
    //     }
    //     if (isset($du['alamat2'])) {
    //         if (strlen($du['alamat2'])) {
    //             $du['alamat2'] = $this->__encrypt($du['alamat2']);
    //         }
    //     }

    //     //END by Donny Dennison - 27 october 2021 11:00

    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("b_user_id", $b_user_id);
    //     return $this->db->update($this->tbl, $du);
    // }
    // public function updateByIdUserId($nation_code, $id, $b_user_id, $du)
    // {
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("id", $id);
    //     $this->db->where("b_user_id", $b_user_id);
    //     return $this->db->update($this->tbl, $du);
    // }
    // public function delByUserId($nation_code, $id, $b_user_id)
    // {
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("id", $id);
    //     $this->db->where("b_user_id", $b_user_id);
    //     return $this->db->delete($this->tbl);
    // }
    // public function getByIds($nation_code, $b_user_id, $ids)
    // {
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status", 0);
    //     $this->db->select_as("$this->tbl_as.address_status", "address_status_code", 0);
    //     $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status_type", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_nama"), "penerima_nama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_telp"), "penerima_telp", 0);
    //     // by Muhammad Sofi - 4 November 2021 10:00
    //     // remark code
    //     // $this->db->select_as("$this->tbl_as.alamat", "alamat", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
    //     $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
    //     $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
    //     $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
    //     $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
    //     $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
    //     $this->db->select_as("$this->tbl_as.negara", "negara", 0);
    //     $this->db->select_as("$this->tbl_as.latitude", "latitude", 0);
    //     $this->db->select_as("$this->tbl_as.longitude", "longitude", 0);
    //     $this->db->select_as("$this->tbl_as.catatan", "catatan", 0);
    //     $this->db->select_as("$this->tbl_as.is_default", "is_default", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join($this->tbl30, $this->tbl30_as, "code", $this->tbl_as, "address_status", "left");
    //     $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.b_user_id", $b_user_id);
    //     $this->db->where_in("$this->tbl_as.id", $ids);
    //     return $this->db->get();
    // }
    // public function getIdUserIdIN($concat_id_b_user_ids=array())
    // {
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
    //     $this->db->select_as("$this->tbl_as.address_status", "address_status_code", 0);
    //     $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status_type", 0);
    //     $this->db->select_as("$this->tbl_as.judul", "judul", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_nama"), "penerima_nama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_telp"), "penerima_telp", 0);
    //     // by Muhammad Sofi - 4 November 2021 10:00
    //     // remark code
    //     // $this->db->select_as("$this->tbl_as.alamat", "alamat", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
    //     $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
    //     $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
    //     $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
    //     $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
    //     $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
    //     $this->db->select_as("$this->tbl_as.negara", "negara", 0);
    //     $this->db->select_as("$this->tbl_as.latitude", "latitude", 0);
    //     $this->db->select_as("$this->tbl_as.longitude", "longitude", 0);
    //     $this->db->select_as("$this->tbl_as.catatan", "catatan", 0);
    //     $this->db->select_as("$this->tbl_as.is_default", "is_default", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join($this->tbl30, $this->tbl30_as, "code", $this->tbl_as, "address_status", "left");
    //     $this->db->where_in("CONCAT($this->tbl_as.id,'-',COALESCE($this->tbl_as.b_user_id,0))", $concat_id_b_user_ids);
    //     //$this->db->group_by("CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.id,'-',$this->tbl_as.b_user_id)");
    //     return $this->db->get('', 0);
    // }

    // //by Donny Dennison - 14 july 2021 14:14
    // //add-general-location-in-address
    // public function getGeneralLocationCustomer($nation_code, $b_user_id)
    // {
    //     $this->db->select_as("$this->tbl_as.id", "b_user_alamat_id", 0);
    //     $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
    //     $this->db->select_as("$this->tbl_as.is_default", "is_default", 0);
        
    //     //by Donny Dennison - 1 july 2021 14:42
    //     //add-general-location-in-address
    //     $this->db->select_as("IF($this->tbl5_as.custom_name IS NULL OR $this->tbl5_as.custom_name = '', $this->tbl5_as.original_name, $this->tbl5_as.custom_name)", "general_location", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);

    //     //by Donny Dennison - 1 july 2021 14:42
    //     //add-general-location-in-address
    //     $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');

    //     $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.b_user_id", $b_user_id);
    //     $this->db->where_as("$this->tbl_as.is_active", 1);
    //     $this->db->order_by("$this->tbl_as.is_default", "desc");
    //     $this->db->order_by("$this->tbl_as.id", "desc");
    //     $this->db->group_by('general_location');
    //     $this->db->limit(5);
    //     return $this->db->get('', 0);
    // }

    public function getAll($nation_code, $keyword='', $provinsi="DKI Jakarta")
    {
        $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));

        if (mb_strlen($keyword)>0) {
            $this->db->where_as("LOWER($this->tbl_as.kelurahan)", addslashes(strtolower($keyword)), 'AND', '%like%');
        }

        if($provinsi != "All"){
            $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($provinsi)));
        }

        $this->db->order_by("$this->tbl_as.id", "asc");
        // $this->db->group_by('postal_district');
        return $this->db->get('', 0);
    }

    // public function getAllPostalSector($nation_code, $postal_district)
    // {
    //     $this->db->select_as("$this->tbl_as.postal_sector", "postal_sector", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.is_active", 1);
    //     $this->db->where_as("$this->tbl_as.postal_district", $postal_district);


    //     $this->db->order_by("$this->tbl_as.postal_sector", "asc");
    //     return $this->db->get('', 0);
    // }

    // public function getPostalDistrictByKodepos($nation_code, $kodepos)
    // {
    //     $this->db->select_as("$this->tbl_as.postal_district", "postal_district", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.is_active", 1);
    //     $this->db->where_as("$this->tbl_as.postal_sector", SUBSTR($kodepos,0,2));
    //     return $this->db->get_first('', 0);
    // }

    public function checkInDBOrNot($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi, $kodepos)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($kelurahan)));
        $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($kecamatan)));
        $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($kabkota)));
        $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($provinsi)));
        $this->db->where_as("$this->tbl_as.kodepos", $this->db->esc($kodepos));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        return $this->db->get_first();
    }

}
