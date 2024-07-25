<?php
class C_Sellon_Ads_model extends JI_Model
{
    public $tbl = 'c_sellon_ads';
    public $tbl_as = 'csa';
    public $tbl2 = 'c_dont_show_ads';
    public $tbl2_as = 'cdsa';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function getLastId($nation_code)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl2_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl2, $this->tbl2_as);
        $this->db->where("nation_code", $nation_code);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return (int) $d->last_id;
        }
        return 0;
    }

    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }

        return $this->db->insert($this->tbl2, $di, 0, 0);
    }

    public function getAll($nation_code)
    {
        $this->db->select_as("$this->tbl_as.id", "id");
        $this->db->select_as("$this->tbl_as.url", "url");
        $this->db->select_as("$this->tbl_as.img_thumbnail", "img_thumbnail");
        $this->db->select_as("$this->tbl_as.cdate", "cdate");
        $this->db->select_as("$this->tbl_as.priority", "priority");
        $this->db->select_as("$this->tbl_as.type_ads", "type_ads");
        $this->db->select_as("$this->tbl_as.product_id", "product_id");
        $this->db->select_as("$this->tbl_as.seller_id", "seller_id");
        $this->db->select_as("$this->tbl_as.community_id", "community_id");
        $this->db->select_as("$this->tbl_as.url_webview", "url_webview");
        $this->db->select_as("$this->tbl_as.teks", "teks");
        $this->db->select_as("''", "judul");
        $this->db->from($this->tbl,$this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.type_ads", $this->db->esc("webview_wallet_ads"),'AND','!=');
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.start_date", $this->db->esc(date('Y-m-d')),'AND','<=', 0, 0);
        $this->db->where_as("$this->tbl_as.end_date", $this->db->esc(date('Y-m-d')),'AND','>=', 0, 0);
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

    public function checkData($nation_code, $device_id, $ads_id)
    {
        $this->db->from($this->tbl2, $this->tbl2_as);
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl2_as.device_id", $this->db->esc($device_id));
        $this->db->where_as("$this->tbl2_as.ads_id", $this->db->esc($ads_id));
        return $this->db->get();
    }

    public function getDataByDeviceId($nation_code, $ads_id, $device_id) {
        $this->db->select_as("$this->tbl2_as.id", "id");
        $this->db->select_as("$this->tbl2_as.ads_id", "ads_id");
        $this->db->select_as("$this->tbl2_as.device_id", "device_id");
        $this->db->from($this->tbl2, $this->tbl2_as);
        $this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl2_as.ads_id", $this->db->esc($ads_id));
        $this->db->where_as("$this->tbl2_as.device_id", $this->db->esc($device_id));
        return $this->db->get();
    }

    public function getByType($nation_code, $type_ads)
    {
        $this->db->select_as("*, $this->tbl_as.id", "id");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.type_ads", $this->db->esc($type_ads));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.start_date", $this->db->esc(date('Y-m-d')),'AND','<=', 0, 0);
        $this->db->where_as("$this->tbl_as.end_date", $this->db->esc(date('Y-m-d')),'AND','>=', 0, 0);
        $this->db->order_by("$this->tbl_as.priority", "ASC");
        return $this->db->get();
    }
}
