<?php
class G_Mobile_Registration_Activity_Model extends JI_Model
{
    public $tbl = 'g_mobile_registration_activity';
    public $tbl_as = 'gmra';
    // public $tbl2 = 'b_user';
    // public $tbl2_as = 'bu';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }
    
    public function getTblAs()
    {
        return $this->tbl_as;
    }

    // private function __joinTbl2()
    // {
    //     $cps = array();
    //     $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
    //     $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
    //     return $cps;
    // }

    public function set($di)
    {
        return $this->db->insert($this->tbl, $di);
    }

    public function update($nation_code, $id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du);
    }

    // public function delete($nation_code, $b_user_id, $id)
    // {
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("b_user_id", $b_user_id);
    //     $this->db->where("id", $id);
    //     return $this->db->delete($this->tbl);
    // }

    public function getLastId($nation_code)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }

    // public function countAll($nation_code, $b_user_id_to, $type)
    // {
    //     $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
        
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id_to", $this->db->esc($b_user_id_to));
    //     $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
        
    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;

    // }

    // public function getAll($nation_code, $b_user_id_to, $page=1, $page_size=10, $sort_col='cdate', $sort_dir='DESC', $type, $language_id=2)
    // {
    //     // $this->db->select_as("$this->tbl_as.id", "chat_id", 0);
    //     // $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
    //     // $this->db->select_as("$this->tbl_as.type", "type", 0);
    //     $this->db->select_as("$this->tbl_as.star", "star", 0);
    //     $this->db->select_as("$this->tbl_as.review", "review", 0);
    //     $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    //     // $this->db->select_as("COALESCE($this->tbl2_as.id,'0')", "b_user_id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "b_user_fnama", 0);
    //     $this->db->select_as("COALESCE($this->tbl2_as.image,'media/user/default-profile-picture.png')", "b_user_image", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id_to", $this->db->esc($b_user_id_to));
    //     $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));

    //     $this->db->order_by($sort_col, $sort_dir)->page($page, $page_size);

    //     return $this->db->get();
    // }

    public function getByReferralType($nation_code, $referral, $type="downloaded")
    {
        $this->db->select_as("*, $this->tbl_as.id", "id", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.referral", $this->db->esc($referral));

        if($type == "downloaded"){

            $this->db->where_as("$this->tbl_as.is_downloaded", $this->db->esc(0));
            $this->db->where_as("$this->tbl_as.is_registered", $this->db->esc(0));

        }else if($type == "registered"){

            $this->db->where_as("$this->tbl_as.is_downloaded", $this->db->esc(1));
            $this->db->where_as("$this->tbl_as.is_registered", $this->db->esc(0));

        }

        $this->db->order_by("$this->tbl_as.cdate", "DESC");

        return $this->db->get_first();
    }

}
