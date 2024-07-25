<?php
	class BulkSale extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_bulksale';
		$this->load("api_admin/a_negara_model","anm");
		$this->load("api_admin/b_kategori_model2","bkm2");
		$this->load("admin/b_berat_model","bberm");
		$this->load("admin/b_kondisi_model","bkonm");
		$this->load("admin/b_user_model","bum");
		$this->load('admin/c_bulksale_model','cbsm');
		$this->load('admin/c_bulksale_foto_model','cbsfm');
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
		$negara = $this->anm->getByNationCode($nation_code);
		$data['negara'] = $negara;

		$this->setTitle('Sell on me '.$this->site_suffix_admin);

		$cats = array();
		$cat = array();
		$kategories = array();
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


		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));
		$this->putThemeContent("ecommerce/bulksale/home_modal",$data);

		$this->putThemeContent("ecommerce/bulksale/home",$data);


		$this->putJsContent("ecommerce/bulksale/home_bottom",$data);
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
			redir(base_url_admin('ecommerce/bulksale/'));
      die();
    }
		$this->setKey($data['sess']);
    $pengguna = $data['sess']->admin;
    $nation_code = $pengguna->nation_code;
		$negara = $this->anm->getByNationCode($nation_code);
		$data['negara'] = $negara;

		$data['produk'] = $this->cbsm->getById($nation_code, $id);
		if(!isset($data['produk']->id)){
			redir(base_url('ecommerce/bulksale/'));
			die();
		}
		$data['user'] = $this->bum->getById($nation_code, $data['produk']->b_user_id);
		if(!isset($data['user']->id)){
			$data['user'] = new stdClass();
			$data['user']->id = "null";
			$data['user']->fnama = "-";
		}

		//$this->debug($data['produk']);
		//die();
		//handled by API
		//$data['produk']->fotos = $this->cfpfm->getByProdukId($nation_code, $data['produk']->id);

		$this->setTitle('Edit '.$data['produk']->nama.' '.$this->site_suffix_admin);
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));
		$this->putJsFooter(base_url('assets/js/jquery.priceformat.min'));

		$this->putThemeContent("ecommerce/bulksale/edit_modal",$data);
		$this->putThemeContent("ecommerce/bulksale/edit",$data);


		$this->putJsContent("ecommerce/bulksale/edit_bottom",$data);
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

		$this->setKey($data['sess']);
    $pengguna = $data['sess']->admin;
    $nation_code = $pengguna->nation_code;

		$data['seller'] = new stdClass();
		$data['produk'] = $this->cbsm->getById($nation_code, $id);
		if(!isset($data['produk']->id)){
			redir(base_url('ecommerce/bulksale/'));
			die();
		}
		$this->setTitle('Sell on me detail: '.$data['produk']->id.' '.$this->site_suffix_admin);
		if(strlen($data['produk']->vdate)<=9) $data['produk']->vdate = '-';

		$data['produk']->fotos = $this->cbsfm->getByBulkSaleId($nation_code, $data['produk']->id);
		if(isset($data['produk']->b_user_id)){
			$data['seller'] = $this->bum->getById($nation_code,$data['produk']->b_user_id);
		}

		$this->putThemeContent("ecommerce/bulksale/detail_modal",$data);
		$this->putThemeContent("ecommerce/bulksale/detail",$data);
		$this->putJsReady("ecommerce/bulksale/detail_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
	public function download_xls(){
    $data = $this->__init();

		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}
		
		$nation_code = $data['sess']->admin->nation_code;
		$keyword = '';
		$order_status = '';
		$payment_status = '';

		$ddata = $this->cbm->exportXls($nation_code,$keyword,$action_status,$is_agent);

		//loading library xls
		$this->lib('phpexcel/PHPExcel','','inc');
		$this->lib('phpexcel/PHPExcel/Writer/Excel2007','','inc');

		//preset array kolom
		$phpexcel_money = '_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)';
		$judul_pertama_sty = array(
			'font'  => array(
				'bold'  => true,
				'size'  => 12,
				'name'  => 'Arial'
			)
		);
		$style = array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$styleborder = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

		//create object xls
		$objPHPExcel = new PHPExcel();

		//===sheet laporan total===//
		$objWorkSheet = $objPHPExcel->setActiveSheetIndex(0);

		$objWorkSheet->getColumnDimension('A')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('B')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('C')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('D')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('E')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('F')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('G')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('H')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('I')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('J')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('K')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('L')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('A')->setWidth(10);
		$objWorkSheet->getColumnDimension('B')->setWidth(20);
		$objWorkSheet->getColumnDimension('C')->setWidth(20);
		$objWorkSheet->getColumnDimension('D')->setWidth(50);
		$objWorkSheet->getColumnDimension('E')->setWidth(20);
		$objWorkSheet->getColumnDimension('F')->setWidth(20);
		$objWorkSheet->getColumnDimension('G')->setWidth(20);
		$objWorkSheet->getColumnDimension('H')->setWidth(10);
		$objWorkSheet->getColumnDimension('I')->setWidth(20);
		$objWorkSheet->getColumnDimension('J')->setWidth(40);
		$objWorkSheet->getColumnDimension('K')->setWidth(20);
		$objWorkSheet->getColumnDimension('L')->setWidth(30);

		$objWorkSheet->setTitle("Bulksale");
		$objWorkSheet->SetCellValue('A2', 'Bulksale')->mergeCells('A2:F2');
		$objWorkSheet->SetCellValue('A3', 'Tanggal: '.$this->__dateIndonesia("now",'hari_tanggal'))->mergeCells('A3:F3');
		$objWorkSheet->getStyle('A2')->getFont()->setBold(true);

		//header
		$objWorkSheet
				->setCellValue('A6', 'No.')
				->setCellValue('B6', 'Order Date')
				->setCellValue('C6', 'Invoice Code')
				->setCellValue('D6', 'Product Name')
				->setCellValue('E6', 'Product Price')
				->setCellValue('F6', 'Paid to Seller')
				->setCellValue('G6', 'Return to Buyer')
				->setCellValue('H6', 'Quantity')
				->setCellValue('I6', 'Payment Cost')
				->setCellValue('J6', 'Seller Name')
				->setCellValue('K6', 'Cancellation Fee')
				->setCellValue('L6', 'Status');

		//setting gaya untuk header
		$objWorkSheet->getStyle('A6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('B6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('C6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('D6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('E6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('F6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('G6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('H6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('I6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('J6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('K6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('L6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);

		$i=7;
		$nomor = 1;
		if(count($ddata)>0){
			foreach($ddata as $pb){
				//mengisikan masing2 data
				$objWorkSheet->setCellValue('A'.$i, $nomor);
				$objWorkSheet->setCellValue('B'.$i, $pb->ldate);
				$objWorkSheet->setCellValue('C'.$i, $pb->invoice_code);
				$objWorkSheet->setCellValue('D'.$i, $pb->nama);
				$objWorkSheet->setCellValue('E'.$i, $pb->harga_jual);
				$objWorkSheet->setCellValue('F'.$i, $pb->paid_to_seller);
				$objWorkSheet->setCellValue('G'.$i, $pb->return_to_buyer);
				$objWorkSheet->setCellValue('H'.$i, $pb->qty);
				$objWorkSheet->setCellValue('I'.$i, $pb->payment_cost);
				$objWorkSheet->setCellValue('J'.$i, $pb->seller_name);
				$objWorkSheet->setCellValue('K'.$i, $pb->cancel_fee);
				$objWorkSheet->setCellValue('L'.$i, $pb->order_status);

				//set border ke masing2 kolom
				$objWorkSheet->getStyle('A'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('B'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('C'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('D'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('E'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('F'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('G'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('H'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('I'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('J'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('K'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('L'.$i)->applyFromArray($styleborder);

				$nomor++;
				$i++;
			}
		}else{
			$objWorkSheet->setCellValue('A'.$i,'Belum ada data')->mergeCells('A'.$i.':D'.$i.'');
			$objWorkSheet->getStyle('A'.$i.':D'.$i.'')->getAlignment()->applyFromArray($style);
			$objWorkSheet->getStyle('A'.$i.':D'.$i.'')->applyFromArray($styleborder);
		}
		$objPHPExcel->setActiveSheetIndex(0);

		//save file
		$save_dir = $this->__checkDir(date("Y/m"));
		$save_file = 'order-history';
		$save_file = str_replace(' ','',str_replace('/','',$save_file));

		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		if(file_exists($save_dir.'/'.$save_file.'.xlsx')) unlink($save_dir.'/'.$save_file.'.xlsx');
		$objWriter->save($save_dir.'/'.$save_file.'.xlsx');

		//$download_path = str_replace(SENEROOT,'/',$save_dir.'/'.$save_file.'.xlsx');
		$this->__forceDownload($save_dir.'/'.$save_file.'.xlsx');
	}
}
