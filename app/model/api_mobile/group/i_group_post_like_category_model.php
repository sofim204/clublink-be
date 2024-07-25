<?php
class I_Group_Post_Like_Category_Model extends JI_Model
{
    public $tbl = 'i_group_post_like_category';
    public $tbl_as = 'igplc';
    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    // public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="id", $sortDir="ASC", $keyword="")
    // {
    //     $this->db->flushQuery();
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     if (strlen($keyword)>1) {
    //         $this->db->where("utype", $keyword, "OR", "%like%", 1, 0);
    //         $this->db->where("nama", $keyword, "OR", "%like%", 0, 0);
    //         $this->db->where("deskripsi", $keyword, "OR", "%like%", 0, 1);
    //     }
    //     $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
    //     return $this->db->get("object", 0);
    // }

    // public function countAll($nation_code, $keyword="")
    // {
    //     $this->db->flushQuery();
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

    public function getKategori($nation_code)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.image_icon", "image_icon", 0);
        $this->db->select_as("$this->tbl_as.prioritas", "prioritas", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("is_active", '1');
        $this->db->order_by("prioritas", "asc");
        return $this->db->get('object', 0);
    }

    public function getById($nation_code, $id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where_as("id", $this->db->esc($id));
        return $this->db->get_first('', 0);
    }

    // public function getHomepage($nation_code, $kategori_id)
    // {
    //     $this->db->flushQuery();
    //     $this->db->cache_save = 0;
    //     $this->db->select('id');
    //     $this->db->select('nama');
    //     $this->db->select('image_icon');
    //     $this->db->select("image_cover", "image_cover", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("is_active", 1);

    //     //by Donny Dennison - 15 october 2020 16:49
    //     //add automovite product api
    //     if($kategori_id != 0){

    //         $this->db->where("parent_c_community_category_id",$kategori_id);
        
    //     }else{
        
    //         $this->db->where("parent_c_community_category_id",'IS NULL');
        
    //     }

    //     $this->db->order_by("prioritas", "asc");
    //     // $this->db->order_by("nama", "asc");
        
    //     return $this->db->get("object", 0);
    // }
}
