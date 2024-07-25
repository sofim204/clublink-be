<?php
class A_ApiKey_Model extends JI_Model{
  var $tbl = 'a_apikey';
  var $tbl_as = 'aak';
  public function __construct(){
    parent::__construct();
    $this->db->from($this->tbl,$this->tbl_as);
  }
  public function getAlias(){
    return $this->tbl_as;
  }
  public function get(){
    $this->db->select('nation_code')
             ->select('id')
             ->select_as($this->__decrypt("$this->tbl_as.str"), "str", 0)
             ->select('is_active');
    $this->db->from($this->tbl,$this->tbl_as);
    $this->db->where("is_active",1);
    return $this->db->get('',0);
  }
  public function getByCode($code){
    $this->db->select('nation_code')
             ->select('id')
             ->select_as($this->__decrypt("$this->tbl_as.str"), "str", 0)
             ->select('is_active');
    $this->db->where_as($this->__decrypt("$this->tbl_as.code"),strtolower($this->db->esc($code)));
    $this->db->where("is_active",1);
    return $this->db->get_first();
  }
  public function countByCode($code){
    $this->db->select_as("COUNT(*)",'total',0);
    $this->db->where_as($this->__decrypt("$this->tbl_as.code"),strtolower($this->db->esc($code)));
    $this->db->where("is_active",1);
    $d = $this->db->get_first('',0);
    if(isset($d->total)) return $d->total;
    return 0;
  }
  public function auth($nation_code,$username,$password){
    $this->db->select_as($this->__decrypt("$this->tbl_as.str"),'str',0);
    $this->db->from($this->tbl,$this->tbl_as);
    $this->db->where("nation_code",$nation_code);
    $this->db->where_as($this->__decrypt("$this->tbl_as.username"),$this->db->esc($username));
    $this->db->where_as($this->__decrypt("$this->tbl_as.password"),$this->db->esc($password));
    return $this->db->get_first('',0);
  }
}
