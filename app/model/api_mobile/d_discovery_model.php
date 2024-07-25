<?php
class D_Discovery_Model extends SENE_Model{
	var $tbl = 'd_discovery';
	var $tbl_as = 'dd';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	public function countAll($keyword="",$kategori_id="",$jenis=""){
		$this->db->select_as("COUNT(*)","total",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("is_visible",'1')->where("is_active",'1');

		if(strlen($kategori_id)) $this->db->where("b_kategori_id",$kategori_id);
		if(strlen($keyword)>2){
			$this->db->where('nama',$keyword,'or','%like%');
			$this->db->where('deskripsi',$keyword,'or','%like%',0,1);
		}
		//$this->db->nolimit();
		$d = $this->db->get('object',0);
		if(isset($d[0]->total)) return $d[0]->total;
		return 0;
	}
	public function getAll($page=1,$page_size=10,$sort_col="id",$sort_direction="ASC",$keyword="",$kategori_id="",$jenis=""){
    $this->db->select_as("$this->tbl_as.id",'id',0);
		$this->db->select("kategori");
		$this->db->select("harga_jual");
		$this->db->select("kondisi");
		$this->db->select("negara");
		$this->db->select("provinsi")->select("kabkota")->select("kecamatan");
		$this->db->select("latitude")->select("longitude");
		$this->db->select("foto")->select("thumb");
		$this->db->select_as("$this->tbl_as.deskripsi",'deskripsi',0);
		$this->db->select_as("$this->tbl_as.nama",'nama',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where_as("$this->tbl_as.is_visible",'1')->where_as("$this->tbl_as.is_active",'1');

		if(strlen($kategori_id)) $this->db->where("b_kategori_id",$kategori_id);
		if(strlen($keyword)>2){
			$this->db->where('nama',$keyword,'or','%like%');
			$this->db->where('deskripsi',$keyword,'or','%like%',0,1);
		}
		$this->db->order_by($sort_col,$sort_direction)->page($page,$page_size);
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
	public function getByKategoriId($b_kategori_ids,$page=1,$page_size=10,$sort_col="id",$sort_direction="ASC",$keyword=""){
		$this->db->select();
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("is_visible",'1')->where("is_active",'1');
		if(is_array($b_kategori_ids) && count($b_kategori_ids)){
			$this->db->where_in('b_kategori_id',$b_kategori_ids);
		}
		if(strlen($keyword)>2){
			$this->db->where('sku',$keyword,'or','%like%',1,0);
			$this->db->where('nama',$keyword,'or','%like%');
			$this->db->where('deskripsi_singkat',$keyword,'or','%like%');
			$this->db->where('deskripsi',$keyword,'or','%like%',0,1);
		}
		$this->db->order_by($sort_col,$sort_direction)->page($page,$page_size);
		return $this->db->get('object',0);
	}

	public function countByKategoriId($b_kategori_ids,$keyword=""){
		$this->db->select_as("COUNT(*)","total",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("is_visible",'1')->where("is_active",'1');
		if(is_array($b_kategori_ids) && count($b_kategori_ids)){
			$this->db->where_in('b_kategori_id',$b_kategori_ids);
		}
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

	//pids array of object
	//pids[0]->id & pids[0]->qty
	public function setTerjuals($pids){
		if(is_array($pids) && count($pids)){
			$sql = '';
			//building multi query
			foreach($pids as $pid){
				$sql .= 'UPDATE '.$this->tbl.' SET terjual = terjual + '.$pid->qty.', stok = stok - '.$pid->qty.' WHERE id = '.$pid->id.';';
				$sql .= 'UPDATE '.$this->tbl.' SET sales_rate = ((sales_count / terjual)*100) WHERE id = '.$pid->id.';';
			}
			$this->db->query_multi($sql);
		}
	}
	public function getByProdukIds($ids){
		$this->db->where_in('id',$ids);
		return $this->db->get();
	}
	public function getByIds($ids){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where_in('id',$ids);
		return $this->db->get();
	}

	public function getHomePage($page=1,$page_size=10,$sort_col="id",$sort_direction="ASC",$keyword="",$kategori_id="",$jenis=""){
		$this->db->select_as("$this->tbl_as.id",'id',0);
		$this->db->select_as("$this->tbl2_as.nama",'kategori',0);
		$this->db->select("harga_jual");
		$this->db->select("kondisi");
		$this->db->select("negara");
		$this->db->select("provinsi")->select("kabkota")->select("kecamatan");
		$this->db->select("latitude")->select("longitude");
		$this->db->select("foto")->select("thumb");
		$this->db->select_as("$this->tbl_as.deskripsi",'deskripsi',0);
		$this->db->select_as("$this->tbl_as.nama",'nama',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join($this->tbl2,$this->tbl2_as,"id",$this->tbl_as,"b_kategori_id",'');
		$this->db->where_as("$this->tbl_as.is_visible",'1')->where_as("$this->tbl_as.is_active",'1');

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
		$this->db->order_by($sort_col,$sort_direction)->page($page,$page_size);
		return $this->db->get('object',0);
	}
}
