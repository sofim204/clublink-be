<?php
class Promosi_Rule extends JI_Controller{

	public function __construct(){
    parent::__construct();
		//$this->setTheme('frontx');
		$this->load("api_admin/c_produk_model",'cpm');
		$this->load("api_admin/c_promosi_model",'cprm');
		$this->load("api_admin/c_promosi_rule_model",'cprrm');
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_promosi';
	}
	public function index($c_promosi_id=""){
		$d = $this->__init();
		$c_promosi_id = (int) $c_promosi_id;
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

		$tbl_as = $this->cprrm->getTableAlias();
		$tbl2_as = $this->cprrm->getTableAlias2();

		switch($iSortCol_0){
			case 0:
				$sortCol = "$tbl_as.id";
				break;
			case 1:
				$sortCol = "$tbl_as.prioritas";
				break;
			case 2:
				$sortCol = "$tbl_as.nama_target_utype";
				break;
			case 5:
				$sortCol = "$tbl_as.qty_min";
				break;
			case 6:
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
		$dcount = $this->cprrm->countAll($keyword,$c_promosi_id);
		$ddata = $this->cprrm->getAll($page,$pagesize,$sortCol,$sortDir,$keyword,$c_promosi_id);

		foreach($ddata as &$gd){
			if(isset($gd->utype)){
        if(isset($gd->is_get)){
          if(!empty($gd->is_get) && isset($gd->promo_jenis) && isset($gd->promo_nilai)){
            $persen = '%';
            $harga = '';
            if($gd->promo_jenis == 'harga'){
              $promo = '';
              $harga = 'Rp';
            }
            $gd->utype = 'Get: <b>'.$gd->nama_target_utype.'</b> <i>diskon '.$harga.''.$gd->promo_nilai.''.$persen.'</i>';
          }else{
    				if(isset($gd->nama_target_utype)){
							if($gd->utype == 'produk'){
    						$gd->utype = 'Beli '.$gd->utype.': <b>'.$gd->nama_target_utype.'</b>';
							}else{
	    					$gd->utype = 'Beli salah satu dari '.$gd->utype.' produk: <b>'.$gd->nama_target_utype.'</b>';
							}
    					unset($gd->nama_target_utype);
    				}
          }
        }
			}
			if(isset($gd->qty_limit)){
				$gd->qty_limit .= ' Pcs';
			}
			if(isset($gd->is_active)){
				if(!empty($gd->is_active)){
					$gd->is_active = '<span class="label label-success">Active</span>';
				}else{
					$gd->is_active = '<span class="label label-default">Not Active</span>';
				}
			}
		}
		$another = array();
		$this->__jsonDataTable($ddata,$dcount);
	}

	public function tambah(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}
		$di = $_POST;
		if(!isset($di['c_promosi_id'])) $di['c_promosi_id']='';
		if(!isset($di['utype'])) $di['utype']='';
		if(strlen($di['utype'])>0 && strlen($di['c_promosi_id'])>0){
      if(!isset($di['prioritas'])) $di['prioritas'] = 0;
      if(empty($di['prioritas'])) $di['prioritas'] = $this->cprrm->getLatest($di['c_promosi_id']);
			$res = $this->cprrm->set($di);
			if($res){
				$this->status = 100;
				$this->message = 'Data baru berhasil ditambahkan';
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
		$data = $this->cprrm->getById($id);
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
		if(!isset($du['utype'])) $du['utype'] = "";
		if(!isset($du['c_promosi_id'])) $du['c_promosi_id']='';
		if($id>0 && strlen($du['utype'])>1 && strlen($du['c_promosi_id'])>0){
      if(!isset($du['prioritas'])) $du['prioritas'] = 0;
      if(empty($du['prioritas'])) $du['prioritas'] = $this->cprrm->getLatest($du['c_promosi_id']);
			$res = $this->cprrm->update($id,$du);
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
			$res = $this->cprrm->del($id);
			if(!$res){
				$this->status = 902;
				$this->message = 'Data gagal dihapus';
			}
		}
		$this->__json_out($data);
	}

	public function pilihan(){
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

		$sortCol = "date";
		$sortDir = strtoupper($sSortDir_0);
		if(empty($sortDir)) $sortDir = "DESC";
		if(strtolower($sortDir) != "desc"){
			$sortDir = "ASC";
		}

		switch($iSortCol_0){
			case 0:
				$sortCol = "id";
				break;
			case 1:
				$sortCol = "utype";
				break;
			case 2:
				$sortCol = "sku";
				break;
			case 3:
				$sortCol = "nama";
				break;
			case 4:
				$sortCol = "harga_jual";
				break;
			case 5:
				$sortCol = "dilihat";
				break;
			case 6:
				$sortCol = "terjual";
				break;
			case 8:
				$sortCol = "stok";
				break;
			default:
				$sortCol = "id";
		}

		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;

		$keyword = $sSearch;

		//filter kategori
		$b_kategori_id = (int) $this->input->request('b_kategori_id');
		if(empty($b_kategori_id)) $b_kategori_id = "";

		//filter terpilih
		$terpilih = $this->input->request("terpilih");
		if(strlen($terpilih)>2){
			$terpilih = explode(",",$terpilih);
		}


		$this->status = '100';
		$this->message = 'Berhasil';
		$dcount = $this->cpm->countAllBarangJasa($keyword,$terpilih,$b_kategori_id);
		$ddata = $this->cpm->getAllBarangJasa($page,$pagesize,$sortCol,$sortDir,$keyword,$terpilih,$b_kategori_id);

		foreach($ddata as &$gd){
			if(isset($gd->opsi) && isset($gd->id)){
				$gd->opsi  = '<div class="input-group">';
				$gd->opsi .= '<span class="input-group-btn">';
        $gd->opsi .= '<button id="button-komposisi-turun-'.$gd->id.'" type="button" class="btn btn-default btn-angka-turun" data-id="'.$gd->id.'"><i class="fa fa-minus"></i></button>';
        $gd->opsi .= '</span>';
				$gd->opsi .= '<input id="input-komposisi-'.$gd->id.'" type="number" class="form-control input-komposisi" placeholder="Qty" data-id="'.$gd->id.'" value="0" />';
        $gd->opsi .= '<span class="input-group-btn">';
        $gd->opsi .= '<button id="button-komposisi-naik-'.$gd->id.'" type="button" class="btn btn-default btn-angka-naik" data-id="'.$gd->id.'"><i class="fa fa-plus"></i></button>';
        $gd->opsi .= '</span>';
        $gd->opsi .= '</div>';
			}
		}
		$this->__jsonDataTable($ddata,$dcount);
	}

}
