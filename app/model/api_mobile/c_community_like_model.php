<?php
class C_Community_Like_Model extends JI_Model
{
    public $tbl = 'c_community_like';
    public $tbl_as = 'el';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';
    public $tbl4 = 'c_community_like_category';
    public $tbl4_as = 'cclc';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function getTblAs()
    {
        return $this->tbl_as;
    }
    public function getTbl2As()
    {
        return $this->tbl2_as;
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

    private function __joinTbl4()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.c_community_like_category_id", "=", "$this->tbl4_as.id");
        return $composites;
    }

    // public function getLastId($nation_code, $type){
    //     // $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    //     // $this->db->from($this->tbl, $this->tbl_as);
    //     // $this->db->where("nation_code",$nation_code);
    //     // $this->db->where("type",$type);
    //     // $d = $this->db->get_first('',0);
    //     // if(isset($d->last_id)) return $d->last_id;
    //     // return 0;
    //     $sql ="SELECT COALESCE(MAX(id),0)+1 AS id FROM `".$this->tbl."` WHERE id >= (SELECT COALESCE(MAX(id),0) FROM `".$this->tbl."` WHERE nation_code = '".$nation_code."' AND type = '".$type."') AND nation_code = '".$nation_code."' AND type = '".$type."' FOR UPDATE;";
    //     return $this->db->query($sql)[0]->id;
    // }

    public function set($di)
    {
        // by Muhammad Sofi - 10 November 2021 09:19
        if (isset($di['alamat2'])) {
            if (strlen($di['alamat2'])) {
                $di['alamat2'] = $this->__encrypt($di['alamat2']);
            }
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    public function update($nation_code, $id, $type, $like_type, $du)
    {
        $this->db->where('nation_code', $nation_code);
        $this->db->where('id', $id);
        $this->db->where('type', $type);
        $this->db->where('like_type', $like_type);
        $this->db->where('is_active', 1);
        return $this->db->update($this->tbl, $du, 0);
    }

    public function del($nation_code, $id, $type, $like_type)
    {
        $this->db->where('nation_code', $nation_code);
        $this->db->where('id', $id);
        $this->db->where('type', $type);
        $this->db->where('like_type', $like_type);
        return $this->db->delete($this->tbl);
    }

    public function countAll($nation_code, $keyword='', $type, $custom_id, $like_type)
    {
        $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        // $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
        $this->db->where_as("$this->tbl_as.like_type", $this->db->esc($like_type));
        $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanent user feature
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));
        
        //advanced filter
        // if (mb_strlen($keyword)>0) {
        //     $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl2_as.fnama").',"")', $keyword, 'AND', '%like%');
        // }
        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword='', $type, $custom_id, $like_type)
    {
        $this->db->select_as("$this->tbl_as.id", "like_id", 0);
        $this->db->select_as("$this->tbl_as.type", "type", 0);
        $this->db->select_as("$this->tbl_as.custom_id", "custom_id", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl2_as.id", "b_user_id", 0);

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanent user feature
        $this->db->select_as("$this->tbl2_as.is_active", "b_user_is_active", 0);

        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl2_as.fnama").',"")', "b_user_nama", 0);
        $this->db->select_as("$this->tbl2_as.image", "foto", 0);
        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "b_user_alamat", 0);
        $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0); 
        $this->db->select_as("$this->tbl4_as.image_icon", "image_icon", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
        $this->db->where_as("$this->tbl_as.like_type", $this->db->esc($like_type));
        $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanent user feature
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));

        //advanced filter
        // if (mb_strlen($keyword)>0) {
        //     $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl2_as.fnama").',"")', $keyword, 'AND', '%like%');
        // }
        $this->db->order_by($sort_col, $sort_dir)->page($page, $page_size);
        return $this->db->get('object', 0);
    }

    public function getByCustomIdUserId($nation_code, $type, $like_type, $custom_id, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.type", "type", 0);
        $this->db->select_as("$this->tbl_as.like_type", "like_type", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
        $this->db->where_as("$this->tbl_as.like_type", $this->db->esc($like_type));
        $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        return $this->db->get_first('object', 0);
    }

    // public function getTopLike($nation_code, $type, $custom_id)
    // {
    //     $this->db->select_as("$this->tbl_as.c_community_like_category_id", "c_community_like_category_id", 0);
    //     $this->db->select_as("COUNT($this->tbl_as.c_community_like_category_id)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     // $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
    //     $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
    //     // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));
    //     $this->db->group_by('c_community_like_category_id');
    //     $this->db->order_by('total', 'desc')->page(1, 3);
    //     return $this->db->get('object', 0);
    // }

    public function checkId($nation_code, $id)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

}
