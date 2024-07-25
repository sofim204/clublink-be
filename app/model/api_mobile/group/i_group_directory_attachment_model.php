<?php
class I_Group_Directory_Attachment_Model extends JI_Model
{
    // public $tbl = 'i_group_directory_attachment';
    // public $tbl_as = 'igda';
    public $tbl = 'i_group_post_attachment';
    public $tbl_as = 'igpa';

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

    public function getAll($nation_code)
    {
		$this->db->select_as("*,$this->tbl_as.id",'id',0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code", $nation_code);

		$this->db->where("is_active",1);

		$this->db->order_by("cdate", "DESC");

		return $this->db->get();
	}

    // public function getByDirectoryId($nation_code, $directory_id, $jenis, $page=1, $page_size=6, $sort_col="cdate", $sort_direction="ASC")
    public function getByDirectoryId($nation_code, $directory_id, $jenis, $page=1, $page_size=6, $sort_col="id", $sort_direction="ASC", $keyword="")
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.i_group_directory_id", "i_group_directory_id", 0);
        $this->db->select_as("$this->tbl_as.i_group_id", "i_group_id", 0);
        $this->db->select_as("$this->tbl_as.i_group_post_id", "i_group_post_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.jenis", "jenis", 0);
        if($jenis == "photo_video") {
            $this->db->select_as("$this->tbl_as.url", "url", 0);
            $this->db->select_as("$this->tbl_as.url_thumb", "url_thumb", 0);
        } else if($jenis == "folder_file") {
            $this->db->select_as("$this->tbl_as.url", "url", 0);
            $this->db->select_as("$this->tbl_as.file_name", "file_name", 0);
        }
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.is_owner_attachment", "is_owner_attachment", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.i_group_directory_id", $this->db->esc($directory_id), "AND", "=", 0, 0);
        if($jenis == "photo_video") {
            $this->db->where_as("$this->tbl_as.jenis", $this->db->esc('image'), "OR", "=", 1, 0);
            $this->db->where_as("$this->tbl_as.jenis", $this->db->esc('video'), "AND", "=", 0, 1);
        } else if($jenis == "folder_file") {
            $this->db->where_as("$this->tbl_as.jenis", $this->db->esc('file'), "AND", "=", 0, 0);
        }
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);

        if (strlen($keyword) > 0) {
            $this->db->where_as("$this->tbl_as.file_name", addslashes($keyword), "OR", "%like%", 1, 1);
        }
        // $this->db->order_by("LOWER(".$sort_col.")", $sort_direction);
        // $this->db->page($page, $page_size);
        $this->db->order_by("LOWER(".$sort_col.")", $sort_direction);
        $this->db->page($page, $page_size);
        return $this->db->get('', 0);
    }

	public function countAttachmentBy($nation_code, $id, $param) {
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
        if($param == "all") {
            $this->db->where_as("$this->tbl_as.i_group_id", $this->db->esc($id), "AND", "=", 0, 0);
        } else if($param == "list") {
            $this->db->where_as("$this->tbl_as.i_group_directory_id", $this->db->esc($id), "AND", "=", 0, 0);
        }
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

    public function getByAttachmentId($nation_code, $attachment_id)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.i_group_directory_id", "i_group_directory_id", 0);
        $this->db->select_as("$this->tbl_as.i_group_id", "i_group_id", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.file_name", "file_name", 0);
        $this->db->select_as("$this->tbl_as.url", "url", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($attachment_id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        return $this->db->get_first('', 0);
    }

    public function getByData($nation_code, $id, $param)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.i_group_id", "i_group_id", 0);
        $this->db->select_as("$this->tbl_as.jenis", "jenis", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        if($param == "photo") {
            $this->db->select_as("$this->tbl_as.url", "url", 0);
        } else if($param == "video") {
            $this->db->select_as("$this->tbl_as.url", "url", 0);
            $this->db->select_as("$this->tbl_as.url_thumb", "url_thumb", 0);
        } else if($param == "file") {
            $this->db->select_as("$this->tbl_as.url", "url", 0);
            $this->db->select_as("$this->tbl_as.file_name", "file_name", 0);
        }
        $this->db->select_as("$this->tbl_as.is_owner_attachment", "is_owner_attachment", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->order_by("cdate", "DESC");
		return $this->db->get_first('', 0);
    }
}
