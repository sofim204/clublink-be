<?php
class Sellon_Download extends JI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load("api_mobile/j_sellondownload_model", 'jsm');
        // $this->lib("phpqrcode/phpqrcode", 'QRcode', "inc");
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

    function allowUnderscoresOnly($input) {
        // Define a regular expression pattern to match text with underscores
        $pattern = '/[^a-zA-Z0-9_]/';

        // remove any characters that are not letters, numbers, or underscores
        $cleanInput = preg_replace($pattern, '', $input);

        return $cleanInput;
    }

    public function click_link() {
        //initial
        $dt = $this->__init();
        $data = array();

        // init
        $datenow = date("Y-m-d");
        $place_name = $this->input->get('place_name');
        $place_name = str_replace(' ', '_', $place_name);
        $place_name = $this->allowUnderscoresOnly($place_name);

        if(!isset($place_name) || $place_name === 0) {
            $place_name = 'user';
        } else if($place_name == '' || empty($place_name)) {
            $place_name = 'user';
        }

        // check if data already generate or not
        $check = $this->jsm->checkData($datenow, strtolower($place_name));

        if($check === '0' || $check === 0 ) {

            $endDoWhile = 0;
            do {

                $last_id = $this->GUIDv4();

                $checkId = $this->jsm->checkId($last_id);

                if($checkId == 0){
                    $endDoWhile = 1;
                }

            } while ($endDoWhile == 0);

            $di = array();
            $di['id'] = $last_id;
            $di['cdate'] = $datenow;
            $di['place_name'] = strtolower($place_name);
            $di['total_link_clicked'] = '1';
            $res = $this->jsm->set($di);
            if($res) {
                $this->status = "200";
                $this->message = "data successfully created";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_download");
            } else {
                $this->status = "920";
                $this->message = "failed to create data";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_download");
                die();
            }
        } else {
            $res = $this->jsm->updateTotalData($datenow, "total_link_clicked", strtolower($place_name), "+", "1");
            if($res) {
                $this->status = "200";
                $this->message = "data successfully updated";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_download");
            } else {
                $this->status = "920";
                $this->message = "failed to update data";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_download");
                // die();
            }
        }
    }

    public function click_app_or_play_store() {
        //initial
        $dt = $this->__init();
        $data = array();

        // init
        $datenow = date("Y-m-d");
        $open_store = $this->input->get('open_store');

        $place_name = $this->input->get('place_name');
        $place_name = str_replace(' ', '_', $place_name);
        $place_name = $this->allowUnderscoresOnly($place_name);

        if(!isset($place_name) || $place_name === 0) {
            $place_name = 'user';
        } else if($place_name == '' || empty($place_name)) {
            $place_name = 'user';
        }

        if(!isset($open_store) || $open_store === 0) {
            $field_count = '';
        } else {
            if($open_store == "play_store") {
                $field_count = 'total_open_playstore';
            } else if($open_store == "app_store") {
                $field_count = 'total_open_appstore';
            } else {
                $field_count = '';
            }
        }

        // check if data already generate or not
        $check = $this->jsm->checkData($datenow, strtolower($place_name));

        if($check === '0' || $check === 0 ) { 

        } else {
            $res = $this->jsm->updateTotalData($datenow, $field_count, strtolower($place_name), "+", "1");
            if($res) {
                $this->status = "200";
                $this->message = "data successfully updated";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_download");
            } else {
                $this->status = "920";
                $this->message = "failed to update data";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_download");
                // die();
            }
        }
    }

    // public function generateQRCode($place_name)
    // {
    //     // credit : https://phpqrcode.sourceforge.net/#demo
    //     // function goes to phpqrcode.php -> Class QRimage - > function png
    //     $content = "https://sellon.net/download/?place_name=" . $place_name;
    //     $targetdir = $this->media_sellon_download_qrcode;
    //     $targetdircheck = realpath(SENEROOT.$targetdir);
    //     if (empty($targetdircheck)) {
    //         if (PHP_OS == "WINNT") {
    //             if (!is_dir(SENEROOT.$targetdir)) {
    //                 mkdir(SENEROOT.$targetdir);
    //             }
    //         } else {
    //             if (!is_dir(SENEROOT.$targetdir)) {
    //                 mkdir(SENEROOT.$targetdir, 0775);
    //             }
    //         }
    //     }

    //     $tahun = date("Y");
    //     $targetdir = $targetdir.DIRECTORY_SEPARATOR.$tahun;
    //     $targetdircheck = realpath(SENEROOT.$targetdir);
    //     if (empty($targetdircheck)) {
    //         if (PHP_OS == "WINNT") {
    //             if (!is_dir(SENEROOT.$targetdir)) {
    //                 mkdir(SENEROOT.$targetdir);
    //             }
    //         } else {
    //             if (!is_dir(SENEROOT.$targetdir)) {
    //                 mkdir(SENEROOT.$targetdir, 0775);
    //             }
    //         }
    //     }

    //     $bulan = date("m");
    //     $targetdir = $targetdir.DIRECTORY_SEPARATOR.$bulan;
    //     $targetdircheck = realpath(SENEROOT.$targetdir);
    //     if (empty($targetdircheck)) {
    //         if (PHP_OS == "WINNT") {
    //             if (!is_dir(SENEROOT.$targetdir)) {
    //                 mkdir(SENEROOT.$targetdir);
    //             }
    //         } else {
    //             if (!is_dir(SENEROOT.$targetdir)) {
    //                 mkdir(SENEROOT.$targetdir, 0775);
    //             }
    //         }
    //     }

    //     $filename = rand(0000, 9999).'-'.$place_name;
    //     $filename = $filename.".png";
    //     QRcode::png($content, $targetdir.$filename, QR_ECLEVEL_H, 12, 2); // creates and save file
    //     $qr = new stdClass();
    //     // $qr->status = 200;
    //     // $qr->message = 'success';
    //     $qr->qrcode_url = $targetdir.$filename;
    //     return $qr;
    // }

    // public function generateQrCodeSellon() {
    //     //initial
    //     $dt = $this->__init();
    //     $data = array();

    //     // init
    //     $place_name = $this->input->get('place_name');
    //     $datenow = date("Y-m-d");

    //     if(!isset($place_name)) {
    //         $place_name = 'user';
    //     } else if($place_name == '' || empty($place_name)) {
    //         $place_name = 'user';
    //     }

    //     $data['qrcode'] = $this->generateQRCode($place_name);

    //     $this->status = "200";
    //     $this->message = "Success";
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_download");
    // }

    public function index()
    {}
}
