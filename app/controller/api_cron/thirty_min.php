<?php

// require_once (SENEROOT.'app/controller/api_mobile/seller/waybill.php');

// class Thirty_Min extends JI_Controller
// {
//     public $email_send = 1;
//     public $is_log = 1;
//     public $is_push = 1;

//     public function __construct()
//     {
//         parent::__construct();
//         // $this->lib("seme_log");
//         $this->lib("seme_email");
//         // $this->load("api_cron/a_notification_model", "anot");
//         // $this->load("api_cron/b_user_model", "bu");
//         // $this->load("api_cron/b_user_setting_model", "busm");
//         // $this->load("api_cron/c_produk_model", "cpm");
//         // $this->load("api_cron/d_cart_model", "cart");
//         // $this->load("api_cron/d_order_model", "order");
//         $this->load("api_cron/d_order_detail_model", "dodmcron");
//         // $this->load("api_cron/d_order_proses_model", "dopm");
//         // $this->load("api_cron/d_pemberitahuan_model", "dpem"); //notification list
//         // $this->load("api_cron/d_order_detail_item_model", "dodim");
//         // $this->load("api_cron/d_order_detail_pickup_model", "dodpm");
//         $this->load("api_mobile/d_order_model", "order");
// 		$this->load("api_mobile/d_order_detail_model", "dodm");
// 		$this->load("api_mobile/d_order_detail_pickup_model", "dodpum");
// 		$this->load("api_mobile/b_user_model", "bu");
// 		$this->load("api_mobile/d_order_proses_model", "dopm");
// 		$this->load("api_mobile/d_order_detail_item_model", "dodim");
// 		$this->load("api_mobile/a_pengguna_model", "apm");
// 		$this->load("api_mobile/b_user_setting_model", "busm");
// 		$this->load("api_mobile/a_notification_model", "anot");
// 		$this->load("api_mobile/d_pemberitahuan_model", "dpem");
//     }
//     public function index()
//     {
//         //open transaction
//         $this->order->trans_start();

//         //change log filename
//         // $this->seme_log->changeFilename("cron.log");

//         /** @var int define seller confirm delivery timeout in minute(s) */
//         $seller_confirm_delivery_timeout = 30;
//         if (isset($this->seller_confirm_delivery_timeout)) {
//             $seller_confirm_delivery_timeout = $this->seller_confirm_delivery_timeout;
//         }

//         //put on log
//         $this->seme_log->write("api_cron", 'API_Cron/Thirty_Min::index --configuration --seller_confirm_delivery_timeout: '.$seller_confirm_delivery_timeout.'m');

//         /** @var array list of order that seller already confirm order but havent confirm delivery */
//         $orderSellerConfirmeds = $this->dodmcron->getSellerConfirmed($seller_confirm_delivery_timeout);

//         $c = count($orderSellerConfirmeds);
//         $this->seme_log->write("api_cron", 'API_Cron/Thirty_Min::index --orderSellerConfirmedCount: '.$c);
//         $this->seme_log->write("api_cron", "API_Cron/Thirty_Min::index --orderSellerConfirmedData: ".json_encode($orderSellerConfirmeds));

//         if (count($orderSellerConfirmeds)>0) {

//             //initial controller from different file
//             $waybillController = new waybill();

//             foreach ($orderSellerConfirmeds as $orderSellerConfirmed) {

//             	//START copy from api_mobile/seller/order/delivery_process

//             	//start transaction
// 		        $this->order->trans_start();

// 		        $nation_code = $orderSellerConfirmed->nation_code;
// 		        $d_order_id = $orderSellerConfirmed->d_order_id;
// 		        $c_produk_id = $orderSellerConfirmed->c_produk_id;
// 		        $pelanggan = $this->bu->getById($nation_code, $orderSellerConfirmed->b_user_id_seller);

// 	            //build history process
// 	            $di = array();
// 	            $di['nation_code'] = $nation_code;
// 	            $di['d_order_id'] = $d_order_id;
// 	            $di['c_produk_id'] = $c_produk_id;
// 	            $di['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $c_produk_id);
// 	            $di['initiator'] = "Seller";
// 	            $di['nama'] = "Order in Process";
// 	            $di['deskripsi'] = "".html_entity_decode($orderSellerConfirmed->nama,ENT_QUOTES)." is confirmed and ready to be delivered by the seller.";
// 	            $di['cdate'] = "NOW()";
// 	            $di['is_done'] = "1";
// 	            $this->dopm->set($di);
//                 $this->status = 200;
//                 $this->message = 'Success, ordered product now in process';
//                 $this->order->trans_commit();

// 		        //populating update data
// 		        $du = array();
// 		        $du['delivery_date'] = 'NOW()';
// 		        $du['shipment_status'] = 'delivered';
// 		        $du['date_begin'] = date("Y-m-d H:i:s");
// 		        $du['date_expire'] = date("Y-m-d H:i:s", strtotime("+3 days"));
// 		        $res = $this->dodm->update($nation_code, $d_order_id, $c_produk_id, $du);
// 		        if ($res) {
// 		            $this->order->trans_commit();

// 		            //by Donny Dennison - 10 july 2020 10:31
// 		            //move send api delivery to controller/api_mobile/order/delivery_process
// 		            // START change by Donny Dennison - 10 july 2020 10:31
// 		            //get order from d_order_detail and d_order
// 		            $order2 = $this->dodmcron->getById($nation_code, $d_order_id, $c_produk_id);

// 		            //running backward compatibilty
// 		            if (!isset($order2->d_order_id)) {
// 		                $c_produk_id = $this->dodim->getOrderDetailByOrderIdProdukId($nation_code, $d_order_id, $c_produk_id);
// 		                if ($c_produk_id<=0) {
// 		                //     $this->status = 6012;
// 		                //     $this->message = 'Order with supplied ID(s) not found';
// 		                //     $this->__json_out($data);
// 		                //     die();
// 		                		continue;
// 		                }
// 		                $order2 = $this->dodmcron->getById($nation_code, $d_order_id, $c_produk_id);
// 		            }
// 		            //get address pickup
// 		            $pickup = $this->dodpum->getById($nation_code, $order2->d_order_id, $order2->d_order_detail_id);
// 		            if (!isset($pickup->penerima_nama)) {
// 		                //if not exist, get from b_user_alamat
// 		                $pa = $this->bua->getById($nation_code, $order2->b_user_id_seller, $order2->b_user_alamat_id);
// 		                if (!isset($pa->penerima_nama)) {
// 		                //     $this->status = 6022;
// 		                //     $this->message = 'Pickup address not found';
// 		                //     $this->__json_out($data);
// 		                //     die();
// 		                		continue;
// 		                }

// 		                //insert into pickup order
// 		                $padi = array();
// 		                $padi['nation_code'] = $nation_code;
// 		                $padi['d_order_id'] = $order2->d_order_id;
// 		                $padi['d_order_detail_id'] = $order2->d_order_detail_id;
// 		                $padi['b_user_id'] = $order2->b_user_id_seller;
// 		                $padi['b_user_alamat_id'] = $order2->b_user_alamat_id;
// 		                $padi['nama'] = $pa->penerima_nama;
// 		                $padi['telp'] = $pa->penerima_telp;
// 						// by Muhammad Sofi - 3 November 2021 10:00
//         				// remark code
// 		                // $padi['alamat'] = $pa->alamat;
// 		                $padi['alamat2'] = $pa->alamat2;
// 		                $padi['kelurahan'] = $pa->kelurahan;
// 		                $padi['kecamatan'] = $pa->kecamatan;
// 		                $padi['kabkota'] = $pa->kabkota;
// 		                $padi['provinsi'] = $pa->penerima_nama;
// 		                $padi['negara'] = $pa->negara;
// 		                $padi['kodepos'] = $pa->kodepos;
// 		                $padi['latitude'] = $pa->latitude;
// 		                $padi['longitude'] = $pa->longitude;
// 		                $padi['catatan'] = $pa->address_notes;
// 		                $this->dodpum->set($padi);
// 		                $pickup = $pa;
// 		                $pickup->nama = $pa->penerima_nama;
// 		                $pickup->telp = $pa->penerima_nama;
// 						// by Muhammad Sofi - 3 November 2021 10:00
//         				// remark code
// 		                // $pickup->alamat1 = $pa->alamat;
// 		                $pickup->catatan = $pa->address_notes;
// 		            }

