<?php
class B_Kategori_Model2 extends SENE_Model {
	var $tbl = 'b_kategori';
	var $tbl_as = 'bk';

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

	public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="sku", $sortDir="", $keyword="", $utype_in=array()) {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY prioritas)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.image_icon", "image_icon", 0);
		$this->db->select_as("$this->tbl_as.nama", "nama", 0);
		$this->db->select_as("$this->tbl_as.indonesia", "indonesia", 0);
		$this->db->select_as("$this->tbl_as.prioritas", "prioritas", 0); // by Muhammad Sofi 11 January 2022 9:47 | add & edit input priority, show priority in datatable
		$this->db->select_as("$this->tbl_as.prioritas_indonesia", "prioritas_indonesia", 0); // by Muhammad Sofi 11 January 2022 9:47 | add & edit input priority, show priority in datatable
		$this->db->select_as("$this->tbl_as.is_fashion", "is_fashion", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		// $this->db->where_as("$this->tbl_as.utype", $this->db->esc("kategori"), "OR", "=", 1, 0);
		// $this->db->where_as("$this->tbl_as.parent_b_kategori_id", "IS NULL", "AND", "IS NULL", 0, 1); // by Muhammad Sofi 23 December 2021 10:00 | don't show category automotive in category product
		$this->db->where_as("$this->tbl_as.utype", $this->db->esc("kategori")); // by Muhammad Sofi 11 January 2022 16:15 | change filter by kategori
		if(is_array($utype_in)) if(count($utype_in)) $this->db->where_in('utype', $utype_in);
		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 1, 0);
			$this->db->where_as("$this->tbl_as.indonesia", addslashes($keyword), "OR", "%like%", 0, 1);
		}
		// $this->db->order_by("$this->tbl_as.prioritas", "ASC")->limit($page,$pagesize);
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object", 0);
	}

	public function countAll($nation_code, $keyword="",$utype_in=array()) {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
		// $this->db->where_as("$this->tbl_as.utype", $this->db->esc("kategori"), "OR", "=", 1, 0);
		// $this->db->where_as("$this->tbl_as.parent_b_kategori_id", "IS NULL", "AND", "IS NULL", 0, 1); // by Muhammad Sofi 23 December 2021 10:00 | don't show category automotive in category product
		$this->db->where_as("$this->tbl_as.utype", $this->db->esc("kategori")); // by Muhammad Sofi 11 January 2022 16:15 | change filter by kategori
		if(is_array($utype_in)) if(count($utype_in)) $this->db->where_in('utype', $utype_in);
		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 1, 0);
			$this->db->where_as("$this->tbl_as.indonesia", addslashes($keyword), "OR", "%like%", 0, 1);
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

	// Add by Yopie Hidayat 14 Agustus 2023 16:36
	public function checkCategoryByNameID($nation_code = '', $category_name = '')
    {
		$this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->where("nation_code", $nation_code);
        $this->db->where_as("indonesia", $this->db->esc($category_name));
        return $this->db->get_first('object', 0);
    }
}
