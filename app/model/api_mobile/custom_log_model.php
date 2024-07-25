<?php
class Custom_Log_Model extends JI_Model{
	var $tbl = 'g_seme_log';
	var $tbl_as = 'gsl';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

  public function getLastId($nation_code, $path)
  {
    $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->where("nation_code", $nation_code);
    $this->db->where_as("DATE(cdate)", $this->db->esc(date("Y-m-d")));
    $this->db->where("path", $path);
    $d = $this->db->get_first('', 0);
    if (isset($d->last_id)) {
        return $d->last_id;
    }
    return 0;
  }

  public function set($di){
    return $this->db->insert($this->tbl,$di);
  }

  // public function update($nation_code, $id,$du){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("id",$id);
  //   return $this->db->update($this->tbl,$du);
  // }

  public function del($nation_code, $date)
  {
    $this->db->where("nation_code", $nation_code);
    $this->db->where_as("DATE(cdate)", $this->db->esc($date), "AND", "<");
    return $this->db->delete($this->tbl);
  }

  // public function getByClassified($nation_code, $classified){
		// $this->db->from($this->tbl,$this->tbl_as);
		// $this->db->where("nation_code",$nation_code);
		// $this->db->where("classified",$classified);
		// $this->db->where("use_yn","y");
		// return $this->db->get();
  // }
  	
}
