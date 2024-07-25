<?php

require_once (SENEROOT.'/vendor/autoload.php');
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

class Customeronly extends JI_Controller
{
    public $media_user = '';
    public $kode_pattern = '%010d';
    public $email_send = 1;

    public function __construct()
    {
        parent::__construct();
        $this->lib('site_config');
        $this->lib('seme_email');
        $this->lib("seme_log");
        $this->load("api_admin/b_customeronly_model", 'bcm');
        $this->load("api_admin/b_user_bankacc_model", 'bubam');
        $this->load("api_admin/e_chat_room_model", 'ecrm');
        $this->current_parent = 'ecommerce';
        $this->current_page = 'ecommerce_customeronly';
        $this->media_user = $this->site_config->media_user;
    }
    
    private function __check_environment() {
        $this->__init();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out(array());
            die();
        }
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
    //         // Encrypt using the public key
    //         openssl_public_encrypt($text, $encrypted, $this->blockchain_api_public_key);
    //         return base64_encode($encrypted);
    //     }else if($type == "decrypt"){
    //         // Decrypt the data using the private key
    //         openssl_private_decrypt(base64_decode($text), $decrypted, openssl_pkey_get_private($this->blockchain_api_private_key, $this->blockchain_api_private_key_password));
    //         return $decrypted;
    //     }
    // }

