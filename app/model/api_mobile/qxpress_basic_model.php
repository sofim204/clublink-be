<?php
class QXpress_Basic_Model extends SENE_Model{
  var $tbl = 'qxpress_basic';
  var $tbl_as = 'qxbc';
  public function __construct(){
    parent::__construct();
    $this->db->from($this->tbl,$this->tbl_as);
  }
  public function get($nation_code){
    $this->db->where_as("nation_code",$nation_code);
    $this->db->order_by("weight","asc");
    return $this->db->get_first();
  }
  public function getByWeight($nation_code,$weight){
    $this->db->where_as("nation_code",$nation_code);
    $this->db->where_as($this->db->esc($weight),"`weight`",'AND','<=');
    $this->db->order_by("weight","asc");
    return $this->db->get_first('',0);
  }
}
