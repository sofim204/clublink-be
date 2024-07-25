<?php
class Galeri extends SENE_Controller{
	var $status = 0;
	var $treehtml = '';
	var $is_login_admin;
	var $module = "cms_galeri";
	var $is_login_user = "";
	var $page = "cms_galeri";

	public function __construct(){
    parent::__construct();
		$this->lib("site_config");
		$this->lib("sene_json_engine");
		$this->load("admin/d_galeri_model");
		$this->load("admin/d_galeri_item_model");
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
	private function __init(){
		$data = array();
		$data['site_config'] = $this->site_config;
		$sess = $this->getKey();

		if(!is_object($sess)){
			$sess = new stdClass();
		}

		if(!isset($sess->user)){
			$sess->user = new stdClass();
		}

		if(isset($sess->user->id)) if(!empty($sess->user->id)) $this->is_login_user = true;
		if(isset($sess->user->modules)){
			if(count($sess->user->modules)>0){
				foreach($sess->user->modules as $m){
					if(empty($m->module) && strtolower($m->rule)=="allowed_except"){
						$this->allowed = 1;
						break;
					}else if($this->module==$m->module){
						if(strtolower($m->rule)=="allowed" || strtolower($m->rule)=="disallowed_except"){
							$this->allowed = 1;
							break;
						}
					}
				}
			}
		}

		if(!$this->allowed){
			$this->loadLayout("disallowed_json",$data);
			$this->render();
			die();
		}

		$data['page'] = $this->page;
		$data['module'] = $this->module;
		$data['sess'] = $sess;
		return $data;
	}
	public function index($utype="kaskecil"){
		$data = array();
		$pesan = array();
		$data = $this->__init();
		$res = array();
		$draw = $this->input->post("draw");
		$sval = $this->input->post("search");
		$sSearch = $this->input->post("sSearch");
		$sEcho = $this->input->post("sEcho");
		$page = (int) $this->input->post("iDisplayLength");
		$start = (int) $this->input->post("iDisplayStart");
		$length = (int) $this->input->post("iDisplayLength");
		$supplier = $this->input->post("supplier");

		$iSortCol_0 = $this->input->post("iSortCol_0");
		$sSortDir_0 = $this->input->post("sSortDir_0");


		$tglmin = $this->input->post("tgl_min");
		$tglmax = $this->input->post("tgl_max");

		$utype = $this->input->post("utype");

		$tbl_as = $this->d_galeri_model->getTableAlias();

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
				$sortCol = $tbl_as.".title";
				break;
			case 2:
				$sortCol = $tbl_as.".kategori";
				break;
			case 3:
				$sortCol =  $tbl_as.".featured_image";
				break;
			case 4:
				$sortCol =  $tbl_as.".cdate";
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


		$count = $this->d_galeri_model->getAllCount($keyword,$tglmax,$tglmin);

		$pengumuman = array();
		$pengumuman = $this->d_galeri_model->getAll($start,$length,$sortCol,$sortDir,$keyword,$tglmax,$tglmin);

		//$this->debug($pengumuman);

		$totalFiltered = $count;
		$totalData =  $count;

		$i =  $start;


		$data = array();
		if(is_array($pengumuman)){
			//var_dump(count($pengumuman));
			//die();
			foreach($pengumuman as &$pe){
				$i++;

				if(isset($pe->cdate))
					$pe->cdate = date("j F Y",strtotime($pe->cdate));

				if(isset($pe->featured_image)){
					$fi = $pe->featured_image;
					if(empty($fi)){
						$fi = $this->site_config->cms_blog.'/default.jpg';
					}
					$pe->featured_image =
					'<img src="'.base_url($fi).'" style="width: 60px; height: auto; max-height: 60px;" />';
				}
				$pe->action  = '<div class="dropdown">';
				$pe->action .= '<button class="btn btn-default dropdown-toggle" type="button" id="ddma1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
    		$pe->action .= '<i class="fa fa-cog"></i> <span class="caret"></span>';
  			$pe->action .= '</button>';

				$pe->action .= '<ul class="dropdown-menu" aria-labelledby="ddma1">';
				$pe->action .= '<li><a href="'.base_url_admin('cms/galeri/edit/'.$pe->id).'" ><i class="fa fa-edit"></i> Edit</a></li>';
				$pe->action .= '<li><a href="#" class="adelete" data-id="'.$pe->id.'" ><i class="fa fa-trash-o"></i> Delete</a></li>';
				$pe->action .= '</ul>';

				$pe->action .= '</div>';
				if(isset($pe->id)) $pe->id = $i;
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
			$data['result'] = $this->d_galeri_model->getById($id);
			if(isset($data['result']->id)) $data['result'] = $data['result'];
		}
		$this->sene_json_engine->out($data);
	}
	public function update($id=""){
		$data = array();
		$data['status'] = 0;
		$data['result'] = 'One or more parameter required';
		$title = $this->input->post("title");
		if(!empty($title)){

			$slug = $this->slugify($title);
			$slug_check = $this->d_galeri_model->checkSlug($slug);

			$try =0;
			while( ($slug_check>0) && ($try<=5) ){
				$slug .= $slug.'-'.rand(0,999);
				$slug_check = $this->d_galeri_model->checkSlug($slug);
				$try++;
			}

			$d = array();
			$d['cdate'] = "NOW()";
			$d['title'] = $this->input->post('title');
			$d['kategori'] = $this->input->post('kategori');
			$d['content'] = $this->input->post('content');
			$d['slug'] = $slug;

			$res = $this->d_galeri_model->update($id,$d);
			if($res){
				$d_galeri_id = $id;
				$dgi = $this->input->post('image');
				$dgc = $this->input->post('caption');

				$items = array();
				$i=0;
				foreach($dgi as $it){
					$gi = array();
					$gi['d_galeri_id'] = $d_galeri_id;
					$gi['image'] = $it;
					$gi['caption'] = '';
					$items[$i] = $gi;
					$i++;
				}

				$i=0;
				foreach($dgc as $it){
					if(isset($items[$i]['caption'])){
						$items[$i]['caption'] = $it;
						$i++;
					}
				}
				$this->d_galeri_item_model->delByGaleriId($d_galeri_id);
				$res2 = $this->d_galeri_item_model->setMass($items);


				if(isset($items[0]['image'])){
					$du = array();
					$du['featured_image'] = $items[0]['image'];
					$this->d_galeri_model->update($d_galeri_id,$du);
				}

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
		if($s['sess']->user->id) $uid = $s['sess']->user->id;
		if(!empty($title)){

			$slug = $this->slugify($title);
			$slug_check = $this->d_galeri_model->checkSlug($slug);

			$try =0;
			while( ($slug_check>0) && ($try<=5) ){
				$slug .= $slug.'-'.rand(0,999);
				$slug_check = $this->d_galeri_model->checkSlug($slug);
				$try++;
			}


			$s = $this->__init();

			$d = array();
			$d['b_user_id'] = $uid;
			$d['cdate'] = "NOW()";
			$d['title'] = $this->input->post('title');
			$d['kategori'] = $this->input->post('kategori');
			$d['content'] = $this->input->post('content');
			$d['slug'] = $slug;


			$dgi = $this->input->post('image');
			$dgc = $this->input->post('caption');

			$res = $this->d_galeri_model->set($d);
			if($res){
				$d_galeri_id = $res;
				$items = array();
				$i=0;
				foreach($dgi as $it){
					$gi = array();
					$gi['d_galeri_id'] = $d_galeri_id;
					$gi['image'] = $it;
					$gi['caption'] = '';
					$items[$i] = $gi;
					$i++;
				}

				$i=0;
				foreach($dgc as $it){
					if(isset($items[$i]['caption'])){
						$items[$i]['caption'] = $it;
						$i++;
					}
				}

				$res2 = $this->d_galeri_item_model->setMass($items);

				if(isset($items[0]['image'])){
					$du = array();
					$du['featured_image'] = $items[0]['image'];
					$this->d_galeri_model->update($d_galeri_id,$du);
				}


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
			$res = $this->d_galeri_model->del($id);
			if($res){
				$d_galeri_id = $id;
				$this->d_galeri_item_model->delByGaleriId($d_galeri_id);
				$data['status'] = 1;
				$data['result'] = 'Berhasil';
			}else{
				$data['result'] = 'Gagal';
			}
		}
		$this->sene_json_engine->out($data);
	}

}
