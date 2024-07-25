<?php
/**
 * Checkout from API
 * Last Error code: 3xxx
 */
class Checkout extends JI_Controller
{
    public $negara = 'SG';
    public $is_softfail = 0;
    public $is_log = 1;
    public $email_send = 1;

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->lib('seme_email');
        $this->load("api_mobile/a_negara_model", 'anm');
        $this->load("api_mobile/a_notification_model", 'anot');
        $this->load("api_mobile/b_user_model", 'bu');
        $this->load("api_mobile/b_user_alamat_model", 'bua');
        $this->load("api_mobile/b_user_setting_model", 'busm');
        $this->load("api_mobile/common_code_model", 'ccm');
        // $this->load("api_mobile/c_produk_model", 'cpm');
        $this->load("api_mobile/d_cart_model", 'cart');
        $this->load("api_mobile/d_order_model", 'order');
        $this->load("api_mobile/d_order_alamat_model", 'doam');
        $this->load("api_mobile/d_order_detail_model", 'dodm');
        $this->load("api_mobile/d_order_proses_model", 'dopm');
        $this->load("api_mobile/d_pemberitahuan_model", 'dpem');
        $this->load("api_mobile/d_order_detail_item_model", 'dodim');
        // $this->load("api_mobile/e_rating_model", 'erm');
    }
    /**
     * Get user default address
     * @param  int $nation_code nation code
     * @param  object $pelanggan   Object from table b_user
     * @return object              Object from table b_user_alamat
     */
    private function __getAddressDefault($nation_code, $pelanggan)
    {
        $address = $this->bua->getByUserDefaultFull($nation_code, $pelanggan->id);
        if (!isset($address->id)) {
        }
        return $address;
    }
    /**
     * Set or Update billing and Shipping Address
     * @param  int $nation_code nation code
     * @param  object $pelanggan   Object from table b_user
     * @param object $order            Object order From table d_order
     * @param int $b_user_alamat_id     id from b_user_alamat
     * @return object              New address Object from table b_user_alamat
     */
    private function __setOrderAddressBilling($nation_code, $pelanggan, $order, $b_user_alamat_id)
    {
        //default output
        $data = array();
        $data['order'] = new stdClass();

        //get by address
        $jenis_alamat = "Billing Address";
        $address_status = $this->ccm->getByClassifiedByCodeName($nation_code, "address", $jenis_alamat);
        if (!isset($address_status->code)) {
            $address_status = new stdClass();
            $address_status->code = 'A1';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::__setOrderAddressBilling -- INFO undefined address_status code, using default: '.$address_status->code);
            }
        }

        if (!isset($order->b_user_id)) {
            $this->status = 3001;
            $this->message = 'Invalid Order ID';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::__setOrderAddressBilling --forceClose: '.$this->status.' '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }
        if ($order->b_user_id != $pelanggan->id) {
            $this->status = 3005;
            $this->message = "This order doesn't belong to you";
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::__setOrderAddressBilling --forceClose: '.$this->status.' '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //get billing address
        $user_alamat = $this->bua->getByIdFull($nation_code, $pelanggan->id, $b_user_alamat_id);
        if (!isset($user_alamat->id)) {
            $this->status = 3006;
            $this->message = 'b_user_alamat_id not found or deleted';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::__setOrderAddressBilling --forceClose: '.$this->status.' '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }
        $order_id = $order->id;
        $pelanggan_id = $pelanggan->id;
        $order_address_billing = $this->doam->getByOrderIdBuyerIdStatusAddressFull($nation_code,$order_id, $pelanggan_id, $address_status->code);
        if (!isset($order_address_billing->d_order_id)) {
            //doing insert for billing
            $d_order_address_id = 0;
            $this->doam->trans_start();
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['d_order_id'] = $order->id;
            $di['b_user_id'] = $pelanggan->id;
            $di['b_user_alamat_id'] = $b_user_alamat_id;
            $di['address_status'] = $address_status->code;
            $di['judul'] = $user_alamat->judul;
            $di['nama'] = $user_alamat->penerima_nama;
            $di['telp'] = $user_alamat->penerima_telp;
            // by Muhammad Sofi - 3 November 2021 10:00
            // remark code
            // $di['alamat'] = $user_alamat->alamat;
            $di['alamat2'] = $user_alamat->alamat2;
            $di['kelurahan'] = $user_alamat->kelurahan;
            $di['kecamatan'] = $user_alamat->kecamatan;
            $di['kabkota'] = $user_alamat->kabkota;
            $di['provinsi'] = $user_alamat->provinsi;
            $di['negara'] = $this->negara;
            $di['kodepos'] = $user_alamat->kodepos;
            $di['latitude'] = $user_alamat->latitude;
            $di['longitude'] = $user_alamat->longitude;
            $di['address_notes'] = $user_alamat->catatan;
            $res = $this->doam->set($di);
            if ($res) {
                $this->doam->trans_commit();
                $this->status = 200;
                // $this->message = 'Billing address created successfully';
                $this->message = 'Success';
            } else {
                $this->doam->trans_rollback();
                $this->status = 3003;
                $this->message = 'Failed to create billing address';
            }
            $this->doam->trans_end();
        } else {
            $du = array();
            $du['b_user_alamat_id'] = $b_user_alamat_id;
            $du['judul'] = $user_alamat->judul;
            $du['nama'] = $user_alamat->penerima_nama;
            $di['telp'] = $user_alamat->penerima_telp;
            // by Muhammad Sofi - 3 November 2021 10:00
            // remark code
            // $du['alamat'] = $user_alamat->alamat;
            $du['alamat2'] = $user_alamat->alamat2;
            $du['kelurahan'] = $user_alamat->kelurahan;
            $du['kecamatan'] = $user_alamat->kecamatan;
            $du['kabkota'] = $user_alamat->kabkota;
            $du['provinsi'] = $user_alamat->provinsi;
            $du['negara'] = $this->negara;
            $du['kodepos'] = $user_alamat->kodepos;
            $du['latitude'] = $user_alamat->latitude;
            $du['longitude'] = $user_alamat->longitude;
            $du['address_notes'] = $user_alamat->catatan;
            $res = $this->doam->updateByAddressStatus($nation_code, $order->id, $pelanggan->id, $b_user_alamat_id, $address_status->code, $du);
            if ($res) {
                $this->status = 200;
                // $this->message = 'Billing address updated successfully';
                $this->message = 'Success';
            } else {
                $this->status = 3041;
                $this->message = 'Failed to update billing address';
            }
        }
        if ($this->status = 200) {
            return 1;
        }
        return 0;
    }

    /**
     * Set or Update Shipping Address
     * @param  int $nation_code nation code
     * @param  object $pelanggan   Object from table b_user
     * @param object $order            Object order From table d_order
     * @param int $b_user_alamat_id     id from b_user_alamat
     * @return object              New address Object from table b_user_alamat
     */
    private function __setOrderAddressShipping($nation_code, $pelanggan, $order, $b_user_alamat_id)
    {
        //default output
        $data = array();
        $data['order'] = new stdClass();

        //get by address
        //by Donny Dennison - 17 juni 2020 20:18
        // request by Mr Jackie change Shipping Address into Receiving Address
        // $jenis_alamat = "Shipping Address";
        $jenis_alamat = "Receiving Address";

        $address_status = $this->ccm->getByClassifiedByCodeName($nation_code, "address", $jenis_alamat);
        if (!isset($address_status->code)) {
            $address_status = new stdClass();
            $address_status->code = 'A2';
        }

        $user_alamat = $this->bua->getByIdFull($nation_code, $pelanggan->id, $b_user_alamat_id);
        if (!isset($user_alamat->b_user_id)) {
            $data = array();
            $this->status = 3006;
            $this->message = 'b_user_alamat_id not found or deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //get shipping address
        $order_address_shipping = $this->doam->getByOrderIdBuyerIdStatusAddressFull($nation_code,$order->id, $pelanggan->id, $address_status->code);
        if (!isset($order_address_shipping->d_order_id)) {
            //doing insert for shipping
            $d_order_address_id = 0;
            $this->doam->trans_start();
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['d_order_id'] = $order->id;
            $di['b_user_id'] = $pelanggan->id;
            $di['b_user_alamat_id'] = $b_user_alamat_id;
            $di['address_status'] = $address_status->code;
            $di['judul'] = $user_alamat->judul;
            $di['nama'] = $user_alamat->penerima_nama;
            $di['telp'] = $user_alamat->penerima_telp;
            // by Muhammad Sofi - 3 November 2021 10:00
            // remark code
            // $di['alamat'] = $user_alamat->alamat;
            $di['alamat2'] = $user_alamat->alamat2;
            $di['kelurahan'] = $user_alamat->kelurahan;
            $di['kecamatan'] = $user_alamat->kecamatan;
            $di['kabkota'] = $user_alamat->kabkota;
            $di['provinsi'] = $user_alamat->provinsi;
            $di['negara'] = $this->negara;
            $di['kodepos'] = $user_alamat->kodepos;
            $di['latitude'] = $user_alamat->latitude;
            $di['longitude'] = $user_alamat->longitude;
            $di['address_notes'] = $user_alamat->catatan;
            $res = $this->doam->set($di);
            if ($res) {
                $this->doam->trans_commit();
                $this->status = 200;

                //by Donny Dennison - 17 juni 2020 20:18
                // request by Mr Jackie change Shipping Address into Receiving Address
                // $this->message = 'Shipping address created successfully';
                // $this->message = 'Receiving address created successfully';
                $this->message = 'Success';
            } else {
                $this->doam->trans_rollback();
                $this->status = 3017;

                //by Donny Dennison - 17 juni 2020 20:18
                // request by Mr Jackie change Shipping Address into Receiving Address
                // $this->message = 'Failed to create shipping address';
                $this->message = 'Failed to create Receiving address';
            }
            $this->doam->trans_end();
        } else {
            $du = array();
            $du['b_user_alamat_id'] = $b_user_alamat_id;
            $du['judul'] = $user_alamat->judul;
            $du['nama'] = $user_alamat->penerima_nama;
            $di['telp'] = $user_alamat->penerima_telp;
            // by Muhammad Sofi - 3 November 2021 10:00
            // remark code
            // $du['alamat'] = $user_alamat->alamat;
            $du['alamat2'] = $user_alamat->alamat2;
            $du['kelurahan'] = $user_alamat->kelurahan;
            $du['kecamatan'] = $user_alamat->kecamatan;
            $du['kabkota'] = $user_alamat->kabkota;
            $du['provinsi'] = $user_alamat->provinsi;
            $du['negara'] = $this->negara;
            $du['kodepos'] = $user_alamat->kodepos;
            $du['latitude'] = $user_alamat->latitude;
            $du['longitude'] = $user_alamat->longitude;
            $du['address_notes'] = $user_alamat->catatan;
            $res = $this->doam->updateByAddressStatus($nation_code, $order->id, $pelanggan->id, $address_status->code, $du);
            if ($res) {
                $this->status = 200;

                //by Donny Dennison - 17 juni 2020 20:18
                // request by Mr Jackie change Shipping Address into Receiving Address
                // $this->message = 'Shipping address updated successfully';
                // $this->message = 'Receiving address updated successfully';
                $this->message = 'Success';
            } else {
                $this->status = 3047;

                //by Donny Dennison - 17 juni 2020 20:18
                // request by Mr Jackie change Shipping Address into Receiving Address
                // $this->message = 'Failed to update shipping address';
                $this->message = 'Failed to update Receiving address';
            }
        }
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Checkout::__setOrderAddressShipping -- RESULT: ".$this->message);
        }
        if ($this->status = 200) {
            return 1;
        }
        return 0;
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

        $addresses->billing = $this->doam->getByOrderIdBuyerIdStatusAddress($nation_code,$order->id, $pelanggan->id, $address_status->code);

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

        $addresses->shipping = $this->doam->getByOrderIdBuyerIdStatusAddress($nation_code,$order->id, $pelanggan->id, $address_status->code);
        return $addresses;
    }
    /**
     * Generate Seller order object
     * @param  [type] $nation_code [description]
     * @param  [type] $pelanggan   [description]
     * @param  [type] $order       [description]
     * @return object              Order details obect from d_order_detail and d_order_detail_item table
     */
    private function __orderSellers($nation_code, $pelanggan, $order)
    {
        $this->sub_total = 0.0;
        $this->ongkir_total = 0.0;
        $this->grand_total = 0.0;
        $detail_items = $this->dodim->getByOrderId($nation_code, $order->id);
        $ditems = array();
        foreach ($detail_items as $di) {
            $key  = $di->nation_code.'-';
            $key .= $di->d_order_id.'-';
            $key .= $di->d_order_detail_id;
            if (!isset($ditems[$key])) {
                $ditems[$key] = array();
            }
            $ditems[$key][] = $di;
        }

        $order_details = $this->dodm->getByOrderId($nation_code, $order->id);
        foreach ($order_details as &$dj) {
            $key  = $dj->nation_code.'-';
            $key .= $dj->d_order_id.'-';
            $key .= $dj->id;
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
            //     $dj->shipment_icon = $this->cdn_url("assets/images/gogovan.png");
            // if (strtolower($dj->shipment_service) == 'gogox') {
            }else if (strtolower($dj->shipment_service) == 'gogox') {
                $dj->shipment_icon = $this->cdn_url("assets/images/gogox.png");
            
            //END by Donny Dennison - 23 september 2020 15:42

            } elseif (strtolower($dj->shipment_service) == 'qxpress') {
                $dj->shipment_icon = $this->cdn_url("assets/images/qxpress.png");
            } else {
                $dj->shipment_icon = $this->cdn_url("assets/images/unavailable.png");
            }
            $dj->products = array();
            if (isset($ditems[$key])) {
                $dj->products = $ditems[$key];
            }
        }
        $order_details = array_values($order_details);
        return $order_details;
    }

    public function index()
    {
        //initial
        $dt = $this->__init();

        //default output
        $data = array();
        $data['order'] = new stdClass();
        $data['order']->addresses= new stdClass();
        $data['order']->sellers = array();

        //get by address

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
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
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //get order id
        $d_order_id = (int) $this->input->post('d_order_id');
        if ($d_order_id<=0) {
            $this->status = 3001;
            $this->message = 'Invalid Order ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //get order
        $order = $this->order->getById($nation_code, $d_order_id);
        if (!isset($order->id)) {
            $this->status = 3010;
            $this->message = 'Order not found or processed';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }
        if ($order->b_user_id != $pelanggan->id) {
            $this->status = 3005;
            $this->message = "This order doesn't belong to you";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //default result
        $this->status = 200;
        $this->message = 'Success';

        //building result
        $data = array();
        $data['order'] = $order;
        $data['order']->addresses = $this->__orderAddresses($nation_code, $pelanggan, $order);
        $data['order']->sellers = $this->__orderSellers($nation_code, $pelanggan, $order);

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
    }

    // Error code last: 3002
    public function billing()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order'] = new stdClass();
        $data['order']->addresses = new stdClass();
        $data['order']->sellers = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::billing --forceClose: '.$this->status.' '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::billing --forceClose: '.$this->status.' '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::billing --forceClose: '.$this->status.' '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //get order id
        $d_order_id = (int) $this->input->post('d_order_id');
        if ($d_order_id<=0) {
            $this->status = 3001;
            $this->message = 'Invalid Order ID';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::billing --forceClose: '.$this->status.' '.$this->message.' POST: '.json_encode($_POST));   
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //get order
        $order = $this->order->getPending($nation_code, $pelanggan->id, $d_order_id);
        if (!isset($order->b_user_id_buyer)) {
            $this->status = 3004;
            $this->message = 'Order was expired';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::billing --forceClose: '.$this->status.' '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //check buyer
        if($order->b_user_id_buyer != $pelanggan->id){
            $this->status = 3005;
            $this->message = "This order doesn't belong to you";
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::billing --forceClose: '.$this->status.' '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //jenis alamat from common_code
        $adst = new stdClass();
        $adst->code = 'A1';

        $b_user_alamat_id = (int) $this->input->post("b_user_alamat_id");
        if ($b_user_alamat_id<=0) {
            $data = array();
            $this->status = 3006;
            $this->message = 'b_user_alamat_id not found or deleted';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::billing --forceClose: '.$this->status.' '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        $user_alamat = $this->bua->getByIdFull($nation_code, $pelanggan->id, $b_user_alamat_id);
        if (!isset($user_alamat->id)) {
            $this->status = 3006;
            $this->message = 'b_user_alamat_id not found or deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Checkout::billing --UserID: $pelanggan->id --AddressID: $b_user_alamat_id");
        }

        //get selected address
        $user_alamat = $this->bua->getByIdFull($nation_code, $order->b_user_id_buyer, $b_user_alamat_id);
        if (!isset($user_alamat->id)) {
            $this->status = 3006;
            $this->message = 'b_user_alamat_id not found or deleted';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::billing --forceClose: '.$this->status.' '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //check
        $doam = $this->doam->check($nation_code,$order->id);
        if ($doam>0) {
          $this->doam->delByOrderId($nation_code, $order->id);
          $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::billing -- INFO doam successfully cleaned');
        }
        $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::billing -- INFO create new d_order_alamat data');

        $this->doam->trans_start();
        $di = array();
        $di['nation_code'] = $nation_code;
        $di['d_order_id'] = $order->id;
        $di['b_user_id'] = $order->b_user_id_buyer;
        $di['b_user_alamat_id'] = $b_user_alamat_id;
        $di['address_status'] = 'A1';
        $di['judul'] = $user_alamat->judul;
        $di['nama'] = $user_alamat->penerima_nama;
        $di['telp'] = $user_alamat->penerima_telp;
        // by Muhammad Sofi - 3 November 2021 10:00
        // remark code
        // $di['alamat'] = $user_alamat->alamat;
        $di['alamat2'] = $user_alamat->alamat2;
        $di['kelurahan'] = $user_alamat->kelurahan;
        $di['kecamatan'] = $user_alamat->kecamatan;
        $di['kabkota'] = $user_alamat->kabkota;
        $di['provinsi'] = $user_alamat->provinsi;
        $di['negara'] = $this->negara;
        $di['kodepos'] = $user_alamat->kodepos;
        $di['latitude'] = $user_alamat->latitude;
        $di['longitude'] = $user_alamat->longitude;
        $di['address_notes'] = $user_alamat->catatan;
        $res = $this->doam->set($di);
        if ($res) {
            $this->status = 200;
            // $this->message = 'Billing address created successfully';
            $this->message = 'Success';
            $this->doam->trans_commit();

            $di['address_status'] = 'A2';
            $res2 = $this->doam->set($di);
            if($res2){

                //by Donny Dennison - 17 juni 2020 20:18
                // request by Mr Jackie change Shipping Address into Receiving Address
              // $this->message = 'Billing and Shipping address created successfully';
              // $this->message = 'Billing and Receiving address created successfully';
              $this->doam->trans_commit();
            }else{
              $this->doam->trans_rollback();
            }
        } else {
            $this->status = 3003;
            $this->message = 'Failed to create billing address';
            $this->doam->trans_rollback();
        }
        $this->doam->trans_end();

        $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::billing -- RESULT '.$this->status.' '.$this->message);
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
    }

    //error code lst : 3008
    public function shipping()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order'] = new stdClass();
        $data['order']->addresses = new stdClass();
        $data['order']->sellers = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::shipping --forceClose: '.$this->status.' '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::shipping --forceClose: '.$this->status.' '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::shipping --forceClose: '.$this->status.' '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //get order id
        $d_order_id = (int) $this->input->post('d_order_id');
        if ($d_order_id<=0) {
            $this->status = 3001;
            $this->message = 'Invalid Order ID';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Checkout::shipping --forceClose $this->message");
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //get order
        $order = $this->order->getPending($nation_code, $pelanggan->id, $d_order_id);
        if (!isset($order->b_user_id_buyer)) {
            $this->status = 3010;
            $this->message = 'Order not found or processed';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Checkout::shipping --forceClose $this->message");
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //check buyer
        if($order->b_user_id_buyer != $pelanggan->id){
            $this->status = 3005;
            $this->message = "This order doesn't belong to you";
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::shipping --forceClose: '.$this->status.' '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //jenis alamat from common_code
        $adst = new stdClass();
        $adst->code = 'A1';

        $b_user_alamat_id = (int) $this->input->post("b_user_alamat_id");
        if ($b_user_alamat_id<=0) {
            $data = array();
            $this->status = 3006;
            $this->message = 'b_user_alamat_id not found or deleted';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Checkout::shipping --forceClose $this->message");
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Checkout::shipping --UserID: $pelanggan->id --AddressID: $b_user_alamat_id");
        }

        //get selected address
        $user_alamat = $this->bua->getByIdFull($nation_code, $order->b_user_id_buyer, $b_user_alamat_id);
        if (!isset($user_alamat->id)) {
            $this->status = 3006;
            $this->message = 'b_user_alamat_id not found or deleted';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::shipping --forceClose: '.$this->status.' '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //check
        $doam = $this->doam->check($nation_code,$order->id);
        if ($doam>0) {
          $this->doam->delByOrderId($nation_code, $order->id);
          $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::shipping -- INFO doam successfully cleaned');
        }

        $this->doam->trans_start();
        $di = array();
        $di['nation_code'] = $nation_code;
        $di['d_order_id'] = $order->id;
        $di['b_user_id'] = $order->b_user_id_buyer;
        $di['b_user_alamat_id'] = $b_user_alamat_id;
        $di['address_status'] = 'A1';
        $di['judul'] = $user_alamat->judul;
        $di['nama'] = $user_alamat->penerima_nama;
        $di['telp'] = $user_alamat->penerima_telp;
        // by Muhammad Sofi - 3 November 2021 10:00
        // remark code
        // $di['alamat'] = $user_alamat->alamat;
        $di['alamat2'] = $user_alamat->alamat2;
        $di['kelurahan'] = $user_alamat->kelurahan;
        $di['kecamatan'] = $user_alamat->kecamatan;
        $di['kabkota'] = $user_alamat->kabkota;
        $di['provinsi'] = $user_alamat->provinsi;
        $di['negara'] = $this->negara;
        $di['kodepos'] = $user_alamat->kodepos;
        $di['latitude'] = $user_alamat->latitude;
        $di['longitude'] = $user_alamat->longitude;
        $di['address_notes'] = $user_alamat->catatan;
        $res = $this->doam->set($di);
        if ($res) {
            $this->status = 200;
            // $this->message = 'Billing address created successfully';
            $this->message = 'Success';
            $this->doam->trans_commit();

            $di['address_status'] = 'A2';
            $res2 = $this->doam->set($di);
            if($res2){

                //by Donny Dennison - 17 juni 2020 20:18
                // request by Mr Jackie change Shipping Address into Receiving Address
              // $this->message = 'Billing and Shipping address created successfully';
              // $this->message = 'Billing and Receiving address created successfully';
              $this->doam->trans_commit();
            }else{
              $this->doam->trans_rollback();
            }
        } else {
            $this->status = 3003;
            $this->message = 'Failed to create billing address';
            $this->doam->trans_rollback();
        }
        $this->doam->trans_end();

        $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::shipping -- RESULT '.$this->status.' '.$this->message);
        //render output
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
    }

    //last error code: 3014
    public function format()
    {
        $data = array();
        $data['products'] = array();
        $ps = new stdClass();
        $ps->id = 1;
        $ps->qty = 1;
        $ps->shipment_type = "Same Day";
        
        //by Donny Dennison - 15 september 2020 17:45
        //change name, image, etc from gogovan to gogox
        // $ps->shipment_service = "Gogovan";
        $ps->shipment_service = "Gogox";

        $ps->shipment_cost = "18.00";
        $ps->shipment_cost_add = "0.00";
        $data['products'][] = $ps;
        $ps = new stdClass();
        $ps->id = 2;
        $ps->qty = 1;
        $ps->shipment_type = "Next Day";
        $ps->shipment_service = "QXpress";
        $ps->shipment_cost = "9.00";
        $ps->shipment_cost_add = "0.00";
        $data['products'][] = $ps;
        header("content-type: application/json");
        echo json_encode($data);
    }

    //last error code: 3014
    public function shipment()
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
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
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
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //get order id
        $d_order_id = (int) $this->input->post('d_order_id');
        if ($d_order_id<=0) {
            $this->status = 3001;
            $this->message = 'Invalid Order ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //get order
        $order = $this->order->getPending($nation_code, $pelanggan->id, $d_order_id);
        if (!isset($order->b_user_id_buyer)) {
            $this->status = 3010;
            $this->message = 'Order not found or processed';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //check buyer
        if($order->b_user_id_buyer != $pelanggan->id){
            $this->status = 3005;
            $this->message = "This order doesn't belong to you";
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::shipping --forceClose: '.$this->status.' '.$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }
        
        $order_address_billing = $this->doam->getByOrderIdBuyerIdStatusAddressFull($nation_code,$order->id, $pelanggan->id, $address_status->code);

        //by Donny Dennison - 17 juni 2020 20:18
        // request by Mr Jackie change Shipping Address into Receiving Address
        // $jenis_alamat = "Shipping Address";
        $jenis_alamat = "Receiving Address";

        $address_status = $this->ccm->getByClassifiedByCodeName($nation_code, "address", $jenis_alamat);
        if (!isset($address_status->code)) {
            $address_status = new stdClass();
            $address_status->code = 'A1';
        }
        $address_shipping = $this->doam->getByOrderIdBuyerIdStatusAddress($nation_code,$order->id, $pelanggan->id, $address_status->code);
        if (!isset($address_shipping->d_order_id)) {
            $this->status = 3018;

            //by Donny Dennison - 17 juni 2020 20:18
            // request by Mr Jackie change Shipping Address into Receiving Address
            // $this->message = 'Please set shipping address first';
            $this->message = 'Please set Receiving address first';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }
    }

    //last error code: 3018
    public function paynow()
    {
        //initial
        $item_total = 0;
        $dt = $this->__init();
        $data = array();
        $data['order'] = new stdClass();
        $data['order']->addresses = new stdClass();
        $data['order']->sellers = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
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
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Checkout::paynow --begin");
        }

        //get order id
        $d_order_id = (int) $this->input->post('d_order_id');
        if ($d_order_id<=0) {
            $this->status = 3001;
            $this->message = 'Invalid Order ID';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Checkout::paynow --forceClose: ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //get order
        $order = $this->order->getPending($nation_code, $pelanggan->id, $d_order_id);
        if (!isset($order->b_user_id_buyer)) {
            $this->status = 3020;
            $this->message = 'Sorry, product is not available at this time';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Checkout::paynow --forceClose: ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //check address
        $addresses = $this->__orderAddresses($nation_code, $pelanggan, $order);
        $data = array();
        $data['order'] = $order;
        $data['order']->sellers = array();
        $data['order']->addresses = $addresses;

        if (!isset($data['order']->addresses->billing->d_order_id)) {
            $this->status = 3021;
            $this->message = 'Please specify billing address first';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Checkout::paynow --forceClose: ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }
        if (!isset($data['order']->addresses->shipping->d_order_id)) {
            $this->status = 3022;

            //by Donny Dennison - 17 juni 2020 20:18
            // request by Mr Jackie change Shipping Address into Receiving Address
            // $this->message = 'Please specify shipping address first';
            $this->message = 'Please specify Receiving address first';

            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Checkout::paynow --forceClose: ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //get ordered products
        $dodm = $this->dodm->getForCheckout($nation_code, $d_order_id);
        if (count($dodm) == 0) {
            $this->status = 3098;
            $this->message = "Order doesn't include products, cancelled";
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Checkout::paynow --forceClose: ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //traverse products
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Checkout::paynow -> inspect product shipping cost");
        }

        //initial vars
        $order_item_total = 0;
        $order_sub_total = 0.0;
        $order_ongkir_total = 0.0;
        $details = array();
        foreach ($dodm as $dod) {
            $key = $dod->id;
            $details[$key] = $dod;
            $details[$key]->d_order_id = "";
            $details[$key]->d_order_detail_id = "";
            $details[$key]->nama = "";
            $details[$key]->thumb = "";
            $details[$key]->foto = "";
            $details[$key]->sub_total = 0;
            $details[$key]->total_qty = 0;
            $details[$key]->total_item = 0;
            $details[$key]->grand_total = 0;
            $details[$key]->shipment_cost = $dod->shipment_cost;
            $details[$key]->shipment_cost_add = $dod->shipment_cost_add;
            $details[$key]->shipment_cost_sub = $dod->shipment_cost_sub;
            $order_ongkir_total += ($dod->shipment_cost + $dod->shipment_cost_add) - $dod->shipment_cost_sub;

            if($dod->b_user_is_active == 0){
                $this->status = 3020;
                $this->message = 'Sorry, product is not available at this time';

                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Checkout::paynow --forceClose: ".$this->message);
                }
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
                die();
            }
        }
        unset($ops); //free some memory
        unset($op); //free some memory

        //load the item and re-calculate
        $nama = '';
        $ordered_products = array();
        $dodim = $this->dodim->getByOrderId($nation_code, $d_order_id);
        foreach ($dodim as $dodi) {
            $ordered_products[] = $dodi;
            $key = $dodi->d_order_detail_id;
            if (isset($details[$key])) {
                $details[$key]->d_order_id = $dodi->d_order_id;
                $details[$key]->d_order_detail_id = $dodi->d_order_detail_id;
                $details[$key]->nama .= $dodi->nama.",";
                $details[$key]->foto = $dodi->foto;
                $details[$key]->thumb = $dodi->thumb;
                $details[$key]->total_item++;

                //by Donny Dennison - 24 february 2021 11:37
                //fix total qty seller order
                // $details[$key]->total_qty = $dodi->qty;
                $details[$key]->total_qty += $dodi->qty;

                $details[$key]->sub_total += ($dodi->harga_jual*$dodi->qty);
                $order_sub_total += ($dodi->harga_jual*$dodi->qty);
            }
            $order_item_total++;
        }
        foreach ($details as $det) {
            $du = array();
            $du['nama'] = rtrim($det->nama, ',');
            $du['foto'] = $det->foto;
            $du['thumb'] = $det->thumb;
            $du['total_item'] = $det->total_item;
            $du['total_qty'] = $det->total_qty;
            $du['sub_total'] = $det->sub_total;
            $du['grand_total'] = $det->sub_total + ($det->shipment_cost + $det->shipment_cost_add) - $det->shipment_cost_sub;
            $this->dodm->update($nation_code, $order->id, $det->d_order_detail_id, $du);
        }

        //end of product inspection
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Checkout::paynow -> Shipping cost on each product: VALIDATED");
        }

        //start transaction before update
        $this->order->trans_start();
        //final check
        $sellers = $this->__orderSellers($nation_code, $pelanggan, $order);
        if (count($sellers)>0) {
            $duo = array();
            $duo['cdate'] = "NOW()";
            $duo['sub_total'] = $order_sub_total;
            $duo['ongkir_total'] = $order_ongkir_total;
            $duo['item_total'] = $order_item_total;
            $duo['grand_total'] = $order_sub_total+$order_ongkir_total;
            $duo['payment_amount'] = $order_sub_total;
            if ($order_ongkir_total>0) {
                $duo['payment_amount'] = $order_sub_total+$order_ongkir_total;
            }
            $duo['order_status'] = "waiting_for_payment";
            $res = $this->order->updateByUserAndOrder($nation_code, $pelanggan->id, $order->id, $duo);
            if ($res) {
                $this->status = 200;
                $this->message = "Success";
                $this->order->trans_commit();

                //for view pupose
                $data['order']->item_total = intval($order_item_total);
                $data['order']->sub_total = intval($order_sub_total);
                $data['order']->ongkir_total = intval($order_ongkir_total);
                $data['order']->grand_total = intval($order_sub_total+$order_ongkir_total);
                $data['order']->payment_amount = intval($order_sub_total);
                if ($order_ongkir_total>0) {
                    $data['order']->payment_amount = intval($order_sub_total+$order_ongkir_total);
                }
                $data['order']->order_status = 'waiting_for_payment';
                $data['order']->sellers = $sellers;

                //get buyer data
                $buyer = $pelanggan;

                //data seller
                $penjuals = array();
                //order process history
                // $notif_product_name = '';
                foreach ($ordered_products as $op) {
                    // $notif_product_name .= "$op->c_produk_nama, ";
                    $ops = array();
                    $ops['nation_code'] = $nation_code;
                    $ops['d_order_id'] = $order->id;
                    $ops['c_produk_id'] = $op->id;
                    $ops['id'] = $this->dopm->getLastId($nation_code, $order->id, $op->id);
                    $ops['initiator'] = "Buyer";
                    $ops['nama'] = "Waiting for Payment";
                    $ops['deskripsi'] = "Your order has been successfully created with invoice number: $order->invoice_code.";
                    $ops['cdate'] = "NOW()";
                    $this->dopm->set($ops);
                    $this->order->trans_commit();

                    //by Donny Dennison - 10 october 2020
                    //send notif 1 minute before payment expired and disable first notif payment
                    //START by Donny Dennison - 10 october 2020

                    // //get notification config for buyer
                    // $setting_value = 0;
                    // $classified = 'setting_notification_buyer';
                    // $notif_code = 'B1';
                    // $notif_cfg = $this->busm->getValue($nation_code, $buyer->id, $classified, $notif_code);
                    // if (isset($notif_cfg->setting_value)) {
                    //     $setting_value = (int) $notif_cfg->setting_value;
                    // }
                    // if ($this->is_log) {
                    //     $this->seme_log->write("api_mobile", "API_Mobile/Checkout::paynow --pushnotifconfig, F: B, UID: $buyer->id, Classified: $classified, Code: $notif_code, value: $setting_value");
                    // }

                    // //notification list for buyer
                    // $dpe = array();
                    // $dpe['nation_code'] = $nation_code;
                    // $dpe['b_user_id'] = $buyer->id;
                    // $dpe['id'] = $this->dpem->getLastId($nation_code, $buyer->id);
                    // $dpe['type'] = "transaction";
                    // $dpe['judul'] = "Waiting for Payment";

                    //  //by Donny Dennison 16 august 2020 16:21
                    // //change payment timeout from 10 minute to 3 minute
                    // // $dpe['teks'] = "Please finish your payment for this product $op->c_produk_nama ($order->invoice_code) immediately. If you don't complete your payment within the next 10 minutes, you must repeat your order.";
                    // $dpe['teks'] = "Please finish your payment for this product $op->c_produk_nama ($order->invoice_code) immediately. If you don't complete your payment within the next 3 minutes, you must repeat your order.";

                    // $dpe['cdate'] = "NOW()";
                    // $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
                    // $extras = new stdClass();
                    // $extras->id_order = "".$order->id;
                    // $extras->id_produk = null;
                    // $extras->id_order_detail = null;
                    // $extras->b_user_id_buyer = $buyer->id;
                    // $extras->b_user_fnama_buyer = $buyer->fnama;
                    // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                    // $extras->b_user_id_seller = $op->b_user_id_seller;
                    // $extras->b_user_fnama_seller = $op->b_user_fnama_seller;
                    // $extras->b_user_image_seller = $this->cdn_url($op->b_user_image_seller);
                    // $extras->status = "waiting_payment";
                    // $dpe['extras'] = json_encode($extras);
                    // $dpe['is_read'] = 0;
                    // $this->dpem->set($dpe);
                    // $this->order->trans_commit();

                    //END by Donny Dennison - 10 october 2020
                
                }
            } else {
                $this->status = 3179;
                $this->message = "Failed updating order_status";
                $this->order->trans_rollback();
            }
        } else {
            $this->status = 3037;
            $this->message = "Cart still empty, please add one product and proceed to paynow";
            $this->order->trans_rollback();
        }
        $this->order->trans_end();

        if ($this->is_log) {
            $this->seme_log->write("api_mobile", 'API_Mobile/Checkout::paynow -- finished '.$this->status.' '.$this->message);
        }

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
    }
    public function cancel($id)
    {
        //initial
        $item_total = 0;
        $dt = $this->__init();
        $data = array();
        $data['order'] = new stdClass();
        $data['order']->addresses = new stdClass();
        $data['order']->sellers = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
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
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Checkout::cancel --begin");
        }

        //get order id
        $d_order_id = (int) $this->input->post('d_order_id');
        if ($d_order_id<=0) {
            $this->status = 3001;
            $this->message = 'Invalid Order ID';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Checkout::cancel --forceClose: ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        //get order
        $order = $this->order->getById($nation_code, $pelanggan->id, $d_order_id);
        if (!isset($order->id)) {
            $this->status = 3020;
            $this->message = 'Sorry, product is not available at this time';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Checkout::cancel --forceClose: ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
            die();
        }

        $products = $this->dodm->getByOrderId($nation_code, $order->id);
        if (count($products)) {
            foreach ($products as $p) {
                //rollback stock
            }
        }

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "checkout");
        die();
    }
}
