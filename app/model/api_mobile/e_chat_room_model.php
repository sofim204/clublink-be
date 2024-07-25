<?php
class E_Chat_Room_Model extends JI_Model {
  var $is_cacheable;
  var $tbl = 'e_chat_room';
  var $tbl_as = 'ecr';

  //START by Donny Dennison - 26 july 2022 14:35
  //offer list for buyer
  var $tbl2 = 'e_chat';
  var $tbl2_as = 'ec';
  var $tbl3 = 'e_offer_review';
  var $tbl3_as = 'eorbuyer';
  var $tbl4 = 'e_offer_review';
  var $tbl4_as = 'eorseller';
  //END by Donny Dennison - 26 july 2022 14:35
  //offer list for buyer

  var $tbl5 = 'b_user';
  var $tbl5_as = 'bu';

  //START by Donny Dennison - 26 july 2022 14:35
  //offer list for buyer
  var $tbl6 = 'b_user';
  var $tbl6_as = 'bu2';
  //END by Donny Dennison - 26 july 2022 14:35
  //offer list for buyer

  var $tbl7 = 'e_chat_participant';
  var $tbl7_as = 'ecp';

  //START by Donny Dennison - 26 july 2022 14:35
  //offer list for buyer
  var $tbl8 = 'c_produk';
  var $tbl8_as = 'cp';
  var $tbl9 = 'b_kategori';
  var $tbl9_as = 'bk';
  //END by Donny Dennison - 26 july 2022 14:35
  //offer list for buyer

  // var $tbl10 = 'b_user'; //join with e_chat_room
  // var $tbl10_as = 'bb';
  // var $tbl11 = 'b_user'; //join with e_chat_room
  // var $tbl11_as = 'bs';
  // var $tbl20 = 'e_chat';
  // var $tbl20_as = 'e';

  public function __construct(){
    parent::__construct();
    $this->is_cacheable = 0;
    $this->db->from($this->tbl,$this->tbl_as);
  }

  public function getTblAs()
  {
    return $this->tbl_as;
  }

  //START by Donny Dennison - 26 july 2022 14:35
  //offer list for buyer
  public function getTblAs2()
  {
    return $this->tbl2_as;
  }

  private function __joinTbl2(){
    $cps = array();
    $cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl2_as.nation_code");
    $cps[] = $this->db->composite_create("$this->tbl_as.id","=","$this->tbl2_as.e_chat_room_id");
    return $cps;
  }

  private function __joinTbl3()
  {
      $cps = array();
      $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
      $cps[] = $this->db->composite_create("$this->tbl2_as.e_chat_room_id", "=", "$this->tbl3_as.e_chat_room_id");
      $cps[] = $this->db->composite_create("$this->tbl2_as.id", "=", "$this->tbl3_as.e_chat_id");
      $cps[] = $this->db->composite_create("'seller'", "=", "$this->tbl3_as.type");
      return $cps;
  }

  private function __joinTbl4()
  {
      $cps = array();
      $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl4_as.nation_code");
      $cps[] = $this->db->composite_create("$this->tbl2_as.e_chat_room_id", "=", "$this->tbl4_as.e_chat_room_id");
      $cps[] = $this->db->composite_create("$this->tbl2_as.id", "=", "$this->tbl4_as.e_chat_id");
      $cps[] = $this->db->composite_create("'buyer'", "=", "$this->tbl4_as.type");
      return $cps;
  }
  //END by Donny Dennison - 26 july 2022 14:35
  //offer list for buyer

  private function __joinTbl5()
  {
      $cps = array();
      $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl5_as.nation_code");
      $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id_starter", "=", "$this->tbl5_as.id");
      return $cps;
  }

  //START by Donny Dennison - 26 july 2022 14:35
  //offer list for buyer
  private function __joinTbl6()
  {
      $cps = array();
      $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl6_as.nation_code");
      $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id_seller", "=", "$this->tbl6_as.id");
      return $cps;
  }
  //END by Donny Dennison - 26 july 2022 14:35
  //offer list for buyer

