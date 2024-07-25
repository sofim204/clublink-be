<?php
class Campaign extends JI_Controller
{
    public $is_log = 1;

    public function __construct()
    {
        parent::__construct();
        //$this->setTheme('frontx');
        $this->lib("seme_log");
        // $this->load("api_admin/common_code_model", 'ccm');
        $this->load("api_admin/c_promo_model", 'cprom');
        // $this->load("api_admin/e_chat_model", 'ecm');
        // $this->load("api_admin/d_pemberitahuan_model", 'dpem');
    }

    private function __uploadFoto($temp, $id="")
    {
        //building path target
        $fldr = $this->media_campaign;
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
                // $this->lib("wideimage/WideImage", 'wideimage', "inc");
                // WideImage::load($filetowrite)->resize(800, 320, 'fill')->saveToFile($filetowrite);
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
                $sortCol = "judul";
                break;
            case 3:
                $sortCol = "gambar";
                break;
            case 4:
                $sortCol = "utype";
                break;
            case 5:
                $sortCol = "edate";
                break;
            case 6:
                $sortCol = "is_active";
                break;
            case 7:
                $sortCol = "top_bar";
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
        if ($is_active == "1") {
        } elseif ($is_active == "0") {
        } else {
            $is_active = "";
        }

        $topbar = $this->input->post("top_bar");
        if ($topbar == "1") {
        } elseif ($topbar == "0") {
        } else {
            $topbar = "";
        }

        $this->status = 200;
        $this->message = 'Success';
        $dcount = $this->cprom->countAll($nation_code, $keyword, $is_active);
        $ddata = $this->cprom->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $is_active);

