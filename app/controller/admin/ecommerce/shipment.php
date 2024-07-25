<?php
class Shipment extends JI_Controller{
	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_shipment';
		$this->load("admin/b_user_model","bum");
		$this->load("admin/b_user_alamat_model","buam");
		$this->load("admin/c_produk_model","cpm");
		$this->load("admin/d_order_model","dom");
		$this->load("admin/d_order_alamat_model","doam");
		$this->load("admin/d_order_detail_model","dodm");
		$this->load("admin/d_order_proses_model","dopm");
		$this->load("admin/e_complain_model","ecm");
		$this->load("admin/e_rating_model","erm");
	}
	protected function __toStars($rating){
		$str = '';
		for($rti=1;$rti<=5;$rti++){
			if($rating<=$rti){
				$str .= '<i class="fa fa-star-o"></i>';
			}else{
				$str .= '<i class="fa fa-star"></i>';
			}
		}
		return $str;
	}
 	 public function index(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$nation_code = $data['sess']->admin->nation_code;

		$this->setTitle('Shipment Process '.$this->site_suffix_admin);
		$this->putThemeContent("ecommerce/shipment/home_modal",$data);
		$this->putThemeContent("ecommerce/shipment/home",$data);
		$this->putJsContent("ecommerce/shipment/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
  	}

  	public function detail($d_order_id="",$c_produk_id=""){
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_shipment';

		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}
		
		$nation_code = $data['sess']->admin->nation_code;

		//get order
		$order = $this->dom->getById($nation_code,$d_order_id);
		if(!isset($order->id)){
			redir(base_url_admin('ecommerce/shipment/'));
			die();
		}

		//get order detail
		$detail = $this->dodm->getDetailByOrderId($nation_code,$d_order_id,$c_produk_id);
		if(!isset($order->id)){
			redir(base_url_admin('ecommerce/shipment/'));
			die();
		}

		//get product
		$produk = $this->cpm->getById($nation_code,$c_produk_id);
		if(!isset($produk->id)){
			redir(base_url_admin('ecommerce/shipment/'));
			die();
		}

		//get seller rating
		$seller_rating = 0;
		$rating_object = $this->erm->getSellerStats($nation_code,$produk->b_user_id);
		if(isset($rating_object->rating_count) && isset($rating_object->rating_count)){
			$seller_rating = floor($rating_object->rating_total/$rating_object->rating_count);
		}

		//get buyer rating
		$buyer_rating = 0;
		$rating_object = $this->erm->getBuyerStats($nation_code,$order->b_user_id);
		if(isset($rating_object->rating_count) && isset($rating_object->rating_count)){
			$buyer_rating = floor($rating_object->rating_total/$rating_object->rating_count);
		}
		unset($rating_object);

		//put on order
		$order->detail = $detail;
		$order->pickup = $this->buam->getDetailByUserIdAndId($nation_code,$produk->b_user_id,$produk->b_user_alamat_id);
		$order->billing = $this->doam->getBillingByOrderId($nation_code,$order->id);
		$order->shipping = $this->doam->getShippingByOrderId($nation_code,$order->id);
		$order->proses = $this->dopm->getDetailByID($nation_code,$order->id,$produk->id);

		//validate address
		if(!isset($order->pickup->penerima_nama)){
			redir(base_url_admin('ecommerce/shipment/'));
			die();
		}

		//get buyer data
		$buyer = $this->bum->getById($nation_code,$order->b_user_id);
		$buyer->rating = $buyer_rating;

		//get seller data
		$seller = $this->bum->getById($nation_code,$produk->b_user_id);
		$seller->address = $order->pickup;
		$seller->rating = $seller_rating;

		//put to view
		$data['nation_code'] = $nation_code;
		$data['order'] = $order;
		$data['produk'] = $produk;
		$data['buyer'] = $buyer;
		$data['seller'] = $seller;
		$data['rating'] = $this->erm->getDetailByID($nation_code,$order->id,$order->b_user_id,$produk->b_user_id);
		$data['order_status'] = $this->__getOrderStatus($order->order_status,$order->payment_status,$order->detail->seller_status,$order->detail->shipment_status,$order->detail->buyer_status);
		$data['complain'] = $this->ecm->getDetailByID($nation_code,$order->id,$order->b_user_id,$produk->b_user_id);

		$this->setTitle('Shipment Detail '.$this->site_suffix_admin);
		$this->putThemeContent("ecommerce/shipment/detail_modal",$data);
		$this->putThemeContent("ecommerce/shipment/detail",$data);
		$this->putJsContent("ecommerce/shipment/detail_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
  	}
}
