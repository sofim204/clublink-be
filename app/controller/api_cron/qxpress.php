<?php
// class qxpress extends JI_Controller
// {
//     public $send_email = 0;
//     public $is_log = 1;
//     public $is_push = 1;

//     public function __construct()
//     {
//         parent::__construct();
//         // $this->lib("seme_log");
//         $this->lib("seme_email");
//         $this->load("api_cron/a_notification_model", "anot");
//         $this->load("api_cron/b_user_model", "bu");
//         $this->load("api_cron/b_user_setting_model", "busm");
//         $this->load("api_cron/c_produk_model", "cpm");
//         $this->load("api_cron/d_cart_model", "cart");
//         $this->load("api_cron/d_order_model", "order");
//         $this->load("api_cron/d_order_detail_model", "dodm");
//         $this->load("api_cron/d_order_proses_model", "dopm");
//         $this->load("api_cron/d_pemberitahuan_model", "dpem"); //notification list
//         $this->load("api_cron/d_order_detail_item_model", "dodim");
//         $this->load("api_cron/d_order_detail_pickup_model", "dodpm");
//     }
//     public function index()
//     {   
//         $this->order->trans_start();    
//         // $this->seme_log->changeFilename("seme.log");

//         $a=$this->dodm->getQxpressProsess();
//         echo "<pre>";
//         print_r($a);
//         echo "</pre>";
//         foreach ($a as $key) {
//             $qty=$this->dodm->getQxpressProsessNgr($key->b_user_id_seller);
//             //print_r(count($qty));
//             $time=11;
//             $rq2 = $this->__createQXpressPickup($key, count($qty),$time);
//             //print_r($rq2);
//             //put on log
//             $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::print -> __createQXpressPickup: ".($rq2));
//             //decode result
//             // $pudt = json_decode($rq2);
//             $pudt = @simplexml_load_string($rq2);
//             //parse XML success
//             if (!is_object($pudt)) {
//                 $pudt = new stdClass();
//             }
//             echo"1";
//             echo "<pre>";
//             print_r($pudt);
//             echo "</pre>";
//             echo"</br>";
//             if (isset($pudt->ResultCode)) {
//                 for ($i=0; $i < 10; $i--) { 
//                     if ($pudt->ResultCode == 0) {
//                         foreach ($qty as $qxpick) {
//                             //collect array notification list
//                             $this->seme_log->write("api_cron", "API_Cronn/seller/WayBill::print QXpress Pickup Order done");
//                             //update pickup date, to tommorow
//                             $dx = array();
//                             $dx["pickup_date"] = date("Y-m-d H:i:s", strtotime("+".$time." Hour"));
//                             $dx["delivery_date"] = $dx["pickup_date"];
//                             $this->dodm->update($qxpick->nation_code, $qxpick->d_order_id, $qxpick->c_produk_id, $dx);
//                             $this->order->trans_commit();
//                             $qxpick->delivery_date = $dx["delivery_date"];
//                             $qxpick->pickup_date = $dx["pickup_date"];
        
//                             // add to order proses with current status
//                             $ops = array();
//                             $ops['nation_code'] = $qxpick->nation_code;
//                             $ops['d_order_id'] = $qxpick->d_order_id;
//                             $ops['c_produk_id'] = $qxpick->c_produk_id;
//                             $ops['id'] = $this->dopm->getLastId($qxpick->nation_code, $qxpick->d_order_id, $qxpick->c_produk_id);
//                             $ops['initiator'] = "Seller";
//                             $ops['nama'] = "Pickup Requested";
//                             $ops['deskripsi'] = "Your order $qxpick->nama ($qxpick->invoice_code) has been added to the QXpress: Next Day pickup queue list";
//                             $ops['cdate'] = "NOW()";
//                             $this->dopm->set($ops);
//                             $this->order->trans_commit();
//                             # code...
//                         }
//                         break;
//                     } elseif($pudt->ResultCode == "-113" or $pudt->ResultCode == "-210") {
//                         $time=$time+24;
//                         $rq2 = $this->__createQXpressPickup($key, count($qty),$time);
//                         $pudt = @simplexml_load_string($rq2);
//                         if (!is_object($pudt)) {
//                             $pudt = new stdClass();
//                         }
//                         echo"2";
//                         echo "<pre>";
//                         print_r($pudt);
//                         echo "</pre>";
//                         echo"</br>";
//                         if (isset($pudt->ResultCode)) {
//                             if ($pudt->ResultCode == 0) {
//                                 foreach ($qty as $qxpick) {
//                                     //collect array notification list
//                                     $this->seme_log->write("api_cron", "API_Cronn/seller/WayBill::print QXpress Pickup Order done");
//                                     //update pickup date, to tommorow
//                                     $dx = array();
//                                     $dx["pickup_date"] = date("Y-m-d H:i:s", strtotime("+".$time." hour"));
//                                     $dx["delivery_date"] = $dx["pickup_date"];
//                                     $this->dodm->update($qxpick->nation_code, $qxpick->d_order_id, $qxpick->c_produk_id, $dx);
//                                     $this->order->trans_commit();
//                                     $qxpick->delivery_date = $dx["delivery_date"];
//                                     $qxpick->pickup_date = $dx["pickup_date"];
                
