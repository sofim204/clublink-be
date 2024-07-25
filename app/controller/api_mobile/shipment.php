<?php
/**
 * Determine shipment cost for order
 */
class Shipment extends JI_Controller
{
    public $negara = 'SG';
    public $is_log = 1;
    public $i = 0;
    public $is_google_distance = 1;

    //by Donny Dennison - 23 september 2020 15:42
    //add direct delivery feature
    //copy from controller/api_mobile/cart
    public $qx_weight_limit = 30; //qxpress weight limit
    public $qx_side_limit = 300; //total side limit
    public $qx_long_limit = 150; //limit long in cm
    public $qx_width_limit = 150; //limit width / breadth in cm
    public $qx_height_limit = 150; //limit height in cm

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_mobile/a_negara_model", 'anm');
        $this->load("api_mobile/b_user_model", 'bu');
        $this->load("api_mobile/b_user_alamat_model", 'buam');
        $this->load("api_mobile/common_code_model", 'ccm');
        $this->load("api_mobile/c_produk_model", "cpm");
        $this->load("api_mobile/d_cart_model", "cart");
        $this->load("api_mobile/d_order_model", "order");
        $this->load("api_mobile/d_order_alamat_model", "doam");
        $this->load("api_mobile/d_order_detail_model", "dodm");
        $this->load("api_mobile/d_order_detail_item_model", "dodim");
        $this->load("api_mobile/qxpress_basic_model", 'qxbc');
        $this->load("api_mobile/qxpress_sameday_model", 'qxsd');
        $this->load("api_mobile/qxpress_volume_model", 'qxvl');
    }
    /**
    * Shipment object container for default result
    * @return object
    */
    private function __calObj()
    {
        $cOb = new stdClass();
        $cOb->courier_services = "qxpress";
        $cOb->shipment_service = "qxpress";
        $cOb->shipment_vehicle = "regular";
        $cOb->shipment_type = "next day";
        $cOb->shipment_cost_add = 0;
        $cOb->shipment_cost = 0;
        $cOb->total_panjang = 0;
        $cOb->total_tinggi = 0;
        $cOb->total_lebar = 0;
        $cOb->total_berat = 0;
        return $cOb;
    }
    /**
    * Get and decide the vehicle types for gogovan shipments
    *
    * @param object $order_detail_data object from d_order_detail table
    * @return string
    */
    private function __determineVehicleType($order_detail_data)
    {
        $shipment_vehicle = 'Regular';

        //by Donny Dennison - 15 september 2020 17:45
        //change name, image, etc from gogovan to gogox
        // if ($order_detail_data->shipment_service == 'gogovan') {
        if ($order_detail_data->shipment_service == 'gogox') {

            if ($order_detail_data->total_panjang<=240 && $order_detail_data->total_lebar<=150 && $order_detail_data->total_tinggi<=120 && $order_detail_data->total_berat<=900) {
                $shipment_vehicle = 'van';
            }elseif ($order_detail_data->total_panjang<=300 && $order_detail_data->total_lebar<=150 && $order_detail_data->total_tinggi<=180 && $order_detail_data->total_berat<=1500) {
                $shipment_vehicle = 'lorry10';
            } elseif ($order_detail_data->total_panjang<=400 && $order_detail_data->total_lebar<=180 && $order_detail_data->total_tinggi<=200 && $order_detail_data->total_berat<=2500) {
                $shipment_vehicle = 'lorry14';
            } else {
                $shipment_vehicle = 'lorry24';
            }
        }
        return $shipment_vehicle;
    }

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
    private function __getGogovanAccessToken()
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

    /**
    * Get gogovan shipment rates
    *
    * @param object $pickup adress object
    * @param object $destination adress object
    * @param string $vehicle_type vehicle types van, lorry10, lorry14, lorry24
    * @return string|empty
    */
    private function __getGogoVanVehicle($pickup, $destination, $vehicle_type)
    {
        
        //by Donny Dennison - 8 September 2020 15:09
        //change api gogovan to new version (gogovan change name to gogox)
        $accessTokenGogox = json_decode($this->__getGogovanAccessToken());

        $ch = curl_init();
        
        //by Donny Dennison - 8 September 2020 15:09
        //change api gogovan to new version (gogovan change name to gogox)
        // curl_setopt($ch, CURLOPT_URL, $this->gv_api_host.'api/v0/orders/price.json');
        if($this->gv_env_new == 'production'){

            curl_setopt($ch, CURLOPT_URL, $this->gv_api_host_new.'transport/quotations'); //for production

        }else{

            curl_setopt($ch, CURLOPT_URL, $this->gv_api_host_new.'api/v2/quotations'); //for staging

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

        //by Donny Dennison - 8 September 2020 15:09
        //change api gogovan to new version (gogovan change name to gogox)
        // $dt = new DateTime();

        //by Donny Dennison - 8 September 2020 15:09
        //change api gogovan to new version (gogovan change name to gogox)
    //     $data = array(
    //   'order[name]' => $destination->penerima_nama,
    //   'order[phone_number]' => $destination->penerima_telp,
    //   'order[pickup_time]' => $dt->format('Y-m-d H:i:s'),
    //   'order[service_type]' => 'delivery',
    //   'order[vehicle]' => $vehicle_type,
    //   'order[title_prefix]' => 'SellOn',
    //   'order[extra_requirements][express_service]' => 'true',
    //   'order[extra_requirements][remark]' => $destination->address_notes,
    //   'order[locations]' => '[["'.$pickup->latitude.'", "'.$pickup->longitude.'","'.$pickup->alamat1.' '.$pickup->alamat2.', '.$pickup->kabkota.'"],
    //         ["'.$destination->latitude.'", "'.$destination->longitude.'","'.$destination->alamat.' '.$destination->alamat2.', '.$destination->kabkota.'"]]'
    // );
        $data = array(
            'vehicle_type' => $vehicle_type,
            'pickup' => array(
                'schedule_at' => strtotime('+2 hour'),
                'location' => array(
                    'lat' => $pickup->latitude,
                    'lng' => $pickup->longitude
                )
            ),
            'destinations' => array( array(
                'location' => array(
                    'lat' => $destination->latitude,
                    'lng' => $destination->longitude
                )
            ) )
        );

        //by Donny Dennison - 8 September 2020 15:09
        //change api gogovan to new version (gogovan change name to gogox)
        // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        //if($this->is_log) $this->seme_log->write("api_mobile", "Gogovan POST: ".json_encode($data));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->seme_log->write("api_mobile", "__getGogoVanVehicle -> ".curl_error($ch));
            return 0;
            //echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        if($this->is_log){
          $this->seme_log->write("api_mobile", 'API_Mobile/Shipment::__getGogoVanVehicle:: -- cUrlHeader: '.json_encode($headers));
          $this->seme_log->write("api_mobile", 'API_Mobile/Shipment::__getGogoVanVehicle:: -- cUrlPOST: '.json_encode($data));
        }
        
        return $result;
    }

    /**
     * Get distance from Google API distance matrix
     *
     * @param string $origin contain about latitude and longitude separated by commas
     * @param string $destination contain about latitude and longitude separated by commas
     * @return object
     */
    private function __getDistance($origin, $destination)
    {
        $ch = curl_init();
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json";
        $options = array(
        "origins" => $origin,
        "destinations" => $destination,
        "key" => $this->google_distance_token
        );
        $request = $url . "?" . http_build_query($options);
        curl_setopt($ch, CURLOPT_URL, $request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /*
    * Calculating Shipment Cost
    */
    private function __hitungOngkir($xz, $pickup, $destination, $d_order_id, $d_order_detail_id)
    {
        if (!is_object($xz)) {
            $xz = new stdClass();
            $xz->shipment_cost = 0;
            $xz->shipment_cost_add = 0;
            return $xz;
        }
        if (!isset($xz->shipment_type)) {
            $xz->shipment_type = 'next day';
        }
        if (!isset($xz->shipment_service)) {

            //by Donny Dennison - 15 september 2020 17:45
            //change name, image, etc from gogovan to gogox
            // $xz->shipment_service = 'gogovan';
            $xz->shipment_service = 'gogox';

            if (isset($xz->courier_services)) {
                $xz->shipment_service = $xz->courier_services;
            }
        }
        if (!isset($xz->shipment_vehicle)) {
            $xz->shipment_vehicle = 'regular';
            if (isset($xz->vehicle_types)) {
                $xz->shipment_vehicle = $xz->vehicle_types;
            }
        }
        $nation_code = $pickup->nation_code;
        $xz->courier_services = $xz->shipment_service;
        $xz->vehicle_types = $xz->shipment_vehicle;
        $xz->services_duration = $xz->shipment_type;
        $shipment_type = $xz->shipment_type;
        if ($shipment_type != 'next day' && $shipment_type != 'same day') {
            $xz->shipment_type = 'next day';
            $shipment_type = $xz->shipment_type;
        }

        //by Donny Dennison - 15 september 2020 17:45
        //change name, image, etc from gogovan to gogox
        // if (strtolower($xz->courier_services) == 'gogovan') {
        if (strtolower($xz->courier_services) == 'gogox') {

            $xz->delivery_date = date("Y-m-d H:i:00", strtotime("+1 day"));
            //get shipping rates from gogovan API.
            $raw = '{}';
            $vehicle_type = strtolower($xz->vehicle_types);
            switch ($vehicle_type) {
                case "lorry 24 ft":
                    $vehicle_type = 'lorry24';
                    break;
                case "lorry 14 ft":
                    $vehicle_type = 'lorry14';
                    break;
                case "lorry 10 ft":
                    $vehicle_type = 'lorry10';
                    break;
                case "van":
                    $vehicle_type = 'van';
                    break;
                default:
                    $vehicle_type = 'van';
            }
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__hitungOngkir -> Gogovan vehicle type: ".$vehicle_type);
            }

            //by Donny Dennison - 15 september 2020 17:45
            //change name, image, etc from gogovan to gogox
            // $xz->shipment_icon = $this->cdn_url("assets/images/gogovan.png");
            $xz->shipment_icon = $this->cdn_url("assets/images/gogox.png");

            //call gogovan API
            $raw = $this->__getGogoVanVehicle($pickup, $destination, $vehicle_type);
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__hitungOngkir -> Gogovan ".$shipment_type.": ".$raw);
            }

            $result = json_decode($raw);
            $this->status = 200;
            
            //by Donny Dennison - 8 September 2020 15:09
            //change api gogovan to new version (gogovan change name to gogox)
            // if (isset($result->breakdown->fee->value)) {
            if (isset($result->estimated_price->amount)) {

                $this->status = 200;
                $this->message = 'Success';
                $xz->shipment_vehicle = $vehicle_type;

                //by Donny Dennison - 15 september 2020 17:45
                //change name, image, etc from gogovan to gogox
                // $xz->shipment_service = 'Gogovan';
                $xz->shipment_service = 'Gogox';
                
                //by Donny Dennison - 8 September 2020 15:09
                //change api gogovan to new version (gogovan change name to gogox)
                // $xz->shipment_cost = $result->breakdown->fee->value;
                $xz->shipment_cost = number_format(substr_replace($result->estimated_price->amount,'.',-2,0),2,".","");

                $xz->shipment_cost_add = 0;
                if ($this->is_log) {

                    //by Donny Dennison - 8 September 2020 15:09
                    //change api gogovan to new version (gogovan change name to gogox)
                    // $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__hitungOngkir -> Gogovan -> shipment_cost: ".$result->breakdown->fee->value);
                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__hitungOngkir -> Gogovan -> shipment_cost: ".$result->estimated_price->amount);

                }
                if (strtolower($xz->services_duration) == 'same day' || strtolower($xz->services_duration) == 'sameday') {
                    // $xz->shipment_cost = $xz->shipment_cost+$result->breakdown->express_service->value;
                    // if ($this->is_log) {
                    //     $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__hitungOngkir -> Gogovan Same Day -> shipment_cost_add: ".$result->breakdown->express_service->value);
                    // }

                    /*
                     * Disabled Same Day Googvan
                     */
                    $this->status = 3158;

                    //by Donny Dennison - 15 september 2020 17:45
                    //change name, image, etc from gogovan to gogox
                    // $this->message = 'Sorry, Gogovan Same Day (Express Service) not available now';
                    $this->message = 'Sorry, Gogox Same Day (Express Service) not available now';

                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> Gogovan APIERROR: $this->message");
                    }
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "shipment");
                    die();
                }

			//by Donny Dennison - 10 august 2020 14:57
			//if latitude or longitude is empty or 0 then set delivery fee to $39
            //START by Donny Dennison - 10 august 2020 14:57

            // }
            } else if($pickup->latitude == 0 || $pickup->longitude == 0 || $destination->latitude == 0 || $destination->longitude == 0){
            	
            	$this->status = 200;
                $this->message = 'Success';
                $xz->shipment_vehicle = $vehicle_type;

                //by Donny Dennison - 15 september 2020 17:45
                //change name, image, etc from gogovan to gogox
                // $xz->shipment_service = 'Gogovan';
                $xz->shipment_service = 'Gogox';

                $xz->shipment_cost = 39;
                $xz->shipment_cost_add = 0;
                if ($this->is_log) {

                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__hitungOngkir -> Gogovan -> shipment_cost: 39");

                }
                if (strtolower($xz->services_duration) == 'same day' || strtolower($xz->services_duration) == 'sameday') {

                    /*
                     * Disabled Same Day Googvan
                     */
                    $this->status = 3158;
                    
                    //by Donny Dennison - 15 september 2020 17:45
                    //change name, image, etc from gogovan to gogox
                    // $this->message = 'Sorry, Gogovan Same Day (Express Service) not available now'
                    $this->message = 'Sorry, Gogox Same Day (Express Service) not available now'
                    ;
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> Gogovan APIERROR: $this->message");
                    }
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "shipment");
                    die();
                }
            	
            }

            //END by Donny Dennison - 10 august 2020 14:57

            if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__hitungOngkir -> __getGogoVanVehicle: $raw");

        } elseif (strtolower($shipment_type) == 'next day' && strtolower($xz->courier_services) == 'qxpress') {
            $this->status = 200;
            $dimension_max = $xz->total_panjang + $xz->total_lebar + $xz->total_tinggi;
            $xz->delivery_date = 'NULL';
            $service = $this->qxbc->getByWeight($nation_code, $xz->total_berat);
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__hitungOngkir -> call QXpress Next Day: ".json_encode($service));
            }
            $xz->shipment_cost_add = 0.0;
            if (isset($service->cost)) {
                $add_cost = 0.0;
                $this->status = 200;
                $this->message = 'Success';
                $add = $this->qxvl->getByDimension($nation_code, $dimension_max);
                if (isset($add->cost)) {
                    $add_cost = $add->cost;
                }
                $xz->shipment_cost = round($service->cost, 2);
                $xz->shipment_icon = $this->cdn_url("assets/images/qxpress.png");
                if (!empty($xz->is_fashion)) {
                    $xz->shipment_cost = round($service->cost, 2);
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__hitungOngkir -> QXpress Next Day -> Fashion: TRUE, addtional cost eliminated");
                    }
                } else {
                    $xz->shipment_cost = round($service->cost+$add_cost, 2);
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__hitungOngkir -> API RESULT: shipment_cost: ".$service->cost." shipment_cost_add: ".$add_cost." total shipment_cost: ".$xz->shipment_cost);
                    }
                }
                $xz->shipment_cost_add = 0;
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__hitungOngkir -> ASSIGNED: shipment_cost: ".$xz->shipment_cost.", shipment_cost_add: ".$xz->shipment_cost_add);
                }
            }
        } elseif (strtolower($shipment_type) == 'same day' && strtolower($xz->courier_services) == 'qxpress') {
            $this->status = 200;
            $distance = 0;
            $origin = $pickup->latitude.','.$pickup->longitude;
            $dest = $destination->latitude.','.$destination->longitude;
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__hitungOngkir -> --originLL: $origin, --destinationLL: $dest");
            }
            if ($this->is_google_distance) {
                $rd = $this->__getDistance($origin, $dest);
                $jd = json_decode($rd);
                if (isset($jd->rows[0]->elements[0]->distance->value)) {
                    $distance = $jd->rows[0]->elements[0]->distance->value;
                }
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> using Google API distance");
                }
            } else {
                $distance = $this->__distanceSphere($pickup->latitude, $pickup->longitude, $destination->latitude, $destination->longitude);
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> using Spherical distance");
                }
            }
            if ($distance>0) {
                $res = $this->dodm->update($nation_code, $d_order_id, $d_order_detail_id, array("shipment_distance"=>$distance));
                if ($res) {
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__hitungOngkir -> Distance: $distance m save to DB");
                    }
                }
            }
            $service = $this->qxsd->getByDistance($nation_code, $xz->total_berat, $distance);
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__hitungOngkir -> call qxpress same day. Distance: $distance");
            }
            if (isset($service->cost)) {
                $this->status = 200;
                $this->message = 'Success';
                $xz->shipment_cost = round($service->cost, 2);
                $xz->shipment_cost_add = 0.0;
            } else {
                //cannot get cost for qxpress same day
                //$this->status = 3079;
                //$this->message = 'Cannot get cost for qxpress same day';
            }
            $xz->shipment_icon = $this->cdn_url("assets/images/qxpress.png");
        } else {
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/shipment::__hitungOngkir -> Courier Service: $xz->courier_services, Shipment Type: $shipment_type");
            }
            $this->status = 3099;
            $this->message = 'Shipment service unavailable for this order';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/shipment::__hitungOngkir -> ERROR: ".$this->message);
            }
        }
        return $xz;
    }

    /**
    * Calculating dimension per item product for updating $proitem
    *
    * @param object $proitem product item object before
    * @param object $proproduk product item object current
    * @return object product item object
    */
    private function __dimensionCalculation($proitem, $proproduk)
    {
        $p = round($proproduk->panjang, 0);
        $l = round($proproduk->lebar, 0);
        $t = round($proproduk->tinggi, 0);
        $v = $p*$l*$t;
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__dimensionCalculation -> qty: ".$proproduk->qty);
        }
        if ($proproduk->qty>1) {
            for ($ia=1;$ia<=$proproduk->qty;$ia++) {
                $min = min($p, $l, $t);
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__dimensionCalculation -> iteration: $ia");
                }
                if ($min == $t && $min!=$l && $min!=$p) {
                    $t += $t;
                } elseif ($min != $t && $min==$l && $min!=$p) {
                    $l += $l;
                } elseif ($min != $t && $min!=$l && $min==$p) {
                    $p += $p;
                } elseif ($min == $t && $min==$l && $min!=$p) {
                    $t += $t;
                } elseif ($min != $t && $min==$l && $min==$p) {
                    $l += $l;
                } elseif ($min == $t && $min!=$l && $min==$p) {
                    $t += $t;
                } elseif ($min == $t && $min==$l && $min==$p) {
                    $t += $t;
                } else {
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__dimensionCalculation -> Something wrong on $ia");
                    }
                }
                $v = $p*$l*$t; //new volume
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__dimensionCalculation -> $ia. new dimension: $p x $l x $l");
                }
            }
        }

        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__dimensionCalculation -> --dimensionCalc: End");
        }
        $tp = $proitem->total_panjang;
        $tl = $proitem->total_lebar;
        $tt = $proitem->total_tinggi;
        if ($this->i==0) {
            $proitem->total_panjang = $p;
            $proitem->total_lebar = $l;
            $proitem->total_tinggi = $t;
        }
        $tv = $tp*$tl*$tt;
        $min1 = min($p, $l, $t);
        $mit1 = '';
        $min2 = min($tp, $tl, $tt);
        $mit2 = '';
        if ($v>$tv) {
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__dimensionCalculation -> current volume greater than last volume");
            }

            //box 1
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__dimensionCalculation -> get minimum side of box 1");
            }
            if ($min1 == $t && $min1!=$l && $min1!=$p) {
                $mit1 = 't';
            } elseif ($min1 != $t && $min1==$l && $min1!=$p) {
                $mit1 = 'l';
            } elseif ($min1 != $t && $min1!=$l && $min1==$p) {
                $mit1 = 'p';
            } elseif ($min1 == $t && $min1==$l && $min1!=$p) {
                $mit1 = 't';
            } elseif ($min1 != $t && $min1==$l && $min1==$p) {
                $mit1 = 'l';
            } elseif ($min1 == $t && $min1!=$l && $min1==$p) {
                $mit1 = 't';
            } elseif ($min1 == $t && $min1==$l && $min1==$p) {
                $mit1 = 't';
            }
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__dimensionCalculation -> get minimum side of box 1 is: ".$mit1);
            }

            //box 2
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__dimensionCalculation -> get minimum side of box 1");
            }
            if ($min2 == $t && $min2!=$l && $min2!=$p) {
                $mit2 = 't';
            } elseif ($min2 != $tt && $min2==$tl && $min2!=$tp) {
                $mit2 = 'l';
            } elseif ($min2 != $tt && $min2!=$tl && $min2==$tp) {
                $mit2 = 'p';
            } elseif ($min2 == $tt && $min2==$tl && $min2!=$tp) {
                $mit2 = 't';
            } elseif ($min2 != $tt && $min2==$tl && $min2==$tp) {
                $mit2 = 'l';
            } elseif ($min2 == $tt && $min2!=$tl && $min2==$tp) {
                $mit2 = 't';
            } elseif ($min2 == $tt && $min2==$tl && $min2==$tp) {
                $mit2 = 't';
            }
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__dimensionCalculation -> get minimum side of box 2 is: ".$mit2);
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
            $proitem->total_panjang = $p;
            $proitem->total_lebar = $l;
            $proitem->total_tinggi = $t;
        } else {
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__dimensionCalculation -> last volume greater than current volume");
            }

            //box 1
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__dimensionCalculation -> get minimum side of box 1");
            }
            if ($min1 == $t && $min1!=$l && $min1!=$p) {
                $mit1 = 't';
            } elseif ($min1 != $t && $min1==$l && $min1!=$p) {
                $mit1 = 'l';
            } elseif ($min1 != $t && $min1!=$l && $min1==$p) {
                $mit1 = 'p';
            } elseif ($min1 == $t && $min1==$l && $min1!=$p) {
                $mit1 = 't';
            } elseif ($min1 != $t && $min1==$l && $min1==$p) {
                $mit1 = 'l';
            } elseif ($min1 == $t && $min1!=$l && $min1==$p) {
                $mit1 = 't';
            } elseif ($min1 == $t && $min1==$l && $min1==$p) {
                $mit1 = 't';
            }
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__dimensionCalculation -> get minimum side of box 1 is: ".$mit1);
            }

            //box 2
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__dimensionCalculation -> get minimum side of box 2");
            }
            if ($min2 == $t && $min2!=$l && $min2!=$p) {
                $mit2 = 't';
            } elseif ($min2 != $tt && $min2==$tl && $min2!=$tp) {
                $mit2 = 'l';
            } elseif ($min2 != $tt && $min2!=$tl && $min2==$tp) {
                $mit2 = 'p';
            } elseif ($min2 == $tt && $min2==$tl && $min2!=$tp) {
                $mit2 = 't';
            } elseif ($min2 != $tt && $min2==$tl && $min2==$tp) {
                $mit2 = 'l';
            } elseif ($min2 == $tt && $min2!=$tl && $min2==$tp) {
                $mit2 = 't';
            } elseif ($min2 == $tt && $min2==$tl && $min2==$tp) {
                $mit2 = 't';
            }
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__dimensionCalculation -> get minimum side of box 2 is: ".$mit2);
            }

            if ($mit1 == 't' && $mit2 == 't') {
                $tt += $t;
            } elseif ($mit1 == 't' && $mit2 == 'l') {
                $tt += $l;
            } elseif ($mit1 == 't' && $mit2 == 'p') {
                $tt += $p;
            } elseif ($mit1 == 'l' && $mit2 == 't') {
                $tl += $t;
            } elseif ($mit1 == 'l' && $mit2 == 'l') {
                $tl += $l;
            } elseif ($mit1 == 'l' && $mit2 == 'p') {
                $tl += $p;
            } elseif ($mit1 == 'p' && $mit2 == 't') {
                $tp += $t;
            } elseif ($mit1 == 'p' && $mit2 == 'l') {
                $tp += $l;
            } elseif ($mit1 == 'p' && $mit2 == 'p') {
                $tp += $p;
            }
            $proitem->total_panjang = $tp;
            $proitem->total_lebar = $tl;
            $proitem->total_tinggi = $tt;
        }
        return $proitem;
    }

    /**
    * Calculating distance by using spherical distance
    *
    * @param string $lat1 latitude origin
    * @param string $long1 longitude origin
    * @param string $lat2 latitude destination
    * @param string $long2 longitude destination
    * @return float in meters
    */
    private function __distanceSphere($lat1, $long1, $lat2, $long2)
    {
        return
            6371 * 2 * asin(sqrt(
                pow(
                    sin(($lat1 - abs($lat2)) * pi()/180 / 2),
                    2
                ) + cos($lat1 * pi()/180) * cos(abs($lat2) *
        pi()/180) * pow(sin(($long1 - $long2) *
        pi()/180 / 2), 2)
            )); //in KM
    }

    public function index()
    {
        //initial
        $sr = new stdClass();
        $dt = $this->__init();
        $data['shipping_rates'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "shipment");
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
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "shipment");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "shipment");
            die();
        }
        //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index");
        //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index --POST: ".json_encode($_POST));

        //get order id
        $d_order_id = (int) $this->input->post('d_order_id');
        if ($d_order_id<=0) {
            $this->status = 3001;
            $this->message = 'Invalid Order ID';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index --forceClosed ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "shipment");
            die();
        }

        //get order
        $order = $this->order->getById($nation_code, $d_order_id);
        if (!isset($order->id)) {
            $this->status = 3002;
            $this->message = 'Data not found or deleted';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index --forceClosed ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "shipment");
            die();
        }
        if ($order->b_user_id != $pelanggan->id) {
            $this->status = 3003;
            $this->message = 'This order not found belong to you';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index --forceClosed ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "shipment");
            die();
        }

        //ini bukan c_produk_id tapi d_order_detail_id
        $c_produk_id = (int) $this->input->post("c_produk_id");
        if ($c_produk_id<=0) {
            $this->status = 3153;
            $this->message = 'Invalid Produk ID / d_order_detail_id';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index --forceClosed ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "shipment");
            die();
        }

        //get detail order (ordered product) from db
        $op = $this->dodm->getForShipment($nation_code, $d_order_id, $c_produk_id);
        if (!isset($op->id)) {
            $this->status = 3154;
            $this->message = 'Order with product ID not found';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index --forceClosed ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "shipment");
            die();
        }
        $d_order_id = $op->d_order_id;
        $d_order_detail_id = $op->id;
        //http_response_code(500);
        //$this->debug($ordered_product);
        //die();

        //get all product items

        //by Donny Dennison - 23 september 2020 15:42
        //add direct delivery feature
        $dd = 0; //flag untuk direct delivery

        $gv = 0; //flag untuk gogovan
        $gvfree = 0; //flag untuk gogovan free ongkir
        $qx = 0; // flag untuk qxpress
        $qxfree = 0; //flag untuk qx free ongkir
        $is_free_ongkir = 0; //flag free ongkir
        $freeongkir = array();
        $ongkir = array();
        $fashion = array();
        $nonfashion = array();
        $products = $this->dodim->getByOrderDetailIdForShipment($nation_code, $d_order_id, $d_order_detail_id);
        $products_count = count($products);

        //by Donny Dennison - 23 september 2020 15:42
        //add direct delivery feature
        $direct_delivery_promotion = 0;
        $direct_delivery = 'Buyer';

        //$this->debug($products);
        //die();
        $gvfi = array();
        $gvfn = array();
        if ($products_count>0) {
            foreach ($products as &$pro) {
                $pro->shipment_service = $op->shipment_service;
                if (!empty($pro->is_include_delivery_cost)) {
                    $freeongkir[] = $pro;
                    $is_free_ongkir = 1;
                }

                //by Donny Dennison - 23 september 2020 15:42
                //add direct delivery feature
                if (strtolower($pro->courier_services) == "direct delivery") {

                    $dd++;

                //by Donny Dennison - 15 september 2020 17:45
                //change name, image, etc from gogovan to gogox
                // if (strtolower($pro->courier_services) == "gogovan") {

                // if (strtolower($pro->courier_services) == "gogox") {
                }else if (strtolower($pro->courier_services) == "gogox") {

                    $gv++;
                    if (!empty($pro->is_include_delivery_cost)) {
                        $gvfree++;
                        $gvfi[] = $pro;
                    } else {
                        $gvfn[] = $pro;
                    }
                } else {
                    $qx++;
                    if (!empty($pro->is_include_delivery_cost)) {
                        $qxfree++;
                    }
                }
            }
        }

        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/shipment::index -> GV Free: $gvfree");
        }
        //for checking big gogovan and small gogovan
        if ($gvfree>0 && (count($gvfn) >= count($gvfi))) {
            $gvfi2 = $this->__calObj();
            foreach ($gvfi as $v1) {
                $gvfi2 = $this->__dimensionCalculation($gvfi2, $v1);
                $gvfi2->total_berat += $v1->berat * $v1->qty;
            }
            $gvfn2 = $this->__calObj();
            foreach ($gvfn as $v2) {
                $gvfn2 = $this->__dimensionCalculation($gvfn2, $v2);
                $gvfn2->total_berat += $v2->berat * $v2->qty;
            }
            if (
                ($gvfn2->total_berat > $gvfi2->total_berat) ||
                ($gvfn2->total_panjang > $gvfi2->total_panjang) ||
                ($gvfn2->total_lebar > $gvfi2->total_lebar) ||
                ($gvfn2->total_tinggi > $gvfi2->total_tinggi)
            ) {
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/shipment::index -> GV not free");
                }
                $gvfree=0;
                $op->total_berat = $gvfi2->total_berat;
                $op->total_panjang = $gvfi2->total_panjang;
                $op->total_lebar = $gvfi2->total_lebar;
                $op->total_tinggi = $gvfi2->total_tinggi;
            } else {
                $gvfree = $products_count;
            }

            //by Donny Dennison - 15 september 2020 17:45
            //change name, image, etc from gogovan to gogox
            // $op->shipment_service = 'gogovan';
            $op->shipment_service = 'gogox';

        }

        //determine shipment vehicle type
        $op->shipment_vehicle = $this->__determineVehicleType($op);

        //substract shipment cost initial value
        $op->shipment_cost_sub = 0;

        //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index --begin");

        //seller
        $seller = $this->bu->getById($nation_code, $op->b_user_id_seller);

        //fill to shipping rates var
        $sr->c_produk_id = (int) $op->id;

        //get shipment type from input
        $shipment_type = strtolower($this->input->post("shipment_type"));
        if (empty($shipment_type)) {
            $shipment_type = $op->shipment_type;
        }
        //if empty again, using preset
        if (empty($shipment_type)) {
            $shipment_type = "Next Day";
        }
        if (strtolower($shipment_type) == 'nextday' || strtolower($shipment_type) == 'next day') {
            $shipment_type = "Next Day";
        }
        if (strtolower($shipment_type) == 'sameday' || strtolower($shipment_type) == 'same day') {
            $shipment_type = "Same Day";
        }

        //put back to product object;
        $op->services_duration = $shipment_type;

        //by Donny Dennison - 23 september 2020 15:42
        //add direct delivery feature
        //START by Donny Dennison - 23 september 2020 15:42

        $shipment_service = strtolower($this->input->post("shipment_service"));

        $flagUpdateDirectDeliveryBuyer = 0;
        $isiUpdateDirectDeliveryBuyer = 0;

        if($shipment_service == 'direct delivery' && strtolower($op->shipment_service) == 'direct delivery' && $op->is_direct_delivery_buyer == 1){

            $direct_delivery = 'Buyer';
            // by Muhammad Sofi - 1 November 2021 13:46
            // add description for shipment type
            $op->deskripsi = 'Please contact a seller through "Chat" after payment to pick up your product';

        }else if($shipment_service == 'direct delivery' && strtolower($op->shipment_service) != 'direct delivery' && $op->is_direct_delivery_buyer == 0){

            $op->shipment_service = 'Direct Delivery';
            $op->courier_services = 'Direct Delivery';
            $flagUpdateDirectDeliveryBuyer = 1;
            $isiUpdateDirectDeliveryBuyer = 1;
            $direct_delivery = 'Buyer';
            // by Muhammad Sofi - 1 November 2021 13:46
            // add description for shipment type
            $op->deskripsi = 'Please contact a seller through "Chat" after payment to pick up your product.';

        }else if($shipment_service != 'direct delivery' && $op->is_direct_delivery_buyer == 0){

            $direct_delivery = 'Buyer';
            // by Muhammad Sofi - 1 November 2021 13:46
            // add description for shipment type
            $op->deskripsi = 'Please contact a seller through "Chat" after payment to pick up your product';

        }else if($shipment_service != 'direct delivery' && $op->is_direct_delivery_buyer == 1){

            //find shipment_service again, gogox atau qxpress
            //START copy from controller/api_mobile/cart
            $k = $op->total_panjang+$op->total_lebar+$op->total_tinggi;
            if ( ($op->total_qty == 1 && $k>$this->qx_side_limit) || ($op->total_panjang>$this->qx_long_limit || $op->total_lebar>$this->qx_width_limit || $op->total_tinggi>$this->qx_height_limit)) {

                $shipment_service = 'gogox';

            } else if ($op->total_berat>$this->qx_weight_limit) {

                $shipment_service = 'gogox';

            }else{

                $shipment_service = 'qxpress';

            }
            //END copy from controller/api_mobile/cart
                                

            $op->shipment_service = $shipment_service;
            $op->courier_services = $shipment_service;
            $flagUpdateDirectDeliveryBuyer = 1;
            $isiUpdateDirectDeliveryBuyer = 0;
            $direct_delivery = 'Buyer';

        }else{

            $direct_delivery = 'Seller';
            // by Muhammad Sofi - 1 November 2021 13:46
            // add description for shipment type
            $op->deskripsi = 'This product will be delivered directly by the seller';

        }

        //END by Donny Dennison - 23 september 2020 15:42


        //logger
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> Key ID: $op->d_order_id-$op->id-$op->b_user_alamat_id");
        }
        //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> product name: $op->nama");
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> prefered shipment_service: $op->shipment_service");
        }
        //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> prefered shipment_type: $shipment_type");
        //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> prefered shipment_vehicle: $op->shipment_vehicle");
        //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> prefered vehicle_types: $op->vehicle_types");
        //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> product qty: $op->qty");
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> product berat: $op->berat -- product total berat: $op->total_berat");
        }
        //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> product dimension: $op->p, $op->l, $op->t");
        //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> free ongkir: $op->is_include_delivery_cost");

        //default icon
        $op->shipment_icon = $this->cdn_url("assets/images/unavailable.png");

        /*----
        start get pickup address and shipping address
        ----*/

        //get pickup address
        $pickup = $this->buam->getById($nation_code, $op->b_user_id_seller, $op->b_user_alamat_id);
        if (!isset($pickup->id)) {
            $this->status = 3164;
            $this->message = 'Pickup Address not set on order_detail: '.$op->d_order_id.' order_detail: '.$op->id.', cannot continue';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> ERROR: ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "shipment");
            die();
        }
        //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> PICKUP --userID: $op->b_user_id_seller, --userAlamatID: $op->b_user_alamat_id, --origin: $pickup->penerima_nama $pickup->alamat $pickup->alamat2 ");

        //declare shipping address code
        $classified = "address";

        //by Donny Dennison - 17 juni 2020 20:18
        // request by Mr Jackie change Shipping Address into Receiving Address
        // $codename = 'Shipping Address';
        $codename = 'Receiving Address';

        //get shipping address code
        $address_status = $this->ccm->getByClassifiedByCodeName($nation_code, $classified, $codename);
        if (!isset($address_status->code)) {
            $address_status = new stdClass();
            $address_status->code = 'A2';
        }

        //get shipping address
        $destination = $this->doam->getById($nation_code, $order->id, $address_status->code);
        if (!isset($destination->d_order_id)) {
            $this->status = 3152;
            $this->message = 'Please set shipping address first';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> ERROR: $this->message");
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "shipment");
            die();
        }
        if ($this->is_log) {
            // by Muhammad Sofi - 11 November 2021 11:30
            // $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> DESTINATION --userID: $pelanggan->id, --userAlamatID: $destination->b_user_alamat_id, --destination: $destination->penerima_nama $destination->alamat $destination->alamat2");
            $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> DESTINATION --userID: $pelanggan->id, --userAlamatID: $destination->b_user_alamat_id, --destination: $destination->penerima_nama $destination->alamat2");
        }

        /*----
        end get pickup address and shipping address
        ----*/

        $is_found=0;

        //by Donny Dennison - 23 september 2020 15:42
        //add direct delivery feature
        //START by Donny Dennison - 23 september 2020 15:42

        if (strtolower($op->courier_services) == 'direct delivery') {

            $op->delivery_date = 'NULL';
            $this->status = 200;
            $this->message = 'Success';
            $op->shipment_cost = round(0, 2);
            $op->shipment_icon = $this->cdn_url("assets/images/direct_delivery.png");
            $op->shipment_cost_add = 0;
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> QXpress Next Day -> shipment_cost: ".$op->shipment_cost.", shipment_cost_add: ".$op->shipment_cost_add);
            }

        //by Donny Dennison - 15 september 2020 17:45
        //change name, image, etc from gogovan to gogox
        // if (strtolower($op->courier_services) == 'gogovan') {
        
        // if (strtolower($op->courier_services) == 'gogox') {
        }else if (strtolower($op->courier_services) == 'gogox') {

        //END by Donny Dennison - 23 september 2020 15:42

            $op->delivery_date = date("Y-m-d H:i:00", strtotime("+1 day"));
            //get shipping rates from gogovan API.
            $raw = '{}';
            $vehicle_type = strtolower($op->vehicle_types);
            switch ($vehicle_type) {
                case "lorry 24 ft":
                    $vehicle_type = 'lorry24';
                    break;
                case "lorry 14 ft":
                    $vehicle_type = 'lorry14';
                    break;
                case "lorry 10 ft":
                    $vehicle_type = 'lorry10';
                    break;
                case "van":
                    $vehicle_type = 'van';
                    break;
                default:
                    $vehicle_type = 'van';
            }
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> Gogovan vehicle type: ".$vehicle_type);
            }

            //by Donny Dennison - 15 september 2020 17:45
            //change name, image, etc from gogovan to gogox
            // $op->shipment_icon = $this->cdn_url("assets/images/gogovan.png");
            $op->shipment_icon = $this->cdn_url("assets/images/gogox.png");

            // by Muhammad Sofi - 1 November 2021 13:46
            // add description for shipment type
            $op->deskripsi = "Delivery cost is only for transport. Contact drivers if manpower is needed";



            //by Donny Dennison 7 oktober 2020 - 14:10
            //add promotion face mask
            //START by Donny Dennison 7 oktober 2020 - 14:10

            //find the face mask product
            $promotion1 = 0;
            $promotion2 = 0;
            $totalProduct = count($products);
            foreach ($products as $key => $value) {
                
                if($value->c_produk_id == 1746 || $value->c_produk_id == 1752 || $value->c_produk_id == 1754){

                    //by Donny Dennison - 23 september 2020 15:42
                    //add direct delivery feature
                    $direct_delivery_promotion = 1;

                    if($value->c_produk_id == 1746){
                        $promotion1 = 1;
                        break;
                    }else if($value->c_produk_id == 1752 || $value->c_produk_id == 1754){
                        $promotion2 = 1;
                    }
                    
                }

            }

            if($promotion1 == 1){

                $this->status = 200;
                $this->message = 'Success';
                $op->shipment_vehicle = $vehicle_type;
                $op->shipment_service = 'Gogox';

                $op->shipment_cost = round(2, 2);

                $op->shipment_cost_add = 0;
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> Gogovan -> shipment_cost \$2 ");

            }else if($promotion2 == 1){


                $this->status = 200;
                $this->message = 'Success';
                $op->shipment_vehicle = $vehicle_type;
                $op->shipment_service = 'Gogox';

                $op->shipment_cost = round(0, 2);

                $op->shipment_cost_add = 0;
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> Gogovan -> shipment_cost \$0 ");

            }else{

	            // call gogovan API
	            $raw = $this->__getGogoVanVehicle($pickup, $destination, $vehicle_type);
	            if ($this->is_log) {
	                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> Gogovan ".$shipment_type." APIRESULT: ".$raw);
	            }

	            $result = json_decode($raw);
	            $this->status = 200;

	            //by Donny Dennison - 8 September 2020 15:09
	            //change api gogovan to new version (gogovan change name to gogox)
	            // if (isset($result->breakdown->fee->value)) {
	            if (isset($result->estimated_price->amount)) {

	                $this->status = 200;
	                $this->message = 'Success';
	                $op->shipment_vehicle = $vehicle_type;

	                //by Donny Dennison - 15 september 2020 17:45
	                //change name, image, etc from gogovan to gogox
	                // $op->shipment_service = 'Gogovan';
	                $op->shipment_service = 'Gogox';
	                
	                //by Donny Dennison - 8 September 2020 15:09
	                //change api gogovan to new version (gogovan change name to gogox)
	                //$op->shipment_cost = $result->breakdown->fee->value;
	                $op->shipment_cost = number_format(substr_replace($result->estimated_price->amount,'.',-2,0),2,".","");

	                $op->shipment_cost_add = 0;

	                if ($this->is_log) {

	                    //by Donny Dennison - 8 September 2020 15:09
	                    //change api gogovan to new version (gogovan change name to gogox)
	                    // $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> Gogovan -> shipment_cost: ".$result->breakdown->fee->value);
	                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> Gogovan -> shipment_cost: ".$result->estimated_price->amount);

	                }

	                if (strtolower($op->services_duration) == 'same day' || strtolower($op->services_duration) == 'sameday') {
	                    // /*
	                    //  * Enabled Same Day Googvan
	                    //  */
	                    // // $op->shipment_cost = $op->shipment_cost+$result->breakdown->express_service->value;
	                    // // if ($this->is_log) {
	                    // //     $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> Gogovan Same Day -> shipment_cost_add: ".$result->breakdown->express_service->value);
	                    // // }

	                    /*
	                     * Disabled Same Day Googvan
	                     */
	                    $this->status = 3158;

	                    //by Donny Dennison - 15 september 2020 17:45
	                    //change name, image, etc from gogovan to gogox
	                    // $this->message = 'Sorry, Gogovan Same Day (Express Service) not available now';
	                    $this->message = 'Sorry, Gogox Same Day (Express Service) not available now';

	                    if ($this->is_log) {
	                        $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> Gogovan APIERROR: $this->message");
	                    }
	                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "shipment");
	                    die();
	                }

	            //by Donny Dennison - 10 august 2020 14:57
				//if latitude or longitude is empty or 0 then set delivery fee to $39
	            //START by Donny Dennison - 10 august 2020 14:57

	            // } else {
	            } else if($pickup->latitude == 0 || $pickup->longitude == 0 || $destination->latitude == 0 || $destination->longitude == 0){
	                // $this->status = 3159;
	                   
	                //    // by Donny Dennison
	                //    // add 'be' in the text 
	                // //$this->message = 'Sorry, this delivery address cannot reached by Gogovan';
	                // $this->message = 'Sorry, this delivery address cannot be reached by Gogovan';
	                
	                // if ($this->is_log) {
	                //     $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> Gogovan APIERROR: $this->message");
	                // }
	                // $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "shipment");
	                // die();


	                $this->status = 200;
	                $this->message = 'Success';
	                $op->shipment_vehicle = $vehicle_type;

	                //by Donny Dennison - 15 september 2020 17:45
	                //change name, image, etc from gogovan to gogox
	                // $op->shipment_service = 'Gogovan';
	                $op->shipment_service = 'Gogox';

	                $op->shipment_cost = 39;
	                $op->shipment_cost_add = 0;

	                if ($this->is_log) {

	                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> Gogovan -> shipment_cost: 39");

	                }

	                if (strtolower($op->services_duration) == 'same day' || strtolower($op->services_duration) == 'sameday') {
	                   

	                    /*
	                     * Disabled Same Day Googvan
	                     */
	                    $this->status = 3158;

	                    //by Donny Dennison - 15 september 2020 17:45
	                    //change name, image, etc from gogovan to gogox
	                    // $this->message = 'Sorry, Gogovan Same Day (Express Service) not available now';
	                    $this->message = 'Sorry, Gogox Same Day (Express Service) not available now';

	                    if ($this->is_log) {
	                        $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> Gogovan APIERROR: $this->message");
	                    }
	                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "shipment");
	                    die();
	                }

	                //END by Donny Dennison - 10 august 2020 14:57
	            }
	            if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> __getGogoVanVehicle: $raw");

	        }

            //END by Donny Dennison 7 oktober 2020 - 14:10
                
        } elseif (strtolower($shipment_type) == 'next day' && strtolower($op->courier_services) == 'qxpress') {
            $this->status = 200;

            //by Donny Dennison 7 oktober 2020 - 14:10
            //add promotion face mask
            //START by Donny Dennison 7 oktober 2020 - 14:10

            //find the face mask product
            $promotion1 = 0;
            $promotion2 = 0;
            $totalProduct = count($products);
            foreach ($products as $key => $value) {
                
                if($value->c_produk_id == 1746 || $value->c_produk_id == 1752 || $value->c_produk_id == 1754){

                    //by Donny Dennison - 23 september 2020 15:42
                    //add direct delivery feature
                    $direct_delivery_promotion = 1;

                    if($value->c_produk_id == 1746){
                        $promotion1 = 1;
                        break;
                    }else if($value->c_produk_id == 1752 || $value->c_produk_id == 1754){
                        $promotion2 = 1;
                    }
                    
                }

            }

            //END by Donny Dennison 7 oktober 2020 - 14:10

            $dimension_max = $op->total_panjang + $op->total_lebar + $op->total_tinggi;
            $op->delivery_date = 'NULL';
            $service = $this->qxbc->getByWeight($nation_code, $op->total_berat);
            //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> call QXpress Next Day: ".json_encode($service));
            $op->shipment_cost_add = 0;
            if (isset($service->cost)) {
                $add_cost = 0.0;
                $this->status = 200;
                $this->message = 'Success';
                $add = $this->qxvl->getByDimension($nation_code, $dimension_max);
                if (isset($add->cost)) {
                    $add_cost = $add->cost;
                }
                $op->shipment_cost = round($service->cost, 2);
                $op->shipment_icon = $this->cdn_url("assets/images/qxpress.png");
                // by Muhammad Sofi - 1 November 2021 13:46
                // add description for shipment type
                $op->deskripsi = "Your order may change with additional fee";
                if (!empty($op->is_fashion)) {
                    $op->shipment_cost = round($service->cost, 2);
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> QXpress Next Day -> Fashion: TRUE, addtional cost eliminated");
                    }
                } else {
                    $op->shipment_cost = round($service->cost+$add_cost, 2);
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> QXpress Next Day -> shipment_cost: ".$service->cost." shipment_cost_add: ".$add_cost." total shipment_cost: ".$op->shipment_cost);
                    }
                }


                //by Donny Dennison 7 oktober 2020 - 14:10
                //add promotion face mask
                //START by Donny Dennison 7 oktober 2020 - 14:10
                
                if($promotion1 == 1){

                    $op->shipment_cost = round(2, 2);
                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> QXpress Next Day -> add shipment_cost \$2 ");

                }else if($promotion2 == 1){

                    $op->shipment_cost = round(0, 2);
                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> QXpress Next Day -> add shipment_cost \$0 ");

                }

                //END by Donny Dennison 7 oktober 2020 - 14:10

                $op->shipment_cost_add = 0;
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> QXpress Next Day -> shipment_cost: ".$op->shipment_cost.", shipment_cost_add: ".$op->shipment_cost_add);
                }
            }
        } elseif (strtolower($shipment_type) == 'same day' && strtolower($op->courier_services) == 'qxpress') {
            $this->status = 200;
            $distance = 0;
            $origin = $pickup->latitude.','.$pickup->longitude;
            $dest = $destination->latitude.','.$destination->longitude;
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> --originLL: $origin, --destinationLL: $dest");
            }
            if ($this->is_google_distance) {
                $rd = $this->__getDistance($origin, $dest);
                $jd = json_decode($rd);
                if (isset($jd->rows[0]->elements[0]->distance->value)) {
                    $distance = $jd->rows[0]->elements[0]->distance->value;
                }
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> using Google API distance");
                }
            } else {
                $distance = $this->__distanceSphere($pickup->latitude, $pickup->longitude, $destination->latitude, $destination->longitude);
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> using Spherical distance");
                }
            }

            if ($distance>0) {
                $res = $this->dodm->update($nation_code, $d_order_id, $d_order_detail_id, array("shipment_distance"=>$distance));
                if ($res) {
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Shipment::__hitungOngkir -> Distance: $distance m save to DB");
                    }
                }
            }

            $service = $this->qxsd->getByDistance($nation_code, $op->total_berat, $distance);
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> call qxpress same day. Distance: $distance");
            }
            if (isset($service->cost)) {
                $this->status = 200;
                $this->message = 'Success';
                $op->shipment_cost = round($service->cost, 2);
                $op->shipment_cost_add = 0.0;
            } else {
                $op->shipment_cost = 20.00;
                $op->shipment_cost_add = 0.0;
                $this->status = 399;
            }
            $op->shipment_icon = $this->cdn_url("assets/images/qxpress.png");
            // by Muhammad Sofi - 1 November 2021 13:46
            // add description for shipment type
            $op->deskripsi = "Your order may change with additional fee";
        } else {
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/shipment::index -> Courier Service: $op->courier_services, Shipment Type: $shipment_type");
            }
            $this->status = 3099;
            $this->message = 'Shipment service unavailable for this order';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/shipment::index -> forceClose '.$this->status.' '.$this->message);
            }
        }
        //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/shipment::index -> POST:".json_encode($_POST));

        //final check
        if ($this->status != 200) {
            $this->status = 860;
            $this->message = 'The origin or destination of product(s) are outside of Singapore';
            //if($dimension_max > 732){
            //$this->status = 861;
            //$this->message = 'Sorry, this transaction is too big for our shipping service. You can make different order to buy more quantity of this product.';
            //}
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", 'API_Mobile/shipment::index -> forceClose '.$this->status.' '.$this->message);
            }
        } else {
            if (strlen($op->delivery_date)!=10 || strlen($op->delivery_date)!=17) {
                $op->delivery_date = 'NULL';
            }
            $this->status = 200;
            $this->message = 'Success';
            $du = array();

            //by Donny Dennison - 23 september 2020 15:42
            //add direct delivery feature
            //START by Donny Dennison - 23 september 2020 15:42

            $du['shipment_service'] = $op->shipment_service;

            if($flagUpdateDirectDeliveryBuyer == 1){
                $du['is_direct_delivery_buyer'] = $isiUpdateDirectDeliveryBuyer;

            }
            //END by Donny Dennison - 23 september 2020 15:42

            $du['shipment_vehicle'] = $op->shipment_vehicle;
            $du['shipment_type'] = $shipment_type;
            $du['delivery_date'] = $op->delivery_date;
            $du['shipment_cost'] = $op->shipment_cost;
            $du['shipment_cost_add'] = $op->shipment_cost_add;
            $du['shipment_cost_sub'] = 0;

            //subtract with free shipping cost
            $freeongkir_count = count($freeongkir);
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> Free Shipping Count: ".$freeongkir_count.", ".$op->shipment_service);
            }

            //by Donny Dennison - 23 september 2020 15:42
            //add direct delivery feature

            //by Donny Dennison - 15 september 2020 17:45
            //change name, image, etc from gogovan to gogox
            // if ($freeongkir_count>0 && strtolower($op->shipment_service) != 'gogovan') {

            // if ($freeongkir_count>0 && strtolower($op->shipment_service) != 'gogox') {
            if ($freeongkir_count>0 && strtolower($op->shipment_service) != 'gogox' && strtolower($op->shipment_service) != 'direct delivery') {

                $this->i = 0;
                $profro = new stdClass();
                $profro->courier_services = "qxpress";
                $profro->shipment_service = "qxpress";
                $profro->shipment_vehicle = "regular";
                $profro->shipment_type = "next day";
                $profro->shipment_cost_add = 0;
                $profro->shipment_cost = 0;
                $profro->total_panjang = 0;
                $profro->total_tinggi = 0;
                $profro->total_lebar = 0;
                $profro->total_berat = 0;
                foreach ($freeongkir as $fro) {
                    $profro = $this->__dimensionCalculation($profro, $fro);
                    $profro->total_berat += $fro->berat * $fro->qty;
                    $profro->shipment_service = $fro->shipment_service;
                    $profro->shipment_type = $fro->shipment_type;
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> profro, total_panjang: ".$profro->total_panjang);
                    }
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> profro, total_lebar: ".$profro->total_lebar);
                    }
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> profro, total_tinggi: ".$profro->total_tinggi);
                    }
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> profro, total_berat: ".$profro->total_berat);
                    }
                }
                $fo = $this->__hitungOngkir($profro, $pickup, $destination, $d_order_id, $d_order_detail_id);
                if (isset($fo->shipment_cost)) {
                    $du['shipment_cost_sub'] += $fo->shipment_cost;
                    if (isset($fo->shipment_cost_add)) {
                        $du['shipment_cost_sub'] += $fo->shipment_cost_add;
                    }
                }
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> Result Free Shipping Cost: ".$op->shipment_cost." Additional: ".$op->shipment_cost);
                }
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> Free Shipping: ".$du['shipment_cost_sub']);
                }

                $op->shipment_cost_sub = $du['shipment_cost_sub'];
                $du['grand_total'] = $op->sub_total + (($du['shipment_cost'] + $du['shipment_cost_add'])-$du['shipment_cost_sub']);
                $op->shipment_cost = ($op->shipment_cost + $op->shipment_cost_add);
                //$op->shipment_cost = ($op->shipment_cost + $op->shipment_cost_add) - $op->shipment_cost_sub;
            }
            if ($gvfree>0) {
                //update to db
                $du['shipment_cost'] = 0;
                $du['shipment_cost_add'] = 0;
                $du['shipment_cost_sub'] = $op->shipment_cost + $op->shipment_cost_add + $op->shipment_cost_sub;

                //output result to buyer
                $this->message = 'Success, yeay you\'ve got free shipping for some product(s)';
                $op->shipment_cost = 0;
                $op->shipment_cost_add = 0;
                $op->shipment_cost_sub = 0;
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> --gvfree ".$this->message);
                }
            } elseif ($freeongkir_count>0 && ($freeongkir_count == $products_count)) {
                //update to db
                $du['shipment_cost'] = 0;
                $du['shipment_cost_add'] = 0;
                $du['shipment_cost_sub'] = $op->shipment_cost + $op->shipment_cost_add + $op->shipment_cost_sub;
                $this->message = 'Success, yeay you\'ve got free shipping for some product(s)';

                //output result to buyer
                $op->shipment_cost = 0;
                $op->shipment_cost_add = 0;
                $op->shipment_cost_sub = 0;
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> --freeongkir ".$this->message);
                }
            } elseif ($qxfree>0 && ($qxfree == $qx)) {
                //update to db
                $du['shipment_cost'] = 0;
                $du['shipment_cost_add'] = 0;
                $du['shipment_cost_sub'] = $op->shipment_cost + $op->shipment_cost_add + $op->shipment_cost_sub;

                //output result to buyer
                $this->message = 'Success, yeay you\'ve got free shipping for some product(s)';
                $op->shipment_cost = 0;
                $op->shipment_cost_add = 0;
                $op->shipment_cost_sub = 0;
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Shipment::index -> --qxfree ".$this->message);
                }
            }
            $du['grand_total'] = $op->sub_total + ($du['shipment_cost'] + $du['shipment_cost_add']);
            $this->dodm->update($nation_code, $order->id, $op->id, $du);
        }

        //building output object
        $data['shipping_rates'] = new stdClass();
        
        //by Donny Dennison - 23 september 2020 15:42
        //add direct delivery feature
        // $data['shipping_rates']->berat = $op->berat;
        $data['shipping_rates']->berat = $op->total_berat;

        $data['shipping_rates']->panjang = $op->total_panjang;
        $data['shipping_rates']->lebar = $op->total_lebar;
        $data['shipping_rates']->tinggi = $op->total_tinggi;
        $data['shipping_rates']->shipment_service = $op->shipment_service;
        $data['shipping_rates']->shipment_icon = $op->shipment_icon;
        $data['shipping_rates']->shipment_vehicle = $op->shipment_vehicle;
        $data['shipping_rates']->shipment_type = $shipment_type;
        $data['shipping_rates']->shipment_cost = strval($op->shipment_cost);
        $data['shipping_rates']->shipment_cost_add = strval($op->shipment_cost_add);
        $data['shipping_rates']->shipment_cost_sub = strval($op->shipment_cost_sub);
        $data['shipping_rates']->shipment_status = $op->shipment_status;
        $data['shipping_rates']->delivery_date = $op->delivery_date;
        // by Muhammad Sofi - 1 November 2021 13:46
        // add description for shipment type
        $data['shipping_rates']->deskripsi = $op->deskripsi;

        //by Donny Dennison - 23 september 2020 15:42
        //add direct delivery feature
        //START by Donny Dennison - 23 september 2020 15:42

        $data['shipping_rates']->direct_delivery_promotion = $direct_delivery_promotion;
        $data['shipping_rates']->direct_delivery = $direct_delivery;

        //update to d_order_detail_item table
        $du = array();
        $du['shipment_service'] = $op->shipment_service;
        $this->dodim->updateByOrderDetailId($nation_code, $op->d_order_id, $op->d_order_detail_id, $du);

        //END by Donny Dennison - 23 september 2020 15:42

        if ($this->is_log) {
            $this->seme_log->write("api_mobile", 'API_Mobile/Shipment::index -> finished '.$this->status.' '.$this->message);
        }

        //render to json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "shipment");
    }
    /**
    * Testing for google maps
    */
    public function distance_test()
    {
        $orig = '1.30377490000,103.90291400000';
        $dest = '1.31738600000,103.77770800000';
        $res = $this->__getDistance($orig, $dest);
        $this->debug($res);
    }
}
