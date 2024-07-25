<?php
class Order extends JI_Controller{
	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_order';
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

	protected function __eBankingUrl($bank_tujuan){
		$url = '#';
		switch(strtolower($bank_tujuan)){
			case 'mandiri':
				$url = 'https://ib.bankmandiri.co.id/retail/Login.do?action=form&lang=in_ID';
				break;
			case 'bca':
				$url = 'https://ibank.klikbca.com/';
				break;
			case 'bni':
				$url = 'https://ibank.bni.co.id/';
				break;
			default:
				$url = '#';
		}
		return $url;
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


		$this->setTitle('Orders '.$this->site_suffix_admin);
		//$this->debug($cats);
		//die();

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		//$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("ecommerce/order/home_modal",$data);
		$this->putThemeContent("ecommerce/order/home",$data);


		$this->putJsContent("ecommerce/order/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
  public function detail($id=""){
    $id = (int) $id;
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
    $order = $this->dom->getById($nation_code,$id);
    if(!isset($order->id)){
			redir(base_url_admin('ecommerce/order/'));
      die();
    }
		$this->setTitle('Transaction Detail '.$order->invoice_code.' '.$this->site_suffix_admin);
    $data['order'] = $order;
		$data['order']->detail = $this->dodm->getByOrderId($id);

		$this->putThemeContent("ecommerce/order/detail_modal",$data);
		$this->putThemeContent("ecommerce/order/detail",$data);
		$this->putJsContent("ecommerce/order/detail_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
  }
}
