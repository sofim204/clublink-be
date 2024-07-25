<?php
class B_User_Detail_Offer_Model extends JI_Model {
    var $is_cacheable;
    var $tbl = 'e_chat_room';
    var $tbl_as = 'ecr';
  
    //START by Donny Dennison - 26 july 2022 14:35
    //offer list for buyer
    var $tbl2 = 'e_chat';
    var $tbl2_as = 'ec';
    var $tbl3 = 'e_offer_review';
    var $tbl3_as = 'eorbuyer';
    var $tbl4 = 'e_offer_review';
    var $tbl4_as = 'eorseller';
    //END by Donny Dennison - 26 july 2022 14:35
    //offer list for buyer
  
    var $tbl5 = 'b_user';
    var $tbl5_as = 'bu';
  
    //START by Donny Dennison - 26 july 2022 14:35
    //offer list for buyer
    var $tbl6 = 'b_user';
    var $tbl6_as = 'bu2';
    //END by Donny Dennison - 26 july 2022 14:35
    //offer list for buyer
  
    var $tbl7 = 'e_chat_participant';
    var $tbl7_as = 'ecp';
  
    //START by Donny Dennison - 26 july 2022 14:35
    //offer list for buyer
    var $tbl8 = 'c_produk';
    var $tbl8_as = 'cp';
    var $tbl9 = 'b_kategori';
    var $tbl9_as = 'bk';
    //END by Donny Dennison - 26 july 2022 14:35
    //offer list for buyer
  
    // var $tbl10 = 'b_user'; //join with e_chat_room
    // var $tbl10_as = 'bb';
    // var $tbl11 = 'b_user'; //join with e_chat_room
    // var $tbl11_as = 'bs';
    // var $tbl20 = 'e_chat';
    // var $tbl20_as = 'e';
  
    public function __construct(){
      parent::__construct();
      $this->is_cacheable = 0;
      $this->db->from($this->tbl,$this->tbl_as);
    }
  
    public function getTblAs()
    {
      return $this->tbl_as;
    }
    
	//START by Donny Dennison - 26 july 2022 14:35
	//offer list for buyer
	public function getTblAs2()
	{
		return $this->tbl2_as;
	}

	private function __joinTbl2(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl2_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl_as.id","=","$this->tbl2_as.e_chat_room_id");
		return $cps;
	}

	private function __joinTbl3()
	{
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl2_as.e_chat_room_id", "=", "$this->tbl3_as.e_chat_room_id");
		$cps[] = $this->db->composite_create("$this->tbl2_as.id", "=", "$this->tbl3_as.e_chat_id");
		$cps[] = $this->db->composite_create("'seller'", "=", "$this->tbl3_as.type");
		return $cps;
	}

	private function __joinTbl4()
	{
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl4_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl2_as.e_chat_room_id", "=", "$this->tbl4_as.e_chat_room_id");
		$cps[] = $this->db->composite_create("$this->tbl2_as.id", "=", "$this->tbl4_as.e_chat_id");
		$cps[] = $this->db->composite_create("'buyer'", "=", "$this->tbl4_as.type");
		return $cps;
	}
	//END by Donny Dennison - 26 july 2022 14:35
	//offer list for buyer

	private function __joinTbl5()
	{
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl5_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl_as.b_user_id_starter", "=", "$this->tbl5_as.id");
		return $cps;
	}

	//START by Donny Dennison - 26 july 2022 14:35
	//offer list for buyer
	private function __joinTbl6()
	{
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl6_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl_as.b_user_id_seller", "=", "$this->tbl6_as.id");
		return $cps;
	}
	//END by Donny Dennison - 26 july 2022 14:35
	//offer list for buyer

	private function __joinTbl7()
	{
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl7_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl7_as.e_chat_room_id");
		return $cps;
	}

	//START by Donny Dennison - 26 july 2022 14:35
	//offer list for buyer
	private function __joinTbl8()
	{
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl8_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl_as.c_produk_id", "=", "$this->tbl8_as.id");
		return $cps;
	}

	private function __joinTbl9()
	{
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl8_as.nation_code", "=", "$this->tbl9_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl8_as.b_kategori_id", "=", "$this->tbl9_as.id");
		return $cps;
	}

