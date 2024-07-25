<?php
class Profile extends JI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->lib("seme_log");
    $this->load("api_mobile/b_user_model", "bu");
    $this->load("api_mobile/group/i_group_post_model", "igpostm");
    $this->load("api_mobile/group/i_group_post_attachment_model", "igpam");
    $this->load("api_mobile/group/i_group_model", "igm");
    $this->load("api_mobile/group/i_chat_room_model", 'icrm');
    $this->load("api_mobile/group/i_chat_participant_model", "icpm");
    $this->load("api_mobile/group/i_group_post_like_model", "igplm");
    $this->load("api_mobile/group/i_group_post_attachment_attendance_sheet_model", "igpaasm");
    $this->load("api_mobile/group/i_group_post_comment_model", "igpcm");
    $this->load("api_mobile/group/i_group_post_comment_attachment_model", "igpcam");
    $this->load("api_mobile/c_block_model", "cbm");
    $this->load("api_mobile/b_user_follow_model", 'buf');
  }

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

  private function __moveImageProfilex($nation_code, $url, $targetdir, $produk_id="0", $ke="")
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
      // $file_path_thumb = substr($file_path_thumb,0,strripos($file_path_thumb,'.'));
      // $file_path_thumb = SENEROOT.$file_path_thumb.'-thumb.'.$extension;

      $filename = "$nation_code-$produk_id-$ke-".date('YmdHis');
      $filethumb = $filename."-thumb.".$extension;
      $filename = $filename.".".$extension;

      rename($file_path, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
      // rename($file_path_thumb, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);

      $this->lib("wideimage/WideImage", "inc");
      WideImage::load(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)->reSize(300)->saveToFile(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
      WideImage::load(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)->crop('center', 'center', 300, 300)->saveToFile(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);

      $sc->status = 200;
      $sc->message = 'Success';
      // $sc->thumb = str_replace("//", "/", $targetdir.'/'.$filethumb);
      $sc->image = str_replace("//", "/", $targetdir.'/'.$filename);
      // $sc->file_size = filesize(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
      // $sc->file_size_thumb = filesize(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);

    } else {
      $sc->status = 997;
      $sc->message = 'Failed';
    }

    // $this->seme_log->write("api_mobile", 'API_Mobile/Community::__moveImageProfilex -- INFO URL: '.$url.' PID:'.$produk_id.' ke:'.$ke.' '.$sc->status.' '.$sc->message.'');
    return $sc;
  }

  private function __sortCol($sort_col, $tbl_as)
  {
    switch ($sort_col) {
      case 'cdate':
        $sort_col = "$tbl_as.cdate";
        break;
      case 'id':
        $sort_col = "$tbl_as.id";
        break;
      case 'name':
        $sort_col = "$tbl_as.name";
        break;
      default:
        $sort_col = "$tbl_as.cdate";
    }
    return $sort_col;
  }
  private function __sortDir($sort_dir)
  {
    $sort_dir = strtolower($sort_dir);
    if(empty($sort_dir)) {
      $sort_dir = "DESC";
    }
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
    $data['profile'] = array();

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

    $b_user_id = trim($this->input->post('b_user_id'));
    if (empty($b_user_id)){
      $this->status = 1111;
      $this->message = 'b_user_id is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $data['profile'] = $this->bu->getById($nation_code, $b_user_id);
    if (!isset($data['profile']->id)) {
      $this->status = 1001;
      $this->message = 'Missing or invalid b_user_id';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
      die();
    }

    if(file_exists(SENEROOT.$data['profile']->band_image) && $data['profile']->band_image != 'media/user/default.png'){
      $data['profile']->band_image = $this->cdn_url($data['profile']->band_image);
    } else {
      $data['profile']->band_image = $this->cdn_url('media/user/default-profile-picture.png');
    }

    $data['profile']->image = $this->cdn_url($data['profile']->image);
    $data['profile']->cdate = $this->humanTiming($data['profile']->cdate, null, $pelanggan->language_id);

    unset($data['profile']->fb_id);
    unset($data['profile']->apple_id);
    unset($data['profile']->google_id);
    unset($data['profile']->password);
    unset($data['profile']->latitude);
    unset($data['profile']->longitude);
    unset($data['profile']->kelamin);
    unset($data['profile']->bdate);
    // unset($data['profile']->cdate);
    unset($data['profile']->adate);
    unset($data['profile']->edate);
    // unset($data['profile']->telp);
    unset($data['profile']->intro_teks);
    unset($data['profile']->api_social_id);
    unset($data['profile']->fcm_token);
    unset($data['profile']->device);
    unset($data['profile']->is_agree);
    unset($data['profile']->is_confirmed);
    // unset($data['profile']->is_active);
    // unset($data['profile']->telp_is_verif);
    unset($data['profile']->api_mobile_edate);
    unset($data['profile']->is_reset_password);
    unset($data['profile']->api_web_token);
    unset($data['profile']->api_mobile_token);
    unset($data['profile']->api_reg_token);

    unset($data['profile']->country_origin);
    unset($data['profile']->register_place_alamat2);
    unset($data['profile']->register_place_kelurahan);
    unset($data['profile']->register_place_kecamatan);
    unset($data['profile']->register_place_kabkota);
    unset($data['profile']->register_place_provinsi);
    unset($data['profile']->register_place_kodepos);
    unset($data['profile']->bio);
    unset($data['profile']->website);
    unset($data['profile']->image_banner);
    unset($data['profile']->ip_address);
    unset($data['profile']->is_emulator);
    unset($data['profile']->user_wallet_code);
    unset($data['profile']->blockchain_createuserwallet_api_called);
    unset($data['profile']->main_transaction_id);
    unset($data['profile']->blockchain_latereferralrewardtransaction_api_called);
    unset($data['profile']->blockchain_latereferralrewardtransaction_api_called_cdate);
    unset($data['profile']->register_from);
    unset($data['profile']->register_from_app);
    unset($data['profile']->device_id);
    unset($data['profile']->g_mobile_registration_activity_id);
    unset($data['profile']->is_online);
    unset($data['profile']->last_online);
    unset($data['profile']->is_changed_address);
    unset($data['profile']->is_active);
    unset($data['profile']->is_permanent_inactive);
    unset($data['profile']->permanent_inactive_date);
    unset($data['profile']->permanent_inactive_by);
    unset($data['profile']->inactive_text);
    unset($data['profile']->telp_is_verif);
    unset($data['profile']->offer_rating_seller_avg);
    unset($data['profile']->offer_rating_seller_total);
    unset($data['profile']->offer_rating_buyer_avg);
    unset($data['profile']->offer_rating_buyer_total);
    unset($data['profile']->is_admin);
    unset($data['profile']->free_ticket_rock_paper_scissors);
    unset($data['profile']->free_ticket_shooting_fire);
    unset($data['profile']->free_ticket_sellon_match);
    unset($data['profile']->earned_ticket);

    $data['profile']->is_blocked = "0";
    $data['profile']->is_follow = "0";
    if($pelanggan->id != $b_user_id){
      $blockDataAccount = $this->cbm->getById($nation_code, 0, $b_user_id, "account", $pelanggan->id);
      $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $b_user_id);
      if(isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){
        $data['profile']->is_blocked = "1";
      }

      $data['profile']->is_follow = $this->buf->checkFollow($nation_code, $pelanggan->id, $b_user_id);
    }

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function change()
  {
    $data = array();
    $data['profile'] = array();

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

    $fnama = trim($this->input->post('fnama'));
    if (strlen($fnama)<1){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $band_fnama = trim($this->input->post('band_fnama'));
    if (strlen($band_fnama)<1){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $bio = trim($this->input->post('bio'));

    $checkFileExist = 1;
    $checkFileTemporaryOrNot = 1;
    if($this->input->post('foto') != null){
      $file_path = parse_url($this->input->post('foto'), PHP_URL_PATH);
      if (strpos($file_path, 'temporary') !== false) {
        if (!file_exists(SENEROOT.$file_path)) {
          $checkFileExist = 0;
        }
      }else{
        $checkFileTemporaryOrNot = 0;
      }
    }

    if ($checkFileExist == 0) {
      $this->status = 995;
      $this->message = 'Failed upload, temporary already gone';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //start transaction and lock table
		$this->bu->trans_start();

    $du = array();
    if($this->input->post('foto') != null && $checkFileTemporaryOrNot == 1){
      $file_path = parse_url($this->input->post('foto'), PHP_URL_PATH);
      $sc = $this->__moveImageProfilex($nation_code, $file_path, $this->media_group_profile_image, $pelanggan->id, "0");
      if (isset($sc->status)) {
        if ($sc->status==200) {
          $du['band_image'] = $sc->image;
          // $du['band_image'] = $sc->thumb;
        }
      }
    }
    $du['fnama'] = $fnama;
    $du['band_fnama'] = $band_fnama;
    $du['band_bio'] = $bio;
    $res = $this->bu->update($nation_code, $pelanggan->id, $du);
    if (!$res) {
      $this->status = 1107;
      $this->message = 'Error, please try again later';
      $this->bu->trans_rollback();
      $this->bu->trans_end();
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->bu->trans_commit();
    $this->bu->trans_end();

    $pelanggan = $this->bu->getById($nation_code, $pelanggan->id);
    $data['profile'] = $pelanggan;

    if(file_exists(SENEROOT.$data['profile']->band_image) && $data['profile']->band_image != 'media/user/default.png'){
      $data['profile']->band_image = $this->cdn_url($data['profile']->band_image);
    } else {
      $data['profile']->band_image = $this->cdn_url('media/user/default-profile-picture.png');
    }

    unset($data['profile']->fb_id);
    unset($data['profile']->apple_id);
    unset($data['profile']->google_id);
    unset($data['profile']->password);
    unset($data['profile']->latitude);
    unset($data['profile']->longitude);
    unset($data['profile']->kelamin);
    unset($data['profile']->bdate);
    // unset($data['profile']->cdate);
    unset($data['profile']->adate);
    unset($data['profile']->edate);
    // unset($data['profile']->telp);
    unset($data['profile']->intro_teks);
    unset($data['profile']->api_social_id);
    unset($data['profile']->fcm_token);
    unset($data['profile']->device);
    unset($data['profile']->is_agree);
    unset($data['profile']->is_confirmed);
    // unset($data['profile']->is_active);
    // unset($data['profile']->telp_is_verif);
    unset($data['profile']->api_mobile_edate);
    unset($data['profile']->is_reset_password);
    unset($data['profile']->api_web_token);
    unset($data['profile']->api_mobile_token);
    unset($data['profile']->api_reg_token);

    $this->status = 200;
    $this->message = 'Success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function user_post_photos()
  {
    $data = array();
    $data['user_post_photos'] = array();

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

    $b_user_id = trim($this->input->post('b_user_id'));
    if (empty($b_user_id)){
      $this->status = 1111;
      $this->message = 'b_user_id is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    $page_size = $this->input->post("page_size");

    $group_id = trim($this->input->post('group_id'));
    if (strlen($group_id)<3){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igm->getGroupSettings($nation_code, $group_id);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    //sanitize input
    $tbl_as = $this->igpam->getTblAs();
    $sort_col = $this->__sortCol($sort_col, $tbl_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

    $posts = $this->igpam->getByGroupIdUserId($nation_code, $b_user_id, "all", "image", $page, $page_size, $sort_col, $sort_dir, $group_id);
    foreach($posts as &$post) {
      $post->url = $this->cdn_url($post->url);
      $post->url_thumb = $this->cdn_url($post->url_thumb);
    }
    $data['user_post_photos'] = $posts;

    $this->status = 200;
    $this->message = 'Success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function user_posts()
  {
    $data = array();
    $data['user_posts'] = array();

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

    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    $page_size = $this->input->post("page_size");

    $group_id = trim($this->input->post('group_id'));
    if (strlen($group_id)<3){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igm->getGroupSettings($nation_code, $group_id);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $b_user_id = trim($this->input->post('b_user_id'));
    if (empty($b_user_id)){
      $this->status = 1111;
      $this->message = 'b_user_id is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    //sanitize input
    $tbl_as = $this->igpostm->getTblAs();
    $sort_col = $this->__sortCol($sort_col, $tbl_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);
    $timezone = $this->input->post("timezone");

    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    // $data['user_posts'] = $this->igpostm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $group_id, "", $pelanggan->id, $b_user_id);
    $data['user_posts'] = $this->igpostm->getAllByGroupIdUserId($nation_code, $page, $page_size, $sort_col, $sort_dir, $group_id, "", $b_user_id);

    foreach ($data['user_posts'] as &$pd) {
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

      $date = date_create($pd->cdate);
      $new_date = date_format($date, "M j, Y");
      $new_time = date_format($date, "H:i");
      $pd->cdate = $new_date.' at '.$new_time;

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
          $temp->url = $this->cdn_url($atc->url);
          $temp->url_thumb = $this->cdn_url($atc->url_thumb);
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
    $this->message = 'Success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function user_post_comments() 
  {
    $data = array();
    $data['user_post_comments'] = array();

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

    $b_user_id = trim($this->input->post('b_user_id'));
    if (empty($b_user_id)){
      $this->status = 1111;
      $this->message = 'b_user_id is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    $page_size = $this->input->post("page_size");
    $timezone = $this->input->get("timezone");
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

    $queryResult = $this->igm->getGroupSettings($nation_code, $group_id);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    //sanitize input
    $tbl_as = $this->igpcm->getTblAs();
    $sort_col = $this->__sortCol($sort_col, $tbl_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

    $ddata = $this->igpcm->getbyGroupIDUserID($nation_code, $page, $page_size, $sort_col, $sort_dir, $b_user_id, $group_id, "all");
    foreach ($ddata as &$dd) {

      $dd->cdate_text = $this->humanTiming($dd->cdate, null, $pelanggan->language_id);
      $dd->cdate = $this->customTimezone($dd->cdate, $timezone);
      $dd->text = html_entity_decode($dd->text,ENT_QUOTES);
      if(file_exists(SENEROOT.$dd->b_user_band_image) && $dd->b_user_band_image != 'media/user/default.png'){
        $dd->b_user_band_image = $this->cdn_url($dd->b_user_band_image);
      } else {
        $dd->b_user_band_image = $this->cdn_url('media/user/default-profile-picture.png');
      }

      $dd->images = new stdClass();
      $dd->locations = new stdClass();
      $attachments = $this->igpcam->getByDiscussionId($nation_code, $dd->id);
      foreach ($attachments as $atc) {
        if($atc->jenis == 'image'){
          $atc->url = $this->cdn_url($atc->url);
          $atc->url_thumb = $this->cdn_url($atc->url_thumb);
          $dd->images = $atc;
        }else{
          $dd->locations = $atc;
        }
      }
      unset($attachments);
    }
    unset($dd); //free some memory

    //build result
    $data['user_post_comments'] = $ddata;

    $this->status = 200;
    $this->message = 'Success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }
}
