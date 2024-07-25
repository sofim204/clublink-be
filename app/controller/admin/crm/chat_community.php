<?php
class Chat_Community extends JI_Controller{

	public function __construct(){
		parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'crm';
		$this->current_page = 'crm_chat_community';
		$this->load("admin/b_user_model","bum");
		$this->load("admin/c_produk_model","cpm");
		$this->load("admin/d_order_model","dom");
		$this->load("admin/d_order_detail_model","dodm");
		$this->load("admin/d_order_detail_item_model","dodim");
		$this->load("admin/e_chat_model","chat");
		$this->load("admin/e_chat_attachment_model","ecam");
		$this->load("admin/e_chat_participant_model","ecpm");
		$this->load("admin/e_complain_model","complain");
	}

	public function index(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$this->setTitle('CRM: Admin Chat'.$this->site_suffix_admin);

		$this->putThemeContent("crm/chat_community/home_modal",$data);
		$this->putThemeContent("crm/chat_community/home",$data);

		$this->putJsReady("crm/chat_community/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function detail($d_order_id="",$c_produk_id="", $chat_type=""){
		$d_order_id = (int) $d_order_id;
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die('admin not logged in');
		}
		$nation_code = $data['sess']->admin->nation_code;

		//validating params
		$d_order_id = (int) $d_order_id;
		if($d_order_id<=0){
			redir(base_url_admin('crm/chat'));
			die('invalid d_order_id');
		}
		$c_produk_id = (int) $c_produk_id;
		if($c_produk_id<=0){
			redir(base_url_admin('crm/chat'));
			die('invalid c_produk_id');
		}

		//get order
		$order = $this->dom->getById($nation_code,$d_order_id);
		if(!isset($order->id)){
			redir(base_url_admin('crm/chat'));
			die('d_order row with supplied ID not found');
		}
		$order->detail = $this->dodm->getDetailByOrderId($nation_code,$d_order_id,$c_produk_id);
		if(!isset($order->detail->id)){
			redir(base_url_admin('crm/chat'));
			die('d_order_detail rows with supplied ID not found');
		}

		//get order items
		$order->detail->item = $this->dodim->getDetailByOrderId($nation_code,$d_order_id,$order->detail->c_produk_id);

		//get buyer
		$buyer = $this->bum->getById($nation_code,$order->b_user_id);

		//validate buyer and seller
		if(!isset($buyer->id)){
			redir(base_url_admin('crm/chat'));
			die('buyer does not exist');
		}

		//seller data
		$seller = $this->bum->getById($nation_code,$order->detail->b_user_id_seller);

		//validate buyer and seller
		if(!isset($seller->id)){
			redir(base_url_admin('crm/chat'));
			die('seller does not exists');
		}

		//get attachments
		//$complains = array();
		$complains = $this->complain->getByOrderDetailId($nation_code,$d_order_id,$c_produk_id,$chat_type);
		foreach($complains as &$dc){
			if(isset($dc->thumb)) $dc->thumb = base_url($dc->thumb);
			if(isset($dc->foto)) $dc->foto = base_url($dc->foto);
		}
		//unset($data_complain,$dc);

		//get attachments
		$attachments = array();
		$data_attachments = $this->ecam->getByChatId($nation_code,$d_order_id,$c_produk_id,$chat_type);
		foreach($data_attachments as $da){
			if(strlen($da->url)<=4) continue;
			if(!isset($attachments[$da->e_chat_id])) $attachments[$da->e_chat_id] = array();
			$attachments[$da->e_chat_id][] = $da;
		}
		unset($data_attachments);
		unset($da);

		//get chat
		$chats = $this->chat->getDetailByID($nation_code,$d_order_id,$c_produk_id,$chat_type);
		foreach($chats as &$chat){
			$chat->attachments = array();
			$chat->complain = new stdClass();
			if(isset($attachments[$chat->id])){
				$chat->attachments = $attachments[$chat->id];
			}
		}

		//by Donny Dennison - 15 september 2020 16:59
        //add flag unread chat
        $this->chat->updateByOrderIDProdukIDChatType($nation_code, $d_order_id, $c_produk_id, $chat_type, array('is_read_admin' =>1));

		//$this->debug($complains);
		//die();
		$data['order'] = $order;
		$data['chats'] = $chats;
		$data['seller'] = $seller;
		$data['buyer'] = $buyer;
		$data['chat_type'] = $chat_type;
		//$data['complains'] = $complains;
		$data['complains'] = array();

		//by Donny Dennison - 1 july 2020 17:12
        //change to make the same status shipment
		// $data['order_status'] = $this->__getOrderStatus($order->order_status,$order->payment_status,$order->detail->seller_status,$order->detail->shipment_status,$order->detail->buyer_confirmed);
		$data['order_status'] = $this->__getOrderStatus($order->order_status,$order->payment_status,$order->detail->seller_status,$order->detail->shipment_status,$order->detail->buyer_confirmed,$order->detail->delivery_date, $order->detail->received_date);
		
		$this->setTitle('Chat Detail #'.$d_order_id.'/'.$c_produk_id.' '.$this->site_suffix_admin);
		$this->putThemeContent("crm/chat_community/detail_modal",$data);
		$this->putThemeContent("crm/chat_community/detail",$data);
		$this->putJsContent("crm/chat_community/detail_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function default($d_order_id="",$c_produk_id="", $chat_type=""){
		$d_order_id = (int) $d_order_id;
		$chat_type="A";
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		$nation_code = $data['sess']->admin->nation_code;

		//validating params
		$d_order_id = (int) $d_order_id;
		if($d_order_id<=0){
			redir(base_url_admin('crm/chat'));
			die();
		}
		$c_produk_id = (int) $c_produk_id;
		if($c_produk_id<=0){
			redir(base_url_admin('crm/chat'));
			die();
		}

		//get order
		$order = $this->dom->getById($nation_code,$d_order_id);
		if(!isset($order->id)){
			redir(base_url_admin('crm/chat'));
			die();
		}
		$order->detail = $this->dodm->getDetailByOrderId($nation_code,$d_order_id,$c_produk_id);
		if(!isset($order->detail->id)){
			redir(base_url_admin('crm/chat'));
			die();
		}

		//get order items
		$order->detail->item = $this->dodim->getDetailByOrderId($nation_code,$d_order_id,$c_produk_id);


		//get buyer seller data
		$buyer = $this->bum->getById($nation_code,$order->b_user_id);
		$seller = $this->bum->getById($nation_code,$order->detail->b_user_id_seller);

		//validate buyer and seller
		if(!isset($buyer->id)){
			redir(base_url_admin('crm/chat'));
			die();
		}

		//validate buyer and seller
		if(!isset($seller->id)){
			redir(base_url_admin('crm/chat'));
			die();
		}

		//get attachments
		//$complains = array();
		$complains = $this->complain->getByOrderDetailId($nation_code,$d_order_id,$c_produk_id,$chat_type);
		foreach($complains as &$dc){
			if(isset($dc->thumb)) $dc->thumb = base_url($dc->thumb);
			if(isset($dc->foto)) $dc->foto = base_url($dc->foto);
		}
		//unset($data_complain,$dc);

		//get attachments
		$attachments = array();
		$data_attachments = $this->ecam->getByChatId($nation_code,$d_order_id,$c_produk_id,$chat_type);
		foreach($data_attachments as $da){
			if(strlen($da->url)<=4) continue;
			if(!isset($attachments[$da->e_chat_id])) $attachments[$da->e_chat_id] = array();
			$attachments[$da->e_chat_id][] = $da;
		}
		unset($data_attachments);
		unset($da);

		//get chat
		$chats = $this->chat->getDetailByID($nation_code,$d_order_id,$c_produk_id,$chat_type);
		foreach($chats as &$chat){
			$chat->attachments = array();
			$chat->complain = new stdClass();
			if(isset($attachments[$chat->id])){
				$chat->attachments = $attachments[$chat->id];
			}
		}

		//by Donny Dennison - 15 september 2020 16:59
        //add flag unread chat
        $this->chat->updateByOrderIDProdukIDChatType($nation_code, $d_order_id, $c_produk_id, $chat_type, array('is_read_admin' =>1));

		//$this->debug($complains);
		//die();
		$data['order'] = $order;
		$data['chats'] = $chats;
		$data['seller'] = $seller;
		$data['buyer'] = $buyer;
		$data['chat_type'] = $chat_type;
		//$data['complains'] = $complains;
		$data['complains'] = array();

		//by Donny Dennison - 1 july 2020 17:12
        //change to make the same status shipment
		// $data['order_status'] = $this->__getOrderStatus($order->order_status,$order->payment_status,$order->detail->seller_status,$order->detail->shipment_status,$order->detail->buyer_confirmed);
		$data['order_status'] = $this->__getOrderStatus($order->order_status,$order->payment_status,$order->detail->seller_status,$order->detail->shipment_status,$order->detail->buyer_confirmed,$order->detail->delivery_date, $order->detail->received_date);
		
		$this->setTitle('Chat Detail #'.$d_order_id.'/'.$c_produk_id.' '.$this->site_suffix_admin);
		$this->putThemeContent("crm/chat_community/detail_modal",$data);
		$this->putThemeContent("crm/chat_community/detail",$data);
		$this->putJsContent("crm/chat_community/detail_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function chat_seller($d_order_id="",$c_produk_id="",$seller_id="",$chat_type=""){
		$d_order_id = (int) $d_order_id;
		$seller_id = (int) $seller_id;
		$chat_type = $chat_type;
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		$nation_code = $data['sess']->admin->nation_code;

		//validating params
		$d_order_id = (int) $d_order_id;
		if($d_order_id<=0){
			redir(base_url_admin('crm/chat'));
			die();
		}
		$c_produk_id = (int) $c_produk_id;
		if($c_produk_id<=0){
			redir(base_url_admin('crm/chat'));
			die();
		}

		//get order
		$order = $this->dom->getById($nation_code,$d_order_id);
		if(!isset($order->id)){
			redir(base_url_admin('crm/chat'));
			die();
		}
		$order->detail = $this->dodm->getDetailByOrderId($nation_code,$d_order_id,$c_produk_id);
		if(!isset($order->detail->id)){
			redir(base_url_admin('crm/chat'));
			die();
		}

		//get order items
		$order->detail->item = $this->dodim->getDetailByOrderId($nation_code,$d_order_id,$c_produk_id);

		//get buyer seller data
		$buyer = $this->bum->getById($nation_code,$order->b_user_id);
		$seller = $this->bum->getById($nation_code,$order->detail->b_user_id_seller);

		//validate buyer and seller
		/*if(!isset($buyer->id)){
			redir(base_url_admin('crm/chat'));
			die();
		}*/

		//validate buyer and seller
		if(!isset($seller->id)){
			redir(base_url_admin('crm/chat'));
			die();
		}

		//get attachments
		//$complains = array();
		$complains = $this->complain->getByOrderDetailId($nation_code,$d_order_id,$c_produk_id);
		foreach($complains as &$dc){
			if(isset($dc->thumb)) $dc->thumb = base_url($dc->thumb);
			if(isset($dc->foto)) $dc->foto = base_url($dc->foto);
		}
		//unset($data_complain,$dc);

		//get attachments
		$attachments = array();

		//By Donny Dennison - 12 august 2020 - 11:32
		//bug fixing attachment not shown in chat from mas adit change
		// $data_attachments = $this->ecam->getByChatSeller($nation_code,$d_order_id,$c_produk_id,$seller_id,$chat_type);
		$data_attachments = $this->ecam->getByChatId($nation_code,$d_order_id,$c_produk_id,$chat_type);

		foreach($data_attachments as $da){
			if(strlen($da->url)<=4) continue;
			if(!isset($attachments[$da->e_chat_id])) $attachments[$da->e_chat_id] = array();
			$attachments[$da->e_chat_id][] = $da;

		}
		unset($data_attachments);
		unset($da);

		//get chat
		$chats = $this->chat->getDetailSeller($nation_code,$d_order_id,$c_produk_id,$seller_id,$chat_type);
		foreach($chats as &$chat){
			$chat->attachments = array();
			$chat->complain = new stdClass();
			if(isset($attachments[$chat->id])){
				$chat->attachments = $attachments[$chat->id];
			}
		}

		//by Donny Dennison - 15 september 2020 16:59
        //add flag unread chat
        $this->chat->updateByOrderIDProdukIDChatType($nation_code, $d_order_id, $c_produk_id, $chat_type, array('is_read_admin' =>1));

		$data['order'] = $order;

		$data['chats'] = $chats;
		$data['seller'] = $seller;
		$data['buyer'] = $buyer;
		$data['chat_type'] = $chat_type;
		//$data['complains'] = $complains;
		$data['complains'] = array();

		//by Donny Dennison - 1 july 2020 17:12
        //change to make the same status shipment
		// $data['order_status'] = $this->__getOrderStatus($order->order_status,$order->payment_status,$order->detail->seller_status,$order->detail->shipment_status,$order->detail->buyer_confirmed);
		$data['order_status'] = $this->__getOrderStatus($order->order_status,$order->payment_status,$order->detail->seller_status,$order->detail->shipment_status,$order->detail->buyer_confirmed,$order->detail->delivery_date, $order->detail->received_date);
		
		$this->setTitle('Chat Detail #'.$d_order_id.'/'.$c_produk_id.' '.$this->site_suffix_admin);
		$this->putThemeContent("crm/chat_community/detail_modal",$data);
		$this->putThemeContent("crm/chat_community/detail",$data);
		$this->putJsContent("crm/chat_community/detail_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function chat_buyer($d_order_id="",$c_produk_id="",$buyer_id="",$chat_type=""){
		$buyer_id = (int) $buyer_id;
		$d_order_id = (int) $d_order_id;
		$chat_type = "B";
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		$nation_code = $data['sess']->admin->nation_code;

		//validating params
		$d_order_id = (int) $d_order_id;
		if($d_order_id<=0){
			redir(base_url_admin('crm/chat'));
			die();
		}
		$c_produk_id = (int) $c_produk_id;
		if($c_produk_id<=0){
			redir(base_url_admin('crm/chat'));
			die();
		}

		//get order
		$order = $this->dom->getById($nation_code,$d_order_id);
		if(!isset($order->id)){
			redir(base_url_admin('crm/chat'));
			die();
		}
		$order->detail = $this->dodm->getDetailByOrderId($nation_code,$d_order_id,$c_produk_id);
		if(!isset($order->detail->id)){
			redir(base_url_admin('crm/chat'));
			die();
		}

		//get order items
		$order->detail->item = $this->dodim->getDetailByOrderId($nation_code,$d_order_id,$c_produk_id);

		//get buyer seller data
		$buyer = $this->bum->getById($nation_code,$order->b_user_id);
		$seller = $this->bum->getById($nation_code,$order->detail->b_user_id_seller);

		//validate buyer and seller
		if(!isset($buyer->id)){
			redir(base_url_admin('crm/chat'));
			die();
		}

		//validate buyer and seller
		/*if(!isset($seller->id)){
			redir(base_url_admin('crm/chat'));
			die();
		}*/

		//get attachments
		//$complains = array();
		$complains = $this->complain->getByOrderDetailId($nation_code,$d_order_id,$c_produk_id);
		foreach($complains as &$dc){
			if(isset($dc->thumb)) $dc->thumb = base_url($dc->thumb);
			if(isset($dc->foto)) $dc->foto = base_url($dc->foto);
		}
		//unset($data_complain,$dc);

		//get attachments
		$attachments = array();
		
		//By Donny Dennison - 12 august 2020 - 11:32
		//bug fixing attachment not shown in chat from mas adit change
		// $data_attachments = $this->ecam->getByChatBuyer($nation_code,$d_order_id,$c_produk_id,$buyer_id,$chat_type);
		$data_attachments = $this->ecam->getByChatId($nation_code,$d_order_id,$c_produk_id,$chat_type);

		foreach($data_attachments as $da){
			if(strlen($da->url)<=4) continue;
			if(!isset($attachments[$da->e_chat_id])) $attachments[$da->e_chat_id] = array();
			$attachments[$da->e_chat_id][] = $da;
		}
		unset($data_attachments);
		unset($da);

		//get chat
		$chats = $this->chat->getDetailBuyer($nation_code,$d_order_id,$c_produk_id,$buyer_id,$chat_type);
		foreach($chats as &$chat){
			$chat->attachments = array();
			$chat->complain = new stdClass();
			if(isset($attachments[$chat->id])){
				$chat->attachments = $attachments[$chat->id];
			}
		}
	
		//by Donny Dennison - 15 september 2020 16:59
        //add flag unread chat
        $this->chat->updateByOrderIDProdukIDChatType($nation_code, $d_order_id, $c_produk_id, $chat_type, array('is_read_admin' =>1));

		//$this->debug($complains);
		//die();
		$data['order'] = $order;
		$data['chats'] = $chats;
		$data['seller'] = $seller;
		$data['buyer'] = $buyer;
		$data['chat_type'] = $chat_type;
		//$data['complains'] = $complains;
		$data['complains'] = array();

		//by Donny Dennison - 1 july 2020 17:12
        //change to make the same status shipment
		// $data['order_status'] = $this->__getOrderStatus($order->order_status,$order->payment_status,$order->detail->seller_status,$order->detail->shipment_status,$order->detail->buyer_confirmed);
		$data['order_status'] = $this->__getOrderStatus($order->order_status,$order->payment_status,$order->detail->seller_status,$order->detail->shipment_status,$order->detail->buyer_confirmed,$order->detail->delivery_date, $order->detail->received_date);
		
		$this->setTitle('Chat Detail #'.$d_order_id.'/'.$c_produk_id.' '.$this->site_suffix_admin);
		$this->putThemeContent("crm/chat_community/detail_modal",$data);
		$this->putThemeContent("crm/chat_community/detail",$data);
		$this->putJsContent("crm/chat_community/detail_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
