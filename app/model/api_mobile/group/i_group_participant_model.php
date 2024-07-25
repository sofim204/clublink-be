<?php
class I_Group_Participant_Model extends JI_Model {
  var $tbl = 'i_group_participant';
  var $tbl_as = 'igp';
  var $tbl2 = 'b_user';
  var $tbl2_as = 'bu';
  var $tbl3 = 'i_group';
  var $tbl3_as = 'ig';

  public function __construct(){
    parent::__construct();
    $this->db->from($this->tbl,$this->tbl_as);
  }

  private function __joinTbl2(){
    $cps = array();
    $cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl2_as.nation_code");
    $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id","=","$this->tbl2_as.id");
    return $cps;
  }

  private function __joinTbl3(){
    $cps = array();
    $cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl3_as.nation_code");
    $cps[] = $this->db->composite_create("$this->tbl_as.i_group_id","=","$this->tbl3_as.id");
    return $cps;
  }

  public function getTblAs()
  {
      return $this->tbl_as;
  }

  public function set($di){
    return $this->db->insert($this->tbl,$di);
  }

  // public function setMass($ds){
  //   return $this->db->insert_multi($this->tbl,$ds,0);
  // }

  public function update($nation_code, $group_id, $b_user_id, $du)
  {
    if (!is_array($du)) {
      return 0;
    }
    $this->db->where('nation_code', $nation_code);
    $this->db->where('i_group_id', $group_id);
    $this->db->where('b_user_id', $b_user_id);
    return $this->db->update($this->tbl, $du, 0);
  }

  // public function softDelete($nation_code,$d_order_id,$c_produk_id,$b_user_id, $chat_type, $du){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where("chat_type",$chat_type);
  //   $this->db->where("b_user_id",$b_user_id);
  //   return $this->db->update($this->tbl,$du);
  // }

  public function del($nation_code, $group_id, $b_user_id)
  {
    $this->db->where("nation_code", $nation_code);
    $this->db->where("i_group_id", $group_id);
    if($b_user_id != '0'){
      $this->db->where("b_user_id", $b_user_id);
    }
    return $this->db->delete($this->tbl);
  }

  // public function deleteByUserId($nation_code,$d_order_id,$c_produk_id,$b_user_id){
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $this->db->where("b_user_id",$b_user_id);
  //   return $this->db->delete($this->tbl);
  // }

  public function countByGroupIdUseridsNotin($nation_code, $keyword="", $group_id, $b_user_ids){
    $this->db->select_as("COUNT($this->tbl2_as.id)", "total", 0);
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");  
    $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
    $this->db->where_as("$this->tbl_as.i_group_id",$this->db->esc($group_id));
    $this->db->where_as("$this->tbl_as.is_active",$this->db->esc(1));
    $this->db->where_as("$this->tbl_as.is_kick",$this->db->esc(0));
    $this->db->where_as("$this->tbl_as.is_accept",$this->db->esc(1));
    $this->db->where_in("b_user_id",$b_user_ids, 1);
    if (mb_strlen($keyword)>0) {
      $this->db->where_as('LOWER(CAST('.$this->__decrypt("$this->tbl2_as.band_fnama").' AS CHAR(50)))', addslashes(strtolower($keyword)), 'and', '%like%');
    }
    $d = $this->db->get_first('object', 0);
    if (isset($d->total)) {
        return $d->total;
    }
    return "0";
  }

