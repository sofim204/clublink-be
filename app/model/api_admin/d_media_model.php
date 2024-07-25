<?php
class D_Media_Model extends SENE_Model {
	var $is_cacheable;
	var $tbl = 'd_media';
	var $tbl_alias = 'dm';
	var $hashkey = 'uwmbeebbuwosas';
	public function __construct(){
		parent::__construct();
		$this->is_cacheable = 0;
    $this->db->from($this->tbl,$this->tbl_alias);
	}
	public function getHashkey(){
		return $this->hashkey;
	}
	public function getTableAlias(){
		return $this->tbl_alias;
	}
	public function getAll($b_user_id,$page=0,$pagesize=10,$sortCol="cdate",$sortDir="DESC",$keyword="",$tglmax="",$tglmin=""){
		$this->db->flushQuery();
		$this->db->select_as($this->tbl_alias.".id","id",0);
		$this->db->select_as("CONCAT(bu.fnama,' ',bu.lnama)","author",0);
		$this->db->select_as($this->tbl_alias.".title","title",0);
		$this->db->select_as($this->tbl_alias.".excerpt","excerpt",0);
		$this->db->select_as("".$this->tbl_alias.".featured_image","featured_image",0);
		$this->db->select_as("".$this->tbl_alias.".cdate","cdate",0);
		$this->db->select_as("".$this->tbl_alias.".status","status",0);
		$this->db->order_by($sortCol,$sortDir);
		$this->db->from($this->tbl,$this->tbl_alias);
		$this->db->join("b_user","bu","id",$this->tbl_alias,"b_user_id","left");
		if(!empty($b_user_id)){
			$this->db->where($this->tbl_alias.".b_user_id",$b_user_id,"AND","like");
		}
		if(!empty($tglmax) && empty($tglmin)){
			$tglmin = $tglmax;
		}else if(empty($tglmax) && !empty($tglmin)){
			$tglmax = $tglmin;
		}

		if(!empty($tglmax) && !empty($tglmin)){
			$this->db->between("DATE(".$this->tbl_alias.".tanggal_pernikahan)",'DATE("'.$tglmin.'")','DATE("'.$tglmax.'")');
		}

		if(strlen($keyword)>2){
			$this->db->where("bu.fnama",$keyword,"OR","%like%");
			$this->db->where("bu.lnama",$keyword,"OR","%like%");
			$this->db->where($this->tbl_alias.".title",$keyword,"OR","%like%");
			$this->db->where($this->tbl_alias.".slug",$keyword,"OR","%like%");
			$this->db->where($this->tbl_alias.".excerpt",$keyword,"OR","%like%");
			$this->db->where($this->tbl_alias.".content",$keyword,"OR","%like%");
			$this->db->where($this->tbl_alias.".status",$keyword,"OR","%like%");
		}
		$this->db->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function getAllCount($b_user_id,$keyword="",$tglmax="",$tglmin=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_alias);
		$this->db->join("b_user","bu","id",$this->tbl_alias,"b_user_id","left");
		if(!empty($b_user_id)){
			$this->db->where($this->tbl_alias.".b_user_id",$b_user_id,"AND","like");
		}
		if(!empty($tglmax) && empty($tglmin)){
			$tglmin = $tglmax;
		}else if(empty($tglmax) && !empty($tglmin)){
			$tglmax = $tglmin;
		}

		if(!empty($tglmax) && !empty($tglmin)){
			$this->db->between("DATE(".$this->tbl_alias.".fdate)",'DATE("'.$tglmin.'")','DATE("'.$tglmax.'")');
		}

		if(strlen($keyword)>2){
			$this->db->where("bu.fnama",$keyword,"OR","%like%");
			$this->db->where("bu.lnama",$keyword,"OR","%like%");
			$this->db->where($this->tbl_alias.".title",$keyword,"OR","%like%");
			$this->db->where($this->tbl_alias.".slug",$keyword,"OR","%like%");
			$this->db->where($this->tbl_alias.".excerpt",$keyword,"OR","%like%");
			$this->db->where($this->tbl_alias.".content",$keyword,"OR","%like%");
			$this->db->where($this->tbl_alias.".status",$keyword,"OR","%like%");
		}
		$d = $this->db->from($this->tbl,$this->tbl_alias)->get("object",0);
		if(isset($d[0]->jumlah)) return $d[0]->jumlah;
		return 0;
	}
	public function countByUserId($b_user_id){
		$d = $this->db->select_as("COUNT(*)","total",1)->from($this->tbl)->where("b_user_id",$b_user_id)->get();
		if(isset($d[0]->total)) return $d[0]->total;
		return 0;
	}
	public function set($d){
		return $this->db->insert($this->tbl,$d);
	}
	public function update($id,$d){
		$this->db->where("id",$id);
		return $this->db->update($this->tbl,$d,0);
	}
	public function del($id){
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}
	public function getById($id){
		$this->db->where("id",$id);
		return $this->db->from($this->tbl)->get_first();
	}
	public function getFolder(){
		$this->db->select_as('DISTINCT folder','folder',0)->order_by('folder','asc')->limit(0,100);
		return $this->db->get();
	}
	public function getByFolder($folder='/',$limit_a=0,$limit_b=120){
		$this->db->where('folder',$folder)->order_by('nama','asc')->limit($limit_a,$limit_b);
		return $this->db->get();
	}
}
