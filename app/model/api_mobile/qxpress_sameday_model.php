<?php
class QXpress_Sameday_Model extends SENE_Model{
  var $tbl = 'qxpress_sameday';
  var $tbl_as = 'qxsd';
  public function __construct(){
    parent::__construct();
    $this->db->from($this->tbl,$this->tbl_as);
  }
  public function get($nation_code){
    $this->db->where_as("nation_code",$nation_code);
    $this->db->order_by("weight","asc");
    return $this->db->get_first();
  }
  public function getByDistance($nation_code,$weight,$distance){
    $this->db->where_as("nation_code",$nation_code);
    $this->db->where_as($weight,"weight_max",'AND','<=');
    $this->db->where_as($distance,"distance_max",'AND','<=');
    $this->db->order_by("distance_max","asc");
    $this->db->order_by("cost","asc");
    return $this->db->get_first('',0);
  }
}
