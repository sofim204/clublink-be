<?php
class E_Chat_Model extends JI_Model
{
    public $tbl = 'e_chat';
    public $tbl_as = 'ec';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';
    public $tbl3 = 'e_complain';
    public $tbl3_as = 'ecom';
    public $tbl4 = 'a_pengguna';
    public $tbl4_as = 'ap';
    public $tbl5 = 'c_produk';
    public $tbl5_as = 'cp';
    public $tbl6 = 'e_chat_attachment';
    public $tbl6_as = 'eca';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
        return $cps;
    }

    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.d_order_id", "=", "$this->tbl3_as.d_order_id");
        return $cps;
    }

    private function __joinTbl4()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.a_pengguna_id", "=", "$this->tbl4_as.id");
        return $cps;
    }

    private function __joinTbl5()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl5_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.c_produk_id", "=", "$this->tbl5_as.id");
        return $cps;
    }

    private function __joinTbl6()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl6_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl6_as.e_chat_id");
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
        return $this->tbl5_as;
    }

    public function getById($id)
    {
        $this->db->where('id', $id);
        return $this->db->get_first();
    }

    public function getByOrderIds($ids=array())
    {
        $this->db->where_in('d_order_id', $ids);
        return $this->db->get();
    }

    public function getByOrderId($nation_code, $d_order_id)
    {
        $this->db->flushQuery();
        //$this->db->select_as("$this->tbl_as.id","id",0);
        $this->db->select_as("$this->tbl_as.d_order_id", "order_id", 0);
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'-',$this->tbl_as.c_produk_id)", "id", 0);
        //$this->db->select_as("$this->tbl_as.nation_code","nation_code",0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        //$this->db->select_as("COALESCE($this->tbl_as.jenis,'-')","jenis",0);
        //$this->db->select_as("COALESCE($this->tbl_as.message,'-')","message",0);
        $this->db->select_as("CONCAT('#','ROOM',$this->tbl_as.d_order_id)", "d_order_id", 0);
        $this->db->select_as("GROUP_CONCAT(DISTINCT AES_ENCRYPT($this->tbl2_as.fnama,'$this->db->enckey') SEPARATOR ', ')", "b_user_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.alasan,'-')", "alasan", 0);
        $this->db->select_as("COUNT(*)", "total_chat", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id), "AND", "=", 0, 0);
        $this->db->group_by("$this->tbl_as.d_order_id,$this->tbl_as.c_produk_id");
        return $this->db->get("object", 0);
    }

    public function getByOrderIdDetail($nation_code, $d_order_id)
    {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.id", "id_chat", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "order_id", 0);
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'-',$this->tbl_as.c_produk_id)", "id", 0);
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("COALESCE($this->tbl_as.jenis,'-')", "jenis", 0);
        $this->db->select_as("COALESCE($this->tbl_as.message,'-')", "message", 0);
        $this->db->select_as("CONCAT('#','ROOM',$this->tbl_as.d_order_id)", "d_order_id", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.id,'-')", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.image,'-')", "b_user_image", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.alasan,'-')", "alasan", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.id,'-')", "a_pengguna_id", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "a_pengguna_nama", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.foto,'-')", "a_pengguna_foto", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "c_produk_id", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.nama,'-')", "c_produk_nama", 0);
        $this->db->select_as("$this->tbl6_as.url", "url", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id), "AND", "=", 0, 0);
        $this->db->order_by('id', 'asc');
        return $this->db->get("object", 0);
    }

    public function get($limit="100", $is_starter="0")
    {
        $this->db->order_by('id', 'asc');
        $this->db->where('is_starter', $is_starter);
        $this->db->limit($limit);
        return $this->db->get();
    }
    public function getDetailByID($nation_code, $d_order_id, $c_produk_id, $chat_type)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.id,'0')", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.image,'')", "b_user_image", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "a_pengguna_id", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "a_pengguna_nama", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.foto,'')", "a_pengguna_foto", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.c_produk_id", $this->db->esc($c_produk_id));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("$this->tbl_as.chat_type", $this->db->esc($chat_type));
        $this->db->order_by("$this->tbl_as.id", 'asc');
        return $this->db->get("object", 0);
    }

   /* public function getDetailByIDDeff($nation_code, $d_order_id, $c_produk_id, $chat_type)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.id,'0')", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.image,'')", "b_user_image", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "a_pengguna_id", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "a_pengguna_nama", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.foto,'')", "a_pengguna_foto", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.c_produk_id", $this->db->esc($c_produk_id));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("$this->tbl_as.chat_type", $this->db->esc($chat_type));
        $this->db->order_by("$this->tbl_as.id", 'asc');
        return $this->db->get("object", 0);
    }
*/
    public function getDetailSeller($nation_code, $d_order_id, $c_produk_id, $seller_id, $chat_type)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.id,'0')", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.image,'')", "b_user_image", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "a_pengguna_id", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "a_pengguna_nama", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.foto,'')", "a_pengguna_foto", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.c_produk_id", $this->db->esc($c_produk_id));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        /*$this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($seller_id));*/
        $this->db->where_as("$this->tbl_as.chat_type", $this->db->esc($chat_type));
        $this->db->order_by("$this->tbl_as.id", 'asc');
        return $this->db->get("object", 0);
    }

    public function getDetailBuyer($nation_code, $d_order_id, $c_produk_id, $buyer_id, $chat_type)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.id,'0')", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.image,'')", "b_user_image", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "a_pengguna_id", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "a_pengguna_nama", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.foto,'')", "a_pengguna_foto", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.c_produk_id", $this->db->esc($c_produk_id));
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id));
        /*$this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($buyer_id));*/
        $this->db->where_as("$this->tbl_as.chat_type", $this->db->esc($chat_type));
        $this->db->order_by("$this->tbl_as.id", 'asc');
        return $this->db->get("object", 0);
    }

    //by Donny Dennison - 15 september 2020 16:59
    //add flag unread chat
    public function updateByOrderIDProdukIDChatType($nation_code, $d_order_id, $c_produk_id, $chat_type, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where_as("nation_code", $this->db->esc($nation_code));
        $this->db->where_as("d_order_id", $this->db->esc($d_order_id));
        $this->db->where_as("c_produk_id", $this->db->esc($c_produk_id));
        $this->db->where_as("chat_type", $this->db->esc($chat_type));
        $this->db->where_as("is_read_admin", $this->db->esc(0));
        return $this->db->update($this->tbl, $du, 0);
    }

}
