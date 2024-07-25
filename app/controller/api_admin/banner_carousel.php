<?php
class Banner_Carousel extends JI_Controller {
    public function __construct() {
        parent::__construct();
        $this->lib("seme_log");
        $this->load('api_admin/g_banner_carousel_model', 'banner_carousel_model');
    }

    private function __uploadFoto($temp, $id="") {
        //building path target
        $fldr = $this->media_banner_carousel;
        $folder = SENEROOT.DIRECTORY_SEPARATOR.$fldr.DIRECTORY_SEPARATOR;
        $folder = str_replace('\\', '/', $folder);
        $folder = str_replace('//', '/', $folder);
        $ifol = realpath($folder);

        //check folder
        if (!$ifol) {
            mkdir($folder);
        } //create folder
        $ifol = realpath($folder); //get current realpath

        reset($_FILES);
        $temp = current($temp);
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
            if (mime_content_type($temp['tmp_name']) == 'image/webp') {
                header("HTTP/1.0 500 Unsupported file format");
                return 0;
            }
            // Verify extension
            $ext = strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, array("jpg", "png","jpeg"))) {
                header("HTTP/1.0 500 Invalid extension.");
                return 0;
            }
            if ($ext == 'jpeg') {
                $ext = "jpg";
            }

            // Create magento style media directory
            $temp['name'] = md5(rand()).date('is').'.'.$ext;

            $name  = $temp['name'];
            $id = (int) $id;
            if ($id>0) {
                $name = $id.'.'.$ext;
            }
            $name1 = date("Y");
            $name2 = date("m");

            //building directory structure
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
                    mkdir($ifol, 0775);
                }
                $ifol = $ifol.DIRECTORY_SEPARATOR.$name1.DIRECTORY_SEPARATOR;
                if (!is_dir($ifol)) {
                    mkdir($ifol, 0775);
                }
                $ifol = $ifol.DIRECTORY_SEPARATOR.$name2.DIRECTORY_SEPARATOR;
                if (!is_dir($ifol)) {
                    mkdir($ifol, 0775);
                }
            }

            // Accept upload if there was no origin, or if it is an accepted origin
            $filetowrite = $ifol . $name;
            $filetowrite = str_replace('//', '/', $filetowrite);

            move_uploaded_file($temp['tmp_name'], $filetowrite);
            if (file_exists($filetowrite)) {
                $this->lib("wideimage/WideImage", 'wideimage', "inc");
                WideImage::load($filetowrite)->resize(800, 320, 'fill')->saveToFile($filetowrite);
                return $fldr."/".$name1."/".$name2."/".$name;
            } else {
                return 0;
            }
        } else {
            // Notify editor that the upload failed
            //header("HTTP/1.0 500 Server Error");
            return 0;
        }
    }

    public function index() {
        //initial
        $data = $this->__init();

        //default response
        $data = array();
        $data['banner_list'] = new stdClass();

        // //check nation_code
        // $nation_code = $this->input->get('nation_code');
        // $nation_code = $this->nation_check($nation_code);
        // if (empty($nation_code)) {
        //     $this->status = 101;
        //     $this->message = 'Missing or invalid nation_code';
        //     $this->__json_out($data);
        //     die();
        // }

        //by Donny Dennison - 06 september 2022 10:42
        //comment code check apikey in api_admin/banner_carousel (request from mobile dev, vicky)
        // //check api_key
        // $apikey = $this->input->get('apikey');
        // $c = $this->apikey_check($apikey);
        // if (empty($c)) {
        //     $this->status = 400;
        //     $this->message = 'Missing or invalid API key';
        //     $this->__json_out($data);
        //     die();
        // }

        $data['banner_list'] = $this->banner_carousel_model->getListBanner();

        foreach ($data['banner_list'] as &$list) {
            // by Muhammad Sofi 21 January 2022 16:32 | get base url
            $list->url = $this->cdn_url($list->url);
        }

        $this->status = 200;
        $this->message = "Berhasil";
        $this->__json_out($data);
    }

    public function databanner() {
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

        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $sSearch = $this->input->request("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->request("iDisplayLength");

        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");

        $status_banner = $this->input->post("is_active");

        //input validation
        $sortCol = $iSortCol_0;
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }

        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
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

        $is_active = "";
		switch ($status_banner) {
			case '1':
				$is_active=1;
				break;
			case '0':
				$is_active=0;
				break;
			default:
				$is_active = "";
				break;
		}

        $this->status = 200;
        $this->message = 'Success';
        $dcount = $this->banner_carousel_model->countAll($nation_code, $keyword, $is_active);
        $ddata = $this->banner_carousel_model->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $is_active);

        foreach ($ddata as &$gd) {
            if (isset($gd->url)) {
                if (strlen($gd->url)>4) {
                    $gd->url = '<img src="'.base_url($gd->url).'" class="img-responsive" style="width:240px;" />';
                } else {
                    $gd->url = '<img src="'.base_url('media/banner_carousel/default_image_banner.png').'" class="img-responsive" />';
                }
            }
            if (isset($gd->is_active)) {
                if (!empty($gd->is_active)) {
                    $gd->is_active = 'Active';
                } else {
                    $gd->is_active = 'Inactive';
                }
            }
        }
        //sleep(3);
        $another = array();
        $this->__jsonDataTable($ddata, $dcount);
    }

    public function add() {
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

        //files upload
        $fi = $_FILES;
        $dataImg = $this->media_banner_carousel.'default_image_banner.png';
        if ($fi["url"]["size"]>2048000) {
            $this->status = 1020;
            $this->message = 'Image file size too big, please try another image';
            $this->seme_log->write("api_admin", "api_admin/Campaign::tambah() -> ".$this->message.". url_size ".$fi["url"]["size"]."bytes");
            $this->__json_out($data);
            die();
        } else if ($fi["url"]["size"] > 0 && $fi["url"]["size"] <= 2048000) {
            $this->seme_log->write("api_admin", "api_admin/Campaign::tambah() -> url_size ".$fi["url"]["size"]."bytes");
            $ext = strtolower(pathinfo($fi["url"]['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, array("jpg", "jpeg", "png"))) {
                $this->status = 1026;
                $this->message = 'Invalid image file extension, only allowed JPG and PNG file format';
                $this->__json_out($data);
                die();
            }
            if (mime_content_type($fi["url"]["tmp_name"]) == 'image/webp') {
                $this->status = 1021;
                $this->message = 'WebP image file format currently unsupported on this system';
                $this->__json_out($data);
                die();
            }
            $dataImg = $this->__uploadFoto($fi);
        }

        $di["url"] = $dataImg;
        $this->banner_carousel_model->trans_start();
        $di['cdate'] = 'NOW()';
        $di['nation_code'] = $nation_code;
        $di['id'] = $this->banner_carousel_model->getLastId($nation_code);
        $res = $this->banner_carousel_model->set($di);
        if ($res) {
            $this->banner_carousel_model->trans_commit();
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->banner_carousel_model->trans_rollback();
            $this->status = 900;
            $this->message = 'Cant add data to database, please try again later';
            if (file_exists(realpath($dataImg)) && is_file(realpath($dataImg))) {
                unlink(realpath($dataImg));
            }
        }
        $this->banner_carousel_model->trans_end();
        $this->__json_out($data);
    }
    
    public function detail($id) {
        $id = (int) $id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access, please login';
            header("HTTP/1.0 400 Unauthorized access, please login");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $bannerdata = $this->banner_carousel_model->getById($nation_code, $id);
        if (!isset($bannerdata->id)) {
            $this->status = 455;
            $this->message = 'Data Campaign not found or Invalid ID';
            $this->__json_out($data);
        }

        $this->status = 200;
        $this->message = 'Success';
        $data = $this->banner_carousel_model->getById($nation_code, $id);
        $this->__json_out($data);
    }

    public function edit($id) {
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

        $du = $_POST;
        $fi = $_FILES;

        $dataImg = false;
        if ($fi["url"]["size"]>2048000) { // by Muhammad Sofi 13 January 2022 16:11 | remodel on sponsored menu
            $this->seme_log->write("api_admin", "api_admin/Campaign::edit() -> url_size ".$fi["url"]["size"]."bytes");
            $this->status = 1020;
            $this->message = 'Image file size too big, please try another image';
            $this->__json_out($data);
            die();
        } else if ($fi["url"]["size"] > 0 && $fi["url"]["size"] <= 2048000) {
            $this->seme_log->write("api_admin", "api_admin/Campaign::edit() -> url_size ".$fi["url"]["size"]."bytes");
            $ext = strtolower(pathinfo($fi["url"]['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, array("jpg", "jpeg", "png"))) {
                $this->status = 1026;
                $this->message = 'Invalid image file extension, only allowed JPG and PNG file format';
                $this->__json_out($data);
                die();
            }
            if (mime_content_type($fi["url"]["tmp_name"]) == 'image/webp') {
                $this->status = 1021;
                $this->message = 'WebP image file format currently unsupporter on this system';
                $this->__json_out($data);
                die();
            }
            $dataImg = $this->__uploadFoto($fi);
        }
        if (!empty($dataImg)) {
            $du["url"] = $dataImg;
            $resGet = $this->banner_carousel_model->getById($nation_code, $id);
            if (strlen($resGet->url)>4) {
                if (file_exists(realpath($resGet->url)) && is_file(realpath($resGet->url))) {
                    unlink(realpath($resGet->url));
                }
            }
        }
        $res = $this->banner_carousel_model->update($nation_code, $id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Perubahan berhasil diterapkan';
        } else {
            $this->status = 901;
            $this->message = 'Failed to make data changes';
            if (file_exists(realpath($dataImg)) && is_file(realpath($dataImg))) {
                unlink(realpath($dataImg));
            }
        }
        $this->__json_out($data);
    }
        
    public function delete($id) {
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

        $bannerdata = $this->banner_carousel_model->getById($nation_code, $id);
        if (isset($bannerdata->id)) {
            $res = $this->banner_carousel_model->del($nation_code, $id);
            if ($res) {
                if (strlen($bannerdata->url)>4 && $bannerdata->url != 'media/banner_carousel/default_image_banner.png') {
                    if (file_exists(SENEROOT.$bannerdata->url) && is_file(SENEROOT.$bannerdata->url)) {
                        unlink(SENEROOT.$bannerdata->url);
                    }
                }
                $this->status = 200;
                $this->message = 'Success';
            } else {
                $this->status = 902;
                $this->message = 'Failed removing data from database, please try again later';
            }
        } else {
            $this->status = 441;
            $this->message = 'Data with supplied ID not found or already deleted';
        }
        $this->__json_out($data);
    }
}
