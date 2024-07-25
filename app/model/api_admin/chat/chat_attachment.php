<?php
	/*==========================================================================================================================
	|											Documentation Model Chat Room
	|	
    |   Functions existing : 
	|		1. count_all($nation_code, $__filter=array()) 
	|			$__filter = [(string)keyword, (enum)room_type, (date)from_date, (date)to_date]
	|		2. get_all($nation_code, $page=1, $page_size=10, $sort_col='cdate', $sort_dir='DESC', $__filter=array()) 
	|			$__filter = [(string)keyword, (enum)room_type, (date)from_date, (date)to_date]
	|		3. update((int)$nation_code, (int)$id, (array)$payload) 
	|		4. set((array)$payload) 
	|	
	==========================================================================================================================*/
?>
<?php
	class Chat_Attachment extends SENE_Model {
		/* Global & Constructor */
	  	var $is_cacheable;
		var $tbl_attachment = 'e_chat_attachment';
		var $tbl_chat_room = 'e_chat_room';
		var $tbl_chat = 'e_chat'; 
		var $tbl_user = 'b_user'; 
		var $tbl_order = 'd_order'; 
		var $tbl_order_detail = 'd_order_detail'; 
		var $tbl_product = 'c_produk'; 
		public function __construct(){
			parent::__construct();
			$this->is_cacheable = 0; 
		}
		/* Global & Constructor */

		/* Transaction */
		public function trans_start(){
			$res = $this->db->autocommit(0);
			if($res) return $this->db->begin();
			return false;
		}
		public function trans_commit(){
			return $this->db->commit();
		}
		public function trans_rollback(){
			return $this->db->rollback();
		}
		public function trans_end(){
			return $this->db->autocommit(1);
		}
		/* Transaction */

		/* Join */
		private function __join_chat_attachment(){
			$join_condition = array(
				$this->db->composite_create("$this->tbl_attachment.nation_code","=","$this->tbl_chat.nation_code"),
				$this->db->composite_create("$this->tbl_attachment.e_chat_id","=","$this->tbl_chat.id"),
			);
			return $join_condition;
		}	
		private function __join_attachment_order(){
			$join_condition = array(
				$this->db->composite_create("$this->tbl_attachment.nation_code","=","$this->tbl_order.nation_code"),
				$this->db->composite_create("$this->tbl_attachment.url","=","$this->tbl_order.id"),
				$this->db->composite_create("$this->tbl_attachment.jenis","=","'order'"),
			);
			return $join_condition;
		}	
		private function __join_order_detail(){
			$join_condition = array(
				$this->db->composite_create("$this->tbl_order.nation_code","=","$this->tbl_order_detail.nation_code"),
				$this->db->composite_create("$this->tbl_order.id","=","$this->tbl_order_detail.d_order_id"),
				$this->db->composite_create("$this->tbl_attachment.order_detail_id","=","$this->tbl_order_detail.id"),
			);
			return $join_condition;
		}	
		private function __join_attachment_product(){
			$join_condition = array(
				$this->db->composite_create("$this->tbl_attachment.nation_code","=","$this->tbl_product.nation_code"),
				$this->db->composite_create("$this->tbl_attachment.url","=","$this->tbl_product.id"),
				// $this->db->composite_create("$this->tbl_attachment.jenis","=","'product'"),
			);
			return $join_condition;
		}
		/* Join */

		/* Public Function */
		public function get_last_id($nation_code,$room_id,$chat_id){
			$this->db->select_as("COALESCE(MAX($this->tbl_attachment.id),0)+1", "last_id", 0);
			$this->db->from($this->tbl_attachment, $this->tbl_attachment);
			$this->db->where("nation_code",$nation_code);
			$this->db->where("e_chat_room_id",$room_id);
			$this->db->where("e_chat_id",$chat_id);
			$d = $this->db->get_first('',0);
			if(isset($d->last_id)) return $d->last_id;
			return 0;
		}
		public function count_all($nation_code, $room_id, $chat_id){
        	$this->db->flushQuery();
			$this->db->select_as("COUNT($this->tbl_attachment.b_user_id)", "total", 0);
			$this->db->from($this->tbl_attachment);
			$this->db->join_composite($this->tbl_chat, $this->tbl_chat, $this->__join_chat_attachment(), "inner");
			$this->db->where("$this->tbl_attachment.nation_code",$nation_code);
			$this->db->where("$this->tbl_attachment.e_chat_id",$chat_id);
			$result = $this->db->get_first('object', 0);
			if (isset($result->total)) {
			    return $result->total;
			}
		}
  		public function get_all($nation_code, $room_id, $chat_id){
  			//Filter Variabel : [keyword, room_type, from_date, to_date]
	        /*
	        SELECT
				`attachment`.`id`,
				`attachment`.`nation_code`,
				`attachment`.`e_chat_room_id`,
				`attachment`.`e_chat_id`,
				`attachment`.`jenis`,
				`attachment`.`url`,
				`attachment`.`ukuran`,
				`attachment`.`produk_nama`,
				`attachment`.`produk_harga_jual`,
				`attachment`.`produk_thumb`,
				`attachment`.`order_detail_id`,
				`attachment`.`order_detail_item_id`
			FROM `e_chat_attachment` `attachment`
	        INNER JOIN `e_chat` `chat`
	            ON `chat`.`id`=`attachment`.`e_chat_id`
	        WHERE `chat`.`id` = ".$this->db->esc($chat_id)."
	        ORDER BY `chat`.`cdate` ASC
	        */
        	$this->db->flushQuery();
  			$this->db->select_as("$this->tbl_attachment.e_chat_id", "chat_id", 0);
  			$this->db->select_as("$this->tbl_attachment.id", "id", 0);
  			$this->db->select_as("$this->tbl_attachment.nation_code", "nation_code", 0);
  			$this->db->select_as("$this->tbl_attachment.e_chat_room_id", "room_id", 0);
  			$this->db->select_as("$this->tbl_attachment.jenis", "type", 0);
  			$this->db->select_as("$this->tbl_attachment.url", "url", 0);
  			$this->db->select_as("$this->tbl_attachment.ukuran", "size", 0);
  			$this->db->select_as("$this->tbl_attachment.order_detail_id", "order", 0);
  			$this->db->select_as("$this->tbl_attachment.order_detail_item_id", "product", 0);
  			$this->db->select_as("$this->tbl_attachment.produk_nama", "product_name", 0);
  			$this->db->select_as("$this->tbl_attachment.produk_harga_jual", "price", 0);

  			$this->db->select_as("$this->tbl_order.invoice_code", "invoice_id", 0);
  			$this->db->select_as("$this->tbl_order.order_status", "order_status", 0);
  			$this->db->select_as("$this->tbl_order.payment_status", "payment_status", 0);
  			$this->db->select_as("$this->tbl_order_detail.seller_status", "seller_status", 0);
  			$this->db->select_as("$this->tbl_order_detail.shipment_status", "shipment_status", 0);
  			$this->db->select_as("$this->tbl_order_detail.buyer_confirmed", "buyer_confirmed", 0);
  			$this->db->select_as("$this->tbl_order_detail.delivery_date", "delivery_date", 0);
  			$this->db->select_as("$this->tbl_order_detail.received_date", "received_date", 0);

  			$this->db->select_as("$this->tbl_product.thumb", "thumb", 0);
			$this->db->select_as("''", "type_barter", 0);
			$this->db->from($this->tbl_attachment);
			$this->db->join_composite($this->tbl_order, $this->tbl_order, $this->__join_attachment_order(), "left");
			$this->db->join_composite($this->tbl_order_detail, $this->tbl_order_detail, $this->__join_order_detail(), "left");
			$this->db->join_composite($this->tbl_product, $this->tbl_product, $this->__join_attachment_product(), "left");
			$this->db->where("$this->tbl_attachment.nation_code",$nation_code);
			$this->db->where("$this->tbl_attachment.e_chat_id",$chat_id);
			$this->db->where("$this->tbl_attachment.e_chat_room_id",$room_id);
			$this->db->order_by("$this->tbl_attachment.id", 'ASC');
    		return $this->db->get("object", 0);
		}
		public function update($nation_code, $id, $payload) {
	        if (!is_array($payload)) {
	            return 0;
	        }
	        $this->db->where("nation_code", $nation_code);
	        $this->db->where("id", $id);
	        return $this->db->update($this->tbl_chat_room, $payload, 0);
	    }
		public function set($payload){
			if(!is_array($payload)) return 0;
			return $this->db->insert($this->tbl_attachment,$payload,0,0);
		}
		/* Public Function */

	}
