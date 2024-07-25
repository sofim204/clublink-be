<?php
class D_Order_Detail_Model extends JI_Model
{
    public $is_cacheable;
    public $tbl = 'd_order_detail';
    public $tbl_as = 'dod';
    public $tbl2 = 'c_produk';
    public $tbl2_as = 'cp';
    public $tbl3 = 'd_order';
    public $tbl3_as = 'dor';
    public $tbl4 = 'b_user';
    public $tbl4_as = 'bu'; //seller alias
    public $tbl5 = 'b_user';
    public $tbl5_as = 'bu2'; //buyer alias
    public $tbl6 = 'b_user_alamat';
    public $tbl6_as = 'bua';
    public $tbl7 = 'd_order_alamat';
    public $tbl7_as = 'doa';
    public $tbl8 = 'b_user_bankacc';
    public $tbl8_as = 'buba1';
    public $tbl9 = 'b_user_bankacc';
    public $tbl9_as = 'buba2';
    public $tbl10 = 'a_bank';
    public $tbl10_as = 'ab1';
    public $tbl11 = 'a_bank';
    public $tbl11_as = 'ab2';
    //Edit By Aditya Adi Prabowo 3/9/2020 1:14
    // Edit in Transation By Seller Menu
    // Start Edit
    public $tbl12 = 'd_order_detail_item';
    public $tbl12_as = 'dodi';

