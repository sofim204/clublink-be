<?php
class G_Install_Trace_Model extends JI_Model {
	var $tbl = 'g_mobile_registration_activity';
	var $tbl_as = 'gmra';

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function update($id, $du){
		if(!is_array($du)) return 0;
		$this->db->where("id", $id);
    	return $this->db->update($this->tbl, $du, 0);
	}

	// START by Muhammad Sofi 27 January 2022 16:42 | adding form add data
	public function set($di) {
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl, $di, 0, 0);
	}

	public function getLastId() {
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}
	// END by Muhammad Sofi 27 January 2022 16:42 | adding form add data

	public function del($id) {
		$this->db->where("id", $id);
		return $this->db->delete($this->tbl);
	}

	public function getAll($page=0, $pagesize=10, $sortCol="sku", $sortDir="ASC", $keyword="", $type="") {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY is_clicked desc)", "no");
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.referral", "referral", 0);
		$this->db->select_as("COUNT($this->tbl_as.referral)", "is_clicked", 0);
		$this->db->select_as("SUM(IF($this->tbl_as.is_downloaded = 1, 1, 0))", "total_downloaded", 0);
		$this->db->select_as("SUM(IF($this->tbl_as.is_registered = 1, 1, 0))", "total_registered", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		
		if(strlen($type)>0) {
			$this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
		}

		if(mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.ui_id", $keyword, "OR", "%like%", 1, 0);
			$this->db->where("$this->tbl_as.nama", $keyword, "OR", "%like%", 0, 1);
		}

		// $this->db->group_by("CONCAT($this->tbl_as.referral, '-',$this->tbl2_as.mobile_type)");
		$this->db->group_by("$this->tbl_as.referral");
		$this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);

		return $this->db->get("object", 0);
	}

	public function countAll($keyword="", $type="") {
		$this->db->flushQuery();
		// $this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->select_as("COUNT(DISTINCT($this->tbl_as.referral))", "jumlah", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		
		if(strlen($type)>0) {
			$this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
		}
		
		if(mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.ui_id", $keyword, "OR", "%like%", 1, 0);
			$this->db->where("$this->tbl_as.nama", $keyword, "OR", "%like%", 0, 1);
		}
		// $this->db->group_by("CONCAT($this->tbl_as.referral, '-',$this->tbl2_as.mobile_type)");
		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getById($referral){
		$this->db->where("referral", $referral);
		return $this->db->get_first();
	}

	public function getDetailAll($page=0, $pagesize=10, $sortCol="sku", $sortDir="ASC", $keyword="", $referral="") {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate desc)", "no");
		// $this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.is_downloaded", "is_downloaded", 0);
		$this->db->select_as("COALESCE($this->tbl_as.cdate_downloaded, '-')", "cdate_downloaded", 0);
		$this->db->select_as("$this->tbl_as.is_registered", "is_registered", 0);
		$this->db->select_as("COALESCE($this->tbl_as.cdate_registered, '-')", "cdate_registered", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		
		$this->db->where_as("$this->tbl_as.referral", $this->db->esc($referral), "AND", "=", 0, 0);

		if(mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.ui_id", $keyword, "OR", "%like%", 1, 0);
			$this->db->where("$this->tbl_as.nama", $keyword, "OR", "%like%", 0, 1);
		}

		$this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);

		return $this->db->get("object", 0);
	}

	public function countDetailAll($keyword="", $referral="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		
		// if(strlen($type)>0) {
		// 	$this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
		// }
		$this->db->where_as("$this->tbl_as.referral", $this->db->esc($referral), "AND", "=", 0, 0);
		
		if(mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.ui_id", $keyword, "OR", "%like%", 1, 0);
			$this->db->where("$this->tbl_as.nama", $keyword, "OR", "%like%", 0, 1);
		}
		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
}
