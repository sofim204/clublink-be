<?php
class G_Daily_Track_Record_Model extends JI_Model
{
    public $tbl = 'g_daily_track_record';
    public $tbl_as = 'gdtr';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function getLastId($nation_code)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return (int) $d->last_id;
        }
        return 0;
    }

    public function getLatestRecord($nation_code)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", 'id', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->order_by("$this->tbl_as.cdate","desc");
        return $this->db->get_first("", 0);
    }

    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    } 

    public function update($nation_code, $id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where('id', $id);
        return $this->db->update($this->tbl, $du, 0);
    }

    public function updateTotalData($cdate, $field_data, $plusormin, $total)
    {
        $this->db->exec("UPDATE `$this->tbl` SET $field_data = $field_data $plusormin $total WHERE DATE(cdate) = '$cdate';");
    }
}
