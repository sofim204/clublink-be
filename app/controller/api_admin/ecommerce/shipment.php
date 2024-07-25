<?php
class Shipment extends JI_Controller
{
    public $is_email = 1;

    public function __construct()
    {
        parent::__construct();
        $this->current_parent = 'ecommerce';
        $this->current_page = 'ecommerce_produk';
        $this->load("api_admin/a_negara_model", 'anm');
        $this->load("api_admin/b_user_model", 'bum');
        $this->load("api_admin/c_produk_model", 'cpm');
        $this->load("api_admin/d_order_model", 'dom');
        $this->load("api_admin/d_order_detail_model", 'dodm');
    }
    private function __getOrderStatus2($order_status)
    {
        $os = '-';
        if ($order_status=='pending') {
            $os = 'pending';
        }
        if ($order_status=='waiting_for_payment') {
            $os = 'Waiting for Payment';
        }
        if ($order_status=='payment_verification') {
            $os = 'Payment Verification';
        }
        if ($order_status=='forward_to_seller') {
            $os = 'On Process';
        }
        if ($order_status=='completed') {
            $os = 'Suceed';
        }
        if ($order_status=='cancelled') {
            $os = 'Cancelled';
        }
        return $os;
    }
  
    /**
     * Cancel pickup QXpress
     * @param  string $pickup_number  pickup number
     * @return string          in xml format
     */
    private function __cancelQXpressPickup($pickup_number)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

        //By Donny Dennison, change url api qxpress to the new one
        // curl_setopt($ch, CURLOPT_URL, $this->qx_api_host.'shipment/CancelPickupOrder.php');
        curl_setopt($ch, CURLOPT_URL, $this->qx_api_host.'GMKT.INC.GLPS.OpenApiService/Giosis.qapi?key=&v=1.0&returnType=xml&method=PickupOuterService.CancelPickupOrder');
        
