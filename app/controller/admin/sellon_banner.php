<?php
class Sellon_Banner extends JI_Controller {
	public function __construct(){
		parent::__construct();
		$this->setTheme('admin');
		$this->load("api_admin/b_user_model", "bu_model");
	}

	public function index() {

	}

    // webview for description of event banner
	public function get_grandprize($language_code, $user_id="")
    {
        $data = $this->__init();
		$nation_code = 62;

		if(!empty($user_id)) {
			$pelanggan = $this->bu_model->getById($nation_code, $user_id);

			if($pelanggan->language_id == 2) {
				$language_code = "id";
			} else {
				$language_code = "en";
			}

			$environment = $this->site_environment;
	
			if($environment == 'production') {
				$url = 'https://prd-bcfe.sellon.net/';
			} else if($environment == 'development') { 
				$url = 'https://stg-bcfe.sellon.net/';
			} else {
				$url = 'http://localhost:8000/';
			}
	
			// $data['urlToWallet'] = $url.'redirectUrl?userWalletCode='.$pelanggan->user_wallet_code_new.'&LanguageIsoCode='.$language_code.'&page=voucher';
			$data['urlToWallet'] = 'https://sellon.net/claim_reward';

			if(strtolower($language_code) == "id") {
				$this->setTitle("Syarat & Ketentuan". $this->site_suffix_admin);
				$this->putThemeContent("sellon_banner/get_grandprize/home_id", $data);
			} else if(strtolower($language_code) == "en") {
				$this->setTitle("Terms & Conditions". $this->site_suffix_admin);
				$this->putThemeContent("sellon_banner/get_grandprize/home_en", $data);
			}
		} else {
			// $data['urlToWallet'] = '#';
			$data['urlToWallet'] = 'https://sellon.net/claim_reward';
			$this->setTitle("Syarat & Ketentuan". $this->site_suffix_admin);
			$this->putThemeContent("sellon_banner/get_grandprize/home_id", $data);
		}

		$this->loadLayout('col-2-left-faqtnc', $data);
		$this->render();
	}

	public function airdrop_event($language_code, $user_id="")
    {
        $data = $this->__init();
		$nation_code = 62;

		if(!empty($user_id)) {
			$pelanggan = $this->bu_model->getById($nation_code, $user_id);

			if($pelanggan->language_id == 2) {
				$language_code = "id";
			} else {
				$language_code = "en";
			}

			$environment = $this->site_environment;
	
			if($environment == 'production') {
				$url = 'https://prd-bcfe.sellon.net/';
			} else if($environment == 'development') { 
				$url = 'https://stg-bcfe.sellon.net/';
			} else {
				$url = 'http://localhost:8000/';
			}
	
			// $data['urlToWallet'] = $url.'redirectUrl?userWalletCode='.$pelanggan->user_wallet_code_new.'&LanguageIsoCode='.$language_code.'&page=voucher';
			$data['urlToWallet'] = 'https://sellon.net/claim_reward';

			if(strtolower($language_code) == "id") {
				$this->setTitle("Syarat & Ketentuan". $this->site_suffix_admin);
				$this->putThemeContent("sellon_banner/airdrop_event/home_id", $data);
			} else if(strtolower($language_code) == "en") {
				$this->setTitle("Terms & Conditions". $this->site_suffix_admin);
				$this->putThemeContent("sellon_banner/airdrop_event/home_en", $data);
			}
		} else {
			$data['urlToWallet'] = 'https://sellon.net/claim_reward';
			$this->setTitle("Syarat & Ketentuan". $this->site_suffix_admin);
			$this->putThemeContent("sellon_banner/airdrop_event/home_id", $data);
		}
		
		$this->loadLayout('col-2-left-faqtnc', $data);
		$this->render();
	}

	public function airdrop_event_bb() {
		$data = $this->__init();

		$data['urlToWallet'] = 'https://sellon.net/claim_reward';
		$this->setTitle("Terms & Conditions". $this->site_suffix_admin);
		$this->putThemeContent("sellon_banner/airdrop_event_bb/home_en", $data);
		
		$this->loadLayout('col-2-left-babyboom', $data);
		$this->render();
	}

	public function get_grandprize_bb() {
		$data = $this->__init();

		$data['urlToWallet'] = 'https://sellon.net/claim_reward';
		$this->setTitle("Terms & Conditions". $this->site_suffix_admin);
		$this->putThemeContent("sellon_banner/get_grandprize_bb/home_en", $data);
		
		$this->loadLayout('col-2-left-babyboom', $data);
		$this->render();
	}

	public function airdrop_event_babyboom() {
		$data = $this->__init();

		$data['urlToWallet'] = 'https://bbt.babyboomtoken.com/claim_reward';
		$this->setTitle("Terms & Conditions". $this->site_suffix_admin);
		$this->putThemeContent("sellon_banner/airdrop_event_bb/home_en", $data);
		
		$this->loadLayout('col-2-left-babyboom', $data);
		$this->render();
	}

	public function get_grandprize_babyboom() {
		$data = $this->__init();

		$data['urlToWallet'] = 'https://bbt.babyboomtoken.com/claim_reward';
		$this->setTitle("Terms & Conditions". $this->site_suffix_admin);
		$this->putThemeContent("sellon_banner/get_grandprize_bb/home_en", $data);
		
		$this->loadLayout('col-2-left-babyboom', $data);
		$this->render();
	}
}