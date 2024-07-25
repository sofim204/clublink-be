<?php
class C_Community_Emoji_Model extends SENE_Model{
	var $tbl = 'c_community_like_category';
	var $tbl_as = 'emoji_category';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	public function getTableAlias(){
		return $this->tbl_as;
	}

	public function getTableAlias2(){
		return $this->tbl2_as;
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
		$this->db->where("nation_code",$nation_code);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function getAll($nation_code, $page=0,$pagesize=10, $sortCol="sku", $sortDir="ASC", $keyword="", $utype_in=array(), $edate=""){
		$this->db->flushQuery();
		$this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'");
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY prioritas ASC)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.image_icon", "image_icon", 0);
		$this->db->select_as("$this->tbl_as.nama", "nama", 0);
		$this->db->select_as("$this->tbl_as.prioritas", "prioritas", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("nation_code", $nation_code, "AND", "=", 0, 0);
		if(is_array($utype_in)) if(count($utype_in)) $this->db->where_in('utype', $utype_in);
		if(mb_strlen($keyword)>1) {
			$this->db->where_as("nama", addslashes($keyword), "OR", "%like%", 1, 1); // by Muhammad Sofi 14 December 2021 14:38 | filter in search box emoji by nama only
		}
		$this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
		return $this->db->get("object",0);
	}

	public function countAll($nation_code, $keyword="",$utype_in=array(),$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code,"AND","=",0,0);
		if(is_array($utype_in)) if(count($utype_in)) $this->db->where_in('utype',$utype_in);
		if(mb_strlen($keyword)>1){
			$this->db->where("nama",$keyword,"OR","%like%",1,1); // by Muhammad Sofi 14 December 2021 14:38 | filter in search box emoji by nama only
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getById($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->get_first();
	}

	public function set($di){
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl,$di,0,0);
	}

	public function update($nation_code, $id, $du){
		if(!is_array($du)) return 0;
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
    return $this->db->update($this->tbl,$du,0);
	}

	public function del($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}

	public function getKategori($nation_code){
		$this->db->select("id")
			->select("utype")
			->select("nama")
			->select("is_active")
			->select("is_visible")
			->select("is_fashion")
			->select_as("COALESCE(parent_b_kategori_id,'-')",'parent_b_kategori_id',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("is_active",'1');
		$this->db->where("utype",'kategori',"OR","like",1,0);
		$this->db->where("utype",'kategori_sub',"OR","like",0,0);
		$this->db->where("utype",'kategori_sub_sub',"OR","like",0,1);
		$this->db->order_by("utype","asc");
		$this->db->limit(100);
		return $this->db->get('object',0);
	}

	public function getParentKategori($nation_code){
		$this->db->select();
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where_as("b_kategori_id", "is null");
		$this->db->where("utype",'kategori',"OR","like",1,0);
		$this->db->where("utype",'kategori_sub',"OR","like",0,0);
		$this->db->where("utype",'kategori_sub_sub',"OR","like",0,1);
		$this->db->order_by("prioritas","asc")->order_by("nama","asc");
		$this->db->limit(100);
		return $this->db->get('object',0);
	}

	public function getSubKategori($nation_code, $b_kategori_id){
		$this->db->select();
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where_as("parent_nation_code", $nation_code);
		$this->db->where_as("parent_b_kategori_id", $b_kategori_id);
		$this->db->where("utype",'kategori_sub',"AND","like",0,0);
		$this->db->limit(100);
		return $this->db->get('',0);
	}
}