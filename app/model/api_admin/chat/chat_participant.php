<?php
	/*==========================================================================================================================
	|											Documentation Model Chat Room
	|	
	|	Hooks added : 
    |       1. get_table_name((string)tbl_name)
	|	
    |   Functions existing : 
	|		1. count_all($nation_code, $__filter=array()) 
	|			$__filter = [(string)keyword, (enum)room_type, (date)from_date, (date)to_date]
	|		2. get_all($nation_code, $page=1, $page_size=10, $sort_col='cdate', $sort_dir='DESC', $__filter=array()) 
	|			$__filter = [(string)keyword, (enum)room_type, (date)from_date, (date)to_date]
	|		3. update($nation_code, $id, $payload) 
	|	
	==========================================================================================================================*/
?>
<?php
	class Chat_Participant extends SENE_Model {
		/* Global & Constructor */
	  	var $is_cacheable;
		var $tbl_participant = 'e_chat_participant';
		var $tbl_participant_as = 'ecp';
		var $tbl_participant_2 = 'e_chat_participant';
		var $tbl_participant_2_as = 'ecp2';
		var $tbl_chat_room = 'e_chat_room';
		var $tbl_chat_room_as = 'ecr';
		var $tbl_chat = 'e_chat'; 
		var $tbl_chat_as = 'ec'; 
		var $tbl_user = 'b_user'; 
		var $tbl_user_as = 'bu'; 
		var $tbl_user_2 = 'b_user'; 
		var $tbl_user_2_as = 'bu2'; 
		
		public function __construct(){
			parent::__construct();
			$this->is_cacheable = 0; 
		}
		/* Global & Constructor */

		/* Public Hooks */
		public function get_table_name($tbl_name) {
			return $this->{$tbl_name};
		}
		/* Public Hooks */

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
		private function __join_participant_user(){
			$join_condition = array(
				$this->db->composite_create("$this->tbl_participant.nation_code","=","$this->tbl_user.nation_code"),
				$this->db->composite_create("$this->tbl_participant.b_user_id","=","$this->tbl_user.id"),
			);
			return $join_condition;
		}	

		// START by Muhammad Sofi 7 January 2022 16:10 | restore button Open Chat
		private function __joinTbl2(){
			$cps = array();
			$cps[] = $this->db->composite_create("$this->tbl_participant_2_as.nation_code","=","$this->tbl_chat_room_as.nation_code");
			$cps[] = $this->db->composite_create("$this->tbl_participant_2_as.e_chat_room_id","=","$this->tbl_chat_room_as.id ");
			return $cps;
		}
		
		private function __joinTbl5(){
			$cps = array();
			$cps[] = $this->db->composite_create("$this->tbl_participant_as.nation_code","=","$this->tbl_user_as.nation_code");
			$cps[] = $this->db->composite_create("$this->tbl_participant_as.b_user_id","=","$this->tbl_user_as.id");
			return $cps;
		}
	
		private function __joinTbl7(){
			$cps = array();
			$cps[] = $this->db->composite_create("$this->tbl_participant_as.nation_code","=","$this->tbl_chat_room_as.nation_code");
			$cps[] = $this->db->composite_create("$this->tbl_participant_as.e_chat_room_id","=","$this->tbl_chat_room_as.id ");
			return $cps;
		}
	
		private function __joinTbl10(){
			$cps = array();
			$cps[] = $this->db->composite_create("$this->tbl_participant_2_as.nation_code","=","$this->tbl_user_2_as.nation_code");
			$cps[] = $this->db->composite_create("$this->tbl_participant_2_as.b_user_id","=","$this->tbl_user_2_as.id");
			return $cps;
		}
		// END by Muhammad Sofi 7 January 2022 16:10 | restore button Open Chat

		/* Join */

		/* Public Function */
		public function count_all($nation_code, $room_id){
        	$this->db->flushQuery();
			$this->db->select_as("COUNT($this->tbl_participant.b_user_id)", "total", 0);
			$this->db->from($this->tbl_participant);
			$this->db->join_composite($this->tbl_user, $this->tbl_user, $this->__join_participant_user(), "inner");
			$this->db->where("$this->tbl_participant.nation_code",$nation_code);
			$this->db->where("$this->tbl_participant.e_chat_room_id",$room_id);
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
  			$this->db->select_as("$this->tbl_participant.nation_code", "nation_code", 0);
  			$this->db->select_as("$this->tbl_participant.cdate", "cdate", 0);
  			$this->db->select_as("$this->tbl_participant.ldate", "ldate", 0);
  			$this->db->select_as("$this->tbl_participant.e_chat_room_id", "e_chat_room_id", 0);
  			$this->db->select_as("$this->tbl_participant.b_user_id", "b_user_id", 0);
		    $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl_user.fnama").", 'Admin')", "user_fname", 0);
			$this->db->from($this->tbl_participant);


			$this->db->join_composite($this->tbl_user, $this->tbl_user, $this->__join_participant_user(), "left");
			$this->db->where("$this->tbl_participant.nation_code",$nation_code);
			$this->db->where("$this->tbl_participant.e_chat_room_id",$room_id);
			$this->db->order_by("$this->tbl_participant.cdate");
    		return $this->db->get("object", 0);
		}
  		public function get_with_admin($nation_code, $room_id){
  			//Filter Variabel : [keyword, room_type, from_date, to_date]
	        /*
	        SELECT
			    `participant`.`nation_code`,
			    `participant`.`cdate`,
			    `participant`.`ldate`,
			    `participant`.`e_chat_room_id`,
			    `participant`.`b_user_id`,
			    (SELECT `chat_room`.`id` FROM `e_chat_room` `chat_room` INNER JOIN `e_chat_participant` `room_participant` ON `room_participant`.`e_chat_room_id`=`chat_room`.`id` WHERE `chat_room`.`chat_type`='admin'AND `room_participant`.`b_user_id`=`participant`.`b_user_id` GROUP BY `chat_room`.`id` LIMIT 1 ) AS `room_admin`, 
			    COALESCE(AES_DECRYPT(`user`.fnama, '".$this->db->enckey."'), 'Admin') AS 'user_fname'
			FROM `e_chat_participant` `participant`
			LEFT JOIN `b_user` `user`
			    ON `user`.`id`=`participant`.`b_user_id`
			WHERE `participant`.`e_chat_room_id` = "$room_id"
			ORDER BY `participant`.`cdate` ASC
	        */
        	$this->db->flushQuery();
  			$this->db->select_as("$this->tbl_participant.nation_code", "nation_code", 0);
  			$this->db->select_as("$this->tbl_participant.cdate", "cdate", 0);
  			$this->db->select_as("$this->tbl_participant.ldate", "ldate", 0);
  			$this->db->select_as("$this->tbl_participant.e_chat_room_id", "e_chat_room_id", 0);
  			$this->db->select_as("$this->tbl_participant.b_user_id", "b_user_id", 0);
		    $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl_user.fnama").", 'Admin')", "starter_fname", 0);
  			$this->db->select_as("IFNULL((
  				SELECT `chat_room`.`id`
  				FROM `$this->tbl_chat_room` `chat_room` 
  				INNER JOIN `$this->tbl_participant` `room_participant` 
  					ON `room_participant`.`e_chat_room_id`=`chat_room`.`id` 
  				WHERE `chat_room`.`chat_type`='admin' 
  				AND `room_participant`.`b_user_id`=`$this->tbl_participant`.`b_user_id` 
  				GROUP BY `chat_room`.`id` 
  				LIMIT 1 
  			), '')", "room_admin", 0);
			$this->db->from($this->tbl_participant);
			$this->db->join_composite($this->tbl_user, $this->tbl_user, $this->__join_participant_user(), "left");
			$this->db->where("$this->tbl_participant.nation_code",$nation_code);
			$this->db->where("$this->tbl_participant.e_chat_room_id",$room_id);
			$this->db->group_by("$this->tbl_participant.b_user_id");
			$this->db->order_by("$this->tbl_participant.cdate", "ASC");
    		return $this->db->get("object", 0);
		}

  		public function get_by_id($nation_code,$chat_room_id){
	        /*
	        SELECT
	            `participant`.`nation_code`,
	            `participant`.`e_chat_room_id`,
	            `participant`.`b_user_id`,
	            AES_DECRYPT(`user`.fnama, '".$this->db->enckey."') AS 'user_fname',
	            `participant`.`cdate`,
	            `participant`.`ldate`,
	            `participant`.`last_delete_chat`,
	            `participant`.`is_read`,
	            `participant`.`is_active`
	        FROM `e_chat_participant` `participant`
	        INNER JOIN `b_user` `user`
	            ON `user`.`id`=`participant`.`b_user_id`
	        WHERE `participant`.`nation_code` = ".$this->db->esc($nation_code)."
	            AND `participant`.`e_chat_room_id` = ".$this->db->esc($chat_room_id)."
	        GROUP BY `participant`.`b_user_id`
	        ORDER BY AES_DECRYPT(`user`.fnama, '".$this->db->enckey."') ASC
	        */

  			$this->db->select_as("$this->tbl_participant.nation_code", "nation_code", 0);
		    $this->db->select_as("$this->tbl_participant.e_chat_room_id", "room_id", 0);
		    $this->db->select_as("$this->tbl_participant.cdate", "cdate", 0);
		    $this->db->select_as("$this->tbl_participant.ldate", "ldate", 0);
		    $this->db->select_as("$this->tbl_participant.b_user_id", "user_id", 0);
		    $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl_user.fnama").", 'Admin')", "user_fname", 0);
		    $this->db->select_as("$this->tbl_user.image", "user_image", 0);
		    $this->db->select_as("$this->tbl_participant.last_delete_chat", "last_delete_chat", 0);
		    $this->db->select_as("$this->tbl_participant.is_read", "is_read", 0);
		    $this->db->select_as("$this->tbl_participant.is_active", "is_active", 0);
		    $this->db->from($this->tbl_participant,$this->tbl_participant);
			$this->db->join_composite($this->tbl_user, $this->tbl_user, $this->__join_participant_user(), "left");
		    $this->db->where_as("$this->tbl_participant.nation_code",$this->db->esc($nation_code));
		    $this->db->where_as("$this->tbl_participant.e_chat_room_id",$this->db->esc($chat_room_id));
			$this->db->group_by("$this->tbl_participant.b_user_id");
			$this->db->order_by("$this->tbl_participant.cdate");
			return $this->db->get_first();
		}
		public function update($nation_code, $room_id, $user_id, $payload) {
	        if (!is_array($payload)) {
	            return 0;
	        }
	        $this->db->where("nation_code", $nation_code);
	        $this->db->where("e_chat_room_id", $room_id);
	        $this->db->where("b_user_id", $user_id);
	        return $this->db->update($this->tbl_participant, $payload, 0);
	    }
		/* Public Function */

		// START by Muhammad Sofi 7 January 2022 16:10 | restore button Open Chat
		public function getRoomChatIDByParticipantId($nation_code, $b_user_id_1, $b_user_id_2, $chat_type){
			$this->db->select_as("$this->tbl_participant_as.nation_code", "nation_code", 0);
			$this->db->select_as("$this->tbl_participant_as.e_chat_room_id", "e_chat_room_id", 0);
			$this->db->from($this->tbl_participant, $this->tbl_participant_as);
			$this->db->join_composite($this->tbl_chat_room, $this->tbl_chat_room_as, $this->__joinTbl7(), "left");
			$this->db->join_composite($this->tbl_user, $this->tbl_user_as, $this->__joinTbl5(), "left");
			$this->db->join_composite($this->tbl_participant_2, $this->tbl_participant_2_as, $this->__joinTbl2(), "left");
			$this->db->join_composite($this->tbl_user_2, $this->tbl_user_2_as, $this->__joinTbl10(), "left");
			$this->db->where_as("$this->tbl_participant_as.nation_code",$this->db->esc($nation_code));
			$this->db->where_as("$this->tbl_chat_room_as.chat_type",$this->db->esc($chat_type));
		
			$this->db->where_as("1", "1", 'or', '<>', 1, 0);
		
			$this->db->where_as("$this->tbl_participant_as.b_user_id", $this->db->esc($b_user_id_1),'AND', '=',1,0);
			$this->db->where_as("$this->tbl_participant_2_as.b_user_id", $this->db->esc($b_user_id_2),'OR', '=',0,1);
		
			$this->db->where_as("$this->tbl_participant_as.b_user_id", $this->db->esc($b_user_id_2),'AND', '=',1,0);
			$this->db->where_as("$this->tbl_participant_2_as.b_user_id", $this->db->esc($b_user_id_1),'AND', '=',0,1);
		
			$this->db->where_as("1", "1", 'and', '<>', 0, 1);
			return $this->db->get_first();
		}

		public function getRoomChatAdminID($nation_code, $b_user_id, $chat_type){
			$this->db->select_as("$this->tbl_participant_as.nation_code", "nation_code", 0);
			$this->db->select_as("$this->tbl_participant_as.e_chat_room_id", "room_chat_id", 0);
			$this->db->from($this->tbl_participant, $this->tbl_participant_as);
			$this->db->join_composite($this->tbl_chat_room, $this->tbl_chat_room_as, $this->__joinTbl7(), "left");
			$this->db->join_composite($this->tbl_user, $this->tbl_user_as, $this->__joinTbl5(), "left");
			$this->db->join_composite($this->tbl_participant_2, $this->tbl_participant_2_as, $this->__joinTbl2(), "left");
			$this->db->join_composite($this->tbl_user_2, $this->tbl_user_2_as, $this->__joinTbl10(), "left");
			$this->db->where_as("$this->tbl_participant_as.nation_code",$this->db->esc($nation_code));
			$this->db->where_as("$this->tbl_chat_room_as.chat_type",$this->db->esc($chat_type));
			$this->db->where_as("$this->tbl_participant_as.b_user_id", $this->db->esc($b_user_id),'AND', '=',0,0);
			return $this->db->get_first();
		}
		// END by Muhammad Sofi 7 January 2022 16:10 | restore button Open Chat
	}
