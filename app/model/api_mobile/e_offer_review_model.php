<?php
class E_Offer_Review_Model extends JI_Model
{
    public $tbl = 'e_offer_review';
    public $tbl_as = 'efr';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }
    
    public function getTblAs()
    {
        return $this->tbl_as;
    }

    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
        return $cps;
    }

    public function getLastId($nation_code)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }

    public function countAll($nation_code, $b_user_id_to, $type)
    {
        $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id_to", $this->db->esc($b_user_id_to));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
        
        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;

    }

    public function getAll($nation_code, $b_user_id_to, $page=1, $page_size=10, $sort_col='cdate', $sort_dir='DESC', $type, $language_id=2)
    {
        // $this->db->select_as("$this->tbl_as.id", "chat_id", 0);
        // $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        // $this->db->select_as("$this->tbl_as.type", "type", 0);
        $this->db->select_as("$this->tbl_as.star", "star", 0);
        $this->db->select_as("$this->tbl_as.review", "review", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        // $this->db->select_as("COALESCE($this->tbl2_as.id,'0')", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.image,'media/user/default-profile-picture.png')", "b_user_image", 0);
        $this->db->select_as("$this->tbl2_as.is_admin", "is_admin", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id_to", $this->db->esc($b_user_id_to));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));

        $this->db->order_by($sort_col, $sort_dir)->page($page, $page_size);

        return $this->db->get();
    }

    // public function getLastChatByChatRoomId($nation_code, $chat_room_id, $last_delete_chat, $language_id=2)
    // {
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.type", "type", 0);
    //     // $this->db->select_as("$this->tbl_as.message", "message", 0);
    //     $this->db->select_as("IF($language_id = 2 AND $this->tbl_as.type = 'announcement' , $this->tbl_as.message_indonesia, $this->tbl_as.message)", "message");
    //     $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "a_pengguna_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "a_pengguna_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.foto,'media/pengguna/default.png')", "a_pengguna_foto", 0);
    //     $this->db->select_as("COALESCE($this->tbl2_as.id,'0')", "b_user_id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama", 0);
    //     $this->db->select_as("COALESCE($this->tbl2_as.image,'media/user/default-profile-picture.png')", "b_user_image", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.e_chat_room_id", $this->db->esc($chat_room_id));
    //     $this->db->where_as("$this->tbl_as.type", $this->db->esc('announcement'), "AND","!=");

    //     if($last_delete_chat){

    //         $this->db->where_as("$this->tbl_as.cdate", $this->db->esc($last_delete_chat), 'AND', '>');
        
    //     }

    //     $this->db->order_by("$this->tbl_as.cdate", 'desc');

    //     return $this->db->get_first();
    // }

    public function getByChatRoomId($nation_code, $chat_room_id, $chat_id, $type="")
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.e_chat_room_id", "chat_room_id", 0);
        // $this->db->select_as("$this->tbl_as.type", "type", 0);
        // $this->db->select_as("$this->tbl_as.message", "message", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.e_chat_room_id", $this->db->esc($chat_room_id));
        $this->db->where_as("$this->tbl_as.e_chat_id", $this->db->esc($chat_id));

        if($type != ""){
            $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
        }

        return $this->db->get_first();
    }

    public function getAvg($nation_code, $b_user_id_to, $type)
    {
        $this->db->select_as("COALESCE(AVG($this->tbl_as.star),'0')", "avg_star", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id_to", $this->db->esc($b_user_id_to));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));

        return $this->db->get_first();
    }

    public function set($di)
    {
        return $this->db->insert($this->tbl, $di);
    }

    // public function update($nation_code, $b_user_id, $id, $du)
    // {
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("b_user_id", $b_user_id);
    //     $this->db->where("id", $id);
    //     return $this->db->update($this->tbl, $du);
    // }

    // public function delete($nation_code, $b_user_id, $id)
    // {
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("b_user_id", $b_user_id);
    //     $this->db->where("id", $id);
    //     return $this->db->delete($this->tbl);
    // }

    // public function getAllUnreadByChatRoomIdUserId($nation_code, $chat_room_id, $b_user_id, $language_id=2)
    // {
    //     $this->db->select_as("$this->tbl_as.id", "chat_id", 0);
    //     $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
    //     $this->db->select_as("$this->tbl_as.type", "type", 0);
    //     // $this->db->select_as("$this->tbl_as.message", "message", 0);
    //     $this->db->select_as("IF($language_id = 2 AND $this->tbl_as.type = 'announcement' , $this->tbl_as.message_indonesia, $this->tbl_as.message)", "message");
    //     $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "a_pengguna_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "a_pengguna_nama", 0);
    //     $this->db->select_as("COALESCE($this->tbl4_as.foto,'media/pengguna/default.png')", "a_pengguna_foto", 0);
    //     $this->db->select_as("COALESCE($this->tbl2_as.id,'0')", "b_user_id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama", 0);
    //     $this->db->select_as("COALESCE($this->tbl2_as.image,'media/user/default-profile-picture.png')", "b_user_image", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
    //     $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl3_as.e_chat_room_id", $this->db->esc($chat_room_id));
    //     $this->db->where_as("$this->tbl3_as.b_user_id", $this->db->esc($b_user_id));
    //     $this->db->where_as("$this->tbl3_as.is_read", $this->db->esc("0"));

    //     $this->db->order_by("$this->tbl_as.cdate", "DESC");

    //     return $this->db->get();
    // }

    // public function getLastOfferByChatRoomId($nation_code, $chat_room_id)
    // {
    //     // $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.message", "message", 0);
    //     // $this->db->select_as("IF($language_id = 2 AND $this->tbl_as.type = 'announcement' , $this->tbl_as.message_indonesia, $this->tbl_as.message)", "message");
    //     // $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    //     // $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "a_pengguna_id", 0);
    //     // $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "a_pengguna_nama", 0);
    //     // $this->db->select_as("COALESCE($this->tbl4_as.foto,'media/pengguna/default.png')", "a_pengguna_foto", 0);
    //     // $this->db->select_as("COALESCE($this->tbl2_as.id,'0')", "b_user_id", 0);
    //     // $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama", 0);
    //     // $this->db->select_as("COALESCE($this->tbl2_as.image,'media/user/default-profile-picture.png')", "b_user_image", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
    //     // $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
    //     // $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.e_chat_room_id", $this->db->esc($chat_room_id));
    //     $this->db->where_as("$this->tbl_as.type", $this->db->esc('offering'));

    //     $this->db->order_by("$this->tbl_as.cdate", 'desc');

    //     return $this->db->get_first();
    // }

}
