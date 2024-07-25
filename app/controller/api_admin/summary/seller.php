<?php
class Seller extends JI_Controller{
  public function __construct(){
    parent::__construct();
    $this->load("api_admin/a_negara_model","anm");
    $this->load('api_admin/c_produk_model','cpm');
    $this->load('api_admin/d_order_detail_model','dodm');;
  }
  public function index($b_user_id=""){
		$d = $this->__init();
		$data = array();
    $data['freeproduct_count'] = 0;
		$data['product_count'] = 0;
		$data['order_count'] = 0;
		$data['sales_total'] = 0;
		$data['rejected_count'] = 0;
		$data['confirmed_count'] = 0;
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

    //check b_user_id
    $b_user_id = (int) $b_user_id;
    if($b_user_id<=0){
			$this->status = 400;
			$this->message = 'Invalid B_USER_ID';
      $this->__json_out($data);
      die();
    }

    $this->status = 200;
    $this->message = 'Success';

    //get data product
    $data['product_count'] = $this->cpm->countBySeller($nation_code,$b_user_id);

    //get data order
    $seller = $this->dodm->getSalesBySeller($nation_code,$b_user_id);
    if(isset($seller->sales_total)) $data['sales_total'] = $seller->sales_total;
		$data['rejected_count'] = $this->dodm->countRejectedBySeller($nation_code,$b_user_id);
		$data['confirmed_count'] = $this->dodm->countConfirmedBySeller($nation_code,$b_user_id);
    $data['order_count'] = $data['rejected_count']+$data['confirmed_count'];
    $this->__json_out($data);
  }
	public function product($b_user_id){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

    //check b_user_id
    $b_user_id = (int) $b_user_id;
    if($b_user_id<=0){
			$this->status = 400;
			$this->message = 'Invalid B_USER_ID';
      $this->__json_out($data);
      die();
    }

		$negara = $this->anm->getByNationCode($nation_code);
		if(!isset($negara->simbol_mata_uang)) $negara->simbol_mata_uang = '-';

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
		$tbl_as = $this->cpm->getTableAlias();
		$tbl2_as = $this->cpm->getTableAlias2();
		$tbl3_as = $this->cpm->getTableAlias3();
		$tbl4_as = $this->cpm->getTableAlias4();

		switch($iSortCol_0){
			case 0:
				$sortCol = "$tbl_as.id";
				break;
			case 1:
				$sortCol = "$tbl3_as.nama";
				break;
			case 2:
				$sortCol = "$tbl_as.nama";
				break;
			case 3:
				$sortCol = "$tbl_as.harga_jual";
				break;
			case 4:
				$sortCol = "$tbl4_as.nama";
				break;
			case 5:
				$sortCol = "$tbl_as.courier_services";
				break;
			default:
				$sortCol = "$tbl_as.id";
		}

		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;

		$keyword = $sSearch;

		$this->status = '200';
		$this->message = 'Success';
		$dcount = $this->cpm->countBySeller($nation_code,$b_user_id,$keyword);
		$ddata = $this->cpm->getBySeller($nation_code,$b_user_id,$page,$pagesize,$sortCol,$sortDir,$keyword);

		foreach($ddata as &$gd){
			if(isset($gd->harga_jual)) $gd->harga_jual = $negara->simbol_mata_uang.number_format($gd->harga_jual,2,'.',',');
		}
		$this->__jsonDataTable($ddata,$dcount);
	}
}
