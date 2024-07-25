<?php
class I_Chat_Model extends JI_Model
{
    public $tbl = 'i_chat';
    public $tbl_as = 'ic';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';
    public $tbl3 = 'i_chat_read';
    public $tbl3_as = 'icread';
    public $tbl4 = 'i_chat_participant';
    public $tbl4_as = 'icp';
    public $tbl5 = 'i_chat_room';
    public $tbl5_as = 'icr';
    public $tbl6 = 'i_group';
    public $tbl6_as = 'ig';

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

    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.i_chat_room_id", "=", "$this->tbl3_as.i_chat_room_id");
        $cps[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl3_as.i_chat_id");
        return $cps;
    }

    private function __joinTbl4()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.i_chat_room_id", "=", "$this->tbl4_as.i_chat_room_id");
        return $cps;
    }

    private function __joinTbl5()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl5_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.i_chat_room_id", "=", "$this->tbl5_as.id");
        return $cps;
    }

    private function __joinTbl6()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl5_as.nation_code", "=", "$this->tbl6_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl5_as.i_group_id", "=", "$this->tbl6_as.id");
        return $cps;
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

    public function checkId($nation_code, $id)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    // public function countAllByChatRoomId($nation_code, $chat_room_id, $last_delete_chat)
    // {
    //     $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.e_chat_room_id", $this->db->esc($chat_room_id));
        
    //     if($last_delete_chat){
    //         $this->db->where_as("$this->tbl_as.cdate", $this->db->esc($last_delete_chat), 'AND', '>');
    //     }

    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }

    public function getAllByChatRoomId($nation_code, $chat_room_id, $last_delete_chat, $page=1, $page_size=10, $sort_col='cdate', $sort_dir='DESC', $language_id=2)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.type", "type", 0);
        // $this->db->select_as("$this->tbl_as.message", "message", 0);
        $this->db->select_as("IF($language_id = 2 AND $this->tbl_as.type = 'announcement' , $this->tbl_as.message_indonesia, $this->tbl_as.message)", "message");
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.id,'0')", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.band_fnama"), "b_user_band_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.band_image,'media/user/default-profile-picture.png')", "b_user_band_image", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.i_chat_room_id", $this->db->esc($chat_room_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));

        if($last_delete_chat){
            $this->db->where_as("$this->tbl_as.cdate", $this->db->esc($last_delete_chat), 'AND', '>');
        }

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

    //     // $this->db->where_as("$this->tbl_as.type", $this->db->esc('chat'));
    //     $this->db->where_as("$this->tbl_as.type", $this->db->esc('announcement'), "AND","!=");

    //     if($last_delete_chat){
    //         $this->db->where_as("$this->tbl_as.cdate", $this->db->esc($last_delete_chat), 'AND', '>');
    //     }

    //     $this->db->order_by("$this->tbl_as.cdate", 'desc');

    //     return $this->db->get_first();
    // }

    // public function getChatByChatIdChatRoomId($nation_code, $chat_id, $chat_room_id, $language_id=2)
    // {
    //     $this->db->select_as("$this->tbl_as.id", "chat_id", 0);
    //     $this->db->select_as("$this->tbl_as.e_chat_room_id", "chat_room_id", 0);
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
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($chat_id));

    //     return $this->db->get_first();
    // }

    public function getAllUnreadByChatRoomIdUserId($nation_code, $chat_room_id, $b_user_id, $language_id=2)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.type", "type", 0);
        // $this->db->select_as("$this->tbl_as.message", "message", 0);
        $this->db->select_as("IF($language_id = 2 AND $this->tbl_as.type = 'announcement' , $this->tbl_as.message_indonesia, $this->tbl_as.message)", "message");
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.id,'0')", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.band_fnama"), "b_user_band_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.band_image,'media/user/default-profile-picture.png')", "b_user_band_image", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.i_chat_room_id", $this->db->esc($chat_room_id));
        $this->db->where_as("$this->tbl3_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_read", $this->db->esc("0"));

        $this->db->order_by("$this->tbl_as.cdate", "DESC");

        return $this->db->get();
    }

    public function searchChatFromRoomChatList($nation_code, $b_user_id, $i_group_id="", $i_chat_room_id="", $page=1, $page_size=10, $keyword="")
    {
        $this->db->select_as("DISTINCT $this->tbl5_as.id", "id", 0);
        $this->db->select_as("$this->tbl5_as.i_group_id", "i_group_id", 0);
        $this->db->select_as("$this->tbl5_as.b_user_id_creator", "b_user_id_creator", 0);
        $this->db->select_as("$this->tbl5_as.is_main_group_chat_room", "is_main_group_chat_room", 0);
        $this->db->select_as("$this->tbl5_as.total_people_chat_room", "total_people_chat_room", 0);
        $this->db->select_as("$this->tbl5_as.b_user_ids", "b_user_ids", 0);
        $this->db->select_as("$this->tbl5_as.type", "type", 0);
        $this->db->select_as("$this->tbl5_as.custom_name_1", "custom_name_1", 0);
        $this->db->select_as("$this->tbl5_as.image", "image", 0);
        $this->db->select_as("$this->tbl5_as.description", "description", 0);
        $this->db->select_as("$this->tbl5_as.is_edited", "is_edited", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl5_as.last_chat_b_user_fnama").',"")', "last_chat_b_user_fnama", 0);
        $this->db->select_as("$this->tbl5_as.last_chat_message", "last_chat_message", 0);
        $this->db->select_as("$this->tbl5_as.last_chat_cdate", "last_chat_cdate", 0);
        $this->db->select_as("$this->tbl5_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl4_as.is_read", "is_read", 0);
        $this->db->select_as("$this->tbl4_as.last_delete_chat", "last_delete_chat", 0);
        $this->db->select_as("$this->tbl6_as.image", "band_group_image", 0);
        $this->db->select_as("$this->tbl6_as.name", "band_group_name", 0);
        $this->db->select_as("IF(($this->tbl5_as.last_chat_cdate IS NOT NULL), IF(($this->tbl5_as.last_chat_cdate >= $this->tbl4_as.last_delete_chat), $this->tbl5_as.last_chat_cdate, $this->tbl4_as.last_delete_chat), $this->tbl5_as.cdate)", "cdate_for_order_by", 0);

        $this->db->from($this->tbl4, $this->tbl4_as);
        $this->db->join_composite($this->tbl, $this->tbl_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "left");

        $this->db->where_as("$this->tbl4_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc("chat"));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl4_as.b_user_id", $this->db->esc($b_user_id));
        // $this->db->where_as("IF(($this->tbl5_as.is_main_group_chat_room = '1'), '1', IF((SELECT COALESCE(cdate, '') FROM i_chat WHERE i_chat_room_id = $this->tbl5_as.id AND type = 'chat' AND is_active = '1' ORDER BY cdate DESC LIMIT 1) >= $this->tbl4_as.last_delete_chat, '1', '0'))",$this->db->esc(1));
        $this->db->where_as("$this->tbl4_as.is_first_time_join",$this->db->esc(0));

        if($i_group_id != ""){
        $this->db->where_as("$this->tbl5_as.i_group_id", $this->db->esc($i_group_id));
        }

        if($i_chat_room_id != ""){
        $this->db->where_as("$this->tbl4_as.i_chat_room_id", $this->db->esc($i_chat_room_id));
        }

        if(mb_strlen($keyword)>0){
            $this->db->where_as("LOWER($this->tbl_as.message)", addslashes(strtolower($keyword)), 'OR', '%like%', 1, 0);
            $this->db->where_as("LOWER($this->tbl5_as.custom_name_1)", addslashes(strtolower($keyword)), 'AND', '%like%', 0, 1);
        }

        // if($last_delete_chat){
        //     $this->db->where_as("$this->tbl_as.cdate", $this->db->esc($last_delete_chat), 'AND', '>');
        // }

        $this->db->order_by("IF(($this->tbl5_as.last_chat_cdate IS NOT NULL), IF(($this->tbl5_as.last_chat_cdate >= $this->tbl4_as.last_delete_chat), $this->tbl5_as.last_chat_cdate, $this->tbl4_as.last_delete_chat), $this->tbl5_as.cdate)", "DESC");

        if($page != 0 && $page_size != 0){
          $this->db->page($page, $page_size);
        }

        return $this->db->get();
    }
}
