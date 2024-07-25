<?php
class Like extends JI_Controller
{
  public $is_soft_delete=1;
  public $is_log = 1;

  public function __construct()
  {
    parent::__construct();
    //$this->setTheme('frontx');
    $this->lib("seme_log");
    $this->lib("seme_email");
    $this->lib("seme_purifier");

    $this->load("api_mobile/e_likes_model", "elm");

    // $this->load("api_mobile/a_notification_model", "anot");
    $this->load("api_mobile/b_user_model", "bu");
    $this->load("api_mobile/b_user_alamat_model", "bua");
    // $this->load("api_mobile/b_user_setting_model", "busm");
    // $this->load("api_mobile/b_user_productwanted_model", "bupw");
    // $this->load("api_mobile/b_kategori_model3", "bkm3");
    // $this->load("api_mobile/b_kondisi_model", "bkon");
    // $this->load("api_mobile/b_berat_model", "brt");
    $this->load("api_mobile/c_produk_model", "cpm");
    // $this->load("api_mobile/c_produk_foto_model", "cpfm");
    // $this->load("api_mobile/common_code_model", "ccm");
    // $this->load("api_mobile/d_wishlist_model", "dwlm");
    // $this->load("api_mobile/d_cart_model", "cart");
    // $this->load("api_mobile/d_pemberitahuan_model", "dpem");
    // $this->load("api_mobile/f_discussion_model", "fdis");
    // $this->load("api_mobile/f_discussion_report_model", "fdisrep");

    // $this->load("api_mobile/a_negara_model", 'anm');

    //by Donny Dennison - 16 december 2021 15:49
    //get point as leaderboard rule
    // $this->load("api_mobile/g_leaderboard_point_area_model", 'glpam');
    // $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
    // $this->load("api_mobile/g_leaderboard_ranking_model", 'glrm');

  }
  
  private function __sortCol($sort_col, $tbl_as, $tbl2_as)
  {
    switch ($sort_col) {
      case 'id':
      $sort_col = "$tbl_as.id";
      break;

      case 'cdate':
      $sort_col = "$tbl_as.cdate";
      break;

      default:
      $sort_col = "$tbl_as.id";
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
    $data['like_total'] = 0;
    $data['likes'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "like");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "like");
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
    $keyword = trim($this->input->get("keyword"));
    $type = trim($this->input->get("type"));
    $custom_id = trim($this->input->get("custom_id"));

    //sanitize input
    $tbl_as = $this->elm->getTblAs();
    $tbl2_as = $this->elm->getTbl2As();

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

    $ddcount = $this->elm->countAll($nation_code, $keyword, $type, $custom_id);

    $ddata = $this->elm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $type, $custom_id);

    //manipulating data
    foreach ($ddata as $pd) {
      //convert to utf friendly
      if (isset($pd->b_user_nama)) {
        $pd->b_user_nama = $this->__dconv($pd->b_user_nama);
      }
      
      if (isset($pd->foto)) {
        if (empty($pd->foto)) {
          $pd->foto = 'media/user/default-profile-picture.png';
        }
        $pd->foto = str_replace("//", "/", $pd->foto);

        if(file_exists(SENEROOT.$pd->foto) && $pd->foto != 'media/user/default.png'){
          $pd->foto = $this->cdn_url($pd->foto);
        } else {
          $pd->foto = $this->cdn_url('media/user/default-profile-picture.png');
        }
      }

      $data['likes'][] = $pd;
    }
    unset($ddata,$pd);

