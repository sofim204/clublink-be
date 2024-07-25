<?php
class Produk extends JI_Controller
{
  public $is_soft_delete=1;
  public $is_log = 1;
  public $imgQueue;

  //by Donny Dennison 16 augustus 2020 00:25
  //fix check emoji in insert & edit product and discussion
  //credit : https://stackoverflow.com/questions/41580483/detect-emoticons-in-string
  public $unicodeRegexp = '([*#0-9](?>\\xEF\\xB8\\x8F)?\\xE2\\x83\\xA3|\\xE2..(\\xF0\\x9F\\x8F[\\xBB-\\xBF])?(?>\\xEF\\xB8\\x8F)?|\\xE3(?>\\x80[\\xB0\\xBD]|\\x8A[\\x97\\x99])(?>\\xEF\\xB8\\x8F)?|\\xF0\\x9F(?>[\\x80-\\x86].(?>\\xEF\\xB8\\x8F)?|\\x87.\\xF0\\x9F\\x87.|..(\\xF0\\x9F\\x8F[\\xBB-\\xBF])?|(((?<zwj>\\xE2\\x80\\x8D)\\xE2\\x9D\\xA4\\xEF\\xB8\\x8F\k<zwj>\\xF0\\x9F..(\k<zwj>\\xF0\\x9F\\x91.)?|(\\xE2\\x80\\x8D\\xF0\\x9F\\x91.){2,3}))?))';

