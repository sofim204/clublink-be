<?php
class Promosi extends JI_Controller{

	public function __construct(){
    parent::__construct();
		//$this->setTheme('frontx');
		$this->load("api_admin/c_produk_model",'cpm');
		$this->load("api_admin/c_promosi_model",'cprm');
		$this->load("api_admin/c_promosi_rule_model",'cprrm');
		$this->load("api_admin/c_promosi_produk_model",'cprpm');
		$this->current_parent = 'penjualan';
		$this->current_page = 'penjualan_promosi';
	}
	public function index(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}

		$draw = $this->input->post("draw");
		$sval = $this->input->post("search");
		$sSearch = $this->input->post("sSearch");
		$sEcho = $this->input->post("sEcho");
		$page = $this->input->post("iDisplayStart");
		$pagesize = $this->input->post("iDisplayLength");

		$iSortCol_0 = $this->input->post("iSortCol_0");
		$sSortDir_0 = $this->input->post("sSortDir_0");

		$b_kategori_id = (int) $this->input->request('b_kategori_id');
		$terpilih = $this->input->request('terpilih');
		if(strlen($terpilih)>0){
			$terpilih = explode(',',$terpilih);
		}else{
			$terpilih = array();
		}

		//var_dump($b_kategori_id);
		//die($b_kategori_id);

		$sortCol = "date";
		$sortDir = strtoupper($sSortDir_0);
		if(empty($sortDir)) $sortDir = "DESC";
		if(strtolower($sortDir) != "desc"){
			$sortDir = "ASC";
		}

		$tbl_as = $this->cprm->getTableAlias();
		$tbl2_as = $this->cprm->getTableAlias2();

		switch($iSortCol_0){
			case 0:
				$sortCol = "$tbl_as.id";
				break;
			case 1:
				$sortCol = "$tbl_as.jenis";
				break;
			case 2:
				$sortCol = "$tbl_as.nama";
				break;
			case 3:
				$sortCol = "$tbl_as.kode";
				break;
			case 4:
				$sortCol = "$tbl_as.adate";
				break;
			case 5:
				$sortCol = "$tbl_as.edate";
				break;
			case 6:
				$sortCol = "$tbl_as.prioritas";
				break;
			case 7:
				$sortCol = "$tbl_as.is_active";
				break;
			default:
				$sortCol = "$tbl_as.id";
		}

		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;

		$keyword = $sSearch;


		$this->status = '100';
		$this->message = 'Berhasil';
		$dcount = $this->cprm->countAll($keyword,array(),$b_kategori_id);
		$ddata = $this->cprm->getAll($page,$pagesize,$sortCol,$sortDir,$keyword,array(),$b_kategori_id);

