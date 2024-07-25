<?php
class I_Group_Category_Model extends JI_Model
{
    public $tbl = 'i_group_category';
    public $tbl_as = 'igc';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function getAll($nation_code, $page=0, $pagesize=10, $language_id=2)
    {
        $this->db->select('id');
        $this->db->select_as("IF($language_id = 4 AND $this->tbl_as.thailand IS NOT NULL AND $this->tbl_as.thailand != '', $this->tbl_as.thailand, IF($language_id = 3 AND $this->tbl_as.korea IS NOT NULL AND $this->tbl_as.korea != '', $this->tbl_as.korea, IF($language_id = 2 AND $this->tbl_as.indonesia IS NOT NULL AND $this->tbl_as.indonesia != '', $this->tbl_as.indonesia, $this->tbl_as.nama)))", "nama");
        $this->db->select('image_icon');
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("type", "category");
        $this->db->where("is_visible", 1);
        $this->db->where("is_active", 1);
        if($language_id == 2){
            $this->db->order_by("prioritas_indonesia", "ASC");
        }else{
            $this->db->order_by("prioritas", "ASC");
        }
        if($page != 0 && $pagesize != 0){
            $this->db->limit($page, $pagesize);
        }
        return $this->db->get("object", 0);
    }

    public function getAllSubCategory($nation_code, $page=0, $pagesize=10, $language_id=2, $i_group_category_id)
    {
        $this->db->select('id');
        $this->db->select_as("IF($language_id = 4 AND $this->tbl_as.thailand IS NOT NULL AND $this->tbl_as.thailand != '', $this->tbl_as.thailand, IF($language_id = 3 AND $this->tbl_as.korea IS NOT NULL AND $this->tbl_as.korea != '', $this->tbl_as.korea, IF($language_id = 2 AND $this->tbl_as.indonesia IS NOT NULL AND $this->tbl_as.indonesia != '', $this->tbl_as.indonesia, $this->tbl_as.nama)))", "nama");
        $this->db->select('image_icon');
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("type", "sub category");
        $this->db->where("is_visible", 1);
        $this->db->where("is_active", 1);
        $this->db->where("i_group_category_id", $i_group_category_id);
        if($language_id == 2){
            $this->db->order_by("prioritas_indonesia", "ASC");
        }else{
            $this->db->order_by("prioritas", "ASC");
        }
        if($page != 0 && $pagesize != 0){
            $this->db->limit($page, $pagesize);
        }
        return $this->db->get("object", 0);
    }

    // public function countAll($nation_code, $keyword="")
    // {
    //     $this->db->select_as("COUNT(*)", "jumlah", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     if (strlen($keyword)>1) {
    //         $this->db->where("utype", $keyword, "OR", "%like%", 1, 0);
    //         $this->db->where("nama", $keyword, "OR", "%like%", 0, 0);
    //         $this->db->where("deskripsi", $keyword, "OR", "%like%", 0, 1);
    //     }
    //     $d = $this->db->get_first("object", 0);
    //     if (isset($d->jumlah)) {
    //         return $d->jumlah;
    //     }
    //     return 0;
    // }

    public function getById($nation_code, $id, $language_id=1)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("IF($language_id = 4 AND $this->tbl_as.thailand IS NOT NULL AND $this->tbl_as.thailand != '', $this->tbl_as.thailand, IF($language_id = 3 AND $this->tbl_as.korea IS NOT NULL AND $this->tbl_as.korea != '', $this->tbl_as.korea, IF($language_id = 2 AND $this->tbl_as.indonesia IS NOT NULL AND $this->tbl_as.indonesia != '', $this->tbl_as.indonesia, $this->tbl_as.nama)))", "nama");
        $this->db->select_as("$this->tbl_as.image_icon", "image_icon", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $this->db->where("is_visible", 1);
        $this->db->where("is_active", 1);
        return $this->db->get_first('', 0);
    }

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
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("id", $id);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    // public function del($nation_code, $id)
    // {
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("id", $id);
    //     return $this->db->delete($this->tbl);
    // }

}
