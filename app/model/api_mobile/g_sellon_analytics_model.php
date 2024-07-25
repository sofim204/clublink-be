<?php
class G_Sellon_Analytics_Model extends JI_Model
{
    public $tbl = 'g_sellon_analytics';
    public $tbl_as = 'gsa';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
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

    // public function getLatestRecord($nation_code, $datenow, $corner, $type, $category, $detail)
    // {
    //     $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.cdate", $this->db->esc($datenow));
    //     // $this->db->where_as("$this->tbl_as.corner", $this->db->esc($corner));
    //     // $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
    //     // $this->db->where_as("$this->tbl_as.category", $this->db->esc($category));
    //     // $this->db->where_as("$this->tbl_as.detail", $this->db->esc($detail));
    //     // $this->db->order_by("$this->tbl_as.cdate","desc");
    //     $d = $this->db->get_first('', 0);
    //     if (isset($d->last_id)) {
    //         return (int) $d->last_id;
    //     }
    //     return 0;
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

    public function checkData($nation_code, $datenow, $corner, $type, $category, $detail, $sub_detail)
    {
        $this->db->select_as("COUNT(*)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.cdate", $this->db->esc($datenow));
        $this->db->where_as("$this->tbl_as.corner", $this->db->esc($corner));
        $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
        $this->db->where_as("$this->tbl_as.category", $this->db->esc($category));
        $this->db->where_as("$this->tbl_as.detail", $this->db->esc($detail));
        $this->db->where_as("$this->tbl_as.sub_detail", $this->db->esc($sub_detail));

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    } 

    public function update($nation_code, $corner, $type, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where('corner', $corner);
        $this->db->where('type', $type);
        return $this->db->update($this->tbl, $du, 0);
    }

    public function updateTotalData($nation_code, $datenow, $field_count, $corner, $type, $category, $detail, $sub_detail, $operator, $total)
    {
        // $this->db->exec("UPDATE `$this->tbl` SET $field_count = $field_count $operator $total WHERE 
        // nation_code = '$nation_code' AND 
        // cdate = '$datenow' AND 
        // corner = '$corner' AND 
        // type = '$type' AND
        // category = '$category' AND
        // detail = '$detail' ;");
        $corner_new = $this->db->esc($corner);
        $type_new = $this->db->esc($type);
        $category_new = $this->db->esc($category);
        $detail_new = $this->db->esc($detail);
        $sub_detail_new = $this->db->esc($sub_detail);

        $sql = "UPDATE $this->tbl SET $field_count = $field_count $operator $total WHERE 
        nation_code = '$nation_code' AND cdate = '$datenow' AND corner = $corner_new AND type = $type_new AND category = $category_new AND detail = $detail_new AND sub_detail = $sub_detail_new";

        return $this->db->exec($sql);

    }
    
}
