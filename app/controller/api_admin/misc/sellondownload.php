<?php
class Sellondownload extends JI_Controller {
    public $is_log = 1;

    public function __construct() {
        parent::__construct();
        $this->lib("seme_log");
        $this->lib("phpqrcode/phpqrcode", 'QRcode', "inc");
        $this->load("api_admin/j_sellondownload_model", 'js_model');
        $this->load("api_admin/j_sellondownload_qrcode_model", 'jsq_model');
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

    public function generateQRCode($place_name)
    {
        // credit : https://phpqrcode.sourceforge.net/#demo
        // function goes to phpqrcode.php -> Class QRimage - > function png

        if($this->site_environment != "production") {
            $url = 'https://dev.sellon.net/';
        } else {
            $url = 'https://sellon.net/';
        }

        $content = $url . "download/?place_name=" . $place_name;
        $targetdir = $this->media_sellon_download_qrcode;
        $targetdircheck = realpath(SENEROOT.$targetdir);
        if (empty($targetdircheck)) {
            if (PHP_OS == "WINNT") {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir);
                }
            } else {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir, 0775);
                }
            }
        }

        $tahun = date("Y");
        $targetdir = $targetdir.DIRECTORY_SEPARATOR.$tahun;
        $targetdircheck = realpath(SENEROOT.$targetdir);
        if (empty($targetdircheck)) {
            if (PHP_OS == "WINNT") {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir);
                }
            } else {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir, 0775);
                }
            }
        }

        $bulan = date("m");
        $targetdir = $targetdir.DIRECTORY_SEPARATOR.$bulan;
        $targetdircheck = realpath(SENEROOT.$targetdir);
        if (empty($targetdircheck)) {
            if (PHP_OS == "WINNT") {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir);
                }
            } else {
                if (!is_dir(SENEROOT.$targetdir)) {
                    mkdir(SENEROOT.$targetdir, 0775);
                }
            }
        }

        $filename = rand(0000, 9999).'-'.$place_name;
        $filename = $filename.".png";
        QRcode::png($content, $targetdir.$filename, QR_ECLEVEL_H, 12, 2); // creates and save file
        $qr = new stdClass();
        $qr->status = 200;
        $qr->message = 'success';
        $qr->qrcode_url = $targetdir.$filename;
        $qr->plain_url = $content;
        return $qr;
    }

    function allowUnderscoresOnly($input) {
        // Define a regular expression pattern to match text with underscores
        $pattern = '/[^a-zA-Z0-9_]/';

        // remove any characters that are not letters, numbers, or underscores
        $cleanInput = preg_replace($pattern, '', $input);

        return $cleanInput;
    }

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

		$from_date = $this->input->post("from_date");
        $to_date = $this->input->post("to_date");

        $sortCol = "date";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }

		// START by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date
        //validating date interval
        if (strlen($from_date)==10) {
            $from_date = date("Y-m-d", strtotime($from_date));
        } else {
            $from_date = "";
        }
        if (strlen($to_date)==10) {
            $to_date = date("Y-m-d", strtotime($to_date));
        } else {
            $to_date = "";
        }
        // END by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date

        switch ($iSortCol_0) {
            case 0:
                $sortCol = "no";
                break;
            case 1:
                $sortCol = "id";
                break;
            case 2:
                $sortCol = "place_name";
                break;
            case 3:
                $sortCol = "cdate";
                break;
            case 4:
                $sortCol = "total_link_clicked";
                break;
            case 5:
                $sortCol = "total_open_playstore";
                break;
            case 6:
                $sortCol = "total_open_appstore";
                break;
            default:
                $sortCol = "id";
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
        $dcount = $this->js_model->countAll($keyword, $from_date, $to_date);
        $ddata = $this->js_model->getAll($page, $pagesize, $sortCol, $sortDir, $keyword, $from_date, $to_date);

        $this->__jsonDataTable($ddata, $dcount);
    }

    public function create_qrcode() {
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}

        $pengguna = $d['sess']->admin;

		$di = $_POST;
		$this->jsq_model->trans_start();
        $endDoWhile = 0;
        do {
            $last_id = $this->GUIDv4();
            $checkId = $this->jsq_model->checkId($last_id);
            if($checkId == 0){
                $endDoWhile = 1;
            }
        } while ($endDoWhile == 0);

        $place_name = $di['place_name'];
        $place_name = str_replace(' ', '_', $place_name);
        $place_name = $this->allowUnderscoresOnly($place_name);

        $qrcode_url = $this->generateQRCode(strtolower($place_name));

		$di['id'] = $last_id;
        $di['place_name'] = strtolower($place_name);
        $di['url'] = $qrcode_url->qrcode_url;
        $di['plain_url'] = $qrcode_url->plain_url;
        $di['cdate'] = 'NOW()';
        $di['admin_name'] = $pengguna->nama;

		$res = $this->jsq_model->set($di);
		if($res) {
			$this->jsq_model->trans_commit();
			$this->status = 200;
			$this->message = 'Data successfully added';
		} else {
			$this->jsq_model->trans_rollback();
			$this->status = 900;
			$this->message = 'Can\'t add Data';
		}

        // get data
        $data['qrcode'] = $this->jsq_model->getById($last_id);

		$this->jsq_model->trans_end();
		$this->__json_out($data);
	}

    public function sellonqrcode() {
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

		$from_date = $this->input->post("from_date");
        $to_date = $this->input->post("to_date");

        $sortCol = "date";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }

		// START by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date
        //validating date interval
        if (strlen($from_date)==10) {
            $from_date = date("Y-m-d", strtotime($from_date));
        } else {
            $from_date = "";
        }
        if (strlen($to_date)==10) {
            $to_date = date("Y-m-d", strtotime($to_date));
        } else {
            $to_date = "";
        }
        // END by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date

        switch ($iSortCol_0) {
            case 0:
                $sortCol = "no";
                break;
            case 1:
                $sortCol = "id";
                break;
            case 2:
                $sortCol = "place_name";
                break;
            case 3:
                $sortCol = "url";
                break;
            case 4:
                $sortCol = "cdate";
                break;
            case 5:
                $sortCol = "admin_name";
                break;
            default:
                $sortCol = "id";
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
        $dcount = $this->jsq_model->countAll($keyword, $from_date, $to_date);
        $ddata = $this->jsq_model->getAll($page, $pagesize, $sortCol, $sortDir, $keyword, $from_date, $to_date);

        foreach($ddata as &$gd){ 
            if(isset($gd->url)) {
                $gd->url = '<img src="'.$this->cdn_url($gd->url).'" class="img-responsive" style="max-width: 128px;"  onerror="this.onerror=null;this.src=\''.$this->cdn_url('media/produk/default.png').'\';"/>';
            }
        }

        $this->__jsonDataTable($ddata, $dcount);
    }

    public function delete($id) {
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}

        // get by id
        $getData = $this->jsq_model->getById($id);

		$res = $this->jsq_model->del($id);
		if($res){
			$this->status = 200;
			$this->message = 'Success';

            if (file_exists(SENEROOT.'/'.$getData->url)) {
                unlink(SENEROOT.'/'.$getData->url);
            }
		}else{
			$this->status = 902;
			$this->message = 'Failed while deleting data from database';
		}
		$this->__json_out($data);
	}
}