<?php
class I_Group_Default_Image_Model extends SENE_Model {
	var $tbl = 'i_group_default_image';
	var $tbl_as = 'igdi';

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function getTableAlias(){
		return $this->tbl_as;
	}

	public function trans_start(){
		$r = $this->db->autocommit(0);
		if($r) return $this->db->begin();
		return false;
	}

	public function trans_commit(){
		return $this->db->commit();
	}

	public function trans_rollback(){
		return $this->db->rollback();
	}

	public function trans_end(){
		return $this->db->autocommit(1);
	}

	public function getLastId($nation_code){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		// $this->db->where("nation_code",$nation_code);
		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
		$d = $this->db->get_first('', 0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}
	
	// by Muhammad Sofi 28 December 2021 10:10 | improvement query
	// public function getAll($nation_code, $page=0,$pagesize=10,$sortCol="sku",$sortDir="ASC",$keyword="",$utype_in=array(),$edate=""){
	// 	$this->db->flushQuery();
	// 	$this->db->select('id');
	// 	$this->db->select('image_icon');
	// 	$this->db->select('nama');
	// 	$this->db->select('is_fashion');
	// 	$this->db->select('is_active');
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where("nation_code",$nation_code,"AND","=",0,0);
	// 	$this->db->where('parent_b_kategori_id', "IS NULL"); // by Muhammad Sofi 23 December 2021 10:00 | don't show category automotive in category product
	// 	$this->db->where("$this->tbl_as.utype", "brand", "AND", "!=", 1, 1); 
	// 	if(is_array($utype_in)) if(count($utype_in)) $this->db->where_in('utype',$utype_in);
	// 	if(mb_strlen($keyword)>1){
	// 		$this->db->where("nama",$keyword,"OR","%like%",1,0);
	// 		$this->db->where("deskripsi",$keyword,"OR","%like%",0,1);
	// 	}
	// 	$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
	// 	return $this->db->get("object",0);
	// }

	public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="cdate", $sortDir="", $keyword="", $utype_in=array()) {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.image", "image", 0);       
		$this->db->select_as("$this->tbl_as.prioritas", "prioritas", 0);
		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl_as.prioritas", addslashes($keyword), "OR", "%like%", 0, 0);
		}
		// $this->db->order_by("$this->tbl_as.prioritas", "ASC")->limit($page,$pagesize);
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object", 0);
	}

	public function countAll($nation_code, $keyword="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl_as.prioritas", addslashes($keyword), "OR", "%like%", 0, 0);
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getById($nation_code, $id) {
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
		return $this->db->get_first();
	}

	public function set($di) {
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl, $di, 0, 0);
	}

	public function update($nation_code, $id, $du) {
		if(!is_array($du)) return 0;
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
    return $this->db->update($this->tbl, $du, 0);
	}

	public function del($nation_code, $id){
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
		return $this->db->delete($this->tbl);
	}
    
}
