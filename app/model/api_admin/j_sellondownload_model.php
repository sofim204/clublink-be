<?php
class J_Sellondownload_Model extends JI_Model {
	var $tbl = 'j_sellon_download_total';
	var $tbl_as = 'jsdt';

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function update($id, $du){
		if(!is_array($du)) return 0;
		$this->db->where("id", $id);
		return $this->db->update($this->tbl, $du, 0);
	}

	public function set($di) {
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl, $di, 0, 0);
	}

	public function del($id) {
		$this->db->where("id", $id);
		return $this->db->delete($this->tbl);
	}

	public function getAll($page=0, $pagesize=10, $sortCol="sku", $sortDir="DESC", $keyword="", $from_date="", $to_date="") {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		// $this->db->select_as("$this->tbl_as.id", "no", 0);
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.place_name", "place_name", 0);
		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.total_link_clicked", "total_link_clicked", 0);
		$this->db->select_as("$this->tbl_as.total_open_playstore", "total_open_playstore", 0);
		$this->db->select_as("$this->tbl_as.total_open_appstore", "total_open_appstore", 0);
		$this->db->from($this->tbl, $this->tbl_as);

		if(mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.place_name", $keyword, "OR", "%like%", 1, 1);
		}

		if (strlen($from_date)==10 && strlen($to_date)==10) {
            $this->db->between("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "DATE('$to_date')");
        } elseif (strlen($from_date)==10 && strlen($to_date)!=10) {
            $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "AND", ">=");
        } elseif (strlen($from_date)!=10 && strlen($to_date)==10) {
            $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$to_date')", "AND", "<=");
        }

		$this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
		return $this->db->get("object", 0);
	}

	public function countAll($keyword="", $from_date="", $to_date="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		
		if(mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.place_name", $keyword, "OR", "%like%", 1, 1);
		}

		if (strlen($from_date)==10 && strlen($to_date)==10) {
            $this->db->between("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "DATE('$to_date')");
        } elseif (strlen($from_date)==10 && strlen($to_date)!=10) {
            $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "AND", ">=");
        } elseif (strlen($from_date)!=10 && strlen($to_date)==10) {
            $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$to_date')", "AND", "<=");
        }

		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getById($id) {
		$this->db->where("id", $id);
		return $this->db->get_first();
	}
}
