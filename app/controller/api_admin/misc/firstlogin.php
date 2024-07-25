<?php
class Firstlogin extends JI_Controller
{
    public $is_log = 1;

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_admin/a_firstlogin_model", 'afm');
    }

    private function __uploadFoto($temp, $id="")
    {
        //building path target
        $fldr = $this->media_firstlogin;
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

            // by Muhammad Sofi 23 January 2022 21:58 | comment code sanitize file input
            // // Sanitize input
            // if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
            //     header("HTTP/1.0 500 Invalid file name.");
            //     return 0;
            // }
            if (mime_content_type($temp['tmp_name']) == 'image/webp') {
                header("HTTP/1.0 500 Unsupported file format");
                return 0;
            }
            // Verify extension
            $ext = strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) {
                header("HTTP/1.0 500 Invalid extension.");
                return 0;
            }
            if ($ext == 'jpeg') {
                $ext = "jpg";
            }

            // Create magento style media directory
            $temp['name'] = 'firstlogin-'.rand(10,1000).'.'.$ext;

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

            if (file_exists($filetowrite) && is_file($filetowrite)) {
                unlink($filetowrite);
                $name = '';
                $rand = substr(md5(microtime()), rand(0, 26), 5);
                $name = 'promo-'.$rand.'.'.$ext;
                if ($id>0) {
                    $name = $id.'-'.$rand.'.'.$ext;
                }
                $filetowrite = $ifol.$name;
                $filetowrite = str_replace('//', '/', $filetowrite);
                if (file_exists($filetowrite) && is_file($filetowrite)) {
                    unlink($filetowrite);
                }
            }
            move_uploaded_file($temp['tmp_name'], $filetowrite);
            if (file_exists($filetowrite)) {
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

    public function index()
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

        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $sSearch = $this->input->request("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->request("iDisplayLength");

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

        switch ($iSortCol_0) {
            case 0:
                $sortCol = "id";
                break;
            case 1:
                $sortCol = "priority";
                break;
            case 2:
                $sortCol = "url";
                break;
            case 3:
                $sortCol = "cdate";
                break;
            case 4:
                $sortCol = "is_active";
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

        //advanced filter
        $is_active = $this->input->post("is_active");

        $this->status = 200;
        $this->message = 'Success';
        $dcount = $this->afm->countAll($nation_code, $keyword, $is_active);
        $ddata = $this->afm->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $is_active);

        foreach ($ddata as &$gd) {
            if (isset($gd->url)) {
                if (strlen($gd->url)>4) {
                    $gd->url = '<img src="'.base_url($gd->url).'?num='.rand(10,100).'" class="img-responsive" style="width:100px;" />';
                } else {
                    $gd->url = '<img src="'.base_url('media/brand/default.png').'" class="img-responsive" />';
                }
            }

            if(isset($gd->cdate)) {
                $gd->cdate = date("d F Y H:i:s", strtotime($gd->cdate));
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
                
        //files upload
        $fi = $_FILES;
        $dataImg = $this->media_firstlogin.'default.png';
        if ($fi["url"]["size"]>5120000) {
            $this->status = 1020;
            $this->message = 'Image file size too big, please try another image size';
            $this->seme_log->write("api_admin", "api_admin/misc/firstlogin::tambah() -> ".$this->message.". url_size ".$fi["url"]["size"]."bytes");
            $this->__json_out($data);
            die();
        } elseif ($fi["url"]["size"] > 0 && $fi["url"]["size"] <= 5120000) {
            $this->seme_log->write("api_admin", "api_admin/misc/firstlogin::tambah() -> url_size ".$fi["url"]["size"]."bytes");
            $ext = strtolower(pathinfo($fi["url"]['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) {
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
        if ($di) {
            $di["url"] = $dataImg;
            $this->afm->trans_start();
            $di['nation_code'] = $nation_code;
            $di['id'] = $this->afm->getLastId($nation_code);
            $di['cdate'] = "NOW()";
            $res = $this->afm->set($di);
            if ($res) {
                $this->afm->trans_commit();
                $this->status = 200;
                $this->message = 'Success';
            } else {
                $this->afm->trans_rollback();
                $this->status = 900;
                $this->message = 'Cant add promotion data to database, please try again later';
                if (file_exists(realpath($dataImg)) && is_file(realpath($dataImg))) {
                    unlink(realpath($dataImg));
                }
            }
            $this->afm->trans_end();
        } else {
            $this->status = 109;
            $this->message = 'Title is required, please check again';
        }

        $this->__json_out($data);
    }
    
    public function detail($id)
    {
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

        $login_image = $this->afm->getById($nation_code, $id);
        if (!isset($login_image->id)) {
            $this->status = 455;
            $this->message = 'Data Campaign not found or Invalid ID';
            $this->__json_out($data);
        }

        $this->status = 200;
        $this->message = 'Success';
        $data = $this->afm->getById($nation_code, $id);
        $this->__json_out($data);
    }

    public function edit($id)
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

        $du = $_POST;
        $fi = $_FILES;
        if (isset($du['id'])) {
            unset($du['id']);
        }

        //last validation
        if (!empty($id)) {
            $dataImg = false;
            if ($fi["url"]["size"]>5120000) { // by Muhammad Sofi 13 January 2022 16:11 | remodel on sponsored menu
                $this->seme_log->write("api_admin", "api_admin/misc/firstlogin::edit() -> url_size ".$fi["url"]["size"]."bytes");
                $this->status = 1020;
                $this->message = 'Image file size too big, please try another image size';
                $this->__json_out($data);
                die();
            } elseif ($fi["url"]["size"] > 0 && $fi["url"]["size"] <= 5120000) {
                $this->seme_log->write("api_admin", "api_admin/misc/firstlogin::edit() -> url_size ".$fi["url"]["size"]."bytes");
                $ext = strtolower(pathinfo($fi["url"]['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) {
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
                $resGet = $this->afm->getById($nation_code, $id);
                if (strlen($resGet->url)>4) {
                    if (file_exists(realpath($resGet->url)) && is_file(realpath($resGet->url))) {
                        unlink(realpath($resGet->url));
                    }
                }
            }
            $res = $this->afm->update($nation_code, $id, $du);
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
        } else {
            $this->status = 444;
            $this->message = 'One or more parameter required';
        }
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

        $login_image = $this->afm->getById($nation_code, $id);
        if (isset($login_image->id)) {
            $res = $this->afm->del($nation_code, $id);
            if ($res) {
                if (strlen($login_image->url)>4 && $login_image->url != 'media/campaign/default.png') {
                    if (file_exists(SENEROOT.$login_image->url) && is_file(SENEROOT.$login_image->url)) {
                        unlink(SENEROOT.$login_image->url);
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