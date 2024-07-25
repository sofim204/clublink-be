<?php
//switch code_bank {
//         case 1:
//             return .visa
//         case 2:
//             return .mastercard
//         case 3:
//             return .americanExpress
//         case 4:
//             return .unionPay
//         case 5:
//             return .jcb
//         case 6:
//             return .discover
//         case 7:
//             return .dinners
// }

require_once (SENEROOT.'assets/php-jwt-master/src/JWT.php');
use Firebase\JWT\JWT;

/**
 * Payment Process API_Mobile
 */
class Payment extends JI_Controller
{
    public $is_softfail = 0;
    public $is_log = 1;
    public $email_send = 1;

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->lib('seme_email');

        //by Donny Dennison - 
        //improvment(for-flutter)
        $this->lib('PGW_MERCHANT_SERVER_PHP_v2.0.0\utils\PaymentGatewayHelper','pgh');

        $this->load("api_mobile/a_negara_model", 'anm');
        $this->load("api_mobile/a_notification_model", 'anot');
        $this->load("api_mobile/b_user_model", 'bu');
        $this->load("api_mobile/b_user_alamat_model", 'bua');
        $this->load("api_mobile/b_user_setting_model", 'busm');
        $this->load("api_mobile/b_user_bankacc_model", 'bubam');
        $this->load("api_mobile/common_code_model", 'ccm');
        $this->load("api_mobile/c_produk_model", 'cpm');
        $this->load("api_mobile/d_cart_model", 'cart');
        $this->load("api_mobile/d_order_model", 'order');
        $this->load("api_mobile/d_order_alamat_model", 'doam');
        $this->load("api_mobile/d_order_detail_model", 'dodm');
        $this->load("api_mobile/d_order_detail_pickup_model", 'dodpu');
        $this->load("api_mobile/d_order_proses_model", 'dopm');
        $this->load("api_mobile/d_pemberitahuan_model", 'dpem');
        $this->load("api_mobile/d_order_detail_item_model", 'dodim');
        $this->load("api_mobile/e_rating_model", 'erm');