		foreach($ddata as &$gd){
			if(isset($gd->utype)){
				$gd->utype = strtolower($gd->utype);
				if($gd->utype == 'persentase'){
					if(isset($gd->persentase)){
						$gd->utype = 'Disc. '.number_format($gd->persentase,2,',','.').'%';
					}
				}else if($gd->utype == 'nominal'){
					if(isset($gd->nominal)){
						$gd->utype = 'Disc. Rp'.number_format($gd->nominal,0,',','.').'';
					}
				}else if($gd->utype == 'gratis_semua'){
					if(isset($gd->nominal)){
						$gd->utype = 'Gratis Semuanya';
					}
				}else if($gd->utype == 'gratis_ongkir'){
					if(isset($gd->nominal)){
						$gd->utype = 'Free Ongkir';
					}
				}
			}if(isset($gd->kode)){
				if(empty($gd->kode)){
					$gd->kode = '-';
				}
			}

			if(isset($gd->adate)){
				if(empty($gd->adate) || $gd->adate == '0000-00-00 00:00:00'){
					$gd->adate = '-';
				}else{
					$gd->adate = $this->__dateIndonesia($gd->adate);
				}
			}
			if(isset($gd->edate)){
				if(empty($gd->edate) || $gd->edate == '0000-00-00 00:00:00'){
					$gd->edate = '-';
				}else{
					$gd->edate = $this->__dateIndonesia($gd->edate);
				}
			}
			if(isset($gd->max_jml)){
				if(!empty($gd->max_jml)){
					$gd->max_jml = $gd->max_jml.'x';
				}else{
					$gd->max_jml = 'tak terbatas';
				}
			}
			if(isset($gd->is_active)){
				if(!empty($gd->is_active)){
					$gd->is_active = '<span class="label label-success">Active</span>';
				}else{
					$gd->is_active = '<span class="label label-default">Not Active</span>';
				}
				if(isset($gd->is_gratis_order)){
					if(!empty($gd->is_gratis_order)) $gd->is_active .= '<span class="label label-info">Gratis</span>';
				}
				if(isset($gd->is_gratis_ongkir)){
					if(!empty($gd->is_gratis_ongkir)) $gd->is_active .= '<span class="label label-warning">Free Ongkir</span>';
				}
				if(isset($gd->is_chained)){
					if(empty($gd->is_chained)) $gd->is_active .= '<span class="label label-default">Not Chained</span>';
				}
			}
		}
		$another = array();
		$this->__jsonDataTable($ddata,$dcount);
	}

	public function tambah(){
		$d = $this->__init();
		$data = 0;
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}
		$di = $_POST;
		if(!isset($di['prioritas'])) $di['prioritas']=0;
		if(!isset($di['utype'])) $di['utype']='persentase';
		if(!isset($di['nama'])) $di['nama']='';
		if(strlen($di['nama'])>0){
			if($di['utype'] == 'gratis_semua'){
				$di['is_gratis_order'] = 1;
			}
			if($di['utype'] == 'gratis_ongkir'){
				$di['is_gratis_ongkir'] = 1;
			}
			if($di['utype'] == 'persentase'){
				$di['nominal'] = 0;
			}else if($di['utype'] == 'nominal'){
				$di['persentase'] = 0;
			}
			if(empty($di['prioritas'])) $di['prioritas'] = $this->cprm->getLatest();

			$res = $this->cprm->set($di);
			if($res){
				$this->status = 100;
				$this->message = 'Data baru berhasil ditambahkan';
				$data = $res;
			}else{
				$this->status = 900;
				$this->message = 'Tidak dapat menyimpan data baru, silakan coba beberapa saat lagi';
			}
		}
		$this->__json_out($data);
	}
	public function detail($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}
		$this->status = 100;
		$this->message = 'Berhasil';
		$data = $this->cprm->getById($id);
		$this->__json_out($data);
	}
	public function edit($id=""){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}
		$du = $_POST;

		$id = (int) $id;
		if(empty($id)){
			if(isset($du['id'])){
				$id = (int) $du['id'];
			}
			unset($du['id']);
		}
		if(!isset($du['nama'])) $di['nama'] = "";
		if($id>0 && strlen($du['nama'])>1){
			$res = $this->cprm->update($id,$du);
			if($res){
				$this->status = 100;
				$this->message = 'Perubahan berhasil diterapkan';
			}else{
				$this->status = 901;
				$this->message = 'Tidak dapat melakukan perubahan ke basis data';
			}
		}
		$this->__json_out($data);
	}
	public function hapus($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}
		$this->status = 100;
		$this->message = 'Berhasil';
		if($id>0){
			$res = $this->cprm->del($id);
			if(!$res){
				$this->status = 902;
				$this->message = 'Data gagal dihapus';
			}
		}
		$this->__json_out($data);
	}
	public function compile($c_promosi_id){
		$s = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}
		$c_promosi_id = (int) $c_promosi_id;
		if($c_promosi_id<=0){
			$this->status = 450;
			$this->message = 'ID Tidak valid';
			$this->__json_out($data);
			die();
		}
		$pids = array();
		$produks = array();
		$promosi = $this->cprm->getById($c_promosi_id);
		if(!isset($promosi->id)){
			$this->status = 451;
			$this->message = 'ID Promosi tidak valid';
			$this->__json_out($data);
			die();
		}
		$promosi_rules = $this->cprrm->getByPromosiId($c_promosi_id);
		foreach($promosi_rules as $prr){
			//$this->debug($prr);
			//die();
			if($prr->utype == 'produk'){
				$produk_data = $this->cpm->getByIds(array($prr->id_target_utype));
				foreach($produk_data as $pd){
					if(!isset($produks[$pd->id])){
						$p = new stdClass();
						$p->c_produk_id = $pd->id;
						$p->promo_jenis = $prr->promo_jenis;
						$p->promo_nilai = $prr->promo_nilai;
						$p->qty_limit = $prr->qty_limit;
						$p->harga_asal = $pd->harga_jual;
						$p->harga_jadi = $p->harga_asal;
						$p->cara = 'buy';
						if(!empty($prr->is_get)) {
							$p->cara = 'get';
							if($p->promo_jenis == 'harga'){
								$p->harga_jadi = $p->harga_asal - $p->promo_nilai;
							}else{
								$p->harga_jadi = $p->harga_asal - ($p->harga_asal*($p->promo_nilai/100));
							}
						}
						$pids[$pd->id] = $pd->id;
						$produks[$pd->id] = $p;
					}
				}
			}else if($prr->utype == 'tag'){
				$produk_data = $this->cpm->getByTags(array($prr->slug_target_utype));
				foreach($produk_data as $pd){
					if(!isset($produks[$pd->id])){
						$p = new stdClass();
						$p->c_produk_id = $pd->id;
						$p->promo_jenis = $prr->promo_jenis;
						$p->promo_nilai = $prr->promo_nilai;
						$p->qty_limit = $prr->qty_limit;
						$p->harga_asal = $pd->harga_jual;
						$p->harga_jadi = $p->harga_asal;
						$p->cara = 'buy';
						$pids[$pd->id] = $pd->id;
						$produks[$pd->id] = $p;
					}
				}
			}else{
				$produk_data = $this->cpm->getByKategoriIds(array($prr->id_target_utype));
				foreach($produk_data as $pd){
					if(!isset($produks[$pd->id])){
						$p = new stdClass();
						$p->c_produk_id = $pd->id;
						$p->promo_jenis = $prr->promo_jenis;
						$p->promo_nilai = $prr->promo_nilai;
						$p->qty_limit = $prr->qty_limit;
						$p->harga_asal = $pd->harga_jual;
						$p->harga_jadi = $p->harga_asal;
						$p->cara = 'buy';
						$pids[$pd->id] = $pd->id;
						$produks[$pd->id] = $p;
					}
				}
			}
		}
		$i=1;
		$emp = $produks;
		$produks = array();
		foreach($emp as $p1){
			$produk = array();
			//$produk['id'] = $i;
			$produk['c_promosi_id'] = $c_promosi_id;
			foreach($p1 as $k=>$v){
				$produk[$k] = $v;
			}
			$i++;
			$produks[] = $produk;
		}

		//$this->debug($produks);
		//die();

		if(count($produks)>0){
			$this->cprpm->deleteAll($c_promosi_id);
			$this->cprpm->setMass($produks);
		}

		$res = 1;
		if($res){
			$this->status = 100;
			$this->message = 'berhasil';
		}else{
			$this->status = 900;
			$this->message = 'Gagal update ke database';
		}
		$this->__json_out($data);
	}

}
