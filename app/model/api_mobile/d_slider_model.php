<?php
class D_Slider_Model extends SENE_Model{
	var $tbl = 'd_slider';
	var $tbl_as = 'dc';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getAlias(){
		return $this->tbl_as;
	}
	public function get($jenis="",$is_active="1"){
		if(strlen($jenis)) $this->db->where('jenis',$jenis);
		if(strlen($is_active)) $this->db->where('is_active',$is_active);
		$this->db->order_by('id','desc');
		return $this->db->get('object',0);
	}
}