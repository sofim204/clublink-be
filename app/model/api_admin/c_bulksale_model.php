<?php
class C_BulkSale_Model extends SENE_Model{
	var $tbl = 'c_bulksale';
	var $tbl_as = 'cbs';
	var $tbl2 = 'b_user';
	var $tbl2_as = 'bu';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

  private function __joinTbl2(){
    $cps = array();
    $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code","=","$this->tbl_as.nation_code");
    $cps[] = $this->db->composite_create("$this->tbl2_as.id","=","$this->tbl_as.b_user_id");
    return $cps;
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

	public function getLastId($nation_code){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function getTableAlias(){
		return $this->tbl_as;
	}
	public function getTableAlias2(){
		return $this->tbl2_as;
	}

	public function getAll($nation_code, $page=0,$pagesize=10,$sortCol="sku",$sortDir="ASC",$keyword="",$action_status="",$is_agent="",$scdate="",$ecdate="",$svdate="",$evdate=""){
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id",'id',0);
		$this->db->select_as("$this->tbl_as.cdate",'cdate',0);
		$this->db->select_as("'Guest'",'agent_status',0);
		$this->db->select_as("$this->tbl_as.description_long",'description_long',0);
		$this->db->select_as("$this->tbl_as.address2",'address2',0);
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
		if(strlen($is_agent)==1){
			if($is_agent==1){
				$this->db->where_as("CHAR_LENGTH(TRIM($this->tbl_as.company_name))",$this->db->esc("0"),"AND",">",0,0);
			}else{
				$this->db->where_as("CHAR_LENGTH(TRIM($this->tbl_as.company_name))",$this->db->esc("0"),"AND","=",0,0);
			}
		}

		if(strlen($scdate)==10 && strlen($ecdate)==10){
			$this->db->between("DATE($this->tbl_as.cdate)","DATE('$scdate')","DATE('$ecdate')");
		}else if(strlen($scdate)==10 && strlen($ecdate)!=10){
			$this->db->where_as("DATE($this->tbl_as.cdate)","DATE('$scdate')",'AND','>=');
		}else if(strlen($scdate)!=10 && strlen($ecdate)==10){
			$this->db->where_as("DATE($this->tbl_as.cdate)","DATE('$ecdate')",'AND','<=');
		}

		if(strlen($svdate)==10 && strlen($evdate)==10){
			$this->db->between("DATE(COALESCE($this->tbl_as.vdate,NOW()))","DATE('$svdate')","DATE('$evdate')");
		}else if(strlen($svdate)==10 && strlen($evdate)!=10){
			$this->db->where_as("DATE(COALESCE($this->tbl_as.vdate,NOW()))","DATE('$svdate')",'AND','>=');
		}else if(strlen($svdate)!=10 && strlen($evdate)==10){
			$this->db->where_as("DATE(COALESCE($this->tbl_as.vdate,NOW()))","DATE('$evdate')",'AND','<=');
		}

		if(mb_strlen($keyword)>0){
			$this->db->where_as("COALESCE($this->tbl2_as.fnama,'-')",addslashes($keyword),"OR","%like%",1,0);
			$this->db->where_as("$this->tbl_as.name",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.agent_name",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.agent_license",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.company_name",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.address2",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.description_long",addslashes($keyword),"OR","%like%",0,1);
		}
    $this->db->group_by("$this->tbl_as.id");
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAll($nation_code, $keyword="",$action_status="",$is_agent="",$scdate="",$ecdate="",$svdate="",$evdate=""){
		//var_dump($is_agent);
		//die($action_status);
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_as);
    $this->db->join_composite($this->tbl2,$this->tbl2_as, $this->__joinTbl2(),'left');
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
		if(strlen($action_status)>1) $this->db->where_as("$this->tbl_as.action_status",$this->db->esc($action_status),"AND","=",0,0);
		if(strlen($is_agent)==1){
			if($is_agent==1){
				$this->db->where_as("CHAR_LENGTH(TRIM($this->tbl_as.company_name))",$this->db->esc("0"),"AND",">",0,0);
			}else{
				$this->db->where_as("CHAR_LENGTH(TRIM($this->tbl_as.company_name))",$this->db->esc("0"),"AND","=",0,0);
			}
		}

		if(strlen($scdate)==10 && strlen($ecdate)==10){
			$this->db->between("DATE($this->tbl_as.cdate)","DATE('$scdate')","DATE('$ecdate')");
		}else if(strlen($scdate)==10 && strlen($ecdate)!=10){
			$this->db->where_as("DATE($this->tbl_as.cdate)","DATE('$scdate')",'AND','>=');
		}else if(strlen($scdate)!=10 && strlen($ecdate)==10){
			$this->db->where_as("DATE($this->tbl_as.cdate)","DATE('$ecdate')",'AND','<=');
		}

		if(strlen($svdate)==10 && strlen($evdate)==10){
			$this->db->between("DATE(COALESCE($this->tbl_as.vdate,NOW()))","DATE('$svdate')","DATE('$evdate')");
		}else if(strlen($svdate)==10 && strlen($evdate)!=10){
			$this->db->where_as("DATE(COALESCE($this->tbl_as.vdate,NOW()))","DATE('$svdate')",'AND','>=');
		}else if(strlen($svdate)!=10 && strlen($evdate)==10){
			$this->db->where_as("DATE(COALESCE($this->tbl_as.vdate,NOW()))","DATE('$evdate')",'AND','<=');
		}

		if(mb_strlen($keyword)>0){
			$this->db->where_as("COALESCE($this->tbl2_as.fnama,'-')",addslashes($keyword),"OR","%like%",1,0);
			$this->db->where_as("$this->tbl_as.name",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.agent_name",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.agent_license",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.company_name",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.address2",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.description_long",addslashes($keyword),"OR","%like%",0,1);
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	public function getById($nation_code, $id){
		$this->db->select_as("$this->tbl_as.*, COALESCE($this->tbl_as.vdate,'-')","vdate",0);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->get_first();
	}
	public function getOwnedById($nation_code, $b_user_id, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("b_user_id",$b_user_id);
		$this->db->where("id",$id);
		return $this->db->get_first();
	}
	public function set($di){
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl,$di,0,0);
	}
	public function update($nation_code, $id,$du){
		if(!is_array($du)) return 0;
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
    return $this->db->update($this->tbl,$du,0);
	}
	public function del($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}
	public function getByIds($nation_code, $pids=array()){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where_in('id',$pids);
		return $this->db->get();
	}
}
