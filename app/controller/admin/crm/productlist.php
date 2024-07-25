<?php
class Productlist extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setTheme('admin');
        $this->current_parent = 'crm';
        $this->current_page = 'crm_productlist';
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
        $data['admin_name'] = $data['sess']->admin->user_alias;

        $this->setTitle('Products '.$this->site_suffix_admin);
        $this->putThemeContent("crm/productlist/home_modal", $data);
        $this->putThemeContent("crm/productlist/home", $data);
        $this->putJsContent("crm/productlist/home_bottom", $data);
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
        //     redir(base_url('crm/productlist/'));
        //     die();
        // }

        //get product data
        $produk = $this->cpm->getById($nation_code, $id);
        if (!isset($produk->id)) {
            redir(base_url('crm/productlist/'));
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
        $data['admin_name'] = $data['sess']->admin->user_alias;

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
            $this->putThemeContent("crm/productlist/detail_automotive", $data);
        } else {
            $this->putThemeContent("crm/productlist/detail", $data);
        }
        //END Rendi Fajrianto - 14 october 2020 17:07

        $this->putJsContent("crm/productlist/detail_bottom", $data);

        $this->loadLayout('col-2-left', $data);
        $this->render();
    }
}
