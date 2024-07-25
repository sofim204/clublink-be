<?php
class Produk_Automotive extends JI_Controller
{
  public $is_soft_delete=1;
  public $is_log = 1;
  public $imgQueue;

  //by Donny Dennison 16 augustus 2020 00:25
  //fix check emoji in insert & edit product and discussion
  //credit : https://stackoverflow.com/questions/41580483/detect-emoticons-in-string
  /*public $unicodeRegexp = '([*#0-9](?>\\xEF\\xB8\\x8F)?\\xE2\\x83\\xA3|\\xE2..(\\xF0\\x9F\\x8F[\\xBB-\\xBF])?(?>\\xEF\\xB8\\x8F)?|\\xE3(?>\\x80[\\xB0\\xBD]|\\x8A[\\x97\\x99])(?>\\xEF\\xB8\\x8F)?|\\xF0\\x9F(?>[\\x80-\\x86].(?>\\xEF\\xB8\\x8F)?|\\x87.\\xF0\\x9F\\x87.|..(\\xF0\\x9F\\x8F[\\xBB-\\xBF])?|(((?<zwj>\\xE2\\x80\\x8D)\\xE2\\x9D\\xA4\\xEF\\xB8\\x8F\k<zwj>\\xF0\\x9F..(\k<zwj>\\xF0\\x9F\\x91.)?|(\\xE2\\x80\\x8D\\xF0\\x9F\\x91.){2,3}))?))';*/

  public function __construct()
  {
    parent::__construct();
    //$this->setTheme('frontx');
    $this->lib("seme_log");
    $this->lib("seme_email");
    $this->lib("seme_purifier");
    $this->load("api_mobile/a_notification_model", "anot");
    $this->load("api_mobile/b_user_model", "bu");
    $this->load("api_mobile/b_user_alamat_model", "bua");
    $this->load("api_mobile/b_user_setting_model", "busm");
    // $this->load("api_mobile/b_user_productwanted_model", "bupw");
    $this->load("api_mobile/b_kategori_model3", "bkm3");
    $this->load("api_mobile/b_kondisi_model", "bkon");
    $this->load("api_mobile/b_berat_model", "brt");
    $this->load("api_mobile/c_produk_model", "cpm");
    $this->load("api_mobile/c_produk_foto_model", "cpfm");
    $this->load("api_mobile/common_code_model", "ccm");
    $this->load("api_mobile/d_wishlist_model", "dwlm");
    $this->load("api_mobile/d_cart_model", "cart");
    // $this->load("api_mobile/d_pemberitahuan_model", "dpem");
    $this->load("api_mobile/f_discussion_model", "fdis");
    $this->load("api_mobile/f_discussion_report_model", "fdisrep");

    $this->load("api_mobile/c_produk_detail_automotive_model", "cpdam");

    //by Donny Dennison - 16 december 2021 15:49
    //get point as leaderboard rule
    // $this->load("api_mobile/g_leaderboard_point_area_model", 'glpam');
    $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
    // $this->load("api_mobile/g_leaderboard_ranking_model", 'glrm');

    //by Donny Dennison - 08 november 2022 15:12
    //new feature, block product(block account or product in automotive product)
    $this->load("api_mobile/c_block_model", "cbm");

    $this->imgQueue = array();
  }
  private function __floatWeight($val)
  {
    $val = (float) $val;
    return ''.round($val, 2);
  }
  // private function __getDimensionMax($long, $width, $height)
  // {
  //   $max = $long;
  //   if ($max<$width) {
  //     $max = $width;
  //   }
  //   if ($max<$height) {
  //     $max = $height;
  //   }
  //   return $max;
  // }

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

  //       if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::__checkUploadedFile -- INFO keyname: '.$keyname.' RESULT: TRUE');
  //       return true;
  //     }
  //   }
  //   if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::__checkUploadedFile -- INFO keyname: '.$keyname.' RESULT: FALSE');
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
  //     $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::__uploadImagex -- INFO KeyName: '.$keyname.' PID:'.$produk_id.' ke:'.$ke.' '.$sc->status.' '.$sc->message.'');
  //   }
  //   return $sc;
  // }

  // private function __moveImagex($nation_code, $url, $produk_id="0", $ke="")
  // {
  //   $sc = new stdClass();
  //   $sc->status = 500;
  //   $sc->message = 'Error';
  //   $sc->image = '';
  //   $sc->thumb = '';
  //   $produk_id = (int) $produk_id;

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

  //   $file_path = SENEROOT.parse_url($url, PHP_URL_PATH);

  //   if (file_exists($file_path) && is_file($file_path)) {
          
  //     $file_path_thumb = parse_url($url, PHP_URL_PATH);
  //     $extension = pathinfo($file_path_thumb, PATHINFO_EXTENSION);
  //     $file_path_thumb = substr($file_path_thumb,0,strripos($file_path_thumb,'.'));
  //     $file_path_thumb = SENEROOT.$file_path_thumb.'-thumb.'.$extension;

  //     $filename = "$nation_code-$produk_id-$ke".date('YmdHis');
  //     $filethumb = $filename."-thumb.".$extension;
  //     $filename = $filename.".".$extension;

  //     rename($file_path, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
  //     rename($file_path_thumb, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);

  //     $sc->status = 200;
  //     $sc->message = 'Success';
  //     $sc->thumb = str_replace("//", "/", $targetdir.'/'.$filethumb);
  //     $sc->image = str_replace("//", "/", $targetdir.'/'.$filename);
    
  //   } else {
  //     $sc->status = 997;
  //     $sc->message = 'Failed';
  //   }
    
  //   if ($this->is_log) {
  //     $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::__moveImagex -- INFO URL: '.$url.' PID:'.$produk_id.' ke:'.$ke.' '.$sc->status.' '.$sc->message.'');
  //   }
  //   return $sc;
  // }

