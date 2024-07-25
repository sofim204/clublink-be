<?php
/**
 * Generate waybill for shipping purpose
 */
class WayBill extends JI_Controller
{
    public $is_log = 1;
    public $email_send = 1;

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->lib("seme_email");
        $this->load("api_mobile/a_pengguna_model", "apm");
        $this->load("api_mobile/a_notification_model", "anot");
        $this->load("api_mobile/b_user_model", "bu");
        $this->load("api_mobile/b_user_alamat_model", "bua");
        $this->load("api_mobile/b_user_setting_model", "busm");
        $this->load("api_mobile/b_kategori_model3", "bkm3");
        $this->load("api_mobile/common_code_model", "ccm");
        $this->load("api_mobile/c_produk_model", "cpm");
        $this->load("api_mobile/c_produk_foto_model", "cpfm");
        $this->load("api_mobile/d_wishlist_model", "dwlm");
        $this->load("api_mobile/d_order_model", "order");
        $this->load("api_mobile/d_order_alamat_model", "doam");
        $this->load("api_mobile/d_order_detail_model", "dodm");
        $this->load("api_mobile/d_order_detail_pickup_model", "dodpum");
        $this->load("api_mobile/d_order_proses_model", "dopm");
        $this->load("api_mobile/d_pemberitahuan_model", "dpem");
        $this->load("api_mobile/d_order_detail_item_model", "dodim");
    }
    private function __randMask($str)
    {
        $l = strlen($str);
        $m = ceil($l/4);
        return substr($str, 0, $m).str_repeat("*", $l-$m);
    }
    /**
     * Get billing and shipping address object from d_order_alamat
     * @param  string $nation_code nation_code
     * @param  object $pelanggan   Buyer object from b_user
     * @param  int $d_order_id  Order ID
     * @return object              billing and shipping address
     */

    //by Donny Dennison - 10 july 2020 10:31
    //move send api delivery to controller/api_mobile/order/delivery_process
    // private function __orderAddresses($nation_code, $pelanggan, $d_order_id)
    public function __orderAddresses($nation_code, $pelanggan, $d_order_id)
    {
        //addresses init
        $addresses = new stdClass();
        $addresses->billing = new stdClass();
        $addresses->shipping = new stdClass();

        //get billing address
        $jenis_alamat = 'Billing Address';
        $address_status = $this->ccm->getByClassifiedByCodeName($nation_code, "address", $jenis_alamat);
        if(!isset($address_status->code)){
          $address_status = new stdClass();
          $address_status->code = 'A1';
        }
        $addresses->billing = $this->doam->getByOrderIdBuyerIdStatusAddress($nation_code, $d_order_id, $pelanggan->id, $address_status->code);

        //get shipping address
        //by Donny Dennison - 17 juni 2020 20:18
        // request by Mr Jackie change Shipping Address into Receiving Address
        // $jenis_alamat = 'Shipping Address';
        $jenis_alamat = 'Receiving Address';
        $address_status = $this->ccm->getByClassifiedByCodeName($nation_code, "address", $jenis_alamat);
        if(!isset($address_status->code)){
          $address_status = new stdClass();
          $address_status->code = 'A2';
        }
        $addresses->shipping = $this->doam->getByOrderIdBuyerIdStatusAddress($nation_code, $d_order_id, $pelanggan->id, $address_status->code);
        return $addresses;
    }
    /**
     * Force download file, sent file header to webserver
     * @param  string $pathFile realpath files
     * @return binary           binary files
     */
    private function __forceDownload($pathFile)
    {
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($pathFile));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . fileSize($pathFile));
        ob_clean();
        flush();
        readfile($pathFile);
        exit;
    }

    /**
     * Call QXpress API Service for create delivery order
     *   If that already called, so the $first not equal to 1
     * @param  object  $address address object from table d_order_alamat
     * @param  object  $order   order object from table d_order, d_order_detail
     * @param  object  $buyer   object from b_user
     * @param  integer $first   if its first request, so the $refOrderno will be same as invoice_code
     * @return string           xml format
     */

    //by Donny Dennison - 10 july 2020 10:31
    //move send api delivery to controller/api_mobile/order/delivery_process
    // private function __createQXpress($address, $order, $buyer, $first=1)
    public function __createQXpress($address, $order, $buyer, $first=1)
    {
        //$refOrderNo = $order->invoice_code.'/'.$order->c_produk_id;
        
        //by Donny Dennison - 11 july 2020 0:23
        //change reference number to be more unique
        // $refOrderNo = ''.$order->nation_code.''.str_pad($order->d_order_id, 7, '0', STR_PAD_LEFT).''.str_pad($order->d_order_detail_id, 2, '0', STR_PAD_LEFT);
        $refOrderNo = ''.$order->nation_code.''.date('ymdHis');

        if (empty($first)) {
            $refOrderNo = '';
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

        //By Donny Dennison, change url api qxpress to the new one
        // curl_setopt($ch, CURLOPT_URL, $this->qx_api_host.'shipment/CreateOrderSG.php');
        curl_setopt($ch, CURLOPT_URL, $this->qx_api_host.'GMKT.INC.GLPS.OpenApiService/Giosis.qapi?key=&v=1.0&returnType=xml&method=ShipmentOuterService.CreateOrder');
        $headers = array();
        $headers[] = 'Content-Type: Text/xml';
        $headers[] = 'Accept: Text/xml';
        $postdata = array(
          'apiKey' => $this->qx_api_key,
          'accountId' => $this->qx_account_id,

          //By Donny Dennison - 27 June 2020 5:31
          //add field that required by qxpress
          'senderCountryCode' => "SG",

          'refOrderNo' => $refOrderNo,
          'svcType' => 'RM',
          'rcptName' => $address->nama,
          'rcptEmail' => $buyer->email,
          //By Donny Dennison - 27 June 2020 5:31
          //add field that required by qxpress
          'rcptCountryCode' => 'SG',

          'rcptCountry' => 'SG',

          //By Donny Dennison - 27 June 2020 5:31
          //Request by Mr Jackie, not using alamat
          // 'rcptAddr1' => $address->alamat,
          'rcptAddr1' => $address->alamat2,

          //By Donny Dennison - 30 June 2020 16:39
          //Request by Mr Jackie, change to address notes
          // 'rcptAddr2' => $address->alamat2,
          'rcptAddr2' => $address->address_notes,

          'rcptZipcode' => $address->kodepos,
          'rcptPhone' => $address->telp,
          'rcptMobile' => $buyer->telp,
          'rcptMemo' => 'Please confirm the following '.$order->qty.' item(s)',
          'contents' => html_entity_decode($order->nama,ENT_QUOTES),
          'quantity' => $order->qty,
          //by Donny Dennison - 10 july 2020 23:49
          //change the value
          // 'value' => ($order->qty*$order->harga_jual),
          'value' => $order->harga_jual,
          
          'currency' => 'SGD',
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return 0;
            //echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        if($this->is_log){
          $this->seme_log->write("api_mobile", 'API_Mobile/WayBill::__createQXpress:: -- cUrlHeader: '.json_encode($headers));
          $this->seme_log->write("api_mobile", 'API_Mobile/WayBill::__createQXpress:: -- cUrlPOST: '.json_encode($postdata));
        }
        return $result;
    }

    /**
     * By Donny Dennison, 30 june 2020 15:43
     * Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
     *   
     * @param  object  $tracking_number from api qxpress after create order
     * @return string           xml format
     */

    //by Donny Dennison - 10 july 2020 10:31
    //move send api delivery to controller/api_mobile/order/delivery_process
    // private function __getQXpressTracking($tracking_number)
    public function __getQXpressTracking($tracking_number)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        // curl_setopt($ch, CURLOPT_URL, $this->qx_api_host.'shipment/Tracking.php');
        curl_setopt($ch, CURLOPT_URL, $this->qx_api_host.'GMKT.INC.GLPS.OpenApiService/Giosis.qapi?key=&v=1.0&returnType=xml&method=ShipmentOuterService.Tracking');
        $headers = array();
        $headers[] = 'Content-Type: Text/xml';
        $headers[] = 'Accept: Text/xml';
        $postdata = array(
          'apiKey' => $this->qx_api_key,
          'accountId' => $this->qx_account_id,
          'trackingNo' => "".$tracking_number.""
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return 0;
            //echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        if($this->is_log){
          $this->seme_log->write("api_mobile", 'API_Mobile/WayBill::__getQXpressTracking:: -- cUrlHeader: '.json_encode($headers));
          $this->seme_log->write("api_mobile", 'API_Mobile/WayBill::__getQXpressTracking:: -- cUrlPOST: '.json_encode($postdata));
        }
        return $result;
    }

    /**
     * Call QXpress API Service for create pickup order
     * @param  [type] $address [description]
     * @param  [type] $order   [description]
     * @param  [type] $seller  [description]
     * @return string          in xml format
     */
    private function __createQXpressPickup($address, $order, $seller)
    {
        //if (strlen($order->shipment_tranid)>4) {
        //  $refOrderNo = $order->shipment_tranid;
        //} else {
        
        //by Donny Dennison - 11 july 2020 0:23
        //change reference number to be more unique
        // $refOrderNo = ''.$order->nation_code.''.str_pad($order->d_order_id, 7, '0', STR_PAD_LEFT).''.str_pad($order->d_order_detail_id, 2, '0', STR_PAD_LEFT);
        $refOrderNo = ''.$order->nation_code.''.date('ymdHis');
        
        //}
        $pickupDate = date("Y-m-d", strtotime("+25 hour"));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

        //By Donny Dennison, change url api qxpress to the new one
        // curl_setopt($ch, CURLOPT_URL, $this->qx_api_host.'shipment/CreatePickupOrder.php');
        curl_setopt($ch, CURLOPT_URL, $this->qx_api_host.'GMKT.INC.GLPS.OpenApiService/Giosis.qapi?key=&v=1.0&returnType=xml&method=PickupOuterService.CreatePickupOrder');

        $headers = array();
        $headers[] = 'Content-Type: Text/xml';
        $headers[] = 'Accept: Text/xml';
        $postdata = array(
          'apiKey' => $this->qx_api_key,
          'accountId' => $this->qx_account_id,
          'pickupDate' => $pickupDate,
          'countryCode' => 'SG',
          'zipcode' => $address->kodepos,
          
          //By Donny Dennison - 27 June 2020 5:31
          //Request by Mr Jackie, not using alamat
          // 'addr1' => $address->alamat,
          'addr1' => $address->alamat2,

          //By Donny Dennison - 30 June 2020 16:39
          //Request by Mr Jackie, change to address notes
          // 'addr2' => $address->alamat2,
          'addr2' => $address->address_notes,

          'mobileNo' => $seller->telp,
          'telNo' => $seller->telp,
          'quantity' => $order->qty,
          'requestMemo' => 'Please confirm the following '.$order->qty.' item(s)',
          'vehicleType' => 'VAN',
          'pickupNo' => $refOrderNo
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return 0;
            //echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        if($this->is_log){
          $this->seme_log->write("api_mobile", 'API_Mobile/WayBill::__createQXpressPickup:: -- cUrlHeader: '.json_encode($headers));
          $this->seme_log->write("api_mobile", 'API_Mobile/WayBill::__createQXpressPickup:: -- cUrlPOST: '.json_encode($postdata));
        }
        return $result;
    }

    /**
     * Get QXpress area code
     * @param  string $kodepos kodepos / zipcode
     * @return string          in xml format
     */

    //by Donny Dennison - 10 july 2020 10:31
    //move send api delivery to controller/api_mobile/order/delivery_process
    // private function __getQXpressArea($kodepos)
    public function __getQXpressArea($kodepos)
    {
        $pickupDate = date("Y-m-d", strtotime("+25 hour"));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

        //By Donny Dennison, change url api qxpress to the new one
        // curl_setopt($ch, CURLOPT_URL, $this->qx_api_host.'shipment/GetAreaCode.php');
        curl_setopt($ch, CURLOPT_URL, $this->qx_api_host.'GMKT.INC.GLPS.OpenApiService/Giosis.qapi?key=&v=1.0&returnType=xml&method=ShipmentOuterService.GetAreaCode');

        $headers = array();
        $headers[] = 'Content-Type: Text/xml';
        $headers[] = 'Accept: Text/xml';
        $postdata = array(
          'apiKey' => $this->qx_api_key,
          'partnerId' => $this->qx_account_id,
          'accountId' => $this->qx_account_id,
          'pickupDate' => $pickupDate,
          'rcptCountryCode' => 'SG',
          'zipCode' => $kodepos
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return 0;
            //echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        if($this->is_log){
          $this->seme_log->write("api_mobile", 'API_Mobile/WayBill::__getQXpressArea:: -- cUrlHeader: '.json_encode($headers));
          $this->seme_log->write("api_mobile", 'API_Mobile/WayBill::__getQXpressArea:: -- cUrlPOST: '.json_encode($postdata));
        }
        return $result;
    }

    /**
     * Create Send Order for Gogovan by calling Gogovan API
     * @param  object $order            from table d_order, d_order_detail
     * @param  object $address_pickup   from table d_order_detail_pickup
     * @param  object $address_shipping from table d_order_alamat
     * @return string                   result in json format
     */

    //by Donny Dennison - 8 September 2020 15:09
    //change api gogovan to new version (gogovan change name to gogox)
    /**
    * Get gogovan access token
    *
    * @param object 
    * @param object 
    * @param string 
    * @return string
    */
    public function __getGogovanAccessToken()
    {

        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $this->gv_api_host_new.'oauth/token');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $headers = array();

        $headers[] = 'accept: application/json';
        $headers[] = 'content-type: application/json';

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $data = array(
            'grant_type' => 'client_credentials',
            'client_id' => $this->gv_client_id_key_new,
            'client_secret' => $this->gv_client_secret_key_new,
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        //if($this->is_log) $this->seme_log->write("api_mobile", "Gogovan POST: ".json_encode($data));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->seme_log->write("api_mobile", "__getGogovanAccessToken -> ".curl_error($ch));
            return 0;
            //echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }

    //by Donny Dennison - 10 july 2020 10:31
    //move send api delivery to controller/api_mobile/order/delivery_process
    // private function __createGogovan($order, $address_pickup, $address_shipping)
    public function __createGogovan($order, $address_pickup, $address_shipping)
    {

        //by Donny Dennison - 8 September 2020 15:09
        //change api gogovan to new version (gogovan change name to gogox)
        $accessTokenGogox = json_decode($this->__getGogovanAccessToken());

        $ch = curl_init();

        //by Donny Dennison - 8 September 2020 15:09
        //change api gogovan to new version (gogovan change name to gogox)
        // curl_setopt($ch, CURLOPT_URL, $this->gv_api_host.'api/v0/orders.json');
        if($this->gv_env_new == 'production'){

          curl_setopt($ch, CURLOPT_URL, $this->gv_api_host_new.'transport/orders'); //for production

        }else{

          curl_setopt($ch, CURLOPT_URL, $this->gv_api_host_new.'api/v2/orders'); //for staging

        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $headers = array();

        //by Donny Dennison - 8 September 2020 15:09
        //change api gogovan to new version (gogovan change name to gogox)
        // $headers[] = 'Gogovan-Api-Key: '.$this->gv_api_key;
        $headers[] = 'authorization: Bearer '.$accessTokenGogox->access_token;

        //by Donny Dennison - 8 September 2020 15:09
        //change api gogovan to new version (gogovan change name to gogox)
        // $headers[] = 'Gogovan-User-Language: en-US';
        $headers[] = 'accept: application/json';

        //by Donny Dennison - 8 September 2020 15:09
        //change api gogovan to new version (gogovan change name to gogox)
        $headers[] = 'content-type: application/json';

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $vehicle = '';
        switch ($order->shipment_vehicle) {
            case "Lorry 10 Ft":
                $vehicle = 'lorry10';
                break;
            case "Lorry 14 Ft":
                $vehicle = 'lorry14';
                break;
            case "Motorcycle":
                $vehicle = 'motorcycle';
                break;
            default:
                $vehicle = 'van';
        }
        $now = strtotime("+2 hour");

        //by Donny Dennison - 8 September 2020 15:09
        //change api gogovan to new version (gogovan change name to gogox)
        // $postdata = array(
        //   'order[name]' => $address_pickup->penerima_nama,
        //   'order[phone_number]' => $address_pickup->penerima_telp,
        //   'order[pickup_time]' => date("Y-m-d", $now).'T'.date("H:i:s", $now).'H',
        //   'order[service_type]' => 'delivery',
        //   'order[vehicle]' => $vehicle,
        //   'order[title_prefix]' => 'SellOn',
        //   'order[bonus]' => '0',
        //   'order[extra_requirements][express_service]' => 'true',
        //   'order[extra_requirements][remark]' => 'Product ('.$order->nama.'), From: '.$address_pickup->address_notes.' To: '.$address_shipping->address_notes.'',

        //   //By Donny Dennison - 27 June 2020 5:31
        //   //Request by Mr Jackie, not using alamat
        //   // 'order[locations]' =>'[["'.$address_pickup->latitude.'", "'.$address_pickup->longitude.'", "'.$address_pickup->alamat2.' '.$address_pickup->alamat.' '.$address_pickup->negara.' '.$address_pickup->kodepos.'"], ["'.$address_shipping->latitude.'", "'.$address_shipping->longitude.'", "'.$address_shipping->alamat2.' '.$address_shipping->alamat.' '.$address_shipping->negara.' '.$address_shipping->kodepos.'"]'
        //   'order[locations]' =>'[["'.$address_pickup->latitude.'", "'.$address_pickup->longitude.'", "'.$address_pickup->alamat2.' '.$address_pickup->negara.' '.$address_pickup->kodepos.'"], ["'.$address_shipping->latitude.'", "'.$address_shipping->longitude.'", "'.$address_shipping->alamat2.' '.$address_shipping->negara.' '.$address_shipping->kodepos.'"]]'
          
        // );
        $postdata = array(
          'vehicle_type' => $vehicle,
          'payment_method' => 'prepaid_wallet',
          'pickup' => array(
            'name' => $address_pickup->penerima_nama,
            'street_address' => $address_pickup->alamat2,
            'floor_or_unit_number' => $address_pickup->address_notes,
            'schedule_at' => $now,
            'location' => array(
              'lat' => $address_pickup->latitude,
              'lng' => $address_pickup->longitude
            ),
            'contact' => array(
              'name' => $address_pickup->penerima_nama,
              'phone_number' => $address_pickup->penerima_telp,
              'phone_extension' => '62'
            )
          ),
          'destinations' => array( array(
            'name' => $address_shipping->nama,
            'street_address' => $address_shipping->alamat2,
            'floor_or_unit_number' => $address_shipping->address_notes,
            'location' => array(
              'lat' => $address_shipping->latitude,
              'lng' => $address_shipping->longitude
            ),
            'contact' => array(
              'name' => $address_shipping->nama,
              'phone_number' => $address_shipping->telp
            )
          ) )
          
        );

        //by Donny Dennison - 8 September 2020 15:09
        //change api gogovan to new version (gogovan change name to gogox)
        // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));

        $result = curl_exec($ch);
        // $this->seme_log->write("api_mobile", 'TESTG-result: '.json_encode($result));
        if (curl_errno($ch)) {
            return 0;
            //echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        if($this->is_log){
          $this->seme_log->write("api_mobile", 'API_Mobile/WayBill::__createGogovan:: -- cUrlHeader: '.json_encode($headers));
          $this->seme_log->write("api_mobile", 'API_Mobile/WayBill::__createGogovan:: -- cUrlPOST: '.json_encode($postdata));
        }
        return $result;
    }
    /**
     * Convert string to URL Slug friendly
     * @param  string $text unformated string
     * @return string       Formatted string
     */
    private function __slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // trim
        $text = trim($text, '-');
        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // lowercase
        $text = strtolower($text);
        return $text;
    }
    /**
     * Create Expired version for WayBill
     * @param  [type] $pelanggan From table b_user
     * @param  [type] $order     From table d_order
     * @param  [type] $pickup    From d_order_detail_pickup
     */

    //by Donny Dennison - 10 july 2020 10:31
    //move send api delivery to controller/api_mobile/order/delivery_process
    // private function __expiredPDF($pelanggan, $order, $pickup)
    public function __expiredPDF($pelanggan, $order, $pickup)
    {
        //load pdf library
        $this->lib("seme_fpdf");
        $this->seme_fpdf->AddUHCFont();

        //set page Size
        $this->seme_fpdf->AddPage('L', 'A5');

        $area = '';

        //by Donny Dennison - 13-07-2020 13:54
        //disable send api to qxpress
        // $tracking_no = $order->shipment_tranid;
        $tracking_no = "-";

        //check logo

        //by Donny Dennison - 23 september 2020 15:42
        //add direct delivery feature
        //START by Donny Dennison - 23 september 2020 15:42
        
        if (strtolower($order->shipment_service) == 'direct delivery') {
            $logo = SENEROOT.'assets/images/direct_delivery.png';
            if (is_file($logo)) {
                $this->seme_fpdf->Image($logo, 2, 1, 40, 20);
                $this->seme_fpdf->Ln();
            }

        // if (strtolower($order->shipment_service) == 'qxpress') {
        }else if (strtolower($order->shipment_service) == 'qxpress') {

        //END by Donny Dennison - 23 september 2020 15:42

            $logo = SENEROOT.'assets/images/qxpress.png';
            if (is_file($logo)) {
                $this->seme_fpdf->Image($logo, 2, 1, 40, 20);
                $this->seme_fpdf->Ln();
            }

            //by Donny Dennison - 13-07-2020 13:54
            //disable send api to qxpress
            // $result = @simplexml_load_string($this->__getQXpressArea($order->addresses->shipping->kodepos), 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS); //LOL use STFU :D veteran should know about this -- DR
            // $result = @json_decode(@json_encode($result));
            // if (isset($result->ResultMsg)) {
            //     $area = $result->ResultMsg;
            // }

        //by Donny Dennison - 15 september 2020 17:45
        //change name, image, etc from gogovan to gogox
        // } elseif (strtolower($order->shipment_service) == 'gogovan') {
        //     $logo = SENEROOT.'assets/images/gogovan.png';
        } elseif (strtolower($order->shipment_service) == 'gogox') {
            $logo = SENEROOT.'assets/images/gogox.png';

            if (is_file($logo)) {
                $this->seme_fpdf->Image($logo, 2, 1, 40, 20);
                $this->seme_fpdf->Ln();
            }
        } else {
            $logo = SENEROOT.'assets/images/sellon.png';
            if (is_file($logo)) {
                $this->seme_fpdf->Image($logo, 2, 1, 40, 20);
                $this->seme_fpdf->Ln();
            }
        }

        //building pdf
        $this->seme_fpdf->SetFont('times', 'B', 10);
        $this->seme_fpdf->SetTextColor(255, 0, 0);
        $this->seme_fpdf->Cell(40, 0, '');
        $this->seme_fpdf->Cell(20, 0, $order->shipment_service, 0, 0, 'R');
        $this->seme_fpdf->Cell(10, 0, '');
        $this->seme_fpdf->Ln();

        $this->seme_fpdf->SetY(7);
        $this->seme_fpdf->SetFont('times', 'B', 6);
        $this->seme_fpdf->SetTextColor(0, 0, 0);
        $this->seme_fpdf->Cell(40, 0, '');
        $this->seme_fpdf->Cell(20, 13, 'TranID #'.$order->invoice_code.'', 0, 0, 'R');
        $this->seme_fpdf->Cell(10, 0, '');
        $this->seme_fpdf->Ln();

        $tgl = $order->cdate;

        $this->seme_fpdf->SetY(9);
        $this->seme_fpdf->SetFont('times', '', 6);
        $this->seme_fpdf->SetTextColor(0, 0, 0);
        $this->seme_fpdf->Cell(40, 0, '');
        $this->seme_fpdf->Cell(20, 13, date("Y-m-d H:i", strtotime($tgl)), 0, 0, 'R');
        $this->seme_fpdf->Cell(10, 0, '');
        $this->seme_fpdf->Ln();


        $this->seme_fpdf->SetFont('times', '', 8);
        $this->seme_fpdf->SetTextColor(0, 0, 0);

        //by Donny Dennison - 13-07-2020 13:54
        //disable send api to qxpress
        // $this->seme_fpdf->Code39(73, 7, $order->shipment_tranid.'', 0.8, 8, 'R');
        $this->seme_fpdf->Code39(73, 7, $tracking_no.'', 0.8, 8, 'R');

        $this->seme_fpdf->Ln();

        //new lines
        $this->seme_fpdf->Line(10, 23, 210-20, 23);

        //table origin start//
        $this->seme_fpdf->SetY(23);
        $this->seme_fpdf->SetFillColor(224, 235, 255);
        $this->seme_fpdf->SetTextColor(0);
        $this->seme_fpdf->SetFont('UHC', '', 8);
        $fill = false;

        $shipping = $order->addresses->shipping;

        $w = array(20,70,20,70); //for width columns, must equal with header array
        $this->seme_fpdf->SetFont('UHC', '', 8);
        $this->seme_fpdf->Cell($w[0], 5, 'Name', 'BLR', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[1], 5, iconv('UTF-8','UHC',$shipping->nama), 'BLR', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[2], 5, 'Name', 'BLR', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[3], 5, iconv('UTF-8','UHC',$pickup->penerima_nama), 'BLR', 0, 'L', $fill);
        $this->seme_fpdf->Ln();

        $this->seme_fpdf->Cell($w[0], 5, 'Tel. No.', 'BLR', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[1], 5, $shipping->telp, 'BLR', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[0], 5, 'Tel. No.', 'BLR', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[1], 5, ($pickup->penerima_telp), 'BLR', 0, 'L', $fill);
        $this->seme_fpdf->Ln();

        //By Donny Dennison - 27 juni 2020 3:23
        //request by Mr Jackie, remove alamat in pdf
        $this->seme_fpdf->Cell($w[0], 5, 'Address', 'TLR', 0, 'L', $fill);
        // $this->seme_fpdf->Cell($w[1], 5, $shipping->alamat, 'TLR', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[1], 5, $shipping->alamat2.' '.$shipping->address_notes, 'TLR', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[0], 5, 'Address', 'TLR', 0, 'L', $fill);
        // $this->seme_fpdf->Cell($w[1], 5, ($pickup->alamat), 'TLR', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[1], 5, ($pickup->alamat2.' '.$pickup->catatan), 'TLR', 0, 'L', $fill);
        // $this->seme_fpdf->Ln();
        // $this->seme_fpdf->Cell($w[0], 5, '', 'BLR', 0, 'L', $fill);
        // $this->seme_fpdf->Cell($w[1], 5, $shipping->alamat2.' '.$shipping->address_notes, 'BLR', 0, 'L', $fill);
        // $this->seme_fpdf->Cell($w[0], 5, '', 'BLR', 0, 'L', $fill);
        // $this->seme_fpdf->Cell($w[1], 5, ($pickup->alamat2.' '.$pickup->catatan), 'BLR', 0, 'L', $fill);
        //-----------//
        //table end//

        //table start//
        $w = array(30, 30, 30,45,45); //for width columns, must equal with header array
        $this->seme_fpdf->Ln();
        $this->seme_fpdf->Cell($w[0], 6, 'Postal Code', 'TLR', 0, 'C', $fill);
        $this->seme_fpdf->Cell($w[1], 6, 'Area', 'TLR', 0, 'C', $fill);
        $this->seme_fpdf->Cell($w[2], 6, 'Destination', 'TLR', 0, 'C', $fill);
        $this->seme_fpdf->Cell($w[3], 6, 'Registered date', 'TLR', 0, 'C', $fill);
        $this->seme_fpdf->Cell($w[4], 6, 'Departure', 'TLR', 0, 'C', $fill);
        $this->seme_fpdf->Ln();
        $this->seme_fpdf->SetFont('UHC', '', 12);
        $this->seme_fpdf->Cell($w[0], 6, $shipping->kodepos, 'BLR', 0, 'C', $fill);
        $this->seme_fpdf->Cell($w[1], 6, $area, 'BLR', 0, 'C', $fill);
        $this->seme_fpdf->Cell($w[2], 6, $shipping->negara, 'BLR', 0, 'C', $fill);
        $this->seme_fpdf->Cell($w[3], 6, date("d-M-y", strtotime($order->cdate)), 'BLR', 0, 'C', $fill);
        $this->seme_fpdf->Cell($w[4], 6, $shipping->negara, 'BLR', 0, 'C', $fill);
        $this->seme_fpdf->Ln();
        //table end//

        // tabel memo
        $w = array(180); //for width columns, must equal with header array
        $this->seme_fpdf->SetFont('UHC', '', 11);
        $this->seme_fpdf->Cell(array_sum($w), 6, 'Memo :'.' Pls pack the item well n deliver them securely to prevent damage. thanks.', 'TLR', 0, 'L', $fill);
        $this->seme_fpdf->Ln();
        //table end//

        // tabel memo
        $w = array(180); //for width columns, must equal with header array
        $this->seme_fpdf->Cell(array_sum($w), 6, 'Item Description'.'       Packing no :'.$order->shipment_tranid.'   Invoice no :'.$order->invoice_code.'', 'TLR', 0, 'L', $fill);
        $this->seme_fpdf->Ln();
        //table end//

        $this->seme_fpdf->SetFont('UHC', '', 10);

        //table start//
        //-----------//
        //table header//
        $this->seme_fpdf->SetFillColor(89, 89, 89);
        $this->seme_fpdf->SetTextColor(255);
        $this->seme_fpdf->SetDrawColor(0, 0, 0);
        $this->seme_fpdf->SetLineWidth(.1);
        $this->seme_fpdf->SetFont('', 'B');

        $header = array('Product', 'Qty', 'Weight'); //header for column
        $w = array(110, 25, 45); //for width columns, must equal with header array
        for ($i=0;$i<count($header);$i++) {
            $this->seme_fpdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        }
        $this->seme_fpdf->Ln();

        //table Data
        $this->seme_fpdf->SetFillColor(224, 235, 255);
        $this->seme_fpdf->SetTextColor(0);

        // Data
        $fill = false;
        $berat_total = 0;
        $order->berat = round($order->berat, 1);
        $order->qty = (int) $order->qty;
        $i = 0;

        $sub_berat = round($order->berat*$order->qty, 1);
        $berat_total += $sub_berat;
        $this->seme_fpdf->Cell($w[0], 6, html_entity_decode($order->nama,ENT_QUOTES), 'LR', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[1], 6, number_format($order->qty, 0, ',', '.').' Pcs', 'LR', 0, 'C', $fill);
        $this->seme_fpdf->Cell($w[2], 6, ''.number_format($order->berat, 1, ',', '.').' Kg', 'LR', 0, 'C', $fill);
        $this->seme_fpdf->Ln();
        $fill = !$fill;
        $i++;
        if (empty($i)) {
            $fill =true;
            $this->seme_fpdf->Cell(array_sum($w), 6, 'Tidak ada produk yang perlu dikirimkan', 'LR', 0, 'L', $fill);
            $this->seme_fpdf->Ln();
        }
        // Closing line
        $this->seme_fpdf->Cell(array_sum($w), 0, '', 'T');
        $this->seme_fpdf->Ln();

        //table footer
        $this->seme_fpdf->SetFillColor(89, 89, 89);
        $this->seme_fpdf->SetTextColor(255);
        $this->seme_fpdf->SetDrawColor(0, 0, 0);
        $this->seme_fpdf->SetLineWidth(.1);

        $header = array('Total', $order->qty.' item(s)', number_format($berat_total, 1, ',', '.').' Kg');
        $w = array(110, 25, 45);
        for ($i=0;$i<count($header);$i++) {
            $this->seme_fpdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        }
        $this->seme_fpdf->Ln();

        //add some new line
        $this->seme_fpdf->Ln();
        $this->seme_fpdf->Ln();

        //table footer
        $this->seme_fpdf->SetFillColor(89, 89, 89);
        $this->seme_fpdf->SetTextColor(255);
        $this->seme_fpdf->SetDrawColor(0, 0, 0);
        $this->seme_fpdf->SetLineWidth(.1);
        $this->seme_fpdf->SetFont('UHC', '');
        //-----------//
        //table end//

        $this->seme_fpdf->setXY(150, 50);
        $this->seme_fpdf->SetFont('Arial', 'B', 50);
        $this->seme_fpdf->SetTextColor(255, 192, 203);
        $this->seme_fpdf->Cell(20, 13, 'EXPIRED ORDER', 0, 0, 'R');


        $save_dir = SENEROOT.'media';
        if (!is_dir($save_dir)) {
            mkdir($save_dir);
        }
        $save_dir = SENEROOT.'media/order/';
        if (!is_dir($save_dir)) {
            mkdir($save_dir);
        }
        $save_file = "waybill-".$order->d_order_id."-".$order->c_produk_id;
        $file_pdf = $save_dir.'/'.$save_file.'.pdf';
        $this->seme_fpdf->Output('I', $file_pdf);
    }
    /**
     * WordWrap for Korean language
     * @param  string  $string input string
     * @param  integer $width  [description]
     * @param  string  $break  [description]
     * @param  boolean $cut    [description]
     * @return string          wrapped string
     */
    private function __wordWrapUTF8($str, $width=75, $break="\n", $cut=false)
    {
        if ($cut) {
            $search = '/(.{1,'.$width.'})(?:\s|$)|(.{'.$width.'})/uS';
            $replace = '$1$2'.$break;
        } else {
            $search = '/(?=\s)(.{1,'.$width.'})(?:\s|$)/uS';
            $replace = '$1'.$break;
        }
        return preg_replace($search, $replace, $str);
    }
    /**
     * Split address into array by row width limit
     * @param  string $astr concatenate address
     * @return array       array of string
     */
    private function __addressSplitter($astr)
    {
        $astr = str_replace("  ", " ", $astr);
        $as = array();
        $as[0] = '';
        $as[1] = '';
        $as[2] = '';
        $as[3] = '';
        $astr = $this->__wordWrapUTF8(trim($astr), 50, "|", false);
        $astr = explode("|", $astr);
        if (isset($astr[0])) {
            $as[0] = $astr[0];
        }
        if (isset($astr[1])) {
            $as[1] = $astr[1];
        }
        if (isset($astr[2])) {
            $as[2] = $astr[2];
        }
        if (isset($astr[3])) {
            $as[3] = $astr[3];
        }
        if (isset($astr[4])) {
            $as[4] = $astr[4];
        }
        return $as;
    }

    /**
     * Address structure fixer
     * @param  string $alamat        [description]
     * @param  string $alamat2       [description]
     * @param  string $address_notes [description]
     * @param  string $negara        [description]
     * @param  string $kodepos       [description]
     * @return string                [description]
     */

    // by Donny Dennison - 3 November 2021 10:00
    // remark code
    // private function __addressStructureFixer(string $alamat,string $alamat2,string $address_notes,string $negara,string $kodepos){
    private function __addressStructureFixer(string $alamat2,string $address_notes,string $negara,string $kodepos){

        // by Donny Dennison - 3 November 2021 10:00
        // remark code
        // $alamat = mb_substr(trim(mb_ereg_replace('  ', ' ', mb_ereg_replace($kodepos, '', $alamat))),0,50,'UTF-8');
      
        //By Donny Dennison - 27 juni 2020 3:23
        //request by Mr Jackie, remove alamat in pdf
        // $alamat2 = mb_substr(trim(mb_ereg_replace('  ', ' ', mb_ereg_replace($kodepos, '', mb_ereg_replace(', Singapore', '', $alamat2)))),0,50,'UTF-8');
        $alamat2 = mb_substr(trim(mb_ereg_replace('  ', ' ', mb_ereg_replace($kodepos, '', $alamat2))),0,50,'UTF-8');
      
        //By Donny Dennison - 29 juni 2020 11:20
        //request by Mr Jackie, after remove alamat in pdf make another bug, if there is no address_notes the return array is not in array 0, the return array is dynamic
        // $address_notes = mb_substr(trim(mb_ereg_replace('  ', ' ', mb_ereg_replace($kodepos, '', $address_notes))),0,50,'UTF-8');
        $address_notes = mb_substr(trim(mb_ereg_replace('  ', ' ', $address_notes)),0,50,'UTF-8');

        $negara = mb_substr(trim(mb_ereg_replace('  ', ' ', mb_ereg_replace($kodepos, '', $negara))),0,50,'UTF-8');
      
        // by Donny Dennison - 3 November 2021 10:00
        // remark code
        // $ka = array($address_notes,$alamat,$alamat2,$negara.' '.$kodepos,'');
        $ka = array($address_notes,$alamat2,$negara.' '.$kodepos,'');

        $c = count($ka);
        for($i=0;$i<$c;$i++){
            if(mb_strlen($ka[$i])==0 && ($i+1)<$c){
                $ka[$i] = $ka[$i+1];
                $ka[$i+1] = '';
            }
        }
        return $ka;
    }

    public function index()
    {
        $this->__json_out(array());
    }

    /**
     * Generates waybill file
     * @param  integer $d_order_id  [description]
     * @param  integer $c_produk_id THis is not c_produk_id but d_order_detail_id, for mobile sake this parameter are not changed
     * @return [type]              [description]
     */
    public function cetak($d_order_id, $c_produk_id)
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
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_waybill");
            die();
        }

        $d_order_id = (int) $d_order_id;
        if ($d_order_id<=0) {
            $this->status = 6010;
            $this->message = 'Invalid Order ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_waybill");
            die();
        }

        //this is not c_produk_id but ID from d_order_detail, for mobile apps sake var doesnt renamed
        $c_produk_id = (int) $c_produk_id;
        if ($c_produk_id<=0) {
            $this->status = 6011;
            $this->message = 'Invalid Produk ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_waybill");
            die();
        }
        //log order id
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> POST: d_order_id: $d_order_id, c_produk_id: $c_produk_id");
        }

        //get order from d_order_detail and d_order
        $order = $this->dodm->getById($nation_code, $d_order_id, $c_produk_id);

        //running backward compatibilty
        if (!isset($order->d_order_id)) {
            $c_produk_id = $this->dodim->getOrderDetailByOrderIdProdukId($nation_code, $d_order_id, $c_produk_id);
            if ($c_produk_id<=0) {
                $this->status = 6012;
                $this->message = 'Order with supplied ID(s) not found';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_waybill");
                die();
            }
            $order = $this->dodm->getById($nation_code, $d_order_id, $c_produk_id);
        }
        $b_user_seller_id = 0;
        if (isset($order->b_user_id)) {
            $b_user_seller_id = $order->b_user_id;
        }
        if (isset($order->b_user_id_seller)) {
            $b_user_seller_id = $order->b_user_id_seller;
        }
        $pelanggan = $this->bu->getById($nation_code, $b_user_seller_id);
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_waybill");
            die();
        }

        if ($order->payment_status != 'paid') {
            $this->status = 6020;
            $this->message = 'Unpaid order';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_waybill");
            die();
        }

        if ($order->seller_status != 'confirmed') {
            $this->status = 6021;
            $this->message = 'Unconfirmed by seller';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_waybill");
            die();
        }


        //by Donny Dennison - 10 july 2020 10:31
        //move send api delivery to controller/api_mobile/order/delivery_process
        // START change by Donny Dennison - 10 july 2020 10:31
        // //get address pickup
        $pickup = $this->dodpum->getById($nation_code, $order->d_order_id, $order->d_order_detail_id);
        // if (!isset($pickup->penerima_nama)) {
        //     //if not exist, get from b_user_alamat
        //     $pa = $this->bua->getById($nation_code, $order->b_user_id_seller, $order->b_user_alamat_id);
        //     if (!isset($pa->penerima_nama)) {
        //         $this->status = 6022;
        //         $this->message = 'Pickup address not found';
        //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_waybill");
        //         die();
        //     }

        //     //insert into pickup order
        //     $padi = array();
        //     $padi['nation_code'] = $nation_code;
        //     $padi['d_order_id'] = $order->d_order_id;
        //     $padi['d_order_detail_id'] = $order->d_order_detail_id;
        //     $padi['b_user_id'] = $order->b_user_id_seller;
        //     $padi['b_user_alamat_id'] = $order->b_user_alamat_id;
        //     $padi['nama'] = $pa->penerima_nama;
        //     $padi['telp'] = $pa->penerima_telp;
        //     $padi['alamat'] = $pa->alamat;
        //     $padi['alamat2'] = $pa->alamat2;
        //     $padi['kelurahan'] = $pa->kelurahan;
        //     $padi['kecamatan'] = $pa->kecamatan;
        //     $padi['kabkota'] = $pa->kabkota;
        //     $padi['provinsi'] = $pa->penerima_nama;
        //     $padi['negara'] = $pa->negara;
        //     $padi['kodepos'] = $pa->kodepos;
        //     $padi['latitude'] = $pa->latitude;
        //     $padi['longitude'] = $pa->longitude;
        //     $padi['catatan'] = $pa->address_notes;
        //     $this->dodpum->set($padi);
        //     $pickup = $pa;
        //     $pickup->nama = $pa->penerima_nama;
        //     $pickup->telp = $pa->penerima_nama;
        //     $pickup->alamat1 = $pa->alamat;
        //     $pickup->catatan = $pa->address_notes;
        // }

        if (isset($order->is_wb_download)) {
            $this->dodm->updateWB($nation_code, $d_order_id, $order->d_order_detail_id);
        }
        // if (isset($order->foto)) {
        //     $order->foto = $this->cdn_url($order->foto);
        // }
        // if (isset($order->thumb)) {
        //     $order->thumb = $this->cdn_url($order->thumb);
        // }

        // //get buyer detail
        $buyer = $this->bu->getById($nation_code, $order->b_user_id_buyer);
        // $seller = $pelanggan;

        //put another
        $order->addresses = $this->__orderAddresses($nation_code, $buyer, $d_order_id);
        $order->proses = $this->dopm->getByOrderId($nation_code, $d_order_id);

        //validation
        $is_rejected = 0;
        if (strtolower($order->seller_status) == 'rejected') {
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print --expiredPDF");
            }
            $is_rejected = 1;
            $this->__expiredPDF($pelanggan, $order, $pickup);
            die();
            //$this->status = 6013;
            //$this->message = 'Order already rejected by seller';
            //$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_waybill");
            //die();
        }

        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -- order->shipment_service: $order->shipment_service, order->shipment_type: $order->shipment_type");
        }

        // //By Donny Dennison - 08-07-2020 16:16
        // //Request by Mr Jackie, add new shipment status "courier fail"
        // //create pickup order, for shipment == process
        // // if (strtolower($order->shipment_service) == 'qxpress' && (strtolower($order->shipment_status) == 'process' || strtolower($order->shipment_status) == 'delivered')) {

        // $isFailedApiDelivery = FALSE;
        // if (strtolower($order->shipment_service) == 'qxpress' && (strtolower($order->shipment_status) == 'process' || strtolower($order->shipment_status) == 'delivered' || strtolower($order->shipment_status) == 'courier fail')) {

        //     if (strtolower($order->shipment_type) == 'next day' && strlen($order->shipment_tranid)<=4) {
        //         $addr = $order->addresses->shipping;

        //         //By Donny Dennison - 7 june 2020 - 14:29
        //         //change send data send to qxpress from buyer to seller
        //         // $rq = $this->__createQXpress($addr, $order, $seller);
        //         $rq = $this->__createQXpress($addr, $order, $buyer);

        //         //put on log
        //         $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> __createQXpress: ".($rq));

        //         //parsing XML result
        //         $sodt = @simplexml_load_string($rq);
        //         if ($sodt === false) {
                    
        //             //parsing error
        //             $cqxe = '';
        //             foreach (libxml_get_errors() as $error) {
        //                 $cqxe .= $error->message.', ';
        //             }
        //             $cqxe = rtrim($cqxe, ', ');
        //             if ($this->is_log) {
        //                 $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> __createQXpress PARSE_ERROR: ".$cqxe);
        //             }

        //             //By Donny Dennison - 08-07-2020 16:16
        //             //Request by Mr Jackie, add new shipment status "courier fail"
        //             // //notif seller to sent it manually, collect array notification list
        //             // $extras = new stdClass();
        //             // $extras->id_produk = $order->c_produk_id;
        //             // $extras->id_order = $order->d_order_id;
        //             // $extras->id_order_detail = $order->c_produk_id;
        //             // $dpe = array();
        //             // $dpe['nation_code'] = $nation_code;
        //             // $dpe['b_user_id'] = $pelanggan->id;
        //             // $dpe['id'] = $this->dpem->getLastId($nation_code, $pelanggan->id);
        //             // $dpe['type'] = "transaction";
        //             // $dpe['judul'] = "Sent to QXpress";
        //             // $dpe['teks'] = "Please bring your ordered product to nearest QXPress courier services.";
        //             // $dpe['cdate'] = "NOW()";
        //             // $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
        //             // $dpe['extras'] = json_encode($extras);
        //             // $this->dpem->set($dpe);
        //             // $this->order->trans_commit();
        //             $isFailedApiDelivery = TRUE;

        //         } else {
        //             //parse XML success
        //             if (!is_object($sodt)) {
        //                 $sodt = new stdClass();
        //             }
        //             if (!isset($sodt->ResultCode)) {
        //                 $sodt->ResultCode = '-99999';
        //             }
        //             if ($sodt->ResultCode==0) {
        //                 if ($this->is_log) {
        //                     $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> sodt->ResultCode->TrackingNo: ".json_encode($sodt->ResultObject));
        //                 }
        //                 //success, check response result object reference number
        //                 if (isset($sodt->ResultObject->TrackingNo)) {
        //                     $order->shipment_tranid = $sodt->ResultObject->TrackingNo;
        //                 }

        //                 //By Donny Dennison, 30 june 2020 15:43
        //                 //Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
        //                 //Start change by Donny Dennison
        //                 $tracking_number = NULL;
        //                 $rq2 = $this->__getQXpressTracking($order->shipment_tranid);

        //                 //put on log
        //                 $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> __getQXpressTracking: ".($rq2));

        //                 //parsing XML result
        //                 $sodt2 = @simplexml_load_string($rq2);
                        
        //                 //parse XML success
        //                 if (!is_object($sodt2)) {
        //                   $sodt2 = new stdClass();
        //                 }
        //                 if (!isset($sodt2->ResultCode)) {
        //                   $sodt2->ResultCode = '-99';
        //                 }
        //                 if ($sodt2->ResultCode==0) {

        //                   if ($this->is_log) {
        //                     $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> sodt2->ResultCode->TrackingNo: ".json_encode($sodt2->ResultObject));
        //                   }

        //                   //add to field tracking_number
        //                   $tracking_number = $sodt2->ResultObject->info->shipping_no;
                              
        //                 } elseif ($sodt2->ResultCode=="-99" || $sodt2->ResultCode==-99) {
        //                   //if server error, recreate order QXpress
        //                   $rq2 = $this->__getQXpressTracking($order->shipment_tranid);
        //                   $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print QXpress server error, recreating");
        //                   //put on log
        //                   $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> __getQXpressTracking : ".($rq2));

        //                   //parsing XML result
        //                   $sodt2 = simplexml_load_string($rq2);
                          
        //                   //parse OK
        //                   if ($this->is_log) {
        //                       $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> sodt2->ResultCode->TrackingNo: ".json_encode($sodt2->ResultObject));
        //                   }

        //                   //decode result
        //                   $pudt = json_decode($rq2);
        //                   if (isset($pudt->ResultCode)) {
        //                     if ($pudt->ResultCode == 0) {
                                
        //                       //add to field tracking_number
        //                       $tracking_number = $pudt->ResultObject->info->shipping_no;

        //                     } else {
        //                         //maybe pickup order has created before, do nothing
        //                     }
        //                   } else {
        //                       //notif to seller for deliver their product manually, collect array notification list
        //                   }

        //                 }

        //                 //End change by Donny Dennison

        //                 //update order detail
        //                 $dx = array();
        //                 $dx["shipment_tranid"] = $order->shipment_tranid;

        //                 //By Donny Dennison, 30 june 2020 15:43
        //                 //Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
        //                 $dx["tracking_number"] = $tracking_number;
                        
        //                 $dx["shipment_confirmed"] = 1;
        //                 $dx["pickup_date"] = "null";
        //                 $dx['shipment_response'] = $rq;
        //                 $this->dodm->update($nation_code, $order->d_order_id, $order->c_produk_id, $dx);
        //                 $this->order->trans_commit();

        //                 //create pickup
        //                 $seller = $this->bu->getById($nation_code, $order->b_user_id_seller);
        //                 $rq2 = $this->__createQXpressPickup($pickup, $order, $seller);

        //                 //put on log
        //                 $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> __createQXpressPickup: ".($rq2));

        //                 //decode result
        //                 $pudt = json_decode($rq2);
        //                 if (isset($pudt->ResultCode)) {
        //                     if ($pudt->ResultCode == 0) {
        //                         //collect array notification list
        //                         $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print QXpress Pickup Order done");

        //                         //update pickup date, to tommorow
        //                         $dx = array();
        //                         $dx["pickup_date"] = date("Y-m-d H:i:s", strtotime("+1 day"));
        //                         $dx["delivery_date"] = $dx["pickup_date"];
        //                         $this->dodm->update($nation_code, $order->d_order_id, $order->c_produk_id, $dx);
        //                         $this->order->trans_commit();
        //                         $order->delivery_date = $dx["delivery_date"];
        //                         $order->pickup_date = $dx["pickup_date"];

        //                         // add to order proses with current status
        //                         $ops = array();
        //                         $ops['nation_code'] = $nation_code;
        //                         $ops['d_order_id'] = $d_order_id;
        //                         $ops['c_produk_id'] = $order->c_produk_id;
        //                         $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order->c_produk_id);
        //                         $ops['initiator'] = "Seller";
        //                         $ops['nama'] = "Pickup Requested";
        //                         $ops['deskripsi'] = "Your order $order->nama ($order->invoice_code) has been added to the QXpress: Next Day pickup queue list";
        //                         $ops['cdate'] = "NOW()";
        //                         $this->dopm->set($ops);
        //                         $this->order->trans_commit();
        //                     } else {
        //                         //maybe pickup order has created before, do nothing
        //                     }
        //                 } else {
        //                     //notif to seller for deliver their product manually, collect array notification list
        //                 }

        //                 // add to order proses with current status
        //                 $ops = array();
        //                 $ops['nation_code'] = $nation_code;
        //                 $ops['d_order_id'] = $d_order_id;
        //                 $ops['c_produk_id'] = $order->c_produk_id;
        //                 $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order->c_produk_id);
        //                 $ops['initiator'] = "Seller";
        //                 $ops['nama'] = "Delivery in Progress";

        //                 //By Donny Dennison, 30 june 2020 15:43
        //                 //Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
        //                 // $ops['deskripsi'] = "Your order $order->nama ($order->invoice_code) has been sent by the seller using a courier from QXpress: Next Day (receipt number: $order->shipment_tranid)";
        //                 $ops['deskripsi'] = "Your order $order->nama ($order->invoice_code) has been sent by the seller using a courier from QXpress: Next Day (receipt number: ".$tracking_number.")";

        //                 $ops['cdate'] = "NOW()";
        //                 $this->dopm->set($ops);
        //                 $this->order->trans_commit();
        //             } elseif ($sodt->ResultCode=="-55" || $sodt->ResultCode==-55) {
        //                 //duplicate invoice code or tranid, recreate order QXpress

        //                 //By Donny Dennison - 7 june 2020 - 14:29
        //                 //change send data send to qxpress from buyer to seller
        //                 // $rq = $this->__createQXpress($addr, $order, $seller, 0);
        //                 $rq = $this->__createQXpress($addr, $order, $buyer, 0);

        //                 $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print QXpress Create Order same TranID, recreating");
        //                 //put on log
        //                 $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> __createQXpress phase2: ".($rq));

        //                 //parsing XML result
        //                 $sodt = simplexml_load_string($rq);
        //                 if ($sodt === false) {
        //                     //parsing error
        //                     $cqxe = '';
        //                     foreach (libxml_get_errors() as $error) {
        //                         $cqxe .= $error->message.', ';
        //                     }
        //                     $cqxe = rtrim($cqxe, ', ');
        //                     if ($this->is_log) {
        //                         $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> __createQXpress phase2 PARSE_ERROR: ".$cqxe);
        //                     }

        //                     //By Donny Dennison - 08-07-2020 16:16
        //                     //Request by Mr Jackie, add new shipment status "courier fail"
        //                     $isFailedApiDelivery = TRUE;

        //                 } else {
        //                     //parse OK
        //                     if ($this->is_log) {
        //                         $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> sodt->ResultCode->TrackingNo: ".json_encode($sodt->ResultObject));
        //                     }
        //                     //success, check response result object tracking number
        //                     if (isset($sodt->ResultObject->TrackingNo)) {
        //                         $order->shipment_tranid = $sodt->ResultObject->TrackingNo;
        //                     }

        //                     //By Donny Dennison, 30 june 2020 15:43
        //                     //Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
        //                     //Start change by Donny Dennison
        //                     $tracking_number = NULL;
        //                     $rq2 = $this->__getQXpressTracking($order->shipment_tranid);

        //                     //put on log
        //                     $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> __getQXpressTracking: ".($rq2));

        //                     //parsing XML result
        //                     $sodt2 = @simplexml_load_string($rq2);
                            
        //                     //parse XML success
        //                     if (!is_object($sodt2)) {
        //                       $sodt2 = new stdClass();
        //                     }
        //                     if (!isset($sodt2->ResultCode)) {
        //                       $sodt2->ResultCode = '-99';
        //                     }
        //                     if ($sodt2->ResultCode==0) {

        //                       if ($this->is_log) {
        //                         $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> sodt2->ResultCode->TrackingNo: ".json_encode($sodt2->ResultObject));
        //                       }

        //                       //add to field tracking_number
        //                       $tracking_number = $sodt2->ResultObject->info->shipping_no;
                                  
        //                     } elseif ($sodt2->ResultCode=="-99" || $sodt2->ResultCode==-99) {
        //                       //if server error, recreate order QXpress
        //                       $rq2 = $this->__getQXpressTracking($order->shipment_tranid);
        //                       $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print QXpress server error, recreating");
        //                       //put on log
        //                       $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> __getQXpressTracking phase2: ".($rq2));

        //                       //parsing XML result
        //                       $sodt2 = simplexml_load_string($rq2);
                              
        //                       //parse OK
        //                       if ($this->is_log) {
        //                           $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> sodt2->ResultCode->TrackingNo: ".json_encode($sodt2->ResultObject));
        //                       }

        //                       //decode result
        //                       $pudt = json_decode($rq2);
        //                       if (isset($pudt->ResultCode)) {
        //                         if ($pudt->ResultCode == 0) {
                                    
        //                           //add to field tracking_number
        //                           $tracking_number = $pudt->ResultObject->info->shipping_no;

        //                         } else {
        //                             //maybe pickup order has created before, do nothing
        //                         }
        //                       } else {
        //                           //notif to seller for deliver their product manually, collect array notification list
        //                       }

        //                     }

        //                     //End change by Donny Dennison

        //                     //update order detail
        //                     $dx = array();
        //                     $dx["shipment_tranid"] = $order->shipment_tranid;
                            
        //                     //By Donny Dennison, 30 june 2020 15:43
        //                     //Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
        //                     $dx["tracking_number"] = $tracking_number;

        //                     $dx["shipment_confirmed"] = 1;
        //                     $dx["pickup_date"] = "null";
        //                     $dx["delivery_date"] = "NOW()";
        //                     $dx['shipment_response'] = $rq;
        //                     $this->dodm->update($nation_code, $order->d_order_id, $order->c_produk_id, $dx);
        //                     $this->order->trans_commit();
        //                     $order->delivery_date = date("Y-m-d");
        //                     $order->pickup_date = date("Y-m-d");

        //                     //create pickup
        //                     $seller = $this->bu->getById($nation_code, $order->b_user_id_seller);
        //                     $rq2 = $this->__createQXpressPickup($pickup, $order, $seller);

        //                     //put on log
        //                     $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> __createQXpressPickup: ".($rq2));

        //                     //decode result
        //                     $pudt = json_decode($rq2);
        //                     if (isset($pudt->ResultCode)) {
        //                         if ($pudt->ResultCode == 0) {
        //                             //update pickup date, to tommorow
        //                             $dx = array();
        //                             $dx["pickup_date"] = date("Y-m-d H:i:s", strtotime("+1 day"));
        //                             $dx["delivery_date"] = date("Y-m-d H:i:s", strtotime("+1 day"));
        //                             $this->dodm->update($nation_code, $order->d_order_id, $order->c_produk_id, $dx);
        //                             $this->order->trans_commit();
        //                             $order->delivery_date = $dx["delivery_date"];
        //                             $order->pickup_date = $dx["pickup_date"];

        //                             // add to order proses with current status
        //                             $ops = array();
        //                             $ops['nation_code'] = $nation_code;
        //                             $ops['d_order_id'] = $d_order_id;
        //                             $ops['c_produk_id'] = $order->c_produk_id;
        //                             $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order->c_produk_id);
        //                             $ops['initiator'] = "Seller";
        //                             $ops['nama'] = "Pickup Requested";
        //                             $ops['deskripsi'] = "Your order $order->nama ($order->invoice_code) has been added to the QXpress: Next Day pickup queue list";
        //                             $ops['cdate'] = "NOW()";
        //                             $this->dopm->set($ops);
        //                             $this->order->trans_commit();
        //                         } else {
        //                             //maybe pickup order has created before, do nothing
        //                         }
        //                     } else {
        //                         //notif to seller for deliver their product manually, collect array notification list
        //                     }

        //                     // add to order proses with current status
        //                     $ops = array();
        //                     $ops['nation_code'] = $nation_code;
        //                     $ops['d_order_id'] = $d_order_id;
        //                     $ops['c_produk_id'] = $order->c_produk_id;
        //                     $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order->c_produk_id);
        //                     $ops['initiator'] = "Seller";
        //                     $ops['nama'] = "Delivery in Progress";

        //                     //By Donny Dennison, 30 june 2020 15:43
        //                     //Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
        //                     // $ops['deskripsi'] = "Your order $order->nama ($order->invoice_code) has been sent by the seller using a courier from QXpress: Next Day (receipt number: $order->shipment_tranid)";
        //                     $ops['deskripsi'] = "Your order $order->nama ($order->invoice_code) has been sent by the seller using a courier from QXpress: Next Day (receipt number: ".$tracking_number.")";

        //                     $ops['cdate'] = "NOW()";
        //                     $this->dopm->set($ops);
        //                     $this->order->trans_commit();
        //                 } //end parse validation
        //             } else {
        //                 // __createQXpress response code error, maybe order already created

        //                 //By Donny Dennison - 08-07-2020 16:16
        //                 //Request by Mr Jackie, add new shipment status "courier fail"
        //                 if ($this->is_log) {
        //                   $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> response dari QXpress bukan 0, isi sodt: ".json_encode($sodt));
        //                 }
        //                 $isFailedApiDelivery = TRUE;

        //             }
        //         }
        //     } elseif ((strtolower($order->shipment_type) == 'same day' || (strtolower($order->shipment_type) == 'sameday')) && strlen($order->shipment_tranid)<=4) {
        //         //for qxpress same day, manually with admin action
        //         //update pickup date to next 2hours
        //         $dod = array();
        //         $dod["pickup_date"] = date("Y-m-d H:i:s", strtotime("+2 hour"));
        //         $dod["delivery_date"] = date("Y-m-d H:i:s", strtotime("+4 hour"));
        //         $this->dodm->update($nation_code, $d_order_id, $c_produk_id, $dod);
        //         $this->order->trans_commit();
        //         $order->delivery_date = $dod["delivery_date"];
        //         $order->pickup_date = $dod["pickup_date"];

        //         //send email to admin
        //         if ($this->email_send) {
        //             //get product data
        //             $produk_nama = '-';
        //             $items = $this->dodim->getByOrderIdDetailId($nation_code, $d_order_id, $c_produk_id);
        //             if (count($items)) {
        //                 $produk_nama = '';
        //                 foreach ($items as $itm) {
        //                     $produk_nama .= $itm->nama.', ';
        //                 }
        //             }

        //             //get active admin
        //             $admins = $this->apm->getEmailActive();

        //             //begin send email to admin
        //             $replacer = array();
        //             $replacer['site_name'] = $this->app_name;
        //             $replacer['produk_nama'] = $produk_nama;
        //             $replacer['invoice_code'] = $order->invoice_code;
        //             $this->seme_email->replyto($this->site_name, $this->site_replyto);
        //             $this->seme_email->from($this->site_email, $this->site_name);
        //             $eml = '';
        //             foreach ($admins as $adm) {
        //                 if (strlen($adm->email)>4) {
        //                     $this->seme_email->to($adm->email, $adm->nama);
        //                     $eml .= $adm->email.', ';
        //                 }
        //             }
        //             $this->seme_email->subject('QXpress - Same day');
        //             $this->seme_email->template('qxpress_sameday');
        //             $this->seme_email->replacer($replacer);
        //             $this->seme_email->send();

        //             $eml = rtrim($eml, ', ');
        //             if ($this->is_log) {
        //                 $this->seme_log->write("api_mobile", "API_Mobile/WayBill::print --sendEmailWBAdmin --to: $eml");
        //             }
        //             //end send email to admin

        //             // add to order proses with current status
        //             $ops = array();
        //             $ops['nation_code'] = $nation_code;
        //             $ops['d_order_id'] = $d_order_id;
        //             $ops['c_produk_id'] = $order->c_produk_id;
        //             $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order->c_produk_id);
        //             $ops['initiator'] = "Seller";
        //             $ops['nama'] = "Delivery in Progress";
        //             $ops['deskripsi'] = "Your order $order->nama ($order->invoice_code) has been added to QXpress: Same Day queue, please wait for delivery";
        //             $ops['cdate'] = "NOW()";
        //             $this->dopm->set($ops);
        //             $this->order->trans_commit();
        //         }
        //     } else {
        //         //undefined shipping type, do nothing
        //     }

        // //By Donny Dennison - 08-07-2020 16:16
        // //Request by Mr Jackie, add new shipment status "courier fail"
        // // } elseif (strtolower($order->shipment_service) == 'gogovan' && (strtolower($order->shipment_status) == 'process' || strlen($order->shipment_tranid)<=4)) {
        // } elseif (strtolower($order->shipment_service) == 'gogovan' && (strtolower($order->shipment_status) == 'process' || strtolower($order->shipment_status) == 'courier fail' || strlen($order->shipment_tranid)<=4)) {
        //     $address_deliver = $order->addresses->shipping;
        //     $rq = $this->__createGogovan($order, $pickup, $address_deliver);
        //     $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> __createGogovan: ".($rq));
        //     $rqd = json_decode($rq);
        //     if (isset($rqd->id)) {
        //         error_reporting(E_ALL);
        //         $dx = array();
        //         $dx["shipment_tranid"] = $rqd->id;
        //         $dx["shipment_confirmed"] = 1;
        //         $dx["pickup_date"] = date("Y-m-d H:i:00", strtotime("+2 hours"));
        //         $dx["delivery_date"] = date("Y-m-d H:i:00", strtotime("+4 hours"));
        //         $dx['shipment_response'] = $rq;
        //         $this->dodm->update($nation_code, $order->d_order_id, $order->c_produk_id, $dx);
        //         $this->order->trans_commit();
        //         $order->shipment_tranid = $rqd->id;
        //         $order->delivery_date = $dx["delivery_date"];
        //         $order->pickup_date = $dx["pickup_date"];

        //         //inform buyer with current status
        //         $ops = array();
        //         $ops['nation_code'] = $nation_code;
        //         $ops['d_order_id'] = $d_order_id;
        //         $ops['c_produk_id'] = $order->c_produk_id;
        //         $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order->c_produk_id);
        //         $ops['initiator'] = "Seller";
        //         $ops['nama'] = "Delivery in Progress";
        //         $ops['deskripsi'] = "Your order $order->nama ($order->invoice_code) has been sent by the seller using a courier from Gogovan (receipt number: $order->shipment_tranid)";
        //         $ops['cdate'] = "NOW()";
        //         $this->dopm->set($ops);
        //         $this->order->trans_commit();
        //     }else{

        //       //By Donny Dennison - 08-07-2020 16:16
        //       //Request by Mr Jackie, add new shipment status "courier fail"
        //       //response from Gogovan not an id
        //       if ($this->is_log) {
        //         $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print -> response dari Gogovan bukan id, isi rq: ".($rq));
        //       }

        //       $isFailedApiDelivery = TRUE;

        //     }
