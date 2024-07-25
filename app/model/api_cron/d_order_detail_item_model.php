<?php
class D_Order_Detail_Item_Model extends JI_Model
{
    public $tbl = 'd_order_detail_item';
    public $tbl_as = 'dodi';
    public $tbl2 = 'd_order_detail';
    public $tbl2_as = 'dod';
    public $tbl3 = 'd_order';
    public $tbl3_as = 'dor';
    public $tbl4 = "c_produk";
    public $tbl4_as = "cp";
    public $tbl5 = "b_user";
    public $tbl5_as = "bu";
    public $tbl6 = "b_user_alamat";
    public $tbl6_as = "bua";
    public $tbl7 = "b_user";
    public $tbl7_as = "bu2";

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.d_order_id", "=", "$this->tbl2_as.d_order_id");
        $composites[] = $this->db->composite_create("$this->tbl_as.d_order_detail_id", "=", "$this->tbl2_as.id");
        return $composites;
    }

    private function __joinTbl3()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl2_as.d_order_id", "=", "$this->tbl3_as.id");
        return $composites;
    }

    //join table d_order_detail with d_order
    private function __joinTbl4()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.c_produk_id", "=", "$this->tbl4_as.id");
        return $composites;
    }

    public function getAll()
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl4_as.id", "id", 0);
        $this->db->select_as("$this->tbl4_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl4_as.thumb", "thumb", 0);
        $this->db->select_as("$this->tbl4_as.foto", "foto", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'inner');
        $this->db->order_by("$this->tbl_as.nation_code", "ASC");
        $this->db->order_by("$this->tbl_as.d_order_id", "ASC");
        $this->db->order_by("$this->tbl_as.d_order_detail_id", "ASC");
        $this->db->order_by("$this->tbl_as.c_produk_id", "ASC");
        return $this->db->get();
    }
    public function set()
    {
    }
    public function update($nation_code, $d_order_id, $d_order_detail_id, $d_order_detail_item_id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("d_order_detail_id", $d_order_detail_id);
        $this->db->where("c_produk_id", $d_order_detail_item_id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function updateByDetailId($nation_code, $d_order_id, $d_order_detail_id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("d_order_detail_id", $d_order_detail_id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function delByOrderId($nation_code, $d_order_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        return $this->db->delete($this->tbl);
    }
    public function getDelivereds()
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.d_order_detail_id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "d_order_detail_item_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "id", 0);
        $this->db->select_as("$this->tbl3_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl_as.shipment_type", "shipment_type", 0);
        $this->db->select_as("$this->tbl_as.shipment_tranid", "shipment_tranid", 0);
        $this->db->select_as("$this->tbl4_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl3_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl2_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl2_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_status", "buyer_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "inner");
        $this->db->where_as("DATE_ADD(COALESCE($this->tbl2_as.delivery_date,NOW()), INTERVAL 24 HOUR)", "NOW()", "AND", "<=");
        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("wait"), "AND", "=");
        $this->db->where_as("$this->tbl2_as.shipment_status", $this->db->esc("delivered"), "AND", "=");
        $this->db->where_as("$this->tbl2_as.seller_status", $this->db->esc("confirmed"), "AND", "=");
        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=");
        $this->db->order_by("$this->tbl3_as.cdate", "asc");
        return $this->db->get('', 0);
    }

    /**
     * Get order list where its on sent status
     * @param  integer $interval date interval
     * @return array             array of object
     */
    public function getSent(int $interval=3)
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.d_order_detail_id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "d_order_detail_item_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "id", 0);
        $this->db->select_as("$this->tbl3_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl2_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl4_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("($this->tbl_as.qty * $this->tbl_as.harga_jual)", "sub_total", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl2_as.shipment_type", "shipment_type", 0);
        $this->db->select_as("$this->tbl2_as.shipment_tranid", "shipment_tranid", 0);
        $this->db->select_as("$this->tbl3_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl3_as.payment_status", "payment_status", 0);
        $this->db->select_as("$this->tbl2_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl2_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_status", "buyer_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "inner");
        $this->db->where_as("COALESCE($this->tbl2_as.delivery_date,'-')", $this->db->esc("-"), "AND", "<>");

        //by Donny Dennison - 9 july 2020 20:56
        //Requested by Mr Jackie, change where from delivery_date to pickup_date
        // $this->db->where_as("DATE_ADD(COALESCE($this->tbl2_as.delivery_date,NOW()), INTERVAL $interval DAY)", "NOW()", "AND", "<=");
        $this->db->where_as("DATE_ADD(COALESCE($this->tbl2_as.pickup_date,NOW()), INTERVAL $interval DAY)", "NOW()", "AND", "<=");

        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=");
        $this->db->where_as("$this->tbl2_as.seller_status", $this->db->esc("confirmed"), "AND", "=");
        $this->db->where_as("$this->tbl2_as.shipment_status", $this->db->esc("succeed"), "AND", "=");
        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("wait"), "AND", "=");
        $this->db->order_by("$this->tbl3_as.cdate", "asc");
        return $this->db->get('', 0);
    }

    public function getUnSettled()
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.d_order_detail_id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "d_order_detail_item_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "id", 0);
        $this->db->select_as("$this->tbl3_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl2_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl2_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl_as.buyer_status", "buyer_status", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("($this->tbl_as.qty * $this->tbl_as.harga_jual)", "sub_total", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost_sub", "shipment_cost_sub", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "inner");
        //$this->db->where_as("COALESCE($this->tbl2_as.received_date,'-')",$this->db->esc("-"),"AND","<>");
        //$this->db->where_as("DATE_ADD(COALESCE($this->tbl2_as.received_date,NOW()), INTERVAL 3 DAY)","NOW()","AND","<=");
        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=");
        $this->db->where_as("$this->tbl2_as.seller_status", $this->db->esc("confirmed"), "AND", "=");
        $this->db->where_as("$this->tbl_as.buyer_status", $this->db->esc("wait"), "AND", "!=");
        $this->db->where_as("$this->tbl2_as.is_calculated", $this->db->esc("0"), "AND", "=");
        $this->db->where_as("$this->tbl2_as.settlement_status", $this->db->esc("completed"), "AND", "!=", 0, 0);
        $this->db->where_as("$this->tbl2_as.shipment_status", $this->db->esc("delivered"), "OR", "=", 1, 0);
        $this->db->where_as("$this->tbl2_as.shipment_status", $this->db->esc("succeed"), "OR", "=", 0, 1);
        $this->db->order_by("$this->tbl3_as.cdate", "asc");
        $this->db->group_by("CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.d_order_detail_id,'-',$this->tbl_as.c_produk_id)", "asc");
        return $this->db->get('', 0);
    }

    public function getSellerRejected()
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.d_order_detail_id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "d_order_detail_item_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "id", 0);
        $this->db->select_as("$this->tbl3_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_seller", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_code", 0);
        $this->db->select_as("$this->tbl2_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl4_as.nama", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "harga_jual", 0);
        $this->db->select_as("$this->tbl2_as.sub_total", "sub_total", 0);
        $this->db->select_as("$this->tbl2_as.grand_total", "grand_total", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost", "shipment_cost", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost_add", "shipment_cost_add", 0);
        $this->db->select_as("$this->tbl2_as.shipment_cost_sub", "shipment_cost_sub", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl2_as.shipment_service", "shipment_service", 0);
        $this->db->select_as("$this->tbl2_as.shipment_type", "shipment_type", 0);
        $this->db->select_as("$this->tbl2_as.shipment_tranid", "shipment_tranid", 0);
        $this->db->select_as("$this->tbl3_as.order_status", "order_status", 0);
        $this->db->select_as("$this->tbl3_as.payment_status", "payment_status", 0);
        $this->db->select_as("$this->tbl2_as.seller_status", "seller_status", 0);
        $this->db->select_as("$this->tbl2_as.shipment_status", "shipment_status", 0);
        $this->db->select_as("$this->tbl_as.buyer_status", "buyer_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "inner");
        $this->db->where_as("$this->tbl3_as.order_status", $this->db->esc("forward_to_seller"), "AND", "=");
        $this->db->where_as("$this->tbl2_as.seller_status", $this->db->esc("rejected"), "AND", "=");
        $this->db->where_as("$this->tbl2_as.is_calculated", $this->db->esc("0"), "AND", "=");
        $this->db->where_as("$this->tbl2_as.settlement_status", $this->db->esc("completed"), "AND", "!=", 0, 0);
        $this->db->order_by("$this->tbl3_as.cdate", "asc");
        $this->db->group_by("CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.d_order_detail_id)");
        return $this->db->get('', 0);
    }
    public function searchInv($inv)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.nation_code", "nation_code", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->where_as("$this->tbl3_as.invoice_code", $this->db->esc($inv));
        return $this->db->get();
    }
    public function get()
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl4_as.id", "id", 0);
        $this->db->select_as("$this->tbl4_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl4_as.foto", "foto", 0);
        $this->db->select_as("$this->tbl4_as.thumb", "thumb", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'inner');
        $this->db->order_by("$this->tbl_as.d_order_id", "ASC");
        $this->db->order_by("$this->tbl_as.d_order_detail_id", "ASC");
        $this->db->order_by("$this->tbl_as.c_produk_id", "ASC");
        return $this->db->get();
    }
    public function getByOrderDetailid($nation_code, $d_order_id, $d_order_detail_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl4_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);
        $this->db->select_as("$this->tbl4_as.stok", "stok", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("$this->tbl_as.d_order_detail_id", $this->db->esc($d_order_detail_id));
        return $this->db->get();
    }
}
