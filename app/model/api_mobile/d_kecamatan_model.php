<?php
class D_Kecamatan_Model extends SENE_Model{
	var $tbl = 'd_kecamatan';
	var $tbl_as = 'dkc';
	var $tbl2 = 'd_kabkota';
	var $tbl2_as = 'dkb';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getByKabkotaId($kabkota_id){
		$this->db->where('kabkota_id',$kabkota_id);
		$this->db->order_by('nama_kecamatan','asc');
		return $this->db->get("object",0);
	}
	public function getAll($page=0,$pagesize=10,$sortCol="kabkota_id",$sortDir="ASC",$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id,$this->tbl2_as.nama_kabkota,$this->tbl_as.nama_kecamatan,$this->tbl_as.latitude,$this->tbl_as.longitude",'longitude',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join($this->tbl2,$this->tbl2_as,'id',$this->tbl_as,'kabkota_id','left');
		if(strlen($keyword)>1){
			$this->db->where("kabkota_id",$keyword,"OR","%like%",1,0);
			$this->db->where("nama_kecamatan",$keyword,"OR","%like%",0,1);
		}
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAll($keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join($this->tbl2,$this->tbl2_as,'id',$this->tbl_as,'kabkota_id','left');
		if(strlen($keyword)>1){
      $this->db->where("kabkota_id",$keyword,"OR","%like%",1,0);
			$this->db->where("nama_kecamatan",$keyword,"OR","%like%",0,1);
		}
		$d = $this->db->from($this->tbl)->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	public function getById($id){
		$this->db->where("id",$id);
		return $this->db->get_first();
	}
	public function set($di){
		if(!is_array($di)) return 0;
		$this->db->insert($this->tbl,$di,0,0);
		return $this->db->last_id;
	}
	public function update($id,$du){
		if(!is_array($du)) return 0;
		$this->db->where("id",$id);
    return $this->db->update($this->tbl,$du,0);
	}
	public function del($id){
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}
	public function checkKode($id=0){
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->where("id",$id);
		$d = $this->db->from($this->tbl)->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	public function checkDuplikat($kabkota_id,$nama_kecamatan,$id=0){
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->where('kabkota_id',$kabkota_id);
		$this->db->where('nama_kecamatan',$nama_kecamatan);
		if($id>0) $this->db->where("id",$id,'and','!=');
		$this->db->from($this->tbl);
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	public function getALamatLengkapByKecamatanId($kecamatan_id){
		$sql = 'SELECT * 
FROM `d_kecamatan` k 
	LEFT JOIN d_kabkota kk ON kk.id = k.kabkota_id 
	LEFT JOIN d_provinsi p ON p.id = kk.provinsi_id 
WHERE k.id = '.$this->db->esc($kecamatan_id).'
LIMIT 0,1';
		$d = $this->db->query($sql);
		if(isset($d[0]->id)) return $d[0];
		return new stdClass();
	}
}
