<?php
class G_Highlight_Community_Model extends JI_Model
{
    public $tbl = 'g_highlight_community';
    public $tbl_as = 'ghc';
    public $tbl2 = 'c_community';
    public $tbl2_as = 'cc';
    public $tbl3 = 'b_user';
    public $tbl3_as = 'bu';

    public function __construct() {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl_as.c_community_id", "=", "$this->tbl2_as.id");
        return $composites;
    }

    private function __joinTbl3()
    {
        $composites = array();
        $composites[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $composites[] = $this->db->composite_create("$this->tbl2_as.b_user_id", "=", "$this->tbl3_as.id");
        return $composites;
    }

    public function getTblAs()
    {
        return $this->tbl_as;
    }

    // public function getTbl2As()
    // {
    //     return $this->tbl2_as;
    // }

    public function getLastId($nation_code, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta")
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_alamat_location_kelurahan", $this->db->esc($kelurahan));
        $this->db->where_as("$this->tbl_as.b_user_alamat_location_kecamatan", $this->db->esc($kecamatan));
        $this->db->where_as("$this->tbl_as.b_user_alamat_location_kabkota", $this->db->esc($kabkota));
        $this->db->where_as("$this->tbl_as.b_user_alamat_location_provinsi", $this->db->esc($provinsi));
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return (int) $d->last_id;
        }
        return 0;
    }

    public function set($du)
    {
        if (!is_array($du)) {
            return 0;
        }
        return $this->db->insert($this->tbl, $du, 0, 0);
    }

    // public function update($nation_code, $id, $b_user_alamat_location_postal_district='00', $du) {
    //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    //     $this->db->where('id', $id);
    //     $this->db->where("b_user_alamat_location_postal_district", $b_user_alamat_location_postal_district);
    //     return $this->db->update($this->tbl, $du, 0);
    // }

    public function delete($nation_code){
        $this->db->where("nation_code",$nation_code);
        $this->db->where("is_active", 0);
        return $this->db->delete($this->tbl);
    }

    public function updateByCommunityId($nation_code, $community_id, $du) {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("c_community_id", $community_id);
        $this->db->where("is_active", 1);
        return $this->db->update($this->tbl, $du, 0);
    }

