<?php
class Block extends JI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->lib("seme_log");
    $this->load("api_mobile/b_user_model", "bu");
    $this->load("api_mobile/b_user_follow_model", 'buf');
    $this->load("api_mobile/c_block_model", "cbm");
    // $this->load("api_mobile/common_code_model", "ccm");
    $this->load("api_mobile/c_community_model", "ccomm");
    $this->load("api_mobile/c_community_attachment_model", "ccam");
    $this->load("api_mobile/e_chat_room_model", 'ecrm');
    $this->load("api_mobile/e_chat_participant_model", 'ecpm');
    $this->load("api_mobile/b_user_report_model", 'burm');
    $this->load("api_mobile/c_community_report_model", 'ccrm');

    //by Donny Dennison - 7 november 2022 14:17
    //new feature, block community post or account
    $this->load("api_mobile/e_chat_model", 'chat');
    $this->load("api_mobile/e_chat_read_model", 'ecreadm');

    //by Donny Dennison - 08 november 2022 11:03
    //new feature, block product
    $this->load("api_mobile/c_produk_model", "cpm");

  }

  private function __sortCol($sort_col, $tbl_as)
  {
    switch ($sort_col) {
      case 'cdate':
      $sort_col = "$tbl_as.cdate";
      break;
      default:
      $sort_col = "$tbl_as.cdate";
    }
    return $sort_col;
  }

  private function __sortDir($sort_dir)
  {
    $sort_dir = strtolower($sort_dir);
    if ($sort_dir == "desc") {
      $sort_dir = "DESC";
    } else {
      $sort_dir = "ASC";
    }
    return $sort_dir;
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

  public function index()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['total'] = '0';
    $data['blocks'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "block");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "block");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "block");
      die();
    }

    $timezone = $this->input->get('timezone');
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $type = $this->input->get("type");
    if (strlen($type)<=0 || empty($type)){
      $type = "community";
    }

    $query_type = $this->input->get("query_type");
    if (strlen($query_type)<=0 || empty($query_type)){
      $query_type = "block";
    }

    // $sort_col = $this->input->get("sort_col");
    // $sort_dir = $this->input->get("sort_dir");
    $sort_col = "cdate";
    $sort_dir = "desc";
    $page = $this->input->get("page");
    $page_size = $this->input->get("page_size");

    //sanitize input
    if($query_type == "block"){
      $tbl_as = $this->cbm->getTblAs();
      $sort_col = $this->__sortCol($sort_col, $tbl_as);
    }else{
      if($type == "community"){
        $tbl_as = $this->ccrm->getTblAs();
        $sort_col = $this->__sortCol($sort_col, $tbl_as);
      }else{
        $tbl_as = $this->burm->getTblAs();
        $sort_col = $this->__sortCol($sort_col, $tbl_as);
      }
    }

    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

    if($query_type == "block"){
      $data['total'] = $this->cbm->countAll($nation_code, $pelanggan->id, $type);

      $data['blocks'] = $this->cbm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $pelanggan->id, $type);
    }else{
      if($type == "community"){
        $data['total'] = $this->ccrm->countAll($nation_code, $pelanggan->id);

        $data['blocks'] = $this->ccrm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $pelanggan->id);
      }else{
        $data['total'] = $this->burm->countAll($nation_code, $pelanggan->id);

        $data['blocks'] = $this->burm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $pelanggan->id);
      }
    }

    //manipulating data
    foreach ($data['blocks'] as &$pd) {
      if($pd->type == "community"){
        $pd->customData = $this->ccomm->getById($nation_code, $pd->custom_id, $pelanggan, $pelanggan->language_id);

        $pd->customData->can_chat_and_like = "0";
        // if(isset($pelanggan->id) && isset($pelangganAddress2->alamat2)){
        if(isset($pelanggan->id)){
          // }else if($pd->customData->postal_district == $pelangganAddress2->postal_district){
            $pd->customData->can_chat_and_like = "1";
          // }
        }

        $pd->customData->is_owner_post = "0";
        if(isset($pelanggan->id)){
          if($pd->customData->b_user_id_starter == $pelanggan->id){
            $pd->customData->is_owner_post = "1";
          }
        }

        // $pd->customData->cdate_text = $this->humanTiming($pd->customData->cdate);
        $pd->customData->cdate_text = $this->humanTiming($pd->customData->cdate, null, $pelanggan->language_id);

        $pd->customData->cdate = $this->customTimezone($pd->customData->cdate, $timezone);

        //convert to utf friendly
        // if (isset($pd->customData->title)) {
        //   $pd->customData->title = $this->__dconv($pd->customData->title);
        // }
        $pd->customData->title = html_entity_decode($pd->customData->title,ENT_QUOTES);
        $pd->customData->deskripsi = html_entity_decode($pd->customData->deskripsi,ENT_QUOTES);

        if (isset($pd->customData->b_user_image_starter)) {
          if (empty($pd->customData->b_user_image_starter)) {
            $pd->customData->b_user_image_starter = 'media/produk/default.png';
          }

          if(file_exists(SENEROOT.$pd->customData->b_user_image_starter) && $pd->customData->b_user_image_starter != 'media/user/default.png'){
            $pd->customData->b_user_image_starter = $this->cdn_url($pd->customData->b_user_image_starter);
          } else {
            $pd->customData->b_user_image_starter = $this->cdn_url('media/user/default-profile-picture.png');
          }
        }

        if($pd->customData->top_like_image_1 > 0){
          $pd->customData->top_like_image_1 = $this->cdn_url("media/icon/like-62-1-141111.png");
        }

        $pd->customData->images = array();
        $pd->customData->locations = array();
        $pd->customData->videos = array();

        $attachments = $this->ccam->getByCommunityId($nation_code, $pd->customData->id);
        foreach ($attachments as $atc) {
          if($atc->jenis == 'image'){
            if (empty($atc->url)) {
              $atc->url = 'media/community_default.png';
            }
            if (empty($atc->url_thumb)) {
              $atc->url_thumb = 'media/community_default.png';
            }

            $atc->url = $this->cdn_url($atc->url);
            $atc->url_thumb = $this->cdn_url($atc->url_thumb);

            $pd->customData->images[] = $atc;
          }else if($atc->jenis == 'video'){
            $atc->url = $this->cdn_url($atc->url);
            $atc->url_thumb = $this->cdn_url($atc->url_thumb);

            $pd->customData->videos[] = $atc;
          }else{
            $pd->customData->locations[] = $atc;
          }
        }
        unset($attachments,$atc);
      }

      if($pd->type == "account"){
        $pd->customData = $this->bu->getById($nation_code, $pd->custom_id);

        if(file_exists(SENEROOT.$pd->customData->image) && $pd->customData->image != 'media/user/default.png'){
            $pd->customData->image = str_replace("//", "/", $this->cdn_url($pd->customData->image));
        }else{
            $pd->customData->image = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
        }

        if(file_exists(SENEROOT.$pd->customData->image_banner)){
            $pd->customData->image_banner = str_replace("//", "/", $this->cdn_url($pd->customData->image_banner));
        }else{
            $pd->customData->image_banner = str_replace("//", "/", $this->cdn_url('media/user/default.png'));
        }

        unset($pd->customData->fb_id);
        unset($pd->customData->apple_id);
        unset($pd->customData->google_id);
        unset($pd->customData->password);
        unset($pd->customData->latitude);
        unset($pd->customData->longitude);
        unset($pd->customData->kelamin);
        unset($pd->customData->bdate);
        unset($pd->customData->cdate);
        unset($pd->customData->adate);
        unset($pd->customData->edate);
        unset($pd->customData->telp);
        unset($pd->customData->intro_teks);
        unset($pd->customData->api_social_id);
        unset($pd->customData->fcm_token);
        unset($pd->customData->device);
        unset($pd->customData->is_agree);
        unset($pd->customData->is_confirmed);
        // unset($pd->customData->is_active);
        unset($pd->customData->telp_is_verif);
        unset($pd->customData->api_mobile_edate);
        unset($pd->customData->is_reset_password);
        unset($pd->customData->api_web_token);
        unset($pd->customData->api_mobile_token);
        unset($pd->customData->api_reg_token);
      }

      if($pd->type == "product"){
        $getProductType = $this->cpm->getProductType($nation_code, $pd->custom_id);
        $getProductType = $getProductType->product_type;

        $pd->customData = $this->cpm->getById($nation_code, $pd->custom_id, $pelanggan, $getProductType);

        $pd->customData->nama = html_entity_decode($pd->customData->nama,ENT_QUOTES);

        $pd->customData->thumb = $this->cdn_url($pd->customData->thumb);
      }
    }

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "block");
  }

  public function baru()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "block");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "block");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "block");
      die();
    }

    //collect product input
    $custom_id = trim($this->input->post('custom_id'));
    $type = trim($this->input->post('type'));
    if (strlen($type)<=0 || empty($type)){
      $type = "community";
    }

    if($type == "community"){
      $community = $this->ccomm->getById($nation_code, $custom_id, $pelanggan, $pelanggan->language_id);
      if (!isset($community->id)) {
        $this->status = 1160;
        $this->message = 'This post is deleted by an author';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
        die();
      }
    }else if($type == "product"){
      $getProductType = $this->cpm->getProductType($nation_code, $custom_id);
      if(!isset($getProductType->product_type)){
        $this->status = 595;
        $this->message = 'Invalid product ID or Product not found';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
        die();
      }

      $getProductType = $getProductType->product_type;

      $produk = $this->cpm->getById($nation_code, $custom_id, $pelanggan, $getProductType, $pelanggan->language_id);
      if (!isset($produk->id)) {
        $this->status = 595;
        $this->message = 'Invalid product ID or Product not found';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
        die();
      }
    }else{
      $userData = $this->bu->getById($nation_code, $custom_id);
      if (!isset($userData->id)) {
        $this->status = 1001;
        $this->message = 'Missing or invalid b_user_id';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
        die();
      }
    }

    $blockData = $this->cbm->getById($nation_code, 0, $pelanggan->id, $type, $custom_id);
    if (isset($blockData->block_id)) {
      $this->status = 596;
      $this->message = 'Already in block list';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "block");
      die();
    }

    //start transaction and lock table
    // $this->cbm->trans_start();

    //get last id for first time
    $cbm_id = $this->cbm->getLastId($nation_code, $type);

    //initial insert with latest ID
    $di = array();
    $di['nation_code'] = $nation_code;
    $di['id'] = $cbm_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['custom_id'] = $custom_id;
    $di['type'] = $type;
    $di['cdate'] = 'NOW()';
    $res = $this->cbm->set($di);
    if (!$res) {
      // $this->cbm->trans_rollback();
      // $this->cbm->trans_end();
      $this->status = 1105;
      $this->message = "Error while insert, please try again later";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "block");
      die();
    }

    // $this->cbm->trans_commit();

    if($type == "account"){
      $di = array();
      $di['is_active'] = 0;

      //insert into database
      $this->buf->update($nation_code, $pelanggan->id, $custom_id, $di);
      // $this->cbm->trans_commit();

      $this->buf->update($nation_code, $custom_id, $pelanggan->id, $di);
      // $this->cbm->trans_commit();

      // $this->ecrm->getChatRoomByCommunityID($nation_code, $custom_id);

      //START by Donny Dennison - 7 november 2022 14:17
      //new feature, block community post or account
      $listOffering = $this->ecrm->getAllByBuyerSeller($nation_code, "offer", $custom_id, $pelanggan->id, "offering");
      foreach($listOffering as $offering){
        $roomChatOfferingparticipant = $this->ecpm->getParticipantByRoomChatId($nation_code, $offering->id);

        $message = $this->chat->getLastOfferByChatRoomId($nation_code, $offering->id, "offering")->message;

        $last_id = $this->chat->getLastId($nation_code, $offering->id);
        $di = array();
        $di['id'] = $last_id;
        $di['nation_code'] = $nation_code;
        $di['e_chat_room_id'] = $offering->id;
        $di['b_user_id'] = $offering->b_user_id_seller;
        $di['type'] = "rejected";
        $di['message'] = $message;
        $di['message_indonesia'] = $message;
        $di['cdate'] = "NOW()";
        $this->chat->set($di);
        // $this->cbm->trans_commit();

        //set unread for admin
        $du = array();
        $du['is_read_admin'] = 0;
        $du['offer_status'] = "rejected";
        $du['offer_status_update_date'] = "NOW()";
        $this->ecrm->update($nation_code, $offering->id, $du);
        // $this->cbm->trans_commit();

        //set unread for other chat participant
        $du = array();
        $du['is_read'] = 0;
        $this->ecpm->updateUnread($nation_code, $offering->id, $offering->b_user_id_seller, $du);
        // $this->cbm->trans_commit();

        //set unread in table e_chat_read
        foreach($roomChatOfferingparticipant AS $participant){
          $du = array();
          $du['nation_code'] = $nation_code;
          $du['b_user_id'] = $participant->b_user_id;
          $du['e_chat_room_id'] = $offering->id;
          $du['e_chat_id'] = $last_id;
          if($participant->b_user_id == $offering->b_user_id_seller){
              $du['is_read'] = 1;
          }
          $du['cdate'] = "NOW()";
          $this->ecreadm->set($du);
          // $this->cbm->trans_commit();
        }
        unset($participant);

      }
      unset($listOffering, $offering);

      $listOffering = $this->ecrm->getAllByBuyerSeller($nation_code, "offer", $pelanggan->id, $custom_id, "offering");
      foreach($listOffering as $offering){
        $roomChatOfferingparticipant = $this->ecpm->getParticipantByRoomChatId($nation_code, $offering->id);

        $message = $this->chat->getLastOfferByChatRoomId($nation_code, $offering->id, "offering")->message;

        $last_id = $this->chat->getLastId($nation_code, $offering->id);
        $di = array();
        $di['id'] = $last_id;
        $di['nation_code'] = $nation_code;
        $di['e_chat_room_id'] = $offering->id;
        $di['b_user_id'] = $offering->b_user_id_seller;
        $di['type'] = "cancelled";
        $di['message'] = $message;
        $di['message_indonesia'] = $message;
        $di['cdate'] = "NOW()";
        $this->chat->set($di);
        // $this->cbm->trans_commit();

        //set unread for admin
        $du = array();
        $du['is_read_admin'] = 0;
        $du['offer_status'] = "cancelled";
        $du['offer_status_update_date'] = "NOW()";
        $this->ecrm->update($nation_code, $offering->id, $du);
        // $this->cbm->trans_commit();

        //set unread for other chat participant
        $du = array();
        $du['is_read'] = 0;
        $this->ecpm->updateUnread($nation_code, $offering->id, $offering->b_user_id_seller, $du);
        // $this->cbm->trans_commit();

        //set unread in table e_chat_read
        foreach($roomChatOfferingparticipant AS $participant){
          $du = array();
          $du['nation_code'] = $nation_code;
          $du['b_user_id'] = $participant->b_user_id;
          $du['e_chat_room_id'] = $offering->id;
          $du['e_chat_id'] = $last_id;
          if($participant->b_user_id == $offering->b_user_id_seller){
              $du['is_read'] = 1;
          }
          $du['cdate'] = "NOW()";
          $this->ecreadm->set($du);
          // $this->cbm->trans_commit();
        }
        unset($participant);
      }
      unset($listOffering, $offering);
      //END by Donny Dennison - 7 november 2022 14:17
      //new feature, block community post or account
    }

    $this->status = 200;
    $this->message = "Success";

    // $this->cbm->trans_end();

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "block");
  }

  public function hapus()
  {
    $dt = $this->__init();
    $data = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "block");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "block");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "block");
      die();
    }

    $block_id = trim($this->input->post('block_id'));
    $type = trim($this->input->post('type'));

    if (strlen($type)<=0 || empty($type)){
      $type = "community";
    }

    $blockData = $this->cbm->getById($nation_code, $block_id, $pelanggan->id, $type);
    if (!isset($blockData->block_id)) {
      $this->status = 595;
      $this->message = 'Invalid block ID or Block not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "block");
      die();
    }

    //start transaction
    $this->cbm->trans_start();

    $du = array();
    $du['is_active'] = 0;
    $res = $this->cbm->update($nation_code, $block_id, $type, $du);
    if ($res) {
      $this->cbm->trans_commit();
      $this->status = 200;
      $this->message = 'Success';
    } else {
      $this->cbm->trans_rollback();
      $this->status = 940;
      $this->message = "Can't delete products from database, please try again later";
    }

    //finish transaction
    $this->cbm->trans_end();

    //render output
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "block");
  }
}
