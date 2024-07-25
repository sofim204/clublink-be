<?php
class E_Chat_Read_Model extends SENE_Model {
  var $is_cacheable;
  var $tbl = 'e_chat_read';
  var $tbl_as = 'ecr';
  // var $tbl2 = 'e_chat';
  // var $tbl2_as = 'ec';
  // var $tbl3 = 'c_produk'; //from seller or buyer
  // var $tbl3_as = 'cp';
  // var $tbl4 = 'a_pengguna';
  // var $tbl4_as = 'ap';
  // var $tbl5 = 'b_user'; //buyer / seller
  // var $tbl5_as = 'bu';
  // var $tbl6 = 'd_order';
  // var $tbl6_as = 'dor';
  // var $tbl10 = 'b_user';
  // var $tbl10_as = 'bb';
  // var $tbl11 = 'b_user';
  // var $tbl11_as = 'bs';
  // var $tbl20 = 'e_chat';
  // var $tbl20_as = 'e';

  public function __construct(){
    parent::__construct();
    $this->is_cacheable = 0;
    $this->db->from($this->tbl,$this->tbl_as);
  }

  // private function __joinTbl2(){
  //   $cps = array();
  //   $cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl2_as.nation_code");
  //   $cps[] = $this->db->composite_create("$this->tbl_as.d_order_id","=","$this->tbl2_as.d_order_id");
  //   $cps[] = $this->db->composite_create("$this->tbl_as.c_produk_id","=","$this->tbl2_as.c_produk_id");
  //   $cps[] = $this->db->composite_create("$this->tbl_as.e_chat_id","=","$this->tbl2_as.id");
  //   return $cps;
  // }

  // public function trans_start(){
  //   $r = $this->db->autocommit(0);
  //   if($r) return $this->db->begin();
  //   return false;
  // }
  // public function trans_commit(){
  //   return $this->db->commit();
  // }
  // public function trans_rollback(){
  //   return $this->db->rollback();
  // }
  // public function trans_end(){
  //   return $this->db->autocommit(1);
  // }
  // public function getLastId($nation_code,$d_order_id,$c_produk_id){
  //   $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
  //   $this->db->from($this->tbl, $this->tbl_as);
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $d = $this->db->get_first('',0);
  //   if(isset($d->last_id)) return $d->last_id;
  //   return 0;
  // }

  public function set($di){
    return $this->db->insert($this->tbl,$di);
  }
  // public function setMass($ds){
  //   return $this->db->insert_multi($this->tbl,$ds,0);
  // }
  // public function update($nation_code,$b_user_id,$id,$du){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("b_user_id",$b_user_id);
  //   $this->db->where("id",$id);
  //   return $this->db->update($this->tbl,$du);
  // }
  // public function delete($nation_code,$d_order_id,$c_produk_id){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   return $this->db->delete($this->tbl);
  // }
  // public function deleteByUserId($nation_code,$d_order_id,$c_produk_id,$b_user_id){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where("b_user_id",$b_user_id);
  //   return $this->db->delete($this->tbl);
  // }

  // public function getByUserId($nation_code,$d_order_id,$c_produk_id,$b_user_id){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where("b_user_id",$b_user_id);
  //   return $this->db->get();
  // }
  // public function getByChatId($nation_code,$d_order_id,$c_produk_id,$e_chat_id){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where("b_user_id",$b_user_id);
  //   $this->db->where("e_chat_id",$e_chat_id);
  //   return $this->db->get();
  // }
  // public function getByChatIds($nation_code,$d_order_id,$c_produk_id,$e_chat_ids){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where_in("e_chat_id",$e_chat_ids);
  //   return $this->db->get();
  // }
  // public function getByUserIdChatIds($nation_code,$d_order_id,$c_produk_id,$b_user_id,$e_chat_ids){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where("b_user_id",$b_user_id);
  //   $this->db->where_in("e_chat_id",$e_chat_ids);
  //   return $this->db->get();
  // }
  // public function getIsReadByUserId($nation_code,$d_order_id,$c_produk_id){
  //   $this->db->select("is_read");
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->order_by("is_read","desc");
  //   $d = $this->db->get_first();
  //   if(isset($d->is_read)) return $d->is_read;
  //   return 1;
  // }
  // public function check($nation_code,$d_order_id,$c_produk_id,$chat_type,$b_user_id){
  //   $this->db->select_as("COUNT(*)","total");
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where("b_user_id",$b_user_id);
  //   $this->db->where("chat_type",$chat_type);
  //   $this->db->order_by("is_read","desc");
  //   $d = $this->db->get_first('',0);
  //   if(isset($d->total)) return $d->total;
  //   return 1;
  // }
  // public function setAsUnRead($nation_code,$d_order_id,$c_produk_id,$chat_type,$b_user_id){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where("b_user_id",$b_user_id);
  //   $this->db->where("chat_type",$chat_type);
  //   return $this->db->update($this->tbl,array("is_read"=>0));
  // }
  // public function setAsRead($nation_code,$d_order_id,$c_produk_id,$b_user_id){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where("b_user_id",$b_user_id);
  //   return $this->db->update($this->tbl,array("is_read"=>1));
  // }
  // public function countUnread($nation_code,$b_user_id){
  //   $this->db->select_as("COUNT(*)","total");
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("b_user_id",$b_user_id);
  //   $this->db->where("is_read",0);
  //   $d = $this->db->get_first('',0);
  //   if(isset($d->total)) return $d->total;
  //   return 0;
  // }

  // public function getByUserIdForChatRoom($nation_code,$b_user_id){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("b_user_id",$b_user_id);
  //   $this->db->where("is_read","0");
  //   return $this->db->get('',0);
  // }
  
}
