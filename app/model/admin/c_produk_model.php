<?php
class C_Produk_Model extends SENE_Model {
	var $is_cacheable;
	var $tbl = 'c_produk';
	var $tbl_as = 'cp';
	
	public $tbl2 = 'e_offer_review';
    public $tbl2_as = 'eor';
    public $tbl3 = 'e_chat_room';
    public $tbl3_as = 'ecr';
	public $tbl4 = 'b_user';
	public $tbl4_as = 'bu';

	// by Muhammad Sofi - 18 November 2021 12:00
    //change car and motorcycle to main category
    public $tbl10 = 'b_kategori';
    public $tbl10_as = 'bk_brand';

	public function __construct(){
		parent::__construct();
		$this->is_cacheable = 0;
		$this->db->from($this->tbl,$this->tbl_as);
	}
	
	// by Muhammad Sofi - 18 November 2021 12:00
    //change car and motorcycle to main category
    private function __joinTbl10()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl10_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.brand", "=", "$this->tbl10_as.id");
        return $composites;
    }

	public function __joinTbl2() {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl2_as.e_chat_room_id", "=", "$this->tbl3_as.id");
        return $cps;
    }

    public function __joinTbl3() {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl2_as.b_user_id_to", "=", "$this->tbl4_as.id");
        return $cps;
    }

	public function getById($nation_code, $id){
		$this->db->select_as("$this->tbl_as.*, $this->tbl_as.id","id",0);
		$this->db->select_as("CONCAT($this->tbl_as.b_kategori_id,'')","b_kategori_id",0);
		$this->db->select_as("CONCAT($this->tbl_as.b_kondisi_id,'')","b_kondisi_id",0);
		$this->db->select_as("CONCAT($this->tbl_as.b_berat_id,'')","b_berat_id",0);
		$this->db->select_as("CONCAT($this->tbl_as.b_lokasi_id,'')","b_lokasi_id",0);
		// $this->db->select_as("IF($this->tbl_as.is_active != '0', $this->tbl_as.stok, '0')", "stok", 0);
		// by Muhammad Sofi - 18 November 2021 12:00
		//change car and motorcycle to main category
        $this->db->select_as("IF($this->tbl_as.product_type != 'Automotive', $this->tbl_as.brand, IF($this->tbl10_as.nama IS NULL, $this->tbl_as.brand, $this->tbl10_as.nama))", "brand", 0);
		$this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');
		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
		// $this->db->where('nation_code',$nation_code);
		$this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
		// $this->db->where('id',$id);
		return $this->db->get_first();
	}

	public function getByIds($nation_code, $pids=array()){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where('nation_code',$nation_code);
		$this->db->where_in('id',$pids);
		return $this->db->get();
	}

	public function getSellerReview($produk_id, $type) {
        $this->db->select_as("$this->tbl2_as.review", "review", 0);
        $this->db->select_as("$this->tbl2_as.star", "star", 0);
        $this->db->select_as($this->__decrypt("$this->tbl4_as.fnama"), "buyer_name", 0);
        $this->db->from($this->tbl2, $this->tbl2_as);
		$this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl2(), 'left');
		$this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl3(), 'left');
        $this->db->where_as("$this->tbl3_as.c_produk_id", $this->db->esc($produk_id));
        $this->db->where_as("$this->tbl2_as.type", $this->db->esc($type));
        return $this->db->get("object", 0); 
    }
}
