<?php
class E_Chat_Attachment_Model extends SENE_Model{
	var $tbl = 'e_chat_attachment';
	var $tbl_as = 'eca';
	var $tbl2 = 'e_chat';
	var $tbl2_as = 'ec';

	public function getTableAlias(){
		return $this->tbl_as;
	}

	public function getTableAlias2(){
		return $this->tbl2_as;
	}

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.e_chat_id", "=", "$this->tbl2_as.id");
        return $cps;
    }

	public function getByChatId($nation_code,$d_order_id,$c_produk_id){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
		$this->db->where_as("$this->tbl_as.d_order_id",$this->db->esc($d_order_id));
		$this->db->where_as("$this->tbl_as.c_produk_id",$this->db->esc($c_produk_id));
		$this->db->order_by("$this->tbl_as.id",'asc');
		return $this->db->get("object",0);
	}

	public function getByChatSeller($nation_code,$d_order_id,$c_produk_id,$seller_id){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
		$this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
		$this->db->where_as("$this->tbl_as.d_order_id",$this->db->esc($d_order_id));
		$this->db->where_as("$this->tbl_as.c_produk_id",$this->db->esc($c_produk_id));
		$this->db->where_as("$this->tbl2_as.b_user_id",$this->db->esc($seller_id));
		$this->db->order_by("$this->tbl_as.id",'asc');
		return $this->db->get("object",0);
	}

	public function getByChatBuyer($nation_code,$d_order_id,$c_produk_id,$buyer_id){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
		$this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
		$this->db->where_as("$this->tbl_as.d_order_id",$this->db->esc($d_order_id));
		$this->db->where_as("$this->tbl_as.c_produk_id",$this->db->esc($c_produk_id));
		$this->db->where_as("$this->tbl2_as.b_user_id",$this->db->esc($buyer_id));
		$this->db->order_by("$this->tbl_as.id",'asc');
		return $this->db->get("object",0);
	}

}