  private function __joinTbl7()
  {
      $cps = array();
      $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl7_as.nation_code");
      $cps[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl7_as.e_chat_room_id");
      return $cps;
  }

  //START by Donny Dennison - 26 july 2022 14:35
  //offer list for buyer
  private function __joinTbl8()
  {
      $cps = array();
      $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl8_as.nation_code");
      $cps[] = $this->db->composite_create("$this->tbl_as.c_produk_id", "=", "$this->tbl8_as.id");
      return $cps;
  }

  private function __joinTbl9()
  {
      $cps = array();
      $cps[] = $this->db->composite_create("$this->tbl8_as.nation_code", "=", "$this->tbl9_as.nation_code");
      $cps[] = $this->db->composite_create("$this->tbl8_as.b_kategori_id", "=", "$this->tbl9_as.id");
      return $cps;
  }
  //END by Donny Dennison - 26 july 2022 14:35
  //offer list for buyer

  // private function __joinTbl10()
  // {
  //     $cps = array();
  //     $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl10_as.nation_code");
  //     $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id_1", "=", "$this->tbl10_as.id");
  //     return $cps;
  // }

  // private function __joinTbl11()
  // {
  //     $cps = array();
  //     $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl11_as.nation_code");
  //     $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id_2", "=", "$this->tbl11_as.id");
  //     return $cps;
  // }

  public function getLastId($nation_code){
    $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->where("nation_code",$nation_code);
    $d = $this->db->get_first('',0);
    if(isset($d->last_id)) return $d->last_id;
    return 0;
  }

  public function set($di){
    return $this->db->insert($this->tbl,$di);
  }

  // public function setMass($ds){
  //   return $this->db->insert_multi($this->tbl,$ds,0);
  // }

  public function update($nation_code,$id,$du){
    $this->db->where("nation_code",$nation_code);
    $this->db->where("id",$id);
    return $this->db->update($this->tbl,$du);
  }

  //START by Donny Dennison - 12 july 2022 14:56
  //new offer system
  public function updateByProductID($nation_code,$c_produk_id,$du){
    $this->db->where("nation_code",$nation_code);
    $this->db->where("c_produk_id",$c_produk_id);
    $this->db->where("offer_status","cancelled", "OR", "=", 1, 0);
    $this->db->where("offer_status","rejected", "OR", "=", 0, 0);
    $this->db->where("offer_status","reviewed", "AND", "=", 0, 1);
    $this->db->where("chat_type","offer");
    return $this->db->update($this->tbl,$du);
  }
  //END by Donny Dennison - 12 july 2022 14:56
  //new offer system

  // //by Donny Dennison - 16-07-2020 14:30
  // //add delete chat room feature
  // public function softDelete($nation_code,$chat_room_id, $chat_type, $du){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("id",$chat_room_id);
    
  //   //By Donny Dennison - 3 august 2020 - 15:22
  //   //Add 2 chat party, admin to seller / admin to buyer
  //   $this->db->where("chat_type",$chat_type);

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

  // //By Donny Dennison - 3 august 2020 - 15:22
  // //Add 2 chat party, admin to seller / admin to buyer
  // // public function check($nation_code,$d_order_id,$c_produk_id,$b_user_id){
  // public function check($nation_code,$d_order_id,$c_produk_id,$b_user_id, $chat_type){
  //   $this->db->select_as("COUNT(*)","total");
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where("b_user_id",$b_user_id);

  //   //By Donny Dennison - 3 august 2020 - 15:22
  //   //Add 2 chat party, admin to seller / admin to buyer
  //   $this->db->where("chat_type",$chat_type);


  //   $this->db->order_by("is_read","desc");
  //   $d = $this->db->get_first('',0);
  //   if(isset($d->total)) return (int) $d->total;
  //   return 0;
  // }

  // //By Donny Dennison - 3 august 2020 - 15:22
  // //Add 2 chat party, admin to seller / admin to buyer
  // // public function setAsUnRead($nation_code,$d_order_id,$c_produk_id,$b_user_id){
  // public function setAsUnRead($nation_code,$d_order_id,$c_produk_id,$b_user_id, $chat_type){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where("b_user_id",$b_user_id);

  //   //By Donny Dennison - 3 august 2020 - 15:22
  //   //Add 2 chat party, admin to seller / admin to buyer
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

  //START by Donny Dennison - 12 july 2022 14:56
  //new offer system
  public function countAll($nation_code, $chat_type = '', $c_produk_id = 0, $offer_status = "", $interval=0){

    $this->db->select_as("COUNT(*)", "total", 0);

    $this->db->from($this->tbl, $this->tbl_as);

    $this->db->where("$this->tbl_as.nation_code",$nation_code);

    if($chat_type){
      $this->db->where("$this->tbl_as.chat_type",$chat_type);
    }

    if($c_produk_id != '0'){
      $this->db->where("$this->tbl_as.c_produk_id",$c_produk_id);
    }

    if (is_array($offer_status) && count($offer_status)>0) {
      $this->db->where_in("$this->tbl_as.offer_status", $offer_status);
    }else if($offer_status){
      $this->db->where("$this->tbl_as.offer_status",$offer_status);
    }

    if($interval != 0){
      $this->db->where_as("DATE_ADD(DATE(COALESCE($this->tbl_as.offer_status_update_date,NOW())), INTERVAL $interval DAY)", "DATE(NOW())", "AND", "<=");
    }

    $d = $this->db->get_first();
    if (isset($d->total)) {
        return $d->total;
    }
    return 0;

  }

  public function getAll($nation_code, $chat_type = '', $c_produk_id = 0, $offer_status = "", $interval=0){

    $this->db->select_as("$this->tbl_as.id", "id", 0);
    $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
    $this->db->select_as("$this->tbl_as.b_user_id_starter", "b_user_id_starter", 0);
    $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl5_as.fnama").',"")', "b_user_nama_starter", 0);
    $this->db->select_as("COALESCE($this->tbl5_as.image,'')", "b_user_image_starter", 0);
    $this->db->select_as("$this->tbl_as.c_community_id", "c_community_id", 0);
    $this->db->select_as("$this->tbl_as.c_produk_id", "c_produk_id", 0);
    $this->db->select_as("$this->tbl_as.b_user_id_seller", "b_user_id_seller", 0);
    $this->db->select_as("$this->tbl_as.c_produk_nama", "c_produk_nama", 0);
    $this->db->select_as("$this->tbl_as.c_produk_harga_jual", "c_produk_harga_jual", 0);
    $this->db->select_as("$this->tbl_as.c_produk_thumb", "c_produk_thumb", 0);
    $this->db->select_as("$this->tbl_as.product_edited", "product_edited", 0);
    $this->db->select_as("$this->tbl_as.offer_status", "offer_status", 0);
    $this->db->select_as("$this->tbl_as.custom_name_1", "custom_name_1", 0);
    $this->db->select_as("$this->tbl_as.custom_name_2", "custom_name_2", 0);
    $this->db->select_as("$this->tbl_as.is_read_admin", "is_read_admin", 0);
    $this->db->select_as("$this->tbl_as.chat_type", "chat_type", 0);

    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");

    $this->db->where("$this->tbl_as.nation_code",$nation_code);

    if($chat_type){
      $this->db->where("$this->tbl_as.chat_type",$chat_type);
    }

    if($c_produk_id != '0'){
      $this->db->where("$this->tbl_as.c_produk_id",$c_produk_id);
    }

    if (is_array($offer_status) && count($offer_status)>0) {
      $this->db->where_in("$this->tbl_as.offer_status", $offer_status);
    }else if($offer_status){
      $this->db->where("$this->tbl_as.offer_status",$offer_status);
    }

    if($interval != 0){
      $this->db->where_as("DATE_ADD(DATE(COALESCE($this->tbl_as.offer_status_update_date,NOW())), INTERVAL $interval DAY)", "DATE(NOW())", "AND", "<=");
      // $this->db->where_as("DATE_ADD(COALESCE($this->tbl_as.offer_status_update_date,NOW()), INTERVAL $interval MINUTE)", "NOW()", "AND", "<=");
      $this->db->limit("2");
    }

    return $this->db->get();

  }
  //END by Donny Dennison - 12 july 2022 14:56
  //new offer system

  public function getChatRoomByID($nation_code, $chat_room_id, $chat_type = ''){

    $this->db->select_as("$this->tbl_as.id", "id", 0);
    $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
    $this->db->select_as("$this->tbl_as.b_user_id_starter", "b_user_id_starter", 0);
    $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl5_as.fnama").',"")', "b_user_nama_starter", 0);
    $this->db->select_as("COALESCE($this->tbl5_as.image,'')", "b_user_image_starter", 0);
    $this->db->select_as("$this->tbl5_as.is_admin", "is_admin", 0);
    $this->db->select_as("$this->tbl_as.c_community_id", "c_community_id", 0);

    //by Donny Dennison - 12 july 2022 14:56
    //new offer system
    $this->db->select_as("$this->tbl_as.c_produk_id", "c_produk_id", 0);
    $this->db->select_as("$this->tbl_as.b_user_id_seller", "b_user_id_seller", 0);
    $this->db->select_as("$this->tbl_as.c_produk_nama", "c_produk_nama", 0);
    $this->db->select_as("$this->tbl_as.c_produk_harga_jual", "c_produk_harga_jual", 0);
    $this->db->select_as("$this->tbl_as.c_produk_thumb", "c_produk_thumb", 0);
    $this->db->select_as("$this->tbl_as.product_edited", "product_edited", 0);
    $this->db->select_as("$this->tbl_as.offer_status", "offer_status", 0);

    $this->db->select_as("$this->tbl_as.custom_name_1", "custom_name_1", 0);
    $this->db->select_as("$this->tbl_as.custom_name_2", "custom_name_2", 0);
    $this->db->select_as("$this->tbl_as.is_read_admin", "is_read_admin", 0);
    $this->db->select_as("$this->tbl_as.chat_type", "chat_type", 0);

    //by Donny Dennison - 23 november 2022 13:42
    //new feature, manage group member
    $this->db->select_as("$this->tbl_as.group_chat_type", "group_chat_type", 0);

    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");

    $this->db->where("$this->tbl_as.nation_code",$nation_code);
    $this->db->where_as("$this->tbl_as.id", $this->db->esc($chat_room_id));

    if($chat_type){
      $this->db->where("$this->tbl_as.chat_type",$chat_type);
    }

    return $this->db->get_first();

  }

  public function getChatRoomByCommunityID($nation_code, $c_community_id){

    $this->db->select_as("$this->tbl_as.id", "id", 0);
    $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
    $this->db->select_as("$this->tbl_as.b_user_id_starter", "b_user_id_starter", 0);
    $this->db->select_as("$this->tbl_as.c_community_id", "c_community_id", 0);
    $this->db->select_as("$this->tbl_as.custom_name_1", "custom_name_1", 0);
    $this->db->select_as("$this->tbl_as.custom_name_2", "custom_name_2", 0);
    $this->db->select_as("$this->tbl_as.chat_type", "chat_type", 0);

    $this->db->from($this->tbl, $this->tbl_as);

    $this->db->where("$this->tbl_as.nation_code",$nation_code);
    $this->db->where_as("$this->tbl_as.c_community_id", $this->db->esc($c_community_id));

    return $this->db->get_first();

  }

  public function countRoomChatByUserID($nation_code, $b_user_id, $chat_type, $datetime_last_call_server=''){

    $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);

    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "inner");

