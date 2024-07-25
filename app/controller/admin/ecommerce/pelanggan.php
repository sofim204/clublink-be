<?php
	class Pelanggan extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_pelanggan';
		$this->load('admin/a_bank_model','abm');
		$this->load('admin/b_user_model','bum');
		$this->load('admin/b_user_bankacc_model','bubam');
		$this->load('admin/b_user_alamat_model','buam');
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

		$pengguna = $data['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$this->setTitle("Pelanggan E-Commerce ".$this->site_suffix_admin);
		$this->setTitle("Customers ".$this->site_suffix_admin);
		$data['api_url'] = base_url('api_admin/alamatongkir/');
		$data['provinsi'] = array();
		$data['user_role'] = $data['sess']->admin->user_role;
		$data['user_alias'] = $data['sess']->admin->user_alias;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));

		$this->putThemeContent("ecommerce/pelanggan/home_modal",$data);
		$this->putThemeContent("ecommerce/pelanggan/home",$data);


		$this->putJsContent("ecommerce/pelanggan/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
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

	public function cseller(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$pengguna = $data['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$this->setTitle("Pelanggan E-Commerce ".$this->site_suffix_admin);
		$this->setTitle("Customers ".$this->site_suffix_admin);
		$data['api_url'] = base_url('api_admin/alamatongkir/');
		$data['provinsi'] = array();

		//$this->loadCss(base_url('assets/css/datatables.min.css'));

		$this->putThemeContent("ecommerce/pelanggan/home_modal",$data);
		$this->putThemeContent("ecommerce/pelanggan/cseller",$data);


		$this->putJsContent("ecommerce/pelanggan/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
	public function cbuyer(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$pengguna = $data['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$this->setTitle("Pelanggan E-Commerce ".$this->site_suffix_admin);
		$this->setTitle("Customers ".$this->site_suffix_admin);
		$data['api_url'] = base_url('api_admin/alamatongkir/');
		$data['provinsi'] = array();

		//$this->loadCss(base_url('assets/css/datatables.min.css'));

		$this->putThemeContent("ecommerce/pelanggan/home_modal",$data);
		$this->putThemeContent("ecommerce/pelanggan/cbuyer",$data);


		$this->putJsContent("ecommerce/pelanggan/home_bottom",$data);
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
        $is_confirmed = $this->input->get("is_confirmed");
        $is_active = $this->input->get("is_active");

        $ddata = $this->bum->exportXlsPayment($nation_code,$keyword,$is_confirmed,$is_active);

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
        $objWorkSheet->getColumnDimension('A')->setWidth(30);
        $objWorkSheet->getColumnDimension('B')->setWidth(14);
        $objWorkSheet->getColumnDimension('C')->setWidth(50);

        //header
        $objWorkSheet
        ->setCellValue('A1', 'Email Address')
        ->setCellValue('B1', 'First Name')
        ->setCellValue('C1', 'Last Name')
        ;

        //setting gaya untuk header
        $objWorkSheet->getStyle('A1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
        $objWorkSheet->getStyle('B1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
        $objWorkSheet->getStyle('C1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);

        $i=2;
        $dot=".";
        $nomor = 1;
        if(count($ddata)>0){
            foreach($ddata as $pb){
                //mengisikan masing2 data
                $objWorkSheet->setCellValue('A'.$i, $pb->email);
                $objWorkSheet->setCellValue('B'.$i, $dot);
                $objWorkSheet->setCellValue('C'.$i, $pb->nama);

                //set border ke masing2 kolom
                $objWorkSheet->getStyle('A'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('B'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('C'.$i)->applyFromArray($styleborder);
                //$objWorkSheet->getStyle('W'.$i)->applyFromArray($styleborder);
                $nomor++;
                $i++;
            }
        }else{
            $objWorkSheet->setCellValue('A'.$i,'Empty result')->mergeCells('A'.$i.':C'.$i.'');
            $objWorkSheet->getStyle('A'.$i.':C'.$i.'')->getAlignment()->applyFromArray($style);
            $objWorkSheet->getStyle('A'.$i.':C'.$i.'')->applyFromArray($styleborder);
        }
        $objPHPExcel->setActiveSheetIndex(0);

        //save file
        $save_dir = $this->__checkDir(date("Y/m"));
        $save_file = 'export_email';
        $save_file = str_replace(' ','',str_replace('/','',$save_file));

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        if(file_exists($save_dir.'/'.$save_file.'.xlsx')) unlink($save_dir.'/'.$save_file.'.xlsx');
        $objWriter->save($save_dir.'/'.$save_file.'.xlsx');

        //$download_path = str_replace(SENEROOT,'/',$save_dir.'/'.$save_file.'.xlsx');
        $this->__forceDownload($save_dir.'/'.$save_file.'.xlsx');
    }
    // Improve By Aditya Adi Prabowo 8/18/2020 14:14
    // Add button to print Xls Detail Data User
    // Start Improve
    public function downloaddetail_xls(){
        $data = $this->__init();

        if(!$this->admin_login){
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;
        $keyword = '';
        $is_confirmed = $this->input->get("is_confirmed");
        $is_active = $this->input->get("is_active");

        $ddata = $this->bum->exportXlsDetail($nation_code,$keyword,$is_confirmed,$is_active);

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
        $objWorkSheet->getColumnDimension('A')->setWidth(50);
        $objWorkSheet->getColumnDimension('B')->setWidth(40);
        $objWorkSheet->getColumnDimension('C')->setWidth(80);
        $objWorkSheet->getColumnDimension('D')->setWidth(50);
        $objWorkSheet->getColumnDimension('E')->setWidth(30);
        $objWorkSheet->getColumnDimension('F')->setWidth(15);
        $objWorkSheet->getColumnDimension('G')->setWidth(15);

        //header
        $objWorkSheet
        ->setCellValue('A1', 'Name')
        ->setCellValue('B1', 'Email')
        ->setCellValue('C1', 'Address')
        ->setCellValue('D1', 'Address Detail')
        ->setCellValue('E1', 'Zipcode')
        ->setCellValue('F1', 'Telephone')
        ->setCellValue('G1', 'Device')
        ;

        //setting gaya untuk header
        $objWorkSheet->getStyle('A1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
        $objWorkSheet->getStyle('B1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
        $objWorkSheet->getStyle('C1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
        $objWorkSheet->getStyle('D1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
        $objWorkSheet->getStyle('E1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
        $objWorkSheet->getStyle('F1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
        $objWorkSheet->getStyle('G1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);

        $i=2;
        $dot=".";
        $nomor = 1;
        if(count($ddata)>0){
            foreach($ddata as $pb){
                //mengisikan masing2 data
                $objWorkSheet->setCellValue('A'.$i, $pb->nama);
                $objWorkSheet->setCellValue('B'.$i, $pb->email);
                $objWorkSheet->setCellValue('C'.$i, $pb->alamat2);
                $objWorkSheet->setCellValue('D'.$i, $pb->catatan);
                $objWorkSheet->setCellValue('E'.$i, $pb->kodepos);
                $objWorkSheet->setCellValue('F'.$i, $pb->telp);
                $objWorkSheet->setCellValue('G'.$i, $pb->device);

                //set border ke masing2 kolom
                $objWorkSheet->getStyle('A'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('B'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('C'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('D'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('E'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('F'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('G'.$i)->applyFromArray($styleborder);
                //$objWorkSheet->getStyle('W'.$i)->applyFromArray($styleborder);
                $nomor++;
                $i++;
            }
        }else{
            $objWorkSheet->setCellValue('A'.$i,'Empty result')->mergeCells('A'.$i.':G'.$i.'');
            $objWorkSheet->getStyle('A'.$i.':G'.$i.'')->getAlignment()->applyFromArray($style);
            $objWorkSheet->getStyle('A'.$i.':G'.$i.'')->applyFromArray($styleborder);
        }
        $objPHPExcel->setActiveSheetIndex(0);

        //save file
        $save_dir = $this->__checkDir(date("Y/m"));
        $save_file = 'export_data_customer'; // by Muhammad Sofi 29 December 2021 15:00 | fix typo on saved file name
        $save_file = str_replace(' ','',str_replace('/','',$save_file));

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        if(file_exists($save_dir.'/'.$save_file.'.xlsx')) unlink($save_dir.'/'.$save_file.'.xlsx');
        $objWriter->save($save_dir.'/'.$save_file.'.xlsx');

        //$download_path = str_replace(SENEROOT,'/',$save_dir.'/'.$save_file.'.xlsx');
        $this->__forceDownload($save_dir.'/'.$save_file.'.xlsx');
    }

	public function transaction(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$pengguna = $data['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$this->setTitle("Pelanggan E-Commerce ".$this->site_suffix_admin);
		$this->setTitle("Customers ".$this->site_suffix_admin);
		$data['api_url'] = base_url('api_admin/alamatongkir/');
		$data['provinsi'] = array();

		//$this->loadCss(base_url('assets/css/datatables.min.css'));

		$this->putThemeContent("ecommerce/pelanggan/transaction_tracking_modal",$data);
		$this->putThemeContent("ecommerce/pelanggan/transaction",$data);


		$this->putJsContent("ecommerce/pelanggan/transaction_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
	// End Improve

	public function detail($id){
		$data = $this->__init();
		// $id = (int) $id;
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}
		
		$pengguna = $data['sess']->admin;
		$nation_code = $pengguna->nation_code;

		if(empty($id)){
			redir(base_url_admin('ecommerce/pelanggan/'));
			die();
		}

		$pelanggan = $this->bum->getById($nation_code,$id);
		if(!isset($pelanggan->id)){
			redir(base_url_admin('ecommerce/pelanggan/'));
			die();
		}
		if(!isset($pelanggan->image)) $pelanggan->image = '';
		if(strlen($pelanggan->image)<=10) $pelanggan->image = 'media/user/default-profile-picture.png';

		$is_active = 1;
		$data['detail_address'] = $this->buam->getDetailAddress($nation_code,$is_active,$pelanggan->id);
		$data['bank_list'] = $this->abm->get($nation_code);
		$data['pelanggan'] = $pelanggan;
		$data['bank_account'] = $this->bubam->getByUserId($nation_code,$pelanggan->id);

		if(!isset($data['bank_account']->nomor)) $data['bank_account']->nomor = '';
		if(!isset($data['bank_account']->nama)) $data['bank_account']->nama = '';
		if(!isset($data['bank_account']->bank_nama)) $data['bank_account']->bank_nama = '';

		// by Muhammad Sofi 15 February 2022 11:07 | show verification number in detail customer
		$verification_number = $this->bum->getByIdVerifCode($nation_code, $pelanggan->id);
		$data['verification_number'] = $verification_number;
		//$this->debug($data['pelanggan']);
		//die();

		// if(isset($pelanggan->fb_id)) {
		// 	if (!empty($pelanggan->fb_id) || ($pelanggan->fb_id) != "NULL") {
		// 		$data['pelanggan']->email = str_replace("@sellon.net","@gmail.com.", $pelanggan->email); 
		// 	}
		// }

		// if(isset($pelanggan->register_from)) {
		// 	if ($pelanggan->register_from == "phone") {
		// 		// $data['pelanggan']->email = str_replace("@sellon.net","@gmail.com.", $pelanggan->email);
		// 		$data['pelanggan']->email = $pelanggan->telp."@sellon.net";
		// 	}
		// }

		$data['user_role'] = $data['sess']->admin->user_role;

		$this->setTitle($pelanggan->fnama." ".$this->site_suffix_admin);

		//$this->loadCss(base_url('assets/css/datatables.min.css'));

		//$this->putThemeContent("ecommerce/pelanggan/home_modal",$data);
		$this->putThemeContent("ecommerce/pelanggan/detail",$data);


		$this->putJsContent("ecommerce/pelanggan/detail_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
	public function __e($val,$rep="-"){
		if(empty($val)) return $rep;
		return $val;
	}
	public function __i($val,$rep="media/user/default.png"){
		if(strlen($val)>4){
			return base_url($val);
		}else{
			return base_url($rep);
		}
	}
	public function __u($val,$rep=0){
		if(!empty($val)){
			return 'Rp '.number_format($val,0,',','.');
		}else{
			return 'Rp '.number_format($rep,0,',','.');
		}
	}
	public function __t($val,$format='hari_tanggal',$rep='-'){
		if(is_null($val)){
			return $rep;
		}else if(!empty($val) || $val != '0000-00-00 00:00:00' || $val != '0000-00-00' || $val != '' || $val != '0'){
			return $this->__dateIndonesia($val,$format);
		}else{
			return $rep;
		}
	}
}
