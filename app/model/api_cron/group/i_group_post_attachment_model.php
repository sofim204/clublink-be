<?php
class I_Group_Post_Attachment_Model extends JI_Model{

	var $tbl = 'i_group_post_attachment';
	var $tbl_as = 'igpa';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	// public function getTblAs()
    // {
    //     return $this->tbl_as;
    // }

	// public function set($dix){
    //     if (isset($dix['location_address'])) {
    //         if (strlen($dix['location_address'])) {
    //             $dix['location_address'] = $this->__encrypt($dix['location_address']);
    //         }
    //     }
	// 	return $this->db->insert($this->tbl, $dix, 0, 0);
	// }

	// public function set2($di){
	// 	if(!is_array($di)) return 0;
	// 	return $this->db->insert_ignore($this->tbl,$di,0,0);
	// }

	public function update($nation_code, $id, $du){
		if(!is_array($du)) return 0;
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->update($this->tbl,$du,0);
	}

	// public function updateByPostId($nation_code,$i_group_post_id, $du){
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where("i_group_post_id",$i_group_post_id);
	// 	return $this->db->update($this->tbl,$du,0);
	// }

	public function del($id){
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}

	// public function delByPostIdJenis($nation_code,$i_group_post_id, $jenis){
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where("i_group_post_id",$i_group_post_id);
	// 	$this->db->where("jenis",$jenis);
	// 	return $this->db->delete($this->tbl);
	// }

	public function getAll($nation_code, $jenis="foto", $convert_status="waiting", $compare="", $checkTempUrl="no", $limit="0") {
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("convert_status",$convert_status);
		$this->db->where("is_active",1);

		if($jenis != "all"){
			$this->db->where("jenis",$jenis);
		}

		if($compare != ""){
			$this->db->where("cdate", $compare, "AND", "<=");
		}
		
		if($checkTempUrl == "yes"){
			$this->db->where_as("$this->tbl_as.tmp_url", $this->db->esc(""), "AND", "!=");
		}

		if($limit != "0"){
			$this->db->limit($limit);
			$this->db->order_by("$this->tbl_as.cdate","ASC");
		}

		return $this->db->get();
	}
}
