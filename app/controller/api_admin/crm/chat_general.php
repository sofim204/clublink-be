<?php
class Chat_General extends JI_Controller
{
    public $is_log = 1;

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_admin/a_notification_model", 'anot');
        $this->load("api_admin/b_user_model", 'bum');
        $this->load("api_admin/c_produk_model", 'cpm');
        $this->load("api_admin/d_order_model", 'dom');
        $this->load("api_admin/d_order_detail_model", 'dodm');
        $this->load("api_admin/d_order_detail_item_model", 'dodim');
        $this->load("api_admin/d_pemberitahuan_model", 'dpem');
        $this->load("api_admin/e_chat_room_model", 'ecpm');
        $this->load("api_admin/e_complain_model", 'complain');

        //by Donny Dennison - 19 october 2020 14:51
        //add user setting chat notif
        $this->load("api_admin/b_user_setting_model", "busm");
    }

    // private function __dataEcp($nation_code, $d_order_id, $d_order_detail_item_id,$chat_type, $b_user_id)
    // {
    //     if ($this->is_log) {
    //         $this->seme_log->write("api_admin", "API_Mobile/buyer/chat::__dataEcp");
    //     }
    //     $ecp = array();
    //     $ecp['nation_code'] = $nation_code;
    //     $ecp['d_order_id'] = $d_order_id;
    //     $ecp['c_produk_id'] = $d_order_detail_item_id;
    //     $ecp['b_user_id'] = $b_user_id;
    //     $ecp['is_read'] = 0;
    //     $ecp['chat_type'] = $chat_type;
    //     $ecps[] = $ecp;
    //     return $ecp;
    // }

    public function index()
    {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $nation_code = $d['sess']->admin->nation_code;

        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $sSearch = $this->input->post("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $page_size = $this->input->post("iDisplayLength");
        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");
        $sortCol = "cdate";

        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }

        $tbl_as = $this->ecpm->getTableAlias();
        $tbl2_as = $this->ecpm->getTableAlias2();
        $tbl5_as = $this->ecpm->getTableAlias5();
        $tbl10_as = $this->ecpm->getTableAlias10();
        $tbl11_as = $this->ecpm->getTableAlias11();

        switch ($iSortCol_0) {
            case 0:
            $sortCol = "$tbl_as.id";
            break;
            case 1:
            $sortCol = "$tbl2_as.cdate";
            break;
            case 2:
            $sortCol = "$tbl10_as.fnama";
            break;
            case 3:
            $sortCol = "$tbl11_as.fnama";
            break;
            case 4:
            $sortCol = "$tbl5_as.fnama";
            break;
            case 5:
            $sortCol = "$tbl2_as.message";
            break;
            default:
            $sortCol = "$tbl2_as.cdate";
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
        $dcount = $this->ecpm->countAll($nation_code, $keyword, 'ADMIN');
        $ddata = $this->ecpm->getAll($nation_code, $keyword, $page, $page_size, $sortCol, $sortDir, 'ADMIN');

        foreach ($ddata as &$dt) {
            if($dt->is_user){
              $dt->b_user_fnama_last_chat = $this->__dconv($dt->b_user_fnama_last_chat);
            }else{
              $dt->b_user_fnama_last_chat = $dt->a_pengguna_nama_last_chat;

            }

            // $dt->action = '<button class="btn btn-default" data-id="'.$dt->chat_room_id.'/'.$dt->chat_type.'">View Detail</button>'
            $dt->action = '<a href="'.base_url_admin('crm/chat/detail_admin/'.$dt->chat_room_id.'/'.$dt->chat_type.'/'.$dt->b_user_id_2).'" class="btn btn-default">View Detail</button>';

            if($dt->is_read_admin == 0){
                $dt->b_user_fnama_2 .= ' <span style= "height: 15px; width: 15px; background-color: #e74c3c; border-radius: 50%; display: inline-block;"></span> ';
            }

            // //by Donny Dennison - 15 september 2020 16:59
            // //add flag unread chat
            // //START by Donny Dennison - 15 september 2020 16:59

            // $totalUnreadChatAll = $this->ecm->getUnreadChatForFlag($nation_code, $dt->d_order_id, $dt->c_produk_id, 'A');

            // if($totalUnreadChatAll > 0){
            //     $dt->buyer_name .= ' <span style= "height: 15px; width: 15px; background-color: #7abce7; border-radius: 50%; display: inline-block;"></span> ';

            //     $dt->seller_name .= ' <span style= "height: 15px; width: 15px; background-color: #7abce7; border-radius: 50%; display: inline-block;"></span> ';
            // }

            // $totalUnreadChatBuyer = $this->ecm->getUnreadChatForFlag($nation_code, $dt->d_order_id, $dt->c_produk_id, 'B');

            // if($totalUnreadChatBuyer > 0){
            //     $dt->buyer_name .= ' <span style= "height: 15px; width: 15px; background-color: #e74c3c; border-radius: 50%; display: inline-block;"></span> ';
            // }

            // $totalUnreadChatSeller = $this->ecm->getUnreadChatForFlag($nation_code, $dt->d_order_id, $dt->c_produk_id, 'S');

            // if($totalUnreadChatSeller > 0){

            //     $dt->seller_name .= ' <span style= "height: 15px; width: 15px; background-color: #e67e22; border-radius: 50%; display: inline-block;"></span> ';
            // }

            //END by Donny Dennison - 15 september 2020 16:59

        }

        $return = array();
        foreach ($ddata as $key => $dts) {
			if(isset($dts->message_last_chat)){
				if (strlen($dts->message_last_chat)>255) {
					$dts->message_last_chat = substr($dts->message_last_chat, 0, 255)." <strong>(More...)<strong>";
				}
			}
            
            $return[$key]['id'] = $dts->chat_room_id;
            $return[$key]['cdate_last_chat'] = date("m/d/y H:i", strtotime($dts->cdate_last_chat));
            $return[$key]['b_user_fnama_1'] = $dts->b_user_fnama_1;
            $return[$key]['b_user_fnama_2'] = $dts->b_user_fnama_2;
            $return[$key]['b_user_fnama_last_chat'] = $dts->b_user_fnama_last_chat;
            $return[$key]['message_last_chat'] = $dts->message_last_chat;
            $return[$key]['action'] = $dts->action;
        }
        
        $this->__jsonDataTable($return, $dcount);
    }

    // public function tambah()
    // {
    //     $d = $this->__init();
    //     $data = array();
    //     if (!$this->admin_login) {
    //         $this->status = 400;
    //         $this->message = 'Unauthorized access';
    //         header("HTTP/1.0 400 Unauthorized");
    //         $this->__json_out($data);
    //         die();
    //     }

    //     $this->ecm->trans_start();

    //     $di = $_POST;
    //     $di['nation_code'] = $d['sess']->admin->nation_code;
    //     $di['id'] = $this->ecm->getLastId($di['nation_code']);

    //     $res = $this->ecm->set($di);
    //     if ($res) {
    //         $this->status = 200;
    //         $this->message = 'Data successfully added';
    //         $this->ecm->trans_commit();
    //     } else {
    //         $this->ecm->trans_rollback();
    //         $this->status = 900;
    //         $this->message = 'Tidak dapat menyimpan data baru, silakan coba beberapa saat lagi';
    //     }

    //     $this->ecm->trans_end();
    //     $this->__json_out($data);
    // }

    // public function detail($id)
    // {
    //     $id = (int) $id;
    //     $d = $this->__init();
    //     $data = array();
    //     if (!$this->admin_login) {
    //         $this->status = 400;
    //         $this->message = 'Unauthorized access';
    //         header("HTTP/1.0 400 Unauthorized");
    //         $this->__json_out($data);
    //         die();
    //     }

    //     if ($id<=0) {
    //         $this->status = 591;
    //         $this->message = 'Invalid ID';
    //         $this->__json_out($data);
    //         die();
    //     }

    //     $nation_code = $d['sess']->admin->nation_code;
    //     $this->status = 200;
    //     $this->message = 'Success';
    //     $data = $this->ecm->getById($nation_code, $id);
    //     $this->__json_out($data);
    // }

    // public function edit()
    // {
    //     //die('edit');
    //     $d = $this->__init();
    //     $data = array();
    //     if (!$this->admin_login) {
    //         $this->status = 400;
    //         $this->message = 'Unauthorized access';
    //         header("HTTP/1.0 400 Unauthorized");
    //         $this->__json_out($data);
    //         die();
    //     }

    //     if (!isset($du['cdate'])) {
    //         $di['cdate'] = "";
    //     }
    //     $nation_code = $d['sess']->admin->nation_code;

    //     if ($id>0 && strlen($du['cdate'])>0 && strlen($du['message'])>0) {
    //         $res = $this->ecm->update($nation_code, $id, $du);
    //         if ($res) {
    //             $this->status = 200;
    //             $this->message = 'Perubahan berhasil diterapkan';
    //         } else {
    //             $this->status = 901;
    //             $this->message = 'Failed to make data changes';
    //         }
    //     } else {
    //         $this->status = 440;
    //         $this->message = 'Salah satu parameter ada yang invalid atau kurang parameter';
    //     }
    //     $this->__json_out($data);
    // }

    // public function hapus($id)
    // {
    //     $id = (int) $id;
    //     $d = $this->__init();
    //     $data = array();
    //     if (!$this->admin_login && empty($id)) {
    //         $this->status = 400;
    //         $this->message = 'Unauthorized access';
    //         header("HTTP/1.0 400 Unauthorized");
    //         $this->__json_out($data);
    //         die();
    //     }

    //     $nation_code = $d['sess']->admin->nation_code;
    //     $this->status = 200;
    //     $this->message = 'Success';

    //     $res = $this->ecm->del($nation_code, $id);
    //     if (!$res) {
    //         $this->status = 902;
    //         $this->message = 'Failed while deleting data from database';
    //     }
    //     $this->__json_out($data);
    // }

    // public function sendMessage()
    // {
    //     //die('sendMessage');
    //     $d = $this->__init();
    //     $data = array();
    //     if (!$this->admin_login) {
    //         $this->status = 400;
    //         $this->message = 'Unauthorized access';
    //         header("HTTP/1.0 400 Unauthorized");
    //         $this->__json_out($data);
    //         die();
    //     }
    //     $pengguna = $d['sess']->admin;
    //     $nation_code = $d['sess']->admin->nation_code;

    //     //collect input
    //     $chat_room_id = $this->input->post("chat_room_id");
    //     // $c_produk_id = $this->input->post("c_produk_id");
    //     $message = $this->input->post("message");
    //     $chat_type = $this->input->post("chat_type");

    //     //validating
    //     $chat_room_id = (int) $chat_room_id;
    //     if ($chat_room_id<=0) {
    //         $this->status = 4000;
    //         $this->message = 'Invalid chat_room_id';
    //         $this->__json_out($data);
    //         die();
    //     }

    //     $chatRoom = $this->ecpm->getByRoomId($nation_code, $chat_room_id, $chat_type);

    //     // $c_produk_id = (int) $c_produk_id;
    //     // if ($c_produk_id<=0) {
    //     //     $this->status = 4001;
    //     //     $this->message = 'Invalid C_PRODUK_ID';
    //     //     $this->__json_out($data);
    //     //     die();
    //     // }
    //     $message = nl2br(trim($message));
    //     $message = strip_tags($message, "<br>");

    //     //open transaction
    //     $this->ecm->trans_start();

    //     //get last id
    //     $ecm_id = $this->ecm->getLastId($nation_code, $chat_room_id);

    //     //insert into e_chat table
    //     $di = array();
    //     $di['id'] = $ecm_id;
    //     $di['nation_code'] = $nation_code;
    //     $di['e_chat_room_id'] = $chat_room_id;
    //     $di['a_pengguna_id'] = $pengguna->id;
    //     $di['message'] = $message;
    //     $di['cdate'] = "NOW()";
    //     $res = $this->ecm->set($di);
    //     /*var_dump($res); die();*/
    //     if ($res) {
    //         $this->status = 200;
    //         $this->message = 'Success';
    //         $this->ecm->trans_commit();

    //         //set unread for other chat participant
    //         $du = array();
    //         $du['is_read_1'] = 0;
    //         $du['is_read_2'] = 0;
    //         $du['is_read_admin'] = 1;
    //         $du['b_user_id_1_is_active'] = 1;
    //         $du['b_user_id_2_is_active'] = 1;

    //         $this->ecpm->update($nation_code, $chat_room_id, $du);

    //         //start file upload if exist
    //         $idf = 'files';
    //         $files = reset($_FILES);
    //         // if ($this->is_log) {
    //         //     $this->seme_log->write("api_admin", 'API_Admin/crm/Chat::sendMessage FILES: '.json_encode($files));
    //         // }
    //         if (isset($files['name'][0])) {
    //             $i=0;
    //             foreach ($files['name'] as $file) {
    //                 if ($files['error'][$i]>0) {
    //                     $i++;
    //                     continue;
    //                 }
    //                 $ecam_id = $this->ecam->getLastId($nation_code, $chat_room_id, $ecm_id);
    //                 // $this->seme_log->write("api_admin", 'API_Admin/crm/Chat::sendMessage --uploadFiles: PROCESS');
    //                 $thn = date("Y");
    //                 $bln = date("m");
    //                 $ds = DIRECTORY_SEPARATOR;
    //                 $target = $this->media_chat;
    //                 if (!realpath($target)) {
    //                     mkdir($target, 0775);
    //                 }
    //                 $target = $this->media_chat.$ds.$thn;
    //                 if (!realpath($target)) {
    //                     mkdir($target, 0775);
    //                 }
    //                 $target = $this->media_chat.$ds.$thn.$ds.$bln;
    //                 if (!realpath($target)) {
    //                     mkdir($target, 0775);
    //                 }
    //                 $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
    //                 $filename = $nation_code.'-'.$chat_room_id.'-'.$ecm_id.'-'.$ecam_id.".$ext";
    //                 if (file_exists(SENEROOT.$ds.$target.$ds.$filename)) {
    //                     unlink(SENEROOT.$ds.$target.$ds.$filename);
    //                 }
    //                 $res = move_uploaded_file($files['tmp_name'][$i], $target.$ds.$filename);
    //                 if ($res) {
    //                     $dix = array();
    //                     $dix['nation_code'] = $nation_code;
    //                     $dix['e_chat_room_id'] = $chat_room_id;
    //                     $dix['e_chat_id'] = $ecm_id;
    //                     $dix['id'] = $ecam_id;
    //                     $dix['url'] = str_replace("//", "/", $target.'/'.$filename);
    //                     $dix['jenis'] = $files['type'][$i];
    //                     $dix['ukuran'] = $files['size'][$i];
    //                     $res3 = $this->ecam->set($dix);
    //                     if ($res3) {
    //                         $this->ecm->trans_commit();
    //                         // $this->seme_log->write("api_admin", 'API_Admin/crm/Chat_V2::sendMessage --uploadFilesName: '.$dix['url']);
    //                         // $this->seme_log->write("api_admin", 'API_Admin/crm/Chat::sendMessage --uploadFiles: SAVED');
    //                     }
    //                 }
    //                 $i++;
    //             }
    //         }


    //         //add attachment product
    //         $productCustomer1 = $this->input->post("productCustomer1");

    //         if($productCustomer1 != ''){
    //             $product_id = $productCustomer1;
    //         }else{
    //             $product_id = 0;
    //         }

    //         if ($product_id  > 0) {
                
    //             //get last id of attachment
    //             $last_id = $this->ecam->getLastId($nation_code, $chat_room_id, $ecm_id);

    //             //get product detail
    //             $productAttached = $this->cpm->getById($nation_code, $product_id);

    //             //directory structure
    //             $thn = date("Y");
    //             $bln = date("m");
    //             $ds = DIRECTORY_SEPARATOR;
    //             $target = $this->media_chat;
    //             if (!realpath($target)) {
    //                 mkdir($target, 0775);
    //             }
    //             $target = $this->media_chat.$ds.$thn;
    //             if (!realpath($target)) {
    //                 mkdir($target, 0775);
    //             }
    //             $target = $this->media_chat.$ds.$thn.$ds.$bln;
    //             if (!realpath($target)) {
    //                 mkdir($target, 0775);
    //             }

    //             $jenis = mime_content_type(SENEROOT.$productAttached->thumb);
    //             $ext = pathinfo(SENEROOT.$productAttached->thumb, PATHINFO_EXTENSION);
    //             $filename = $chat_room_id.'-'.$ecm_id.'-'.$last_id.'.'.$ext;
    //             if (file_exists(SENEROOT.$ds.$target.$ds.$filename)) {
    //                 unlink(SENEROOT.$ds.$target.$ds.$filename);
    //             }
    //             $res = copy(SENEROOT.$productAttached->thumb, SENEROOT.$ds.$target.$ds.$filename);
                
    //             $url = $this->media_chat.'/'.$thn.'/'.$bln.'/'.$filename;
    //             $url = str_replace("//", "/", $url);

    //             $dix = array();
    //             $dix['nation_code'] = $nation_code;
    //             $dix['e_chat_room_id'] = $chat_room_id;
    //             $dix['e_chat_id'] = $ecm_id;
    //             $dix['id'] = $last_id;
    //             $dix['jenis'] = 'product';
    //             $dix['url'] = $product_id;
    //             $dix['produk_nama'] = $productAttached->nama;
    //             $dix['produk_harga_jual'] = $productAttached->harga_jual;
    //             $dix['produk_thumb'] = $url;

    //             //insert into database
    //             $res = $this->ecam->set($dix);

    //             $this->ecm->trans_commit();
                
    //         }

    //         //add attachment order
    //         $orderBuyerCustomer1 = $this->input->post("orderBuyerCustomer1");

    //         $orderSellerCustomer1 = $this->input->post("orderSellerCustomer1");

    //         if($orderBuyerCustomer1 != ''){
    //             $order_id = $orderBuyerCustomer1;
    //         }else if($orderSellerCustomer1 != ''){
    //             $order_id = $orderSellerCustomer1;
    //         }else{
    //             $order_id = '0/0/0';
    //         }

    //         $explode = explode("/", $order_id);

    //         $order_id = $explode[0];
    //         $order_detail_id = $explode[1];
    //         $order_detail_item_id = $explode[2];

    //         if ($order_id > 0 && $order_detail_id > 0 && $order_detail_item_id > 0) {
                
    //             //get last id of attachment
    //             $last_id = $this->ecam->getLastId($nation_code, $chat_room_id, $ecm_id);

    //             $dix = array();
    //             $dix['nation_code'] = $nation_code;
    //             $dix['e_chat_room_id'] = $chat_room_id;
    //             $dix['e_chat_id'] = $ecm_id;
    //             $dix['id'] = $last_id;
    //             $dix['jenis'] = 'order';
    //             $dix['url'] = $order_id;
    //             $dix['order_detail_id'] = $order_detail_id;
    //             $dix['order_detail_item_id'] = $order_detail_item_id;

    //             //insert into database
    //             $res = $this->ecam->set($dix);

    //             $this->ecm->trans_commit();
                
    //         }

    //         //building notification dataset
    //         $type = 'chat';
    //         $anotid = 2;
    //         $replacer = array();

    //         //buyer
    //         $buyer = new stdClass();
    //         $seller = new stdClass();

    //         //by Donny Dennison - 19 october 2020 14:51
    //         //add user setting chat notif
    //         $classified = 'setting_notification_user';
    //         $code = 'U4';

    //         if($chat_type=="ALL"){
                
    //             //push notif for customer 1
    //             $customer1 = $this->bum->getById($nation_code, $chatRoom->b_user_id_1);

    //             $customer1SettingNotif = $this->busm->getValue($nation_code, $chatRoom->b_user_id_1, $classified, $code);

    //             if (!isset($customer1SettingNotif->setting_value)) {
    //                 $customer1SettingNotif->setting_value = 0;
    //             }

    //             //push notif for seller
    //             $customer2 = $this->bum->getById($nation_code, $chatRoom->b_user_id_2);

    //             //by Donny Dennison - 19 october 2020 14:51
    //             //add user setting chat notif
    //             $customer2SettingNotif = $this->busm->getValue($nation_code, $chatRoom->b_user_id_2, $classified, $code);

    //             if (!isset($customer2SettingNotif->setting_value)) {
    //                 $customer2SettingNotif->setting_value = 0;
    //             }

    //             if ($customer1SettingNotif->setting_value == 1) {

    //                 if (strlen($customer1->fcm_token)>50) {

    //                     $device = $customer1->device; //jenis device
    //                     $tokens = array($customer1->fcm_token); //device token
    //                     $title = 'Obrolan Baru';
    //                     $message = "Anda memiliki pesan baru dari Administrator. Balas sekarang";
    //                     $image = 'media/pemberitahuan/chat.png';
    //                     $payload = new stdClass();
    //                     $payload->chat_room_id = $chatRoom->id;
    //                     $payload->user_id = $customer2->id;
    //                     $payload->user_fnama = $customer2->fnama;
    //                     $payload->user_image = $this->cdn_url($customer2->image);
    //                     $payload->chat_type = $chat_type;

    //                     $nw = $this->anot->get($nation_code, "push", $type, $anotid);
    //                     if (isset($nw->title)) {
    //                         $title = $nw->title;
    //                     }
    //                     if (isset($nw->message)) {
    //                         $message = $this->__nRep($nw->message, $replacer);
    //                     }
    //                     if (isset($nw->image)) {
    //                         $image = $nw->image;
    //                     }
    //                     $image = $this->cdn_url($image);
    //                     $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
    //                     // if ($this->is_log) {
    //                     //     $this->seme_log->write("api_admin", 'API_Admin/CRM/Chat::sendMessage -> __pushNotifBuyer: '.json_encode($res));
    //                     // }
    //                 }
    //             }

    //             if ($customer2SettingNotif->setting_value == 1) {

    //                 if (strlen($customer2->fcm_token)>50) {

    //                     $device = $customer2->device; //jenis device
    //                     $tokens = array($customer2->fcm_token); //device token
    //                     $title = 'Obrolan Baru';
    //                     $message = "Anda memiliki pesan baru dari Administrator. Balas sekarang";
    //                     $image = 'media/pemberitahuan/chat.png';
    //                     $payload = new stdClass();
    //                     $payload->chat_room_id = $chatRoom->id;
    //                     $payload->user_id = $customer1->id;
    //                     $payload->user_fnama = $customer1->fnama;
    //                     $payload->user_image = $this->cdn_url($customer1->image);
    //                     $payload->chat_type = $chat_type;
                        
    //                     $nw = $this->anot->get($nation_code, "push", $type, $anotid);
    //                     if (isset($nw->title)) {
    //                         $title = $nw->title;
    //                     }
    //                     if (isset($nw->message)) {
    //                         $message = $this->__nRep($nw->message, $replacer);
    //                     }
    //                     if (isset($nw->image)) {
    //                         $image = $nw->image;
    //                     }
    //                     $image = $this->cdn_url($image);
    //                     $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
    //                     // if ($this->is_log) {
    //                     //     $this->seme_log->write("api_admin", 'API_Admin/CRM/Chat::sendMessage -> __pushNotifSeller -> Type: '.$type.', message: '.$message);
    //                     // }
    //                     //if($this->is_log) $this->seme_log->write("api_admin", 'API_Admin/CRM/Chat::sendMessage -> __pushNotifSeller payload: '.json_encode($payload));
    //                     //if($this->is_log) $this->seme_log->write("api_admin", 'API_Admin/CRM/Chat::sendMessage -> __pushNotifSeller payload: '.json_encode($payload));
    //                 }
    //             }
    //         }

    //         if($chat_type=="ADMIN")
    //         {   
    //             //push notif for seller
    //             $customer2 = $this->bum->getById($nation_code, $chatRoom->b_user_id_2);

    //             //by Donny Dennison - 19 october 2020 14:51
    //             //add user setting chat notif
    //             $customer2SettingNotif = $this->busm->getValue($nation_code, $chatRoom->b_user_id_2, $classified, $code);

    //             if (!isset($customer2SettingNotif->setting_value)) {
    //                 $customer2SettingNotif->setting_value = 0;
    //             }
                
    //             if ($customer2SettingNotif->setting_value == 1) {

    //                 if (strlen($customer2->fcm_token)>50) {

    //                     $device = $customer2->device; //jenis device
    //                     $tokens = array($customer2->fcm_token); //device token
    //                     $title = 'Obrolan Baru';
    //                     $message = "Anda memiliki pesan baru dari Administrator. Balas sekarang";
    //                     $image = 'media/pemberitahuan/chat.png';
    //                     $payload = new stdClass();
    //                     $payload->chat_room_id = $chatRoom->id;
    //                     $payload->user_id = 0;
    //                     $payload->user_fnama = '';
    //                     $payload->user_image = '';
    //                     $payload->chat_type = $chat_type;
                        
    //                     $nw = $this->anot->get($nation_code, "push", $type, $anotid);
    //                     if (isset($nw->title)) {
    //                         $title = $nw->title;
    //                     }
    //                     if (isset($nw->message)) {
    //                         $message = $this->__nRep($nw->message, $replacer);
    //                     }
    //                     if (isset($nw->image)) {
    //                         $image = $nw->image;
    //                     }
    //                     $image = $this->cdn_url($image);
    //                     $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
    //                     // if ($this->is_log) {
    //                     //     $this->seme_log->write("api_admin", 'API_Admin/CRM/Chat::sendMessage -> __pushNotifBuyer: '.json_encode($res));
    //                     // }
    //                 }
    //             }
    //         }

    //     } else {
    //         $this->status = 4004;
    //         $this->message = 'Failed add chat to database';
    //         $this->ecm->trans_rollback();
    //     }

    //     //close transaction
    //     $this->ecm->trans_end();

    //     $this->__json_out($data);
    // }

    // public function cancel($nation_code, $d_order_id, $d_order_detail_id, $c_produk_id)
    // {
    //     $d = $this->__init();
    //     $data = array();
    //     if (!$this->admin_login) {
    //         $this->status = 400;
    //         $this->message = 'Unauthorized access';
    //         header("HTTP/1.0 400 Unauthorized");
    //         $this->__json_out($data);
    //         die();
    //     }
    //     $pengguna = $d['sess']->admin;
    //     //$nation_code = $d['sess']->admin->nation_code;

    //     //validating
    //     $d_order_id = (int) $d_order_id;
    //     if ($d_order_id<=0) {
    //         $this->status = 4040;
    //         $this->message = 'Invalid D_ORDER_ID';
    //         $this->__json_out($data);
    //         die();
    //     }
    //     $d_order_detail_id = (int) $d_order_detail_id;
    //     if ($d_order_detail_id<=0) {
    //         $this->status = 4041;
    //         $this->message = 'Invalid d_order_detail_id';
    //         $this->__json_out($data);
    //         die();
    //     }
    //     $c_produk_id = (int) $c_produk_id;
    //     if ($c_produk_id<=0) {
    //         $this->status = 4042;
    //         $this->message = 'Invalid c_produk_id';
    //         $this->__json_out($data);
    //         die();
    //     }
    //     $this->status = 200;
    //     $this->message = 'Success';
    //     $this->complain->del($nation_code, $d_order_id, $d_order_detail_id, $c_produk_id);
    //     $_POST=array();
    //     $_POST['d_order_id'] = $d_order_id;
    //     $_POST['c_produk_id'] = $d_order_detail_id;
    //     $_POST['message'] = 'Administrator has cancelled this complain.';
    //     $this->sendMessage();
    // }
    // public function solved_to_buyer($nation_code, $d_order_id, $d_order_detail_id, $c_produk_id)
    // {
    //     $d = $this->__init();
    //     $data = array();
    //     if (!$this->admin_login) {
    //         $this->status = 400;
    //         $this->message = 'Unauthorized access';
    //         header("HTTP/1.0 400 Unauthorized");
    //         $this->__json_out($data);
    //         die();
    //     }
    //     $pengguna = $d['sess']->admin;
    //     //$nation_code = $d['sess']->admin->nation_code;

    //     //validating
    //     $d_order_id = (int) $d_order_id;
    //     if ($d_order_id<=0) {
    //         $this->status = 4051;
    //         $this->message = 'Invalid D_ORDER_ID';
    //         $this->__json_out($data);
    //         die();
    //     }
    //     $d_order_detail_id = (int) $d_order_detail_id;
    //     if ($d_order_detail_id<=0) {
    //         $this->status = 4052;
    //         $this->message = 'Invalid d_order_detail_id';
    //         $this->__json_out($data);
    //         die();
    //     }
    //     $c_produk_id = (int) $c_produk_id;
    //     if ($c_produk_id<=0) {
    //         $this->status = 4053;
    //         $this->message = 'Invalid c_produk_id';
    //         $this->__json_out($data);
    //         die();
    //     }
    //     $this->status = 200;
    //     $this->message = 'Success';
    //     $this->complain->del($nation_code, $d_order_id, $d_order_detail_id, $c_produk_id);

    //     $du = array();
    //     $du['settlement_status'] = "solved_to_buyer";
    //     $this->dodim->update($nation_code, $d_order_id, $d_order_detail_id, $c_produk_id, $du);

    //     $du = array();
    //     $du['is_calculated'] = "0";
    //     $this->dodm->update($nation_code, $d_order_id, $d_order_detail_id, $du);

    //     $_POST=array();
    //     $_POST['d_order_id'] = $d_order_id;
    //     $_POST['c_produk_id'] = $d_order_detail_id;
    //     $_POST['message'] = 'Administrator has solved this complain to buyer. This complained product will be transfered to buyer soon.';
    //     $this->sendMessage();
    // }
    // public function solved_to_seller($nation_code, $d_order_id, $d_order_detail_id, $c_produk_id)
    // {
    //     $d = $this->__init();
    //     $data = array();
    //     if (!$this->admin_login) {
    //         $this->status = 400;
    //         $this->message = 'Unauthorized access';
    //         header("HTTP/1.0 400 Unauthorized");
    //         $this->__json_out($data);
    //         die();
    //     }
    //     $pengguna = $d['sess']->admin;
    //     //$nation_code = $d['sess']->admin->nation_code;

    //     //validating
    //     $d_order_id = (int) $d_order_id;
    //     if ($d_order_id<=0) {
    //         $this->status = 4031;
    //         $this->message = 'Invalid D_ORDER_ID';
    //         $this->__json_out($data);
    //         die();
    //     }
    //     $d_order_detail_id = (int) $d_order_detail_id;
    //     if ($d_order_detail_id<=0) {
    //         $this->status = 4032;
    //         $this->message = 'Invalid d_order_detail_id';
    //         $this->__json_out($data);
    //         die();
    //     }
    //     $c_produk_id = (int) $c_produk_id;
    //     if ($c_produk_id<=0) {
    //         $this->status = 4033;
    //         $this->message = 'Invalid c_produk_id';
    //         $this->__json_out($data);
    //         die();
    //     }
    //     $this->status = 200;
    //     $this->message = 'Success';
    //     $this->complain->del($nation_code, $d_order_id, $d_order_detail_id, $c_produk_id);

    //     $du = array();
    //     $du['settlement_status'] = "solved_to_seller";
    //     $this->dodim->update($nation_code, $d_order_id, $d_order_detail_id, $c_produk_id, $du);

    //     $du = array();
    //     $du['is_calculated'] = "0";
    //     $this->dodm->update($nation_code, $d_order_id, $d_order_detail_id, $du);

    //     $_POST=array();
    //     $_POST['d_order_id'] = $d_order_id;
    //     $_POST['c_produk_id'] = $d_order_detail_id;
    //     $_POST['message'] = 'Administrator has solved this complain to seller. '.$this->site_name.' will transfered money to seller soon.';
    //     $this->sendMessage();
    // }
}
