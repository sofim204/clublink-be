<?php
class Offer_Detail extends JI_Controller {

	public function __construct() {
    	parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'offer_detail_seller';
		$this->current_page = 'offer_detail_seller';
        $this->load("api_admin/b_user_detail_offer_model", 'budom');
	}

	public function index() {
		redir(base_url_admin());
	}

	public function seller($toggle_seller, $b_user_id_seller, $filter_from_date, $filter_to_date) {
		$this->current_parent = 'offer_detail_seller';
		$this->current_page = 'offer_detail_seller';
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }

        // if (!$this->checkPermissionAdmin($this->current_page)) {
        //     redir(base_url_admin('forbidden'));
        //     die();
        // }

        $data['seller_name_offer'] = $this->budom->getUserNameById($b_user_id_seller);
        $data['toggle_seller'] = $toggle_seller;
        $data['id_seller'] = $b_user_id_seller;
        $data['from_date_detail'] = $filter_from_date;
        $data['to_date_detail'] = $filter_to_date;
        $data['today_year_month'] = date("Y-m", strtotime('now'));

        $this->setTitle('Offer Detail Seller'.$this->site_suffix_admin);

		$this->putThemeContent("offer_detail/seller/home_modal",$data);
		$this->putThemeContent("offer_detail/seller/home",$data);
		$this->putJsContent("offer_detail/seller/home_bottom",$data);
        $this->loadLayout('col-2-left', $data);
        $this->render();
	}

	public function buyer($toggle_buyer, $b_user_id_buyer, $filter_from_date, $filter_to_date) {
		$this->current_parent = 'offer_detail_buyer';
		$this->current_page = 'offer_detail_buyer';
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }

        // if (!$this->checkPermissionAdmin($this->current_page)) {
        //     redir(base_url_admin('forbidden'));
        //     die();
        // }

        $data['buyer_name_offer'] = $this->budom->getUserNameById($b_user_id_buyer);
        $data['toggle_buyer'] = $toggle_buyer;
        $data['id_buyer'] = $b_user_id_buyer;
        $data['from_date_detail'] = $filter_from_date;
        $data['to_date_detail'] = $filter_to_date;
        $data['today_year_month'] = date("Y-m", strtotime('now'));

        $this->setTitle('Offer Detail Buyer'.$this->site_suffix_admin);

		$this->putThemeContent("offer_detail/buyer/home_modal",$data);
		$this->putThemeContent("offer_detail/buyer/home",$data);
		$this->putJsContent("offer_detail/buyer/home_bottom",$data);
        $this->loadLayout('col-2-left', $data);
        $this->render();
	}
}