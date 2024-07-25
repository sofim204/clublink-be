<?php
class Konsultasi extends JI_Controller{
	var $email_send = 1;
	var $a_company_kode = '00';
	var $domain_email = '@m.thecloudalert.com';
	var $kode_pattern = '%06d';
	var $media_user = 'media/pesan';
	var $auto_respon = 'Hai ka, mohon menunggu ya, staff kami akan segera membantu biasanya menjawab dalam beberapa menit.';

	public function __construct(){
    parent::__construct();
		$this->lib('site_config');
		$this->load("api_mobile/a_pengguna_model",'apm');
		$this->load("api_mobile/b_user_model",'bu');
		$this->load("api_mobile/d_pesan_model",'dp');
		$this->load("api_mobile/c_produk_model",'cp');
		$this->load("api_mobile/d_pesan_file_model",'dpf');

	}
	public function __reArrayFiles(&$file_post) {
    $file_ary = array();
		if(is_array($file_post)){
			reset($file_post);
			$file_post = current($file_post);
			$file_count = count($file_post['tmp_name']);
			$file_keys = array_keys($file_post);
			for ($i=0; $i<$file_count; $i++) {
				foreach ($file_keys as $key) {
					$file_ary[$i][$key] = $file_post[$key][$i];
				}
			}
		}
		return $file_ary;
	}
	private function __uploadUserImage($temp,$b_user_id){
		sleep(1);
		/*******************
		 * Only these origins will be allowed to upload images *
		 ******************/
		$folder = SENEROOT.DIRECTORY_SEPARATOR.$this->media_user.DIRECTORY_SEPARATOR;
		$folder = str_replace('\\','/',$folder);
		$folder = str_replace('//','/',$folder);
		$ifol = realpath($folder);
		//die($folder);
		if(!$ifol){
			mkdir($folder);
		}
		$ifol = realpath($folder);
		//die($ifol);


		if(!isset($temp['tmp_name'])){
			return 0;
		}
		//reset($_FILES);
		//$temp = current($_FILES);

		if (is_uploaded_file($temp['tmp_name'])){
			if (isset($_SERVER['HTTP_ORIGIN'])) {
				// same-origin requests won't set an origin. If the origin is set, it must be valid.
				header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
			}
				header('Access-Control-Allow-Credentials: true');
				header('P3P: CP="There is no P3P policy."');

			// Sanitize input
			if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
					header("HTTP/1.0 500 Invalid file name.");
					return 0;
			}
			// Verify extension
			$ext = pathinfo($temp['name'], PATHINFO_EXTENSION);
			if (!in_array(strtolower($ext), array("jpg", "jpeg", "png"))) {
					header("HTTP/1.0 500 Invalid extension.");
					return 0;
			}

			// Create magento style media directory
			$name  = date("His").$b_user_id;
			if (strlen($name)==1) $name=$name.'-';
			$name1 = date("Y");
			$name2 = date("m");
			if(PHP_OS == "WINNT"){
				if(!is_dir($ifol)) mkdir($ifol);
				$ifol = $ifol.DIRECTORY_SEPARATOR.$name1.DIRECTORY_SEPARATOR;
				if(!is_dir($ifol)) mkdir($ifol);
				$ifol = $ifol.DIRECTORY_SEPARATOR.$name2.DIRECTORY_SEPARATOR;
				if(!is_dir($ifol)) mkdir($ifol);
			}else{
				if(!is_dir($ifol)) mkdir($ifol,0775,true);
				$ifol = $ifol.DIRECTORY_SEPARATOR.$name1.DIRECTORY_SEPARATOR;
				if(!is_dir($ifol)) mkdir($ifol,0775,true);
				$ifol = $ifol.DIRECTORY_SEPARATOR.$name2.DIRECTORY_SEPARATOR;
				if(!is_dir($ifol)) mkdir($ifol,0775,true);
			}

			// Accept upload if there was no origin, or if it is an accepted origin

			$filetowrite = $ifol.$name.'.'.$ext;
			$filetowrite = str_replace('//','/',$filetowrite);

			if(file_exists($filetowrite)) unlink($filetowrite);
			move_uploaded_file($temp['tmp_name'], $filetowrite);
			return $this->media_user."/".$name1."/".$name2."/".$name.'.'.$ext;
		} else {
			// Notify editor that the upload failed
			//header("HTTP/1.0 500 Server Error");
			return 0;
		}
	}

	public function index(){
		$dt = $this->__init();
		$data = array();

		$this->status = 600;
		$this->message = 'Missing apikey or wrong apisess';

		$apikey = $this->input->get('apikey');
		$apisess = $this->input->get('apisess');

		$c = $this->apikey_check($apikey);
		$pelanggan = $this->bu->getByToken($apisess,'api_mobile');
		$data['konsultasi'] = array();
		$data['konsultasi_count'] = 0;
		if($c && isset($pelanggan->id)){
			$this->status = 200;
			$this->message = 'Success';

			$keyword = $this->input->request("keyword");
			if(strlen($keyword)<=2) $keyword = '';
			$page = (int) $this->input->request('page');
			if($page<=0) $page=0;


			$pesan = $this->dp->getAllByUserId($page,$pagesize=100,$sortCol="ldate",$sortDir="DESC",$keyword,$pelanggan->id);
			$pesan_count = $this->dp->countAllByUserId($keyword,$pelanggan->id);

			$data['konsultasi'] = $pesan;
			$data['konsultasi_count'] = (int) $pesan_count;
		}
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "konsultasi");
	}
	public function detail($id){
		$dt = $this->__init();
		$data = array();

		$this->status = 600;
		$this->message = 'Missing apikey or wrong apisess';

		$apikey = $this->input->get('apikey');
		$apisess = $this->input->get('apisess');

		$c = $this->apikey_check($apikey);
		$pelanggan = $this->bu->getByToken($apisess,'api_mobile');
		$data['konsultasi_detail'] = new stdClass();
		$data['konsultasi_balasan'] = array();
		if($c && isset($pelanggan->id)){
			$this->status = 200;
			$this->message = 'Success';
			$data['konsultasi_detail'] = $this->dp->getByIdAndUserId($id,$pelanggan->id);
			$blsn = $this->dp->getReplyByPesanId($id);

			$produk_ids = array();
			$d_pesan_ids = array();
			$d_pesan_files = array();
			foreach($blsn as &$bls){
				$d_pesan_ids[] = $bls->id;
				$bls->produk = array();
				if(!empty($bls->c_produk_ids)){
					$pids = explode(",",$bls->c_produk_ids);
					foreach($pids as $pid){
						if(!isset($produk_ids[$pid])) $produk_ids[$pid] = $pid;
					}
					$bls->c_produk_ids = $pids;
				}else{
					$bls->c_produk_ids = array();
				}
			}

			//get attachment
			if(is_array($d_pesan_ids)){
				if(count($d_pesan_ids)){
					$attachments = $this->dpf->getByPesanIds($d_pesan_ids);
					foreach($attachments as $atch){
						if(!isset($d_pesan_files[$atch->d_pesan_id])){
							$d_pesan_files[$atch->d_pesan_id] = array();
						}
						$d_pesan_files[$atch->d_pesan_id][] = $atch;
					}
					unset($atch);
					unset($attachments);
				}
			}

			$produks = array();
			if(count($produk_ids)){
				$produks = $this->cp->getByIds($produk_ids);

				$produk_ids = array();
				foreach($produks as $prd){
					$produk_ids[$prd->id] = $prd;
					if(strlen($produk_ids[$prd->id]->foto)>4){
						$produk_ids[$prd->id]->foto = base_url($produk_ids[$prd->id]->foto);
					}else{
						$produk_ids[$prd->id]->foto = base_url('media/uploads/default.jpg');
					}
					if(strlen($produk_ids[$prd->id]->thumb)>4){
						$produk_ids[$prd->id]->thumb = base_url($produk_ids[$prd->id]->thumb);
					}else{
						$produk_ids[$prd->id]->thumb = base_url('media/uploads/default.jpg');
					}
				}
			}
			unset($prd);
			unset($produks);
			foreach($blsn as &$balas){
				if(is_array($balas->c_produk_ids)){
					foreach($balas->c_produk_ids as $bcpids){
						if(isset($produk_ids[$bcpids])){
							$balas->produk[] = $produk_ids[$bcpids];
						}
					}
				}
				$d_pesan_id = $balas->id;
				$balas->attachments = array();
				if(isset($d_pesan_files[$d_pesan_id])) $balas->attachments = $d_pesan_files[$d_pesan_id];
			}
			$data['konsultasi_balasan'] = $blsn;
		}
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "konsultasi");
	}
	public function baru(){
		$dt = $this->__init();
		$data = array();

		$this->status = 600;
		$this->message = 'Missing apikey or wrong apisess';

		$apikey = $this->input->get('apikey');
		$apisess = $this->input->get('apisess');

		$c = $this->apikey_check($apikey);
		$pelanggan = $this->bu->getByToken($apisess,'api_mobile');
		$data['konsultasi'] = array();
		$data['konsultasi_count'] = 0;
		if($c && isset($pelanggan->id)){
			$di = array();
			$di['cdate'] = "NOW()";
			$di['b_user_id'] = $pelanggan->id;
			$di['b_user_nama'] = $pelanggan->fnama.' '.$pelanggan->lnama;
			$di['judul'] = $this->input->post('judul');
			$di['isi'] = $this->__format($this->input->post('isi'),'richtext');
			$di['utype'] = $this->input->post('utype');
			if(empty($di['utype'])) $di['utype'] = 'konsultasi';
			$di['is_complain'] = $this->input->post('is_complain');
			$di['reply_from'] = 'user';
			$di['is_read'] = 0;
			$di['is_reply'] = 0;

			if(strlen($di['isi'])<=3){
				$di['isi'] = 'Halo Ka...';
			}

			if(strlen($di['judul'])>0 && strlen($di['isi'])>0){
				$res = $this->dp->set($di);
				if($res){
					$this->status = 200;
					$this->message = 'Success';
				}else{
					$this->status = 900;
					$this->message = 'Tidak dapat menambahkan konsultasi baru, silakan coba beberapa saat lagi';
				}
			}else{
				$this->status = 300;
				$this->message = 'Missing one or more parameters';
			}
			$keyword = '';
			$pesan = $this->dp->getAllByUserId($page=0,$pagesize=1000,$sortCol="ldate",$sortDir="DESC",$keyword,$pelanggan->id);
			$pesan_count = $this->dp->countAllByUserId($keyword,$pelanggan->id);

			$data['konsultasi'] = $pesan;
			$data['konsultasi_count'] = (int) $pesan_count;
		}
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "konsultasi");
	}
	public function balas($id){
		$dt = $this->__init();
		$data = array();

		$this->status = 600;
		$this->message = 'Missing apikey or wrong apisess';

		$apikey = $this->input->get('apikey');
		$apisess = $this->input->get('apisess');

		$c = $this->apikey_check($apikey);
		$pelanggan = $this->bu->getByToken($apisess,'api_mobile');
		$data['konsultasi_detail'] = new stdClass();
		$data['konsultasi_balasan'] = array();
		$id = (int) $id;
		if($c && isset($pelanggan->id) && !empty($id)){
			$pesan = $this->dp->getByIdAndUserId($id,$pelanggan->id);
			if(isset($pesan->id)){
				$di = array();
				$di['b_user_id'] = $pelanggan->id;
				$di['b_user_nama'] = $pelanggan->fnama.' '.$pelanggan->lnama;
				$di['isi'] = $this->__format($this->input->post('isi'),'richtext');
				$di['reply_from'] = 'user';
				$di['d_pesan_id'] = $id;
				$di['cdate'] = "NOW()";

				$du = array();
				$du['ldate'] = 'NOW()';
				$du['is_complain'] = $this->input->post('is_complain');
				$du['is_reply'] = 0;
				$du['is_read'] = 0;

				if(strlen($di['isi'])){
					$res = $this->dp->set($di);
					if($res){
						$this->dp->update($id,$du);
						$fls = array();
						if(is_array($_FILES)){
							if(count($_FILES)){
								$fls = $this->__reArrayFiles($_FILES);
							}
						}
						if(count($fls)){
							foreach($fls as $fl){
								$uplodan = $this->__uploadUserImage($fl,$res);
								if($uplodan){
									$df = array();
									$df['d_pesan_id'] = $res;
									$df['utype'] = 'internal';
									$df['jenis'] = 'foto';
									$df['url'] = $uplodan;
									$this->dpf->set($df);
								}
							}
						}
						$this->status = 200;
						$this->message = 'Success';

						//auto responder
						sleep(2);
						$bls_user_jml = (int) $this->dp->countBalasan($id);
						if($bls_user_jml == 1){
							$this->__autoResponder($id);
						}
					}else{
						$this->status = 900;
						$this->message = 'Tidak dapat menambahkan konsultasi baru, silakan coba beberapa saat lagi';
					}
				}else{
					$this->status = 300;
					$this->message = 'Missing one or more parameters';
				}

				$data['konsultasi_detail'] = $this->dp->getByIdAndUserId($id,$pelanggan->id);
				$blsn = $this->dp->getReplyByPesanId($id);

				$produk_ids = array();
				$d_pesan_ids = array();
				$d_pesan_files = array();
				foreach($blsn as &$bls){
					$d_pesan_ids[] = $bls->id;
					$bls->produk = array();
					if(!empty($bls->c_produk_ids)){
						$pids = explode(",",$bls->c_produk_ids);
						foreach($pids as $pid){
							if(!isset($produk_ids[$pid])) $produk_ids[$pid] = $pid;
						}
						$bls->c_produk_ids = $pids;
					}else{
						$bls->c_produk_ids = array();
					}
				}

				//get attachment
				$attachments = $this->dpf->getByPesanIds($d_pesan_ids);
				foreach($attachments as $atch){
					if(!isset($d_pesan_files[$atch->d_pesan_id])){
						$d_pesan_files[$atch->d_pesan_id] = array();
					}
					$d_pesan_files[$atch->d_pesan_id][] = $atch;
				}
				unset($atch);
				unset($attachments);

				$produks = array();
				if(count($produk_ids)){
					$produks = $this->cp->getByIds($produk_ids);

					$produk_ids = array();
					foreach($produks as $prd){
						$produk_ids[$prd->id] = $prd;
						if(strlen($produk_ids[$prd->id]->foto)>4){
							$produk_ids[$prd->id]->foto = base_url($produk_ids[$prd->id]->foto);
						}else{
							$produk_ids[$prd->id]->foto = base_url('media/uploads/default.jpg');
						}
						if(strlen($produk_ids[$prd->id]->thumb)>4){
							$produk_ids[$prd->id]->thumb = base_url($produk_ids[$prd->id]->thumb);
						}else{
							$produk_ids[$prd->id]->thumb = base_url('media/uploads/default.jpg');
						}
					}
				}
				unset($prd);
				unset($produks);
				foreach($blsn as &$balas){
					if(is_array($balas->c_produk_ids)){
						foreach($balas->c_produk_ids as $bcpids){
							if(isset($produk_ids[$bcpids])){
								$balas->produk[] = $produk_ids[$bcpids];
							}
						}
					}
					$d_pesan_id = $balas->id;
					$balas->attachments = array();
					if(isset($d_pesan_files[$d_pesan_id])) $balas->attachments = $d_pesan_files[$d_pesan_id];
				}
				$data['konsultasi_balasan'] = $blsn;
			}else{
				$this->status = 446;
				$this->message = 'ID Pesan konsultasi tidak dapat ditemukan, silakan coba kembali';
			}
		}
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "konsultasi");
	}
	public function __autoResponder($d_pesan_id){
		$admin = $this->apm->getFirst();
    $b_user_nama = $admin->username; //Buat User Ganti Ku Session User
		$b_user_id = $admin->id;
		$di = array();
		$di['isi'] = $this->auto_respon;
    $di['c_produk_ids'] = "";
		$di['reply_from'] = 'admin';

    if(strlen($di["isi"]) > 0){
      $di["cdate"] = date("Y-m-d H:i:s");
      $di["is_reply"] = 1;
			$di["b_user_nama"] = $b_user_nama;
      $di["b_user_id"] = $b_user_id;
			$di['d_pesan_id'] = $d_pesan_id;
      $res = $this->dp->set($di);
      if($res){
				$du = array();
				$du['ldate'] = "NOW()";
				$du['is_read'] = 0;
				$du['is_reply'] = 1;
				$res2 = $this->dp->update($d_pesan_id,$du);
				return 1;
      }else{
				return 0;
      }
    }else{
			return 0;
		}
	}
}
