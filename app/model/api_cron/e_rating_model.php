<?php
class E_Rating_model extends JI_Model{
	var $tbl = 'e_rating';
	var $tbl_as = 'er1';
  var $tbl2 = 'e_rating';
  var $tbl2_as = 'er2';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getByOrderId($nation_code,$d_order_id){
		$this->db->from($this->tbl,$this->tbl_as);
    $this->db->where_as("nation_code",$this->db->esc($nation_code));
    $this->db->where_as("d_order_id",$this->db->esc($d_order_id));
    return $this->db->get();
	}
}