//                                     // add to order proses with current status
//                                     $ops = array();
//                                     $ops['nation_code'] = $qxpick->nation_code;
//                                     $ops['d_order_id'] = $qxpick->d_order_id;
//                                     $ops['c_produk_id'] = $qxpick->c_produk_id;
//                                     $ops['id'] = $this->dopm->getLastId($qxpick->nation_code, $qxpick->d_order_id, $qxpick->c_produk_id);
//                                     $ops['initiator'] = "Seller";
//                                     $ops['nama'] = "Pickup Requested";
//                                     $ops['deskripsi'] = "Your order $qxpick->nama ($qxpick->invoice_code) has been added to the QXpress: Next Day pickup queue list";
//                                     $ops['cdate'] = "NOW()";
//                                     $this->dopm->set($ops);
//                                     $this->order->trans_commit();
//                                 }
//                                 break;
//                             }elseif($pudt->ResultCode == "-113" or $pudt->ResultCode == "-210"){
//                                 continue;
//                             }else{
//                                 break;
//                             }
//                         } else {
//                             break;
//                             //notif to seller for deliver their product manually, collect array notification list
//                         }
//                     }else{
//                         break;
//                     }   
//                 }
//             } else {
                
//                 //notif to seller for deliver their product manually, collect array notification list
//             }
//         }
//     }


//     private function __createQXpressPickup($order,$qty,$time)
//     {
//         //if (strlen($order->shipment_tranid)>4) {
//         //  $refOrderNo = $order->shipment_tranid;
//         //} else {
        
//         //by Donny Dennison - 11 july 2020 0:23
//         //change reference number to be more unique
//         // $refOrderNo = ''.$order->nation_code.''.str_pad($order->d_order_id, 7, '0', STR_PAD_LEFT).''.str_pad($order->d_order_detail_id, 2, '0', STR_PAD_LEFT);
//         $refOrderNo = ''.$order->nation_code.''.date('ymdHis');
        
//         //}
//         $pickupDate = date("Y-m-d", strtotime("+".$time." hour"));
//         $ch = curl_init();
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

//         //By Donny Dennison, change url api qxpress to the new one
//         // curl_setopt($ch, CURLOPT_URL, $this->qx_api_host.'shipment/CreatePickupOrder.php');
//         curl_setopt($ch, CURLOPT_URL, $this->qx_api_host.'GMKT.INC.GLPS.OpenApiService/Giosis.qapi?key=&v=1.0&returnType=xml&method=PickupOuterService.CreatePickupOrder');

//         $headers = array();
//         $headers[] = 'Content-Type: Text/xml';
//         $headers[] = 'Accept: Text/xml';
//         $postdata = array(
//           'apiKey' => $this->qx_api_key,
//           'accountId' => $this->qx_account_id,
//           'pickupDate' => $pickupDate,
//           'countryCode' => 'SG',
//           'zipcode' => $order->kodepos,
          
//           //By Donny Dennison - 27 June 2020 5:31
//           //Request by Mr Jackie, not using alamat
//           // 'addr1' => $address->alamat,
//           'addr1' => $order->alamat2,

//           //By Donny Dennison - 30 June 2020 16:39
//           //Request by Mr Jackie, change to address notes
//           // 'addr2' => $address->alamat2,
//           'addr2' => $order->catatan,

//           'mobileNo' => $order->telp,
//           'telNo' => $order->telp,
//           'quantity' => $qty,
//           'requestMemo' => 'Please confirm the following '.$qty.' item(s)',
//           'vehicleType' => 'VAN',
//           'pickupNo' => $refOrderNo
//         );
//         curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));

//         $result = curl_exec($ch);
//         if (curl_errno($ch)) {
//             return 0;
//             //echo 'Error:' . curl_error($ch);
//         }
//         curl_close($ch);
//         if($this->is_log){
//           $this->seme_log->write("api_cron", 'API_Mobile/WayBill::__createQXpressPickup:: -- cUrlHeader: '.json_encode($headers));
//           $this->seme_log->write("api_cron", 'API_Mobile/WayBill::__createQXpressPickup:: -- cUrlPOST: '.json_encode($postdata));
//         }
//         return $result;
//     }


// }