        //by Donny Dennison - 28 october 2021 10:48
        //payment call 2c2p in api for flutter version
        $this->load("api_mobile/b_user_card_model", 'bucm');

    }

    // private function __shipmentCheck($qty, $berat, $dimension_long, $dimension_width, $dimension_height)
    // {
    //     $shipments = array();
    //     $berat_total = $qty*$berat;
    //     $dl_total = $dimension_long*$qty;
    //     $dw_total = $dimension_width*$qty;
    //     $dh_total = $dimension_height*$qty;
    //     if ($berat_total<=$this->$this->berat_max_qxpress) {
    //         if ($dl_total<=$this->length_max_qxpress && $dw_total<=$this->length_max_qxpress && $dh_total<=$this->length_max_qxpress) {
    //             $sh = new stdClass();
    //             $sh->shipment_service = 'QXpress';
    //             $sh->shipment_type = 'Next Day';
    //             $sh->shipment_cost = 5.00;
    //             $sh->shipment_cost_add = 0.00;
    //             $shipments[] = $sh;
    //         }
    //         $sh = new stdClass();

    //         //by Donny Dennison - 15 september 2020 17:45
    //         //change name, image, etc from gogovan to gogox
    //         // $sh->shipment_service = 'Gogovan';
    //         $sh->shipment_service = 'Gogox';

    //         $sh->shipment_type = 'Next Day';
    //         $sh->shipment_cost = 25.00;
    //         $sh->shipment_cost_add = 0.00;
    //         $shipments[] = $sh;
    //     } else {
    //         $sh = new stdClass();
            
    //         //by Donny Dennison - 15 september 2020 17:45
    //         //change name, image, etc from gogovan to gogox
    //         // $sh->shipment_service = 'Gogovan';
    //         $sh->shipment_service = 'Gogox';

    //         $sh->shipment_type = 'Next Day';
    //         $sh->shipment_cost = 30.00;
    //         $sh->shipment_cost_add = 0.00;
    //         $shipments[] = $sh;
    //     }
    // }

    // private function __orderAddresses($nation_code, $pelanggan, $order)
    // {
    //     //addresses init
    //     $addresses = new stdClass();
    //     $addresses->billing = new stdClass();
    //     $addresses->shipping = new stdClass();

    //     //get billing address
    //     $jenis_alamat = 'Billing Address';
    //     $address_status = $this->ccm->getByClassifiedByCodeName($nation_code, "address", $jenis_alamat);
    //     $addresses->billing = $this->doam->getByOrderIdBuyerIdStatusAddress($nation_code, $order->id, $pelanggan->id, $address_status->code);

    //     //get shipping address
    //     //by Donny Dennison - 17 juni 2020 20:18
    //     // request by Mr Jackie change Shipping Address into Receiving Address
    //     // $jenis_alamat = 'Shipping Address';
    //     $jenis_alamat = 'Receiving Address';
    //     $address_status = $this->ccm->getByClassifiedByCodeName($nation_code, "address", $jenis_alamat);
    //     $addresses->shipping = $this->doam->getByOrderIdBuyerIdStatusAddress($nation_code, $order->id, $pelanggan->id, $address_status->code);
    //     return $addresses;
    // }
    // private function __orderSellers($nation_code, $pelanggan, $order)
    // {
    //     $this->sub_total = 0.0;
    //     $this->ongkir_total = 0.0;
    //     $this->grand_total = 0.0;
    //     $sps = $this->dodm->getProdukAlamatByOrderId($nation_code, $order->id);
    //     $sellers = array();
    //     foreach ($sps as $product) {
    //         $pid = (int) $product->id;
    //         //url manipulator
    //         $product->foto = $this->cdn_url($product->foto);
    //         $product->thumb = $this->cdn_url($product->thumb);
            
    //         // by Muhammad Sofi - 26 October 2021 11:16
    //         // if user img & banner not exist or empty, change to default image
    //         // $product->b_user_image_seller = $this->cdn_url($product->b_user_image_seller);
    //         if(file_exists(SENEROOT.$product->b_user_image_seller) && $product->b_user_image_seller != 'media/user/default.png'){
    //             $product->b_user_image_seller = $this->cdn_url($product->b_user_image_seller);
    //         } else {
    //             $product->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
    //         }

    //         //by Donny Dennison - 23 september 2020 15:42
    //         //add direct delivery feature
    //         //START by Donny Dennison - 23 september 2020 15:42

    //         if (strtolower($product->shipment_service) == 'direct delivery') {
    //             $product->shipment_icon = $this->cdn_url("assets/images/direct_delivery.png");

    //         //by Donny Dennison - 15 september 2020 17:45
    //         //change name, image, etc from gogovan to gogox
    //         // if (strtolower($product->shipment_service) == 'gogovan') {
    //         //     $product->shipment_icon = $this->cdn_url("assets/images/gogovan.png");
    //         // if (strtolower($product->shipment_service) == 'gogox') {
    //         }else if (strtolower($product->shipment_service) == 'gogox') {
    //             $product->shipment_icon = $this->cdn_url("assets/images/gogox.png");

    //         //END by Donny Dennison - 23 september 2020 15:42

    //         } elseif (strtolower($product->shipment_service) == 'qxpress') {
    //             $product->shipment_icon = $this->cdn_url("assets/images/qxpress.png");
    //         } else {
    //             $product->shipment_icon = $this->cdn_url("assets/images/unavailable.png");
    //         }

    //         //building seller data
    //         $seller = new stdClass();
    //         $seller->nation_code = $order->nation_code;
    //         $seller->d_order_id = $order->id;
    //         $seller->b_user_id = (int) $product->b_user_id_seller;
            
    //         // by Muhammad Sofi - 28 October 2021 11:00
    //         // if user img & banner not exist or empty, change to default image
    //         // $seller->image = $product->b_user_image_seller;
    //         if(file_exists(SENEROOT.$product->b_user_image_seller) && $product->b_user_image_seller != 'media/user/default.png'){
    //             $seller->image = $product->b_user_image_seller;
    //         } else {
    //             $seller->image = $this->cdn_url('media/user/default-profile-picture.png');
    //         }
    //         $seller->nama = $product->b_user_nama_seller;
    //         $seller->products = array();
    //         $seller->products[] = $product;
    //         if (!isset($sellers[$seller->b_user_id])) {
    //             $sellers[$seller->b_user_id] = $seller;
    //         } else {
    //             $sellers[$seller->b_user_id]->products[] = $product;
    //         }
    //         $sub_total = $product->qty * $product->harga_jual;
    //         $this->sub_total += $sub_total;
    //         $this->ongkir_total += $product->shipment_cost + $product->shipment_cost_add;
    //     }
    //     $this->grand_total = $this->sub_total + $this->ongkir_total;
    //     $sellers = array_values($sellers);
    //     return $sellers;
    // }

    private function __feeCalculation($nation_code)
    {
        //declare initial variable
        //payment gateway deduction value
        $pg_fee = 0.0;
        $pg_fee_jenis = 'percentage';
        $pg_fee_vat = 0;
        $profit = 0.0; //profit
        $profit_jenis = 'percentage';
        $asuransi = 0.0; //insurance
        $asuransi_jenis = 'percentage';
        $selling_fee_percent = 0; //selling fee

        //get preset from DB
        $fee = array();
        $presets = $this->ccm->getByClassified($nation_code, "product_fee");
        if (count($presets)>0) {
            foreach ($presets as $pre) {
                $fee[$pre->code] = $pre;
            }
            unset($pre); //free some memory
            unset($presets); //free some memory
        }

        //passing into current var
        if (isset($fee['F7']->remark)) {
            $selling_fee_percent = $fee['F7']->remark;
        }
        if (isset($fee['F0']->remark)) {
            $pg_fee = ($fee['F0']->remark);
        }
        if (isset($fee['F1']->remark)) {
            $pg_fee_jenis = $fee['F1']->remark;
        }
        if (isset($fee['F6']->remark)) {
            $pg_fee_vat = $fee['F6']->remark;
        }
        if (isset($fee['F2']->remark)) {
            $profit = ($fee['F2']->remark);
        }
        if (isset($fee['F3']->remark)) {
            $profit_jenis = $fee['F3']->remark;
        }
        if (isset($fee['F4']->remark)) {
            $asuransi = ($fee['F4']->remark);
        }
        if (isset($fee['F5']->remark)) {
            $asuransi_jenis = $fee['F5']->remark;
        }

        $fee = new stdClass();
        $fee->pg_fee = $pg_fee;
        $fee->pg_fee_jenis = $pg_fee_jenis;
        $fee->profit = $profit;
        $fee->profit_jenis = $profit_jenis;
        $fee->asuransi = $asuransi;
        $fee->asuransi_jenis = $asuransi_jenis;
        $fee->pg_fee_vat = $pg_fee_vat;
        $fee->selling_fee_percent = $selling_fee_percent;
        return $fee;
    }
    
    /**
     * Convert string encode to ISO-8859-1 or ASCII
     * @param  string $str any encoded string
     * @return string      ASCII or ISO-8859-1 string encoded
     */
    private function __cAmp(string $str)
    {
        $str = utf8_encode(trim($str));
        $enc = mb_detect_encoding($str, 'UTF-8');
        if ($enc == 'UTF-8') {
            $str = iconv($enc, 'ISO-8859-1//TRANSLIT', $str);
        } else {
            $str = iconv($enc, 'ASCII//IGNORE', $str);
        }
        return $str;
    }

    //by Donny Dennison - 2 november 2021 13:45
    //payment call 2c2p in api for flutter version
    private function __call2c2pApiRedirectDirect($data, $url)
    {
        //Construct data request
        // $data = new stdClass();
        // $data->merchantID = $this->merchantID_2c2p;
        // $data->invoiceNo = 'INV652109290003';
        // $data->description = 'Desc : Add product v2, and Juice2';
        // $data->amount = '10.00';
        // $data->currencyCode = 'SGD';
        // $data->paymentChannel = array("CC","GRAB");
        // $data->request3DS = 'Y';
        // $data->tokenize = true;
        // $data->cardTokens = array("29102115142548739443","30719155505728282");
        // $data->userDefined1 = "Buyer : vicky1";
        // $data->userDefined2 = "Product : Add product v2, and Juice2";
        // $data->frontendReturnUrl = $frontend_return_url;
        // $data->backendReturnUrl = $backend_return_url;
        // $data->nonceStr = uniqid('', true);
        // $data->uiParams = $ui_params;

        $ptr = $this->pgh->generatePayload($data, $this->secretKey_2c2p);
        
        //Do Payment Token API request
        $response_payload_json = $this->pgh->requestAPI($this->redirect_direct_payment_api_host_2c2p.$url, $ptr);

        //Check Payload availability
        if($this->pgh->containPayload($response_payload_json)) {

            // Important: Verify response payload
            if($this->pgh->validatePayload($response_payload_json, $this->secretKey_2c2p)) {
                
                //Parse response
                $payment_token_response = $this->pgh->parseAPIResponse($response_payload_json);
                
                //Get payment token and pass token to your mobile application.
                // echo $payment_token = $payment_token_response->paymentToken;
                return $payment_token_response;
          
                //Or open V4UI for web payment
                // echo $web_payment_url = $payment_token_response->webPaymentUrl;
            } else {

                //Return error response
                return 'response not matched';
            }
        } else {
            //Show error response from API
            return json_decode($response_payload_json);
        }
    }
    
    private function __activateGenerateLink($nation_code, $user_id, $token="")
    {
        $this->lib("conumtext");
        $min = 25;
        if (strlen($token)<25) {
            $token = $this->conumtext->genRand($type="str", $min, $max=30);
            $this->bu->setToken($nation_code, $user_id, $token, $kind="api_reg");
        }
        return base_url("account/activate/index/$token");
    }

    public function index()
    {
        $this->__json_out(array());
    }
    public function pre()
    {
        //initial
        $dt = $this->__init();
        $data = array();
        $data['problem'] = array();
        //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Payment::pre --beginProcess");

        $this->status = 200;
        $this->message = 'Success';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }

        if (empty($pelanggan->is_confirmed) && (strlen($pelanggan->fb_id)<=1 || strlen($pelanggan->google_id)<=1)) {
            $data = $pelanggan->is_confirmed;
            $link = $this->__activateGenerateLink($nation_code, $pelanggan->id, $pelanggan->api_reg_token);
            $email = $pelanggan->email;
            $nama = $pelanggan->fnama;
            $replacer = array();
            $replacer['site_name'] = $this->app_name;
            $replacer['fnama'] = $nama;
            $replacer['activation_link'] = $link;
            $this->seme_email->flush();
            $this->seme_email->replyto($this->site_name, $this->site_replyto);
            $this->seme_email->from($this->site_email, $this->site_name);
            $this->seme_email->subject('Please Confirm your email');
            $this->seme_email->to($email, $nama);
            $this->seme_email->template('account_register');
            $this->seme_email->replacer($replacer);
            $this->seme_email->send();

            //$this->status = 1709;
            $this->status = 412;
            //by Donny Dennison
            //Request dari Mr Jackie untuk mengganti message
            // $this->message = 'Please activate your new account by clicking your email';
            $this->message = 'Please activate your new account by clicking the link at your email';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Payment::pre INFO --userID: '.$pelanggan->id.' --unconfirmedEmail '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        // $bankacc = $this->bubam->getByUserId($nation_code, $pelanggan->id);
        // if (!isset($bankacc->a_bank_id)) {
        //     $data = "0";
        //     $this->message = 'Please activate your new account by inserting bank account';
        //     if ($this->is_log) {
        //         $this->seme_log->write("api_mobile", 'API_Mobile/Payment::pre INFO --userID: '.$pelanggan->id.' --undefinedBankAccount  '.$this->message);
        //     }
        //     //$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     //die();
        // } else {
        //     $data = "1";
        //     $this->message = 'Success';
        // }

        $d_order_id = (int) $this->input->post("d_order_id");
        if ($d_order_id<=0) {
            $this->status = 3040;
            $this->message = 'Order not found or not belong to you';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Payment::pre --forceClose $this->message");
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }

        $order = $this->order->getByIdUserId($nation_code, $pelanggan->id, $d_order_id);
        if (!isset($order->id)) {
            $this->status = 3040;
            $this->message = 'Order not found or not belong to you';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Payment::pre --forceClose $this->message");
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }

        $this->seme_log->write("api_mobile", 'API_Mobile/Payment::pre -- INFO order_status: '.$order->order_status.' -- order_status: '.$order->order_status);
        if ($order->order_status == 'cancelled') {
            $this->status = 3050;
            $this->message = 'Order confirmation was expired, please check your cart';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Payment::pre --forceClose $this->message");
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }

        if ($order->order_status != 'waiting_for_payment' && $order->order_status != 'pending') {
            $this->status = 3041;
            $this->message = 'Order already paid';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Payment::pre --forceClose $this->message");
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }

        //get current time
        // $now = strtotime("now");
        $is_expired = 0;

        //get ordered product
        $pids = array();
        $prds = array();
        // $dodm = $this->dodm->getByOrderId($nation_code, $order->id);
        // foreach ($dodm as $dod) {
        //     $exp = strtotime($dod->date_expire);
        //     if ($now>$exp) {
        //         //$is_expired = 1;
        //         if ($this->is_log) {
        //             $this->seme_log->write("api_mobile", "API_Mobile/Payment::pre --expiredOrder ID: $order->id");
        //         }
        //         break;
        //     }
        // }
        $ordered_products = $this->dodim->getByOrderId($nation_code, $order->id);
        foreach ($ordered_products as $op) {
            $pid = (int) $op->c_produk_id;
            $pids[] = $pid;
            $prds[$pid] = $op;

            if ($op->b_user_is_active == 0) {
                $this->status = 3100;
                $this->message = 'Sorry, product is not available at this time';
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Payment::pre --forceClose $this->message");
                }
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
                die();
            }
        }
        unset($op, $pid, $ordered_products);

        //validate
        // if (!empty($is_expired)) {
        //     $this->status = 3099;
        //     $this->message = 'Your order has expired';
        //     if ($this->is_log) {
        //         $this->seme_log->write("api_mobile", "API_Mobile/Payment::pre --forceClose $this->message");
        //     }
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
        //     die();
        // }
        // out of stock
        $oos = array();

        //declare product out of stock var
        $is_problem = 0;
        //get product stock qty
        $products = $this->cpm->getByIdsForCart($nation_code, $pids);
        if (count($products)) {
            foreach ($products as $pr) {
                $pid = (int) $pr->id;
                if (isset($prds[$pid])) {
                    $qty = $prds[$pid]->qty;
                    //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Payment::pre --stockVerification PID: $pid, qty: $qty, stock: $pr->stok");
                    if ($qty > $pr->stok) {
                        $is_problem = 1;
                        if (!empty($this->is_softfail)) {
                            $prd = new stdClass();
                            $prd->id = strval($pid);
                            $prd->b_user_id_seller = strval($pr->b_user_id_seller);
                            $prd->b_user_nama_seller = $pr->b_user_nama_seller;
                            
                            // by Muhammad Sofi - 26 October 2021 11:16
                            // if user img & banner not exist or empty, change to default image
                            // $prd->b_user_image_seller = $this->cdn_url($pr->b_user_image_seller);
                            if(file_exists(SENEROOT.$pr->b_user_image_seller) && $pr->b_user_image_seller != 'media/user/default.png'){
                                $prd->b_user_image_seller = $this->cdn_url($pr->b_user_image_seller);
                            } else {
                                $prd->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                            }
                            $prd->qty = $pr->qty;
                            $prd->stok = $pr->stok;
                            $prd->nama = $pr->nama;
                            $prd->foto = $this->cdn_url($pr->foto);
                            $prd->thumb = $this->cdn_url($pr->thumb);
                            $data['problem'][] = $prd;
                        } else {
                            // $this->status = 3102;
                            // $this->message = ''.$pr->nama.' is out of stock';
                            // if ($this->is_log) {
                            //     $this->seme_log->write("api_mobile", 'API_Mobile/Payment::pre -- WARN '.$pr->nama.' is out of stock');
                            // }
                            //$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
                            //die();
                            
                            $oo = new stdClass();
                            $oo->nama = $pr->nama;
                            $oos[] = $oo;
                        }
                    }
                } else {
                    //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Payment::pre --missingProduct PID: $pid, qty: $qty, stock: $pr->stok");
                    $is_problem = 1;
                    if (!empty($this->is_softfail)) {
                        $prd = new stdClass();
                        $prd->id = strval($pid);
                        $prd->b_user_id_seller = strval($pr->b_user_id_seller);
                        $prd->b_user_nama_seller = $pr->b_user_nama_seller;
                        
                        // by Muhammad Sofi - 28 October 2021 11:00
                        // if user img & banner not exist or empty, change to default image
                        // $prd->b_user_image_seller = $this->cdn_url($pr->b_user_image_seller);
                        if(file_exists(SENEROOT.$pr->b_user_image_seller) && $pr->b_user_image_seller != 'media/user/default.png'){
                            $prd->b_user_image_seller = $this->cdn_url($pr->b_user_image_seller);
                        } else {
                            $prd->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                        }
                        $prd->qty = $pr->qty;
                        $prd->stok = $pr->stok;
                        $prd->nama = $pr->nama;
                        $prd->foto = $this->cdn_url($pr->foto);
                        $prd->thumb = $this->cdn_url($pr->thumb);
                        $data['problem'][] = $prd;
                    } else {
                        // $this->status = 3103;
                        // $this->message = 'all or one of the products is empty stock are out of stock';
                        // if ($this->is_log) {
                        //     $this->seme_log->write("api_mobile", 'API_Mobile/Payment::pre -- WARN '.$this->status.' '.$this->message);
                        // }
                        //$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
                        //die();
                        $oo = new stdClass();
                        $oo->nama = $pr->nama;
                        $oos[] = $oo;
                    }
                }
            }
        }
        
        //check stock, fix issue 25-05-2020 no 8
        $oosc = count($oos);
        if ($oosc>0) {
            $du = array('order_status'=>'cancelled');
            $res = $this->order->update($nation_code, $order->id, $du);
            // if ($res) {
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Payment::pre --Order cancelled DONE");
                }
            // }

            $this->status = 1493;

            if ($oosc>1) {
                // $pw = 'product';
                // if (($oosc-1) > 1) {
                //     $pw = 'products';
                // }
                
                // $this->message = $oos[0]->nama.' is out of stock';
                if($oosc>1){
                  $this->message = '';

                  //by Donny Dennison - 2 june 2020 15:10
                  // request by mobile developer add "|" so then can rtim the product
                  $loop = 1;
                  
                  foreach($oos as $oosv){
                    
                    //by Donny Dennison - 2 june 2020 15:10
                    // request by mobile developer add "|" so then can rtim the product
                    //$this->message .= $oosv->nama.'|, ';
                    if(count($oos) == $loop){

                        $this->message .= $oosv->nama;

                    }else{

                        $this->message .= $oosv->nama.'|';

                    }
                    $loop++;

                  }
                  unset($oos, $oosv, $loop);
                  
                  //by Donny Dennison - 2 june 2020 15:10
                  // request by mobile developer add "|" so then can rtim the product
                  // $this->message = rtrim($this->message,', ');
                  // $this->message .= ' are out of stock';

                }
                
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", 'API_Mobile/Payment::pre --forceClose '.$this->status.' '.$this->message);
                }
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
                die();
            } else {
                $this->message = $oos[0]->nama.' is out of stock';
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", 'API_Mobile/Payment::pre --forceClose '.$this->status.' '.$this->message);
                }
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
                die();
            }
        }

        //last order response
        if (empty($is_problem)) {
            $this->status = 200;
            // $this->message = 'Ok, please continue to payment process';
            $this->message = 'Success';

            //update timer counter
            if (!isset($this->payment_timeout)) {

                $this->payment_timeout = "10";
                
            }
            $this->order->trans_start();

            $du = array();
            $du['date_begin'] = date("Y-m-d H:i:s", strtotime("now"));
            $du['date_expire'] = date("Y-m-d H:i:s", strtotime("+".$this->payment_timeout." minutes"));
            $this->dodm->updateByOrderId($nation_code, $d_order_id, $du);
            $this->order->trans_commit();

            if (empty($order->payment_confirmed)) {
                $pids = array();
                $ops = $this->dodim->getByOrderId($nation_code, $d_order_id);
                foreach ($ops as $op) {
                    $pids[] = $op->c_produk_id;
                    //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Payment::pre --PID: $op->c_produk_id");
                    $this->cpm->substractStok($nation_code, $op->id, $op->qty);
                    $this->order->trans_commit();
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", 'API_Mobile/Payment::pre -- INFO Product Stock Deduction PID:'.$op->id.' QTY: '.$op->qty);
                    }
                }
                unset($ops, $op);

                // $this->cart->syncQty();
                // $this->order->trans_commit();

                $c = count($pids);
                
            } else {
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", 'API_Mobile/Payment::pre -- INFO order was confirmed, stock deduction skipped');
                }
            }

            //set flag for eliminating duplicate stock deduction
            $this->order->update($nation_code, $d_order_id, array('payment_confirmed'=>'1'));
            $this->order->trans_commit();
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Payment::pre -- INFO order set as confirmed');
            }

            //declare var for cart remove
            //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Payment::pre -> PIDS_COUNT: $c");
            //remove from cart
            if ($c) {
                $res = $this->cart->delByProductIds($nation_code, $pelanggan->id, $pids);
                // if ($res) {
                    $this->order->trans_commit();
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Payment::pre --removeCart: SUCCESS");
                    }
                // } else {
                    //$success = 0;
          //$this->status = 834;
          //$this->message = 'Failed moving product from cart to order';
          //$this->cart->trans_rollback();
                // }
            }
            $this->order->trans_end();
        } else {
            $this->status = 222;
            $this->message = 'one or more of the products has a problem';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Payment::pre RESULT: $this->message");
            }
        }
        if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Payment::pre --endProcess");
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    }

    //by Donny Dennison - 2 november 2021 13:45
    //payment call 2c2p in api for flutter version
    public function callpg()
    {
        //initial
        $dt = $this->__init();
        $data = array();
        $data['webPaymentUrl'] = "";

        $this->status = 200;
        $this->message = 'Success';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }

        $d_order_id = (int) $this->input->post("d_order_id");
        if ($d_order_id<=0) {
            $this->status = 3040;
            $this->message = 'Order not found or not belong to you';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Payment::callpg --forceClose $this->message");
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }

        $order = $this->order->getByIdUserId($nation_code, $pelanggan->id, $d_order_id);
        if (!isset($order->id)) {
            $this->status = 3040;
            $this->message = 'Order not found or not belong to you';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Payment::callpg --forceClose $this->message");
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }

        $order = $this->order->getOrderBuyerById($nation_code, $d_order_id);

        $this->seme_log->write("api_mobile", 'API_Mobile/Payment::callpg -- INFO order_status: '.$order->order_status.' -- order_status: '.$order->order_status);
        if ($order->order_status == 'cancelled') {
            $this->status = 3050;
            $this->message = 'Order confirmation was expired, please check your cart';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Payment::callpg --forceClose $this->message");
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }

        if ($order->order_status != 'waiting_for_payment' && $order->order_status != 'pending') {
            $this->status = 3041;
            $this->message = 'Order already paid';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Payment::callpg --forceClose $this->message");
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }

        if($order->webPaymentUrl != ''){

            $data['webPaymentUrl'] = $order->webPaymentUrl;

        }else{

            $listCardToken = array();
            //get credit card token
            $listCard = $this->bucm->getAll($nation_code, $order->b_user_id);
            foreach($listCard AS $card){
                $listCardToken[] = $card->token_result;
            }

            $productNameLong = 'product : ';
            $maxStrlen = 1019;

            //get ordered product
            $dodm = $this->dodm->getByOrderId($nation_code, $order->id);
            foreach ($dodm as $dod) {
                
                if(strlen($productNameLong) >=$maxStrlen){
                    break;
                }

                $productNameLong .= html_entity_decode($dod->c_produk_nama,ENT_QUOTES).", ";

            }

            //beautify the string
            $productNameLong = substr($productNameLong, 0, strlen($productNameLong)-2);
            $productNameLong = substr($productNameLong, 0, $maxStrlen);

            $productNameDesc = substr_replace($productNameLong, "Desc", 0, 7);
            $productNameDesc = substr($productNameDesc, 0, 255);

            //Construct data request
            $request = new stdClass();
            $request->merchantID = $this->merchantID_2c2p;
            $request->invoiceNo = date('YmdHis',strtotime("now"));
            $request->description = $productNameDesc;
            $request->amount = $order->grand_total;
            $request->currencyCode = 'SGD';
            $request->paymentChannel = array("CC","GRAB");
            $request->request3DS = 'Y';
            $request->tokenize = true;
            $request->cardTokens = $listCardToken;
            $request->userDefined1 = "Buyer : ".$order->b_user_fnama_buyer. " ".$order->invoice_code;
            $request->userDefined2 = substr($productNameLong, 0, 254);

            if(strlen($productNameLong) > 254){
                $request->userDefined3 = substr($productNameLong, 255, 509);
            }

            if(strlen($productNameLong) > 509){
                $request->userDefined4 = substr($productNameLong, 510, 764);
            }

            if(strlen($productNameLong) > 764){
                $request->userDefined5 = substr($productNameLong, 765, $maxStrlen);
            }

            $request->backendReturnUrl = base_url()."api_mobile/payment/process";
            $request->paymentExpiry = date('Y-m-d H:i:s',strtotime('+'.$this->payment_timeout.' minutes'));

            $response2c2p = $this->__call2c2pApiRedirectDirect($request, "paymentToken");
            
            if ($response2c2p->respCode == 0000) {
                $this->status = 200;
                // $this->message = 'Ok, please continue to payment process';
                $this->message = 'Success';

                $this->order->trans_start();

                $du = array();
                $du['is_countdown'] = 1;
                $du['cdate'] = 'NOW()';
                $du['webPaymentUrl'] = $response2c2p->webPaymentUrl;
                $du['paymentToken'] = $response2c2p->paymentToken;
                $du['payment_tranid'] = $request->invoiceNo;
                $this->order->update($nation_code, $d_order_id, $du);
                $this->order->trans_commit();

                $du = array();
                $du['date_begin'] = date("Y-m-d H:i:s", strtotime("now"));
                $du['date_expire'] = date("Y-m-d H:i:s", strtotime("+".$this->payment_timeout." minutes"));
                $this->dodm->updateByOrderId($nation_code, $d_order_id, $du);
                $this->order->trans_commit();

                $this->order->trans_end();

                $data['webPaymentUrl'] = $response2c2p->webPaymentUrl;

            } else {
                $this->status = 223;
                $this->message = $response2c2p->respCode." - ".$response2c2p->respDesc;
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Payment::callpg DATA: ".json_encode($request));
                    $this->seme_log->write("api_mobile", "API_Mobile/Payment::callpg RESULT: $this->message");
                }
            }

        }

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    }

    //by Donny Dennison - 2 november 2021 13:45
    //payment call 2c2p in api for flutter version
    // /**
    //  * Payment processing
    //  *   Remove cart content
    //  *   Sent Notification
    //  *   Renew time counter
    //  *   d_order.status = forward_to_seller
    //  *   Add pickup address to d_order_detail_pickup table
    //  *   last error code: 3037
    //  * @return [type] [description]
    //  */

    //     $request = file_get_contents('php://input');
    //     $decoded = json_decode($request,true);
    //     $payloadResponse = $decoded['payload'];
    //     $request = JWT::decode($payloadResponse, $this->secretKey_2c2p, array('HS256'));
       
    //     echo $request->respCode;
    // public function process()
    // {
    //     //initial
    //     $dt = $this->__init();
    //     $data = array();
    //     $data['order'] = new stdClass();
    //     $data['order']->addresses = new stdClass();
    //     $data['order']->sellers = array();

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }
    //     $negara = $this->anm->getByNationCode($nation_code);
    //     if (isset($negara->nama)) {
    //         $this->negara = $negara->nama;
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }

    //     //check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = new stdClass();
    //     if (strlen($apisess)>4) {
    //         $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     }
    //     if (!isset($pelanggan->id)) {
    //         $this->status = 401;
    //         $this->message = 'Missing or invalid API session';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }
    //     if ($this->is_log) {
    //         $this->seme_log->write("api_mobile", "API_Mobile/Payment::process --beginProcess");
    //     }

    //     $d_order_id = (int) $this->input->post("d_order_id");
    //     if ($d_order_id<=0) {
                // $this->status = 3040;
                // $this->message = 'Order not found or not belong to you';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }
    //     $order = $this->order->getByIdUserId($nation_code, $pelanggan->id, $d_order_id);
    //     if (!isset($order->id)) {
    //         $this->status = 3040;
    //         $this->message = 'Order not found or not belong to you';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }
    //     if ($order->order_status != 'waiting_for_payment') {
    //         $this->status = 3041;
    //         $this->message = 'Order already paid';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }
    //     //collect input
    //     $code_bank = intval($this->input->post("code_bank")); //for backward compatibilty
    //     $payment_gateway = $this->input->post("payment_service"); //for backward compatibilty
    //     if (empty($payment_gateway)) {
    //         $payment_gateway = $this->input->post("payment_gateway");
    //     }
    //     // new refactored API
    //     $payment_method = $this->input->post("payment_method");
    //     $payment_status = $this->input->post("payment_status");
    //     $payment_date = $this->input->post("payment_date");
    //     $payment_tranid = $this->input->post("payment_tranid");
    //     $payment_response = $this->input->post("payment_response");

    //     //empty validation
    //     if (empty($payment_gateway)) {
    //         $payment_gateway = '';
    //     }
    //     if (empty($payment_method)) {
    //         $payment_method = '';
    //     }
    //     if (empty($payment_status)) {
    //         $payment_status = '';
    //     }
    //     if (strlen($payment_date)!=19) {
    //         $payment_date = date("Y-m-d H:i:00", strtotime($payment_date));
    //     }
    //     if (empty($payment_tranid)) {
    //         $payment_tranid = '';
    //     }
    //     if (empty($payment_response)) {
    //         $payment_response = '';
    //     }

    //     // force payment_status to paid
    //     $payment_status = 'paid';

    //     // force order status to forward_to_seller
    //     $order_status = 'forward_to_seller';

    //     //validation
    //     if (strlen($payment_gateway)<=0) {
    //         $this->status = 3042;
    //         $this->message = 'Payment Gateway name are required';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }
    //     if (strlen($payment_method)<=0) {
    //         $this->status = 3043;
    //         $this->message = 'Payment Method name are required';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }
    //     if (strlen($payment_status)<=0) {
    //         $this->status = 3044;
    //         $this->message = 'Payment Status name required';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }
    //     if (strlen($payment_date)<=9) {
    //         $this->status = 3045;
    //         $this->message = 'required Payment Date, make sure in Y-m-d H:i:s format';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }
    //     if (strlen($payment_tranid)<=0) {
    //         $this->status = 3046;
    //         $this->message = 'Payment TranID name required';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }
    //     if (strlen($payment_response)<=0) {
    //         $this->status = 3047;
    //         $this->message = 'Payment Response required';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }

    //     //by Donny Dennison - 2 november 2020 16:03
    //     //add payment 2c2p grab pay
    //     // if (strlen($code_bank)<=0) {
    //     //     $this->status = 3048;
    //     //     $this->message = 'Code Bank required';
    //     //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //     //     die();
    //     // }

    //     //open transaction
    //     $this->order->trans_start();

    //     if ($this->is_log) {
    //         $this->seme_log->write("api_mobile", "API_Mobile/Payment::process -- POST: ".json_encode($_POST));
    //     }

    //     //collect data payment
    //     $du = array();
    //     $du['code_bank'] = $code_bank;
    //     $du['payment_gateway'] = $payment_gateway;
    //     $du['payment_method'] = $payment_method;
    //     $du['payment_status'] = $payment_status;
    //     $du['payment_date'] = $payment_date;
    //     $du['payment_tranid'] = $payment_tranid;
    //     $du['payment_response'] = $payment_response;
    //     $du['order_status'] = $order_status;

    //     //by Donny Dennison - 3 november 2020 10:37
    //     //add flag start countdown payment
    //     $du['is_countdown'] = 1;

    //     $res = $this->order->updateByUserAndOrder($nation_code, $pelanggan->id, $order->id, $du);
    //     if ($res) {
    //         $this->status = 200;
    //         $this->message = 'Success';
    //         $this->order->trans_commit();

    //         //declare var for cart remove
    //         $pids = array();
    //         $pickups = array();
    //         $ops = $this->dodm->getByOrderId($nation_code, $order->id);
    //         foreach ($ops as $op) {
    //             $pids[] = $op->c_produk_id;
    //             $pckp = $this->bua->getById($nation_code, $op->b_user_id, $op->b_user_alamat_id);
    //             $pckp->d_order_id = $op->d_order_id;
    //             $pckp->d_order_detail_id = $op->d_order_detail_id;
    //             $pckp->b_user_alamat_id = $op->id;
    //             $pickups[] = $pckp;
    //         }
    //         $c = count($pids);
    //         if ($this->is_log) {
    //             $this->seme_log->write("api_mobile", "API_Mobile/Payment::process --PIDS: $c");
    //         }
    //         //remove from cart
    //         if ($c) {
    //             $res = $this->cart->delByProductIds($nation_code, $pelanggan->id, $pids);
    //             if ($res) {
    //                 $this->cart->trans_commit();
    //             //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Payment::process --removeCart: SUCCESS");
    //             } else {
    //                 //$success = 0;
    //                 //$this->status = 834;
    //                 //$this->message = 'Failed moving product from cart to order';
    //                 //$this->cart->trans_rollback();
    //             }
    //         }

    //         // add pickup location by d_order_detail.id
    //         $mass_pickup = array();
    //         if (count($pickups)) {
    //             foreach ($pickups as $pckp) {
    //                 $mp = array();
    //                 $mp['nation_code'] = $nation_code;
    //                 $mp['d_order_id'] = $pckp->d_order_id;
    //                 $mp['d_order_detail_id'] = $pckp->d_order_detail_id;
    //                 $mp['b_user_id'] = $pckp->b_user_id;
    //                 $mp['b_user_alamat_id'] = $pckp->b_user_alamat_id;
    //                 $mp['nama'] = $pckp->penerima_nama;
    //                 $mp['telp'] = $pckp->penerima_telp;
    //                 // by Muhammad Sofi - 3 November 2021 10:00
    //                 // remark code
    //                 // $mp['alamat'] = $pckp->alamat;
    //                 $mp['alamat2'] = $pckp->alamat2;
    //                 $mp['kelurahan'] = $pckp->kelurahan;
    //                 $mp['kecamatan'] = $pckp->kecamatan;
    //                 $mp['kabkota'] = $pckp->kabkota;
    //                 $mp['provinsi'] = $pckp->provinsi;
    //                 $mp['negara'] = $pckp->negara;
    //                 $mp['kodepos'] = $pckp->kodepos;
    //                 $mp['latitude'] = $pckp->latitude;
    //                 $mp['longitude'] = $pckp->longitude;
    //                 $mp['catatan'] = $pckp->catatan;
    //                 $mass_pickup[] = $mp;
    //             }
    //             unset($pckp);
    //             unset($mp);
    //             unset($pickups);
    //         }
    //         if (count($mass_pickup)) {
    //             foreach ($mass_pickup as $mp) {
    //                 $this->dodpu->set($mp);
    //             }
    //             //$this->dodpu->setMass($mass_pickup);
    //         }

    //         //update forward to seller time
    //         if (!isset($this->seller_timeout)) {
    //             $this->seller_timeout = 12;
    //         }
    //         $now = date("Y-m-d H:i:s", strtotime("now"));
    //         $dux = array();
    //         $dux['forward_to_seller_date'] = $now;
    //         $dux['date_begin'] = $now;

    //         //by Donny Dennison - 30 July 2020 13:21
    //         //change auto reject become auto confirm
    //         //START change by Donny Dennison - 30 july 2020 13:21

    //         //by Donny Dennison - 26 Juni 2020 21:05
    //         //Request by Mr Jackie, change expire for seller become 22:30 everyday
    //         // $dux['date_expire'] = date("Y-m-d H:i:s", strtotime("+".$this->seller_timeout." minutes"));
    //         // if(date('H:i:s') >= '22:30:00'){
    //         //     $dux['date_expire'] = date("Y-m-d 22:30:00", strtotime("+1 day"));
    //         // }else{
    //         //     $dux['date_expire'] = date("Y-m-d 22:30:00");
    //         // }
    //         if(date('H:i:s') >= '22:53:00'){
    //             $dux['date_expire'] = date("Y-m-d 22:53:00", strtotime("+1 day"));
    //         }else{
    //             $dux['date_expire'] = date("Y-m-d 22:53:00");
    //         }

    //         //END change by Donny Dennison - 30 july 2020 13:21

    //         $this->dodm->updateByOrderId($nation_code, $order->id, $dux);
    //         $this->order->trans_commit();

    //         //for sellers notification
    //         $pids = array();
    //         $penjuals = array();
    //         $ordered_products = $this->dodim->getByOrderId($nation_code, $order->id);
    //         foreach ($ordered_products as $product) {
    //             $penj_id = (int) $product->b_user_id_seller;
    //             if (!isset($penjuals[$penj_id])) {
    //                 $nama = '';
    //                 $penj = $this->bu->getById($nation_code, $penj_id);
    //                 $penjual = new stdClass();
    //                 $penjual->id = $penj->id;
    //                 $penjual->fnama = $penj->fnama;
    //                 $penjual->email = $penj->email;
    //                 $penjual->fcm_token = $penj->fcm_token;
    //                 $penjual->device = $penj->device;

    //                 // by Muhammad Sofi - 26 October 2021 11:16
    //                 // if user img & banner not exist or empty, change to default image
    //                 // $penjual->image = $penj->image;
    //                 if(file_exists(SENEROOT.$penj->image) && $penj->image != 'media/user/default.png'){
    //                     $penjual->image = $penj->image;
    //                 } else {
    //                     $penjual->image = $this->cdn_url('media/user/default-profile-picture.png');
    //                 }
    //                 $penjual->d_order_id = $product->d_order_id;
    //                 $penjual->d_order_detail_id = $product->d_order_detail_id;
    //                 $penjual->c_produk_nama = '';
    //                 $penjual->produk = array();
    //                 $penjuals[$penj_id] = $penjual;
    //             }
    //             $penjual->c_produk_nama .= $product->c_produk_nama.', ';
    //             $penjuals[$penj_id]->produk[] = $product;
    //             $pids[] = $product->id;
    //         }
    //         $penjual->c_produk_nama = rtrim($penjual->c_produk_nama, ', ');

    //         //clear cart
    //         $res_cart = $this->cart->delByProductIds($nation_code, $pelanggan->id, $pids);
    //         if ($res_cart) {
    //             $this->order->trans_commit();
    //             if ($this->is_log) {
    //                 $this->seme_log->write("api_mobile", "API_Mobile/Payment::process -- cart cleared successfully");
    //             }
    //         }

    //         //get notification config for buyer
    //         $type = 'transaction';
    //         $anotid = 18;
    //         $replacer = array();
    //         $replacer['invoice_code'] = $order->invoice_code;
    //         $replacer['pelanggan_fnama'] = $pelanggan->fnama;
    //         $setting_value = 0;
    //         $classified = 'setting_notification_buyer';
    //         $notif_code = 'B1';
    //         $notif_cfg = $this->busm->getValue($nation_code, $pelanggan->id, $classified, $notif_code);
    //         if (isset($notif_cfg->setting_value)) {
    //             $setting_value = (int) $notif_cfg->setting_value;
    //         }
    //         if ($this->is_log) {
    //             $this->seme_log->write("api_mobile", "API_Mobile/Payment::process --pushNotifConfig, F: B, UID: $pelanggan->id, Classified: $classified, Code: $notif_code, value: $setting_value");
    //         }

    //         $ordered_products = $this->dodm->getByOrderId($nation_code, $order->id);

    //         //push notif seller
    //         $pji=0;
    //         foreach ($ordered_products as $op) {
    //             //push notif for buyer
    //             if (strlen($pelanggan->fcm_token) > 50 && !empty($setting_value)) {
    //                 $device = $pelanggan->device;
    //                 $tokens = array($pelanggan->fcm_token);
    //                 $title = 'Waiting for Confirmation';
    //                 $message = "You have already completed a payment for the following invoice: $order->invoice_code";
    //                 $image = 'media/pemberitahuan/transaction.png';
    //                 $payload = new stdClass();
    //                 $payload->id_produk = "".$op->d_order_detail_id;
    //                 $payload->id_order = "".$order->id;
    //                 $payload->id_order_detail = "".$op->d_order_detail_id;
    //                 $payload->b_user_id_buyer = $pelanggan->id;
    //                 $payload->b_user_fnama_buyer = $pelanggan->fnama;
                    
    //                 // by Muhammad Sofi - 28 October 2021 11:00
    //                 // if user img & banner not exist or empty, change to default image
    //                 // $payload->b_user_image_buyer = $this->cdn_url($pelanggan->image);
    //                 if(file_exists(SENEROOT.$pelanggan->image) && $pelanggan->image != 'media/user/default.png'){
    //                     $payload->b_user_image_buyer = $this->cdn_url($pelanggan->image);
    //                 } else {
    //                     $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
    //                 }
    //                 $payload->b_user_id_seller = null;
    //                 $payload->b_user_fnama_seller = null;
                    
    //                 // by Muhammad Sofi - 28 October 2021 11:00
    //                 // if user img & banner not exist or empty, change to default image
    //                 // $payload->b_user_image_seller = null;
    //                 $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
    //                 $payload->status = "waiting_confirmation";
    //                 $nw = $this->anot->get($nation_code, "push", $type, $anotid);
    //                 if (isset($nw->title)) {
    //                     $title = $nw->title;
    //                 }
    //                 if (isset($nw->message)) {
    //                     $message = $this->__nRep($nw->message, $replacer);
    //                 }
    //                 if (isset($nw->image)) {
    //                     $image = $nw->image;
    //                 }
    //                 $image = $this->cdn_url($image);
    //                 $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
    //                 if ($this->is_log) {
    //                     $this->seme_log->write("api_mobile", 'API_Mobile/Payment::process __pushNotifBuyer: '.json_encode($res));
    //                 }
    //             }
    //             $pji++;
    //             //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Payment::process -> SellerIteration-".$pji." BUID: ".$pj->id);

    //             //get notif list config from db for buyer
    //             $anotid = 18;
    //             $replacer = array();
    //             $replacer['invoice_code'] = $order->invoice_code;
    //             $replacer['pelanggan_fnama'] = $pelanggan->fnama;
    //             $replacer['c_produk_nama'] = $op->c_produk_nama;
    //             $replacer['order_cdate'] = date("l, j F Y H:i", strtotime($order->d_order_cdate));

    //             //collect array notification list for buyer
    //             $dpe = array();
    //             $dpe['nation_code'] = $nation_code;
    //             $dpe['b_user_id'] = $pelanggan->id;
    //             $dpe['id'] = $this->dpem->getLastId($nation_code, $pelanggan->id);
    //             $dpe['type'] = $type;
    //             $dpe['judul'] = "Waiting for Confirmation";
    //             $dpe['teks'] = "You have already completed a payment for the following product: $op->c_produk_nama ($order->invoice_code) ".$replacer['order_cdate'].". Please wait for the seller's confirmation.";
    //             $dpe['cdate'] = "NOW()";
    //             $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
    //             $extras = new stdClass();
    //             $extras->id_order = "".$order->id;
    //             $extras->id_produk ="". $op->d_order_detail_id;
    //             $extras->id_order_detail = "".$op->d_order_detail_id;
    //             $extras->b_user_id_buyer = $pelanggan->id;
    //             $extras->b_user_fnama_buyer = $pelanggan->fnama;
                
    //             // by Muhammad Sofi - 28 October 2021 11:00
    //             // if user img & banner not exist or empty, change to default image
    //             // $extras->b_user_image_buyer = $this->cdn_url($pelanggan->image);
    //             if(file_exists(SENEROOT.$pelanggan->image) && $pelanggan->image != 'media/user/default.png'){
    //                 $extras->b_user_image_buyer = $this->cdn_url($pelanggan->image);
    //             } else {
    //                 $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
    //             }
    //             $extras->b_user_id_seller = $op->b_user_id_seller;
    //             $extras->b_user_fnama_seller = $op->b_user_fnama_seller;
                
    //             // by Muhammad Sofi - 28 October 2021 11:00
    //             // if user img & banner not exist or empty, change to default image
    //             // $extras->b_user_image_seller = $this->cdn_url($op->b_user_image_seller);
    //             if(file_exists(SENEROOT.$op->b_user_image_seller) && $op->b_user_image_seller != 'media/user/default.png'){
    //                 $extras->b_user_image_seller = $this->cdn_url($op->b_user_image_seller);
    //             } else {
    //                 $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
    //             }
    //             $extras->status = "waiting_confirmation";
    //             $dpe['extras'] = json_encode($extras);
    //             $nw = $this->anot->get($nation_code, "list", $type, $anotid);
    //             if (isset($nw->title)) {
    //                 $dpe['judul'] = $nw->title;
    //             }
    //             if (isset($nw->message)) {
    //                 $dpe['teks'] = $this->__nRep($nw->message, $replacer);
    //             }
    //             if (isset($nw->image)) {
    //                 $dpe['gambar'] = $nw->image;
    //             }
    //             $this->dpem->set($dpe);
    //             $this->order->trans_commit();
    //             //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Payment::process -> DPE_BUYER: COMMIT");

    //             //get notif list config from db for buyer
    //             $anotid = 19;
    //             $replacer = array();
    //             $replacer['invoice_code'] = $order->invoice_code;
    //             $replacer['pelanggan_fnama'] = $pelanggan->fnama;
    //             $replacer['c_produk_nama'] = $op->c_produk_nama;
    //             $replacer['order_cdate'] = date("l, j F Y H:i", strtotime($order->d_order_cdate));

    //             //collect array notification list for seller
    //             $dpe = array();
    //             $dpe['nation_code'] = $nation_code;
    //             $dpe['b_user_id'] = $op->b_user_id_seller;
    //             $dpe['id'] = $this->dpem->getLastId($nation_code, $op->b_user_id_seller);
    //             $dpe['type'] = $type;
    //             $dpe['judul'] = "Waiting confirmation";

    //             //by Donny Dennison - 30 July 2020 13:21
    //             //change auto reject become auto confirm
    //             //START Change by Donny Dennison - 30 july 2020 13:21

    //             //by Donny Dennison - 26 Juni 2020 21:05
    //             //request by Mr Jackie, change the message from 12 hours to 10:30 pm tonight
    //             // $dpe['teks'] = "You get an order for the product ".$this->__cAmp($op->c_produk_nama).", please confirm the order immediately within the next 12 hours.";
    //             // $dpe['teks'] = "You get an order for the product ".$this->__cAmp($op->c_produk_nama).", please confirm the order immediately before 10:30 pm tonight.";
    //             $dpe['teks'] = "You get an order for the product ".$this->__cAmp($op->c_produk_nama).", please confirm the order immediately before 10:57 pm tonight.";
                
    //             //END Change by Donny Dennison - 30 july 2020 13:21

    //             $dpe['cdate'] = "NOW()";
    //             $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
    //             $extras = new stdClass();
    //             $extras->id_order = "".$order->id;
    //             $extras->id_produk = "".$op->d_order_detail_id;
    //             $extras->id_order_detail = "".$op->d_order_detail_id;
    //             $extras->b_user_id_buyer = $pelanggan->id;
    //             $extras->b_user_fnama_buyer = $pelanggan->fnama;
                
    //             // by Muhammad Sofi - 28 October 2021 11:00
    //             // if user img & banner not exist or empty, change to default image
    //             // $extras->b_user_image_buyer = $this->cdn_url($pelanggan->image);
    //             if(file_exists(SENEROOT.$pelanggan->image) && $pelanggan->image != 'media/user/default.png'){
    //                 $extras->b_user_image_buyer = $this->cdn_url($pelanggan->image);
    //             } else {
    //                 $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
    //             }
    //             $extras->b_user_id_seller = $op->b_user_id_seller;
    //             $extras->b_user_fnama_seller = $op->b_user_fnama_seller;
                
    //             // by Muhammad Sofi - 28 October 2021 11:00
    //             // if user img & banner not exist or empty, change to default image
    //             // $extras->b_user_image_seller = $this->cdn_url($op->b_user_image_seller);
    //             if(file_exists(SENEROOT.$op->b_user_image_seller) && $op->b_user_image_seller != 'media/user/default.png'){
    //                 $extras->b_user_image_seller = $this->cdn_url($op->b_user_image_seller);
    //             } else {
    //                 $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
    //             }
    //             $extras->status = "waiting_confirmation";
    //             $dpe['extras'] = json_encode($extras);
    //             $nw = $this->anot->get($nation_code, "list", $type, $anotid);
    //             if (isset($nw->title)) {
    //                 $dpe['judul'] = $nw->title;
    //             }
    //             if (isset($nw->message)) {
    //                 $dpe['teks'] = $this->__nRep($nw->message, $replacer);
    //             }
    //             if (isset($nw->image)) {
    //                 $dpe['gambar'] = $nw->image;
    //             }
    //             $this->dpem->set($dpe);
    //             $this->order->trans_commit();
    //             //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Payment::process -> DPE_SELLER: COMMIT");


    //             //collect array order history process
    //             $dop = array();
    //             $dop['nation_code'] = $nation_code;
    //             $dop['d_order_id'] = $order->id;
    //             $dop['c_produk_id'] = $op->d_order_detail_id;
    //             $dop['id'] = $this->dopm->getLastId($nation_code, $order->id, $op->d_order_detail_id);
    //             $dop['initiator'] = "buyer";
    //             $dop['nama'] = "Waiting for confirmation";
    //             $dop['deskripsi'] = "Your ordered product: $op->c_produk_nama has forwarded to seller for confirmation.";
    //             $dop['cdate'] = "NOW()";
    //             $this->dopm->set($dop);
    //             $this->order->trans_commit();
    //             //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Payment::process -> DPOM: COMMIT");


    //             //get notification config for seller
    //             $setting_value = 0;
    //             $classified = 'setting_notification_seller';
    //             $notif_code = 'S0';
    //             $notif_cfg = $this->busm->getValue($nation_code, $op->b_user_id_seller, $classified, $notif_code);
    //             if (isset($notif_cfg->setting_value)) {
    //                 $setting_value = (int) $notif_cfg->setting_value;
    //             }
    //             if ($this->is_log) {
    //                 $this->seme_log->write("api_mobile", "API_Mobile/Payment::process --pushNotifConfig, F: S, UID: $op->b_user_id_seller, Classified: $classified, Code: $notif_code, value: $setting_value");
    //             }

    //             //push notif to seller
    //             if (strlen($op->fcm_token) > 50 && !empty($setting_value)) {
    //                 $device = $op->device;
    //                 $tokens = array($op->fcm_token);
    //                 $title = 'Waiting confirmation!';

    //                 //by Donny Dennison - 30 July 2020 13:21
    //                 //change auto reject become auto confirm
    //                 //START Change by Donny Dennison - 30 july 2020 13:21
                                
    //                 //by Donny Dennison - 26 Juni 2020 21:05
    //                 //request by Mr Jackie, change the message from 12 hours to 10:30 pm tonight
    //                 // $message = "You've got an order for ".$this->__cAmp($op->c_produk_nama).", please confirm the order immediately within the next 12 hours.";
    //                 // $message = "You've got an order for ".$this->__cAmp($op->c_produk_nama).", please confirm the order immediately before 10:30 pm tonight.";
    //                 $message = "You've got an order for ".$this->__cAmp($op->c_produk_nama).", please confirm the order immediately before 10:57 pm tonight.";

    //                 //END Change by Donny Dennison - 30 july 2020 13:21
                    
    //                 $image = 'media/pemberitahuan/transaction.png';
    //                 $payload = new stdClass();
    //                 $payload->id_produk = "".$op->d_order_detail_id;
    //                 $payload->id_order = "".$order->id;
    //                 $payload->id_order_detail = "".$op->d_order_detail_id;
    //                 $payload->b_user_id_buyer = $pelanggan->id;
    //                 $payload->b_user_fnama_buyer = $pelanggan->fnama;
                    
    //                 // by Muhammad Sofi - 28 October 2021 11:00
    //                 // if user img & banner not exist or empty, change to default image
    //                 // $payload->b_user_image_buyer = $this->cdn_url($pelanggan->image);
    //                 if(file_exists(SENEROOT.$pelanggan->image) && $pelanggan->image != 'media/user/default.png'){
    //                     $payload->b_user_image_buyer = $this->cdn_url($pelanggan->image);
    //                 } else {
    //                     $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
    //                 }
    //                 $payload->b_user_id_seller = $op->b_user_id_seller;
    //                 $payload->b_user_fnama_seller = $op->b_user_fnama_seller;
                    
    //                 // by Muhammad Sofi - 28 October 2021 11:00
    //                 // if user img & banner not exist or empty, change to default image
    //                 // $payload->b_user_image_seller = $this->cdn_url($op->b_user_image_seller);
    //                 if(file_exists(SENEROOT.$op->b_user_image_seller) && $op->b_user_image_seller != 'media/user/default.png'){
    //                     $payload->b_user_image_seller = $this->cdn_url($op->b_user_image_seller);
    //                 } else {
    //                     $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
    //                 }
    //                 $payload->status = "waiting_confirmation";
    //                 $nw = $this->anot->get($nation_code, "push", $type, $anotid);
    //                 if (isset($nw->title)) {
    //                     $title = $nw->title;
    //                 }
    //                 if (isset($nw->message)) {
    //                     $message = $this->__nRep($nw->message, $replacer);
    //                 }
    //                 if (isset($nw->image)) {
    //                     $image = $nw->image;
    //                 }
    //                 $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
    //                 if ($this->is_log) {
    //                     $this->seme_log->write("api_mobile", 'API_Mobile/Payment::process __pushNotifSeller: '.json_encode($res));
    //                 }
    //             }

    //             //check rating
    //             $erm = $this->erm->check($nation_code, $order->id, $op->d_order_detail_id, $op->b_user_id_seller, $pelanggan->id);
    //             if (!isset($erm->nation_code)) {
    //                 //create rating
    //                 $der = array();
    //                 $der['nation_code'] = $nation_code;
    //                 $der['d_order_id'] = $order->id;
    //                 $der['d_order_detail_id'] = $op->d_order_detail_id;
    //                 $der['b_user_id_seller'] = $op->b_user_id_seller;
    //                 $der['b_user_id_buyer'] = $pelanggan->id;
    //                 $this->erm->set($der);
    //                 $this->order->trans_commit();
    //             }
    //         } //end penjuals

    //         //send email for buyer
    //         if ($this->email_send && strlen($pelanggan->email)>4) {
    //             $replacer = array();
    //             $replacer['site_name'] = $this->app_name;
    //             $replacer['fnama'] = $pelanggan->fnama;
    //             $replacer['invoice_code'] = $order->invoice_code;
    //             $this->seme_email->flush();
    //             $this->seme_email->replyto($this->site_name, $this->site_email_finance);
    //             $this->seme_email->from($this->site_email_finance, $this->site_name);
    //             $this->seme_email->subject('Waiting for Seller Confirmation');
    //             $this->seme_email->to($pelanggan->email, $pelanggan->fnama);
    //             $this->seme_email->template('buyer_after_payment');
    //             $this->seme_email->replacer($replacer);
    //             $this->seme_email->send();
    //             if ($this->is_log) {
    //                 $this->seme_log->write("api_mobile", "API_Mobile/Payment::process -- email sent to buyer");
    //             }
    //         }
    //     } else {
    //         $this->status = 3048;
    //         $this->message = 'Failed updating Order';
    //         $this->order->trans_rollback();
    //         if ($this->is_log) {
    //             $this->seme_log->write("api_mobile", "API_Mobile/Payment::process -- POST: ".json_encode($_POST));
    //         }
    //     }
    //     $this->order->trans_end();

    //     //doing earning calculation
    //     if ($this->status == 200) {
    //         //get ST and GT
    //         $sub_total = $order->sub_total;
    //         $payment_amount = $order->payment_amount;

    //         //get fee configuration
    //         $f = $this->__feeCalculation($nation_code);

    //         //declare var Fee and COST
    //         $pg_fee = 0.0;
    //         $pg_fee_vat = 0.0;
    //         $pg_fee_jenis = 'percentage';
    //         $cancel_fee = 0;
    //         $refund_amount = 0;
    //         $profit_amount = 0.0;
    //         $pg_fee_percent = 0.0;
    //         $profit_percent = 0.0;
    //         $selling_fee = 0.0;
    //         $selling_fee_percent = 10;

    //         //calculating Selling Fee from payment_amount
    //         if (isset($f->selling_fee_percent)) {
    //             $selling_fee_percent = $f->selling_fee_percent;
    //             $selling_fee = $sub_total * ($selling_fee_percent/100);
    //             $selling_fee = round($selling_fee, 2, PHP_ROUND_HALF_DOWN);
    //         }

    //         //get code bank
    //         $is_foreign_card = 1;
    //         $nation_card = '-';
    //         $card_type = $this->__card2Text($code_bank);
    //         if (strtolower($card_type) == 'visa' || strtolower($card_type) == 'mastercard' || strtolower($card_type) == 'jcb') {
    //             $probj = json_decode($payment_response);
    //             if (isset($probj->issuerCountry)) {
    //                 if (strtolower($probj->issuerCountry) == 'sg' || strtolower($probj->issuerCountry) == 'sgd') {
    //                     $pg_fee_percent = 2.80;
    //                     $is_foreign_card = 0;
    //                 } else {
    //                     $pg_fee_percent = 3.00;
    //                 }
    //                 $nation_card = $probj->issuerCountry;
    //             }

    //         //by Donny Dennison - 2 november 2020 16:03
    //         //add payment 2c2p grab pay
    //         //START by Donny Dennison - 2 november 2020 16:03

    //         }else if($payment_method == 'Grab Pay' && $code_bank == 0){

    //             $pg_fee_percent = 2.30;

    //         //END by Donny Dennison - 2 november 2020 16:03

    //         } else {
    //             $pg_fee_percent = $this->__card2Val($code_bank);
    //         }


    //         //calculating PG Fee from Grand total

    //         //by Donny Dennison - 2 november 2020 16:03
    //         //add payment 2c2p grab pay
    //         // $pg_fee = round($payment_amount * ($pg_fee_percent/100), 2, PHP_ROUND_HALF_DOWN);
    //         $pg_fee = floor((($payment_amount * ($pg_fee_percent/100)) * 100)) / 100;
    //         $pg_fee_temp = $payment_amount * ($pg_fee_percent/100);

    //         //logger
    //         if ($this->is_log) {
    //             $this->seme_log->write("api_mobile", "API_Mobile/Payment::process --cardType: $card_type --cardNation: $nation_card --cardMDRPercent: $pg_fee_percent --cardMDR: $pg_fee");
    //         }

    //         //calculating PG Fee VAT from PG Fee
    //         if (isset($f->pg_fee_vat)) {

	   //          //by Donny Dennison - 2 november 2020 16:03
	   //          //add payment 2c2p grab pay
    //             // $pg_fee_vat = round($pg_fee * ($f->pg_fee_vat/100), 2, PHP_ROUND_HALF_DOWN);
    //             $pg_fee_vat = floor((($pg_fee_temp * ($f->pg_fee_vat/100)) * 100)) / 100;

    //         }

    //         //get Seller earning percent
    //         $earning_fee_percent = 100 - $selling_fee_percent;

    //         //sellon profit
    //         $profit_amount = $selling_fee - ($pg_fee+$pg_fee_vat);

    //         //update to table d_order
    //         $du = array();
    //         $du['pg_fee'] = $pg_fee;
    //         $du['pg_fee_vat'] = $pg_fee_vat;
    //         $du['pg_fee_percent'] = $pg_fee_percent;
    //         $du['profit_amount'] = $profit_amount;
    //         $du['payment_card_origin'] = $nation_card;
    //         $du['selling_fee'] = $selling_fee;
    //         $du['selling_fee_percent'] = $selling_fee_percent;
    //         $res = $this->order->update($nation_code, $order->id, $du);

    //         //start shared cost calculation
    //         $odetails = $this->dodm->getByOrderId($nation_code, $d_order_id);
    //         $odetails_count = count($odetails);

    //         //by Donny Dennison - 30 july 2020 19:25
    //         // change seller fee percent to 5% if product id is more than 78 and product cdate is less than 31 august 2020
    //         $final_total_selling_fee = 0;


    //         foreach ($odetails as $op) {

    //             //by Donny Dennison - 30 july 2020 19:25
    //             // change seller fee percent to 5% if product id is more than 78 and product cdate is less than 31 august 2020
    //             // START change by Donny Dennison - 30 july 2020 19:25

    //             //get subtotal and grand_total per item
    //             // $sub_total = $op->sub_total;


    //             $orderProductDetails = $this->dodim->getByOrderIdDetailId($nation_code, $d_order_id, $op->d_order_detail_id);

    //             $selling_fee = 0;
    //             $earning_total = 0;

    //             foreach($orderProductDetails As $OrderProdDetail){

    //                 $productDetails = $this->cpm->getById($nation_code, $OrderProdDetail->c_produk_id);
                    
    //                 // if($productDetails->id >= 209 && date('Y-m-d', strtotime($productDetails->cdate)) <= '2020-08-31' ){

    //                 //     //selling fee
    //                 //     $selling_fee_temp = $OrderProdDetail->harga_jual * $OrderProdDetail->qty * (5/100);
    //                 //     $selling_fee += $selling_fee_temp;

    //                 //     $earning_total_temp = $OrderProdDetail->harga_jual * $OrderProdDetail->qty - $selling_fee_temp;
    //                 //     $earning_total += $earning_total_temp;
                    
    //                 // }else{
                        
    //                     //selling fee
    //                     $selling_fee_temp = $OrderProdDetail->harga_jual * $OrderProdDetail->qty * ($selling_fee_percent/100);
    //                     $selling_fee += $selling_fee_temp;

    //                     $earning_total_temp = $OrderProdDetail->harga_jual * $OrderProdDetail->qty - $selling_fee_temp;
    //                     $earning_total += $earning_total_temp;
                            
    //                 // }

    //             }

    //             // //selling fee
    //             // $selling_fee = $sub_total * ($selling_fee_percent/100);
    //             // $earning_total = $sub_total - $selling_fee;

    //             // END change by Donny Dennison - 30 july 2020 19:25

    //             //collect to update to database
    //             unset($du);
    //             $du = array();
    //             $du['pg_fee'] = $pg_fee;
    //             $du['pg_vat'] = $pg_fee_vat;
    //             $du['profit_amount'] = $profit_amount;
    //             $du['selling_fee'] = $selling_fee;
    //             $du['selling_fee_percent'] = $selling_fee_percent;
    //             $du['earning_total'] = $earning_total;
    //             $du['earning_percent'] = $earning_fee_percent;
    //             $res = $this->dodm->update($nation_code, $op->d_order_id, $op->d_order_detail_id, $du);
    //             if (!$res) {
    //                 $this->seme_log->write("api_mobile", "API_Mobile/Payment::process --IDS: $op->d_order_id/$op->id  --sharedCost: failed");
    //             }

    //             $final_total_selling_fee += $selling_fee;
    //         }

    //         //by Donny Dennison - 30 july 2020 19:25
    //         // change seller fee percent to 5% if product id is more than 78 and product cdate is less than 31 august 2020
    //         //START change by Donny Dennison - 30 july 2020 19:25

    //         //sellon profit
    //         $profit_amount = $final_total_selling_fee - ($pg_fee+$pg_fee_vat);

    //         //update to table d_order
    //         unset($du);
    //         $du = array();
    //         $du['profit_amount'] = $profit_amount;
    //         $du['selling_fee'] = $final_total_selling_fee;
    //         $res = $this->order->update($nation_code, $order->id, $du);

    //         foreach ($odetails as $op) {

    //             //collect to update to database
    //             unset($du);
    //             $du = array();
    //             $du['profit_amount'] = $profit_amount;
    //             $res = $this->dodm->update($nation_code, $op->d_order_id, $op->d_order_detail_id, $du);
    //             if (!$res) {
    //                 $this->seme_log->write("api_mobile", "API_Mobile/Payment::process --IDS: $op->d_order_id/$op->id  --sharedCost: failed");
    //             }
    //         }

    //         //END change by Donny Dennison - 30 july 2020 19:25

    //         //by Donny Dennison - 14 august 2020 15:53
    //         // curl to facebook every time customer doing payment order
    //         //START by Donny Dennison - 14 august 2020 15:53


    //         $ordered_products = $this->dodim->getByOrderId($nation_code, $order->id);

    //         //product that got purchased add to content and send to facebook
    //         $contentIDSentToFacebook = array();
    //         $contentProductSentToFacebook = array();
    //         $valueSentToFacebook = 0;

    //         for ($i=0; $i < count($ordered_products) ; $i++) { 

    //             $contentIDSentToFacebook[$i] = $ordered_products[$i]->c_produk_id;
                
    //             $contentProductSentToFacebook[$i]['id'] = $ordered_products[$i]->c_produk_id;
    //             $contentProductSentToFacebook[$i]['quantity'] = $ordered_products[$i]->qty;
    //             $contentProductSentToFacebook[$i]['item_price'] = $ordered_products[$i]->harga_jual;

    //             $valueSentToFacebook += $ordered_products[$i]->qty * $ordered_products[$i]->harga_jual;

    //         }

    //         //send data to facebook
    //         $postToFB= array(
              
    //           'data' => array( 

    //             array(
                
    //                 'event_name' => 'Purchase',
    //                 'event_time' => strtotime('now'),
    //                 'event_id' => 'Purchase'.date('YmdHis'),
    //                 'event_source_url' => 'https://sellon.net/product_detail.php?product_id='.$ordered_products[0]->c_produk_id,
    //                 'user_data' => array(
    //                     'client_ip_address' => '35.240.185.29',
    //                     'client_user_agent' => 'browser'
    //                 ),
    //                 'custom_data' => array(
    //                     'value' => $valueSentToFacebook,
    //                     'currency' => 'SGD',
    //                     'content_ids' => $contentIDSentToFacebook,
    //                     'contents' => $contentProductSentToFacebook,
    //                     'content_type' => 'product',
    //                     'delivery_category' => 'home_delivery'
    //                 ),
    //                 "opt_out" => 'true'
    //             )

    //           ),
    //           // 'test_event_code' =>'TEST20037'

    //         );
            

    //         $curlToFacebook = $this->__CurlFacebook($postToFB);

    //         $this->seme_log->write("api_mobile", "__CurlFacebook : Response -> ".$curlToFacebook);
            
    //         //END by Donny Dennison - 14 august 2020 15:53

    //     }

    //     if ($this->is_log) {
    //         $this->seme_log->write("api_mobile", "API_Mobile/Payment::process --endProcess RESULT: $this->message");
    //     }

    //     //last order response
    //     $data['order'] = $this->order->getByIdUserId($nation_code, $pelanggan->id, $d_order_id);
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    // }

    //by Donny Dennison - 2 november 2021 13:45
    //payment call 2c2p in api for flutter version
    public function process()
    {
        //initial
        $dt = $this->__init();
        $data = array();
        $data['order'] = new stdClass();
        $nation_code = 62;

        $request = file_get_contents('php://input');
        $this->seme_log->write("api_mobile", "API_Mobile/Payment::process -- POST: ".$request);
        $decoded = json_decode($request,true);
        $payloadResponse = $decoded['payload'];
        $request = JWT::decode($payloadResponse, $this->secretKey_2c2p, array('HS256'));
        $this->seme_log->write("api_mobile", "API_Mobile/Payment::process -- POST after decrypt/decode: ".json_encode($request));
        
        if($request->respCode != 0000){

            $order = $this->order->getByPaymentTranId($nation_code, $request->invoiceNo);

            $du = array();
            $du['webPaymentUrl'] = '';
            $du['paymentToken'] = '';
            $du['payment_tranid'] = '';
            $this->order->update($nation_code, $order->id, $du);

            $this->status = 3039;
            $this->message = 'payment failed, '.$request->respCode.' - '.$request->respDesc;
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();

        }else{

            $order = $this->order->getByPaymentTranId($nation_code, $request->invoiceNo);
            if (!isset($order->id)) {
                $this->status = 3040;
                $this->message = 'Order not found or not belong to you';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
                die();
            }
            if ($order->order_status != 'waiting_for_payment') {
                $this->status = 3041;
                $this->message = 'Order already paid';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
                die();
            }

            $pelanggan = $this->bu->getById($nation_code, $order->b_user_id);

            //collect input
            switch ($request->paymentScheme) {
                case "VI":
                    $code_bank = 1;
                    break;
                case "MA":
                    $code_bank = 2;
                    break;
                case "AM":
                    $code_bank = 3;
                    break;
                case "UP":
                    $code_bank = 4;
                    break;
                case "JC":
                    $code_bank = 5;
                    break;
                case "DI":
                    $code_bank = 6;
                    break;
                case "DN":
                    $code_bank = 7;
                    break;
                case "GP":
                    $code_bank = 0;
                    break;
                default:
                    $code_bank = 0;
            }

            //check if there is card token or not
            if($request->cardToken != '' && $code_bank != 0){

                //check already in db or not
                $checkCardToken = $this->bucm->getByCardToken($nation_code, $order->b_user_id, $request->cardToken);

                if(!isset($checkCardToken->token_result)){
                                
                    //get last id
                    $card_id = $this->bucm->getLastId($nation_code,$order->b_user_id);

                    $di = array();
                    $di['nation_code'] = $nation_code;
                    $di['b_user_id'] = $order->b_user_id;
                    $di['id'] = $card_id;
                    $di['jenis'] = strtolower($request->cardType);
                    $di['bank'] = $code_bank;
                    $di['nomor'] = $request->cardNo;
                    $di['token_result'] = $request->cardToken;
                    $this->bucm->set($di);

                }

            }

            $payment_gateway = "2c2p";
            $payment_method = ($code_bank == 0) ? "Grab Pay": "Credit Card";
            $payment_status = 'paid';
            $payment_date = date("Y-m-d H:i:s", strtotime($request->transactionDateTime));
            $payment_response = json_encode($request);

            // force order status to forward_to_seller
            $order_status = 'forward_to_seller';

            //open transaction
            $this->order->trans_start();

            //collect data payment
            $du = array();
            $du['code_bank'] = $code_bank;
            $du['payment_gateway'] = $payment_gateway;
            $du['payment_method'] = $payment_method;
            $du['payment_status'] = $payment_status;
            $du['payment_date'] = $payment_date;
            $du['payment_response'] = $payment_response;
            $du['order_status'] = $order_status;

            //by Donny Dennison - 3 november 2020 10:37
            //add flag start countdown payment
            $du['is_countdown'] = 1;

            $res = $this->order->updateByUserAndOrder($nation_code, $pelanggan->id, $order->id, $du);
            if ($res) {
                $this->status = 200;
                $this->message = 'Success';
                $this->order->trans_commit();

                //declare var for cart remove
                $pids = array();
                $pickups = array();
                $ops = $this->dodm->getByOrderId($nation_code, $order->id);
                foreach ($ops as $op) {
                    $pids[] = $op->c_produk_id;
                    $pckp = $this->bua->getById($nation_code, $op->b_user_id, $op->b_user_alamat_id);
                    $pckp->d_order_id = $op->d_order_id;
                    $pckp->d_order_detail_id = $op->d_order_detail_id;
                    // $pckp->b_user_alamat_id = $op->id;
                    $pckp->b_user_alamat_id = $op->b_user_alamat_id;
                    $pickups[] = $pckp;
                }
                $c = count($pids);
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Payment::process --PIDS: $c");
                }
                //remove from cart
                if ($c) {
                    $res = $this->cart->delByProductIds($nation_code, $pelanggan->id, $pids);
                    if ($res) {
                        $this->cart->trans_commit();
                    //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Payment::process --removeCart: SUCCESS");
                    } else {
                        //$success = 0;
                        //$this->status = 834;
                        //$this->message = 'Failed moving product from cart to order';
                        //$this->cart->trans_rollback();
                    }
                }

                // add pickup location by d_order_detail.id
                $mass_pickup = array();
                if (count($pickups)) {
                    foreach ($pickups as $pckp) {
                        $mp = array();
                        $mp['nation_code'] = $nation_code;
                        $mp['d_order_id'] = $pckp->d_order_id;
                        $mp['d_order_detail_id'] = $pckp->d_order_detail_id;
                        $mp['b_user_id'] = $pckp->b_user_id;
                        $mp['b_user_alamat_id'] = $pckp->b_user_alamat_id;
                        $mp['nama'] = $pckp->penerima_nama;
                        $mp['telp'] = $pckp->penerima_telp;
                        // by Muhammad Sofi - 3 November 2021 10:00
                        // remark code
                        // $mp['alamat'] = $pckp->alamat;
                        $mp['alamat2'] = $pckp->alamat2;
                        $mp['kelurahan'] = $pckp->kelurahan;
                        $mp['kecamatan'] = $pckp->kecamatan;
                        $mp['kabkota'] = $pckp->kabkota;
                        $mp['provinsi'] = $pckp->provinsi;
                        $mp['negara'] = $pckp->negara;
                        $mp['kodepos'] = $pckp->kodepos;
                        $mp['latitude'] = $pckp->latitude;
                        $mp['longitude'] = $pckp->longitude;
                        $mp['catatan'] = $pckp->catatan;
                        $mass_pickup[] = $mp;
                    }
                    unset($pckp);
                    unset($mp);
                    unset($pickups);
                }
                if (count($mass_pickup)) {
                    foreach ($mass_pickup as $mp) {
                        $this->dodpu->set($mp);
                    }
                    //$this->dodpu->setMass($mass_pickup);
                }

                //update forward to seller time
                if (!isset($this->seller_timeout)) {
                    $this->seller_timeout = 12;
                }
                $now = date("Y-m-d H:i:s", strtotime("now"));
                $dux = array();
                $dux['forward_to_seller_date'] = $now;
                $dux['date_begin'] = $now;

                //by Donny Dennison - 30 July 2020 13:21
                //change auto reject become auto confirm
                //START change by Donny Dennison - 30 july 2020 13:21

                //by Donny Dennison - 26 Juni 2020 21:05
                //Request by Mr Jackie, change expire for seller become 22:30 everyday
                // $dux['date_expire'] = date("Y-m-d H:i:s", strtotime("+".$this->seller_timeout." minutes"));
                // if(date('H:i:s') >= '22:30:00'){
                //     $dux['date_expire'] = date("Y-m-d 22:30:00", strtotime("+1 day"));
                // }else{
                //     $dux['date_expire'] = date("Y-m-d 22:30:00");
                // }
                if(date('H:i:s') >= '22:53:00'){
                    $dux['date_expire'] = date("Y-m-d 22:53:00", strtotime("+1 day"));
                }else{
                    $dux['date_expire'] = date("Y-m-d 22:53:00");
                }

                //END change by Donny Dennison - 30 july 2020 13:21

                $this->dodm->updateByOrderId($nation_code, $order->id, $dux);
                $this->order->trans_commit();

                //for sellers notification
                $pids = array();
                $penjuals = array();
                $ordered_products = $this->dodim->getByOrderId($nation_code, $order->id);
                foreach ($ordered_products as $product) {
                    $penj_id = (int) $product->b_user_id_seller;
                    if (!isset($penjuals[$penj_id])) {
                        $nama = '';
                        $penj = $this->bu->getById($nation_code, $penj_id);
                        $penjual = new stdClass();
                        $penjual->id = $penj->id;
                        $penjual->fnama = $penj->fnama;
                        $penjual->email = $penj->email;
                        $penjual->fcm_token = $penj->fcm_token;
                        $penjual->device = $penj->device;

                        // by Muhammad Sofi - 26 October 2021 11:16
                        // if user img & banner not exist or empty, change to default image
                        // $penjual->image = $penj->image;
                        if(file_exists(SENEROOT.$penj->image) && $penj->image != 'media/user/default.png'){
                            $penjual->image = $penj->image;
                        } else {
                            $penjual->image = $this->cdn_url('media/user/default-profile-picture.png');
                        }
                        $penjual->d_order_id = $product->d_order_id;
                        $penjual->d_order_detail_id = $product->d_order_detail_id;
                        $penjual->c_produk_nama = '';
                        $penjual->produk = array();
                        $penjuals[$penj_id] = $penjual;
                    }
                    $penjual->c_produk_nama .= html_entity_decode($product->c_produk_nama,ENT_QUOTES).', ';
                    $penjuals[$penj_id]->produk[] = $product;
                    $pids[] = $product->id;
                }
                $penjual->c_produk_nama = rtrim($penjual->c_produk_nama, ', ');

                //clear cart
                $res_cart = $this->cart->delByProductIds($nation_code, $pelanggan->id, $pids);
                if ($res_cart) {
                    $this->order->trans_commit();
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Payment::process -- cart cleared successfully");
                    }
                }

                //get notification config for buyer
                $type = 'transaction';
                $anotid = 18;
                $replacer = array();
                $replacer['invoice_code'] = $order->invoice_code;
                $replacer['pelanggan_fnama'] = $pelanggan->fnama;
                $setting_value = 0;
                $classified = 'setting_notification_buyer';
                $notif_code = 'B1';
                $notif_cfg = $this->busm->getValue($nation_code, $pelanggan->id, $classified, $notif_code);
                if (isset($notif_cfg->setting_value)) {
                    $setting_value = (int) $notif_cfg->setting_value;
                }
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Payment::process --pushNotifConfig, F: B, UID: $pelanggan->id, Classified: $classified, Code: $notif_code, value: $setting_value");
                }

                $ordered_products = $this->dodm->getByOrderId($nation_code, $order->id);

                //push notif seller
                $pji=0;
                foreach ($ordered_products as $op) {
                    //push notif for buyer
                    if (strlen($pelanggan->fcm_token) > 50 && !empty($setting_value) && $pelanggan->is_active == 1) {
                        $device = $pelanggan->device;
                        $tokens = array($pelanggan->fcm_token);
                        if($pelanggan->language_id == 2) {
                            $title = 'Menunggu konfirmasi';
                            $message = "Anda telah menyelesaikan pembayaran untuk faktur berikut: $order->invoice_code";
                        } else {
                            $title = 'Waiting for confirmation';
                            $message = "You have completed payment for the following invoice: $order->invoice_code";
                        }
                        
                        $image = 'media/pemberitahuan/transaction.png';
                        $payload = new stdClass();
                        $payload->id_produk = "".$op->d_order_detail_id;
                        $payload->id_order = "".$order->id;
                        $payload->id_order_detail = "".$op->d_order_detail_id;
                        $payload->b_user_id_buyer = $pelanggan->id;
                        $payload->b_user_fnama_buyer = $pelanggan->fnama;
                        
                        // by Muhammad Sofi - 28 October 2021 11:00
                        // if user img & banner not exist or empty, change to default image
                        // $payload->b_user_image_buyer = $this->cdn_url($pelanggan->image);
                        if(file_exists(SENEROOT.$pelanggan->image) && $pelanggan->image != 'media/user/default.png'){
                            $payload->b_user_image_buyer = $this->cdn_url($pelanggan->image);
                        } else {
                            $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                        }
                        $payload->b_user_id_seller = null;
                        $payload->b_user_fnama_seller = null;
                        
                        // by Muhammad Sofi - 28 October 2021 11:00
                        // if user img & banner not exist or empty, change to default image
                        // $payload->b_user_image_seller = null;
                        $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                        $payload->status = "waiting_confirmation";
                        $nw = $this->anot->get($nation_code, "push", $type, $anotid, $pelanggan->language_id);
                        if (isset($nw->title)) {
                            $title = $nw->title;
                        }
                        if (isset($nw->message)) {
                            $message = $this->__nRep($nw->message, $replacer);
                        }
                        if (isset($nw->image)) {
                            $image = $nw->image;
                        }
                        $image = $this->cdn_url($image);
                        $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                        if ($this->is_log) {
                            $this->seme_log->write("api_mobile", 'API_Mobile/Payment::process __pushNotifBuyer: '.json_encode($res));
                        }
                    }
                    $pji++;
                    //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Payment::process -> SellerIteration-".$pji." BUID: ".$pj->id);

                    //get notif list config from db for buyer
                    $anotid = 18;
                    $replacer = array();
                    $replacer['invoice_code'] = $order->invoice_code;
                    $replacer['pelanggan_fnama'] = $pelanggan->fnama;
                    $replacer['c_produk_nama'] = html_entity_decode($op->c_produk_nama,ENT_QUOTES);
                    $replacer['order_cdate'] = date("l, j F Y H:i", strtotime($order->d_order_cdate));

                    //collect array notification list for buyer
                    $dpe = array();
                    $dpe['nation_code'] = $nation_code;
                    $dpe['b_user_id'] = $pelanggan->id;
                    $dpe['id'] = $this->dpem->getLastId($nation_code, $pelanggan->id);
                    $dpe['type'] = $type;
                    if($pelanggan->language_id == 2) {
                        $dpe['judul'] = "Menunggu konfirmasi";
                        $dpe['teks'] = "Anda telah menyelesaikan pembayaran untuk produk berikut: ".html_entity_decode($op->c_produk_nama,ENT_QUOTES)." ($order->invoice_code) ".$replacer['order_cdate'].". Mohon menunggu konfirmasi seller.";    
                    } else {
                        $dpe['judul'] = "Waiting for confirmation";
                        $dpe['teks'] = "You have completed payment for the following products: ".html_entity_decode($op->c_produk_nama,ENT_QUOTES)." ($order->invoice_code) ".$replacer['order_cdate'].". Please wait for seller confirmation.";    
                    }
                    $dpe['cdate'] = "NOW()";
                    $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
                    $extras = new stdClass();
                    $extras->id_order = "".$order->id;
                    $extras->id_produk ="". $op->d_order_detail_id;
                    $extras->id_order_detail = "".$op->d_order_detail_id;
                    $extras->b_user_id_buyer = $pelanggan->id;
                    $extras->b_user_fnama_buyer = $pelanggan->fnama;
                    
                    // by Muhammad Sofi - 28 October 2021 11:00
                    // if user img & banner not exist or empty, change to default image
                    // $extras->b_user_image_buyer = $this->cdn_url($pelanggan->image);
                    if(file_exists(SENEROOT.$pelanggan->image) && $pelanggan->image != 'media/user/default.png'){
                        $extras->b_user_image_buyer = $this->cdn_url($pelanggan->image);
                    } else {
                        $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    $extras->b_user_id_seller = $op->b_user_id_seller;
                    $extras->b_user_fnama_seller = $op->b_user_fnama_seller;
                    
                    // by Muhammad Sofi - 28 October 2021 11:00
                    // if user img & banner not exist or empty, change to default image
                    // $extras->b_user_image_seller = $this->cdn_url($op->b_user_image_seller);
                    if(file_exists(SENEROOT.$op->b_user_image_seller) && $op->b_user_image_seller != 'media/user/default.png'){
                        $extras->b_user_image_seller = $this->cdn_url($op->b_user_image_seller);
                    } else {
                        $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    $extras->status = "waiting_confirmation";
                    $dpe['extras'] = json_encode($extras);
                    $nw = $this->anot->get($nation_code, "list", $type, $anotid, $pelanggan->language_id);
                    if (isset($nw->title)) {
                        $dpe['judul'] = $nw->title;
                    }
                    if (isset($nw->message)) {
                        $dpe['teks'] = $this->__nRep($nw->message, $replacer);
                    }
                    if (isset($nw->image)) {
                        $dpe['gambar'] = $nw->image;
                    }
                    $this->dpem->set($dpe);
                    $this->order->trans_commit();
                    //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Payment::process -> DPE_BUYER: COMMIT");

                    //get notif list config from db for buyer
                    $anotid = 19;
                    $replacer = array();
                    $replacer['invoice_code'] = $order->invoice_code;
                    $replacer['pelanggan_fnama'] = $pelanggan->fnama;
                    $replacer['c_produk_nama'] = html_entity_decode($op->c_produk_nama,ENT_QUOTES);
                    $replacer['order_cdate'] = date("l, j F Y H:i", strtotime($order->d_order_cdate));

                    //collect array notification list for seller
                    $dpe = array();
                    $dpe['nation_code'] = $nation_code;
                    $dpe['b_user_id'] = $op->b_user_id_seller;
                    $dpe['id'] = $this->dpem->getLastId($nation_code, $op->b_user_id_seller);
                    $dpe['type'] = $type;
                    // $op?
                    if($op->language_id == 2) {
                        $dpe['judul'] = "Menunggu konfirmasi";

                        //by Donny Dennison - 30 July 2020 13:21
                        //change auto reject become auto confirm
                        //START Change by Donny Dennison - 30 july 2020 13:21
    
                        //by Donny Dennison - 26 Juni 2020 21:05
                        //request by Mr Jackie, change the message from 12 hours to 10:30 pm tonight
                        // $dpe['teks'] = "You get an order for the product ".$this->__cAmp($op->c_produk_nama).", please confirm the order immediately within the next 12 hours.";
                        // $dpe['teks'] = "You get an order for the product ".$this->__cAmp($op->c_produk_nama).", please confirm the order immediately before 10:30 pm tonight.";
                        $dpe['teks'] = "Anda mendapatkan pesanan untuk produk ".html_entity_decode($op->c_produk_nama,ENT_QUOTES).", mohon konfirmasi pesanan segera sebelum 10:57 malam.";
                    } else {
                        $dpe['judul'] = "Waiting for confirmation";
                        $dpe['teks'] = "You get an order for a product ".html_entity_decode($op->c_produk_nama,ENT_QUOTES).", please confirm the order immediately before 10:57 pm.";
                    }
                    
                    //END Change by Donny Dennison - 30 july 2020 13:21

                    $dpe['cdate'] = "NOW()";
                    $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
                    $extras = new stdClass();
                    $extras->id_order = "".$order->id;
                    $extras->id_produk = "".$op->d_order_detail_id;
                    $extras->id_order_detail = "".$op->d_order_detail_id;
                    $extras->b_user_id_buyer = $pelanggan->id;
                    $extras->b_user_fnama_buyer = $pelanggan->fnama;
                    
                    // by Muhammad Sofi - 28 October 2021 11:00
                    // if user img & banner not exist or empty, change to default image
                    // $extras->b_user_image_buyer = $this->cdn_url($pelanggan->image);
                    if(file_exists(SENEROOT.$pelanggan->image) && $pelanggan->image != 'media/user/default.png'){
                        $extras->b_user_image_buyer = $this->cdn_url($pelanggan->image);
                    } else {
                        $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    $extras->b_user_id_seller = $op->b_user_id_seller;
                    $extras->b_user_fnama_seller = $op->b_user_fnama_seller;
                    
                    // by Muhammad Sofi - 28 October 2021 11:00
                    // if user img & banner not exist or empty, change to default image
                    // $extras->b_user_image_seller = $this->cdn_url($op->b_user_image_seller);
                    if(file_exists(SENEROOT.$op->b_user_image_seller) && $op->b_user_image_seller != 'media/user/default.png'){
                        $extras->b_user_image_seller = $this->cdn_url($op->b_user_image_seller);
                    } else {
                        $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    $extras->status = "waiting_confirmation";
                    $dpe['extras'] = json_encode($extras);
                    $nw = $this->anot->get($nation_code, "list", $type, $anotid, $op->language_id);
                    if (isset($nw->title)) {
                        $dpe['judul'] = $nw->title;
                    }
                    if (isset($nw->message)) {
                        $dpe['teks'] = $this->__nRep($nw->message, $replacer);
                    }
                    if (isset($nw->image)) {
                        $dpe['gambar'] = $nw->image;
                    }
                    $this->dpem->set($dpe);
                    $this->order->trans_commit();
                    //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Payment::process -> DPE_SELLER: COMMIT");


                    //collect array order history process
                    $dop = array();
                    $dop['nation_code'] = $nation_code;
                    $dop['d_order_id'] = $order->id;
                    $dop['c_produk_id'] = $op->d_order_detail_id;
                    $dop['id'] = $this->dopm->getLastId($nation_code, $order->id, $op->d_order_detail_id);
                    $dop['initiator'] = "buyer";
                    $dop['nama'] = "Menunggu konfirmasi";
                    $dop['deskripsi'] = "Produk pesanan Anda: ".html_entity_decode($op->c_produk_nama)." telah diteruskan ke penjual untuk konfirmasi.";
                    $dop['cdate'] = "NOW()";
                    $this->dopm->set($dop);
                    $this->order->trans_commit();
                    //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Payment::process -> DPOM: COMMIT");


                    //get notification config for seller
                    $setting_value = 0;
                    $classified = 'setting_notification_seller';
                    $notif_code = 'S0';
                    $notif_cfg = $this->busm->getValue($nation_code, $op->b_user_id_seller, $classified, $notif_code);
                    if (isset($notif_cfg->setting_value)) {
                        $setting_value = (int) $notif_cfg->setting_value;
                    }
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Payment::process --pushNotifConfig, F: S, UID: $op->b_user_id_seller, Classified: $classified, Code: $notif_code, value: $setting_value");
                    }

                    //push notif to seller
                    if (strlen($op->fcm_token) > 50 && !empty($setting_value) && $op->b_user_is_active == 1) {
                        $device = $op->device;
                        $tokens = array($op->fcm_token);
                        if($op->language_id == 2) {
                            $title = 'Menunggu konfirmasi!';

                            //by Donny Dennison - 30 July 2020 13:21
                            //change auto reject become auto confirm
                            //START Change by Donny Dennison - 30 july 2020 13:21
                                        
                            //by Donny Dennison - 26 Juni 2020 21:05
                            //request by Mr Jackie, change the message from 12 hours to 10:30 pm tonight
                            // $message = "You've got an order for ".$this->__cAmp($op->c_produk_nama).", please confirm the order immediately within the next 12 hours.";
                            // $message = "You've got an order for ".$this->__cAmp($op->c_produk_nama).", please confirm the order immediately before 10:30 pm tonight.";
                            $message = "Anda punya pesanan untuk ".html_entity_decode($this->convertEmoji($op->c_produk_nama),ENT_QUOTES).", mohon konfirmasi pesanan segera sebelum 10:57 malam.";
                        } else {
                            $title = 'Waiting for confirmation!';
                            $message = "You have an order for ".html_entity_decode($this->convertEmoji($op->c_produk_nama),ENT_QUOTES).", please confirm the order immediately before 10:57 pm.";
                        }
                        
                        //END Change by Donny Dennison - 30 july 2020 13:21
                        
                        $image = 'media/pemberitahuan/transaction.png';
                        $payload = new stdClass();
                        $payload->id_produk = "".$op->d_order_detail_id;
                        $payload->id_order = "".$order->id;
                        $payload->id_order_detail = "".$op->d_order_detail_id;
                        $payload->b_user_id_buyer = $pelanggan->id;
                        $payload->b_user_fnama_buyer = $pelanggan->fnama;
                        
                        // by Muhammad Sofi - 28 October 2021 11:00
                        // if user img & banner not exist or empty, change to default image
                        // $payload->b_user_image_buyer = $this->cdn_url($pelanggan->image);
                        if(file_exists(SENEROOT.$pelanggan->image) && $pelanggan->image != 'media/user/default.png'){
                            $payload->b_user_image_buyer = $this->cdn_url($pelanggan->image);
                        } else {
                            $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                        }
                        $payload->b_user_id_seller = $op->b_user_id_seller;
                        $payload->b_user_fnama_seller = $op->b_user_fnama_seller;
                        
                        // by Muhammad Sofi - 28 October 2021 11:00
                        // if user img & banner not exist or empty, change to default image
                        // $payload->b_user_image_seller = $this->cdn_url($op->b_user_image_seller);
                        if(file_exists(SENEROOT.$op->b_user_image_seller) && $op->b_user_image_seller != 'media/user/default.png'){
                            $payload->b_user_image_seller = $this->cdn_url($op->b_user_image_seller);
                        } else {
                            $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                        }
                        $payload->status = "waiting_confirmation";
                        $nw = $this->anot->get($nation_code, "push", $type, $anotid, $op->language_id);
                        if (isset($nw->title)) {
                            $title = $nw->title;
                        }
                        if (isset($nw->message)) {
                            $message = $this->__nRep($nw->message, $replacer);
                        }
                        if (isset($nw->image)) {
                            $image = $nw->image;
                        }
                        $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                        if ($this->is_log) {
                            $this->seme_log->write("api_mobile", 'API_Mobile/Payment::process __pushNotifSeller: '.json_encode($res));
                        }
                    }

                    //check rating
                    $erm = $this->erm->check($nation_code, $order->id, $op->d_order_detail_id, $op->b_user_id_seller, $pelanggan->id);
                    if (!isset($erm->nation_code)) {
                        //create rating
                        $der = array();
                        $der['nation_code'] = $nation_code;
                        $der['d_order_id'] = $order->id;
                        $der['d_order_detail_id'] = $op->d_order_detail_id;
                        $der['b_user_id_seller'] = $op->b_user_id_seller;
                        $der['b_user_id_buyer'] = $pelanggan->id;
                        $this->erm->set($der);
                        $this->order->trans_commit();
                    }
                } //end penjuals

                //send email for buyer
                if ($this->email_send && strlen($pelanggan->email)>4) {
                    $replacer = array();
                    $replacer['site_name'] = $this->app_name;
                    $replacer['fnama'] = $pelanggan->fnama;
                    $replacer['invoice_code'] = $order->invoice_code;
                    $this->seme_email->flush();
                    $this->seme_email->replyto($this->site_name, $this->site_email_finance);
                    $this->seme_email->from($this->site_email_finance, $this->site_name);
                    $this->seme_email->subject('Waiting for Seller Confirmation');
                    $this->seme_email->to($pelanggan->email, $pelanggan->fnama);
                    $this->seme_email->template('buyer_after_payment');
                    $this->seme_email->replacer($replacer);
                    $this->seme_email->send();
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Payment::process -- email sent to buyer");
                    }
                }
            } else {
                $this->status = 3048;
                $this->message = 'Failed updating Order';
                $this->order->trans_rollback();
            }
            $this->order->trans_end();

            //doing earning calculation
            if ($this->status == 200) {
                //get ST and GT
                $sub_total = $order->sub_total;
                $payment_amount = $order->payment_amount;

                //get fee configuration
                $f = $this->__feeCalculation($nation_code);

                //declare var Fee and COST
                $pg_fee = 0.0;
                $pg_fee_vat = 0.0;
                $pg_fee_jenis = 'percentage';
                $cancel_fee = 0;
                $refund_amount = 0;
                $profit_amount = 0.0;
                $pg_fee_percent = 0.0;
                $profit_percent = 0.0;
                $selling_fee = 0.0;
                $selling_fee_percent = 10;

                //calculating Selling Fee from payment_amount
                if (isset($f->selling_fee_percent)) {
                    $selling_fee_percent = $f->selling_fee_percent;
                    $selling_fee = $sub_total * ($selling_fee_percent/100);
                    $selling_fee = round($selling_fee, 2, PHP_ROUND_HALF_DOWN);
                }

                //get code bank
                $is_foreign_card = 1;
                $nation_card = '-';
                $card_type = $this->__card2Text($code_bank);
                if (strtolower($card_type) == 'visa' || strtolower($card_type) == 'mastercard' || strtolower($card_type) == 'jcb') {
                    $probj = json_decode($payment_response);
                    if (isset($probj->issuerCountry)) {
                        if (strtolower($probj->issuerCountry) == 'sg' || strtolower($probj->issuerCountry) == 'sgd') {
                            $pg_fee_percent = 2.80;
                            $is_foreign_card = 0;
                        } else {
                            $pg_fee_percent = 3.00;
                        }
                        $nation_card = $probj->issuerCountry;
                    }

                //by Donny Dennison - 2 november 2020 16:03
                //add payment 2c2p grab pay
                //START by Donny Dennison - 2 november 2020 16:03

                }else if($payment_method == 'Grab Pay' && $code_bank == 0){

                    $pg_fee_percent = 2.30;

                //END by Donny Dennison - 2 november 2020 16:03

                } else {
                    $pg_fee_percent = $this->__card2Val($code_bank);
                }


                //calculating PG Fee from Grand total

                //by Donny Dennison - 2 november 2020 16:03
                //add payment 2c2p grab pay
                // $pg_fee = round($payment_amount * ($pg_fee_percent/100), 2, PHP_ROUND_HALF_DOWN);
                $pg_fee = floor((($payment_amount * ($pg_fee_percent/100)) * 100)) / 100;
                $pg_fee_temp = $payment_amount * ($pg_fee_percent/100);

                //logger
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Payment::process --cardType: $card_type --cardNation: $nation_card --cardMDRPercent: $pg_fee_percent --cardMDR: $pg_fee");
                }

                //calculating PG Fee VAT from PG Fee
                if (isset($f->pg_fee_vat)) {

                    //by Donny Dennison - 2 november 2020 16:03
                    //add payment 2c2p grab pay
                    // $pg_fee_vat = round($pg_fee * ($f->pg_fee_vat/100), 2, PHP_ROUND_HALF_DOWN);
                    $pg_fee_vat = floor((($pg_fee_temp * ($f->pg_fee_vat/100)) * 100)) / 100;

                }

                //get Seller earning percent
                $earning_fee_percent = 100 - $selling_fee_percent;

                //sellon profit
                $profit_amount = $selling_fee - ($pg_fee+$pg_fee_vat);

                //update to table d_order
                $du = array();
                $du['pg_fee'] = $pg_fee;
                $du['pg_fee_vat'] = $pg_fee_vat;
                $du['pg_fee_percent'] = $pg_fee_percent;
                $du['profit_amount'] = $profit_amount;
                $du['payment_card_origin'] = $nation_card;
                $du['selling_fee'] = $selling_fee;
                $du['selling_fee_percent'] = $selling_fee_percent;
                $res = $this->order->update($nation_code, $order->id, $du);

                //start shared cost calculation
                $odetails = $this->dodm->getByOrderId($nation_code, $order->id);
                $odetails_count = count($odetails);

                //by Donny Dennison - 30 july 2020 19:25
                // change seller fee percent to 5% if product id is more than 78 and product cdate is less than 31 august 2020
                $final_total_selling_fee = 0;


                foreach ($odetails as $op) {

                    //by Donny Dennison - 30 july 2020 19:25
                    // change seller fee percent to 5% if product id is more than 78 and product cdate is less than 31 august 2020
                    // START change by Donny Dennison - 30 july 2020 19:25

                    //get subtotal and grand_total per item
                    // $sub_total = $op->sub_total;


                    $orderProductDetails = $this->dodim->getByOrderIdDetailId($nation_code, $order->id, $op->d_order_detail_id);

                    $selling_fee = 0;
                    $earning_total = 0;

                    foreach($orderProductDetails As $OrderProdDetail){

                        $getProductType = $this->cpm->getProductType($nation_code, $OrderProdDetail->c_produk_id);
                        $getProductType = $getProductType->product_type;
                        $productDetails = $this->cpm->getById($nation_code, $OrderProdDetail->c_produk_id, $pelanggan, $getProductType);
                        
                        // if($productDetails->id >= 209 && date('Y-m-d', strtotime($productDetails->cdate)) <= '2020-08-31' ){

                        //     //selling fee
                        //     $selling_fee_temp = $OrderProdDetail->harga_jual * $OrderProdDetail->qty * (5/100);
                        //     $selling_fee += $selling_fee_temp;

                        //     $earning_total_temp = $OrderProdDetail->harga_jual * $OrderProdDetail->qty - $selling_fee_temp;
                        //     $earning_total += $earning_total_temp;
                        
                        // }else{
                            
                            //selling fee
                            $selling_fee_temp = $OrderProdDetail->harga_jual * $OrderProdDetail->qty * ($selling_fee_percent/100);
                            $selling_fee += $selling_fee_temp;

                            $earning_total_temp = $OrderProdDetail->harga_jual * $OrderProdDetail->qty - $selling_fee_temp;
                            $earning_total += $earning_total_temp;
                                
                        // }

                    }

                    // //selling fee
                    // $selling_fee = $sub_total * ($selling_fee_percent/100);
                    // $earning_total = $sub_total - $selling_fee;

                    // END change by Donny Dennison - 30 july 2020 19:25

                    //collect to update to database
                    unset($du);
                    $du = array();
                    $du['pg_fee'] = $pg_fee;
                    $du['pg_vat'] = $pg_fee_vat;
                    $du['profit_amount'] = $profit_amount;
                    $du['selling_fee'] = $selling_fee;
                    $du['selling_fee_percent'] = $selling_fee_percent;
                    $du['earning_total'] = $earning_total;
                    $du['earning_percent'] = $earning_fee_percent;
                    $res = $this->dodm->update($nation_code, $op->d_order_id, $op->d_order_detail_id, $du);
                    if (!$res) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Payment::process --IDS: $op->d_order_id/$op->id  --sharedCost: failed");
                    }

                    $final_total_selling_fee += $selling_fee;
                }

                //by Donny Dennison - 30 july 2020 19:25
                // change seller fee percent to 5% if product id is more than 78 and product cdate is less than 31 august 2020
                //START change by Donny Dennison - 30 july 2020 19:25

                //sellon profit
                $profit_amount = $final_total_selling_fee - ($pg_fee+$pg_fee_vat);

                //update to table d_order
                unset($du);
                $du = array();
                $du['profit_amount'] = $profit_amount;
                $du['selling_fee'] = $final_total_selling_fee;
                $res = $this->order->update($nation_code, $order->id, $du);

                foreach ($odetails as $op) {

                    //collect to update to database
                    unset($du);
                    $du = array();
                    $du['profit_amount'] = $profit_amount;
                    $res = $this->dodm->update($nation_code, $op->d_order_id, $op->d_order_detail_id, $du);
                    if (!$res) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Payment::process --IDS: $op->d_order_id/$op->id  --sharedCost: failed");
                    }
                }

                //END change by Donny Dennison - 30 july 2020 19:25

                //by Donny Dennison - 14 august 2020 15:53
                // curl to facebook every time customer doing payment order
                //START by Donny Dennison - 14 august 2020 15:53


                $ordered_products = $this->dodim->getByOrderId($nation_code, $order->id);

                //product that got purchased add to content and send to facebook
                $contentIDSentToFacebook = array();
                $contentProductSentToFacebook = array();
                $valueSentToFacebook = 0;

                for ($i=0; $i < count($ordered_products) ; $i++) { 

                    $contentIDSentToFacebook[$i] = $ordered_products[$i]->c_produk_id;
                    
                    $contentProductSentToFacebook[$i]['id'] = $ordered_products[$i]->c_produk_id;
                    $contentProductSentToFacebook[$i]['quantity'] = $ordered_products[$i]->qty;
                    $contentProductSentToFacebook[$i]['item_price'] = $ordered_products[$i]->harga_jual;

                    $valueSentToFacebook += $ordered_products[$i]->qty * $ordered_products[$i]->harga_jual;

                }

                //send data to facebook
                $postToFB= array(
                  
                  'data' => array( 

                    array(
                    
                        'event_name' => 'Purchase',
                        'event_time' => strtotime('now'),
                        'event_id' => 'Purchase'.date('YmdHis'),
                        'event_source_url' => 'https://sellon.net/product_detail.php?product_id='.$ordered_products[0]->c_produk_id,
                        'user_data' => array(
                            'client_ip_address' => '35.240.185.29',
                            'client_user_agent' => 'browser'
                        ),
                        'custom_data' => array(
                            'value' => $valueSentToFacebook,
                            'currency' => 'SGD',
                            'content_ids' => $contentIDSentToFacebook,
                            'contents' => $contentProductSentToFacebook,
                            'content_type' => 'product',
                            'delivery_category' => 'home_delivery'
                        ),
                        "opt_out" => 'true'
                    )

                  ),
                  // 'test_event_code' =>'TEST20037'

                );
                

                $curlToFacebook = $this->__CurlFacebook($postToFB);

                $this->seme_log->write("api_mobile", "__CurlFacebook : Response -> ".$curlToFacebook);
                
                //END by Donny Dennison - 14 august 2020 15:53

            }

            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");

        }

    }

    //by Donny Dennison - 11 november 2021 11:11
    //deprecated api start countdown payment
    // //by Donny Dennison - 3 november 2020 10:37
    // //add flag start countdown payment
    // public function countdown()
    // {
    //     //initial
    //     $dt = $this->__init();
    //     $data = array();
    //     $data['order'] = new stdClass();
    //     $data['order']->addresses = new stdClass();
    //     $data['order']->sellers = array();

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }
    //     $negara = $this->anm->getByNationCode($nation_code);
    //     if (isset($negara->nama)) {
    //         $this->negara = $negara->nama;
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }

    //     //check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = new stdClass();
    //     if (strlen($apisess)>4) {
    //         $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     }
    //     if (!isset($pelanggan->id)) {
    //         $this->status = 401;
    //         $this->message = 'Missing or invalid API session';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }
    //     if ($this->is_log) {
    //         $this->seme_log->write("api_mobile", "API_Mobile/Payment::countdown --beginProcess");
    //     }

    //     $d_order_id = (int) $this->input->post("d_order_id");
    //     if ($d_order_id<=0) {
                // $this->status = 3040;
                // $this->message = 'Order not found or not belong to you';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }
    //     $order = $this->order->getByIdUserId($nation_code, $pelanggan->id, $d_order_id);
    //     if (!isset($order->id)) {
    //         $this->status = 3040;
    //         $this->message = 'Order not found or not belong to you';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }

    //     //open transaction
    //     $this->order->trans_start();


    //     //collect data payment
    //     $du = array();
    //     $du['cdate'] = 'NOW()';
    //     $du['is_countdown'] = 1;
    //     $res = $this->order->updateByUserAndOrder($nation_code, $pelanggan->id, $order->id, $du);

    //     if ($res) {

    //         if($order->is_countdown == 0){

    //             $du = array();
    //             $du['date_begin'] = date("Y-m-d H:i:s", strtotime("now"));
    //             $du['date_expire'] = date("Y-m-d H:i:s", strtotime("+".$this->payment_timeout." minutes"));
    //             $this->dodm->updateByOrderId($nation_code, $d_order_id, $du);
            
    //         }

    //         $this->status = 200;
    //         $this->message = 'Success';
    //         $this->order->trans_commit();
            
    //     } else {
    //         $this->status = 3048;
    //         $this->message = 'Failed updating Order';
    //         $this->order->trans_rollback();
    //     }
    //     $this->order->trans_end();

    //     //last order response
    //     $data['order'] = $this->order->getByIdUserId($nation_code, $pelanggan->id, $d_order_id);
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    // }

    //by Donny Dennison - 11 november 2021 11:11
    //change payment expiry from 3 minutes to 5 minutes and send notif if user close the webview/iframe
    public function sendnotif()
    {
        //initial
        $dt = $this->__init();
        $data = array();
        $data['order'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }
        $negara = $this->anm->getByNationCode($nation_code);
        if (isset($negara->nama)) {
            $this->negara = $negara->nama;
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Payment::sendnotif --beginProcess");
        }

        $d_order_id = (int) $this->input->post("d_order_id");
        if ($d_order_id<=0) {
            $this->status = 3040;
            $this->message = 'Order not found or not belong to you';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }
        $orderDetail = $this->dodm->getById($nation_code, $d_order_id, 1);
        if (!isset($orderDetail->d_order_id)) {
            $this->status = 3040;
            $this->message = 'Order not found or not belong to you';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
            die();
        }

        //open transaction
        $this->order->trans_start();

        $c_produk_nama = $orderDetail->nama. '...';

        // calculate remaining minutes to pay
        $startDate = strtotime('now');
        $endDate = strtotime("+".$this->payment_timeout." minutes", strtotime($orderDetail->d_order_cdate));
        $diff = ($endDate - $startDate);
        $minuteLeft = ($diff/60 > 0) ? round($diff/60) : 0; //Echoes in min

        //update status order to cancelled
        $res = $this->order->update($orderDetail->nation_code, $orderDetail->d_order_id, array("payment_notif_count"=>"1"));
        $this->order->trans_commit();

        //get buyer and seller
        $buyer = $this->bu->getById($orderDetail->nation_code, $orderDetail->b_user_id_buyer);
        $seller = $this->bu->getById($orderDetail->nation_code, $orderDetail->b_user_id_seller);

        //define notification message from DB
        $type = 'transaction';

        //add to d_order_prosess table for historical order process
        $dpe = array();
        $dpe['nation_code'] = $orderDetail->nation_code;
        $dpe['b_user_id'] = $buyer->id;
        $dpe['id'] = $this->dpem->getLastId($orderDetail->nation_code, $buyer->id);
        $dpe['type'] = $type;
        $dpe['judul'] = "Menunggu pembayaran";
        $dpe['teks'] = "Harap selesaikan pembayaran Anda untuk produk ini ".html_entity_decode($op->c_produk_nama,ENT_QUOTES)." (".$orderDetail->invoice_code.") langsung. Jika Anda tidak menyelesaikan pembayaran dalam ".$minuteLeft." berikutnya. menit, Anda harus mengulang pesanan Anda.";
        $dpe['gambar'] = "media/pemberitahuan/transaction.png";
        $dpe['cdate'] = "NOW()";
        $extras = new stdClass();
        $extras->id_order = "".$orderDetail->d_order_id;
        $extras->id_produk = null;
        $extras->id_order_detail = null;
        $extras->b_user_id_buyer = $buyer->id;
        $extras->b_user_fnama_buyer = $buyer->fnama;

        if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
            $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        } else {
            $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
        }
        $extras->b_user_id_seller = $seller->id;
        $extras->b_user_fnama_seller = $seller->fnama;
        
        if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
            $extras->b_user_image_seller = $this->cdn_url($seller->image);
        } else {
            $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        }
        $extras->status = "waiting_payment";

        $dpe['extras'] = json_encode($extras);
        $dpe['is_read'] = 0;
        $res = $this->dpem->set($dpe);

        //get notification config for buyer
        $setting_value = 0;
        $classified = 'setting_notification_buyer';
        $notif_code = 'B1';
        $notif_cfg = $this->busm->getValue($orderDetail->nation_code, $buyer->id, $classified, $notif_code);
        if (isset($notif_cfg->setting_value)) {
            $setting_value = (int) $notif_cfg->setting_value;
        }

        //push notif for buyer
        if (strlen($buyer->fcm_token)>50 && !empty($setting_value) && $buyer->is_active == 1) {
            //push notif to buyer
            $device = $buyer->device;
            $tokens = array($buyer->fcm_token);
            if($buyer->language_id == 2) {
                $title = 'Menunggu pembayaran';
                $message = "Selesaikan pembayaran Anda untuk produk ini segera: ".html_entity_decode($this->convertEmoji($op->c_produk_nama),ENT_QUOTES)." (".$orderDetail->invoice_code.").";
            } else {
                $title = 'Waiting for payment';
                $message = "Complete your payment for this product immediately: ".html_entity_decode($this->convertEmoji($op->c_produk_nama),ENT_QUOTES)." (".$orderDetail->invoice_code.").";
            }
            $type = 'transaction';
            $image = 'media/pemberitahuan/transaction.png';
            $payload = new stdClass();
            $payload->id_order = "".$orderDetail->d_order_id;
            $payload->id_produk = null;
            $payload->id_order_detail = null;
            $payload->b_user_id_buyer = $buyer->id;
            $payload->b_user_fnama_buyer = $buyer->fnama;
            
            if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
            } else {
                $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
            }
            $payload->b_user_id_seller = $seller->id;
            $payload->b_user_fnama_seller = $seller->fnama;
            
            if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
                $payload->b_user_image_seller = $this->cdn_url($seller->image);
            } else {
                $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
            }
            $payload->status = "waiting_payment";

            $image = $this->cdn_url($image);
            $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        }

        $this->status = 200;
        $this->message = 'Success';
        $this->order->trans_commit();
        
        $this->order->trans_end();

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    }
    
    //by Donny Dennison - 11 november 2021 11:11
    //deprecated api jwt encode and decode HS256
    // //by Donny Dennison - 23 october 2020 10:30
    // //add api jwt encode and decode HS256
    // public function jwtencodedecode()
    // {
    //     //initial
    //     $dt = $this->__init();
    //     $data = '';

    //     $key = $this->input->post("key");
    //     if (strlen($key) < 1 ) {
    //         $this->status = 828;
    //         $this->message = 'Invalid post_data format';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }

    //     $type = $this->input->post("type");
    //     if ($type != 'encode' && $type != 'decode' ) {
    //         $data = array();
    //         $data['type'] = $type;
    //         $this->status = 828;
    //         $this->message = 'Wrong Type';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //         die();
    //     }

    //     if($type == 'encode'){

    //         $post_data = json_decode($this->input->post("post_data"));
    //         if (!isset($post_data)) {
    //             $this->status = 828;
    //             $this->message = 'Invalid post_data format';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //             die();
    //         }

    //         if (!is_array($post_data) && !is_object($post_data)) {
    //             $this->status = 829;
    //             $this->message = 'Products on post_data must be an array';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //             die();
    //         }

    //     }else{

    //         $post_data = $this->input->post("data");
    //         if (strlen($post_data) < 1) {
    //             $this->status = 828;
    //             $this->message = 'Invalid post_data format';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    //             die();
    //         }

    //     }

    //     if($type == 'encode'){

    //         $data = array();
    //         $data['hasil'] = JWT::encode($post_data, $key, 'HS256');

    //     }else{

    //         $data = JWT::decode($post_data, $key, array('HS256'));

    //     }

    //     //response
    //     $this->status = 200;
    //     $this->message = 'Success';

    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "payment");
    // }

}
