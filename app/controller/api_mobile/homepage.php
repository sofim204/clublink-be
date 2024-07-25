<?php

require_once (SENEROOT.'/vendor/autoload.php');
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class Homepage extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_mobile/b_kategori_model3", 'bk');
        $this->load("api_mobile/c_produk_model", 'cp');
        $this->load("api_mobile/c_promo_model", "cp2");

        //by Donny Dennison - 28 august 2020 15:14
        //add new api for best shop in homepage
        $this->load("api_mobile/d_order_detail_model", "dodm");
        $this->load("api_mobile/common_code_model","ccm");

        //by Donny Dennison - 24 november 2021 9:45
        //add feature highlight community & leaderboard point & hot item
        // $this->load("api_mobile/g_highlight_community_model", "ghcm");
        $this->load("api_mobile/c_community_attachment_model", "ccam");
        $this->load("api_mobile/c_community_category_model", "cccm");
        $this->load("api_mobile/c_community_model", "ccomm");
        // $this->load("api_mobile/g_leaderboard_point_area_model", 'glpam');
 
        //by Donny Dennison - 6 december 2021 17:02
        //add weather api
        $this->load("api_mobile/b_user_model", "bu");
        $this->load("api_mobile/b_user_alamat_model", 'bua');
        $this->load("api_mobile/g_air_quality_index_model", "gaqim");

        //by Donny Dennison - 3 january 2021 13:52
        //add event banner homepage
        $this->load("api_mobile/c_event_banner_model", "cebm");

        $this->load("api_mobile/g_leaderboard_ranking_model", 'glrm');

        //by Donny Dennison - 22 july 2022 10:45
        //add response parameter have_video in api product, product/detail, homepage, seller/product, product_automotive, wishlist
        $this->load("api_mobile/c_produk_foto_model", "cpfm");

        //by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account
        $this->load("api_mobile/c_block_model", "cbm");

        $this->load("api_mobile/b_user_follow_model", 'buf');
        $this->load("api_mobile/c_community_like_model", "cclm");
        $this->load("api_mobile/g_leaderboard_point_total_model", "glptm");
        $this->load("api_mobile/group/i_group_participant_model", "igparticipantm");
        $this->load("api_mobile/group/i_group_model", "igm");
        $this->load("api_mobile/group/i_group_attachment_model", 'igam');
        $this->load("api_mobile/c_homepage_main_popular_model", 'chmpm');
    }
    
    //by Donny Dennison - 6 december 2021 17:02
    //add weather api
    private function __getWeatherApi($parameter)
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $this->weather_api_host.'current.json?key='.$this->weather_api_key.'&aqi=yes&q='.$parameter);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->seme_log->write("api_mobile", "__getWeatherApi -> ".curl_error($ch));
            return 0;
            //echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        
        return $result;
    }

    //START by Donny Dennison - 10 october 2022 10:45
    //integrate api blockchain
    // private function __callBlockChainSPTBalance($userWalletCode){
    //     // $ch = curl_init();
    //     // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     // curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    //     // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    //     // curl_setopt($ch, CURLOPT_URL, $this->blockchain_api_host."Wallet/GetSPTBalanceWithEncryption");

    //     // $headers = array();
    //     // $headers[] = 'Content-Type:  application/json';
    //     // $headers[] = 'Accept:  application/json';
    //     // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //     $postdata = json_encode(array(
    //       'userWalletCode' => $this->__encryptdecrypt($userWalletCode,"encrypt"),
    //       'countryIsoCode' => $this->blockchain_api_country
    //     ));
    //     // curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

    //     // $result = curl_exec($ch);
    //     // if (curl_errno($ch)) {
    //     //   return 0;
    //     //   //echo 'Error:' . curl_error($ch);
    //     // }
    //     // curl_close($ch);

    //     // $client = new Client([
    //     //   'base_uri' => $this->blockchain_api_host,
    //     //   'headers' => array(
    //     //         "Content-Type" => "application/json",
    //     //         "Accept" => "application/json"
    //     //     ),
    //     //   // default timeout 5 detik
    //     //   'timeout'  => 5,
    //     // ]);

    //     // $response = $client->request('POST', "Wallet/GetSPTBalanceWithEncryption", ['body'=>$postdata]);
    //     // $result = $response->getBody();
    //     try {
    //         $client = new Client([
    //             'base_uri' => $this->blockchain_api_host,
    //             'headers' => array(
    //                 "Content-Type" => "application/json",
    //                 "Accept" => "application/json"
    //             ),
    //             // default timeout 5 detik
    //             'timeout'  => 5,
    //         ]);

    //         //https://stackoverflow.com/a/54624802/7578520
    //         $promise = $client->postAsync("Wallet/GetSPTBalanceWithEncryption", ['body'=>$postdata])->then(
    //             function (ResponseInterface $res) {
    //                 $response = $res->getBody()->getContents();

    //                 return $response;
    //             }
    //         );
    //         $result = $promise->wait();
    //         // echo $result;

    //         $this->seme_log->write("api_mobile", "url untuk block chain server ". $this->blockchain_api_host."Wallet/GetSPTBalanceWithEncryption. data send to blockchain api ". $postdata.". isi response block chain server ". $result);

    //         return $result;
    //     } catch (ClientException $e) {
    //         $this->seme_log->write("api_mobile", "url untuk block chain server ". $this->blockchain_api_host."Wallet/GetSPTBalanceWithEncryption. data send to blockchain api ". $postdata.". isi response block chain server ". $e->getMessage());

    //         return $e->getCode();
    //     }
    // }

    //credit :
    //https://stackoverflow.com/a/35289156/7578520
    //https://stackoverflow.com/a/29560553/7578520
    // private function __encryptdecrypt($text, $type="encrypt"){
    //     if($type == "encrypt"){
    //         // Encrypt using the public key
    //         openssl_public_encrypt($text, $encrypted, $this->blockchain_api_public_key);
    //         return base64_encode($encrypted);
    //     }else if($type == "decrypt"){
    //         // Decrypt the data using the private key
    //         openssl_private_decrypt(base64_decode($text), $decrypted, openssl_pkey_get_private($this->blockchain_api_private_key, $this->blockchain_api_private_key_password));
    //         return $decrypted;
    //     }
    // }
    //END by Donny Dennison - 10 october 2022 10:45
    //integrate api blockchain

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

    //START by Donny Dennison - 24 november 2021 9:45
    //add feature highlight community & leaderboard point & hot item
    private function __sortCol($sort_col, $tbl_as)
    {
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
    //END by Donny Dennison - 24 november 2021 9:45

    // public function index()
    // {
    //     //initial
    //     $dt = $this->__init();

    //     //default result
    //     $data = array();
    //     $data['freeproducts'] = array();
    //     $data['produk_count'] = 0;
    //     $data['kategori'] = array();
    //     $data['banner'] = array();
    //     $data['produk'] = array();
                
    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    //         die();
    //     }
        
    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    //         die();
    //     }

    //     //collect input
    //     $page = (int) $this->input->get("page");
    //     $page_size = (int) $this->input->get("page_size");
    //     $sort_col = strtolower($this->input->get("sort_col"));
    //     $sort_dir = strtolower($this->input->get("sort_dir"));
    //     $keyword = strtolower($this->input->get("keyword"));
    //     if (empty($keyword)) {
    //         $keyword = '';
    //     }

    //     //input validation
    //     if ($page<=0) {
    //         $page = 1;
    //     }
    //     if ($page_size<=0) {
    //         $page_size = 12;
    //     }
    //     switch ($sort_col) {
    //         case "nama":
    //             $sort_col = "nama";
    //             break;
    //         case "harga_jual":
    //             $sort_col = "harga_jual";
    //             break;
    //         case "kondisi":
    //             $sort_col = "b_kondisi_id";
    //             break;
    //         default:
    //             //by Donny Dennison - 13-07-2020 16:08
    //             //edit stok allow 0 and if edit stok more than 0 then change cdate to newest
    //             // $sort_col = "id";
    //             $sort_col = "cdate";
                
    //     }
    //     switch ($sort_dir) {
    //         case "asc":
    //             $sort_dir = "asc";
    //             break;
    //         default:
    //             $sort_dir = "desc";
    //     }

    //     //advanced filter
    //     $harga_jual_min = '';
    //     if (isset($_GET['harga_jual_min'])) {
    //         $harga_jual_min = (int) $_GET['harga_jual_min'];
    //         if ($harga_jual_min<=-1) {
    //             $harga_jual_min = '';
    //         }
    //     }
    //     if ($harga_jual_min>=0) {
    //         $harga_jual_min = (float) $harga_jual_min;
    //     }

    //     $harga_jual_max = (int) $this->input->get("harga_jual_max");
    //     if ($harga_jual_max<=0) {
    //         $harga_jual_max = "";
    //     }
    //     if ($harga_jual_max>0) {
    //         $harga_jual_max = (float) $harga_jual_max;
    //     }

    //     $b_kondisi_ids = "";
    //     if (isset($_GET['b_kondisi_ids'])) {
    //         $b_kondisi_ids = $_GET['b_kondisi_ids'];
    //     }
    //     if (strlen($b_kondisi_ids)>0) {
    //         $b_kondisi_ids = rtrim($b_kondisi_ids, ",");
    //         $b_kondisi_ids = explode(",", $b_kondisi_ids);
    //         if (count($b_kondisi_ids)) {
    //             $kons = array();
    //             foreach ($b_kondisi_ids as &$bks) {
    //                 $bks = (int) trim($bks);
    //                 if ($bks>0) {
    //                     $kons[] = $bks;
    //                 }
    //             }
    //             $b_kondisi_ids = $kons;
    //         } else {
    //             $b_kondisi_ids = array();
    //         }
    //     } else {
    //         $b_kondisi_ids = array();
    //     }

    //     $b_kategori_ids = "";
    //     if (isset($_GET['b_kategori_ids'])) {
    //         $b_kategori_ids = $_GET['b_kategori_ids'];
    //     }
    //     if (strlen($b_kategori_ids)>0) {
    //         $b_kategori_ids = rtrim($b_kategori_ids, ",");
    //         $b_kategori_ids = explode(",", $b_kategori_ids);
    //         if (count($b_kategori_ids)) {
    //             $kods = array();
    //             foreach ($b_kategori_ids as &$bki) {
    //                 $bki = (int) trim($bki);
    //                 if ($bki>0) {
    //                     $kods[] = $bki;
    //                 }
    //             }
    //             $b_kategori_ids = $kods;
    //         } else {
    //             $b_kategori_ids = array();
    //         }
    //     } else {
    //         $b_kategori_ids = array();
    //     }

    //     $kecamatan = $this->input->get("kecamatan");
    //     if (strlen($kecamatan)) {
    //         $kecamatan = "";
    //     }

    //     //end advanced filter
    //     $data['page'] = $page;
    //     $data['page_size'] = $page_size;
    //     $data['sort_col'] = $sort_col;
    //     $data['sort_dir'] = $sort_dir;
    //     $data['produk_count'] = $this->cp->countHomePage($nation_code, $keyword="", $harga_jual_min, $harga_jual_max, $b_kondisi_ids, $b_kategori_ids, $kecamatan="");
    //     $produk = $this->cp->getHomePage($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword="", $harga_jual_min, $harga_jual_max, $b_kondisi_ids, $b_kategori_ids, $kecamatan="");
    //     $kategori = $this->bk->getHomepage($nation_code, $page=0, $pageSize=32, $sortcol="nama", $sortdir="asc");
    //     $banner = $this->cp2->getHomepage($nation_code);
        
    //     //manipulator
    //     $data['produk'] = array();
    //     foreach ($produk as $pd) {
    //         if (isset($pd->nama)) {
    //             $pd->nama = $this->__dconv($pd->nama);
    //         }
    //         if (isset($pd->brand)) {
    //             $pd->brand = $this->__dconv($pd->brand);
    //         }
    //         if (isset($pd->b_user_fnama_seller)) {
    //             $pd->b_user_fnama_seller = $this->__dconv($pd->b_user_fnama_seller);
    //         }
            
    //         if (isset($pd->b_user_image_seller)) {
    //             if (empty($pd->b_user_image_seller)) {
    //                 $pd->b_user_image_seller = 'media/produk/default.png';
    //             }
                
    //             // by Muhammad Sofi - 28 October 2021 11:00
    //             // if user img & banner not exist or empty, change to default image
    //             // $pd->b_user_image_seller = $this->cdn_url($pd->b_user_image_seller);
    //             if(file_exists(SENEROOT.$pd->b_user_image_seller) && $pd->b_user_image_seller != 'media/user/default.png'){
    //                 $pd->b_user_image_seller = $this->cdn_url($pd->b_user_image_seller);
    //             } else {
    //                 $pd->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
    //             }
    //         }
    //         if (isset($pd->b_kondisi_icon)) {
    //             if (empty($pd->b_kondisi_icon)) {
    //                 $pd->b_kondisi_icon = 'media/produk/default.png';
    //             }
    //             $pd->b_kondisi_icon = $this->cdn_url($pd->b_kondisi_icon);
    //         }
    //         if (isset($pd->b_berat_icon)) {
    //             if (empty($pd->b_berat_icon)) {
    //                 $pd->b_berat_icon = 'media/produk/default.png';
    //             }
    //             $pd->b_berat_icon = $this->cdn_url($pd->b_berat_icon);
    //         }
    //         if (isset($pd->thumb)) {
    //             if (empty($pd->thumb)) {
    //                 $pd->thumb = 'media/produk/default.png';
    //             }
    //             $pd->thumb = $this->cdn_url($pd->thumb);
    //         }
    //         if (isset($pd->foto)) {
    //             if (empty($pd->foto)) {
    //                 $pd->foto = 'media/produk/default.png';
    //             }
    //             $pd->foto = $this->cdn_url($pd->foto);
    //         }
    //         $data['produk'][] = $pd;
    //     }
    //     unset($produk,$pd);
                
    //     $data['kategori'] = array();
    //     foreach ($kategori as $kat) {
    //         if (isset($kat->image_icon)) {
    //             if (strlen($kat->image_icon)<=4) {
    //                 $kat->image_icon = "media/kategori/default-icon.png";
    //             }
    //         }
    //         if (isset($kat->image_cover)) {
    //             if (strlen($kat->image_cover)<=4) {
    //                 $kat->image_cover = "media/kategori/default-cover.png";
    //             }
    //         }
    //         if (isset($kat->image)) {
    //             if (strlen($kat->image)<=4) {
    //                 $kat->image = "media/kategori/default.png";
    //             }
    //         }
    //         if (isset($kat->image_icon)) {
    //             $kat->image_icon = $this->cdn_url($kat->image_icon);
    //         }
    //         if (isset($kat->image_cover)) {
    //             $kat->image_cover = $this->cdn_url($kat->image_cover);
    //         }
    //         if (isset($kat->image)) {
    //             $kat->image = $this->cdn_url($kat->image);
    //         }
    //         $data['kategori'][] = $kat;
    //     }
    //     unset($kategori,$kat);
        
    //     $data['banner'] = array();
    //     foreach ($banner as $bn) {
    //         if (isset($bn->image)) {
    //             if (strlen($bn->image)<=4) {
    //                 $bn->image = 'media/promo/default.png';
    //             }
                
    //             // by Muhammad Sofi - 28 October 2021 11:00
    //             // if user img & banner not exist or empty, change to default image
    //             // $bn->image = $this->cdn_url($bn->image);
    //             if(file_exists(SENEROOT.$bn->image)){
    //                 $bn->image = $this->cdn_url($bn->image);
    //             } else {
    //                 $bn->image = $this->cdn_url('media/user/default.png');
    //             }
    //         }
    //         $data['banner'][] = $bn;
    //     }
    //     unset($banner,$bn);
        
    //     $data['freeproducts'] = array();
    //     $fps = $this->cfp->getHomepage($nation_code, 10);
    //     foreach ($fps as $fp) {
    //         if (isset($fp->b_user_image_seller)) {
    //             if (strlen($fp->b_user_image_seller)<=4) {
    //                 $fp->b_user_image_seller = 'media/user/default.png';
    //             }
                
    //             // by Muhammad Sofi - 28 October 2021 11:00
    //             // if user img & banner not exist or empty, change to default image
    //             // $fp->b_user_image_seller = $this->cdn_url($fp->b_user_image_seller);
    //             if(file_exists(SENEROOT.$fp->b_user_image_seller) && $fp->b_user_image_seller != 'media/user/default.png'){
    //                 $fp->b_user_image_seller = $this->cdn_url($fp->b_user_image_seller);
    //             } else {
    //                 $fp->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
    //             }
    //         }
    //         if (isset($fp->foto)) {
    //             if (strlen($fp->foto)<=4) {
    //                 $fp->foto = 'media/produk/default.png';
    //             }
    //             $fp->foto = $this->cdn_url($fp->foto);
    //         }
    //         if (isset($fp->thumb)) {
    //             if (strlen($fp->thumb)<=4) {
    //                 $fp->thumb = 'media/produk/default.png';
    //             }
    //             $fp->thumb = $this->cdn_url($fp->thumb);
    //         }
    //         $data['freeproducts'][] = $fp;
    //     }
    //     unset($fps,$fp);

    //     $this->status = 200;
    //     $this->message = 'Success';
                
    //     //ini_set('memory_limit', -1);
    //     ini_set('max_execution_time', 0);
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    // }

    //by Donny Dennison - 24 november 2021 9:45
    //add feature highlight community & leaderboard point & hot item
    public function index()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['highlight_community'] = array();
        $data['event_banner'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
            die();
        }

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

        $provinsi = trim($this->input->get("provinsi"));

        $type = $this->input->get("type");
        if (strlen($type)<=0 || empty($type)){
          $type="All";
        }

        if($type == 'sameStreet'){
            $type = 'neighborhood';
        }

        $kelurahan = 'All';
        $kecamatan = 'All';
        $kabkota = 'All';

        if (!$provinsi || $provinsi == "") {
            if(isset($pelanggan->id)){
                $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
                if($type == 'neighborhood'){
                    $kelurahan = $pelangganAddress->kelurahan;
                    $kecamatan = $pelangganAddress->kecamatan;
                    $kabkota = $pelangganAddress->kabkota;
                    $provinsi = $pelangganAddress->provinsi;
                }else if($type == 'district'){
                    $kecamatan = $pelangganAddress->kecamatan;
                    $kabkota = $pelangganAddress->kabkota;
                    $provinsi = $pelangganAddress->provinsi;
                }else if($type == 'city'){
                    $kabkota = $pelangganAddress->kabkota;
                    $provinsi = $pelangganAddress->provinsi;
                }else if($type == 'province'){
                    $provinsi = $pelangganAddress->provinsi;
                }else{
                    $provinsi = 'All';
                }
            }else{
                $provinsi = 'All';
            }
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

        // if($provinsi != "All" && date("Y-m-d") <= "2022-08-31"){
        //     //by Donny Dennison - 29 july 2022 13:22
        //     //new feature, block community post or account
        //     // $data['highlight_community'] = $this->ghcm->getAllByLocation($nation_code, 'All', 'All', 'All', 'All');
        //     $data['highlight_community'] = $this->ghcm->getAllByLocation($nation_code, 'All', 'All', 'All', 'All', $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse);
        // }else{
        //     //by Donny Dennison - 29 july 2022 13:22
        //     //new feature, block community post or account
        //     // $data['highlight_community'] = $this->ghcm->getAllByLocation($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi);
        //     $data['highlight_community'] = $this->ghcm->getAllByLocation($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi, $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse);
        // }

        $data['highlight_community'] = $this->ccomm->getAll($nation_code, 1, 6, "cc.cdate", "DESC", "", array(), "", array(), "", $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, "normal", 1);
        foreach ($data['highlight_community'] as &$pd) {
            $pd->title = html_entity_decode($pd->title,ENT_QUOTES);

            $pd->images = array();

            // $attachment = $this->ccam->getByCommunityId($nation_code, $pd->c_community_id,"first", "image");
            $attachment = $this->ccam->getByCommunityId($nation_code, $pd->id,"first", "image");
            // $attachmentVideo = $this->ccam->getByCommunityId($nation_code, $pd->c_community_id,"first", "video");
            $attachmentVideo = $this->ccam->getByCommunityId($nation_code, $pd->id,"first", "video");
            if(isset($attachment->id)){
              // if (empty($attachment->url)) {
              //   $attachment->url = 'media/community_default.png';
              // }
              // if (empty($attachment->url_thumb)) {
              //   $attachment->url_thumb = 'media/community_default.png';
              // }
                if (empty($attachment->url_thumb)) {
                    $categoryData = $this->cccm->getById($nation_code, $pd->c_community_category_id);
                    $attachment->url = $categoryData->image_cover;
                    $attachment->url_thumb = $categoryData->image_cover;
                }

                $attachment->url = $this->cdn_url($attachment->url);
                $attachment->url_thumb = $this->cdn_url($attachment->url_thumb);
            }else if(isset($attachmentVideo->id)){
                $attachment = $attachmentVideo;

              // if (empty($attachment->url)) {
              //   $attachment->url = 'media/community_default.png';
              // }
              // if (empty($attachment->url_thumb)) {
              //   $attachment->url_thumb = 'media/community_default.png';
              // }
                if (empty($attachment->url_thumb)) {
                    $categoryData = $this->cccm->getById($nation_code, $pd->c_community_category_id);
                    $attachment->url = $categoryData->image_cover;
                    $attachment->url_thumb = $categoryData->image_cover;
                }

                $attachment->url = $this->cdn_url($attachment->url);
                $attachment->url_thumb = $this->cdn_url($attachment->url_thumb);
            }else{
                // $attachment = new stdClass();
                // $attachment->url = 'media/community_default.png';
                // $attachment->url_thumb = 'media/community_default.png';

                $attachment = $this->cccm->getById($nation_code, $pd->c_community_category_id);

                $attachment->url = $this->cdn_url($attachment->image_cover);
                $attachment->url_thumb = $this->cdn_url($attachment->image_cover);
            }

            $pd->images[] = $attachment;
            unset($attachment);
        }

        // random order index by ali -- 25 jan 2023
        $arr = $this->cebm->getAll($nation_code);
        if (count($arr) > 0) {
            $rand = rand(1, count($arr));
            $last_element = $arr[count($arr) - $rand];

            for($i = count($arr) - $rand; $i > 0; $i--) {
                $arr[$i] = $arr[$i - 1];
            }

            $arr[0] = $last_element;
        }
        // end

        $data['event_banner'] = $arr;
        foreach ($data['event_banner'] as &$banner) {
            $banner->url = $this->cdn_url($banner->url);
            $banner->img_thumbnail = $this->cdn_url($banner->img_thumbnail);

            if($banner->type_event_banner == "webview"){
                $banner->teks .= (($pelanggan->language_id == 2) ? "ID" : "EN")."/".((isset($pelanggan->id)) ? $pelanggan->id : "")."/"; 
            }
        }

        $this->status = 200;
        $this->message = 'Success';

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    }

    //START by Donny Dennison - 19 december 2022 12:50
    //separate homepage api
    public function whatsuptown()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['highlight_community'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
            die();
        }

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

        $provinsi = trim($this->input->get("provinsi"));

        $type = $this->input->get("type");
        if (strlen($type)<=0 || empty($type)){
          $type="All";
        }

        if($type == 'sameStreet'){
            $type = 'neighborhood';
        }

        $kelurahan = 'All';
        $kecamatan = 'All';
        $kabkota = 'All';

        if (!$provinsi || $provinsi == "") {
            if(isset($pelanggan->id)){
                $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
                if($type == 'neighborhood'){
                    $kelurahan = $pelangganAddress->kelurahan;
                    $kecamatan = $pelangganAddress->kecamatan;
                    $kabkota = $pelangganAddress->kabkota;
                    $provinsi = $pelangganAddress->provinsi;
                }else if($type == 'district'){
                    $kecamatan = $pelangganAddress->kecamatan;
                    $kabkota = $pelangganAddress->kabkota;
                    $provinsi = $pelangganAddress->provinsi;
                }else if($type == 'city'){
                    $kabkota = $pelangganAddress->kabkota;
                    $provinsi = $pelangganAddress->provinsi;
                }else if($type == 'province'){
                    $provinsi = $pelangganAddress->provinsi;
                }else{
                    $provinsi = 'All';
                }
            }else{
                $provinsi = 'All';
            }
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

        // if($provinsi != "All" && date("Y-m-d") <= "2022-08-31"){
        //     //by Donny Dennison - 29 july 2022 13:22
        //     //new feature, block community post or account
        //     // $data['highlight_community'] = $this->ghcm->getAllByLocation($nation_code, 'All', 'All', 'All', 'All');
        //     $data['highlight_community'] = $this->ghcm->getAllByLocation($nation_code, 'All', 'All', 'All', 'All', $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse);
        // }else{
        //     //by Donny Dennison - 29 july 2022 13:22
        //     //new feature, block community post or account
        //     // $data['highlight_community'] = $this->ghcm->getAllByLocation($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi);
        //     $data['highlight_community'] = $this->ghcm->getAllByLocation($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi, $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse);
        // }

        $data['highlight_community'] = $this->ccomm->getAll($nation_code, 1, 6, "cc.cdate", "DESC", "", array(), "", array(), "", $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, "normal", 1);
        foreach ($data['highlight_community'] as &$pd) {
            $pd->title = html_entity_decode($pd->title,ENT_QUOTES);

            $pd->images = array();

            // $attachment = $this->ccam->getByCommunityId($nation_code, $pd->c_community_id,"first", "image");
            $attachment = $this->ccam->getByCommunityId($nation_code, $pd->id,"first", "image");
            // $attachmentVideo = $this->ccam->getByCommunityId($nation_code, $pd->c_community_id,"first", "video");
            $attachmentVideo = $this->ccam->getByCommunityId($nation_code, $pd->id,"first", "video");
            if(isset($attachment->id)){
              // if (empty($attachment->url)) {
              //   $attachment->url = 'media/community_default.png';
              // }
              // if (empty($attachment->url_thumb)) {
              //   $attachment->url_thumb = 'media/community_default.png';
              // }
                if (empty($attachment->url_thumb)) {
                    $categoryData = $this->cccm->getById($nation_code, $pd->c_community_category_id);
                    $attachment->url = $categoryData->image_cover;
                    $attachment->url_thumb = $categoryData->image_cover;
                }

                $attachment->url = $this->cdn_url($attachment->url);
                $attachment->url_thumb = $this->cdn_url($attachment->url_thumb);
            }else if(isset($attachmentVideo->id)){
                $attachment = $attachmentVideo;

              // if (empty($attachment->url)) {
              //   $attachment->url = 'media/community_default.png';
              // }
              // if (empty($attachment->url_thumb)) {
              //   $attachment->url_thumb = 'media/community_default.png';
              // }
                if (empty($attachment->url_thumb)) {
                    $categoryData = $this->cccm->getById($nation_code, $pd->c_community_category_id);
                    $attachment->url = $categoryData->image_cover;
                    $attachment->url_thumb = $categoryData->image_cover;
                }

                $attachment->url = $this->cdn_url($attachment->url);
                $attachment->url_thumb = $this->cdn_url($attachment->url_thumb);
            }else{
                // $attachment = new stdClass();
                // $attachment->url = 'media/community_default.png';
                // $attachment->url_thumb = 'media/community_default.png';

                $attachment = $this->cccm->getById($nation_code, $pd->c_community_category_id);

                $attachment->url = $this->cdn_url($attachment->image_cover);
                $attachment->url_thumb = $this->cdn_url($attachment->image_cover);
            }

            $pd->images[] = $attachment;
            unset($attachment);
        }

        $this->status = 200;
        $this->message = 'Success';

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    }

    public function eventbanner()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['event_banner'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
            die();
        }

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

        // random order index by ali -- 25 jan 2023
        $arr = $this->cebm->getAllNew($nation_code);

        $priorityOne = array();
        if(isset($arr[0]->priority)){
            if($arr[0]->priority == "1"){
                $priorityOne = $arr[0];
                unset($arr[0]);
                $arr = array_values($arr);
            }
        }
        unset($banner);

        $priorityTwo = array();
        if(isset($arr[0]->priority)){
            if($arr[0]->priority == "2"){
                $priorityTwo = $arr[0];
                unset($arr[0]);
                $arr = array_values($arr);
            }
        }
        unset($banner);

        if (count($arr) > 0) {
            $rand = rand(1, count($arr));
            $last_element = $arr[count($arr) - $rand];

            for($i = count($arr) - $rand; $i > 0; $i--) {
                $arr[$i] = $arr[$i - 1];
            }

            $arr[0] = $last_element;
        }
        // end

        if($priorityTwo){
            array_unshift($arr, $priorityTwo);
        }

        if($priorityOne){
            array_unshift($arr, $priorityOne);
        }

        $data['event_banner'] = $arr;
        foreach ($data['event_banner'] as &$banner) {
            $banner->url = $this->cdn_url($banner->url);
            $banner->img_thumbnail = $this->cdn_url($banner->img_thumbnail);

            if($banner->type_event_banner == "webview"){
                $banner->teks .= (($pelanggan->language_id == 2) ? "ID" : "EN")."/".((isset($pelanggan->id)) ? $pelanggan->id : "")."/"; 
            }
        }

        $this->status = 200;
        $this->message = 'Success';

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    }

    public function leaderboard_ranking()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['leaderboard'] = array();
        $data['leaderboard_my_ranking'] = 'N/A';
        $data['leaderboard_my_point'] = '0';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
            die();
        }

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

        $provinsi = trim($this->input->get("provinsi"));

        // $type = $this->input->get("type");
        // if (strlen($type)<=0 || empty($type)){
          $type="All";
        // }

        // if($type == 'sameStreet'){
        //     $type = 'neighborhood';
        // }

        $kelurahan = 'All';
        $kecamatan = 'All';
        $kabkota = 'All';

        // if (!$provinsi || $provinsi == "") {
        //     if(isset($pelanggan->id)){
        //         $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

        //         if($type == 'neighborhood'){
        //             $kelurahan = $pelangganAddress->kelurahan;
        //             $kecamatan = $pelangganAddress->kecamatan;
        //             $kabkota = $pelangganAddress->kabkota;
        //             $provinsi = $pelangganAddress->provinsi;
        //         }else if($type == 'district'){
        //             $kecamatan = $pelangganAddress->kecamatan;
        //             $kabkota = $pelangganAddress->kabkota;
        //             $provinsi = $pelangganAddress->provinsi;
        //         }else if($type == 'city'){
        //             $kabkota = $pelangganAddress->kabkota;
        //             $provinsi = $pelangganAddress->provinsi;
        //         }else if($type == 'province'){
        //             $provinsi = $pelangganAddress->provinsi;
        //         }else{
        //             $provinsi = 'All';
        //         }
        //     }else{
                $provinsi = 'All';
        //     }
        // }

        $data['leaderboard'] = $this->glrm->getAll($nation_code, 1, 3, $kelurahan, $kecamatan, $kabkota, $provinsi);
        foreach ($data['leaderboard'] as &$lr) {
            if (isset($lr->b_user_image)) {
                if(file_exists(SENEROOT.$lr->b_user_image) && $lr->b_user_image != 'media/user/default.png'){
                  $lr->b_user_image = $this->cdn_url($lr->b_user_image);
                } else {
                  $lr->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }
        }

        if(isset($pelanggan->id)){
            $findRanking = $this->glrm->getByUserId($nation_code, $pelanggan->id, $kelurahan, $kecamatan, $kabkota, $provinsi);
            if(isset($findRanking->ranking)){
                if($findRanking->total_point != 0){
                    $data['leaderboard_my_ranking'] = $findRanking->ranking;
                    $data['leaderboard_my_point'] = $findRanking->total_point;
                }
            }
            unset($findRanking);
        }

        $this->status = 200;
        $this->message = 'Success';

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    }

    public function leaderboard_rankingv2()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['leaderboard'] = array();
        $data['leaderboard_my_ranking'] = 'N/A';
        $data['leaderboard_my_point'] = '0';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
            die();
        }

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

        $provinsi = trim($this->input->get("provinsi"));

        // $type = $this->input->get("type");
        // if (strlen($type)<=0 || empty($type)){
          $type="All";
        // }

        // if($type == 'sameStreet'){
        //     $type = 'neighborhood';
        // }

        $kelurahan = 'All';
        $kecamatan = 'All';
        $kabkota = 'All';

        // if (!$provinsi || $provinsi == "") {
        //     if(isset($pelanggan->id)){
        //         $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

        //         if($type == 'neighborhood'){
        //             $kelurahan = $pelangganAddress->kelurahan;
        //             $kecamatan = $pelangganAddress->kecamatan;
        //             $kabkota = $pelangganAddress->kabkota;
        //             $provinsi = $pelangganAddress->provinsi;
        //         }else if($type == 'district'){
        //             $kecamatan = $pelangganAddress->kecamatan;
        //             $kabkota = $pelangganAddress->kabkota;
        //             $provinsi = $pelangganAddress->provinsi;
        //         }else if($type == 'city'){
        //             $kabkota = $pelangganAddress->kabkota;
        //             $provinsi = $pelangganAddress->provinsi;
        //         }else if($type == 'province'){
        //             $provinsi = $pelangganAddress->provinsi;
        //         }else{
        //             $provinsi = 'All';
        //         }
        //     }else{
                $provinsi = 'All';
        //     }
        // }

        $data['leaderboard'] = $this->glrm->getAll($nation_code, 1, 3, $kelurahan, $kecamatan, $kabkota, $provinsi);
        foreach ($data['leaderboard'] as &$lr) {
            if (isset($lr->b_user_image)) {
                if(file_exists(SENEROOT.$lr->b_user_image) && $lr->b_user_image != 'media/user/default.png'){
                  $lr->b_user_image = $this->cdn_url($lr->b_user_image);
                } else {
                  $lr->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }
        }

        if(isset($pelanggan->id)){
            $findRanking = $this->glrm->getByUserId($nation_code, $pelanggan->id, $kelurahan, $kecamatan, $kabkota, $provinsi);
            if(isset($findRanking->ranking)){
                if($findRanking->total_point != 0){
                    $data['leaderboard_my_ranking'] = $findRanking->ranking;
                    $data['leaderboard_my_point'] = $findRanking->total_point;
                }
            }
            unset($findRanking);
        }

        $this->status = 200;
        $this->message = 'Success';

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    }

    public function leaderboard_spt_balance() {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        // $data['leaderboard_my_spt_balance'] = 'menghitung';
        $data['leaderboard_my_spt_balance'] = '0';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
            die();
        }

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

        $getPointNow = $this->glptm->getByUserId($nation_code, $pelanggan->id);
        if(isset($getPointNow->b_user_id)){
            $data['leaderboard_my_spt_balance'] = number_format($getPointNow->total_point, 0, ',', '.');
        }

        $this->status = 200;
        $this->message = 'Success';

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    }

    public function hot_item()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['hot_item'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
            die();
        }

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

        $provinsi = trim($this->input->get("provinsi"));

        $type = $this->input->get("type");
        if (strlen($type)<=0 || empty($type)){
          $type="All";
        }

        if($type == 'sameStreet'){
            $type = 'neighborhood';
        }

        $kelurahan = 'All';
        $kecamatan = 'All';
        $kabkota = 'All';

        if (!$provinsi || $provinsi == "") {
            if(isset($pelanggan->id)){
                $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

                if($type == 'neighborhood'){
                    $kelurahan = $pelangganAddress->kelurahan;
                    $kecamatan = $pelangganAddress->kecamatan;
                    $kabkota = $pelangganAddress->kabkota;
                    $provinsi = $pelangganAddress->provinsi;
                }else if($type == 'district'){
                    $kecamatan = $pelangganAddress->kecamatan;
                    $kabkota = $pelangganAddress->kabkota;
                    $provinsi = $pelangganAddress->provinsi;
                }else if($type == 'city'){
                    $kabkota = $pelangganAddress->kabkota;
                    $provinsi = $pelangganAddress->provinsi;
                }else if($type == 'province'){
                    $provinsi = $pelangganAddress->provinsi;
                }else{
                    $provinsi = 'All';
                }
            }else{
                $provinsi = 'All';
            }
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

        if($provinsi != "All" && date("Y-m-d") <= "2022-08-31"){
            //by Donny Dennison - 02 november 2022 14:21
            //new feature, block community post or account
            // $data['hot_item'] = $this->cp->getAllHomepage($nation_code, 1, 8, "All", "All", "All", "All", $pelanggan, "option1", "All", "All", $pelanggan->language_id);
            // $data['hot_item'] = $this->cp->getAllHomepage($nation_code, 1, 8, "All", "All", "All", "All", $pelanggan, "option1", "All", "All", $blockDataAccount, $blockDataAccountReverse, $blockDataProduct, $pelanggan->language_id);
            // if(!$data['hot_item']){
                //by Donny Dennison - 02 november 2022 14:21
                //new feature, block community post or account
                // $data['hot_item'] = $this->cp->getAllHomepage($nation_code, 1, 8, "All", "All", "All", "All", $pelanggan, "option2", "MeetUp", "All", $pelanggan->language_id);
                $data['hot_item'] = $this->cp->getAllHomepage($nation_code, 1, 4, "All", "All", "All", "All", $pelanggan, "option2", "MeetUp", "All", $blockDataAccount, $blockDataAccountReverse, $blockDataProduct, $pelanggan->language_id);
                // $hot_item_protection = $this->cp->getAllHomepage($nation_code, 1, 4,  "All", "All", "All", "All", $pelanggan, "option2", "Protection", $type);
                // $data['hot_item'] = array_merge($data['hot_item'], $hot_item_protection);
            // }
        }else{
            //by Donny Dennison - 02 november 2022 14:21
            //new feature, block community post or account
            // $data['hot_item'] = $this->cp->getAllHomepage($nation_code, 1, 8, $kelurahan, $kecamatan, $kabkota, $provinsi, $pelanggan, "option1", "All", $type, $pelanggan->language_id);
            // $data['hot_item'] = $this->cp->getAllHomepage($nation_code, 1, 8, $kelurahan, $kecamatan, $kabkota, $provinsi, $pelanggan, "option1", "All", $type, $blockDataAccount, $blockDataAccountReverse, $blockDataProduct, $pelanggan->language_id);
            // if(!$data['hot_item']){
                //by Donny Dennison - 02 november 2022 14:21
                //new feature, block community post or account
                // $data['hot_item'] = $this->cp->getAllHomepage($nation_code, 1, 8, $kelurahan, $kecamatan, $kabkota, $provinsi, $pelanggan, "option2", "MeetUp", $type, $pelanggan->language_id);
                $data['hot_item'] = $this->cp->getAllHomepage($nation_code, 1, 4, $kelurahan, $kecamatan, $kabkota, $provinsi, $pelanggan, "option2", "MeetUp", $type, $blockDataAccount, $blockDataAccountReverse, $blockDataProduct, $pelanggan->language_id);
                // $hot_item_protection = $this->cp->getAllHomepage($nation_code, 1, 4, $kelurahan, $kecamatan, $kabkota, $provinsi, $pelanggan, "option2", "Protection", $type);
                // $data['hot_item'] = array_merge($data['hot_item'], $hot_item_protection);
            // }
        }

        foreach ($data['hot_item'] as &$pd) {
            //conver to utf friendly
            // if (isset($pd->nama)) {
            //   $pd->nama = $this->__dconv($pd->nama);
            // }
            $pd->nama = html_entity_decode($pd->nama,ENT_QUOTES);

            // if (isset($pd->brand)) {
            //     $pd->brand = $this->__dconv($pd->brand);
            // }
            if (isset($pd->b_user_nama_seller)) {
                $pd->b_user_nama_seller = $this->__dconv($pd->b_user_nama_seller);
            }

            if (isset($pd->b_user_image_seller)) {
                if (empty($pd->b_user_image_seller)) {
                  $pd->b_user_image_seller = 'media/produk/default.png';
                }
                // by Muhammad Sofi - 28 October 2021 11:00
                // if user img & banner not exist or empty, change to default image
                // $pd->b_user_image_seller = $this->cdn_url($pd->b_user_image_seller);
                if(file_exists(SENEROOT.$pd->b_user_image_seller) && $pd->b_user_image_seller != 'media/user/default.png'){
                  $pd->b_user_image_seller = $this->cdn_url($pd->b_user_image_seller);
                } else {
                  $pd->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
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
        }

        $this->status = 200;
        $this->message = 'Success';

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    }
    //END by Donny Dennison - 19 december 2022 12:50
    //separate homepage api

    public function kategori()
    {
        $data = array();
        $nation_code = 62;
        $data['kategori'] = $this->bk->getHomepage($nation_code, $page=0, $pageSize=10, $sortcol="nama", $sortdir="asc");
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    }
    public function produk()
    {
        $data = array();
        $nation_code = 62;
        $data['produk'] = $this->cp->getHomePage($nation_code, $page=0, $page_size=12, $sort_col="id", $sort_dir="asc", $keyword="", $harga_jual_min="", $harga_jual_max="", $b_kondisi_ids=array(), $b_kategori_ids=array(), $kecamatan="");
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    }
    public function produk_count()
    {
        $data = array();
        $nation_code = 62;
        $data['produk_count'] = $this->cp->countHomePage($nation_code, $keyword="", $harga_jual_min="", $harga_jual_max="", $b_kondisi_ids=array(), $b_kategori_ids=array(), $kecamatan="");
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    }

    // //by Donny Dennison - 28 august 2020 15:14
    // //add new api for best shop in homepage
    // public function best_shop()
    // {
    //     //initial
    //     $dt = $this->__init();

    //     //default result
    //     $data = array();
    //     $data['page'] = 0;
    //     $data['page_size'] = 0;
    //     $data['best_shop'] = array();
                
    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    //         die();
    //     }
        
    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    //         die();
    //     }

    //     //collect input
    //     $page = (int) $this->input->get("page");
    //     // $page_size = (int) $this->input->get("page_size");
    //     // $sort_col = strtolower($this->input->get("sort_col"));
    //     // $sort_dir = strtolower($this->input->get("sort_dir"));

    //     //input validation
    //     if ($page<=0) {
    //         $page = 1;
    //     }
    //     // if ($page_size<=0) {
    //     //     $page_size = 25;
    //     // }

    //     //declare config var
    //     $classified = "app_config";
    //     $code = "C1";
    //     $config = $this->ccm->getByClassifiedAndCode($nation_code,$classified,$code);
    //     $page_size = (int) $config->remark;

    //     // switch ($sort_col) {
    //     //     case "nama":
    //     //         $sort_col = "nama";
    //     //         break;
    //     //     case "harga_jual":
    //     //         $sort_col = "harga_jual";
    //     //         break;
    //     //     case "kondisi":
    //     //         $sort_col = "b_kondisi_id";
    //     //         break;
    //     //     default:
    //     //         //by Donny Dennison - 13-07-2020 16:08
    //     //         //edit stok allow 0 and if edit stok more than 0 then change cdate to newest
    //     //         // $sort_col = "id";
    //     //         $sort_col = "cdate";
                
    //     // }
    //     // switch ($sort_dir) {
    //     //     case "asc":
    //     //         $sort_dir = "asc";
    //     //         break;
    //     //     default:
    //     //         $sort_dir = "desc";
    //     // }

    //     // $b_kategori_ids = "";
    //     // if (isset($_GET['b_kategori_ids'])) {
    //     //     $b_kategori_ids = $_GET['b_kategori_ids'];
    //     // }
    //     // if (strlen($b_kategori_ids)>0) {
    //     //     $b_kategori_ids = rtrim($b_kategori_ids, ",");
    //     //     $b_kategori_ids = explode(",", $b_kategori_ids);
    //     //     if (count($b_kategori_ids)) {
    //     //         $kods = array();
    //     //         foreach ($b_kategori_ids as &$bki) {
    //     //             $bki = (int) trim($bki);
    //     //             if ($bki>0) {
    //     //                 $kods[] = $bki;
    //     //             }
    //     //         }
    //     //         $b_kategori_ids = $kods;
    //     //     } else {
    //     //         $b_kategori_ids = array();
    //     //     }
    //     // } else {
    //     //     $b_kategori_ids = array();
    //     // }

    //     //end advanced filter
    //     $data['page'] = $page;
    //     $data['page_size'] = $page_size;
    //     // $data['sort_col'] = $sort_col;
    //     // $data['sort_dir'] = $sort_dir;

    //     $bestShop = $this->dodm->getBestShopTopSold($nation_code, $page, $page_size);
    //     $data['best_shop'] = $bestShop;
    //     foreach ($bestShop as $key => $bs) {
    //         //get total product seller
    //         // $data['best_shop'][$key]->total_product = $this->cp->countByUserIdForBestShop($nation_code, $bs->b_user_id_seller);

    //         $newlyRegisteredProduct = $this->cp->getByUserId($nation_code, $bs->b_user_id_seller,1,1,'id','desc');
            
    //         $data['best_shop'][$key]->nama_product = '';
    //         if(isset($newlyRegisteredProduct[0]->nama)){
    //             $data['best_shop'][$key]->nama_product = $newlyRegisteredProduct[0]->nama;
    //         }

    //         $data['best_shop'][$key]->thumb_product = '';
    //         if(isset($newlyRegisteredProduct[0]->thumb)){
    //             $data['best_shop'][$key]->thumb_product = $newlyRegisteredProduct[0]->thumb;
    //         }

    //         if (isset($data['best_shop'][$key]->nama_product)) {
    //             $data['best_shop'][$key]->nama_product = $this->__dconv($data['best_shop'][$key]->nama_product);
    //         }

    //         if (isset($data['best_shop'][$key]->fnama_seller)) {
    //             $data['best_shop'][$key]->fnama_seller = $this->__dconv($data['best_shop'][$key]->fnama_seller);
    //         }

    //         if (isset($data['best_shop'][$key]->thumb_product)) {
    //             if (empty($data['best_shop'][$key]->thumb_product)) {
    //                 $data['best_shop'][$key]->thumb_product = 'media/produk/default.png';
    //             }
    //             $data['best_shop'][$key]->thumb_product = $this->cdn_url($data['best_shop'][$key]->thumb_product);
    //         }

    //         // if($data['best_shop'][$key]->total_product == 0){
    //         //     unset($data['best_shop'][$key]);
    //         // }
    //     }

    //     //reset key
    //     // array_values($data['best_shop']);

    //     //sort by top sold (priority 1) and top total product (priority 2)
    //     array_multisort(array_column($data['best_shop'], 'total_sold'), SORT_DESC,
    //             array_column($data['best_shop'], 'total_product'),      SORT_DESC,
    //             $data['best_shop']);

    //     $this->status = 200;
    //     $this->message = 'Success';
                
    //     //ini_set('memory_limit', -1);
    //     ini_set('max_execution_time', 0);
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    // }

    //by Donny Dennison - 6 december 2021 17:02
    //add weather api
    public function weather()
    {
        $data = array();
        $data['icon'] = 'https://cdn.weatherapi.com/weather/64x64/day/113.png';
        $data['temp_c'] = '0';
        $data['icon_text'] = '';
        $data['pm2_5_text'] = '';
        $data['pm2_5'] = '0';

        $this->status = 200;
        $this->message = 'Success';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
            die();
        }

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

        //input default value
        $data['pm2_5_text'] = $this->gaqim->getName($nation_code, 0, $pelanggan->language_id)->name;

        $parameter = $this->input->get("parameter");

        if(isset($pelanggan->id)){
            $default_address = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
            if($default_address->latitude > 0 && $default_address->latitude > 0){
                $parameter = $default_address->latitude.','.$default_address->longitude;
            }else{
                if(!$parameter){
                    $this->status = 1002;
                    $this->message = 'Latitude & Longitude empty and Parameter not provided';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
                    die();
                }
            }
        }else{
            if(!$parameter){
                $this->status = 1003;
                $this->message = 'Parameter not provided';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
                die();
            }
        }

        $weather = $this->__getWeatherApi($parameter);
        $weather = json_decode($weather);
        if(!isset($weather->error)){
            if(isset($weather->current)){
                $data['icon'] = (isset($weather->current->condition->icon)) ? 'https:'.$weather->current->condition->icon : "https://cdn.weatherapi.com/weather/64x64/day/113.png";
                $data['temp_c'] = (isset($weather->current->temp_c)) ? $weather->current->temp_c : "0";
                $data['icon_text'] = (isset($weather->current->condition->text)) ? $weather->current->condition->text : "";
                $data['pm2_5'] = (isset($weather->current->air_quality->pm2_5)) ? $weather->current->air_quality->pm2_5 : "0";

                if(isset($weather->current->air_quality->pm2_5)){
                    $pm2_5_text = $this->gaqim->getName($nation_code, $weather->current->air_quality->pm2_5, $pelanggan->language_id);
                    
                    if(isset($pm2_5_text->name)){
                        $data['pm2_5_text'] = ($pm2_5_text->name != null) ? $pm2_5_text->name : $this->gaqim->getName($nation_code, 0, $pelanggan->language_id)->name;
                    }else{
                        $data['pm2_5_text'] = $this->gaqim->getName($nation_code, 0, $pelanggan->language_id)->name;
                    }
                }else{
                    $data['pm2_5_text'] = $this->gaqim->getName($nation_code, 0, $pelanggan->language_id)->name;
                }
            }
        }else{
            //credit: https://www.weatherapi.com/docs/#intro-error-codes
            // Error code  Description
            // 1002        API key not provided.
            // 1003        Parameter 'q' not provided.
            // 1005        API request url is invalid
            // 1006        No location found matching parameter 'q'
            // 2006        API key provided is invalid
            // 2007        API key has exceeded calls per month quota.
            // 2008        API key has been disabled.
            // 9999        Internal application error.
            $this->seme_log->write("api_mobile", "api_mobile/homepage/weather, response from api weather ".$weather->error->code." ".$weather->error->message);
            // $this->status = $weather->error->code;
            // $this->message = $weather->error->message;
        }

        $this->seme_log->write("api_mobile", "api_mobile/homepage/weather, response send to mobile ".json_encode($data));

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    }

    //by Donny Dennison - 24 november 2021 9:45
    //add feature highlight community & leaderboard point & hot item
    public function friendpost()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        // $data['community_total'] = 0;
        $data['communitys'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
          $this->status = 101;
          $this->message = 'Missing or invalid nation_code';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
          die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
          $this->status = 400;
          $this->message = 'Missing or invalid API key';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
          die();
        }

        // check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
            die();
        }

        //populate input get
        $sort_col = $this->input->get("sort_col");
        $sort_dir = $this->input->get("sort_dir");
        $page = $this->input->get("page");
        // $page_size = $this->input->get("page_size");
        $timezone = $this->input->get("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        //sanitize input
        $tbl_as = $this->ccomm->getTblAs();

        $sort_col = $this->__sortCol($sort_col, $tbl_as);
        $sort_dir = $this->__sortDir($sort_dir);
        $page = $this->__page(1);
        // $page_size = $this->__pageSize($page_size);
        $page_size= 5; // set limit from 25 to 15

        // if (isset($pelanggan->id)) {
        //   $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
        // }else{
        //   $pelangganAddress = array();
        // }

        //by Donny Dennison - 07 february 2022 17:41
        //bug fix subquery get wrong total
        //so the fix is call the first one (the wrong one) after that it's query become normal again
        // $this->ccomm->countAllHomepage($nation_code, $pelanggan);
        // $this->ccomm->resetRankVariableMysql("f_rank1");

        // $ddcount = $this->ccomm->countAllHomepage($nation_code, $pelanggan);

        //by Donny Dennison - 07 february 2022 17:41
        //bug fix subquery get wrong total
        //so the fix is call the first one (the wrong one) after that it's query become normal again
        // $this->ccomm->getAllHomepage($nation_code, $page, $page_size, $sort_col, $sort_dir, $pelanggan, $pelanggan->language_id);
        // $this->ccomm->resetRankVariableMysql("f_rank2");

        $data['communitys'] = $this->ccomm->getAllHomepage($nation_code, $page, $page_size, $sort_col, $sort_dir, $pelanggan, $pelanggan->language_id);
        foreach ($data['communitys'] as &$pd) {
            // $pd->can_chat_and_like = "0";

            // if(isset($pelanggan->id) && isset($pelangganAddress->alamat2)){
            // if(isset($pelanggan->id)){
                // if($pd->postal_district == $pelangganAddress->postal_district){
                $pd->can_chat_and_like = "1";
                // }
            // }

            $pd->is_owner_post = "0";
            $pd->is_liked = '0';
            $pd->is_disliked = '0';
            // if(isset($pelanggan->id)){
                // if($pd->b_user_id_starter == $pelanggan->id){
                //     $pd->is_owner_post = "1";
                // }

                $checkLike = $this->cclm->getByCustomIdUserId($nation_code, "community", "like", $pd->id, $pelanggan->id);
                if(isset($checkLike->id)){
                  $pd->is_liked = '1';
                }

                $checkDislike = $this->cclm->getByCustomIdUserId($nation_code, "community", "dislike", $pd->id, $pelanggan->id);
                if(isset($checkDislike->id)){
                  $pd->is_disliked = '1';
                }
            // }

            // $pd->cdate_text = $this->humanTiming($pd->cdate);
            $pd->cdate_text = $this->humanTiming($pd->cdate, null, $pelanggan->language_id);

            $pd->cdate = $this->customTimezone($pd->cdate, $timezone);

            $pd->title = html_entity_decode($pd->title,ENT_QUOTES);

            $pd->deskripsi = html_entity_decode($pd->deskripsi,ENT_QUOTES);

            if (isset($pd->b_user_image_starter)) {
                if (empty($pd->b_user_image_starter)) {
                    $pd->b_user_image_starter = 'media/produk/default.png';
                }
            
                if(file_exists(SENEROOT.$pd->b_user_image_starter) && $pd->b_user_image_starter != 'media/user/default.png'){
                    $pd->b_user_image_starter = $this->cdn_url($pd->b_user_image_starter);
                } else {
                    $pd->b_user_image_starter = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }

            // if($pd->is_liked_image){
            //     $pd->is_liked_image = $this->cdn_url($pd->is_liked_image);
            // }

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
                        $atc->url = 'media/produk/default.png';
                    }
                    if (empty($atc->url_thumb)) {
                        $atc->url_thumb = 'media/produk/default.png';
                    }

                    $atc->url = $this->cdn_url($atc->url);
                    $atc->url_thumb = $this->cdn_url($atc->url_thumb);

                    $pd->images[] = $atc;
                }else if($atc->jenis == 'video'){
                    $atc->url = $this->cdn_url($atc->url);
                    $atc->url_thumb = $this->cdn_url($atc->url_thumb);

                    $pd->videos[] = $atc;
                }else{
                    $pd->locations[] = $atc;
                }
            }
            unset($attachments,$atc);
        }

        //build result
        // $data['community_total'] = $ddcount;

        //response
        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    }

    public function get_user_online() {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['user_status'] = array();

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

        // check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $onlineoroffline = $this->input->get('onlineoroffline');
        if(!in_array($onlineoroffline, array("online", "offline"))){
            $onlineoroffline = "online";
        }

        //request uncomment from mr jackie(7 nov 2023 14:59 by verbal)
        // check if user offline, set to online
        $du = array();
        if($onlineoroffline == "online"){
            $du['is_online'] = '1';
        }else{
            $du['is_online'] = '0';
        }
        $du['last_online'] = 'NOW()';
        $this->bu->update($nation_code, $pelanggan->id, $du);

        if($pelanggan->is_band_online_status == "1") {
            $dx = array();
            if($onlineoroffline == "online"){
                $dx['is_online'] = '1';
            }else{
                $dx['is_online'] = '0';
            }
            $this->igparticipantm->updateStatusParticipant($nation_code, '0', '1', $pelanggan->id, "", $dx);
        }

        $this->status = 200;
        $this->message = 'success';

        $get_current = $this->bu->getById($nation_code, $pelanggan->id);
        $status = $get_current->is_online == "0" ? "offline" : "online";
        $data['user_status'] = $status;

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    }

    public function club()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['group'] = array();
        $data['type'] = "popular";
        $data['title'] = "Popular Clubs";
        $data['link_url'] = "group/group/";
        $data['link_url_parameter'] = array(
          0 => array(
            "parameter" => "query_type",
            "value" => "popular"
          )
        );

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

        $timezone = $this->input->get("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        // $sort_col = $this->input->get("sort_col");
        // $sort_dir = $this->input->get("sort_dir");
        // $page = $this->input->get("page");
        // $page_size = $this->input->get("page_size");
        $sort_col = "";
        $sort_dir = "";
        $page = 1;
        $page_size = 3;

        // $tbl_as = $this->igm->getTblAs();
        // $sort_col = $this->__sortCol($sort_col, $tbl_as);
        // $sort_dir = $this->__sortDir($sort_dir);
        // $page = $this->__page($page);
        // $page_size = $this->__pageSize($page_size);

        //keyword
        // $keyword = trim($this->input->get("keyword"));
        $keyword = "";
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

        $data['group'] = $this->igm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, "", "", "popular");
        foreach ($data['group'] as &$pd) {
            $pd->cdate_text = $this->humanTiming($pd->cdate, null, $pelanggan->language_id);
            if($pelanggan->language_id == 2) {
                $pd->cdate_text_2 = $this->__dateIndonesia($pd->cdate, "tanggal");
            }else{
                $pd->cdate_text_2 = $this->__dateEnglish($pd->cdate, "tanggal");
            }
            $cdate_texte = date_create($pd->cdate);
            $pd->created_on = date_format($cdate_texte, "M Y");
            $pd->cdate = $this->customTimezone($pd->cdate, $timezone);
            $pd->image = $this->cdn_url($pd->image_thumb);

            if(isset($pelanggan->id)){
              $pd->status_member = $this->__statusMember($nation_code, $pd->id, $pelanggan->id);
            }else{
              $pd->status_member = $this->__statusMember($nation_code, $pd->id, "0");
            }

            $pd->description_images = array();
            $pd->description_location = array();
            $attachmentImage = $this->igam->getByGroupId($nation_code, $pd->id, "all", "image");
            foreach($attachmentImage as &$atc_image) {
              if($atc_image->jenis == 'image' || $atc_image->jenis == 'video'){
                $atc_image->url = $this->cdn_url($atc_image->url);
                $atc_image->url_thumb = $this->cdn_url($atc_image->url_thumb);
                $pd->description_images[] = $atc_image;
              }
            }
            unset($attachmentImage);

            $attachmentLocation = $this->igam->getByGroupId($nation_code, $pd->id, "all", "location");
            foreach($attachmentLocation as &$atc_location) {
              $pd->description_location[] = $atc_location;
            }
            unset($attachmentLocation);
        }

        if($pelanggan->language_id == 2) {
            $data['title'] = "Klub Populer";
        }else{
            $data['title'] = "Popular Clubs";
        }

        //response
        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    }

    public function popular()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        // $data['popular'] = array();

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

        $timezone = $this->input->get("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        $type = $this->input->get("type");
        if($type == "community"){
            $data["communitys"] = array();
        }
        if($type == "club"){
            $data["group"] = array();
            $data["type"] = "popular";
            if($timezone == "Asia/Jakarta") {
                $data['title'] = "Klub Populer";
            }else{
                $data['title'] = "Popular Clubs";
            }
            $data["link_url"] = "group/group/";
            $data["link_url_parameter"] = array(
              0 => array(
                "parameter" => "query_type",
                "value" => "popular"
              )
            );
        }

        $chmpm = $this->chmpm->getAll($nation_code, $type);
        if(count($chmpm) == 0){
            //response
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
        }

        $ids = array();
        foreach($chmpm AS $chmpmodel){
            $ids[] = $chmpmodel->custom_id;
            if($type == "community"){
                $data["communitys"][] = array();
            }
            if($type == "club"){
                $data["group"][] = array();
            }
        }
        unset($chmpmodel);

        if($type == "community"){
            $queryResult = $this->ccomm->getAllByids($nation_code, $ids, $pelanggan->language_id);
            foreach ($queryResult as $pd) {
                // $pd->can_chat_and_like = "0";

                // if(isset($pelanggan->id) && isset($pelangganAddress->alamat2)){
                // if(isset($pelanggan->id)){
                    // if($pd->postal_district == $pelangganAddress->postal_district){
                    $pd->can_chat_and_like = "1";
                    // }
                // }

                $pd->is_owner_post = "0";
                $pd->is_liked = '0';
                $pd->is_disliked = '0';
                if(isset($pelanggan->id)){
                    if($pd->b_user_id_starter == $pelanggan->id){
                        $pd->is_owner_post = "1";
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

                $pd->title = html_entity_decode($pd->title,ENT_QUOTES);

                $pd->deskripsi = html_entity_decode($pd->deskripsi,ENT_QUOTES);

                if (isset($pd->b_user_image_starter)) {
                    if (empty($pd->b_user_image_starter)) {
                        $pd->b_user_image_starter = 'media/produk/default.png';
                    }
                
                    if(file_exists(SENEROOT.$pd->b_user_image_starter) && $pd->b_user_image_starter != 'media/user/default.png'){
                        $pd->b_user_image_starter = $this->cdn_url($pd->b_user_image_starter);
                    } else {
                        $pd->b_user_image_starter = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                }

                // if($pd->is_liked_image){
                //     $pd->is_liked_image = $this->cdn_url($pd->is_liked_image);
                // }

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
                            $atc->url = 'media/produk/default.png';
                        }
                        if (empty($atc->url_thumb)) {
                            $atc->url_thumb = 'media/produk/default.png';
                        }

                        $atc->url = $this->cdn_url($atc->url);
                        $atc->url_thumb = $this->cdn_url($atc->url_thumb);

                        $pd->images[] = $atc;
                    }else if($atc->jenis == 'video'){
                        $atc->url = $this->cdn_url($atc->url);
                        $atc->url_thumb = $this->cdn_url($atc->url_thumb);

                        $pd->videos[] = $atc;
                    }else{
                        $pd->locations[] = $atc;
                    }
                }
                unset($attachments,$atc);

                $key = array_search($pd->id, array_column($chmpm, 'custom_id'));
                $data["communitys"][$key] = $pd;
            }
            unset($queryResult);
        }

        if($type == "club"){
            $queryResult = $this->igm->getByIds($nation_code, 0, 0, "", "", $ids);
            foreach ($queryResult as $pd) {
                $pd->cdate_text = $this->humanTiming($pd->cdate, null, $pelanggan->language_id);
                if($pelanggan->language_id == 2) {
                    $pd->cdate_text_2 = $this->__dateIndonesia($pd->cdate, "tanggal");
                }else{
                    $pd->cdate_text_2 = $this->__dateEnglish($pd->cdate, "tanggal");
                }
                $cdate_texte = date_create($pd->cdate);
                $pd->created_on = date_format($cdate_texte, "M Y");
                $pd->cdate = $this->customTimezone($pd->cdate, $timezone);
                $pd->image = $this->cdn_url($pd->image_thumb);

                if(isset($pelanggan->id)){
                  $pd->status_member = $this->__statusMember($nation_code, $pd->id, $pelanggan->id);
                }else{
                  $pd->status_member = $this->__statusMember($nation_code, $pd->id, "0");
                }

                $pd->description_images = array();
                $pd->description_location = array();
                $attachmentImage = $this->igam->getByGroupId($nation_code, $pd->id, "all", "image");
                foreach($attachmentImage as &$atc_image) {
                  if($atc_image->jenis == 'image' || $atc_image->jenis == 'video'){
                    $atc_image->url = $this->cdn_url($atc_image->url);
                    $atc_image->url_thumb = $this->cdn_url($atc_image->url_thumb);
                    $pd->description_images[] = $atc_image;
                  }
                }
                unset($attachmentImage);

                $attachmentLocation = $this->igam->getByGroupId($nation_code, $pd->id, "all", "location");
                foreach($attachmentLocation as &$atc_location) {
                  $pd->description_location[] = $atc_location;
                }
                unset($attachmentLocation);

                $key = array_search($pd->id, array_column($chmpm, 'custom_id'));
                $data["group"][$key] = $pd;
            }
            unset($queryResult);
        }

        //response
        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "homepage");
    }
}
