<?php
class Produk extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setTheme('admin');
        $this->current_parent = 'ecommerce';
        $this->current_page = 'ecommerce_produk';
        $this->load("api_admin/a_negara_model", "anm");
        $this->load("api_admin/b_kategori_model2", "bkm2");
        $this->load("admin/b_kategori_model4", "bkm4");
        $this->load("admin/b_berat_model", "bberm");
        $this->load("admin/b_kondisi_model", "bkonm");
        $this->load("admin/b_user_model", "bum");
        $this->load("admin/b_user_alamat_model", "buam");
        $this->load('admin/c_produk_model', 'cpm');
        $this->load('admin/c_produk_foto_model', 'cpfm');
        $this->load('admin/c_produk_detail_automotive_model', 'cpdam');
        $this->load('admin/e_rating_model', 'erm');
    }

    protected function __toStars($rating)
    {
        $str = '';
        for ($rti=0; $rti<5; $rti++) {
            if ($rating<=$rti) {
                $str .= '<i class="fa fa-star-o"></i>';
            } else {
                $str .= '<i class="fa fa-star"></i>';
            }
        }
        return $str;
    }

    private function __convertToEmoji($text){
		$value = $text;
		$readTextWithEmoji = preg_replace("/\\\\u([0-9A-F]{2,5})/i", "&#x$1;", $value);
		return $readTextWithEmoji;
	}

    public function index()
    {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }

        if (!$this->checkPermissionAdmin($this->current_page)) {
            redir(base_url_admin('forbidden'));
            die();
        }

        $this->setKey($data['sess']);
        $pengguna = $data['sess']->admin;
        $nation_code = $pengguna->nation_code;

        //get country data
        $negara = $this->anm->getByNationCode($nation_code);
        $data['negara'] = $negara;

        //get list of product condition
        $data['kategori_list'] = $this->bkm4->getActive($nation_code);
        $data['kondisi_list'] = $this->bkonm->getActive($nation_code);
		$data['user_role'] = $data['sess']->admin->user_role;

        $this->setTitle('Products '.$this->site_suffix_admin);
        $this->putThemeContent("ecommerce/produk/home_modal", $data);
        $this->putThemeContent("ecommerce/produk/home", $data);
        $this->putJsContent("ecommerce/produk/home_bottom", $data);
        $this->loadLayout('col-2-left', $data);
        $this->render();
    }
    
    public function tambah()
    {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }

        if (!$this->checkPermissionAdmin($this->current_page)) {
            redir(base_url_admin('forbidden'));
            die();
        }

        $this->setKey($data['sess']);

        $pengguna = $data['sess']->admin;
        $nation_code = $pengguna->nation_code;
        $negara = $this->anm->getByNationCode($nation_code);
        $data['negara'] = $negara;

        $this->setTitle('Tambah Produk Ecommerce '.$this->site_suffix_admin);
        $this->setTitle('New Products '.$this->site_suffix_admin);
        $cats = array();
        $cat = array();
        $kategories = array();//$this->bkm2->getKategori();
        foreach ($kategories as $kategori) {
            if ($kategori->b_kategori_id == "-" || $kategori->utype=="kategori") {
                $cats[$kategori->id] = $kategori;
                $cats[$kategori->id]->childs = array();
            } elseif ($kategori->utype=="kategori_sub") {
                if (!isset($cats[$kategori->b_kategori_id])) {
                    $cats[$kategori->b_kategori_id] = new stdClass();
                    $cats[$kategori->b_kategori_id]->childs = array();
                }
                if (!isset($cats[$kategori->b_kategori_id]->childs[$kategori->id])) {
                    $cats[$kategori->b_kategori_id]->childs[$kategori->id] = new stdClass();
                }

                $cats[$kategori->b_kategori_id]->childs[$kategori->id] = $kategori;
            } else {
                $cat[$kategori->id] = $kategori;
            }
        }

        //$this->debug($cats);
        //die();
        $data['kategori'] = $cats;
        $data['kondisi'] = $this->bkonm->get();
        $data['berat'] = $this->bberm->get();

        $this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));
        $this->putJsFooter(base_url('assets/js/jquery.priceformat.min'));

        $this->putThemeContent("ecommerce/produk/tambah_modal", $data);
        $this->putThemeContent("ecommerce/produk/tambah", $data);

        $this->putJsContent("ecommerce/produk/tambah_bottom", $data);
        $this->loadLayout('col-2-left', $data);
        $this->render();
    }

    public function edit($id)
    {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }

        if (!$this->checkPermissionAdmin($this->current_page)) {
            redir(base_url_admin('forbidden'));
            die();
        }

        $id = (int) $id;
        if ($id<=0) {
            redir(base_url_admin('ecommerce/produk'));
            die();
        }
        $this->setKey($data['sess']);
        $pengguna = $data['sess']->admin;
        $nation_code = $pengguna->nation_code;
        $negara = $this->anm->getByNationCode($nation_code);
        $data['negara'] = $negara;

        $data['produk'] = $this->cpm->getById($nation_code, $id);
        if (!isset($data['produk']->id)) {
            redir(base_url('ecommerce/produk/'));
            die();
        }
        //kategori list
        $cats = array();
        $cat = array();
        $kategories = $this->bkm2->getKategori($nation_code);
        foreach ($kategories as $kategori) {
            if ($kategori->parent_b_kategori_id == "-" || $kategori->utype=="kategori") {
                $cats[$kategori->id] = $kategori;
                $cats[$kategori->id]->childs = array();
            } elseif ($kategori->utype=="kategori_sub") {
                if (!isset($cats[$kategori->parent_b_kategori_id])) {
                    $cats[$kategori->parent_b_kategori_id] = new stdClass();
                    $cats[$kategori->parent_b_kategori_id]->childs = array();
                }
                if (!isset($cats[$kategori->parent_b_kategori_id]->childs[$kategori->id])) {
                    $cats[$kategori->parent_b_kategori_id]->childs[$kategori->id] = new stdClass();
                }

                $cats[$kategori->parent_b_kategori_id]->childs[$kategori->id] = $kategori;
            } else {
                $cat[$kategori->id] = $kategori;
            }
        }
        //$this->debug($data['produk']);
        //die();
        $data['kategori'] = $cats;
        $data['kondisi'] = $this->bkonm->get($nation_code);
        $data['berat'] = $this->bberm->get($nation_code);
        $data['user'] = $this->bum->getById($nation_code, $data['produk']->b_user_id);
        if (!isset($data['user']->id)) {
            $data['user'] = new stdClass();
            $data['user']->id = "null";
            $data['user']->fnama = "-";
        }
        $data['alamat'] = $this->buam->getByUserId($nation_code, $data['produk']->b_user_id);

        //$this->debug($data['produk']);
        //die();
        //handled by API
        //$data['produk']->fotos = $this->cpfm->getByProdukId($nation_code, $data['produk']->id);

        $this->setTitle('Edit '.$data['produk']->nama.' '.$this->site_suffix_admin);
        $this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));
        $this->putJsFooter(base_url('assets/js/jquery.priceformat.min'));

        $this->putThemeContent("ecommerce/produk/edit_modal", $data);
        $this->putThemeContent("ecommerce/produk/edit", $data);


        $this->putJsContent("ecommerce/produk/edit_bottom", $data);
        $this->loadLayout('col-2-left', $data);
        $this->render();
    }
        
    /**
     * View product detail
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function detail($id)
    {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }

        if (!$this->checkPermissionAdmin($this->current_page)) {
            redir(base_url_admin('forbidden'));
            die();
        }
    
        $this->setKey($data['sess']); //extending session time

        //get current admin
        $pengguna = $data['sess']->admin;
        $nation_code = $pengguna->nation_code; //get nation_code from current admin

        //validate produk ID
        // $id = (int) $id; // cast as integer
        // if ($id<=0) {
        //     redir(base_url('ecommerce/produk/'));
        //     die();
        // }

        //get product data
        $produk = $this->cpm->getById($nation_code, $id);
        if (!isset($produk->id)) {
            redir(base_url('ecommerce/produk/'));
            die();
        }

        //get kategori data
        $data['kategori'] = $this->bkm4->getById($nation_code, $produk->b_kategori_id);
        $data['kondisi'] = $this->bkonm->getById($nation_code, $produk->b_kondisi_id);

        //handling if missing or null
        if (!isset($data['kategori']->nama)) {
            $data['kategori']->nama = '-';
        }
        if (!isset($data['kategori']->image_icon)) {
            $data['kategori']->image_icon = 'media/icon/default-icon.png';
        }
        $data['sub_kategori'] = (object)['nama' => ''];
        if($data['kategori']->utype=='kategori_sub'){
            $data['sub_kategori'] = $data['kategori'];
            $data['kategori'] = $this->bkm4->getById($nation_code, $data['sub_kategori']->parent_b_kategori_id);
        }
        //get user data (seller)
        $data['user'] = $this->bum->getById($nation_code, $produk->b_user_id);
        // $data['user']->rating = 0;
        // $seller_rating = 0;
        // $rating_object = $this->erm->getSellerStats($nation_code, $produk->b_user_id);
        // if (isset($rating_object->rating_count) && isset($rating_object->rating_count)) {
        //     $data['user']->rating = 0;
        //     if (!empty($rating_object->rating_count)) {
        //         $data['user']->rating = floor($rating_object->rating_total/$rating_object->rating_count);
        //     }
        // }

        //get pickup address
        $data['alamat'] = $this->buam->getById($nation_code, $produk->b_user_id, $produk->b_user_alamat_id);

        $this->setTitle('Detail: '.$this->__convertToEmoji($produk->nama).' '.$this->site_suffix_admin);
        $produk->fotos = $this->cpfm->getByProdukId($nation_code, $produk->id);
        $produk->detail_automotive = $this->cpdam->getByProdukId($nation_code, $produk->id);

        $produk->videos = $this->cpfm->getVideoByProdukId($nation_code, $produk->id);
        $data['total_video'] = $this->cpfm->getTotalUploadVideo($nation_code, $produk->id, "notlike");
        // $data['total_uploading_image'] = $this->cpfm->getTotalUploadingImage($nation_code, $produk->id, "like");
        $data['total_uploading_image'] = $this->cpfm->getTotalUploadVideo($nation_code, $produk->id, "like");
    
        $data['produk'] = $produk;
        $negara = $this->anm->getByNationCode($nation_code);

        if (!isset($data['produk']->harga_jual)) {
            $data['produk']->harga_jual = '-';
        } else {
            $number = $data['produk']->harga_jual;
            $format = number_format($number, 2, ',', '.');
            $currencySymbol = $negara->simbol_mata_uang.'. '.$format;
            $data['produk']->harga_jual = $currencySymbol;
        }

        if(isset($produk->nama)) {
            $produk->nama = $this->__convertToEmoji($produk->nama);
        }

        $data['seller_review'] = $this->cpm->getSellerReview($produk->id, "buyer");

        //by Rendi Fajrianto - 14 october 2020 17:07
        //automotive detail
        //START Rendi Fajrianto - 14 october 2020 17:07
        // by Muhammad Sofi - 17 November 2021 11:46 | fixing on product detail
        if($data['kategori']->id == 32 || $data['kategori']->id == 33) {
            $this->putThemeContent("ecommerce/produk/detail_automotive", $data);
        } else {
            $this->putThemeContent("ecommerce/produk/detail", $data);
        }
        //END Rendi Fajrianto - 14 october 2020 17:07

        $this->putJsContent("ecommerce/produk/detail_bottom", $data);

        $this->loadLayout('col-2-left', $data);
        $this->render();
    }

    public function upload(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$pengguna = $data['sess']->admin;
		
		$this->setTitle('Upload Product '.$this->site_suffix_admin);

        // $seller = $this->bum->getAll();
        // $data['seller'] = $seller;

		//$this->debug($cats);
		//die();

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("ecommerce/produk/upload_modal",$data);
		$this->putThemeContent("ecommerce/produk/upload",$data);


		$this->putJsContent("ecommerce/produk/upload_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
