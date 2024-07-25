<?php
class I_Chat_Room_Model extends JI_Model {
  var $tbl = 'i_chat_room';
  var $tbl_as = 'icr';
  var $tbl2 = 'i_chat_participant';
  var $tbl2_as = 'icp';
  var $tbl3 = 'i_group';
  var $tbl3_as = 'ig';
  var $tbl4 = 'b_user';
  var $tbl4_as = 'bu';

  public function __construct(){
    parent::__construct();
    $this->db->from($this->tbl,$this->tbl_as);
  }

  public function getTblAs()
  {
    return $this->tbl_as;
  }

  private function __joinTbl2(){
    $cps = array();
    $cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl2_as.nation_code");
    $cps[] = $this->db->composite_create("$this->tbl_as.id","=","$this->tbl2_as.i_chat_room_id");
    return $cps;
  }

  private function __joinTbl3()
  {
    $cps = array();
    $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
    $cps[] = $this->db->composite_create("$this->tbl_as.i_group_id", "=", "$this->tbl3_as.id");
    return $cps;
  }

  private function __joinTbl4()
  {
    $cps = array();
    $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
    $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id_creator", "=", "$this->tbl4_as.id");
    return $cps;
  }

  public function checkId($nation_code, $id)
  {
    $this->db->select_as("COUNT(*)", "jumlah");
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
    $d = $this->db->get_first("object", 0);
    if (isset($d->jumlah)) {
        return $d->jumlah;
    }
    return 0;
  }

  public function set($di){
    if (isset($di['last_chat_b_user_fnama'])) {
      if (strlen($di['last_chat_b_user_fnama'])) {
          $di['last_chat_b_user_fnama'] = $this->__encrypt($di['last_chat_b_user_fnama']);
      }
    }
    return $this->db->insert($this->tbl,$di);
  }

  public function update($nation_code,$id,$du){
    if (isset($du['last_chat_b_user_fnama'])) {
      if (strlen($du['last_chat_b_user_fnama'])) {
        $du['last_chat_b_user_fnama'] = $this->__encrypt($du['last_chat_b_user_fnama']);
      }
    }
    $this->db->where("nation_code",$nation_code);
    $this->db->where("id",$id);
    return $this->db->update($this->tbl,$du);
  }

  // public function delete($nation_code,$d_order_id,$c_produk_id){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   return $this->db->delete($this->tbl);
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

  // public function setAsUnRead($nation_code,$d_order_id,$c_produk_id,$b_user_id, $chat_type){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where("b_user_id",$b_user_id);
  //   $this->db->where("chat_type",$chat_type);
  //   return $this->db->update($this->tbl,array("is_read"=>0));
  // }

  // public function setAsRead($nation_code,$chat_room_id,$checkActive, $chat_type){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("id",$chat_room_id);
  //   $this->db->where("chat_type",$chat_type);
    
  //   if($checkActive == 1){
  //     $du = array('is_read_1' => 1);
  //   }else{
  //     $du = array('is_read_2' => 1);

  //   }

  //   return $this->db->update($this->tbl,$du);
  // }

  // public function countUnread($nation_code,$b_user_id){
  //   $this->db->select_as("COUNT(*)","total");

  //   $this->db->from($this->tbl, $this->tbl_as);
  //   $this->db->where("nation_code",$nation_code);

  //   $this->db->where_as("$this->tbl_as.b_user_id_1", $this->db->esc($b_user_id), 'and', '=', 1, 0);
  //   $this->db->where_as("$this->tbl_as.is_read_1", $this->db->esc(0), 'and', '=', 0, 0);
  //   $this->db->where_as("$this->tbl_as.b_user_id_1_is_active", $this->db->esc(1), 'or', '=', 0, 1);

  //   $this->db->where_as("$this->tbl_as.b_user_id_2", $this->db->esc($b_user_id), 'and', '=', 1, 0);
  //   $this->db->where_as("$this->tbl_as.is_read_2", $this->db->esc(0), 'and', '=', 0, 0);
  //   $this->db->where_as("$this->tbl_as.b_user_id_2_is_active", $this->db->esc(1), 'and', '=', 0, 1);

  //   // $this->db->where("is_read",0);
  //   $d = $this->db->get_first('',0);
  //   if(isset($d->total)) return $d->total;
  //   return 0;
  // }

  // public function countAll($nation_code, $chat_type = '', $c_produk_id = 0, $offer_status = "", $interval=0){
  //   $this->db->select_as("COUNT(*)", "total", 0);

  //   $this->db->from($this->tbl, $this->tbl_as);