  public function getByGroupIdUseridsNotin($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_direction="ASC", $keyword="", $group_id, $b_user_ids){
    $this->db->select_as("$this->tbl2_as.id", "b_user_id");
    $this->db->select_as($this->__decrypt("$this->tbl2_as.band_fnama"), "b_user_band_fnama");
    $this->db->select_as("COALESCE($this->tbl2_as.band_image,'media/user/default-profile-picture.png')", "b_user_band_image");
    $this->db->select_as("$this->tbl_as.is_owner", "is_owner");
    $this->db->select_as("$this->tbl_as.is_co_admin", "is_co_admin");
    $this->db->select_as("$this->tbl_as.is_accept", "is_accept");
    // $this->db->select_as("$this->tbl_as.is_request", "is_request");
    $this->db->select_as("$this->tbl_as.is_online", "is_online");
    $this->db->select_as("$this->tbl_as.is_online_status", "is_online_status");
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");  
    $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
    $this->db->where_as("$this->tbl_as.i_group_id",$this->db->esc($group_id));
    $this->db->where_as("$this->tbl_as.is_active",$this->db->esc(1));
    $this->db->where_as("$this->tbl_as.is_kick",$this->db->esc(0));
    $this->db->where_as("$this->tbl_as.is_accept",$this->db->esc(1));
    $this->db->where_in("b_user_id",$b_user_ids, 1);
    if (mb_strlen($keyword)>0) {
      $this->db->where_as('LOWER(CAST('.$this->__decrypt("$this->tbl2_as.band_fnama").' AS CHAR(50)))', addslashes(strtolower($keyword)), 'and', '%like%');
    }
    $this->db->order_by('LOWER(CAST('.$this->__decrypt("$this->tbl2_as.band_fnama").' AS CHAR(50)))', $sort_direction);
    $this->db->page($page, $page_size);
    return $this->db->get('', 0);
  }

  public function countByGroupId($nation_code, $keyword="", $group_id){
    $this->db->select_as("COUNT($this->tbl2_as.id)", "total", 0);
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");  
    $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
    $this->db->where_as("$this->tbl_as.i_group_id",$this->db->esc($group_id));
    $this->db->where_as("$this->tbl_as.is_active",$this->db->esc(1));
    $this->db->where_as("$this->tbl_as.is_kick",$this->db->esc(0));
    $this->db->where_as("$this->tbl_as.is_accept",$this->db->esc(1));
    // if (mb_strlen($keyword)>0) {
    //   $this->db->where_as('LOWER(CAST('.$this->__decrypt("$this->tbl2_as.band_fnama").' AS CHAR(50)))', addslashes(strtolower($keyword)), 'and', '%like%');
    // }
    $d = $this->db->get_first('object', 0);
    if (isset($d->total)) {
        return $d->total;
    }
    return "0";
  }

  public function getByGroupId($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_direction="ASC", $keyword="", $group_id){
    $this->db->select_as("$this->tbl2_as.id", "b_user_id");
    $this->db->select_as($this->__decrypt("$this->tbl2_as.band_fnama"), "b_user_band_fnama");
    $this->db->select_as("COALESCE($this->tbl2_as.band_image,'media/user/default-profile-picture.png')", "b_user_band_image");
    $this->db->select_as("$this->tbl_as.is_owner", "is_owner");
    $this->db->select_as("$this->tbl_as.is_co_admin", "is_co_admin");
    $this->db->select_as("$this->tbl_as.is_accept", "is_accept");
    // $this->db->select_as("$this->tbl_as.is_request", "is_request");
    $this->db->select_as("$this->tbl_as.is_online", "is_online");
    $this->db->select_as("$this->tbl_as.is_online_status", "is_online_status");
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");  
    $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
    $this->db->where_as("$this->tbl_as.i_group_id",$this->db->esc($group_id));
    $this->db->where_as("$this->tbl_as.is_active",$this->db->esc(1));
    $this->db->where_as("$this->tbl_as.is_kick",$this->db->esc(0));
    $this->db->where_as("$this->tbl_as.is_accept",$this->db->esc(1));
    if (mb_strlen($keyword)>0) {
      $this->db->where_as('LOWER(CAST('.$this->__decrypt("$this->tbl2_as.band_fnama").' AS CHAR(50)))', addslashes(strtolower($keyword)), 'and', '%like%');
    }
    $this->db->order_by('LOWER("$this->tbl_as.is_owner")'.','. 'LOWER(CAST('.$this->__decrypt("$this->tbl2_as.band_fnama").' AS CHAR(50)))', $sort_direction);
    $this->db->page($page, $page_size);
    return $this->db->get('', 0);
  }

