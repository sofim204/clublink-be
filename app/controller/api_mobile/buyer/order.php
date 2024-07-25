<?php
class Order extends JI_Controller
{
    public $is_log = 1;

    public function __construct()
    {
        parent::__construct();
        //$this->setTheme('frontx');
        $this->lib("seme_log");
        $this->load("api_mobile/b_kategori_model3", "bkm3");
        $this->load("api_mobile/b_user_alamat_model", "bua");
        $this->load("api_mobile/common_code_model", "ccm");
        $this->load("api_mobile/c_produk_model", "cpm");
        $this->load("api_mobile/c_produk_foto_model", "cpfm");
        $this->load("api_mobile/b_user_model", "bu");
        $this->load("api_mobile/d_wishlist_model", "dwlm");
        $this->load("api_mobile/d_order_model", "order");
        $this->load("api_mobile/d_order_alamat_model", "doam");
        $this->load("api_mobile/d_order_detail_model", "dodm");
        $this->load("api_mobile/d_order_proses_model", "dopm");
        $this->load("api_mobile/d_order_detail_item_model", "dodim");
        $this->load("api_mobile/e_rating_model", "erm");
    }
    private function __getRatings($nation_code, $d_order_id)
    {
        $rs = array();
        $ratings = $this->erm->getByOrderId($nation_code, $d_order_id);
        foreach ($ratings as $rate) {
            $key = $rate->nation_code.'-'.$rate->d_order_id.'-'.$rate->d_order_detail_id;
            $rs[$key] = $this->__ratingObj($rate->seller_rating, $rate->buyer_rating);
        }
        return $rs;
    }
    private function __ratingObj($seller_rating=0, $buyer_rating=0)
    {
        $r = new stdClass();
        $r->seller = new stdClass();
        $r->seller->rating_value = (int) $seller_rating;
        $r->buyer = new stdClass();
        $r->buyer->rating_value = (int) $buyer_rating;
        return $r;
    }
    private function __orderAddresses($nation_code, $pelanggan, $order)
    {
        //addresses init
        $addresses = new stdClass();
        $addresses->billing = new stdClass();
        $addresses->shipping = new stdClass();

        //get billing address
        $jenis_alamat = 'Billing Address';
        $address_status = $this->ccm->getByClassifiedByCodeName($nation_code, "address", $jenis_alamat);
        if (!isset($address_status->code)) {
            $address_status->code = 'A1';
        }
        $addresses->billing = $this->doam->getByOrderIdBuyerIdStatusAddress($nation_code, $order->id, $pelanggan->id, $address_status->code);

        //get shipping address
        //by Donny Dennison - 17 juni 2020 20:18
        // request by Mr Jackie change Shipping Address into Receiving Address
        // $jenis_alamat = 'Shipping Address';
        $jenis_alamat = 'Receiving Address';
        $address_status = $this->ccm->getByClassifiedByCodeName($nation_code, "address", $jenis_alamat);
        if (!isset($address_status->code)) {
            $address_status->code = 'A2';
        }
        $addresses->shipping = $this->doam->getByOrderIdBuyerIdStatusAddress($nation_code, $order->id, $pelanggan->id, $address_status->code);
        return $addresses;
    }

