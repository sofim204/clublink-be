<?php
class G_Pointpolicybuyandsell_Model extends SENE_Model{
	var $tbl = 'common_code';
	var $tbl_as = 'cc';

    public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

    public function getByClassified($nation_code, $classified){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("classified",$classified);
		$this->db->where("use_yn","y");
		return $this->db->get();
    }

    public function getByClassifiedAndCode($nation_code, $classified, $code){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("classified",$classified);
		$this->db->where("code",$code);
		$this->db->where("use_yn","y");
		$this->db->order_by("id",'asc');
		return $this->db->get_first();
	}
}
