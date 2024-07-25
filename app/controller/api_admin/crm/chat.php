<?php
    /*==========================================================================================================================
    |                                           Documentation API Chat
    |   
    |   Hooks added : 
    |       1. __convert_to_emoji((string)payload)
    |       2. __check_environment()
    |       3. __insert_file_attachment((array)payload,(array)file_payload)
    |       4. __insert_product_attachment((array)payload)
    |       5. __insert_order_attachment((array)payload)
    |       6. __push_notification((array)payload)
    |   
    |   Model Revamped : 
    |       1. chat_room_model
    |       2. chat_model
    |       3. participant_model
    |       4. attachment_model
    |       5. user_setting
    |   
    |   HTTP Function existing : 
    |       1. [GET]    index() 
    |       2. [GET]    get_chat((int)$room_id)
    |       3. [GET]    get_participant((int)$room_id)
    |       4. [GET]    get_room_admin((int)$room_id)
    |       5. [POST]   send_chat_admin((int)$room_id)
    |   
    ==========================================================================================================================*/
?>

<?php
class Chat extends JI_Controller {
    public $is_log = 1;

    public function __construct() {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_admin/chat/chat_room", 'chat_room_model');
        $this->load("api_admin/chat/chat_model", 'chat_model');
        $this->load("api_admin/chat/chat_participant", 'participant_model');
        $this->load("api_admin/chat/chat_attachment", 'attachment_model');
        $this->load("api_admin/product/product_model", "product_model");
        $this->load("api_admin/user/user_model", "user_model");
        $this->load("api_admin/user/user_setting", "user_setting");
        $this->load("api_admin/notification/notification_model", 'notification_model');
        $this->load("api_admin/e_chat_room_model", 'ecrm');
        
        //by Donny Dennison - 22 november 2021 15:49    
        //set unread in table e_chat_read
        $this->load("api_admin/chat/e_chat_read_model", 'chat_read_model');

        // $this->lib("seme_log");
        // $this->lib("seme_curl");
        // $this->load("api_mobile/d_order_detail_model", 'dodm');
        // $this->load("api_mobile/d_order_detail_item_model", 'dodim');
        // $this->load("api_mobile/d_pemberitahuan_model", 'dpem');
        // $this->load("api_mobile/e_chat_model", 'chat');
        // $this->load("api_mobile/e_chat_room_model", 'ecrm');
        // $this->load("api_mobile/e_chat_participant_model", 'ecpm');
        // $this->load("api_mobile/e_complain_model", 'complain');
        // $this->load("api_mobile/e_chat_attachment_model", 'ecam');
    }

    //credit: https://www.php.net/manual/en/function.com-create-guid.php#119168
    private function GUIDv4($trim = true)
    {
        // Windows
        if (function_exists('com_create_guid') === true) {
            if ($trim === true)
                return trim(com_create_guid(), '{}');
            else
                return com_create_guid();
        }

        // OSX/Linux
        if (function_exists('openssl_random_pseudo_bytes') === true) {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }

        // Fallback (PHP 4.2+)
        mt_srand((double)microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);                  // "-"
        $lbrace = $trim ? "" : chr(123);    // "{"
        $rbrace = $trim ? "" : chr(125);    // "}"
        $guidv4 = $lbrace.
                  substr($charid,  0,  8).$hyphen.
                  substr($charid,  8,  4).$hyphen.
                  substr($charid, 12,  4).$hyphen.
                  substr($charid, 16,  4).$hyphen.
                  substr($charid, 20, 12).
                  $rbrace;
        return $guidv4;
    }

    private function __convert_to_emoji($payload){
        $value = json_decode($payload);
        if ($value) {
            return json_decode($payload);
        } else {
            return json_decode('"'.$payload.'"');
        }
    }

    private function __convertToEmoji($text){
		$value = $text;
		$readTextWithEmoji = preg_replace("/\\\\u([0-9A-F]{2,5})/i", "&#x$1;", $value);
		return $readTextWithEmoji;
	}

