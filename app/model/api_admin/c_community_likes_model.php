<?php
class C_Community_Likes_Model extends SENE_Model{
	var $tbl = 'c_community_like';
	var $tbl_as = 'comm_likes';
	var $tbl2 = 'c_community_like_category';
	var $tbl2_as = 'comm_emoji';
	var $tbl3 = 'b_user';
	var $tbl3_as = 'master_user';
	var $tbl4 = 'b_user_alamat';
	var $tbl4_as = 'user_address';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	public function getTableAlias(){
		return $this->tbl_as;
	}

	public function getTableAlias2(){
		return $this->tbl2_as;
	}

	public function getTableAlias3(){
		return $this->tbl3_as;
	}

	public function getTableAlias4(){
		return $this->tbl4_as;
	}

	public function getTableAlias5(){
		return $this->tbl5_as;
	}

    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.c_community_like_category_id", "=", "$this->tbl2_as.id");
        return $cps;
    }

    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl3_as.id");
        return $cps;
    }
	
	// by Muhammad Sofi - 12 November 2021 21:40 | not used
    // private function __joinTbl4()
    // {
    //     $cps = array();
    //     $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
    //     $cps[] = $this->db->composite_create("$this->tbl_as.b_user_alamat_id", "=", "$this->tbl4_as.id");
    //     $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl4_as.b_user_id");
    //     return $cps;
    // }

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

	public function getAll($nation_code, $page=0,$pagesize=10,$sortCol="sku",$sortDir="ASC",$keyword="",$utype_in=array(),$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.type", "type", 0);
		$this->db->select_as("$this->tbl2_as.image_icon", "image_icon", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "user", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);

		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.ldate", "ldate", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl4_as.alamat"), "address", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl4_as.alamat2"), "address2", 0);

		$this->db->from($this->tbl,$this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        // $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left"); // by Muhammad Sofi - 12 November 2021 21:40 | not used

		$this->db->where("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
		// if(is_array($utype_in)) if(count($utype_in)) $this->db->where_in('utype',$utype_in);
		// if(mb_strlen($keyword)>1){
		// 	$this->db->where("title",$keyword,"OR","%like%",1,0);
		// 	$this->db->where("deskripsi",$keyword,"OR","%like%",0,1);
		// }
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}

	public function countAll($nation_code, $keyword="",$utype_in=array(),$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code,"AND","=",0,0);
		if(is_array($utype_in)) if(count($utype_in)) $this->db->where_in('utype',$utype_in);
		if(mb_strlen($keyword)>1){
			$this->db->where("nama",$keyword,"OR","%like%",1,0);
			$this->db->where("deskripsi",$keyword,"OR","%like%",0,1);
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
}
