<?php
class konfirmasi extends JI_Controller {
	var $is_email = 1;

	public function __construct(){
    parent::__construct();
		$this->load("api_mobile/b_user_model",'bu');
		// $this->load('api_mobile/c_produk_model','produk');
		$this->load("api_mobile/d_order_model","order");
		$this->load("api_mobile/d_order_detail_model","order_detail");
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

	//prosedur kirim email order
	private function __sendEmailKonfirmasi($session,$cartObject){
		if($this->is_email){
			$nama = $session->user->fnama;
			$email = $session->user->email;
			$order_id = $cartObject->id;
			$order_pembayaran = $cartObject->pembayaran;
			$order_grand_total = 'Rp'.number_format($cartObject->grand_total,'2',',','.');
			$bank = $this->__bankHtml($order_pembayaran);
			$harike = (int) date("N",strtotime('now'));
			$to = $email."";
			$subject = "[Calysta] Tagihan untuk order ".$order_id;
			$hari_plus = 2;
			if($harike == 5){
				$hari_plus = 3;
			}else if($harike == 6){
				$hari_plus = 3;
			}
			$sebelum = $this->__dateIndonesia('+ '.$hari_plus.' days');

			// Always set content-type when sending HTML email
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

			// More headers
			$headers .= 'From: <noreply@calysta.thecloudalert.com>' . "\r\n";
			$headers .= 'Cc: calysta@thecloudalert.com' . "\r\n";

			//mail($to,$subject,$message,$headers);
			$replacer = array();
			$replacer['order_id'] = $order_id;
			$replacer['fnama'] = $session->user->fnama;
			$replacer['date_expire'] = $sebelum;
			$replacer['bank_nama'] = $bank->nama;
			$replacer['bank_norek'] = $bank->norek;
			$replacer['bank_an'] = $bank->an;
			$replacer['order_total'] = $order_grand_total;

			$this->lib('seme_email');
			$this->seme_email->from('calysta@thecloudalert.com','Calysta');
			$this->seme_email->subject('[Calysta] Konfirmasi ID ORDER #'.$order_id.' sudah terkirim');
			$this->seme_email->to($session->user->email,$session->user->fnama);
			$this->seme_email->template('order_after');
			$this->seme_email->replacer($replacer);
			$this->seme_email->send();
		}
	}
	public function index(){
		$dt = $this->__init();
		$data = array();

		$this->status = 600;
		$this->message = 'Missing apikey or wrong apisess';

		$apikey = $this->input->get('apikey');
		$apisess = $this->input->get('apisess');
		$order_id = (int) $this->input->get('order_id');

		$c = $this->apikey_check($apikey);
		$pelanggan = $this->bu->getByToken($apisess,'api_mobile');
		$data['kode_voucher'] = '-';
		if($c && isset($pelanggan->id)){
			if($order_id>0){
				$tran = $this->order->getByIdAndUserId($order_id,$pelanggan->id);
				if(isset($tran->utype)){
					//if($tran->utype == 'order_konfirmasi'){
						$user = $pelanggan;
						$b_user_id = $user->id;
						$du = array();
						$du['date_order_konfirmasi'] = 'NOW()';
						$du['confirm_tgl'] = $this->input->post('confirm_tgl');
						$du['confirm_dari'] = $this->input->post('confirm_dari');
						$du['confirm_norek'] = $this->input->post('confirm_norek');
						$du['confirm_nama'] = $this->input->post('confirm_nama');
						$du['confirm_cara'] = $this->input->post('confirm_cara');
						$du['confirm_ke'] = $this->input->post('confirm_ke');
						$du['confirm_nom'] = $this->input->post('confirm_nom');
						$du['utype'] = 'order_konfirmasi_sudah';

						$img = $this->__uploadImageKonfirmasi($order_id);
						if(!empty($img)){
							$du['confirm_bukti'] = $img;
						}
						$res = $this->order->update($order_id,$du);
						if($res){
							$this->status = 200;
							$this->message = 'Success';
							$data['kode_voucher'] = $tran->kode;
						}else{
							$this->status = 163;
							$this->message = 'Tidak dapat menyimpan konfirmasi, coba beberapa saat lagi';
						}
					//}else{
					//	$this->status = 164;
					//	$this->message = 'Pembayaran sudah dikonfirmasi';
					//}
				}else{
					$this->status = 177;
					// $this->message = 'Order konfirmasi salah';
					$this->message = 'Wrong order confirmation';
				}
			}else{
				$this->status = 177;
				// $this->message = 'ID Order tidak valid';
				$this->message = 'Wrong order confirmation';
			}
		}
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "konfirmasi");
	}
}