// 		            // if (isset($order2->is_wb_download)) {
// 		            //     $this->dodm->updateWB($nation_code, $d_order_id, $order2->d_order_detail_id);
// 		            // }
// 		            if (isset($order2->foto)) {
// 		                $order2->foto = $this->cdn_url($order2->foto);
// 		            }
// 		            if (isset($order2->thumb)) {
// 		                $order2->thumb = $this->cdn_url($order2->thumb);
// 		            }

// 		            //get buyer detail
// 		            $buyer = $this->bu->getById($nation_code, $order2->b_user_id_buyer);
// 		            $seller = $pelanggan;

// 		            //put another
// 		            $order2->addresses = $waybillController->__orderAddresses($nation_code, $buyer, $d_order_id);
// 		            $order2->proses = $this->dopm->getByOrderId($nation_code, $d_order_id);

// 		            //validation
// 		            $is_rejected = 0;
// 		            if (strtolower($order2->seller_status) == 'rejected') {
// 		                if ($this->is_log) {
// 		                    $this->seme_log->write("api_cron", "API_Cron/Thirty_Min::index --expiredPDF");
// 		                }
// 		                $is_rejected = 1;
// 		                $waybillController->__expiredPDF($pelanggan, $order2, $pickup);
// 		                // die();
// 		                continue;
// 		                //$this->status = 6013;
// 		                //$this->message = 'Order already rejected by seller';
// 		                //$this->__json_out($data);
// 		                //die();
// 		            }
// 		            //log order id
// 		            if ($this->is_log) {
// 		                $this->seme_log->write("api_cron", "API_Cron/Thirty_Min::index -> POST: d_order_id: $d_order_id, c_produk_id: $c_produk_id");
// 		            }

// 		            if ($this->is_log) {
// 		                $this->seme_log->write("api_cron", "API_Cron/Thirty_Min::index -- order2->shipment_service: $order2->shipment_service, order2->shipment_type: $order2->shipment_type");
// 		            }

// 		            //By Donny Dennison - 08-07-2020 16:16
// 		            //Request by Mr Jackie, add new shipment status "courier fail"
// 		            //create pickup order, for shipment == process
// 		            // if (strtolower($order2->shipment_service) == 'qxpress' && (strtolower($order2->shipment_status) == 'process' || strtolower($order2->shipment_status) == 'delivered')) {

// 		            $isFailedApiDelivery = FALSE;

// 		            //by Donny Dennison - 23 september 2020 15:42
// 					//add direct delivery feature
// 					//START by Donny Dennison - 23 september 2020 15:42

// 		            if ((strtolower($order2->shipment_service) == 'direct delivery' || $order2->is_direct_delivery_buyer == 1) && (strtolower($order2->shipment_status) == 'process' || strtolower($order2->shipment_status) == 'delivered' || strtolower($order2->shipment_status) == 'courier fail')) {

// 		                    $addr = $order2->addresses->shipping;

//                             //update order detail
//                             $dx = array();

//                             $refOrderNo = ''.$nation_code.''.date('ymdHis');
//                             $dx["shipment_tranid"] = $refOrderNo;
//                             $dx["shipment_confirmed"] = 1;
//                             $dx["pickup_date"] = date("Y-m-d H:i:s", strtotime("+1 day"));
//                             $dx["delivery_date"] = $dx["pickup_date"];

//                             $this->dodm->update($nation_code, $order2->d_order_id, $order2->c_produk_id, $dx);
//                             $this->order->trans_commit();


//                             // add to order proses with current status
//                             $ops = array();
//                             $ops['nation_code'] = $nation_code;
//                             $ops['d_order_id'] = $d_order_id;
//                             $ops['c_produk_id'] = $order2->c_produk_id;
//                             $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order2->c_produk_id);
//                             $ops['initiator'] = "Seller";
//                             $ops['nama'] = "Delivery in Progress";
//                             $ops['deskripsi'] = "Seller is going to deliver the product, ".html_entity_decode($order2->nama,ENT_QUOTES)."($order2->invoice_code), to you directly.";
//                             $ops['cdate'] = "NOW()";
//                             $this->dopm->set($ops);
//                             $this->order->trans_commit();

// 		            //by Donny Dennison - 15 september 2020 17:45
// 	       			 //change name, image, etc from gogovan to gogox

// 		            //By Donny Dennison - 08-07-2020 16:16
// 		            //Request by Mr Jackie, add new shipment status "courier fail"
// 		            // } elseif (strtolower($order2->shipment_service) == 'gogovan' && (strtolower($order2->shipment_status) == 'process' || strlen($order2->shipment_tranid)<=4)) {
// 		            // } elseif (strtolower($order2->shipment_service) == 'gogovan' && (strtolower($order2->shipment_status) == 'process' || strtolower($order2->shipment_status) == 'courier fail' || strlen($order2->shipment_tranid)<=4)) {
// 		            // if (strtolower($order2->shipment_service) == 'qxpress' && (strtolower($order2->shipment_status) == 'process' || strtolower($order2->shipment_status) == 'delivered' || strtolower($order2->shipment_status) == 'courier fail')) {
// 		            } else if (strtolower($order2->shipment_service) == 'qxpress' && (strtolower($order2->shipment_status) == 'process' || strtolower($order2->shipment_status) == 'delivered' || strtolower($order2->shipment_status) == 'courier fail')) {

// 	            	//END by Donny Dennison - 23 september 2020 15:42

// 		                if (strtolower($order2->shipment_type) == 'next day' && strlen($order2->shipment_tranid)<=4) {
// 		                    $addr = $order2->addresses->shipping;

// 		                    //by Donny Dennison - 13-07-2020 13:54
// 		                    //disable send api to qxpress
// 		                    // //By Donny Dennison - 7 june 2020 - 14:29
// 		                    // //change send data send to qxpress from buyer to seller
// 		                    // // $rq = $this->__createQXpress($addr, $order2, $seller);
// 		                    // $rq = $waybillController->__createQXpress($addr, $order2, $buyer);

// 		                    // //put on log
// 		                    // $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process -> __createQXpress: ".($rq));

// 		                    // //parsing XML result
// 		                    // $sodt = @simplexml_load_string($rq);
// 		                    // if ($sodt === false) {
		                        
// 		                    //     //parsing error
// 		                    //     $cqxe = '';
// 		                    //     foreach (libxml_get_errors() as $error) {
// 		                    //         $cqxe .= $error->message.', ';
// 		                    //     }
// 		                    //     $cqxe = rtrim($cqxe, ', ');
// 		                    //     if ($this->is_log) {
// 		                    //         $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process -> __createQXpress PARSE_ERROR: ".$cqxe);
//                     //     }

//                     //     //By Donny Dennison - 08-07-2020 16:16
//                     //     //Request by Mr Jackie, add new shipment status "courier fail"
//                     //     // //notif seller to sent it manually, collect array notification list
//                     //     // $extras = new stdClass();
//                     //     // $extras->id_produk = $order2->c_produk_id;
//                     //     // $extras->id_order = $order2->d_order_id;
//                     //     // $extras->id_order_detail = $order2->c_produk_id;
//                     //     // $dpe = array();
//                     //     // $dpe['nation_code'] = $nation_code;
//                     //     // $dpe['b_user_id'] = $pelanggan->id;
//                     //     // $dpe['id'] = $this->dpem->getLastId($nation_code, $pelanggan->id);
//                     //     // $dpe['type'] = "transaction";
//                     //     // $dpe['judul'] = "Sent to QXpress";
//                     //     // $dpe['teks'] = "Please bring your ordered product to nearest QXPress courier services.";
//                     //     // $dpe['cdate'] = "NOW()";
//                     //     // $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
//                     //     // $dpe['extras'] = json_encode($extras);
//                     //     // $this->dpem->set($dpe);
//                     //     // $this->order->trans_commit();
//                     //     $isFailedApiDelivery = TRUE;

