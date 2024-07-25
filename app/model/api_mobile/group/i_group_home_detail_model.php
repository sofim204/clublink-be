<?php
class I_Group_Home_Detail_Model extends JI_Model
{
    public $tbl = 'i_group_home_detail';
    public $tbl_as = 'ighd';
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

    // public function getTblAs()
    // {
    //     return $this->tbl_as;
    // }

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

    // public function set($di)
    // {
    //     if (!is_array($di)) {
    //         return 0;
    //     }
    //     return $this->db->insert($this->tbl, $di, 0, 0);
    // }

    // public function update($nation_code, $id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where('nation_code', $nation_code);
    //     $this->db->where('id', $id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    // public function updateTotal($nation_code, $id, $parameter, $operator, $total)
    // {
    //     return $this->db->exec("UPDATE `$this->tbl` SET $parameter = IF('$operator' = '+', $parameter $operator $total, IF($parameter <= 0,0,$parameter $operator $total))
    //         WHERE nation_code = '$nation_code' AND id = '$id';");
    // }

    public function getAll($nation_code, $page=1, $page_size=10, $sort_col="prioritas", $sort_direction="ASC", $i_group_home_list_id, $language_id)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        // $this->db->select_as("$this->tbl_as.i_group_home_list_id", "i_group_home_list_id", 0);
        $this->db->select_as("$this->tbl_as.type", "type", 0);
        $this->db->select_as("$this->tbl_as.i_group_ids", "i_group_ids", 0);
        if($language_id == 2){
            $this->db->select_as("$this->tbl_as.indonesia", "title", 0);
        }else{
            $this->db->select_as("$this->tbl_as.english", "title", 0);
        }
        $this->db->select_as("''", "detail", 0);
        $this->db->select_as("''", "link_url", 0);
        // $this->db->select_as("''", "link_url_parameter", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        // $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.i_group_home_list_id", $this->db->esc($i_group_home_list_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));

        $this->db->order_by("$this->tbl_as.prioritas", "ASC");
        // $this->db->order_by("LOWER(".$sort_col.")", $sort_direction);
        // $this->db->page($page, $page_size);

        return $this->db->get('object', 0);
    }

    // public function getAllFromParticipant($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_direction="ASC", $keyword="", $group_type="", $b_user_id="")
    // {
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
    //     $this->db->select_as("$this->tbl_as.total_people", "total_people", 0);
    //     $this->db->select_as("$this->tbl_as.group_type", "group_type", 0);
    //     $this->db->select_as("$this->tbl_as.name", "name", 0);
    //     $this->db->select_as("$this->tbl_as.image", "image", 0);
    //     $this->db->select_as("$this->tbl_as.image_thumb", "image_thumb", 0);
    //     $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    //     $this->db->select_as("$this->tbl_as.description", "description", 0);
    //     $this->db->select_as("IF((SELECT deskripsi FROM i_group_post WHERE nation_code = '$nation_code' AND i_group_id = $this->tbl_as.id AND is_active = 1 ORDER BY cdate DESC LIMIT 1) IS NOT NULL, (SELECT deskripsi FROM i_group_post WHERE nation_code = '$nation_code' AND i_group_id = $this->tbl_as.id AND is_active = 1 ORDER BY cdate DESC LIMIT 1), $this->tbl_as.description)", "description_custom", 0);
    //     $this->db->select_as("$this->tbl_as.size_limit", "size_limit", 0);
    //     $this->db->select_as("$this->tbl_as.need_admin_approval", "need_admin_approval", 0);
    //     $this->db->select_as("$this->tbl_as.show_welcome_post", "show_welcome_post", 0);
    //     $this->db->select_as("IF((SELECT i_group_id FROM i_group_pin where nation_code = '$nation_code' AND i_group_id = $this->tbl_as.id AND b_user_id = '$b_user_id') IS NOT NULL, '1','0')", "is_pin", 0);

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

    //     $this->db->order_by("IF((SELECT i_group_id FROM i_group_pin WHERE nation_code = '$nation_code' AND i_group_id = $this->tbl_as.id AND b_user_id = '$b_user_id') IS NOT NULL, '9999-12-31 11:59:59', IF((SELECT cdate FROM i_group_post WHERE nation_code = '$nation_code' AND i_group_id = $this->tbl_as.id AND is_active = 1 ORDER BY cdate DESC LIMIT 1) IS NOT NULL, (SELECT cdate FROM i_group_post WHERE nation_code = '$nation_code' AND i_group_id = $this->tbl_as.id AND is_active = 1 ORDER BY cdate DESC LIMIT 1), $this->tbl_as.cdate))", "desc");
    //     // $this->db->order_by("LOWER(".$sort_col.")", $sort_direction);
    //     $this->db->page($page, $page_size);

    //     return $this->db->get('object', 0);
    // }

    // public function getById($nation_code, $id)
    // {
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
    //     $this->db->select_as("$this->tbl_as.i_group_category_id", "i_group_category_id", 0);
    //     $this->db->select_as("$this->tbl_as.i_chat_room_id", "i_chat_room_id", 0);
    //     $this->db->select_as("$this->tbl_as.total_people", "total_people", 0);
    //     $this->db->select_as("$this->tbl_as.group_type", "group_type", 0);
    //     $this->db->select_as("$this->tbl_as.name", "name", 0);
    //     $this->db->select_as("$this->tbl_as.description", "description", 0);
    //     $this->db->select_as("$this->tbl_as.image", "image", 0);
    //     $this->db->select_as("$this->tbl_as.image_thumb", "image_thumb", 0);
    //     $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    //     $this->db->select_as("$this->tbl_as.qrcode_url", "qrcode_url", 0);
    //     $this->db->select_as("$this->tbl_as.invite_code_digit", "invite_code_digit", 0);
    //     $this->db->select_as("$this->tbl_as.invite_code_word", "invite_code_word", 0);
    //     $this->db->select_as("$this->tbl_as.size_limit", "size_limit", 0);
    //     $this->db->select_as("$this->tbl_as.need_admin_approval", "need_admin_approval", 0);
    //     $this->db->select_as("$this->tbl_as.show_welcome_post", "show_welcome_post", 0);
    //     $this->db->select_as("''", "status_member", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    //     return $this->db->get_first('', 0);
    // }

    public function getByPrioritas($nation_code, $i_group_home_list_id, $prioritas, $language_id)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        // $this->db->select_as("$this->tbl_as.i_group_home_list_id", "i_group_home_list_id", 0);
        $this->db->select_as("$this->tbl_as.type", "type", 0);
        $this->db->select_as("$this->tbl_as.i_group_ids", "i_group_ids", 0);
        if($language_id == 2){
            $this->db->select_as("$this->tbl_as.indonesia", "title", 0);
        }else{
            $this->db->select_as("$this->tbl_as.english", "title", 0);
        }
        $this->db->select_as("''", "detail", 0);
        $this->db->select_as("''", "link_url", 0);
        // $this->db->select_as("''", "link_url_parameter", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        // $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.i_group_home_list_id", $this->db->esc($i_group_home_list_id));
        $this->db->where_as("$this->tbl_as.prioritas", $this->db->esc($prioritas));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        return $this->db->get_first('', 0);
    }
}
