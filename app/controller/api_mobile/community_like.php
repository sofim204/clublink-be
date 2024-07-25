<?php
class Community_like extends JI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->lib("seme_log");

    $this->load("api_mobile/c_community_like_model", "cclm");
    $this->load("api_mobile/c_community_like_category_model", "cclcm");
    $this->load("api_mobile/b_user_model", "bu");
    $this->load("api_mobile/b_user_alamat_model", "bua");
    $this->load("api_mobile/common_code_model", "ccm");

    $this->load("api_mobile/c_community_model", "ccomm");
    $this->load("api_mobile/c_community_discussion_model", "ccdm");

    //by Donny Dennison - 16 december 2021 15:49
    //get point as leaderboard rule
    // $this->load("api_mobile/g_leaderboard_point_area_model", 'glpam');
    $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
    // $this->load("api_mobile/g_leaderboard_ranking_model", 'glrm');
    $this->load("api_mobile/g_leaderboard_point_limit_model", 'glplm');

    //by Donny Dennison - 21 november 2022 10:02
    //new feature, block
    $this->load("api_mobile/c_block_model", "cbm");

    $this->load("api_mobile/c_community_fake_like_model", "ccflm");
    $this->load("api_mobile/c_community_like_history_model", "cclhm");
    $this->load("api_mobile/h_ticket_history_model", 'hthm');

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
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
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
    // $keyword = trim($this->input->get("keyword"));
    $type = trim($this->input->get("type"));
    $custom_id = trim($this->input->get("custom_id"));

    $like_type = (string) $this->input->get('like_type');

    if($like_type != "dislike") {
      $like_type = "like";
    }

    //sanitize input
    $tbl_as = $this->cclm->getTblAs();
    $tbl2_as = $this->cclm->getTbl2As();

    $sort_col = $this->__sortCol($sort_col, $tbl_as, $tbl2_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

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
    $keyword = ""; // set to 0

    $data['like_total'] = $this->cclm->countAll($nation_code, $keyword, $type, $custom_id, $like_type);

    $data['likes'] = $this->cclm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $type, $custom_id, $like_type);

    //manipulating data
    foreach ($data['likes'] as &$pd) {
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
      
      if (isset($pd->image_icon)) {
        if (empty($pd->image_icon)) {
          $pd->image_icon = 'media/produk/default.png';
        }
        $pd->image_icon = str_replace("//", "/", $pd->image_icon);
        $pd->image_icon = $this->cdn_url($pd->image_icon);
      }

    }

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
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
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  //     die();
  //   }

  //   $id = (int) $id;
  //   if ($id<=0) {
  //     $this->status = 595;
  //     $this->message = 'Invalid product ID';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
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
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
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

  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  // }

  // public function baru()
  // {
  //   //initial
  //   $dt = $this->__init();

  //   //default result
  //   $data = array();
  //   $data['total_likes'] = "0";
  //   $data['total_dislikes'] = "0";
  //   $data['is_like'] = "0";
  //   $data['is_dislike'] = "0";

  //   //check nation_code
  //   $nation_code = $this->input->get('nation_code');
  //   $nation_code = $this->nation_check($nation_code);
  //   if (empty($nation_code)) {
  //     $this->status = 101;
  //     $this->message = 'Missing or invalid nation_code';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  //     die();
  //   }

  //   //check apisess
  //   $apisess = $this->input->get('apisess');
  //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   if (!isset($pelanggan->id)) {
  //     $this->status = 401;
  //     $this->message = 'Missing or invalid API session';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  //     die();
  //   }

  //   $this->status = 300;
  //   $this->message = 'Missing one or more parameters';
    
  //   //collect input
  //   $type = trim($this->input->post('type'));
  //   $custom_id = $this->input->post('custom_id');
  //   $like_type = (string) $this->input->post('like_type');

  //   //validating
  //   if (strlen($type)<=0) {
  //     $type = 'community';
  //   }

  //   if ($custom_id<='0') {
  //     $this->status = 1099;
  //     $this->message = 'Invalid custom ID or Community not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  //     die();
  //   }

  //   if($like_type != "dislike") {
  //     $like_type = "like";
  //   }

  //   if($type == 'community'){

  //     $community = $this->ccomm->getById($nation_code, $custom_id, array());
  //     if (!isset($community->id)) {
  //       $this->status = 1099;
  //       $this->message = 'Invalid custom ID or Community not found';
  //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  //       die();
  //     }

  //   }else{

  //     $discussion = $this->ccdm->getbyDiscussionID($nation_code, $custom_id);
  //     if (!isset($discussion->id)) {
  //       $this->status = 1099;
  //       $this->message = 'Invalid custom ID or Community not found';
  //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  //       die();
  //     }

  //     $community = $this->ccomm->getById($nation_code, $discussion->c_community_id, array());

  //   }

  //   //START by Donny Dennison - 21 november 2022 10:02
  //   //new feature, block
  //   if($pelanggan->id != $community->b_user_id_starter){

  //     $blockDataCommunity = $this->cbm->getById($nation_code, 0, $pelanggan->id, "community", $community->id);
  //     $blockDataAccount = $this->cbm->getById($nation_code, 0, $community->b_user_id_starter, "account", $pelanggan->id);
  //     $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $community->b_user_id_starter);

  //     if(isset($blockDataCommunity->block_id) || isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){

  //       $this->status = 1005;
  //       $this->message = "You can't like or dislike as you're blocked";
  //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_blocked_like");
  //       die();

  //     }

  //   }
  //   //END by Donny Dennison - 21 november 2022 10:02
  //   //new feature, block

  //   //start transaction and lock table
  //   $this->cclm->trans_start();

  //   //check like / unlike
  //   $checkLikeUnlike = $this->cclm->getByCustomIdUserId($nation_code, $type, $like_type, $custom_id, $pelanggan->id);
  //   if(!isset($checkLikeUnlike->id)){

  //     //get last id
  //     // $cclm_id = $this->cclm->getLastId($nation_code, $type);

  //     $d_address = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

  //     //initial insert with latest ID
  //     $di = array();
  //     $di['nation_code'] = $nation_code;
  //     // $di['id'] = $cclm_id;
  //     $di['type'] = $type;
  //     $di['like_type'] = $like_type;
  //     $di['custom_id'] = $custom_id;
  //     $di['b_user_id'] = $pelanggan->id;
  //     $di['alamat2'] = $d_address->alamat2;
  //     $di['kelurahan'] = $d_address->kelurahan;
  //     $di['kecamatan'] = $d_address->kecamatan;
  //     $di['kabkota'] = $d_address->kabkota;
  //     $di['provinsi'] = $d_address->provinsi;
  //     $di['negara'] = $d_address->negara;
  //     $di['kodepos'] = $d_address->kodepos;
  //     $di['cdate'] = 'NOW()';

  //     $endDoWhile = 0;
  //     do{

  //       $cclm_id = $this->cclm->getLastId($nation_code, $type);
  //       $di['id'] = $cclm_id;
  //       $checkIdAlreadyInDB = $this->cclm->checkLastId($nation_code, $di['id'], $type);

  //       if(!isset($checkIdAlreadyInDB->id)){
  //           $endDoWhile = 1;
  //       }

  //     }while($endDoWhile == 0);
  //     $res = $this->cclm->set($di);
  //     // $this->cclm->trans_commit();

  //     if (!$res) {
  //       $this->cclm->trans_rollback();
  //       $this->cclm->trans_end();
  //       $this->status = 1108;
  //       $this->message = "Error, please try again later";
  //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  //       die();
  //     }

  //     $this->status = 200;
  //     $this->message = "Success";

  //     if ($type == 'community') {

  //       if($like_type == "like"){

  //         $existInDB = $this->cclm->getByCustomIdUserId($nation_code, $type, "dislike", $custom_id, $pelanggan->id);
  //         if(isset($existInDB->id)){

  //           $this->cclm->del($nation_code, $existInDB->id, $existInDB->type, $existInDB->like_type);
  //           // $this->ccomm->updateTotal($nation_code, $custom_id, "total_".$existInDB->like_type."s", '-', 1);

  //         }

  //         // $this->ccomm->updateTotal($nation_code, $custom_id, "total_".$like_type."s", '+', 1);
  //         $data['is_like'] = "1";

  //       }else{

  //         $existInDB = $this->cclm->getByCustomIdUserId($nation_code, $type, "like", $custom_id, $pelanggan->id);
  //         if(isset($existInDB->id)){

  //           $this->cclm->del($nation_code, $existInDB->id, $existInDB->type, $existInDB->like_type);
  //           // $this->ccomm->updateTotal($nation_code, $custom_id, "total_".$existInDB->like_type."s", '-', 1);

  //         }

  //         // $this->ccomm->updateTotal($nation_code, $custom_id, "total_".$like_type."s", '+', 1);
  //         $data['is_dislike'] = "1";

  //       }

  //     }

  //     if($type == 'community_discussion') {

  //       if($like_type == "like"){

  //         $existInDB = $this->cclm->getByCustomIdUserId($nation_code, $type, "dislike", $custom_id, $pelanggan->id);
  //         if(isset($existInDB->id)){

  //           $this->cclm->del($nation_code, $existInDB->id, $existInDB->type, $existInDB->like_type);
  //           // $this->ccdm->updateTotal($nation_code, $custom_id, "total_".$existInDB->like_type."s", '-', 1);

  //         }

  //         // $this->ccdm->updateTotal($nation_code, $custom_id, "total_".$like_type."s", '+', 1);
  //         $data['is_like'] = "1";

  //       }else{

  //         $existInDB = $this->cclm->getByCustomIdUserId($nation_code, $type, "like", $custom_id, $pelanggan->id);
  //         if(isset($existInDB->id)){

  //           $this->cclm->del($nation_code, $existInDB->id, $existInDB->type, $existInDB->like_type);
  //           // $this->ccdm->updateTotal($nation_code, $custom_id, "total_".$existInDB->like_type."s", '-', 1);

  //         }

  //         // $this->ccdm->updateTotal($nation_code, $custom_id, "total_".$like_type."s", '+', 1);
  //         $data['is_dislike'] = "1";

  //       }

  //     }

  //   }

  //   if(isset($checkLikeUnlike->id)){

  //     $res = $this->cclm->del($nation_code, $checkLikeUnlike->id, $checkLikeUnlike->type, $checkLikeUnlike->like_type);
  //     // $this->cclm->trans_commit();

  //     if (!$res) {
  //       $this->cclm->trans_rollback();
  //       $this->cclm->trans_end();
  //       $this->status = 1108;
  //       $this->message = "Error, please try again later";
  //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  //       die();
  //     }

  //     $this->status = 200;
  //     $this->message = "Success";

  //     // if ($type == 'community') {

  //     //   $this->ccomm->updateTotal($nation_code, $custom_id, "total_".$like_type."s", '-', 1);

  //     // }

  //     // if($type == 'community_discussion') {

  //     //   $this->ccdm->updateTotal($nation_code, $custom_id, "total_".$like_type."s", '-', 1);

  //     // }

  //   }

  //   $du = array();
  //   $du['total_likes'] = $this->cclm->countAll($nation_code, '', $type, $custom_id, "like");
  //   $du['total_dislikes'] = $this->cclm->countAll($nation_code, '', $type, $custom_id, "dislike");

  //   if ($type == 'community') {

  //     $checkAlreadyInDB = $this->ccflm->checkAlreadyInDB($nation_code, $custom_id);
  //     if(isset($checkAlreadyInDB->nation_code)){

  //       $du['total_likes'] += $checkAlreadyInDB->total_likes;

  //     }

  //     $this->ccomm->update($nation_code, $custom_id, $du);
  //   }

  //   if($type == 'community_discussion') {
  //     $this->ccdm->update($nation_code, $custom_id, $du);
  //   }

  //   $this->cclm->trans_commit();
  //   //end transaction
  //   $this->cclm->trans_end();

  //   if($type == 'community'){

  //     $responseData = $this->ccomm->getById($nation_code, $custom_id, array());

  //   }else{

  //     $responseData = $this->ccdm->getbyDiscussionID($nation_code, $custom_id);

  //   }

  //   $data['total_likes'] = $this->thousandsCurrencyFormat($responseData->total_likes);
  //   $data['total_dislikes'] = $this->thousandsCurrencyFormat($responseData->total_dislikes);

  //   // if($community->b_user_id_starter == $pelanggan->id){

  //   //   if($type == 'community'){
  //   //     $responseData = $this->ccomm->getById($nation_code, $custom_id, $pelanggan);
  //   //   }

  //   //   // $data['total_dislikes'] = $responseData->total_dislikes;

  //   // }

  //   //START by Donny Dennison - 16 december 2021 15:49
  //   //get point as leaderboard rule
  //   if($type == 'community' && $community->b_user_id_starter != $pelanggan->id){

  //     if(!isset($checkLikeUnlike->id)){

  //       //get limit left
  //       $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "EJ");

  //       if(!isset($limitLeft->limit_plus)){

  //         $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EJ");

  //         $lastID = $this->glplm->getLastId($nation_code, date("Y-m-d"));

  //         $du = array();
  //         $du['nation_code'] = $nation_code;
  //         $du['id'] = $lastID;
  //         $du['cdate'] = date("Y-m-d");
  //         $du['b_user_id'] = $pelanggan->id;
  //         $du['code'] = "EJ";
  //         $du['limit_plus'] = $pointGet->remark;
  //         $du['limit_minus'] = $pointGet->remark;
  //         $this->glplm->set($du);

  //         //get limit left
  //         $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "EJ");

  //       }

  //       if($limitLeft->limit_plus > 0){

  //         //get point
  //         $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EK");
  //         if (!isset($pointGet->remark)) {
  //           $pointGet = new stdClass();
  //           $pointGet->remark = 1;
  //         }

  //         $di = array();
  //         $di['nation_code'] = $nation_code;
  //         $di['b_user_alamat_location_kelurahan'] = $responseData->kelurahan;
  //         $di['b_user_alamat_location_kecamatan'] = $responseData->kecamatan;
  //         $di['b_user_alamat_location_kabkota'] = $responseData->kabkota;
  //         $di['b_user_alamat_location_provinsi'] = $responseData->provinsi;
  //         $di['b_user_id'] = $pelanggan->id;
  //         $di['point'] = $pointGet->remark;
  //         $di['custom_id'] = $custom_id;
  //         $di['custom_type'] = 'community';
  //         $di['custom_type_sub'] = 'like';
  //         $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
  //         $this->glphm->set($di);
  //         // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '+', $di['point']);
  //         $this->glplm->updateTotal($nation_code, date("Y-m-d"), $pelanggan->id, 'EJ', 'limit_plus', '-', 1);
  //       }
  //     }

  //     if(isset($checkLikeUnlike->id)){
  //       //get limit left
  //       $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "EJ");
  //       if(!isset($limitLeft->limit_minus)){
  //         $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EJ");

  //         $lastID = $this->glplm->getLastId($nation_code, date("Y-m-d"));

  //         $du = array();
  //         $du['nation_code'] = $nation_code;
  //         $du['id'] = $lastID;
  //         $du['cdate'] = date("Y-m-d");
  //         $du['b_user_id'] = $pelanggan->id;
  //         $du['code'] = "EJ";
  //         $du['limit_plus'] = $pointGet->remark;
  //         $du['limit_minus'] = $pointGet->remark;
  //         $this->glplm->set($du);

  //         //get limit left
  //         $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "EJ");

  //       }

  //       if($limitLeft->limit_minus > 0){

  //         //get point
  //         $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EK");
  //         if (!isset($pointGet->remark)) {
  //           $pointGet = new stdClass();
  //           $pointGet->remark = 1;
  //         }

  //         $di = array();
  //         $di['nation_code'] = $nation_code;
  //         $di['b_user_alamat_location_kelurahan'] = $responseData->kelurahan;
  //         $di['b_user_alamat_location_kecamatan'] = $responseData->kecamatan;
  //         $di['b_user_alamat_location_kabkota'] = $responseData->kabkota;
  //         $di['b_user_alamat_location_provinsi'] = $responseData->provinsi;
  //         $di['b_user_id'] = $pelanggan->id;
  //         $di['plusorminus'] = "-";
  //         $di['point'] = $pointGet->remark;
  //         $di['custom_id'] = $custom_id;
  //         $di['custom_type'] = 'community';
  //         $di['custom_type_sub'] = 'like';
  //         $di['custom_text'] = $pelanggan->fnama.' has un'.$di['custom_type_sub'].' '.$di['custom_type'].' and deduct '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
  //         $this->glphm->set($di);
  //         // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
  //         $this->glplm->updateTotal($nation_code, date("Y-m-d"), $pelanggan->id, 'EJ', 'limit_minus', '-', 1);
  //         $this->glplm->updateTotal($nation_code, date("Y-m-d"), $pelanggan->id, 'EJ', 'limit_plus', '+', 1);
  //       }
  //     }
  //   }
  //   //END by Donny Dennison - 16 december 2021 15:49
  //   //get point as leaderboard rule

  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  // }

  // public function baruv2()
  // {
  //   //initial
  //   $dt = $this->__init();

  //   //default result
  //   $data = array();
  //   $data['total_likes'] = "0";
  //   $data['total_dislikes'] = "0";
  //   $data['is_like'] = "0";
  //   $data['is_dislike'] = "0";

  //   //check nation_code
  //   $nation_code = $this->input->get('nation_code');
  //   $nation_code = $this->nation_check($nation_code);
  //   if (empty($nation_code)) {
  //     $this->status = 101;
  //     $this->message = 'Missing or invalid nation_code';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  //     die();
  //   }

  //   //check apisess
  //   $apisess = $this->input->get('apisess');
  //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   if (!isset($pelanggan->id)) {
  //     $this->status = 401;
  //     $this->message = 'Missing or invalid API session';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  //     die();
  //   }

  //   $this->status = 300;
  //   $this->message = 'Missing one or more parameters';
    
  //   //collect input
  //   $type = trim($this->input->post('type'));
  //   $custom_id = $this->input->post('custom_id');
  //   $like_type = (string) $this->input->post('like_type');

  //   //validating
  //   if (strlen($type)<=0) {
  //     $type = 'community';
  //   }

  //   if ($custom_id<='0') {
  //     $this->status = 1099;
  //     $this->message = 'Invalid custom ID or Community not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  //     die();
  //   }

  //   if($like_type != "dislike") {
  //     $like_type = "like";
  //   }

  //   if($type == 'community'){

  //     $community = $this->ccomm->getById($nation_code, $custom_id, array());
  //     if (!isset($community->id)) {
  //       $this->status = 1099;
  //       $this->message = 'Invalid custom ID or Community not found';
  //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  //       die();
  //     }

  //   }else{

  //     $discussion = $this->ccdm->getbyDiscussionID($nation_code, $custom_id);
  //     if (!isset($discussion->id)) {
  //       $this->status = 1099;
  //       $this->message = 'Invalid custom ID or Community not found';
  //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  //       die();
  //     }

  //     $community = $this->ccomm->getById($nation_code, $discussion->c_community_id, array());

  //   }

  //   //START by Donny Dennison - 21 november 2022 10:02
  //   //new feature, block
  //   if($pelanggan->id != $community->b_user_id_starter){

  //     $blockDataCommunity = $this->cbm->getById($nation_code, 0, $pelanggan->id, "community", $community->id);
  //     $blockDataAccount = $this->cbm->getById($nation_code, 0, $community->b_user_id_starter, "account", $pelanggan->id);
  //     $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $community->b_user_id_starter);

  //     if(isset($blockDataCommunity->block_id) || isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){

  //       $this->status = 1005;
  //       $this->message = "You can't like or dislike as you're blocked";
  //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_blocked_like");
  //       die();

  //     }

  //   }
  //   //END by Donny Dennison - 21 november 2022 10:02
  //   //new feature, block

  //   //start transaction and lock table
  //   $this->cclm->trans_start();

  //   //check like / unlike
  //   $checkLikeUnlike = $this->cclm->getByCustomIdUserId($nation_code, $type, $like_type, $custom_id, $pelanggan->id);
  //   if(!isset($checkLikeUnlike->id)){

  //     //get last id
  //     // $cclm_id = $this->cclm->getLastId($nation_code, $type);

  //     $d_address = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

  //     //initial insert with latest ID
  //     $di = array();
  //     $di['nation_code'] = $nation_code;
  //     // $di['id'] = $cclm_id;
  //     $di['type'] = $type;
  //     $di['like_type'] = $like_type;
  //     $di['custom_id'] = $custom_id;
  //     $di['b_user_id'] = $pelanggan->id;
  //     $di['alamat2'] = $d_address->alamat2;
  //     $di['kelurahan'] = $d_address->kelurahan;
  //     $di['kecamatan'] = $d_address->kecamatan;
  //     $di['kabkota'] = $d_address->kabkota;
  //     $di['provinsi'] = $d_address->provinsi;
  //     $di['negara'] = $d_address->negara;
  //     $di['kodepos'] = $d_address->kodepos;
  //     $di['cdate'] = 'NOW()';

  //     $endDoWhile = 0;
  //     do{

  //       $cclm_id = $this->cclm->getLastId($nation_code, $type);
  //       $di['id'] = $cclm_id;
  //       $checkIdAlreadyInDB = $this->cclm->checkLastId($nation_code, $di['id'], $type);

  //       if(!isset($checkIdAlreadyInDB->id)){
  //           $endDoWhile = 1;
  //       }

  //     }while($endDoWhile == 0);
  //     $res = $this->cclm->set($di);
  //     // $this->cclm->trans_commit();

  //     if (!$res) {
  //       $this->cclm->trans_rollback();
  //       $this->cclm->trans_end();
  //       $this->status = 1108;
  //       $this->message = "Error, please try again later";
  //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  //       die();
  //     }

  //     $this->status = 200;
  //     $this->message = "Success";

  //     if ($type == 'community') {

  //       if($like_type == "like"){

  //         $existInDB = $this->cclm->getByCustomIdUserId($nation_code, $type, "dislike", $custom_id, $pelanggan->id);
  //         if(isset($existInDB->id)){

  //           $this->cclm->del($nation_code, $existInDB->id, $existInDB->type, $existInDB->like_type);
  //           // $this->ccomm->updateTotal($nation_code, $custom_id, "total_".$existInDB->like_type."s", '-', 1);

  //         }

  //         // $this->ccomm->updateTotal($nation_code, $custom_id, "total_".$like_type."s", '+', 1);
  //         $data['is_like'] = "1";

  //       }else{

  //         $existInDB = $this->cclm->getByCustomIdUserId($nation_code, $type, "like", $custom_id, $pelanggan->id);
  //         if(isset($existInDB->id)){

  //           $this->cclm->del($nation_code, $existInDB->id, $existInDB->type, $existInDB->like_type);
  //           // $this->ccomm->updateTotal($nation_code, $custom_id, "total_".$existInDB->like_type."s", '-', 1);

  //         }

  //         // $this->ccomm->updateTotal($nation_code, $custom_id, "total_".$like_type."s", '+', 1);
  //         $data['is_dislike'] = "1";

  //       }

  //     }

  //     if($type == 'community_discussion') {

  //       if($like_type == "like"){

  //         $existInDB = $this->cclm->getByCustomIdUserId($nation_code, $type, "dislike", $custom_id, $pelanggan->id);
  //         if(isset($existInDB->id)){

  //           $this->cclm->del($nation_code, $existInDB->id, $existInDB->type, $existInDB->like_type);
  //           // $this->ccdm->updateTotal($nation_code, $custom_id, "total_".$existInDB->like_type."s", '-', 1);

  //         }

  //         // $this->ccdm->updateTotal($nation_code, $custom_id, "total_".$like_type."s", '+', 1);
  //         $data['is_like'] = "1";

  //       }else{

  //         $existInDB = $this->cclm->getByCustomIdUserId($nation_code, $type, "like", $custom_id, $pelanggan->id);
  //         if(isset($existInDB->id)){

  //           $this->cclm->del($nation_code, $existInDB->id, $existInDB->type, $existInDB->like_type);
  //           // $this->ccdm->updateTotal($nation_code, $custom_id, "total_".$existInDB->like_type."s", '-', 1);

  //         }

  //         // $this->ccdm->updateTotal($nation_code, $custom_id, "total_".$like_type."s", '+', 1);
  //         $data['is_dislike'] = "1";

  //       }

  //     }

  //   }

  //   if(isset($checkLikeUnlike->id)){

  //     $res = $this->cclm->del($nation_code, $checkLikeUnlike->id, $checkLikeUnlike->type, $checkLikeUnlike->like_type);
  //     // $this->cclm->trans_commit();

  //     if (!$res) {
  //       $this->cclm->trans_rollback();
  //       $this->cclm->trans_end();
  //       $this->status = 1108;
  //       $this->message = "Error, please try again later";
  //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  //       die();
  //     }

  //     $this->status = 200;
  //     $this->message = "Success";

  //     // if ($type == 'community') {

  //     //   $this->ccomm->updateTotal($nation_code, $custom_id, "total_".$like_type."s", '-', 1);

  //     // }

  //     // if($type == 'community_discussion') {

  //     //   $this->ccdm->updateTotal($nation_code, $custom_id, "total_".$like_type."s", '-', 1);

  //     // }

  //   }

  //   $du = array();
  //   $du['total_likes'] = $this->cclm->countAll($nation_code, '', $type, $custom_id, "like");
  //   $du['total_dislikes'] = $this->cclm->countAll($nation_code, '', $type, $custom_id, "dislike");

  //   if ($type == 'community') {

  //     $checkAlreadyInDB = $this->ccflm->checkAlreadyInDB($nation_code, $custom_id);
  //     if(isset($checkAlreadyInDB->nation_code)){

  //       $du['total_likes'] += $checkAlreadyInDB->total_likes;

  //     }

  //     $this->ccomm->update($nation_code, $custom_id, $du);
  //   }

  //   if($type == 'community_discussion') {
  //     $this->ccdm->update($nation_code, $custom_id, $du);
  //   }

  //   $this->cclm->trans_commit();
  //   //end transaction
  //   $this->cclm->trans_end();

  //   if($type == 'community'){

  //     $responseData = $this->ccomm->getById($nation_code, $custom_id, array());

  //   }else{

  //     $responseData = $this->ccdm->getbyDiscussionID($nation_code, $custom_id);

  //   }

  //   $data['total_likes'] = $this->thousandsCurrencyFormat($responseData->total_likes);
  //   $data['total_dislikes'] = $this->thousandsCurrencyFormat($responseData->total_dislikes);

  //   // if($community->b_user_id_starter == $pelanggan->id){

  //   //   if($type == 'community'){
  //   //     $responseData = $this->ccomm->getById($nation_code, $custom_id, $pelanggan);
  //   //   }

  //   //   // $data['total_dislikes'] = $responseData->total_dislikes;

  //   // }

  //   //START by Donny Dennison - 16 december 2021 15:49
  //   //get point as leaderboard rule
  //   if($type == 'community' && $community->b_user_id_starter != $pelanggan->id){

  //     if(!isset($checkLikeUnlike->id)){

  //       $checkAlreadyInDB = $this->cclhm->checkAlreadyInDB($nation_code, $custom_id, $pelanggan->id);
  //       if(!isset($checkAlreadyInDB->custom_id)){

  //         $di = array();
  //         $di['nation_code'] = $nation_code;
  //         $di['custom_id'] = $custom_id;
  //         $di['b_user_id'] = $pelanggan->id;
  //         $this->cclhm->set($di);

  //         //every X like/dislike can get entry ticket
  //         $divisible = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E11");
  //         if (!isset($divisible->remark)) {
  //           $divisible = new stdClass();
  //           $divisible->remark = 5;
  //         }

  //         $totalLikeDislike = $this->cclhm->countAll($nation_code, $pelanggan->id);
  //         if($totalLikeDislike % $divisible->remark == 0){

  //           $ticketGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E12");
  //           if (!isset($ticketGet->remark)) {
  //             $ticketGet = new stdClass();
  //             $ticketGet->remark = 1;
  //           }

  //           $this->bu->updateTotal($nation_code, $pelanggan->id, 'earned_ticket', '+', $ticketGet->remark);

  //           $di = array();
  //           $di['nation_code'] = $nation_code;
  //           $di['b_user_id'] = $pelanggan->id;
  //           $di['type'] = "get ticket from like or dislike";
  //           $di['total_like'] = $divisible->remark;
  //           $di['total_ticket'] = $ticketGet->remark;
  //           $di['custom_text'] = $pelanggan->fnama.' has get '.$di['total_ticket'].' tickets from '.$di['total_like'].' like or dislike';

  //           $endDoWhile = 0;
  //           do{
  //             $di['id'] = $this->GUIDv4();
  //             $checkId = $this->hthm->checkId($nation_code, $di['id']);
  //             if($checkId == 0){
  //                 $endDoWhile = 1;
  //             }
  //           }while($endDoWhile == 0);
  //           $this->hthm->set($di);

  //         }

  //       }

  //     }

  //     if(isset($checkLikeUnlike->id)){
  //       $checkAlreadyInDB = $this->cclhm->checkAlreadyInDB($nation_code, $custom_id, $pelanggan->id);
  //       if(isset($checkAlreadyInDB->custom_id)){
  //         $this->cclhm->del($nation_code, $custom_id, $pelanggan->id);

  //         //every X like/dislike can get entry ticket
  //         $divisible = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E11");
  //         if (!isset($divisible->remark)) {
  //           $divisible = new stdClass();
  //           $divisible->remark = 5;
  //         }

  //         $totalLikeDislike = $this->cclhm->countAll($nation_code, $pelanggan->id);
  //         $totalLikeDislike+=1;
  //         if($totalLikeDislike % $divisible->remark == 0){

  //           $ticketGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E12");
  //           if (!isset($ticketGet->remark)) {
  //             $ticketGet = new stdClass();
  //             $ticketGet->remark = 1;
  //           }

  //           $this->bu->updateTotal($nation_code, $pelanggan->id, 'earned_ticket', '-', $ticketGet->remark);

  //           $di = array();
  //           $di['nation_code'] = $nation_code;
  //           $di['b_user_id'] = $pelanggan->id;
  //           $di['total_like'] = $divisible->remark;
  //           $di['plusorminus'] = "-";
  //           $di['total_ticket'] = $ticketGet->remark;
  //           $di['custom_text'] = $pelanggan->fnama.' has cancel like or dislike below '.$di['total_like'].' and deduct '.$di['total_ticket'].' tickets';

  //           $endDoWhile = 0;
  //           do{
  //             $di['id'] = $this->GUIDv4();
  //             $checkId = $this->hthm->checkId($nation_code, $di['id']);
  //             if($checkId == 0){
  //                 $endDoWhile = 1;
  //             }
  //           }while($endDoWhile == 0);
  //           $this->hthm->set($di);

  //         }

  //       }

  //     }

  //   }
  //   //END by Donny Dennison - 16 december 2021 15:49
  //   //get point as leaderboard rule

  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  // }

  public function baruv3()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['total_likes'] = "0";
    $data['total_dislikes'] = "0";
    $data['is_like'] = "0";
    $data['is_dislike'] = "0";

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
      die();
    }

    $postData = $this->apikeyDecrypt($nation_code, $data, $this->input->post('samsung'), $this->input->post('nvidia'), $this->input->post('fullhd'));
    if ($postData === false) {
        $this->status = 1750;
        $this->message = 'Please check your data again';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        die();
    }
    $postData = json_decode($postData);

    $listOfPostData = array(
        "apikey",
        "apisess",
        "type",
        "custom_id",
        "like_type"
    );

    foreach($listOfPostData as $value) {
        if(!isset($postData->$value)){
            $postData->$value = "";
        }
    }

    //check apikey
    $apikey = $postData->apikey;
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
      die();
    }

    //check apisess
    $apisess = $postData->apisess;
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
      die();
    }

    $this->status = 300;
    $this->message = 'Missing one or more parameters';

    //collect input
    $type = trim($postData->type);
    $custom_id = $postData->custom_id;
    $like_type = (string) $postData->like_type;

    //validating
    if (strlen($type)<=0) {
      $type = 'community';
    }

    if ($custom_id<='0') {
      $this->status = 1099;
      $this->message = 'Invalid custom ID or Community not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
      die();
    }

    if($like_type != "dislike") {
      $like_type = "like";
    }

    if($type == 'community'){

      $community = $this->ccomm->getById($nation_code, $custom_id, array());
      if (!isset($community->id)) {
        $this->status = 1099;
        $this->message = 'Invalid custom ID or Community not found';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
        die();
      }

    }else{

      $discussion = $this->ccdm->getbyDiscussionID($nation_code, $custom_id);
      if (!isset($discussion->id)) {
        $this->status = 1099;
        $this->message = 'Invalid custom ID or Community not found';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
        die();
      }
      $community = $this->ccomm->getById($nation_code, $discussion->c_community_id, array());

    }

    //START by Donny Dennison - 21 november 2022 10:02
    //new feature, block
    if($pelanggan->id != $community->b_user_id_starter){

      $blockDataCommunity = $this->cbm->getById($nation_code, 0, $pelanggan->id, "community", $community->id);
      $blockDataAccount = $this->cbm->getById($nation_code, 0, $community->b_user_id_starter, "account", $pelanggan->id);
      $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $community->b_user_id_starter);

      if(isset($blockDataCommunity->block_id) || isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){

        $this->status = 1005;
        $this->message = "You can't like or dislike as you're blocked";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_blocked_like");
        die();

      }

    }
    //END by Donny Dennison - 21 november 2022 10:02
    //new feature, block

    //start transaction and lock table
    $this->cclm->trans_start();

    //check like / unlike
    $checkLikeUnlike = $this->cclm->getByCustomIdUserId($nation_code, $type, $like_type, $custom_id, $pelanggan->id);
    if(!isset($checkLikeUnlike->id)){

      //get last id
      // $cclm_id = $this->cclm->getLastId($nation_code, $type);

      $d_address = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

      //initial insert with latest ID
      $di = array();
      $di['nation_code'] = $nation_code;
      // $di['id'] = $cclm_id;
      $di['type'] = $type;
      $di['like_type'] = $like_type;
      $di['custom_id'] = $custom_id;
      $di['b_user_id'] = $pelanggan->id;
      $di['alamat2'] = $d_address->alamat2;
      $di['kelurahan'] = $d_address->kelurahan;
      $di['kecamatan'] = $d_address->kecamatan;
      $di['kabkota'] = $d_address->kabkota;
      $di['provinsi'] = $d_address->provinsi;
      $di['negara'] = $d_address->negara;
      $di['kodepos'] = $d_address->kodepos;
      $di['cdate'] = 'NOW()';

      $endDoWhile = 0;
      do{
        $di['id'] = $this->GUIDv4();
        $checkId = $this->cclm->checkId($nation_code, $di['id']);
        if($checkId == 0){
            $endDoWhile = 1;
        }
      }while($endDoWhile == 0);
      $res = $this->cclm->set($di);
      // $this->cclm->trans_commit();

      if (!$res) {
        $this->cclm->trans_rollback();
        $this->cclm->trans_end();
        $this->status = 1108;
        $this->message = "Error, please try again later";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
        die();
      }

      $this->status = 200;
      $this->message = "Success";

      if ($type == 'community') {

        if($like_type == "like"){

          $existInDB = $this->cclm->getByCustomIdUserId($nation_code, $type, "dislike", $custom_id, $pelanggan->id);
          if(isset($existInDB->id)){

            $this->cclm->del($nation_code, $existInDB->id, $existInDB->type, $existInDB->like_type);
            // $this->ccomm->updateTotal($nation_code, $custom_id, "total_".$existInDB->like_type."s", '-', 1);

          }

          // $this->ccomm->updateTotal($nation_code, $custom_id, "total_".$like_type."s", '+', 1);
          $data['is_like'] = "1";

        }else{

          $existInDB = $this->cclm->getByCustomIdUserId($nation_code, $type, "like", $custom_id, $pelanggan->id);
          if(isset($existInDB->id)){

            $this->cclm->del($nation_code, $existInDB->id, $existInDB->type, $existInDB->like_type);
            // $this->ccomm->updateTotal($nation_code, $custom_id, "total_".$existInDB->like_type."s", '-', 1);

          }

          // $this->ccomm->updateTotal($nation_code, $custom_id, "total_".$like_type."s", '+', 1);
          $data['is_dislike'] = "1";

        }

      }

      if($type == 'community_discussion') {

        if($like_type == "like"){

          $existInDB = $this->cclm->getByCustomIdUserId($nation_code, $type, "dislike", $custom_id, $pelanggan->id);
          if(isset($existInDB->id)){

            $this->cclm->del($nation_code, $existInDB->id, $existInDB->type, $existInDB->like_type);
            // $this->ccdm->updateTotal($nation_code, $custom_id, "total_".$existInDB->like_type."s", '-', 1);

          }

          // $this->ccdm->updateTotal($nation_code, $custom_id, "total_".$like_type."s", '+', 1);
          $data['is_like'] = "1";

        }else{

          $existInDB = $this->cclm->getByCustomIdUserId($nation_code, $type, "like", $custom_id, $pelanggan->id);
          if(isset($existInDB->id)){

            $this->cclm->del($nation_code, $existInDB->id, $existInDB->type, $existInDB->like_type);
            // $this->ccdm->updateTotal($nation_code, $custom_id, "total_".$existInDB->like_type."s", '-', 1);

          }

          // $this->ccdm->updateTotal($nation_code, $custom_id, "total_".$like_type."s", '+', 1);
          $data['is_dislike'] = "1";

        }

      }

    }

    if(isset($checkLikeUnlike->id)){

      $res = $this->cclm->del($nation_code, $checkLikeUnlike->id, $checkLikeUnlike->type, $checkLikeUnlike->like_type);
      // $this->cclm->trans_commit();

      if (!$res) {
        $this->cclm->trans_rollback();
        $this->cclm->trans_end();
        $this->status = 1108;
        $this->message = "Error, please try again later";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
        die();
      }

      $this->status = 200;
      $this->message = "Success";

      // if ($type == 'community') {

      //   $this->ccomm->updateTotal($nation_code, $custom_id, "total_".$like_type."s", '-', 1);

      // }

      // if($type == 'community_discussion') {

      //   $this->ccdm->updateTotal($nation_code, $custom_id, "total_".$like_type."s", '-', 1);

      // }

    }

    $du = array();
    $du['total_likes'] = $this->cclm->countAll($nation_code, '', $type, $custom_id, "like");
    $du['total_dislikes'] = $this->cclm->countAll($nation_code, '', $type, $custom_id, "dislike");

    if ($type == 'community') {

      $checkAlreadyInDB = $this->ccflm->checkAlreadyInDB($nation_code, $custom_id);
      if(isset($checkAlreadyInDB->nation_code)){

        $du['total_likes'] += $checkAlreadyInDB->total_likes;

      }

      $this->ccomm->update($nation_code, $custom_id, $du);
    }

    if($type == 'community_discussion') {
      $this->ccdm->update($nation_code, $custom_id, $du);
    }

    $this->cclm->trans_commit();
    //end transaction
    $this->cclm->trans_end();

    if($type == 'community'){

      $responseData = $this->ccomm->getById($nation_code, $custom_id, array());

    }else{

      $responseData = $this->ccdm->getbyDiscussionID($nation_code, $custom_id);

    }

    $data['total_likes'] = $this->thousandsCurrencyFormat($responseData->total_likes);
    $data['total_dislikes'] = $this->thousandsCurrencyFormat($responseData->total_dislikes);

    // if($community->b_user_id_starter == $pelanggan->id){

    //   if($type == 'community'){
    //     $responseData = $this->ccomm->getById($nation_code, $custom_id, $pelanggan);
    //   }

    //   // $data['total_dislikes'] = $responseData->total_dislikes;

    // }

    //START by Donny Dennison - 16 december 2021 15:49
    //get point as leaderboard rule
    if($type == 'community' && $community->b_user_id_starter != $pelanggan->id){

      if(!isset($checkLikeUnlike->id)){

        $checkAlreadyInDB = $this->cclhm->checkAlreadyInDB($nation_code, $custom_id, $pelanggan->id);
        if(!isset($checkAlreadyInDB->custom_id)){

          $di = array();
          $di['nation_code'] = $nation_code;
          $di['custom_id'] = $custom_id;
          $di['b_user_id'] = $pelanggan->id;
          $this->cclhm->set($di);

          $totalTicketGetToday = $this->hthm->sumTicket($nation_code, $pelanggan->id, "get ticket from like or dislike", date("Y-m-d"));

          $totalTicketLimit = $this->ccm->getByClassifiedAndCode($nation_code, "game", "I2");
          if (!isset($totalTicketLimit->remark)) {
            $totalTicketLimit = new stdClass();
            $totalTicketLimit->remark = 20;
          }

          if($totalTicketGetToday < $totalTicketLimit->remark){

            //every X like/dislike can get entry ticket
            $divisible = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E11");
            if (!isset($divisible->remark)) {
              $divisible = new stdClass();
              $divisible->remark = 5;
            }

            $totalLikeDislike = $this->cclhm->countAll($nation_code, $pelanggan->id);
            if($totalLikeDislike % $divisible->remark == 0){

              $ticketGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E12");
              if (!isset($ticketGet->remark)) {
                $ticketGet = new stdClass();
                $ticketGet->remark = 1;
              }

              $this->bu->updateTotal($nation_code, $pelanggan->id, 'earned_ticket', '+', $ticketGet->remark);

              $di = array();
              $di['nation_code'] = $nation_code;
              $di['b_user_id'] = $pelanggan->id;
              $di['type'] = "get ticket from like or dislike";
              $di['total_like'] = $divisible->remark;
              $di['total_ticket'] = $ticketGet->remark;
              $di['custom_text'] = $pelanggan->fnama.' has get '.$di['total_ticket'].' tickets from '.$di['total_like'].' like or dislike';

              $endDoWhile = 0;
              do{
                $di['id'] = $this->GUIDv4();
                $checkId = $this->hthm->checkId($nation_code, $di['id']);
                if($checkId == 0){
                  $endDoWhile = 1;
                }
              }while($endDoWhile == 0);
              $this->hthm->set($di);

            }

          }

        }

      }

      if(isset($checkLikeUnlike->id)){
        $checkAlreadyInDB = $this->cclhm->checkAlreadyInDB($nation_code, $custom_id, $pelanggan->id);
        if(isset($checkAlreadyInDB->custom_id)){
          $this->cclhm->del($nation_code, $custom_id, $pelanggan->id);

          //every X like/dislike can get entry ticket
          $divisible = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E11");
          if (!isset($divisible->remark)) {
            $divisible = new stdClass();
            $divisible->remark = 5;
          }

          $totalLikeDislike = $this->cclhm->countAll($nation_code, $pelanggan->id);
          $totalLikeDislike+=1;
          if($totalLikeDislike % $divisible->remark == 0){
            $totalTicketGetToday = $this->hthm->sumTicket($nation_code, $pelanggan->id, "get ticket from like or dislike", date("Y-m-d"));

            if($totalTicketGetToday > 0){
              $ticketGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E12");
              if (!isset($ticketGet->remark)) {
                $ticketGet = new stdClass();
                $ticketGet->remark = 1;
              }

              $this->bu->updateTotal($nation_code, $pelanggan->id, 'earned_ticket', '-', $ticketGet->remark);

              $di = array();
              $di['nation_code'] = $nation_code;
              $di['b_user_id'] = $pelanggan->id;
              $di['type'] = "get ticket from like or dislike";
              $di['total_like'] = $divisible->remark;
              $di['plusorminus'] = "-";
              $di['total_ticket'] = $ticketGet->remark;
              $di['custom_text'] = $pelanggan->fnama.' has cancel like or dislike below '.$di['total_like'].' and deduct '.$di['total_ticket'].' tickets';

              $endDoWhile = 0;
              do{
                $di['id'] = $this->GUIDv4();
                $checkId = $this->hthm->checkId($nation_code, $di['id']);
                if($checkId == 0){
                  $endDoWhile = 1;
                }
              }while($endDoWhile == 0);
              $this->hthm->set($di);

            }

          }

        }

      }

    }
    //END by Donny Dennison - 16 december 2021 15:49
    //get point as leaderboard rule

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like");
  }

}
