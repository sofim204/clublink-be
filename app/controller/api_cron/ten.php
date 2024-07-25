<?php
// class Ten extends JI_Controller{
//   var $is_log = 1;

//   public function __construct(){
//     parent::__construct();
//     $this->lib("seme_log");
//     $this->lib("seme_email");
//     $this->load("api_cron/b_user_model","bu");
//     $this->load("api_cron/b_user_setting_model","busm");
//     $this->load("api_cron/d_cart_model","cart");
//   }
//   public function index(){

//   }
//   public function cart(){
//     $carts = $this->cart->getInCart();
//     if(count($carts)>0){
//       if($this->is_log) $this->seme_log->write("api_cron", 'api_cron/Ten::test() Run! ');
//       foreach($carts as $cart){
//         //get notification config for buyer
//         $setting_value = 1;
//         $classified = 'setting_notification_buyer';
//         $notif_code = 'B0';
//         $notif_cfg = $this->busm->getValue($cart->nation_code,$cart->b_user_id_buyer,$classified,$notif_code);
//         if(isset($notif_cfg->setting_value)) $setting_value = (int) $notif_cfg->setting_value;
//         if($this->is_log) $this->seme_log->write("api_cron", "api_cron/Ten::test() Run! -> b_user_id_buyer: $cart->b_user_id_buyer, Notif enable: $setting_value");

//         //push notif for buyer
//         if(strlen($cart->b_user_fcm_token_buyer) > 50 && !empty($setting_value)){
//           $device = $cart->b_user_device_buyer;
//           $tokens = array($cart->b_user_fcm_token_buyer);
//           $title = 'Produk dalam keranjang';
//           $message = "$cart->total produk menunggu di keranjang Anda.";
//           $type = 'promotion';
//           $image = 'media/pemberitahuan/promotion.png';
//           $payload = new stdClass();
//           $payload->id_produk = null;
//           $payload->id_order = null;
//           $payload->b_user_id_buyer = $cart->b_user_id_buyer;
//           $payload->b_user_fnama_buyer = $cart->b_user_fnama_buyer;
          
//           // by Muhammad Sofi - 27 October 2021 10:12
// 					// if user img & banner not exist or empty, change to default image
// 					// $payload->b_user_image_buyer = $this->cdn_url($cart->b_user_image_buyer);
// 					if(file_exists(SENEROOT.$cart->b_user_image_buyer) && $cart->b_user_image_buyer != 'media/user/default.png'){
// 						$payload->b_user_image_buyer = $this->cdn_url($cart->b_user_image_buyer);
// 					} else {
// 						$payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
// 					}
//           $payload->b_user_id_seller = null;
//           $payload->b_user_fnama_seller = null;
          
//           // by Muhammad Sofi - 28 October 2021 11:00
// 					// if user img & banner not exist or empty, change to default image
//           // $payload->b_user_image_seller = null;
//           $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
//           $res = $this->__pushNotif($device,$tokens,$title,$message,$type,$image,$payload);
//           if($this->is_log) $this->seme_log->write("api_cron", 'api_cron/Ten::test() __pushNotif(): '.json_encode($res));
//         }
//       }
//     }
//   }
// }
