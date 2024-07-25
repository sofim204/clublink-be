<?php
class D_Order_Model extends JI_Model
{
    public $tbl = 'd_order';
    public $tbl_as = 'dor';
    public $tbl2 = 'd_order_detail';
    public $tbl2_as = 'dod';
    public $tbl3 = 'c_produk';
    public $tbl3_as = 'cp';
    public $tbl4 = 'b_user';
    public $tbl4_as = 'bu';
    public $tbl5 = 'b_user';
    public $tbl5_as = 'bu2';
    public $tbl7 = 'd_order_alamat';
    public $tbl7_as = 'doa';
    public $tbl20 = 'b_user_alamat';
    public $tbl20_as = 'bua';
    public $tbl21 = 'b_lokasi';
    public $tbl21_as = 'blok';
    public $tbl22 = 'b_kodepos';
    public $tbl22_as = 'bkp';
    public $tbl23 = 'a_negara';
    public $tbl23_as = 'an';
    public $tbl24 = 'd_order_detail_produk';
    public $tbl24_as = 'dodp';
    public $tbl25 = 'b_user_alamat';
    public $tbl25_as = 'bua2';

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


    private function __joinTbl()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl2_as.d_order_id", "=", "$this->tbl_as.id");
        return $cps;
    }
    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.d_order_id");
        return $cps;
    }
    //buyer
    private function __joinTbl4()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl4_as.id");
        return $cps;
    }
    private function __joinTbl5()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl3_as.nation_code", "=", "$this->tbl5_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl3_as.b_user_id", "=", "$this->tbl5_as.id");
        return $cps;
    }
    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl2_as.id", "=", "$this->tbl3_as.id");
        return $cps;
    }
    private function __joinTbl7()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl7_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl7_as.d_order_id");
        return $cps;
    }
    private function __joinTbl20()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl4_as.nation_code", "=", "$this->tbl20_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl4_as.id", "=", "$this->tbl20_as.id");
        return $composites;
    }
    private function __joinTbl21()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl20_as.nation_code", "=", "$this->tbl21_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl20_as.b_lokasi_Id", "=", "$this->tbl21_as.id");
        return $composites;
    }
    private function __joinTbl22()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl20_as.nation_code", "=", "$this->tbl22_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl20_as.b_lokasi_Id", "=", "$this->tbl22_as.id");
        return $composites;
    }
    private function __joinTbl25()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl3_as.nation_code", "=", "$this->tbl25_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl3_as.b_user_alamat_id", "=", "$this->tbl25_as.id");
        $composites[] = $this->db->composite_create("$this->tbl3_as.b_user_id", "=", "$this->tbl25_as.b_user_id");
        return $composites;
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

    public function countByorder_status($order_status='order_konfirmasi')
    {
        $this->db->where('order_status', $order_status);
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return false;
    }

    public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="id", $sortDir="desc", $keyword="", $sdate="", $edate="", $in_order_status=array(), $b_user_id="")
    {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.ldate", "ldate", 0);
        $this->db->select_as($this->__decrypt("$this->tbl4_as.fnama"), "b_user_fnama_buyer", 0);
        $this->db->select_as("COALESCE($this->tbl7_as.alamat,'-')", "kirim_alamat", 0);
        $this->db->select_as("$this->tbl_as.payment_gateway", "payment_gateway", 0);
        $this->db->select_as("$this->tbl_as.item_total", "item_total", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl_as.ongkir_total", "ongkir_total", 0);
        $this->db->select_as("$this->tbl_as.grand_total", "grand_total", 0);
        $this->db->select_as("$this->tbl_as.order_status", "order_status", 0);
        $this->db->select_as("'-'", "action_text", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.order_status", $this->db->esc("pending"), "AND", "<>", 0, 0);
        $this->db->where_as("$this->tbl7_as.address_status", $this->db->esc("A2"), "AND", "=", 0, 0);
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
        if (strlen($keyword)>0) {
            $this->db->where_as($this->__decrypt("$this->tbl4_as.fnama"), addslashes($keyword), "OR", "%like%", 0, 0);
            // by Muhammad Sofi - 4 November 2021 10:00
            // remark code
            // $this->db->where_as("COALESCE($this->tbl7_as.alamat,'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as($this->__decrypt("$this->tbl7_as.alamat2"), addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.invoice_code", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get("object", 0);
    }
    public function countAll($nation_code, $keyword="", $sdate="", $edate="", $in_order_status=array(), $b_user_id="")
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.order_status", $this->db->esc("pending"), "AND", "<>", 0, 0);
        $this->db->where_as("$this->tbl7_as.address_status", $this->db->esc("A2"), "AND", "=", 0, 0);
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
        if (strlen($keyword)>0) {
            $this->db->where_as($this->__decrypt("$this->tbl4_as.fnama"), addslashes($keyword), "OR", "%like%", 1, 0);
            // by Muhammad Sofi - 4 November 2021 10:00
            // remark code
            // $this->db->where_as("COALESCE($this->tbl7_as.alamat,'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as($this->__decrypt("$this->tbl7_as.alamat2"), addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.invoice_code", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getAllBuyer($nation_code, $page=0, $pagesize=10, $sortCol="id", $sortDir="desc", $keyword="", $sdate="", $edate="", $payment_status="", $payment_gateway="", $order_status="")
    {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as($this->__decrypt("$this->tbl4_as.fnama"), "b_user_fnama_buyer", 0);

        //By Donny Dennison - 27 juni 2020 3:23
        //not using alamat again
        // $this->db->select_as("COALESCE($this->tbl7_as.alamat,'-')", "kirim_alamat", 0);
        $this->db->select_as($this->__decrypt("$this->tbl7_as.alamat2"), "kirim_alamat", 0);
        
        //by Donny Dennison - 2 november 2020 16:03
        //add payment 2c2p grab pay
        // $this->db->select_as("$this->tbl_as.payment_gateway", "payment_gateway", 0);
        $this->db->select_as("IF($this->tbl_as.payment_method != 'Grab Pay',$this->tbl_as.payment_gateway, concat($this->tbl_as.payment_gateway, ' - ', $this->tbl_as.payment_method))", "payment_gateway", 0);

        $this->db->select_as("$this->tbl_as.item_total", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("($this->tbl_as.ongkir_total)", "ongkir_total", 0);
        $this->db->select_as("$this->tbl_as.grand_total", "grand_total", 0);
        $this->db->select_as("$this->tbl_as.payment_status", "payment_status", 0);
        $this->db->select_as("($this->tbl_as.pg_fee+$this->tbl_as.pg_fee_vat)", "pg_cost", 0);
        $this->db->select_as("$this->tbl_as.order_status", "order_status", 0);
        $this->db->select_as("'-'", "action_text", 0);
        $this->db->select_as("code_bank", "code_bank", 0);
        $this->db->select_as("payment_card_origin", "payment_card_origin", 0);
        $this->db->from($this->tbl2, $this->tbl2_as);
        $this->db->join_composite($this->tbl, $this->tbl_as, $this->__joinTbl(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.order_status", $this->db->esc("pending"), "AND", "<>", 0, 0);
        $this->db->where_as("$this->tbl7_as.address_status", $this->db->esc("A2"), "AND", "=", 0, 0);
        if (strlen($sdate)==10 && strlen($edate)==10) {
            $this->db->between("DATE($this->tbl_as.cdate)", "DATE('$sdate')", "DATE('$edate')");
        } elseif (strlen($sdate)==10 && strlen($edate)!=10) {
            $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$sdate')", 'AND', '>=');
        } elseif (strlen($sdate)!=10 && strlen($edate)==10) {
            $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$edate')", 'AND', '<=');
        }
        if (strlen($payment_gateway)>0) {
            $this->db->where_as("$this->tbl_as.payment_gateway", $this->db->esc($payment_gateway), "AND", "=", 0, 0);
        }
        if (strlen($payment_status)>0) {
            $this->db->where_as("$this->tbl_as.payment_status", $this->db->esc($payment_status), "AND", "=", 0, 0);
        }
        if (strlen($order_status)>0) {
            $this->db->where_as("$this->tbl_as.order_status", $this->db->esc($order_status), "AND", "=", 0, 0);
        }

        if (strlen($keyword)>0) {
            /*$this->db->where_as($this->__decrypt("$this->tbl4_as.fnama"), addslashes($keyword), "OR", "%like%", 0, 0);
            Edit by Aditya Adi Prabowo 8/9/2020
            Fix Sensitive Filter by Buyer Name, Seller Name, And Order Status*/
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl4_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
            // End Of Edit

            // by Muhammad Sofi - 4 November 2021 10:00
            // remark code
            // $this->db->where_as("COALESCE($this->tbl7_as.alamat,'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as($this->__decrypt("$this->tbl7_as.alamat2"), addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.invoice_code", addslashes($keyword), "OR", "%like%", 0, 0);
        }
        $this->db->group_by("$this->tbl2_as.d_order_id");
        
        switch ($sortCol) {
                case 0:
                        $sortCol = "$this->tbl_as.id";
                        break;
                case 1:
                        $sortCol = "$this->tbl_as.invoice_code";
                        break;
                case 2:
                        $sortCol = "$this->tbl_as.cdate";
                        break;
                case 3:
                        $sortCol = $this->__decrypt("$this->tbl4_as.fnama");
                        break;
                case 4:
                        $sortCol = "COALESCE($this->tbl7_as.alamat,'-')";
                        break;
                case 5:
                        $sortCol = "$this->tbl_as.payment_gateway";
                        break;
                case 6:
                        $sortCol = "$this->tbl_as.item_total";
                        break;
                case 7:
                        $sortCol = "CAST($this->tbl_as.sub_total AS SIGNED)";
                        break;
                case 8:
                        $sortCol = "CAST($this->tbl_as.ongkir_total AS SIGNED)";
                        break;
                case 9:
                        $sortCol = "CAST($this->tbl_as.grand_total AS SIGNED)";
                        break;
                case 10:
                        $sortCol = "$this->tbl_as.payment_status";
                        break;
                case 11:
                        $sortCol = "$this->tbl_as.order_status";
                        break;
                default:
                        $sortCol = "$this->tbl_as.id";
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get('', 0);
    }
    public function countAllBuyer($nation_code, $keyword="", $sdate="", $edate="", $payment_status="", $payment_gateway="", $order_status="")
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(DISTINCT $this->tbl2_as.d_order_id)", "jumlah", 0);
        $this->db->from($this->tbl2, $this->tbl2_as);
        $this->db->join_composite($this->tbl, $this->tbl_as, $this->__joinTbl(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.order_status", $this->db->esc("pending"), "AND", "<>", 0, 0);
        $this->db->where_as("$this->tbl7_as.address_status", $this->db->esc("A2"), "AND", "=", 0, 0);
        if (strlen($sdate)==10 && strlen($edate)==10) {
            $this->db->between("DATE($this->tbl_as.cdate)", "DATE('$sdate')", "DATE('$edate')");
        } elseif (strlen($sdate)==10 && strlen($edate)!=10) {
            $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$sdate')", 'AND', '>=');
        } elseif (strlen($sdate)!=10 && strlen($edate)==10) {
            $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$edate')", 'AND', '<=');
        }
        if (strlen($payment_gateway)>0) {
            $this->db->where_as("$this->tbl_as.payment_gateway", $this->db->esc($payment_gateway), "AND", "=", 0, 0);
        }
        if (strlen($payment_status)>0) {
            $this->db->where_as("$this->tbl_as.payment_status", $this->db->esc($payment_status), "AND", "=", 0, 0);
        }
        if (strlen($order_status)>0) {
            $this->db->where_as("$this->tbl_as.order_status", $this->db->esc($order_status), "AND", "=", 0, 0);
        }

        if (strlen($keyword)>0) {
            $this->db->where_as($this->__decrypt("$this->tbl4_as.fnama"), addslashes($keyword), "OR", "%like%", 1, 0);
            // by Muhammad Sofi - 4 November 2021 10:00
            // remark code
            // $this->db->where_as("COALESCE($this->tbl7_as.alamat,'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as($this->__decrypt("$this->tbl7_as.alamat2"), addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.invoice_code", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }
    public function getById($nation_code, $id)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.id", $id);
        return $this->db->get_first();
    }

    public function update($nation_code, $id, $du=array())
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }

    //method untuk merubah order status yang order_cekstok ke QC atau ke po
    public function setOrderIdsToQcPo($order_ids)
    {
        $sql = 'UPDATE `'.$this->tbl.'` SET `order_status` = "order_pembelian", `cdate_pembelian` = NOW() WHERE `order_status` = "order_cekstok" AND `id` IN('.$order_ids.')';
        $res1 = $this->db->exec($sql);
        $sql = 'UPDATE `'.$this->tbl.'` SET `order_status` = "order_qc", `cdate_qc` = NOW(), qc_by = NULL WHERE `order_status` = "order_cekstok" AND `id` NOT IN('.$order_ids.')';
        $res2 = $this->db->exec($sql);
        if (!empty($res1) && !empty($res2)) {
            return 1;
        } else {
            return 0;
        }
    }
    public function laporanPenjualanSelesaiX($sdate, $edate)
    {
        $sql = "SELECT
    CONCAT(COALESCE($this->tbl3_as.order_status,'utama'),'_',COALESCE($this->tbl3_as.jenis,'barang')) jenis_produk,
    SUM($this->tbl_as.diskon_total) diskon_total,
    SUM($this->tbl_as.ongkir) ongkir_total,
    SUM($this->tbl_as.faktor_kodeunik) kodeunik_total,
    SUM($this->tbl_as.sub_total) subtotal,
    SUM($this->tbl_as.grand_total) total
FROM $this->tbl `$this->tbl_as`
LEFT JOIN $this->tbl2 $this->tbl2_as ON $this->tbl_as.id = $this->tbl2_as.d_order_id
LEFT JOIN $this->tbl3 $this->tbl3_as ON $this->tbl2_as.id = $this->tbl3_as.id
WHERE
    ($this->tbl_as.order_status = 'order_selesai' OR $this->tbl_as.order_status = 'order_kirim') AND
    DATE(COALESCE(cdate_selesai,COALESCE(cdate_kirim,cdate_packing))) BETWEEN
        DATE('$sdate') AND DATE('$edate')
GROUP BY
    CONCAT(COALESCE($this->tbl3_as.order_status,'utama'),'_',COALESCE($this->tbl3_as.jenis,'barang'))
";
        //die($sql);
        return $this->db->query($sql);
    }
    public function laporanPenjualanSelesai($beli_mindate, $beli_maxdate)
    {
        $this->db->flushQuery();

        $this->db->select_as("$this->tbl_as.id", 'id', 0);
        $this->db->select_as("COALESCE($this->tbl3_as.nama,'-')", 'produk', 0);
        $this->db->select_as("COALESCE($this->tbl3_as.jenis,'-')", 'produk_jenis', 0);
        $this->db->select_as("$this->tbl2_as.total_qty", 'qty', 0);
        $this->db->select_as("$this->tbl2_as.harga_jadi", 'harga_jadi', 0);
        $this->db->select_as("$this->tbl2_as.total_qty * $this->tbl2_as.harga_jadi", 'total_harga', 0);
        $this->db->select_as("$this->tbl_as.ongkir", 'ongkir', 0);
        $this->db->select_as("$this->tbl_as.diskon_total", 'diskon_total', 0);
        $this->db->select_as("$this->tbl_as.sub_total", 'sub_total', 0);
        $this->db->select_as("$this->tbl_as.grand_total", 'grand_total', 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join($this->tbl2, $this->tbl2_as, 'd_order_id', $this->tbl_as, 'id', 'left');
        $this->db->join($this->tbl3, $this->tbl3_as, 'id', $this->tbl2_as, 'c_produk_id', 'left');

        $this->db->where_as("$this->tbl_as.order_status", $this->db->esc("order_selesai"), 'OR', 'like', 1, 0);
        $this->db->where_as("$this->tbl_as.order_status", $this->db->esc("order_kirim"), 'AND', 'like', 0, 1);

        $this->db->where_as("DATE($this->tbl_as.cdate_kirim)", "DATE('0000-00-00 00:00:00')", 'AND', '<>', 0, 0);
        if (strlen($beli_mindate)>4 || strlen($beli_maxdate)>4) {
            $this->db->between("DATE( COALESCE($this->tbl_as.`cdate_selesai`, $this->tbl_as.cdate_kirim) )", 'DATE("'.$beli_mindate.'")', 'DATE("'.$beli_maxdate.'")');
        }

        return $this->db->get('', 0);
    }
    public function getLatestStat()
    {
        $this->db->select_as("$this->tbl_as.b_user_id,
SUM($this->tbl_as.grand_total) total_transaksi,
COUNT(*) beli_jml,
$this->tbl_as.grand_total transaksi_terakhir,
COALESCE($this->tbl_as.cdate_selesai,COALESCE($this->tbl_as.cdate_kirim,$this->tbl_as.cdate))", 'beli_terakhir', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join($this->tbl4, $this->tbl4_as, 'id', $this->tbl_as, 'b_user_id', '');

        $this->db->where_as("$this->tbl_as.order_status", $this->db->esc("order_selesai"), 'OR', 'like', 1, 0);
        $this->db->where_as("$this->tbl_as.order_status", $this->db->esc("order_kirim"), 'AND', 'like', 0, 1);

        $this->db->group_by("$this->tbl_as.b_user_id");
        $this->db->order_by("$this->tbl_as.id");
        return $this->db->get();
    }
    public function normalizeOrder()
    {
        $sql ="UPDATE $this->tbl SET order_status = 'order_selesai' WHERE COALESCE(cdate_selesai,'0000-00-00 00:00:00') NOT LIKE '%0000-00-00 00:00:00%'";
        $this->db->exec($sql);
        $sql ="UPDATE $this->tbl SET order_status = 'order_packing' WHERE cdate_kirim LIKE '%0000-00-00 00:00:00%'";
        $this->db->exec($sql);
        $sql ="UPDATE $this->tbl SET order_status = 'order_cekstok' WHERE cdate_proses NOT LIKE '%0000-00-00 00:00:00%'";
        $this->db->exec($sql);
        $sql ="UPDATE $this->tbl SET order_status = 'order_konfirmasi_sudah' WHERE cdate_proses LIKE '%0000-00-00 00:00:00%'";
        $this->db->exec($sql);
        $sql ="UPDATE $this->tbl SET order_status = 'order_konfirmasi' WHERE cdate_konfirmasi LIKE '%0000-00-00 00:00:00%'";
        $this->db->exec($sql);
        $sql ="UPDATE $this->tbl SET order_status = 'cart' WHERE cdate LIKE '%0000-00-00 00:00:00%'";
        $this->db->exec($sql);
    }

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
        $this->db->join($this->tbl2, $this->tbl2_as, 'd_order_id', $this->tbl_as, 'id', 'left');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->where_as("$this->tbl_as.payment_status", $this->db->esc("paid"));
        $this->db->where_as("$this->tbl2_as.seller_status", $this->db->esc("confirmed"));
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
    public function getPerMonth($nation_code, $year=2019, $cache_save=1)
    {
        $this->db->cache_save = $cache_save;
        $this->db->select_as("MONTH(cdate)", "month", 0);
        $this->db->select_as("SUM($this->tbl_as.sub_total)", "earnings", 0);
        $this->db->select_as("SUM($this->tbl_as.grand_total)", "sales", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_in("$this->tbl_as.order_status", array("forward_to_seller","completed"));
        $this->db->where_as("YEAR(cdate)", $this->db->esc($year));
        $this->db->group_by("MONTH(cdate)");
        return $this->db->get();
    }
    public function getEarnings($nation_code, $sdate="", $edate="", $cache_save=1)
    {
        $this->db->cache_save = $cache_save;
        $this->db->select_as("SUM($this->tbl_as.sub_total)", "earnings", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        if (strlen($sdate)!=10) {
            $sdate = date("Y-m-d");
        }
        if (strlen($edate)!=10) {
            $edate = date("Y-m-d");
        }
        if ($sdate == $edate) {
            $this->db->where_as("DATE(cdate)", "DATE('$sdate')");
        } else {
            $this->db->between("DATE(cdate)", "DATE('$sdate')", "DATE('$edate')");
        }
        $this->db->where_in("$this->tbl_as.order_status", array("forward_to_seller","completed"));
        $this->db->group_by("DATE(cdate)");
        $d =  $this->db->get_first('', 0);
        if (isset($d->earnings)) {
            return $d->earnings;
        }
        return 0;
    }
    public function countOrders($nation_code, $sdate="", $edate="", $cache_save=1)
    {
        $this->db->cache_save = $cache_save;
        $this->db->select_as("COALESCE(SUM($this->tbl_as.grand_total),0)", "sales", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        if (empty($sdate)) {
            $sdate = date("Y-m-d");
        }
        if (empty($edate)) {
            $edate = date("Y-m-d");
        }
        if ($sdate == $edate) {
            $this->db->where_as("DATE(cdate)", "DATE('$sdate')");
        } else {
            $this->dv->between("DATE(cdate)", "DATE('$sdate')", "DATE('$edate')");
        }
        $this->db->where_in("$this->tbl_as.order_status", array("order_konfirmasi_sudah","order_packing","order_kirim","order_selesai"));
        $this->db->group_by("DATE(cdate)");
        $d =  $this->db->get_first('', 0);
        if (isset($d->sales)) {
            return $d->sales;
        }
        return 0;
    }
    public function countOrdersPending($nation_code, $sdate="", $edate="", $cache_save=1)
    {
        $this->db->cache_save = $cache_save;
        $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        if (empty($sdate)) {
            $sdate = date("Y-m-d");
        }
        if (empty($edate)) {
            $edate = date("Y-m-d");
        }
        if ($sdate == $edate) {
            $this->db->where_as("DATE(cdate)", "DATE('$sdate')");
        } else {
            $this->dv->between("DATE(cdate)", "DATE('$sdate')", "DATE('$edate')");
        }
        $this->db->where_in("$this->tbl_as.order_status", array("pending"));
        $this->db->group_by("DATE(cdate)");
        $d = $this->db->get_first('', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function updateStatusPayment($orderid, $payment_status)
    {
        $sql = 'UPDATE `'.$this->tbl.'` SET `payment_status` = "'.$payment_status.'", `cdate` = NOW() WHERE `id` IN('.$orderid.')';
        $res = $this->db->exec($sql);
        if (!empty($res)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function updateStatusCancellation($orderid, $payment_status)
    {
        $sql = 'UPDATE `'.$this->tbl.'` SET `payment_status` = "'.$payment_status.'", `cdate` = NOW() WHERE `id` IN('.$orderid.')';
        $res = $this->db->exec($sql);
        if (!empty($res)) {
            return 1;
        } else {
            return 0;
        }
    }

}
