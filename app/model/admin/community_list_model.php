<?php
class Community_List_Model extends SENE_Model{
	var $tbl = 'c_community';
	var $tbl_as = 'comm_list';
	var $tbl2 = 'c_community_category';
	var $tbl2_as = 'comm_category';
	var $tbl3 = 'b_user';
	var $tbl3_as = 'master_user';
	var $tbl4 = 'b_user_alamat';
	var $tbl4_as = 'user_address';
	var $tbl5 = 'c_community_report';
	var $tbl5_as = 'comm_report';
	var $tbl6 = 'c_community_like';
	var $tbl6_as = 'comm_likes';
	var $tbl7 = 'b_user';
	var $tbl7_as = 'master_user';
	var $tbl8 = 'b_user_alamat';
	var $tbl8_as = 'user_address';
	var $tbl9 = 'c_community_like_category';
	var $tbl9_as = 'comm_emoji';

	var $tbl27 = 'c_community_like_category'; // for top like image 1
    var $tbl27_as = 'cclc';
    var $tbl28 = 'c_community_like_category'; // for top like image 2
    var $tbl28_as = 'cclc_2';
    var $tbl29 = 'c_community_like_category'; // for top like image 3
    var $tbl29_as = 'cclc_3';

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
        $cps[] = $this->db->composite_create("$this->tbl_as.c_community_category_id", "=", "$this->tbl2_as.id");
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
    //     $cps[] = $this->db->composite_create("$this->tbl3_as.id", "=", "$this->tbl4_as.b_user_id");
    //     return $cps;
    // }

