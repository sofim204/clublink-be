<?php
class I_Group_Model extends JI_Model
{
    public $tbl = 'i_group';
    public $tbl_as = 'ig';
    public $tbl2 = 'i_group_participant';
    public $tbl2_as = 'igp';
    public $tbl3 = 'i_group_home_detail';
    public $tbl3_as = 'ighd';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl2_as.i_group_id");
        return $composites;
    }

    private function __joinTbl3($i_group_home_list_id)
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl3_as.i_group_id");
        $composites[] = $this->db->composite_create($this->db->esc($i_group_home_list_id), "=", "$this->tbl3_as.i_group_home_list_id");
        return $composites;
    }

    public function getTblAs()
    {
        return $this->tbl_as;
    }

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

    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    public function update($nation_code, $id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where('nation_code', $nation_code);
        $this->db->where('id', $id);
        return $this->db->update($this->tbl, $du, 0);
    }

    public function getAll($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_direction="ASC", $keyword="", $group_type="", $b_user_id="", $query_type="", $i_group_sub_category_id="", $i_group_home_list_id="")
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.total_people", "total_people", 0);
        $this->db->select_as("$this->tbl_as.group_type", "group_type", 0);
        $this->db->select_as("$this->tbl_as.name", "name", 0);
        $this->db->select_as("$this->tbl_as.image", "image", 0);
        $this->db->select_as("$this->tbl_as.image_thumb", "image_thumb", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.description", "description", 0);
        $this->db->select_as("$this->tbl_as.size_limit", "size_limit", 0);
        $this->db->select_as("$this->tbl_as.need_admin_approval", "need_admin_approval", 0);
        $this->db->select_as("$this->tbl_as.show_welcome_post", "show_welcome_post", 0);
        $this->db->select_as("'0'", "is_pin", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        if($i_group_home_list_id != ''){
            $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3($i_group_home_list_id), 'left');
        }

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));

        if ($b_user_id>'0') {
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        }
        if (mb_strlen($keyword)>0) {
            $this->db->where_as("LOWER($this->tbl_as.name)", addslashes(strtolower($keyword)), 'AND', '%like%');
        }
        if($group_type != ''){
            $this->db->where_as("$this->tbl_as.group_type", $this->db->esc($group_type));
        }else{
            $this->db->where_as("$this->tbl_as.group_type", $this->db->esc("private"), "AND", "!=");
        }

        if($i_group_home_list_id != ''){
            $this->db->where_as("$this->tbl_as.i_group_sub_category_id", $this->db->esc($i_group_sub_category_id));
            $this->db->order_by("iF($this->tbl3_as.prioritas IS NOT NULL, $this->tbl3_as.prioritas, 100)", "ASC");
            $sort_col = "$this->tbl_as.total_people";
            $sort_direction = "DESC";
        }

        if($query_type == "popular"){
            $this->db->order_by("$this->tbl_as.total_people", "DESC");
        }else if($query_type == "latest"){
            $this->db->order_by("$this->tbl_as.cdate", "DESC");
        }else{
            $this->db->order_by("LOWER(".$sort_col.")", $sort_direction);
        }

        $this->db->page($page, $page_size);

        return $this->db->get('object', 0);
    }

    public function getAllFromParticipant($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_direction="ASC", $keyword="", $group_type="", $b_user_id="")
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.total_people", "total_people", 0);
        $this->db->select_as("$this->tbl_as.group_type", "group_type", 0);
        $this->db->select_as("$this->tbl_as.name", "name", 0);
        $this->db->select_as("$this->tbl_as.image", "image", 0);
        $this->db->select_as("$this->tbl_as.image_thumb", "image_thumb", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.description", "description", 0);
        $this->db->select_as("IF((SELECT deskripsi FROM i_group_post WHERE nation_code = '$nation_code' AND i_group_id = $this->tbl_as.id AND is_active = 1 ORDER BY cdate DESC LIMIT 1) IS NOT NULL, (SELECT deskripsi FROM i_group_post WHERE nation_code = '$nation_code' AND i_group_id = $this->tbl_as.id AND is_active = 1 ORDER BY cdate DESC LIMIT 1), $this->tbl_as.description)", "description_custom", 0);
        $this->db->select_as("$this->tbl_as.size_limit", "size_limit", 0);
        $this->db->select_as("$this->tbl_as.need_admin_approval", "need_admin_approval", 0);
        $this->db->select_as("$this->tbl_as.show_welcome_post", "show_welcome_post", 0);
        $this->db->select_as("IF((SELECT i_group_id FROM i_group_pin where nation_code = '$nation_code' AND i_group_id = $this->tbl_as.id AND b_user_id = '$b_user_id') IS NOT NULL, '1','0')", "is_pin", 0);

        $this->db->from($this->tbl2, $this->tbl2_as);
        $this->db->join_composite($this->tbl, $this->tbl_as, $this->__joinTbl2(), 'left');

        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl2_as.is_accept", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));

        if ($b_user_id>'0') {
            $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id));
        }
        if (mb_strlen($keyword)>0) {
            $this->db->where_as("LOWER($this->tbl_as.name)", addslashes(strtolower($keyword)), 'AND', '%like%');
        }
        if($group_type != ''){
            $this->db->where_as("$this->tbl_as.group_type", $this->db->esc($group_type));
        }

        $this->db->order_by("IF((SELECT i_group_id FROM i_group_pin WHERE nation_code = '$nation_code' AND i_group_id = $this->tbl_as.id AND b_user_id = '$b_user_id') IS NOT NULL, '9999-12-31 11:59:59', IF((SELECT cdate FROM i_group_post WHERE nation_code = '$nation_code' AND i_group_id = $this->tbl_as.id AND is_active = 1 ORDER BY cdate DESC LIMIT 1) IS NOT NULL, (SELECT cdate FROM i_group_post WHERE nation_code = '$nation_code' AND i_group_id = $this->tbl_as.id AND is_active = 1 ORDER BY cdate DESC LIMIT 1), $this->tbl_as.cdate))", "desc");
        // $this->db->order_by("LOWER(".$sort_col.")", $sort_direction);
        $this->db->page($page, $page_size);

        return $this->db->get('object', 0);
    }

    public function getAllFromSubCategory($nation_code, $page=1, $page_size=10, $sort_col="total_people", $sort_direction="DESC", $i_group_sub_category_id="")
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.total_people", "total_people", 0);
        $this->db->select_as("$this->tbl_as.group_type", "group_type", 0);
        $this->db->select_as("$this->tbl_as.name", "name", 0);
        $this->db->select_as("$this->tbl_as.image", "image", 0);
        $this->db->select_as("$this->tbl_as.image_thumb", "image_thumb", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.description", "description", 0);
        $this->db->select_as("$this->tbl_as.size_limit", "size_limit", 0);
        $this->db->select_as("$this->tbl_as.need_admin_approval", "need_admin_approval", 0);
        $this->db->select_as("$this->tbl_as.show_welcome_post", "show_welcome_post", 0);
        $this->db->select_as("'0'", "is_pin", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));
        $this->db->where_as("$this->tbl_as.group_type", $this->db->esc("private"), "AND", "!=");

        if($i_group_sub_category_id != ''){
            $this->db->where_as("$this->tbl_as.i_group_sub_category_id", $this->db->esc($i_group_sub_category_id));
        }

        $this->db->order_by("$this->tbl_as.total_people", "DESC");
        $this->db->page($page, $page_size);

        return $this->db->get('object', 0);
    }

    public function getById($nation_code, $id)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.i_group_category_id", "i_group_category_id", 0);
        $this->db->select_as("$this->tbl_as.i_group_sub_category_id", "i_group_sub_category_id", 0);
        $this->db->select_as("$this->tbl_as.i_chat_room_id", "i_chat_room_id", 0);
        $this->db->select_as("$this->tbl_as.total_people", "total_people", 0);
        $this->db->select_as("$this->tbl_as.group_type", "group_type", 0);
        $this->db->select_as("$this->tbl_as.name", "name", 0);
        $this->db->select_as("$this->tbl_as.description", "description", 0);
        $this->db->select_as("$this->tbl_as.image", "image", 0);
        $this->db->select_as("$this->tbl_as.image_thumb", "image_thumb", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.qrcode_url", "qrcode_url", 0);
        $this->db->select_as("$this->tbl_as.invite_code_digit", "invite_code_digit", 0);
        $this->db->select_as("$this->tbl_as.invite_code_word", "invite_code_word", 0);
        $this->db->select_as("$this->tbl_as.size_limit", "size_limit", 0);
        $this->db->select_as("$this->tbl_as.need_admin_approval", "need_admin_approval", 0);
        $this->db->select_as("$this->tbl_as.show_welcome_post", "show_welcome_post", 0);
        $this->db->select_as("''", "status_member", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        return $this->db->get_first('', 0);
    }

    public function getByIds($nation_code, $page=1, $page_size=10, $sort_col="total_people", $sort_direction="DESC", $ids)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.total_people", "total_people", 0);
        $this->db->select_as("$this->tbl_as.group_type", "group_type", 0);
        $this->db->select_as("$this->tbl_as.name", "name", 0);
        $this->db->select_as("$this->tbl_as.image", "image", 0);
        $this->db->select_as("$this->tbl_as.image_thumb", "image_thumb", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.description", "description", 0);
        $this->db->select_as("$this->tbl_as.size_limit", "size_limit", 0);
        $this->db->select_as("$this->tbl_as.need_admin_approval", "need_admin_approval", 0);
        $this->db->select_as("$this->tbl_as.show_welcome_post", "show_welcome_post", 0);
        $this->db->select_as("'0'", "is_pin", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));
        $this->db->where_in("id", $ids);

        // if($query_type == "popular"){
        //     $this->db->order_by("$this->tbl_as.total_people", "DESC");
        //     $this->db->page($page, 10);
        // }else if($query_type == "latest"){
        //     $this->db->order_by("$this->tbl_as.cdate", "DESC");
        //     $this->db->page($page, 10);
        // }else{
            $this->db->order_by("$this->tbl_as.total_people", "DESC");
            if($page_size != 0){
                $this->db->page($page, $page_size);
            }
        // }

        return $this->db->get('object', 0);
    }

    public function checkIsOwner($nation_code, $i_group_id, $b_user_id)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($i_group_id));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getByInvitationCode($nation_code, $digit, $word)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.invite_code_digit", $this->db->esc($digit));
        $this->db->where_as("$this->tbl_as.invite_code_word", $this->db->esc($word));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function updateTotal($nation_code, $id, $parameter, $operator, $total)
    {
        return $this->db->exec("UPDATE `$this->tbl` SET $parameter = IF('$operator' = '+', $parameter $operator $total, IF($parameter <= 0,0,$parameter $operator $total))
            WHERE nation_code = '$nation_code' AND id = '$id';");
    }

    public function checkQrCode($nation_code, $i_group_id)
    {
        $this->db->select_as("$this->tbl_as.qrcode_url", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($i_group_id));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function checkInvitationCode($nation_code, $i_group_id)
    {
        $this->db->select_as("$this->tbl_as.invite_code_digit", "invite_code_digit");
        $this->db->select_as("$this->tbl_as.invite_code_word", "invite_code_word");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($i_group_id));
        return $this->db->get_first("object", 0);
    }

    public function getDataByInvitationCode($nation_code, $digit, $word)
    {
        $this->db->select_as("$this->tbl_as.id", "group_id");
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id");
        $this->db->select_as("$this->tbl_as.group_type", "group_type");
        $this->db->select_as("$this->tbl_as.i_chat_room_id", "i_chat_room_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.invite_code_digit", $this->db->esc($digit));
        $this->db->where_as("$this->tbl_as.invite_code_word", $this->db->esc($word));
        return $this->db->get_first("", 0);
    }

    public function getGroupSettings($nation_code, $id, $b_user_id="")
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.i_group_category_id", "i_group_category_id", 0);
        $this->db->select_as("$this->tbl_as.i_group_sub_category_id", "i_group_sub_category_id", 0);
        $this->db->select_as("$this->tbl_as.i_chat_room_id", "i_chat_room_id", 0);
        $this->db->select_as("$this->tbl_as.group_type", "group_type", 0);
        $this->db->select_as("$this->tbl_as.size_limit", "size_limit", 0);
        $this->db->select_as("$this->tbl_as.name", "name", 0);
        $this->db->select_as("$this->tbl_as.description", "description", 0);
        $this->db->select_as("$this->tbl_as.image", "image", 0);
        $this->db->select_as("$this->tbl_as.image_thumb", "image_thumb", 0);
        $this->db->select_as("$this->tbl_as.need_admin_approval", "need_admin_approval", 0);
        $this->db->select_as("$this->tbl_as.show_welcome_post", "show_welcome_post", 0);
        $this->db->select_as("''", "status", 0);
        $this->db->select_as("IF($this->tbl2_as.is_owner = '1' AND $this->tbl2_as.is_co_admin = '0' AND $this->tbl2_as.is_accept, 'Owner', 
            IF($this->tbl2_as.is_owner = '0' AND $this->tbl2_as.is_co_admin = '1' AND $this->tbl2_as.is_accept, 'Admin', 
            IF($this->tbl2_as.is_owner = '0' AND $this->tbl2_as.is_co_admin = '0' AND $this->tbl2_as.is_accept, 'Member',
            'not_member')
            )
        )", "status_member");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl2_as.is_accept", $this->db->esc(1));
        if ($b_user_id>'0') {
            $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id));
        }
        return $this->db->get_first('', 0);
    }

    public function totalClubCreated($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc(date("Y-m-d")));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }
}
