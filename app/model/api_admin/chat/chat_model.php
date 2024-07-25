<?php
	/*==========================================================================================================================
	|											Documentation Model Chat
	|	
	|	Functions existing : 
	|		1. count_all($nation_code) 
	|		2. get_all($nation_code, $page=1, $page_size=10, $sort_col='cdate', $sort_dir='DESC', $__filter=array()) 
	|			$__filter = [(string)keyword, (enum)room_type, (date)from_date, (date)to_date]
	|		3. update($nation_code, $id, $payload) 
	|	
	==========================================================================================================================*/
?>
<?php
	class Chat_Model extends SENE_Model {
		/* Global & Constructor */
	  	var $is_cacheable;
		var $tbl_chat_room = 'e_chat_room';
		var $tbl_chat = 'e_chat'; 
		var $tbl_order = 'd_order';
		var $tbl_attachment = 'e_chat_attachment';
		var $tbl_admin = 'a_pengguna';
		var $tbl_user = 'b_user'; 
		var $tbl_user_alias = 'user'; 
		var $tbl_user2_alias = 'user2'; 
		var $tbl_user3_alias = 'user3';
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
		private function __join_chat_user(){
			$join_condition = array(
				$this->db->composite_create("$this->tbl_chat.nation_code","=","$this->tbl_user.nation_code"),
				$this->db->composite_create("$this->tbl_chat.b_user_id","=","$this->tbl_user.id"),
			);
			return $join_condition;
		}	
		private function __join_chat_chat_room(){
			$join_condition = array(
				$this->db->composite_create("$this->tbl_chat.nation_code","=","$this->tbl_chat_room.nation_code"),
				$this->db->composite_create("$this->tbl_chat.e_chat_room_id","=","$this->tbl_chat_room.id"),
			);
			return $join_condition;
		}	
		private function __join_chat_attachment(){
			$join_condition = array(
				$this->db->composite_create("$this->tbl_attachment.nation_code","=","$this->tbl_chat.nation_code"),
				$this->db->composite_create("$this->tbl_attachment.e_chat_id","=","$this->tbl_chat.id"),
			);
			return $join_condition;
		}	
		/* Join */

		/* Public Function */
		public function count_all($nation_code, $room_id){
        	$this->db->flushQuery();
			$this->db->select_as("COUNT($this->tbl_chat.id)", "total", 0);
			$this->db->from($this->tbl_chat);
			$this->db->join_composite($this->tbl_user, $this->tbl_user, $this->__join_chat_user(), "left");
			$this->db->where("$this->tbl_chat.nation_code",$nation_code);
			$this->db->where("$this->tbl_chat.e_chat_room_id",$room_id);
			$result = $this->db->get_first('object', 0);
			if (isset($result->total)) {
			    return $result->total;
			}
		}
  		public function get_all($nation_code, $room_id){
  			//Filter Variabel : [keyword, room_type, from_date, to_date]
	        /*
	        SELECT
	            `chat`.`id`,
	            `chat`.`nation_code`,
	            `chat`.`cdate`,
	            `chat`.`ldate`,
	            `chat`.`e_chat_room_id`,
	            `chat`.`b_user_id`,
	            COALESCE(AES_DECRYPT(`user`.fnama, '".$this->db->enckey."'), 'Admin') AS 'user_fname',
	            `chat`.`a_pengguna_id`,
	            `chat`.`type`,
	            `chat`.`message`
	        FROM `e_chat` `chat`
	        INNER JOIN `e_chat_room` `chat_room`
	            ON `chat_room`.`id`=`chat`.`e_chat_room_id`
	            AND `chat_room`.`nation_code`=".$this->db->esc($nation_code)."
	        LEFT JOIN `b_user` `user`
	            ON `user`.`id`=`chat`.`b_user_id`
	        WHERE `chat_room`.`id` = ".$this->db->esc($room_id)."
	        ORDER BY `chat`.`cdate` ASC
	        */
        	$this->db->flushQuery();
  			$this->db->select_as("$this->tbl_chat.id", "id", 0);
  			$this->db->select_as("$this->tbl_chat.nation_code", "nation_code", 0);
  			$this->db->select_as("$this->tbl_chat.cdate", "cdate", 0);
  			$this->db->select_as("$this->tbl_chat.ldate", "ldate", 0);
  			$this->db->select_as("$this->tbl_chat.e_chat_room_id", "e_chat_room_id", 0);
  			$this->db->select_as("$this->tbl_chat.b_user_id", "b_user_id", 0);
		    $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl_user.fnama").", 'Admin')", "starter_fname", 0);
  			$this->db->select_as("$this->tbl_chat.a_pengguna_id", "a_pengguna_id", 0);
  			$this->db->select_as("$this->tbl_chat.type", "type", 0);
  			$this->db->select_as("$this->tbl_chat.message", "message", 0);
  			$this->db->select_as("$this->tbl_chat_room.chat_type", "room_type", 0);
			$this->db->from($this->tbl_chat);
			$this->db->join_composite($this->tbl_chat_room, $this->tbl_chat_room, $this->__join_chat_chat_room(), "inner");
			$this->db->join_composite($this->tbl_user, $this->tbl_user, $this->__join_chat_user(), "left");
			$this->db->where("$this->tbl_chat.nation_code",$nation_code);
			$this->db->where("$this->tbl_chat.e_chat_room_id",$room_id);
			$this->db->order_by("$this->tbl_chat.cdate");
    		return $this->db->get("object", 0);
		}
	    public function getAdminChatRoom($nation_code,$user_id) {
	        /*
	        SELECT `chat_room`.`id`
	        FROM `e_chat_room` `chat_room`
	        INNER JOIN `e_chat_participant` `participant`
	            ON `participant`.`e_chat_room_id`=`chat_room`.`id`
	        WHERE `chat_room`.`chat_type`='admin'
	             AND `participant`.`b_user_id`=".$this->db->esc($user_id)."
	        GROUP BY `chat_room`.`id`
	        */

	        $sql = "SELECT `chat_room`.`id` FROM `e_chat_room` `chat_room` INNER JOIN `e_chat_participant` `participant` ON `participant`.`e_chat_room_id`=`chat_room`.`id` WHERE `chat_room`.`chat_type`='admin' AND `participant`.`b_user_id`=".$this->db->esc($user_id)." GROUP BY `chat_room`.`id`"; $data = $this->db->query($sql);
	        if($data) return $data[0]->id;
	        return false;
	    }

	    public function get_last_id($nation_code,$room_id) {
	        $this->db->select_as("MAX($this->tbl_chat.id)+1", "last_id", 0);
	        $this->db->from($this->tbl_chat, $this->tbl_chat);
	        $this->db->where("nation_code", $nation_code);
	        $this->db->where("e_chat_room_id", $room_id);
	        $d = $this->db->get_first('', 0);
	        if (isset($d->last_id)) {
	            return $d->last_id;
	        }
	        return 0;
	    }

	    public function createChatRoom($nation_code, $user_id, $room_id) {
	        // $sql = "SELECT (MAX(`chat_room`.`id`)+1) AS `id` FROM `e_chat_room` `chat_room`"; 
	        // $data = $this->db->query($sql);
	        // $room_id = $data[0]->id;

	        /*
	        INSERT INTO `e_chat_room` (
	            `id`,
	            `nation_code`,
	            `b_user_id_starter`,
	            `cdate`,
	            `chat_type`
	        )
	        VALUES(
	            ".$this->db->esc($room_id).",
	            ".$this->db->esc($nation_code).",
	            ".$this->db->esc($user_id).",
	            NOW(),
	            'admin'
	        );
	        */
	        /*
	        INSERT INTO `e_chat_participant` (
	            `nation_code`,
	            `e_chat_room_id`,
	            `b_user_id`
	        )
	        VALUES(
	            ".$this->db->esc($nation_code).",
	            ".$this->db->esc($room_id).",
	            ".$this->db->esc($user_id)."
	        );    
	        */

	        $sql = "INSERT INTO `e_chat_room` (`id`, `nation_code`, `b_user_id_starter`, `cdate`, `chat_type` ) VALUES(".$this->db->esc($room_id).", ".$this->db->esc($nation_code).", ".$this->db->esc($user_id).", NOW(), 'admin');"; 
	        $res = $this->db->exec($sql);

	        $sql = "INSERT INTO `e_chat_participant` (`nation_code`, `e_chat_room_id`, `b_user_id`, `cdate`, `is_read`) VALUES(".$this->db->esc( $nation_code).", ".$this->db->esc($room_id).", ".$this->db->esc($user_id).", NOW(), 1 ); "; 
	        $res = $this->db->exec($sql);
	        return $room_id;
	    }

	    public function set($payload)
	    {
	        if (!is_array($payload)) {
	            return 0;
	        }
	        return $this->db->insert($this->tbl_chat, $payload, 0, 0);
	    }

		public function update($nation_code, $id, $payload) {
	        if (!is_array($payload)) {
	            return 0;
	        }
	        $this->db->where("nation_code", $nation_code);
	        $this->db->where("id", $id);
	        return $this->db->update($this->tbl_chat_room, $payload, 0);
	    }
		/* Public Function */

	}
