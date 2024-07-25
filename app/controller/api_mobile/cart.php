<?php
/**
 * API_Mobile Cart Controller
 */
class Cart extends JI_Controller
{
    public $berat_max_qxpress = 30;
    public $length_max_qxpress = 1.6;
    public $negara = 'SG';
    public $is_log = 1;
    public $qx_weight_limit = 30; //qxpress weight limit
    public $qx_side_limit = 300; //total side limit
    public $qx_long_limit = 150; //limit long in cm
    public $qx_width_limit = 150; //limit width / breadth in cm
    public $qx_height_limit = 150; //limit height in cm
    public $qx_limit = 150; //qx minimum side limit
    public $product_lists;
    public $i=0;
    public $success=0;

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_mobile/a_negara_model", 'anm');
        $this->load("api_mobile/b_user_model", 'bu');
        $this->load("api_mobile/b_user_alamat_model", 'bua');
        $this->load("api_mobile/common_code_model", 'ccm');
        $this->load("api_mobile/c_produk_model", 'cpm');
        $this->load("api_mobile/d_cart_model", 'cart');
        $this->load("api_mobile/d_order_model", 'order');
        $this->load("api_mobile/d_order_alamat_model", 'doam');
        $this->load("api_mobile/d_order_detail_model", 'dodm');
        $this->load("api_mobile/d_order_proses_model", 'dopm');
        $this->load("api_mobile/d_order_detail_item_model", 'dodim');
        $this->product_lists = array();
        $this->i = 0;
        $this->success = 0;

