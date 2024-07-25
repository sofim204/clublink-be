<?php
class I_Group_Bookmark_Post_Model extends JI_Model
{
    public $tbl = 'i_group_bookmark_post';
    public $tbl_as = 'igbp';
    public $tbl2 = 'i_group_post';
    public $tbl2_as = 'igp';
    public $tbl3 = 'b_user';
    public $tbl3_as = 'bu';
    public $tbl4 = 'i_group';
    public $tbl4_as = 'ig';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.i_group_post_id", "=", "$this->tbl2_as.id");
        return $composites;
    }

    private function __joinTbl3()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl2_as.b_user_id", "=", "$this->tbl3_as.id");
        return $composites;
    }

    private function __joinTbl4()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl2_as.i_group_id", "=", "$this->tbl4_as.id");
        return $composites;
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

    public function delete($nation_code, $i_group_post_id, $b_user_id = "")
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("i_group_post_id", $i_group_post_id);
        if($b_user_id > '0') {
            $this->db->where("b_user_id", $b_user_id);
        }
        return $this->db->delete($this->tbl);
    }


    public function getTblAs()
    {
        return $this->tbl_as;
    }

    public function getAll($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_direction="ASC", $keyword="", $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.i_group_post_id", "id", 0);
        $this->db->select_as("$this->tbl_as.id", "bookmark_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.is_active", "is_bookmark", 0);
        $this->db->select_as("$this->tbl2_as.i_group_id", "i_group_id", 0);
        $this->db->select_as("$this->tbl2_as.deskripsi", "deskripsi", 0);
        $this->db->select_as("$this->tbl2_as.total_discussion", "total_discussion", 0);
        $this->db->select_as("$this->tbl2_as.total_likes", "total_likes", 0);
        $this->db->select_as("$this->tbl2_as.top_like_image_1", "top_like_image_1", 0);
        $this->db->select_as("$this->tbl2_as.top_like_image_2", "top_like_image_2", 0);
        $this->db->select_as("$this->tbl2_as.cdate", "cdate", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.band_fnama").',"")', "b_user_band_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.band_image,'')", "b_user_band_image", 0);
        $this->db->select_as("$this->tbl4_as.name", "group_name", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl4_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));

        // if (mb_strlen($keyword)>0) {
        //     $this->db->where_as("LOWER($this->tbl_as.name)", addslashes(strtolower($keyword)), 'AND', '%like%');
        // }

        $this->db->order_by("LOWER(".$sort_col.")", $sort_direction);
        $this->db->page($page, $page_size);

        return $this->db->get('object', 0);
    }

    public function getByUserIdPostId($nation_code, $b_user_id, $post_id = "")
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.i_group_post_id", "i_group_post_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        if ($post_id > '0') {
            $this->db->where_as("$this->tbl_as.i_group_post_id", $this->db->esc($post_id));
        }
        return $this->db->get_first('', 0);
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
    
}
