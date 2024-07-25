<?php
class I_Group_Directory_Model extends JI_Model
{
    public $tbl = 'i_group_directory';
    public $tbl_as = 'igd';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
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
        $this->db->where('nation_code', $nation_code);
        $this->db->where('id', $id);
        return $this->db->update($this->tbl, $du, 0);
    }

    public function getTblAs()
    {
        return $this->tbl_as;
    }

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

    public function getByGroupId($nation_code, $page=1, $page_size=6, $sort_col="id", $sort_direction="ASC", $group_id, $type, $keyword="")
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.i_group_id", "i_group_id", 0);
        $this->db->select_as("$this->tbl_as.directory_name", "directory_name", 0);
        $this->db->select_as("$this->tbl_as.type", "type", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
        $this->db->select_as("$this->tbl_as.is_owner_directory", "is_owner_directory", 0);
        $this->db->select_as("$this->tbl_as.is_owner_group", "is_owner_group", 0);
        $this->db->select_as("$this->tbl_as.is_publish", "is_publish", 0);
        $this->db->select_as("$this->tbl_as.i_group_post_id", "i_group_post_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.i_group_id", $this->db->esc($group_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        if($type == "album"){
            $this->db->where_as("$this->tbl_as.type", $this->db->esc('album'));
        } else if($type == "folder"){
            $this->db->where_as("$this->tbl_as.type", $this->db->esc('folder'));
        }

        if (strlen($keyword) > 0) {
            $this->db->where_as("$this->tbl_as.directory_name", addslashes($keyword), "AND", "%like%");
        }

        $this->db->order_by("LOWER(".$sort_col.")", $sort_direction);
        $this->db->page($page, $page_size);

        return $this->db->get('', 0);
    }

    public function getByDirectoryId($nation_code, $id)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.i_group_id", "i_group_id", 0);
        $this->db->select_as("$this->tbl_as.directory_name", "directory_name", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
        $this->db->select_as("$this->tbl_as.is_owner_group", "is_owner_group", 0);
        $this->db->select_as("$this->tbl_as.is_owner_directory", "is_owner_directory", 0);
        $this->db->select_as("$this->tbl_as.is_publish", "is_publish", 0);
        $this->db->select_as("$this->tbl_as.i_group_post_id", "i_group_post_id", 0);
        $this->db->select_as("$this->tbl_as.type", "type", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        return $this->db->get_first('', 0);
    }

    public function IsOwnerDirectory($nation_code, $directory_id, $b_user_id)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($directory_id));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.is_owner_directory", $this->db->esc(1));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }
}
