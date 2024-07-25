<?php
class Payment extends JI_Controller{
	public function __construct(){
		parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_payment';
		$this->load("admin/a_bank_model","abm");
		$this->load("admin/a_bank_trfcost_model","abtcm");
		$this->load("admin/common_code_model","ccm");
		$this->load("admin/d_order_model","dom");
		$this->load("admin/d_order_detail_model","dodm");
	}

	private function __forceDownload($pathFile){
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($pathFile));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($pathFile));
		ob_clean();
		flush();
		readfile($pathFile);
		exit;
	}

	private function __checkDir($periode){
		if(!is_dir(SENEROOT.'media/')) mkdir(SENEROOT.'media/',0777);
		if(!is_dir(SENEROOT.'media/laporan/')) mkdir(SENEROOT.'media/laporan/',0777);
		$str = $periode.'/01';
		$periode_y = date("Y",strtotime($str));
		$periode_m = date("m",strtotime($str));
		if(!is_dir(SENEROOT.'media/laporan/'.$periode_y)) mkdir(SENEROOT.'media/laporan/'.$periode_y,0777);
		if(!is_dir(SENEROOT.'media/laporan/'.$periode_y.'/'.$periode_m)) mkdir(SENEROOT.'media/laporan/'.$periode_y.'/'.$periode_m,0777);
		return SENEROOT.'media/laporan/'.$periode_y.'/'.$periode_m;
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

    $nation_code = $data['sess']->admin->nation_code;

		//get current bank configuration
		$app_bank_id = 0;
		$config = $this->ccm->getByClassifiedAndCode($nation_code,"app_config","C0");
		if(isset($config->remark)) $app_bank_id = (int) $config->remark;
		$app_bank = $this->abm->getById($nation_code,$app_bank_id);
		if(!isset($app_bank->id)){
			$app_bank->id = 0;
			$app_bank->nation_code = $nation_code;
			$app_bank->nama = '-';
			$app_bank->is_active = 0;
		}
		$data['app_bank'] = $app_bank;

		$this->setTitle('Payment '.$this->site_suffix_admin);
		$this->putThemeContent("ecommerce/payment/home_modal",$data);
		$this->putThemeContent("ecommerce/payment/home",$data);
		$this->putJsContent("ecommerce/payment/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function download_xls(){
		$data = $this->__init();

		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		$nation_code = $data['sess']->admin->nation_code;
		$keyword = '';
		$seller_status = $this->input->get("seller_status");
		$buyer_confirmed = $this->input->get("buyer_confirmed");
		$settlement_status = $this->input->get("settlement_status");
		$cdate_start = $this->input->get("cdate_start");
		$cdate_end = $this->input->get("cdate_end");

		$ddata = $this->dodm->exportXlsPayment($nation_code,$keyword,$seller_status,$buyer_confirmed,$settlement_status,$cdate_start,$cdate_end);

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
		$objWorkSheet->getColumnDimension('M')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('N')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('O')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('P')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('Q')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('R')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('S')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('T')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('U')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('V')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('W')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('A')->setWidth(10);
		$objWorkSheet->getColumnDimension('B')->setWidth(20);
		$objWorkSheet->getColumnDimension('C')->setWidth(20);
		$objWorkSheet->getColumnDimension('D')->setWidth(50);
		$objWorkSheet->getColumnDimension('E')->setWidth(20);
		$objWorkSheet->getColumnDimension('F')->setWidth(20);
		$objWorkSheet->getColumnDimension('G')->setWidth(20);
		$objWorkSheet->getColumnDimension('H')->setWidth(20);
		$objWorkSheet->getColumnDimension('I')->setWidth(20);
		$objWorkSheet->getColumnDimension('J')->setWidth(40);
		$objWorkSheet->getColumnDimension('K')->setWidth(20);
		$objWorkSheet->getColumnDimension('L')->setWidth(30);
		$objWorkSheet->getColumnDimension('M')->setWidth(30);
		$objWorkSheet->getColumnDimension('N')->setWidth(30);
		$objWorkSheet->getColumnDimension('O')->setWidth(30);
		$objWorkSheet->getColumnDimension('P')->setWidth(30);
		$objWorkSheet->getColumnDimension('Q')->setWidth(30);
		$objWorkSheet->getColumnDimension('R')->setWidth(30);
		$objWorkSheet->getColumnDimension('S')->setWidth(30);
		$objWorkSheet->getColumnDimension('T')->setWidth(30);
		$objWorkSheet->getColumnDimension('U')->setWidth(30);
		$objWorkSheet->getColumnDimension('V')->setWidth(30);
		$objWorkSheet->getColumnDimension('W')->setWidth(30);

		$objWorkSheet->setTitle("Order");
		$objWorkSheet->SetCellValue('A2', 'Payment List')->mergeCells('A2:V2');
		$objWorkSheet->SetCellValue('A3', 'Date: '.date("j/F/y"))->mergeCells('A3:V3');
		$objWorkSheet->getStyle('A2')->getFont()->setBold(true);

		//header
		$objWorkSheet
		->setCellValue('A6', 'No.')
		->setCellValue('B6', 'Order Date')
		->setCellValue('C6', 'Invoice Number')
		->setCellValue('D6', 'Product')
		->setCellValue('E6', 'Price')
		->setCellValue('F6', 'Qty')
		->setCellValue('G6', 'Subtotal')
		->setCellValue('H6', 'Shipping Cost')
		->setCellValue('I6', 'Grand Total')
		->setCellValue('J6', 'Seller Earning')
		->setCellValue('K6', 'Profit ')
		->setCellValue('L6', 'Refund Amount')
		->setCellValue('M6', 'Bank Transfer Cost')
		->setCellValue('N6', 'Order Status')
		->setCellValue('O6', 'Payment Status')
		->setCellValue('P6', 'Seller Status')
		->setCellValue('Q6', 'Shipment Status')
		->setCellValue('R6', 'Buyer Status')
		->setCellValue('S6', 'Settlement Status')
		->setCellValue('T6', 'Bank Name')
		->setCellValue('U6', 'Account Name')
		->setCellValue('V6', 'Account No')
		;

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
		$objWorkSheet->getStyle('M6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('N6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('O6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('P6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('Q6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('R6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('S6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('T6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('U6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('V6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);

		$i=7;
		$nomor = 1;
		if(count($ddata)>0){
			foreach($ddata as $pb){
				if($pb->d_order_detail_id==1){$value=$pb->profit_amount;}else{$value=0;}
				//mengisikan masing2 data
				$objWorkSheet->setCellValue('A'.$i, $nomor);
				$objWorkSheet->setCellValue('B'.$i, date("j/F/y", strtotime($pb->cdate)));
				$objWorkSheet->setCellValue('C'.$i, $pb->invoice_code);
				$objWorkSheet->setCellValue('D'.$i, $pb->nama);
				$objWorkSheet->setCellValue('E'.$i, $pb->harga_jual);
				$objWorkSheet->setCellValue('F'.$i, $pb->qty);
				$objWorkSheet->setCellValue('G'.$i, $pb->subtotal);
				$objWorkSheet->setCellValue('H'.$i, $pb->shipment_cost);
				$objWorkSheet->setCellValue('I'.$i, $pb->grand_total);
				$objWorkSheet->setCellValue('J'.$i, $pb->earning_total);
				$objWorkSheet->setCellValue('K'.$i, $value);
				$objWorkSheet->setCellValue('L'.$i, $pb->refund_amount); 
				$objWorkSheet->setCellValue('M'.$i, $pb->banktrf_cost);
				$objWorkSheet->setCellValue('N'.$i, $this->__orderStatusText($pb->order_status));
				$objWorkSheet->setCellValue('O'.$i, $this->__paymentStatusText($pb->payment_status));
				$objWorkSheet->setCellValue('P'.$i, $this->__sellerStatusText($pb->seller_status));
				$objWorkSheet->setCellValue('Q'.$i, $this->__shipmentStatusText($pb->shipment_status));
				$objWorkSheet->setCellValue('R'.$i, $this->__buyerConfirmedText($pb->buyer_confirmed));
				$objWorkSheet->setCellValue('S'.$i, $this->__settlementStatusText($pb->settlement_status));
				$objWorkSheet->setCellValue('T'.$i, $pb->rekening_bank_seller);
				$objWorkSheet->setCellValue('U'.$i, $pb->rekening_nama_seller);
				$objWorkSheet->setCellValue('V'.$i, $pb->rekening_nomor_seller);

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
				$objWorkSheet->getStyle('M'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('N'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('O'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('P'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('Q'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('R'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('S'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('T'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('U'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('V'.$i)->applyFromArray($styleborder);
				//$objWorkSheet->getStyle('W'.$i)->applyFromArray($styleborder);
				$nomor++;
				$i++;
			}
		}else{
			$objWorkSheet->setCellValue('A'.$i,'Empty result')->mergeCells('A'.$i.':U'.$i.'');
			$objWorkSheet->getStyle('A'.$i.':U'.$i.'')->getAlignment()->applyFromArray($style);
			$objWorkSheet->getStyle('A'.$i.':U'.$i.'')->applyFromArray($styleborder);
		}
		$objPHPExcel->setActiveSheetIndex(0);

		//save file
		$save_dir = $this->__checkDir(date("Y/m"));
		$save_file = 'report-payment';
		$save_file = str_replace(' ','',str_replace('/','',$save_file));

		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		if(file_exists($save_dir.'/'.$save_file.'.xlsx')) unlink($save_dir.'/'.$save_file.'.xlsx');
		$objWriter->save($save_dir.'/'.$save_file.'.xlsx');

		//$download_path = str_replace(SENEROOT,'/',$save_dir.'/'.$save_file.'.xlsx');
		$this->__forceDownload($save_dir.'/'.$save_file.'.xlsx');
	}


	//by Donny Dennison - 19 January 2020 11:17
	//add seller settlement download excel
	public function seller_settlement_download_xls(){
		$data = $this->__init();

		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		$nation_code = $data['sess']->admin->nation_code;
		$keyword = '';
		$seller_status = $this->input->get("seller_status");
		$buyer_confirmed = $this->input->get("buyer_confirmed");
		$settlement_status = $this->input->get("settlement_status");
		$cdate_start = $this->input->get("cdate_start");
		$cdate_end = $this->input->get("cdate_end");

		$ddata = $this->dodm->exportXlsPayment($nation_code,$keyword,$seller_status,$buyer_confirmed,$settlement_status,$cdate_start,$cdate_end);

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
		$objWorkSheet->getColumnDimension('A')->setWidth(10);
		$objWorkSheet->getColumnDimension('B')->setWidth(20);
		$objWorkSheet->getColumnDimension('C')->setWidth(20);
		$objWorkSheet->getColumnDimension('D')->setWidth(50);
		$objWorkSheet->getColumnDimension('E')->setWidth(20);
		$objWorkSheet->getColumnDimension('F')->setWidth(20);
		$objWorkSheet->getColumnDimension('G')->setWidth(20);
		$objWorkSheet->getColumnDimension('H')->setWidth(20);
		$objWorkSheet->getColumnDimension('I')->setWidth(30);
		$objWorkSheet->getColumnDimension('J')->setWidth(40);

		$objWorkSheet->setTitle("Seller Settlement ".date('d M Y'));

		//header
		$objWorkSheet
		->setCellValue('A1', 'Date')
		->setCellValue('B1', 'ID')
		->setCellValue('C1', 'Invoice Number')
		->setCellValue('D1', 'Order Date')
		->setCellValue('E1', 'Account Name')
		->setCellValue('F1', 'Bank Name')
		->setCellValue('G1', 'Account Number')
		->setCellValue('H1', 'Selling Amount')
		->setCellValue('I1', 'Seller Settlement Amount')
		->setCellValue('J1', 'Paid Date')
		;

		//setting gaya untuk header
		$objWorkSheet->getStyle('A1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('B1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('C1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('D1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('E1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('F1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('G1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('H1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('I1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('J1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);

		$i=2;
		$nomor = 1;
		if(count($ddata)>0){
			foreach($ddata as $pb){
				//mengisikan masing2 data
				$objWorkSheet->setCellValue('A'.$i, date('d M Y'));
				$objWorkSheet->setCellValue('B'.$i, $pb->id);
				$objWorkSheet->setCellValue('C'.$i, $pb->invoice_code);
				$objWorkSheet->setCellValue('D'.$i, date("m/d/Y H:i:s", strtotime($pb->cdate)));
				$objWorkSheet->setCellValue('E'.$i, $pb->rekening_nama_seller);
				$objWorkSheet->setCellValue('F'.$i, $pb->rekening_bank_seller);
				$objWorkSheet->setCellValue('G'.$i, $pb->rekening_nomor_seller);
				$objWorkSheet->setCellValue('H'.$i, $pb->subtotal);
				$objWorkSheet->setCellValue('I'.$i, $pb->earning_total);
				$objWorkSheet->setCellValue('J'.$i, '');

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
				$i++;
			}
		}else{
			$objWorkSheet->setCellValue('A'.$i,'Empty result')->mergeCells('A'.$i.':U'.$i.'');
			$objWorkSheet->getStyle('A'.$i.':U'.$i.'')->getAlignment()->applyFromArray($style);
			$objWorkSheet->getStyle('A'.$i.':U'.$i.'')->applyFromArray($styleborder);
		}
		$objPHPExcel->setActiveSheetIndex(0);

		//save file
		$save_dir = $this->__checkDir(date("Y/m"));
		$save_file = 'Sellon-Seller-Settlement-file-'.date('d').'-'.date('M').'-'.date('Y');
		$save_file = str_replace(' ','',str_replace('/','',$save_file));

		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		if(file_exists($save_dir.'/'.$save_file.'.xlsx')) unlink($save_dir.'/'.$save_file.'.xlsx');
		$objWriter->save($save_dir.'/'.$save_file.'.xlsx');

		//$download_path = str_replace(SENEROOT,'/',$save_dir.'/'.$save_file.'.xlsx');
		$this->__forceDownload($save_dir.'/'.$save_file.'.xlsx');
	}

	public function pg_xls(){
		$data = $this->__init();

		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		$nation_code = $data['sess']->admin->nation_code;
		$keyword = '';
		$seller_status = $this->input->get("seller_status");
		$buyer_confirmed = $this->input->get("buyer_confirmed");
		$settlement_status = $this->input->get("settlement_status");
		$cdate_start = $this->input->get("cdate_start");
		$cdate_end = $this->input->get("cdate_end");

		$ddata = $this->dodm->exportXlsPG($nation_code,$keyword,$seller_status,$buyer_confirmed,$settlement_status,$cdate_start,$cdate_end);

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
		$objWorkSheet->getColumnDimension('M')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('N')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('O')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('P')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('Q')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('R')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('S')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('T')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('U')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('V')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('W')->setAutoSize(false);
		$objWorkSheet->getColumnDimension('A')->setWidth(10);
		$objWorkSheet->getColumnDimension('B')->setWidth(20);
		$objWorkSheet->getColumnDimension('C')->setWidth(20);
		$objWorkSheet->getColumnDimension('D')->setWidth(20);
		$objWorkSheet->getColumnDimension('E')->setWidth(20);
		$objWorkSheet->getColumnDimension('F')->setWidth(20);
		$objWorkSheet->getColumnDimension('G')->setWidth(20);
		$objWorkSheet->getColumnDimension('H')->setWidth(20);
		$objWorkSheet->getColumnDimension('I')->setWidth(20);
		$objWorkSheet->getColumnDimension('J')->setWidth(40);
		$objWorkSheet->getColumnDimension('K')->setWidth(20);
		$objWorkSheet->getColumnDimension('L')->setWidth(30);
		$objWorkSheet->getColumnDimension('M')->setWidth(40);
		$objWorkSheet->getColumnDimension('N')->setWidth(30);
		$objWorkSheet->getColumnDimension('O')->setWidth(30);
		$objWorkSheet->getColumnDimension('P')->setWidth(30);
		$objWorkSheet->getColumnDimension('Q')->setWidth(30);
		$objWorkSheet->getColumnDimension('R')->setWidth(30);
		$objWorkSheet->getColumnDimension('S')->setWidth(30);
		$objWorkSheet->getColumnDimension('T')->setWidth(30);
		$objWorkSheet->getColumnDimension('U')->setWidth(30);
		$objWorkSheet->getColumnDimension('V')->setWidth(30);
		$objWorkSheet->getColumnDimension('W')->setWidth(30);

		$objWorkSheet->setTitle("PG Fee");
		$objWorkSheet->SetCellValue('A2', 'PG Fee')->mergeCells('A2:O2');
		$objWorkSheet->SetCellValue('A3', 'Date: '.date("j/F/y"))->mergeCells('A3:O3');
		$objWorkSheet->getStyle('A2')->getFont()->setBold(true);

		//header
		$objWorkSheet
		->setCellValue('A6', 'No.')
		->setCellValue('B6', 'Order Date')
		->setCellValue('C6', 'Invoice Number')
		->setCellValue('D6', 'Product(s) Price Total')
		->setCellValue('E6', 'Total Shipping Cost')
		->setCellValue('F6', 'Grand Total')
		->setCellValue('G6', 'Estimated Selling Fee')
		->setCellValue('H6', 'MDR')
		->setCellValue('I6', 'VAT')
		->setCellValue('J6', 'PG Fee')
		->setCellValue('K6', 'Total Profit')
		->setCellValue('L6', 'Refund Amount')
		->setCellValue('M6', 'Payment Method')
		->setCellValue('N6', 'Payment ID')
		->setCellValue('O6', 'Payment Date')
		;

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
		$objWorkSheet->getStyle('M6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('N6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
		$objWorkSheet->getStyle('O6')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);

		$i=7;
		$nomor = 1;
		if(count($ddata)>0){
			foreach($ddata as $pb){
				//mengisikan masing2 data
				$objWorkSheet->setCellValue('A'.$i, $nomor);
				$objWorkSheet->setCellValue('B'.$i, date("j/F/y", strtotime($pb->cdate)));
				$objWorkSheet->setCellValue('C'.$i, $pb->invoice_code);
				$objWorkSheet->setCellValue('D'.$i, $pb->sub_total);
				$objWorkSheet->setCellValue('E'.$i, $pb->ongkir_total);
				$objWorkSheet->setCellValue('F'.$i, $pb->grand_total);
				$objWorkSheet->setCellValue('G'.$i, $pb->selling_fee);
				$objWorkSheet->setCellValue('H'.$i, $pb->pg_fee);
				$objWorkSheet->setCellValue('I'.$i, $pb->pg_fee_vat);
				$objWorkSheet->setCellValue('J'.$i, $pb->pg_cost);
				$objWorkSheet->setCellValue('K'.$i, $pb->profit_amount);
				$objWorkSheet->setCellValue('L'.$i, $pb->refund_amount);
				$objWorkSheet->setCellValue('M'.$i, $pb->payment_gateway.' - '.$this->__card2Text($pb->code_bank));
				$objWorkSheet->setCellValue('N'.$i, $pb->payment_tranid);
				$objWorkSheet->setCellValue('O'.$i, date("j/F/y H:i:s", strtotime($pb->payment_date)));

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
				$objWorkSheet->getStyle('M'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('N'.$i)->applyFromArray($styleborder);
				$objWorkSheet->getStyle('O'.$i)->applyFromArray($styleborder);
				//$objWorkSheet->getStyle('W'.$i)->applyFromArray($styleborder);
				$nomor++;
				$i++;
			}
		}else{
			$objWorkSheet->setCellValue('A'.$i,'Empty result')->mergeCells('A'.$i.':O'.$i.'');
			$objWorkSheet->getStyle('A'.$i.':O'.$i.'')->getAlignment()->applyFromArray($style);
			$objWorkSheet->getStyle('A'.$i.':O'.$i.'')->applyFromArray($styleborder);
		}
		$objPHPExcel->setActiveSheetIndex(0);

		//save file
		$save_dir = $this->__checkDir(date("Y/m"));
		$save_file = 'report-pg-cost';
		$save_file = str_replace(' ','',str_replace('/','',$save_file));

		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		if(file_exists($save_dir.'/'.$save_file.'.xlsx')) unlink($save_dir.'/'.$save_file.'.xlsx');
		$objWriter->save($save_dir.'/'.$save_file.'.xlsx');

		//$download_path = str_replace(SENEROOT,'/',$save_dir.'/'.$save_file.'.xlsx');
		$this->__forceDownload($save_dir.'/'.$save_file.'.xlsx');
	}
}