  private function __sortCol($sort_col, $tbl_as, $tbl2_as)
  {
    switch ($sort_col) {
      case 'cdate':
      $sort_col = "$tbl_as.cdate";
      break;
      case 'harga_jual':
      $sort_col = "$tbl_as.harga_jual";
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
  //   $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::__processUpload -- INFO '.$this->status.' '.$this->message);
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
  // private function __dataImageProduct($nation_code,$c_produk_id,$cpfm_id,$url,$thumb,$img_count){
  //   $dix = array();
  //   $dix['nation_code'] = $nation_code;
  //   $dix['c_produk_id'] = $c_produk_id;
  //   $dix['id'] = $cpfm_id;
  //   $dix['url'] = $url;
  //   $dix['url_thumb'] = $thumb;
  //   $dix['is_active'] = 1;
  //   $dix['caption'] = '';
  //   return $this->cpfm->set($dix);
  // }

  public function index()
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
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
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
    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    $page_size = $this->input->post("page_size");
    $kelurahan = trim($this->input->post("kelurahan"));
    $kecamatan = trim($this->input->post("kecamatan"));
    $kabkota = trim($this->input->post("kabkota"));
    $provinsi = trim($this->input->post("provinsi"));
    $b_kategori_id = $this->input->post("b_kategori_id");
    $b_brand_id = $this->input->post("b_brand_id");
    $year = $this->input->post("year");
    $keyword = trim($this->input->post("keyword"));

    //sanitize input
    $tbl_as = $this->cpm->getTblAs();
    $tbl2_as = $this->cpm->getTbl2As();
    $sort_col = $this->__sortCol($sort_col, $tbl_as, $tbl2_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);


    //advanced filter
    $harga_jual_min = '';
    if (isset($_POST['harga_jual_min'])) {
      $harga_jual_min = (float) $_POST['harga_jual_min'];
      if ($harga_jual_min<=-1) {
        $harga_jual_min = '';
      }
    }
    if ($harga_jual_min>0) {
      $harga_jual_min = (float) $harga_jual_min;
    }

    $harga_jual_max = (float) $this->input->post("harga_jual_max");
    if ($harga_jual_max<=0) {
      $harga_jual_max = "";
    }
    if ($harga_jual_max>0) {
      $harga_jual_max = (float) $harga_jual_max;
    }

    if (strlen($kelurahan)<=1) {
      $kelurahan = "All";
    }
    if (strlen($kecamatan)<=1) {
      $kecamatan = "All";
    }
    if (strlen($kabkota)<=1) {
      $kabkota = "All";
    }
    if (strlen($provinsi)<=1) {
      $provinsi = "DKI Jakarta";
    }

    if ($b_kategori_id != 33) {
      $b_kategori_id = 32;
    }

    if ($b_brand_id >0) {
      $brandData = $this->bkm3->getById($nation_code, $b_brand_id);
      if(isset($brandData->id)){
        $b_brand_id = array(
          'b_brand_id' => $b_brand_id,
          'brand_name' => strtolower($brandData->nama)
        );
      }
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

    //START by Donny Dennison - 08 november 2022 15:12
    //new feature, block product(block account or product in automotive product)
    if (isset($pelanggan->id)) {

      $blockDataAccount = $this->cbm->getAllByUserId($nation_code, 0, $pelanggan->id, "account");
      $blockDataAccountReverse = $this->cbm->getAllByUserId($nation_code, 0, 0, "account", $pelanggan->id);
      $blockDataProduct = $this->cbm->getAllByUserId($nation_code, 0, $pelanggan->id, "product");

    }else{

      $blockDataAccount = array();
      $blockDataAccountReverse = array();
      $blockDataProduct = array();

    }
    //END by Donny Dennison - 08 november 2022 15:12
    //new feature, block product(block account or product in automotive product)

    //get produk data
    $ddcount = $this->cpm->countAllAutomotive($nation_code, $harga_jual_min, $harga_jual_max, $kelurahan, $kecamatan, $kabkota, $provinsi, $b_brand_id, $year, $b_kategori_id, $keyword, $blockDataAccount, $blockDataAccountReverse, $blockDataProduct);
    $ddata = $this->cpm->getAllAutomotive($nation_code, $page, $page_size, $sort_col, $sort_dir, $harga_jual_min, $harga_jual_max, $kelurahan, $kecamatan, $kabkota, $provinsi, $b_brand_id, $year, $b_kategori_id, $keyword, $blockDataAccount, $blockDataAccountReverse, $blockDataProduct, $pelanggan, $pelanggan->language_id);

    //manipulating data
    foreach ($ddata as $pd) {
      //conver to utf friendly
      // if (isset($pd->nama)) {
      //   $pd->nama = $this->__dconv($pd->nama);
      // }
      $pd->nama = html_entity_decode($pd->nama,ENT_QUOTES);

      if (isset($pd->brand)) {
        $pd->brand = $this->__dconv($pd->brand);
      }
      if (isset($pd->b_user_nama_seller)) {
        $pd->b_user_nama_seller = $this->__dconv($pd->b_user_nama_seller);
      }

      if (isset($pd->b_user_image_seller)) {
        if (empty($pd->b_user_image_seller)) {
          $pd->b_user_image_seller = 'media/produk/default.png';
        }
        $pd->b_user_image_seller = $this->cdn_url($pd->b_user_image_seller);
      }
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
      if (isset($pd->b_kondisi_icon)) {
        if (empty($pd->b_kondisi_icon)) {
          $pd->b_kondisi_icon = 'media/icon/default.png';
        }
        $pd->b_kondisi_icon = $this->cdn_url($pd->b_kondisi_icon);
      }
      if (isset($pd->b_berat_icon)) {
        if (empty($pd->b_berat_icon)) {
          $pd->b_berat_icon = 'media/icon/default.png';
        }
        $pd->b_berat_icon = $this->cdn_url($pd->b_berat_icon);
      }

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

      $data['produks'][] = $pd;
    }
    unset($ddata,$pd);

    //build result
    $data['produk_total'] = $ddcount;

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  }

  // public function detail($id)
  // {
  //   //initial
  //   $dt = $this->__init();

  //   //default result
  //   $data = array();
  //   $data['produk'] = new stdClass();

  //   //check nation_code
  //   $nation_code = $this->input->get('nation_code');
  //   $nation_code = $this->nation_check($nation_code);
  //   if (empty($nation_code)) {
  //     $this->status = 101;
  //     $this->message = 'Missing or invalid nation_code';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $id = (int) $id;
  //   if ($id<=0) {
  //     $this->status = 595;
  //     $this->message = 'Invalid product ID';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $pelanggan = new stdClass();
  //   $apisess = $this->input->get('apisess');
  //   if (strlen($apisess)>3) {
  //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   }

  //   $produk = $this->cpm->getOwnedById($nation_code, $id);
  //   if (!isset($produk->id)) {
  //     $this->status = 1600;
  //     $this->message = 'Invalid product ID or Product not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }
  //   if($produk->stok<=0){
  //     $produk->stok='0';
  //   }

  //   $is_liked = 0;
  //   if (isset($pelanggan->id)) {
  //     $is_liked = $this->dwlm->check($nation_code, $pelanggan->id, $produk->id);
  //   }
  //   if (strlen($produk->b_user_image_seller)<=4) {
  //     $produk->b_user_image_seller = 'media/user/default.png';
  //   }

  //   // filter utf-8
  //   if (isset($produk->b_user_fnama_seller)) {
  //     $produk->b_user_fnama_seller = $this->__dconv($produk->b_user_fnama_seller);
  //   }
  //   if (isset($produk->nama)) {
  //     $produk->nama = $this->__dconv($produk->nama);
  //   }
  //   if (isset($produk->brand)) {
  //     $produk->brand = $this->__dconv($produk->brand);
  //   }
  //   if (isset($produk->deskripsi)) {
  //     $produk->deskripsi = $this->__dconv($produk->deskripsi);
  //   }

  //   //cast CDN
  //   $produk->b_user_image_seller = $this->cdn_url($produk->b_user_image_seller);
  //   $produk->b_kondisi_icon = $this->cdn_url($produk->b_kondisi_icon);
  //   $produk->b_berat_icon = $this->cdn_url($produk->b_berat_icon);
  //   $produk->kategori_icon = $this->cdn_url($produk->kategori_icon);
  //   $produk->foto = $this->cdn_url($produk->foto);
  //   $produk->thumb = $this->cdn_url($produk->thumb);

  //   $i=0;
  //   $produk->galeri = array();
  //   $galeri = $this->cpfm->getByProdukId($nation_code, $id);
  //   foreach ($galeri as &$gal) {
  //     if($i>=5) continue;
  //     $gal->url = $this->cdn_url($gal->url);
  //     $gal->url_thumb = $this->cdn_url($gal->url_thumb);
  //     $produk->galeri[] = $gal;
  //     $i++;
  //   }

  //   if (isset($produk->is_liked)) {
  //     $produk->is_liked = (int) $is_liked;
  //   }
  //   $produk->berat = round($produk->berat, 2);
  //   $produk->harga_jual = round($produk->harga_jual, 2);
  //   $produk->dimension_long = round($produk->dimension_long, 0);
  //   $produk->dimension_width = round($produk->dimension_width, 0);
  //   $produk->dimension_height = round($produk->dimension_height, 0);

  //   //by Donny Dennison 7 oktober 2020 - 14:10
  //   //add promotion face mask
  //   if($id == 1746 || $id == 1752){
    
  //     $produk->courier_services = 'SingPost';
    
  //   }

  //   $this->status = 200;
  //   $this->message = 'Success';

  //   //by Donny Dennison - 7 august 2020 09:47
  //   //add discussion data to detail api
  //   //START by Donny Dennison - 7 august 2020 09:47

  //   $tbl_as = $this->fdis->getTblAs();
  //   $tbl2_as = $this->fdis->getTbl2As();
  //   $sort_col = $this->__sortColDiscussion('cdate', $tbl_as, $tbl2_as);
  //   $sort_dir = $this->__sortDir('desc');
  //   $page = $this->__page(1);
  //   $page_size_parent_discussion = $this->__pageSize(2);
  //   $page_size_child_discussion = $this->__pageSize(1);

  //   $produk->diskusi_total = $this->fdis->countAll($nation_code, 0, $id);

  //   $produk->diskusis = $this->fdis->getAll($nation_code, $page, $page_size_parent_discussion, $sort_col, $sort_dir, 0, $id);

  //   foreach ($produk->diskusis as $key => $discuss) {

  //     $produk->diskusis[$key]->diskusi_anak_total = $this->fdis->countAll($nation_code,$discuss->discussion_id, $id);
    
  //    $produk->diskusis[$key]->diskusi_anak = $this->fdis->getAll($nation_code, $page, $page_size_child_discussion, $sort_col, $sort_dir, $discuss->discussion_id, $id);

  //   }

  //   //END by Donny Dennison - 7 august 2020 09:47

  //   $data['produk'] = $produk;

  //   //by Donny Dennison - 14 august 2020 15:53
  //   // curl to facebook every time customer open product detail
  //   //START by Donny Dennison - 14 august 2020 15:53

  //   //send data to facebook
  //   $postToFB= array(
      
  //     'data' => array( 

  //       array(
        
  //         'event_name' => 'ViewContent',
  //         'event_time' => strtotime('now'),
  //         'event_id' => 'ViewContent'.date('YmdHis'),
  //         'event_source_url' => 'https://sellon.net/product_detail.php?product_id='.$id,
  //         'user_data' => array(
  //           'client_ip_address' => '35.240.185.29',
  //           'client_user_agent' => 'browser'
  //         ),

  //         'custom_data' => array(
  //           'value' => $produk->harga_jual,
  //           'currency' => 'SGD',
  //           'content_name' => $produk->nama,
  //           'content_category' => $produk->kategori,
  //           'content_ids' => array($id),
  //           'contents' => array( 
              
  //             0 => array(
  //               'id' => $id,
  //               'quantity' => $produk->stok,
  //               'item_price' => $produk->harga_jual
  //             )

  //           ),
  //           'content_type' => 'product',
  //           'delivery_category' => 'home_delivery'
  //         ) 
  //       )

  //     ),
  //     // 'test_event_code' =>'TEST20037'

  //   );
    

  //   $curlToFacebook = $this->__CurlFacebook($postToFB);

  //   $this->seme_log->write("api_mobile", "__CurlFacebook : Response -> ".$curlToFacebook);
    
  //   //END by Donny Dennison - 14 august 2020 15:53

  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  // }

  // //by Donny Dennison - 23 september 2020 15:42
  // //add direct delivery feature
  // // private function __shipment_check($berat, $panjang, $lebar, $tinggi)
  // private function __shipment_check($berat, $panjang, $lebar, $tinggi, $direct_delivery = 0)
  // {
  //   //init
  //   $data = array();
  //   $service_duration = array();
  //   $courier_services = array();
  //   $this->status = 200;
  //   $this->message = 'success';

  //   //casting input
  //   $b = round($berat, 1); //weight
  //   $p = round($panjang, 0); //long
  //   $l = round($lebar, 0); //width
  //   $t = round($tinggi, 0); //height

  //   //get max dimension
  //   $dimension_max = $this->__getDimensionMax($p, $l, $t);
  //   $qxv = $p+$l+$t;
  //   if ($this->is_log) {
  //     $this->seme_log->write("api_mobile", "API_Mobile/Produk::__shipment_check -> B: $b, D: $p x $l x $t");
  //   }

  //   //by Donny Dennison - 23 september 2020 15:42
  //   //add direct delivery feature
  //   //START by Donny Dennison - 23 september 2020 15:42

  //   if($direct_delivery == 1){

  //     $courier_services[0] = new stdClass();
  //     $courier_services[0]->nama = "Direct Delivery";
  //     $courier_services[0]->jenis = "Next Day";
  //     $courier_services[0]->icon = $this->cdn_url("assets/images/direct_delivery.png");
  //     $courier_services[0]->vehicle_types = array();
  //     $courier_services[0]->vehicle_types[0] = new stdClass();
  //     // $courier_services[0]->vehicle_types[0]->nama = "Regular";
  //     // $courier_services[0]->vehicle_types[0]->icon = $this->cdn_url("assets/images/regular.png");

  //   //By Donny Dennison - 7 june 2020 - 10:05
  //   //request by mr Jackie, add checking if width or length or long more than 1,5 m then courier service is gogovan
  //   // if ($b<=30.0 && $qxv<=300.0) {
  //   // if ($b<=30.0 && $qxv<=300.0 && $dimension_max <= 150) {
  //   }else if ($b<=30.0 && $qxv<=300.0 && $dimension_max <= 150) {
    
  //   //END by Donny Dennison - 23 september 2020 15:42

  //     //bisa QXPress same day
  //     $courier_services[0] = new stdClass();
  //     $courier_services[0]->nama = "QXpress";
  //     $courier_services[0]->jenis = "Same Day";
  //     $courier_services[0]->icon = $this->cdn_url("assets/images/qxpress.png");
  //     $courier_services[0]->vehicle_types = array();
  //     $courier_services[0]->vehicle_types[0] = new stdClass();
  //     $courier_services[0]->vehicle_types[0]->nama = "Regular";
  //     $courier_services[0]->vehicle_types[0]->icon = $this->cdn_url("assets/images/regular.png");
  //   } else {
  //     $courier_services[0] = new stdClass();

  //     //by Donny Dennison - 15 september 2020 17:45
  //     //change name, image, etc from gogovan to gogox
  //     // $courier_services[0]->nama = "Gogovan";
  //     $courier_services[0]->nama = "Gogox";

  //     $courier_services[0]->jenis = "Same Day";

  //     //by Donny Dennison - 15 september 2020 17:45
  //     //change name, image, etc from gogovan to gogox
  //     // $courier_services[0]->icon = $this->cdn_url("assets/images/gogovan.png");
  //     $courier_services[0]->icon = $this->cdn_url("assets/images/gogox.png");

  //     $courier_services[0]->vehicle_types = array();

  //     $vt = new stdClass();
  //     $vt->nama = "Lorry 24 Ft";
  //     $vt->icon = $this->cdn_url("assets/images/lorry24.png");
  //     $courier_services[0]->vehicle_types[] = $vt;

  //     if ($p<400 && $l<180 && $t<200 && $b<2500) {
  //       $vt = new stdClass();
  //       $vt->nama = "Lorry 14 Ft";
  //       $vt->icon = $this->cdn_url("assets/images/lorry14.png");
  //       $courier_services[0]->vehicle_types[] = $vt;
  //     }
  //     if ($p<300 && $l<150 && $t<180 && $b<1500) {
  //       $vt = new stdClass();
  //       $vt->nama = "Lorry 10 Ft";
  //       $vt->icon = $this->cdn_url("assets/images/lorry10.png");
  //       $courier_services[0]->vehicle_types[] = $vt;
  //     }
  //     if ($p<240 && $l<150 && $t<120 && $b<900) {
  //       $vt = new stdClass();
  //       $vt->nama = "Van";
  //       $vt->icon = $this->cdn_url("assets/images/van.png");
  //       $courier_services[0]->vehicle_types[] = $vt;
  //     }
  //   }
  //   $data = new stdClass();
  //   $data->courier_services = $courier_services;
  //   $data->services_duration = $service_duration;
  //   unset($service_duration);
  //   unset($courier_services);
  //   return $data;
  // }

  // public function baru()
  // {
  //   //initial
  //   $dt = $this->__init();
  //   //error_reporting(0);

  //   //default result
  //   $data = array();
  //   $data['produk'] = new stdClass();

  //   //check nation_code
  //   $nation_code = $this->input->get('nation_code');
  //   $nation_code = $this->nation_check($nation_code);
  //   if (empty($nation_code)) {
  //     $this->status = 101;
  //     $this->message = 'Missing or invalid nation_code';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //check apisess
  //   $apisess = $this->input->get('apisess');
  //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   if (!isset($pelanggan->id)) {
  //     $this->status = 401;
  //     $this->message = 'Missing or invalid API session';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $this->__userUnconfirmedDenied($nation_code, $pelanggan);
        
  //   $this->status = 300;
  //   $this->message = 'Missing one or more parameters';

  //   //collect product input
  //   $nama = trim($this->input->post('nama'));
  //   $brand = trim($this->input->post('brand'));
  //   $b_kategori_id = trim($this->input->post('b_kategori_id'));
  //   // $b_berat_id = (int) $this->input->post('b_berat_id');
  //   // $b_kondisi_id = (int) $this->input->post('b_kondisi_id');
  //   $b_user_alamat_id = (int) $this->input->post('b_user_alamat_id');
  //   $harga_jual = $this->input->post('harga_jual');
  //   $deskripsi_singkat = $this->input->post('deskripsi_singkat');
  //   $deskripsi = $this->input->post('deskripsi');
  //   $satuan = trim($this->input->post('satuan'));
  //   $berat = $this->input->post('berat');
  //   // $dimension_long = $this->input->post("dimension_long");
  //   // $dimension_width = $this->input->post("dimension_width");
  //   // $dimension_height = $this->input->post("dimension_height");
  //   // $vehicle_types = $this->input->post("vehicle_types");
  //   // $courier_services = $this->input->post("courier_services");
  //   // $services_duration = $this->input->post("services_duration");
  //   $stok = (int) $this->input->post("stok");
  //   // $is_include_delivery_cost = (int) $this->input->post("is_include_delivery_cost");
  //   // $is_published = (int) $this->input->post("is_published");
  //   $model = $this->input->post("model");
  //   $color = $this->input->post("color");
  //   $year = $this->input->post("year");

  //   //input validation
  //   if (empty($nama)) {
  //     $nama = '';
  //   }
  //   if (empty($brand)) {
  //     $brand = '';
  //   }
  //   // if (empty($b_kategori_id)) {
  //   //   $b_kategori_id = 'null';
  //   // }
  //   // if (empty($b_kondisi_id)) {
  //   //   $b_kondisi_id = 'null';
  //   // }
  //   // if (empty($b_berat_id)) {
  //   //   $b_berat_id = 'null';
  //   // }
  //   if (empty($b_user_alamat_id)) {
  //     $b_user_alamat_id = 0;
  //   }
  //   if (empty($deskripsi_singkat)) {
  //     $deskripsi_singkat = '';
  //   }
  //   if (empty($deskripsi)) {
  //     $deskripsi = '';
  //   }
  //   if (empty($foto)) {
  //     $foto = "media/produk/default.png";
  //   }
  //   if (empty($satuan)) {
  //     $satuan = 'pcs';
  //   }
  //   // if (strtolower($services_duration) == 'sameday' || strtolower($services_duration) == 'same day') {
  //   //   $services_duration = 'Same Day';
  //   // }
  //   // if (strtolower($services_duration) == 'nextday' || strtolower($services_duration) == 'next day') {
  //   //   $services_duration = 'Next Day';
  //   // }

  //   //validating FK
  //   if ($b_kategori_id == 'car') {
  //     $b_kategori_id = 32;
  //   }else if($b_kategori_id == 'motorcycle'){
  //     $b_kategori_id = 33;
  //   }
  //   // if ($b_kondisi_id<=0) {
  //   //   $b_kondisi_id = 0;
  //   // }
  //   // if ($b_berat_id<=0) {
  //   //   $b_berat_id = 0;
  //   // }

  //   $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
  //   if(isset($pelangganAddress->id)){
  //     $b_user_alamat_id = $pelangganAddress->id;
  //     unset($pelangganAddress);
  //   }

  //   //validating user address
  //   if ($b_user_alamat_id<=0) {
  //     $this->status = 1099;
  //     $this->message = 'Invalid b_user_alamat_id';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }
    
  //   $almt = $this->bua->getByIdUserId($nation_code, $pelanggan->id, $b_user_alamat_id);
  //   if (!isset($almt->id)) {
  //     $this->status = 916;
  //     $this->message = 'Please choose pickup address';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $kat = $this->bkm3->getById($nation_code, $b_kategori_id);
  //   if (!isset($kat->id)) {
  //     $this->status = 917;
  //     $this->message = 'Please choose automotive product category';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   // $kon = $this->bkon->getById($nation_code, $b_kondisi_id);
  //   // if (!isset($kon->id)) {
  //   //   $this->status = 1101;
  //   //   $this->message = 'Please choose product condition';
  //   //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //   //   die();
  //   // }

  //   //if($harga_jual<=0) $harga_jual = 1;
  //   // if ($stok<=0) {
  //   //   $stok = 1;
  //   // }
  //   if ($berat<=0) {
  //     $berat = 1.0;
  //   }
  //   // if ($dimension_long<=0) {
  //   //   $dimension_long = 1;
  //   // }
  //   // if ($dimension_width<=0) {
  //   //   $dimension_width = 1;
  //   // }
  //   // if ($dimension_height<=0) {
  //   //   $dimension_height = 1;
  //   // }

  //   //re-casting weight
  //   $berat = $this->__floatWeight($berat);

  //   // $is_include_delivery_cost = !empty($is_include_delivery_cost) ? 1:0;
  //   // $is_published = !empty($is_published) ? 1:0;

  //   //by Donny Dennison 24 february 2021 18:45
  //   //change ’ to ' in add & edit product name and description
  //   $nama = str_replace('’',"'",$nama);
  //   $deskripsi = str_replace('’',"'",$deskripsi);

  //   //by Donny Dennison 16 augustus 2020 00:25
  //   //fix check emoji in insert & edit product and discussion
  //   // if( preg_match( $this->unicodeRegexp, $nama ) ){

  //   //   $this->status = 1104;
  //   //   $this->message = 'Symbol image(ex.Emoji..) is not allowed in Automotive Product Name or Description';
  //   //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //   //   die();

  //   // }else if( preg_match( $this->unicodeRegexp, $deskripsi ) ){

  //   //   $this->status = 1104;
  //   //   $this->message = 'Symbol image(ex.Emoji..) is not allowed in Automotive Product Name or Description';
  //   //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //   //   die();

  //   // }

  //   // $nama = filter_var($nama, FILTER_SANITIZE_STRING);
  //   // $brand = filter_var($brand, FILTER_SANITIZE_STRING);
  //   // $deskripsi_singkat = filter_var($deskripsi_singkat, FILTER_SANITIZE_STRING);
  //   // $deskripsi = filter_var($deskripsi, FILTER_SANITIZE_STRING);
  //   $deskripsi = nl2br($deskripsi);

  //   //by Donny Dennison - 15 augustus 2020 15:09
  //   //bug fix \n (enter) didnt get remove
  //   $deskripsi = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $deskripsi);

  //   if (strlen($nama)<=0) {
  //     $this->status = 910;
  //     $this->message = 'Product name is required';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   if ($harga_jual<=0) {
  //     $this->status = 911;
  //     $this->message = 'Price is required';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }
  //   $harga_jual = $harga_jual;

  //   // if ($stok<=0) {
  //   //   $this->status = 1106;
  //   //   $this->message = 'Please specify product quantity (stock) correctly';
  //   //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //   //   die();
  //   // }

  //   //check dimension
  //   // $dimension_max = $this->__getDimensionMax($dimension_long, $dimension_width, $dimension_height);
  //   // if ($dimension_max>724) {
  //   //   $this->status = 1107;
  //   //   $this->message = 'Product too big, we currently unsupported product with dimension above 7,2 m';
  //   //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //   //   die();
  //   // }

  //   //by Donny Dennison - 29 july 2020 - 15:47
  //   //prevent insert product duplication
  //   $duplicateProduct = $this->cpm->getActiveByUserIdProductNameBrandModelColorYearDescriptionPrice($nation_code, $pelanggan->id, $nama, $brand, $model, $color, $year, $deskripsi, $harga_jual);
  //   if (!empty($duplicateProduct)) {
  //     $this->status = 1109;
  //     $this->message = 'Your product has already been registered';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //start transaction and lock table
  //   $this->cpm->trans_start();

  //   //get last id for first time
  //   $cpm_id = $this->cpm->getLastId($nation_code);

  //   //initial insert with latest ID
  //   $di = array();
  //   $di['nation_code'] = $nation_code;
  //   $di['id'] = $cpm_id;
  //   $di['b_user_id'] = $pelanggan->id;
  //   $di['b_user_alamat_id'] = $b_user_alamat_id;
  //   $di['b_kategori_id'] = $b_kategori_id;
  //   // $di['b_kondisi_id'] = $b_kondisi_id;
  //   // $di['b_berat_id'] = $b_berat_id;
  //   $di['nama'] = $nama;
  //   $di['brand'] = $brand;
  //   $di['harga_jual'] = $harga_jual;
  //   $di['deskripsi_singkat'] = $deskripsi_singkat;
  //   $di['deskripsi'] = $deskripsi;
  //   $di['foto'] = $foto;
  //   $di['thumb'] = $foto;
  //   // $di['satuan'] = $satuan;
  //   $di['stok'] = $stok;
  //   $di['berat'] = $berat;
  //   // $di['dimension_long'] = $dimension_long;
  //   // $di['dimension_width'] = $dimension_width;
  //   // $di['dimension_height'] = $dimension_height;
  //   // $di['courier_services'] = $courier_services;
  //   // $di['vehicle_types'] = $vehicle_types;
  //   // $di['services_duration'] = $services_duration;
  //   $di['cdate'] = 'NOW()';
  //   // $di['is_include_delivery_cost'] = $is_include_delivery_cost;
  //   // $di['is_published'] = $is_published;

  //   //by Donny Dennison - 7 december 2020 11:03
  //   //add new product type(meetup)
  //   $di['product_type'] = 'Automotive';

  //   $di['alamat2'] = $almt->alamat2;
  //   $di['kelurahan'] = $almt->kelurahan;
  //   $di['kecamatan'] = $almt->kecamatan;
  //   $di['kabkota'] = $almt->kabkota;
  //   $di['provinsi'] = $almt->provinsi;
  //   $di['kodepos'] = $almt->kodepos;
  //   $di['latitude'] = $almt->latitude;
  //   $di['longitude'] = $almt->longitude;

  //   $res = $this->cpm->set($di);
  //   if (!$res) {
  //     $this->cpm->trans_rollback();
  //     $this->cpm->trans_end();
  //     $this->status = 1107;
  //     $this->message = "Error while posting automotive product, please try again later";
  //     $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::baru -- RollBack -- forceClose '.$this->status.' '.$this->message);
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //initial insert product detail automotive with latest ID
  //   $di = array();
  //   $di['nation_code'] = $nation_code;
  //   $di['c_produk_id'] = $cpm_id;
  //   $di['model'] = $model;
  //   $di['color'] = $color;
  //   $di['year'] = $year;
  //   $res = $this->cpdam->set($di);
  //   if (!$res) {
  //     $this->cpm->trans_rollback();
  //     $this->cpm->trans_end();

  //     $this->status = 1107;
  //     $this->message = "Error while posting automotive product, please try again later";
  //     $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::baru -- RollBack -- forceClose '.$this->status.' '.$this->message);
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }
  //   $this->cpm->trans_commit();

  //   //input collection to db
  //   $res = true;
  //   if ($res) {
  //     $this->status = 200;
  //     $this->message = "Success";
  //     // if (!empty($is_published)) {
  //       $this->message = 'Your automotive product Has Been Posted';
  //     // } else {
  //       // $this->message = 'Your automotive product Has Been Saved as Draft';
  //     // }
  //     $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::baru -- INFO '.$this->status.' '.$this->message);
  //   } else {
  //     $this->cpm->trans_rollback();
  //     $this->cpm->trans_end();
  //     $this->status = 1107;
  //     $this->message = "Error while posting automotive product, please try again later";
  //     $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::baru -- RollBack -- forceClose '.$this->status.' '.$this->message);
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //doing image upload if success
  //   if ($res) {

  //     $totalFoto = 0;
  //     $checkFileExist = 1;
  //     $checkFileTemporaryOrNot = 1;

  //     for ($i=1; $i < 4; $i++) {

  //       $file_path = parse_url($this->input->post('foto'.$i), PHP_URL_PATH);

  //       if (!file_exists(SENEROOT.$file_path)) {
  //         $checkFileExist = 0;
  //       }

  //       if (strpos($file_path, 'temporary') !== false) {
        
  //         $totalFoto++;

  //       }
      
  //     }

  //     for ($i=1; $i < 6; $i++) {

  //       $file_path = parse_url($this->input->post('foto'.$i), PHP_URL_PATH);
        
  //       if($this->input->post('foto'.$i) != null){
          
  //         if (strpos($file_path, 'temporary') !== false) {

  //         }else{

  //           $checkFileTemporaryOrNot = 0;
          
  //         }

  //       }
      
  //     }

  //     if ($totalFoto < 3) {
  //       $this->status = 1300;
  //       $this->message = 'Upload failed';
  //       $this->cpm->trans_rollback();
  //       $this->cpm->trans_end();
  //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //       die();
  //     }

  //     if ($checkFileExist == 0) {
  //       $this->status = 995;
  //       $this->message = 'Failed upload, temporary already gone';
  //       $this->cpm->trans_rollback();
  //       $this->cpm->trans_end();
  //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //       die();
  //     }

  //     if ($checkFileTemporaryOrNot == 0) {
  //       $this->status = 996;
  //       $this->message = 'Failed upload, upload is not temporary';
  //       $this->cpm->trans_rollback();
  //       $this->cpm->trans_end();
  //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //       die();
  //     }

  //     $iq = new stdClass();
  //     $iq->cpfm_id = 1;
  //     $cpfm_last = $this->cpfm->getLastByProdukId($nation_code,$cpm_id);
  //     if(isset($cpfm_last->id)){
  //       $iq->cpfm_id = $cpfm_last->id + 1;
  //     }
  //     $iq->img_count = 0;
  //     if (isset($iq->cpfm_id)) {
  //       $cpfm_id = $iq->cpfm_id;
  //     }
  //     if (isset($iq->img_count)) {
  //       $img_count = $iq->img_count;
  //     }

  //     $keyname = 'foto1';
  //     // $upi = $this->__uploadImagex($nation_code, $keyname, $cpm_id, $iq->cpfm_id);
  //     $upi = $this->__moveImagex($nation_code, $this->input->post("foto1"), $cpm_id, $iq->cpfm_id);
  //     if(isset($upi->status)){
  //       if($upi->status == 200){
  //         $dpi = $this->__dataImageProduct($nation_code,$cpm_id,$cpfm_id,$upi->image,$upi->thumb,$iq->img_count);
  //         if($dpi){
  //           $iq->cpfm_id++;
  //           $iq->img_count++;
  //         }else{
  //           $this->status = 994;
  //           $this->message = 'Failed save uploaded image to db';
  //           $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::baru -- forceClose '.$this->status.' '.$this->message.' for '.$keyname.' CPID: '.$cpm_id.' CPFID: '.$cpfm_id);
  //           $this->cpm->trans_rollback();
  //           $this->cpm->trans_end();

  //           //START by Donny Dennison - 24 november 2021 16:48
  //           //bug fix product image still exist in db if upload failed
  //           $this->cpm->trans_start();
  //           $this->cpfm->delByProdukId($nation_code, $cpm_id);
  //           $this->cpm->trans_commit();
  //           $this->cpm->trans_end();
  //           //END by Donny Dennison - 24 november 2021 16:48

  //           $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //           die();
  //         }
  //       }else{
  //         $this->status = 1300;
  //         $this->message = 'Upload failed';
  //         $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::baru -- forceClose '.$this->status.' '.$this->message.' for '.$keyname);
  //         $this->cpm->trans_rollback();
  //         $this->cpm->trans_end();

  //         //START by Donny Dennison - 24 november 2021 16:48
  //         //bug fix product image still exist in db if upload failed
  //         $this->cpm->trans_start();
  //         $this->cpfm->delByProdukId($nation_code, $cpm_id);
  //         $this->cpm->trans_commit();
  //         $this->cpm->trans_end();
  //         //END by Donny Dennison - 24 november 2021 16:48

  //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //         die();
  //       }
  //     }else{
  //       $this->status = 1300;
  //       $this->message = 'Upload failed';
  //       $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::baru -- forceClose '.$this->status.' '.$this->message.' for '.$keyname);
  //       $this->cpm->trans_rollback();
  //       $this->cpm->trans_end();

  //       //START by Donny Dennison - 24 november 2021 16:48
  //       //bug fix product image still exist in db if upload failed
  //       $this->cpm->trans_start();
  //       $this->cpfm->delByProdukId($nation_code, $cpm_id);
  //       $this->cpm->trans_commit();
  //       $this->cpm->trans_end();
  //       //END by Donny Dennison - 24 november 2021 16:48

  //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //       die();
  //     }

  //     $cpfm_id = 2;
  //     $cpfm_last = $this->cpfm->getLastByProdukId($nation_code,$cpm_id);
  //     if(isset($cpfm_last->id)){
  //       $iq->cpfm_id = $cpfm_last->id + 1;
  //     }
  //     $img_count = 1;
  //     if (isset($iq->cpfm_id)) {
  //       $cpfm_id = $iq->cpfm_id;
  //     }
  //     if (isset($iq->img_count)) {
  //       $img_count = $iq->img_count;
  //     }
  //     $keyname = 'foto2';
  //     // $upi = $this->__uploadImagex($nation_code, $keyname, $cpm_id, $iq->cpfm_id);
  //     $upi = $this->__moveImagex($nation_code, $this->input->post("foto2"), $cpm_id, $iq->cpfm_id);
  //     if(isset($upi->status)){
  //       if($upi->status == 200){
  //         $dpi = $this->__dataImageProduct($nation_code,$cpm_id,$cpfm_id,$upi->image,$upi->thumb,$iq->img_count);
  //         if($dpi){
  //           $iq->cpfm_id++;
  //           $iq->img_count++;
  //         }else{
  //           $this->status = 994;
  //           $this->message = 'Failed save uploaded image to db';
  //           $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::baru -- forceClose '.$this->status.' '.$this->message.' for '.$keyname.' CPID: '.$cpm_id.' CPFID: '.$cpfm_id);
  //           $this->cpm->trans_rollback();
  //           $this->cpm->trans_end();

  //           //START by Donny Dennison - 24 november 2021 16:48
  //           //bug fix product image still exist in db if upload failed
  //           $this->cpm->trans_start();
  //           $this->cpfm->delByProdukId($nation_code, $cpm_id);
  //           $this->cpm->trans_commit();
  //           $this->cpm->trans_end();
  //           //END by Donny Dennison - 24 november 2021 16:48

  //           $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //           die();
  //         }
  //       }else{
  //         $this->status = 1300;
  //         $this->message = 'Upload failed';
  //         $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::baru -- forceClose '.$this->status.' '.$this->message.' for '.$keyname);
  //         $this->cpm->trans_rollback();
  //         $this->cpm->trans_end();

  //         //START by Donny Dennison - 24 november 2021 16:48
  //         //bug fix product image still exist in db if upload failed
  //         $this->cpm->trans_start();
  //         $this->cpfm->delByProdukId($nation_code, $cpm_id);
  //         $this->cpm->trans_commit();
  //         $this->cpm->trans_end();
  //         //END by Donny Dennison - 24 november 2021 16:48

  //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //         die();
  //       }
  //     }else{
  //       $this->status = 1300;
  //       $this->message = 'Upload failed';
  //       $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::baru -- forceClose '.$this->status.' '.$this->message.' for '.$keyname);
  //       $this->cpm->trans_rollback();
  //       $this->cpm->trans_end();

  //       //START by Donny Dennison - 24 november 2021 16:48
  //       //bug fix product image still exist in db if upload failed
  //       $this->cpm->trans_start();
  //       $this->cpfm->delByProdukId($nation_code, $cpm_id);
  //       $this->cpm->trans_commit();
  //       $this->cpm->trans_end();
  //       //END by Donny Dennison - 24 november 2021 16:48

  //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //       die();
  //     }

  //     $cpfm_id = 3;
  //     $cpfm_last = $this->cpfm->getLastByProdukId($nation_code,$cpm_id);
  //     if(isset($cpfm_last->id)){
  //       $iq->cpfm_id = $cpfm_last->id + 1;
  //     }
  //     $img_count = 2;
  //     if (isset($iq->cpfm_id)) {
  //       $cpfm_id = $iq->cpfm_id;
  //     }
  //     if (isset($iq->img_count)) {
  //       $img_count = $iq->img_count;
  //     }

  //     $keyname = 'foto3';
  //     // $upi = $this->__uploadImagex($nation_code, $keyname, $cpm_id, $iq->cpfm_id);
  //     $upi = $this->__moveImagex($nation_code, $this->input->post("foto3"), $cpm_id, $iq->cpfm_id);
  //     if(isset($upi->status)){
  //       if($upi->status == 200){
  //         $dpi = $this->__dataImageProduct($nation_code,$cpm_id,$cpfm_id,$upi->image,$upi->thumb,$iq->img_count);
  //         if($dpi){
  //           $iq->cpfm_id++;
  //           $iq->img_count++;
  //         }else{
  //           $this->status = 994;
  //           $this->message = 'Failed save uploaded image to db';
  //           $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::baru -- forceClose '.$this->status.' '.$this->message.' for '.$keyname.' CPID: '.$cpm_id.' CPFID: '.$cpfm_id);
  //           $this->cpm->trans_rollback();
  //           $this->cpm->trans_end();

  //           //START by Donny Dennison - 24 november 2021 16:48
  //           //bug fix product image still exist in db if upload failed
  //           $this->cpm->trans_start();
  //           $this->cpfm->delByProdukId($nation_code, $cpm_id);
  //           $this->cpm->trans_commit();
  //           $this->cpm->trans_end();
  //           //END by Donny Dennison - 24 november 2021 16:48

  //           $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //           die();
  //         }
  //       }else{
  //         $this->status = 1300;
  //         $this->message = 'Upload failed';
  //         $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::baru -- forceClose '.$this->status.' '.$this->message.' for '.$keyname);
  //         $this->cpm->trans_rollback();
  //         $this->cpm->trans_end();

  //         //START by Donny Dennison - 24 november 2021 16:48
  //         //bug fix product image still exist in db if upload failed
  //         $this->cpm->trans_start();
  //         $this->cpfm->delByProdukId($nation_code, $cpm_id);
  //         $this->cpm->trans_commit();
  //         $this->cpm->trans_end();
  //         //END by Donny Dennison - 24 november 2021 16:48

  //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //         die();
  //       }
  //     }else{
  //       $this->status = 1300;
  //       $this->message = 'Upload failed';
  //       $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::baru -- forceClose '.$this->status.' '.$this->message.' for '.$keyname);
  //       $this->cpm->trans_rollback();
  //       $this->cpm->trans_end();

  //       //START by Donny Dennison - 24 november 2021 16:48
  //       //bug fix product image still exist in db if upload failed
  //       $this->cpm->trans_start();
  //       $this->cpfm->delByProdukId($nation_code, $cpm_id);
  //       $this->cpm->trans_commit();
  //       $this->cpm->trans_end();
  //       //END by Donny Dennison - 24 november 2021 16:48

  //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //       die();
  //     }

  //     $cpfm_id = 4;
  //     $cpfm_last = $this->cpfm->getLastByProdukId($nation_code,$cpm_id);
  //     if(isset($cpfm_last->id)){
  //       $iq->cpfm_id = $cpfm_last->id + 1;
  //     }
  //     $img_count = 3;
  //     if (isset($iq->cpfm_id)) {
  //       $cpfm_id = $iq->cpfm_id;
  //     }
  //     if (isset($iq->img_count)) {
  //       $img_count = $iq->img_count;
  //     }

  //     $is_foto4 = 0;
  //     $is_foto4_r = 0;

  //     $keyname = 'foto4';
  //     // $ck = $this->__checkUploadedFile($keyname);
  //     // if($ck){
  //     if($this->input->post("foto4") != null){
  //       $is_foto4 = 1;
  //       // $upi = $this->__uploadImagex($nation_code, $keyname, $cpm_id, $iq->cpfm_id);
  //       $upi = $this->__moveImagex($nation_code, $this->input->post("foto4"), $cpm_id, $iq->cpfm_id);
  //       if(isset($upi->status)){
  //         if($upi->status == 200){
  //           $dpi = $this->__dataImageProduct($nation_code,$cpm_id,$cpfm_id,$upi->image,$upi->thumb,$iq->img_count);
  //           if($dpi){
  //             $iq->cpfm_id++;
  //             $iq->img_count++;
  //             $is_foto4_r = 1;
  //           }
  //         }
  //       }
  //     }

  //     $cpfm_id = 5;
  //     $cpfm_last = $this->cpfm->getLastByProdukId($nation_code,$cpm_id);
  //     if(isset($cpfm_last->id)){
  //       $iq->cpfm_id = $cpfm_last->id + 1;
  //     }
  //     $img_count = 4;
  //     if (isset($iq->cpfm_id)) {
  //       $cpfm_id = $iq->cpfm_id;
  //     }
  //     if (isset($iq->img_count)) {
  //       $img_count = $iq->img_count;
  //     }

  //     $is_foto5 = 0;
  //     $is_foto5_r = 0;

  //     $keyname = 'foto5';
  //     // $ck = $this->__checkUploadedFile($keyname);
  //     // if($ck){
  //     if($this->input->post("foto5") != null){
  //       $is_foto5 = 1;
  //       // $upi = $this->__uploadImagex($nation_code, $keyname, $cpm_id, $iq->cpfm_id);
  //       $upi = $this->__moveImagex($nation_code, $this->input->post("foto5"), $cpm_id, $iq->cpfm_id);
  //       if(isset($upi->status)){
  //         if($upi->status == 200){
  //           $dpi = $this->__dataImageProduct($nation_code,$cpm_id,$cpfm_id,$upi->image,$upi->thumb,$iq->img_count);
  //           if($dpi){
  //             $iq->cpfm_id++;
  //             $iq->img_count++;
  //             $is_foto5_r = 1;
  //           }
  //         }
  //       }
  //     }

  //     //commit and end transaction
  //     $this->cpm->trans_commit();
  //     $this->cpm->trans_end();

  //     $data['produk'] = $this->cpm->getById($nation_code, $cpm_id, $pelanggan, $pelanggan->id);
  //     $data['produk']->galeri = $this->cpfm->getByProdukId($nation_code, $cpm_id);

  //     //building product data for response
  //     $i = 0;
  //     $dix = array();
  //     $dix['nation_code'] = $nation_code;
  //     $dix['c_produk_id'] = $cpm_id;
  //     $dix['caption'] = '';
  //     $dix['is_active'] = 1;
  //     foreach ($data['produk']->galeri as &$gal) {
  //       $dix['url'] = '';
  //       $dix['url_thumb'] = '';
  //       if (isset($gal->url)) {
  //         $gal->url = str_replace("//", "/", $gal->url);
  //         if($i==0) $data['produk']->foto = $gal->url;
  //         $dix['url'] = $gal->url;
  //         $gal->url = $this->cdn_url($gal->url);
  //       }

  //       if (isset($gal->url_thumb)) {
  //         $gal->url_thumb = str_replace("//", "/", $gal->url_thumb);
  //         if($i==0) $data['produk']->thumb = $gal->url_thumb;
  //         $dix['url_thumb'] = $gal->url_thumb;
  //         $gal->url_thumb = $this->cdn_url($gal->url_thumb);
  //       }

  //       if(!empty($is_foto4) && empty($is_foto4_r) && strlen($dix['url'])>4 && strlen($dix['url_thumb'])>4 && $i==1){
  //         $cpfm_last = $this->cpfm->getLastByProdukId($nation_code,$cpm_id);
  //         if(isset($cpfm_last->id)){
  //           $dix['id'] = $cpfm_last->id+1;

  //           //by Donny Dennison - 30 november 2021 14:46
  //           //fix if foto4 or foto5 failed then copy foto2 or foto3
  //           $newURL = "$nation_code-$cpm_id-".$dix['id']. pathinfo($path, PATHINFO_EXTENSION);
  //           $newURLThumb = "$nation_code-$cpm_id-".$dix['id']."-thumb". pathinfo($path, PATHINFO_EXTENSION);
  //           copy($dix['url'],$newURL);
  //           copy($dix['url_thumb'],$newURLThumb);
  //           $dix['url'] = $newURL;
  //           $dix['url_thumb'] = $newURLThumb;

  //           $this->cpfm->set($dix);
  //           $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::baru -- INFO upload failed for foto4 replaced by foto2 SUCCESS');
  //         }
  //       }

  //       if(!empty($is_foto5) && empty($is_foto5_r) && strlen($dix['url'])>4 && strlen($dix['url_thumb'])>4 && $i==2 ){
  //         $cpfm_last = $this->cpfm->getLastByProdukId($nation_code,$cpm_id);
  //         if(isset($cpfm_last->id)){
  //           $dix['id'] = $cpfm_last->id+1;

  //           //by Donny Dennison - 30 november 2021 14:46
  //           //fix if foto4 or foto5 failed then copy foto2 or foto3
  //           $newURL = "$nation_code-$cpm_id-".$dix['id']. pathinfo($path, PATHINFO_EXTENSION);
  //           $newURLThumb = "$nation_code-$cpm_id-".$dix['id']."-thumb". pathinfo($path, PATHINFO_EXTENSION);
  //           copy($dix['url'],$newURL);
  //           copy($dix['url_thumb'],$newURLThumb);
  //           $dix['url'] = $newURL;
  //           $dix['url_thumb'] = $newURLThumb;
            
  //           $this->cpfm->set($dix);
  //           $this->seme_log->write("api_mobile", 'API_Mobile/Produk_Automotive::baru -- INFO upload failed for foto5 replaced by foto3 SUCCESS');
  //         }
  //       }

  //       $i++;
  //     }
  //     unset($dix);

  //     //update image
  //     $this->cpm->update2($nation_code,$cpm_id,array("foto"=>$data['produk']->foto,"thumb"=>$data['produk']->thumb));

  //     $data['produk']->nama = html_entity_decode($data['produk']->nama,ENT_QUOTES);
  //     $data['produk']->deskripsi = html_entity_decode($data['produk']->deskripsi,ENT_QUOTES);

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

  //     //START by Donny Dennison - 16 december 2021 15:49
  //     //get point as leaderboard rule
  //     $pelangganAddress = $this->bua->getById($nation_code, $pelanggan->id, $b_user_alamat_id);

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
  //     $di['custom_id'] = $cpm_id;
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
  //     //END by Donny Dennison - 16 december 2021 15:49
  //     // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '+', $di['point']);
  //     // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_post', '+', 1);

  //   }
  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  // }

  // public function hapus($c_produk_id="")
  // {
  //   $dt = $this->__init();
  //   $data = new stdClass();

  //   //check nation_code
  //   $nation_code = $this->input->get('nation_code');
  //   $nation_code = $this->nation_check($nation_code);
  //   if (empty($nation_code)) {
  //     $this->status = 101;
  //     $this->message = 'Missing or invalid nation_code';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //check apisess
  //   $apisess = $this->input->get('apisess');
  //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   if (!isset($pelanggan->id)) {
  //     $this->status = 401;
  //     $this->message = 'Missing or invalid API session';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $c_produk_id = (int) $c_produk_id;
  //   if ($c_produk_id<=0) {
  //     $this->status = 908;
  //     $this->message = 'Invalid Automotive product ID or Automotive Product not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $produk = $this->cpm->getById($nation_code, $c_produk_id, $pelanggan);
  //   if (!isset($produk->id)) {
  //     $this->status = 908;
  //     $this->message = 'Invalid Automotive product ID or Automotive Product not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   if ($produk->b_user_id_seller != $pelanggan->id) {
  //     $this->status = 907;
  //     $this->message = 'Access denied, you cant delete other people Automotive product';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }
  //   //get product images
  //   $images = $this->cpfm->getByProdukId($nation_code, $c_produk_id);

  //   //start transaction
  //   $this->cpm->trans_start();

  //   //check delete method
  //   if ($this->is_soft_delete) {
  //     $du = array();
  //     $du['foto'] = "media/produk/default.png";
  //     $du['thumb'] = "media/produk/default.png";
  //     $du['is_published'] = 0;
  //     $du['is_visible'] = 0;
  //     $du['is_active'] = 0;
  //     $res = $this->cpm->update($nation_code, $pelanggan->id, $c_produk_id, $du);
  //     if ($res) {
  //       $this->cpm->trans_commit();
  //       $res2 = $this->cpfm->delByProdukId($nation_code, $c_produk_id);
  //       if ($res2) {
  //         $this->cpm->trans_commit();
  //         $this->status = 200;
  //         $this->message = 'Automotive Product deleted successfully';

  //         //delete product images file
  //         if (count($images)) {
  //           foreach ($images as $img) {
  //             $fileloc = SENEROOT.$img->url;
  //             if (file_exists($fileloc)) {
  //               unlink($fileloc);
  //             }
  //             $fileloc = SENEROOT.$img->url_thumb;
  //             if (file_exists($fileloc)) {
  //               unlink($fileloc);
  //             }
  //           }
  //           unset($img);
  //         }
  //         unset($images);

  //         //remove from cart
  //         $c_produk_ids = array($c_produk_id);
  //         $this->cart->delAllByProdukIds($nation_code, $c_produk_ids);
  //         $this->cpm->trans_commit();

  //         //remove from wishlist
  //         $c_produk_ids = array($c_produk_id);
  //         $this->dwlm->delAllByProdukIds($nation_code, $c_produk_ids);
  //         $this->cpm->trans_commit();

  //         //START by Donny Dennison - 16 december 2021 15:49
  //         //decrease total post in leaderboard
  //         if($produk->is_published == 1){
            
  //           //get point
  //           $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EA");
  //           if (!isset($pointGet->remark)) {
  //             $pointGet = new stdClass();
  //             $pointGet->remark = 3;
  //           }

  //           $di = array();
  //           $di['nation_code'] = $nation_code;
  //           $di['b_user_alamat_location_kelurahan'] = $produk->kelurahan;
  //           $di['b_user_alamat_location_kecamatan'] = $produk->kecamatan;
  //           $di['b_user_alamat_location_kabkota'] = $produk->kabkota;
  //           $di['b_user_alamat_location_provinsi'] = $produk->provinsi;
  //           $di['b_user_id'] = $pelanggan->id;
  //           $di['plusorminus'] = "-";
  //           $di['point'] = $pointGet->remark;
  //           $di['custom_id'] = $c_produk_id;
  //           $di['custom_type'] = 'product';
  //           $di['custom_type_sub'] = 'post';
  //           $di['custom_text'] = $pelanggan->fnama.' has delete '.$di['custom_type'].' '.$di['custom_type_sub'].' and lose '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
  //           $this->glphm->set($di);
  //           $this->cpm->trans_commit();
  //           // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
  //           // $this->cpm->trans_commit();
  //           // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_post', '-', 1);
  //           // $this->cpm->trans_commit();

  //           $totalShareBySeller = $this->glphm->countAll($nation_code, $produk->kelurahan, $produk->kecamatan, $produk->kabkota, $produk->provinsi, $pelanggan->id, "+", $c_produk_id, "product", "share", date("Y-m-d"));

  //           if($totalShareBySeller > 0){

  //             $di = array();
  //             $di['nation_code'] = $nation_code;
  //             $di['b_user_alamat_location_kelurahan'] = $produk->kelurahan;
  //             $di['b_user_alamat_location_kecamatan'] = $produk->kecamatan;
  //             $di['b_user_alamat_location_kabkota'] = $produk->kabkota;
  //             $di['b_user_alamat_location_provinsi'] = $produk->provinsi;
  //             $di['b_user_id'] = $pelanggan->id;
  //             $di['plusorminus'] = "-";
  //             $di['point'] = $pointGet->remark * $totalShareBySeller;
  //             $di['custom_id'] = $c_produk_id;
  //             $di['custom_type'] = 'product';
  //             $di['custom_type_sub'] = 'share';
  //             $di['custom_text'] = $pelanggan->fnama.' has delete '.$di['custom_type'].' '.$di['custom_type_sub'].' and lose '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
  //             $this->glphm->set($di);
  //             $this->cpm->trans_commit();
  //             // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
  //             // $this->cpm->trans_commit();
  //           }
  //         }
  //         //END by Donny Dennison - 16 december 2021 15:49
          
  //       } else {
  //         $this->cpm->trans_rollback();
  //         $this->status = 941;
  //         $this->message = 'Failed deleting Automotive product images';
  //       }
  //     } else {
  //       $this->cpm->trans_rollback();
  //       $this->status = 940;
  //       $this->message = "Can't delete Automotive products from database, please try again later.";
  //     }
  //   } else {
  //     $res = $this->cpm->del($nation_code, $pelanggan->id, $c_produk_id);
  //     if ($res) {
  //       $this->cpm->trans_commit();
  //       //delete product images from db
  //       $res2 = $this->cpfm->delByProdukId($nation_code, $c_produk_id);
  //       if ($res2) {
  //         $this->cpm->trans_commit();
  //         $this->status = 200;
  //         $this->message = 'Automotive Product deleted successfully';
  //         //delete product images file
  //         if (count($images)) {
  //           foreach ($images as $img) {
  //             $fileloc = SENEROOT.$img->url;
  //             if (file_exists($fileloc)) {
  //               unlink($fileloc);
  //             }
  //             $fileloc = SENEROOT.$img->url_thumb;
  //             if (file_exists($fileloc)) {
  //               unlink($fileloc);
  //             }
  //           }
  //         }
  //         $fileloc = SENEROOT.$produk->foto;
  //         if ($produk->foto != 'default.png' && (!is_dir($fileloc)) && file_exists($fileloc)) {
  //           unlink($fileloc);
  //         }
  //         $fileloc = SENEROOT.$produk->thumb;
  //         if ($produk->foto != 'default.png' && (!is_dir($fileloc)) && file_exists($fileloc)) {
  //           unlink($fileloc);
  //         }
  //       } else {
  //         $this->cpm->trans_rollback();
  //         $this->status = 941;
  //         $this->message = 'Failed deleting Automotive product images';
  //       }
  //       //end delete file images;
  //     } else {
  //       $this->cpm->trans_rollback();
  //       $this->status = 940;
  //       $this->message = "Can't delete Automotive products from database, please try again later.";
  //     }
  //   }
  //   //finish transaction
  //   $this->cpm->trans_end();

  //   //render output
  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  // }

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
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //check apisess
  //   $apisess = $this->input->get('apisess');
  //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   if (!isset($pelanggan->id)) {
  //     $this->status = 401;
  //     $this->message = 'Missing or invalid API session';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $c_produk_id = (int) $c_produk_id;
  //   if ($c_produk_id<=0) {
        // $this->status = 908;
        // $this->message = 'Invalid Automotive product ID or Automotive Product not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $produk = $this->cpm->getById($nation_code, $c_produk_id);
  //   if (!isset($produk->id)) {
        // $this->status = 908;
        // $this->message = 'Invalid Automotive product ID or Automotive Product not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   if ($produk->b_user_id_seller != $pelanggan->id) {
  //     $this->status = 907;
  //     $this->message = 'Access denied, you cant change other people product';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }
  //   if (!isset($_FILES[$keyname])) {
  //     $this->status = 1300;
  //     $this->message = 'Upload failed';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }
  //   if ($_FILES[$keyname]['size']<=0) {
  //     $this->status = 1301;
  //     $this->message = 'File upload failed.';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }
  //   if ($_FILES[$keyname]['size']>=2500000) {
  //     $this->status = 1302;
  //     $this->message = 'Image file Size too big';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }
  //   if (mime_content_type($_FILES[$keyname]['tmp_name'])=="image/webp") {
  //     $this->status = 1303;
  //     $this->message = 'WebP image file format is not supported.';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }
  //   if (mime_content_type($_FILES[$keyname]['tmp_name'])=="image/webp") {
  //     $this->status = 1304;
  //     $this->message = 'WebP image file format is not supported.';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
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
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
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
  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  // }
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
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //check apisess
  //   $apisess = $this->input->get('apisess');
  //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   if (!isset($pelanggan->id)) {
  //     $this->status = 401;
  //     $this->message = 'Missing or invalid API session';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $c_produk_id = (int) $c_produk_id;
  //   if ($c_produk_id<=0) {
        // $this->status = 908;
        // $this->message = 'Invalid Automotive product ID or Automotive Product not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $produk = $this->cpm->getById($nation_code, $c_produk_id);
  //   if (!isset($produk->id)) {
        // $this->status = 908;
        // $this->message = 'Invalid Automotive product ID or Automotive Product not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   if ($produk->b_user_id_seller != $pelanggan->id) {
  //     $this->status = 907;
  //     $this->message = 'Access denied, you cant change other people product';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $c_produk_foto_id = (int) $c_produk_foto_id;
  //   if ($c_produk_foto_id<=0) {
  //     $this->status = 906;
  //     $this->message = 'Invalid ID Product image, please check again';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
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

  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  // }

  // public function image_default($c_produk_id, $c_produk_foto_id)
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
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //check apisess
  //   $apisess = $this->input->get('apisess');
  //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   if (!isset($pelanggan->id)) {
  //     $this->status = 401;
  //     $this->message = 'Missing or invalid API session';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $c_produk_id = (int) $c_produk_id;
  //   if ($c_produk_id<=0) {
        // $this->status = 908;
        // $this->message = 'Invalid Automotive product ID or Automotive Product not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $produk = $this->cpm->getById($nation_code, $c_produk_id);
  //   if (!isset($produk->id)) {
        // $this->status = 908;
        // $this->message = 'Invalid Automotive product ID or Automotive Product not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   if ($produk->b_user_id_seller != $pelanggan->id) {
  //     $this->status = 907;
  //     $this->message = 'Access denied, you cant change other people product';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $c_produk_foto_id = (int) $c_produk_foto_id;
  //   if ($c_produk_foto_id<=0) {
  //     $this->status = 906;
  //     $this->message = 'Invalid ID Product image, please check again';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //$this->debug($pelanggan);
  //   //die();
  //   $galeri = $this->cpfm->getByIdProdukId($nation_code, $produk->id, $c_produk_foto_id);
  //   if (!isset($galeri->url)) {
  //     $this->status = 961;
  //     $this->message = 'Invalid ID Product image';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }
  //   $du = array();
  //   $du['foto'] = $galeri->url;
  //   $du['thumb'] = $galeri->url_thumb;
  //   $res = $this->cpm->update($nation_code, $pelanggan->id, $c_produk_id, $du);
  //   if ($res) {
  //     $this->status = 200;
  //     $this->message = 'Succcess';
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
  //     $this->status = 960;
  //     $this->message = 'Failed to change a default image.';
  //   }
  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  // }
  // public function shipment_check()
  // {
  //   $data = array();
  //   $data['courier_services'] = array();
  //   $this->status = 200;
  //   $this->message = 'success';

  //   //populating input
  //   $berat = (float) $this->input->post('berat');
  //   $panjang = (float) $this->input->post('dimension_long');
  //   $lebar = (float) $this->input->post('dimension_width');
  //   $tinggi = (float) $this->input->post('dimension_height');
  //   $direct_delivery = (int) $this->input->post('direct_delivery');
  //   $this->seme_log->write("api_mobile", "Produk::shipment_check() -> POST: ".json_encode($_POST));

  //   $sc = $this->__shipment_check($berat, $panjang, $lebar, $tinggi, $direct_delivery);
  //   $data['courier_services'] = $sc->courier_services;

  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  // }
  // public function hapus_banyak()
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
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //check apisess
  //   $apisess = $this->input->get('apisess');
  //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   if (!isset($pelanggan->id)) {
  //     $this->status = 401;
  //     $this->message = 'Missing or invalid API session';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //init
  //   $pids = array();

  //   //collect input
  //   $c_produk_ids = $this->input->post("c_produk_ids");
  //   if (empty($c_produk_ids)) {
  //     $c_produk_ids = $this->input->post("c_produk_id");
  //   }
  //   $pos = strpos($c_produk_ids, ',');
  //   if ($pos === false) {
  //     $c_produk_ids = (int) $c_produk_ids;
  //     if ($c_produk_ids>0) {
  //       $pids[] = $c_produk_ids;
  //     }
  //     unset($c_produk_ids); //freed up some memory
  //   } else {
  //     $temp = explode(",", $c_produk_ids);
  //     foreach ($temp as $t) {
  //       if (!empty($t)) {
  //         $pids[] = $t;
  //       }
  //     }
  //     unset($t); //freed up some memory
  //     unset($temp); //freed up some memory
  //   }
  //   if (count($pids)<0) {
  //     $this->status = 963;
  //     $this->message = 'Please input at least one id product';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }
  //   //start transaction
  //   $this->cpm->trans_start();

  //   //get product by seller
  //   $products = $this->cpm->getActiveByUserIdAndIds($nation_code, $pelanggan->id, $pids);
  //   if (count($products)<=0) {
  //     $this->status = 964;
  //     $this->message = 'Produk ID(s) not found or not belong to you';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }
  //   //get your PIDS
  //   $pids = array();
  //   foreach ($products as $product) {
  //     $pids[] = $product->id;
  //   }

  //   //get product images
  //   $images = $this->cpfm->getByProdukIds($nation_code, $pids);

  //   //check delete method
  //   if ($this->is_soft_delete) {
  //     $du = array();
  //     $du['foto'] = "media/produk/default.png";
  //     $du['thumb'] = "media/produk/default.png";
  //     $du['is_published'] = 0;
  //     $du['is_visible'] = 0;
  //     $du['is_active'] = 0;
  //     $res = $this->cpm->updateMass($nation_code, $pelanggan->id, $pids, $du);
  //     if ($res) {
  //       $this->cpm->trans_commit();
  //       $this->status = 200;
  //       $this->message = 'Success';

  //       //deleting product images
  //       $this->cpfm->delByProdukIds($nation_code, $pids);
  //       $this->cpm->trans_commit();

  //       //delete image files
  //       if (count($images)) {
  //         foreach ($images as $img) {
  //           $fileloc = SENEROOT.$img->url;
  //           if (file_exists($fileloc)) {
  //             unlink($fileloc);
  //           }
  //           $fileloc = SENEROOT.$img->url_thumb;
  //           if (file_exists($fileloc)) {
  //             unlink($fileloc);
  //           }
  //         }
  //       }

  //       //remove from cart
  //       $this->cart->delAllByProdukIds($nation_code, $pids);
  //       $this->cpm->trans_commit();

  //       //remove from wishlist
  //       $this->dwlm->delAllByProdukIds($nation_code, $pids);
  //       $this->cpm->trans_commit();
  //     } else {
  //       $this->cpm->trans_rollback();
  //       $this->status = 964;
  //       $this->message = 'Deleting data failed, please try again';
  //     }
  //   } else {
  //     //delete
  //     $res = $this->cpm->deleteMass($nation_code, $pelanggan->id, $pids);
  //     if ($res) {
  //       $this->cpm->trans_commit();
  //       $this->status = 200;
  //       $this->message = 'Success';

  //       //deleting product images
  //       $this->cpfm->delByProdukIds($nation_code, $pids);
  //       $this->cpm->trans_commit();

  //       //delete image files
  //       if (count($images)) {
  //         foreach ($images as $img) {
  //           $fileloc = SENEROOT.$img->url;
  //           if (file_exists($fileloc)) {
  //             unlink($fileloc);
  //           }
  //           $fileloc = SENEROOT.$img->url_thumb;
  //           if (file_exists($fileloc)) {
  //             unlink($fileloc);
  //           }
  //         }
  //       }

  //       //remove from cart
  //       $this->cart->delAllByProdukIds($nation_code, $pids);
  //       $this->cpm->trans_commit();

  //       //remove from wishlist
  //       $this->dwlm->delAllByProdukIds($nation_code, $pids);
  //       $this->cpm->trans_commit();
  //     } else {
  //       $this->cpm->trans_rollback();
  //       $this->status = 965;
  //       $this->message = 'Deleting data failed, please try again';
  //     }
  //   }

  //   //finish transaction
  //   $this->cpm->trans_end();

  //   //render output
  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  // }

  // public function edit()
  // {
  //   //init
  //   $dt = $this->__init();

  //   //default response
  //   $data = array();
  //   $data['produk'] = new stdClass();

  //   // $this->seme_log->write("api_mobile", "Produk::edit -> ".json_encode($_POST));

  //   //check nation_code
  //   $nation_code = $this->input->get('nation_code');
  //   $nation_code = $this->nation_check($nation_code);
  //   if (empty($nation_code)) {
  //     $this->status = 101;
  //     $this->message = 'Missing or invalid nation_code';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //check apisess
  //   $apisess = $this->input->get('apisess');
  //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   if (!isset($pelanggan->id)) {
  //     $this->status = 401;
  //     $this->message = 'Missing or invalid API session';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $c_produk_id = (int) $this->input->post("c_produk_id");
  //   if ($c_produk_id<=0) {
  //     $this->status = 908;
  //     $this->message = 'Invalid Automotive product ID or Automotive Product not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $produk = $this->cpm->getById($nation_code, $c_produk_id, $pelanggan);
  //   if (!isset($produk->id)) {
  //     $this->status = 908;
  //     $this->message = 'Invalid Automotive product ID or Automotive Product not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   if ($produk->b_user_id_seller != $pelanggan->id) {
  //     $this->status = 907;
  //     $this->message = 'Access denied, you cant change other people Automotive product';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //sanitize post
  //   foreach ($_POST as $key=>&$val) {
  //     if (is_string($val)) {
  //       if ($key == 'deskripsi') {
  //         $val = $this->seme_purifier->richtext($val);
  //       } else {
  //         $val = $this->__f($val);
  //       }
  //     }
  //   }

  //   //populating input
  //   $b_user_alamat_id = (int) $this->input->post("b_user_alamat_id");
  //   $b_kategori_id = trim($this->input->post("b_kategori_id"));
  //   // $b_berat_id = (int) $this->input->post("b_berat_id");
  //   // $b_kondisi_id = (int) $this->input->post("b_kondisi_id");
  //   $brand =  $this->input->post("brand");
  //   $nama =  $this->input->post("nama");
  //   $harga_jual = $this->input->post("harga_jual");
  //   $deskripsi_singkat = $this->input->post('deskripsi_singkat');
  //   $deskripsi = $this->input->post("deskripsi");
  //   // $dimension_long = $this->input->post("dimension_long");
  //   // $dimension_width = $this->input->post("dimension_width");
  //   // $dimension_height = $this->input->post("dimension_height");
  //   // $vehicle_types = $this->input->post("vehicle_types");
  //   // $courier_services = $this->input->post("courier_services");
  //   // $services_duration = $this->input->post("services_duration");
  //   // $berat = $this->input->post("berat");
  //   $satuan = $this->input->post("satuan");
  //   $stok = (int) $this->input->post("stok");
  //   // $is_include_delivery_cost = (int) $this->input->post("is_include_delivery_cost");
  //   // $is_published = (int) $this->input->post("is_published");
  //   $model = $this->input->post("model");
  //   $color = $this->input->post("color");
  //   $year = $this->input->post("year");

  //   //validation
  //   if ($b_kategori_id == 'car') {
  //     $b_kategori_id = 32;
  //   }else if($b_kategori_id == 'motorcycle'){
  //     $b_kategori_id = 33;
  //   }
  //   // if ($b_berat_id<=0) {
  //   //   $b_berat_id = 0;
  //   // }
  //   // if ($b_kondisi_id<=0) {
  //   //   $b_kondisi_id = 0;
  //   // }
  //   if ($harga_jual<=0) {
  //     $harga_jual = 0;
  //   }
  //   if (empty($nama)) {
  //     $nama = "";
  //   }
  //   if (empty($deskripsi)) {
  //     $deskripsi = "";
  //   }
  //   // $is_include_delivery_cost = !empty($is_include_delivery_cost) ? 1:0;
  //   // $is_published = !empty($is_published) ? 1:0;
  //   // if (strtolower($services_duration) == 'sameday' || strtolower($services_duration) == 'same day') {
  //   //   $services_duration = 'Same Day';
  //   // }
  //   // if (strtolower($services_duration) == 'nextday' || strtolower($services_duration) == 'next day') {
  //   //   $services_duration = 'Next Day';
  //   // }
  //   // if(strtolower($courier_services) == 'qxpress' && !empty($is_include_delivery_cost)){
  //   //   $is_include_delivery_cost = 0;
  //   // }

  //   $b_user_alamat_id = $produk->b_user_alamat_id;

  //   //b_user_alamat_id
  //   if ($b_user_alamat_id<=0) {
  //     $this->status = 1099;
  //     $this->message = 'Invalid b_user_alamat_id';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $almt = $this->bua->getByIdUserId($nation_code, $pelanggan->id, $b_user_alamat_id);
  //   if (!isset($almt->id)) {
  //     $this->status = 916;
  //     $this->message = 'Please choose pickup address';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $kat = $this->bkm3->getById($nation_code, $b_kategori_id);
  //   if (!isset($kat->id)) {
  //     $this->status = 917;
  //     $this->message = 'Please choose automotive product category';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   // $kon = $this->bkon->getById($nation_code, $b_kondisi_id);
  //   // if (!isset($kon->id)) {
  //   //   $this->status = 919;
  //   //   $this->message = 'Please choose product condition';
  //   //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //   //   die();
  //   // }

  //   if (strlen($nama)<=0) {
  //     $this->status = 910;
  //     $this->message = 'Product name is required';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   if ($harga_jual<=0) {
  //     $this->status = 911;
  //     $this->message = 'Price is required';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   // if ($berat<=0) {
  //   //   $this->status = 912;
  //   //   $this->message = 'Please specify the product weight correctly';
  //   //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //   //   die();
  //   // }
  //   //recasting weight
  //   // $berat = $this->__floatWeight($berat);

  //   // if ($dimension_long<=0) {
  //   //   $this->status = 913;
  //   //   $this->message = 'Invalid product long correctly';
  //   //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //   //   die();
  //   // }

  //   // if ($dimension_height<=0) {
  //   //   $this->status = 914;
  //   //   $this->message = 'Invalid product height correctly';
  //   //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //   //   die();
  //   // }

  //   // if ($dimension_width<=0) {
  //   //   $this->status = 915;
  //   //   $this->message = 'Invalid product width correctly';
  //   //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //   //   die();
  //   // }

  //   //by Donny Dennison - 13-07-2020 16:08
  //   //edit stok allow 0 and if edit stok more than 0 then change cdate to newest
  //   // if ($stok<=0) {
  //   //   $this->status = 916;
  //   //   $this->message = 'Please input atleast one product stock quantity';
  //   //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //   //   die();
  //   // }

  //   //check dimension
  //   // $dimension_max = $this->__getDimensionMax($dimension_long, $dimension_width, $dimension_height);
  //   // if ($dimension_max>724) {
  //   //   $this->status = 917;
  //   //   $this->message = 'Product too big, we currently unsupported product with dimension above 7,2 m';
  //   //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //   //   die();
  //   // }

  //   //by Donny Dennison 24 february 2021 18:45
  //   //change ’ to ' in add & edit product name and description
  //   $nama = str_replace('’',"'",$nama);
  //   $deskripsi = str_replace('’',"'",$deskripsi);

  //   //by Donny Dennison 16 augustus 2020 00:25
  //   //fix check emoji in insert & edit product and discussion
  //   // if( preg_match( $this->unicodeRegexp, $nama ) ){

  //   //   $this->status = 1104;
  //   //   $this->message = 'Symbol image(ex.Emoji..) is not allowed in Automotive Product Name or Description';
  //   //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //   //   die();

  //   // }else if( preg_match( $this->unicodeRegexp, $deskripsi ) ){

  //   //   $this->status = 1104;
  //   //   $this->message = 'Symbol image(ex.Emoji..) is not allowed in Automotive Product Name or Description';
  //   //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //   //   die();

  //   // }

  //   //sanitize input
  //   // $nama = filter_var($nama, FILTER_SANITIZE_STRING);
  //   // $brand = filter_var($brand, FILTER_SANITIZE_STRING);
  //   // $deskripsi_singkat = filter_var($deskripsi_singkat, FILTER_SANITIZE_STRING);
  //   // $deskripsi = filter_var($deskripsi, FILTER_SANITIZE_STRING);
  //   $deskripsi = nl2br($deskripsi);

  //   //by Donny Dennison - 15 augustus 2020 15:09
  //   //bug fix \n (enter) didnt get remove
  //   $deskripsi = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $deskripsi);

  //   //check shipments
  //   // $shipments_check = $this->__shipment_check($berat, $dimension_long, $dimension_width, $dimension_height);

  //   //start transaction
  //   $this->cpm->trans_start();

  //   //updating to database
  //   $du = array();
  //   $du['b_user_alamat_id'] = $b_user_alamat_id;
  //   $du['b_kategori_id'] = $b_kategori_id;
  //   // $du['b_berat_id'] = $b_berat_id;
  //   // $du['b_kondisi_id'] = $b_kondisi_id;
  //   $du['brand'] = $brand;
  //   $du['nama'] = $nama;
  //   $du['harga_jual'] = $harga_jual;
  //   $du['deskripsi'] = $deskripsi;
  //   // $du['berat'] = $berat;
  //   // $du['satuan'] = $satuan;
  //   $du['stok'] = $stok;
  //   // $du['dimension_width'] = $dimension_width;
  //   // $du['dimension_height'] = $dimension_height;
  //   // $du['dimension_long'] = $dimension_long;
  //   // $du['courier_services'] = $courier_services;
  //   // $du['vehicle_types'] = $vehicle_types;
  //   // $du['services_duration'] = $services_duration;
  //   // $du['is_include_delivery_cost'] = $is_include_delivery_cost;
  //   // $du['is_published'] = $is_published;

  //   //by Donny Dennison - 13-07-2020 16:08
  //   //edit stok allow 0 and if edit stok more than 0 then change cdate to newest
  //   if($produk->stok != $stok && $stok > 0){
  //       $du['cdate'] = 'NOW()'; 
  //   }

  //   //by Donny Dennison - 28 july 2020 10:25
  //   //if edit price then change cdate to newest
  //   if($produk->harga_jual != number_format($harga_jual, 2, '.', '') && $harga_jual > 0){
  //       $du['cdate'] = 'NOW()'; 
  //   }
    
  //   $du['alamat2'] = $almt->alamat2;
  //   $di['kelurahan'] = $almt->kelurahan;
  //   $di['kecamatan'] = $almt->kecamatan;
  //   $di['kabkota'] = $almt->kabkota;
  //   $di['provinsi'] = $almt->provinsi;
  //   $du['kodepos'] = $almt->kodepos;
  //   $du['latitude'] = $almt->latitude;
  //   $du['longitude'] = $almt->longitude;

  //   $res = $this->cpm->update($nation_code, $pelanggan->id, $c_produk_id, $du);

  //   //start transaction automotive detail
  //   $du = array();
  //   $du['model'] = $model;
  //   $du['color'] = $color;
  //   $du['year'] = $year;

  //   $res2 = $this->cpdam->update($nation_code, $c_produk_id, $du);

  //   if ($res && $res2) {
  //     $this->cpm->trans_commit();
  //     $cpm_id = $c_produk_id;
  //     $this->status = 200;
  //     // $this->message = 'Automotive Product edited successfully';
  //     $this->message = 'Success';

  //     $listUrl = array();
  //     $listUpload = array();

  //     $totalFoto = 0;
  //     $checkFileExist = 1;
  //     //looping for get list of url
  //     for ($i=1; $i < 6; $i++) {

  //       $file_path = parse_url($this->input->post('foto'.$i), PHP_URL_PATH);

  //       if (strpos($file_path, 'temporary') !== false) {

  //         $listUpload = array_merge($listUpload, array($file_path));
  //         $totalFoto++;

  //         if (!file_exists(SENEROOT.$file_path)) {
  //           $checkFileExist = 0;
  //         }

  //       }else if($this->input->post('foto'.$i) != null){

  //         $listUrl = array_merge($listUrl, array(substr($file_path, 1)));
  //         $totalFoto++;
        
  //       }
      
  //     }

  //     if ($totalFoto < 3) {
  //       $this->status = 1300;
  //       $this->message = 'Upload failed';
  //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //       die();
  //     }

  //     if ($checkFileExist == 0) {
  //       $this->status = 995;
  //       $this->message = 'Failed upload, temporary already gone';
  //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //       die();
  //     }

  //     //delete image that is not in array
  //     $galeri = $this->cpfm->getByProdukId($nation_code, $c_produk_id);
  //     foreach ($galeri as $gal) {

  //       if (!in_array($gal->url, $listUrl) || empty($listUrl)) {

  //         $this->cpfm->delByIdProdukId($nation_code, $gal->id, $c_produk_id);
          
  //         if (strlen($gal->url)>4) {
  //           $file = SENEROOT.$gal->url;
  //           if (!is_dir($file) && file_exists($file)) {
  //             unlink($file);
  //           }
  //         }

  //         if (strlen($gal->url_thumb)>4) {
  //           $file = SENEROOT.$gal->url_thumb;
  //           if (!is_dir($file) && file_exists($file)) {
  //             unlink($file);
  //           }
  //         }

  //       }

  //     }

  //     if(!empty($listUpload)){
      
  //       //upload image and insert to c_product_foto table
  //       foreach ($listUpload as $key => $upload) {
          
  //         $photoId_last = $this->cpfm->getLastByProdukId($nation_code,$c_produk_id);

  //         if(!isset($photoId_last->id)){
  //           $photoId_last->id = 1;
  //         }else{
  //           $photoId_last->id += 1;

  //         }

  //         // $sc = $this->__uploadImagex($nation_code, $upload, $c_produk_id, $photoId_last->id);
  //         $sc = $this->__moveImagex($nation_code, $upload, $c_produk_id, $photoId_last->id);
  //         if (isset($sc->status)) {
  //           if ($sc->status==200) {
  //             $this->cpm->trans_commit();
                
  //             $dix = array();
  //             $dix['nation_code'] = $nation_code;
  //             $dix['c_produk_id'] = $c_produk_id;
  //             $dix['id'] = $photoId_last->id;
  //             $dix['url'] = $sc->image;
  //             $dix['url_thumb'] = $sc->thumb;
  //             $dix['is_active'] = 1;
  //             $dix['caption'] = '';
  //             $this->cpfm->set($dix);

  //             $this->cpm->trans_commit();
                
  //           }
  //         }

  //       }

  //     }

  //     //update cover image
  //     $i=0;
  //     $galeri = $this->cpfm->getByProdukId($nation_code, $c_produk_id);
  //     foreach ($galeri as &$gal) {
  //       if ($i==0) {
  //         if (isset($gal->url)) {
  //           if (strlen($gal->url)>4) {
  //             $this->cpm->update($nation_code, $pelanggan->id, $c_produk_id, array("foto"=>$gal->url,"thumb"=>$gal->url_thumb));
  //             $this->cpm->trans_commit();
  //             if ($this->is_log) {
  //               $this->seme_log->write("api_mobile", "API_Mobile/Produk_Automotive::index --updateProductImageCover $gal->url DONE");
  //             }
  //             $i++;
  //           }
  //         }
  //       }
  //     }

  //     //START by Donny Dennison - 16 december 2021 15:49
  //     //get point as leaderboard rule
  //     $pelangganAddress = $this->bua->getById($nation_code, $pelanggan->id, $b_user_alamat_id);

  //     $checkAlreadyInleaderBoardHistory = $this->glphm->checkAlreadyInDB($nation_code, $pelangganAddress->kelurahan, $pelangganAddress->kecamatan, $pelangganAddress->kabkota, $pelangganAddress->provinsi, $pelanggan->id, $c_produk_id, 'product', 'post');
      
  //     if(!isset($checkAlreadyInleaderBoardHistory->b_user_id)){

  //       //get point
  //       $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EA");
  //       if (!isset($pointGet->remark)) {
  //         $pointGet = new stdClass();
  //         $pointGet->remark = 3;
  //       }

  //       $di = array();
  //       $di['nation_code'] = $nation_code;
  //       $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
  //       $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
  //       $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
  //       $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
  //       $di['b_user_id'] = $pelanggan->id;
  //       $di['point'] = $pointGet->remark;
  //       $di['custom_id'] = $c_produk_id;
  //       $di['custom_type'] = 'product';
  //       $di['custom_type_sub'] = 'post';
  //       $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
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
  //       $this->cpm->trans_commit();
  //       // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '+', $di['point']);
  //       // $this->cpm->trans_commit();
  //       // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_post', '+', 1);
  //       // $this->cpm->trans_commit();
  //     }
  //     //END by Donny Dennison - 16 december 2021 15:49

  //     //get images
  //     $data['produk'] = $this->cpm->getById($nation_code, $c_produk_id, $pelanggan, $pelanggan->id);
  //     $data['produk']->galeri = $galeri;
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

  //     $data['produk']->nama = html_entity_decode($data['produk']->nama,ENT_QUOTES);
  //     $data['produk']->deskripsi = html_entity_decode($data['produk']->deskripsi,ENT_QUOTES);
      
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
  //     $this->cpm->trans_rollback();
  //     $this->status = 990;
  //     $this->message = 'Cant edit automotive product from database, please try again later';
  //   }
  //   //finish transaction
  //   $this->cpm->trans_end();

  //   //render output
  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  // }

  // public function kalkulasi()
  // {
  //   $dt = $this->__init(); //init

  //   //default response
  //   $data = array();
  //   $data['harga_jual'] = 0.0;
  //   $data['biaya'] = new stdClass();
  //   $data['biaya']->admin = 0.0;
  //   $data['biaya']->asuransi = 0.0;
  //   $data['biaya']->admin_teks = "0";
  //   $data['pendapatan'] = 0.0;
  //   //$data['debug'] = new stdClass();

  //   //convert to string
  //   $data['harga_jual'] = strval($data['harga_jual']);
  //   $data['biaya']->admin = strval($data['biaya']->admin);
  //   $data['biaya']->asuransi = strval($data['biaya']->asuransi);
  //   $data['pendapatan'] = strval($data['pendapatan']);

  //   if ($this->is_log) {
  //     $this->seme_log->write("api_mobile", "Produk::kalkulasi -> ".json_encode($_POST));
  //   }

  //   //check nation_code
  //   $nation_code = $this->input->get('nation_code');
  //   $nation_code = $this->nation_check($nation_code);
  //   if (empty($nation_code)) {
  //     $this->status = 101;
  //     $this->message = 'Missing or invalid nation_code';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //check apisess
  //   $apisess = $this->input->get('apisess');
  //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   if (!isset($pelanggan->id)) {
  //     $this->status = 401;
  //     $this->message = 'Missing or invalid API session';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $harga_jual = (float) $this->input->post("harga_jual");
  //   $harga_jual = round($harga_jual, 2);
  //   $data['harga_jual'] = $harga_jual;

  //   $berat = $this->input->post("berat");
  //   $berat = round($berat, 2);

  //   //declare and get variable
  //   $selling_fee_percent = 0;

  //   //get preset from DB
  //   $fee = array();
  //   $presets = $this->ccm->getByClassified($nation_code, "product_fee");
  //   if (count($presets)>0) {
  //     foreach ($presets as $pre) {
  //       $fee[$pre->code] = $pre;
  //     }
  //     unset($pre); //free some memory
  //     unset($presets); //free some memory
  //   }

  //   //passing into current var
  //   if (isset($fee['F7']->remark)) {
  //     $selling_fee_percent = $fee['F7']->remark;
  //   } //insurance deduction type

  //   //calculating Earning Total
  //   $selling_fee = round($harga_jual * ($selling_fee_percent/100), 2);
  //   $data['pendapatan'] = round($harga_jual - $selling_fee, 2);
  //   $admin = round($harga_jual - $data['pendapatan'], 2);

  //   //render output
  //   $this->status = 200;
  //   $this->message = 'Success';
  //   $data['harga_jual'] = strval($data['harga_jual']);
  //   $data['biaya']->admin = strval($admin);
  //   $data['biaya']->asuransi = strval(0.0);
  //   $data['biaya']->admin_teks = strval($selling_fee_percent);
  //   $data['pendapatan'] = strval($data['pendapatan']);
  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  // }

  // //by Donny Dennison - 3 august 2020 16:25
  // // add QnA / discussion feature
  // private function __sortColDiscussion($sort_col, $tbl_as, $tbl2_as)
  // {
  //   switch ($sort_col) {
  //     case 'cdate':
  //     $sort_col = "$tbl_as.cdate";
  //     break;
  //     default:
  //     $sort_col = "$tbl_as.cdate";
  //   }
  //   return $sort_col;
  // }

  // //by Donny Dennison - 3 august 2020 16:25
  // // add QnA / discussion feature
  // public function list_discussion()
  // {
  //   //initial
  //   $dt = $this->__init();

  //   //default result
  //   $data = array();
  //   // $data['is_login'] = "0";
  //   // $data['is_saved'] = "0";
  //   $data['diskusi_total'] = 0;
  //   $data['diskusis'] = array();

  //   //check nation_code
  //   $nation_code = $this->input->get('nation_code');
  //   $nation_code = $this->nation_check($nation_code);
  //   if (empty($nation_code)) {
  //     $this->status = 101;
  //     $this->message = 'Missing or invalid nation_code';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $pelanggan = new stdClass();
  //   $apisess = $this->input->get('apisess');
  //   if (strlen($apisess)>3) {
  //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   }
  //   if (!isset($pelanggan->id)) {
  //     $pelanggan = new stdClass();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //populate input get
  //   $sort_col = $this->input->get("sort_col");
  //   $sort_dir = $this->input->get("sort_dir");
  //   $page = $this->input->get("page");
  //   $page_size = $this->input->get("page_size");
  //   // $grid = $this->input->get("grid");
  //   // $keyword = trim($this->input->get("keyword"));
  //   //$kategori_id = $this->input->get("kategori_id");
  //   $parent_discussion_id = (int) $this->input->get("parent_discussion_id");
  //   $product_id = (int) $this->input->get("product_id");
  //   if ($parent_discussion_id<=0) {
  //     $parent_discussion_id = 0;
  //   }
  //   if ($product_id<=0) {
  //     $product_id = 0;
  //   }

  //   // $kategori_id = ''; //not used
  //   // if (empty($kategori_id)) {
  //   //   $kategori_id="";
  //   // }

  //   // $harga_jual_mulai = (int)$this->input->get("harga_jual_mulai");
  //   // $harga_jual_sampai = (int) $this->input->get("harga_jual_sampai");

  //   //sanitize input
  //   $tbl_as = $this->fdis->getTblAs();
  //   $tbl2_as = $this->fdis->getTbl2As();
  //   $sort_col = $this->__sortColDiscussion($sort_col, $tbl_as, $tbl2_as);
  //   $sort_dir = $this->__sortDir($sort_dir);
  //   $page = $this->__page($page);
  //   $page_child_discussion = $this->__page(1);
  //   $page_size = $this->__pageSize($page_size);
  //   $page_size_child_discussion = $this->__pageSize(1);

  //   //keyword
  //   // if (mb_strlen($keyword)>1) {
  //   //   //$keyword = utf8_encode(trim($keyword));
  //   //   $enc = mb_detect_encoding($keyword, 'UTF-8');
  //   //   if ($enc == 'UTF-8') {
  //   //   } else {
  //   //       $keyword = iconv($enc, 'ISO-8859-1//TRANSLIT', $keyword);
  //   //   }
  //   // } else {
  //   //   $keyword="";
  //   // }
  //   // $keyword = filter_var(strip_tags($keyword), FILTER_SANITIZE_SPECIAL_CHARS);
  //   // $keyword = substr($keyword, 0, 32);
  //   // if (isset($pelanggan->id)) {
  //   //   $data['is_login'] = "1";
  //   //   $bupm = $this->bupw->check($nation_code, $pelanggan->id, $keyword);
  //   //   if (isset($bupm->nation_code)) {
  //   //     $data['is_saved'] = "1";
  //   //   }
  //   //   if ($this->is_log) {
  //   //     $this->seme_log->write("api_mobile", "API_Mobile/Produ::index -> keyword lookup done for USERID: ".$pelanggan->id.", KEYWORD: ".$keyword.", SAVED: ".$data['is_saved']);
  //   //   }
  //   // }

  //   //advanced filter
  //   // $harga_jual_min = '';
  //   // if (isset($_GET['harga_jual_min'])) {
  //   //   $harga_jual_min = (int) $_GET['harga_jual_min'];
  //   //   if ($harga_jual_min<=-1) {
  //   //     $harga_jual_min = '';
  //   //   }
  //   // }
  //   // if ($harga_jual_min>0) {
  //   //   $harga_jual_min = (float) $harga_jual_min;
  //   // }

  //   // $harga_jual_max = (int) $this->input->get("harga_jual_max");
  //   // if ($harga_jual_max<=0) {
  //   //   $harga_jual_max = "";
  //   // }
  //   // if ($harga_jual_max>0) {
  //   //   $harga_jual_max = (float) $harga_jual_max;
  //   // }

  //   // $b_kondisi_ids = "";
  //   // if (isset($_GET['b_kondisi_ids'])) {
  //   //   $b_kondisi_ids = $_GET['b_kondisi_ids'];
  //   // }
  //   // if (strlen($b_kondisi_ids)>0) {
  //   //   $b_kondisi_ids = rtrim($b_kondisi_ids, ",");
  //   //   $b_kondisi_ids = explode(",", $b_kondisi_ids);
  //   //   if (count($b_kondisi_ids)) {
  //   //     $kons = array();
  //   //     foreach ($b_kondisi_ids as &$bks) {
  //   //       $bks = (int) trim($bks);
  //   //       if ($bks>0) {
  //   //         $kons[] = $bks;
  //   //       }
  //   //     }
  //   //     $b_kondisi_ids = $kons;
  //   //   } else {
  //   //     $b_kondisi_ids = array();
  //   //   }
  //   // } else {
  //   //   $b_kondisi_ids = array();
  //   // }

  //   // $b_kategori_ids = "";
  //   // if (isset($_GET['b_kategori_ids'])) {
  //   //   $b_kategori_ids = $_GET['b_kategori_ids'];
  //   // }
  //   // if (strlen($b_kategori_ids)>0) {
  //   //   $b_kategori_ids = rtrim($b_kategori_ids, ",");
  //   //   $b_kategori_ids = explode(",", $b_kategori_ids);
  //   //   if (count($b_kategori_ids)) {
  //   //     $kods = array();
  //   //     foreach ($b_kategori_ids as &$bki) {
  //   //       $bki = (int) trim($bki);
  //   //       if ($bki>0) {
  //   //         $kods[] = $bki;
  //   //       }
  //   //     }
  //   //     $b_kategori_ids = $kods;
  //   //   } else {
  //   //     $b_kategori_ids = array();
  //   //   }
  //   // } else {
  //   //   $b_kategori_ids = array();
  //   // }

  //   // $kecamatan = $this->input->get("kecamatan");
  //   // if (strlen($kecamatan)) {
  //   //   $kecamatan = "";
  //   // }

  //   //get produk data
  //   $ddcount = $this->fdis->countAll($nation_code,$parent_discussion_id, $product_id);
  //   $ddata = $this->fdis->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $parent_discussion_id, $product_id);

  //   foreach ($ddata as &$dd) {

  //     $dd->diskusi_anak_total = $this->fdis->countAll($nation_code,$dd->id, $product_id);
      
  //     $dd->diskusi_anak = $this->fdis->getAll($nation_code, $page_child_discussion, $page_size_child_discussion, $sort_col, $sort_dir, $dd->id, $product_id);

  //   }
    
  //   unset($dd); //free some memory

  //   //build result
  //   $data['diskusis'] = $ddata;
  //   $data['diskusi_total'] = $ddcount;

  //   //response
  //   $this->status = 200;
  //   $this->message = 'Success';
  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  // }

  // //by Donny Dennison - 7 august 2020 10:40
  // // add QnA / discussion feature
  // public function detail_discussion()
  // {
  //   //initial
  //   $dt = $this->__init();

  //   //default result
  //   $data = array();
  //   $data['diskusis'] = array();

  //   //check nation_code
  //   $nation_code = $this->input->get('nation_code');
  //   $nation_code = $this->nation_check($nation_code);
  //   if (empty($nation_code)) {
  //     $this->status = 101;
  //     $this->message = 'Missing or invalid nation_code';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   $pelanggan = new stdClass();
  //   $apisess = $this->input->get('apisess');
  //   if (strlen($apisess)>3) {
  //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   }
  //   if (!isset($pelanggan->id)) {
  //     $pelanggan = new stdClass();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  //     die();
  //   }

  //   //populate input get
  //   $sort_col = "cdate";
  //   $sort_dir = "asc";
  //   $page = $this->input->get("page");
  //   $page_size = $this->input->get("page_size");

  //   $parent_discussion_id = (int) $this->input->get("parent_discussion_id");
  //   $product_id = (int) $this->input->get("product_id");
  //   if ($parent_discussion_id<=0) {
  //     $parent_discussion_id = 0;
  //   }
  //   if ($product_id<=0) {
  //     $product_id = 0;
  //   }

  //   //sanitize input
  //   $tbl_as = $this->fdis->getTblAs();
  //   $tbl2_as = $this->fdis->getTbl2As();
  //   $sort_col = $this->__sortColDiscussion($sort_col, $tbl_as, $tbl2_as);
  //   $sort_dir = $this->__sortDir($sort_dir);
  //   $page = $this->__page($page);
  //   $page_size = $this->__pageSize($page_size);


  //   //get produk data
  //   $ddata = $this->fdis->getbyDiscussionID($nation_code, $parent_discussion_id, $product_id);

  //   $ddata->diskusi_anak_total = $this->fdis->countAll($nation_code,$parent_discussion_id, $product_id);
    
  //   $ddata->diskusi_anak = $this->fdis->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $parent_discussion_id, $product_id);
    

  //   //build result
  //   $data['diskusis'] = $ddata;

  //   //response
  //   $this->status = 200;
  //   $this->message = 'Success';
  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
  // }

//   //by Donny Dennison - 3 august 2020 16:25
//   // add QnA / discussion feature
//   public function add_discussion()
//   {
//     //initial
//     $dt = $this->__init();
//     //error_reporting(0);

//     //default result
//     $data = array();
//     $data['diskusis'] = new stdClass();

//     //check nation_code
//     $nation_code = $this->input->get('nation_code');
//     $nation_code = $this->nation_check($nation_code);
//     if (empty($nation_code)) {
//       $this->status = 101;
//       $this->message = 'Missing or invalid nation_code';
//       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
//       die();
//     }

//     //check apikey
//     $apikey = $this->input->get('apikey');
//     /*var_dump($apikey); die();*/
//     $c = $this->apikey_check($apikey);
//     if (!$c) {
//       $this->status = 400;
//       $this->message = 'Missing or invalid API key';
//       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
//       die();
//     }

//     //check apisess
//     $apisess = $this->input->get('apisess');
//     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
//     /*var_dump($pelanggan);
//     die();*/
//     if (!isset($pelanggan->id)) {
//       $this->status = 401;
//       $this->message = 'Missing or invalid API session';
//       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
//       die();
//     }

//     $this->status = 300;
//     $this->message = 'Missing one or more parameters';

//     //collect product input
//     $parent_discussion_id = (int) $this->input->post('parent_discussion_id');
//     $text = $this->input->post('text');
//     $product_id = (int) $this->input->post('product_id');

//     //input validation
//     if (empty($parent_discussion_id)) {
//       $parent_discussion_id = 0;
//     }

//     //by Donny Dennison 16 augustus 2020 00:25
//     //fix check emoji in insert & edit product and discussion
//     if( preg_match( $this->unicodeRegexp, $text ) ){

//       $this->status = 1099;
//       $this->message = 'Symbol image(ex.Emoji..) is not allowed in Product Q&A';
//       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
//       die();

//     }

//     if (preg_match("/(@|telephone|phone|cell|e-mail|email|commission|address|zip|postal|[0-9]{5}|[0-9]{6})/i", $text)) {
//       $this->status = 1099;
//       $this->message = 'Some words are not allowed to use for Product Q&A';
//       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
//       die();
//     }
    
//     if (empty($product_id)) {
//       $product_id = 0;
//     }

//     //validating
//     if ($parent_discussion_id<0) {
//       $this->status = 1099;
//       $this->message = 'Invalid parent_discussion_id';
//       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
//       die();
//     }

//     if ($product_id<=0) {
//       $this->status = 1099;
//       $this->message = 'Invalid product_id';
//       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
//       die();
//     }

//     $produk = $this->cpm->getById($nation_code, $product_id);

//     $user_type = 'buyer';

//     if($produk->b_user_id_seller == $pelanggan->id){
    
//       $user_type = 'seller';

//     }

//     //filter phone number
//    //  $text = preg_replace('/\+?[0-9][0-9()\-\s+]{4,20}[0-9]/', '[blocked]', $text);

//    //   //filter email
//    //   $pattern = "/[^@\s]*@[^@\s]*\.[^@\s]*/";
//    //   $replacement = "[blocked]";
//    //   $text = preg_replace($pattern, $replacement, $text);

//     // //filter phone number
//     // $text = preg_replace('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i','[blocked]',$text);

//     // //filter email
//     // $text = preg_replace('/(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?/','[blocked]',$text);

//     // $deskripsi = filter_var($deskripsi, FILTER_SANITIZE_STRING);
//     // $deskripsi = nl2br($deskripsi);


//     //start transaction and lock table
//     $this->fdis->trans_start();

//     //initial insert with latest ID
//     $di = array();
//     $di['nation_code'] = $nation_code;
//     $di['parent_f_discussion_id'] = $parent_discussion_id;
//     $di['product_id'] = $product_id;
//     $di['b_user_id'] = $pelanggan->id;
//     $di['user_type'] = $user_type;
//     $di['text'] = $text;
//     $di['cdate'] = 'NOW()';
//     $res = $this->fdis->set($di);
//     if (!$res) {
//       $this->fdis->trans_rollback();
//       $this->fdis->trans_end();
//       $this->status = 1108;
//       $this->message = "Error while posting discussion, please try again later";
//       $this->seme_log->write("api_mobile", 'API_Mobile/Produk::Discussion::baru -- RollBack -- forceClose '.$this->status.' '.$this->message);
//       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
//       die();
//     }

//     //input collection to db
   
//       $this->status = 200;
//       $this->message = "Success";
//       $this->message = 'Your writing is posted';
      
//       $this->seme_log->write("api_mobile", 'API_Mobile/Produk::Discussion::baru -- INFO '.$this->status.' '.$this->message);
//       $this->fdis->trans_commit();
//       $this->fdis->trans_end();


//         if($di['user_type']=="buyer")
//         {
//           $parentId = $di['parent_f_discussion_id'];
//           $productId = $di['product_id'];
//           $userid = $di['b_user_id'];

//           $detailProduct = $this->cpm->getByIdRaw($nation_code, $productId);
//           $sellerid = $detailProduct->b_user_id;
 
//           // select fcm token
//           $users = $this->bu->getFcmTokenSeller($nation_code, $sellerid);
//           /*var_dump($users); die();*/
//           /*var_dump($users); die();*/
//           // select id discuss
//           if($parentId==0){
//           $dataID = $res;
//           }
//           else
//           {
//             $dataID = $parentId;
//           }
          
//           // select data discuss depends id
//           $getData = $this->fdis->getDataSeller($nation_code, $dataID);
//           /*var_dump($getData); die();*/

//           $dpe = array();
//           $dpe['nation_code'] = $nation_code;
//           $dpe['b_user_id'] = $sellerid;
//           $dpe['id'] = $this->dpem->getLastId($nation_code, $sellerid);
//           $dpe['type'] = "discussion";
//           $dpe['judul'] = "Product Q&A";
//           $dpe['teks'] =  "Someone left a message on the product( ".$detailProduct->nama. " )";
//           $dpe['cdate'] = "NOW()";
//           $extras = new stdClass();
//           $extras->id = $dataID;
//           $extras->product_id = $productId;
//           $extras->nama = $detailProduct->nama;
//           $extras->harga_jual = $detailProduct->harga_jual;
//           $extras->foto = base_url().$detailProduct->thumb;
//           $extras->judul = "Product Q&A";
//           $extras->teks =  "Someone left a message on the product( ".$detailProduct->nama. " )";
//           $dpe['extras'] = json_encode($extras);
//           $this->dpem->set($dpe);
//           $this->fdis->trans_commit();


//           if($users->device == "ios"){
//               //push notif to ios
//               $device = "ios"; //jenis device
//               $tokens = $users->fcm_token; //device token
//               if(!is_array($tokens)) $tokens = array($tokens);
//               $title = "Product Q&A";
//               $message = "Someone left a message on the product( ".$detailProduct->nama. " )";
//               $image = 'media/pemberitahuan/promotion.png';
//               $type = 'discussion';
//               $payload = new stdClass();
//               $payload->id = $dataID;
//               $payload->product_id = $productId;
//               $payload->nama = $detailProduct->nama;
//               $payload->harga_jual = $detailProduct->harga_jual;
//               $payload->foto = base_url().$detailProduct->thumb;
//               $payload->judul = "Product Q&A";
//               //by Donny Dennison
//               //dicomment untuk handle message too big, response dari fcm
//               // $payload->teks = strip_tags(html_entity_decode($di['teks']));
//               $payload->teks = "Someone left a message on the product( ".$detailProduct->nama. " )";
//               $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
//               if ($this->is_log) {
//                   $this->seme_log->write("api_mobile", 'API_Admin/Discussion::baru __pushNotifiOS: '.json_encode($res));
//               }
//           }
//           else
//           {
//               //push notif to ios
//               $device = "android"; //jenis device
//               $tokens = $users->fcm_token; //device token
//               if(!is_array($tokens)) $tokens = array($tokens);
//               $title = "Product Q&A";
//               $message = "Someone left a message on the product( ".$detailProduct->nama. " )";
//               $image = 'media/pemberitahuan/promotion.png';
//               $type = 'discussion';
//               $payload = new stdClass();
//               $payload->id = $dataID;
//               $payload->product_id = $productId;
//               $payload->nama = $detailProduct->nama;
//               $payload->harga_jual = $detailProduct->harga_jual;
//               $payload->foto = base_url().$detailProduct->thumb;
//               $payload->judul = "Product Q&A";
//               //by Donny Dennison
//               //dicomment untuk handle message too big, response dari fcm
//               // $payload->teks = strip_tags(html_entity_decode($di['teks']));
//               $payload->teks = "Someone left a message on the product( ".$detailProduct->nama. " )";
//               $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
//               if ($this->is_log) {
//                   $this->seme_log->write("api_mobile", 'API_Admin/Discussion::baru __pushNotifiOS: '.json_encode($res));
//               }
//            }
//         }
//         else{
//           $parentId = $di['parent_f_discussion_id'];
//           $productId = $di['product_id'];
//           $ios = array();
//           $android = array();
//           $user_id = array();
//           $discussionid = array();
//           $usertype = array();

//           // select product detail dengan product_id = productId
//           $productDetail = $this->cpm->getByIdRaw($nation_code, $productId);
//           $sellerid = $productDetail->b_user_id;
//           // select id, b_user_id, usertype, nama dengan where parent id = parentID dan parent_f_discussion_id = parentId
//           $getData = $this->fdis->getLastId($nation_code, $parentId, $productId, $sellerid);
//           /*print_r($getData); die();*/
          
//           $var1 = (int) $pelanggan->id;

//           foreach ($getData as $dts) 
//           {
//               $var2 = (int)$dts->b_user_id;
//               $user_id[] = $dts->b_user_id;
//               $userid = $dts->b_user_id; 
// /*              var_dump($user_id); die();*/
//               $discussionid[] = $dts->discussion_id;
//               $usertype[] = $dts->user_type;

//               $users = $this->bu->getFcmToken($nation_code, $userid);
//               /*print_r($users); die();*/
//             // select fcm token

//               if (count($users)>0 && $var2!=$var1) {
//               foreach ($users as $user) 
//               {

//                   if (strtolower($user->device) == 'ios') {
//                       $ios[] = $user->fcm_token;
//                   } else {
//                       $android[] = $user->fcm_token;
//                   }
//                   $dpe = array();
//                   $dpe['nation_code'] = $nation_code;
//                   $dpe['b_user_id'] = $userid;
//                   $dpe['id'] = $this->dpem->getLastId($nation_code, $userid);
//                   $dpe['type'] = "discussion";
//                   $dpe['judul'] = "Product Q&A";
//                   $dpe['teks'] =  "Someone left a message on the product( ".$productDetail->nama. " )";
//                   $dpe['cdate'] = "NOW()";
//                   $extras = new stdClass();
//                   //parent_f_id
//                   $extras->id = $parentId;
//                   $extras->product_id = $productId;
//                   $extras->nama = $productDetail->nama;
//                   $extras->harga_jual = $productDetail->harga_jual;
//                   $extras->foto = base_url().$productDetail->thumb;
//                   $extras->judul = "Product Q&A";
//                   $extras->teks =  "Someone left a message on the product( ".$productDetail->nama. " )";
//                   $dpe['extras'] = json_encode($extras);
//                   $this->dpem->set($dpe);
//                   $this->fdis->trans_commit();
//                 }
//               } 
//             }
//                   if (array_unique($ios)) {
              
//                       //push notif to ios
//                       $device = "ios"; //jenis device
//                       $tokens = $ios; 
//                       /*print_r($tokens); echo "ios"; die();*///device token
//                       $title = "Product Q&A";
//                       $message = "Someone left a message on the product( ".$productDetail->nama. " )";
//                       $image = 'media/pemberitahuan/promotion.png';
//                       $type = 'discussion';
//                       $payload = new stdClass();
//                       $payload->id = $parentId;
//                       $payload->product_id = $productId;
//                       $payload->nama = $productDetail->nama;
//                       $payload->harga_jual = $productDetail->harga_jual;
//                       $payload->foto = base_url().$productDetail->thumb;
//                       $payload->judul = "Product Q&A";
//                       //by Donny Dennison
//                       //dicomment untuk handle message too big, response dari fcm
//                       // $payload->teks = strip_tags(html_entity_decode($di['teks']));
//                       $payload->teks = "Someone left a message on the product( ".$productDetail->nama. " )";
//                       $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
//                       if ($this->is_log) {
//                           $this->seme_log->write("api_mobile", 'API_Admin/Discussion::baru __pushNotifiOS: '.json_encode($res));
//                       }
//                     }

//                 if (array_unique($android)) {
                    
//                     $device = "android"; //jenis device
//                     $tokens = $android; 
//                     /*print_r($tokens); echo "android"; die();*///device token
//                     $title = "Product Q&A";
//                     $message = "Someone left a message on the product( ".$productDetail->nama. " )";
//                     $type = 'discussion';
//                     $image = 'media/pemberitahuan/promotion.png';
//                     $payload = new stdClass();
//                     $payload->id = $parentId;
//                     $payload->product_id = $productId;
//                     $payload->nama = $productDetail->nama;
//                     $payload->harga_jual = $productDetail->harga_jual;
//                     $payload->foto = base_url().$productDetail->thumb;
//                     $payload->judul = "Product Q&A";
//                     //by Donny Dennison
//                     //dicomment untuk handle message too big, response dari fcm
//                     // $payload->teks = strip_tags(html_entity_decode($di['teks']));
//                     $payload->teks = "Someone left a message on the product( ".$productDetail->nama. " )";
//                     $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
//                     if ($this->is_log) {
//                         $this->seme_log->write("api_mobile", 'API_Admin/Discussion::baru __pushNotifAndroid: '.json_encode($res));
//                     }
//                   } 

// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//                   //SEND NOTIF TO SELLER

//              /* $user = $this->bu->getFcmTokenSeller($nation_code, $sellerid);


//               if ($sellerid!=$var1) {

//                   $dpe = array();
//                   $dpe['nation_code'] = $nation_code;
//                   $dpe['b_user_id'] = $sellerid;
//                   $dpe['id'] = $this->dpem->getLastId($nation_code, $sellerid);
//                   $dpe['type'] = "discussion";
//                   $dpe['judul'] = "Product Q&A";
//                   $dpe['teks'] =  "Someone left a message on the product( ".$productDetail->nama. " )";
//                   $dpe['cdate'] = "NOW()";
//                   $extras = new stdClass();
//                   //parent_f_id
//                   $extras->id = $parentId;
//                   $extras->product_id = $productId;
//                   $extras->nama = $productDetail->nama;
//                   $extras->harga_jual = $productDetail->harga_jual;
//                   $extras->foto = base_url().$productDetail->thumb;
//                   $extras->judul = "Product Q&A";
//                   $extras->teks =  "Someone left a message on the product( ".$productDetail->nama. " )";
//                   $dpe['extras'] = json_encode($extras);
//                   $this->dpem->set($dpe);
//                   $this->fdis->trans_commit();
                

            
//                   if (strtolower($user->device) == 'ios') {
              
//                       //push notif to ios
//                       $device = "ios"; //jenis device
//                       $tokens = array($user->fcm_token); 
//                       $title = "Product Q&A";
//                       $message = "Someone left a message on the product( ".$productDetail->nama. " )";
//                       $image = 'media/pemberitahuan/promotion.png';
//                       $type = 'discussion';
//                       $payload = new stdClass();
//                       $payload->id = $parentId;
//                       $payload->product_id = $productId;
//                       $payload->nama = $productDetail->nama;
//                       $payload->harga_jual = $productDetail->harga_jual;
//                       $payload->foto = base_url().$productDetail->thumb;
//                       $payload->judul = "Product Q&A";
//                       $payload->teks = "Someone left a message on the product( ".$productDetail->nama. " )";
//                       $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
//                       if ($this->is_log) {
//                           $this->seme_log->write("api_mobile", 'API_Admin/Discussion::baru __pushNotifiOS: '.json_encode($res));
//                       }
//                     }

//                 if (strtolower($user->device) == 'android') {
                    
//                     $device = "android"; //jenis device
//                     $tokens = array($user->fcm_token); 
//                     $title = "Product Q&A";
//                     $message = "Someone left a message on the product( ".$productDetail->nama. " )";
//                     $type = 'discussion';
//                     $image = 'media/pemberitahuan/promotion.png';
//                     $payload = new stdClass();
//                     $payload->id = $parentId;
//                     $payload->product_id = $productId;
//                     $payload->nama = $productDetail->nama;
//                     $payload->harga_jual = $productDetail->harga_jual;
//                     $payload->foto = base_url().$productDetail->thumb;
//                     $payload->judul = "Product Q&A";
//                     $payload->teks = "Someone left a message on the product( ".$productDetail->nama. " )";
//                     $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
//                     if ($this->is_log) {
//                         $this->seme_log->write("api_mobile", 'API_Admin/Discussion::baru __pushNotifAndroid: '.json_encode($res));
//                     }
//                   } 
//                 } */                   

//         }   
    

//          /*//push notif 
//          if (strlen($w->b_user_fcm_token_buyer) > 50 && (!empty($setting_value) || !empty($setting_value2))) {
//            $device = $w->b_user_device_buyer;
//            $tokens = array($w->b_user_fcm_token_buyer);
//            $title = 'Product Recommendation';
//            $message = "Someone has posted a product you might be interested in.";
//            $type = 'product_recommend';
//            $image = 'media/pemberitahuan/promotion.png';
//            $payload = new stdClass();
//            $payload->keyword = $w->keyword_text;
//            $payload->id_produk = $cpm_id;
//            $payload->id_order = null;
//            $payload->id_order_detail = null;
//            $payload->b_user_id_buyer = $w->b_user_id_buyer;
//            $payload->b_user_fnama_buyer = $w->b_user_fnama_buyer;
//            $payload->b_user_image_buyer = $this->cdn_url($w->b_user_image_buyer);
//            $payload->b_user_id_seller = $seller->id;
//            $payload->b_user_fnama_seller = $seller->fnama;
//            $payload->b_user_image_seller = $this->cdn_url($seller->image);
//            $nw = $this->anot->get($nation_code, "push", $type, $anotid);
//            if (isset($nw->title)) {
//              $title = $nw->title;
//            }
//            if (isset($nw->message)) {
//              $message = $this->__nRep($nw->message, $replacer);
//            }
//            if (isset($nw->image)) {
//              $image = $nw->image;
//            }
//            $image = $this->cdn_url($image);
//            $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
//            if ($this->is_log) {
//              $this->seme_log->write("api_mobile", 'API_Mobile/Produk::baru -> __pushNotif: '.json_encode($res));
//            }
//          }
//      }*/ 
//     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
//   }

//   //by Donny Dennison - 7 august 2020 10:40
//   // add QnA / discussion feature
//   public function delete_discussion()
//   {
//     //initial
//     $dt = $this->__init();

//     //default result
//     $data = array();
//     $data['diskusis'] = array();

//     //check nation_code
//     $nation_code = $this->input->get('nation_code');
//     $nation_code = $this->nation_check($nation_code);
//     if (empty($nation_code)) {
//       $this->status = 101;
//       $this->message = 'Missing or invalid nation_code';
//       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
//       die();
//     }

//     $pelanggan = new stdClass();
//     $apisess = $this->input->get('apisess');
//     if (strlen($apisess)>3) {
//       $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
//     }
//     if (!isset($pelanggan->id)) {
//       $pelanggan = new stdClass();
//     }

//     //check apikey
//     $apikey = $this->input->get('apikey');
//     $c = $this->apikey_check($apikey);
//     if (!$c) {
//       $this->status = 400;
//       $this->message = 'Missing or invalid API key';
//       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
//       die();
//     }

//     //populate input get
//     $discussion_id = (int) $this->input->get("discussion_id");

//     //check discussion id and user id
//     $c = $this->fdis->getbyDiscussionIDUserID($nation_code, $discussion_id, $pelanggan->id);
//     if (!isset($c->discussion_id)) {
//       $this->status = 1109;
//       $this->message = 'Discussion ID and User ID is different';
//       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
//       die();
//     }
    

//     //start transaction and lock table
//     $this->fdis->trans_start();

//     //initial insert with latest ID
//     $di = array();
//     $di['edate'] = 'NOW()';
//     $di['is_active'] = 0;
//     $res = $this->fdis->update($nation_code, $discussion_id, $di);
//     if (!$res) {
//       $this->fdis->trans_rollback();
//       $this->fdis->trans_end();
//       $this->status = 1108;
//       $this->message = "Error while delete discussion, please try again later";
//       $this->seme_log->write("api_mobile", 'API_Mobile/Produk::Discussion::hapus -- RollBack -- forceClose '.$this->status.' '.$this->message);
//       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
//       die();
//     }

//     //input collection to db
//     $res = true;
//     if ($res) {
//       $this->status = 200;
//       $this->message = 'Your case is deleted';
      
//       $this->seme_log->write("api_mobile", 'API_Mobile/Produk::Discussion::hapus -- INFO '.$this->status.' '.$this->message);
//     } else {
//       $this->fdis->trans_rollback();
//       $this->fdis->trans_end();
//       $this->status = 1107;
//       $this->message = "Error while delete discussion, please try again later";
//       $this->seme_log->write("api_mobile", 'API_Mobile/Produk::Discussion::hapus -- RollBack -- forceClose '.$this->status.' '.$this->message);
//       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
//       die();
//     }

//     //commit and end transaction
//     $this->fdis->trans_commit();
//     $this->fdis->trans_end();

//     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");

//   }

//   //by Donny Dennison - 7 august 2020 10:40
//   // add QnA / discussion feature
//   public function report_discussion()
//   {
//     //initial
//     $dt = $this->__init();

//     //default result
//     $data = array();
//     $data['diskusis'] = array();

//     //check nation_code
//     $nation_code = $this->input->get('nation_code');
//     $nation_code = $this->nation_check($nation_code);
//     if (empty($nation_code)) {
//       $this->status = 101;
//       $this->message = 'Missing or invalid nation_code';
//       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
//       die();
//     }

//     $pelanggan = new stdClass();
//     $apisess = $this->input->get('apisess');
//     if (strlen($apisess)>3) {
//       $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
//     }
//     if (!isset($pelanggan->id)) {
//       $pelanggan = new stdClass();
//     }

//     //check apikey
//     $apikey = $this->input->get('apikey');
//     $c = $this->apikey_check($apikey);
//     if (!$c) {
//       $this->status = 400;
//       $this->message = 'Missing or invalid API key';
//       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
//       die();
//     }

//     //populate input get
//     $discussion_id = (int) $this->input->get("discussion_id");
    

//     //start transaction and lock table
//     $this->fdisrep->trans_start();

//     //initial insert with latest ID
//     $di = array();
//     $di['nation_code'] = $nation_code;
//     $di['f_discussion_id'] = $discussion_id;
//     $di['b_user_id'] = $pelanggan->id;
//     $di['cdate'] = 'NOW()';
//     $res = $this->fdisrep->set($di);
//     if (!$res) {
//       $this->fdisrep->trans_rollback();
//       $this->fdisrep->trans_end();
//       $this->status = 1108;
//       $this->message = "Error while report discussion, please try again later";
//       $this->seme_log->write("api_mobile", 'API_Mobile/Produk::Discussion::report -- RollBack -- forceClose '.$this->status.' '.$this->message);
//       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
//       die();
//     }

//     //input collection to db
//     $res = true;
//     if ($res) {
//       $this->status = 200;
//       $this->message = "Success";
//       $this->message = 'This case is reported to SellOn admin';
      
//       $this->seme_log->write("api_mobile", 'API_Mobile/Produk::Discussion::report -- INFO '.$this->status.' '.$this->message);
//     } else {
//       $this->fdisrep->trans_rollback();
//       $this->fdisrep->trans_end();
//       $this->status = 1107;
//       $this->message = "Error while report discussion, please try again later";
//       $this->seme_log->write("api_mobile", 'API_Mobile/Produk::Discussion::report -- RollBack -- forceClose '.$this->status.' '.$this->message);
//       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");
//       die();
//     }

//     //commit and end transaction
//     $this->fdisrep->trans_commit();
//     $this->fdisrep->trans_end();

//     //update is_report and report_date
//     $di = array();
//     $di['report_date'] = 'NOW()';
//     $di['is_report'] = 1;
//     $this->fdis->update($nation_code, $discussion_id, $di);

//     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk_automotive");

//   }

}
