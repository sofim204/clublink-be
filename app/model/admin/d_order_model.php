<?php
class D_Order_Model extends SENE_Model {
	var $tbl = 'd_order';
	var $tbl_as = 'dor';
	var $tbl2 = 'd_order_detail';
	var $tbl2_as = 'dod';
	var $tbl3 = 'c_produk';
	var $tbl3_as = 'cp';
	var $tbl4 = 'b_user';
	var $tbl4_as = 'bu';
	var $tbl5 = 'a_pengguna';
	var $tbl5_as = 'ap';
	var $tbl6 = 'b_kategori';
	var $tbl6_as = 'bk';
	var $tbl7 = 'b_user_alamat';
	var $tbl7_as = 'bum';
	var $tbl8 = 'd_order_pembayaran';
	var $tbl8_as = 'dop';
	var $tbl9 = 'd_order_proses';
	var $tbl9_as = 'dopr';

	public function __construct(){
		parent::__construct();
		$this->is_cacheable = 0;
    $this->db->from($this->tbl,$this->tbl_as);
	}

	private function __joinTbl2(){
		$cbs = array();
		$cbs[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl2_as.nation_code");
		$cbs[] = $this->db->composite_create("$this->tbl_as.id","=","$this->tbl2_as.d_order_id");
		return $cbs;
	}

	private function __joinTbl4(){
		$composites = array();
		$composites[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl4_as.nation_code");
		$composites[] = $this->db->composite_create("$this->tbl_as.b_user_id","=","$this->tbl4_as.id");
		return $composites;
	}
	private function __joinTbl7(){
		$composites = array();
		$composites[] = $this->db->composite_create("$this->tbl4_as.nation_code","=","$this->tbl7_as.nation_code");
		$composites[] = $this->db->composite_create("$this->tbl4_as.id","=","$this->tbl7_as.id");
		return $composites;
	}
	private function __joinTbl8(){
		$composites = array();
		$composites[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl8_as.nation_code");
		$composites[] = $this->db->composite_create("$this->tbl_as.id","=","$this->tbl8_as.d_order_id");
		return $composites;
	}
	private function __joinTbl9(){
		$composites = array();
		$composites[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl9_as.nation_code");
		$composites[] = $this->db->composite_create("$this->tbl_as.id","=","$this->tbl9_as.d_order_id");
		return $composites;
	}

	public function getById($nation_code,$id){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("$this->tbl_as.nation_code",$nation_code);
		$this->db->where("$this->tbl_as.id",$id);
		return $this->db->get_first();
	}

	public function update($nation_code,$id,$du=array()){
		if(!is_array($du)) return 0;
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
    return $this->db->update($this->tbl,$du,0);
	}
}
