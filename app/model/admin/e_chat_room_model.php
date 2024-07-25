<?php
class E_Chat_Room_Model extends SENE_Model {
  var $is_cacheable;
  var $tbl = 'e_chat_room_v2';
  var $tbl_as = 'ecp';
  var $tbl2 = 'e_chat_v2';
  var $tbl2_as = 'ec';
  var $tbl3 = 'c_produk'; //from seller or buyer
  var $tbl3_as = 'cp';
  var $tbl4 = 'a_pengguna';
  var $tbl4_as = 'ap';
  var $tbl5 = 'b_user'; //buyer / seller
  var $tbl5_as = 'bu';
  var $tbl6 = 'd_order';
  var $tbl6_as = 'dor';
  var $tbl10 = 'b_user';
  var $tbl10_as = 'bb';
  var $tbl11 = 'b_user';
  var $tbl11_as = 'bs';
  var $tbl20 = 'e_chat_v2';
  var $tbl20_as = 'e';

  public function __construct(){
    parent::__construct();
    $this->is_cacheable = 0;
    $this->db->from($this->tbl,$this->tbl_as);
  }

  private function __joinTbl2(){
    $cps = array();
    $cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl2_as.nation_code");
    $cps[] = $this->db->composite_create("$this->tbl_as.id","=","$this->tbl2_as.e_chat_room_id");
    return $cps;
  }
  private function __joinTbl10(){
    $cps = array();
    $cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl10_as.nation_code");
    $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id_1","=","$this->tbl10_as.id");
    return $cps;
  }
  private function __joinTbl11(){
    $cps = array();
    $cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl11_as.nation_code");
    $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id_2","=","$this->tbl11_as.id");
    return $cps;
  }

  public function trans_start(){
    $r = $this->db->autocommit(0);
    if($r) return $this->db->begin();
    return false;
  }
  public function trans_commit(){
    return $this->db->commit();
  }
  public function trans_rollback(){
    return $this->db->rollback();
  }
  public function trans_end(){
    return $this->db->autocommit(1);
  }

  public function getByRoomId($nation_code,$chat_room_id,$chat_type){

    $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
    $this->db->select_as("$this->tbl_as.id", "id", 0);
    $this->db->select_as("$this->tbl_as.b_user_id_1", "b_user_id_1", 0);
    $this->db->select_as("$this->tbl_as.b_user_id_2", "b_user_id_2", 0);
    $this->db->select_as("$this->tbl_as.chat_type", "chat_type", 0);
    $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl10_as.fnama").", 'Admin')", "b_user_fnama_1", 0);
    $this->db->select_as($this->__decrypt("$this->tbl11_as.fnama"), "b_user_fnama_2", 0);
    $this->db->from($this->tbl,$this->tbl_as);
    $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), "left");
    $this->db->join_composite($this->tbl11, $this->tbl11_as, $this->__joinTbl11(), "left");
    $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
    $this->db->where_as("$this->tbl_as.id",$this->db->esc($chat_room_id));
    $this->db->where_as("$this->tbl_as.chat_type",$this->db->esc($chat_type));
    return $this->db->get_first();
  }

  public function update($nation_code,$chat_room_id,$chat_type,$du){
    $this->db->where("nation_code",$nation_code);
    $this->db->where("id",$chat_room_id);
    $this->db->where("chat_type",$chat_type);
    return $this->db->update($this->tbl,$du);
  }

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

  public function checkRoomChat($nation_code, $b_user_id_1, $b_user_id_2, $chat_type){
    $this->db->select("*");

    $this->db->from($this->tbl,$this->tbl_as);

    $this->db->where("$this->tbl_as.nation_code",$nation_code);

    $this->db->where_as("$this->tbl_as.b_user_id_1", $this->db->esc($b_user_id_1), 'and', '=', 1, 0);
    $this->db->where_as("$this->tbl_as.b_user_id_2", $this->db->esc($b_user_id_2), 'or', '=', 0, 1);

    $this->db->where_as("$this->tbl_as.b_user_id_1", $this->db->esc($b_user_id_2), 'and', '=', 1, 0);
    $this->db->where_as("$this->tbl_as.b_user_id_2", $this->db->esc($b_user_id_1), 'and', '=', 0, 1);

    $this->db->where_as("$this->tbl_as.chat_type",$this->db->esc($chat_type));

    return $this->db->get_first();
    
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
  // public function check($nation_code,$d_order_id,$c_produk_id,$b_user_id){
  //   $this->db->select_as("COUNT(*)","total");
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where("b_user_id",$b_user_id);
  //   $this->db->order_by("is_read","desc");
  //   $d = $this->db->get_first('',0);
  //   if(isset($d->total)) return $d->total;
  //   return 1;
  // }
  // public function setAsUnRead($nation_code,$d_order_id,$c_produk_id,$b_user_id){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where("b_user_id",$b_user_id);
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
