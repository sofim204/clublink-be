<?php
class E_Chat_Attachment_Model extends JI_Model {
  var $is_cacheable;
  var $tbl = 'e_chat_attachment';
  var $tbl_as = 'eca';

  public function __construct(){
    parent::__construct();
    $this->is_cacheable = 0;
    $this->db->from($this->tbl,$this->tbl_as);
  }

  public function getLastId($nation_code,$e_chat_room_id,$e_chat_id){
    // $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    // $this->db->from($this->tbl, $this->tbl_as);
    // $this->db->where("nation_code",$nation_code);
    // $this->db->where("e_chat_room_id",$this->db->esc($e_chat_room_id));
    // $this->db->where("e_chat_id",$e_chat_id);
    // $d = $this->db->get_first('',0);
    // if(isset($d->last_id)) return $d->last_id;
    // return 0;
    $sql ="SELECT COALESCE(MAX(id),0)+1 AS id FROM `".$this->tbl."` WHERE id >= (SELECT COALESCE(MAX(id),0) FROM `".$this->tbl."` WHERE nation_code = '".$nation_code."' AND  e_chat_room_id = '".$e_chat_room_id."' AND  e_chat_id = '".$e_chat_id."') AND nation_code = '".$nation_code."' AND  e_chat_room_id = '".$e_chat_room_id."' AND  e_chat_id = '".$e_chat_id."' FOR UPDATE;";
    return $this->db->query($sql)[0]->id;
  }
  // public function getById($nation_code, $b_user_id, $id){
  //   $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id","id",0);
  //   $this->db->from($this->tbl, $this->tbl_as);
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("b_user_id",$b_user_id);
  //   $this->db->where("id",$id);
  //   return $this->db->get_first();
  // }
  // public function getAll($nation_code, $b_user_id){
  //   $this->db->select("*");
  //   $this->db->from($this->tbl, $this->tbl_as);
  //   $this->db->where("b_user_id",$b_user_id);
  //   $this->db->order_by("id","DESC");
  //   return $this->db->get();
  // }
  public function set($di){
    return $this->db->insert($this->tbl,$di);
  }
  // public function update($nation_code,$b_user_id,$id,$du){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("b_user_id",$b_user_id);
  //   $this->db->where("id",$id);
  //   return $this->db->update($this->tbl,$du);
  // }
  // public function delete($nation_code,$b_user_id,$id){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("b_user_id",$b_user_id);
  //   $this->db->where("id",$id);
  //   return $this->db->delete($this->tbl);
  // }
  // public function getByChatIds($nation_code,$d_order_id,$c_produk_id,$e_chat_ids){
  //   $this->db->where_as("nation_code",$nation_code);
  //   $this->db->where_as("d_order_id",$d_order_id);
  //   $this->db->where_as("c_produk_id",$c_produk_id);
  //   $this->db->where_in("e_chat_id",$e_chat_ids);
  //   return $this->db->get();
  // }
  // public function getDetailByOrder($nation_code,$d_order_id,$c_produk_id){
  //   $this->db->where_as("nation_code",$nation_code);
  //   $this->db->where_as("d_order_id",$d_order_id);
  //   $this->db->where_as("c_produk_id",$c_produk_id);
  //   $this->db->order_by("e_chat_id","DESC")->order_by("id","asc");
  //   return $this->db->get();
  // }

  public function getDetailByChatRoomID($nation_code,$chat_room_id, $e_chat_ids=array()){
    $this->db->where_as("nation_code",$nation_code);
    $this->db->where_as("e_chat_room_id",$this->db->esc($chat_room_id));

    if($e_chat_ids){
        $this->db->where_in("e_chat_id", $e_chat_ids);
    }
    
    $this->db->order_by("e_chat_id","DESC")->order_by("id","asc");
    return $this->db->get();
  }

  public function getDetailByChatRoomIDChatID($nation_code,$chat_room_id, $chat_id){
    $this->db->where_as("nation_code",$nation_code);
    $this->db->where_as("e_chat_room_id",$this->db->esc($chat_room_id));
    $this->db->where_as("e_chat_id",$chat_id);
    return $this->db->get();
  }

}
