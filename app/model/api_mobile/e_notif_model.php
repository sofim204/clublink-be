<?php
class E_Notif_Model extends SENE_Model{
	var $tbl = 'e_notif';
	var $tbl_as = 'en';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	public function countAll($keyword="",$kategori_id="",$jenis=""){
		$this->db->select_as("COUNT(*)","total",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("is_visible",'1')->where("is_active",'1');

		if(strlen($jenis)>3){
			$jenis = strtolower($jenis);
			if($jenis == 'jasa'){
				$jenis = 'jasa';
			}else if($jenis == 'paket'){
				$jenis = 'paket';
			}else{
				$jenis = 'barang';
			}
			$this->db->where('jenis',$jenis,'and','%like%',0,0);
		}

		if(strlen($kategori_id)) $this->db->where("b_kategori_id",$kategori_id);
		if(strlen($keyword)>2){
			$this->db->where('sku',$keyword,'or','%like%',1,0);
			$this->db->where('nama',$keyword,'or','%like%');
			$this->db->where('deskripsi_singkat',$keyword,'or','%like%');
			$this->db->where('deskripsi',$keyword,'or','%like%',0,1);
		}
		//$this->db->nolimit();
		$d = $this->db->get('object',0);
		if(isset($d[0]->total)) return $d[0]->total;
		return 0;
	}
	public function getAll($page=1,$page_size=10,$sort_col="id",$sort_direction="ASC",$keyword="",$kategori_id="",$jenis=""){
		$this->db->select_as("$this->tbl_as.id",'id',0);
    $this->db->select("judul");
    $this->db->select("isi");
    $this->db->select("thumb");
    $this->db->select("cdate")->select("ldate");
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("is_active",'1');

		return $this->db->get('object',0);
	}
	public function getBySlug($slug){
		$this->db->select_as('cp`.*, av.`nama','vendor_nama',1)
						->from($this->tbl,$this->tbl_as)
						->where("slug",$slug)
						->join("a_vendor","av","id",$this->tbl_as,"id","left");
		return $this->db->get_first();
	}
	public function getRelated($pid){
		$this->db->select()->from($this->tbl,$this->tbl_as)->where("id",$pid,'AND','!=')->where("is_active",'1')->limit(6);
		return $this->db->get();
	}
	public function getById($pid){
		$this->db->from($this->tbl)->where('id',$pid)->where('is_active',1);
		return $this->db->get_first();
  }
}
