<?php
class G_Wallet_Access_Schedule_Model extends JI_Model
{
    public $tbl = 'g_wallet_access_schedule';
    public $tbl_as = 'gwas';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.c_community_category_id", "=", "$this->tbl2_as.id");
        return $composites;
    }

    // public function getLastId($nation_code)
    // {
    //     $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $d = $this->db->get_first('', 0);
    //     if (isset($d->last_id)) {
    //         return (int) $d->last_id;
    //     }
    //     return 0;
    // }

    // public function set($di)
    // {
    //     if (!is_array($di)) {
    //         return 0;
    //     }
    //     return $this->db->insert($this->tbl, $di, 0, 0);
    // }

    // public function update($nation_code, $id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     $this->db->where('nation_code', $nation_code);
    //     $this->db->where('id', $id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    // public function del($nation_code, $id, $b_user_id)
    // {
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('id', $id);
    //     $this->db->where('b_user_id', $b_user_id);
    //     return $this->db->delete($this->tbl);
    // }

    public function getTblAs()
    {
        return $this->tbl_as;
    }

    // public function countAll($nation_code, $keyword="", $c_community_category_ids=array(), $type="", $pelangganAddress, $b_user_id="", $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $query_type="normal")
    // {
    //     $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));

    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }

    // public function getAll($nation_code, $page=1, $page_size=10, $sort_col="id", $sort_direction="ASC", $keyword="", $c_community_category_ids=array(), $type="", $pelangganAddress, $b_user_id="", $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse, $query_type="normal", $language_id=1)
    // {
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.id", "c_community_id", 0);
    //     $this->db->select_as("$this->tbl_as.c_community_category_id", "c_community_category_id", 0);
    //     $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_starter", 0);
    //     $this->db->select_as("$this->tbl_as.title", "title", 0);
    //     $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    //     $this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);

    //     $this->db->select_as("$this->tbl_as.kodepos", "kodepos");

    //     $this->db->select_as("$this->tbl_as.total_discussion", "total_discussion", 0);
    //     $this->db->select_as("$this->tbl_as.group_chat_type", "group_chat_type", 0);
    //     $this->db->select_as("$this->tbl_as.e_chat_room_id", "chat_room_id", 0);
    //     $this->db->select_as("$this->tbl_as.total_people_group_chat", "total_people_group_chat", 0);
    //     $this->db->select_as("IF(($this->tbl_as.c_community_category_id = 24),(100),(50))", "max_people_group_chat", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));

    //     $this->db->order_by($sort_col, $sort_direction);
    //     $this->db->page($page, $page_size);

    //     return $this->db->get('object', 0);
    // }

    // public function getById($nation_code, $pid, $pelanggan, $language_id=1)
    // {
    //     $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_starter", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl3_as.fnama").',"")', "b_user_nama_starter", 0);
    //     $this->db->select_as("COALESCE($this->tbl3_as.image,'')", "b_user_image_starter", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl_as.alamat2").',"")', "alamat2", 0);
    //     $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(50))", "alamat4", 0);
    //     $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
    //     $this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
    //     $this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
    //     $this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
    //     $this->db->select_as("$this->tbl_as.negara", "negara", 0);
    //     $this->db->select_as("$this->tbl_as.kodepos", "kodepos", 0);
    //     $this->db->select_as("$this->tbl_as.c_community_category_id", "c_community_category_id", 0);
    //     $this->db->select_as("$this->tbl_as.group_chat_type", "group_chat_type", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //     $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($pid));
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
    //     $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
    //     return $this->db->get_first('', 0);
    // }

    public function getByDate($nation_code, $date)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.nation_code", "nation_code", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.from", $this->db->esc($date), "AND", "<=");
        $this->db->where_as("$this->tbl_as.to", $this->db->esc($date), "AND", ">=");
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->order_by("$this->tbl_as.to", "ASC");
        return $this->db->get_first('', 0);
    }

}
