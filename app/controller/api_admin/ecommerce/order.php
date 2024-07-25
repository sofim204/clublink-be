<?php
class Order extends JI_Controller{
	var $is_email = 1;

	public function __construct(){
    parent::__construct();
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_produk';
		$this->load("api_admin/b_user_model",'bum');
		$this->load("api_admin/c_produk_model",'cpm');
		$this->load("api_admin/d_order_model",'dom');
		$this->load("api_admin/d_order_detail_model",'dodm');
	}

	private function __uploadImageKonfirmasi($tran_id){
		/*******************
		 * Only these origins will be allowed to upload images *
		 *****************
		*/
		$folder = SENEROOT.DIRECTORY_SEPARATOR.'media/konfirmasi'.DIRECTORY_SEPARATOR;
		$folder = str_replace('\\','/',$folder);
		$folder = str_replace('//','/',$folder);
		$ifol = realpath($folder);
		//die($folder);
		if(!$ifol){
			mkdir($folder);
		}
		$ifol = realpath($folder);
		//die($ifol);

		reset($_FILES);
		$temp = current($_FILES);
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
			if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("jpg", "png",'jpeg'))) {
					header("HTTP/1.0 500 Invalid extension.");
					return 0;
			}

			// Create wordpress style media directory
			$cy = date('Y'); //current year
			$cm = date('m'); //and month
			if(PHP_OS == "WINNT"){
				if(!is_dir($ifol)) mkdir($ifol);
				$ifol = $ifol.DIRECTORY_SEPARATOR.$cy.DIRECTORY_SEPARATOR;
				if(!is_dir($ifol)) mkdir($ifol);
				$ifol = $ifol.DIRECTORY_SEPARATOR.$cm.DIRECTORY_SEPARATOR;
				if(!is_dir($ifol)) mkdir($ifol);
			}else{
				if(!is_dir($ifol)) mkdir($ifol,0775);
				$ifol = $ifol.DIRECTORY_SEPARATOR.$cy.DIRECTORY_SEPARATOR;
				if(!is_dir($ifol)) mkdir($ifol,0775);
				$ifol = $ifol.DIRECTORY_SEPARATOR.$cm.DIRECTORY_SEPARATOR;
				if(!is_dir($ifol)) mkdir($ifol,0775);
			}


			$name  = md5($tran_id);
			$ext = strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION));
			// Accept upload if there was no origin, or if it is an accepted origin
			$filetowrite = $ifol.$name.'.'.$ext;

			if(file_exists($filetowrite)) unlink($filetowrite);
			move_uploaded_file($temp['tmp_name'], $filetowrite);
			if(file_exists($filetowrite)){
				$this->lib("wideimage/WideImage","inc");
				WideImage::load($filetowrite)->resize(500)->saveToFile($filetowrite,70);
				return "media/konfirmasi/".$cy.'/'.$cm.'/'.$name.'.'.$ext;
			} else {
				return 0;
			}

		} else {
			// Notify editor that the upload failed
			//header("HTTP/1.0 500 Server Error");
			return 0;
		}
	}
	private function __emailTemplateKonfirmasi($email,$nama,$order_id){
		$h  = '<p>Hi '.$nama.'</p>';
		$h .= '<p>Pembayaran kamu sedang kami verifikasi.</p>';
		$h .= '<p>Mohon bersabar yaa...</p>';
		$h .= '<p>Untuk memantau pesanan kamu, cukup buka aja diaplikasi</p>';
		$h .= '<p>Akan kita kabarin lagi secepatnya...</p>';
		$h .= '<br /><br />--<br />';
		$h .= '<p>Salaam,<br />'.$this->site_name.'</p>';

		$this->lib('sene_email_sender');
		$this->sene_email_sender->from($this->site_name,$this->site_email);
		$this->sene_email_sender->to($email,$nama);
		$this->sene_email_sender->subject("[$this->site_name] Pembayaran sedang diverifikasi");
		$this->sene_email_sender->html($h);
		$this->sene_email_sender->send();
	}
	private function __emailTemplateProses($email,$nama,$order_id){
		$this->lib('seme_email');
		$replacer = array();
		$replacer['fnama'] = $nama;
		$replacer['order_id'] = $order_id;
		$this->seme_email->replyto($this->site_name,$this->site_replyto);
		$this->seme_email->from($this->site_email,$this->site_name);
		$this->seme_email->subject('Pesanan di Proses TranID #'.$order_id);
		$this->seme_email->to($email,$nama);
		$this->seme_email->template('order_payment_confirm');
		$this->seme_email->replacer($replacer);
		$this->seme_email->send();
	}
	private function __emailTemplateProses2($email,$nama,$order_id){
		$h  = '<p>Hi '.$nama.',</p>';
		$h .= '<p>Pembayaran kamu sudah kami terima dan valid.</p>';
		$h .= '<p>Terimakasih telah melakukan pembayaran.</p>';
		$h .= '<p>Orderan dengan ID Order #'.$order_id.' Sekarang, tim kami akan menyiapkan pesanan Anda dan memastikan bahwa kualitasnya sesuai dengan kebutuhan kulitmu.</p>';
		$h .= '<p>Untuk memantau status pesanan Anda, silakan akses di Calysta Mobille Apps.</p>';

		$this->lib('sene_email_sender');
		$this->sene_email_sender->from($this->site_name,$this->site_email);
		$this->sene_email_sender->to($email,$nama);
		$this->sene_email_sender->subject("[$this->site_name] Orderan sedang diproses");
		$this->sene_email_sender->html($h);
		$this->sene_email_sender->send();
	}
	private function __emailTemplateProsesPacking($email,$nama,$order_id){
		$h  = '<p>Hi '.$nama.',</p>';
		$h .= '<p>Status Pesanan Anda dengan ID Order #'.$order_id.' saat ini sudah dalam  Proses Penyiapan & Packing.</p>';
		$h .= '<br/>';
		$h .= '<p>Untuk memantau status pesanan Anda, silakan akses di Calysta Mobille Apps.</p>';
		$h .= '<br /><br />--<br />';
		$h .= '<p>Happy Shopping</p>';

		$this->lib('sene_email_sender');
		$this->sene_email_sender->from($this->site_name,$this->site_email);
		$this->sene_email_sender->to($email,$nama);
		$this->sene_email_sender->subject("[$this->site_name] Orderan sedang dipacking");
		$this->sene_email_sender->html($h);
		$this->sene_email_sender->send();
	}
	private function __emailTemplateProsesKirim($email,$nama,$order_id,$kurir="JNE",$noresi=""){
		$this->lib('seme_email');
		$replacer = array();
		$replacer['fnama'] = $nama;
		$replacer['order_id'] = $order_id;
		$replacer['order_kurir'] = $kurir;
		$replacer['order_noresi'] = $noresi;
		$this->seme_email->replyto($this->site_name,$this->site_replyto);
		$this->seme_email->from($this->site_email,$this->site_name);
		$this->seme_email->subject('Pesanan telah dikirim TranID #'.$order_id);
		$this->seme_email->to($email,$nama);
		$this->seme_email->template('order_kirim');
		$this->seme_email->replacer($replacer);
		$this->seme_email->send();
	}
	private function __emailTemplateProsesKirim2($email,$nama,$order_id,$kurir="JNE",$noresi=""){
		$h  = '<p>Hi '.$nama.',</p>';
		$h .= '<p>Orderan Pesanan Anda dengan ID Order #'.$order_id.' sudah kami kirimkan dengan Kurir '.$kurir.' dan nomor resi '.$noresi.' ya.</p>';
		$h .= '<br/>';
		$h .= '<p>Untuk memantau status pesanan Anda, silakan akses di Calysta Mobille Apps.</p>';
		$h .= '<br /><br />--<br />';
		$h .= '<p>Happy Shopping</p>';

		$this->lib('sene_email_sender');
		$this->sene_email_sender->from($this->site_name,$this->site_email);
		$this->sene_email_sender->to($email,$nama);
		$this->sene_email_sender->subject("[$this->site_name] Orderan sedang dikirim");
		$this->sene_email_sender->html($h);
		$this->sene_email_sender->send();
	}
	private function __emailTemplateProsesSelesai($email,$nama,$order_id){
		$h  = '<p>Hi '.$nama.',</p>';
		$h .= '<p>Pesanan kamu sudah selesai ya, gunakan rangkaian produk Calysta secara rutin dan tepat, serta melakukan gaya hidup sehat agar kulit wajahmu bisa tetap sehat terawat...</p>';
		$h .= '<br/>';
		$h .= '<p>Kami tunggu pesanan Anda berikutnya ya...</p>';
		$h .= '<br /><br />--<br />';
		$h .= '<p>TERIMAKASIH,<br>CALYSTA MOBILE APS</p>';

		$this->lib('sene_email_sender');
		$this->sene_email_sender->from($this->site_name,$this->site_email);
		$this->sene_email_sender->to($email,$nama);
		$this->sene_email_sender->subject("[$this->site_name] Order selesai");
		$this->sene_email_sender->html($h);
		$this->sene_email_sender->send();
	}

	private function __emailTemplateProsesUbah($email,$nama,$order_id,$order_status="proses"){
		$h  = '<p>Hi '.$nama.',</p>';
		$h .= '<p>Status orderan kamu dengan ID Order #'.$order_id.' sudah kita ubah menjadi '.$order_status.'.</p>';
		$h .= '<p>Pantau terus pesanan kamu melalui aplikasi</p>';
		$h .= '<p>Untuk update selanjutnya, nanti akan kita kabarin lagi...</p>';
		$h .= '<br /><br /><br />--<br />';
		$h .= '<p>Salaam,<br />'.$this->site_name.'</p>';

		$this->lib('sene_email_sender');
		$this->sene_email_sender->from($this->site_name,$this->site_email);
		$this->sene_email_sender->to($email,$nama);
		$this->sene_email_sender->subject("[$this->site_name] Perubahan status Order");
		$this->sene_email_sender->html($h);
		$this->sene_email_sender->send();
	}

	private function __emailTemplateBatal($email,$nama,$order_id,$order_status="proses"){
		$h  = '<p>Hi '.$nama.',</p>';
		$h .= '<p>Status orderan kamu dengan TranID #'.$order_id.' sudah kita batalkan.</p>';
		$h .= '<p>Jika ada pertanyaan mengenai pembatalan pesanan, silakan hubungi kami di via telp +6222 873 097 86 atau whatsapp +6281 322 550 616.</p>';

		$this->lib('sene_email_sender');
		$this->sene_email_sender->from($this->site_name,$this->site_email);
		$this->sene_email_sender->to($email,$nama);
		$this->sene_email_sender->subject("Pesanan Dibatalkan TranID #".$order_id);
		$this->sene_email_sender->html($h);
		$this->sene_email_sender->send();
	}

	public function index(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

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

		switch($iSortCol_0){
			case 0:
				$sortCol = "id";
				break;
			default:
				$sortCol = "id";
		}

		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;

		$keyword = $sSearch;

    $sdate = $this->input->post('tglmin');
    $edate = $this->input->post('tglmax');
    $utype = $this->input->post('utype');
    $b_user_id = (int) $this->input->post('b_user_id');

    $in_utype = array();
    if(!empty($utype)) $in_utype = explode(',',$utype);
    if(!empty($sdate)){
      if(empty($edate)) $edate =  date('Y-m-d');
      $sdate = date('Y-m-d',strtotime($sdate));
      $edate = date('Y-m-d',strtotime($edate));
    }else if(!empty($edate) && empty($sdate)){
      $sdate = date('Y-m-d',strtotime($sdate));
      $edate = date('Y-m-d',strtotime($edate));
    }
    //die($edate);

		$this->status = '100';
		$this->message = 'Berhasil';

		$dcount = $this->dom->countAll($nation_code,$keyword,$sdate,$edate,$in_utype,$b_user_id);
		$ddata = $this->dom->getAll($nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword,$sdate,$edate,$in_utype,$b_user_id);

		foreach($ddata as &$dt){

			if(isset($dt->grand_total)) $dt->grand_total = 'Rp'.number_format($dt->grand_total,0,',','.');
			if(isset($dt->date_order)) $dt->date_order = $this->__dateIndonesia($dt->date_order,'tanggal_jam');
      if(isset($dt->pembayaran)){
				if(isset($cb[$dt->pembayaran])){
					$dt->pembayaran = $cb[$dt->pembayaran]->nama;
				}else{
					$dt->pembayaran = '-';
				}
			}
      if(isset($dt->status)) $dt->status = $this->__orderStatusLabel($dt->status);
			$dt->action = '<button class="btn btn-warning" data-id="'.$dt->id.'">Tracking</button>';
		}
		//sleep(3);
		$this->__jsonDataTable($ddata,$dcount);
	}
	public function tambah(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}
		$di = $_POST;
		if(!isset($di['sku'])) $di['sku'] = "";
		if(!isset($di['nama'])) $di['nama'] = "";
		if(strlen($di['nama'])>1 && strlen($di['sku'])>1){
			$check = $this->cpm->checkSku($di['sku']); //1 = sudah digunakan
			if(empty($check)){
				$res = $this->cpm->set($di);
				if($res){
					$this->status = 100;
					$this->message = 'Data baru berhasil ditambahkan';
				}else{
					$this->status = 900;
					$this->message = 'Tidak dapat menyimpan data baru, silakan coba beberapa saat lagi';
				}
			}else{
				$this->status = 104;
				$this->message = 'Kode sudah digunakan, silakan coba kode lain';
			}
		}
		$this->__json_out($data);
	}
	public function detail($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}
		$this->status = 100;
		$this->message = 'Berhasil';
		$data = $this->cpm->getById($id);
		$this->__json_out($data);
	}
	public function edit(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}
		$du = $_POST;
		if(!isset($du['id'])) $du['id'] = 0;
		$id = (int) $du['id'];
		unset($du['id']);
		if(!isset($du['sku'])) $du['sku'] = "";
		if(!isset($du['nama'])) $di['nama'] = "";
		if($id>1 && strlen($du['nama'])>1 && strlen($du['sku'])>1){
			$check = $this->sku->checkSku($du['sku'],$id); //1 = sudah digunakan
			if(empty($check)){
				$res = $this->sku->update($id,$du);
				if($res){
					$this->status = 100;
					$this->message = 'Perubahan berhasil diterapkan';
				}else{
					$this->status = 901;
					$this->message = 'Tidak dapat melakukan perubahan ke basis data';
				}
			}else{
				$this->status = 104;
				$this->message = 'Kode sudah digunakan, silakan coba kode lain';
			}
		}
		$this->__json_out($data);
	}
	public function hapus($id){
		die();
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}
		$this->status = 100;
		$this->message = 'Berhasil';
		$res = $this->sku->del($id);
		if(!$res){
			$this->status = 902;
			$this->message = 'Data gagal dihapus';
		}
		$this->__json_out($data);
	}
	public function verifikasi($id=""){
		$id = (int) $id;
		$s = $this->__init();
		$data = array();
		if($this->admin_login){
			$id2 = (int) $this->input->post('id');
			$dibayar = (int) $this->input->post('dibayar');
			if($id == $id2 && !empty($id) && !empty($dibayar)){
				$order = $this->dom->getById($id,1);
				if(isset($order->id)){
					$email = $order->pemesan_email;
					$nama = $order->pemesan_nama;

					$du = array();
					$du['utype'] = 'order_cekstok';
					$du['confirm_by'] = $s['sess']->admin->id;
					$du['date_order_proses'] = 'NOW()';
					$du['dibayar'] = $dibayar;

					$res = $this->dom->update($id,$du);
					if($res){
						if($this->is_email) $this->__emailTemplateProses($email,$nama,$id);
						$this->status = 100;
						$this->message = 'Berhasil';
					}else{
						$this->status = 102;
						$this->message = 'Gagal update verifikasi pembayaran untuk orderan ini.';
					}
				}else{
					$this->status = 103;
					$this->message = 'TranID tidak dapat ditemukan';
				}
			}else{
				$this->status = 101;
				$this->message = 'Tidak dapat memproses verifikasi pembayaran';
			}
		}
		$this->__json_out($data);
	}
	public function konfirmasi($tran_id=0){
		$tran_id = (int) $tran_id;
		$s = $this->__init();
		$data = array();
		if($this->admin_login){
			if(empty($tran_id)){
				$tran_id = (int) $this->input->post('tran_id');
			}
			if(!empty($tran_id)){
				$order = $this->dom->getById($tran_id,0);
				if(isset($order->id)){
					$du = array();
					$du['date_order_konfirmasi'] = 'NOW()';
					$du['confirm_tgl'] = $this->input->post('confirm_tgl');
					$du['confirm_ke'] = $this->input->post('confirm_ke');
					$du['confirm_nom'] = $this->input->post('confirm_nom');
					$du['confirm_cara'] = $this->input->post('confirm_cara');
					$du['confirm_dari'] = $this->input->post('confirm_dari');
					$du['confirm_nama'] = $this->input->post('confirm_nama');
					$du['confirm_norek'] = $this->input->post('confirm_norek');
					$du['utype'] = 'order_konfirmasi_sudah';

					$img = $this->__uploadImageKonfirmasi($tran_id);
					if(!empty($img)){
						$du['confirm_bukti'] = $img;
					}
					$res = $this->dom->update($tran_id,$du);
					if($res){
						$this->status = 100;
						$this->message = 'Berhasil';
					}else{
						$this->status = 163;
						$this->message = 'Tidak dapat menyimpan konfirmasi, coba beberapa saat lagi';
					}
				}
			}else{

			}
		}
		$this->__json_out($data);
	}
	public function cekstok_produk($tran_id){
		$tran_id = (int) $tran_id;
		$s = $this->__init();
		$data = array();
		if($this->admin_login && !empty($tran_id)){
			$this->status = 100;
			$this->message = 'Berhasil';
			$produks = $this->dodm->getOrderedProductStok($tran_id);
			$data['produk'] = $produks;
		}
		$this->__json_out($data);
	}

	public function proses($id=""){
		$id = (int) $id;
		$s = $this->__init();
		$data = array();
		if($this->admin_login){
			$id2 = (int) $this->input->post('id');
			$utype = strtolower($this->input->post('utype'));
			$catatan_admin = $this->input->post("catatan_admin");
			if(empty($catatan_admin)) $catatan_admin = '';
			if($id == $id2 && !empty($id) && !empty($utype)){
				$order = $this->dom->getById($id,1);
				if(isset($order->id)){
					$email = $order->pemesan_email;
					$nama = $order->pemesan_nama;
					$kurir = $order->kurir;
					$order_status = $this->__orderStatus($utype);
					if(strlen($catatan_admin)){
						if(strlen($order->catatan_admin)){
							$catatan_admin = '[@'.$s['sess']->admin->username.' '.date("Y-m-d H:i").'] '.$catatan_admin."\r\n".$order->catatan_admin;
						}else{
							$catatan_admin = '[@'.$s['sess']->admin->username.' '.date("Y-m-d H:i").'] '.$catatan_admin;
						}
					}

					$du = array();
					$du['utype'] = $utype;
					$du['catatan_admin'] = $catatan_admin;
					switch($utype){
						case "order_pembelian":
							$du['date_order_pembelian'] = "NOW()";
							$du['purchased_by'] = $s['sess']->admin->id;
							break;
						case "order_qc":
							$du['date_order_qc'] = "NOW()";
							$du['qc_by'] = $s['sess']->admin->id;
							if($this->is_email) $this->__emailTemplateProses($email,$nama,$id);
							break;
						case "order_packing":
							$du['date_order_packing'] = "NOW()";
							$du['packing_by'] = $s['sess']->admin->id;
							//if($this->is_email) $this->__emailTemplateProsesPacking($email,$nama,$id);
							break;
						case "order_kirim":
							$du['date_order_kirim'] = "NOW()";
							break;
						case "order_selesai":
							$du['date_order_selesai'] = "NOW()";
							$this->__updateStatUser($order->id);
							$this->__hitungPoin($order->id,"plus");
							$noresi = $this->input->post("noresi");
							$du['noresi'] = $noresi;
							if($this->is_email){
								$this->__emailTemplateProsesKirim($email,$nama,$id,$kurir,$noresi);
								//sleep(2);
							}
							if($this->is_email){
								//$this->__emailTemplateProsesSelesai($email,$nama,$id);
								//sleep(1);
							}
							break;
						case "order_batal":
							$this->__hitungPoin($order->id,"minus");
							$du['date_order_batal'] = "NOW()";
							$ca = '[@'.$s['sess']->admin->username.' '.date("Y-m-d H:i").'] Membatalkan orderan #'.$order->id;
							if(strlen($du['catatan_admin'])){
								$du['catatan_admin'] = $ca."\r\n".$du['catatan_admin'];
							}else{
								$du['catatan_admin'] = $ca;
							}
							if($this->is_email) $this->__emailTemplateBatal($email,$nama,$id,$order_status);
							break;
						case "order_pending":
							$du['date_order_pending'] = "NOW()";
							$ca = '[@'.$s['sess']->admin->username.' '.date("Y-m-d H:i").'] Pending orderan #'.$order->id;
							if(strlen($du['catatan_admin'])){
								$du['catatan_admin'] = $ca."\r\n".$du['catatan_admin'];
							}else{
								$du['catatan_admin'] = $ca;
							}
							if($this->is_email) $this->__emailTemplateProsesUbah($email,$nama,$order_id,$order_status);
							break;
						default:
							//none
					}

					$res = $this->dom->update($id,$du);
					if($res){
						//if($this->is_email) $this->__emailTemplateProsesUbah($email,$nama,$id,$order_status);
						$this->status = 100;
						$this->message = 'Berhasil';
					}else{
						$this->status = 102;
						$this->message = 'Gagal update status order untuk orderan ini.';
					}
				}else{
					$this->status = 103;
					$this->message = 'TranID tidak dapat ditemukan';
				}
			}else{
				$this->status = 101;
				$this->message = 'Salah satu parameter wajib ada yang tidak terkirim';
			}
		}
		$this->__json_out($data);
	}
	public function rating($id){
		$id = (int) $id;
		$s = $this->__init();
		$data = array();
		if($this->admin_login){
			$id = (int) $id;
			if($id>0){
				$du = array();
				$du['rating_nilai'] = $this->input->post('rating_nilai');
				$du['rating_komentar'] = $this->input->post('rating_teks');
				$this->dom->update($id,$du);
				$this->status = 100;
				$this->message = 'Berhasil';
			}else{
				$this->status = 101;
				$this->message = 'Salah satu parameter wajib ada yang tidak terkirim';
			}
		}
		$this->__json_out($data);
	}
	private function __generateOngkir($provinsi,$kabkota,$kecamatan){
		$data = new stdClass();
		$data->kurir = '-';
		$data->ongkir = 0;
		$jnt = $this->jnt->getOngkir($provinsi,$kabkota,$kecamatan);

		$jne = $this->jne->getOngkir($provinsi,$kabkota,$kecamatan);
		if(count($jne)){
			foreach($jne as $jo){
				if(strtolower(trim($jo->kecamatan)) == strtolower(trim($kecamatan))){
					$data->kurir = 'JNE REG';
					$data->ongkir = $jo->reg15_tarif;
					break;
				}
			}
			if(empty($data->kurir)){
				$data->kurir = 'JNE REG';
				$data->ongkir = $jne[0]->reg15_tarif;
			}
		}

		//$this->debug($data);
		//die();
		return $data;
	}
	public function revalidate($id){
		$id = (int) $id;
		$s = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}
		if($this->admin_login){
			$id = (int) $id;
			if($id>0){
				$order = $this->dom->getById($id);
				if(isset($order->id)){
					$this->status = 100;
					$this->message = 'Berhasil<br>';
					$du = array();

					$order_detail = $this->dodm->getByOrderId($id);
					$item_total = 0;
					$berat_total = 0;
					$subtotal = 0;
					$diskon_total = 0;
					if(count($order_detail)){
						foreach($order_detail as $ord){
							$berat = (float) $ord->berat;
							$qty = (float) $ord->qty;
							$harga_asal = (float) $ord->harga_asal;
							$harga_jadi = (float) $ord->harga_jadi;
							$berat_total += ($qty * $berat);
							$subtotal += ($harga_jadi*$qty);
						}
					}
					if($berat_total<=1) $berat_total = $this->berat_faktor;
					$du['sub_total'] = $subtotal;
					$du['berat_total'] = $berat_total;
					$berat_total = ceil($berat_total/$this->berat_faktor);


					if(empty($order->ongkir)){
						$do = $this->__generateOngkir($order->penerima_provinsi,$order->penerima_kabkota,$order->penerima_kecamatan);
						$du['kurir'] = $do->kurir;
						$du['ongkir'] = $do->ongkir * $berat_total;
						$du['grand_total'] = ($order->sub_total + $du['ongkir'] + $order->biaya_admin) - ($order->diskon_total + $order->faktor_kodeunik);
						$du['kurir_catatan'] = 'Digenerate oleh sistem';
						$this->message .= 'Ongkir di rebuild<br>';
					}
					if(count($du)){
						$this->message .= 'Berhasil diupdate<br>';
						$this->dom->update($id,$du);
					}else{
						$this->message .= 'tapi tidak ada yang diubah<br>';
					}
				}else{
					$this->status = 450;
					$this->message = 'ID Order tidak ditemukan';
				}
			}else{
				$this->status = 449;
				$this->message = 'ID Order tidak valid';
			}
		}
		$this->__json_out($data);
	}
	public function normalize(){
		$this->status = 100;
		$this->message = 'Berhasil';
		$this->dom->normalizeOrder();
		$data = array();
		$this->__json_out($data);
	}

	private function __hitungPoin($id_order,$utype="plus"){
		$poin_asal = 0;
		$order = $this->dom->getById($id_order);
		if(isset($order->id)){
			if($utype="plus"){
				$poin_baru = 0;
				$order_detail = $this->dodm->getByOrderId($id_order);
				foreach($order_detail as $ord){
					if(isset($ord->poin_pelanggan)){
						$poin_baru += $ord->poin_pelanggan;
					}
				}
				if($poin_baru>0){
					$user = $this->bum->getById($order->b_user_id);
					if(isset($user->poin)){
						$poin_asal = (int) $user->poin;
						$poin = $poin_asal + $poin_baru;
						$du=array();
						$du['poin'] = $poin;
						$this->bum->update($user->id,$du);

						$du = array();
						$du['catatan_admin'] = $order->catatan_admin;
						$ca = '[@'.$user->fnama.' '.date("Y-m-d H:i").'] Poin ditambahkan dari ID Order #'.$order->id;
						$du['catatan_admin'] = $ca."\r\n".$du['catatan_admin'];
						$this->dom->update($order->id,$du);
					}
				}
			}else{
				if($order->utype == 'order_selesai'){
					$poin_baru = 0;
					$order_detail = $this->dodm->getByOrderId($id_order);
					foreach($order_detail as $ord){
						if(isset($ord->poin_pelanggan)){
							$poin_baru += $ord->poin_pelanggan;
						}
					}
					if($poin_baru>0){
						$user = $this->bum->getById($order->b_user_id);
						if(isset($user->poin)){
							$poin_asal = (int) $user->poin;
							$poin = $poin_asal - $poin_baru;
							$du=array();
							$du['poin'] = $poin;
							$this->bum->update($user->id,$du);

							$du = array();
							$du['catatan_admin'] = $order->catatan_admin;
							$ca = '[@'.$user->fnama.' '.date("Y-m-d H:i").'] Poin dikurangi dari ID Order #'.$order->id;
							$du['catatan_admin'] = $ca."\r\n".$du['catatan_admin'];
							$this->dom->update($order->id,$du);
						}
					}
				}
			}
		}
	}
	private function __updateStatUser($id_order){
		$order = $this->dom->getById($id_order);
		if(isset($order->id)){
			$order_detail = $this->dodm->getByOrderId($id_order);
			$user = $this->bum->getById($order->b_user_id);
			if(isset($user->id)){
				$du = array();
				$du['beli_terakhir'] = $order->date_order;
				$du['beli_jml'] = $user->beli_jml + 1;
				$du['beli_total'] = $user->beli_total + $order->grand_total;
				$this->bum->update($user->id,$du);

				$du = array();
				$du['catatan_admin'] = $order->catatan_admin;
				$ca = '[@'.$user->fnama.' '.date("Y-m-d H:i").'] Statistik orderan diupdate';
				$du['catatan_admin'] = $ca."\r\n".$du['catatan_admin'];
				$this->dom->update($order->id,$du);
			}
		}
	}
	public function latest(){
		$s = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 600;
			$this->message = 'Unauthorized API access';
			$this->__json_out($data);
			die();
		}
		$this->status = 200;
		$this->message = 'Berhasil';
		$data = $this->dom->getLatestDashboard(0,10,'id','desc');
		$this->__json_out($data);
	}
	public function bestseller(){
		$s = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 600;
			$this->message = 'Unauthorized API access';
			$this->__json_out($data);
			die();
		}
		$this->status = 200;
		$this->message = 'Berhasil';
		$data = $this->dodm->getBestSeller();
		$this->__json_out($data);
	}
}
