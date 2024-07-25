<?php
	Class Notif extends JI_Controller {
		public function __construct(){
			parent::__construct();
			$this->load("api_mobile/e_notif_model",'en');
			//$this->setTheme('frontx');
		}
		public function index(){
			$this->status = 200;
			// $this->message = 'Thankyou for using seme framework';
			$this->message = 'Success';
			$data = array();
			$data['notif'] = $this->en->getAll();

			//manipulator
			foreach($data['notif'] as &$notif){
				if(strlen($notif->thumb)<=4){
					$notif->thumb = 'media/produk/default.png';
				}
				if(strlen($notif->thumb)>=4){
					$notif->thumb = base_url($notif->thumb);
				}
			}
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "notif");
		}

	}
