<?php
/**
 * Model for d_order_detail table
 *   API_Mobile
 */
class D_Order_Detail_Model extends JI_Model
{
    public $tbl = 'd_order_detail';
    public $tbl_as = 'dod';
    public $tbl2 = 'd_order';
    public $tbl2_as = 'dor';
    public $tbl3 = 'b_user';
    public $tbl3_as = 'bu';
    // public $tbl4 = "c_produk";
    // public $tbl4_as = "cp";
    public $tbl5 = "b_user";
    public $tbl5_as = "b2";
    public $tbl6 = "b_user_alamat";
    public $tbl6_as = "bua";
    // public $tbl7 = 'b_user';
    // public $tbl7_as = 'b3';

    //by Donny Dennison 13 september 2021 - 10:38
    //revamp-profile
    public $tbl8 = 'd_order_detail_item';
    public $tbl8_as = 'dodim';

    public $tbl9 = "d_order_detail_pickup";
    public $tbl9_as = "dodp";

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

    //getting buyer data by joining table b_user with d_order, requires __joinTbl2()
    private function __joinTbl3()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl2_as.b_user_id", "=", "$this->tbl3_as.id");
        return $composites;
    }

    //join table d_order_detail with c_produk
    // private function __joinTbl4()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl_as.c_produk_id", "=", "$this->tbl4_as.id");
    //     return $composites;
    // }

    //For getting Seller data by joining table b_user with c_produk, requires __joinTbl4()
    private function __joinTbl5()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl5_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl5_as.id");
        return $composites;
    }

    //for getting user address
    private function __joinTbl6()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl6_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl6_as.b_user_id");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_alamat_id", "=", "$this->tbl6_as.id");
        return $composites;
    }

    //For getting seller data by joining table b_user with d_order, requires __joinTbl2()
    // private function __joinTbl7()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl4_as.nation_code", "=", "$this->tbl7_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl4_as.b_user_id", "=", "$this->tbl7_as.id");
    //     return $composites;
    // }

    //by Donny Dennison 13 september 2021 - 10:38
    //revamp-profile
    private function __joinTbl8()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl8_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.d_order_id", "=", "$this->tbl8_as.d_order_id");
        $composites[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl8_as.d_order_detail_id");
        return $composites;
    }

    private function __joinTbl9()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl9_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.d_order_id", "=", "$this->tbl9_as.d_order_id");
        $composites[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl9_as.d_order_detail_id");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl9_as.b_user_id");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_alamat_id", "=", "$this->tbl9_as.b_user_alamat_id");
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
    public function edit($nation_code, $d_order_id, $id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function update($nation_code, $d_order_id, $id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function updateByOrderId($nation_code, $d_order_id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function del($nation_code, $d_order_id, $id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("id", $id);
        return $this->db->delete($this->tbl, 0);
    }
    public function delByOrderId($nation_code, $d_order_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        return $this->db->delete($this->tbl, 0);
    }

    public function updateWB($nation_code, $d_order_id, $id)
    {
        return $this->db->exec("UPDATE `$this->tbl` SET `is_wb_download`=`is_wb_download`+1 WHERE `nation_code` = '$nation_code' AND `d_order_id` = '$d_order_id' AND `id` = '$id';");
    }

    public function getLastId($nation_code, $d_order_id)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $id);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }
    public function cartUpdateQty($d_order_id, $pid, $new_qty)
    {
        $du = array();
        $du['qty'] = $new_qty;
        $this->db->where('d_order_id', $d_order_id)->where('c_produk_id', $pid);
        $this->db->update($this->tbl, $du, 0);
        return $this->updateRows($d_order_id);
    }
    public function cartAdd($di)
    {
        return $this->set($di);
    }
    public function check($nation_code, $d_order_id, $b_user_id)
    {
        $this->db->select_as("COUNT(*)", 'total', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("d_order_id", $d_order_id);
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return (int) $d->total;
        }
        return 0;
    }

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
    public function getByIdFull($nation_code, $d_order_id, $id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("($this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add)", "ongkir_total", 0);
        $this->db->select_as("$this->tbl_as.grand_total", "grand_total", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "b_user_nama_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "b_user_fnama_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl5_as.email"), "b_user_email_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl5_as.telp"), "b_user_telp_seller", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.fcm_token,'-')", "b_user_fcm_token_seller", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.device,'-')", "b_user_device_seller", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.b_user_id,'-')", "b_user_id_buyer", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "b_user_nama_buyer", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "b_user_fnama_buyer", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.email"), "b_user_email_buyer", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.fcm_token,'-')", "b_user_fcm_token_buyer", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.device,'-')", "b_user_device_buyer", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.judul,'-')", "judul", 0);
        $this->db->select_as($this->__decrypt("$this->tbl6_as.penerima_nama"), "penerima_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl6_as.penerima_telp"), "penerima_telp", 0);
        // by Muhammad Sofi - 4 November 2021 10:00
        // remark code
        // $this->db->select_as("COALESCE($this->tbl6_as.alamat,'-')", "alamat", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl6_as.alamat2"), "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl6_as.kelurahan, ', ', $this->tbl6_as.kabkota)", "alamat2", 0);
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
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "inner");
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $this->db->group_by("$this->tbl_as.id");
        return $this->db->get_first('', 0);
    }

    // Update by Aditya Adi Prabowo 3.29 21-07-2020
    // Fubtion update to detail_order after reject
    // Sellon Improve and part of MDR Configuration
    public function updateAfterreject($nation_code, $d_order_id, $value_update_detail_order)
    {
        if (!is_array($value_update_detail_order)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        return $this->db->update($this->tbl, $value_update_detail_order, 0);
    }

    public function getByIdForChat($nation_code, $d_order_id, $c_produk_id)
    {
        //by Donny Dennison - 25 january 2021 11:13
        //change chat to open chatting
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);

        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl3_as.id", "b_user_id_buyer", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "b_user_fnama_buyer", 0);
        $this->db->select_as("$this->tbl3_as.image", "b_user_image_buyer", 0);
        $this->db->select_as("$this->tbl5_as.id", "b_user_id_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "b_user_fnama_seller", 0);
        $this->db->select_as("$this->tbl5_as.image", "b_user_image_seller", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_status", "payment_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);
        $this->db->select_as("$this->tbl_as.total_item", "total_item", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($c_produk_id));
        return $this->db->get_first();
    }

    public function getByOrderId($nation_code, $d_order_id)
    {
        $this->db->select_as("$this->tbl_as.*,$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "b_user_nama_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "b_user_fnama_seller", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.image,'default.png')", "b_user_image_seller", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.total_berat", "produk_berat", 0);
        $this->db->select_as("$this->tbl_as.total_panjang", "dimension_long", 0);
        $this->db->select_as("$this->tbl_as.total_lebar", "dimension_width", 0);
        $this->db->select_as("$this->tbl_as.total_tinggi", "dimension_height", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "courier_services", 0);
        $this->db->select_as("$this->tbl_as.shipment_type", "services_duration", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("'99'", "stok", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.fcm_token,'')", "fcm_token", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.device,'android')", "device", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl_as.buyer_confirmed", "buyer_confirmed", 0);

        //by Donny Dennison - 2 august 2020 14:47
        //bug fixing earning total in d_order_detail table
        $this->db->select_as("$this->tbl_as.selling_fee", "selling_fee_seller", 0);

        //by Donny Dennison - 12 october 2020 14:03
        //add seller address at order detail buyer
        $this->db->select_as("IF(".$this->__decrypt("$this->tbl9_as.alamat2")."IS NULL,".$this->__decrypt("$this->tbl6_as.alamat2").",".$this->__decrypt("$this->tbl9_as.alamat2").")", "alamat_pickup", 0);

        $this->db->select_as("$this->tbl5_as.is_active", "b_user_is_active", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'inner');

        //by Donny Dennison - 12 october 2020 14:03
        //add seller address at order detail buyer
        $this->db->join_composite($this->tbl9, $this->tbl9_as, $this->__joinTbl9(), 'left');
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), 'left');


        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        return $this->db->get('', 0);
    }

    public function getByOrderIdDetailId($nation_code, $d_order_id, $d_order_detail_id)
    {
        $this->db->select_as("$this->tbl_as.*,$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "b_user_nama_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "b_user_fnama_seller", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.image,'default.png')", "b_user_image_seller", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.total_berat", "produk_berat", 0);
        $this->db->select_as("$this->tbl_as.total_panjang", "dimension_long", 0);
        $this->db->select_as("$this->tbl_as.total_lebar", "dimension_width", 0);
        $this->db->select_as("$this->tbl_as.total_tinggi", "dimension_height", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "courier_services", 0);
        $this->db->select_as("$this->tbl_as.shipment_type", "services_duration", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("'99'", "stok", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.fcm_token,'')", "fcm_token", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.device,'android')", "device", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl_as.buyer_confirmed", "buyer_confirmed", 0);

        //by Donny Dennison - 12 october 2020 14:03
        //add seller address at order detail buyer
        $this->db->select_as("IF(".$this->__decrypt("$this->tbl9_as.alamat2")."IS NULL,".$this->__decrypt("$this->tbl6_as.alamat2").",".$this->__decrypt("$this->tbl9_as.alamat2").")", "alamat_pickup", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'inner');

        //by Donny Dennison - 12 october 2020 14:03
        //add seller address at order detail buyer
        $this->db->join_composite($this->tbl9, $this->tbl9_as, $this->__joinTbl9(), "left");
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "left");

        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        $this->db->where_as("$this->tbl_as.id", $d_order_detail_id);
        return $this->db->get('', 0);
    }

    public function getByOrderIdSellerId($nation_code, $d_order_id, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.b_user_id, '0')", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.invoice_code, '-')", "invoice_code", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.cdate, NOW())", "d_order_cdate", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.ldate, NOW())", "d_order_ldate", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.payment_gateway, '-')", "payment_gateway", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.payment_status, 'pending')", "payment_status", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.payment_confirmed, '0')", "payment_confirmed", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.order_status, 'pending')", "order_status", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.item_total, '0')", "item_total", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.sub_total, '0')", "sub_total", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.grand_total, '0')", "grand_total", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "b_user_fnama_seller", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        return $this->db->get('', 0);
    }

    // public function getProdukAlamatByOrderId($nation_code, $d_order_id)
    // {
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
    //     $this->db->select_as("COALESCE($this->tbl_as.b_user_id,0)", "b_user_id_seller", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "b_user_nama_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl5_as.image,'default.png')", "b_user_image_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.judul,'-')", "seller_alamat_judul", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl6_as.penerima_nama"), "seller_alamat_nama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl6_as.penerima_telp"), "seller_alamat_telp", 0);
    //     // by Muhammad Sofi - 4 November 2021 10:00
    //     // remark code
    //     // $this->db->select_as("COALESCE($this->tbl6_as.alamat,'-')", "seller_alamat_1", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl6_as.alamat2"), "seller_alamat_2", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.latitude,'-')", "seller_alamat_latitude", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.longitude,'-')", "seller_alamat_longitude", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.catatan,'-')", "seller_alamat_catatan", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.kecamatan,'-')", "seller_alamat_kecamatan", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.kabkota,'-')", "seller_alamat_kabkota", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.provinsi,'-')", "seller_alamat_provinsi", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.kodepos,'-')", "seller_alamat_kodepos", 0);
    //     $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
    //     $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
    //     $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "inner");
    //     $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "inner");
    //     $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
    //     return $this->db->get();
    // }

    //buyer
    public function countBuyerPaymentUnconfirmed($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT $this->tbl2_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("waiting_for_payment"), "AND", "=", 0, 0);
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    /**
     * Get list of unpaid order for buyer
     * @param  [type]  $nation_code [description]
     * @param  [type]  $b_user_id   [description]
     * @param  integer $page        [description]
     * @param  integer $page_size   [description]
     * @return [type]               [description]
     */
    public function getBuyerPaymentUnconfirmed($nation_code, $b_user_id, $page=1, $page_size=10)
    {
        $this->db->select_as("$this->tbl2_as.id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("'0'", "d_order_detail_item_id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("GROUP_CONCAT($this->tbl_as.nama)", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.total_item", "item_total", 0);
        $this->db->select_as("$this->tbl2_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl2_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("SUM($this->tbl_as.sub_total)", "harga_jual", 0);
        $this->db->select_as("SUM($this->tbl_as.sub_total)", "sub_total", 0);
        $this->db->select_as("SUM($this->tbl_as.shipment_cost)", "shipment_cost", 0);
        $this->db->select_as("SUM($this->tbl_as.shipment_cost_add)", "shipment_cost_add", 0);
        $this->db->select_as("SUM($this->tbl_as.shipment_cost_sub)", "shipment_cost_sub", 0);
        $this->db->select_as("(SUM($this->tbl_as.shipment_cost) + SUM($this->tbl_as.shipment_cost_add))", "ongkir_total", 0);
        $this->db->select_as("(SUM($this->tbl_as.sub_total) + SUM($this->tbl_as.shipment_cost) + SUM($this->tbl_as.shipment_cost_add))", "grand_total", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_status", "payment_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_confirmed", "payment_confirmed", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_begin,NOW())", "date_begin", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_expire,NOW())", "date_expire", 0);
        $this->db->select_as("NOW()", "date_current", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "shipment_service", 0);

        //by Donny Dennison - 3 november 2020 10:37
        //add flag start countdown payment
        $this->db->select_as("$this->tbl2_as.is_countdown", "is_countdown", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("waiting_for_payment"), "AND", "=", 0, 0);
        $this->db->group_by("CONCAT($this->tbl2_as.nation_code,'-',$this->tbl_as.d_order_id)");
        $this->db->order_by("$this->tbl_as.id", "ASC");
        $this->db->order_by("$this->tbl_as.b_user_id", "ASC");
        $this->db->order_by("$this->tbl2_as.id", "ASC");
        $this->db->order_by("COALESCE($this->tbl2_as.cdate,NOW())", "ASC");
        $this->db->page($page, $page_size);
        return $this->db->get('');
    }

    //waiting for seller confirmation
    public function countBuyerSellerUnconfirmed($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.d_order_id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("unconfirmed"), "AND", "=", 0, 0);
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    //buyer waiting for seller confirmation
    public function getBuyerSellerUnconfirmed($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl2_as.id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("''", "c_produk_deskripsi", 0);
        $this->db->select_as("$this->tbl_as.total_item", "total_item", 0);
        $this->db->select_as("$this->tbl_as.total_item", "item_total", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_sub", "shipment_cost_sub", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add", "ongkir_total", 0);
        $this->db->select_as("($this->tbl_as.sub_total + $this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add)", "grand_total", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_status", "payment_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_confirmed", "payment_confirmed", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_begin,NOW())", "date_begin", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_expire,NOW())", "date_expire", 0);
        $this->db->select_as("NOW()", "date_current", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("unconfirmed"), "AND", "=", 0, 0);
        $this->db->order_by("COALESCE($this->tbl2_as.cdate,NOW())", "DESC");
        return $this->db->get();
    }

    public function countBuyerPending($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.id))", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("pending"), "AND", "=");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getBuyerPending($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl2_as.id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl2_as.item_total", "item_total", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("''", "c_produk_deskripsi", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_sub", "shipment_cost_sub", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add", "ongkir_total", 0);
        $this->db->select_as("($this->tbl_as.sub_total + $this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add) - $this->tbl_as.shipment_cost_sub", "grand_total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("pending"), "AND", "=");
        $this->db->order_by("COALESCE($this->tbl2_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.id", "asc");
        return $this->db->get('', 0);
    }

    public function countBuyerProcess($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("process"), "AND", "=", 0, 0);
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getBuyerProcess($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl2_as.id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("''", "c_produk_deskripsi", 0);
        $this->db->select_as("$this->tbl_as.total_item", "total_item", 0);
        $this->db->select_as("$this->tbl_as.total_item", "item_total", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_sub", "shipment_cost_sub", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add", "ongkir_total", 0);
        $this->db->select_as("($this->tbl_as.sub_total + $this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add)", "grand_total", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_status", "payment_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_confirmed", "payment_confirmed", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_begin,NOW())", "date_begin", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_expire,NOW())", "date_expire", 0);
        $this->db->select_as("NOW()", "date_current", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("process"), "AND", "=", 0, 0);
        $this->db->order_by("COALESCE($this->tbl2_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.id", "asc");
        return $this->db->get();
    }

    public function countBuyerDelivered($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "=", 0, 0);
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getBuyerDelivered($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl2_as.id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("$this->tbl_as.total_item", "total_item", 0);
        $this->db->select_as("$this->tbl_as.total_item", "item_total", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_sub", "shipment_cost_sub", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add", "ongkir_total", 0);
        $this->db->select_as("($this->tbl_as.sub_total + $this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add)", "grand_total", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_status", "payment_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_confirmed", "payment_confirmed", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_begin,NOW())", "date_begin", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_expire,NOW())", "date_expire", 0);
        $this->db->select_as("NOW()", "date_current", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "=", 0, 0);
        $this->db->order_by("COALESCE($this->tbl2_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.id", "asc");
        return $this->db->get('', 0);
    }

    //delivered by shipper
    public function countBuyerReceived($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.buyer_confirmed", $this->db->esc("confirmed"), "AND", "!=", 0, 0);
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    //delivered by shipper
    public function getBuyerReceived($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl2_as.id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_begin,NOW())", "date_begin", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_expire,NOW())", "date_expire", 0);
        $this->db->select_as("NOW()", "date_current", 0);
        $this->db->select_as("$this->tbl_as.total_item", "item_total", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_sub", "shipment_cost_sub", 0);
        $this->db->select_as("$this->tbl_as.grand_total", "grand_total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.buyer_confirmed", $this->db->esc("confirmed"), "AND", "!=", 0, 0);
        $this->db->order_by("COALESCE($this->tbl2_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.id", "asc");
        return $this->db->get();
    }

    //succeed
    public function countBuyerSucceed($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.buyer_confirmed", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_rejected_all", $this->db->esc("0"), "AND", "=", 0, 0);
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function getBuyerSucceed($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl2_as.id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);
        $this->db->select_as("$this->tbl_as.total_item", "item_total", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_sub", "shipment_cost_sub", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl_as.grand_total", "grand_total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.buyer_confirmed", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_rejected_all", $this->db->esc("0"), "AND", "=", 0, 0);
        $this->db->order_by("COALESCE($this->tbl2_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.id", "asc");
        return $this->db->get('', 0);
    }

    //rejected
    public function countBuyerRejected($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("rejected"), "OR", "=", 1, 0);
        $this->db->where_as("$this->tbl_as.is_rejected_all", $this->db->esc("1"), "AND", "=", 0, 1);
        $d = $this->db->get_first('', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function getBuyerRejected($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl2_as.id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("''", "c_produk_deskripsi", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.total_item", "item_total", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_sub", "shipment_cost_sub", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl_as.grand_total", "grand_total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("rejected"), "OR", "=", 1, 0);
        $this->db->where_as("$this->tbl_as.is_rejected_all", $this->db->esc("1"), "AND", "=", 0, 1);
        $this->db->order_by("COALESCE($this->tbl2_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.id", "asc");
        return $this->db->get('', 0);
    }

    //expired
    public function countBuyerExpired($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("cancelled"), "AND", "=");
        $d = $this->db->get_first('', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function getBuyerExpired($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl2_as.id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("$this->tbl_as.grand_total", "grand_total", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl_as.total_item", "item_total", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_sub", "shipment_cost_sub", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl_as.grand_total", "grand_total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("cancelled"), "AND", "=");
        $this->db->order_by("COALESCE($this->tbl2_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.id", "asc");
        return $this->db->get('', 0);
    }

    public function getOrderByBuyer($nation_code, $d_order_id, $c_produk_id, $b_user_id_buyer)
    {
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl2_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl2_as.grand_total", "grand_total", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_status", "payment_status", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "ldate", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("'-'", "c_produk_brand", 0);
        $this->db->select_as("''", "c_produk_deskripsi", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        $this->db->where_as("$this->tbl_as.id", $c_produk_id);
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id_buyer));
        return $this->db->get_first();
    }
    // public function getByIdForBuyer($nation_code, $d_order_id, $c_produk_id)
    // {
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "c_produk_id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
    //     $this->db->select_as("COALESCE($this->tbl_as.berat,'1')", "c_produk_berat", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.b_user_alamat_id,'0')", "b_user_alamat_id", 0);
    //     $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
    //     $this->db->select_as("''", "c_produk_brand", 0);
    //     $this->db->select_as("''", "c_produk_deskripsi", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.judul,'-')", "seller_alamat_judul", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl6_as.penerima_nama"), "seller_alamat_nama", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl6_as.penerima_telp"), "seller_alamat_telp", 0);
    //     // by Muhammad Sofi - 4 November 2021 10:00
    //     // remark code
    //     // $this->db->select_as("COALESCE($this->tbl6_as.alamat,'-')", "seller_alamat_1", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl6_as.alamat2"), "seller_alamat_2", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.latitude,'-')", "seller_alamat_latitude", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.longitude,'-')", "seller_alamat_longitude", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.catatan,'-')", "seller_alamat_catatan", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.kecamatan,'-')", "seller_alamat_kecamatan", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.kabkota,'-')", "seller_alamat_kabkota", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.provinsi,'-')", "seller_alamat_provinsi", 0);
    //     $this->db->select_as("COALESCE($this->tbl6_as.kodepos,'-')", "seller_alamat_kodepos", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
    //     $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "inner");
    //     $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "inner");
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($c_produk_id));
    //     return $this->db->get_first('', 0);
    // }
    //end buyer

    //start seller
    public function getOrderBySeller($nation_code, $d_order_id, $c_produk_id, $b_user_id_seller)
    {
        $this->db->select_as("('wait')", "buyer_status", 0);
        $this->db->select_as("$this->tbl_as.*, $this->tbl2_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_id", "b_user_alamat_id_seller", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.b_user_id,0)", "b_user_id_buyer", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.order_status,0)", "order_status", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.payment_status,0)", "payment_status", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.cdate,0)", "cdate", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.invoice_code,0)", "invoice_code", 0);
        $this->db->select_as("(90.0)", "settlement_percentage", 0);
        
        //by Donny Dennison - 16 February 2020 15:50
        //fix reject by seller didnt deduct the total
        //START by Donny Dennison - 16 February 2020 15:50
        $this->db->select_as("$this->tbl2_as.pg_fee_vat", "pg_fee_vat", 0);
        $this->db->select_as("$this->tbl2_as.sub_total", "sub_total_order", 0);
        $this->db->select_as("$this->tbl2_as.selling_fee_percent", "selling_fee_percent_order", 0);
        $this->db->select_as("$this->tbl2_as.ongkir_total", "ongkir_total_order", 0);
        $this->db->select_as("$this->tbl2_as.grand_total", "grand_total_order", 0);
        $this->db->select_as("$this->tbl2_as.refund_amount", "refund_amount_order", 0);
        //END by Donny Dennison - 16 February 2020 15:50

        //by Donny Dennison - 29 april 2021 14:06
        //add-void-and-refund-2c2p-after-reject-by-seller
        $this->db->select_as("$this->tbl2_as.payment_tranid", "payment_tranid", 0);
        $this->db->select_as("$this->tbl2_as.payment_method", "payment_method", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($c_produk_id));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id_seller));
        return $this->db->get_first('', 0);
    }
    public function getSellerNew($nation_code, $b_user_id, $page=1, $page_size=10)
    {
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "item_total", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("($this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add + $this->tbl_as.shipment_cost_sub)", "ongkir_total", 0);
        $this->db->select_as("($this->tbl_as.sub_total + $this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add + $this->tbl_as.shipment_cost_sub)", "grand_total", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_status", "payment_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_begin,NOW())", "date_begin", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_expire,NOW())", "date_expire", 0);
        $this->db->select_as("NOW()", "date_current", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_confirmed", $this->db->esc("1"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("unconfirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_expired", $this->db->esc("0"), "AND", "=", 0, 0);
        $this->db->order_by("COALESCE($this->tbl2_as.cdate,NOW())", "ASC");
        $this->db->order_by("$this->tbl_as.d_order_id", "ASC");
        $this->db->order_by("$this->tbl_as.id", "asc");
        $this->db->page($page, $page_size);
        return $this->db->get('', 0);
    }
    public function countSellerNew($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.id))", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_confirmed", $this->db->esc("1"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("unconfirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_expired", $this->db->esc("0"), "AND", "=", 0, 0);
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function countSellerProcess($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.id))", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 1, 0);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=", 0, 1);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("process"), "AND", "=", 0, 0);
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function getSellerProcess($nation_code, $b_user_id, $page=1, $page_size=20)
    {
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("'-'", "c_produk_brand", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "item_total", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("($this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add + $this->tbl_as.shipment_cost_sub)", "ongkir_total", 0);
        $this->db->select_as("($this->tbl_as.sub_total + $this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add + $this->tbl_as.shipment_cost_sub)", "grand_total", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_status", "payment_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        //$this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("process"), "AND", "=", 1, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("process"), "OR", "=", 1, 0);//By Donny Dennison - 08-07-2020 16:16 Request by Mr Jackie, add new shipment status "courier fail"
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("courier fail"), "AND", "=", 0, 1);//By Donny Dennison - 08-07-2020 16:16 Request by Mr Jackie, add new shipment status "courier fail"
        $this->db->order_by("COALESCE($this->tbl2_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.id", "asc");
        $this->db->page($page, $page_size);
        return $this->db->get();
    }

    public function countSellerDelivered($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.id))", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "=", 0, 0);
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function getSellerDelivered($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "item_total", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("($this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add + $this->tbl_as.shipment_cost_sub)", "ongkir_total", 0);
        $this->db->select_as("($this->tbl_as.sub_total + $this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add + $this->tbl_as.shipment_cost_sub)", "grand_total", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_status", "payment_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_begin,NOW())", "date_begin", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_expire,NOW())", "date_expire", 0);
        $this->db->select_as("NOW()", "date_current", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "=", 0, 0);
        $this->db->order_by("COALESCE($this->tbl2_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.id", "asc");
        return $this->db->get();
    }

    //received by seller according to Shipping Service
    public function countSellerReceived($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.id))", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.buyer_confirmed", $this->db->esc("confirmed"), "AND", "!=", 0, 0);
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function getSellerReceived($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("'-'", "c_produk_brand", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "item_total", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("($this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add + $this->tbl_as.shipment_cost_sub)", "ongkir_total", 0);
        $this->db->select_as("$this->tbl_as.earning_total", "grand_total", 0);
        $this->db->select_as("$this->tbl_as.grand_total", "grand_total2", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_status", "payment_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_begin,NOW())", "date_begin", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_expire,NOW())", "date_expire", 0);
        $this->db->select_as("NOW()", "date_current", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.buyer_confirmed", $this->db->esc("confirmed"), "AND", "!=", 0, 0);
        $this->db->order_by("COALESCE($this->tbl2_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.id", "asc");
        return $this->db->get();
    }

    //received by seller according to Shipping Service
    public function countSellerSucceed($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.id))", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.buyer_confirmed", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_rejected_all", $this->db->esc("0"), "AND", "=", 0, 0);
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function getSellerSucceed($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.item_total", "item_total", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_sub", "shipment_cost_sub", 0);
        $this->db->select_as("($this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add + $this->tbl_as.shipment_cost_sub)", "ongkir_total", 0);
        $this->db->select_as("$this->tbl_as.earning_total", "grand_total", 0);
        $this->db->select_as("$this->tbl_as.grand_total", "grand_total2", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_status", "payment_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_confirmed", "buyer_confirmed", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.buyer_confirmed", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_rejected_all", $this->db->esc("0"), "AND", "=", 0, 0);
        $this->db->order_by("COALESCE($this->tbl2_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.id", "asc");
        return $this->db->get();
    }

    //rejected
    public function countSellerRejected($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.id))", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("rejected"), "OR", "=", 1, 0);
        $this->db->where_as("$this->tbl_as.is_rejected_all", $this->db->esc("1"), "AND", "=", 0, 1);
        $d = $this->db->get_first('', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function getSellerRejected($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl2_as.id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("$this->tbl2_as.item_total", "item_total", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("($this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add + $this->tbl_as.shipment_cost_sub)", "ongkir_total", 0);
        $this->db->select_as("($this->tbl_as.sub_total + $this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add + $this->tbl_as.shipment_cost_sub)", "grand_total", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("rejected"), "OR", "=", 1, 0);
        $this->db->where_as("$this->tbl_as.is_rejected_all", $this->db->esc("1"), "AND", "=", 0, 1);
        $this->db->order_by("COALESCE($this->tbl2_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.id", "asc");
        return $this->db->get('', 0);
    }

    //expired
    public function countSellerExpired($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.id))", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("cancelled"), "AND", "=");
        $d = $this->db->get_first('', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function getSellerExpired($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("$this->tbl2_as.item_total", "item_total", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("($this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add + $this->tbl_as.shipment_cost_sub)", "ongkir_total", 0);
        $this->db->select_as("($this->tbl_as.sub_total + $this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add + $this->tbl_as.shipment_cost_sub)", "grand_total", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("cancelled"), "AND", "=");
        $this->db->order_by("COALESCE($this->tbl2_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.id", "asc");
        return $this->db->get('', 0);
    }
    public function getSellerStats($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.id))", "sales_count", 0);
        $this->db->select_as("SUM($this->tbl_as.total_qty)", "sales_qty", 0);
        $this->db->select_as("SUM($this->tbl_as.sub_total)", "sales_sum", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        return $this->db->get_first();
    }
    //end seller

    //checkout
    public function getSellerByOrderId($nation_code, $d_order_id)
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "b_user_fnama_seller", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.image,'default.png')", "b_user_image_seller", 0);
        $this->db->select_as("$this->tbl_as.date_begin", "date_begin", 0);
        $this->db->select_as("$this->tbl_as.date_expire", "date_expire", 0);
        $this->db->select_as("$this->tbl_as.date_duration", "date_duration", 0);
        $this->db->select_as("$this->tbl_as.forward_to_seller_date", "forward_to_seller_date", 0);
        $this->db->select_as("$this->tbl_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl_as.shipment_type", "shipment_type", 0);
        $this->db->select_as("$this->tbl_as.shipment_vehicle", "shipment_vehicle", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_sub", "shipment_cost_sub", 0);
        $this->db->select_as("$this->tbl_as.pickup_date", "pickup_date", 0);
        $this->db->select_as("$this->tbl_as.delivery_date", "delivery_date", 0);
        $this->db->select_as("$this->tbl_as.received_date", "received_date", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "total_qty", 0);
        $this->db->select_as("$this->tbl_as.total_berat", "total_berat", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl_as.grand_total", "grand_total", 0);
        $this->db->select_as("$this->tbl_as.is_wb_download", "is_wb_download", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        return $this->db->get('', 0);
    }
    public function getForCheckout($nation_code, $d_order_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "b_user_fnama_seller", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.image,'default.png')", "b_user_image_seller", 0);
        $this->db->select_as("$this->tbl5_as.is_active", "b_user_is_active", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        return $this->db->get('', 0);
    }
    //end checkout

    //shipment
    public function getForShipment($nation_code, $d_order_id, $d_order_detail_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_id", "b_user_alamat_id", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.total_berat", "berat", 0);
        $this->db->select_as("$this->tbl_as.total_panjang", "p", 0);
        $this->db->select_as("$this->tbl_as.total_lebar", "l", 0);
        $this->db->select_as("$this->tbl_as.total_tinggi", "t", 0);
        $this->db->select_as("$this->tbl_as.total_panjang", "dimension_long", 0);
        $this->db->select_as("$this->tbl_as.total_lebar", "dimension_width", 0);
        $this->db->select_as("$this->tbl_as.total_tinggi", "dimension_height", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "courier_services", 0);
        $this->db->select_as("$this->tbl_as.shipment_vehicle", "vehicle_types", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        $this->db->where_as("$this->tbl_as.id", $d_order_detail_id);
        return $this->db->get_first('', 0);
    }
    //end shipment
    
    // //by Donny Dennison - 28 august 2020 15:14
    // //add new api for best shop in homepage
    // public function getBestShopTopSold($nation_code, $page=1, $pagesize)
    // {
    //     // $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
    //     // $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "fnama_seller", 0);
    //     // $this->db->select_as("SUM($this->tbl_as.total_item)", "total_sold", 0);
    //     // $this->db->from($this->tbl, $this->tbl_as);
    //     // $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
    //     // $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "inner");
    //     // $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
    //     // $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
    //     // $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
    //     // $this->db->where_as("$this->tbl5_as.is_active", $this->db->esc("1"), "AND", "=", 0, 0);
    //     // $this->db->group_by("$this->tbl_as.b_user_id");
    //     // $this->db->order_by("SUM($this->tbl_as.total_item)", 'desc')->limit($page, $pagesize);
    //     // return $this->db->get('', 1);

    //     //pagination logic
    //     $page = ($page * $pagesize) - $pagesize;
        
    //     $sql = "SELECT dod.b_user_id AS 'b_user_id_seller', ".$this->__decrypt('b2.fnama')." AS 'fnama_seller', SUM(dod.total_item) AS
    //     'total_sold', total_product FROM `d_order_detail` dod INNER JOIN `d_order` dor ON dod.nation_code = dor.nation_code AND dod.d_order_id
    //     = dor.id INNER JOIN `b_user` b2 ON dod.nation_code = b2.nation_code AND dod.b_user_id = b2.id INNER JOIN (SELECT COUNT(*) AS 'total_product', cp.b_user_id FROM `c_produk` cp WHERE cp.nation_code = ".$nation_code." AND cp.is_published = 1 AND cp.is_visible = 1 AND cp.is_active = 1 AND COALESCE(cp.stok,0) > '0' GROUP BY
    //     cp.b_user_id) cp ON dod.b_user_id = cp.b_user_id WHERE dor.nation_code =
    //     ".$nation_code." AND dor.order_status = 'forward_to_seller' AND dor.payment_status = 'paid' AND b2.is_active = '1' AND total_product > '4' GROUP BY
    //     dod.b_user_id ORDER BY SUM(dod.total_item) DESC LIMIT ".$page.", ".$pagesize;

    //     return $this->db->query($sql);
    // }

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

    //by Donny Dennison 13 september 2021 - 10:38
    //revamp-profile
    //START by Donny Dennison 13 september 2021 - 10:38
    public function countBuyingHistoryOnGoing($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.nation_code,'-',$this->tbl2_as.id,'-',$this->tbl_as.id))", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("unconfirmed"), "OR", "=", 1, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 1);
        // $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "!=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "!=", 0, 0);
        $this->db->where_as("$this->tbl8_as.buyer_status", $this->db->esc("accepted"), "AND", "!=", 0, 0);
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getBuyingHistoryOnGoing($nation_code, $b_user_id, $page=1, $page_size=10)
    {
        $this->db->select_as("$this->tbl2_as.id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("$this->tbl_as.total_item", "total_item", 0);
        $this->db->select_as("$this->tbl_as.total_item", "item_total", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_sub", "shipment_cost_sub", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add", "ongkir_total", 0);
        $this->db->select_as("($this->tbl_as.sub_total + $this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add)", "grand_total", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_status", "payment_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_confirmed", "payment_confirmed", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_begin,NOW())", "date_begin", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_expire,NOW())", "date_expire", 0);
        $this->db->select_as("NOW()", "date_current", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("unconfirmed"), "OR", "=", 1, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 1);
        // $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "!=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "!=", 0, 0);
        $this->db->where_as("$this->tbl8_as.buyer_status", $this->db->esc("accepted"), "AND", "!=", 0, 0);
        $this->db->group_by("CONCAT($this->tbl_as.nation_code,'-',$this->tbl2_as.id,'-',$this->tbl_as.id)");
        $this->db->order_by("COALESCE($this->tbl2_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.id", "asc");
        $this->db->page($page, $page_size);
        return $this->db->get('', 0);
    }

    public function countSellingHistoryOnGoing($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.nation_code,'-',$this->tbl2_as.id,'-',$this->tbl_as.id))", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("rejected"), "AND", "!=", 0, 0);
        // $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "!=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "!=", 0, 0);
        $this->db->where_as("$this->tbl8_as.buyer_status", $this->db->esc("accepted"), "AND", "!=", 0, 0);
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getSellingHistoryOnGoing($nation_code, $b_user_id, $page=1, $page_size=10)
    {
        $this->db->select_as("$this->tbl2_as.id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl2_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("$this->tbl_as.total_item", "total_item", 0);
        $this->db->select_as("$this->tbl_as.total_item", "item_total", 0);
        $this->db->select_as("$this->tbl_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost_sub", "shipment_cost_sub", 0);
        $this->db->select_as("$this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add", "ongkir_total", 0);
        $this->db->select_as("($this->tbl_as.sub_total + $this->tbl_as.shipment_cost + $this->tbl_as.shipment_cost_add)", "grand_total", 0);
        $this->db->select_as("$this->tbl2_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_status", "payment_status", 0);
        $this->db->select_as("$this->tbl2_as.payment_confirmed", "payment_confirmed", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_begin,NOW())", "date_begin", 0);
        $this->db->select_as("COALESCE($this->tbl_as.date_expire,NOW())", "date_expire", 0);
        $this->db->select_as("NOW()", "date_current", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("rejected"), "AND", "!=", 0, 0);
        // $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("delivered"), "AND", "!=", 0, 0);
        $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "!=", 0, 0);
        $this->db->where_as("$this->tbl8_as.buyer_status", $this->db->esc("accepted"), "AND", "!=", 0, 0);
        $this->db->group_by("CONCAT($this->tbl_as.nation_code,'-',$this->tbl2_as.id,'-',$this->tbl_as.id)");
        $this->db->order_by("COALESCE($this->tbl2_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.id", "asc");
        $this->db->page($page, $page_size);
        return $this->db->get('', 0);
    }
    //END by Donny Dennison 13 september 2021 - 10:38

    //START by Donny Dennison - 19 july 2022 15:42
    //delete temporary or permanent user feature
    public function countUnfinishedOrderSeller($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.nation_code,'-',$this->tbl2_as.id,'-',$this->tbl_as.id))", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("void"), "AND", "!=");
        $this->db->where_as("$this->tbl2_as.payment_status", $this->db->esc("failed"), "AND", "!=");
        $this->db->where_as("$this->tbl2_as.order_status", $this->db->esc("cancelled"), "AND", "!=");
        // $this->db->where_as("$this->tbl_as.seller_status", $this->db->esc("rejected"), "AND", "!=");
        // $this->db->where_as("$this->tbl_as.shipment_status", $this->db->esc("succeed"), "AND", "!=", 0, 0);
        $this->db->where_as("$this->tbl_as.settlement_status", $this->db->esc("completed"), "AND", "!=", 0, 0);
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    //END by Donny Dennison - 19 july 2022 15:42
    //delete temporary or permanent user feature

}
