<?php
// base from b_user_model.php
// by Donny Dennison
class F_Version_mobile_Model extends SENE_Model{
	var $tbl = 'f_version_mobile';
	var $tbl_as = 'vm';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
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

	// public function getLastId($nation_code){
	// 	$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
	// 	$this->db->from($this->tbl, $this->tbl_as);
	// 	$this->db->where("nation_code",$nation_code);
	// 	$d = $this->db->get_first('',0);
	// 	if(isset($d->last_id)) return $d->last_id;
	// 	return 0;
	// }

	// public function auth($nation_code,$username){
	// 	$this->db->select("*");
	// 	$this->db->select_as("COALESCE(`fb_id`,'-')",'fb_id',0);
	// 	$this->db->select_as("COALESCE(`apple_id`,'-')",'apple_id',0);
	// 	$this->db->select_as("COALESCE(`google_id`,'-')",'google_id',0);
	// 	$this->db->select_as("COALESCE(`api_web_token`,'-')",'api_web_token',0);
	// 	$this->db->where_as("`nation_code`",$nation_code);
	// 	$this->db->where_as("`email`",$this->db->esc($username),"OR","like",1,0);
	// 	$this->db->where_as("`telp`",$this->db->esc($username),"OR","like",0,1);
	// 	return $this->db->get_first('object',0);
	// }

	// public function checkToken($nation_code, $token, $kind="api_web"){
	// 	if(strlen($token)<=4) return false;
	// 	$dt = $this->db->where($kind.'_token',$token)->get();
	// 	if(count($dt)>1){
	// 		foreach($dt as $d){
	// 			$this->setToken($nation_code, $d->id, "NULL", $kind);
	// 		}
	// 		return false;
	// 	}else if(count($dt)==1){
	// 		return true;
	// 	}else{
	// 		return false;
	// 	}
	// }

	// public function setToken($nation_code,$id,$token,$kind="api_web"){
	// 	$this->db->where("nation_code",$nation_code)->where("id",$id);
	// 	$du = array($kind.'_token'=>$token);
	// 	return $this->db->update($this->tbl,$du);
	// }

	// public function getByToken($nation_code, $token, $kind="api_web"){
	// 	if(strlen($token)<=4) return new stdClass();
	// 	$this->db->select_as("$this->tbl_as.*, $this->tbl_as.id","id",0);
	// 	$this->db->select_as("COALESCE($this->tbl_as.fb_id,'-')","fb_id",0);
	// 	$this->db->select_as("COALESCE($this->tbl_as.apple_id,'-')","apple_id",0);
	// 	$this->db->select_as("COALESCE($this->tbl_as.google_id,'-')","google_id",0);
	// 	$this->db->from($this->tbl, $this->tbl_as);
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where($kind.'_token',$token);
	// 	return $this->db->get_first('object',0);
	// }

	// public function setAgree($id){
	// 	$du = array('is_agree'=>'1');
	// 	return $this->db->where("id",$id)->update($this->tbl,$du);
	// }

	// public function register($di=array()){
	// 	$this->db->flushQuery();
	// 	return $this->db->insert($this->tbl,$di,0,0);
	// }

	// public function update($nation_code, $id, $du){
	// 	if(!is_array($du)) return 0;
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where("id",$id);
 //    return $this->db->update($this->tbl,$du,0);
	// }

	// public function getByEmail($nation_code, $email){
	// 	$this->db->select_as("$this->tbl_as.*, $this->tbl_as.id","id",0);
	// 	$this->db->select_as("COALESCE($this->tbl_as.fb_id,'-')","fb_id",0);
	// 	$this->db->select_as("COALESCE($this->tbl_as.apple_id,'-')","apple_id",0);
	// 	$this->db->select_as("COALESCE($this->tbl_as.google_id,'-')","google_id",0);
	// 	$this->db->from($this->tbl, $this->tbl_as);
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where("email",$email);
	// 	return $this->db->get_first();
	// }

	// public function getById($nation_code, $id){
	// 	$this->db->select_as("$this->tbl_as.*, $this->tbl_as.id","id",0);
	// 	$this->db->select_as("COALESCE($this->tbl_as.fb_id,'-')","fb_id",0);
	// 	$this->db->select_as("COALESCE($this->tbl_as.google_id,'-')","google_id",0);
	// 	$this->db->from($this->tbl, $this->tbl_as);
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where("id",$id);
	// 	return $this->db->get_first();
	// }

