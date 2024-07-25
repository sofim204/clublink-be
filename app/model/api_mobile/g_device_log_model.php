<?php
class G_Device_Log_Model extends JI_Model
{
    public $tbl = 'g_device_log';
    public $tbl_as = 'gdl';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
        return $composites;
    }

    // public function getLastId($nation_code, $device_id)
    // {
    //     $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("device_id", $device_id);
    //     $this->db->where_as("DATE(cdate)", $this->db->esc(date("Y-m-d")));
    //     $d = $this->db->get_first('', 0);
    //     if (isset($d->last_id)) {
    //         return (int) $d->last_id;
    //     }
    //     return 0;
    // }

    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }

        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    // public function update($nation_code, $id, $du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }

    //     $this->db->where('nation_code', $nation_code);
    //     $this->db->where('id', $id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    // public function del($nation_code, $id)
    // {
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('id', $id);
    //     return $this->db->delete($this->tbl);
    // }

    public function getTblAs()
    {
        return $this->tbl_as;
    }

    public function countAll($nation_code, $b_user_id="", $device_id="")
    {
        if($b_user_id != "" && $device_id != ""){
            $this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl_as.b_user_id, $this->tbl_as.device_id))", "total", 0);
        }else if($device_id != ""){
            $this->db->select_as("COUNT(DISTINCT $this->tbl_as.b_user_id)", "total", 0);
        }else if($b_user_id != ""){
            $this->db->select_as("COUNT(DISTINCT $this->tbl_as.device_id)", "total", 0);
        }
        $this->db->from($this->tbl, $this->tbl_as);
        // $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        if($b_user_id != ""){
            $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        }
        if($device_id != ""){
            $this->db->where_as("$this->tbl_as.device_id", $this->db->esc($device_id));
        }
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    // public function getAll($nation_code, $page=1, $page_size=10, $sort_col="cdate", $sort_direction="ASC", $b_user_id="", $type="community")
    // {
    //     $this->db->select_as("$this->tbl_as.id", "block_id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
    //     $this->db->select_as("$this->tbl_as.custom_id", "custom_id", 0);
    //     $this->db->select_as("$this->tbl_as.type", "type", 0);
    //     $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        
    //     $this->db->from($this->tbl, $this->tbl_as);

    //     if($type == "community"){

    //         $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
    //         $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

    //     }

    //     if($type == "account"){

    //         $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), 'left');

    //     }

    //     if($type == "product"){

    //         $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), 'left');
    //         $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), 'left');

    //     }

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));

    //     if($type == "community"){

    //         $this->db->where_as("$this->tbl2_as.is_published", $this->db->esc(1));
    //         $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
    //         $this->db->where_as("$this->tbl2_as.is_take_down", $this->db->esc('0'));
    //         $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));

    //     }

    //     if($type == "account"){

    //         $this->db->where_as("$this->tbl4_as.is_active", $this->db->esc(1));

    //     }

    //     if($type == "product"){

    //         $this->db->where_as("$this->tbl5_as.is_active", $this->db->esc(1));
    //         $this->db->where_as("$this->tbl6_as.is_active", $this->db->esc('1'));

    //     }

    //     $this->db->order_by($sort_col, $sort_direction);

    //     $this->db->page($page, $page_size);

    //     return $this->db->get('object', 0);
    // }

    // public function getById($nation_code, $block_id=0, $b_user_id, $type, $custom_id=0)
    // {
    //     $this->db->select_as("$this->tbl_as.id", "block_id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
    //     $this->db->select_as("$this->tbl_as.custom_id", "custom_id", 0);
    //     $this->db->select_as("$this->tbl_as.type", "type", 0);
    //     $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);

    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

    //     if($block_id != 0){
    //         $this->db->where_as("$this->tbl_as.id", $this->db->esc($block_id));
    //     }

    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));

    //     if($custom_id != 0){
    //         $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
    //     }

    //     $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));

    //     return $this->db->get_first('', 0);
    // }

    public function checkId($nation_code, $id)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

}
