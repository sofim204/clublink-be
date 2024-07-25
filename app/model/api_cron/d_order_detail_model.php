<?php
class D_Order_Detail_Model extends JI_Model
{
    public $tbl = 'd_order_detail';
    public $tbl_as = 'dod';
    public $tbl2 = 'd_order';
    public $tbl2_as = 'dor';
    public $tbl3 = 'b_user';
    public $tbl3_as = 'bu';
    public $tbl4 = "c_produk";
    public $tbl4_as = "cp";
    public $tbl5 = "b_user";
    public $tbl5_as = "bu";
    public $tbl6 = "b_user_alamat";
    public $tbl6_as = "bua";
    public $tbl7 = 'b_user';
    public $tbl7_as = 'b3';
    public $tbl8 = 'd_order_detail_pickup';
    public $tbl8_as = 'dodp';
    

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    //join table d_order_detail with d_order
    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.d_order_id", "=", "$this->tbl2_as.id");
        return $composites;
    }

    //join table d_order with b_user, requires __joinTbl2()
    private function __joinTbl3()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl2_as.b_user_id", "=", "$this->tbl3_as.id");
        return $composites;
    }

    private function __joinTbl3v2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl3_as.id");
        return $composites;
    }

    //join table d_order_detail with c_produk
    private function __joinTbl4()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl4_as.id");
        return $composites;
    }

    //join table b_user with c_produk, requires __joinTbl4()
    private function __joinTbl5()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl5_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl5_as.id");
        return $composites;
    }
    private function __joinTbl6()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl6_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl6_as.b_user_id");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_alamat_id", "=", "$this->tbl6_as.id");
        return $composites;
    }

    private function __joinTbl8()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl8_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.d_order_id", "=", "$this->tbl8_as.d_order_id");
        return $composites;
    }

    public function set($d)
    {
        return $this->db->insert($this->tbl, $d, 0, 0);
    }
    public function setMass($ds)
    {
        return $this->db->insert_multi($this->tbl, $ds, 0);
    }
    public function edit($id, $d)
    {
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $d, 0);
    }
    public function update($nation_code, $d_order_id, $c_produk_id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("id", $c_produk_id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function updateByOrderId($nation_code, $d_order_id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function setExpired()
    {
        $sql = "UPDATE `$this->tbl` SET `seller_status` = 'rejected' WHERE `seller_status` = 'unconfirmed' AND ABS(TIMESTAMPDIFF(HOUR,COALESCE(`forward_to_seller_date`,NOW()),NOW()))>=12";
        return $this->db->exec($sql);
    }
    public function del($id)
    {
        $this->db->where("id", $id);
        return $this->db->delete($this->tbl, 0);
    }
    public function delByOrderId($nation_code, $d_order_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        return $this->db->delete($this->tbl, 0);
    }

    //by Donny Dennison - 22 july 2020 11:44
    // copy from api_mobile/d_order_detail_model for thirty_min api cron
    public function getById($nation_code, $d_order_id, $id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.total_berat", "berat", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "ldate", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.item_total", "item_total", 0);
        $this->db->select_as("$this->tbl2_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl2_as.ongkir_total", "ongkir_total", 0);
        $this->db->select_as("$this->tbl2_as.grand_total", "grand_total", 0);
        $this->db->select_as("$this->tbl2_as.payment_gateway", "payment_gateway", 0);
        $this->db->select_as("$this->tbl2_as.payment_method", "payment_method", 0);
        $this->db->select_as("$this->tbl2_as.payment_status", "payment_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_date", "payment_date", 0);
        $this->db->select_as("$this->tbl2_as.payment_tranid", "payment_tranid", 0);
        $this->db->select_as("$this->tbl2_as.payment_response", "payment_response", 0);
        $this->db->select_as("$this->tbl2_as.payment_confirmed", "payment_confirmed", 0);
        $this->db->select_as("$this->tbl2_as.payment_notif_count", "payment_notif_count", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        return $this->db->get_first();
    }

    public function getByIdFull($nation_code, $d_order_id, $c_produk_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("($this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add)", "ongkir_total", 0);
        $this->db->select_as("$this->tbl_as.grand_total", "grand_total", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.b_user_id,'-')", "b_user_id_buyer", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.invoice_code,'-')", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("COALESCE($this->tbl_as.shipment_vehicle,'-')", "vehicle_types", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.id,'-')", "b_user_id_seller", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.fnama,'-')", "b_user_nama_seller", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.email,'-')", "b_user_email_seller", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.telp,'-')", "b_user_telp_seller", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.judul,'-')", "judul", 0);
        $this->db->select_as($this->__decrypt("$this->tbl6_as.penerima_nama"), "penerima_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl6_as.peneriam_telp"), "penerima_telp", 0);
        // by Muhammad Sofi - 4 November 2021 10:00
        // remark code
        // $this->db->select_as("COALESCE($this->tbl6_as.alamat,'-')", "alamat", 0);
        $this->db->select_as($this->__decrypt("$this->tbl6_as.alamat2"), "alamat2", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.kelurahan,'-')", "kelurahan", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.kecamatan,'-')", "kecamatan", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.kabkota,'-')", "kabkota", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.provinsi,'-')", "provinsi", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.kodepos,'-')", "kodepos", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.latitude,'-')", "latitude", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.longitude,'-')", "longitude", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.catatan,'-')", "catatan", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "inner");
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($c_produk_id));
        $this->db->group_by("$this->tbl_as.id");
        return $this->db->get_first('', 0);
    }

    public function countBuyerPending()
    {
        $this->db->select_as("COUNT(*)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("pending"), "AND", "=");
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getBuyerPending(int $interval=30)
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("pending"), "AND", "=");
        $this->db->where_as("DATE_ADD($this->tbl2_as.cdate, INTERVAL $interval MINUTE)", "NOW()", "AND", "<=");
        $this->db->order_by("$this->tbl2_as.cdate", "asc");
        return $this->db->get('', 0);
    }

    public function getBuyerFreezeOrder($payment_timeout="10")
    {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_notif_count", "payment_notif_count", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");

        //by Donny Dennison - 15 december 2021 14:19
        //bug fix order stuck in waiting for payment if mobile dont call send notif api
        //$this->db->where_as("$this->tbl2_as.payment_notif_count", $this->db->esc(0), "AND", ">");
        
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("pending"), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("waiting_for_payment"), "AND", "=");
        $this->db->where_as("DATE_ADD($this->tbl2_as.cdate, INTERVAL ".$payment_timeout." MINUTE)", "NOW()", "AND", "<=");

        //by Donny Dennison - 3 november 2020 10:37
        //add flag start countdown payment
        $this->db->where_as("$this->tbl2_as.is_countdown", $this->db->esc(0), "AND", "=");

        $this->db->order_by("$this->tbl2_as.cdate", "asc");
        return $this->db->get('', 0);
    }

    public function getBuyerUnPaid($payment_timeout="10")
    {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_notif_count", "payment_notif_count", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");

        //by Donny Dennison - 15 december 2021 14:19
        //bug fix order stuck in waiting for payment if mobile dont call send notif api
        //$this->db->where_as("$this->tbl2_as.payment_notif_count", $this->db->esc(0), "AND", ">");
        
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("pending"), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("waiting_for_payment"), "AND", "=");
        $this->db->where_as("DATE_ADD($this->tbl2_as.cdate, INTERVAL ".$payment_timeout." MINUTE)", "NOW()", "AND", "<=");

        //by Donny Dennison - 3 november 2020 10:37
        //add flag start countdown payment
        $this->db->where_as("$this->tbl2_as.is_countdown", $this->db->esc(1), "AND", "=");

        $this->db->order_by("$this->tbl2_as.cdate", "asc");
        return $this->db->get('', 0);
    }

    public function getBuyerUnPaidLast10($payment_timeout="5")
    {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl_as.total_item", "total_item", 0);
        $this->db->select_as("$this->tbl2_as.payment_notif_count", "payment_notif_count", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.payment_notif_count", $this->db->esc(0), "AND", "=");
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("pending"), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("waiting_for_payment"), "AND", "=");
        $this->db->where_as("DATE_ADD($this->tbl2_as.cdate, INTERVAL ".$payment_timeout." MINUTE)", "NOW()", "AND", "<=");

        //by Donny Dennison - 3 november 2020 10:37
        //add flag start countdown payment
        $this->db->where_as("$this->tbl2_as.is_countdown", $this->db->esc(1), "AND", "=");
        
        $this->db->order_by("$this->tbl2_as.cdate", "asc");
        return $this->db->get('', 0);
    }

    public function getSellerUnconfirmed( $interval=12)
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost", "shipment_cost", 0);

        //by Donny Dennison - 30 July 2020 13:21
        //change auto reject become auto confirm
        $this->db->select_as("$this->tbl_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl_as.shipment_type", "shipment_type", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        
        $this->db->select_as("$this->tbl_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl_as.sub_total+$this->tbl_as.shipment_cost+$this->tbl_as.shipment_cost_add", "grand_total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("unconfirmed"), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=");

        //by Donny Dennison - 30 July 2020 13:21
        //change auto reject become auto confirm
        //START Change by Donny Dennison - 30 july 2020 13:21

        //By Donny Dennison - 26 Juni 2020 21:05
        //Request by Mr Jackie, change seller timeout to 22:30 everyday
        // $this->db->where_as("DATE_ADD($this->tbl_as.forward_to_seller_date, INTERVAL $interval MINUTE)", "NOW()", "AND", "<=");
        // $this->db->where_as("$this->tbl_as.forward_to_seller_date", "'".date('Y-m-d 22:30:00')."'", "AND", "<=");
        $this->db->where_as("$this->tbl_as.forward_to_seller_date", "'".date('Y-m-d 22:53:00')."'", "AND", "<=");

        //END Change by Donny Dennison - 30 july 2020 13:21

        $this->db->order_by("$this->tbl2_as.cdate", "asc");
        return $this->db->get('', 0);
    }
    /**
     * Get order from delivery with status pickup
     * @param  integer $interval interval duration in hour
     * @return array             array of object result
     */
    public function getDelivereds(int $interval=24)
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl_as.shipment_type", "shipment_type", 0);
        $this->db->select_as("$this->tbl_as.shipment_tranid", "shipment_tranid", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=");
        $this->db->where_as("DATE_ADD(COALESCE($this->tbl_as.delivery_date,NOW()), INTERVAL $interval HOUR)", "NOW()", "AND", "<=");
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=");
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "=");
        $this->db->order_by("COALESCE($this->tbl_as.delivery_date,NOW())", "desc");
        return $this->db->get('', 0);
    }

    //by Donny Dennison - 08-07-2020 13:59
    //auto confirm delivery every 30 minutes
    public function getSellerConfirmed( $interval=30)
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl_as.shipment_tranid", "shipment_tranid", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl_as.sub_total+$this->tbl_as.shipment_cost+$this->tbl_as.shipment_cost_add", "grand_total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=");
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("process"), "OR", "=", 1, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("courier fail"), "AND", "=", 0, 1);
        $this->db->order_by("$this->tbl2_as.cdate", "asc");
        return $this->db->get('', 0);
    }
    
    //by Donny Dennison - 08-07-2020 13:59
    //request by Mr Jackie, change shipment status from delivered to succeed after 48 hours for Qxpress
    /**
     * Get order from delivery with status pickup
     * @param  integer $interval interval duration in hour
     * @return array             array of object result
     */
    public function getDeliveredsGogovan(int $interval=24)
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl_as.shipment_type", "shipment_type", 0);
        $this->db->select_as("$this->tbl_as.shipment_tranid", "shipment_tranid", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=");
        $this->db->where_as("DATE_ADD(COALESCE($this->tbl_as.delivery_date,NOW()), INTERVAL $interval HOUR)", "NOW()", "AND", "<=");
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=");
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "=");

        //by Donny Dennison - 08-07-2020 13:59
        //request by Mr Jackie, change shipment status from delivered to succeed after 48 hours for Qxpress
        
        //by Donny Dennison - 15 september 2020 17:45
        //change name, image, etc from gogovan to gogox
        // $this->db->where_as("LOWER($this->tbl_as.shipment_service)", $this->db->esc("gogovan"), "AND", "=");
        $this->db->where_as("LOWER($this->tbl_as.shipment_service)", $this->db->esc("gogox"), "AND", "=");

        $this->db->order_by("COALESCE($this->tbl_as.delivery_date,NOW())", "desc");
        return $this->db->get('', 0);
    }

    //by Donny Dennison - 23 september 2020 15:42
    //add direct delivery feature
    /**
     * Get order from delivery with status pickup
     * @param  integer $interval interval duration in hour
     * @return array             array of object result
     */
    public function getDeliveredsDirectDelivery(int $interval=48)
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl_as.shipment_type", "shipment_type", 0);
        $this->db->select_as("$this->tbl_as.shipment_tranid", "shipment_tranid", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=");
        $this->db->where_as("DATE_ADD(COALESCE($this->tbl_as.delivery_date,NOW()), INTERVAL $interval HOUR)", "NOW()", "AND", "<=");
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=");
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "=");
        $this->db->where_as("LOWER($this->tbl_as.shipment_service)", $this->db->esc("direct delivery"), "AND", "=");
        $this->db->order_by("COALESCE($this->tbl_as.delivery_date,NOW())", "desc");
        return $this->db->get('', 0);
    }

    //by Donny Dennison - 08-07-2020 13:59
    //request by Mr Jackie, change shipment status from delivered to succeed after 48 hours for Qxpress
    public function getDeliveredsQxpress(int $interval=2)
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl_as.shipment_type", "shipment_type", 0);
        $this->db->select_as("$this->tbl_as.shipment_tranid", "shipment_tranid", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=");

        //by Donny Dennison - 09-07-2020 21:04
        //requested by Mr Jackie, from delivery date to pickup date
        // $this->db->where_as("DATE_ADD(DATE(COALESCE($this->tbl_as.delivery_date,NOW())), INTERVAL $interval DAY)", "DATE(NOW())", "AND", "<=");
        $this->db->where_as("DATE_ADD(DATE(COALESCE($this->tbl_as.pickup_date,NOW())), INTERVAL $interval DAY)", "DATE(NOW())", "AND", "<=");

        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=");
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "=");
        $this->db->where_as("LOWER($this->tbl_as.shipment_service)", $this->db->esc("qxpress"), "AND", "=");
        $this->db->order_by("COALESCE($this->tbl_as.delivery_date,NOW())", "desc");
        return $this->db->get('', 0);
    }

    /**
     * Get order from delivery with status pickup
     * @param  integer $interval interval duration in hour
     * @return array             array of object result
     */
    public function getQxpressProsess()
    {   
        $dates="2020-07-10 22:00:00";
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl_as.tracking_number", "tracking_number", 0);
        $this->db->select_as("$this->tbl_as.shipment_type", "shipment_type", 0);
        $this->db->select_as("$this->tbl_as.shipment_tranid", "shipment_tranid", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as($this->__decrypt("$this->tbl8_as.alamat2"), "alamat2", 0);
        $this->db->select_as("$this->tbl8_as.kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl8_as.kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl8_as.kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl8_as.provinsi", "provinsi", 0);
        $this->db->select_as("$this->tbl8_as.negara", "negara", 0);
        $this->db->select_as("$this->tbl8_as.kodepos", "kodepos", 0);
        $this->db->select_as("$this->tbl8_as.latitude", "latitude", 0);
        $this->db->select_as("$this->tbl8_as.longitude", "longitude", 0);
        $this->db->select_as("$this->tbl8_as.catatan", "catatan", 0);
        $this->db->select_as("$this->tbl8_as.catatan", "catatan", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.telp"), "telp", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3v2(), "inner");
        $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "inner");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=");
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=");
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "=");
        $this->db->where_as("$this->tbl_as.shipment_service", $this->db->esc("QXpress"), "AND", "=");
        $this->db->where_as("$this->tbl_as.shipment_type", $this->db->esc("Next Day"), "AND", "=");
        $this->db->where_as("$this->tbl_as.pickup_date", "is null");
        $this->db->where_as("$this->tbl_as.tracking_number", $this->db->esc(""), "AND", "<>");
        //$this->db->where_as("$this->tbl_as.tracking_number", "is not null");
        $this->db->where_as("DATE($this->tbl_as.delivery_date)", "DATE('$dates')", "AND", ">=");
        //$this->db->where_as("$this->tbl_as.delivery_date", "DATE(".date('Y-m-d').")", "AND", "=");
        //$this->db->where_as("$this->tbl_as.shipment_tranid", 4, "AND", "<=");
        $this->db->order_by("COALESCE($this->tbl_as.d_order_id,NOW())", "desc");
        $this->db->group_by("b_user_id_seller");
        return $this->db->get('', 0);
    }

    public function getQxpressProsessNgr($id_seller)
    {   
        $dates="2020-07-10 22:00:00";
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl_as.shipment_type", "shipment_type", 0);
        $this->db->select_as("$this->tbl_as.shipment_tranid", "shipment_tranid", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.tracking_number", "tracking_number", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as($this->__decrypt("$this->tbl8_as.alamat2"), "alamat2", 0);
        $this->db->select_as("$this->tbl8_as.kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl8_as.kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl8_as.kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl8_as.provinsi", "provinsi", 0);
        $this->db->select_as("$this->tbl8_as.negara", "negara", 0);
        $this->db->select_as("$this->tbl8_as.kodepos", "kodepos", 0);
        $this->db->select_as("$this->tbl8_as.latitude", "latitude", 0);
        $this->db->select_as("$this->tbl8_as.longitude", "longitude", 0);
        $this->db->select_as("$this->tbl8_as.catatan", "catatan", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.telp"), "telp", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3v2(), "inner");
        $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "inner");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=");
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=");
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "=");
        $this->db->where_as("$this->tbl_as.shipment_service", $this->db->esc("QXpress"), "AND", "=");
        $this->db->where_as("$this->tbl_as.shipment_type", $this->db->esc("Next Day"), "AND", "=");
        $this->db->where_as("DATE($this->tbl_as.delivery_date)", "DATE('$dates')", "AND", ">=");
        $this->db->where_as("$this->tbl_as.pickup_date", "is null");
        $this->db->where_as("$this->tbl_as.tracking_number", $this->db->esc(""), "AND", "<>");
        //$this->db->where_as("$this->tbl_as.tracking_number", "is not null");
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($id_seller), "AND", "=");
        //$this->db->where_as("$this->tbl_as.shipment_tranid", 4, "AND", "<=");
        $this->db->order_by("COALESCE($this->tbl_as.delivery_date,NOW())", "desc");
        return $this->db->get('', 0);
    }
    
    public function getTerkirims()
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl_as.shipment_type", "shipment_type", 0);
        $this->db->select_as("$this->tbl_as.shipment_tranid", "shipment_tranid", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=");
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=");
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "=");
        $this->db->where_as("$this->tbl_as.buyer_confirmed", $this->db->esc("confirmed"), "AND", "!=");
        $this->db->order_by("COALESCE($this->tbl_as.received_date,NOW())", "asc");
        return $this->db->get('', 0);
    }
    public function getSent()
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl_as.shipment_type", "shipment_type", 0);
        $this->db->select_as("$this->tbl_as.shipment_tranid", "shipment_tranid", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_status", "buyer_status", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("COALESCE($this->tbl_as.delivery_date,'-')", $this->db->esc("-"), "AND", "<>");
        $this->db->where_as("DATE_ADD(COALESCE($this->tbl_as.delivery_date,NOW()), INTERVAL 3 DAY)", "NOW()", "AND", "<=");
        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("wait"), "AND", "=");
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "=");
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=");
        $this->db->order_by("$this->tbl2_as.cdate", "asc");
        return $this->db->get('', 0);
    }
    public function updateGrandTotal()
    {
        $sql = 'UPDATE '.$this->tbl.' SET grand_total = sub_total + shipment_cost + shipment_cost_add WHERE 1';
        return $this->db->exec($sql);
    }
    public function searchInv($inv)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.nation_code", "nation_code", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.invoice_code", $this->db->esc($inv));
        return $this->db->get();
    }
    public function getNonPickup()
    {
        $sql = "SELECT
  bua.*, dod.nation_code,dod.d_order_id,dod.id 'd_order_detail_id',dod.b_user_id,dod.b_user_alamat_id
FROM
    d_order_detail dod
INNER JOIN
    b_user_alamat bua ON
        dod.nation_code = bua.nation_code AND
        dod.b_user_id = bua.b_user_id AND
        dod.b_user_alamat_id = bua.id
WHERE
    CONCAT(dod.nation_code,'-',dod.d_order_id,'-',dod.id) NOT IN(
        SELECT CONCAT(nation_code,'-',d_order_id,'-',d_order_detail_id) FROM d_order_detail_pickup
    )";
        return $this->db->query($sql);
    }

    //by Donny Dennison - 29 april 2021 14:06
    //add-void-and-refund-2c2p-after-reject-by-seller
    public function getSellerRejected()
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.refund_amount", "refund_amount", 0);
        $this->db->select_as("$this->tbl2_as.payment_tranid ", "payment_tranid", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("rejected"), "AND", "=");
        $this->db->where_as("$this->tbl_as.settlement_status", $this->db->esc("processing"), "AND", "=");
        $this->db->where_as("$this->tbl_as.forward_to_seller_date", "'".date('Y-m-d 22:53:00',strtotime('- 1 day'))."'", "AND", "<=");
        $this->db->order_by("$this->tbl2_as.cdate", "asc");
        return $this->db->get('', 0);
    }
}
