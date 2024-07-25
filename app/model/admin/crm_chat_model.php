<?php
class Crm_Chat_Model extends JI_Model
{
    var $is_cacheable;
  
    public function __construct(){
      parent::__construct();
      $this->is_cacheable = 0;
    }
  
    public function trans_start(){
      $r = $this->db->autocommit(0);
      if($r) return $this->db->begin();
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
  
    public function getByRoomId($nation_code,$chat_room_id){
        /*
        SELECT
            `chat_room`.`id`,
            `chat_room`.`nation_code`,
            `chat_room`.`b_user_id_starter`,
            COALESCE(AES_DECRYPT(`starter_user`.fnama, '".$this->db->enckey."'), 'Admin') AS 'starter_fname',
            CONCAT(UCASE(SUBSTRING(`chat_room`.`chat_type`, 1, 1)), LOWER(SUBSTRING(`chat_room`.`chat_type`, 2))) AS `room_type`,
            `chat_room`.`cdate` AS `submit_date`,
            `chat_room`.`ldate` AS `last_date`,
            `chat_room`.`c_community_id` AS `community_id`,
            `chat_room`.`custom_name_1`,
            `chat_room`.`custom_name_2`,
            `chat_room`.`is_read_admin`
        FROM `e_chat_room` `chat_room`
        LEFT JOIN `b_user` `starter_user`
            ON `starter_user`.`nation_code`=`chat_room`.`nation_code`
            AND `starter_user`.`id`=`chat_room`.`b_user_id_starter`
        WHERE `chat_room`.`nation_code` = ".$this->db->esc($nation_code)." 
            AND `chat_room`.`id` = ".$this->db->esc($chat_room_id)." 
        */

        $sql = "SELECT `chat_room`.`id`, `chat_room`.`nation_code`, `chat_room`.`b_user_id_starter`, COALESCE(AES_DECRYPT(`starter_user`.fnama, '".$this->db->enckey."'), 'Admin') AS 'starter_fname', CONCAT(UCASE(SUBSTRING(`chat_room`.`chat_type`, 1, 1)), LOWER(SUBSTRING(`chat_room`.`chat_type`, 2))) AS `room_type`, `chat_room`.`cdate` AS `submit_date`, `chat_room`.`ldate` AS `last_date`, `chat_room`.`c_community_id` AS `community_id`, `chat_room`.`custom_name_1`, `chat_room`.`custom_name_2`, `chat_room`.`is_read_admin` FROM `e_chat_room` `chat_room` LEFT JOIN `b_user` `starter_user` ON `starter_user`.`nation_code`=`chat_room`.`nation_code` AND `starter_user`.`id`=`chat_room`.`b_user_id_starter` WHERE `chat_room`.`nation_code` = ".$this->db->esc($nation_code)." AND `chat_room`.`id` = ".$this->db->esc($chat_room_id)." ";

        $data = $this->db->query($sql);
        return $data[0];
    }
  
    public function update($nation_code,$chat_room_id,$du){
      $this->db->where("nation_code",$nation_code);
      $this->db->where("id",$chat_room_id);
      return $this->db->update('e_chat_room',$du);
    }
  
    public function set($di){
      return $this->db->insert($this->tbl,$di);
    }
  
    public function checkRoomChat($nation_code, $b_user_id_1, $b_user_id_2, $chat_type){
      $this->db->select("*");
  
      $this->db->from($this->tbl,$this->tbl_as);
  
      $this->db->where("$this->tbl_as.nation_code",$nation_code);
  
      $this->db->where_as("$this->tbl_as.b_user_id_1", $this->db->esc($b_user_id_1), 'and', '=', 1, 0);
      $this->db->where_as("$this->tbl_as.b_user_id_2", $this->db->esc($b_user_id_2), 'or', '=', 0, 1);
  
      $this->db->where_as("$this->tbl_as.b_user_id_1", $this->db->esc($b_user_id_2), 'and', '=', 1, 0);
      $this->db->where_as("$this->tbl_as.b_user_id_2", $this->db->esc($b_user_id_1), 'and', '=', 0, 1);
  
      $this->db->where_as("$this->tbl_as.chat_type",$this->db->esc($chat_type));
  
      return $this->db->get_first();
      
    }

    public function getChatByRoomId($nation_code, $chat_room_id) {
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
        WHERE `chat_room`.`id` = ".$this->db->esc($chat_room_id)."
        ORDER BY `chat`.`cdate` DESC
        */

        $sql = "SELECT `chat`.`id`, `chat`.`nation_code`, `chat`.`cdate`, `chat`.`ldate`, `chat`.`e_chat_room_id`, `chat`.`b_user_id`, COALESCE(AES_DECRYPT(`user`.fnama, '".$this->db->enckey."'), 'Admin') AS 'user_fname', `chat`.`a_pengguna_id`, `chat`.`type`, `chat`.`message` FROM `e_chat` `chat` INNER JOIN `e_chat_room` `chat_room` ON `chat_room`.`id`=`chat`.`e_chat_room_id` AND `chat_room`.`nation_code`=".$this->db->esc($nation_code)."LEFT JOIN `b_user` `user` ON `user`.`id`=`chat`.`b_user_id` WHERE `chat_room`.`id` = ".$this->db->esc($chat_room_id)."ORDER BY `chat`.`cdate` DESC";

        $data = $this->db->query($sql);
        return $data;
    }

    public function getParticipantByRoomId($nation_code, $chat_room_id) {
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

        $sql = "SELECT `participant`.`nation_code`, `participant`.`e_chat_room_id`, `participant`.`b_user_id`, AES_DECRYPT(`user`.fnama, '".$this->db->enckey."') AS 'user_fname', `participant`.`cdate`, `participant`.`ldate`, `participant`.`last_delete_chat`, `participant`.`is_read`, `participant`.`is_active` FROM `e_chat_participant` `participant` INNER JOIN `b_user` `user` ON `user`.`id`=`participant`.`b_user_id` WHERE `participant`.`nation_code` = ".$this->db->esc($nation_code)." AND `participant`.`e_chat_room_id` = ".$this->db->esc($chat_room_id)." GROUP BY `participant`.`b_user_id` ORDER BY AES_DECRYPT(`user`.fnama, '".$this->db->enckey."') ASC";

        $data = $this->db->query($sql);
        return $data;
    }
}
