<?php
class G_Highlight_Community_Model extends SENE_Model{
	var $tbl = 'g_highlight_community';
	var $tbl_as = 'ghc';
	var $tbl2 = 'c_community';
	var $tbl2_as = 'comm_list';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}
}
