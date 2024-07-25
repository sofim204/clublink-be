<?php
class B_User_ProductWanted_Model extends JI_Model
{
    public $is_cacheable;
    public $tbl = 'b_user_productwanted';
    public $tbl_as = 'bupw';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';

    public function __construct()
    {
        parent::__construct();
        $this->is_cacheable = 0;
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
        return $composites;
    }

    public function getLastId($nation_code, $b_user_id)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }
    public function getById($nation_code, $b_user_id, $id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("id", $id);
        return $this->db->get_first();
    }

    public function getAll($nation_code, $b_user_id)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->order_by("id", "DESC");
        $this->db->group_by("CONCAT(nation_code,'-',b_user_id,'-',keyword_text)");
        return $this->db->get();
    }

    public function set($di)
    {
        return $this->db->insert($this->tbl, $di);
    }
    public function update($nation_code, $b_user_id, $id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du);
    }
    public function delete($nation_code, $b_user_id, $id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("id", $id);
        return $this->db->delete($this->tbl);
    }
    public function check($nation_code, $b_user_id, $keyword_text)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("keyword_text", $keyword_text);
        return $this->db->get_first();
    }
    public function getWanteds($nation_code, $keyword_text)
    {
        $keyword_text = strtolower(strip_tags($keyword_text));
        $this->db->select_as("$this->tbl_as.id", "b_user_productwanted_id", 0);
        $this->db->select_as("$this->tbl2_as.id", "b_user_id_buyer", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama_buyer", 0);
        $this->db->select_as("$this->tbl2_as.image", "b_user_image_buyer", 0);
        $this->db->select_as("$this->tbl2_as.device", "b_user_device_buyer", 0);
        $this->db->select_as("$this->tbl2_as.fcm_token", "b_user_fcm_token_buyer", 0);
        $this->db->select_as("$this->tbl2_as.is_active", "b_user_is_active", 0);
        $this->db->select_as("$this->tbl_as.keyword_text", "keyword_text", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        // $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));

        //by Donny Dennison - 23-09-2021 15:45
        //revamp-profile
        // $this->db->where_as($this->db->esc(strtolower($keyword_text)), "LOWER(CONCAT('%',$this->tbl_as.keyword_text,'%'))", "AND", "like");
        $this->db->where_as("LOWER($this->tbl_as.keyword_text)", addslashes(strtolower($keyword_text)), "OR", "%like%",1,0);
        $this->db->where_as($this->db->esc(strtolower($keyword_text)), "LOWER(CONCAT('%',$this->tbl_as.keyword_text,'%'))", "AND", "like",0,1);

        $this->db->order_by("$this->tbl_as.id", "DESC");
        //$this->db->group_by("CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.b_user_id,'-',$this->tbl_as.keyword_text)");
        return $this->db->get('', 0);
    }
}
