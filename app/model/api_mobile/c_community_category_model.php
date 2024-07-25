<?php
class C_Community_Category_Model extends JI_Model
{
    public $tbl = 'c_community_category';
    public $tbl_as = 'ccc';
    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }
    public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="id", $sortDir="ASC", $keyword="")
    {
        $this->db->flushQuery();
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        if (strlen($keyword)>1) {
            $this->db->where("utype", $keyword, "OR", "%like%", 1, 0);
            $this->db->where("nama", $keyword, "OR", "%like%", 0, 0);
            $this->db->where("deskripsi", $keyword, "OR", "%like%", 0, 1);
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get("object", 0);
    }
    public function countAll($nation_code, $keyword="")
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        if (strlen($keyword)>1) {
            $this->db->where("utype", $keyword, "OR", "%like%", 1, 0);
            $this->db->where("nama", $keyword, "OR", "%like%", 0, 0);
            $this->db->where("deskripsi", $keyword, "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language
    // public function getById($nation_code, $id)
    public function getById($nation_code, $id, $language_id=1)
    {

        //by Donny Dennison - 15 february 2022 9:50
        //category product and category community have more than 1 language
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.utype", "utype", 0);
        $this->db->select_as("IF($language_id = 4 AND $this->tbl_as.thailand IS NOT NULL AND $this->tbl_as.thailand != '', $this->tbl_as.thailand, IF($language_id = 3 AND $this->tbl_as.korea IS NOT NULL AND $this->tbl_as.korea != '', $this->tbl_as.korea, IF($language_id = 2 AND $this->tbl_as.indonesia IS NOT NULL AND $this->tbl_as.indonesia != '', $this->tbl_as.indonesia, $this->tbl_as.nama)))", "nama");
        $this->db->select_as("IF($language_id = 4 AND $this->tbl_as.deskripsi_thailand IS NOT NULL AND $this->tbl_as.deskripsi_thailand != '', $this->tbl_as.deskripsi_thailand, IF($language_id = 3 AND $this->tbl_as.deskripsi_korea IS NOT NULL AND $this->tbl_as.deskripsi_korea != '', $this->tbl_as.deskripsi_korea, IF($language_id = 2 AND $this->tbl_as.deskripsi_indonesia IS NOT NULL AND $this->tbl_as.deskripsi_indonesia != '', $this->tbl_as.deskripsi_indonesia, $this->tbl_as.deskripsi)))", "deskripsi");
        $this->db->select_as("$this->tbl_as.image", "image", 0);
        $this->db->select_as("$this->tbl_as.image_cover", "image_cover", 0);
        $this->db->select_as("$this->tbl_as.image_icon", "image_icon", 0);
        $this->db->select_as("$this->tbl_as.prioritas", "prioritas", 0);
        $this->db->select_as("$this->tbl_as.is_visible", "is_visible", 0);
        $this->db->select_as("$this->tbl_as.is_active", "is_active", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        return $this->db->get_first('', 0);
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
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function del($nation_code, $id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->delete($this->tbl);
    }
    public function getKategori($nation_code)
    {
        $this->db->select("id")
                         ->select("utype")
                         ->select("nama")
                         ->select("slug")
                         ->select("image")
                         ->select("image_icon")
                         ->select("image_cover")
             ->select("is_active")
                         ->select("is_visible")
                         ->select_as("COALESCE(b_kategori_id,'-')", 'b_kategori_id', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("is_active", '1');
        $this->db->where("utype", 'kategori', "OR", "like", 1, 0);
        $this->db->where("utype", 'kategori_sub', "OR", "like", 0, 0);
        $this->db->where("utype", 'kategori_sub_sub', "OR", "like", 0, 1);
        $this->db->order_by("utype", "asc");
        $this->db->limit(100);
        return $this->db->get('object', 0);
    }
    public function getParentKategori($nation_code)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->select()->from($this->tbl, $this->tbl_as)->where_as("b_kategori_id", "is null");
        $this->db->where("utype", 'kategori', "OR", "like", 1, 0);
        $this->db->where("utype", 'kategori_sub', "OR", "like", 0, 0);
        $this->db->where("utype", 'kategori_sub_sub', "OR", "like", 0, 1);
        $this->db->order_by("prioritas", "asc")->order_by("nama", "asc");
        $this->db->limit(100);
        return $this->db->get('object', 0);
    }
    public function getSubKategori($nation_code, $b_kategori_id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->select()->from($this->tbl, $this->tbl_as)->where_as("b_kategori_id", $b_kategori_id);
        $this->db->where("utype", 'kategori_sub', "AND", "like", 0, 0);
        $this->db->limit(100);
        return $this->db->get('', 0);
    }

    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language

    //by Donny Dennison - 15 october 2020 16:49
    //add automovite product api
    // public function getHomepage($nation_code)
    // public function getHomepage($nation_code, $kategori_id)
    public function getHomepage($nation_code, $kategori_id, $language_id=1)
    {
        $this->db->flushQuery();
        $this->db->cache_save = 0;
        $this->db->select('id');

        //by Donny Dennison - 15 february 2022 9:50
        //category product and category community have more than 1 language
        // $this->db->select('nama');
        $this->db->select_as("IF($language_id = 4 AND $this->tbl_as.thailand IS NOT NULL AND $this->tbl_as.thailand != '', $this->tbl_as.thailand, IF($language_id = 3 AND $this->tbl_as.korea IS NOT NULL AND $this->tbl_as.korea != '', $this->tbl_as.korea, IF($language_id = 2 AND $this->tbl_as.indonesia IS NOT NULL AND $this->tbl_as.indonesia != '', $this->tbl_as.indonesia, $this->tbl_as.nama)))", "nama");

        $this->db->select('image_icon');
        $this->db->select("image_cover", "image_cover", 0);

        if($language_id == 2){
            $this->db->select_as("prioritas_indonesia", "prioritas");
        }else{
            $this->db->select("prioritas", "prioritas");
        }

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("is_active", 1);

        //by Donny Dennison - 15 october 2020 16:49
        //add automovite product api
        if($kategori_id != 0){

            $this->db->where("parent_c_community_category_id",$kategori_id);
        
        }else{
        
            $this->db->where("parent_c_community_category_id",'IS NULL');
        
        }

        if($language_id == 2){
            $this->db->order_by("prioritas_indonesia", "asc");
        }else{
            $this->db->order_by("prioritas", "asc");
        }

        // $this->db->order_by("nama", "asc");
        
        return $this->db->get("object", 0);
    }
}
