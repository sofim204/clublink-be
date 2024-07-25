<?php
class G_Semelog_Model extends JI_Model {
	var $tbl = 'g_seme_log';
	var $tbl_as = 'gsl';

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

	public function getLastId() {
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function del($id) {
		$this->db->where("id", $id);
		return $this->db->delete($this->tbl);
	}

	public function getAll($page=0, $pagesize=10, $sortCol="sku", $sortDir="ASC", $keyword="", $from_date="", $to_date="", $path="") {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate desc)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.path", "path", 0);
		$this->db->select_as("COALESCE($this->tbl_as.log_text, '-')", "log_text", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		
		if (strlen($from_date)==10 && strlen($to_date)==10) {
			$this->db->between("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "DATE('$to_date')");
		} else if (strlen($from_date)==10 && strlen($to_date)!=10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$from_date')", 'AND', '>=');
		} else if (strlen($from_date)!=10 && strlen($to_date)==10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$to_date')", 'AND', '<=');
		}

		if(strlen($path)>0) {
			$this->db->where_as("$this->tbl_as.path", $this->db->esc($path));
		}

		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl_as.path", addslashes($keyword), "OR", "%like%", 1, 0);
			$this->db->where_as("$this->tbl_as.log_text", addslashes($keyword), "OR", "%like%", 0, 1);
		}

		// $this->db->order_by("$this->tbl_as.cdate", "DESC");

		$this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
		return $this->db->get("object", 0);
	}

	public function countAll($keyword="", $from_date="", $to_date="", $path="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl, $this->tbl_as);

		if (strlen($from_date)==10 && strlen($to_date)==10) {
			$this->db->between("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "DATE('$to_date')");
		} else if (strlen($from_date)==10 && strlen($to_date)!=10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$from_date')", 'AND', '>=');
		} else if (strlen($from_date)!=10 && strlen($to_date)==10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$to_date')", 'AND', '<=');
		}
		
		if(strlen($path)>0) {
			$this->db->where_as("$this->tbl_as.path", $this->db->esc($path));
		}

		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl_as.path", addslashes($keyword), "OR", "%like%", 1, 0);
			$this->db->where_as("$this->tbl_as.log_text", addslashes($keyword), "OR", "%like%", 0, 1);
		}
		
		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getById($id){
		$this->db->where("id", $id);
		return $this->db->get_first();
	}

	public function m_delete_log($from_date="", $to_date="") {
		$sdate = 'DATE("'.$from_date.'")';
		$edate = 'DATE("'.$to_date.'")';
		$this->db->between("DATE($this->tbl.cdate)", $sdate, $edate);

		return $this->db->delete($this->tbl);
	}
}