    $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
    $this->db->where_as("$this->tbl7_as.b_user_id", $this->db->esc($b_user_id));
    $this->db->where_as(" IF($this->tbl_as.chat_type = 'community',IF((SELECT COUNT(*) FROM e_chat_participant WHERE e_chat_room_id = $this->tbl_as.id AND nation_code= $nation_code AND is_active= 1) > 1,1,IF((SELECT COALESCE(ec.cdate,'') FROM e_chat AS ec WHERE ec.type != 'announcement' AND nation_code= $nation_code AND ec.cdate > IF($this->tbl7_as.last_delete_chat IS NULL,'',$this->tbl7_as.last_delete_chat) AND ec.e_chat_room_id = $this->tbl_as.id ORDER BY ec.cdate DESC LIMIT 1) IS NOT NULL,1,0)), IF((SELECT COALESCE(ec.cdate,'') FROM e_chat AS ec WHERE ec.type != 'announcement' AND nation_code= $nation_code AND ec.cdate > IF($this->tbl7_as.last_delete_chat IS NULL,'',$this->tbl7_as.last_delete_chat) AND ec.e_chat_room_id = $this->tbl_as.id ORDER BY ec.cdate DESC LIMIT 1) IS NOT NULL,1,0))", 1);
    
    if($chat_type){
      $this->db->where_as("$this->tbl_as.chat_type", $this->db->esc($chat_type));
    }

