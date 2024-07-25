<?php
class D_Order_Model extends JI_Model{
	var $tbl = 'd_order';
	var $tbl_as = 'd';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	
	public function getPaymentUnconfirmed(){
		$this->db->where("order_status","payment_verification");
		$this->db->where("payment_confirmed","0");
		$this->db->where("payment_status","paid");
		return $this->db->get();
	}
	public function update($nation_code,$id,$du){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->update($this->tbl,$du,0);
	}
	public function setExpired(){
		$sql = "UPDATE `$this->tbl` SET `order_status` = 'cancelled' WHERE `order_status` = 'pending' AND ABS(TIMESTAMPDIFF(HOUR,`ldate`,NOW()))>=12  ";
		return $this->db->exec($sql);
	}
	public function searchInv($inv){
		$this->db->where_as("invoice_code",$this->db->esc($inv));
		return $this->db->get_first();
	}
	public function getEmptyCardOrigin(){
		$this->db->select_as("nation_code,id,payment_response",'payment_response',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where_as("$this->tbl_as.payment_confirmed",$this->db->esc("1"),"AND",'LIKE');
		$this->db->where_as("$this->tbl_as.payment_gateway",$this->db->esc("2c2p"),"AND",'LIKE');
		$this->db->where_as("$this->tbl_as.payment_card_origin",$this->db->esc(""),"OR",'LIKE',1,0);
		$this->db->where_as("$this->tbl_as.payment_card_origin",$this->db->esc("-"),"OR",'LIKE',0,1);
		$this->db->order_by("$this->tbl_as.id","asc");
		return $this->db->get('',0);
	}
	public function del($nation_code,$d_order_id){
    $this->db->where("nation_code",$nation_code);
		$this->db->where("id",$d_order_id);
		return $this->db->delete($this->tbl);
	}

	// //by Donny Dennison - 3 november 2020 10:37
 //    //add flag start countdown payment
 //    public function getBuyerPendingCountDown()
 //    {
 //        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
 //        $this->db->select_as("$this->tbl_as.id", "d_order_id", 0);
 //        $this->db->from($this->tbl, $this->tbl_as);
 //        $this->db->where_as("$this->tbl_as.payment_status", $this->db->esc("pending"), "AND", "=");
 //        $this->db->where_as("$this->tbl_as.order_status", $this->db->esc("waiting_for_payment"), "AND", "=");
 //        $this->db->where_as("$this->tbl_as.is_countdown", $this->db->esc(0), "AND", "=");

 //        $this->db->order_by("$this->tbl_as.cdate", "asc");
 //        return $this->db->get('', 0);
 //    }

}