//                     // } else {
//                     //     //parse XML success
//                     //     if (!is_object($sodt)) {
//                     //         $sodt = new stdClass();
//                     //     }
//                     //     if (!isset($sodt->ResultCode)) {
//                     //         $sodt->ResultCode = '-99999';
//                     //     }
//                     //     if ($sodt->ResultCode==0) {
//                     //         if ($this->is_log) {
//                     //             $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process -> sodt->ResultCode->TrackingNo: ".json_encode($sodt->ResultObject));
//                     //         }
//                     //         //success, check response result object reference number
//                     //         if (isset($sodt->ResultObject->TrackingNo)) {
//                     //             $order2->shipment_tranid = $sodt->ResultObject->TrackingNo;
//                     //         }

//                     //         //By Donny Dennison, 30 june 2020 15:43
//                     //         //Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
//                     //         //Start change by Donny Dennison
//                     //         $tracking_number = NULL;
//                     //         $rq2 = $waybillController->__getQXpressTracking($order2->shipment_tranid);

//                     //         //put on log
//                     //         $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process -> __getQXpressTracking: ".($rq2));

//                     //         //parsing XML result
//                     //         $sodt2 = @simplexml_load_string($rq2);
                            
//                     //         //parse XML success
//                     //         if (!is_object($sodt2)) {
//                     //           $sodt2 = new stdClass();
//                     //         }
//                     //         if (!isset($sodt2->ResultCode)) {
//                     //           $sodt2->ResultCode = '-99';
//                     //         }
//                     //         if ($sodt2->ResultCode==0) {

//                     //           if ($this->is_log) {
//                     //             $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process -> sodt2->ResultCode->TrackingNo: ".json_encode($sodt2->ResultObject));
//                     //           }

//                     //           //add to field tracking_number
//                     //           $tracking_number = $sodt2->ResultObject->info->shipping_no;
                                  
//                     //         } elseif ($sodt2->ResultCode=="-99" || $sodt2->ResultCode==-99) {
//                     //           //if server error, recreate order QXpress
//                     //           $rq2 = $waybillController->__getQXpressTracking($orde2r->shipment_tranid);
//                     //           $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process QXpress server error, recreating");
//                     //           //put on log
//                     //           $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process -> __getQXpressTracking : ".($rq2));

//                     //           //parsing XML result
//                     //           $sodt2 = simplexml_load_string($rq2);
                              
//                     //           //parse OK
//                     //           if ($this->is_log) {
//                     //               $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process -> sodt2->ResultCode->TrackingNo: ".json_encode($sodt2->ResultObject));
//                     //           }

//                     //           //decode result
//                     //           $pudt = json_decode($rq2);
//                     //           if (isset($pudt->ResultCode)) {
//                     //             if ($pudt->ResultCode == 0) {
                                    
//                     //               //add to field tracking_number
//                     //               $tracking_number = $pudt->ResultObject->info->shipping_no;

//                     //             } else {
//                     //                 //maybe pickup order has created before, do nothing
//                     //             }
//                     //           } else {
//                     //               //notif to seller for deliver their product manually, collect array notification list
//                     //           }

//                     //         }

//                     //         //End change by Donny Dennison

//                             //update order detail
//                             $dx = array();

//                             //by Donny Dennison - 13-07-2020 13:54
//                             //disable send api to qxpress
//                             // $dx["shipment_tranid"] = $order2->shipment_tranid;
//                             $refOrderNo = ''.$nation_code.''.date('ymdHis');
//                             $dx["shipment_tranid"] = $refOrderNo;

//                             //by Donny Dennison - 13-07-2020 13:54
//                             //disable send api to qxpress
//                             // //By Donny Dennison, 30 june 2020 15:43
//                             // //Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
//                             // $dx["tracking_number"] = $tracking_number;
                            
//                             $dx["shipment_confirmed"] = 1;
//                             // $dx["pickup_date"] = "null";
//                             $dx["pickup_date"] = date("Y-m-d H:i:s", strtotime("+1 day"));
//                             $dx["delivery_date"] = $dx["pickup_date"];

//                             //by Donny Dennison - 13-07-2020 13:54
//                             //disable send api to qxpress
//                             // $dx['shipment_response'] = $rq;

//                             $this->dodm->update($nation_code, $order2->d_order_id, $order2->c_produk_id, $dx);
//                             $this->order->trans_commit();

//                             //comment by mas Ilham
//                             // //create pickup
//                             // $seller = $this->bu->getById($nation_code, $order2->b_user_id_seller);
//                             // $rq2 = $this->__createQXpressPickup($pickup, $order2, $seller);

//                             // //put on log
//                             // $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process -> __createQXpressPickup: ".($rq2));

//                             // //decode result
//                             // $pudt = json_decode($rq2);
//                             // if (isset($pudt->ResultCode)) {
//                             //     if ($pudt->ResultCode == 0) {
//                             //         //collect array notification list
//                             //         $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process QXpress Pickup Order done");

//                             //         //update pickup date, to tommorow
//                             //         $dx = array();
//                             //         $dx["pickup_date"] = date("Y-m-d H:i:s", strtotime("+1 day"));
//                             //         $dx["delivery_date"] = $dx["pickup_date"];
//                             //         $this->dodm->update($nation_code, $order2->d_order_id, $order2->c_produk_id, $dx);
//                             //         $this->order->trans_commit();
//                             //         $order2->delivery_date = $dx["delivery_date"];
//                             //         $order2->pickup_date = $dx["pickup_date"];

//                             //         // add to order proses with current status
//                             //         $ops = array();
//                             //         $ops['nation_code'] = $nation_code;
//                             //         $ops['d_order_id'] = $d_order_id;
//                             //         $ops['c_produk_id'] = $order2->c_produk_id;
//                             //         $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order2->c_produk_id);
//                             //         $ops['initiator'] = "Seller";
//                             //         $ops['nama'] = "Pickup Requested";
//                             //         $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been added to the QXpress: Next Day pickup queue list";
//                             //         $ops['cdate'] = "NOW()";
//                             //         $this->dopm->set($ops);
//                             //         $this->order->trans_commit();
//                             //     } else {
//                             //         //maybe pickup order has created before, do nothing
//                             //     }
//                             // } else {
//                             //     //notif to seller for deliver their product manually, collect array notification list
//                             // }

//                             // add to order proses with current status
//                             $ops = array();
//                             $ops['nation_code'] = $nation_code;
//                             $ops['d_order_id'] = $d_order_id;
//                             $ops['c_produk_id'] = $order2->c_produk_id;
//                             $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order2->c_produk_id);
//                             $ops['initiator'] = "Seller";
//                             $ops['nama'] = "Delivery in Progress";

//                             //by Donny Dennison - 10 october 2020 17:50
//                             //remove receipt number

//                             //By Donny Dennison, 30 june 2020 15:43
//                             //Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
//                             // $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from QXpress: Next Day (receipt number: $order2->shipment_tranid)";
                            
//                             // $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from QXpress: Next Day (receipt number: ".$refOrderNo.")";
//                             $ops['deskripsi'] = "Your order ".html_entity_decode($order2->nama,ENT_QUOTES)." ($order2->invoice_code) has been sent by the seller using a courier from QXpress: Next Day";

//                             $ops['cdate'] = "NOW()";
//                             $this->dopm->set($ops);
//                             $this->order->trans_commit();
//                         // } elseif ($sodt->ResultCode=="-55" || $sodt->ResultCode==-55) {
//                         //     //duplicate invoice code or tranid, recreate order QXpress

//                         //     //By Donny Dennison - 7 june 2020 - 14:29
//                         //     //change send data send to qxpress from buyer to seller
//                         //     // $rq = $this->__createQXpress($addr, $order, $seller, 0);
//                         //     $rq = $waybillController->__createQXpress($addr, $order2, $buyer, 0);

//                         //     $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process QXpress Create Order same TranID, recreating");
//                         //     //put on log
//                         //     $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process -> __createQXpress phase2: ".($rq));

