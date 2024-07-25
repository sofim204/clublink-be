<?php
class E_Chat_Model extends JI_Model
{
    public $is_cacheable;
    public $tbl = 'e_chat';
    public $tbl_as = 'ec';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';
    public $tbl3 = 'e_chat_read';
    public $tbl3_as = 'ecr';
    public $tbl4 = 'a_pengguna';
    public $tbl4_as = 'ap';

    public function __construct()
    {
        parent::__construct();
        $this->is_cacheable = 0;
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

    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.e_chat_room_id", "=", "$this->tbl3_as.e_chat_room_id");
        $cps[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl3_as.e_chat_id");
        return $cps;
    }

    private function __joinTbl4()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.a_pengguna_id", "=", "$this->tbl4_as.id");
        return $cps;
    }

    public function getLastId($nation_code, $chat_room_id)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("e_chat_room_id", $chat_room_id);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }

    public function countAllByChatRoomId($nation_code, $chat_room_id, $last_delete_chat)
    {
        $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.e_chat_room_id", $this->db->esc($chat_room_id));
        
        if($last_delete_chat){

            $this->db->where_as("$this->tbl_as.cdate", $this->db->esc($last_delete_chat), 'AND', '>');
        
        }
        
        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;

    }

    public function getAllByChatRoomId($nation_code, $chat_room_id, $last_delete_chat, $page=1, $page_size=10, $sort_col='cdate', $sort_dir='DESC', $language_id=2)
    {
        $this->db->select_as("$this->tbl_as.id", "chat_id", 0);
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.type", "type", 0);
        // $this->db->select_as("$this->tbl_as.message", "message", 0);
        $this->db->select_as("IF($language_id = 2 AND $this->tbl_as.type = 'announcement' , $this->tbl_as.message_indonesia, $this->tbl_as.message)", "message");
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "a_pengguna_id", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "a_pengguna_nama", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.foto,'media/pengguna/default.png')", "a_pengguna_foto", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.id,'0')", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.image,'media/user/default-profile-picture.png')", "b_user_image", 0);
        $this->db->select_as("$this->tbl2_as.is_admin", "is_admin", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.e_chat_room_id", $this->db->esc($chat_room_id));
        
        if($last_delete_chat){

            $this->db->where_as("$this->tbl_as.cdate", $this->db->esc($last_delete_chat), 'AND', '>');
        
        }

        $this->db->order_by($sort_col, $sort_dir)->page($page, $page_size);

        return $this->db->get();
    }

    public function getLastChatByChatRoomId($nation_code, $chat_room_id, $last_delete_chat, $language_id=2)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);

        //by Donny Dennison - 12 july 2022 14:56
        //new offer system
        $this->db->select_as("$this->tbl_as.type", "type", 0);

        // $this->db->select_as("$this->tbl_as.message", "message", 0);
        $this->db->select_as("IF($language_id = 2 AND $this->tbl_as.type = 'announcement' , $this->tbl_as.message_indonesia, $this->tbl_as.message)", "message");
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "a_pengguna_id", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "a_pengguna_nama", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.foto,'media/pengguna/default.png')", "a_pengguna_foto", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.id,'0')", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.image,'media/user/default-profile-picture.png')", "b_user_image", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.e_chat_room_id", $this->db->esc($chat_room_id));

        //by Donny Dennison - 12 july 2022 14:56
        //new offer system
        // $this->db->where_as("$this->tbl_as.type", $this->db->esc('chat'));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc('announcement'), "AND","!=");

        if($last_delete_chat){

            $this->db->where_as("$this->tbl_as.cdate", $this->db->esc($last_delete_chat), 'AND', '>');
        
        }

        $this->db->order_by("$this->tbl_as.cdate", 'desc');

        return $this->db->get_first();
    }

    public function getChatByChatIdChatRoomId($nation_code, $chat_id, $chat_room_id, $language_id=2)
    {
        $this->db->select_as("$this->tbl_as.id", "chat_id", 0);
        $this->db->select_as("$this->tbl_as.e_chat_room_id", "chat_room_id", 0);

        //by Donny Dennison - 12 july 2022 14:56
        //new offer system
        $this->db->select_as("$this->tbl_as.type", "type", 0);

        // $this->db->select_as("$this->tbl_as.message", "message", 0);
        $this->db->select_as("IF($language_id = 2 AND $this->tbl_as.type = 'announcement' , $this->tbl_as.message_indonesia, $this->tbl_as.message)", "message");
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "a_pengguna_id", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "a_pengguna_nama", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.foto,'media/pengguna/default.png')", "a_pengguna_foto", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.id,'0')", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.image,'media/user/default-profile-picture.png')", "b_user_image", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.e_chat_room_id", $this->db->esc($chat_room_id));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($chat_id));

        return $this->db->get_first();
    }

    public function set($di)
    {
        if(!isset($di["message_indonesia"])){
            $di["message_indonesia"] = $di["message"];
        }

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

    public function getAllUnreadByChatRoomIdUserId($nation_code, $chat_room_id, $b_user_id, $language_id=2)
    {
        $this->db->select_as("$this->tbl_as.id", "chat_id", 0);
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.type", "type", 0);
        // $this->db->select_as("$this->tbl_as.message", "message", 0);
        $this->db->select_as("IF($language_id = 2 AND $this->tbl_as.type = 'announcement' , $this->tbl_as.message_indonesia, $this->tbl_as.message)", "message");
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "a_pengguna_id", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "a_pengguna_nama", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.foto,'media/pengguna/default.png')", "a_pengguna_foto", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.id,'0')", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.image,'media/user/default-profile-picture.png')", "b_user_image", 0);
        $this->db->select_as("$this->tbl2_as.is_admin", "is_admin", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl3_as.e_chat_room_id", $this->db->esc($chat_room_id));
        $this->db->where_as("$this->tbl3_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl3_as.is_read", $this->db->esc("0"));

        $this->db->order_by("$this->tbl_as.cdate", "DESC");

        return $this->db->get();
    }

    //START by Donny Dennison - 12 july 2022 14:56
    //new offer system
    public function getLastOfferByChatRoomId($nation_code, $chat_room_id)
    {
        // $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.message", "message", 0);
        // $this->db->select_as("IF($language_id = 2 AND $this->tbl_as.type = 'announcement' , $this->tbl_as.message_indonesia, $this->tbl_as.message)", "message");
        // $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "a_pengguna_id", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "a_pengguna_nama", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.foto,'media/pengguna/default.png')", "a_pengguna_foto", 0);
        // $this->db->select_as("COALESCE($this->tbl2_as.id,'0')", "b_user_id", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama", 0);
        // $this->db->select_as("COALESCE($this->tbl2_as.image,'media/user/default-profile-picture.png')", "b_user_image", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        // $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        // $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.e_chat_room_id", $this->db->esc($chat_room_id));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc('offering'));

        $this->db->order_by("$this->tbl_as.cdate", 'desc');

        return $this->db->get_first();
    }

    public function getLastAcceptedByChatRoomId($nation_code, $chat_room_id)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.message", "message", 0);
        // $this->db->select_as("IF($language_id = 2 AND $this->tbl_as.type = 'announcement' , $this->tbl_as.message_indonesia, $this->tbl_as.message)", "message");
        // $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.id,'0')", "a_pengguna_id", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "a_pengguna_nama", 0);
        // $this->db->select_as("COALESCE($this->tbl4_as.foto,'media/pengguna/default.png')", "a_pengguna_foto", 0);
        // $this->db->select_as("COALESCE($this->tbl2_as.id,'0')", "b_user_id", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama", 0);
        // $this->db->select_as("COALESCE($this->tbl2_as.image,'media/user/default-profile-picture.png')", "b_user_image", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        // $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        // $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.e_chat_room_id", $this->db->esc($chat_room_id));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc('accepted'));

        $this->db->order_by("$this->tbl_as.cdate", 'desc');

        return $this->db->get_first();
    }
    //END by Donny Dennison - 12 july 2022 14:56
    //new offer system

}
