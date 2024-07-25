<?php
class Chat_Ecommerce extends JI_Controller{

	public function __construct(){
		parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'crm';
		$this->current_page = 'crm_chat_v2';
		$this->load("admin/b_user_model","bum");
		$this->load("admin/c_produk_model","cpm");
		$this->load("admin/d_order_model","dom");
		$this->load("admin/d_order_detail_model","dodm");
		$this->load("admin/d_order_detail_item_model","dodim");
		$this->load("admin/e_chat_room_model","ecpm");
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

		$this->putThemeContent("crm/chat_v2/home_modal",$data);
		$this->putThemeContent("crm/chat_v2/home",$data);

		$this->putJsReady("crm/chat_v2/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	// public function detail($chat_room_id="0", $chat_type="ALL", $user_id_1="0", $user_id_2="0", $type="1", $chat_room_id_customer = "0"){
	// 	$chat_room_id = (int) $chat_room_id;
	// 	$data = $this->__init();
	// 	if(!$this->admin_login){
	// 		redir(base_url_admin('login'));
	// 		die('admin not logged in');
	// 	}
	// 	$nation_code = $data['sess']->admin->nation_code;

	// 	if($chat_room_id == 0 && $chat_type == 'ALL'){
            
    //         //insert room chat
    //         $di = array();
    //         $di['nation_code'] = $nation_code;
    //         $di['b_user_id_1'] = $user_id_1;
    //         $di['b_user_id_2'] = $user_id_2;
    //         $di['b_user_id_1_is_active'] = 0;
    //         $di['b_user_id_2_is_active'] = 0;
    //         $di['chat_type'] = $chat_type;

    //         $this->ecpm->set($di);

    //         $chat_room_id = $this->ecpm->lastId();

	// 	}else if($chat_room_id_customer == 0 && $chat_type == 'ADMIN'){
            
    //         //insert room chat
    //         $di = array();
    //         $di['nation_code'] = $nation_code;
    //         $di['b_user_id_1'] = 0;

    //         if($type == 1){
    //         	$di['b_user_id_2'] = $user_id_1;
    //         }else{
    //         	$di['b_user_id_2'] = $user_id_2;
    //         }

    //         $di['b_user_id_2_is_active'] = 0;
    //         $di['chat_type'] = $chat_type;

    //         $this->ecpm->set($di);

	// 	}

	// 	//get chat room detail
	// 	$chatRoomDetail = $this->ecpm->getByRoomId($nation_code, $chat_room_id, "ALL");

	// 	//get customer 1 data
	// 	$customer1Data = $this->bum->getById($nation_code,$user_id_1);

	// 	//get customer 2 data
	// 	$customer2Data = $this->bum->getById($nation_code,$user_id_2);

	// 	//get chat room customer 1 detail
	// 	$chatRoomCustomer1Detail = $this->ecpm->checkRoomChat($nation_code, 0, $user_id_1, $chat_type);

	// 	if(!isset($chatRoomCustomer1Detail->id)){
	// 		$chatRoomCustomer1Detail->id = 0;
	// 	}

	// 	//get chat room customer 2 detail
	// 	$chatRoomCustomer2Detail = $this->ecpm->checkRoomChat($nation_code, 0, $user_id_2, $chat_type);

	// 	if(!isset($chatRoomCustomer2Detail->id)){
	// 		$chatRoomCustomer2Detail->id = 0;
	// 	}

	// 	if($type == 1 && $chat_type == 'ADMIN'){
	// 		$chat_room_temp_id = $chatRoomCustomer1Detail->id;
	// 	}else if($type == 2 && $chat_type == 'ADMIN'){
	// 		$chat_room_temp_id = $chatRoomCustomer2Detail->id;
	// 	}else{
	// 		$chat_room_temp_id = $chat_room_id;
	// 	}

	// 	//get complain
	// 	$complains = array();
	// 	$data_complains = $this->complain->getDetailByChatRoomID($nation_code,$chat_room_temp_id);
	// 	foreach($data_complains as $dc){

	// 		if(!isset($complains[$dc->e_chat_id])) $complains[$dc->e_chat_id] = array();
	// 		$complains[$dc->e_chat_id][] = $dc;

	// 	}
	// 	unset($data_complains);
	// 	unset($dc);

	// 	//get attachments
	// 	$attachments = array();
	// 	$data_attachments = $this->ecam->getByChatRoomChatId($nation_code,$chat_room_temp_id);
	// 	foreach($data_attachments as $da){
			
    //         $da->order_invoice_code = '';
    //         $da->order_name = '';
    //         $da->order_thumb = '';
    //         $da->order_status = '';

	// 		if($da->jenis == 'order'){

	// 			//get order
	// 			$order = $this->dom->getById($nation_code,$da->url);
	// 			$order->detail = $this->dodm->getDetailByOrderId($nation_code,$da->url,$da->order_detail_id);
	// 			$order->item = $this->dodim->getById($nation_code,$da->url,$da->order_detail_id,$da->order_detail_item_id);

	// 			$da->order_invoice_code = $order->invoice_code;
    //         	$da->order_name = $order->item->nama;
    //         	$da->order_thumb = $order->item->thumb;
	// 			$da->order_status = $this->__getOrderStatus($order->order_status,$order->payment_status,$order->detail->seller_status,$order->detail->shipment_status,$order->detail->buyer_confirmed,$order->detail->delivery_date, $order->detail->received_date);

    //         }

	// 		if(!isset($attachments[$da->e_chat_id])) $attachments[$da->e_chat_id] = array();
	// 		$attachments[$da->e_chat_id][] = $da;

	// 	}
	// 	unset($data_attachments);
	// 	unset($da);

	// 	//get chat
	// 	$chats = $this->chat->getDetailByRoomID($nation_code,$chat_room_temp_id,$chat_type);
	// 	foreach($chats as &$chat){
	// 		$chat->attachments = array();
	// 		$chat->complains = array();

	// 		if(isset($attachments[$chat->id])){
	// 			$chat->attachments = $attachments[$chat->id];
	// 		}
			
	// 		if(isset($complains[$chat->id])){
	// 			$chat->complains = $complains[$chat->id];
	// 		}
	// 	}

    //     //set read by admin
    //     $this->ecpm->update($nation_code, $chat_room_temp_id, $chat_type, array('is_read_admin' =>1));

	// 	$data['chat_room_id'] = $chat_room_id;
	// 	$data['chat_room_id_customer_1'] = $chatRoomCustomer1Detail->id;
	// 	$data['chat_room_id_customer_2'] = $chatRoomCustomer2Detail->id;
	// 	$data['chat_type'] = $chat_type;
	// 	$data['type'] = $type;
	// 	$data['chat_room'] = $chatRoomDetail;
	// 	$data['chat_room_customer_1'] = $customer1Data;
	// 	$data['chat_room_customer_2'] = $customer2Data;
	// 	$data['chats'] = $chats;
	// 	$data['complains'] = array();
		
	// 	$this->setTitle('Chat Detail #'.$chat_room_temp_id.'/'.$chat_type.' '.$this->site_suffix_admin);
	// 	$this->putThemeContent("crm/chat_v2/detail_modal",$data);
	// 	$this->putThemeContent("crm/chat_v2/detail",$data);
	// 	$this->putJsContent("crm/chat_v2/detail_bottom",$data);
	// 	$this->loadLayout('col-2-left',$data);
	// 	$this->render();
	// }
	
}
