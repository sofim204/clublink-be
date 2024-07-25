<?php
	class Produkreport extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'crm';
		$this->current_page = 'crm_produkreport';
		$this->load("api_admin/a_negara_model","anm");
		$this->load("api_admin/b_kategori_model2","bkm2");
		$this->load("admin/b_kategori_model4","bkm4");
		$this->load("admin/b_berat_model","bberm");
		$this->load("admin/b_kondisi_model","bkonm");
		$this->load("admin/b_user_model","bum");
		$this->load("admin/b_user_alamat_model","buam");
		$this->load('admin/c_produk_model','cpm');
		$this->load('admin/c_produk_foto_model','cpfm');
		$this->load('admin/e_rating_model','erm');
		$this->load('api_admin/a_pengguna_model','apl');
        $this->load("api_admin/c_produk_laporan_model", 'cplm');
	}
	protected function __toStars($rating){
		$str = '';
		for($rti=1;$rti<=5;$rti++){
			if($rating<=$rti){
				$str .= '<i class="fa fa-star-o"></i>';
			}else{
				$str .= '<i class="fa fa-star"></i>';
			}
		}
		return $str;
	}

    public function exportExcel()
    {
        $this->lib('phpexcel/PHPExcel','','inc');
        $this->lib('phpexcel/PHPExcel/Writer/Excel2007','','inc');

        $object = new PHPExcel();
        $object->setActiveSheetIndex(0);
        $table_columns = ["Tanggal Submit", "Produk", "Owner", "Reported_by", "Takedown_by", "Deskripsi", "Status"];
        $column = 0;

        foreach ($table_columns as $filed) {
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $filed);

            $column ++;
        }

        $excel_row = 2;
        $field_name = "admin_name";
		$from_date = $this->input->post("from_date");
		$end_date = $this->input->post("end_date");
        $admin_name = $this->input->post("admin_name") == '-' ? "IS NULL" : $this->input->post("admin_name");

        $data = $this->cplm->getAllBy($field_name, $admin_name, $from_date, $end_date);
		
        foreach ($data as $row) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $row->cdate);
            $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $row->c_produk_nama);
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, empty($row->b_user_nama_seller) ? 'admin' : $row->b_user_nama_seller);
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, empty($row->b_user_nama_reporter) ? 'admin' : $row->b_user_nama_reporter);
            $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, empty($row->admin_name) ? 'admin' : $row->admin_name);
            $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, empty($row->deskripsi) ? '-' : $row->deskripsi);
            $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $row->reported_status);

            $excel_row ++;
        }

        $objWriter = new PHPExcel_Writer_Excel2007($object);
        $filename = "file" . time();
		ob_start();
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
		ob_end_clean();

		$response =  [
			'op' => 'ok',
			'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($xlsData),
			'filename' => $filename
		];

		echo json_encode($response);
    }

	public function cs_work_history()
	{
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$this->setKey($data['sess']);
		$pengguna = $data['sess']->admin;
		$nation_code = $pengguna->nation_code;
		
		$this->current_page = 'crm_cs_work_history';
		//get country data
		$negara = $this->anm->getByNationCode($nation_code);
		$data['negara'] = $negara;
		$data['admin_name'] = $data['sess']->admin->user_alias;

		$data['admin_list'] = $this->apl->getAdminName(62);

		//get list of product condition
		$data['kategori_list'] = $this->bkm4->getActive($nation_code);
		$data['kondisi_list'] = $this->bkonm->getActive($nation_code);

		$this->setTitle('Products Report'.$this->site_suffix_admin);
		$this->putThemeContent("crm/produkreport/cs_work/index",$data);
		$this->putJsContent("crm/produkreport/cs_work/index_js",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function index(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$this->setKey($data['sess']);
		$pengguna = $data['sess']->admin;
		$nation_code = $pengguna->nation_code;

		//get country data
		$negara = $this->anm->getByNationCode($nation_code);
		$data['negara'] = $negara;
		$data['admin_name'] = $data['sess']->admin->user_alias;

		$data['admin_list'] = $this->apl->getAdminName(62);

		//get list of product condition
		$data['kategori_list'] = $this->bkm4->getActive($nation_code);
		$data['kondisi_list'] = $this->bkonm->getActive($nation_code);

		$this->setTitle('Products Report'.$this->site_suffix_admin);
		$this->putThemeContent("crm/produkreport/home_modal",$data);
		$this->putThemeContent("crm/produkreport/home",$data);
		$this->putJsContent("crm/produkreport/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
	public function tambah(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$this->setKey($data['sess']);

		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;
		$negara = $this->anm->getByNationCode($nation_code);
		$data['negara'] = $negara;

		$this->setTitle('Tambah Produk Ecommerce '.$this->site_suffix_admin);
		$this->setTitle('New Products '.$this->site_suffix_admin);
		$cats = array();
		$cat = array();
		$kategories = array();//$this->bkm2->getKategori();
		foreach($kategories as $kategori){
			if($kategori->b_kategori_id == "-" || $kategori->utype=="kategori"){
				$cats[$kategori->id] = $kategori;
				$cats[$kategori->id]->childs = array();
			}else if($kategori->utype=="kategori_sub"){
				if(!isset($cats[$kategori->b_kategori_id])){
					$cats[$kategori->b_kategori_id] = new stdClass();
					$cats[$kategori->b_kategori_id]->childs = array();
				}
				if(!isset($cats[$kategori->b_kategori_id]->childs[$kategori->id]))
					$cats[$kategori->b_kategori_id]->childs[$kategori->id] = new stdClass();

				$cats[$kategori->b_kategori_id]->childs[$kategori->id] = $kategori;
			}else{
				$cat[$kategori->id] = $kategori;
			}
		}

		//$this->debug($cats);
		//die();
		$data['kategori'] = $cats;
		$data['kondisi'] = $this->bkonm->get();
		$data['berat'] = $this->bberm->get();

		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));
		$this->putJsFooter(base_url('assets/js/jquery.priceformat.min'));

		$this->putThemeContent("crm/produkreport/tambah_modal",$data);
		$this->putThemeContent("crm/produkreport/tambah",$data);

		$this->putJsContent("crm/produkreport/tambah_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
	public function edit($id){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

    $id = (int) $id;
    if($id<=0){
			redir(base_url_admin('ecommerce/produk'));
      die();
    }
		$this->setKey($data['sess']);
    $pengguna = $data['sess']->admin;
    $nation_code = $pengguna->nation_code;
		$negara = $this->anm->getByNationCode($nation_code);
		$data['negara'] = $negara;

		$data['produk'] = $this->cpm->getById($nation_code, $id);
		if(!isset($data['produk']->id)){
			redir(base_url('ecommerce/produk/'));
			die();
		}
		//kategori list
		$cats = array();
		$cat = array();
		$kategories = $this->bkm2->getKategori($nation_code);
		foreach($kategories as $kategori){
			if($kategori->parent_b_kategori_id == "-" || $kategori->utype=="kategori"){
				$cats[$kategori->id] = $kategori;
				$cats[$kategori->id]->childs = array();
			}else if($kategori->utype=="kategori_sub"){
				if(!isset($cats[$kategori->parent_b_kategori_id])){
					$cats[$kategori->parent_b_kategori_id] = new stdClass();
					$cats[$kategori->parent_b_kategori_id]->childs = array();
				}
				if(!isset($cats[$kategori->parent_b_kategori_id]->childs[$kategori->id]))
					$cats[$kategori->parent_b_kategori_id]->childs[$kategori->id] = new stdClass();

				$cats[$kategori->parent_b_kategori_id]->childs[$kategori->id] = $kategori;
			}else{
				$cat[$kategori->id] = $kategori;
			}
		}
		//$this->debug($data['produk']);
		//die();
		$data['kategori'] = $cats;
		$data['kondisi'] = $this->bkonm->get($nation_code);
		$data['berat'] = $this->bberm->get($nation_code);
		$data['user'] = $this->bum->getById($nation_code, $data['produk']->b_user_id);
		if(!isset($data['user']->id)){
			$data['user'] = new stdClass();
			$data['user']->id = "null";
			$data['user']->fnama = "-";
		}
		$data['alamat'] = $this->buam->getByUserId($nation_code,$data['produk']->b_user_id);

		//$this->debug($data['produk']);
		//die();
		//handled by API
		//$data['produk']->fotos = $this->cpfm->getByProdukId($nation_code, $data['produk']->id);

		$this->setTitle('Edit '.$data['produk']->nama.' '.$this->site_suffix_admin);
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));
		$this->putJsFooter(base_url('assets/js/jquery.priceformat.min'));

		$this->putThemeContent("crm/produkreport/edit_modal",$data);
		$this->putThemeContent("crm/produkreport/edit",$data);


		$this->putJsContent("crm/produkreport/edit_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
	public function detail($id){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$this->setKey($data['sess']); //extending session time

		//get current admin
    $pengguna = $data['sess']->admin;
    $nation_code = $pengguna->nation_code; //get nation_code from current admin

		//validate produk ID
		$id = (int) $id; // cast as integer
		if($id<=0){
			redir(base_url('ecommerce/produk/'));
			die();
		}

		//get product data
		$data['produk'] = $this->cpm->getById($nation_code, $id);
		if(!isset($data['produk']->id)){
			redir(base_url('ecommerce/produk/'));
			die();
		}

		//get kategori data
		$data['kategori'] = $this->bkm4->getById($nation_code,$data['produk']->b_kategori_id);
		$data['kondisi'] = $this->bkonm->getById($nation_code,$data['produk']->b_kondisi_id);

		//handling if missing or null
		if(!isset($data['kategori']->nama)) $data['kategori']->nama = '-';
		if(!isset($data['kategori']->image_icon)) $data['kategori']->image_icon = 'media/icon/default-icon.png';
		//get user data (seller)
		$data['user'] = $this->bum->getById($nation_code,$data['produk']->b_user_id);
		$data['user']->rating = 0;
		$seller_rating = 0;
		$rating_object = $this->erm->getSellerStats($nation_code,$data['produk']->b_user_id);
		if(isset($rating_object->rating_count) && isset($rating_object->rating_count)){
			$data['user']->rating = 0;
			if(!empty($rating_object->rating_count)){
				$data['user']->rating = floor($rating_object->rating_total/$rating_object->rating_count);
			}
		}

		//get pickup address
		$data['alamat'] = $this->buam->getById($nation_code,$data['produk']->b_user_id,$data['produk']->b_user_alamat_id);

		$this->setTitle(''.$data['produk']->nama.' '.$this->site_suffix_admin);
		$data['produk']->fotos = $this->cpfm->getByProdukId($nation_code, $data['produk']->id);
		$this->putThemeContent("crm/produkreport/detail",$data);

		$this->putJsContent("crm/produkreport/detail_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

}
