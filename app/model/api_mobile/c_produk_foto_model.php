<?php
class C_Produk_Foto_Model extends JI_Model{
	var $tbl = 'c_produk_foto';
	var $tbl_as = 'cpf';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	public function getLastId($nation_code,$c_produk_id, $jenis="foto"){
		// $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		// $this->db->from($this->tbl, $this->tbl_as);
		// $this->db->where("nation_code",$nation_code);
		// $this->db->where("c_produk_id",$c_produk_id);
		// $this->db->where("jenis",$jenis);
		// $d = $this->db->get_first('',0);
		// if(isset($d->last_id)) return $d->last_id;
		// return 0;
	    $sql ="SELECT COALESCE(MAX(id),0)+1 AS id FROM `".$this->tbl."` WHERE id >= (SELECT COALESCE(MAX(id),0) FROM `".$this->tbl."` WHERE nation_code = '".$nation_code."' AND  c_produk_id = '".$c_produk_id."' AND  jenis = '".$jenis."') AND nation_code = '".$nation_code."' AND  c_produk_id = '".$c_produk_id."' AND  jenis = '".$jenis."' FOR UPDATE;";
	    return $this->db->query($sql)[0]->id;
	}

	public function getById($nation_code, $c_produk_id, $id){
		$this->db->select()
						->from($this->tbl,$this->tbl_as)
						->where("nation_code",$nation_code)
						->where("c_produk_id",$c_produk_id)
						->where("id",$id);
		return $this->db->get_first();
	}

	public function countByProdukId($nation_code, $c_produk_id, $jenis="foto"){
		$this->db->select_as("COUNT(*)",'total',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_produk_id",$c_produk_id);
		if($jenis != "all"){
			$this->db->where("jenis",$jenis);
		}
		$d = $this->db->get_first();
		if(isset($d->total)) return $d->total;
		return 0;
	}

	public function getByProdukId($nation_code, $c_produk_id, $jenis="foto"){
		$this->db->select_as("$this->tbl_as.nation_code",'nation_code',0);
		$this->db->select_as("$this->tbl_as.c_produk_id",'c_produk_id',0);
		$this->db->select_as("$this->tbl_as.id",'id',0);
		$this->db->select_as("$this->tbl_as.url",'url',0);
		$this->db->select_as("$this->tbl_as.url_thumb",'url_thumb',0);
		$this->db->select_as("$this->tbl_as.jenis",'jenis',0);
		$this->db->select_as("$this->tbl_as.convert_status",'convert_status',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_produk_id",$c_produk_id);
		if($jenis != "all"){
			$this->db->where("jenis",$jenis);
		}
		return $this->db->get();
	}

	//by Donny Dennison - 22 july 2022 10:45
	//add response parameter have_video in api product, product/detail, homepage, seller/product, product_automotive, wishlist
	public function countByProdukIdJenisConvertStatusNotEqual($nation_code, $c_produk_id, $jenis="video", $convert_status="uploading"){
		$this->db->select_as("COUNT(*)",'total',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_produk_id",$c_produk_id);
		$this->db->where("jenis",$jenis);
		$this->db->where("convert_status",$convert_status,"AND", "!=");
		$d = $this->db->get_first();
		if(isset($d->total)) return $d->total;
		return 0;
	}

	public function getByProdukIdJenisConvertStatus($nation_code, $c_produk_id, $jenis="foto", $convert_status="uploading"){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_produk_id",$c_produk_id);
		$this->db->where("jenis",$jenis);
		$this->db->where("convert_status",$convert_status);
		$this->db->order_by("id","asc");
		return $this->db->get_first('',0);
	}

	public function getLastByProdukId($nation_code,$c_produk_id, $jenis="foto"){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_produk_id",$c_produk_id);
		$this->db->where("jenis",$jenis);
		$this->db->order_by("id","desc");
		return $this->db->get_first('',0);
	}

	public function getByIdProdukId($nation_code, $c_produk_id, $id, $jenis="foto"){
		$this->db->select_as("$this->tbl_as.id",'id',0);
		$this->db->select_as("$this->tbl_as.c_produk_id",'c_produk_id',0);
		$this->db->select_as("$this->tbl_as.url",'url',0);
		$this->db->select_as("$this->tbl_as.url_thumb",'url_thumb',0);
		$this->db->select_as("$this->tbl_as.jenis",'jenis',0);
		$this->db->select_as("$this->tbl_as.convert_status",'convert_status',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		$this->db->where("c_produk_id",$c_produk_id);
		$this->db->where("jenis",$jenis);
		return $this->db->get_first('',0);
	}

	public function getByProdukIds($nation_code, $c_produk_ids){
		if(!is_array($c_produk_ids)) return array();
		$this->db->select_as("$this->tbl_as.id",'id',0);
		$this->db->select_as("$this->tbl_as.c_produk_id",'c_produk_id',0);
		$this->db->select_as("$this->tbl_as.url",'url',0);
		$this->db->select_as("$this->tbl_as.url_thumb",'url_thumb',0);
		$this->db->select_as("$this->tbl_as.jenis",'jenis',0);
		$this->db->select_as("$this->tbl_as.convert_status",'convert_status',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where_in("c_produk_id",$c_produk_ids);
		return $this->db->get();
	}

	public function set($dix){
		return $this->db->insert($this->tbl, $dix, 0, 0);
	}

	public function set2($di){
		if(!is_array($di)) return 0;
		return $this->db->insert_ignore($this->tbl,$di,0,0);
	}

	public function update($nation_code, $c_produk_id, $id, $jenis="foto", $du){
		if(!is_array($du)) return 0;
		$this->db->where_as("nation_code",$nation_code);
		$this->db->where("c_produk_id",$c_produk_id);
		$this->db->where("id",$id);
		$this->db->where("jenis",$jenis);
		return $this->db->update($this->tbl,$du,0);
	}

	public function del($id){
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}

	public function delByIdProdukId($nation_code,$id,$c_produk_id, $jenis="foto"){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		$this->db->where("c_produk_id",$c_produk_id);
		$this->db->where("jenis",$jenis);
		return $this->db->delete($this->tbl);
	}

	public function delByProdukId($nation_code,$c_produk_id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_produk_id",$c_produk_id);
		return $this->db->delete($this->tbl);
	}

	public function delByProdukIds($nation_code,$c_produk_ids){
		$this->db->where("nation_code",$nation_code);
		$this->db->where_in("c_produk_id",$c_produk_ids);
		return $this->db->delete($this->tbl);
	}

}
