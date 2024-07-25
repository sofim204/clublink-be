<?php
class G_User_Referral_Code_Model extends JI_Model {
	var $tbl = 'b_user';
	var $tbl_as = 'bu';
	var $tbl2 = 'b_user_alamat';
	var $tbl2_as = 'bua';

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	private function __joinTbl1(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl2_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl_as.id","=","$this->tbl2_as.b_user_id");
		return $cps;
	}

	public function update($id, $du){
		if(!is_array($du)) return 0;
		$this->db->where("id", $id);
    	return $this->db->update($this->tbl, $du, 0);
	}

	public function set($di) {
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl, $di, 0, 0);
	}

	public function getLastId() {
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function del($id) {
		$this->db->where("id", $id);
		return $this->db->delete($this->tbl);
	}

	public function getAll($page=0, $pagesize=10, $sortCol="sku", $sortDir="DESC", $keyword="", $from_date="", $to_date="", $user_status="") {
		$this->db->flushQuery();
		// $this->db->select_as("ROW_NUMBER() OVER (ORDER BY id desc)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
        $this->db->select_as("$this->tbl_as.kode_referral", "kode_referral", 0);
        // $this->db->select_as("$this->tbl_as.b_user_id_recruiter", "b_user_id_recruiter", 0);
		// $this->db->select_as("(SELECT COUNT($this->tbl_as.b_user_id_recruiter) FROM b_user)", "b_user_id_recruiter", 0);
		$this->db->select_as("$this->tbl_as.total_recruited", "total_recruited", 0);
		$this->db->select_as("$this->tbl_as.bdate", "bdate", 0);
		// $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl_as.register_place_alamat2").", '-')", "register_place_alamat2", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		
		if (strlen($from_date)==10 && strlen($to_date)==10) {
			$this->db->between("DATE($this->tbl_as.bdate)", "DATE('$from_date')", "DATE('$to_date')");
		} else if (strlen($from_date)==10 && strlen($to_date)!=10) {
			$this->db->where_as("DATE($this->tbl_as.bdate)", "DATE('$from_date')", 'AND', '>=');
		} else if (strlen($from_date)!=10 && strlen($to_date)==10) {
			$this->db->where_as("DATE($this->tbl_as.bdate)", "DATE('$to_date')", 'AND', '<=');
		}

		if(strlen($user_status)>0) {
			$this->db->where_as("$this->tbl_as.is_active", $this->db->esc($user_status));
		}

		if(mb_strlen($keyword)>0) {
			$this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 0);
			$this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.email").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
			$this->db->where_as("$this->tbl_as.kode_referral", addslashes($keyword), "OR", "%like%", 0, 1);
		}

		// $this->db->group_by("$this->tbl_as.id");
		// $this->db->order_by("$this->tbl_as.cdate", "DESC");

		$this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
		return $this->db->get("object", 0);
	}

	public function countAll($keyword="", $from_date="", $to_date="", $user_status="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl, $this->tbl_as);

		if (strlen($from_date)==10 && strlen($to_date)==10) {
			$this->db->between("DATE($this->tbl_as.bdate)", "DATE('$from_date')", "DATE('$to_date')");
		} else if (strlen($from_date)==10 && strlen($to_date)!=10) {
			$this->db->where_as("DATE($this->tbl_as.bdate)", "DATE('$from_date')", 'AND', '>=');
		} else if (strlen($from_date)!=10 && strlen($to_date)==10) {
			$this->db->where_as("DATE($this->tbl_as.bdate)", "DATE('$to_date')", 'AND', '<=');
		}

		if(strlen($user_status)>0) {
			$this->db->where_as("$this->tbl_as.is_active", $this->db->esc($user_status));
		}

		if(mb_strlen($keyword)>0) {
			$this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 0);
			$this->db->where_as("$this->tbl_as.kode_referral", addslashes($keyword), "OR", "%like%", 0, 1);
		}
		
		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getById($id){
		$this->db->where("id", $id);
		return $this->db->get_first();
	}

	public function countDetailAll($keyword="", $b_user_id_recruiter="", $referral_type="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl, $this->tbl_as);

		$this->db->where_as("$this->tbl_as.b_user_id_recruiter", $this->db->esc($b_user_id_recruiter), "AND", "=", 0, 0);
		// $this->db->where_as("$this->tbl_as.is_confirmed", $this->db->esc(1), "AND", "=", 0, 0); // right now show all, not by verified email
		$this->db->where_as("$this->tbl_as.b_user_id_recruiter", $this->db->esc(0), "AND", "!=", 0, 0);

		if(strlen($referral_type)>0) {
			$this->db->where_as("$this->tbl_as.referral_type", $this->db->esc($referral_type));
		}

		// if(mb_strlen($keyword)>0) {
        //     $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.fnama").'USING "utf8" ) ', "$keyword", "OR", "%like%", 1, 0);
		// 	$this->db->where_as("$this->tbl_as.referral_type", "$keyword", "OR", "%like%", 0, 1);
		// }
		
		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getDetailAll($page=0, $pagesize=10, $sortCol="sku", $sortDir="DESC", $keyword="", $b_user_id_recruiter="", $referral_type="") {
		$this->db->flushQuery();
		// $this->db->select_as("ROW_NUMBER() OVER (ORDER BY b_user_id_recruiter desc)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.b_user_id_recruiter", "b_user_id_recruiter", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
		$this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as("$this->tbl_as.referral_type", "referral_type", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		// $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl_as.register_place_alamat2").", '')", "register_place_alamat2", 0);
		$this->db->select_as("CONCAT($this->tbl_as.register_place_kelurahan, ', ', $this->tbl_as.register_place_kecamatan, ', ', $this->tbl_as.register_place_kabkota)", "register_place_all", 0);
		$this->db->from($this->tbl, $this->tbl_as);

		$this->db->where_as("$this->tbl_as.b_user_id_recruiter", $this->db->esc($b_user_id_recruiter), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.b_user_id_recruiter", $this->db->esc(0), "AND", "!=", 0, 0);
		// $this->db->where_as("$this->tbl_as.is_confirmed", $this->db->esc(1), "AND", "=", 0, 0);

		if(strlen($referral_type)>0) {
			$this->db->where_as("$this->tbl_as.referral_type", $this->db->esc($referral_type));
		}

		// if(mb_strlen($keyword)>0) {
		// 	$this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.fnama").'USING "utf8" ) ', "$keyword", "OR", "%like%", 1, 0);
		// 	$this->db->where_as("$this->tbl_as.referral_type", "$keyword", "OR", "%like%", 0, 1);
		// }

		// $this->db->group_by("$this->tbl_as.id");
		// $this->db->order_by("$this->tbl_as.cdate", "DESC");

		$this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
		return $this->db->get("object", 0);
	}
}
