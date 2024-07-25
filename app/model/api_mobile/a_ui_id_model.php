<?php
class A_Ui_Id_Model extends JI_Model{
  var $tbl = 'a_ui_id';
  var $tbl_as = 'aui';
  public function __construct(){
    parent::__construct();
    $this->db->from($this->tbl,$this->tbl_as);
  }
  public function getAlias(){
    return $this->tbl_as;
  }

  public function getLastId(){
      $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
      $this->db->from($this->tbl, $this->tbl_as);
      $d = $this->db->get_first('',0);
      if(isset($d->last_id)) return $d->last_id;
      return 0;
  }
  
  public function set($di)
  {
      return $this->db->insert($this->tbl, $di, 0, 0);
  }

  public function check($ui_id){
    $this->db->select('*');
    $this->db->from($this->tbl,$this->tbl_as);
    $this->db->where("ui_id",$ui_id);
    return $this->db->get_first('',0);
  }
  // public function countByCode($code){
  //   $this->db->select_as("COUNT(*)",'total',0);
  //   $this->db->where_as($this->__decrypt("$this->tbl_as.code"),strtolower($this->db->esc($code)));
  //   $this->db->where("is_active",1);
  //   $d = $this->db->get_first('',0);
  //   if(isset($d->total)) return $d->total;
  //   return 0;
  // }
}