//                         //     //parsing XML result
//                         //     $sodt = simplexml_load_string($rq);
//                         //     if ($sodt === false) {
//                         //         //parsing error
//                         //         $cqxe = '';
//                         //         foreach (libxml_get_errors() as $error) {
//                         //             $cqxe .= $error->message.', ';
//                         //         }
//                         //         $cqxe = rtrim($cqxe, ', ');
//                         //         if ($this->is_log) {
//                         //             $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process -> __createQXpress phase2 PARSE_ERROR: ".$cqxe);
//                         //         }

//                         //         //By Donny Dennison - 08-07-2020 16:16
//                         //         //Request by Mr Jackie, add new shipment status "courier fail"
//                         //         $isFailedApiDelivery = TRUE;

//                         //     } else {
//                         //         //parse OK
//                         //         if ($this->is_log) {
//                         //             $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process -> sodt->ResultCode->TrackingNo: ".json_encode($sodt->ResultObject));
//                         //         }
//                         //         //success, check response result object tracking number
//                         //         if (isset($sodt->ResultObject->TrackingNo)) {
//                         //             $order2->shipment_tranid = $sodt->ResultObject->TrackingNo;
//                         //         }

//                         //         //By Donny Dennison, 30 june 2020 15:43
//                         //         //Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
//                         //         //Start change by Donny Dennison
//                         //         $tracking_number = NULL;
//                         //         $rq2 = $waybillController->__getQXpressTracking($order2->shipment_tranid);

//                         //         //put on log
//                         //         $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process -> __getQXpressTracking: ".($rq2));

//                         //         //parsing XML result
//                         //         $sodt2 = @simplexml_load_string($rq2);
                                
//                         //         //parse XML success
//                         //         if (!is_object($sodt2)) {
//                         //           $sodt2 = new stdClass();
//                         //         }
//                         //         if (!isset($sodt2->ResultCode)) {
//                         //           $sodt2->ResultCode = '-99';
//                         //         }
//                         //         if ($sodt2->ResultCode==0) {

//                         //           if ($this->is_log) {
//                         //             $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process -> sodt2->ResultCode->TrackingNo: ".json_encode($sodt2->ResultObject));
//                         //           }

//                         //           //add to field tracking_number
//                         //           $tracking_number = $sodt2->ResultObject->info->shipping_no;
                                      
//                         //         } elseif ($sodt2->ResultCode=="-99" || $sodt2->ResultCode==-99) {
//                         //           //if server error, recreate order QXpress
//                         //           $rq2 = $waybillController->__getQXpressTracking($order2->shipment_tranid);
//                         //           $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process QXpress server error, recreating");
//                         //           //put on log
//                         //           $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process -> __getQXpressTracking phase2: ".($rq2));

//                         //           //parsing XML result
//                         //           $sodt2 = simplexml_load_string($rq2);
                                  
//                         //           //parse OK
//                         //           if ($this->is_log) {
//                         //               $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process -> sodt2->ResultCode->TrackingNo: ".json_encode($sodt2->ResultObject));
//                         //           }

//                         //           //decode result
//                         //           $pudt = json_decode($rq2);
//                         //           if (isset($pudt->ResultCode)) {
//                         //             if ($pudt->ResultCode == 0) {
                                        
//                         //               //add to field tracking_number
//                         //               $tracking_number = $pudt->ResultObject->info->shipping_no;

//                         //             } else {
//                         //                 //maybe pickup order has created before, do nothing
//                         //             }
//                         //           } else {
//                         //               //notif to seller for deliver their product manually, collect array notification list
//                         //           }

//                         //         }

//                         //         //End change by Donny Dennison

//                         //         //update order detail
//                         //         $dx = array();
//                         //         $dx["shipment_tranid"] = $order2->shipment_tranid;
                                
//                         //         //By Donny Dennison, 30 june 2020 15:43
//                         //         //Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
//                         //         $dx["tracking_number"] = $tracking_number;

//                         //         $dx["shipment_confirmed"] = 1;
//                         //         $dx["pickup_date"] = "null";
//                         //         $dx["delivery_date"] = "NOW()";
//                         //         $dx['shipment_response'] = $rq;
//                         //         $this->dodm->update($nation_code, $order2->d_order_id, $order2->c_produk_id, $dx);
//                         //         $this->order->trans_commit();
//                         //         $order->delivery_date = date("Y-m-d");
//                         //         $order->pickup_date = date("Y-m-d");

//                         //         //comment by mas Ilham
//                         //         // //create pickup
//                         //         // $seller = $this->bu->getById($nation_code, $order2->b_user_id_seller);
//                         //         // $rq2 = $this->__createQXpressPickup($pickup, $order2, $seller);

//                         //         // //put on log
//                         //         // $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process -> __createQXpressPickup: ".($rq2));

//                         //         // //decode result
//                         //         // $pudt = json_decode($rq2);
//                         //         // if (isset($pudt->ResultCode)) {
//                         //         //     if ($pudt->ResultCode == 0) {
//                         //         //         //update pickup date, to tommorow
//                         //         //         $dx = array();
//                         //         //         $dx["pickup_date"] = date("Y-m-d H:i:s", strtotime("+1 day"));
//                         //         //         $dx["delivery_date"] = date("Y-m-d H:i:s", strtotime("+1 day"));
//                         //         //         $this->dodm->update($nation_code, $order2->d_order_id, $order2->c_produk_id, $dx);
//                         //         //         $this->order->trans_commit();
//                         //         //         $order2->delivery_date = $dx["delivery_date"];
//                         //         //         $order2->pickup_date = $dx["pickup_date"];

//                         //         //         // add to order proses with current status
//                         //         //         $ops = array();
//                         //         //         $ops['nation_code'] = $nation_code;
//                         //         //         $ops['d_order_id'] = $d_order_id;
//                         //         //         $ops['c_produk_id'] = $order2->c_produk_id;
//                         //         //         $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order2->c_produk_id);
//                         //         //         $ops['initiator'] = "Seller";
//                         //         //         $ops['nama'] = "Pickup Requested";
//                         //         //         $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been added to the QXpress: Next Day pickup queue list";
//                         //         //         $ops['cdate'] = "NOW()";
//                         //         //         $this->dopm->set($ops);
//                         //         //         $this->order->trans_commit();
//                         //         //     } else {
//                         //         //         //maybe pickup order has created before, do nothing
//                         //         //     }
//                         //         // } else {
//                         //         //     //notif to seller for deliver their product manually, collect array notification list
//                         //         // }

//                         //         // add to order proses with current status
//                         //         $ops = array();
//                         //         $ops['nation_code'] = $nation_code;
//                         //         $ops['d_order_id'] = $d_order_id;
//                         //         $ops['c_produk_id'] = $order2->c_produk_id;
//                         //         $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order2->c_produk_id);
//                         //         $ops['initiator'] = "Seller";
//                         //         $ops['nama'] = "Delivery in Progress";

//                         //         //By Donny Dennison, 30 june 2020 15:43
//                         //         //Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
//                         //         // $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from QXpress: Next Day (receipt number: $order2->shipment_tranid)";
//                         //         $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from QXpress: Next Day (receipt number: ".$tracking_number.")";

//                         //         $ops['cdate'] = "NOW()";
//                         //         $this->dopm->set($ops);
//                         //         $this->order->trans_commit();
//                         //     } //end parse validation
//                         // } else {
//                         //     // __createQXpress response code error, maybe order already created

//                         //     //By Donny Dennison - 08-07-2020 16:16
//                         //     //Request by Mr Jackie, add new shipment status "courier fail"
//                         //     if ($this->is_log) {
//                         //       $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process -> response dari QXpress bukan 0, isi sodt: ".json_encode($sodt));
//                         //     }
//                         //     $isFailedApiDelivery = TRUE;

//                         // }
//                     // }
// 	                } elseif ((strtolower($order2->shipment_type) == 'same day' || (strtolower($order2->shipment_type) == 'sameday')) && strlen($order2->shipment_tranid)<=4) {
// 	                    //for qxpress same day, manually with admin action
// 	                    //update pickup date to next 2hours
// 	                    $dod = array();
// 	                    $dod["pickup_date"] = date("Y-m-d H:i:s", strtotime("+2 hour"));
// 	                    $dod["delivery_date"] = date("Y-m-d H:i:s", strtotime("+4 hour"));
// 	                    $this->dodm->update($nation_code, $d_order_id, $c_produk_id, $dod);
// 	                    $this->order->trans_commit();
// 	                    $order2->delivery_date = $dod["delivery_date"];
// 	                    $order2->pickup_date = $dod["pickup_date"];

