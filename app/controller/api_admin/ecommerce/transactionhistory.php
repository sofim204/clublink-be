<?php
class TransactionHistory extends JI_Controller
{
    public $is_email = 1;
    public $regards = 'Sellon.com';
    public $from_name = 'Sellon';
    public $from_email = '';
    public $from_subject = '';
    public $berat_faktor = 1200;

    public function __construct()
    {
        parent::__construct();
        //$this->setTheme('frontx');
        $this->load("api_admin/b_user_model", 'bum');
        $this->load("api_admin/c_produk_model", 'cpm');
        $this->load("api_admin/d_order_model", 'dom'); //change alias load model
        $this->load("api_admin/d_order_detail_model", 'dodm');
        $this->load("api_admin/qxpress_basic_model", 'qbm');
        $this->load("api_admin/qxpress_volume_model", 'qvm');
        $this->load("api_admin/qxpress_sameday_model", 'qsm');
        $this->load("api_admin/a_negara_model", 'anm');
        $this->current_parent = 'ecommerce';
        $this->current_page = 'ecommerce_transactionhistory';
    }

    private function __updateStatUser($id_order)
    {
        $order = $this->dom->getById($id_order);
        if (isset($order->id)) {
            $order_detail = $this->dodm->getByOrderId($id_order);
            $user = $this->bum->getById($order->b_user_id);
            if (isset($user->id)) {
                $du = array();
                $du['beli_terakhir'] = $order->date_order;
                $du['beli_jml'] = $user->beli_jml + 1;
                $du['beli_total'] = $user->beli_total + $order->grand_total;
                $this->bum->update($user->id, $du);

                $du = array();
                $du['catatan_admin'] = $order->catatan_admin;
                $ca = '[@'.$user->fnama.' '.date("Y-m-d H:i").'] Statistik orderan diupdate';
                $du['catatan_admin'] = $ca."\r\n".$du['catatan_admin'];
                $this->dom->update($order->id, $du);
            }
        }
    }