    //by Donny Dennison - 5 january 2021 10:37
    //show half rejected product in api rejected order list
    // private function __orderSellers($nation_code, $pelanggan, $order, $d_order_detail_id="")
    private function __orderSellers($nation_code, $pelanggan, $order, $d_order_detail_id="", $showRejectProduct=0)
    {
        $ditems = array();
        $d_order_detail_id = (int) $d_order_detail_id;
        if ($order->order_status == "waiting_for_payment") {
            $d_order_detail_id = 0;
        }

        if ($d_order_detail_id>0) {
            $detail_items = $this->dodim->getByOrderIdDetailId($nation_code, $order->id, $d_order_detail_id);
        } else {
            $detail_items = $this->dodim->getByOrderId($nation_code, $order->id);
        }
        $order_item_total = 0;
        $itm_sub_total = 0.0;
        $dicount = count($detail_items);
        $dtcount = 0;
        $ddetail = array();
        $bs = array(); //buyer status
        foreach ($detail_items as $di) {
            if ($d_order_detail_id>0) {
                if ($d_order_detail_id != $di->d_order_detail_id) {
                    $this->seme_log->write("api_mobile", 'API_Mobile/buyer/Order::__orderSellers -- continue d_order_detail_id');
                    continue;
                }
            }

            //by Donny Dennison - 5 january 2021 10:37
            //show half rejected product in api rejected order list
            //START by Donny Dennison - 5 january 2021 10:37
            if($showRejectProduct == 0){
            //END by Donny Dennison - 5 january 2021 10:37

                if (empty($di->is_rejected_all) && ($di->shipment_status == "delivered" || $di->shipment_status == "succeed") && $di->buyer_status == "rejected") {
                    $this->seme_log->write("api_mobile", 'API_Mobile/buyer/Order::__orderSellers -- continue empty is_rejected_all');
                    continue;
                }

            //by Donny Dennison - 5 january 2021 10:37
            //show half rejected product in api rejected order list
            //START by Donny Dennison - 5 january 2021 10:37
            }else{
                if (($di->shipment_status == "delivered" || $di->shipment_status == "succeed") && $di->buyer_status != "rejected") {
                    // $this->seme_log->write("api_mobile", 'API_Mobile/buyer/Order::__orderSellers -- continue empty is_rejected_all');
                    continue;
                }
            }
            //END by Donny Dennison - 5 january 2021 10:37

            $di->nama = html_entity_decode($di->nama,ENT_QUOTES);
            $di->c_produk_nama = html_entity_decode($di->c_produk_nama,ENT_QUOTES);
            
            //init key
            $key  = $di->nation_code.'-';
            $key .= $di->d_order_id.'-';
            $key .= $di->d_order_detail_id;

            //create detail object
            if (!isset($ddetail[$key])) {
                $ddetail[$key] = new stdClass();
                $ddetail[$key]->nama = '';
                $ddetail[$key]->sub_total = 0.0;
                $ddetail[$key]->total_item = 0;
                $ddetail[$key]->total_qty = 0;
                $ddetail[$key]->total_item=0;
                $ddetail[$key]->thumb = $di->thumb;
                $ddetail[$key]->foto = $di->foto;
                $ddetail[$key]->thumb2 = $di->thumb;
                $ddetail[$key]->foto2 = $di->foto;
                $order_item_total++;
            }
            $ddetail[$key]->nama .= $di->nama.', ';
            if (!isset($ditems[$key])) {
                $ditems[$key] = array();
            }
            $ditems[$key][] = $di;

            //by Donny Dennison - 2 august 2020 14:47
            //bug fixing earning total in d_order_detail table
            // $itm_sub_total += $di->qty * $di->harga_jual;
            if($di->buyer_status != 'rejected'){
                $itm_sub_total += $di->qty * $di->harga_jual;
            }

            $dtcount++;
        }
        unset($detail_items);
        unset($di);

        if ($d_order_detail_id>0 && $dicount != $dtcount) {
            $du = array();
            $du['total_item'] = $dtcount;
            $du['sub_total'] = $itm_sub_total;
            $this->dodm->update($nation_code, $order->id, $d_order_detail_id, $du);
        }

        if ($d_order_detail_id>0) {
            $order_details = $this->dodm->getByOrderIdDetailId($nation_code, $order->id, $d_order_detail_id);
        } else {
            $order_details = $this->dodm->getByOrderId($nation_code, $order->id);
        }

        foreach ($order_details as &$dj) {
            $key  = $dj->nation_code.'-';
            $key .= $dj->d_order_id.'-';
            $key .= $dj->d_order_detail_id;

            //casting image
            $dj->buyer_status = 'wait';
            $dj->d_order_detail_id = $dj->id;
            $dj->foto = $this->cdn_url($dj->foto);
            $dj->thumb = $this->cdn_url($dj->thumb);

            //by Donny Dennison - 23 september 2020 15:42
            //add direct delivery feature
            //START by Donny Dennison - 23 september 2020 15:42

            if (strtolower($dj->shipment_service) == 'direct delivery') {

                $dj->shipment_icon = $this->cdn_url("assets/images/direct_delivery.png");

            //by Donny Dennison - 15 september 2020 17:45
            //change name, image, etc from gogovan to gogox
            // if (strtolower($dj->shipment_service) == 'gogovan') {
            // if (strtolower($dj->shipment_service) == 'gogox') {
            }else if (strtolower($dj->shipment_service) == 'gogox') {

            //END by Donny Dennison - 23 september 2020 15:42

                //by Donny Dennison - 15 september 2020 17:45
                //change name, image, etc from gogovan to gogox
                // $dj->shipment_icon = $this->cdn_url("assets/images/gogovan.png");
                $dj->shipment_icon = $this->cdn_url("assets/images/gogox.png");

            } elseif (strtolower($dj->shipment_service) == 'qxpress') {
                $dj->shipment_icon = $this->cdn_url("assets/images/qxpress.png");
            } else {
                $dj->shipment_icon = $this->cdn_url("assets/images/unavailable.png");
            }
            $dj->sub_total_old = $dj->sub_total;
            $dj->grand_total_old = $dj->grand_total;

            $dj->products = array();
            if (isset($ditems[$key])) {
                $dj->c_produk_nama = '';
                foreach ($ditems[$key] as $dk) {
                    if ($dk->qty>1) {
                        $dj->c_produk_nama .= trim($dk->c_produk_nama).'('.$dk->qty.'),';
                    } else {
                        $dj->c_produk_nama .= trim($dk->c_produk_nama).',';
                    }
                }
                $dj->c_produk_nama = rtrim($dj->c_produk_nama, ',');
            }

            //convert to string after calculation
            if (isset($ditems[$key])) {
                $dj->products = $ditems[$key];
            }
            $cdit = count($dj->products);
            //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/buyer/Order::detail -> DItemsCount: ".$cdit);
            if (empty($cdit) && empty($dj->is_rejected_all)) {
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/buyer/Order::detail -> update to rejected all: $dj->d_order_id / $dj->id");
                }
                $du = array();
                $du['is_rejected_all'] = 1;
                $this->dodm->update($nation_code, $dj->d_order_id, $dj->id, $du);
                $dj->is_rejected_all = "1";
            }
        }
        $order_details = array_values($order_details);
        return $order_details;
    }

    private function __sortCol($sort_col, $tbl_as, $tbl2_as)
    {
        switch ($sort_col) {
            case 'id':
            $sort_col = "$tbl_as.id";
            break;
            case 'kondisi':
            $sort_col = "$tbl_as.b_kondisi_id";
            break;
            case 'harga':
            $sort_col = "$tbl_as.harga_jual";
            break;
            case 'harga_jual':
            $sort_col = "$tbl_as.harga_jual";
            break;
            case 'nama':
            $sort_col = "$tbl_as.nama";
            break;
            default:
            $sort_col = "$tbl_as.nama";
        }
        return $sort_col;
    }
    private function __sortDir($sort_dir)
    {
        $sort_dir = strtolower($sort_dir);
        if ($sort_dir == "desc") {
            $sort_dir = "DESC";
        } else {
            $sort_dir = "ASC";
        }
        return $sort_dir;
    }
    private function __page($page)
    {
        if (!is_int($page)) {
            $page = (int) $page;
        }
        if (empty($page)) {
            $page = 1;
        }
        return $page;
    }
    private function __pageSize($page_size)
    {
        $page_size = (int) $page_size;
        if ($page_size<=0) {
            $page_size = 100;
        }
        return $page_size;
    }

    private function __statusText($order, $detail)
    {
        $status_text = new stdClass();
        $status_text->seller = '';
        $status_text->buyer = '';
        $order->order_status = strtolower($order->order_status);
        if ($order->order_status == 'waiting_for_payment') {
            $status_text->seller = '-';
            $status_text->buyer = 'Waiting for Payment';
        } elseif ($order->order_status == 'forward_to_seller') {
            $detail->seller_status = strtolower($detail->seller_status);
            $detail->shipment_status = strtolower($detail->shipment_status);
            $detail->buyer_confirmed = strtolower($detail->buyer_confirmed);
            $detail->settlement_status = strtolower($detail->settlement_status);
            if ($detail->seller_status == 'unconfirmed') {
                $status_text->seller = 'Waiting for Confirmation';
                $status_text->buyer = 'Waiting for Confirmation';
            } elseif ($detail->seller_status == 'confirmed') {
                if ($detail->shipment_status == "process") {
                    $status_text->seller = 'In Process';
                    $status_text->buyer = 'In Process';

                //By Donny Dennison - 08-07-2020 16:16
                //Request by Mr Jackie, add new shipment status "courier fail"
                } elseif ($detail->shipment_status == "courier fail") {
                    $status_text->seller = 'Courier Fail';
                    $status_text->buyer = 'Courier Fail';

                } elseif ($detail->shipment_status == "delivered") {
                    $status_text->seller = 'Delivery in progress';
                    $status_text->buyer = 'Delivery in progress';
                } else {
                    $status_text->seller = 'Delivered';
                    $status_text->buyer = 'Delivered';
                    if ($detail->buyer_confirmed == 'confirmed') {
                        $status_text->seller = 'Finished';
                        $status_text->buyer = 'Finished';
                    }
                }
            } else {
                $status_text->buyer = 'Rejected';
                $status_text->seller = 'Rejected';
                if ($detail->settlement_status == 'completed') {
                    //$status_text->seller = 'Refund (Paid)';
                    //$status_text->buyer = 'Refund (Paid)';
                } elseif ($detail->settlement_status == 'processing') {
                    //$status_text->seller = 'Refund (On Process)';
                    //$status_text->buyer = 'Refund (On Process)';
                }
            }
        } elseif ($order->order_status == 'cancelled') {
            $status_text->seller = 'Order Cancelled';
            $status_text->buyer = 'Order Cancelled';
        } elseif ($order->order_status == 'expired') {
            $status_text->buyer = 'Order Expired';
            $status_text->seller = 'Order Expired';
        } else {
            $status_text->seller = 'Unknown';
            $status_text->buyer = 'Unknown';
        }
        return $status_text;
    }

    public function index()
    {
        $this->status = '404';
        header("HTTP/1.0 404 Not Found");
        $data = array();
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
    }
    /**
     * Get order detail for buyer perspective
     * @param  integer $id                ID from table d_drder
     * @param  mixed $d_order_detail_id ID from table d_order_detail or empty to view order from all seller
     * @return [type]                    [description]
     */

    //by Donny Dennison - 5 january 2021 10:37
    //show half rejected product in api rejected order list
    // public function detail($id, $d_order_detail_id="")
    public function detail($id, $d_order_detail_id="", $showRejectProduct=0)
    {
        //initial
        $dt = $this->__init();
        $data = array();
        $data['order'] = new stdClass();
        $data['order']->sellers = array();
        $data['order']->history = array();
        $data['order']->addresses = new stdClass();
        $data['order']->addresses->billing = new stdClass();
        $data['order']->addresses->shipping = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }
        if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/buyer/Order::detail -- OID: '.$id.' DOID: '.$d_order_detail_id);

        $id = (int) $id;
        if ($id<=0) {
            $this->status = 4010;
            $this->message = 'Invalid Order ID';
            if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/buyer/Order::detail -> forceClose '.$this->status.' '.$this->message);
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        $order = $this->order->getById($nation_code, $id);
        if (!isset($order->id)) {
            $this->status = 4011;
            $this->message = 'Order with supplied ID not found';
            if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/buyer/Order::detail -> forceClose '.$this->status.' '.$this->message);
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }
        if ($order->b_user_id != $pelanggan->id) {
            $this->status = 4012;
            $this->message = 'Sorry this order ID not belong to you';
            if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/buyer/Order::detail -> forceClose '.$this->status.' '.$this->message);
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }
        $d_order_detail_id = (int) $d_order_detail_id;
        if($d_order_detail_id<0){
            $this->status = 4022;
            $this->message = 'Invalid Order Detail ID';
            if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/buyer/Order::detail -> forceClose '.$this->status.' '.$this->message);
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }
        if ($order->order_status == "pending" || $order->order_status == "waiting_for_payment") {
            $d_order_detail_id = 0;
        }

        //put to log
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/buyer/Order::detail -> ORDERID: ".$id." DETAILID: ".$d_order_detail_id." OS: ".$order->order_status." PS: ".$order->payment_status);
        }
        //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/buyer/Order::detail -> ST: ".$order->sub_total." GT: ".$order->grand_total."");

        //by Donny Dennison - 18 november 2021 16:37
        //set timezone in api buyer->order and seller->order
        $timezone = $this->input->get("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }
        
        $data['order'] = $order;
        
        //by Donny Dennison - 5 january 2021 10:37
        //show half rejected product in api rejected order list
        // $data['order']->sellers = $this->__orderSellers($nation_code, $pelanggan, $order, $d_order_detail_id);
        $data['order']->sellers = $this->__orderSellers($nation_code, $pelanggan, $order, $d_order_detail_id, $showRejectProduct);

        $data['order']->addresses = $this->__orderAddresses($nation_code, $pelanggan, $order);

        $ratings = $this->__getRatings($nation_code, $order->id);

        //initial vars
        $order_item_total = 0;
        $order_sub_total = 0.0;
        $order_ongkir_total = 0.0;
        $order_grand_total = 0.0;
        $odd = array();

        //manipulator
        $date_begin = "";
        $date_expire = "";
        foreach ($data['order']->sellers as &$sel) {
            if ($d_order_detail_id>0) {
                if ($sel->d_order_detail_id != $d_order_detail_id) {
                    unset($sel);
                    continue;
                }
            }
            //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/buyer/Order::detail -> DST: ".$sel->sub_total." DGT: ".$sel->grand_total."");

            $sel->buyer_confirmed = 'unconfirmed';

            //reinit var(s)
            $sel->total_item = 0;
            $sel->total_qty = 0;
            $sel->date_current = date("Y-m-d H:i:s");
            $sel->sub_total = 0.0;
            $sel->grand_total = 0.0;
            $sel->foto2 = $sel->foto;
            $sel->thumb2 = $sel->thumb;
            $is_confirmed = 0;
            $accepted_count = 0;
            $rejected_count = 0;

            //casting image url
            // $sel->b_user_image_seller = $this->cdn_url($sel->b_user_image_seller);

            // by Muhammad Sofi - 26 October 2021 11:16
            // if user img & banner not exist or empty, change to default image
            // $sel->b_user_image_seller = $this->cdn_url($sel->b_user_image_seller);
            if(file_exists(SENEROOT.$sel->b_user_image_seller) && $sel->b_user_image_seller != 'media/user/default.png'){
                $sel->b_user_image_seller = $this->cdn_url($sel->b_user_image_seller);
            } else {
                $sel->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
            }

            //get rating object
            $sel->rating = $this->__ratingObj();
            $key_rating = $nation_code.'-'.$sel->d_order_id.'-'.$sel->d_order_detail_id;
            if (isset($ratings[$key_rating])) {
                $sel->rating = $ratings[$key_rating];
            }

            //redefined product list
            $item_count = count($sel->products);
            $sel->__item_count = count($sel->products);
            //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/buyer/order::detail -> AWAL SEL SUB TOTAL: $sel->sub_total");
            foreach ($sel->products as &$prd) {
                if ($sel->shipment_status == "delivered" || $sel->shipment_status == "succeed") {
                    if ($prd->buyer_status == "accepted") {
                        $accepted_count++;
                        $is_confirmed++;
                    } elseif ($prd->buyer_status == "rejected") {
                        $rejected_count++;
                        $is_confirmed++;
                    } else {
                    }
                }

                //re-cast cdn url
                if (isset($prd->foto)) {
                    $prd->foto = $this->cdn_url($prd->foto);
                }
                if (isset($prd->thumb)) {
                    $prd->thumb = $this->cdn_url($prd->thumb);
                }
                if (isset($prd->b_user_image_seller)) {
                    // by Muhammad Sofi - 26 October 2021 11:16
                    // if user img & banner not exist or empty, change to default image
                    // $prd->b_user_image_seller = $this->cdn_url($prd->b_user_image_seller);
                    if(file_exists(SENEROOT.$prd->b_user_image_seller) && $prd->b_user_image_seller != 'media/user/default.png'){
                        $prd->b_user_image_seller = $this->cdn_url($prd->b_user_image_seller);
                    } else {
                        $prd->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                }

                $prd->id = $sel->c_produk_id;
                $prd->c_produk_id = $sel->c_produk_id;
                $prd->shipment_icon = $sel->shipment_icon;

                //by Donny Dennison - 29 april 2021 14:06
                //add-void-and-refund-2c2p-after-reject-by-seller
                
                //by Donny Dennison - 2 august 2020 14:47
                //bug fixing earning total in d_order_detail table
                // $sel->sub_total+= ($prd->harga_jual*$prd->qty);
                
                // if ($prd->buyer_status != "rejected") {
                if ($prd->buyer_status != "rejected" && $prd->seller_status != 'rejected') {
                        
                    $sel->sub_total+= ($prd->harga_jual*$prd->qty);
                    
                }

                $sel->total_qty += $prd->qty;
                $sel->total_item++;
                //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/buyer/order::detail -> SEL_PRODUCT -> harga_jual: $prd->harga_jual QTY: $prd->qty");
                $order_item_total++;
            }

            //trim name
            $sel->nama = rtrim($sel->nama, ',');

            //set buyer confirmed
            if ($item_count == $is_confirmed) {
                $sel->buyer_confirmed = 'confirmed';
            }
            if ($item_count == $accepted_count) {
                $sel->buyer_status = "accepted";
            } elseif ($item_count == $rejected_count || !empty($sel->is_rejected_all)) {
                $sel->buyer_status = "rejected";
            }
            //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/buyer/order::detail -> AKHIR SEL SUB TOTAL: $sel->sub_total");

            //to seller
            $sel->grand_total = $sel->sub_total + ($sel->shipment_cost + $sel->shipment_cost_add);

            //to order
            $order_sub_total += $sel->sub_total;
            $order_ongkir_total +=($sel->shipment_cost + $sel->shipment_cost_add);

            //get order detail status teks
            $sel->status_text = $this->__statusText($data['order'], $sel);

            //get order detail history
            $sel->history = $this->dopm->getByOrderIdProdukId($nation_code, $sel->d_order_id, $sel->c_produk_id);
            foreach ($sel->history as &$his) {
                if (isset($his->initiator)) {
                    $his->initiator = ucwords($his->initiator);
                }
            }

            //convert to string
            $sel->total_qty = strval($sel->total_qty);
            $sel->total_item = strval($sel->total_item);
            $sel->sub_total = strval($sel->sub_total);
            $sel->grand_total = strval($sel->grand_total);

            //by Donny Dennison - 18 november 2021 16:37
            //set timezone in api buyer->order and seller->order
            $sel->date_begin = $this->customTimezone($sel->date_begin, $timezone);
            $sel->date_expire = $this->customTimezone($sel->date_expire, $timezone);

            if (true) {
                $odd[] = $sel;
            }
        }
        $data['order']->item_total = $order_item_total;
        $data['order']->sub_total = $order_sub_total;
        $data['order']->ongkir_total = $order_ongkir_total;
        $data['order']->grand_total = $order_sub_total+$order_ongkir_total;
        $data['order']->ongkir_total = strval($data['order']->ongkir_total);
        $data['order']->item_total = strval($data['order']->item_total);
        $data['order']->sub_total = strval($data['order']->sub_total);
        $data['order']->grand_total = strval($data['order']->grand_total);

        //update to order
        if (count($odd)) {
            foreach ($odd as $o) {
                $du = array();
                $du['sub_total'] = $o->sub_total;
                $du['grand_total'] = $o->grand_total;
                $du['buyer_confirmed'] = $o->buyer_confirmed;
                if ($o->buyer_confirmed == "confirmed") {
                    $du['shipment_status'] = "succeed";
                }
                $this->dodm->update($o->nation_code, $o->d_order_id, $o->d_order_detail_id, $du);
            }
        }
        if ($order->order_status == "waiting_for_payment") {
            $du = array();
            $du["item_total"] = $data['order']->item_total;
            $du["sub_total"] = $data['order']->sub_total;
            $du["ongkir_total"] = $data['order']->ongkir_total;
            $du["grand_total"] = $data['order']->grand_total;
            $this->order->update($nation_code, $id, $du);
        }

        //render output
        $this->status = 200;
        $this->message = 'Success';
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", 'API_Mobile/buyer/Order::detail -- finished '.$this->status.' '.$this->message);
        }

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
    }

    public function pending()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }
        //get produk data
        $dcount = $this->dodm->countBuyerPending($nation_code, $pelanggan->id);
        $ddata = $this->dodm->getBuyerPending($nation_code, $pelanggan->id);
        foreach ($ddata as &$pd) {
            $pd->d_order_grand_total = "0";
            // if (isset($pd->c_produk_nama)) {
                // $pd->c_produk_nama = $this->__dconv($pd->c_produk_nama);
            // }
            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            if (isset($pd->grand_total)) {
                $pd->d_order_grand_total = strval($pd->grand_total);
            }
            $pd->grand_total = $pd->d_order_grand_total;
            $pd->d_order_invoice_code = '';
            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
            if (isset($pd->harga_jual)) {
                $pd->harga_jual = strval($pd->harga_jual);
            }
            $iidc = 0;
            if (isset($pd->is_include_delivery_cost)) {
                $iidc = $pd->is_include_delivery_cost;
            }
            if ($iidc) {
                $pd->grand_total = strval($pd->harga_jual*$pd->qty);
                $pd->grand_total = strval($pd->grand_total);
                $pd->d_order_grand_total = $pd->grand_total;
            }
        }

        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
    }

    /**
     * list of waiting for payment
     * @return [type] [description]
     */
    public function waiting()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //by Donny Dennison - 18 november 2021 16:37
        //set timezone in api buyer->order and seller->order
        $timezone = $this->input->get("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        //pagination
        $page = (int) $this->input->get("page");
        $page_size = (int) $this->input->get("page_size");
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        //get order data
        $dcount = $this->dodm->countBuyerPaymentUnconfirmed($nation_code, $pelanggan->id);
        $ddata = $this->dodm->getBuyerPaymentUnconfirmed($nation_code, $pelanggan->id, $page, $page_size);
        //$this->debug($ddata);
        //die();
        foreach ($ddata as $pd) {
            $pd->d_order_invoice_code = '';
            // if (isset($pd->c_produk_nama)) {
                // $pd->c_produk_nama = $this->__dconv($pd->c_produk_nama);
            // }
            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
            $iidc = 0;
            if (isset($pd->is_include_delivery_cost)) {
                $iidc = $pd->is_include_delivery_cost;
            }
            if (!empty($iidc)) {
                $pd->grand_total = strval($pd->sub_total);
                $pd->d_order_grand_total = $pd->grand_total;
            } else {
                $pd->grand_total = strval($pd->sub_total+($pd->shipment_cost + $pd->shipment_cost_add));
                $pd->d_order_grand_total = $pd->grand_total;
            }

            $pd->date_expire_remaining = "00:00:00";
            if(strtotime($pd->date_current) < strtotime($pd->date_expire)){
                $date = new DateTime();
                $date2 = new DateTime($pd->date_expire);

                // $pd->date_expire_remaining = $date2->diff($date)->format("%a days and %H hours and %i minutes and %s seconds");
                $pd->date_expire_remaining = $date2->diff($date)->format("%H:%i:%s");
            }

            //by Donny Dennison - 18 november 2021 16:37
            //set timezone in api buyer->order and seller->order
            $pd->date_begin = $this->customTimezone($pd->date_begin, $timezone);
            $pd->date_expire = $this->customTimezone($pd->date_expire, $timezone);

        }

        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
    }

    /**
     * waiting for confirmation seller waiting for seller confirmation
     * @return [type] [description]
     */
    public function confirmation()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //by Donny Dennison - 18 november 2021 16:37
        //set timezone in api buyer->order and seller->order
        $timezone = $this->input->get("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        //get produk data
        $dcount = $this->dodm->countBuyerSellerUnconfirmed($nation_code, $pelanggan->id);
        $ddata = $this->dodm->getBuyerSellerUnconfirmed($nation_code, $pelanggan->id);
        foreach ($ddata as &$pd) {
            $pd->d_order_grand_total = "0";
            // if (isset($pd->c_produk_nama)) {
                // $pd->c_produk_nama = $this->__dconv($pd->c_produk_nama);
            // }
            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            if (isset($pd->grand_total)) {
                $pd->d_order_grand_total = strval($pd->grand_total);
            }
            $pd->grand_total = $pd->d_order_grand_total;
            $pd->d_order_invoice_code = '';
            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->thumb)) {
                $pd->thumb = $this->cdn_url($pd->thumb);
            }
            if (isset($pd->foto)) {
                $pd->foto = $this->cdn_url($pd->foto);
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
            if (isset($pd->harga_jual)) {
                $pd->harga_jual = strval($pd->harga_jual);
            }
            if (isset($pd->sub_total)) {
                $pd->sub_total = strval($pd->sub_total);
            }
            if (isset($pd->ongkir_total)) {
                $pd->ongkir_total = strval($pd->ongkir_total);
            }
            if (isset($pd->grand_total)) {
                $pd->grand_total = strval($pd->grand_total);
            }

            //by Donny Dennison - 18 november 2021 16:37
            //set timezone in api buyer->order and seller->order
            $pd->date_begin = $this->customTimezone($pd->date_begin, $timezone);
            $pd->date_expire = $this->customTimezone($pd->date_expire, $timezone);

        }

        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
    }

    /**
     * get order process or confirmed by seller, order in process
     * @return [type] [description]
     */
    public function process()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //by Donny Dennison - 18 november 2021 16:37
        //set timezone in api buyer->order and seller->order
        $timezone = $this->input->get("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        //get produk data
        $dcount = $this->dodm->countBuyerProcess($nation_code, $pelanggan->id);
        $ddata = $this->dodm->getBuyerProcess($nation_code, $pelanggan->id);
        foreach ($ddata as &$pd) {
            $pd->d_order_grand_total = "0";
            // if (isset($pd->c_produk_nama)) {
                // $pd->c_produk_nama = $this->__dconv($pd->c_produk_nama);
            // }
            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            if (isset($pd->grand_total)) {
                $pd->d_order_grand_total = strval($pd->grand_total);
            }
            $pd->d_order_invoice_code = '';
            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
            if (isset($pd->harga_jual)) {
                $pd->harga_jual = strval($pd->harga_jual);
            }
            if (isset($pd->sub_total)) {
                $pd->sub_total = strval($pd->sub_total);
            }
            if (isset($pd->ongkir_total)) {
                $pd->ongkir_total = strval($pd->ongkir_total);
            }
            if (isset($pd->grand_total)) {
                $pd->grand_total = strval($pd->grand_total);
            }

            //by Donny Dennison - 18 november 2021 16:37
            //set timezone in api buyer->order and seller->order
            $pd->date_begin = $this->customTimezone($pd->date_begin, $timezone);
            $pd->date_expire = $this->customTimezone($pd->date_expire, $timezone);

        }
        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
    }

    //delivery in progress (seller has change status)
    public function delivered()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/buyer/Order::delivered");
        }

        //by Donny Dennison - 18 november 2021 16:37
        //set timezone in api buyer->order and seller->order
        $timezone = $this->input->get("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        //get produk data
        $dcount = $this->dodm->countBuyerDelivered($nation_code, $pelanggan->id);
        $ddata = $this->dodm->getBuyerDelivered($nation_code, $pelanggan->id);
        foreach ($ddata as &$pd) {
            $pd->d_order_invoice_code = '';
            // if (isset($pd->c_produk_nama)) {
                // $pd->c_produk_nama = $this->__dconv($pd->c_produk_nama);
            // }
            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
            $iidc = 0;
            if (isset($pd->is_include_delivery_cost)) {
                $iidc = $pd->is_include_delivery_cost;
            }
            if (!empty($iidc)) {
                $pd->grand_total = strval($pd->sub_total);
                $pd->d_order_grand_total = $pd->grand_total;
            } else {
                $pd->grand_total = strval($pd->sub_total+($pd->shipment_cost + $pd->shipment_cost_add));
                $pd->d_order_grand_total = $pd->grand_total;
            }

            //by Donny Dennison - 18 november 2021 16:37
            //set timezone in api buyer->order and seller->order
            $pd->date_begin = $this->customTimezone($pd->date_begin, $timezone);
            $pd->date_expire = $this->customTimezone($pd->date_expire, $timezone);
            
        }
        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
    }

    //list of received order
    public function received()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }
        //get produk data
        $dcount = $this->dodm->countBuyerReceived($nation_code, $pelanggan->id);
        $ddata = $this->dodm->getBuyerReceived($nation_code, $pelanggan->id);
        foreach ($ddata as &$pd) {
            $pd->d_order_grand_total = "0";
            // if (isset($pd->c_produk_nama)) {
                // $pd->c_produk_nama = $this->__dconv($pd->c_produk_nama);
            // }
            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            if (isset($pd->grand_total)) {
                $pd->d_order_grand_total = strval($pd->grand_total);
            }
            $pd->grand_total = $pd->d_order_grand_total;
            $pd->d_order_invoice_code = '';
            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
            if (isset($pd->harga_jual)) {
                $pd->harga_jual = strval($pd->harga_jual);
            }
            $iidc = 0;
            if (isset($pd->is_include_delivery_cost)) {
                $iidc = $pd->is_include_delivery_cost;
            }
            if ($iidc) {
                $pd->grand_total = strval($pd->harga_jual*$pd->qty);
                $pd->grand_total = strval($pd->grand_total);
                $pd->d_order_grand_total = $pd->grand_total;
            }
            if (isset($pd->date_begin)) {
                $pd->date_begin = null;
            }
            if (isset($pd->date_expire)) {
                $pd->date_expire = null;
            }
            if (isset($pd->date_current)) {
                $pd->date_current = null;
            }
        }
        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
    }

    //list of succeed
    public function succeed()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //pagination
        $page = (int) $this->input->get("page");
        $page_size = (int) $this->input->get("page_size");
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        //get produk data

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $dcount = $this->dodm->countBuyerSucceed($nation_code, $pelanggan->id);
        $dcount = $this->dodim->countBuyerSucceed($nation_code, $pelanggan->id);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $ddata = $this->dodm->getBuyerSucceed($nation_code, $pelanggan->id, $page, $page_size);
        $ddata = $this->dodim->getBuyerSucceed($nation_code, $pelanggan->id, $page, $page_size);

        foreach ($ddata as &$pd) {
            $pd->d_order_invoice_code = '';
            // if (isset($pd->c_produk_nama)) {
                // $pd->c_produk_nama = $this->__dconv($pd->c_produk_nama);
            // }
            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
            if (isset($pd->harga_jual)) {
                $pd->harga_jual = strval($pd->harga_jual);
            }
            if (isset($pd->sub_total)) {
                $pd->sub_total = strval($pd->sub_total);
            }
            if (isset($pd->ongkir_total)) {
                $pd->ongkir_total = strval($pd->ongkir_total);
            }
            if (isset($pd->grand_total)) {
                $pd->grand_total = strval($pd->grand_total);
            }
            $pd->d_order_sub_total = $pd->sub_total;
            $pd->d_order_grand_total = $pd->grand_total;
        }
        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
    }

    //list of rejected
    public function listrejected()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //pagination
        $page = (int) $this->input->get("page");
        $page_size = (int) $this->input->get("page_size");
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        //get produk data

        //by Donny Dennison - 5 january 2021 10:37
        //show half rejected product in api rejected order list
        // $dcount = $this->dodm->countBuyerRejected($nation_code, $pelanggan->id, $page, $page_size);
        $dcount = $this->dodim->countBuyerRejected($nation_code, $pelanggan->id, $page, $page_size);
        
        //by Donny Dennison - 5 january 2021 10:37
        //show half rejected product in api rejected order list
        // $ddata = $this->dodm->getBuyerRejected($nation_code, $pelanggan->id, $page, $page_size);
        $ddata = $this->dodim->getBuyerRejected($nation_code, $pelanggan->id, $page, $page_size);

        foreach ($ddata as &$pd) {
            $pd->d_order_grand_total = "0";
            $pd->d_order_invoice_code = '';
            // if (isset($pd->c_produk_nama)) {
                // $pd->c_produk_nama = $this->__dconv($pd->c_produk_nama);
            // }
            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
            if (isset($pd->harga_jual)) {
                $pd->harga_jual = strval($pd->harga_jual);
            }
            if (isset($pd->ongkir_total)) {
                $pd->ongkir_total = strval($pd->ongkir_total);
            }
            if (isset($pd->sub_total)) {
                $pd->sub_total = strval($pd->sub_total);
            }
            if (isset($pd->grand_total)) {
                $pd->grand_total = strval($pd->grand_total);
            }
            $pd->d_order_sub_total = $pd->sub_total;
            $pd->d_order_grand_total = $pd->grand_total;
        }
        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
    }

    //list of expired
    public function expired()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
            die();
        }
        //get produk data
        $dcount = $this->dodm->countBuyerExpired($nation_code, $pelanggan->id);
        $ddata = $this->dodm->getBuyerExpired($nation_code, $pelanggan->id);
        foreach ($ddata as &$pd) {
            $pd->d_order_grand_total = "0";
            $pd->d_order_invoice_code = '';
            // if (isset($pd->c_produk_nama)) {
                // $pd->c_produk_nama = $this->__dconv($pd->c_produk_nama);
            // }
            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);
            
            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
            if (isset($pd->harga_jual)) {
                $pd->harga_jual = strval($pd->harga_jual);
            }
            if (isset($pd->sub_total)) {
                $pd->sub_total = strval($pd->sub_total);
            }
            if (isset($pd->ongkir_total)) {
                $pd->ongkir_total = strval($pd->ongkir_total);
            }
            if (isset($pd->grand_total)) {
                $pd->grand_total = strval($pd->grand_total);
            }
            $pd->d_order_sub_total = $pd->sub_total;
            $pd->d_order_grand_total = $pd->grand_total;
        }
        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_order");
    }
}
