<?php
class H_Redemptionexchange_Type_Model extends SENE_Model{
	var $tbl = 'h_point_redemption_exchange_list';
	var $tbl_as = 'hprel';
	// var $tbl2 = 'b_vendor_brand';
	// var $tbl2_as = 'bvb';
	// var $tbl3 = 'a_vendor';
	// var $tbl3_as = 'av';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getTableAlias(){
		return $this->tbl_as;
	}
	// public function getTableAlias2(){
	// 	return $this->tbl2_as;
	// }
	public function get($nation_code, $limit=100,$is_active=1){
		$this->db->where('nation_code',$nation_code,'=','AND',0,0);
		$this->db->where('is_active',$is_active);
		$this->db->limit($limit);
		return $this->db->get();
	}
	public function getById($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->get_first();
	}
	public function set($di){
		if(!is_array($di)) return 0;
		$this->db->insert($this->tbl,$di,0,0);
		return $this->db->last_id;
	}
	public function update($nation_code, $id,$du){
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
	public function getType(){
		$this->db->select_as("DISTINCT(type)","type",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->order_by("$this->tbl_as.type", "ASC");
		return $this->db->get();
	}
	// public function getKategori($nation_code, $limit=100){
	// 	$this->db->select("id")
	// 					 ->select("utype")
	// 					 ->select("nama")
	// 					 ->select("is_active")
	// 					 ->select("is_visible")
	// 					 ->select_as("COALESCE(parent_nation_code,'-')",'parent_nation_code',0)
	// 					 ->select_as("COALESCE(parent_b_kategori_id,'-')",'b_kategori_id',0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where("is_active",'1');
	// 	$this->db->where("utype",'kategori',"OR","like",1,0);
	// 	$this->db->where("utype",'kategori_sub',"OR","like",0,0);
	// 	$this->db->where("utype",'kategori_sub_sub',"OR","like",0,1);
	// 	$this->db->order_by("utype","asc");
	// 	$this->db->limit($limit);
	// 	return $this->db->get('object',0);
	// }
	// public function getParentKategori(){
	// 	$this->db->select()->from($this->tbl,$this->tbl_as)->where_as("b_kategori_id", "is null");
	// 	$this->db->where("utype",'kategori',"OR","like",1,0);
	// 	$this->db->where("utype",'kategori_sub',"OR","like",0,0);
	// 	$this->db->where("utype",'kategori_sub_sub',"OR","like",0,1);
	// 	$this->db->order_by("prioritas","asc")->order_by("nama","asc");
	// 	$this->db->limit(100);
	// 	return $this->db->get('object',0);
	// }
	// public function getSubKategori($b_kategori_id){
	// 	$this->db->select()->from($this->tbl,$this->tbl_as)->where_as("b_kategori_id", $b_kategori_id);
	// 	$this->db->where("utype",'kategori_sub',"AND","like",0,0);
	// 	$this->db->limit(100);
	// 	return $this->db->get('',0);
	// }
	// public function getFirstByName($nama){
	// 	$nama = strtolower(trim($nama));
	// 	$this->db->where_as('LOWER(TRIM(nama))',$this->db->esc($nama),'and','like')->order_by('id','desc');
	// 	return $this->db->get_first('object',0);
	// }
	// public function getActive($nation_code){
	// 	$this->db->where_as("nation_code",$nation_code);
	// 	$this->db->where("utype",'kategori',"AND","like",0,0); // by Muhammad Sofi - 17 November 2021 17:20 | filter data by kategori
	// 	return $this->db->get();
	// }
}