        foreach ($ddata as &$gd) {
            if (isset($gd->judul)) {
                $gd->judul = $this->__convertToEmoji($gd->judul);
            }

            if (isset($gd->utype)) {
                $gd->utype = 'link '.$gd->utype;
            }

            if (isset($gd->gambar)) {
                if (strlen($gd->gambar)>4) {
                    $gd->gambar = '<img src="'.base_url($gd->gambar).'" class="img-responsive" style="width:240px;" />';
                } else {
                    $gd->gambar = '<img src="'.base_url('media/brand/default.png').'" class="img-responsive" />';
                }
            }

            if (isset($gd->gambar_sponsored)) {
                if (strlen($gd->gambar_sponsored)>4) {
                    $gd->gambar_sponsored = '<img src="'.base_url($gd->gambar_sponsored).'" class="img-responsive" style="width:240px;" />';
                } else {
                    $gd->gambar_sponsored = 'no picture';
                }
            }

            if (isset($gd->is_active)) {
                if (!empty($gd->is_active)) {
                    $gd->is_active = 'Active';
                } else {
                    $gd->is_active = 'Inactive';
                }
            }

            if (isset($gd->top_bar)) {
                if (!empty($gd->top_bar)) {
                    $gd->top_bar = 'Available';
                } else {
                    $gd->top_bar = 'Not Available';
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
        // foreach ($di as $key=>&$val) {
        //     if (is_string($val)) {
        //         if ($key == 'teks') {
        //             //$val = $this->seme_purifier->richtext($val);
        //             $val = $this->__f($val);
        //         } else {
        //             $val = $this->__f($val);
        //         }
        //     }
        // }
                
        // removes html
        if (isset($di['judul'])) {
            $di['judul'] = strip_tags($di['judul']);
        }
        // if (isset($di['teks'])) {
        //     $di['teks'] = strip_tags($di['teks']);
        // }
                
        //files upload
        $fi = $_FILES;
        $dataImg = $this->media_campaign.'default.png';
        if ($fi["gambar"]["size"]>5120000) {
            $this->status = 1020;
            $this->message = 'Image file size too big, please try another image size';
            $this->seme_log->write("api_admin", "api_admin/Campaign::tambah() -> ".$this->message.". gambar_size ".$fi["gambar"]["size"]."bytes");
            $this->__json_out($data);
            die();
        } elseif ($fi["gambar"]["size"] > 0 && $fi["gambar"]["size"] <= 5120000) {
            $this->seme_log->write("api_admin", "api_admin/Campaign::tambah() -> gambar_size ".$fi["gambar"]["size"]."bytes");
            $ext = strtolower(pathinfo($fi["gambar"]['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) {
                $this->status = 1026;
                $this->message = 'Invalid image file extension, only allowed JPG and PNG file format';
                $this->__json_out($data);
                die();
            }
            if (mime_content_type($fi["gambar"]["tmp_name"]) == 'image/webp') {
                $this->status = 1021;
                $this->message = 'WebP image file format currently unsupported on this system';
                $this->__json_out($data);
                die();
            }
            $dataImg = $this->__uploadFoto($fi);
        }
        if (!isset($di['judul'])) {
            $di['judul'] = "";
        }
        if (strlen($di['judul'])>0) {
            $di["gambar"] = $dataImg;
            $this->cprom->trans_start();
            $di['cdate'] = 'NOW()';
            $di['teks'] = '';  // by Muhammad Sofi 23 January 2022 21:38 | add empty string to teks
            $di['nation_code'] = $nation_code;
            $di['id'] = $this->cprom->getLastId($nation_code);
            $res = $this->cprom->set($di);
            if ($res) {
                $this->cprom->trans_commit();
                $this->status = 200;
                $this->message = 'Success';

                //by Donny Dennison - 2 july 2021 9:37
                //move-campaign-to-sponsored
                //START by Donny Dennison - 2 july 2021 9:37

                // //check is_active promotion and still active
                // //&& (strtotime($di['edate']) > strtotime("now"))
                // if (!empty($di['is_active'])) {
                //     //push notif array per device
                //     $ios = array();
                //     $android = array();

                //     //create push notif
                //     $classified = 'setting_notification_user';

                //     //by Donny Dennison - 19 october 2020 14:51
                //     //fix notif campaign still send when disable send notif
                //     // $code = 'U1';
                //     // $users = $this->ccm->getUsersByNationCode($nation_code, $classified, $code);
                //     $code = 'U3';
                //     $users = $this->ccm->getUsersByNationCodeAndSettingValueTrue($nation_code, $classified, $code);
                    
                //     if (count($users)>0) {
                //         foreach ($users as $user) {
                //             if (strtolower($user->device) == 'ios') {
                //                 $ios[] = $user->fcm_token;
                //             } else {
                //                 $android[] = $user->fcm_token;
                //             }

                //             //notification list for buyer
                //             $dpe = array();
                //             $dpe['nation_code'] = $nation_code;
                //             $dpe['b_user_id'] = $user->id;
                //             $dpe['id'] = $this->dpem->getLastId($nation_code, $user->id);
                //             $dpe['type'] = "promotion";

                //             //by Donny Dennison - 30 September 2020 17:12
                //             //bug fixing ' or " become emoji
                //             // $dpe['judul'] = strip_tags(html_entity_decode($di['judul']));
                //             // $dpe['teks'] = strip_tags(html_entity_decode($di['teks']));
                //             $dpe['judul'] = strip_tags(html_entity_decode($di['judul'],ENT_QUOTES));
                //             $dpe['teks'] = strip_tags(html_entity_decode($di['teks'],ENT_QUOTES));

                //             $dpe['cdate'] = "NOW()";
                //             $dpe['gambar'] = 'media/pemberitahuan/promotion.png';
                //             $extras = new stdClass();
                //             $extras->id_promo = $di['id'];

                //             //by Donny Dennison - 30 September 2020 17:12
                //             //bug fixing ' or " become emoji
                //             // $extras->judul = strip_tags(html_entity_decode($di['judul']));
                //             // $extras->teks = strip_tags(html_entity_decode($di['teks']));
                //             $extras->judul = strip_tags(html_entity_decode($di['judul'],ENT_QUOTES));
                //             $extras->teks = strip_tags(html_entity_decode($di['teks'],ENT_QUOTES));
                            
                //             $extras->top_bar = $di['top_bar'];
                //             $extras->due_date = $di['edate'];
                //             $dpe['extras'] = json_encode($extras);
                //             $this->dpem->set($dpe);
                //             $this->cprom->trans_commit();
                //         }
                //     } //end foreach

                //     $total = count($ios);
                //     if ($this->is_log) {
                //         $this->seme_log->write("api_admin", "API_Admin/Campaign::baru __pushNotifiOSCount: $total");
                //     }
                //     if (is_array($ios) && $total>0) {
                //         //push notif to ios
                //         $device = "ios"; //jenis device
                //         $tokens = $ios; //device token

                //         //by Donny Dennison - 30 September 2020 17:12
                //         //bug fixing ' or " become emoji
                //         // $title = strip_tags(html_entity_decode($di['judul']));
                //         // $message = strip_tags(html_entity_decode($di['teks']));
                //         $title = strip_tags(html_entity_decode($di['judul'],ENT_QUOTES));
                //         $message = strip_tags(html_entity_decode($di['teks'],ENT_QUOTES));

                //         $type = 'promotion';
                //         $image = 'media/pemberitahuan/promotion.png';
                //         $payload = new stdClass();
                //         $payload->id_promo = $di['id'];

                //         //by Donny Dennison - 30 September 2020 17:12
                //         //bug fixing ' or " become emoji
                //         // $payload->judul = strip_tags(html_entity_decode($di['judul']));
                //         $payload->judul = strip_tags(html_entity_decode($di['judul'],ENT_QUOTES));

                //         //by Donny Dennison
                //         //dicomment untuk handle message too big, response dari fcm
                //         // $payload->teks = strip_tags(html_entity_decode($di['teks']));
                //         $payload->teks = '';
                //         $payload->due_date = $di['edate'];
                //         $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                //         if ($this->is_log) {
                //             $this->seme_log->write("api_admin", 'API_Admin/Campaign::baru __pushNotifiOS: '.json_encode($res));
                //         }
                //     }

                //     //push notif to android
                //     $total = count($android);
                //     if ($this->is_log) {
                //         $this->seme_log->write("api_admin", "API_Admin/Campaign::baru __pushNotifAndroidCount: $total");
                //     }
                //     if (is_array($android) && $total>0) {
                //         $device = "android"; //jenis device
                //         $tokens = $android; //device token
                //         //by Donny Dennison - 30 September 2020 17:12
                //         //bug fixing ' or " become emoji
                //         // $title = strip_tags(html_entity_decode($di['judul']));
                //         // $message = strip_tags(html_entity_decode($di['teks']));
                //         $title = strip_tags(html_entity_decode($di['judul'],ENT_QUOTES));
                //         $message = strip_tags(html_entity_decode($di['teks'],ENT_QUOTES));

                //         $type = 'promotion';
                //         $image = 'media/pemberitahuan/promotion.png';
                //         $payload = new stdClass();
                //         $payload->id_promo = $di['id'];
                        
                //         //by Donny Dennison - 30 September 2020 17:12
                //         //bug fixing ' or " become emoji
                //         // $payload->judul = strip_tags(html_entity_decode($di['judul']));
                //         $payload->judul = strip_tags(html_entity_decode($di['judul'],ENT_QUOTES));

                //         //by Donny Dennison
                //         //dicomment untuk handle message too big, response dari fcm
                //         // $payload->teks = strip_tags(html_entity_decode($di['teks']));
                //         $payload->teks = '';
                //         $payload->due_date = $di['edate'];
                //         $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                //         if ($this->is_log) {
                //             $this->seme_log->write("api_admin", 'API_Admin/Campaign::baru __pushNotifAndroid: '.json_encode($res));
                //         }
                //     }
                // } else {
                //     $this->seme_log->write("api_admin", 'API_Admin/Campaign::baru() -> campaign not active or expired');
                // }

                //END by Donny Dennison - 2 july 2021 9:37

            } else {
                $this->cprom->trans_rollback();
                $this->status = 900;
                $this->message = 'Cant add promotion data to database, please try again later';
                if (file_exists(realpath($dataImg)) && is_file(realpath($dataImg))) {
                    unlink(realpath($dataImg));
                }
            }
            $this->cprom->trans_end();
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

        $promo = $this->cprom->getById($nation_code, $id);
        if (!isset($promo->id)) {
            $this->status = 455;
            $this->message = 'Data Campaign not found or Invalid ID';
            $this->__json_out($data);
        }

        $this->status = 200;
        $this->message = 'Success';
        $data = $this->cprom->getById($nation_code, $id);
        if(isset($data->judul)){
			$data->judul = html_entity_decode($this->__convertToEmoji($data->judul), ENT_QUOTES);
		}
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
        if (!isset($du['judul'])) {
            $du['judul'] = "";
        }
        // by Muhammad Sofi 23 January 2022 21:38 | add empty string to teks
        if (!isset($du['teks'])) {
            $du['teks'] = "";
        }
                
        // removes html
        if (isset($du['judul'])) {
            $du['judul'] = strip_tags($du['judul']);
        }

        // by Muhammad Sofi 23 January 2022 21:38 | add empty string to teks
        if (isset($du['teks'])) {
            $du['teks'] = strip_tags($du['teks']);
        }
        
        // By Donny Dennison - 19 Juni 2020 16:22
        // terdapat bug sewaktu edit konten / teks, image yang dimasukin tidak tersimpan ke database, dengan menghilangkan strip_tags yang menghilangkan tag html menjadi bisa menyimpan html 
        // if (isset($du['teks'])) {
            // $du['teks'] = strip_tags($du['teks']);
        // }

        //last validation
        if (!empty($id) && strlen($du['judul'])>0) {
            $dataImg = false;
            if ($fi["gambar"]["size"]>5120000) { // by Muhammad Sofi 13 January 2022 16:11 | remodel on sponsored menu
                $this->seme_log->write("api_admin", "api_admin/Campaign::edit() -> gambar_size ".$fi["gambar"]["size"]."bytes");
                $this->status = 1020;
                $this->message = 'Image file size too big, please try another image size';
                $this->__json_out($data);
                die();
            } elseif ($fi["gambar"]["size"] > 0 && $fi["gambar"]["size"] <= 5120000) {
                $this->seme_log->write("api_admin", "api_admin/Campaign::edit() -> gambar_size ".$fi["gambar"]["size"]."bytes");
                $ext = strtolower(pathinfo($fi["gambar"]['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) {
                    $this->status = 1026;
                    $this->message = 'Invalid image file extension, only allowed JPG and PNG file format';
                    $this->__json_out($data);
                    die();
                }
                if (mime_content_type($fi["gambar"]["tmp_name"]) == 'image/webp') {
                    $this->status = 1021;
                    $this->message = 'WebP image file format currently unsupporter on this system';
                    $this->__json_out($data);
                    die();
                }
                $dataImg = $this->__uploadFoto($fi);
            }
            if (!empty($dataImg)) {
                $du["gambar"] = $dataImg;
                $resGet = $this->cprom->getById($nation_code, $id);
                if (strlen($resGet->gambar)>4) {
                    if (file_exists(realpath($resGet->gambar)) && is_file(realpath($resGet->gambar))) {
                        unlink(realpath($resGet->gambar));
                    }
                }
            }
            $res = $this->cprom->update($nation_code, $id, $du);
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
            // if (file_exists(realpath($dataImg)) && is_file(realpath($dataImg))) {
            //     unlink(realpath($dataImg));
            // }
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

        $promo = $this->cprom->getById($nation_code, $id);
        if (isset($promo->id)) {
            $res = $this->cprom->del($nation_code, $id);
            if ($res) {
                if (strlen($promo->gambar)>4 && $promo->gambar != 'media/campaign/default.png') {
                    if (file_exists(SENEROOT.$promo->gambar) && is_file(SENEROOT.$promo->gambar)) {
                        unlink(SENEROOT.$promo->gambar);
                        if (file_exists(SENEROOT.$promo->gambar_sponsored) && is_file(SENEROOT.$promo->gambar_sponsored)) {
                            unlink(SENEROOT.$promo->gambar_sponsored);
                        }
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

    public function getCustomer() {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;

        //standar input
        $search = $this->input->post("search");

        $ddata = $this->cprom->getCustomer($nation_code, $search, 1);

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

        $ddata = $this->cprom->getProductDetail($nation_code, $search, 1, $seller_id);

        $data = array();

        foreach ($ddata as $gd) {
            $data[] = array("id"=>$gd->product_id, "text"=>html_entity_decode($gd->product_name));
        }
        echo json_encode($data);
    }

    public function addsponsoredpicture($id){
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
		$this->message = 'Failed add picture to database';

		//get last id
	    $imgThumbnail = $this->cprom->getByIdData($pengguna->nation_code, $id);
		if(!isset($imgThumbnail->id)){
			$this->status = 520;
			$this->message = 'Invalid ID';
			$this->__json_out($data);
			die();
		}

		$fldr = $this->media_campaign;
        $folder = SENEROOT.DIRECTORY_SEPARATOR.$fldr.DIRECTORY_SEPARATOR;
        $folder = str_replace('\\', '/', $folder);
        $folder = str_replace('//', '/', $folder);
        $ifol = realpath($folder);

        //check folder
        if (!$ifol) {
            mkdir($folder);
        } //create folder
        $ifol = realpath($folder); //get current realpath

        $ext = strtolower(pathinfo($_FILES['gambar_sponsored']['name'], PATHINFO_EXTENSION));
        if ($ext == 'jpeg') {
            $ext = "jpg";
        }

        $originalfilename = $imgThumbnail->gambar;
        $getext = strtolower(pathinfo($originalfilename, PATHINFO_EXTENSION));
        $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $originalfilename);
        // $name = $filenameoriginal.'-thumb.'.$getext;
        $newname = $withoutExt."-thumb.".$ext;

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

        $gambar_sponsored = $imgThumbnail->gambar_sponsored;
        if(file_exists($gambar_sponsored)) {
            unlink($gambar_sponsored);
        }
        
		$mv = move_uploaded_file($_FILES["gambar_sponsored"]["tmp_name"], $filename);
		if($mv){
			$du = array();
            $writethumb = $newname;
			$du['gambar_sponsored'] = $writethumb;
			$this->cprom->update($pengguna->nation_code, $imgThumbnail->id, $du);
			$this->status = 200;
			$this->message = 'Success';
		}
		$this->__json_out($data);
	}
}