        $headers = array();
        $headers[] = 'Content-Type: Text/xml';
        $headers[] = 'Accept: Text/xml';
        $postdata = array(
          'apiKey' => $this->qx_api_key,
          'accountId' => $this->qx_account_id,
          'pickupNo' => $pickup_number
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return 0;
            //echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }

    private function __convertToEmoji($text){
		$value = $text;
		$readTextWithEmoji = preg_replace("/\\\\u([0-9A-F]{2,5})/i", "&#x$1;", $value);
		return $readTextWithEmoji;
	}

    public function index()
    {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;
        $negara = $this->anm->getByNationCode($nation_code);
        $currency = '';
        if (isset($negara->simbol_mata_uang)) {
            $currency = $negara->simbol_mata_uang;
        }
        $data['currency'] = $currency;

        //get table alias
        $tbl_as = $this->dodm->getTableAlias();
        $tbl2_as = $this->dodm->getTableAlias2(); //product
        $tbl3_as = $this->dodm->getTableAlias3(); //d_order
        $tbl4_as = $this->dodm->getTableAlias4(); //seller
        $tbl5_as = $this->dodm->getTableAlias5(); //buyer
        $tbl6_as = $this->dodm->getTableAlias6();
        $tbl7_as = $this->dodm->getTableAlias7();

        //collect standar input
        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $keyword = $this->input->post("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->post("iDisplayLength");
        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");

        //standar input validation
        $sortCol = "date";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }
        switch ($iSortCol_0) {
            case 0:
                $sortCol = "$tbl_as.d_order_id";
                break;
            case 1:
                $sortCol = "$tbl_as.id";
                break;
            case 2:
                $sortCol = "$tbl_as.nama";
                break;
            case 3:
                $sortCol = "CONCAT(shipment_service,' ',shipment_type)";
                break;
            case 4:
                $sortCol = "(shipment_cost + shipment_cost_add)";
                break;
            case 5:
                $sortCol = "shipment_distance";
                break;
            case 6:
                $sortCol = "shipment_status";
                break;
            case 7:
                $sortCol = "shipment_status";
                break;
            default:
                $sortCol = "$tbl_as.d_order_id";
        }
        if (empty($draw)) {
            $draw = 0;
        }
        if (empty($pagesize)) {
            $pagesize=10;
        }
        if (empty($page)) {
            $page=0;
        }

        //custom input
        $seller_status = $this->input->post('seller_status');
        $shipment_status = $this->input->post('shipment_status');
        $courier_service = $this->input->post('courier_service');
        $delivery_date = $this->input->post('delivery_date');

        //validating date interval
        if (strlen($delivery_date)==10) {
            $delivery_date = date("Y-m-d", strtotime($delivery_date));
        } else {
            $delivery_date = "";
        }
        switch ($courier_service) {

            //by Donny Dennison - 15 september 2020 17:45
            //change name, image, etc from gogovan to gogox
            // case "gogovan":
            case "gogox":

                //by Donny Dennison - 15 september 2020 17:45
                //change name, image, etc from gogovan to gogox
                // $shipment_service = 'gogovan';
                $shipment_service = 'gogox';

                $shipment_type = 'next day';
                break;


            //by Donny Dennison - 15 september 2020 17:45
            //change name, image, etc from gogovan to gogox
            // case "gogovan_sameday":
            case "gogox_sameday":

                //by Donny Dennison - 15 september 2020 17:45
                //change name, image, etc from gogovan to gogox
                // $shipment_service = 'gogovan';
                $shipment_service = 'gogox';

                $shipment_type = 'same day';
                break;
            case "qxpress":
                $shipment_service = 'qxpress';
                $shipment_type = 'next day';
                break;
            case "qxpress_sameday":
                $shipment_service = 'qxpress';
                $shipment_type = 'same day';
                break;

            //by Donny Dennison - 23 september 2020 15:42
            //add direct delivery feature
            case "direct_delivery":
                $shipment_service = 'direct delivery';
                $shipment_type = 'next day';
                break;

            default:
             $shipment_service = '';
             $shipment_type = '';
        }

        $this->status = 200;
        $this->message = 'Success';
        $dcount = $this->dodm->countAllForShipment($nation_code, $keyword, $delivery_date, $shipment_service, $shipment_type, $shipment_status);
        $ddata = $this->dodm->getAllForShipment($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $delivery_date, $shipment_service, $shipment_type, $shipment_status);
        $data['order'] = $ddata;
        foreach ($ddata as &$gd) {
            if (isset($gd->nama)) {

                //by Donny Dennison - 2 september 2020 - 16:00
                // fix korean name in shipment menu
                // if (strlen($gd->nama)>30) {
                //    $gd->nama = substr($gd->nama, 0, 30).'...';

                // }
                $gd->nama = $this->__st2($this->__convertToEmoji($gd->nama), 30);
            
            }
            if (isset($gd->b_user_fnama_seller)) {
                if (strlen($gd->b_user_fnama_seller)>30) {
                    $gd->b_user_fnama_seller = substr($gd->b_user_fnama_seller, 0, 30).'...';
                }
            }
            if (isset($gd->b_user_fnama_buyer)) {
                if (strlen($gd->b_user_fnama_buyer)>30) {
                    $gd->b_user_fnama_buyer = substr($gd->b_user_fnama_buyer, 0, 30).'...';
                }
            }
            if (isset($gd->pickup_date)) {
                if (strlen($gd->pickup_date)>=10) {
                    $gd->pickup_date = date("d/M/y", strtotime($gd->pickup_date));
                }
            }

            //by Donny Dennison - 2 February 2021 13:02
            //fix wrong status in shipment menu cms
            // if (isset($gd->delivery_date)) {
            //     if (strlen($gd->delivery_date)>=10) {
            //         $gd->delivery_date = date("d/M/y", strtotime($gd->delivery_date));
            //     }
            // }
            // if (isset($gd->received_date)) {
            //     if (strlen($gd->received_date)>=10) {
            //         $gd->received_date = date("d/M/y", strtotime($gd->received_date));
            //     }
            // }

            if (isset($gd->order_status_text)) {
                $gd->order_status_text = $this->__orderStatusText($gd->order_status);
            }
            if (isset($gd->shipment_service) && isset($gd->shipment_type)) {
                $ss = strtolower($gd->shipment_service);
                
                //by Donny Dennison - 15 september 2020 17:45
                //change name, image, etc from gogovan to gogox
                // if ($ss!='gogovan') {
                if ($ss!='gogox') {

                    $gd->shipment_service = $gd->shipment_service.' '.$shipment_type;
                }
            }
            if (isset($gd->shipment_status) && isset($gd->delivery_date) && isset($gd->received_date)) {
                if ($gd->shipment_status == 'courier fail') {
                    $gd->shipment_status = ucfirst('courier Fail');
                } elseif ($gd->shipment_status == 'delivered' && strlen($gd->delivery_date) > 9 && strlen($gd->received_date) > 9) {
                    $gd->shipment_status = ucfirst('delivered');
                } elseif ($gd->shipment_status == 'delivered' && strlen($gd->delivery_date) >9 && strlen($gd->received_date) <= 9) {
                    $gd->shipment_status = ucfirst('delivery in progress');
                } elseif (($gd->shipment_status == 'process' || $gd->shipment_status == 'delivered') && strlen($gd->delivery_date) <= 9) {
                    $gd->shipment_status = ucfirst('not yet sent');
                } elseif ($gd->shipment_status == 'succeed') {
                    $gd->shipment_status = ucfirst('received');
                } else {
                    $gd->shipment_status = ucfirst($gd->shipment_status);
                }
            }

            //by Donny Dennison - 2 February 2021 13:02
            //fix wrong status in shipment menu cms
            if (isset($gd->delivery_date)) {
                if (strlen($gd->delivery_date)>=10) {
                    $gd->delivery_date = date("d/M/y", strtotime($gd->delivery_date));
                }
            }
            if (isset($gd->received_date)) {
                if (strlen($gd->received_date)>=10) {
                    $gd->received_date = date("d/M/y", strtotime($gd->received_date));
                }
            }
            
            if (isset($gd->action_text)) {
                $gd->action_text = '<button class="btn btn-warning btn-sm btn-action">Action</button>';
            }
        }
        $this->__jsonDataTable($ddata, $dcount);
    }
    public function detail($d_order_id, $id="")
    {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Access Denied';
            header("HTTP/1.0 400 Access Denied");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;
    
    
        $this->status = 200;
        $this->message = 'Success';
        $data = $this->dodm->getById($nation_code, $d_order_id, $id);
        $this->__json_out($data);
    }
    public function change_status($d_order_id, $c_produk_id)
    {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Access Denied';
            header("HTTP/1.0 400 Access Denied");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        //collect input
        if ($d_order_id<=0) {
            $this->status = 600;
            $this->message = 'Invalid Order ID';
            $this->__json_out($data);
            die();
        }
        if ($c_produk_id<=0) {
            $this->status = 600;
            $this->message = 'Invalid Produk ID';
            $this->__json_out($data);
            die();
        }
        $change_status = $this->input->post("change_status");
        if ($change_status == 'succeed') {
            $du = array("shipment_status"=>'succeed',"delivery_date"=>"NOW()");
        } elseif ($change_status == 'delivered') {
            $du = array("shipment_status"=>'delivered',"delivery_date"=>"NOW()");
        } else {
            $du = array("shipment_status"=>'process',"pickup_date"=>"NOW()","delivery_date"=>"NULL");
        }
        $res = $this->dodm->update($nation_code, $d_order_id, $c_produk_id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->status = 188;
            $this->message = 'Failed updating data bulksale';
        }
        $this->__json_out($data);
    }

    public function change_tracking($d_order_id, $c_produk_id)
    {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Access Denied';
            header("HTTP/1.0 400 Access Denied");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        //collect input
        if ($d_order_id<=0) {
            $this->status = 600;
            $this->message = 'Invalid Order ID';
            $this->__json_out($data);
            die();
        }
        if ($c_produk_id<=0) {
            $this->status = 600;
            $this->message = 'Invalid Produk ID';
            $this->__json_out($data);
            die();
        }
        $shipment_tranid = $this->input->post("shipment_tranid");
        $du = array("shipment_tranid"=>$shipment_tranid);
        $res = $this->dodm->update($nation_code, $d_order_id, $c_produk_id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->status = 188;
            $this->message = 'Failed updating data bulksale';
        }
        $this->__json_out($data);
    }
    public function cancel_qxpress_nextday()
    {
        $pickup_number = $this->input->post("pickup_number");
        $res = $this->__cancelQXpressPickup($pickup_number);
        echo $res;
    }

    // by Muhammad Sofi 9 February 2022 10:00 | get current shipment status data in modal
    public function getShipmentStatus() {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Access Denied';
            header("HTTP/1.0 400 Access Denied");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $d_order_id = $this->input->get('d_order_id') ? $this->input->get('d_order_id') : '';
        $produk_id = $this->input->get('produk_id') ? $this->input->get('produk_id') : '';
    
        $this->status = 200;
        $this->message = 'Success';
        $data = $this->dodm->getById($nation_code, $d_order_id, $produk_id);
        $this->__json_out($data);
    }
}