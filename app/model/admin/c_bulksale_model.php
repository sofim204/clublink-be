<?php
class C_BulkSale_Model extends SENE_Model {
	var $is_cacheable;
	var $tbl = 'c_bulksale';
	var $tbl_as = 'cbs';

	public function __construct(){
		parent::__construct();
		$this->is_cacheable = 0;
    $this->db->from($this->tbl,$this->tbl_as);
	}
	private function __joinTbl2(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl2_as.nation_code","=","$this->tbl_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl2_as.id","=","$this->tbl_as.b_user_id");
		return $cps;
	}

	private function __joinTbl3(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl3_as.nation_code","=","$this->tbl_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl3_as.id","=","$this->tbl_as.b_kategori_id");
		return $cps;
	}

	private function __joinTbl4(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl4_as.nation_code","=","$this->tbl_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl4_as.id","=","$this->tbl_as.b_kondisi_id");
		return $cps;
	}

	private function __joinTbl5(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl5_as.nation_code","=","$this->tbl_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl5_as.id","=","$this->tbl_as.b_berat_id");
		return $cps;
	}

	public function getById($nation_code, $id){
		$this->db->select_as("$this->tbl_as.*, $this->tbl_as.id","id",0);
		$this->db->select_as("COALESCE($this->tbl_as.vdate,'-')","vdate",0);
		$this->db->where('nation_code',$nation_code);
		$this->db->where('id',$id);
		return $this->db->get_first();
	}

	public function getByIds($nation_code, $pids=array()){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where('nation_code',$nation_code);
		$this->db->where_in('id',$pids);
		return $this->db->get();
	}
	public function exportXls($nation_code,$keyword="",$action_status="",$is_agent=""){
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id",'id',0);
		$this->db->select_as("$this->tbl_as.cdate",'cdate',0);
		$this->db->select_as("'Guest'",'agent_status',0);
		$this->db->select_as("$this->tbl_as.description_long",'description_long',0);
		$this->db->select_as("$this->tbl_as.address1",'address1',0);
		$this->db->select_as("$this->tbl_as.action_status",'action_status',0);
		$this->db->select_as("$this->tbl_as.vdate",'vdate',0);
		$this->db->select_as("$this->tbl_as.price",'price',0);
		$this->db->select_as("$this->tbl_as.name",'name',0);
		$this->db->select_as("COALESCE($this->tbl2_as.image,'media/user/default.png')",'b_user_image',0);
		$this->db->select_as("COALESCE($this->tbl2_as.email,'-')",'b_user_email',0);
		$this->db->select_as("COALESCE($this->tbl2_as.telp,'-')",'b_user_telp',0);
		$this->db->select_as("$this->tbl_as.is_active",'is_active',0);
		$this->db->select_as("$this->tbl_as.phone",'phone',0);
		$this->db->select_as("$this->tbl_as.agent_name",'agent_name',0);
		$this->db->select_as("$this->tbl_as.agent_license",'agent_license',0);
		$this->db->select_as("$this->tbl_as.company_name",'company_name',0);
		$this->db->select_as("$this->tbl_as.address2",'address2',0);
		$this->db->select_as("$this->tbl_as.district",'district',0);
		$this->db->select_as("$this->tbl_as.zipcode",'zipcode',0);
		$this->db->select_as("$this->tbl_as.ldate",'ldate',0);
		$this->db->select_as("$this->tbl_as.thumb",'thumb',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl2,$this->tbl2_as, $this->__joinTbl2(),'left');
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
		if(strlen($action_status)>1) $this->db->where_as("$this->tbl_as.action_status",$this->db->esc($action_status),"AND","=",0,0);
		if(strlen($is_agent)==1) $this->db->where_as("$this->tbl_as.is_agent",$this->db->esc($is_agent),"AND","=",0,0);
		if(strlen($keyword)>0){
			$this->db->where_as("COALESCE($this->tbl2_as.fnama,'-')",addslashes($keyword),"OR","%like%",1,0);
			$this->db->where_as("$this->tbl_as.name",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.agent_name",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.agent_license",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.company_name",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.address1",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.description_long",addslashes($keyword),"OR","%like%",0,1);
		}
    $this->db->group_by("$this->tbl_as.id");
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
}