    public function updateByPriorityDesc($nation_code, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta", $limit) {
        return $this->db->exec("UPDATE `$this->tbl` SET is_active = 0
            WHERE nation_code = '$nation_code' AND b_user_alamat_location_kelurahan = '$kelurahan'  AND b_user_alamat_location_kecamatan = '$kecamatan'  AND b_user_alamat_location_kabkota = '$kabkota'  AND b_user_alamat_location_provinsi = '$provinsi' AND is_active = 1  ORDER BY priority desc LIMIT $limit;");
    }

    public function updatePriority($nation_code, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta", $operator, $total)
    {
        return $this->db->exec("UPDATE `$this->tbl` SET priority = priority $operator $total
            WHERE nation_code = '$nation_code' AND b_user_alamat_location_kelurahan = '$kelurahan'  AND b_user_alamat_location_kecamatan = '$kecamatan'  AND b_user_alamat_location_kabkota = '$kabkota'  AND b_user_alamat_location_provinsi = '$provinsi' AND is_active = 1;");
    }

    public function inactiveExpired() {
        $this->db->where("is_active", 1);
        $this->db->where("end_date", date('Y-m-d'),'AND','<=');
        return $this->db->update($this->tbl, array("is_active"=>0), 0);
    }

    // public function countAll($nation_code, $b_user_id="")
    // {
    //     $this->db->exec("SET NAMES 'UTF8MB4'");
    //     $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.is_active", '1');

    //     $d = $this->db->get_first('object', 0);
    //     if (isset($d->total)) {
    //         return $d->total;
    //     }
    //     return 0;
    // }

    // public function getAll($nation_code, $b_user_id="")
    // {
    //     $this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'");
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.c_community_id", "c_community_id", 0);
    //     $this->db->select_as("$this->tbl2_as.title", "title", 0);
    //     $this->db->select_as("$this->tbl2_as.deskripsi", "deskripsi", 0);
    //     $this->db->select_as('COALESCE('.$this->__decrypt("$this->tbl2_as.alamat2").',"")', "alamat2", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));;
    //     $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1')); 
        
    //     $this->db->order_by("$this->tbl_as.priority", 'ASC');
        
    //     return $this->db->get('object', 0);
    // }

    public function countAllByLocation($nation_code, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta")
    {
        $this->db->select_as("COUNT($this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.b_user_alamat_location_kelurahan", $this->db->esc($kelurahan));
        $this->db->where_as("$this->tbl_as.b_user_alamat_location_kecamatan", $this->db->esc($kecamatan));
        $this->db->where_as("$this->tbl_as.b_user_alamat_location_kabkota", $this->db->esc($kabkota));
        $this->db->where_as("$this->tbl_as.b_user_alamat_location_provinsi", $this->db->esc($provinsi));
        $this->db->where_as("$this->tbl_as.start_date", $this->db->esc(date('Y-m-d')),'AND','<=');
        $this->db->where_as("$this->tbl_as.end_date", $this->db->esc(date('Y-m-d')),'AND','>=');

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

    //by Donny Dennison - 29 july 2022 13:22
    //new feature, block community post or account
    // public function getAllByLocation($nation_code, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta")
    public function getAllByLocation($nation_code, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta", $blockDataCommunity, $blockDataAccount, $blockDataAccountReverse)
    {
        $this->db->select_as("$this->tbl_as.c_community_id", "c_community_id", 0);
        $this->db->select_as("$this->tbl2_as.title", "title", 0);
        $this->db->select_as("$this->tbl2_as.c_community_category_id", "c_community_category_id", 0);
        $this->db->select_as("$this->tbl2_as.b_user_id", "b_user_id_starter", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.b_user_alamat_location_kelurahan", $this->db->esc($kelurahan));
        $this->db->where_as("$this->tbl_as.b_user_alamat_location_kecamatan", $this->db->esc($kecamatan));
        $this->db->where_as("$this->tbl_as.b_user_alamat_location_kabkota", $this->db->esc($kabkota));
        $this->db->where_as("$this->tbl_as.b_user_alamat_location_provinsi", $this->db->esc($provinsi));
        $this->db->where_as("$this->tbl_as.start_date", $this->db->esc(date('Y-m-d')),'AND','<=');
        $this->db->where_as("$this->tbl_as.end_date", $this->db->esc(date('Y-m-d')),'AND','>=');

        //START by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account
        if(count($blockDataCommunity)>0){

            $listArray = array();
            foreach($blockDataCommunity AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataCommunity, $block);

            $this->db->where_in("$this->tbl_as.c_community_id", $listArray, 1);
            unset($listArray);

        }

        if(count($blockDataAccount)>0){

            $listArray = array();
            foreach($blockDataAccount AS $block){

                $listArray[] = $block->custom_id;

            }
            unset($blockDataAccount, $block);

            $this->db->where_in("$this->tbl3_as.id", $listArray, 1);
            unset($listArray);

        }

        if(count($blockDataAccountReverse)>0){

            $listArray = array();
            foreach($blockDataAccountReverse AS $block){

                $listArray[] = $block->b_user_id;

            }
            unset($blockDataAccountReverse, $block);

            $this->db->where_in("$this->tbl3_as.id", $listArray, 1);
            unset($listArray);

        }
        //END by Donny Dennison - 29 july 2022 13:22
        //new feature, block community post or account

        $this->db->order_by("$this->tbl_as.priority", 'ASC');

        $this->db->limit(0, 10);
        
        return $this->db->get('object', 0);
    }


    public function getAllByLocationGroupBy($nation_code)
    {
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_kelurahan", "kelurahan", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_kecamatan", "kecamatan", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_kabkota", "kabkota", 0);
        $this->db->select_as("$this->tbl_as.b_user_alamat_location_provinsi", "provinsi", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');
        
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc('1'));
        $this->db->where_as("$this->tbl_as.start_date", $this->db->esc(date('Y-m-d')),'AND','<=');
        $this->db->where_as("$this->tbl_as.end_date", $this->db->esc(date('Y-m-d')),'AND','>=');
        
        $this->db->group_by("CONCAT($this->tbl_as.b_user_alamat_location_kelurahan,'-',$this->tbl_as.b_user_alamat_location_kecamatan,'-',$this->tbl_as.b_user_alamat_location_kabkota,'-',$this->tbl_as.b_user_alamat_location_provinsi)");
        
        return $this->db->get('object', 0);
    }

}