    public function __construct()
    {
        parent::__construct();
        $this->is_cacheable = 0;
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl2_as.id");
        return $cps;
    }
    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.d_order_id", "=", "$this->tbl3_as.id");
        return $cps;
    }

    //for seller, requires joinTbl2
    private function __joinTbl4()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl4_as.id");
        return $cps;
    }

    //for buyer, requires joinTbl3
    private function __joinTbl5()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl3_as.nation_code", "=", "$this->tbl5_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl3_as.b_user_id", "=", "$this->tbl5_as.id");
        return $cps;
    }

    //for seller address, requires joinTbl2
    private function __joinTbl6()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl6_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl6_as.b_user_id");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_alamat_id", "=", "$this->tbl6_as.id");
        return $cps;
    }

    //for buyer address, requires joinTbl3
    private function __joinTbl7()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl3_as.nation_code", "=", "$this->tbl7_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl3_as.id", "=", "$this->tbl7_as.d_order_id");
        return $cps;
    }

    //for buyer bank account, requires joinTbl3
    private function __joinTbl8()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl3_as.nation_code", "=", "$this->tbl8_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl3_as.b_user_id", "=", "$this->tbl8_as.b_user_id");
        return $cps;
    }

    //for buyer bank name, requires joinTbl8
    private function __joinTbl10()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl8_as.nation_code", "=", "$this->tbl10_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl8_as.a_bank_id", "=", "$this->tbl10_as.id");
        return $cps;
    } 

    //for seller bank account, requires joinTbl2
    private function __joinTbl9()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl9_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl9_as.b_user_id");
        return $cps;
    }

    //for seller bank name, requires joinTbl9
    private function __joinTbl11()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl9_as.nation_code", "=", "$this->tbl11_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl9_as.a_bank_id", "=", "$this->tbl11_as.id");
        return $cps;
    }

    public function getTableAlias()
    {
        return $this->tbl_as;
    }

    public function getTableAlias2()
    {
        return $this->tbl2_as;
    }

    public function getTableAlias3()
    {
        return $this->tbl3_as;
    }

    public function getTableAlias4()
    {
        return $this->tbl4_as;
    }

    public function getTableAlias5()
    {
        return $this->tbl5_as;
    }

    public function getTableAlias6()
    {
        return $this->tbl6_as;
    }

    public function getTableAlias7()
    {
        return $this->tbl7_as;
    }

    public function trans_start()
    {
        $r = $this->db->autocommit(0);
        if ($r) {
            return $this->db->begin();
        }
        return false;
    }
    public function trans_commit()
    {
        return $this->db->commit();
    }
    public function trans_rollback()
    {
        return $this->db->rollback();
    }
    public function trans_end()
    {
        return $this->db->autocommit(1);
    }
        
    public function setDebug($is_debug=0)
    {
        $this->db->setDebug($is_debug);
    }

    public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="id", $sortDir="desc", $keyword="", $sdate="", $edate="", $shipment_type="", $shipment_service="", $seller_status="", $shipment_status="", $order_status="")
    {
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'/',$this->tbl_as.id)", "id", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as($this->__decrypt("$this->tbl4_as.fnama"), "b_user_fnama_seller", 0);

        //By Donny Dennison - 27 juni 2020 3:23
        //not using alamat again
        // $this->db->select_as("COALESCE($this->tbl6_as.alamat)", "b_user_alamat_alamat_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl6_as.alamat2"), "b_user_alamat_alamat_seller", 0);
        
        $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "b_user_fnama_buyer", 0);
        
        //By Donny Dennison - 27 juni 2020 3:23
        //not using alamat again
        // $this->db->select_as("COALESCE($this->tbl7_as.alamat)", "b_user_alamat_alamat_buyer", 0);
        $this->db->select_as($this->__decrypt("$this->tbl7_as.alamat2"), "b_user_alamat_alamat_buyer", 0);

        $this->db->select_as("'-'", "order_status_text", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("'-'", "action_text", 0);
        $this->db->select_as("$this->tbl3_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl3_as.payment_status", "payment_status", 0);
       
        $this->db->select_as("$this->tbl_as.shipment_tranid", "shipment_tranid", 0);
        $this->db->select_as("$this->tbl_as.shipment_type", "shipment_type", 0);
        $this->db->select_as("COALESCE($this->tbl_as.delivery_date,'')", "delivery_date", 0);
        $this->db->select_as("COALESCE($this->tbl_as.received_date,'')", "received_date", 0);
        //Edit By Aditya Adi Prabowo 3/9/2020 1:14
        // Edit in Transation By Seller Menu
        // Start Edit
        $this->db->select_as("$this->tbl_as.id", "id_temp", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "id_temp2", 0);
        // End Of Edit
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "left");
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("pending"), "AND", "<>");
        $this->db->where_as("$this->tbl7_as.address_status", $this->db->esc("A2"));
        if (strlen($sdate)==10 && strlen($edate)==10) {
            $this->db->between("DATE($this->tbl3_as.cdate)", "DATE('$sdate')", "DATE('$edate')");
        } elseif (strlen($sdate)==10 && strlen($edate)!=10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$sdate')", 'AND', '>=');
        } elseif (strlen($sdate)!=10 && strlen($edate)==10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$edate')", 'AND', '<=');
        }
        if (strlen($shipment_type)) {
            $this->db->where_as("$this->tbl_as.shipment_type", $this->db->esc($shipment_type));
        }
        if (strlen($shipment_service)) {
            $this->db->where_as("$this->tbl_as.shipment_service", $this->db->esc($shipment_service));
        }
        if (strlen($seller_status)) {
            $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc($seller_status));
        }
        //By Aditya Adi - 2 July 2020 18.30 
        // Add parameter order status
        //$ddata = $this->dodm->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $sdate, $edate, $shipment_type, $shipment_service, $seller_status, $shipment_status);
        if (strlen($order_status)) {
            $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc($order_status));
        }
        if (strlen($shipment_status)) {
            switch ($shipment_status) {
            case 'not_yet_sent':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("process"), "OR", "like");
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "like");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.delivery_date,''))", 9, "AND", "<=");
              break;

            //By Donny Dennison - 08-07-2020 16:16
            //Request by Mr Jackie, add new shipment status "courier fail"
            case 'courier_fail':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("courier fail"), "AND", "like");
              break;

            case 'delivery_in_progress':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "like");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.delivery_date,''))", 9, "AND", ">");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.received_date,''))", 9, "AND", "<=");
              break;
            case 'delivered':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "like");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.delivery_date,''))", 9, "AND", ">");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.received_date,''))", 9, "AND", ">");
              break;
            case 'received':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "like");
              break;
            default:
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc($shipment_status));
          }
        }
        if (strlen($keyword)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.invoice_code,'-')", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("CONCAT($this->tbl_as.d_order_id,'/',$this->tbl_as.id)", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);
            /*$this->db->where_as($this->__decrypt("$this->tbl4_as.fnama"), addslashes($keyword), "OR", "%like%", 0, 0);
            Edit by Aditya Adi Prabowo 8/9/2020
            Fix Sensitive Filter by Buyer Name, Seller Name, And Order Status*/
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl4_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl5_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            // End Of Edit
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);

        }
        switch ($sortCol) {
            case 0:
                $sortCol = "$this->tbl_as.d_order_id $sortDir, $this->tbl_as.id";
                break;
            case 1:
                $sortCol = "$this->tbl3_as.cdate";
                break;
            case 2:
                $sortCol = "$this->tbl_as.nama";
                break;
            case 3:
                $sortCol = "CAST(COALESCE($this->tbl_as.sub_total,0) AS SIGNED)";
                break;
            case 4:
                $sortCol = $this->__decrypt("$this->tbl4_as.fnama");
                break;
            case 5:
                $sortCol = "COALESCE($this->tbl6_as.alamat,'-')";
                break;
            case 6:
                $sortCol = $this->__decrypt("$this->tbl5_as.fnama");
                break;
            case 7:
                $sortCol = "COALESCE($this->tbl7_as.alamat,'-')";
                break;
            case 8:
                $sortCol = "$this->tbl3_as.order_status";
                break;
            case 9:
                $sortCol = "$this->tbl_as.shipment_service";
                break;
            case 10:
                $sortCol = "$this->tbl_as.seller_status";
                break;
            case 11:
                $sortCol = "$this->tbl_as.shipment_status";
                break;
            case 12:
                $sortCol = "$this->tbl_as.buyer_confirmed";
                break;
            default:
                $sortCol = "CONCAT($this->tbl_as.d_order_id,'-',$this->tbl_as.id)";
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get('', 0);
    }

    public function countAll($nation_code, $keyword="", $sdate="", $edate="", $shipment_type="", $shipment_service="", $seller_status="", $shipment_status="")
    {
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
        //EDIT by Aditya Adi Prabowo 28 July 2020 11:18
        // Request By Mr. Jackie to change same with display and report excel.
        // Improve Start
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "left");
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("pending"), "AND", "<>");
        $this->db->where_as("$this->tbl7_as.address_status", $this->db->esc("A2"));

        if (strlen($sdate)==10 && strlen($edate)==10) {
            $this->db->between("DATE($this->tbl3_as.cdate)", "DATE('$sdate')", "DATE('$edate')");
        } elseif (strlen($sdate)==10 && strlen($edate)!=10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$sdate')", 'AND', '>=');
        } elseif (strlen($sdate)!=10 && strlen($edate)==10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$edate')", 'AND', '<=');
        }
        if (strlen($shipment_type)) {
            $this->db->where_as("$this->tbl_as.shipment_type", $this->db->esc($shipment_type));
        }
        if (strlen($shipment_service)) {
            $this->db->where_as("$this->tbl_as.shipment_service", $this->db->esc($shipment_service));
        }
        if (strlen($seller_status)) {
            $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc($seller_status));
        }
        if (strlen($shipment_status)) {
            switch ($shipment_status) {
            case 'not_yet_sent':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("process"), "OR", "like");
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "like");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.delivery_date,''))", 9, "AND", "<=");
              break;
            
            //By Donny Dennison - 08-07-2020 16:16
            //Request by Mr Jackie, add new shipment status "courier fail"
            case 'courier_fail':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("courier fail"), "AND", "like");
              break;

            case 'delivery_in_progress':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "like");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.delivery_date,''))", 9, "AND", ">");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.received_date,''))", 9, "AND", "<=");
              break;
            case 'delivered':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "like");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.delivery_date,''))", 9, "AND", ">");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.received_date,''))", 9, "AND", ">");
              break;
            case 'received':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "like");
              break;
            default:
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc($shipment_status));
          }
        }

        if (strlen($keyword)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.invoice_code,'-')", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("CONCAT($this->tbl_as.d_order_id,'/',$this->tbl_as.id)", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as($this->__decrypt("$this->tbl4_as.fnama"), addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        // Improve END
        $d = $this->db->get_first();
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getAllForShipment($nation_code, $page=0, $pagesize=10, $sortCol="id", $sortDir="desc", $keyword="", $delivery_date="", $shipment_service="", $shipment_type="", $shipment_status="")
    {
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("CONCAT(shipment_service,' - ',shipment_type)", "shipment_type", 0);
        $this->db->select_as("(shipment_cost + shipment_cost_add)", "cost", 0);
        $this->db->select_as("shipment_distance", "shipment_distance", 0);
        $this->db->select_as("COALESCE($this->tbl_as.pickup_date,'-')", "pickup_date", 0);
        $this->db->select_as("COALESCE($this->tbl_as.delivery_date,'-')", "delivery_date", 0);
        $this->db->select_as("COALESCE($this->tbl_as.received_date,'-')", "received_date", 0);
        $this->db->select_as("COALESCE($this->tbl_as.shipment_tranid,'-')", "shipment_tranid", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status_teks", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        //EDIT by Aditya Adi Prabowo 28 July 2020 11:18
        // Request By Mr. Jackie to change same with display and report excel.
        // Improve Start
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"));
        if (strlen($delivery_date)==10) {
            $this->db->where_as("DATE($this->tbl_as.delivery_date)", "DATE('$delivery_date')", 'AND', '=');
        }
        if (strlen($shipment_type)) {
            $this->db->where_as("$this->tbl_as.shipment_type", $this->db->esc($shipment_type));
        }
        if (strlen($shipment_status)) {
            switch ($shipment_status) {
            case 'not_yet_sent':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("process"), "OR", "like");
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "like");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.delivery_date,''))", 9, "AND", "<=");
              break;

            //By Donny Dennison - 08-07-2020 16:16
            //Request by Mr Jackie, add new shipment status "courier fail"
            case 'courier_fail':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("courier fail"), "AND", "like");
              break;

            case 'delivery_in_progress':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "like");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.delivery_date,''))", 9, "AND", ">");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.received_date,''))", 9, "AND", "<=");
              break;
            case 'delivered':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "like");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.delivery_date,''))", 9, "AND", ">");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.received_date,''))", 9, "AND", ">");
              break;
            case 'received':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "like");
              break;
            default:
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc($shipment_status));
          }
        }
        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("COALESCE($this->tbl_as.d_order_id,'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl_as.nama,'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl_as.shipment_tranid,'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl_as.shipment_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        //Improve END
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get('', 0);
    }

    public function countAllForShipment($nation_code, $keyword="", $delivery_date="", $shipment_service="", $shipment_type="", $shipment_status="")
    {
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"));
        if (strlen($delivery_date)==10) {
            $this->db->where_as("DATE($this->tbl_as.delivery_date)", "DATE('$delivery_date')", 'AND', '=');
        }
        if (strlen($shipment_type)) {
            $this->db->where_as("$this->tbl_as.shipment_type", $this->db->esc($shipment_type));
        }
        if (strlen($shipment_service)) {
            $this->db->where_as("$this->tbl_as.shipment_service", $this->db->esc($shipment_service));
        }
        if (strlen($shipment_status)) {
            switch ($shipment_status) {
            case 'not_yet_sent':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("process"), "OR", "like");
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "like");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.delivery_date,''))", 9, "AND", "<=");
              break;

            //By Donny Dennison - 08-07-2020 16:16
            //Request by Mr Jackie, add new shipment status "courier fail"
            case 'courier_fail':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("courier fail"), "AND", "like");
              break;

            case 'delivery_in_progress':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "like");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.delivery_date,''))", 9, "AND", ">");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.received_date,''))", 9, "AND", "<=");
              break;
            case 'delivered':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "like");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.delivery_date,''))", 9, "AND", ">");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.received_date,''))", 9, "AND", ">");
              break;
            case 'received':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "like");
              break;
            default:
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc($shipment_status));
          }
        }
        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("COALESCE($this->tbl_as.d_order_id,'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl_as.nama,'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl_as.shipment_tranid,'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl_as.shipment_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first();
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }
    

    public function getAllForRejectSeller($nation_code, $page=0, $pagesize=10, $sortCol="id", $sortDir="desc", $keyword="", $cdate_start="", $cdate_end="", $settlement_status="")
    {
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'/',$this->tbl_as.id)", "id", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.total_item", "total_item", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("($this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add)", "cost", 0);
        $this->db->select_as("$this->tbl_as.refund_amount", "refund_amount", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "resolution", 0);
        $this->db->select_as("''", "action", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("rejected"));
        if (strlen($settlement_status)>0) {
            $this->db->where_as("$this->tbl_as.settlement_status", $this->db->esc($settlement_status));
        }
        if (strlen($cdate_start)==10 && strlen($cdate_end)==10) {
            $this->db->between("DATE($this->tbl3_as.cdate)", "DATE('$cdate_start')", "DATE('$cdate_end')");
        } elseif (strlen($cdate_start)==10 && strlen($cdate_end)!=10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$cdate_start')", "AND", ">=");
        } elseif (strlen($cdate_start)!=10 && strlen($cdate_end)==10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$cdate_end')", "AND", "<=");
        }
        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("COALESCE($this->tbl3_as.invoice_code,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get('', 0);
    }

    public function countAllForRejectSeller($nation_code, $keyword="", $cdate_start="", $cdate_end="", $settlement_status="")
    {
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("rejected"));
        if (strlen($settlement_status)>0) {
            $this->db->where_as("$this->tbl_as.settlement_status", $this->db->esc($settlement_status));
        }
        if (strlen($cdate_start)==10 && strlen($cdate_end)==10) {
            $this->db->between("DATE($this->tbl3_as.cdate)", "DATE('$cdate_start')", "DATE('$cdate_end')");
        } elseif (strlen($cdate_start)==10 && strlen($cdate_end)!=10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$cdate_start')", "AND", ">=");
        } elseif (strlen($cdate_start)!=10 && strlen($cdate_end)==10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$cdate_end')", "AND", "<=");
        }
        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("COALESCE($this->tbl3_as.invoice_code,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first();
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }
    
    public function getById($nation_code, $d_order_id, $id)
    {
        $this->db->where('nation_code', $nation_code);
        $this->db->where('d_order_id', $d_order_id);
        $this->db->where('id', $id);
        return $this->db->get_first();
    }
    public function getByOrderIdProdukId($nation_code, $d_order_id, $c_produk_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "subtotal", 0);
        $this->db->select_as("COALESCE($this->tbl_as.delivery_date,'-')", "delivery_date", 0);
        $this->db->select_as("$this->tbl_as.nama", "produk_nama", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl3_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl3_as.payment_status", "payment_status", 0);
        $this->db->select_as($this->__decrypt("$this->tbl8_as.nomor"), "bank_acc_nomor_buyer", 0);
        $this->db->select_as($this->__decrypt("$this->tbl8_as.nama"), "bank_acc_nama_buyer", 0);
        $this->db->select_as($this->__decrypt("$this->tbl9_as.nomor"), "bank_acc_nomor_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl9_as.nama"), "bank_acc_nama_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl4_as.fnama"), "b_user_fnama_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "b_user_fnama_buyer", 0);
        $this->db->select_as("COALESCE($this->tbl8_as.a_bank_id,'0')", "a_bank_id_buyer", 0);
        $this->db->select_as("COALESCE($this->tbl9_as.a_bank_id,'0')", "a_bank_id_seller", 0);
        $this->db->select_as("COALESCE($this->tbl10_as.nama,'-')", "a_bank_nama_buyer", 0);
        $this->db->select_as("COALESCE($this->tbl11_as.nama,'-')", "a_bank_nama_seller", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "inner");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "inner");
        $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "left");
        $this->db->join_composite($this->tbl9, $this->tbl9_as, $this->__joinTbl9(), "left");
        $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), "left");
        $this->db->join_composite($this->tbl11, $this->tbl11_as, $this->__joinTbl11(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($c_produk_id));
        return $this->db->get_first();
    }
    public function getByOrderId($nation_code, $d_order_id)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        return $this->db->get();
    }
    public function getByOrderIdForPayment($d_order_id)
    {
        $this->db->select_as('cp.* ,'.$this->tbl_as.'.*, cp.id', 'c_produk_id', 0);
        $this->db->select_as('do.* ,'.$this->tbl_as.'.*, do.id', 'd_order_id', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where('d_order_id', $d_order_id);
        $this->db->join('c_produk', 'cp', 'id', $this->tbl_as, 'c_produk_id', 'left');
        $this->db->join('d_order', 'do', 'id', $this->tbl_as, 'd_order_id', 'left');
        return $this->db->get();
    }
    public function getByOrderIdForCancellation($d_order_id)
    {
        $this->db->select_as('cp.* ,'.$this->tbl_as.'.*, cp.id', 'c_produk_id', 0);
        $this->db->select_as('do.* ,'.$this->tbl_as.'.*, do.id', 'd_order_id', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where('d_order_id', $d_order_id);
        $this->db->join('c_produk', 'cp', 'id', $this->tbl_as, 'c_produk_id', 'left');
        $this->db->join('d_order', 'do', 'id', $this->tbl_as, 'd_order_id', 'left');
        return $this->db->get();
    }
    public function getStatusByOrderIdProdukIdForCancellation($nation_code, $d_order_id, $c_produk_id)
    {
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($c_produk_id));
        return $this->db->get_first();
    }
    public function exportXls($nation_code, $keyword="", $order_status="", $payment_status="")
    {
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'/',$this->tbl_as.id)", "id", 0);
        $this->db->select_as("$this->tbl3_as.ldate", "ldate", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("'0'", "paid_to_seller", 0);
        $this->db->select_as("'0'", "return_to_buyer", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "payment_cost", 0);
        $this->db->select_as($this->__decrypt("$this->tbl4_as.fnama"), "seller_name", 0);
        $this->db->select_as("'0'", "cancel_fee", 0);
        $this->db->select_as("$this->tbl3_as.order_status", "order_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        if (strlen($order_status)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", $this->db->esc($order_status), "AND", "=", 0, 0);
        }
        if (strlen($payment_status)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.payment_status,'-')", $this->db->esc($payment_status), "AND", "=", 0, 0);
        }
        if (strlen($keyword)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.invoice_code,'-')", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as($this->__decrypt("$this->tbl4_as.fnama"), addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        return $this->db->get();
    }
    public function getSalesBySeller($nation_code, $b_user_id)
    {
        $this->db->select_as("COALESCE(SUM($this->tbl_as.sub_total),0)", "sales_total", 0);
        $this->db->select_as("COALESCE(SUM($this->tbl_as.shipment_cost),0)", "ongkir", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"));
        $this->db->where_as("$this->tbl_as.buyer_confirmed", $this->db->esc("confirmed"));
        return $this->db->get_first('', 0);
    }
    public function countSalesBySeller($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(*)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"));
        $this->db->where_as("$this->tbl_as.buyer_confirmed", $this->db->esc("confirmed"));
        $d = $this->db->get_first('', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function countRejectedBySeller($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(*)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("rejected"));
        $d = $this->db->get_first('', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function countConfirmedBySeller($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(*)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"));
        $d = $this->db->get_first('', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getAllForHistoryTRX($nation_code, $page=0, $pagesize=10, $sortCol="id", $sortDir="desc", $keyword="", $payment_status="", $order_status="", $seller_status="", $shipment_status="", $buyer_confirmed="", $settlement_status="", $sdate="", $edate="")
    {
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'/',$this->tbl_as.id)", "id", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("($this->tbl_as.sub_total)", "sub_total", 0);
        $this->db->select_as("($this->tbl_as.shipment_cost+$this->tbl_as.shipment_cost_add)", "shipment_cost", 0);
        $this->db->select_as("$this->tbl_as.profit_amount", "profit_amount", 0);
        $this->db->select_as("$this->tbl_as.earning_total", "earning_total", 0);
        $this->db->select_as("$this->tbl_as.refund_amount", "refund_amount", 0);
        $this->db->select_as("$this->tbl_as.banktrf_cost", "banktrf_cost", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.order_status,'-')", "order_status", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.payment_status,'-')", "payment_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);
        $this->db->select_as("''", "action", 0);
        $this->db->select_as("COALESCE($this->tbl_as.delivery_date,'')", "delivery_date", 0);
        $this->db->select_as("COALESCE($this->tbl_as.received_date,'')", "received_date", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "left");
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "left");

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("pending"), "AND", "<>");
        $this->db->where_as("$this->tbl7_as.address_status", $this->db->esc("A2"));

        if (strlen($payment_status)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.payment_status,'-')", $this->db->esc($payment_status), "AND", "=", 0, 0);
        }
        if (strlen($order_status)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", $this->db->esc($order_status), "AND", "=", 0, 0);
        }
        if (strlen($seller_status)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.seller_status,'-')", $this->db->esc($seller_status), "AND", "=", 0, 0);
        }
        if (strlen($shipment_status)) {
            switch ($shipment_status) {
            case 'not_yet_sent':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("process"), "OR", "like");
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "like");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.delivery_date,''))", 9, "AND", "<=");
              break;

            //By Donny Dennison - 08-07-2020 16:16
            //Request by Mr Jackie, add new shipment status "courier fail"
            case 'courier_fail':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("courier fail"), "AND", "like");
              break;

            case 'delivery_in_progress':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "like");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.delivery_date,''))", 9, "AND", ">");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.received_date,''))", 9, "AND", "<=");
              break;
            case 'delivered':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "like");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.delivery_date,''))", 9, "AND", ">");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.received_date,''))", 9, "AND", ">");
              break;
            case 'received':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "like");
              break;
            default:
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc($shipment_status));
          }
        }
        if (strlen($buyer_confirmed)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.buyer_confirmed,'-')", $this->db->esc($buyer_confirmed), "AND", "=", 0, 0);
        }
        if (strlen($settlement_status)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.settlement_status,'-')", $this->db->esc($settlement_status), "AND", "=", 0, 0);
        }
        if (strlen($sdate)==10 && strlen($edate)==10) {
            $this->db->between("DATE($this->tbl3_as.cdate)", "DATE('$sdate')", "DATE('$edate')");
        } elseif (strlen($sdate)==10 && strlen($edate)!=10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$sdate')", 'AND', '>=');
        } elseif (strlen($sdate)!=10 && strlen($edate)==10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$edate')", 'AND', '<=');
        }

        if (strlen($keyword)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.invoice_code,'-')", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as($this->__decrypt("$this->tbl4_as.fnama"), addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get();
    }

    public function countAllForHistoryTRX($nation_code, $keyword="", $payment_status="", $order_status="", $seller_status="", $shipment_status="", $buyer_confirmed="", $settlement_status="", $sdate="", $edate="")
    {
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "left");
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "left");

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("pending"), "AND", "<>");
        $this->db->where_as("$this->tbl7_as.address_status", $this->db->esc("A2"));

        if (strlen($payment_status)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.payment_status,'-')", $this->db->esc($payment_status), "AND", "=", 0, 0);
        }
        if (strlen($order_status)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", $this->db->esc($order_status), "AND", "=", 0, 0);
        }
        if (strlen($seller_status)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.seller_status,'-')", $this->db->esc($seller_status), "AND", "=", 0, 0);
        }
        if (strlen($shipment_status)) {
            switch ($shipment_status) {
            case 'not_yet_sent':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("process"), "OR", "like");
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "like");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.delivery_date,''))", 9, "AND", "<=");
              break;

            //By Donny Dennison - 08-07-2020 16:16
            //Request by Mr Jackie, add new shipment status "courier fail"
            case 'courier_fail':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("courier fail"), "AND", "like");
              break;
              
            case 'delivery_in_progress':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "like");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.delivery_date,''))", 9, "AND", ">");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.received_date,''))", 9, "AND", "<=");
              break;
            case 'delivered':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "like");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.delivery_date,''))", 9, "AND", ">");
              $this->db->where_as("LENGTH(COALESCE($this->tbl_as.received_date,''))", 9, "AND", ">");
              break;
            case 'received':
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "like");
              break;
            default:
              $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc($shipment_status));
          }
        }
        if (strlen($buyer_confirmed)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.buyer_confirmed,'-')", $this->db->esc($buyer_confirmed), "AND", "=", 0, 0);
        }
        if (strlen($settlement_status)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.settlement_status,'-')", $this->db->esc($settlement_status), "AND", "=", 0, 0);
        }
        if (strlen($sdate)==10 && strlen($edate)==10) {
            $this->db->between("DATE($this->tbl3_as.cdate)", "DATE('$sdate')", "DATE('$edate')");
        } elseif (strlen($sdate)==10 && strlen($edate)!=10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$sdate')", 'AND', '>=');
        } elseif (strlen($sdate)!=10 && strlen($edate)==10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$edate')", 'AND', '<=');
        }

        if (strlen($keyword)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.invoice_code,'-')", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as($this->__decrypt("$this->tbl4_as.fnama"), addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first();
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getAllForCancellation($nation_code, $page=0, $pagesize=10, $sortCol="id", $sortDir="desc", $keyword="", $payment_status="", $order_status="", $seller_status="", $shipment_status="", $buyer_confirmed="", $settlement_status="", $sdate="", $edate="")
    {
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'/',$this->tbl_as.id)", "id", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("($this->tbl_as.sub_total)", "sub_total", 0);
        $this->db->select_as("($this->tbl_as.shipment_cost+$this->tbl_as.shipment_cost_add)", "shipment_cost", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.order_status,'-')", "order_status", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.payment_status,'-')", "payment_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);
        $this->db->select_as("''", "action", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "left");
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        //$this->db->where_as("$this->tbl3_as.order_status",$this->db->esc("pending"),"AND","<>");
        $this->db->where_as("$this->tbl7_as.address_status", $this->db->esc("A2"));

        if (strlen($payment_status)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.payment_status,'-')", $this->db->esc($payment_status), "AND", "=", 0, 0);
        }
        if (strlen($order_status)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", $this->db->esc($order_status), "AND", "=", 0, 0);
        }
        if (strlen($seller_status)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.seller_status,'-')", $this->db->esc($seller_status), "AND", "=", 0, 0);
        }
        if (strlen($shipment_status)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.shipment_status,'-')", $this->db->esc($shipment_status), "AND", "=", 0, 0);
        }
        if (strlen($buyer_confirmed)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.buyer_confirmed,'-')", $this->db->esc($buyer_confirmed), "AND", "=", 0, 0);
        }
        if (strlen($settlement_status)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.settlement_status,'-')", $this->db->esc($settlement_status), "AND", "=", 0, 0);
        }
        if (strlen($sdate)==10 && strlen($edate)==10) {
            $this->db->between("DATE($this->tbl3_as.cdate)", "DATE('$sdate')", "DATE('$edate')");
        } elseif (strlen($sdate)==10 && strlen($edate)!=10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$sdate')", 'AND', '>=');
        } elseif (strlen($sdate)!=10 && strlen($edate)==10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$edate')", 'AND', '<=');
        }

        if (strlen($keyword)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.invoice_code,'-')", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as($this->__decrypt("$this->tbl4_as.fnama"), addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get('', 0);
    }

    public function countAllForCancellation($nation_code, $keyword="", $payment_status="", $order_status="", $seller_status="", $shipment_status="", $buyer_confirmed="", $settlement_status="", $sdate="", $edate="")
    {
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "left");
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl7_as.address_status", $this->db->esc("A2"));

        if (strlen($payment_status)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.payment_status,'-')", $this->db->esc($payment_status), "AND", "=", 0, 0);
        }
        if (strlen($order_status)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", $this->db->esc($order_status), "AND", "=", 0, 0);
        }
        if (strlen($seller_status)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.seller_status,'-')", $this->db->esc($seller_status), "AND", "=", 0, 0);
        }
        if (strlen($shipment_status)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.shipment_status,'-')", $this->db->esc($shipment_status), "AND", "=", 0, 0);
        }
        if (strlen($buyer_confirmed)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.buyer_confirmed,'-')", $this->db->esc($buyer_confirmed), "AND", "=", 0, 0);
        }
        if (strlen($settlement_status)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.settlement_status,'-')", $this->db->esc($settlement_status), "AND", "=", 0, 0);
        }
        if (strlen($sdate)==10 && strlen($edate)==10) {
            $this->db->between("DATE($this->tbl3_as.cdate)", "DATE('$sdate')", "DATE('$edate')");
        } elseif (strlen($sdate)==10 && strlen($edate)!=10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$sdate')", 'AND', '>=');
        } elseif (strlen($sdate)!=10 && strlen($edate)==10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$edate')", 'AND', '<=');
        }

        if (strlen($keyword)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.invoice_code,'-')", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as($this->__decrypt("$this->tbl4_as.fnama"), addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first();
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function countAllForPayment($nation_code,$keyword,$seller_status="", $buyer_confirmed="", $settlement_status="", $scdate="", $ecdate="")
    {
        $confirmed = "confirmed";
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.d_order_id,'/',$this->tbl_as.id))", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
        $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "left");
        $this->db->join_composite($this->tbl9, $this->tbl9_as, $this->__joinTbl9(), "left");
        $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), "left");
        $this->db->join_composite($this->tbl11, $this->tbl11_as, $this->__joinTbl11(), "left");

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc($confirmed));
        if (strlen($seller_status)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.seller_status,'-')", $this->db->esc($seller_status), "AND", "=", 0, 0);
        }
        if (strlen($buyer_confirmed)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.buyer_confirmed,'-')", $this->db->esc($buyer_confirmed), "AND", "=", 0, 0);
        }
        if (strlen($settlement_status)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.settlement_status,'-')", $this->db->esc($settlement_status), "AND", "=", 0, 0);
        }

        if (strlen($scdate)==10 && strlen($ecdate)==10) {
            $this->db->between("DATE($this->tbl3_as.cdate)", "DATE('$scdate')", "DATE('$ecdate')");
        } elseif (strlen($scdate)==10 && strlen($ecdate)!=10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$scdate')", 'AND', '>=');
        } elseif (strlen($scdate)!=10 && strlen($ecdate)==10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$ecdate')", 'AND', '<=');
        }

        if (strlen($keyword)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.invoice_code,'-')", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE(".$this->__decrypt("$this->tbl4_as.fnama").",'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first();
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getAllForPayment($nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword,$seller_status="", $buyer_confirmed="", $settlement_status="", $scdate="", $ecdate="")
    {
        $confirmed = "confirmed";
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'/',$this->tbl_as.id)", "id", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "cdate", 0);
        $this->db->select_as("CONCAT($this->tbl3_as.invoice_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.id)", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.total_item", "total_item", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "total_qty", 0);
        $this->db->select_as("($this->tbl_as.sub_total)", "sub_total", 0);
        $this->db->select_as("($this->tbl_as.shipment_cost+$this->tbl_as.shipment_cost_add)", "shipment_cost", 0);
        $this->db->select_as("$this->tbl_as.profit_amount", "profit_amount", 0);
        $this->db->select_as("$this->tbl_as.earning_total", "earning_total", 0);
        $this->db->select_as("$this->tbl_as.refund_amount", "refund_amount", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        //$this->db->select_as("$this->tbl_as.id", "id_data", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
        $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "left");
        $this->db->join_composite($this->tbl9, $this->tbl9_as, $this->__joinTbl9(), "left");
        $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), "left");
        $this->db->join_composite($this->tbl11, $this->tbl11_as, $this->__joinTbl11(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc($confirmed));
        if (strlen($seller_status)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.seller_status,'-')", $this->db->esc($seller_status), "AND", "=", 0, 0);
        }
        if (strlen($buyer_confirmed)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.buyer_confirmed,'-')", $this->db->esc($buyer_confirmed), "AND", "=", 0, 0);
        }
        if (strlen($settlement_status)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.settlement_status,'-')", $this->db->esc($settlement_status), "AND", "=", 0, 0);
        }

        if (strlen($scdate)==10 && strlen($ecdate)==10) {
            $this->db->between("DATE($this->tbl3_as.cdate)", "DATE('$scdate')", "DATE('$ecdate')");
        } elseif (strlen($scdate)==10 && strlen($ecdate)!=10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$scdate')", 'AND', '>=');
        } elseif (strlen($scdate)!=10 && strlen($ecdate)==10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('$ecdate')", 'AND', '<=');
        }

        if (strlen($keyword)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.invoice_code,'-')", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE(".$this->__decrypt("$this->tbl4_as.fnama").",'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get('', 0);
    }

    public function getDetailByID($nation_code, $d_order_id, $c_produk_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "d_order_detail_id");
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code");
        $this->db->select_as("$this->tbl3_as.order_status", "order_status");
        $this->db->select_as("$this->tbl3_as.payment_status", "payment_status");

        //by Donny Dennison - 29 april 2021 14:06
        //add-void-and-refund-2c2p-after-reject-by-seller
        $this->db->select_as("COALESCE($this->tbl3_as.b_user_id,0)", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.cdate,0)", "cdate", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.payment_date,0)", "payment_date", 0);

        //by Donny Dennison - 16 February 2020 15:50
        //fix reject by seller didnt deduct the total
        //START by Donny Dennison - 16 February 2020 15:50
        $this->db->select_as("$this->tbl3_as.pg_fee_vat", "pg_fee_vat", 0);
        $this->db->select_as("$this->tbl3_as.sub_total", "sub_total_order", 0);
        $this->db->select_as("$this->tbl3_as.selling_fee_percent", "selling_fee_percent_order", 0);
        $this->db->select_as("$this->tbl3_as.ongkir_total", "ongkir_total_order", 0);
        $this->db->select_as("$this->tbl3_as.grand_total", "grand_total_order", 0);
        $this->db->select_as("$this->tbl3_as.refund_amount", "refund_amount_order", 0);

        //by Donny Dennison - 29 april 2021 14:06
        //add-void-and-refund-2c2p-after-reject-by-seller
        $this->db->select_as("$this->tbl3_as.payment_tranid", "payment_tranid", 0);
        $this->db->select_as("$this->tbl3_as.payment_method", "payment_method", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($c_produk_id));
        return $this->db->get_first();
    }
    public function update($nation_code, $d_order_id, $c_produk_id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("id", $c_produk_id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function getPerMonth($nation_code, $year=2019, $cache_save=1)
    {
        $this->db->cache_save = $cache_save;
        $this->db->select_as("MONTH(cdate)", "month", 0);
        $this->db->select_as("SUM($this->tbl_as.selling_fee)", "earnings", 0);
        $this->db->select_as("SUM($this->tbl_as.sub_total)", "sales", 0);
        $this->db->select_as("SUM($this->tbl_as.shipment_cost) + SUM($this->tbl_as.shipment_cost_add)", "shipment_cost", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl3_as.payment_status", $this->db->esc("paid"));
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"));
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"));
        $this->db->where_as("$this->tbl_as.buyer_confirmed", $this->db->esc("confirmed"));
        $this->db->where_as("YEAR($this->tbl3_as.cdate)", $this->db->esc($year));
        $this->db->where_in("$this->tbl3_as.order_status", array("forward_to_seller","completed"));
        $this->db->group_by("MONTH($this->tbl3_as.cdate)");
        return $this->db->get();
    }
    /*Edited By Aditya Adi Prabowo 5/8/2020 16:25
      Improve Filter By Date and Change Value In Summary Report
      START IMPROVE*/
    public function getEarnings($nation_code, $sdate="", $edate="", $cache_save=1)
    {
        $this->db->cache_save = $cache_save;
        $this->db->select_as("SUM($this->tbl_as.grand_total)", "earnings", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        if (strlen($sdate)==10 && strlen($edate)==10) {
            $this->db->between("DATE(COALESCE($this->tbl_as.forward_to_seller_date))", "DATE('$sdate')", "DATE('$edate')");
        }
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"));
        $this->db->where_as("$this->tbl_as.settlement_status", $this->db->esc("unconfirmed"));
        $d =  $this->db->get_first('', 0);
        if (isset($d->earnings)) {
            return $d->earnings;
        }
        return 0;
    }
     public function getSales($nation_code, $sdate="", $edate="", $cache_save=1)
    {
        $this->db->cache_save = $cache_save;
        $this->db->select_as("COALESCE(SUM($this->tbl_as.grand_total),0)", "grand_total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        if (strlen($sdate)==10 && strlen($edate)==10) {
            $this->db->between("DATE(COALESCE($this->tbl_as.forward_to_seller_date))", "DATE('$sdate')", "DATE('$edate')");
        }
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"));
        $d =  $this->db->get_first('', 0);
        if (isset($d->grand_total)) {
            return $d->grand_total;
        }
        return 0;
    }

    public function getByIdChat($nation_code, $d_order_id)
    {
        $this->db->where('nation_code', $nation_code);
        $this->db->where('d_order_id', $d_order_id);
        return $this->db->get_first();
    }

    public function getUnPaid($nation_code, $sdate="", $edate="", $cache_save=1)
    {
        $asyawau = 1;
        $confirmed = "confirmed";
        $this->db->cache_save = $cache_save;
        $this->db->select_as("COALESCE(SUM($this->tbl_as.profit_amount),0)", "grand_total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        if (strlen($sdate)==10 && strlen($edate)==10) {
            $this->db->between("DATE(COALESCE($this->tbl_as.forward_to_seller_date,NOW()))", "DATE('$sdate')", "DATE('$edate')");
        }
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc($confirmed));
        $this->db->where_as("$this->tbl_as.id", $asyawau);

        $d =  $this->db->get_first('', 0);
        if (isset($d->grand_total)) {
            return $d->grand_total;
        }
        return 0;
    }
    /*END OF EDIT*/

    public function getLatestDashboard($nation_code, $page=0, $pagesize=10, $sortCol="kode", $sortDir="ASC", $keyword="", $sdate="", $edate="", $in_order_status=array(), $b_user_id="")
    {
        $this->db->flushQuery();
        $this->db->cache_save = 0;
        $this->db->select_as("$this->tbl_as.id", 'id', 0);
        $this->db->select_as("$this->tbl_as.b_user_id", 'b_user_id', 0);
        $this->db->select_as($this->__decrypt("$this->tbl4_as.fnama"), 'penerima_nama', 0);
        $this->db->select_as("$this->tbl_as.invoice_code", 'invoice_code', 0);
        $this->db->select_as("$this->tbl_as.item_total", 'item_total', 0);
        $this->db->select_as("$this->tbl_as.sub_total", 'sub_total', 0);
        $this->db->select_as("$this->tbl_as.ongkir_total", 'ongkir_total', 0);
        $this->db->select_as("$this->tbl_as.grand_total", 'grand_total', 0);
        $this->db->select_as("$this->tbl_as.payment_gateway", 'payment_gateway', 0);
        $this->db->select_as("$this->tbl_as.order_status", 'order_status', 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        if (is_array($in_order_status) && count($in_order_status)) {
            $this->db->where_in("$this->tbl_as.order_status", $in_order_status);
        }
        if (!empty($sdate)) {
            if (empty($edate)) {
                $edate = 'NOW()';
            }
            $sdate = 'DATE("'.$sdate.'")';
            $edate = 'DATE("'.$edate.'")';
            $this->db->between("$this->tbl_as.cdate", $sdate, $edate, $is_not=0);
        }
        if (!empty($b_user_id)) {
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), 'AND', '=', 0, 0);
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get("object", 0);
    }


    //by Donny Dennison - 29 april 2021 14:06
    //add-void-and-refund-2c2p-after-reject-by-seller
    public function updateByOrderId($nation_code, $d_order_id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        return $this->db->update($this->tbl, $du, 0);
    }
    
    //by Donny Dennison - 29 april 2021 14:06
    //add-void-and-refund-2c2p-after-reject-by-seller
    public function countTotalSeller($nation_code, $d_order_id, $seller_status='')
    {
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.id", $this->db->esc($d_order_id), "AND", "=");

        if($seller_status != ''){
            $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc($seller_status), "AND", "=", 0, 0);
        }

        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

}
