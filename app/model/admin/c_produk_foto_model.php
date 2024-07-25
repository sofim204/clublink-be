<?php
class C_Produk_Foto_Model extends SENE_Model{
	var $tbl = 'c_produk_foto';
	var $tbl_as = 'cpf';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getTableAlias(){
		return $this->tbl_as;
	}
	public function getByProdukId($nation_code, $c_produk_id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_produk_id",$c_produk_id);
		return $this->db->get();
	}

	public function getVideoByProdukId($nation_code, $c_produk_id){
		$this->db->select_as("SUBSTRING_INDEX($this->tbl_as.url, '.', -1)","url_ext", 0);
		$this->db->select_as("$this->tbl_as.url","url",0);
		$this->db->select_as("$this->tbl_as.jenis","jenis",0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.c_produk_id", $this->db->esc($c_produk_id), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.jenis", $this->db->esc("video"), "AND", "=", 0, 0);
		$this->db->order_by("url_ext");
		return $this->db->get();
	}

	public function getTotalUploadVideo($nation_code, $c_produk_id, $operator){
		$this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.c_produk_id", $this->db->esc($c_produk_id), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.jenis", $this->db->esc("video"), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.url", ".png", "AND", "%$operator%", 0, 0);
        $d = $this->db->get_first("object", 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
	}

	public function setMass($cpfs_data){
		$this->db->insert_multi($this->tbl, $cpfs_data);
	}
}
