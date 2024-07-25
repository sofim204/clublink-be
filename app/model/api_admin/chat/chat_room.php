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
	class Chat_Room extends SENE_Model {
		/* Global & Constructor */
	  	var $is_cacheable;
		var $tbl_chat_room = 'e_chat_room';
		var $tbl_chat = 'e_chat'; 
		var $tbl_order = 'd_order';
		var $tbl_admin = 'a_pengguna';
		var $tbl_user = 'b_user';
		var $tbl_user_alias = 'user'; 
		var $tbl_user2_alias = 'user2'; 
		var $tbl_user3_alias = 'user3';

        //by Donny Dennison - 22 october 2021 16:54
        //cms-community
        //fix search in chat list and last chat by column
		var $tbl_chat_participant = 'e_chat_participant';

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
		private function __join_chat_room_user(){
			$join_condition = array(
				$this->db->composite_create("$this->tbl_chat_room.nation_code","=","$this->tbl_user.nation_code"),
				$this->db->composite_create("$this->tbl_chat_room.b_user_id_starter","=","$this->tbl_user.id"),
			);
			return $join_condition;
		}	
		/* Join */

        //by Donny Dennison - 22 october 2021 16:54
        //cms-community
        //fix search in chat list and last chat by column
        //START by Donny Dennison - 22 october 2021 16:54

		/* Join */
		private function __join_chat_participant(){
			$join_condition = array(
				$this->db->composite_create("$this->tbl_chat_room.nation_code","=","$this->tbl_chat_participant.nation_code"),
				$this->db->composite_create("$this->tbl_chat_room.id","=","$this->tbl_chat_participant.e_chat_room_id"),
				$this->db->composite_create("1","=","$this->tbl_chat_participant.is_active"),
			);
			return $join_condition;
		}	
		/* Join */

		/* Join */
		private function __join_user_chat_participant(){
			$join_condition = array(
				$this->db->composite_create("$this->tbl_chat_participant.nation_code","=","$this->tbl_user_alias.nation_code"),
				$this->db->composite_create("$this->tbl_chat_participant.b_user_id ","=","$this->tbl_user_alias.id"),
			);
			return $join_condition;
		}	
		/* Join */

        //END by Donny Dennison - 22 october 2021 16:54

		/* Public Function */
		public function count_all($nation_code, $__filter=array()){
			$this->db->select_as("COUNT(DISTINCT $this->tbl_chat_room.id)", "total", 0);
			$this->db->from($this->tbl_chat_room);
			$this->db->join_composite($this->tbl_user, $this->tbl_user, $this->__join_chat_room_user(), "left");

			//by Donny Dennison - 22 october 2021 16:54
	        //cms-community
	        //fix search in chat list and last chat by column
			if (strlen($__filter['keyword'])>0) {
				$this->db->join_composite($this->tbl_chat_participant, $this->tbl_chat_participant, $this->__join_chat_participant(), "left");
				$this->db->join_composite($this->tbl_user, $this->tbl_user_alias, $this->__join_user_chat_participant(), "left");
			}

			$this->db->where("$this->tbl_chat_room.nation_code",$nation_code);

  			$this->db->where_as("IF((SELECT COUNT(*) FROM `$this->tbl_chat` WHERE `nation_code`=`$this->tbl_chat_room`.`nation_code` AND `e_chat_room_id`=`$this->tbl_chat_room`.`id` AND (`type`='chat' OR `type`='offering')) > 0,'yes','no')", $this->db->esc("yes"));

			if (strlen($__filter['keyword'])>0) {

				//by Donny Dennison - 22 october 2021 16:54
		        //cms-community
		        //fix search in chat list and last chat by column
		        // $this->db->where_as("COALESCE(".$this->__decrypt("$this->tbl_user.fnama").", 'Admin')", $this->db->esc($__filter['keyword']), "AND", "%like%", 0,0);
		        $this->db->where_as("COALESCE(".$this->__decrypt("$this->tbl_user.fnama").", 'Admin')", $__filter['keyword'], "OR", "%like%", 1,0);

				//by Donny Dennison - 22 october 2021 16:54
		        //cms-community
		        //fix search in chat list and last chat by column
		        $this->db->where_as("COALESCE(".$this->__decrypt("$this->tbl_user_alias.fnama").", 'Admin')", $__filter['keyword'], "AND", "%like%", 0,1);

		    }
		    if ($__filter['room_type']) {
		        $this->db->where_as("$this->tbl_chat_room.chat_type", $this->db->esc($__filter['room_type']), "AND", "=", 0,0);
		    }
		    // if ($__filter['from_date']) {
		    //     $this->db->where_as("$this->tbl_chat_room.ldate", $this->db->esc($__filter['from_date']), "AND", ">=", 1,0);
		    // }
		    // if ($__filter['to_date']) {
		    //     $this->db->where_as("$this->tbl_chat_room.ldate", $this->db->esc($__filter['to_date']), "AND", "<=", 0,1);
		    // }
			
			// START by Muhammad Sofi 2 February 2022 14:17 | change filter start date to end date
			$sdate = $__filter['from_date'];
			$edate = $__filter['to_date'];

			if (strlen($sdate)==10 && strlen($edate)==10) {
            	$this->db->between("DATE($this->tbl_chat_room.ldate)", "DATE('$sdate')", "DATE('$edate')");
			} elseif (strlen($sdate)==10 && strlen($edate)!=10) {
				$this->db->where_as("DATE($this->tbl_chat_room.ldate)", "DATE('$sdate')", 'AND', '>=');
			} elseif (strlen($sdate)!=10 && strlen($edate)==10) {
				$this->db->where_as("DATE($this->tbl_chat_room.ldate)", "DATE('$edate')", 'AND', '<=');
			}
			// END by Muhammad Sofi 2 February 2022 14:17 | change filter start date to end date

			//by Donny Dennison - 22 october 2021 16:54
	        //cms-community
	        //fix search in chat list and last chat by column
		    // $this->db->group_by("$this->tbl_chat_room.id");

			$result = $this->db->get_first('object', 0);
			if (isset($result->total)) {
			    return $result->total;
			}
		}
  		public function get_all($nation_code, $page=1, $page_size=10, $sort_col='ldate', $sort_dir='DESC', $__filter=array()){
  			//Filter Variabel : [keyword, room_type, from_date, to_date]
  			$this->db->select_as("$this->tbl_chat_room.id", "room_id", 0);
  			$this->db->select_as("$this->tbl_chat_room.chat_type", "room_type", 0);
  			$this->db->select_as("$this->tbl_chat_room.cdate", "submit_date", 0);
  			$this->db->select_as("$this->tbl_chat_room.ldate", "last_date", 0);
		    $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl_user.fnama").", 'Admin')", "starter_fname", 0);
  			$this->db->select_as("$this->tbl_chat_room.c_community_id", "community_id", 0);
  			$this->db->select_as("IFNULL((SELECT `message` FROM `$this->tbl_chat` WHERE `nation_code`=`$this->tbl_chat_room`.`nation_code` AND `e_chat_room_id`=`$this->tbl_chat_room`.`id` AND (`type`='chat' OR `type`='offering') ORDER BY `cdate` DESC LIMIT 1 ), '-')", "last_chat", 0);

			//by Donny Dennison - 22 october 2021 16:54
	        //cms-community
	        //fix search in chat list and last chat by column
  			$this->db->select_as("IFNULL((SELECT MAX(cdate) FROM `$this->tbl_chat` WHERE `nation_code`=`$this->tbl_chat_room`.`nation_code` AND `e_chat_room_id`=`$this->tbl_chat_room`.`id` AND `type`='chat'), $this->tbl_chat_room.ldate )", "last_chat_date", 0);
  			$this->db->select_as("IFNULL((SELECT ".$this->__decrypt("`user`.`fnama`")." FROM `$this->tbl_chat` `chat` LEFT JOIN `$this->tbl_user` `user` ON `user`.`nation_code`=`chat`.`nation_code` AND `user`.`id`=`chat`.`b_user_id` WHERE `chat`.`nation_code`=`$this->tbl_chat_room`.`nation_code` AND `chat`.`e_chat_room_id`=`$this->tbl_chat_room`.`id` AND (`chat`.`type`='chat' OR `chat`.`type`='offering')  ORDER BY `chat`.`cdate` DESC LIMIT 1 ), IF($this->tbl_chat_room.chat_type = 'admin','Admin','-'))", "last_fname", 0);

  			$this->db->from($this->tbl_chat_room,$this->tbl_chat_room);
			$this->db->join_composite($this->tbl_user, $this->tbl_user, $this->__join_chat_room_user(), "left");

			//by Donny Dennison - 22 october 2021 16:54
	        //cms-community
	        //fix search in chat list and last chat by column
			if (strlen($__filter['keyword'])>0) {
				$this->db->join_composite($this->tbl_chat_participant, $this->tbl_chat_participant, $this->__join_chat_participant(), "left");
				$this->db->join_composite($this->tbl_user, $this->tbl_user_alias, $this->__join_user_chat_participant(), "left");
			}

		    $this->db->where_as("$this->tbl_chat_room.nation_code",$this->db->esc($nation_code));

  			$this->db->where_as("IF((SELECT COUNT(*) FROM `$this->tbl_chat` WHERE `nation_code`=`$this->tbl_chat_room`.`nation_code` AND `e_chat_room_id`=`$this->tbl_chat_room`.`id` AND (`type`='chat' OR `type`='offering')) > 0,'yes','no')", $this->db->esc("yes"));

			if (strlen($__filter['keyword'])>0) {

				//by Donny Dennison - 22 october 2021 16:54
		        //cms-community
		        //fix search in chat list and last chat by column
		        // $this->db->where_as("COALESCE(".$this->__decrypt("$this->tbl_user.fnama").", 'Admin')", $this->db->esc($__filter['keyword']), "AND", "%like%", 0,0);
		        $this->db->where_as("COALESCE(".$this->__decrypt("$this->tbl_user.fnama").", 'Admin')", $__filter['keyword'], "OR", "%like%", 1,0);

				//by Donny Dennison - 22 october 2021 16:54
		        //cms-community
		        //fix search in chat list and last chat by column
		        $this->db->where_as("COALESCE(".$this->__decrypt("$this->tbl_user_alias.fnama").", 'Admin')", $__filter['keyword'], "AND", "%like%", 0,1);

		    }
		    if ($__filter['room_type']) {
		        $this->db->where_as("$this->tbl_chat_room.chat_type", $this->db->esc($__filter['room_type']), "AND", "=", 0,0);
		    }
		    // if ($__filter['from_date']) {
		    //     $this->db->where_as("$this->tbl_chat_room.ldate", $this->db->esc($__filter['from_date']), "AND", ">=", 1,0);
		    // }
		    // if ($__filter['to_date']) {
		    //     $this->db->where_as("$this->tbl_chat_room.ldate", $this->db->esc($__filter['to_date']), "AND", "<=", 0,1);
		    // }
			
			// START by Muhammad Sofi 2 February 2022 14:17 | change filter start date to end date
			$sdate = $__filter['from_date'];
			$edate = $__filter['to_date'];

			if (strlen($sdate)==10 && strlen($edate)==10) {
            	$this->db->between("DATE($this->tbl_chat_room.ldate)", "DATE('$sdate')", "DATE('$edate')");
			} elseif (strlen($sdate)==10 && strlen($edate)!=10) {
				$this->db->where_as("DATE($this->tbl_chat_room.ldate)", "DATE('$sdate')", 'AND', '>=');
			} elseif (strlen($sdate)!=10 && strlen($edate)==10) {
				$this->db->where_as("DATE($this->tbl_chat_room.ldate)", "DATE('$edate')", 'AND', '<=');
			}
			// END by Muhammad Sofi 2 February 2022 14:17 | change filter start date to end date

			$this->db->group_by("$this->tbl_chat_room.id");
			$this->db->order_by($sort_col, $sort_dir);
			$this->db->limit($page, $page_size);
  			
    		return $this->db->get("object", 0);
		}

  		public function get_by_id($nation_code,$chat_room_id){
  			$this->db->select_as("$this->tbl_chat_room.nation_code", "nation_code", 0);
		    $this->db->select_as("$this->tbl_chat_room.id", "id", 0);
		    $this->db->select_as("$this->tbl_chat_room.chat_type", "chat_type", 0);
		    $this->db->select_as("$this->tbl_chat_room.custom_name_1", "custom_name_1", 0);
		    $this->db->select_as("$this->tbl_chat_room.b_user_id_starter", "starter_id", 0);
		    $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl_user.fnama").", 'Admin')", "starter_fname", 0);
		    $this->db->from($this->tbl_chat_room,$this->tbl_chat_room);
			$this->db->join_composite($this->tbl_user, $this->tbl_user, $this->__join_chat_room_user(), "left");
		    $this->db->where_as("$this->tbl_chat_room.nation_code",$this->db->esc($nation_code));
		    $this->db->where_as("$this->tbl_chat_room.id",$this->db->esc($chat_room_id));
			return $this->db->get_first();
		}

	    public function get_last_id($nation_code) {
	        $this->db->select_as("MAX($this->tbl_chat_room.id)+1", "last_id", 0);
	        $this->db->from($this->tbl_chat_room, $this->tbl_chat_room);
	        $this->db->where("nation_code", $nation_code);
	        $d = $this->db->get_first('', 0);
	        if (isset($d->last_id)) {
	            return $d->last_id;
	        }
	        return 0;
	    }

		public function update($nation_code, $id, $payload) {
	        if (!is_array($payload)) {
	            return 0;
	        }
	        $this->db->where("nation_code", $nation_code);
	        $this->db->where("id", $id);
	        return $this->db->update($this->tbl_chat_room, $payload, 0);
	    }

		public function get_all_offer($nation_code, $room_id) {
			$this->db->flushQuery();
			$this->db->select_as("$this->tbl_chat_room.nation_code", "nation_code", 0);
			$this->db->select_as("$this->tbl_chat_room.id", "room_id", 0);
			$this->db->select_as("$this->tbl_chat_room.c_produk_thumb", "url", 0);
			$this->db->select_as("$this->tbl_chat_room.id", "room_id", 0);
			$this->db->select_as("$this->tbl_chat_room.c_produk_harga_jual", "harga_jual", 0);
			$this->db->select_as("$this->tbl_chat_room.c_produk_nama", "product_name_offer", 0);
			$this->db->from($this->tbl_chat_room, $this->tbl_chat_room);
			$this->db->where("$this->tbl_chat_room.nation_code",$nation_code);
			$this->db->where("$this->tbl_chat_room.id",$room_id);
			$this->db->order_by("$this->tbl_chat_room.id", 'ASC');
			return $this->db->get("object", 0);
		}

	}
