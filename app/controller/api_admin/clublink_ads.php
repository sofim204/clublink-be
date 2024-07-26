<?php
class Clublink_Ads extends JI_Controller
{
    public $is_log = 1;

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->lib("seme_purifier");
        $this->load("api_admin/c_clublink_ads_model", 'ccam');
    }

    private function __uploadFoto($temp, $id="")
    {
        $sc = new stdClass();
        //building path target
        $fldr = $this->media_event_banner;
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
            // Sanitize input
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
            // by Muhammad Sofi 5 January 2022 20:22 | can input file image/video, and add validation when upload file
            if (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) {
                header("HTTP/1.0 500 Invalid extension.");
                return 0;
            }
            if ($ext == 'jpeg') {
                $ext = "jpg";
            }

            // Create magento style media directory
            // $temp['name'] = md5(rand()).date('is');

            // $name  = $temp['name'];
            $id = (int) $id;
            // if ($id>0) {
            $name = md5(rand()).date('is');
            $namethumb = $name.'-thumb.'.$ext;
            $name = $name.'.'.$ext;
            // }
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
                // by Muhammad Sofi 5 January 2022 20:22 | can input file image/video, and add validation when upload file
                $filetowritethumb = $ifol . $namethumb;
                $filetowritethumb = str_replace('//', '/', $filetowritethumb);
                // $this->lib("wideimage/WideImage", 'wideimage', "inc");
                // WideImage::load($filetowrite)->resize(800, 320, 'fill')->saveToFile($filetowritethumb);
                copy($filetowrite, $filetowritethumb);
                $sc->status = 200;
                $sc->message = 'Successful';
                $sc->image = $fldr."/".$name1."/".$name2."/".$name;
                $sc->thumb = $fldr."/".$name1."/".$name2."/".$namethumb;
            } else {
                $sc->status = 200;
                $sc->message = 'Successful';
                $sc->image = "";
                $sc->thumb = $fldr."/".$name1."/".$name2."/".$namethumb;
            }
        } else {
            // Notify editor that the upload failed
            //header("HTTP/1.0 500 Server Error");
            return 0;
        }
        return $sc;
    }

    private function __uploadVideo($temp, $id="")
    {
        //building path target
        $fldr = $this->media_event_banner;
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
            // Sanitize input
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
            // by Muhammad Sofi 5 January 2022 20:22 | can input file image/video, and add validation when upload file
            if (!in_array($ext, array("mp4", "mkv", "mov", "3gp"))) {
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

            // if (file_exists($filetowrite) && is_file($filetowrite)) {
            //     unlink($filetowrite);
            //     $name = '';
            //     $rand = substr(md5(microtime()), rand(0, 26), 5);
            //     $name = 'promo-'.$rand.'.'.$ext;
            //     if ($id>0) {
            //         $name = $id.'-'.$rand.'.'.$ext;
            //     }
            //     $filetowrite = $ifol.$name;
            //     $filetowrite = str_replace('//', '/', $filetowrite);
            //     if (file_exists($filetowrite) && is_file($filetowrite)) {
            //         unlink($filetowrite);
            //     }
            // }
            move_uploaded_file($temp['tmp_name'], $filetowrite);
            if (file_exists($filetowrite)) {
                // by Muhammad Sofi 5 January 2022 20:22 | can input file image/video, and add validation when upload file
                // $this->lib("wideimage/WideImage", 'wideimage', "inc");
                // WideImage::load($filetowrite)->resize(800, 320, 'fill')->saveToFile($filetowrite);
                // ;
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

    private function __uploadFotoThumb($temp)
    {
        $data = $temp;

        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        //building path target
        $fldr = $this->media_event_banner;
        $folder = SENEROOT.DIRECTORY_SEPARATOR.$fldr.DIRECTORY_SEPARATOR;
        $folder = str_replace('\\', '/', $folder);
        $folder = str_replace('//', '/', $folder);
        $ifol = realpath($folder);

        //check folder
        if (!$ifol) {
            mkdir($folder);
        } //create folder
        $ifol = realpath($folder); //get current realpath

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

        // put file to destination path
        $realfolder = $ifol;
        $newName = md5(rand()).date('is');
        // file_put_contents($realfolder.$newName.'-thumb.png', $data);
        file_put_contents($realfolder.$newName.'-thumb.png', $data);

        // put filename to db
        $filenameindb = $fldr."/".$name1."/".$name2."/".$newName.'-thumb.png';
        return $di['img_thumbnail'] = $filenameindb;
    }

    private function __convertToEmoji($text){
		$value = $text;
		$readTextWithEmoji = preg_replace("/\\\\u([0-9A-F]{2,5})/i", "&#x$1;", $value);
		return $readTextWithEmoji;
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


        $sortCol = "cdate";
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
                $sortCol = "priority";
                break;
            case 3:
                $sortCol = "img_thumbnail";
                break;
            case 4:
                $sortCol = "start_date";
                break;
            case 5:
                $sortCol = "end_date";
                break;
            case 6:
                $sortCol = "is_active";
                break;
            case 7:
                $sortCol = "type_ads";
                break;
            default:
                $sortCol = "cdate";
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
        if ($is_active == "1") {
        } elseif ($is_active == "0") {
        } else {
            $is_active = "";
        }

        $this->status = 200;
        $this->message = 'Success';
        $dcount = $this->ccam->countAll($nation_code, $keyword, $is_active);
        $ddata = $this->ccam->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $is_active);

        foreach ($ddata as &$gd) {
            if (isset($gd->judul)) {
                $gd->judul = $this->__convertToEmoji($gd->judul);
            }

            if (isset($gd->img_thumbnail)) {
                if (strlen($gd->img_thumbnail)>4) {
                    // add query to prevent cached image
                    $gd->img_thumbnail = '<img src="'.base_url($gd->img_thumbnail).'?num='.rand(10,100).'" class="img-responsive" style="width:150px;" />';
                } else {
                    $gd->img_thumbnail = '<img src="'.base_url('media/event_banner/default_video.png').'" class="img-responsive" />';
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

        $path = $fi['url']['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        $dataImg = $this->media_event_banner.'default.png';
        if ($fi["url"]["size"]>5120000) {
            $this->status = 1020;
            $this->message = 'Image file size too big, please try another image size';
            $this->__json_out($data);
            die();
        } else if ($fi["url"]["size"] > 0 && $fi["url"]["size"] <= 5120000) {
            $ext = strtolower(pathinfo($fi["url"]['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, array("jpg", "png", "jpeg", "gif"))) {
                $this->status = 1026;
                $this->message = 'Invalid image file extension, only allowed JPG, PNG file format';
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

            if($dataImg->status == 200){
                $di["url"] = $dataImg->image;
                $di["url_type"] = "image";
                $di["img_thumbnail"] = $dataImg->thumb;
            }
        }

        $di["cdate"] = 'NOW()';

        $this->ccam->trans_start();

        $di['nation_code'] = $nation_code;
        $di['id'] = $this->ccam->getLastId($nation_code);
        $res = $this->ccam->set($di);
        if ($res) {
            $this->ccam->trans_commit();
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->ccam->trans_rollback();
            $this->status = 900;
            $this->message = 'Cant add data to database, please try again later';
            if (file_exists(realpath($dataImg)) && is_file(realpath($dataImg))) {
                unlink(realpath($dataImg));
            }
        }
        $this->ccam->trans_end();

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

        $detail = $this->ccam->getById($nation_code, $id);
        if (!isset($detail->id)) {
            $this->status = 455;
            $this->message = 'Data Event_Banner not found or Invalid ID';
            $this->__json_out($data);
        }

        $this->status = 200;
        $this->message = 'Success';
        $data = $this->ccam->getById($nation_code, $id);
        if(isset($data->judul)){
			$data->judul = html_entity_decode($this->__convertToEmoji($data->judul), ENT_QUOTES);
		}
        $this->__json_out($data);
    }

    // by Muhammad Sofi 23 January 2022 23:00 | improvemnt and bug fixing on edit data event banner
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

        // removes html
        // by Muhammad Sofi 3 January 2022 18:23 | add title for event banner
        if (isset($du['judul'])) {
            $du['judul'] = strip_tags($du['judul']);
        }
        // by Muhammad Sofi 3 January 2022 17:18 | add description for event banner
        // if (isset($du['teks'])) {
        //     $du['teks'] = strip_tags($du['teks']);
        // }

        //last validation
        // by Muhammad Sofi 31 January 2022 14:14 | fix bug on edit data, image thumbnail is missing
        // if (!empty($id) && strlen($du['judul'])>0) {
        if (!empty($id)) {

            $path = $fi['url']['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            //get last id
            // $checkdata = $this->ccam->getByIdThumbnail($nation_code, $id);
            // by Muhammad Sofi 31 January 2022 14:14 | fix bug on edit data, image thumbnail is missing
            $checkdata = $this->ccam->getById($nation_code, $id);

            // if($ext == "png" || $ext == "jpg") {
            if($checkdata->url_type == "image") {
                if(!isset($checkdata->id)){
                    $this->status = 520;
                    $this->message = 'Invalid ID';
                    $this->__json_out($data);
                    die();
                }

                $fldr = $this->media_event_banner;
                $folder = SENEROOT.DIRECTORY_SEPARATOR.$fldr.DIRECTORY_SEPARATOR;
                $folder = str_replace('\\', '/', $folder);
                $folder = str_replace('//', '/', $folder);
                $ifol = realpath($folder);

                $new_ext = strtolower(pathinfo($fi['url']['name'], PATHINFO_EXTENSION));
                
                $randfilename = md5(rand()).date('is');
                $newimgthumbname = $randfilename.'-thumb.'.$new_ext;
                $newimgname = $randfilename.'.'.$new_ext;

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

                $dir_imgname = $ifol . $newimgname;
                $dir_imgname = str_replace('//', '/', $dir_imgname);
                $fileimgname = $dir_imgname;

                $mv = move_uploaded_file($fi["url"]["tmp_name"], $fileimgname);
                // if(file_exists($fileimgname)){
                if($mv){
                    // by Muhammad Sofi 31 January 2022 14:14 | fix bug on edit data, image thumbnail is missing
                    $img_url = $checkdata->url;
                    $img_thumbnail = $checkdata->img_thumbnail;
                    if(file_exists($img_url) && is_file($img_url) && file_exists($img_thumbnail) && is_file($img_thumbnail)) {
                        unlink(SENEROOT.$img_url);
                        unlink(SENEROOT.$img_thumbnail);
                    }
                    
                    $dir_imgthumbname = $ifol . $newimgthumbname;
                    $dir_imgthumbname = str_replace('//', '/', $dir_imgthumbname);
                    $fileimgthumbname = $dir_imgthumbname;

                    // $this->lib("wideimage/WideImage", 'wideimage', "inc");
                    // WideImage::load($fileimgname)->resize(800, 320, 'fill')->saveToFile($fileimgthumbname);
                    copy($fileimgname, $fileimgthumbname);

                    $writeimg = $fldr."/".$name1."/".$name2."/".$newimgname;
                    $writeimgthumb = $fldr."/".$name1."/".$name2."/".$newimgthumbname;
                    $du['url'] = $writeimg;
                    $du['img_thumbnail'] = $writeimgthumb;
                } else {
                    // by Muhammad Sofi 31 January 2022 14:14 | fix bug on edit data, image thumbnail is missing
                    $du['img_thumbnail'] = $checkdata->img_thumbnail;
                }
            }    
            // } else if($ext == "mp4" || $ext == "mkv"){
            if($checkdata->url_type == "video") {
                if(!isset($checkdata->id)){
                    $this->status = 520;
                    $this->message = 'Invalid ID';
                    $this->__json_out($data);
                    die();
                }

                //building path target
                $fldr = $this->media_event_banner;
                $folder = SENEROOT.DIRECTORY_SEPARATOR.$fldr.DIRECTORY_SEPARATOR;
                $folder = str_replace('\\', '/', $folder);
                $folder = str_replace('//', '/', $folder);
                $ifol = realpath($folder);

                $new_ext = strtolower(pathinfo($fi['url']['name'], PATHINFO_EXTENSION));

                $randfilename = md5(rand()).date('is');
                $newvidthumbname = $randfilename.'-thumb';
                $newvidname = $randfilename.'.'.$new_ext;

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

                $dir_vidname = $ifol . $newvidname;
                $dir_vidname = str_replace('//', '/', $dir_vidname);
                $filevidname = $dir_vidname;

                $mv = move_uploaded_file($fi["url"]["tmp_name"], $filevidname);

                if($mv){
                    // by Muhammad Sofi 31 January 2022 14:14 | fix bug on edit data, image thumbnail is missing
                    $vid_url = $checkdata->url;
                    $vid_thumbnail = $checkdata->img_thumbnail;
                    if(file_exists($vid_url) && is_file($vid_url) && file_exists($vid_thumbnail) && is_file($vid_thumbnail)) {
                        unlink(SENEROOT.$vid_url);
                        unlink(SENEROOT.$vid_thumbnail);
                    }

                    if (isset($du['img_thumbnail'])) {
                        $dataencode = $du['img_thumbnail'];
                    }

                    $database64 = $dataencode;

                    list($type, $database64) = explode(';', $database64);
                    list(, $database64)      = explode(',', $database64);
                    $database64 = base64_decode($database64);

                    $realfolder = $ifol;
                    file_put_contents($realfolder.$newvidthumbname.'.png', $database64);

                    $writevid = $fldr."/".$name1."/".$name2."/".$newvidname;
                    $writevidthumb = $fldr."/".$name1."/".$name2."/".$newvidthumbname.'.png';
                    
                    $du['url'] = $writevid;
                    $du['img_thumbnail'] = $writevidthumb;
                } else {
                    // by Muhammad Sofi 31 January 2022 14:14 | fix bug on edit data, image thumbnail is missing
                    $du['img_thumbnail'] = $checkdata->img_thumbnail;
                }
            }

            $res = $this->ccam->update($nation_code, $id, $du); // by Muhammad Sofi 25 January 2022 9:43 | fix undefined checkdata
            if ($res) {
                $this->status = 200;
                $this->message = 'Change data successfully';
            } else {
                $this->status = 901;
                $this->message = 'Failed to make data changes';
            }
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

        $dataurl = $this->ccam->getById($nation_code, $id);
        if (isset($dataurl->id)) {
            $res = $this->ccam->del($nation_code, $id);
            if ($res) {
                if (strlen($dataurl->url)>4 && strlen($dataurl->img_thumbnail)>4 && $dataurl->url != 'media/event_banner/default.png') {
                    if (file_exists(SENEROOT.$dataurl->url) && is_file(SENEROOT.$dataurl->url) && file_exists(SENEROOT.$dataurl->img_thumbnail) && is_file(SENEROOT.$dataurl->img_thumbnail)) {
                        unlink(SENEROOT.$dataurl->url);
                        unlink(SENEROOT.$dataurl->img_thumbnail);
                    } else {}
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

    // by Muhammad Sofi 20 January 2022 16:06 | bug fixing when change image thumbnail
    public function change_thumbnail($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();

		if($id<=0){
			$this->status = 400;
			$this->message = 'Invalid ID';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}

		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Access denied, please login';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		//admin
		$pengguna = $d['sess']->admin;

		//admin
		$this->status = 900;
		$this->message = 'Failed updating thumbnail to database';

		//get last id
	    $imgThumbnail = $this->ccam->getByIdThumbnail($pengguna->nation_code, $id);
		if(!isset($imgThumbnail->id)){
			$this->status = 520;
			$this->message = 'Invalid ID';
			$this->__json_out($data);
			die();
		}

		$fldr = $this->media_event_banner;
        $folder = SENEROOT.DIRECTORY_SEPARATOR.$fldr.DIRECTORY_SEPARATOR;
        $folder = str_replace('\\', '/', $folder);
        $folder = str_replace('//', '/', $folder);
        $ifol = realpath($folder);

        //check folder
        if (!$ifol) {
            mkdir($folder);
        } //create folder
        $ifol = realpath($folder); //get current realpath

        $ext = strtolower(pathinfo($_FILES['img_thumbnail']['name'], PATHINFO_EXTENSION));
        if ($ext == 'jpeg') {
            $ext = "jpg";
        }

        $originalfilename = $imgThumbnail->url;
        $getext = strtolower(pathinfo($originalfilename, PATHINFO_EXTENSION));
        $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $originalfilename);
        // $name = $filenameoriginal.'-thumb.'.$getext;
        $newname = $withoutExt."-thumb.".$ext;
        // $getfilename = basename($filedata, 0); // get file name & extension

        $getfilename = basename($originalfilename, $getext);
        $filenameoriginal = str_replace(".", "", $getfilename);
        
        $name = md5(rand()).date('is');
        // $name = $name.'-thumb.'.$ext;
        $name = $filenameoriginal.'-thumb.'.$ext;

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

        $target_dir = $ifol . $name;
        $target_dir = str_replace('//', '/', $target_dir);
		$filename = $target_dir;

        $img_thumbnail = $imgThumbnail->img_thumbnail;
        if(file_exists($img_thumbnail)) {
            unlink($img_thumbnail);
        }
        
		$mv = move_uploaded_file($_FILES["img_thumbnail"]["tmp_name"], $filename);
		if($mv){
			$du = array();
            $writethumb = $newname;
			$du['img_thumbnail'] = $writethumb;
			$this->ccam->update($pengguna->nation_code, $imgThumbnail->id, $du);
			$this->status = 200;
			$this->message = 'Success';
		}
		$this->__json_out($data);
	}

    public function getCustomer() {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;

        //standar input
        $search = $this->input->post("search");

        $ddata = $this->ccam->getCustomer($nation_code, $search, 1);

        $data = array();

        foreach ($ddata as $gd) {
            $data[] = array("id"=>$gd->user_id, "text"=>$gd->user_name." (".$gd->user_email.")");
        }
        echo json_encode($data);
    }

    public function getProductDetail() {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;

        //standar input
        $search = $this->input->post("search");
        $seller_id = $this->input->post("seller_id");

        $ddata = $this->ccam->getProductDetail($nation_code, $search, 1, $seller_id);

        $data = array();

        foreach ($ddata as $gd) {
            $data[] = array("id"=>$gd->product_id, "text"=>html_entity_decode($gd->product_name));
        }
        echo json_encode($data);
    }

    public function getCommunity() {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;

        //standar input
        $search = $this->input->post("search");

        $ddata = $this->ccam->getCommunity($nation_code, $search, 1);

        $data = array();

        foreach ($ddata as $gd) {
            $data[] = array("id"=>$gd->community_id, "text"=>$gd->title);
        }
        echo json_encode($data);
    }
}