// 	                    //send email to admin
// 	                    if ($this->email_send) {
// 	                        //get product data
// 	                        $produk_nama = '-';
// 	                        $items = $this->dodim->getByOrderIdDetailId($nation_code, $d_order_id, $c_produk_id);
// 	                        if (count($items)) {
// 	                            $produk_nama = '';
// 	                            foreach ($items as $itm) {
// 	                                $produk_nama .= $itm->nama.', ';
// 	                            }
// 	                        }

// 	                        //get active admin
// 	                        $admins = $this->apm->getEmailActive();

// 	                        //begin send email to admin
// 	                        $replacer = array();
// 	                        $replacer['site_name'] = $this->app_name;
// 	                        $replacer['produk_nama'] = html_entity_decode($produk_nama,ENT_QUOTES);
// 	                        $replacer['invoice_code'] = $order2->invoice_code;
// 	                        $this->seme_email->replyto($this->site_name, $this->site_replyto);
// 	                        $this->seme_email->from($this->site_email, $this->site_name);
// 	                        $eml = '';
// 	                        foreach ($admins as $adm) {
// 	                            if (strlen($adm->email)>4) {
// 	                                $this->seme_email->to($adm->email, $adm->nama);
// 	                                $eml .= $adm->email.', ';
// 	                            }
// 	                        }
// 	                        $this->seme_email->subject('QXpress - Same day');
// 	                        $this->seme_email->template('qxpress_sameday');
// 	                        $this->seme_email->replacer($replacer);
// 	                        $this->seme_email->send();

// 	                        $eml = rtrim($eml, ', ');
// 	                        if ($this->is_log) {
// 	                            $this->seme_log->write("api_cron", "API_Cron/Thirty_Min::index --sendEmailWBAdmin --to: $eml");
// 	                        }
// 	                        //end send email to admin

// 	                        // add to order proses with current status
// 	                        $ops = array();
// 	                        $ops['nation_code'] = $nation_code;
// 	                        $ops['d_order_id'] = $d_order_id;
// 	                        $ops['c_produk_id'] = $order2->c_produk_id;
// 	                        $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order2->c_produk_id);
// 	                        $ops['initiator'] = "Seller";
// 	                        $ops['nama'] = "Delivery in Progress";
// 	                        $ops['deskripsi'] = "Your order ".html_entity_decode($order2->nama,ENT_QUOTES)." ($order2->invoice_code) has been added to QXpress: Same Day queue, please wait for delivery";
// 	                        $ops['cdate'] = "NOW()";
// 	                        $this->dopm->set($ops);
// 	                        $this->order->trans_commit();
// 	                    }
// 	                } else {
// 	                    //undefined shipping type, do nothing
// 	                }


// 	            //by Donny Dennison - 15 september 2020 17:45
//        			 //change name, image, etc from gogovan to gogox

// 	            //By Donny Dennison - 08-07-2020 16:16
// 	            //Request by Mr Jackie, add new shipment status "courier fail"
// 	            // } elseif (strtolower($order2->shipment_service) == 'gogovan' && (strtolower($order2->shipment_status) == 'process' || strlen($order2->shipment_tranid)<=4)) {
// 	            // } elseif (strtolower($order2->shipment_service) == 'gogovan' && (strtolower($order2->shipment_status) == 'process' || strtolower($order2->shipment_status) == 'courier fail' || strlen($order2->shipment_tranid)<=4)) {
// 	            } elseif (strtolower($order2->shipment_service) == 'gogox' && (strtolower($order2->shipment_status) == 'process' || strtolower($order2->shipment_status) == 'courier fail' || strlen($order2->shipment_tranid)<=4)) {

// 	                $address_deliver = $order2->addresses->shipping;

// 	                //by Donny Dennison 7 oktober 2020 - 14:10
// 	                //add promotion face mask
// 	                //START by Donny Dennison 7 oktober 2020 - 14:10

// 	                //find the face mask product
// 	                $products = $this->dodim->getByOrderDetailIdForShipment($nation_code, $order2->d_order_id, $order2->c_produk_id);

// 	                $promotion1 = 0;
// 	                foreach ($products as $key => $value) {
	                    
// 	                    if($value->c_produk_id == 1746 || $value->c_produk_id == 1752 || $value->c_produk_id == 1754){

// 	                        $promotion1 = 1;
// 	                        break;
	                        
// 	                    }

// 	                }

// 	                if($promotion1 == 1){

// 	                    $refOrderNo = ''.$nation_code.''.date('ymdHis');
// 	                    // error_reporting(E_ALL);
// 	                    $dx = array();

// 	                    $dx["shipment_tranid"] = $refOrderNo;
// 	                    $dx["shipment_confirmed"] = 1;
// 	                    $dx["pickup_date"] = date("Y-m-d H:i:00", strtotime("+2 hours"));
// 	                    $dx["delivery_date"] = date("Y-m-d H:i:00", strtotime("+4 hours"));
	                    
// 	                    // $dx['shipment_response'] = $rq;

// 	                    $this->dodm->update($nation_code, $order2->d_order_id, $order2->c_produk_id, $dx);
// 	                    $this->order->trans_commit();

// 	                    $order2->shipment_tranid = $refOrderNo;

// 	                    $order2->delivery_date = $dx["delivery_date"];
// 	                    $order2->pickup_date = $dx["pickup_date"];

// 	                    //inform buyer with current status
// 	                    $ops = array();
// 	                    $ops['nation_code'] = $nation_code;
// 	                    $ops['d_order_id'] = $d_order_id;
// 	                    $ops['c_produk_id'] = $order2->c_produk_id;
// 	                    $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order2->c_produk_id);
// 	                    $ops['initiator'] = "Seller";
// 	                    $ops['nama'] = "Delivery in Progress";
	                    
// 	                    //by Donny Dennison - 10 october 2020 17:50
//                         //remove receipt number

// 	                    //by Donny Dennison - 15 september 2020 17:45
//         				//change name, image, etc from gogovan to gogox
// 	                    // $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from Gogovan (receipt number: $order2->shipment_tranid)";

// 	                    // $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from Gogox (receipt number: $order2->shipment_tranid)";
// 	                    $ops['deskripsi'] = "Your order ".html_entity_decode($order2->nama,ENT_QUOTES)." ($order2->invoice_code) has been sent by the seller using a courier from Gogox";

// 	                    $ops['cdate'] = "NOW()";
// 	                    $this->dopm->set($ops);
// 	                    $this->order->trans_commit();

// 	                //by Donny Dennison - 10 august 2020 14:57
// 	                //if latitude or longitude is empty or 0 then set delivery fee to $39
// 	                //START by Donny Dennison - 10 august 2020 14:57

// 	            	// }else{
// 	                } else if($pickup->latitude == 0 || $pickup->longitude == 0 || $address_deliver->latitude == 0 || $address_deliver->longitude == 0){

//                 	//END by Donny Dennison 7 oktober 2020 - 14:10

// 	                	$refOrderNo = ''.$nation_code.''.date('ymdHis');
// 	                    // error_reporting(E_ALL);
// 	                    $dx = array();

// 	                    $dx["shipment_tranid"] = $refOrderNo;
// 	                    $dx["shipment_confirmed"] = 1;
// 	                    $dx["pickup_date"] = date("Y-m-d H:i:00", strtotime("+2 hours"));
// 	                    $dx["delivery_date"] = date("Y-m-d H:i:00", strtotime("+4 hours"));
	                    
// 	                    // $dx['shipment_response'] = $rq;

// 	                    $this->dodm->update($nation_code, $order2->d_order_id, $order2->c_produk_id, $dx);
// 	                    $this->order->trans_commit();

// 	                    $order2->shipment_tranid = $refOrderNo;

// 	                    $order2->delivery_date = $dx["delivery_date"];
// 	                    $order2->pickup_date = $dx["pickup_date"];

