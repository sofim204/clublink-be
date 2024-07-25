<?php
class E_Rating_Model extends SENE_Model {
  var $tbl  = 'e_rating';
  var $tbl_as = 'er';

  public function __construct(){
    parent::__construct();
    $this->db->from($this->tbl, $this->tbl_as);
  }
  public function getByOrderId($nation_code,$d_order_id){
    $this->db->where_as("nation_code",$this->db->esc($nation_code));
    $this->db->where_as("d_order_id",$this->db->esc($d_order_id));
    return $this->db->get();
  }
  public function getByOrderDetailId($nation_code,$d_order_id,$d_order_detail_id){
    $this->db->where_as("nation_code",$this->db->esc($nation_code));
    $this->db->where_as("d_order_id",$this->db->esc($d_order_id));
    $this->db->where_as("d_order_detail_id",$this->db->esc($d_order_detail_id));
    return $this->db->get_first('',0);
  }
  public function getDetailByID($nation_code,$d_order_id,$b_user_id_buyer,$b_user_id_seller){
    $this->db->where_as("nation_code",$this->db->esc($nation_code));
    $this->db->where_as("d_order_id",$this->db->esc($d_order_id));
    $this->db->where_as("b_user_id_buyer",$this->db->esc($b_user_id_buyer));
    $this->db->where_as("b_user_id_seller",$this->db->esc($b_user_id_seller));
    return $this->db->get_first();
  }
  public function getSellerStats($nation_code,$b_user_id_seller){
    $this->db->select_as("COUNT(*)","rating_count",0);
    $this->db->select_as("SUM($this->tbl_as.seller_rating)","rating_total",0);
    $this->db->select_as("FLOOR(SUM($this->tbl_as.seller_rating)/COUNT(*))","rating_rate",0);
    $this->db->from($this->tbl,$this->tbl_as);
    $this->db->where_as("nation_code",$this->db->esc($nation_code));
    $this->db->where_as("b_user_id_buyer",$this->db->esc($b_user_id_seller));
    $this->db->where_as("seller_rating",$this->db->esc(0),"AND",">");
    return $this->db->get_first();
  }
  public function getBuyerStats($nation_code,$b_user_id_buyer){
    $this->db->select_as("COUNT(*)","rating_count",0);
    $this->db->select_as("SUM($this->tbl_as.buyer_rating)","rating_total",0);
    $this->db->select_as("FLOOR(SUM($this->tbl_as.buyer_rating)/COUNT(*))","rating_rate",0);
    $this->db->from($this->tbl,$this->tbl_as);
    $this->db->where_as("nation_code",$this->db->esc($nation_code));
    $this->db->where_as("b_user_id_seller",$this->db->esc($b_user_id_buyer));
    $this->db->where_as("buyer_rating",$this->db->esc(0),"AND",">");
    return $this->db->get_first();
  }
}
