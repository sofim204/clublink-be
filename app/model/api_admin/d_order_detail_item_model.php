<?php
class D_Order_Detail_Item_Model extends JI_Model
{
    public $is_cacheable;
    public $tbl = 'd_order_detail_item';
    public $tbl_as = 'dodi';
    public $tbl2 = 'd_order_detail';
    public $tbl2_as = 'dod';
    public $tbl3 = 'd_order';
    public $tbl3_as = 'dor';
    public $tbl4 = 'c_produk';
    public $tbl4_as = 'cp';
    public $tbl5 = 'b_user';
    public $tbl5_as = 'bu';
    public $tbl6 = 'c_produk';
    public $tbl6_as = 'bu';

    public function __construct()
    {
        parent::__construct();
        $this->is_cacheable = 0;
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function getTableAlias()
    {
        return $this->tbl_as;
    }

    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.d_order_id", "=", "$this->tbl2_as.d_order_id");
        $cps[] = $this->db->composite_create("$this->tbl_as.d_order_detail_id", "=", "$this->tbl2_as.id");
        return $cps;
    }
    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl2_as.d_order_id", "=", "$this->tbl3_as.id");
        return $cps;
    }

    //for produk
    private function __joinTbl4()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.c_produk_id", "=", "$this->tbl4_as.id");
        return $cps;
    }

    //for seller, requires joinTbl4
    private function __joinTbl5()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl4_as.nation_code", "=", "$this->tbl5_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl4_as.b_user_id", "=", "$this->tbl5_as.id");
        return $cps;
    }

    private function __joinTbl6()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl6_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.c_produk_id", "=", "$this->tbl6_as.id");
        return $cps;
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
    /**
     * Update single rows of d_order_detail_item
     * @param  [type] $nation_code       [description]
     * @param  [type] $d_order_id        [description]
     * @param  [type] $d_order_detail_id [description]
     * @param  [type] $c_produk_id       [description]
     * @param  [type] $du                array of string that contain name value pair
     * @return [type]                    [description]
     */
    public function update($nation_code, $d_order_id, $d_order_detail_id, $c_produk_id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("d_order_detail_id", $d_order_detail_id);
        $this->db->where("c_produk_id", $c_produk_id);
        return $this->db->update($this->tbl, $du, 0);
    }
    /**
     * Update table row(s) on d_order_detail_item
     * @param  [type] $nation_code       [description]
     * @param  [type] $d_order_id        [description]
     * @param  [type] $d_order_detail_id [description]
     * @param  [type] $du                array of string that contain name value pair
     * @return boolean
     */
    public function updateByOrderDetailId($nation_code, $d_order_id, $d_order_detail_id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("d_order_detail_id", $d_order_detail_id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function getByOrderIdDetailId($nation_code, $d_order_id, $d_order_detail_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("d_order_detail_id", $d_order_detail_id);
        return $this->db->get();
    }

    /**
     * Get data for rejected item(s) by buyer
     * @param  [type]  $nation_code       [description]
     * @param  integer $page              [description]
     * @param  integer $pagesize          [description]
     * @param  string  $sortCol           [description]
     * @param  string  $sortDir           [description]
     * @param  string  $keyword           [description]
     * @param  string  $sdate             start date YYYY-MM-DD
     * @param  string  $edate             end date YYYY-MM-DD
     * @param  string  $settlement_status [description]
     * @return array                     array of object
     */
    public function getAllForRejectBuyer($nation_code, $page=0, $pagesize=10, $sortCol="id", $sortDir="desc", $keyword="", $sdate="", $edate="", $settlement_status="")
    {
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'/',$this->tbl_as.d_order_detail_id,'/',$this->tbl_as.c_produk_id)", "id", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "cdate", 0);
        $this->db->select_as("CONCAT(COALESCE($this->tbl3_as.invoice_code,'-'),'-',$this->tbl_as.d_order_detail_id,'-',$this->tbl_as.c_produk_id)", "invoice_code", 0);
        $this->db->select_as("$this->tbl4_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->select_as("($this->tbl_as.qty * $this->tbl_as.harga_jual)", "sub_total", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "resolution", 0);
        $this->db->select_as("''", "action", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

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

        $this->db->where_as("$this->tbl2_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 1, 0);
        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("rejected"), "AND", "=", 0, 1);

        if (strlen($keyword)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.invoice_code,'-')", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("CONCAT(COALESCE($this->tbl3_as.invoice_code,'-'),'-',$this->tbl_as.d_order_detail_id,'-',$this->tbl_as.c_produk_id)", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl4_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }

        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get('', 0);
    }
    /**
     * Count row from d_order_detail_item for rejected item(s) by buyer
     * @param  int $nation_code       [description]
     * @param  string $keyword           [description]
     * @param  string $sdate             start date
     * @param  string $edate             end date
     * @param  string $settlement_status [description]
     * @return int                    number of rows
     */
    public function countAllForRejectBuyer($nation_code, $keyword="", $sdate="", $edate="", $settlement_status="")
    {

        //by Donny Dennison - 2 march 2021 10:52
        //add need action column in dashboard
        // $this->db->select_as("COUNT(DISTINCT $this->tbl_as.c_produk_id)", "jumlah", 0);
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.d_order_id,'/',$this->tbl_as.d_order_detail_id,'/',$this->tbl_as.c_produk_id))", "jumlah", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "inner");

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

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

        $this->db->where_as("$this->tbl2_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 1, 0);
        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("rejected"), "AND", "=", 0, 1);

        if (strlen($keyword)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.invoice_code,'-')", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("CONCAT(COALESCE($this->tbl3_as.invoice_code,'-'),'-',$this->tbl_as.d_order_detail_id,'-',$this->tbl_as.c_produk_id)", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl4_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first();
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function countAllForPayment($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $payment_status, $order_status, $seller_status, $shipment_status, $buyer_confirmed, $settlement_status, $cdate_start, $cdate_end)
    {
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.c_produk_id)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "inner");

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("rejected"), "AND", "=", 0, 0);

        if (strlen($settlement_status)>0) {
            $this->db->where_as("COALESCE($this->tbl2_as.settlement_status,'-')", $this->db->esc($settlement_status), "AND", "=", 0, 0);
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
            $this->db->where_as("CONCAT(COALESCE($this->tbl3_as.invoice_code,'-'),'-',$this->tbl_as.d_order_detail_id,'-',$this->tbl_as.c_produk_id)", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl4_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $this->db->where_in("$this->tbl_as.settlement_status", array("complain","wait"));
        $d = $this->db->get_first();
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getAllForPayment($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $payment_status, $order_status, $seller_status, $shipment_status, $buyer_confirmed, $settlement_status, $cdate_start, $cdate_end)
    {
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'/',$this->tbl_as.d_order_detail_id,'/',$this->tbl_as.c_produk_id)", "id", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "cdate", 0);
        $this->db->select_as("CONCAT(COALESCE($this->tbl3_as.invoice_code,'-'),'-',$this->tbl_as.d_order_detail_id,'-',$this->tbl_as.c_produk_id)", "invoice_code", 0);
        $this->db->select_as("$this->tbl4_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->select_as("($this->tbl_as.qty * $this->tbl_as.harga_jual)", "sub_total", 0);
        $this->db->select_as("IF($this->tbl2_as.seller_status='rejected','Seller','Buyer')", "reject_status", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "resolution", 0);
        $this->db->select_as("''", "action", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "inner");

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("rejected"), "AND", "=", 0, 0);

        if (strlen($settlement_status)>0) {
            $this->db->where_as("COALESCE($this->tbl2_as.settlement_status,'-')", $this->db->esc($settlement_status), "AND", "=", 0, 0);
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
            $this->db->where_as("CONCAT(COALESCE($this->tbl3_as.invoice_code,'-'),'-',$this->tbl_as.d_order_detail_id,'-',$this->tbl_as.c_produk_id)", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl4_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $this->db->where_in("$this->tbl_as.settlement_status", array("complain","wait"));

        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get('', 0);
    }

    public function getDetailByIdForCancellation($nation_code, $d_order_id, $d_order_detail_id, $c_produk_id)
    {
        $this->db->select_as("$this->tbl2_as.*, $this->tbl_as.*, CONCAT($this->tbl_as.d_order_id,'/',$this->tbl_as.d_order_detail_id,'/',$this->tbl_as.c_produk_id)", "id", 0);
        $this->db->select_as("$this->tbl_as.buyer_status", "buyer_status", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl3_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl3_as.payment_status", "payment_status", 0);
        $this->db->select_as("$this->tbl2_as.settlement_status", "settlement_status2", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $this->db->esc($d_order_detail_id));
        $this->db->where_as("$this->tbl_as.c_produk_id", $this->db->esc($c_produk_id));
        return $this->db->get_first();
    }

    public function getDetailByIdForPayment($nation_code, $d_order_id, $d_order_detail_id, $c_produk_id)
    {
        $this->db->select_as("$this->tbl2_as.*, $this->tbl_as.*, CONCAT($this->tbl_as.d_order_id,'/',$this->tbl_as.d_order_detail_id,'/',$this->tbl_as.c_produk_id)", "id", 0);
        $this->db->select_as("$this->tbl_as.buyer_status", "buyer_status", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl3_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl3_as.payment_status", "payment_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $this->db->esc($d_order_detail_id));
        $this->db->where_as("$this->tbl_as.c_produk_id", $this->db->esc($c_produk_id));
        return $this->db->get_first();
    }

    /**
     * get Best Seller Product(s)
     * @param  [type] $nation_code [description]
     * @return [type]              [description]
     */
    public function getBestSeller($nation_code)
    {
        $this->db->select_as("CONCAT($this->tbl_as.nation_code,'/',$this->tbl_as.c_produk_id)", "produk_sku");
        $this->db->select_as("$this->tbl_as.c_produk_id", "produk_id");
        $this->db->select_as("$this->tbl4_as.b_user_id", "b_user_id_seller");
        $this->db->select_as("$this->tbl4_as.nama", "produk_nama");
        $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "b_user_fnama_seller");
        $this->db->select_as("SUM($this->tbl_as.qty)", "qty");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "inner");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("accepted"));
        $this->db->order_by("SUM($this->tbl_as.qty)", "DESC");
        $this->db->group_by("$this->tbl_as.c_produk_id");
        $this->db->limit(0, 10);
        return $this->db->get();
    }

    public function exportXlsCancellation($nation_code, $keyword="", $shipment_status="", $seller_status="", $buyer_confirmed="", $order_status="", $payment_status="", $settlement_status="", $cdate_start="", $cdate_end="")
    {
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'-',$this->tbl_as.c_produk_id)", "id", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl4_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->select_as("$this->tbl2_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl2_as.grand_total", "grand_total", 0);
        $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "seller_name", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.cancel_fee,'-')", "cancel_fee", 0);
        $this->db->select_as("$this->tbl3_as.payment_status", "payment_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
        $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", $this->db->esc($order_status), "AND", "=", 0, 0);
        $this->db->where_as("COALESCE($this->tbl3_as.payment_status,'-')", $this->db->esc($payment_status), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.shipment_status", $this->db->esc("succeed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("rejected"), "AND", "=", 0, 0);

        if (strlen($cdate_start)==10 && strlen($cdate_end)==10) {
            $this->db->between("DATE($this->tbl3_as.cdate)", "DATE('$cdate_start')", "DATE('$cdate_end')");
        } elseif (strlen($cdate_start)==10 && strlen($cdate_end)!=10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('".$cdate_start."')", 'AND', '>=');
        } elseif (strlen($cdate_start)!=10 && strlen($cdate_end)==10) {
            $this->db->where_as("DATE($this->tbl3_as.cdate)", "DATE('".$cdate_end."')", 'AND', '<=');
        }

        if (strlen($keyword)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.invoice_code,'-')", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl_as.nama", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as($this->__decrypt("$this->tbl4_as.fnama"), addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        return $this->db->get('', 0);
    }

    public function getByOrderDetailId($nation_code, $d_order_id, $d_order_detail_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl2_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl2_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl2_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl2_as.refund_amount", "refund_amount", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl3_as.selling_fee", "order_selling_fee", 0);
        $this->db->select_as("$this->tbl3_as.pg_fee", "order_pg_fee", 0);
        $this->db->select_as("$this->tbl3_as.pg_fee_vat", "order_pg_fee_vat", 0);
        $this->db->select_as("$this->tbl3_as.profit_amount", "order_profit_amount", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $this->db->esc($d_order_detail_id));
        $this->db->order_by("$this->tbl_as.c_produk_id", "ASC");
        return $this->db->get();
    }

    public function getByOrderIdChat($nation_code, $d_order_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        return $this->db->get_first();
    }

    public function getByOrderId($nation_code,$d_order_id){
      $this->db->select_as("$this->tbl_as.nation_code","nation_code");
      $this->db->select_as("$this->tbl_as.d_order_id","d_order_id");
      $this->db->select_as("$this->tbl_as.d_order_detail_id","d_order_detail_id");
      $this->db->select_as("$this->tbl_as.c_produk_id","id");
      $this->db->select_as("$this->tbl_as.qty","qty");
      $this->db->select_as("$this->tbl4_as.stok","stok");
      $this->db->from($this->tbl,$this->tbl_as);
      $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "inner");
      $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
      $this->db->where_as("$this->tbl_as.d_order_id",$this->db->esc($d_order_id));
      return $this->db->get();
    }
    
    //Edit By Aditya Adi Prabowo 3/9/2020 1:14
    // Edit in Transation By Seller Menu
    // Start Edit
    public function getDetailBuyerStatus($id_temp_id, $id_temp_order_id)
    {
        $this->db->select_as("COUNT($this->tbl_as.buyer_status)", "reject", 0);
        $this->db->from($this->tbl,$this->tbl_as);
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $this->db->esc($id_temp_id));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($id_temp_order_id));
        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("rejected"));
        return $this->db->get_first();
    }

    public function getWait($id_temp_id, $id_temp_order_id)
    {
        $this->db->select_as("COUNT($this->tbl_as.buyer_status)", "wait", 0);
        $this->db->from($this->tbl,$this->tbl_as);
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $this->db->esc($id_temp_id));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($id_temp_order_id));
        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("wait"));
        return $this->db->get_first();
    }

    public function getCount($id_temp_id, $id_temp_order_id)
    {
        $this->db->select_as("COUNT($this->tbl_as.buyer_status)", "total", 0);
        $this->db->from($this->tbl,$this->tbl_as);
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $this->db->esc($id_temp_id));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($id_temp_order_id));
        return $this->db->get_first();
    }

    public function getAccept($id_temp_id, $id_temp_order_id)
    {
        $this->db->select_as("COUNT($this->tbl_as.buyer_status)", "accept", 0);
        $this->db->from($this->tbl,$this->tbl_as);
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $this->db->esc($id_temp_id));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($id_temp_order_id));
        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("accepted"));
        return $this->db->get_first();
    }
    // End Of Edit

    //by Donny dennison - 5 february 2021 - 17:31
    //change chat to open chatting
    public function getinvoiceajax($nation_code, $search, $user_id_buyer, $user_id_seller)
    {
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl3_as.id", "order_id", 0);
        $this->db->select_as("$this->tbl2_as.id", "order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "order_detail_item_id", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->where_as("$this->tbl3_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("pending"), "AND", "<>", 0, 0);
        
        if($user_id_buyer != 0){
            $this->db->where_as("$this->tbl3_as.b_user_id", $this->db->esc($user_id_buyer), "AND", "=", 0, 0);
        }

        if($user_id_seller != 0){
            $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($user_id_seller), "AND", "=", 0, 0);
        }
        

        if(strlen($search)>0){
            $this->db->where_as("$this->tbl3_as.invoice_code", $search, "OR", "%like%", 0, 0);
        }

        return $this->db->get();
    }

}