    private function __joinTbl7()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl6_as.nation_code", "=", "$this->tbl7_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl6_as.b_user_id", "=", "$this->tbl7_as.id");
        return $cps;
    }

	// by Muhammad Sofi - 12 November 2021 21:40 | not used
    // private function __joinTbl8()
    // {
    //     $cps = array();
    //     $cps[] = $this->db->composite_create("$this->tbl6_as.nation_code", "=", "$this->tbl8_as.nation_code");
    //     $cps[] = $this->db->composite_create("$this->tbl6_as.b_user_alamat_id", "=", "$this->tbl8_as.id");
    //     $cps[] = $this->db->composite_create("$this->tbl7_as.id", "=", "$this->tbl8_as.b_user_id");
    //     return $cps;
    // }

    private function __joinTbl9()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl6_as.nation_code", "=", "$this->tbl9_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl6_as.c_community_like_category_id", "=", "$this->tbl9_as.id");
        return $cps;
    }

	// by Muhammad Sofi - 17 November 2021 17:20 | for join top like image 1
	private function __joinTbl27()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl27_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.top_like_image_1 ", "=", "$this->tbl27_as.id");
        return $composites;
    }

    private function __joinTbl28()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl28_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.top_like_image_2 ", "=", "$this->tbl28_as.id");
        return $composites;
    }

    private function __joinTbl29()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl29_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.top_like_image_3 ", "=", "$this->tbl29_as.id");
        return $composites;
    }
	
	public function get($nation_code, $limit=100,$is_active=1){
		$this->db->where('nation_code',$nation_code,'=','AND',0,0);
		$this->db->where('utype','kategori','=','OR',1,0);
		$this->db->where('utype','kategori_sub','=','OR',0,0);
		$this->db->where('utype','kategori_sub_sub','=','OR',0,1);
		$this->db->where('is_active',$is_active);
		$this->db->limit($limit);
		return $this->db->get();
	}
	public function getById($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->get_first();
	}
	public function set($di){
		if(!is_array($di)) return 0;
		$this->db->insert($this->tbl,$di,0,0);
		return $this->db->last_id;
	}
	public function update($nation_code, $id, $du){
		if(!is_array($du)) return 0;
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
    return $this->db->update($this->tbl,$du,0);
	}
	public function del($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}
	public function getParentCategory(){
		$this->db->select()->from($this->tbl,$this->tbl_as)->where_as("b_kategori_id", "is null");
		$this->db->where("utype",'kategori',"OR","like",1,0);
		$this->db->where("utype",'kategori_sub',"OR","like",0,0);
		$this->db->where("utype",'kategori_sub_sub',"OR","like",0,1);
		$this->db->order_by("prioritas","asc")->order_by("nama","asc");
		$this->db->limit(100);
		return $this->db->get('object',0);
	}
	public function getSubCategory($b_kategori_id){
		$this->db->select()->from($this->tbl,$this->tbl_as)->where_as("b_kategori_id", $b_kategori_id);
		$this->db->where("utype",'kategori_sub',"AND","like",0,0);
		$this->db->limit(100);
		return $this->db->get('',0);
	}
	public function getFirstByName($nama){
		$nama = strtolower(trim($nama));
		$this->db->where_as('LOWER(TRIM(nama))',$this->db->esc($nama),'and','like')->order_by('id','desc');
		return $this->db->get_first('object',0);
	}
	public function getActive($nation_code){
		$this->db->where_as("nation_code",$nation_code);
		return $this->db->get();
	}

    public function detail($id)
    {
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.title", "title", 0);
		$this->db->select_as("$this->tbl_as.deskripsi", "description", 0);
		$this->db->select_as("$this->tbl2_as.nama", "nama", 0); // by Muhammad Sofi 29 December 2021 11:34 | show community category on list and detail
		$this->db->select_as("COALESCE($this->tbl27_as.image_icon,'')", "top_like_image_1", 0); // by Muhammad Sofi - 17 November 2021 17:20 | for top like image 1
        $this->db->select_as("COALESCE($this->tbl28_as.image_icon,'')", "top_like_image_2", 0); // by Muhammad Sofi - 17 November 2021 17:20 | for top like image 2
        $this->db->select_as("COALESCE($this->tbl29_as.image_icon,'')", "top_like_image_3", 0); // by Muhammad Sofi - 17 November 2021 17:20 | for top like image 3
		$this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);
		// $this->db->select_as("$this->tbl9_as.image_icon", "image_icon", 0);

        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "user", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);

		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.ldate", "ldate", 0);
		// by Muhammad Sofi - 4 November 2021 10:00
        // remark code
        // $this->db->select_as($this->__decrypt("$this->tbl4_as.alamat"), "address", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "address2", 0);
		$this->db->select_as("$this->tbl_as.is_published", "is_published", 0);
		$this->db->select_as("$this->tbl_as.is_report", "is_report", 0);
		$this->db->select_as("$this->tbl_as.is_take_down", "is_take_down", 0);

		$this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
		$this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
		$this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
		$this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
		$this->db->select_as($this->__decrypt("$this->tbl3_as.email"), "email", 0);

		$this->db->from($this->tbl,$this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
		// $this->db->join_composite($this->tbl9, $this->tbl9_as, $this->__joinTbl9(), "left");
		$this->db->join_composite($this->tbl27, $this->tbl27_as, $this->__joinTbl27(), 'left'); // by Muhammad Sofi - 17 November 2021 17:20 | for join top like image 1
        $this->db->join_composite($this->tbl28, $this->tbl28_as, $this->__joinTbl28(), 'left'); // by Muhammad Sofi - 17 November 2021 17:20 | for join top like image 2
        $this->db->join_composite($this->tbl29, $this->tbl29_as, $this->__joinTbl29(), 'left'); // by Muhammad Sofi - 17 November 2021 17:20 | for join top like image 3
        // $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");	// by Muhammad Sofi - 12 November 2021 21:40 | not used

		$this->db->where("$this->tbl_as.id",$id,"AND","=",0,0);
		// return $this->db->get("object",0);
		return $this->db->get_first();
    }

    public function count_reported()
    {
        $this->db->flushQuery();
		$this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl5_as.c_community_id, '-', $this->tbl5_as.b_user_id))","jumlah",0);
        $this->db->from($this->tbl5, $this->tbl5_as);
		$this->db->where_as("$this->tbl5_as.nation_code", "62", "AND", "=", 0, 0);
        return $this->db->get_first('',0);
    }

    public function getAllLikes($id)
    {
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl6_as.id", "id", 0);
		$this->db->select_as("$this->tbl6_as.type", "type", 0);
		$this->db->select_as("$this->tbl9_as.image_icon", "image_icon", 0);
        $this->db->select_as($this->__decrypt("$this->tbl7_as.fnama"), "user", 0);
		$this->db->select_as("$this->tbl6_as.is_active", "is_active", 0);

		$this->db->select_as("$this->tbl6_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl6_as.ldate", "ldate", 0);

		$this->db->from($this->tbl6,$this->tbl6_as);
        $this->db->join_composite($this->tbl9, $this->tbl9_as, $this->__joinTbl9(), "inner");
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "inner");
        // $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "left"); // by Muhammad Sofi - 12 November 2021 21:40 | not used

		$this->db->where("$this->tbl6_as.type","community","AND","=",0,0);
		$this->db->where("$this->tbl6_as.custom_id",$id,"AND","=",0,0);
		// by Muhammad Sofi - 4 November 2021 10:00
		// add is_active = 1
		$this->db->where("$this->tbl6_as.is_active", 1);
		return $this->db->get("object",0);
    }
	
	public function getCategoryImageById($nation_code, $id) {
        $this->db->select_as("$this->tbl2_as.image_icon", "image_icon");
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
		$this->db->where_as("$this->tbl_as.nation_code" ,$nation_code);
		$this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
		return $this->db->get_first();
	}
}
