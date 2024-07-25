<?php
class Group extends JI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->lib("seme_log");
    $this->lib("phpqrcode/phpqrcode", 'QRcode', "inc");
    $this->load("api_mobile/b_user_model", "bu");
    $this->load("api_mobile/group/i_group_category_model", "igcm");
    $this->load("api_mobile/group/i_group_default_image_model", "igdim");
    $this->load("api_mobile/group/i_group_model", "igm");
    $this->load("api_mobile/group/i_group_participant_model", "igparticipantm");
    $this->load("api_mobile/group/i_chat_room_model", 'icrm');
    $this->load("api_mobile/group/i_chat_participant_model", "icpm");
    $this->load("api_mobile/group/i_chat_model", 'icm');
    $this->load("api_mobile/group/i_chat_read_model", 'icreadm');
    $this->load("api_mobile/group/i_group_request_share_model", 'igrsm');
    $this->load("api_mobile/group/i_group_attachment_model", 'igam');
    $this->load("api_mobile/group/i_group_admin_activity_log_model", 'igaalm');
    $this->load("api_mobile/group/i_group_pin_model", 'igpinm');
    $this->load("api_mobile/group/i_group_notifications_model", "ignotifm");
    $this->load("api_mobile/group/i_group_post_model", "igpostm");
    $this->load("api_mobile/common_code_model", "ccm");
    $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
    $this->load("api_mobile/group/i_group_participant_history_model", "igphm");
    $this->load("api_mobile/group/i_group_home_list_model", "ighlm");
    $this->load("api_mobile/group/i_group_home_detail_model", "ighdm");
    $this->load("api_mobile/g_daily_track_record_model", 'gdtrm');
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

  // private function __checkUploadedFile($keyname){
  //   if (isset($_FILES[$keyname]['name'])) {
      
  //     //by Donny Dennison - 5 august 2020 1:42
  //     //fix upload image problem product
  //     // if(strlen($_FILES[$keyname]['tmp_name'])>4 && strlen($_FILES[$keyname]['size'])>4){
  //     if(strlen($_FILES[$keyname]['tmp_name'])>4 && strlen($_FILES[$keyname]['size'])>3){

  //       $this->seme_log->write("api_mobile", 'API_Mobile/Community::__checkUploadedFile -- INFO keyname: '.$keyname.' RESULT: TRUE');
  //       return true;
  //     }
  //   }
  //   $this->seme_log->write("api_mobile", 'API_Mobile/Community::__checkUploadedFile -- INFO keyname: '.$keyname.' RESULT: FALSE');
  //   return false;
  // }

  private function __moveImagex($nation_code, $url, $targetdir, $produk_id="0", $ke="")
  {
    $sc = new stdClass();
    $sc->status = 500;
    $sc->message = 'Error';
    $sc->image = '';
    $sc->thumb = '';
    // $produk_id = (int) $produk_id;

    // $targetdir = $this->media_community;
    $targetdircheck = realpath(SENEROOT.$targetdir);
    if (empty($targetdircheck)) {
      if (PHP_OS == "WINNT") {
        if (!is_dir(SENEROOT.$targetdir)) {
          mkdir(SENEROOT.$targetdir);
        }
      } else {
        if (!is_dir(SENEROOT.$targetdir)) {
          mkdir(SENEROOT.$targetdir, 0775);
        }
      }
    }

    $tahun = date("Y");
    $targetdir = $targetdir.DIRECTORY_SEPARATOR.$tahun;
    $targetdircheck = realpath(SENEROOT.$targetdir);
    if (empty($targetdircheck)) {
      if (PHP_OS == "WINNT") {
        if (!is_dir(SENEROOT.$targetdir)) {
          mkdir(SENEROOT.$targetdir);
        }
      } else {
        if (!is_dir(SENEROOT.$targetdir)) {
          mkdir(SENEROOT.$targetdir, 0775);
        }
      }
    }

    $bulan = date("m");
    $targetdir = $targetdir.DIRECTORY_SEPARATOR.$bulan;
    $targetdircheck = realpath(SENEROOT.$targetdir);
    if (empty($targetdircheck)) {
      if (PHP_OS == "WINNT") {
        if (!is_dir(SENEROOT.$targetdir)) {
          mkdir(SENEROOT.$targetdir);
        }
      } else {
        if (!is_dir(SENEROOT.$targetdir)) {
          mkdir(SENEROOT.$targetdir, 0775);
        }
      }
    }

    $file_path = SENEROOT.parse_url($url, PHP_URL_PATH);
    if (file_exists($file_path) && is_file($file_path)) {  
      $file_path_thumb = parse_url($url, PHP_URL_PATH);
      $extension = pathinfo($file_path_thumb, PATHINFO_EXTENSION);
      $file_path_thumb = substr($file_path_thumb,0,strripos($file_path_thumb,'.'));
      $file_path_thumb = SENEROOT.$file_path_thumb.'-thumb.'.$extension;

      $filename = "$nation_code-$produk_id-$ke-".date('YmdHis');
      $filethumb = $filename."-thumb.".$extension;
      $filename = $filename.".".$extension;

      rename($file_path, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
      rename($file_path_thumb, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);

      $sc->status = 200;
      $sc->message = 'Success';
      $sc->thumb = str_replace("//", "/", $targetdir.'/'.$filethumb);
      $sc->image = str_replace("//", "/", $targetdir.'/'.$filename);
    } else {
      $sc->status = 997;
      $sc->message = 'Failed';
    }

    // $this->seme_log->write("api_mobile", 'API_Mobile/Group/Group::__moveImagex -- INFO URL: '.$url.' PID:'.$produk_id.' ke:'.$ke.' '.$sc->status.' '.$sc->message.'');
    return $sc;
  }

  //by Donny Dennison - 11 august 2022 10:46
  //fix rotated image after resize(thumb)
  //credit: https://stackoverflow.com/a/18919355/7578520
  // private function correctImageOrientation($filename) {
  //   //credit: https://github.com/FriendsOfCake/cakephp-upload/issues/221#issuecomment-50128062
  //   $exif = false;
  //   $size = getimagesize($filename, $info);
  //   if (!isset($info["APP13"])) {
  //     if (function_exists('exif_read_data')) {
  //       $exif = exif_read_data($filename);
  //       if($exif && isset($exif['Orientation'])) {
  //         $orientation = $exif['Orientation'];
  //         if($orientation != 1){
  //           $img = imagecreatefromjpeg($filename);
  //           $deg = 0;
  //           switch ($orientation) {
  //             case 3:
  //               $deg = 180;
  //               break;
  //             case 6:
  //               $deg = 270;
  //               break;
  //             case 8:
  //               $deg = 90;
  //               break;
  //           }
  //           if ($deg) {
  //             $img = imagerotate($img, $deg, 0);        
  //           }
  //           // then rewrite the rotated image back to the disk as $filename
  //           imagejpeg($img, $filename, 95);
  //         } // if there is some rotation necessary
  //       } // if have the exif orientation info
  //     } // if function exists
  //   }
  // }

  private function __statusMember($nation_code, $i_group_id, $b_user_id="0"){
    $status_member = "not_member";
    if($b_user_id == "0"){
      return $status_member;
    }
    $checkStatus = $this->igparticipantm->getStatus($nation_code, $i_group_id, $b_user_id);
    if(isset($checkStatus->i_group_id)){
      if($checkStatus->is_owner == "1" && $checkStatus->is_co_admin == "0" && $checkStatus->is_accept == "1") {
        $status_member = "Owner";
      } else if($checkStatus->is_owner == "0" && $checkStatus->is_co_admin == "1" && $checkStatus->is_accept == "1") {
        $status_member = "Admin";
      } else if($checkStatus->is_owner == "0" && $checkStatus->is_co_admin == "0" && $checkStatus->is_accept == "1") {
        $status_member = "Member";
      }else if($checkStatus->is_accept == "0" && $checkStatus->is_request == "1"){
        $status_member = "requested_join"; 
      }
    }
    return $status_member;
  }

  private function give_spt($nation_code, $type, $b_user_id, $custom_id)
  {
    if($type == "inviter"){
      $pointCode = "E28";
      $custom_type_sub = "invite member join club";
    }else{
      $pointCode = "E29";
      $custom_type_sub = "member join club";
    }
    $pelanggan = $this->bu->getById($nation_code, $b_user_id);

    //get point
    $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $pointCode);
    // if (!isset($pointGet->remark)) {
    //   $pointGet = new stdClass();
    //   $pointGet->remark = 100;
    // }

    $di = array();
    $di['nation_code'] = $nation_code;
    $di['b_user_alamat_location_kelurahan'] = "All";
    $di['b_user_alamat_location_kecamatan'] = "All";
    $di['b_user_alamat_location_kabkota'] = "All";
    $di['b_user_alamat_location_provinsi'] = "All";
    $di['b_user_id'] = $b_user_id;
    $di['point'] = $pointGet->remark;
    $di['custom_id'] = $custom_id;
    $di['custom_type'] = 'club';
    $di['custom_type_sub'] = $custom_type_sub;
    $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' and  get '.$di['point'].' point(s)';
    $endDoWhile = 0;
    do{
      $leaderBoardHistoryId = $this->GUIDv4();
      $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
      if($checkId == 0){
        $endDoWhile = 1;
      }
    }while($endDoWhile == 0);
    $di['id'] = $leaderBoardHistoryId;
    $this->glphm->set($di);
    // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '+', $di['point']);

    return 0;
  }

  private function __sortCol($sort_col, $tbl_as)
  {
    switch ($sort_col) {
      case 'id':
      $sort_col = "$tbl_as.id";
      break;
      case 'name':
      $sort_col = "$tbl_as.name";
      break;
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
    if ($sort_dir == "asc") {
      $sort_dir = "ASC";
    } else {
      $sort_dir = "DESC";
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

  private function __sortColRequestMember($sort_col, $tbl_as)
  {
    switch ($sort_col) {
      case 'i_group_id':
        $sort_col = "$tbl_as.i_group_id";
        break;
      case 'name':
        $sort_col = "$tbl_as.name";
        break;
      case 'cdate':
        $sort_col = "$tbl_as.cdate";
        break;
      default:
      $sort_col = "$tbl_as.cdate";
    }
    return $sort_col;
  }

  public function index()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['groups'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $pelanggan = new stdClass();
      if($nation_code == 62){ //indonesia
        $pelanggan->language_id = 2;
      }else if($nation_code == 82){ //korea
        $pelanggan->language_id = 3;
      }else if($nation_code == 66){ //thailand
        $pelanggan->language_id = 4;
      }else {
        $pelanggan->language_id = 1;
      }
    }

    $timezone = $this->input->post("timezone");
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    // $page_size = $this->input->post("page_size");
    $page_size = 10;

    $tbl_as = $this->igm->getTblAs();
    $sort_col = $this->__sortCol($sort_col, $tbl_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

    $group_type = strtolower(trim($this->input->post('group_type')));
    if (!in_array($group_type, array("private","listed", "public"))) {
      $group_type = "";
    }

    $query_type = strtolower(trim($this->input->post('query_type')));
    if(!in_array($query_type, array("popular", "latest", "sub category", "joined club", "similar club"))){
      $query_type = "";
    }

    $b_user_id = $this->input->post("b_user_id");
    if ($b_user_id<='0') {
      $b_user_id = "";
    }

    $i_group_sub_category_id = $this->input->post("i_group_sub_category_id");
    if ($i_group_sub_category_id <= '0') {
      $i_group_sub_category_id = "";
    }

    $i_group_home_list_id = $this->input->post("i_group_home_list_id");
    if ($i_group_home_list_id<='0') {
      $i_group_home_list_id = "";
    }

    $i_group_ids = json_decode($this->input->post("i_group_ids"));

    //keyword
    $keyword = trim($this->input->post("keyword"));
    // if (mb_strlen($keyword)>1) {
    //   //$keyword = utf8_encode(trim($keyword));
    //   $enc = mb_detect_encoding($keyword, 'UTF-8');
    //   if ($enc == 'UTF-8') {
    //   } else {
    //     $keyword = iconv($enc, 'ISO-8859-1//TRANSLIT', $keyword);
    //   }
    // } else {
    //   $keyword="";
    // }
    $keyword = filter_var(strip_tags($keyword), FILTER_SANITIZE_SPECIAL_CHARS);
    $keyword = substr($keyword, 0, 32);

    if($b_user_id != ""){
      $data['groups'] = $this->igm->getAllFromParticipant($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $group_type, $b_user_id);
    }else if(in_array($query_type, array("popular", "latest"))){
      $data['groups'] = $this->igm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $group_type, $b_user_id, $query_type);
    // }else if($query_type == "sub category" && $i_group_sub_category_id != "" && $i_group_home_list_id != ""){
    //   $data['groups'] = $this->igm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $group_type, $b_user_id, $query_type, $i_group_sub_category_id, $i_group_home_list_id);
    }else if($query_type == "joined club" && count($i_group_ids) != 0){
      $data['groups'] = $this->igm->getByIds($nation_code, $page, $page_size, $sort_col, $sort_dir, $i_group_ids);
    }else if($query_type == "similar club" && $i_group_sub_category_id != ""){
      $data['groups'] = $this->igm->getAllFromSubCategory($nation_code, $page, $page_size, $sort_col, $sort_dir, $i_group_sub_category_id);
    }else{
      $data['groups'] = $this->igm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $group_type, $b_user_id);
    }

    foreach ($data['groups'] as &$pd) {
      $pd->cdate_text = $this->humanTiming($pd->cdate, null, $pelanggan->language_id);
      if($pelanggan->language_id == 2) {
          $pd->cdate_text_2 = $this->__dateIndonesia($pd->cdate, "tanggal");
      }else{
          $pd->cdate_text_2 = $this->__dateEnglish($pd->cdate, "tanggal");
      }
      $cdate_texte = date_create($pd->cdate);
      $pd->created_on = date_format($cdate_texte, "M Y");
      $pd->cdate = $this->customTimezone($pd->cdate, $timezone);
      $pd->image = $this->cdn_url($pd->image_thumb);

      if(isset($pelanggan->id)){
        $pd->status_member = $this->__statusMember($nation_code, $pd->id, $pelanggan->id);
      }else{
        $pd->status_member = $this->__statusMember($nation_code, $pd->id, "0");
      }

      $pd->description_images = array();
      $pd->description_location = array();
      $attachmentImage = $this->igam->getByGroupId($nation_code, $pd->id, "all", "image");
      foreach($attachmentImage as &$atc_image) {
        if($atc_image->jenis == 'image' || $atc_image->jenis == 'video'){
          $atc_image->url = $this->cdn_url($atc_image->url);
          $atc_image->url_thumb = $this->cdn_url($atc_image->url_thumb);
          $pd->description_images[] = $atc_image;
        }
      }
      unset($attachmentImage);

      $attachmentLocation = $this->igam->getByGroupId($nation_code, $pd->id, "all", "location");
      foreach($attachmentLocation as &$atc_location) {
        $pd->description_location[] = $atc_location;
      }
      unset($attachmentLocation);
    }

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function homepage()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['group'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $pelanggan = new stdClass();
      if($nation_code == 62){ //indonesia
        $pelanggan->language_id = 2;
      }else if($nation_code == 82){ //korea
        $pelanggan->language_id = 3;
      }else if($nation_code == 66){ //thailand
        $pelanggan->language_id = 4;
      }else {
        $pelanggan->language_id = 1;
      }
    }

    $timezone = $this->input->post("timezone");
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    if (!isset($pelanggan->id)) {
      if($timezone == "Asia/Jakarta") {
        $pelanggan->language_id = 2;
      }else{
        $pelanggan->language_id = 1;
      }
    }

    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    // $page_size = $this->input->post("page_size");
    $page_size = 10;

    $tbl_as = $this->igm->getTblAs();
    $sort_col = $this->__sortCol($sort_col, $tbl_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

    $b_user_id = $this->input->post("b_user_id");
    if ($b_user_id<='0') {
      $b_user_id = "";
    }

    $prioritas = $this->input->post("prioritas");
    if ($prioritas<='0') {
      $prioritas = "1";
    }

    //keyword
    // $keyword = trim($this->input->post("keyword"));
    $keyword = "";
    // if (mb_strlen($keyword)>1) {
    //   //$keyword = utf8_encode(trim($keyword));
    //   $enc = mb_detect_encoding($keyword, 'UTF-8');
    //   if ($enc == 'UTF-8') {
    //   } else {
    //     $keyword = iconv($enc, 'ISO-8859-1//TRANSLIT', $keyword);
    //   }
    // } else {
    //   $keyword="";
    // }
    // $keyword = filter_var(strip_tags($keyword), FILTER_SANITIZE_SPECIAL_CHARS);
    // $keyword = substr($keyword, 0, 32);

    $data['group'] = $this->ighlm->getByPrioritas($nation_code, $prioritas, $pelanggan->language_id);
    if(isset($data['group']->type)){
      $data['group']->detail = array();

      if($b_user_id != "" && $data['group']->type == "my club"){
        $data['group']->detail = $this->igm->getAllFromParticipant($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, "", $b_user_id);
        $data['group']->link_url = "group/group/";
        $data['group']->link_url_parameter = array(
          "b_user_id" => $b_user_id
        );
      }else if(in_array($data['group']->type, array("popular", "latest"))){
        $data['group']->detail = $this->igm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, "", "", $data['group']->type);
        $data['group']->link_url = "group/group/";
        $data['group']->link_url_parameter = array(
          "query_type" => $data['group']->type
        );
      }else if($data['group']->type == "sub category"){
        // $data['group']->detail = $this->igm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, "", "", $data['group']->type, $data['group']->i_group_sub_category_id, $data['group']->id);
        $data['group']->url = $this->cdn_url($data['group']->url);
        $data['group']->detail = array();
        $data['group']->link_url = "group/group/homepagedetail/";
        $data['group']->link_url_parameter = array(
          // "query_type" => $data['group']->type,
          "i_group_sub_category_id" => $data['group']->i_group_sub_category_id,
          "i_group_home_list_id" => $data['group']->id,
          "prioritas" => "1"
        );
      // }else{
      //   $data['group']->detail = $this->igm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, "", $b_user_id);
      }

      foreach ($data['group']->detail as &$pd) {
        $pd->cdate_text = $this->humanTiming($pd->cdate, null, $pelanggan->language_id);
        if($pelanggan->language_id == 2) {
            $pd->cdate_text_2 = $this->__dateIndonesia($pd->cdate, "tanggal");
        }else{
            $pd->cdate_text_2 = $this->__dateEnglish($pd->cdate, "tanggal");
        }
        $cdate_texte = date_create($pd->cdate);
        $pd->created_on = date_format($cdate_texte, "M Y");
        $pd->cdate = $this->customTimezone($pd->cdate, $timezone);
        $pd->image = $this->cdn_url($pd->image_thumb);

        if(isset($pelanggan->id)){
          $pd->status_member = $this->__statusMember($nation_code, $pd->id, $pelanggan->id);
        }else{
          $pd->status_member = $this->__statusMember($nation_code, $pd->id, "0");
        }

        $pd->description_images = array();
        $pd->description_location = array();
        $attachmentImage = $this->igam->getByGroupId($nation_code, $pd->id, "all", "image");
        foreach($attachmentImage as &$atc_image) {
          if($atc_image->jenis == 'image' || $atc_image->jenis == 'video'){
            $atc_image->url = $this->cdn_url($atc_image->url);
            $atc_image->url_thumb = $this->cdn_url($atc_image->url_thumb);
            $pd->description_images[] = $atc_image;
          }
        }
        unset($attachmentImage);

        $attachmentLocation = $this->igam->getByGroupId($nation_code, $pd->id, "all", "location");
        foreach($attachmentLocation as &$atc_location) {
          $pd->description_location[] = $atc_location;
        }
        unset($attachmentLocation);
      }
    }

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function homepagev2()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['group'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $pelanggan = new stdClass();
      if($nation_code == 62){ //indonesia
        $pelanggan->language_id = 2;
      }else if($nation_code == 82){ //korea
        $pelanggan->language_id = 3;
      }else if($nation_code == 66){ //thailand
        $pelanggan->language_id = 4;
      }else {
        $pelanggan->language_id = 1;
      }
    }

    $timezone = $this->input->post("timezone");
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    if (!isset($pelanggan->id)) {
      if($timezone == "Asia/Jakarta") {
        $pelanggan->language_id = 2;
      }else{
        $pelanggan->language_id = 1;
      }
    }

    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    // $page_size = $this->input->post("page_size");
    $page_size = 10;

    $tbl_as = $this->igm->getTblAs();
    $sort_col = $this->__sortCol($sort_col, $tbl_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

    $b_user_id = $this->input->post("b_user_id");
    if ($b_user_id<='0') {
      $b_user_id = "";
    }

    // $prioritas = $this->input->post("prioritas");
    // if ($prioritas<='0') {
    //   $prioritas = "1";
    // }

    //keyword
    // $keyword = trim($this->input->post("keyword"));
    $keyword = "";
    // if (mb_strlen($keyword)>1) {
    //   //$keyword = utf8_encode(trim($keyword));
    //   $enc = mb_detect_encoding($keyword, 'UTF-8');
    //   if ($enc == 'UTF-8') {
    //   } else {
    //     $keyword = iconv($enc, 'ISO-8859-1//TRANSLIT', $keyword);
    //   }
    // } else {
    //   $keyword="";
    // }
    // $keyword = filter_var(strip_tags($keyword), FILTER_SANITIZE_SPECIAL_CHARS);
    // $keyword = substr($keyword, 0, 32);

    $data['group'] = $this->ighlm->getAll($nation_code, 0, 0, "prioritas", "ASC", $pelanggan->language_id);
    foreach($data['group'] AS &$group){
      $group->detail = array();

      if($b_user_id != "" && $group->type == "my club"){
        $group->detail = $this->igm->getAllFromParticipant($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, "", $b_user_id);
        $group->link_url = "group/group/";
        $group->link_url_parameter = array(
          0 => array(
            "parameter" => "b_user_id",
            "value" => $b_user_id
          )
        );
      }else if(in_array($group->type, array("popular", "latest"))){
        $group->detail = $this->igm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, "", "", $group->type);
        $group->link_url = "group/group/";
        $group->link_url_parameter = array(
          0 => array(
            "parameter" => "query_type",
            "value" => $group->type
          )
        );
      }else if($group->type == "sub category"){
        // $group->detail = $this->igm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, "", "", $group->type, $group->i_group_sub_category_id, $group->id);
        $group->url = $this->cdn_url($group->url);
        $group->detail = array();
        $group->link_url = "group/group/homepagedetailv2/";
        $group->link_url_parameter = array(
          0 => array(
            "parameter" => "i_group_sub_category_id",
            "value" => $group->i_group_sub_category_id
          ),
          1 => array(
            "parameter" => "i_group_home_list_id",
            "value" => $group->id
          )
        );
      // }else{
      //   $group->detail = $this->igm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, "", $b_user_id);
      }

      foreach ($group->detail as &$pd) {
        $pd->cdate_text = $this->humanTiming($pd->cdate, null, $pelanggan->language_id);
        if($pelanggan->language_id == 2) {
            $pd->cdate_text_2 = $this->__dateIndonesia($pd->cdate, "tanggal");
        }else{
            $pd->cdate_text_2 = $this->__dateEnglish($pd->cdate, "tanggal");
        }
        $cdate_texte = date_create($pd->cdate);
        $pd->created_on = date_format($cdate_texte, "M Y");
        $pd->cdate = $this->customTimezone($pd->cdate, $timezone);
        $pd->image = $this->cdn_url($pd->image_thumb);

        if(isset($pelanggan->id)){
          $pd->status_member = $this->__statusMember($nation_code, $pd->id, $pelanggan->id);
        }else{
          $pd->status_member = $this->__statusMember($nation_code, $pd->id, "0");
        }

        $pd->description_images = array();
        $pd->description_location = array();
        $attachmentImage = $this->igam->getByGroupId($nation_code, $pd->id, "all", "image");
        foreach($attachmentImage as &$atc_image) {
          if($atc_image->jenis == 'image' || $atc_image->jenis == 'video'){
            $atc_image->url = $this->cdn_url($atc_image->url);
            $atc_image->url_thumb = $this->cdn_url($atc_image->url_thumb);
            $pd->description_images[] = $atc_image;
          }
        }
        unset($attachmentImage);

        $attachmentLocation = $this->igam->getByGroupId($nation_code, $pd->id, "all", "location");
        foreach($attachmentLocation as &$atc_location) {
          $pd->description_location[] = $atc_location;
        }
        unset($attachmentLocation);
      }
    }

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function homepagedetail()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['group'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $pelanggan = new stdClass();
      if($nation_code == 62){ //indonesia
        $pelanggan->language_id = 2;
      }else if($nation_code == 82){ //korea
        $pelanggan->language_id = 3;
      }else if($nation_code == 66){ //thailand
        $pelanggan->language_id = 4;
      }else {
        $pelanggan->language_id = 1;
      }
    }

    $timezone = $this->input->post("timezone");
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    // $page_size = $this->input->post("page_size");
    $page_size = 10;

    $tbl_as = $this->igm->getTblAs();
    $sort_col = $this->__sortCol($sort_col, $tbl_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

    $b_user_id = $this->input->post("b_user_id");
    if ($b_user_id<='0') {
      $b_user_id = "";
    }

    $prioritas = $this->input->post("prioritas");
    if ($prioritas<='0') {
      $prioritas = "1";
    }

    $i_group_sub_category_id = $this->input->post("i_group_sub_category_id");
    if ($i_group_sub_category_id <= '0') {
      $i_group_sub_category_id = "";
    }

    $i_group_home_list_id = $this->input->post("i_group_home_list_id");
    if ($i_group_home_list_id<='0') {
      $i_group_home_list_id = "";
    }

    //keyword
    // $keyword = trim($this->input->post("keyword"));
    $keyword = "";
    // if (mb_strlen($keyword)>1) {
    //   //$keyword = utf8_encode(trim($keyword));
    //   $enc = mb_detect_encoding($keyword, 'UTF-8');
    //   if ($enc == 'UTF-8') {
    //   } else {
    //     $keyword = iconv($enc, 'ISO-8859-1//TRANSLIT', $keyword);
    //   }
    // } else {
    //   $keyword="";
    // }
    // $keyword = filter_var(strip_tags($keyword), FILTER_SANITIZE_SPECIAL_CHARS);
    // $keyword = substr($keyword, 0, 32);

    $data['group'] = $this->ighdm->getByPrioritas($nation_code, $i_group_home_list_id, $prioritas, $pelanggan->language_id);
    if(isset($data['group']->type)){
      $data['group']->i_group_sub_category_id = $i_group_sub_category_id;
      $data['group']->detail = array();

      if($data['group']->type == "official club"){
        $data['group']->detail = $this->igm->getByIds($nation_code, 1, 10, $sort_col, $sort_dir, json_decode($data['group']->i_group_ids));
        $data['group']->link_url = "";
        $data['group']->link_url_parameter = new stdClass();
      }else if($data['group']->type == "joined club"){
        $data['group']->detail = $this->igm->getByIds($nation_code, $page, $page_size, $sort_col, $sort_dir, json_decode($data['group']->i_group_ids));
        $data['group']->link_url = "group/group/";
        $data['group']->link_url_parameter = array(
          "query_type" => $data['group']->type,
          "i_group_home_list_id" => $i_group_home_list_id,
          "i_group_ids" => $data['group']->i_group_ids
        );
      }else if($data['group']->type == "similar club"){
        $data['group']->detail = $this->igm->getAllFromSubCategory($nation_code, $page, $page_size, $sort_col, $sort_dir, $i_group_sub_category_id);
        $data['group']->link_url = "group/group/";
        $data['group']->link_url_parameter = array(
          "query_type" => $data['group']->type,
          "i_group_sub_category_id" => $i_group_sub_category_id
          // "i_group_home_list_id" => $data['group']->id
        );
      // }else{
      //   $data['group']->detail = $this->igm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, "", $b_user_id);
      }

      foreach ($data['group']->detail as &$pd) {
        $pd->cdate_text = $this->humanTiming($pd->cdate, null, $pelanggan->language_id);
        if($pelanggan->language_id == 2) {
            $pd->cdate_text_2 = $this->__dateIndonesia($pd->cdate, "tanggal");
        }else{
            $pd->cdate_text_2 = $this->__dateEnglish($pd->cdate, "tanggal");
        }
        $cdate_texte = date_create($pd->cdate);
        $pd->created_on = date_format($cdate_texte, "M Y");
        $pd->cdate = $this->customTimezone($pd->cdate, $timezone);
        $pd->image = $this->cdn_url($pd->image_thumb);

        if(isset($pelanggan->id)){
          $pd->status_member = $this->__statusMember($nation_code, $pd->id, $pelanggan->id);
        }else{
          $pd->status_member = $this->__statusMember($nation_code, $pd->id, "0");
        }

        $pd->description_images = array();
        $pd->description_location = array();
        $attachmentImage = $this->igam->getByGroupId($nation_code, $pd->id, "all", "image");
        foreach($attachmentImage as &$atc_image) {
          if($atc_image->jenis == 'image' || $atc_image->jenis == 'video'){
            $atc_image->url = $this->cdn_url($atc_image->url);
            $atc_image->url_thumb = $this->cdn_url($atc_image->url_thumb);
            $pd->description_images[] = $atc_image;
          }
        }
        unset($attachmentImage);

        $attachmentLocation = $this->igam->getByGroupId($nation_code, $pd->id, "all", "location");
        foreach($attachmentLocation as &$atc_location) {
          $pd->description_location[] = $atc_location;
        }
        unset($attachmentLocation);
      }

      if(count($data['group']->detail) == 0){
        $data['group'] = new stdClass();
      }
    }

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function homepagedetailv2()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['group'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $pelanggan = new stdClass();
      if($nation_code == 62){ //indonesia
        $pelanggan->language_id = 2;
      }else if($nation_code == 82){ //korea
        $pelanggan->language_id = 3;
      }else if($nation_code == 66){ //thailand
        $pelanggan->language_id = 4;
      }else {
        $pelanggan->language_id = 1;
      }
    }

    $timezone = $this->input->post("timezone");
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    // $page_size = $this->input->post("page_size");
    $page_size = 10;

    $tbl_as = $this->igm->getTblAs();
    $sort_col = $this->__sortCol($sort_col, $tbl_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

    $b_user_id = $this->input->post("b_user_id");
    if ($b_user_id<='0') {
      $b_user_id = "";
    }

    $i_group_sub_category_id = $this->input->post("i_group_sub_category_id");
    if ($i_group_sub_category_id <= '0') {
      $i_group_sub_category_id = "";
    }

    $i_group_home_list_id = $this->input->post("i_group_home_list_id");
    if ($i_group_home_list_id<='0') {
      $i_group_home_list_id = "";
    }

    //keyword
    // $keyword = trim($this->input->post("keyword"));
    $keyword = "";
    // if (mb_strlen($keyword)>1) {
    //   //$keyword = utf8_encode(trim($keyword));
    //   $enc = mb_detect_encoding($keyword, 'UTF-8');
    //   if ($enc == 'UTF-8') {
    //   } else {
    //     $keyword = iconv($enc, 'ISO-8859-1//TRANSLIT', $keyword);
    //   }
    // } else {
    //   $keyword="";
    // }
    // $keyword = filter_var(strip_tags($keyword), FILTER_SANITIZE_SPECIAL_CHARS);
    // $keyword = substr($keyword, 0, 32);

    $data['group'] = $this->ighdm->getAll($nation_code, 0, 0, "prioritas", "ASC", $i_group_home_list_id, $pelanggan->language_id);
    foreach($data['group'] AS $key => $group){
      $group->i_group_sub_category_id = $i_group_sub_category_id;
      $group->detail = array();

      if($group->type == "official club"){
        $group->detail = $this->igm->getByIds($nation_code, 1, 10, $sort_col, $sort_dir, json_decode($group->i_group_ids));
        $group->link_url = "";
        $group->link_url_parameter = array();
      }else if($group->type == "joined club"){
        $group->detail = $this->igm->getByIds($nation_code, $page, $page_size, $sort_col, $sort_dir, json_decode($group->i_group_ids));
        $group->link_url = "group/group/";
        $group->link_url_parameter = array(
          0 => array(
            "parameter" => "query_type",
            "value" => $group->type
          ),
          1 => array(
            "parameter" => "i_group_home_list_id",
            "value" => $i_group_home_list_id
          ),
          2 => array(
            "parameter" => "i_group_ids",
            "value" => $group->i_group_ids
          )
        );
      }else if($group->type == "similar club"){
        $group->detail = $this->igm->getAllFromSubCategory($nation_code, $page, $page_size, $sort_col, $sort_dir, $i_group_sub_category_id);
        $group->link_url = "group/group/";
        $group->link_url_parameter = array(
          0 => array(
            "parameter" => "query_type",
            "value" => $group->type
          ),
          1 => array(
            "parameter" => "i_group_sub_category_id",
            "value" => $i_group_sub_category_id
          )
        );
      // }else{
      //   $group->detail = $this->igm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, "", $b_user_id);
      }

      foreach ($group->detail as &$pd) {
        $pd->cdate_text = $this->humanTiming($pd->cdate, null, $pelanggan->language_id);
        if($pelanggan->language_id == 2) {
            $pd->cdate_text_2 = $this->__dateIndonesia($pd->cdate, "tanggal");
        }else{
            $pd->cdate_text_2 = $this->__dateEnglish($pd->cdate, "tanggal");
        }
        $cdate_texte = date_create($pd->cdate);
        $pd->created_on = date_format($cdate_texte, "M Y");
        $pd->cdate = $this->customTimezone($pd->cdate, $timezone);
        $pd->image = $this->cdn_url($pd->image_thumb);

        if(isset($pelanggan->id)){
          $pd->status_member = $this->__statusMember($nation_code, $pd->id, $pelanggan->id);
        }else{
          $pd->status_member = $this->__statusMember($nation_code, $pd->id, "0");
        }

        $pd->description_images = array();
        $pd->description_location = array();
        $attachmentImage = $this->igam->getByGroupId($nation_code, $pd->id, "all", "image");
        foreach($attachmentImage as &$atc_image) {
          if($atc_image->jenis == 'image' || $atc_image->jenis == 'video'){
            $atc_image->url = $this->cdn_url($atc_image->url);
            $atc_image->url_thumb = $this->cdn_url($atc_image->url_thumb);
            $pd->description_images[] = $atc_image;
          }
        }
        unset($attachmentImage);

        $attachmentLocation = $this->igam->getByGroupId($nation_code, $pd->id, "all", "location");
        foreach($attachmentLocation as &$atc_location) {
          $pd->description_location[] = $atc_location;
        }
        unset($attachmentLocation);
      }

      if(count($group->detail) == 0){
        unset($data['group'][$key]);
      }else{
        $data['group'][$key] = $group;
      }
    }
    $data['group'] = array_values($data['group']);

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function detail()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['group'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)){
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c){
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)){
      $pelanggan = new stdClass();
      if($nation_code == 62){ //indonesia
        $pelanggan->language_id = 2;
      }else if($nation_code == 82){ //korea
        $pelanggan->language_id = 3;
      }else if($nation_code == 66){ //thailand
        $pelanggan->language_id = 4;
      }else {
        $pelanggan->language_id = 1;
      }
    }

    $timezone = $this->input->post('timezone');
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $group_id = trim($this->input->post('group_id'));
    if (strlen($group_id)<3){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igm->getById($nation_code, $group_id);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult->cdate_text = $this->humanTiming($queryResult->cdate, null, $pelanggan->language_id);
    if($pelanggan->language_id == 2) {
        $queryResult->cdate_text_2 = $this->__dateIndonesia($queryResult->cdate, "tanggal");
    }else{
        $queryResult->cdate_text_2 = $this->__dateEnglish($queryResult->cdate, "tanggal");
    }
    $cdate_texte = date_create($queryResult->cdate);
    $queryResult->created_on = date_format($cdate_texte, "M Y");
    $queryResult->cdate = $this->customTimezone($queryResult->cdate, $timezone);
    $queryResult->image = $this->cdn_url($queryResult->image_thumb);
    $queryResult->qrcode_url = $this->cdn_url($queryResult->qrcode_url);
    if(isset($pelanggan->id)){
      $queryResult->status_member = $this->__statusMember($nation_code, $group_id, $pelanggan->id);
    }else{
      $queryResult->status_member = $this->__statusMember($nation_code, $group_id, "0");
    }

    $queryResult->description_images = array();
    $queryResult->description_location = array();
    $attachmentImage = $this->igam->getByGroupId($nation_code, $queryResult->id, "all", "image");
    foreach($attachmentImage as &$atc_image) {
      if($atc_image->jenis == 'image' || $atc_image->jenis == 'video'){
        $atc_image->url = $this->cdn_url($atc_image->url);
        $atc_image->url_thumb = $this->cdn_url($atc_image->url_thumb);
        $queryResult->description_images[] = $atc_image;
      }
    }
    unset($attachmentImage);

    $attachmentLocation = $this->igam->getByGroupId($nation_code, $queryResult->id, "all", "location");
    foreach($attachmentLocation as &$atc_location) {
      $queryResult->description_location[] = $atc_location;
    }
    unset($attachmentLocation);
    $data['group'] = $queryResult;

    $this->status = 200;
    $this->message = 'Success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  private function __sortColParticipant($sort_col, $tbl_as)
  {
    switch ($sort_col) {
      case 'is_owner':
        $sort_col = "$tbl_as.is_owner";
        break;
      case 'b_user_fnama':
        $sort_col = "$tbl_as.b_user_fnama";
        break;
      case 'cdate':
        $sort_col = "$tbl_as.cdate";
        break;
      default:
      $sort_col = "$tbl_as.is_owner";
    }
    return $sort_col;
  }

  public function participant()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['participant_total'] ="0";
    $data['participant_list'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    $page_size = $this->input->post("page_size");
    $keyword = trim($this->input->post("keyword"));
    $timezone = $this->input->post("timezone");
    $group_id = trim($this->input->post('group_id'));
    if (strlen($group_id)<3) {
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igm->getById($nation_code, $group_id);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    //sanitize input
    $tbl_as = $this->igparticipantm->getTblAs();
    $sort_col = $this->__sortColParticipant($sort_col, $tbl_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

    //keyword
    if (mb_strlen($keyword)>1) {
      //$keyword = utf8_encode(trim($keyword));
      $enc = mb_detect_encoding($keyword, 'UTF-8');
      if ($enc == 'UTF-8') {
      } else {
        $keyword = iconv($enc, 'ISO-8859-1//TRANSLIT', $keyword);
      }
    } else {
      $keyword="";
    }
    $keyword = filter_var(strip_tags($keyword), FILTER_SANITIZE_SPECIAL_CHARS);
    $keyword = substr($keyword, 0, 32);

    $data['participant_total'] = $this->igparticipantm->countByGroupId($nation_code, $keyword, $group_id);
    $data['participant_list'] = $this->igparticipantm->getByGroupId($nation_code, $page, $page_size, $sort_col, "ASC", $keyword, $group_id);
    foreach($data['participant_list'] AS &$list){
      if(file_exists(SENEROOT.$list->b_user_band_image) && $list->b_user_band_image != 'media/user/default.png'){
        $list->b_user_band_image = $this->cdn_url($list->b_user_band_image);
      } else {
        $list->b_user_band_image = $this->cdn_url('media/user/default-profile-picture.png');
      }

      $list->status_member = $this->__statusMember($nation_code, $group_id, $list->b_user_id);
    }

    $this->status = 200;
    $this->message = "Success";

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function baru()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['group'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $timezone = $this->input->post('timezone');
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $maxClubCreated = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E30");
    if (!isset($maxClubCreated->remark)) {
      $maxClubCreated = new stdClass();
      $maxClubCreated->remark = 10;
    }
    $totalClubCreated = $this->igm->totalClubCreated($nation_code, $pelanggan->id);
    if ($totalClubCreated >= $maxClubCreated->remark) {
      $this->status = 1102;
      $this->message = 'Cannot create club anymore today';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->status = 300;
    $this->message = 'Missing one or more parameters';

    //collect input
    $category_id = trim($this->input->post('category_id'));
    $name = trim($this->input->post('name'));
    $image = ltrim(parse_url(trim($this->input->post('image')), PHP_URL_PATH), "/");
    $group_type = strtolower(trim($this->input->post('group_type')));
    if (!in_array($group_type, array("private","listed"))) {
      $group_type = "public";
    }

    $kat = $this->igcm->getById($nation_code, $category_id);
    if (!isset($kat->id)) {
      $this->status = 1100;
      $this->message = 'Please choose type';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }
    unset($kat);

    if (strlen($name)<3) {
      $this->status = 1104;
      $this->message = 'Club name is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    //start transaction and lock table
    $this->igm->trans_start();

    $di = array();
    $di['nation_code'] = $nation_code;
    $di['b_user_id'] = $pelanggan->id;
    $di['i_group_category_id'] = $category_id;
    $di['group_type'] = $group_type;
    $di['description'] = "Welcome! Please join us to stay connected!";
    // by Muhammad Sofi 1 Nov 2023 | set admin approval is required to join group
    if($group_type == "private" || $group_type == "listed") {
      $di['need_admin_approval'] = 1;
    }
    $di['name'] = $name;
    $di['image'] = $image;
    $di['image_thumb'] = $image;
    $di['cdate'] = 'NOW()';

    $endDoWhile = 0;
    do{
      $id = $this->GUIDv4();
      $checkId = $this->igm->checkId($nation_code, $id);
      if($checkId == 0){
        $endDoWhile = 1;
      }
    }while($endDoWhile == 0);
    $di['id'] = $id;

    // // generate qrCode
    // $generate = $this->generateQRCode($nation_code, $id);
    // if(isset($generate->status)) {
    //   if ($generate->status==200) { 
    //     $di['qrcode_url'] = $generate->qrcode_url;
    //   }
    // }

    // generate invite code
    $invite_code = $this->generateInviteCode();
    if(isset($invite_code->status)) {
      if ($invite_code->status==200) { 
        $di['invite_code_digit'] = $invite_code->digit;
        $di['invite_code_word'] = $invite_code->word;
      }
    }

    $endDoWhile = 0;
    do{
      $chatRoomId = $this->GUIDv4();
      $checkId = $this->icrm->checkId($nation_code, $chatRoomId);
      if($checkId == 0){
        $endDoWhile = 1;
      }
    }while($endDoWhile == 0);
    $di['i_chat_room_id'] = $chatRoomId;

    $res = $this->igm->set($di);
    if (!$res) {
      $this->igm->trans_rollback();
      $this->igm->trans_end();
      $this->status = 1107;
      $this->message = "Error, please try again later";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }
    $this->status = 200;
    $this->message = "Success";

    $di = array();
    $di['nation_code'] = $nation_code;
    $di['id'] = $chatRoomId;
    $di['i_group_id'] = $id;
    $di['b_user_id_creator'] = $pelanggan->id;
    $di['is_main_group_chat_room'] = "1";
    $di['b_user_ids'] = json_encode(array($pelanggan->id));
    $di['type'] = "group";
    $di['custom_name_1'] = $name;
    $di['custom_name_2'] = "";
    $di['cdate'] = 'NOW()';
    $createChatRoom = $this->icrm->set($di);
    if(!$createChatRoom){
      $this->igm->trans_rollback();
      $this->igm->trans_end();
      $this->status = 1107;
      $this->message = "Error, please try again later";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $di = array();
    $di['nation_code'] = $nation_code;
    $di['i_group_id'] = $id;
    $di['b_user_id'] = $pelanggan->id;
    $di['cdate'] = 'NOW()';
    $di['is_owner'] = 1;
    $di['is_accept'] = 1;
    $this->igparticipantm->set($di);

    $di = array();
    $di['nation_code'] = $nation_code;
    $di['i_chat_room_id'] = $chatRoomId;
    $di['b_user_id'] = $pelanggan->id;
    $di['cdate'] = 'NOW()';
    $di['last_delete_chat'] = 'NOW()';
    $di['is_read'] = 1;
    $di['is_owner'] = "1";
    $di['is_creator'] = "1";
    $this->icpm->set($di);

    usleep(500000);

    $di = array();
    $imageGone = 0;
    if($image != null){
      if (strpos($image, 'temporary') !== false) {
        if (!file_exists(SENEROOT.$image)) {
          $this->status = 995;
          $this->message = 'Failed upload, temporary already gone';
          $this->igm->trans_rollback();
          $this->igm->trans_end();
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }else{
          $sc = $this->__moveImagex($nation_code, $image, $this->media_group_image, $id);
          if (isset($sc->status)) {
            if ($sc->status==200) {
              $di['image'] = $sc->image;
              $di['image_thumb'] = $sc->thumb;
            }else{
              $imageGone = 1;
            }
          }else{
            $imageGone = 1;
          }
        }
      }else{
        $checkImage = $this->igdim->checkImage($nation_code, $image);
        if($checkImage == 0){
          $getImage = $this->igdim->getAll($nation_code, 1, 1);
          $di['image'] = $getImage[0]->image;
          $di['image_thumb'] = $getImage[0]->image;
        }
      }
    }else{
      $imageGone = 1;
    }

    if($imageGone == 1){
      $getImage = $this->igdim->getAll($nation_code, 1, 1);
      $di['image'] = $getImage[0]->image;
      $di['image_thumb'] = $getImage[0]->image;
    }

    if(isset($di['image'])){
      $this->igm->update($nation_code, $id, $di);
    }

    $totalClubCreatedGetSpt = $this->glphm->countCreateClub($nation_code, $pelanggan->id, "club", "create club");

    $LimitCreateClubGetSpt = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E33");
    if (!isset($LimitCreateClubGetSpt->remark)) {
      $LimitCreateClubGetSpt = new stdClass();
      $LimitCreateClubGetSpt->remark = 30;
    }

    if($LimitCreateClubGetSpt->remark > $totalClubCreatedGetSpt){
      //get point
      $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E17");
      if (!isset($pointGet->remark)) {
        $pointGet = new stdClass();
        $pointGet->remark = 100;
      }

      $di = array();
      $di['nation_code'] = $nation_code;
      $di['b_user_alamat_location_kelurahan'] = "All";
      $di['b_user_alamat_location_kecamatan'] = "All";
      $di['b_user_alamat_location_kabkota'] = "All";
      $di['b_user_alamat_location_provinsi'] = "All";
      $di['b_user_id'] = $pelanggan->id;
      $di['point'] = $pointGet->remark;
      $di['custom_id'] = $id;
      $di['custom_type'] = 'club';
      $di['custom_type_sub'] = "create club";
      $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
      $endDoWhile = 0;
      do{
        $leaderBoardHistoryId = $this->GUIDv4();
        $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
        if($checkId == 0){
          $endDoWhile = 1;
        }
      }while($endDoWhile == 0);
      $di['id'] = $leaderBoardHistoryId;
      $this->glphm->set($di);
      // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '+', $di['point']);
    }

    $this->igm->trans_commit();
    $this->igm->trans_end();

    $queryResult = $this->igm->getById($nation_code, $id);
    $queryResult->cdate_text = $this->humanTiming($queryResult->cdate, null, $pelanggan->language_id);
    $cdate_texte = date_create($queryResult->cdate);
    $queryResult->created_on = date_format($cdate_texte, "M Y");
    $queryResult->cdate = $this->customTimezone($queryResult->cdate, $timezone);
    $queryResult->image = $this->cdn_url($queryResult->image_thumb);
    $queryResult->qrcode_url = $this->cdn_url($queryResult->qrcode_url);
    $queryResult->status_member = $this->__statusMember($nation_code, $id, $pelanggan->id);
    $data['group'] = $queryResult;

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function baruv2()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['group'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $timezone = $this->input->post('timezone');
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $maxClubCreated = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E30");
    if (!isset($maxClubCreated->remark)) {
      $maxClubCreated = new stdClass();
      $maxClubCreated->remark = 10;
    }
    $totalClubCreated = $this->igm->totalClubCreated($nation_code, $pelanggan->id);
    if ($totalClubCreated >= $maxClubCreated->remark) {
      $this->status = 1102;
      $this->message = 'Cannot create club anymore today';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->status = 300;
    $this->message = 'Missing one or more parameters';

    //collect input
    $category_id = trim($this->input->post('category_id'));
    $i_group_sub_category_id = trim($this->input->post('i_group_sub_category_id'));
    $name = trim($this->input->post('name'));
    $image = ltrim(parse_url(trim($this->input->post('image')), PHP_URL_PATH), "/");
    $group_type = strtolower(trim($this->input->post('group_type')));
    if (!in_array($group_type, array("private","listed"))) {
      $group_type = "public";
    }

    $kat = $this->igcm->getById($nation_code, $category_id);
    if (!isset($kat->id)) {
      $this->status = 1100;
      $this->message = 'Please choose type';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }
    unset($kat);

    if($category_id == "13744ff9-1312-40c3-8acc-83cfd792013c"){
      $subKat = $this->igcm->getById($nation_code, $i_group_sub_category_id);
      if (!isset($subKat->id)) {
        $this->status = 1120;
        $this->message = 'Please choose sports category';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }
      unset($subKat);
    }

    if (strlen($name)<3) {
      $this->status = 1104;
      $this->message = 'Club name is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    //start transaction and lock table
    $this->igm->trans_start();

    $di = array();
    $di['nation_code'] = $nation_code;
    $di['b_user_id'] = $pelanggan->id;
    $di['i_group_category_id'] = $category_id;
    $di['i_group_sub_category_id'] = $i_group_sub_category_id;
    $di['group_type'] = $group_type;
    $di['description'] = "Welcome! Please join us to stay connected!";
    // by Muhammad Sofi 1 Nov 2023 | set admin approval is required to join group
    if($group_type == "private" || $group_type == "listed") {
      $di['need_admin_approval'] = 1;
    }
    $di['name'] = $name;
    $di['image'] = $image;
    $di['image_thumb'] = $image;
    $di['cdate'] = 'NOW()';

    $endDoWhile = 0;
    do{
      $id = $this->GUIDv4();
      $checkId = $this->igm->checkId($nation_code, $id);
      if($checkId == 0){
        $endDoWhile = 1;
      }
    }while($endDoWhile == 0);
    $di['id'] = $id;

    // // generate qrCode
    // $generate = $this->generateQRCode($nation_code, $id);
    // if(isset($generate->status)) {
    //   if ($generate->status==200) { 
    //     $di['qrcode_url'] = $generate->qrcode_url;
    //   }
    // }

    // generate invite code
    $invite_code = $this->generateInviteCode();
    if(isset($invite_code->status)) {
      if ($invite_code->status==200) { 
        $di['invite_code_digit'] = $invite_code->digit;
        $di['invite_code_word'] = $invite_code->word;
      }
    }

    $endDoWhile = 0;
    do{
      $chatRoomId = $this->GUIDv4();
      $checkId = $this->icrm->checkId($nation_code, $chatRoomId);
      if($checkId == 0){
        $endDoWhile = 1;
      }
    }while($endDoWhile == 0);
    $di['i_chat_room_id'] = $chatRoomId;

    $res = $this->igm->set($di);
    if (!$res) {
      $this->igm->trans_rollback();
      $this->igm->trans_end();
      $this->status = 1107;
      $this->message = "Error, please try again later";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->gdtrm->updateTotalData(DATE("Y-m-d"), "club_create", "+", "1");

    $this->status = 200;
    $this->message = "Success";

    $di = array();
    $di['nation_code'] = $nation_code;
    $di['id'] = $chatRoomId;
    $di['i_group_id'] = $id;
    $di['b_user_id_creator'] = $pelanggan->id;
    $di['is_main_group_chat_room'] = "1";
    $di['b_user_ids'] = json_encode(array($pelanggan->id));
    $di['type'] = "group";
    $di['custom_name_1'] = $name;
    $di['custom_name_2'] = "";
    $di['cdate'] = 'NOW()';
    $createChatRoom = $this->icrm->set($di);
    if(!$createChatRoom){
      $this->igm->trans_rollback();
      $this->igm->trans_end();
      $this->status = 1107;
      $this->message = "Error, please try again later";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $di = array();
    $di['nation_code'] = $nation_code;
    $di['i_group_id'] = $id;
    $di['b_user_id'] = $pelanggan->id;
    $di['cdate'] = 'NOW()';
    $di['is_owner'] = 1;
    $di['is_accept'] = 1;
    $this->igparticipantm->set($di);

    $di = array();
    $di['nation_code'] = $nation_code;
    $di['i_chat_room_id'] = $chatRoomId;
    $di['b_user_id'] = $pelanggan->id;
    $di['cdate'] = 'NOW()';
    $di['last_delete_chat'] = 'NOW()';
    $di['is_read'] = 1;
    $di['is_owner'] = "1";
    $di['is_creator'] = "1";
    $this->icpm->set($di);

    usleep(500000);

    $di = array();
    $imageGone = 0;
    if($image != null){
      if (strpos($image, 'temporary') !== false) {
        if (!file_exists(SENEROOT.$image)) {
          $this->status = 995;
          $this->message = 'Failed upload, temporary already gone';
          $this->igm->trans_rollback();
          $this->igm->trans_end();
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }else{
          $sc = $this->__moveImagex($nation_code, $image, $this->media_group_image, $id);
          if (isset($sc->status)) {
            if ($sc->status==200) {
              $di['image'] = $sc->image;
              $di['image_thumb'] = $sc->thumb;
            }else{
              $imageGone = 1;
            }
          }else{
            $imageGone = 1;
          }
        }
      }else{
        $checkImage = $this->igdim->checkImage($nation_code, $image);
        if($checkImage == 0){
          $getImage = $this->igdim->getAll($nation_code, 1, 1);
          $di['image'] = $getImage[0]->image;
          $di['image_thumb'] = $getImage[0]->image;
        }
      }
    }else{
      $imageGone = 1;
    }

    if($imageGone == 1){
      $getImage = $this->igdim->getAll($nation_code, 1, 1);
      $di['image'] = $getImage[0]->image;
      $di['image_thumb'] = $getImage[0]->image;
    }

    if(isset($di['image'])){
      $this->igm->update($nation_code, $id, $di);
    }

    $totalClubCreatedGetSpt = $this->glphm->countCreateClub($nation_code, $pelanggan->id, "club", "create club");

    $LimitCreateClubGetSpt = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E33");
    if (!isset($LimitCreateClubGetSpt->remark)) {
      $LimitCreateClubGetSpt = new stdClass();
      $LimitCreateClubGetSpt->remark = 30;
    }

    if($LimitCreateClubGetSpt->remark > $totalClubCreatedGetSpt){
      //get point
      $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E17");
      if (!isset($pointGet->remark)) {
        $pointGet = new stdClass();
        $pointGet->remark = 100;
      }

      $di = array();
      $di['nation_code'] = $nation_code;
      $di['b_user_alamat_location_kelurahan'] = "All";
      $di['b_user_alamat_location_kecamatan'] = "All";
      $di['b_user_alamat_location_kabkota'] = "All";
      $di['b_user_alamat_location_provinsi'] = "All";
      $di['b_user_id'] = $pelanggan->id;
      $di['point'] = $pointGet->remark;
      $di['custom_id'] = $id;
      $di['custom_type'] = 'club';
      $di['custom_type_sub'] = "create club";
      $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
      $endDoWhile = 0;
      do{
        $leaderBoardHistoryId = $this->GUIDv4();
        $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
        if($checkId == 0){
          $endDoWhile = 1;
        }
      }while($endDoWhile == 0);
      $di['id'] = $leaderBoardHistoryId;
      $this->glphm->set($di);
      // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '+', $di['point']);
    }

    $this->igm->trans_commit();
    $this->igm->trans_end();

    $queryResult = $this->igm->getById($nation_code, $id);
    $queryResult->cdate_text = $this->humanTiming($queryResult->cdate, null, $pelanggan->language_id);
    $cdate_texte = date_create($queryResult->cdate);
    $queryResult->created_on = date_format($cdate_texte, "M Y");
    $queryResult->cdate = $this->customTimezone($queryResult->cdate, $timezone);
    $queryResult->image = $this->cdn_url($queryResult->image_thumb);
    $queryResult->qrcode_url = $this->cdn_url($queryResult->qrcode_url);
    $queryResult->status_member = $this->__statusMember($nation_code, $id, $pelanggan->id);
    $data['group'] = $queryResult;

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function total_member_request()
  {
    //initial
    $dt = $this->__init();

    //default result
    // $data = array();
    $data = "0";

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $group_id = trim($this->input->post('group_id'));
    if (strlen($group_id)<3){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igm->getById($nation_code, $group_id);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $data = $this->igparticipantm->getRequestTotal($nation_code, $group_id);

    $this->status = 200;
    $this->message = "Success";

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function list_member_request()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['request_list'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $timezone = $this->input->post("timezone");
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    $page_size = $this->input->post("page_size");
    $keyword = trim($this->input->post("keyword"));

    //sanitize input
    $tbl_as = $this->igparticipantm->getTblAs();
    $sort_col = $this->__sortColRequestMember($sort_col, $tbl_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

    $group_id = trim($this->input->get('group_id'));
    if (strlen($group_id)<3){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igm->getById($nation_code, $group_id);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    if ($queryResult->b_user_id != $pelanggan->id) {
      $stillParticipant = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $queryResult->id, $pelanggan->id);
      if ($stillParticipant->is_owner == "0" && $stillParticipant->is_co_admin == "0") {
        $this->status = 1125;
        $this->message = "Your priviledge is limited as you're member";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }
    }

    $queryResult = $this->igparticipantm->getRequestList($nation_code, $group_id, $sort_col, $sort_dir, $page, $page_size);
    foreach($queryResult as $qr) {
      $date = date_create($qr->cdate);
      $qr->cdate = date_format($date, "d M Y");
      $qr->b_user_band_image = $this->cdn_url($qr->b_user_band_image);
      // $qr->cdate = $this->humanTiming($qr->cdate, null, $pelanggan->language_id);
    }

    $data['request_list'] = $queryResult;

    $this->status = 200;
    $this->message = "Success";

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function accept_or_reject_member()
  {
    //initial
    $dt = $this->__init();

    $this->seme_log->write("api_mobile", 'API_Mobile/group::accept_or_reject_member POST: '.json_encode($_POST));

    //default result
    $data = array();
    $data['status'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $get_action = $this->input->get('action');
		if (empty($get_action) || !in_array($get_action, array('accept', 'reject'))) {
			$this->status = 1112;
			$this->message = 'Missing or invalid action';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

    $group_id = trim($this->input->post('group_id'));
    if (strlen($group_id)<3){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igm->getById($nation_code, $group_id);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    if ($queryResult->b_user_id != $pelanggan->id) {
      $stillParticipant = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $queryResult->id, $pelanggan->id);
      if ($stillParticipant->is_owner == "0" && $stillParticipant->is_co_admin == "0") {
        $this->status = 1125;
        $this->message = "Your priviledge is limited as you're member";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }
    }

    $b_user_id = trim($this->input->post('b_user_id'));
    if (empty($b_user_id)){
      $this->status = 1111;
      $this->message = 'b_user_id is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->igparticipantm->trans_start();

    $requestedMember = $this->bu->getById($nation_code, $b_user_id);

    if(strtolower($get_action) == "accept") {
      $res = $this->igparticipantm->setStatusMember($nation_code, $group_id, $b_user_id, "accept");

      // get from chat_room
      $cr = $this->icrm->getChatRoomByID($nation_code, $queryResult->i_chat_room_id);
      $cr->b_user_ids = json_decode($cr->b_user_ids);
      $cr->b_user_ids[] = $b_user_id;

      $du = array();
      $du['b_user_ids'] = json_encode($cr->b_user_ids);
      $du['total_people_chat_room'] = count($cr->b_user_ids);
      $this->icrm->update($nation_code, $queryResult->i_chat_room_id, $du);
      unset($du);

      $du = array();
      $du['total_people'] = count($cr->b_user_ids);
      $this->igm->update($nation_code, $group_id, $du);
      unset($du);

      $di = array();
      $di['nation_code'] = $nation_code;
      $di['i_chat_room_id'] = $queryResult->i_chat_room_id;
      $di['b_user_id'] = $b_user_id;
      $di['cdate'] = 'NOW()';
      $di['last_delete_chat'] = 'NOW()';
      $di['is_read'] = 1;
      $this->icpm->set($di);
      unset($di);

      $alreadyJoinedBefore = $this->igphm->getByGroupidUserid($nation_code, $group_id, $b_user_id);
      if(!isset($alreadyJoinedBefore->cdate)){
        $oneParticipantData = $this->igparticipantm->getStatus($nation_code, $group_id, $b_user_id);
        if($oneParticipantData->b_user_id_inviter == "0"){
          $this->give_spt($nation_code, "self join", $queryResult->b_user_id, $group_id);
        }else{
          $this->give_spt($nation_code, "inviter", $oneParticipantData->b_user_id_inviter, $group_id);
        }
        unset($oneParticipantData);
      }
      unset($alreadyJoinedBefore);

      $oneParticipantData = $this->igparticipantm->getStatus($nation_code, $group_id, $b_user_id);
      $di = array();
      $di['nation_code'] = $nation_code;
      $di['i_group_id'] = $group_id;
      $di['b_user_id'] = $b_user_id;
      $di['b_user_id_inviter'] = $oneParticipantData->b_user_id_inviter;
      $di['cdate'] = 'NOW()';
      $endDoWhile = 0;
      do{
        $igphmId = $this->GUIDv4();
        $checkId = $this->igphm->checkId($nation_code, $igphmId);
        if($checkId == 0){
          $endDoWhile = 1;
        }
      }while($endDoWhile == 0);
      $di['id'] = $igphmId;
      $this->igphm->set($di);
      unset($di, $oneParticipantData);

      $type = 'accept_member';
      $title = 'Approve';
      $text = $pelanggan->band_fnama . " approve " . $requestedMember->band_fnama . "'s member request";

      if($queryResult->show_welcome_post == 1){
        $di = array();
        $di['nation_code'] = $nation_code;
        $di['i_group_id'] = $group_id;
        $di['b_user_id'] = $pelanggan->id;
        $di['type'] = "welcome message";
        $di['deskripsi'] = "Mr./Ms. ".$requestedMember->band_fnama." joined as a new member today.<br />Please warmly welcome him/her with a comment.";
        $di['cdate'] = 'NOW()';
        $endDoWhile = 0;
        do{
          $id = $this->GUIDv4();
          $checkId = $this->igpostm->checkId($nation_code, $id);
          if($checkId == 0){
            $endDoWhile = 1;
          }
        }while($endDoWhile == 0);
        $di['id'] = $id;
        $this->igpostm->set($di);
        unset($di);
      }
    } else if(strtolower($get_action) == "reject") {
      // $res = $this->igparticipantm->setStatusMember($nation_code, $group_id, $b_user_id, "reject");
      $res = $this->igparticipantm->del($nation_code, $group_id, $b_user_id);

      $type = 'reject_member';
      $title = 'Reject';
      $text = $pelanggan->band_fnama . " reject " . $requestedMember->band_fnama . "'s member request";
    }

    // insert to admin activity log
    $di = array();
    $di['nation_code'] = $nation_code;
    $di['i_group_id'] = $group_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['type'] = $type;
    $di['title'] = $title;
    $di['text'] = $text;
    $di['image'] = $pelanggan->band_image;
    $di['cdate'] = 'NOW()';
    $di['is_active'] = 1;
    $endDoWhile = 0;
    do {
      $id = $this->GUIDv4();
      $checkId = $this->igaalm->checkId($nation_code, $id);
      if($checkId == 0) {
        $endDoWhile = 1;
      }
    } while ($endDoWhile == 0);
    $di['id'] = $id;
    $this->igaalm->set($di);
    if(!$res){
      $this->igparticipantm->trans_rollback();
      $this->igparticipantm->trans_end();
      $this->status = 1107;
      $this->message = 'Error, please try again later';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->igparticipantm->trans_commit();
    $this->igparticipantm->trans_end();

    if(strtolower($get_action) == "accept") {
      $user = $this->bu->getById($nation_code, $b_user_id);

      $dpe = array();
      $dpe['nation_code'] = $nation_code;
      $dpe['b_user_id'] = $b_user_id;
      $dpe['type'] = "band_group_join_request_accept";
      if($user->language_id == 2) {
        $dpe['judul'] = "Reply to Join Request";
        $dpe['teks'] =  "Anda telah diterima";
      } else {
        $dpe['judul'] = "Reply to Join Request";
        $dpe['teks'] =  "You're accepted";
      }
      $dpe['group_name'] = $queryResult->name;
      $dpe['i_group_id'] = $queryResult->id;
      $dpe['gambar'] = 'media/pemberitahuan/community.png';
      $dpe['cdate'] = "NOW()";
      $extras = new stdClass();
      $extras->i_group_id = $queryResult->id;
      if($user->language_id == 2) { 
        $extras->judul = "Reply to Join Request";
        $extras->teks =  "Anda telah diterima";
      } else {
        $extras->judul = "Reply to Join Request";
        $extras->teks =  "You're accepted";
      }
      $dpe['extras'] = json_encode($extras);
      $endDoWhile = 0;
      do{
        $notifId = $this->GUIDv4();
        $checkId = $this->ignotifm->checkId($nation_code, $notifId);
        if($checkId == 0){
          $endDoWhile = 1;
        }
      }while($endDoWhile == 0);
      $dpe['id'] = $notifId;
      $this->ignotifm->set($dpe);

      if ($user->is_band_push_notif == "1" && $user->is_active == 1) {
        if($user->device == "ios"){
          //push notif to ios
          $device = "ios"; //jenis device
        }else{
          //push notif to android
          $device = "android"; //jenis device
        }
        $tokens = $user->fcm_token; //device token
        if(!is_array($tokens)) $tokens = array($tokens);
        if($user->language_id == 2){
          $title = "Reply to Join Request";
          $message = "Anda telah diterima";
        } else {
          $title = "Reply to Join Request";
          $message = "You're accepted";
        }
        $image = 'media/pemberitahuan/community.png';
        $type = 'band_group_join_request_accept';
        $payload = new stdClass();
        $payload->i_group_id = $queryResult->id;
        if($user->language_id == 2) {
          $payload->judul = "Reply to Join Request";
          $payload->teks = "Anda telah diterima";
        } else {
          $payload->judul = "Reply to Join Request";
          $payload->teks = "You're accepted";
        }
        $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
      }
    }else if(strtolower($get_action) == "reject"){
      $user = $this->bu->getById($nation_code, $b_user_id);

      $dpe = array();
      $dpe['nation_code'] = $nation_code;
      $dpe['b_user_id'] = $b_user_id;
      $dpe['type'] = "band_group_join_request_reject";
      if($user->language_id == 2) {
        $dpe['judul'] = "Reply to Join Request";
        $dpe['teks'] =  "Anda telah ditolak";
      } else {
        $dpe['judul'] = "Reply to Join Request";
        $dpe['teks'] =  "You're rejected";
      }
      $dpe['group_name'] = $queryResult->name;
      $dpe['i_group_id'] = $queryResult->id;
      $dpe['gambar'] = 'media/pemberitahuan/community.png';
      $dpe['cdate'] = "NOW()";
      $extras = new stdClass();
      $extras->i_group_id = $queryResult->id;
      if($user->language_id == 2) { 
        $extras->judul = "Reply to Join Request";
        $extras->teks =  "Anda telah ditolak";
      } else {
        $extras->judul = "Reply to Join Request";
        $extras->teks =  "You're rejected";
      }
      $dpe['extras'] = json_encode($extras);
      $endDoWhile = 0;
      do{
        $notifId = $this->GUIDv4();
        $checkId = $this->ignotifm->checkId($nation_code, $notifId);
        if($checkId == 0){
          $endDoWhile = 1;
        }
      }while($endDoWhile == 0);
      $dpe['id'] = $notifId;
      $this->ignotifm->set($dpe);

      if ($user->is_band_push_notif == "1" && $user->is_active == 1) {
        if($user->device == "ios"){
          //push notif to ios
          $device = "ios"; //jenis device
        }else{
          //push notif to android
          $device = "android"; //jenis device
        }
        $tokens = $user->fcm_token; //device token
        if(!is_array($tokens)) $tokens = array($tokens);
        if($user->language_id == 2){
          $title = "Reply to Join Request";
          $message = "Anda telah ditolak";
        } else {
          $title = "Reply to Join Request";
          $message = "You're rejected";
        }
        $image = 'media/pemberitahuan/community.png';
        $type = 'band_group_join_request_reject';
        $payload = new stdClass();
        $payload->i_group_id = $queryResult->id;
        if($user->language_id == 2) {
          $payload->judul = "Reply to Join Request";
          $payload->teks = "Anda telah ditolak";
        } else {
          $payload->judul = "Reply to Join Request";
          $payload->teks = "You're rejected";
        }
        $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
      }
    }

    $data['status'] = $get_action;

    $this->status = 200;
    $this->message = "Success";

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function kick_member()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['member_status'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $timezone = $this->input->get('timezone');
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $group_id = trim($this->input->post('group_id'));
    if (strlen($group_id)<3) {
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igm->getById($nation_code, $group_id);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    if ($queryResult->b_user_id != $pelanggan->id) {
      $stillParticipant = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $queryResult->id, $pelanggan->id);
      if ($stillParticipant->is_owner == "0" && $stillParticipant->is_co_admin == "0") {
        $this->status = 1125;
        $this->message = "Your priviledge is limited as you're member";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }
    }

    $b_user_id = trim($this->input->post('b_user_id'));
    if (empty($b_user_id)) {
      $this->status = 1111;
      $this->message = 'b_user_id is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }
    if ($queryResult->b_user_id == $b_user_id) {
      $this->status = 1112;
      $this->message = 'Missing or invalid action';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }
    if ($pelanggan->id == $b_user_id) {
      $this->status = 1112;
      $this->message = 'Missing or invalid action';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->igparticipantm->trans_start();

    // get from chat_room
    $cr = $this->icrm->getChatRoomByID($nation_code, $queryResult->i_chat_room_id);
    $cr->b_user_ids = json_decode($cr->b_user_ids);
    if (($key = array_search($b_user_id, $cr->b_user_ids)) !== false) {
      unset($cr->b_user_ids[$key]);
    }
    $cr->b_user_ids = array_values($cr->b_user_ids);
    $du = array();
    $du['b_user_ids'] = json_encode($cr->b_user_ids);
    $du['total_people_chat_room'] = count($cr->b_user_ids);
    $this->icrm->update($nation_code, $queryResult->i_chat_room_id, $du);
    unset($du);

    $this->icpm->del($nation_code, $queryResult->i_chat_room_id, $b_user_id);

    $du = array();
    $du['total_people'] = count($cr->b_user_ids);
    $this->igm->update($nation_code, $group_id, $du);
    unset($du);

    $kickedMemberData = $this->bu->getById($nation_code, $b_user_id);
    $chatRoomList = $this->icrm->getAllByCustom($nation_code, $group_id, $b_user_id);
    foreach($chatRoomList AS $chatRoom){
      $chatRoom->b_user_ids = json_decode($chatRoom->b_user_ids);
      if (($key = array_search($b_user_id, $chatRoom->b_user_ids)) !== false) {
        unset($chatRoom->b_user_ids[$key]);
      }
      $chatRoom->b_user_ids = array_values($chatRoom->b_user_ids);

      $di = array();
      $di['nation_code'] = $nation_code;
      $di['i_chat_room_id'] = $chatRoom->id;
      $di['b_user_id'] = 0;
      $di['type'] = "announcement";
      $di['message'] = $kickedMemberData->band_fnama." has left the chat.";
      $di['message_indonesia'] = $kickedMemberData->band_fnama." telah meninggalkan obrolan";
      $di['cdate'] = date('Y-m-d H:i:s', strtotime('+ 1 second'));
      $endDoWhile = 0;
      do{
        $chat_id = $this->GUIDv4();
        $checkId = $this->icm->checkId($nation_code, $chat_id);
        if($checkId == 0){
            $endDoWhile = 1;
        }
      }while($endDoWhile == 0);
      $di['id'] = $chat_id;
      $this->icm->set($di);
      unset($di);

      //set unread in table i_chat_read
      $insertArray = array();
      foreach($chatRoom->b_user_ids AS $participant){
          $du = array();
          $du['nation_code'] = $nation_code;
          $du['b_user_id'] = $participant;
          $du['i_chat_room_id'] = $chatRoom->id;
          $du['i_chat_id'] = $chat_id;
          $du['is_read'] = 0;
          $du['cdate'] = "NOW()";
          $insertArray[] = $du;
      }
      unset($participant);

      $chunkInsertArray = array_chunk($insertArray,50);
      foreach($chunkInsertArray AS $chunk){
          //insert multi
          $this->icreadm->setMass($chunk);
      }
      unset($insertArray, $chunkInsertArray, $chunk);

      $this->icpm->del($nation_code, $chatRoom->id, $b_user_id);

      $du = array();
      $du['b_user_ids'] = json_encode($chatRoom->b_user_ids);
      $du['total_people_chat_room'] = count($chatRoom->b_user_ids);
      if($du['total_people_chat_room'] == 0){
        $du['is_active'] = 0;
      }
      $this->icrm->update($nation_code, $chatRoom->id, $du);
      unset($du);
    }

    $res = $this->igparticipantm->del($nation_code, $group_id, $b_user_id);
    if(!$res){
      $this->igparticipantm->trans_rollback();
      $this->igparticipantm->trans_end();
      $this->status = 1107;
      $this->message = 'Error, please try again later';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->igparticipantm->trans_commit();
    $this->igparticipantm->trans_end();

    // insert to admin activity log
    $getUserData = $this->bu->getById($nation_code, $b_user_id);

    $di = array();
    $di['nation_code'] = $nation_code;
    $di['i_group_id'] = $group_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['type'] = 'kick_member';
    $di['title'] = 'Kick Member';
    $di['text'] = $pelanggan->band_fnama . " kick " . $getUserData->band_fnama . " from group";
    $di['image'] = $pelanggan->band_image;
    $di['cdate'] = 'NOW()';
    $di['is_active'] = 1;
    $endDoWhile = 0;
    do {
      $id = $this->GUIDv4();
      $checkId = $this->igaalm->checkId($nation_code, $id);
      if($checkId == 0) {
        $endDoWhile = 1;
      }
    } while ($endDoWhile == 0);
    $di['id'] = $id;
    $this->igaalm->set($di);

    $this->status = 200;
    $this->message = "Success";
    $data['member_status'] = 'kicked';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function leave_member()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['member_status'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $timezone = $this->input->get('timezone');
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $group_id = trim($this->input->post('group_id'));
    if (strlen($group_id)<3) {
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igm->getById($nation_code, $group_id);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    if ($queryResult->b_user_id == $pelanggan->id) {
      $this->status = 1113;
      $this->message = "owner(you) cannot leave club";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $checkMember = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $group_id, $pelanggan->id);
    if (!isset($checkMember->b_user_id)){
      $this->status = 1103;
      $this->message = 'You are not the member of this club';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->igparticipantm->trans_start();

    // get from chat_room
    $cr = $this->icrm->getChatRoomByID($nation_code, $queryResult->i_chat_room_id);
    $cr->b_user_ids = json_decode($cr->b_user_ids);
    if (($key = array_search($pelanggan->id, $cr->b_user_ids)) !== false) {
      unset($cr->b_user_ids[$key]);
    }
    $cr->b_user_ids = array_values($cr->b_user_ids);
    $du = array();
    $du['b_user_ids'] = json_encode($cr->b_user_ids);
    $du['total_people_chat_room'] = count($cr->b_user_ids);
    $this->icrm->update($nation_code, $queryResult->i_chat_room_id, $du);
    unset($du);

    $this->icpm->del($nation_code, $queryResult->i_chat_room_id, $pelanggan->id);

    $du = array();
    $du['total_people'] = count($cr->b_user_ids);
    $this->igm->update($nation_code, $group_id, $du);
    unset($du);

    // $kickedMemberData = $this->bu->getById($nation_code, $pelanggan->id);
    $chatRoomList = $this->icrm->getAllByCustom($nation_code, $group_id, $pelanggan->id);
    foreach($chatRoomList AS $chatRoom){
      $chatRoom->b_user_ids = json_decode($chatRoom->b_user_ids);
      if (($key = array_search($pelanggan->id, $chatRoom->b_user_ids)) !== false) {
        unset($chatRoom->b_user_ids[$key]);
      }
      $chatRoom->b_user_ids = array_values($chatRoom->b_user_ids);

      $di = array();
      $di['nation_code'] = $nation_code;
      $di['i_chat_room_id'] = $chatRoom->id;
      $di['b_user_id'] = 0;
      $di['type'] = "announcement";
      $di['message'] = $pelanggan->band_fnama." has left the chat.";
      $di['message_indonesia'] = $pelanggan->band_fnama." telah meninggalkan obrolan";
      $di['cdate'] = date('Y-m-d H:i:s', strtotime('+ 1 second'));
      $endDoWhile = 0;
      do{
        $chat_id = $this->GUIDv4();
        $checkId = $this->icm->checkId($nation_code, $chat_id);
        if($checkId == 0){
            $endDoWhile = 1;
        }
      }while($endDoWhile == 0);
      $di['id'] = $chat_id;
      $this->icm->set($di);
      unset($di);

      //set unread in table i_chat_read
      $insertArray = array();
      foreach($chatRoom->b_user_ids AS $participant){
          $du = array();
          $du['nation_code'] = $nation_code;
          $du['b_user_id'] = $participant;
          $du['i_chat_room_id'] = $chatRoom->id;
          $du['i_chat_id'] = $chat_id;
          $du['is_read'] = 0;
          $du['cdate'] = "NOW()";
          $insertArray[] = $du;
      }
      unset($participant);

      $chunkInsertArray = array_chunk($insertArray,50);
      foreach($chunkInsertArray AS $chunk){
          //insert multi
          $this->icreadm->setMass($chunk);
      }
      unset($insertArray, $chunkInsertArray, $chunk);

      $this->icpm->del($nation_code, $chatRoom->id, $pelanggan->id);

      $du = array();
      $du['b_user_ids'] = json_encode($chatRoom->b_user_ids);
      $du['total_people_chat_room'] = count($chatRoom->b_user_ids);
      if($du['total_people_chat_room'] == 0){
        $du['is_active'] = 0;
      }
      $this->icrm->update($nation_code, $chatRoom->id, $du);
      unset($du);
    }

    $res = $this->igparticipantm->del($nation_code, $group_id, $pelanggan->id);
    if(!$res){
      $this->igparticipantm->trans_rollback();
      $this->igparticipantm->trans_end();
      $this->status = 1101;
      $this->message = 'Error while leave, please try again';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->igparticipantm->trans_commit();
    $this->igparticipantm->trans_end();

    $this->status = 200;
    $this->message = "Success";
    $data['member_status'] = 'leave';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function generateQRCode($nation_code, $request_id)
  {
    // credit : https://phpqrcode.sourceforge.net/#demo
    // function goes to phpqrcode.php -> Class QRimage - > function png
    // $file_name = $nation_code.'-'.$id.'.png';
    $content = "https://sellon.net/group/".$request_id;

    $targetdir = $this->media_group_qrcode;
    $targetdircheck = realpath(SENEROOT.$targetdir);
    if (empty($targetdircheck)) {
      if (PHP_OS == "WINNT") {
        if (!is_dir(SENEROOT.$targetdir)) {
          mkdir(SENEROOT.$targetdir);
        }
      } else {
        if (!is_dir(SENEROOT.$targetdir)) {
          mkdir(SENEROOT.$targetdir, 0775);
        }
      }
    }

    $tahun = date("Y");
    $targetdir = $targetdir.DIRECTORY_SEPARATOR.$tahun;
    $targetdircheck = realpath(SENEROOT.$targetdir);
    if (empty($targetdircheck)) {
      if (PHP_OS == "WINNT") {
        if (!is_dir(SENEROOT.$targetdir)) {
          mkdir(SENEROOT.$targetdir);
        }
      } else {
        if (!is_dir(SENEROOT.$targetdir)) {
          mkdir(SENEROOT.$targetdir, 0775);
        }
      }
    }

    $bulan = date("m");
    $targetdir = $targetdir.DIRECTORY_SEPARATOR.$bulan;
    $targetdircheck = realpath(SENEROOT.$targetdir);
    if (empty($targetdircheck)) {
      if (PHP_OS == "WINNT") {
        if (!is_dir(SENEROOT.$targetdir)) {
          mkdir(SENEROOT.$targetdir);
        }
      } else {
        if (!is_dir(SENEROOT.$targetdir)) {
          mkdir(SENEROOT.$targetdir, 0775);
        }
      }
    }

    $filename = $nation_code.'-'.$request_id;
    $filename = $filename.".png";

    QRcode::png($content, $targetdir.$filename, QR_ECLEVEL_H, 6, 2); // creates and save file

    $qr = new stdClass();
    $qr->status = 200;
    $qr->message = 'success';
    $qr->qrcode_url = $targetdir.$filename;
    return $qr;
  }

  public function generateInviteCode()
  {
    $length = 4;
    $range_char = range('A', 'Z');
    shuffle($range_char);
    $word = substr(implode($range_char), 0, $length);

    // create invite code digit
    $code = rand(0000, 9999);

    $invite_code = new stdClass();
    $invite_code->status = 200;
    $invite_code->message = 'success';
    $invite_code->word = $word;
    $invite_code->digit = $code;
    return $invite_code;
  }

  public function share_invitation_url()
  {
    $data = array();
    $data["url"] = "";

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $group_id = $this->input->get('group_id');
    $queryResult = $this->igm->getById($nation_code, $group_id);
    if (!isset($queryResult->id)) {
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $checkMember = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $group_id, $pelanggan->id);
    if (!isset($checkMember->b_user_id)){
      $this->status = 1103;
      $this->message = 'You are not the member of this club';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    // update share_invitation_url
    $this->igm->updateTotal($nation_code, $group_id, "share_invitation_url", '+', 1);

    // create share data
    $di = array();
    $di['nation_code'] = $nation_code;
    $di['i_group_id'] = $group_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['cdate'] = 'NOW()';
    $di['type'] = 'invitation_url';
    $endDoWhile = 0;
    do {
      $id = $this->GUIDv4();
      $checkId = $this->igrsm->checkId($nation_code, $id);
      if($checkId == 0) {
        $endDoWhile = 1;
      }
    } while ($endDoWhile == 0);
    $di['id'] = $id;
    $this->igrsm->set($di);

    // $queryResult->cdate_text = $this->humanTiming($queryResult->cdate, null, $pelanggan->language_id);
    // $cdate_texte = date_create($queryResult->cdate);
    // $queryResult->created_on = date_format($cdate_texte, "M Y");
    // $queryResult->cdate = $this->customTimezone($queryResult->cdate, $this->default_timezone);
    // $queryResult->name = html_entity_decode($queryResult->name,ENT_QUOTES);
    // $queryResult->image = $this->cdn_url($queryResult->image_thumb);
    // $queryResult->qrcode_url = $this->cdn_url($queryResult->qrcode_url);
    // $queryResult->url = "https://sellon.net/group/".$id;
    // $queryResult->status_member = $this->__statusMember($nation_code, $queryResult->id, $pelanggan->id);
    // $data['group'] = $queryResult;

    $data["url"] = "https://sellon.net/group/".$id;

    $this->status = 200;
    $this->message = 'success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
	}

  public function share_invitation_code()
  {
    $data = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $group_id = trim($this->input->get('group_id'));
    if (strlen($group_id)<3) {
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igm->getById($nation_code, $group_id);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $checkMember = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $group_id, $pelanggan->id);
    if (!isset($checkMember->b_user_id)){
      $this->status = 1103;
      $this->message = 'You are not the member of this club';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    // check in table if invite code digit & word is not exist
    $checkInvitationCode = $this->igm->checkInvitationCode($nation_code, $group_id);
    if(empty($checkInvitationCode->invite_code_digit) && empty($checkInvitationCode->invite_code_word)){
      // if invitation code not found, generate new one
      $invite_code = $this->generateInviteCode();

      $du = array();
      if(isset($invite_code->status)) {
        if ($invite_code->status == 200) { 
          $du['invite_code_digit'] = $invite_code->digit;
          $du['invite_code_word'] = $invite_code->word;
        }
      }
      $res = $this->igm->update($nation_code, $group_id, $du);
      if($res) {
        $query = $this->igm->getById($nation_code, $group_id);
        $data->invite_code_digit = $query->invite_code_digit;
        $data->invite_code_word = $query->invite_code_word;
      }
    }else{
      $data->invite_code_digit = $queryResult->invite_code_digit;
      $data->invite_code_word = $queryResult->invite_code_word;
    }

    // update share_invitation_code
    $this->igm->updateTotal($nation_code, $group_id, "share_invitation_code", '+', 1);

    $this->status = 200;
    $this->message = 'success';
    
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function recreate_invitation_code()
  {
    $data = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $group_id = trim($this->input->get('group_id'));
    if (strlen($group_id)<3) {
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igm->getById($nation_code, $group_id);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    if ($queryResult->b_user_id != $pelanggan->id) {
      $this->status = 1125;
      $this->message = "Your priviledge is limited as you're member";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    // generate invite code
    $invite_code = $this->generateInviteCode();
    if(isset($invite_code->status)) {
      if ($invite_code->status==200) { 
        $invite_code_digit = $invite_code->digit;
        $invite_code_word = $invite_code->word;
      }
    }

    // update group invite code digit
    $du = array();
    $du['invite_code_digit'] = $invite_code_digit;
    $du['invite_code_word'] = $invite_code_word;
    $this->igm->update($nation_code, $group_id, $du);

    // update share_invitation_code
    $this->igm->updateTotal($nation_code, $group_id, "share_invitation_code", '+', 1);

    $query = $this->igm->getById($nation_code, $group_id);
    $data->invite_code_digit = $query->invite_code_digit;
    $data->invite_code_word = $query->invite_code_word;

    $this->status = 200;
    $this->message = 'success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function share_qrcode()
  {
    $data = array();
    $data["qrcode_url"] = "";

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $group_id = $this->input->get('group_id');
    $queryResult = $this->igm->getById($nation_code, $group_id);
    if (!isset($queryResult->id)) {
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $checkMember = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $group_id, $pelanggan->id);
    if (!isset($checkMember->b_user_id)){
      $this->status = 1103;
      $this->message = 'You are not the member of this club';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    // check in table if qrcode is not exist
    // $checkQrCode = $this->igm->checkQrCode($nation_code, $group_id);
    // if($checkQrCode == "0"){
    //   // if qrcode is not found, generate new one
    //   $generate = $this->generateQRCode($nation_code, $group_id);

    //   $du = array();
    //   if(isset($generate->status)) {
    //     if ($generate->status==200) { 
    //       $du['qrcode_url'] = $generate->qrcode_url;
    //     }
    //   }
    //   $res = $this->igm->update($nation_code, $group_id, $du);
    //   if($res) {
    //     $queryResult = $this->igm->getById($nation_code, $group_id);
    //   }
    // }

    // create share data
    $di = array();
    $di['nation_code'] = $nation_code;
    $di['i_group_id'] = $group_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['cdate'] = 'NOW()';
    $di['type'] = 'share_qrcode';
    $endDoWhile = 0;
    do {
      $id = $this->GUIDv4();
      $checkId = $this->igrsm->checkId($nation_code, $id);
      if($checkId == 0) {
        $endDoWhile = 1;
      }
    } while ($endDoWhile == 0);
    $di['id'] = $id;
    $this->igrsm->set($di);

    // generate qrcode with request_id
    $generate = $this->generateQRCode($nation_code, $id);
    $du = array();
    if(isset($generate->status)) {
      if ($generate->status==200) { 
        $du['qrcode_url'] = $generate->qrcode_url;
      }
    }

    $res = $this->igm->update($nation_code, $group_id, $du);
    if($res) {

      // update share_qrcode
      $this->igm->updateTotal($nation_code, $group_id, "share_qrcode", '+', 1);

      $queryResult = $this->igm->getById($nation_code, $group_id);
    }

    // $queryResult->cdate_text = $this->humanTiming($queryResult->cdate, null, $pelanggan->language_id);
    // $cdate_texte = date_create($queryResult->cdate);
    // $queryResult->created_on = date_format($cdate_texte, "M Y");
    // $queryResult->cdate = $this->customTimezone($queryResult->cdate, $this->default_timezone);
    // $queryResult->image = $this->cdn_url($queryResult->image_thumb);
    // $queryResult->qrcode_url = $this->cdn_url($queryResult->qrcode_url);
    // $queryResult->url = "https://sellon.net/group/".$id;
    // $queryResult->status_member = $this->__statusMember($nation_code, $queryResult->id, $pelanggan->id);
    // $data['group'] = $queryResult;

    $data["qrcode_url"] = $this->cdn_url($queryResult->qrcode_url);

    $this->status = 200;
    $this->message = 'success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
	}

  public function enter_invitation_code()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['group'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    // populate input
    $digit = $this->input->post('digit');
    if (empty($digit)) {
      $this->status = 701;
      $this->message = 'Code is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $word = $this->input->post('word');
    if (empty($word)) {
      $this->status = 702;
      $this->message = 'Word is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $query = $this->igm->getDataByInvitationCode($nation_code, $digit, $word);
    if($query == new stdClass()){
      $this->status = 703;
      $this->message = 'Please, provide a valid value. (code & word: 4 digits)';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    if($query->group_type == "public") {
      //check already member or not
      $checkAlreadyParticipant = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $query->group_id, $pelanggan->id);
      if(!isset($checkAlreadyParticipant->b_user_id)){
        //start transaction and lock table
        $this->igparticipantm->trans_start();

        $di = array();
        $di['nation_code'] = $nation_code;
        $di['i_group_id'] = $query->group_id;
        $di['b_user_id'] = $pelanggan->id;
        $di['cdate'] = 'NOW()';
        $di['is_accept'] = 1;
        $res = $this->igparticipantm->set($di);
        if (!$res) {
          $this->igparticipantm->trans_rollback();
          $this->igparticipantm->trans_end();
          $this->status = 1107;
          $this->message = "Error, please try again later";
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
          die();
        }

        // get from chat_room
        $cr = $this->icrm->getChatRoomByID($nation_code, $query->i_chat_room_id);
        $cr->b_user_ids = json_decode($cr->b_user_ids);
        $cr->b_user_ids[] = $pelanggan->id;

        $du = array();
        $du['b_user_ids'] = json_encode($cr->b_user_ids);
        $du['total_people_chat_room'] = count($cr->b_user_ids);
        $this->icrm->update($nation_code, $query->i_chat_room_id, $du);
        unset($du);

        $du = array();
        $du['total_people'] = count($cr->b_user_ids);
        $this->igm->update($nation_code, $query->group_id, $du);
        unset($du);

        $di = array();
        $di['nation_code'] = $nation_code;
        $di['i_chat_room_id'] = $query->i_chat_room_id;
        $di['b_user_id'] = $pelanggan->id;
        $di['cdate'] = 'NOW()';
        $di['last_delete_chat'] = 'NOW()';
        $di['is_read'] = 1;
        $this->icpm->set($di);
        unset($di);

        $this->igparticipantm->trans_commit();
        $this->igparticipantm->trans_end();
      }

      $queryResult = $this->igm->getById($nation_code, $query->group_id);
      $queryResult->cdate_text = $this->humanTiming($queryResult->cdate, null, $pelanggan->language_id);
      $cdate_texte = date_create($queryResult->cdate);
      $queryResult->created_on = date_format($cdate_texte, "M Y");
      $queryResult->cdate = $this->customTimezone($queryResult->cdate, $this->default_timezone);
      $queryResult->image = $this->cdn_url($queryResult->image_thumb);
      $queryResult->qrcode_url = $this->cdn_url($queryResult->qrcode_url);
      $queryResult->status_member = $this->__statusMember($nation_code, $query->group_id, $pelanggan->id);
      $data['group'] = $queryResult;
    } else {
      // create share data
      $di = array();
      $endDoWhile = 0;
      do {
        $id = $this->GUIDv4();
        $checkId = $this->igrsm->checkId($nation_code, $id);
        if($checkId == 0) {
          $endDoWhile = 1;
        }
      } while ($endDoWhile == 0);
      $di['id'] = $id;
      $di['nation_code'] = $nation_code;
      $di['i_group_id'] = $query->group_id;
      $di['b_user_id'] = $query->b_user_id;
      $di['cdate'] = 'NOW()';
      $di['type'] = 'invitation_code';
      $this->igrsm->set($di);

      $queryData = $this->igrsm->getById($nation_code, $id);
      $data['group']->inviter_name = $queryData->b_user_band_fnama;
      $data['group']->inviter_image = $this->cdn_url($queryData->b_user_band_image);
      $data['group']->group_id = $queryData->group_id;
      $data['group']->status = "User already request to join";

      $queryResult = $this->igm->getById($nation_code, $queryData->group_id);
      $data['group']->b_user_id = $queryResult->b_user_id;
      $data['group']->total_people = $queryResult->total_people;
      $data['group']->group_type = $queryResult->group_type;
      $data['group']->name = $queryResult->name;
      $data['group']->image =  $this->cdn_url($queryResult->image_thumb);
      $data['group']->cdate = $queryResult->cdate;
      $data['group']->description = $queryResult->description;
      $data['group']->size_limit = $queryResult->size_limit;
      $data['group']->need_admin_approval = $queryResult->need_admin_approval;
      $data['group']->show_welcome_post = $queryResult->show_welcome_post;
      $data['group']->status_member = $queryResult->status_member;
      $data['group']->description_images = array();
      $data['group']->description_location = array();

      $attachmentImage = $this->igam->getByGroupId($nation_code, $data['group']->group_id, "all", "image");
      foreach($attachmentImage as &$atc_image) {
        if($atc_image->jenis == 'image' || $atc_image->jenis == 'video'){
          $atc_image->url = $this->cdn_url($atc_image->url);
          $atc_image->url_thumb = $this->cdn_url($atc_image->url_thumb);
          $data['group']->description_images[] = $atc_image;
        }
      }
      unset($attachmentImage);

      $attachmentLocation = $this->igam->getByGroupId($nation_code, $data['group']->group_id, "all", "location");
      foreach($attachmentLocation as &$atc_location) {
        $data['group']->description_location[] = $atc_location;
      }
      unset($attachmentLocation);
    }

    $this->status = 200;
    $this->message = "Success";

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function request_to_join()
  {
    $data = array();
    $data['status_user'] = "";

    $this->seme_log->write("api_mobile", 'API_Mobile/group::request_to_join POST: '.json_encode($_GET));

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $group_id = trim($this->input->get('group_id'));
    if (strlen($group_id)<3) {
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igm->getById($nation_code, $group_id);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $share_id = trim($this->input->get('share_id'));
    if (strlen($share_id)>3) {
      $shareData = $this->igrsm->getById($nation_code, $share_id);
      if(!isset($shareData->b_user_id)){
        $shareData = new stdClass();
        $shareData->b_user_id = "0";
      }
    }else{
      $shareData = new stdClass();
      $shareData->b_user_id = "0";
    }

    //check already member or not
    $checkAlreadyParticipant = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $group_id, $pelanggan->id);
    if(!isset($checkAlreadyParticipant->b_user_id)){
      //start transaction and lock table
      $this->igparticipantm->trans_start();

      $di = array();
      $di['nation_code'] = $nation_code;
      $di['i_group_id'] = $group_id;
      $di['b_user_id'] = $pelanggan->id;
      $di['b_user_id_inviter'] = $shareData->b_user_id;
      $di['cdate'] = 'NOW()';
      if($queryResult->group_type == "public"){
        $di['is_accept'] = 1;
      }else{
        $di['is_request'] = 1;
      }
      $res = $this->igparticipantm->set($di);
      if (!$res) {
        $this->igparticipantm->trans_rollback();
        $this->igparticipantm->trans_end();
        $this->status = 1107;
        $this->message = "Error, please try again later";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }

      if($queryResult->group_type == "public"){
        // get from chat_room
        $cr = $this->icrm->getChatRoomByID($nation_code, $queryResult->i_chat_room_id);
        $cr->b_user_ids = json_decode($cr->b_user_ids);
        $cr->b_user_ids[] = $pelanggan->id;

        $du = array();
        $du['b_user_ids'] = json_encode($cr->b_user_ids);
        $du['total_people_chat_room'] = count($cr->b_user_ids);
        $this->icrm->update($nation_code, $queryResult->i_chat_room_id, $du);
        unset($du);

        $du = array();
        $du['total_people'] = count($cr->b_user_ids);
        $this->igm->update($nation_code, $group_id, $du);
        unset($du);

        $di = array();
        $di['nation_code'] = $nation_code;
        $di['i_chat_room_id'] = $queryResult->i_chat_room_id;
        $di['b_user_id'] = $pelanggan->id;
        $di['cdate'] = 'NOW()';
        $di['last_delete_chat'] = 'NOW()';
        $di['is_read'] = 1;
        $this->icpm->set($di);
        unset($di);

        $alreadyJoinedBefore = $this->igphm->getByGroupidUserid($nation_code, $group_id, $pelanggan->id);
        if(!isset($alreadyJoinedBefore->cdate)){
          if($shareData->b_user_id == "0"){
            $this->give_spt($nation_code, "self join", $queryResult->b_user_id, $group_id);
          }else{
            $this->give_spt($nation_code, "inviter", $shareData->b_user_id, $group_id);
          }
        }
        unset($alreadyJoinedBefore);

        $di = array();
        $di['nation_code'] = $nation_code;
        $di['i_group_id'] = $group_id;
        $di['b_user_id'] = $pelanggan->id;
        $di['b_user_id_inviter'] = $shareData->b_user_id;
        $di['cdate'] = 'NOW()';
        $endDoWhile = 0;
        do{
          $igphmId = $this->GUIDv4();
          $checkId = $this->igphm->checkId($nation_code, $igphmId);
          if($checkId == 0){
            $endDoWhile = 1;
          }
        }while($endDoWhile == 0);
        $di['id'] = $igphmId;
        $this->igphm->set($di);
        unset($di, $shareData);

        $data['status_user'] = "User joined";
      }else{
        $data['status_user'] = "User already request to join";

        $user = $this->bu->getById($nation_code, $queryResult->b_user_id);

        $dpe = array();
        $dpe['nation_code'] = $nation_code;
        $dpe['b_user_id'] = $queryResult->b_user_id;
        $dpe['type'] = "band_group_join_request";
        if($user->language_id == 2) {
          $dpe['judul'] = "Permintaan Masuk Baru";
          $dpe['teks'] =  "Ada permintaan masuk baru\n(".$pelanggan->band_fnama.")";
        } else {
          $dpe['judul'] = "New Join Request";
          $dpe['teks'] =  "There is new join request\n(".$pelanggan->band_fnama.")";
        }
        $dpe['group_name'] = $queryResult->name;
        $dpe['i_group_id'] = $group_id;
        $dpe['gambar'] = 'media/pemberitahuan/community.png';
        $dpe['cdate'] = "NOW()";
        $extras = new stdClass();
        $extras->i_group_id = $group_id;
        $extras->group_band_name = $queryResult->name;
        if($user->language_id == 2) { 
          $extras->judul = "Permintaan Masuk Baru";
          $extras->teks =  "Ada permintaan masuk baru\n(".$pelanggan->band_fnama.")";
        } else {
          $extras->judul = "New Join Request";
          $extras->teks =  "There is new join request\n(".$pelanggan->band_fnama.")";
        }
        $dpe['extras'] = json_encode($extras);
        $endDoWhile = 0;
        do{
          $notifId = $this->GUIDv4();
          $checkId = $this->ignotifm->checkId($nation_code, $notifId);
          if($checkId == 0){
            $endDoWhile = 1;
          }
        }while($endDoWhile == 0);
        $dpe['id'] = $notifId;
        $this->ignotifm->set($dpe);

        if ($user->is_band_push_notif == "1" && $user->is_active == 1) {
          if($user->device == "ios"){
            //push notif to ios
            $device = "ios"; //jenis device
          }else{
            //push notif to android
            $device = "android"; //jenis device
          }
          $tokens = $user->fcm_token; //device token
          if(!is_array($tokens)) $tokens = array($tokens);
          if($user->language_id == 2){
            $title = "Permintaan Masuk Baru";
            $message = "Ada permintaan masuk baru\n(".$pelanggan->band_fnama.")";
          } else {
            $title = "New Join Request";
            $message = "There is new join request\n(".$pelanggan->band_fnama.")";
          }
          $image = 'media/pemberitahuan/community.png';
          $type = 'band_group_join_request';
          $payload = new stdClass();
          $payload->i_group_id = $group_id;
          $payload->group_band_name = $queryResult->name;
          if($user->language_id == 2) {
            $payload->judul = "Permintaan Masuk Baru\n(".$pelanggan->band_fnama.")";
            $payload->teks = "Ada permintaan masuk baru\n(".$pelanggan->band_fnama.")";
          } else {
            $payload->judul = "New Join Request\n(".$pelanggan->band_fnama.")";
            $payload->teks = "There is new join request\n(".$pelanggan->band_fnama.")";
          }
          $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        }
      }

      $this->igparticipantm->trans_commit();
      $this->igparticipantm->trans_end();
    }else{
      if($checkAlreadyParticipant->is_accept == "1"){
        $data['status_user'] = "User joined";
      }else if($checkAlreadyParticipant->is_request == "1"){
        $data['status_user'] = "User already request to join";
      }
    }

    $this->status = 200;
    $this->message = 'success';
    
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function request_share()
  {
    $data = new stdClass();

    $this->seme_log->write("api_mobile", 'API_Mobile/group::request_share POST: '.json_encode($_GET));

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $id_request = trim($this->input->get('id_request'));
    if (strlen($id_request)<3) {
      $this->status = 1101;
      $this->message = 'id request is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igrsm->getById($nation_code, $id_request);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'id request not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $data->inviter_name = $queryResult->b_user_band_fnama;
    $data->inviter_image = $this->cdn_url($queryResult->b_user_band_image);
    $data->group_id = $queryResult->group_id;

    // check if user already requst join to the group
    $check = $this->igparticipantm->checkIfUserAlreadyRequestJoin($nation_code, $queryResult->group_id, $pelanggan->id);
    if($check == "0") {
      $data->request_status = "not_requested";
    } else {
      $data->request_status = "requested";
    }

    // check if group is public, show need_admin_approval, show_welcome_post response
    $checkGroup = $this->igm->getById($nation_code, $queryResult->group_id);
    if($checkGroup->group_type == "public") {
      $data->need_admin_approval = $checkGroup->need_admin_approval;
      $data->show_welcome_post = $checkGroup->show_welcome_post;

      //check already member or not
      $checkAlreadyParticipant = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $queryResult->group_id, $pelanggan->id);
      if(!isset($checkAlreadyParticipant->b_user_id)){
        //start transaction and lock table
        $this->igparticipantm->trans_start();

        $di = array();
        $di['nation_code'] = $nation_code;
        $di['i_group_id'] = $queryResult->group_id;
        $di['b_user_id'] = $pelanggan->id;
        $di['b_user_id_inviter'] = $queryResult->b_user_id;
        $di['cdate'] = 'NOW()';
        $di['is_accept'] = 1;
        $res = $this->igparticipantm->set($di);
        if (!$res) {
          $this->igparticipantm->trans_rollback();
          $this->igparticipantm->trans_end();
          $this->status = 1107;
          $this->message = "Error, please try again later";
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
          die();
        }

        // get from chat_room
        $cr = $this->icrm->getChatRoomByID($nation_code, $checkGroup->i_chat_room_id);
        $cr->b_user_ids = json_decode($cr->b_user_ids);
        $cr->b_user_ids[] = $pelanggan->id;

        $du = array();
        $du['b_user_ids'] = json_encode($cr->b_user_ids);
        $du['total_people_chat_room'] = count($cr->b_user_ids);
        $this->icrm->update($nation_code, $checkGroup->i_chat_room_id, $du);
        unset($du);

        $du = array();
        $du['total_people'] = count($cr->b_user_ids);
        $this->igm->update($nation_code, $queryResult->group_id, $du);
        unset($du);

        $di = array();
        $di['nation_code'] = $nation_code;
        $di['i_chat_room_id'] = $checkGroup->i_chat_room_id;
        $di['b_user_id'] = $pelanggan->id;
        $di['cdate'] = 'NOW()';
        $di['last_delete_chat'] = 'NOW()';
        $di['is_read'] = 1;
        $this->icpm->set($di);
        unset($di);

        $alreadyJoinedBefore = $this->igphm->getByGroupidUserid($nation_code, $queryResult->group_id, $pelanggan->id);
        if(!isset($alreadyJoinedBefore->cdate)){
          if($queryResult->b_user_id == "0"){
            $this->give_spt($nation_code, "self join", $checkGroup->b_user_id, $queryResult->group_id);
          }else{
            $this->give_spt($nation_code, "inviter", $queryResult->b_user_id, $queryResult->group_id);
          }
        }
        unset($alreadyJoinedBefore);

        $di = array();
        $di['nation_code'] = $nation_code;
        $di['i_group_id'] = $queryResult->group_id;
        $di['b_user_id'] = $pelanggan->id;
        $di['b_user_id_inviter'] = $queryResult->b_user_id;
        $di['cdate'] = 'NOW()';
        $endDoWhile = 0;
        do{
          $igphmId = $this->GUIDv4();
          $checkId = $this->igphm->checkId($nation_code, $igphmId);
          if($checkId == 0){
            $endDoWhile = 1;
          }
        }while($endDoWhile == 0);
        $di['id'] = $igphmId;
        $this->igphm->set($di);
        unset($di);

        $this->igparticipantm->trans_commit();
        $this->igparticipantm->trans_end();
      }
    }

    $this->status = 200;
    $this->message = 'success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  // request join list
  public function join_request_sent()
  {
    $data = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $queryResult = $this->igparticipantm->getRequestJoinList($nation_code, $pelanggan->id);
    // $sort_col = $this->input->post("sort_col");
    // $sort_dir = $this->input->post("sort_dir");
    // $page = $this->input->post("page");
    // $page_size = $this->input->post("page_size");
    // $keyword = trim($this->input->post("keyword"));

    // //sanitize input
    // $tbl_as = $this->igparticipantm->getTblAs();
    // $sort_col = $this->__sortColRequestMember($sort_col, $tbl_as);
    // $sort_dir = $this->__sortDir($sort_dir);
    // $page = $this->__page($page);
    // $page_size = $this->__pageSize($page_size);

    // $queryResult = $this->igparticipantm->getRequestJoinList($nation_code, $pelanggan->id, $sort_col, $sort_dir, $page, $page_size);

    foreach($queryResult as $qr) {
      $date = date_create($qr->cdate);
      // $qr->cdate = date_format($date, "d M Y");
      $qr->group_image = $this->cdn_url($qr->group_image_thumb);
      $qr->cdate = $this->humanTiming($qr->cdate, null, $pelanggan->language_id);
    }

    $data['join_request'] = $queryResult;

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function cancel_join_request()
  {
    $data = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $group_id = trim($this->input->get('group_id'));
    if (strlen($group_id)<3){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igm->getById($nation_code, $group_id);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    // check if user already accept by admin
    $checkParticipant = $this->igparticipantm->checkIfUserAlreadyAcceptByAdmin($nation_code, $group_id, $pelanggan->id);
    if($checkParticipant == "1") {
      $this->status = 1114;
      $this->message = "You can't cancel request because already accept by admin";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    } 

    if($checkParticipant == "0") {
      // $this->igparticipantm->setCancelJoinRequest($nation_code, $group_id, $pelanggan->id);
      // set delete from participant
      $res = $this->igparticipantm->del($nation_code, $group_id, $pelanggan->id);
      if($res) {
        $data['join_request'] = "You already cancel join request to this club";
      }
    }

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function delete_group()
  {
    $data = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $group_id = trim($this->input->post('group_id'));
    if (strlen($group_id)<3){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igm->getById($nation_code, $group_id);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    // check if user id is owner group
    if($queryResult->b_user_id != $pelanggan->id){
      $this->status = 1125;
      $this->message = "Your priviledge is limited as you're member";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    // check if group still have member
    $checkTotalParticipant = $this->igparticipantm->checkTotalParticipant($nation_code, $group_id);
    if($checkTotalParticipant == 0) {
      // get from chat_room
      $cr = $this->icrm->getChatRoomByID($nation_code, $queryResult->i_chat_room_id);
      $du = array();
      $du['b_user_ids'] = json_encode(array());
      $du['total_people_chat_room'] = 0;
      $this->icrm->update($nation_code, $queryResult->i_chat_room_id, $du);
      unset($du);

      $this->icpm->del($nation_code, $queryResult->i_chat_room_id, '0');

      $memberData = $this->bu->getById($nation_code, $pelanggan->id);
      $chatRoomList = $this->icrm->getAllByCustom($nation_code, $group_id, $pelanggan->id);
      foreach($chatRoomList AS $chatRoom){
        $di = array();
        $di['nation_code'] = $nation_code;
        $di['i_chat_room_id'] = $chatRoom->id;
        $di['b_user_id'] = 0;
        $di['type'] = "announcement";
        $di['message'] = $memberData->band_fnama." has delete the chat room.";
        $di['message_indonesia'] = $memberData->band_fnama." telah menghapus chat room";
        $di['cdate'] = date('Y-m-d H:i:s', strtotime('+ 1 second'));
        $endDoWhile = 0;
        do{
          $chat_id = $this->GUIDv4();
          $checkId = $this->icm->checkId($nation_code, $chat_id);
          if($checkId == 0){
              $endDoWhile = 1;
          }
        }while($endDoWhile == 0);
        $di['id'] = $chat_id;
        $this->icm->set($di);
        unset($di);

        $this->icpm->del($nation_code, $chatRoom->id, '0');

        $du = array();
        $du['b_user_ids'] = json_encode(array());
        $du['total_people_chat_room'] = 0;
        $du['is_active'] = 0;
        $this->icrm->update($nation_code, $chatRoom->id, $du);
        unset($du);
      }

      $this->igparticipantm->del($nation_code, $group_id, '0');

      $du = array();
      $du['total_people'] = 0;
      $du['is_active'] = 0;
      $this->igm->update($nation_code, $group_id, $du);
    } else {
      $this->status = 1121;
      $this->message = "there are still user left in club, please kick first";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function set_pin_or_unpin()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    // $data['group'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $timezone = $this->input->post('timezone');
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $this->status = 300;
    $this->message = 'Missing one or more parameters';

    $i_group_id = trim($this->input->post('group_id'));
    if (strlen($i_group_id)<3){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igm->getById($nation_code, $i_group_id);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $i_group_id, $pelanggan->id);
    if (!isset($queryResult->b_user_id)){
      $this->status = 1103;
      $this->message = 'You are not the member of this club';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }
    unset($queryResult);

    //start transaction and lock table
    $this->igpinm->trans_start();

    $queryResult = $this->igpinm->getById($nation_code, $i_group_id, $pelanggan->id);
    if(!isset($queryResult->i_group_id)){
      $di = array();
      $di['nation_code'] = $nation_code;
      $di['i_group_id'] = $i_group_id;
      $di['b_user_id'] = $pelanggan->id;
      $di['cdate'] = 'NOW()';
      $res = $this->igpinm->set($di);
    }else{
      $res = $this->igpinm->delete($nation_code, $i_group_id, $pelanggan->id);
    }

    if (!$res) {
      $this->igpinm->trans_rollback();
      $this->igpinm->trans_end();
      $this->status = 1107;
      $this->message = "Error, please try again later";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }
    $this->status = 200;
    $this->message = "Success";

    $this->igpinm->trans_commit();
    $this->igpinm->trans_end();

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }
}