        //by Donny Dennison - 21 november 2022 13:03
        //new feature, block
        $this->load("api_mobile/c_block_model", "cbm");

    }

    /**
    * Generate keys for grouping product by b_user_id_seller, b_user_alamat_id_seller, d_order id, and nation code.
    * @param  int $nation_code  nation code
    * @param  int $d_order_id id    from table d_order
    * @param  object $pr    Product Object from table c_produk
    * @return string              Key for grouping product in associative array
    */
    private function __keyProduct($nation_code, $d_order_id, $pr)
    {
        $key  = $nation_code.'-';
        $key .= $d_order_id.'-';
        $key .= $pr->b_user_id.'-';
        $key .= $pr->b_user_alamat_id.'-';
        //$key .= strtolower($pr->shipment_service).'-';

        //by Donny Dennison - 23 september 2020 15:42
        //add direct delivery feature
        $key .= $pr->courier_services.'-';
        
        $key = rtrim($key, "-");
        return $key;
    }

    /**
    * Generate keys for grouping product without detecting its shipping method and shipping service
    * @param  object $pb    Product Object from table c_produk
    * @return string              Key for grouping product
    */
    private function __keyProduct3($pb)
    {
        $key  = $pb->nation_code.'-';
        $key .= $pb->b_user_id.'-';
        $key .= $pb->b_user_alamat_id.'-';

        //by Donny Dennison - 23 september 2020 15:42
        //add direct delivery feature
        $key .= $pb->courier_services.'-';
        
        $key = rtrim($key, "-");
        return $key;
    }

    /**
     * Generates cart object output
     * @param  int $nation_code nation code
     * @param  object $pelanggan   Object from table b_user
     * @return object              Cart object completed with product object from d_cart and c_produk table
     */
    private function __getCart($nation_code, $pelanggan)
    {
        $cart = new stdClass();
        $cart->product_count = 0;
        $cart->item_total = 0;
        $cart->sub_total = 0.0;
        $cart->berat_total = 0.0;

        array();
        $cart->sellers = $this->cart->getSellers($nation_code, $pelanggan->id);

        $cps = array();
        $cart_products = $this->cart->getByUserId($nation_code, $pelanggan->id);
        foreach ($cart_products as $cpd) {
            //sanitize
            // if (isset($cpd->nama)) {
            //     $cpd->nama = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($cpd->nama));
            // }
            // if (isset($cpd->brand)) {
            //     $cpd->brand = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($cpd->brand));
            // }
            
            $cpd->nama = html_entity_decode($cpd->nama,ENT_QUOTES);

            //castings
            if (isset($cpd->qty)) {
                $cpd->qty = (float) $cpd->qty;
            }
            if (isset($cpd->stok)) {
                $cpd->stok = (int) $cpd->stok;
            }
            if (isset($cpd->berat)) {
                $cpd->berat = (int) $cpd->berat;
            }
            if (isset($cpd->harga_jual)) {
                $cpd->harga_jual = (float) $cpd->harga_jual;
            }
            if (isset($cpd->dimension_long)) {
                $cpd->dimension_long = (float) $cpd->dimension_long;
            }
            if (isset($cpd->dimension_width)) {
                $cpd->dimension_width = (float) $cpd->dimension_width;
            }
            if (isset($cpd->dimension_height)) {
                $cpd->dimension_height = (float) $cpd->dimension_height;
            }

            //cart calculation
            $cart->product_count++;
            $cart->item_total += (int) $cpd->qty;
            $subtotal = $cpd->qty * $cpd->harga_jual;
            $cart->sub_total += $subtotal;
            $berat_total = $cpd->qty * $cpd->berat;
            $cart->berat_total += $berat_total;

            //for json sake
            //https://stackoverflow.com/questions/42981409/php7-1-json-encode-float-issue/43056278
            $cpd->berat = "".round($cpd->berat, 2);

            //product manipulator
            $seller_id = $cpd->b_user_id;
            if (!isset($cps[$seller_id])) {
                $cps[$seller_id] = array();
            }
            if (isset($cpd->thumb)) {
                $cpd->thumb = $this->cdn_url(str_replace("//", "/", $cpd->thumb));
            }
            if (isset($cpd->foto)) {
                $cpd->foto = $this->cdn_url(str_replace("//", "/", $cpd->foto));
            }

            $cps[$seller_id][] = $cpd;
        }
        unset($cpd,$cart_products);

        //hitung berat
        $cart->berat_total = ''.round($cart->berat_total, 2);

        foreach ($cart->sellers as &$s1) {
            $seller_id = $s1->id;
            // if (isset($s1->fnama)) {
            //     $s1->fnama = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($s1->fnama));
            // }
            if (isset($s1->image)) {
                $s1->image = $this->cdn_url(str_replace("//", "/", $s1->image));
            }
            $s1->products = array();
            if (isset($cps[$seller_id])) {
                $s1->products = $cps[$seller_id];
            }
        }
        return $cart;
    }

    /**
     * Get Order address from d_order_alamat
     * @param  [type] $nation_code [description]
     * @param  object $pelanggan   [description]
     * @param  object $order       [description]
     * @return object              Object address from table d_order_alamat
     */
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
            $address_status = new stdClass();
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
            $address_status = new stdClass();
            $address_status->code = 'A2';
        }
        $addresses->shipping = $this->doam->getByOrderIdBuyerIdStatusAddress($nation_code, $order->id, $pelanggan->id, $address_status->code);
        return $addresses;
    }
    /**
     * Generate initial object for order details
     * @param  [type] $nation_code [description]
     * @param  [type] $details     [description]
     * @param  [type] $key         [description]
     * @param  [type] $order       [description]
     * @param  [type] $pr          [description]
     * @param  [type] $prds        [description]
     * @param  [type] $did         [description]
     * @return array              Order Details array of array
     */
    private function __initDetails($nation_code, $details, $key, $order, $pr, $prds, $did)
    {
        $details[$key] = new stdClass();
        $details[$key]->nation_code = $nation_code;
        $details[$key]->d_order_id = $order->id;
        $details[$key]->b_user_id = $pr->b_user_id;
        $details[$key]->b_user_alamat_id = $pr->b_user_alamat_id;
        $details[$key]->shipment_vehicle = $pr->vehicle_types;
        $details[$key]->shipment_service = $pr->shipment_service;
        $details[$key]->is_include_delivery_cost = $pr->is_include_delivery_cost;
        $details[$key]->is_fashion = $pr->is_fashion;
        $details[$key]->id = $did;
        $details[$key]->total_qty = 0;
        $details[$key]->total_berat = 0;
        $details[$key]->total_panjang = 0;
        $details[$key]->total_lebar = 0;
        $details[$key]->total_tinggi = 0;
        $details[$key]->sub_total = 0;
        $details[$key]->grand_total = 0;
        $details[$key]->total_item = 0;
        $details[$key]->nama = $pr->nama;
        $details[$key]->thumb = $pr->thumb;
        $details[$key]->foto = $pr->foto;
        $details[$key]->produks = array();
        if (isset($pr->services_duration)) {
            $details[$key]->shipment_type = $pr->services_duration;
        } elseif (isset($pr->shipment_type)) {
            $details[$key]->shipment_type = $pr->shipment_type;
        } else {
            trigger_error("details shipment_type / services_duration not found");
            die();
        }
        return $details;
    }
    /**
     * Generate initial product object
     * @param  object   $order  order object from d_order table
     * @param  object   $pr     product object from c_produk table
     * @param  int      $did    d_order_detail_id
     * @return object           New formatted product object
     */
    private function __initProduct($order, $pr, $did)
    {
        $prv = new stdClass();
        $prv->nation_code = $order->nation_code;
        $prv->d_order_id = $order->id;
        $prv->d_order_detail_id = $did;
        $prv->c_produk_id = $pr->c_produk_id;
        $prv->b_user_id = $pr->b_user_id;
        $prv->b_user_alamat_id = $pr->b_user_alamat_id;
        $prv->is_fashion = $pr->is_fashion;
        $prv->is_include_delivery_cost = $pr->is_include_delivery_cost;
        $prv->harga_jual = $pr->harga_jual;
        $prv->qty = $pr->qty;
        $prv->berat = ($pr->berat);
        if (!empty($pr->is_fashion)) {
            $pr->dimension_long = 1;
            $pr->dimension_width = 1;
            $pr->dimension_height = 1;
            $pr->panjang = 1;
            $pr->lebar = 1;
            $pr->tinggi = 1;
        }
        $prv->dimension_long = ($pr->dimension_long);
        $prv->dimension_width = ($pr->dimension_width);
        $prv->dimension_height = ($pr->dimension_height);
        $prv->panjang = ($pr->dimension_long);
        $prv->lebar = ($pr->dimension_width);
        $prv->tinggi = ($pr->dimension_height);
        //$prv->total_qty = $pr->total_qty;
        $prv->total_berat = $pr->qty * $pr->berat;
        $prv->total_panjang = $pr->qty * $pr->dimension_long;
        $prv->total_lebar = $pr->qty * $pr->dimension_width;
        $prv->total_tinggi = $pr->qty * $pr->dimension_height;
        $prv->brand = $pr->brand;
        $prv->nama = $pr->nama;
        $prv->c_produk_nama = $pr->c_produk_nama;
        $prv->thumb = $pr->thumb;
        $prv->foto = $pr->foto;
        $prv->deskripsi = $pr->deskripsi;
        $prv->shipment_service = $pr->shipment_service;
        if (isset($pr->vehicle_types)) {
            $prv->shipment_vehicle = $pr->vehicle_types;
        } elseif (isset($pr->shipment_vehicle)) {
            $prv->shipment_vehicle = $pr->shipment_vehicle;
        } else {
            trigger_error("Prv vehicle_types / shipment_vehicle not found");
            die();
        }
        if (isset($pr->services_duration)) {
            $prv->shipment_type = $pr->services_duration;
        } elseif (isset($pr->shipment_type)) {
            $prv->shipment_type = $pr->shipment_type;
        } else {
            trigger_error("Prv shipment_type / services_duration not found");
            die();
        }
        return $prv;
    }
    /**
     * Cast url to CDN url or to base_url
     * @param  object $pr product object
     * @return object     new product object with new base url
     */
    private function __castPrUrl($pr)
    {
        $pr->foto = $this->cdn_url($pr->foto);
        $pr->thumb = $this->cdn_url($pr->thumb);
        if (isset($pr->b_user_image_seller)) {
            // by Muhammad Sofi - 26 October 2021 11:16
            // if user img & banner not exist or empty, change to default image
            // $pr->b_user_image_seller = $this->cdn_url($pr->b_user_image_seller);
            if(file_exists(SENEROOT.$pr->b_user_image_seller) && $pr->b_user_image_seller != 'media/user/default.png'){
                $pr->b_user_image_seller = $this->cdn_url($pr->b_user_image_seller);
            } else {
                $pr->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
            }
        }
        return $pr;
    }
    /**
     * initialize sellers object for each order
     * @param  [type] $sellers [description]
     * @param  [type] $order   [description]
     * @param  [type] $pr      [description]
     * @return object          [description]
     */
    private function __initSellers($sellers, $order, $pr)
    {
        //building seller data
        $b_user_id_seller = $pr->b_user_id;
        if (!isset($sellers[$b_user_id_seller])) {
            $seller = new stdClass();
            $seller->nation_code = $order->nation_code;
            $seller->d_order_id = $order->id;
            $seller->b_user_id = $b_user_id_seller;

            // by Muhammad Sofi - 27 October 2021 10:12
            // if user img & banner not exist or empty, change to default image
            // $seller->image = $pr->b_user_image_seller;
            if(file_exists(SENEROOT.$pr->b_user_image_seller) && $pr->b_user_image_seller != 'media/user/default.png'){
                $seller->image = $pr->b_user_image_seller;
            } else {
                $seller->image = $this->cdn_url('media/user/default-profile-picture.png');
            }
            $seller->nama = $pr->b_user_nama_seller;
            $seller->products = array();
            $seller->products[] = $pr;
            $sellers[$b_user_id_seller] = $seller;
        } else {
            $sellers[$b_user_id_seller]->products[] = $pr;
        }
        return $sellers;
    }

    /**
     * Dimension Calculation
     *   Only sum the shortest side
     *   Should passed with initDetails
     * @param  array    $details    array of array that obtained from __initDetails
     * @param  string   $key        key product
     * @param  object   $prv        product object
     * @return array                array of object
     */
    private function __dimensionCalculation($details, $key, $prv)
    {
        $p = $prv->panjang;
        $l = $prv->lebar;
        $t = $prv->tinggi;
        if (!empty($prv->is_fashion)) {
            $p = 1;
            $l = 1;
            $t = 1;
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/cart::__dimensionCalculation -> is_fashion: TRUE');
            }
        }
        $ap = $p;
        $al = $l;
        $at = $t;
        $v = $p*$l*$t;

        //by Donny Dennison - 14 october 2020 17:07
        //fix shipping after checkout
        $smallestLengthTimesQuantity = min($p, $l, $t) * $prv->qty;

        if ($prv->qty>1) {
            $this->seme_log->write("api_mobile", 'API_Mobile/cart::__dimensionCalculation -> qty more than 1');
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/cart::__dimensionCalculation -> total_qty: '.$prv->qty.' dimensionPerProduct: '.$p.'x'.$l.'x'.$t);
            }
            $qty = $prv->qty;
            $min = min($p, $l, $t);
            if ($min == $t && $min!=$l && $min!=$p) {
                $t = $t*$qty;
            } elseif ($min != $t && $min==$l && $min!=$p) {
                $l = $l*$qty;
            } elseif ($min != $t && $min!=$l && $min==$p) {
                $p = $p*$qty;
            } elseif ($min == $t && $min==$l && $min!=$p) {
                $l = $l*$qty;
            } elseif ($min != $t && $min==$l && $min==$p) {
                $l = $l*$qty;
            } elseif ($min == $t && $min!=$l && $min==$p) {
                $t = $t*$qty;
            } elseif ($min == $t && $min==$l && $min==$p) {
                $t = $t*$qty;
            } else {
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", 'API_Mobile/cart::__dimensionCalculation -> Something wrong');
                }
            }
            $v = $p*$l*$t; //new volume
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/cart::__dimensionCalculation -> INFO finalDimension: '.$p.'x'.$l.'x'.$t);
            }
        }

        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/cart::__dimensionCalculation --dimensionCalc: End");
        }
        $tp = $details[$key]->total_panjang;
        $tl = $details[$key]->total_lebar;
        $tt = $details[$key]->total_tinggi;
        if ($this->i==0) {
            $details[$key]->total_panjang = $p;
            $details[$key]->total_lebar = $l;
            $details[$key]->total_tinggi = $t;
        }
        $tv = $tp*$tl*$tt;

        //by Donny Dennison - 14 october 2020 17:07
        //fix shipping after checkout
        // $min1 = min($p, $l, $t);
        $min1 = $smallestLengthTimesQuantity;

        $mit1 = '';
        $min2 = min($tp, $tl, $tt);
        $mit2 = '';
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", 'API_Mobile/cart::__dimensionCalculation -> Current volume: '.$v.' total volume: '.$tv);
        }
        if ($v>$tv) {
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/cart::__dimensionCalculation -> current volume greater than total volume');
            }

            //box 1
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/cart::__dimensionCalculation -> get minimum side of box1');
            }
            if ($min1 == $t && $min1!=$l && $min1!=$p) {
                $mit1 = 't';
            } elseif ($min1 != $t && $min1==$l && $min1!=$p) {
                $mit1 = 'l';
            } elseif ($min1 != $t && $min1!=$l && $min1==$p) {
                $mit1 = 'p';
            } elseif ($min1 == $t && $min1==$l && $min1!=$p) {
                $mit1 = 'l';
            } elseif ($min1 != $t && $min1==$l && $min1==$p) {
                $mit1 = 'l';
            } elseif ($min1 == $t && $min1!=$l && $min1==$p) {
                $mit1 = 't';
            } elseif ($min1 == $t && $min1==$l && $min1==$p) {
                $mit1 = 't';
            }
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/cart::__dimensionCalculation -> box1 shortest side is: '.$mit1);
            }

            //box 2
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/cart::__dimensionCalculation -> get shortest side of box2');
            }

            //by Donny Dennison - 14 october 2020 17:07
            //fix shipping after checkout
            // if ($min2 == $t && $min2!=$l && $min2!=$p) {
            if ($min2 == $tt && $min2!=$tl && $min2!=$tp) {

                $mit2 = 't';
            } elseif ($min2 != $tt && $min2==$tl && $min2!=$tp) {
                $mit2 = 'l';
            } elseif ($min2 != $tt && $min2!=$tl && $min2==$tp) {
                $mit2 = 'p';
            } elseif ($min2 == $tt && $min2==$tl && $min2!=$tp) {
                $mit2 = 'l';
            } elseif ($min2 != $tt && $min2==$tl && $min2==$tp) {
                $mit2 = 'l';
            } elseif ($min2 == $tt && $min2!=$tl && $min2==$tp) {
                $mit2 = 't';
            } elseif ($min2 == $tt && $min2==$tl && $min2==$tp) {
                $mit2 = 't';
            }
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/cart::__dimensionCalculation -> box2 shortest side is : ".$mit2);
            }

            if ($mit1 == 't' && $mit2 == 't') {
                $t += $tt;
            } elseif ($mit1 == 't' && $mit2 == 'l') {
                $t += $tl;
            } elseif ($mit1 == 't' && $mit2 == 'p') {
                $t += $tp;
            } elseif ($mit1 == 'l' && $mit2 == 't') {
                $l += $tt;
            } elseif ($mit1 == 'l' && $mit2 == 'l') {
                $l += $tl;
            } elseif ($mit1 == 'l' && $mit2 == 'p') {
                $l += $tp;
            } elseif ($mit1 == 'p' && $mit2 == 't') {
                $p += $tt;
            } elseif ($mit1 == 'p' && $mit2 == 'l') {
                $p += $tl;
            } elseif ($mit1 == 'p' && $mit2 == 'p') {
                $p += $tp;
            }
            $details[$key]->total_panjang = $p;
            $details[$key]->total_lebar = $l;
            $details[$key]->total_tinggi = $t;
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/cart::__dimensionCalculation -> result: panjang_total: '.$details[$key]->total_panjang.' lebar_total: '.$details[$key]->total_lebar.' tinggi_total: '.$details[$key]->total_tinggi);
            }
        } else {
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/cart::__dimensionCalculation -> total volume greater than current volume');
            }

            //box 1
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/cart::__dimensionCalculation -> get shortest side of box1');
            }
            if ($min1 == $t && $min1!=$l && $min1!=$p) {
                $mit1 = 't';
            } elseif ($min1 != $t && $min1==$l && $min1!=$p) {
                $mit1 = 'l';
            } elseif ($min1 != $t && $min1!=$l && $min1==$p) {
                $mit1 = 'p';
            } elseif ($min1 == $t && $min1==$l && $min1!=$p) {
                $mit1 = 'l';
            } elseif ($min1 != $t && $min1==$l && $min1==$p) {
                $mit1 = 'l';
            } elseif ($min1 == $t && $min1!=$l && $min1==$p) {
                $mit1 = 't';
            } elseif ($min1 == $t && $min1==$l && $min1==$p) {
                $mit1 = 't';
            }
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/cart::__dimensionCalculation -> box1 shortest side is: '.$mit1);
            }

            //box 2
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/cart::__dimensionCalculation -> get minimum side of box2');
            }


            //by Donny Dennison - 14 october 2020 17:07
            //fix shipping after checkout
            // if ($min2 == $t && $min2!=$l && $min2!=$p) {
            if ($min2 == $tt && $min2!=$tl && $min2!=$tp) {

                $mit2 = 't';
            } elseif ($min2 != $tt && $min2==$tl && $min2!=$tp) {
                $mit2 = 'l';
            } elseif ($min2 != $tt && $min2!=$tl && $min2==$tp) {
                $mit2 = 'p';
            } elseif ($min2 == $tt && $min2==$tl && $min2!=$tp) {
                $mit2 = 'l';
            } elseif ($min2 != $tt && $min2==$tl && $min2==$tp) {
                $mit2 = 'l';
            } elseif ($min2 == $tt && $min2!=$tl && $min2==$tp) {
                $mit2 = 't';
            } elseif ($min2 == $tt && $min2==$tl && $min2==$tp) {
                $mit2 = 't';
            }

            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/cart::__dimensionCalculation -> box2 shortest side is: '.$mit2);
            }
            if ($mit1 == 't' && $mit2 == 't') {
                $tt += $t;
            } elseif ($mit1 == 't' && $mit2 == 'l') {

                //by Donny Dennison - 14 october 2020 17:07
                //fix shipping after checkout
                // $tt += $l;
                $tl += $t;

            } elseif ($mit1 == 't' && $mit2 == 'p') {

                //by Donny Dennison - 14 october 2020 17:07
                //fix shipping after checkout
                // $tt += $p;
                $tp += $t;

            } elseif ($mit1 == 'l' && $mit2 == 't') {

                //by Donny Dennison - 14 october 2020 17:07
                //fix shipping after checkout
                // $tl += $t;
                $tt += $l;

            } elseif ($mit1 == 'l' && $mit2 == 'l') {
                $tl += $l;
            } elseif ($mit1 == 'l' && $mit2 == 'p') {

                //by Donny Dennison - 14 october 2020 17:07
                //fix shipping after checkout
                // $tl += $p;
                $tp += $l;

            } elseif ($mit1 == 'p' && $mit2 == 't') {

                //by Donny Dennison - 14 october 2020 17:07
                //fix shipping after checkout
                // $tp += $t;
                $tt += $p;

            } elseif ($mit1 == 'p' && $mit2 == 'l') {

                //by Donny Dennison - 14 october 2020 17:07
                //fix shipping after checkout
                // $tp += $l;
                $tl += $p;

            } elseif ($mit1 == 'p' && $mit2 == 'p') {
                $tp += $p;
            }
            $details[$key]->total_panjang = $tp;
            $details[$key]->total_lebar = $tl;
            $details[$key]->total_tinggi = $tt;
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/cart::__dimensionCalculation -> result: TP: '.$details[$key]->total_panjang.' TL: '.$details[$key]->total_lebar.' TT: '.$details[$key]->total_tinggi);
            }
        }
        return $details;
    }

    private function __produkErrorList($order, $pr, $prv)
    {
        $prr = new stdClass();
        $prr->nation_code = $order->nation_code;
        $prr->id = $pr->id;
        $prr->b_user_id = $pr->b_user_id;
        $prr->brand = $pr->brand;
        $prr->nama = $pr->nama;
        $prr->qty = $prv->qty;
        $prr->berat = $prv->berat;
        $prr->dimension_long = $pr->dimension_long;
        $prr->dimension_width = $pr->dimension_width;
        $prr->dimension_height = $pr->dimension_height;
        return $prr;
    }

    //START by Donny Dennison - 17 january 2022 14:01
    //make image in order product standalone
    private function __copyImagex($nation_code, $url, $order_id="1", $order_detail_id="1", $product_id="0")
    {
        $sc = new stdClass();
        $sc->status = 500;
        $sc->message = 'Error';
        $sc->image = '';
        $sc->thumb = '';
        $order_id = (int) $order_id;

        $targetdir = $this->media_order_produk;
        $targetdircheck = realpath(SENEROOT.$targetdir);
        if (empty($targetdircheck)) {
          if (PHP_OS == "WINNT") {
            if (!is_dir(SENEROOT.$targetdir)) {
              mkdir(SENEROOT.$targetdir);
            }
          } else {
            if (!is_dir(SENEROOT.$targetdir)) {
              mkdir(SENEROOT.$targetdir, 0775);
            }
          }
        }

        $tahun = date("Y");
        $targetdir = $targetdir.DIRECTORY_SEPARATOR.$tahun;
        $targetdircheck = realpath(SENEROOT.$targetdir);
        if (empty($targetdircheck)) {
          if (PHP_OS == "WINNT") {
            if (!is_dir(SENEROOT.$targetdir)) {
              mkdir(SENEROOT.$targetdir);
            }
          } else {
            if (!is_dir(SENEROOT.$targetdir)) {
              mkdir(SENEROOT.$targetdir, 0775);
            }
          }
        }

        $bulan = date("m");
        $targetdir = $targetdir.DIRECTORY_SEPARATOR.$bulan;
        $targetdircheck = realpath(SENEROOT.$targetdir);
        if (empty($targetdircheck)) {
          if (PHP_OS == "WINNT") {
            if (!is_dir(SENEROOT.$targetdir)) {
              mkdir(SENEROOT.$targetdir);
            }
          } else {
            if (!is_dir(SENEROOT.$targetdir)) {
              mkdir(SENEROOT.$targetdir, 0775);
            }
          }
        }

        $file_path = SENEROOT.$url;

        $file_path_thumb = $url;
        $extension = pathinfo($file_path_thumb, PATHINFO_EXTENSION);
        $file_path_thumb = substr($file_path_thumb,0,strripos($file_path_thumb,'.'));
        $file_path_thumb = SENEROOT.$file_path_thumb.'-thumb.'.$extension;

        $filename = "$nation_code-$order_id-$order_detail_id-$product_id-".date('YmdHis');
        $filethumb = $filename."-thumb.".$extension;
        $filename = $filename.".".$extension;

        if (file_exists($file_path) && is_file($file_path)) {
            copy($file_path, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
            copy($file_path_thumb, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
        }

        $sc->status = 200;
        $sc->message = 'Success';
        $sc->thumb = str_replace("//", "/", $targetdir.'/'.$filethumb);
        $sc->image = str_replace("//", "/", $targetdir.'/'.$filename);


        if ($this->is_log) {
          $this->seme_log->write("api_mobile", 'API_Mobile/cart::__copyImagex -- INFO URL: '.$url.' OID:'.$order_id.' ODID:'.$order_detail_id.' PID:'.$product_id.' '.$sc->status.' '.$sc->message.'');
        }
        return $sc;
    }
    //END by Donny Dennison - 17 january 2022 14:01

    public function index()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['produk_count'] = 0;
        $data['produk'] = array();
        $data['cart'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Cart::index -> Executed");
        }
        $tbl_as = $this->cart->getTableAlias();
        $page = 0;
        $pagesize = 1000; //has different
        $sort_col = "$tbl_as.cdate";
        $sort_dir = "desc";
        $keyword = "";

        //by Donny Dennison - 20 july 2020 17:37
        // remove the product from cart if the stock is out of stock
        //START change by Donny Dennison

        //check products
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Cart::index -> START Check product is out of stock or not for user : nation code = $nation_code , user id = $pelanggan->id");
        }


        $check_product = $this->cart->getAll($nation_code, $pelanggan->id, $page, $pagesize, $sort_col, $sort_dir, $keyword);

        if (count($check_product)>0) {

            foreach ($check_product as $cp) {

                if (isset($cp->id)) {

                    if ($cp->stok<=0) {

                        $this->seme_log->write("api_mobile", 'API_Mobile/Cart::index -> product '.($cp->nama).' is out of stock');
                       
                       $this->cart->del($nation_code, $pelanggan->id, $cp->c_produk_id);

                    }

                    if ($cp->b_user_is_active == 0) {

                        $this->seme_log->write("api_mobile", 'API_Mobile/Cart::index -> product '.($cp->nama).' seller already inactive');
                       
                       $this->cart->del($nation_code, $pelanggan->id, $cp->c_produk_id);

                    }

                }

            }

        }

        unset($check_product);
        unset($cp);

        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Cart::index -> END Check product is out of stock or not");
        }

        //END change by Donny Dennison


        $this->status = 200;
        $this->message = 'Success';
        $data['produk'] = $this->cart->getAll($nation_code, $pelanggan->id, $page, $pagesize, $sort_col, $sort_dir, $keyword);
        $data['produk_count'] = $this->cart->countAll($nation_code, $pelanggan->id, $keyword);
        foreach ($data['produk'] as &$dw) {

            $dw->nama = html_entity_decode($dw->nama,ENT_QUOTES);
            
            if (isset($dw->b_user_image_seller)) {
                if (empty($dw->b_user_image_seller)) {
                    $dw->b_user_image_seller = 'media/produk/default.png';
                }
                
                // by Muhammad Sofi - 28 October 2021 11:00
                // if user img & banner not exist or empty, change to default image
                // $dw->b_user_image_seller = $this->cdn_url(str_replace("//", "/", $dw->b_user_image_seller));
                if(file_exists(SENEROOT.$dw->b_user_image_seller) && $dw->b_user_image_seller != 'media/user/default.png'){
                    $dw->b_user_image_seller = $this->cdn_url(str_replace("//", "/", $dw->b_user_image_seller));
                } else {
                    $dw->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }
            if (isset($dw->foto)) {
                if (empty($dw->foto)) {
                    $dw->foto = 'media/produk/default.png';
                }
                $dw->foto = $this->cdn_url(str_replace("//", "/", $dw->foto));
            }
            if (isset($dw->thumb)) {
                if (empty($dw->thumb)) {
                    $dw->thumb = 'media/produk/default.png';
                }
                $dw->thumb = $this->cdn_url(str_replace("//", "/", $dw->thumb));
            }
        }
        $data['cart'] = $this->__getCart($nation_code, $pelanggan);
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
    }

    public function tambah()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['produk_count'] = 0;
        $data['produk'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        $this->__userUnconfirmedDenied($nation_code, $pelanggan);

        $c_produk_id = (int) $this->input->post('c_produk_id');
        if ($c_produk_id<=0) {
            $this->status = 810;
            $this->message = 'Product ID not found or has been deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        $getProductType = $this->cpm->getProductType($nation_code, $c_produk_id);
        if(!isset($getProductType->product_type)){
            $this->status = 810;
            $this->message = 'Product ID not found or has been deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        $getProductType = $getProductType->product_type;

        $produk = $this->cpm->getById($nation_code, $c_produk_id, $pelanggan, $getProductType);
        if (!isset($produk->id)) {
            $this->status = 810;
            $this->message = 'Product ID not found or has been deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        //START by Donny Dennison - 21 november 2022 15:03
        //new feature, block
        if($pelanggan->id != $produk->b_user_id_seller){

            $blockDataAccount = $this->cbm->getById($nation_code, 0, $produk->b_user_id_seller, "account", $pelanggan->id);
            $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $produk->b_user_id_seller);

            if(isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){

                $this->status = 1005;
                $this->message = "Add to cart is not allowed as you're blocked";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat_blocked_cart");
                die();

            }

        }
        //END by Donny Dennison - 21 november 2022 15:03
        //new feature, block

        if (!isset($produk->stok)) {
            $produk->stok = 0;
        }
        $produk->stok = (int) $produk->stok;
        if ($produk->stok<=0) {
            $this->status = 811;
            $this->message = 'Product run out of stock';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        $qty = (int) $this->input->post("qty");
        if ($qty<=0) {
            $this->status = 812;
            $this->message = 'Invalid number of QTY, if you want to remove product from cart. Using Cart/Change API';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        if ($qty > $produk->stok) {
            $this->status = 820;
            $this->message = ' It\'s lack of product quantity. Please check your cart';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Cart::tambah -- WARN '.$this->status.': '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        //by Donny Dennison 7 oktober 2020 - 14:10
        //add promotion face mask
        //START by Donny Dennison 7 oktober 2020 - 14:10

        if($c_produk_id == 1746){
            
            $checkAlreadyOrderFaceMask = $this->dodim->checkAlreadyOrderFaceMask($nation_code, $pelanggan->id, $c_produk_id);

            if ($checkAlreadyOrderFaceMask > 0) {
                $this->status = 813;
                $this->message = 'Sorry! One-time purchase is only allowed. You\'ve already bought this before';
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", 'API_Mobile/Cart::tambah -- WARN '.$this->status.': '.$this->message);
                }
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
                die();
            }

        }
        
        //END by Donny Dennison 7 oktober 2020 - 14:10

        $du = array();
        $du['qty'] = $qty;
        $cart_list = $this->cart->getById($nation_code, $pelanggan->id, $c_produk_id);
        if (isset($cart_list->id)) {
            $cart_list->qty = (int) $cart_list->qty;
            if ($du['qty'] > $produk->stok) {
                $this->status = 817;
                $this->message = ($produk->nama).' is out of stock';
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", 'API_Mobile/Cart::tambah -- forceClose '.$this->status.': '.$this->message.' buy: '.$du['qty'].' & current_stock: '.$produk->stok);
                }
                //$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
                //die();
            }

            //$du['qty'] = $cart_list->qty+$du['qty'];
            if ($du['qty'] > $cart_list->qty) {
                //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Cart::tambah -> Update Cart with qty: ".$du['qty']);
                $res = $this->cart->update($nation_code, $pelanggan->id, $c_produk_id, $du);
                if ($res) {
                    $this->status = 200;
                    $this->message = 'Success';
                } else {
                    $this->status = 814;
                    $this->message = 'Failed updating to cart';
                }
            } else {
                //for information only
                $this->status = 815;
                $this->message = 'One or more ordered product is out of stock';
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", 'API_Mobile/Cart::tambah -- INFO '.$this->status.' '.$this->message);
                }

                // avoid strict
                $this->status = 200;
                $this->message = 'Success';
            }
        } else {
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Cart::tambah -> Insert Cart with qty: ".$du['qty']);
            }
            //insert data
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['c_produk_id'] = $c_produk_id;
            $di['b_user_id'] = $pelanggan->id;
            $di['id'] = 1;
            $di['qty'] = $qty;
            $di['cdate'] = 'NOW()';
            $res = $this->cart->set($di);
            if ($res) {
                $this->status = 200;
                $this->message = 'Success';
                //Facebook Pixel

                $getProductType = $this->cpm->getProductType($nation_code, $c_produk_id);
                $getProductType = $getProductType->product_type;

                $produksnya = $this->cpm->getById($nation_code, $c_produk_id, $pelanggan, $getProductType);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,'https://graph.facebook.com/v8.0/286031182689890/events');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $data= array(
                    'data' => array( 
                    array(
                        'event_name' => 'AddToCart',
                        'event_time' => strtotime('now'),
                        'event_id' => 'AddToCart'.date('YmdHis'),
                        'event_source_url' => 'https://sellon.net/product_detail.php?product_id='.$c_produk_id,
                        'user_data' => array(
                            'client_ip_address' => '35.240.185.29',
                            'client_user_agent' => 'browser'
                        ),
                        'custom_data' => array(
                            'value' => $produksnya->harga_jual,
                            'currency' => 'SGD',
                            'content_ids' => array(
                                'c_produk_id'.$c_produk_id
                            ),
                            'content_type' => 'product',
                        ),
                        "opt_out" => 'true'
                    )
                    ),
                );
                $data['access_token'] = 'EAAF2Y2qjJ1sBAC4m0Op07KUXF8oWZC6IlKTZAsEsLlZBBeCSGHZB7lnrmSZA2WLFw4A96xg9ZB9pbl9bRW991NpCEDKuzBbvXckn30aNefcfeBY9GoPAuQX7Ns5DzYSQBCFpZCZBlhf2502ieaIRbfZBEAbEf5GIQ4TT71uC9zf2puHGDwLkZCYDMn';

                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

                if($this->is_log) $this->seme_log->write("api_mobile", "Facebook Pixel ad to cart: ".json_encode($data));

                $result = curl_exec($ch);
                if (curl_errno($ch)) {
                    $this->seme_log->write("api_mobile", "__CurlFacebook -> ".curl_error($ch));
                    //return 0;
                    //echo 'Error:' . curl_error($ch);
                }
                curl_close($ch);
            } else {
                $this->status = 816;
                $this->message = 'Failed add to cart';
            }
        }
        $tbl_as = $this->cart->getTableAlias();
        $page = 0;
        $pagesize = 1000; //has different
        $sort_col = "$tbl_as.cdate";
        $sort_dir = "desc";
        $keyword = "";

        $data['produk'] = $this->cart->getAll($nation_code, $pelanggan->id, $page, $pagesize, $sort_col, $sort_dir, $keyword);
        $data['produk_count'] = $this->cart->countAll($nation_code, $pelanggan->id, $keyword);
        foreach ($data['produk'] as &$dw) {

            $dw->nama = html_entity_decode($dw->nama,ENT_QUOTES);

            if (isset($dw->b_user_image_seller)) {
                if (empty($dw->b_user_image_seller)) {
                    $dw->b_user_image_seller = 'media/produk/default.png';
                }
                
                // by Muhammad Sofi - 28 October 2021 11:00
                // if user img & banner not exist or empty, change to default image
                // $dw->b_user_image_seller = $this->cdn_url($dw->b_user_image_seller);
                if(file_exists(SENEROOT.$dw->b_user_image_seller) && $dw->b_user_image_seller != 'media/user/default.png'){
                    $dw->b_user_image_seller = $this->cdn_url($dw->b_user_image_seller);
                } else {
                    $dw->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }
            if (isset($dw->foto)) {
                if (empty($dw->foto)) {
                    $dw->foto = 'media/produk/default.png';
                }
                $dw->foto = $this->cdn_url($dw->foto);
            }
            if (isset($dw->thumb)) {
                if (empty($dw->thumb)) {
                    $dw->thumb = 'media/produk/default.png';
                }
                $dw->thumb = $this->cdn_url($dw->thumb);
            }
        }

        $data['cart'] = $this->__getCart($nation_code, $pelanggan);
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
    }

    public function tambah_coba()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['produk_count'] = 0;
        $data['produk'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        $this->__userUnconfirmedDenied($nation_code, $pelanggan);

        $c_produk_id = (int) $this->input->post('c_produk_id');
        if ($c_produk_id<=0) {
            $this->status = 810;
            $this->message = 'Product ID not found or has been deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        $getProductType = $this->cpm->getProductType($nation_code, $c_produk_id);
        if(!isset($getProductType->product_type)){
            $this->status = 810;
            $this->message = 'Product ID not found or has been deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        $getProductType = $getProductType->product_type;

        $produk = $this->cpm->getById($nation_code, $c_produk_id, $pelanggan, $getProductType);
        if (!isset($produk->id)) {
            $this->status = 810;
            $this->message = 'Product ID not found or has been deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }
        if (!isset($produk->stok)) {
            $produk->stok = 0;
        }
        $produk->stok = (int) $produk->stok;
        if ($produk->stok<=0) {
            $this->status = 811;
            $this->message = 'Product run out of stock';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        $qty = (int) $this->input->post("qty");
        if ($qty<=0) {
            $this->status = 812;
            $this->message = 'Invalid number of QTY, if you want to remove product from cart. Using Cart/Change API';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        if ($qty > $produk->stok) {
            $this->status = 820;
            $this->message = ' It\'s lack of product quantity. Please check your cart';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Cart::tambah -- WARN '.$this->status.': '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        $du = array();
        $du['qty'] = $qty;
        $cart_list = $this->cart->getById($nation_code, $pelanggan->id, $c_produk_id);
        if (isset($cart_list->id)) {
            $cart_list->qty = (int) $cart_list->qty;
            if ($du['qty'] > $produk->stok) {
                $this->status = 817;
                $this->message = ($produk->nama).' is out of stock';
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", 'API_Mobile/Cart::tambah -- forceClose '.$this->status.': '.$this->message.' buy: '.$du['qty'].' & current_stock: '.$produk->stok);
                }
                //$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
                //die();
            }

            //$du['qty'] = $cart_list->qty+$du['qty'];
            if ($du['qty'] > $cart_list->qty) {
                //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Cart::tambah -> Update Cart with qty: ".$du['qty']);
                $res = $this->cart->update($nation_code, $pelanggan->id, $c_produk_id, $du);
                if ($res) {
                    $this->status = 200;
                    $this->message = 'Success';
                } else {
                    $this->status = 814;
                    $this->message = 'Failed updating to cart';
                }
            } else {
                //for information only
                $this->status = 815;
                $this->message = 'One or more ordered product is out of stock';
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", 'API_Mobile/Cart::tambah -- INFO '.$this->status.' '.$this->message);
                }

                // avoid strict
                $this->status = 200;
                $this->message = 'Success';
            }
        } else {
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Cart::tambah -> Insert Cart with qty: ".$du['qty']);
            }
            //insert data
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['c_produk_id'] = $c_produk_id;
            $di['b_user_id'] = $pelanggan->id;
            $di['id'] = 1;
            $di['qty'] = $qty;
            $di['cdate'] = 'NOW()';
            $res = $this->cart->set($di);
            if ($res) {
                $this->status = 200;
                $this->message = 'Success';

                $getProductType = $this->cpm->getProductType($nation_code, $c_produk_id);
                $getProductType = $getProductType->product_type;

                $produksnya = $this->cpm->getById($nation_code, $c_produk_id, $pelanggan, $getProductType);
            //Facebook Pixel
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,'https://graph.facebook.com/v8.0/286031182689890/events');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $data= array(
                'data' => array( 
                  array(
                      'event_name' => 'AddToCart',
                      'event_time' => strtotime('now'),
                      'event_id' => 'AddToCart'.date('YmdHis'),
                      'event_source_url' => 'https://sellon.net/product_detail.php?product_id='.$c_produk_id,
                      'user_data' => array(
                          'client_ip_address' => '35.240.185.29',
                          'client_user_agent' => 'browser'
                      ),
                      'custom_data' => array(
                          'value' => $produksnya->harga_jual,
                          'currency' => 'SGD',
                          'content_ids' => array(
                              'c_produk_id'.$c_produk_id
                          ),
                          'content_type' => 'product',
                      ),
                      "opt_out" => 'true'
                  )
                ),
              );
            $data['access_token'] = 'EAAF2Y2qjJ1sBAC4m0Op07KUXF8oWZC6IlKTZAsEsLlZBBeCSGHZB7lnrmSZA2WLFw4A96xg9ZB9pbl9bRW991NpCEDKuzBbvXckn30aNefcfeBY9GoPAuQX7Ns5DzYSQBCFpZCZBlhf2502ieaIRbfZBEAbEf5GIQ4TT71uC9zf2puHGDwLkZCYDMn';

            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

            if($this->is_log) $this->seme_log->write("api_mobile", "Facebook Pixel ad to cart: ".json_encode($data));

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                $this->seme_log->write("api_mobile", "__CurlFacebook -> ".curl_error($ch));
                //return 0;
                //echo 'Error:' . curl_error($ch);
            }
            $this->__json_out($result);
            curl_close($ch);
            } else {
                $this->status = 816;
                $this->message = 'Failed add to cart';
            }
        }
        $tbl_as = $this->cart->getTableAlias();
        $page = 0;
        $pagesize = 1000; //has different
        $sort_col = "$tbl_as.cdate";
        $sort_dir = "desc";
        $keyword = "";

        $data['produk'] = $this->cart->getAll($nation_code, $pelanggan->id, $page, $pagesize, $sort_col, $sort_dir, $keyword);
        $data['produk_count'] = $this->cart->countAll($nation_code, $pelanggan->id, $keyword);
        foreach ($data['produk'] as &$dw) {
            if (isset($dw->b_user_image_seller)) {
                if (empty($dw->b_user_image_seller)) {
                    $dw->b_user_image_seller = 'media/produk/default.png';
                }
                
                // by Muhammad Sofi - 28 October 2021 11:00
                // if user img & banner not exist or empty, change to default image
                // $dw->b_user_image_seller = $this->cdn_url($dw->b_user_image_seller);
                if(file_exists(SENEROOT.$dw->b_user_image_seller) && $dw->b_user_image_seller != 'media/user/default.png'){
                    $dw->b_user_image_seller = $this->cdn_url($dw->b_user_image_seller);
                } else {
                    $dw->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }
            if (isset($dw->foto)) {
                if (empty($dw->foto)) {
                    $dw->foto = 'media/produk/default.png';
                }
                $dw->foto = $this->cdn_url($dw->foto);
            }
            if (isset($dw->thumb)) {
                if (empty($dw->thumb)) {
                    $dw->thumb = 'media/produk/default.png';
                }
                $dw->thumb = $this->cdn_url($dw->thumb);
            }
        }

        $data['cart'] = $this->__getCart($nation_code, $pelanggan);
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
    }

    public function edit()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['produk_count'] = 0;
        $data['produk'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        $c_produk_id = (int) $this->input->post('c_produk_id');
        if ($c_produk_id<=0) {
            $this->status = 817;
            $this->message = 'Invalid product ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        $getProductType = $this->cpm->getProductType($nation_code, $c_produk_id);
        if(!isset($getProductType->product_type)){
            $this->status = 818;
            $this->message = 'Product ID not found or has been deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        $getProductType = $getProductType->product_type;

        $produk = $this->cpm->getById($nation_code, $c_produk_id, $pelanggan, $getProductType);
        if (!isset($produk->id)) {
            $this->status = 818;
            $this->message = 'Product ID not found or has been deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        $qty = (int) $this->input->post("qty");
        if ($qty<0) {
            $this->status = 819;
            $this->message = 'Invalid number of QTY, if you want to remove product from cart. Using Cart/Change API';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Cart::edit --begin: ".$qty);
        }

        $cart_list = $this->cart->getById($nation_code, $pelanggan->id, $c_produk_id);
        if (isset($cart_list->id)) {
            //check stok
            $qty_now = $qty + $cart_list->qty;
            if ($qty>$produk->stok) {
                $this->status = 820;
                $this->message = ' It\'s lack of product quantity. Please check your cart';
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", 'API_Mobile/Cart::edit -- WARN '.$this->status.': '.$this->message);
                }
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
                die();
            }
            if ($qty>0) {
                //update qty
                $du = array();
                $du['qty'] = $qty;
                $du['cdate'] = 'NOW()';
                $res = $this->cart->update($nation_code, $pelanggan->id, $c_produk_id, $du);
                if ($res) {
                    $this->status = 200;
                    $this->message = 'Success';
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Cart::edit -> cart updated with: ".$du['qty']);
                    }
                } else {
                    $this->status = 821;
                    $this->message = 'Failed updating product qty from cart';
                }
            } else {
                //ajie request
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Cart::edit -> cart deleted");
                }
                $res = $this->cart->del($nation_code, $pelanggan->id, $c_produk_id);
                if ($res) {
                    $this->status = 200;
                    $this->message = 'Success';
                } else {
                    $this->status = 822;
                    $this->message = 'Failed deleting product from cart';
                }
            }
        } else {
            $this->status = 823;
            $this->message = 'Product with current ID not in cart yet';
        }
        $tbl_as = $this->cart->getTableAlias();
        $page = 0;
        $pagesize = 1000; //has different
        $sort_col = "$tbl_as.cdate";
        $sort_dir = "desc";
        $keyword = "";

        $data['produk'] = $this->cart->getAll($nation_code, $pelanggan->id, $page, $pagesize, $sort_col, $sort_dir, $keyword);
        $data['produk_count'] = $this->cart->countAll($nation_code, $pelanggan->id, $keyword);
        foreach ($data['produk'] as &$dw) {

            $dw->nama = html_entity_decode($dw->nama,ENT_QUOTES);

            if (isset($dw->b_user_image_seller)) {
                if (empty($dw->b_user_image_seller)) {
                    $dw->b_user_image_seller = 'media/produk/default.png';
                }
                
                // by Muhammad Sofi - 28 October 2021 11:00
                // if user img & banner not exist or empty, change to default image
                // $dw->b_user_image_seller = base_url($dw->b_user_image_seller);
                if(file_exists(SENEROOT.$dw->b_user_image_seller) && $dw->b_user_image_seller != 'media/user/default.png'){
                    $dw->b_user_image_seller = base_url($dw->b_user_image_seller);
                } else {
                    $dw->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }
            if (isset($dw->foto)) {
                if (empty($dw->foto)) {
                    $dw->foto = 'media/produk/default.png';
                }
                $dw->foto = base_url($dw->foto);
            }
            if (isset($dw->thumb)) {
                if (empty($dw->thumb)) {
                    $dw->thumb = 'media/produk/default.png';
                }
                $dw->thumb = base_url($dw->thumb);
            }
        }
        $data['cart'] = $this->__getCart($nation_code, $pelanggan);
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
    }
    public function hapus()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['produk_count'] = 0;
        $data['produk'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        $c_produk_id = (int) $this->input->post('c_produk_id');
        if ($c_produk_id<=0) {
            $this->status = 810;
            $this->message = 'Product ID not found or has been deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        //bypassing
        if (false) {

            $getProductType = $this->cpm->getProductType($nation_code, $c_produk_id);
            if(!isset($getProductType->product_type)){
                $this->status = 810;
                $this->message = 'Product ID not found or has been deleted';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
                die();
            }

            $getProductType = $getProductType->product_type;

            $produk = $this->cpm->getById($nation_code, $c_produk_id, $pelanggan, $getProductType);
            if (!isset($produk->id)) {
                $this->status = 810;
                $this->message = 'Product ID not found or has been deleted';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
                die();
            }
        }


        $cart_list = $this->cart->getById($nation_code, $pelanggan->id, $c_produk_id);
        if (isset($cart_list->id)) {
            $res = $this->cart->del($nation_code, $pelanggan->id, $c_produk_id);
            if ($res) {
                $this->status = 200;
                // $this->message = $cart_list->nama.' successfully removed from cart';
                $this->message = 'Success';
            } else {
                $this->status = 822;
                $this->message = 'Failed deleting product from cart';
            }
        } else {
            $this->status = 823;
            $this->message = 'Product with current ID not in cart yet';
        }
        $tbl_as = $this->cart->getTableAlias();
        $page = 0;
        $pagesize = 1000; //has different
        $sort_col = "$tbl_as.cdate";
        $sort_dir = "desc";
        $keyword = "";

        $data['produk'] = $this->cart->getAll($nation_code, $pelanggan->id, $page, $pagesize, $sort_col, $sort_dir, $keyword);
        $data['produk_count'] = $this->cart->countAll($nation_code, $pelanggan->id, $keyword);
        foreach ($data['produk'] as &$dw) {

            $dw->nama = html_entity_decode($dw->nama,ENT_QUOTES);
            
            if (isset($dw->b_user_image_seller)) {
                if (empty($dw->b_user_image_seller)) {
                    $dw->b_user_image_seller = 'media/produk/default.png';
                }
                
                // by Muhammad Sofi - 28 October 2021 11:00
                // if user img & banner not exist or empty, change to default image
                // $dw->b_user_image_seller = base_url($dw->b_user_image_seller);
                if(file_exists(SENEROOT.$dw->b_user_image_seller) && $dw->b_user_image_seller != 'media/user/default.png'){
                    $dw->b_user_image_seller = base_url($dw->b_user_image_seller);
                } else {
                    $dw->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }
            if (isset($dw->foto)) {
                if (empty($dw->foto)) {
                    $dw->foto = 'media/produk/default.png';
                }
                $dw->foto = base_url($dw->foto);
            }
            if (isset($dw->thumb)) {
                if (empty($dw->thumb)) {
                    $dw->thumb = 'media/produk/default.png';
                }
                $dw->thumb = base_url($dw->thumb);
            }
        }
        $data['cart'] = $this->__getCart($nation_code, $pelanggan);
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
    }
    public function format()
    {
        $data = array();
        $data['products'] = array();
        $ps = new stdClass();
        $ps->id = 1;
        $ps->qty = 1;
        $data['products'][] = $ps;
        $ps = new stdClass();
        $ps->id = 2;
        $ps->qty = 1;
        $data['products'][] = $ps;
        header("content-type: application/json");
        echo json_encode($data);
    }
    public function paynow()
    {
        //initial
        $dt = $this->__init();
        $d_order_id = 0;
        $sub_total = 0;
        $grand_total = 0;

        $this->status = 1400;
        $this->message = 'Cannot proceed to order confirmation, cart still empty';

        //default result
        $data = array();
        $data['order'] = new stdClass();
        $data['order']->addresses = new stdClass();
        $data['order']->addresses->billing = new stdClass();
        $data['order']->addresses->shipping = new stdClass();
        $data['order']->sellers = array();
        $data['order']->problem_constrain = '';
        $data['order']->problem_products = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
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
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        // jika email belum terkonfirmasi
        $this->__userUnconfirmedDenied($nation_code, $pelanggan);

        $post_data_json = $this->input->post("post_data");
        $post_data = json_decode($post_data_json);
        if (!isset($post_data->products)) {
            $this->status = 828;
            $this->message = 'Invalid post_data format';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        //if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow --POST: post_data: '.$post_data_json);
        if (!is_array($post_data->products)) {
            $this->status = 829;
            $this->message = 'Products on post_data must be an array';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }
        if (count($post_data->products)<=0) {
            $this->status = 830;
            $this->message = 'Please add at least one product on post_data';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        //check key inside products
        $i=0;
        $pids = array();
        $prds = array();
        foreach ($post_data->products as $pdp) {
            if (!isset($pdp->id)) {
                $this->status = 831;
                $this->message = 'Missing id on product array index-'.$i;
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
                die();
            }
            if (!isset($pdp->qty)) {
                $this->status = 832;
                $this->message = 'Missing qty on product array index-'.$i;
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
                die();
            }
            $pid = (int) $pdp->id;
            $prd = new stdClass();
            $prd->id = $pid;
            $prd->qty = (int) $pdp->qty;
            if (!isset($prds[$pid])) {
                $prds[$pid] = $prd;
                $prds[$pid]->nama = '';
                $prds[$pid]->thumb = '';
                $prds[$pid]->foto = '';
            } else {
                $prds[$pid]->qty += $prd->qty;
                $prds[$pid]->nama = '';
                $prds[$pid]->thumb = '';
                $prds[$pid]->foto = '';
            }
            $pids[] = $pid;
        }
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Cart::paynow -> PIDS: ".implode(",", $pids));
        }

        if (false) {
            //get order pendings if exists and rollback stock
            $pendings = $this->dodim->getPendingOrder($nation_code, $pelanggan->id);

            //convert pendings to cancel
            $this->order->setPending2Cancel($nation_code, $pelanggan->id);
            $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> convertPending2Cancel');
            //traverse product stock
            $pc = count($pendings);
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> INFO OrderPendingCount: '.$pc);
            }
            if ($pc) {
                foreach ($pendings as $pending) {
                    $this->cpm->addStok($nation_code, $pending->c_produk_id, $pending->qty);
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> RollBack Stock PID:'.$pending->c_produk_id.' qty: '.$pending->qty);
                    }
                }
            }
        }
        // out of stocks
        $oos = array();

        //check products
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Cart::paynow -> Check product 1st step");
        }
        $error=0;
        $pre_checks = array();
        $check_product = $this->cpm->getByIdsForCart($nation_code, $pids);
        if (count($check_product)>0) {
            foreach ($check_product as $cp) {
                $pid = (int) $cp->id;
                $stok = (int) $cp->stok;
                if (isset($cp->id)) {
                    $qty = (int) $prds[$pid]->qty;
                    if ($cp->stok<=0) {
                        $this->status = 1493;
                        $this->message = ($cp->nama).' is out of stock';
                        $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> forceClose: '.$this->status.' '.$this->message.' buy: '.$qty.' & current_stock: '.$cp->stok);
                        $o = new stdClass();
                        $o->id = $cp->id;
                        $o->nama = $cp->nama;
                        $oos[] = $o;
                    }
                    $prds[$pid]->nama = $cp->nama;
                    $prds[$pid]->foto = $cp->foto;
                    $prds[$pid]->thumb = $cp->thumb;
                    $prds[$pid]->is_fashion = "0";
                    $cp->c_produk_id = $prds[$pid]->id;
                    $cp->c_produk_nama = $cp->nama;
                    $cp->b_user_id = $cp->b_user_id;
                    $cp->qty = $qty;
                    $cp->shipment_service = $cp->courier_services;
                    $cp->total_berat = $qty * $cp->berat;
                    $cp->total_panjang = $qty * $cp->dimension_long;
                    $cp->total_lebar = $qty * $cp->dimension_width;
                    $cp->total_tinggi = $qty * $cp->dimension_height;
                    $key = $this->__keyProduct3($cp);
                    if (!isset($pre_checks[$key])) {
                        $pre_checks[$key] = new stdClass();
                        $pre_checks[$key]->total_berat = 0;
                        $pre_checks[$key]->products = array();
                    }
                    $pre_checks[$key]->total_berat += ($cp->qty * $cp->berat);
                    $pre_checks[$key]->products[] = $cp;
                } else {
                    $error=1;
                    break;
                }
            }
        } else {
            $error = 3;
        }

        if ($error == 1) {
            $this->status = 1496;
            $this->message = 'One or more product ID is invalid';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow ERROR: '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }
        if ($error == 2) {
            //$this->status = 1497;
            //$this->message = 'Stock quantity for '.$nama.' unavailable';
            //if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow ERROR: '.$this->message);
            //$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            //die();
        }
        if ($error == 3) {
            $this->status = 1498;
            $this->message = 'Selected product unavailable or not found';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow ERROR: '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }
        if ($error == 4) {
            $this->status = 1499;
            $this->message = 'Sorry, this product too big for our shipping service';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow ERROR: '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }
        
        $oosc = count($oos);
        if ($oosc>0) {
            $this->status = 1493;
            $this->message = $oos[0]->nama.' is out of stock';
            if($oosc>1){
              $this->message = '';

              //by Donny Dennison - 2 june 2020 15:10
              // request by mobile developer add "|" so then can rtim the product
              $loop = 1;
              
              foreach($oos as $oosv){

                //by Donny Dennison - 2 june 2020 15:10
                // request by mobile developer add "|" so then can rtim the product    
                // $this->message .= $oosv->nama.', ';
                if(count($oos) == $loop){

                    $this->message .= $oosv->nama.'';

                }else{

                    $this->message .= $oosv->nama.'|';

                }
                $loop++;
                    
              }

              //by Donny Dennison - 2 june 2020 15:10
              // request by mobile developer add "|" so then can rtim the product   
              // $this->message = rtrim($this->message,', ');
              // $this->message .= ' are out of stock';
            
            }
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -- forceClose '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
            die();
        }

        //by Donny Dennison 7 oktober 2020 - 14:10
        //add promotion face mask
        foreach ($check_product as $cp) {

            if (isset($cp->id)) {
            
                $pid = (int) $cp->id;
                $qty = (int) $prds[$pid]->qty;
                
                if($cp->id == 1746 && $qty != 1){

                    $this->status = 1494;
                    $this->message = 'Sorry! "'.$cp->nama.'" is only allowed to buy one';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart",$cp->nama);
                    die();

                }

            }
        }

        //reset shipment courier
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> reassigning shipment services');
        }
        $check_product = array();
        $force_gogovan = 0;
        foreach ($pre_checks as $pc) {
            foreach ($pc->products as $pcp) {

                //by Donny Dennison - 23 september 2020 15:42
                //add direct delivery feature
                // if ($pc->total_berat>$this->qx_weight_limit) {
                if ($pc->total_berat>$this->qx_weight_limit && strtolower($pcp->courier_services) != 'direct delivery') {
                    
                    //by Donny Dennison - 15 september 2020 17:45
                    //change name, image, etc from gogovan to gogox
                    // $pcp->courier_services = 'gogovan';
                    $pcp->courier_services = 'gogox';

                    $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> changed to gogovan, because its above qxpress weight limit '.$this->qx_weight_limit);
                }

                //by Donny Dennison - 14 october 2020 17:07
                //fix shipping after checkout
                //START by Donny Dennison - 14 october 2020 17:07
                $pid = (int) $pcp->id;
                $qty = (int) $prds[$pid]->qty;
                $pcp->total_panjang = $qty * $pcp->dimension_long;
                $pcp->total_lebar = $qty * $pcp->dimension_width;
                $pcp->total_tinggi = $qty * $pcp->dimension_height;

                $pcp->volume = $pcp->total_panjang + $pcp->total_lebar + $pcp->total_tinggi;
                //END by Donny Dennison - 14 october 2020 17:07

                $check_product[] = $pcp;
            }
        }

        //by Donny Dennison - 14 october 2020 17:07
        //fix shipping after checkout
        array_multisort(array_column($check_product, 'b_user_id'), SORT_ASC, 
        				array_column($check_product, 'volume'), SORT_DESC,
        				$check_product);

        //check products 2nd steps
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> check product 2nd step');
        }
        $error=0;
        $nama = '';
        $stok = 0;
        $qty = 0;
        $produk_nama_error = "";
        foreach ($check_product as $cp) {
            $pid = (int) $cp->id;
            $stok = (int) $cp->stok;
            $nama = $cp->nama;
            if (isset($prds[$pid]->id)) {
                $qty = (int) $prds[$pid]->qty;
                //if(mb_strlen($nama)>=13) $nama = mb_substr($nama,0,13);
                $nama = trim($nama);

                //by Donny Dennison - 24 february 2021 10:30
                //remove total quantity beside product name in order
                // if ($qty>1) {
                //     $nama .= "($qty)";
                // }
                
                $prds[$pid]->nama = $nama;
                $prds[$pid]->foto = $cp->foto;
                $prds[$pid]->thumb = $cp->thumb;
                $prds[$pid]->is_fashion = "0";
                $cp->c_produk_id = $prds[$pid]->id;
                $cp->c_produk_nama = $cp->nama;
                $cp->b_user_id = $cp->b_user_id;
                $cp->nama = $nama;
                $cp->qty = $qty;
                $cp->shipment_service = $cp->courier_services;
                $cp->total_berat = $qty * $cp->berat;
                $cp->total_panjang = $qty * $cp->dimension_long;
                $cp->total_lebar = $qty * $cp->dimension_width;
                $cp->total_tinggi = $qty * $cp->dimension_height;

                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Cart::paynow -> Check Product -- PID: ".$cp->c_produk_id.", BUID: ".$cp->b_user_id.'/'.$cp->b_user_alamat_id." shipment: ".$cp->courier_services." is_include_delivery_cost: ".$cp->is_include_delivery_cost." is_fashion: ".$cp->is_fashion);
                }
            }
        }

        //foto and thumb
        $foto = '';
        $thumb = '';

        // define grand total lements
        $item_total = 0;
        $sub_total = 0;
        $ongkir_total = 0;
        $grand_total = 0;

        //start transaction
        $this->cart->trans_start();

        //calculation items for d_order
        foreach ($check_product as $cp) {
            $foto = $cp->foto;
            $thumb = $cp->thumb;
            $sub_total += ($cp->qty*$cp->harga_jual);
            $item_total++;
        }
        $grand_total = $sub_total + $ongkir_total;

        // generates counter for invoice code
        $counter = $this->order->countWaitingToday($nation_code);
        if ($counter<=0) {
            $counter = 1;
        }

        //prepare for inputing to d_order_detail and d_order_detail_produk
        $grand_total = 0;
        $sub_total = 0;
        $dod_data = array();
        $dodp_data = array();

        //get last pending order
        $last_id = $this->order->getLastId($nation_code);
        $di = array();
        $di['nation_code'] = $nation_code;
        $di['b_user_id'] = $pelanggan->id;
        $di['id'] = $last_id;
        $di['invoice_code'] = 'INV/'.$nation_code.'/'.date("ymd").'/'.sprintf("%04d", $counter);
        
        //by Donny Dennison - 15 december 2021 14:19
        //bug fix order stuck in waiting for payment if mobile dont call send notif api
        $di['is_countdown'] = 0;

        $di['cdate'] = 'NOW()';
        $di['item_total'] = $item_total;
        $di['payment_confirmed'] = '0';
        $di['payment_status'] = 'pending';
        $di['order_status'] = 'pending';
        $di['sub_total'] = $sub_total;
        $di['ongkir_total'] = $ongkir_total;
        $di['grand_total'] = $grand_total;
        $di['payment_method'] = "";
        $di['payment_tranid'] = "";
        $di['payment_response'] = "";
        $di['thumb'] = $thumb;
        $di['foto'] = $foto;
        $res = $this->order->set($di);
        if ($res) {
            $this->order->trans_commit();
            $this->status = 200;
            // $this->message = 'Order successfully created';
            $this->message = 'Success';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow --createOrder: DONE');
            }
        } else {
            $this->order->trans_rollback();
            $this->status = 800;
            $this->message = 'Failed create initial order, please try again';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow --createOrder: FAILED: '.$this->message);
            }
        }
        if ($this->status != 200) {
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow --forceCloseTransaction');
            }
            $this->order->trans_end();
            $this->__json_out(array());
            die();
        }

        //get latest
        $order = $this->order->getByIdUserid($nation_code, $pelanggan->id, $last_id);
        if (!isset($order->id)) {
            $this->status = 765;
            $this->message = "Cannot get order detail order after creating order";
            $this->order->trans_rollback();
            $this->order->trans_end();
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow --forceCloseTransaction');
            }
            $this->__json_out(array());
            die();
        }
        $d_order_id = $order->id;

        //define order object


        $order_detail = array();

        //check order existed
        if (isset($order->id)) {
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow --checkOrder: Exist --orderID: '.$order->id);
            }
            $sellers = array(); //seller array for view purpose
            $sub_total = 0;
            $grand_total = 0;

            //get products
            $this->i=0;
            $did=0; //detail id
            $details = array();
            foreach ($check_product as $pr1) {
                $pr1->id = (int) $pr1->id;
                //sanitize cs
                $pr1->courier_services = trim(strtolower($pr1->courier_services));
                //create key
                $key = $this->__keyProduct($nation_code, $order->id, $pr1);
                if (!isset($details[$key])) {
                    $did++;
                    $details = $this->__initDetails($nation_code, $details, $key, $order, $pr1, $prds, $did);
                } else {
                    $details[$key]->nama .= ', '.$prds[$pr1->id]->nama;
                }
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/cart::paynow -> Create product Key: $key");
                }

                //product item calculation
                $prv = $this->__initProduct($order, $pr1, $did);
                $details[$key]->produks[] = $prv;

                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/cart::paynow -> Product Item Added");
                }

                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/cart::paynow -> Product Merge: started");
                }
                //merged product calcluation
                //test weight asal
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/cart::paynow -> Asal Berat Total: ".$details[$key]->total_berat.", PID: ".$prv->c_produk_id);
                }

                $details[$key]->total_item++;
                $details[$key]->total_qty += $prv->qty;
                $details[$key]->total_berat += ($prv->berat*$prv->qty);
                $details[$key]->sub_total = ($prv->harga_jual*$prv->qty);
                $details[$key]->grand_total = ($prv->harga_jual*$prv->qty);

                //test weight after
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/cart::paynow -> Product Berat: ".$prv->berat.", Qty: ".$prv->qty.", Berat Total: ".$details[$key]->total_berat."KG");
                }

                //url manipulator
                $pr1->qty = $qty;
                $pr1 = $this->__castPrUrl($pr1);

                $sellers = $this->__initSellers($sellers, $order, $pr1);

                //dimension calculation
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/cart::paynow --dimensionCalc: Start");
                }
                $details = $this->__dimensionCalculation($details, $key, $prv);
                $this->i++;
            }

            //shipping constraints check and merging into gogovan
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> Shipment Constraint Check (SCC): START');
            }
            $produks = array();
            $is_recalculation = 0;
            if (count($details)>0) {
                foreach ($details as $detail) {
                    $k = $detail->total_panjang+$detail->total_lebar+$detail->total_tinggi;
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> keliling_total: '.$k.' and limit: '.$this->qx_side_limit);
                        $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> order_panjang_total: '.$detail->total_panjang);
                        $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> order_lebar_total: '.$detail->total_lebar);
                        $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> order_tinggi_total: '.$detail->total_tinggi);
                    }
                    foreach ($detail->produks as $det) {
                        $k = $det->total_panjang+$det->total_lebar+$det->total_tinggi;
                        if ($this->is_log) {
                            $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> produk_keliling_total: '.$k);
                            $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> produk_panjang_total: '.$det->total_panjang);
                            $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> produk_order_lebar_total: '.$det->total_lebar);
                            $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> produk_order_tinggi_total: '.$det->total_tinggi);
                        }
                        $detail->vehicle_types = $detail->shipment_vehicle;
                        $det->vehicle_types = $det->shipment_vehicle;
                        if ($this->is_log) {
                            $this->seme_log->write("api_mobile", "API_Mobile/cart::paynow -> SCC -- PID: ".$det->d_order_detail_id." - ".$det->shipment_service.' - '.$det->total_berat.'KG');
                        }
                        if (strtolower($det->shipment_service) == 'qxpress') {
                            $k = $det->total_panjang+$det->total_lebar+$det->total_tinggi;
                            $mr = min($det->total_panjang,$det->total_lebar,$det->total_tinggi);

                            //by Donny Dennison - 14 october 2020 17:07
                            //fix shipping after checkout
                            // if ($k>$this->qx_side_limit || $mr>$this->qx_limit) {
                            if (($det->qty == 1 && $k>$this->qx_side_limit) || $mr>$this->qx_limit) {

                                //by Donny Dennison - 15 september 2020 17:45
                                //change name, image, etc from gogovan to gogox
                                // $detail->shipment_service = 'gogovan';
                                // $det->shipment_service = 'gogovan';
                                $detail->shipment_service = 'gogox';
                                $det->shipment_service = 'gogox';

                                $is_recalculation=1;
                                if ($this->is_log) {
                                    $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> keliling_total: '.$k.' and limit: '.$this->qx_side_limit);
                                    $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> order_panjang_total: '.$detail->total_panjang.' and panjang_total: '.$det->total_panjang.' and limit: '.$this->qx_long_limit);
                                    $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> order_lebar_total: '.$detail->total_lebar.' and lebar_total: '.$det->total_lebar.' and limit: '.$this->qx_width_limit);
                                    $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> order_tinggi_total: '.$detail->total_tinggi.' and tinggi_total: '.$det->total_tinggi.' and limit: '.$this->qx_height_limit);
                                    $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> SCC to Gogovan by dimension limit');
                                }
                            } elseif ($det->total_berat>$this->qx_weight_limit && empty($det->is_fashion)) {
                                
                                //by Donny Dennison - 15 september 2020 17:45
                                //change name, image, etc from gogovan to gogox
                                // $detail->shipment_service = 'gogovan';
                                // $det->shipment_service = 'gogovan';
                                $detail->shipment_service = 'gogox';
                                $det->shipment_service = 'gogox';

                                $is_recalculation=1;
                                if ($this->is_log) {
                                    $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> SCC to Gogovan by weight');
                                }
                            }

                        //by Donny Dennison - 23 september 2020 15:42
                        //add direct delivery feature

                        //by Donny Dennison - 15 september 2020 17:45
                        //change name, image, etc from gogovan to gogox
                        // } elseif (strtolower($det->shipment_service) == 'gogovan') {

                        // } elseif (strtolower($det->shipment_service) == 'gogox') {
                        } elseif (strtolower($det->shipment_service) == 'gogox' || strtolower($det->shipment_service) == 'direct delivery') {

                        } else {
                            $this->cart->trans_rollback();
                            $this->cart->trans_end();
                            $this->status = 854;
                            $this->message = 'Undefined shipping method on System';
                            if ($this->is_log) {
                                $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> '.$this->message);
                            }
                            if ($this->is_log) {
                                $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow --forceClose '.$this->status.' '.$this->message);
                            }
                            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
                            die();
                        }
                        $produks[] = $det;
                    }
                }
            }
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> SCC: FINISH');
            }

            if (!empty($is_recalculation)) {
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> Re-Calculation Check');
                }
                $this->i=0;
                $did=0; //detail id
                $details = array();
                foreach ($produks as $pr2) {
                    //sanitize courier services
                    $pr2->shipment_service = trim(strtolower($pr2->shipment_service));

                    //by Donny Dennison - 23 september 2020 15:42
                    //add direct delivery feature
                    $pr2->courier_services = trim(strtolower($pr2->shipment_service));

                    //create key
                    $key = $this->__keyProduct($nation_code, $order->id, $pr2);
                    if (!isset($details[$key])) {
                        $did++;
                        $details = $this->__initDetails($nation_code, $details, $key, $order, $pr2, $prds, $did);
                    } else {
                        $details[$key]->nama .= ', '.$pr2->nama;
                    }
                    //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/cart::paynow -> Create Key: $key");

                    //product item calculation
                    $prv = $this->__initProduct($order, $pr2, $did);
                    $details[$key]->produks[] = $prv;

                    //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/cart::paynow -> Product Item Added");

                    //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/cart::paynow -> Product Merge: started");
                    //merged product calcluation

                    //test weight asal
                    //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/cart::paynow -> Asal Berat Total: ".$details[$key]->total_berat.", PID: ".$prv->c_produk_id);

                    $details[$key]->total_item++;
                    $details[$key]->total_qty += $prv->qty;
                    $details[$key]->total_berat += ($prv->berat*$prv->qty);
                    $details[$key]->sub_total = ($prv->harga_jual*$prv->qty);
                    $details[$key]->grand_total = ($prv->harga_jual*$prv->qty);
                    $details[$key]->shipment_vehicle = $prv->shipment_vehicle;

                    //test weight after
                    //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/cart::paynow -> Product Berat: ".$prv->berat.", Qty: ".$prv->qty.", Berat Total: ".$details[$key]->total_berat."KG");

                    //url manipulator
                    $pr2->qty = $qty;
                    $pr2 = $this->__castPrUrl($pr2);

                    $sellers = $this->__initSellers($sellers, $order, $pr2);

                    //dimension calculation
                    //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/cart::paynow --dimensionCalc: Start");
                    $details = $this->__dimensionCalculation($details, $key, $prv);
                    $this->i++;
                }
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -> Re-Calculation Check: DONE');
                }
            }

            if (count($details)>0) {
                $this->dodm->delByOrderId($nation_code, $order->id);
                $this->cart->trans_commit();

                $mass_details = array();
                $mass_items = array();
                foreach ($details as $mds) {

                    //by Donny Dennison - 23 september 2020 15:42
                    //add direct delivery feature

                    //by Donny Dennison - 15 september 2020 17:45
                    //change name, image, etc from gogovan to gogox
                    // if (strtolower($mds->shipment_service) != 'gogovan' && $mds->total_berat>$this->qx_weight_limit) {

                    // if (strtolower($mds->shipment_service) != 'gogox' && $mds->total_berat>$this->qx_weight_limit) {
                    if (strtolower($mds->shipment_service) != 'gogox' && strtolower($mds->shipment_service) != 'direct delivery' && $mds->total_berat>$this->qx_weight_limit) {


                        //by Donny Dennison - 15 september 2020 17:45
                        //change name, image, etc from gogovan to gogox
                        // $mds->shipment_service = 'Gogovan';
                        $mds->shipment_service = 'Gogox';

                    }

                    //by Donny Dennison - 23 september 2020 15:42
                    //add direct delivery feature

                    //by Donny Dennison - 15 september 2020 17:45
                    //change name, image, etc from gogovan to gogox
                    // if (strtolower($mds->shipment_service) != 'gogovan' && ($mds->total_panjang>$this->qx_long_limit || $mds->total_lebar>$this->qx_width_limit || $mds->total_tinggi>$this->qx_height_limit)) {

                    // if (strtolower($mds->shipment_service) != 'gogox' && ($mds->total_panjang>$this->qx_long_limit || $mds->total_lebar>$this->qx_width_limit || $mds->total_tinggi>$this->qx_height_limit)) {
                    if (strtolower($mds->shipment_service) != 'gogox' && strtolower($mds->shipment_service) != 'direct delivery' && ($mds->total_panjang>$this->qx_long_limit || $mds->total_lebar>$this->qx_width_limit || $mds->total_tinggi>$this->qx_height_limit)) {
                    
                        
                        // by Donny Dennison - 15 september 2020 17:45
                        // change name, image, etc from gogovan to gogox
                        // $mds->shipment_service = 'Gogovan';
                        $mds->shipment_service = 'Gogox';

                    }
                    $md = array();
                    $total_qty = 0;
                    $sub_total = 0;
                    foreach ($mds->produks as $prd) {
                        $mi = array(); //mass insert
                        foreach ($prd as $k2=>$v2) {
                            if (is_array($v2)) {
                                continue;
                            }
                            if (is_object($v2)) {
                                continue;
                            }
                            if ($k2 == 'b_user_id') {
                                continue;
                            }
                            if ($k2 == 'b_user_alamat_id') {
                                continue;
                            }
                            if ($k2 == 'dimension_long') {
                                continue;
                            }
                            if ($k2 == 'dimension_width') {
                                continue;
                            }
                            if ($k2 == 'dimension_height') {
                                continue;
                            }
                            if ($k2 == 'total_panjang') {
                                continue;
                            }
                            if ($k2 == 'total_lebar') {
                                continue;
                            }
                            if ($k2 == 'total_tinggi') {
                                continue;
                            }
                            if ($k2 == 'total_berat') {
                                continue;
                            }
                            if ($k2 == 'total_qty') {
                                continue;
                            }
                            if ($k2 == 'c_produk_nama') {
                                continue;
                            }
                            if ($k2 == 'vehicle_types') {
                                continue;
                            }
                            $mi[$k2] = $v2;
                        }
                        if (!isset($mi['shipment_service'])) {
                            
                            //by Donny Dennison - 15 september 2020 17:45
                            //change name, image, etc from gogovan to gogox
                            // $mi['shipment_service'] = 'Gogovan';
                            $mi['shipment_service'] = 'Gogox';

                        }
                        if (strtolower($mi['shipment_service']) != strtolower($mds->shipment_service)) {
                            $mi['shipment_service'] = $mds->shipment_service;
                        }
                        $mass_items[] = $mi;
                        $total_qty += $prd->qty;
                        $sub_total += ($prd->qty*$prd->harga_jual);
                    }

                    //end mass items
                    foreach ($mds as $k=>$v) {
                        if (is_array($v)) {
                            continue;
                        }
                        if (is_object($v)) {
                            continue;
                        }
                        $md[$k] = $v;
                    }
                    if (isset($md['is_include_delivery_cost'])) {
                        unset($md['is_include_delivery_cost']);
                    }
                    if (isset($md['is_fashion'])) {
                        unset($md['is_fashion']);
                    }
                    if (isset($md['vehicle_types'])) {
                        unset($md['vehicle_types']);
                    }
                    $md['total_qty'] = $total_qty;
                    $md['sub_total'] = $sub_total;
                    $md['grand_total'] = $md['sub_total'];
                    $md['total_item'] = count($mds->produks);
                    $md['date_duration'] = 0;
                    $md['shipment_type'] = '';
                    $md['shipment_vehicle'] = '';
                    $md['shipment_cost_sub'] = 0;
                    $md['shipment_cost_add'] = 0;
                    $md['shipment_response'] = '';
                    $md['shipment_distance'] = 0;
                    $md['pg_fee'] = 0;
                    $md['pg_vat'] = 0;
                    $md['cancel_fee'] = 0;
                    $md['shipment_tranid'] = "";
                    $mass_details[] = $md;
                }

                if (false) {
                    $this->cart->trans_rollback();
                    $this->cart->trans_end();
                    $this->debug($mass_details);
                    http_response_code(500);
                    die();
                }

                //starting input to database
                $res = $this->dodm->setMass($mass_details);
                $this->cart->trans_commit();
                $res = $this->dodim->setMass($mass_items);
                if ($res) {
                    $this->status = 200;
                    // $this->message = 'Order item successfully inserted';
                    $this->message = 'Success';
                    $this->order->trans_commit();
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -- '.$this->status.': '.$this->message);
                    }
                } else {
                    $this->status = 834;
                    $this->message = 'Failed moving seller to order';
                    $this->order->trans_rollback();
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow -- '.$this->status.': '.$this->message);
                    }
                }
            }
        } else {
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow --checkOrder: not Exist');
            }
        }
        if ($this->status == 200) {

            //START by Donny Dennison 23 november 2021 15:56
            //call api "checkout/2. Set Shipping Address" after finish run "cart/Pay (Proceed to checkout)"
            $b_user_alamat_id = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id)->id;

            $postData= array(
                'd_order_id' => $order->id,
                'b_user_alamat_id' => $b_user_alamat_id
            );

            $this->lib("seme_curl");
            $url = base_url("api_mobile/checkout/shipping/?apikey=$apikey&nation_code=$nation_code&apisess=$apisess");
            $this->seme_curl->post($url, $postData);
            //END by Donny Dennison 23 november 2021 15:56

            //START by Donny Dennison - 17 january 2022 14:01
            //make image in order product standalone
            $getAllOrderProduct = $this->dodim->getByOrderId($nation_code, $order->id);

            $oldOrderDetailID = 0;
            $oldOrderProductPhoto = '';
            $oldOrderProductPhotoThumb = '';
            $isProblemCopy = 0;
            foreach($getAllOrderProduct AS $orderProduct){
                
                if($oldOrderDetailID == 0){
                    $oldOrderDetailID = $orderProduct->d_order_detail_id;
                }else if($oldOrderDetailID != $orderProduct->d_order_detail_id){
                    
                    $du = array();
                    $du['foto'] = $oldOrderProductPhoto;
                    $du['thumb'] = $oldOrderProductPhotoThumb;
                    $this->dodm->update($nation_code, $order->id, $oldOrderDetailID, $du);
                    $this->order->trans_commit();

                    $oldOrderDetailID = $orderProduct->d_order_detail_id;

                }

                $copiedImage = $this->__copyImagex($nation_code, $orderProduct->foto, $order->id, $orderProduct->d_order_detail_id, $orderProduct->d_order_detail_item_id);

                if(isset($copiedImage->status)){
                    if($copiedImage->status == 200){

                        $du = array();
                        $du['foto'] = $copiedImage->image;
                        $du['thumb'] = $copiedImage->thumb;
                        $this->dodim->update($nation_code, $order->id, $orderProduct->d_order_detail_id, $orderProduct->d_order_detail_item_id, $du);
                        $this->order->trans_commit();

                        $oldOrderProductPhoto = $copiedImage->image;
                        $oldOrderProductPhotoThumb = $copiedImage->thumb;

                    }else{
                        $isProblemCopy = 1;
                    }
                }else{
                    $isProblemCopy = 1;
                }

            }

            if($isProblemCopy == 0){
                //update d_order table, column foto and thumb
                $du = array();
                $du['foto'] = $oldOrderProductPhoto;
                $du['thumb'] = $oldOrderProductPhotoThumb;
                $this->order->update($nation_code, $order->id, $du);
                $this->order->trans_commit();
            }
            //END by Donny Dennison - 17 january 2022 14:01

            $sellers = array_values($sellers);
            $data['order'] = $order;
            $data['order']->d_order_id = $order->id;
            $data['order']->sub_total = $sub_total;
            $data['order']->grand_total = $grand_total;
            $data['order']->addresses = $this->__orderAddresses($nation_code, $pelanggan, $order);
            $data['order']->sellers = $sellers;
        } else {
            $this->cart->trans_rollback();
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow --forceClosedTransaction with status '.$this->status);
            }
        }
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", 'API_Mobile/Cart::paynow COMPLETED');
        }

        $this->cart->trans_end();
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cart");
    }
}