    private function __uploadImageKonfirmasi($tran_id)
    {
        /*******************
         * Only these origins will be allowed to upload images *
         *****************
        */
        $folder = SENEROOT.DIRECTORY_SEPARATOR.'media/konfirmasi'.DIRECTORY_SEPARATOR;
        $folder = str_replace('\\', '/', $folder);
        $folder = str_replace('//', '/', $folder);
        $ifol = realpath($folder);
        //die($folder);
        if (!$ifol) {
            mkdir($folder);
        }
        $ifol = realpath($folder);
        //die($ifol);

        reset($_FILES);
        $temp = current($_FILES);
        if (is_uploaded_file($temp['tmp_name'])) {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                // same-origin requests won't set an origin. If the origin is set, it must be valid.
                header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            }
            header('Access-Control-Allow-Credentials: true');
            header('P3P: CP="There is no P3P policy."');

            // Sanitize input
            if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
                header("HTTP/1.0 500 Invalid file name.");
                return 0;
            }
            // Verify extension
            if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("jpg", "png",'jpeg'))) {
                header("HTTP/1.0 500 Invalid extension.");
                return 0;
            }

            // Create wordpress style media directory
            $cy = date('Y'); //current year
            $cm = date('m'); //and month
            if (PHP_OS == "WINNT") {
                if (!is_dir($ifol)) {
                    mkdir($ifol);
                }
                $ifol = $ifol.DIRECTORY_SEPARATOR.$cy.DIRECTORY_SEPARATOR;
                if (!is_dir($ifol)) {
                    mkdir($ifol);
                }
                $ifol = $ifol.DIRECTORY_SEPARATOR.$cm.DIRECTORY_SEPARATOR;
                if (!is_dir($ifol)) {
                    mkdir($ifol);
                }
            } else {
                if (!is_dir($ifol)) {
                    mkdir($ifol, 0775);
                }
                $ifol = $ifol.DIRECTORY_SEPARATOR.$cy.DIRECTORY_SEPARATOR;
                if (!is_dir($ifol)) {
                    mkdir($ifol, 0775);
                }
                $ifol = $ifol.DIRECTORY_SEPARATOR.$cm.DIRECTORY_SEPARATOR;
                if (!is_dir($ifol)) {
                    mkdir($ifol, 0775);
                }
            }

            $name  = md5($tran_id);
            $ext = strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION));
            // Accept upload if there was no origin, or if it is an accepted origin
            $filetowrite = $ifol.$name.'.'.$ext;

            if (file_exists($filetowrite)) {
                unlink($filetowrite);
            }
            move_uploaded_file($temp['tmp_name'], $filetowrite);
            if (file_exists($filetowrite)) {
                $this->lib("wideimage/WideImage", "inc");
                WideImage::load($filetowrite)->resize(500)->saveToFile($filetowrite, 70);
                return "media/konfirmasi/".$cy.'/'.$cm.'/'.$name.'.'.$ext;
            } else {
                return 0;
            }
        } else {
            // Notify editor that the upload failed
            //header("HTTP/1.0 500 Server Error");
            return 0;
        }
    }

    protected function __m($amount, $nation_code="") {
        $n = $this->anm->getByNationCode($nation_code);
        if (isset($n->simbol_mata_uang)) {
            return $n->simbol_mata_uang.'. '.number_format($amount, 2, ',', '.');
        } else {
            return number_format($amount, 2, ',', '.');
        }
    }

    private function __convertToEmoji($text){
		$value = $text;
		$readTextWithEmoji = preg_replace("/\\\\u([0-9A-F]{2,5})/i", "&#x$1;", $value);
		return $readTextWithEmoji;
	}

    public function index()
    {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        //get table alias
        $tbl_as = $this->dodm->getTableAlias();
        $tbl2_as = $this->dodm->getTableAlias2();
        $tbl3_as = $this->dodm->getTableAlias3();
        $tbl4_as = $this->dodm->getTableAlias4();

        //standard input
        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $keyword = $this->input->post("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->post("iDisplayLength");
        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");

        //standard validation
        $sortCol = "invoice_code";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }
        switch ($iSortCol_0) {
            case 0:
                $sortCol = "$tbl_as.d_order_id $sortDir, $tbl_as.id";
                break;
            case 1:
                $sortCol = "COALESCE($tbl3_as.cdate,NOW())";
                break;
            case 2:
                $sortCol = "$tbl3_as.invoice_code";
                break;
            case 3:
                $sortCol = "$tbl_as.nama";
                break;
            case 4:
                $sortCol = "$tbl_as.sub_total";
                break;
            case 5:
                $sortCol = "$tbl_as.total_qty";
                break;
            case 6:
                $sortCol = "$tbl_as.sub_total";
                break;
            case 7:
                $sortCol = "($tbl_as.shipment_cost+$tbl_as.shipment_cost_add)";
                break;
            case 8:
                $sortCol = "$tbl_as.profit_amount";
                break;
            case 9:
                $sortCol = "$tbl_as.earning_total";
                break;
            case 10:
                $sortCol = "$tbl_as.refund_amount";
                break;
            case 11:
                $sortCol = "$tbl_as.banktrf_cost";
                break;
            case 12:
                $sortCol = "COALESCE($tbl3_as.order_status,'-')";
                break;
            case 13:
                $sortCol = "COALESCE($tbl3_as.payment_status,'-')";
                break;
            case 14:
                $sortCol = "$tbl_as.seller_status";
                break;
            case 15:
                $sortCol = "$tbl_as.shipment_status";
                break;
            case 16:
                $sortCol = "$tbl_as.buyer_confirmed";
                break;
            default:
                $sortCol = "CONCAT($tbl_as.d_order_id,'/',$tbl_as.id)";
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
        $shipment_status = $this->input->post("shipment_status");
        $seller_status = $this->input->post("seller_status");
        $buyer_confirmed = $this->input->post("buyer_confirmed");
        $order_status = $this->input->post("order_status");
        $payment_status = $this->input->post("payment_status");
        $settlement_status = $this->input->post("settlement_status");
        $cdate_start = $this->input->post("cdate_start");
        $cdate_end = $this->input->post("cdate_end");

        //validating date interval
        if (strlen($cdate_start)==10) {
            $cdate_start = date("Y-m-d", strtotime($cdate_start));
        } else {
            $cdate_start = "";
        }
        if (strlen($cdate_end)==10) {
            $cdate_end = date("Y-m-d", strtotime($cdate_end));
        } else {
            $cdate_end = "";
        }
        $nc = $nation_code;

        //get data
        $dcount = $this->dodm->countAllForHistoryTRX($nation_code, $keyword, $payment_status, $order_status, $seller_status, $shipment_status, $buyer_confirmed, $settlement_status, $cdate_start, $cdate_end);
        $ddata = $this->dodm->getAllForHistoryTRX($nc, $page, $pagesize, $sortCol, $sortDir, $keyword, $payment_status, $order_status, $seller_status, $shipment_status, $buyer_confirmed, $settlement_status, $cdate_start, $cdate_end);
        foreach ($ddata as &$dt) {
            if(isset($dt->nama)){
				$dt->nama = $this->__convertToEmoji($dt->nama);
			}

            if (isset($dt->harga_jual)) {
                $dt->harga_jual = $this->__m($dt->harga_jual, $nation_code);
            }

            if (isset($dt->sub_total)) {
                $dt->sub_total = $this->__m($dt->sub_total, $nation_code);
            }

            if (isset($dt->earning_total)) {
                $dt->earning_total = $this->__m($dt->earning_total, $nation_code);
            }

            if (isset($dt->shipment_cost)) {
                $dt->shipment_cost = round($dt->shipment_cost, 2, PHP_ROUND_HALF_DOWN);
            }
            if (isset($dt->cdate)) {
                $dt->cdate = date("d/M/y", strtotime($dt->cdate));
            }
            $dt->action = '<button class="btn btn-default" data-id="'.$dt->id.'">View Detail</button>';
            if (isset($dt->order_status)) {
                $dt->order_status = $this->__orderStatusText($dt->order_status);
            }
            if (isset($dt->payment_status)) {
                $dt->payment_status = $this->__paymentStatusText($dt->payment_status);
            }
            if (isset($dt->seller_status)) {
                $dt->seller_status = $this->__sellerStatusText($dt->seller_status);
            }
            if (isset($dt->shipment_status) && isset($dt->delivery_date) && isset($dt->received_date)) {

                //By Donny Dennison - 08-07-2020 16:16
                //Request by Mr Jackie, add new shipment status "courier fail"
                if ($dt->shipment_status == 'courier fail') {
                    $dt->shipment_status = ucfirst('courier Fail');
                    
                } elseif ($dt->shipment_status == 'delivered' && strlen($dt->delivery_date) > 9 && strlen($dt->received_date) > 9) {
                    $dt->shipment_status = ucfirst('delivered');
                } elseif ($dt->shipment_status == 'delivered' && strlen($dt->delivery_date) >9 && strlen($dt->received_date) <= 9) {
                    $dt->shipment_status = ucfirst('delivery in progress');
                } elseif (($dt->shipment_status == 'process' || $dt->shipment_status == 'delivered') && strlen($dt->delivery_date) <= 9) {
                    $dt->shipment_status = ucfirst('not yet sent');
                } elseif ($dt->shipment_status == 'succeed') {
                    $dt->shipment_status = ucfirst('received');
                } else {
                    $dt->shipment_status = ucfirst($dt->shipment_status);
                }
            }
            if (isset($dt->buyer_confirmed)) {
                $dt->buyer_confirmed = $this->__buyerConfirmedText($dt->buyer_confirmed);
            }
            if (isset($dt->settlement_status)) {
                $dt->settlement_status = $this->__settlementStatusText($dt->settlement_status);
            }
        }

        //render output
        $this->status = 200;
        $this->message = 'Success';
        $this->__jsonDataTable($ddata, $dcount);
    }

    public function detail($id)
    {
        $id = (int) $id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $this->status = 200;
        $this->message = 'Success';
        $data = $this->cpm->getById($id);
        $this->__json_out($data);
    }
}
