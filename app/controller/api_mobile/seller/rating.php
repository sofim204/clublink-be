<?php
//seller
class Rating extends JI_Controller{
	var $is_log=1;
	public function __construct(){
		parent::__construct();
		$this->lib("seme_log");
    $this->load("api_mobile/b_user_model","bu");
    $this->load("api_mobile/d_order_model","order");
		$this->load("api_mobile/d_order_model","dom");
    $this->load("api_mobile/d_order_detail_model","dodm");
    $this->load("api_mobile/e_rating_model","erm");
	}
  public function index(){
    http_response_code("404");
    echo 'Not found';
  }
	public function detail($d_order_id,$d_order_detail_id){
		//initial
		$dt = $this->__init();
		$data = array();
		$data['rating'] = new stdClass();
    $data['rating']->buyer = new stdClass();
    $data['rating']->seller = new stdClass();

    //check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
      $this->status = 101;
  		$this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_rating");
      die();
    }

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_rating");
			die();
		}

		//check apisess
		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if(!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_rating");
			die();
		}

		$d_order_id = (int) $d_order_id;
		if($d_order_id<=0){
			$this->status = 8700;
			$this->message = 'Invalid Order ID';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_rating");
			die();
		}

		$d_order_detail_id = (int) $d_order_detail_id;
		if($d_order_detail_id<=0){
			$this->status = 8701;
			$this->message = 'Invalid Order Detail ID';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_rating");
			die();
		}

    //get last rating
    $rating = $this->erm->getByOrderIdSellerId($nation_code,$d_order_id,$d_order_detail_id,$pelanggan->id);
		$data = array();
		$data['rating'] = new stdClass();
		$data['rating']->seller = new stdClass();
		$data['rating']->seller->rating_value = 0;
		$data['rating']->buyer = new stdClass();
		$data['rating']->buyer->rating_value = 0;
		if(isset($rating->buyer_rating)) $data['rating']->buyer->rating_value = (int) $rating->buyer_rating;
		if(isset($rating->seller_rating)) $data['rating']->seller->rating_value = (int) $rating->seller_rating;

		//rating
  	$this->status = 200;
  	$this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_rating");
	}

	public function create(){
		//initial
		$dt = $this->__init();
		$data = array();
		$data['rating'] = new stdClass();
    $data['rating']->buyer = new stdClass();
    $data['rating']->seller = new stdClass();
		
		//check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
      $this->status = 101;
  		$this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_rating");
      die();
    }

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_rating");
			die();
		}

		//check apisess
		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if(!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_rating");
			die();
		}

		if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/seller/Rating::create -> BUID: ".$pelanggan->id." POST: ".json_encode($_POST));

		$d_order_id = (int) $this->input->post("d_order_id");
		if($d_order_id<=0){
			$this->status = 8700;
			$this->message = 'Invalid Order ID';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_rating");
			die();
		}

		$d_order_detail_id = (int) $this->input->post("d_order_detail_id");
		if($d_order_detail_id<=0){
			$this->status = 8701;
			$this->message = 'Invalid Order Detail ID';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_rating");
			die();
		}

		$order = $this->dom->getById($nation_code,$d_order_id);
		if(!isset($order->id)){
			$this->status = 8713;
			$this->message = 'Order with supplied ID not found';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_rating");
			die();
		}
		

		$detail = $this->dodm->getById($nation_code,$d_order_id,$d_order_detail_id);
		if(!isset($detail->id)){
			$this->status = 8714;
			$this->message = 'Order Detail with supplied ID not found';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_rating");
			die();
		}

		if($detail->b_user_id_seller != $pelanggan->id){
			$this->status = 8715;
			$this->message = 'Sorry this order ID not belong to you';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_rating");
			die();
		}
		$buyer = $this->bu->getById($nation_code,$order->b_user_id);

    //set rating from buyer
    $rating_from = 'seller';

    //check rating if already exists
    $rating = $this->erm->check($nation_code,$d_order_id,$d_order_detail_id,$pelanggan->id,$buyer->id,$rating_from);
    if(!isset($rating->d_order_id)){
			$this->erm->create($nation_code,$d_order_id,$d_order_detail_id,$pelanggan->id,$buyer->id,0,0);
			$rating = $this->erm->check($nation_code,$d_order_id,$d_order_detail_id,$pelanggan->id,$buyer->id,$rating_from);
    }
    //collect input
    $rating_value = (int) $this->input->post("rating_value");

    //validation
    if($rating_value<=0){
			$this->status = 8716;

			//by Donny Dennison 17 august 2020 15:21
			//change response text
			// $this->message = 'rating_value must be more than equal to 1';
			$this->message = 'Please give him/her a rate';

			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_rating");
			die();
    }
    if($rating_value>5){
			$this->status = 8717;

			//by Donny Dennison 17 august 2020 15:21
			//change response text
			// $this->message = 'rating_value must be less than equal to 5';
			$this->message = 'You can\'t give him/her more than 5 stars';
			
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_rating");
			die();
    }

    //insert to table e_rating
    $du = array();
    $du['buyer_rating'] = $rating_value; //from seller to buyer, fill buyer_rating. CROSS method
    $res = $this->erm->update($nation_code,$d_order_id,$d_order_detail_id,$pelanggan->id,$buyer->id,$du);
    if($res){
  		$this->status = 200;
  		$this->message = 'Success';
    }else{
  		$this->status = 8718;
  		$this->message = 'Failed to add order rating right now, please try again later';
    }

    //get last rating
    $rating = $this->erm->getByOrderIdSellerId($nation_code,$d_order_id,$d_order_detail_id,$pelanggan->id);
		$data = array();
		$data['rating'] = new stdClass();
		$data['rating']->seller = new stdClass();
		$data['rating']->seller->rating_value = 0;
		$data['rating']->buyer = new stdClass();
		$data['rating']->buyer->rating_value = 0;
		if(isset($rating->buyer_rating)) $data['rating']->buyer->rating_value = (int) $rating->buyer_rating;
		if(isset($rating->seller_rating)) $data['rating']->seller->rating_value = (int) $rating->seller_rating;
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_rating");
	}
}
