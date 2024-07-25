<?php
class D_Order_Detail_Item_Model extends JI_Model
{
    public $tbl = 'd_order_detail_item';
    public $tbl_as = 'dodi';
    public $tbl2 = 'd_order_detail';
    public $tbl2_as = 'dod';
    public $tbl3 = 'd_order';
    public $tbl3_as = 'dor';
    public $tbl4 = 'b_user';
    public $tbl4_as = 'bu';
    // public $tbl5 = "c_produk";
    // public $tbl5_as = "cp";
    public $tbl6 = "b_user";
    public $tbl6_as = "b2";
    // public $tbl7 = "b_user_alamat";
    // public $tbl7_as = "bua";
    public $tbl8 = 'b_user';
    public $tbl8_as = 'b3';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    //join table d_order_detail_item with d_order_detail
    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.d_order_id", "=", "$this->tbl2_as.d_order_id");
        $composites[] = $this->db->composite_create("$this->tbl_as.d_order_detail_id", "=", "$this->tbl2_as.id");
        return $composites;
    }

    //join table d_order_detail with d_order, requires __joinTbl2
    private function __joinTbl3()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl2_as.d_order_id", "=", "$this->tbl3_as.id");
        return $composites;
    }

    //for buyer user data
    private function __joinTbl4()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl3_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl3_as.b_user_id", "=", "$this->tbl4_as.id");
        return $composites;
    }

    //join table d_order_detail item with produk
    // private function __joinTbl5()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl5_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl_as.c_produk_id", "=", "$this->tbl5_as.id");
    //     return $composites;
    // }

    //join table b_user (2) with produk (seller)
    private function __joinTbl6()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl6_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl2_as.b_user_id", "=", "$this->tbl6_as.id");
        return $composites;
    }

    //join table b_user_alamat with b_user (2 (pickup address)
    // private function __joinTbl7()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl7_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl2_as.b_user_id", "=", "$this->tbl7_as.b_user_id");
    //     $composites[] = $this->db->composite_create("$this->tbl2_as.b_user_alamat_id", "=", "$this->tbl7_as.id");
    //     return $composites;
    // }

    public function set($d)
    {
        return $this->db->insert($this->tbl, $d, 0, 0);
    }
    public function setMass($ds)
    {
        return $this->db->insert_multi($this->tbl, $ds, 0);
    }
    public function edit($nation_code, $d_order_id, $d_order_detail_id, $c_produk_id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("d_order_detail_id", $d_order_detail_id);
        $this->db->where("c_produk_id", $c_produk_id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function update($nation_code, $d_order_id, $d_order_detail_id, $c_produk_id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("d_order_detail_id", $d_order_detail_id);
        $this->db->where("c_produk_id", $c_produk_id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function updateByOrderDetailId($nation_code, $d_order_id, $d_order_detail_id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("d_order_detail_id", $d_order_detail_id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function del($id)
    {
        $this->db->where("id", $id);
        return $this->db->delete($this->tbl, 0);
    }

    public function getByOrderId($nation_code, $d_order_id)
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.d_order_detail_id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "d_order_detail_item_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.brand", "brand", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.berat", "berat", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
        $this->db->select_as($this->__decrypt("$this->tbl6_as.fnama"), "b_user_fnama_seller", 0);
        $this->db->select_as("$this->tbl6_as.image", "b_user_image_seller", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost_sub", "shipment_cost_sub", 0);
        $this->db->select_as("$this->tbl3_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl2_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl2_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.buyer_status", "buyer_status", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);
        $this->db->select_as("$this->tbl2_as.is_rejected_all", "is_rejected_all", 0);
        $this->db->select_as("$this->tbl6_as.is_active", "b_user_is_active", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        $this->db->order_by("$this->tbl_as.c_produk_id", "asc");
        return $this->db->get('', 0);
    }

    public function getByOrderIdDetailId($nation_code, $d_order_id, $d_order_detail_id)
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.d_order_detail_id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "d_order_detail_item_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.brand", "brand", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.berat", "berat", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
        $this->db->select_as($this->__decrypt("$this->tbl6_as.fnama"), "b_user_fnama_seller", 0);
        $this->db->select_as("$this->tbl6_as.image", "b_user_image_seller", 0);
        $this->db->select_as("$this->tbl3_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl2_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl2_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.buyer_status", "buyer_status", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);
        $this->db->select_as("$this->tbl2_as.is_rejected_all", "is_rejected_all", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $d_order_detail_id);
        return $this->db->get('', 0);
    }

    public function getByOrderDetailId($nation_code, $d_order_id, $d_order_detail_id)
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.d_order_detail_id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.d_order_detail_id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "d_order_detail_item_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.brand", "brand", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.berat", "berat", 0);
        $this->db->select_as("$this->tbl_as.panjang", "panjang", 0);
        $this->db->select_as("$this->tbl_as.lebar", "lebar", 0);
        $this->db->select_as("$this->tbl_as.tinggi", "tinggi", 0);
        $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl2_as.shipment_vehicle", "shipment_vehicle", 0);
        $this->db->select_as("$this->tbl2_as.shipment_type", "shipment_type", 0);
        $this->db->select_as("$this->tbl2_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_status", "buyer_status", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);
        $this->db->select_as("$this->tbl_as.is_fashion", "is_fashion", 0);
        $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $d_order_detail_id);
        $this->db->order_by("$this->tbl_as.c_produk_id", "asc");
        return $this->db->get('', 0);
    }
    public function getById($nation_code, $d_order_id, $d_order_detail_id, $c_produk_id)
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.d_order_detail_id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "d_order_detail_item_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "id", 0);
        $this->db->select_as("$this->tbl3_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.brand", "brand", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.berat", "berat", 0);
        $this->db->select_as("$this->tbl_as.panjang", "panjang", 0);
        $this->db->select_as("$this->tbl_as.lebar", "lebar", 0);
        $this->db->select_as("$this->tbl_as.tinggi", "tinggi", 0);
        $this->db->select_as("$this->tbl_as.satuan", "satuan", 0);
        $this->db->select_as("$this->tbl2_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl2_as.shipment_vehicle", "shipment_vehicle", 0);
        $this->db->select_as("$this->tbl2_as.shipment_type", "shipment_type", 0);
        $this->db->select_as("$this->tbl2_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.is_fashion", "is_fashion", 0);
        $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
        $this->db->select_as("$this->tbl2_as.pg_fee", "pg_fee", 0);
        $this->db->select_as("$this->tbl2_as.cancel_fee", "cancel_fee", 0);
        $this->db->select_as("$this->tbl2_as.selling_fee", "selling_fee", 0);
        $this->db->select_as("$this->tbl2_as.earning_total", "earning_total", 0);
        $this->db->select_as("$this->tbl2_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.buyer_status", "buyer_status", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);

        //by Donny Dennison - 2 august 2020 14:47
        //bug fixing earning total in d_order_detail table
        //START by Donny Dennison - 2 august 2020 14:47

        $this->db->select_as("$this->tbl3_as.pg_fee", "pg_fee_order", 0);
        $this->db->select_as("$this->tbl3_as.pg_fee_vat", "pg_fee_vat", 0);
        $this->db->select_as("$this->tbl3_as.profit_amount", "profit_amount", 0);
        $this->db->select_as("$this->tbl3_as.selling_fee", "selling_fee_order", 0);
        $this->db->select_as("$this->tbl3_as.selling_fee_percent", "selling_fee_percent", 0);
        $this->db->select_as("$this->tbl3_as.refund_amount", "refund_amount", 0);
        $this->db->select_as("$this->tbl3_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl2_as.sub_total", "sub_total_d_order_detail", 0);
        $this->db->select_as("$this->tbl2_as.refund_amount", "refund_amount_d_order_detail", 0);
        $this->db->select_as("$this->tbl2_as.selling_fee_percent", "selling_fee_percent_d_order_detail", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost_sub", "shipment_cost_sub", 0);

        //END by Donny Dennison - 2 august 2020 14:47

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $d_order_detail_id);
        $this->db->where_as("$this->tbl_as.c_produk_id", $c_produk_id);
        return $this->db->get_first('', 0);
    }
    public function count($nation_code, $d_order_id, $d_order_detail_id)
    {
        $this->db->select_as("COUNT(*)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $d_order_detail_id);
        $this->db->group_by("CONCAT(nation_code,'-',d_order_id,'-',d_order_detail_id)");
        $d = $this->db->get_first('', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function countBuyerStatus($nation_code, $d_order_id, $d_order_detail_id)
    {
        $this->db->select_as("buyer_status", "buyer_status", 0);
        $this->db->select_as("COUNT(*)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $d_order_detail_id);
        $this->db->group_by("CONCAT(nation_code,'-',d_order_id,'-',d_order_detail_id,'-',buyer_status)");
        return $this->db->get('', 0);
    }
    public function countBuyerStatusWait($nation_code, $d_order_id, $d_order_detail_id)
    {
        $this->db->select_as("COUNT(*)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $d_order_detail_id);
        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("wait"));
        $d = $this->db->get_first('', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function countBuyerStatusAccepted($nation_code, $d_order_id, $d_order_detail_id)
    {
        $this->db->select_as("COUNT(*)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $d_order_detail_id);
        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("accepted"));
        $d = $this->db->get_first('', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    //succeed
    public function countBuyerSucceed($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.d_order_detail_id))", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl3_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl3_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.shipment_status", $this->db->esc("succeed"), "AND", "=", 0, 0);
        
        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->where_as("$this->tbl2_as.buyer_confirmed", $this->db->esc("unconfirmed"), "AND", "!=", 0, 0);
        $this->db->where_as("$this->tbl2_as.buyer_confirmed", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.is_rejected_all", $this->db->esc("0"), "AND", "=", 0, 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("accepted"), "AND", "==", 0, 0);
        // $this->db->where_as("$this->tbl_as.settlement_status", $this->db->esc("solved_to_seller"), "OR", "==", 1, 0);
        // $this->db->where_as("$this->tbl_as.settlement_status", $this->db->esc("paid_to_seller"), "OR", "==", 0, 1);
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function getBuyerSucceed($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.d_order_detail_id", "d_order_detail_id", 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        $this->db->select_as("$this->tbl2_as.id", "c_produk_id", 0);

        $this->db->select_as("$this->tbl_as.c_produk_id", "d_order_detail_item_id", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl3_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl3_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl2_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl2_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl2_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.buyer_status", "buyer_stauts", 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);
        $this->db->select_as("$this->tbl2_as.settlement_status", "settlement_status", 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->select_as("(1)", "item_total", 0);
        $this->db->select_as("$this->tbl2_as.total_item", "item_total", 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->select_as("$this->tbl2_as.total_qty", "qty", 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.sub_total", "harga_jual", 0);

        $this->db->select_as("$this->tbl2_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost_sub", "shipment_cost_sub", 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->select_as("($this->tbl_as.harga_jual * $this->tbl_as.qty)", "sub_total", 0);
        $this->db->select_as("$this->tbl2_as.sub_total", "sub_total", 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->select_as("($this->tbl_as.harga_jual * $this->tbl_as.qty) + $this->tbl2_as.shipment_cost+$this->tbl2_as.shipment_cost_add", "grand_total", 0);
        $this->db->select_as("$this->tbl2_as.grand_total", "grand_total", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl3_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl3_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.shipment_status", $this->db->esc("succeed"), "AND", "=", 0, 0);
        
        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->where_as("$this->tbl2_as.buyer_confirmed", $this->db->esc("unconfirmed"), "AND", "!=", 0, 0);
        $this->db->where_as("$this->tbl2_as.buyer_confirmed", $this->db->esc("confirmed"), "AND", "=", 0, 0);

        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("accepted"), "AND", "==", 0, 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->where_as("$this->tbl_as.settlement_status", $this->db->esc("solved_to_seller"), "OR", "==", 1, 0);
        // $this->db->where_as("$this->tbl_as.settlement_status", $this->db->esc("paid_to_seller"), "OR", "==", 0, 1);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        $this->db->where_as("$this->tbl2_as.is_rejected_all", $this->db->esc("0"), "AND", "=", 0, 0);

        $this->db->order_by("COALESCE($this->tbl3_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.d_order_detail_id", "asc");
        $this->db->order_by("$this->tbl_as.c_produk_id", "asc");
        $this->db->group_by("CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.d_order_detail_id)");
        return $this->db->get('', 0);
    }

    //rejected
    public function countBuyerRejected($nation_code, $b_user_id)
    {

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.d_order_detail_id,'-',$this->tbl_as.c_produk_id))", "total", 0);
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.d_order_detail_id))", "total", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl3_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        // $this->db->where_as("$this->tbl3_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        $this->db->where_as("$this->tbl3_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);

        $this->db->where_as("$this->tbl2_as.seller_status", $this->db->esc("rejected"), "OR", "=", 1, 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->where_as("$this->tbl2_as.buyer_confirmed", $this->db->esc("completed"), "OR", "=", 0, 0);

        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("rejected"), "OR", "=", 0, 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->where_as("$this->tbl_as.settlement_status", $this->db->esc("complain"), "OR", "==", 0, 0);
        // $this->db->where_as("$this->tbl_as.settlement_status", $this->db->esc("solved_to_buyer"), "OR", "==", 0, 0);
        // $this->db->where_as("$this->tbl_as.settlement_status", $this->db->esc("paid_to_buyer"), "OR", "==", 0, 1);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        $this->db->where_as("$this->tbl2_as.is_rejected_all", $this->db->esc("1"), "AND", "=", 0, 1);

        $d = $this->db->get_first('', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
    public function getBuyerRejected($nation_code, $b_user_id, $page=1, $page_size=10)
    {

        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.d_order_detail_id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "d_order_detail_item_id", 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        $this->db->select_as("$this->tbl2_as.id", "c_produk_id", 0);

        $this->db->select_as("$this->tbl3_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        $this->db->select_as("''", "c_produk_deskripsi", 0);

        $this->db->select_as("$this->tbl3_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl3_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl3_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl2_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.buyer_status", "buyer_status", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->select_as("(1)", "item_total", 0);
        $this->db->select_as("$this->tbl2_as.total_item", "item_total", 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->select_as("$this->tbl2_as.total_qty", "qty", 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->select_as("($this->tbl_as.harga_jual)", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.sub_total", "harga_jual", 0);

        $this->db->select_as("$this->tbl2_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost_sub", "shipment_cost_sub", 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->select_as("($this->tbl_as.qty * $this->tbl_as.harga_jual)", "sub_total", 0);
        $this->db->select_as("$this->tbl2_as.sub_total", "sub_total", 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->select_as("($this->tbl_as.qty * $this->tbl_as.harga_jual)", "grand_total", 0);
        $this->db->select_as("$this->tbl2_as.grand_total", "grand_total", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl3_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl2_as.seller_status", $this->db->esc("rejected"), "OR", "=", 1, 0);
        $this->db->where_as("$this->tbl2_as.buyer_confirmed", $this->db->esc("completed"), "OR", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("rejected"), "OR", "=", 0, 0);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        // $this->db->where_as("$this->tbl_as.settlement_status", $this->db->esc("complain"), "OR", "==", 0, 0);
        // $this->db->where_as("$this->tbl_as.settlement_status", $this->db->esc("solved_to_buyer"), "OR", "==", 0, 0);
        // $this->db->where_as("$this->tbl_as.settlement_status", $this->db->esc("paid_to_buyer"), "OR", "==", 0, 1);

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        $this->db->where_as("$this->tbl2_as.is_rejected_all", $this->db->esc("1"), "AND", "=", 0, 1);

        $this->db->order_by("COALESCE($this->tbl3_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.d_order_detail_id", "desc");

        //by Donny Dennison - 8 february 2021 12:02
        //show half rejected product in api rejected order list
        $this->db->group_by("CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.d_order_detail_id)");
        
        $this->db->page($page, $page_size);
        return $this->db->get('', 0);
    }
    public function getOrderBySeller($nation_code, $d_order_id, $d_order_detail_id, $b_user_id_seller)
    {
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.d_order_detail_id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "d_order_detail_item_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl3_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl3_as.payment_status", "payment_status", 0);
        $this->db->select_as("$this->tbl2_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl2_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl2_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.buyer_status", "buyer_status", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);
        $this->db->select_as("$this->tbl_as.is_fashion", "is_fashion", 0);
        $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $d_order_detail_id);
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id_seller));
        return $this->db->get('', 0);
    }

    public function getOrderByDetailId($nation_code, $d_order_id, $d_order_detail_id)
    {
        $this->db->select_as("$this->tbl_as.*,$this->tbl_as.c_produk_id", "id", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl2_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_status", "buyer_status", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);
        $this->db->select_as("$this->tbl_as.is_fashion", "is_fashion", 0);
        $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
        $this->db->select_as("$this->tbl2_as.is_rejected_all", "is_rejected_all", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $d_order_detail_id);
        $this->db->order_by("$this->tbl_as.c_produk_id", "asc");
        return $this->db->get('', 0);
    }
    public function getOrderDetailByOrderIdProdukId($nation_code, $d_order_id, $c_produk_id)
    {
        $this->db->select_as("$this->tbl_as.d_order_detail_id", "d_order_detail_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        $this->db->where_as("$this->tbl_as.c_produk_id", $c_produk_id);
        $d = $this->db->get_first('', 0);
        if (isset($d->d_order_detail_id)) {
            return $d->d_order_detail_id;
        }
        return 0;
    }
    public function getByOrderDetailIdForShipment($nation_code, $d_order_id, $d_order_detail_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.c_produk_id", "id", 0);
        $this->db->select_as("$this->tbl_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl2_as.shipment_service", "courier_services", 0);
        $this->db->select_as("$this->tbl_as.is_include_delivery_cost", "is_include_delivery_cost", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.d_order_id", $d_order_id);
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $d_order_detail_id);
        $this->db->order_by("$this->tbl_as.d_order_id", "asc");
        $this->db->order_by("$this->tbl_as.d_order_detail_id", "asc");
        $this->db->order_by("$this->tbl_as.c_produk_id", "asc");
        return $this->db->get();
    }
    public function getPendingOrder($nation_code,$b_user_id){
      $this->db->select_as("$this->tbl_as.d_order_id","d_order_id");
      $this->db->select_as("$this->tbl_as.d_order_detail_id","d_order_detail_id");
      $this->db->select_as("$this->tbl_as.c_produk_id","c_produk_id");
      $this->db->select_as("$this->tbl_as.c_produk_id","id");
      $this->db->select_as("$this->tbl_as.qty","qty");
      $this->db->from($this->tbl, $this->tbl_as);
      $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
      $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
      $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
      $this->db->where_as("$this->tbl3_as.b_user_id",$this->db->esc($b_user_id));
      return $this->db->get();
    }

    //by Donny Dennison 7 oktober 2020 - 14:10
    //add promotion face mask
    public function checkAlreadyOrderFaceMask($nation_code, $buyer_id, $c_produk_id)
    {
        $this->db->select_as("COUNT(*)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl3_as.b_user_id", $this->db->esc($buyer_id));
        $this->db->where_as("$this->tbl_as.c_produk_id", $c_produk_id);
        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("waiting_for_payment"), "OR", "=", 1, 0);
        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("forward_to_seller"), "OR", "=", 0, 1);
        $d = $this->db->get_first('', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    //by Donny Dennison 13 september 2021 - 10:38
    //revamp-profile
    //START by Donny Dennison 13 september 2021 - 10:38
    public function countBuyingHistoryFinished($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.d_order_detail_id))", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl3_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl3_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl2_as.shipment_status", $this->db->esc("delivered"), "OR", "=", 1, 0);
        // $this->db->where_as("$this->tbl2_as.shipment_status", $this->db->esc("succeed"), "AND", "=", 0, 1);
        $this->db->where_as("$this->tbl2_as.shipment_status", $this->db->esc("succeed"), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl2_as.buyer_confirmed", $this->db->esc("unconfirmed"), "AND", "!=", 0, 0);
        // $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("accepted"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("rejected"), "AND", "!=", 0, 0);
        $this->db->where_as("$this->tbl2_as.is_rejected_all", $this->db->esc("0"), "AND", "=", 0, 0);

        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getBuyingHistoryFinished($nation_code, $b_user_id, $page=1, $page_size=10)
    {
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.d_order_detail_id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "d_order_detail_item_id", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl3_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl3_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl2_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl2_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl2_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.buyer_status", "buyer_stauts", 0);
        $this->db->select_as("$this->tbl2_as.settlement_status", "settlement_status", 0);
        $this->db->select_as("$this->tbl2_as.total_item", "item_total", 0);
        $this->db->select_as("$this->tbl2_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl2_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost_sub", "shipment_cost_sub", 0);
        $this->db->select_as("$this->tbl2_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl2_as.grand_total", "grand_total", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl3_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl3_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl2_as.shipment_status", $this->db->esc("delivered"), "OR", "=", 1, 0);
        // $this->db->where_as("$this->tbl2_as.shipment_status", $this->db->esc("succeed"), "AND", "=", 0, 1);
        $this->db->where_as("$this->tbl2_as.shipment_status", $this->db->esc("succeed"), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl2_as.buyer_confirmed", $this->db->esc("unconfirmed"), "AND", "!=", 0, 0);
        // $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("accepted"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("rejected"), "AND", "!=", 0, 0);
        $this->db->where_as("$this->tbl2_as.is_rejected_all", $this->db->esc("0"), "AND", "=", 0, 0);

        $this->db->order_by("COALESCE($this->tbl3_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.d_order_detail_id", "asc");
        $this->db->order_by("$this->tbl_as.c_produk_id", "asc");
        $this->db->group_by("CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.d_order_detail_id)");
        $this->db->page($page, $page_size);
        return $this->db->get('', 0);
    }

    public function countSellingHistoryFinished($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.d_order_detail_id))", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl3_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl2_as.shipment_status", $this->db->esc("delivered"), "OR", "=", 1, 0);
        $this->db->where_as("$this->tbl2_as.shipment_status", $this->db->esc("succeed"), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl2_as.buyer_confirmed", $this->db->esc("unconfirmed"), "AND", "!=", 0, 0);
        // $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("accepted"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.is_rejected_all", $this->db->esc("0"), "AND", "=", 0, 0);

        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getSellingHistoryFinished($nation_code, $b_user_id, $page=1, $page_size=10)
    {
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.d_order_detail_id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "d_order_detail_item_id", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl3_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.foto", "c_produk_foto", 0);
        $this->db->select_as("$this->tbl_as.thumb", "c_produk_thumb", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl3_as.ldate", "d_order_ldate", 0);
        $this->db->select_as("$this->tbl2_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl2_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl2_as.buyer_confirmed", "buyer_confirmed", 0);
        $this->db->select_as("$this->tbl_as.buyer_status", "buyer_stauts", 0);
        $this->db->select_as("$this->tbl2_as.settlement_status", "settlement_status", 0);
        $this->db->select_as("$this->tbl2_as.total_item", "item_total", 0);
        $this->db->select_as("$this->tbl2_as.total_qty", "qty", 0);
        $this->db->select_as("$this->tbl2_as.sub_total", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost_sub", "shipment_cost_sub", 0);
        $this->db->select_as("$this->tbl2_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl2_as.grand_total", "grand_total", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id), "AND", "=");
        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl3_as.payment_status", $this->db->esc("paid"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl2_as.shipment_status", $this->db->esc("delivered"), "OR", "=", 1, 0);
        $this->db->where_as("$this->tbl2_as.shipment_status", $this->db->esc("succeed"), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl2_as.buyer_confirmed", $this->db->esc("unconfirmed"), "AND", "!=", 0, 0);
        // $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("accepted"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.is_rejected_all", $this->db->esc("0"), "AND", "=", 0, 0);

        $this->db->order_by("COALESCE($this->tbl3_as.cdate,NOW())", "DESC");
        $this->db->order_by("$this->tbl_as.d_order_id", "desc");
        $this->db->order_by("$this->tbl_as.d_order_detail_id", "asc");
        $this->db->order_by("$this->tbl_as.c_produk_id", "asc");
        $this->db->group_by("CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.d_order_detail_id)");
        $this->db->page($page, $page_size);
        return $this->db->get('', 0);
    }
    //END by Donny Dennison 13 september 2021 - 10:38

    //by Donny Dennison - 19 july 2022 15:42
    //delete temporary or permanent user feature
    public function countUnfinishedOrderBuyer($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.d_order_detail_id))", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl3_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl3_as.payment_status", $this->db->esc("void"), "AND", "!=");
        $this->db->where_as("$this->tbl3_as.payment_status", $this->db->esc("failed"), "AND", "!=");
        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("cancelled"), "AND", "!=");
        // $this->db->where_as("$this->tbl2_as.seller_status", $this->db->esc("confirmed"), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl2_as.shipment_status", $this->db->esc("succeed"), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl2_as.settlement_status", $this->db->esc("completed"), "AND", "!=", 0, 0);
        // $this->db->where_as("$this->tbl2_as.buyer_confirmed", $this->db->esc("unconfirmed"), "AND", "!=", 0, 0);
        // $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("accepted"), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("rejected"), "AND", "!=", 0, 0);
        // $this->db->where_as("$this->tbl2_as.is_rejected_all", $this->db->esc("0"), "AND", "=", 0, 0);

        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

}
