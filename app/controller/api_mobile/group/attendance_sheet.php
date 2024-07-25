<?php
class attendance_sheet extends JI_Controller
{

  public function __construct() 
  {
    parent::__construct();
    $this->lib("seme_log");
    $this->load("api_mobile/b_user_model", "bu");
    $this->load("api_mobile/group/i_group_model", "igm");
    $this->load("api_mobile/group/i_group_post_model", "igpostm");
    $this->load("api_mobile/group/i_group_participant_model", "igparticipantm");
    $this->load("api_mobile/group/i_group_post_attachment_model", "igpam");
    $this->load("api_mobile/group/i_group_post_attachment_attendance_sheet_model", "igpaasm");
    $this->load("api_mobile/group/i_group_post_attachment_attendance_sheet_member_model", "igpaasmm");
    $this->load("api_mobile/group/i_group_post_attachment_attendance_sheet_guest_model", "igpaasgm");
    $this->load("api_mobile/group/i_group_notifications_model", "ignotifm");
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

  // private function __sortCol($sort_col, $tbl_as)
  // {
  //   switch ($sort_col) {
  //     case 'id':
  //     $sort_col = "$tbl_as.id";
  //     break;
  //     case 'cdate':
  //     $sort_col = "$tbl_as.cdate";
  //     break;
  //     default:
  //     $sort_col = "$tbl_as.cdate";
  //   }
  //   return $sort_col;
  // }
  // private function __sortDir($sort_dir)
  // {
  //   $sort_dir = strtolower($sort_dir);
  //   if ($sort_dir == "asc") {
  //     $sort_dir = "ASC";
  //   } else {
  //     $sort_dir = "DESC";
  //   }
  //   return $sort_dir;
  // }
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

  // public function index()
  // {
  //   //initial
  //   $dt = $this->__init();

  //   //default result
  //   $data = array();
  //   // $data['community_category'] = new stdClass();
  //   // $data['community_total'] = 0;
  //   $data['posts'] = array();

  //   //check nation_code
  //   $nation_code = $this->input->get('nation_code');
  //   $nation_code = $this->nation_check($nation_code);
  //   if (empty($nation_code)) {
  //     $this->status = 101;
  //     $this->message = 'Missing or invalid nation_code';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  //     die();
  //   }

  //   $apisess = $this->input->get('apisess');
  //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   if (!isset($pelanggan->id)) {
  //     $pelanggan = new stdClass();
  //     if($nation_code == 62){ //indonesia
  //       $pelanggan->language_id = 2;
  //     }else if($nation_code == 82){ //korea
  //       $pelanggan->language_id = 3;
  //     }else if($nation_code == 66){ //thailand
  //       $pelanggan->language_id = 4;
  //     }else {
  //       $pelanggan->language_id = 1;
  //     }
  //   }

  //   $sort_col = $this->input->post("sort_col");
  //   $sort_dir = $this->input->post("sort_dir");
  //   $page = $this->input->post("page");
  //   $page_size = $this->input->post("page_size");
  //   $keyword = trim($this->input->post("keyword"));
  //   $group_id = trim($this->input->post("group_id"));
  //   $b_user_id = $this->input->post("b_user_id");
  //   $timezone = $this->input->post("timezone");
  //   if ($b_user_id<='0') {
  //     $b_user_id = "";
  //   }

  //   if (strlen($group_id)<3){
  //     $this->status = 1101;
  //     $this->message = 'Club id not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  //     die();
  //   }

  //   $group = $this->igm->getById($nation_code, $group_id);
  //   if (!isset($group->id)){
  //     $this->status = 1101;
  //     $this->message = 'Club id not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  //     die();
  //   }

  //   if($this->isValidTimezoneId($timezone) === false){
  //     $timezone = $this->default_timezone;
  //   }

  //   //sanitize input
  //   $tbl_as = $this->igpostm->getTblAs();
  //   $sort_col = $this->__sortCol($sort_col, $tbl_as);
  //   $sort_dir = $this->__sortDir($sort_dir);
  //   $page = $this->__page($page);
  //   $page_size = $this->__pageSize($page_size);

  //   //keyword
  //   if (mb_strlen($keyword)>1) {
  //     //$keyword = utf8_encode(trim($keyword));
  //     $enc = mb_detect_encoding($keyword, 'UTF-8');
  //     if ($enc == 'UTF-8') {
  //     } else {
  //       $keyword = iconv($enc, 'ISO-8859-1//TRANSLIT', $keyword);
  //     }
  //   } else {
  //     $keyword="";
  //   }
  //   $keyword = filter_var(strip_tags($keyword), FILTER_SANITIZE_SPECIAL_CHARS);
  //   $keyword = substr($keyword, 0, 32);

  //   // $data['community_total'] = $this->igpostm->countAll($nation_code, $keyword, $c_community_category_ids, $type, $pelangganAddress1, $b_user_id, $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $query_type);

  //   $data['posts'] = $this->igpostm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $group->id, $keyword, $b_user_id);

  //   //manipulating data
  //   foreach ($data['posts'] as &$pd) {

  //     $pd->total_likes = $this->thousandsCurrencyFormat($pd->total_likes);

  //     $pd->is_liked = '0';
  //     if(isset($pelanggan->id)){
  //       $checkLike = $this->igplm->getByCustomIdUserId($nation_code, $pd->id, $pelanggan->id, "post");
  //       if(isset($checkLike->id)){
  //         $pd->is_liked = '1';
  //       }
  //       unset($checkLike);
  //     }

  //     $pd->cdate_text = $this->humanTiming($pd->cdate, null, $pelanggan->language_id);
  //     $pd->cdate = $this->customTimezone($pd->cdate, $timezone);
  //     $pd->deskripsi = html_entity_decode($pd->deskripsi,ENT_QUOTES);

  //     if (isset($pd->b_user_image)) {
  //       if(file_exists(SENEROOT.$pd->b_user_image) && $pd->b_user_image != 'media/user/default.png'){
  //         $pd->b_user_image = $this->cdn_url($pd->b_user_image);
  //       } else {
  //         $pd->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
  //       }
  //     }

  //     if(strlen($pd->top_like_image_1) > 0){
  //       $pd->top_like_image_1 = $this->cdn_url($pd->top_like_image_1);
  //     }
  //     if(strlen($pd->top_like_image_2) > 0){
  //       $pd->top_like_image_2 = $this->cdn_url($pd->top_like_image_2);
  //     }

  //     $pd->images = array();
  //     $pd->locations = array();
  //     $pd->videos = array();
  //     $pd->file = array();
  //     $pd->attendance = array();
  //     $attachments = $this->igpam->getByGroupIdPostId($nation_code, $group->id, $pd->id);
  //     foreach ($attachments as $atc) {
  //       $temp = new stdClass();
  //       if($atc->jenis == 'image'){
  //         if (empty($atc->url)) {
  //           $atc->url = 'media/community_default.png';
  //         }
  //         if (empty($atc->url_thumb)) {
  //           $atc->url_thumb = 'media/community_default.png';
  //         }
  //         $temp->url = $this->cdn_url($atc->url);
  //         $temp->url_thumb = $this->cdn_url($atc->url_thumb);
  //         $pd->images[] = $temp;
  //       }else if($atc->jenis == 'video'){
  //         $temp->url = $this->cdn_url($atc->url);
  //         $temp->url_thumb = $this->cdn_url($atc->url_thumb);
  //         // $temp->total_views = $this->thousandsCurrencyFormat($temp->total_views);
  //         $pd->videos[] = $temp;
  //       }else if($atc->jenis == 'file'){
  //         $temp->url = $this->cdn_url($atc->url);
  //         $temp->file_name = $atc->file_name;
  //         $temp->file_size = $atc->file_size;
  //         $pd->file[] = $temp;
  //       }else if($atc->jenis == 'attendance sheet'){
  //         $temp->attendance_sheet_id = $atc->id;
  //         $temp->attendance_sheet_title = $atc->attendance_sheet_title;
  //         $temp->attendance_sheet_filled = $atc->attendance_sheet_filled;
  //         $temp->attendance_sheet_total = $atc->attendance_sheet_total;
  //         $pd->attendance[] = $temp;
  //       }
  //     }
  //     unset($attachments,$atc);
  //   }

  //   //response
  //   $this->status = 200;
  //   $this->message = 'Success';
  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  // }

  public function detail()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['detail'] = new stdClass();
    $data['total'] = "0";
    $data['list'] = new stdClass();

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

    $timezone = $this->input->post('timezone');
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $viewPosition = trim($this->input->post('viewPosition'));
    if (!in_array($viewPosition, array("detail","list"))) {
      $viewPosition = "detail";
    }

    $present_or_absent = trim($this->input->post('present_or_absent'));
    if (!in_array($present_or_absent, array("", "present","absent"))) {
      $present_or_absent = "";
    }

    // $sort_col = $this->input->post("sort_col");
    // $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    $page_size = $this->input->post("page_size");
    $keyword = trim($this->input->post("keyword"));

    // $tbl_as = $this->igpaasmm->getTblAs();
    // $sort_col = $this->__sortCol($sort_col, $tbl_as);
    // $sort_dir = $this->__sortDir($sort_dir);
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

    $attendance_id = trim($this->input->post('attendance_id'));
    if (strlen($attendance_id)<3){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $queryResult = $this->igpaasm->getById($nation_code, $attendance_id);
    if (!isset($queryResult->id)){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    if(isset($pelanggan->id)){
      if($queryResult->show_attendance_progress == "private" && $queryResult->b_user_id != $pelanggan->id){
        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      if($queryResult->b_user_id == $pelanggan->id){
        $data['detail'] = $queryResult;
      }
    }else{
      if($queryResult->show_attendance_progress == "private"){
        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }
    }

    if($page == 1 && $viewPosition == "list" && isset($pelanggan->id)){
      $attendanceHimself = $this->igpaasmm->getByAttendanceidUserid($nation_code, $attendance_id, $pelanggan->id);
      if(isset($attendanceHimself->member_id)){
        $page_size -= 1;
      }
    }

    $groupData = $this->igm->getById($nation_code, $queryResult->i_group_id);

    $data['total'] = $this->igpaasmm->countByAttendanceId($nation_code, $keyword, $attendance_id, $viewPosition, $present_or_absent);

    if(isset($pelanggan->id)){
      $data['list'] = $this->igpaasmm->getByAttendanceId($nation_code, $page, $page_size, "", "", $keyword, $attendance_id, $viewPosition, $present_or_absent, $queryResult->sort_members, "all", $pelanggan->id);
    }else{
      $data['list'] = $this->igpaasmm->getByAttendanceId($nation_code, $page, $page_size, "", "", $keyword, $attendance_id, $viewPosition, $present_or_absent, $queryResult->sort_members);
    }

    if(isset($attendanceHimself->member_id)){
      $data['list'] = array_merge(array($attendanceHimself), $data['list']);
    }

    foreach ($data['list'] as &$list) {
      if(file_exists(SENEROOT.$list->b_user_band_image) && $list->b_user_band_image != 'media/user/default.png'){
        $list->b_user_band_image = $this->cdn_url($list->b_user_band_image);
      } else {
        $list->b_user_band_image = $this->cdn_url('media/user/default-profile-picture.png');
      }

      if($list->b_user_id == "0"){
        $list->b_user_band_image = $this->cdn_url('media/user/default-profile-picture.png');
      }

      if($groupData->b_user_id == $list->b_user_id){
        $list->status = "owner";
      }else{
        $list->status = "member";
      }
    }

    $this->status = 200;
    $this->message = 'Success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function checkin()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    // $data['list'] = new stdClass();
    $data["present_or_absent"] = "";

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
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
    }

    $timezone = $this->input->post('timezone');
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $present_or_absent = trim($this->input->post('present_or_absent'));
    if (!in_array($present_or_absent, array("present","absent"))) {
      $present_or_absent = "";
    }

    $attendance_id = trim($this->input->post('attendance_id'));
    if (strlen($attendance_id)<3){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $queryResult = $this->igpaasm->getById($nation_code, $attendance_id);
    if (!isset($queryResult->id)){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    if($queryResult->self_check_in == 0 && $queryResult->b_user_id != $pelanggan->id && $queryResult->start_date <= date("Y-m-d") && $queryResult->deadline >= date("Y-m-d")){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $member_id = trim($this->input->post('member_id'));
    if (strlen($member_id)<3){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $memberIncluded = $this->igpaasmm->getById($nation_code, $member_id);
    if (!isset($memberIncluded->id)){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    if($queryResult->response_option == "present only" && $present_or_absent == "absent"){
      $present_or_absent = "present";
    }

    $du = array();
    $du["present_or_absent"] = $present_or_absent;
    $this->igpaasmm->update($nation_code, $member_id, $du);

    $attachmentData = $this->igpam->getById($nation_code, $queryResult->i_group_post_attachment_id);
    $totalHaventPresentAbsent = $this->igpaasmm->countByAttendanceId($nation_code, "", $attendance_id, "detail");

    $du = array();
    $du["attendance_sheet_filled"] = $attachmentData->attendance_sheet_total - $totalHaventPresentAbsent;
    $this->igpam->update($nation_code, $queryResult->i_group_post_attachment_id, $du);

    if($queryResult->b_user_id != $pelanggan->id){
      if($present_or_absent == ""){
        $present_or_absent = "cancel";
      }

      $groupData = $this->igm->getById($nation_code, $queryResult->i_group_id);
      $user = $this->bu->getById($nation_code, $queryResult->b_user_id);

      $dpe = array();
      $dpe['nation_code'] = $nation_code;
      $dpe['b_user_id'] = $queryResult->b_user_id;
      $dpe['type'] = "band_group_post";
      if($user->language_id == 2) {
        $dpe['judul'] = "Attendance";
        $dpe['teks'] =  "Mr./Ms. ".$pelanggan->band_fnama." menjawab requestan anda(".$present_or_absent.")";
      } else {
        $dpe['judul'] = "Attendance";
        $dpe['teks'] =  "Mr./Ms. ".$pelanggan->band_fnama." responded to your request(".$present_or_absent.")";
      }
      $dpe['group_name'] = $groupData->name;
      $dpe['i_group_id'] = $queryResult->i_group_id;
      $dpe['gambar'] = 'media/pemberitahuan/community.png';
      $dpe['cdate'] = "NOW()";
      $extras = new stdClass();
      $extras->i_group_post_id = $queryResult->i_group_post_id;
      $extras->i_group_id = $queryResult->i_group_id;
      if($user->language_id == 2) { 
        $extras->judul = "Attendance";
        $extras->teks =  "Mr./Ms. ".$pelanggan->band_fnama." menjawab requestan anda(".$present_or_absent.")";
      } else {
        $extras->judul = "Attendance";
        $extras->teks =  "Mr./Ms. ".$pelanggan->band_fnama." responded to your request(".$present_or_absent.")";
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
          $title = "Attendance";
          $message = "Mr./Ms. ".$pelanggan->band_fnama." menjawab requestan anda(".$present_or_absent.")";
        } else {
          $title = "Attendance";
          $message = "Mr./Ms. ".$pelanggan->band_fnama." responded to your request(".$present_or_absent.")";
        }
        $image = 'media/pemberitahuan/community.png';
        $type = 'band_group_post';
        $payload = new stdClass();
        $payload->i_group_post_id = $queryResult->i_group_post_id;
        $payload->i_group_id = $queryResult->i_group_id;
        if($user->language_id == 2) {
          $payload->judul = "Attendance";
          $payload->teks = "Mr./Ms. ".$pelanggan->band_fnama." menjawab requestan anda(".$present_or_absent.")";
        } else {
          $payload->judul = "Attendance";
          $payload->teks = "Mr./Ms. ".$pelanggan->band_fnama." responded to your request(".$present_or_absent.")";
        }
        $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
      }
    }

    $data["present_or_absent"] = $present_or_absent;

    $this->status = 200;
    $this->message = 'Success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function guest_list()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['list'] = array();

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
        $this->status = 401;
        $this->message = 'Missing or invalid API session';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
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

    $data['list'] = $this->igpaasgm->getByGroupidUserid($nation_code, $queryResult->id, $pelanggan->id);
    foreach ($data['list'] as &$list) {
      $list->image = $this->cdn_url('media/user/default-profile-picture.png');
    }

    $this->status = 200;
    $this->message = 'Success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function guest_add()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['list'] = new stdClass();

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

    $name = trim($this->input->post('name'));
    if (strlen($name) < 3){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $di = array();
    $di['nation_code'] = $nation_code;
    $di['i_group_id'] = $group_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['fnama'] = $name;

    $endDoWhile = 0;
    do{
      $id = $this->GUIDv4();
      $checkId = $this->igpaasgm->checkId($nation_code, $id);
      if($checkId == 0){
        $endDoWhile = 1;
      }
    }while($endDoWhile == 0);
    $di['id'] = $id;
    $this->igpaasgm->set($di);

    $data['list'] = $this->igpaasgm->getByGroupidUserid($nation_code, $queryResult->id, $pelanggan->id);
    foreach ($data['list'] as &$list) {
      $list->image = $this->cdn_url('media/user/default-profile-picture.png');
    }

    $this->status = 200;
    $this->message = 'Success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function guest_delete()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['list'] = new stdClass();

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

    $guest_id = trim($this->input->post('guest_id'));
    if (strlen($guest_id) < 3){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $guestData = $this->igpaasgm->getByIdGroupidUserid($nation_code, $guest_id, $group_id, $pelanggan->id);
    if (!isset($guestData->id)){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $this->igpaasgm->del($guest_id);

    $data['list'] = $this->igpaasgm->getByGroupidUserid($nation_code, $queryResult->id, $pelanggan->id);
    foreach ($data['list'] as &$list) {
      $list->image = $this->cdn_url('media/user/default-profile-picture.png');
    }

    $this->status = 200;
    $this->message = 'Success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }
}
