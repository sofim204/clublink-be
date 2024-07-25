<?php
class I_Group_Post_Attachment_Attendance_Sheet_Member_Model extends JI_Model{

	var $tbl = 'i_group_post_attachment_attendance_sheet_member';
	var $tbl_as = 'igpaasm';
    var $tbl2 = 'b_user';
    var $tbl2_as = 'bu';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
        return $composites;
    }

    public function getTblAs()
    {
        return $this->tbl_as;
    }

	public function set($di){
		return $this->db->insert($this->tbl, $di, 0, 0);
	}

	// public function set2($di){
	// 	if(!is_array($di)) return 0;
	// 	return $this->db->insert_ignore($this->tbl,$di,0,0);
	// }

    public function setMass($ds)
    {
        return $this->db->insert_multi($this->tbl, $ds, 0);
    }

	public function update($nation_code, $id, $du){
		if(!is_array($du)) return 0;
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->update($this->tbl,$du,0);
	}

	public function updateByAttendancesheetid($nation_code,$i_group_post_attachment_attendance_sheet_id, $du){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("i_group_post_attachment_attendance_sheet_id", $i_group_post_attachment_attendance_sheet_id);
		return $this->db->update($this->tbl,$du,0);
	}

	public function updateByPostId($nation_code,$i_group_post_id, $du){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("i_group_post_id",$i_group_post_id);
		return $this->db->update($this->tbl,$du,0);
	}

	// public function del($id){
	// 	$this->db->where("id",$id);
	// 	return $this->db->delete($this->tbl);
	// }

	// public function delByIdCommunityId($nation_code,$id,$c_community_id, $jenis){
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where("id",$id);
	// 	$this->db->where("c_community_id",$c_community_id);
	// 	$this->db->where("jenis",$jenis);
	// 	return $this->db->delete($this->tbl);
	// }

	// public function delByProdukIds($nation_code,$c_produk_ids){
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where_in("c_produk_id",$c_produk_ids);
	// 	return $this->db->delete($this->tbl);
	// }

	public function delMemberByAttendancesheetidJenisNotInList($nation_code,$i_group_post_attachment_attendance_sheet_id, $jenis, $custom_array=array()){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("i_group_post_attachment_attendance_sheet_id",$i_group_post_attachment_attendance_sheet_id);
		$this->db->where("jenis",$jenis);
		if($jenis == "member" && $custom_array){
			$this->db->where_in("b_user_id",$custom_array, 1);
		}else if($jenis == "guest" && $custom_array){
			$this->db->where_in("guest_fnama",$custom_array, 1);
		}
		return $this->db->delete($this->tbl);
	}

	public function getById($nation_code, $id){
		$this->db->select()
			->from($this->tbl,$this->tbl_as)
			->where("nation_code",$nation_code)
			->where("id",$id);
		return $this->db->get_first();
	}

	// public function countByCommunityIdJenisConvertStatusNotEqual($nation_code, $community_id, $jenis="video", $convert_status="uploading"){
	// 	$this->db->select_as("COUNT(*)",'total',0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where("c_community_id",$community_id);
	// 	$this->db->where("jenis",$jenis);
	// 	$this->db->where("convert_status",$convert_status,"AND", "!=");
	// 	$d = $this->db->get_first();
	// 	if(isset($d->total)) return $d->total;
	// 	return 0;
	// }

	// public function getAll($nation_code, $jenis="image"){
	// 	$this->db->select_as("*,$this->tbl_as.id",'id',0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where("nation_code",$nation_code);
		
	// 	if($jenis != ""){
	// 		$this->db->where("jenis",$jenis);
	// 	}

	// 	$this->db->where("is_active",1);

	// 	$this->db->order_by("id","ASC");

	// 	return $this->db->get();
	// }

	public function countByAttendanceId($nation_code, $keyword="", $i_group_post_attachment_attendance_sheet_id, $getType="detail", $present_or_absent="", $jenis="all"){
        $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
		$this->db->from($this->tbl,$this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
		$this->db->where_as("$this->tbl_as.i_group_post_attachment_attendance_sheet_id", $this->db->esc($i_group_post_attachment_attendance_sheet_id));
		$this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));

        if (mb_strlen($keyword)>0) {
            $this->db->where_as("$this->tbl_as.guest_fnama", addslashes($keyword), 'or', '%like%', 1, 0);
            $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl2_as.band_fnama").',"")', addslashes($keyword), 'and', '%like%', 0, 1);
        }

		if($getType == "list"){
			// $this->db->where_as("$this->tbl_as.present_or_absent", $this->db->esc(""));
		}else if($getType == "detail"){
			$this->db->where_as("$this->tbl_as.present_or_absent", $this->db->esc($present_or_absent));
		}

		if($jenis != "all"){
			$this->db->where_as("$this->tbl_as.jenis", $this->db->esc($jenis));
		}

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return "0";
	}

	public function getByAttendanceId($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_dir="DESC", $keyword="", $i_group_post_attachment_attendance_sheet_id, $getType="detail", $present_or_absent="", $sort_members="", $jenis="all", $b_user_id=""){
		$this->db->select_as("$this->tbl_as.id",'member_id',0);
		$this->db->select_as("$this->tbl_as.b_user_id",'b_user_id',0);
        $this->db->select_as("IF($this->tbl_as.b_user_id = '0', $this->tbl_as.guest_fnama, COALESCE(".$this->__decrypt("$this->tbl2_as.band_fnama").",''))", "b_user_band_nama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.band_image,'')", "b_user_band_image", 0);
		$this->db->select_as("$this->tbl_as.custom_text",'custom_text',0);
		$this->db->select_as("$this->tbl_as.jenis",'jenis',0);
		$this->db->select_as("$this->tbl_as.present_or_absent",'present_or_absent',0);
		$this->db->from($this->tbl,$this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
		$this->db->where_as("$this->tbl_as.i_group_post_attachment_attendance_sheet_id", $this->db->esc($i_group_post_attachment_attendance_sheet_id));
		$this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));

        if (mb_strlen($keyword)>0) {
            $this->db->where_as("$this->tbl_as.guest_fnama", addslashes($keyword), 'or', '%like%', 1, 0);
            $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl2_as.band_fnama").',"")', addslashes($keyword), 'and', '%like%', 0, 1);
        }

		if($getType == "list"){
			// $this->db->where_as("$this->tbl_as.present_or_absent", $this->db->esc(""));
			$this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "!=");
		}else if($getType == "detail"){
			$this->db->where_as("$this->tbl_as.present_or_absent", $this->db->esc($present_or_absent));
		}

		if($jenis != "all"){
			$this->db->where_as("$this->tbl_as.jenis", $this->db->esc($jenis));
		}

		if($sort_members == "Attending members first"){
        	$this->db->order_by("$this->tbl_as.present_or_absent", "DESC");
        }else if($sort_members == "Not attending members first"){
        	$this->db->order_by("$this->tbl_as.present_or_absent", "ASC");
        }
        // $this->db->order_by('LOWER(CAST('.$this->__decrypt("$this->tbl_as.alamat2").' AS CHAR(50)))', addslashes(strtolower($pelangganAddress->alamat2)), 'and', '%like%', 1, 1);
    	$this->db->order_by("IF($this->tbl_as.b_user_id = '0', $this->tbl_as.guest_fnama, COALESCE(".$this->__decrypt("$this->tbl2_as.band_fnama").",''))", "ASC");
        if($page != 0 && $page_size != 0){
        	$this->db->page($page, $page_size);
		}
		return $this->db->get();
	}

	public function getByAttendanceidUserid($nation_code, $i_group_post_attachment_attendance_sheet_id, $b_user_id){
		$this->db->select_as("$this->tbl_as.id",'member_id',0);
		$this->db->select_as("$this->tbl_as.b_user_id",'b_user_id',0);
        $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl2_as.band_fnama").")", "b_user_band_nama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.band_image,'')", "b_user_band_image", 0);
		$this->db->select_as("$this->tbl_as.custom_text",'custom_text',0);
		$this->db->select_as("$this->tbl_as.jenis",'jenis',0);
		$this->db->select_as("$this->tbl_as.present_or_absent",'present_or_absent',0);
		$this->db->from($this->tbl,$this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
		$this->db->where_as("$this->tbl_as.i_group_post_attachment_attendance_sheet_id", $this->db->esc($i_group_post_attachment_attendance_sheet_id));
		$this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
		$this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
		return $this->db->get_first();
	}

	// public function getByIdCommunityId($nation_code, $c_community_id, $id, $jenis="image", $convert_status=""){
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where("c_community_id",$c_community_id);
	// 	$this->db->where("id",$id);
	// 	$this->db->where("jenis",$jenis);
	// 	if($convert_status != ""){
	// 		$this->db->where("convert_status",$convert_status);
	// 	}
	// 	return $this->db->get_first('',0);
	// }

    public function checkId($nation_code, $id)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }
}