// 	                    //inform buyer with current status
// 	                    $ops = array();
// 	                    $ops['nation_code'] = $nation_code;
// 	                    $ops['d_order_id'] = $d_order_id;
// 	                    $ops['c_produk_id'] = $order2->c_produk_id;
// 	                    $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order2->c_produk_id);
// 	                    $ops['initiator'] = "Seller";
// 	                    $ops['nama'] = "Delivery in Progress";
	                    
// 	                    //by Donny Dennison - 10 october 2020 17:50
//                         //remove receipt number

// 	                    //by Donny Dennison - 15 september 2020 17:45
//         				//change name, image, etc from gogovan to gogox
// 	                    // $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from Gogovan (receipt number: $order2->shipment_tranid)";
	                    
// 	                    // $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from Gogox (receipt number: $order2->shipment_tranid)";
// 	                    $ops['deskripsi'] = "Your order ".html_entity_decode($order2->nama,ENT_QUOTES)." ($order2->invoice_code) has been sent by the seller using a courier from Gogox";

// 	                    $ops['cdate'] = "NOW()";
// 	                    $this->dopm->set($ops);
// 	                    $this->order->trans_commit();

// 	                    //send email to support@sellon.net and jackie@corea.co.id
// 	                    if ($this->email_send) {
// 	                        $replacer = array();
// 	                        $replacer['site_name'] = $this->app_name;
// 	                        $replacer['order_id'] = $order2->d_order_id;
// 	                        $replacer['produk_nama'] = html_entity_decode($order2->nama,ENT_QUOTES);
// 	                        $this->seme_email->flush();
// 	                        $this->seme_email->replyto($this->site_name, $this->site_replyto);
// 	                        $this->seme_email->from($this->site_email, $this->site_name);
// 	                        $this->seme_email->subject('(Admin) GoGoX delivery is required manually.');
// 	                        $this->seme_email->to('support@sellon.net', 'support@sellon.net');
// 	                        $this->seme_email->to('jackie@corea.co.id', 'jackie@corea.co.id');
// 	                        $this->seme_email->template('delivery_in_progress_gogovan_manual');
// 	                        $this->seme_email->replacer($replacer);
// 	                        $this->seme_email->send();

// 	                    }

// 	                }else{

// 	                //END by Donny Dennison - 10 august 2020 14:57

// 		                $rq = $waybillController->__createGogovan($order2, $pickup, $address_deliver);
// 	                	$this->seme_log->write("api_cron", "API_Cron/Thirty_Min::index -> __createGogovan: ".($rq));
		                
// 		                $rqd = json_decode($rq);

// 		                //by Donny Dennison - 8 September 2020 15:09
// 		                //change api gogovan to new version (gogovan change name to gogox)
// 		                // if (isset($rqd->id)) {
// 		                if (isset($rqd->uuid)) {

// 		                    // error_reporting(E_ALL);
// 		                    $dx = array();

// 		                    //by Donny Dennison - 8 September 2020 15:09
// 		                    //change api gogovan to new version (gogovan change name to gogox)
// 		                    // $dx["shipment_tranid"] = $rqd->id;
// 		                    $dx["shipment_tranid"] = $rqd->uuid;

// 		                    $dx["shipment_confirmed"] = 1;
// 		                    $dx["pickup_date"] = date("Y-m-d H:i:00", strtotime("+2 hours"));
// 		                    $dx["delivery_date"] = date("Y-m-d H:i:00", strtotime("+4 hours"));
		                    
// 		                    // $dx['shipment_response'] = $rq;
		                    
// 		                    $this->dodm->update($nation_code, $order2->d_order_id, $order2->c_produk_id, $dx);
// 		                    $this->order->trans_commit();
		                    
// 		                    //by Donny Dennison - 8 September 2020 15:09
// 		                    //change api gogovan to new version (gogovan change name to gogox)
// 		                    // $order2->shipment_tranid = $rqd->id;
// 		                    $order2->shipment_tranid = $rqd->uuid;

// 		                    $order2->delivery_date = $dx["delivery_date"];
// 		                    $order2->pickup_date = $dx["pickup_date"];

// 		                    //inform buyer with current status
// 		                    $ops = array();
// 		                    $ops['nation_code'] = $nation_code;
// 		                    $ops['d_order_id'] = $d_order_id;
// 		                    $ops['c_produk_id'] = $order2->c_produk_id;
// 		                    $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order2->c_produk_id);
// 		                    $ops['initiator'] = "Seller";
// 		                    $ops['nama'] = "Delivery in Progress";

// 		                    //by Donny Dennison - 10 october 2020 17:50
//                         	//remove receipt number

// 		                    //by Donny Dennison - 15 september 2020 17:45
// 	        				//change name, image, etc from gogovan to gogox
// 		                    // $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from Gogovan (receipt number: $order2->shipment_tranid)";

// 		                    // $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from Gogox (receipt number: $order2->shipment_tranid)";
// 		                    $ops['deskripsi'] = "Your order ".html_entity_decode($order2->nama,ENT_QUOTES)." ($order2->invoice_code) has been sent by the seller using a courier from Gogox";

// 		                    $ops['cdate'] = "NOW()";
// 		                    $this->dopm->set($ops);
// 		                    $this->order->trans_commit();

// 		                }else{

// 		                    //By Donny Dennison - 08-07-2020 16:16
// 		                    //Request by Mr Jackie, add new shipment status "courier fail"
// 		                    //response from Gogovan not an id
// 		                    if ($this->is_log) {
// 		                      $this->seme_log->write("api_cron", "API_Mobile/seller/WayBill::delivery_process -> response dari Gogovan bukan uuid, isi rq: ".($rq));
// 		                    }

// 		                    $isFailedApiDelivery = TRUE;

// 		                }

// 		            }

// 	            } else {
// 	                //undefined shipment method, do nothing...        
// 	            }

// 	            //By Donny Dennison - 08-07-2020 16:16
// 	            //Request by Mr Jackie, add new shipment status "courier fail"
// 	            if($isFailedApiDelivery == TRUE){

// 	               //populating update data
// 	              $du = array();
// 	              $du['pickup_date'] = "null";
// 	              $du['delivery_date'] = "null";
// 	              $du['shipment_status'] = 'courier fail';
// 	              $du['date_begin'] = "null";
// 	              $du['date_expire'] = "null";
// 	              $res = $this->dodm->update($nation_code, $d_order_id, $order2->c_produk_id, $du);
// 	              $this->order->trans_commit();

// 	              $code = '301';
// 	              $message = '-';
	              
// 	              //by Donny Dennison - 15 september 2020 17:45
//         		  //change name, image, etc from gogovan to gogox
//               	  // if(strtolower($order2->shipment_service) == 'gogovan'){
//               	  if(strtolower($order2->shipment_service) == 'gogox'){
                
//                     if(!isset($rqd->uuid)){
                    
//                         if(!isset($rqd->code)){

//                             $code = '503';
//                             $message = 'Service Temporarily Unavailable';

//                         }else{

//                             $code = '503';
//                             $message = ''.$rqd->code;

//                         }

//                     }

// 	            	//by Donny Dennison - 13-07-2020 13:54
// 	            	//disable send api to qxpress
// 	              	// }else if(strtolower($order2->shipment_service) == 'qxpress'){

// 	              	//   if($sodt === false) {
	                        
// 	              	//       $message = $cqxe;
	                
// 	              	//   }else{

// 	              	//       $code = ''.$sodt->ResultCode;
// 	              	//       $message = ''.$sodt->ResultMsg;

// 	              	//   }


// 	              	}

// 	                $data['messageFromAPI'] = array(
// 	                    'code'=>$code,
// 	                    'message'=>$message
// 	                );

// 	              // $this->status = 301;
// 	              // $this->message = $message;
// 	              // $this->__json_out($data);
// 	              // die();
// 	                continue;
	            
// 	            }

// 	            // END change by Donny Dennison - 10 july 2020 10:31


//                 //get buyer data
//                 $buyer = $this->bu->getById($nation_code, $order2->b_user_id_buyer);
//                 $seller = $pelanggan;