//
        // } else {
        //     //undefined shipment method, do nothing...        
        // }

        // //By Donny Dennison - 08-07-2020 16:16
        // //Request by Mr Jackie, add new shipment status "courier fail"
        // if($isFailedApiDelivery == TRUE){

        //    //populating update data
        //   $du = array();
        //   $du['delivery_date'] = "null";
        //   $du['shipment_status'] = 'courier fail';
        //   $du['date_begin'] = "null";
        //   $du['date_expire'] = "null";
        //   $res = $this->dodm->update($nation_code, $d_order_id, $order->c_produk_id, $du);
        //   $this->order->trans_commit();

        //   $this->status = 301;
        //   $this->message = 'Failed Creating Delivery Order';
        //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_waybill");
        //   die();
        
        // }
        // END change by Donny Dennison - 10 july 2020 10:31

        //load pdf library
        $this->lib("seme_fpdf");
        $this->seme_fpdf->AddUHCFont();

        //set page Size
        $this->seme_fpdf->AddPage('L', 'A5');

        $area = '';

        //by Donny Dennison - 13-07-2020 13:54
        //disable send api to qxpress
        // //by Donny Dennison - 13-07-2020 17:44
        // //get tracking number if qxpress next day
        // // $tracking_no = $order->shipment_tranid;
        // if(strtolower($order->shipment_service) == 'qxpress' && strtolower($order->shipment_type) == 'next day'){
        
        //   $tracking_no = $order->tracking_number;

        // }else{

        //   $tracking_no = $order->shipment_tranid;

        // }
          $tracking_no = "-";

        //check logo

        //by Donny Dennison - 23 september 2020 15:42
        //add direct delivery feature
        //START by Donny Dennison - 23 september 2020 15:42
        
        if (strtolower($order->shipment_service) == 'direct delivery') {
            $logo = SENEROOT.'assets/images/direct_delivery.png';
            if (is_file($logo)) {
                $this->seme_fpdf->Image($logo, 90, 5, 40, 20);
                $this->seme_fpdf->Ln();
            }

            $this->seme_fpdf->SetFont('times', '', 8);
            $this->seme_fpdf->SetTextColor(40, 40, 40);
            $this->seme_fpdf->Cell(0, 10, 'For Tracking the delivery status');
            $this->seme_fpdf->Ln();
            $this->seme_fpdf->Cell(20, 0, 'Please visit our website: ');
            $this->seme_fpdf->SetTextColor(0, 0, 200);
            $this->seme_fpdf->Cell(31, 0, 'http://sellon.net/', 0, 0, 'R');
            $this->seme_fpdf->Ln();

        // if (strtolower($order->shipment_service) == 'qxpress') {
        } else if (strtolower($order->shipment_service) == 'qxpress') {

        //END by Donny Dennison - 23 september 2020 15:42

            $logo = SENEROOT.'assets/images/qxpress.png';
            if (is_file($logo)) {
                $this->seme_fpdf->Image($logo, 90, 5, 40, 20);
                $this->seme_fpdf->Ln();
            }

            //by Donny Dennison - 13-07-2020 13:54
            //disable send api to qxpress
            // $result = @simplexml_load_string($this->__getQXpressArea($order->addresses->shipping->kodepos), 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS); //LOL use STFU :D veteran should know about this -- DR
            // $result = @json_decode(@json_encode($result));
            // if (isset($result->ResultMsg)) {
            //     $area = $result->ResultMsg;
            // }

            $this->seme_fpdf->SetFont('times', '', 8);
            $this->seme_fpdf->SetTextColor(40, 40, 40);
            $this->seme_fpdf->Cell(0, 10, 'For Tracking the delivery status');
            $this->seme_fpdf->Ln();
            $this->seme_fpdf->Cell(20, 0, 'Please visit our website: ');
            $this->seme_fpdf->SetTextColor(0, 0, 200);
            $this->seme_fpdf->Cell(31, 0, 'http://qxpress.asia/', 0, 0, 'R');
            $this->seme_fpdf->Ln();

        //by Donny Dennison - 15 september 2020 17:45
        //change name, image, etc from gogovan to gogox
        // } elseif (strtolower($order->shipment_service) == 'gogovan') {
        //     $logo = SENEROOT.'assets/images/gogovan.png';
        } elseif (strtolower($order->shipment_service) == 'gogox') {
            $logo = SENEROOT.'assets/images/gogox.png';

            if (is_file($logo)) {
                $this->seme_fpdf->Image($logo, 90, 5, 40, 20);
                $this->seme_fpdf->Ln();
            }
            $this->seme_fpdf->SetFont('times', '', 8);
            $this->seme_fpdf->SetTextColor(40, 40, 40);
            $this->seme_fpdf->Cell(0, 10, 'For Tracking the delivery status');
            $this->seme_fpdf->Ln();
            $this->seme_fpdf->Cell(20, 0, 'Please visit our website: ');
            $this->seme_fpdf->SetTextColor(0, 0, 200);

            //by Donny Dennison - 15 september 2020 17:45
          //change name, image, etc from gogovan to gogox
            // $this->seme_fpdf->Cell(31, 0, 'http://gogovan.sg/', 0, 0, 'R');
            $this->seme_fpdf->Cell(31, 0, 'https://www.gogox.com/sg/', 0, 0, 'R');

            $this->seme_fpdf->Ln();
        } else {
            $logo = SENEROOT.'assets/images/sellon.png';
            if (is_file($logo)) {
                $this->seme_fpdf->Image($logo, 90, 5, 40, 20);
                $this->seme_fpdf->Ln();
            }
            $this->seme_fpdf->SetFont('times', '', 8);
            $this->seme_fpdf->SetTextColor(40, 40, 40);
            $this->seme_fpdf->Cell(0, 10, 'For Tracking the delivery status');
            $this->seme_fpdf->Ln();
            $this->seme_fpdf->Cell(20, 0, 'Please visit our website: ');
            $this->seme_fpdf->SetTextColor(0, 0, 200);
            $this->seme_fpdf->Cell(31, 0, 'http://sellon.net/', 0, 0, 'R');
            $this->seme_fpdf->Ln();
        }

        //definisikan directory
        $save_dir = SENEROOT.'media';
        if (!is_dir($save_dir)) {
            mkdir($save_dir);
        }
        $save_dir = SENEROOT.'media/order/';
        if (!is_dir($save_dir)) {
            mkdir($save_dir);
        }

        //building pdf
        $tgl = $order->cdate;

        //new lines
        $this->seme_fpdf->Line(10, 23, 210-20, 23);

        //table origin start//
        $this->seme_fpdf->SetY(23);
        $this->seme_fpdf->SetFillColor(224, 235, 255);
        $this->seme_fpdf->SetTextColor(0);
        $this->seme_fpdf->SetFont('UHC', '', 8);
        $fill = false;

        $shipping = $order->addresses->shipping;

        // validation
        if (!isset($pickup->nama)) {
            $pickup->nama = '';
        }
        if (!isset($pickup->telp)) {
            $pickup->telp = '';
        }
        if (!isset($pickup->penerima_nama)) {
            $pickup->penerima_nama = '';
        }
        if (!isset($pickup->penerima_telp)) {
            $pickup->penerima_telp = '';
        }
        // by Muhammad Sofi - 11 November 2021 10:07
        // if (!isset($pickup->alamat)) {
        //     $pickup->alamat = '';
        // }
        if (!isset($pickup->alamat2)) {
            $pickup->alamat2 = '';
        }
        if (!isset($pickup->kodepos)) {
            $pickup->kodepos = '';
        }
        if (!isset($pickup->address_notes)) {
            $pickup->address_notes = '';
        }
        if (!isset($pickup->catatan)) {
            $pickup->catatan = '';
        }

        //By Donny Dennison - 03-07-2020
        //remove alamat from pdf
        //check if shipping address are same
        // if (strtolower($shipping->alamat) == strtolower($shipping->alamat2)) {
        //     $shipping->alamat = '';
        // }
        // if (strtolower($pickup->alamat) == strtolower($pickup->alamat2)) {
        //     $pickup->alamat = '';
        // }

        //kirim alamat (shipping)

        // by Donny Dennison - 3 November 2021 10:00
        // remark code
        // $ka = $this->__addressStructureFixer($shipping->alamat,$shipping->alamat2,$shipping->address_notes,$shipping->negara,$shipping->kodepos);
        $ka = $this->__addressStructureFixer($shipping->alamat2,$shipping->address_notes,$shipping->negara,$shipping->kodepos);


        //pickup

        // by Donny Dennison - 3 November 2021 10:00
        // remark code
        // $pa = $this->__addressStructureFixer($pickup->alamat,$pickup->alamat2,$pickup->address_notes,$pickup->negara,$pickup->kodepos);
        $pa = $this->__addressStructureFixer($pickup->alamat2,$pickup->address_notes,$pickup->negara,$pickup->kodepos);

        $w = array(20,70,20,70); //for width columns, must equal with header array
        $this->seme_fpdf->SetFont('UHC', '', 8);
        $this->seme_fpdf->Cell($w[0], 5, 'Consignee', 'BL', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[1], 5, iconv('UTF-8','UHC',$shipping->nama), 'BR', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[2], 5, 'Shipper', 'BL', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[3], 5, iconv('UTF-8','UHC',$pickup->penerima_nama), 'BR', 0, 'L', $fill);
        $this->seme_fpdf->Ln();

        $this->seme_fpdf->Cell($w[0], 5, 'Tel No.', 'BL', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[1], 5, $shipping->telp, 'BR', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[0], 5, 'Tel No.', 'BL', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[1], 5, ($pickup->penerima_telp), 'BR', 0, 'L', $fill);
        $this->seme_fpdf->Ln();

        $this->seme_fpdf->SetFont('UHC', '', 8);
        $this->seme_fpdf->Cell($w[0], 5, 'Address', 'L', 0, 'L', $fill);
        $this->seme_fpdf->SetFont('UHC', '', 6);
        $this->seme_fpdf->Cell($w[1], 5, $ka[0], 'R', 0, 'L', $fill);
        $this->seme_fpdf->SetFont('UHC', '', 8);
        $this->seme_fpdf->Cell($w[0], 5, 'Address', 'L', 0, 'L', $fill);
        $this->seme_fpdf->SetFont('UHC', '', 6);
        $this->seme_fpdf->Cell($w[1], 5, $pa[0], 'R', 0, 'L', $fill);
        $this->seme_fpdf->Ln();
        $this->seme_fpdf->Cell($w[0], 5, '', 'L', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[1], 5, $ka[1], 'R', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[0], 5, '', 'L', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[1], 5, $pa[1], 'R', 0, 'L', $fill);
        $this->seme_fpdf->Ln();
        $this->seme_fpdf->Cell($w[0], 5, '', 'L', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[1], 5, $ka[2], 'R', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[0], 5, '', 'L', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[1], 5, $pa[2], 'R', 0, 'L', $fill);
        $this->seme_fpdf->Ln();
        $this->seme_fpdf->Cell($w[0], 5, '', 'L', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[1], 5, $ka[3], 'R', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[0], 5, '', 'L', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[1], 5, $pa[3], 'R', 0, 'L', $fill);
        //-----------//
        //table end//

        //table start//
        $this->seme_fpdf->SetFont('UHC', '', 8);
        $w = array(30, 30, 30,45,45); //for width columns, must equal with header array
        $this->seme_fpdf->Ln();
        $this->seme_fpdf->Cell($w[0], 6, 'Postal Code', 'TLR', 0, 'C', $fill);
        $this->seme_fpdf->Cell($w[1], 6, 'Area', 'TLR', 0, 'C', $fill);
        $this->seme_fpdf->Cell($w[2], 6, 'Destination', 'TLR', 0, 'C', $fill);
        $this->seme_fpdf->Cell($w[3], 6, 'Registered date', 'TLR', 0, 'C', $fill);
        $this->seme_fpdf->Cell($w[4], 6, 'Departure', 'TLR', 0, 'C', $fill);
        $this->seme_fpdf->Ln();
        $this->seme_fpdf->SetFont('UHC', '', 12);
        $this->seme_fpdf->Cell($w[0], 6, $shipping->kodepos, 'BLR', 0, 'C', $fill);
        $this->seme_fpdf->Cell($w[1], 6, $area, 'BLR', 0, 'C', $fill);
        $this->seme_fpdf->Cell($w[2], 6, $shipping->negara, 'BLR', 0, 'C', $fill);
        $this->seme_fpdf->Cell($w[3], 6, date("d-M-y", strtotime($order->cdate)), 'BLR', 0, 'C', $fill);
        $this->seme_fpdf->Cell($w[4], 6, $shipping->negara, 'BLR', 0, 'C', $fill);
        $this->seme_fpdf->Ln();
        //table end//

        // tabel memo
        $w = array(180); //for width columns, must equal with header array
        $this->seme_fpdf->SetFont('UHC', '', 8);
        $this->seme_fpdf->Cell(array_sum($w), 6, 'Memo :'.' Pls pack the item well n deliver them securely to prevent damage. thanks.', 'TLR', 0, 'L', $fill);
        $this->seme_fpdf->Ln();
        //table end//

        // tabel memo
        $w = array(180); //for width columns, must equal with header array
        $this->seme_fpdf->Cell(array_sum($w), 6, 'Item Description'.'       Packing no :'.$order->shipment_tranid.'   Invoice no :'.$order->invoice_code.'', 'TLRB', 0, 'L', $fill);
        $this->seme_fpdf->Ln();
        //table end//

        $this->seme_fpdf->SetFont('UHC', '', 6);

        //get items data
        $items = $this->dodim->getByOrderDetailId($nation_code, $d_order_id, $c_produk_id);
        $items_count = count($items);

        //table start//
        //-----------//
        //table header//
        $w = array(80); //for width columns, must equal with header array
        $this->seme_fpdf->SetFillColor(255, 255, 255);
        $this->seme_fpdf->SetTextColor(0, 0, 0);
        $this->seme_fpdf->SetDrawColor(0, 0, 0);
        $this->seme_fpdf->SetLineWidth(.1);

        //table Data
        $this->seme_fpdf->SetFillColor(255, 255, 255);
        $this->seme_fpdf->SetTextColor(0);

        // Data
        $fill = false;
        $total_item = 0;
        $total_qty = 0;
        $total_berat = 0;
        $order->berat = round($order->berat, 1);
        $order->qty = (int) $order->qty;

        //item list
        $i = 0;
        foreach ($items as $item) {
            $total_qty += $item->qty;
            $total_berat += ($item->berat*$item->qty);
            $this->seme_fpdf->Cell(20, 5, 'Qty. '.$item->qty.' ea', 1, 0, 'C', true);
            $this->seme_fpdf->Cell(60, 5, '', 1, 0, 'C', true);
            $this->seme_fpdf->Ln();
            $this->seme_fpdf->Cell($w[0], 5, html_entity_decode($item->nama,ENT_QUOTES), '1', 0, 'L', $fill);
            //$this->seme_fpdf->Cell($w[1],6,number_format($item->qty,0,',','.').' '.$item->satuan,'LR',0,'C',$fill);
            //$this->seme_fpdf->Cell($w[2],6,''.number_format(($item->berat*$item->qty),1,',','.').' Kg','LR',0,'C',$fill);
            $this->seme_fpdf->Ln();
            $fill = !$fill;
            $i++;
        }
        if (empty($i)) {
            $fill =true;
            $this->seme_fpdf->Cell(array_sum($w), 5, 'no product', 'LR', 0, 'L', $fill);
            $this->seme_fpdf->Ln();
        }
        // Closing line
        $this->seme_fpdf->Cell(array_sum($w), 0, '', 'T');
        $this->seme_fpdf->Ln();

        //table footer
        $this->seme_fpdf->SetFillColor(89, 89, 89);
        $this->seme_fpdf->SetTextColor(255);
        $this->seme_fpdf->SetDrawColor(0, 0, 0);
        $this->seme_fpdf->SetLineWidth(.1);


        $this->seme_fpdf->Ln();

        //add some new line
        $this->seme_fpdf->Ln();
        $this->seme_fpdf->Ln();

        //table footer
        $this->seme_fpdf->SetFillColor(89, 89, 89);
        $this->seme_fpdf->SetTextColor(255);
        $this->seme_fpdf->SetDrawColor(0, 0, 0);
        $this->seme_fpdf->SetLineWidth(.1);
        $this->seme_fpdf->SetFont('UHC', '');
        //-----------//
        //table end//
        // $tracking_no = $order->shipment_tranid;
        if (strlen($tracking_no)<=0) {
            $tracking_no="-";
        }
        $tracking_no_file = $this->__slugify($tracking_no);

        //generate QRCode
        $this->lib("seme_qrcode");
        $this->seme_qrcode->root("");
        $this->seme_qrcode->media($save_dir);
        $pngurl = $this->seme_qrcode->write(strval($tracking_no), strtolower($tracking_no_file), "png");
        if (file_exists($pngurl) && is_file($pngurl)) {
            $this->seme_fpdf->Image($pngurl, 133, 85, 20, 20);
        }

        //barcode
        $this->seme_fpdf->SetFont('times', '', 8);
        $this->seme_fpdf->SetTextColor(0, 0, 0);
        $this->seme_fpdf->Code39(117, 106, $tracking_no.'', 0.8, 8, 'C');
        $this->seme_fpdf->Ln();
        $this->seme_fpdf->setXY(110, 117);
        $this->seme_fpdf->SetFont('Arial', 'B', 8);
        $this->seme_fpdf->Cell(65, 6, $tracking_no, 0, 2, 'C', false);


        $save_file = "waybill-".$d_order_id."-".$c_produk_id;
        $file_pdf = $save_dir.'/'.$save_file.'.pdf';
        if (file_exists($file_pdf) && is_file($file_pdf)) {
            unlink($file_pdf);
        }
        $this->seme_fpdf->Output('F', $file_pdf);
        //$this->seme_fpdf->Output('I', $file_pdf);
        sleep(1);
        //die();

        //by Donny Dennison - 13-07-2020 13:54
        //disable send api to qxpress
        //email sender
        // if ($this->email_send) {
        //     $replacer = array();
        //     $replacer['site_name'] = $this->app_name;
        //     $replacer['fnama'] = $pelanggan->fnama;
        //     $this->seme_email->flush();
        //     $this->seme_email->replyto($this->site_name, $this->site_replyto);
        //     $this->seme_email->from($this->site_email, $this->site_name);
        //     $this->seme_email->subject("WayBill for $d_order_id - $c_produk_id");
        //     $this->seme_email->to($pelanggan->email, $pelanggan->fnama);
        //     $this->seme_email->attachment_add($file_pdf, $save_file.'.pdf');
        //     $this->seme_email->template('waybill');
        //     $this->seme_email->replacer($replacer);
        //     $this->seme_email->send();
        //     if ($this->is_log) {
        //         $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::print --sendEmailWB: $pelanggan->email");
        //     }
        // }

        //if(file_exists($save_dir.'/'.$save_file.'.pdf')) unlink($save_dir.'/'.$save_file.'.pdf');
        $this->__forceDownload($file_pdf);
    }

    public function area($kodepos)
    {
        $res = $this->__getQXpressArea($kodepos);
        header("Content-Type: text/xml");
        echo $res;
    }

    /**
     * Test get pickup address
     * @param  [type] $nation_code
     * @param  [type] $d_order_id  ID from table d_order_detail
     * @param  [type] $c_produk_id ID from table d_order_detail
     */
    public function testGetPickupAddress($nation_code, $d_order_id, $c_produk_id)
    {
        //$order = $this->dodm->getById($nation_code, $d_order_id, $c_produk_id);
        $pickup = $this->dodpum->getById($nation_code, $d_order_id, $c_produk_id);
        $this->status = 200;
        $this->message = "Success";
        $this->__json_out(array("pickup"=>$pickup));
    }
}
