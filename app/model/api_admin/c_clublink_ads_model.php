<?php
class C_Clublink_Ads_Model extends JI_Model {
	var $tbl = 'c_sellon_ads';
	var $tbl_as = 'ceb';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
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
		// $this->db->select_as("ROW_NUMBER() OVER (ORDER BY priority)", "no");
		$this->db->select_as("$this->tbl_as.id", "no", 0);
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.priority", "priority", 0);
		$this->db->select_as("$this->tbl_as.img_thumbnail", "img_thumbnail", 0);
		$this->db->select_as("$this->tbl_as.start_date", "start_date", 0);
		$this->db->select_as("$this->tbl_as.end_date", "end_date", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->select_as("$this->tbl_as.type_ads", "type_ads", 0);
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
		$this->db->select_as("$this->tbl_as.teks", "teks", 0);
		$this->db->select_as("$this->tbl_as.img_thumbnail", "img_thumbnail", 0);
		$this->db->select_as("$this->tbl_as.product_id", "product_id", 0);
		$this->db->select_as("$this->tbl_as.seller_id", "seller_id", 0);
		$this->db->select_as("$this->tbl_as.community_id", "community_id", 0);
		$this->db->select_as("$this->tbl_as.type_ads", "type_ads", 0);
		$this->db->select_as("$this->tbl_as.start_date", "start_date", 0);
		$this->db->select_as("$this->tbl_as.end_date", "end_date", 0);
		$this->db->select_as("$this->tbl_as.priority", "priority", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->select_as("$this->tbl_as.url_webview", "url_webview", 0);
		$this->db->select_as("$this->tbl_as.judul", "judul", 0);
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

	public function getByIdData($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->get_first();
	}
}