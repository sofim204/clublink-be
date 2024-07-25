<?php
class Community extends JI_Controller
{
  public $is_soft_delete=1;
  public $is_log = 1;
  public $imgQueue;

  public function __construct()
  {
    parent::__construct();
    //$this->setTheme('frontx');
    $this->lib("seme_log");
    // $this->lib("seme_curl");
    $this->lib("seme_email");
    $this->lib("seme_purifier");
    $this->load("api_mobile/a_notification_model", "anot");
    $this->load("api_mobile/b_user_model", "bu");
    $this->load("api_mobile/b_user_alamat_model", "bua");
    $this->load("api_mobile/b_user_setting_model", "busm");
    $this->load("api_mobile/c_community_category_model", "cccm");
    $this->load("api_mobile/c_community_model", "ccomm");
    $this->load("api_mobile/c_community_report_model", "ccrm");
    $this->load("api_mobile/c_community_attachment_model", "ccam");
    $this->load("api_mobile/common_code_model", "ccm");
    // $this->load("api_mobile/d_wishlist_model", "dwlm");
    $this->load("api_mobile/d_pemberitahuan_model", "dpem");
    $this->load("api_mobile/c_community_discussion_model", "ccdm");
    $this->load("api_mobile/c_community_discussion_report_model", "ccdrm");
    $this->load("api_mobile/c_community_discussion_attachment_model", "ccdam");

    //by Donny Dennison - 27 agustus 2020
    // add seller data in response
    $this->load("api_mobile/a_negara_model", 'anm');

    // //by Donny Dennison - 2 july 2021 9:37
    // //move-campaign-to-sponsored
    // $this->load("api_mobile/c_promo_model", "cp2");

    $this->load("api_mobile/e_chat_room_model", 'ecrm');
    $this->load("api_mobile/e_chat_participant_model", 'ecpm');
    $this->load("api_mobile/e_chat_model", 'chat');
    $this->load("api_mobile/e_chat_read_model", 'ecreadm');

    //by Donny Dennison - 24 november 2021 9:45
    //add feature highlight community & leaderboard point & hot item
    // $this->load("api_mobile/g_general_location_highlight_status_model", 'gglhsm');
    // $this->load("api_mobile/g_highlight_community_model", "ghcm");

    //by Donny Dennison - 16 december 2021 15:49
    //get point as leaderboard rule
    // $this->load("api_mobile/g_leaderboard_point_area_model", 'glpam');
    $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
    // $this->load("api_mobile/g_leaderboard_ranking_model", 'glrm');
    $this->load("api_mobile/g_leaderboard_point_limit_model", 'glplm');

    //by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account
    $this->load("api_mobile/c_block_model", "cbm");

    //START by Donny Dennison 29 august 2022 14:31
    //new point rule(2 points for Community Share, limit 10 share per day)
    $this->load("api_mobile/c_community_share_history_model", "ccshm");

    $this->load("api_mobile/g_daily_track_record_model", 'gdtrm');
    $this->load("api_mobile/b_user_follow_model", 'buf');
    $this->load("api_mobile/c_community_like_model", "cclm");
    $this->load("api_mobile/c_community_hashtag_list_model", "cchlm");
    $this->load("api_mobile/c_community_hashtag_history_model", "cchhm");
    $this->load("api_mobile/c_community_hashtag_history_for_search_model", "cchhfsm");
    $this->load("api_mobile/c_community_event_new_user_model", "ccenum");
    $this->load("api_mobile/c_community_event_re_targeting_model", "ccertm");

    $this->imgQueue = array();
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

  /**
   * Check uploaded FILE keyname for empty product picture
   * @param  string $keyname  FILES keyname
   * @return bool             return true if file is uploaded and not empty
   */
  // private function __checkUploadedFile($keyname){
  //   if (isset($_FILES[$keyname]['name'])) {
      
  //     //by Donny Dennison - 5 august 2020 1:42
  //     //fix upload image problem product
  //     // if(strlen($_FILES[$keyname]['tmp_name'])>4 && strlen($_FILES[$keyname]['size'])>4){
  //     if(strlen($_FILES[$keyname]['tmp_name'])>4 && strlen($_FILES[$keyname]['size'])>3){

  //       if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/Community::__checkUploadedFile -- INFO keyname: '.$keyname.' RESULT: TRUE');
  //       return true;
  //     }
  //   }
  //   if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/Community::__checkUploadedFile -- INFO keyname: '.$keyname.' RESULT: FALSE');
  //   return false;
  // }

  /**
  * Upload product
  * @param  int      $nation_code    nation code
  * @param  string   $keyname        image key
  * @param  integer  $produk_id      id for c_produk
  * @param  integer  $ke             image counter
  * @return object                   upload result object
  */
  private function __uploadImagex($nation_code, $keyname, $produk_id="0", $ke="")
  {
    $sc = new stdClass();
    $sc->status = 500;
    $sc->message = 'Error';
    $sc->image = '';
    $sc->thumb = '';
    // $produk_id = (int) $produk_id;
    if (isset($_FILES[$keyname]['name'])) {

      //by Donny Dennison - 30 november 2021 14:59
      //comment check size uploaded file
      // if ($_FILES[$keyname]['size']>2000000) {
      //   $sc->status = 301;
      //   $sc->message = 'Image file Size too big, please try again';
      //   return $sc;
      // }

      $filenames = pathinfo($_FILES[$keyname]['name']);
      if (isset($filenames['extension'])) {
        $fileext = strtolower($filenames['extension']);
      } else {
        $fileext = '';
      }

      if (!in_array($fileext, array("jpg","png","jpeg"))) {
        $sc->status = 303;
        $sc->message = 'Invalid file extension, please try other file.';
        return $sc;
      }
      $filename = "$nation_code-$produk_id-$ke";
      $filethumb = $filename.'-thumb';

      $targetdir = $this->media_community;
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

      $sc->status = 998;
      $sc->message = 'Invalid file extension uploaded';
      if (in_array($fileext, array("jpg", "png","jpeg"))) {
        $filecheck = SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename.'.'.$fileext;
        if (file_exists($filecheck)) {
          unlink($filecheck);
          $rand = rand(0, 999);
          $filename = "$nation_code-$produk_id-$ke-".$rand;
          $filecheck = SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename.'.'.$fileext;
          if (file_exists($filecheck)) {
            unlink($filecheck);
            $rand = rand(1000, 99999);
            $filename = "$nation_code-$produk_id-$ke-".$rand;
          }
        }
        $filethumb = $filename."-thumb.".$fileext;
        $filename = $filename.".".$fileext;

        move_uploaded_file($_FILES[$keyname]['tmp_name'], SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
        if (is_file(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename) && file_exists(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)) {
          if (@mime_content_type(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename) == 'image/webp') {
            $sc->status = 302;
            $sc->message = 'WebP image format currently unsupported';
            return $sc;
          }
          if (file_exists(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb) && is_file(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb)) {
            unlink(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
          }
          if (file_exists(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename) && is_file(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)) {

            //START by Donny Dennison - 11 august 2022 10:46
            //fix rotated image after resize(thumb)
            // if (in_array($fileext, array("jpg","jpeg"))) {
            //   $this->correctImageOrientation(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
            // }
            //END by Donny Dennison - 11 august 2022 10:46
            //fix rotated image after resize(thumb)

            $this->lib("wideimage/WideImage", 'wideimage', "inc");
            WideImage::load(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)->reSize(370)->saveToFile(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
            $sc->status = 200;
            $sc->message = 'Success';
            $sc->thumb = str_replace("//", "/", $targetdir.'/'.$filethumb);
            $sc->image = str_replace("//", "/", $targetdir.'/'.$filename);
          } else {
            $sc->status = 997;
            $sc->message = 'Failed';
          }
        } else {
          $sc->status = 999;
          $sc->message = 'Failed';
        }
      } else {
        $sc->status = 998;
        $sc->message = 'Invalid file extension uploaded';
      }
    } else {
      $sc->status = 988;
      $sc->message = 'Keyname file does not exists';
    }
    if ($this->is_log) {
      $this->seme_log->write("api_mobile", 'API_Mobile/Community::__uploadImagex -- INFO KeyName: '.$keyname.' PID:'.$produk_id.' ke:'.$ke.' '.$sc->status.' '.$sc->message.'');
    }
    return $sc;
  }

  private function __moveImagex($nation_code, $url, $produk_id="0", $ke="")
  {
    $sc = new stdClass();
    $sc->status = 500;
    $sc->message = 'Error';
    $sc->image = '';
    $sc->thumb = '';
    // $produk_id = (int) $produk_id;

    $targetdir = $this->media_community;
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
    
    if ($this->is_log) {
      $this->seme_log->write("api_mobile", 'API_Mobile/Community::__moveImagex -- INFO URL: '.$url.' PID:'.$produk_id.' ke:'.$ke.' '.$sc->status.' '.$sc->message.'');
    }
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

  private function __sortCol($sort_col, $tbl_as, $tbl2_as)
  {
    switch ($sort_col) {
      case 'id':
      $sort_col = "$tbl_as.id";
      break;
      case 'title':
      $sort_col = "$tbl_as.title";
      break;

      //by Donny Dennison - 2 march 2021 11:35
      //list-produt-sameStreet-neighborhood-all-from-user-address
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
  private function __pageSize2($page_size)
  {
    if (!is_int($page_size)) {
      $page_size = (int) $page_size;
    }
    switch ($page_size) {
      case 48:
      $page_size= 48;
      break;
      case 24:
      $page_size= 24;
      break;
      case 16:
      $page_size= 16;
      break;
      case 12:
      $page_size= 12;
      break;
      case 2:
      $page_size= 2;
      // no break
      default:
      $page_size = 12;
    }
    return $page_size;
  }

  public function index()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['community_category'] = new stdClass();
    $data['community_total'] = 0;
    $data['communitys'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }
    
    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language
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

    //populate input get

    //by Donny Dennison - 1 desember 2020 16:29
    //list-produt-sameStreet-neighborhood-all-from-user-address
    $type = $this->input->post("type");
    
    if (strlen($type)<=0 || empty($type)){
      $type="";
    }

    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    // $page_size = $this->input->post("page_size");
    $positionSplice = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C15");
    if (!isset($positionSplice->remark)) {
      $positionSplice = new stdClass();
      $positionSplice->remark = 6;
    }
    $page_size = $positionSplice->remark;
    $keyword = trim($this->input->post("keyword"));
    $b_user_id = $this->input->post("b_user_id");
    $timezone = $this->input->post("timezone");

    //START by Donny Dennison - 30 november 2022 16:31
    //new feature, manage group member
    $query_type = $this->input->post("query_type");
    if($query_type != "group_joined" || empty($query_type)){
      $query_type = "normal";
    }
    //END by Donny Dennison - 30 november 2022 16:31
    //new feature, manage group member

    if ($b_user_id<='0') {
      $b_user_id = "";
    }

    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    //sanitize input
    $tbl_as = $this->ccomm->getTblAs();
    $tbl2_as = $this->ccomm->getTbl2As();

    $sort_col = $this->__sortCol($sort_col, $tbl_as, $tbl2_as);
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

    //advanced filter
    $c_community_category_ids = "";
    if (isset($_POST['c_community_category_ids'])) {
      $c_community_category_ids = $_POST['c_community_category_ids'];
    }
    if (strlen($c_community_category_ids)>0) {
      $c_community_category_ids = rtrim($c_community_category_ids, ",");
      $c_community_category_ids = explode(",", $c_community_category_ids);
      if (count($c_community_category_ids)) {
        $kods = array();
        foreach ($c_community_category_ids as &$bki) {
          $bki = (int) trim($bki);
          if ($bki>0) {
            $kods[] = $bki;
          }
        }
        $c_community_category_ids = $kods;
      } else {
        $c_community_category_ids = array();
      }
    } else {
      $c_community_category_ids = array();
    }

    if (isset($pelanggan->id)) {

      $pelangganAddress1 = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
      $pelangganAddress2 = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

    }else{

      $pelangganAddress1 = array();
      $pelangganAddress2 = array();

    }

    //START by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account
    if (isset($pelanggan->id)) {

      $blockDataCommunity = $this->cbm->getAllByUserId($nation_code, 0, $pelanggan->id, "community");
      $blockDataAccount = $this->cbm->getAllByUserId($nation_code, 0, $pelanggan->id, "account");
      $blockDataAccountReverse = $this->cbm->getAllByUserId($nation_code, 0, 0, "account", $pelanggan->id);

    }else{

      $blockDataCommunity = array();
      $blockDataAccount = array();
      $blockDataAccountReverse = array();

    }
    //END by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account

    if(substr($keyword, 0, 1) != "#"){
      //by Donny Dennison - 29 july 2022 13:22
      //new feature, block community post or account
      // $data['community_total'] = $this->ccomm->countAll($nation_code, $keyword, $c_community_category_ids, $type, $pelangganAddress1, $b_user_id);
      // $data['community_total'] = $this->ccomm->countAll($nation_code, $keyword, $c_community_category_ids, $type, $pelangganAddress1, $b_user_id, $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $query_type);

      //by Donny Dennison - 29 july 2022 13:22
      //new feature, block community post or account
      //by Donny Dennison - 15 february 2022 9:50
      //category product and category community have more than 1 language
      // $ddata = $this->ccomm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $c_community_category_ids, $type, $pelangganAddress2, $b_user_id);
      // $data['communitys'] = $this->ccomm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $c_community_category_ids, $type, $pelangganAddress2, $b_user_id, $pelanggan->language_id);
      $data['communitys'] = $this->ccomm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $c_community_category_ids, $type, $pelangganAddress2, $b_user_id, $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $query_type, $pelanggan->language_id);
    }else{
      $data['community_total'] = $this->ccomm->countAllHashtag($nation_code, $keyword, $c_community_category_ids, $type, $pelangganAddress1, $b_user_id, $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $query_type);
      $data['communitys'] = $this->ccomm->getAllHashtag($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $c_community_category_ids, $type, $pelangganAddress2, $b_user_id, $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $query_type, $pelanggan->language_id);
    }

    //manipulating data
    foreach ($data['communitys'] as &$pd) {

      // by muhammad sofi 19 January 2023 | count total likes using k
      $pd->total_likes = $this->thousandsCurrencyFormat($pd->total_likes);
      $pd->total_dislikes = $this->thousandsCurrencyFormat($pd->total_dislikes);

      $pd->can_chat_and_like = "0";

      // if(isset($pelanggan->id) && isset($pelangganAddress2->alamat2)){
      if(isset($pelanggan->id)){

        // }else if($pd->postal_district == $pelangganAddress2->postal_district){

          $pd->can_chat_and_like = "1";
          
        // }

      }

      $pd->is_owner_post = "0";
      $pd->is_follow = '0';
      $pd->is_liked = '0';
      $pd->is_disliked = '0';
      if(isset($pelanggan->id)){
        
        if($pd->b_user_id_starter == $pelanggan->id){
          $pd->is_owner_post = "1";
        }

        if($pd->is_owner_post == "0"){
          $pd->is_follow = $this->buf->checkFollow($nation_code, $pelanggan->id, $pd->b_user_id_starter);
        }

        $checkLike = $this->cclm->getByCustomIdUserId($nation_code, "community", "like", $pd->id, $pelanggan->id);
        if(isset($checkLike->id)){
          $pd->is_liked = '1';
        }

        $checkDislike = $this->cclm->getByCustomIdUserId($nation_code, "community", "dislike", $pd->id, $pelanggan->id);
        if(isset($checkDislike->id)){
          $pd->is_disliked = '1';
        }

      }

      // $pd->cdate_text = $this->humanTiming($pd->cdate);
      $pd->cdate_text = $this->humanTiming($pd->cdate, null, $pelanggan->language_id);

      $pd->cdate = $this->customTimezone($pd->cdate, $timezone);

      //convert to utf friendly
      // if (isset($pd->title)) {
      //   $pd->title = $this->__dconv($pd->title);
      // }
      $pd->title = html_entity_decode($pd->title,ENT_QUOTES);
      $pd->deskripsi = html_entity_decode($pd->deskripsi,ENT_QUOTES);

      if (isset($pd->b_user_image_starter)) {
        if (empty($pd->b_user_image_starter)) {
          $pd->b_user_image_starter = 'media/produk/default.png';
        }
        
        // by Muhammad Sofi - 28 October 2021 11:00
        // if user img & banner not exist or empty, change to default image
        // $pd->b_user_image_starter = $this->cdn_url($pd->b_user_image_starter);
        if(file_exists(SENEROOT.$pd->b_user_image_starter) && $pd->b_user_image_starter != 'media/user/default.png'){
          $pd->b_user_image_starter = $this->cdn_url($pd->b_user_image_starter);
        } else {
          $pd->b_user_image_starter = $this->cdn_url('media/user/default-profile-picture.png');
        }
      }

      if($pd->top_like_image_1 > 0){
        $pd->top_like_image_1 = $this->cdn_url("media/icon/like-62-1-141111.png");
      }

      $pd->images = array();
      $pd->locations = array();
      $pd->videos = array();

      $attachments = $this->ccam->getByCommunityId($nation_code, $pd->id);
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

          $pd->images[] = $atc;

        }else if($atc->jenis == 'video'){

          $atc->url = $this->cdn_url($atc->url);
          $atc->url_thumb = $this->cdn_url($atc->url_thumb);
          $atc->total_views = $this->thousandsCurrencyFormat($atc->total_views);
          
          $pd->videos[] = $atc;

        }else{
          $pd->locations[] = $atc;
        }

      }
      unset($attachments,$atc);

    }

    if(count($c_community_category_ids)>0){

      //by Donny Dennison - 15 february 2022 9:50
      //category product and category community have more than 1 language
      // $data['community_category'] = $this->cccm->getById($nation_code, $c_community_category_ids[0]);
      $data['community_category'] = $this->cccm->getById($nation_code, $c_community_category_ids[0], $pelanggan->language_id);

      if(strlen($data['community_category']->image_icon)<=4) $data['community_category']->image_icon = "media/kategori/default-icon.png";
      if(strlen($data['community_category']->image_cover)<=4) $data['community_category']->image_cover = "media/kategori/default-cover.png";
      if(strlen($data['community_category']->image)<=4) $data['community_category']->image = "media/kategori/default.png";
      $data['community_category']->image_icon = base_url($data['community_category']->image_icon);
      $data['community_category']->image_cover = base_url($data['community_category']->image_cover);
      $data['community_category']->image = base_url($data['community_category']->image);
    }

    // $show_ads = (int) $this->input->post("show_ads");
    // if($show_ads == 1){

    //   $positionSplice = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C15");
    //   if (!isset($positionSplice->remark)) {
    //     $positionSplice = new stdClass();
    //     $positionSplice->remark = 6;
    //   }

    //   $inserted = array("ads");

    //   array_splice($data['communitys'], $positionSplice->remark, 0, $inserted);

    // }

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  }

  public function detail($id)
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['community'] = new stdClass();
    $data['is_blocked'] = '0';

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    // $id = (int) $id;
    if ($id<='0') {
      $this->status = 595;
      $this->message = 'Invalid Community ID';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
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

    $timezone = $this->input->get('timezone');
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language
    // $community = $this->ccomm->getById($nation_code, $id, $pelanggan);
    $community = $this->ccomm->getById($nation_code, $id, $pelanggan, $pelanggan->language_id);
    if (!isset($community->id)) {
      $this->status = 1160;
      $this->message = 'This post is deleted by an author';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    if (isset($pelanggan->id)) {

      $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
      
    }else{

      $pelangganAddress = array();

    }

    $community->can_chat_and_like = "0";

    // if(isset($pelanggan->id) && isset($pelangganAddress->alamat2)){
    if(isset($pelanggan->id)){

      // if($community->postal_district == $pelangganAddress->postal_district){

        $community->can_chat_and_like = "1";
        
      // }

    }

    $community->is_owner_post = "0";
    $community->is_liked = '0';
    $community->is_disliked = '0';
    if(isset($pelanggan->id)){
      
      if($community->b_user_id_starter == $pelanggan->id){
        $community->is_owner_post = "1";
      }

      $checkLike = $this->cclm->getByCustomIdUserId($nation_code, "community", "like", $community->id, $pelanggan->id);
      if(isset($checkLike->id)){
        $community->is_liked = '1';
      }

      $checkDislike = $this->cclm->getByCustomIdUserId($nation_code, "community", "dislike", $community->id, $pelanggan->id);
      if(isset($checkDislike->id)){
        $community->is_disliked = '1';
      }

    }

    $community->is_follow = '0';
    if(isset($pelanggan->id) && $community->is_owner_post == "0"){
      $community->is_follow = $this->buf->checkFollow($nation_code, $pelanggan->id, $community->b_user_id_starter);
    }

    // $community->cdate_text = $this->humanTiming($community->cdate);
    $community->cdate_text = $this->humanTiming($community->cdate, null, $pelanggan->language_id);

    $community->cdate = $this->customTimezone($community->cdate, $timezone);

    if (strlen($community->b_user_image_starter)<=4) {
      $community->b_user_image_starter = 'media/user/default.png';
    }

    // filter utf-8
    if (isset($community->b_user_nama_starter)) {
      $community->b_user_nama_starter = $this->__dconv($community->b_user_nama_starter);
    }

    $community->title = html_entity_decode($community->title,ENT_QUOTES);
    $community->deskripsi = html_entity_decode($community->deskripsi,ENT_QUOTES);

    // if (isset($community->title)) {
    //   $community->title = $this->__dconv($community->title);
    // }
    // if (isset($community->deskripsi)) {
    //   $community->deskripsi = $this->__dconv($community->deskripsi);
    // }

    //cast CDN
    
    // by Muhammad Sofi - 28 October 2021 11:00
    // if user img & banner not exist or empty, change to default image
    // $community->b_user_image_starter = $this->cdn_url($community->b_user_image_starter);
    if(file_exists(SENEROOT.$community->b_user_image_starter) && $community->b_user_image_starter != 'media/user/default.png'){
      $community->b_user_image_starter = $this->cdn_url($community->b_user_image_starter);
    } else {
      $community->b_user_image_starter = $this->cdn_url('media/user/default-profile-picture.png');
    }

    //by muhammad sofi - 19 September 2022 | 10:14
    //add category image(cover)
    if($community->category_image_cover){
      $community->category_image_cover = $this->cdn_url($community->category_image_cover);
    }

    if($community->top_like_image_1 > 0){
      $community->top_like_image_1 = $this->cdn_url("media/icon/like-62-1-141111.png");
    }

    $community->images = array();
    $community->locations = array();
    $community->videos = array();

    $attachments = $this->ccam->getByCommunityId($nation_code, $community->id);
    foreach ($attachments as $atc) {

      if($atc->jenis == 'image'){

        $atc->url = $this->cdn_url($atc->url);
        $atc->url_thumb = $this->cdn_url($atc->url_thumb);
        $community->images[] = $atc;

      }else if($atc->jenis == 'video'){

        $atc->url = $this->cdn_url($atc->url);
        $atc->url_thumb = $this->cdn_url($atc->url_thumb);
        $atc->total_views = $this->thousandsCurrencyFormat($atc->total_views);

        $community->videos[] = $atc;

      }else{
        $community->locations[] = $atc;
      }
      
    }
    unset($attachments);

    $this->status = 200;
    $this->message = 'Success';

    //START by Donny Dennison - 18 november 2022 10:51
    //new feature, block
    if (isset($pelanggan->id)) {

      if($pelanggan->id != $community->b_user_id_starter){

        $blockDataCommunity = $this->cbm->getById($nation_code, 0, $pelanggan->id, "community", $community->id);
        $blockDataAccount = $this->cbm->getById($nation_code, 0, $community->b_user_id_starter, "account", $pelanggan->id);
        $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $community->b_user_id_starter);

        if(isset($blockDataCommunity->block_id) || isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){

          $data['is_blocked'] = '1';

        }

      }

    }
    //END by Donny Dennison - 18 november 2022 10:51
    //new feature, block

    // Start By Muhammad Sofi - 9 November 2021 09:41
    // Remark code

    //by Donny Dennison - 7 august 2020 09:47
    //add discussion data to detail api
    //START by Donny Dennison - 7 august 2020 09:47

    // $tbl_as = $this->ccdm->getTblAs();
    // $tbl2_as = $this->ccdm->getTbl2As();
    // $sort_col = $this->__sortColDiscussion('cdate', $tbl_as, $tbl2_as);
    // $sort_dir = $this->__sortDir('desc');
    // $page = $this->__page(1);
    // $page_size_parent_discussion = $this->__pageSize(2);
    // $page_size_child_discussion = $this->__pageSize(1);

    // $community->diskusi_total = $this->ccdm->countAll($nation_code, 0, $id);

    // $community->diskusis = $this->ccdm->getAll($nation_code, $page, $page_size_parent_discussion, $sort_col, $sort_dir, 0, $id, $pelanggan);

    // foreach ($community->diskusis as $key => $discuss) {

    //   $community->diskusis[$key]->can_chat_and_like = $community->can_chat_and_like;

    //   $community->diskusis[$key]->is_owner_reply = "0";

    //   if(isset($pelanggan->id)){
        
    //     if($community->diskusis[$key]->b_user_id == $pelanggan->id){
    //       $community->diskusis[$key]->is_owner_reply = "1";
    //     }

    //   }

    //   $community->diskusis[$key]->cdate_text = $this->humanTiming($community->diskusis[$key]->cdate);

    //   $community->diskusis[$key]->cdate = $this->customTimezone($community->diskusis[$key]->cdate, $timezone);

    //   // by Muhammad Sofi - 27 October 2021 10:12
    //   // if user img & banner not exist or empty, change to default image
    //   // $community->diskusis[$key]->b_user_image = $this->cdn_url($community->diskusis[$key]->b_user_image);
    //   if(file_exists(SENEROOT.$community->diskusis[$key]->b_user_image) && $community->diskusis[$key]->b_user_image != 'media/user/default.png'){
    //     $community->diskusis[$key]->b_user_image = $this->cdn_url($community->diskusis[$key]->b_user_image);
    //   } else {
    //     $community->diskusis[$key]->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
    //   }
      
    //   if($community->diskusis[$key]->is_liked_image){
    //     $community->diskusis[$key]->is_liked_image = $this->cdn_url($community->diskusis[$key]->is_liked_image);
    //   }

    //   if($community->diskusis[$key]->top_like_image_1){
    //     $community->diskusis[$key]->top_like_image_1 = $this->cdn_url($community->diskusis[$key]->top_like_image_1);
    //   }

    //   if($community->diskusis[$key]->top_like_image_2){
    //     $community->diskusis[$key]->top_like_image_2 = $this->cdn_url($community->diskusis[$key]->top_like_image_2);
    //   }

    //   if($community->diskusis[$key]->top_like_image_3){
    //     $community->diskusis[$key]->top_like_image_3 = $this->cdn_url($community->diskusis[$key]->top_like_image_3);
    //   }

    //   $community->diskusis[$key]->images = new stdClass();
    //   $community->diskusis[$key]->locations = new stdClass();

    //   $attachments = $this->ccdam->getByDiscussionId($nation_code, $discuss->discussion_id);
    //   foreach ($attachments as $atc) {

    //     if($atc->jenis == 'image'){
    //       $atc->url = $this->cdn_url($atc->url);
    //       $atc->url_thumb = $this->cdn_url($atc->url_thumb);
    //       $community->diskusis[$key]->images = $atc;
    //     }else{
    //       $community->diskusis[$key]->locations = $atc;
    //     }
        
    //   }
    //   unset($attachments);

    //   $community->diskusis[$key]->diskusi_anak_total = $this->ccdm->countAll($nation_code,$discuss->discussion_id, $id);
    
    //   $community->diskusis[$key]->diskusi_anak = $this->ccdm->getAll($nation_code, $page, $page_size_child_discussion, $sort_col, 'ASC', $discuss->discussion_id, $id, $pelanggan);

    //   foreach ($community->diskusis[$key]->diskusi_anak as $key_anak => $discuss_anak) {

    //     $community->diskusis[$key]->diskusi_anak[$key_anak]->can_chat_and_like = $community->can_chat_and_like;

    //     $community->diskusis[$key]->diskusi_anak[$key_anak]->is_owner_reply = "0";

    //     if(isset($pelanggan->id)){
          
    //       if($community->diskusis[$key]->diskusi_anak[$key_anak]->b_user_id == $pelanggan->id){
    //         $community->diskusis[$key]->diskusi_anak[$key_anak]->is_owner_reply = "1";
    //       }

    //     }

    //     $community->diskusis[$key]->diskusi_anak[$key_anak]->cdate_text = $this->humanTiming($community->diskusis[$key]->diskusi_anak[$key_anak]->cdate);

    //   $community->diskusis[$key]->diskusi_anak[$key_anak]->cdate = $this->customTimezone($community->diskusis[$key]->diskusi_anak[$key_anak]->cdate, $timezone);

    //     // by Muhammad Sofi - 27 October 2021 10:12
    //     // if user img & banner not exist or empty, change to default image
    //     // $community->diskusis[$key]->diskusi_anak[$key_anak]->b_user_image = $this->cdn_url($community->diskusis[$key]->diskusi_anak[$key_anak]->b_user_image);
    //     if(file_exists(SENEROOT.$community->diskusis[$key]->diskusi_anak[$key_anak]->b_user_image) && $community->diskusis[$key]->diskusi_anak[$key_anak]->b_user_image != 'media/user/default.png'){
    //       $community->diskusis[$key]->diskusi_anak[$key_anak]->b_user_image = $this->cdn_url($community->diskusis[$key]->diskusi_anak[$key_anak]->b_user_image);
    //     } else {
    //       $community->diskusis[$key]->diskusi_anak[$key_anak]->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
    //     }
        
    //     if($community->diskusis[$key]->diskusi_anak[$key_anak]->is_liked_image){
    //       $community->diskusis[$key]->diskusi_anak[$key_anak]->is_liked_image = $this->cdn_url($community->diskusis[$key]->diskusi_anak[$key_anak]->is_liked_image);
    //     }

    //     if($community->diskusis[$key]->diskusi_anak[$key_anak]->top_like_image_1){
    //       $community->diskusis[$key]->diskusi_anak[$key_anak]->top_like_image_1 = $this->cdn_url($community->diskusis[$key]->diskusi_anak[$key_anak]->top_like_image_1);
    //     }

    //     if($community->diskusis[$key]->diskusi_anak[$key_anak]->top_like_image_2){
    //       $community->diskusis[$key]->diskusi_anak[$key_anak]->top_like_image_2 = $this->cdn_url($community->diskusis[$key]->diskusi_anak[$key_anak]->top_like_image_2);
    //     }

    //     if($community->diskusis[$key]->diskusi_anak[$key_anak]->top_like_image_3){
    //       $community->diskusis[$key]->diskusi_anak[$key_anak]->top_like_image_3 = $this->cdn_url($community->diskusis[$key]->diskusi_anak[$key_anak]->top_like_image_3);
    //     }

    //     $community->diskusis[$key]->diskusi_anak[$key_anak]->images = new stdClass();
    //     $community->diskusis[$key]->diskusi_anak[$key_anak]->locations = new stdClass();

    //     $attachments = $this->ccdam->getByDiscussionId($nation_code, $discuss_anak->discussion_id);
    //     foreach ($attachments as $atc) {

    //       if($atc->jenis == 'image'){
    //         $atc->url = $this->cdn_url($atc->url);
    //         $atc->url_thumb = $this->cdn_url($atc->url_thumb);
    //         $community->diskusis[$key]->diskusi_anak[$key_anak]->images = $atc;
    //       }else{
    //         $community->diskusis[$key]->diskusi_anak[$key_anak]->locations = $atc;
    //       }
          
    //     }
    //     unset($attachments);

    //   }

    // }

    //END by Donny Dennison - 7 august 2020 09:47

    // End By Muhammad Sofi - 9 November 2021 09:41

    $data['community'] = $community;

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  }

  //START by Donny Dennison 20 may 2022 17:23
  //new api community/video_list
  public function video_list()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['community_total'] = 0;
    $data['communitys'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
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

    //populate input get
    $type = $this->input->post("type");

    if (strlen($type)<=0 || empty($type)){
      $type="";
    }

    if($type == "sameStreet") {
      $type = "neighborhood";
    }

    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    // $page_size = $this->input->post("page_size");
    $positionSplice = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C15");
    if (!isset($positionSplice->remark)) {
      $positionSplice = new stdClass();
      $positionSplice->remark = 6;
    }
    $page_size = $positionSplice->remark;
    $keyword = trim($this->input->post("keyword"));
    $b_user_id = $this->input->post("b_user_id");
    $timezone = $this->input->post("timezone");
    $watched_video = $this->input->post("watched_video");
    if ($b_user_id<='0') {
      $b_user_id = "";
    }

    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    //sanitize input
    $tbl_as = $this->ccomm->getTblAs();
    $tbl2_as = $this->ccomm->getTbl2As();

    $sort_col = $this->__sortCol("id", $tbl_as, $tbl2_as);
    $sort_dir = $this->__sortDir("DESC");
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

    //advanced filter
    $c_community_category_ids = "";
    if (isset($_POST['c_community_category_ids'])) {
      $c_community_category_ids = $_POST['c_community_category_ids'];
    }
    if (strlen($c_community_category_ids)>0) {
      $c_community_category_ids = rtrim($c_community_category_ids, ",");
      $c_community_category_ids = explode(",", $c_community_category_ids);
      if (count($c_community_category_ids)) {
        $kods = array();
        foreach ($c_community_category_ids as &$bki) {
          $bki = (int) trim($bki);
          if ($bki>0) {
            $kods[] = $bki;
          }
        }
        $c_community_category_ids = $kods;
      } else {
        $c_community_category_ids = array();
      }
    } else {
      $c_community_category_ids = array();
    }

    if (isset($pelanggan->id)) {

      // $pelangganAddress1 = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
      $pelangganAddress2 = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

    }else{

      // $pelangganAddress1 = array();
      $pelangganAddress2 = array();

    }

    //START by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account
    if (isset($pelanggan->id)) {
      $blockDataCommunity = $this->cbm->getAllByUserId($nation_code, 0, $pelanggan->id, "community");
      $blockDataAccount = $this->cbm->getAllByUserId($nation_code, 0, $pelanggan->id, "account");
      $blockDataAccountReverse = $this->cbm->getAllByUserId($nation_code, 0, 0, "account", $pelanggan->id);
    }else{
      $blockDataCommunity = array();
      $blockDataAccount = array();
      $blockDataAccountReverse = array();
  
    }
    //END by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account

    // $data['community_total'] = $this->ccomm->countAllVideo($nation_code, $keyword, $c_community_category_ids, $type, $pelangganAddress1, $b_user_id);
    $data['communitys'] = $this->ccomm->getAllVideoManualQuery($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $c_community_category_ids, $type, $pelangganAddress2, $b_user_id, $pelanggan->language_id, $watched_video, $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse);
    // $data['communitys'] = $this->ccomm->getAllVideoCa($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $c_community_category_ids, $type, $pelangganAddress2, $b_user_id, $pelanggan->language_id, $watched_video, $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse);

    //manipulating data
    foreach ($data['communitys'] as &$pd) {

      // by muhammad sofi 19 January 2023 | count total likes using k
      $pd->total_likes = $this->thousandsCurrencyFormat($pd->total_likes);
      $pd->total_dislikes = $this->thousandsCurrencyFormat($pd->total_dislikes);
      $pd->total_views = $this->thousandsCurrencyFormat($pd->total_views);

      // $pd->can_chat_and_like = "0";

      // // if(isset($pelanggan->id) && isset($pelangganAddress2->alamat2)){
      // if(isset($pelanggan->id)){

      //   // }else if($pd->postal_district == $pelangganAddress2->postal_district){

      //     $pd->can_chat_and_like = "1";
          
      //   // }

      // }

      // }

      $pd->is_owner_post = "0";
      $pd->is_follow = '0';
      $pd->is_liked = '0';
      $pd->is_disliked = '0';

      if(isset($pelanggan->id)){
        
        if($pd->b_user_id_starter == $pelanggan->id){
          $pd->is_owner_post = "1";
        }

        if($pd->is_owner_post == "0"){
          $pd->is_follow = $this->buf->checkFollow($nation_code, $pelanggan->id, $pd->b_user_id_starter);
        }

        $checkLike = $this->cclm->getByCustomIdUserId($nation_code, "community", "like", $pd->id, $pelanggan->id);
        if(isset($checkLike->id)){
          $pd->is_liked = '1';
        }

        $checkDislike = $this->cclm->getByCustomIdUserId($nation_code, "community", "dislike", $pd->id, $pelanggan->id);
        if(isset($checkDislike->id)){
          $pd->is_disliked = '1';
        }

      }

      // $pd->cdate_text = $this->humanTiming($pd->cdate);
      $pd->cdate_text = $this->humanTiming($pd->cdate, null, $pelanggan->language_id);

      $pd->cdate = $this->customTimezone($pd->cdate, $timezone);

      //convert to utf friendly
      // if (isset($pd->title)) {
      //   $pd->title = $this->__dconv($pd->title);
      // }
      $pd->title = html_entity_decode($pd->title,ENT_QUOTES);
      $pd->deskripsi = html_entity_decode($pd->deskripsi,ENT_QUOTES);

      if (isset($pd->b_user_image_starter)) {
        if (empty($pd->b_user_image_starter)) {
          $pd->b_user_image_starter = 'media/produk/default.png';
        }
        
        // by Muhammad Sofi - 28 October 2021 11:00
        // if user img & banner not exist or empty, change to default image
        // $pd->b_user_image_starter = $this->cdn_url($pd->b_user_image_starter);
        if(file_exists(SENEROOT.$pd->b_user_image_starter) && $pd->b_user_image_starter != 'media/user/default.png'){
          $pd->b_user_image_starter = $this->cdn_url($pd->b_user_image_starter);
        } else {
          $pd->b_user_image_starter = $this->cdn_url('media/user/default-profile-picture.png');
        }
      }

      // if($pd->top_like_image_1){
      //   $pd->top_like_image_1 = $this->cdn_url($pd->top_like_image_1);
      // }

      // if($pd->top_like_image_2){
      //   $pd->top_like_image_2 = $this->cdn_url($pd->top_like_image_2);
      // }

      // if($pd->top_like_image_3){
      //   $pd->top_like_image_3 = $this->cdn_url($pd->top_like_image_3);
      // }

      $pd->url = $this->cdn_url($pd->url);
      $pd->url_thumb = $this->cdn_url($pd->url_thumb);

      $pd->locations = array();
      $attachments = $this->ccam->getByCommunityId($nation_code, $pd->id, "all", "location");
      foreach ($attachments as $atc) {
        $pd->locations[] = $atc;
      }
      unset($attachments,$atc);
    }

    // $show_ads = (int) $this->input->post("show_ads");
    // if($show_ads == 1){

    //   $positionSplice = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C15");
    //   if (!isset($positionSplice->remark)) {
    //     $positionSplice = new stdClass();
    //     $positionSplice->remark = 6;
    //   }

    //   $inserted = array("ads");

    //   array_splice($data['communitys'], $positionSplice->remark, 0, $inserted);

    // }

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  }
  //END by Donny Dennison 20 may 2022 17:23

  public function video_listv2()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['start_position'] = "0";
    $data['page'] = "1";
    $data['community_total'] = 0;
    $data['communitys'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
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

    //populate input get
    $type = $this->input->post("type");

    if (strlen($type)<=0 || empty($type)){
      $type="";
    }

    if($type == "sameStreet") {
      $type = "neighborhood";
    }

    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    // $page_size = $this->input->post("page_size");
    $positionSplice = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C15");
    if (!isset($positionSplice->remark)) {
      $positionSplice = new stdClass();
      $positionSplice->remark = 6;
    }
    $page_size = $positionSplice->remark;
    $keyword = trim($this->input->post("keyword"));
    $b_user_id = $this->input->post("b_user_id");
    $timezone = $this->input->post("timezone");
    $watched_video = $this->input->post("watched_video");
    $start_position = $this->input->post("start_position");
    if($start_position == 0 || $start_position == ""){
      $start_position = rand(1,49000);
    }

    $checkLimit = $start_position + ($page * $page_size);
    if($checkLimit >= 49800){
      $start_position = rand(1,49000);
      $page = 1;
    }

    if ($b_user_id<='0') {
      $b_user_id = "";
    }

    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    //sanitize input
    $tbl_as = $this->ccomm->getTblAs();
    $tbl2_as = $this->ccomm->getTbl2As();

    $sort_col = $this->__sortCol("id", $tbl_as, $tbl2_as);
    $sort_dir = $this->__sortDir("DESC");
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

    //advanced filter
    $c_community_category_ids = "";
    if (isset($_POST['c_community_category_ids'])) {
      $c_community_category_ids = $_POST['c_community_category_ids'];
    }
    if (strlen($c_community_category_ids)>0) {
      $c_community_category_ids = rtrim($c_community_category_ids, ",");
      $c_community_category_ids = explode(",", $c_community_category_ids);
      if (count($c_community_category_ids)) {
        $kods = array();
        foreach ($c_community_category_ids as &$bki) {
          $bki = (int) trim($bki);
          if ($bki>0) {
            $kods[] = $bki;
          }
        }
        $c_community_category_ids = $kods;
      } else {
        $c_community_category_ids = array();
      }
    } else {
      $c_community_category_ids = array();
    }

    if (isset($pelanggan->id)) {

      // $pelangganAddress1 = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
      $pelangganAddress2 = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

    }else{

      // $pelangganAddress1 = array();
      $pelangganAddress2 = array();

    }

    //START by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account
    if (isset($pelanggan->id)) {
      $blockDataCommunity = $this->cbm->getAllByUserId($nation_code, 0, $pelanggan->id, "community");
      $blockDataAccount = $this->cbm->getAllByUserId($nation_code, 0, $pelanggan->id, "account");
      $blockDataAccountReverse = $this->cbm->getAllByUserId($nation_code, 0, 0, "account", $pelanggan->id);
    }else{
      $blockDataCommunity = array();
      $blockDataAccount = array();
      $blockDataAccountReverse = array();
  
    }
    //END by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account

    // $data['community_total'] = $this->ccomm->countAllVideo($nation_code, $keyword, $c_community_category_ids, $type, $pelangganAddress1, $b_user_id);
    $data['communitys'] = $this->ccomm->getAllVideoManualQueryV2($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $c_community_category_ids, $type, $pelangganAddress2, $b_user_id, $pelanggan->language_id, $watched_video, $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $start_position);
    // $data['communitys'] = $this->ccomm->getAllVideoCa($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $c_community_category_ids, $type, $pelangganAddress2, $b_user_id, $pelanggan->language_id, $watched_video, $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse);

    //manipulating data
    foreach ($data['communitys'] as &$pd) {

      // by muhammad sofi 19 January 2023 | count total likes using k
      $pd->total_likes = $this->thousandsCurrencyFormat($pd->total_likes);
      $pd->total_dislikes = $this->thousandsCurrencyFormat($pd->total_dislikes);
      $pd->total_views = $this->thousandsCurrencyFormat($pd->total_views);

      // $pd->can_chat_and_like = "0";

      // // if(isset($pelanggan->id) && isset($pelangganAddress2->alamat2)){
      // if(isset($pelanggan->id)){

      //   // }else if($pd->postal_district == $pelangganAddress2->postal_district){

      //     $pd->can_chat_and_like = "1";
          
      //   // }

      // }

      // }

      $pd->is_owner_post = "0";
      $pd->is_follow = '0';
      $pd->is_liked = '0';
      $pd->is_disliked = '0';

      if(isset($pelanggan->id)){
        
        if($pd->b_user_id_starter == $pelanggan->id){
          $pd->is_owner_post = "1";
        }

        if($pd->is_owner_post == "0"){
          $pd->is_follow = $this->buf->checkFollow($nation_code, $pelanggan->id, $pd->b_user_id_starter);
        }

        $checkLike = $this->cclm->getByCustomIdUserId($nation_code, "community", "like", $pd->id, $pelanggan->id);
        if(isset($checkLike->id)){
          $pd->is_liked = '1';
        }

        $checkDislike = $this->cclm->getByCustomIdUserId($nation_code, "community", "dislike", $pd->id, $pelanggan->id);
        if(isset($checkDislike->id)){
          $pd->is_disliked = '1';
        }

      }

      // $pd->cdate_text = $this->humanTiming($pd->cdate);
      $pd->cdate_text = $this->humanTiming($pd->cdate, null, $pelanggan->language_id);

      $pd->cdate = $this->customTimezone($pd->cdate, $timezone);

      //convert to utf friendly
      // if (isset($pd->title)) {
      //   $pd->title = $this->__dconv($pd->title);
      // }
      $pd->title = html_entity_decode($pd->title,ENT_QUOTES);
      $pd->deskripsi = html_entity_decode($pd->deskripsi,ENT_QUOTES);

      if (isset($pd->b_user_image_starter)) {
        if (empty($pd->b_user_image_starter)) {
          $pd->b_user_image_starter = 'media/produk/default.png';
        }
        
        // by Muhammad Sofi - 28 October 2021 11:00
        // if user img & banner not exist or empty, change to default image
        // $pd->b_user_image_starter = $this->cdn_url($pd->b_user_image_starter);
        if(file_exists(SENEROOT.$pd->b_user_image_starter) && $pd->b_user_image_starter != 'media/user/default.png'){
          $pd->b_user_image_starter = $this->cdn_url($pd->b_user_image_starter);
        } else {
          $pd->b_user_image_starter = $this->cdn_url('media/user/default-profile-picture.png');
        }
      }

      // if($pd->top_like_image_1){
      //   $pd->top_like_image_1 = $this->cdn_url($pd->top_like_image_1);
      // }

      // if($pd->top_like_image_2){
      //   $pd->top_like_image_2 = $this->cdn_url($pd->top_like_image_2);
      // }

      // if($pd->top_like_image_3){
      //   $pd->top_like_image_3 = $this->cdn_url($pd->top_like_image_3);
      // }

      $pd->url = $this->cdn_url($pd->url);
      $pd->url_thumb = $this->cdn_url($pd->url_thumb);

      $pd->locations = array();
      $attachments = $this->ccam->getByCommunityId($nation_code, $pd->id, "all", "location");
      foreach ($attachments as $atc) {
        $pd->locations[] = $atc;
      }
      unset($attachments,$atc);
    }

    // $show_ads = (int) $this->input->post("show_ads");
    // if($show_ads == 1){

    //   $positionSplice = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C15");
    //   if (!isset($positionSplice->remark)) {
    //     $positionSplice = new stdClass();
    //     $positionSplice->remark = 6;
    //   }

    //   $inserted = array("ads");

    //   array_splice($data['communitys'], $positionSplice->remark, 0, $inserted);

    // }

    $data['start_position'] = $start_position;
    $data['page'] = $page;

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  }

  public function baru()
  {
    //initial
    $dt = $this->__init();
    //error_reporting(0);

    $this->seme_log->write("api_mobile", "Community::baru -> ".json_encode($_POST));

    //default result
    $data = array();
    $data['community'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    // $this->__userUnconfirmedDenied($nation_code, $pelanggan);

    $timezone = $this->input->get('timezone');
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $this->status = 300;
    $this->message = 'Missing one or more parameters';

    //collect input
    // $b_user_alamat_id = (int) $this->input->post('b_user_alamat_id'); // by Muhammad Sofi - 10 November 2021 09:19
    $c_community_category_id = (int) $this->input->post('c_community_category_id');
    $title = trim($this->input->post('title'));
    $deskripsi = trim($this->input->post('deskripsi'));
    $location_json = $this->input->post("location_json");

    //START by Donny Dennison - 23 november 2022 13:42
    //new feature, manage group member
    $group_chat_type = trim($this->input->post('group_chat_type'));
    if($group_chat_type != "private"){
      $group_chat_type = "public";
    }
    //END by Donny Dennison - 23 november 2022 13:42
    //new feature, manage group member

    // by Muhammad Sofi - 10 November 2021 09:19
    //validating user address
    // if ($b_user_alamat_id<=0) {
    //   $b_user_alamat_id = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
    //   if(!isset($b_user_alamat_id->id)){
    //     $this->status = 1100;
    //     $this->message = 'Please add address';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
    //     die();

    //   }else{
    //     $b_user_alamat_id = $b_user_alamat_id->id;
    //   }
    // }

    $kat = $this->cccm->getById($nation_code, $c_community_category_id);
    if (!isset($kat->id)) {
      $this->status = 1100;
      $this->message = 'Please choose Community category';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //by Donny Dennison 24 february 2021 18:45
    //change ’ to ' in add & edit product name and description
    $title = str_replace('’',"'",$title);
    $deskripsi = str_replace('’',"'",$deskripsi);

    // $title = filter_var($title, FILTER_SANITIZE_STRING);
    // $deskripsi = filter_var($deskripsi, FILTER_SANITIZE_STRING);
    $deskripsi = nl2br($deskripsi);

    //by Donny Dennison - 15 augustus 2020 15:09
    //bug fix \n (enter) didnt get remove
    $deskripsi = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $deskripsi);

    $title = str_replace("\\n", "<br />", $title);
    $deskripsi = str_replace("\\n", "<br />", $deskripsi);

    //validation
    if (strlen($title)<3) {
      $this->status = 1104;
      $this->message = 'Title is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    if (strlen($deskripsi)<3) {
      $this->status = 1105;
      $this->message = 'Description is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    // validation max size video
    // if (!empty($this->input->post('video'))) {
    //   $max_size = 102400;
    //   for ($i = 1; $i < 11; $i++) {
    //     if ($_FILES['video']['size'][$i] > $max_size) {
    //       $this->status = 1105;
    //       $this->message = 'Video too large, max size ' . $max_size / 10 . ' Mb';
    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
    //       die();
    //     }
    //   }
    // }

    // by Muhammad Sofi - 10 November 2021 09:19
    // get detail address
    $d_address = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
    if (!isset($d_address->id)) {
      $this->status = 916;
      $this->message = 'Please register your address first';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $duplicateCommunity = $this->ccomm->getActiveByUserIdTitleCategoryDescription5Minutes($nation_code, $pelanggan->id, $title, $c_community_category_id, $deskripsi);
    if (!empty($duplicateCommunity)) {
      $this->status = 1113;
      $this->message = 'Your community post has already been registered';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //start transaction and lock table
    $this->ccomm->trans_start();

    //get last id for first time
    // $com_id = $this->ccomm->getLastId($nation_code);

    //initial insert with latest ID
    $di = array();
    $di['nation_code'] = $nation_code;
    // $di['id'] = $com_id;
    $di['b_user_id'] = $pelanggan->id;
    // $di['b_user_alamat_id'] = $b_user_alamat_id; // by Muhammad Sofi - 10 November 2021 09:19
    $di['alamat2'] = $d_address->alamat2;
    $di['kelurahan'] = $d_address->kelurahan;
    $di['kecamatan'] = $d_address->kecamatan;
    $di['kabkota'] = $d_address->kabkota;
    $di['provinsi'] = $d_address->provinsi;
    $di['negara'] = $d_address->negara;
    $di['kodepos'] = $d_address->kodepos;
    $di['c_community_category_id'] = $c_community_category_id;
    $di['group_chat_type'] = $group_chat_type;
    $di['title'] = $title;
    $di['deskripsi'] = $deskripsi;
    $di['cdate'] = 'NOW()';

    $endDoWhile = 0;
    do{
      $com_id = $this->GUIDv4();
      $checkId = $this->ccomm->checkId($nation_code, $com_id);
      if($checkId == 0){
        $endDoWhile = 1;
      }
    }while($endDoWhile == 0);
    $di['id'] = $com_id;

    // $is_double_spt = "0";
    // //credit : https://stackoverflow.com/a/50285475
    // preg_match_all('/(?<!\w)#\w+/', $deskripsi, $allMatches);
    // $allMatches = array_values(array_unique($allMatches[0]));
    // if($allMatches){
    //   foreach($allMatches AS $hashtag){
    //     $checkHistoryHashtag = $this->cchhm->getByUseridHashtag($nation_code, $pelanggan->id, $hashtag, date("Y-m-d"));
    //     if(!isset($checkHistoryHashtag->hashtag)){
    //       $insideSelectedHashtag = $this->cchlm->getByHashtag($nation_code, $hashtag);
    //       if(isset($insideSelectedHashtag->id)){
    //         $is_double_spt = "1";
    //       }
    //     }

    //     $dataInsertHistory = array();
    //     $dataInsertHistory['nation_code'] = $nation_code;
    //     $endDoWhile = 0;
    //     do{
    //       $historyHastagId = $this->GUIDv4();
    //       $checkId = $this->cchhm->checkId($nation_code, $historyHastagId);
    //       if($checkId == 0){
    //         $endDoWhile = 1;
    //       }
    //     }while($endDoWhile == 0);
    //     $dataInsertHistory['id'] = $historyHastagId;
    //     $dataInsertHistory['c_community_id'] = $com_id;
    //     $dataInsertHistory['b_user_id'] = $pelanggan->id;
    //     $dataInsertHistory['hashtag'] = $hashtag;
    //     $dataInsertHistory['is_double_spt'] = $is_double_spt;
    //     $dataInsertHistory['cdate'] = "NOW()";
    //     $this->cchhm->set($dataInsertHistory);
    //   }
    // }
    // $di['is_double_spt'] = $is_double_spt;

    $res = $this->ccomm->set($di);
    if (!$res) {
      $this->ccomm->trans_rollback();
      $this->ccomm->trans_end();
      $this->status = 1107;
      $this->message = "Error while posting community, please try again later";
      $this->seme_log->write("api_mobile", 'API_Mobile/Community::baru -- RollBack -- forceClose '.$this->status.' '.$this->message);
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $this->gdtrm->updateTotalData(DATE("Y-m-d"), "community_post", "+", "1");

    // $this->ccomm->trans_commit();
    $this->status = 200;
    $this->message = "Success";
    $this->message = 'Your community post Has Been Posted';
    $this->seme_log->write("api_mobile", 'API_Mobile/Community::baru -- INFO '.$this->status.' '.$this->message);

    //doing image & location upload if success
    if ($res) {

      $checkFileExist = 1;
      $checkFileTemporaryOrNot = 1;
      $listUploadImage = array();
      //looping for get list of image
      for ($i=1; $i < 11; $i++) {

        $file_path = parse_url($this->input->post('foto'.$i), PHP_URL_PATH);

        if($this->input->post('foto'.$i) != null){

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
        $this->status = 995;
        $this->message = 'Failed upload, temporary already gone';
        $this->ccomm->trans_rollback();
        $this->ccomm->trans_end();
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
        die();
      }

      if ($checkFileTemporaryOrNot == 0) {
        $this->status = 996;
        $this->message = 'Failed upload, upload is not temporary';
        $this->ccomm->trans_rollback();
        $this->ccomm->trans_end();
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
        die();
      }
      
      if(!empty($listUploadImage)){
      
        //upload image and insert to c_community_attachment table
        foreach ($listUploadImage as $key => $upload) {
          
          $photoId_last = $this->ccam->getLastId($nation_code,$com_id, 'image');

          // $sc = $this->__uploadImagex($nation_code, $upload, $com_id, $photoId_last);
          $sc = $this->__moveImagex($nation_code, $upload, $com_id, $photoId_last);
          if (isset($sc->status)) {
            if ($sc->status==200) {
                
              $dix = array();
              $dix['nation_code'] = $nation_code;
              $dix['c_community_id'] = $com_id;
              $dix['id'] = $photoId_last;
              $dix['jenis'] = 'image';
              $dix['url'] = $sc->image;
              $dix['url_thumb'] = $sc->thumb;
              $this->ccam->set($dix);

              // $this->ccomm->trans_commit();
                
            }
          }

        }

      }

      if(is_array($location_json)){

        if(count($location_json) > 0){

          //upload location and insert to c_community_attachment table
          foreach ($location_json as $key => $upload) {
            
            if(isset($upload['location_nama']) && isset($upload['location_address']) && isset($upload['location_place_id']) && isset($upload['location_latitude']) && isset($upload['location_longitude'])){

              if($upload['location_nama'] && $upload['location_address'] && $upload['location_place_id'] && $upload['location_latitude'] && $upload['location_longitude']){

                $locationId_last = $this->ccam->getLastId($nation_code,$com_id, 'location');

                $dix = array();
                $dix['nation_code'] = $nation_code;
                $dix['c_community_id'] = $com_id;
                $dix['id'] = $locationId_last;
                $dix['jenis'] = 'location';
                $dix['location_nama'] = $upload['location_nama'];
                $dix['location_address'] = $upload['location_address'];
                $dix['location_place_id'] = $upload['location_place_id'];
                $dix['location_latitude'] = $upload['location_latitude'];
                $dix['location_longitude'] = $upload['location_longitude'];
                $this->ccam->set($dix);

                // $this->ccomm->trans_commit();        
              }

            }

          }

        }
      
      }

      $insertVideo = 0;

      //looping for get list of image
      for ($i=1; $i < 6; $i++) {

        if($this->input->post('video'.$i) === "yes"){
          $insertVideo++;
        }

      }

      // check if exist foto dont push to db video data
      if (count($listUploadImage) < 1) {
        if($insertVideo > 0){
      
          //insert to c_produk_foto table
          for ($i=1; $i <= $insertVideo; $i++) {
            
            $cpfm_last = $this->ccam->getLastId($nation_code,$com_id, "video");
            
            $upi = $this->__moveImagex($nation_code, $this->input->post("video".$i."_thumb"), $com_id, $cpfm_last);
  
            $dix = array();
            $dix['nation_code'] = $nation_code;
            $dix['c_community_id'] = $com_id;
            $dix['id'] = $cpfm_last;
            $dix['jenis'] = 'video';
            $dix['convert_status'] = 'uploading';
            if($upi->status == 200){
              $dix['url'] = $upi->image;
              $dix['url_thumb'] = $upi->thumb;
            }else{
              $dix['url'] = $this->media_community_video."default.png";
              $dix['url_thumb'] = $this->media_community_video."default.png";
            }
            $this->ccam->set($dix);
            // $this->ccomm->trans_commit();
  
          }
  
          $this->gdtrm->updateTotalData(DATE("Y-m-d"), "community_video", "+", "1");
        }
      }

      $kategori = $this->cccm->getById($nation_code,$c_community_category_id);

      // $chat_room_id = $this->ecrm->getLastId($nation_code);

      $endDoWhile = 0;
      do{
        $chat_room_id = $this->GUIDv4();
        $checkId = $this->ecrm->checkId($nation_code, $chat_room_id);
        if($checkId == 0){
          $endDoWhile = 1;
        }
      }while($endDoWhile == 0);

      //insert room chat
      $di = array();
      $di['id'] = $chat_room_id;
      $di['nation_code'] = $nation_code;
      $di['b_user_id_starter'] = $pelanggan->id;
      $di['c_community_id'] = $com_id;
      $di['custom_name_1'] = $title;
      $di['custom_name_2'] = $kategori->nama;
      $di['cdate'] = 'NOW()';
      $di['chat_type'] = 'community';
      $di['group_chat_type'] = $group_chat_type;

      $createChatRoom = $this->ecrm->set($di);
      if(!$createChatRoom){
        $this->ccomm->trans_rollback();
        $this->ccomm->trans_end();
        $this->status = 1107;
        $this->message = "Error while posting community, please try again later";
        $this->seme_log->write("api_mobile", 'API_Mobile/Community::baru -- RollBack in create chat -- forceClose '.$this->status.' '.$this->message);
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
        die();
      }

      usleep(500000);
      // by Muhammad Sofi - 5 November 2021 13:38
      // remove subquery get chat_room_id, add new column e_chat_room_id in community
      $di = array();
      $di['e_chat_room_id'] = $chat_room_id;
      $this->ccomm->update($nation_code, $com_id, $di);

      //START by Donny Dennison - 30 november 2022 16:31
      //new feature, manage group member
      if($c_community_category_id == 1 || $c_community_category_id == 24){

        //insert chat participant
        $di = array();
        $di['nation_code'] = $nation_code;
        $di['e_chat_room_id'] = $chat_room_id;
        $di['b_user_id'] = $pelanggan->id;
        $di['cdate'] = 'NOW()';
        $di['is_read'] = 1;
        $this->ecpm->set($di);

        $this->ccomm->updateTotal($nation_code, $com_id, "total_people_group_chat", '+', 1);

        //create announcement
        $type = 'chat';
        $replacer = array();

        $replacer['user_nama'] = html_entity_decode($pelanggan->fnama,ENT_QUOTES);
        $message = '';
        $message_indonesia = '';

        $nw = $this->anot->get($nation_code, "push", $type, 2, 1);
        if (isset($nw->message)) {
          $message = $this->__nRep($nw->message, $replacer);
        }

        $nw = $this->anot->get($nation_code, "push", $type, 2, 2);
        if (isset($nw->message)) {
          $message_indonesia = $this->__nRep($nw->message, $replacer);
        }

        //get last chat id
        $chat_id = $this->chat->getLastId($nation_code, $chat_room_id);

        $di = array();
        $di['id'] = $chat_id;
        $di['nation_code'] = $nation_code;
        $di['e_chat_room_id'] = $chat_room_id;
        $di['b_user_id'] = 0;
        $di['type'] = 'announcement';
        $di['message'] = $message;
        $di['message_indonesia'] = $message_indonesia;
        $di['cdate'] = date('Y-m-d H:i:s', strtotime('+ 1 second'));
        $this->chat->set($di);

      }
      //END by Donny Dennison - 30 november 2022 16:31
      //new feature, manage group member

      //START by Donny Dennison - 24 november 2021 9:45
      //add feature highlight community & leaderboard point & hot item
      // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", "All", "All");

      // if(!isset($getStatusHighlight->status)){

      //   //get last id
      //   $highlight_status_id = $this->gglhsm->getLastId($nation_code);

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_status_id;
      //   $di['b_user_alamat_location_kelurahan'] = 'All';
      //   $di['b_user_alamat_location_kecamatan'] = 'All';
      //   $di['b_user_alamat_location_kabkota'] = 'All';
      //   $di['b_user_alamat_location_provinsi'] = 'All';
      //   $this->gglhsm->set($di);

      //   $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", "All", "All");
      // }

      // if($getStatusHighlight->status == 'automatic'){

      //   $this->ghcm->updatePriority($nation_code, "All", "All", "All", 'All', '+', 1);

      //   //get last id
      //   $highlight_id = $this->ghcm->getLastId($nation_code, "All", "All", "All", 'All');

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_id;
      //   $di['c_community_id'] = $com_id;
      //   $di['b_user_alamat_location_kelurahan'] = 'All';
      //   $di['b_user_alamat_location_kecamatan'] = 'All';
      //   $di['b_user_alamat_location_kabkota'] = 'All';
      //   $di['b_user_alamat_location_provinsi'] = 'All';
      //   $di['start_date'] = date('Y-m-d');
      //   $di['end_date'] = '9999-12-31';
      //   $di['priority'] = 1;
      //   $this->ghcm->set($di);

      // }

      // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", "All", $d_address->provinsi);

      // if(!isset($getStatusHighlight->status)){

      //   //get last id
      //   $highlight_status_id = $this->gglhsm->getLastId($nation_code);

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_status_id;
      //   $di['b_user_alamat_location_kelurahan'] = 'All';
      //   $di['b_user_alamat_location_kecamatan'] = 'All';
      //   $di['b_user_alamat_location_kabkota'] = 'All';
      //   $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //   $this->gglhsm->set($di);

      //   $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", "All", $d_address->provinsi);
      // }

      // if($getStatusHighlight->status == 'automatic'){

      //   $this->ghcm->updatePriority($nation_code, "All", "All", "All", $d_address->provinsi, '+', 1);

      //   //get last id
      //   $highlight_id = $this->ghcm->getLastId($nation_code, "All", "All", "All", $d_address->provinsi);

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_id;
      //   $di['c_community_id'] = $com_id;
      //   $di['b_user_alamat_location_kelurahan'] = 'All';
      //   $di['b_user_alamat_location_kecamatan'] = 'All';
      //   $di['b_user_alamat_location_kabkota'] = 'All';
      //   $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //   $di['start_date'] = date('Y-m-d');
      //   $di['end_date'] = '9999-12-31';
      //   $di['priority'] = 1;
      //   $this->ghcm->set($di);

      // }

      // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", $d_address->kabkota, $d_address->provinsi);
      
      // if(!isset($getStatusHighlight->status)){

      //   //get last id
      //   $highlight_status_id = $this->gglhsm->getLastId($nation_code);

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_status_id;
      //   $di['b_user_alamat_location_kelurahan'] = 'All';
      //   $di['b_user_alamat_location_kecamatan'] = 'All';
      //   $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
      //   $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //   $this->gglhsm->set($di);

      //   $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", $d_address->kabkota, $d_address->provinsi);
      // }

      // if($getStatusHighlight->status == 'automatic'){

      //   $this->ghcm->updatePriority($nation_code, "All", "All", $d_address->kabkota, $d_address->provinsi, '+', 1);

      //   //get last id
      //   $highlight_id = $this->ghcm->getLastId($nation_code, "All", "All", $d_address->kabkota, $d_address->provinsi);

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_id;
      //   $di['c_community_id'] = $com_id;
      //   $di['b_user_alamat_location_kelurahan'] = 'All';
      //   $di['b_user_alamat_location_kecamatan'] = 'All';
      //   $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
      //   $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //   $di['start_date'] = date('Y-m-d');
      //   $di['end_date'] = '9999-12-31';
      //   $di['priority'] = 1;
      //   $this->ghcm->set($di);

      // }

      // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi);

      // if(!isset($getStatusHighlight->status)){

      //   //get last id
      //   $highlight_status_id = $this->gglhsm->getLastId($nation_code);

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_status_id;
      //   $di['b_user_alamat_location_kelurahan'] = 'All';
      //   $di['b_user_alamat_location_kecamatan'] = $d_address->kecamatan;
      //   $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
      //   $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //   $this->gglhsm->set($di);

      //   $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi);
      // }

      // if($getStatusHighlight->status == 'automatic'){

      //   $this->ghcm->updatePriority($nation_code, "All", $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi, '+', 1);

      //   //get last id
      //   $highlight_id = $this->ghcm->getLastId($nation_code, "All", $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi);

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_id;
      //   $di['c_community_id'] = $com_id;
      //   $di['b_user_alamat_location_kelurahan'] = 'All';
      //   $di['b_user_alamat_location_kecamatan'] = $d_address->kecamatan;
      //   $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
      //   $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //   $di['start_date'] = date('Y-m-d');
      //   $di['end_date'] = '9999-12-31';
      //   $di['priority'] = 1;
      //   $this->ghcm->set($di);

      // }

      // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, $d_address->kelurahan, $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi);
      
      // if(!isset($getStatusHighlight->status)){

      //   //get last id
      //   $highlight_status_id = $this->gglhsm->getLastId($nation_code);

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_status_id;
      //   $di['b_user_alamat_location_kelurahan'] = $d_address->kelurahan;
      //   $di['b_user_alamat_location_kecamatan'] = $d_address->kecamatan;
      //   $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
      //   $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //   $this->gglhsm->set($di);

      //   $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, $d_address->kelurahan, $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi);
      // }

      // if($getStatusHighlight->status == 'automatic'){

      //   // $totalHighlight = $this->ghcm->countAllByLocation($nation_code, $d_address->kelurahan, $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi);

      //   // if($totalHighlight >= 10){

      //   //   $overHighlight = $totalHighlight - 9;

      //   //   $this->ghcm->updateByPriorityDesc($nation_code, $d_address->kelurahan, $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi, $overHighlight);

      //   // }

      //   $this->ghcm->updatePriority($nation_code, $d_address->kelurahan, $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi, '+' , 1);

      //   //get last id
      //   $highlight_id = $this->ghcm->getLastId($nation_code, $d_address->kelurahan, $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi);

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_id;
      //   $di['c_community_id'] = $com_id;
      //   $di['b_user_alamat_location_kelurahan'] = $d_address->kelurahan;
      //   $di['b_user_alamat_location_kecamatan'] = $d_address->kecamatan;
      //   $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
      //   $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //   $di['start_date'] = date('Y-m-d');
      //   $di['end_date'] = '9999-12-31';
      //   $di['priority'] = 1;
      //   $this->ghcm->set($di);

      // }
      //END by Donny Dennison - 24 november 2021 9:45

      //START by Donny Dennison - 16 december 2021 15:49
      //get point as leaderboard rule

      //get total community post
      // $totalPostNow = $this->ccomm->countAllByUserId($nation_code, $pelanggan->id);

      // if($totalPostNow == 1){

      //   //get point
      //   $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EF");
      //   if (!isset($pointGet->remark)) {
      //     $pointGet = new stdClass();
      //     $pointGet->remark = 100;
      //   }

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['b_user_alamat_location_kelurahan'] = $d_address->kelurahan;
      //   $di['b_user_alamat_location_kecamatan'] = $d_address->kecamatan;
      //   $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
      //   $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //   $di['b_user_id'] = $pelanggan->id;
      //   $di['point'] = $pointGet->remark;
      //   $di['custom_id'] = $com_id;
      //   $di['custom_type'] = 'community';
      //   $di['custom_type_sub'] = 'post';
      //   $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
      //   $this->glphm->set($di);
      //   // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '+', $di['point']);
      // }else{
        //get total post
        $totalPost = $this->glphm->countAll($nation_code, "", "", "", "", $pelanggan->id, "+", "", "community", "post", date("Y-m-d"), "");

        $limitPost = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EE");
        if (!isset($limitPost->remark)) {
          $limitPost = new stdClass();
          $limitPost->remark = 5;
        }

        if($totalPost < $limitPost->remark){
          $postPointCode = "EG";
          $imagePointCode = "E13";
          $post_custom_type_sub = "post";
          $image_custom_type_sub = "upload image";
          // if($is_double_spt == "1"){
          //   $postPointCode = "E14";
          //   $imagePointCode = "E15";
          //   $post_custom_type_sub = "post(double point)";
          //   $image_custom_type_sub = "upload image(double point)";
          // }

          //get point
          $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $postPointCode);
          if (!isset($pointGet->remark)) {
            $pointGet = new stdClass();
            $pointGet->remark = 10;
          }

          $di = array();
          $di['nation_code'] = $nation_code;
          $di['b_user_alamat_location_kelurahan'] = $d_address->kelurahan;
          $di['b_user_alamat_location_kecamatan'] = $d_address->kecamatan;
          $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
          $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
          $di['b_user_id'] = $pelanggan->id;
          $di['point'] = $pointGet->remark;
          $di['custom_id'] = $com_id;
          $di['custom_type'] = 'community';
          $di['custom_type_sub'] = $post_custom_type_sub;
          $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
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

          if (count($listUploadImage) >= 1) {
            //get point
            $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $imagePointCode);
            if (!isset($pointGet->remark)) {
              $pointGet = new stdClass();
              $pointGet->remark = 14;
            }

            $di = array();
            $di['nation_code'] = $nation_code;
            $di['b_user_alamat_location_kelurahan'] = $d_address->kelurahan;
            $di['b_user_alamat_location_kecamatan'] = $d_address->kecamatan;
            $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
            $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
            $di['b_user_id'] = $pelanggan->id;
            $di['point'] = $pointGet->remark;
            $di['custom_id'] = $com_id;
            $di['custom_type'] = 'community';
            $di['custom_type_sub'] = $image_custom_type_sub;
            $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
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
      // }
      // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_post', '+', 1);
      //END by Donny Dennison - 16 december 2021 15:49

      $this->ccomm->trans_commit();
      //end transaction
      $this->ccomm->trans_end();
    }

    // $url = base_url("api_mobile/community/detail/$com_id/?apikey=$apikey&nation_code=$nation_code&apisess=$apisess");
    // $res = $this->seme_curl->get($url);
    // $body = json_decode($res->body);
    // $data = $body->data;

    $community = $this->ccomm->getById($nation_code, $com_id, $pelanggan);

    if (isset($pelanggan->id)) {
      $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
    }else{
      $pelangganAddress = array();
    }

    $community->can_chat_and_like = "0";
    // if(isset($pelanggan->id) && isset($pelangganAddress->alamat2)){
    if(isset($pelanggan->id)){
      // if($community->postal_district == $pelangganAddress->postal_district){
        $community->can_chat_and_like = "1";
      // }
    }

    $community->is_owner_post = "0";
    $community->is_liked = '0';
    $community->is_disliked = '0';
    if(isset($pelanggan->id)){
      if($community->b_user_id_starter == $pelanggan->id){
        $community->is_owner_post = "1";
      }

      $checkLike = $this->cclm->getByCustomIdUserId($nation_code, "community", "like", $community->id, $pelanggan->id);
      if(isset($checkLike->id)){
        $community->is_liked = '1';
      }

      $checkDislike = $this->cclm->getByCustomIdUserId($nation_code, "community", "dislike", $community->id, $pelanggan->id);
      if(isset($checkDislike->id)){
        $community->is_disliked = '1';
      }
    }

    // $community->cdate_text = $this->humanTiming($community->cdate);
    $community->cdate_text = $this->humanTiming($community->cdate, null, $pelanggan->language_id);
    $community->cdate = $this->customTimezone($community->cdate, $timezone);

    if (strlen($community->b_user_image_starter)<=4) {
      $community->b_user_image_starter = 'media/user/default.png';
    }

    // filter utf-8
    if (isset($community->b_user_nama_starter)) {
      $community->b_user_nama_starter = $this->__dconv($community->b_user_nama_starter);
    }

    $community->title = html_entity_decode($community->title,ENT_QUOTES);
    $community->deskripsi = html_entity_decode($community->deskripsi,ENT_QUOTES);

    if(file_exists(SENEROOT.$community->b_user_image_starter) && $community->b_user_image_starter != 'media/user/default.png'){
      $community->b_user_image_starter = $this->cdn_url($community->b_user_image_starter);
    } else {
      $community->b_user_image_starter = $this->cdn_url('media/user/default-profile-picture.png');
    }

    if($community->top_like_image_1 > 0){
      $community->top_like_image_1 = $this->cdn_url("media/icon/like-62-1-141111.png");
    }

    $community->images = array();
    $community->locations = array();
    $community->videos = array();
    $attachments = $this->ccam->getByCommunityId($nation_code, $community->id);
    foreach ($attachments as $atc) {
      if($atc->jenis == 'image'){
        $atc->url = $this->cdn_url($atc->url);
        $atc->url_thumb = $this->cdn_url($atc->url_thumb);
        $community->images[] = $atc;
      }else if($atc->jenis == 'video'){
        $atc->url = $this->cdn_url($atc->url);
        $atc->url_thumb = $this->cdn_url($atc->url_thumb);
        $community->videos[] = $atc;
      }else{
        $community->locations[] = $atc;
      }
    }
    unset($attachments);

    $data['community'] = $community;

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  }

  public function baruv2()
  {
    //initial
    $dt = $this->__init();
    //error_reporting(0);

    $this->seme_log->write("api_mobile", "Community::baruv2 -> ".json_encode($_POST));

    //default result
    $data = array();
    $data['community'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    // $this->__userUnconfirmedDenied($nation_code, $pelanggan);

    $timezone = $this->input->get('timezone');
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $this->status = 300;
    $this->message = 'Missing one or more parameters';

    //collect input
    // $b_user_alamat_id = (int) $this->input->post('b_user_alamat_id'); // by Muhammad Sofi - 10 November 2021 09:19
    $c_community_category_id = (int) $this->input->post('c_community_category_id');
    $title = trim($this->input->post('title'));
    $deskripsi = trim($this->input->post('deskripsi'));
    $location_json = $this->input->post("location_json");

    //START by Donny Dennison - 23 november 2022 13:42
    //new feature, manage group member
    $group_chat_type = trim($this->input->post('group_chat_type'));
    if($group_chat_type != "private"){
      $group_chat_type = "public";
    }
    //END by Donny Dennison - 23 november 2022 13:42
    //new feature, manage group member

    // by Muhammad Sofi - 10 November 2021 09:19
    //validating user address
    // if ($b_user_alamat_id<=0) {
    //   $b_user_alamat_id = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
    //   if(!isset($b_user_alamat_id->id)){
    //     $this->status = 1100;
    //     $this->message = 'Please add address';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
    //     die();

    //   }else{
    //     $b_user_alamat_id = $b_user_alamat_id->id;
    //   }
    // }

    $kat = $this->cccm->getById($nation_code, $c_community_category_id);
    if (!isset($kat->id)) {
      $this->status = 1100;
      $this->message = 'Please choose Community category';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //by Donny Dennison 24 february 2021 18:45
    //change ’ to ' in add & edit product name and description
    $title = str_replace('’',"'",$title);
    $deskripsi = str_replace('’',"'",$deskripsi);

    // $title = filter_var($title, FILTER_SANITIZE_STRING);
    // $deskripsi = filter_var($deskripsi, FILTER_SANITIZE_STRING);
    $deskripsi = nl2br($deskripsi);

    //by Donny Dennison - 15 augustus 2020 15:09
    //bug fix \n (enter) didnt get remove
    $deskripsi = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $deskripsi);

    $title = str_replace("\\n", "<br />", $title);
    $deskripsi = str_replace("\\n", "<br />", $deskripsi);
    $deskripsi = str_replace(" #", "#", $deskripsi);
    $deskripsi = str_replace("#", " #", $deskripsi);

    //validation
    if (strlen($title)<3) {
      $this->status = 1104;
      $this->message = 'Title is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    if (strlen($deskripsi)<3) {
      $this->status = 1105;
      $this->message = 'Description is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    // validation max size video
    // if (!empty($this->input->post('video'))) {
    //   $max_size = 102400;
    //   for ($i = 1; $i < 11; $i++) {
    //     if ($_FILES['video']['size'][$i] > $max_size) {
    //       $this->status = 1105;
    //       $this->message = 'Video too large, max size ' . $max_size / 10 . ' Mb';
    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
    //       die();
    //     }
    //   }
    // }

    // by Muhammad Sofi - 10 November 2021 09:19
    // get detail address
    $d_address = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
    if (!isset($d_address->id)) {
      $this->status = 916;
      $this->message = 'Please register your address first';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $duplicateCommunity = $this->ccomm->getActiveByUserIdTitleCategoryDescription5Minutes($nation_code, $pelanggan->id, $title, $c_community_category_id, $deskripsi);
    if (!empty($duplicateCommunity)) {
      $this->status = 1113;
      $this->message = 'Your community post has already been registered';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $getDoubleSpt = 0;
    $is_double_spt = "0";
    $getEventNewUser = 0;
    $getEventRetargeting = 0;
    $hashtagEventFound = "";
    $hashtags = array();
    //credit : https://stackoverflow.com/a/50285475
    preg_match_all('/(?<!\w)#\w+/', $deskripsi, $allMatches);
    $allMatches = array_values(array_unique($allMatches[0]));
    if($allMatches){
      foreach($allMatches AS $hashtag){
        if (date("Y-m-d") >= "2023-10-01" && date("Y-m-d") <= "2023-10-31"){
          $checkHistoryHashtag = $this->cchhm->getByUseridHashtag($nation_code, $pelanggan->id, $hashtag, "1", date("Y-m-d"));
          if(!isset($checkHistoryHashtag->hashtag)){
            $insideSelectedHashtag = $this->cchlm->getByHashtag($nation_code, $hashtag);
            if(isset($insideSelectedHashtag->id)){
              $getDoubleSpt++;
              if($getDoubleSpt == 1){
                $is_double_spt = "1";
              }
            }
          }
        }

        $hashtags[] = array(
          "hashtag" => $hashtag,
          "is_double_spt" => $is_double_spt
        );
        $is_double_spt = "0";

        if (date("Y-m-d") >= "2023-11-06" && date("Y-m-d") <= "2023-11-30" && in_array(strtolower($hashtag), array("#penggunabarusellon", "#sellonmissions", "#informasisekitar"))){
          $getEventNewUser ++;
          $hashtagEventFound = strtolower($hashtag);
        }

        if (date("Y-m-d") >= "2023-10-16" && date("Y-m-d") <= "2023-10-31" && in_array(strtolower($hashtag), array("#penggunasetia", "#dailymissions", "#sellonkomunitas"))){
          $getEventRetargeting ++;
          $hashtagEventFound = strtolower($hashtag);
        }
      }
    }

    if($getDoubleSpt >= 1 && ($getEventNewUser >= 1 || $getEventRetargeting >= 1)){
      $getDoubleSpt = 0;
    }

    //start transaction and lock table
    $this->ccomm->trans_start();

    //initial insert with latest ID
    $di = array();
    $di['nation_code'] = $nation_code;
    $di['b_user_id'] = $pelanggan->id;
    // $di['b_user_alamat_id'] = $b_user_alamat_id; // by Muhammad Sofi - 10 November 2021 09:19
    $di['alamat2'] = $d_address->alamat2;
    $di['kelurahan'] = $d_address->kelurahan;
    $di['kecamatan'] = $d_address->kecamatan;
    $di['kabkota'] = $d_address->kabkota;
    $di['provinsi'] = $d_address->provinsi;
    $di['negara'] = $d_address->negara;
    $di['kodepos'] = $d_address->kodepos;
    $di['c_community_category_id'] = $c_community_category_id;
    $di['group_chat_type'] = $group_chat_type;
    $di['title'] = $title;
    $di['deskripsi'] = $deskripsi;
    $di['cdate'] = 'NOW()';
    $endDoWhile = 0;
    do{
      $com_id = $this->GUIDv4();
      $checkId = $this->ccomm->checkId($nation_code, $com_id);
      if($checkId == 0){
        $endDoWhile = 1;
      }
    }while($endDoWhile == 0);
    $di['id'] = $com_id;
    $di['is_double_spt'] = ($getDoubleSpt == 1) ? "1":"0";
    if($hashtags){
      foreach($hashtags AS $hashtag){
        $dataInsertHistory = array();
        $dataInsertHistory['nation_code'] = $nation_code;
        $endDoWhile = 0;
        do{
          $historyHastagId = $this->GUIDv4();
          $checkId = $this->cchhm->checkId($nation_code, $historyHastagId);
          if($checkId == 0){
            $endDoWhile = 1;
          }
        }while($endDoWhile == 0);
        $dataInsertHistory['id'] = $historyHastagId;
        $dataInsertHistory['c_community_id'] = $com_id;
        $dataInsertHistory['b_user_id'] = $pelanggan->id;
        $dataInsertHistory['hashtag'] = $hashtag["hashtag"];
        $dataInsertHistory['is_double_spt'] = ($getDoubleSpt == 1) ? $hashtag["is_double_spt"] :"0";
        $dataInsertHistory['cdate'] = "NOW()";
        $this->cchhm->set($dataInsertHistory);
        unset($dataInsertHistory['is_double_spt']);
        $endDoWhile = 0;
        do{
          $historyHastagId = $this->GUIDv4();
          $checkId = $this->cchhfsm->checkId($nation_code, $historyHastagId);
          if($checkId == 0){
            $endDoWhile = 1;
          }
        }while($endDoWhile == 0);
        $dataInsertHistory['id'] = $historyHastagId;
        $this->cchhfsm->set($dataInsertHistory);
      }
    }

    $res = $this->ccomm->set($di);
    if (!$res) {
      $this->ccomm->trans_rollback();
      $this->ccomm->trans_end();
      $this->status = 1107;
      $this->message = "Error while posting community, please try again later";
      $this->seme_log->write("api_mobile", 'API_Mobile/Community::baru -- RollBack -- forceClose '.$this->status.' '.$this->message);
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $this->gdtrm->updateTotalData(DATE("Y-m-d"), "community_post", "+", "1");

    // $this->ccomm->trans_commit();
    $this->status = 200;
    $this->message = "Success";
    $this->message = 'Your community post Has Been Posted';
    $this->seme_log->write("api_mobile", 'API_Mobile/Community::baru -- INFO '.$this->status.' '.$this->message);

    //doing image & location upload if success
    if ($res) {
      $checkFileExist = 1;
      $checkFileTemporaryOrNot = 1;
      $listUploadImage = array();
      //looping for get list of image
      for ($i=1; $i < 11; $i++) {
        $file_path = parse_url($this->input->post('foto'.$i), PHP_URL_PATH);
        if($this->input->post('foto'.$i) != null){
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
        $this->status = 995;
        $this->message = 'Failed upload, temporary already gone';
        $this->ccomm->trans_rollback();
        $this->ccomm->trans_end();
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
        die();
      }

      if ($checkFileTemporaryOrNot == 0) {
        $this->status = 996;
        $this->message = 'Failed upload, upload is not temporary';
        $this->ccomm->trans_rollback();
        $this->ccomm->trans_end();
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
        die();
      }
      
      if(!empty($listUploadImage)){
        //upload image and insert to c_community_attachment table
        foreach ($listUploadImage as $key => $upload) {
          $photoId_last = $this->ccam->getLastId($nation_code,$com_id, 'image');
          // $sc = $this->__uploadImagex($nation_code, $upload, $com_id, $photoId_last);
          $sc = $this->__moveImagex($nation_code, $upload, $com_id, $photoId_last);
          if (isset($sc->status)) {
            if ($sc->status==200) {
              $dix = array();
              $dix['nation_code'] = $nation_code;
              $dix['c_community_id'] = $com_id;
              $dix['id'] = $photoId_last;
              $dix['jenis'] = 'image';
              $dix['url'] = $sc->image;
              $dix['url_thumb'] = $sc->thumb;
              $this->ccam->set($dix);
              // $this->ccomm->trans_commit();
            }
          }
        }
      }

      if(is_array($location_json)){
        if(count($location_json) > 0){
          //upload location and insert to c_community_attachment table
          foreach ($location_json as $key => $upload) {
            if(isset($upload['location_nama']) && isset($upload['location_address']) && isset($upload['location_place_id']) && isset($upload['location_latitude']) && isset($upload['location_longitude'])){
              if($upload['location_nama'] && $upload['location_address'] && $upload['location_place_id'] && $upload['location_latitude'] && $upload['location_longitude']){
                $locationId_last = $this->ccam->getLastId($nation_code,$com_id, 'location');
                $dix = array();
                $dix['nation_code'] = $nation_code;
                $dix['c_community_id'] = $com_id;
                $dix['id'] = $locationId_last;
                $dix['jenis'] = 'location';
                $dix['location_nama'] = $upload['location_nama'];
                $dix['location_address'] = $upload['location_address'];
                $dix['location_place_id'] = $upload['location_place_id'];
                $dix['location_latitude'] = $upload['location_latitude'];
                $dix['location_longitude'] = $upload['location_longitude'];
                $this->ccam->set($dix);
                // $this->ccomm->trans_commit();        
              }
            }
          }
        }
      }

      $insertVideo = 0;
      for ($i=1; $i < 6; $i++) {
        if($this->input->post('video'.$i) === "yes"){
          $insertVideo++;
        }
      }

      $checkFileExist = 1;
      $checkFileTemporaryOrNot = 1;
      $listUploadVideo = array();
      for ($i=1; $i < 6; $i++) {
        $file_path = parse_url($this->input->post('video_new_flow_'.$i), PHP_URL_PATH);
        if($this->input->post('video_new_flow_'.$i) != null){
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
        $this->ccomm->trans_rollback();
        $this->ccomm->trans_end();
        $this->status = 995;
        $this->message = 'Failed upload, temporary already gone';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }

      if ($checkFileTemporaryOrNot == 0) {
        $this->ccomm->trans_rollback();
        $this->ccomm->trans_end();
        $this->status = 996;
        $this->message = 'Failed upload, upload is not temporary';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }

      $listUploadVideoThumb = array();
      for ($i=1; $i < 6; $i++) {
        $file_path = parse_url($this->input->post('video_new_flow_'.$i.'_thumb'), PHP_URL_PATH);
        if($this->input->post('video_new_flow_'.$i.'_thumb') != null){
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
        $this->ccomm->trans_rollback();
        $this->ccomm->trans_end();
        $this->status = 995;
        $this->message = 'Failed upload, temporary already gone';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }

      if ($checkFileTemporaryOrNot == 0) {
        $this->ccomm->trans_rollback();
        $this->ccomm->trans_end();
        $this->status = 996;
        $this->message = 'Failed upload, upload is not temporary';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }

      $upload_video_new_version = 0;
      // check if exist foto dont push to db video data
      if (count($listUploadImage) < 1) {
        if(!empty($listUploadVideo)){
          foreach ($listUploadVideo as $key => $upload) {
            $cpfm_last = $this->ccam->getLastId($nation_code,$com_id, "video");
            $moveVideo = $this->__moveVideox($nation_code, $upload, $this->media_community_video, $com_id, $cpfm_last);
            $sc = $this->__moveImagex($nation_code, $listUploadVideoThumb[$key], $com_id, $cpfm_last);
            if (isset($moveVideo->status) && isset($sc->status)) {
              if ($moveVideo->status==200 && $sc->status==200) {
                $dix = array();
                $dix['nation_code'] = $nation_code;
                $dix['c_community_id'] = $com_id;
                $dix['id'] = $cpfm_last;
                $dix['jenis'] = 'video';
                $dix['convert_status'] = 'waiting';
                $dix['url'] = $moveVideo->url;
                $dix['url_thumb'] = $sc->thumb;
                $this->ccam->set($dix);
                $upload_video_new_version = 1;
              }
            }
          }
          $this->gdtrm->updateTotalData(DATE("Y-m-d"), "community_video", "+", "1");
        }else if($insertVideo > 0){
          for ($i=1; $i <= $insertVideo; $i++) {
            $cpfm_last = $this->ccam->getLastId($nation_code,$com_id, "video");
            $upi = $this->__moveImagex($nation_code, $this->input->post("video".$i."_thumb"), $com_id, $cpfm_last);
            $dix = array();
            $dix['nation_code'] = $nation_code;
            $dix['c_community_id'] = $com_id;
            $dix['id'] = $cpfm_last;
            $dix['jenis'] = 'video';
            $dix['convert_status'] = 'uploading';
            if($upi->status == 200){
              $dix['url'] = $upi->image;
              $dix['url_thumb'] = $upi->thumb;
            }else{
              $dix['url'] = $this->media_community_video."default.png";
              $dix['url_thumb'] = $this->media_community_video."default.png";
            }
            $this->ccam->set($dix);
            // $this->ccomm->trans_commit();
          }
          $this->gdtrm->updateTotalData(DATE("Y-m-d"), "community_video", "+", "1");
        }

      }

      $kategori = $this->cccm->getById($nation_code,$c_community_category_id);

      // $chat_room_id = $this->ecrm->getLastId($nation_code);
      $endDoWhile = 0;
      do{
        $chat_room_id = $this->GUIDv4();
        $checkId = $this->ecrm->checkId($nation_code, $chat_room_id);
        if($checkId == 0){
          $endDoWhile = 1;
        }
      }while($endDoWhile == 0);
      //insert room chat
      $di = array();
      $di['id'] = $chat_room_id;
      $di['nation_code'] = $nation_code;
      $di['b_user_id_starter'] = $pelanggan->id;
      $di['c_community_id'] = $com_id;
      $di['custom_name_1'] = $title;
      $di['custom_name_2'] = $kategori->nama;
      $di['cdate'] = 'NOW()';
      $di['chat_type'] = 'community';
      $di['group_chat_type'] = $group_chat_type;
      $createChatRoom = $this->ecrm->set($di);
      if(!$createChatRoom){
        $this->ccomm->trans_rollback();
        $this->ccomm->trans_end();
        $this->status = 1107;
        $this->message = "Error while posting community, please try again later";
        $this->seme_log->write("api_mobile", 'API_Mobile/Community::baru -- RollBack in create chat -- forceClose '.$this->status.' '.$this->message);
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
        die();
      }

      usleep(500000);
      // by Muhammad Sofi - 5 November 2021 13:38
      // remove subquery get chat_room_id, add new column e_chat_room_id in community
      $di = array();
      $di['e_chat_room_id'] = $chat_room_id;
      $this->ccomm->update($nation_code, $com_id, $di);

      //START by Donny Dennison - 30 november 2022 16:31
      //new feature, manage group member
      if($c_community_category_id == 1 || $c_community_category_id == 24){
        //insert chat participant
        $di = array();
        $di['nation_code'] = $nation_code;
        $di['e_chat_room_id'] = $chat_room_id;
        $di['b_user_id'] = $pelanggan->id;
        $di['cdate'] = 'NOW()';
        $di['is_read'] = 1;
        $this->ecpm->set($di);

        $this->ccomm->updateTotal($nation_code, $com_id, "total_people_group_chat", '+', 1);

        //create announcement
        $type = 'chat';
        $replacer = array();

        $replacer['user_nama'] = html_entity_decode($pelanggan->fnama,ENT_QUOTES);
        $message = '';
        $message_indonesia = '';

        $nw = $this->anot->get($nation_code, "push", $type, 2, 1);
        if (isset($nw->message)) {
          $message = $this->__nRep($nw->message, $replacer);
        }

        $nw = $this->anot->get($nation_code, "push", $type, 2, 2);
        if (isset($nw->message)) {
          $message_indonesia = $this->__nRep($nw->message, $replacer);
        }

        //get last chat id
        $chat_id = $this->chat->getLastId($nation_code, $chat_room_id);

        $di = array();
        $di['id'] = $chat_id;
        $di['nation_code'] = $nation_code;
        $di['e_chat_room_id'] = $chat_room_id;
        $di['b_user_id'] = 0;
        $di['type'] = 'announcement';
        $di['message'] = $message;
        $di['message_indonesia'] = $message_indonesia;
        $di['cdate'] = date('Y-m-d H:i:s', strtotime('+ 1 second'));
        $this->chat->set($di);

      }
      //END by Donny Dennison - 30 november 2022 16:31
      //new feature, manage group member

      //START by Donny Dennison - 24 november 2021 9:45
      //add feature highlight community & leaderboard point & hot item
      // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", "All", "All");

      // if(!isset($getStatusHighlight->status)){

      //   //get last id
      //   $highlight_status_id = $this->gglhsm->getLastId($nation_code);

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_status_id;
      //   $di['b_user_alamat_location_kelurahan'] = 'All';
      //   $di['b_user_alamat_location_kecamatan'] = 'All';
      //   $di['b_user_alamat_location_kabkota'] = 'All';
      //   $di['b_user_alamat_location_provinsi'] = 'All';
      //   $this->gglhsm->set($di);

      //   $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", "All", "All");
      // }

      // if($getStatusHighlight->status == 'automatic'){

      //   $this->ghcm->updatePriority($nation_code, "All", "All", "All", 'All', '+', 1);

      //   //get last id
      //   $highlight_id = $this->ghcm->getLastId($nation_code, "All", "All", "All", 'All');

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_id;
      //   $di['c_community_id'] = $com_id;
      //   $di['b_user_alamat_location_kelurahan'] = 'All';
      //   $di['b_user_alamat_location_kecamatan'] = 'All';
      //   $di['b_user_alamat_location_kabkota'] = 'All';
      //   $di['b_user_alamat_location_provinsi'] = 'All';
      //   $di['start_date'] = date('Y-m-d');
      //   $di['end_date'] = '9999-12-31';
      //   $di['priority'] = 1;
      //   $this->ghcm->set($di);

      // }

      // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", "All", $d_address->provinsi);

      // if(!isset($getStatusHighlight->status)){

      //   //get last id
      //   $highlight_status_id = $this->gglhsm->getLastId($nation_code);

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_status_id;
      //   $di['b_user_alamat_location_kelurahan'] = 'All';
      //   $di['b_user_alamat_location_kecamatan'] = 'All';
      //   $di['b_user_alamat_location_kabkota'] = 'All';
      //   $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //   $this->gglhsm->set($di);

      //   $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", "All", $d_address->provinsi);
      // }

      // if($getStatusHighlight->status == 'automatic'){

      //   $this->ghcm->updatePriority($nation_code, "All", "All", "All", $d_address->provinsi, '+', 1);

      //   //get last id
      //   $highlight_id = $this->ghcm->getLastId($nation_code, "All", "All", "All", $d_address->provinsi);

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_id;
      //   $di['c_community_id'] = $com_id;
      //   $di['b_user_alamat_location_kelurahan'] = 'All';
      //   $di['b_user_alamat_location_kecamatan'] = 'All';
      //   $di['b_user_alamat_location_kabkota'] = 'All';
      //   $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //   $di['start_date'] = date('Y-m-d');
      //   $di['end_date'] = '9999-12-31';
      //   $di['priority'] = 1;
      //   $this->ghcm->set($di);

      // }

      // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", $d_address->kabkota, $d_address->provinsi);
      
      // if(!isset($getStatusHighlight->status)){

      //   //get last id
      //   $highlight_status_id = $this->gglhsm->getLastId($nation_code);

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_status_id;
      //   $di['b_user_alamat_location_kelurahan'] = 'All';
      //   $di['b_user_alamat_location_kecamatan'] = 'All';
      //   $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
      //   $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //   $this->gglhsm->set($di);

      //   $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", $d_address->kabkota, $d_address->provinsi);
      // }

      // if($getStatusHighlight->status == 'automatic'){

      //   $this->ghcm->updatePriority($nation_code, "All", "All", $d_address->kabkota, $d_address->provinsi, '+', 1);

      //   //get last id
      //   $highlight_id = $this->ghcm->getLastId($nation_code, "All", "All", $d_address->kabkota, $d_address->provinsi);

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_id;
      //   $di['c_community_id'] = $com_id;
      //   $di['b_user_alamat_location_kelurahan'] = 'All';
      //   $di['b_user_alamat_location_kecamatan'] = 'All';
      //   $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
      //   $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //   $di['start_date'] = date('Y-m-d');
      //   $di['end_date'] = '9999-12-31';
      //   $di['priority'] = 1;
      //   $this->ghcm->set($di);

      // }

      // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi);

      // if(!isset($getStatusHighlight->status)){

      //   //get last id
      //   $highlight_status_id = $this->gglhsm->getLastId($nation_code);

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_status_id;
      //   $di['b_user_alamat_location_kelurahan'] = 'All';
      //   $di['b_user_alamat_location_kecamatan'] = $d_address->kecamatan;
      //   $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
      //   $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //   $this->gglhsm->set($di);

      //   $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi);
      // }

      // if($getStatusHighlight->status == 'automatic'){

      //   $this->ghcm->updatePriority($nation_code, "All", $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi, '+', 1);

      //   //get last id
      //   $highlight_id = $this->ghcm->getLastId($nation_code, "All", $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi);

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_id;
      //   $di['c_community_id'] = $com_id;
      //   $di['b_user_alamat_location_kelurahan'] = 'All';
      //   $di['b_user_alamat_location_kecamatan'] = $d_address->kecamatan;
      //   $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
      //   $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //   $di['start_date'] = date('Y-m-d');
      //   $di['end_date'] = '9999-12-31';
      //   $di['priority'] = 1;
      //   $this->ghcm->set($di);

      // }

      // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, $d_address->kelurahan, $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi);
      
      // if(!isset($getStatusHighlight->status)){

      //   //get last id
      //   $highlight_status_id = $this->gglhsm->getLastId($nation_code);

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_status_id;
      //   $di['b_user_alamat_location_kelurahan'] = $d_address->kelurahan;
      //   $di['b_user_alamat_location_kecamatan'] = $d_address->kecamatan;
      //   $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
      //   $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //   $this->gglhsm->set($di);

      //   $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, $d_address->kelurahan, $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi);
      // }

      // if($getStatusHighlight->status == 'automatic'){

      //   // $totalHighlight = $this->ghcm->countAllByLocation($nation_code, $d_address->kelurahan, $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi);

      //   // if($totalHighlight >= 10){

      //   //   $overHighlight = $totalHighlight - 9;

      //   //   $this->ghcm->updateByPriorityDesc($nation_code, $d_address->kelurahan, $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi, $overHighlight);

      //   // }

      //   $this->ghcm->updatePriority($nation_code, $d_address->kelurahan, $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi, '+' , 1);

      //   //get last id
      //   $highlight_id = $this->ghcm->getLastId($nation_code, $d_address->kelurahan, $d_address->kecamatan, $d_address->kabkota, $d_address->provinsi);

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['id'] = $highlight_id;
      //   $di['c_community_id'] = $com_id;
      //   $di['b_user_alamat_location_kelurahan'] = $d_address->kelurahan;
      //   $di['b_user_alamat_location_kecamatan'] = $d_address->kecamatan;
      //   $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
      //   $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //   $di['start_date'] = date('Y-m-d');
      //   $di['end_date'] = '9999-12-31';
      //   $di['priority'] = 1;
      //   $this->ghcm->set($di);

      // }
      //END by Donny Dennison - 24 november 2021 9:45

      //START by Donny Dennison - 16 december 2021 15:49
      //get point as leaderboard rule

      //get total community post
      // $totalPostNow = $this->ccomm->countAllByUserId($nation_code, $pelanggan->id);

      // if($totalPostNow == 1){

      //   //get point
      //   $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EF");
      //   if (!isset($pointGet->remark)) {
      //     $pointGet = new stdClass();
      //     $pointGet->remark = 100;
      //   }

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['b_user_alamat_location_kelurahan'] = $d_address->kelurahan;
      //   $di['b_user_alamat_location_kecamatan'] = $d_address->kecamatan;
      //   $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
      //   $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //   $di['b_user_id'] = $pelanggan->id;
      //   $di['point'] = $pointGet->remark;
      //   $di['custom_id'] = $com_id;
      //   $di['custom_type'] = 'community';
      //   $di['custom_type_sub'] = 'post';
      //   $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
      //   $this->glphm->set($di);
      //   // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '+', $di['point']);
      // }else{
        //get total post
        $totalPost = $this->glphm->countAll($nation_code, "", "", "", "", $pelanggan->id, "+", "", "community", "post", date("Y-m-d"), "");

        $limitPost = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EE");
        if (!isset($limitPost->remark)) {
          $limitPost = new stdClass();
          $limitPost->remark = 5;
        }

        if($totalPost < $limitPost->remark){
          $postPointCode = "EG";
          $imagePointCode = "E13";
          $post_custom_type_sub = "post";
          $image_custom_type_sub = "upload image";
          if($getDoubleSpt == "1"){
            $postPointCode = "E14";
            $imagePointCode = "E15";
            $post_custom_type_sub = "post(double point)";
            $image_custom_type_sub = "upload image(double point)";
          }

          //get point
          $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $postPointCode);
          if (!isset($pointGet->remark)) {
            $pointGet = new stdClass();
            $pointGet->remark = 10;
          }

          $di = array();
          $di['nation_code'] = $nation_code;
          $di['b_user_alamat_location_kelurahan'] = $d_address->kelurahan;
          $di['b_user_alamat_location_kecamatan'] = $d_address->kecamatan;
          $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
          $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
          $di['b_user_id'] = $pelanggan->id;
          $di['point'] = $pointGet->remark;
          $di['custom_id'] = $com_id;
          $di['custom_type'] = 'community';
          $di['custom_type_sub'] = $post_custom_type_sub;
          $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
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

          if (count($listUploadImage) >= 1) {
            //get point
            $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $imagePointCode);
            if (!isset($pointGet->remark)) {
              $pointGet = new stdClass();
              $pointGet->remark = 14;
            }

            $di = array();
            $di['nation_code'] = $nation_code;
            $di['b_user_alamat_location_kelurahan'] = $d_address->kelurahan;
            $di['b_user_alamat_location_kecamatan'] = $d_address->kecamatan;
            $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
            $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
            $di['b_user_id'] = $pelanggan->id;
            $di['point'] = $pointGet->remark;
            $di['custom_id'] = $com_id;
            $di['custom_type'] = 'community';
            $di['custom_type_sub'] = $image_custom_type_sub;
            $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
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

        if($upload_video_new_version == 1){
          //get limit left
          $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "E9");
          if(!isset($limitLeft->limit_plus)){
            $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E9");

            $lastID = $this->glplm->getLastId($nation_code, date("Y-m-d"));
            $du = array();
            $du['nation_code'] = $nation_code;
            $du['id'] = $lastID;
            $du['cdate'] = date("Y-m-d");
            $du['b_user_id'] = $pelanggan->id;
            $du['code'] = "E9";
            $du['limit_plus'] = $pointGet->remark;
            $du['limit_minus'] = $pointGet->remark;
            $this->glplm->set($du);

            //get limit left
            $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "E9");
          }

          if($limitLeft->limit_plus > 0){
            $PointCode = "EP";
            $custom_type_sub = "upload video";
            if($getDoubleSpt == "1"){
              $PointCode = "E16";
              $custom_type_sub = "upload video(double point)";
            }

            //get point
            $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $PointCode);
            if (!isset($pointGet->remark)) {
                $pointGet = new stdClass();
                $pointGet->remark = 10;
            }

            $di = array();
            $di['nation_code'] = $nation_code;
            $di['b_user_alamat_location_kelurahan'] = $d_address->kelurahan;
            $di['b_user_alamat_location_kecamatan'] = $d_address->kecamatan;
            $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
            $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
            $di['b_user_id'] = $pelanggan->id;
            $di['point'] = $pointGet->remark;
            $di['custom_id'] = $com_id;
            $di['custom_type'] = 'community';
            $di['custom_type_sub'] = $custom_type_sub;
            $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
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
            $this->glplm->updateTotal($nation_code, date("Y-m-d"), $pelanggan->id, 'E9', 'limit_plus', '-', 1);
          }
        }
      // }
      // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_post', '+', 1);
      //END by Donny Dennison - 16 december 2021 15:49

      if(date("Y-m-d") >= "2023-11-06" && date("Y-m-d") <= "2023-11-30" && $getDoubleSpt == 0 && $getEventNewUser == 1 && $getEventRetargeting == 0){
        if($pelanggan->cdate >= "2023-11-06" && $pelanggan->cdate <= "2023-11-30"){
          $eventProgress = $this->ccenum->getByUserid($nation_code, $pelanggan->id);
          if(!isset($eventProgress->id) && $hashtagEventFound == "#penggunabarusellon" && !empty($listUploadImage)){
            $endDoWhile = 0;
            do{
              $eventId = $this->GUIDv4();
              $checkId = $this->ccenum->checkId($nation_code, $eventId);
              if($checkId == 0){
                $endDoWhile = 1;
              }
            }while($endDoWhile == 0);
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['id'] = $eventId;
            $di['b_user_id'] = $pelanggan->id;
            $di['c_community_id_day_1'] = $com_id;
            $di['cdate_day_1'] = 'NOW()';
            $di['cdate'] = 'NOW()';
            $this->ccenum->set($di);
          }

          if(isset($eventProgress->id) && $hashtagEventFound == "#penggunabarusellon" && !empty($listUploadImage)){
            if(!$eventProgress->cdate_day_2){
              if(date("Y-m-d", strtotime($eventProgress->cdate_day_1. " +1 day")) < date("Y-m-d") && date("Y-m-d", strtotime($eventProgress->cdate_day_1)) != date("Y-m-d")){
                $this->ccenum->inactivePrevious($nation_code, $pelanggan->id, array("is_active"=>0));
                $endDoWhile = 0;
                do{
                  $eventId = $this->GUIDv4();
                  $checkId = $this->ccenum->checkId($nation_code, $eventId);
                  if($checkId == 0){
                    $endDoWhile = 1;
                  }
                }while($endDoWhile == 0);
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['id'] = $eventId;
                $di['b_user_id'] = $pelanggan->id;
                $di['c_community_id_day_1'] = $com_id;
                $di['cdate_day_1'] = 'NOW()';
                $di['cdate'] = 'NOW()';
                $this->ccenum->set($di);
              }
            }else if(!$eventProgress->cdate_day_3){
              if(date("Y-m-d", strtotime($eventProgress->cdate_day_2. " +1 day")) < date("Y-m-d") && date("Y-m-d", strtotime($eventProgress->cdate_day_2)) != date("Y-m-d")){
                $this->ccenum->inactivePrevious($nation_code, $pelanggan->id, array("is_active"=>0));
                $endDoWhile = 0;
                do{
                  $eventId = $this->GUIDv4();
                  $checkId = $this->ccenum->checkId($nation_code, $eventId);
                  if($checkId == 0){
                    $endDoWhile = 1;
                  }
                }while($endDoWhile == 0);
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['id'] = $eventId;
                $di['b_user_id'] = $pelanggan->id;
                $di['c_community_id_day_1'] = $com_id;
                $di['cdate_day_1'] = 'NOW()';
                $di['cdate'] = 'NOW()';
                $this->ccenum->set($di);
              }
            }
          }

          if(isset($eventProgress->id) && $hashtagEventFound == "#sellonmissions" && ($insertVideo > 0 || !empty($listUploadVideo))){
            if(!$eventProgress->cdate_day_2 && !$eventProgress->cdate_day_3){
              if(date("Y-m-d", strtotime($eventProgress->cdate_day_1. " +1 day")) == date("Y-m-d")){
                $du = array();
                $du['c_community_id_day_2'] = $com_id;
                $du['cdate_day_2'] = 'NOW()';
                $this->ccenum->update($nation_code, $eventProgress->id, $du);
              }
            }
          }

          if(isset($eventProgress->id) && $hashtagEventFound == "#informasisekitar" && !empty($listUploadImage)){
            if($eventProgress->cdate_day_2 && !$eventProgress->cdate_day_3){
              if(date("Y-m-d", strtotime($eventProgress->cdate_day_2. " +1 day")) == date("Y-m-d")){
                $du = array();
                $du['c_community_id_day_3'] = $com_id;
                $du['cdate_day_3'] = 'NOW()';
                $this->ccenum->update($nation_code, $eventProgress->id, $du);

                $dpe = array();
                $dpe['nation_code'] = $nation_code;
                $dpe['b_user_id'] = $pelanggan->id;
                $dpe['id'] = $this->dpem->getLastId($nation_code, $pelanggan->id);
                $dpe['type'] = "event_hashtag_new_user";
                if($pelanggan->language_id == 2) {
                  $dpe['judul'] = "New User Event";
                  $dpe['teks'] =  "Anda telah menyelesaikan Misi Harian di Event user baru. Kami akan menverifikasi proses anda. Mohon menunggu instruksi berikutnya.";
                } else {
                  $dpe['judul'] = "New User Event";
                  $dpe['teks'] =  "You have successfully completed the Daily Mission in our Event for New User. We are currently verifying. Please await further instructions.";
                }

                $dpe['gambar'] = 'media/pemberitahuan/promotion.png';
                $dpe['cdate'] = "NOW()";
                $extras = new stdClass();
                $extras->id = $pelanggan->id;
                if($pelanggan->language_id == 2) { 
                  $extras->judul = "New User Event";
                  $extras->teks =  "Anda telah menyelesaikan Misi Harian di Event user baru. Kami akan menverifikasi proses anda. Mohon menunggu instruksi berikutnya.";
                } else {
                  $extras->judul = "New User Event";
                  $extras->teks =  "You have successfully completed the Daily Mission in our Event for New User. We are currently verifying. Please await further instructions.";
                }

                $dpe['extras'] = json_encode($extras);
                $this->dpem->set($dpe);

                $classified = 'setting_notification_user';
                $code = 'U3';
                $receiverSettingNotif = $this->busm->getValue($nation_code, $pelanggan->id, $classified, $code);
                if (!isset($receiverSettingNotif->setting_value)){
                    $receiverSettingNotif->setting_value = 0;
                }

                if ($receiverSettingNotif->setting_value == 1 && $pelanggan->is_active == 1) {
                  if($pelanggan->device == "ios"){
                    $device = "ios";
                  }else{
                    $device = "android";
                  }

                  $tokens = $pelanggan->fcm_token;
                  if(!is_array($tokens)) $tokens = array($tokens);
                  if($pelanggan->language_id == 2){
                    $title = "New User Event";
                    $message = "Anda telah menyelesaikan Misi Harian di Event user baru. Kami akan menverifikasi proses anda. Mohon menunggu instruksi berikutnya.";
                  } else {
                    $title = "New User Event";
                    $message = "You have successfully completed the Daily Mission in our Event for New User. We are currently verifying. Please await further instructions.";
                  }

                  $image = 'media/pemberitahuan/promotion.png';
                  $type = 'event_hashtag_new_user';
                  $payload = new stdClass();
                  $payload->id = $pelanggan->id;
                  if($pelanggan->language_id == 2) {
                    $payload->judul = "New User Event";
                    $payload->teks = "Anda telah menyelesaikan Misi Harian di Event user baru. Kami akan menverifikasi proses anda. Mohon menunggu instruksi berikutnya.";
                  } else {
                    $payload->judul = "New User Event";
                    $payload->teks = "You have successfully completed the Daily Mission in our Event for New User. We are currently verifying. Please await further instructions.";
                  }
                  $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                }
              }
            }
          }
        }
      }

      if (date("Y-m-d") >= "2023-10-16" && date("Y-m-d") <= "2023-10-31" && $getDoubleSpt == 0 && $getEventNewUser == 0 && $getEventRetargeting == 1){
        $newUserFinishNewUserEvent= 1;
        if($pelanggan->cdate >= "2023-10-16" && $pelanggan->cdate <= "2023-10-31" && $pelanggan->b_user_id_recruiter == "0"){
          $eventProgress = $this->ccenum->getByUserid($nation_code, $pelanggan->id);
          if(!isset($eventProgress->id)){
            $newUserFinishNewUserEvent = 0;
          }
          if(isset($eventProgress->id)){
            if(!$eventProgress->cdate_day_3){
              $newUserFinishNewUserEvent = 0;
            }
          }
        }

        if($newUserFinishNewUserEvent == 1){
          $eventProgress = $this->ccertm->getByUserid($nation_code, $pelanggan->id);
          if(!isset($eventProgress->id) && $hashtagEventFound == "#penggunasetia" && !empty($listUploadImage)){
            $endDoWhile = 0;
            do{
              $eventId = $this->GUIDv4();
              $checkId = $this->ccertm->checkId($nation_code, $eventId);
              if($checkId == 0){
                $endDoWhile = 1;
              }
            }while($endDoWhile == 0);
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['id'] = $eventId;
            $di['b_user_id'] = $pelanggan->id;
            $di['c_community_id_day_1'] = $com_id;
            $di['cdate_day_1'] = 'NOW()';
            $di['cdate'] = 'NOW()';
            $this->ccertm->set($di);
          }

          if(isset($eventProgress->id) && $hashtagEventFound == "#penggunasetia" && !empty($listUploadImage)){
            if(!$eventProgress->cdate_day_2){
              if(date("Y-m-d", strtotime($eventProgress->cdate_day_1. " +1 day")) < date("Y-m-d") && date("Y-m-d", strtotime($eventProgress->cdate_day_1)) != date("Y-m-d")){
                $this->ccertm->inactivePrevious($nation_code, $pelanggan->id, array("is_active"=>0));
                $endDoWhile = 0;
                do{
                  $eventId = $this->GUIDv4();
                  $checkId = $this->ccertm->checkId($nation_code, $eventId);
                  if($checkId == 0){
                    $endDoWhile = 1;
                  }
                }while($endDoWhile == 0);
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['id'] = $eventId;
                $di['b_user_id'] = $pelanggan->id;
                $di['c_community_id_day_1'] = $com_id;
                $di['cdate_day_1'] = 'NOW()';
                $di['cdate'] = 'NOW()';
                $this->ccertm->set($di);
              }
            }else if(!$eventProgress->cdate_day_3){
              if(date("Y-m-d", strtotime($eventProgress->cdate_day_2. " +1 day")) < date("Y-m-d") && date("Y-m-d", strtotime($eventProgress->cdate_day_2)) != date("Y-m-d")){
                $this->ccertm->inactivePrevious($nation_code, $pelanggan->id, array("is_active"=>0));
                $endDoWhile = 0;
                do{
                  $eventId = $this->GUIDv4();
                  $checkId = $this->ccertm->checkId($nation_code, $eventId);
                  if($checkId == 0){
                    $endDoWhile = 1;
                  }
                }while($endDoWhile == 0);
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['id'] = $eventId;
                $di['b_user_id'] = $pelanggan->id;
                $di['c_community_id_day_1'] = $com_id;
                $di['cdate_day_1'] = 'NOW()';
                $di['cdate'] = 'NOW()';
                $this->ccertm->set($di);
              }
            }else if(!$eventProgress->cdate_day_4){
              if(date("Y-m-d", strtotime($eventProgress->cdate_day_3. " +1 day")) < date("Y-m-d") && date("Y-m-d", strtotime($eventProgress->cdate_day_3)) != date("Y-m-d")){
                $this->ccertm->inactivePrevious($nation_code, $pelanggan->id, array("is_active"=>0));
                $endDoWhile = 0;
                do{
                  $eventId = $this->GUIDv4();
                  $checkId = $this->ccertm->checkId($nation_code, $eventId);
                  if($checkId == 0){
                    $endDoWhile = 1;
                  }
                }while($endDoWhile == 0);
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['id'] = $eventId;
                $di['b_user_id'] = $pelanggan->id;
                $di['c_community_id_day_1'] = $com_id;
                $di['cdate_day_1'] = 'NOW()';
                $di['cdate'] = 'NOW()';
                $this->ccertm->set($di);
              }
            }else if(!$eventProgress->cdate_day_5){
              if(date("Y-m-d", strtotime($eventProgress->cdate_day_4. " +1 day")) < date("Y-m-d") && date("Y-m-d", strtotime($eventProgress->cdate_day_4)) != date("Y-m-d")){
                $this->ccertm->inactivePrevious($nation_code, $pelanggan->id, array("is_active"=>0));
                $endDoWhile = 0;
                do{
                  $eventId = $this->GUIDv4();
                  $checkId = $this->ccertm->checkId($nation_code, $eventId);
                  if($checkId == 0){
                    $endDoWhile = 1;
                  }
                }while($endDoWhile == 0);
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['id'] = $eventId;
                $di['b_user_id'] = $pelanggan->id;
                $di['c_community_id_day_1'] = $com_id;
                $di['cdate_day_1'] = 'NOW()';
                $di['cdate'] = 'NOW()';
                $this->ccertm->set($di);
              }
            }
          }

          if(isset($eventProgress->id) && $hashtagEventFound == "#dailymissions" && ($insertVideo > 0 || !empty($listUploadVideo))){
            if(!$eventProgress->cdate_day_2 && !$eventProgress->cdate_day_3){
              if(date("Y-m-d", strtotime($eventProgress->cdate_day_1. " +1 day")) == date("Y-m-d")){
                $du = array();
                $du['c_community_id_day_2'] = $com_id;
                $du['cdate_day_2'] = 'NOW()';
                $this->ccertm->update($nation_code, $eventProgress->id, $du);
              }
            }
          }

          if(isset($eventProgress->id) && $hashtagEventFound == "#sellonkomunitas" && !empty($listUploadImage)){
            if($eventProgress->cdate_day_2 && !$eventProgress->cdate_day_3){
              if(date("Y-m-d", strtotime($eventProgress->cdate_day_2. " +1 day")) == date("Y-m-d")){
                $du = array();
                $du['c_community_id_day_3'] = $com_id;
                $du['cdate_day_3'] = 'NOW()';
                $this->ccertm->update($nation_code, $eventProgress->id, $du);
              }
            }
          }
        }
      }

      $this->ccomm->trans_commit();
      //end transaction
      $this->ccomm->trans_end();
    }

    // $url = base_url("api_mobile/community/detail/$com_id/?apikey=$apikey&nation_code=$nation_code&apisess=$apisess");
    // $res = $this->seme_curl->get($url);
    // $body = json_decode($res->body);
    // $data = $body->data;

    $community = $this->ccomm->getById($nation_code, $com_id, $pelanggan);

    if (isset($pelanggan->id)) {
      $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
    }else{
      $pelangganAddress = array();
    }

    $community->can_chat_and_like = "0";
    // if(isset($pelanggan->id) && isset($pelangganAddress->alamat2)){
    if(isset($pelanggan->id)){
      // if($community->postal_district == $pelangganAddress->postal_district){
        $community->can_chat_and_like = "1";
      // }
    }

    $community->is_owner_post = "0";
    $community->is_liked = '0';
    $community->is_disliked = '0';
    if(isset($pelanggan->id)){
      if($community->b_user_id_starter == $pelanggan->id){
        $community->is_owner_post = "1";
      }

      $checkLike = $this->cclm->getByCustomIdUserId($nation_code, "community", "like", $community->id, $pelanggan->id);
      if(isset($checkLike->id)){
        $community->is_liked = '1';
      }

      $checkDislike = $this->cclm->getByCustomIdUserId($nation_code, "community", "dislike", $community->id, $pelanggan->id);
      if(isset($checkDislike->id)){
        $community->is_disliked = '1';
      }
    }

    // $community->cdate_text = $this->humanTiming($community->cdate);
    $community->cdate_text = $this->humanTiming($community->cdate, null, $pelanggan->language_id);
    $community->cdate = $this->customTimezone($community->cdate, $timezone);

    if (strlen($community->b_user_image_starter)<=4) {
      $community->b_user_image_starter = 'media/user/default.png';
    }

    // filter utf-8
    if (isset($community->b_user_nama_starter)) {
      $community->b_user_nama_starter = $this->__dconv($community->b_user_nama_starter);
    }

    $community->title = html_entity_decode($community->title,ENT_QUOTES);
    $community->deskripsi = html_entity_decode($community->deskripsi,ENT_QUOTES);

    if(file_exists(SENEROOT.$community->b_user_image_starter) && $community->b_user_image_starter != 'media/user/default.png'){
      $community->b_user_image_starter = $this->cdn_url($community->b_user_image_starter);
    } else {
      $community->b_user_image_starter = $this->cdn_url('media/user/default-profile-picture.png');
    }

    if($community->top_like_image_1 > 0){
      $community->top_like_image_1 = $this->cdn_url("media/icon/like-62-1-141111.png");
    }

    $community->images = array();
    $community->locations = array();
    $community->videos = array();
    $attachments = $this->ccam->getByCommunityId($nation_code, $community->id);
    foreach ($attachments as $atc) {
      if($atc->jenis == 'image'){
        $atc->url = $this->cdn_url($atc->url);
        $atc->url_thumb = $this->cdn_url($atc->url_thumb);
        $community->images[] = $atc;
      }else if($atc->jenis == 'video'){
        $atc->url = $this->cdn_url($atc->url);
        $atc->url_thumb = $this->cdn_url($atc->url_thumb);
        $community->videos[] = $atc;
      }else{
        $community->locations[] = $atc;
      }
    }
    unset($attachments);

    $data['community'] = $community;

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  }

  public function hapus($c_community_id="")
  {
    $dt = $this->__init();
    $data = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    // $c_community_id = (int) $c_community_id;
    if ($c_community_id<='0') {
      $this->status = 595;
      $this->message = 'Invalid Community ID or Community not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $community = $this->ccomm->getById($nation_code, $c_community_id, $pelanggan);
    if (!isset($community->id)) {
      $this->status = 595;
      $this->message = 'Invalid Community ID or Community not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    if ($community->b_user_id_starter != $pelanggan->id) {
      $this->status = 908;
      $this->message = "Access denied, you can't delete other people community post";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //start transaction
    $this->ccomm->trans_start();

    $du = array();
    $du['is_published'] = 0;
    $du['is_active'] = 0;
    $res = $this->ccomm->update($nation_code, $c_community_id, $du);
    if ($res) {
      $this->ccomm->trans_commit();
      
      $attachments = $this->ccam->getByCommunityId($nation_code, $c_community_id);

      $du = array();
      $du['is_active'] = 0;
      $res2 = $this->ccam->updateByCommunityId($nation_code, $c_community_id, $du);
      if ($res2) {
        $this->ccomm->trans_commit();
        $this->status = 200;
        // $this->message = 'Community deleted successfully';
        $this->message = 'Success';

        //delete attachment file
        if (count($attachments)) {
          foreach ($attachments as $atc) {
            if($atc->jenis == 'image' || $atc->jenis == 'video'){
              if ($atc->url != $this->media_community_video."default.png") {
                $fileloc = SENEROOT.$atc->url;
                if (file_exists($fileloc)) {
                  unlink($fileloc);
                }
              }

              if ($atc->url_thumb != $this->media_community_video."default.png") {
                $fileloc = SENEROOT.$atc->url_thumb;
                if (file_exists($fileloc)) {
                  unlink($fileloc);
                }
              }
            }
          }
          unset($atc);
        }
        // unset($attachments);
      } else {
        $this->ccomm->trans_rollback();
        $this->status = 941;
        $this->message = 'Failed deleting community images';

        //finish transaction
        $this->ccomm->trans_end();

        //render output
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
        die();
      }

      $type = 'chat';
      $replacer = array();

      $replacer['user_nama'] = html_entity_decode($pelanggan->fnama,ENT_QUOTES);
      $message = '';
      $message_indonesia = '';

      $nw = $this->anot->get($nation_code, "push", $type, 4, 1);
      if (isset($nw->message)) {
        $message = $this->__nRep($nw->message, $replacer);
      }

      $nw = $this->anot->get($nation_code, "push", $type, 4, 2);
      if (isset($nw->message)) {
        $message_indonesia = $this->__nRep($nw->message, $replacer);
      }

      $chat_room = $this->ecrm->getChatRoomByCommunityID($nation_code, $c_community_id);

      //get last chat id
      $chat_id = $this->chat->getLastId($nation_code, $chat_room->id);
      
      $di = array();
      $di['id'] = $chat_id;
      $di['nation_code'] = $nation_code;
      $di['e_chat_room_id'] = $chat_room->id;
      $di['b_user_id'] = 0;
      $di['type'] = 'announcement';
      $di['message'] = $message;
      $di['message_indonesia'] = $message_indonesia;
      $di['cdate'] = "NOW()";
      $this->chat->set($di);
      $this->ccomm->trans_commit();

      $participant_list = $this->ecpm->getParticipantByRoomChatId($nation_code,$chat_room->id);

      //set unread in table e_chat_read
      $insertArray = array();
      foreach($participant_list AS $participant){
        $du = array();
        $du['nation_code'] = $nation_code;
        $du['b_user_id'] = $participant->b_user_id;
        $du['e_chat_room_id'] = $chat_room->id;
        $du['e_chat_id'] = $chat_id;
        if($participant->b_user_id == $pelanggan->id){
          $du['is_read'] = 1;
        }else{
          $du['is_read'] = 0;
        }
        $du['cdate'] = "NOW()";
        $insertArray[] = $du;
      }
      unset($participant_list, $participant);

      $chunkInsertArray = array_chunk($insertArray,50);

      foreach($chunkInsertArray AS $chunk){
        //insert multi
        $this->ecreadm->setMass($chunk);
        $this->ccomm->trans_commit();
      }
      unset($insertArray, $chunkInsertArray, $chunk);

      // $di = array();
      // $di['is_active'] = 0;
      // $this->dpem->updateCustomByTypeExtras($nation_code,'community_discussion',$c_community_id,$di);
      // $this->ccomm->trans_commit();

      //START by Donny Dennison - 13 october 2022 14:10
      //change point policy
      // //check already get point or not previously
      // $alreadyGetPointGroupChat = $this->glphm->countAll($nation_code, "", "", "", "", $pelanggan->id, "+", $c_community_id, "community", "more than", "", "");

      // if($alreadyGetPointGroupChat == 1){

      //   //get point
      //   $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EN");
      //   if (!isset($pointGet->remark)) {
      //       $pointGet = new stdClass();
      //       $pointGet->remark = 50;
      //   }

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
      //   $di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
      //   $di['b_user_alamat_location_kabkota'] = $community->kabkota;
      //   $di['b_user_alamat_location_provinsi'] = $community->provinsi;
      //   $di['b_user_id'] = $pelanggan->id;
      //   $di['plusorminus'] = "-";
      //   $di['point'] = $pointGet->remark;
      //   $di['custom_id'] = $c_community_id;
      //   $di['custom_type'] = 'community';
      //   $di['custom_type_sub'] = 'more than';
      //   $di['custom_text'] = $pelanggan->fnama.' has delete '.$di['custom_type'].' post (deduct point get from group chat if more than) and lose '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
      //   $this->glphm->set($di);
      //   $this->ccomm->trans_commit();
      //   // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '+', $di['point']);
      // }
      //END by Donny Dennison - 13 october 2022 14:10
      //change point policy

      //get total community post
      // $totalPostNow = $this->ccomm->countAllByUserId($nation_code, $pelanggan->id);

      // if($totalPostNow == 0){

      //   //get point
      //   $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EF");
      //   if (!isset($pointGet->remark)) {
      //     $pointGet = new stdClass();
      //     $pointGet->remark = 100;
      //   }

      //   $di = array();
      //   $di['nation_code'] = $nation_code;
      //   $di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
      //   $di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
      //   $di['b_user_alamat_location_kabkota'] = $community->kabkota;
      //   $di['b_user_alamat_location_provinsi'] = $community->provinsi;
      //   $di['b_user_id'] = $pelanggan->id;
      //   $di['plusorminus'] = "-";
      //   $di['point'] = $pointGet->remark;
      //   $di['custom_id'] = $c_community_id;
      //   $di['custom_type'] = 'community';
      //   $di['custom_type_sub'] = 'post';
      //   $di['custom_text'] = $pelanggan->fnama.' has delete '.$di['custom_type'].' '.$di['custom_type_sub'].' and lose '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
      //   $this->glphm->set($di);
      //   $this->ccomm->trans_commit();
      //   // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
      //   // $this->ccomm->trans_commit();

      // }else{

        $postPointCode = "EG";
        $imagePointCode = "E13";
        $videoPointCode = "EP";
        $post_custom_type_sub = "post";
        $image_custom_type_sub = "upload image";
        $video_custom_type_sub = "upload video";
        if($community->is_double_spt == "1"){
          $postPointCode = "E14";
          $imagePointCode = "E15";
          $videoPointCode = "E16";
          $post_custom_type_sub = "post(double point)";
          $image_custom_type_sub = "upload image(double point)";
          $video_custom_type_sub = "upload video(double point)";
        }

        //get point
        $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $postPointCode);
        if (!isset($pointGet->remark)) {
          $pointGet = new stdClass();
          $pointGet->remark = 10;
        }

        $di = array();
        $di['nation_code'] = $nation_code;
        $di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
        $di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
        $di['b_user_alamat_location_kabkota'] = $community->kabkota;
        $di['b_user_alamat_location_provinsi'] = $community->provinsi;
        $di['b_user_id'] = $pelanggan->id;
        $di['plusorminus'] = "-";
        $di['point'] = $pointGet->remark;
        $di['custom_id'] = $c_community_id;
        $di['custom_type'] = 'community';
        $di['custom_type_sub'] = $post_custom_type_sub;
        $di['custom_text'] = $pelanggan->fnama.' has delete '.$di['custom_type'].' '.$di['custom_type_sub'].' and lose '.$di['point'].' point(s)';
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
        $this->ccomm->trans_commit();
        // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
        // $this->ccomm->trans_commit();

        $checkAlreadyInleaderBoardHistory = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", $pelanggan->id, $c_community_id, 'community', $image_custom_type_sub);
        if(isset($checkAlreadyInleaderBoardHistory->b_user_id)){
          //get point
          $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $imagePointCode);
          if (!isset($pointGet->remark)) {
              $pointGet = new stdClass();
              $pointGet->remark = 14;
          }

          $di = array();
          $di['nation_code'] = $nation_code;
          $di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
          $di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
          $di['b_user_alamat_location_kabkota'] = $community->kabkota;
          $di['b_user_alamat_location_provinsi'] = $community->provinsi;
          $di['b_user_id'] = $pelanggan->id;
          $di['plusorminus'] = "-";
          $di['point'] = $pointGet->remark;
          $di['custom_id'] = $c_community_id;
          $di['custom_type'] = 'community';
          $di['custom_type_sub'] = $image_custom_type_sub;
          $di['custom_text'] = $pelanggan->fnama.' has delete '.$di['custom_type_sub'].' '.$di['custom_type'].' and lose '.$di['point'].' point(s)';
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
          // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
        }

        $checkAlreadyInleaderBoardHistory = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", $pelanggan->id, $c_community_id, 'community', $video_custom_type_sub);
        if(isset($checkAlreadyInleaderBoardHistory->b_user_id)){

          //get point
          $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $videoPointCode);
          if (!isset($pointGet->remark)) {
              $pointGet = new stdClass();
              $pointGet->remark = 10;
          }

          $di = array();
          $di['nation_code'] = $nation_code;
          $di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
          $di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
          $di['b_user_alamat_location_kabkota'] = $community->kabkota;
          $di['b_user_alamat_location_provinsi'] = $community->provinsi;
          $di['b_user_id'] = $pelanggan->id;
          $di['plusorminus'] = "-";
          $di['point'] = $pointGet->remark;
          $di['custom_id'] = $c_community_id;
          $di['custom_type'] = 'community';
          $di['custom_type_sub'] = $video_custom_type_sub;
          $di['custom_text'] = $pelanggan->fnama.' has delete '.$di['custom_type_sub'].' '.$di['custom_type'].' and lose '.$di['point'].' point(s)';
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
          // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
        }
      // }
      // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_post', '-', 1);
      // $this->ccomm->trans_commit();

      // //START by Donny Dennison 25 july 2022 - 16:48
      // //change point get rule for upload video community
      // foreach ($attachments as $atc) {
      //   if($atc->jenis == "video" && $atc->convert_status != "uploading"){
      //     //START by Donny Dennison 12 December 2022 - 15:24
      //     //Set daily limit to Video registration : 10 to Community posts and 15 to products
      //     //get limit left
      //     $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "E9");
      //     if(!isset($limitLeft->limit_minus)){
      //       $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E9");
      //       $lastID = $this->glplm->getLastId($nation_code, date("Y-m-d"));
      //       $du = array();
      //       $du['nation_code'] = $nation_code;
      //       $du['id'] = $lastID;
      //       $du['cdate'] = date("Y-m-d");
      //       $du['b_user_id'] = $pelanggan->id;
      //       $du['code'] = "E9";
      //       $du['limit_plus'] = $pointGet->remark;
      //       $du['limit_minus'] = $pointGet->remark;
      //       $this->glplm->set($du);
      //       //get limit left
      //       $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "E9");
      //     }

      //     if($limitLeft->limit_minus > 0){
      //       //get point
      //       $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EP");
      //       if (!isset($pointGet->remark)) {
      //           $pointGet = new stdClass();
      //           $pointGet->remark = 10;
      //       }

      //       $di = array();
      //       $di['nation_code'] = $nation_code;
      //       $di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
      //       $di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
      //       $di['b_user_alamat_location_kabkota'] = $community->kabkota;
      //       $di['b_user_alamat_location_provinsi'] = $community->provinsi;
      //       $di['b_user_id'] = $pelanggan->id;
      //       $di['plusorminus'] = "-";
      //       $di['point'] = $pointGet->remark;
      //       $di['custom_id'] = $c_community_id;
      //       $di['custom_type'] = 'community';
      //       $di['custom_type_sub'] = 'upload video';
      //       $di['custom_text'] = $pelanggan->fnama.' has delete '.$di['custom_type_sub'].' '.$di['custom_type'].' and lose '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
      //       $this->glphm->set($di);
      //       // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
      //       $this->glplm->updateTotal($nation_code, date("Y-m-d"), $pelanggan->id, 'E9', 'limit_minus', '-', 1);
      //       $this->glplm->updateTotal($nation_code, date("Y-m-d"), $pelanggan->id, 'E9', 'limit_plus', '+', 1);
      //     }
      //     //END by Donny Dennison 12 December 2022 - 15:24
      //     //Set daily limit to Video registration : 10 to Community posts and 15 to products
      //     break;
      //   }
      // }
      // //END by Donny Dennison 25 july 2022 - 16:48
      // //change point get rule for upload video community

      //START by Donny Dennison - 24 november 2021 9:45
      //add feature highlight community & leaderboard point & hot item
      // $di = array();
      // $di['is_active'] = 0;
      // $this->ghcm->updateByCommunityId($nation_code, $c_community_id, $di);
      // $this->ccomm->trans_commit();
      //END by Donny Dennison - 24 november 2021 9:45
    } else {
      $this->ccomm->trans_rollback();
      $this->status = 940;
      $this->message = "Can't delete community from database, please try again later";
    }

    //finish transaction
    $this->ccomm->trans_end();

    //render output
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  }

  public function edit()
  {
    //init
    $dt = $this->__init();

    //default response
    $data = array();
    $data['community'] = new stdClass();

    $this->seme_log->write("api_mobile", "Community::edit -> ".json_encode($_POST));

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $timezone = $this->input->get('timezone');
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $c_community_id = $this->input->post("c_community_id");
    if ($c_community_id<='0') {
      $this->status = 595;
      $this->message = 'Invalid Community ID or Community not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $community = $this->ccomm->getById($nation_code, $c_community_id, $pelanggan);
    if (!isset($community->id)) {
      $this->status = 595;
      $this->message = 'Invalid Community ID or Community not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    if ($community->b_user_id_starter != $pelanggan->id) {
      $this->status = 907;
      $this->message = "Access denied, you can't change other people community post";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //populating input
    $title =  trim($this->input->post("title"));
    $deskripsi =  trim($this->input->post("deskripsi"));
    $location_json = $this->input->post("location_json");
    $c_community_category_id = $this->input->post("c_community_category_id");

    //START by Donny Dennison - 23 november 2022 13:42
    //new feature, manage group member
    $group_chat_type = trim($this->input->post('group_chat_type'));
    if(empty($group_chat_type)){
      $group_chat_type = $community->group_chat_type;
    }

    if($group_chat_type != "private"){
      $group_chat_type = "public";
    }
    //END by Donny Dennison - 23 november 2022 13:42
    //new feature, manage group member

    //by Donny Dennison 24 february 2021 18:45
    //change ’ to ' in add & edit product name and description
    $title = str_replace('’',"'",$title);
    $deskripsi = str_replace('’',"'",$deskripsi);

    //sanitize input
    // $title = filter_var($title, FILTER_SANITIZE_STRING);
    // $deskripsi = filter_var($deskripsi, FILTER_SANITIZE_STRING);
    $deskripsi = nl2br($deskripsi);

    //by Donny Dennison - 15 augustus 2020 15:09
    //bug fix \n (enter) didnt get remove
    $deskripsi = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $deskripsi);

    $title = str_replace("\\n", "<br />", $title);
    $deskripsi = str_replace("\\n", "<br />", $deskripsi);
    $deskripsi = str_replace(" #", "#", $deskripsi);
    $deskripsi = str_replace("#", " #", $deskripsi);

    //validation
    if (strlen($title)<3) {
      $this->status = 1104;
      $this->message = 'Title is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }
    
    if (strlen($deskripsi)<3) {
      $this->status = 1105;
      $this->message = 'Description is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    if(empty($c_community_category_id)){
      $c_community_category_id = $community->c_community_category_id;
    }

    $kat = $this->cccm->getById($nation_code, $c_community_category_id);
    if (!isset($kat->id)) {
      $this->status = 1100;
      $this->message = 'Please choose Community category';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }
    
    //start transaction
    $this->ccomm->trans_start();

    //updating to database
    $du = array();
    $du['c_community_category_id'] = $c_community_category_id;
    $du['group_chat_type'] = $group_chat_type;
    $du['title'] = $title;
    $du['deskripsi'] = $deskripsi;
    
    $res = $this->ccomm->update($nation_code, $c_community_id, $du);
    if ($res) {
      $this->ccomm->trans_commit();
      $cpm_id = $c_community_id;
      $this->status = 200;
      // $this->message = 'Community post edited successfully';
      $this->message = 'Success';

      $listUrl = array();
      $listUpload = array();
      $checkFileExist = 1;
      //looping for get list of url
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
        $this->ccomm->trans_rollback();
        $this->ccomm->trans_end();
        $this->status = 995;
        $this->message = 'Failed upload, temporary already gone';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
        die();
      }

      //delete image that is not in array
      $attachments = $this->ccam->getByCommunityId($nation_code, $c_community_id);
      foreach ($attachments as $atc) {
        if ((!in_array($atc->url, $listUrl) || empty($listUrl)) && $atc->jenis == 'image') {
          $this->ccam->delByIdCommunityId($nation_code, $atc->id, $c_community_id, $atc->jenis);
          $this->ccomm->trans_commit();

          if (strlen($atc->url)>4) {
            $file = SENEROOT.$atc->url;
            if (!is_dir($file) && file_exists($file)) {
              unlink($file);
            }
          }

          if (strlen($atc->url_thumb)>4) {
            $file = SENEROOT.$atc->url_thumb;
            if (!is_dir($file) && file_exists($file)) {
              unlink($file);
            }
          }
        }
      }

      if(!empty($listUpload)){
        //upload image and insert to c_product_foto table
        foreach ($listUpload as $key => $upload) {
          $photoId_last = $this->ccam->getLastId($nation_code,$c_community_id, 'image');

          // $sc = $this->__uploadImagex($nation_code, $upload, $c_community_id, $photoId_last);
          $sc = $this->__moveImagex($nation_code, $upload, $c_community_id, $photoId_last);
          if (isset($sc->status)) {
            if ($sc->status==200) { 
              $dix = array();
              $dix['nation_code'] = $nation_code;
              $dix['c_community_id'] = $c_community_id;
              $dix['id'] = $photoId_last;
              $dix['jenis'] = 'image';
              $dix['url'] = $sc->image;
              $dix['url_thumb'] = $sc->thumb;
              $this->ccam->set($dix);
              $this->ccomm->trans_commit();
            }
          }
        }
      }

      $this->ccam->delByCommunityIdJenis($nation_code, $c_community_id, 'location');
      $this->ccomm->trans_commit();

      if(is_array($location_json)){
        if(count($location_json) > 0){
          //upload location and insert to c_community_attachment table
          foreach ($location_json as $key => $upload) {
            if(isset($upload['location_nama']) && isset($upload['location_address']) && isset($upload['location_place_id']) && isset($upload['location_latitude']) && isset($upload['location_longitude'])){
              if($upload['location_nama'] && $upload['location_address'] && $upload['location_place_id'] && $upload['location_latitude'] && $upload['location_longitude']){
                $locationId_last = $this->ccam->getLastId($nation_code,$c_community_id, 'location');

                $dix = array();
                $dix['nation_code'] = $nation_code;
                $dix['c_community_id'] = $c_community_id;
                $dix['id'] = $locationId_last;
                $dix['jenis'] = 'location';
                $dix['location_nama'] = $upload['location_nama'];
                $dix['location_address'] = $upload['location_address'];
                $dix['location_place_id'] = $upload['location_place_id'];
                $dix['location_latitude'] = $upload['location_latitude'];
                $dix['location_longitude'] = $upload['location_longitude'];
                $this->ccam->set($dix);
                $this->ccomm->trans_commit();        
              }
            }
          }
        }
      }

      $insertVideo = 0;
      //looping for get list of image
      for ($i=1; $i < 6; $i++) {
        if($this->input->post('video'.$i) === "yes"){
          $insertVideo++;
        }
      }

      if($insertVideo > 0){
        //insert to c_produk_foto table
        for ($i=1; $i <= $insertVideo; $i++) {
          $cpfm_last = $this->ccam->getLastId($nation_code,$c_community_id, "video");
          
          $upi = $this->__moveImagex($nation_code, $this->input->post("video".$i."_thumb"), $c_community_id, $cpfm_last);
          
          $dix = array();
          $dix['nation_code'] = $nation_code;
          $dix['c_community_id'] = $c_community_id;
          $dix['id'] = $cpfm_last;
          $dix['jenis'] = 'video';
          $dix['convert_status'] = 'uploading';
          if($upi->status == 200){
            $dix['url'] = $upi->image;
            $dix['url_thumb'] = $upi->thumb;
          }else{
            $dix['url'] = $this->media_community_video."default.png";
            $dix['url_thumb'] = $this->media_community_video."default.png";
          }
          $this->ccam->set($dix);
          $this->ccomm->trans_commit();
        }
      }

      if($community->chat_room_id != 0 && $community->chat_room_id != NULL){
        $chatRoomDetail = $this->ecrm->getChatRoomByID($nation_code, $community->chat_room_id);
        if(isset($chatRoomDetail->id)){
          //update title in chat
          $di = array();
          $di['custom_name_1'] = $title;

          //by Donny Dennison - 23 november 2022 13:42
          //new feature, manage group member
          $di['group_chat_type'] = $group_chat_type;

          $this->ecrm->update($nation_code, $community->chat_room_id, $di);
          $this->ccomm->trans_commit();
        }
      } 
    } else {
      $this->ccomm->trans_rollback();
      $this->status = 990;
      $this->message = "Can't edit community post from database, please try again later";
    }
    //finish transaction
    $this->ccomm->trans_end();

    if($this->status == 200){
      $this->cchhfsm->inactivePrevious($nation_code, $c_community_id, array("is_active"=>0));

      preg_match_all('/(?<!\w)#\w+/', $deskripsi, $allMatches);
      $allMatches = array_values(array_unique($allMatches[0]));
      if($allMatches){
        foreach($allMatches AS $hashtag){
          $dataInsertHistory = array();
          $dataInsertHistory['nation_code'] = $nation_code;
          $endDoWhile = 0;
          do{
            $historyHastagId = $this->GUIDv4();
            $checkId = $this->cchhfsm->checkId($nation_code, $historyHastagId);
            if($checkId == 0){
              $endDoWhile = 1;
            }
          }while($endDoWhile == 0);
          $dataInsertHistory['id'] = $historyHastagId;
          $dataInsertHistory['c_community_id'] = $c_community_id;
          $dataInsertHistory['b_user_id'] = $pelanggan->id;
          $dataInsertHistory['hashtag'] = $hashtag;
          $dataInsertHistory['cdate'] = "NOW()";
          $this->cchhfsm->set($dataInsertHistory);
        }
      }
    }

    // $url = base_url("api_mobile/community/detail/$c_community_id/?apikey=$apikey&nation_code=$nation_code&apisess=$apisess");
    // $res = $this->seme_curl->get($url);
    // $body = json_decode($res->body);
    // $data = $body->data;

    $community = $this->ccomm->getById($nation_code, $c_community_id, $pelanggan);

    if (isset($pelanggan->id)) {
      $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
    }else{
      $pelangganAddress = array();
    }

    $community->can_chat_and_like = "0";
    // if(isset($pelanggan->id) && isset($pelangganAddress->alamat2)){
    if(isset($pelanggan->id)){
      // if($community->postal_district == $pelangganAddress->postal_district){
        $community->can_chat_and_like = "1";
      // }
    }

    $community->is_owner_post = "0";
    $community->is_liked = '0';
    $community->is_disliked = '0';
    if(isset($pelanggan->id)){
      if($community->b_user_id_starter == $pelanggan->id){
        $community->is_owner_post = "1";
      }

      $checkLike = $this->cclm->getByCustomIdUserId($nation_code, "community", "like", $community->id, $pelanggan->id);
      if(isset($checkLike->id)){
        $community->is_liked = '1';
      }

      $checkDislike = $this->cclm->getByCustomIdUserId($nation_code, "community", "dislike", $community->id, $pelanggan->id);
      if(isset($checkDislike->id)){
        $community->is_disliked = '1';
      }
    }

    // $community->cdate_text = $this->humanTiming($community->cdate);
    $community->cdate_text = $this->humanTiming($community->cdate, null, $pelanggan->language_id);
    $community->cdate = $this->customTimezone($community->cdate, $timezone);

    if (strlen($community->b_user_image_starter)<=4) {
      $community->b_user_image_starter = 'media/user/default.png';
    }

    // filter utf-8
    if (isset($community->b_user_nama_starter)) {
      $community->b_user_nama_starter = $this->__dconv($community->b_user_nama_starter);
    }

    $community->title = html_entity_decode($community->title,ENT_QUOTES);
    $community->deskripsi = html_entity_decode($community->deskripsi,ENT_QUOTES);

    if(file_exists(SENEROOT.$community->b_user_image_starter) && $community->b_user_image_starter != 'media/user/default.png'){
      $community->b_user_image_starter = $this->cdn_url($community->b_user_image_starter);
    } else {
      $community->b_user_image_starter = $this->cdn_url('media/user/default-profile-picture.png');
    }

    if($community->top_like_image_1 > 0){
      $community->top_like_image_1 = $this->cdn_url("media/icon/like-62-1-141111.png");
    }

    $community->images = array();
    $community->locations = array();
    $community->videos = array();
    $attachments = $this->ccam->getByCommunityId($nation_code, $community->id);
    foreach ($attachments as $atc) {
      if($atc->jenis == 'image'){
        $atc->url = $this->cdn_url($atc->url);
        $atc->url_thumb = $this->cdn_url($atc->url_thumb);
        $community->images[] = $atc;
      }else if($atc->jenis == 'video'){
        $atc->url = $this->cdn_url($atc->url);
        $atc->url_thumb = $this->cdn_url($atc->url_thumb);
        $community->videos[] = $atc;
      }else{
        $community->locations[] = $atc;
      }
    }
    unset($attachments);

    $data['community'] = $community;

    //render output
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
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
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      // $this->status = 401;
      // $this->message = 'Missing or invalid API session';
      // $this->__json_out($data);
      // die();

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

    $community_id = $this->input->post('community_id');
    if (empty($community_id)) {
      $this->status = 595;
      $this->message = 'Invalid community ID or Community not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $community = $this->ccomm->getById($nation_code, $community_id, $pelanggan);
    if (!isset($community->id)) {
      $this->status = 595;
      $this->message = 'Invalid community ID or Community not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    if (!isset($_FILES[$keyname])) {
      $this->status = 1300;
      $this->message = 'Upload failed';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    if ($_FILES[$keyname]['size']<=0) {
      $this->status = 1300;
      $this->message = 'Upload failed';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    if ($_FILES[$keyname]['size'] > 104857600) {
      $this->status = 1308;
      $this->message = 'Video file Size too big, max size 100 MB';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
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

    $this->seme_log->write("api_mobile", 'community_id '.$community_id);
    $this->seme_log->write("api_mobile", 'extension '.$fileext);

    // if (!in_array($fileext, array("mp4"))) {
    //   $this->status = 1305;
    //   $this->message = 'Invalid file extension, please try other file';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
    //   die();
    // }

    $targetdir = $this->media_community_video;
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

    $cpfm = $this->ccam->getByIdCommunityId($nation_code, $community_id, $video_id, "video", "uploading");
    if (!isset($cpfm->id)) {
      $this->status = 1307;
      $this->message = 'There is no reserve attachment for video';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $data['video_id'] = $cpfm->id;

    $filename = "$nation_code-$community_id-$cpfm->id-".date('YmdHis');
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
        $this->ccam->update($nation_code, $community_id, $cpfm->id, "video", $dix);

        $data['video_url'] = $this->cdn_url($data['video_url']);
        // $data['video_thumb_url'] = $this->cdn_url($data['video_thumb_url']);
        $data['video_thumb_url'] = $this->cdn_url($cpfm->url_thumb);

        if($cpfm->url != $this->media_community_video."default.png"){
          $file_path = SENEROOT.$cpfm->url;
          if (file_exists($file_path)) {
            unlink($file_path);
          }
        }

        //START by Donny Dennison 25 july 2022 - 16:48
        //change point get rule for upload video community
        $totalVideo = $this->ccam->countByCommunityIdJenisConvertStatusNotEqual($nation_code, $community_id, "video", "uploading");
        if($totalVideo == 1){
          $owner = $this->bu->getById($nation_code, $community->b_user_id_starter);

          //START by Donny Dennison 12 December 2022 - 15:24
          //Set daily limit to Video registration : 10 to Community posts and 15 to products
          //get limit left
          $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $owner->id, "E9");
          if(!isset($limitLeft->limit_plus)){
            $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E9");

            $lastID = $this->glplm->getLastId($nation_code, date("Y-m-d"));

            $du = array();
            $du['nation_code'] = $nation_code;
            $du['id'] = $lastID;
            $du['cdate'] = date("Y-m-d");
            $du['b_user_id'] = $owner->id;
            $du['code'] = "E9";
            $du['limit_plus'] = $pointGet->remark;
            $du['limit_minus'] = $pointGet->remark;
            $this->glplm->set($du);

            //get limit left
            $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $owner->id, "E9");
          }

          if($limitLeft->limit_plus > 0){
            $PointCode = "EP";
            $custom_type_sub = "upload video";
            if ($community->is_double_spt == "1") {
              $PointCode = "E16";
              $custom_type_sub = "upload video(double point)";
            }

            //get point
            $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $PointCode);
            if (!isset($pointGet->remark)) {
                $pointGet = new stdClass();
                $pointGet->remark = 10;
            }

            $di = array();
            $di['nation_code'] = $nation_code;
            $di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
            $di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
            $di['b_user_alamat_location_kabkota'] = $community->kabkota;
            $di['b_user_alamat_location_provinsi'] = $community->provinsi;
            $di['b_user_id'] = $owner->id;
            $di['point'] = $pointGet->remark;
            $di['custom_id'] = $community_id;
            $di['custom_type'] = 'community';
            $di['custom_type_sub'] = $custom_type_sub;
            $di['custom_text'] = $owner->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
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
            $this->glplm->updateTotal($nation_code, date("Y-m-d"), $owner->id, 'E9', 'limit_plus', '-', 1);
          }
          //END by Donny Dennison 12 December 2022 - 15:24
          //Set daily limit to Video registration : 10 to Community posts and 15 to products
        }
        //END by Donny Dennison 25 july 2022 - 16:48
        //change point get rule for upload video community
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
            $this->ccam->update($nation_code, $community_id, $cpfm->id, "video", $dix);

            $this->seme_log->write("api_mobile", 'tmp url moved');
          }
        }else{
          $this->seme_log->write("api_mobile", 'tmp url gone');
        }

        $this->status = 1306;
        $this->message = 'move upload file failed';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
        die();
      }

      $this->status = 200;
      $this->message = 'Success';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
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
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }
    
    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      // $this->status = 401;
      // $this->message = 'Missing or invalid API session';
      // $this->__json_out($data);
      // die();
      
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

    $community_id = $this->input->post('community_id');
    $community = $this->ccomm->getById($nation_code, $community_id, $pelanggan);
    if (!isset($community->id)) {
      // $this->status = 595;
      // $this->message = 'Invalid product ID or Product not found';
      $this->status = 200;
      $this->message = 'Success';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $cpfm_id = $this->input->post('cpfm_id');
    $cpfm = $this->ccam->getByIdCommunityId($nation_code, $community_id, $cpfm_id, "video");
    if (!isset($cpfm->id)) {
      $this->status = 200;
      $this->message = 'Success';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
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

    $this->ccam->delByIdCommunityId($nation_code, $cpfm_id, $community_id, "video");

    // //START by Donny Dennison 25 july 2022 - 16:48
    // //change point get rule for upload video community
    // $totalVideo = $this->ccam->countByCommunityIdJenisConvertStatusNotEqual($nation_code, $community_id, "video", "uploading");
    // if($totalVideo == 0){
    //   $owner = $this->bu->getById($nation_code, $community->b_user_id_starter);

    //   //START by Donny Dennison 12 December 2022 - 15:24
    //   //Set daily limit to Video registration : 10 to Community posts and 15 to products
    //   //get limit left
    //   $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $owner->id, "E9");
    //   if(!isset($limitLeft->limit_minus)){
    //     $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E9");

    //     $lastID = $this->glplm->getLastId($nation_code, date("Y-m-d"));

    //     $du = array();
    //     $du['nation_code'] = $nation_code;
    //     $du['id'] = $lastID;
    //     $du['cdate'] = date("Y-m-d");
    //     $du['b_user_id'] = $owner->id;
    //     $du['code'] = "E9";
    //     $du['limit_plus'] = $pointGet->remark;
    //     $du['limit_minus'] = $pointGet->remark;
    //     $this->glplm->set($du);

    //     //get limit left
    //     $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $owner->id, "E9");
    //   }

    //   if($limitLeft->limit_minus > 0){
    //     //get point
    //     $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EP");
    //     if (!isset($pointGet->remark)) {
    //         $pointGet = new stdClass();
    //         $pointGet->remark = 10;
    //     }

    //     $di = array();
    //     $di['nation_code'] = $nation_code;
    //     $di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
    //     $di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
    //     $di['b_user_alamat_location_kabkota'] = $community->kabkota;
    //     $di['b_user_alamat_location_provinsi'] = $community->provinsi;
    //     $di['b_user_id'] = $owner->id;
    //     $di['plusorminus'] = "-";
    //     $di['point'] = $pointGet->remark;
    //     $di['custom_id'] = $community_id;
    //     $di['custom_type'] = 'community';
    //     $di['custom_type_sub'] = 'upload video';
    //     $di['custom_text'] = $owner->fnama.' has delete '.$di['custom_type_sub'].' '.$di['custom_type'].' and lose '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
    //     $this->glphm->set($di);
    //     // $this->glrm->updateTotal($nation_code, $owner->id, 'total_point', '-', $di['point']);
    //     $this->glplm->updateTotal($nation_code, date("Y-m-d"), $owner->id, 'E9', 'limit_minus', '-', 1);
    //     $this->glplm->updateTotal($nation_code, date("Y-m-d"), $owner->id, 'E9', 'limit_plus', '+', 1);
    //   }
    //   //END by Donny Dennison 12 December 2022 - 15:24
    //   //Set daily limit to Video registration : 10 to Community posts and 15 to products
    // }
    // //END by Donny Dennison 25 july 2022 - 16:48
    // //change point get rule for upload video community

    $this->status = 200;
    $this->message = 'Success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  }

  public function report()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['community'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //populate input get
    $community_id = $this->input->get("community_id");
    $community = $this->ccomm->getById($nation_code, $community_id, $pelanggan);
    if (!isset($community->id)) {
      $this->status = 1160;
      $this->message = 'This post is deleted by an author';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //start transaction and lock table
    $this->ccrm->trans_start();

    //initial insert with latest ID
    $di = array();
    $di['nation_code'] = $nation_code;
    $di['c_community_id'] = $community_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['cdate'] = 'NOW()';
    $res = $this->ccrm->set($di);
    if (!$res) {
      $this->ccrm->trans_rollback();
      $this->ccrm->trans_end();
      $this->status = 1108;
      $this->message = "Error while report community, please try again later";
      $this->seme_log->write("api_mobile", 'API_Mobile/Community::report -- RollBack -- forceClose '.$this->status.' '.$this->message);
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $this->ccrm->trans_commit();
    $this->status = 200;
    // $this->message = 'This case is reported to SellOn Support';
    $this->message = 'Success';
    $this->seme_log->write("api_mobile", 'API_Mobile/Community::report -- INFO '.$this->status.' '.$this->message);
    
    //end transaction
    $this->ccrm->trans_end();

    //update is_report and report_date
    $di = array();
    $di['report_date'] = 'NOW()';
    $di['is_report'] = 1;
    $this->ccomm->update($nation_code, $community_id, $di);

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  }

  //START by Donny Dennison 29 august 2022 14:31
  //new point rule(2 points for Community Share, limit 10 share per day)
  public function countshared()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    // $data['produk'] = new stdClass();

    $this->seme_log->write("api_mobile", "API_Mobile/Community/countshared:: --GET: ".json_encode($_GET));

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $this->status = 300;
    $this->message = 'Missing one or more parameters';
   
    $community_id = $this->input->get('community_id');
    if ($community_id<='0') {
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $community = $this->ccomm->getById($nation_code, $community_id, $pelanggan);
    if (!isset($community->id)) {
      $this->status = 1160;
      $this->message = 'This post is deleted by an author';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //start transaction and lock table
    $this->ccshm->trans_start();

    //get last id for first time
    $ccshm_id = $this->ccshm->getLastId($nation_code,$community_id);

    //initial insert with latest ID
    $di = array();
    $di['nation_code'] = $nation_code;
    $di['id'] = $ccshm_id;
    $di['c_community_id'] = $community_id;
    $di['b_user_id'] = $pelanggan->id;
    $res = $this->ccshm->set($di);
    if (!$res) {
      $this->ccshm->trans_rollback();
      $this->ccshm->trans_end();
      $this->status = 903;
      $this->message = "Error while save, please try again later";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $this->status = 200;
    $this->message = "Success";
    $this->ccshm->trans_commit();
    $this->ccshm->trans_end();

    $newUserFinishNewUserEvent= 1;
    if($pelanggan->cdate >= "2023-10-16" && $pelanggan->cdate <= "2023-10-31" && $pelanggan->b_user_id_recruiter == "0"){
      $eventProgress = $this->ccenum->getByUserid($nation_code, $pelanggan->id);
      if(!isset($eventProgress->id)){
        $newUserFinishNewUserEvent = 0;
      }
      if(isset($eventProgress->id)){
        if(!$eventProgress->cdate_day_3){
          $newUserFinishNewUserEvent = 0;
        }
      }
    }

    if($newUserFinishNewUserEvent == 1){
      $eventProgress = $this->ccertm->getByUserid($nation_code, $pelanggan->id);
      if(isset($eventProgress->id)){
        if($eventProgress->cdate_day_3 && !$eventProgress->cdate_day_4){
          if(date("Y-m-d", strtotime($eventProgress->cdate_day_3. " +1 day")) == date("Y-m-d")){
            $totalShare = $this->ccshm->countByCommunityidUserid($nation_code, $community_id, $pelanggan->id, date("Y-m-d"));
            if($totalShare == 1 && $pelanggan->id != $community->b_user_id_starter){
              if(!$eventProgress->task_day_4){
                $eventProgress->task_day_4= array();
              }else{
                $eventProgress->task_day_4 = json_decode($eventProgress->task_day_4);
              }
              $eventProgress->task_day_4[] = $community_id;

              $du = array();
              $du['task_day_4'] = json_encode($eventProgress->task_day_4);
              $du['cdate_day_4'] = count($eventProgress->task_day_4) == 3 ? 'NOW()' : 'null';
              $this->ccertm->update($nation_code, $eventProgress->id, $du);
            }
          }
        }
      }
    }

    //START by Donny Dennison - 13 october 2022 14:10
    //change point policy
    // $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

    // //get total share
    // $totalShare = $this->glphm->countAll($nation_code, "", "", "", "", $pelanggan->id, "+", "", "community", "share", date("Y-m-d"), "");

    // $limitShare = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EW");
    // if (!isset($limitShare->remark)) {
    //   $limitShare = new stdClass();
    //   $limitShare->remark = 10;
    // }

    // if($totalShare < $limitShare->remark){
    //   //get point
    //   $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EX");
    //   if (!isset($pointGet->remark)) {
    //     $pointGet = new stdClass();
    //     $pointGet->remark = 2;
    //   }

    //   $di = array();
    //   $di['nation_code'] = $nation_code;
    //   $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
    //   $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
    //   $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
    //   $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
    //   $di['b_user_id'] = $pelanggan->id;
    //   $di['point'] = $pointGet->remark;
    //   $di['custom_id'] = $community_id;
    //   $di['custom_type'] = 'community';
    //   $di['custom_type_sub'] = 'share';
    //   $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
    //   $this->glphm->set($di);
    //   // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '+', $di['point']);
    // }
    //END by Donny Dennison - 13 october 2022 14:10
    //change point policy

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  }
  //END by Donny Dennison 29 august 2022 14:31
  //new point rule(2 points for Community Share, limit 10 share per day)

  //by Donny Dennison - 3 august 2020 16:25
  // add QnA / discussion feature
  private function __sortColDiscussion($sort_col, $tbl_as, $tbl2_as)
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

  //by Donny Dennison - 3 august 2020 16:25
  // add QnA / discussion feature
  public function list_discussion()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    // $data['is_login'] = "0";
    // $data['is_saved'] = "0";
    $data['diskusi_total'] = 0;
    $data['diskusis'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
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

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //populate input get
    $sort_col = $this->input->get("sort_col");
    $sort_dir = $this->input->get("sort_dir");
    $page = $this->input->get("page");
    $page_size = $this->input->get("page_size");
    // $grid = $this->input->get("grid");
    // $keyword = trim($this->input->get("keyword"));
    //$kategori_id = $this->input->get("kategori_id");
    $parent_discussion_id = (int) $this->input->get("parent_discussion_id");
    $c_community_id = $this->input->get("c_community_id");
    $timezone = $this->input->get("timezone");
    
    if ($parent_discussion_id<=0) {
      $parent_discussion_id = 0;
    }
    if ($c_community_id<='0') {
      $c_community_id = 0;
    }

    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $community = $this->ccomm->getById($nation_code, $c_community_id, $pelanggan);
    if (!isset($community->id)) {
      $this->status = 1160;
      $this->message = 'This post is deleted by an author';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    // $kategori_id = ''; //not used
    // if (empty($kategori_id)) {
    //   $kategori_id="";
    // }

    // $harga_jual_mulai = (int)$this->input->get("harga_jual_mulai");
    // $harga_jual_sampai = (int) $this->input->get("harga_jual_sampai");

    //sanitize input
    $tbl_as = $this->ccdm->getTblAs();
    $tbl2_as = $this->ccdm->getTbl2As();
    $sort_col = $this->__sortColDiscussion($sort_col, $tbl_as, $tbl2_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    // $page_child_discussion = $this->__page(1);
    $page_size = $this->__pageSize($page_size);
    // $page_size_child_discussion = $this->__pageSize(1);

    //keyword
    // if (mb_strlen($keyword)>1) {
    //   //$keyword = utf8_encode(trim($keyword));
    //   $enc = mb_detect_encoding($keyword, 'UTF-8');
    //   if ($enc == 'UTF-8') {
    //   } else {
    //       $keyword = iconv($enc, 'ISO-8859-1//TRANSLIT', $keyword);
    //   }
    // } else {
    //   $keyword="";
    // }
    // $keyword = filter_var(strip_tags($keyword), FILTER_SANITIZE_SPECIAL_CHARS);
    // $keyword = substr($keyword, 0, 32);
    // if (isset($pelanggan->id)) {
    //   $data['is_login'] = "1";
    //   $bupm = $this->bupw->check($nation_code, $pelanggan->id, $keyword);
    //   if (isset($bupm->nation_code)) {
    //     $data['is_saved'] = "1";
    //   }
    //   if ($this->is_log) {
    //     $this->seme_log->write("api_mobile", "API_Mobile/Produ::index -> keyword lookup done for USERID: ".$pelanggan->id.", KEYWORD: ".$keyword.", SAVED: ".$data['is_saved']);
    //   }
    // }

    //advanced filter
    // $harga_jual_min = '';
    // if (isset($_GET['harga_jual_min'])) {
    //   $harga_jual_min = (int) $_GET['harga_jual_min'];
    //   if ($harga_jual_min<=-1) {
    //     $harga_jual_min = '';
    //   }
    // }
    // if ($harga_jual_min>0) {
    //   $harga_jual_min = (float) $harga_jual_min;
    // }

    // $harga_jual_max = (int) $this->input->get("harga_jual_max");
    // if ($harga_jual_max<=0) {
    //   $harga_jual_max = "";
    // }
    // if ($harga_jual_max>0) {
    //   $harga_jual_max = (float) $harga_jual_max;
    // }

    // $b_kondisi_ids = "";
    // if (isset($_GET['b_kondisi_ids'])) {
    //   $b_kondisi_ids = $_GET['b_kondisi_ids'];
    // }
    // if (strlen($b_kondisi_ids)>0) {
    //   $b_kondisi_ids = rtrim($b_kondisi_ids, ",");
    //   $b_kondisi_ids = explode(",", $b_kondisi_ids);
    //   if (count($b_kondisi_ids)) {
    //     $kons = array();
    //     foreach ($b_kondisi_ids as &$bks) {
    //       $bks = (int) trim($bks);
    //       if ($bks>0) {
    //         $kons[] = $bks;
    //       }
    //     }
    //     $b_kondisi_ids = $kons;
    //   } else {
    //     $b_kondisi_ids = array();
    //   }
    // } else {
    //   $b_kondisi_ids = array();
    // }

    // $b_kategori_ids = "";
    // if (isset($_GET['b_kategori_ids'])) {
    //   $b_kategori_ids = $_GET['b_kategori_ids'];
    // }
    // if (strlen($b_kategori_ids)>0) {
    //   $b_kategori_ids = rtrim($b_kategori_ids, ",");
    //   $b_kategori_ids = explode(",", $b_kategori_ids);
    //   if (count($b_kategori_ids)) {
    //     $kods = array();
    //     foreach ($b_kategori_ids as &$bki) {
    //       $bki = (int) trim($bki);
    //       if ($bki>0) {
    //         $kods[] = $bki;
    //       }
    //     }
    //     $b_kategori_ids = $kods;
    //   } else {
    //     $b_kategori_ids = array();
    //   }
    // } else {
    //   $b_kategori_ids = array();
    // }

    // $kecamatan = $this->input->get("kecamatan");
    // if (strlen($kecamatan)) {
    //   $kecamatan = "";
    // }

    if (isset($pelanggan->id)) {
      $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
    }else{
      $pelangganAddress = array();
    }

    //get produk data
    // $ddcount = $this->ccdm->countAll($nation_code,$parent_discussion_id, $c_community_id); //get total discussion without child
    $ddcount = $community->total_discussion; //get total discussion with child
    $ddata = $this->ccdm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $parent_discussion_id, $c_community_id, $pelanggan);
    foreach ($ddata as &$dd) {
      $dd->can_chat_and_like = "0";
      // if(isset($pelanggan->id) && isset($pelangganAddress->alamat2)){
      if(isset($pelanggan->id)){
        // if($dd->postal_district == $pelangganAddress->postal_district){
          $dd->can_chat_and_like = "1";
        // }
      }

      $dd->is_owner_reply = "0";
      $dd->is_liked = '0';
      $dd->is_disliked = '0';
      if(isset($pelanggan->id)){
        if($dd->b_user_id == $pelanggan->id){
          $dd->is_owner_reply = "1";
        }

        $checkLike = $this->cclm->getByCustomIdUserId($nation_code, "community_discussion", "like", $dd->id, $pelanggan->id);
        if(isset($checkLike->id)){
          $dd->is_liked = '1';
        }

        $checkDislike = $this->cclm->getByCustomIdUserId($nation_code, "community_discussion", "dislike", $dd->id, $pelanggan->id);
        if(isset($checkDislike->id)){
          $dd->is_disliked = '1';
        }
      }

      // $dd->cdate_text = $this->humanTiming($dd->cdate);
      $dd->cdate_text = $this->humanTiming($dd->cdate, null, $pelanggan->language_id);
      $dd->cdate = $this->customTimezone($dd->cdate, $timezone);
      $dd->text = html_entity_decode($dd->text,ENT_QUOTES);

      // by Muhammad Sofi - 27 October 2021 10:12
      // if user img & banner not exist or empty, change to default image
      // $dd->b_user_image = $this->cdn_url($dd->b_user_image);
      if(file_exists(SENEROOT.$dd->b_user_image) && $dd->b_user_image != 'media/user/default.png'){
        $dd->b_user_image = $this->cdn_url($dd->b_user_image);
      } else {
        $dd->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
      }

      if($dd->top_like_image_1 > 0){
        $dd->top_like_image_1 = $this->cdn_url("media/icon/like-62-1-141111.png");
      }

      $dd->images = new stdClass();
      $dd->locations = new stdClass();

      $attachments = $this->ccdam->getByDiscussionId($nation_code, $dd->id);
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

      $dd->diskusi_anak_total = $this->ccdm->countAll($nation_code,$dd->id, $c_community_id);
      
      // $dd->diskusi_anak = $this->ccdm->getAll($nation_code, $page_child_discussion, $page_size_child_discussion, $sort_col, $sort_dir, $dd->id, $c_community_id, $pelanggan);
      $dd->diskusi_anak = $this->ccdm->getAll($nation_code, 0, 0, $sort_col, 'ASC', $dd->id, $c_community_id, $pelanggan);
      foreach ($dd->diskusi_anak as &$value) {
        $value->can_chat_and_like = $dd->can_chat_and_like;

        $value->is_owner_reply = "0";
        $value->is_liked = '0';
        $value->is_disliked = '0';
        if(isset($pelanggan->id)){
          if($value->b_user_id == $pelanggan->id){
            $value->is_owner_reply = "1";
          }

          $checkLike = $this->cclm->getByCustomIdUserId($nation_code, "community_discussion", "like", $value->id, $pelanggan->id);
          if(isset($checkLike->id)){
            $value->is_liked = '1';
          }

          $checkDislike = $this->cclm->getByCustomIdUserId($nation_code, "community_discussion", "dislike", $value->id, $pelanggan->id);
          if(isset($checkDislike->id)){
            $value->is_disliked = '1';
          }
        }

        // $value->cdate_text = $this->humanTiming($value->cdate);
        $value->cdate_text = $this->humanTiming($value->cdate, null, $pelanggan->language_id);
        $value->cdate = $this->customTimezone($value->cdate, $timezone);
        $value->text = html_entity_decode($value->text,ENT_QUOTES);

        // by Muhammad Sofi - 27 October 2021 10:12
        // if user img & banner not exist or empty, change to default image
        // $value->b_user_image = $this->cdn_url($value->b_user_image);
        if(file_exists(SENEROOT.$value->b_user_image) && $value->b_user_image != 'media/user/default.png'){
          $value->b_user_image = $this->cdn_url($value->b_user_image);
        } else {
          $value->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
        }

        if($value->top_like_image_1 > 0){
          $value->top_like_image_1 = $this->cdn_url("media/icon/like-62-1-141111.png");
        }

        $value->images = new stdClass();
        $value->locations = new stdClass();

        $attachments = $this->ccdam->getByDiscussionId($nation_code, $value->id);
        foreach ($attachments as $atc) {
          if($atc->jenis == 'image'){
            $atc->url = $this->cdn_url($atc->url);
            $atc->url_thumb = $this->cdn_url($atc->url_thumb);
            $value->images = $atc;
          }else{
            $value->locations = $atc;
          }
        }
        unset($attachments);
      }
    }
    unset($dd); //free some memory

    //build result
    $data['diskusis'] = $ddata;
    $data['diskusi_total'] = $ddcount;

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  }

  //by Donny Dennison - 7 august 2020 10:40
  // add QnA / discussion feature
  public function detail_discussion() //deprecated 
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['diskusis'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
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

    //populate input get
    $sort_col = "cdate";
    $sort_dir = "asc";
    $page = $this->input->get("page");
    $page_size = $this->input->get("page_size");

    $discussion_id = (int) $this->input->get("discussion_id");
    $c_community_id = $this->input->get("c_community_id");
    if ($discussion_id<=0) {
      $discussion_id = 0;
    }
    if ($c_community_id<='0') {
      $c_community_id = 0;
    }

    $community = $this->ccomm->getById($nation_code, $c_community_id, $pelanggan);
    if (!isset($community->id)) {
      $this->status = 1160;
      $this->message = 'This post is deleted by an author';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //sanitize input
    $tbl_as = $this->ccdm->getTblAs();
    $tbl2_as = $this->ccdm->getTbl2As();
    $sort_col = $this->__sortColDiscussion($sort_col, $tbl_as, $tbl2_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

    $ddata = $this->ccdm->getbyDiscussionIDCommunityID($nation_code, $discussion_id, $c_community_id);

    if (isset($pelanggan->id)) {
      $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
    }else{
      $pelangganAddress = array();
    }

    $ddata->can_chat_and_like = "0";
    // if(isset($pelanggan->id) && isset($pelangganAddress->alamat2)){
    if(isset($pelanggan->id)){
      // if($ddata->postal_district == $pelangganAddress->postal_district){
        $ddata->can_chat_and_like = "1";
      // }
    }

    $ddata->is_owner_reply = "0";
    $ddata->is_liked = '0';
    $ddata->is_disliked = '0';
    if(isset($pelanggan->id)){
      if($ddata->b_user_id == $pelanggan->id){
        $ddata->is_owner_reply = "1";
      }

      $checkLike = $this->cclm->getByCustomIdUserId($nation_code, "community_discussion", "like", $ddata->id, $pelanggan->id);
      if(isset($checkLike->id)){
        $ddata->is_liked = '1';
      }

      $checkDislike = $this->cclm->getByCustomIdUserId($nation_code, "community_discussion", "dislike", $ddata->id, $pelanggan->id);
      if(isset($checkDislike->id)){
        $ddata->is_disliked = '1';
      }
    }

    $ddata->text = html_entity_decode($ddata->text,ENT_QUOTES);

    // by Muhammad Sofi - 27 October 2021 10:12
    // if user img & banner not exist or empty, change to default image
    // $ddata->b_user_image = $this->cdn_url($ddata->b_user_image);
    if(file_exists(SENEROOT.$ddata->b_user_image) && $ddata->b_user_image != 'media/user/default.png'){
      $ddata->b_user_image = $this->cdn_url($ddata->b_user_image);
    } else {
      $ddata->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
    }

    if($ddata->top_like_image_1 > 0){
      $ddata->top_like_image_1 = $this->cdn_url("media/icon/like-62-1-141111.png");
    }

    $ddata->images = new stdClass();
    $ddata->locations = new stdClass();
    $attachments = $this->ccdam->getByDiscussionId($nation_code, $ddata->discussion_id);
    foreach ($attachments as $atc) {
      if($atc->jenis == 'image'){
        $atc->url = $this->cdn_url($atc->url);
        $atc->url_thumb = $this->cdn_url($atc->url_thumb);
        $ddata->images = $atc;
      }else{
        $ddata->locations = $atc;
      }
    }
    unset($attachments);

    $ddata->diskusi_anak_total = $this->ccdm->countAll($nation_code,$discussion_id, $c_community_id);

    $ddata->diskusi_anak = $this->ccdm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $discussion_id, $c_community_id, $pelanggan);
    foreach ($ddata->diskusi_anak as &$value) {
      $value->can_chat_and_like = $ddata->can_chat_and_like;

      $value->is_owner_reply = "0";
      $value->is_liked = '0';
      $value->is_disliked = '0';
      if(isset($pelanggan->id)){
        if($value->b_user_id == $pelanggan->id){
          $value->is_owner_reply = "1";
        }

        $checkLike = $this->cclm->getByCustomIdUserId($nation_code, "community_discussion", "like", $value->id, $pelanggan->id);
        if(isset($checkLike->id)){
          $value->is_liked = '1';
        }

        $checkDislike = $this->cclm->getByCustomIdUserId($nation_code, "community_discussion", "dislike", $value->id, $pelanggan->id);
        if(isset($checkDislike->id)){
          $value->is_disliked = '1';
        }
      }

      $value->text = html_entity_decode($value->text,ENT_QUOTES);

      // by Muhammad Sofi - 27 October 2021 10:12
      // if user img & banner not exist or empty, change to default image
      // $value->b_user_image = $this->cdn_url($value->b_user_image);
      if(file_exists(SENEROOT.$value->b_user_image) && $value->b_user_image != 'media/user/default.png'){
        $value->b_user_image = $this->cdn_url($value->b_user_image);
      } else {
        $value->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
      }

      if($value->top_like_image_1 > 0){
        $value->top_like_image_1 = $this->cdn_url("media/icon/like-62-1-141111.png");
      }

      $value->images = new stdClass();
      $value->locations = new stdClass();
      $attachments = $this->ccdam->getByDiscussionId($nation_code, $value->id);
      foreach ($attachments as $atc) {
        if($atc->jenis == 'image'){
          $atc->url = $this->cdn_url($atc->url);
          $atc->url_thumb = $this->cdn_url($atc->url_thumb);
          $value->images = $atc;
        }else{
          $value->locations = $atc;
        }
      }
      unset($attachments);
    }

    //build result
    $data['diskusis'] = $ddata;

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  }

  //by Donny Dennison - 3 august 2020 16:25
  // add QnA / discussion feature
  public function add_discussion()
  {
    //initial
    $dt = $this->__init();
    //error_reporting(0);

    //default result
    $data = array();
    $data['total_discussion'] = 0;
    $data['diskusi'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $this->status = 300;
    $this->message = 'Missing one or more parameters';

    //collect product input
    $parent_discussion_id = (int) $this->input->post('parent_discussion_id');
    $text = trim($this->input->post('text'));
    $c_community_id = $this->input->post('c_community_id');
    // $b_user_alamat_id = (int) $this->input->post('b_user_alamat_id'); // by Muhammad Sofi - 10 November 2021 09:19
    $b_user_id_to = $this->input->post('b_user_id_to');
    $show_nama = $this->input->post('show_nama');
    $location_nama = $this->input->post("location_nama");
    $location_address = $this->input->post("location_address");
    $location_place_id = $this->input->post("location_place_id");
    $location_latitude = $this->input->post("location_latitude");
    $location_longitude = $this->input->post("location_longitude");
    $timezone = $this->input->post("timezone");

    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    //input validation
    if (empty($parent_discussion_id)) {
      $parent_discussion_id = 0;
    }

    // by Muhammad Sofi - 10 November 2021 09:19
    //validating user address
    // if ($b_user_alamat_id<=0) {
    //   $b_user_alamat_id = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id)->id;
    // }

    if ($b_user_id_to<='0') {
      $this->status = 1098;
      $this->message = 'Invalid b_user_id_to';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    if($show_nama == '' || $show_nama != 0 || $show_nama == "NULL") {
      $show_nama = 1;
    }

    //by Donny Dennison 24 february 2021 18:45
    //change ’ to ' in add & edit product name and description
    $text = str_replace('’',"'",$text);
    
    // $text = filter_var($text, FILTER_SANITIZE_STRING);
    $text = nl2br($text);

    //by Donny Dennison - 15 augustus 2020 15:09
    //bug fix \n (enter) didnt get remove
    $text = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $text);

    $text = str_replace("\\n", "<br />", $text);

    if (empty($c_community_id)) {
      $c_community_id = 0;
    }

    //validating
    if ($parent_discussion_id<0) {
      $this->status = 1099;
      $this->message = 'Invalid parent_discussion_id';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    if ($c_community_id<='0') {
      $this->status = 595;
      $this->message = 'Invalid Community ID or Community not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $community = $this->ccomm->getById($nation_code, $c_community_id, $pelanggan);
    if (!isset($community->id)) {
      $this->status = 1160;
      $this->message = 'This post is deleted by an author';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //START by Donny Dennison - 21 november 2022 10:02
    //new feature, block
    if($pelanggan->id != $community->b_user_id_starter){
      $blockDataCommunity = $this->cbm->getById($nation_code, 0, $pelanggan->id, "community", $community->id);
      $blockDataAccount = $this->cbm->getById($nation_code, 0, $community->b_user_id_starter, "account", $pelanggan->id);
      $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $community->b_user_id_starter);

      if(isset($blockDataCommunity->block_id) || isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){
        $this->status = 1005;
        $this->message = "You can no reply as you're blocked";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_blocked_reply");
        die();
      }
    }
    //END by Donny Dennison - 21 november 2022 10:02
    //new feature, block

    // $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
    // if(isset($pelangganAddress->alamat2)){
    //   if($community->postal_district == $pelangganAddress->postal_district){
    //   }else{
    //     $this->status = 1099;
    //     $this->message = 'You\'re not allowed to join Group Chat outside your neighborhood';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
    //     die();
    //   }
    // }else{
    //   $this->status = 1099;
    //   $this->message = 'You\'re not allowed to join Group Chat outside your neighborhood';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
    //   die();
    // }

    $user_type = 'follower';
    if($community->b_user_id_starter == $pelanggan->id){
      $user_type = 'starter';
    }
    
    // by Muhammad Sofi - 10 November 2021 09:19
    // get detail address
    $d_address = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
    if (!isset($d_address->id)) {
      $this->status = 916;
      $this->message = 'Please register your address first';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //start transaction and lock table
    $this->ccdm->trans_start();

    //get last id for first time
    $dis_id = $this->ccdm->getLastId($nation_code);

    //initial insert with latest ID
    $di = array();
    $di['nation_code'] = $nation_code;
    $di['id'] = $dis_id;
    $di['parent_c_community_discussion_id'] = $parent_discussion_id;
    $di['c_community_id'] = $c_community_id;
    $di['b_user_id'] = $pelanggan->id;
    // $di['b_user_alamat_id'] = $b_user_alamat_id; // by Muhammad Sofi - 10 November 2021 09:19
    $di['alamat2'] = $d_address->alamat2;
    $di['kelurahan'] = $d_address->kelurahan;
    $di['kecamatan'] = $d_address->kecamatan;
    $di['kabkota'] = $d_address->kabkota;
    $di['provinsi'] = $d_address->provinsi;
    $di['negara'] = $d_address->negara;
    $di['kodepos'] = $d_address->kodepos;
    $di['b_user_id_to'] = $b_user_id_to;
    $di['show_nama'] = $show_nama;
    $di['user_type'] = $user_type;
    $di['text'] = $text;
    $di['cdate'] = 'NOW()';
    $res = $this->ccdm->set($di);
    if (!$res) {
      $this->ccdm->trans_rollback();
      $this->ccdm->trans_end();
      $this->status = 1109;
      $this->message = "Error while posting reply, please try again later";
      $this->seme_log->write("api_mobile", 'API_Mobile/Community::Discussion::baru -- RollBack -- forceClose '.$this->status.' '.$this->message);
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $this->ccdm->trans_commit();
    $this->status = 200;
    // $this->message = 'Your writing is posted';
    $this->message = 'Success';

    if($b_user_id_to != $pelanggan->id){
      //START by Donny Dennison - 29 july 2022 13:22
      //new feature, block community post or account
      $blockDataCommunity = $this->cbm->getById($nation_code, 0, $b_user_id_to, "community", $c_community_id);
      $blockDataAccount = $this->cbm->getById($nation_code, 0, $b_user_id_to, "account", $pelanggan->id);
      $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $b_user_id_to);
      if(!isset($blockDataCommunity->block_id) && !isset($blockDataAccount->block_id) && !isset($blockDataAccountReverse->block_id)){
      //END by Donny Dennison - 29 july 2022 13:22
      //new feature, block community post or account

        // select fcm token
        $user = $this->bu->getById($nation_code, $b_user_id_to);

        $dpe = array();
        $dpe['nation_code'] = $nation_code;
        $dpe['b_user_id'] = $b_user_id_to;
        $dpe['id'] = $this->dpem->getLastId($nation_code, $b_user_id_to);
        $dpe['type'] = "community_discussion";
        if($user->language_id == 2) {
          $dpe['judul'] = "Balasan Baru";
          $dpe['teks'] =  "Anda mendapat balasan dari tetangga Anda (".html_entity_decode($community->title,ENT_QUOTES).")";
        } else {
          $dpe['judul'] = "New Reply";
          $dpe['teks'] =  "You got a reply from your neighbor (".html_entity_decode($community->title,ENT_QUOTES).")";
        }

        $dpe['gambar'] = 'media/pemberitahuan/community.png';
        $dpe['cdate'] = "NOW()";
        $extras = new stdClass();
        $extras->id = $c_community_id;
        $extras->title = $community->title;
        // $extras->harga_jual = $community->harga_jual;
        // $extras->foto = base_url().$community->thumb;
        if($user->language_id == 2) { 
          $extras->judul = "Balasan Baru";
          $extras->teks =  "Anda mendapat balasan dari tetangga Anda (".html_entity_decode($community->title,ENT_QUOTES).")";
        } else {
          $extras->judul = "New Reply";
          $extras->teks =  "You got a reply from your neighbor (".html_entity_decode($community->title,ENT_QUOTES).")";
        }
        
        $dpe['extras'] = json_encode($extras);
        $this->dpem->set($dpe);
        $this->ccdm->trans_commit();

        $classified = 'setting_notification_user';
        $code = 'U6';

        $receiverSettingNotif = $this->busm->getValue($nation_code, $b_user_id_to, $classified, $code);
        if (!isset($receiverSettingNotif->setting_value)) {
            $receiverSettingNotif->setting_value = 0;
        }

        if ($receiverSettingNotif->setting_value == 1 && $user->is_active == 1) {
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
            $title = "Balasan Baru";
            $message = "Anda mendapat balasan dari tetangga Anda (".html_entity_decode($this->convertEmoji($community->title),ENT_QUOTES).")";
          } else {
            $title = "New Reply";
            $message = "You got a reply from your neighbor (".html_entity_decode($this->convertEmoji($community->title),ENT_QUOTES).")";
          }
          
          $image = 'media/pemberitahuan/promotion.png';
          $type = 'community_discussion';
          $payload = new stdClass();
          $payload->id = $c_community_id;
          $payload->title = html_entity_decode($community->title,ENT_QUOTES);
          // $payload->harga_jual = $community->harga_jual;
          // $payload->foto = base_url().$community->thumb;
          if($user->language_id == 2) {
            $payload->judul = "Balasan Baru";
            //by Donny Dennison
            //dicomment untuk handle message too big, response dari fcm
            // $payload->teks = strip_tags(html_entity_decode($di['teks']));
            // $payload->teks = "You get a reply from your neighbors (".$tempTitle->{'title'}.")";
            $payload->teks = "Anda mendapat balasan dari tetangga Anda (".html_entity_decode($this->convertEmoji($community->title),ENT_QUOTES).")";
          } else {
            $payload->judul = "New Reply";
            $payload->teks = "You got a reply from your neighbor (".html_entity_decode($this->convertEmoji($community->title),ENT_QUOTES).")";
          }
          $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        }
      //START by Donny Dennison - 29 july 2022 13:22
      //new feature, block community post or account
      }
      //END by Donny Dennison - 29 july 2022 13:22
      //new feature, block community post or account
    }

    if(isset($_FILES['foto']['name'])){
      $photoId_last = $this->ccdam->getLastId($nation_code,$dis_id, 'image');

      $sc = $this->__uploadImagex($nation_code, 'foto', $dis_id, $photoId_last);
      if (isset($sc->status)) {
        if ($sc->status==200) {
          $dix = array();
          $dix['nation_code'] = $nation_code;
          $dix['c_community_discussion_id'] = $dis_id;
          $dix['id'] = $photoId_last;
          $dix['jenis'] = 'image';
          $dix['url'] = $sc->image;
          $dix['url_thumb'] = $sc->thumb;
          $this->ccdam->set($dix);
          $this->ccdm->trans_commit();
        }
      }
    }

    if($location_nama && $location_address && $location_place_id && $location_latitude && $location_longitude){
      //upload location and insert to c_community_attachment table
      $locationId_last = $this->ccdam->getLastId($nation_code,$dis_id, 'location');

      $dix = array();
      $dix['nation_code'] = $nation_code;
      $dix['c_community_discussion_id'] = $dis_id;
      $dix['id'] = $locationId_last;
      $dix['jenis'] = 'location';
      $dix['location_nama'] = $location_nama;
      $dix['location_address'] = $location_address;
      $dix['location_place_id'] = $location_place_id;
      $dix['location_latitude'] = $location_latitude;
      $dix['location_longitude'] = $location_longitude;
      $this->ccdam->set($dix);
      $this->ccdm->trans_commit();
    }

    // $this->seme_log->write("api_mobile", 'API_Mobile/Community::Discussion::baru -- INFO '.$this->status.' '.$this->message);
    $this->ccdm->trans_end();

    //update total_discussion in table c_community
    $this->ccomm->updateTotal($nation_code, $c_community_id, "total_discussion", '+', 1);

    //START by Donny Dennison - 16 december 2021 15:49
    //get point as leaderboard rule
    if($user_type == 'follower'){
      //get total active reply from $pelanggan
      $totalActiveReplyUser = $this->ccdm->countAllCommunityIDUserID($nation_code, $pelanggan->id, $c_community_id);
      if($totalActiveReplyUser == 1){
        //get limit left
        $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "EH");
        if(!isset($limitLeft->limit_plus)){
          $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EH");

          $lastID = $this->glplm->getLastId($nation_code, date("Y-m-d"));

          $du = array();
          $du['nation_code'] = $nation_code;
          $du['id'] = $lastID;
          $du['cdate'] = date("Y-m-d");
          $du['b_user_id'] = $pelanggan->id;
          $du['code'] = "EH";
          $du['limit_plus'] = $pointGet->remark;
          $du['limit_minus'] = $pointGet->remark;
          $this->glplm->set($du);

          //get limit left
          $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "EH");
        }

        if($limitLeft->limit_plus > 0){
          //get point
          $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EI");
          if (!isset($pointGet->remark)) {
            $pointGet = new stdClass();
            $pointGet->remark = 1;
          }

          $di = array();
          $di['nation_code'] = $nation_code;
          $di['b_user_alamat_location_kelurahan'] = $d_address->kelurahan;
          $di['b_user_alamat_location_kecamatan'] = $d_address->kecamatan;
          $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
          $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
          $di['b_user_id'] = $pelanggan->id;
          $di['point'] = $pointGet->remark;
          $di['custom_id'] = $c_community_id;
          $di['custom_type'] = 'community';
          $di['custom_type_sub'] = 'reply';
          $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
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
          $this->glplm->updateTotal($nation_code, date("Y-m-d"), $pelanggan->id, 'EH', 'limit_plus', '-', 1);
        }
      }
    }
    //END by Donny Dennison - 16 december 2021 15:49

    // //check group chat
    // if($community->is_group_chat == 1){
    //   $chat_room = $this->ecrm->getChatRoomByCommunityID($nation_code, $c_community_id);

    //   $participant_list = $this->ecpm->getParticipantByRoomChatId($nation_code,$chat_room->id);

    //   //check already in group chat or not
    //   $alreadyInGroupChat = 0;
    //   foreach($participant_list as $participantList){
    //       if($participantList->b_user_id == $pelanggan->id){
    //           $alreadyInGroupChat = 1;
    //           break;
    //       }
    //   }

    //   if($alreadyInGroupChat == 0 && count($participant_list) < 10){
    //     //insert chat participant
    //     $di = array();
    //     $di['nation_code'] = $nation_code;
    //     $di['e_chat_room_id'] = $chat_room->id;
    //     $di['b_user_id'] = $pelanggan->id;
    //     $di['cdate'] = 'NOW()';
    //     $this->ecpm->set($di);

    //     //create announcement
    //     $type = 'chat';
    //     $replacer = array();

    //     $replacer['user_nama'] = $pelanggan->fnama;
    //     $message = '';

    //     $nw = $this->anot->get($nation_code, "push", $type, 2);

    //     if (isset($nw->message)) {
    //       $message = $this->__nRep($nw->message, $replacer);
    //     }

    //     //get last chat id
    //     $chat_id = $this->chat->getLastId($nation_code, $chat_room->id);

    //     $di = array();
    //     $di['id'] = $chat_id;
    //     $di['nation_code'] = $nation_code;
    //     $di['e_chat_room_id'] = $chat_room->id;
    //     $di['b_user_id'] = 0;
    //     $di['type'] = 'announcement';
    //     $di['message'] = $message;
    //     $di['cdate'] = "NOW()";
    //     $this->chat->set($di);
    //   }
    // }

    $data['diskusi'] = $this->ccdm->getbyDiscussionIDCommunityID($nation_code, $dis_id, $c_community_id);
    $data['diskusi']->can_chat_and_like = "1";
    $data['diskusi']->is_owner_reply = "1";
    $data['diskusi']->is_liked = '0';
    $data['diskusi']->is_disliked = '0';
    // $data['diskusi']->cdate_text = $this->humanTiming($data['diskusi']->cdate);
    $data['diskusi']->cdate_text = $this->humanTiming($data['diskusi']->cdate, null, $pelanggan->language_id);
    $data['diskusi']->cdate = $this->customTimezone($data['diskusi']->cdate, $timezone);
    $data['diskusi']->text = html_entity_decode($data['diskusi']->text,ENT_QUOTES);

    // by Muhammad Sofi - 27 October 2021 10:12
    // if user img & banner not exist or empty, change to default image
    // $data['diskusi']->b_user_image = $this->cdn_url($data['diskusi']->b_user_image);
    if(file_exists(SENEROOT.$data['diskusi']->b_user_image) && $data['diskusi']->b_user_image != 'media/user/default.png'){
      $data['diskusi']->b_user_image = $this->cdn_url($data['diskusi']->b_user_image);
    } else {
      $data['diskusi']->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
    }

    if($data['diskusi']->top_like_image_1 > 0){
      $data['diskusi']->top_like_image_1 = $this->cdn_url("media/icon/like-62-1-141111.png");
    }

    $data['diskusi']->images = new stdClass();
    $data['diskusi']->locations = new stdClass();

    $attachments = $this->ccdam->getByDiscussionId($nation_code, $data['diskusi']->discussion_id);
    foreach ($attachments as $atc) {
      if($atc->jenis == 'image'){
        $atc->url = $this->cdn_url($atc->url);
        $atc->url_thumb = $this->cdn_url($atc->url_thumb);
        $data['diskusi']->images = $atc;
      }else{
        $data['diskusi']->locations = $atc;
      }
    }
    unset($attachments);

    $data['total_discussion'] = $this->ccomm->getById($nation_code, $c_community_id, $pelanggan)->total_discussion;

    if($parent_discussion_id != 0){
      $data['diskusi']->diskusi_anak_total = $this->ccdm->countAll($nation_code,$parent_discussion_id, $c_community_id);
    }else{
      $data['diskusi']->diskusi_anak_total = "0";
    }

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  }

  //by Donny Dennison - 7 august 2020 10:40
  // add QnA / discussion feature
  public function delete_discussion()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['total_discussion'] = 0;
    $data['diskusis'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //populate input get
    $discussion_id = (int) $this->input->get("discussion_id");

    //check discussion id and user id
    $c = $this->ccdm->getbyDiscussionIDUserID($nation_code, $discussion_id, $pelanggan->id);
    if (!isset($c->discussion_id)) {
      $this->status = 1114;
      $this->message = 'Discussion ID and User ID is different';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }
    
    //start transaction and lock table
    $this->ccdm->trans_start();

    $di = array();
    $di['edate'] = 'NOW()';
    $di['is_active'] = 0;
    $res = $this->ccdm->update($nation_code, $discussion_id, $di);
    if (!$res) {
      $this->ccdm->trans_rollback();
      $this->ccdm->trans_end();
      $this->status = 1110;
      $this->message = "Error while delete reply, please try again later";
      $this->seme_log->write("api_mobile", 'API_Mobile/Community::Discussion::hapus -- RollBack -- forceClose '.$this->status.' '.$this->message);
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $this->ccdm->trans_commit();
    $this->status = 200;
    // $this->message = 'Your reply is deleted';
    $this->message = 'Success';
    
    //update total_discussion in table c_community
    $this->ccomm->updateTotal($nation_code, $c->c_community_id, "total_discussion", '-', 1);
    $this->ccdm->trans_commit();

    //if discussion is a parent, child also deleted
    if($c->parent_c_community_discussion_id == 0){
      $getTotalChildIsActive = $this->ccdm->countAll($nation_code, $c->discussion_id, $c->c_community_id);
      
      //update total_discussion in table c_community
      $this->ccomm->updateTotal($nation_code, $c->c_community_id, "total_discussion", '-', $getTotalChildIsActive);
      $this->ccdm->trans_commit();

      $di = array();
      $di['edate'] = 'NOW()';
      $di['is_active'] = 0;
      $this->ccdm->updateByParentCommunityDiscussionId($nation_code, $discussion_id, $di);
      $this->ccdm->trans_commit();
    }

    $this->seme_log->write("api_mobile", 'API_Mobile/Community::Discussion::hapus -- INFO '.$this->status.' '.$this->message);

    //end transaction
    $this->ccdm->trans_end();

    //START by Donny Dennison - 16 december 2021 15:49
    //get point as leaderboard rule
    if($c->user_type == 'follower'){
      //get total active reply from $pelanggan
      $totalActiveReplyUser = $this->ccdm->countAllCommunityIDUserID($nation_code, $pelanggan->id, $c->c_community_id);
      if($totalActiveReplyUser == 0){
        //get limit left
        $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "EH");
        if(!isset($limitLeft->limit_minus)){
          $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EH");

          $lastID = $this->glplm->getLastId($nation_code, date("Y-m-d"));

          $du = array();
          $du['nation_code'] = $nation_code;
          $du['id'] = $lastID;
          $du['cdate'] = date("Y-m-d");
          $du['b_user_id'] = $pelanggan->id;
          $du['code'] = "EH";
          $du['limit_plus'] = $pointGet->remark;
          $du['limit_minus'] = $pointGet->remark;
          $this->glplm->set($du);

          //get limit left
          $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "EH");
        }

        if($limitLeft->limit_minus > 0){
          //get point
          $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EI");
          if (!isset($pointGet->remark)) {
            $pointGet = new stdClass();
            $pointGet->remark = 1;
          }

          $di = array();
          $di['nation_code'] = $nation_code;
          $di['b_user_alamat_location_kelurahan'] = $c->kelurahan;
          $di['b_user_alamat_location_kecamatan'] = $c->kecamatan;
          $di['b_user_alamat_location_kabkota'] = $c->kabkota;
          $di['b_user_alamat_location_provinsi'] = $c->provinsi;
          $di['b_user_id'] = $pelanggan->id;
          $di['plusorminus'] = "-";
          $di['point'] = $pointGet->remark;
          $di['custom_id'] = $c->c_community_id;
          $di['custom_type'] = 'community';
          $di['custom_type_sub'] = 'reply';
          $di['custom_text'] = $pelanggan->fnama.' has delete '.$di['custom_type'].' '.$di['custom_type_sub'].' and lose '.$di['point'].' point(s)';
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
          // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
          $this->glplm->updateTotal($nation_code, date("Y-m-d"), $pelanggan->id, 'EH', 'limit_minus', '-', 1);
          $this->glplm->updateTotal($nation_code, date("Y-m-d"), $pelanggan->id, 'EH', 'limit_plus', '+', 1);
        }
      }
    }
    //END by Donny Dennison - 16 december 2021 15:49

    $data['total_discussion'] = $this->ccomm->getById($nation_code, $c->c_community_id, $pelanggan);
    if(isset($data['total_discussion']->total_discussion)){
      $data['total_discussion'] = $data['total_discussion']->total_discussion;
    }else{
      $data['total_discussion'] = 0;
    }

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
  }

  //by Donny Dennison - 7 august 2020 10:40
  // add QnA / discussion feature
  public function report_discussion()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['diskusis'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //populate input get
    $discussion_id = (int) $this->input->get("discussion_id");

    //start transaction and lock table
    $this->ccdrm->trans_start();

    //initial insert with latest ID
    $di = array();
    $di['nation_code'] = $nation_code;
    $di['c_community_discussion_id'] = $discussion_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['cdate'] = 'NOW()';
    $res = $this->ccdrm->set($di);
    if (!$res) {
      $this->ccdrm->trans_rollback();
      $this->ccdrm->trans_end();
      $this->status = 1112;
      $this->message = "Error while report discussion, please try again later";
      $this->seme_log->write("api_mobile", 'API_Mobile/Community::Discussion::report -- RollBack -- forceClose '.$this->status.' '.$this->message);
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $this->ccdrm->trans_commit();
    $this->status = 200;
    // $this->message = 'This case is reported to SellOn Support';
    $this->message = 'Success';
    
    $this->seme_log->write("api_mobile", 'API_Mobile/Community::Discussion::report -- INFO '.$this->status.' '.$this->message);

    //end transaction
    $this->ccdrm->trans_end();

    //update is_report and report_date
    $di = array();
    $di['report_date'] = 'NOW()';
    $di['is_report'] = 1;
    $this->ccdm->update($nation_code, $discussion_id, $di);

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");

  }
  public function incrementVideoView()
  {
      $dt = $this->__init();

      //default result
      $data = array();

      //check nation_code
      $nation_code = $this->input->get('nation_code');
      $nation_code = $this->nation_check($nation_code);
      $video_id = $this->input->post("video_id");

      if (empty($nation_code)) {
          $this->status = 101;
          $this->message = 'Missing or invalid nation_code';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
          die();
      }

        //check apikey
      $apikey = $this->input->get('apikey');
      $c = $this->apikey_check($apikey);
      if (!$c) {
          $this->status = 400;
          $this->message = 'Missing or invalid API key';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
          die();
      }

      $cc_id = $this->input->post("c_community_id");
      if (!isset($cc_id)) {
          $this->status = 101;
          $this->message = 'c_community_id cannot be null';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      }

      $this->ccomm->incrementView($nation_code, $cc_id, $video_id, "total_views", '+', 1);

      $this->status = 200;
      // $this->message = 'This case is reported to SellOn Support';
      $this->message = 'Success';

      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
    }
}
