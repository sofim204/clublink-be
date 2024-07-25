<?php
class WayBill extends JI_Controller
{
    public $is_log = 1;
    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->lib("seme_email");
        $this->load("admin/a_pengguna_model", "apm");
        $this->load("admin/common_code_model", "ccm");
        $this->load("admin/c_produk_model", "cpm");
        $this->load("admin/c_produk_foto_model", "cpfm");
        $this->load("admin/b_user_model", "bu");
        $this->load("admin/b_user_alamat_model", "bua");
        $this->load("admin/d_order_model", "dom");
        $this->load("admin/d_order_alamat_model", "doam");
        $this->load("admin/d_order_detail_model", "dodm");
        $this->load("admin/d_order_proses_model", "dopm");
        $this->load("admin/d_order_detail_item_model", "dodim");
        $this->load("admin/d_order_detail_pickup_model", "dodpum");
    }
    /**
     * Force download file over http
     * @param  string $pathFile realpath to file
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
        header('Content-Length: ' . filesize($pathFile));
        ob_clean();
        flush();
        readfile($pathFile);
        exit;
    }

    /**
     * Get Area Code QXpress by calling axpress api
     * @param  string $kodepos zipcode
     * @return string          in XML format
     */
    private function __getQXpressArea($kodepos)
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
          $this->seme_log->write("api_admin", 'API_Mobile/WayBill::__getQXpressArea:: -- cUrlHeader: '.json_encode($headers));
          $this->seme_log->write("api_admin", 'API_Mobile/WayBill::__getQXpressArea:: -- cUrlPOST: '.json_encode($postdata));
        }
        return $result;
    }
    
    /**
     * WordWrap for Korean language
     * @param  string  $string input string
     * @param  integer $width  [description]
     * @param  string  $break  [description]
     * @param  boolean $cut    [description]
     * @return string          wrapped string
     */
    private function __wordWrapUTF8($string, $width=75, $break="\n", $cut=false)
    {
        if ($cut) {
            $search = '/(.{1,'.$width.'})(?:\s|$)|(.{'.$width.'})/uS';
            $replace = '$1$2'.$break;
        } else {
            $search = '/(?=\s)(.{1,'.$width.'})(?:\s|$)/uS';
            $replace = '$1'.$break;
        }
        return preg_replace($search, $replace, $string);
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

    private function __convertToEmoji($text){
		$value = $text;
		$readTextWithEmoji = preg_replace("/\\\\u([0-9A-F]{2,5})/i", "&#x$1;", $value);
		return $readTextWithEmoji;
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
    public function download($d_order_id, $c_produk_id)
    {
        //initial
        $dt = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $dt['sess']->admin->nation_code;

        //c_produk_id
        $d_order_id = (int) $d_order_id;
        if ($d_order_id<=0) {
            redir(base_url_admin("ecommerce/transaction/seller/"));
            die();
        }

        $c_produk_id = (int) $c_produk_id;
        if ($c_produk_id<=0) {
            redir(base_url_admin("ecommerce/transaction/seller/"));
            die();
        }

        //get order
        $order = $this->dom->getById($nation_code, $d_order_id);
        if (!isset($order->b_user_id)) {
            redir(base_url_admin("ecommerce/transaction/seller/"));
            die();
        }

        //get order detail
        $order->detail = $this->dodm->getDetailByOrderId($nation_code, $d_order_id, $c_produk_id);
        if (!isset($order->detail->c_produk_id)) {
            redir(base_url_admin("ecommerce/transaction/seller/"));
            die();
        }
        $order->detail->products = $this->dodim->getByOrderDetailId($nation_code, $d_order_id, $c_produk_id);
        //$this->debug($order->detail);
        //die();

        //get address pickup

        //get buyer detail
        $buyer = $this->bu->getById($nation_code, $order->b_user_id);
        $seller = $this->bu->getById($nation_code, $order->detail->b_user_id);

        //put another
        $pickup = $this->dodpum->getById($nation_code, $order->id, $order->detail->id);
        $order->billing = $this->doam->getBillingByOrderId($nation_code, $order->id);
        $order->shipping = $this->doam->getShippingByOrderId($nation_code, $order->id);
        $order->proses = $this->dopm->getDetailByID($nation_code, $order->id, $order->detail->id);

        if (!isset($pickup->penerima_nama)) {
            //if not exist, get from b_user_alamat
            $pa = $this->bua->getById($nation_code, $order->detail->b_user_id, $order->detail->b_user_alamat_id);
            if (!isset($pa->penerima_nama)) {
                $this->status = 6022;
                $this->message = 'Pickup address not found';
                $this->__json_out($data);
                die();
            }

            //insert into pickup order
            $padi = array();
            $padi['nation_code'] = $nation_code;
            $padi['d_order_id'] = $order->detail->d_order_id;
            $padi['d_order_detail_id'] = $order->detail->id;
            $padi['b_user_id'] = $order->detail->b_user_id;
            $padi['b_user_alamat_id'] = $order->detail->b_user_alamat_id;
            $padi['nama'] = $pa->penerima_nama;
            $padi['telp'] = $pa->penerima_telp;

            // by Donny Dennison - 3 November 2021 10:00
            // remark code
            // $padi['alamat'] = $pa->alamat;

            $padi['alamat2'] = $pa->alamat2;
            $padi['kelurahan'] = $pa->kelurahan;
            $padi['kecamatan'] = $pa->kecamatan;
            $padi['kabkota'] = $pa->kabkota;
            $padi['provinsi'] = $pa->penerima_nama;
            $padi['negara'] = $pa->negara;
            $padi['kodepos'] = $pa->kodepos;
            $padi['latitude'] = $pa->latitude;
            $padi['longitude'] = $pa->longitude;
            $padi['catatan'] = $pa->catatan;
            $this->dodpum->set($padi);
            $pickup = $pa;
            $pickup->nama = $pa->penerima_nama;
            $pickup->telp = $pa->penerima_nama;

            // by Donny Dennison - 3 November 2021 10:00
            // remark code
            // $pickup->alamat1 = $pa->alamat;

            $pickup->address_notes = $pa->catatan;
            unset($pa, $paid);
        }

        $order->pickup = $pickup;

        //put to log
        if ($this->is_log) {
            $this->seme_log->write("api_admin", "admin/ecommerce/WayBill::print() --shipment_service:".$order->detail->shipment_service.", --shipment_type: ".$order->detail->shipment_type);
        }

        //load pdf library
        $this->lib("seme_fpdf");
        $this->seme_fpdf->AddUHCFont();

        //set page size
        $this->seme_fpdf->AddPage('L', 'A5');

        $area = '';

        //by Donny Dennison - 13-07-2020 13:54
        //disable send api to qxpress
        // $tracking_no = $order->detail->shipment_tranid;
        // if (strtolower($order->detail->shipment_service) == 'qxpress') {
        //     $tracking_number = $order->detail->tracking_number;
        //     if (strlen($tracking_number)<=0) {
        //         $tracking_number="-";
        //     }
        // }else {
        //     $tracking_no = $order->detail->shipment_tranid;
        //     if (strlen($tracking_no)<=0) {
        //         $tracking_no="-";
        //     }
        // }
        $tracking_no = "-";

        //check logo

        //by Donny Dennison - 23 september 2020 15:42
        //add direct delivery feature
        //START by Donny Dennison - 23 september 2020 15:42
        if (strtolower($order->detail->shipment_service) == 'direct delivery' || $order->detail->is_direct_delivery_buyer == 1) {

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
            $this->seme_fpdf->Cell(31, 0, 'https://sellon.net/', 0, 0, 'R');
            $this->seme_fpdf->Ln();


        // if (strtolower($order->detail->shipment_service) == 'qxpress') {
        }else if (strtolower($order->detail->shipment_service) == 'qxpress') {

        //END by Donny Dennison - 23 september 2020 15:42

            $logo = SENEROOT.'assets/images/qxpress.png';
            if (is_file($logo)) {
                $this->seme_fpdf->Image($logo, 90, 5, 40, 20);
                $this->seme_fpdf->Ln();
            }

            //by Donny Dennison - 13-07-2020 13:54
            //disable send api to qxpress
            // $result = @simplexml_load_string($this->__getQXpressArea($order->shipping->kodepos), 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS); //LOL use STFU :D veteran should know about this -- DR
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
        // } elseif (strtolower($order->detail->shipment_service) == 'gogovan') {
        } elseif (strtolower($order->detail->shipment_service) == 'gogox') {
            
            //by Donny Dennison - 15 september 2020 17:45
            //change name, image, etc from gogovan to gogox
            // $logo = SENEROOT.'assets/images/gogovan.png';
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
        $pickup = $order->pickup;

        //new lines
        $this->seme_fpdf->Line(10, 23, 210-20, 23);

        //table origin start//
        $this->seme_fpdf->SetY(23);
        $this->seme_fpdf->SetFillColor(224, 235, 255);
        $this->seme_fpdf->SetTextColor(0);
        $this->seme_fpdf->SetFont('UHC', '', 8);
        $fill = false;

        $shipping = $order->shipping;

        // remove zip code from address

        // by Donny Dennison - 3 November 2021 10:00
        // remark code
        // $shipping->alamat = str_replace(" ".$shipping->kodepos, "", $shipping->alamat);

        $shipping->alamat2 = str_replace(" ".$shipping->kodepos, "", $shipping->alamat2);
        
        // by Donny Dennison - 3 November 2021 10:00
        // remark code
        // $pickup->alamat = str_replace(" ".$pickup->kodepos, "", $pickup->alamat);
        
        $pickup->alamat2 = str_replace(" ".$pickup->kodepos, "", $pickup->alamat2);

        //By Donny Dennison - 27 juni 2020 3:23
        //request by Mr Jackie, remove alamat in pdf
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
        $this->seme_fpdf->Cell($w[1], 5, iconv("UTF-8","UHC",$shipping->nama), 'BR', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[2], 5, 'Shipper', 'BL', 0, 'L', $fill);
        $this->seme_fpdf->Cell($w[3], 5, iconv("UTF-8","UHC",$pickup->penerima_nama), 'BR', 0, 'L', $fill);
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
        $this->seme_fpdf->Cell($w[3], 6, date("d-M-y", strtotime($tgl)), 'BLR', 0, 'C', $fill);
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
        $this->seme_fpdf->Cell(array_sum($w), 6, 'Item Description'.'       Packing no :'.$order->detail->shipment_tranid.'   Invoice no :'.$order->invoice_code.'', 'TLRB', 0, 'L', $fill);
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
        $order->berat = round($order->detail->total_berat, 1);
        $order->qty = (int) $order->detail->qty;

        //item list
        $i = 0;
        foreach ($items as $item) {
            $total_qty += $item->qty;
            $total_berat += ($item->berat*$item->qty);
            $this->seme_fpdf->SetFont('', 'B');
            $this->seme_fpdf->Cell(20, 5, 'Qty. '.$item->qty.' ea', 1, 0, 'C', true);
            $this->seme_fpdf->Cell(60, 5, '', 1, 0, 'C', true);
            $this->seme_fpdf->Ln();
            $this->seme_fpdf->Cell($w[0], 5, $item->nama, '1', 0, 'L', $fill);
            //$this->seme_fpdf->Cell($w[1],6,number_format($item->qty,0,',','.').' '.$item->satuan,'LR',0,'C',$fill);
            //$this->seme_fpdf->Cell($w[2],6,''.number_format(($item->berat*$item->qty),1,',','.').' Kg','LR',0,'C',$fill);
            $this->seme_fpdf->Ln();
            $fill = !$fill;
            $i++;
        }
        if (empty($i)) {
            $fill =true;
            $this->seme_fpdf->Cell(array_sum($w), 5, 'No product', 'LR', 0, 'L', $fill);
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

       // // By Donny Dennison, 30 june 2020 15:43
       // // change tracking_no to tracking_number
        // $tracking_no = $order->detail->shipment_tranid;
        // if (strlen($tracking_no)<=0) {
        //     $tracking_no="-";
        // }
        

        //generate QRCode
        $this->lib("seme_qrcode");
        $this->seme_qrcode->root("");
        $this->seme_qrcode->media($save_dir);

        $pngurl = $this->seme_qrcode->write(strval($tracking_no), strtolower($tracking_no), "png");

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
        
        $this->seme_fpdf->Code39(117, 106, $tracking_no.'', 0.8, 8, 'C');
        

        if ($order->detail->seller_status == 'rejected') {
            $this->seme_fpdf->setXY(160, 50);
            $this->seme_fpdf->SetFont('Arial', 'B', 40);
            $this->seme_fpdf->SetTextColor(255, 192, 203);
            $this->seme_fpdf->Cell(20, 13, 'REJECTED BY SELLER', 0, 0, 'R');
        } elseif ($order->detail->shipment_status == 'pending' || $order->detail->shipment_status == 'courier fail') {
            $this->seme_fpdf->setXY(160, 50);
            $this->seme_fpdf->SetFont('Arial', 'B', 40);
            $this->seme_fpdf->SetTextColor(255, 192, 203);
            $this->seme_fpdf->Cell(20, 13, 'NOT YET SHIPPED', 0, 0, 'R');
        }

        $save_dir = SENEROOT.'media';
        if (!is_dir($save_dir)) {
            mkdir($save_dir);
        }
        $save_dir = SENEROOT.'media/order/';
        if (!is_dir($save_dir)) {
            mkdir($save_dir);
        }
        $save_file = "waybill-".$d_order_id."-".$c_produk_id;
        $this->seme_fpdf->Output('I', $save_dir.'/'.$save_file.'.pdf');

        //if(file_exists($save_dir.'/'.$save_file.'.pdf')) unlink($save_dir.'/'.$save_file.'.pdf');
        //$this->__forceDownload($save_dir.'/'.$save_file.'.pdf');
    }
}
