<?php
class E_Jne_Model extends SENE_Model{
	var $tbl = 'e_jne';
	var $tbl_as = 'ej';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getAll($page=0,$pagesize=10,$sortCol="origin",$sortDir="ASC",$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select('id');
		$this->db->select('origin');
		$this->db->select('kode_jne');
		$this->db->select('kecamatan');
		$this->db->select('kabkota');
    $this->db->select('propinsi');
		$this->db->select('oke15_tarif');
		$this->db->select('oke15_est');
    $this->db->select('reg15_tarif');
		$this->db->select('reg15_est');
		$this->db->select('yes15_tarif');
    $this->db->select('yes15_est');
    $this->db->select('status');
		$this->db->from($this->tbl,$this->tbl_as);
		if(strlen($keyword)>1){
			$this->db->where("origin",$keyword,"OR","%like%",1,0);
			$this->db->where("kode_jne",$keyword,"OR","%like%",0,0);
      $this->db->where("kabkota",$keyword,"OR","%like%",0,0);
      $this->db->where("propinsi",$keyword,"OR","%like%",0,0);
      $this->db->where("status",$keyword,"OR","%like%",0,0);
      $this->db->where("kecamatan",$keyword,"OR","%like%",0,1);
    }
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAll($keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		if(strlen($keyword)>1){
      $this->db->where("origin",$keyword,"OR","%like%",1,0);
			$this->db->where("kode_jne",$keyword,"OR","%like%",0,0);
      $this->db->where("kabkota",$keyword,"OR","%like%",0,0);
      $this->db->where("propinsi",$keyword,"OR","%like%",0,0);
      $this->db->where("status",$keyword,"OR","%like%",0,0);
      $this->db->where("kecamatan",$keyword,"OR","%like%",0,1);
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
	public function getOngkir($provinsi,$kabkota,$kecamatan){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where('propinsi',$provinsi)->where('kabkota',$kabkota)->where('kecamatan',$kecamatan);
		$d =  $this->db->get_first('',0);
		if(!isset($d->id)){
			$this->db->from($this->tbl,$this->tbl_as);
			$this->db->where('propinsi',$provinsi)->where('kabkota',$kabkota)->order_by('reg15_tarif','desc');
			$d =  $this->db->get_first('',0);
			if(!isset($d->id)){
				$this->db->from($this->tbl,$this->tbl_as);
				$this->db->where('propinsi',$provinsi)->order_by('reg15_tarif','desc');
				$d =  $this->db->get_first('',0);
				if(!isset($d->id)) $d = new stdClass();
			}
		}
		return $d;
	}
	public function getOngkir2($provinsi,$kabkota,$kecamatan){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where('propinsi',$provinsi)->where('kabkota',$kabkota)->where('kecamatan',$kecamatan);
		return $this->db->get_first('',0);
	}
}
