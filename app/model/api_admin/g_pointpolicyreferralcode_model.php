<?php
class G_Pointpolicyreferralcode_Model extends SENE_Model{
	var $tbl = 'common_code';
	var $tbl_as = 'cc';
	var $tbl2 = 'b_user_setting';
	var $tbl2_as = 'bus';
	var $tbl3 = 'b_user';
	var $tbl3_as = 'bu';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	private function __joinTbl2(){
		$composites = array();
		$composites[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl2_as.nation_code");
		$composites[] = $this->db->composite_create("$this->tbl_as.classified","=","$this->tbl2_as.classified");
		$composites[] = $this->db->composite_create("$this->tbl_as.code","=","$this->tbl2_as.code");
		return $composites;
	}
    
	private function __joinTbl3(){
		$composites = array();
		$composites[] = $this->db->composite_create("$this->tbl2_as.nation_code","=","$this->tbl3_as.nation_code");
		$composites[] = $this->db->composite_create("$this->tbl2_as.b_user_id","=","$this->tbl3_as.id");
		return $composites;
	}

	public function set($di){
		return $this->db->insert($this->tbl,$di);
	}

	public function update($nation_code,$id,$du){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->update($this->tbl,$du);
	}

	public function getUsersByNationCode($nation_code,$classified,$code){
		$this->db->select_as("$this->tbl3_as.nation_code","nation_code",0);
		$this->db->select_as("$this->tbl3_as.id","id",0);
		$this->db->select_as("$this->tbl3_as.device","device",0);
		$this->db->select_as("$this->tbl3_as.fcm_token","fcm_token",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl2,$this->tbl2_as,$this->__joinTbl2(),"inner");
		$this->db->join_composite($this->tbl3,$this->tbl3_as,$this->__joinTbl3(),"inner");
		$this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
		$this->db->where_as("$this->tbl_as.classified",$this->db->esc($classified));
		$this->db->where_as("$this->tbl_as.code",$this->db->esc($code));
		$this->db->where_as("$this->tbl_as.use_yn",$this->db->esc("y"));
		return $this->db->get();
	}

  	//by Donny Dennison - 19 october 2020 14:51
	//fix notif campaign still send when disable send notif
  	public function getUsersByNationCodeAndSettingValueTrue($nation_code,$classified,$code){
	    $this->db->select_as("$this->tbl3_as.nation_code","nation_code",0);
	    $this->db->select_as("$this->tbl3_as.id","id",0);
	    $this->db->select_as("$this->tbl3_as.device","device",0);
	    $this->db->select_as("$this->tbl3_as.fcm_token","fcm_token",0);
	    $this->db->from($this->tbl,$this->tbl_as);
	    $this->db->join_composite($this->tbl2,$this->tbl2_as,$this->__joinTbl2(),"inner");
	    $this->db->join_composite($this->tbl3,$this->tbl3_as,$this->__joinTbl3(),"inner");
	    $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
	    $this->db->where_as("$this->tbl_as.classified",$this->db->esc($classified));
	    $this->db->where_as("$this->tbl_as.code",$this->db->esc($code));
	    $this->db->where_as("$this->tbl_as.use_yn",$this->db->esc("y"));
	    $this->db->where_as("$this->tbl2_as.setting_value",$this->db->esc(1));
	    return $this->db->get();
  	}

	public function getByClassifiedByCodeName($nation_code,$classified,$codename){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("classified",$classified);
		$this->db->where("codename",$codename);
		$this->db->where("use_yn","y");
		$this->db->order_by("id",'asc');
		return $this->db->get_first();
	}

    public function getByClassified($nation_code, $classified){
        $this->db->from($this->tbl,$this->tbl_as);
        $this->db->where("nation_code",$nation_code);
        $this->db->where("classified",$classified);
        $this->db->where("use_yn","y");
        return $this->db->get();
    }

	public function getByClassifiedAndCode($nation_code,$classified,$code){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("classified",$classified);
		$this->db->where("code",$code);
		$this->db->where("use_yn","y");
		$this->db->order_by("id",'asc');
		return $this->db->get_first();
	}

}