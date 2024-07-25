<?php
class E_Rating_model extends SENE_Model{
	var $tbl = 'e_rating';
	var $tbl_as = 'er1';
  var $tbl2 = 'e_rating';
  var $tbl2_as = 'er2';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
  public function set($di){
    return $this->db->insert($this->tbl,$di);
  }
  public function update($nation_code,$d_order_id,$d_order_detail_id,$b_user_id_seller,$b_user_id_buyer,$du){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("d_order_id",$d_order_id);
		$this->db->where("d_order_detail_id",$d_order_detail_id);
		$this->db->where("b_user_id_seller",$b_user_id_seller);
		$this->db->where("b_user_id_buyer",$b_user_id_buyer);
    return $this->db->update($this->tbl,$du);
  }
	public function create($nation_code,$d_order_id,$d_order_detail_id,$b_user_id_seller,$b_user_id_buyer,$seller_rating=0,$buyer_rating=0){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("d_order_id",$d_order_id);
		$this->db->where("d_order_detail_id",$d_order_detail_id);
		$this->db->where("b_user_id_seller",$b_user_id_seller);
		$this->db->where("b_user_id_buyer",$b_user_id_buyer);
		$d = $this->db->get_first();
		if(isset($d->d_order_id)){
			$du = array();
			$du['seller_rating'] = $seller_rating;
			$du['buyer_rating'] = $buyer_rating;
			return $this->update($nation_code,$d_order_id,$d_order_detail_id,$b_user_id_seller,$b_user_id_buyer,$du);
		}else{
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['d_order_id'] = $d_order_id;
			$di['d_order_detail_id'] = $d_order_detail_id;
			$di['b_user_id_seller'] = $b_user_id_seller;
			$di['b_user_id_buyer'] = $b_user_id_buyer;
			$du['seller_rating'] = $seller_rating;
			$du['buyer_rating'] = $buyer_rating;
			return $this->set($di);
		}
	}
	public function check($nation_code,$d_order_id,$d_order_detail_id,$b_user_id_seller,$b_user_id_buyer){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("d_order_id",$d_order_id);
		$this->db->where("d_order_detail_id",$d_order_detail_id);
		$this->db->where("b_user_id_seller",$b_user_id_seller);
		$this->db->where("b_user_id_buyer",$b_user_id_buyer);
		return $this->db->get_first();
	}
  public function getByOrderId($nation_code,$d_order_id){
    $this->db->where_as("nation_code",$this->db->esc($nation_code));
		$this->db->where_as("d_order_id",$this->db->esc($d_order_id));
    return $this->db->get();
  }
  public function getByOrderIdBuyerId($nation_code,$d_order_id,$d_order_detail_id,$b_user_id_buyer){
    $this->db->where_as("nation_code",$this->db->esc($nation_code));
    $this->db->where_as("d_order_id",$this->db->esc($d_order_id));
    $this->db->where_as("d_order_detail_id",$this->db->esc($d_order_detail_id));
    $this->db->where_as("b_user_id_buyer",$this->db->esc($b_user_id_buyer));
    return $this->db->get_first();
  }
  public function getByOrderIdSellerId($nation_code,$d_order_id,$d_order_detail_id,$b_user_id_seller){
    $this->db->where_as("nation_code",$this->db->esc($nation_code));
    $this->db->where_as("d_order_id",$this->db->esc($d_order_id));
    $this->db->where_as("d_order_detail_id",$this->db->esc($d_order_detail_id));
    $this->db->where_as("b_user_id_seller",$this->db->esc($b_user_id_seller));
    return $this->db->get_first();
  }
	public function getSellerStats($nation_code,$b_user_id_seller){
		$this->db->select_as("COUNT(*)","count",0);
		$this->db->select_as("SUM($this->tbl_as.buyer_rating)","rating",0);
		$this->db->from($this->tbl,$this->tbl_as);
    $this->db->where_as("nation_code",$this->db->esc($nation_code));
    $this->db->where_as("b_user_id_seller",$this->db->esc($b_user_id_seller));
    $this->db->where_as("buyer_rating",$this->db->esc(0),"AND",">");
    return $this->db->get_first();
	}
	public function getBuyerStats($nation_code,$b_user_id_seller){
		$this->db->select_as("COUNT(*)","count",0);
		$this->db->select_as("SUM($this->tbl_as.seller_rating)","rating",0);
		$this->db->from($this->tbl,$this->tbl_as);
    $this->db->where_as("nation_code",$this->db->esc($nation_code));
    $this->db->where_as("b_user_id_buyer",$this->db->esc($b_user_id_seller));
    $this->db->where_as("seller_rating",$this->db->esc(0),"AND",">");
    return $this->db->get_first();
	}
}
