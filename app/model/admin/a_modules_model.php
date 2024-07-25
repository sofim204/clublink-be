<?php
//admin
class A_Modules_Model extends SENE_Model{
	var $tbl = 'a_modules';
	var $tbl_alias = 'bmod';
	var $tbl_as = 'bmod';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	public function getAll($page=0,$pagesize=10,$sortCol="identifier",$sortDir="ASC",$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as('identifier,name,path,level,is_visible','is_visible',0);
		$this->db->from($this->tbl,$this->tbl_as);
		if(strlen($keyword)>1){
			$this->db->where("name",$keyword,"OR","%like%",1,0);
			$this->db->where("path",$keyword,"OR","%like%",0,0);
			$this->db->where("identifier",$keyword,"OR","%like%",0,1);
		}
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAll($keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		if(strlen($keyword)>1){
			$this->db->where("name",$keyword,"OR","%like%",1,0);
			$this->db->where("path",$keyword,"OR","%like%",0,0);
			$this->db->where("identifier",$keyword,"OR","%like%",0,1);
		}
		$d = $this->db->from($this->tbl)->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
  public function getAllDs(){
    $sql="SELECT * FROM `$this->tbl` WHERE `is_visible` = 1 ORDER BY priority ASC, `has_submenu` ASC";
    return $this->select($sql);
  }
  public function getAllParent($nation_code){
		$this->db->select();
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where_as("nation_code",$this->db->esc($nation_code));
		$this->db->where_as("COALESCE(children_identifier,'XXX')",$this->db->esc("XXX"));
		$this->db->where_as("is_visible",1);
		$this->db->order_by("priority","asc");
		$this->db->order_by("has_submenu","asc");
    return $this->db->get();
  }
  public function getChild($nation_code,$children_identifier){
		$this->db->select();
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where_as("nation_code",$this->db->esc($nation_code));
		$this->db->where_as("COALESCE(children_identifier,'-')",$this->db->esc($children_identifier));
		$this->db->where_as("is_visible",1);
		$this->db->order_by("priority","asc");
		$this->db->order_by("has_submenu","asc");
    return $this->db->get();
  }
	public function getAllVisible(){
		//return $this->db->from($this->tbl)->where("is_visible",1)->order_by("priority","asc")->get();
		return $this->db->from($this->tbl)->order_by("priority","asc")->get();
	}
	public function getAllVisibleParent(){
		return $this->db->from($this->tbl)->order_by("priority","asc")->where_as("children_identifier","IS NULL")->get("object",0);
	}
	public function getIdentifierAll(){
		//return $this->db->from($this->tbl)->where("is_visible",1)->order_by("priority","asc")->get();
		return $this->db->select("identifier")->from($this->tbl)->order_by("priority","asc")->get();
	}
	public function getParent($identifier){
		$d = $this->db->select_as("COALESCE(children_identifier,'')","children_identifier",1)->from($this->tbl)->where("identifier",$identifier)->order_by("priority","asc")->get_first();
		if(isset($d[0]->children_identifier)) return $d[0]->children_identifier;
		return "";
	}

	public function getChildModules($nation_code,$id=''){
		$filter = empty($id) ? "IS NULL" : "= '". $id ."'";
		$d = $this->db->query("SELECT * FROM a_modules WHERE nation_code = $nation_code AND is_visible = 1 AND is_active = 1 AND children_identifier ". $filter ." ORDER BY priority");
		return $d;
	}
	public function getVisibleAndActive(){
		$this->db->where('is_active','1');
		$this->db->where('is_visible','1');
		$this->db->order_by('identifier','asc');
		return $this->db->get();
	}
}
