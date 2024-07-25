<?php
class Group_Notifications extends JI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->lib("seme_log");
		$this->load("api_mobile/b_user_model", "bu");
		$this->load("api_mobile/group/i_group_notifications_model", "ignotifm");
	}

	private function __page($page)
	{
		if (!is_int($page)) {
			$page = (int) $page;
		}
		if ($page<=0) {
			$page = 1;
		}
		return $page;
	}

	private function __pageSize($page_size)
	{
		$page_size = (int) $page_size;
		if ($page_size<=0) {
			$page_size = 10;
		}
		return $page_size;
	}

	public function index() {
		//initial
		$dt = $this->__init();

		//default result
		$data = array();
		$data['notifications'] = new stdClass();

		//check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
		if(empty($nation_code)){
			$this->status = 101;
			$this->message = 'Missing or invalid nation_code';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
			die();
		}

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
			die();
		}

		//check apisess
		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if(!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
			die();
		}

		$page = $this->input->get("page");
		$page_size = $this->input->get("page_size");
		$page = $this->__page($page);
		$page_size = $this->__pageSize($page_size);

		//manipulator
		$notifications = $this->ignotifm->getAll($nation_code, $pelanggan->id, $page, $page_size, "cdate", "desc", "all");
		foreach($notifications as &$notif){
			$notif->judul = html_entity_decode($notif->judul, ENT_QUOTES);
			$notif->teks = html_entity_decode($notif->teks, ENT_QUOTES);
			$date = date_create($notif->cdate);
			$new_date = date_format($date, "M j");
			$notif->cdate = $new_date;

			if(strlen($notif->extras)<=2) $notif->extras = '{}';
			$obj = json_decode($notif->extras);
			if(is_object($obj)) $notif->extras = $obj;
			if(strlen($notif->gambar)>4){
				$notif->gambar = $this->cdn_url($notif->gambar);
			}
		}
		//success
		$this->status = 200;
		$this->message = 'Success';
		$data['notifications'] = $notifications;
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
	}

	public function read_notif() {
		//initial
		$dt = $this->__init();

		//default result
		$data = array();
		$data['notifications'] = new stdClass();

		//check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
		if(empty($nation_code)){
			$this->status = 101;
			$this->message = 'Missing or invalid nation_code';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
			die();
		}

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
			die();
		}

		//check apisess
		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if(!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
			die();
		}

		$band_notif_id = $this->input->get("band_notif_id");

		$du = array();
		$du['is_read'] = 1;

		if($band_notif_id > '0'){
			$this->ignotifm->update($nation_code, $pelanggan->id, $band_notif_id, $du);

			$notifications = $this->ignotifm->getAll($nation_code, $pelanggan->id, 1, 1, "cdate", "desc", "first");

			$notifications->judul = html_entity_decode($notifications->judul, ENT_QUOTES);
			$notifications->teks = html_entity_decode($notifications->teks, ENT_QUOTES);

			$date = date_create($notifications->cdate);
			$new_date = date_format($date, "M j");
			$notifications->cdate = $new_date;
			
			if(strlen($notifications->extras)<=2) $notifications->extras = '{}';
			$obj = json_decode($notifications->extras);
			if(is_object($obj)) $notifications->extras = $obj;
			if(strlen($notifications->gambar)>4){
				$notifications->gambar = $this->cdn_url($notifications->gambar);
			}
		} else {
			$this->ignotifm->update($nation_code, $pelanggan->id, "", $du);
			$notifications = [];
		}

		//success
		$this->status = 200;
		$this->message = 'Success';
		$data = $notifications;
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
	}
}