<?php
class E_Likes_Model extends JI_Model
{
    public $tbl = 'e_likes';
    public $tbl_as = 'el';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';

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

    public function set($di)
    {
        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    public function update($nation_code, $type, $id, $du)
    {
        $this->db->where('nation_code', $nation_code);
        $this->db->where('type', $type);
        $this->db->where('id', $id);
        $this->db->where('is_active', 1);
        return $this->db->update($this->tbl, $du, 0);
    }

    public function getTblAs()
    {
        return $this->tbl_as;
    }
    public function getTbl2As()
    {
        return $this->tbl2_as;
    }

    public function getLastId($nation_code, $type){
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code",$nation_code);
        $this->db->where("type",$type);
        $d = $this->db->get_first('',0);
        if(isset($d->last_id)) return $d->last_id;
        return 0;
    }

    public function countAll($nation_code, $keyword='', $type, $custom_id)
    {
        $this->db->exec("SET NAMES 'UTF8MB4'");
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
        $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        
        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanent user feature
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));
        
        //advanced filter
        if (mb_strlen($keyword)>0) {
            $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl2_as.fnama").',"")', $keyword, 'AND', '%like%');
        }
        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $keyword='', $type, $custom_id)
    {
        $this->db->exec("SET NAMES 'UTF8MB4'");
        $this->db->select_as("$this->tbl_as.id", "like_id", 0);
        $this->db->select_as("$this->tbl_as.type", "type", 0);
        $this->db->select_as("$this->tbl_as.custom_id", "custom_id", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanent user feature
        $this->db->select_as("$this->tbl2_as.id", "b_user_id", 0);
        $this->db->select_as("$this->tbl2_as.is_active", "b_user_is_active", 0);

        $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl2_as.fnama").',"")', "b_user_nama", 0);
        $this->db->select_as("$this->tbl2_as.image", "foto", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
        $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanent user feature
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));

        //advanced filter
        if (mb_strlen($keyword)>0) {
            $this->db->where_as('COALESCE('.$this->__decrypt("$this->tbl2_as.fnama").',"")', addslashes($keyword), 'AND', '%like%');
        }
        $this->db->order_by($sort_col, $sort_dir)->page($page, $page_size);
        return $this->db->get('object', 0);
    }

    public function getByCustomIdUserId($nation_code, $type, $custom_id, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
        $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        
        return $this->db->get_first('object', 0);
        
    }

}