  public function getByGroupIdParticipantId($nation_code, $group_id='0', $b_user_id, $getType="first"){
    $this->db->select_as("$this->tbl2_as.id", "b_user_id");
    $this->db->select_as($this->__decrypt("$this->tbl2_as.band_fnama"), "b_user_band_fnama");
    $this->db->select_as("COALESCE($this->tbl2_as.band_image,'media/user/default-profile-picture.png')", "b_user_band_image");
    $this->db->select_as("$this->tbl_as.i_group_id", "group_id");
    $this->db->select_as("$this->tbl_as.is_owner", "is_owner");
    $this->db->select_as("$this->tbl_as.is_co_admin", "is_co_admin");
    $this->db->select_as("$this->tbl_as.is_accept", "is_accept");
    $this->db->select_as("$this->tbl_as.is_request", "is_request");
    $this->db->select_as("$this->tbl_as.is_online", "is_online");
    $this->db->select_as("$this->tbl_as.is_online_status", "is_online_status");
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");  
    $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
    if($group_id != '0') {
      $this->db->where_as("$this->tbl_as.i_group_id",$this->db->esc($group_id));
    }
    $this->db->where_as("$this->tbl_as.b_user_id",$this->db->esc($b_user_id));
    $this->db->where_as("$this->tbl_as.is_active",$this->db->esc(1));
    $this->db->where_as("$this->tbl_as.is_kick",$this->db->esc(0));
    $this->db->where_as("$this->tbl_as.is_accept",$this->db->esc(1));
    if($getType == "first") {
      return $this->db->get_first();
    } else {
      return $this->db->get('', 0);
    }
  }

  public function getRequestTotal($nation_code, $group_id) {
    $this->db->select_as("COUNT(*)", "jumlah");
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    $this->db->where_as("$this->tbl_as.i_group_id", $this->db->esc($group_id));
    $this->db->where_as("$this->tbl_as.is_active",$this->db->esc(1));
    $this->db->where_as("$this->tbl_as.is_accept",$this->db->esc(0));
    $this->db->where_as("$this->tbl_as.is_reject",$this->db->esc(0));
    $this->db->where_as("$this->tbl_as.is_owner",$this->db->esc(0));
    $this->db->where_as("$this->tbl_as.is_co_admin",$this->db->esc(0));
    $this->db->where_as("$this->tbl_as.is_request", $this->db->esc(1));
    $d = $this->db->get_first("object", 0);
    if (isset($d->jumlah)) {
      return $d->jumlah;
    }
    return "0";
  }

  public function getRequestList($nation_code, $group_id, $sort_col="cdate", $sort_dir="DESC", $page=1, $page_size=10)
  {
    $this->db->select_as("$this->tbl2_as.id", "b_user_id");
    $this->db->select_as($this->__decrypt("$this->tbl2_as.band_fnama"), "b_user_band_fnama");
    $this->db->select_as("COALESCE($this->tbl2_as.band_image,'media/user/default-profile-picture.png')", "b_user_band_image");
    $this->db->select_as("$this->tbl_as.cdate", "cdate");
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");  
    $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
    $this->db->where_as("$this->tbl_as.i_group_id",$this->db->esc($group_id));
    $this->db->where_as("$this->tbl_as.is_active",$this->db->esc(1));
    $this->db->where_as("$this->tbl_as.is_accept",$this->db->esc(0));
    $this->db->where_as("$this->tbl_as.is_reject",$this->db->esc(0));
    $this->db->where_as("$this->tbl_as.is_owner",$this->db->esc(0));
    $this->db->where_as("$this->tbl_as.is_co_admin",$this->db->esc(0));
    $this->db->where_as("$this->tbl_as.is_request", $this->db->esc(1));
    $this->db->order_by("LOWER(".$sort_col.")", $sort_dir);
    $this->db->page($page, $page_size);
    return $this->db->get('', 0);
  }

  public function setStatusMember($nation_code, $group_id, $b_user_id, $action)
  {
    $this->db->where("nation_code", $nation_code);
    $this->db->where("i_group_id", $group_id);
    $this->db->where("b_user_id", $b_user_id);

    if($action == "accept") {
      $du = array('is_accept' => 1, 'is_reject' => 0, 'is_request' => 0);
    } else if($action == "reject") {
      $du = array('is_accept' => 0, 'is_reject' => 1, 'is_request' => 0);
    }

    return $this->db->update($this->tbl, $du);
  }

