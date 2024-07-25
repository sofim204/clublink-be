<?php
class C_Community_Discussion_Model extends JI_Model
{
    public $tbl = 'c_community_discussion';
    public $tbl_as = 'ccd';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';
    public $tbl3 = 'b_user';
    public $tbl3_as = 'bu2';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    /**
     * Composite join for multiple PK on table 2
     * @return array composites join
     */
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
        // by Muhammad Sofi - 10 November 2021 09:19
        if (isset($di['alamat2'])) {
            if (strlen($di['alamat2'])) {
                $di['alamat2'] = $this->__encrypt($di['alamat2']);
            }
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    /**
     * Insert into database, wiht ignore option (slower)
     * @param array $di name value pair describes column and value to insert into table
     */
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
        $this->db->where('parent_c_community_discussion_id', $parent_c_community_discussion_id);
        $this->db->where("is_active", 1);
        return $this->db->update($this->tbl, $du, 0);
    }

    /**
     * Update multiple rows on c_produk
     * @param  integer $nation_code [description]
     * @param  integer $b_user_id   ID from b_user
     * @param  string $ids          ID(s) from c_produk separated by commas
     * @param  array $du          name value pairs for edited column in a row
     * @return bool              1 success, 0 failed
     */
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
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.parent_c_community_discussion_id", $this->db->esc($parent_discussion_id));
        $this->db->where_as("$this->tbl_as.c_community_id", $this->db->esc($community_id));
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
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl2_as.fnama").',"")', "b_user_nama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.image,'')", "b_user_image", 0);
        $this->db->select_as("$this->tbl2_as.is_admin", "is_admin", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_nama_to", 0);

        //by Donny Dennison - 1 july 2021 14:42
        //add-general-location-in-address
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        // $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat4", 0);

        $this->db->select_as("$this->tbl_as.top_like_image_1", "top_like_image_1", 0);
        $this->db->select_as("''", "top_like_image_2", 0);
        $this->db->select_as("''", "top_like_image_3", 0);

        //by Donny Dennison - 8 july 2021 11:02
        //add-like-product
        //START by Donny Dennison - 8 july 2021 11:02
        $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);

        if(isset($pelanggan->id)){
            $this->db->select_as("IF($this->tbl_as.b_user_id = '$pelanggan->id', $this->tbl_as.total_dislikes,0)", "total_dislikes", 0);
            // $this->db->select_as("IF( (SELECT COUNT(*) FROM c_community_like WHERE type = 'community_discussion' AND like_type = 'like' AND custom_id = $this->tbl_as.id AND b_user_id = $pelanggan->id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_liked", 0);
            // $this->db->select_as("IF( (SELECT COUNT(*) FROM c_community_like WHERE type = 'community_discussion' AND like_type = 'dislike' AND custom_id = $this->tbl_as.id AND b_user_id = $pelanggan->id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_disliked", 0);
        }else{
            $this->db->select_as("(0)", "total_dislikes", 0);
            // $this->db->select_as("(0)", "is_liked", 0);
            // $this->db->select_as("(0)", "is_disliked", 0);
        }

        //END by Donny Dennison - 8 july 2021 11:02
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.parent_c_community_discussion_id", $this->db->esc($parent_discussion_id));
        $this->db->where_as("$this->tbl_as.c_community_id", $this->db->esc($community_id));
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

    public function getbyDiscussionIDCommunityID($nation_code, $discussion_id=0, $c_community_id = 0)
    {
        $this->db->select_as("$this->tbl_as.id", "discussion_id", 0);
        $this->db->select_as("$this->tbl_as.parent_c_community_discussion_id", "parent_c_community_discussion_id", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl2_as.fnama").',"")', "b_user_nama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.image,'')", "b_user_image", 0);
        $this->db->select_as("$this->tbl2_as.is_admin", "is_admin", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_nama_to", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.text", "text", 0);
        $this->db->select_as("$this->tbl_as.show_nama", "show_nama", 0);
        // $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat2", 0);
        // $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
        $this->db->select_as("CONCAT($this->tbl_as.kelurahan, ', ', $this->tbl_as.kabkota)", "alamat4", 0);

        $this->db->select_as("$this->tbl_as.top_like_image_1", "top_like_image_1", 0);
        $this->db->select_as("''", "top_like_image_2", 0);
        $this->db->select_as("''", "top_like_image_3", 0);

        //by Donny Dennison - 8 july 2021 11:02
        //add-like-product
        //START by Donny Dennison - 8 july 2021 11:02
        $this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);

        if(isset($pelanggan->id)){
            $this->db->select_as("IF($this->tbl_as.b_user_id = '$pelanggan->id', $this->tbl_as.total_dislikes,0)", "total_dislikes", 0);
            // $this->db->select_as("IF( (SELECT COUNT(*) FROM c_community_like WHERE type = 'community_discussion' AND like_type = 'like' AND custom_id = $this->tbl_as.id AND b_user_id = $pelanggan->id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_liked", 0);
            // $this->db->select_as("IF( (SELECT COUNT(*) FROM c_community_like WHERE type = 'community_discussion' AND like_type = 'dislike' AND custom_id = $this->tbl_as.id AND b_user_id = $pelanggan->id AND nation_code= $nation_code AND is_active= 1) > 0, 1, 0)", "is_disliked", 0);
        }else{
            $this->db->select_as("(0)", "total_dislikes", 0);
            // $this->db->select_as("(0)", "is_liked", 0);
            // $this->db->select_as("(0)", "is_disliked", 0);
        }

        //END by Donny Dennison - 8 july 2021 11:02
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($discussion_id));
        $this->db->where_as("$this->tbl_as.c_community_id", $this->db->esc($c_community_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanent user feature
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));

        return $this->db->get_first('object', 0);
    }

    public function getbyDiscussionIDUserID($nation_code, $discussion_id=0, $b_user_id = 0)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "discussion_id", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl2_as.fnama").',"")', "b_user_nama", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($discussion_id));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanent user feature
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));

        return $this->db->get_first('object', 0);
    }

    public function getbyDiscussionID($nation_code, $discussion_id=0)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "discussion_id", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl2_as.fnama").',"")', "b_user_nama", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($discussion_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanent user feature
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));

        return $this->db->get_first('object', 0);
    }

    public function countAllCommunityIDUserID($nation_code, $b_user_id = 0, $community_id = 0)
    {
        $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.c_community_id", $this->db->esc($community_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanent user feature
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

}
