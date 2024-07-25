<?php
class E_Chat_Room_Model extends SENE_Model {
  var $is_cacheable;
  var $tbl = 'e_chat_room';
  var $tbl_as = 'chat_room';
  var $tbl2 = 'b_user'; 
  var $tbl2_as = 'starter_user';
  var $tbl3 = 'e_chat'; 
  var $tbl3_as = 'chat';
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
  var $tbl20 = 'e_chat';
  var $tbl20_as = 'e';

  public function __construct(){
    parent::__construct();
    $this->is_cacheable = 0;
    $this->db->from($this->tbl,$this->tbl_as);
  }

  private function __joinTbl2(){
    $cps = array();
    $cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl2_as.nation_code");
    $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id_starter","=","$this->tbl2_as.id");
    return $cps;
  }

  private function __joinTbl3(){
    $cps = array();
    $cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl3_as.nation_code");
    $cps[] = $this->db->composite_create("$this->tbl_as.id","=","$this->tbl3_as.e_chat_room_id");
    return $cps;
  }

  private function __joinTbl4()
  {
      $cps = array();
      $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl4_as.nation_code");
      $cps[] = $this->db->composite_create("$this->tbl2_as.a_pengguna_id", "=", "$this->tbl4_as.id");
      return $cps;
  }

  private function __joinTbl5()
  {
      $cps = array();
      $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl5_as.nation_code");
      $cps[] = $this->db->composite_create("$this->tbl2_as.b_user_id", "=", "$this->tbl5_as.id");
      return $cps;
  }

  private function __joinTbl10()
  {
      $cps = array();
      $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl10_as.nation_code");
      $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id_starter", "=", "$this->tbl10_as.id");
      return $cps;
  }

  private function __joinTbl11()
  {
      $cps = array();
      $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl11_as.nation_code");
      $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id_2", "=", "$this->tbl11_as.id");
      return $cps;
  }

  public function getTableAlias()
  {
      return $this->tbl_as;
  }

  public function getTableAlias2()
  {
      return $this->tbl2_as;
  }

  public function getTableAlias5()
  {
      return $this->tbl5_as;
  }

  public function getTableAlias10()
  {
      return $this->tbl10_as;
  }

  public function getTableAlias11()
  {
      return $this->tbl11_as;
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

  public function countAll($nation_code,$keyword, $chat_type = 'ALL'){
    
    $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);

    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
    // $this->db->join_composite($this->tbl11, $this->tbl11_as, $this->__joinTbl11(), "left");

    $this->db->where("$this->tbl_as.nation_code",$nation_code);
    // $this->db->where("$this->tbl_as.chat_type", $chat_type);

    if (strlen($keyword)>0) {
        $this->db->where_as("COALESCE($this->tbl2_as.fnama,'-')", addslashes($keyword), "AND", "%like%", 0,0);
        // $this->db->where_as("COALESCE($this->tbl11_as.fnama,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
    }

    $d = $this->db->get_first('object', 0);
    if (isset($d->total)) {
        return $d->total;
    }

  }


  // public function getAll($nation_code, $keyword, $page=1, $page_size=10, $sort_col='cdate', $sort_dir='DESC', $chat_type='ALL'){

