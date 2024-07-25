<?php
class E_Complain_model extends JI_Model
{
    public $tbl = 'e_complain';
    public $tbl_as = 'er';
    public $tbl2 = 'd_order';
    public $tbl2_as = 'dor';
    public $tbl3 = 'c_produk';
    public $tbl3_as = 'cp';
    public $tbl4 = 'b_user';
    public $tbl4_as = 'bu';
    public $tbl5 = 'b_user';
    public $tbl5_as = 'bs';
    public $tbl6 = 'd_order_detail';
    public $tbl6_as = 'dod';
    public $tbl7 = 'd_order_detail_item';
    public $tbl7_as = 'dodi';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.d_order_id", "=", "$this->tbl2_as.id");
        return $cps;
    }
    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.c_produk_id", "=", "$this->tbl3_as.id");
        return $cps;
    }

    //for buyer, required joinTbl2
    private function __joinTbl4()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl2_as.b_user_id", "=", "$this->tbl4_as.id");
        return $cps;
    }

    //for seller, required joinTbl3
    private function __joinTbl5()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl3_as.nation_code", "=", "$this->tbl5_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl3_as.b_user_id", "=", "$this->tbl5_as.id");
        return $cps;
    }

    private function __joinTbl6()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl6_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.d_order_id", "=", "$this->tbl6_as.d_order_id");
        $cps[] = $this->db->composite_create("$this->tbl_as.d_order_detail_id", "=", "$this->tbl6_as.id");
        return $cps;
    }

    // private function __joinTbl7()
    // {
    //     $cps = array();
    //     $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl7_as.nation_code");
    //     $cps[] = $this->db->composite_create("$this->tbl_as.d_order_id", "=", "$this->tbl7_as.d_order_id");
    //     $cps[] = $this->db->composite_create("$this->tbl_as.d_order_detail_id", "=", "$this->tbl7_as.d_order_detail_id");
    //     $cps[] = $this->db->composite_create("$this->tbl_as.c_produk_id", "=", "$this->tbl7_as.c_produk_id");
    //     return $cps;
    // }

    public function set($di)
    {
        return $this->db->insert($this->tbl, $di);
    }
    // public function update($nation_code, $d_order_id, $c_produk_id, $du)
    // {
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("d_order_id", $d_order_id);
    //     $this->db->where("c_produk_id", $c_produk_id);
    //     return $this->db->update($this->tbl, $du);
    // }
    public function check($nation_code, $d_order_id, $d_order_detail_id, $b_user_id_seller, $b_user_id_buyer)
    {
        $this->db->select_as("COUNT(*)", "total", 0);
        $this->db->where_as("nation_code", $nation_code);
        $this->db->where_as("d_order_id", $d_order_id);
        $this->db->where_as("d_order_detail_id", $d_order_detail_id);
        $this->db->where_as("b_user_id_seller", $this->db->esc($b_user_id_seller));
        $this->db->where_as("b_user_id_buyer", $this->db->esc($b_user_id_buyer));
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 1;
    }
    public function getDetailByChatRoomID($nation_code, $chat_room_id, $e_chat_ids=array())
    {
        $this->db->select_as("$this->tbl_as.e_chat_id", "e_chat_id", 0);
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.d_order_detail_id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "d_order_detail_item_id", 0);
        // $this->db->select_as("$this->tbl_as.d_order_detail_id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.dari", "dari", 0);
        $this->db->select_as("$this->tbl_as.alasan", "alasan", 0);
        $this->db->select_as("$this->tbl_as.complain_status", "complain_status", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_user_id_buyer", 0);
        $this->db->select_as($this->__decrypt("$this->tbl4_as.fnama"), "b_user_fnama_buyer", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.image,'media/user/default-profile-picture.png')", "b_user_image_buyer", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.id,'0')", "b_user_id_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "b_user_fnama_seller", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.image,'media/user/default-profile-picture.png')", "b_user_image_seller", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.nama,'0')", "c_produk_nama", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.foto,'0')", "c_produk_foto", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.thumb,'0')", "c_produk_thumb", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.sub_total,'0')", "harga_jual", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.total_qty,'0')", "qty", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.invoice_code,'0')", "invoice_code", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.payment_status,'0')", "payment_status", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.order_status,'0')", "order_status", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.seller_status,'0')", "seller_status", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.shipment_status,'0')", "shipment_status", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.buyer_confirmed,'0')", "buyer_confirmed", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.settlement_status,'0')", "settlement_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "inner");
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.e_chat_room_id", $this->db->esc($chat_room_id));
        
        if($e_chat_ids){
            $this->db->where_in("$this->tbl_as.e_chat_id", $e_chat_ids);
        }

        return $this->db->get('', 0);
    }

    public function getDetailByChatRoomIDChatID($nation_code, $chat_room_id, $chat_id)
    {
        $this->db->select_as("$this->tbl_as.e_chat_id", "e_chat_id", 0);
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.d_order_detail_id", "d_order_detail_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "d_order_detail_item_id", 0);
        // $this->db->select_as("$this->tbl_as.d_order_detail_id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.dari", "dari", 0);
        $this->db->select_as("$this->tbl_as.alasan", "alasan", 0);
        $this->db->select_as("$this->tbl_as.complain_status", "complain_status", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "b_user_id_buyer", 0);
        $this->db->select_as($this->__decrypt("$this->tbl4_as.fnama"), "b_user_fnama_buyer", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.image,'media/user/default-profile-picture.png')", "b_user_image_buyer", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.id,'0')", "b_user_id_seller", 0);
        $this->db->select_as($this->__decrypt("$this->tbl5_as.fnama"), "b_user_fnama_seller", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.image,'media/user/default-profile-picture.png')", "b_user_image_seller", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.nama,'0')", "c_produk_nama", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.foto,'0')", "c_produk_foto", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.thumb,'0')", "c_produk_thumb", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.sub_total,'0')", "harga_jual", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.total_qty,'0')", "qty", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.invoice_code,'0')", "invoice_code", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.payment_status,'0')", "payment_status", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.order_status,'0')", "order_status", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.seller_status,'0')", "seller_status", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.shipment_status,'0')", "shipment_status", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.buyer_confirmed,'0')", "buyer_confirmed", 0);
        $this->db->select_as("COALESCE($this->tbl6_as.settlement_status,'0')", "settlement_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "inner");
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.e_chat_room_id", $this->db->esc($chat_room_id));
        $this->db->where_as("$this->tbl_as.e_chat_id", $chat_id);
        return $this->db->get_first('', 0);
    }

}
