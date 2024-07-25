<?php
class Booking extends JI_Controller{
	var $booking_prefix = 'DO';
	public function __construct(){
    parent::__construct();
		//$this->setTheme('frontx');
		$this->load("api_mobile/a_company_model","acm");
		$this->load("api_mobile/b_user_model","bu");
		$this->load("api_mobile/c_booking_model","cbm");
		$this->load("api_mobile/c_produk_model","cpm");
		$this->lib("conumtext");
	}
	private function __genKode2($a_company_id,$b_user_id,$c_produk_id,$tgl){
		$company = $this->acm->getById($a_company_id);
		$user = $this->bu->getById($b_user_id);
		$produk = $this->cpm->getById($c_produk_id);
		$tgl = date("Ymd",strtotime($tgl));
		if(!isset($company->kode)){
			$company = new stdClass();
			$company->kode = '00';
		}
		if(!isset($user->kode)){
			$user = new stdClass();
			$user->kode = '00';
		}
		if(!isset($produk->id)){
			$produk = new stdClass();
			$produk->id = '00';
		}
		return 'BO/'.$company->kode.'/'.$tgl.'/'.$user->kode.'/'.$produk->id;
	}
	private function __genKode($bdate,$pelanggan_id,$cabang_id=""){
		$tgl = $this->conumtext->encode(date("ymd",strtotime($bdate)));
		$jam = '';
		//$jam = $this->conumtext->encode(date('His').$pelanggan_id);
		$tgljam = $tgl.$jam.$cabang_id;
		return $this->booking_prefix.$tgljam;
	}
	public function index(){
		$dt = $this->__init();
		$data = array();

		$apikey = $this->input->get('apikey');
		$apisess = $this->input->get('apisess');

		$this->status = 600;
		$this->message = 'Missing apikey or wrong apisess';
		$c = $this->apikey_check($apikey);

		$pelanggan = $this->bu->getByToken($apisess,'api_mobile');
		$data['bookings'] = array();
		if($c && isset($pelanggan->id)){
			$this->status = 200;
			$this->message = 'Success';
			$bookings = $this->cbm->getByUserId($pelanggan->id);
			foreach($bookings as &$book){
				if(isset($book->bdate)){
					$book->bdate = $this->__dateIndonesia($book->bdate,'hari_tanggal_jam');
				}
				if(isset($book->jenis)){
					if($book->jenis == '-'){
						$book->jenis = 'booking';
					}else{
						$book->jenis = 'via order';
					}
				}
			}
			$data['bookings'] = $bookings;
		}
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "booking");
	}
	public function cabang(){
		$dt = $this->__init();
		$data = array();

		$apikey = $this->input->get('apikey');
		$apisess = $this->input->get('apisess');

		$this->status = 600;
		$this->message = 'Missing apikey or wrong apisess';
		$c = $this->apikey_check($apikey);

		$pelanggan = $this->bu->getByToken($apisess,'api_mobile');
		$data['bookings'] = array();

		if($c && isset($pelanggan->id)){
			$cabang_id = (int) $this->input->request('cabang_id');
			if($cabang_id>0){
				$this->status = 200;
				$this->message = 'Success';
				$bookings = $this->cbm->getByCompanyId($pelanggan->id,$cabang_id);
				foreach($bookings as &$booking){
					if($booking->user_id != $pelanggan->id){
						$booking->kode = '';
						$booking->treatment = '';
					}
					if(isset($booking->bdate)){
						$booking->bdate = $this->__dateIndonesia($booking->bdate);
					}
				}
				$data['bookings'] = $bookings;
			}else{
				$this->status = 192;
				$this->message = 'Invalid cabang_id atau invalid id treatment';
			}
		}
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "booking");
	}
	public function baru(){
		$dt = $this->__init();
		$data = array();

		$apikey = $this->input->get('apikey');
		$apisess = $this->input->get('apisess');

		$this->status = 600;
		$this->message = 'Missing apikey or wrong apisess';
		$c = $this->apikey_check($apikey);

		$pelanggan = $this->bu->getByToken($apisess,'api_mobile');
		$data['bookings'] = array();
		$data['booking'] = new stdClass();
		$data['kode_voucher'] = '';

		if($c && isset($pelanggan->id)){
			$cabang_id = (int) $this->input->post('cabang_id');
			$c_produk_id = (int) $this->input->post('c_produk_id');
			if($cabang_id>0 && $c_produk_id>0){
				$bdate = strtotime($this->input->post('bdate'));
				$cdate = strtotime("now");
				if($bdate>=$cdate){
					$di = array();
					$di['a_company_id'] = $cabang_id;
					$di['b_user_id'] = $pelanggan->id;
					$di['bdate'] = date("Y-m-d H:i",$bdate).':00';
					$di['cdate'] = 'NOW()';
					$di['c_produk_id'] = $c_produk_id;

					$bdatex = date("Y-m-d",$bdate);
					$di['kode'] = $this->__genKode($bdatex,$pelanggan->id,$cabang_id);
					if(empty($this->cbm->checkKode($di['kode'],$pelanggan->id))){
						$res = $this->cbm->set($di);
						if($res){
							$this->status = 200;
							$this->message = 'Success';
							$data['kode_voucher'] = $di['kode'];
						}else{
							$this->status = 190;
							$this->message = 'Error, cant save booking data';
						}
						$bookings = $this->cbm->getByUserId($pelanggan->id);
						foreach($bookings as &$book){
							if($book->user_id != $pelanggan->id){
								$booking->kode = '';
							}
							if($book->id == $res){
								$data['booking'] = $book;
							}
							if(isset($book->jenis)){
								if($book->jenis == '-'){
									$book->jenis = 'booking';
								}else{
									$book->jenis = 'via order';
								}
							}
						}
						$data['bookings'] = $bookings;
					}else{
						$this->status = 197;
						$this->message = 'Maaf, maksimal hanya 1 booking untuk cabang dan hari yang sama';
					}
				}else{
					$this->status = 191;
					$this->message = 'Invalid bdate, please choose future datetime';
				}
			}else{
				$this->status = 192;
				$this->message = 'Invalid cabang_id atau invalid id treatment';
			}
		}
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "booking");
	}
	public function batal(){
		$dt = $this->__init();
		$data = array();

		$apikey = $this->input->get('apikey');
		$apisess = $this->input->get('apisess');

		$this->status = 600;
		$this->message = 'Missing apikey or wrong apisess';
		$c = $this->apikey_check($apikey);

		$pelanggan = $this->bu->getByToken($apisess,'api_mobile');
		$data['bookings'] = array();

		if($c && isset($pelanggan->id)){
			$cabang_id = (int) $this->input->get('cabang_id');
			if($cabang_id>0){
				$booking_id = (int) $this->input->post('booking_id');
				if($booking_id>0){
					$di = array();
					$di['a_company_id'] = $cabang_id;
					$di['b_user_id'] = $pelanggan->id;
					$di['bdate'] = $bdate;
					$di['cdate'] = 'NOW()';
					$res = $this->cbm->cancel($booking_id,$pelanggan->id);

					if($res){
						$bdate = date('Y-m-d H:i:s',$bdate);
						$this->status = 200;
						$this->message = 'Success';
					}else{
						$this->status = 189;
						$this->message = 'Error, cant cancel booking data';
					}

					$bookings = $this->cbm->getByCompanyId($cabang_id);
					foreach($bookings as &$booking){
						if($booking->user_id != $pelanggan->id){
							$booking->kode = '';
						}
					}

					$data['bookings'] = $bookings;
				}else{
					$this->status = 191;
					$this->message = 'Invalid bdate, please choose future datetime';
				}
			}else{
				$this->status = 192;
				$this->message = 'Invalid cabang_id atau invalid id treatment';
			}
		}
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "booking");
	}
}