  public function __construct()
  {
    parent::__construct();
    //$this->setTheme('frontx');
    $this->lib("seme_log");
    $this->lib("seme_purifier");
    $this->load("api_mobile/a_notification_model", "anot");
    $this->load("api_mobile/b_user_model", "bu");
    $this->load("api_mobile/b_user_alamat_model", "bua");
    $this->load("api_mobile/b_user_setting_model", "busm");
    $this->load("api_mobile/b_user_productwanted_model", "bupw");
    $this->load("api_mobile/b_kategori_model3", "bkm3");
    $this->load("api_mobile/b_kondisi_model", "bkon");
    $this->load("api_mobile/b_berat_model", "brt");
    $this->load("api_mobile/c_produk_model", "cpm");
    $this->load("api_mobile/c_produk_detail_automotive_model", "cpdam");
    $this->load("api_mobile/c_produk_foto_model", "cpfm");
    $this->load("api_mobile/common_code_model", "ccm");
    $this->load("api_mobile/d_wishlist_model", "dwlm");
    $this->load("api_mobile/d_cart_model", "cart");
    $this->load("api_mobile/d_pemberitahuan_model", "dpem");
    $this->load("api_mobile/f_discussion_model", "fdis");
    $this->load("api_mobile/f_discussion_report_model", "fdisrep");

    //by Donny Dennison - 27 agustus 2020
    // add seller data in response
    $this->load("api_mobile/a_negara_model", 'anm');

    //by Donny Dennison - 2 july 2021 9:37
    //move-campaign-to-sponsored
    $this->load("api_mobile/c_promo_model", "cp2");

    //by Donny Dennison - 22 september 2021 15:01
    //revamp-profile
    $this->load("api_mobile/b_user_wish_product_model", "buwp");

    //by Donny Dennison - 10 december 2021 13:36
    //add feature hot item di homepage
    $this->load("api_mobile/c_product_share_history_model", "cpshm");

    //by Donny Dennison - 16 december 2021 15:49
    //get point as leaderboard rule
    // $this->load("api_mobile/g_leaderboard_point_area_model", 'glpam');
    $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
    // $this->load("api_mobile/g_leaderboard_ranking_model", 'glrm');
    $this->load("api_mobile/g_leaderboard_point_limit_model", 'glplm');

    //by Donny Dennison - 12 july 2022 14:56
    //new offer system
    $this->load("api_mobile/e_chat_room_model", 'ecrm');
    $this->load("api_mobile/e_chat_participant_model", 'ecpm');

    //by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account
    $this->load("api_mobile/c_block_model", "cbm");

    //by Donny Dennison - 21 december 2022 14:27
    //improve product list api
    $this->load("api_mobile/d_order_detail_model", 'dodm');
    $this->load("api_mobile/e_rating_model", 'erm');
    $this->load("api_mobile/g_daily_track_record_model", 'gdtrm');

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

  private function __floatWeight($val)
  {
    $val = (float) $val;
    return ''.round($val, 2);
  }
  private function __getDimensionMax($long, $width, $height)
  {
    $max = $long;
    if ($max<$width) {
      $max = $width;
    }
    if ($max<$height) {
      $max = $height;
    }
    return $max;
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

  //       if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/Produk::__checkUploadedFile -- INFO keyname: '.$keyname.' RESULT: TRUE');
  //       return true;
  //     }
  //   }
  //   if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/Produk::__checkUploadedFile -- INFO keyname: '.$keyname.' RESULT: FALSE');
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
  // private function __uploadImagex($nation_code, $keyname, $produk_id="0", $ke="")
  // {
  //   $sc = new stdClass();
  //   $sc->status = 500;
  //   $sc->message = 'Error';
  //   $sc->image = '';
  //   $sc->thumb = '';
  //   $produk_id = (int) $produk_id;
  //   if (isset($_FILES[$keyname]['name'])) {

  //     //by Donny Dennison - 30 november 2021 14:59
  //     //comment check size uploaded file
  //     // if ($_FILES[$keyname]['size']>2000000) {
  //     //   $sc->status = 301;
  //     //   $sc->message = 'Image file Size too big, please try again';
  //     //   return $sc;
  //     // }
      
  //     $filenames = pathinfo($_FILES[$keyname]['name']);
  //     if (isset($filenames['extension'])) {
  //       $fileext = strtolower($filenames['extension']);
  //     } else {
  //       $fileext = '';
  //     }

  //     if (!in_array($fileext, array("jpg","png","jpeg"))) {
  //       $sc->status = 303;
  //       $sc->message = 'Invalid file extension, please try other file.';
  //       return $sc;
  //     }
  //     $filename = "$nation_code-$produk_id-$ke";
  //     $filethumb = $filename.'-thumb';

  //     $targetdir = $this->media_produk;
  //     $targetdircheck = realpath(SENEROOT.$targetdir);
  //     if (empty($targetdircheck)) {
  //       if (PHP_OS == "WINNT") {
  //         if (!is_dir(SENEROOT.$targetdir)) {
  //           mkdir(SENEROOT.$targetdir);
  //         }
  //       } else {
  //         if (!is_dir(SENEROOT.$targetdir)) {
  //           mkdir(SENEROOT.$targetdir, 0775);
  //         }
  //       }
  //     }

  //     $tahun = date("Y");
  //     $targetdir = $targetdir.DIRECTORY_SEPARATOR.$tahun;
  //     $targetdircheck = realpath(SENEROOT.$targetdir);
  //     if (empty($targetdircheck)) {
  //       if (PHP_OS == "WINNT") {
  //         if (!is_dir(SENEROOT.$targetdir)) {
  //           mkdir(SENEROOT.$targetdir);
  //         }
  //       } else {
  //         if (!is_dir(SENEROOT.$targetdir)) {
  //           mkdir(SENEROOT.$targetdir, 0775);
  //         }
  //       }
  //     }

  //     $bulan = date("m");
  //     $targetdir = $targetdir.DIRECTORY_SEPARATOR.$bulan;
  //     $targetdircheck = realpath(SENEROOT.$targetdir);
  //     if (empty($targetdircheck)) {
  //       if (PHP_OS == "WINNT") {
  //         if (!is_dir(SENEROOT.$targetdir)) {
  //           mkdir(SENEROOT.$targetdir);
  //         }
  //       } else {
  //         if (!is_dir(SENEROOT.$targetdir)) {
  //           mkdir(SENEROOT.$targetdir, 0775);
  //         }
  //       }
  //     }

  //     $sc->status = 998;
  //     $sc->message = 'Invalid file extension uploaded';
  //     if (in_array($fileext, array("jpg", "png","jpeg"))) {
  //       $filecheck = SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename.'.'.$fileext;
  //       if (file_exists($filecheck)) {
  //         unlink($filecheck);
  //         $rand = rand(0, 999);
  //         $filename = "$nation_code-$produk_id-$ke-".$rand;
  //         $filecheck = SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename.'.'.$fileext;
  //         if (file_exists($filecheck)) {
  //           unlink($filecheck);
  //           $rand = rand(1000, 99999);
  //           $filename = "$nation_code-$produk_id-$ke-".$rand;
  //         }
  //       }
  //       $filethumb = $filename."-thumb.".$fileext;
  //       $filename = $filename.".".$fileext;

  //       move_uploaded_file($_FILES[$keyname]['tmp_name'], SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
  //       if (is_file(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename) && file_exists(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)) {
  //         if (@mime_content_type(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename) == 'image/webp') {
  //           $sc->status = 302;
  //           $sc->message = 'WebP image format currently unsupported';
  //           return $sc;
  //         }
  //         if (file_exists(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb) && is_file(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb)) {
  //           unlink(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
  //         }
  //         if (file_exists(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename) && is_file(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)) {
  //           $this->lib("wideimage/WideImage", 'wideimage', "inc");
  //           WideImage::load(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)->reSize(370)->saveToFile(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
  //           $sc->status = 200;
  //           $sc->message = 'Success';
  //           $sc->thumb = str_replace("//", "/", $targetdir.'/'.$filethumb);
  //           $sc->image = str_replace("//", "/", $targetdir.'/'.$filename);
  //         } else {
  //           $sc->status = 997;
  //           $sc->message = 'Failed';
  //         }
  //       } else {
  //         $sc->status = 999;
  //         $sc->message = 'Failed';
  //       }
  //     } else {
  //       $sc->status = 998;
  //       $sc->message = 'Invalid file extension uploaded';
  //     }
  //   } else {
  //     $sc->status = 988;
  //     $sc->message = 'Keyname file does not exists';
  //   }
  //   if ($this->is_log) {
  //     $this->seme_log->write("api_mobile", 'API_Mobile/Produk::__uploadImagex -- INFO KeyName: '.$keyname.' PID:'.$produk_id.' ke:'.$ke.' '.$sc->status.' '.$sc->message.'');
  //   }
  //   return $sc;
  // }

  private function __moveImagex($nation_code, $url, $produk_id="0", $ke="")
  {
    $sc = new stdClass();
    $sc->status = 500;
    $sc->message = 'Error';
    $sc->image = '';
    $sc->thumb = '';
    // $produk_id = (int) $produk_id;

    $targetdir = $this->media_produk;
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
      $this->seme_log->write("api_mobile", 'API_Mobile/Produk::__moveImagex -- INFO URL: '.$url.' PID:'.$produk_id.' ke:'.$ke.' '.$sc->status.' '.$sc->message.'');
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

  private function __sortCol($sort_col, $tbl_as, $tbl2_as)
  {
    switch ($sort_col) {
      case 'id':
      $sort_col = "$tbl_as.id";
      break;
      // case 'kondisi':
      // $sort_col = "$tbl_as.b_kondisi_id";
      // break;
      case 'harga':
      $sort_col = "$tbl_as.harga_jual";
      break;
      case 'harga_jual':
      $sort_col = "$tbl_as.harga_jual";
      break;
      case 'nama':
      $sort_col = "$tbl_as.nama";
      break;
      case 'stok':
      $sort_col = "$tbl_as.stok";
      break;

      //by Donny Dennison - 2 march 2021 11:35
      //list-produt-sameStreet-neighborhood-all-from-user-address
      case 'cdate':
      $sort_col = "$tbl_as.cdate";
      break;

      //by Donny Dennison - 1 desember 2020 16:29
      //list-produt-sameStreet-neighborhood-all-from-user-address
      // case 'ldate':
      // $sort_col = "$tbl_as.ldate";
      // break;

      //by Donny Dennison - 3 desember 2020 15:31
      //list-produt-sameStreet-neighborhood-all-from-user-address
      case 'kodepos':
      $sort_col = "$tbl_as.kodepos";
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

  /**
  * Process the uploaded file, if failed go to queue (BUGGY)
  * @param  int      $nation_code    nation code
  * @param  object   $pelanggan      object from b_user
  * @param  integer  $cpm_id         id for c_produk
  * @param  string   $keyImg         image key
  * @param  integer  $cpfm_id        id for c_produk_foto table
  * @param  integer  $img_count      image counter
  * @return object                   process result object
  */
  // private function __processUpload($nation_code, $pelanggan, $cpm_id, $keyImg, $cpfm_id=1, $img_count=0)
  // {
  //   $ir = new stdClass();
  //   $ir->sc = new stdClass();
  //   $ir->sc->status = 999;
  //   $ir->sc->message = "Uninitialized process";
  //   $ir->img_count = $img_count;
  //   $ir->cpfm_id = $cpfm_id;

  //   $sc = $this->__uploadImagex($nation_code, $keyImg, $cpm_id, $cpfm_id);
  //   if (isset($sc->status)) {
  //     $ir->sc = $sc;
  //     if ($sc->status==200) {
  //       $dix = array();
  //       $dix['nation_code'] = $nation_code;
  //       $dix['c_produk_id'] = $cpm_id;
  //       $dix['id'] = $cpfm_id;
  //       $dix['url'] = $sc->image;
  //       $dix['url_thumb'] = $sc->thumb;
  //       $dix['is_active'] = 1;
  //       $dix['caption'] = '';
  //       $res = $this->cpfm->set($dix);
  //       if ($res) {
  //         $this->cpm->trans_commit();
  //         if ($img_count==0) {
  //           $this->cpm->update($nation_code, $pelanggan->id, $cpm_id, array("foto"=>$sc->image,"thumb"=>$sc->thumb));
  //           $this->cpm->trans_commit();
  //         }
  //         $img_count++;
  //         $cpfm_id++;
  //         $ir->img_count = $img_count;
  //         $ir->cpfm_id = $cpfm_id;
  //       } else {
  //         $ir->sc->status = 996;
  //         $ir->sc->message = "Image upload process failed";
  //       }
  //     }
  //   }
  //   $this->seme_log->write("api_mobile", 'API_Mobile/Produk::__processUpload -- INFO '.$this->status.' '.$this->message);
  //   return $ir;
  // }

  /**
   * Generates data object for image product
   * @param  int      $nation_code      nation code
   * @param  int      $c_produk_id      id for c_produk
   * @param  int      $cpfm_id          id for c_produk_foto
   * @param  string   $url              url image
   * @param  string   $thumb            url for thumbnail
   * @param  int      $img_count        image counter
   * @return int                        return 0 if failed
   */
  private function __dataImageProduct($nation_code,$c_produk_id,$cpfm_id,$url,$thumb,$img_count){
    $dix = array();
    $dix['nation_code'] = $nation_code;
    $dix['c_produk_id'] = $c_produk_id;
    $dix['id'] = $cpfm_id;
    $dix['url'] = $url;
    $dix['url_thumb'] = $thumb;
    $dix['is_active'] = 1;
    $dix['caption'] = '';
    return $this->cpfm->set($dix);
  }

  public function index()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['is_login'] = "0";
    $data['is_saved'] = "0";
    $data['produk_total'] = 0;
    $data['produks'] = array();
    $data['seller'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
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

    //by Donny Dennison - 1 desember 2020 16:29
    //list-produt-sameStreet-neighborhood-all-from-user-address
    $type = $this->input->post("type");

    if (strlen($type)<=0 || empty($type)){
      $type="";
    }

    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    $page_size = $this->input->post("page_size");
    $grid = $this->input->post("grid");
    $keyword = trim($this->input->post("keyword"));
    //$kategori_id = $this->input->post("kategori_id");
    $brand_id = $this->input->post("kategori_id");
    $b_user_id = $this->input->post("b_user_id");
    if ($b_user_id<='0') {
      $b_user_id = 0;
    }

    $checkUserStillInDb = $this->bu->getById($nation_code, $b_user_id);
    if(!isset($checkUserStillInDb->id)) {
      $this->status = 200;
      $this->message = 'Success';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $kategori_id = ''; //not used
    if (empty($kategori_id)) {
      $kategori_id="";
    }

    //by Donny Dennison - 2 july 2021 9:37
    //move-campaign-to-sponsored
    $sponsor = (int) $this->input->post("sponsor");

    //by Donny Dennison - 17 December 2021 10:46
    //product list api can choose between show free product or not
    $product_type = $this->input->post("product_type");
    
    if(!$product_type){
      $product_type = 'All';
    }

    $show_car = (int) $this->input->post("show_car");

    if(!$show_car){
      $show_car = 0;
    }

    if($show_car != 1){
      $show_car = 0;
    }

    $soldout_meetup = (string) $this->input->post("soldout_meetup");
    $page_size_setting = (string) trim(strtolower($this->input->post("page_size_setting")));
    if(!in_array($page_size_setting, array("api", "mobile"))){
      $page_size_setting = "api";
    }

    //sanitize input
    $tbl_as = $this->cpm->getTblAs();
    $tbl2_as = $this->cpm->getTbl2As();

    $sort_col = $this->__sortCol($sort_col, $tbl_as, $tbl2_as);

    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);

    if($page_size_setting == "mobile"){
      $page_size = $this->__pageSize($page_size);
    }else{
      $positionSplice = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C15");
      if (!isset($positionSplice->remark)) {
        $positionSplice = new stdClass();
        $positionSplice->remark = 6;
      }
      $page_size = $positionSplice->remark;
    }

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
    //if (isset($pelanggan->id) && strlen($keyword)) {
    //  $data['is_login'] = "1";
    //  $bupm = $this->bupw->check($nation_code, $pelanggan->id, $keyword);
    //  if (isset($bupm->nation_code)) {
    //    $data['is_saved'] = "1";
    //  }
    //  if ($this->is_log) {
    //    $this->seme_log->write("api_mobile", "API_Mobile/Produk::index -> keyword lookup done for USERID: ".$pelanggan->id.", KEYWORD: ".$keyword.", SAVED: ".$data['is_saved']);
    //  }
    //}

    //advanced filter
    $harga_jual_min = '';
    if (isset($_POST['harga_jual_min'])) {
      
      //by Donny Dennison - 9 november 2021 9:59
      //harga_jual_min and harga_jual_max can accept float /decimal
      // $harga_jual_min = (int) $_POST['harga_jual_min'];
      $harga_jual_min = (float) $_POST['harga_jual_min'];

      if ($harga_jual_min<=-1) {
        $harga_jual_min = '';
      }
    }
    if ($harga_jual_min>0) {
      $harga_jual_min = (float) $harga_jual_min;
    }
    
    //by Donny Dennison - 9 november 2021 9:59
    //harga_jual_min and harga_jual_max can accept float /decimal
    // $harga_jual_max = (int) $this->input->post("harga_jual_max");
    $harga_jual_max = (float) $this->input->post("harga_jual_max");

    if ($harga_jual_max<=0) {
      $harga_jual_max = "";
    }
    if ($harga_jual_max>0) {
      $harga_jual_max = (float) $harga_jual_max;
    }

    $b_kondisi_ids = "";
    if (isset($_POST['b_kondisi_ids'])) {
      $b_kondisi_ids = $_POST['b_kondisi_ids'];
    }
    if (strlen($b_kondisi_ids)>0) {
      $b_kondisi_ids = rtrim($b_kondisi_ids, ",");
      $b_kondisi_ids = explode(",", $b_kondisi_ids);
      if (count($b_kondisi_ids)) {
        $kons = array();
        foreach ($b_kondisi_ids as &$bks) {
          $bks = (int) trim($bks);
          if ($bks>0) {
            $kons[] = $bks;
          }
        }
        $b_kondisi_ids = $kons;
      } else {
        $b_kondisi_ids = array();
      }
    } else {
      $b_kondisi_ids = array();
    }

    $b_kategori_ids = "";
    if (isset($_POST['b_kategori_ids'])) {
      $b_kategori_ids = $_POST['b_kategori_ids'];
    }
    if (strlen($b_kategori_ids)>0) {
      $b_kategori_ids = rtrim($b_kategori_ids, ",");
      $b_kategori_ids = explode(",", $b_kategori_ids);
      if (count($b_kategori_ids)) {
        $kods = array();
        foreach ($b_kategori_ids as &$bki) {
          $bki = (int) trim($bki);
          if ($bki>0) {
            $kods[] = $bki;
          }
        }
        $b_kategori_ids = $kods;
      } else {
        $b_kategori_ids = array();
      }
    } else {
      $b_kategori_ids = array();
    }

    $b_brand_ids = "";
    if (isset($_POST['b_brand_ids'])) {
      $b_brand_ids = $_POST['b_brand_ids'];
    }
    if (strlen($b_brand_ids)>0) {
      $b_brand_ids = rtrim($b_brand_ids, ",");
      $b_brand_ids = explode(",", $b_brand_ids);
      if (count($b_brand_ids)) {
        $kods = array();
        foreach ($b_brand_ids as &$bki) {
          $bki = (int) trim($bki);
          if ($bki>0) {
            $kods[] = $bki;
          }
        }
        $b_brand_ids = $kods;
      } else {
        $b_brand_ids = array();
      }
    } else {
      $b_brand_ids = array();
    }

    $kecamatan = $this->input->post("kecamatan");
    if (strlen($kecamatan)) {
      $kecamatan = "";
    }
    
    // input brand name
    $c_brand_name = $this->input->post("c_brand_name");

    //by Donny Dennison - 1 desember 2020 16:29
    //list-produt-sameStreet-neighborhood-all-from-user-address
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

      $blockDataAccount = $this->cbm->getAllByUserId($nation_code, 0, $pelanggan->id, "account");
      $blockDataAccountReverse = $this->cbm->getAllByUserId($nation_code, 0, 0, "account", $pelanggan->id);
      $blockDataProduct = $this->cbm->getAllByUserId($nation_code, 0, $pelanggan->id, "product");

    }else{

      $blockDataAccount = array();
      $blockDataAccountReverse = array();
      $blockDataProduct = array();

    }
    //END by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account

    //by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account

    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language

    //by Donny Dennison - 1 desember 2020 16:29
    //list-produt-sameStreet-neighborhood-all-from-user-address
    //get produk data
    // $ddcount = $this->cpm->countAll($nation_code, $keyword, $kategori_id, $b_user_id, $harga_jual_min, $harga_jual_max, $b_kondisi_ids, $b_kategori_ids, $kecamatan);
    // $ddcount = $this->cpm->countAll($nation_code, $keyword, $kategori_id, $b_user_id, $harga_jual_min, $harga_jual_max, $b_kondisi_ids, $b_kategori_ids, $type, $pelangganAddress1, $product_type, $show_car, $soldout_meetup);
    // $ddcount = $this->cpm->countAll($nation_code, $keyword, $kategori_id, $b_user_id, $harga_jual_min, $harga_jual_max, $b_kondisi_ids, $b_kategori_ids, $type, $pelangganAddress1, $product_type, $show_car, $soldout_meetup, $pelanggan->language_id);
    $ddcount = $this->cpm->countAll($nation_code, $keyword, $kategori_id, $b_user_id, $harga_jual_min, $harga_jual_max, $b_kondisi_ids, $b_kategori_ids, $b_brand_ids, $type, $pelangganAddress1, $product_type, $show_car, $soldout_meetup, $blockDataAccount, $blockDataAccountReverse, $blockDataProduct, $pelanggan->language_id, $c_brand_name);
    unset($pelangganAddress1);

    //by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account

    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language

    //by Donny Dennison - 1 desember 2020 16:29
    //list-produt-sameStreet-neighborhood-all-from-user-address
    // $ddata = $this->cpm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $kategori_id, $b_user_id, $harga_jual_min, $harga_jual_max, $b_kondisi_ids, $b_kategori_ids, $kecamatan);

    //by Donny Dennison - 17 December 2021 10:46
    //product list api can choose between show free product or not
    // $ddata = $this->cpm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $kategori_id, $b_user_id, $harga_jual_min, $harga_jual_max, $b_kondisi_ids, $b_kategori_ids, $type, $pelangganAddress2, $product_type, $show_car, $soldout_meetup);
    // $ddata = $this->cpm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $kategori_id, $b_user_id, $harga_jual_min, $harga_jual_max, $b_kondisi_ids, $b_kategori_ids, $type, $pelangganAddress2, $product_type, $show_car, $soldout_meetup, $pelanggan->language_id);
    $data['produks'] = $this->cpm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $kategori_id, $b_user_id, $harga_jual_min, $harga_jual_max, $b_kondisi_ids, $b_kategori_ids, $b_brand_ids, $type, $pelangganAddress2, $product_type, $show_car, $soldout_meetup, $blockDataAccount, $blockDataAccountReverse, $blockDataProduct, $pelanggan->language_id, $c_brand_name);
    unset($pelangganAddress2);

    // // //get address, for bug list sake
    // // $adl = array(); //address list
    // // $bua_com = array(); //library pointer
    // // $pid_com = array(); //library pointer
    // // foreach ($ddata as $dd) {
    // //   $kid = (int) $dd->id;
    // //   $kvl = $dd->b_user_alamat_id.'-'.$dd->b_user_id_seller;
    // //   $bua_com[$kid] = $kvl;
    // //   $pid_com[$kvl] = $kid;
    // // }
    // // unset($dd); //free some memory

    // // //check if product exists
    // // if (count($bua_com)) {
    // //   //get address collection
    // //   $ads = $this->bua->getIdUserIdIN($bua_com);

    // //   //mapping address to product
    // //   $pdl = array(); //produk address list
    // //   foreach ($ads as $ad) {
    // //     $pid = 0;
    // //     $kvl = $ad->id.'-'.$ad->b_user_id;
    // //     if (isset($pid_com[$kvl])) {
    // //       $pid = $pid_com[$kvl];
    // //     }
    // //     if (!empty($pid)) {
    // //       $pdl[$pid] = $ad;
    // //     }
    // //     $adl[$kvl] = $ad;
    // //   }
    // //   unset($ad); //freed some memory
    // //   unset($ads); //freed some memory
    // // }
    // // unset($bua_com); //free some memory

    // // $this->debug($ddata);
    // // die();

    //manipulating data
    foreach ($data['produks'] as &$pd) {
      //conver to utf friendly
      // if (isset($pd->nama)) {
      //   $pd->nama = $this->__dconv($pd->nama);
      // }
      $pd->nama = html_entity_decode($pd->nama,ENT_QUOTES);

      //if (isset($pd->brand)) {
      //  $pd->brand = $this->__dconv($pd->brand);
      //}
      //if (isset($pd->b_user_nama_seller)) {
      //  $pd->b_user_nama_seller = $this->__dconv($pd->b_user_nama_seller);
      //}

      // if (isset($pd->b_user_image_seller)) {
      //   if (empty($pd->b_user_image_seller)) {
      //     $pd->b_user_image_seller = 'media/produk/default.png';
      //   }
      //   // by Muhammad Sofi - 28 October 2021 11:00
      //   // if user img & banner not exist or empty, change to default image
      //   // $pd->b_user_image_seller = $this->cdn_url($pd->b_user_image_seller);
      //   if(file_exists(SENEROOT.$pd->b_user_image_seller) && $pd->b_user_image_seller != 'media/user/default.png'){
      //     $pd->b_user_image_seller = $this->cdn_url($pd->b_user_image_seller);
      //   } else {
      //     $pd->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
      //   }
      // }
      if (isset($pd->thumb)) {
        if (empty($pd->thumb)) {
          $pd->thumb = 'media/produk/default.png';
        }
        $pd->thumb = str_replace("//", "/", $pd->thumb);
        $pd->thumb = $this->cdn_url($pd->thumb);
      }
      if (isset($pd->foto)) {
        if (empty($pd->foto)) {
          $pd->foto = 'media/produk/default.png';
        }
        $pd->foto = str_replace("//", "/", $pd->foto);
        $pd->foto = $this->cdn_url($pd->foto);
      }
      // if (isset($pd->b_kondisi_icon)) {
      //   if (empty($pd->b_kondisi_icon)) {
      //     $pd->b_kondisi_icon = 'media/icon/default.png';
      //   }
      //   $pd->b_kondisi_icon = $this->cdn_url($pd->b_kondisi_icon);
      // }
      // if (isset($pd->b_berat_icon)) {
      //   if (empty($pd->b_berat_icon)) {
      //     $pd->b_berat_icon = 'media/icon/default.png';
      //   }
      //   $pd->b_berat_icon = $this->cdn_url($pd->b_berat_icon);
      // }
      // $kvl = $pd->b_user_alamat_id.'-'.$pd->b_user_id_seller;
      // if (isset($adl[$kvl])) {
      //   // by Muhammad Sofi - 3 November 2021 10:00
      //   // remark code
      //   // $pd->alamat = $this->__dconv($adl[$kvl]->alamat);
      //   $pd->alamat2 = $this->__dconv($adl[$kvl]->alamat2);
      //   $pd->kecamatan = $this->__dconv($adl[$kvl]->kecamatan);
      //   $pd->latitude = $this->__dconv($adl[$kvl]->latitude);
      //   $pd->longitude = $this->__dconv($adl[$kvl]->longitude);
      // }

      if($pd->product_type == 'Automotive' && ($pd->b_kategori_id == 32 || $pd->b_kategori_id == 33)){
        $pd->automotive_type = $pd->kategori;
      }else{
        $pd->automotive_type = "";
      }

      //by Donny Dennison - 22 february 2022 17:42
      //change product_type language
      if($pelanggan->language_id == 2){
        if($pd->product_type == "Protection"){
          $pd->product_type = "Proteksi";
        } else if($pd->product_type == "Automotive"){
          $pd->product_type = "Otomotif";
        } else if($pd->product_type == "Free"){
          $pd->product_type = "Gratis";
        }
      }

      //by Donny Dennison - 22 july 2022 10:45
      //add response parameter have_video in api product, product/detail, homepage, seller/product, product_automotive, wishlist
      $pd->have_video = ($this->cpfm->countByProdukIdJenisConvertStatusNotEqual($nation_code, $pd->id, "video", "uploading") > 0) ? "1" : "0";

    }

    //build result

    //by Donny Dennison - 2 september 2021 10:26
    //query-free-product-in-api-product
    // $data['produk_total'] = $ddcount;
    $data['produk_total'] = (string) $ddcount;

    //by Donny Dennison - 27 agustus 2020
    // add seller data in response
    if ($b_user_id>'0') {

      //START by Donny Dennison - 21 december 2022 14:27
      //improve product list api
      //get seller profile
      $rating_stats = $this->erm->getSellerStats($nation_code, $b_user_id);
      $rating_stats2 = $this->erm->getBuyerStats($nation_code, $b_user_id);
      $seller = $this->bu->detail($nation_code, $b_user_id);

      $seller->image = $this->cdn_url($seller->image);

      //fill default value
      $seller->rating = 0;
      $seller->rating_count = 0;
      $seller->rating_total = 0;

      $default_address = $this->bua->getByUserIdDefault($nation_code, $b_user_id);

      if(isset($default_address->alamat2)){

        $seller->default_address = $default_address->alamat2;
        $seller->kelurahan = $default_address->kelurahan;
        $seller->kecamatan = $default_address->kecamatan;
        $seller->kabkota = $default_address->kabkota;
        $seller->provinsi = $default_address->provinsi;

      }else{

        $seller->default_address = '';
        $seller->kelurahan = '';
        $seller->kecamatan = '';
        $seller->kabkota = '';
        $seller->provinsi = '';

      }

      //put
      if (isset($rating_stats->count)) {
          $seller->rating_count = (int) $rating_stats->count;
      }
      if (isset($rating_stats->rating)) {
          $seller->rating_total = (int) $rating_stats->rating;
      }

      //calculate rating as seller
      if ($seller->rating_count>0 && $seller->rating_total>0) {
          $seller->rating = floor($seller->rating_total/$seller->rating_count);
      }

      //calculate rating as buyer
      if (isset($rating_stats2->count)) {
          $seller->rating_count = (int) $rating_stats->count;
      }
      if (isset($rating_stats2->rating)) {
          $seller->rating_total = (int) $rating_stats->rating;
      }
      if ($seller->rating_count>0 && $seller->rating_total>0) {
          $buyer = floor($seller->rating_total/$seller->rating_count);
          $seller->rating = ($seller->rating + $buyer) / 2;
      }

      $seller->rating = (string) $seller->rating;

      $data['seller'] = $seller;
      //END by Donny Dennison - 21 december 2022 14:27
      //improve product list api

    }else if(isset($data['produks'][0]->b_user_id_seller)){

      //START by Donny Dennison - 21 december 2022 14:27
      //improve product list api
      //get seller profile
      $rating_stats = $this->erm->getSellerStats($nation_code, $data['produks'][0]->b_user_id_seller);
      $rating_stats2 = $this->erm->getBuyerStats($nation_code, $data['produks'][0]->b_user_id_seller);
      $seller = $this->bu->detail($nation_code, $data['produks'][0]->b_user_id_seller);

      $seller->image = $this->cdn_url($seller->image);

      //fill default value
      $seller->rating = 0;
      $seller->rating_count = 0;
      $seller->rating_total = 0;

      $default_address = $this->bua->getByUserIdDefault($nation_code, $data['produks'][0]->b_user_id_seller);

      if(isset($default_address->alamat2)){

        $seller->default_address = $default_address->alamat2;
        $seller->kelurahan = $default_address->kelurahan;
        $seller->kecamatan = $default_address->kecamatan;
        $seller->kabkota = $default_address->kabkota;
        $seller->provinsi = $default_address->provinsi;

      }else{

        $seller->default_address = '';
        $seller->kelurahan = '';
        $seller->kecamatan = '';
        $seller->kabkota = '';
        $seller->provinsi = '';

      }

      //put
      if (isset($rating_stats->count)) {
          $seller->rating_count = (int) $rating_stats->count;
      }
      if (isset($rating_stats->rating)) {
          $seller->rating_total = (int) $rating_stats->rating;
      }

      //calculate rating as seller
      if ($seller->rating_count>0 && $seller->rating_total>0) {
          $seller->rating = floor($seller->rating_total/$seller->rating_count);
      }

      //calculate rating as buyer
      if (isset($rating_stats2->count)) {
          $seller->rating_count = (int) $rating_stats->count;
      }
      if (isset($rating_stats2->rating)) {
          $seller->rating_total = (int) $rating_stats->rating;
      }
      if ($seller->rating_count>0 && $seller->rating_total>0) {
          $buyer = floor($seller->rating_total/$seller->rating_count);
          $seller->rating = ($seller->rating + $buyer) / 2;
      }

      $seller->rating = (string) $seller->rating;

      $data['seller'] = $seller;
      //END by Donny Dennison - 21 december 2022 14:27
      //improve product list api

    }

    //by Donny Dennison - 2 july 2021 9:37
    //move-campaign-to-sponsored
    if($sponsor == 1){

      $sponsorList = $this->cp2->getList($nation_code,$page);

      if (isset($sponsorList[$page-1]->gambar)) {
          if (strlen($sponsorList[$page-1]->gambar)<=4) {
              $sponsorList[$page-1]->gambar = 'media/promo/default.png';
          }
          $sponsorList[$page-1]->gambar = $this->cdn_url($sponsorList[$page-1]->gambar);

          $sponsorList[$page-1]->judul = html_entity_decode($sponsorList[$page-1]->judul,ENT_QUOTES);

          $sponsorList[$page-1]->product_type = 'Sponsor';
          $data['produks'][]= $sponsorList[$page-1];
      }

    }

    // $show_ads = (int) $this->input->post("show_ads");
    // if($show_ads == 1 && count($data['produks']) > 0){

    //   $positionSplice = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C15");
    //   if (!isset($positionSplice->remark)) {
    //     $positionSplice = new stdClass();
    //     $positionSplice->remark = 6;
    //   }

    //   $inserted = array("ads");

    //   array_splice($data['produks'], $positionSplice->remark, 0, $inserted);

    // }

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }

  //by Donny Dennison - 9 july 2021 14:54
  //add-total-people-around-you-in-product-list
  public function peoplearound()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['people_around_total'] = 0;

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language
    // check apisess
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
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //populate input get
    $type = $this->input->get("type");
    
    if (strlen($type)<=0 || empty($type)){
      $type="";
    }

    //by Donny Dennison - 1 desember 2020 16:29
    //list-produt-sameStreet-neighborhood-all-from-user-address
    if (isset($pelanggan->id)) {

      $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
      
    }else{

      $pelangganAddress = array();

    }

    //by Donny Dennison - 9 july 2021 14:54
    //add-total-people-around-you-in-product-list
    if($type == 'sameStreet' || $type == 'neighborhood' || $type == 'district'  || $type == 'city' || $type == 'province'){
      $data['people_around_total'] = $this->bua->countPeopleAround($nation_code, $type, $pelangganAddress);
    }

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }

  public function detail($id)
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['produk'] = new stdClass();
    $data['is_blocked'] = '0';

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language
    // check apisess
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

    // $id = (int) $id;
    if (!$id) {
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $getProductType = $this->cpm->getProductType($nation_code, $id);
    if(!isset($getProductType->product_type)){
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $getProductType = $getProductType->product_type;

    $produk = $this->cpm->getById($nation_code, $id, $pelanggan, $getProductType, $pelanggan->language_id);
    if (!isset($produk->id)) {
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }
    if($produk->stok<=0){
      $produk->stok = '0';
    }

    $is_wishlist= 0;
    if (isset($pelanggan->id)) {
      $is_wishlist = $this->dwlm->check($nation_code, $pelanggan->id, $produk->id);
    }
    if (strlen($produk->b_user_image_seller)<=4) {
      $produk->b_user_image_seller = 'media/user/default.png';
    }

    // filter utf-8
    if (isset($produk->b_user_fnama_seller)) {
      $produk->b_user_fnama_seller = $this->__dconv($produk->b_user_fnama_seller);
    }
    // if (isset($produk->nama)) {
    //   $produk->nama = $this->__dconv($produk->nama);
    // }
    $produk->nama = html_entity_decode($produk->nama,ENT_QUOTES);

    if (isset($produk->brand)) {
      $produk->brand = $this->__dconv($produk->brand);
    }

    // if (isset($produk->deskripsi)) {
    //   $produk->deskripsi = $this->__dconv($produk->deskripsi);
    // }
    $produk->deskripsi = html_entity_decode($produk->deskripsi,ENT_QUOTES);

    //cast CDN
    
    // by Muhammad Sofi - 28 October 2021 11:00
    // if user img & banner not exist or empty, change to default image
    // $produk->b_user_image_seller = $this->cdn_url($produk->b_user_image_seller);
    if(file_exists(SENEROOT.$produk->b_user_image_seller) && $produk->b_user_image_seller != 'media/user/default.png'){
      $produk->b_user_image_seller = $this->cdn_url($produk->b_user_image_seller);
    } else {
      $produk->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
    }
    // $produk->b_kondisi_icon = $this->cdn_url($produk->b_kondisi_icon);
    // $produk->b_berat_icon = $this->cdn_url($produk->b_berat_icon);
    $produk->kategori_icon = $this->cdn_url($produk->kategori_icon);
    $produk->foto = $this->cdn_url($produk->foto);
    $produk->thumb = $this->cdn_url($produk->thumb);

    $i=0;
    $produk->galeri = array();
    $galeri = $this->cpfm->getByProdukId($nation_code, $id);
    foreach ($galeri as &$gal) {
      if($i>=5) continue;
      $gal->url = $this->cdn_url($gal->url);
      $gal->url_thumb = $this->cdn_url($gal->url_thumb);
      $produk->galeri[] = $gal;
      $i++;
    }

    $i=0;
    $produk->galeri_video = array();
    $galeri = $this->cpfm->getByProdukId($nation_code, $id, "video");
    foreach ($galeri as &$gal) {
      if($i>=5) continue;
      $gal->url = $this->cdn_url($gal->url);
      $gal->url_thumb = $this->cdn_url($gal->url_thumb);
      $produk->galeri_video[] = $gal;
      $i++;
    }

    $produk->is_wishlist = $is_wishlist;
    
    // $produk->berat = round($produk->berat, 2);
    $produk->harga_jual = round($produk->harga_jual, 2);
    // $produk->dimension_long = round($produk->dimension_long, 0);
    // $produk->dimension_width = round($produk->dimension_width, 0);
    // $produk->dimension_height = round($produk->dimension_height, 0);

    if($produk->product_type == 'Automotive' && ($produk->b_kategori_id == 32 || $produk->b_kategori_id == 33)){
      $produk->automotive_type = $produk->kategori;
    }else{
      $produk->automotive_type = "";
    }

    //by Donny Dennison - 22 february 2022 17:42
    //change product_type language
    if($pelanggan->language_id == 2){
      if($produk->product_type == "Protection"){
        $produk->product_type = "Proteksi";
      } else if($produk->product_type == "Automotive"){
        $produk->product_type = "Otomotif";
      } else if($produk->product_type == "Free"){
        $produk->product_type = "Gratis";
      }
    }

    //by Donny Dennison - 22 july 2022 10:45
    //add response parameter have_video in api product, product/detail, homepage, seller/product, product_automotive, wishlist
    $produk->have_video = ($this->cpfm->countByProdukIdJenisConvertStatusNotEqual($nation_code, $produk->id, "video", "uploading") > 0) ? "1" : "0";

    //START by Donny Dennison - 12 july 2022 14:56
    //new offer system
    $produk->exist_chat_room_id_for_offer = '0';
    $produk->offer_status = '';
    $produk->chat_room_id_for_review = '0';
    $produk->already_review = 'yes';

    if (isset($pelanggan->id)) {

      if($pelanggan->id != $produk->b_user_id_seller){

        //check already have room or not
        $checkRoomChat = $this->ecpm->getRoomChatIDByParticipantId($nation_code, $pelanggan->id, $produk->b_user_id_seller, "offer", $produk->id);
        if (isset($checkRoomChat->nation_code)) {
          $produk->exist_chat_room_id_for_offer = $checkRoomChat->e_chat_room_id;
          $produk->offer_status = $checkRoomChat->offer_status;
        }

        $checkAlreadyReview = $this->ecrm->getForOffer($nation_code, "offer", "buyer", $pelanggan->id, $produk->id);
        if (isset($checkAlreadyReview->chat_room_id)) {
          $produk->chat_room_id_for_review = $checkAlreadyReview->chat_room_id;
          $produk->already_review = 'no';
        }

      }else{

        $checkAlreadyReview = $this->ecrm->getForOffer($nation_code, "offer", "seller", $pelanggan->id, $produk->id);
        if (isset($checkAlreadyReview->chat_room_id)) {
          $produk->chat_room_id_for_review = $checkAlreadyReview->chat_room_id;
          $produk->already_review = 'no';
        }

      }

    }
    //END by Donny Dennison - 12 july 2022 14:56
    //new offer system

    //START by Donny Dennison - 4 november 2022 15:33
    //new feature, block community post or account
    if (isset($pelanggan->id)) {

      if($pelanggan->id != $produk->b_user_id_seller){

        $blockDataAccount = $this->cbm->getById($nation_code, 0, $produk->b_user_id_seller, "account", $pelanggan->id);
        $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $produk->b_user_id_seller);

        if(isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){

          $data['is_blocked'] = '1';

        }

      }

    }
    //END by Donny Dennison - 4 november 2022 15:33
    //new feature, block community post or account

    $this->status = 200;
    $this->message = 'Success';

    //by Donny Dennison - 7 august 2020 09:47
    //add discussion data to detail api
    //START by Donny Dennison - 7 august 2020 09:47

    $tbl_as = $this->fdis->getTblAs();
    $tbl2_as = $this->fdis->getTbl2As();
    $sort_col = $this->__sortColDiscussion('cdate', $tbl_as, $tbl2_as);
    $sort_dir = $this->__sortDir('desc');
    $page = $this->__page(1);
    $page_size_parent_discussion = $this->__pageSize(2);
    $page_size_child_discussion = $this->__pageSize(1);

    $produk->diskusi_total = $this->fdis->countAll($nation_code, 0, $id);

    $produk->diskusis = $this->fdis->getAll($nation_code, $page, $page_size_parent_discussion, $sort_col, $sort_dir, 0, $id);

    foreach ($produk->diskusis as $key => $discuss) {

      //by Donny Dennison 17 September 2021 - 11:54
      //community-feature
      // $produk->diskusis[$key]->cdate_text = $this->humanTiming($produk->diskusis[$key]->cdate);
      $produk->diskusis[$key]->cdate_text = $this->humanTiming($produk->diskusis[$key]->cdate, null, $pelanggan->language_id);

      $produk->diskusis[$key]->text = html_entity_decode($produk->diskusis[$key]->text,ENT_QUOTES);

      $produk->diskusis[$key]->diskusi_anak_total = $this->fdis->countAll($nation_code,$discuss->discussion_id, $id);

      $produk->diskusis[$key]->diskusi_anak = $this->fdis->getAll($nation_code, $page, $page_size_child_discussion, $sort_col, $sort_dir, $discuss->discussion_id, $id);

      //by Donny Dennison 17 September 2021 - 11:54
      //community-feature
      foreach($produk->diskusis[$key]->diskusi_anak as &$de){

        // $de->cdate_text = $this->humanTiming($de->cdate);
        $de->cdate_text = $this->humanTiming($de->cdate, null, $pelanggan->language_id);

        $de->text = html_entity_decode($de->text,ENT_QUOTES);

      }

    }

    //END by Donny Dennison - 7 august 2020 09:47

    $data['produk'] = $produk;

    // //START by Donny Dennison - 14 august 2020 15:53
    // //curl to facebook every time customer open product detail
    // //send data to facebook
    // $postToFB= array(
      
    //   'data' => array( 

    //     array(
        
    //       'event_name' => 'ViewContent',
    //       'event_time' => strtotime('now'),
    //       'event_id' => 'ViewContent'.date('YmdHis'),
    //       'event_source_url' => 'https://sellon.net/product_detail.php?product_id='.$id,
    //       'user_data' => array(
    //         'client_ip_address' => '35.240.185.29',
    //         'client_user_agent' => 'browser'
    //       ),

    //       'custom_data' => array(
    //         'value' => $produk->harga_jual,
    //         'currency' => 'SGD',
    //         'content_name' => $produk->nama,
    //         'content_category' => $produk->kategori,
    //         'content_ids' => array($id),
    //         'contents' => array( 
              
    //           0 => array(
    //             'id' => $id,
    //             'quantity' => $produk->stok,
    //             'item_price' => $produk->harga_jual
    //           )

    //         ),
    //         'content_type' => 'product',
    //         'delivery_category' => 'home_delivery'
    //       ) 
    //     )

    //   ),
    //   // 'test_event_code' =>'TEST20037'

    // );

    // $curlToFacebook = $this->__CurlFacebook($postToFB);

    // $this->seme_log->write("api_mobile", "__CurlFacebook : Response -> ".$curlToFacebook);
    // //END by Donny Dennison - 14 august 2020 15:53
    // //curl to facebook every time customer open product detail

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }

  //START by Donny Dennison - 14 july 2022 14:28
  //new api product/video_list
  public function video_list()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['produk_total'] = 0;
    $data['produks'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
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

    // $sort_col = $this->input->post("sort_col");
    // $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    // $page_size = $this->input->post("page_size");
    $positionSplice = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C15");
    if (!isset($positionSplice->remark)) {
      $positionSplice = new stdClass();
      $positionSplice->remark = 6;
    }
    $page_size = $positionSplice->remark;
    $timezone = $this->input->post("timezone");
    $watched_video = $this->input->post("watched_video");

    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    //sanitize input
    $tbl_as = $this->cpm->getTblAs();
    $tbl2_as = $this->cpm->getTbl2As();
    $sort_col = $this->__sortCol("id", $tbl_as, $tbl2_as);
    $sort_dir = $this->__sortDir("DESC");
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

    $b_kategori_ids = "";
    if (isset($_POST['b_kategori_ids'])) {
      $b_kategori_ids = $_POST['b_kategori_ids'];
    }
    if (strlen($b_kategori_ids)>0) {
      $b_kategori_ids = rtrim($b_kategori_ids, ",");
      $b_kategori_ids = explode(",", $b_kategori_ids);
      if (count($b_kategori_ids)) {
        $kods = array();
        foreach ($b_kategori_ids as &$bki) {
          $bki = (int) trim($bki);
          if ($bki>0) {
            $kods[] = $bki;
          }
        }
        $b_kategori_ids = $kods;
      } else {
        $b_kategori_ids = array();
      }
    } else {
      $b_kategori_ids = array();
    }

    if (isset($pelanggan->id)) {

      // $pelangganAddress1 = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
      $pelangganAddress2 = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
      
    }else{

      // $pelangganAddress1 = array();
      $pelangganAddress2 = array();

    }

    // $data['produk_total'] = $this->cpm->countAllVideo($nation_code, $keyword, $kategori_id, $b_user_id, $harga_jual_min, $harga_jual_max, $b_kondisi_ids, $b_kategori_ids, $type, $pelangganAddress1, $product_type, $soldout_meetup, $pelanggan->language_id);
    // unset($pelangganAddress1);

    $data['produks'] = $this->cpm->getAllVideoManualQuery($nation_code, $page, $page_size, $sort_col, $sort_dir, $type, $pelangganAddress2, $pelanggan->language_id, $watched_video, $b_kategori_ids);
    unset($pelangganAddress2);

    //manipulating data
    foreach ($data['produks'] as &$pd) {

      $pd->nama = html_entity_decode($pd->nama,ENT_QUOTES);

      // if (isset($pd->b_user_nama_seller)) {
      //   $pd->b_user_nama_seller = $this->__dconv($pd->b_user_nama_seller);
      // }

      // if (isset($pd->b_user_image_seller)) {
      //   if (empty($pd->b_user_image_seller)) {
      //     $pd->b_user_image_seller = 'media/produk/default.png';
      //   }

      //   if(file_exists(SENEROOT.$pd->b_user_image_seller) && $pd->b_user_image_seller != 'media/user/default.png'){
      //     $pd->b_user_image_seller = $this->cdn_url($pd->b_user_image_seller);
      //   } else {
      //     $pd->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
      //   }
      // }

      // if (isset($pd->thumb)) {
      //   if (empty($pd->thumb)) {
      //     $pd->thumb = 'media/produk/default.png';
      //   }
      //   $pd->thumb = str_replace("//", "/", $pd->thumb);
      //   $pd->thumb = $this->cdn_url($pd->thumb);
      // }

      if($pd->product_type == 'Automotive' && ($pd->b_kategori_id == 32 || $pd->b_kategori_id == 33)){
        $pd->automotive_type = $pd->kategori;
      }else{
        $pd->automotive_type = "";
      }

      //by Donny Dennison - 22 february 2022 17:42
      //change product_type language
      if($pelanggan->language_id == 2){
        if($pd->product_type == "Protection"){
          $pd->product_type = "Proteksi";
        } else if($pd->product_type == "Automotive"){
          $pd->product_type = "Otomotif";
        } else if($pd->product_type == "Free"){
          $pd->product_type = "Gratis";
        }
      }

      $pd->url = $this->cdn_url($pd->url);
      $pd->url_thumb = $this->cdn_url($pd->url_thumb);

    }

    // $show_ads = (int) $this->input->post("show_ads");
    // if($show_ads == 1){

    //   $positionSplice = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C15");
    //   if (!isset($positionSplice->remark)) {
    //     $positionSplice = new stdClass();
    //     $positionSplice->remark = 2;
    //   }

    //   $inserted = array("ads");

    //   array_splice($data['produks'], $positionSplice->remark, 0, $inserted);

    // }

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }
  //END by Donny Dennison - 14 july 2022 14:28
  //new api product/video_list

  public function video_listv2()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['start_position'] = "0";
    $data['page'] = "1";
    $data['produk_total'] = 0;
    $data['produks'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
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

    // $sort_col = $this->input->post("sort_col");
    // $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    // $page_size = $this->input->post("page_size");
    $positionSplice = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C15");
    if (!isset($positionSplice->remark)) {
      $positionSplice = new stdClass();
      $positionSplice->remark = 6;
    }
    $page_size = $positionSplice->remark;
    $timezone = $this->input->post("timezone");
    $watched_video = $this->input->post("watched_video");
    $start_position = $this->input->post("start_position");
    if($start_position == 0 || $start_position == ""){
      $start_position = rand(1,10500);
    }

    $checkLimit = $start_position + ($page * $page_size);
    if($checkLimit >= 10500){
      $start_position = rand(1,10500);
      $page = 1;
    }

    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    //sanitize input
    $tbl_as = $this->cpm->getTblAs();
    $tbl2_as = $this->cpm->getTbl2As();
    $sort_col = $this->__sortCol("id", $tbl_as, $tbl2_as);
    $sort_dir = $this->__sortDir("DESC");
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

    $b_kategori_ids = "";
    if (isset($_POST['b_kategori_ids'])) {
      $b_kategori_ids = $_POST['b_kategori_ids'];
    }
    if (strlen($b_kategori_ids)>0) {
      $b_kategori_ids = rtrim($b_kategori_ids, ",");
      $b_kategori_ids = explode(",", $b_kategori_ids);
      if (count($b_kategori_ids)) {
        $kods = array();
        foreach ($b_kategori_ids as &$bki) {
          $bki = (int) trim($bki);
          if ($bki>0) {
            $kods[] = $bki;
          }
        }
        $b_kategori_ids = $kods;
      } else {
        $b_kategori_ids = array();
      }
    } else {
      $b_kategori_ids = array();
    }

    if (isset($pelanggan->id)) {

      // $pelangganAddress1 = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
      $pelangganAddress2 = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
      
    }else{

      // $pelangganAddress1 = array();
      $pelangganAddress2 = array();

    }

    if (isset($pelanggan->id)) {

      $blockDataAccount = $this->cbm->getAllByUserId($nation_code, 0, $pelanggan->id, "account");
      $blockDataAccountReverse = $this->cbm->getAllByUserId($nation_code, 0, 0, "account", $pelanggan->id);
      $blockDataProduct = $this->cbm->getAllByUserId($nation_code, 0, $pelanggan->id, "product");

    }else{

      $blockDataAccount = array();
      $blockDataAccountReverse = array();
      $blockDataProduct = array();

    }

    // $data['produk_total'] = $this->cpm->countAllVideo($nation_code, $keyword, $kategori_id, $b_user_id, $harga_jual_min, $harga_jual_max, $b_kondisi_ids, $b_kategori_ids, $type, $pelangganAddress1, $product_type, $soldout_meetup, $pelanggan->language_id);
    // unset($pelangganAddress1);

    $data['produks'] = $this->cpm->getAllVideoManualQueryV2($nation_code, $page, $page_size, $sort_col, $sort_dir, $type, $pelangganAddress2, $pelanggan->language_id, $watched_video, $b_kategori_ids, $blockDataAccount, $blockDataAccountReverse, $blockDataProduct, $start_position);
    unset($pelangganAddress2);

    //manipulating data
    foreach ($data['produks'] as &$pd) {

      $pd->nama = html_entity_decode($pd->nama,ENT_QUOTES);

      // if (isset($pd->b_user_nama_seller)) {
      //   $pd->b_user_nama_seller = $this->__dconv($pd->b_user_nama_seller);
      // }

      // if (isset($pd->b_user_image_seller)) {
      //   if (empty($pd->b_user_image_seller)) {
      //     $pd->b_user_image_seller = 'media/produk/default.png';
      //   }

      //   if(file_exists(SENEROOT.$pd->b_user_image_seller) && $pd->b_user_image_seller != 'media/user/default.png'){
      //     $pd->b_user_image_seller = $this->cdn_url($pd->b_user_image_seller);
      //   } else {
      //     $pd->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
      //   }
      // }

      // if (isset($pd->thumb)) {
      //   if (empty($pd->thumb)) {
      //     $pd->thumb = 'media/produk/default.png';
      //   }
      //   $pd->thumb = str_replace("//", "/", $pd->thumb);
      //   $pd->thumb = $this->cdn_url($pd->thumb);
      // }

      if($pd->product_type == 'Automotive' && ($pd->b_kategori_id == 32 || $pd->b_kategori_id == 33)){
        $pd->automotive_type = $pd->kategori;
      }else{
        $pd->automotive_type = "";
      }

      //by Donny Dennison - 22 february 2022 17:42
      //change product_type language
      if($pelanggan->language_id == 2){
        if($pd->product_type == "Protection"){
          $pd->product_type = "Proteksi";
        } else if($pd->product_type == "Automotive"){
          $pd->product_type = "Otomotif";
        } else if($pd->product_type == "Free"){
          $pd->product_type = "Gratis";
        }
      }

      $pd->url = $this->cdn_url($pd->url);
      $pd->url_thumb = $this->cdn_url($pd->url_thumb);

    }

    // $show_ads = (int) $this->input->post("show_ads");
    // if($show_ads == 1){

    //   $positionSplice = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C15");
    //   if (!isset($positionSplice->remark)) {
    //     $positionSplice = new stdClass();
    //     $positionSplice->remark = 2;
    //   }

    //   $inserted = array("ads");

    //   array_splice($data['produks'], $positionSplice->remark, 0, $inserted);

    // }

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }

  //by Donny Dennison - 23 september 2020 15:42
  //add direct delivery feature
  // private function __shipment_check($berat, $panjang, $lebar, $tinggi)
  private function __shipment_check($berat, $panjang, $lebar, $tinggi, $direct_delivery = 0)
  {
    //init
    $data = array();
    $service_duration = array();
    $courier_services = array();
    $this->status = 200;
    $this->message = 'success';

    //casting input
    $b = round($berat, 1); //weight
    $p = round($panjang, 0); //long
    $l = round($lebar, 0); //width
    $t = round($tinggi, 0); //height

    //get max dimension
    $dimension_max = $this->__getDimensionMax($p, $l, $t);
    $qxv = $p+$l+$t;
    if ($this->is_log) {
      $this->seme_log->write("api_mobile", "API_Mobile/Produk::__shipment_check -> B: $b, D: $p x $l x $t");
    }

    //by Donny Dennison - 23 september 2020 15:42
    //add direct delivery feature
    //START by Donny Dennison - 23 september 2020 15:42

    if($direct_delivery == 1){

      $courier_services[0] = new stdClass();
      $courier_services[0]->nama = "Direct Delivery";
      $courier_services[0]->jenis = "Next Day";
      $courier_services[0]->icon = $this->cdn_url("assets/images/direct_delivery.png");
      // by Muhammad Sofi - 1 November 2021 13:46
      // add description for shipment type
      $courier_services[0]->deskripsi = "The Seller supports direct delivery without using Carrier Service to Buyer location. You can have an appointment with Buyer after payment.";
      $courier_services[0]->vehicle_types = array();
      $courier_services[0]->vehicle_types[0] = new stdClass();
      // $courier_services[0]->vehicle_types[0]->nama = "Regular";
      // $courier_services[0]->vehicle_types[0]->icon = $this->cdn_url("assets/images/regular.png");

    //By Donny Dennison - 7 june 2020 - 10:05
    //request by mr Jackie, add checking if width or length or long more than 1,5 m then courier service is gogovan
    // if ($b<=30.0 && $qxv<=300.0) {
    // if ($b<=30.0 && $qxv<=300.0 && $dimension_max <= 150) {
    }else if ($b<=30.0 && $qxv<=300.0 && $dimension_max <= 150) {
    
    //END by Donny Dennison - 23 september 2020 15:42

      //bisa QXPress same day
      $courier_services[0] = new stdClass();
      $courier_services[0]->nama = "QXpress";
      $courier_services[0]->jenis = "Same Day";
      $courier_services[0]->icon = $this->cdn_url("assets/images/qxpress.png");
      // by Muhammad Sofi - 1 November 2021 13:46
      // add description for shipment type
      $courier_services[0]->deskripsi = "Please attach the waybill on your product after packaging it.";
      $courier_services[0]->vehicle_types = array();
      $courier_services[0]->vehicle_types[0] = new stdClass();
      $courier_services[0]->vehicle_types[0]->nama = "Regular";
      $courier_services[0]->vehicle_types[0]->icon = $this->cdn_url("assets/images/regular.png");
    } else {
      $courier_services[0] = new stdClass();

      //by Donny Dennison - 15 september 2020 17:45
      //change name, image, etc from gogovan to gogox
      // $courier_services[0]->nama = "Gogovan";
      $courier_services[0]->nama = "Gogox";

      $courier_services[0]->jenis = "Same Day";

      //by Donny Dennison - 15 september 2020 17:45
      //change name, image, etc from gogovan to gogox
      // $courier_services[0]->icon = $this->cdn_url("assets/images/gogovan.png");
      $courier_services[0]->icon = $this->cdn_url("assets/images/gogox.png");
      // by Muhammad Sofi - 1 November 2021 13:46
      // add description for shipment type
      $courier_services[0]->deskripsi = "A delivery charge is only for transportation. Please contact a GOGOX driver if you need any manpower. (GOGOX charges $15/helper for loading and unloading)";

      $courier_services[0]->vehicle_types = array();

      $vt = new stdClass();
      $vt->nama = "Lorry 24 Ft";
      $vt->icon = $this->cdn_url("assets/images/lorry24.png");
      $courier_services[0]->vehicle_types[] = $vt;

      if ($p<400 && $l<180 && $t<200 && $b<2500) {
        $vt = new stdClass();
        $vt->nama = "Lorry 14 Ft";
        $vt->icon = $this->cdn_url("assets/images/lorry14.png");
        $courier_services[0]->vehicle_types[] = $vt;
      }
      if ($p<300 && $l<150 && $t<180 && $b<1500) {
        $vt = new stdClass();
        $vt->nama = "Lorry 10 Ft";
        $vt->icon = $this->cdn_url("assets/images/lorry10.png");
        $courier_services[0]->vehicle_types[] = $vt;
      }
      if ($p<240 && $l<150 && $t<120 && $b<900) {
        $vt = new stdClass();
        $vt->nama = "Van";
        $vt->icon = $this->cdn_url("assets/images/van.png");
        $courier_services[0]->vehicle_types[] = $vt;
      }
    }
    $data = new stdClass();
    $data->courier_services = $courier_services;
    $data->services_duration = $service_duration;
    unset($service_duration);
    unset($courier_services);
    return $data;
  }

  public function baru()
  {
    //initial
    $dt = $this->__init();
    //error_reporting(0);

    //default result
    $data = array();
    $data['produk'] = new stdClass();
    $data['can_input_referral'] = '0';

    $this->seme_log->write("api_mobile", "Produk::baru -> ".json_encode($_POST));

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //start transaction and lock table
    $this->cpm->trans_start();

    // $totalRegisterBefore = $this->cpm->getForRegisterBefore($nation_code);
    // if ($totalRegisterBefore > 0) {
    //   $this->cpm->trans_rollback();
    //   $this->cpm->trans_end();
    //   $this->status = 1751;
    //   $this->message = 'Please try again';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    //   die();
    // }

    //START by Donny Dennison - 10 november 2022 14:34
    //new feature, join/input referral
    $limit = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E7");
    if (!isset($limit->remark)) {
      $limit = new stdClass();
      $limit->remark = 5;
    }

    if($pelanggan->b_user_id_recruiter == '0' && date("Y-m-d", strtotime($pelanggan->cdate." +".$limit->remark." days")) > date("Y-m-d")){
        $data['can_input_referral'] = '1';
    }
    //END by Donny Dennison - 10 november 2022 14:34
    //new feature, join/input referral

    //$this->__userUnconfirmedDenied($nation_code, $pelanggan);

    $this->status = 300;
    $this->message = 'Missing one or more parameters';
    
    //collect product input
    $nama = trim($this->input->post('nama'));
    $brand = trim($this->input->post('brand'));
    $b_kategori_id = (int) $this->input->post('b_kategori_id');
    // $b_berat_id = (int) $this->input->post('b_berat_id');
    $b_kondisi_id = (int) $this->input->post('b_kondisi_id');
    $b_user_alamat_id = (int) $this->input->post('b_user_alamat_id');
    $harga_jual = $this->input->post('harga_jual');
    $deskripsi_singkat = $this->input->post('deskripsi_singkat');
    $deskripsi = $this->input->post('deskripsi');
    // $satuan = trim($this->input->post('satuan'));
    // $berat = $this->input->post('berat');
    // $dimension_long = $this->input->post("dimension_long");
    // $dimension_width = $this->input->post("dimension_width");
    // $dimension_height = $this->input->post("dimension_height");
    // $vehicle_types = $this->input->post("vehicle_types");
    // $courier_services = $this->input->post("courier_services");
    // $services_duration = $this->input->post("services_duration");
    $stok = (int) $this->input->post("stok");
    // $is_include_delivery_cost = (int) $this->input->post("is_include_delivery_cost");
    $is_published = (int) $this->input->post("is_published");
    $model = $this->input->post("model");
    $color = $this->input->post("color");
    $year = $this->input->post("year");

    //by Donny Dennison - 7 december 2020 11:03
    //add new product type(meetup)
    $product_type = $this->input->post("product_type");

    //by Donny Dennison - 19 january 2022 10:35
    //merge table free product to table product
    $telp = $this->input->post("telp");

    //input validation
    if (empty($nama)) {
      $nama = '';
    }
    if (empty($brand)) {
      $brand = '';
    }
    if (empty($b_kategori_id)) {
      $b_kategori_id = 'null';
    }
    if (empty($b_kondisi_id)) {
      $b_kondisi_id = 'null';
    }
    // if (empty($b_berat_id)) {
    //   $b_berat_id = 'null';
    // }
    if (empty($b_user_alamat_id)) {
      $b_user_alamat_id = 0;
    }
    if (empty($deskripsi_singkat)) {
      $deskripsi_singkat = '';
    }
    if (empty($deskripsi)) {
      $deskripsi = '';
    }
    if (empty($foto)) {
      $foto = "media/produk/default.png";
    }
    // if (empty($satuan)) {
    //   $satuan = 'pcs';
    // }
    // if (strtolower($services_duration) == 'sameday' || strtolower($services_duration) == 'same day') {
    //   $services_duration = 'Same Day';
    // }
    // if (strtolower($services_duration) == 'nextday' || strtolower($services_duration) == 'next day') {
    //   $services_duration = 'Next Day';
    // }

    //validating FK
    // if ($b_kategori_id<=0) {
    //   $b_kategori_id = 0;
    // }
    if ($b_kondisi_id<=0) {
      $b_kondisi_id = 0;
    }
    // if ($b_berat_id<=0) {
    //   $b_berat_id = 0;
    // }

    //by Donny Dennison - 24 september 2021 15:31
    //simplify-data-inserted-and-edit-for-product-meetup-type
    //by Donny Dennison - 3 june 2022 13:10
    //new feature, product type santa
    // if($product_type == 'MeetUp' || $product_type == 'Free' || $product_type == 'Automotive'){
    if($product_type == 'MeetUp' || $product_type == 'Free' || $product_type == 'Automotive' || $product_type == 'Santa'){
      $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
      if(isset($pelangganAddress->id)){
        $b_user_alamat_id = $pelangganAddress->id;
        unset($pelangganAddress);
      }
      $b_kondisi_id = 4;

      //by Donny Dennison - 19 january 2022 10:35
      //merge table free product to table product
      // if($product_type == 'Free' && strlen($telp) <= 0){
      //   $this->status = 1102;
      //   $this->message = 'Please input phone number';
      //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      //   die();
      // }

      if($product_type == 'Free') {
        $harga_jual = 0;
        $b_kategori_id = 1;
      }

      if($product_type == 'Automotive') {

        if ($b_kategori_id == 'car') {
          $b_kategori_id = 32;
        }else if($b_kategori_id == 'motorcycle'){
          $b_kategori_id = 33;
        }

      }

      //by Donny Dennison - 3 june 2022 13:10
      //new feature, product type santa
      if($product_type == 'Santa') {
        $nama = "Santa";
        $harga_jual = 0;
        $b_kategori_id = 0;
      }

    }

    //validating user address
    if ($b_user_alamat_id<=0) {
      $this->cpm->trans_rollback();
      $this->cpm->trans_end();
      $this->status = 1098;
      $this->message = 'Invalid b_user_alamat_id';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }
    
    $almt = $this->bua->getByIdUserId($nation_code, $pelanggan->id, $b_user_alamat_id);
    if (!isset($almt->id)) {
      $this->cpm->trans_rollback();
      $this->cpm->trans_end();
      $this->status = 916;
      $this->message = 'Please choose pickup address';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $kat = $this->bkm3->getById($nation_code, $b_kategori_id);
    //by Donny Dennison - 3 june 2022 13:10
    //new feature, product type santa
    // if (!isset($kat->id)) {
    if (!isset($kat->id) && $product_type != 'Santa') {
      $this->cpm->trans_rollback();
      $this->cpm->trans_end();
      $this->status = 917;
      $this->message = 'Please choose product category';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $kon = $this->bkon->getById($nation_code, $b_kondisi_id);
    if (!isset($kon->id)) {
      $this->cpm->trans_rollback();
      $this->cpm->trans_end();
      $this->status = 919;
      $this->message = 'Please choose product condition';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //if($harga_jual<=0) $harga_jual = 1;
    if ($stok<=0) {
      $stok = 1;
    }
    // if ($berat<=0) {
    //   $berat = 1.0;
    // }
    // if ($dimension_long<=0) {
    //   $dimension_long = 1;
    // }
    // if ($dimension_width<=0) {
    //   $dimension_width = 1;
    // }
    // if ($dimension_height<=0) {
    //   $dimension_height = 1;
    // }

    //re-casting weight
    // $berat = $this->__floatWeight($berat);

    // $is_include_delivery_cost = !empty($is_include_delivery_cost) ? 1:0;
    $is_published = !empty($is_published) ? 1:0;

    //by Donny Dennison 24 february 2021 18:45
    //change ’ to ' in add & edit product name and description
    $nama = str_replace('’',"'",$nama);
    $deskripsi = str_replace('’',"'",$deskripsi);

    // //by Donny Dennison 16 augustus 2020 00:25
    // //fix check emoji in insert & edit product and discussion
    // if( preg_match( $this->unicodeRegexp, $nama ) ){

    //   $this->status = 1104;
    //   $this->message = 'Symbol image(ex.Emoji..) is not allowed in Product Name or Description';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    //   die();

    // }else if( preg_match( $this->unicodeRegexp, $deskripsi ) ){

    //   $this->status = 1104;
    //   $this->message = 'Symbol image(ex.Emoji..) is not allowed in Product Name or Description';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    //   die();

    // }

    // $nama = filter_var($nama, FILTER_SANITIZE_STRING);
    // $brand = filter_var($brand, FILTER_SANITIZE_STRING);
    // $deskripsi_singkat = filter_var($deskripsi_singkat, FILTER_SANITIZE_STRING);
    // $deskripsi = filter_var($deskripsi, FILTER_SANITIZE_STRING);
    $deskripsi = nl2br($deskripsi);

    //by Donny Dennison - 15 augustus 2020 15:09
    //bug fix \n (enter) didnt get remove
    $deskripsi = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $deskripsi);

    $deskripsi = str_replace("\\n", "<br />", $deskripsi);

    if (strlen($nama)<=0) {
      $this->cpm->trans_rollback();
      $this->cpm->trans_end();
      $this->status = 910;
      $this->message = 'Product name is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //by Donny Dennison - 24 september 2021 15:31
    //simplify-data-inserted-and-edit-for-product-meetup-type
    // if ($harga_jual<=0) {
    //by Donny Dennison - 3 june 2022 13:10
    //new feature, product type santa
    // if ($harga_jual<=0 && $product_type != 'Free') {
    if ($harga_jual<=0 && $product_type != 'Free' && $product_type != 'Santa') {
      $this->cpm->trans_rollback();
      $this->cpm->trans_end();
      $this->status = 911;
      $this->message = 'Price is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    if ($stok<=0) {
      $this->cpm->trans_rollback();
      $this->cpm->trans_end();
      $this->status = 1103;
      $this->message = 'Please specify quantity(stock) correctly';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    // //check dimension
    // $dimension_max = $this->__getDimensionMax($dimension_long, $dimension_width, $dimension_height);
    // if ($dimension_max>724) {
    //   $this->status = 918;
    //   $this->message = 'Product too big, we currently unsupported product with dimension above 7,2 m';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    //   die();
    // }

    //by Donny Dennison - 29 july 2020 - 15:47
    //prevent insert product duplication
    // if($product_type == 'Protection'){
    //   $duplicateProduct = $this->cpm->getActiveByUserIdProductNameWeightDimensionPrice($nation_code, $pelanggan->id, $nama, $berat, $dimension_long, $dimension_width, $dimension_height, $harga_jual);
    //   if (!empty($duplicateProduct)) {
    //     $this->status = 1109;
    //     $this->message = 'Your product has already been registered';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    //     die();
    //   }
    // }else if($product_type == 'MeetUp'){
    if($product_type == 'MeetUp'){
      $duplicateProduct = $this->cpm->getActiveByUserIdProductNameCategoryDescriptionPrice($nation_code, $pelanggan->id, $nama, $b_kategori_id, $deskripsi, $harga_jual);
      if (!empty($duplicateProduct)) {
        $this->cpm->trans_rollback();
        $this->cpm->trans_end();
        $this->status = 1113;
        $this->message = 'Your product is duplicated';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
        die();
      }
    }else if($product_type == 'Free'){
      $duplicateProduct = $this->cpm->getActiveByUserIdProductNameDescriptionTelephone($nation_code, $pelanggan->id, $nama, $deskripsi, $telp);
      if (!empty($duplicateProduct)) {
        $this->cpm->trans_rollback();
        $this->cpm->trans_end();
        $this->status = 1113;
        $this->message = 'Your product is duplicated';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
        die();
      }
    }else if($product_type == 'Automotive'){
      $duplicateProduct = $this->cpm->getActiveByUserIdProductNameBrandModelColorYearDescriptionPrice($nation_code, $pelanggan->id, $nama, $brand, $model, $color, $year, $deskripsi, $harga_jual);
      if (!empty($duplicateProduct)) {
        $this->cpm->trans_rollback();
        $this->cpm->trans_end();
        $this->status = 1113;
        $this->message = 'Your product is duplicated';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
        die();
      }
    //START by Donny Dennison - 3 june 2022 13:10
    //new feature, product type santa
    }else if($product_type == 'Santa'){
      $totalSantaProduct = $this->cpm->countAll($nation_code, "", "",$pelanggan->id, "", "", array(), array(), array(), "All", $almt, "Santa", 0, '', array(), array(), array(), 1);
      if ($totalSantaProduct > 0) {
        $this->cpm->trans_rollback();
        $this->cpm->trans_end();
        $this->status = 1112;
        $this->message = 'You can only have 1 Santa Product';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
        die();
      }
    //END by Donny Dennison - 3 june 2022 13:10
    }else{
      $this->cpm->trans_rollback();
      $this->cpm->trans_end();
      $this->status = 1110;
      $this->message = 'Please try add product again from homepage';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //get last id for first time
    // $cpm_id = $this->cpm->getLastId($nation_code);

    $endDoWhile = 0;
    do{
        $cpm_id = $this->GUIDv4();
        $checkId = $this->cpm->checkId($nation_code, $cpm_id);
        if($checkId == 0){
          $endDoWhile = 1;
        }
    }while($endDoWhile == 0);

    //initial insert with latest ID
    $di = array();
    $di['nation_code'] = $nation_code;
    $di['id'] = $cpm_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['b_user_alamat_id'] = $b_user_alamat_id;
    $di['b_kategori_id'] = $b_kategori_id;
    $di['b_kondisi_id'] = $b_kondisi_id;
    // $di['b_berat_id'] = $b_berat_id;
    $di['nama'] = $nama;
    $di['brand'] = $brand;
    $di['harga_jual'] = $harga_jual;
    $di['deskripsi_singkat'] = $deskripsi_singkat;
    $di['deskripsi'] = $deskripsi;
    $di['foto'] = $foto;
    $di['thumb'] = $foto;
    // $di['satuan'] = $satuan;
    $di['stok'] = $stok;
    // $di['berat'] = $berat;
    $di['berat'] = 1.0;
    // $di['dimension_long'] = $dimension_long;
    // $di['dimension_width'] = $dimension_width;
    // $di['dimension_height'] = $dimension_height;
    // $di['courier_services'] = $courier_services;
    // $di['vehicle_types'] = $vehicle_types;
    // $di['services_duration'] = $services_duration;
    $di['cdate'] = 'NOW()';

    //by Donny Dennison - 19 january 2022 10:35
    //merge table free product to table product
    $di['start_date'] = 'NOW()';

    // $di['is_include_delivery_cost'] = $is_include_delivery_cost;
    $di['is_published'] = $is_published;

    //by Donny Dennison - 7 december 2020 11:03
    //add new product type(meetup)
    $di['product_type'] = $product_type;

    $di['alamat2'] = $almt->alamat2;
    $di['kelurahan'] = $almt->kelurahan;
    $di['kecamatan'] = $almt->kecamatan;
    $di['kabkota'] = $almt->kabkota;
    $di['provinsi'] = $almt->provinsi;
    $di['kodepos'] = $almt->kodepos;
    $di['latitude'] = $almt->latitude;
    $di['longitude'] = $almt->longitude;

    //by Donny Dennison - 19 january 2022 10:35
    //merge table free product to table product
    if($product_type == 'Free'){
      $di['end_date'] = date("Y-m-d", strtotime("+".$this->produk_gratis_limit_hari." day"));
      $di['check_wanted'] = "1";
    }

    //by Donny Dennison - 3 june 2022 13:10
    //new feature, product type santa
    if($product_type == 'Santa'){
      $di['check_wanted'] = "1";
    }

    $res = $this->cpm->set($di);
    if (!$res) {
      $this->cpm->trans_rollback();
      $this->cpm->trans_end();
      $this->status = 1105;
      $this->message = "Error while posting product, please try again later";
      $this->seme_log->write("api_mobile", 'API_Mobile/Produk::baru -- RollBack -- forceClose '.$this->status.' '.$this->message);
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    // $this->cpm->trans_commit();

    if($product_type == "Automotive"){

      //initial insert product detail automotive with latest ID
      $di = array();
      $di['nation_code'] = $nation_code;
      $di['c_produk_id'] = $cpm_id;
      $di['model'] = $model;
      $di['color'] = $color;
      $di['year'] = $year;
      $res = $this->cpdam->set($di);
      if (!$res) {
        $this->cpm->trans_rollback();
        $this->cpm->trans_end();

        $this->status = 1105;
        $this->message = "Error while posting product, please try again later";
        $this->seme_log->write("api_mobile", 'API_Mobile/Produk::baru -- RollBack -- forceClose '.$this->status.' '.$this->message);
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
        die();
      }
      // $this->cpm->trans_commit();

    }

    $this->gdtrm->updateTotalData(DATE("Y-m-d"), "product_post", "+", "1");

    $this->status = 200;
    $this->message = "Success";
    if (!empty($is_published)) {
      $this->message = 'Your product Has Been Posted';
    } else {
      $this->message = 'Your product Has Been Saved as Draft';
    }
    $this->seme_log->write("api_mobile", 'API_Mobile/Produk::baru -- INFO '.$this->status.' '.$this->message);

    //doing image upload if success
    if ($res) {

      $totalFoto = 0;
      $checkFileExist = 1;
      $checkFileTemporaryOrNot = 1;

      for ($i=1; $i < 4; $i++) {

        $file_path = parse_url($this->input->post('foto'.$i), PHP_URL_PATH);

        if ($product_type == 'Santa' && $i == 1) {

          if (!file_exists(SENEROOT.$file_path)) {
            $checkFileExist = 0;
          }

        }else if ($product_type != 'Santa') {

          if (!file_exists(SENEROOT.$file_path)) {
            $checkFileExist = 0;
          }

        }

        if (strpos($file_path, 'temporary') !== false) {
        
          $totalFoto++;

        }
      
      }

      for ($i=1; $i < 6; $i++) {

        $file_path = parse_url($this->input->post('foto'.$i), PHP_URL_PATH);
        
        if($this->input->post('foto'.$i) != null){
          
          if (strpos($file_path, 'temporary') !== false) {

          }else{

            $checkFileTemporaryOrNot = 0;
          
          }

        }
      
      }

      if ($product_type == 'Santa') {

        if ($totalFoto < 1) {
          $this->status = 992;
          $this->message = 'Failed upload image product';
          $this->cpm->trans_rollback();
          $this->cpm->trans_end();
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
          die();
        }
      
      }else{

        if ($totalFoto < 3) {
          $this->status = 992;
          $this->message = 'Failed upload image product';
          $this->cpm->trans_rollback();
          $this->cpm->trans_end();
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
          die();
        }

      }

      if ($checkFileExist == 0) {
        $this->status = 995;
        $this->message = 'Failed upload, temporary already gone';
        $this->cpm->trans_rollback();
        $this->cpm->trans_end();
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
        die();
      }

      if ($checkFileTemporaryOrNot == 0) {
        $this->status = 996;
        $this->message = 'Failed upload, upload is not temporary';
        $this->cpm->trans_rollback();
        $this->cpm->trans_end();
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
        die();
      }

      $iq = new stdClass();
      // $iq->cpfm_id = 1;
      // $cpfm_last = $this->cpfm->getLastByProdukId($nation_code,$cpm_id);
      // if(isset($cpfm_last->id)){
      //   $iq->cpfm_id = $cpfm_last->id + 1;
      // }
      $iq->cpfm_id = $this->cpfm->getLastId($nation_code,$cpm_id);

      $iq->img_count = 0;
      if (isset($iq->cpfm_id)) {
        $cpfm_id = $iq->cpfm_id;
      }
      if (isset($iq->img_count)) {
        $img_count = $iq->img_count;
      }

      $keyname = 'foto1';
      // $upi = $this->__uploadImagex($nation_code, $keyname, $cpm_id, $iq->cpfm_id);
      $upi = $this->__moveImagex($nation_code, $this->input->post("foto1"), $cpm_id, $iq->cpfm_id);
      if(isset($upi->status)){
        if($upi->status == 200){
          $dpi = $this->__dataImageProduct($nation_code,$cpm_id,$cpfm_id,$upi->image,$upi->thumb,$iq->img_count);
          if($dpi){
            $iq->cpfm_id++;
            $iq->img_count++;
          }else{
            $this->status = 994;
            $this->message = 'Failed save uploaded image to db';
            $this->seme_log->write("api_mobile", 'API_Mobile/Produk::baru -- forceClose '.$this->status.' '.$this->message.' for '.$keyname.' CPID: '.$cpm_id.' CPFID: '.$cpfm_id);
            $this->cpm->trans_rollback();
            $this->cpm->trans_end();

            //START by Donny Dennison - 24 november 2021 16:48
            //bug fix product image still exist in db if upload failed
            $this->cpfm->delByProdukId($nation_code, $cpm_id);
            //END by Donny Dennison - 24 november 2021 16:48

            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
            die();
          }
        }else{
          $this->status = 1300;
          $this->message = 'Upload failed';
          $this->seme_log->write("api_mobile", 'API_Mobile/Produk::baru -- forceClose '.$this->status.' '.$this->message.' for '.$keyname);
          $this->cpm->trans_rollback();
          $this->cpm->trans_end();

          //START by Donny Dennison - 24 november 2021 16:48
          //bug fix product image still exist in db if upload failed
          $this->cpfm->delByProdukId($nation_code, $cpm_id);
          //END by Donny Dennison - 24 november 2021 16:48

          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
          die();
        }
      }else{
        $this->status = 992;
        $this->message = 'Failed upload image product';
        $this->seme_log->write("api_mobile", 'API_Mobile/Produk::baru -- forceClose '.$this->status.' '.$this->message.' for '.$keyname);
        $this->cpm->trans_rollback();
        $this->cpm->trans_end();

        //START by Donny Dennison - 24 november 2021 16:48
        //bug fix product image still exist in db if upload failed
        $this->cpfm->delByProdukId($nation_code, $cpm_id);
        //END by Donny Dennison - 24 november 2021 16:48

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
        die();
      }

      // $cpfm_id = 2;
      // $cpfm_last = $this->cpfm->getLastByProdukId($nation_code,$cpm_id);
      // if(isset($cpfm_last->id)){
      //   $iq->cpfm_id = $cpfm_last->id + 1;
      // }
      $iq->cpfm_id += 1;

      $img_count = 1;
      if (isset($iq->cpfm_id)) {
        $cpfm_id = $iq->cpfm_id;
      }
      if (isset($iq->img_count)) {
        $img_count = $iq->img_count;
      }

      $keyname = 'foto2';
      // $upi = $this->__uploadImagex($nation_code, $keyname, $cpm_id, $iq->cpfm_id);
      $upi = $this->__moveImagex($nation_code, $this->input->post("foto2"), $cpm_id, $iq->cpfm_id);
      if(isset($upi->status)){
        if($upi->status == 200){
          $dpi = $this->__dataImageProduct($nation_code,$cpm_id,$cpfm_id,$upi->image,$upi->thumb,$iq->img_count);
          if($dpi){
            $iq->cpfm_id++;
            $iq->img_count++;
          }else{
            $this->status = 994;
            $this->message = 'Failed save uploaded image to db';
            $this->seme_log->write("api_mobile", 'API_Mobile/Produk::baru -- forceClose '.$this->status.' '.$this->message.' for '.$keyname.' CPID: '.$cpm_id.' CPFID: '.$cpfm_id);
            $this->cpm->trans_rollback();
            $this->cpm->trans_end();

            //START by Donny Dennison - 24 november 2021 16:48
            //bug fix product image still exist in db if upload failed
            $this->cpfm->delByProdukId($nation_code, $cpm_id);
            //END by Donny Dennison - 24 november 2021 16:48

            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
            die();
          }
        }else{

          if ($product_type != 'Santa') {

            $this->status = 1300;
            $this->message = 'Upload failed';
            $this->seme_log->write("api_mobile", 'API_Mobile/Produk::baru -- forceClose '.$this->status.' '.$this->message.' for '.$keyname);
            $this->cpm->trans_rollback();
            $this->cpm->trans_end();

            //START by Donny Dennison - 24 november 2021 16:48
            //bug fix product image still exist in db if upload failed
            $this->cpfm->delByProdukId($nation_code, $cpm_id);
            //END by Donny Dennison - 24 november 2021 16:48

            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
            die();

          }

        }
      }else{
        $this->status = 992;
        $this->message = 'Failed upload image product';
        $this->seme_log->write("api_mobile", 'API_Mobile/Produk::baru -- forceClose '.$this->status.' '.$this->message.' for '.$keyname);
        $this->cpm->trans_rollback();
        $this->cpm->trans_end();

        //START by Donny Dennison - 24 november 2021 16:48
        //bug fix product image still exist in db if upload failed
        $this->cpfm->delByProdukId($nation_code, $cpm_id);
        //END by Donny Dennison - 24 november 2021 16:48

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
        die();
      }

      // $cpfm_id = 3;
      // $cpfm_last = $this->cpfm->getLastByProdukId($nation_code,$cpm_id);
      // if(isset($cpfm_last->id)){
      //   $iq->cpfm_id = $cpfm_last->id + 1;
      // }
      $iq->cpfm_id += 1;

      $img_count = 2;
      if (isset($iq->cpfm_id)) {
        $cpfm_id = $iq->cpfm_id;
      }
      if (isset($iq->img_count)) {
        $img_count = $iq->img_count;
      }

      $keyname = 'foto3';
      // $upi = $this->__uploadImagex($nation_code, $keyname, $cpm_id, $iq->cpfm_id);
      $upi = $this->__moveImagex($nation_code, $this->input->post("foto3"), $cpm_id, $iq->cpfm_id);

      if(isset($upi->status)){
        if($upi->status == 200){
          $dpi = $this->__dataImageProduct($nation_code,$cpm_id,$cpfm_id,$upi->image,$upi->thumb,$iq->img_count);
          if($dpi){
            $iq->cpfm_id++;
            $iq->img_count++;
          }else{
            $this->status = 994;
            $this->message = 'Failed save uploaded image to db';
            $this->seme_log->write("api_mobile", 'API_Mobile/Produk::baru -- forceClose '.$this->status.' '.$this->message.' for '.$keyname.' CPID: '.$cpm_id.' CPFID: '.$cpfm_id);
            $this->cpm->trans_rollback();
            $this->cpm->trans_end();

            //START by Donny Dennison - 24 november 2021 16:48
            //bug fix product image still exist in db if upload failed
            $this->cpfm->delByProdukId($nation_code, $cpm_id);
            //END by Donny Dennison - 24 november 2021 16:48

            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
            die();
          }
        }else{

          if ($product_type != 'Santa') {

            $this->status = 1300;
            $this->message = 'Upload failed';
            $this->seme_log->write("api_mobile", 'API_Mobile/Produk::baru -- forceClose '.$this->status.' '.$this->message.' for '.$keyname);
            $this->cpm->trans_rollback();
            $this->cpm->trans_end();

            //START by Donny Dennison - 24 november 2021 16:48
            //bug fix product image still exist in db if upload failed
            $this->cpfm->delByProdukId($nation_code, $cpm_id);
            //END by Donny Dennison - 24 november 2021 16:48

            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
            die();

          }

        }
      }else{
        $this->status = 992;
        $this->message = 'Failed upload image product';
        $this->seme_log->write("api_mobile", 'API_Mobile/Produk::baru -- forceClose '.$this->status.' '.$this->message.' for '.$keyname);
        $this->cpm->trans_rollback();
        $this->cpm->trans_end();

        //START by Donny Dennison - 24 november 2021 16:48
        //bug fix product image still exist in db if upload failed
        $this->cpfm->delByProdukId($nation_code, $cpm_id);
        //END by Donny Dennison - 24 november 2021 16:48
        
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
        die();
      }

      // $cpfm_id = 4;
      // $cpfm_last = $this->cpfm->getLastByProdukId($nation_code,$cpm_id);
      // if(isset($cpfm_last->id)){
      //   $iq->cpfm_id = $cpfm_last->id + 1;
      // }
      $iq->cpfm_id += 1;

      $img_count = 3;
      if (isset($iq->cpfm_id)) {
        $cpfm_id = $iq->cpfm_id;
      }
      if (isset($iq->img_count)) {
        $img_count = $iq->img_count;
      }

      $is_foto4 = 0;
      $is_foto4_r = 0;

      $keyname = 'foto4';
      // $ck = $this->__checkUploadedFile($keyname);
      // if($ck){
      if($this->input->post("foto4") != null){

        $is_foto4 = 1;

        // $upi = $this->__uploadImagex($nation_code, $keyname, $cpm_id, $iq->cpfm_id);
        $upi = $this->__moveImagex($nation_code, $this->input->post("foto4"), $cpm_id, $iq->cpfm_id);

        if(isset($upi->status)){
          if($upi->status == 200){
            $dpi = $this->__dataImageProduct($nation_code,$cpm_id,$cpfm_id,$upi->image,$upi->thumb,$iq->img_count);
            if($dpi){
              $iq->cpfm_id++;
              $iq->img_count++;
              $is_foto4_r = 1;
            }
          }
        }
      }

      // $cpfm_id = 5;
      // $cpfm_last = $this->cpfm->getLastByProdukId($nation_code,$cpm_id);
      // if(isset($cpfm_last->id)){
      //   $iq->cpfm_id = $cpfm_last->id + 1;
      // }
      // $iq->cpfm_id = $this->cpfm->getLastId($nation_code,$cpm_id);
      $iq->cpfm_id += 1;

      $img_count = 4;
      if (isset($iq->cpfm_id)) {
        $cpfm_id = $iq->cpfm_id;
      }
      if (isset($iq->img_count)) {
        $img_count = $iq->img_count;
      }

      $is_foto5 = 0;
      $is_foto5_r = 0;

      $keyname = 'foto5';
      // $ck = $this->__checkUploadedFile($keyname);
      // if($ck){
      if($this->input->post("foto5") != null){

        $is_foto5 = 1;
        
        // $upi = $this->__uploadImagex($nation_code, $keyname, $cpm_id, $iq->cpfm_id);
        $upi = $this->__moveImagex($nation_code, $this->input->post("foto5"), $cpm_id, $iq->cpfm_id);

        if(isset($upi->status)){
          if($upi->status == 200){
            $dpi = $this->__dataImageProduct($nation_code,$cpm_id,$cpfm_id,$upi->image,$upi->thumb,$iq->img_count);
            if($dpi){
              $iq->cpfm_id++;
              $iq->img_count++;
              $is_foto5_r = 1;
            }
          }
        }
      }

      $insertVideo = 0;

      //looping for get list of video
      for ($i=1; $i < 6; $i++) {

        if($this->input->post('video'.$i) === "yes"){
          $insertVideo++;
        }

      }

      if($insertVideo > 0){
        //insert to c_produk_foto table
        for ($i=1; $i <= $insertVideo; $i++) {
          $cpfm_last = $this->cpfm->getLastId($nation_code,$cpm_id, "video");

          $upi = $this->__moveImagex($nation_code, $this->input->post("video".$i."_thumb"), $cpm_id, $cpfm_last);

          $dix = array();
          $dix['nation_code'] = $nation_code;
          $dix['c_produk_id'] = $cpm_id;
          $dix['id'] = $cpfm_last;
          $dix['jenis'] = 'video';
          $dix['convert_status'] = 'uploading';
          if($upi->status == 200){
            $dix['url'] = $upi->image;
            $dix['url_thumb'] = $upi->thumb;
          }else{
            $dix['url'] = "media/produk_video/default.png";
            $dix['url_thumb'] = "media/produk_video/default.png";
          }
          $dix['caption'] = '';
          $this->cpfm->set($dix);
          // $this->cpm->trans_commit();
        }
        $this->gdtrm->updateTotalData(DATE("Y-m-d"), "product_video", "+", "1");
      }

      usleep(500000);
      //by Donny Dennison - 15 february 2022 9:50
      //category product and category community have more than 1 language
      // $data['produk'] = $this->cpm->getById($nation_code, $cpm_id);
      $data['produk'] = $this->cpm->getById($nation_code, $cpm_id, $pelanggan, $product_type, $pelanggan->language_id);

      $data['produk']->galeri = $this->cpfm->getByProdukId($nation_code, $cpm_id);

      $data['produk']->galeri_video = $this->cpfm->getByProdukId($nation_code, $cpm_id, "video");

      //building product data for response
      $i = 0;
      $dix = array();
      $dix['nation_code'] = $nation_code;
      $dix['c_produk_id'] = $cpm_id;
      $dix['caption'] = '';
      $dix['is_active'] = 1;
      foreach ($data['produk']->galeri as &$gal) {
        $dix['url'] = '';
        $dix['url_thumb'] = '';
        if (isset($gal->url)) {
          $gal->url = str_replace("//", "/", $gal->url);
          if($i==0) $data['produk']->foto = $gal->url;
          $dix['url'] = $gal->url;
          $gal->url = $this->cdn_url($gal->url);
        }

        if (isset($gal->url_thumb)) {
          $gal->url_thumb = str_replace("//", "/", $gal->url_thumb);
          if($i==0) $data['produk']->thumb = $gal->url_thumb;
          $dix['url_thumb'] = $gal->url_thumb;
          $gal->url_thumb = $this->cdn_url($gal->url_thumb);
        }

        if(!empty($is_foto4) && empty($is_foto4_r) && strlen($dix['url'])>4 && strlen($dix['url_thumb'])>4 && $i==1){
          $cpfm_last = $this->cpfm->getLastByProdukId($nation_code,$cpm_id);
          if(isset($cpfm_last->id)){
            $dix['id'] = $cpfm_last->id+1;

            //by Donny Dennison - 30 november 2021 14:46
            //fix if foto4 or foto5 failed then copy foto2 or foto3
            $newURL = "$nation_code-$cpm_id-".$dix['id']. pathinfo($path, PATHINFO_EXTENSION);
            $newURLThumb = "$nation_code-$cpm_id-".$dix['id']."-thumb". pathinfo($path, PATHINFO_EXTENSION);
            copy($dix['url'],$newURL);
            copy($dix['url_thumb'],$newURLThumb);
            $dix['url'] = $newURL;
            $dix['url_thumb'] = $newURLThumb;

            $this->cpfm->set($dix);
            $this->seme_log->write("api_mobile", 'API_Mobile/Produk::baru -- INFO upload failed for foto4 replaced by foto2 SUCCESS');
          }
        }

        if(!empty($is_foto5) && empty($is_foto5_r) && strlen($dix['url'])>4 && strlen($dix['url_thumb'])>4 && $i==2 ){
          $cpfm_last = $this->cpfm->getLastByProdukId($nation_code,$cpm_id);
          if(isset($cpfm_last->id)){
            $dix['id'] = $cpfm_last->id+1;

            //by Donny Dennison - 30 november 2021 14:46
            //fix if foto4 or foto5 failed then copy foto2 or foto3
            $newURL = "$nation_code-$cpm_id-".$dix['id']. pathinfo($path, PATHINFO_EXTENSION);
            $newURLThumb = "$nation_code-$cpm_id-".$dix['id']."-thumb". pathinfo($path, PATHINFO_EXTENSION);
            copy($dix['url'],$newURL);
            copy($dix['url_thumb'],$newURLThumb);
            $dix['url'] = $newURL;
            $dix['url_thumb'] = $newURLThumb;

            $this->cpfm->set($dix);
            $this->seme_log->write("api_mobile", 'API_Mobile/Produk::baru -- INFO upload failed for foto5 replaced by foto3 SUCCESS');
          }
        }

        $i++;
      }
      unset($dix);

      foreach ($data['produk']->galeri_video as &$gal_vid) {
        if (isset($gal_vid->url)) {
          $gal_vid->url = str_replace("//", "/", $gal_vid->url);
          $gal_vid->url = $this->cdn_url($gal_vid->url);
        }

        if (isset($gal_vid->url_thumb)) {
          $gal_vid->url_thumb = str_replace("//", "/", $gal_vid->url_thumb);
          $gal_vid->url_thumb = $this->cdn_url($gal_vid->url_thumb);
        }

      }

      //update image
      $this->cpm->update2($nation_code,$cpm_id,array("foto"=>$data['produk']->foto,"thumb"=>$data['produk']->thumb));

      $data['produk']->nama = html_entity_decode($data['produk']->nama,ENT_QUOTES);
      $data['produk']->deskripsi = html_entity_decode($data['produk']->deskripsi,ENT_QUOTES);

      if (isset($data['produk']->b_kondisi_icon)) {
        $data['produk']->b_kondisi_icon = $this->cdn_url($data['produk']->b_kondisi_icon);
      }
      if (isset($data['produk']->b_berat_icon)) {
        $data['produk']->b_berat_icon = $this->cdn_url($data['produk']->b_berat_icon);
      }
      if (isset($data['produk']->b_user_image_seller)) {
        
        // by Muhammad Sofi - 27 October 2021 10:12
        // if user img & banner not exist or empty, change to default image
        // $data['produk']->b_user_image_seller = $this->cdn_url($data['produk']->b_user_image_seller);
        if(file_exists(SENEROOT.$data['produk']->b_user_image_seller) && $data['produk']->b_user_image_seller != 'media/user/default.png'){
          $data['produk']->b_user_image_seller = $this->cdn_url($data['produk']->b_user_image_seller);
        } else {
          $data['produk']->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        }
      }
      if (isset($data['produk']->foto)) {
        $data['produk']->foto = $this->cdn_url($data['produk']->foto);
      }
      if (isset($data['produk']->thumb)) {
        $data['produk']->thumb = $this->cdn_url($data['produk']->thumb);
      }

      //by Donny Dennison - 22 february 2022 17:42
      //change product_type language
      if($pelanggan->language_id == 2){
        if($data['produk']->product_type == "Protection"){
          $data['produk']->product_type = "Proteksi";
        } else if($data['produk']->product_type == "Automotive"){
          $data['produk']->product_type = "Otomotif";
        } else if($data['produk']->product_type == "Free"){
          $data['produk']->product_type = "Gratis";
        }
      }

      //check logger
      if ($this->is_log) {
        $this->seme_log->write("api_mobile", 'API_Mobile/Produk::baru -> is_published: '.$is_published);
      }

      if (!empty($is_published)) {
        //START by Donny Dennison - 16 december 2021 15:49
        //get point as leaderboard rule
        //START by Donny Dennison - 12 December 2022 15:24
        //Set daily limit 15 to product registration
        //START by Donny Dennison - 08 january 2024 11:51
        //get spt when create product(5 spt for word only, 10 spt for video)
        //get limit left
        $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "E10");
        if(!isset($limitLeft->limit_plus)){
          $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E10");

          $lastID = $this->glplm->getLastId($nation_code, date("Y-m-d"));
          $du = array();
          $du['nation_code'] = $nation_code;
          $du['id'] = $lastID;
          $du['cdate'] = date("Y-m-d");
          $du['b_user_id'] = $pelanggan->id;
          $du['code'] = "E10";
          $du['limit_plus'] = $pointGet->remark;
          $du['limit_minus'] = $pointGet->remark;
          $this->glplm->set($du);

          //get limit left
          $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "E10");
        }

        if($limitLeft->limit_plus > 0){
          //get point
          $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EA");
          if (!isset($pointGet->remark)) {
              $pointGet = new stdClass();
              $pointGet->remark = 5;
          }

          $pelangganAddress = $this->bua->getById($nation_code, $pelanggan->id, $b_user_alamat_id);
          $di = array();
          $di['nation_code'] = $nation_code;
          $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
          $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
          $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
          $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
          $di['b_user_id'] = $pelanggan->id;
          $di['point'] = $pointGet->remark;
          $di['custom_id'] = $cpm_id;
          $di['custom_type'] = 'product';
          $di['custom_type_sub'] = 'post';
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
          // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_post', '+', 1);
          $this->glplm->updateTotal($nation_code, date("Y-m-d"), $pelanggan->id, 'E10', 'limit_plus', '-', 1);
        }
        //END by Donny Dennison - 16 december 2021 15:49
        //get point as leaderboard rule
        //END by Donny Dennison - 12 December 2022 15:24
        //Set daily limit 15 to product registration
        //END by Donny Dennison - 08 january 2024 11:51
        //get spt when create product(5 spt for word only, 10 spt for video)
      }
    }

    //commit and end transaction
    $this->cpm->trans_commit();
    $this->cpm->trans_end();

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }

  public function hapus($c_produk_id="")
  {
    $dt = $this->__init();
    $data = array();
    $data['can_input_referral'] = '0';

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //START by Donny Dennison - 10 november 2022 14:34
    //new feature, join/input referral
    $limit = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E7");
    if (!isset($limit->remark)) {
      $limit = new stdClass();
      $limit->remark = 5;
    }

    if($pelanggan->b_user_id_recruiter == '0' && date("Y-m-d", strtotime($pelanggan->cdate." +".$limit->remark." days")) > date("Y-m-d")){
        $data['can_input_referral'] = '1';
    }
    //END by Donny Dennison - 10 november 2022 14:34
    //new feature, join/input referral

    $c_produk_id = $c_produk_id;
    if ($c_produk_id<='0') {
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $getProductType = $this->cpm->getProductType($nation_code, $c_produk_id);
    if(!isset($getProductType->product_type)){
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $getProductType = $getProductType->product_type;

    $produk = $this->cpm->getById($nation_code, $c_produk_id, $pelanggan, $getProductType);
    if (!isset($produk->id)) {
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    if ($produk->b_user_id_seller != $pelanggan->id) {
      $this->status = 908;
      $this->message = 'Access denied, you cant delete other people product';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //START by Donny Dennison - 12 july 2022 14:56
    //new offer system
    $listOffering = $this->ecrm->getAll($nation_code, 'offer', $c_produk_id, "offering");
    if($listOffering){
      $this->status = 920;
      $this->message = 'Cannot delete because there is offer';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();

    }

    $listOffering = $this->ecrm->getAll($nation_code, 'offer', $c_produk_id, "accepted");
    if($listOffering){
      $this->status = 921;
      $this->message = 'Cannot delete because havent completed the review';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();

    }

    $listOffering = $this->ecrm->getAll($nation_code, 'offer', $c_produk_id, "waiting review from seller");
    if($listOffering){
      $this->status = 921;
      $this->message = 'Cannot delete because havent completed the review';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();

    }

    $listOffering = $this->ecrm->getAll($nation_code, 'offer', $c_produk_id, "waiting review from buyer");
    if($listOffering){
      $this->status = 921;
      $this->message = 'Cannot delete because havent completed the review';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();

    }
    //END by Donny Dennison - 12 july 2022 14:56

    //get product images
    $images = $this->cpfm->getByProdukId($nation_code, $c_produk_id, "all");

    //start transaction
    $this->cpm->trans_start();

    //check delete method
    if ($this->is_soft_delete) {
      $du = array();
      $du['foto'] = "media/produk/default.png";
      $du['thumb'] = "media/produk/default.png";

      //by Donny Dennison - 01 september 2022 13:38
      //change stok to 0 if delete product
      $du['stok'] = 0;

      $du['is_published'] = 0;
      $du['is_visible'] = 0;
      $du['is_active'] = 0;
      $res = $this->cpm->update($nation_code, $pelanggan->id, $c_produk_id, $du);
      if ($res) {
        // $this->cpm->trans_commit();
        $res2 = $this->cpfm->delByProdukId($nation_code, $c_produk_id);
        if ($res2) {
          // $this->cpm->trans_commit();
          $this->status = 200;
          $this->message = 'Product deleted successfully';

          //delete product images file
          if (count($images)) {
            foreach ($images as $img) {
              if($img->url != "media/produk_video/default.png"){
                $file_path = SENEROOT.$img->url;
                if (file_exists($file_path)) {
                  unlink($file_path);
                }
              }
              if($img->url_thumb != "media/produk_video/default.png"){
                $file_path = SENEROOT.$img->url_thumb;
                if (file_exists($file_path)) {
                  unlink($file_path);
                }
              }
            }
            unset($img);
          }

          //remove from cart
          $c_produk_ids = array($c_produk_id);
          $this->cart->delAllByProdukIds($nation_code, $c_produk_ids);
          // $this->cpm->trans_commit();

          //remove from wishlist
          $c_produk_ids = array($c_produk_id);
          $this->dwlm->delAllByProdukIds($nation_code, $c_produk_ids);
          // $this->cpm->trans_commit();

          //by Donny Dennison - 22 september 2021 15:01
          //revamp-profile
          $this->buwp->delete($nation_code, $c_produk_id);
          // $this->cpm->trans_commit();

          //START by Donny Dennison - 16 december 2021 15:49
          //decrease total post in leaderboard
          if($produk->is_published == 1){
            //START by Donny Dennison 12 December 2022 - 15:24
            //Set daily limit 15 to product registration
            //get limit left
            $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "E10");
            if(!isset($limitLeft->limit_minus)){
              $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E10");

              $lastID = $this->glplm->getLastId($nation_code, date("Y-m-d"));
              $du = array();
              $du['nation_code'] = $nation_code;
              $du['id'] = $lastID;
              $du['cdate'] = date("Y-m-d");
              $du['b_user_id'] = $pelanggan->id;
              $du['code'] = "E10";
              $du['limit_plus'] = $pointGet->remark;
              $du['limit_minus'] = $pointGet->remark;
              $this->glplm->set($du);

              //get limit left
              $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "E10");
            }

            if($limitLeft->limit_minus > 0){
              //get point
              $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EA");
              if (!isset($pointGet->remark)) {
                  $pointGet = new stdClass();
                  $pointGet->remark = 5;
              }

              $di = array();
              $di['nation_code'] = $nation_code;
              $di['b_user_alamat_location_kelurahan'] = $produk->kelurahan;
              $di['b_user_alamat_location_kecamatan'] = $produk->kecamatan;
              $di['b_user_alamat_location_kabkota'] = $produk->kabkota;
              $di['b_user_alamat_location_provinsi'] = $produk->provinsi;
              $di['b_user_id'] = $pelanggan->id;
              $di['plusorminus'] = "-";
              $di['point'] = $pointGet->remark;
              $di['custom_id'] = $c_produk_id;
              $di['custom_type'] = 'product';
              $di['custom_type_sub'] = 'post';
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
              // $this->cpm->trans_commit();
              // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
              // $this->cpm->trans_commit();
              // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_post', '-', 1);
              // $this->cpm->trans_commit();
              $this->glplm->updateTotal($nation_code, date("Y-m-d"), $pelanggan->id, 'E10', 'limit_minus', '-', 1);
              // $this->cpm->trans_commit();
              $this->glplm->updateTotal($nation_code, date("Y-m-d"), $pelanggan->id, 'E10', 'limit_plus', '+', 1);
              // $this->cpm->trans_commit();
            }
            //END by Donny Dennison 12 December 2022 - 15:24
            //Set daily limit 15 to product registration

            //START by Donny Dennison - 25 july 2022 11:40
            //change point get rule for group chat community and upload video product
            foreach($images as $img){
              if($img->jenis == "video" && $img->convert_status != "uploading"){
                //START by Donny Dennison 12 December 2022 - 15:24
                //Set daily limit to Video registration : 10 to Community posts and 15 to products
                //get limit left
                $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "E8");
                if(!isset($limitLeft->limit_minus)){
                  $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E8");

                  $lastID = $this->glplm->getLastId($nation_code, date("Y-m-d"));
                  $du = array();
                  $du['nation_code'] = $nation_code;
                  $du['id'] = $lastID;
                  $du['cdate'] = date("Y-m-d");
                  $du['b_user_id'] = $pelanggan->id;
                  $du['code'] = "E8";
                  $du['limit_plus'] = $pointGet->remark;
                  $du['limit_minus'] = $pointGet->remark;
                  $this->glplm->set($du);

                  //get limit left
                  $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "E8");
                }

                if($limitLeft->limit_minus > 0){
                  //get point
                  $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EO");
                  if (!isset($pointGet->remark)) {
                      $pointGet = new stdClass();
                      $pointGet->remark = 10;
                  }

                  $di = array();
                  $di['nation_code'] = $nation_code;
                  $di['b_user_alamat_location_kelurahan'] = $produk->kelurahan;
                  $di['b_user_alamat_location_kecamatan'] = $produk->kecamatan;
                  $di['b_user_alamat_location_kabkota'] = $produk->kabkota;
                  $di['b_user_alamat_location_provinsi'] = $produk->provinsi;
                  $di['b_user_id'] = $pelanggan->id;
                  $di['plusorminus'] = "-";
                  $di['point'] = $pointGet->remark;
                  $di['custom_id'] = $c_produk_id;
                  $di['custom_type'] = 'product';
                  $di['custom_type_sub'] = 'upload video';
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
                  // $this->cpm->trans_commit();
                  // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '+', $di['point']);
                  // $this->cpm->trans_commit();
                  $this->glplm->updateTotal($nation_code, date("Y-m-d"), $pelanggan->id, 'E8', 'limit_minus', '-', 1);
                  $this->glplm->updateTotal($nation_code, date("Y-m-d"), $pelanggan->id, 'E8', 'limit_plus', '+', 1);
                }
                //END by Donny Dennison 12 December 2022 - 15:24
                //Set daily limit to Video registration : 10 to Community posts and 15 to products
                break;
              }
            }
            //END by Donny Dennison - 25 july 2022 11:40
            //change point get rule for group chat community and upload video product

            // // START by Donny Dennison - 15 September 2022 11:38
            // // revisi rule deduct point get from share product(only for seller) if delete product
            // $totalShareBySeller = $this->glphm->countAll($nation_code, $produk->kelurahan, $produk->kecamatan, $produk->kabkota, $produk->provinsi, $pelanggan->id, "+", $c_produk_id, "product", "share", date("Y-m-d"), "");
            // if($totalShareBySeller > 0){
            //   $di = array();
            //   $di['nation_code'] = $nation_code;
            //   $di['b_user_alamat_location_kelurahan'] = $produk->kelurahan;
            //   $di['b_user_alamat_location_kecamatan'] = $produk->kecamatan;
            //   $di['b_user_alamat_location_kabkota'] = $produk->kabkota;
            //   $di['b_user_alamat_location_provinsi'] = $produk->provinsi;
            //   $di['b_user_id'] = $pelanggan->id;
            //   $di['plusorminus'] = "-";
            //   $di['point'] = $pointGet->remark * $totalShareBySeller;
            //   $di['custom_id'] = $c_produk_id;
            //   $di['custom_type'] = 'product';
            //   $di['custom_type_sub'] = 'share';
            //   $di['custom_text'] = $pelanggan->fnama.' has delete '.$di['custom_type'].' '.$di['custom_type_sub'].' and lose '.$di['point'].' point(s)';
            //   $endDoWhile = 0;
            //   do{
            //     $leaderBoardHistoryId = $this->GUIDv4();
            //     $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
            //     if($checkId == 0){
            //       $endDoWhile = 1;
            //     }
            //   }while($endDoWhile == 0);
            //   $di['id'] = $leaderBoardHistoryId;
            //   $this->glphm->set($di);
            //   // $this->cpm->trans_commit();
            //   // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
            //   // $this->cpm->trans_commit();
            // }
            // //END by Donny Dennison - 15 September 2022 11:38
            // //revisi rule deduct point get from share product(only for seller) if delete product
          }
          //END by Donny Dennison - 16 december 2021 15:49
          //decrease total post in leaderboard

        } else {
          $this->cpm->trans_rollback();
          $this->status = 941;
          $this->message = 'Failed deleting product images';
        }
      } else {
        $this->cpm->trans_rollback();
        $this->status = 940;
        $this->message = "Can't delete products from database, please try again later";
      }
    } else {
      $res = $this->cpm->del($nation_code, $pelanggan->id, $c_produk_id);
      if ($res) {
        $this->cpm->trans_commit();
        //delete product images from db
        $res2 = $this->cpfm->delByProdukId($nation_code, $c_produk_id);
        if ($res2) {
          $this->cpm->trans_commit();
          $this->status = 200;
          $this->message = 'Product deleted successfully';
          //delete product images file
          if (count($images)) {
            foreach ($images as $img) {
              $fileloc = SENEROOT.$img->url;
              if (file_exists($fileloc)) {
                unlink($fileloc);
              }
              $fileloc = SENEROOT.$img->url_thumb;
              if (file_exists($fileloc)) {
                unlink($fileloc);
              }
            }
          }
          $fileloc = SENEROOT.$produk->foto;
          if ($produk->foto != 'default.png' && (!is_dir($fileloc)) && file_exists($fileloc)) {
            unlink($fileloc);
          }
          $fileloc = SENEROOT.$produk->thumb;
          if ($produk->foto != 'default.png' && (!is_dir($fileloc)) && file_exists($fileloc)) {
            unlink($fileloc);
          }
        } else {
          $this->cpm->trans_rollback();
          $this->status = 941;
          $this->message = 'Failed deleting product images';
        }
        //end delete file images;
      } else {
        $this->cpm->trans_rollback();
        $this->status = 940;
        $this->message = "Can't delete products from database, please try again later";
      }
    }
    $this->cpm->trans_commit();
    $this->cpm->trans_end();

    //render output
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }
  
  //START by Donny Dennison - 1 december 2021 13:55
  //change flow add attachment / photo in api product(add & edit), api free product(add & edit),api automotive product(add & edit), and community(add & edit)
  // public function image_add($c_produk_id)
  // {
  //   $dt = $this->__init();
  //   $keyname = 'foto';

  //   $data = array();
  //   $data['produk'] = new stdClass();

  //   //check nation_code
  //   $nation_code = $this->input->get('nation_code');
  //   $nation_code = $this->nation_check($nation_code);
  //   if (empty($nation_code)) {
  //     $this->status = 101;
  //     $this->message = 'Missing or invalid nation_code';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }

  //   //check apisess
  //   $apisess = $this->input->get('apisess');
  //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   if (!isset($pelanggan->id)) {
  //     $this->status = 401;
  //     $this->message = 'Missing or invalid API session';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }

  //   $c_produk_id = (int) $c_produk_id;
  //   if ($c_produk_id<=0) {
        // $this->status = 595;
        // $this->message = 'Invalid product ID or Product not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }

  //   $produk = $this->cpm->getById($nation_code, $c_produk_id);
  //   if (!isset($produk->id)) {
        // $this->status = 595;
        // $this->message = 'Invalid product ID or Product not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }

  //   if ($produk->b_user_id_seller != $pelanggan->id) {
  //     $this->status = 907;
  //     $this->message = 'Access denied, you cant change other people product';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }
  //   if (!isset($_FILES[$keyname])) {
  //     $this->status = 1300;
  //     $this->message = 'Upload failed';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }
  //   if ($_FILES[$keyname]['size']<=0) {
  //     $this->status = 1301;
  //     $this->message = 'File upload failed.';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }
  //   if ($_FILES[$keyname]['size']>=2500000) {
  //     $this->status = 1302;
  //     $this->message = 'Image file Size too big';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }
  //   if (mime_content_type($_FILES[$keyname]['tmp_name'])=="image/webp") {
  //     $this->status = 1303;
  //     $this->message = 'WebP image file format is not supported.';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }
  //   if (mime_content_type($_FILES[$keyname]['tmp_name'])=="image/webp") {
  //     $this->status = 1304;
  //     $this->message = 'WebP image file format is not supported.';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }
  //   $filenames = pathinfo($_FILES[$keyname]['name']);
  //   $fileext = '';
  //   if (isset($filenames['extension'])) {
  //     $fileext = strtolower($filenames['extension']);
  //   }
  //   if (!in_array($fileext, array("jpg","png","jpeg"))) {
  //     $this->status = 1305;
  //     $this->message = 'Invalid file extension, please try other file.';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }

  //   $targetdir = $this->media_produk;
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

  //   $tahun = date("Y");
  //   $targetdir = $targetdir.DIRECTORY_SEPARATOR.$tahun;
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

  //   $bulan = date("m");
  //   $targetdir = $targetdir.DIRECTORY_SEPARATOR.$bulan;
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

  //   $ke = $this->cpfm->getLastId($nation_code, $c_produk_id);
  //   $filename = "$nation_code-$c_produk_id-$ke";
  //   $filethumb = $filename.'-thumb';
  //   $filecheck = SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename.'.'.$fileext;
  //   if (file_exists($filecheck)) {
  //     $rand = rand(0, 999);
  //     $filename = "$nation_code-$c_produk_id-$ke-".$rand;
  //     $filecheck = SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename.'.'.$fileext;
  //     if (file_exists($filecheck)) {
  //       $rand = rand(1000, 99999);
  //       $filename = "$nation_code-$c_produk_id-$ke-".$rand;
  //     }
  //   };
  //   $filethumb = $filename."-thumb.".$fileext;
  //   $filename = $filename.".".$fileext;

  //   move_uploaded_file($_FILES[$keyname]['tmp_name'], SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
  //   $this->lib("wideimage/WideImage", 'wideimage', "inc");
  //   if (file_exists(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb)) {
  //     unlink(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
  //   }
  //   WideImage::load(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)->reSize(370)->saveToFile(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);

  //   $this->status = 200;
  //   $this->message = 'Success';

  //   $dix = array();
  //   $dix['nation_code'] = $nation_code;
  //   $dix['id'] = $ke;
  //   $dix['c_produk_id'] = $c_produk_id;
  //   $dix['url_thumb'] = str_replace("//", "/", $targetdir.'/'.$filethumb);
  //   $dix['url'] = str_replace("//", "/", $targetdir.'/'.$filename);
  //   $dix['is_active'] = 1;
  //   $dix['caption'] = '';
  //   $res = $this->cpfm->set($dix);
  //   if ($res) {
  //     $this->status = 200;
  //     $this->message = 'Success';
  //     if ($ke==1) {
  //       $du = array();
  //       $du['foto'] = $dix['url'];
  //       $du['thumb'] = $dix['url_thumb'];
  //       $this->cpm->update($nation_code, $pelanggan->id, $c_produk_id, $du);
  //     }
  //     $data['produk'] = $this->cpm->getById($nation_code, $c_produk_id);
  //     $data['produk']->galeri = $this->cpfm->getByProdukId($nation_code, $c_produk_id);
  //     foreach ($data['produk']->galeri as &$gal) {
  //       if (isset($gal->url)) {
  //         $gal->url = str_replace("//", "/", $gal->url);
  //         $gal->url = $this->cdn_url($gal->url);
  //       }
  //       if (isset($gal->url_thumb)) {
  //         $gal->url_thumb = str_replace("//", "/", $gal->url_thumb);
  //         $gal->url_thumb = $this->cdn_url($gal->url_thumb);
  //       }
  //     }
  //     if (isset($data['produk']->b_kondisi_icon)) {
  //       $data['produk']->b_kondisi_icon = $this->cdn_url($data['produk']->b_kondisi_icon);
  //     }
  //     if (isset($data['produk']->b_berat_icon)) {
  //       $data['produk']->b_berat_icon = $this->cdn_url($data['produk']->b_berat_icon);
  //     }
  //     if (isset($data['produk']->b_user_image_seller)) {

  //       // by Muhammad Sofi - 27 October 2021 10:12
  //       // if user img & banner not exist or empty, change to default image
  //       // $data['produk']->b_user_image_seller = $this->cdn_url($data['produk']->b_user_image_seller);
  //       if(file_exists(SENEROOT.$data['produk']->b_user_image_seller) && $data['produk']->b_user_image_seller != 'media/user/default.png'){
  //         $data['produk']->b_user_image_seller = $this->cdn_url($data['produk']->b_user_image_seller);
  //       } else {
  //         $data['produk']->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
  //       }
  //     }
  //     if (isset($data['produk']->foto)) {
  //       $data['produk']->foto = $this->cdn_url($data['produk']->foto);
  //     }
  //     if (isset($data['produk']->thumb)) {
  //       $data['produk']->thumb = $this->cdn_url($data['produk']->thumb);
  //     }
  //   } else {
  //     $this->status = 1305;
  //     $this->message = 'Failed insert to database';
  //   }
  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  // }

  public function image_add()
  {
    $dt = $this->__init();
    $keyname = 'foto';

    $data = array();
    $data['foto_url'] = '';

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    if (!isset($_FILES[$keyname])) {
      $this->status = 1300;
      $this->message = 'Upload failed';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }
    if ($_FILES[$keyname]['size']<=0) {
      $this->status = 1300;
      $this->message = 'Upload failed';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }
    // if ($_FILES[$keyname]['size']>=2500000) {
    //   $this->status = 1302;
    //   $this->message = 'Image file Size too big';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    //   die();
    // }
    if (mime_content_type($_FILES[$keyname]['tmp_name'])=="image/webp") {
      $this->status = 1303;
      $this->message = 'WebP image file format is not supported.';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }
    $filenames = pathinfo($_FILES[$keyname]['name']);
    $fileext = '';
    if (isset($filenames['extension'])) {
      $fileext = strtolower($filenames['extension']);
    }
    if (!in_array($fileext, array("jpg","png","jpeg"))) {
      $this->status = 1305;
      $this->message = 'Invalid file extension, please try other file';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $targetdir = $this->media_temporary;
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

    $filename = "$nation_code-$pelanggan->id-".date('YmdHis').rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
    $filethumb = $filename."-thumb.".$fileext;
    $filename = $filename.".".$fileext;

    move_uploaded_file($_FILES[$keyname]['tmp_name'], SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);

    //START by Donny Dennison - 11 august 2022 10:46
    //fix rotated image after resize(thumb)
    // if (in_array($fileext, array("jpg","jpeg"))) {
    //   $this->correctImageOrientation(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
    // }
    //END by Donny Dennison - 11 august 2022 10:46
    //fix rotated image after resize(thumb)

    $this->lib("wideimage/WideImage", 'wideimage', "inc");
    if (file_exists(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb)) {
      unlink(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
    }
    WideImage::load(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)->reSize(370)->saveToFile(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);

    $data['foto_url'] = str_replace("//", "/", $targetdir.'/'.$filename);
    $data['foto_url'] = str_replace("\\", "/", $data['foto_url']);
    $data['foto_url'] = $this->cdn_url($data['foto_url']);

    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }
  //END by Donny Dennison - 1 december 2021 13:55

  //START by Donny Dennison - 1 december 2021 13:55
  //change flow add attachment / photo in api product(add & edit), api free product(add & edit),api automotive product(add & edit), and community(add & edit)
  // public function image_delete($c_produk_id, $c_produk_foto_id)
  // {
  //   $dt = $this->__init();

  //   $data = array();
  //   $data['produk'] = new stdClass();

  //   //check nation_code
  //   $nation_code = $this->input->get('nation_code');
  //   $nation_code = $this->nation_check($nation_code);
  //   if (empty($nation_code)) {
  //     $this->status = 101;
  //     $this->message = 'Missing or invalid nation_code';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }

  //   //check apisess
  //   $apisess = $this->input->get('apisess');
  //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   if (!isset($pelanggan->id)) {
  //     $this->status = 401;
  //     $this->message = 'Missing or invalid API session';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }

  //   $c_produk_id = (int) $c_produk_id;
  //   if ($c_produk_id<=0) {
        // $this->status = 595;
        // $this->message = 'Invalid product ID or Product not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }

  //   $produk = $this->cpm->getById($nation_code, $c_produk_id);
  //   if (!isset($produk->id)) {
        // $this->status = 595;
        // $this->message = 'Invalid product ID or Product not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }

  //   if ($produk->b_user_id_seller != $pelanggan->id) {
  //     $this->status = 907;
  //     $this->message = 'Access denied, you cant change other people product';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }

  //   $c_produk_foto_id = (int) $c_produk_foto_id;
  //   if ($c_produk_foto_id<=0) {
  //     $this->status = 906;
  //     $this->message = 'Invalid ID Product image, please check again';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  //     die();
  //   }

  //   //$this->debug($pelanggan);
  //   //die();
  //   $galeri = $this->cpfm->getByProdukId($nation_code, $produk->id);
  //   $galeri_id_current = 0;
  //   $foto_delete = "";
  //   $thumb_delete = "";
  //   $foto_replace = "media/produk/default.png";
  //   $thumb_replace = "media/produk/default.png";
  //   foreach ($galeri as $gal) {
  //     if ($gal->url == $produk->foto) {
  //       $galeri_id_current = (int) $gal->id;
  //     } else {
  //       $foto_replace = $gal->url;
  //       $thumb_replace = $gal->url_thumb;
  //     }
  //     if ($gal->id == $c_produk_foto_id) {
  //       $foto_delete = $gal->url;
  //       $thumb_delete = $gal->url_thumb;
  //     }
  //   }
  //   $change_default = 0;
  //   if ($galeri_id_current == $c_produk_foto_id) {
  //     $change_default = 1;
  //   }
  //   $res = $this->cpfm->delByIdProdukId($nation_code, $c_produk_foto_id, $c_produk_id);
  //   if ($res) {
  //     if (strlen($thumb_delete)>4) {
  //       $file = SENEROOT.$foto_delete;
  //       if (!is_dir($file) && file_exists($file)) {
  //         unlink($file);
  //       }
  //     }
  //     if (strlen($thumb_delete)>4) {
  //       $file = SENEROOT.$thumb_delete;
  //       if (!is_dir($file) && file_exists($file)) {
  //         unlink($file);
  //       }
  //     }
  //     if (!empty($change_default)) {
  //       $du = array();
  //       $du['foto'] = $foto_replace;
  //       $du['thumb'] = $thumb_replace;
  //       $this->cpm->update($nation_code, $pelanggan->id, $c_produk_id, $du);
  //     }
  //     $this->status = 200;
  //     $this->message = 'Product image successfully deleted';
  //     $data['produk'] = $this->cpm->getById($nation_code, $c_produk_id);
  //     $data['produk']->galeri = $this->cpfm->getByProdukId($nation_code, $c_produk_id);
  //     foreach ($data['produk']->galeri as &$gal) {
  //       if (isset($gal->url)) {
  //         $gal->url = str_replace("//", "/", $gal->url);
  //         $gal->url = $this->cdn_url($gal->url);
  //       }
  //       if (isset($gal->url_thumb)) {
  //         $gal->url_thumb = str_replace("//", "/", $gal->url_thumb);
  //         $gal->url_thumb = $this->cdn_url($gal->url_thumb);
  //       }
  //     }
  //     if (isset($data['produk']->b_kondisi_icon)) {
  //       $data['produk']->b_kondisi_icon = $this->cdn_url($data['produk']->b_kondisi_icon);
  //     }
  //     if (isset($data['produk']->b_berat_icon)) {
  //       $data['produk']->b_berat_icon = $this->cdn_url($data['produk']->b_berat_icon);
  //     }
  //     if (isset($data['produk']->b_user_image_seller)) {

  //       // by Muhammad Sofi - 27 October 2021 10:12
  //       // if user img & banner not exist or empty, change to default image
  //       // $data['produk']->b_user_image_seller = $this->cdn_url($data['produk']->b_user_image_seller);
  //       if(file_exists(SENEROOT.$data['produk']->b_user_image_seller) && $data['produk']->b_user_image_seller != 'media/user/default.png'){
  //         $data['produk']->b_user_image_seller = $this->cdn_url($data['produk']->b_user_image_seller);
  //       } else {
  //         $data['produk']->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
  //       }
  //     }
  //     if (isset($data['produk']->foto)) {
  //       $data['produk']->foto = $this->cdn_url($data['produk']->foto);
  //     }
  //     if (isset($data['produk']->thumb)) {
  //       $data['produk']->thumb = $this->cdn_url($data['produk']->thumb);
  //     }
  //   } else {
  //     $this->status = 979;
  //     $this->message = 'Failed to delete data image product';
  //   }

  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  // }

  public function image_delete()
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
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language
    // check apisess
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

    $foto_url = $this->input->post('foto');
    $file_path = SENEROOT.parse_url($foto_url, PHP_URL_PATH);
    if (file_exists($file_path)) {
      unlink($file_path);
    }
    
    $foto_url_thumb = $this->input->post('foto');
    $file_path = parse_url($foto_url_thumb, PHP_URL_PATH);
    $extension = pathinfo($file_path, PATHINFO_EXTENSION);
    $file_path = substr($file_path,0,strripos($file_path,'.'));
    $file_path = SENEROOT.$file_path.'-thumb.'.$extension;
    if(file_exists($file_path)) {
      unlink($file_path);
    }
      
    $this->status = 200;
    $this->message = 'Success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }
  //END by Donny Dennison - 1 december 2021 13:55

  public function image_default($c_produk_id, $c_produk_foto_id)
  {
    $dt = $this->__init();

    $data = array();
    $data['produk'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $c_produk_id = $c_produk_id;
    if ($c_produk_id<='0') {
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $getProductType = $this->cpm->getProductType($nation_code, $c_produk_id);
    if(!isset($getProductType->product_type)){
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $getProductType = $getProductType->product_type;

    $produk = $this->cpm->getById($nation_code, $c_produk_id, $pelanggan, $getProductType);
    if (!isset($produk->id)) {
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    if ($produk->b_user_id_seller != $pelanggan->id) {
      $this->status = 907;
      $this->message = 'Access denied, you cant change other people product';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $c_produk_foto_id = (int) $c_produk_foto_id;
    if ($c_produk_foto_id<=0) {
      $this->status = 906;
      $this->message = 'Invalid ID Product image, please check again';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //$this->debug($pelanggan);
    //die();
    $galeri = $this->cpfm->getByIdProdukId($nation_code, $produk->id, $c_produk_foto_id);
    if (!isset($galeri->url)) {
      $this->status = 906;
      $this->message = 'Invalid ID Product image, please check again';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }
    $du = array();
    $du['foto'] = $galeri->url;
    $du['thumb'] = $galeri->url_thumb;
    $res = $this->cpm->update($nation_code, $pelanggan->id, $c_produk_id, $du);
    if ($res) {
      $this->status = 200;
      $this->message = 'Succcess';
      $data['produk'] = $this->cpm->getById($nation_code, $c_produk_id, $pelanggan, $getProductType, $pelanggan->id);

      $data['produk']->galeri = $this->cpfm->getByProdukId($nation_code, $c_produk_id);
      foreach ($data['produk']->galeri as &$gal) {
        if (isset($gal->url)) {
          $gal->url = str_replace("//", "/", $gal->url);
          $gal->url = $this->cdn_url($gal->url);
        }
        if (isset($gal->url_thumb)) {
          $gal->url_thumb = str_replace("//", "/", $gal->url_thumb);
          $gal->url_thumb = $this->cdn_url($gal->url_thumb);
        }
      }

      $data['produk']->galeri_video = $this->cpfm->getByProdukId($nation_code, $c_produk_id, "video");
      foreach ($data['produk']->galeri_video as &$gal_vid) {
        if (isset($gal_vid->url)) {
          $gal_vid->url = str_replace("//", "/", $gal_vid->url);
          $gal_vid->url = $this->cdn_url($gal_vid->url);
        }
        if (isset($gal_vid->url_thumb)) {
          $gal_vid->url_thumb = str_replace("//", "/", $gal_vid->url_thumb);
          $gal_vid->url_thumb = $this->cdn_url($gal_vid->url_thumb);
        }
      }

      if (isset($data['produk']->b_kondisi_icon)) {
        $data['produk']->b_kondisi_icon = $this->cdn_url($data['produk']->b_kondisi_icon);
      }
      if (isset($data['produk']->b_berat_icon)) {
        $data['produk']->b_berat_icon = $this->cdn_url($data['produk']->b_berat_icon);
      }
      if (isset($data['produk']->b_user_image_seller)) {

        // by Muhammad Sofi - 27 October 2021 10:12
        // if user img & banner not exist or empty, change to default image
        // $data['produk']->b_user_image_seller = $this->cdn_url($data['produk']->b_user_image_seller);
        if(file_exists(SENEROOT.$data['produk']->b_user_image_seller) && $data['produk']->b_user_image_seller != 'media/user/default.png'){
          $data['produk']->b_user_image_seller = $this->cdn_url($data['produk']->b_user_image_seller);
        } else {
          $data['produk']->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        }
      }
      if (isset($data['produk']->foto)) {
        $data['produk']->foto = $this->cdn_url($data['produk']->foto);
      }
      if (isset($data['produk']->thumb)) {
        $data['produk']->thumb = $this->cdn_url($data['produk']->thumb);
      }
    } else {
      $this->status = 960;
      $this->message = 'Failed to change a default image';
    }
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }

  public function video_add()
  {
    $dt = $this->__init();
    $keyname = 'video';

    $data = array();
    $data['video_url'] = '';
    $data['video_thumb_url'] = '';

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
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

    $product_id = $this->input->post('product_id');
    if(empty($product_id)){
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $getProductType = $this->cpm->getProductType($nation_code, $product_id);
    if(!isset($getProductType->product_type)){
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $getProductType = $getProductType->product_type;

    $produk = $this->cpm->getById($nation_code, $product_id, $pelanggan, $getProductType, $pelanggan->language_id);
    if (!isset($produk->id)) {
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    if (!isset($_FILES[$keyname])) {
      $this->status = 1300;
      $this->message = 'Upload failed';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    if ($_FILES[$keyname]['size']<=0) {
      $this->status = 1300;
      $this->message = 'Upload failed';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    if ($_FILES[$keyname]['size'] > 104857600) {
      $this->status = 1308;
      $this->message = 'Video file Size too big, max size 100 MB';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    if (mime_content_type($_FILES[$keyname]['tmp_name'])=="image/webp") {
      $this->status = 1303;
      $this->message = 'WebP image file format is not supported.';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $filenames = pathinfo($_FILES[$keyname]['name']);
    $fileext = '';
    if (isset($filenames['extension'])) {
      $fileext = strtolower($filenames['extension']);
    }

    $this->seme_log->write("api_mobile", 'product_id '.$product_id);

    $this->seme_log->write("api_mobile", 'extension '.$fileext);

    // if (!in_array($fileext, array("mp4"))) {
    //   $this->status = 1305;
    //   $this->message = 'Invalid file extension, please try other file';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    //   die();
    // }

    $targetdir = $this->media_produk_video;
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

    $cpfm = $this->cpfm->getByProdukIdJenisConvertStatus($nation_code, $product_id, "video", "uploading");
    if (!isset($cpfm->id)) {
      $this->status = 1307;
      $this->message = 'There is no reserve attachment for video';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $filename = "$nation_code-$product_id-$cpfm->id-".date('YmdHis');
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
        $dix['convert_status'] = "waiting";
        $dix['url'] = $data['video_url'];
        // $dix['url_thumb'] = $data['video_thumb_url'];
        $this->cpfm->update($nation_code, $product_id, $cpfm->id, "video", $dix);

        $data['video_url'] = $this->cdn_url($data['video_url']);
        // $data['video_thumb_url'] = $this->cdn_url($data['video_thumb_url']);
        $data['video_thumb_url'] = $this->cdn_url($cpfm->url_thumb);

        if($cpfm->url != "media/produk_video/default.png"){
          $file_path = SENEROOT.$cpfm->url;
          if (file_exists($file_path)) {
            unlink($file_path);
          }
        }

        //START by Donny Dennison - 25 july 2022 11:40
        //change point get rule for group chat community and upload video product
        $totalVideo = $this->cpfm->countByProdukIdJenisConvertStatusNotEqual($nation_code, $product_id, "video", "uploading");
        if($totalVideo == 1){
          $ownerProduct = $this->bu->getById($nation_code, $produk->b_user_id_seller);

          //START by Donny Dennison 12 December 2022 - 15:24
          //Set daily limit to Video registration : 10 to Community posts and 15 to products
          //get limit left
          $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $ownerProduct->id, "E8");
          if(!isset($limitLeft->limit_plus)){
            $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E8");

            $lastID = $this->glplm->getLastId($nation_code, date("Y-m-d"));
            $du = array();
            $du['nation_code'] = $nation_code;
            $du['id'] = $lastID;
            $du['cdate'] = date("Y-m-d");
            $du['b_user_id'] = $ownerProduct->id;
            $du['code'] = "E8";
            $du['limit_plus'] = $pointGet->remark;
            $du['limit_minus'] = $pointGet->remark;
            $this->glplm->set($du);

            //get limit left
            $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $ownerProduct->id, "E8");
          }

          if($limitLeft->limit_plus > 0){
            //get point
            $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EO");
            if (!isset($pointGet->remark)) {
                $pointGet = new stdClass();
                $pointGet->remark = 10;
            }

            $di = array();
            $di['nation_code'] = $nation_code;
            $di['b_user_alamat_location_kelurahan'] = $produk->kelurahan;
            $di['b_user_alamat_location_kecamatan'] = $produk->kecamatan;
            $di['b_user_alamat_location_kabkota'] = $produk->kabkota;
            $di['b_user_alamat_location_provinsi'] = $produk->provinsi;
            $di['b_user_id'] = $ownerProduct->id;
            $di['point'] = $pointGet->remark;
            $di['custom_id'] = $product_id;
            $di['custom_type'] = 'product';
            $di['custom_type_sub'] = 'upload video';
            $di['custom_text'] = $ownerProduct->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
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
            // $this->glrm->updateTotal($nation_code, $ownerProduct->id, 'total_point', '+', $di['point']);
            $this->glplm->updateTotal($nation_code, date("Y-m-d"), $ownerProduct->id, 'E8', 'limit_plus', '-', 1);
          }
          //END by Donny Dennison 12 December 2022 - 15:24
          //Set daily limit to Video registration : 10 to Community posts and 15 to products
        }
        //END by Donny Dennison - 25 july 2022 11:40
        //change point get rule for group chat community and upload video product
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
            $this->cpfm->update($nation_code, $product_id, $cpfm->id, "video", $dix);

            $this->seme_log->write("api_mobile", 'tmp url moved');
          }
        }else{
          $this->seme_log->write("api_mobile", 'tmp url gone');
        }

        $this->status = 1306;
        $this->message = 'move upload file failed';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
        die();
      }

      $this->status = 200;
      $this->message = 'Success';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    // }else{
    //   $this->status = 1300;
    //   $this->message = 'Upload failed';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
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
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
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

    $product_id = $this->input->post('product_id');

    $getProductType = $this->cpm->getProductType($nation_code, $product_id);
    if(!isset($getProductType->product_type)){
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $getProductType = $getProductType->product_type;

    $produk = $this->cpm->getById($nation_code, $product_id, $pelanggan, $getProductType, $pelanggan->language_id);
    if (!isset($produk->id)) {
      // $this->status = 595;
      // $this->message = 'Invalid product ID or Product not found';
      $this->status = 200;
      $this->message = 'Success';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $cpfm_id = $this->input->post('cpfm_id');
    $cpfm = $this->cpfm->getByIdProdukId($nation_code, $product_id, $cpfm_id, "video");
    if (!isset($cpfm->id)) {
      $this->status = 200;
      $this->message = 'Success';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    if($cpfm->url != "media/produk_video/default.png"){
      $file_path = SENEROOT.$cpfm->url;
      if (file_exists($file_path)) {
        unlink($file_path);
      }
    }

    if($cpfm->url_thumb != "media/produk_video/default.png"){
      $file_path = SENEROOT.$cpfm->url_thumb;
      if (file_exists($file_path)) {
        unlink($file_path);
      }
    }

    $this->cpfm->delByIdProdukId($nation_code, $cpfm_id, $product_id, "video");

    //START by Donny Dennison - 25 july 2022 11:40
    //change point get rule for group chat community and upload video product
    $totalVideo = $this->cpfm->countByProdukIdJenisConvertStatusNotEqual($nation_code, $product_id, "video", "uploading");
    if($totalVideo == 0){
      $ownerProduct = $this->bu->getById($nation_code, $produk->b_user_id_seller);

      //START by Donny Dennison - 12 December 2022 15:24
      //Set daily limit to Video registration : 10 to Community posts and 15 to products
      //get limit left
      $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $ownerProduct->id, "E8");
      if(!isset($limitLeft->limit_minus)){
        $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E8");

        $lastID = $this->glplm->getLastId($nation_code, date("Y-m-d"));
        $du = array();
        $du['nation_code'] = $nation_code;
        $du['id'] = $lastID;
        $du['cdate'] = date("Y-m-d");
        $du['b_user_id'] = $ownerProduct->id;
        $du['code'] = "E8";
        $du['limit_plus'] = $pointGet->remark;
        $du['limit_minus'] = $pointGet->remark;
        $this->glplm->set($du);

        //get limit left
        $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $ownerProduct->id, "E8");
      }

      if($limitLeft->limit_minus > 0){
        //get point
        $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EO");
        if (!isset($pointGet->remark)) {
          $pointGet = new stdClass();
          $pointGet->remark = 10;
        }

        $di = array();
        $di['nation_code'] = $nation_code;
        $di['b_user_alamat_location_kelurahan'] = $produk->kelurahan;
        $di['b_user_alamat_location_kecamatan'] = $produk->kecamatan;
        $di['b_user_alamat_location_kabkota'] = $produk->kabkota;
        $di['b_user_alamat_location_provinsi'] = $produk->provinsi;
        $di['b_user_id'] = $ownerProduct->id;
        $di['plusorminus'] = "-";
        $di['point'] = $pointGet->remark;
        $di['custom_id'] = $product_id;
        $di['custom_type'] = 'product';
        $di['custom_type_sub'] = 'upload video';
        $di['custom_text'] = $ownerProduct->fnama.' has delete '.$di['custom_type_sub'].' '.$di['custom_type'].' and lose '.$di['point'].' point(s)';
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
        // $this->glrm->updateTotal($nation_code, $ownerProduct->id, 'total_point', '-', $di['point']);
        $this->glplm->updateTotal($nation_code, date("Y-m-d"), $ownerProduct->id, 'E8', 'limit_minus', '-', 1);
        $this->glplm->updateTotal($nation_code, date("Y-m-d"), $ownerProduct->id, 'E8', 'limit_plus', '+', 1);
      }
      //END by Donny Dennison - 12 December 2022 15:24
      //Set daily limit to Video registration : 10 to Community posts and 15 to products
    }
    //END by Donny Dennison - 25 july 2022 11:40
    //change point get rule for group chat community and upload video product

    $this->status = 200;
    $this->message = 'Success';

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }

  public function shipment_check()
  {
    $data = array();
    $data['courier_services'] = array();
    $this->status = 200;
    $this->message = 'success';

    //populating input
    $berat = (float) $this->input->post('berat');
    $panjang = (float) $this->input->post('dimension_long');
    $lebar = (float) $this->input->post('dimension_width');
    $tinggi = (float) $this->input->post('dimension_height');
    $direct_delivery = (int) $this->input->post('direct_delivery');
    $this->seme_log->write("api_mobile", "Produk::shipment_check() -> POST: ".json_encode($_POST));

    $sc = $this->__shipment_check($berat, $panjang, $lebar, $tinggi, $direct_delivery);
    $data['courier_services'] = $sc->courier_services;

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }
  public function hapus_banyak()
  {
    $dt = $this->__init();

    $data = array();
    $data['produk'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //init
    $pids = array();

    //collect input
    $c_produk_ids = $this->input->post("c_produk_ids");
    if (empty($c_produk_ids)) {
      $c_produk_ids = $this->input->post("c_produk_id");
    }
    $pos = strpos($c_produk_ids, ',');
    if ($pos === false) {
      $c_produk_ids = (int) $c_produk_ids;
      if ($c_produk_ids>0) {
        $pids[] = $c_produk_ids;
      }
      unset($c_produk_ids); //freed up some memory
    } else {
      $temp = explode(",", $c_produk_ids);
      foreach ($temp as $t) {
        if (!empty($t)) {
          $pids[] = $t;
        }
      }
      unset($t); //freed up some memory
      unset($temp); //freed up some memory
    }
    if (count($pids)<0) {
      $this->status = 963;
      $this->message = 'Please input at least one id product';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }
    //start transaction
    $this->cpm->trans_start();

    //get product by seller
    $products = $this->cpm->getActiveByUserIdAndIds($nation_code, $pelanggan->id, $pids);
    if (count($products)<=0) {
      $this->status = 964;
      $this->message = 'Produk ID(s) not found or not belong to you';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }
    //get your PIDS
    $pids = array();
    foreach ($products as $product) {
      $pids[] = $product->id;
    }

    //get product images
    $images = $this->cpfm->getByProdukIds($nation_code, $pids);

    //check delete method
    if ($this->is_soft_delete) {
      $du = array();
      $du['foto'] = "media/produk/default.png";
      $du['thumb'] = "media/produk/default.png";
      $du['is_published'] = 0;
      $du['is_visible'] = 0;
      $du['is_active'] = 0;
      $res = $this->cpm->updateMass($nation_code, $pelanggan->id, $pids, $du);
      if ($res) {
        $this->cpm->trans_commit();
        $this->status = 200;
        $this->message = 'Success';

        //deleting product images
        $this->cpfm->delByProdukIds($nation_code, $pids);
        $this->cpm->trans_commit();

        //delete image files
        if (count($images)) {
          foreach ($images as $img) {
            $fileloc = SENEROOT.$img->url;
            if (file_exists($fileloc)) {
              unlink($fileloc);
            }
            $fileloc = SENEROOT.$img->url_thumb;
            if (file_exists($fileloc)) {
              unlink($fileloc);
            }
          }
        }

        //remove from cart
        $this->cart->delAllByProdukIds($nation_code, $pids);
        $this->cpm->trans_commit();

        //remove from wishlist
        $this->dwlm->delAllByProdukIds($nation_code, $pids);
        $this->cpm->trans_commit();
      } else {
        $this->cpm->trans_rollback();
        $this->status = 965;
        $this->message = 'Deleting data failed, please try again';
      }
    } else {
      //delete
      $res = $this->cpm->deleteMass($nation_code, $pelanggan->id, $pids);
      if ($res) {
        $this->cpm->trans_commit();
        $this->status = 200;
        $this->message = 'Success';

        //deleting product images
        $this->cpfm->delByProdukIds($nation_code, $pids);
        $this->cpm->trans_commit();

        //delete image files
        if (count($images)) {
          foreach ($images as $img) {
            $fileloc = SENEROOT.$img->url;
            if (file_exists($fileloc)) {
              unlink($fileloc);
            }
            $fileloc = SENEROOT.$img->url_thumb;
            if (file_exists($fileloc)) {
              unlink($fileloc);
            }
          }
        }

        //remove from cart
        $this->cart->delAllByProdukIds($nation_code, $pids);
        $this->cpm->trans_commit();

        //remove from wishlist
        $this->dwlm->delAllByProdukIds($nation_code, $pids);
        $this->cpm->trans_commit();
      } else {
        $this->cpm->trans_rollback();
        $this->status = 965;
        $this->message = 'Deleting data failed, please try again';
      }
    }

    //finish transaction
    $this->cpm->trans_end();

    //render output
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }

  public function edit()
  {
    //init
    $dt = $this->__init();

    //default response
    $data = array();
    $data['produk'] = new stdClass();
    $data['can_input_referral'] = '0';

    $this->seme_log->write("api_mobile", "Produk::edit -> ".json_encode($_POST));

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //START by Donny Dennison - 10 november 2022 14:34
    //new feature, join/input referral
    $limit = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E7");
    if (!isset($limit->remark)) {
      $limit = new stdClass();
      $limit->remark = 5;
    }

    if($pelanggan->b_user_id_recruiter == '0' && date("Y-m-d", strtotime($pelanggan->cdate." +".$limit->remark." days")) > date("Y-m-d")){
        $data['can_input_referral'] = '1';
    }
    //END by Donny Dennison - 10 november 2022 14:34
    //new feature, join/input referral

    $c_produk_id = $this->input->post("c_produk_id");
    if ($c_produk_id<='0') {
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $getProductType = $this->cpm->getProductType($nation_code, $c_produk_id);
    if(!isset($getProductType->product_type)){
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $getProductType = $getProductType->product_type;

    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language
    // $produk = $this->cpm->getById($nation_code, $c_produk_id);
    $produk = $this->cpm->getById($nation_code, $c_produk_id, $pelanggan, $getProductType, $pelanggan->language_id);

    if (!isset($produk->id)) {
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    if ($produk->b_user_id_seller != $pelanggan->id) {
      $this->status = 907;
      $this->message = 'Access denied, you cant change other people product';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //START by Donny Dennison - 12 july 2022 14:56
    //new offer system
    $listOffering = $this->ecrm->getAll($nation_code, 'offer', $c_produk_id, "offering");
    if($listOffering){
      $this->status = 920;
      $this->message = 'Cannot delete because there is offer';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();

    }

    $listOffering = $this->ecrm->getAll($nation_code, 'offer', $c_produk_id, "accepted");
    if($listOffering){
      $this->status = 921;
      $this->message = 'Cannot delete because havent completed the review';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();

    }

    $listOffering = $this->ecrm->getAll($nation_code, 'offer', $c_produk_id, "waiting review from seller");
    if($listOffering){
      $this->status = 921;
      $this->message = 'Cannot delete because havent completed the review';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();

    }

    $listOffering = $this->ecrm->getAll($nation_code, 'offer', $c_produk_id, "waiting review from buyer");
    if($listOffering){
      $this->status = 921;
      $this->message = 'Cannot delete because havent completed the review';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();

    }
    //END by Donny Dennison - 12 july 2022 14:56

    //sanitize post
    foreach ($_POST as $key=>&$val) {
      if (is_string($val)) {
        if ($key == 'deskripsi') {
          $val = $this->seme_purifier->richtext($val);
        } else {
          $val = $this->__f($val);
        }
      }
    }

    //populating input
    $b_user_alamat_id = (int) $this->input->post("b_user_alamat_id");
    $b_kategori_id = (int) $this->input->post("b_kategori_id");
    // $b_berat_id = (int) $this->input->post("b_berat_id");
    $b_kondisi_id = (int) $this->input->post("b_kondisi_id");
    $brand =  $this->input->post("brand");
    $nama =  $this->input->post("nama");
    $harga_jual = $this->input->post("harga_jual");
    $deskripsi_singkat = $this->input->post('deskripsi_singkat');
    $deskripsi = $this->input->post("deskripsi");
    // $dimension_long = $this->input->post("dimension_long");
    // $dimension_width = $this->input->post("dimension_width");
    // $dimension_height = $this->input->post("dimension_height");
    // $vehicle_types = $this->input->post("vehicle_types");
    // $courier_services = $this->input->post("courier_services");
    // $services_duration = $this->input->post("services_duration");
    // $berat = $this->input->post("berat");
    // $satuan = $this->input->post("satuan");
    $stok = (int) $this->input->post("stok");
    // $is_include_delivery_cost = (int) $this->input->post("is_include_delivery_cost");
    $is_published = (int) $this->input->post("is_published");
    $model = $this->input->post("model");
    $color = $this->input->post("color");
    $year = $this->input->post("year");

    //by Donny Dennison - 16 december 2020 14:09
    //add new product type(meetup)
    $product_type = $this->input->post("product_type");

    //by Donny Dennison - 19 january 2022 10:35
    //merge table free product to table product
    $telp = $this->input->post("telp");

    //validation
    // if ($b_kategori_id<=0) {
    //   $b_kategori_id = 0;
    // }
    // if ($b_berat_id<=0) {
    //   $b_berat_id = 0;
    // }
    if ($b_kondisi_id<=0) {
      $b_kondisi_id = 0;
    }
    if ($harga_jual<=0) {
      $harga_jual = 0;
    }
    if (empty($nama)) {
      $nama = "";
    }
    if (empty($deskripsi)) {
      $deskripsi = "";
    }
    // $is_include_delivery_cost = !empty($is_include_delivery_cost) ? 1:0;
    $is_published = !empty($is_published) ? 1:0;
    // if (strtolower($services_duration) == 'sameday' || strtolower($services_duration) == 'same day') {
    //   $services_duration = 'Same Day';
    // }
    // if (strtolower($services_duration) == 'nextday' || strtolower($services_duration) == 'next day') {
    //   $services_duration = 'Next Day';
    // }
    // if(strtolower($courier_services) == 'qxpress' && !empty($is_include_delivery_cost)){
    //   $is_include_delivery_cost = 0;
    // }

    //by Donny Dennison - 24 september 2021 15:31
    //simplify-data-inserted-and-edit-for-product-meetup-type
    //by Donny Dennison - 3 june 2022 13:10
    //new feature, product type santa
    // if($product_type == 'MeetUp' || $product_type == 'Free' || $product_type == 'Automotive'){
    if($product_type == 'MeetUp' || $product_type == 'Free' || $product_type == 'Automotive' || $product_type == 'Santa'){
      $b_user_alamat_id = $produk->b_user_alamat_id;
      $b_kondisi_id = 4;
      // $berat = 1.0;
      // $stok = 1;
      // $dimension_long = 1;
      // $dimension_width = 1;
      // $dimension_height = 1;

      //by Donny Dennison - 19 january 2022 10:35
      //merge table free product to table product
      // if($product_type == 'Free' && strlen($telp) <= 0){
      //   $this->status = 1102;
      //   $this->message = 'Please input phone number';
      //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      //   die();
      // }

      if($product_type == 'Free') {
        $harga_jual = 0;
        $b_kategori_id = 1;
      }

      if($product_type == "Automotive"){

        if ($b_kategori_id == 'car') {
          $b_kategori_id = 32;
        }else if($b_kategori_id == 'motorcycle'){
          $b_kategori_id = 33;
        }

      }

      //by Donny Dennison - 3 june 2022 13:10
      //new feature, product type santa
      if($product_type == 'Santa') {
        $nama = "Santa";
        $harga_jual = 0;
        $b_kategori_id = 0;
      }

    }

    //b_user_alamat_id
    if ($b_user_alamat_id<=0) {
      $this->status = 1098;
      $this->message = 'Invalid b_user_alamat_id';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $almt = $this->bua->getByIdUserId($nation_code, $pelanggan->id, $b_user_alamat_id);
    if (!isset($almt->id)) {
      $this->status = 916;
      $this->message = 'Please choose pickup address';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $kat = $this->bkm3->getById($nation_code, $b_kategori_id);
    //by Donny Dennison - 3 june 2022 13:10
    //new feature, product type santa
    // if (!isset($kat->id)) {
    if (!isset($kat->id) && $product_type != 'Santa') {
      $this->status = 917;
      $this->message = 'Please choose product category';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $kon = $this->bkon->getById($nation_code, $b_kondisi_id);
    if (!isset($kon->id)) {
      $this->status = 919;
      $this->message = 'Please choose product condition';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    if (strlen($nama)<=0) {
      $this->status = 910;
      $this->message = 'Product name is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //by Donny Dennison - 24 september 2021 15:31
    //simplify-data-inserted-and-edit-for-product-meetup-type
    // if ($harga_jual<=0) {
    //by Donny Dennison - 3 june 2022 13:10
    //new feature, product type santa
    // if ($harga_jual<=0 && $product_type != 'Free') {
    if ($harga_jual<=0 && $product_type != 'Free' && $product_type != 'Santa') {
      $this->status = 911;
      $this->message = 'Price is required';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    // if ($berat<=0) {
    //   $this->status = 912;
    //   $this->message = 'Please specify weight correctly';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    //   die();
    // }
    // //recasting weight
    // $berat = $this->__floatWeight($berat);

    // if ($dimension_long<=0) {
    //   $this->status = 913;
    //   $this->message = 'Invalid product long correctly';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    //   die();
    // }

    // if ($dimension_height<=0) {
    //   $this->status = 914;
    //   $this->message = 'Invalid product height correctly';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    //   die();
    // }

    // if ($dimension_width<=0) {
    //   $this->status = 915;
    //   $this->message = 'Invalid product width correctly';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    //   die();
    // }

    //by Donny Dennison - 13-07-2020 16:08
    //edit stok allow 0 and if edit stok more than 0 then change cdate to newest
    // if ($stok<=0) {
    //   $this->status = 916;
    //   $this->message = 'Please input atleast one product stock quantity';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    //   die();
    // }

    // //check dimension
    // $dimension_max = $this->__getDimensionMax($dimension_long, $dimension_width, $dimension_height);
    // if ($dimension_max>724) {
    //   $this->status = 918;
    //   $this->message = 'Product too big, we currently unsupported product with dimension above 7,2 m';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    //   die();
    // }

    //by Donny Dennison 24 february 2021 18:45
    //change ’ to ' in add & edit product name and description
    $nama = str_replace('’',"'",$nama);
    $deskripsi = str_replace('’',"'",$deskripsi);

    // //by Donny Dennison 16 augustus 2020 00:25
    // //fix check emoji in insert & edit product and discussion
    // if( preg_match( $this->unicodeRegexp, $nama ) ){

    //   $this->status = 1104;
    //   $this->message = 'Symbol image(ex.Emoji..) is not allowed in Product Name or Description';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    //   die();

    // }else if( preg_match( $this->unicodeRegexp, $deskripsi ) ){

    //   $this->status = 1104;
    //   $this->message = 'Symbol image(ex.Emoji..) is not allowed in Product Name or Description';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    //   die();

    // }

    //sanitize input
    // $nama = filter_var($nama, FILTER_SANITIZE_STRING);
    // $brand = filter_var($brand, FILTER_SANITIZE_STRING);
    // $deskripsi_singkat = filter_var($deskripsi_singkat, FILTER_SANITIZE_STRING);
    // $deskripsi = filter_var($deskripsi, FILTER_SANITIZE_STRING);
    $deskripsi = nl2br($deskripsi);

    //by Donny Dennison - 15 augustus 2020 15:09
    //bug fix \n (enter) didnt get remove
    $deskripsi = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $deskripsi);
    
    $deskripsi = str_replace("\\n", "<br />", $deskripsi);

    //check shipments
    // $shipments_check = $this->__shipment_check($berat, $dimension_long, $dimension_width, $dimension_height);

    //start transaction
    $this->cpm->trans_start();

    //updating to database
    $du = array();
    $du['b_user_alamat_id'] = $b_user_alamat_id;
    $du['b_kategori_id'] = $b_kategori_id;
    // $du['b_berat_id'] = $b_berat_id;
    $du['b_kondisi_id'] = $b_kondisi_id;
    $du['brand'] = $brand;
    $du['nama'] = $nama;
    $du['harga_jual'] = $harga_jual;
    $du['deskripsi'] = $deskripsi;
    // $du['berat'] = $berat;
    // $du['satuan'] = $satuan;
    $du['stok'] = $stok;
    // $du['dimension_width'] = $dimension_width;
    // $du['dimension_height'] = $dimension_height;
    // $du['dimension_long'] = $dimension_long;
    // $du['courier_services'] = $courier_services;
    // $du['vehicle_types'] = $vehicle_types;
    // $du['services_duration'] = $services_duration;
    // $du['is_include_delivery_cost'] = $is_include_delivery_cost;
    $du['is_published'] = $is_published;

    //by Donny Dennison - 16 december 2020 14:09
    //add new product type(meetup)
    $du['product_type'] = $product_type;

    //by Donny Dennison - 13-07-2020 16:08
    //edit stok allow 0 and if edit stok more than 0 then change cdate to newest
    if($produk->stok != $stok && $stok > 0){
        $du['cdate'] = 'NOW()'; 
    }

    //by Donny Dennison - 28 july 2020 10:25
    //if edit price then change cdate to newest
    if($produk->harga_jual != number_format($harga_jual, 2, '.', '') && $harga_jual > 0){
        $du['cdate'] = 'NOW()'; 
    }

    //by Donny Dennison - 1 march 2021 14:47
    //add need action column in dashboard
    if($is_published == 1){
      $du['reported_status'] = '';
      if($product_type != 'Free' && $product_type != 'Santa' && $produk->is_published == 0){
        $du['check_wanted'] = '0';
      }
    }

    $du['alamat2'] = $almt->alamat2;
    $du['kelurahan'] = $almt->kelurahan;
    $du['kecamatan'] = $almt->kecamatan;
    $du['kabkota'] = $almt->kabkota;
    $du['provinsi'] = $almt->provinsi;
    $du['kodepos'] = $almt->kodepos;
    $du['latitude'] = $almt->latitude;
    $du['longitude'] = $almt->longitude;

    //by Donny Dennison - 19 january 2022 10:35
    //merge table free product to table product
    if($product_type == 'Free'){
      $du['start_date'] = 'NOW()';
      $du['end_date'] = date("Y-m-d", strtotime("+".$this->produk_gratis_limit_hari." day"));
    }

    $res = $this->cpm->update($nation_code, $pelanggan->id, $c_produk_id, $du);
    if ($res) {
      $this->cpm->trans_commit();

      if($product_type == 'Automotive'){
        //start transaction automotive detail
        $du = array();
        $du['model'] = $model;
        $du['color'] = $color;
        $du['year'] = $year;

        $res = $this->cpdam->update($nation_code, $c_produk_id, $du);
        if(!$res){

          $this->cpm->trans_rollback();
          $this->cpm->trans_end();
          $this->status = 990;
          $this->message = 'Cant edit product from database, please try again later';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");

        }
        $this->cpm->trans_commit();

      }

      //START by Donny Dennison - 12 july 2022 14:56
      //new offer system
      $product_edited_for_offer_chat = 0;

      if($produk->nama != $nama || $produk->harga_jual != $harga_jual || $produk->deskripsi != $deskripsi){

        $product_edited_for_offer_chat = 1;

      }
      //END by Donny Dennison - 12 july 2022 14:56
      //new offer system

      $this->status = 200;
      // $this->message = 'Product edited successfully';
      $this->message = 'Success';

      $listUrl = array();
      $listUpload = array();

      $totalFoto = 0;
      $checkFileExist = 1;
      //looping for get list of url
      for ($i=1; $i < 6; $i++) {

        $file_path = parse_url($this->input->post('foto'.$i), PHP_URL_PATH);

        if (strpos($file_path, 'temporary') !== false) {

          $listUpload = array_merge($listUpload, array($file_path));
          $totalFoto++;

          if (!file_exists(SENEROOT.$file_path)) {
            $checkFileExist = 0;
          }

        }else if($this->input->post('foto'.$i) != null){

          $listUrl = array_merge($listUrl, array(substr($file_path, 1)));
          $totalFoto++;
        
        }
      
      }

      if ($product_type == 'Santa') {

        if ($totalFoto < 1) {
          $this->status = 1300;
          $this->message = 'Upload failed';
          $this->cpm->trans_rollback();
          $this->cpm->trans_end();
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
          die();
        }

      }else{

        if ($totalFoto < 3) {
          $this->status = 1300;
          $this->message = 'Upload failed';
          $this->cpm->trans_rollback();
          $this->cpm->trans_end();
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
          die();
        }

      }

      if ($checkFileExist == 0) {
        $this->status = 995;
        $this->message = 'Failed upload, temporary already gone';
        $this->cpm->trans_rollback();
        $this->cpm->trans_end();
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
        die();
      }

      // $this->seme_log->write("api_mobile", "isi list url ". json_encode($listUrl));
      // $this->seme_log->write("api_mobile", "isi list upload ". json_encode($listUpload));

      //delete image that is not in array
      $galeri = $this->cpfm->getByProdukId($nation_code, $c_produk_id);
      foreach ($galeri as $gal) {

        if (!in_array($gal->url, $listUrl) || empty($listUrl)) {

          $this->cpfm->delByIdProdukId($nation_code, $gal->id, $c_produk_id);
          $this->cpm->trans_commit();
          
          if (strlen($gal->url)>4) {
            $file = SENEROOT.$gal->url;
            if (!is_dir($file) && file_exists($file)) {
              unlink($file);
            }
          }

          if (strlen($gal->url_thumb)>4) {
            $file = SENEROOT.$gal->url_thumb;
            if (!is_dir($file) && file_exists($file)) {
              unlink($file);
            }
          }

          //START by Donny Dennison - 12 july 2022 14:56
          //new offer system
          $product_edited_for_offer_chat = 1;

        }

      }

      if(!empty($listUpload)){
      
        //upload image and insert to c_product_foto table
        foreach ($listUpload as $key => $upload) {
          
          $photoId_last = $this->cpfm->getLastByProdukId($nation_code,$c_produk_id);

          if(!isset($photoId_last->id)){
            $photoId_last->id = 1;
          }else{
            $photoId_last->id += 1;

          }

          // $sc = $this->__uploadImagex($nation_code, $upload, $c_produk_id, $photoId_last->id);
          $sc = $this->__moveImagex($nation_code, $upload, $c_produk_id, $photoId_last->id);
          
          if (isset($sc->status)) {
            if ($sc->status==200) {
              $this->cpm->trans_commit();
                
              $dix = array();
              $dix['nation_code'] = $nation_code;
              $dix['c_produk_id'] = $c_produk_id;
              $dix['id'] = $photoId_last->id;
              $dix['url'] = $sc->image;
              $dix['url_thumb'] = $sc->thumb;
              $dix['is_active'] = 1;
              $dix['caption'] = '';
              $this->cpfm->set($dix);
              $this->cpm->trans_commit();

              //START by Donny Dennison - 12 july 2022 14:56
              //new offer system
              $product_edited_for_offer_chat = 1;

            }
          }

        }

      }

      //update cover image
      $i=0;
      $galeri = $this->cpfm->getByProdukId($nation_code, $c_produk_id);
      foreach ($galeri as &$gal) {
        if ($i==0) {
          if (isset($gal->url)) {
            if (strlen($gal->url)>4) {
              $this->cpm->update($nation_code, $pelanggan->id, $c_produk_id, array("foto"=>$gal->url,"thumb"=>$gal->url_thumb));
              $this->cpm->trans_commit();
              if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Produk::edit --updateProductImageCover $gal->url DONE");
              }
              $i++;
            }
          }
        }else{
          break;
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
          
          $cpfm_last = $this->cpfm->getLastId($nation_code,$c_produk_id, "video");
          
          $upi = $this->__moveImagex($nation_code, $this->input->post("video".$i."_thumb"), $c_produk_id, $cpfm_last);
          
          $dix = array();
          $dix['nation_code'] = $nation_code;
          $dix['c_produk_id'] = $c_produk_id;
          $dix['id'] = $cpfm_last;
          $dix['jenis'] = 'video';
          $dix['convert_status'] = 'uploading';
          if($upi->status == 200){
            $dix['url'] = $upi->image;
            $dix['url_thumb'] = $upi->thumb;
          }else{
            $dix['url'] = "media/produk_video/default.png";
            $dix['url_thumb'] = "media/produk_video/default.png";
          }
          $dix['caption'] = '';
          $this->cpfm->set($dix);
          $this->cpm->trans_commit();

        }

      }

      //START by Donny Dennison - 12 july 2022 14:56
      //new offer system
      if($product_edited_for_offer_chat == 1){

        $checkExistingOffer = $this->ecrm->countAll($nation_code, "offer", $c_produk_id, array(0 => "cancelled", 1 => "rejected", 2 => "reviewed"));

        if($checkExistingOffer > 0){

          $du = array();
          // $du['product_edited'] = 1;

          //directory structure
          // $thn = date("Y");
          // $bln = date("m");
          $ds = DIRECTORY_SEPARATOR;
          $target = $this->media_chat;
          if (!realpath($target)) {
              mkdir($target, 0775);
          }
          // $target = $this->media_chat.$ds.$thn;
          // if (!realpath($target)) {
          //     mkdir($target, 0775);
          // }
          // $target = $this->media_chat.$ds.$thn.$ds.$bln;
          // if (!realpath($target)) {
          //     mkdir($target, 0775);
          // }

          $ext = pathinfo(SENEROOT.$galeri[0]->url_thumb, PATHINFO_EXTENSION);
          $filename = $c_produk_id.'.'.$ext;

          if (file_exists(SENEROOT.$galeri[0]->url_thumb) && is_file(SENEROOT.$galeri[0]->url_thumb)) {
              copy(SENEROOT.$galeri[0]->url_thumb, SENEROOT.$ds.$target.$ds.$filename);
          }

          $url = $this->media_chat.'/'.$filename;
          $url = str_replace("//", "/", $url);

          $du['custom_name_1'] = $nama;
          $du['c_produk_nama'] = $nama;
          $du['c_produk_harga_jual'] = $harga_jual;
          $du['c_produk_thumb'] = $url;

          $this->ecrm->updateByProductID($nation_code, $c_produk_id, $du);
          $this->cpm->trans_commit();

        }

      }
      //END by Donny Dennison - 12 july 2022 14:56
      //new offer system

      //get images
      
      //by Donny Dennison - 15 february 2022 9:50
      //category product and category community have more than 1 language
      // $data['produk'] = $this->cpm->getById($nation_code, $c_produk_id);
      $data['produk'] = $this->cpm->getById($nation_code, $c_produk_id, $pelanggan, $product_type, $pelanggan->language_id);

      $data['produk']->galeri = $galeri;
      foreach ($data['produk']->galeri as &$gal) {
        if (isset($gal->url)) {
          $gal->url = str_replace("//", "/", $gal->url);
          $gal->url = $this->cdn_url($gal->url);
        }
        if (isset($gal->url_thumb)) {
          $gal->url_thumb = str_replace("//", "/", $gal->url_thumb);
          $gal->url_thumb = $this->cdn_url($gal->url_thumb);
        }
      }

      $data['produk']->galeri_video = $this->cpfm->getByProdukId($nation_code, $c_produk_id, "video");

      foreach ($data['produk']->galeri_video as &$gal_vid) {
        if (isset($gal_vid->url)) {
          $gal_vid->url = str_replace("//", "/", $gal_vid->url);
          $gal_vid->url = $this->cdn_url($gal_vid->url);
        }

        if (isset($gal_vid->url_thumb)) {
          $gal_vid->url_thumb = str_replace("//", "/", $gal_vid->url_thumb);
          $gal_vid->url_thumb = $this->cdn_url($gal_vid->url_thumb);
        }

      }

      $data['produk']->nama = html_entity_decode($data['produk']->nama,ENT_QUOTES);
      $data['produk']->deskripsi = html_entity_decode($data['produk']->deskripsi,ENT_QUOTES);

      if (isset($data['produk']->b_kondisi_icon)) {
        $data['produk']->b_kondisi_icon = $this->cdn_url($data['produk']->b_kondisi_icon);
      }
      if (isset($data['produk']->b_berat_icon)) {
        $data['produk']->b_berat_icon = $this->cdn_url($data['produk']->b_berat_icon);
      }
      if (isset($data['produk']->b_user_image_seller)) {

        // by Muhammad Sofi - 27 October 2021 10:12
        // if user img & banner not exist or empty, change to default image
        // $data['produk']->b_user_image_seller = $this->cdn_url($data['produk']->b_user_image_seller);
        if(file_exists(SENEROOT.$data['produk']->b_user_image_seller) && $data['produk']->b_user_image_seller != 'media/user/default.png'){
          $data['produk']->b_user_image_seller = $this->cdn_url($data['produk']->b_user_image_seller);
        } else {
          $data['produk']->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        }
      }
      if (isset($data['produk']->foto)) {
        $data['produk']->foto = $this->cdn_url($data['produk']->foto);
      }
      if (isset($data['produk']->thumb)) {
        $data['produk']->thumb = $this->cdn_url($data['produk']->thumb);
      }

      //by Donny Dennison - 22 february 2022 17:42
      //change product_type language
      if($pelanggan->language_id == 2){
        if($data['produk']->product_type == "Protection"){
          $data['produk']->product_type = "Proteksi";
        } else if($data['produk']->product_type == "Automotive"){
          $data['produk']->product_type = "Otomotif";
        } else if($data['produk']->product_type == "Free"){
          $data['produk']->product_type = "Gratis";
        }
      }

      //by Donny Dennison - 23-09-2021 15:45
      //revamp-profile
      //START by Donny Dennison - 23-09-2021 15:45

      //push notif to wanted product user from product name
      if (!empty($is_published)) {

        // //START by Donny Dennison - 16 december 2021 15:49
        // //get point as leaderboard rule
        // $pelangganAddress = $this->bua->getById($nation_code, $pelanggan->id, $b_user_alamat_id);

        // $checkAlreadyInleaderBoardHistory = $this->glphm->checkAlreadyInDB($nation_code, "", $pelangganAddress->kelurahan, $pelangganAddress->kecamatan, $pelangganAddress->kabkota, $pelangganAddress->provinsi, $pelanggan->id, $c_produk_id, 'product', 'post');
        
        // if(!isset($checkAlreadyInleaderBoardHistory->b_user_id)){

        //   //START by Donny Dennison 12 December 2022 - 15:24
        //   //Set daily limit 15 to product registration
        //   //get limit left
        //   $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "E10");

        //   if(!isset($limitLeft->limit_plus)){

        //     $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E10");

        //     $lastID = $this->glplm->getLastId($nation_code, date("Y-m-d"));

        //     $du = array();
        //     $du['nation_code'] = $nation_code;
        //     $du['id'] = $lastID;
        //     $du['cdate'] = date("Y-m-d");
        //     $du['b_user_id'] = $pelanggan->id;
        //     $du['code'] = "E10";
        //     $du['limit_plus'] = $pointGet->remark;
        //     $du['limit_minus'] = $pointGet->remark;
        //     $this->glplm->set($du);

        //     //get limit left
        //     $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "E10");

        //   }

        //   if($limitLeft->limit_plus > 0){

        //     //get point
        //     $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EA");
        //     if (!isset($pointGet->remark)) {
        //       $pointGet = new stdClass();
        //       $pointGet->remark = 3;
        //     }

        //     $di = array();
        //     $di['nation_code'] = $nation_code;
        //     $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
        //     $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
        //     $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
        //     $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
        //     $di['b_user_id'] = $pelanggan->id;
        //     $di['point'] = $pointGet->remark;
        //     $di['custom_id'] = $c_produk_id;
        //     $di['custom_type'] = 'product';
        //     $di['custom_type_sub'] = 'post';
        //     $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
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
        //     $this->cpm->trans_commit();
        //     // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '+', $di['point']);
        //     // $this->cpm->trans_commit();
        //     // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_post', '+', 1);
        //     // $this->cpm->trans_commit();
        //     $this->glplm->updateTotal($nation_code, date("Y-m-d"), $pelanggan->id, 'E10', 'limit_plus', '-', 1);
        //     $this->cpm->trans_commit();
        //   }
        //   //END by Donny Dennison 12 December 2022 - 15:24
        //   //Set daily limit 15 to product registration
        // }
        // //END by Donny Dennison - 16 december 2021 15:49
        // //get point as leaderboard rule
      } //check if published
      //END by Donny Dennison - 23-09-2021 15:45
      
    } else {
      $this->cpm->trans_rollback();
      $this->status = 990;
      $this->message = 'Cant edit product from database, please try again later';
    }
    //finish transaction
    $this->cpm->trans_end();

    //render output
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }

  public function editstok($product_id=0)
  {
    //init
    $dt = $this->__init();

    //default response
    $data = array();
    $data['produk'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    if ($product_id<='0') {
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $getProductType = $this->cpm->getProductType($nation_code, $product_id);
    if(!isset($getProductType->product_type)){
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $getProductType = $getProductType->product_type;

    $produk = $this->cpm->getById($nation_code, $product_id, $pelanggan, $getProductType);
    if (!isset($produk->id)) {
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }
    
    if ($produk->b_user_id_seller != $pelanggan->id) {
      $this->status = 907;
      $this->message = 'Access denied, you cant change other people product';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    if ($produk->product_type == 'Protection') {
      $this->status = 909;
      $this->message = 'product type is protection';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //START by Donny Dennison - 12 july 2022 14:56
    //new offer system
    $listOffering = $this->ecrm->getAll($nation_code, 'offer', $product_id, "offering");
    if($listOffering){
      $this->status = 920;
      $this->message = 'Cannot delete because there is offer';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();

    }

    $listOffering = $this->ecrm->getAll($nation_code, 'offer', $product_id, "accepted");
    if($listOffering){
      $this->status = 921;
      $this->message = 'Cannot delete because havent completed the review';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();

    }

    $listOffering = $this->ecrm->getAll($nation_code, 'offer', $product_id, "waiting review from seller");
    if($listOffering){
      $this->status = 921;
      $this->message = 'Cannot delete because havent completed the review';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();

    }

    $listOffering = $this->ecrm->getAll($nation_code, 'offer', $product_id, "waiting review from buyer");
    if($listOffering){
      $this->status = 921;
      $this->message = 'Cannot delete because havent completed the review';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();

    }
    //END by Donny Dennison - 12 july 2022 14:56

    if($produk->stok == 1){
      $stok = 0;
    }else{
      $stok = 1;
    }

    //start transaction
    $this->cpm->trans_start();

    //updating to database
    $du = array();
    $du['stok'] = $stok;

    $res = $this->cpm->update($nation_code, $pelanggan->id, $product_id, $du);
    if ($res) {
      $this->cpm->trans_commit();
      $this->status = 200;
      // $this->message = 'Product edited successfully';
      $this->message = 'Success';

      //get images

      //by Donny Dennison - 15 february 2022 9:50
      //category product and category community have more than 1 language
      // $data['produk'] = $this->cpm->getById($nation_code, $product_id);
      $data['produk'] = $this->cpm->getById($nation_code, $product_id, $pelanggan, $produk->product_type, $pelanggan->language_id);

      $data['produk']->galeri = $this->cpfm->getByProdukId($nation_code, $product_id);
      foreach ($data['produk']->galeri as &$gal) {
        if (isset($gal->url)) {
          $gal->url = str_replace("//", "/", $gal->url);
          $gal->url = $this->cdn_url($gal->url);
        }
        if (isset($gal->url_thumb)) {
          $gal->url_thumb = str_replace("//", "/", $gal->url_thumb);
          $gal->url_thumb = $this->cdn_url($gal->url_thumb);
        }
      }

      $data['produk']->galeri_video = $this->cpfm->getByProdukId($nation_code, $product_id, "video");
      foreach ($data['produk']->galeri_video as &$gal_vid) {
        if (isset($gal_vid->url)) {
          $gal_vid->url = str_replace("//", "/", $gal_vid->url);
          $gal_vid->url = $this->cdn_url($gal_vid->url);
        }
        if (isset($gal_vid->url_thumb)) {
          $gal_vid->url_thumb = str_replace("//", "/", $gal_vid->url_thumb);
          $gal_vid->url_thumb = $this->cdn_url($gal_vid->url_thumb);
        }
      }

      $data['produk']->nama = html_entity_decode($data['produk']->nama,ENT_QUOTES);
      $data['produk']->deskripsi = html_entity_decode($data['produk']->deskripsi,ENT_QUOTES);

      if (isset($data['produk']->b_kondisi_icon)) {
        $data['produk']->b_kondisi_icon = $this->cdn_url($data['produk']->b_kondisi_icon);
      }
      if (isset($data['produk']->b_berat_icon)) {
        $data['produk']->b_berat_icon = $this->cdn_url($data['produk']->b_berat_icon);
      }
      if (isset($data['produk']->b_user_image_seller)) {

        // by Muhammad Sofi - 27 October 2021 10:12
        // if user img & banner not exist or empty, change to default image
        // $data['produk']->b_user_image_seller = $this->cdn_url($data['produk']->b_user_image_seller);
        if(file_exists(SENEROOT.$data['produk']->b_user_image_seller) && $data['produk']->b_user_image_seller != 'media/user/default.png'){
          $data['produk']->b_user_image_seller = $this->cdn_url($data['produk']->b_user_image_seller);
        } else {
          $data['produk']->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        }
      }
      if (isset($data['produk']->foto)) {
        $data['produk']->foto = $this->cdn_url($data['produk']->foto);
      }
      if (isset($data['produk']->thumb)) {
        $data['produk']->thumb = $this->cdn_url($data['produk']->thumb);
      }
      
      if($data['produk']->product_type == 'Automotive' && ($data['produk']->b_kategori_id == 32 || $data['produk']->b_kategori_id == 33)){
        $data['produk']->automotive_type = $data['produk']->kategori;
      }else{
        $data['produk']->automotive_type = "";
      }

      //by Donny Dennison - 22 february 2022 17:42
      //change product_type language
      if($pelanggan->language_id == 2){
        if($data['produk']->product_type == "Protection"){
          $data['produk']->product_type = "Proteksi";
        } else if($data['produk']->product_type == "Automotive"){
          $data['produk']->product_type = "Otomotif";
        } else if($data['produk']->product_type == "Free"){
          $data['produk']->product_type = "Gratis";
        }
      }

    } else {
      $this->cpm->trans_rollback();
      $this->status = 990;
      $this->message = 'Cant edit product from database, please try again later';
    }
    //finish transaction
    $this->cpm->trans_end();

    //render output
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }

  public function kalkulasi()
  {
    $dt = $this->__init(); //init

    //default response
    $data = array();
    $data['harga_jual'] = 0.0;
    $data['biaya'] = new stdClass();
    $data['biaya']->admin = 0.0;
    $data['biaya']->asuransi = 0.0;
    $data['biaya']->admin_teks = "0";
    $data['pendapatan'] = 0.0;
    //$data['debug'] = new stdClass();

    //convert to string
    $data['harga_jual'] = strval($data['harga_jual']);
    $data['biaya']->admin = strval($data['biaya']->admin);
    $data['biaya']->asuransi = strval($data['biaya']->asuransi);
    $data['pendapatan'] = strval($data['pendapatan']);

    if ($this->is_log) {
      $this->seme_log->write("api_mobile", "Produk::kalkulasi -> ".json_encode($_POST));
    }

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $harga_jual = (float) $this->input->post("harga_jual");
    $harga_jual = round($harga_jual, 2);
    $data['harga_jual'] = $harga_jual;

    $berat = $this->input->post("berat");
    $berat = round($berat, 2);

    //declare and get variable
    $selling_fee_percent = 0;

    //get preset from DB
    $fee = array();
    $presets = $this->ccm->getByClassified($nation_code, "product_fee");
    if (count($presets)>0) {
      foreach ($presets as $pre) {
        $fee[$pre->code] = $pre;
      }
      unset($pre); //free some memory
      unset($presets); //free some memory
    }

    //passing into current var
    if (isset($fee['F7']->remark)) {
      $selling_fee_percent = $fee['F7']->remark;
    } //insurance deduction type

    //calculating Earning Total
    $selling_fee = round($harga_jual * ($selling_fee_percent/100), 2);
    $data['pendapatan'] = round($harga_jual - $selling_fee, 2);
    $admin = round($harga_jual - $data['pendapatan'], 2);

    //render output
    $this->status = 200;
    $this->message = 'Success';
    $data['harga_jual'] = strval($data['harga_jual']);
    $data['biaya']->admin = strval($admin);
    $data['biaya']->asuransi = strval(0.0);
    $data['biaya']->admin_teks = strval($selling_fee_percent);
    $data['pendapatan'] = strval($data['pendapatan']);
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }

  //by Donny Dennison - 10 december 2021 13:36
  //add feature hot item di homepage
  public function countshared()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['produk'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $this->status = 300;
    $this->message = 'Missing one or more parameters';
   
    $c_produk_id = $this->input->get('c_produk_id');
    if ($c_produk_id<='0') {
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $getProductType = $this->cpm->getProductType($nation_code, $c_produk_id);
    if(!isset($getProductType->product_type)){
      $this->status = 310;
      $this->message = 'Data not found or deleted';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
      die();
    }

    $getProductType = $getProductType->product_type;

    $produk = $this->cpm->getById($nation_code, $c_produk_id, $pelanggan, $getProductType);
    if (!isset($produk->id)) {
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //start transaction and lock table
    $this->cpshm->trans_start();

    //get last id for first time
    $cpshm_id = $this->cpshm->getLastId($nation_code,$c_produk_id);

    //initial insert with latest ID
    $di = array();
    $di['nation_code'] = $nation_code;
    $di['id'] = $cpshm_id;
    $di['c_produk_id'] = $c_produk_id;
    $di['b_user_id'] = $pelanggan->id;

    $res = $this->cpshm->set($di);
    if (!$res) {
      $this->cpshm->trans_rollback();
      $this->cpshm->trans_end();
      $this->status = 903;
      $this->message = "Error while save, please try again later";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $this->status = 200;
    $this->message = "Success";
    $this->cpshm->trans_commit();
    $this->cpshm->trans_end();

    //START by Donny Dennison - 13 october 2022 14:10
    //change point policy
    // $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

    // //get total share
    // $totalShare = $this->glphm->countAll($nation_code, "", "", "", "", $pelanggan->id, "+", "", "product", "share", date("Y-m-d"), "");

    // $limitShare = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EC");
    // if (!isset($limitShare->remark)) {
    //   $limitShare = new stdClass();
    //   $limitShare->remark = 10;
    // }

    // if($totalShare < $limitShare->remark){

    //   //get point
    //   $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "ED");
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
    //   $di['custom_id'] = $c_produk_id;
    //   $di['custom_type'] = 'product';
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

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }

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
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language
    // check apisess
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
    $sort_col = $this->input->get("sort_col");
    $sort_dir = $this->input->get("sort_dir");
    $page = $this->input->get("page");
    $page_size = $this->input->get("page_size");
    // $grid = $this->input->get("grid");
    // $keyword = trim($this->input->get("keyword"));
    //$kategori_id = $this->input->get("kategori_id");
    $parent_discussion_id = (int) $this->input->get("parent_discussion_id");
    $product_id = $this->input->get("product_id");
    if ($parent_discussion_id<=0) {
      $parent_discussion_id = 0;
    }
    if ($product_id<='0') {
      $product_id = 0;
    }

    // $kategori_id = ''; //not used
    // if (empty($kategori_id)) {
    //   $kategori_id="";
    // }

    //sanitize input
    $tbl_as = $this->fdis->getTblAs();
    $tbl2_as = $this->fdis->getTbl2As();
    $sort_col = $this->__sortColDiscussion($sort_col, $tbl_as, $tbl2_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_child_discussion = $this->__page(1);
    $page_size = $this->__pageSize($page_size);
    $page_size_child_discussion = $this->__pageSize(1);

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

    //get produk data
    $ddcount = $this->fdis->countAll($nation_code,$parent_discussion_id, $product_id);
    $ddata = $this->fdis->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $parent_discussion_id, $product_id);

    foreach ($ddata as &$dd) {

      //by Donny Dennison 17 September 2021 - 11:54
      //community-feature
      // $dd->cdate_text = $this->humanTiming($dd->cdate);
      $dd->cdate_text = $this->humanTiming($dd->cdate, null, $pelanggan->language_id);

      $dd->text = html_entity_decode($dd->text,ENT_QUOTES);

      $dd->diskusi_anak_total = $this->fdis->countAll($nation_code,$dd->id, $product_id);

      $dd->diskusi_anak = $this->fdis->getAll($nation_code, $page_child_discussion, $page_size_child_discussion, $sort_col, $sort_dir, $dd->id, $product_id);

      //by Donny Dennison 17 September 2021 - 11:54
      //community-feature
      foreach($dd->diskusi_anak as &$de){

        // $de->cdate_text = $this->humanTiming($de->cdate);
        $de->cdate_text = $this->humanTiming($de->cdate, null, $pelanggan->language_id);
        $de->text = html_entity_decode($de->text,ENT_QUOTES);

      }

    }
    
    unset($dd); //free some memory

    //build result
    $data['diskusis'] = $ddata;
    $data['diskusi_total'] = $ddcount;

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }

  //by Donny Dennison - 7 august 2020 10:40
  // add QnA / discussion feature
  public function detail_discussion()
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
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language
    // check apisess
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

    $parent_discussion_id = (int) $this->input->get("parent_discussion_id");
    $product_id = $this->input->get("product_id");
    if ($parent_discussion_id<=0) {
      $parent_discussion_id = 0;
    }
    if ($product_id<='0') {
      $product_id = 0;
    }

    //sanitize input
    $tbl_as = $this->fdis->getTblAs();
    $tbl2_as = $this->fdis->getTbl2As();
    $sort_col = $this->__sortColDiscussion($sort_col, $tbl_as, $tbl2_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

    $ddata = $this->fdis->getbyDiscussionID($nation_code, $parent_discussion_id, $product_id);

    if(!isset($ddata->discussion_id)){
      $this->status = 200;
      $this->message = 'Success';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    }

    //by Donny Dennison 17 September 2021 - 11:54
    //community-feature
    // $ddata->cdate_text = $this->humanTiming($ddata->cdate);
    $ddata->cdate_text = $this->humanTiming($ddata->cdate, null, $pelanggan->language_id);

    $ddata->text = html_entity_decode($ddata->text,ENT_QUOTES);

    $ddata->diskusi_anak_total = $this->fdis->countAll($nation_code,$parent_discussion_id, $product_id);

    $ddata->diskusi_anak = $this->fdis->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $parent_discussion_id, $product_id);

    //by Donny Dennison 17 September 2021 - 11:54
    //community-feature
    foreach($ddata->diskusi_anak as &$de){

      // $de->cdate_text = $this->humanTiming($de->cdate);
      $de->cdate_text = $this->humanTiming($de->cdate, null, $pelanggan->language_id);
      $de->text = html_entity_decode($de->text,ENT_QUOTES);

    }

    //build result
    $data['diskusis'] = $ddata;

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
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
    $data['diskusis'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $this->status = 300;
    $this->message = 'Missing one or more parameters';

    //collect product input
    $parent_discussion_id = (int) $this->input->post('parent_discussion_id');
    $text = $this->input->post('text');
    $product_id = $this->input->post('product_id');

    //input validation
    if (empty($parent_discussion_id)) {
      $parent_discussion_id = 0;
    }

    // //by Donny Dennison 16 augustus 2020 00:25
    // //fix check emoji in insert & edit product and discussion
    // if( preg_match( $this->unicodeRegexp, $text ) ){

    //   $this->status = 1099;
    //   $this->message = 'Symbol image(ex.Emoji..) is not allowed in Product Discussion';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    //   die();

    // }

    //by Donny Dennison 24 february 2021 18:45
    //change ’ to ' in add & edit product name and description
    $text = str_replace('’',"'",$text);
    
    // $text = filter_var($text, FILTER_SANITIZE_STRING);
    $text = nl2br($text);

    //by Donny Dennison - 15 augustus 2020 15:09
    //bug fix \n (enter) didnt get remove
    $text = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $text);

    $text = str_replace("\\n", "<br />", $text);

    if (empty($product_id)) {
      $product_id = 0;
    }

    //validating
    if ($parent_discussion_id<0) {
      $this->status = 1099;
      $this->message = 'Invalid parent_discussion_id';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    if ($product_id<='0') {
      $this->status = 595;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $getProductType = $this->cpm->getProductType($nation_code, $product_id);
    $getProductType = $getProductType->product_type;

    $produk = $this->cpm->getById($nation_code, $product_id, $pelanggan, $getProductType);

    $user_type = 'buyer';

    if($produk->b_user_id_seller == $pelanggan->id){
    
      $user_type = 'seller';

    }

    //start transaction and lock table
    $this->fdis->trans_start();

    //initial insert with latest ID
    $dis = array();
    $dis['nation_code'] = $nation_code;
    $dis['parent_f_discussion_id'] = $parent_discussion_id;
    $dis['product_id'] = $product_id;
    $dis['b_user_id'] = $pelanggan->id;
    $dis['user_type'] = $user_type;
    $dis['text'] = $text;
    $dis['cdate'] = 'NOW()';
    $res = $this->fdis->set($dis);
    if (!$res) {
      $this->fdis->trans_rollback();
      $this->fdis->trans_end();
      $this->status = 1106;
      $this->message = "Error while posting discussion, please try again later";
      $this->seme_log->write("api_mobile", 'API_Mobile/Produk::Discussion::baru -- RollBack -- forceClose '.$this->status.' '.$this->message);
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //input collection to db
   
    $this->status = 200;
    $this->message = "Success";
    // $this->message = 'Your writing is posted';
    
    $this->seme_log->write("api_mobile", 'API_Mobile/Produk::Discussion::baru -- INFO '.$this->status.' '.$this->message);
    $this->fdis->trans_commit();
    $this->fdis->trans_end();

    //START by Donny Dennison - 13 october 2022 14:10
    //change point policy
    // if($parent_discussion_id == 0  && $user_type == 'buyer'){

    //   $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

    //   //get total active question from $pelanggan
    //   $totalActiveQuestionUser = $this->fdis->countAllProductIDUserID($nation_code, $pelanggan->id, $product_id);
      
    //   if($totalActiveQuestionUser == 1){

    //     //get point
    //     $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EB");
    //     if (!isset($pointGet->remark)) {
    //       $pointGet = new stdClass();
    //       $pointGet->remark = 1;
    //     }

    //     $di = array();
    //     $di['nation_code'] = $nation_code;
    //     $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
    //     $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
    //     $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
    //     $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
    //     $di['b_user_id'] = $pelanggan->id;
    //     $di['point'] = $pointGet->remark;
    //     $di['custom_id'] = $product_id;
    //     $di['custom_type'] = 'product';
    //     $di['custom_type_sub'] = 'reply';
    //     $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
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
    //     // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '+', $di['point']);
    //   }
    // }
    //END by Donny Dennison - 13 october 2022 14:10
    //change point policy

    $this->cpm->updateTotal($nation_code, $product_id, 'total_discussion', '+', 1);

    if($user_type == "buyer"){
      $parentId = $dis['parent_f_discussion_id'];
      $productId = $dis['product_id'];
      $userid = $dis['b_user_id'];

      $detailProduct = $this->cpm->getByIdRaw($nation_code, $productId);
      $sellerid = $detailProduct->b_user_id;

      // select fcm token
      // $users = $this->bu->getFcmTokenSeller($nation_code, $sellerid);
      $user = $this->bu->getById($nation_code, $sellerid);

      // select id discuss
      if($parentId == 0){
      $dataID = $res;
      }else{
        $dataID = $parentId;
      }

      $dpe = array();
      $dpe['nation_code'] = $nation_code;
      $dpe['b_user_id'] = $sellerid;
      $dpe['id'] = $this->dpem->getLastId($nation_code, $sellerid);
      $dpe['type'] = "discussion";
      if($user->language_id == 2) {
        $dpe['judul'] = "Diskusi Produk";
        $dpe['teks'] =  "Seseorang meninggalkan pesan di produk( ".html_entity_decode($detailProduct->nama,ENT_QUOTES). " )";
      } else {
        $dpe['judul'] = "Product Q&A";
        $dpe['teks'] =  "Someone left a message on the product( ".html_entity_decode($detailProduct->nama,ENT_QUOTES). " )";
      }

      $dpe['gambar'] = 'media/pemberitahuan/productdiscussion.png';
      $dpe['cdate'] = "NOW()";
      $extras = new stdClass();
      $extras->id = $dataID;
      $extras->product_id = (string) $productId;
      $extras->nama = $detailProduct->nama;
      $extras->harga_jual = $detailProduct->harga_jual;
      $extras->foto = base_url().$detailProduct->thumb;
      if($user->language_id == 2) { 
        $extras->judul = "Diskusi Produk";
        $extras->teks =  "Seseorang meninggalkan pesan di produk( ".html_entity_decode($detailProduct->nama,ENT_QUOTES). " )";
      } else {
        $extras->judul = "Product Q&A";
        $extras->teks =  "Someone left a message on the product( ".html_entity_decode($detailProduct->nama,ENT_QUOTES). " )";
      }

      $dpe['extras'] = json_encode($extras);
      $this->dpem->set($dpe);

      $classified = 'setting_notification_user';
      $code = 'U5';

      $receiverSettingNotif = $this->busm->getValue($nation_code, $sellerid, $classified, $code);

      if (!isset($receiverSettingNotif->setting_value)) {
          $receiverSettingNotif->setting_value = 0;
      }

      if ($receiverSettingNotif->setting_value == 1 && $user->is_active == 1) {

        if($user->device == "ios"){
          //push notif to ios
          $device = "ios"; //jenis device
          $tokens = $user->fcm_token; //device token
          if(!is_array($tokens)) $tokens = array($tokens);
          if($user->language_id == 2) {
            $title = "Diskusi Produk";
            $message = "Seseorang meninggalkan pesan di produk( ".html_entity_decode($detailProduct->nama,ENT_QUOTES). " )";
          } else {
            $title = "Product Q&A";
            $message = "Someone left a message on the product( ".html_entity_decode($detailProduct->nama,ENT_QUOTES). " )";
          }
          
          $image = 'media/pemberitahuan/promotion.png';
          $type = 'discussion';
          $payload = new stdClass();
          $payload->id = $dataID;
          $payload->product_id = $productId;
          $payload->nama = $detailProduct->nama;
          $payload->harga_jual = $detailProduct->harga_jual;
          $payload->foto = base_url().$detailProduct->thumb;
          if($user->language_id == 2) {
            $payload->judul = "Diskusi Produk";
            //by Donny Dennison
            //dicomment untuk handle message too big, response dari fcm
            // $payload->teks = strip_tags(html_entity_decode($di['teks']));
            $payload->teks = "Seseorang meninggalkan pesan di produk( ".html_entity_decode($this->convertEmoji($detailProduct->nama),ENT_QUOTES). " )";
          } else {
            $payload->judul = "Product Q&A";
            $payload->teks = "Someone left a message on the product( ".html_entity_decode($this->convertEmoji($detailProduct->nama),ENT_QUOTES). " )";
          }
          
          $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
          if ($this->is_log) {
              $this->seme_log->write("api_mobile", 'API_Admin/Discussion::baru __pushNotifiOS: '.json_encode($res));
          }
        }else{
          //push notif to ios
          $device = "android"; //jenis device
          $tokens = $user->fcm_token; //device token
          if(!is_array($tokens)) $tokens = array($tokens);
          if($user->language_id == 2) { 
            $title = "Diskusi Produk";
            $message = "Seseorang meninggalkan pesan di produk( ".html_entity_decode($this->convertEmoji($detailProduct->nama),ENT_QUOTES). " )";
          } else {
            $title = "Product Q&A";
            $message = "Someone left a message on the product( ".html_entity_decode($this->convertEmoji($detailProduct->nama),ENT_QUOTES). " )";
          }
          $image = 'media/pemberitahuan/promotion.png';
          $type = 'discussion';
          $payload = new stdClass();
          $payload->id = $dataID;
          $payload->product_id = $productId;
          $payload->nama = $detailProduct->nama;
          $payload->harga_jual = $detailProduct->harga_jual;
          $payload->foto = base_url().$detailProduct->thumb;
          if($user->language_id == 2) { 
            $payload->judul = "Diskusi Produk";
            //by Donny Dennison
            //dicomment untuk handle message too big, response dari fcm
            // $payload->teks = strip_tags(html_entity_decode($di['teks']));
            $payload->teks = "Seseorang meninggalkan pesan di produk( ".html_entity_decode($this->convertEmoji($detailProduct->nama),ENT_QUOTES). " )";
          } else {
            $payload->judul = "Product Q&A";
            $payload->teks = "Someone left a message on the product( ".html_entity_decode($this->convertEmoji($detailProduct->nama),ENT_QUOTES). " )";
          }
          $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
          if ($this->is_log) {
              $this->seme_log->write("api_mobile", 'API_Admin/Discussion::baru __pushNotifiOS: '.json_encode($res));
          }
        }

      }

    }else{
      $parentId = $dis['parent_f_discussion_id'];
      $productId = $dis['product_id'];
      $ios = array();
      $android = array();

      // select product detail dengan product_id = productId
      $productDetail = $this->cpm->getByIdRaw($nation_code, $productId);
      $sellerid = $productDetail->b_user_id;
      // select id, b_user_id, usertype, nama dengan where parent id = parentID dan parent_f_discussion_id = parentId
      $getData = $this->fdis->getLastId($nation_code, $parentId, $productId, $sellerid);

      foreach ($getData as $dts){

        $user = $this->bu->getById($nation_code, $dts->b_user_id);

        if ($dts->b_user_id != $pelanggan->id) {

          $classified = 'setting_notification_user';
          $code = 'U5';

          $receiverSettingNotif = $this->busm->getValue($nation_code, $dts->b_user_id, $classified, $code);

          if (!isset($receiverSettingNotif->setting_value)) {
              $receiverSettingNotif->setting_value = 0;
          }

          if ($receiverSettingNotif->setting_value == 1 && $user->is_active == 1) {
            if (strtolower($user->device) == 'ios') {
                $ios[] = $user->fcm_token;
            } else {
                $android[] = $user->fcm_token;
            }
          }

          $dpe = array();
          $dpe['nation_code'] = $nation_code;
          $dpe['b_user_id'] = $dts->b_user_id;
          $dpe['id'] = $this->dpem->getLastId($nation_code, $dts->b_user_id);
          $dpe['type'] = "discussion";
          $dpe['judul'] = "Diskusi Produk";
          $dpe['teks'] =  "Seseorang meninggalkan pesan di produk( ".html_entity_decode($productDetail->nama,ENT_QUOTES). " )";
          $dpe['gambar'] = 'media/pemberitahuan/productdiscussion.png';
          $dpe['cdate'] = "NOW()";
          $extras = new stdClass();
          //parent_f_id
          $extras->id = $parentId;
          $extras->product_id = (string) $productId;
          $extras->nama = $productDetail->nama;
          $extras->harga_jual = $productDetail->harga_jual;
          $extras->foto = base_url().$productDetail->thumb;
          $extras->judul = "Diskusi Produk";
          $extras->teks =  "Seseorang meninggalkan pesan di produk( ".html_entity_decode($productDetail->nama,ENT_QUOTES). " )";
          $dpe['extras'] = json_encode($extras);
          $this->dpem->set($dpe);

        } 
      }

      if (array_unique($ios)) {
    
        //push notif to ios
        $device = "ios"; //jenis device
        $tokens = $ios; 
        $title = "Diskusi Produk";
        $message = "Seseorang meninggalkan pesan di produk( ".html_entity_decode($this->convertEmoji($productDetail->nama),ENT_QUOTES). " )";
        $image = 'media/pemberitahuan/promotion.png';
        $type = 'discussion';
        $payload = new stdClass();
        $payload->id = $parentId;
        $payload->product_id = $productId;
        $payload->nama = $productDetail->nama;
        $payload->harga_jual = $productDetail->harga_jual;
        $payload->foto = base_url().$productDetail->thumb;
        $payload->judul = "Diskusi Produk";
        //by Donny Dennison
        //dicomment untuk handle message too big, response dari fcm
        // $payload->teks = strip_tags(html_entity_decode($di['teks']));
        $payload->teks = "Seseorang meninggalkan pesan di produk( ".html_entity_decode($this->convertEmoji($productDetail->nama),ENT_QUOTES). " )";
        $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", 'API_Admin/Discussion::baru __pushNotifiOS: '.json_encode($res));
        }
      }

      if (array_unique($android)) {
          
        $device = "android"; //jenis device
        $tokens = $android; 
        $title = "Diskusi Produk";
        $message = "Seseorang meninggalkan pesan di produk( ".html_entity_decode($this->convertEmoji($productDetail->nama),ENT_QUOTES). " )";
        $type = 'discussion';
        $image = 'media/pemberitahuan/promotion.png';
        $payload = new stdClass();
        $payload->id = $parentId;
        $payload->product_id = $productId;
        $payload->nama = $productDetail->nama;
        $payload->harga_jual = $productDetail->harga_jual;
        $payload->foto = base_url().$productDetail->thumb;
        $payload->judul = "Diskusi Produk";
        //by Donny Dennison
        //dicomment untuk handle message too big, response dari fcm
        // $payload->teks = strip_tags(html_entity_decode($di['teks']));
        $payload->teks = "Seseorang meninggalkan pesan di produk( ".html_entity_decode($this->convertEmoji($productDetail->nama),ENT_QUOTES). " )";
        $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", 'API_Admin/Discussion::baru __pushNotifAndroid: '.json_encode($res));
        }
      } 

    }   
   
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
  }

  //by Donny Dennison - 7 august 2020 10:40
  // add QnA / discussion feature
  public function delete_discussion()
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
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language
    // check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //populate input get
    $discussion_id = (int) $this->input->get("discussion_id");

    //check discussion id and user id
    $c = $this->fdis->getbyDiscussionIDUserID($nation_code, $discussion_id, $pelanggan->id);
    if (!isset($c->discussion_id)) {
      $this->status = 1111;
      $this->message = 'Discussion ID and User ID is different';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }
    

    //start transaction and lock table
    $this->fdis->trans_start();

    //initial insert with latest ID
    $di = array();
    $di['edate'] = 'NOW()';
    $di['is_active'] = 0;
    $res = $this->fdis->update($nation_code, $discussion_id, $di);
    $this->fdis->trans_commit();
    if (!$res) {
      $this->fdis->trans_rollback();
      $this->fdis->trans_end();
      $this->status = 1107;
      $this->message = "Error while delete discussion, please try again later";
      $this->seme_log->write("api_mobile", 'API_Mobile/Produk::Discussion::hapus -- RollBack -- forceClose '.$this->status.' '.$this->message);
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    $this->status = 200;
    // $this->message = 'Your case is deleted';
    $this->message = 'Success';
    
    $this->seme_log->write("api_mobile", 'API_Mobile/Produk::Discussion::hapus -- INFO '.$this->status.' '.$this->message);


    $this->cpm->updateTotal($nation_code, $c->product_id, 'total_discussion', '-', 1);
    $this->fdis->trans_commit();
    
    if($c->parent_f_discussion_id == 0){

      $activeChild = $this->fdis->countAll($nation_code, $discussion_id, $c->product_id);
      
      $this->cpm->updateTotal($nation_code, $c->product_id, 'total_discussion', '-', $activeChild);
      $this->fdis->trans_commit();

      $di = array();
      $di['edate'] = 'NOW()';
      $di['is_active'] = 0;
      $this->fdis->updateByparentId($nation_code, $discussion_id, $di);
      $this->fdis->trans_commit();

      //START by Donny Dennison - 13 october 2022 14:10
      //change point policy
      // if($c->user_type == "buyer"){

      //   //get total active question from $pelanggan
      //   $totalActiveQuestionUser = $this->fdis->countAllProductIDUserID($nation_code, $pelanggan->id, $c->product_id);

      //   if($totalActiveQuestionUser == 0){

      //     $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

      //     //get point
      //     $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EB");
      //     if (!isset($pointGet->remark)) {
      //       $pointGet = new stdClass();
      //       $pointGet->remark = 1;
      //     }

      //     $di = array();
      //     $di['nation_code'] = $nation_code;
      //     $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
      //     $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
      //     $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
      //     $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
      //     $di['b_user_id'] = $pelanggan->id;
      //     $di['plusorminus'] = "-";
      //     $di['point'] = $pointGet->remark;
      //     $di['custom_id'] = $c->product_id;
      //     $di['custom_type'] = 'product';
      //     $di['custom_type_sub'] = 'reply';
      //     $di['custom_text'] = $pelanggan->fnama.' has delete '.$di['custom_type'].' '.$di['custom_type_sub'].' and lose '.$di['point'].' point(s)';
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
      //     $this->fdis->trans_commit();
      //     // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
      //     // $this->fdis->trans_commit();
      //   }
      // }
      //END by Donny Dennison - 13 october 2022 14:10
      //change point policy
    }

    //commit and end transaction
    $this->fdis->trans_commit();
    $this->fdis->trans_end();

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");

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
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language
    // check apisess
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
    $discussion_id = (int) $this->input->get("discussion_id");
    

    //start transaction and lock table
    $this->fdisrep->trans_start();

    //initial insert with latest ID
    $di = array();
    $di['nation_code'] = $nation_code;
    $di['f_discussion_id'] = $discussion_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['cdate'] = 'NOW()';
    $res = $this->fdisrep->set($di);
    if (!$res) {
      $this->fdisrep->trans_rollback();
      $this->fdisrep->trans_end();
      $this->status = 1108;
      $this->message = "Error while report discussion, please try again later";
      $this->seme_log->write("api_mobile", 'API_Mobile/Produk::Discussion::report -- RollBack -- forceClose '.$this->status.' '.$this->message);
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //input collection to db
    $res = true;
    if ($res) {
      $this->status = 200;
      $this->message = "Success";
      // $this->message = 'This case is reported to SellOn admin';
      
      $this->seme_log->write("api_mobile", 'API_Mobile/Produk::Discussion::report -- INFO '.$this->status.' '.$this->message);
    } else {
      $this->fdisrep->trans_rollback();
      $this->fdisrep->trans_end();
      $this->status = 1104;
      $this->message = "Error while report discussion, please try again later";
      $this->seme_log->write("api_mobile", 'API_Mobile/Produk::Discussion::report -- RollBack -- forceClose '.$this->status.' '.$this->message);
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
      die();
    }

    //commit and end transaction
    $this->fdisrep->trans_commit();
    $this->fdisrep->trans_end();

    //update is_report and report_date
    $di = array();
    $di['report_date'] = 'NOW()';
    $di['is_report'] = 1;
    $this->fdis->update($nation_code, $discussion_id, $di);

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");

  }

}
