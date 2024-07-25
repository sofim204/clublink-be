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

    public function set($d)
    {
        return $this->db->insert($this->tbl, $d, 0, 0);
    }
    public function setMass($ds)
    {
        return $this->db->insert_multi($this->tbl, $ds, 0);
    }
    public function edit($nation_code, $d_order_id, $id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function update($nation_code, $d_order_id, $id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function updateByOrderId($nation_code, $d_order_id, $du)
    {
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
    public function delByOrderId($nation_code,$d_order_id){
      $this->db->where("nation_code",$nation_code);
        $this->db->where("d_order_id",$d_order_id);
        return $this->db->delete($this->tbl);
    }

    public function getById($nation_code, $d_order_id, $d_order_detail_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.catatan", "address_notes");
        $this->db->select_as($this->__decrypt("$this->tbl_as.nama"), "penerima_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "penerima_telp", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $this->db->esc($d_order_detail_id));
        return $this->db->get_first();
    }

    public function getQxpressmodel($nation_code, $d_order_id, $d_order_detail_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.catatan", "address_notes");
        $this->db->select_as($this->__decrypt("$this->tbl_as.nama"), "penerima_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "penerima_telp", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $this->db->esc($d_order_detail_id));
        return $this->db->get_first();
    }

    
}
