<?php
class TrfCost extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->load("api_admin/a_bank_trfcost_model",'abtcm');
	}
	public function index(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Authorization required");
			$this->__json_out($data);
			die();
		}
		$nation_code = $d['sess']->admin->nation_code;

		//get table alias
		$tbl_as = $this->abtcm->getTableAlias();
		$tbl2_as = $this->abtcm->getTableAlias2();
		$tbl3_as = $this->abtcm->getTableAlias3();

		//standard input
		$keyword = $this->input->post("sSearch");
		$page = $this->input->post("iDisplayStart");
		$pagesize = $this->input->post("iDisplayLength");
		$iSortCol_0 = $this->input->post("iSortCol_0");
		$sSortDir_0 = $this->input->post("sSortDir_0");

		//standard validation
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;
		$sortCol = "$tbl_as.id";
		$sortDir = strtoupper($sSortDir_0);
		if(empty($sortDir)) $sortDir = "DESC";
		if(strtolower($sortDir) != "desc") $sortDir = "ASC";
		switch($iSortCol_0){
			case 0:
				$sortCol = "CONCAT($tbl_as.nation_code,'-',$tbl_as.a_bank_id_to,'-',$tbl_as.a_bank_id_from)";
				break;
			case 1:
				$sortCol = "$tbl2_as.nama";
				break;
			case 2:
				$sortCol = "$tbl3_as.nama";
				break;
			case 3:
				$sortCol = "$tbl_as.utype";
				break;
			case 4:
				$sortCol = "$tbl_as.cost";
				break;
			case 5:
				$sortCol = "$tbl_as.is_active";
				break;
			default:
				$sortCol = "CONCAT($tbl_as.nation_code,'-',$tbl_as.a_bank_id_to,'-',$tbl_as.a_bank_id_from)";
		};

		//advanced filtering
		$is_active = $this->input->post("is_active");
		if($is_active == "1"){
		}else if($is_active == "0"){
		}else{
			$is_active = "";
		}

		//get from db
		$dcount = $this->abtcm->countAll($nation_code,$keyword,$is_active);
		$ddata = $this->abtcm->getAll($nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword,$is_active);

		//manipulate result
    foreach ($ddata as $d) {
			if(isset($d->uytpe)){
        if($d->uytpe == 'nominal'){
          $d->uytpe = 'Fixed Cost';
        }else{
          $d->uytpe = 'Percentage';
        }
      }
			if(isset($d->is_active)){
	      if($d->is_active == 1){
	        $d->is_active = 'Active';
	      }else{
	        $d->is_active = 'Inactive';
	      }
			}
    }

		//render result
		$this->status = 200;
		$this->message = 'Success';

		$this->__jsonDataTable($ddata,$dcount);
	}
	public function tambah(){
		$d = $this->__init();

		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Authorization required");
			$this->__json_out($data);
			die();
		}
    $nation_code = $d['sess']->admin->nation_code;

    //open transaction
		$this->abtcm->trans_start();

    //collect input
    $di = array();
		$di['nation_code'] = $nation_code;
    $di['a_bank_id_from'] = $this->input->post("a_bank_id_from");
    $di['a_bank_id_to'] = $this->input->post("a_bank_id_to");
    $di['utype'] = $this->input->post("utype");
    $di['cost'] = $this->input->post("cost");
    $di['is_active'] = (int) $this->input->post("is_active");
    $dt = $this->abtcm->getById($nation_code,$di['a_bank_id_from'],$di['a_bank_id_to']);

    //$this->debug($dt);
    //die();
    if(!isset($dt->nation_code)){
  		$res = $this->abtcm->set($di);
  		if($res){
  			$this->status = 200;
  			$this->message = 'Success';
  			$this->abtcm->trans_commit();
  		}else{
  			$this->abtcm->trans_rollback();
  			$this->status = 900;
  			$this->message = 'Cannot insert data to database';
  		}
    }else{
      $du = array();
      $du['cost'] = $this->input->post("cost");
      $du['utype'] = $this->input->post("utype");
      $du['is_active'] = (int) $this->input->post("is_active");
  		$res = $this->abtcm->update($nation_code,$di['a_bank_id_from'],$di['a_bank_id_to'],$du);
  		if($res){
  			$this->status = 200;
  			$this->message = 'Success';
  			$this->abtcm->trans_commit();
  		}else{
  			$this->abtcm->trans_rollback();
  			$this->status = 900;
  			$this->message = 'Cannot insert data to database';
  		}
    }
		$this->abtcm->trans_end();
		$this->__json_out($data);
	}
	public function detail($a_bank_id_to,$a_bank_id_from){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
    $nation_code = $d['sess']->admin->nation_code;

    //validation
  	$a_bank_id_to = (int) $a_bank_id_to;
		if($a_bank_id_to<=0){
			$this->status = 591;
			$this->message = 'Invalid A_BANK_ID_FROM';
			$this->__json_out($data);
			die();
		}
  	$a_bank_id_from = (int) $a_bank_id_from;
		if($a_bank_id_from<=0){
			$this->status = 592;
			$this->message = 'Invalid A_BANK_ID_TO';
			$this->__json_out($data);
			die();
		}

		$this->status = 200;
		$this->message = 'Success';
		$data = $this->abtcm->getById($nation_code,$a_bank_id_to,$a_bank_id_from);
		$this->__json_out($data);
	}
	public function edit($a_bank_id_from,$a_bank_id_to){
		//die('edit');
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Authorization required");
			$this->__json_out($data);
			die();
		}
    $nation_code = $d['sess']->admin->nation_code;

    //validation
  	$a_bank_id_to = (int) $a_bank_id_to;
		if($a_bank_id_to<=0){
			$this->status = 593;
			$this->message = 'Invalid A_BANK_ID_FROM';
			$this->__json_out($data);
			die();
		}
  	$a_bank_id_from = (int) $a_bank_id_from;
		if($a_bank_id_from<=0){
			$this->status = 594;
			$this->message = 'Invalid A_BANK_ID_TO';
			$this->__json_out($data);
			die();
		}

		if($a_bank_id_from>0 && $a_bank_id_to>0){
      $dt = $this->abtcm->getById($nation_code,$a_bank_id_from,$a_bank_id_to);
      if(!isset($dt->nation_code)){
        $du = array();
				$du['a_bank_id_from'] = $a_bank_id_from;
				$du['a_bank_id_to'] = $a_bank_id_to;
        $du['cost'] = $this->input->post("cost");
        $du['utype'] = $this->input->post("utype");
        $du['is_active'] = (int) $this->input->post("is_active");
  			$res = $this->abtcm->update($nation_code,$a_bank_id_from,$a_bank_id_to,$du);
  			if($res){
  				$this->status = 200;
  				$this->message = 'Sucess';
  			}else{
  				$this->status = 901;
  				$this->message = 'Cannot update data to database';
  			}
  		}else{
  			$this->status = 440;
  			$this->message = 'One or more parameter are missing';
  		}
    }else{
      $this->status = 899;
      $this->message = 'Data not found';
    }
		$this->__json_out($data);

	}
	public function hapus($a_bank_id_from,$a_bank_id_to){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Authorization required");
			$this->__json_out($data);
			die();
		}
    $nation_code = $d['sess']->admin->nation_code;

    //validation
  	$a_bank_id_to = (int) $a_bank_id_to;
		if($a_bank_id_to<=0){
			$this->status = 595;
			$this->message = 'Invalid A_BANK_ID_FROM';
			$this->__json_out($data);
			die();
		}
  	$a_bank_id_from = (int) $a_bank_id_from;
		if($a_bank_id_from<=0){
			$this->status = 596;
			$this->message = 'Invalid A_BANK_ID_TO';
			$this->__json_out($data);
			die();
		}

		$this->status = 200;
		$this->message = 'Success';
		$res = $this->abtcm->del($nation_code,$a_bank_id_from,$a_bank_id_to);
		if(!$res){
			$this->status = 902;
			$this->message = 'Failed deleting data from database';
		}
		$this->__json_out($data);
	}
}
