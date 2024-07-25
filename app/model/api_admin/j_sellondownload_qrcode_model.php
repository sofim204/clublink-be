<?php
class J_Sellondownload_Qrcode_Model extends JI_Model {
	var $tbl = 'j_sellon_download_qrcode';
	var $tbl_as = 'q';

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

    public function checkId($id)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

	public function getAll($page=0, $pagesize=10, $sortCol="sku", $sortDir="DESC", $keyword="", $from_date="", $to_date="") {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		// $this->db->select_as("$this->tbl_as.id", "no", 0);
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.place_name", "place_name", 0);
		$this->db->select_as("$this->tbl_as.url", "url", 0);
		$this->db->select_as("$this->tbl_as.plain_url", "plain_url", 0);
		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.admin_name", "admin_name", 0);
		$this->db->from($this->tbl, $this->tbl_as);

		if(mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.place_name", $keyword, "OR", "%like%", 1, 1);
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

		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getById($id) {
		$this->db->where("id", $id);
		return $this->db->get_first();
	}
}