    private function __check_environment(){
        $this->__init();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out(array());
            die();
        }
    }

    private function __insert_file_attachment($payload, $file_payload){
        if (isset($file_payload['name'])) {
            if ($file_payload['size']>8000000) {
                $this->message .= ', but attachment too big';
            } elseif (strlen($file_payload['tmp_name'])) {

                //get last id of attachment
                $last_id = $this->attachment_model->get_last_id($payload['nation_code'], $payload['chat_room_id'], $payload['chat_id']);

                //directory structure
                $thn = date("Y");
                $bln = date("m");
                $ds = DIRECTORY_SEPARATOR;
                $target = $this->media_chat;
                if (!realpath($target)) {
                    mkdir($target, 0775);
                }
                $target = $this->media_chat.$ds.$thn;
                if (!realpath($target)) {
                    mkdir($target, 0775);
                }
                $target = $this->media_chat.$ds.$thn.$ds.$bln;
                if (!realpath($target)) {
                    mkdir($target, 0775);
                }

                $jenis = mime_content_type($file_payload['tmp_name']);
                $ext = pathinfo($file_payload['name'], PATHINFO_EXTENSION);
                $filename = $payload['chat_room_id'].'-'.$payload['chat_id'].'-'.$last_id.'.'.$ext;
                if (file_exists(SENEROOT.$ds.$target.$ds.$filename)) {
                    unlink(SENEROOT.$ds.$target.$ds.$filename);
                }
                $upload_res = move_uploaded_file($file_payload['tmp_name'], SENEROOT.$ds.$target.$ds.$filename);
                if ($upload_res) {
                    $url = $this->media_chat.'/'.$thn.'/'.$bln.'/'.$filename;
                    $url = str_replace("//", "/", $url);

                    $attachment_payload = array();
                    $attachment_payload['nation_code'] = $payload['nation_code'];
                    $attachment_payload['e_chat_room_id'] = $payload['chat_room_id'];
                    $attachment_payload['e_chat_id'] = $payload['chat_id'];
                    $attachment_payload['id'] = $last_id;
                    $attachment_payload['jenis'] = $file_payload['type'];
                    $attachment_payload['ukuran'] = $file_payload['size'];
                    $attachment_payload['url'] = $url;
                    //insert into database
                    $attachment_res = $this->attachment_model->set($attachment_payload);
                    if ($attachment_res) {
                        $this->message = ', attachment uploaded';
                    } else {
                        $this->message = ', attachment failed';
                    }
                    $this->chat_model->trans_commit();
                } else {
                    $this->message = ', but image upload failed';
                }
            } //end check file Size
        }
    }

    private function __insert_product_attachment($payload){
        if ($payload['product_id']  > 0) {
            //get last id of attachment
            $last_id = $this->attachment_model->get_last_id($payload['nation_code'], $payload['chat_room_id'], $payload['chat_id']);

            //get product detail
            $product_attached = $this->product_model->get_by_id($payload['nation_code'], $payload['product_id']);

            //directory structure
            $thn = date("Y");
            $bln = date("m");
            $ds = DIRECTORY_SEPARATOR;
            $target = $this->media_chat;
            if (!realpath($target)) {
                mkdir($target, 0775);
            }
            $target = $this->media_chat.$ds.$thn;
            if (!realpath($target)) {
                mkdir($target, 0775);
            }
            $target = $this->media_chat.$ds.$thn.$ds.$bln;
            if (!realpath($target)) {
                mkdir($target, 0775);
            }

            $jenis = mime_content_type(SENEROOT.$product_attached->thumb);
            $ext = pathinfo(SENEROOT.$product_attached->thumb, PATHINFO_EXTENSION);
            $filename = $payload['chat_room_id'].'-'.$payload['chat_id'].'-'.$last_id.'.'.$ext;
            if (file_exists(SENEROOT.$ds.$target.$ds.$filename)) {
                unlink(SENEROOT.$ds.$target.$ds.$filename);
            }
            $res = copy(SENEROOT.$product_attached->thumb, SENEROOT.$ds.$target.$ds.$filename);
            
            $url = $this->media_chat.'/'.$thn.'/'.$bln.'/'.$filename;
            $url = str_replace("//", "/", $url);

            $product_payload = array();
            $product_payload['nation_code'] = $payload['nation_code'];
            $product_payload['e_chat_room_id'] = $payload['chat_room_id'];
            $product_payload['e_chat_id'] = $payload['chat_id'];
            $product_payload['id'] = $last_id;
            $product_payload['jenis'] = 'product';
            $product_payload['url'] = $payload['product_id'];
            $product_payload['produk_nama'] = $product_attached->nama;
            $product_payload['produk_harga_jual'] = $product_attached->harga_jual;
            $product_payload['produk_thumb'] = $url;

            //insert into database
            $res = $this->attachment_model->set($product_payload);

            //set custom_name_1
            $update_payload = array();
            $update_payload['custom_name_1'] = $product_attached->nama;

            $this->chat_room_model->update($payload['nation_code'], $payload['chat_room_id'], $update_payload);

            $this->chat_model->trans_commit();

        }
    }

    private function __insert_order_attachment($payload){
        if ($payload['_id'] > 0 && $payload['_detail_id'] > 0 && $payload['_item_id'] > 0) {
            //get last id of attachment
            $last_id = $this->attachment_model->get_last_id($payload['nation_code'], $payload['chat_room_id'], $payload['chat_id']);

            $payload_order = array();
            $payload_order['nation_code'] = $payload['nation_code'];
            $payload_order['e_chat_room_id'] = $payload['chat_room_id'];
            $payload_order['e_chat_id'] = $payload['chat_id'];
            $payload_order['id'] = $last_id;
            $payload_order['jenis'] = 'order';
            $payload_order['url'] = $payload['_id'] ;
            $payload_order['order_detail_id'] = $payload['_detail_id'];
            $payload_order['order_detail_item_id'] = $payload['_item_id'];

            //insert into database
            $res = $this->attachment_model->set($payload_order);

            $this->chat_model->trans_commit();
        }
    }

    private function __push_notification($__payload){
        //get missing data
        $receiver = $this->user_model->get_by_id($__payload['nation_code'], $__payload['receiver']);

        $type = 'chat';
        $anotid = 1;
        $replacer = array();
        $replacer['pelanggan_fnama'] = 'SellOn Support';
        $classified = 'setting_notification_user';
        $code = 'U4';

        $receiver_setting_notif = $this->user_setting->get_value($__payload['nation_code'], $__payload['receiver'], $classified, $code);

        if (!isset($receiver_setting_notif->setting_value)) {
            $receiver_setting_notif->setting_value = 0;
        }

        //Push Notification
        if ($receiver_setting_notif->setting_value == 1) {
            if (strlen($receiver->fcm_token)>50) {
                $device = $receiver->device; //jenis device
                $tokens = array($receiver->fcm_token); //device token
                $title = 'Obrolan Baru';
                $message = "Anda memiliki pesan baru dari Administrator. Balas sekarang";
                $type = 'chat';
                $image = 'media/pemberitahuan/chat.png';
                $payload = new stdClass();
                $payload->chat_room_id = (string) $__payload['chat_room_id'];
                $payload->user_id = 0;
                $payload->user_fnama = '';
                $payload->user_image = '';
                $payload->chat_type = $__payload['chat_type'];

                $notification_result = $this->notification_model->get($__payload['nation_code'], "push", $type, $anotid);
                if (isset($notification_result->title)) {
                    $title = $notification_result->title;
                }
                if (isset($notification_result->message)) {
                    $message = $this->__nRep($notification_result->message, $replacer);
                }
                if (isset($notification_result->image)) {
                    $image = $notification_result->image;
                }
                $image = $this->cdn_url($image);
                $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
            }
        }
    }
    
    public function index() {
        $this->__check_environment();
        $__init_data = $this->__init();
        $data = array();
        $nation_code = $__init_data['sess']->admin->nation_code;

        $draw = $this->input->post("draw");

        //by Donny Dennison - 22 october 2021 16:54
        //cms-community
        //fix search in chat list and last chat by column
        // $sval = $this->input->post("search");

        $sSearch = $this->input->post("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $page_size = $this->input->post("iDisplayLength");
        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");

        $from_date = $this->input->post("from_date");
        $to_date = $this->input->post("to_date");
        $room_type = $this->input->post("room_type");
        $sortCol = "cdate";

        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }

        $tbl_chat_room = $this->chat_room_model->get_table_name('tbl_chat_room');

        switch ($iSortCol_0) {
            case 0:
                $sortCol = "$tbl_chat_room.ldate";
            break;
            // case 1:
            //     $sortCol = "$tbl_chat_room.id";
            // break;
            // case 2:
            //     $sortCol = "$tbl_chat_room.chat_type";
            // break;
            // case 1:
            //     $sortCol = "$tbl_chat_room.chat_type";
            // break;
            default:
                $sortCol = "$tbl_chat_room.ldate";
        }

        if (empty($draw)) {
            $draw = 0;
        }
        if (empty($page_size)) {
            $page_size=10;
        }
        if (empty($page)) {
            $page=0;
        }

        $keyword = $sSearch;
        
        if(empty($keyword)) $keyword = '';

        $this->status = 200;
        $this->message = 'Success';

        // START by Muhammad Sofi 2 February 2022 14:17 | change filter start date to end date
        //validating date interval
        if (strlen($from_date)==10) {
            $from_date = date("Y-m-d", strtotime($from_date));
        } else {
            $from_date = "";
        }
        if (strlen($to_date)==10) {
            $to_date = date("Y-m-d", strtotime($to_date));
        } else {
            $to_date = "";
        }
        // END by Muhammad Sofi 2 February 2022 14:17 | change filter start date to end date

        $__filter = array('keyword'=>$keyword, 'room_type'=>$room_type, 'from_date'=>$from_date, 'to_date'=>$to_date);

        $count_data = $this->chat_room_model->count_all($nation_code, $__filter);
        $data = $this->chat_room_model->get_all($nation_code, $page, $page_size, $sortCol, $sortDir, $__filter);

        $payload = array();
        foreach ($data as $key => $dts) {
            $payload[$key][] = $dts->room_id;
            // $payload[$key][] = $dts->last_date;
            $payload[$key][] = $dts->last_chat_date;
            $payload[$key][] = $dts->room_type;
            $payload[$key][] = $dts->starter_fname;

            //by Donny Dennison - 22 october 2021 16:54
            //cms-community
            //fix search in chat list and last chat by column
            // $payload[$key][] = $dts->starter_fname;
            $payload[$key][] = $dts->last_fname;
            
            $payload[$key][] = html_entity_decode($this->__convertToEmoji($dts->last_chat),ENT_QUOTES);
            $payload[$key]['action'] = '<a href="'.base_url_admin('crm/chat/detail/'.strtolower($dts->room_type).'/'.$dts->room_id).'" class="btn btn-default">View Detail</button>';
        }
        unset($data, $key, $dts);
        
        $this->__jsonDataTable($payload, $count_data);
    }

    public function get_chat($room_id){
        $this->__check_environment();
        $__init_data = $this->__init();
        $nation_code = $__init_data['sess']->admin->nation_code;

        // $room_id = (int) $room_id;
        $room_id = $room_id; // change to string
        

        $dcount = $this->chat_model->count_all($nation_code, $room_id);
        $chats = $this->chat_model->get_all($nation_code,$room_id);

        if ($chats) {
            // code...
            $this->status = 200;
            $this->message = 'Success';
            foreach($chats as $chat){
                if($chat->type==='chat'){
                    $chat->message = strip_tags(html_entity_decode($this->__convertToEmoji($chat->message),ENT_QUOTES)); //strip_tags untuk menghilangkan <br />
                    $chat->attachment = $this->attachment_model->get_all($nation_code, $room_id, $chat->id);
                    foreach ($chat->attachment as $res_attachment) {
                        if(isset($res_attachment->product_name)){
                            $res_attachment->product_name = html_entity_decode($this->__convertToEmoji($res_attachment->product_name));
                        }
                        if(isset($res_attachment->price)) {
                            $res_attachment->price = 'Rp. '.number_format($res_attachment->price, 2,',', '.');
                        }
                        $order_status = $this->__getOrderStatus($res_attachment->order_status, $res_attachment->payment_status, $res_attachment->seller_status, $res_attachment->shipment_status, $res_attachment->buyer_confirmed, $res_attachment->delivery_date, $res_attachment->received_date);
                        $res_attachment->order_status = $order_status->text;

                        if(($res_attachment->type) == "barter_request") {
                            $res_attachment->type_barter = "Barter Request";
                        } else if(($res_attachment->type) == "barter_exchange") {
                            $res_attachment->type_barter = "Barter Exchange";
                        }
                    }
                    if($chat->room_type ==='offer' || $chat->room_type ==='offering') {
                        $chat->message = strip_tags(html_entity_decode($this->__convertToEmoji($chat->message),ENT_QUOTES)); //strip_tags untuk menghilangkan <br />
                        $chat->attachment = $this->chat_room_model->get_all_offer($nation_code, $room_id, $chat->id);

                        foreach ($chat->attachment as $res_attachment) {
                            if(isset($res_attachment->product_name_offer)){
                                $res_attachment->product_name_offer = html_entity_decode($this->__convertToEmoji($res_attachment->product_name_offer));
                            }
                            if(isset($res_attachment->harga_jual)) {
                                $res_attachment->harga_jual = 'Rp. '.number_format($res_attachment->harga_jual, 2,',', '.');
                            }
                        }
                    }
                } 
                else if($chat->type ==='offer' || $chat->type === 'offering' || $chat->room_type ==='offer' || $chat->room_type ==='offering' ) {
                    $chat->message = strip_tags(html_entity_decode($this->__convertToEmoji($chat->message),ENT_QUOTES)); //strip_tags untuk menghilangkan <br />
                    // $chat->attachment = $this->chat_room_model->get_all_offer($nation_code, $room_id, $chat->id);

                    // foreach ($chat->attachment as $res_attachment) {
                    //     if(isset($res_attachment->product_name_offer)){
                    //         $res_attachment->product_name_offer = html_entity_decode($this->__convertToEmoji($res_attachment->product_name_offer));
                    //     }
                    //     if(isset($res_attachment->harga_jual)) {
                    //         $res_attachment->harga_jual = 'Rp. '.number_format($res_attachment->harga_jual, 2,',', '.');
                    //     }
                    // }
                }
            }
        }

        $this->__jsonDataTable($chats, $dcount);
    }

    public function get_participant($room_id){
        $this->__check_environment();
        $__init_data = $this->__init();
        $nation_code = $__init_data['sess']->admin->nation_code;

        // $room_id = (int) $room_id;
        $room_id = $room_id; // change to string
        

        $dcount = $this->participant_model->count_all($nation_code, $room_id);
        $participants = $this->participant_model->get_with_admin($nation_code,$room_id);
        
        if ($participants) {
            // code...
            $this->status = 200;
            $this->message = 'Success';
        }

        $this->__jsonDataTable($participants, $dcount);
    }

    public function get_room_admin($user_id){
        $this->__check_environment();
        $__init_data = $this->__init();
        $nation_code = $__init_data['sess']->admin->nation_code;

        // $user_id = (int) $user_id;
        $user_id = $user_id;

        $data = array();
        if ($user_id!=='' && $user_id>0) {
            $room_id = $this->chat_model->getAdminChatRoom($nation_code, $user_id);
            if (!$room_id) {
                // $room_id = $this->chat_model->createChatRoom($nation_code, $user_id);
                $endDoWhile = 0;
                do{

                    $room_id = $this->GUIDv4();

                    $checkId = $this->ecrm->checkId($nation_code, $room_id);

                    if($checkId == 0){
                        $endDoWhile = 1;
                    }

                }while($endDoWhile == 0);
                $room_id = $this->chat_model->createChatRoom($nation_code, $user_id,$room_id);
            }
            $this->status = 200;
            $this->message = 'Success';
            $data[] = (object) $room_id;
            $this->__jsonDataTable($data, 1);
        } else {
            $this->status = 201;
            $this->message = 'User ID is not defined';
            $this->__jsonDataTable(array(), 0);
        }
    }

    public function send_chat_admin($room_id) {
        $this->__check_environment();
        $__init_data = $this->__init();
        $data = array();
        $payload = json_decode(file_get_contents('php://input'), true);
        
        //collect input
        $chat_type = "Admin";
        $message = "";
        if($payload) $message = $payload["textMessage"];
        else $message = $this->input->post('message');
        $nation_code = $__init_data['sess']->admin->nation_code;
        $pengguna = $__init_data['sess']->admin;

        //validating
        // $chat_room_id = (int) $room_id;
        $chat_room_id = $room_id; // back to string
        if ($chat_room_id<='0') {
            $this->status = 4000;
            $this->message = 'Invalid chat_room_id';
            $this->__json_out($data);
            die();
        }

        $chat_room = $this->chat_room_model->get_by_id($nation_code, $chat_room_id);
        $participant = $this->participant_model->get_by_id($nation_code, $chat_room_id);

        $message = nl2br(trim($message));
        $message = strip_tags($message, "<br>");

        //TRANSACTION
        $this->chat_model->trans_start();

            //get last id
            $chat_id = (int)$this->chat_model->get_last_id($nation_code, $chat_room_id);
            if($chat_id<1)$chat_id=1;

            //insert into e_chat table
            $chat_message_payload = array(
                'a_pengguna_id' => 0,
                'b_user_id' => 0,
                'cdate' => "NOW()",
                'e_chat_room_id' => $chat_room_id,
                'id' => $chat_id,
                'nation_code' => $nation_code,
                'message' => $message,
                'message_indonesia' => $message,
                'type' => "chat"
            );

            $res = $this->chat_model->set($chat_message_payload);

            if ($res) {
                $this->status = 200;
                $this->message = 'Success';
                $this->chat_model->trans_commit();

                //set unread for admin
                $du = array();
                $status_payload = array('is_read_admin' => 0 );
                $this->chat_room_model->update($nation_code, $chat_room_id, $status_payload);

                //set unread for other chat participant
                $status_payload = array('is_read' => 0 );
                $this->participant_model->update($nation_code, $chat_room_id, $participant->user_id, $status_payload);
                
                //START by Donny Dennison - 22 november 2021 15:49    
                //set unread in table e_chat_read
                $du = array();
                $du['nation_code'] = $nation_code;
                $du['b_user_id'] = $participant->user_id;
                $du['e_chat_room_id'] = $chat_room_id;
                $du['e_chat_id'] = $chat_id;
                $du['cdate'] = "NOW()";

                $this->chat_read_model->set($du);  
                $this->chat_model->trans_commit();
                //END by Donny Dennison - 22 november 2021 15:49  

                $file = reset($_FILES);
                $file_payload = array(
                    'chat_room_id' => $chat_room_id,
                    'chat_id' => $chat_id,
                    'nation_code' => $nation_code
                );
                $this->__insert_file_attachment($file_payload, $file);

                $product_payload = array(
                    'chat_room_id' => $chat_room_id,
                    'chat_id' => $chat_id,
                    'nation_code' => $nation_code,
                    'product_id' => $this->input->post('product_id')
                );
                $this->__insert_product_attachment($product_payload);

                $imploded_order = "";
                if($this->input->post('buyer_invoice') != '') $imploded_order = $this->input->post('buyer_invoice');
                else if($this->input->post('seller_invoice') != '') $imploded_order = $this->input->post('seller_invoice');
                else $imploded_order = '0/0/0';
                $exploded_order = explode("/", $imploded_order);

                $order_id = $exploded_order[0];
                $order_detail_id = $exploded_order[1];
                $order_detail_item_id = $exploded_order[2];

                $order_payload = array(
                    '_id'           => $order_id,
                    '_detail_id'    => $order_detail_id,
                    '_item_id'      => $order_detail_item_id,
                    'chat_room_id' => $chat_room_id,
                    'chat_id' => $chat_id,
                    'nation_code' => $nation_code
                );
                $this->__insert_order_attachment($order_payload);

                $chat_room = $this->chat_room_model->get_by_id($nation_code, $chat_room_id);
                
                if(isset($participant->user_image) && file_exists(SENEROOT.$participant->user_image) && $participant->user_image != 'media/user/default.png'){
                    $participant->user_image = $this->cdn_url($participant->user_image);
                } else {
                    $participant->user_image = $this->cdn_url('media/user/default-profile-picture.png');
                }

                $notif_payload = array(
                    'receiver' => $participant->user_id,
                    'chat_room_id' => $chat_room_id,
                    'chat_id' => $chat_id,
                    'chat_type' => 'admin',
                    'nation_code' => $nation_code,
                    'custom_name_1' => $chat_room->custom_name_1,
                    'custom_name_2' => 'SellOn Support',
                    'custom_image' => $participant->user_image
                );
                $this->__push_notification($notif_payload);
            } 
            else {
                $this->status = 4004;
                $this->message = 'Failed add chat to database';
                $this->ecm->trans_rollback();
            }

        $this->chat_model->trans_end();
        //TRANSACTION

        $this->__json_out($data);
    }
}
