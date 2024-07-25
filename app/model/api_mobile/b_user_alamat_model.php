<?php
class B_User_Alamat_model extends JI_Model
{
    public $tbl = 'b_user_alamat';
    public $tbl_as = 'bua';
    public $tbl2 = 'a_negara';
    public $tbl2_as = 'an';
    public $tbl3 = 'b_lokasi';
    public $tbl3_as = 'bl';
    public $tbl4 = 'b_kodepos';
    public $tbl4_as = 'bkp';

    public $tbl30 = 'common_code';
    public $tbl30_as = 'cc';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl3()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl3_as.nation_code", "=", "$this->tbl_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl3_as.id", "=", "COALESCE($this->tbl_as.b_lokasi_id,0)");
        return $composites;
    }

    private function __joinTbl4()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl4_as.nation_code", "=", "$this->tbl_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl4_as.id", "=", "COALESCE($this->tbl_as.b_kodepos_id,0)");
        return $composites;
    }

    public function getLastId($nation_code, $b_user_id)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("nation_code", $nation_code);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }

    public function getAddressType($nation_code)
    {
        $this->db->from($this->tbl30, $this->tbl30_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("classified", "address");
        
        //by Donny Dennison - 17 juni 2020 20:18
        // request by Mr Jackie dont show basic address and billing address option
        $this->db->where("code", "A0",'AND','!=');
        $this->db->where("code", "A1",'AND','!=');

        $this->db->where("use_yn", "y");
        return $this->db->get('', 0);
    }

    public function getByUserId($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.address_status", "address_status_code", 0);
        $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status", 0);
        $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status_type", 0);
        $this->db->select_as("$this->tbl_as.judul", "judul", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_nama"), "penerima_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_telp"), "penerima_telp", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kecamatan, ', ', $this->tbl_as.kabkota, ', ', $this->tbl_as.provinsi)", "alamat2", 0);
        $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
        $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
        $this->db->select_as("$this->tbl_as.negara", "negara", 0);
        $this->db->select_as("$this->tbl_as.latitude", "latitude", 0);
        $this->db->select_as("$this->tbl_as.longitude", "longitude", 0);
        $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status_type", 0);
        $this->db->select_as("$this->tbl_as.catatan", "catatan", 0);
        $this->db->select_as("$this->tbl_as.is_default", "is_default", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join($this->tbl30, $this->tbl30_as, "code", $this->tbl_as, "address_status", "left");

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.is_active", 1);
        $this->db->order_by("$this->tbl_as.is_default", "desc");
        // $this->db->order_by("$this->tbl_as.id", "desc");
        $this->db->order_by($this->__decrypt("$this->tbl_as.alamat2"), "asc");
        $this->db->group_by("$this->tbl_as.id");
        return $this->db->get('', 0);
    }

    //by Donny Dennison - 1 desember 2020 16:29
    //list-produt-sameStreet-neighborhood-all-from-user-address
    public function getByUserIdDefault($nation_code, $b_user_id)
    {

        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.address_status", "address_status_code", 0);
        $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status", 0);
        $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status_type", 0);
        $this->db->select_as("$this->tbl_as.judul", "judul", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_nama"), "penerima_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_telp"), "penerima_telp", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kecamatan, ', ', $this->tbl_as.kabkota, ', ', $this->tbl_as.provinsi)", "alamat2", 0);
        $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
        $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
        $this->db->select_as("$this->tbl_as.negara", "negara", 0);
        $this->db->select_as("$this->tbl_as.latitude", "latitude", 0);
        $this->db->select_as("$this->tbl_as.longitude", "longitude", 0);
        $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status_type", 0);
        $this->db->select_as("$this->tbl_as.catatan", "catatan", 0);
        $this->db->select_as("$this->tbl_as.is_default", "is_default", 0);

        //by Donny Dennison - 8 july 2021 11:02
        //add-like-product
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);

        // by Muhammad Sofi - 2 November 2021 10:30
        // show alamat4 in api response
        // $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat4", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join($this->tbl30, $this->tbl30_as, "code", $this->tbl_as, "address_status", "left");
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.is_active", 1);
        // $this->db->where_as("$this->tbl_as.is_default", 1);
        $this->db->order_by("$this->tbl_as.is_default", "desc");
        // $this->db->order_by("$this->tbl_as.id", "desc");
        return $this->db->get_first('', 0);
    }

    //by Donny Dennison - 16 july 2021 16:22
    //limit-address-to-5
    // public function countByUserId($b_user_id){
    public function countByUserId($nation_code, $b_user_id){
        $this->db->select_as("COUNT($this->tbl_as.id)", 'total', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join($this->tbl30, $this->tbl30_as, "code", $this->tbl_as, "address_status", "left");

        //by Donny Dennison - 16 july 2021 16:22
        //limit-address-to-5
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);

        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.is_active", 1);
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function getById($nation_code, $b_user_id, $id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.catatan", "address_notes", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_nama"), "penerima_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_telp"), "penerima_telp", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.id", $id);
        return $this->db->get_first();
    }
    public function getByIdUserId($nation_code, $b_user_id, $id)
    {
        $this->db->select_as("DISTINCT $this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.address_status", "address_status_code", 0);
        $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status", 0);
        $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status_type", 0);
        $this->db->select_as("$this->tbl_as.judul", "judul", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_nama"), "penerima_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_telp"), "penerima_telp", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kecamatan, ', ', $this->tbl_as.kabkota, ', ', $this->tbl_as.provinsi)", "alamat2", 0);
        $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
        $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
        $this->db->select_as("$this->tbl_as.negara", "negara", 0);
        $this->db->select_as("$this->tbl_as.latitude", "latitude", 0);
        $this->db->select_as("$this->tbl_as.longitude", "longitude", 0);
        $this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
        $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status_type", 0);
        $this->db->select_as("$this->tbl_as.catatan", "catatan", 0);
        $this->db->select_as("$this->tbl_as.is_default", "is_default", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join($this->tbl30, $this->tbl30_as, "code", $this->tbl_as, "address_status", "left");

        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where("$this->tbl_as.id", $id);
        $this->db->where("$this->tbl_as.is_active", 1);
        return $this->db->get_first('', 0);
    }
    
    public function set($di)
    {
        if (isset($di['penerima_nama'])) {
            if (strlen($di['penerima_nama'])) {
                $di['penerima_nama'] = $this->__encrypt($di['penerima_nama']);
            }
        }
        if (isset($di['penerima_telp'])) {
            if (strlen($di['penerima_telp'])) {
                $di['penerima_telp'] = $this->__encrypt($di['penerima_telp']);
            }
        }
        if (isset($di['alamat2'])) {
            if (strlen($di['alamat2'])) {
                $di['alamat2'] = $this->__encrypt($di['alamat2']);
            }
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }
    public function update($nation_code, $b_user_id, $id, $du)
    {
        if (isset($du['penerima_nama'])) {
            if (strlen($du['penerima_nama'])) {
                $du['penerima_nama'] = $this->__encrypt($du['penerima_nama']);
            }
        }
        if (isset($du['penerima_telp'])) {
            if (strlen($du['penerima_telp'])) {
                $du['penerima_telp'] = $this->__encrypt($du['penerima_telp']);
            }
        }
        if (isset($du['alamat2'])) {
            if (strlen($du['alamat2'])) {
                $du['alamat2'] = $this->__encrypt($du['alamat2']);
            }
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du);
    }
    public function updateByUserId($nation_code, $b_user_id, $du)
    {
        //by Donny Dennison - 27 october 2021 11:00
        //if edit profile then also change address penerima_nama and penerima_telp
        //START by Donny Dennison - 27 october 2021 11:00

        if (isset($du['penerima_nama'])) {
            if (strlen($du['penerima_nama'])) {
                $du['penerima_nama'] = $this->__encrypt($du['penerima_nama']);
            }
        }
        if (isset($du['penerima_telp'])) {
            if (strlen($du['penerima_telp'])) {
                $du['penerima_telp'] = $this->__encrypt($du['penerima_telp']);
            }
        }
        if (isset($du['alamat2'])) {
            if (strlen($du['alamat2'])) {
                $du['alamat2'] = $this->__encrypt($du['alamat2']);
            }
        }

        //END by Donny Dennison - 27 october 2021 11:00

        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        return $this->db->update($this->tbl, $du);
    }
    public function updateByIdUserId($nation_code, $id, $b_user_id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        $this->db->where("b_user_id", $b_user_id);
        return $this->db->update($this->tbl, $du);
    }
    public function delByUserId($nation_code, $id, $b_user_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        $this->db->where("b_user_id", $b_user_id);
        return $this->db->delete($this->tbl);
    }
    public function getByIds($nation_code, $b_user_id, $ids)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status", 0);
        $this->db->select_as("$this->tbl_as.address_status", "address_status_code", 0);
        $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status_type", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_nama"), "penerima_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_telp"), "penerima_telp", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
        $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
        $this->db->select_as("$this->tbl_as.negara", "negara", 0);
        $this->db->select_as("$this->tbl_as.latitude", "latitude", 0);
        $this->db->select_as("$this->tbl_as.longitude", "longitude", 0);
        $this->db->select_as("$this->tbl_as.catatan", "catatan", 0);
        $this->db->select_as("$this->tbl_as.is_default", "is_default", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join($this->tbl30, $this->tbl30_as, "code", $this->tbl_as, "address_status", "left");
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_in("$this->tbl_as.id", $ids);
        return $this->db->get();
    }
    public function getIdUserIdIN($concat_id_b_user_ids=array())
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.address_status", "address_status_code", 0);
        $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status_type", 0);
        $this->db->select_as("$this->tbl_as.judul", "judul", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_nama"), "penerima_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_telp"), "penerima_telp", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
        $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
        $this->db->select_as("$this->tbl_as.negara", "negara", 0);
        $this->db->select_as("$this->tbl_as.latitude", "latitude", 0);
        $this->db->select_as("$this->tbl_as.longitude", "longitude", 0);
        $this->db->select_as("$this->tbl_as.catatan", "catatan", 0);
        $this->db->select_as("$this->tbl_as.is_default", "is_default", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join($this->tbl30, $this->tbl30_as, "code", $this->tbl_as, "address_status", "left");
        $this->db->where_in("CONCAT($this->tbl_as.id,'-',COALESCE($this->tbl_as.b_user_id,0))", $concat_id_b_user_ids);
        //$this->db->group_by("CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.id,'-',$this->tbl_as.b_user_id)");
        return $this->db->get('', 0);
    }
    public function getByIdFull($nation_code, $b_user_id, $id)
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.judul", "judul", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_nama"), "penerima_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_telp"), "penerima_telp", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
        $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
        $this->db->select_as("$this->tbl_as.negara", "negara", 0);
        $this->db->select_as("$this->tbl_as.latitude", "latitude", 0);
        $this->db->select_as("$this->tbl_as.longitude", "longitude", 0);
        $this->db->select_as("$this->tbl_as.catatan", "catatan", 0);
        $this->db->select_as("$this->tbl_as.is_default", "is_default", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join($this->tbl30, $this->tbl30_as, "code", $this->tbl_as, "address_status", "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $this->db->where_as("$this->tbl_as.is_active", 1);
        return $this->db->get_first('', 0);
    }

    public function getByUserDefaultFull($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.judul", "judul", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_nama"), "penerima_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.penerima_telp"), "penerima_telp", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
        $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
        $this->db->select_as("$this->tbl_as.negara", "negara", 0);
        $this->db->select_as("$this->tbl_as.latitude", "latitude", 0);
        $this->db->select_as("$this->tbl_as.longitude", "longitude", 0);
        $this->db->select_as("$this->tbl_as.catatan", "catatan", 0);
        $this->db->select_as("$this->tbl_as.is_default", "is_default", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join($this->tbl30, $this->tbl30_as, "code", $this->tbl_as, "address_status", "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.is_active", 1);
        $this->db->order_by("$this->tbl_as.is_default", "desc");
        $this->db->order_by("$this->tbl_as.id", "desc");
        return $this->db->get_first('', 0);
    }

    //by Donny Dennison - 9 july 2021 14:54
    //add-total-people-around-you-in-product-list
    public function countPeopleAround($nation_code, $type="", $pelangganAddress)
    {
        $this->db->exec("SET NAMES 'UTF8MB4'");
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.b_user_id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        
        //by Donny Dennison - 14 july 2021 14:14
        //add-general-location-in-address
        // $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($pelangganAddress->b_user_id),'and','!=');
        
        //advanced filter

        if(isset($pelangganAddress->alamat2)){

            // $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strpos($pelangganAddress->alamat2," ")));
            
            $pelangganAddress->alamat2 = strtolower(trim($pelangganAddress->alamat2));

            if (substr($pelangganAddress->alamat2, 0, strlen("jalan raya")) == "jalan raya") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jalan raya")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jalan")) == "jalan") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jalan")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jln.")) == "jln.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jln.")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jln")) == "jln") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jln")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("jl.")) == "jl.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jl.")));
            }
            
            if (substr($pelangganAddress->alamat2, 0, strlen("jl")) == "jl") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("jl")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("gang")) == "gang") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gang")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("gg.")) == "gg.") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gg.")));
            }

            if (substr($pelangganAddress->alamat2, 0, strlen("gg")) == "gg") {
                $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, strlen("gg")));
            }

            if (strpos($pelangganAddress->alamat2, ' ') !== false) {
                
                $totalSpace = strpos($pelangganAddress->alamat2," ");

                $tempAlamat2 = trim(substr($pelangganAddress->alamat2, 0, $totalSpace));

                if (strpos($tempAlamat2, ' ') !== false) {

                    $totalSpace += strpos($tempAlamat2, ' ') + 1;

                    $pelangganAddress->alamat2 = trim(substr($pelangganAddress->alamat2, 0, $totalSpace));
                }
                unset($totalSpace, $tempAlamat2);
            
            }

            if($type == 'sameStreet'){

                $this->db->where_as('LOWER(CAST('.$this->__decrypt("$this->tbl_as.alamat2").' AS CHAR(50)))', addslashes(strtolower($pelangganAddress->alamat2)), 'and', '%like%', 1, 1);
                $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($pelangganAddress->kelurahan)));
                $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

            }else if($type == 'neighborhood'){

                $this->db->where_as("LOWER($this->tbl_as.kelurahan)", $this->db->esc(strtolower($pelangganAddress->kelurahan)));
                $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

            }else if($type == 'district'){

                $this->db->where_as("LOWER($this->tbl_as.kecamatan)", $this->db->esc(strtolower($pelangganAddress->kecamatan)));
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

            }else if($type == 'city'){
                
                $this->db->where_as("LOWER($this->tbl_as.kabkota)", $this->db->esc(strtolower($pelangganAddress->kabkota)));
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

            }else if($type == 'province'){
                
                $this->db->where_as("LOWER($this->tbl_as.provinsi)", $this->db->esc(strtolower($pelangganAddress->provinsi)));

            }

        }

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    //by Donny Dennison - 14 july 2021 14:14
    //add-general-location-in-address
    public function getGeneralLocationCustomer($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.id", "b_user_alamat_id", 0);
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.is_default", "is_default", 0);
        $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->group_by("CONCAT($this->tbl_as.kelurahan,'-',$this->tbl_as.kecamatan,'-',$this->tbl_as.kabkota,'-',$this->tbl_as.provinsi)");
        $this->db->order_by("$this->tbl_as.is_default", "desc");
        $this->db->order_by("$this->tbl_as.id", "desc");
        $this->db->limit(5);
        return $this->db->get('', 0);
    }

    // public function countByUserIdKodepos($nation_code, $b_user_id, $kodepos, $b_user_alamat_id=0){
    //     $this->db->select_as("COUNT($this->tbl_as.id)", 'total', 0);
    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     $this->db->where_as("$this->tbl_as.kodepos", $this->db->esc($kodepos));
        
    //     if($b_user_alamat_id != 0){
    //         $this->db->where_as("$this->tbl_as.id", $this->db->esc($b_user_alamat_id),'AND','!=');
    //     }

    //     $this->db->where_as("$this->tbl_as.is_active", 1);
    //     $d = $this->db->get_first();
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }

}
