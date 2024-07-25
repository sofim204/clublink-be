<?php
class B_Karyawan_Model extends SENE_Model {
	var $is_cacheable;
	var $tbl = 'b_karyawan';
	var $tbl_as = 'bk';
	var $hashkey = 'uwmbeebbuwosas';
	public function __construct(){
		parent::__construct();
		$this->is_cacheable = 0;
    $this->db->from($this->tbl,$this->tbl_as);
	}
	public function getHashkey(){
		return $this->hashkey;
	}
	public function getTableAlias(){
		return $this->tbl_as;
	}
  public function getKaryawanAktif(){
    $sql = 'SELECT * FROM '.$this->tbl.' WHERE kstatus NOT LIKE "resigned" AND a_pengguna_id IS NOT NULL ORDER BY nama_depan ASC';
    return $this->db->query($sql);
  }
}