//                 //get notification config for buyer
//                 $setting_value = 0;
//                 $classified = 'setting_notification_buyer';
//                 $notif_code = 'B3';
//                 $notif_cfg = $this->busm->getValue($nation_code, $buyer->id, $classified, $notif_code);
//                 if (isset($notif_cfg->setting_value)) {
//                     $setting_value = (int) $notif_cfg->setting_value;
//                 }

//                 $type = 'transaction';
//                 $anotid = '3';
//                 $replacer = array();
//                 $replacer['invoice_code'] = $orderSellerConfirmed->invoice_code;
//                 $replacer['shipment_service'] = $orderSellerConfirmed->shipment_service;

//                 //by Donny Dennison - 9 october 2020 21:23
//             	// receipt number not showing
//                 $replacer['order_name'] = html_entity_decode($orderSellerConfirmed->nama,ENT_QUOTES);
//                 $replacer['shipment_tranid'] = $orderSellerConfirmed->shipment_tranid;

//                 //push notif for buyer
//                 if (strlen($buyer->fcm_token) > 50 && !empty($setting_value)) {
//                     $device = $buyer->device;
//                     $tokens = array($buyer->fcm_token);
//                     if($buyer->language_id == 2) {
//                         $title = 'Pengiriman sedang berlangsung!';

//                         if(strtolower($orderSellerConfirmed->shipment_service) == 'direct delivery'){
//                             $message = "Pesanan Anda dengan nomor faktur $orderSellerConfirmed->invoice_code sudah disiapkan oleh penjual. Silakan hubungi dia sekarang.";
//                         }else{
//                             $message = "Pesanan Anda dengan nomor faktur $orderSellerConfirmed->invoice_code telah dikirim oleh penjual menggunakan kurir dari $orderSellerConfirmed->shipment_service";
//                         }
//                     } else {
//                         $title = 'Delivery in progress!';

//                         if(strtolower($orderSellerConfirmed->shipment_service) == 'direct delivery'){
//                             $message = "Your order with invoice number $orderSellerConfirmed->invoice_code already prepared by the seller. Please contact him now.";
//                         }else{
//                             $message = "Your order with invoice number $orderSellerConfirmed->invoice_code has been sent by the seller using a courier from $orderSellerConfirmed->shipment_service";
//                         }
//                     }
                    
//                     $type = 'transaction';
//                     $image = 'media/pemberitahuan/transaction.png';
//                     $payload = new stdClass();
//                     $payload->id_produk = "".$c_produk_id;
//                     $payload->id_order = "".$d_order_id;
//                     $payload->id_order_detail = "".$c_produk_id;
//                     $payload->b_user_id_buyer = $buyer->id;
//                     $payload->b_user_fnama_buyer = $buyer->fnama;
                    
// 					// by Muhammad Sofi - 27 October 2021 10:12
// 					// if user img & banner not exist or empty, change to default image
// 					// $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
// 					if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
// 						$payload->b_user_image_buyer = $this->cdn_url($buyer->image);
// 					} else {
// 						$payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
// 					}
//                     $payload->b_user_id_seller = $seller->id;
//                     $payload->b_user_fnama_seller = $seller->fnama;
                    
// 					// by Muhammad Sofi - 27 October 2021 10:12
// 					// if user img & banner not exist or empty, change to default image
// 					// $payload->b_user_image_seller = $this->cdn_url($seller->image);
// 					if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
// 						$payload->b_user_image_seller = $this->cdn_url($seller->image);
// 					} else {
// 						$payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
// 					}
//                     $nw = $this->anot->get($nation_code, 'push', $type, $anotid, $buyer->language_id);
//                     if (isset($nw->title)) {
//                         $title = $nw->title;
//                     }
//                     if (isset($nw->message) && strtolower($orderSellerConfirmed->shipment_service) != 'direct delivery') {
//                         $message = $this->__nRep($nw->message, $replacer);
//                     }
//                     if (isset($nw->image)) {
//                         $image = $nw->image;
//                     }
//                     $image = $this->cdn_url($image);
//                     $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
//                     $this->seme_log->write("api_cron", 'API_Cron/Thirty_Min::index __pushNotif: '.json_encode($res));
//                 }

//                 //collect array notification list for buyer
//                 $replacer['order_name'] = html_entity_decode($orderSellerConfirmed->nama,ENT_QUOTES);
//                 $replacer['shipment_tranid'] = $orderSellerConfirmed->shipment_tranid;
//                 $dpe = array();
//                 $dpe['nation_code'] = $nation_code;
//                 $dpe['b_user_id'] = $buyer->id;
//                 $dpe['id'] = $this->dpem->getLastId($nation_code, $buyer->id);
//                 $dpe['type'] = "transaction";
//                 if($buyer->language_id == 2) {
//                     $dpe['judul'] = "Pengiriman sedang berlangsung";

//                     if(strtolower($orderSellerConfirmed->shipment_service) == 'direct delivery'){
//                         $dpe['teks'] = "Pesanan Anda ".html_entity_decode($orderSellerConfirmed->nama,ENT_QUOTES)." ($orderSellerConfirmed->invoice_code) sudah siap oleh penjual. Silakan hubungi dia sekarang.";
//                     }else{
//                         $dpe['teks'] = "Pesanan Anda ".html_entity_decode($orderSellerConfirmed->nama,ENT_QUOTES)." ($orderSellerConfirmed->invoice_code) telah dikirim oleh penjual menggunakan kurir dari $orderSellerConfirmed->shipment_service.";
//                     }
//                 } else {
//                     $dpe['judul'] = "Delivery in Progress";

//                     if(strtolower($orderSellerConfirmed->shipment_service) == 'direct delivery'){
//                         $dpe['teks'] = "Your order ".html_entity_decode($orderSellerConfirmed->nama,ENT_QUOTES)." ($orderSellerConfirmed->invoice_code) is ready by the seller. Please contact him/her now.";
//                     }else{
//                         $dpe['teks'] = "Your order ".html_entity_decode($orderSellerConfirmed->nama,ENT_QUOTES)." ($orderSellerConfirmed->invoice_code) has been sent by the seller using a courier from $orderSellerConfirmed->shipment_service.";
//                     }
//                 }
                

//                 $dpe['cdate'] = "NOW()";
//                 $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
//                 $extras = new stdClass();
//                 $extras->id_order = "".$d_order_id;
//                 $extras->id_produk = "".$c_produk_id;
//                 $extras->id_order_detail = "".$c_produk_id;
//                 $extras->b_user_id_buyer = $buyer->id;
//                 $extras->b_user_fnama_buyer = $buyer->fnama;
                
// 				// by Muhammad Sofi - 27 October 2021 10:12
// 				// if user img & banner not exist or empty, change to default image
// 				// $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
// 				if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
// 					$extras->b_user_image_buyer = $this->cdn_url($buyer->image);
// 				} else {
// 					$extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
// 				}
//                 $extras->b_user_id_seller = $seller->id;
//                 $extras->b_user_fnama_seller = $seller->fnama;
                
// 				// by Muhammad Sofi - 27 October 2021 10:12
// 				// if user img & banner not exist or empty, change to default image
// 				// $extras->b_user_image_seller = $this->cdn_url($seller->image);
// 				if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
// 					$extras->b_user_image_seller = $this->cdn_url($seller->image);
// 				} else {
// 					$extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
// 				}
//                 $nw = $this->anot->get($nation_code, "list", $type, $anotid, $buyer->language_id);
//                 if (isset($nw->title)) {
//                     $dpe['judul'] = $nw->title;
//                 }
//                 if (isset($nw->message) && strtolower($orderSellerConfirmed->shipment_service) != 'direct delivery') {
//                     $dpe['teks'] = $this->__nRep($nw->message, $replacer);
//                 }
//                 if (isset($nw->image)) {
//                     $dpe['gambar'] = $nw->image;
//                 }
//                 // $dpe['gambar'] = $this->cdn_url($dpe['gambar']);
//                 $dpe['extras'] = json_encode($extras);
//                 $this->dpem->set($dpe);
//                 $this->order->trans_commit();

