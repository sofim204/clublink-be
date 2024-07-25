<?php
class B_User_Card_Model extends JI_Model
{
    public $is_cacheable;
    public $tbl = 'b_user_card';
    public $tbl_as = 'buc';

    //by Donny Dennison - 28 october 2021 10:48
    //payment call 2c2p in api for flutter version
    public $tbl2 = 'b_user_card_type';
    public $tbl2_as = 'buct';

    public function __construct()
    {
        parent::__construct();
        $this->is_cacheable = 0;
        $this->db->from($this->tbl, $this->tbl_as);
    }

    //by Donny Dennison - 28 october 2021 10:48
    //payment call 2c2p in api for flutter version
    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.bank", "=", "$this->tbl2_as.id");
        return $cps;
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
    public function getLastId($nation_code, $b_user_id)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }
    public function getById($nation_code, $b_user_id, $id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.nomor"), "nomor", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("id", $id);
        return $this->db->get_first();
    }
    public function getAll($nation_code, $b_user_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.nomor"), "nomor", 0);

        //by Donny Dennison - 28 october 2021 10:48
        //payment call 2c2p in api for flutter version
        $this->db->select_as("$this->tbl2_as.url", "url", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        //by Donny Dennison - 28 october 2021 10:48
        //payment call 2c2p in api for flutter version
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');

        $this->db->where("b_user_id", $b_user_id);
        $this->db->order_by("id", "DESC");
        return $this->db->get();
    }
    public function set($di)
    {
        if (isset($di['nomor'])) {
            if (strlen($di['nomor'])) {
                $di['nomor'] = $this->__encrypt($di['nomor']);
            }
        }
        return $this->db->insert($this->tbl, $di);
    }
    public function update($nation_code, $b_user_id, $id, $du)
    {
        if (isset($du['nomor'])) {
            if (strlen($du['nomor'])) {
                $du['nomor'] = $this->__encrypt($du['nomor']);
            }
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du);
    }
    public function delete($nation_code, $b_user_id, $id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("id", $id);
        return $this->db->delete($this->tbl);
    }

    //by Donny Dennison - 2 november 2021 13:45
    //payment call 2c2p in api for flutter version
    public function getByCardToken($nation_code, $b_user_id, $token_result)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.nomor"), "nomor", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("token_result", $token_result);
        return $this->db->get_first();
    }

}
