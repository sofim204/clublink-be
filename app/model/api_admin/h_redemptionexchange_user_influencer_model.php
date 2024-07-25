<?php
class H_Redemptionexchange_User_Influencer_Model extends SENE_Model {
	var $tbl = 'h_point_redemption_exchange_user_influencer';
	var $tbl_as = 'hpreui';
    var $tbl2 = 'b_user';
	var $tbl2_as = 'bu';

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function getTableAlias(){
		return $this->tbl_as;
	}

    public function getTableAlias2(){
		return $this->tbl2_as;
	}

    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
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

	public function getById($id) {
        $this->db->flushQuery();
		$this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("$this->tbl_as.b_user_id", $id);
		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
    
}
