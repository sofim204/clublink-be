<?php
class A_Exception_User_Force_Id_Model extends JI_Model{
  var $tbl = 'a_exception_user_force_id';
  var $tbl_as = 'aeufi';

  public function __construct(){
    parent::__construct();
    $this->db->from($this->tbl,$this->tbl_as);
  }

  // public function getAlias(){
  //   return $this->tbl_as;
  // }

  // public function getLastId(){
  //   $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
  //   $this->db->from($this->tbl, $this->tbl_as);
  //   $d = $this->db->get_first('',0);
  //   if(isset($d->last_id)) return $d->last_id;
  //   return 0;
  // }

  // public function set($di)
  // {
  //   return $this->db->insert($this->tbl, $di, 0, 0);
  // }

  public function check($id){
    $this->db->select('*');
    $this->db->from($this->tbl,$this->tbl_as);
    $this->db->where("b_user_id_sg", $id, "OR", "=", 1, 0);
    $this->db->where("b_user_id_id", $id, "AND", "=", 0, 1);
    return $this->db->get_first('',0);
  }
}
