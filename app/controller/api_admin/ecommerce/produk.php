<?php
class Produk extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //$this->setTheme('frontx');
        $this->lib("seme_purifier");
        $this->load("api_admin/a_negara_model", 'anm');
        $this->load("api_admin/c_produk_model", 'cpm');
        $this->load("api_admin/c_produk_foto_model", 'cpfm');
        $this->load("api_admin/d_cart_model", 'cart');
        $this->load("api_admin/d_wishlist_model", 'dwlm');
        $this->load("api_admin/c_produk_laporan_model", 'cplm');
        $this->load("api_admin/common_code_model", "ccm");
        $this->load("api_admin/b_user_model", 'bum');
        $this->load("api_admin/g_leaderboard_point_history_model", "glphm");
        $this->load("api_admin/b_kategori_model2", 'bkm');
        $this->load("api_mobile/b_user_alamat_model", "bua");
        $this->load("api_mobile/g_daily_track_record_model", 'gdtrm');
        $this->load("api_mobile/c_produk_foto_model", 'cpfm_mobile');
        $this->current_parent = 'ecommerce';
        $this->current_page = 'ecommerce_produk';
        
        //by Donny Dennison - 22 september 2021 15:01
        //revamp-profile
        $this->load("api_admin/b_user_wish_product_model", "buwp");
    }

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

    private function thumbParser($imgname)
    {
        $imgnames = explode('.', $imgname);
        $imgname_last = $imgnames[count($imgnames)-1];
        return rtrim($imgname, '.'.$imgname_last).'_thumb.'.$imgname_last;
    }

    private function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // trim
        $text = trim($text, '-');
        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // lowercase
        $text = strtolower($text);
        return $text;
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
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;
        $negara = $this->anm->getByNationCode($nation_code);
        if (!isset($negara->simbol_mata_uang)) {
            $negara->simbol_mata_uang = '-';
        }

        //collect table alias
        $tbl_as = $this->cpm->getTableAlias();
        $tbl2_as = $this->cpm->getTableAlias2();

        //collect standard input
        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $sSearch = $this->input->post("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->post("iDisplayLength");
        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");

        //collect custom input
        $b_kategori_id = $this->input->post("b_kategori_id");
        $b_kondisi_id = $this->input->post("b_kondisi_id");
        $courier_service = $this->input->post("courier_service");
        $is_include_delivery_cost = $this->input->post("free_ship");
        $produk_status = $this->input->post("produk_status");

        //by Donny Dennison - 8 february 2021 16:44
        //add product type column in product menu
        $produk_type = $this->input->post("produk_type");

        $price_min = $this->input->post("price_min");
        $price_max = $this->input->post("price_max");
        $fromDate = $this->input->post("from_date");
        $toDate = $this->input->post("to_date");

        //validate custom input
        $is_published = "";
        $is_active = "";
        switch ($produk_status) {
            case 'publish_active':
                $is_published=1;
                $is_active=1;
                break;
            case 'draft_active':
                $is_published=0;
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

        //input validation
        $sortCol = $iSortCol_0;
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }

        //validating date interval
        if (strlen($fromDate)==10) {
            $fromDate = date("Y-m-d", strtotime($fromDate));
        } else {
            $fromDate = "";
        }
        if (strlen($toDate)==10) {
            $toDate = date("Y-m-d", strtotime($toDate));
        } else {
            $toDate = "";
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

        //by Donny Dennison - 8 february 2021 16:44
        //add product type column in product menu
        // $dcount = $this->cpm->countAll($nation_code, $keyword, $b_kondisi_id, $courier_service, $is_include_delivery_cost, $is_published, $is_active, $b_kategori_id, $price_min, $price_max);
        $dcount = $this->cpm->countAll($nation_code, $keyword, $fromDate, $toDate, $b_kondisi_id, $courier_service, $is_include_delivery_cost, $is_published, $is_active, $b_kategori_id, $price_min, $price_max, $produk_type);

        //by Donny Dennison - 8 february 2021 16:44
        //add product type column in product menu
        // $ddata = $this->cpm->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $b_kondisi_id, $courier_service, $is_include_delivery_cost, $is_published, $is_active, $b_kategori_id, $price_min, $price_max);
        $ddata = $this->cpm->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $fromDate, $toDate, $b_kondisi_id, $courier_service, $is_include_delivery_cost, $is_published, $is_active, $b_kategori_id, $price_min, $price_max, $produk_type);

        foreach ($ddata as &$gd) {
            if (isset($gd->thumb)) {
                if (strlen($gd->thumb)<=10) {
                    $gd->thumb = 'media/produk/default.png';
                }
                $gd->thumb = '<img src="'.$this->cdn_url($gd->thumb).'" class="img-responsive" style="max-width: 128px;"  onerror="this.onerror=null;this.src=\''.$this->cdn_url('media/produk/default.png').'\';"/>';
            }

            if (isset($gd->nama)) {
                $gd->nama = $this->__st2($gd->nama, 30);
                $nama = '<h4 class="tbl-content"><b>'.$this->__convertToEmoji($gd->nama).'</b></h4>';
                if (isset($gd->kategori)) {
                    $nama .= '<p class="tbl-content-category">'.$gd->kategori.'</p>';
                }
                
                //by Donny Dennison - 8 february 2021 16:44
                //add product type column in product menu
                if (isset($gd->product_type)) {
                    if($gd->product_type == 'Free') $gd->product_type = 'Gratis';
                    $nama .= '<p class="tbl-content-category">'.$gd->product_type.'</p>';
                }

                $nama .= '<table class="tbl-product-properties"><tr><td>';
                if (isset($gd->b_kondisi_nama)) {
                    $nama .= '<strong>'.$gd->b_kondisi_nama.'</strong>';
                }
                $nama .= '</td><td>';
                if (isset($gd->courier_services)) {
                    
                    //by Donny Dennison - 23 september 2020 15:42
                    //add direct delivery feature
                    //START by Donny Dennison - 23 september 2020 15:42

                    if (strtolower($gd->courier_services)=='direct delivery') {

                        $nama .= '<img src="'.$this->cdn_url("assets/images/direct_delivery.png").'" class="img-responsive img-icon"  />';

                    // if (strtolower($gd->courier_services)=='qxpress') {
                    }else if (strtolower($gd->courier_services)=='qxpress') {

                    //END by Donny Dennison - 23 september 2020 15:42

                        $nama .= '<img src="'.$this->cdn_url("assets/images/qxpress.png").'" class="img-responsive img-icon"  />';

                    //by Donny Dennison - 15 september 2020 17:45
                    //change name, image, etc from gogovan to gogox
                    // } elseif (strtolower($gd->courier_services)=='gogovan') {
                    } elseif (strtolower($gd->courier_services)=='gogox') {

                        //by Donny Dennison - 15 september 2020 17:45
                        //change name, image, etc from gogovan to gogox
                        // $nama .= '<img src="'.$this->cdn_url("assets/images/gogovan.png").'" class="img-responsive img-icon"  />';
                        $nama .= '<img src="'.$this->cdn_url("assets/images/gogox.png").'" class="img-responsive img-icon"  />';

                    } else {
                        $nama .= '<img src="'.$this->cdn_url("assets/images/unavailable.png").'" class="img-responsive img-icon"  />';
                    }
                }
                $nama .= '</td></tr></table>';
                $gd->nama = $nama;
            }
            if (isset($gd->b_user_nama)) {
                $nama = '<h4 class="tbl-content"><b>'.$gd->b_user_nama.'</b></h4>';
                if (isset($gd->b_user_telp)) {
                    $nama .= '<p class="tbl-content-category"><i class="fa fa-phone"></i> '.$gd->b_user_telp.'</p>';
                }
                if (isset($gd->b_user_email)) {
                    $nama .= '<p class="tbl-content-category"><i class="fa fa-envelope"></i> '.$this->__st($gd->b_user_email, 30).'</p>';
                }
                //if(isset($gd->b_user_image)) $nama .= '<img src="'.base_url($gd->b_user_image).'" class="img-responsive img-icon"  />';
                $gd->b_user_nama = $nama;
            }
            if (isset($gd->harga_jual)) {
                // $gd->harga_jual = $negara->simbol_mata_uang.''.$gd->harga_jual;
                // by Muhammad Sofi 21 December 18:00 | formatting to currency / money format 
                $number = $gd->harga_jual;
                // by Muhammad Sofi 14 February 2022 14:51 | change format currency to indonesian version
                $format = number_format($number, 2, ',', '.');
                $gd->harga_jual = $format;
            }
            // if (isset($gd->cdate)) {
            //     $gd->cdate = date("j F y H:i", strtotime($gd->cdate));
            // }
            if (isset($gd->is_active)) {
                $nama = '';
                if (empty($gd->is_active)) {
                    $prop = '<span class="tbl-content-nok">Inactive</span>';
                    $nama .= $prop.'';
                } else {
                    if (empty($gd->is_published)) {
                        $prop = '<span class="tbl-content-nok">Draft</span>';
                        $nama .= $prop.'<br />';
                    } else {
                        $prop = '<span class="tbl-content-ok">Show on Homepage <i class="fa fa-check"></i></span>';
                        if (empty($gd->is_featured)) {
                            $prop = '';
                        }
                        if (strlen($prop)>0) {
                            $nama .= $prop.'<br />';
                        }

                        $prop = '<span class="tbl-content-ok">Free Shipping</span>';
                        if (empty($gd->is_include_delivery_cost)) {
                            $prop = '';
                        }
                        if (strlen($prop)>0) {
                            $nama .= $prop.'<br />';
                        }

                        $prop = '<span class="tbl-content-ok">Publish <i class="fa fa-check"></i></span>';
                        if (strlen($prop)>0) {
                            $nama .= $prop.'<br />';
                        }

                        $prop = '<span class="tbl-content-ok">Delivery Cost Included</span>';
                        if (empty($gd->is_include_delivery_cost)) {
                            $prop = '';
                        }
                        if (strlen($prop)>0) {
                            $nama .= $prop.'<br />';
                        }

                        $prop = '<span class="tbl-content-ok">Active <i class="fa fa-check"></i></span>';
                        if (empty($gd->is_active)) {
                            $prop = '<span class="tbl-content-nok">Inactive</span>';
                        }
                        $nama .= $prop.'';
                    }
                }
                $gd->is_active = $nama;
            }
        }

        //render
        $this->status = 200;
        $this->message = 'Success';
        $this->__jsonDataTable($ddata, $dcount);
    }
    public function check()
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
        $kolom = $this->input->request('kolom');
        $nilai = $this->input->request('nilai');

        if (strlen($kolom)>1 && strlen($nilai)>1) {
            $res = $this->cpm->check($kolom, $nilai);
            if ($res) {
                $this->status = 443;
                $this->message = 'Sudah Digunakan';
            } else {
                $this->status = 442;
                $this->message = 'Belum Digunakan';
            }
        } else {
            $this->status = 444;
            $this->message = 'Parameter kurang';
        }
        $this->__json_out($data);
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
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;
        $di = $_POST;
        foreach ($di as $key=>&$val) {
            if (is_string($val)) {
                if ($key == 'deskripsi') {
                    $val = $this->seme_purifier->richtext($val);
                } else {
                    $val = $this->__f($val);
                }
            }
        }
        if (!isset($di['nama'])) {
            $di['nama'] = "";
        }
        if (isset($di['image'])) {
            unset($di['image']);
        }
        if (isset($di['caption'])) {
            unset($di['caption']);
        }
        if (isset($di['produk_items'])) {
            unset($di['produk_items']);
        }
        if (strlen($di['nama'])>1 && strlen($di['sku'])>1) {
            $check = $this->cpm->check('sku', $di['sku']); //1 = sudah digunakan
            if (empty($check)) {
                $nama = $di['nama'];
                $sku = $di['sku'];
                $slug = $nama.'-'.$sku;
                if (isset($di['slug'])) {
                    if (!empty($di['slug'])) {
                        $slug = $di['slug'];
                    }
                }
                $slug = $this->slugify($slug);
                $slug_check = $this->cpm->checkSlug($slug);
                $try =0;
                while (($slug_check > 0) && ($try <= 5)) {
                    $slug .= $slug.'-'.rand(0, 999);
                    $slug_check = $this->cpm->checkSlug($slug);
                    $try++;
                }
                $di['slug'] = $slug;

                if (isset($di['foto'])) {
                    if (strlen($di['foto'])>3) {
                        $di['thumb'] = $this->thumbParser($di['thumb']);
                    }
                }
                $di['sku'] = strtoupper($di['sku']);
                //$this->debug($di);
                //die();
                $res = $this->cpm->set($di);
                if ($res) {
                    //foto dan caption
                    $fotos = array();
                    $dgi = array(); //for produk fotos
                    $dgc = array(); //for produk fotos caption
                    if (is_array($this->input->post('image'))) {
                        $dgi = $this->input->post('image');
                    }
                    if (is_array($this->input->post('caption'))) {
                        $dgc = $this->input->post('image');
                    }
                    $i=0;

                    $i=0;
                    foreach ($dgi as $it) {
                        $gi = array();
                        $gi['c_produk_id'] = $res;
                        $gi['url'] = str_replace('//', '/', $it);
                        $gi['url_thumb'] = $this->thumbParser(str_replace('//', '/', $it));
                        $gi['caption'] = '';
                        $fotos[$i] = $gi;
                        $i++;
                    }

                    $i=0;
                    foreach ($dgc as $it) {
                        if (isset($fotos[$i]['caption'])) {
                            $fotos[$i]['caption'] = $it;
                            $i++;
                        }
                    }
                    if (count($fotos)) {
                        $res2 = $this->cpfm->setMass($fotos);
                    }

                    //set default thumb & img
                    if (isset($fotos[0]['url'])) {
                        $dx = array();
                        $dx['foto'] = $fotos[0]['url'];
                        $dx['thumb'] = $fotos[0]['url_thumb'];
                        $this->cpm->update($res, $dx);
                    }

                    //bundling produk


                    $this->status = 200;
                    $this->message = 'Data successfully added';
                } else {
                    $this->status = 900;
                    $this->message = 'Tidak dapat menyimpan data baru, silakan coba beberapa saat lagi';
                }
            } else {
                $this->status = 104;
                $this->message = 'Code already used, please try another code';
            }
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
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $this->status = 200;
        $this->message = 'Success';
        $data = $this->cpm->getById($nation_code, $id);
        $this->__json_out($data);
    }
    public function edit($id="")
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

        //populate post
        $du = $_POST;
        foreach ($du as $key=>&$val) {
            if (is_string($val)) {
                if ($key == 'deskripsi') {
                    $val = $this->seme_purifier->richtext($val);
                } else {
                    $val = $this->__f($val);
                }
            }
        }
        $id = (int) $id;
        if (empty($id)) {
            if (isset($du['id'])) {
                $id = (int) $du['id'];
            }
            unset($du['id']);
        }

        if (!isset($du['nama'])) {
            $di['nama'] = "";
        }
        if (isset($du['image'])) {
            unset($du['image']);
        }
        if (isset($du['caption'])) {
            unset($du['caption']);
        }
        if (isset($du['produk_items'])) {
            unset($du['produk_items']);
        }
        if ($id>0 && strlen($du['nama'])>0) {
            //$check = $this->cpm->checkSku($du['sku'],$id); //1 = sudah digunakan
            $check = 0;
            if (empty($check)) {
                //echo json_encode($du);
                //die();
                $res = $this->cpm->update($nation_code, $id, $du);
                if ($res) {
                    //foto dan caption
                    $fotos = array();
                    $dgi = array(); //for produk fotos
                    $dgc = array(); //for produk fotos caption
                    if (is_array($this->input->post('image'))) {
                        $dgi = $this->input->post('image');
                    }
                    if (is_array($this->input->post('caption'))) {
                        $dgc = $this->input->post('image');
                    }
                    $i=0;

                    $i=0;
                    foreach ($dgi as $it) {
                        $gi = array();
                        $gi['c_produk_id'] = $id;
                        $gi['url'] = str_replace('//', '/', $it);
                        $gi['url_thumb'] = $this->thumbParser(str_replace('//', '/', $it));
                        $gi['caption'] = '';
                        $fotos[$i] = $gi;
                        $i++;
                    }

                    $i=0;
                    foreach ($dgc as $it) {
                        if (isset($fotos[$i]['caption'])) {
                            $fotos[$i]['caption'] = $it;
                            $i++;
                        }
                    }
                    if (is_array($fotos) && count($fotos)) {
                        $this->cpfm->delByProdukId($id);
                        $res2 = $this->cpfm->setMass($fotos);
                    }

                    //set default thumb & img
                    if (isset($fotos[0]['url'])) {
                        $dx = array();
                        $dx['foto'] = $fotos[0]['url'];
                        $dx['thumb'] = $fotos[0]['url_thumb'];
                        $this->cpm->update($nation_code, $id, $dx);
                    }

                    $this->status = 200;
                    $this->message = 'Perubahan berhasil diterapkan';
                } else {
                    $this->status = 901;
                    $this->message = 'Failed to make data changes';
                }
            } else {
                $this->status = 104;
                $this->message = 'Code already used, please try another code';
            }
        }
        $this->__json_out($data);
    }
    public function hapus($id)
    {
        $id = (int) $id;
        $d = $this->__init();
        $data = array();
        if ($id<=0) {
            $this->status = 500;
            $this->message = 'ID tidak valid';
            $this->__json_out($data);
            die();
        }
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
        $res = $this->cpm->del($nation_code, $id);
        if (!$res) {
            $this->status = 902;
            $this->message = 'Failed while deleting data from database';
        }
        $this->__json_out($data);
    }
    public function image($id)
    {
        $id = (int) $id;
        $d = $this->__init();
        $data = array();
        $data['images'] = array();
        if (!$this->admin_login && empty($id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        //get from db
        $images = $this->cpfm->getByProdukId($nation_code, $id);
        if (is_array($images)) {
            $this->status = 200;
            $this->message = 'Success';
            foreach ($images as &$im) {
                if ($im->utype == 'internal') {
                    $im->url = ($im->url);
                    $im->url_thumb = ($im->url_thumb);
                }
            }
            $data['images'] = $images;
        }
        $this->__json_out($data);
    }
    public function gambar_upload($c_produk_id)
    {
        // $c_produk_id = (int) $c_produk_id;
        $d = $this->__init();
        $data = array();
        $data['images'] = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        if ($c_produk_id<='0') {
            $this->status = 700;
            $this->message = 'Invalid product ID';
            $this->__json_out($data);
            die();
        }
        $produk = $this->cpm->getById($nation_code, $c_produk_id);
        if (!isset($produk->id)) {
            $this->status = 451;
            $this->message = 'Product not found or has been deleted';
            $this->__json_out($data);
            die();
        }

        if (isset($_FILES['image'])) {
            $ext = 'jpg';
            $pi = pathinfo($_FILES['image']['name']);
            if (isset($pi['extension'])) {
                $ext = strtolower($pi['extension']);
            }
            $exts = array("jpg","jpeg","png");
            if (in_array($ext, $exts)) {
                if ($_FILES['image']['size']>500000) {
                    $this->status = 1959;
                    $this->message = 'Image file size too big, please try another image';
                    $this->__json_out($data);
                    die();
                }
                if ($_FILES['image']['size']>0 && $_FILES['image']['size']<=500000) {
                    if (mime_content_type($_FILES['image']['tmp_name']) == 'image/webp') {
                        $this->status = 1958;
                        $this->message = 'WebP image format currently unsupported on this system';
                        $this->__json_out($data);
                        die();
                    }
                }
                $target_dir = $this->media_produk;
                $ifol = SENEROOT.DIRECTORY_SEPARATOR.$target_dir;
                if (!is_dir($ifol)) {
                    if (PHP_OS == "WINNT") {
                        if (!is_dir($ifol)) {
                            mkdir($ifol);
                        }
                    } else {
                        if (!is_dir($ifol)) {
                            mkdir($ifol, 0775, true);
                        }
                    }
                }
                $target_dir = $target_dir.DIRECTORY_SEPARATOR.date("Y");
                $ifol = SENEROOT.DIRECTORY_SEPARATOR.$target_dir;
                if (!is_dir($ifol)) {
                    if (PHP_OS == "WINNT") {
                        if (!is_dir($ifol)) {
                            mkdir($ifol);
                        }
                    } else {
                        if (!is_dir($ifol)) {
                            mkdir($ifol, 0775, true);
                        }
                    }
                }
                $target_dir = $target_dir.DIRECTORY_SEPARATOR.date("m");
                $ifol = SENEROOT.DIRECTORY_SEPARATOR.$target_dir;
                if (!is_dir($ifol)) {
                    if (PHP_OS == "WINNT") {
                        if (!is_dir($ifol)) {
                            mkdir($ifol);
                        }
                    } else {
                        if (!is_dir($ifol)) {
                            mkdir($ifol, 0775, true);
                        }
                    }
                }
                $rand = rand(100, 999);
                $filename = $nation_code.'-'.$c_produk_id.'-'.$rand.'.'.$ext;
                $filethumb = $nation_code.'-'.$c_produk_id.'-'.$rand.'-thumb.'.$ext;
                $filetarget = $target_dir.DIRECTORY_SEPARATOR.$filename;

                if (file_exists(SENEROOT.$filetarget)) {
                    $rand = rand(100, 999);
                    $filename = $nation_code.'-'.$c_produk_id.'-'.$rand.'.'.$ext;
                    $filethumb = $nation_code.'-'.$c_produk_id.'-'.$rand.'-thumb.'.$ext;
                    $filetarget = $target_dir.DIRECTORY_SEPARATOR.$filename;
                    if (file_exists(SENEROOT.$filetarget)) {
                        $rand = rand(1000, 9999);
                        $filename = $nation_code.'-'.$c_produk_id.'-'.$rand.'.'.$ext;
                        $filethumb = $nation_code.'-'.$c_produk_id.'-'.$rand.'-thumb.'.$ext;
                        $filetarget = $target_dir.DIRECTORY_SEPARATOR.$filename;
                    }
                }
                $filetargetthumb = $target_dir.DIRECTORY_SEPARATOR.$filethumb;
                $filetarget = str_replace('//', '/', $filetarget);

                move_uploaded_file($_FILES["image"]["tmp_name"], SENEROOT.$filetarget);
                if (file_exists(SENEROOT.$filetarget)) {
                    $this->lib("wideimage/WideImage", "inc");
                    $filetargetthumb = str_replace('//', '/', $filetargetthumb);

                    $f1 = str_replace("//", "/", SENEROOT.$filetarget);
                    $f2 = str_replace("//", "/", SENEROOT.$filetargetthumb);
                    WideImage::load($f1)->resize(300)->saveToFile($f2);
                    WideImage::load($f2)->crop('center', 'center', 300, 300)->saveToFile($f2);

                    $filetarget = ltrim($filetarget, '/');
                    $filetargetthumb = ltrim($filetargetthumb, '/');

                    $di = array();
                    $di['nation_code'] = $nation_code;
                    $di['c_produk_id'] = $c_produk_id;
                    $di['id'] = $this->cpfm->getLastId($nation_code, $c_produk_id);
                    $di['utype'] = 'internal';
                    $di['jenis'] = 'foto';
                    $di['url'] = $filetarget;
                    $di['url_thumb'] = $filetargetthumb;
                    $di['is_active'] = 1;
                    $res = $this->cpfm->set($di);
                    if ($res) {
                        $this->status = 200;
                        $this->message = 'Success';
                    } else {
                        $this->status = 902;
                        $this->message = 'Failed upload data';
                    }
                } else {
                    $this->status = 903;
                    $this->message = 'Failed upload data';
                }
            } else {
                $this->status = 904;
                $this->message = 'Only JPG, JPEG, PNG extension allowed';
            }
        } else {
            $this->status = 905;
            $this->message = 'Image parameter not delivered, please check again';
        }
        $this->__json_out($data);
    }
    public function gambar_hapus($c_produk_id, $c_produk_foto_id)
    {
        // $c_produk_id = (int) $c_produk_id;
        $c_produk_foto_id = (int) $c_produk_foto_id;
        $d = $this->__init();
        $data = array();
        $data['images'] = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        if ($c_produk_id<='0') {
            $this->status = 701;
            $this->message = 'Invalid product ID';
            $this->__json_out($data);
            die();
        }
        if ($c_produk_foto_id<=0) {
            $this->status = 702;
            $this->message = 'ID Foto Produk tidak sah';
            $this->__json_out($data);
            die();
        }
        $produk = $this->cpm->getById($nation_code, $c_produk_id);
        if (!isset($produk->id)) {
            $this->status = 451;
            $this->message = 'Product not found or has been deleted';
            $this->__json_out($data);
            die();
        }
        $produk_foto = $this->cpfm->getByIdProdukId($nation_code, $c_produk_foto_id, $c_produk_id);
        if (!isset($produk_foto->id)) {
            $this->status = 452;
            $this->message = 'Image product not found or has been deleted';
            $this->__json_out($data);
            die();
        }
        $res = $this->cpfm->delByIdProdukId($nation_code, $c_produk_id, $c_produk_foto_id);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
            if (file_exists(SENEROOT.'/'.$produk_foto->url)) {
                unlink(SENEROOT.'/'.$produk_foto->url);
            }
            if (file_exists(SENEROOT.'/'.$produk_foto->url_thumb)) {
                unlink(SENEROOT.'/'.$produk_foto->url_thumb);
            }
        } else {
            $this->status = 900;
            $this->message = 'Failed deleted image from database';
        }
        $this->__json_out($data);
    }
    public function gambar_cover($c_produk_id, $c_produk_foto_id)
    {
        // $c_produk_id = (int) $c_produk_id;
        $c_produk_foto_id = (int) $c_produk_foto_id;
        $d = $this->__init();
        $data = array();
        $data['images'] = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        if ($c_produk_id<='0') {
            $this->status = 701;
            $this->message = 'Invalid product ID';
            $this->__json_out($data);
            die();
        }
        if ($c_produk_foto_id<=0) {
            $this->status = 702;
            $this->message = 'ID Foto Produk tidak sah';
            $this->__json_out($data);
            die();
        }
        $produk = $this->cpm->getById($nation_code, $c_produk_id);
        if (!isset($produk->id)) {
            $this->status = 451;
            $this->message = 'Product not found or has been deleted';
            $this->__json_out($data);
            die();
        }
        $produk_foto = $this->cpfm->getByIdProdukId($nation_code, $c_produk_foto_id, $c_produk_id);
        if (!isset($produk_foto->id)) {
            $this->status = 452;
            $this->message = 'Image product not found or has been deleted';
            $this->__json_out($data);
            die();
        }
        $du = array();
        $du['foto'] = $produk_foto->url;
        $du['thumb'] = $produk_foto->url_thumb;
        $res = $this->cpm->update($nation_code, $c_produk_id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->status = 900;
            $this->message = 'Failed deleted image from database';
        }
        $this->__json_out($data);
    }
    public function draft($c_produk_id)
    {
        // $c_produk_id = (int) $c_produk_id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($c_produk_id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        if ($c_produk_id<='0') {
            $this->status = 400;
            $this->message = 'Invalid Product ID';
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $produk = $this->cpm->getById($nation_code, $c_produk_id);
        if (!isset($produk->id)) {
            $this->status = 400;
            $this->message = 'Product not found';
            $this->__json_out($data);
            die();
        }
        if (empty($produk->is_published) && !empty($produk->is_active)) {
            $this->status = 999;
            $this->message = 'Product already in draft mode';
            $this->__json_out($data);
            die();
        }
        $du = array("is_published"=>0, "is_active"=>1);
        $res = $this->cpm->update($nation_code, $c_produk_id, $du);

        //by Donny Dennison - 22 september 2021 15:01
        //revamp-profile
        $this->buwp->delete($nation_code, $c_produk_id);

        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->status = 188;
            $this->message = 'Failed updating data product';
        }
        $this->__json_out($data);
    }
    public function publish($c_produk_id)
    {
        // $c_produk_id = (int) $c_produk_id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($c_produk_id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        if ($c_produk_id<='0') {
            $this->status = 400;
            $this->message = 'Invalid Product ID';
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $produk = $this->cpm->getById($nation_code, $c_produk_id);
        if (!isset($produk->id)) {
            $this->status = 400;
            $this->message = 'Product not found';
            $this->__json_out($data);
            die();
        }
        if (!empty($produk->is_published)) {
            $this->status = 999;
            $this->message = 'Product already published';
            $this->__json_out($data);
            die();
        }
        $du = array("is_published"=>1);
        $res = $this->cpm->update($nation_code, $c_produk_id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->status = 188;
            $this->message = 'Failed updating data product';
        }
        $this->__json_out($data);
    }

    public function homepage_show($c_produk_id)
    {
        // $c_produk_id = (int) $c_produk_id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($c_produk_id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        if ($c_produk_id<='0') {
            $this->status = 400;
            $this->message = 'Invalid Product ID';
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $produk = $this->cpm->getById($nation_code, $c_produk_id);
        if (!isset($produk->id)) {
            $this->status = 400;
            $this->message = 'Product not found';
            $this->__json_out($data);
            die();
        }
        if (!empty($produk->is_featured)) {
            $this->status = 999;
            $this->message = 'Product already set as featured';
            $this->__json_out($data);
            die();
        }
        $du = array("is_featured"=>1);
        $res = $this->cpm->update($nation_code, $c_produk_id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->status = 188;
            $this->message = 'Failed updating data product';
        }
        $this->__json_out($data);
    }

    public function homepage_hide($c_produk_id)
    {
        // $c_produk_id = (int) $c_produk_id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($c_produk_id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        if ($c_produk_id<='0') {
            $this->status = 400;
            $this->message = 'Invalid Product ID';
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $produk = $this->cpm->getById($nation_code, $c_produk_id);
        if (!isset($produk->id)) {
            $this->status = 400;
            $this->message = 'Product not found';
            $this->__json_out($data);
            die();
        }
        if (empty($produk->is_featured)) {
            $this->status = 999;
            $this->message = 'Product already removed from featured product';
            $this->__json_out($data);
            die();
        }
        $du = array("is_featured"=>0);
        $res = $this->cpm->update($nation_code, $c_produk_id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->status = 188;
            $this->message = 'Failed updating data product';
        }
        $this->__json_out($data);
    }

    public function active($c_produk_id)
    {
        // $c_produk_id = (int) $c_produk_id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($c_produk_id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        if ($c_produk_id<='0') {
            $this->status = 400;
            $this->message = 'Invalid Product ID';
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $produk = $this->cpm->getById($nation_code, $c_produk_id);
        if (!isset($produk->id)) {
            $this->status = 400;
            $this->message = 'Product not found';
            $this->__json_out($data);
            die();
        }
        if (!empty($produk->is_active)) {
            $this->status = 999;
            $this->message = 'Product already activated';
            $this->__json_out($data);
            die();
        }
        $du = array("is_active"=>1, "stok" =>1);
        $res = $this->cpm->update($nation_code, $c_produk_id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->status = 188;
            $this->message = 'Failed updating data product';
        }
        $this->__json_out($data);
    }

    public function inactive($c_produk_id)
    {
        // $c_produk_id = (int) $c_produk_id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($c_produk_id)) {
            $this->status = 400;
            $this->message = 'Authorization required';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        if ($c_produk_id<='0') {
            $this->status = 400;
            $this->message = 'Invalid Product ID';
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $produk = $this->cpm->getById($nation_code, $c_produk_id);
        if (!isset($produk->id)) {
            $this->status = 400;
            $this->message = 'Product not found';
            $this->__json_out($data);
            die();
        }
        if (empty($produk->is_active)) {
            $this->status = 999;
            $this->message = 'Product already inactive';
            $this->__json_out($data);
            die();
        }
        $du = array("is_active"=>0, "stok"=>0);
        $res = $this->cpm->update($nation_code, $c_produk_id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
            $c_produk_ids = array($c_produk_id);
            $this->cart->delAllByProdukIds($nation_code, $c_produk_ids);
            $this->dwlm->delAllByProdukIds($nation_code, $c_produk_ids);

            //by Donny Dennison - 22 september 2021 15:01
            //revamp-profile
            $this->buwp->delete($nation_code, $c_produk_id);

        } else {
            $this->status = 188;
            $this->message = 'Failed updating data product';
        }
        $this->__json_out($data);
    }

    //by Donny Dennison - 3 january 2021 14:19
    //change chat to open chatting
    public function getproductajax()
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
        $negara = $this->anm->getByNationCode($nation_code);
        if (!isset($negara->simbol_mata_uang)) {
            $negara->simbol_mata_uang = '-';
        }

        //collect standard input
        $search = $this->input->post("search");
        $user_id = $this->input->post("user_id");

        $ddata = $this->cpm->getByUserID($nation_code, $search, $user_id);

        $data = array();

        foreach ($ddata as $gd) {
            if (isset($gd->thumb)) {
                if (strlen($gd->thumb)<=10) {
                    $gd->thumb = 'media/produk/default.png';
                }
                $gd->thumb = '<img src="'.$this->cdn_url($gd->thumb).'" class="img-responsive" style="max-width: 128px;"  onerror="this.onerror=null;this.src=\''.$this->cdn_url('media/produk/default.png').'\';"/>';
            }

            if (isset($gd->nama)) {
                $gd->nama = $this->__st2($gd->nama, 30);
                
            }
            
            if (isset($gd->harga_jual)) {
                $gd->harga_jual = $negara->simbol_mata_uang.''.$gd->harga_jual;
            }


          $data[] = array("id"=>$gd->id, "text"=>$gd->nama.' - '.$gd->harga_jual);
            
        }

        echo json_encode($data);
    }

    // by Muhammad Sofi 20 December 2022 | add feature report from admin
	public function report_from_admin($c_produk_id) {
		$d = $this->__init();
        $data = array();

		$pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

		$owner_product = $this->cpm->getById($nation_code, $c_produk_id);

		//start transaction and lock table
		$this->cplm->trans_start();

		//initial insert with latest ID
		$di = array();
		$di['nation_code'] = $nation_code;
		$di['c_produk_id'] = $c_produk_id;
		$di['b_user_id'] = 0; // admin
		$di['cdate'] = 'NOW()';
		$res = $this->cplm->set($di);

		if (!$res) {
			$this->ccrm->trans_rollback();
			$this->ccrm->trans_end();
			$this->status = 1108;
			$this->message = "Error while report product, please try again later";
			$this->__json_out($data);
			die();
		} else {
			$this->cplm->trans_commit();
			$this->status = 200;
			$this->message = 'Success';
		}

		//end transaction
		$this->cplm->trans_end();

		//update is_report and report_date
		$di = array();
        $di['is_active'] = "0";
        $di['is_published'] = 0;
        $di['is_visible'] = 0;
        $di['check_wanted'] = 0;
		$this->cpm->update($nation_code, $c_produk_id, $di);

		// if($res) {
		// 	$this->status = 200;
		// 	$this->message = 'Success';
		// }else {
		// 	$this->status = 920;
		// 	$this->message = 'Failed to report';
		// }

		$this->__json_out($data);
	}

    public function delete_from_admin($c_produk_id)
    {
        $dt = $this->__init();
        $data = array();

        //get current admin
        $pengguna = $dt['sess']->admin;
        $nation_code = $pengguna->nation_code; //get nation_code from current admin

        // $c_produk_id = (int) $c_produk_id;
        if (empty($c_produk_id)) {
            $this->status = 101;
            $this->message = "Product ID is not valid";
            $this->__json_out($data);
            die();
        }

        $produk = $this->cpm->getById($nation_code, $c_produk_id);
        if (strlen($produk->id) < 0) {
            $this->status = 102;
            $this->message = "Product not found";
            $this->__json_out($data);
            die();
        }

        $pelanggan = $this->bum->getById($nation_code, $produk->b_user_id);

		$di = array();
        $di['is_active'] = "0";
        $di['is_published'] = 0;
        $di['is_visible'] = 0;
        $di['is_featured'] = 0;
        $di['check_wanted'] = 0;
		$di['reported_status'] = "takedown";
		
        $res = $this->cpm->update($nation_code, $c_produk_id, $di);
        if ($res) {
            $this->status = 200;
            $this->message = "Done";

            //by Donny Dennison - 2 march 2021 10:52
            //add need action column in dashboard
            //START by Donny Dennison - 2 march 2021 10:52
            $du = array();
            $du['nation_code'] = $nation_code;
            $du['c_produk_id'] = $c_produk_id;
            $du['b_user_id'] = 0;
            $du['kategori'] = 'spam';
            $du['cdate'] = 'NOW()'; // report date
            $du['reported_status'] = "takedown";
            $res = $this->cplm->set($du);

            //END by Donny Dennison - 2 march 2021 10:52

            //start transaction
			$this->cpm->trans_start();

			$attachments = $this->cpm->getByProductId($nation_code, $c_produk_id);

            $du = array();
			$du['is_active'] = 0;
			$res2 = $this->cpm->updateByProductId($nation_code, $c_produk_id, $du);
			if ($res2) {
				$this->cpm->trans_commit();
				$this->status = 200;
				$this->message = 'Success';

				// //delete attachment file
				// if (count($attachments)) {
				// 	// $i = 0;
				// 	foreach ($attachments as $atc) {
				// 		// $i++;
				// 		if($atc->jenis == 'image' || $atc->jenis == 'video'){
				// 			// if ($atc->url != $this->media_community_video."default.png") {
				// 			// 	$fileloc = SENEROOT.$atc->url;
				// 			// 	if (file_exists($fileloc)) {
				// 			// 		unlink($fileloc);
				// 			// 	}
				// 			// }
			
				// 			// if ($atc->url_thumb != $this->media_community_video."default.png") {
				// 			// 	$fileloc = SENEROOT.$atc->url_thumb;
				// 			// 	if (file_exists($fileloc)) {
				// 			// 		unlink($fileloc);
				// 			// 	}
				// 			// }

				// 			if($atc->jenis == "video" && $atc->convert_status != "uploading"){

				// 				// if(count($attachments) != 0 && count($attachments) < 2) {
				// 				// if($i == 1 && $atc->jenis == "video") {
				// 					//get point
				// 					$pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EO");
				// 					if (!isset($pointGet->remark)) {
				// 						$pointGet = new stdClass();
				// 						$pointGet->remark = 3;
				// 					}

				// 					$leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $produk->b_user_id, $produk->kelurahan, $produk->kecamatan, $produk->kabkota, $produk->provinsi);
				// 					$di = array();
				// 					$di['nation_code'] = $nation_code;
				// 					$di['id'] = $leaderBoardHistoryId;
				// 					$di['b_user_alamat_location_kelurahan'] = $produk->kelurahan;
				// 					$di['b_user_alamat_location_kecamatan'] = $produk->kecamatan;
				// 					$di['b_user_alamat_location_kabkota'] = $produk->kabkota;
				// 					$di['b_user_alamat_location_provinsi'] = $produk->provinsi;
				// 					$di['b_user_id'] = $produk->b_user_id;
				// 					$di['plusorminus'] = "-";
				// 					$di['point'] = $pointGet->remark;
				// 					$di['custom_id'] = $c_produk_id;
				// 					$di['custom_type'] = 'product';
				// 					$di['custom_type_sub'] = 'takedown video';
				// 					$di['custom_text'] = 'Admin has '.$di['custom_type_sub'].' '.$pelanggan->fnama.' '.$di['custom_type'].' and lose '.$di['point'].' point(s)';
				// 					$this->glphm->set($di);
				// 					$this->cpm->trans_commit();
				// 				// } 
				// 				// break;
				// 			}
				// 		}

				// 	}
				// 	unset($atc);
				// }

                // $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EA");
                // if (!isset($pointGet->remark)) {
                //     $pointGet = new stdClass();
                //     $pointGet->remark = 10;
                // }

                // $leaderBoardHistoryId = $this->glphm->getLastIdLeaderboardPointHistory($nation_code, $produk->b_user_id, $produk->kelurahan, $produk->kecamatan, $produk->kabkota, $produk->provinsi);
                // $di = array();
                // $di['nation_code'] = $nation_code;
                // $di['id'] = $leaderBoardHistoryId;
                // $di['b_user_alamat_location_kelurahan'] = $produk->kelurahan;
                // $di['b_user_alamat_location_kecamatan'] = $produk->kecamatan;
                // $di['b_user_alamat_location_kabkota'] = $produk->kabkota;
                // $di['b_user_alamat_location_provinsi'] = $produk->provinsi;
                // $di['b_user_id'] = $produk->b_user_id;
                // $di['plusorminus'] = "-";
                // $di['point'] = $pointGet->remark;
                // $di['custom_id'] = $c_produk_id;
                // $di['custom_type'] = 'product';
                // $di['custom_type_sub'] = 'takedown post';
                // $di['custom_text'] = 'Admin has '.$di['custom_type_sub'].' '.$pelanggan->fnama.' '.$di['custom_type'].' and lose '.$di['point'].' point(s)';
                // $this->glphm->set($di);
                // $this->cpm->trans_commit();

            }
			$this->cpm->trans_end();
        } else {
            $this->status = 900;
            $this->message = "Failed";
        }
        $this->__json_out($data);
    }

    public function upload_xls(){
        set_time_limit(0);
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
		$nation_code = $pengguna->nation_code;	 

        // ====== check $_POST ==========================
        if (trim($_POST['email']) == '' || trim($_POST['email']) == null){
            $this->status = 104;
			$this->message = 'Email must be filled';
			$this->__json_out($data);
			die();
        }
        //  check file
        if ($_FILES["file_xls"]["tmp_name"] == '' || $_FILES["file_xls"]["tmp_name"] == null){
            $this->status = 104;
			$this->message = 'File must be filled';
			$this->__json_out($data);
			die();
        }
        // ========= End Check $_POST =========================

        // cek email in db
        $data_user_seller = $this->bum->getByEmail($nation_code, trim($_POST['email']));
        if (!isset($data_user_seller->id)) {
            $this->status = 104;
            $this->message = 'Email not found or has been deleted';
            $this->__json_out($data);
            die();
        }
        // end cek email in db

        // Check extension
        $filename = $_FILES['file_xls']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if($ext != 'xlsx'){
            //it is not xlsx
            $this->status = 104;
			$this->message = 'File must be xlsx';
			$this->__json_out($data);
			die();
        }  
        // End Check Extension

        //loading library xls
        $this->lib('phpexcel/PHPExcel','','inc');
        $this->lib('phpexcel/PHPExcel/Reader/Excel2007','','inc');

        $objReader = new PHPExcel_Reader_Excel2007($_FILES['file_xls']['name']);        
        $data_file = $objReader->load($_FILES['file_xls']['tmp_name']);
        $objData = $data_file->getActiveSheet();
        $dataArray = $objData->toArray();



        // var_dump(date_format(date_create($dataArray[3]['5']),"Y-m-d H:i:s"));die;  
        // var_dump(str_replace(".","-",$dataArray[3]['3']));die;
        // var_dump($dataArray[0]);die;
        
        // check format header
        if (
            $dataArray[0]['0'] != "Nama Produk" ||
            $dataArray[0]['1'] != "Kategori produk" ||
            $dataArray[0]['2'] != "Deskripsi" ||
            $dataArray[0]['3'] != "harga produk" ||
            $dataArray[0]['4'] != "Gambar 1" ||
            $dataArray[0]['5'] != "Gambar 2" ||
            $dataArray[0]['6'] != "Gambar 3" ||
            $dataArray[0]['7'] != "Gambar 4" ||
            $dataArray[0]['8'] != "Gambar 5"
        ) {
            $this->status = 104;
			$this->message = 'Format Header File is Wrong. Please check again.';
			$this->__json_out($data);
			die();
        }
        // End check format header

        // limit hanya bisa 10 row
        $limit_upload = 0;
        for ($k=4; $k < count($dataArray) ; $k++) {
            // cek jika semua baris kosong, maka lewati (tidak dihitung)
            if ($dataArray[$k]['0'] == "" && $dataArray[$k]['1'] == "" && $dataArray[$k]['2'] == "" && $dataArray[$k]['3'] == "" && $dataArray[$k]['4'] == "" && $dataArray[$k]['5'] == "" && $dataArray[$k]['6'] == "" && $dataArray[$k]['7'] == "" && $dataArray[$k]['8'] == "") {
                // tidak dilakukan pengecekan lanjut pengecekan selanjutnya
            } else {
                $limit_upload++;
            }
            if ($limit_upload > 10) {
                $this->status = 104;
                $this->message = 'Exceeding the specified limit (Maximum 10 Data) . Please check again.';
                $this->__json_out($data);
                die();
            }
        }
        // End limit 10 row
        
        //start transaction
		$this->cpm->trans_start();
        $tampung_foto = array();
        $tampung_foto_thumb = array();
        $tampung_foto_media = array();
        for ($i=4; $i < count($dataArray) ; $i++) {

            
            $product_name = $dataArray[$i]['0'];            
            // $product_category_name = $dataArray[$i]['1'];
            // $check_category = $this->bkm->checkCategoryByNameID($nation_code, str_replace("&#38;","&#38; ",$this->__f($product_category_name)));
            // var_dump($check_category);die;
            // if (!$check_category) {
            //     $this->status = 110;
            //     $this->message = 'Failed !! Category not found. Please check again.';
            //     $this->cpm->trans_rollback();
            //     $this->cpm->trans_end();
            //     $this->__json_out($data);
            //     die();
            // }             
            // if (isset($check_category->id)) {
            //     if ($check_category->id == '' || $check_category->id == null) {
            //         $this->status = 110;
            //         $this->message = 'Failed !! Category not found. Please check again.';
            //         $this->cpm->trans_rollback();
            //         $this->cpm->trans_end();
            //         $this->__json_out($data);
            //         die();
            //     }                
            // }else{
            //     $this->status = 110;
            //     $this->message = 'Failed !! Category not found. Please check again.';
            //     $this->cpm->trans_rollback();
            //     $this->cpm->trans_end();
            //     $this->__json_out($data);
            //     die();
            // }
            // $category_id = $check_category->id;

            $product_category_id = $dataArray[$i]['1'];
            if ($product_category_id != "") {
                if (!is_numeric($product_category_id)) {
                    $this->status = 110;
                    $this->message = 'Failed !! Category product must be numberic.';
                    $this->cpm->trans_rollback();
                    $this->cpm->trans_end();
                    $this->__json_out($data);
                }
            }            
            $category_id = $product_category_id;
            $product_desc = $dataArray[$i]['2'];
            $product_price = $dataArray[$i]['3'];
            $product_image1 = $dataArray[$i]['4'];
            $product_image2 = $dataArray[$i]['5'];
            $product_image3 = $dataArray[$i]['6'];
            $product_image4 = $dataArray[$i]['7'];
            $product_image5 = $dataArray[$i]['8'];

            // cek jika semua baris kosong, maka lewati (tidak dihitung)
            if ($product_name == "" && $product_category_id == "" && $product_desc == "" && $product_price == "" && $product_image1 == "" && $product_image2 == "" && $product_image3 == "" && $product_image4 == "" && $product_image5 == "") {
                // tidak dilakukan pengecekan lanjut pengecekan selanjutnya
            } else {
                // cek required data 
                if ($product_name == "" || $category_id == "" || $product_desc == "" || $product_price == "" ) {
                    $this->status = 110;
                    $this->message = 'Failed !! Incomplete data. Please check again.';
                    $this->cpm->trans_rollback();
                    $this->cpm->trans_end();
                    $this->__json_out($data);
                    die();
                } elseif($product_image1 == "" && $product_image2 == "" && $product_image3 == "" && $product_image4 == "" && $product_image5 == "") {
                    $this->status = 110;
                    $this->message = 'Failed !! Incomplete data. Please check again.';
                    $this->cpm->trans_rollback();
                    $this->cpm->trans_end();
                    $this->__json_out($data);
                    die();
                }
                // end cek mandatory data
                
                // cek gambar 1 sampai 3 (mandatory)
                if ($product_image1 == ""){
                    $product_image1 = $product_image2 ?? $product_image3 ?? $product_image4 ?? $product_image5;
                }
                if ($product_image2 == ""){
                    $product_image2 = $product_image1 ?? $product_image3 ?? $product_image4 ?? $product_image5;
                }
                if ($product_image3 == ""){
                    $product_image3 = $product_image1 ?? $product_image2 ?? $product_image4 ?? $product_image5;
                }
                // end cek gambar mandatory

                // cek alamat seller
                $sellerAddress = $this->bua->getByUserIdDefault($nation_code, $data_user_seller->id);
                if(isset($sellerAddress->id)){
                    $b_user_alamat_id = $sellerAddress->id;
                    unset($sellerAddress);
                }else{
                    $this->status = 110;
                    $this->message = 'Failed !! Seller Address not found. Please check again.';
                    $this->cpm->trans_rollback();
                    $this->cpm->trans_end();
                    $this->__json_out($data);
                    die();
                }
                //validating user address
                if ($b_user_alamat_id<=0) {
                    $this->status = 110;
                    $this->message = 'Failed !! Invalid b_user_alamat_id.';
                    $this->cpm->trans_rollback();
                    $this->cpm->trans_end();
                    $this->__json_out($data);
                    die();
                }
                $almt = $this->bua->getByIdUserId($nation_code, $data_user_seller->id, $b_user_alamat_id);
                if (!isset($almt->id)) {
                    $this->status = 110;
                    $this->message = 'Failed !! Seller Address not found. Please check again.';
                    $this->cpm->trans_rollback();
                    $this->cpm->trans_end();
                    $this->__json_out($data);
                    die();
                }
                // End cek alamat seller

                // create ID produk
                $endDoWhile = 0;
                do{

                    $product_id = $this->GUIDv4();

                    $checkId = $this->cpm->checkId($nation_code, $product_id);

                    if($checkId == 0){
                        $endDoWhile = 1;
                    }

                }while($endDoWhile == 0);
                // End create ID produk

                // foto
                if (empty($foto)) {
                    $foto = "media/produk/default.png";
                }
                // end foto



                //initial insert with latest ID
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['id'] = $product_id;
                $di['b_user_id'] = $data_user_seller->id;
                $di['b_user_alamat_id'] = $b_user_alamat_id;
                $di['b_kategori_id'] = $category_id;
                $di['b_kondisi_id'] = 4;
                // $di['b_berat_id'] = $b_berat_id;
                $di['nama'] = $product_name;
                $di['brand'] = '';
                $di['harga_jual'] = $product_price;
                $di['deskripsi_singkat'] = '';

                // $product_desc = str_replace('’',"'",$product_desc);
                // $product_desc = nl2br($product_desc);
                // $product_desc = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $product_desc);
                // $product_desc = str_replace("\n", "<br />", $product_desc);

                $di['deskripsi'] = $product_desc;
                $di['foto'] = $foto;
                $di['thumb'] = $foto;
                // $di['satuan'] = $satuan;
                $di['stok'] = 1;
                // $di['berat'] = $berat;
                $di['berat'] = 1.0;
                // $di['dimension_long'] = $dimension_long;
                // $di['dimension_width'] = $dimension_width;
                // $di['dimension_height'] = $dimension_height;
                // $di['courier_services'] = $courier_services;
                // $di['vehicle_types'] = $vehicle_types;
                // $di['services_duration'] = $services_duration;
                $di['cdate'] = 'NOW()';

                //by Donny Dennison - 19 january 2022 10:35
                //merge table free product to table product
                $di['start_date'] = 'NOW()';

                // $di['is_include_delivery_cost'] = $is_include_delivery_cost;
                $di['is_published'] = 1;

                //by Donny Dennison - 7 december 2020 11:03
                //add new product type(meetup)
                $di['product_type'] = 'MeetUp';

                $di['alamat2'] = $almt->alamat2;
                $di['kelurahan'] = $almt->kelurahan;
                $di['kecamatan'] = $almt->kecamatan;
                $di['kabkota'] = $almt->kabkota;
                $di['provinsi'] = $almt->provinsi;
                $di['kodepos'] = $almt->kodepos;
                $di['latitude'] = $almt->latitude;
                $di['longitude'] = $almt->longitude;

                //by Donny Dennison - 19 january 2022 10:35
                //merge table free product to table product
                // if($product_type == 'Free'){
                // $di['end_date'] = date("Y-m-d", strtotime("+".$this->produk_gratis_limit_hari." day"));
                // $di['check_wanted'] = "1";
                // }

                //by Donny Dennison - 3 june 2022 13:10
                //new feature, product type santa
                // if($product_type == 'Santa'){
                // $di['check_wanted'] = "1";
                // }

                // === FOTO ============================
                $path = $this->__checkDir(date("Y/m"));
                $iq = new stdClass();
                $iq->img_count = 0;

                $product_image_arr = [$product_image1,$product_image2,$product_image3,$product_image4,$product_image5];
                for($x=0;$x<5;$x++){
                    if ($product_image_arr[$x] != "") {
                        $iq->cpfm_id = $this->cpfm_mobile->getLastId($nation_code,$product_id);
                        if (isset($iq->cpfm_id)) {
                            $cpfm_id = $iq->cpfm_id;
                        }else {
                            $this->status = 110;
                            $this->message = 'Failed !! Product Foto ID not found.';
                            $this->cpm->trans_rollback();
                            $this->cpm->trans_end();
                            $this->__json_out($data);
                            die();
                        }
                        // @$rawImage = file_get_contents($product_image_arr[$x]);
                        // var_dump($rawImage);die;

                        // ====== create curl resource (Gantinya file_get_contents)===================
                        $ch = curl_init();

                        // set url
                        curl_setopt($ch, CURLOPT_URL, $product_image_arr[$x]);

                        //return the transfer as a string
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

                        // $output contains the output string
                        $rawImage = curl_exec($ch);

                        // close curl resource to free up system resources
                        curl_close($ch); 

                        // var_dump($rawImage);die;

                        // ====== END create curl resource ===================

                        if($rawImage)
                        {
                            // $name_file = rand();
                            $ke = $cpfm_id;
                            $filename = "$nation_code-$product_id-$ke-".date('YmdHis');
                            $file_path_thumb = parse_url($product_image_arr[$x], PHP_URL_PATH);                        
                            $extension = pathinfo($file_path_thumb, PATHINFO_EXTENSION);
                            $file = $filename.".".$extension;
                            $filethumb = $filename."-thumb.".$extension;
                            // khusus utk foto pertama saja utk dimasukkan ke c_produk
                            if($x==0) {
                                $di['foto'] = $this->media_produk.date("Y/m").'/'.$file;
                                $di['thumb'] = $this->media_produk.date("Y/m").'/'.$filethumb;
                            }
                            file_put_contents($path.'/'.$file,$rawImage);                        
                            file_put_contents($path.'/'.$filethumb,$rawImage);                        
                            // echo 'Image Saved';
                            $dpi = $this->__dataImageProduct($nation_code,$product_id,$cpfm_id,$this->media_produk.date("Y/m").'/'.$file,$this->media_produk.date("Y/m").'/'.$filethumb,$iq->img_count);

                            array_push($tampung_foto, $path.'/'.$file);
                            array_push($tampung_foto_thumb, $path.'/'.$filethumb);
                            array_push($tampung_foto_media, $this->media_produk.date("Y/m").'/'.$file);
                            if($dpi){
                                // $iq->cpfm_id++;
                                $iq->img_count++;
                                
                            }else{
                                // Hapus data yang telah masuk di db dan hapus file gambar di directory
                                if (count($tampung_foto) > 0) {
                                    for($t=0;$t<count($tampung_foto);$t++){
                                        $this->cpfm->delByUrlGambar($nation_code, $tampung_foto_media[$t]);
                                        unlink($tampung_foto[$t]);
                                        unlink($tampung_foto_thumb[$t]);
                                    }
                                }
                                // END Hapus data yang telah masuk di db dan hapus file gambar di directory
                                $this->status = 110;
                                $this->message = 'Failed save uploaded image to db';
                                $this->cpm->trans_rollback();
                                $this->cpm->trans_end();
                                $this->__json_out($data);
                                die();                            
                            }
                        }else{
                            // Hapus data yang telah masuk di db dan hapus file gambar di directory
                            if (count($tampung_foto) > 0) {
                                for($t=0;$t<count($tampung_foto);$t++){
                                    $this->cpfm->delByUrlGambar($nation_code, $tampung_foto_media[$t]);
                                    unlink($tampung_foto[$t]);
                                    unlink($tampung_foto_thumb[$t]);
                                }
                            }
                            // END Hapus data yang telah masuk di db dan hapus file gambar di directory
                            $this->status = 110;
                            $this->message = 'Failed !! Invalid Image Link.';
                            $this->cpm->trans_rollback();
                            $this->cpm->trans_end();
                            $this->__json_out($data);
                            die();
                        }
                    }
                }
                // === END FOTO ============================

                // PROSES INSERT C_PRODUK
                $res = $this->cpm->set($di);
                if (!$res) {
                    // Hapus data yang telah masuk di db dan hapus file gambar di directory
                    if (count($tampung_foto) > 0) {
                        for($t=0;$t<count($tampung_foto);$t++){
                            $this->cpfm->delByUrlGambar($nation_code, $tampung_foto_media[$t]);
                            unlink($tampung_foto[$t]);
                            unlink($tampung_foto_thumb[$t]);
                        }
                    }
                    // END Hapus data yang telah masuk di db dan hapus file gambar di directory
                    $this->status = 110;
                    $this->message = 'Failed !! Error while posting product, please try again later.';
                    $this->cpm->trans_rollback();
                    $this->cpm->trans_end();
                    $this->__json_out($data);
                    die();
                }
                // END PROSES INSERT C_PRODUK

                // $this->gdtrm->updateTotalData(DATE("Y-m-d"), "product_post", "+", "1"); // belum dibutuhkan




            }
            // END cek jika semua baris kosong, maka lewati (tidak dihitung)           
        }

        $this->status = 200;
        $this->message = 'Success';
        $this->cpm->trans_commit();

        $this->cpm->trans_end();
		$this->__json_out($data);
	}

    public function checkEmail($email)
    {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($email)) {
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

        // cek email in db
        $data = $this->bum->getByEmail($nation_code, trim($email));
        if (!isset($data->id)) {
            $this->status = 104;
            $this->message = 'Email not found or has been deleted';
            $this->__json_out($data);
            die();
        }
        // end cek email in db
           
        $this->__json_out($data);
    }

    function __checkDir($periode)
    {
        if (!is_dir(SENEROOT.'media/')) {
            mkdir(SENEROOT.'media/', 0777);
        }
        $targetdir = $this->media_produk; 
        if (!is_dir(SENEROOT.$targetdir)) {
            mkdir(SENEROOT.$targetdir, 0777);
        }
        $str = $periode.'/01';
        $periode_y = date("Y", strtotime($str));
        $periode_m = date("m", strtotime($str));
        if (!is_dir(SENEROOT.$targetdir.$periode_y)) {
            mkdir(SENEROOT.$targetdir.$periode_y, 0777);
        }
        if (!is_dir(SENEROOT.$targetdir.$periode_y.'/'.$periode_m)) {
            mkdir(SENEROOT.$targetdir.$periode_y.'/'.$periode_m, 0777);
        }
        return SENEROOT.$targetdir.$periode_y.'/'.$periode_m;
    }

    private function __moveImagex($nation_code, $url, $produk_id="0", $ke="")
    {
        $sc = new stdClass();
        $sc->status = 500;
        $sc->message = 'Error';
        $sc->image = '';
        $sc->thumb = '';
        // $produk_id = (int) $produk_id;

        $targetdir = $this->media_produk;
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

        $file_path = SENEROOT.parse_url($url, PHP_URL_PATH);

        if (file_exists($file_path) && is_file($file_path)) {
            
        $file_path_thumb = parse_url($url, PHP_URL_PATH);
        $extension = pathinfo($file_path_thumb, PATHINFO_EXTENSION);
        $file_path_thumb = substr($file_path_thumb,0,strripos($file_path_thumb,'.'));
        $file_path_thumb = SENEROOT.$file_path_thumb.'-thumb.'.$extension;

        $filename = "$nation_code-$produk_id-$ke-".date('YmdHis');
        $filethumb = $filename."-thumb.".$extension;
        $filename = $filename.".".$extension;

        rename($file_path, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
        rename($file_path_thumb, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);

        $sc->status = 200;
        $sc->message = 'Success';
        $sc->thumb = str_replace("//", "/", $targetdir.'/'.$filethumb);
        $sc->image = str_replace("//", "/", $targetdir.'/'.$filename);
        
        } else {
        $sc->status = 997;
        $sc->message = 'Failed';
        }
        
        if ($this->is_log) {
        $this->seme_log->write("api_mobile", 'API_Mobile/Produk::__moveImagex -- INFO URL: '.$url.' PID:'.$produk_id.' ke:'.$ke.' '.$sc->status.' '.$sc->message.'');
        }
        return $sc;
    }

    private function __dataImageProduct($nation_code,$c_produk_id,$cpfm_id,$url,$thumb,$img_count){
        $dix = array();
        $dix['nation_code'] = $nation_code;
        $dix['c_produk_id'] = $c_produk_id;
        $dix['id'] = $cpfm_id;
        $dix['url'] = $url;
        $dix['url_thumb'] = $thumb;
        $dix['is_active'] = 1;
        $dix['caption'] = '';
        return $this->cpfm_mobile->set($dix);
    }
}
