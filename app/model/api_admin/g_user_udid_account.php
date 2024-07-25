<?php
class G_User_Udid_Account extends JI_Model {
	var $tbl = 'b_user';
	var $tbl_as = 'bu';

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function getAll($page=0, $pagesize=10, $sortCol="sku", $sortDir="DESC", $keyword="", $type="") {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY device_id DESC)", "no");
		$this->db->select_as("$this->tbl_as.device_id", "udid", 0);
		$this->db->select_as("COUNT($this->tbl_as.device_id)", "total", 0);

		$this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("CHAR_LENGTH($this->tbl_as.device_id)", 0, "AND", "=");
		
		if(strlen($type)>0) {
			$this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
		}

		if(mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.device_id", $keyword, "OR", "%like%", 1, 1);
		}

		$this->db->group_by("$this->tbl_as.device_id");
		$this->db->order_by("$this->tbl_as.device_id", $sortDir)->limit($page, $pagesize);

		return $this->db->get("object", 0);
	}

	public function countAll($keyword="", $type="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT($this->tbl_as.device_id)", "jumlah", 0);
		$this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("CHAR_LENGTH($this->tbl_as.device_id)", 0, "AND", "=");
		
		if(strlen($type)>0) {
			$this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
		}
		
		if(mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.device_id", $keyword, "OR", "%like%", 1, 1);
		}

		$this->db->group_by("$this->tbl_as.device_id");
		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getById($device_id){
		$this->db->where("device_id", $device_id);
		return $this->db->get_first();
	}

	public function getDetailAll($page=0, $pagesize=10, $sortCol="sku", $sortDir="ASC", $keyword="", $device_id="") {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate desc)", "no");
		$this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
		$this->db->select_as("$this->tbl_as.cdate", "create_date", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		
		$this->db->where_as("$this->tbl_as.device_id", $this->db->esc($device_id), "AND", "=", 0, 0);

		if(mb_strlen($keyword)>0) {
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.email").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 1);
		}

		$this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);

		return $this->db->get("object", 0);
	}

	public function countDetailAll($keyword="", $device_id="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		
		$this->db->where_as("$this->tbl_as.device_id", $this->db->esc($device_id), "AND", "=", 0, 0);
		
		if(mb_strlen($keyword)>0) {
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.email").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 1);
		}
		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
}