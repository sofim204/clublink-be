<?php
class D_Cart_Model extends JI_Model{
	var $tbl = 'd_cart';
	var $tbl_as = 'dc';
	var $tbl2 = 'b_user';
	var $tbl2_as = 'bu';
	var $tbl3 = 'c_produk';
	var $tbl3_as = 'cp';
	var $tbl4 = 'b_kategori';
	var $tbl4_as = 'bk';
	var $tbl5 = 'b_berat';
	var $tbl5_as = 'bbi';
	var $tbl6 = 'b_kondisi';
	var $tbl6_as = 'bki';
	var $tbl7 = 'b_user';
	var $tbl7_as = 'bu2';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	private function __joinTbl2(){
		$cps   = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl2_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl_as.b_user_id","=","$this->tbl2_as.id");
		return $cps;
	}

	private function __joinTbl3(){
		$cps   = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl3_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl_as.c_produk_id","=","$this->tbl3_as.id");
		return $cps;
	}

	private function __joinTbl4(){
		$cps   = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl4_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl3_as.b_kategori_id","=","$this->tbl4_as.id");
		return $cps;
	}

	private function __joinTbl5(){
		$cps   = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl5_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl3_as.b_berat_id","=","$this->tbl5_as.id");
		return $cps;
	}

	private function __joinTbl6(){
		$cps   = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl6_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl3_as.b_kondisi_id","=","$this->tbl6_as.id");
		return $cps;
	}


	private function __joinTbl7(){
		$cps   = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl7_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl3_as.b_user_id","=","$this->tbl7_as.id");
		return $cps;
	}
  public function getTableAlias(){
    return $this->tbl_as;
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

	public function getLastId($nation_code,$c_produk_id,$b_user_id){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("b_user_id",$b_user_id);
		$this->db->where("c_produk_id",$c_produk_id);
		$this->db->where("nation_code",$nation_code);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function set($di=array()){
		$this->db->flushQuery();
		return $this->db->insert($this->tbl,$di,0,0);
	}

	public function update($nation_code, $b_user_id, $c_produk_id, $du){
		if(!is_array($du)) return 0;
		$this->db->where("nation_code",$nation_code);
		$this->db->where("b_user_id",$b_user_id);
		$this->db->where("c_produk_id",$c_produk_id);
    return $this->db->update($this->tbl,$du,0);
	}

	public function updateQty($nation_code,$b_user_id,$c_produk_id,$qty){
		$qty = (int) $qty;
		$sql = "UPDATE `$this->tbl` SET `qty` = (`qty`+$qty) WHERE `nation_code` = ".$this->db->esc($nation_code)." AND `b_user_id` = ".$this->db->esc($b_user_id)." AND `c_produk_id` = ".$this->db->esc($c_produk_id)."";
		return $this->db->exec($sql);
	}

	public function del($nation_code, $b_user_id, $c_produk_id){
	   $this->db->where("nation_code",$nation_code);
    $this->db->where("c_produk_id",$c_produk_id);
		$this->db->where("b_user_id",$b_user_id);
		return $this->db->delete($this->tbl);
	}
  public function getInCart(){
    $this->db->select_as("$this->tbl2_as.nation_code","nation_code");
    $this->db->select_as("$this->tbl2_as.id","b_user_id_buyer");
    $this->db->select_as("$this->tbl2_as.fnama","b_user_fnama_buyer");
    $this->db->select_as("$this->tbl2_as.image","b_user_image_buyer");
    $this->db->select_as("$this->tbl2_as.device","b_user_device_buyer");
    $this->db->select_as("$this->tbl2_as.fcm_token","b_user_fcm_token_buyer");
    $this->db->select_as("COUNT(*)","total");
    $this->db->from($this->tbl,$this->tbl_as);
    $this->db->join_composite($this->tbl2,$this->tbl2_as,$this->__joinTbl2(),"inner");
    $this->db->group_by("CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.b_user_id)");
    return $this->db->get('',0);
  }
	public function check(int $nation_code,int $b_user_id,int $c_produk_id){
    $this->db->select_as('COUNT(*)','total');
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where('nation_code',$nation_code);
		$this->db->where('b_user_id',$b_user_id);
		$this->db->where('c_produk_id',$c_produk_id);
		$d = $this->db->get_first();
		if(isset($d->total)) return (int) $d->total;
		return 0;
	}
}
