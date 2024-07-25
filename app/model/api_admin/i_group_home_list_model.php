<?php
class I_Group_Home_List_Model extends JI_Model {
	var $tbl = 'i_group_home_list';
	var $tbl_as = 'ighl';
	var $tbl2 = 'i_group_category';
	var $tbl2_as = 'igc';

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function getTableAlias(){
		return $this->tbl_as;
	}

	private function __joinTbl_Group_Home_List_With_Tbl_Group_Category()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.i_group_sub_category_id", "=", "$this->tbl2_as.id");
        return $cps;
    }

	public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="cdate", $sortDir="", $keyword="") {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY prioritas)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		// $this->db->select_as("$this->tbl_as.id", "no", 0);
		$this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.type", "type", 0);
        $this->db->select_as("$this->tbl_as.url", "url", 0);
        $this->db->select_as("$this->tbl_as.english", "english", 0);
        $this->db->select_as("$this->tbl_as.indonesia", "indonesia", 0);
		$this->db->select_as("$this->tbl_as.prioritas", "prioritas", 0);
		$this->db->select_as("$this->tbl_as.prioritas_indonesia", "prioritas_indonesia", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->select_as("$this->tbl_as.type", "type_parameter", 0);
        $this->db->select_as("$this->tbl2_as.nama", "sub_category_name", 0);
        $this->db->select_as("$this->tbl_as.i_group_sub_category_id", "i_group_sub_category_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl_Group_Home_List_With_Tbl_Group_Category(), "left");
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl_as.english", addslashes($keyword), "OR", "%like%", 1, 0);
			$this->db->where_as("$this->tbl_as.indonesia", addslashes($keyword), "OR", "%like%", 0, 1);
		}
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object", 0);
	}

	public function countAll($nation_code, $keyword="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl_as.english", addslashes($keyword), "OR", "%like%", 1, 0);
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
    
}