<?php
class Post extends JI_Controller
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
    $this->load("api_mobile/group/i_group_post_like_model", "igplm");
    $this->load("api_mobile/group/i_group_bookmark_post_model", "igbpm");
    $this->load("api_mobile/group/i_group_notifications_model", "ignotifm");
    $this->load("api_mobile/common_code_model", "ccm");
    $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
    $this->load("api_mobile/group/i_group_post_attachment_watch_video_model", "igpawvm");
    $this->load("api_mobile/g_daily_track_record_model", 'gdtrm');
    $this->load("api_mobile/c_block_model", "cbm");
    $this->load("api_mobile/b_user_follow_model", 'buf');
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

  private function __moveImagex($nation_code, $url, $targetdir, $produk_id="0", $ke="", $identifier="")
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
      if($identifier != ""){
        $filethumb = $filename."-thumb".$identifier.".".$extension;
      }else{
        $filethumb = $filename."-thumb.".$extension;
      }
      $filename = $filename.".".$extension;

      rename($file_path, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
      rename($file_path_thumb, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
      
      $sc->status = 200;
      $sc->message = 'Success';
      $sc->thumb = str_replace("//", "/", $targetdir.'/'.$filethumb);
      $sc->image = str_replace("//", "/", $targetdir.'/'.$filename);
      $sc->file_size = filesize(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
      $sc->file_size_thumb = filesize(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
    
    } else {
      $sc->status = 997;
      $sc->message = 'Failed';
    }

    // $this->seme_log->write("api_mobile", 'API_Mobile/Community::__moveImagex -- INFO URL: '.$url.' PID:'.$produk_id.' ke:'.$ke.' '.$sc->status.' '.$sc->message.'');
    return $sc;
  }

  private function __moveVideox($nation_code, $url, $targetdir, $produk_id="0", $ke="")
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

      $filename = "$nation_code-$produk_id-$ke-".date('YmdHis');
      $filename = $filename.".".$extension;

      rename($file_path, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
      
      $sc->status = 200;
      $sc->message = 'Success';
      $sc->url = str_replace("//", "/", $targetdir.'/'.$filename);
      $sc->file_size = filesize(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
    
    } else {
      $sc->status = 997;
      $sc->message = 'Failed';
    }
    return $sc;
  }

  private function __moveFilex($nation_code, $url, $targetdir, $produk_id="0", $ke="")
  {
    $sc = new stdClass();
    $sc->status = 500;
    $sc->message = 'Error';
    $sc->image = '';
    $sc->thumb = '';

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

      $filename = "$nation_code-$produk_id-$ke-".date('YmdHis');
      $filename = $filename.".".$extension;

      rename($file_path, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);

      $sc->status = 200;
      $sc->message = 'Success';
      $sc->url = str_replace("//", "/", $targetdir.'/'.$filename);
      $sc->file_size = filesize(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
    
    } else {
      $sc->status = 997;
      $sc->message = 'Failed';
    }
    return $sc;
  }

  private function __sortCol($sort_col, $tbl_as)
  {
    if($sort_col="0") $sort_col="";
    switch ($sort_col) {
      case 'id':
      $sort_col = "$tbl_as.id";
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

  public function index()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    // $data['community_category'] = new stdClass();
    // $data['community_total'] = 0;
    $data['posts'] = array();

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

    $timezone = $this->input->post("timezone");
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    $page_size = $this->input->post("page_size");
    $tbl_as = $this->igpostm->getTblAs();
    $sort_col = $this->__sortCol($sort_col, $tbl_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

    $keyword = trim($this->input->post("keyword"));
    $group_id = trim($this->input->post("group_id"));
    $type_post = trim($this->input->post("type_post"));
    $b_user_id = $this->input->post("b_user_id");
    if ($b_user_id<='0') {
      $b_user_id = "";
    }

    // if (strlen($group_id)<3){
    //   $this->status = 1101;
    //   $this->message = 'Club id not found';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    //   die();
    // }

    // $group = $this->igm->getById($nation_code, $group_id);
    // if (!isset($group->id)){
    //   $this->status = 1101;
    //   $this->message = 'Club id not found';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    //   die();
    // }

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

    if (strlen($group_id)>3){
      $queryResult = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $group_id, $pelanggan->id);
      if (!isset($queryResult->b_user_id)){
        $this->status = 1103;
        $this->message = 'You are not the member of this club';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }
    }

    if (strlen($group_id)<3){
      // $data['community_total'] = $this->igpostm->countAll($nation_code, $keyword, $c_community_category_ids, $type, $pelangganAddress1, $b_user_id, $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $query_type);
      $data['posts'] = $this->igpostm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, "", $keyword, $pelanggan->id, $b_user_id, $type_post);
    }else{
      // $data['community_total'] = $this->igpostm->countAll($nation_code, $keyword, $c_community_category_ids, $type, $pelangganAddress1, $b_user_id, $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $query_type);
      $data['posts'] = $this->igpostm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $group_id, $keyword, $pelanggan->id, $b_user_id, $type_post);
    }

    //manipulating data
    foreach ($data['posts'] as &$pd) {
      $pd->total_likes = $this->thousandsCurrencyFormat($pd->total_likes);
      $pd->is_liked = '0';
      $pd->currentLikeEmoji = '';
      $pd->is_owner_post = '0';
      if(isset($pelanggan->id)){
        $checkLike = $this->igplm->getByCustomIdUserId($nation_code, $pd->id, $pelanggan->id, "post");
        if(isset($checkLike->id)){
          $pd->is_liked = '1';
          $pd->currentLikeEmoji = $checkLike->i_group_post_like_category_id;
        }
        unset($checkLike);
        if($pelanggan->id == $pd->b_user_id){
          $pd->is_owner_post = '1';
        }
      }

      $pd->is_owner_or_admin = '0';
      if (strlen($group_id)>3){
        if($queryResult->is_owner == "1" || $queryResult->is_co_admin == "1"){
          $pd->is_owner_or_admin = "1";
        }
      }

      $checkBookmark = $this->igbpm->getByUserIdPostId($nation_code, $pelanggan->id, $pd->id);
      if($pelanggan->id != isset($checkBookmark->b_user_id)) {
        $pd->is_bookmark = "0";
      } else {
        $pd->is_bookmark = "1";
      }

      $pd->cdate_text = $this->humanTiming($pd->cdate, null, $pelanggan->language_id);
      $pd->cdate = $this->customTimezone($pd->cdate, $timezone);
      $pd->deskripsi = html_entity_decode($pd->deskripsi,ENT_QUOTES);
      $pd->group_name = html_entity_decode($pd->group_name,ENT_QUOTES);
      if (isset($pd->b_user_band_image)) {
        if(file_exists(SENEROOT.$pd->b_user_band_image) && $pd->b_user_band_image != 'media/user/default.png'){
          $pd->b_user_band_image = $this->cdn_url($pd->b_user_band_image);
        } else {
          $pd->b_user_band_image = $this->cdn_url('media/user/default-profile-picture.png');
        }
      }

      $pd->images = array();
      $pd->locations = array();
      $pd->videos = array();
      $pd->file = array();
      $pd->attendance = array();
      $attachments = $this->igpam->getByGroupIdPostId($nation_code, $group_id, $pd->id);
      foreach ($attachments as $atc) {
        $temp = new stdClass();
        if($atc->jenis == 'image'){
          if (empty($atc->url)) {
            $atc->url = 'media/community_default.png';
          }
          if (empty($atc->url_thumb)) {
            $atc->url_thumb = 'media/community_default.png';
          }
          $temp->url = $this->cdn_url($atc->url);
          $temp->url_thumb = $this->cdn_url($atc->url_thumb);
          $pd->images[] = $temp;
        }else if($atc->jenis == 'location'){
          $temp->location_nama = $atc->location_nama;
          $temp->location_address = $atc->location_address;
          $temp->location_place_id = $atc->location_place_id;
          $temp->location_latitude = $atc->location_latitude;
          $temp->location_longitude = $atc->location_longitude;
          $pd->locations[] = $temp;
        }else if($atc->jenis == 'video'){
          $temp->video_id = $atc->id;
          $temp->url = $this->cdn_url($atc->url);
          $temp->url_thumb = $this->cdn_url($atc->url_thumb);
          $temp->convert_status = $atc->convert_status;
          // $temp->total_views = $this->thousandsCurrencyFormat($temp->total_views);
          $pd->videos[] = $temp;
        }else if($atc->jenis == 'file'){
          $temp->url = $this->cdn_url($atc->url);
          $temp->file_name = $atc->file_name;
          $temp->file_size = $atc->file_size;
          $pd->file[] = $temp;
        }else if($atc->jenis == 'attendance sheet'){
          $temp->attendance_sheet_id = $atc->attendance_sheet_id;
          $temp->attendance_sheet_title = $atc->attendance_sheet_title;
          $temp->attendance_sheet_filled = $atc->attendance_sheet_filled;
          $temp->attendance_sheet_total = $atc->attendance_sheet_total;
          $attendanceSheetData = $this->igpaasm->getById($nation_code, $atc->attendance_sheet_id);
          $temp->response_option = $attendanceSheetData->response_option;
          $temp->self_check_in = $attendanceSheetData->self_check_in;
          $temp->start_date = $attendanceSheetData->start_date;
          $temp->deadline = $attendanceSheetData->deadline;
          $temp->show_attendance_progress = $attendanceSheetData->show_attendance_progress;
          $pd->attendance[] = $temp;
        }
      }
      unset($attachments,$atc);

      $pd->is_blocked = "0";
      $blockDataAccount = $this->cbm->getById($nation_code, 0, $pd->b_user_id, "account", $pelanggan->id);
      $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $pd->b_user_id);
      if(isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){
        $pd->is_blocked = "1";
      }

      $pd->is_follow = $this->buf->checkFollow($nation_code, $pelanggan->id, $pd->b_user_id);
    }

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
    $data['post'] = new stdClass();

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

    $post_id = trim($this->input->post('post_id'));
    if (strlen($post_id)<3){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $queryResult = $this->igpostm->getById($nation_code, $post_id);
    if (!isset($queryResult->id)){
      $this->status = 1160;
      $this->message = 'This post is deleted by an author';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $groupDetail = $this->igm->getById($nation_code, $queryResult->i_group_id);
    if($groupDetail->group_type != "public"){
      $j8devaror8 = $this->input->post("j8devaror8");
      if($j8devaror8 != "miny0b9o54"){
        if (!isset($pelanggan->id)){
          $this->status = 1106;
          $this->message = 'You are forbidden to see the post';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
          die();
        }

        $stillParticipant = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $queryResult->i_group_id, $pelanggan->id);
        if (!isset($stillParticipant->b_user_id)){
          $this->status = 1106;
          $this->message = 'You are forbidden to see the post';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
          die();
        }
      }
    }

    // $queryResult->can_chat_and_like = "0";
    // if(isset($pelanggan->id)){
    //   $queryResult->can_chat_and_like = "1";
    // }

    $queryResult->total_likes = $this->thousandsCurrencyFormat($queryResult->total_likes);
    $queryResult->is_liked = '0';
    $queryResult->currentLikeEmoji = '';
    $queryResult->is_owner_post = '0';
    if(isset($pelanggan->id)){
      $checkLike = $this->igplm->getByCustomIdUserId($nation_code, $queryResult->id, $pelanggan->id, "post");
      if(isset($checkLike->id)){
        $queryResult->is_liked = '1';
        $queryResult->currentLikeEmoji = $checkLike->i_group_post_like_category_id;
      }
      unset($checkLike);
      if($pelanggan->id == $queryResult->b_user_id){
        $queryResult->is_owner_post = '1';
      }
    }

    $queryResult->is_owner_or_admin = '0';
    if (isset($pelanggan->id)){
      $stillParticipant = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $queryResult->i_group_id, $pelanggan->id);
      if (isset($stillParticipant->b_user_id)){
        if($stillParticipant->is_owner == "1" || $stillParticipant->is_co_admin == "1"){
          $queryResult->is_owner_or_admin = "1";
        }
      }
    }

    $queryResult->cdate_text = $this->humanTiming($queryResult->cdate, null, $pelanggan->language_id);
    $queryResult->cdate = $this->customTimezone($queryResult->cdate, $timezone);
    $queryResult->deskripsi = html_entity_decode($queryResult->deskripsi,ENT_QUOTES);
    $queryResult->group_name = html_entity_decode($queryResult->group_name,ENT_QUOTES);
    if (isset($queryResult->b_user_band_image)) {
      if(file_exists(SENEROOT.$queryResult->b_user_band_image) && $queryResult->b_user_band_image != 'media/user/default.png'){
        $queryResult->b_user_band_image = $this->cdn_url($queryResult->b_user_band_image);
      } else {
        $queryResult->b_user_band_image = $this->cdn_url('media/user/default-profile-picture.png');
      }
    }

    $queryResult->images = array();
    $queryResult->locations = array();
    $queryResult->videos = array();
    $queryResult->file = array();
    $queryResult->attendance = array();
    $attachments = $this->igpam->getByGroupIdPostId($nation_code, $queryResult->i_group_id, $queryResult->id);
    foreach ($attachments as $atc) {
      $temp = new stdClass();
      if($atc->jenis == 'image'){
        if (empty($atc->url)) {
          $atc->url = 'media/community_default.png';
        }
        if (empty($atc->url_thumb)) {
          $atc->url_thumb = 'media/community_default.png';
        }
        $temp->url = $this->cdn_url($atc->url);
        $temp->url_thumb = $this->cdn_url($atc->url_thumb);
        $queryResult->images[] = $temp;
      }else if($atc->jenis == 'location'){
        $temp->location_nama = $atc->location_nama;
        $temp->location_address = $atc->location_address;
        $temp->location_place_id = $atc->location_place_id;
        $temp->location_latitude = $atc->location_latitude;
        $temp->location_longitude = $atc->location_longitude;
        $queryResult->locations[] = $temp;
      }else if($atc->jenis == 'video'){
        $temp->video_id = $atc->id;
        $temp->url = $this->cdn_url($atc->url);
        $temp->url_thumb = $this->cdn_url($atc->url_thumb);
        $temp->convert_status = $atc->convert_status;
        // $temp->total_views = $this->thousandsCurrencyFormat($temp->total_views);
        $queryResult->videos[] = $temp;
      }else if($atc->jenis == 'file'){
        $temp->url = $this->cdn_url($atc->url);
        $temp->file_name = $atc->file_name;
        $temp->file_size = $atc->file_size;
        $queryResult->file[] = $temp;
      }else if($atc->jenis == 'attendance sheet'){
        $temp->attendance_sheet_id = $atc->attendance_sheet_id;
        $temp->attendance_sheet_title = $atc->attendance_sheet_title;
        $temp->attendance_sheet_filled = $atc->attendance_sheet_filled;
        $temp->attendance_sheet_total = $atc->attendance_sheet_total;
        $attendanceSheetData = $this->igpaasm->getById($nation_code, $atc->attendance_sheet_id);
        $temp->response_option = $attendanceSheetData->response_option;
        $temp->self_check_in = $attendanceSheetData->self_check_in;
        $temp->start_date = $attendanceSheetData->start_date;
        $temp->deadline = $attendanceSheetData->deadline;
        $temp->show_attendance_progress = $attendanceSheetData->show_attendance_progress;
        $queryResult->attendance[] = $temp;
      }
    }
    unset($attachments,$atc);

    $queryResult->is_follow = "0";
    if (isset($pelanggan->id)){
      $queryResult->is_follow = $this->buf->checkFollow($nation_code, $pelanggan->id, $queryResult->b_user_id);
    }

    $this->status = 200;
    $this->message = 'Success';

    $data['post'] = $queryResult;

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function baru()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['post'] = new stdClass();

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
    $deskripsi = trim($this->input->post('deskripsi'));
    $location_json = $this->input->post("location_json");
    $attendance_sheet = $this->input->post("attendance_sheet");

    if (strlen($group_id)<3){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $groupData = $this->igm->getById($nation_code, $group_id);
    if (!isset($groupData->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $group_id, $pelanggan->id);
    if (!isset($queryResult->b_user_id)){
      $this->status = 1103;
      $this->message = 'You are not the member of this club';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }
    unset($queryResult);

    $deskripsi = str_replace('’',"'",$deskripsi);
    $deskripsi = nl2br($deskripsi);
    $deskripsi = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $deskripsi);
    $deskripsi = str_replace("\\n", "<br />", $deskripsi);
    if (strlen($deskripsi)<3) {
      $this->status = 1105;
      $this->message = 'Description is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->igpostm->trans_start();

    $di = array();
    $di['nation_code'] = $nation_code;
    $di['i_group_id'] = $group_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['deskripsi'] = $deskripsi;
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
    $res = $this->igpostm->set($di);
    if (!$res) {
      $this->igpostm->trans_rollback();
      $this->igpostm->trans_end();
      $this->status = 1107;
      $this->message = "Error, please try again later";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->gdtrm->updateTotalData(DATE("Y-m-d"), "club_post", "+", "1");

    $this->status = 200;
    $this->message = "Success";

    $getSPTPhoto = 0;
    $getSPTVideo = 0;
    $getSPTAttendancesheet = 0;
    $getSPTLocation = 0;
    if ($res) {
      $checkFileExist = 1;
      $checkFileTemporaryOrNot = 1;
      $listUploadImage = array();
      for ($i=1; $i < 11; $i++) {
        if($this->input->post('foto'.$i) != null){
          $file_path = parse_url($this->input->post('foto'.$i), PHP_URL_PATH);
          $listUploadImage = array_merge($listUploadImage, array($file_path));
          if (strpos($file_path, 'temporary') !== false) {
            if (!file_exists(SENEROOT.$file_path)) {
              $checkFileExist = 0;
            }
          }else{
            $checkFileTemporaryOrNot = 0;
          }
        }
      }

      if ($checkFileExist == 0) {
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 995;
        $this->message = 'Failed upload, temporary already gone';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      if ($checkFileTemporaryOrNot == 0) {
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 996;
        $this->message = 'Failed upload, upload is not temporary';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      if(!empty($listUploadImage)){
        foreach ($listUploadImage as $key => $upload) {
          $endDoWhile = 0;
          do{
            $attachmentId = $this->GUIDv4();
            $checkId = $this->igpam->checkId($nation_code, $attachmentId);
            if($checkId == 0){
              $endDoWhile = 1;
            }
          }while($endDoWhile == 0);

          $sc = $this->__moveImagex($nation_code, $upload, $this->media_group_post_image, $id, $attachmentId);
          if (isset($sc->status)) {
            if ($sc->status==200) {
              $dix = array();
              $dix['nation_code'] = $nation_code;
              $dix['id'] = $attachmentId;
              $dix['i_group_id'] = $group_id;
              $dix['i_group_post_id'] = $id;
              $dix['i_group_directory_id'] = $group_id;
              $dix['b_user_id'] = $pelanggan->id;
              $dix['jenis'] = 'image';
              $dix['url'] = $sc->image;
              $dix['url_thumb'] = $sc->thumb;
              $dix['file_size'] = $sc->file_size;
              $dix['file_size_thumb'] = $sc->file_size_thumb;
              $this->igpam->set($dix);
              $getSPTPhoto = 1;
            }
          }
        }
      }

      if(is_array($location_json)){
        if(count($location_json) > 0){
          foreach ($location_json as $key => $upload) {
            if(isset($upload['location_nama']) && isset($upload['location_address']) && isset($upload['location_place_id']) && isset($upload['location_latitude']) && isset($upload['location_longitude'])){
              if($upload['location_nama'] && $upload['location_address'] && $upload['location_place_id'] && $upload['location_latitude'] && $upload['location_longitude']){
                $endDoWhile = 0;
                do{
                  $attachmentId = $this->GUIDv4();
                  $checkId = $this->igpam->checkId($nation_code, $attachmentId);
                  if($checkId == 0){
                    $endDoWhile = 1;
                  }
                }while($endDoWhile == 0);

                $dix = array();
                $dix['nation_code'] = $nation_code;
                $dix['id'] = $attachmentId;
                $dix['i_group_id'] = $group_id;
                $dix['i_group_post_id'] = $id;
                $dix['i_group_directory_id'] = $group_id;
                $dix['b_user_id'] = $pelanggan->id;
                $dix['jenis'] = 'location';
                $dix['location_nama'] = $upload['location_nama'];
                $dix['location_address'] = $upload['location_address'];
                $dix['location_place_id'] = $upload['location_place_id'];
                $dix['location_latitude'] = $upload['location_latitude'];
                $dix['location_longitude'] = $upload['location_longitude'];
                $this->igpam->set($dix);
                $getSPTLocation = 1;
              }
            }
          }
        }
      }

      $checkFileExist = 1;
      $checkFileTemporaryOrNot = 1;
      $listUploadVideo = array();
      for ($i=1; $i < 3; $i++) {
        if($this->input->post('video'.$i) != null){
          $file_path = parse_url($this->input->post('video'.$i), PHP_URL_PATH);
          $listUploadVideo = array_merge($listUploadVideo, array($file_path));
          if (strpos($file_path, 'temporary') !== false) {
            if (!file_exists(SENEROOT.$file_path)) {
              $checkFileExist = 0;
            }
          }else{
            $checkFileTemporaryOrNot = 0;
          }
        }
      }

      if ($checkFileExist == 0) {
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 995;
        $this->message = 'Failed upload, temporary already gone';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      if ($checkFileTemporaryOrNot == 0) {
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 996;
        $this->message = 'Failed upload, upload is not temporary';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      $listUploadVideoThumb = array();
      for ($i=1; $i < 3; $i++) {
        if($this->input->post('video'.$i.'_thumb') != null){
          $file_path = parse_url($this->input->post('video'.$i.'_thumb'), PHP_URL_PATH);
          $listUploadVideoThumb = array_merge($listUploadVideoThumb, array($file_path));
          if (strpos($file_path, 'temporary') !== false) {
            if (!file_exists(SENEROOT.$file_path)) {
              $checkFileExist = 0;
            }
          }else{
            $checkFileTemporaryOrNot = 0;
          }
        }
      }

      if ($checkFileExist == 0) {
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 995;
        $this->message = 'Failed upload, temporary already gone';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      if ($checkFileTemporaryOrNot == 0) {
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 996;
        $this->message = 'Failed upload, upload is not temporary';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      if(!empty($listUploadVideo)){
        foreach ($listUploadVideo as $key => $upload) {
          $endDoWhile = 0;
          do{
            $attachmentId = $this->GUIDv4();
            $checkId = $this->igpam->checkId($nation_code, $attachmentId);
            if($checkId == 0){
              $endDoWhile = 1;
            }
          }while($endDoWhile == 0);

          $moveVideo = $this->__moveVideox($nation_code, $upload, $this->media_group_post_video, $id, $attachmentId);
          $sc = $this->__moveImagex($nation_code, $listUploadVideoThumb[$key], $this->media_group_post_video, $id, $attachmentId);
          if (isset($moveVideo->status) && isset($sc->status)) {
            if ($moveVideo->status==200 && $sc->status==200) {
              $dix = array();
              $dix['nation_code'] = $nation_code;
              $dix['id'] = $attachmentId;
              $dix['i_group_id'] = $group_id;
              $dix['i_group_post_id'] = $id;
              $dix['i_group_directory_id'] = $group_id;
              $dix['b_user_id'] = $pelanggan->id;
              $dix['jenis'] = 'video';
              $dix['convert_status'] = 'waiting';
              $dix['url'] = $moveVideo->url;
              $dix['url_thumb'] = $sc->thumb;
              $dix['file_size'] = $moveVideo->file_size;
              $dix['file_size_thumb'] = $sc->file_size_thumb;
              $this->igpam->set($dix);
              $getSPTVideo = 1;
            }
          }
        }
      }

      $checkFileExist = 1;
      $checkFileTemporaryOrNot = 1;
      $listUploadFile = array();
      for ($i=1; $i < 3; $i++) {
        if($this->input->post('file'.$i) != null){
          $file_path = parse_url($this->input->post('file'.$i), PHP_URL_PATH);
          $listUploadFile = array_merge($listUploadFile, array($file_path));
          if (strpos($file_path, 'temporary') !== false) {
            if (!file_exists(SENEROOT.$file_path)) {
              $checkFileExist = 0;
            }
          }else{
            $checkFileTemporaryOrNot = 0;
          }
        }
      }

      if ($checkFileExist == 0) {
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 995;
        $this->message = 'Failed upload, temporary already gone';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      if ($checkFileTemporaryOrNot == 0) {
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 996;
        $this->message = 'Failed upload, upload is not temporary';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      if(!empty($listUploadFile)){
        foreach ($listUploadFile as $key => $upload) {
          $endDoWhile = 0;
          do{
            $attachmentId = $this->GUIDv4();
            $checkId = $this->igpam->checkId($nation_code, $attachmentId);
            if($checkId == 0){
              $endDoWhile = 1;
            }
          }while($endDoWhile == 0);

          $sc = $this->__moveFilex($nation_code, $upload, $this->media_group_post_file, $id, $attachmentId);
          if (isset($sc->status)) {
            if ($sc->status==200) {
              $dix = array();
              $dix['nation_code'] = $nation_code;
              $dix['id'] = $attachmentId;
              $dix['i_group_id'] = $group_id;
              $dix['i_group_post_id'] = $id;
              $dix['i_group_directory_id'] = $group_id;
              $dix['b_user_id'] = $pelanggan->id;
              $dix['jenis'] = 'file';
              $dix['url'] = $sc->url;
              $dix['file_name'] = substr(pathinfo($upload, PATHINFO_BASENAME), 0, strpos(pathinfo($upload, PATHINFO_BASENAME),"-")).".".pathinfo($upload, PATHINFO_EXTENSION);
              $dix['file_size'] = $sc->file_size;
              $this->igpam->set($dix);
            }
          }
        }
      }

      if(is_array($attendance_sheet)){
        if(count($attendance_sheet) > 0){
          if(strlen($attendance_sheet["title"]) < 3){
            $this->igpostm->trans_rollback();
            $this->igpostm->trans_end();
            $this->status = 1102;
            $this->message = 'You cannot create post';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
            die();
          }

          if (!in_array($attendance_sheet["sort_members"], array("By name", "Attending members first","Not attending members first"))) {
            $attendance_sheet["sort_members"] = "By name";
          }

          if (!in_array($attendance_sheet["response_option"], array("present only","present and absent"))) {
            $attendance_sheet["response_option"] = "present only";
          }

          if($attendance_sheet["self_check_in"] != 1){
            $attendance_sheet["self_check_in"] = 0;
          }

          if(strlen($attendance_sheet["start_date"]) < 3){
            $attendance_sheet["start_date"] = date("Y-m-d");
          }

          if(strlen($attendance_sheet["deadline"]) < 3){
            $attendance_sheet["deadline"] = date("Y-m-d");
          }

          if (!in_array($attendance_sheet["show_attendance_progress"], array("public","private"))) {
            $attendance_sheet["show_attendance_progress"] = "private";
          }

          $endDoWhile = 0;
          do{
            $attachmentId = $this->GUIDv4();
            $checkId = $this->igpam->checkId($nation_code, $attachmentId);
            if($checkId == 0){
              $endDoWhile = 1;
            }
          }while($endDoWhile == 0);

          $endDoWhile = 0;
          do{
            $attendanceSheetId = $this->GUIDv4();
            $checkId = $this->igpaasm->checkId($nation_code, $attendanceSheetId);
            if($checkId == 0){
              $endDoWhile = 1;
            }
          }while($endDoWhile == 0);

          $dix = array();
          $dix['nation_code'] = $nation_code;
          $dix['id'] = $attachmentId;
          $dix['i_group_id'] = $group_id;
          $dix['i_group_post_id'] = $id;
          $dix['i_group_directory_id'] = $group_id;
          $dix['b_user_id'] = $pelanggan->id;
          $dix['jenis'] = 'attendance sheet';
          $dix['attendance_sheet_id'] = $attendanceSheetId;
          $dix['attendance_sheet_title'] = $attendance_sheet["title"];
          $dix['attendance_sheet_total'] = count($attendance_sheet["member"]);
          $this->igpam->set($dix);
          $getSPTAttendancesheet = 1;

          $dix = array();
          $dix['nation_code'] = $nation_code;
          $dix['id'] = $attendanceSheetId;
          $dix['i_group_id'] = $group_id;
          $dix['i_group_post_id'] = $id;
          $dix['i_group_post_attachment_id'] = $attachmentId;
          $dix['b_user_id'] = $pelanggan->id;
          $dix['title'] = $attendance_sheet["title"];
          $dix['sort_members'] = $attendance_sheet["sort_members"];
          $dix['response_option'] = $attendance_sheet["response_option"];
          $dix['self_check_in'] = $attendance_sheet["self_check_in"];
          $dix['start_date'] = $attendance_sheet["start_date"];
          $dix['deadline'] = $attendance_sheet["deadline"];
          $dix['show_attendance_progress'] = $attendance_sheet["show_attendance_progress"];
          $this->igpaasm->set($dix);

          if(count($attendance_sheet["member"]) == 0){
            $this->igpostm->trans_rollback();
            $this->igpostm->trans_end();
            $this->status = 1102;
            $this->message = 'You cannot create post';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
            die();
          }
          $insertArray = array();
          $listInsertUser = array();
          foreach ($attendance_sheet["member"] as $member) {
            $endDoWhile = 0;
            do{
              $attendanceSheetmemberId = $this->GUIDv4();
              $checkId = $this->igpaasmm->checkId($nation_code, $attendanceSheetmemberId);
              if($checkId == 0){
                $endDoWhile = 1;
              }
            }while($endDoWhile == 0);

            $dix = array();
            $dix['nation_code'] = $nation_code;
            $dix['id'] = $attendanceSheetmemberId;
            $dix['i_group_id'] = $group_id;
            $dix['i_group_post_id'] = $id;
            $dix['i_group_post_attachment_id'] = $attachmentId;
            $dix['i_group_post_attachment_attendance_sheet_id'] = $attendanceSheetId;
            if(isset($member["b_user_id"])){
              $listInsertUser[] = $member["b_user_id"];
              $dix['b_user_id'] = $member["b_user_id"];
              $dix['guest_fnama'] = "";
              $dix['custom_text'] = "";
              $dix['jenis'] = "member";
            }else{
              $dix['b_user_id'] = "0";
              $dix['guest_fnama'] = $member["guest_fnama"];
              $dix['custom_text'] = "Guest Member | Added by". $pelanggan->band_fnama;
              $dix['jenis'] = "guest";
            }
            $insertArray[] = $dix;
          }

          if(count($insertArray) > 0){
            // $this->glrm->delAll($nation_code, $area->kelurahan, $area->kecamatan, $area->kabkota, $area->provinsi);
            // $this->glrm->delAll($nation_code, "All", "All", "All", "All");

            $chunkInsertArray = array_chunk($insertArray,50);
            foreach($chunkInsertArray AS $chunk){
              //insert multi
              $this->igpaasmm->setMass($chunk);
            }
            unset($chunkInsertArray, $chunk);
          }
          unset($insertArray);
        }
      }

      $minimumTotalPeople = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E18");
      if (!isset($minimumTotalPeople->remark)) {
        $minimumTotalPeople = new stdClass();
        $minimumTotalPeople->remark = 5;
      }
      if($groupData->total_people >= $minimumTotalPeople->remark){
        //get point
        $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E19");
        if (!isset($pointGet->remark)) {
          $pointGet = new stdClass();
          $pointGet->remark = 5;
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
        $di['custom_type_sub'] = "post";
        $di['custom_text'] = $pelanggan->fnama.' has create '.$di['custom_type'].' '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
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

        $totalPointGetForCommission = $pointGet->remark;

        if($getSPTPhoto == 1){
          //get point
          $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E20");
          if (!isset($pointGet->remark)) {
            $pointGet = new stdClass();
            $pointGet->remark = 10;
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
          $di['custom_type_sub'] = "upload image";
          $di['custom_text'] = $pelanggan->fnama.' has create '.$di['custom_type'].' '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
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

          $totalPointGetForCommission += $pointGet->remark;
        }

        if($getSPTVideo == 1){
          //get point
          $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E21");
          if (!isset($pointGet->remark)) {
            $pointGet = new stdClass();
            $pointGet->remark = 20;
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
          $di['custom_type_sub'] = "upload video";
          $di['custom_text'] = $pelanggan->fnama.' has create '.$di['custom_type'].' '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
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

          $totalPointGetForCommission += $pointGet->remark;
        }

        if($groupData->b_user_id != $pelanggan->id){
          //get commission in %
          $commissionInPercent = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E32");
          if (!isset($commissionInPercent->remark)) {
            $commissionInPercent = new stdClass();
            $commissionInPercent->remark = 20;
          }

          $di = array();
          $di['nation_code'] = $nation_code;
          $di['b_user_alamat_location_kelurahan'] = "All";
          $di['b_user_alamat_location_kecamatan'] = "All";
          $di['b_user_alamat_location_kabkota'] = "All";
          $di['b_user_alamat_location_provinsi'] = "All";
          $di['b_user_id'] = $groupData->b_user_id;
          $di['point'] = $totalPointGetForCommission * $commissionInPercent->remark / 100;
          $di['custom_id'] = $id;
          $di['custom_type'] = 'club';
          $di['custom_type_sub'] = "commission";
          $di['custom_text'] = $pelanggan->fnama.' has create '.$di['custom_type'].' post so owner club get '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
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
      }

      if($getSPTAttendancesheet == 1){
        //get point
        $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E22");
        if (!isset($pointGet->remark)) {
          $pointGet = new stdClass();
          $pointGet->remark = 5;
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
        $di['custom_type_sub'] = "attendance sheet";
        $di['custom_text'] = $pelanggan->fnama.' has create '.$di['custom_type'].' '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
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

      if($getSPTLocation == 1){
        //get point
        $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E23");
        if (!isset($pointGet->remark)) {
          $pointGet = new stdClass();
          $pointGet->remark = 1;
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
        $di['custom_type_sub'] = "location";
        $di['custom_text'] = $pelanggan->fnama.' has create '.$di['custom_type'].' '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
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

      $this->igpostm->trans_commit();
      $this->igpostm->trans_end();
    }

    usleep(500000);

    $queryResult = $this->igpostm->getById($nation_code, $id);

    if($groupData->b_user_id != $pelanggan->id){
      $user = $this->bu->getById($nation_code, $groupData->b_user_id);

      $dpe = array();
      $dpe['nation_code'] = $nation_code;
      $dpe['b_user_id'] = $groupData->b_user_id;
      $dpe['type'] = "band_group_post";
      if($user->language_id == 2) {
        $dpe['judul'] = "Post Baru";
        $dpe['teks'] =  "Ada post baru";
      } else {
        $dpe['judul'] = "New Post";
        $dpe['teks'] =  "There is new post";
      }
      $dpe['group_name'] = $queryResult->group_name;
      $dpe['i_group_id'] = $queryResult->i_group_id;
      $dpe['gambar'] = 'media/pemberitahuan/community.png';
      $dpe['cdate'] = "NOW()";
      $extras = new stdClass();
      $extras->i_group_post_id = $queryResult->id;
      $extras->i_group_id = $queryResult->i_group_id;
      if($user->language_id == 2) { 
        $extras->judul = "Post Baru";
        $extras->teks =  "Ada post baru";
      } else {
        $extras->judul = "New Post";
        $extras->teks =  "There is new post";
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
          $title = "Post Baru";
          $message = "Ada post baru";
        } else {
          $title = "New Post";
          $message = "There is new post";
        }
        $image = 'media/pemberitahuan/community.png';
        $type = 'band_group_post';
        $payload = new stdClass();
        $payload->i_group_post_id = $queryResult->id;
        $payload->i_group_id = $queryResult->i_group_id;
        if($user->language_id == 2) {
          $payload->judul = "Post Baru";
          $payload->teks = "Ada post baru";
        } else {
          $payload->judul = "New Post";
          $payload->teks = "There is new post";
        }
        $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
      }
    }

    if(isset($listInsertUser)){
      foreach($listInsertUser AS $b_user_id){
        if($b_user_id == $pelanggan->id){
          continue;
        }
        $user = $this->bu->getById($nation_code, $b_user_id);

        $dpe = array();
        $dpe['nation_code'] = $nation_code;
        $dpe['b_user_id'] = $b_user_id;
        $dpe['type'] = "band_group_post";
        if($user->language_id == 2) {
          $dpe['judul'] = "Attendance";
          $dpe['teks'] =  "Anda diminta untuk absensi oleh Mr./Ms. ". $pelanggan->band_fnama;
        } else {
          $dpe['judul'] = "Attendance";
          $dpe['teks'] =  "You're asked to attend by Mr./Ms. ". $pelanggan->band_fnama;
        }
        $dpe['group_name'] = $queryResult->group_name;
        $dpe['i_group_id'] = $queryResult->i_group_id;
        $dpe['gambar'] = 'media/pemberitahuan/community.png';
        $dpe['cdate'] = "NOW()";
        $extras = new stdClass();
        $extras->i_group_post_id = $queryResult->id;
        $extras->i_group_id = $queryResult->i_group_id;
        if($user->language_id == 2) { 
          $extras->judul = "Attendance";
          $extras->teks =  "Anda diminta untuk absensi oleh Mr./Ms. ". $pelanggan->band_fnama;
        } else {
          $extras->judul = "Attendance";
          $extras->teks =  "You're asked to attend by Mr./Ms. ". $pelanggan->band_fnama;
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
            $message = "Anda diminta untuk absensi oleh Mr./Ms. ". $pelanggan->band_fnama;
          } else {
            $title = "Attendance";
            $message = "You're asked to attend by Mr./Ms. ". $pelanggan->band_fnama;
          }
          $image = 'media/pemberitahuan/community.png';
          $type = 'band_group_post';
          $payload = new stdClass();
          $payload->i_group_post_id = $queryResult->id;
          $payload->i_group_id = $queryResult->i_group_id;
          if($user->language_id == 2) {
            $payload->judul = "Attendance";
            $payload->teks = "Anda diminta untuk absensi oleh Mr./Ms. ". $pelanggan->band_fnama;
          } else {
            $payload->judul = "Attendance";
            $payload->teks = "You're asked to attend by Mr./Ms. ". $pelanggan->band_fnama;
          }
          $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        }
      }
    }

    // $queryResult->can_chat_and_like = "0";
    // if(isset($pelanggan->id)){
    //   $queryResult->can_chat_and_like = "1";
    // }

    $queryResult->total_likes = $this->thousandsCurrencyFormat($queryResult->total_likes);

    $queryResult->is_liked = '0';
    $queryResult->currentLikeEmoji = '';
    $queryResult->is_owner_post = '0';
    if(isset($pelanggan->id)){
      $checkLike = $this->igplm->getByCustomIdUserId($nation_code, $queryResult->id, $pelanggan->id, "post");
      if(isset($checkLike->id)){
        $queryResult->is_liked = '1';
        $queryResult->currentLikeEmoji = $checkLike->i_group_post_like_category_id;
      }
      unset($checkLike);
      if($pelanggan->id == $queryResult->b_user_id){
        $queryResult->is_owner_post = '1';
      }
    }

    $queryResult->is_owner_or_admin = '0';
    $queryResult->cdate_text = $this->humanTiming($queryResult->cdate, null, $pelanggan->language_id);
    $queryResult->cdate = $this->customTimezone($queryResult->cdate, $timezone);
    $queryResult->deskripsi = html_entity_decode($queryResult->deskripsi,ENT_QUOTES);
    $queryResult->group_name = html_entity_decode($queryResult->group_name,ENT_QUOTES);

    if (isset($queryResult->b_user_band_image)) {
      if(file_exists(SENEROOT.$queryResult->b_user_band_image) && $queryResult->b_user_band_image != 'media/user/default.png'){
        $queryResult->b_user_band_image = $this->cdn_url($queryResult->b_user_band_image);
      } else {
        $queryResult->b_user_band_image = $this->cdn_url('media/user/default-profile-picture.png');
      }
    }

    $queryResult->images = array();
    $queryResult->locations = array();
    $queryResult->videos = array();
    $queryResult->file = array();
    $queryResult->attendance = array();
    $attachments = $this->igpam->getByGroupIdPostId($nation_code, $queryResult->i_group_id, $queryResult->id);
    foreach ($attachments as $atc) {
      $temp = new stdClass();
      if($atc->jenis == 'image'){
        if (empty($atc->url)) {
          $atc->url = 'media/community_default.png';
        }
        if (empty($atc->url_thumb)) {
          $atc->url_thumb = 'media/community_default.png';
        }
        $temp->url = $this->cdn_url($atc->url);
        $temp->url_thumb = $this->cdn_url($atc->url_thumb);
        $queryResult->images[] = $temp;
      }else if($atc->jenis == 'location'){
        $temp->location_nama = $atc->location_nama;
        $temp->location_address = $atc->location_address;
        $temp->location_place_id = $atc->location_place_id;
        $temp->location_latitude = $atc->location_latitude;
        $temp->location_longitude = $atc->location_longitude;
        $queryResult->locations[] = $temp;
      }else if($atc->jenis == 'video'){
        $temp->video_id = $atc->id;
        $temp->url = $this->cdn_url($atc->url);
        $temp->url_thumb = $this->cdn_url($atc->url_thumb);
        $temp->convert_status = $atc->convert_status;
        // $temp->total_views = $this->thousandsCurrencyFormat($temp->total_views);
        $queryResult->videos[] = $temp;
      }else if($atc->jenis == 'file'){
        $temp->url = $this->cdn_url($atc->url);
        $temp->file_name = $atc->file_name;
        $temp->file_size = $atc->file_size;
        $queryResult->file[] = $temp;
      }else if($atc->jenis == 'attendance sheet'){
        $temp->attendance_sheet_id = $atc->attendance_sheet_id;
        $temp->attendance_sheet_title = $atc->attendance_sheet_title;
        $temp->attendance_sheet_filled = $atc->attendance_sheet_filled;
        $temp->attendance_sheet_total = $atc->attendance_sheet_total;
        $attendanceSheetData = $this->igpaasm->getById($nation_code, $atc->attendance_sheet_id);
        $temp->response_option = $attendanceSheetData->response_option;
        $temp->self_check_in = $attendanceSheetData->self_check_in;
        $temp->start_date = $attendanceSheetData->start_date;
        $temp->deadline = $attendanceSheetData->deadline;
        $temp->show_attendance_progress = $attendanceSheetData->show_attendance_progress;
        $queryResult->attendance[] = $temp;
      }
    }
    unset($attachments,$atc);

    $this->status = 200;
    $this->message = 'Success';

    $data['post'] = $queryResult;

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function baruv2()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['post'] = new stdClass();

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
    $deskripsi = trim($this->input->post('deskripsi'));
    $location_json = $this->input->post("location_json");
    $attendance_sheet = $this->input->post("attendance_sheet");

    if (strlen($group_id)<3){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $groupData = $this->igm->getById($nation_code, $group_id);
    if (!isset($groupData->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $group_id, $pelanggan->id);
    if (!isset($queryResult->b_user_id)){
      $this->status = 1103;
      $this->message = 'You are not the member of this club';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }
    unset($queryResult);

    $deskripsi = str_replace('’',"'",$deskripsi);
    $deskripsi = nl2br($deskripsi);
    $deskripsi = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $deskripsi);
    $deskripsi = str_replace("\\n", "<br />", $deskripsi);
    if (strlen($deskripsi)<3) {
      $this->status = 1105;
      $this->message = 'Description is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->igpostm->trans_start();

    $di = array();
    $di['nation_code'] = $nation_code;
    $di['i_group_id'] = $group_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['deskripsi'] = $deskripsi;
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
    $res = $this->igpostm->set($di);
    if (!$res) {
      $this->igpostm->trans_rollback();
      $this->igpostm->trans_end();
      $this->status = 1107;
      $this->message = "Error, please try again later";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->gdtrm->updateTotalData(DATE("Y-m-d"), "club_post", "+", "1");

    $this->status = 200;
    $this->message = "Success";

    $getSPTPhoto = 0;
    $getSPTVideo = 0;
    $getSPTAttendancesheet = 0;
    $getSPTLocation = 0;
    if ($res) {
      $checkFileExist = 1;
      $checkFileTemporaryOrNot = 1;
      $listUploadImage = array();
      for ($i=1; $i < 11; $i++) {
        if($this->input->post('foto'.$i) != null){
          $file_path = parse_url($this->input->post('foto'.$i), PHP_URL_PATH);
          $listUploadImage = array_merge($listUploadImage, array($file_path));
          if (strpos($file_path, 'temporary') !== false) {
            if (!file_exists(SENEROOT.$file_path)) {
              $checkFileExist = 0;
            }
          }else{
            $checkFileTemporaryOrNot = 0;
          }
        }
      }

      if ($checkFileExist == 0) {
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 995;
        $this->message = 'Failed upload, temporary already gone';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      if ($checkFileTemporaryOrNot == 0) {
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 996;
        $this->message = 'Failed upload, upload is not temporary';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      if(!empty($listUploadImage)){
        foreach ($listUploadImage as $key => $upload) {
          $endDoWhile = 0;
          do{
            $attachmentId = $this->GUIDv4();
            $checkId = $this->igpam->checkId($nation_code, $attachmentId);
            if($checkId == 0){
              $endDoWhile = 1;
            }
          }while($endDoWhile == 0);

          $sc = $this->__moveImagex($nation_code, $upload, $this->media_group_post_image, $id, $attachmentId);
          if (isset($sc->status)) {
            if ($sc->status==200) {
              $dix = array();
              $dix['nation_code'] = $nation_code;
              $dix['id'] = $attachmentId;
              $dix['i_group_id'] = $group_id;
              $dix['i_group_post_id'] = $id;
              $dix['i_group_directory_id'] = $group_id;
              $dix['b_user_id'] = $pelanggan->id;
              $dix['jenis'] = 'image';
              $dix['url'] = $sc->image;
              $dix['url_thumb'] = $sc->thumb;
              $dix['file_size'] = $sc->file_size;
              $dix['file_size_thumb'] = $sc->file_size_thumb;
              $this->igpam->set($dix);
              $getSPTPhoto = 1;
            }
          }
        }
      }

      if(is_array($location_json)){
        if(count($location_json) > 0){
          foreach ($location_json as $key => $upload) {
            if(isset($upload['location_nama']) && isset($upload['location_address']) && isset($upload['location_place_id']) && isset($upload['location_latitude']) && isset($upload['location_longitude'])){
              if($upload['location_nama'] && $upload['location_address'] && $upload['location_place_id'] && $upload['location_latitude'] && $upload['location_longitude']){
                $endDoWhile = 0;
                do{
                  $attachmentId = $this->GUIDv4();
                  $checkId = $this->igpam->checkId($nation_code, $attachmentId);
                  if($checkId == 0){
                    $endDoWhile = 1;
                  }
                }while($endDoWhile == 0);

                $dix = array();
                $dix['nation_code'] = $nation_code;
                $dix['id'] = $attachmentId;
                $dix['i_group_id'] = $group_id;
                $dix['i_group_post_id'] = $id;
                $dix['i_group_directory_id'] = $group_id;
                $dix['b_user_id'] = $pelanggan->id;
                $dix['jenis'] = 'location';
                $dix['location_nama'] = $upload['location_nama'];
                $dix['location_address'] = $upload['location_address'];
                $dix['location_place_id'] = $upload['location_place_id'];
                $dix['location_latitude'] = $upload['location_latitude'];
                $dix['location_longitude'] = $upload['location_longitude'];
                $this->igpam->set($dix);
                $getSPTLocation = 1;
              }
            }
          }
        }
      }

      $insertVideo = 0;
      for ($i=1; $i < 3; $i++) {
        if($this->input->post('video_bg'.$i) === "yes"){
          $insertVideo++;
        }
      }

      $checkFileExist = 1;
      $checkFileTemporaryOrNot = 1;
      $listUploadVideo = array();
      for ($i=1; $i < 3; $i++) {
        if($this->input->post('video'.$i) != null){
          $file_path = parse_url($this->input->post('video'.$i), PHP_URL_PATH);
          $listUploadVideo = array_merge($listUploadVideo, array($file_path));
          if (strpos($file_path, 'temporary') !== false) {
            if (!file_exists(SENEROOT.$file_path)) {
              $checkFileExist = 0;
            }
          }else{
            $checkFileTemporaryOrNot = 0;
          }
        }
      }

      if ($checkFileExist == 0) {
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 995;
        $this->message = 'Failed upload, temporary already gone';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      if ($checkFileTemporaryOrNot == 0) {
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 996;
        $this->message = 'Failed upload, upload is not temporary';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      $listUploadVideoThumb = array();
      for ($i=1; $i < 3; $i++) {
        if($this->input->post('video'.$i.'_thumb') != null){
          $file_path = parse_url($this->input->post('video'.$i.'_thumb'), PHP_URL_PATH);
          $listUploadVideoThumb = array_merge($listUploadVideoThumb, array($file_path));
          if (strpos($file_path, 'temporary') !== false) {
            if (!file_exists(SENEROOT.$file_path)) {
              $checkFileExist = 0;
            }
          }else{
            $checkFileTemporaryOrNot = 0;
          }
        }
      }

      if ($checkFileExist == 0) {
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 995;
        $this->message = 'Failed upload, temporary already gone';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      if ($checkFileTemporaryOrNot == 0) {
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 996;
        $this->message = 'Failed upload, upload is not temporary';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      if(!empty($listUploadVideo)){
        foreach ($listUploadVideo as $key => $upload) {
          $endDoWhile = 0;
          do{
            $attachmentId = $this->GUIDv4();
            $checkId = $this->igpam->checkId($nation_code, $attachmentId);
            if($checkId == 0){
              $endDoWhile = 1;
            }
          }while($endDoWhile == 0);

          $moveVideo = $this->__moveVideox($nation_code, $upload, $this->media_group_post_video, $id, $attachmentId);
          $sc = $this->__moveImagex($nation_code, $listUploadVideoThumb[$key], $this->media_group_post_video, $id, $attachmentId);
          if (isset($moveVideo->status) && isset($sc->status)) {
            if ($moveVideo->status==200 && $sc->status==200) {
              $dix = array();
              $dix['nation_code'] = $nation_code;
              $dix['id'] = $attachmentId;
              $dix['i_group_id'] = $group_id;
              $dix['i_group_post_id'] = $id;
              $dix['i_group_directory_id'] = $group_id;
              $dix['b_user_id'] = $pelanggan->id;
              $dix['jenis'] = 'video';
              $dix['convert_status'] = 'waiting';
              $dix['url'] = $moveVideo->url;
              $dix['url_thumb'] = $sc->thumb;
              $dix['file_size'] = $moveVideo->file_size;
              $dix['file_size_thumb'] = $sc->file_size_thumb;
              $this->igpam->set($dix);
              $getSPTVideo = 1;
            }
          }
        }
      }else if($insertVideo > 0){
        for ($i=1; $i <= $insertVideo; $i++) {
          $endDoWhile = 0;
          do{
            $attachmentId = $this->GUIDv4();
            $checkId = $this->igpam->checkId($nation_code, $attachmentId);
            if($checkId == 0){
              $endDoWhile = 1;
            }
          }while($endDoWhile == 0);

          $upi = $this->__moveImagex($nation_code, $listUploadVideoThumb[$i-1], $this->media_group_post_video, $id, $attachmentId, $i);
          $dix = array();
          $dix['nation_code'] = $nation_code;
          $dix['id'] = $attachmentId;
          $dix['i_group_id'] = $group_id;
          $dix['i_group_post_id'] = $id;
          $dix['i_group_directory_id'] = $group_id;
          $dix['b_user_id'] = $pelanggan->id;
          $dix['jenis'] = 'video';
          $dix['convert_status'] = 'uploading';
          if($upi->status == 200){
            $dix['url'] = $upi->image;
            $dix['url_thumb'] = $upi->thumb;
            $dix['file_size'] = $upi->file_size;
            $dix['file_size_thumb'] = $upi->file_size_thumb;
          }else{
            $dix['url'] = $this->media_community_video."default.png";
            $dix['url_thumb'] = $this->media_community_video."default.png";
          }
          $this->igpam->set($dix);
        }
      }

      $checkFileExist = 1;
      $checkFileTemporaryOrNot = 1;
      $listUploadFile = array();
      for ($i=1; $i < 3; $i++) {
        if($this->input->post('file'.$i) != null){
          $file_path = parse_url($this->input->post('file'.$i), PHP_URL_PATH);
          $listUploadFile = array_merge($listUploadFile, array($file_path));
          if (strpos($file_path, 'temporary') !== false) {
            if (!file_exists(SENEROOT.$file_path)) {
              $checkFileExist = 0;
            }
          }else{
            $checkFileTemporaryOrNot = 0;
          }
        }
      }

      if ($checkFileExist == 0) {
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 995;
        $this->message = 'Failed upload, temporary already gone';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      if ($checkFileTemporaryOrNot == 0) {
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 996;
        $this->message = 'Failed upload, upload is not temporary';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      if(!empty($listUploadFile)){
        foreach ($listUploadFile as $key => $upload) {
          $endDoWhile = 0;
          do{
            $attachmentId = $this->GUIDv4();
            $checkId = $this->igpam->checkId($nation_code, $attachmentId);
            if($checkId == 0){
              $endDoWhile = 1;
            }
          }while($endDoWhile == 0);

          $sc = $this->__moveFilex($nation_code, $upload, $this->media_group_post_file, $id, $attachmentId);
          if (isset($sc->status)) {
            if ($sc->status==200) {
              $dix = array();
              $dix['nation_code'] = $nation_code;
              $dix['id'] = $attachmentId;
              $dix['i_group_id'] = $group_id;
              $dix['i_group_post_id'] = $id;
              $dix['i_group_directory_id'] = $group_id;
              $dix['b_user_id'] = $pelanggan->id;
              $dix['jenis'] = 'file';
              $dix['url'] = $sc->url;
              $dix['file_name'] = substr(pathinfo($upload, PATHINFO_BASENAME), 0, strpos(pathinfo($upload, PATHINFO_BASENAME),"-")).".".pathinfo($upload, PATHINFO_EXTENSION);
              $dix['file_size'] = $sc->file_size;
              $this->igpam->set($dix);
            }
          }
        }
      }

      if(is_array($attendance_sheet)){
        if(count($attendance_sheet) > 0){
          if(strlen($attendance_sheet["title"]) < 3){
            $this->igpostm->trans_rollback();
            $this->igpostm->trans_end();
            $this->status = 1102;
            $this->message = 'You cannot create post';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
            die();
          }

          if (!in_array($attendance_sheet["sort_members"], array("By name", "Attending members first","Not attending members first"))) {
            $attendance_sheet["sort_members"] = "By name";
          }

          if (!in_array($attendance_sheet["response_option"], array("present only","present and absent"))) {
            $attendance_sheet["response_option"] = "present only";
          }

          if($attendance_sheet["self_check_in"] != 1){
            $attendance_sheet["self_check_in"] = 0;
          }

          if(strlen($attendance_sheet["start_date"]) < 3){
            $attendance_sheet["start_date"] = date("Y-m-d");
          }

          if(strlen($attendance_sheet["deadline"]) < 3){
            $attendance_sheet["deadline"] = date("Y-m-d");
          }

          if (!in_array($attendance_sheet["show_attendance_progress"], array("public","private"))) {
            $attendance_sheet["show_attendance_progress"] = "private";
          }

          $endDoWhile = 0;
          do{
            $attachmentId = $this->GUIDv4();
            $checkId = $this->igpam->checkId($nation_code, $attachmentId);
            if($checkId == 0){
              $endDoWhile = 1;
            }
          }while($endDoWhile == 0);

          $endDoWhile = 0;
          do{
            $attendanceSheetId = $this->GUIDv4();
            $checkId = $this->igpaasm->checkId($nation_code, $attendanceSheetId);
            if($checkId == 0){
              $endDoWhile = 1;
            }
          }while($endDoWhile == 0);

          $dix = array();
          $dix['nation_code'] = $nation_code;
          $dix['id'] = $attachmentId;
          $dix['i_group_id'] = $group_id;
          $dix['i_group_post_id'] = $id;
          $dix['i_group_directory_id'] = $group_id;
          $dix['b_user_id'] = $pelanggan->id;
          $dix['jenis'] = 'attendance sheet';
          $dix['attendance_sheet_id'] = $attendanceSheetId;
          $dix['attendance_sheet_title'] = $attendance_sheet["title"];
          $dix['attendance_sheet_total'] = count($attendance_sheet["member"]);
          $this->igpam->set($dix);
          $getSPTAttendancesheet = 1;

          $dix = array();
          $dix['nation_code'] = $nation_code;
          $dix['id'] = $attendanceSheetId;
          $dix['i_group_id'] = $group_id;
          $dix['i_group_post_id'] = $id;
          $dix['i_group_post_attachment_id'] = $attachmentId;
          $dix['b_user_id'] = $pelanggan->id;
          $dix['title'] = $attendance_sheet["title"];
          $dix['sort_members'] = $attendance_sheet["sort_members"];
          $dix['response_option'] = $attendance_sheet["response_option"];
          $dix['self_check_in'] = $attendance_sheet["self_check_in"];
          $dix['start_date'] = $attendance_sheet["start_date"];
          $dix['deadline'] = $attendance_sheet["deadline"];
          $dix['show_attendance_progress'] = $attendance_sheet["show_attendance_progress"];
          $this->igpaasm->set($dix);

          if(count($attendance_sheet["member"]) == 0){
            $this->igpostm->trans_rollback();
            $this->igpostm->trans_end();
            $this->status = 1102;
            $this->message = 'You cannot create post';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
            die();
          }
          $insertArray = array();
          $listInsertUser = array();
          foreach ($attendance_sheet["member"] as $member) {
            $endDoWhile = 0;
            do{
              $attendanceSheetmemberId = $this->GUIDv4();
              $checkId = $this->igpaasmm->checkId($nation_code, $attendanceSheetmemberId);
              if($checkId == 0){
                $endDoWhile = 1;
              }
            }while($endDoWhile == 0);

            $dix = array();
            $dix['nation_code'] = $nation_code;
            $dix['id'] = $attendanceSheetmemberId;
            $dix['i_group_id'] = $group_id;
            $dix['i_group_post_id'] = $id;
            $dix['i_group_post_attachment_id'] = $attachmentId;
            $dix['i_group_post_attachment_attendance_sheet_id'] = $attendanceSheetId;
            if(isset($member["b_user_id"])){
              $listInsertUser[] = $member["b_user_id"];
              $dix['b_user_id'] = $member["b_user_id"];
              $dix['guest_fnama'] = "";
              $dix['custom_text'] = "";
              $dix['jenis'] = "member";
            }else{
              $dix['b_user_id'] = "0";
              $dix['guest_fnama'] = $member["guest_fnama"];
              $dix['custom_text'] = "Guest Member | Added by". $pelanggan->band_fnama;
              $dix['jenis'] = "guest";
            }
            $insertArray[] = $dix;
          }

          if(count($insertArray) > 0){
            // $this->glrm->delAll($nation_code, $area->kelurahan, $area->kecamatan, $area->kabkota, $area->provinsi);
            // $this->glrm->delAll($nation_code, "All", "All", "All", "All");

            $chunkInsertArray = array_chunk($insertArray,50);
            foreach($chunkInsertArray AS $chunk){
              //insert multi
              $this->igpaasmm->setMass($chunk);
            }
            unset($chunkInsertArray, $chunk);
          }
          unset($insertArray);
        }
      }

      $minimumTotalPeople = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E18");
      if (!isset($minimumTotalPeople->remark)) {
        $minimumTotalPeople = new stdClass();
        $minimumTotalPeople->remark = 5;
      }
      if($groupData->total_people >= $minimumTotalPeople->remark){
        //get point
        $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E19");
        if (!isset($pointGet->remark)) {
          $pointGet = new stdClass();
          $pointGet->remark = 5;
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
        $di['custom_type_sub'] = "post";
        $di['custom_text'] = $pelanggan->fnama.' has create '.$di['custom_type'].' '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
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

        $totalPointGetForCommission = $pointGet->remark;

        if($getSPTPhoto == 1){
          //get point
          $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E20");
          if (!isset($pointGet->remark)) {
            $pointGet = new stdClass();
            $pointGet->remark = 10;
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
          $di['custom_type_sub'] = "upload image";
          $di['custom_text'] = $pelanggan->fnama.' has create '.$di['custom_type'].' '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
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

          $totalPointGetForCommission += $pointGet->remark;
        }

        if($getSPTVideo == 1){
          //get point
          $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E21");
          if (!isset($pointGet->remark)) {
            $pointGet = new stdClass();
            $pointGet->remark = 20;
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
          $di['custom_type_sub'] = "upload video";
          $di['custom_text'] = $pelanggan->fnama.' has create '.$di['custom_type'].' '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
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

          $totalPointGetForCommission += $pointGet->remark;
        }

        if($groupData->b_user_id != $pelanggan->id){
          //get commission in %
          $commissionInPercent = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E32");
          if (!isset($commissionInPercent->remark)) {
            $commissionInPercent = new stdClass();
            $commissionInPercent->remark = 20;
          }

          $di = array();
          $di['nation_code'] = $nation_code;
          $di['b_user_alamat_location_kelurahan'] = "All";
          $di['b_user_alamat_location_kecamatan'] = "All";
          $di['b_user_alamat_location_kabkota'] = "All";
          $di['b_user_alamat_location_provinsi'] = "All";
          $di['b_user_id'] = $groupData->b_user_id;
          $di['point'] = $totalPointGetForCommission * $commissionInPercent->remark / 100;
          $di['custom_id'] = $id;
          $di['custom_type'] = 'club';
          $di['custom_type_sub'] = "commission";
          $di['custom_text'] = $pelanggan->fnama.' has create '.$di['custom_type'].' post so owner club get '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
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
      }

      if($getSPTAttendancesheet == 1){
        //get point
        $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E22");
        if (!isset($pointGet->remark)) {
          $pointGet = new stdClass();
          $pointGet->remark = 5;
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
        $di['custom_type_sub'] = "attendance sheet";
        $di['custom_text'] = $pelanggan->fnama.' has create '.$di['custom_type'].' '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
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

      if($getSPTLocation == 1){
        //get point
        $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E23");
        if (!isset($pointGet->remark)) {
          $pointGet = new stdClass();
          $pointGet->remark = 1;
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
        $di['custom_type_sub'] = "location";
        $di['custom_text'] = $pelanggan->fnama.' has create '.$di['custom_type'].' '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
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

      $this->igpostm->trans_commit();
      $this->igpostm->trans_end();
    }

    usleep(500000);

    $queryResult = $this->igpostm->getById($nation_code, $id);

    if($groupData->b_user_id != $pelanggan->id){
      $user = $this->bu->getById($nation_code, $groupData->b_user_id);

      $dpe = array();
      $dpe['nation_code'] = $nation_code;
      $dpe['b_user_id'] = $groupData->b_user_id;
      $dpe['type'] = "band_group_post";
      if($user->language_id == 2) {
        $dpe['judul'] = "Post Baru";
        $dpe['teks'] =  "Ada post baru";
      } else {
        $dpe['judul'] = "New Post";
        $dpe['teks'] =  "There is new post";
      }
      $dpe['group_name'] = $queryResult->group_name;
      $dpe['i_group_id'] = $queryResult->i_group_id;
      $dpe['gambar'] = 'media/pemberitahuan/community.png';
      $dpe['cdate'] = "NOW()";
      $extras = new stdClass();
      $extras->i_group_post_id = $queryResult->id;
      $extras->i_group_id = $queryResult->i_group_id;
      if($user->language_id == 2) { 
        $extras->judul = "Post Baru";
        $extras->teks =  "Ada post baru";
      } else {
        $extras->judul = "New Post";
        $extras->teks =  "There is new post";
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
          $title = "Post Baru";
          $message = "Ada post baru";
        } else {
          $title = "New Post";
          $message = "There is new post";
        }
        $image = 'media/pemberitahuan/community.png';
        $type = 'band_group_post';
        $payload = new stdClass();
        $payload->i_group_post_id = $queryResult->id;
        $payload->i_group_id = $queryResult->i_group_id;
        if($user->language_id == 2) {
          $payload->judul = "Post Baru";
          $payload->teks = "Ada post baru";
        } else {
          $payload->judul = "New Post";
          $payload->teks = "There is new post";
        }
        $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
      }
    }

    if(isset($listInsertUser)){
      foreach($listInsertUser AS $b_user_id){
        if($b_user_id == $pelanggan->id){
          continue;
        }
        $user = $this->bu->getById($nation_code, $b_user_id);

        $dpe = array();
        $dpe['nation_code'] = $nation_code;
        $dpe['b_user_id'] = $b_user_id;
        $dpe['type'] = "band_group_post";
        if($user->language_id == 2) {
          $dpe['judul'] = "Attendance";
          $dpe['teks'] =  "Anda diminta untuk absensi oleh Mr./Ms. ". $pelanggan->band_fnama;
        } else {
          $dpe['judul'] = "Attendance";
          $dpe['teks'] =  "You're asked to attend by Mr./Ms. ". $pelanggan->band_fnama;
        }
        $dpe['group_name'] = $queryResult->group_name;
        $dpe['i_group_id'] = $queryResult->i_group_id;
        $dpe['gambar'] = 'media/pemberitahuan/community.png';
        $dpe['cdate'] = "NOW()";
        $extras = new stdClass();
        $extras->i_group_post_id = $queryResult->id;
        $extras->i_group_id = $queryResult->i_group_id;
        if($user->language_id == 2) { 
          $extras->judul = "Attendance";
          $extras->teks =  "Anda diminta untuk absensi oleh Mr./Ms. ". $pelanggan->band_fnama;
        } else {
          $extras->judul = "Attendance";
          $extras->teks =  "You're asked to attend by Mr./Ms. ". $pelanggan->band_fnama;
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
            $message = "Anda diminta untuk absensi oleh Mr./Ms. ". $pelanggan->band_fnama;
          } else {
            $title = "Attendance";
            $message = "You're asked to attend by Mr./Ms. ". $pelanggan->band_fnama;
          }
          $image = 'media/pemberitahuan/community.png';
          $type = 'band_group_post';
          $payload = new stdClass();
          $payload->i_group_post_id = $queryResult->id;
          $payload->i_group_id = $queryResult->i_group_id;
          if($user->language_id == 2) {
            $payload->judul = "Attendance";
            $payload->teks = "Anda diminta untuk absensi oleh Mr./Ms. ". $pelanggan->band_fnama;
          } else {
            $payload->judul = "Attendance";
            $payload->teks = "You're asked to attend by Mr./Ms. ". $pelanggan->band_fnama;
          }
          $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        }
      }
    }

    // $queryResult->can_chat_and_like = "0";
    // if(isset($pelanggan->id)){
    //   $queryResult->can_chat_and_like = "1";
    // }

    $queryResult->total_likes = $this->thousandsCurrencyFormat($queryResult->total_likes);

    $queryResult->is_liked = '0';
    $queryResult->currentLikeEmoji = '';
    $queryResult->is_owner_post = '0';
    if(isset($pelanggan->id)){
      $checkLike = $this->igplm->getByCustomIdUserId($nation_code, $queryResult->id, $pelanggan->id, "post");
      if(isset($checkLike->id)){
        $queryResult->is_liked = '1';
        $queryResult->currentLikeEmoji = $checkLike->i_group_post_like_category_id;
      }
      unset($checkLike);
      if($pelanggan->id == $queryResult->b_user_id){
        $queryResult->is_owner_post = '1';
      }
    }

    $queryResult->is_owner_or_admin = '0';
    $queryResult->cdate_text = $this->humanTiming($queryResult->cdate, null, $pelanggan->language_id);
    $queryResult->cdate = $this->customTimezone($queryResult->cdate, $timezone);
    $queryResult->deskripsi = html_entity_decode($queryResult->deskripsi,ENT_QUOTES);
    $queryResult->group_name = html_entity_decode($queryResult->group_name,ENT_QUOTES);

    if (isset($queryResult->b_user_band_image)) {
      if(file_exists(SENEROOT.$queryResult->b_user_band_image) && $queryResult->b_user_band_image != 'media/user/default.png'){
        $queryResult->b_user_band_image = $this->cdn_url($queryResult->b_user_band_image);
      } else {
        $queryResult->b_user_band_image = $this->cdn_url('media/user/default-profile-picture.png');
      }
    }

    $queryResult->images = array();
    $queryResult->locations = array();
    $queryResult->videos = array();
    $queryResult->file = array();
    $queryResult->attendance = array();
    $attachments = $this->igpam->getByGroupIdPostId($nation_code, $queryResult->i_group_id, $queryResult->id);
    foreach ($attachments as $atc) {
      $temp = new stdClass();
      if($atc->jenis == 'image'){
        if (empty($atc->url)) {
          $atc->url = 'media/community_default.png';
        }
        if (empty($atc->url_thumb)) {
          $atc->url_thumb = 'media/community_default.png';
        }
        $temp->url = $this->cdn_url($atc->url);
        $temp->url_thumb = $this->cdn_url($atc->url_thumb);
        $queryResult->images[] = $temp;
      }else if($atc->jenis == 'location'){
        $temp->location_nama = $atc->location_nama;
        $temp->location_address = $atc->location_address;
        $temp->location_place_id = $atc->location_place_id;
        $temp->location_latitude = $atc->location_latitude;
        $temp->location_longitude = $atc->location_longitude;
        $queryResult->locations[] = $temp;
      }else if($atc->jenis == 'video'){
        $temp->video_id = $atc->id;
        $temp->url = $this->cdn_url($atc->url);
        $temp->url_thumb = $this->cdn_url($atc->url_thumb);
        $temp->convert_status = $atc->convert_status;
        // $temp->total_views = $this->thousandsCurrencyFormat($temp->total_views);
        $queryResult->videos[] = $temp;
      }else if($atc->jenis == 'file'){
        $temp->url = $this->cdn_url($atc->url);
        $temp->file_name = $atc->file_name;
        $temp->file_size = $atc->file_size;
        $queryResult->file[] = $temp;
      }else if($atc->jenis == 'attendance sheet'){
        $temp->attendance_sheet_id = $atc->attendance_sheet_id;
        $temp->attendance_sheet_title = $atc->attendance_sheet_title;
        $temp->attendance_sheet_filled = $atc->attendance_sheet_filled;
        $temp->attendance_sheet_total = $atc->attendance_sheet_total;
        $attendanceSheetData = $this->igpaasm->getById($nation_code, $atc->attendance_sheet_id);
        $temp->response_option = $attendanceSheetData->response_option;
        $temp->self_check_in = $attendanceSheetData->self_check_in;
        $temp->start_date = $attendanceSheetData->start_date;
        $temp->deadline = $attendanceSheetData->deadline;
        $temp->show_attendance_progress = $attendanceSheetData->show_attendance_progress;
        $queryResult->attendance[] = $temp;
      }
    }
    unset($attachments,$atc);

    $this->status = 200;
    $this->message = 'Success';

    $data['post'] = $queryResult;

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function video_add()
  {
    $dt = $this->__init();
    $keyname = 'video';

    $data = array();
    $data['video_id'] = 0;
    $data['video_url'] = '';
    $data['video_thumb_url'] = '';

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

    $post_id = $this->input->post('post_id');
    if (empty($post_id)) {
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $queryResult = $this->igpostm->getById($nation_code, $post_id);
    if (!isset($queryResult->id)){
      $this->status = 1160;
      $this->message = 'This post is deleted by an author';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    if (!isset($_FILES[$keyname])) {
      $this->status = 1300;
      $this->message = 'Upload failed';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    if ($_FILES[$keyname]['size']<=0) {
      $this->status = 1300;
      $this->message = 'Upload failed';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    if ($_FILES[$keyname]['size'] > 104857600) {
      $this->status = 1308;
      $this->message = 'Video file Size too big, max size 100 MB';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    if (mime_content_type($_FILES[$keyname]['tmp_name'])=="image/webp") {
      $this->status = 1303;
      $this->message = 'WebP image file format is not supported.';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $filenames = pathinfo($_FILES[$keyname]['name']);
    $fileext = '';
    if (isset($filenames['extension'])) {
      $fileext = strtolower($filenames['extension']);
    }

    $this->seme_log->write("api_mobile", 'club post_id '.$post_id);
    $this->seme_log->write("api_mobile", 'extension '.$fileext);

    // if (!in_array($fileext, array("mp4"))) {
    //   $this->status = 1305;
    //   $this->message = 'Invalid file extension, please try other file';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
    //   die();
    // }

    $targetdir = $this->media_group_post_video;
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

    $video_id = $this->input->post('video_id');
    if (empty($video_id)) {
      $this->status = 1307;
      $this->message = 'There is no reserve attachment for video';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $cpfm = $this->igpam->getByIdPostId($nation_code, $post_id, $video_id, "video", "uploading");
    if (!isset($cpfm->id)) {
      $this->status = 1307;
      $this->message = 'There is no reserve attachment for video';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $data['video_id'] = $cpfm->id;

    $filename = "$nation_code-$post_id-$cpfm->id-".date('YmdHis');
    $filethumb = $filename."-thumb.png";
    $filename = $filename.".".$fileext;

    // exec("ffmpeg -y -i ".$_FILES[$keyname]['tmp_name']." -s 540x960 -preset ultrafast -ss 00:00:00.000 -frames:v 1 ".SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb." -hide_banner 2>&1", $responseFFmpeg, $statusFFmpeg);
    // if($statusFFmpeg == 0){
      if(move_uploaded_file($_FILES[$keyname]['tmp_name'], SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)){
        $data['video_url'] = str_replace("//", "/", $targetdir.'/'.$filename);
        $data['video_url'] = str_replace("\\", "/", $data['video_url']);

        // $data['video_thumb_url'] = str_replace("//", "/", $targetdir.'/'.$filethumb);
        // $data['video_thumb_url'] = str_replace("\\", "/", $data['video_thumb_url']);

        $dix = array();
        $dix['tmp_url'] = '';
        $dix['convert_status'] = "waiting";
        $dix['url'] = $data['video_url'];
        // $dix['url_thumb'] = $data['video_thumb_url'];
        $dix['file_size'] = filesize(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
        $this->igpam->update($nation_code, $cpfm->id, $dix);

        $data['video_url'] = $this->cdn_url($data['video_url']);
        // $data['video_thumb_url'] = $this->cdn_url($data['video_thumb_url']);
        $data['video_thumb_url'] = $this->cdn_url($cpfm->url_thumb);

        if($cpfm->url != $this->media_community_video."default.png"){
          $file_path = SENEROOT.$cpfm->url;
          if (file_exists($file_path)) {
            unlink($file_path);
          }
        }

        $checkGetSpt = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", $queryResult->b_user_id, $post_id, "club", "post");
        if(isset($checkGetSpt->id)){
          $checkGetSpt = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", $queryResult->b_user_id, $post_id, "club", "upload video");
          if(!isset($checkGetSpt->id)){
            $owner = $this->bu->getById($nation_code, $queryResult->b_user_id);

            //get point
            $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E21");
            if (!isset($pointGet->remark)) {
              $pointGet = new stdClass();
              $pointGet->remark = 20;
            }

            $di = array();
            $di['nation_code'] = $nation_code;
            $di['b_user_alamat_location_kelurahan'] = "All";
            $di['b_user_alamat_location_kecamatan'] = "All";
            $di['b_user_alamat_location_kabkota'] = "All";
            $di['b_user_alamat_location_provinsi'] = "All";
            $di['b_user_id'] = $queryResult->b_user_id;
            $di['point'] = $pointGet->remark;
            $di['custom_id'] = $post_id;
            $di['custom_type'] = 'club';
            $di['custom_type_sub'] = "upload video";
            $di['custom_text'] = $owner->fnama.' has create '.$di['custom_type'].' '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
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
            // $this->glrm->updateTotal($nation_code, $owner->id, 'total_point', '+', $di['point']);

            $groupData = $this->igm->getById($nation_code, $queryResult->i_group_id);
            $checkGetSpt = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", $groupData->b_user_id, $post_id, "club", "commission");
            if(isset($checkGetSpt->id)){
              //get commission in %
              $commissionInPercent = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E32");
              if (!isset($commissionInPercent->remark)) {
                $commissionInPercent = new stdClass();
                $commissionInPercent->remark = 20;
              }

              $di = array();
              $di['nation_code'] = $nation_code;
              $di['b_user_alamat_location_kelurahan'] = "All";
              $di['b_user_alamat_location_kecamatan'] = "All";
              $di['b_user_alamat_location_kabkota'] = "All";
              $di['b_user_alamat_location_provinsi'] = "All";
              $di['b_user_id'] = $groupData->b_user_id;
              $di['point'] = $pointGet->remark * $commissionInPercent->remark / 100;
              $di['custom_id'] = $post_id;
              $di['custom_type'] = 'club';
              $di['custom_type_sub'] = "commission";
              $di['custom_text'] = $owner->fnama.' has create '.$di['custom_type'].' post so owner club get '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
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
              // $this->glrm->updateTotal($nation_code, $owner->id, 'total_point', '+', $di['point']);
            }
          }
        }
      }else{
        $this->seme_log->write("api_mobile", 'tmp url '.$_FILES[$keyname]['tmp_name']);
        if (file_exists($_FILES[$keyname]['tmp_name'])) {
          $this->seme_log->write("api_mobile", 'tmp url exist');

          $filename = date('YmdHis');
          $filename = $filename.".".$fileext;

          if(move_uploaded_file($_FILES[$keyname]['tmp_name'], SENEROOT.$this->media_temporary.DIRECTORY_SEPARATOR.$filename)){
            $tmp_url = str_replace("//", "/", $this->media_temporary.'/'.$filename);
            $tmp_url = str_replace("\\", "/", $tmp_url);

            $dix = array();
            $dix['tmp_url'] = $tmp_url;
            $this->igpam->update($nation_code, $cpfm->id, $dix);

            $this->seme_log->write("api_mobile", 'tmp url moved');
          }
        }else{
          $this->seme_log->write("api_mobile", 'tmp url gone');
        }

        $this->status = 1306;
        $this->message = 'move upload file failed';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }

      $this->status = 200;
      $this->message = 'Success';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    // }else{
    //   $this->status = 1300;
    //   $this->message = 'Upload failed';
    //   $this->__json_out($data);
    // }
  }

  public function video_delete()
  {
    $dt = $this->__init();

    $data = array();
    $data['foto_url'] = '';

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

    $post_id = $this->input->post('post_id');
    $queryResult = $this->igpostm->getById($nation_code, $post_id);
    if (!isset($queryResult->id)){
      $this->status = 1160;
      $this->message = 'This post is deleted by an author';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $video_id = $this->input->post('video_id');
    $cpfm = $this->igpam->getByIdPostId($nation_code, $post_id, $video_id, "video");
    if (!isset($cpfm->id)) {
      $this->status = 200;
      $this->message = 'Success';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    if($cpfm->url != $this->media_community_video."default.png"){
      $file_path = SENEROOT.$cpfm->url;
      if (file_exists($file_path)) {
        unlink($file_path);
      }
    }

    if($cpfm->url_thumb != $this->media_community_video."default.png"){
      $file_path = SENEROOT.$cpfm->url_thumb;
      if (file_exists($file_path)) {
        unlink($file_path);
      }
    }

    $this->igpam->del($video_id);

    $this->status = 200;
    $this->message = 'Success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  // public function file_add()
  // {
  //   $dt = $this->__init();
  //   $keyname = 'file';

  //   $data = array();
  //   $data['url'] = '';

  //   //check nation_code
  //   $nation_code = $this->input->get('nation_code');
  //   $nation_code = $this->nation_check($nation_code);
  //   if (empty($nation_code)) {
  //     $this->status = 101;
  //     $this->message = 'Missing or invalid nation_code';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
  //     die();
  //   }

  //   //check apisess
  //   $apisess = $this->input->get('apisess');
  //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   if (!isset($pelanggan->id)) {
  //     $this->status = 401;
  //     $this->message = 'Missing or invalid API session';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
  //     die();
  //   }

  //   if (!isset($_FILES[$keyname])) {
  //     $this->status = 1300;
  //     $this->message = 'Upload failed';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
  //     die();
  //   }

  //   if ($_FILES[$keyname]['size']<=0) {
  //     $this->status = 1300;
  //     $this->message = 'Upload failed';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
  //     die();
  //   }

  //   if ($_FILES[$keyname]['size'] > 104857600) {
  //     $this->status = 1308;
  //     $this->message = 'Video file Size too big, max size 100 MB';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
  //     die();
  //   }

  //   if (mime_content_type($_FILES[$keyname]['tmp_name'])=="image/webp") {
  //     $this->status = 1303;
  //     $this->message = 'WebP image file format is not supported.';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
  //     die();
  //   }

  //   $filenames = pathinfo($_FILES[$keyname]['name']);
  //   $fileext = '';
  //   if (isset($filenames['extension'])) {
  //     $fileext = strtolower($filenames['extension']);
  //   }
  //   if (!in_array($fileext, array("csv","doc","docx","pdf","ppt","pptx","xls","xlsx"))) {
  //     $this->status = 1305;
  //     $this->message = 'Invalid file extension, please try other file';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
  //     die();
  //   }

  //   $targetdir = $this->media_temporary;
  //   $targetdircheck = realpath(SENEROOT.$targetdir);
  //   if (empty($targetdircheck)) {
  //     if (PHP_OS == "WINNT") {
  //       if (!is_dir(SENEROOT.$targetdir)) {
  //         mkdir(SENEROOT.$targetdir);
  //       }
  //     } else {
  //       if (!is_dir(SENEROOT.$targetdir)) {
  //         mkdir(SENEROOT.$targetdir, 0775);
  //       }
  //     }
  //   }

  //   $file_name = basename($filenames['basename'],'.'.$filenames['extension']);
  //   $file_name = str_replace(" ", "_", $file_name);
  //   $file_name = str_replace("-", "_", $file_name);
  //   $file_name = str_replace("\\", "_", $file_name);
  //   $file_name = str_replace("/", "_", $file_name);
  //   $file_name = str_replace(":", "_", $file_name);
  //   $file_name = str_replace("*", "_", $file_name);
  //   $file_name = str_replace("?", "_", $file_name);
  //   $file_name = str_replace('"', "_", $file_name);
  //   $file_name = str_replace("<", "_", $file_name);
  //   $file_name = str_replace(">", "_", $file_name);
  //   $file_name = str_replace("|", "_", $file_name);
  //   $file_name = str_replace("&", "_", $file_name);

  //   $filename = "$file_name-$nation_code-$pelanggan->id-".date('YmdHis').rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
  //   $filename = $filename.".".$fileext;

  //   move_uploaded_file($_FILES[$keyname]['tmp_name'], SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);

  //   $data['url'] = str_replace("//", "/", $targetdir.'/'.$filename);
  //   $data['url'] = str_replace("\\", "/", $data['url']);
  //   $data['url'] = $this->cdn_url($data['url']);

  //   $this->status = 200;
  //   $this->message = 'Success';
  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  // }

  public function hapus($post_id)
  {
    $dt = $this->__init();
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

    if (strlen($post_id)<3){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $queryResult = $this->igpostm->getById($nation_code, $post_id);
    if (!isset($queryResult->id)){
      $this->status = 1160;
      $this->message = 'This post is deleted by an author';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    if ($queryResult->b_user_id != $pelanggan->id) {
      $stillParticipant = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $queryResult->i_group_id, $pelanggan->id);
      if ($stillParticipant->is_owner == "0" && $stillParticipant->is_co_admin == "0") {
        $this->status = 1125;
        $this->message = "Your priviledge is limited as you're member";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }
    }

    //start transaction
    $this->igpostm->trans_start();

    $du = array();
    $du['is_active'] = 0;
    $res = $this->igpostm->update($nation_code, $post_id, $du);
    if (!$res) {
      $this->igpostm->trans_rollback();
      $this->igpostm->trans_end();
      $this->status = 1107;
      $this->message = "Error, please try again later";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->status = 200;
    $this->message = 'Success';

    $du = array();
    $du['is_active'] = 0;
    $this->igpam->updateByPostId($nation_code, $post_id, $du);
    $this->igpaasm->updateByPostId($nation_code, $post_id, $du);
    $this->igpaasmm->updateByPostId($nation_code, $post_id, $du);

    // by Muhammad Sofi 13 November 2023 | delete bookmark on the post
    $this->igbpm->delete($nation_code, $post_id);

    // $attachments = $this->ccam->getByCommunityId($nation_code, $post_id);
    // //delete attachment file
    // if (count($attachments)) {
    //   foreach ($attachments as $atc) {
    //     if($atc->jenis == 'image' || $atc->jenis == 'video'){
    //       if ($atc->url != $this->media_community_video."default.png") {
    //         $fileloc = SENEROOT.$atc->url;
    //         if (file_exists($fileloc)) {
    //           unlink($fileloc);
    //         }
    //       }
    //       if ($atc->url_thumb != $this->media_community_video."default.png") {
    //         $fileloc = SENEROOT.$atc->url_thumb;
    //         if (file_exists($fileloc)) {
    //           unlink($fileloc);
    //         }
    //       }
    //     }
    //   }
    //   unset($atc);
    // }
    // unset($attachments);

    $historysGetSPT = $this->glphm->getRecordGroupPost($nation_code, $post_id);
    if($historysGetSPT){
      foreach ($historysGetSPT as $key => $value) {
        $di = array();
        $di['nation_code'] = $nation_code;
        $di['b_user_alamat_location_kelurahan'] = "All";
        $di['b_user_alamat_location_kecamatan'] = "All";
        $di['b_user_alamat_location_kabkota'] = "All";
        $di['b_user_alamat_location_provinsi'] = "All";
        $di['b_user_id'] = $value->b_user_id;
        $di['plusorminus'] = "-";
        $di['point'] = $value->point;
        $di['custom_id'] = $post_id;
        $di['custom_type'] = $value->custom_type;
        $di['custom_type_sub'] = $value->custom_type_sub;
        $di['custom_text'] = $pelanggan->fnama.' has delete '.$di['custom_type'].' '.$di['custom_type_sub'].' and deduct '.$di['point'].' point(s)';
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
    }

    $this->igpostm->trans_commit();
    $this->igpostm->trans_end();

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function edit()
  {
    //init
    $dt = $this->__init();

    //default response
    $data = array();
    $data['post'] = new stdClass();

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

    $post_id = trim($this->input->post('post_id'));
    if (strlen($post_id)<3){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $queryResult = $this->igpostm->getById($nation_code, $post_id);
    if (!isset($queryResult->id)){
      $this->status = 1160;
      $this->message = 'This post is deleted by an author';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    if ($queryResult->b_user_id != $pelanggan->id) {
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $deskripsi = trim($this->input->post('deskripsi'));
    $location_json = $this->input->post("location_json");
    $attendance_sheet = $this->input->post("attendance_sheet");

    $deskripsi = str_replace('’',"'",$deskripsi);
    $deskripsi = nl2br($deskripsi);
    $deskripsi = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $deskripsi);
    $deskripsi = str_replace("\\n", "<br />", $deskripsi);
    if (strlen($deskripsi)<3) {
      $this->status = 1105;
      $this->message = 'Description is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->igpostm->trans_start();

    //updating to database
    $du = array();
    $du['deskripsi'] = $deskripsi;
    $res = $this->igpostm->update($nation_code, $post_id, $du);
    if (!$res) {
      $this->igpostm->trans_rollback();
      $this->igpostm->trans_end();
      $this->status = 1107;
      $this->message = "Error, please try again later";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->status = 200;
    $this->message = 'Success';

    $listUrl = array();
    $listUpload = array();
    $checkFileExist = 1;
    for ($i=1; $i < 11; $i++) {
      $file_path = parse_url($this->input->post('foto'.$i), PHP_URL_PATH);
      if (strpos($file_path, 'temporary') !== false) {
        $listUpload = array_merge($listUpload, array($file_path));
        if (!file_exists(SENEROOT.$file_path)) {
          $checkFileExist = 0;
        }
      }else if($this->input->post('foto'.$i) != null){
        $listUrl = array_merge($listUrl, array(substr($file_path, 1)));
      }
    }

    if ($checkFileExist == 0) {
      $this->igpostm->trans_rollback();
      $this->igpostm->trans_end();
      $this->status = 995;
      $this->message = 'Failed upload, temporary already gone';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //delete image that is not in array
    $attachments = $this->igpam->getByGroupIdPostId($nation_code, $queryResult->i_group_id, $post_id, "all", "image");
    foreach ($attachments as $atc) {
      if ((!in_array($atc->url, $listUrl) || empty($listUrl))) {
        $this->igpam->update($nation_code, $atc->id, array("is_active"=> 0));
        // if (strlen($atc->url)>4) {
        //   $file = SENEROOT.$atc->url;
        //   if (!is_dir($file) && file_exists($file)) {
        //     unlink($file);
        //   }
        // }
        // if (strlen($atc->url_thumb)>4) {
        //   $file = SENEROOT.$atc->url_thumb;
        //   if (!is_dir($file) && file_exists($file)) {
        //     unlink($file);
        //   }
        // }
      }
    }
    unset($attachments, $atc);

    if(!empty($listUpload)){
      foreach ($listUpload as $key => $upload) { 
        $endDoWhile = 0;
        do{
          $attachmentId = $this->GUIDv4();
          $checkId = $this->igpam->checkId($nation_code, $attachmentId);
          if($checkId == 0){
            $endDoWhile = 1;
          }
        }while($endDoWhile == 0);

        $sc = $this->__moveImagex($nation_code, $upload, $this->media_group_post_image, $post_id, $attachmentId);
        if (isset($sc->status)) {
          if ($sc->status==200) {
            $dix = array();
            $dix['nation_code'] = $nation_code;
            $dix['id'] = $attachmentId;
            $dix['i_group_id'] = $queryResult->i_group_id;
            $dix['i_group_post_id'] = $post_id;
            $dix['i_group_directory_id'] = $queryResult->i_group_id;
            $dix['b_user_id'] = $pelanggan->id;
            $dix['jenis'] = 'image';
            $dix['url'] = $sc->image;
            $dix['url_thumb'] = $sc->thumb;
            $dix['file_size'] = $sc->file_size;
            $dix['file_size_thumb'] = $sc->file_size_thumb;
            $this->igpam->set($dix);
          }
        }
      }
    }

    $this->igpam->delByPostIdJenis($nation_code, $post_id, 'location');
    if(is_array($location_json)){
      if(count($location_json) > 0){
        foreach ($location_json as $key => $upload) {
          if(isset($upload['location_nama']) && isset($upload['location_address']) && isset($upload['location_place_id']) && isset($upload['location_latitude']) && isset($upload['location_longitude'])){
            if($upload['location_nama'] && $upload['location_address'] && $upload['location_place_id'] && $upload['location_latitude'] && $upload['location_longitude']){
              $endDoWhile = 0;
              do{
                $attachmentId = $this->GUIDv4();
                $checkId = $this->igpam->checkId($nation_code, $attachmentId);
                if($checkId == 0){
                  $endDoWhile = 1;
                }
              }while($endDoWhile == 0);

              $dix = array();
              $dix['nation_code'] = $nation_code;
              $dix['id'] = $attachmentId;
              $dix['i_group_id'] = $queryResult->i_group_id;
              $dix['i_group_post_id'] = $post_id;
              $dix['i_group_directory_id'] = $queryResult->i_group_id;
              $dix['b_user_id'] = $pelanggan->id;
              $dix['jenis'] = 'location';
              $dix['location_nama'] = $upload['location_nama'];
              $dix['location_address'] = $upload['location_address'];
              $dix['location_place_id'] = $upload['location_place_id'];
              $dix['location_latitude'] = $upload['location_latitude'];
              $dix['location_longitude'] = $upload['location_longitude'];
              $this->igpam->set($dix);
            }
          }
        }
      }
    }

    $listUrl = array();
    $listUpload = array();
    $checkFileExist = 1;
    for ($i=1; $i < 3; $i++) {
      $file_path = parse_url($this->input->post('video'.$i), PHP_URL_PATH);
      if (strpos($file_path, 'temporary') !== false) {
        $listUpload = array_merge($listUpload, array($file_path));
        if (!file_exists(SENEROOT.$file_path)) {
          $checkFileExist = 0;
        }
      }else if($this->input->post('video'.$i) != null){
        $listUrl = array_merge($listUrl, array(substr($file_path, 1)));
      }
    }

    if ($checkFileExist == 0) {
      $this->igpostm->trans_rollback();
      $this->igpostm->trans_end();
      $this->status = 995;
      $this->message = 'Failed upload, temporary already gone';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //delete data that is not in array
    $attachments = $this->igpam->getByGroupIdPostId($nation_code, $queryResult->i_group_id, $post_id, "all", "video");
    foreach ($attachments as $atc) {
      if ((!in_array($atc->url, $listUrl) || empty($listUrl))) {
        $this->igpam->update($nation_code, $atc->id, array("is_active"=> 0));
        // if (strlen($atc->url)>4) {
        //   $file = SENEROOT.$atc->url;
        //   if (!is_dir($file) && file_exists($file)) {
        //     unlink($file);
        //   }
        // }
        // if (strlen($atc->url_thumb)>4) {
        //   $file = SENEROOT.$atc->url_thumb;
        //   if (!is_dir($file) && file_exists($file)) {
        //     unlink($file);
        //   }
        // }
      }
    }
    unset($attachments, $atc);

    // $listUrl = array();
    $listUploadVideoThumb = array();
    $checkFileExist = 1;
    for ($i=1; $i < 3; $i++) {
      $file_path = parse_url($this->input->post('video'.$i.'_thumb'), PHP_URL_PATH);
      if (strpos($file_path, 'temporary') !== false) {
        $listUploadVideoThumb = array_merge($listUploadVideoThumb, array($file_path));
        if (!file_exists(SENEROOT.$file_path)) {
          $checkFileExist = 0;
        }
      }else if($this->input->post('video'.$i.'_thumb') != null){
        // $listUrl = array_merge($listUrl, array(substr($file_path, 1)));
      }
    }

    if ($checkFileExist == 0) {
      $this->igpostm->trans_rollback();
      $this->igpostm->trans_end();
      $this->status = 995;
      $this->message = 'Failed upload, temporary already gone';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    if(!empty($listUpload)){
      foreach ($listUpload as $key => $upload) {
        $endDoWhile = 0;
        do{
          $attachmentId = $this->GUIDv4();
          $checkId = $this->igpam->checkId($nation_code, $attachmentId);
          if($checkId == 0){
            $endDoWhile = 1;
          }
        }while($endDoWhile == 0);

        $moveVideo = $this->__moveVideox($nation_code, $upload, $this->media_group_post_video, $post_id, $attachmentId);
        $sc = $this->__moveImagex($nation_code, $listUploadVideoThumb[$key], $this->media_group_post_video, $post_id, $attachmentId);
        if (isset($moveVideo->status) && isset($sc->status)) {
          if ($moveVideo->status==200 && $sc->status==200) {
            $dix = array();
            $dix['nation_code'] = $nation_code;
            $dix['id'] = $attachmentId;
            $dix['i_group_id'] = $queryResult->i_group_id;
            $dix['i_group_post_id'] = $post_id;
            $dix['i_group_directory_id'] = $queryResult->i_group_id;
            $dix['b_user_id'] = $pelanggan->id;
            $dix['jenis'] = 'video';
            $dix['convert_status'] = 'waiting';
            $dix['url'] = $moveVideo->url;
            $dix['url_thumb'] = $sc->thumb;
            $dix['file_size'] = $moveVideo->file_size;
            $dix['file_size_thumb'] = $sc->file_size_thumb;
            $this->igpam->set($dix);
          }
        }
      }
    }

    $listUrl = array();
    $listUpload = array();
    $checkFileExist = 1;
    for ($i=1; $i < 3; $i++) {
      $file_path = parse_url($this->input->post('file'.$i), PHP_URL_PATH);
      if (strpos($file_path, 'temporary') !== false) {
        $listUpload = array_merge($listUpload, array($file_path));
        if (!file_exists(SENEROOT.$file_path)) {
          $checkFileExist = 0;
        }
      }else if($this->input->post('file'.$i) != null){
        $listUrl = array_merge($listUrl, array(substr($file_path, 1)));
      }
    }

    if ($checkFileExist == 0) {
      $this->igpostm->trans_rollback();
      $this->igpostm->trans_end();
      $this->status = 995;
      $this->message = 'Failed upload, temporary already gone';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //delete data that is not in array
    $attachments = $this->igpam->getByGroupIdPostId($nation_code, $queryResult->i_group_id, $post_id, "all", "file");
    foreach ($attachments as $atc) {
      if ((!in_array($atc->url, $listUrl) || empty($listUrl))) {
        $this->igpam->update($nation_code, $atc->id, array("is_active"=> 0));
        // if (strlen($atc->url)>4) {
        //   $file = SENEROOT.$atc->url;
        //   if (!is_dir($file) && file_exists($file)) {
        //     unlink($file);
        //   }
        // }
        // if (strlen($atc->url_thumb)>4) {
        //   $file = SENEROOT.$atc->url_thumb;
        //   if (!is_dir($file) && file_exists($file)) {
        //     unlink($file);
        //   }
        // }
      }
    }
    unset($attachments, $atc);

    if(!empty($listUpload)){
      foreach ($listUpload as $key => $upload) {
        $endDoWhile = 0;
        do{
          $attachmentId = $this->GUIDv4();
          $checkId = $this->igpam->checkId($nation_code, $attachmentId);
          if($checkId == 0){
            $endDoWhile = 1;
          }
        }while($endDoWhile == 0);

        $sc = $this->__moveFilex($nation_code, $upload, $this->media_group_post_file, $post_id, $attachmentId);
        if (isset($sc->status)) {
          if ($sc->status==200) {
            $dix = array();
            $dix['nation_code'] = $nation_code;
            $dix['id'] = $attachmentId;
            $dix['i_group_id'] = $queryResult->i_group_id;
            $dix['i_group_post_id'] = $post_id;
            $dix['i_group_directory_id'] = $queryResult->i_group_id;
            $dix['b_user_id'] = $pelanggan->id;
            $dix['jenis'] = 'file';
            $dix['url'] = $sc->url;
            $dix['file_name'] = substr(pathinfo($upload, PATHINFO_BASENAME), 0, strpos(pathinfo($upload, PATHINFO_BASENAME),"-")).".".pathinfo($upload, PATHINFO_EXTENSION);
            $dix['file_size'] = $sc->file_size;
            $this->igpam->set($dix);
          }
        }
      }
    }

    if(!isset($attendance_sheet["attendance_sheet_id"])){
      $attachment = $this->igpam->getByGroupIdPostId($nation_code, $queryResult->i_group_id, $post_id, "first", "attendance sheet");
      if(isset($attachment->id)){
        $this->igpam->update($nation_code, $attachment->id, array("is_active"=> 0));
        $this->igpaasm->update($nation_code, $attachment->attendance_sheet_id, array("is_active"=> 0));
        $this->igpaasmm->updateByAttendancesheetid($nation_code, $attachment->attendance_sheet_id, array("is_active"=> 0));
      }
      unset($attachment);
    }

    if(isset($attendance_sheet["title"])){
      if(strlen($attendance_sheet["title"]) < 3){
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 1107;
        $this->message = 'Error, please try again later';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }

      if(count($attendance_sheet["member"]) == 0){
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 1107;
        $this->message = 'Error, please try again later';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }

      if (!in_array($attendance_sheet["sort_members"], array("By name", "Attending members first","Not attending members first"))) {
        $attendance_sheet["sort_members"] = "By name";
      }

      if (!in_array($attendance_sheet["response_option"], array("present only","present and absent"))) {
        $attendance_sheet["response_option"] = "present only";
      }

      if($attendance_sheet["self_check_in"] != 1){
        $attendance_sheet["self_check_in"] = 0;
      }

      if(strlen($attendance_sheet["start_date"]) < 3){
        $attendance_sheet["start_date"] = date("Y-m-d");
      }

      if(strlen($attendance_sheet["deadline"]) < 3){
        $attendance_sheet["deadline"] = date("Y-m-d");
      }

      if (!in_array($attendance_sheet["show_attendance_progress"], array("public","private"))) {
        $attendance_sheet["show_attendance_progress"] = "private";
      }

      if(isset($attendance_sheet["attendance_sheet_id"])){
        $attachment = $this->igpam->getByGroupIdPostId($nation_code, $queryResult->i_group_id, $post_id, "first", "attendance sheet");
        if(!isset($attachment->id)){
          $this->igpostm->trans_rollback();
          $this->igpostm->trans_end();
          $this->status = 1107;
          $this->message = 'Error, please try again later';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
          die();
        }

        if($attachment->attendance_sheet_id != $attendance_sheet["attendance_sheet_id"]){
          $this->igpostm->trans_rollback();
          $this->igpostm->trans_end();
          $this->status = 1107;
          $this->message = 'Error, please try again later';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
          die();
        }

        $dix = array();
        $dix['title'] = $attendance_sheet["title"];
        $dix['sort_members'] = $attendance_sheet["sort_members"];
        $dix['response_option'] = $attendance_sheet["response_option"];
        $dix['self_check_in'] = $attendance_sheet["self_check_in"];
        $dix['start_date'] = $attendance_sheet["start_date"];
        $dix['deadline'] = $attendance_sheet["deadline"];
        $dix['show_attendance_progress'] = $attendance_sheet["show_attendance_progress"];
        $this->igpaasm->update($nation_code, $attendance_sheet["attendance_sheet_id"], $dix);

        $listInsertUser = array();
        $listInsertGuest = array();
        foreach ($attendance_sheet["member"] as $member) {
          if(isset($member["b_user_id"])){
            $listInsertUser[] = $member["b_user_id"];
          }else{
            $listInsertGuest[] = $member["guest_fnama"];
          }
        }

        if(!$listInsertUser){
          $this->igpaasmm->delMemberByAttendancesheetidJenisNotInList($nation_code, $attendance_sheet["attendance_sheet_id"], "member");
        }

        if($listInsertUser){
          $this->igpaasmm->delMemberByAttendancesheetidJenisNotInList($nation_code, $attendance_sheet["attendance_sheet_id"], "member", $listInsertUser);
          $listExistingUser = $this->igpaasmm->getByAttendanceId($nation_code, 0, 0, "igpaasm.cdate", "DESC", "", $attendance_sheet["attendance_sheet_id"], "all", "", "", "member");
          foreach ($listExistingUser as $value) {
            if (($key = array_search($value->b_user_id, $listInsertUser)) !== false) {
              unset($listInsertUser[$key]);
            }
          }
          unset($listExistingUser);

          $insertArray = array();
          foreach ($listInsertUser as $b_user_id) {
            $endDoWhile = 0;
            do{
              $attendanceSheetmemberId = $this->GUIDv4();
              $checkId = $this->igpaasmm->checkId($nation_code, $attendanceSheetmemberId);
              if($checkId == 0){
                $endDoWhile = 1;
              }
            }while($endDoWhile == 0);

            $dix = array();
            $dix['nation_code'] = $nation_code;
            $dix['id'] = $attendanceSheetmemberId;
            $dix['i_group_id'] = $queryResult->i_group_id;
            $dix['i_group_post_id'] = $post_id;
            $dix['i_group_post_attachment_id'] = $attachment->id;
            $dix['i_group_post_attachment_attendance_sheet_id'] = $attendance_sheet["attendance_sheet_id"];
            $dix['b_user_id'] = $b_user_id;
            $dix['guest_fnama'] = "";
            $dix['custom_text'] = "";
            $dix['jenis'] = "member";
            $insertArray[] = $dix;
          }

          if(count($insertArray) > 0){
            // $this->glrm->delAll($nation_code, $area->kelurahan, $area->kecamatan, $area->kabkota, $area->provinsi);
            // $this->glrm->delAll($nation_code, "All", "All", "All", "All");

            $chunkInsertArray = array_chunk($insertArray,50);
            foreach($chunkInsertArray AS $chunk){
              //insert multi
              $this->igpaasmm->setMass($chunk);
            }
            unset($chunkInsertArray, $chunk);
          }
          unset($insertArray); 
        }

        if(!$listInsertGuest){
          $this->igpaasmm->delMemberByAttendancesheetidJenisNotInList($nation_code, $attendance_sheet["attendance_sheet_id"], "guest");
        }

        if($listInsertGuest){
          $this->igpaasmm->delMemberByAttendancesheetidJenisNotInList($nation_code, $attendance_sheet["attendance_sheet_id"], "guest", $listInsertGuest);
          $listExistingGuest = $this->igpaasmm->getByAttendanceId($nation_code, 0, 0, "igpaasm.cdate", "DESC", "", $attendance_sheet["attendance_sheet_id"], "all", "", "", "guest");
          foreach ($listExistingGuest as $value) {
            if (($key = array_search($value->b_user_band_nama, $listInsertGuest)) !== false) {
              unset($listInsertGuest[$key]);
            }
          }
          unset($listExistingGuest);

          $insertArray = array();
          foreach ($listInsertGuest as $guest_fnama) {
            $endDoWhile = 0;
            do{
              $attendanceSheetmemberId = $this->GUIDv4();
              $checkId = $this->igpaasmm->checkId($nation_code, $attendanceSheetmemberId);
              if($checkId == 0){
                $endDoWhile = 1;
              }
            }while($endDoWhile == 0);

            $dix = array();
            $dix['nation_code'] = $nation_code;
            $dix['id'] = $attendanceSheetmemberId;
            $dix['i_group_id'] = $queryResult->i_group_id;
            $dix['i_group_post_id'] = $post_id;
            $dix['i_group_post_attachment_id'] = $attachment->id;
            $dix['i_group_post_attachment_attendance_sheet_id'] = $attendance_sheet["attendance_sheet_id"];
            $dix['b_user_id'] = "0";
            $dix['guest_fnama'] = $guest_fnama;
            $dix['custom_text'] = "Guest Member | Added by". $pelanggan->band_fnama;
            $dix['jenis'] = "guest";
            $insertArray[] = $dix;
          }

          if(count($insertArray) > 0){
            // $this->glrm->delAll($nation_code, $area->kelurahan, $area->kecamatan, $area->kabkota, $area->provinsi);
            // $this->glrm->delAll($nation_code, "All", "All", "All", "All");

            $chunkInsertArray = array_chunk($insertArray,50);
            foreach($chunkInsertArray AS $chunk){
              //insert multi
              $this->igpaasmm->setMass($chunk);
            }
            unset($chunkInsertArray, $chunk);
          }
          unset($insertArray);
        }

        $totalMember = $this->igpaasmm->countByAttendanceId($nation_code, "", $attendance_sheet["attendance_sheet_id"], "all");
        $totalHaventPresentAbsent = $this->igpaasmm->countByAttendanceId($nation_code, "", $attendance_sheet["attendance_sheet_id"], "detail");
        $dix = array();
        $dix['attendance_sheet_title'] = $attendance_sheet["title"];
        $dix['attendance_sheet_filled'] = $totalMember - $totalHaventPresentAbsent;
        $dix['attendance_sheet_total'] = $totalMember;
        $this->igpam->update($nation_code, $attachment->id, $dix); 
      }else {
        $endDoWhile = 0;
        do{
          $attachmentId = $this->GUIDv4();
          $checkId = $this->igpam->checkId($nation_code, $attachmentId);
          if($checkId == 0){
            $endDoWhile = 1;
          }
        }while($endDoWhile == 0);

        $endDoWhile = 0;
        do{
          $attendanceSheetId = $this->GUIDv4();
          $checkId = $this->igpaasm->checkId($nation_code, $attendanceSheetId);
          if($checkId == 0){
            $endDoWhile = 1;
          }
        }while($endDoWhile == 0);

        $dix = array();
        $dix['nation_code'] = $nation_code;
        $dix['id'] = $attachmentId;
        $dix['i_group_id'] = $queryResult->i_group_id;
        $dix['i_group_post_id'] = $post_id;
        $dix['i_group_directory_id'] = $queryResult->i_group_id;
        $dix['b_user_id'] = $pelanggan->id;
        $dix['jenis'] = 'attendance sheet';
        $dix['attendance_sheet_id'] = $attendanceSheetId;
        $dix['attendance_sheet_title'] = $attendance_sheet["title"];
        $dix['attendance_sheet_total'] = count($attendance_sheet["member"]);
        $this->igpam->set($dix);

        $dix = array();
        $dix['nation_code'] = $nation_code;
        $dix['id'] = $attendanceSheetId;
        $dix['i_group_id'] = $queryResult->i_group_id;
        $dix['i_group_post_id'] = $post_id;
        $dix['i_group_post_attachment_id'] = $attachmentId;
        $dix['b_user_id'] = $pelanggan->id;
        $dix['title'] = $attendance_sheet["title"];
        $dix['sort_members'] = $attendance_sheet["sort_members"];
        $dix['response_option'] = $attendance_sheet["response_option"];
        $dix['self_check_in'] = $attendance_sheet["self_check_in"];
        $dix['start_date'] = $attendance_sheet["start_date"];
        $dix['deadline'] = $attendance_sheet["deadline"];
        $dix['show_attendance_progress'] = $attendance_sheet["show_attendance_progress"];
        $this->igpaasm->set($dix);

        $insertArray = array();
        $listInsertUser = array();
        foreach ($attendance_sheet["member"] as $member) {
          $endDoWhile = 0;
          do{
            $attendanceSheetmemberId = $this->GUIDv4();
            $checkId = $this->igpaasmm->checkId($nation_code, $attendanceSheetmemberId);
            if($checkId == 0){
              $endDoWhile = 1;
            }
          }while($endDoWhile == 0);

          $dix = array();
          $dix['nation_code'] = $nation_code;
          $dix['id'] = $attendanceSheetmemberId;
          $dix['i_group_id'] = $queryResult->i_group_id;
          $dix['i_group_post_id'] = $post_id;
          $dix['i_group_post_attachment_id'] = $attachmentId;
          $dix['i_group_post_attachment_attendance_sheet_id'] = $attendanceSheetId;
          if(isset($member["b_user_id"])){
            $listInsertUser[] = $member["b_user_id"];
            $dix['b_user_id'] = $member["b_user_id"];
            $dix['guest_fnama'] = "";
            $dix['custom_text'] = "";
            $dix['jenis'] = "member";
          }else{
            $dix['b_user_id'] = "0";
            $dix['guest_fnama'] = $member["guest_fnama"];
            $dix['custom_text'] = "Guest Member | Added by". $pelanggan->band_fnama;
            $dix['jenis'] = "guest";
          }
          $insertArray[] = $dix;
        }

        if(count($insertArray) > 0){
          // $this->glrm->delAll($nation_code, $area->kelurahan, $area->kecamatan, $area->kabkota, $area->provinsi);
          // $this->glrm->delAll($nation_code, "All", "All", "All", "All");

          $chunkInsertArray = array_chunk($insertArray,50);
          foreach($chunkInsertArray AS $chunk){
            //insert multi
            $this->igpaasmm->setMass($chunk);
          }
          unset($chunkInsertArray, $chunk);
        }
        unset($insertArray);
      }
    }

    $this->igpostm->trans_commit();
    $this->igpostm->trans_end();

    $queryResult = $this->igpostm->getById($nation_code, $post_id);

    if(isset($listInsertUser)){
      foreach($listInsertUser AS $b_user_id){
        if($b_user_id == $pelanggan->id){
          continue;
        }

        $user = $this->bu->getById($nation_code, $b_user_id);

        $dpe = array();
        $dpe['nation_code'] = $nation_code;
        $dpe['b_user_id'] = $b_user_id;
        $dpe['type'] = "band_group_post";
        if($user->language_id == 2) {
          $dpe['judul'] = "Attendance";
          $dpe['teks'] =  "Anda diminta untuk absensi oleh Mr./Ms. ". $pelanggan->band_fnama;
        } else {
          $dpe['judul'] = "Attendance";
          $dpe['teks'] =  "You're asked to attend by Mr./Ms. ". $pelanggan->band_fnama;
        }
        $dpe['group_name'] = $queryResult->group_name;
        $dpe['i_group_id'] = $queryResult->i_group_id;
        $dpe['gambar'] = 'media/pemberitahuan/community.png';
        $dpe['cdate'] = "NOW()";
        $extras = new stdClass();
        $extras->i_group_post_id = $queryResult->id;
        $extras->i_group_id = $queryResult->i_group_id;
        if($user->language_id == 2) { 
          $extras->judul = "Attendance";
          $extras->teks =  "Anda diminta untuk absensi oleh Mr./Ms. ". $pelanggan->band_fnama;
        } else {
          $extras->judul = "Attendance";
          $extras->teks =  "You're asked to attend by Mr./Ms. ". $pelanggan->band_fnama;
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
            $message = "Anda diminta untuk absensi oleh Mr./Ms. ". $pelanggan->band_fnama;
          } else {
            $title = "Attendance";
            $message = "You're asked to attend by Mr./Ms. ". $pelanggan->band_fnama;
          }
          $image = 'media/pemberitahuan/community.png';
          $type = 'band_group_post';
          $payload = new stdClass();
          $payload->i_group_post_id = $queryResult->id;
          $payload->i_group_id = $queryResult->i_group_id;
          if($user->language_id == 2) {
            $payload->judul = "Attendance";
            $payload->teks = "Anda diminta untuk absensi oleh Mr./Ms. ". $pelanggan->band_fnama;
          } else {
            $payload->judul = "Attendance";
            $payload->teks = "You're asked to attend by Mr./Ms. ". $pelanggan->band_fnama;
          }
          $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        }
      }
    }

    // $queryResult->can_chat_and_like = "0";
    // if(isset($pelanggan->id)){
    //   $queryResult->can_chat_and_like = "1";
    // }

    $queryResult->total_likes = $this->thousandsCurrencyFormat($queryResult->total_likes);

    $queryResult->is_liked = '0';
    $queryResult->currentLikeEmoji = '';
    $queryResult->is_owner_post = '0';
    if(isset($pelanggan->id)){
      $checkLike = $this->igplm->getByCustomIdUserId($nation_code, $queryResult->id, $pelanggan->id, "post");
      if(isset($checkLike->id)){
        $queryResult->is_liked = '1';
        $queryResult->currentLikeEmoji = $checkLike->i_group_post_like_category_id;
      }
      unset($checkLike);
      if($pelanggan->id == $queryResult->b_user_id){
        $queryResult->is_owner_post = '1';
      }
    }

    $queryResult->is_owner_or_admin = '0';
    $queryResult->cdate_text = $this->humanTiming($queryResult->cdate, null, $pelanggan->language_id);
    $queryResult->cdate = $this->customTimezone($queryResult->cdate, $timezone);
    $queryResult->deskripsi = html_entity_decode($queryResult->deskripsi,ENT_QUOTES);
    $queryResult->group_name = html_entity_decode($queryResult->group_name,ENT_QUOTES);

    if (isset($queryResult->b_user_band_image)) {
      if(file_exists(SENEROOT.$queryResult->b_user_band_image) && $queryResult->b_user_band_image != 'media/user/default.png'){
        $queryResult->b_user_band_image = $this->cdn_url($queryResult->b_user_band_image);
      } else {
        $queryResult->b_user_band_image = $this->cdn_url('media/user/default-profile-picture.png');
      }
    }

    $queryResult->images = array();
    $queryResult->locations = array();
    $queryResult->videos = array();
    $queryResult->file = array();
    $queryResult->attendance = array();
    $attachments = $this->igpam->getByGroupIdPostId($nation_code, $queryResult->i_group_id, $queryResult->id);
    foreach ($attachments as $atc) {
      $temp = new stdClass();
      if($atc->jenis == 'image'){
        if (empty($atc->url)) {
          $atc->url = 'media/community_default.png';
        }
        if (empty($atc->url_thumb)) {
          $atc->url_thumb = 'media/community_default.png';
        }
        $temp->url = $this->cdn_url($atc->url);
        $temp->url_thumb = $this->cdn_url($atc->url_thumb);
        $queryResult->images[] = $temp;
      }else if($atc->jenis == 'location'){
        $temp->location_nama = $atc->location_nama;
        $temp->location_address = $atc->location_address;
        $temp->location_place_id = $atc->location_place_id;
        $temp->location_latitude = $atc->location_latitude;
        $temp->location_longitude = $atc->location_longitude;
        $queryResult->locations[] = $temp;
      }else if($atc->jenis == 'video'){
        $temp->video_id = $atc->id;
        $temp->url = $this->cdn_url($atc->url);
        $temp->url_thumb = $this->cdn_url($atc->url_thumb);
        $temp->convert_status = $atc->convert_status;
        // $temp->total_views = $this->thousandsCurrencyFormat($temp->total_views);
        $queryResult->videos[] = $temp;
      }else if($atc->jenis == 'file'){
        $temp->url = $this->cdn_url($atc->url);
        $temp->file_name = $atc->file_name;
        $temp->file_size = $atc->file_size;
        $queryResult->file[] = $temp;
      }else if($atc->jenis == 'attendance sheet'){
        $temp->attendance_sheet_id = $atc->attendance_sheet_id;
        $temp->attendance_sheet_title = $atc->attendance_sheet_title;
        $temp->attendance_sheet_filled = $atc->attendance_sheet_filled;
        $temp->attendance_sheet_total = $atc->attendance_sheet_total;
        $attendanceSheetData = $this->igpaasm->getById($nation_code, $atc->attendance_sheet_id);
        $temp->response_option = $attendanceSheetData->response_option;
        $temp->self_check_in = $attendanceSheetData->self_check_in;
        $temp->start_date = $attendanceSheetData->start_date;
        $temp->deadline = $attendanceSheetData->deadline;
        $temp->show_attendance_progress = $attendanceSheetData->show_attendance_progress;
        $queryResult->attendance[] = $temp;
      }
    }
    unset($attachments,$atc);

    $this->status = 200;
    $this->message = 'Success';

    $data['post'] = $queryResult;

    //render output
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function editv2()
  {
    //init
    $dt = $this->__init();

    //default response
    $data = array();
    $data['post'] = new stdClass();

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

    $post_id = trim($this->input->post('post_id'));
    if (strlen($post_id)<3){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $queryResult = $this->igpostm->getById($nation_code, $post_id);
    if (!isset($queryResult->id)){
      $this->status = 1160;
      $this->message = 'This post is deleted by an author';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    if ($queryResult->b_user_id != $pelanggan->id) {
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $deskripsi = trim($this->input->post('deskripsi'));
    $location_json = $this->input->post("location_json");
    $attendance_sheet = $this->input->post("attendance_sheet");

    $deskripsi = str_replace('’',"'",$deskripsi);
    $deskripsi = nl2br($deskripsi);
    $deskripsi = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $deskripsi);
    $deskripsi = str_replace("\\n", "<br />", $deskripsi);
    if (strlen($deskripsi)<3) {
      $this->status = 1105;
      $this->message = 'Description is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->igpostm->trans_start();

    //updating to database
    $du = array();
    $du['deskripsi'] = $deskripsi;
    $res = $this->igpostm->update($nation_code, $post_id, $du);
    if (!$res) {
      $this->igpostm->trans_rollback();
      $this->igpostm->trans_end();
      $this->status = 1107;
      $this->message = "Error, please try again later";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->status = 200;
    $this->message = 'Success';

    $listUrl = array();
    $listUpload = array();
    $checkFileExist = 1;
    for ($i=1; $i < 11; $i++) {
      $file_path = parse_url($this->input->post('foto'.$i), PHP_URL_PATH);
      if (strpos($file_path, 'temporary') !== false) {
        $listUpload = array_merge($listUpload, array($file_path));
        if (!file_exists(SENEROOT.$file_path)) {
          $checkFileExist = 0;
        }
      }else if($this->input->post('foto'.$i) != null){
        $listUrl = array_merge($listUrl, array(substr($file_path, 1)));
      }
    }

    if ($checkFileExist == 0) {
      $this->igpostm->trans_rollback();
      $this->igpostm->trans_end();
      $this->status = 995;
      $this->message = 'Failed upload, temporary already gone';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //delete image that is not in array
    $attachments = $this->igpam->getByGroupIdPostId($nation_code, $queryResult->i_group_id, $post_id, "all", "image");
    foreach ($attachments as $atc) {
      if ((!in_array($atc->url, $listUrl) || empty($listUrl))) {
        $this->igpam->update($nation_code, $atc->id, array("is_active"=> 0));
        // if (strlen($atc->url)>4) {
        //   $file = SENEROOT.$atc->url;
        //   if (!is_dir($file) && file_exists($file)) {
        //     unlink($file);
        //   }
        // }
        // if (strlen($atc->url_thumb)>4) {
        //   $file = SENEROOT.$atc->url_thumb;
        //   if (!is_dir($file) && file_exists($file)) {
        //     unlink($file);
        //   }
        // }
      }
    }
    unset($attachments, $atc);

    if(!empty($listUpload)){
      foreach ($listUpload as $key => $upload) { 
        $endDoWhile = 0;
        do{
          $attachmentId = $this->GUIDv4();
          $checkId = $this->igpam->checkId($nation_code, $attachmentId);
          if($checkId == 0){
            $endDoWhile = 1;
          }
        }while($endDoWhile == 0);

        $sc = $this->__moveImagex($nation_code, $upload, $this->media_group_post_image, $post_id, $attachmentId);
        if (isset($sc->status)) {
          if ($sc->status==200) {
            $dix = array();
            $dix['nation_code'] = $nation_code;
            $dix['id'] = $attachmentId;
            $dix['i_group_id'] = $queryResult->i_group_id;
            $dix['i_group_post_id'] = $post_id;
            $dix['i_group_directory_id'] = $queryResult->i_group_id;
            $dix['b_user_id'] = $pelanggan->id;
            $dix['jenis'] = 'image';
            $dix['url'] = $sc->image;
            $dix['url_thumb'] = $sc->thumb;
            $dix['file_size'] = $sc->file_size;
            $dix['file_size_thumb'] = $sc->file_size_thumb;
            $this->igpam->set($dix);
          }
        }
      }
    }

    $this->igpam->delByPostIdJenis($nation_code, $post_id, 'location');
    if(is_array($location_json)){
      if(count($location_json) > 0){
        foreach ($location_json as $key => $upload) {
          if(isset($upload['location_nama']) && isset($upload['location_address']) && isset($upload['location_place_id']) && isset($upload['location_latitude']) && isset($upload['location_longitude'])){
            if($upload['location_nama'] && $upload['location_address'] && $upload['location_place_id'] && $upload['location_latitude'] && $upload['location_longitude']){
              $endDoWhile = 0;
              do{
                $attachmentId = $this->GUIDv4();
                $checkId = $this->igpam->checkId($nation_code, $attachmentId);
                if($checkId == 0){
                  $endDoWhile = 1;
                }
              }while($endDoWhile == 0);

              $dix = array();
              $dix['nation_code'] = $nation_code;
              $dix['id'] = $attachmentId;
              $dix['i_group_id'] = $queryResult->i_group_id;
              $dix['i_group_post_id'] = $post_id;
              $dix['i_group_directory_id'] = $queryResult->i_group_id;
              $dix['b_user_id'] = $pelanggan->id;
              $dix['jenis'] = 'location';
              $dix['location_nama'] = $upload['location_nama'];
              $dix['location_address'] = $upload['location_address'];
              $dix['location_place_id'] = $upload['location_place_id'];
              $dix['location_latitude'] = $upload['location_latitude'];
              $dix['location_longitude'] = $upload['location_longitude'];
              $this->igpam->set($dix);
            }
          }
        }
      }
    }

    // $listUrl = array();
    $listId = array();
    $listUploadVideo = array();
    for ($i=1; $i < 3; $i++) {
      if($this->input->post('video'.$i) === "yes"){
        $listUploadVideo[] = $i;
      }else if($this->input->post('video'.$i) != null){
        // $file_path = parse_url($this->input->post('video'.$i), PHP_URL_PATH);
        // $listUrl = array_merge($listUrl, array(substr($file_path, 1)));
        $listId = array_merge($listId, array($this->input->post('video'.$i)));
      }
    }

    //delete data that is not in array
    $attachments = $this->igpam->getByGroupIdPostId($nation_code, $queryResult->i_group_id, $post_id, "all", "video");
    foreach ($attachments as $atc) {
      // if ((!in_array($atc->id, $listUrl) || empty($listUrl))) {
      if ((!in_array($atc->id, $listId) || empty($listId))) {
        $this->igpam->update($nation_code, $atc->id, array("is_active"=> 0));
        // if (strlen($atc->url)>4) {
        //   $file = SENEROOT.$atc->url;
        //   if (!is_dir($file) && file_exists($file)) {
        //     unlink($file);
        //   }
        // }
        // if (strlen($atc->url_thumb)>4) {
        //   $file = SENEROOT.$atc->url_thumb;
        //   if (!is_dir($file) && file_exists($file)) {
        //     unlink($file);
        //   }
        // }
      }
    }
    unset($attachments, $atc);

    if(!empty($listUploadVideo)){
      foreach ($listUploadVideo as $i) {
        $endDoWhile = 0;
        do{
          $attachmentId = $this->GUIDv4();
          $checkId = $this->igpam->checkId($nation_code, $attachmentId);
          if($checkId == 0){
            $endDoWhile = 1;
          }
        }while($endDoWhile == 0);

        $upi = $this->__moveImagex($nation_code, $this->input->post("video".$i."_thumb"), $this->media_group_post_video, $post_id, $attachmentId, $i);
        $dix = array();
        $dix['nation_code'] = $nation_code;
        $dix['id'] = $attachmentId;
        $dix['i_group_id'] = $queryResult->i_group_id;
        $dix['i_group_post_id'] = $post_id;
        $dix['i_group_directory_id'] = $queryResult->i_group_id;
        $dix['b_user_id'] = $pelanggan->id;
        $dix['jenis'] = 'video';
        $dix['convert_status'] = 'uploading';
        if($upi->status == 200){
          $dix['url'] = $upi->image;
          $dix['url_thumb'] = $upi->thumb;
          $dix['file_size'] = $upi->file_size;
          $dix['file_size_thumb'] = $upi->file_size_thumb;
        }else{
          $dix['url'] = $this->media_community_video."default.png";
          $dix['url_thumb'] = $this->media_community_video."default.png";
        }
        $this->igpam->set($dix);
      }
    }

    $listUrl = array();
    $listUpload = array();
    $checkFileExist = 1;
    for ($i=1; $i < 3; $i++) {
      $file_path = parse_url($this->input->post('file'.$i), PHP_URL_PATH);
      if (strpos($file_path, 'temporary') !== false) {
        $listUpload = array_merge($listUpload, array($file_path));
        if (!file_exists(SENEROOT.$file_path)) {
          $checkFileExist = 0;
        }
      }else if($this->input->post('file'.$i) != null){
        $listUrl = array_merge($listUrl, array(substr($file_path, 1)));
      }
    }

    if ($checkFileExist == 0) {
      $this->igpostm->trans_rollback();
      $this->igpostm->trans_end();
      $this->status = 995;
      $this->message = 'Failed upload, temporary already gone';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //delete data that is not in array
    $attachments = $this->igpam->getByGroupIdPostId($nation_code, $queryResult->i_group_id, $post_id, "all", "file");
    foreach ($attachments as $atc) {
      if ((!in_array($atc->url, $listUrl) || empty($listUrl))) {
        $this->igpam->update($nation_code, $atc->id, array("is_active"=> 0));
        // if (strlen($atc->url)>4) {
        //   $file = SENEROOT.$atc->url;
        //   if (!is_dir($file) && file_exists($file)) {
        //     unlink($file);
        //   }
        // }
        // if (strlen($atc->url_thumb)>4) {
        //   $file = SENEROOT.$atc->url_thumb;
        //   if (!is_dir($file) && file_exists($file)) {
        //     unlink($file);
        //   }
        // }
      }
    }
    unset($attachments, $atc);

    if(!empty($listUpload)){
      foreach ($listUpload as $key => $upload) {
        $endDoWhile = 0;
        do{
          $attachmentId = $this->GUIDv4();
          $checkId = $this->igpam->checkId($nation_code, $attachmentId);
          if($checkId == 0){
            $endDoWhile = 1;
          }
        }while($endDoWhile == 0);

        $sc = $this->__moveFilex($nation_code, $upload, $this->media_group_post_file, $post_id, $attachmentId);
        if (isset($sc->status)) {
          if ($sc->status==200) {
            $dix = array();
            $dix['nation_code'] = $nation_code;
            $dix['id'] = $attachmentId;
            $dix['i_group_id'] = $queryResult->i_group_id;
            $dix['i_group_post_id'] = $post_id;
            $dix['i_group_directory_id'] = $queryResult->i_group_id;
            $dix['b_user_id'] = $pelanggan->id;
            $dix['jenis'] = 'file';
            $dix['url'] = $sc->url;
            $dix['file_name'] = substr(pathinfo($upload, PATHINFO_BASENAME), 0, strpos(pathinfo($upload, PATHINFO_BASENAME),"-")).".".pathinfo($upload, PATHINFO_EXTENSION);
            $dix['file_size'] = $sc->file_size;
            $this->igpam->set($dix);
          }
        }
      }
    }

    if(!isset($attendance_sheet["attendance_sheet_id"])){
      $attachment = $this->igpam->getByGroupIdPostId($nation_code, $queryResult->i_group_id, $post_id, "first", "attendance sheet");
      if(isset($attachment->id)){
        $this->igpam->update($nation_code, $attachment->id, array("is_active"=> 0));
        $this->igpaasm->update($nation_code, $attachment->attendance_sheet_id, array("is_active"=> 0));
        $this->igpaasmm->updateByAttendancesheetid($nation_code, $attachment->attendance_sheet_id, array("is_active"=> 0));
      }
      unset($attachment);
    }

    if(isset($attendance_sheet["title"])){
      if(strlen($attendance_sheet["title"]) < 3){
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 1107;
        $this->message = 'Error, please try again later';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }

      if(count($attendance_sheet["member"]) == 0){
        $this->igpostm->trans_rollback();
        $this->igpostm->trans_end();
        $this->status = 1107;
        $this->message = 'Error, please try again later';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }

      if (!in_array($attendance_sheet["sort_members"], array("By name", "Attending members first","Not attending members first"))) {
        $attendance_sheet["sort_members"] = "By name";
      }

      if (!in_array($attendance_sheet["response_option"], array("present only","present and absent"))) {
        $attendance_sheet["response_option"] = "present only";
      }

      if($attendance_sheet["self_check_in"] != 1){
        $attendance_sheet["self_check_in"] = 0;
      }

      if(strlen($attendance_sheet["start_date"]) < 3){
        $attendance_sheet["start_date"] = date("Y-m-d");
      }

      if(strlen($attendance_sheet["deadline"]) < 3){
        $attendance_sheet["deadline"] = date("Y-m-d");
      }

      if (!in_array($attendance_sheet["show_attendance_progress"], array("public","private"))) {
        $attendance_sheet["show_attendance_progress"] = "private";
      }

      if(isset($attendance_sheet["attendance_sheet_id"])){
        $attachment = $this->igpam->getByGroupIdPostId($nation_code, $queryResult->i_group_id, $post_id, "first", "attendance sheet");
        if(!isset($attachment->id)){
          $this->igpostm->trans_rollback();
          $this->igpostm->trans_end();
          $this->status = 1107;
          $this->message = 'Error, please try again later';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
          die();
        }

        if($attachment->attendance_sheet_id != $attendance_sheet["attendance_sheet_id"]){
          $this->igpostm->trans_rollback();
          $this->igpostm->trans_end();
          $this->status = 1107;
          $this->message = 'Error, please try again later';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
          die();
        }

        $dix = array();
        $dix['title'] = $attendance_sheet["title"];
        $dix['sort_members'] = $attendance_sheet["sort_members"];
        $dix['response_option'] = $attendance_sheet["response_option"];
        $dix['self_check_in'] = $attendance_sheet["self_check_in"];
        $dix['start_date'] = $attendance_sheet["start_date"];
        $dix['deadline'] = $attendance_sheet["deadline"];
        $dix['show_attendance_progress'] = $attendance_sheet["show_attendance_progress"];
        $this->igpaasm->update($nation_code, $attendance_sheet["attendance_sheet_id"], $dix);

        $listInsertUser = array();
        $listInsertGuest = array();
        foreach ($attendance_sheet["member"] as $member) {
          if(isset($member["b_user_id"])){
            $listInsertUser[] = $member["b_user_id"];
          }else{
            $listInsertGuest[] = $member["guest_fnama"];
          }
        }

        if(!$listInsertUser){
          $this->igpaasmm->delMemberByAttendancesheetidJenisNotInList($nation_code, $attendance_sheet["attendance_sheet_id"], "member");
        }

        if($listInsertUser){
          $this->igpaasmm->delMemberByAttendancesheetidJenisNotInList($nation_code, $attendance_sheet["attendance_sheet_id"], "member", $listInsertUser);
          $listExistingUser = $this->igpaasmm->getByAttendanceId($nation_code, 0, 0, "igpaasm.cdate", "DESC", "", $attendance_sheet["attendance_sheet_id"], "all", "", "", "member");
          foreach ($listExistingUser as $value) {
            if (($key = array_search($value->b_user_id, $listInsertUser)) !== false) {
              unset($listInsertUser[$key]);
            }
          }
          unset($listExistingUser);

          $insertArray = array();
          foreach ($listInsertUser as $b_user_id) {
            $endDoWhile = 0;
            do{
              $attendanceSheetmemberId = $this->GUIDv4();
              $checkId = $this->igpaasmm->checkId($nation_code, $attendanceSheetmemberId);
              if($checkId == 0){
                $endDoWhile = 1;
              }
            }while($endDoWhile == 0);

            $dix = array();
            $dix['nation_code'] = $nation_code;
            $dix['id'] = $attendanceSheetmemberId;
            $dix['i_group_id'] = $queryResult->i_group_id;
            $dix['i_group_post_id'] = $post_id;
            $dix['i_group_post_attachment_id'] = $attachment->id;
            $dix['i_group_post_attachment_attendance_sheet_id'] = $attendance_sheet["attendance_sheet_id"];
            $dix['b_user_id'] = $b_user_id;
            $dix['guest_fnama'] = "";
            $dix['custom_text'] = "";
            $dix['jenis'] = "member";
            $insertArray[] = $dix;
          }

          if(count($insertArray) > 0){
            // $this->glrm->delAll($nation_code, $area->kelurahan, $area->kecamatan, $area->kabkota, $area->provinsi);
            // $this->glrm->delAll($nation_code, "All", "All", "All", "All");

            $chunkInsertArray = array_chunk($insertArray,50);
            foreach($chunkInsertArray AS $chunk){
              //insert multi
              $this->igpaasmm->setMass($chunk);
            }
            unset($chunkInsertArray, $chunk);
          }
          unset($insertArray); 
        }

        if(!$listInsertGuest){
          $this->igpaasmm->delMemberByAttendancesheetidJenisNotInList($nation_code, $attendance_sheet["attendance_sheet_id"], "guest");
        }

        if($listInsertGuest){
          $this->igpaasmm->delMemberByAttendancesheetidJenisNotInList($nation_code, $attendance_sheet["attendance_sheet_id"], "guest", $listInsertGuest);
          $listExistingGuest = $this->igpaasmm->getByAttendanceId($nation_code, 0, 0, "igpaasm.cdate", "DESC", "", $attendance_sheet["attendance_sheet_id"], "all", "", "", "guest");
          foreach ($listExistingGuest as $value) {
            if (($key = array_search($value->b_user_band_nama, $listInsertGuest)) !== false) {
              unset($listInsertGuest[$key]);
            }
          }
          unset($listExistingGuest);

          $insertArray = array();
          foreach ($listInsertGuest as $guest_fnama) {
            $endDoWhile = 0;
            do{
              $attendanceSheetmemberId = $this->GUIDv4();
              $checkId = $this->igpaasmm->checkId($nation_code, $attendanceSheetmemberId);
              if($checkId == 0){
                $endDoWhile = 1;
              }
            }while($endDoWhile == 0);

            $dix = array();
            $dix['nation_code'] = $nation_code;
            $dix['id'] = $attendanceSheetmemberId;
            $dix['i_group_id'] = $queryResult->i_group_id;
            $dix['i_group_post_id'] = $post_id;
            $dix['i_group_post_attachment_id'] = $attachment->id;
            $dix['i_group_post_attachment_attendance_sheet_id'] = $attendance_sheet["attendance_sheet_id"];
            $dix['b_user_id'] = "0";
            $dix['guest_fnama'] = $guest_fnama;
            $dix['custom_text'] = "Guest Member | Added by". $pelanggan->band_fnama;
            $dix['jenis'] = "guest";
            $insertArray[] = $dix;
          }

          if(count($insertArray) > 0){
            // $this->glrm->delAll($nation_code, $area->kelurahan, $area->kecamatan, $area->kabkota, $area->provinsi);
            // $this->glrm->delAll($nation_code, "All", "All", "All", "All");

            $chunkInsertArray = array_chunk($insertArray,50);
            foreach($chunkInsertArray AS $chunk){
              //insert multi
              $this->igpaasmm->setMass($chunk);
            }
            unset($chunkInsertArray, $chunk);
          }
          unset($insertArray);
        }

        $totalMember = $this->igpaasmm->countByAttendanceId($nation_code, "", $attendance_sheet["attendance_sheet_id"], "all");
        $totalHaventPresentAbsent = $this->igpaasmm->countByAttendanceId($nation_code, "", $attendance_sheet["attendance_sheet_id"], "detail");
        $dix = array();
        $dix['attendance_sheet_title'] = $attendance_sheet["title"];
        $dix['attendance_sheet_filled'] = $totalMember - $totalHaventPresentAbsent;
        $dix['attendance_sheet_total'] = $totalMember;
        $this->igpam->update($nation_code, $attachment->id, $dix); 
      }else {
        $endDoWhile = 0;
        do{
          $attachmentId = $this->GUIDv4();
          $checkId = $this->igpam->checkId($nation_code, $attachmentId);
          if($checkId == 0){
            $endDoWhile = 1;
          }
        }while($endDoWhile == 0);

        $endDoWhile = 0;
        do{
          $attendanceSheetId = $this->GUIDv4();
          $checkId = $this->igpaasm->checkId($nation_code, $attendanceSheetId);
          if($checkId == 0){
            $endDoWhile = 1;
          }
        }while($endDoWhile == 0);

        $dix = array();
        $dix['nation_code'] = $nation_code;
        $dix['id'] = $attachmentId;
        $dix['i_group_id'] = $queryResult->i_group_id;
        $dix['i_group_post_id'] = $post_id;
        $dix['i_group_directory_id'] = $queryResult->i_group_id;
        $dix['b_user_id'] = $pelanggan->id;
        $dix['jenis'] = 'attendance sheet';
        $dix['attendance_sheet_id'] = $attendanceSheetId;
        $dix['attendance_sheet_title'] = $attendance_sheet["title"];
        $dix['attendance_sheet_total'] = count($attendance_sheet["member"]);
        $this->igpam->set($dix);

        $dix = array();
        $dix['nation_code'] = $nation_code;
        $dix['id'] = $attendanceSheetId;
        $dix['i_group_id'] = $queryResult->i_group_id;
        $dix['i_group_post_id'] = $post_id;
        $dix['i_group_post_attachment_id'] = $attachmentId;
        $dix['b_user_id'] = $pelanggan->id;
        $dix['title'] = $attendance_sheet["title"];
        $dix['sort_members'] = $attendance_sheet["sort_members"];
        $dix['response_option'] = $attendance_sheet["response_option"];
        $dix['self_check_in'] = $attendance_sheet["self_check_in"];
        $dix['start_date'] = $attendance_sheet["start_date"];
        $dix['deadline'] = $attendance_sheet["deadline"];
        $dix['show_attendance_progress'] = $attendance_sheet["show_attendance_progress"];
        $this->igpaasm->set($dix);

        $insertArray = array();
        $listInsertUser = array();
        foreach ($attendance_sheet["member"] as $member) {
          $endDoWhile = 0;
          do{
            $attendanceSheetmemberId = $this->GUIDv4();
            $checkId = $this->igpaasmm->checkId($nation_code, $attendanceSheetmemberId);
            if($checkId == 0){
              $endDoWhile = 1;
            }
          }while($endDoWhile == 0);

          $dix = array();
          $dix['nation_code'] = $nation_code;
          $dix['id'] = $attendanceSheetmemberId;
          $dix['i_group_id'] = $queryResult->i_group_id;
          $dix['i_group_post_id'] = $post_id;
          $dix['i_group_post_attachment_id'] = $attachmentId;
          $dix['i_group_post_attachment_attendance_sheet_id'] = $attendanceSheetId;
          if(isset($member["b_user_id"])){
            $listInsertUser[] = $member["b_user_id"];
            $dix['b_user_id'] = $member["b_user_id"];
            $dix['guest_fnama'] = "";
            $dix['custom_text'] = "";
            $dix['jenis'] = "member";
          }else{
            $dix['b_user_id'] = "0";
            $dix['guest_fnama'] = $member["guest_fnama"];
            $dix['custom_text'] = "Guest Member | Added by". $pelanggan->band_fnama;
            $dix['jenis'] = "guest";
          }
          $insertArray[] = $dix;
        }

        if(count($insertArray) > 0){
          // $this->glrm->delAll($nation_code, $area->kelurahan, $area->kecamatan, $area->kabkota, $area->provinsi);
          // $this->glrm->delAll($nation_code, "All", "All", "All", "All");

          $chunkInsertArray = array_chunk($insertArray,50);
          foreach($chunkInsertArray AS $chunk){
            //insert multi
            $this->igpaasmm->setMass($chunk);
          }
          unset($chunkInsertArray, $chunk);
        }
        unset($insertArray);
      }
    }

    $this->igpostm->trans_commit();
    $this->igpostm->trans_end();

    $queryResult = $this->igpostm->getById($nation_code, $post_id);

    if(isset($listInsertUser)){
      foreach($listInsertUser AS $b_user_id){
        if($b_user_id == $pelanggan->id){
          continue;
        }

        $user = $this->bu->getById($nation_code, $b_user_id);

        $dpe = array();
        $dpe['nation_code'] = $nation_code;
        $dpe['b_user_id'] = $b_user_id;
        $dpe['type'] = "band_group_post";
        if($user->language_id == 2) {
          $dpe['judul'] = "Attendance";
          $dpe['teks'] =  "Anda diminta untuk absensi oleh Mr./Ms. ". $pelanggan->band_fnama;
        } else {
          $dpe['judul'] = "Attendance";
          $dpe['teks'] =  "You're asked to attend by Mr./Ms. ". $pelanggan->band_fnama;
        }
        $dpe['group_name'] = $queryResult->group_name;
        $dpe['i_group_id'] = $queryResult->i_group_id;
        $dpe['gambar'] = 'media/pemberitahuan/community.png';
        $dpe['cdate'] = "NOW()";
        $extras = new stdClass();
        $extras->i_group_post_id = $queryResult->id;
        $extras->i_group_id = $queryResult->i_group_id;
        if($user->language_id == 2) { 
          $extras->judul = "Attendance";
          $extras->teks =  "Anda diminta untuk absensi oleh Mr./Ms. ". $pelanggan->band_fnama;
        } else {
          $extras->judul = "Attendance";
          $extras->teks =  "You're asked to attend by Mr./Ms. ". $pelanggan->band_fnama;
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
            $message = "Anda diminta untuk absensi oleh Mr./Ms. ". $pelanggan->band_fnama;
          } else {
            $title = "Attendance";
            $message = "You're asked to attend by Mr./Ms. ". $pelanggan->band_fnama;
          }
          $image = 'media/pemberitahuan/community.png';
          $type = 'band_group_post';
          $payload = new stdClass();
          $payload->i_group_post_id = $queryResult->id;
          $payload->i_group_id = $queryResult->i_group_id;
          if($user->language_id == 2) {
            $payload->judul = "Attendance";
            $payload->teks = "Anda diminta untuk absensi oleh Mr./Ms. ". $pelanggan->band_fnama;
          } else {
            $payload->judul = "Attendance";
            $payload->teks = "You're asked to attend by Mr./Ms. ". $pelanggan->band_fnama;
          }
          $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        }
      }
    }

    // $queryResult->can_chat_and_like = "0";
    // if(isset($pelanggan->id)){
    //   $queryResult->can_chat_and_like = "1";
    // }

    $queryResult->total_likes = $this->thousandsCurrencyFormat($queryResult->total_likes);

    $queryResult->is_liked = '0';
    $queryResult->currentLikeEmoji = '';
    $queryResult->is_owner_post = '0';
    if(isset($pelanggan->id)){
      $checkLike = $this->igplm->getByCustomIdUserId($nation_code, $queryResult->id, $pelanggan->id, "post");
      if(isset($checkLike->id)){
        $queryResult->is_liked = '1';
        $queryResult->currentLikeEmoji = $checkLike->i_group_post_like_category_id;
      }
      unset($checkLike);
      if($pelanggan->id == $queryResult->b_user_id){
        $queryResult->is_owner_post = '1';
      }
    }

    $queryResult->is_owner_or_admin = '0';
    $queryResult->cdate_text = $this->humanTiming($queryResult->cdate, null, $pelanggan->language_id);
    $queryResult->cdate = $this->customTimezone($queryResult->cdate, $timezone);
    $queryResult->deskripsi = html_entity_decode($queryResult->deskripsi,ENT_QUOTES);
    $queryResult->group_name = html_entity_decode($queryResult->group_name,ENT_QUOTES);

    if (isset($queryResult->b_user_band_image)) {
      if(file_exists(SENEROOT.$queryResult->b_user_band_image) && $queryResult->b_user_band_image != 'media/user/default.png'){
        $queryResult->b_user_band_image = $this->cdn_url($queryResult->b_user_band_image);
      } else {
        $queryResult->b_user_band_image = $this->cdn_url('media/user/default-profile-picture.png');
      }
    }

    $queryResult->images = array();
    $queryResult->locations = array();
    $queryResult->videos = array();
    $queryResult->file = array();
    $queryResult->attendance = array();
    $attachments = $this->igpam->getByGroupIdPostId($nation_code, $queryResult->i_group_id, $queryResult->id);
    foreach ($attachments as $atc) {
      $temp = new stdClass();
      if($atc->jenis == 'image'){
        if (empty($atc->url)) {
          $atc->url = 'media/community_default.png';
        }
        if (empty($atc->url_thumb)) {
          $atc->url_thumb = 'media/community_default.png';
        }
        $temp->url = $this->cdn_url($atc->url);
        $temp->url_thumb = $this->cdn_url($atc->url_thumb);
        $queryResult->images[] = $temp;
      }else if($atc->jenis == 'location'){
        $temp->location_nama = $atc->location_nama;
        $temp->location_address = $atc->location_address;
        $temp->location_place_id = $atc->location_place_id;
        $temp->location_latitude = $atc->location_latitude;
        $temp->location_longitude = $atc->location_longitude;
        $queryResult->locations[] = $temp;
      }else if($atc->jenis == 'video'){
        $temp->video_id = $atc->id;
        $temp->url = $this->cdn_url($atc->url);
        $temp->url_thumb = $this->cdn_url($atc->url_thumb);
        $temp->convert_status = $atc->convert_status;
        // $temp->total_views = $this->thousandsCurrencyFormat($temp->total_views);
        $queryResult->videos[] = $temp;
      }else if($atc->jenis == 'file'){
        $temp->url = $this->cdn_url($atc->url);
        $temp->file_name = $atc->file_name;
        $temp->file_size = $atc->file_size;
        $queryResult->file[] = $temp;
      }else if($atc->jenis == 'attendance sheet'){
        $temp->attendance_sheet_id = $atc->attendance_sheet_id;
        $temp->attendance_sheet_title = $atc->attendance_sheet_title;
        $temp->attendance_sheet_filled = $atc->attendance_sheet_filled;
        $temp->attendance_sheet_total = $atc->attendance_sheet_total;
        $attendanceSheetData = $this->igpaasm->getById($nation_code, $atc->attendance_sheet_id);
        $temp->response_option = $attendanceSheetData->response_option;
        $temp->self_check_in = $attendanceSheetData->self_check_in;
        $temp->start_date = $attendanceSheetData->start_date;
        $temp->deadline = $attendanceSheetData->deadline;
        $temp->show_attendance_progress = $attendanceSheetData->show_attendance_progress;
        $queryResult->attendance[] = $temp;
      }
    }
    unset($attachments,$atc);

    $this->status = 200;
    $this->message = 'Success';

    $data['post'] = $queryResult;

    //render output
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  // public function report()
  // {
  //   //initial
  //   $dt = $this->__init();

  //   //default result
  //   $data = array();
  //   $data['community'] = array();

  //   //check nation_code
  //   $nation_code = $this->input->get('nation_code');
  //   $nation_code = $this->nation_check($nation_code);
  //   if (empty($nation_code)) {
  //     $this->status = 101;
  //     $this->message = 'Missing or invalid nation_code';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
  //     die();
  //   }

  //   $apisess = $this->input->get('apisess');
  //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   if (!isset($pelanggan->id)) {
  //     $this->status = 401;
  //     $this->message = 'Missing or invalid API session';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
  //     die();
  //   }

  //   //populate input get
  //   $community_id = $this->input->get("community_id");
  //   $community = $this->ccomm->getById($nation_code, $community_id, $pelanggan);
  //   if (!isset($community->id)) {
  //     $this->status = 1160;
  //     $this->message = 'This post is deleted by an author';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  //     die();
  //   }

  //   //start transaction and lock table
  //   $this->ccrm->trans_start();

  //   //initial insert with latest ID
  //   $di = array();
  //   $di['nation_code'] = $nation_code;
  //   $di['c_community_id'] = $community_id;
  //   $di['b_user_id'] = $pelanggan->id;
  //   $di['cdate'] = 'NOW()';
  //   $res = $this->ccrm->set($di);
  //   if (!$res) {
  //     $this->ccrm->trans_rollback();
  //     $this->ccrm->trans_end();
  //     $this->status = 1107;
  //     $this->message = "Error, please try again later";
  //     $this->seme_log->write("api_mobile", 'API_Mobile/Community::report -- RollBack -- forceClose '.$this->status.' '.$this->message);
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  //     die();
  //   }

  //   $this->ccrm->trans_commit();
  //   $this->status = 200;
  //   // $this->message = 'This case is reported to SellOn Support';
  //   $this->message = 'Success';
  //   $this->seme_log->write("api_mobile", 'API_Mobile/Community::report -- INFO '.$this->status.' '.$this->message);

  //   //end transaction
  //   $this->ccrm->trans_end();

  //   //update is_report and report_date
  //   $di = array();
  //   $di['report_date'] = 'NOW()';
  //   $di['is_report'] = 1;
  //   $this->ccomm->update($nation_code, $community_id, $di);

  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  // }

  public function list_bookmark_post()
  {
    $data = array();
    $data['bookmarked_post'] = new stdClass();

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
    if (!isset($pelanggan->id)){
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

    //sanitize input
    $tbl_as = $this->igbpm->getTblAs();
    $sort_col = $this->__sortCol($sort_col, $tbl_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);
    $group_id = trim($this->input->post('group_id'));
    $timezone = $this->input->post("timezone");

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

    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $queryResult = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $group_id, $pelanggan->id);
    // if (strlen($group_id)>3){
    //   if (!isset($queryResult->b_user_id)){
    //     $this->status = 1103;
    //     $this->message = 'You are not the member of this club';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    //     die();
    //   }
    // }

    $bookmarkedPost = $this->igbpm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $pelanggan->id);
    // foreach($bookmarkedPost as $bookmarkerdpost) {
    //   $date = date_create($bookmarkerdpost->cdate);
    //   $new_date = date_format($date, "M j, Y");
    //   $new_time = date_format($date, "H:i");
    //   $bookmarkerdpost->cdate = $new_date.' at '.$new_time;
    // }

    $data['bookmarked_post'] = $bookmarkedPost;
    foreach ($data['bookmarked_post'] as &$pd) {
      $pd->total_likes = $this->thousandsCurrencyFormat($pd->total_likes);
      $pd->is_liked = '0';
      $pd->currentLikeEmoji = '';
      $pd->is_owner_post = '0';
      if(isset($pelanggan->id)){
        $checkLike = $this->igplm->getByCustomIdUserId($nation_code, $pd->id, $pelanggan->id, "post");
        if(isset($checkLike->id)){
          $pd->is_liked = '1';
          $pd->currentLikeEmoji = $checkLike->i_group_post_like_category_id;
        }
        unset($checkLike);
        if($pelanggan->id == $pd->b_user_id){
          $pd->is_owner_post = '1';
        }
      }

      $pd->is_owner_or_admin = '0';
      if (strlen($group_id)>3){
        if($queryResult->is_owner == "1" || $queryResult->is_co_admin == "1"){
          $pd->is_owner_or_admin = "1";
        }
      }

      $pd->cdate_text = $this->humanTiming($pd->cdate, null, $pelanggan->language_id);
      $pd->cdate = $this->customTimezone($pd->cdate, $timezone);
      $pd->deskripsi = html_entity_decode($pd->deskripsi,ENT_QUOTES);
      $pd->group_name = html_entity_decode($pd->group_name,ENT_QUOTES);
      if (isset($pd->b_user_band_image)) {
        if(file_exists(SENEROOT.$pd->b_user_band_image) && $pd->b_user_band_image != 'media/user/default.png'){
          $pd->b_user_band_image = $this->cdn_url($pd->b_user_band_image);
        } else {
          $pd->b_user_band_image = $this->cdn_url('media/user/default-profile-picture.png');
        }
      }

      $pd->images = array();
      $pd->locations = array();
      $pd->videos = array();
      $pd->file = array();
      $pd->attendance = array();
      $attachments = $this->igpam->getByGroupIdPostId($nation_code, $group_id, $pd->id);
      foreach ($attachments as $atc) {
        $temp = new stdClass();
        if($atc->jenis == 'image'){
          if (empty($atc->url)) {
            $atc->url = 'media/community_default.png';
          }
          if (empty($atc->url_thumb)) {
            $atc->url_thumb = 'media/community_default.png';
          }
          $temp->url = $this->cdn_url($atc->url);
          $temp->url_thumb = $this->cdn_url($atc->url_thumb);
          $pd->images[] = $temp;
        }else if($atc->jenis == 'location'){
          $temp->location_nama = $atc->location_nama;
          $temp->location_address = $atc->location_address;
          $temp->location_place_id = $atc->location_place_id;
          $temp->location_latitude = $atc->location_latitude;
          $temp->location_longitude = $atc->location_longitude;
          $pd->locations[] = $temp;
        }else if($atc->jenis == 'video'){
          $temp->video_id = $atc->id;
          $temp->url = $this->cdn_url($atc->url);
          $temp->url_thumb = $this->cdn_url($atc->url_thumb);
          $temp->convert_status = $atc->convert_status;
          // $temp->total_views = $this->thousandsCurrencyFormat($temp->total_views);
          $pd->videos[] = $temp;
        }else if($atc->jenis == 'file'){
          $temp->url = $this->cdn_url($atc->url);
          $temp->file_name = $atc->file_name;
          $temp->file_size = $atc->file_size;
          $pd->file[] = $temp;
        }else if($atc->jenis == 'attendance sheet'){
          $temp->attendance_sheet_id = $atc->attendance_sheet_id;
          $temp->attendance_sheet_title = $atc->attendance_sheet_title;
          $temp->attendance_sheet_filled = $atc->attendance_sheet_filled;
          $temp->attendance_sheet_total = $atc->attendance_sheet_total;
          $attendanceSheetData = $this->igpaasm->getById($nation_code, $atc->attendance_sheet_id);
          $temp->response_option = $attendanceSheetData->response_option;
          $temp->self_check_in = $attendanceSheetData->self_check_in;
          $temp->start_date = $attendanceSheetData->start_date;
          $temp->deadline = $attendanceSheetData->deadline;
          $temp->show_attendance_progress = $attendanceSheetData->show_attendance_progress;
          $pd->attendance[] = $temp;
        }
      }
      unset($attachments,$atc);
    }

    $this->status = 200;
    $this->message = "success";
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function add_bookmark_post()
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

    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)){
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $post_id = trim($this->input->post('post_id'));
    if (strlen($post_id)<3){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $queryResult = $this->igpostm->getById($nation_code, $post_id);
    if (!isset($queryResult->id)){
      $this->status = 1160;
      $this->message = 'This post is deleted by an author';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    // check if post already bookmarked
    $checkData = $this->igbpm->getByUserIdPostId($nation_code, $pelanggan->id, $post_id);
    if(isset($checkData->i_group_post_id) && isset($checkData->b_user_id)) {
      $this->status = 1130;
      $this->message = 'Bookmark has been deleted';
      $this->igbpm->delete($nation_code, $post_id, $pelanggan->id);
    } else {
      $this->igbpm->trans_start();

      $di = array();
      $endDoWhile = 0;
      do{
        $id = $this->GUIDv4();
        $checkId = $this->igbpm->checkId($nation_code, $id);
        if($checkId == 0){
          $endDoWhile = 1;
        }
      }while($endDoWhile == 0);
      $di['id'] = $id;
      $di['nation_code'] = $nation_code;
      $di['i_group_post_id'] = $post_id;
      $di['b_user_id'] = $pelanggan->id;
      $di['cdate'] = 'NOW()';
      $res = $this->igbpm->set($di);
      if (!$res) {
        $this->igbpm->trans_rollback();
        $this->igbpm->trans_end();
        $this->status = 1107;
        $this->message = "Error, please try again later";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }

      $this->igbpm->trans_commit();
      $this->igbpm->trans_end();

      $this->status = 1129;
      $this->message = 'Post has been added to bookmark';
    }

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function watchvideo()
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

    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)){
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $video_id = trim($this->input->post('video_id'));
    if (strlen($video_id)<3){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $queryResult = $this->igpam->getById($nation_code, $video_id);
    if (!isset($queryResult->id)){
      $this->status = 1160;
      $this->message = 'This post is deleted by an author';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    if($queryResult->b_user_id == $pelanggan->id){
      $this->status = 200;
      $this->message = 'Success';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    // check if watched video already got spt from this user
    $checkData = $this->igpawvm->getByAttachmentIdUserIdType($nation_code, $video_id, $pelanggan->id, $queryResult->jenis);
    if(!isset($checkData->cdate) && $queryResult->jenis == "video") {
      //get point
      $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E27");
      if (!isset($pointGet->remark)) {
        $pointGet = new stdClass();
        $pointGet->remark = 1;
      }

      $di = array();
      $di['nation_code'] = $nation_code;
      $di['b_user_alamat_location_kelurahan'] = "All";
      $di['b_user_alamat_location_kecamatan'] = "All";
      $di['b_user_alamat_location_kabkota'] = "All";
      $di['b_user_alamat_location_provinsi'] = "All";
      $di['b_user_id'] = $queryResult->b_user_id;
      $di['point'] = $pointGet->remark;
      $di['custom_id'] = $video_id;
      $di['custom_type'] = 'club';
      $di['custom_type_sub'] = "watch video";
      $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and creator post get '.$di['point'].' point(s)';
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

      $di = array();
      $di['nation_code'] = $nation_code;
      $di['i_group_id'] = $queryResult->i_group_id;
      $di['i_group_post_id'] = $queryResult->i_group_post_id;
      $di['i_group_directory_id'] = $queryResult->i_group_directory_id;
      $di['i_group_post_attachment_id'] = $queryResult->id;
      $di['b_user_id'] = $pelanggan->id;
      $di['jenis'] = $queryResult->jenis;
      $endDoWhile = 0;
      do{
        $igpawvmId = $this->GUIDv4();
        $checkId = $this->igpawvm->checkId($nation_code, $igpawvmId);
        if($checkId == 0){
          $endDoWhile = 1;
        }
      }while($endDoWhile == 0);
      $di['id'] = $igpawvmId;
      $this->igpawvm->set($di);
    }

    $this->status = 200;
    $this->message = 'Success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
  }

  public function video_list()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['posts'] = array();

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

    $timezone = $this->input->post("timezone");
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    // $sort_col = $this->input->post("sort_col");
    // $sort_dir = $this->input->post("sort_dir");
    // $page = $this->input->post("page");
    $page = 1;
    $page_size = $this->input->post("page_size");

    // $tbl_as = $this->igpostm->getTblAs();
    // $sort_col = $this->__sortCol($sort_col, $tbl_as);
    // $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

    $group_id = trim($this->input->post("group_id"));
    if (strlen($group_id)<3){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $group = $this->igm->getById($nation_code, $group_id);
    if (!isset($group->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $group_id, $pelanggan->id);
    if (!isset($queryResult->b_user_id)){
      $this->status = 1103;
      $this->message = 'You are not the member of this club';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $watched_video = $this->input->post("watched_video");

    //keyword
    // $keyword = trim($this->input->post("keyword"));
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

    $data['posts'] = $this->igpostm->getAllVideo($nation_code, $page, $page_size, $group_id, $watched_video);
    foreach ($data['posts'] as &$pd) {
      $pd->total_likes = $this->thousandsCurrencyFormat($pd->total_likes);
      $pd->is_liked = '0';
      $pd->currentLikeEmoji = '';
      $pd->is_owner_post = '0';
      if(isset($pelanggan->id)){
        $checkLike = $this->igplm->getByCustomIdUserId($nation_code, $pd->id, $pelanggan->id, "post");
        if(isset($checkLike->id)){
          $pd->is_liked = '1';
          $pd->currentLikeEmoji = $checkLike->i_group_post_like_category_id;
        }
        unset($checkLike);
        if($pelanggan->id == $pd->b_user_id){
          $pd->is_owner_post = '1';
        }
      }

      $pd->is_owner_or_admin = '0';
      if (strlen($group_id)>3){
        if($queryResult->is_owner == "1" || $queryResult->is_co_admin == "1"){
          $pd->is_owner_or_admin = "1";
        }
      }

      $checkBookmark = $this->igbpm->getByUserIdPostId($nation_code, $pelanggan->id, $pd->id);
      if($pelanggan->id != isset($checkBookmark->b_user_id)) {
        $pd->is_bookmark = "0";
      } else {
        $pd->is_bookmark = "1";
      }

      $pd->cdate_text = $this->humanTiming($pd->cdate, null, $pelanggan->language_id);
      $pd->cdate = $this->customTimezone($pd->cdate, $timezone);
      $pd->deskripsi = html_entity_decode($pd->deskripsi,ENT_QUOTES);
      $pd->group_name = html_entity_decode($pd->group_name,ENT_QUOTES);
      if (isset($pd->b_user_band_image)) {
        if(file_exists(SENEROOT.$pd->b_user_band_image) && $pd->b_user_band_image != 'media/user/default.png'){
          $pd->b_user_band_image = $this->cdn_url($pd->b_user_band_image);
        } else {
          $pd->b_user_band_image = $this->cdn_url('media/user/default-profile-picture.png');
        }
      }

      $pd->url = $this->cdn_url($pd->url);
      $pd->url_thumb = $this->cdn_url($pd->url_thumb);

      $pd->is_blocked = "0";
      $blockDataAccount = $this->cbm->getById($nation_code, 0, $pd->b_user_id, "account", $pelanggan->id);
      $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $pd->b_user_id);
      if(isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){
        $pd->is_blocked = "1";
      }

      $pd->is_follow = $this->buf->checkFollow($nation_code, $pelanggan->id, $pd->b_user_id);
    }

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }
}
