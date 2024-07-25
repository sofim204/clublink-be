<?php
class Produkreport extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();

        //by Donny Dennison - 26 october 2020 15:16
        //fix report product notif
        $this->lib("seme_log");

        //$this->setTheme('frontx');
        $this->lib("seme_purifier");
        $this->load("api_admin/a_negara_model", 'anm');
        $this->load("api_admin/c_produk_laporan_model", 'cplm');
        $this->load("api_admin/c_produk_model", 'cpm');
        $this->load("api_admin/c_produk_foto_model", 'cpfm');
        $this->load("api_admin/b_user_model", 'bum');
        $this->load("api_admin/d_pemberitahuan_model", 'dpem');
        $this->load("api_admin/common_code_model", "ccm");
        $this->load("api_admin/g_leaderboard_point_history_model", "glphm");
        $this->current_parent = 'crm';
        $this->current_page = 'crm_produkreport';
        
        //by Donny Dennison - 22 september 2021 15:01
        //revamp-profile
        $this->load("api_admin/b_user_wish_product_model", "buwp");
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
        $tbl_as = $this->cplm->getTableAlias();
        $tbl2_as = $this->cplm->getTableAlias2();
        $tbl3_as = $this->cplm->getTableAlias3();

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
        $b_kondisi_id = $this->input->post("b_kondisi_id");
        $b_kategori_id = $this->input->post("b_kategori_id");
        $c_produk_id = $this->input->post("c_produk_id");
        $b_user_id = $this->input->post("b_user_id");
        $courier_service = $this->input->post("courier_service");
        $is_include_delivery_cost = $this->input->post("free_ship");
        $price_min = $this->input->post("price_min");
        $price_max = $this->input->post("price_max");
        $reported_status = $this->input->post("reported_status");
        $s_admin_name = $this->input->post('p_admin_name');
        $from_date = $this->input->post("from_date");
        $end_date = $this->input->post("end_date");

        switch ($reported_status) {
            case 'takedown':
                $reported_status = 'takedown';
                break;
            case 'ignore':
                $reported_status = 'ignore';
                break;
            default:
                $reported_status = "";
                break;
        }

        //input validation
        $sortCol = "date";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }

        switch($iSortCol_0){
			case 0:
				$sortCol = "no";
				break;
			case 1:
				$sortCol = "c_produk_id";
				break;
			case 2:
				$sortCol = "b_user_id";
				break;
			case 3:
				$sortCol = "cdate";
				break;
			case 4:
				$sortCol = "thumb";
				break;
			case 5:
				$sortCol = "c_produk_nama";
				break;
			case 6:
				$sortCol = "b_user_nama_seller";
				break;
			case 7:
				$sortCol = "deskripsi";
				break;
			case 8:
				$sortCol = "b_user_nama_reporter";
				break;
			case 9:
				$sortCol = "admin_name";
				break;
			case 10:
				$sortCol = "jumlah_lapor";
				break;
			case 11:
				$sortCol = "reported_status";
				break;
			default:
				$sortCol = "no";
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

        $dcount = $this->cplm->countAll(
            $nation_code,
            $keyword,
            $b_kondisi_id,
            $courier_service,
            $is_include_delivery_cost,
            $b_kategori_id, $price_min,
            $price_max,
            $c_produk_id,
            $b_user_id,
            $reported_status,
            "",
            $s_admin_name,
            $from_date,
            $end_date
        );

        $ddata = $this->cplm->getAll(
            $nation_code,
            $page,
            $pagesize,
            $sortCol,
            $sortDir,
            $keyword,
            $b_kondisi_id,
            $courier_service,
            $is_include_delivery_cost,
            $b_kategori_id,
            $price_min,
            $price_max,
            $c_produk_id,
            $b_user_id,
            $reported_status,
            $s_admin_name,
            $from_date,
            $end_date
        );

        foreach ($ddata as &$gd) {
            $reported_status = '';
            if (isset($gd->reported_status)) {
                $reported_status = $gd->reported_status;
            }
            if (isset($gd->thumb)) {
                if (strlen($gd->thumb)<=10) {
                    $gd->thumb = 'media/produk/default.png';
                }
                $gd->thumb = '<img src="'.$this->cdn_url($gd->thumb).'" class="img-responsive" style="max-width: 128px;"  onerror="this.onerror=null;this.src=\''.$this->cdn_url('media/produk/default.png').'\';"/>';
            }
            
            if (isset($gd->c_produk_nama)) {
                $gd->c_produk_nama = $this->__st2($gd->c_produk_nama, 30);
                $c_produk_nama = '<h4 class="tbl-content" style="margin-bottom: 5px;"><b>'.$this->__convertToEmoji($gd->c_produk_nama).'</b></h4>';
                // if (isset($gd->b_user_nama_seller)) {
                //     $c_produk_nama .= '<p class="tbl-content-category">'.$gd->b_user_nama_seller.'</p>';
                // }
                // $c_produk_nama .= '<table class="tbl-product-properties"><tr><td>';
                // if(isset($gd->b_user_email_seller)) {
                //     $c_produk_nama .= ''.$gd->b_user_email_seller.'<br/>';
                // }
                if (isset($gd->product_type)) {
                    $c_produk_nama .= '<strong>'.$gd->product_type.'</strong><br />';
                }
                // if (isset($gd->c_produk_harga_jual)) {
                //     $c_produk_nama .= '<strong>'.$negara->simbol_mata_uang.'. '.number_format((float)$gd->c_produk_harga_jual, 2, ',', '.').'</strong>';
                //     // $c_produk_nama .= '<strong>'.$negara->simbol_mata_uang.'. '.$gd->c_produk_harga_jual.'</strong>';
                // }
                if (isset($gd->c_produk_harga_jual)) {
                    // $gd->harga_jual = $negara->simbol_mata_uang.''.$gd->harga_jual;
                    // by Muhammad Sofi 21 December 18:00 | formatting to currency / money format 
                    $number = $gd->c_produk_harga_jual;
                    $format = number_format($number, 2, ',', '.');
                    $currencySymbol = $negara->simbol_mata_uang.'. '.$format;
                    $c_produk_nama .= '<strong>'.$currencySymbol.'</strong>';
                }
                $c_produk_nama .= '</td></tr></table>';
                $gd->c_produk_nama = $c_produk_nama;
            }
            if (isset($gd->b_user_nama_seller)) {
                $nama = '<h4 class="tbl-content"><b>'.$gd->b_user_nama_seller.'</b></h4>';
                // if (isset($gd->b_user_telp_reporter)) {
                //     $nama .= '<p class="tbl-content-category"><i class="fa fa-phone"></i> '.$gd->b_user_telp_reporter.'</p>';
                // }
                if (isset($gd->b_user_email_seller)) {
                    // $nama .= '<p class="tbl-content-category"><i class="fa fa-envelope"></i> '.$this->__st($gd->b_user_email_reporter, 30).'</p>';
                    $nama .= '<p class="tbl-content-category"><i class="fa fa-envelope"></i> '.$gd->b_user_email_seller.'</p>';
                }
                if (isset($gd->b_user_address_seller)) {
                    $nama .= '<p class="tbl-content-category"><i class="fa fa-map-marker"></i> '.$gd->b_user_address_seller.'</p>';
                }
                //if(isset($gd->b_user_image)) $nama .= '<img src="'.base_url($gd->b_user_image).'" class="img-responsive img-icon"  />';
                $gd->b_user_nama_seller = $nama;
            }
            // if (isset($gd->b_user_nama_reporter)) {
            //     $nama = '<h4 class="tbl-content"><b>'.$gd->b_user_nama_reporter.'</b></h4>';
            //     // if (isset($gd->b_user_telp_reporter)) {
            //     //     $nama .= '<p class="tbl-content-category"><i class="fa fa-phone"></i> '.$gd->b_user_telp_reporter.'</p>';
            //     // }
            //     if (isset($gd->b_user_email_reporter)) {
            //         // $nama .= '<p class="tbl-content-category"><i class="fa fa-envelope"></i> '.$this->__st($gd->b_user_email_reporter, 30).'</p>';
            //         $nama .= '<p class="tbl-content-category"><i class="fa fa-envelope"></i> '.$gd->b_user_email_reporter.'</p>';
            //     }
            //     if (isset($gd->b_user_address_reporter)) {
            //         $nama .= '<p class="tbl-content-category"><i class="fa fa-map-marker"></i> '.$gd->b_user_address_reporter.'</p>';
            //     }
            //     //if(isset($gd->b_user_image)) $nama .= '<img src="'.base_url($gd->b_user_image).'" class="img-responsive img-icon"  />';
            //     $gd->b_user_nama_reporter = $nama;
            // }

            if (isset($gd->b_user_nama_reporter)) {
                if (isset($gd->b_user_email_reporter)) {
                    $email = $gd->b_user_email_reporter.'<br />';
                }
                if (isset($gd->b_user_address_reporter)) {
                    $address = $gd->b_user_address_reporter.'<br />';
                }

                if(isset($gd->b_user_id)) {
                    if($gd->b_user_id != 0) {
                        $result = '<span style="font-size: 1.2em; font-weight: bolder;">'. $gd->b_user_nama_reporter.'</span><br />'.$email.$address;
                    } else {
                        if(empty($gd->admin_name)) {
                            $result = '<span style="font-size: 1.2em; font-weight: bolder;">Admin</span><br />';
                        } else {
                            $admin_name = strtoupper(str_replace("_", " ", $gd->admin_name));

                            $result = '<span style="font-size: 1.2em; font-weight: bolder;">'.$admin_name.'</span><br />';
                        }
                    }
                }
                $gd->b_user_nama_reporter = $result;
            }

            if (isset($gd->admin_name)) {
                if(empty($gd->admin_name)) {
					$admin_name = "Admin";
				} else {
					$admin_name = strtoupper(str_replace("_", " ", $gd->admin_name));
				}

				$result = '<span style="font-size: 1.2em; font-weight: bolder;">'.$admin_name.'</span><br />';

                $gd->admin_name = $result;
            }

            if(isset($gd->reported_status)) {
                if($gd->reported_status != "takedown") {
                    $gd->reported_status = "reported";
                } else {
                    $gd->reported_status = "takedown";
                }
            }
            // if (isset($gd->harga_jual)) {
            //     $gd->harga_jual = $negara->simbol_mata_uang.''.$gd->harga_jual;
            // }
            // if (isset($gd->action)) {
            //     if ($reported_status == 'takedown') {
            //         $b1 = '<button class="btn btn-alt btn-sm btn-danger btn-disabled disabled">Takedown</button>';
            //     } else {
            //         $b1 = '<button class="btn btn-sm btn-danger">Takedown</button>';
            //     }
            //     if ($reported_status == 'ignore') {
            //         $b2 = '<button class="btn btn-alt btn-sm btn-warning btn-disabled disabled">Ignore</button>';
            //     } else {
            //         $b2 = '<button class="btn btn-sm btn-warning">Ignore</button>';
            //     }
            //     $gd->action = $b1.$b2;
            // }
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
        if (mb_strlen($di['nama'])>1 && strlen($di['sku'])>1) {
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
    public function daftar_laporan($c_produk_id)
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
        $tbl_as = $this->cplm->getTableAlias();
        $tbl2_as = $this->cplm->getTableAlias2();
        $tbl3_as = $this->cplm->getTableAlias3();

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
        $b_kondisi_id = $this->input->post("b_kondisi_id");
        $courier_service = $this->input->post("courier_service");
        $is_include_delivery_cost = $this->input->post("free_ship");
        $produk_status = $this->input->post("produk_status");
        $price_min = $this->input->post("price_min");
        $price_max = $this->input->post("price_max");

        //input validation
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
                $sortCol = "$tbl3_as.fnama";
                break;
            case 1:
                $sortCol = "$tbl_as.kategori";
                break;
            case 2:
                $sortCol = "$tbl_as.kategori_sub";
                break;
            case 3:
                $sortCol = "$tbl_as.deskripsi";
                break;
            case 4:
                $sortCol = "$tbl_as.foto";
                break;
            default:
                $sortCol = "$tbl3_as.fnama";
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

        $dcount = $this->cplm->countByProdukId($nation_code, $c_produk_id, $keyword);
        $ddata = $this->cplm->getByProdukId($nation_code, $c_produk_id, $page, $pagesize, $sortCol, $sortDir, $keyword);

        foreach ($ddata as &$gd) {
            if (isset($gd->foto)) {
                if (strlen($gd->foto)<=10) {
                    $gd->foto = 'media/produk/default.png';
                }
                $gd->foto = '<img src="'.$this->cdn_url($gd->foto).'" class="img-responsive" style="max-width: 128px;"  onerror="this.onerror=null;this.src=\''.$this->cdn_url('media/produk/default.png').'\';"/>';
            }
        }

        //render
        $this->status = 200;
        $this->message = 'Success';
        $this->__jsonDataTable($ddata, $dcount);
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
        $du = array("is_active"=>1);
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
        $du = array("is_active"=>0);
        $res = $this->cpm->update($nation_code, $c_produk_id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
            $c_produk_ids = array($c_produk_id);
            $this->cart->delAllByProdukIds($nation_code, $c_produk_ids);
            $this->dwlm->delAllByProdukIds($nation_code, $c_produk_ids);
        } else {
            $this->status = 188;
            $this->message = 'Failed updating data product';
        }
        $this->__json_out($data);
    }
    public function ignore($c_produk_id)
    {
        $dt = $this->__init();
        $data = array();

        //get current admin
        $pengguna = $dt['sess']->admin;
        $nation_code = $pengguna->nation_code; //get nation_code from current admin

        $c_produk_id = (int) $c_produk_id;
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
        
        $du = array();
        $du['reported_status'] = "ignore";

        $res = $this->cpm->update($nation_code, $produk->id, $du);

        //by Donny Dennison - 2 march 2021 10:52
        //add need action column in dashboard
        //START by Donny Dennison - 2 march 2021 10:52
        
        $res = $this->cplm->update($nation_code, $produk->id, $du);

        //END by Donny Dennison - 2 march 2021 10:52


        if ($res) {
            $this->status = 200;
            $this->message = "Done";
        } else {
            $this->status = 900;
            $this->message = "Failed";
        }
        $this->__json_out($data);
    }

    public function takedown()
    {
        $dt = $this->__init();
        $data = array();

        //get current admin
        $pengguna = $dt['sess']->admin;
        $nation_code = $pengguna->nation_code; //get nation_code from current admin

        $c_produk_id = $this->input->get('c_product_id') ? $this->input->get('c_product_id') : '';
		$b_user_id_reporter = $this->input->get('b_user_id') ? $this->input->get('b_user_id') : '';
		$admin_name = $this->input->get('admin_name') ? $this->input->get('admin_name') : '';

        // $this->debug($b_user_id_reporter);
        // die();

        // $c_produk_id = (int) $c_produk_id;
        // if (empty($c_produk_id)) {
        //     $this->status = 101;
        //     $this->message = "Product ID is not valid";
        //     $this->__json_out($data);
        //     die();
        // }

        $produk = $this->cpm->getById($nation_code, $c_produk_id);
        if (strlen($produk->id) < 0) {
            $this->status = 102;
            $this->message = "Product not found";
            $this->__json_out($data);
            die();
        }

        $pelanggan = $this->bum->getById($nation_code, $produk->b_user_id);

        // $checkUserIdReporter = $this->cplm->getUserIdReporterByProductId($nation_code, $c_produk_id);
        // $this->debug($checkUserIdReporter);
        // die();

        $du = array();

        //by Donny Dennison - 26 october 2020 15:16
        //fix report product notif
        //START by Donny Dennison - 26 october 2020 15:16

        $du['reported_status'] = "takedown";
        $du['is_active'] = "0";
        $du['is_published'] = 0;
        $du['is_visible'] = 0;

        //END by Donny Dennison - 26 october 2020 15:16

        $res = $this->cpm->update($nation_code, $c_produk_id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = "Done";

            //by Donny Dennison - 2 march 2021 10:52
            //add need action column in dashboard
            //START by Donny Dennison - 2 march 2021 10:52

            $du = array();
            $du['reported_status'] = "takedown";
            //$du['cdate'] = "NOW()";
            if($b_user_id_reporter == 0 || $b_user_id_reporter == "0" || empty($b_user_id_reporter)) {
				// $di['b_user_id'] = 0;
				$di['admin_name'] = $admin_name;
			} else {
				$du['b_user_id'] = $b_user_id_reporter;
				$du['admin_name'] = $admin_name;

			}
            $this->cplm->update($nation_code, $c_produk_id, $du);

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
            // not add push notif

            // //by Donny Dennison - 26 october 2020 15:16
            // //fix report product notif
            // //START by Donny Dennison - 26 october 2020 15:16
            // $takedown = $this->cpm->getByIdTakedown($nation_code, $produk->id);

            // $user = $this->bum->getByIdTakedown($nation_code,$takedown->b_user_id);
              
            // $dpe = array();
            // $dpe['nation_code'] = $nation_code;
            // $dpe['b_user_id'] = $takedown->b_user_id;
            // $dpe['id'] = $this->dpem->getLastId($nation_code, $takedown->b_user_id);
            // $dpe['type'] = "product_report";
            // $dpe['judul'] = "Laporkan Produk";
            // $dpe['teks'] = "Produk Anda ".$takedown->nama." telah dihapus oleh Administrator. Harap tinjau produk Anda.";
            // $dpe['gambar'] = 'media/pemberitahuan/productdiscussion.png';
            // $dpe['cdate'] = "NOW()";
            // $extras = new stdClass();
            // $extras->id = $produk->id;
            // $extras->judul = "Laporkan Produk";
            // $extras->teks = "Produk Anda ".$takedown->nama." telah dihapus oleh Administrator. Harap tinjau produk Anda.";

            // $dpe['extras'] = json_encode($extras);
            // $this->dpem->set($dpe);
            
            // //send notif to firebase
            // $device = strtolower($user->device); //jenis device
            // $tokens = array($user->fcm_token); //device token
            // $title = "Laporkan Produk";
            // $message = "Produk Anda ".$this->convertEmoji($takedown->nama)." telah dihapus oleh Administrator. Harap tinjau produk Anda.";
            // $image = '';
            // $type = 'product_report';
            // $payload = new stdClass();
            // $payload->id = $produk->id;
            // $payload->judul = "Laporkan Produk";
            // $payload->teks = '';
            // $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
            
            // $this->seme_log->write("api_admin", 'API_Admin/ProdukReport::takedown __pushNotif: '.json_encode($res));
            
            // //END by Donny Dennison - 26 october 2020 15:16

            // //by Donny Dennison - 22 september 2021 15:01
            // //revamp-profile
            // $this->buwp->delete($nation_code, $produk->id);
            
            // 
            // end not push notif
            //start transaction
			$this->cpm->trans_end();
        } else {
            $this->status = 900;
            $this->message = "Failed";
        }
        $this->__json_out($data);
    }
}
