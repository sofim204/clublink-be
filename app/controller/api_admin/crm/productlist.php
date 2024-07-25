<?php
class Productlist extends JI_Controller
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
        $this->current_parent = 'crm';
        $this->current_page = 'crm_productlist';
        
        //by Donny Dennison - 22 september 2021 15:01
        //revamp-profile
        $this->load("api_admin/b_user_wish_product_model", "buwp");

        $this->load("api_mobile/d_pemberitahuan_model", "dpem");
		$this->load("api_mobile/b_user_setting_model", "busm");
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
        $statusFilter = $this->input->post("produk_status");

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
        $dcount = $this->cpm->countAllProductTakedown($nation_code, $keyword, $fromDate, $toDate, $b_kondisi_id, $courier_service, $is_include_delivery_cost, $is_published, $is_active, $b_kategori_id, $price_min, $price_max, $produk_type, $statusFilter);

        //by Donny Dennison - 8 february 2021 16:44
        //add product type column in product menu
        // $ddata = $this->cpm->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $b_kondisi_id, $courier_service, $is_include_delivery_cost, $is_published, $is_active, $b_kategori_id, $price_min, $price_max);
        $ddata = $this->cpm->getAllProductTakedown($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $fromDate, $toDate, $b_kondisi_id, $courier_service, $is_include_delivery_cost, $is_published, $is_active, $b_kategori_id, $price_min, $price_max, $produk_type, $statusFilter);

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
        $c_produk_id = (int) $c_produk_id;
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

        if ($c_produk_id<=0) {
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
        $c_produk_id = (int) $c_produk_id;
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

        if ($c_produk_id<=0) {
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
        $c_produk_id = (int) $c_produk_id;
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

        if ($c_produk_id<=0) {
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
        $c_produk_id = (int) $c_produk_id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($c_produk_id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        if ($c_produk_id<=0) {
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
        $c_produk_id = (int) $c_produk_id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($c_produk_id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        if ($c_produk_id<=0) {
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
        $c_produk_id = (int) $c_produk_id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($c_produk_id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        if ($c_produk_id<=0) {
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
        $c_produk_id = (int) $c_produk_id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($c_produk_id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        if ($c_produk_id<=0) {
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
        $c_produk_id = (int) $c_produk_id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($c_produk_id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        if ($c_produk_id<=0) {
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
        $c_produk_id = (int) $c_produk_id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($c_produk_id)) {
            $this->status = 400;
            $this->message = 'Authorization required';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        if ($c_produk_id<=0) {
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
			$this->cplm->trans_rollback();
			$this->cplm->trans_end();
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

    public function delete_from_admin()
    {
        $dt = $this->__init();
        $data = array();

        //get current admin
        $pengguna = $dt['sess']->admin;
        $nation_code = $pengguna->nation_code; //get nation_code from current admin

        // $c_produk_id = (int) $c_produk_id;
        // if (empty($c_produk_id)) {
        //     $this->status = 101;
        //     $this->message = "Product ID is not valid";
        //     $this->__json_out($data);
        //     die();
        // }

        $c_produk_id = $this->input->get('c_product_id') ? $this->input->get('c_product_id') : '';
		$admin_name = $this->input->get('admin_name') ? $this->input->get('admin_name') : '';

        $produk = $this->cpm->getById($nation_code, $c_produk_id);
        if (strlen($produk->id) < 0) {
            $this->status = 102;
            $this->message = "Product not found";
            $this->__json_out($data);
            die();
        }

        if ($produk->is_active == 0) {
            $this->status = 102;
            $this->message = "Product already taken down";
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
            $du['admin_name'] = $admin_name;
            $res = $this->cplm->set($du);

            //END by Donny Dennison - 2 march 2021 10:52

            //start transaction
			$this->cpm->trans_start();

			$attachments = $this->cpm->getByProductId($nation_code, $c_produk_id);

            $du = array();
			$du['is_active'] = 1; // set is_active  = 0 means image/video already deleted from server
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

                // by muhammad sofi 24 January 2023 | send push notif to creator
                // select fcm token
                $user = $this->bum->getById($nation_code, $produk->b_user_id);

                $dpe = array();
                $dpe['nation_code'] = $nation_code;
                $dpe['b_user_id'] = $produk->b_user_id;
                $dpe['id'] = $this->dpem->getLastId($nation_code, $produk->b_user_id);
                $dpe['type'] = "product";
                if($user->language_id == 2) {
                    $dpe['judul'] = "Perhatian";
                    $dpe['teks'] =  "Maaf, Produkmu (".html_entity_decode($produk->nama,ENT_QUOTES).") telah dihapus oleh Sellon. Poin SPT dari produkmu akan dibatalkan. Pastikan produkmu asli dan relevan.";
                } else {
                    $dpe['judul'] = "Attention";
                    $dpe['teks'] =  "Sorry, your product (".html_entity_decode($produk->nama,ENT_QUOTES).") is deleted by Sellon. The SPT point of the product will be cancelled. Make sure the product is relevant & original.";
                }

                $dpe['gambar'] = 'media/pemberitahuan/community.png';
                $dpe['cdate'] = "NOW()";
                $extras = new stdClass();
                $extras->id = $c_produk_id;
                $extras->title = $produk->nama;
                if($user->language_id == 2) { 
                    $extras->judul = "Perhatian";
                    $extras->teks =  "Maaf, Produkmu (".html_entity_decode($produk->nama,ENT_QUOTES).") telah dihapus oleh Sellon. Poin SPT dari produkmu akan dibatalkan. Pastikan produkmu asli dan relevan.";
                } else {
                    $extras->judul = "Attention";
                    $extras->teks =  "Sorry, your product (".html_entity_decode($produk->nama,ENT_QUOTES).") is deleted by Sellon. The SPT point of the product will be cancelled. Make sure the product is relevant & original.";
                }

                $dpe['extras'] = json_encode($extras);
                $this->dpem->set($dpe);

                $classified = 'setting_notification_user';
                $code = 'U5';

                $receiverSettingNotif = $this->busm->getValue($nation_code, $produk->b_user_id, $classified, $code);

                if (!isset($receiverSettingNotif->setting_value)) {
                    $receiverSettingNotif->setting_value = 0;
                }

                if ($receiverSettingNotif->setting_value == 1 && $user->is_active == 1) {

                if($user->device == "ios"){
                    //push notif to ios
                    $device = "ios"; //jenis device
                }else{
                    //push notif to android
                    $device = "android"; //jenis device
                }

                $tokens = $user->fcm_token; //device token
                if(!is_array($tokens)) $tokens = array($tokens);
                    if($user->language_id == 2){
                        $title = "Perhatian";
                        $message = "Maaf, Produkmu (".html_entity_decode($produk->nama,ENT_QUOTES).") telah dihapus oleh Sellon. Poin SPT dari produkmu akan dibatalkan. Pastikan produkmu asli dan relevan.";
                    } else {
                        $title = "Attention";
                        $message = "Sorry, your product (".html_entity_decode($produk->nama,ENT_QUOTES).") is deleted by Sellon. The SPT point of the product will be cancelled. Make sure the product is relevant & original.";
                    }
                    
                    $image = 'media/pemberitahuan/promotion.png';
                    $type = 'community';
                    $payload = new stdClass();
                    $payload->id = $c_produk_id;
                    $payload->title = html_entity_decode($produk->nama,ENT_QUOTES);
                    // $payload->harga_jual = $community->harga_jual;
                    // $payload->foto = base_url().$community->thumb;
                    if($user->language_id == 2) {
                        $payload->judul = "Perhatian";
                        //by Donny Dennison
                        //dicomment untuk handle message too big, response dari fcm
                        // $payload->teks = strip_tags(html_entity_decode($di['teks']));
                        // $payload->teks = "You get a reply from your neighbors (".$tempTitle->{'title'}.")";
                        $payload->teks = "Maaf, Produkmu (".html_entity_decode($produk->nama,ENT_QUOTES).") telah dihapus oleh Sellon. Poin SPT dari produkmu akan dibatalkan. Pastikan produkmu asli dan relevan.";
                    } else {
                        $payload->judul = "Attention";
                        $payload->teks = "Sorry, your product (".html_entity_decode($produk->nama,ENT_QUOTES).") is deleted by Sellon. The SPT point of the product will be cancelled. Make sure the product is relevant & original.";
                    }

                    $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);

                }

            }
			$this->cpm->trans_end();
        } else {
            $this->status = 900;
            $this->message = "Failed";
        }
        $this->__json_out($data);
    }

    // by Muhammad Sofi 9 January 2023 | add feature restore post from admin
	public function restore_from_admin() {
		$d = $this->__init();
		$data = array();

		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

        $c_produk_id = $this->input->get('c_product_id') ? $this->input->get('c_product_id') : '';
		$admin_name = $this->input->get('admin_name') ? $this->input->get('admin_name') : '';

		if(empty($c_produk_id)) {
			$this->status = 400;
			$this->message = 'No Product Id';
			$this->__json_out($data);
			die();
		}

		//start transaction and lock table
		$this->cpm->trans_start();

		//initial insert with latest ID
		$du = array();
        $du['is_active'] = "1";
        $du['is_published'] = 1;
        $du['is_visible'] = 1;
        // $du['is_featured'] = 1; // no longer use is_featured
        $du['check_wanted'] = 1;
		$du['reported_status'] = "";
		$res = $this->cpm->update($nation_code, $c_produk_id, $du);

		if (!$res) {
			$this->cpm->trans_rollback();
			$this->cpm->trans_end();
			$this->status = 1108;
			$this->message = "Error while restore product, please try again later";
			$this->__json_out($data);
			die();
		} else {
			$this->cpm->trans_commit();
			$this->status = 200;
			$this->message = 'Success';
		}

		//delete from c_produk_laporan
		$this->cplm->delete_by_product_id($nation_code, $c_produk_id);

		//end transaction
		$this->cpm->trans_end();

		$this->__json_out($data);
	}
}
