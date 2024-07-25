<?php
class C_Booking_Model extends SENE_Model{
	var $tbl = 'c_booking';
	var $tbl_as = 'cb';
	var $tbl2 = 'a_company';
	var $tbl2_as = 'ac';
	var $tbl3 = 'b_user';
	var $tbl3_as = 'bu';
	var $tbl4 = 'c_produk';
	var $tbl4_as = 'cp';
	var $tbl5 = 'd_order';
	var $tbl5_as = 'dor';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getAlias(){
		return $this->tbl_as;
	}
	public function getByUserId($b_user_id,$a_company_id="",$bdate="",$limit=100){
		$this->db->select_as("$this->tbl_as.id, $this->tbl2_as.id cabang_id, $this->tbl_as.b_user_id user_id, $this->tbl_as.kode, $this->tbl_as.bdate, $this->tbl2_as.nama cabang_nama, COALESCE($this->tbl4_as.nama,'-') treatment, CONCAT('".base_url()."',COALESCE($this->tbl4_as.foto,'media/upload/default.png')) treatment_image, COALESCE($this->tbl5_as.id,'-') jenis, $this->tbl_as.ustatus",'status',0);
		$this->db->from($this->tbl,$this->tbl_as);
		
		$this->db->join($this->tbl2,$this->tbl2_as,'id',$this->tbl_as,'a_company_id','left');
		$this->db->join($this->tbl4,$this->tbl4_as,'id',$this->tbl_as,'c_produk_id','left');
		$this->db->join($this->tbl5,$this->tbl5_as,'id',$this->tbl_as,'d_order_id','left');
		$this->db->where_as("$this->tbl_as.b_user_id",$this->db->esc($b_user_id));
		
		if(strlen($a_company_id)){
			$this->db->where_as("$this->tbl_as.a_company_id",$a_company_id);
		}
		if(strlen($bdate)){
			$this->db->where("$this->tbl_as.bdate",$bdate);
		}
		$this->db->order_by('id','desc');
		$this->db->limit($limit);
		return $this->db->get('object',0);
	}
	public function getByCompanyId($b_user_id,$a_company_id="",$bdate="",$limit=100){
		$this->db->select_as("$this->tbl_as.id, $this->tbl2_as.id cabang_id, $this->tbl_as.b_user_id user_id, $this->tbl_as.kode, $this->tbl_as.bdate, $this->tbl2_as.nama cabang_nama, COALESCE($this->tbl4_as.nama,'-') treatment, $this->tbl_as.ustatus",'status',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join($this->tbl2,$this->tbl2_as,'id',$this->tbl_as,'a_company_id','left');
		$this->db->join($this->tbl4,$this->tbl4_as,'id',$this->tbl_as,'c_produk_id','left');
		
		$this->db->where_as("$this->tbl_as.a_company_id",$this->db->esc($a_company_id));
		
		if(strlen($bdate)){
			$this->db->where('bdate','DATE('.$bdate.')');
		}else{
			$this->db->between('CURDATE()','DATE(bdate)','DATE(edate)');
		}
		$this->db->order_by('bdate','asc');
		$this->db->limit($limit);
		return $this->db->get('object',0);
	}
	public function set($di){
		$this->db->insert($this->tbl,$di);
		return $this->db->lastId();
	}
	public function set2($a_company_id,$b_user_id,$kode,$bdate){
		$di = array('a_company_id'=>$a_company_id,'b_user_id'=>$b_user_id,'kode'=>$kode,'bdate'=>$bdate);
		$this->db->insert($this->tbl,$di);
		return $this->db->lastId();
	}
	public function cancel($id,$b_user_id){
		$du['ustatus'] = 'cancelled';
		$this->db->where('id',$id);
		$this->db->where('b_user_id',$b_user_id);
		return $this->db->update($this->tbl,$du);
	}
	public function checkKode($kode,$b_user_id){
		$this->db->select_as('COUNT(*)','total',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where('kode',$kode);
		$this->db->where('b_user_id',$b_user_id);
		$this->db->where('ustatus',"cancelled",'AND','<>');
		$d = $this->db->get_first('',0);
		if(isset($d->total)) return $d->total;
		return 0;
	}
}