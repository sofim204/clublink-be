<?php
class A_Config_Model extends SENE_Model{
	var $tbl = 'a_config';
	var $tbl_as = 'ac';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	private function __getNilaiFromObject($dbresobj){
		if(isset($dbresobj->nilai)){
			if(strlen($dbresobj->nilai)==0 && strlen($dbresobj->nilai_default)>0){
				return $dbresobj->nilai_default;
			}else{
				return $dbresobj->nilai;
			}
		}else{
			return '';
		}
	}
	
	public function get(){
		$this->db->order_by('nama','asc')->limit(100);
		return $this->db->get();
	}
	public function getObject(){
		$this->db->order_by('nama','asc')->limit(100);
		$d = $this->db->get();
		$dObj = new stdClass();
		foreach($d as $val){
			$cfg_nama = $val->nama;
			$cfg_nilai = $val->nilai_default;
			if(strlen($val->nilai)>0){
				$cfg_nilai = $val->nilai;
			}
			$dObj->{$cfg_nama} = $cfg_nilai;
		}
		return $dObj;
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
	public function checkNama($nama,$id=0){
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->where("nama",$nama);
		if(!empty($id)) $this->db->where("id",$id,'AND','!=');
		$d = $this->db->from($this->tbl)->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
  public function check($nama){
    $this->db->where("nama",$nama);
    $d = $this->db->get_first();
    if(isset($d->id)){
      return $d;
    }else{
      $di['nama'] = $nama;
      $id = $this->set($di);
      return $this->getById($id);
    }
  }
	public function updateNilaiByNama($nama,$nilai){
		$d = $this->check($nama);
		if(isset($d->id)){
			return $this->update($d->id,array("nilai"=>$nilai));
		}else{
			return 0;
		}
	}
  public function checkNilai($nama){
    $this->db->where("nama",$nama);
    $d = $this->db->get_first();
    if(isset($d->id)){
			//var_dump($d);
			//die();
      return $this->__getNilaiFromObject($d);
    }else{
      $di['nama'] = $nama;
      $id = $this->set($di);
      $d = $this->getById($id);
      return $this->__getNilaiFromObject($d);
    }
  }
}
