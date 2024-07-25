<?php
class I_Group_Pin_Model extends JI_Model
{
    public $tbl = 'i_group_pin';
    public $tbl_as = 'igp';
    // public $tbl2 = 'i_group_participant';
    // public $tbl2_as = 'igp';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    // private function __joinTbl2()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl2_as.i_group_id");
    //     return $composites;
    // }

    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    // public function update($nation_code, $id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where('nation_code', $nation_code);
    //     $this->db->where('id', $id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    public function delete($nation_code, $i_group_id, $b_user_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("i_group_id", $i_group_id);
        $this->db->where("b_user_id", $b_user_id);
        return $this->db->delete($this->tbl);
    }

    // public function getTblAs()
    // {
    //     return $this->tbl_as;
    // }

    // public function getAll($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_direction="ASC", $keyword="", $group_type="", $b_user_id="")
    // {
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
    //     $this->db->select_as("$this->tbl_as.total_people", "total_people", 0);
    //     $this->db->select_as("$this->tbl_as.group_type", "group_type", 0);
    //     $this->db->select_as("$this->tbl_as.name", "name", 0);
    //     $this->db->select_as("$this->tbl_as.image", "image", 0);
    //     $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    //     $this->db->select_as("$this->tbl_as.description", "description", 0);
    //     $this->db->select_as("$this->tbl_as.size_limit", "size_limit", 0);
    //     $this->db->select_as("$this->tbl_as.need_admin_approval", "need_admin_approval", 0);
    //     $this->db->select_as("$this->tbl_as.show_welcome_post", "show_welcome_post", 0);
    //     $this->db->select_as("'0'", "is_pin", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));

    //     if ($b_user_id>'0') {
    //         $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     }
    //     if (mb_strlen($keyword)>0) {
    //         $this->db->where_as("LOWER($this->tbl_as.name)", addslashes(strtolower($keyword)), 'AND', '%like%');
    //     }
    //     if($group_type != ''){
    //         $this->db->where_as("$this->tbl_as.group_type", $this->db->esc($group_type));
    //     }else{
    //         $this->db->where_as("$this->tbl_as.group_type", $this->db->esc("private"), "AND", "!=");
    //     }

    //     $this->db->order_by("LOWER(".$sort_col.")", $sort_direction);
    //     $this->db->page($page, $page_size);

    //     return $this->db->get('object', 0);
    // }

    // public function getAllFromParticipant($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_direction="ASC", $keyword="", $group_type="", $b_user_id="")
    // {
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
    //     $this->db->select_as("$this->tbl_as.total_people", "total_people", 0);
    //     $this->db->select_as("$this->tbl_as.group_type", "group_type", 0);
    //     $this->db->select_as("$this->tbl_as.name", "name", 0);
    //     $this->db->select_as("$this->tbl_as.image", "image", 0);
    //     $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    //     $this->db->select_as("$this->tbl_as.description", "description", 0);
    //     $this->db->select_as("$this->tbl_as.size_limit", "size_limit", 0);
    //     $this->db->select_as("$this->tbl_as.need_admin_approval", "need_admin_approval", 0);
    //     $this->db->select_as("$this->tbl_as.show_welcome_post", "show_welcome_post", 0);
    //     $this->db->select_as("IF((SELECT i_group_id FROM i_group_pin where nation_code = '$nation_code' AND i_group_id = '$this->tbl_as.id' AND b_user_id = '$b_user_id') IS NOT NULL, '1','0')", "is_pin", 0);

    //     $this->db->from($this->tbl2, $this->tbl2_as);
    //     $this->db->join_composite($this->tbl, $this->tbl_as, $this->__joinTbl2(), 'left');

    //     $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl2_as.is_accept", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));

    //     if ($b_user_id>'0') {
    //         $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id));
    //     }
    //     if (mb_strlen($keyword)>0) {
    //         $this->db->where_as("LOWER($this->tbl_as.name)", addslashes(strtolower($keyword)), 'AND', '%like%');
    //     }
    //     if($group_type != ''){
    //         $this->db->where_as("$this->tbl_as.group_type", $this->db->esc($group_type));
    //     }

    //     $this->db->order_by("IF((SELECT i_group_id FROM i_group_pin where nation_code = '$nation_code' AND i_group_id = '$this->tbl_as.id' AND b_user_id = '$b_user_id') IS NOT NULL, '1','0')", "desc");
    //     $this->db->order_by("LOWER(".$sort_col.")", $sort_direction);
    //     $this->db->page($page, $page_size);

    //     return $this->db->get('object', 0);
    // }

    public function getById($nation_code, $i_group_id, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.i_group_id", "i_group_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.i_group_id", $this->db->esc($i_group_id));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        return $this->db->get_first('', 0);
    }

    // public function checkId($nation_code, $id)
    // {
    //     $this->db->select_as("COUNT(*)", "jumlah");
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
    //     $d = $this->db->get_first("object", 0);
    //     if (isset($d->jumlah)) {
    //         return $d->jumlah;
    //     }
    //     return 0;
    // }

    
    // public function checkIsOwner($nation_code, $i_group_id, $b_user_id)
    // {
    //     $this->db->select_as("COUNT(*)", "jumlah");
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($i_group_id));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     $d = $this->db->get_first("object", 0);
    //     if (isset($d->jumlah)) {
    //         return $d->jumlah;
    //     }
    //     return 0;
    // }

    // public function getByInvitationCode($nation_code, $digit, $word)
    // {
    //     $this->db->select_as("COUNT(*)", "jumlah");
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.invite_code_digit", $this->db->esc($digit));
    //     $this->db->where_as("$this->tbl_as.invite_code_word", $this->db->esc($word));
    //     $d = $this->db->get_first("object", 0);
    //     if (isset($d->jumlah)) {
    //         return $d->jumlah;
    //     }
    //     return 0;
    // }

    // public function updateTotal($nation_code, $id, $parameter, $operator, $total)
    // {
    //     return $this->db->exec("UPDATE `$this->tbl` SET $parameter = IF('$operator' = '+', $parameter $operator $total, IF($parameter <= 0,0,$parameter $operator $total))
    //         WHERE nation_code = '$nation_code' AND id = '$id';");
    // }

    // public function checkQrCode($nation_code, $i_group_id)
    // {
    //     $this->db->select_as("$this->tbl_as.qrcode_url", "jumlah");
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($i_group_id));
    //     $d = $this->db->get_first("object", 0);
    //     if (isset($d->jumlah)) {
    //         return $d->jumlah;
    //     }
    //     return 0;
    // }

    // public function checkInvitationCode($nation_code, $i_group_id)
    // {
    //     $this->db->select_as("$this->tbl_as.invite_code_digit", "invite_code_digit");
    //     $this->db->select_as("$this->tbl_as.invite_code_word", "invite_code_word");
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($i_group_id));
    //     return $this->db->get_first("object", 0);
    // }

    // public function getDataByInvitationCode($nation_code, $digit, $word)
    // {
    //     $this->db->select_as("$this->tbl_as.id", "group_id");
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id");
    //     $this->db->select_as("$this->tbl_as.group_type", "group_type");
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.invite_code_digit", $this->db->esc($digit));
    //     $this->db->where_as("$this->tbl_as.invite_code_word", $this->db->esc($word));
    //     return $this->db->get_first("", 0);
    // }

    // public function getGroupSettings($nation_code, $id, $b_user_id="")
    // {
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
    //     $this->db->select_as("$this->tbl_as.group_type", "group_type", 0);
    //     $this->db->select_as("$this->tbl_as.size_limit", "size_limit", 0);
    //     $this->db->select_as("$this->tbl_as.name", "name", 0);
    //     $this->db->select_as("$this->tbl_as.description", "description", 0);
    //     $this->db->select_as("$this->tbl_as.image", "image", 0);
    //     $this->db->select_as("$this->tbl_as.need_admin_approval", "need_admin_approval", 0);
    //     $this->db->select_as("$this->tbl_as.show_welcome_post", "show_welcome_post", 0);
    //     $this->db->select_as("''", "status", 0);
    //     $this->db->select_as("IF($this->tbl2_as.is_owner = '1' AND $this->tbl2_as.is_co_admin = '0' AND $this->tbl2_as.is_accept, 'Owner', 
    //         IF($this->tbl2_as.is_owner = '0' AND $this->tbl2_as.is_co_admin = '1' AND $this->tbl2_as.is_accept, 'Admin', 
    //         IF($this->tbl2_as.is_owner = '0' AND $this->tbl2_as.is_co_admin = '0' AND $this->tbl2_as.is_accept, 'Member',
    //         'not_member')
    //         )
    //     )", "status_member");
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl2_as.is_accept", $this->db->esc(1));
    //     if ($b_user_id>'0') {
    //         $this->db->where_as("$this->tbl2_as.b_user_id", $this->db->esc($b_user_id));
    //     }
    //     return $this->db->get_first('', 0);
    // }
}
