<?php
class I_Group_Post_Attachment_Attendance_Sheet_Model extends JI_Model{

	var $tbl = 'i_group_post_attachment_attendance_sheet';
	var $tbl_as = 'igpaas';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	public function set($dix){
		return $this->db->insert($this->tbl, $dix, 0, 0);
	}

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

	public function updateByPostId($nation_code,$i_group_post_id, $du){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("i_group_post_id",$i_group_post_id);
		return $this->db->update($this->tbl,$du,0);
	}

	// // public function del($id){
	// // 	$this->db->where("id",$id);
	// // 	return $this->db->delete($this->tbl);
	// // }

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

	public function getById($nation_code, $id){
		$this->db->select()
			->from($this->tbl,$this->tbl_as)
			->where("nation_code",$nation_code)
			->where("id",$id);
		return $this->db->get_first();
	}

	// public function countByProdukId($nation_code, $c_produk_id){
	// 	$this->db->select_as("COUNT(*)",'total',0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where("c_produk_id",$c_produk_id);
	// 	$d = $this->db->get_first();
	// 	if(isset($d->total)) return $d->total;
	// 	return 0;
	// }

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

	// public function getByGroupIdPostId($nation_code, $i_group_id, $i_group_post_id, $getType="all", $type=""){
	// 	$this->db->select_as("*,$this->tbl_as.id",'id',0);
    //     $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl_as.location_address").",'')", "location_address", 0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where("i_group_id",$i_group_id);
	// 	$this->db->where("i_group_post_id",$i_group_post_id);

	// 	if($type != ""){
	// 		$this->db->where("jenis",$type);
	// 	}

	// 	$this->db->where("is_active",1);

	// 	$this->db->order_by("cdate","ASC");

	// 	if($getType == "first"){
	// 		return $this->db->get_first();
	// 	}else{
	// 		return $this->db->get();
	// 	}
	// }

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

	// public function getByProdukIds($nation_code, $c_produk_ids){
	// 	if(!is_array($c_produk_ids)) return array();
	// 	$this->db->select_as("$this->tbl_as.id",'id',0);
	// 	$this->db->select_as("$this->tbl_as.c_produk_id",'c_produk_id',0);
	// 	$this->db->select_as("$this->tbl_as.url",'url',0);
	// 	$this->db->select_as("$this->tbl_as.url_thumb",'url_thumb',0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
 	//  $this->db->where("nation_code",$nation_code);
	// 	$this->db->where_in("c_produk_id",$c_produk_ids);
	// 	return $this->db->get();
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
