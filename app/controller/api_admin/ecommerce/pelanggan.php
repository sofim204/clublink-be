<?php

require_once (SENEROOT.'/vendor/autoload.php');
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

class Pelanggan extends JI_Controller
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
        $this->load("api_admin/b_user_model", 'bum');
        $this->load("api_admin/b_user_bankacc_model", 'bubam');
        $this->load("api_admin/e_chat_room_model", 'ecrm');
        $this->current_parent = 'ecommerce';
        $this->current_page = 'ecommerce_pelanggan';
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
    //     // Encrypt using the public key
    //     openssl_public_encrypt($text, $encrypted, $this->blockchain_api_public_key);
    //     return base64_encode($encrypted);
    //     }else if($type == "decrypt"){
    //     // Decrypt the data using the private key
    //     openssl_private_decrypt(base64_decode($text), $decrypted, openssl_pkey_get_private($this->blockchain_api_private_key, $this->blockchain_api_private_key_password));
    //     return $decrypted;
    //     }
    // }

    private function __activateGenerateLink($nation_code, $user_id)
    {
        $this->lib("conumtext");
        $token = $this->conumtext->genRand($type="str", $min=25, $max=30);
        $this->bum->setToken($nation_code, $user_id, $token, $kind="api_reg");
        return base_url('account/activate/index/'.$token);
    }

    private function __emailKonfirmasi($nation_code, $b_user_id)
    {
        $user = $this->bum->getById($nation_code, $b_user_id);
        if ($this->email_send && strlen($user->email)>4) {
            $link = $this->__activateGenerateLink($nation_code, $b_user_id);
            $replacer = array();
            $replacer['site_name'] = $this->app_name;
            $replacer['fnama'] = $user->fnama;
            $replacer['activation_link'] = $link;
            $this->seme_email->flush();
            $this->seme_email->replyto($this->site_name, $this->site_replyto);
            $this->seme_email->from($this->site_email, $this->site_name);
            $this->seme_email->subject('Registration Successful');
            $this->seme_email->to($user->email, $user->fnama);
            $this->seme_email->template('account_register');
            $this->seme_email->replacer($replacer);
            $this->seme_email->send();
        }
    }

    private function __passwordGenerateLink($nation_code, $user_id)
    {
        $this->lib("conumtext");
        $token = $this->conumtext->genRand($type="str", $min=18, $max=24);
        $this->bum->setToken($nation_code, $user_id, $token, $kind="api_web");
        return base_url('account/password/reset/'.$token);
    }

    private function __emailLupa($nation_code, $b_user_id)
    {
        $user = $this->bum->getById($nation_code, $b_user_id);
        if ($this->email_send && strlen($user->email)>4) {
            $link = $this->__passwordGenerateLink($nation_code, $user->id);

            $replacer = array();
            $replacer['fnama'] = $user->fnama;
            $replacer['site_name'] = $this->app_name;
            $replacer['site_name1'] = $this->app_name;
            $replacer['reset_link'] = $link;
            $this->seme_email->flush();
            $this->seme_email->replyto($this->site_name, $this->site_replyto);
            $this->seme_email->from($this->site_email, $this->site_name);
            $this->seme_email->subject('Forgot Password');
            $this->seme_email->to($user->email, $user->fnama);
            $this->seme_email->template('account_forgot');
            $this->seme_email->replacer($replacer);
            $this->seme_email->send();
        }
    }

    private function __hitungUmur($bdate)
    {
        $tgl1 = new DateTime($bdate);
        $tgl2 = new DateTime('today');
        return $tgl1->diff($tgl2)->y;
    }
    private function __kodeBuilder($fnama)
    {
        $fnama = strtoupper($fnama);
        $fnama_inisial = '00';
        if (strlen($fnama)==1) {
            $fnama_inisial = $fnama[0].'0';
        } elseif (strlen($fnama)>1) {
            $fnama_inisial = $fnama[0].$fnama[1];
        }
        $kode_last = 1;
        $kode_get = $this->bum->getKodeOnline($fnama_inisial);
        if (isset($kode_get->urutan)) {
            $kode_last = $kode_get->urutan;
        }
        return $fnama_inisial.sprintf($this->kode_pattern, $kode_last);
    }

    private function __uploadUserImage($b_user_id)
    {
        /*******************
        * Only these origins will be allowed to upload images *
        ******************/
        $folder = SENEROOT.DIRECTORY_SEPARATOR.$this->media_user.DIRECTORY_SEPARATOR;
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
        if (is_uploaded_file($temp['tmp_name'])) {
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
            // Verify extension
            $ext = pathinfo($temp['name'], PATHINFO_EXTENSION);
            if (!in_array(strtolower($ext), array("jpg", "png","jpeg"))) {
                header("HTTP/1.0 500 Invalid extension.");
                return 0;
            }
            if ($ext == 'jpeg') {
                $ext = "jpg";
            }

            // Create magento style media directory
            $name  = md5($b_user_id);
            if (strlen($name)==1) {
                $name=$name.'-';
            }
            $name1 = date("Y");
            $name2 = date("m");
            if (PHP_OS == "WINNT") {
                if (!is_dir($ifol)) {
                    mkdir($ifol);
                }
                $ifol = $ifol.DIRECTORY_SEPARATOR.$name1.DIRECTORY_SEPARATOR;
                if (!is_dir($ifol)) {
                    mkdir($ifol);
                }
                $ifol = $ifol.DIRECTORY_SEPARATOR.$name2.DIRECTORY_SEPARATOR;
                if (!is_dir($ifol)) {
                    mkdir($ifol);
                }
            } else {
                if (!is_dir($ifol)) {
                    mkdir($ifol, 0775, true);
                }
                $ifol = $ifol.DIRECTORY_SEPARATOR.$name1.DIRECTORY_SEPARATOR;
                if (!is_dir($ifol)) {
                    mkdir($ifol, 0775, true);
                }
                $ifol = $ifol.DIRECTORY_SEPARATOR.$name2.DIRECTORY_SEPARATOR;
                if (!is_dir($ifol)) {
                    mkdir($ifol, 0775, true);
                }
            }

            // Accept upload if there was no origin, or if it is an accepted origin
            $filetowrite = $ifol.$name.'.'.$ext;
            $filetowrite = str_replace('//', '/', $filetowrite);

            if (file_exists($filetowrite)) {
                unlink($filetowrite);
                $rand = rand(0, 999);
                $name  = md5($b_user_id).'-'.$rand.'-';
                $filetowrite = $ifol.$name.'.'.$ext;
                $filetowrite = str_replace('//', '/', $filetowrite);
                if (file_exists($filetowrite)) {
                    unlink($filetowrite);
                }
            }
            move_uploaded_file($temp['tmp_name'], $filetowrite);
            if (file_exists($filetowrite)) {
                $this->lib("wideimage/WideImage", "inc");
                WideImage::load($filetowrite)->resize(300)->saveToFile($filetowrite);
                WideImage::load($filetowrite)->crop('center', 'center', 300, 300)->saveToFile($filetowrite, 8);
                return $this->media_user."/".$name1."/".$name2."/".$name.'.'.$ext;
            } else {
                return 0;
            }


            // Respond to the successful upload with JSON.
            // Use a location key to specify the path to the saved image resource.
            // { location : '/your/uploaded/image/file'}
        } else {
            // Notify editor that the upload failed
            //header("HTTP/1.0 500 Server Error");
            return 0;
        }
    }

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
        $tbl_as = $this->bum->getTblAlias();
        $tbl7_as = $this->bum->getTblAlias7();

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
                $sortCol = "$tbl_as.is_admin";
                break;    
            case 4:
                $sortCol = "$tbl_as.email";
                break;
            case 5:
                $sortCol = "$tbl_as.ip_address";
                break;
            case 6:
                $sortCol = "$tbl_as.is_emulator";
                break;
            case 7:
                $sortCol = "$tbl_as.is_active";
                break;
            case 8:
                $sortCol = "$tbl_as.is_permanent_inactive";
                break;
            case 9:
                $sortCol = "$tbl7_as.fnama";
                break;
            // Improve By Aditya Adi Prabowo 8/9/2020 
            // Add Device field on customer
            // Start Improve
            case 10:
                $sortCol = "$tbl_as.device";
                break;
            case 11:
                $sortCol = "$tbl_as.device_id";
                break;
            // End Improve
            case 12:
                $sortCol = "$tbl_as.fcm_token";
                break;
            case 13:
                $sortCol = "$tbl_as.alamat2";
                break;

            //START by Donny Dennison - 15 august 2022 13:16
            //Add fb_id, google_id, apple_id, and email status in cms
            // case 13:
            //     $sortCol = "$tbl_as.fb_id";
            //     break;
            // case 14:
            //     $sortCol = "$tbl_as.apple_id";
            //     break;
            // case 15:
            //     $sortCol = "$tbl_as.google_id";
            //     break;
            // case 16:
            //     $sortCol = "$tbl_as.email_id";
            //     break;
            //END by Donny Dennison - 15 august 2022 13:16
            //Add fb_id, google_id, apple_id, and email status in cms

            //by Donny Dennison - 23 august 2022 12:11
            //Add phone status in cms
            case 13:
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

        $dcount = $this->bum->countAll($nation_code, $keyword, $is_confirmed, $is_active);
        $ddata = $this->bum->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $is_confirmed, $is_active);

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
                if(isset($gd->is_admin))  {
                    if($gd->is_admin == "1") {
                        $nama = $gd->nama . '<i class="fa fa-star" style="color: blue;"></i> ';
                    } else {
                        $nama = $gd->nama;
                    }
                }
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
            // if (empty($gd->fb_id) || is_null($gd->fb_id)) {
            //     $gd->fb_id = '<label class="label label-danger">No</label>';
            // } else {
            //     $gd->fb_id = '<label class="label label-success">Yes</label>';
            // }

            // if (empty($gd->apple_id) || is_null($gd->apple_id)) {
            //     $gd->apple_id = '<label class="label label-danger">No</label>';
            // } else {
            //     $gd->apple_id = '<label class="label label-success">Yes</label>';
            // }

            // if (empty($gd->google_id) || is_null($gd->google_id)) {
            //     $gd->google_id = '<label class="label label-danger">No</label>';
            // } else {
            //     $gd->google_id = '<label class="label label-success">Yes</label>';
            // }

            // if ($gd->email_id == "no") {
            //     $gd->email_id = '<label class="label label-danger">No</label>';
            // } else {
            //     $gd->email_id = '<label class="label label-success">Yes</label>';
            // }
            //END by Donny Dennison - 15 august 2022 13:16
            //Add fb_id, google_id, apple_id, and email status in cms

            //by Donny Dennison - 23 august 2022 12:11
            //Add phone status in cms
            // if ($gd->register_from != "phone") {
            //     $gd->register_from = '<label class="label label-danger">No</label>';
            // } else {
            //     $gd->register_from = '<label class="label label-success">Yes</label>';
            // }

            if (isset($gd->register_from)) {
                $gd->register_from = ucwords($gd->register_from);
            }

            if (isset($gd->fcm_token)) {
                if($gd->fcm_token == "" || empty($gd->fcm_token)) {
                    $gd->fcm_token = "-";
                } else {
                    $gd->fcm_token = $gd->fcm_token;
                }
            }

            if (isset($gd->is_admin)) {
                if ($gd->is_admin == "0") {
                    $gd->is_admin = '<label class="label label-danger">No</label>';
                } else {
                    $gd->is_admin = '<label class="label label-success">Yes</label>';
                }
            }

        }
        //sleep(3);
        $another = array();

        if(mb_strlen($keyword) > 0 || mb_strlen($is_confirmed) > 0 || mb_strlen($is_active) > 0) {
            $new_dcount = $dcount;
        } else {
            $new_dcount = $dcount+40000;
        }
        $this->__jsonDataTable($ddata, $new_dcount);
    }
    public function tambah()
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
        $di = $_POST;
        if (!isset($di['email'])) {
            $di['email'] = "";
        }
        if (!isset($di['fnama'])) {
            $di['fnama'] = "";
        }
        if ($this->__mbLen($di['fnama'])>=64) {
            $this->status = 1749;
            $this->message = 'Name too long';
            $this->__json_out($data);
            die();
        }
        if (strlen($di['fnama'])>0) {
            $check = $this->bum->cekEmail($di['email']);
            if (empty($check)) {
                $di['email'] = strtolower($di['email']);
                $di['kode'] = $this->__kodeBuilder($di['fnama']);
                if ($di['bdate']) {
                    $di['umur'] = $this->__hitungUmur($di['bdate']);
                }
                $res = $this->bum->set($di);
                if ($res) {
                    $this->status = 200;
                    $this->message = 'Data baru Success ditambahkan';
                } else {
                    $this->status = 900;
                    $this->message = 'Tidak dapat menyimpan data baru, silakan coba beberapa saat lagi';
                }
            } else {
                $this->status = 447;
                $this->message = 'Email sudah digunakan, coba yang lain';
            }
        } else {
            $this->status = 449;
            $this->message = 'Silakan dilengkapi namanya';
        }
        $this->__json_out($data);
    }
    public function detail($id)
    {
        // $id = (int) $id;
        $d = $this->__init();
        $data = array();
        // if ($id<=0) {
        //     $this->status = 450;
        //     $this->message = 'Invalid ID';
        //     $this->__json_out($data);
        //     die();
        // }
        if (!$this->admin_login && empty($id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $data = $this->bum->getById($nation_code, $id);
        if (!isset($data->id)) {
            $this->status = 451;
            $this->message = 'Invalid ID or user has been deleted';
            $this->__json_out($data);
            die();
        }

        // if(isset($data->fb_id)) {
		// 	if (!empty($data->fb_id) || ($data->fb_id) != "NULL") {
		// 		$data->email = str_replace("@sellon.net","@gmail.com.", $data->email); 
		// 	}
		// }

		// if(isset($data->register_from)) {
		// 	if ($data->register_from == "phone") {
		// 		// $data->email = str_replace("@sellon.net","@gmail.com.", $data->email);
        //         $data->email = $data->telp."@sellon.net";
		// 	}
		// }

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data);
    }
    public function edit($id)
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
        $du = $_POST;
        if (isset($du['id'])) {
            unset($du['id']);
        }
        if ($this->__mbLen($du['fnama'])>=64) {
            $this->status = 1749;
            $this->message = 'Name too long';
            $this->__json_out($data);
            die();
        }

        $id = (int) $id;
        if ($id>'0') {
            $user = $this->bum->getById($id);
            if (isset($user->id)) {
                $check = 0;
                if (strlen($user->kode)<=9) {
                    $du['kode'] = $this->__kodeBuilder($user->fnama);
                }
                if (isset($du['bdate'])) {
                    $du['umur'] = $this->__hitungUmur($du['bdate']);
                } else {
                    $du['umur'] = $this->__hitungUmur($user->bdate);
                }
                if (isset($du['email'])) {
                    $du['email'] = strtolower($du['email']);
                    if ($du['email'] != strtolower($user->email)) {
                        $check = $this->bum->cekEmail($du['email']);
                    }
                }

                if (empty($check)) {
                    $res = $this->bum->update($id, $du);
                    if ($res) {
                        $this->status = 200;
                        $this->message = 'Perubahan Success diterapkan';
                        $res = $this->__uploadUserImage($id);
                    } else {
                        $this->status = 901;
                        $this->message = 'Failed to make data changes';
                    }
                } else {
                    $this->status = 104;
                    $this->message = 'Email sudah digunakan, silakan coba kode lain';
                }
            } else {
                $this->status = 448;
                $this->message = 'ID Pelanggan tidak dapat ditemukan';
            }
        } else {
            $this->status = 441;
            $this->message = 'Salah satu parameter ada yang kurang';
        }
        $this->__json_out($data);
    }
    public function hapus($id)
    {
        // $id = (int) $id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $user = $this->bum->getById($id);
        if (isset($user->id)) {
            if (!strstr($user->image, 'default') && strlen($user->image)>4) {
                if (file_exists(SENEROOT.DIRECTORY_SEPARATOR.$user->image)) {
                    unlink(SENEROOT.DIRECTORY_SEPARATOR.$user->image);
                }
            }
            $this->status = 200;
            $this->message = 'Success';
            $res = $this->bum->del($id);
            if (!$res) {
                $this->status = 902;
                $this->message = 'Failed while deleting data from database';
            }
        } else {
            $this->status = 449;
            $this->message = 'Pelanggan tidak dapat ditemukan';
        }
        $this->__json_out($data);
    }
    public function cari()
    {
        $d = $this->__init();
        $data = array();
        $data['pelanggan'] = array();
        if (!$this->admin_login && empty($id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $this->status = 200;
        $this->message = 'Success';
        $keyword = $this->input->request("keyword");
        $data['pelanggan'] = $this->bum->cari($nation_code, $keyword);
        foreach ($data['pelanggan'] as &$pelanggan) {
            if (isset($pelanggan->is_active)) {
                $h = '';
                if (!empty($pelanggan->is_active)) {
                    $h = '<label class="label label-success">Active</label>';
                } else {
                    $h = '<label class="label label-default">Inactive</label>';
                }
                $pelanggan->is_active = $h;
            }
        }
        $this->__json_out($data);
    }

    public function image_change($id)
    {
        // $id = (int) $id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        // if ($id<=0) {
        //     $this->status = 440;
        //     $this->message = 'Invalid ID Pelanggan';
        //     $this->__json_out($data);
        //     die();
        // }

        $user = $this->bum->getById($id);
        if (isset($user->id)) {
            $image = $this->__uploadUserImage($id);
            if (!empty($image)) {
                $du = array();
                $du['image'] = $image;
                $res = $this->bum->update($id, $du);
                if ($res) {
                    $this->status = 200;
                    $this->message = 'Success';
                } else {
                    $this->status = 941;
                    $this->message = 'Cant update to database';
                }
            } else {
                $this->status = 541;
                $this->message = 'Upload image failed, please try another image';
            }
        } else {
            $this->status = 449;
            $this->message = 'Pelanggan tidak dapat ditemukan';
        }
        $this->__json_out($data);
    }
    public function activated($id)
    {
        $d = $this->__init();
        $data = array();
        // $id = (int) $id;
        // if ($id<=0) {
        //     $this->status = 450;
        //     $this->message = 'Invalid ID';
        //     $this->__json_out($data);
        //     die();
        // }
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $user = $this->bum->getById($nation_code, $id);
        if (!isset($user->id)) {
            $this->status = 451;
            $this->message = 'Invalid ID or user has been deleted';
            $this->__json_out($data);
            die();
        }
        if (!empty($user->is_active)) {
            $this->status = 457;
            $this->message = 'User already activated';
            $this->__json_out($data);
            die();
        }
        $du = array("is_active"=>1);
        $res = $this->bum->update($nation_code, $id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->status = 920;
            $this->message = 'Failed change data to database';
        }
        $this->__json_out($data);
    }
    public function deactivated($id)
    {
        $d = $this->__init();
        $data = array();
        // $id = (int) $id;
        // if ($id<=0) {
        //     $this->status = 450;
        //     $this->message = 'Invalid ID';
        //     $this->__json_out($data);
        //     die();
        // }
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $user = $this->bum->getById($nation_code, $id);
        if (!isset($user->id)) {
            $this->status = 451;
            $this->message = 'Invalid ID or user has been deleted';
            $this->__json_out($data);
            die();
        }
        if (empty($user->is_active)) {
            $this->status = 457;
            $this->message = 'User already deactivated';
            $this->__json_out($data);
            die();
        }
        // $du = array("api_mobile_token"=>"null","is_confirmed"=>0,"is_active"=>0);
        $du = array("api_mobile_token"=>"null","fcm_token"=>"","is_confirmed"=>0,"is_active"=>0);
        $res = $this->bum->update($nation_code, $id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->status = 920;
            $this->message = 'Failed change data to database';
        }
        $this->__json_out($data);
    }

    public function email_konfirmasi($id)
    {
        $d = $this->__init();
        $data = array();
        // $id = (int) $id;
        // if ($id<=0) {
        //     $this->status = 450;
        //     $this->message = 'Invalid ID';
        //     $this->__json_out($data);
        //     die();
        // }
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $user = $this->bum->getById($nation_code, $id);
        if (!isset($user->id)) {
            $this->status = 451;
            $this->message = 'Invalid ID or user has been deleted';
            $this->__json_out($data);
            die();
        }
        $this->__emailKonfirmasi($nation_code, $user->id);
        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data);
    }

    public function email_lupa($id)
    {
        $d = $this->__init();
        $data = array();
        // $id = (int) $id;
        // if ($id<=0) {
        //     $this->status = 450;
        //     $this->message = 'Invalid ID';
        //     $this->__json_out($data);
        //     die();
        // }
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $user = $this->bum->getById($nation_code, $id);
        if (!isset($user->id)) {
            $this->status = 451;
            $this->message = 'Invalid ID or user has been deleted';
            $this->__json_out($data);
            die();
        }
        $this->__emailLupa($nation_code, $user->id);
        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data);
    }
    // public function bank_account()
    // {
    //     $d = $this->__init();
    //     $data = array();
    //     if (!$this->admin_login) {
    //         $this->status = 400;
    //         $this->message = 'Unauthorized access';
    //         header("HTTP/1.0 400 Unauthorized");
    //         $this->__json_out($data);
    //         die();
    //     }
    //     $pengguna = $d['sess']->admin;
    //     $nation_code = $pengguna->nation_code;

    //     $nation_code = $this->input->post("nation_code");
    //     $b_user_id = (int) $this->input->post("b_user_id");
    //     if ($b_user_id<='0') {
    //         $this->status = 450;
    //         $this->message = 'Invalid B_USER_ID';
    //         $this->__json_out($data);
    //         die();
    //     }

    //     $user = $this->bum->getById($nation_code, $b_user_id);
    //     if (!isset($user->id)) {
    //         $this->status = 451;
    //         $this->message = 'Invalid ID or user has been deleted';
    //         $this->__json_out($data);
    //         die();
    //     }
    //     $a_bank_id = (int) $this->input->post("a_bank_id");
    //     if ($a_bank_id<='0') {
    //         $this->status = 452;
    //         $this->message = 'Invalid A_BANK_ID';
    //         $this->__json_out($data);
    //         die();
    //     }
    //     if ($this->__mbLen($nama)>=32) {
    //         $this->status = 453;
    //         $this->message = 'Bank account name (holder) too long';
    //         $this->__json_out($data);
    //         die();
    //     }
    //     if ($this->__mbLen($nomor)>=64) {
    //         $this->status = 454;
    //         $this->message = 'Bank account number too long';
    //         $this->__json_out($data);
    //         die();
    //     }
    //     $res = 0;
    //     $bubam = $this->bubam->getByUserId($nation_code, $b_user_id);
    //     if (isset($bubam->nation_code)) {
    //         $du = array("nomor"=>$nomor,"nama"=>$nama,"a_bank_id"=>$a_bank_id);
    //         $res = $this->bubam->update($nation_code, $b_user_id, $du);
    //     } else {
    //         $di = array();
    //         $di['nation_code'] = $nation_code;
    //         $di['a_bank_id'] = $a_bank_id;
    //         $di['b_user_id'] = $b_user_id;
    //         $di['nomor'] = $nomor;
    //         $di['nama'] = $nama;
    //         $di['is_default'] = 1;
    //         $res = $this->bubam->set($di);
    //     }

    //     if ($res) {
    //         $this->status = 200;
    //         $this->message = 'Success';
    //     } else {
    //         $this->status = 920;
    //         $this->message = 'Failed change data to database';
    //     }
    //     $this->__json_out($data);
    // }

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

        // $ddata = $this->bum->getAll($nation_code, -1, -1, "", "", $search, "", 1);
        $data = $this->bum->getByNameChat($nation_code, -1, -1, "", "", $search, "", 1);

        // $data = array();

        // foreach ($ddata as $gd) {

        //     $data[] = array("id"=>$gd->id, "text"=>$gd->nama);

        // }
        
        echo json_encode($data);

    }

    public function edit_status_verification_user($id) {
		$d = $this->__init();
		$data = array();

		// $id = (int) $id;
		// if($id<=0){
		// 	$this->status = 451;
		// 	$this->message = 'Invalid ID';
		// 	$this->__json_out($data);
		// 	die();
		// }
        $this->__check_environment();
		$pengguna = $d['sess']->admin;
    	$nation_code = $pengguna->nation_code;

		$this->status = 800;
		$this->message = 'One or more parameter are required';
		$du = $_POST;
		if(isset($du['id'])) unset($du['id']);
		if(!isset($du['is_confirmed'])) $du['is_confirmed'] = "";

		if($id > '0') {
			$res = $this->bum->update_status_user($nation_code, $id, $du);
			if($res) {
				$this->status = 200;
				$this->message = 'Perubahan berhasil diterapkan';
			} else {
				$this->status = 901;
				$this->message = 'Failed to make data changes';
			}
		}
		$this->__json_out($data);
	}

    public function edit_status_permanent_inactive($id) {
        $d = $this->__init();
        $data = array();

        // $id = (int) $id;
        // if($id<='0'){
        //  $this->status = 451;
        //  $this->message = 'Invalid ID';
        //  $this->__json_out($data);
        //  die();
        // }
        $this->__check_environment();
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        // $this->status = 800;
        // $this->message = 'One or more parameter are required';
        $du = $_POST;
        if(isset($du['id'])) unset($du['id']);
        if(!isset($du['is_permanent_inactive'])) $du['is_permanent_inactive'] = "";
        // if(!empty($du['inactive_text'])) $du['inactive_text'] = "permanent inactive by admin";
        $inactive_text = $du['inactive_text'];

        $status_inactive = $du['is_permanent_inactive'];

        if($id > '0') {
            $this->bum->trans_start();

            // check if user is group owner
            // $checkUserIsOwnerGroup = $this->bum->countUserIsOwnerGroup($nation_code, $id, array(0 => 1, 1 => 24));
            // if($checkUserIsOwnerGroup > 0) {
            //     $this->status = 900;
            //  $this->message = 'User is an owner of '. $checkUserIsOwnerGroup .' group, cannot be delete';
            //     $this->__json_out($data);
            //     die();
            // } else {

                // for other checking
                // else {
                //     $this->status = 201;
                //  $this->message = 'User is not an owner';
                //     $this->__json_out($data);
                //     die();
                // }

                // check if user already join group chat, auto leave group

                // rule
                // 1. seach for community only in chat_room
                // 

                // $roomChat = $this->ecrm->getChatRoomByUserId($nation_code, $id);
                // $this->debug($roomChat);
                // die();

                // if (count($roomChat)) {
                //     foreach ($roomChat as $room) { 
                        
                //         $participant_list = $this->ecpm->getParticipantByRoomChatId($nation_code, $room->id);

                //     }
                // }


                // $totalUnfinishedOfferSeller = $this->bum->countAllUnfinisedOffer($nation_code, 'offer', "seller", $id, array(0 => "offering", 1=>"accepted", 2 => "waiting review from seller", 3 => "waiting review from buyer"));
                // if($totalUnfinishedOfferSeller > 0){
                //     $di = array();
                //     $di['offer_status'] = 'rejected';
                //     $di['b_user_id_seller'] = $id;
                // }
        
                // $totalUnfinishedOfferBuyer = $this->bum->countAllUnfinisedOffer($nation_code, 'offer', "buyer", $id, array(0 => "offering", 1=>"accepted", 2 => "waiting review from seller", 3 => "waiting review from buyer"));
                // if($totalUnfinishedOfferBuyer > 0){
                //     $di = array();
                //     $di['offer_status'] = 'cancelled';
                //     $di['b_user_id_starter'] = $id;
                // }

                //check in db last register of user
                $getLastRegister = $this->bum->getLastRegisterFrom($nation_code, $id);

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
                    $res = $this->bum->update_status_user($nation_code, $id, $du);
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
                    $res = $this->bum->update_status_user($nation_code, $id, $du);
                }

                // if($status_inactive == "0") {
                //     // call BlackListUserWallet from blockchain API, stop rewarding and withdraw suspended user
                //     $userList = $this->bum->getByIdData($nation_code, $id);
                //     // if(count($userList) > 0){
                //         $postdata = array();
                //         foreach ($userList as $user) {
                //             $postdata[] = array(
                //             'userWalletCode' => $this->__encryptdecrypt($user->user_wallet_code, "encrypt"),
                //             'countryIsoCode' => $this->blockchain_api_country,
                //             );
                //         }
                //         unset($user);
            
                //         $postdata = array(
                //             "userWalletList" => $postdata
                //         );
            
                //         $responseWalletApi = 0;
                //         $response = json_decode($this->__callBlockChainBlacklist($postdata));
                //         if(isset($response->responseCode)){
                //             if($response->responseCode == 0){
                //                 $responseWalletApi = 1;
                //             }
                //         }
                //         unset($response);
                //     // }
                //     // unset($userList);
                // } else {
                //     // echo "kaga panggil";
                // }
                
                if($res) {
                    $this->bum->trans_commit();
                    // if($status_inactive == "0") {
                    //     $condition = "Stop";
                    // } else if($status_inactive == "1") {
                    //     $condition = "Active";
                    // } else {}

                    $this->status = 200;
                    // $this->message = "Account Successfully " . $condition;
                    $this->message = "Success";
                    
                    // $response = json_decode($this->__callBlockChainBlacklist($get_data->user_wallet_code));
                    // if(isset($response->responseCode)){
                    //     if($response->responseCode == 0){
                    //       $this->status = 200;
                    //       $this->message = "Success";
                    //     }else{
                    //       $this->status = 1001;
                    //       $this->message = "Sorry!!  We're now upgrading Wallet service to meet huge user connections.";
                    //     }
                    // } else {
                    //     $this->status = 1001;
                    //     $this->message = "Sorry!!  We're now upgrading Wallet service to meet huge user connections.";
                    // }
                } else {
                    $this->bum->trans_rollback();
                    $this->status = 901;
                    $this->message = 'Failed to make data changes';
                }
            // }

            $this->bum->trans_end();
        }
        $this->__json_out($data);
    }

    public function edit_status_active_user($id) {
		$d = $this->__init();
		$data = array();

		// $id = (int) $id;
		// if($id<=0){
		// 	$this->status = 451;
		// 	$this->message = 'Invalid ID';
		// 	$this->__json_out($data);
		// 	die();
		// }
        $this->__check_environment();
		$pengguna = $d['sess']->admin;
    	$nation_code = $pengguna->nation_code;

		$this->status = 800;
		$this->message = 'One or more parameter are required';
		$du = $_POST;
		if(isset($du['id'])) unset($du['id']);
		if(!isset($du['is_active'])) $du['is_active'] = "";

		if($id > '0') {
            if($du['is_active'] == "0") {
                $du = array("is_confirmed" => 0, "is_active" => 0, "api_mobile_token" => "null", "fcm_token" => ""); // prevent send notif if user inactive
            } else {
                $du = array("is_active" => 1);
            }
            
			$res = $this->bum->update_status_user($nation_code, $id, $du);
			if($res) {
				$this->status = 200;
				$this->message = 'Perubahan berhasil diterapkan';
			} else {
				$this->status = 901;
				$this->message = 'Failed to make data changes';
			}
		}
		$this->__json_out($data);
	}

    public function delete_user_data($id) {
        // $id = (int) $id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $data = $this->bum->getById($nation_code, $id);

        $this->bum->trans_start();

		//delete user data
		$res = $this->bum->delete_user($id, $nation_code);

		//delete user alamat data
		$res = $this->bum->delete_user_alamat($id, $nation_code);

        // //delete user bank acc data
		// $res = $this->bum->delete_user_bankacc($id, $nation_code);

        // //delete user card data
		// $res = $this->bum->delete_user_card($id, $nation_code);
        
        //delete user follow data
		// $res = $this->bum->delete_user_follow($id, $nation_code);
       
        // //delete user productwanted data
		// $res = $this->bum->delete_user_productwanted($id, $nation_code);
       
        // //delete user setting data
		// $res = $this->bum->delete_user_setting($id, $nation_code);
       
        // //delete user wish product data
		// $res = $this->bum->delete_user_wish_product($id, $nation_code);

        // // delete user community, attachment, highlight data
        // $get_community_data = $this->bum->get_user_community($id, $nation_code);
        // foreach($get_community_data as $gcd){
        //     if(isset($gcd->community_id)) {
        //         $res = $this->bum->delete_user_community_attachment($gcd->community_id, $nation_code);
        //         $res = $this->bum->delete_user_highlight_community($gcd->community_id, $nation_code);
        //     }
        // }
        // $res = $this->bum->delete_user_community($id, $nation_code);

        // //delete community report data
        // $res = $this->bum->delete_user_community_report($id, $nation_code);

        // //delete community like data
        // $res = $this->bum->delete_user_community_like($id, $nation_code);
        
        // // delete community discussion, attachment data
        // $get_community_discussion_data = $this->bum->get_user_community_discussion($id, $nation_code);
        // foreach($get_community_discussion_data as $gccd){
        //     if(isset($gccd->community_discussion_id)) {
        //         $res = $this->bum->delete_user_community_discussion_attachment($gccd->community_discussion_id, $nation_code);
        //     }
        // }
        // $res = $this->bum->delete_user_community_discussion($id, $nation_code);

        // //delete community discussion report data
        // $res = $this->bum->delete_user_community_discussion_report($id, $nation_code);

        // // delete product, product detail automotive, product foto(photo and video) data
        // $get_product_data = $this->bum->get_user_product($id, $nation_code);
        // foreach($get_product_data as $gpd){
        //     if(isset($gpd->c_produk_id)) {
        //         $res = $this->bum->delete_user_product_detail_automotive($gpd->c_produk_id, $nation_code);
        //         $res = $this->bum->delete_user_product_foto($gpd->c_produk_id, $nation_code);
        //     }
        // }
        // $res = $this->bum->delete_user_product($id, $nation_code);

        // //delete product share history data
        // $res = $this->bum->delete_user_product_share_history($id, $nation_code);

        // //delete product laporan data
        // $res = $this->bum->delete_user_product_laporan($id, $nation_code);

        // //delete cart data
        // $res = $this->bum->delete_user_cart($id, $nation_code);

        // // delete order, order_detail, order_detail_item, order_process data
        // $get_order_data = $this->bum->get_user_order($id, $nation_code);
        // foreach($get_order_data as $god){
        //     if(isset($god->order_id)) {
        //         $res = $this->bum->delete_user_order_detail($god->order_id, $nation_code);
        //         $res = $this->bum->delete_user_order_detail_item($god->order_id, $nation_code);
        //         $res = $this->bum->delete_user_order_process($god->order_id, $nation_code);
        //     }
        // }
        // $res = $this->bum->delete_user_order($id, $nation_code);

        // //delete order alamat data
        // $res = $this->bum->delete_user_order_alamat($id, $nation_code);
        
        // //delete order detail pickup data
        // $res = $this->bum->delete_user_order_detail_pickup($id, $nation_code);

        // //delete wishlist data
        // $res = $this->bum->delete_user_wishlist($id, $nation_code);

        // // delete chat room, chat attachment data
        // $get_chat_room_data = $this->bum->get_user_chat_room($id, $nation_code);
        // foreach($get_chat_room_data as $gcrd){
        //     if(isset($gcrd->chat_room_id)) {
        //         $res = $this->bum->delete_user_chat_attachment($gcrd->chat_room_id, $nation_code);
        //     }
        // }
        // $res = $this->bum->delete_user_chat_room($id, $nation_code);

        // //delete chat data
        // $res = $this->bum->delete_user_chat($id, $nation_code);

        // //delete chat participant data
        // $res = $this->bum->delete_user_chat_participant($id, $nation_code);

        // //delete chat read data
        // $res = $this->bum->delete_user_chat_read($id, $nation_code);

        // //delete chat complain data
        // $res = $this->bum->delete_user_complain($id, $nation_code);

        // //delete chat likes data
        // $res = $this->bum->delete_user_likes($id, $nation_code);

        // //delete chat rating data
        // $res = $this->bum->delete_user_rating($id, $nation_code);

        // //delete discussion product data
        // $res = $this->bum->delete_user_discussion($id, $nation_code);

        // //delete discussion product report data
        // $res = $this->bum->delete_user_discussion_report($id, $nation_code);

        // //delete verification phone number data
        // $res = $this->bum->delete_user_verification_phone_number($id, $nation_code); //singapore

        // //delete leaderboard point area data
        // $res = $this->bum->delete_user_leaderboard_point_area($id, $nation_code);

        // //delete leaderboard point history data
        // $res = $this->bum->delete_user_leaderboard_point_history($id, $nation_code);

        // //delete leaderboard point limit data
        // $res = $this->bum->delete_user_leaderboard_point_limit($id, $nation_code);

        // //delete leaderboard point total data
        // $res = $this->bum->delete_user_leaderboard_point_total($id, $nation_code);

        // //delete leaderboard ranking data
        // $res = $this->bum->delete_user_leaderboard_ranking($id, $nation_code);

        /*
        * using query return $this->db->exec()

        $res = $this->bum->delete_user_community_attachment_and_highlight($id, $nation_code);
        $res = $this->bum->delete_user_community($id, $nation_code); // tidak dipakai ttp bisa kehapus dari query yg atas
        
        */
        
		if($res) {
            $this->bum->trans_commit();
			$this->status = 200;
			$this->message = 'Success';
		} else {
            $this->bum->trans_rollback();
			$this->status = 802;
			$this->message = 'Failed to delete data';
		}
	    
        $this->bum->trans_end();
        $this->__json_out($data);
    }

    public function edit_status_as_admin($id) {
		$d = $this->__init();
		$data = array();

		if($id<='0'){
			$this->status = 451;
			$this->message = 'Invalid ID';
			$this->__json_out($data);
			die();
		}
        $this->__check_environment();
		$pengguna = $d['sess']->admin;
    	$nation_code = $pengguna->nation_code;

		$this->status = 800;
		$this->message = 'One or more parameter are requireds';
		$du = $_POST;
		if(isset($du['id'])) unset($du['id']);
		if(!isset($du['is_admin'])) $du['is_admin'] = "";

		if($id > 0) {
			$res = $this->bum->update_status_user($nation_code, $id, $du);
			if($res) {
				$this->status = 200;
				$this->message = 'Perubahan berhasil diterapkan';
			} else {
				$this->status = 901;
				$this->message = 'Failed to make data changes';
			}
		}
		$this->__json_out($data);
	}
}
