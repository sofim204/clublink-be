<?php

require_once (SENEROOT.'/vendor/autoload.php');
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
class Blacklistuser extends JI_Controller {
    public function __construct() {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_admin/g_blacklist_model", 'gbm');
        $this->load("api_admin/b_user_model", 'bum');
    }

    // private function __callBlockChainBlacklist($postdata){
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    //     curl_setopt($ch, CURLOPT_URL, $this->blockchain_api_host."Wallet/BlackListUserWallet");

    //     $headers = array();
    //     $headers[] = 'Content-Type:  application/json';
    //     $headers[] = 'Accept:  application/json';
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //     // $postdata = array(
    //     //   'userWalletCode' => $this->__encryptdecrypt($userWalletCode,"encrypt"),
    //     //   'countryIsoCode' => $this->blockchain_api_country,
    //     //   'isReferralSignUp' => ($referralUserWalletCode == "") ? false : true,
    //     //   'referralUserWalletCode' => ($referralUserWalletCode == "") ? "" : $this->__encryptdecrypt($referralUserWalletCode,"encrypt")
    //     // );
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));

    //     $result = curl_exec($ch);
    //     if (curl_errno($ch)) {
    //       return 0;
    //       //echo 'Error:' . curl_error($ch);
    //     }
    //     curl_close($ch);

    //     $this->seme_log->write("api_admin", " API_ADMIN/CallBlockChainBlackList::index -- url untuk block chain server ". $this->blockchain_api_host."Wallet/BlackListUserWallet. data send to blockchain api ". json_encode($postdata).". isi response block chain server ". $result);
    //     return $result;
    // }

    //credit :
    //https://stackoverflow.com/a/35289156/7578520
    //https://stackoverflow.com/a/29560553/7578520
    // private function __encryptdecrypt($text, $type="encrypt"){
    //     if($type == "encrypt"){
    //     // Encrypt using the public key
    //     openssl_public_encrypt($text, $encrypted, $this->blockchain_api_public_key);
    //     return base64_encode($encrypted);
    //     }else if($type == "decrypt"){
    //     // Decrypt the data using the private key
    //     openssl_private_decrypt(base64_decode($text), $decrypted, openssl_pkey_get_private($this->blockchain_api_private_key, $this->blockchain_api_private_key_password));
    //     return $decrypted;
    //     }
    // }

    public function index() {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access, please login';
            header("HTTP/1.0 400 Unauthorized access, please login");
            $this->__json_out($data);
            die();
        }

        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $sSearch = $this->input->request("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->request("iDisplayLength");

        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");

		$type = $this->input->post("type");

        $sortCol = "date";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }

        switch ($iSortCol_0) {
            case 0:
                $sortCol = "no";
                break;
            case 1:
                $sortCol = "id";
                break;
            case 2:
                $sortCol = "type";
                break;
            case 3:
                $sortCol = "text";
                break;
            case 4:
                $sortCol = "admin_name";
                break;
            default:
                $sortCol = "no";
        }

        if (empty($draw)) {
            $draw = 0;
        }
        if (empty($pagesize)) {
            $pagesize=10;
        }
        if (empty($page)) {
            $page=0;
        }

        $keyword = $sSearch;

        $this->status = 200;
        $this->message = 'Success';
        $dcount = $this->gbm->countAll($keyword, $type);
        $ddata = $this->gbm->getAll($page, $pagesize, $sortCol, $sortDir, $keyword, $type);

		foreach ($ddata as &$gd) {
            if (isset($gd->admin_name)) {
                if(empty($gd->admin_name)) {
					$admin_name = "ADMIN";
				} else {
					$admin_name = strtoupper(str_replace("_", " ", $gd->admin_name));
				}

				$result = '<span style="font-size: 1.0em; font-weight: bolder;">'.$admin_name.'</span><br />';

                $gd->admin_name = $result;
            }
		}

        $this->__jsonDataTable($ddata, $dcount);
    }

    public function tambah()
    {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access, please login';
            header("HTTP/1.0 400 Unauthorized access, please login");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $di = $_POST;
        $text = trim($di['text']);
        $type = $di['type'];

        if (!in_array($type, array("fcm_token", "ip_address"))) {
            $this->status = 1104;
            $this->message = 'Please choose type';
            $this->__json_out($data);
            die();
        }

        if (strlen($text)<3 || $text == "" || empty($text)) {
            $this->status = 1104;
            $this->message = 'Cannot empty';
            $this->__json_out($data);
            die();
        }

        $this->gbm->trans_start();

        $di['nation_code'] = $nation_code;
        $di['id'] = $this->gbm->getLastId($nation_code);

        $res = $this->gbm->set($di);
        if ($res) {
            $this->gbm->trans_commit();
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->gbm->trans_rollback();
            $this->status = 900;
            $this->message = 'Cant add data right now';
        }

        // update b_user set permanent inactive 

        $param = $di['type'];
        $value = $di['text'];
        
        $du = array();
        $du['is_permanent_inactive'] = 0;
        $du['permanent_inactive_by'] = 'admin';
        $du['permanent_inactive_date'] = date('Y-m-d H:i:s');
        $du['api_mobile_token'] = "";
        // $du['fcm_token'] = ""; // don't delete fcm_token
        $du['is_active'] = 0;
        $du['is_confirmed'] = 0;
        $du['is_online'] = 0;
        $du['telp_is_verif'] = 0;
        $du['inactive_text'] = "spammer account";
        if($param == "fcm_token") {
            $fcm_token = $value;
            // $this->bum->inactive_user_from_fcm_token($nation_code, $fcm_token, $du);
            
            $this->gbm->trans_end();

            // call BlackListUserWallet from blockchain API, stop rewarding and withdraw suspended user
            $userList = $this->bum->getByFcmToken($nation_code, $fcm_token);
            if(count($userList) > 0){

                $ids = array();
                foreach($userList as $list) {
                    $ids[] = $list->id;
                }

                $this->bum->inactive_user_from_fcm_token($nation_code, $ids, $du);

                // $postdata = array();
                // $loop = 0;
                // $counter = 0;
                // foreach($userList AS $chunk){
                //     $postdata[$loop][] = array(
                //       'userWalletCode' => $this->__encryptdecrypt($chunk->user_wallet_code,"encrypt"),
                //     //   'userWalletCode' => $chunk->user_wallet_code,
                //       'countryIsoCode' => $this->blockchain_api_country,
                //     );

                //     $counter++;
                //     if($counter == 50){
                //         $loop++;
                //         $counter = 0;
                //     }
                // }

                // for ($i=0; $i <= $loop; $i++) { 
                //     $postdataFinal = array(
                //         "userWalletList" => $postdata[$i]
                //     );

                //     $responseWalletApi = 0;
                //     $response = json_decode($this->__callBlockChainBlacklist($postdataFinal));
                //     if(isset($response->responseCode)){
                //         if($response->responseCode == 0){
                //             $responseWalletApi = 1;
                //         }
                //     }
                //     unset($response);

                // }

            }
            unset($userList);
                        
            // $response = json_decode($this->__callBlockChainBlacklist($get_data->user_wallet_code));
            // if(isset($response->responseCode)){
            //     if($response->responseCode == 0){
            //     $this->status = 200;
            //     $this->message = "Success";
            //     }else{
            //     $this->status = 1001;
            //     $this->message = "Sorry!!  We're now upgrading Wallet service to meet huge user connections.";
            //     }
            // } else {
            //     $this->status = 1001;
            //     $this->message = "Sorry!!  We're now upgrading Wallet service to meet huge user connections.";
            // }
        } else if($param == "ip_address") {
            $ip_address = $value;
            // $this->bum->inactive_user_from_ip_address($nation_code, $ip_address, $du);
            
            $this->gbm->trans_end();

            // call BlackListUserWallet from blockchain API, stop rewarding and withdraw suspended user
            $userList = $this->bum->getByIpAddress($nation_code, $ip_address);

            if(count($userList) > 0){
                
                $ids = array();
                foreach($userList as $list) {
                    $ids[] = $list->id;
                }

                $this->bum->inactive_user_from_ip_address($nation_code, $ids, $du);

                // $postdata = array();
                // $loop = 0;
                // $counter = 0;
                // foreach($userList AS $chunk){
                //     $postdata[$loop][] = array(
                //       'userWalletCode' => $this->__encryptdecrypt($chunk->user_wallet_code,"encrypt"),
                //     //   'userWalletCode' => $chunk->user_wallet_code,
                //       'countryIsoCode' => $this->blockchain_api_country,
                //     );

                //     $counter++;
                //     if($counter == 50){
                //         $loop++;
                //         $counter = 0;
                //     }

                // }

                // for ($i=0; $i <= $loop; $i++) { 
                //     $postdataFinal = array(
                //         "userWalletList" => $postdata[$i]
                //     );

                //     $responseWalletApi = 0;
                //     $response = json_decode($this->__callBlockChainBlacklist($postdataFinal));
                //     if(isset($response->responseCode)){
                //         if($response->responseCode == 0){
                //             $responseWalletApi = 1;
                //         }
                //     }
                //     unset($response);
                // }

            }
            unset($userList);
                        
            // $response = json_decode($this->__callBlockChainBlacklist($get_data->user_wallet_code));
            // if(isset($response->responseCode)){
            //     if($response->responseCode == 0){
            //     $this->status = 200;
            //     $this->message = "Success";
            //     }else{
            //     $this->status = 1001;
            //     $this->message = "Sorry!!  We're now upgrading Wallet service to meet huge user connections.";
            //     }
            // } else {
            //     $this->status = 1001;
            //     $this->message = "Sorry!!  We're now upgrading Wallet service to meet huge user connections.";
            // }

        } else {}

        $this->__json_out($data);
    }

    public function hapus($id)
    {
        $d = $this->__init();
        $data = array();

        $id = (int) $id;
        if ($id<=0) {
            $this->status = 450;
            $this->message = 'Invalid ID';
            $this->__json_out($data);
            die();
        }

        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access, please login';
            header("HTTP/1.0 400 Unauthorized access, please login");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $res = $this->gbm->del($nation_code, $id);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->status = 902;
            $this->message = 'Failed removing data from database, please try again later';
        }
        $this->__json_out($data);
    }
}