<?php
class QXpress_Volume_Model extends SENE_Model{
  var $tbl = 'qxpress_volume';
  var $tbl_as = 'qxve';
  public function __construct(){
    parent::__construct();
    $this->db->from($this->tbl,$this->tbl_as);
  }
  public function get($nation_code){
    $this->db->where_as("nation_code",$nation_code);
    $this->db->order_by("length_max","asc");
    return $this->db->get_first();
  }
  public function getByDimension($nation_code,$dimension){
    $this->db->where_as("nation_code",$nation_code);
    $this->db->where_as($dimension,"length_max",'AND','<=');
    $this->db->order_by("length_max","asc");
    return $this->db->get_first('',0);
  }
}
