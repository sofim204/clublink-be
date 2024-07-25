<?php
class D_Order_Alamat_Model extends JI_Model
{
    public $tbl = 'd_order_alamat';
    public $tbl_as = 'doa';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }
    public function getShippingByOrderId($nation_code, $d_order_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.address_notes", "address_notes");
        $this->db->select_as($this->__decrypt("$this->tbl_as.nama"), "nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.nama"), "penerima_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "penerima_telp", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("nation_code", $this->db->esc($nation_code));
        $this->db->where_as("address_status", $this->db->esc("A2"));
        $this->db->where_as("d_order_id", $this->db->esc($d_order_id));
        return $this->db->get_first();
    }
    public function getBillingByOrderId($nation_code, $d_order_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.address_notes", "address_notes");
        $this->db->select_as($this->__decrypt("$this->tbl_as.nama"), "nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.nama"), "penerima_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "penerima_telp", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "alamat2", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("nation_code", $this->db->esc($nation_code));
        $this->db->where_as("address_status", $this->db->esc("A1"));
        $this->db->where_as("d_order_id", $this->db->esc($d_order_id));
        return $this->db->get_first();
    }
}
