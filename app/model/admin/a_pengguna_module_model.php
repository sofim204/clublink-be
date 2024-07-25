<?php
//admin
class A_Pengguna_Module_Model extends SENE_Model{
	var $tbl="a_pengguna_module";
	var $tbl_as = "apm";
	public function __construct(){
		parent::__construct();

	}
	public function getUserModulesx($a_pengguna_id){
		$this->db->select("");
		return $this->db->from($this->tbl)->where("a_pengguna_id",$a_pengguna_id)->get();
	}
	public function getUserModules($nation_code,$a_pengguna_id){
		$this->db->select_as("*, COALESCE(`a_modules_identifier`,'')","module",0);
		$this->db->from($this->tbl);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("a_pengguna_id",$a_pengguna_id);
		$this->db->order_by("a_modules_identifier","ASC");
		return $this->db->get();
	}
	public function clearBefore($a_pengguna_id,$modules){
		$sql = "DELETE FROM `$this->tbl` WHERE `a_pengguna_id` = ".$this->db->esc($a_pengguna_id)."";
		$this->exec($sql);
	}
	public function saveModules($a_pengguna_id,$modules){
		$sql = "INSERT INTO `$this->tbl`(`id`,`a_pengguna_id`,`a_modules_identifier`,`rule`) VALUES ";

		if(!is_array($modules)){
			trigger_error("Fungsi di MB_USER_MMODULES / saveModules parameter keduanya harus array! check lagi kodinganya!");
			die();
		}

		foreach($modules as $m){
			$sql .="(NULL, ".$this->db->esc($a_pengguna_id).", ".$this->db->esc($m['module']).",".$this->db->esc($m['rule'])."),";
		}

		$sql = rtrim($sql,",");
		return $this->exec($sql);
	}
	public function getUserModuleDetail($a_pengguna_id){
		$sql = "SELECT bum.*, COALESCE(bum.`a_modules_identifier`,'') AS module
		FROM $this->tbl bum
		LEFT JOIN b_modules bmod
			ON bmod.identfier = bum.a_modules_identifier
		WHERE `a_pengguna_id` = ".$this->db->esc($a_pengguna_id)."
		ORDER BY bum.a_modules_identifier ASC";
		return $this->select($sql);
	}
}