	public function countAllDetailAsSeller($chat_type = 'offer', $type='seller', $keyword, $from_date="", $to_date="", $b_user_id, $is_seller_or_buyer) {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
		$this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
		$this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "left");
		$this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "left");
		// if (strlen($from_date)==10 && strlen($to_date)==10) {
		// 	$this->db->between("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "DATE('$to_date')");
		// } else if (strlen($from_date)==10 && strlen($to_date)!=10) {
		// 	$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$from_date')", 'AND', '>=');
		// } else if (strlen($from_date)!=10 && strlen($to_date)==10) {
		// 	$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$to_date')", 'AND', '<=');
		// }
		
		// if(strlen($path)>0) {
		// 	$this->db->where_as("$this->tbl_as.path", $this->db->esc($path));
		// }



		$this->db->where("$this->tbl_as.nation_code", "62");
		$this->db->where("$this->tbl2_as.type","accepted");
		
		//by Donny Dennison - 3 june 2022 13:10
		//new feature, product type santa
		$this->db->where_as("$this->tbl8_as.product_type", $this->db->esc("Santa"), "AND", "!=");
		
		if($chat_type){
			$this->db->where("$this->tbl_as.chat_type", "offer");
		}
		
		if($b_user_id != 0){
			if($type == "buyer"){
				$this->db->where("$this->tbl_as.b_user_id_starter",$b_user_id);
			}else{
				$this->db->where("$this->tbl_as.b_user_id_seller",$b_user_id);
			}
		}

		// if(mb_strlen($keyword)>0) {
		// 	$this->db->where_as("$this->tbl_as.path", $keyword, "OR", "%like%", 1, 0);
		// 	$this->db->where_as("$this->tbl_as.log_text", $keyword, "OR", "%like%", 0, 1);
		// }

		if (strlen($from_date)==7 && strlen($to_date)==7) {
			$this->db->between("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$from_date','-','01'))", "DATE(CONCAT('$to_date','-','28'))");
			// $this->db->where_as("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$from_date','-','01'))", 'AND', '>=');
			// $this->db->where_as("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$to_date','-','28'))", 'AND', '<=');
		} else if (strlen($from_date)==7 && strlen($to_date)!=7) {
			$this->db->where_as("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$from_date','-','01'))", 'AND', '>=');
		} else if (strlen($from_date)!=7 && strlen($to_date)==7) {
			$this->db->where_as("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$to_date','-','28'))", 'AND', '<=');
		}

		$this->db->where("$this->tbl_as.offer_status","reviewed");
		
		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
  }

	public function getAllDetailAsSeller($nation_code, $chat_type = 'offer', $type="seller", $page, $page_size, $sort_col, $sort_dir, $from_date="", $to_date="", $b_user_id = 0, $product_type = "All", $offer_type = "ongoing", $is_seller_or_buyer){
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY message DESC)", "no");
		$this->db->select_as("$this->tbl_as.id", "chat_room_id", 0);
		$this->db->select_as("$this->tbl_as.c_produk_nama", "c_produk_nama", 0);
		$this->db->select_as("$this->tbl8_as.product_type", "product_type", 0);
		// $this->db->select_as("$this->tbl_as.c_produk_harga_jual", "c_produk_harga_jual", 0);
		// $this->db->select_as("$this->tbl2_as.message", "message", 0);
		// $this->db->select_as("IF($this->tbl8_as.product_type = 'Free', 0, $this->tbl2_as.message)", 'message', 0);
		$this->db->select_as("IF($this->tbl8_as.product_type = 'Free', 0, CAST($this->tbl2_as.message AS UNSIGNED))", 'message', 0);
		if($is_seller_or_buyer == "buyer") {
			// $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl5_as.fnama").',"")', "b_user_nama_buyer", 0);
			$this->db->select_as('CONCAT(COALESCE('.$this->__decrypt("$this->tbl5_as.fnama").',"")," - ", COALESCE('.$this->__decrypt("$this->tbl5_as.email").',"") )', "b_user_nama_buyer", 0);
		} else if($is_seller_or_buyer == "seller") {
			// $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl6_as.fnama").',"")', "b_user_nama_seller", 0);
			$this->db->select_as('CONCAT(COALESCE('.$this->__decrypt("$this->tbl6_as.fnama").',"")," - ", COALESCE('.$this->__decrypt("$this->tbl6_as.email").',"") )', "b_user_nama_seller", 0);
		}
		$this->db->select_as("$this->tbl_as.offer_status_update_date", "cdate", 0);
		
		// $this->db->select_as("$this->tbl_as.b_user_id_starter", "b_user_id_buyer", 0);

		// $this->db->select_as("COALESCE($this->tbl5_as.image,'')", "b_user_image_buyer", 0);
		// $this->db->select_as("$this->tbl_as.c_produk_id", "c_produk_id", 0);
		// $this->db->select_as("$this->tbl_as.b_user_id_seller", "b_user_id_seller", 0);

		// $this->db->select_as("COALESCE($this->tbl6_as.image,'')", "b_user_image_seller", 0);
		// $this->db->select_as("$this->tbl_as.c_produk_thumb", "c_produk_thumb", 0);
		// $this->db->select_as("$this->tbl8_as.b_kategori_id", "b_kategori_id", 0);
		// $this->db->select_as("$this->tbl9_as.nama", "kategori", 0);
		// $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl8_as.alamat2").',"")', "alamat2", 0);
		// $this->db->select_as("$this->tbl8_as.stok", "stok", 0);
		

		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
		$this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
		$this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "left");
		$this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "left");

		$this->db->where("$this->tbl_as.nation_code",$nation_code);
		$this->db->where("$this->tbl2_as.type","accepted");
		
		//by Donny Dennison - 3 june 2022 13:10
		//new feature, product type santa
		$this->db->where_as("$this->tbl8_as.product_type", $this->db->esc("Santa"), "AND", "!=");
		
		if($chat_type){
			$this->db->where("$this->tbl_as.chat_type","offer");
		}
		
		if($b_user_id != 0){
			if($type == "buyer"){
				$this->db->where("$this->tbl_as.b_user_id_starter",$b_user_id);
			}else{
				$this->db->where("$this->tbl_as.b_user_id_seller",$b_user_id);
			}
		}

		if (strlen($from_date)==7 && strlen($to_date)==7) {
			$this->db->between("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$from_date','-','01'))", "DATE(CONCAT('$to_date','-','28'))");
			// $this->db->where_as("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$from_date','-','01'))", 'AND', '>=');
			// $this->db->where_as("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$to_date','-','28'))", 'AND', '<=');
		} else if (strlen($from_date)==7 && strlen($to_date)!=7) {
			$this->db->where_as("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$from_date','-','01'))", 'AND', '>=');
		} else if (strlen($from_date)!=7 && strlen($to_date)==7) {
			$this->db->where_as("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$to_date','-','28'))", 'AND', '<=');
		}

		$this->db->where("$this->tbl_as.offer_status","reviewed");

		$this->db->order_by($sort_col, $sort_dir);

		$this->db->page($page, $page_size);

		return $this->db->get("", 0);
		
	}

  public function countAllDetailAsBuyer($chat_type = 'offer', $type='buyer', $keyword, $from_date="", $to_date="", $b_user_id, $is_seller_or_buyer) {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
		$this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
		$this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "left");
		$this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "left");
		// if (strlen($from_date)==10 && strlen($to_date)==10) {
		// 	$this->db->between("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "DATE('$to_date')");
		// } else if (strlen($from_date)==10 && strlen($to_date)!=10) {
		// 	$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$from_date')", 'AND', '>=');
		// } else if (strlen($from_date)!=10 && strlen($to_date)==10) {
		// 	$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$to_date')", 'AND', '<=');
		// }
		
		// if(strlen($path)>0) {
		// 	$this->db->where_as("$this->tbl_as.path", $this->db->esc($path));
		// }

		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl_as.path", addslashes($keyword), "OR", "%like%", 1, 0);
			$this->db->where_as("$this->tbl_as.log_text", addslashes($keyword), "OR", "%like%", 0, 1);
		}

		$this->db->where("$this->tbl_as.nation_code", '62');
		$this->db->where("$this->tbl2_as.type","accepted");
		
		//by Donny Dennison - 3 june 2022 13:10
		//new feature, product type santa
		$this->db->where_as("$this->tbl8_as.product_type", $this->db->esc("Santa"), "AND", "!=");
		
		if($chat_type){
			$this->db->where("$this->tbl_as.chat_type", "offer");
		}
		
		if($b_user_id != 0){
			if($type == "buyer"){
				$this->db->where("$this->tbl_as.b_user_id_starter",$b_user_id);
			}else{
				$this->db->where("$this->tbl_as.b_user_id_seller",$b_user_id);
			}
		}

		if (strlen($from_date)==7 && strlen($to_date)==7) {
			$this->db->between("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$from_date','-','01'))", "DATE(CONCAT('$to_date','-','28'))");
			// $this->db->where_as("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$from_date','-','01'))", 'AND', '>=');
			// $this->db->where_as("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$to_date','-','28'))", 'AND', '<=');
		} else if (strlen($from_date)==7 && strlen($to_date)!=7) {
			$this->db->where_as("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$from_date','-','01'))", 'AND', '>=');
		} else if (strlen($from_date)!=7 && strlen($to_date)==7) {
			$this->db->where_as("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$to_date','-','28'))", 'AND', '<=');
		}

		$this->db->where("$this->tbl_as.offer_status","reviewed");
		
		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
  }

	public function getAllDetailAsBuyer($nation_code, $chat_type = 'offer', $type="buyer", $page, $page_size, $sort_col, $sort_dir, $from_date="", $to_date="", $b_user_id = 0, $product_type = "All", $offer_type = "ongoing", $is_seller_or_buyer){
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY message DESC)", "no");
		$this->db->select_as("$this->tbl_as.id", "chat_room_id", 0);
		$this->db->select_as("$this->tbl_as.c_produk_nama", "c_produk_nama", 0);
		$this->db->select_as("$this->tbl8_as.product_type", "product_type", 0);
		// $this->db->select_as("$this->tbl_as.c_produk_harga_jual", "c_produk_harga_jual", 0);
		// $this->db->select_as("$this->tbl2_as.message", "message", 0);
		// $this->db->select_as("IF($this->tbl8_as.product_type = 'Free', 0, $this->tbl2_as.message)", 'message', 0);
		$this->db->select_as("IF($this->tbl8_as.product_type = 'Free', 0, CAST($this->tbl2_as.message AS UNSIGNED))", 'message', 0);
		if($is_seller_or_buyer == "buyer") {
			// $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl5_as.fnama").',"")', "b_user_nama_buyer", 0);
			$this->db->select_as('CONCAT(COALESCE('.$this->__decrypt("$this->tbl5_as.fnama").',"")," - ", COALESCE('.$this->__decrypt("$this->tbl5_as.email").',"") )', "b_user_nama_buyer", 0);
		} else if($is_seller_or_buyer == "seller") {
			// $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl6_as.fnama").',"")', "b_user_nama_seller", 0);
			$this->db->select_as('CONCAT(COALESCE('.$this->__decrypt("$this->tbl6_as.fnama").',"")," - ", COALESCE('.$this->__decrypt("$this->tbl6_as.email").',"") )', "b_user_nama_seller", 0);
		}
		$this->db->select_as("$this->tbl_as.offer_status_update_date", "cdate", 0);
		
		// $this->db->select_as("$this->tbl_as.b_user_id_starter", "b_user_id_buyer", 0);

		// $this->db->select_as("COALESCE($this->tbl5_as.image,'')", "b_user_image_buyer", 0);
		// $this->db->select_as("$this->tbl_as.c_produk_id", "c_produk_id", 0);
		// $this->db->select_as("$this->tbl_as.b_user_id_seller", "b_user_id_seller", 0);

		// $this->db->select_as("COALESCE($this->tbl6_as.image,'')", "b_user_image_seller", 0);
		// $this->db->select_as("$this->tbl_as.c_produk_thumb", "c_produk_thumb", 0);
		// $this->db->select_as("$this->tbl8_as.b_kategori_id", "b_kategori_id", 0);
		// $this->db->select_as("$this->tbl9_as.nama", "kategori", 0);
		// $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl8_as.alamat2").',"")', "alamat2", 0);
		// $this->db->select_as("$this->tbl8_as.stok", "stok", 0);
		

		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
		$this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
		$this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "left");
		$this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "left");

		$this->db->where("$this->tbl_as.nation_code",$nation_code);
		$this->db->where("$this->tbl2_as.type","accepted");
		
		//by Donny Dennison - 3 june 2022 13:10
		//new feature, product type santa
		$this->db->where_as("$this->tbl8_as.product_type", $this->db->esc("Santa"), "AND", "!=");
		
		if($chat_type){
			$this->db->where("$this->tbl_as.chat_type","offer");
		}
		
		if($b_user_id != 0){
			if($type == "buyer"){
				$this->db->where("$this->tbl_as.b_user_id_starter",$b_user_id);
			}else{
				$this->db->where("$this->tbl_as.b_user_id_seller",$b_user_id);
			}
		}

		if (strlen($from_date)==7 && strlen($to_date)==7) {
			$this->db->between("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$from_date','-','01'))", "DATE(CONCAT('$to_date','-','28'))");
			// $this->db->where_as("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$from_date','-','01'))", 'AND', '>=');
			// $this->db->where_as("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$to_date','-','28'))", 'AND', '<=');
		} else if (strlen($from_date)==7 && strlen($to_date)!=7) {
			$this->db->where_as("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$from_date','-','01'))", 'AND', '>=');
		} else if (strlen($from_date)!=7 && strlen($to_date)==7) {
			$this->db->where_as("DATE($this->tbl_as.offer_status_update_date)", "DATE(CONCAT('$to_date','-','28'))", 'AND', '<=');
		}

		$this->db->where("$this->tbl_as.offer_status","reviewed");

		$this->db->order_by($sort_col, $sort_dir);

		$this->db->page($page, $page_size);

		return $this->db->get("", 0);
		
	}

	public function getUserNameById($b_user_id_seller) {
		$this->db->select_as($this->__decrypt('fnama'), 'fnama');
		$this->db->from($this->tbl5, $this->tbl5_as);
		$this->db->where("id", $b_user_id_seller);
		return $this->db->get_first();
 	}
}