    if($datetime_last_call_server != ''){
      $this->db->where_as("$this->tbl7_as.ldate", $this->db->esc($datetime_last_call_server),'AND','>=');
      $this->db->where_as("$this->tbl7_as.is_read", $this->db->esc(0));
    }

    $this->db->where_as("$this->tbl7_as.is_active",$this->db->esc(1));

    $d = $this->db->get_first('object', 0);
    if (isset($d->total)) {
        return $d->total;
    }
    return 0;

  }

  public function getRoomChatByUserID($nation_code, $b_user_id, $page=1, $page_size=10, $sort_col='cdate', $sort_dir='DESC', $chat_type, $datetime_last_call_server=''){

    $this->db->select_as("$this->tbl_as.id", "id", 0);
    $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
    $this->db->select_as("$this->tbl_as.b_user_id_starter", "b_user_id_starter", 0);
    $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl5_as.fnama").',"")', "b_user_nama_starter", 0);
    $this->db->select_as("COALESCE($this->tbl5_as.image,'')", "b_user_image_starter", 0);
    $this->db->select_as("$this->tbl5_as.is_admin", "is_admin", 0);
    $this->db->select_as("$this->tbl_as.c_community_id", "c_community_id", 0);
    $this->db->select_as("$this->tbl_as.offer_status", "offer_status", 0);
    $this->db->select_as("$this->tbl_as.custom_name_1", "custom_name_1", 0);
    $this->db->select_as("$this->tbl_as.custom_name_2", "custom_name_2", 0);
    $this->db->select_as("$this->tbl_as.chat_type", "chat_type", 0);
    $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    $this->db->select_as("$this->tbl7_as.last_delete_chat", "last_delete_chat", 0);
    $this->db->select_as("$this->tbl7_as.is_read", "is_read", 0);

    $this->db->select_as("IF($this->tbl_as.chat_type = 'community',IF((SELECT COALESCE(ec.cdate,'') FROM e_chat AS ec WHERE ec.type != 'announcement' AND nation_code= $nation_code AND ec.cdate > IF($this->tbl7_as.last_delete_chat IS NULL,'',$this->tbl7_as.last_delete_chat) AND ec.e_chat_room_id = $this->tbl_as.id ORDER BY ec.cdate DESC LIMIT 1) IS NOT NULL,(SELECT COALESCE(ec.cdate,'') FROM e_chat AS ec WHERE ec.type != 'announcement' AND nation_code= $nation_code AND ec.cdate > IF($this->tbl7_as.last_delete_chat IS NULL,'',$this->tbl7_as.last_delete_chat) AND ec.e_chat_room_id = $this->tbl_as.id ORDER BY ec.cdate DESC LIMIT 1),$this->tbl7_as.cdate),IF((SELECT COALESCE(ec.cdate,'') FROM e_chat AS ec WHERE ec.type != 'announcement' AND nation_code= $nation_code AND ec.cdate > IF($this->tbl7_as.last_delete_chat IS NULL,'',$this->tbl7_as.last_delete_chat) AND ec.e_chat_room_id = $this->tbl_as.id ORDER BY ec.cdate DESC LIMIT 1) IS NOT NULL,(SELECT COALESCE(ec.cdate,'') FROM e_chat AS ec WHERE ec.type != 'announcement' AND nation_code= $nation_code AND ec.cdate > IF($this->tbl7_as.last_delete_chat IS NULL,'',$this->tbl7_as.last_delete_chat) AND ec.e_chat_room_id = $this->tbl_as.id ORDER BY ec.cdate DESC LIMIT 1),$this->tbl_as.cdate))", "cdate_for_order_by", 0);

    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "inner");
    $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");

    $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
    $this->db->where_as("$this->tbl7_as.b_user_id", $this->db->esc($b_user_id));
    $this->db->where_as(" IF($this->tbl_as.chat_type = 'community',IF((SELECT COUNT(*) FROM e_chat_participant WHERE e_chat_room_id = $this->tbl_as.id AND nation_code= $nation_code AND is_active= 1) > 1,1,IF((SELECT COALESCE(ec.cdate,'') FROM e_chat AS ec WHERE ec.type != 'announcement' AND nation_code= $nation_code AND ec.cdate > IF($this->tbl7_as.last_delete_chat IS NULL,'',$this->tbl7_as.last_delete_chat) AND ec.e_chat_room_id = $this->tbl_as.id ORDER BY ec.cdate DESC LIMIT 1) IS NOT NULL,1,0)), IF((SELECT COALESCE(ec.cdate,'') FROM e_chat AS ec WHERE ec.type != 'announcement' AND nation_code= $nation_code AND ec.cdate > IF($this->tbl7_as.last_delete_chat IS NULL,'',$this->tbl7_as.last_delete_chat) AND ec.e_chat_room_id = $this->tbl_as.id ORDER BY ec.cdate DESC LIMIT 1) IS NOT NULL,1,0))", 1);

    if($chat_type){
      $this->db->where_as("$this->tbl_as.chat_type", $this->db->esc($chat_type));
    }

    if($datetime_last_call_server != ''){
      $this->db->where_as("$this->tbl7_as.ldate", $this->db->esc($datetime_last_call_server),'AND','>=');
      $this->db->where_as("$this->tbl7_as.is_read", $this->db->esc(0));
    }
    
    $this->db->where_as("$this->tbl7_as.is_active",$this->db->esc(1));

    if($sort_col == "$this->tbl_as.cdate"){
      $this->db->order_by("cdate_for_order_by", $sort_dir);
    }else{
      $this->db->order_by($sort_col, $sort_dir);
    }

    if($page != 0 && $page_size != 0){
      $this->db->page($page, $page_size);
    }

    return $this->db->get('',0);

    // //pagination logic
    // $page = ($page * $page_size) - $page_size;

    // $sql = "
    //   SELECT
    //     ".$this->tbl_as.".id AS 'chat_room_id',
    //     ".$this->tbl_as.".nation_code AS 'nation_code',
    //     ".$this->tbl_as.".b_user_id_starter AS 'b_user_id_starter',
    //     ".$this->tbl_as.".c_community_category_id AS 'c_community_category_id',
    //     ".$this->tbl_as.".custom_name_1 AS 'custom_name_1',
    //     ".$this->tbl_as.".custom_name_2 AS 'custom_name_2',
    //     ".$this->tbl_as.".chat_type AS 'chat_type',
    //     ".$this->tbl2_as.".message AS 'message_last_chat',
    //     ".$this->tbl2_as.".cdate AS 'cdate_last_chat',
    //   FROM
    //       `".$this->tbl."` ".$this->tbl_as."
    //   INNER JOIN (SELECT * FROM
    //   `".$this->tbl2."` WHERE type = 'chat' ORDER BY `cdate` DESC LIMIT 18446744073709551615) ".$this->tbl2_as."
    //   ON
    //       ".$this->tbl_as.".nation_code = ".$this->tbl2_as.".nation_code AND ".$this->tbl_as.".id = ".$this->tbl2_as.".e_chat_room_id AND ".$this->tbl_as.".
    //   LEFT JOIN `".$this->tbl4."` ".$this->tbl4_as." ON
    //       ".$this->tbl2_as.".nation_code = ".$this->tbl4_as.".nation_code AND ".$this->tbl2_as.".a_pengguna_id = ".$this->tbl4_as.".id
    //   LEFT JOIN `".$this->tbl5."` ".$this->tbl5_as." ON
    //       ".$this->tbl2_as.".nation_code = ".$this->tbl5_as.".nation_code AND ".$this->tbl2_as.".b_user_id = ".$this->tbl5_as.".id
    //   LEFT JOIN `".$this->tbl10."` ".$this->tbl10_as." ON
    //       ".$this->tbl_as.".nation_code = ".$this->tbl10_as.".nation_code AND ".$this->tbl_as.".b_user_id_1 = ".$this->tbl10_as.".id
    //   LEFT JOIN `".$this->tbl11."` ".$this->tbl11_as." ON
    //       ".$this->tbl_as.".nation_code = ".$this->tbl11_as.".nation_code AND ".$this->tbl_as.".b_user_id_2 = ".$this->tbl11_as.".id
    //   WHERE
    //       `ecr`.`nation_code` = ".$nation_code." AND(
    //               ".$this->tbl_as.".b_user_id_1 = ".$b_user_id." AND ".$this->tbl_as.".b_user_id_1_is_active = 1 AND ".$this->tbl2_as.".b_user_id_1_is_active = 1
    //           ) OR(
    //               ".$this->tbl_as.".b_user_id_2 = ".$b_user_id." AND ".$this->tbl_as.".b_user_id_2_is_active = 1 AND ".$this->tbl2_as.".b_user_id_2_is_active = 1
    //           )
    //   GROUP BY
    //       CONCAT(
    //           ".$this->tbl_as.".nation_code,
    //           '-',
    //           ".$this->tbl_as.".b_user_id_1,
    //           '-',
    //           ".$this->tbl_as.".b_user_id_2,
    //           '-',
    //           ".$this->tbl2_as.".e_chat_room_id,
    //           '-',
    //           ".$this->tbl_as.".chat_type
    //       )
    //   ORDER BY
    //     ".$sort_col." ". $sort_dir."
    //   LIMIT
    //     ".$page.", ".$page_size;

    // return $this->db->query($sql);

  }

  //START by Donny Dennison - 19 july 2022 15:42
  //delete temporary or permanent user feature
  public function countAllUnfinisedOffer($nation_code, $chat_type = 'offer', $type="buyer", $b_user_id = 0, $offer_status = ""){

    $this->db->select_as("COUNT(*)", "total", 0);

    $this->db->from($this->tbl, $this->tbl_as);

    $this->db->where("$this->tbl_as.nation_code",$nation_code);

    if($chat_type){
      $this->db->where("$this->tbl_as.chat_type",$chat_type);
    }

    if($b_user_id != '0'){
      if($type == "buyer"){
        $this->db->where("$this->tbl_as.b_user_id_starter",$b_user_id);
      }else{
        $this->db->where("$this->tbl_as.b_user_id_seller",$b_user_id);
      }
    }

    if (is_array($offer_status) && count($offer_status)>0) {
      $this->db->where_in("$this->tbl_as.offer_status", $offer_status);
    }else if($offer_status){
      $this->db->where("$this->tbl_as.offer_status",$offer_status);
    }

    $d = $this->db->get_first();
    if (isset($d->total)) {
        return $d->total;
    }
    return 0;

  }
  //END by Donny Dennison - 19 july 2022 15:42
  //delete temporary or permanent user feature

  //START by Donny Dennison - 26 july 2022 14:35
  //offer list for buyer
  public function countAllForOfferList($nation_code, $chat_type = 'offer', $type="buyer", $b_user_id = 0, $product_type = "All", $offer_type = "ongoing"){

    $this->db->select_as("COUNT(*)", "total", 0);

    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "left");
    $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
    $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
    $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");

    $this->db->where("$this->tbl_as.nation_code",$nation_code);

    if($chat_type){
      $this->db->where("$this->tbl_as.chat_type",$chat_type);
    }

    if($b_user_id != '0'){
      if($type == "buyer"){
        $this->db->where("$this->tbl_as.b_user_id_starter",$b_user_id);
      }else{
        $this->db->where("$this->tbl_as.b_user_id_seller",$b_user_id);
      }
    }

    if ($product_type != 'All') {
        if ($product_type == 'AutomotiveCar') {
            $this->db->where_as("$this->tbl8_as.product_type", $this->db->esc("Automotive"));
            $this->db->where_as("$this->tbl8_as.b_kategori_id", 32);
        }else if ($product_type == 'AutomotiveMotorcycle') {
            $this->db->where_as("$this->tbl8_as.product_type", $this->db->esc("Automotive"));
            $this->db->where_as("$this->tbl8_as.b_kategori_id", 33);
        }else{
            $this->db->where_as("$this->tbl8_as.product_type", $this->db->esc($product_type));
        }
    }

    //by Donny Dennison - 3 june 2022 13:10
    //new feature, product type santa
    $this->db->where_as("$this->tbl8_as.product_type", $this->db->esc("Santa"), "AND", "!=");

    $this->db->where("$this->tbl2_as.type","accepted");

    if($offer_type == "ongoing"){

      $this->db->where("$this->tbl3_as.star", "IS NULL", "OR", "=", 1, 0);
      $this->db->where("$this->tbl4_as.star", "IS NULL", "AND", "=", 0, 1);

    }else{

      $this->db->where("$this->tbl3_as.star", "IS NOT NULL");
      $this->db->where("$this->tbl4_as.star", "IS NOT NULL");

    }

    // if (is_array($offer_status) && count($offer_status)>0) {
    //   $this->db->where_in("$this->tbl_as.offer_status", $offer_status);
    // }else if($offer_status){
    //   $this->db->where("$this->tbl_as.offer_status",$offer_status);
    // }

    $d = $this->db->get_first();
    if (isset($d->total)) {
        return $d->total;
    }
    return 0;

  }

  public function getAllForOfferList($nation_code, $chat_type = 'offer', $type="buyer", $page, $page_size, $sort_col, $sort_dir, $b_user_id = 0, $product_type = "All", $offer_type = "ongoing"){

    $this->db->select_as("$this->tbl_as.id", "chat_room_id", 0);
    $this->db->select_as("$this->tbl_as.b_user_id_starter", "b_user_id_buyer", 0);
    $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl5_as.fnama").',"")', "b_user_nama_buyer", 0);
    $this->db->select_as("COALESCE($this->tbl5_as.image,'')", "b_user_image_buyer", 0);
    $this->db->select_as("$this->tbl_as.c_produk_id", "c_produk_id", 0);
    $this->db->select_as("$this->tbl_as.b_user_id_seller", "b_user_id_seller", 0);
    $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl6_as.fnama").',"")', "b_user_nama_seller", 0);
    $this->db->select_as("COALESCE($this->tbl6_as.image,'')", "b_user_image_seller", 0);
    $this->db->select_as("$this->tbl_as.c_produk_nama", "c_produk_nama", 0);
    $this->db->select_as("$this->tbl_as.c_produk_harga_jual", "c_produk_harga_jual", 0);
    $this->db->select_as("$this->tbl_as.c_produk_thumb", "c_produk_thumb", 0);
    $this->db->select_as("$this->tbl8_as.b_kategori_id", "b_kategori_id", 0);
    $this->db->select_as("$this->tbl9_as.nama", "kategori", 0);
    $this->db->select_as("$this->tbl8_as.product_type", "product_type", 0);
    // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl8_as.alamat2").',"")', "alamat2", 0);
    $this->db->select_as("CONCAT($this->tbl8_as.kelurahan, ', ', $this->tbl8_as.kabkota)", "alamat2", 0);
    $this->db->select_as("$this->tbl8_as.stok", "stok", 0);

    if($offer_type == "ongoing"){
      $this->db->select_as("$this->tbl_as.offer_status", "offer_status", 0);
    }else{
      $this->db->select_as("'reviewed'", "offer_status", 0);
    }

    $this->db->select_as("$this->tbl2_as.cdate", "cdate", 0);

    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "left");
    $this->db->join_composite($this->tbl9, $this->tbl9_as, $this->__joinTbl9(), "left");
    $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
    $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "left");
    $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
    $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
    $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");

    $this->db->where("$this->tbl_as.nation_code",$nation_code);
    $this->db->where("$this->tbl2_as.type","accepted");

    //by Donny Dennison - 3 june 2022 13:10
    //new feature, product type santa
    $this->db->where_as("$this->tbl8_as.product_type", $this->db->esc("Santa"), "AND", "!=");

    if($chat_type){
      $this->db->where("$this->tbl_as.chat_type",$chat_type);
    }

    if($b_user_id != '0'){
      if($type == "buyer"){
        $this->db->where("$this->tbl_as.b_user_id_starter",$b_user_id);
      }else{
        $this->db->where("$this->tbl_as.b_user_id_seller",$b_user_id);
      }
    }

    if ($product_type != 'All') {
        if ($product_type == 'AutomotiveCar') {
            $this->db->where_as("$this->tbl8_as.product_type", $this->db->esc("Automotive"));
            $this->db->where_as("$this->tbl8_as.b_kategori_id", 32);
        }else if ($product_type == 'AutomotiveMotorcycle') {
            $this->db->where_as("$this->tbl8_as.product_type", $this->db->esc("Automotive"));
            $this->db->where_as("$this->tbl8_as.b_kategori_id", 33);
        }else{
            $this->db->where_as("$this->tbl8_as.product_type", $this->db->esc($product_type));
        }
    }

    if($offer_type == "ongoing"){

      $this->db->where("$this->tbl3_as.star", "IS NULL", "OR", "=", 1, 0);
      $this->db->where("$this->tbl4_as.star", "IS NULL", "AND", "=", 0, 1);

    }else{

      $this->db->where("$this->tbl3_as.star", "IS NOT NULL");
      $this->db->where("$this->tbl4_as.star", "IS NOT NULL");

    }

    // if (is_array($offer_status) && count($offer_status)>0) {
    //   $this->db->where_in("$this->tbl_as.offer_status", $offer_status);
    // }else if($offer_status){
    //   $this->db->where("$this->tbl_as.offer_status",$offer_status);
    // }

    $this->db->order_by($sort_col, $sort_dir);

    $this->db->page($page, $page_size);

    return $this->db->get();

  }
  //END by Donny Dennison - 26 july 2022 14:35
  //offer list for buyer

  //START by Donny Dennison - 12 july 2022 14:56
  //new offer system
  public function getForOffer($nation_code, $chat_type = 'offer', $type="buyer", $b_user_id, $c_produk_id){

    $this->db->select_as("$this->tbl_as.id", "chat_room_id", 0);
    $this->db->select_as("$this->tbl_as.b_user_id_starter", "b_user_id_buyer", 0);
    // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl5_as.fnama").',"")', "b_user_nama_buyer", 0);
    // $this->db->select_as("COALESCE($this->tbl5_as.image,'')", "b_user_image_buyer", 0);
    $this->db->select_as("$this->tbl_as.c_produk_id", "c_produk_id", 0);
    $this->db->select_as("$this->tbl_as.b_user_id_seller", "b_user_id_seller", 0);
    // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl6_as.fnama").',"")', "b_user_nama_seller", 0);
    // $this->db->select_as("COALESCE($this->tbl6_as.image,'')", "b_user_image_seller", 0);
    $this->db->select_as("$this->tbl_as.c_produk_nama", "c_produk_nama", 0);
    $this->db->select_as("$this->tbl_as.c_produk_harga_jual", "c_produk_harga_jual", 0);
    $this->db->select_as("$this->tbl_as.c_produk_thumb", "c_produk_thumb", 0);
    // $this->db->select_as("$this->tbl8_as.product_type", "product_type", 0);
    // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl8_as.alamat2").',"")', "alamat2", 0);
    $this->db->select_as("$this->tbl_as.offer_status", "offer_status", 0);
    $this->db->select_as("$this->tbl2_as.cdate", "cdate", 0);

    $this->db->from($this->tbl, $this->tbl_as);
    // $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "left");
    // $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
    // $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "left");
    $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
    $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
    $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");

    $this->db->where("$this->tbl_as.nation_code",$nation_code);
    $this->db->where("$this->tbl_as.c_produk_id",$c_produk_id);
    $this->db->where("$this->tbl2_as.type","accepted");

    if($chat_type){
      $this->db->where("$this->tbl_as.chat_type",$chat_type);
    }

    if($type == "buyer"){
      $this->db->where("$this->tbl_as.b_user_id_starter",$b_user_id);
      $this->db->where("$this->tbl3_as.star", "IS NULL");
    }else{
      $this->db->where("$this->tbl_as.b_user_id_seller",$b_user_id);
      $this->db->where("$this->tbl4_as.star", "IS NULL");
    }

    return $this->db->get_first();

  }
  //END by Donny Dennison - 12 july 2022 14:56
  //new offer system

  //START by Donny Dennison - 7 november 2022 14:17
  //new feature, block community post or account
  public function getAllByBuyerSeller($nation_code, $chat_type = '', $b_user_id_starter = 0, $b_user_id_seller = 0, $offer_status = "", $interval=0){

    $this->db->select_as("$this->tbl_as.id", "id", 0);
    $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
    $this->db->select_as("$this->tbl_as.b_user_id_starter", "b_user_id_starter", 0);
    $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl5_as.fnama").',"")', "b_user_nama_starter", 0);
    $this->db->select_as("COALESCE($this->tbl5_as.image,'')", "b_user_image_starter", 0);
    $this->db->select_as("$this->tbl_as.c_community_id", "c_community_id", 0);
    $this->db->select_as("$this->tbl_as.c_produk_id", "c_produk_id", 0);
    $this->db->select_as("$this->tbl_as.b_user_id_seller", "b_user_id_seller", 0);
    $this->db->select_as("$this->tbl_as.c_produk_nama", "c_produk_nama", 0);
    $this->db->select_as("$this->tbl_as.c_produk_harga_jual", "c_produk_harga_jual", 0);
    $this->db->select_as("$this->tbl_as.c_produk_thumb", "c_produk_thumb", 0);
    $this->db->select_as("$this->tbl_as.product_edited", "product_edited", 0);
    $this->db->select_as("$this->tbl_as.offer_status", "offer_status", 0);
    $this->db->select_as("$this->tbl_as.custom_name_1", "custom_name_1", 0);
    $this->db->select_as("$this->tbl_as.custom_name_2", "custom_name_2", 0);
    $this->db->select_as("$this->tbl_as.is_read_admin", "is_read_admin", 0);
    $this->db->select_as("$this->tbl_as.chat_type", "chat_type", 0);

    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");

    $this->db->where("$this->tbl_as.nation_code",$nation_code);

    if($chat_type){
      $this->db->where("$this->tbl_as.chat_type",$chat_type);
    }

    if($b_user_id_starter != '0'){
      $this->db->where("$this->tbl_as.b_user_id_starter",$b_user_id_starter);
    }

    if($b_user_id_seller != '0'){
      $this->db->where("$this->tbl_as.b_user_id_seller",$b_user_id_starter);
    }

    if (is_array($offer_status) && count($offer_status)>0) {
      $this->db->where_in("$this->tbl_as.offer_status", $offer_status);
    }else if($offer_status){
      $this->db->where("$this->tbl_as.offer_status",$offer_status);
    }

    if($interval != 0){
      $this->db->where_as("DATE_ADD(DATE(COALESCE($this->tbl_as.offer_status_update_date,NOW())), INTERVAL $interval DAY)", "DATE(NOW())", "AND", "<=");
    }

    return $this->db->get();

  }
  //END by Donny Dennison - 7 november 2022 14:17
  //new feature, block community post or account

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

}