  //   $this->db->where("$this->tbl_as.nation_code",$nation_code);

  //   if($chat_type){
  //     $this->db->where("$this->tbl_as.chat_type",$chat_type);
  //   }

  //   if($c_produk_id != '0'){
  //     $this->db->where("$this->tbl_as.c_produk_id",$c_produk_id);
  //   }

  //   if (is_array($offer_status) && count($offer_status)>0) {
  //     $this->db->where_in("$this->tbl_as.offer_status", $offer_status);
  //   }else if($offer_status){
  //     $this->db->where("$this->tbl_as.offer_status",$offer_status);
  //   }

  //   if($interval != 0){
  //     $this->db->where_as("DATE_ADD(DATE(COALESCE($this->tbl_as.offer_status_update_date,NOW())), INTERVAL $interval DAY)", "DATE(NOW())", "AND", "<=");
  //   }

  //   $d = $this->db->get_first();
  //   if (isset($d->total)) {
  //       return $d->total;
  //   }
  //   return 0;
  // }

  // public function getAll($nation_code, $chat_type = '', $c_produk_id = 0, $offer_status = "", $interval=0){
  //   $this->db->select_as("$this->tbl_as.id", "id", 0);
  //   $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
  //   $this->db->select_as("$this->tbl_as.b_user_id_starter", "b_user_id_starter", 0);
  //   $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl5_as.fnama").',"")', "b_user_nama_starter", 0);
  //   $this->db->select_as("COALESCE($this->tbl5_as.image,'')", "b_user_image_starter", 0);
  //   $this->db->select_as("$this->tbl_as.c_community_id", "c_community_id", 0);
  //   $this->db->select_as("$this->tbl_as.c_produk_id", "c_produk_id", 0);
  //   $this->db->select_as("$this->tbl_as.b_user_id_seller", "b_user_id_seller", 0);
  //   $this->db->select_as("$this->tbl_as.c_produk_nama", "c_produk_nama", 0);
  //   $this->db->select_as("$this->tbl_as.c_produk_harga_jual", "c_produk_harga_jual", 0);
  //   $this->db->select_as("$this->tbl_as.c_produk_thumb", "c_produk_thumb", 0);
  //   $this->db->select_as("$this->tbl_as.product_edited", "product_edited", 0);
  //   $this->db->select_as("$this->tbl_as.offer_status", "offer_status", 0);
  //   $this->db->select_as("$this->tbl_as.custom_name_1", "custom_name_1", 0);
  //   $this->db->select_as("$this->tbl_as.custom_name_2", "custom_name_2", 0);
  //   $this->db->select_as("$this->tbl_as.is_read_admin", "is_read_admin", 0);
  //   $this->db->select_as("$this->tbl_as.chat_type", "chat_type", 0);

  //   $this->db->from($this->tbl, $this->tbl_as);
  //   $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");

  //   $this->db->where("$this->tbl_as.nation_code",$nation_code);

  //   if($chat_type){
  //     $this->db->where("$this->tbl_as.chat_type",$chat_type);
  //   }

  //   if($c_produk_id != '0'){
  //     $this->db->where("$this->tbl_as.c_produk_id",$c_produk_id);
  //   }

  //   if (is_array($offer_status) && count($offer_status)>0) {
  //     $this->db->where_in("$this->tbl_as.offer_status", $offer_status);
  //   }else if($offer_status){
  //     $this->db->where("$this->tbl_as.offer_status",$offer_status);
  //   }

  //   if($interval != 0){
  //     $this->db->where_as("DATE_ADD(DATE(COALESCE($this->tbl_as.offer_status_update_date,NOW())), INTERVAL $interval DAY)", "DATE(NOW())", "AND", "<=");
  //     // $this->db->where_as("DATE_ADD(COALESCE($this->tbl_as.offer_status_update_date,NOW()), INTERVAL $interval MINUTE)", "NOW()", "AND", "<=");
  //     $this->db->limit("2");
  //   }

  //   return $this->db->get();
  // }

