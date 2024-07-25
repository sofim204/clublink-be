<?php
class D_Order_Alamat_Model extends JI_Model
{
    public $tbl = 'd_order_alamat';
    public $tbl_as = 'doa';
    public $tbl2 = 'd_order';
    public $tbl2_as = 'dor';
    // public $tbl3 = 'b_user_alamat';
    // public $tbl3_as = 'bua';
    public $tbl30 = 'common_code';
    public $tbl30_as = 'cc';

    public function __construct()
    {
        parent::__construct();
        $this->is_cacheable = 0;
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
    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        if (isset($di['nama'])) {
            if (strlen($di['nama'])) {
                $di['nama'] = $this->__encrypt($di['nama']);
            }
        }
        if (isset($di['telp'])) {
            if (strlen($di['telp'])) {
                $di['telp'] = $this->__encrypt($di['telp']);
            }
        }
        if (isset($di['alamat2'])) {
            if (strlen($di['alamat2'])) {
                $di['alamat2'] = $this->__encrypt($di['alamat2']);
            }
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }
    public function update($nation_code, $d_order_id, $b_user_id, $id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        if (isset($du['nama'])) {
            if (strlen($du['nama'])) {
                $du['nama'] = $this->__encrypt($du['nama']);
            }
        }
        if (isset($du['telp'])) {
            if (strlen($du['telp'])) {
                $du['telp'] = $this->__encrypt($du['telp']);
            }
        }
        if (isset($du['alamat2'])) {
            if (strlen($du['alamat2'])) {
                $du['alamat2'] = $this->__encrypt($du['alamat2']);
            }
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function updateByAddressStatus($nation_code, $d_order_id, $b_user_id, $address_status, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        if (isset($du['nama'])) {
            if (strlen($du['nama'])) {
                $du['nama'] = $this->__encrypt($du['nama']);
            }
        }
        if (isset($du['telp'])) {
            if (strlen($du['telp'])) {
                $du['telp'] = $this->__encrypt($du['telp']);
            }
        }
        if (isset($du['alamat2'])) {
            if (strlen($du['alamat2'])) {
                $du['alamat2'] = $this->__encrypt($du['alamat2']);
            }
        }
        $this->db->where("address_status", $address_status);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("b_user_id", $b_user_id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function del($nation_code, $d_order_id, $b_user_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("b_user_id", $b_user_id);
        return $this->db->delete($this->tbl);
    }
    public function delByOrderId($nation_code, $d_order_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        return $this->db->delete($this->tbl);
    }

    public function cleanup($nation_code, $d_order_id, $b_user_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("b_user_id", $b_user_id,'AND','<>');
        return $this->db->delete($this->tbl);
    }

    public function getByOrderIdBuyerId($nation_code, $d_order_id, $b_user_id)
    {
        $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "kind", 0);
        $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status", 0);
        $this->db->select_as("$this->tbl_as.judul", "judul", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.nama"), "nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        // by Muhammad Sofi - 4 November 2021 10:00
        // remark code
        // $this->db->select_as("$this->tbl_as.alamat", "alamat", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
        $this->db->select_as("$this->tbl_as.negara", "negara", 0);
        $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
        $this->db->select_as("$this->tbl_as.latitude", "latitude", 0);
        $this->db->select_as("$this->tbl_as.address_notes", "address_notes", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join($this->tbl30, $this->tbl30_as, "code", $this->tbl_as, "address_status", "left");
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        return $this->db->get();
    }

    /**
     * retrieve addresses based on certain criteria
     * @param  int $d_order_id            ID from d_order
     * @param  int $b_user_id_buyer       ID from b_user_id for buyer
     * @param  string $address_status     address status / address code
     * @return object                     return row result
     */
    public function getByOrderIdBuyerIdStatusAddressFull($nation_code,$d_order_id, $b_user_id_buyer, $address_status)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.nama"), "nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("$this->tbl_as.address_status", $this->db->esc($address_status));
        return $this->db->get_first();
    }

    /**
     * retrieve addresses based on certain criteria
     * @param  int $d_order_id            ID from d_order
     * @param  int $b_user_id_buyer       ID from b_user_id for buyer
     * @param  string $address_status     address status / address code
     * @return object                     return row result
     */
    public function getByOrderIdBuyerIdStatusAddress($nation_code,$d_order_id, $b_user_id, $address_status)
    {
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "kind", 0);
        $this->db->select_as("COALESCE($this->tbl30_as.codename,'-')", "address_status", 0);
        $this->db->select_as("$this->tbl_as.judul", "judul", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.nama"), "nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        // by Muhammad Sofi - 4 November 2021 10:00
        // remark code
        // $this->db->select_as("$this->tbl_as.alamat", "alamat", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
        $this->db->select_as("$this->tbl_as.negara", "negara", 0);
        $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
        $this->db->select_as("$this->tbl_as.latitude", "latitude", 0);
        $this->db->select_as("$this->tbl_as.longitude", "longitude", 0);
        $this->db->select_as("$this->tbl_as.address_notes", "address_notes", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join($this->tbl30, $this->tbl30_as, "code", $this->tbl_as, "address_status", "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.address_status", $this->db->esc($address_status));
        return $this->db->get_first('', 0);
    }
    public function getById($nation_code, $d_order_id, $address_status)
    {
        // by Muhammad Sofi - 4 November 2021 10:00
        // remark code
        // $this->db->select_as("$this->tbl_as.*, $this->tbl_as.alamat", "alamat1", 0);

        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.nama"), "penerima_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "penerima_telp", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.address_status", $this->db->esc($address_status));
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        return $this->db->get_first('', 0);
    }
    public function check($nation_code,$d_order_id){
      $this->db->select_as("COUNT(*)",'total',0);
      $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
      $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
      $d = $this->db->get_first('', 0);
      if(isset($d->total)) return $d->total;
      return 0;
    }
}
