<?php
class E_Chat_Model extends JI_Model
{
    public $tbl = 'e_chat';
    public $tbl_as = 'ec';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';
    public $tbl3 = 'e_complain';
    public $tbl3_as = 'ecom';
    public $tbl4 = 'a_pengguna';
    public $tbl4_as = 'ap';
    public $tbl5 = 'c_produk';
    public $tbl5_as = 'cp';
    public $tbl6 = 'e_chat_attachment';
    public $tbl6_as = 'eca';
    public $tbl7 = 'd_order_detail';
    public $tbl7_as = 'dod';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
        return $cps;
    }

    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.d_order_id", "=", "$this->tbl3_as.d_order_id");
        return $cps;
    }

    private function __joinTbl4()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.a_pengguna_id", "=", "$this->tbl4_as.id");
        return $cps;
    }

    private function __joinTbl5()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl5_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.c_produk_id", "=", "$this->tbl5_as.id");
        return $cps;
    }
     private function __joinTbl7()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl7_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.d_order_id", "=", "$this->tbl7_as.d_order_id");
        $cps[] = $this->db->composite_create("$this->tbl_as.c_produk_id", "=", "$this->tbl7_as.id");
        return $cps;
    }

    public function getTableAlias()
    {
        return $this->tbl_as;
    }

    public function getTableAlias2()
    {
        return $this->tbl2_as;
    }

    public function getTableAlias3()
    {
        return $this->tbl3_as;
    }

    public function getTableAlias4()
    {
        return $this->tbl4_as;
    }

    public function getTableAlias5()
    {
        return $this->tbl5_as;
    }

    public function getTableAlias7()
    {
        return $this->tbl7_as;
    }

    public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="", $sortDir="ASC", $keyword="")
    {
        $this->db->flushQuery();
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'/',$this->tbl_as.c_produk_id)", "id", 0);
        $this->db->select_as("$this->tbl7_as.nama", "nama", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        //$this->db->select_as("IF(COALESCE($this->tbl_as.b_user_id,0)=0,COALESCE($this->tbl4_as.nama,'-'),COALESCE(".$this->__decrypt("$this->tbl2_as.fnama").",'-'))", "b_user_fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"),'b_user_fnama');
        $this->db->select_as("$this->tbl_as.message", "message", 0);
        $this->db->select_as("$this->tbl_as.chat_type", "chat_type", 0);
        $this->db->select_as("COALESCE($this->tbl_as.b_user_id,0)", "is_user", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "a_pengguna_nama", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);

        //by Donny Dennison - 15 september 2020 16:59
        //add flag unread chat
        $this->db->select_as("$this->tbl_as.c_produk_id ", "c_produk_id", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("COALESCE($this->tbl_as.is_starter,0)", $this->db->esc(1), "AND", "=", 0, 0);
        if (strlen($keyword)>0) {
            $this->db->where_as("COALESCE($this->tbl_as.message,'-')", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("COALESCE($this->tbl2_as.fnama,'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl4_as.nama,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $this->db->group_by("id");
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get("object", 0);
    }

    public function countAll($nation_code, $keyword="")
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("COALESCE($this->tbl_as.is_starter,0)", $this->db->esc(1), "AND", "=", 0, 0);
        if (strlen($keyword)>1) {
            $this->db->where_as("COALESCE($this->tbl_as.message,'-')", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("COALESCE($this->tbl2_as.fnama,'-')", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("COALESCE($this->tbl4_as.nama,'-')", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getById($nation_code, $id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->get_first();
    }

    public function getByOrderIds($ids=array())
    {
        $this->db->where_in('d_order_id', $ids);
        return $this->db->get();
    }

    public function getByOrderId($nation_code, $d_order_id)
    {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "order_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "c_produk_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id), "AND", "=", 0, 0);
        $this->db->group_by("$this->tbl_as.d_order_id,$this->tbl_as.c_produk_id");
        return $this->db->get("object", 0);
    }

    public function getByOrderIdDetail($nation_code, $d_order_id)
    {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "order_id", 0);
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'-',$this->tbl_as.c_produk_id)", "id", 0);
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("COALESCE($this->tbl_as.jenis,'-')", "jenis", 0);
        $this->db->select_as("COALESCE($this->tbl_as.message,'-')", "message", 0);
        $this->db->select_as("CONCAT('#','ROOM',$this->tbl_as.d_order_id)", "d_order_id", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.id,'-')", "b_user_id", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.fnama,'-')", "b_user_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.image,'-')", "b_user_image", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.alasan,'-')", "alasan", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.id,'-')", "a_pengguna_id", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "a_pengguna_nama", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.foto,'-')", "a_pengguna_foto", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "c_produk_id", 0);
        $this->db->select_as("COALESCE($this->tbl5_as.nama,'-')", "c_produk_nama", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.d_order_id", $this->db->esc($d_order_id), "AND", "=", 0, 0);
        return $this->db->get("object", 0);
    }

    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    public function update($nation_code, $id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }

    public function updateStarter($nation_code, $d_order_id, $c_produk_id, $ecm_id, $chat_type)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("d_order_id", $d_order_id);
        $this->db->where("c_produk_id", $c_produk_id);
        $this->db->where("chat_type", $chat_type);
        $this->db->where("id", $ecm_id, "AND", "<>");
        return $this->db->update($this->tbl, array("is_starter"=>0), 0);
    }

    public function del($nation_code, $id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->delete($this->tbl);
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

    public function getLastId($nation_code)
    {
        $this->db->select_as("MAX($this->tbl_as.id)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }

    public function getLastIdAttachment($nation_code)
    {
        $this->db->select_as("MAX($this->tbl6_as.id)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl6_as);
        $this->db->where("nation_code", $nation_code);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }

    public function sendMessageInsert($nation_code, $d_order_id, $c_produk_id, $id, $b_user_id, $a_pengguna_id, $e_complain_id, $jenis, $message, $is_starter)
    {
        $sql = 'INSERT INTO `'.$this->tbl.'` (`nation_code`,`d_order_id`,`c_produk_id`,`id`,`b_user_id`,`a_pengguna_id`,`e_complain_id`,`jenis`,`message`,`cdate`,`is_starter`) VALUES ("'.$nation_code.'","'.$d_order_id.'","'.$c_produk_id.'","'.$id.'","'.$b_user_id.'","'.$a_pengguna_id.'","'.$e_complain_id.'","'.$jenis.'","'.$message.'",NOW(),"'.$is_starter.'")';
        $res = $this->db->exec($sql);
        if (!empty($res)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function sendMessageInsertAttachment($nation_code, $d_order_id, $c_produk_id, $e_chat_id, $id, $jenis, $message)
    {
        $sql = 'INSERT INTO `'.$this->tbl6.'` (`nation_code`,`d_order_id`,`c_produk_id`,`e_chat_id`,`id`,`jenis`,`message`) VALUES ("'.$nation_code.'","'.$d_order_id.'","'.$c_produk_id.'","'.$e_chat_id.'","'.$id.'","'.$jenis.'","'.$message.'")';
        $res = $this->db->exec($sql);
        if (!empty($res)) {
            return 1;
        } else {
            return 0;
        }
    }
    public function getRoom($nation_code)
    {
        $this->db->select_as("$this->tbl_as.nation_code", "nation_code", 0);
        $this->db->select_as("$this->tbl_as.d_order_id", "d_order_id", 0);
        $this->db->select_as("$this->tbl_as.c_produk_id", "c_produk_id", 0);
        $this->db->select_as("$this->tbl_as.id", "e_chat_id", 0);
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.jenis", "jenis", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("COALESCE($this->tbl_as.b_user_id,'0')", "b_user_id", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.fnama,'-')", "b_user_fnama", 0);
        $this->db->select_as("COALESCE($this->tbl3_as.image,'media/user/default-profile-picture.png')", "b_user_image", 0);
        $this->db->select_as("COALESCE($this->tbl_as.a_pengguna_id,'0')", "a_pengguna_id", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.nama,'-')", "a_pengguna_nama", 0);
        $this->db->select_as("COALESCE($this->tbl4_as.foto,'media/pengguna/default.png')", "a_pengguna_foto", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.message", "message", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.id,'-')", "c_produk_id", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.foto,'-')", "c_produk_foto", 0);
        $this->db->select_as("COALESCE($this->tbl2_as.thumb,'media/user/default-profile-picture.png')", "c_produk_thumb", 0);
        $this->db->select_as("COALESCE($this->tbl10_as.id,'-')", "b_user_id_buyer", 0);
        $this->db->select_as("COALESCE($this->tbl10_as.fnama,'-')", "b_user_fnama_buyer", 0);
        $this->db->select_as("COALESCE($this->tbl10_as.image,'media/user/default-profile-picture.png')", "b_user_image_buyer", 0);
        $this->db->select_as("COALESCE($this->tbl11_as.id,'-')", "b_user_id_seller", 0);
        $this->db->select_as("COALESCE($this->tbl11_as.fnama,'-')", "b_user_fnama_seller", 0);
        $this->db->select_as("COALESCE($this->tbl11_as.image,'media/user/default-profile-picture.png')", "b_user_image_seller", 0);

        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "left");
        $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), "left");
        $this->db->join_composite($this->tbl11, $this->tbl11_as, $this->__joinTbl11(), "left");
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("COALESCE($this->tbl_as.is_starter,'0')", "1", "AND", "=");
        $this->db->group_by("CONCAT($this->tbl_as.nation_code,'-',$this->tbl_as.d_order_id,'-',$this->tbl_as.c_produk_id)");
        $this->db->order_by("$this->tbl_as.cdate", "DESC");
        return $this->db->get('', 0);
    }

    //by Donny Dennison - 15 september 2020 16:59
    //add flag unread chat
    public function getUnreadChatForFlag($nation_code, $d_order_id, $c_produk_id, $chat_type) {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl_as.d_order_id ", $this->db->esc($d_order_id), "AND", "=");
        $this->db->where_as("$this->tbl_as.c_produk_id ", $this->db->esc($c_produk_id), "AND", "=");
        $this->db->where_as("$this->tbl_as.chat_type", $this->db->esc($chat_type), "AND", "=");
        $this->db->where_as("$this->tbl_as.is_read_admin", $this->db->esc(0), "AND", "=");
        $d = $this->db->get_first();

        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function countAllChat($nation_code, $chat_room_id){
        /*
        SELECT COUNT(*) AS `jumlah`
        FROM `e_chat` `chat`
        INNER JOIN `e_chat_room` `chat_room`
            ON `chat_room`.`id`=`chat`.`e_chat_room_id`
            AND `chat_room`.`nation_code`=".$this->db->esc($nation_code)."
        LEFT JOIN `b_user` `user`
            ON `user`.`id`=`chat`.`b_user_id`
        WHERE `chat_room`.`id` = ".$this->db->esc($chat_room_id)."
        */

        $sql = "SELECT COUNT(*) AS `jumlah` FROM `e_chat` `chat` INNER JOIN `e_chat_room` `chat_room` ON `chat_room`.`id`=`chat`.`e_chat_room_id` AND `chat_room`.`nation_code`=".$this->db->esc($nation_code)."LEFT JOIN `b_user` `user` ON `user`.`id`=`chat`.`b_user_id` WHERE `chat_room`.`id` = ".$this->db->esc($chat_room_id).""; 
        $data = $this->db->query($sql);
        // echo "<pre>".print_r($data)."</pre>";

        if (isset($data[0])) {
            return $data[0]->jumlah;
        }
        return 0;
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
        ORDER BY `chat`.`cdate` ASC
        */

        $sql = "SELECT `chat`.`id`, `chat`.`nation_code`, `chat`.`cdate`, `chat`.`ldate`, `chat`.`e_chat_room_id`, `chat`.`b_user_id`, COALESCE(AES_DECRYPT(`user`.fnama, '".$this->db->enckey."'), 'Admin') AS 'user_fname', `chat`.`a_pengguna_id`, `chat`.`type`, `chat`.`message` FROM `e_chat` `chat` INNER JOIN `e_chat_room` `chat_room` ON `chat_room`.`id`=`chat`.`e_chat_room_id` AND `chat_room`.`nation_code`=".$this->db->esc($nation_code)."LEFT JOIN `b_user` `user` ON `user`.`id`=`chat`.`b_user_id` WHERE `chat_room`.`id` = ".$this->db->esc($chat_room_id)."ORDER BY `chat`.`cdate` ASC";
        
        $data = $this->db->query($sql);
        return $data;
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
        INNER JOIN `b_user` `starter_user`
            ON `starter_user`.`nation_code`=`chat_room`.`nation_code`
            AND `starter_user`.`id`=`chat_room`.`b_user_id_starter`
        WHERE `chat_room`.`nation_code` = ".$this->db->esc($nation_code)." 
            AND `chat_room`.`id` = ".$this->db->esc($chat_room_id)." 
        */

        $sql = "SELECT `chat_room`.`id`, `chat_room`.`nation_code`, `chat_room`.`b_user_id_starter`, COALESCE(AES_DECRYPT(`starter_user`.fnama, '".$this->db->enckey."'), 'Admin') AS 'starter_fname', CONCAT(UCASE(SUBSTRING(`chat_room`.`chat_type`, 1, 1)), LOWER(SUBSTRING(`chat_room`.`chat_type`, 2))) AS `room_type`, `chat_room`.`cdate` AS `submit_date`, `chat_room`.`ldate` AS `last_date`, `chat_room`.`c_community_id` AS `community_id`, `chat_room`.`custom_name_1`, `chat_room`.`custom_name_2`, `chat_room`.`is_read_admin` FROM `e_chat_room` `chat_room` INNER JOIN `b_user` `starter_user` ON `starter_user`.`nation_code`=`chat_room`.`nation_code` AND `starter_user`.`id`=`chat_room`.`b_user_id_starter` WHERE `chat_room`.`nation_code` = ".$this->db->esc($nation_code)." AND `chat_room`.`id` = ".$this->db->esc($chat_room_id)." ";

        $data = $this->db->query($sql);
        return $data[0];
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

        $sql = "SELECT `chat_room`.`id` FROM `e_chat_room` `chat_room` INNER JOIN `e_chat_participant` `participant` ON `participant`.`e_chat_room_id`=`chat_room`.`id` WHERE `chat_room`.`chat_type`='admin'AND `participant`.`b_user_id`=".$this->db->esc($user_id)."GROUP BY `chat_room`.`id`"; $data = $this->db->query($sql);
        if($data) return $data[0]->id;
        return false;
    }

  // public function getLastId($nation_code,$d_order_id,$c_produk_id){
  //   $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
  //   $this->db->from($this->tbl, $this->tbl_as);
  //   $this->db->where("nation_code",$nation_code);
  //   $this->db->where("d_order_id",$d_order_id);
  //   $this->db->where("c_produk_id",$c_produk_id);
  //   $d = $this->db->get_first('',0);
  //   if(isset($d->last_id)) return $d->last_id;
  //   return 0;
  // }

    public function createChatRoom($nation_code, $user_id) {
        $sql = "SELECT (MAX(`chat_room`.`id`)+1) AS `id` FROM `e_chat_room` `chat_room`"; 
        $data = $this->db->query($sql);
        $room_id = $data[0]->id;

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

        $sql = "INSERT INTO `e_chat_participant` (`nation_code`, `e_chat_room_id`, `b_user_id` ) VALUES(".$this->db->esc($nation_code).", ".$this->db->esc($room_id).", ".$this->db->esc($user_id)."); "; 
        $res = $this->db->exec($sql);
        return $room_id;
    }

}