  public function getAllByCustom($nation_code, $i_group_id, $b_user_id, $type=""){
    $this->db->select_as("$this->tbl_as.id", "id", 0);
    $this->db->select_as("$this->tbl_as.i_group_id", "i_group_id", 0);
    $this->db->select_as("$this->tbl_as.b_user_ids", "b_user_ids", 0);
    $this->db->select_as("$this->tbl_as.type", "type", 0);
    $this->db->select_as("$this->tbl_as.custom_name_1", "custom_name_1", 0);
    $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.last_chat_b_user_fnama").',"")', "last_chat_b_user_fnama", 0);
    $this->db->select_as("$this->tbl_as.last_chat_message", "last_chat_message", 0);
    $this->db->select_as("$this->tbl_as.last_chat_cdate", "last_chat_cdate", 0);
    $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    $this->db->select_as("$this->tbl3_as.image_thumb", "band_group_image", 0);

    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");

    $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    $this->db->where_as("$this->tbl_as.i_group_id", $this->db->esc($i_group_id));
    $this->db->where_as("$this->tbl_as.is_active",$this->db->esc(1));
    $this->db->where_as("$this->tbl_as.b_user_ids", $this->db->esc($b_user_id), 'AND', '%like%');
    $this->db->where_as("$this->tbl_as.id", "$this->tbl3_as.i_chat_room_id", 'AND', '!=');
    if($type != ""){
      $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
    }

    return $this->db->get();
  }

  public function getChatRoomByID($nation_code, $chat_room_id){
    $this->db->select_as("$this->tbl_as.id", "id", 0);
    $this->db->select_as("$this->tbl_as.i_group_id", "i_group_id", 0);
    $this->db->select_as("$this->tbl_as.b_user_id_creator", "b_user_id_creator", 0);
    $this->db->select_as("$this->tbl_as.is_main_group_chat_room", "is_main_group_chat_room", 0);
    $this->db->select_as("$this->tbl_as.total_people_chat_room", "total_people_chat_room", 0);
    $this->db->select_as("$this->tbl_as.b_user_ids", "b_user_ids", 0);
    $this->db->select_as("$this->tbl_as.type", "type", 0);
    $this->db->select_as("$this->tbl_as.custom_name_1", "custom_name_1", 0);
    $this->db->select_as("$this->tbl_as.image", "image", 0);
    $this->db->select_as("$this->tbl_as.description", "description", 0);
    $this->db->select_as("$this->tbl_as.is_edited", "is_edited", 0);
    $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.last_chat_b_user_fnama").',"")', "last_chat_b_user_fnama", 0);
    $this->db->select_as("$this->tbl_as.last_chat_message", "last_chat_message", 0);
    $this->db->select_as("$this->tbl_as.last_chat_cdate", "last_chat_cdate", 0);
    $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    $this->db->select_as("$this->tbl3_as.image_thumb", "band_group_image", 0);
    $this->db->select_as("$this->tbl3_as.name", "band_group_name", 0);
    $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl4_as.band_fnama").',"")', "b_user_band_fnama_creator", 0);

    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
    $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");

    $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    $this->db->where_as("$this->tbl_as.id", $this->db->esc($chat_room_id));
    $this->db->where_as("$this->tbl_as.is_active",$this->db->esc(1));

    return $this->db->get_first();
  }

  public function getRoomChatIDByParticipantId($nation_code, $i_group_id, $b_user_id_from, $b_user_id_to, $type){
    $this->db->select_as("$this->tbl_as.id", "id", 0);
    $this->db->select_as("$this->tbl_as.i_group_id", "i_group_id", 0);
    $this->db->select_as("$this->tbl_as.b_user_ids", "b_user_ids", 0);
    $this->db->select_as("$this->tbl_as.type", "type", 0);
    $this->db->select_as("$this->tbl_as.custom_name_1", "custom_name_1", 0);
    $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.last_chat_b_user_fnama").',"")', "last_chat_b_user_fnama", 0);
    $this->db->select_as("$this->tbl_as.last_chat_message", "last_chat_message", 0);
    $this->db->select_as("$this->tbl_as.last_chat_cdate", "last_chat_cdate", 0);
    $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    $this->db->select_as("$this->tbl3_as.image_thumb", "band_group_image", 0);

    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");

    $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    $this->db->where_as("$this->tbl_as.i_group_id", $this->db->esc($i_group_id));
    $this->db->where_as("$this->tbl_as.b_user_ids", $this->db->esc($b_user_id_from), 'AND', '%like%');
    $this->db->where_as("$this->tbl_as.b_user_ids", $this->db->esc($b_user_id_to), 'AND', '%like%');
    $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
    $this->db->where_as("$this->tbl_as.is_active",$this->db->esc(1));

    return $this->db->get_first();
  }

