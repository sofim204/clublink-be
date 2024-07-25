<?php
class Delete_Ip_Recommender_Model extends SENE_Model{

	//By Yopie Hidayat - 09 Mei 2023 - 14:50
	//Requested by Mr Jackie to make function that can delete ip recommender from DB

    // public $tbl = 'b_user';
    // public $tbl_as = 'bu';

	public function __construct(){
		parent::__construct();
	}


    public function trans_start()
    {
        $r = $this->db->autocommit(0);
        if ($r) {
            return $this->db->begin();
        }
        return false;
    }
    public function trans_commit()
    {
        return $this->db->commit();
    }
    public function trans_rollback()
    {
        return $this->db->rollback();
    }
    public function trans_end()
    {
        return $this->db->autocommit(1);
    }

    public function getRecommender($year, $month){
        $this->db->select_as("id", "id", 0);
        $this->db->select_as("ip_address", "ip_address", 0);
        $this->db->select_as("total_recruited", "total_recruited", 0);
        $this->db->select_as("is_permanent_inactive", "is_permanent_inactive", 0);
        $this->db->select_as("permanent_inactive_by", "permanent_inactive_by", 0);
        $this->db->select_as("cdate", "cdate", 0);
        $this->db->from("b_user");
        $this->db->where_as("is_permanent_inactive", $this->db->esc(0));
		$this->db->where("permanent_inactive_by", 'admin');
        $this->db->where_as("ip_address", $this->db->esc(""), "AND", "<>");
        $this->db->where_as("YEAR(cdate)", $this->db->esc($year));
        $this->db->where_as("MONTH(cdate)", $this->db->esc($month));
        $this->db->order_by("cdate","desc");
        // $this->db->limit(50);
		return $this->db->get('object',0);
    }

    public function getUserByRecommenderID($recommender_id = ""){
        $this->db->select_as("b_user_id_recruiter", "b_user_id_recruiter", 0);
        $this->db->select_as("id", "id", 0);
        $this->db->select_as("ip_address", "ip_address", 0);
        $this->db->select_as("cdate", "cdate", 0);
        $this->db->from("b_user");
		$this->db->where("b_user_id_recruiter", $recommender_id);
        $this->db->order_by("cdate","desc");
		return $this->db->get('object',0);
    }

    public function deleteIpByID($id = ""){
        $this->db->where("id",$id);
        return $this->db->update("b_user",array("ip_address"=>""));
    }

    public function deleteIpByRecommenderID($b_user_id_recruiter = ""){
        $this->db->where("b_user_id_recruiter",$b_user_id_recruiter);
        return $this->db->update("b_user",array("ip_address"=>""));
    }

}