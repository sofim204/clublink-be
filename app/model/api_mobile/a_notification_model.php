<?php
class A_Notification_Model extends SENE_Model{
  var $tbl = 'a_notification';
  var $tbl_as = 'an';
  var $tbl2 = 'common_code';
  var $tbl2_as = 'cc';

  public function __construct(){
    parent::__construct();
    $this->db->from($this->tbl,$this->tbl_as);
  }
  public function get($nation_code,$method,$type,$id, $language_id=2){
    $this->db->from($this->tbl,$this->tbl_as);
    $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
    $this->db->where_as("$this->tbl_as.method",$this->db->esc($method));
    $this->db->where_as("$this->tbl_as.type",$this->db->esc($type));
    $this->db->where_as("$this->tbl_as.id",$this->db->esc($id));
    $this->db->where_as("$this->tbl_as.language_id",$this->db->esc($language_id));
    $this->db->where_as("$this->tbl_as.is_active",$this->db->esc("1"));
    return $this->db->get_first();
  }
}
