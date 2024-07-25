<?php
class I_Group_Post_Comment_Model extends JI_Model
{
    public $tbl = 'i_group_post_comment';
    public $tbl_as = 'igpc';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';
    public $tbl3 = 'b_user';
    public $tbl3_as = 'bu2';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
        return $composites;
    }

    private function __joinTbl3()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id_to", "=", "$this->tbl3_as.id");
        return $composites;
    }

    public function getLastId($nation_code){
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code",$nation_code);
        $d = $this->db->get_first('',0);
        if(isset($d->last_id)) return $d->last_id;
        return 0;
    }

    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    // public function set2($di)
    // {
    //     if (!is_array($di)) {
    //         return 0;
    //     }
    //     return $this->db->insert_ignore($this->tbl, $di, 0, 0);
    // }

    public function update($nation_code, $discussion_id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where('nation_code', $nation_code);
        $this->db->where('id', $discussion_id);
        return $this->db->update($this->tbl, $du, 0);
    }

    public function updateByParentCommunityDiscussionId($nation_code, $parent_c_community_discussion_id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where('parent_i_group_post_comment_id', $parent_c_community_discussion_id);
        $this->db->where("is_active", 1);
        return $this->db->update($this->tbl, $du, 0);
    }

    // public function updateMass($nation_code, $b_user_id, $ids, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where_in("id", $ids);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    // public function updateByUserIdAlamatId($nation_code, $b_user_id, $b_user_alamat_id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where("b_user_alamat_id", $b_user_alamat_id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    // public function del($nation_code, $id, $b_user_id)
    // {
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('id', $id);
    //     $this->db->where('b_user_id', $b_user_id);
    //     return $this->db->delete($this->tbl);
    // }

    // public function deleteMass($nation_code, $b_user_id, $pids)
    // {
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('b_user_id', $b_user_id);
    //     $this->db->where_in("id", $pids);
    //     return $this->db->delete($this->tbl);
    // }

    public function getTblAs()
    {
        return $this->tbl_as;
    }
    public function getTbl2As()
    {
        return $this->tbl2_as;
    }

    public function countAll($nation_code, $parent_discussion_id = 0, $community_id = 0)
    {
        $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.parent_i_group_post_comment_id", $this->db->esc($parent_discussion_id));
        $this->db->where_as("$this->tbl_as.i_group_post_id", $this->db->esc($community_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanent user feature
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));

        //by Donny Dennison - 28 june 2020 11:06
        //request by Mr Jackie, still show prodcut even the stock is zero
        // only show stok qty above zero
        // $this->db->where_as("$this->tbl_as.stok", $this->db->esc(0), "AND", ">");

        //advanced filter
        // if (strlen($harga_jual_min)>0 && strlen($harga_jual_max)>0) {
        //     $this->db->between("($this->tbl_as.harga_jual)", '("'.$harga_jual_min.'")', '("'.$harga_jual_max.'")', 0);
        // } elseif (strlen($harga_jual_min)==0 && strlen($harga_jual_max)>0) {
        //     $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_max), "AND", "<=");
        // } elseif (strlen($harga_jual_min)>0 && strlen($harga_jual_max)==0) {
        //     $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_min), "AND", ">=");
        // }
        // if (is_array($b_kondisi_ids) && count($b_kondisi_ids)>0) {
        //     $this->db->where_in("$this->tbl_as.b_kondisi_id", $b_kondisi_ids);
        // }
        // if (is_array($b_kategori_ids) && count($b_kategori_ids)>0) {
        //     $this->db->where_in("$this->tbl_as.b_kategori_id", $b_kategori_ids);
        // }

        // if (intval($kategori_id)>0) {
        //     $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc($kategori_id));
        // }
        // if (intval($b_user_id)>0) {
        //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        // }

        // if (mb_strlen($keyword)>0) {
        //     $this->db->where_as("$this->tbl_as.nama", $keyword, 'or', '%like%', 1, 0);
        //     $this->db->where_as("$this->tbl2_as.nama", $keyword, 'or', '%like%');
        //     $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', $keyword, 'or', '%like%');
        //     $this->db->where_as("$this->tbl_as.deskripsi", $keyword, 'or', '%like%');
        //     $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
        // }
        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getAll($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_direction="ASC", $parent_discussion_id=0, $community_id = 0, $pelanggan = array())
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "discussion_id", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl2_as.band_fnama").',"")', "b_user_band_nama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.band_image,'')", "b_user_band_image", 0);
        $this->db->select_as("$this->tbl2_as.is_admin", "is_admin", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.band_fnama").',"")', "b_user_band_nama_to", 0);
        $this->db->select_as("$this->tbl_as.top_like_image_1", "top_like_image_1", 0);
        $this->db->select_as("$this->tbl_as.top_like_image_2", "top_like_image_2", 0);
        $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.parent_i_group_post_comment_id", $this->db->esc($parent_discussion_id));
        $this->db->where_as("$this->tbl_as.i_group_post_id", $this->db->esc($community_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanent user feature
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));

        //advanced filter
        // if (strlen($harga_jual_min)>0 && strlen($harga_jual_max)>0) {
        //     $this->db->between("($this->tbl_as.harga_jual)", '("'.$harga_jual_min.'")', '("'.$harga_jual_max.'")', 0);
        // } elseif (strlen($harga_jual_min)==0 && strlen($harga_jual_max)>0) {
        //     $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_max), "AND", "<=");
        // } elseif (strlen($harga_jual_min)>0 && strlen($harga_jual_max)==0) {
        //     $this->db->where_as("$this->tbl_as.harga_jual", $this->db->esc($harga_jual_min), "AND", ">=");
        // }
        // if (is_array($b_kondisi_ids) && count($b_kondisi_ids)>0) {
        //     $this->db->where_in("$this->tbl_as.b_kondisi_id", $b_kondisi_ids);
        // }
        // if (is_array($b_kategori_ids) && count($b_kategori_ids)>0) {
        //     $this->db->where_in("$this->tbl_as.b_kategori_id", $b_kategori_ids);
        // }

        // if (intval($kategori_id)>0) {
        //     $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc($kategori_id));
        // }
        // if (intval($b_user_id)>0) {
        //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        // }

        // if (strlen($kategori_id)) {
        //     $this->db->where_as("$this->tbl_as.b_kategori_id", $this->db->esc($kategori_id));
        // }
        // if ($b_user_id>0) {
        //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        // }
        // if (mb_strlen($keyword)>0) {
        //     $this->db->where_as("$this->tbl_as.nama", $keyword, 'or', '%like%', 1, 0);
        //     $this->db->where_as("$this->tbl2_as.nama", $keyword, 'or', '%like%');
        //     $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', $keyword, 'or', '%like%');
        //     $this->db->where_as("$this->tbl_as.deskripsi", $keyword, 'or', '%like%');
        //     $this->db->where_as("$this->tbl_as.brand", $keyword, 'or', '%like%', 0, 1);
        // }

        $this->db->order_by($sort_col, $sort_direction);

        if($page != 0 && $page_size != 0){
            $this->db->page($page, $page_size);
        }

        return $this->db->get('object', 0);
    }

    public function getbyDiscussionIDPostID($nation_code, $discussion_id=0, $i_group_post_id = 0)
    {
        $this->db->select_as("$this->tbl_as.id", "discussion_id", 0);
        $this->db->select_as("$this->tbl_as.parent_i_group_post_comment_id", "parent_i_group_post_comment_id", 0);
        $this->db->select_as("$this->tbl_as.i_group_id", "i_group_id", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl2_as.band_fnama").',"")', "b_user_band_nama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.band_image,'')", "b_user_band_image", 0);
        $this->db->select_as("$this->tbl2_as.is_admin", "is_admin", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.band_fnama").',"")', "b_user_band_nama_to", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.text", "text", 0);
        $this->db->select_as("$this->tbl_as.show_nama", "show_nama", 0);
        $this->db->select_as("$this->tbl_as.top_like_image_1", "top_like_image_1", 0);
        $this->db->select_as("$this->tbl_as.top_like_image_2", "top_like_image_2", 0);
        $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($discussion_id));
        $this->db->where_as("$this->tbl_as.i_group_post_id", $this->db->esc($i_group_post_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));

        return $this->db->get_first('object', 0);
    }

    public function getbyDiscussionIDUserID($nation_code, $discussion_id=0, $b_user_id = 0)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "discussion_id", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl2_as.band_fnama").',"")', "b_user_band_nama", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($discussion_id));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        // $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));

        return $this->db->get_first('object', 0);
    }

    public function getbyDiscussionID($nation_code, $discussion_id=0)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "discussion_id", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl2_as.band_fnama").',"")', "b_user_band_nama", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($discussion_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));

        return $this->db->get_first('object', 0);
    }

    public function countAllPostIDUserID($nation_code, $b_user_id = 0, $post_id = 0)
    {
        $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.i_group_post_id", $this->db->esc($post_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function updateTotal($nation_code, $id, $parameter, $operator, $total)
    {
        return $this->db->exec("UPDATE `$this->tbl` SET $parameter = IF('$operator' = '+', $parameter $operator $total, IF($parameter <= 0,0,$parameter $operator $total))
            WHERE nation_code = '$nation_code' AND id = '$id';");
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

    public function getbyGroupIDUserID($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_dir="desc", $b_user_id ="", $group_id="", $getType="all")
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "discussion_id", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl2_as.band_fnama").',"")', "b_user_band_nama", 0);
        $this->db->select_as("$this->tbl2_as.band_image", "b_user_band_image", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

        if ($group_id>'0') {
            $this->db->where_as("$this->tbl_as.i_group_id", $this->db->esc($group_id));
        }

        if ($b_user_id>'0') {
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        }

        $this->db->order_by("LOWER(".$sort_col.")", $sort_dir);

        if($page != 0 && $page_size != 0){
            $this->db->page($page, $page_size);
        }

        if($getType == "first"){
			return $this->db->get_first();
		} else {
			return $this->db->get('', 0);
		}
    }
}
