<?php
//api_cron
class Common_Code_Model extends JI_Model{
	var $tbl = 'common_code';
	var $tbl_as = 'cc';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
  public function set($di){
    return $this->db->insert($this->tbl,$di);
  }
  public function update($nation_code, $id,$du){
    $this->db->where("nation_code",$nation_code);
    $this->db->where("id",$id);
    return $this->db->update($this->tbl,$du);
  }
  public function getByClassified($nation_code, $classified){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("classified",$classified);
		$this->db->where("use_yn","y");
		return $this->db->get();
  }
  public function check($nation_code, $classified, $code){
    $this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_as);
    $this->db->where("nation_code",$nation_code);
    $this->db->where("classified",$classified);
    $this->db->where("code",$code);
    $d = $this->db->get_first();
    if(isset($d->jumlah)) return $d->jumlah;
    return 0;
  }

	public function getNotificationSetting($nation_code){
		$this->db->select_as("$this->tbl_as.*, ('')",'user_value',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("classified","setting_notification_",'and','like%');
		$this->db->where("use_yn","y");
		$this->db->order_by("id",'asc');
		return $this->db->get();
	}
	public function getByClassifiedAndCode($nation_code,$classified,$code){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("classified",$classified);
		$this->db->where("code",$code);
		$this->db->where("use_yn","y");
		$this->db->order_by("id",'asc');
		return $this->db->get_first();
	}
	public function getByClassifiedByCodeName($nation_code,$classified,$codename){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("classified",$classified);
		$this->db->where("codename",$codename);
		$this->db->where("use_yn","y");
		$this->db->order_by("id",'asc');
		return $this->db->get_first();
	}
}
