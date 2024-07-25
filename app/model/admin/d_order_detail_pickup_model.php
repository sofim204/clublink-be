<?php
class D_Order_Detail_Pickup_Model extends JI_Model
{
    public $tbl = 'd_order_detail_pickup';
    public $tbl_as = 'dodpu';

    public function __construct()
    {
        parent::__construct();
        $this->is_cacheable = 0;
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function set($di)
    {
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
    public function setMass($dis)
    {
        foreach ($dis as &$di) {
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
        }
        return $this->db->insert_multi($this->tbl, $dis, 0);
    }
    public function edit($nation_code, $d_order_id, $id, $du)
    {
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
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function update($nation_code, $d_order_id, $id, $du)
    {
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
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function updateByOrderId($nation_code, $d_order_id, $du)
    {
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
        return $this->db->update($this->tbl, $du, 0);
    }
    public function del($nation_code, $d_order_id, $id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("id", $id);
        return $this->db->delete($this->tbl, 0);
    }

    public function getById($nation_code, $d_order_id, $d_order_detail_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.catatan", "address_notes");
        $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl_as.nama").",'')", "penerima_nama", 0);
        $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl_as.telp").",'')", "penerima_telp", 0);
        $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl_as.alamat2").",'')", "alamat2", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $this->db->esc($d_order_detail_id));
        return $this->db->get_first('', 0);
    }
}