  public function countRoomChatByUserID($nation_code, $b_user_id, $i_group_id="", $datetime_last_call_server=''){
    $this->db->select_as("COUNT($this->tbl_as.i_group_id)", "total", 0);

    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");

    $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
    $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id));
    $this->db->where_as("$this->tbl_as.is_active",$this->db->esc(1));

    if($i_group_id != ''){
      $this->db->where_as("$this->tbl_as.i_group_id", $this->db->esc($i_group_id));
    }

    if($datetime_last_call_server != ''){
      $this->db->where_as("$this->tbl2_as.ldate", $this->db->esc($datetime_last_call_server),'AND','>=');
      $this->db->where_as("$this->tbl2_as.is_read", $this->db->esc(0));
    }

    $d = $this->db->get_first('object', 0);
    if (isset($d->total)) {
        return $d->total;
    }
    return 0;
  }

  public function getRoomChatByUserID($nation_code, $b_user_id, $page=1, $page_size=10, $sort_col='last_chat_cdate', $sort_dir='DESC', $i_group_id="", $datetime_last_call_server=''){
    $this->db->select_as("$this->tbl_as.id", "id", 0);
    $this->db->select_as("$this->tbl_as.i_group_id", "i_group_id", 0);
    $this->db->select_as("$this->tbl_as.b_user_id_creator", "b_user_id_creator", 0);
    $this->db->select_as("$this->tbl_as.is_main_group_chat_room", "is_main_group_chat_room", 0);
    $this->db->select_as("$this->tbl_as.total_people_chat_room", "total_people_chat_room", 0);
    $this->db->select_as("$this->tbl_as.b_user_ids", "b_user_ids", 0);
    $this->db->select_as("$this->tbl_as.type", "type", 0);
    $this->db->select_as("$this->tbl_as.custom_name_1", "custom_name_1", 0);
    $this->db->select_as("COALESCE($this->tbl_as.image, '')", "image", 0);
    $this->db->select_as("$this->tbl_as.description", "description", 0);
    $this->db->select_as("$this->tbl_as.is_edited", "is_edited", 0);
    $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.last_chat_b_user_fnama").',"")', "last_chat_b_user_fnama", 0);
    $this->db->select_as("$this->tbl_as.last_chat_message", "last_chat_message", 0);
    $this->db->select_as("$this->tbl_as.last_chat_cdate", "last_chat_cdate", 0);
    $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    $this->db->select_as("$this->tbl2_as.is_read", "is_read", 0);
    $this->db->select_as("$this->tbl2_as.last_delete_chat", "last_delete_chat", 0);
    $this->db->select_as("$this->tbl3_as.image_thumb", "band_group_image", 0);
    $this->db->select_as("$this->tbl3_as.name", "band_group_name", 0);
    $this->db->select_as("IF(($this->tbl_as.last_chat_cdate IS NOT NULL), IF(($this->tbl_as.last_chat_cdate >= $this->tbl2_as.last_delete_chat), $this->tbl_as.last_chat_cdate, $this->tbl2_as.last_delete_chat), $this->tbl_as.cdate)", "cdate_for_order_by", 0);

    $this->db->from($this->tbl2, $this->tbl2_as);
    $this->db->join_composite($this->tbl, $this->tbl_as, $this->__joinTbl2(), "inner");
    $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");

    $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
    $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id));
    $this->db->where_as("$this->tbl_as.is_active",$this->db->esc(1));
    // $this->db->where_as("IF(($this->tbl_as.is_main_group_chat_room = '1'), '1', IF((SELECT COALESCE(cdate, '') FROM i_chat WHERE i_chat_room_id = $this->tbl_as.id AND type = 'chat' AND is_active = '1' ORDER BY cdate DESC LIMIT 1) >= $this->tbl2_as.cdate, '1', '0'))",$this->db->esc(1));
    $this->db->where_as("$this->tbl2_as.is_first_time_join",$this->db->esc(0));

    if($i_group_id != ''){
      $this->db->where_as("$this->tbl_as.i_group_id", $this->db->esc($i_group_id));
    }

    if($datetime_last_call_server != ''){
      $this->db->where_as("$this->tbl2_as.ldate", $this->db->esc($datetime_last_call_server),'AND','>=');
      $this->db->where_as("$this->tbl2_as.is_read", $this->db->esc(0));
    }

    $this->db->order_by("IF(($this->tbl_as.last_chat_cdate IS NOT NULL), IF(($this->tbl_as.last_chat_cdate >= $this->tbl2_as.last_delete_chat), $this->tbl_as.last_chat_cdate, $this->tbl2_as.last_delete_chat), $this->tbl_as.cdate)", $sort_dir);

    if($page != 0 && $page_size != 0){
      $this->db->page($page, $page_size);
    }

    return $this->db->get('',0);
  }
}
