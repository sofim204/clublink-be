<?php
class C_Produk_Foto_Model extends JI_Model{
	var $tbl = 'c_produk_foto';
	var $tbl_as = 'cpf';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	// public function getLastId($nation_code,$c_produk_id, $jenis="foto"){
	// 	$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
	// 	$this->db->from($this->tbl, $this->tbl_as);
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where("c_produk_id",$c_produk_id);
	// 	$this->db->where("jenis",$jenis);
	// 	$d = $this->db->get_first('',0);
	// 	if(isset($d->last_id)) return $d->last_id;
	// 	return 0;
	// }

	// public function getById($nation_code, $c_produk_id, $id){
	// 	$this->db->select()
	// 					->from($this->tbl,$this->tbl_as)
	// 					->where("nation_code",$nation_code)
	// 					->where("c_produk_id",$c_produk_id)
	// 					->where("id",$id);
	// 	return $this->db->get_first();
	// }

	// public function countByProdukId($nation_code, $c_produk_id){
	// 	$this->db->select_as("COUNT(*)",'total',0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where("c_produk_id",$c_produk_id);
	// 	$d = $this->db->get_first();
	// 	if(isset($d->total)) return $d->total;
	// 	return 0;
	// }

	// public function getByProdukId($nation_code, $c_produk_id, $jenis="foto"){
	// 	$this->db->select_as("$this->tbl_as.nation_code",'nation_code',0);
	// 	$this->db->select_as("$this->tbl_as.c_produk_id",'c_produk_id',0);
	// 	$this->db->select_as("$this->tbl_as.id",'id',0);
	// 	$this->db->select_as("$this->tbl_as.url",'url',0);
	// 	$this->db->select_as("$this->tbl_as.url_thumb",'url_thumb',0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where("c_produk_id",$c_produk_id);
	// 	if($jenis != "all"){
	// 		$this->db->where("jenis",$jenis);
	// 	}
	// 	return $this->db->get();
	// }

	// public function getLastByProdukId($nation_code,$c_produk_id){
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where("c_produk_id",$c_produk_id);
	// 	$this->db->order_by("id","desc");
	// 	return $this->db->get_first('',0);
	// }

	// public function getByIdProdukId($nation_code, $c_produk_id, $id){
	// 	$this->db->select_as("$this->tbl_as.id",'id',0);
	// 	$this->db->select_as("$this->tbl_as.c_produk_id",'c_produk_id',0);
	// 	$this->db->select_as("$this->tbl_as.url",'url',0);
	// 	$this->db->select_as("$this->tbl_as.url_thumb",'url_thumb',0);
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where("id",$id);
	// 	$this->db->where("c_produk_id",$c_produk_id);
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

	// public function set($dix){
	// 	return $this->db->insert($this->tbl, $dix, 0, 0);
	// }

	// public function set2($di){
	// 	if(!is_array($di)) return 0;
	// 	return $this->db->insert_ignore($this->tbl,$di,0,0);
	// }

	public function update($nation_code, $c_produk_id, $id, $jenis="foto", $du){
		if(!is_array($du)) return 0;
		$this->db->where_as("nation_code",$nation_code);
		$this->db->where("c_produk_id",$c_produk_id);
		$this->db->where("id",$id);
		$this->db->where("jenis",$jenis);
		return $this->db->update($this->tbl,$du,0);
	}

	// public function del($id){
	// 	$this->db->where("id",$id);
	// 	return $this->db->delete($this->tbl);
	// }

	public function delByIdProdukIdJenis($nation_code,$id,$c_produk_id, $jenis="foto"){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		$this->db->where("c_produk_id",$c_produk_id);
		$this->db->where("jenis",$jenis);
		return $this->db->delete($this->tbl);
	}

	// public function delByProdukId($nation_code,$c_produk_id){
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where("c_produk_id",$c_produk_id);
	// 	return $this->db->delete($this->tbl);
	// }

	// public function delByProdukIds($nation_code,$c_produk_ids){
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where_in("c_produk_id",$c_produk_ids);
	// 	return $this->db->delete($this->tbl);
	// }

	public function getAll($nation_code, $jenis="foto", $convert_status="waiting", $compare="", $checkTempUrl="no", $limit="0"){
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
