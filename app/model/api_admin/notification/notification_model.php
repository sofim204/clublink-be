<?php
class Notification_Model extends SENE_Model{
  var $tbl_notification = 'a_notification';

  public function __construct(){
    parent::__construct();
    $this->db->from($this->tbl_notification,$this->tbl_notification);
  }
  public function get($nation_code,$method,$type,$id){
    $this->db->from($this->tbl_notification,$this->tbl_notification);
    $this->db->where_as("$this->tbl_notification.nation_code",$this->db->esc($nation_code));
    $this->db->where_as("$this->tbl_notification.method",$this->db->esc($method));
    $this->db->where_as("$this->tbl_notification.type",$this->db->esc($type));
    $this->db->where_as("$this->tbl_notification.id",$this->db->esc($id));
    $this->db->where_as("$this->tbl_notification.is_active",$this->db->esc("1"));
    return $this->db->get_first();
  }
}
