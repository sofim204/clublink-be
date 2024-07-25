<?php
class I_Group_Attachment_Model extends JI_Model{

	var $tbl = 'i_group_attachment';
	var $tbl_as = 'iga';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	public function set($dix){
        if (isset($dix['location_address'])) {
            if (strlen($dix['location_address'])) {
                $dix['location_address'] = $this->__encrypt($dix['location_address']);
            }
        }
		return $this->db->insert($this->tbl, $dix, 0, 0);
	}

	public function update($nation_code, $id, $du){
		if(!is_array($du)) return 0;
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->update($this->tbl,$du,0);
	}

	public function delByGroupIdJenis($nation_code,$i_group_id, $jenis){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("i_group_id",$i_group_id);
		$this->db->where("jenis",$jenis);
		return $this->db->delete($this->tbl);
	}

	public function getByGroupId($nation_code, $i_group_id, $getType="all", $type=""){
		$this->db->select_as("$this->tbl_as.id", 'id', 0);
		$this->db->select_as("$this->tbl_as.i_group_id", 'i_group_id', 0);
		$this->db->select_as("$this->tbl_as.b_user_id", 'b_user_id', 0);
		$this->db->select_as("$this->tbl_as.jenis", 'jenis', 0);
		if($type == "image") {
			$this->db->select_as("$this->tbl_as.url", 'url', 0);
			$this->db->select_as("$this->tbl_as.url_thumb", 'url_thumb', 0);
		} else if($type == "location") {
			$this->db->select_as("$this->tbl_as.location_nama", 'location_nama', 0);
			$this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl_as.location_address").",'')", "location_address", 0);
			$this->db->select_as("$this->tbl_as.location_place_id", 'location_place_id', 0);
			$this->db->select_as("$this->tbl_as.location_latitude", 'latitude', 0);
			$this->db->select_as("$this->tbl_as.location_longitude", 'longitude', 0);
		}
		$this->db->select_as("$this->tbl_as.cdate", 'cdate', 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code", $nation_code);
		$this->db->where("i_group_id", $i_group_id);
		$this->db->where("is_active", 1);

		if($type != ""){
			$this->db->where("jenis", $type);
		}

		$this->db->order_by("cdate", "ASC");

		if($getType == "first"){
			return $this->db->get_first();
		}else{
			return $this->db->get();
		}
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
}