    public function index()
    {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $sSearch = $this->input->post("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->post("iDisplayLength");

        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");


        $sortCol = "date";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }
        $tbl_as = $this->bcm->getTblAlias();
        $tbl7_as = $this->bcm->getTblAlias7();

        switch ($iSortCol_0) {
            case 0:
                $sortCol = "$tbl_as.cdate";
                break;
            case 1:
                $sortCol = "$tbl_as.image";
                break;
            case 2:
                $sortCol = "$tbl_as.fnama";
                break;
            case 3:
                $sortCol = "$tbl_as.email";
                break;
            case 4:
                $sortCol = "$tbl_as.ip_address";
                break;
            case 5:
                $sortCol = "$tbl_as.is_emulator";
                break;
            case 6:
                $sortCol = "$tbl_as.is_active";
                break;
            case 7:
                $sortCol = "$tbl_as.is_permanent_inactive";
                break;
            case 8:
                $sortCol = "$tbl7_as.fnama";
                break;
            // Improve By Aditya Adi Prabowo 8/9/2020 
            // Add Device field on customer
            // Start Improve
            case 9:
                $sortCol = "$tbl_as.device";
                break;
            case 10:
                $sortCol = "$tbl_as.device_id";
                break;
            // End Improve
            case 11:
                $sortCol = "$tbl_as.fcm_token";
                break;
            case 12:
                $sortCol = "$tbl_as.alamat2";
                break;

            //START by Donny Dennison - 15 august 2022 13:16
            //Add fb_id, google_id, apple_id, and email status in cms
            case 13:
                $sortCol = "$tbl_as.fb_id";
                break;
            case 14:
                $sortCol = "$tbl_as.apple_id";
                break;
            case 15:
                $sortCol = "$tbl_as.google_id";
                break;
            case 16:
                $sortCol = "$tbl_as.email_id";
                break;
            //END by Donny Dennison - 15 august 2022 13:16
            //Add fb_id, google_id, apple_id, and email status in cms

            //by Donny Dennison - 23 august 2022 12:11
            //Add phone status in cms
            case 17:
                $sortCol = "$tbl_as.register_from";
                break;

            default:
                // $sortCol = "CAST($tbl_as.id AS INT)";
                $sortCol = "$tbl_as.cdate";
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

        //advanced filter
        $is_published = "";
        $is_active = "";
        $pelanggan_status = $this->input->post("pelanggan_status");
        switch ($pelanggan_status) {
            case 'active':
                $is_published=1;
                $is_active=1;
                break;
            case 'inactive':
                $is_published = "";
                $is_active=0;
                break;
            default:
                $is_published = "";
                $is_active = "";
                break;
        }
        $is_confirmed = $this->input->post("is_confirmed");
        if (strlen($is_confirmed)>0) {
            $is_confirmed = intval($is_confirmed);
            if (!empty($is_confirmed)) {
                $is_confirmed=1;
            }
        } else {
            $is_confirmed="";
        }

        $this->status = 200;
        $this->message = 'Success';

        $dcount = $this->bcm->countAllCustomerOnly($nation_code, $keyword, $is_confirmed, $is_active);
        $ddata = $this->bcm->getAllCustomerOnly($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $is_confirmed, $is_active);

        foreach ($ddata as &$gd) {
            if (isset($gd->nama)) {
                if (strlen($gd->nama)>30) {
                    //$gd->nama = mb_substr($gd->nama, 0, 30).'...';
                }
            }
            if (isset($gd->image)) {
                if (strlen($gd->image)<=10) {
                    $gd->image = 'media/user/default-profile-picture.png';
                }
                $gd->image = base_url($gd->image);
                $gd->image = '<img src="'.$gd->image.'" class="img-responsive" style="max-width: 64px;" onerror="this.onerror=null;this.src=\''.base_url().'media/default.png\';" />';
            }
            if (isset($gd->nama)) {
                $nama = $gd->nama;
                $gd->nama = '<span style="font-size: 1.2em; font-weight: bolder;">'.$nama.'</span><br />';
                if (isset($gd->cdate)) {
                    $gd->nama .= '<small>Reg date: <br/> '.$gd->cdate.'</small><br />';
                }
            }
            if (isset($gd->email)) {
                $nama = $gd->email;
                // if(isset($gd->fb_id)) {
                //     if (!empty($gd->fb_id) || ($gd->fb_id) != "NULL") {
                //         $nama = str_replace("@sellon.net","@gmail.com.", $nama); 
                //     }
                // }

                // if(isset($gd->register_from)) {
                //     if ($gd->register_from == "phone") {
                //         // $nama = str_replace("@sellon.net","@gmail.com.", $nama); 
                //         $nama = $gd->telp."@sellon.net";
                //     }
                // }
                
                $gd->email = '<i class="fa fa-envelope"></i> '.$nama.'<br />';
                $telp = '';
                if (isset($gd->telp)) {
                    $telp = $gd->telp;
                }
                if (empty($telp)) {
                    $telp = '-';
                }
                // by Muhammad Sofi 2 February 2022 19:27 | comment code replace 65 to empty string
                // if ($telp != '-') {
                //     $telp = preg_replace("/^\+?{$nation_code}/", '', $telp);
                // }
                $gd->email .= '<i class="fa fa-phone"></i> (+'.$gd->nation_code.')'.$telp.''; // by Muhammad Sofi 29 December 2021 15:00 | Separate between nation code no and telp no
            }

            if (isset($gd->ip_address)) {
                if (empty($gd->ip_address)) {
                    $gd->ip_address = '-';
                }
            }

            if (isset($gd->is_emulator)) {
                if (empty($gd->is_emulator)) {
                    $gd->is_emulator = 'no';
                } else {
                    $gd->is_emulator = 'yes';
                }
            }

            if (isset($gd->produk_active_count)) {
                $active = $gd->produk_active_count;
                $gd->produk_active_count = '<label class="label label-success"><i class="fa fa-dropbox"></i> '.number_format($active).' item(s)</label><br />';
                $pending = 0;
                if (isset($gd->produk_pending_count)) {
                    $pending = $gd->produk_pending_count;
                }
                $gd->produk_active_count .= '<label class="label label-warning"><i class="fa fa-dropbox"></i> '.number_format($pending).' item(s)</label>';
            }
            if (isset($gd->jenis_pelanggan)) {
                $gd->jenis_pelanggan = str_replace('S.D.', 's.d.', strtoupper($gd->jenis_pelanggan));
            }
            if (isset($gd->device_id)) {
                if(empty($gd->device_id)) {
                    $gd->device_id = "-";
                }
            }

            if (isset($gd->fnama_recommender)) {
                $email = "";
                $contact = "";
                $referral_code  = "";
                if (isset($gd->email_recommender)) {
                    $email = '<small>'.$gd->email_recommender.'</small><br />';
                }
                if (isset($gd->contact_recommender)) {
                    $contact = '<small>'.$gd->contact_recommender.'</small><br />';
                }
                if (isset($gd->kode_referral)) {
                    $referral_code = '<small>'.$gd->kode_referral.'</small><br />';
                }

                if(isset($gd->b_user_id_recruiter)) {
                    if($gd->b_user_id_recruiter != '0') {
                        $result = '<span style="font-size: 1.2em; font-weight: bolder;">'. $gd->fnama_recommender.'</span><br />'.$email.$contact.$referral_code;
                    } else {
                        $result = '<span style="font-size: 1.2em; font-weight: bolder;">-</span><br />';
                    }
                }
                $gd->fnama_recommender = $result;
            }

            if (isset($gd->is_active)) {
                if (!empty($gd->is_active)) {
                    $gd->is_active = '<label class="label label-success">Active</label>';
                } else {
                    $gd->is_active = '<label class="label label-default">Inactive</label>';
                }
                if (isset($gd->is_confirmed)) {
                    if (!empty($gd->is_confirmed)) {
                        $gd->is_confirmed = '<label class="label label-success">Verified Email</label>';
                    } else {
                        $gd->is_confirmed = '<label class="label label-danger">unverified email</label>';
                    }

                    if (!empty($gd->telp_is_verif)) {
                        $gd->telp_is_verif = '<label class="label label-success">Verified Phone</label>';
                    } else {
                        $gd->telp_is_verif = '<label class="label label-danger">unverified phone</label>';
                    }

                    // if (!empty($gd->telp_is_verif) && !empty($gd->is_confirmed)) {
                    //     $gd->telp_is_verif = '<label class="label label-success">Verified Email-Phone</label>';
                    // } else if(!empty($gd->telp_is_verif) && $gd->is_confirmed == 1) {
                    //     $gd->telp_is_verif = '<label class="label label-success">Verified Email-Phone</label>';
                    // }
                    // else {
                    //     $gd->telp_is_verif = '<label class="label label-danger">unverified email-phone</label>';
                    // }

                    // $gd->is_active = $gd->is_confirmed.' - '.$gd->is_active;
                    $gd->is_active = $gd->is_confirmed.' - '.$gd->telp_is_verif.' - '.$gd->is_active;
                }

                //by Donny Dennison - 29 august 2020 12:26
                //add label 2 step verified or not yet
                //START by Donny Dennison - 29 august 2020 12:26
                
                // by Muhammad Sofi 14 February 2022 9:56 | request by Mr. Jackie, remove 2 step 
                // if (isset($gd->telp_is_verif)) {
                //     if (!empty($gd->telp_is_verif)) {
                //         $gd->telp_is_verif = '<label class="label label-success">2 step</label>';
                //     } else {
                //         $gd->telp_is_verif = '<label class="label label-danger">2 step</label>';
                //     }
                //     $gd->is_active .= ' - '.$gd->telp_is_verif;
                // }

                //END by Donny Dennison - 29 august 2020 12:26

            }

            if (isset($gd->is_permanent_inactive)) {
                if ($gd->is_permanent_inactive == "0") {
                    $gd->is_permanent_inactive = '<label class="label label-danger">Yes</label>';
                } else {
                    $gd->is_permanent_inactive = '<label class="label label-success">No</label>';
                }
            }

            //START by Donny Dennison - 15 august 2022 13:16
            //Add fb_id, google_id, apple_id, and email status in cms
            if (empty($gd->fb_id) || is_null($gd->fb_id)) {
                $gd->fb_id = '<label class="label label-danger">No</label>';
            } else {
                $gd->fb_id = '<label class="label label-success">Yes</label>';
            }

            if (empty($gd->apple_id) || is_null($gd->apple_id)) {
                $gd->apple_id = '<label class="label label-danger">No</label>';
            } else {
                $gd->apple_id = '<label class="label label-success">Yes</label>';
            }

            if (empty($gd->google_id) || is_null($gd->google_id)) {
                $gd->google_id = '<label class="label label-danger">No</label>';
            } else {
                $gd->google_id = '<label class="label label-success">Yes</label>';
            }

            if ($gd->email_id == "no") {
                $gd->email_id = '<label class="label label-danger">No</label>';
            } else {
                $gd->email_id = '<label class="label label-success">Yes</label>';
            }
            //END by Donny Dennison - 15 august 2022 13:16
            //Add fb_id, google_id, apple_id, and email status in cms

            //by Donny Dennison - 23 august 2022 12:11
            //Add phone status in cms
            if ($gd->register_from != "phone") {
                $gd->register_from = '<label class="label label-danger">No</label>';
            } else {
                $gd->register_from = '<label class="label label-success">Yes</label>';
            }

            if (isset($gd->fcm_token)) {
                if($gd->fcm_token == "" || empty($gd->fcm_token)) {
                    $gd->fcm_token = "-";
                } else {
                    $gd->fcm_token = $gd->fcm_token;
                }
            }

        }
        //sleep(3);
        $another = array();
        $this->__jsonDataTable($ddata, $dcount);
    }

    //by Donny Dennison - 4 january 2021 14:36
    //change chat to open chatting
    public function getcustomerajax()
    {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;

        //standar input
        $search = $this->input->post("search");

        $ddata = $this->bcm->getAll($nation_code, -1, -1, "", "", $search, "", 1);

        $data = array();

        foreach ($ddata as $gd) {

            $data[] = array("id"=>$gd->id, "text"=>$gd->nama);

        }
        
        echo json_encode($data);

    }

    public function edit_status_permanent_inactive($id) {
        $d = $this->__init();
        $data = array();

        $this->__check_environment();
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        // $this->status = 800;
        // $this->message = 'One or more parameter are required';
        $du = $_POST;
        if(isset($du['id'])) unset($du['id']);
        // if(!isset($du['is_permanent_inactive'])) $du['is_permanent_inactive'] = "";
        $inactive_text = "spammer account";

        $status_inactive = "0"; // set default

        if($id > '0') {
            $this->bcm->trans_start();

                //check in db last register of user
                $getLastRegister = $this->bcm->getLastRegisterFrom($nation_code, $id);

                if($getLastRegister->register_from == "online" || $getLastRegister->register_from == "phone" || $getLastRegister->register_from == "undentified") {
                    $status_telp_verif = 1;
                    $status_email_verif = 1;
                } else {
                    $status_telp_verif = 0;
                    $status_email_verif = 0;
                }
                
                // 0 = yes ; 1 = no
                if($status_inactive == "1") {
                    $du = array();
                    $du['is_permanent_inactive'] = 1;
                    $du['permanent_inactive_by'] = '';
                    $du['permanent_inactive_date'] = date('Y-m-d H:i:s');
                    $du['api_mobile_token'] = "";
                    $du['fcm_token'] = "";
                    $du['is_active'] = 1;
                    $du['is_confirmed'] = $status_email_verif;
                    // $du['is_get_point'] = 1;
                    $du['is_online'] = 0;
                    $du['telp_is_verif'] = $status_telp_verif;
                    $du['inactive_text'] = '';
                    $res = $this->bcm->update_status_user($nation_code, $id, $du);
                } else if($status_inactive == "0") {
                    $du = array();
                    $du['is_permanent_inactive'] = 0;
                    $du['permanent_inactive_by'] = 'admin';
                    $du['permanent_inactive_date'] = date('Y-m-d H:i:s');
                    $du['api_mobile_token'] = "";
                    $du['fcm_token'] = "";
                    $du['is_active'] = 0;
                    $du['is_confirmed'] = 0;
                    // $du['is_get_point'] = 0;
                    $du['is_online'] = 0;
                    $du['telp_is_verif'] = 0;
                    $du['inactive_text'] = $inactive_text;
                    $res = $this->bcm->update_status_user($nation_code, $id, $du);
                }

                // if($status_inactive == "0") {
                //     // call BlackListUserWallet from blockchain API, stop rewarding and withdraw suspended user
                //     $userList = $this->bcm->getByIdData($nation_code, $id);
                //     $postdata = array();
                //     foreach ($userList as $user) {
                //         $postdata[] = array(
                //         'userWalletCode' => $this->__encryptdecrypt($user->user_wallet_code, "encrypt"),
                //         'countryIsoCode' => $this->blockchain_api_country,
                //         );
                //         // $this->seme_log->write("api_admin", "API_Admin, user wallet code : " . $user->user_wallet_code);
                //     }
                //     unset($user);

                //     $postdata = array(
                //         "userWalletList" => $postdata
                //     );

                //     $responseWalletApi = 0;
                //     $response = json_decode($this->__callBlockChainBlacklist($postdata));
                //     // $this->seme_log->write("api_admin", "API_Admin, response : ". $response);
                //     if(isset($response->responseCode)){
                //         if($response->responseCode == 0){
                //             $responseWalletApi = 1;
                //         }
                //     }
                //     unset($response);
                // } else { }

                if($res) {
                    $this->bcm->trans_commit();
                    $this->status = 200;
                    $this->message = "Success";
                } else {
                    $this->bcm->trans_rollback();
                    $this->status = 901;
                    $this->message = 'Failed to make data changes';
                }

            $this->bcm->trans_end();
        }
        $this->__json_out($data);
    }
}
