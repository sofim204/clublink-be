<?php
class BulkSale extends JI_Controller
{
    public $email_send = 1;
    public $is_log = 1;

    public function __construct()
    {
        parent::__construct();
        //$this->setTheme('frontx');
        $this->lib('seme_email');
        $this->lib("seme_purifier");
        $this->lib("seme_log");
        $this->load("api_mobile/a_negara_model", "anm");
        $this->load("api_mobile/a_pengguna_model", "apm");
        $this->load("api_mobile/b_kategori_model3", "bkm3");
        $this->load("api_mobile/b_berat_model", "brt");
        $this->load("api_mobile/b_user_alamat_model", "bua");
        $this->load("api_mobile/c_bulksale_model", "cbsm");
        $this->load("api_mobile/c_bulksale_foto_model", "cbsfm");
        $this->load("api_mobile/b_user_model", "bu");
        $this->load("api_mobile/d_wishlist_model", "dwlm");
    }

    private function __uploadImagex($nation_code, $keyname, $bulksale_id="0", $ke="")
    {
        $sc = new stdClass();
        $sc->status = 500;
        $sc->message = 'Error';
        $sc->image = '';
        $sc->thumb = '';
        $bulksale_id = (int) $bulksale_id;
        if (isset($_FILES[$keyname]['name'])) {
            if ($_FILES[$keyname]['size']<=0) {
                $sc->status = 300;
                $sc->message = 'Empty file';
                return $sc;
            }
            if ($_FILES[$keyname]['size']>1000000) {
                $sc->status = 301;
                $sc->message = 'Image file Size too big, please try again';
                return $sc;
            }
            if ($_FILES[$keyname]['size']>0 && $_FILES[$keyname]['size']<=1000000) {
                if (mime_content_type($_FILES[$keyname]['tmp_name']) == 'image/webp') {
                    $sc->status = 302;
                    $sc->message = 'WebP image file format is not supported.';
                    return $sc;
                }
            }
            $filenames = pathinfo($_FILES[$keyname]['name']);
            $fileext = '';
            if (isset($filenames['extension'])) {
                $fileext = strtolower($filenames['extension']);
            }
            if (!in_array($fileext, array("jpg","png","jpeg"))) {
                $sc->status = 303;
                $sc->message = 'Invalid file extension, please try other file.';
                return $sc;
            }
            $filename = "$nation_code-$bulksale_id-$ke";
            $filethumb = $filename.'-thumb';

            $targetdir = $this->media_bulksale;
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

            $sc->status = 998;
            $sc->message = 'Invalid file extension uploaded';
            if (in_array($fileext, array("jpg", "png","jpeg"))) {
                $filecheck = SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename.'.'.$fileext;
                if (file_exists($filecheck)) {
                    $rand = rand(0, 999);
                    $filename = "$nation_code-$bulksale_id-$ke-".$rand;
                    $filecheck = SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename.'.'.$fileext;
                    if (file_exists($filecheck)) {
                        $rand = rand(1000, 99999);
                        $filename = "$nation_code-$bulksale_id-$ke-".$rand;
                    }
                };
                $filethumb = $filename."-thumb.".$fileext;
                $filename = $filename.".".$fileext;

                move_uploaded_file($_FILES[$keyname]['tmp_name'], SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
                $this->lib("wideimage/WideImage", 'wideimage', "inc");
                if (file_exists(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb)) {
                    unlink(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
                }
                WideImage::load(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)->reSize(370)->saveToFile(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);

                $sc->status = 200;
                $sc->message = 'Success';
                $sc->thumb = str_replace("//", "/", $targetdir.'/'.$filethumb);
                $sc->image = str_replace("//", "/", $targetdir.'/'.$filename);
            }
        } else {
            $sc->status = 988;
            $sc->message = 'Keyname file does not exists';
        }
        return $sc;
    }

    private function __sortCol($sort_col, $tbl_as)
    {
        switch ($sort_col) {
            case 'id':
            $sort_col = "$tbl_as.id";
            break;
            case 'action_status':
            $sort_col = "$tbl_as.action_status";
            break;
            case 'agent_name':
            $sort_col = "$tbl_as.agent_name";
            break;
            case 'cdate':
            $sort_col = "$tbl_as.cdate";
            break;
            case 'ldate':
            $sort_col = "$tbl_as.ldate";
            break;
            default:
            $sort_col = "$tbl_as.name";
        }
        return $sort_col;
    }
    private function __sortDir($sort_dir)
    {
        $sort_dir = strtolower($sort_dir);
        if ($sort_dir == "desc") {
            $sort_dir = "DESC";
        } else {
            $sort_dir = "ASC";
        }
        return $sort_dir;
    }
    private function __page($page)
    {
        if (!is_int($page)) {
            $page = (int) $page;
        }
        if (empty($page)) {
            $page = 1;
        }
        return $page;
    }
    private function __pageSize($page_size)
    {
        $page_size = (int) $page_size;
        if ($page_size<=0) {
            $page_size = 1;
        }
        return $page_size;
    }
    private function __pageSize2($page_size)
    {
        if (!is_int($page_size)) {
            $page_size = (int) $page_size;
        }
        switch ($page_size) {
            case 48:
            $page_size= 48;
            break;
            case 24:
            $page_size= 24;
            break;
            case 16:
            $page_size= 16;
            break;
            case 12:
            $page_size= 12;
            break;
            case 2:
            $page_size= 2;
            // no break
            default:
            $page_size = 12;
        }
        return $page_size;
    }
    public function index()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['bulksale_total'] = 0;
        $data['bulksale'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }
        $b_user_id = $pelanggan->id; //assign to current user

        //populate input get
        $sort_col = $this->input->get("sort_col");
        $sort_dir = $this->input->get("sort_dir");
        $page = $this->input->get("page");
        $page_size = $this->input->get("page_size");
        $grid = $this->input->get("grid");
        $keyword = $this->input->get("keyword");
        $action_status = strtolower($this->input->get("action_status"));
        if (empty($action_status)) {
            $action_status="";
        }

        switch ($action_status) {
            case 'pending':
                $action_status = 'pending';
                break;
            case 'visited':
                $action_status = 'visited';
                break;
            case 'completed':
                $action_status = 'completed';
                break;
            case 'leaved':
                $action_status = 'leaved';
                break;
            default:
                $action_status = '';
        }

        //sanitize input
        $tbl_as = $this->cbsm->getTblAs();
        $sort_col = $this->__sortCol($sort_col, $tbl_as);
        $sort_dir = $this->__sortDir($sort_dir);
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);
        if (empty($keyword)) {
            $keyword="";
        }
        $keyword = filter_var(strip_tags($keyword), FILTER_SANITIZE_SPECIAL_CHARS);
        $keyword = substr($keyword, 0, 32);


        //get produk data
        $ddcount = $this->cbsm->countAll($nation_code, $keyword, $b_user_id, $action_status);
        $ddata = $this->cbsm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword, $b_user_id, $action_status);

