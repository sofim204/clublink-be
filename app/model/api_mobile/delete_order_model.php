<?php
class Delete_order_Model extends SENE_Model{

	//By Donny Dennison - 22 Juni 2020 - 18:38
	//Requested by Mr Jackie to make function that can delete order from DB
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

	public function deleteOrder($id,$nation_code){
		$this->db->from("d_order");
		$this->db->where("id",$id);
		$this->db->where("nation_code",$nation_code);
		return $this->db->delete("d_order",0);
	}

	public function deleteOrderAlamat($id,$nation_code){
		$this->db->from("d_order_alamat");
		$this->db->where("d_order_id",$id);
		$this->db->where("nation_code",$nation_code);
		return $this->db->delete("d_order_alamat",0);
	}

	public function deleteOrderDetail($id,$nation_code){
		$this->db->from("d_order_detail");
		$this->db->where("d_order_id",$id);
		$this->db->where("nation_code",$nation_code);
		return $this->db->delete("d_order_detail",0);
	}

	public function deleteOrderDetailItem($id,$nation_code){
		$this->db->from("d_order_detail_item");
		$this->db->where("d_order_id",$id);
		$this->db->where("nation_code",$nation_code);
		return $this->db->delete("d_order_detail_item",0);
	}

	public function deleteOrderDetailPickup($id,$nation_code){
		$this->db->from("d_order_detail_pickup");
		$this->db->where("d_order_id",$id);
		$this->db->where("nation_code",$nation_code);
		return $this->db->delete("d_order_detail_pickup",0);
	}

	public function deleteOrderProses($id,$nation_code){
		$this->db->from("d_order_proses");
		$this->db->where("d_order_id",$id);
		$this->db->where("nation_code",$nation_code);
		return $this->db->delete("d_order_proses",0);
	}

	// public function deleteChat($id,$nation_code){
	// 	$this->db->from("e_chat");
	// 	$this->db->where("d_order_id",$id);
	// 	$this->db->where("nation_code",$nation_code);
	// 	return $this->db->delete("e_chat",0);
	// }

	public function deleteChatAttachment($id,$nation_code){
		$this->db->from("e_chat_attachment");
		$this->db->where("jenis",'order');
		$this->db->where("url",$id);
		$this->db->where("nation_code",$nation_code);
		return $this->db->delete("e_chat_attachment",0);
	}

	// public function deleteChatParticipant($id,$nation_code){
	// 	$this->db->from("e_chat_participant");
	// 	$this->db->where("d_order_id",$id);
	// 	$this->db->where("nation_code",$nation_code);
	// 	return $this->db->delete("e_chat_participant",0);
	// }

	public function deleteComplain($id,$nation_code){
		$this->db->from("e_complain");
		$this->db->where("d_order_id",$id);
		$this->db->where("nation_code",$nation_code);
		return $this->db->delete("e_complain",0);
	}

	public function deleteRating($id,$nation_code){
		$this->db->from("e_rating");
		$this->db->where("d_order_id",$id);
		$this->db->where("nation_code",$nation_code);
		return $this->db->delete("e_rating",0);
	}

	public function deletePemberitahuan($id,$nation_code){
		$this->db->from("d_pemberitahuan");
		$this->db->where("extras",'id_order":"'.$id.'"',"AND","%like%");
		$this->db->where("nation_code",$nation_code);
		return $this->db->delete("d_pemberitahuan",0);
	}

	public function emptyCustomerPhoneNumber($nation_code){

        $du['telp'] = $this->__encrypt('1');

		$this->db->where("nation_code",$nation_code);
        return $this->db->update('b_user', $du, 0);
	}

}