  public function checkIfUserExistInGroup($nation_code, $group_id, $b_user_id)
  {
    $this->db->select_as("COUNT(*)", "jumlah");
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    $this->db->where_as("$this->tbl_as.i_group_id", $this->db->esc($group_id));
    $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    $this->db->where_as("$this->tbl_as.is_accept", $this->db->esc(0));
    $d = $this->db->get_first("object", 0);
    if (isset($d->jumlah)) {
      return $d->jumlah;
    }
    return 0;
  }

  public function getRequestJoinList($nation_code, $b_user_id)
  {
    $this->db->select_as("$this->tbl3_as.id", "group_id");
    $this->db->select_as("$this->tbl3_as.name", "group_name");
    $this->db->select_as("COALESCE($this->tbl3_as.image,'media/user/default-profile-picture.png')", "group_image");
    $this->db->select_as("COALESCE($this->tbl3_as.image_thumb,'media/user/default-profile-picture.png')", "group_image_thumb");
    $this->db->select_as("$this->tbl_as.cdate", "cdate");
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
    $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    $this->db->where_as("$this->tbl_as.is_request", $this->db->esc(1));
    return $this->db->get('', 0);
  }

  public function setCancelJoinRequest($nation_code, $group_id, $b_user_id)
  {
    $this->db->where("nation_code", $nation_code);
    $this->db->where("i_group_id", $group_id);
    $this->db->where("b_user_id", $b_user_id);

    $du = array('is_request' => 0, 'is_accept' => 0);

    return $this->db->update($this->tbl, $du);
  }

  public function checkIfUserAlreadyRequestJoin($nation_code, $group_id, $b_user_id)
  {
    $this->db->select_as("COUNT(*)", "jumlah");
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    $this->db->where_as("$this->tbl_as.i_group_id", $this->db->esc($group_id));
    $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    $this->db->where_as("$this->tbl_as.is_request", $this->db->esc(1));
    $d = $this->db->get_first("object", 0);
    if (isset($d->jumlah)) {
      return $d->jumlah;
    }
    return 0;
  }

  public function checkIfUserAlreadyAcceptByAdmin($nation_code, $group_id, $b_user_id)
  {
    $this->db->select_as("COUNT(*)", "jumlah");
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    $this->db->where_as("$this->tbl_as.i_group_id", $this->db->esc($group_id));
    $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    $this->db->where_as("$this->tbl_as.is_accept", $this->db->esc(1));
    $d = $this->db->get_first("object", 0);
    if (isset($d->jumlah)) {
      return $d->jumlah;
    }
    return 0;
  }

  public function checkTotalParticipant($nation_code, $group_id) {
    $this->db->select_as("COUNT(*)", "jumlah");
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    $this->db->where_as("$this->tbl_as.i_group_id", $this->db->esc($group_id));
    $this->db->where_as("$this->tbl_as.is_accept", $this->db->esc(1));
    $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    $this->db->where_as("$this->tbl_as.is_owner", $this->db->esc(0));
    $this->db->where_as("$this->tbl_as.is_request", $this->db->esc(0));
    $d = $this->db->get_first("object", 0);
    if (isset($d->jumlah)) {
      return $d->jumlah;
    }
    return 0;
  }

