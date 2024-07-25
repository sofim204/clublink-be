<?php
class G_Ip_Whitelist_Model extends JI_Model
{
    public $tbl = 'g_ip_whitelist';
    public $tbl_as = 'giw';

    public function __construct() {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    // private function __joinTbl2()
    // {
    //     $composites = array();
    //     $composites[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
    //     $composites[] = $this->db->composite_create("$this->tbl_as.c_community_id", "=", "$this->tbl2_as.id");
    //     return $composites;
    // }

    public function getTblAs()
    {
        return $this->tbl_as;
    }

    // public function getLastId($nation_code, $b_user_alamat_location_postal_district='00')
    // {
    //     $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where("b_user_alamat_location_postal_district", $b_user_alamat_location_postal_district);
    //     $d = $this->db->get_first('', 0);
    //     if (isset($d->last_id)) {
    //         return (int) $d->last_id;
    //     }
    //     return 0;
    // }

    // public function set($du)
    // {
    //     if (!is_array($du)) {
    //         return 0;
    //     }
    //     return $this->db->insert($this->tbl, $du, 0, 0);
    // }

    // // public function update($nation_code, $id, $b_user_alamat_location_postal_district='00', $du) {
    // //     $this->db->where_as('nation_code', $this->db->esc($nation_code));
    // //     $this->db->where('id', $id);
    // //     $this->db->where("b_user_alamat_location_postal_district", $b_user_alamat_location_postal_district);
    // //     return $this->db->update($this->tbl, $du, 0);
    // // }

    // // public function countAll($nation_code, $b_user_id="")
    // // {
    // //     $this->db->exec("SET NAMES 'UTF8MB4'");
    // //     $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
    // //     $this->db->from($this->tbl, $this->tbl_as);
    // //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    // //     $this->db->where_as("$this->tbl_as.is_active", '1');

    // //     $d = $this->db->get_first('object', 0);
    // //     if (isset($d->total)) {
    // //         return $d->total;
    // //     }
    // //     return 0;
    // // }

    // public function getAll($variable_name="",$type="mobile")
    // {
    //     $this->db->select_as("*,$this->tbl_as.id", "id", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
        
    //     if(strlen($variable_name) > 0){
    //         $this->db->where_as("$this->tbl_as.variable_name", $this->db->esc($variable_name));
    //     }
        
    //     return $this->db->get('object', 0);
    // }

    // public function getByVariableName($variable_name="",$type="mobile")
    // {
    //     $this->db->select_as("*,$this->tbl_as.id", "id", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
        
    //     $this->db->where_as("$this->tbl_as.variable_name", $this->db->esc($variable_name));
        
    //     return $this->db->get_first('object', 0);
    // }

    public function check($nation_code, $ip_address)
    {
        $this->db->select_as("*,$this->tbl_as.id", "id", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

        $this->db->where_as("$this->tbl_as.ip_address", $this->db->esc($ip_address));

        return $this->db->get_first('object', 0);
    }

}