	// public function getByEmailAndSocialID($email,$social_id){
	// 	$this->db->where("email",$email);
	// 	$this->db->where("fb_id",$social_id,'or','like',1,0);
	// 	$this->db->where("google_id",$social_id,'or','like',0,1);
	// 	$d = $this->db->get_first();
	// 	if(isset($d->id)) return $d;
	// 	return new stdClass();
	// }

	// public function getKode($a_company_inisial,$a_company_id="",$fnama=""){
	// 	$a_company_inisial = strtoupper($a_company_inisial);
	// 	$kode = $a_company_inisial;
	// 	if(strlen($fnama)>0){
	// 		$fnama = strtoupper($fnama);
	// 		$kode = $a_company_inisial.''.$fnama[0];
	// 	}
	// 	$this->db->flushQuery();
	// 	$this->db->select_as('COUNT(*) total, CAST(COALESCE(SUBSTRING(kode,4),0) AS UNSIGNED)+1','urutan',0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where('kode',$kode,'and','like%');
	// 	$this->db->order_by('CAST(COALESCE(SUBSTRING(kode,4),0) AS UNSIGNED)','desc');
	// 	if(strlen($a_company_id)>0){
	// 		if(strtolower($a_company_id)=='null'){
	// 			$this->db->where_as('COALESCE(a_company_id,"-")',$this->db->esc('-'),'and','=');
	// 		}else{
	// 			$this->db->where('a_company_id',$a_company_id,'and','=');
	// 		}
	// 	}
	// 	return $this->db->get_first('object',0);
	// }

	// public function getKodeOnline($fnama_inisial){
	// 	$this->db->flushQuery();
	// 	$this->db->select_as('CAST(SUBSTRING(kode,3) AS UNSIGNED)+1','urutan',0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where('kode',$fnama_inisial,'and','like%');
	// 	$this->db->order_by('CAST(SUBSTRING(kode,3) AS UNSIGNED)+1','desc');
	// 	return $this->db->get_first('object',0);
	// }

	// public function flushFcm($fcm_token=""){
	// 	if(strlen($fcm_token)>50){
	// 		$sql = 'UPDATE `'.$this->tbl.'` SET fcm_token = "" WHERE fcm_token LIKE "'.$fcm_token.'"';
	// 		$this->db->exec($sql);
	// 	}
	// }

	// public function auth_sosmed($nation_code,$fb_id,$google_id,$apple_id,$email,$telp){
	// 	$this->db->select("*");
	// 	$this->db->select_as("COALESCE(`api_web_token`,'-')",'api_web_token',0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code),'AND','LIKE',0,0);
	// 	$this->db->where_as("$this->tbl_as.fb_id",$this->db->esc($fb_id),'OR','LIKE',1,0);
	// 	$this->db->where_as("$this->tbl_as.apple_id",$this->db->esc($apple_id),'AND','LIKE',0,0);
	// 	$this->db->where_as("$this->tbl_as.google_id",$this->db->esc($google_id),'AND','LIKE',0,1);
	// 	$this->db->where_as("$this->tbl_as.email",$this->db->esc($email),'OR','LIKE',1,0);
	// 	$this->db->where_as("$this->tbl_as.telp",$this->db->esc($telp),'AND','LIKE',0,1);
	// 	$this->db->order_by("id","ASC");
	// 	return $this->db->get_first('',0);
	// }

	// public function checkEmail($nation_code,$email){
	// 	$this->db->select_as("*,COALESCE(google_id,'NULL')","google_id",0);
	// 	$this->db->select_as("COALESCE(fb_id,'NULL')","fb_id",0);
	// 	$this->db->select_as("COALESCE(apple_id,'NULL')","apple_id",0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where_as("nation_code",$this->db->esc($nation_code),"AND","LIKE");
	// 	$this->db->where_as("email",$this->db->esc($email),"AND","LIKE");
	// 	$this->db->order_by("id","asc");
	// 	return $this->db->get_first('',0);
	// }

	// public function checkTelp($nation_code,$telp){
	// 	$this->db->select_as("*,COALESCE(google_id,'NULL')","google_id",0);
	// 	$this->db->select_as("COALESCE(fb_id,'NULL')","fb_id",0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where_as("nation_code",$this->db->esc($nation_code),"AND","LIKE");
	// 	$this->db->where_as("telp",$this->db->esc($telp),"AND","LIKE");
	// 	$this->db->order_by("id","asc");
	// 	return $this->db->get_first('',0);
	// }