  public function getAllParticipantByGroupId($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_direction="ASC", $keyword="", $group_id, $param){
    $this->db->select_as("$this->tbl2_as.id", "b_user_id");
    $this->db->select_as($this->__decrypt("$this->tbl2_as.band_fnama"), "b_user_band_fnama");
    $this->db->select_as("COALESCE($this->tbl2_as.band_image,'media/user/default-profile-picture.png')", "b_user_band_image");
    if($param == "manager" || $param == "coadmin") {
      $this->db->select_as("IF($this->tbl_as.is_owner = '1' AND $this->tbl_as.is_co_admin = '0', 'Owner', 
        IF($this->tbl_as.is_owner = '0' AND $this->tbl_as.is_co_admin = '1', 'Admin', 
        'Member')
      )", "status");
    } else if($param == "member") {
      $this->db->select_as("'Member'", "status");
    }

    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");  
    $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code), "AND", "=", 0, 0);
    $this->db->where_as("$this->tbl_as.i_group_id",$this->db->esc($group_id), "AND", "=", 0, 0);
    $this->db->where_as("$this->tbl_as.is_active",$this->db->esc(1), "AND", "=", 0, 0);
    $this->db->where_as("$this->tbl_as.is_accept",$this->db->esc(1));
    $this->db->where_as("$this->tbl_as.is_kick",$this->db->esc(0), "AND", "=", 0, 0);
    $this->db->where_as("$this->tbl_as.is_request",$this->db->esc(0), "AND", "=", 0, 0);
    if($param == "manager") {
      $this->db->where_as("$this->tbl_as.is_owner",$this->db->esc(1), "OR", "=", 1, 0);
      $this->db->where_as("$this->tbl_as.is_co_admin",$this->db->esc(1), "AND", "=", 0, 1);
    } else if($param == "member") {
      $this->db->where_as("$this->tbl_as.is_owner",$this->db->esc(0));
      $this->db->where_as("$this->tbl_as.is_co_admin",$this->db->esc(0));
    } else if($param == "coadmin") {
      $this->db->where_as("$this->tbl_as.is_owner",$this->db->esc(0));
      $this->db->where_as("$this->tbl_as.is_co_admin",$this->db->esc(1));
    }

    if (mb_strlen($keyword)>0) {
      $this->db->where_as('LOWER(CAST('.$this->__decrypt("$this->tbl2_as.band_fnama").' AS CHAR(50)))', addslashes(strtolower($keyword)), 'and', '%like%');
    }
    if($param == "manager") {
      $this->db->order_by("status". ' '. $sort_direction. ','. 'LOWER(CAST('.$this->__decrypt("$this->tbl2_as.band_fnama").' AS CHAR(50)))', $sort_direction);
    } else if($param == "member" || $param == "coadmin") {
      $this->db->order_by('LOWER(CAST('.$this->__decrypt("$this->tbl2_as.band_fnama").' AS CHAR(50)))', $sort_direction);
    } 
    
    $this->db->page($page, $page_size);
    return $this->db->get('', 0);
  }

  public function updateStatusParticipant($nation_code, $group_id='0', $is_online_status='0', $b_user_id, $param, $du)
  {
    if (!is_array($du)) {
      return 0;
    }
    $this->db->where('nation_code', $nation_code);
    $this->db->where('i_group_id', $group_id);
    if($group_id != '0') {
      $this->db->where('i_group_id', $group_id);
    }
    if($is_online_status != '0') {
      $this->db->where('is_online_status', $is_online_status);
    }
    if($param == "mass") {
      $this->db->where_in('b_user_id', $b_user_id);
    } else {
      $this->db->where('b_user_id', $b_user_id);
    }
    return $this->db->update($this->tbl, $du, 0);
  }

  public function getStatus($nation_code, $group_id, $b_user_id) {
    $this->db->select_as("$this->tbl_as.i_group_id", "i_group_id");
    $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id");
    $this->db->select_as("$this->tbl_as.is_owner", "is_owner");
    $this->db->select_as("$this->tbl_as.is_co_admin", "is_co_admin");
    $this->db->select_as("$this->tbl_as.is_accept", "is_accept");
    $this->db->select_as("$this->tbl_as.is_request", "is_request");
    $this->db->select_as("$this->tbl_as.b_user_id_inviter", "b_user_id_inviter");
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code), "AND", "=", 0, 0);
    $this->db->where_as("$this->tbl_as.i_group_id",$this->db->esc($group_id), "AND", "=", 0, 0);
    $this->db->where_as("$this->tbl_as.b_user_id",$this->db->esc($b_user_id), "AND", "=", 0, 0);
    $this->db->where_as("$this->tbl_as.is_active",$this->db->esc(1), "AND", "=", 0, 0);
    return $this->db->get_first('', 0);
  }
}
