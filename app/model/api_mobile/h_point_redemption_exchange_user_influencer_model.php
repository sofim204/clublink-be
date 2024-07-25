<?php
class H_Point_Redemption_Exchange_User_Influencer_Model extends JI_Model
{
    public $tbl = 'h_point_redemption_exchange_user_influencer';
    public $tbl_as = 'hpreui';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function getInfluencerById($user_id) {
        
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($user_id));
        return $this->db->get_first('', 0);
    }

}
