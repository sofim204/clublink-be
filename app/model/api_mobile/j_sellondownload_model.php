<?php
class J_Sellondownload_Model extends JI_Model
{
    public $tbl = 'j_sellon_download_total';
    public $tbl_as = 'jsdt';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function set($di) {
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl, $di, 0, 0);
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

    public function checkData($datenow, $place_name)
    {
        $this->db->select_as("COUNT(*)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.cdate", $this->db->esc($datenow));
        $this->db->where_as("$this->tbl_as.place_name", $this->db->esc($place_name));

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function updateTotalData($datenow, $field_count, $place_name, $operator, $total)
    {
        $place_name_new = $this->db->esc($place_name);

        $sql = "UPDATE $this->tbl SET $field_count = $field_count $operator $total WHERE 
        cdate = '$datenow' AND place_name = $place_name_new";

        return $this->db->exec($sql);
    }

}