//                 //send email for buyer
//                 if ($this->email_send && strlen($buyer->email)>4) {
//                     $replacer = array();
//                     $replacer['site_name'] = $this->app_name;
//                     $replacer['fnama'] = $buyer->fnama;
//                     $replacer['produk_nama'] = html_entity_decode($orderSellerConfirmed->nama,ENT_QUOTES);
//                     $replacer['invoice_code'] = $orderSellerConfirmed->invoice_code;
//                     $replacer['shipment_tranid'] = $orderSellerConfirmed->shipment_tranid;
//                     $replacer['shipment_service'] = $orderSellerConfirmed->shipment_service;
//                     $this->seme_email->flush();
//                     $this->seme_email->replyto($this->site_name, $this->site_replyto);
//                     $this->seme_email->from($this->site_email, $this->site_name);
//                     $this->seme_email->subject('Delivery in Progress');
//                     $this->seme_email->to($buyer->email, $buyer->fnama);
//                     if(strtolower($orderSellerConfirmed->shipment_service) == 'direct delivery'){
//                         $this->seme_email->template('delivery_in_progress_direct_delivery');
//                     }else{
//                         $this->seme_email->template('delivery_in_progress');
//                     }
//                     $this->seme_email->replacer($replacer);
//                     $this->seme_email->send();
//                 }

//                 //get notification config for seller
//                 $setting_value = 0;
//                 $classified = 'setting_notification_seller';
//                 $notif_code = 'S1';
//                 $notif_cfg = $this->busm->getValue($nation_code, $seller->id, $classified, $notif_code);
//                 if (isset($notif_cfg->setting_value)) {
//                     $setting_value = (int) $notif_cfg->setting_value;
//                 }

//                 //push notif for seller
//                 $type = 'transaction';
//                 $anotid = '4';
//                 $replacer['invoice_code'] = $orderSellerConfirmed->invoice_code;
//                 if (strlen($seller->fcm_token) > 50 && !empty($setting_value)) {
//                     $device = $seller->device;
//                     $tokens = array($seller->fcm_token);
//                     if($seller->language_id == 2) {
//                         $title = 'Pengiriman sedang berlangsung!';

//                         if(strtolower($orderSellerConfirmed->shipment_service) == 'direct delivery'){
//                             $message = "Produk Anda dengan nomor faktur $orderSellerConfirmed->invoice_code perlu dikirimkan secara langsung. Silahkan hubungi pembeli.";
//                         }else{
//                             $message = "Produk Anda dengan nomor faktur $orderSellerConfirmed->invoice_code sedang dikirim.";
//                         }
//                     } else {
//                         $title = 'Delivery in progress!';

//                         if(strtolower($orderSellerConfirmed->shipment_service) == 'direct delivery'){
//                             $message = "Your product with invoice number $orderSellerConfirmed->invoice_code need to be sent directly. Please contact the buyer.";
//                         }else{
//                             $message = "Your product with invoice number $orderSellerConfirmed->invoice_code being sent.";
//                         }
//                     }
                    

//                     $type = 'transaction';
//                     $image = 'media/pemberitahuan/transaction.png';
//                     $payload = new stdClass();
//                     $payload->id_produk = "".$c_produk_id;
//                     $payload->id_order = "".$d_order_id;
//                     $payload->id_order_detail = "".$c_produk_id;
//                     $payload->b_user_id_buyer = $buyer->id;
//                     $payload->b_user_fnama_buyer = $buyer->fnama;
                    
// 					// by Muhammad Sofi - 27 October 2021 10:12
// 					// if user img & banner not exist or empty, change to default image
// 					// $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
// 					if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
// 						$payload->b_user_image_buyer = $this->cdn_url($buyer->image);
// 					} else {
// 						$payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
// 					}
//                     $payload->b_user_id_seller = $seller->id;
//                     $payload->b_user_fnama_seller = $seller->fnama;
                    
// 					// by Muhammad Sofi - 27 October 2021 10:12
// 					// if user img & banner not exist or empty, change to default image
// 					// $payload->b_user_image_seller = $this->cdn_url($seller->image);
// 					if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
// 						$payload->b_user_image_seller = $this->cdn_url($seller->image);
// 					} else {
// 						$payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
// 					}
//                     $nw = $this->anot->get($nation_code, 'push', $type, $anotid, $seller->language_id);
//                     if (isset($nw->title)) {
//                         $title = $nw->title;
//                     }
//                     if (isset($nw->message) && strtolower($orderSellerConfirmed->shipment_service) != 'direct delivery') {
//                         $message = $this->__nRep($nw->message, $replacer);
//                     }
//                     if (isset($nw->image)) {
//                         $image = $nw->image;
//                     }
//                     $image = $this->cdn_url($image);

//                     $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
//                     if ($this->is_log) {
//                         $this->seme_log->write("api_cron", 'API_Cron/Thirty_Min::index __pushNotif: '.json_encode($res));
//                     }
//                 }

//                 //collect array notification list for seller
//                 $dpe = array();
//                 $dpe['nation_code'] = $nation_code;
//                 $dpe['b_user_id'] = $seller->id;
//                 $dpe['id'] = $this->dpem->getLastId($nation_code, $seller->id);
//                 $dpe['type'] = "transaction";
//                 if($seller->language_id == 2) {
//                     $dpe['judul'] = "Pengiriman sedang berlangsung!";
                
//                     if(strtolower($orderSellerConfirmed->shipment_service) == 'direct delivery'){
//                         $dpe['teks'] = "Produk Anda dengan nomor faktur $orderSellerConfirmed->invoice_code perlu dikirimkan secara langsung. Silahkan hubungi pembeli.";
//                     }else{
//                         $dpe['teks'] = "Produk Anda dengan nomor faktur $orderSellerConfirmed->invoice_code sedang dikirim.";
//                     }
//                 } else {
//                     $dpe['judul'] = "Delivery in progress!";
                
//                     if(strtolower($orderSellerConfirmed->shipment_service) == 'direct delivery'){
//                         $dpe['teks'] = "Your product with invoice number $orderSellerConfirmed->invoice_code needs to deliver directly.Please contact the buyer.";
//                     }else{
//                         $dpe['teks'] = "Your product with invoice number $orderSellerConfirmed->invoice_code is being delivered.";
//                     }
//                 }
                

//                 $dpe['cdate'] = "NOW()";
//                 $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
//                 $extras = new stdClass();
//                 $extras->id_order = "".$d_order_id;
//                 $extras->id_produk = "".$c_produk_id;
//                 $extras->id_order_detail = "".$c_produk_id;
//                 $extras->b_user_id_buyer = $buyer->id;
//                 $extras->b_user_fnama_buyer = $buyer->fnama;
                
// 				// by Muhammad Sofi - 27 October 2021 10:12
// 				// if user img & banner not exist or empty, change to default image
// 				// $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
// 				if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
// 					$extras->b_user_image_buyer = $this->cdn_url($buyer->image);
// 				} else {
// 					$extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
// 				}
//                 $extras->b_user_id_seller = $seller->id;
//                 $extras->b_user_fnama_seller = $seller->fnama;
                
// 				// by Muhammad Sofi - 27 October 2021 10:12
// 				// if user img & banner not exist or empty, change to default image
// 				// $extras->b_user_image_seller = $this->cdn_url($seller->image);
// 				if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
// 					$extras->b_user_image_seller = $this->cdn_url($seller->image);
// 				} else {
// 					$extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
// 				}
//                 $nw = $this->anot->get($nation_code, "list", $type, $anotid, $seller->language_id);
//                 if (isset($nw->title)) {
//                     $dpe['judul'] = $nw->title;
//                 }
//                 if (isset($nw->message) && strtolower($orderSellerConfirmed->shipment_service) != 'direct delivery') {
//                     $dpe['teks'] = $this->__nRep($nw->message, $replacer);
//                 }
//                 if (isset($nw->image)) {
//                     $dpe['gambar'] = $nw->image;
//                 }
//                 // $dpe['gambar'] = $this->cdn_url($dpe['gambar']);
//                 $dpe['extras'] = json_encode($extras);
//                 $this->dpem->set($dpe);
//                 $this->order->trans_commit();

//     		}
             
//         //END copy from api_mobile/seller/order/delivery_process   
            
//             }//end foreach

//         }//end data count

//         //end transacation
//         $this->order->trans_end();
//     }
// }
