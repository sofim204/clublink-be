<?php

class Sellon_Analytics extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_mobile/g_sellon_analytics_model", 'gsam');
        $this->load("api_mobile/common_code_model", 'ccm');
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

    public function insert() {
        //initial
        $dt = $this->__init();

        $data = array();
        $data['analytics'] = new stdClass();

        $this->seme_log->write("api_mobile", "Sellon Analytics -> " . json_encode($_POST));

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_analytics");
            die();
        }

        $data['is_email_verif_avail'] = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C24")->remark;
        $data['is_phone_verif_avail'] = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C25")->remark;

        // init
        $corner = $this->input->post('corner');
        $type = $this->input->post('type') ? $this->input->post('type') : "";
        $category = $this->input->post('category') ? $this->input->post('category') : "";
        $detail = $this->input->post('detail') ? $this->input->post('detail') : "";
        $sub_detail = $this->input->post('sub_detail') ? $this->input->post('sub_detail') : "";

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c && $corner != "Wallet") { // except corner = wallet don't need checking apikey
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_analytics");
            die();
        }

        // check if corner is empty
        if (empty($corner)) {
            $this->status = 400;
            $this->message = "Corner can't be empty!";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_analytics");
            die();
        }

        // init
        $corner_seq = "null";
        $type_seq = "null";

        // corner
        if($corner == "HOME" || $corner == "Home") {
            $corner_seq = "1";
            // type
            if($type == "HOME" || $type == "Home" || $type == "Main") {
                $type_seq = "1";
            } else if($type == "Product Search(Search by keyword)") {
                $type_seq = "2";
            } else if($type == "Post Search(Search by keyword)") {
                $type_seq = "3";
            } else if($type == "Account Search(Search by keyword)") {
                $type_seq = "4";
            }
        } else if($corner == "Buy&Sell") {
            $corner_seq = "2";
            // type
            if($type == "HOME" || $type == "Home" || $type == "Main") {
                $type_seq = "1";
            } else if($type == "MeetUp(View)") {
                $type_seq = "2";
            } else if($type == "MotorCycle(View,Brand)") {
                $type_seq = "3";
            } else if($type == "Car(View,Brand)") {
                $type_seq = "4";
            } else if($type == "Free Product") {
                $type_seq = "5";
            } else if($type == "Video(View)") {
                $type_seq = "6";
            } else if($type == "MeetUp(Register)") {
                $type_seq = "7";
            } else if($type == "MotorCycle(Register)") {
                $type_seq = "8";
            } else if($type == "Cars(Register)") {
                $type_seq = "9";
            } else if($type == "Free Product(Register)") {
                $type_seq = "10";
            } else if($type == "My Likes") {
                $type_seq = "11";
            } else if($type == "Product Share") {
                $type_seq = "12";
            } else if($type == "Seller Shop") {
                $type_seq = "13";
            } else if($type == "Seller Shop Share") {
                $type_seq = "14";
            } else if($type == "Product Discovery (Category)" || $type == "Discovery by Category") {
                $type_seq = "15";
            } else if($type == "Product Discovery(Search by keyword)") {
                $type_seq = "16";
            } else if($type == "Product Discovery (Filter)") {
                $type_seq = "17";
            } else if($type == "Product Discovery (Sort)") {
                $type_seq = "18";
            }
        } else if($corner == "Community") {
            $corner_seq = "3";
            // type
            if($type == "HOME" || $type == "Home" || $type == "Main") {
                $type_seq = "1";
            } else if($type == "Category Detail") {
                $type_seq = "2";
            } else if($type == "Community(View)") {
                $type_seq = "3";
            } else if($type == "Video(View)") {
                $type_seq = "4";
            } else if($type == "Community(Register)") {
                $type_seq = "5";
            } else if($type == "Post Share") {
                $type_seq = "6";
            } else if($type == "Search Result Community (Search by keyword)") {
                $type_seq = "7";
            }
        } else if($corner == "My") {
            $corner_seq = "4";
            // type
            if($type == "HOME" || $type == "Home" || $type == "Main") {
                $type_seq = "1";
            } else if($type == "Today's mission" && $category == "Main") {
                $type_seq = "2";
            } else if($type == "Today's mission" && $category == "Check-In") {
                $type_seq = "3";
            } else if($type == "Today's mission" && $category == "Invite") {
                $type_seq = "4";
            } else if($type == "Today's mission" && $category == "Guide") {
                $type_seq = "5";
            } else if($type == "Address" && $category == "Main") {
                $type_seq = "6";
            } else if($type == "Address" && $category == "Address Add") {
                $type_seq = "7";
            } else if($type == "Product Keyword") {
                $type_seq = "8";
            } else if($type == "My Likes") {
                $type_seq = "9";
            } else if($type == "Wishlist") {
                $type_seq = "10";
            } else if($type == "Sales") {
                $type_seq = "11";
            } else if($type == "Purchases") {
                $type_seq = "12";
            } else if($type == "Follower&Following") {
                $type_seq = "13";
            }
        } else if($corner == "chat") {
            $corner_seq = "5";
            // type
            if($type == "Main") {
                $type_seq = "1";
            } else if($type == "buyandsell") {
                $type_seq = "2";
            } else if($type == "community") {
                $type_seq = "3";
            } else if($type == "private") {
                $type_seq = "4";
            } else if($type == "barter") {
                $type_seq = "5";
            } else if($type == "offer") {
                $type_seq = "6";
            }
        } else if($corner == "Wallet") {
            $corner_seq = "6";
            // type
            if($type == "Main") {
                $type_seq = "1";
            }
        } else if($corner == "GNB") {
            $corner_seq = "7";
            // type
            if($type == "Home Category") {
                $type_seq = "1";
            } else if($type == "Category Product") {
                $type_seq = "2";
            } else if($type == "Category Community") {
                $type_seq = "3";
            } else if($type == "Notification (Bell)") {
                $type_seq = "4";
            }
        } else if($corner == "mainBanner") {
            $corner_seq = "8";
            // type
            // if($corner == "mainBanner" && $type == "Category Product") {
            //     $type_seq = "1";
            // }
        } else if($corner == "SideMenuBar") {
            $corner_seq = "9";
            // type
            if($type == "Main") {
                $type_seq = "1";
            } else if($type == "Edit Profile") {
                $type_seq = "2";
            } else if($type == "Find a Mosque") {
                $type_seq = "3";
            } else if($type == "Notification") {
                $type_seq = "4";
            } else if($type == "Term&Conditions" || $type == "Terms&Conditions") {
                $type_seq = "5";
            } else if($type == "FAQs") {
                $type_seq = "6";
            } else if($type == "Setting Language" || $type == "Setting Languange") {
                $type_seq = "7";
            } else if($type == "Setting Delete Account") {
                $type_seq = "8";
            } else if($type == "Setting List of Accounts you Blocked" || $type == "Setting List of Accounts you blocked" || $type == "Setting List of Accouns you Blocked") {
                $type_seq = "9";
            } else if($type == "Setting List of Posts you Hide" || $type == "Setting List of Posts you hide") {
                $type_seq = "10";
            } else if($type == "Setting List of Products you Blocked" || $type == "Setting List of products you blocked") {
                $type_seq = "11";
            } else if($type == "Reinstall Sellon") {
                $type_seq = "12";
            } else if($type == "LOG OUT") {
                $type_seq = "13";
            }
        } else if($corner == "Club") {
            $corner_seq = "10";
            if($type == "Home" || $type == "Main") {
                $type_seq = "1";
            } else if($type == "Club Category") {
                $type_seq = "2";
            }
        }

        $datenow = date("Y-m-d");

        // get last id
        // $last_id = $this->gsam->getLatestRecord($nation_code, $datenow, $corner, $type, $category, $detail);

        // check if data already generate or not
        $check = $this->gsam->checkData($nation_code, $datenow, $corner, $type, $category, $detail, $sub_detail);
        if($check === '0' || $check === 0 ) {
            $endDoWhile = 0;
            do{
                $last_id = $this->GUIDv4();
                $checkId = $this->gsam->checkId($nation_code, $last_id);
                if($checkId == 0){
                    $endDoWhile = 1;
                }
            }while($endDoWhile == 0);

            $di = array();
            $di['nation_code'] = $nation_code;
            $di['id'] = $last_id;
            $di['cdate'] = $datenow;
            $di['corner'] = $corner;
            $di['corner_seq'] = $corner_seq;
            $di['type'] = $type;
            $di['type_seq'] = $type_seq;
            $di['category'] = $category;
            $di['detail'] = $detail;
            $di['sub_detail'] = $sub_detail;
            $di['count'] = '1';
            $res = $this->gsam->set($di);
            if($res) {
                $this->status = "200";
                $this->message = "data successfully created";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_analytics");
            } else {
                $this->status = "400";
                $this->message = "failed to create data";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_analytics");
                die();
            }
        } else {
            // $du = array();
            // $du['corner_seq'] = $corner_seq;
            // $du['type_seq'] = $type_seq;
            // $res = $this->gsam->update($nation_code, $corner, $type, $du);
            $res = $this->gsam->updateTotalData($nation_code, $datenow, "count", $corner, $type, $category, $detail, $sub_detail, "+", "1");
            if($res) {
                $this->status = "200";
                $this->message = "data successfully updated";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_analytics");
            } else {
                $this->status = "400";
                $this->message = "failed to update data";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_analytics");
                die();
            }
        }
    }

    public function index() {

    }
}    