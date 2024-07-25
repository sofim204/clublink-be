<?php
class Settings extends JI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->lib("seme_log");
    $this->load("api_mobile/b_user_model", "bu");
    $this->load("api_mobile/group/i_group_model", "igm");
    $this->load("api_mobile/group/i_group_participant_model", "igparticipantm");
    $this->load("api_mobile/group/i_chat_room_model", 'icrm');
    $this->load("api_mobile/group/i_chat_participant_model", "icpm");
    $this->load("api_mobile/group/i_group_attachment_model", 'igam');
    $this->load("api_mobile/group/i_group_admin_activity_log_model", 'igaalm');
    $this->load("api_mobile/group/i_group_category_model", "igcm");
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

  private function __uploadUserImage($nation_code, $group_id)
  {
    /*******************
     * Only these origins will be allowed to upload images *
     ******************/
    $folder = SENEROOT.DIRECTORY_SEPARATOR.$this->media_group_image.DIRECTORY_SEPARATOR;
    $folder = str_replace('\\', '/', $folder);
    $folder = str_replace('//', '/', $folder);
    $ifol = realpath($folder);
    //die($folder);
    if (!$ifol) {
      mkdir($folder);
    }
    $ifol = realpath($folder);
    //die($ifol);

    reset($_FILES);
    $temp = current($_FILES);
    if (is_array($temp) && is_uploaded_file($temp['tmp_name'])) {
      if (isset($_SERVER['HTTP_ORIGIN'])) {
        // same-origin requests won't set an origin. If the origin is set, it must be valid.
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
      }
      header('Access-Control-Allow-Credentials: true');
      header('P3P: CP="There is no P3P policy."');

      // Sanitize input
      if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
        header("HTTP/1.0 500 Invalid file name.");
        return 0;
      }
      if (mime_content_type($temp['tmp_name']) == 'webp') {
        header("HTTP/1.0 500 WebP currently unsupported.");
        return 0;
      }
      // Verify extension
      $ext = pathinfo($temp['name'], PATHINFO_EXTENSION);
      if (!in_array(strtolower($ext), array("jpeg", "jpg", "png"))) {
        header("HTTP/1.0 500 Invalid extension.");
        return 0;
      }

      // Create magento style media directory
      $year = date("Y");
      $month = date("m");
      if (PHP_OS == "WINNT") {
        if (!is_dir($ifol)) {
          mkdir($ifol);
        }
        $ifol = $ifol.DIRECTORY_SEPARATOR.$year.DIRECTORY_SEPARATOR;
        if (!is_dir($ifol)) {
          mkdir($ifol);
        }
        $ifol = $ifol.DIRECTORY_SEPARATOR.$month.DIRECTORY_SEPARATOR;
        if (!is_dir($ifol)) {
          mkdir($ifol);
        }
      } else {
        if (!is_dir($ifol)) {
          mkdir($ifol, 0775, true);
        }
        $ifol = $ifol.DIRECTORY_SEPARATOR.$year.DIRECTORY_SEPARATOR;
        if (!is_dir($ifol)) {
          mkdir($ifol, 0775, true);
        }
        $ifol = $ifol.DIRECTORY_SEPARATOR.$month.DIRECTORY_SEPARATOR;
        if (!is_dir($ifol)) {
          mkdir($ifol, 0775, true);
        }
      }

      // Accept upload if there was no origin, or if it is an accepted origin
      $name = $nation_code.'-'.$group_id.'--'.date('YmdHis');
      $filetowrite = $ifol.$name.'.'.$ext;
      $filetowrite = str_replace('//', '/', $filetowrite);
      if (file_exists($filetowrite)) {
        $name = $nation_code.'-'.$group_id.'--'.date('YmdHis');
        $filetowrite = $ifol.$name.'.'.$ext;
        $filetowrite = str_replace('//', '/', $filetowrite);
        if (file_exists($filetowrite)) {
          $name = $nation_code.'-'.$group_id.'--'.date('YmdHis');
          $filetowrite = $ifol.$name.'.'.$ext;
          $filetowrite = str_replace('//', '/', $filetowrite);
        }
      }
      move_uploaded_file($temp['tmp_name'], $filetowrite);
      if (file_exists($filetowrite)) {
        //START by Donny Dennison - 16 november 2022 11:13
        //fix rotated image after resize(thumb)
        // if (in_array(strtolower($ext), array("jpg","jpeg"))) {
        //   $this->correctImageOrientation($filetowrite);
        // }
        //END by Donny Dennison - 16 november 2022 11:13
        //fix rotated image after resize(thumb)

        // $this->lib("wideimage/WideImage", "inc");
        // WideImage::load($filetowrite)->reSize(300)->saveToFile($filetowrite);
        // WideImage::load($filetowrite)->crop('center', 'center', 300, 300)->saveToFile($filetowrite);
        return $this->media_group_image."/".$year."/".$month."/".$name.'.'.$ext;
      } else {
        return 0;
      }
    } else {
      // Notify editor that the upload failed
      //header("HTTP/1.0 500 Server Error");
      return 0;
    }
  }

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
      $sc->file_size = filesize(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
      $sc->file_size_thumb = filesize(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
    } else {
      $sc->status = 997;
      $sc->message = 'Failed';
    }

    // $this->seme_log->write("api_mobile", 'API_Mobile/Community::__moveImagex -- INFO URL: '.$url.' PID:'.$produk_id.' ke:'.$ke.' '.$sc->status.' '.$sc->message.'');
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

  public function index()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['group_settings'] = array();
    $queryResult = new stdClass();

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

    $timezone = $this->input->post("timezone");
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $group_id = trim($this->input->get('group_id'));
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

    // check if you're owner
    // if($queryResult->b_user_id != $pelanggan->id){
    //   $this->status = 1103;
    //   $this->message = "You're not owner of this group";
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    //   die();
    // }

    $queryResult->status_member = $this->__statusMember($nation_code, $group_id, $pelanggan->id);
    $data['group_settings'] = $queryResult;

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function change_group_name_image()
  {
    $data = array();
    $data['group_settings'] = new stdClass();

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

    $group_name = trim($this->input->post('name'));

    //start transaction and lock table
		$this->igm->trans_start();

    $du = array();

    // if ($checkFileExist == 0) {
    //   $this->igm->trans_rollback();
    //   $this->igm->trans_end();
    //   $this->status = 995;
    //   $this->message = 'Failed upload, temporary already gone';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
    //   die();
    // }

    $file_path = parse_url($this->input->post('image1'), PHP_URL_PATH);
    if(strpos($file_path, 'temporary') !== false) {
      $sc = $this->__moveImagex($nation_code, $file_path, $this->media_group_image, $group_id, "");
      if (isset($sc->status)) {
        if ($sc->status == 200) {
          $du['image'] = $sc->image;
          $du['image_thumb'] = $sc->thumb;
        }
      }
    } else {
      $du['image_thumb'] = $file_path;
    }

    $du['name'] = $group_name;
    $res = $this->igm->update($nation_code, $group_id, $du);
    if (!$res) {
      $this->status = 1107;
      $this->message = 'Error, please try again later';
      $this->igm->trans_rollback();
      $this->igm->trans_end();
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $du = array();
    $du['custom_name_1'] = $group_name;
    $this->icrm->update($nation_code, $queryResult->i_chat_room_id, $du);

    $this->igm->trans_commit();
    $this->igm->trans_end();

    $group_setting = $this->igm->getGroupSettings($nation_code, $group_id);

    // insert to admin activity log
    $di = array();
    $endDoWhile = 0;
    do {
      $id = $this->GUIDv4();
      $checkId = $this->igaalm->checkId($nation_code, $id);
      if($checkId == 0) {
        $endDoWhile = 1;
      }
    } while ($endDoWhile == 0);

    $di['nation_code'] = $nation_code;
    $di['id'] = $id;
    $di['i_group_id'] = $group_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['type'] = 'band_settings';
    $di['title'] = 'Club Settings';
    $di['text'] = $pelanggan->band_fnama . " changing club name or image";
    $di['image'] = $pelanggan->band_image;
    $di['cdate'] = 'NOW()';
    $di['is_active'] = 1;
    $this->igaalm->set($di);

    $this->status = 200;
    $this->message = 'Success';

    $data['group_settings']->name = $group_setting->name;
    $data['group_settings']->image = $this->cdn_url($group_setting->image);

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function change_group_type()
  {
    $data = array();
    $data['group_settings'] = new stdClass();

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

    $group_type = strtolower(trim($this->input->post('group_type')));
    if (!in_array($group_type, array("private", "listed", "public"))) {
      $this->status = 1123;
      $this->message = 'Invalid Club Type';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    //start transaction and lock table
		$this->igm->trans_start();

    $du = array();
    $du['group_type'] = $group_type;
    $res = $this->igm->update($nation_code, $group_id, $du);
    if ($res) {
      $this->status = 200;
      $this->message = 'Success';
      $group_setting = $this->igm->getGroupSettings($nation_code, $group_id);

      // insert to admin activity log
      $di = array();
      $endDoWhile = 0;
      do {
        $id = $this->GUIDv4();
        $checkId = $this->igaalm->checkId($nation_code, $id);
        if($checkId == 0) {
          $endDoWhile = 1;
        }
      } while ($endDoWhile == 0);

      $di['nation_code'] = $nation_code;
      $di['id'] = $id;
      $di['i_group_id'] = $group_id;
      $di['b_user_id'] = $pelanggan->id;
      $di['type'] = 'band_settings';
      $di['title'] = 'Club Settings';
      $di['text'] = $pelanggan->band_fnama . " changing club type";
      $di['image'] = $pelanggan->band_image;
      $di['cdate'] = 'NOW()';
      $di['is_active'] = 1;
      $this->igaalm->set($di);
    } else {
      $this->status = 1107;
      $this->message = 'Error, please try again later';
      $this->igm->trans_rollback();
      $this->igm->trans_end();
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->igm->trans_commit();
    $this->igm->trans_end();

    $data['group_settings']->group_type = $group_setting->group_type;

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function change_group_sub_category()
  {
    $data = array();
    // $data['group_settings'] = new stdClass();

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

    $i_group_sub_category_id = strtolower(trim($this->input->post('i_group_sub_category_id')));
    if($queryResult->i_group_category_id == "13744ff9-1312-40c3-8acc-83cfd792013c"){
      $subKat = $this->igcm->getById($nation_code, $i_group_sub_category_id);
      if (!isset($subKat->id)) {
        $this->status = 1120;
        $this->message = 'Please choose sports category';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }
    }

    //start transaction and lock table
    $this->igm->trans_start();

    $du = array();
    $du['i_group_sub_category_id'] = $i_group_sub_category_id;
    $res = $this->igm->update($nation_code, $group_id, $du);
    if ($res) {
      $this->status = 200;
      $this->message = 'Success';
      // $group_setting = $this->igm->getGroupSettings($nation_code, $group_id);

      // insert to admin activity log
      $di = array();
      $endDoWhile = 0;
      do {
        $id = $this->GUIDv4();
        $checkId = $this->igaalm->checkId($nation_code, $id);
        if($checkId == 0) {
          $endDoWhile = 1;
        }
      } while ($endDoWhile == 0);

      $di['nation_code'] = $nation_code;
      $di['id'] = $id;
      $di['i_group_id'] = $group_id;
      $di['b_user_id'] = $pelanggan->id;
      $di['type'] = 'band_settings';
      $di['title'] = 'Club Settings';
      $di['text'] = $pelanggan->band_fnama . " changing club sub category";
      $di['image'] = $pelanggan->band_image;
      $di['cdate'] = 'NOW()';
      $di['is_active'] = 1;
      $this->igaalm->set($di);
    } else {
      $this->status = 1107;
      $this->message = 'Error, please try again later';
      $this->igm->trans_rollback();
      $this->igm->trans_end();
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->igm->trans_commit();
    $this->igm->trans_end();

    // $data['group_settings']->group_type = $group_setting->group_type;

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function change_group_description()
  {
    $data = array();
    $data['group_description'] = new stdClass();

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

    $queryResult = $this->igm->getGroupSettings($nation_code, $group_id);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $description = trim($this->input->post('description'));

    //by Donny Dennison 24 february 2021 18:45
    //change ’ to ' in add & edit product name and description
    $description = str_replace('’',"'",$description);
    $description = nl2br($description);

    //by Donny Dennison - 15 augustus 2020 15:09
    //bug fix \n (enter) didnt get remove
    $description = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $description);

    $description = str_replace("\\n", "<br />", $description);

    $location_json = $this->input->post("location_json");

    //start transaction and lock table
		$this->igm->trans_start();

    //updating to database
    $du = array();
    $du['description'] = $description;
    $res = $this->igm->update($nation_code, $group_id, $du);
    if (!$res) {
      $this->igm->trans_rollback();
      $this->igm->trans_end();
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
      $this->igm->trans_rollback();
      $this->igm->trans_end();
      $this->status = 995;
      $this->message = 'Failed upload, temporary already gone';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //delete image that is not in array
    $attachments = $this->igam->getByGroupId($nation_code, $group_id, "all", "image");
    foreach ($attachments as $atc) {
      if ((!in_array($atc->url, $listUrl) || empty($listUrl))) {
        $this->igam->update($nation_code, $atc->id, array("is_active"=> 0));
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
        do {
          $attachmentId = $this->GUIDv4();
          $checkId = $this->igam->checkId($nation_code, $attachmentId);
          if($checkId == 0){
            $endDoWhile = 1;
          }
        } while($endDoWhile == 0);

        $sc = $this->__moveImagex($nation_code, $upload, $this->media_group_description, $group_id, $attachmentId);
        if (isset($sc->status)) {
          if ($sc->status==200) {
            $dix = array();
            $dix['nation_code'] = $nation_code;
            $dix['id'] = $attachmentId;
            $dix['i_group_id'] = $group_id;
            $dix['b_user_id'] = $pelanggan->id;
            $dix['jenis'] = 'image';
            $dix['url'] = $sc->image;
            $dix['url_thumb'] = $sc->thumb;
            $dix['file_size'] = $sc->file_size;
            $dix['file_size_thumb'] = $sc->file_size_thumb;
            $this->igam->set($dix);
          }
        }
      }
    }

    $this->igam->delByGroupIdJenis($nation_code, $group_id, 'location');
    if(is_array($location_json)){
      if(count($location_json) > 0){
        foreach ($location_json as $key => $upload) {
          if(isset($upload['location_nama']) && isset($upload['location_address']) && isset($upload['location_place_id']) && isset($upload['location_latitude']) && isset($upload['location_longitude'])){
            if($upload['location_nama'] && $upload['location_address'] && $upload['location_place_id'] && $upload['location_latitude'] && $upload['location_longitude']){
              $endDoWhile = 0;
              do{
                $attachmentId = $this->GUIDv4();
                $checkId = $this->igam->checkId($nation_code, $attachmentId);
                if($checkId == 0){
                  $endDoWhile = 1;
                }
              }while($endDoWhile == 0);

              $dix = array();
              $dix['nation_code'] = $nation_code;
              $dix['id'] = $attachmentId;
              $dix['i_group_id'] = $group_id;
              $dix['b_user_id'] = $pelanggan->id;
              $dix['jenis'] = 'location';
              $dix['location_nama'] = $upload['location_nama'];
              $dix['location_address'] = $upload['location_address'];
              $dix['location_place_id'] = $upload['location_place_id'];
              $dix['location_latitude'] = $upload['location_latitude'];
              $dix['location_longitude'] = $upload['location_longitude'];
              $this->igam->set($dix);
            }
          }
        }
      }
    }

    $this->igm->trans_commit();
    $this->igm->trans_end();

    // insert to admin activity log
    $di = array();
    $endDoWhile = 0;
    do {
      $id = $this->GUIDv4();
      $checkId = $this->igaalm->checkId($nation_code, $id);
      if($checkId == 0) {
        $endDoWhile = 1;
      }
    } while ($endDoWhile == 0);

    $di['nation_code'] = $nation_code;
    $di['id'] = $id;
    $di['i_group_id'] = $group_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['type'] = 'band_settings';
    $di['title'] = 'Club Settings';
    $di['text'] = $pelanggan->band_fnama . " changing club description";
    $di['image'] = $pelanggan->band_image;
    $di['cdate'] = 'NOW()';
    $di['is_active'] = 1;
    $this->igaalm->set($di);

    $group_description = $this->igm->getGroupSettings($nation_code, $group_id, $pelanggan->id);
    $group_description->image = $this->cdn_url($group_description->image);

    $group_description->images = array();
    $group_description->location = array();

    $attachmentImage = $this->igam->getByGroupId($nation_code, $group_description->id, "all", "image");
    foreach($attachmentImage as &$atc_image) {
      if($atc_image->jenis == 'image' || $atc_image->jenis == 'video'){
				$atc_image->url = $this->cdn_url($atc_image->url);
				$atc_image->url_thumb = $this->cdn_url($atc_image->url_thumb);

				$group_description->images[] = $atc_image;
			}
    }
    unset($attachmentImage);

    $attachmentLocation = $this->igam->getByGroupId($nation_code, $group_description->id, "all", "location");
    foreach($attachmentLocation as &$atc_location) {
			$group_description->location[] = $atc_location;
    }
    unset($attachmentLocation);

    $data['group_description'] = $group_description;

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function change_group_size_limit()
  {
    $data = array();
    $data['group_settings'] = new stdClass();

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

    $group_size_limit = trim($this->input->post('size_limit'));
    if(!is_numeric($group_size_limit)) {
      $this->status = 1124;
      $this->message = 'Invalid size limit';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    // parse to int
    $group_size_limit = (int) $group_size_limit;

    //start transaction and lock table
		$this->igm->trans_start();

    $du = array();
    $du['size_limit'] = $group_size_limit;
    $res = $this->igm->update($nation_code, $group_id, $du);
    if ($res) {
      $this->status = 200;
      $this->message = 'Success';
      $group_setting = $this->igm->getGroupSettings($nation_code, $group_id);

      // insert to admin activity log
      $di = array();
      $endDoWhile = 0;
      do {
        $id = $this->GUIDv4();
        $checkId = $this->igaalm->checkId($nation_code, $id);
        if($checkId == 0) {
          $endDoWhile = 1;
        }
      } while ($endDoWhile == 0);
  
      $di['nation_code'] = $nation_code;
      $di['id'] = $id;
      $di['i_group_id'] = $group_id;
      $di['b_user_id'] = $pelanggan->id;
      $di['type'] = 'band_settings';
      $di['title'] = 'Club Settings';
      $di['text'] = $pelanggan->band_fnama . " changing club size limit";
      $di['image'] = $pelanggan->band_image;
      $di['cdate'] = 'NOW()';
      $di['is_active'] = 1;
      $this->igaalm->set($di);
    } else {
      $this->status = 1107;
      $this->message = 'Error, please try again later';
      $this->igm->trans_rollback();
      $this->igm->trans_end();
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->igm->trans_commit();
    $this->igm->trans_end();

    $data['group_settings']->size_limit = $group_setting->size_limit;

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function list_manager()
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

    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    $page_size = $this->input->post("page_size");
    $keyword = trim($this->input->post("keyword"));

    //sanitize input
    $tbl_as = $this->igparticipantm->getTblAs();
    $sort_col = $this->__sortColParticipant($sort_col, $tbl_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

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

    $getManager = $this->igparticipantm->getAllParticipantByGroupId($nation_code, $page, $page_size, $sort_col, "DESC", $keyword, $group_id, "manager");
    foreach($getManager as &$gm){
      $gm->b_user_band_image = $this->cdn_url($gm->b_user_band_image);
    }

    $data['list_manager'] = $getManager;

    $this->status = 200;
    $this->message = "success";

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  // can be use for assign co admin and transfer admin role
  public function list_member()
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

    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    $page_size = $this->input->post("page_size");
    $keyword = trim($this->input->post("keyword"));

    //sanitize input
    $tbl_as = $this->igparticipantm->getTblAs();
    $sort_col = $this->__sortColParticipant($sort_col, $tbl_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

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

    $getData = $this->igparticipantm->getAllParticipantByGroupId($nation_code, $page, $page_size, $sort_col, "ASC", $keyword, $group_id, "member");
    foreach($getData as &$gd){
      $gd->b_user_band_image = $this->cdn_url($gd->b_user_band_image);
    }

    $data['list_member'] = $getData;

    $this->status = 200;
    $this->message = "success";

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function set_member_to_coadmin()
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

    // $b_user_id = trim($this->input->post('b_user_id'));
    $b_user_id = $this->input->post('b_user_id');
    if (empty($b_user_id)){
      $this->status = 1111;
      $this->message = 'b_user_id is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    // if b_user_id == owner group, dont allow to become coadmin
    foreach($b_user_id as $key => $user_id) {
      if($b_user_id[$key] == $queryResult->b_user_id) {
        $this->status = 1122;
        $this->message = "You are owner";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }

      $getUserData = $this->bu->getById($nation_code, $b_user_id[$key]);
      // insert to admin activity log
      $di = array();
      $endDoWhile = 0;
      do {
        $id = $this->GUIDv4();
        $checkId = $this->igaalm->checkId($nation_code, $id);
        if($checkId == 0) {
          $endDoWhile = 1;
        }
      } while ($endDoWhile == 0);

      $di['nation_code'] = $nation_code;
      $di['id'] = $id;
      $di['i_group_id'] = $group_id;
      $di['b_user_id'] = $pelanggan->id;
      $di['type'] = 'band_settings';
      $di['title'] = 'Club Settings';
      $di['text'] = $pelanggan->band_fnama . " appointed " . $getUserData->band_fnama . ' as admin';
      $di['image'] = $pelanggan->image;
      $di['cdate'] = 'NOW()';
      $di['is_active'] = 1;
      $this->igaalm->set($di);

      $du = array();
      $du['is_co_admin'] = "1";
      $this->icpm->update($nation_code, $queryResult->i_chat_room_id, $b_user_id[$key], $du);

      $chatRoomList = $this->icrm->getAllByCustom($nation_code, $group_id, $b_user_id[$key]);
      foreach($chatRoomList AS $chatRoom){
        $du = array();
        $du['is_co_admin'] = "1";
        $this->icpm->update($nation_code, $chatRoom->id, $b_user_id[$key], $du);
      }
    }

    $du = array();
    $du['is_co_admin'] = "1";
    $this->igparticipantm->updateStatusParticipant($nation_code, $group_id, '0', $b_user_id, "mass", $du);

    $this->status = 200;
    $this->message = "success";

    $getManager = $this->igparticipantm->getAllParticipantByGroupId($nation_code, "", "", "", "DESC", "", $group_id, "manager");
    foreach($getManager as &$gm){
      $gm->b_user_band_image = $this->cdn_url($gm->b_user_band_image);
    }
    $data['list_manager'] = $getManager;

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function set_transfer_coadmin_role()
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

    // get data from participant to check previous account is owner, is admin or is member
    $getOld = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $group_id, $pelanggan->id);
    if(!isset($getOld->is_owner)){
      $this->status = 1125;
      $this->message = "Your priviledge is limited as you're member";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }
    if($getOld->is_owner != "1"){
      $this->status = 1125;
      $this->message = "Your priviledge is limited as you're member";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    if($getOld->is_owner == "1" && $getOld->is_co_admin == "0") {
      $status = "owner_to_member";
    // } else if($getOld->is_owner == "0" && $getOld->is_co_admin == "1") {
    //   $status = "coadmin_to_member";
    }

    // update old
    $du = array();
    if($status == "owner_to_member") {
      $du['is_owner'] = "0";
      $du['is_co_admin'] = "0";
    // } else if($status == "coadmin_to_member") {
    //   $du['is_owner'] = "0";
    //   $du['is_co_admin'] = "0";
    }
    $this->igparticipantm->updateStatusParticipant($nation_code, $group_id, '0', $pelanggan->id, "", $du);

    $du = array();
    if($status == "owner_to_member") {
      $du['is_owner'] = "0";
      $du['is_co_admin'] = "0";
      $du['is_creator'] = "0";
    // } else if($status == "coadmin_to_member") {
    //   $du['is_owner'] = "0";
    //   $du['is_co_admin'] = "0";
    //   $du['is_creator'] = "0";
    }
    $this->icpm->update($nation_code, $queryResult->i_chat_room_id, $pelanggan->id, $du);

    $chatRoomList = $this->icrm->getAllByCustom($nation_code, $group_id, $pelanggan->id);
    foreach($chatRoomList AS $chatRoom){
      $du = array();
      if($status == "owner_to_member") {
        $du['is_owner'] = "0";
        $du['is_co_admin'] = "0";
      // } else if($status == "coadmin_to_member") {
      //   $du['is_owner'] = "0";
      //   $du['is_co_admin'] = "0";
      }
      $this->icpm->update($nation_code, $chatRoom->id, $pelanggan->id, $du);
    }

    // update new
    $du = array();
    if($status == "owner_to_member") {
      $du['is_owner'] = "1";
      $du['is_co_admin'] = "0";
    // } else if($status == "coadmin_to_member") {
    //   $du['is_owner'] = "0";
    //   $du['is_co_admin'] = "1";
    }
    $this->igparticipantm->updateStatusParticipant($nation_code, $group_id, '0', $b_user_id, "", $du);

    $du = array();
    if($status == "owner_to_member") {
      $du['is_owner'] = "1";
      $du['is_co_admin'] = "0";
      $du['is_creator'] = "1";
    // } else if($status == "coadmin_to_member") {
    //   $du['is_owner'] = "0";
    //   $du['is_co_admin'] = "1";
    //   $du['is_creator'] = "0";
    }
    $this->icpm->update($nation_code, $queryResult->i_chat_room_id, $b_user_id, $du);

    $chatRoomList = $this->icrm->getAllByCustom($nation_code, $group_id, $b_user_id);
    foreach($chatRoomList AS $chatRoom){
      $du = array();
      if($status == "owner_to_member") {
        $du['is_owner'] = "1";
        $du['is_co_admin'] = "0";
      // } else if($status == "coadmin_to_member") {
      //   $du['is_owner'] = "0";
      //   $du['is_co_admin'] = "1";
      }
      $this->icpm->update($nation_code, $chatRoom->id, $b_user_id, $du);
    }

    if($status == "owner_to_member") {
      $du = array();
      $du['b_user_id'] = $b_user_id;
      $this->igm->update($nation_code, $group_id, $du);

      $du = array();
      $du['b_user_id_creator'] = $b_user_id;
      $this->icrm->update($nation_code, $queryResult->i_chat_room_id, $du);
    }

    $getUserData = $this->bu->getById($nation_code, $b_user_id);

    // insert to admin activity log
    $di = array();
    $endDoWhile = 0;
    do {
      $id = $this->GUIDv4();
      $checkId = $this->igaalm->checkId($nation_code, $id);
      if($checkId == 0) {
        $endDoWhile = 1;
      }
    } while ($endDoWhile == 0);

    $di['nation_code'] = $nation_code;
    $di['id'] = $id;
    $di['i_group_id'] = $group_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['type'] = 'band_settings';
    $di['title'] = 'Club Settings';
    $di['text'] = $pelanggan->band_fnama . " transferring owner role to " . $getUserData->band_fnama;
    $di['image'] = $pelanggan->image;
    $di['cdate'] = 'NOW()';
    $di['is_active'] = 1;
    $this->igaalm->set($di);
    
    $getManager = $this->igparticipantm->getAllParticipantByGroupId($nation_code, "", "", "", "DESC", "", $group_id, "manager");
    foreach($getManager as &$gm){
      $gm->b_user_band_image = $this->cdn_url($gm->b_user_band_image);
    }
    $data['list_manager'] = $getManager;

    $this->status = 200;
    $this->message = "success";

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function admin_activity_log()
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

    $group_id = trim($this->input->get('group_id'));
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

    $page = $this->input->get("page");
    $page_size = $this->input->get("page_size");
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

    //manipulator
    $activity_log = $this->igaalm->getAll($nation_code, $group_id, $page, $page_size, "cdate", "desc");
    foreach($activity_log as &$al){
      $al->title = html_entity_decode($al->title,ENT_QUOTES);
      $al->text = html_entity_decode($al->text,ENT_QUOTES);
      
      // if(strlen($al->extras)<=2) $al->extras = '{}';
      // $obj = json_decode($al->extras);
      // if(is_object($obj)) $al->extras = $obj;
      if(strlen($al->image)>4){
        $al->image = $this->cdn_url($al->image);
      }

      $date = date_create($al->cdate);
      $new_date = date_format($date, "M j, Y");
      $new_time = date_format($date, "H:i");
      $al->cdate = $new_date.' at '.$new_time;

      // if(isset($obj->product_id)){
      //   $obj->product_id = (string) $obj->product_id;
      // }
    }

    $data['activity_log'] = $activity_log;

    $this->status = 200;
    $this->message = "success";
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  // member requests | Admin's approval is required to join
  public function need_admin_approval()
  {
    $data = array();
    $updatedData = new stdClass();

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

    $get_action = $this->input->get('action');
		if (empty($get_action) || !in_array($get_action, array('yes', 'no'))) {
			$this->status = 1112;
			$this->message = 'Missing or invalid action';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

    $group_id = trim($this->input->get('group_id'));
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

    if($get_action == "yes")
      $action = "1";
    else if($get_action == "no")
      $action = "0";
    
    $du = array();
    $du['need_admin_approval'] = $action;
    $this->igm->update($nation_code, $group_id, $du);

    // insert to admin activity log
    $di = array();
    $endDoWhile = 0;
    do {
      $id = $this->GUIDv4();
      $checkId = $this->igaalm->checkId($nation_code, $id);
      if($checkId == 0) {
        $endDoWhile = 1;
      }
    } while ($endDoWhile == 0);

    $di['nation_code'] = $nation_code;
    $di['id'] = $id;
    $di['i_group_id'] = $group_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['type'] = 'band_settings';
    $di['title'] = 'Club Settings';
    if($action == "1") {
      $di['text'] = $pelanggan->band_fnama . " set active need admin approval setting";
    } else if($action == "0") {
      $di['text'] = $pelanggan->band_fnama . " set inactive need admin approval setting";
    }
    $di['image'] = $pelanggan->band_image;
    $di['cdate'] = 'NOW()';
    $di['is_active'] = 1;
    $this->igaalm->set($di);

    $updatedData = $this->igm->getGroupSettings($nation_code, $group_id);
    $updatedData->status_member = $this->__statusMember($nation_code, $group_id, $pelanggan->id);
    $data['group_settings'] = $updatedData;

    $this->status = 200;
    $this->message = "success";
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  // A “Welcome” post will be uploaded when a member joins.
  public function show_welcome_post()
  {
    $data = array();
    $updatedData = new stdClass();

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

    $get_action = $this->input->get('action');
		if (empty($get_action) || !in_array($get_action, array('yes', 'no'))) {
			$this->status = 1112;
			$this->message = 'Missing or invalid action';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

    $group_id = trim($this->input->get('group_id'));
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

    if($get_action == "yes")
      $action = "1";
    else if($get_action == "no")
      $action = "0";
    
    $du = array();
    $du['show_welcome_post'] = $action;
    $this->igm->update($nation_code, $group_id, $du);

    // insert to admin activity log
    $di = array();
    $endDoWhile = 0;
    do {
      $id = $this->GUIDv4();
      $checkId = $this->igaalm->checkId($nation_code, $id);
      if($checkId == 0) {
        $endDoWhile = 1;
      }
    } while ($endDoWhile == 0);

    $di['nation_code'] = $nation_code;
    $di['id'] = $id;
    $di['i_group_id'] = $group_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['type'] = 'band_settings';
    $di['title'] = 'Club Settings';
    if($action == "1") {
      $di['text'] = $pelanggan->band_fnama . " set active show welcome post setting";
    } else if($action == "0") {
      $di['text'] = $pelanggan->band_fnama . " set inactive show welcome post setting";
    }
    $di['image'] = $pelanggan->band_image;
    $di['cdate'] = 'NOW()';
    $di['is_active'] = 1;
    $this->igaalm->set($di);

    $updatedData = $this->igm->getGroupSettings($nation_code, $group_id);
    $updatedData->status_member = $this->__statusMember($nation_code, $group_id, $pelanggan->id);
    $data['group_settings'] = $updatedData;

    $this->status = 200;
    $this->message = "success";
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  // set co admin to become member
  public function delete_co_admin()
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

    $b_user_id = $this->input->post('b_user_id');
    if (empty($b_user_id)){
      $this->status = 1111;
      $this->message = 'b_user_id is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    if($b_user_id == $queryResult->b_user_id) {
      $this->status = 1122;
      $this->message = "You are owner";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    // update old
    $du = array();
    $du['is_co_admin'] = "0";
    $this->igparticipantm->updateStatusParticipant($nation_code, $group_id, '0', $b_user_id, "", $du);

    $du = array();
    $du['is_co_admin'] = "0";
    $this->icpm->update($nation_code, $queryResult->i_chat_room_id, $b_user_id, $du);

    $chatRoomList = $this->icrm->getAllByCustom($nation_code, $group_id, $b_user_id);
    foreach($chatRoomList AS $chatRoom){
      $du = array();
      $du['is_co_admin'] = "0";
      $this->icpm->update($nation_code, $chatRoom->id, $b_user_id, $du);
    }

    $getUserData = $this->bu->getById($nation_code, $b_user_id);

    // insert to admin activity log
    $di = array();
    $endDoWhile = 0;
    do {
      $id = $this->GUIDv4();
      $checkId = $this->igaalm->checkId($nation_code, $id);
      if($checkId == 0) {
        $endDoWhile = 1;
      }
    } while ($endDoWhile == 0);

    $di['nation_code'] = $nation_code;
    $di['id'] = $id;
    $di['i_group_id'] = $group_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['type'] = 'band_settings';
    $di['title'] = 'Club Settings';
    $di['text'] = $pelanggan->band_fnama . " delete admin " . $getUserData->band_fnama;
    $di['image'] = $pelanggan->band_image;
    $di['cdate'] = 'NOW()';
    $di['is_active'] = 1;
    $this->igaalm->set($di);
    
    $getManager = $this->igparticipantm->getAllParticipantByGroupId($nation_code, "", "", "", "DESC", "", $group_id, "manager");
    foreach($getManager as &$gm){
      $gm->b_user_band_image = $this->cdn_url($gm->b_user_band_image);
    }

    $data['list_manager'] = $getManager;

    $this->status = 200;
    $this->message = "success";
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function set_onoffline_status()
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

    $get_action = $this->input->get('action');
		if (empty($get_action) || !in_array($get_action, array('yes', 'no'))) {
			$this->status = 1112;
			$this->message = 'Missing or invalid action';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

    if($get_action == "yes") {
      $du = array();
      $du['is_band_online_status'] = "1";
      $this->bu->update($nation_code, $pelanggan->id, $du);

      $dx = array();
      $dx['is_online'] = "1";
      $dx['is_online_status'] = "1";
      $this->igparticipantm->updateStatusParticipant($nation_code, '0', '0', $pelanggan->id, "", $dx);
    } else if($get_action == "no"){
      $du = array();
      $du['is_band_online_status'] = "0";
      $this->bu->update($nation_code, $pelanggan->id, $du);

      // if set to no, change all is_online_status by user id on group participant
      $dx = array();
      $dx['is_online'] = "0";
      $dx['is_online_status'] = "0";
      $this->igparticipantm->updateStatusParticipant($nation_code, '0', '0', $pelanggan->id, "", $dx);
    }

    $this->status = 200;
    $this->message = 'Success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function user_onoffline_status()
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

    $getData = $this->bu->getById($nation_code, $pelanggan->id);
    if($getData->is_band_online_status == "1") {
      $set = "yes";
    } else if($getData->is_band_online_status == "0") {
      $set = "no";
    }
    $data['is_band_online_status'] = $set;
    
    $this->status = 200;
    $this->message = "success";
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function report_group()
  {
    $data = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
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

    $group_id = trim($this->input->get('group_id'));
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
    
    $this->igm->updateTotal($nation_code, $group_id, "report_count", "+", "1");

    $this->status = 200;
    $this->message = "success";
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function set_push_notification()
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

    $get_action = $this->input->get('action');
		if (empty($get_action) || !in_array($get_action, array('yes', 'no'))) {
			$this->status = 1112;
			$this->message = 'Missing or invalid action';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

    $du = array();
    if($get_action == "yes") {
      $du['is_band_push_notif'] = "1";
      $this->bu->update($nation_code, $pelanggan->id, $du);

      $this->status = 1131;
      $this->message = 'Set active club push notification';
    } else if($get_action == "no"){
      $du['is_band_push_notif'] = "0";
      $this->bu->update($nation_code, $pelanggan->id, $du);
      
      $this->status = 1132;
      $this->message = 'Set inactive club push notification';
    }

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function user_push_notif()
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

    $getData = $this->bu->getById($nation_code, $pelanggan->id);
    if($getData->is_band_push_notif == "1") {
      $set = "yes";
    } else if($getData->is_band_push_notif == "0") {
      $set = "no";
    }
    $data['is_band_push_notif'] = $set;
    
    $this->status = 200;
    $this->message = "success";
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function list_customize_each_group_online()
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

    $listGroup = $this->igparticipantm->getByGroupIdParticipantId($nation_code, "0", $pelanggan->id, "all");
    foreach($listGroup as &$group) {
      $groupName = $this->igm->getById($nation_code, $group->group_id);
      $group->name = $groupName->name;
      $group->image = $this->cdn_url($groupName->image_thumb);
      unset($groupName);

      if($group->is_online_status == "1") 
        $group->is_online_status = "yes";
      else if($group->is_online_status == "0") 
        $group->is_online_status = "no";

      unset($group->b_user_band_fnama);
      unset($group->b_user_band_image);
      unset($group->is_owner);
      unset($group->is_co_admin);
      unset($group->is_accept);
      unset($group->is_request);
    }

    $data['list_group'] = $listGroup;

    $this->status = 200;
    $this->message = "success";
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function change_each_group_online_status()
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

    $get_action = $this->input->get('action');
		if (empty($get_action) || !in_array($get_action, array('yes', 'no'))) {
			$this->status = 1112;
			$this->message = 'Missing or invalid action';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
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

    $du = array();

    if($get_action == "yes") {
      $du['is_online'] = "1";
      $du['is_online_status'] = "1";
      $this->igparticipantm->updateStatusParticipant($nation_code, $group_id, '0', $pelanggan->id, "", $du);
    } else if($get_action == "no"){
      $du['is_online'] = "0";
      $du['is_online_status'] = "0";
      $this->igparticipantm->updateStatusParticipant($nation_code, $group_id, '0', $pelanggan->id, "", $du);
    }

    $this->status = 200;
    $this->message = "Success";

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }
}
