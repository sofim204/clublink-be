<?php
class I_Group_Post_Comment_Attachment_Model extends JI_Model{
	var $tbl = 'i_group_post_comment_attachment';
	var $tbl_as = 'igpca';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	public function getLastId($nation_code,$discussion_id, $jenis){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("i_group_post_comment_id",$discussion_id);
		$this->db->where("jenis",$jenis);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function getByDiscussionId($nation_code, $discussion_id){
		$this->db->select_as("*,$this->tbl_as.id",'id',0);
        $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl_as.location_address").",'')", "location_address", 0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("i_group_post_comment_id",$discussion_id);
		$this->db->where("is_active",1);
		return $this->db->get();
	}

	public function set($dix){
		
        if (isset($dix['location_address'])) {
            if (strlen($dix['location_address'])) {
                $dix['location_address'] = $this->__encrypt($dix['location_address']);
            }
        }
        
		return $this->db->insert($this->tbl, $dix, 0, 0);
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
