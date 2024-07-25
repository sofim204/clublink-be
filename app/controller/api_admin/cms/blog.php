<?php
class Blog extends JI_Controller{
	var $status = 0;
	var $treehtml = '';
	var $is_login_admin;
	var $module = "cms_blog";
	var $is_login_user = "";
	var $page = "cms_blog";

	public function __construct(){
    parent::__construct();
		$this->lib("sene_json_engine");
		$this->load("api_admin/d_blog_model");
		$this->setTheme("admin/");
		$this->is_login_user = 0;
	}

	private function slugify($text){
		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		// trim
		$text = trim($text, '-');

		// remove duplicate -
		$text = preg_replace('~-+~', '-', $text);

		// lowercase
		$text = strtolower($text);

		return $text;
	}
	public function index($utype="kaskecil"){
		$data = array();
		$pesan = array();
		$s = $this->__init();
		$res = array();
		$draw = $this->input->post("draw");
		$sval = $this->input->post("search");
		$sSearch = $this->input->post("sSearch");
		$sEcho = $this->input->post("sEcho");
		$page = $this->input->post("iDisplayLength");
		$start = $this->input->post("iDisplayStart");
		$length = $this->input->post("iDisplayLength");
		$supplier = $this->input->post("supplier");

		$iSortCol_0 = $this->input->post("iSortCol_0");
		$sSortDir_0 = $this->input->post("sSortDir_0");


		$tglmin = $this->input->post("tgl_min");
		$tglmax = $this->input->post("tgl_max");

		$utype = $this->input->post("utype");

		$tbl_as = $this->d_blog_model->getTableAlias();

		if(empty($utype)) $utype = '';
		if(empty($tglmin)) $tglmin = '';
		if(empty($tglmax)) $tglmax = '';

		//echo $tglmax;
		//die();

		$sortCol = "cdate";
		$sortDir = strtoupper($sSortDir_0);
		if(empty($sortDir)) $sortDir = "DESC";
		if($sortDir != "DESC") $sortDir = "ASC";

		switch($iSortCol_0){
			case 0:
				$sortCol = $tbl_as.".id";
				break;
			case 1:
				$sortCol = "CONCAT(bu.fnama,' ',bu.lnama)";
				break;
			case 2:
				$sortCol = $tbl_as.".title";
				break;
			case 3:
				$sortCol = $tbl_as.".excerpt";
				break;
			case 4:
				$sortCol =  $tbl_as.".featured_image";
				break;
			case 5:
				$sortCol =  $tbl_as.".cdate";
				break;
			case 6:
				$sortCol =  $tbl_as.".status";
				break;
			default:
				$sortCol = $tbl_as.".id";
		}

		if(empty($draw)) $draw = 0;
		if(empty($length)) $length=10;
		if(empty($start)) $start=0;

		$keyword = $sSearch;
		if(isset($sval['value'])){
			$keyword = $sval['value'];
		}


		$count = $this->d_blog_model->getAllCount($utype,$keyword,$tglmax,$tglmin);

		$pengumuman = array();
		$pengumuman = $this->d_blog_model->getAll($utype,$start,$length,$sortCol,$sortDir,$keyword,$tglmax,$tglmin);

		//$this->debug($pengumuman);

		$totalFiltered = $count;
		$totalData =  $count;

		$data = array();
		if(is_array($pengumuman)){
			//var_dump(count($pengumuman));
			//die();
			foreach($pengumuman as &$pe){
				if(isset($pe->cdate))
					$pe->cdate = date("j F Y",strtotime($pe->cdate));

				if(isset($pe->featured_image)){
					$fi = $pe->featured_image;
					if(empty($fi)){
						$fi = $this->cms_blog.'/default.jpg';
					}
					$pe->featured_image =
					'<img src="'.base_url($fi).'" style="width: 60px; height: auto; max-height: 60px;" />';
				}
			}
			unset($pe);
			$i=0;

			//$this->debug($pengumuman);
			//die();
			foreach($pengumuman as $p){
				$d = array();
				foreach($p as $key=>$val){
					$d[] = $val;
				}
				$data[$i] = $d;
				$i++;
			}
			unset($val);
			unset($key);
			unset($p);
		}
		$i=0;

		$json_data = array(
			"pesan"           => $pesan,
			"draw"            => intval( $draw ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data,   // total data array
		);
		$this->sene_json_engine->out($json_data);
	}
	public function detail($id=""){
		$data = array();
		$data['status'] = 0;
		if(!empty($id)){
			$data['status'] = 1;
			$data['result'] = $this->d_blog_model->getById($id);
			if(isset($data['result']->id)) $data['result'] = $data['result'];
		}
		$this->sene_json_engine->out($data);
	}
	public function update($id=""){
		$data = array();
		$data['status'] = 0;
		$data['result'] = 'One or more parameter required';
		$title = $this->input->post("title");
		$s = $this->__init();
		if(!empty($title)){
			$d = array();
			foreach($_POST as $key=>$val){
				$d[$key] = $val;
			}
			$d['cdate'] = "NOW()";

			$res = $this->d_blog_model->update($id,$d);
			if($res){
				$data['status'] = 1;
				$data['result'] = 'Berhasil';
			}else{
				$data['result'] = 'Gagal';
			}
		}
		$this->sene_json_engine->out($data);
	}
	public function add(){
		$s = $this->__init();
		$data = array();
		$data['status'] = 0;
		$data['result'] = 'One or more parameter required';
		$uid = 'NULL';

		$title = $this->input->post("title");

		if(!empty($title) && $this->admin_login){
			if($s['sess']->admin->id) $uid = $s['sess']->admin->id;

			$slug = $this->slugify($title);
			$slug_check = $this->d_blog_model->checkSlug($slug);

			$try =0;
			while( ($slug_check>0) && ($try<=5) ){
				$slug .= $slug.'-'.rand(0,999);
				$slug_check = $this->d_blog_model->checkSlug($slug);
				$try++;
			}


			$s = $this->__init();

			$d = array();
			$d['b_user_id'] = $uid;
			$d['cdate'] = "NOW()";
			foreach($_POST as $key=>$val){
				$d[$key] = $val;
			}
			$d['slug'] = $slug;
			//$this->debug($d);
			//die();
			$res = $this->d_blog_model->set($d);
			if($res){
				$data['status'] = 1;
				$data['result'] = 'Berhasil';
			}else{
				$data['result'] = 'Gagal';
			}
		}
		$this->sene_json_engine->out($data);
	}
	public function del($id=""){
		$data = array();
		$data['status'] = 0;
		$data['result'] = 'One or more parameter required';
		if(!empty($id)){
			$res = $this->d_blog_model->del($id);
			if($res){
				$data['status'] = 1;
				$data['result'] = 'Berhasil';
			}else{
				$data['result'] = 'Gagal';
			}
		}
		$this->sene_json_engine->out($data);
	}

}
