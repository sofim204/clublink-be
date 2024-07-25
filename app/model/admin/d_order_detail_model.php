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

    public function update($nation_code, $d_order_id, $id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }

    public function getById($nation_code, $id)
    {
        $this->db->where('nation_code', $nation_code);
        $this->db->where('id', $id);
        return $this->db->get_first();
    }
    public function getByOrderId($nation_code, $d_order_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "c_produk_id");
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller");
        $this->db->select_as("'media/produk/default.png'", "foto", 0);
        $this->db->select_as("'media/produk/default.png'", "thumb", 0);
        $this->db->select_as("'-'", "stok", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as($this->__decrypt("$this->tbl4_as.fnama"), "fnama", 0);
        $this->db->select_as("$this->tbl4_as.image", "image", 0);
        $this->db->select_as("$this->tbl4_as.is_active", "b_user_is_active", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_As("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        return $this->db->get();
    }
    public function getDetailByOrderId($nation_code, $d_order_id, $id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "c_produk_id");
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller");
        $this->db->select_as("'-'", "stok", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as($this->__decrypt("$this->tbl4_as.fnama"), "fnama", 0);
        $this->db->select_as("$this->tbl4_as.image", "image", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        $this->db->where_as("$this->tbl_as.id", $id);
        return $this->db->get_first('', 0);
    }

    public function exportXls($nation_code, $keyword="", $order_status="", $payment_status="")
    {
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'-',$this->tbl_as.id)", "id", 0);
        $this->db->select_as("$this->tbl3_as.ldate", "ldate", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("'0'", "paid_to_seller", 0);
        $this->db->select_as("'0'", "return_to_buyer", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total*$this->tbl_as.total_qty", "payment_cost", 0);
        $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl4_as.fnama").",'-')", "seller_name", 0);
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
            $this->db->where_as("COALESCE(".$this->__decrypt("$this->tbl4_as.fnama").",'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        return $this->db->get();
    }

    public function exportXlsHistoryTRX($nation_code, $keyword="", $shipment_status="", $seller_status="", $buyer_confirmed="", $order_status="", $payment_status="")
    {
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'-',$this->tbl_as.id)", "id", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("CONCAT('Merged Product: ',$this->tbl_as.d_order_id,'-',$this->tbl_as.id)", "nama", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("'0'", "paid_to_seller", 0);
        $this->db->select_as("'0'", "return_to_buyer", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total*$this->tbl_as.total_qty", "payment_cost", 0);
        $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl4_as.fnama").",'-')", "seller_name", 0);
        $this->db->select_as("COALESCE($this->tbl_as.cancel_fee,'-')", "cancel_fee", 0);
        $this->db->select_as("$this->tbl3_as.order_status", "order_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        if (strlen($shipment_status)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.shipment_status,'-')", $this->db->esc($shipment_status), "AND", "=", 0, 0);
        }
        if (strlen($seller_status)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.seller_status,'-')", $this->db->esc($seller_status), "AND", "=", 0, 0);
        }
        if (strlen($buyer_confirmed)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.buyer_confirmed,'-')", $this->db->esc($buyer_confirmed), "AND", "=", 0, 0);
        }
        if (strlen($order_status)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", $this->db->esc($order_status), "AND", "=", 0, 0);
        }
        if (strlen($payment_status)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.payment_status,'-')", $this->db->esc($payment_status), "AND", "=", 0, 0);
        }
        if (strlen($keyword)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.invoice_code,'-')", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("CONCAT('Merged Product: ',$this->tbl_as.d_order_id,'-',$this->tbl_as.id)", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE(".$this->__decrypt("$this->tbl4_as.fnama").",'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        return $this->db->get();
    }

    public function exportXlsPayment($nation_code, $keyword="", $seller_status="", $buyer_confirmed="", $settlement_status="", $scdate="", $ecdate="")
    {
        $confirmed = "confirmed";
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'/',$this->tbl_as.id)", "id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "subtotal", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl_as.grand_total", "grand_total", 0);
        $this->db->select_as("($this->tbl_as.shipment_cost+$this->tbl_as.shipment_cost_add)", "shipment_cost", 0);
        $this->db->select_as("$this->tbl_as.pg_fee", "pg_fee", 0);
        $this->db->select_as("$this->tbl_as.profit_amount", "profit_amount", 0);
        $this->db->select_as("$this->tbl_as.earning_total", "earning_total", 0);
        $this->db->select_as("$this->tbl_as.cancel_fee", "cancel_fee", 0);
        $this->db->select_as("$this->tbl_as.refund_amount", "refund_amount", 0);
        $this->db->select_as("$this->tbl_as.banktrf_cost", "banktrf_cost", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.order_status,'-')", "order_status", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.payment_status,'-')", "payment_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);

        //by Donny Dennison - 7 may 2021
        //change encryption key
        // $this->db->select_as("COALESCE(AES_DECRYPT($this->tbl9_as.nama,''),' ')", "rekening_nama_seller", 0);
        // $this->db->select_as("COALESCE(AES_DECRYPT($this->tbl9_as.nomor,''),' ')", "rekening_nomor_seller", 0);

        // by Muhammad Sofi 21 December 2021 14:30 | fix error while click button Seller Payment(s)
        // $this->db->select_as($this->__decrypt('$this->tbl9_as.nama'), 'rekening_nama_seller', 0);
        // $this->db->select_as($this->__decrypt('$this->tbl9_as.nomor'), 'rekening_nomor_seller', 0);
        $this->db->select_as($this->__decrypt("$this->tbl9_as.nama"), "rekening_nama_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl9_as.nomor"), "rekening_nomor_seller", 0);

        $this->db->select_as("COALESCE($this->tbl11_as.nama,'')", "rekening_bank_seller", 0);
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
        //var_dump($this->db->get('object', 1)); die();
        return $this->db->get('', 0); 
    }

    public function exportXlsPG($nation_code, $keyword="", $seller_status="", $buyer_confirmed="", $settlement_status="", $scdate="", $ecdate="")
    {
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'/',$this->tbl_as.id)", "id", 0);
        $this->db->select_as("$this->tbl3_as.*, ($this->tbl3_as.pg_fee + $this->tbl3_as.pg_fee_vat)", "pg_cost", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
        $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "left");
        $this->db->join_composite($this->tbl9, $this->tbl9_as, $this->__joinTbl9(), "left");
        $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), "left");
        $this->db->join_composite($this->tbl11, $this->tbl11_as, $this->__joinTbl11(), "left");

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
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
        return $this->db->get('', 0);
    }

    public function exportXlsCancellation($nation_code, $keyword="", $shipment_status="", $seller_status="", $buyer_confirmed="", $order_status="", $payment_status="", $settlement_status="", $cdate_start="", $cdate_end="")
    {
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'-',$this->tbl_as.id)", "id", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.grand_total", "sub_total", 0);
        $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl4_as.fnama").",'-')", "seller_name", 0);
        $this->db->select_as("COALESCE($this->tbl_as.cancel_fee,'-')", "cancel_fee", 0);
        $this->db->select_as("$this->tbl3_as.payment_status", "payment_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        if (strlen($order_status)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", $this->db->esc($order_status), "AND", "=", 0, 0);
        }
        if (strlen($payment_status)>0) {
            $this->db->where_as("COALESCE($this->tbl3_as.payment_status,'-')", $this->db->esc($payment_status), "AND", "=", 0, 0);
        }
        if (strlen($seller_status)>0) {
            $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc($seller_status), "AND", "=", 0, 0);
        }
        if (strlen($shipment_status)>0) {
            $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc($shipment_status), "AND", "=", 0, 0);
        }
        if (strlen($buyer_confirmed)>0) {
            $this->db->where_as("$this->tbl_as.buyer_confirmed", $this->db->esc($buyer_confirmed), "AND", "=", 0, 0);
        }
        if (strlen($settlement_status)>0) {
            $this->db->where_as("$this->tbl_as.settlement_status", $this->db->esc($settlement_status), "AND", "=", 0, 0);
        }

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
            $this->db->where_as("COALESCE(".$this->__decrypt("$this->tbl4_as.fnama").",'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        return $this->db->get('', 0);
    }
}
