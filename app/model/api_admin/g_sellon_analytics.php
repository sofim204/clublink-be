<?php

class G_Sellon_Analytics extends SENE_Model
{
    public $tbl = 'g_sellon_analytics';
    public $tbl_as = 'gsa';

    public $tbl2 = "c_community";
    public $tbl2_as = "cc";

    public function __construct()
    {
        parent::__construct();

        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function getById($nation_code, $id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->get_first();
    }

    public function countBy($corner = [], $q, $fromDate, $toDate)
    {
        $this->db->select_as("SUM($this->tbl_as.count)", "totalView");
        $this->db->from($this->tbl, $this->tbl_as);

        if (strlen($fromDate) == 10 && strlen($toDate) == 10) {
            $this->db->between("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
        } else if (strlen($fromDate) == 10 && strlen($toDate) != 10) {
            $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", 'AND', '>=');
        } else if (strlen($fromDate) != 10 && strlen($toDate) == 10) {
            $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$toDate')", 'AND', '<=');
        }

        if (!empty($corner)) {
            $this->db->where_in("$this->tbl_as.corner", $corner);
        }

        if (!empty($q)) {
            $this->db->where("$this->tbl_as.type", $q, "OR", "%like%");
        }

        $d = $this->db->get_first("object", 0);
        if (isset($d->totalView)) return $d->totalView;
        return 0;
    }

    public function getAll($fromDate = "", $toDate = "", $groupBy)
    {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl.corner", "corner");
        $this->db->select_as("$this->tbl.type", "type");
        $this->db->select_as("$this->tbl.category", "category");
        $this->db->select_as("$this->tbl.cdate", "cdate");
        $this->db->select_as("$this->tbl.sub_detail", "sub_detail");
        $this->db->select_as("$this->tbl.detail", "detail");
        $this->db->select_as("$this->tbl.type_seq", "type_seq");
        $this->db->select_as("$this->tbl.corner_seq", "corner_seq");
        $this->db->select_as("SUM($this->tbl.count)", "count");

        $this->db->where('nation_code', 62);

        if (strlen($fromDate) == 10 && strlen($toDate) == 10) {
            $this->db->between("DATE($this->tbl.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
        } else if (strlen($fromDate) == 10 && strlen($toDate) != 10) {
            $this->db->where_as("DATE($this->tbl.cdate)", "DATE('$fromDate')", 'AND', '>=');
        } else if (strlen($fromDate) != 10 && strlen($toDate) == 10) {
            $this->db->where_as("DATE($this->tbl.cdate)", "DATE('$toDate')", 'AND', '<=');
        }

        $this->db->where("$this->tbl.corner", 'Buy&Sell', "AND", "<>");

        $this->db->group_by($groupBy);
        $this->db->order_by("$this->tbl.corner_seq", "ASC");

        return $this->db->get("object", 0);
    }

    public function getChatAll($fromDate = "", $toDate = "", $order, $sort)
    {
        $this->db->select_as("$this->tbl.corner", "corner");
        $this->db->select_as("$this->tbl.type", "type");
        $this->db->select_as("$this->tbl.category", "category");
        $this->db->select_as("$this->tbl.cdate", "cdate");
        $this->db->select_as("$this->tbl.sub_detail", "sub_detail");
        $this->db->select_as("$this->tbl.detail", "detail");
        $this->db->select_as("$this->tbl.type_seq", "type_seq");
        $this->db->select_as("$this->tbl.corner_seq", "corner_seq");
        $this->db->select_as("SUM($this->tbl.count)", "count");

        $this->db->where('nation_code', 62);

        if (strlen($fromDate) == 10 && strlen($toDate) == 10) {
            $this->db->between("DATE($this->tbl.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
        } else if (strlen($fromDate) == 10 && strlen($toDate) != 10) {
            $this->db->where_as("DATE($this->tbl.cdate)", "DATE('$fromDate')", 'AND', '>=');
        } else if (strlen($fromDate) != 10 && strlen($toDate) == 10) {
            $this->db->where_as("DATE($this->tbl.cdate)", "DATE('$toDate')", 'AND', '<=');
        }

        $this->db->where("corner", "Chat", "AND", "=", 1, 1);
        $this->db->group_by('type');
        $this->db->order_by("$this->tbl.type_seq", "ASC");

        return $this->db->get("object", 0);
    }

    public function getChatMain($fromDate = "", $toDate = "", $order, $sort)
    {
        $this->db->select_as("$this->tbl.corner", "corner");
        $this->db->select_as("$this->tbl.type", "type");
        $this->db->select_as("$this->tbl.category", "category");
        $this->db->select_as("$this->tbl.cdate", "cdate");
        $this->db->select_as("$this->tbl.sub_detail", "sub_detail");
        $this->db->select_as("$this->tbl.detail", "detail");
        $this->db->select_as("$this->tbl.type_seq", "type_seq");
        $this->db->select_as("$this->tbl.corner_seq", "corner_seq");
        $this->db->select_as("SUM($this->tbl.count)", "count");

        $this->db->where('nation_code', 62);

        if (strlen($fromDate) == 10 && strlen($toDate) == 10) {
            $this->db->between("DATE($this->tbl.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
        } else if (strlen($fromDate) == 10 && strlen($toDate) != 10) {
            $this->db->where_as("DATE($this->tbl.cdate)", "DATE('$fromDate')", 'AND', '>=');
        } else if (strlen($fromDate) != 10 && strlen($toDate) == 10) {
            $this->db->where_as("DATE($this->tbl.cdate)", "DATE('$toDate')", 'AND', '<=');
        }

        $this->db->where("corner", "Chat", "AND", "=", 1, 1);
        $this->db->where("type", "Main", "AND", "=", 1, 1);
        $this->db->group_by('corner, type, category');
        $this->db->order_by("$this->tbl.category", "ASC");

        return $this->db->get("object", 0);
    }

    public function getChatExceptMain($fromDate = "", $toDate = "", $order, $sort)
    {
        $this->db->select_as("$this->tbl.corner", "corner");
        $this->db->select_as("$this->tbl.type", "type");
        $this->db->select_as("$this->tbl.category", "category");
        $this->db->select_as("$this->tbl.cdate", "cdate");
        $this->db->select_as("$this->tbl.sub_detail", "sub_detail");
        $this->db->select_as("$this->tbl.detail", "detail");
        $this->db->select_as("$this->tbl.type_seq", "type_seq");
        $this->db->select_as("$this->tbl.corner_seq", "corner_seq");
        $this->db->select_as("SUM($this->tbl.count)", "count");

        $this->db->where('nation_code', 62);

        if (strlen($fromDate) == 10 && strlen($toDate) == 10) {
            $this->db->between("DATE($this->tbl.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
        } else if (strlen($fromDate) == 10 && strlen($toDate) != 10) {
            $this->db->where_as("DATE($this->tbl.cdate)", "DATE('$fromDate')", 'AND', '>=');
        } else if (strlen($fromDate) != 10 && strlen($toDate) == 10) {
            $this->db->where_as("DATE($this->tbl.cdate)", "DATE('$toDate')", 'AND', '<=');
        }

        $this->db->where("corner", "Chat", "AND", "=", 1, 1);
        $this->db->where("type", "Main", "AND", "!=", 1, 1);
        $this->db->group_by('type');
        $this->db->order_by("$this->tbl.type_seq", "ASC");

        return $this->db->get("object", 0);
    }

    public function getByIdCC($nation_code, $id)
    {
        $this->db->select_as("$this->tbl2_as.title", "title");
        $this->db->from($this->tbl2, $this->tbl2_as);
        $this->db->where_as("$this->tbl2_as.id", $this->db->esc($id));

        return $this->db->get_first('', 0);
    }
}