  //   $sql = "
  //     SELECT
  //       ".$this->tbl_as.".id AS 'chat_room_id',
  //       ".$this->tbl_as.".b_user_id_1,
  //       ".$this->tbl_as.".b_user_id_2,
  //       COALESCE(AES_DECRYPT(".$this->tbl10_as.".fnama, '".$this->db->enckey."'), 'Admin') AS 'b_user_fnama_1',
  //       AES_DECRYPT(".$this->tbl11_as.".fnama, '".$this->db->enckey."') AS 'b_user_fnama_2',
  //       COALESCE(".$this->tbl4_as.".nama, '-') AS 'a_pengguna_nama_last_chat',
  //       AES_DECRYPT(".$this->tbl5_as.".fnama, '".$this->db->enckey."') AS 'b_user_fnama_last_chat',
  //       ".$this->tbl2_as.".message AS 'message_last_chat',
  //       ".$this->tbl2_as.".cdate AS 'cdate_last_chat',
  //       COALESCE(".$this->tbl5_as.".id, '0') AS 'is_user',
  //       ".$this->tbl_as.".is_read_1,
  //       ".$this->tbl_as.".is_read_2,
  //       ".$this->tbl_as.".is_read_admin,
  //       ".$this->tbl_as.".chat_type
  //     FROM
  //         `".$this->tbl."` ".$this->tbl_as."
  //     LEFT JOIN (SELECT * FROM
  //     `".$this->tbl2."` ORDER BY `cdate` DESC LIMIT 18446744073709551615) ".$this->tbl2_as."
  //     ON
  //         ".$this->tbl_as.".nation_code = ".$this->tbl2_as.".nation_code AND ".$this->tbl_as.".id = ".$this->tbl2_as.".e_chat_room_id
  //     LEFT JOIN `".$this->tbl4."` ".$this->tbl4_as." ON
  //         ".$this->tbl2_as.".nation_code = ".$this->tbl4_as.".nation_code AND ".$this->tbl2_as.".a_pengguna_id = ".$this->tbl4_as.".id
  //     LEFT JOIN `".$this->tbl5."` ".$this->tbl5_as." ON
  //         ".$this->tbl2_as.".nation_code = ".$this->tbl5_as.".nation_code AND ".$this->tbl2_as.".b_user_id = ".$this->tbl5_as.".id
  //     LEFT JOIN `".$this->tbl10."` ".$this->tbl10_as." ON
  //         ".$this->tbl_as.".nation_code = ".$this->tbl10_as.".nation_code AND ".$this->tbl_as.".b_user_id_1 = ".$this->tbl10_as.".id
  //     LEFT JOIN `".$this->tbl11."` ".$this->tbl11_as." ON
  //         ".$this->tbl_as.".nation_code = ".$this->tbl11_as.".nation_code AND ".$this->tbl_as.".b_user_id_2 = ".$this->tbl11_as.".id
  //     WHERE
  //         ".$this->tbl_as.".`nation_code` = ".$nation_code." 
  //         AND ".$this->tbl_as.".`chat_type` = '".$chat_type."'";

  //     if(strlen($keyword)>0){
        
  //       $sql .= " AND(
  //             AES_DECRYPT(".$this->tbl10_as.".fnama, '".$this->db->enckey."') LIKE '%".$keyword."%' OR AES_DECRYPT(".$this->tbl11_as.".fnama, '".$this->db->enckey."') LIKE '%".$keyword."%'
  //         )";

  //     }
      
  //     $sql .= " GROUP BY
  //         CONCAT(
  //             ".$this->tbl_as.".nation_code,
  //             '-',
  //             ".$this->tbl_as.".b_user_id_1,
  //             '-',
  //             ".$this->tbl_as.".b_user_id_2,
  //             '-',
  //             ".$this->tbl_as.".id,
  //             '-',
  //             ".$this->tbl_as.".chat_type
  //         )
  //     ORDER BY
  //       ".$sort_col." ". $sort_dir."
  //     LIMIT
  //       ".$page.", ".$page_size;

  //   return $this->db->query($sql);

  // }
  
