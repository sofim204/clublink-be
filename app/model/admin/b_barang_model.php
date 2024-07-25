<?php
class B_Barang_Model extends SENE_Model {
	var $is_cacheable;
	var $tbl = 'b_barang';
	var $tbl_as = 'bb';
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
  public function getKendaraan($tipe='semua'){
    $tipe = strtoupper($tipe);
    $this->db->where('can_use',1);
    if($tipe == 'RD' || $tipe == 'RE'){
      $this->db->where('kode',$tipe.'.','AND','like%');
    }else{
      $this->db->where('kode','RD.','OR','like%',1,0);
      $this->db->where('kode','RE.','OR','like%',0,1);
    }
    $this->db->order_by('nama','asc');
    return $this->db->get('object',0);
  }
	public function getById($id){
		$this->db->where('id',$id);
		return $this->db->get_first($id);
	}
}
