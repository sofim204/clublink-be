<?php
    /*==========================================================================================================================
    |                                           Documentation Controller Chat
    |   
    |   Hooks added : 
    |       1. checkEnvironment()
    |   
    |   
    |   URI existing : 
    |       1. index() 
    |       2. send_chat_admin((int)$room_id)
    |       3. get_detail_room((int)$room_id)
    |   
    ==========================================================================================================================*/
?>

<?php
class Chat extends JI_Controller{

	public function __construct(){
		parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'crm';
		$this->current_page = 'crm_chat';
		$this->load("api_admin/chat/chat_room","chat_room_model");
		$this->load("api_admin/chat/chat_participant","chat_participant_model");
		// $this->load("admin/b_user_model","bum");
		// $this->load("admin/c_produk_model","cpm");
		// $this->load("admin/d_order_model","dom");
		// $this->load("admin/d_order_detail_model","dodm");
		// $this->load("admin/d_order_detail_item_model","dodim");
		// $this->load("admin/e_chat_model","chat");
		// $this->load("admin/e_chat_attachment_model","ecam");
		// $this->load("admin/e_chat_participant_model","ecpm");
		// $this->load("admin/e_complain_model","complain");
	}

    private function checkEnvironment(){
        $this->__init();
        if (!$this->admin_login) {
			redir(base_url_admin('login'));
			die('admin not logged in');
        }
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

		$data['table_column'] = array(
			'Room ID' => (object) array(
				'width' => 5,
			), 
			'Last Update' => (object) array(
				'width' => 15,
			),   
			'Room Type' => (object) array(
				'width' => 10,
			),   
			'Chat Starter' => (object) array(
				'width' => 15,
			),  
			'Last Chat By' => (object) array(
				'width' => 15,
			), 
			'Last Chat Message' => (object) array(
				'width' => 20,
			), 
			'Action' =>  (object) array(
				'width' => 15,
			)
		);

		$data['from_date'] = date("Y-m-d", strtotime('last month'));
		$data['to_date'] = date("Y-m-d", strtotime('now'));
		// $data['from_date'] = date("Y-m-d", strtotime('2022-01-11'));
		// $data['to_date'] = date("Y-m-d", strtotime('2022-01-19'));

		$this->setTitle('CRM: Admin Chat'.$this->site_suffix_admin);

		$this->putThemeContent("crm/chat/home_modal",$data);
		$this->putThemeContent("crm/chat/home",$data);
		$this->putJsReady("crm/chat/home_js",$data);

		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function detail($room_type, $_id=""){
        $this->checkEnvironment();
		$data = $this->__init();
		$user_id = (int) $_id;
		// $room_id = (int) $_id;
		$room_id = $_id; // change to string
		$nation_code = $data['sess']->admin->nation_code;
		/*

		if($room_type == "admin"){
			// echo "Test";
			//check already have chat room or not
			$checkChatRoom = $this->chat_room_model->checkRoomChat($nation_code, 0, $user_id);

			if(!isset($checkChatRoom->id)){
	            //insert room chat
	            $di = array();
	            $di['nation_code'] = $nation_code;
	            $di['b_user_id_1'] = 0;
            	$di['b_user_id_2'] = $user_id;
	            $di['b_user_id_2_is_active'] = 0;
	            $di['chat_type'] = "Admin";

	            $this->chat_room_model->set($di);

	            $room_id = $this->chat_room_model->lastId();

	        }else{
	        	$room_id = $checkChatRoom->id;
	        }
		}

		//get chat room detail
		//get complain
		$complains = array();
		$data_complains = $this->complain->getDetailByChatRoomID($nation_code,$room_id);
		foreach($data_complains as $dc){

			if(!isset($complains[$dc->e_chat_id])) $complains[$dc->e_chat_id] = array();
			$complains[$dc->e_chat_id][] = $dc;

		}
		unset($data_complains);
		unset($dc);

		//get attachments
		$attachments = array();
		$data_attachments = $this->ecam->getByChatRoomChatId($nation_code,$room_id);
		foreach($data_attachments as $da){
			
            $da->order_invoice_code = '';
            $da->order_name = '';
            $da->order_thumb = '';
            $da->order_status = '';

			if($da->jenis == 'order'){

				//get order
				$order = $this->dom->getById($nation_code,$da->url);
				$order->detail = $this->dodm->getDetailByOrderId($nation_code,$da->url,$da->order_detail_id);
				$order->item = $this->dodim->getById($nation_code,$da->url,$da->order_detail_id,$da->order_detail_item_id);

				$da->order_invoice_code = $order->invoice_code;
            	$da->order_name = $order->item->nama;
            	$da->order_thumb = $order->item->thumb;
				$da->order_status = $this->__getOrderStatus($order->order_status,$order->payment_status,$order->detail->seller_status,$order->detail->shipment_status,$order->detail->buyer_confirmed,$order->detail->delivery_date, $order->detail->received_date);

            }

			if(!isset($attachments[$da->e_chat_id])) $attachments[$da->e_chat_id] = array();
			$attachments[$da->e_chat_id][] = $da;

		}
		unset($data_attachments);
		unset($da);

		//get chat
		$chats = $this->chat_room_model->getChatByRoomId($nation_code,$room_id);
		// foreach($chats as &$chat){
		// 	$chat->attachments = array();
		// 	$chat->complains = array();

		// 	if(isset($attachments[$chat->id])){
		// 		$chat->attachments = $attachments[$chat->id];
		// 	}
			
		// 	if(isset($complains[$chat->id])){
		// 		$chat->complains = $complains[$chat->id];
		// 	}
		// }
		foreach($chats as &$chat){
			if($chat->type=='chat'){
				$chat->message = $this->__convertToEmoji($chat->message);
			}
		}

        //set read by admin
        $this->chat_room_model->update($nation_code, $room_id, array('is_read_admin' =>1));

		$data['chats'] = $chats;
		$data['complains'] = array();
		*/
		$chatRoomDetail = $this->chat_room_model->get_by_id($nation_code, $room_id);

		$data['chat_room'] = $chatRoomDetail;
		$data['chat_type'] = $room_type;
		$data['chat_room_id'] = $room_id;

		$this->setTitle('Chat Detail #'.$room_id.' '.$this->site_suffix_admin);
		$this->putThemeContent("crm/chat/detail_modal",$data);
		$this->putThemeContent("crm/chat/detail",$data);
		$this->putJsContent("crm/chat/detail_js",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function admin($room_id, $user=0){
		$room_id = (int) $room_id;

		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die('admin not logged in');
		}
		$nation_code = $data['sess']->admin->nation_code;

		if($room_id == 0){
			//check already have chat room or not
			$checkChatRoom = $this->chat_room_model->checkRoomChat($nation_code, 0, $user_id);

			if(!isset($checkChatRoom->id)){
	            //insert room chat
	            $di = array();
	            $di['nation_code'] = $nation_code;
	            $di['b_user_id_1'] = 0;
            	$di['b_user_id_2'] = $user_id;
	            $di['b_user_id_2_is_active'] = 0;
	            $di['chat_type'] = "Admin";

	            $this->chat_room_model->set($di);

	            $room_id = $this->chat_room_model->lastId();

	        }else{

	        	$room_id = $checkChatRoom->id;

	        }

		}

		//get chat room detail
		$chatRoomDetail = $this->chat_room_model->getByRoomId($nation_code, $room_id);
		//get complain
		$complains = array();
		$data_complains = $this->complain->getDetailByChatRoomID($nation_code,$room_id);
		foreach($data_complains as $dc){

			if(!isset($complains[$dc->e_chat_id])) $complains[$dc->e_chat_id] = array();
			$complains[$dc->e_chat_id][] = $dc;

		}
		unset($data_complains);
		unset($dc);

		//get attachments
		$attachments = array();
		$data_attachments = $this->ecam->getByChatRoomChatId($nation_code,$room_id);
		foreach($data_attachments as $da){
			
            $da->order_invoice_code = '';
            $da->order_name = '';
            $da->order_thumb = '';
            $da->order_status = '';

			if($da->jenis == 'order'){

				//get order
				$order = $this->dom->getById($nation_code,$da->url);
				$order->detail = $this->dodm->getDetailByOrderId($nation_code,$da->url,$da->order_detail_id);
				$order->item = $this->dodim->getById($nation_code,$da->url,$da->order_detail_id,$da->order_detail_item_id);

				$da->order_invoice_code = $order->invoice_code;
            	$da->order_name = $order->item->nama;
            	$da->order_thumb = $order->item->thumb;
				$da->order_status = $this->__getOrderStatus($order->order_status,$order->payment_status,$order->detail->seller_status,$order->detail->shipment_status,$order->detail->buyer_confirmed,$order->detail->delivery_date, $order->detail->received_date);

            }

			if(!isset($attachments[$da->e_chat_id])) $attachments[$da->e_chat_id] = array();
			$attachments[$da->e_chat_id][] = $da;

		}
		unset($data_attachments);
		unset($da);

		//get chat
		$chats = $this->chat->getDetailByRoomID($nation_code,$room_id);
		foreach($chats as &$chat){
			$chat->attachments = array();
			$chat->complains = array();

			if(isset($attachments[$chat->id])){
				$chat->attachments = $attachments[$chat->id];
			}
			
			if(isset($complains[$chat->id])){
				$chat->complains = $complains[$chat->id];
			}
		}

        //set read by admin
        $this->chat_room_model->update($nation_code, $room_id, array('is_read_admin' =>1));

		$data['chat_type'] = "Admin";
		$data['chat_room_id'] = $room_id;
		$data['chat_room'] = $chatRoomDetail;
		$data['chats'] = $chats;
		$data['complains'] = array();


		$this->setTitle('Chat Detail #'.$room_id.' '.$this->site_suffix_admin);
		$this->putThemeContent("crm/chat/detail_modal",$data);
		$this->putThemeContent("crm/chat/admin",$data);
		$this->putJsContent("crm/chat/detail_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();

		// $data = $this->__init();
		// $this->setTitle('Chat Admin'.$this->site_suffix_admin);
		// $this->putThemeContent("crm/chat/admin",$data);
		// $this->loadLayout('col-2-left',$data);
		// $this->render();
	}
}