  public function getAll($nation_code, $keyword, $page=1, $page_size=10, $sort_col='cdate', $sort_dir='DESC', $room_type=null, $from_date=null, $to_date=null){
    /*
      SELECT
        `chat_room`.`id` AS `room_id`,
        CONCAT(UCASE(SUBSTRING(`chat_room`.`chat_type`, 1, 1)), LOWER(SUBSTRING(`chat_room`.`chat_type`, 2))) AS `room_type`,
        `chat_room`.`cdate` AS `submit_date`,
        `chat_room`.`ldate` AS `last_date`,
        COALESCE(AES_DECRYPT(`starter_user`.fnama, '".$this->db->enckey."'), 'Admin') AS 'starter_fname',
        `chat_room`.`c_community_id` AS `community_id`,
        IFNULL((
          SELECT `chat`.`message`
          FROM `e_chat` `chat`
          WHERE `chat`.`nation_code`=`chat_room`.`nation_code`
            AND `chat`.`e_chat_room_id`=`chat_room`.`id`
            AND `chat`.`type`='chat'
          ORDER BY `cdate` DESC
          LIMIT 1
        ), '-') AS `last_chat`,
        `chat_room`.`custom_name_1`,
        `chat_room`.`custom_name_2`,
        `chat_room`.`is_read_admin`
      FROM `e_chat_room` `chat_room`
      LEFT JOIN `b_user` `starter_user`
        ON `starter_user`.`nation_code`=`chat_room`.`nation_code`
        AND `starter_user`.`id`=`chat_room`.`b_user_id_starter`
      WHERE `chat_room`.`nation_code` = ".$nation_code." 
    */
    
    /*
       AND AES_DECRYPT(`starter_user`.fnama, '".$this->db->enckey."') LIKE '%".$keyword."%' 
    */

    /*
       GROUP BY CONCAT(
          `chat_room`.nation_code,
          '-',
          `chat_room`.b_user_id_starter,
          '-',
          `chat_room`.id,
          '-',
          `chat_room`.chat_type
      )
      ORDER BY ".$sort_col." ". $sort_dir."
      LIMIT ".$page.", ".$page_size.";
    */

    $sql = "SELECT `chat_room`.`id` AS `room_id`, CONCAT(UCASE(SUBSTRING(`chat_room`.`chat_type`, 1, 1)), LOWER(SUBSTRING(`chat_room`.`chat_type`, 2))) AS `room_type`, `chat_room`.`cdate` AS `submit_date`, `chat_room`.`ldate` AS `last_date`, COALESCE(AES_DECRYPT(`starter_user`.fnama, '".$this->db->enckey."'), 'Admin') AS 'starter_fname', `chat_room`.`c_community_id` AS `community_id`, IFNULL((SELECT `chat`.`message` FROM `e_chat` `chat` WHERE `chat`.`nation_code`=`chat_room`.`nation_code` AND `chat`.`e_chat_room_id`=`chat_room`.`id` AND `chat`.`type`='chat'ORDER BY `cdate` DESC LIMIT 1 ), '-') AS `last_chat`, `chat_room`.`custom_name_1`, `chat_room`.`custom_name_2`, `chat_room`.`is_read_admin` FROM `e_chat_room` `chat_room` LEFT JOIN `b_user` `starter_user` ON `starter_user`.`nation_code`=`chat_room`.`nation_code` AND `starter_user`.`id`=`chat_room`.`b_user_id_starter` WHERE `chat_room`.`nation_code` = ".$nation_code." "; if (strlen($keyword)>0) {
      $sql .= "  AND LOWER(AES_DECRYPT(`starter_user`.fnama, '".$this->db->enckey."')) LIKE '%".$keyword."%'";
    }
    if ($room_type) {
      $sql .= " AND `chat_room`.`chat_type` = '".$room_type."'";
    }
    if ($from_date) {
      $sql .= " AND DATE(`chat_room`.`ldate`)>='".$from_date."'";
    }
    if ($to_date) {
      $sql .= " AND DATE(`chat_room`.`ldate`)<='".$to_date."'";
    }

    $sql .= " GROUP BY CONCAT( `chat_room`.nation_code, '-', `chat_room`.b_user_id_starter, '-', `chat_room`.id, '-', `chat_room`.chat_type ) ORDER BY ".$sort_col." ". $sort_dir." LIMIT ".$page.", ".$page_size.";";

    return $this->db->query($sql);
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

  // public function set($di){
  //   return $this->db->insert($this->tbl,$di);
  // }
  // public function setMass($ds){
  //   return $this->db->insert_multi($this->tbl,$ds,0);
  // }
  public function update($nation_code,$chat_room_id,$du){
    $this->db->where("nation_code",$nation_code);
    $this->db->where("id",$chat_room_id);
    return $this->db->update($this->tbl,$du);
  }
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
