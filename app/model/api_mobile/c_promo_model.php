<?php
class C_Promo_Model extends JI_Model
{
    public $tbl = 'c_promo';
    public $tbl_as = 'cpro';
    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }
    public function getById($nation_code, $id)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where('nation_code', $nation_code);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        return $this->db->get_first();
    }
    // public function getHomepage($nation_code, $device="")
    // {
    //     $this->db->flushQuery();
    //     $this->db->cache_save = 0;
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.judul", "title", 0);
    //     $this->db->select_as("$this->tbl_as.gambar", "image", 0);
    //     $this->db->select_as("$this->tbl_as.edate", "duedate", 0);
    //     $this->db->select_as("$this->tbl_as.cdate", "", 0);
    //     $this->db->select_as("$this->tbl_as.top_bar", "topbar", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where('nation_code', $nation_code);
    //     $this->db->where_as("DATE(`edate`)", "CURRENT_DATE()", "AND", ">=");
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    //     $this->db->order_by('priority', 'asc');
    //     $this->db->limit(0, 5);
    //     return $this->db->get('', 0);
    // }

    //by Donny Dennison - 2 july 2021 9:37
    //move-campaign-to-sponsored
    // public function getList($nation_code)
    public function getList($nation_code, $limit=0)

    {
        $this->db->select_as("*, $this->tbl_as.id", "id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("DATE($this->tbl_as.edate)", "CURRENT_DATE()", "AND", ">=");
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->order_by('priority', 'asc');

        //by Donny Dennison - 2 july 2021 9:37
        //move-campaign-to-sponsored
        if($limit != 0){
            $this->db->limit(0, $limit);

        }

        return $this->db->get('', 0);
    }
}
