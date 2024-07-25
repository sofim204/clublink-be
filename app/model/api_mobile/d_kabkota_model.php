<?php
class D_Kabkota_Model extends SENE_Model{
	var $tbl = 'd_kabkota';
	var $tbl_as = 'dk';
	var $tbl2 = 'd_provinsi';
	var $tbl2_as = 'dp';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getByProvinsiIdAndId($provinsi_id,$id){
		$this->db->where('id',$id);
		$this->db->where('provinsi_id',$provinsi_id);
		$this->db->order_by('nama_kabkota','asc');
		return $this->db->get("object",0);
	}
	public function getByProvinsiId($provinsi_id){
		$this->db->where('provinsi_id',$provinsi_id);
		$this->db->order_by('nama_kabkota','asc');
		return $this->db->get("object",0);
	}
	public function getAll($page=0,$pagesize=10,$sortCol="provinsi_id",$sortDir="ASC",$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id,$this->tbl2_as.nama_provinsi,$this->tbl_as.nama_kabkota,$this->tbl_as.latitude,$this->tbl_as.longitude",'longitude',0);
		$this->db->join($this->tbl2,$this->tbl2_as,'id',$this->tbl_as,'provinsi_id','left');
		$this->db->from($this->tbl,$this->tbl_as);
		if(strlen($keyword)>1){
			$this->db->where("provinsi_id",$keyword,"OR","%like%",1,0);
			$this->db->where("nama_kabkota",$keyword,"OR","%like%",0,1);
		}
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAll($keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join($this->tbl2,$this->tbl2_as,'id',$this->tbl_as,'provinsi_id','left');
		if(strlen($keyword)>1){
      $this->db->where("provinsi_id",$keyword,"OR","%like%",1,0);
			$this->db->where("nama_kabkota",$keyword,"OR","%like%",0,1);
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
}
