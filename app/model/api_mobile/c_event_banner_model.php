<?php
class C_Event_Banner_model extends JI_Model
{
    public $tbl = 'c_event_banner';
    public $tbl_as = 'ceb';


    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function getAll($nation_code)
    {
        // $this->db->select_as("*, $this->tbl_as.id", "id");
        $this->db->select_as("$this->tbl_as.id", "id");
        $this->db->select_as("$this->tbl_as.judul", "judul");
        $this->db->select_as("$this->tbl_as.teks", "teks");
        $this->db->select_as("$this->tbl_as.url", "url");
        $this->db->select_as("$this->tbl_as.url_type", "url_type");
        $this->db->select_as("$this->tbl_as.img_thumbnail", "img_thumbnail");
        $this->db->select_as("$this->tbl_as.type_event_banner", "type_event_banner");
        $this->db->select_as("$this->tbl_as.product_id", "product_id");
        $this->db->select_as("$this->tbl_as.seller_id", "seller_id");
        $this->db->select_as("$this->tbl_as.community_id", "community_id");
        $this->db->select_as("$this->tbl_as.cdate", "cdate");
        $this->db->from($this->tbl,$this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.start_date", $this->db->esc(date('Y-m-d')),'AND','<=');
        $this->db->where_as("$this->tbl_as.end_date", $this->db->esc(date('Y-m-d')),'AND','>=');
        $this->db->order_by("$this->tbl_as.priority", "ASC");
        return $this->db->get();
    }

    public function getAllNew($nation_code)
    {
        // $this->db->select_as("*, $this->tbl_as.id", "id");
        $this->db->select_as("$this->tbl_as.id", "id");
        $this->db->select_as("$this->tbl_as.judul", "judul");
        $this->db->select_as("IF($this->tbl_as.type_event_banner = 'webview', $this->tbl_as.teks, '')", "teks");
        $this->db->select_as("$this->tbl_as.url", "url");
        $this->db->select_as("$this->tbl_as.url_type", "url_type");
        $this->db->select_as("$this->tbl_as.img_thumbnail", "img_thumbnail");
        $this->db->select_as("$this->tbl_as.type_event_banner", "type_event_banner");
        $this->db->select_as("$this->tbl_as.product_id", "product_id");
        $this->db->select_as("$this->tbl_as.seller_id", "seller_id");
        $this->db->select_as("$this->tbl_as.community_id", "community_id");
        $this->db->select_as("$this->tbl_as.cdate", "cdate");
        $this->db->select_as("$this->tbl_as.priority", "priority");
        $this->db->from($this->tbl,$this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.start_date", $this->db->esc(date('Y-m-d')),'AND','<=');
        $this->db->where_as("$this->tbl_as.end_date", $this->db->esc(date('Y-m-d')),'AND','>=');
        $this->db->order_by("$this->tbl_as.priority", "ASC");
        return $this->db->get();
    }

    public function getById($nation_code, $id)
    {
        $this->db->select_as("*, $this->tbl_as.id", "id");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        return $this->db->get_first();
    }

}
