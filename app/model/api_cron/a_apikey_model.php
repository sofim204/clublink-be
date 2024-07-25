<?php
class A_ApiKey_Model extends JI_Model{
	var $tbl = 'a_apikey';
	var $tbl_as = 'aa';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
  public function getActive(){
    $this->db->select('nation_code');
    $this->db->select('id');
    $this->db->where('is_active',1);
    return $this->db->get('',0);
  }

	public function set($di){
		if(!is_array($di)) return 0;
		if (isset($di['username'])) {
				if (mb_strlen($di['username'])) {
						$di['username'] = $this->__encrypt($di['username']);
				}
		}
		if (isset($di['password'])) {
				if (mb_strlen($di['password'])) {
						$di['password'] = $this->__encrypt($di['password']);
				}
		}
		if (isset($di['code'])) {
				if (mb_strlen($di['code'])) {
						$di['code'] = $this->__encrypt($di['code']);
				}
		}
		if (isset($du['str'])) {
				if (mb_strlen($du['str'])) {
						$di['str'] = $this->__encrypt($di['str']);
				}
		}
		return $this->db->insert($this->tbl,$di,0,0);
	}
	public function update($nation_code, $id,$du){
		if(!is_array($du)) return 0;
		if (isset($du['username'])) {
				if (mb_strlen($du['username'])) {
						$du['username'] = $this->__encrypt($du['username']);
				}
		}
		if (isset($du['password'])) {
				if (mb_strlen($du['password'])) {
						$du['password'] = $this->__encrypt($du['password']);
				}
		}
		if (isset($du['code'])) {
				if (mb_strlen($du['code'])) {
						$du['code'] = $this->__encrypt($du['code']);
				}
		}
		if (isset($du['str'])) {
				if (mb_strlen($du['str'])) {
						$du['str'] = $this->__encrypt($du['str']);
				}
		}
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
    return $this->db->update($this->tbl,$du,0);
	}
}
