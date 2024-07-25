<?php
class Post_like extends JI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->lib("seme_log");
    $this->load("api_mobile/b_user_model", "bu");
    $this->load("api_mobile/group/i_group_post_like_model", "igplm");
    $this->load("api_mobile/group/i_group_post_like_category_model", "igplcm");
    $this->load("api_mobile/group/i_group_post_model", "igpostm");
    $this->load("api_mobile/group/i_group_post_comment_model", "igpcm");
    // $this->load("api_mobile/c_community_like_history_model", "cclhm");
    $this->load("api_mobile/group/i_group_notifications_model", "ignotifm");
    $this->load("api_mobile/common_code_model", "ccm");
    $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
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
  //     $sort_col = "$tbl_as.id";
  //   }
  //   return $sort_col;
  // }
  // private function __sortDir($sort_dir)
  // {
  //   $sort_dir = strtolower($sort_dir);
  //   if ($sort_dir == "desc") {
  //     $sort_dir = "DESC";
  //   } else {
  //     $sort_dir = "ASC";
  //   }
  //   return $sort_dir;
  // }
  // private function __page($page)
  // {
  //   if (!is_int($page)) {
  //     $page = (int) $page;
  //   }
  //   if ($page<=0) {
  //     $page = 1;
  //   }
  //   return $page;
  // }
  // private function __pageSize($page_size)
  // {
  //   $page_size = (int) $page_size;
  //   if ($page_size<=0) {
  //     $page_size = 10;
  //   }
  //   return $page_size;
  // }

  // public function index()
  // {
  //   //initial
  //   $dt = $this->__init();

  //   //default result
  //   $data = array();
  //   $data['like_total'] = 0;
  //   $data['likes'] = array();

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

  //   //by Donny Dennison - 15 february 2022 9:50
  //   //category product and category community have more than 1 language
  //   // check apisess
  //   $apisess = $this->input->get('apisess');
  //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   if (!isset($pelanggan->id)) {
  //       $pelanggan = new stdClass();
  //       if($nation_code == 62){ //indonesia
  //           $pelanggan->language_id = 2;
  //       }else if($nation_code == 82){ //korea
  //           $pelanggan->language_id = 3;
  //       }else if($nation_code == 66){ //thailand
  //           $pelanggan->language_id = 4;
  //       }else {
  //           $pelanggan->language_id = 1;
  //       }
  //   }

  //   //populate input get
  //   $sort_col = $this->input->get("sort_col");
  //   $sort_dir = $this->input->get("sort_dir");
  //   $page = $this->input->get("page");
  //   $page_size = $this->input->get("page_size");
  //   // $keyword = trim($this->input->get("keyword"));
  //   $type = trim($this->input->get("type"));
  //   $custom_id = trim($this->input->get("custom_id"));

  //   $like_type = (string) $this->input->get('like_type');

  //   if($like_type != "dislike") {
  //     $like_type = "like";
  //   }

  //   //sanitize input
  //   $tbl_as = $this->cclm->getTblAs();
  //   $tbl2_as = $this->cclm->getTbl2As();

  //   $sort_col = $this->__sortCol($sort_col, $tbl_as, $tbl2_as);
  //   $sort_dir = $this->__sortDir($sort_dir);
  //   $page = $this->__page($page);
  //   $page_size = $this->__pageSize($page_size);

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
  //   $keyword = ""; // set to 0

  //   $data['like_total'] = $this->cclm->countAll($nation_code, $keyword, $type, $custom_id, $like_type);

  //   $data['likes'] = $this->cclm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $type, $custom_id, $like_type);

  //   //manipulating data
  //   foreach ($data['likes'] as &$pd) {
  //     //convert to utf friendly
  //     if (isset($pd->b_user_nama)) {
  //       $pd->b_user_nama = $this->__dconv($pd->b_user_nama);
  //     }
      
  //     if (isset($pd->foto)) {
  //       if (empty($pd->foto)) {
  //         $pd->foto = 'media/user/default-profile-picture.png';
  //       }
  //       $pd->foto = str_replace("//", "/", $pd->foto);

  //       if(file_exists(SENEROOT.$pd->foto) && $pd->foto != 'media/user/default.png'){
  //         $pd->foto = $this->cdn_url($pd->foto);
  //       } else {
  //         $pd->foto = $this->cdn_url('media/user/default-profile-picture.png');
  //       }
  //     }
      
  //     if (isset($pd->image_icon)) {
  //       if (empty($pd->image_icon)) {
  //         $pd->image_icon = 'media/produk/default.png';
  //       }
  //       $pd->image_icon = str_replace("//", "/", $pd->image_icon);
  //       $pd->image_icon = $this->cdn_url($pd->image_icon);
  //     }

  //   }

  //   //response
  //   $this->status = 200;
  //   $this->message = 'Success';
  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  // }

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

  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  // }

  public function baru()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['is_like'] = "0";
    $data['total_likes'] = "0";
    $data['top_like_image_1'] = "";
    $data['top_like_image_2'] = "";
    $data['currentLikeEmoji'] = "";

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

    $like_category_id = trim($this->input->post('like_category_id'));

    $type = trim($this->input->post('type'));
    if (!in_array($type, array("post","discussion"))) {
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $custom_id = trim($this->input->post('custom_id'));
    if (strlen($custom_id)<3){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    if($type == "post"){
      $existInDB = $this->igpostm->getById($nation_code, $custom_id);
    }else{
      $existInDB = $this->igpcm->getbyDiscussionID($nation_code, $custom_id);
    }
    if (!isset($existInDB->b_user_id)){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //start transaction and lock table
    $this->igplm->trans_start();

    //check like / unlike
    $checkLikeUnlike = $this->igplm->getByCustomIdUserId($nation_code, $custom_id, $pelanggan->id, $type);
    if(!isset($checkLikeUnlike->id)){
      if (strlen($like_category_id)<3){
        $this->igplm->trans_rollback();
        $this->igplm->trans_end();
        $this->status = 300;
        $this->message = 'Missing one or more parameters';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      $checkLikeCategory = $this->igplcm->getById($nation_code, $like_category_id);
      if (!isset($checkLikeCategory->id)){
        $this->igplm->trans_rollback();
        $this->igplm->trans_end();
        $this->status = 300;
        $this->message = 'Missing one or more parameters';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }
      unset($checkLikeCategory);

      $di = array();
      $di['nation_code'] = $nation_code;
      $di['b_user_id'] = $pelanggan->id;
      $di['i_group_post_like_category_id'] = $like_category_id;
      $di['custom_id'] = $custom_id;
      $di['type'] = $type;
      $di['cdate'] = 'NOW()';
      $endDoWhile = 0;
      do{
        $id = $this->GUIDv4();
        $checkId = $this->igplm->checkId($nation_code, $id);
        if($checkId == 0){
          $endDoWhile = 1;
        }
      }while($endDoWhile == 0);
      $di['id'] = $id;
      $res = $this->igplm->set($di);
      if (!$res) {
        $this->igplm->trans_rollback();
        $this->igplm->trans_end();
        $this->status = 1107;
        $this->message = "Error, please try again later";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }

      $data['is_like'] = "1";

      if($type == "post" && $pelanggan->id != $existInDB->b_user_id){
        //get point
        $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E24");
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
        $di['b_user_id'] = $existInDB->b_user_id;
        $di['point'] = $pointGet->remark;
        $di['custom_id'] = $custom_id;
        $di['custom_type'] = 'club';
        $di['custom_type_sub'] = "like";
        $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' post and creator post get '.$di['point'].' point(s)';
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

    if(isset($checkLikeUnlike->id)){
      $res = $this->igplm->del($nation_code, $checkLikeUnlike->id);
      if (!$res) {
        $this->igplm->trans_rollback();
        $this->igplm->trans_end();
        $this->status = 1107;
        $this->message = "Error, please try again later";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }

      if($type == "post" && $pelanggan->id != $existInDB->b_user_id){
        //get point
        $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E24");
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
        $di['b_user_id'] = $existInDB->b_user_id;
        $di['plusorminus'] = "-";
        $di['point'] = $pointGet->remark;
        $di['custom_id'] = $custom_id;
        $di['custom_type'] = 'club';
        $di['custom_type_sub'] = "like";
        $di['custom_text'] = $pelanggan->fnama.' has un'.$di['custom_type_sub'].' '.$di['custom_type'].' post and creator post deduct '.$di['point'].' point(s)';
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

    $du = array();
    $du['total_likes'] = $this->igplm->countAll($nation_code, $custom_id, $type);
    $du['top_like_image_1'] = "";
    $du['top_like_image_2'] = "";
    $lastLike = $this->igplm->getLastLike($nation_code, $custom_id, $type);
    if(count($lastLike) > 0){
      if(isset($lastLike[0]->i_group_post_like_category_id)){
        $du['top_like_image_1'] = $lastLike[0]->i_group_post_like_category_id;
      }
      if(isset($lastLike[1]->i_group_post_like_category_id)){
        $du['top_like_image_2'] = $lastLike[1]->i_group_post_like_category_id;
      }
    }

    if($type == "post"){
      $this->igpostm->update($nation_code, $custom_id, $du);
    }else{
      $this->igpcm->update($nation_code, $custom_id, $du);
    }

    $this->igplm->trans_commit();
    $this->igplm->trans_end();

    // if(!isset($checkLikeUnlike->id) && $existInDB->b_user_id != $pelanggan->id){
    //   $user = $this->bu->getById($nation_code, $existInDB->b_user_id);

    //   if($type == "post"){
    //     $typeNotif = "band_group_post_like";
    //     $i_group_post_id = $existInDB->id;
    //     $group_name = $existInDB->group_name;
    //     $i_group_id = $existInDB->i_group_id;
    //   }else{
    //     $typeNotif = "band_group_post_comment_like";
    //     $i_group_post_id = $existInDB->i_group_post_id;
    //     $postData = $this->igpostm->getById($nation_code, $existInDB->i_group_post_id);
    //     $group_name = $postData->group_name;
    //     $i_group_id = $postData->i_group_id;
    //     unset($postData);
    //   }

    //   $dpe = array();
    //   $dpe['nation_code'] = $nation_code;
    //   $dpe['b_user_id'] = $existInDB->b_user_id;
    //   $dpe['type'] = $typeNotif;
    //   if($user->language_id == 2) {
    //     $dpe['judul'] = "Shout Baru";
    //     $dpe['teks'] =  "Anda mendapat shout";
    //   } else {
    //     $dpe['judul'] = "New Shout";
    //     $dpe['teks'] =  "You got a shout";
    //   }
    //   $dpe['gambar'] = 'media/pemberitahuan/community.png';
    //   $dpe['cdate'] = "NOW()";
    //   $extras = new stdClass();
    //   if($type != "post"){
    //     $extras->i_group_post_comment_id = $existInDB->discussion_id;
    //   }
    //   $extras->i_group_post_id = $i_group_post_id;
    //   $extras->i_group_id = $i_group_id;
    //   if($user->language_id == 2) { 
    //     $extras->judul = "Shout Baru";
    //     $extras->teks =  "Anda mendapat shout";
    //   } else {
    //     $extras->judul = "New Shout";
    //     $extras->teks =  "You got a shout";
    //   }
    //   $dpe['group_name'] = $group_name;
    //   $dpe['i_group_id'] = $i_group_id;
    //   $dpe['extras'] = json_encode($extras);
    //   $endDoWhile = 0;
    //   do{
    //     $notifId = $this->GUIDv4();
    //     $checkId = $this->ignotifm->checkId($nation_code, $notifId);
    //     if($checkId == 0){
    //       $endDoWhile = 1;
    //     }
    //   }while($endDoWhile == 0);
    //   $dpe['id'] = $notifId;
    //   $this->ignotifm->set($dpe);

    //   if ($user->is_band_push_notif == "1" && $user->is_active == 1) {
    //     if($user->device == "ios"){
    //       //push notif to ios
    //       $device = "ios"; //jenis device
    //     }else{
    //       //push notif to android
    //       $device = "android"; //jenis device
    //     }
    //     $tokens = $user->fcm_token; //device token
    //     if(!is_array($tokens)) $tokens = array($tokens);
    //     if($user->language_id == 2){
    //       $title = "Shout Baru";
    //       $message = "Anda mendapat shout";
    //     } else {
    //       $title = "New Shout";
    //       $message = "You got a shout";
    //     }
    //     $image = 'media/pemberitahuan/community.png';
    //     // $type = 'band_group_post_comment';
    //     $payload = new stdClass();
    //     if($type != "post"){
    //       $payload->i_group_post_comment_id = $existInDB->discussion_id;
    //     }
    //     $payload->i_group_post_id = $i_group_post_id;
    //     $payload->i_group_id = $i_group_id;
    //     if($user->language_id == 2) {
    //       $payload->judul = "Shout Baru";
    //       $payload->teks = "Anda mendapat shout";
    //     } else {
    //       $payload->judul = "New Shout";
    //       $payload->teks = "You got a shout";
    //     }
    //     $this->__pushNotif($device, $tokens, $title, $message, $typeNotif, $image, $payload);
    //   }
    // }

    $this->status = 200;
    $this->message = "Success";

    $data['total_likes'] = $this->thousandsCurrencyFormat($du['total_likes']);
    $data['top_like_image_1'] = $du['top_like_image_1'];
    $data['top_like_image_2'] = $du['top_like_image_2'];
    $data['currentLikeEmoji'] = $like_category_id;

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function baruv2()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['is_like'] = "0";
    $data['total_likes'] = "0";
    $data['top_like_image_1'] = "";
    $data['top_like_image_2'] = "";
    $data['currentLikeEmoji'] = "";

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

    $like_category_id = trim($this->input->post('like_category_id'));

    $type = trim($this->input->post('type'));
    if (!in_array($type, array("post","discussion"))) {
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $custom_id = trim($this->input->post('custom_id'));
    if (strlen($custom_id)<3){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    if($type == "post"){
      $existInDB = $this->igpostm->getById($nation_code, $custom_id);
    }else{
      $existInDB = $this->igpcm->getbyDiscussionID($nation_code, $custom_id);
    }
    if (!isset($existInDB->b_user_id)){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //start transaction and lock table
    $this->igplm->trans_start();

    //check like / unlike
    $checkLikeUnlike = $this->igplm->getByCustomIdUserId($nation_code, $custom_id, $pelanggan->id, $type);
    if(isset($checkLikeUnlike->id)){
      $res = $this->igplm->del($nation_code, $checkLikeUnlike->id);
      if (!$res) {
        $this->igplm->trans_rollback();
        $this->igplm->trans_end();
        $this->status = 1107;
        $this->message = "Error, please try again later";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }

      if($type == "post" && $pelanggan->id != $existInDB->b_user_id){
        //get point
        $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E24");
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
        $di['b_user_id'] = $existInDB->b_user_id;
        $di['plusorminus'] = "-";
        $di['point'] = $pointGet->remark;
        $di['custom_id'] = $custom_id;
        $di['custom_type'] = 'club';
        $di['custom_type_sub'] = "like";
        $di['custom_text'] = $pelanggan->fnama.' has un'.$di['custom_type_sub'].' '.$di['custom_type'].' post and creator post deduct '.$di['point'].' point(s)';
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

    if(!isset($checkLikeUnlike->id) || $like_category_id){
      if (strlen($like_category_id)<3){
        $this->igplm->trans_rollback();
        $this->igplm->trans_end();
        $this->status = 300;
        $this->message = 'Missing one or more parameters';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }

      $checkLikeCategory = $this->igplcm->getById($nation_code, $like_category_id);
      if (!isset($checkLikeCategory->id)){
        $this->igplm->trans_rollback();
        $this->igplm->trans_end();
        $this->status = 300;
        $this->message = 'Missing one or more parameters';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        die();
      }
      unset($checkLikeCategory);

      $di = array();
      $di['nation_code'] = $nation_code;
      $di['b_user_id'] = $pelanggan->id;
      $di['i_group_post_like_category_id'] = $like_category_id;
      $di['custom_id'] = $custom_id;
      $di['type'] = $type;
      $di['cdate'] = 'NOW()';
      $endDoWhile = 0;
      do{
        $id = $this->GUIDv4();
        $checkId = $this->igplm->checkId($nation_code, $id);
        if($checkId == 0){
          $endDoWhile = 1;
        }
      }while($endDoWhile == 0);
      $di['id'] = $id;
      $res = $this->igplm->set($di);
      if (!$res) {
        $this->igplm->trans_rollback();
        $this->igplm->trans_end();
        $this->status = 1107;
        $this->message = "Error, please try again later";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }

      $data['is_like'] = "1";

      if($type == "post" && $pelanggan->id != $existInDB->b_user_id){
        //get point
        $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E24");
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
        $di['b_user_id'] = $existInDB->b_user_id;
        $di['point'] = $pointGet->remark;
        $di['custom_id'] = $custom_id;
        $di['custom_type'] = 'club';
        $di['custom_type_sub'] = "like";
        $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' post and creator post get '.$di['point'].' point(s)';
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

    $du = array();
    $du['total_likes'] = $this->igplm->countAll($nation_code, $custom_id, $type);
    $du['top_like_image_1'] = "";
    $du['top_like_image_2'] = "";
    $lastLike = $this->igplm->getLastLike($nation_code, $custom_id, $type);
    if(count($lastLike) > 0){
      if(isset($lastLike[0]->i_group_post_like_category_id)){
        $du['top_like_image_1'] = $lastLike[0]->i_group_post_like_category_id;
      }
      if(isset($lastLike[1]->i_group_post_like_category_id)){
        $du['top_like_image_2'] = $lastLike[1]->i_group_post_like_category_id;
      }
    }

    if($type == "post"){
      $this->igpostm->update($nation_code, $custom_id, $du);
    }else{
      $this->igpcm->update($nation_code, $custom_id, $du);
    }

    $this->igplm->trans_commit();
    $this->igplm->trans_end();

    // if(!isset($checkLikeUnlike->id) && $existInDB->b_user_id != $pelanggan->id){
    //   $user = $this->bu->getById($nation_code, $existInDB->b_user_id);

    //   if($type == "post"){
    //     $typeNotif = "band_group_post_like";
    //     $i_group_post_id = $existInDB->id;
    //     $group_name = $existInDB->group_name;
    //     $i_group_id = $existInDB->i_group_id;
    //   }else{
    //     $typeNotif = "band_group_post_comment_like";
    //     $i_group_post_id = $existInDB->i_group_post_id;
    //     $postData = $this->igpostm->getById($nation_code, $existInDB->i_group_post_id);
    //     $group_name = $postData->group_name;
    //     $i_group_id = $postData->i_group_id;
    //     unset($postData);
    //   }

    //   $dpe = array();
    //   $dpe['nation_code'] = $nation_code;
    //   $dpe['b_user_id'] = $existInDB->b_user_id;
    //   $dpe['type'] = $typeNotif;
    //   if($user->language_id == 2) {
    //     $dpe['judul'] = "Shout Baru";
    //     $dpe['teks'] =  "Anda mendapat shout";
    //   } else {
    //     $dpe['judul'] = "New Shout";
    //     $dpe['teks'] =  "You got a shout";
    //   }
    //   $dpe['gambar'] = 'media/pemberitahuan/community.png';
    //   $dpe['cdate'] = "NOW()";
    //   $extras = new stdClass();
    //   if($type != "post"){
    //     $extras->i_group_post_comment_id = $existInDB->discussion_id;
    //   }
    //   $extras->i_group_post_id = $i_group_post_id;
    //   $extras->i_group_id = $i_group_id;
    //   if($user->language_id == 2) { 
    //     $extras->judul = "Shout Baru";
    //     $extras->teks =  "Anda mendapat shout";
    //   } else {
    //     $extras->judul = "New Shout";
    //     $extras->teks =  "You got a shout";
    //   }
    //   $dpe['group_name'] = $group_name;
    //   $dpe['i_group_id'] = $i_group_id;
    //   $dpe['extras'] = json_encode($extras);
    //   $endDoWhile = 0;
    //   do{
    //     $notifId = $this->GUIDv4();
    //     $checkId = $this->ignotifm->checkId($nation_code, $notifId);
    //     if($checkId == 0){
    //       $endDoWhile = 1;
    //     }
    //   }while($endDoWhile == 0);
    //   $dpe['id'] = $notifId;
    //   $this->ignotifm->set($dpe);

    //   if ($user->is_band_push_notif == "1" && $user->is_active == 1) {
    //     if($user->device == "ios"){
    //       //push notif to ios
    //       $device = "ios"; //jenis device
    //     }else{
    //       //push notif to android
    //       $device = "android"; //jenis device
    //     }
    //     $tokens = $user->fcm_token; //device token
    //     if(!is_array($tokens)) $tokens = array($tokens);
    //     if($user->language_id == 2){
    //       $title = "Shout Baru";
    //       $message = "Anda mendapat shout";
    //     } else {
    //       $title = "New Shout";
    //       $message = "You got a shout";
    //     }
    //     $image = 'media/pemberitahuan/community.png';
    //     // $type = 'band_group_post_comment';
    //     $payload = new stdClass();
    //     if($type != "post"){
    //       $payload->i_group_post_comment_id = $existInDB->discussion_id;
    //     }
    //     $payload->i_group_post_id = $i_group_post_id;
    //     $payload->i_group_id = $i_group_id;
    //     if($user->language_id == 2) {
    //       $payload->judul = "Shout Baru";
    //       $payload->teks = "Anda mendapat shout";
    //     } else {
    //       $payload->judul = "New Shout";
    //       $payload->teks = "You got a shout";
    //     }
    //     $this->__pushNotif($device, $tokens, $title, $message, $typeNotif, $image, $payload);
    //   }
    // }

    $this->status = 200;
    $this->message = "Success";

    $data['total_likes'] = $this->thousandsCurrencyFormat($du['total_likes']);
    $data['top_like_image_1'] = $du['top_like_image_1'];
    $data['top_like_image_2'] = $du['top_like_image_2'];
    $data['currentLikeEmoji'] = $like_category_id;

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }
}