        //$this->debug($ddata);
        //die();

        //manipulating data
        foreach ($ddata as &$pd) {
            if (isset($pd->b_user_image_seller)) {
                if (empty($pd->b_user_image_seller)) {
                    $pd->b_user_image_seller = 'media/produk/default.png';
                }
                
                // by Muhammad Sofi - 28 October 2021 11:00
                // if user img & banner not exist or empty, change to default image
                // $pd->b_user_image_seller = $this->cdn_url($pd->b_user_image_seller);
                if(file_exists(SENEROOT.$pd->b_user_image_seller) && $pd->b_user_image_seller != 'media/user/default.png'){
                    $pd->b_user_image_seller = $this->cdn_url($pd->b_user_image_seller);
                } else {
                    $pd->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }
            if (isset($pd->thumb)) {
                if (empty($pd->thumb)) {
                    $pd->thumb = 'media/produk/default.png';
                }
                $pd->thumb = str_replace("//", "/", $pd->thumb);
                $pd->thumb = $this->cdn_url($pd->thumb);
            }
            if (isset($pd->foto)) {
                if (empty($pd->foto)) {
                    $pd->foto = 'media/produk/default.png';
                }
                $pd->foto = str_replace("//", "/", $pd->foto);
                $pd->foto = $this->cdn_url($pd->foto);
            }
        }

        //build result
        $data['bulksale_total'] = $ddcount;
        $data['bulksale'] = $ddata;

        //response
        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
    }

    public function detail($id)
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['bulksale'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }
        $b_user_id = $pelanggan->id; //assign to current user

        $id = (int) $id;
        if ($id<=0) {
            $this->status = 104;
            $this->message = 'Missing or invalid BulkSale ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        $bulksale = $this->cbsm->getById($nation_code, $id, $b_user_id);
        if (isset($bulksale->id)) {
            if ($bulksale->b_user_id_seller != $pelanggan->id) {
                $this->status = 105;
                $this->message = "You can't see other person's BulkSale";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
                die();
            }
            
            // by Muhammad Sofi - 28 October 2021 11:00
            // if user img & banner not exist or empty, change to default image
            // $bulksale->b_user_image_seller = $this->cdn_url($bulksale->b_user_image_seller);
            if(file_exists(SENEROOT.$bulksale->b_user_image_seller) && $bulksale->b_user_image_seller != 'media/user/default.png'){
                $bulksale->b_user_image_seller = $this->cdn_url($bulksale->b_user_image_seller);
            } else {
                $bulksale->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
            }
            $bulksale->foto = $this->cdn_url($bulksale->foto);
            $bulksale->thumb = $this->cdn_url($bulksale->thumb);
            $galeri = $this->cbsfm->getByBulkSaleId($nation_code, $id);
            //$this->debug($galeri);
            //die();
            foreach ($galeri as &$g) {
                if (isset($g->url)) {
                    $g->url = $this->cdn_url($g->url);
                }
                if (isset($g->url_thumb)) {
                    $g->url_thumb = $this->cdn_url($g->url_thumb);
                }
            }
            $bulksale->galeri = $galeri;
            unset($galeri); //freed some memory
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->status = 404;
            $this->message = 'Not found';
        }

        $data['bulksale'] = $bulksale;
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
    }
        