	// public function checkEmailTelp($nation_code,$email,$telp){
	// 	$this->db->select_as("*,COALESCE(google_id,'NULL')","google_id",0);
	// 	$this->db->select_as("COALESCE(fb_id,'NULL')","fb_id",0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where_as("nation_code",$this->db->esc($nation_code),"AND","LIKE");
	// 	$this->db->where_as("email",$this->db->esc($email),'AND','LIKE',1,0);
	// 	$this->db->where_as("telp",$this->db->esc($telp),'AND','LIKE',0,1);
	// 	$this->db->order_by("id","asc");
	// 	return $this->db->get_first('',0);
	// }

	// public function checkFBID($nation_code,$fb_id){
	// 	$this->db->select_as("*,COALESCE(google_id,'NULL')","google_id",0);
	// 	$this->db->select_as("COALESCE(fb_id,'NULL')","fb_id",0);
	// 	$this->db->select_as("COALESCE(apple_id,'NULL')","apple_id",0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where_as("nation_code",$this->db->esc($nation_code),"AND","LIKE");
	// 	$this->db->where_as("COALESCE(fb_id,'-')",$this->db->esc($fb_id),'AND','LIKE',0,0);
	// 	$this->db->order_by("id","asc");
	// 	return $this->db->get_first('',0);
	// }

	// public function checkAppleID($nation_code,$apple_id){
	// 	$this->db->select_as("*,COALESCE(google_id,'NULL')","google_id",0);
	// 	$this->db->select_as("COALESCE(fb_id,'NULL')","fb_id",0);
	// 	$this->db->select_as("COALESCE(apple_id,'NULL')","apple_id",0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where_as("nation_code",$this->db->esc($nation_code),"AND","LIKE");
	// 	$this->db->where_as("COALESCE(apple_id,'-')",$this->db->esc($apple_id),'AND','LIKE',0,0);
	// 	$this->db->order_by("id","asc");
	// 	return $this->db->get_first('',0);
	// }

	// public function checkGoogleID($nation_code,$google_id){
	// 	$this->db->select_as("*,COALESCE(google_id,'NULL')","google_id",0);
	// 	$this->db->select_as("COALESCE(fb_id,'NULL')","fb_id",0);
	// 	$this->db->select_as("COALESCE(apple_id,'NULL')","apple_id",0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where_as("nation_code",$this->db->esc($nation_code),"AND","LIKE");
	// 	$this->db->where_as("COALESCE(google_id,'-')",$this->db->esc($google_id),'AND','LIKE',0,0);
	// 	$this->db->order_by("id","asc");
	// 	return $this->db->get_first('',0);
	// }

	// public function detail($nation_code,$id){
	// 	$this->db->select_as("$this->tbl_as.id","id",0);
	// 	$this->db->select_as("$this->tbl_as.id","b_user_id",0);
	// 	$this->db->select_as("$this->tbl_as.id","b_user_id_seller",0);
	// 	$this->db->select_as("$this->tbl_as.fnama","fnama",0);
	// 	$this->db->select_as("$this->tbl_as.image","image",0);
	// 	$this->db->select_as("'0'","rating",0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code),"AND","=");
	// 	$this->db->where_as("$this->tbl_as.id",$this->db->esc($id),"AND","=");
	// 	return $this->db->get_first('',0);
	// }
	// public function flushFcmToken($fcm_token_old){
	// 	$du = array("fcm_token"=>'');
	// 	$this->db->where("fcm_token",$fcm_token_old,'AND','like%');
	// 	return $this->db->update($this->tbl,$du,0);
	// }

	public function compareMobileVersion($nation_code,$device, $mobile_version,$type=NULL){

		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where_as('nation_code',$nation_code);
		$this->db->where_as('is_active',1);		
		$this->db->where('device',$device);
		
		$mobile_version = str_replace('.','',$mobile_version);
		
		$this->db->where_as("REPLACE($this->tbl_as.version, '.', '')",$mobile_version,"AND",">=");
		$this->db->order_by("version","desc");

		if($type != NULL || $type != ''){

			if($type == 'minor'){
	
				$this->db->where('status',1);

			}else{

				$this->db->where('status',2);
			
			}

		}
		
		return $this->db->get('array');
	
	}

	public function getNewMobileVersion($nation_code,$device){

		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where_as('nation_code',$nation_code);
		$this->db->where_as('is_active',1);		
		$this->db->where('device',$device);
		$this->db->order_by("cdate","desc");
		
		return $this->db->get_first();
	
	}

}
