<?php
class I_Group_Admin_Activity_Log_Model extends JI_Model{

	var $tbl = 'i_group_admin_activity_log';
	var $tbl_as = 'igaal';

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }

	public function checkId($nation_code, $id)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

	public function getAll($nation_code,$group_id,$page=0,$pagesize=10,$sortCol="id",$sortDir="desc",$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id", "id",0);
		$this->db->select_as("$this->tbl_as.title","title",0);
		$this->db->select_as("$this->tbl_as.text","text",0);
		$this->db->select_as("$this->tbl_as.type","type",0);
		$this->db->select_as("COALESCE($this->tbl_as.image,'')","image",0);
		// $this->db->select_as("COALESCE($this->tbl_as.extras,'{}')","extras",0);
		$this->db->select_as("$this->tbl_as.cdate","cdate",0);
		$this->db->from($this->tbl, $this->tbl_as);

		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
		$this->db->where_as("$this->tbl_as.i_group_id", $this->db->esc($group_id));
		$this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));

		if(strlen($keyword)>1){
			$this->db->where_as("$this->tbl_as.judul",$keyword, "OR", "%like%", 1, 0);
			$this->db->where_as("$this->tbl_as.text", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.type",$keyword,"OR","%like%", 0, 1);
		}
		$this->db->order_by($sortCol, $sortDir)->page($page, $pagesize);
		return $this->db->get("object",0);
	}
}
