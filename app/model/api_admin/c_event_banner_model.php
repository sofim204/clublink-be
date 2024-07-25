<?php
class C_Event_Banner_Model extends SENE_Model{
	var $tbl = 'c_event_banner';
	var $tbl_as = 'ceb';
	var $tbl2 = 'c_produk';
	var $tbl2_as = 'produk_data';
	var $tbl3 = 'b_user';
	var $tbl3_as = 'master_user';
	var $tbl4 = 'c_community';
	var $tbl4_as = 'cc';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
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

	private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl2_as.b_user_id", "=", "$this->tbl3_as.id");
        return $cps;
    }

	public function getLastId($nation_code){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
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

	public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="kode", $sortDir="ASC", $keyword, $is_active=""){
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER ()", "no");
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.priority", "priority", 0);
		$this->db->select_as("$this->tbl_as.judul", "judul", 0);
		$this->db->select_as("$this->tbl_as.img_thumbnail", "img_thumbnail", 0);
		$this->db->select_as("$this->tbl_as.type_event_banner", "type_event_banner", 0);
		$this->db->select_as("$this->tbl_as.url_type", "url_type", 0);
		$this->db->select_as("$this->tbl_as.start_date", "start_date", 0);
		$this->db->select_as("$this->tbl_as.end_date", "end_date", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		if(strlen($is_active)) $this->db->where_as("$this->tbl_as.is_active",$this->db->esc($is_active));
		if(mb_strlen($keyword)>0){
			$this->db->where("judul",$keyword,"OR","%like%",1,1);
		}
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAll($nation_code, $keyword, $is_active=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code,"AND","=",0,0);
		if(strlen($is_active)) $this->db->where_as("$this->tbl_as.is_active",$this->db->esc($is_active));
		if(mb_strlen($keyword)>0){
			$this->db->where("judul",$keyword,"OR","%like%",1,1);
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	public function getById($nation_code, $id){
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.url", "url", 0);
		$this->db->select_as("$this->tbl_as.url_type", "url_type", 0);
		$this->db->select_as("$this->tbl_as.img_thumbnail", "img_thumbnail", 0);
		$this->db->select_as("$this->tbl_as.type_event_banner", "type_event_banner", 0);
		$this->db->select_as("$this->tbl_as.product_id", "product_id", 0);
		$this->db->select_as("$this->tbl_as.seller_id", "seller_id", 0);
		$this->db->select_as("$this->tbl_as.community_id", "community_id", 0);
		$this->db->select_as("$this->tbl_as.type_event_banner", "type_event_banner", 0);
		$this->db->select_as("$this->tbl_as.judul", "judul", 0); // by Muhammad Sofi 3 January 2022 18:23 | add title for event banner
		$this->db->select_as("$this->tbl_as.teks", "teks", 0); // by Muhammad Sofi 3 January 2022 17:18 | add description for event banner
		$this->db->select_as("$this->tbl_as.start_date", "start_date", 0);
		$this->db->select_as("$this->tbl_as.end_date", "end_date", 0);
		$this->db->select_as("$this->tbl_as.priority", "priority", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->get_first();
	}

	public function getByIdThumbnail($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->get_first();
	}

	public function getCustomer($nation_code, $keyword="", $is_active="") {
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl3_as.id", "user_id", 0);
		$this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "user_name", 0);
		$this->db->select_as($this->__decrypt("$this->tbl3_as.email"), "user_email", 0);
		$this->db->from($this->tbl3, $this->tbl3_as);
		// $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
		$this->db->where_as("$this->tbl3_as.nation_code", $nation_code, "AND", "=", 0, 0);

		if (mb_strlen($is_active)) {
			$this->db->where_as("$this->tbl3_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
		}

		if (mb_strlen($keyword)>0) {
			// by Muhammad Sofi 10 February 2022 15:31 | fix error when search user
			$this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl3_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 1);
		}

		return $this->db->get("object", 0);
	}

	public function getProductDetail($nation_code, $keyword="", $is_active="", $seller_id) {
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl2_as.id", "product_id", 0);
		$this->db->select_as("$this->tbl2_as.nama", "product_name", 0);
		$this->db->from($this->tbl2, $this->tbl2_as);
		$this->db->where_as("$this->tbl2_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl2_as.b_user_id", $seller_id, "AND", "=", 0, 0);

		if (mb_strlen($is_active)) {
			$this->db->where_as("$this->tbl2_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
		}

		if (mb_strlen($keyword)>0) {
			// by Muhammad Sofi 10 February 2022 15:31 | fix error when search user
			$this->db->where_as("$this->tbl2_as.nama", addslashes($keyword), "OR", "%like%", 1, 1);
		}

		return $this->db->get("object", 0);
	}

	public function getCommunity($nation_code, $keyword="", $is_active="") {
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl4_as.id", "community_id", 0);
		$this->db->select_as("$this->tbl4_as.title", "title", 0);
		$this->db->from($this->tbl4, $this->tbl4_as);
		$this->db->where_as("$this->tbl4_as.nation_code", $nation_code, "AND", "=", 0, 0);

		if (mb_strlen($is_active)) {
			$this->db->where_as("$this->tbl4_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
		}

		if (mb_strlen($keyword)>0) {
			// by Muhammad Sofi 10 February 2022 15:31 | fix error when search user
			$this->db->where_as("$this->tbl4_as.title", addslashes($keyword), "OR", "%like%", 1, 1);
		}

		return $this->db->get("object", 0);
	}

	public function getByIdData($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->get_first();
	}
}