    //build result
    $data['like_total'] = $ddcount;

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "like");
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
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "like");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "like");
  //     die();
  //   }

  //   $id = (int) $id;
  //   if ($id<=0) {
  //     $this->status = 595;
  //     $this->message = 'Invalid product ID';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "like");
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
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "like");
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
    
  //     $produk->diskusis[$key]->diskusi_anak = $this->fdis->getAll($nation_code, $page, $page_size_child_discussion, $sort_col, $sort_dir, $discuss->discussion_id, $id);

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

  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "like");
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

  public function baru()
  {
    //initial
    $dt = $this->__init();
    //error_reporting(0);

    //default result
    $data = array();
    $data['product'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "like");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "like");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "like");
      die();
    }

    $this->status = 300;
    $this->message = 'Missing one or more parameters';
    
    //collect input
    $type = trim($this->input->post('type'));
    $custom_id = $this->input->post('custom_id');

    //validating
    if (strlen($type)<=0) {
      $type = 'product';
    }

    $getProductType = $this->cpm->getProductType($nation_code, $custom_id);
    if(!isset($getProductType->product_type)){
      $this->status = 1099;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "like");
      die();
    }

    $getProductType = $getProductType->product_type;

    $product = $this->cpm->getById($nation_code, $custom_id ,$pelanggan, $getProductType, $pelanggan->language_id);
    if (!isset($product->id)) {
      $this->status = 1099;
      $this->message = 'Invalid product ID or Product not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "like");
      die();
    }

    //0 = unlike or 1 = like
    $likeOrUnlike = 0;

    //start transaction and lock table
    $this->elm->trans_start();

    //check like / unlike
    $checkLikeUnlike = $this->elm->getByCustomIdUserId($nation_code, $type, $custom_id, $pelanggan->id);

    if(!isset($checkLikeUnlike->id)){

      //get last id
      $elm_id = $this->elm->getLastId($nation_code, $type);

      //initial insert with latest ID
      $di = array();
      $di['nation_code'] = $nation_code;
      $di['id'] = $elm_id;
      $di['type'] = $type;
      $di['custom_id'] = $custom_id;
      $di['b_user_id'] = $pelanggan->id;
      $di['cdate'] = 'NOW()';

      $res = $this->elm->set($di);
      $this->elm->trans_commit();
      if (!$res) {
        $this->elm->trans_rollback();
        $this->elm->trans_end();
        $this->status = 1108;
        $this->message = "Error, please try again later";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "like");
        die();
      }

      $this->status = 200;
      $this->message = "Success";
      // $this->message = 'Your like has been saved';

      $likeOrUnlike = 1;

    }else{

      //initial insert with latest ID
      $di = array();
      $di['ldate'] = 'NOW()';
      $di['is_active'] = 0;

      $res = $this->elm->update($nation_code, $type, $checkLikeUnlike->id,$di);
      $this->elm->trans_commit();
      if (!$res) {
        $this->elm->trans_rollback();
        $this->elm->trans_end();
        $this->status = 1108;
        $this->message = "Error, please try again later";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "like");
        die();
      }

      $this->status = 200;
      $this->message = "Success";
      // $this->message = 'Your unlike has been saved';

    }

    //count again total like and update to product table
    $totalLikes = $this->elm->countAll($nation_code,'', $type, $custom_id);

    $di = array();
    $di['total_likes'] = $this->thousandsCurrencyFormat($totalLikes);
    $this->cpm->update2($nation_code, $custom_id, $di);
    $this->elm->trans_commit();

    //end transaction
    $this->elm->trans_end();

    //START by Donny Dennison - 16 december 2021 15:49
    //get point as leaderboard rule
    // if(!isset($checkLikeUnlike->id)){

    //   $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

    //   $checkAlreadyInleaderBoardHistory = $this->glphm->checkAlreadyInDB($nation_code, $pelangganAddress->kelurahan, $pelangganAddress->kecamatan, $pelangganAddress->kabkota, $pelangganAddress->provinsi, $pelanggan->id, $custom_id, 'product', 'like');
      
    //   if(!isset($checkAlreadyInleaderBoardHistory->b_user_id)){
    //     $di = array();
    //     $di['nation_code'] = $nation_code;
    //     $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
    //     $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
    //     $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
    //     $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
    //     $di['b_user_id'] = $pelanggan->id;
    //     $di['point'] = 1;
    //     $di['custom_id'] = $custom_id;
    //     $di['custom_type'] = 'product';
    //     $di['custom_type_sub'] = 'like';
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
    //     $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '+', $di['point']);
    //   }
    // }
    //END by Donny Dennison - 16 december 2021 15:49

    // //push notif to wanted product user from product name
    // if (!empty($is_published)) {
    //   //get seller data
    //   $seller = $pelanggan;

    //   //open transaction

    //   //get wanted user
    //   $wanteds = $this->bupw->getWanteds($nation_code, $data['produk']->nama);
    //   if ($this->is_log) {
    //     $this->seme_log->write("api_mobile", "API_Mobile/Produk::baru --pushNotifCount: ".count($wanteds));
    //   }
    //   foreach ($wanteds as $w) {
    //     //get notification config for buyer
    //     $type = 'product_recommend';
    //     $anotid = 1;
    //     $replacer = array();

    //     //by Donny Dennison - 20 october 2020 15:55
    //     //add product name in push notif
    //     // $replacer['product_recommendation'] = '';
    //     $replacer['product_recommendation'] = $data['produk']->nama;

    //     //declare var for notification

    //     //by Donny Dennison - 20 october 2020 10:17
    //     //disable / delete product sought to notification
    //     // $setting_value = 0;
    //     // $classified = 'setting_notification_buyer';
    //     // $notif_code = 'B6';
    //     // $notif_cfg = $this->busm->getValue($nation_code, $w->b_user_id_buyer, $classified, $notif_code);
    //     // if (isset($notif_cfg->setting_value)) {
    //     //   $setting_value = (int) $notif_cfg->setting_value;
    //     // }

    //     $setting_value2 = 0;
    //     $classified = 'setting_notification_buyer';
    //     $notif_code = 'B5';
    //     $notif_cfg = $this->busm->getValue($nation_code, $w->b_user_id_buyer, $classified, $notif_code);
    //     if (isset($notif_cfg->setting_value)) {
    //       $setting_value2 = (int) $notif_cfg->setting_value;
    //     }
    //     if ($this->is_log) {

    //       //by Donny Dennison - 20 october 2020 10:17
    //       //disable / delete product sought to notification
    //       // $this->seme_log->write("api_mobile", "API_Mobile/Produk::baru -- b_user_device_buyer: $w->b_user_device_buyer, b_user_id_buyer: $w->b_user_id_buyer, setting_value: $setting_value, setting_value2: $setting_value2");
    //       $this->seme_log->write("api_mobile", "API_Mobile/Produk::baru -- b_user_device_buyer: $w->b_user_device_buyer, b_user_id_buyer: $w->b_user_id_buyer, setting_value2: $setting_value2");

    //     }

    //     //push notif to buyer


    //     //by Donny Dennison - 20 october 2020 10:17
    //     //disable / delete product sought to notification
    //     // if (strlen($w->b_user_fcm_token_buyer) > 50 && (!empty($setting_value) || !empty($setting_value2))) {
    //     if (strlen($w->b_user_fcm_token_buyer) > 50 && !empty($setting_value2)) {

    //       $device = $w->b_user_device_buyer;
    //       $tokens = array($w->b_user_fcm_token_buyer);
    //       $title = 'Product Recommendation';

    //       //by Donny Dennison - 20 october 2020 11:32
    //       //remove "in" in message
    //       // $message = "Someone has posted a product you might be interested in.";
    //       $message = "Someone has posted a product you might be interested.";

    //       $type = 'product_recommend';
    //       $image = 'media/pemberitahuan/promotion.png';
    //       $payload = new stdClass();
    //       $payload->keyword = $w->keyword_text;
    //       $payload->id_produk = $cpm_id;
    //       $payload->id_order = null;
    //       $payload->id_order_detail = null;
    //       $payload->b_user_id_buyer = $w->b_user_id_buyer;
    //       $payload->b_user_fnama_buyer = $w->b_user_fnama_buyer;
    //       $payload->b_user_image_buyer = $this->cdn_url($w->b_user_image_buyer);
    //       $payload->b_user_id_seller = $seller->id;
    //       $payload->b_user_fnama_seller = $seller->fnama;
    //       $payload->b_user_image_seller = $this->cdn_url($seller->image);
    //       $nw = $this->anot->get($nation_code, "push", $type, $anotid);
    //       if (isset($nw->title)) {
    //         $title = $nw->title;
    //       }
    //       if (isset($nw->message)) {
    //         $message = $this->__nRep($nw->message, $replacer);
    //       }
    //       if (isset($nw->image)) {
    //         $image = $nw->image;
    //       }
    //       $image = $this->cdn_url($image);
    //       $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
    //       if ($this->is_log) {
    //         $this->seme_log->write("api_mobile", 'API_Mobile/Produk::baru -> __pushNotif: '.json_encode($res));
    //       }

    //     //by Donny Dennison 20 october 2020 11:36
    //     //fix notif product recommendation still send when disable send notif
    //     // }

    //       $replacer['product_recommendation'] = $data['produk']->nama;

    //       //collect array notification list for buyer
    //       $extras = new stdClass();
    //       $extras->keyword = $w->keyword_text;
    //       $extras->id_order = null;
    //       $extras->id_produk = $cpm_id;
    //       $extras->id_order_detail = null;
    //       $extras->b_user_id_buyer = $w->b_user_id_buyer;
    //       $extras->b_user_fnama_buyer = $w->b_user_fnama_buyer;
    //       $extras->b_user_image_buyer = $this->cdn_url($w->b_user_image_buyer);
    //       $extras->b_user_id_seller = $seller->id;
    //       $extras->b_user_fnama_seller = $seller->fnama;
    //       $extras->b_user_image_seller = $this->cdn_url($seller->image);
    //       $dpe = array();
    //       $dpe['nation_code'] = $nation_code;
    //       $dpe['b_user_id'] = $w->b_user_id_buyer;
    //       $dpe['id'] = $this->dpem->getLastId($nation_code, $w->b_user_id_buyer);
    //       $dpe['type'] = "product_recommend";
    //       $dpe['judul'] = "Product Recommendation";
    //       $dpe['teks'] = "The product you're looking for has been posted ".$data['produk']->nama;
    //       $dpe['cdate'] = "NOW()";
    //       $dpe['gambar'] = 'media/pemberitahuan/promotion.png';
    //       $dpe['extras'] = json_encode($extras);
    //       $nw = $this->anot->get($nation_code, "list", $type, $anotid);
    //       if (isset($nw->title)) {
    //         $di2['judul'] = $nw->title;
    //       }
    //       if (isset($nw->message)) {
    //         $di2['teks'] = $this->__nRep($nw->message, $replacer);
    //       }
    //       if (isset($nw->image)) {
    //         $di2['gambar'] = $nw->image;
    //       }
    //       $di2['gambar'] = $this->cdn_url($di2['gambar']);
    //       $this->dpem->set($dpe);
    //       $this->cpm->trans_commit();
    //       if ($this->is_log) {
    //         $this->seme_log->write("api_mobile", "API_Mobile/Produk:baru --triggerRecommendation DONE");
    //       }

    //     //by Donny Dennison 20 october 2020 11:36
    //     //fix notif product recommendation still send when disable send notif
    //     }
        
    //   }
    // } //check if published

    if($type == 'product'){

      $getProductType = $this->cpm->getProductType($nation_code, $custom_id);
      $getProductType = $getProductType->product_type;

      $data['product'] = $this->cpm->getById($nation_code, $custom_id ,$pelanggan, $getProductType, $pelanggan->language_id);

      $data['product']->is_liked = (string) $likeOrUnlike;

    }

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "like");
  }

}