    /**
     * Menambahkan data bulksale baru
     * @return [type] [description]
     */
    public function baru()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['bulksale'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }
        //get negara object
        $negara = $this->anm->getByNationCode($nation_code);

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //log
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "Bulksale::baru() - POST: ".json_encode($_POST));
        }

        $b_user_alamat_id = (int) $this->input->post('b_user_alamat_id');
        if ($b_user_alamat_id<=0) {
            $this->status = 1110;
            $this->message = 'Invalid b_user_alamat_id';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //check address are existed
        $alamat = $this->bua->getByIdUserId($nation_code, $pelanggan->id, $b_user_alamat_id);
        if (!isset($alamat->id)) {
            $this->status = 1111;
            $this->message = 'Address not found or already deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }
        
        if ($this->bulksale_enable) {
            $this->status = 1112;
            $this->message = 'Service is comming soon';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        $this->status = 300;
        $this->message = 'Missing one or more parameters';

        //collect product input
        $name = $this->input->post('name');
        $phone = $this->input->post("phone");
        $email = strtolower(trim($this->input->post("email")));
        $agent_name = $this->input->post('agent_name');
        $agent_license = $this->input->post('agent_license');
        $company_name = trim($this->input->post('company_name'));
        $description_long = $this->input->post('description_long');
        $b_user_alamat_id = (int) $this->input->post('b_user_alamat_id');
        $reason = strip_tags($this->input->post('reason'), '<br>');
        if (empty($reason)) {
            $reason = '';
        }
        $is_agent = 0;

        //input validation
        if (empty($name)) {
            $name = '';
        }
        if (empty($phone)) {
            $phone = '';
        }
        if (empty($email)) {
            $email = '';
        }
        if (empty($agent_name)) {
            $agent_name = '';
        }
        if (empty($agent_license)) {
            $agent_license = '';
        }
        if (empty($company_name)) {
            $company_name = '';
        }
        if (empty($description_long)) {
            $description_long = '';
        }

        //trimming
        $agent_name = trim($agent_name);
        $agent_license = trim($agent_license);
        $company_name = trim($company_name);
        $description_long = strip_tags($description_long);

        //is agent validation
        if (strlen($agent_name)>0 || strlen($agent_license)>0 || strlen($company_name)>0) {
            $is_agent = 1;
        }

        //validating FK
        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $description_long = filter_var($description_long, FILTER_SANITIZE_STRING);

        //start transaction
        $this->cbsm->trans_start();

        //get last id
        $cpm_id = $this->cbsm->getLastId($nation_code);

        //input collection to db
        $di = array();
        $di['nation_code'] = $nation_code;
        $di['id'] = $cpm_id;
        $di['b_user_id'] = $pelanggan->id;
        $di['name'] = $name;
        $di['phone'] = $phone;
        $di['email'] = $email;
        $di['agent_name'] = $agent_name;
        $di['agent_license'] = $agent_license;
        $di['company_name'] = $company_name;
        $di['description_long'] = $description_long;
        // by Muhammad Sofi - 3 November 2021 10:00
        // remark code
        // $di['address1'] = $alamat->alamat;
        $di['address2'] = $alamat->alamat2;
        $di['subdistrict'] = $alamat->kelurahan;
        $di['district'] = $alamat->kecamatan;
        $di['city'] = $alamat->kabkota;
        $di['province'] = $alamat->provinsi;
        $di['country'] = $negara->nama;
        $di['zipcode'] = $alamat->kodepos;
        $di['cdate'] = 'NOW()';
        $di['ldate'] = 'NOW()';
        $di['latitude'] = $alamat->latitude;
        $di['longitude'] = $alamat->longitude;
        $di['action_status'] = 'pending';
        $di['reason'] = $reason;
        $di['is_agent'] = $is_agent;
        $res = $this->cbsm->set($di);
        if ($res) {
            $this->cbsm->trans_commit();
            $this->status = 200;
            $this->message = "Success";

            //send email to admin for notification
            if ($this->email_send) {
                $admins = $this->apm->getEmailActive();
                $replacer = array();
                $replacer['site_name'] = $this->app_name;
                $replacer['b_user_fnama_seller'] = $pelanggan->fnama;
                $replacer['b_user_id_seller'] = $pelanggan->fnama;
                $replacer['bulksale_id'] = $cpm_id;
                $this->seme_email->flush();
                $this->seme_email->replyto($this->site_name, $this->site_replyto);
                $this->seme_email->from($this->site_email, $this->site_name);
                foreach ($admins as $adm) {
                    $this->seme_email->to($adm->email, $adm->nama);
                }
                $this->seme_email->subject('Buy It All');
                $this->seme_email->template('buy_it_all');
                $this->seme_email->replacer($replacer);
                $this->seme_email->send();
            }
        } else {
            $this->cbsm->trans_rollback();
            $this->status = 1107;
            $this->message = "Error while posting bulksale, please try again later";
        }
        $this->cbsm->trans_end();

        //doing image upload if success
        if ($res) {
            $img_count = 0;
            $files = $_FILES;
            if (count($files)) {
                $targetdir = $this->media_bulksale;
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
                $this->lib("wideimage/WideImage", 'wideimage', "inc");
                $foto = '';
                $thumb = '';
                $i=0;
                foreach ($files as $kf=>$file) {
                    $i++;
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/BulkSale::baru --uploadProcess: $i");
                    }
                    if ($file['size']<=0) {
                        continue;
                    }
                    if ($file['size']>=2500000) {
                        continue;
                    }
                    if (mime_content_type($file['tmp_name'])=="image/webp") {
                        continue;
                    }
                    $filenames = pathinfo($file['name']);
                    $fileext = 'jpg';
                    if (isset($filenames['extension'])) {
                        $fileext = strtolower($filenames['extension']);
                    }
                    if (!in_array($fileext, array("jpg","png","jpeg"))) {
                        continue;
                    }
                    
                    //create filename
                    $ke = $this->cbsfm->getLastId($nation_code, $cpm_id);
                    $filename = "$nation_code-$cpm_id-$ke";
                    $filecheck = SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename.'.'.$fileext;
                    if (file_exists($filecheck) && is_file($filecheck)) {
                        $rand = rand(0, 999);
                        $filename = "$nation_code-$cpm_id-$ke-".$rand;
                        $filecheck = SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename.'.'.$fileext;
                        if (file_exists($filecheck) && is_file($filecheck)) {
                            $rand = rand(1000, 99999);
                            $filename = "$nation_code-$cpm_id-$ke-".$rand;
                        }
                    }
                    if (file_exists($filecheck) && is_file($filecheck)) {
                        continue;
                    }
                    $filethumb = $filename."-thumb.".$fileext;
                    $filename = $filename.".".$fileext;
                    
                    //completed file upload
                    move_uploaded_file($file['tmp_name'], SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
                    WideImage::load(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)->reSize(370)->saveToFile(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
                    $dix = array();
                    $dix['nation_code'] = $nation_code;
                    $dix['id'] = $ke;
                    $dix['c_bulksale_id'] = $cpm_id;
                    $dix['url_thumb'] = str_replace("//", "/", $targetdir.'/'.$filethumb);
                    $dix['url'] = str_replace("//", "/", $targetdir.'/'.$filename);
                    $res = $this->cbsfm->set($dix);
                    if ($res && strlen($foto)<=4) {
                        $foto = $dix['url'];
                        $thumb = $dix['url_thumb'];
                    }
                }
                if (strlen($foto)>4) {
                    $this->cbsm->update($nation_code, $cpm_id, $pelanggan->id, array("foto"=>$foto,"thumb"=>$thumb));
                }
            }
            
            //building product data for response
            $data['bulksale'] = $this->cbsm->getById($nation_code, $cpm_id);
            $data['bulksale']->galeri = $this->cbsfm->getByBulkSaleId($nation_code, $cpm_id);
            foreach ($data['bulksale']->galeri as &$gal) {
                if (isset($gal->url)) {
                    $gal->url = str_replace("//", "/", $gal->url);
                    $gal->url = $this->cdn_url($gal->url);
                }
                if (isset($gal->url_thumb)) {
                    $gal->url_thumb = str_replace("//", "/", $gal->url_thumb);
                    $gal->url_thumb = $this->cdn_url($gal->url_thumb);
                }
            }
            if (isset($data['bulksale']->b_user_image_seller)) {

                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $data['bulksale']->b_user_image_seller = $this->cdn_url($data['bulksale']->b_user_image_seller);
                if(file_exists(SENEROOT.$data['bulksale']->b_user_image_seller) && $data['bulksale']->b_user_image_seller != 'media/user/default.png'){
                    $data['bulksale']->b_user_image_seller = $this->cdn_url($data['bulksale']->b_user_image_seller);
                } else {
                    $data['bulksale']->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }
            if (isset($data['bulksale']->foto)) {
                $data['bulksale']->foto = $this->cdn_url($data['bulksale']->foto);
            }
            if (isset($data['bulksale']->thumb)) {
                $data['bulksale']->thumb = $this->cdn_url($data['bulksale']->thumb);
            }
        }
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
    }

    public function edit($c_bulksale_id="")
    {
        $dt = $this->__init(); //init

        //default response
        $data = array();
        $data['bulksale'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }
        //get negara object
        $negara = $this->anm->getByNationCode($nation_code);

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        $c_bulksale_id = (int) $c_bulksale_id;
        if ($c_bulksale_id<=0) {
            $this->status = 104;
            $this->message = 'Missing or invalid BulkSale ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        $bulksale = $this->cbsm->getById($nation_code, $c_bulksale_id);
        if (!isset($bulksale->id)) {
            $this->status = 909;
            $this->message = 'Bulksale not found';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        if ($bulksale->b_user_id_seller != $pelanggan->id) {
            $this->status = 907;
            $this->message = "Access denied, you can't change other person's bulksale";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }
        $b_user_alamat_id = (int) $this->input->post('b_user_alamat_id');
        if ($b_user_alamat_id<=0) {
            $this->status = 1110;
            $this->message = 'Invalid b_user_alamat_id';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }
        //check address are existed
        $alamat = $this->bua->getByIdUserId($nation_code, $pelanggan->id, $b_user_alamat_id);
        if (!isset($alamat->id)) {
            $this->status = 1111;
            $this->message = 'Address not found or already deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //sanitize post
        foreach ($_POST as $key=>&$val) {
            if (is_string($val)) {
                if ($key == 'deskripsi') {
                    $val = $this->seme_purifier->richtext($val);
                } else {
                    $val = $this->__f($val);
                }
            }
        }

        //populating input
        $name = $this->input->post("name");
        $phone = $this->input->post("phone");
        $email = strtolower(trim($this->input->post("email")));
        $company_name = $this->input->post("company_name");
        $agent_name = $this->input->post("agent_name");
        $agent_license = (int) $this->input->post("agent_license");
        $description_long =  $this->input->post("description_long");
        $reason = $this->input->post("reason");
        if (empty($reason)) {
            $reason = '';
        }
        $reason = strip_tags($reason, '<br>');

        //validation
        //input validation
        if (empty($name)) {
            $name = '';
        }
        if (empty($agent_name)) {
            $agent_name = '';
        }
        if (empty($agent_license)) {
            $agent_license = '';
        }
        if (empty($company_name)) {
            $company_name = '';
        }
        if (empty($description_long)) {
            $description_long = '';
        }
        if (strlen($name)<=0) {
            $this->status = 910;
            $this->message = 'Please specify owner name';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //updating to database
        $du = array();
        $du['name'] = $name;
        $du['phone'] = $phone;
        $du['email'] = $email;
        $du['agent_name'] = $agent_name;
        $du['agent_license'] = $agent_license;
        $du['company_name'] = $company_name;
        $du['description_long'] = $description_long;
        // by Muhammad Sofi - 3 November 2021 10:00
        // remark code
        // $du['address1'] = $alamat->alamat;
        $du['address2'] = $alamat->alamat2;
        $du['subdistrict'] = $alamat->kelurahan;
        $du['district'] = $alamat->kecamatan;
        $du['city'] = $alamat->kabkota;
        $du['province'] = $alamat->provinsi;
        $du['country'] = $negara->nama;
        $du['zipcode'] = $alamat->kodepos;
        $du['latitude'] = $alamat->latitude;
        $du['longitude'] = $alamat->longitude;
        $du['reason'] = $reason;

        //res
        $res = $this->cbsm->update($nation_code, $c_bulksale_id, $pelanggan->id, $du);
        if ($res) {
            $this->status = 200;
            // $this->message = 'Product edited successfully';
            $this->message = 'Success';
            $data['bulksale'] = $this->cbsm->getById($nation_code, $c_bulksale_id);
            $data['bulksale']->galeri = $this->cbsfm->getByBulkSaleId($nation_code, $c_bulksale_id);
            foreach ($data['bulksale']->galeri as &$gal) {
                if (isset($gal->url)) {
                    $gal->url = str_replace("//", "/", $gal->url);
                    $gal->url = $this->cdn_url($gal->url);
                }
                if (isset($gal->url_thumb)) {
                    $gal->url_thumb = str_replace("//", "/", $gal->url_thumb);
                    $gal->url_thumb = $this->cdn_url($gal->url_thumb);
                }
            }
            if (isset($data['bulksale']->b_kondisi_icon)) {
                $data['bulksale']->b_kondisi_icon = $this->cdn_url($data['bulksale']->b_kondisi_icon);
            }
            if (isset($data['bulksale']->b_berat_icon)) {
                $data['bulksale']->b_berat_icon = $this->cdn_url($data['bulksale']->b_berat_icon);
            }
            if (isset($data['bulksale']->b_user_image_seller)) {

                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $data['bulksale']->b_user_image_seller = $this->cdn_url($data['bulksale']->b_user_image_seller);
                if(file_exists(SENEROOT.$data['bulksale']->b_user_image_seller) && $data['bulksale']->b_user_image_seller != 'media/user/default.png'){
                    $data['bulksale']->b_user_image_seller = $this->cdn_url($data['bulksale']->b_user_image_seller);
                } else {
                    $data['bulksale']->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }
            if (isset($data['bulksale']->foto)) {
                $data['bulksale']->foto = $this->cdn_url($data['bulksale']->foto);
            }
            if (isset($data['bulksale']->thumb)) {
                $data['bulksale']->thumb = $this->cdn_url($data['bulksale']->thumb);
            }
        } else {
            $this->status = 991;
            $this->message = "Can't edit product from database, please try again later";
        }
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
    }

    public function hapus($c_bulksale_id="")
    {
        $dt = $this->__init();
        $data = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        $c_bulksale_id = (int) $c_bulksale_id;
        if ($c_bulksale_id<=0) {
            $this->status = 104;
            $this->message = 'Missing or invalid BulkSale ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        $bulksale = $this->cbsm->getById($nation_code, $c_bulksale_id);
        if (!isset($bulksale->id)) {
            $this->status = 909;
            $this->message = 'BulkSale not found';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        if ($bulksale->b_user_id_seller != $pelanggan->id) {
            $this->status = 907;
            $this->message = "Access denied, you can't change other person's bulksale";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        $galeri = $this->cbsfm->getByBulkSaleId($nation_code, $c_bulksale_id);
        $res = $this->cbsm->del($nation_code, $c_bulksale_id, $pelanggan->id);
        if ($res) {
            //delete file images;
            if (count($galeri)) {
                foreach ($galeri as $gal) {
                    $fileloc = SENEROOT.$gal->url;
                    if (file_exists($fileloc)) {
                        unlink($fileloc);
                    }
                    $fileloc = SENEROOT.$gal->url_thumb;
                    if (file_exists($fileloc)) {
                        unlink($fileloc);
                    }
                }
            }
            $fileloc = SENEROOT.$bulksale->foto;
            if ($bulksale->foto != 'default.png' && (!is_dir($fileloc)) && file_exists($fileloc)) {
                unlink($fileloc);
            }
            $fileloc = SENEROOT.$bulksale->thumb;
            if ($bulksale->foto != 'default.png' && (!is_dir($fileloc)) && file_exists($fileloc)) {
                unlink($fileloc);
            }

            //end delete file images;
            $this->status = 200;
            // $this->message = 'Product deleted successfully';
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
        } else {
            $this->status = 992;
            $this->message = "Can't delete products from database, please try again later";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
        }
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
    }
    public function image_add($c_bulksale_id)
    {
        $dt = $this->__init();
        $keyname = 'foto';

        $data = array();
        $data['bulksale'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        $c_bulksale_id = (int) $c_bulksale_id;
        if ($c_bulksale_id<=0) {
            $this->status = 104;
            $this->message = 'Missing or invalid BulkSale ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        $bulksale = $this->cbsm->getById($nation_code, $c_bulksale_id);
        if (!isset($bulksale->id)) {
            $this->status = 909;
            $this->message = 'BulkSale not found';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        if ($bulksale->b_user_id_seller != $pelanggan->id) {
            $this->status = 907;
            $this->message = "Access denied, you can't change other person's bulksale";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }
        if (!isset($_FILES[$keyname])) {
            $this->status = 1300;
            $this->message = 'Upload failed';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }
        if ($_FILES[$keyname]['size']<=0) {
            $this->status = 1300;
            $this->message = 'Upload failed';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }
        if ($_FILES[$keyname]['size']>=2500000) {
            $this->status = 1302;
            $this->message = $_FILES[$keyname]['size'];
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }
        if (mime_content_type($_FILES[$keyname]['tmp_name'])=="image/webp") {
            $this->status = 1303;
            $this->message = 'WebP image file format is not supported.';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }
        $filenames = pathinfo($_FILES[$keyname]['name']);
        $fileext = '';
        if (isset($filenames['extension'])) {
            $fileext = strtolower($filenames['extension']);
        }
        if (!in_array($fileext, array("jpg","png","jpeg"))) {
            $this->status = 1305;
            $this->message = 'Invalid file extension, please try other file';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        $targetdir = $this->media_bulksale;
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

        $ke = $this->cbsfm->getLastId($nation_code, $c_bulksale_id);
        $filename = "$nation_code-$c_bulksale_id-$ke";
        $filethumb = $filename.'-thumb';
        $filecheck = SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename.'.'.$fileext;
        if (file_exists($filecheck)) {
            $rand = rand(0, 999);
            $filename = "$nation_code-$c_bulksale_id-$ke-".$rand;
            $filecheck = SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename.'.'.$fileext;
            if (file_exists($filecheck)) {
                $rand = rand(1000, 99999);
                $filename = "$nation_code-$c_bulksale_id-$ke-".$rand;
            }
        };
        $filethumb = $filename."-thumb.".$fileext;
        $filename = $filename.".".$fileext;


        move_uploaded_file($_FILES[$keyname]['tmp_name'], SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
        $this->lib("wideimage/WideImage", 'wideimage', "inc");
        if (file_exists(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb)) {
            unlink(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
        }
        WideImage::load(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)->reSize(370)->saveToFile(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);

        $this->status = 200;
        $this->message = 'Success';

        $dix = array();
        $dix['nation_code'] = $nation_code;
        $dix['id'] = $ke;
        $dix['c_bulksale_id'] = $c_bulksale_id;
        $dix['url_thumb'] = str_replace("//", "/", $targetdir.'/'.$filethumb);
        $dix['url'] = str_replace("//", "/", $targetdir.'/'.$filename);
        $res = $this->cbsfm->set($dix);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
            if ($ke==1) {
                $du = array();
                $du['foto'] = $dix['url'];
                $du['thumb'] = $dix['url_thumb'];
                $this->cbsm->update($nation_code, $c_bulksale_id, $pelanggan->id, $du);
            }
            $data['bulksale'] = $this->cbsm->getById($nation_code, $c_bulksale_id);
            $data['bulksale']->galeri = $this->cbsfm->getByBulkSaleId($nation_code, $c_bulksale_id);
            foreach ($data['bulksale']->galeri as &$gal) {
                if (isset($gal->url)) {
                    $gal->url = str_replace("//", "/", $gal->url);
                    $gal->url = $this->cdn_url($gal->url);
                }
                if (isset($gal->url_thumb)) {
                    $gal->url_thumb = str_replace("//", "/", $gal->url_thumb);
                    $gal->url_thumb = $this->cdn_url($gal->url_thumb);
                }
            }
            if (isset($data['bulksale']->b_user_image_seller)) {

                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $data['bulksale']->b_user_image_seller = $this->cdn_url($data['bulksale']->b_user_image_seller);
                if(file_exists(SENEROOT.$data['bulksale']->b_user_image_seller) && $data['bulksale']->b_user_image_seller != 'media/user/default.png'){
                    $data['bulksale']->b_user_image_seller = $this->cdn_url($data['bulksale']->b_user_image_seller);
                } else {
                    $data['bulksale']->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }
            if (isset($data['bulksale']->foto)) {
                $data['bulksale']->foto = $this->cdn_url($data['bulksale']->foto);
            }
            if (isset($data['bulksale']->thumb)) {
                $data['bulksale']->thumb = $this->cdn_url($data['bulksale']->thumb);
            }
        } else {
            $this->status = 990;
            $this->message = 'Failed insert to database';
        }
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
    }

    public function image_delete($c_bulksale_id, $c_bulksale_foto_id)
    {
        $dt = $this->__init();

        $data = array();
        $data['bulksale'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        $c_bulksale_id = (int) $c_bulksale_id;
        if ($c_bulksale_id<=0) {
            $this->status = 104;
            $this->message = 'Missing or invalid BulkSale ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        $bulksale = $this->cbsm->getById($nation_code, $c_bulksale_id);
        if (!isset($bulksale->id)) {
            $this->status = 909;
            $this->message = 'BulkSale not found';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        if ($bulksale->b_user_id_seller != $pelanggan->id) {
            $this->status = 907;
            $this->message = "Access denied, you can't change other person's bulksale";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        $c_bulksale_foto_id = (int) $c_bulksale_foto_id;
        if ($c_bulksale_foto_id<=0) {
            $this->status = 906;
            $this->message = 'Invalid BulkSale Image ID, please check it again';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //$this->debug($pelanggan);
        //die();
        $galeri = $this->cbsfm->getByBulkSaleId($nation_code, $bulksale->id);
        $galeri_id_current = 0;
        $foto_delete = "";
        $thumb_delete = "";
        $foto_replace = "media/produk/default.png";
        $thumb_replace = "media/produk/default.png";
        foreach ($galeri as $gal) {
            if ($gal->url == $bulksale->foto) {
                $galeri_id_current = (int) $gal->id;
            } else {
                $foto_replace = $gal->url;
                $thumb_replace = $gal->url_thumb;
            }
            if ($gal->id == $c_bulksale_foto_id) {
                $foto_delete = $gal->url;
                $thumb_delete = $gal->url_thumb;
            }
        }
        $change_default = 0;
        if ($galeri_id_current == $c_bulksale_foto_id) {
            $change_default = 1;
        }
        $res = $this->cbsfm->delByIdBulkSaleId($nation_code, $c_bulksale_foto_id, $c_bulksale_id);
        if ($res) {
            if (strlen($thumb_delete)>4) {
                $file = SENEROOT.$foto_delete;
                if (!is_dir($file) && file_exists($file)) {
                    unlink($file);
                }
            }
            if (strlen($thumb_delete)>4) {
                $file = SENEROOT.$thumb_delete;
                if (!is_dir($file) && file_exists($file)) {
                    unlink($file);
                }
            }
            if (!empty($change_default)) {
                $du = array();
                $du['foto'] = $foto_replace;
                $du['thumb'] = $thumb_replace;
                $this->cbsm->update($nation_code, $c_bulksale_id, $pelanggan->id, $du);
            }
            $this->status = 200;
            // $this->message = 'BulkSale image successfully deleted';
            $this->message = 'Success';
            $data['bulksale'] = $this->cbsm->getById($nation_code, $c_bulksale_id);
            $data['bulksale']->galeri = $this->cbsfm->getByBulkSaleId($nation_code, $c_bulksale_id);
            foreach ($data['bulksale']->galeri as &$gal) {
                if (isset($gal->url)) {
                    $gal->url = str_replace("//", "/", $gal->url);
                    $gal->url = $this->cdn_url($gal->url);
                }
                if (isset($gal->url_thumb)) {
                    $gal->url_thumb = str_replace("//", "/", $gal->url_thumb);
                    $gal->url_thumb = $this->cdn_url($gal->url_thumb);
                }
            }
            if (isset($data['bulksale']->b_kondisi_icon)) {
                $data['bulksale']->b_kondisi_icon = $this->cdn_url($data['bulksale']->b_kondisi_icon);
            }
            if (isset($data['bulksale']->b_berat_icon)) {
                $data['bulksale']->b_berat_icon = $this->cdn_url($data['bulksale']->b_berat_icon);
            }
            if (isset($data['bulksale']->b_user_image_seller)) {

                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $data['bulksale']->b_user_image_seller = $this->cdn_url($data['bulksale']->b_user_image_seller);
                if(file_exists(SENEROOT.$data['bulksale']->b_user_image_seller) && $data['bulksale']->b_user_image_seller != 'media/user/default.png'){
                    $data['bulksale']->b_user_image_seller = $this->cdn_url($data['bulksale']->b_user_image_seller);
                } else {
                    $data['bulksale']->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }
            if (isset($data['bulksale']->foto)) {
                $data['bulksale']->foto = $this->cdn_url($data['bulksale']->foto);
            }
            if (isset($data['bulksale']->thumb)) {
                $data['bulksale']->thumb = $this->cdn_url($data['bulksale']->thumb);
            }
        } else {
            $this->status = 979;
            $this->message = 'Failed to delete BulkSale image';
        }

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
    }

    public function image_default($c_bulksale_id, $c_bulksale_foto_id)
    {
        $dt = $this->__init();

        $data = array();
        $data['bulksale'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        $c_bulksale_id = (int) $c_bulksale_id;
        if ($c_bulksale_id<=0) {
            $this->status = 104;
            $this->message = 'Missing or invalid BulkSale ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        $bulksale = $this->cbsm->getById($nation_code, $c_bulksale_id);
        if (!isset($bulksale->id)) {
            $this->status = 909;
            $this->message = 'BulkSale not found';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        if ($bulksale->b_user_id_seller != $pelanggan->id) {
            $this->status = 907;
            $this->message = "Access denied, you can't change other person's bulksale";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        $c_bulksale_foto_id = (int) $c_bulksale_foto_id;
        if ($c_bulksale_foto_id<=0) {
            $this->status = 906;
            $this->message = 'Invalid BulkSale Image ID, please check it again';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }

        //$this->debug($pelanggan);
        //die();
        $galeri = $this->cbsfm->getByIdBulkSaleId($nation_code, $c_bulksale_foto_id, $bulksale->id);
        if (!isset($galeri->url)) {
            $this->status = 906;
            $this->message = 'Invalid BulkSale Image ID, please check it again';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
            die();
        }
        $du = array();
        $du['foto'] = $galeri->url;
        $du['thumb'] = $galeri->url_thumb;
        $res = $this->cbsm->update($nation_code, $c_bulksale_id, $pelanggan->id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Succcess';
            $data['bulksale'] = $this->cbsm->getById($nation_code, $c_bulksale_id);
            $data['bulksale']->galeri = $this->cbsfm->getByBulkSaleId($nation_code, $c_bulksale_id);
            foreach ($data['bulksale']->galeri as &$gal) {
                if (isset($gal->url)) {
                    $gal->url = str_replace("//", "/", $gal->url);
                    $gal->url = $this->cdn_url($gal->url);
                }
                if (isset($gal->url_thumb)) {
                    $gal->url_thumb = str_replace("//", "/", $gal->url_thumb);
                    $gal->url_thumb = $this->cdn_url($gal->url_thumb);
                }
            }
            if (isset($data['bulksale']->b_user_image_seller)) {
                
                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $data['bulksale']->b_user_image_seller = $this->cdn_url($data['bulksale']->b_user_image_seller);
                if(file_exists(SENEROOT.$data['bulksale']->b_user_image_seller) && $data['bulksale']->b_user_image_seller != 'media/user/default.png'){
                    $data['bulksale']->b_user_image_seller = $this->cdn_url($data['bulksale']->b_user_image_seller);
                } else {
                    $data['bulksale']->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }
            if (isset($data['bulksale']->foto)) {
                $data['bulksale']->foto = $this->cdn_url($data['bulksale']->foto);
            }
            if (isset($data['bulksale']->thumb)) {
                $data['bulksale']->thumb = $this->cdn_url($data['bulksale']->thumb);
            }
        } else {
            $this->status = 960;
            $this->message = 'Failed to change a default image';
        }
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bulksale");
    }
}
