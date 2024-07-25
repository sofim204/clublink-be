<?php
class I_Chat_Read_Model extends JI_Model {
  var $tbl = 'i_chat_read';
  var $tbl_as = 'icr';
  // var $tbl2 = 'e_chat_participant';
  // var $tbl2_as = 'ecp2';

  public function __construct(){
    parent::__construct();
    $this->db->from($this->tbl,$this->tbl_as);
  }

  // private function __joinTbl2(){
  //   $cps = array();
  //   $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code","=","$this->tbl7_as.nation_code");
  //   $cps[] = $this->db->composite_create("$this->tbl2_as.e_chat_room_id","=","$this->tbl7_as.id ");
  //   return $cps;
  // }

  public function set($di){
    return $this->db->insert($this->tbl,$di);
  }

  public function setMass($ds){
    return $this->db->insert_multi($this->tbl,$ds,0);
  }

  public function update($nation_code, $i_chat_room_id, $b_user_id, $du){
    $this->db->where("nation_code",$nation_code);
    $this->db->where("i_chat_room_id",$i_chat_room_id);
    if($b_user_id != '0'){
      $this->db->where("b_user_id",$b_user_id);
    }
    return $this->db->update($this->tbl,$du);
  }

  // public function updateUnread($nation_code,$e_chat_room_id ,$b_user_id ,$du){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("e_chat_room_id",$e_chat_room_id);
  //   $this->db->where("b_user_id",$b_user_id, 'AND', '!=');
  //   $this->db->where("is_active",1);
  //   return $this->db->update($this->tbl,$du);
  // }

  // public function softDelete($nation_code,$d_order_id,$c_produk_id,$b_user_id, $chat_type, $du){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where("chat_type",$chat_type);
  //   $this->db->where("b_user_id",$b_user_id);
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

  // public function getParticipantByRoomChatId($nation_code,$room_chat_id){
  //   $this->db->select_as("$this->tbl_as.*,$this->tbl5_as.id", "b_user_id", 0);
  //   $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "b_user_fnama", 0);
  //   $this->db->select_as("COALESCE($this->tbl5_as.image,'media/user/default-profile-picture.png')", "b_user_image", 0);
  //   $this->db->from($this->tbl, $this->tbl_as);
  //   $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");  
  //   $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
  //   $this->db->where_as("$this->tbl_as.e_chat_room_id",$this->db->esc($room_chat_id));
  //   $this->db->where_as("$this->tbl_as.is_active",$this->db->esc(1));
  //   return $this->db->get();
  // }

  // public function getIsReadByUserId($nation_code,$d_order_id,$c_produk_id, $b_user_id, $chat_type){
  //   $this->db->select("is_read");
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where("b_user_id",$b_user_id);
  //   $this->db->where("chat_type",$chat_type);

  //   $this->db->order_by("is_read","desc");
  //   $d = $this->db->get_first();
  //   if(isset($d->is_read)) return $d->is_read;
  //   return 1;
  // }

  // public function check($nation_code,$d_order_id,$c_produk_id,$b_user_id, $chat_type){
  //   $this->db->select_as("COUNT(*)","total");
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where("b_user_id",$b_user_id);
  //   $this->db->where("chat_type",$chat_type);

  //   $this->db->order_by("is_read","desc");
  //   $d = $this->db->get_first('',0);
  //   if(isset($d->total)) return (int) $d->total;
  //   return 0;
  // }

  // public function setAsUnRead($nation_code,$d_order_id,$c_produk_id,$b_user_id, $chat_type){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where("b_user_id",$b_user_id);
  //   $this->db->where("chat_type",$chat_type);

  //   return $this->db->update($this->tbl,array("is_read"=>0));
  // }

  // public function countUnread($nation_code,$b_user_id){
  //   $this->db->select_as("COUNT(*)","total");
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("b_user_id",$b_user_id);
  //   $this->db->where("is_read",0);
  //   $this->db->where("is_active",1);
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

  // public function getRoomChatIDByParticipantId($nation_code, $b_user_id_1, $b_user_id_2, $chat_type){
  //   $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
  //   $this->db->select_as("$this->tbl_as.e_chat_room_id", "e_chat_room_id", 0);
  //   $this->db->from($this->tbl, $this->tbl_as);
  //   $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "left");
  //   $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
  //   $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
  //   $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), "left");
  //   $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
  //   $this->db->where_as("$this->tbl7_as.chat_type",$this->db->esc($chat_type));
  //   $this->db->where_as("$this->tbl_as.is_active",$this->db->esc(1));

  //   $this->db->where_as("1", "1", 'or', '<>', 1, 0);

  //   $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id_1),'AND', '=',1,0);
  //   $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id_2),'OR', '=',0,1);

  //   $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id_2),'AND', '=',1,0);
  //   $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id_1),'AND', '=',0,1);

  //   $this->db->where_as("1", "1", 'and', '<>', 0, 1);
    
  //   return $this->db->get_first();
  // }

  public function countAllByChatRoomIdUserId($nation_code, $chat_room_id, $b_user_id){
    $this->db->select_as("COUNT($this->tbl_as.nation_code)", "total", 0);

    $this->db->from($this->tbl, $this->tbl_as);

    $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    $this->db->where_as("$this->tbl_as.i_chat_room_id", $this->db->esc($chat_room_id));
    $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    $this->db->where_as("$this->tbl_as.is_read", $this->db->esc("0"));

    $d = $this->db->get_first('object', 0);
    if (isset($d->total)) {
        return $d->total;
    }
    return 0;
  }

  public function setAsRead($nation_code,$chat_room_id,$b_user_id, $i_chat_ids=array()){
    $this->db->where("nation_code",$nation_code);
    $this->db->where("i_chat_room_id",$chat_room_id);
    $this->db->where("b_user_id",$b_user_id);

    if($i_chat_ids){
      $this->db->where_in("i_chat_id", $i_chat_ids);
    }

    $du = array('is_read' => 1);

    return $this->db->update($this->tbl,$du);
  }

  // public function setAsReadAll($nation_code,$b_user_id){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("is_read",0);
  //   $this->db->where("b_user_id",$b_user_id);

  //   $du = array('is_read' => 1);

  //   return $this->db->update($this->tbl,$du);
  // }

  public function checkReadByLawanBicara($nation_code, $chat_room_id, $i_chat_id, $b_user_id){
    $this->db->select_as("$this->tbl_as.is_read", "is_read", 0);

    $this->db->from($this->tbl, $this->tbl_as);

    $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    $this->db->where_as("$this->tbl_as.i_chat_room_id", $this->db->esc($chat_room_id));
    $this->db->where_as("$this->tbl_as.i_chat_id", $this->db->esc($i_chat_id));
    $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id),'AND','!=');
    $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    // $this->db->where_as("$this->tbl_as.is_read", $this->db->esc("0"));
    $this->db->order_by("$this->tbl_as.is_read", "ASC");

    $d = $this->db->get('object', 0);
    if ($d) {
      if($d[0]->is_read == 0){
        return '0';
      }else{
        return '1';
      }
    }
    return '1';
  }

  public function GetUnReadByLawanBicara($nation_code, $chat_room_id, $b_user_id){
    $this->db->select_as("$this->tbl_as.i_chat_id", "chat_id", 0);

    $this->db->from($this->tbl, $this->tbl_as);

    $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    $this->db->where_as("$this->tbl_as.i_chat_room_id", $this->db->esc($chat_room_id));
    $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id),'AND','!=');
    $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    $this->db->where_as("$this->tbl_as.is_read", $this->db->esc("0"));

    return $this->db->get('object', 0);
  }
}
