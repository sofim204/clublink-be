<?php
class F_Verification_Phone_Number_Model extends JI_Model
{

    public $tbl = 'f_verification_phone_number';
    public $tbl_as = 'fvpm';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function set($di)
    {
        if (!is_array($di)) {
            return 0;
        }

        if (isset($di['email'])) {
            if (strlen($di['email'])) {
                $di['email'] = $this->__encrypt($di['email']);
            }
        }

        if (isset($di['telp'])) {
            if (strlen($di['telp'])) {
                $di['telp'] = $this->__encrypt($di['telp']);
            }
        }

        $this->db->insert($this->tbl, $di, 0, 0);
        return $this->db->last_id;
    }

    public function update($id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }

    public function del($id)
    {
        $this->db->where("id", $id);
        return $this->db->delete($this->tbl);
    }

    public function getById($nation_code, $id)
    {
        $this->db->select_as("*, $this->tbl_as.id", "id");
        $this->db->select_as($this->__decrypt('fnama'), 'fnama');
        $this->db->select_as($this->__decrypt('lnama'), 'lnama');
        $this->db->select_as($this->__decrypt('email'), 'email');
        $this->db->select_as($this->__decrypt('telp'), 'telp');
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->get_first();
    }

    public function deactiveByPhoneNumber($phone_number, $du, $b_user_id = NULL)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where_as('is_confirmed', $this->db->esc(0));
        $this->db->where_as('is_active', $this->db->esc(1));
        $this->db->where_as($this->__decrypt('telp'), $this->db->esc($phone_number));

        if($b_user_id != NULL){
            $this->db->where_as('b_user_id', $this->db->esc($b_user_id));
        }

        return $this->db->update($this->tbl, $du, 0);
    }

    public function checkVerificationNumber($nation_code, $verification_number, $phone_number = NULL, $b_user_id = NULL)
    {
        $this->db->select("*");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("nation_code", $this->db->esc($nation_code));
        $this->db->where_as('verification_number', $this->db->esc($verification_number));
        $this->db->where_as('is_active', $this->db->esc(1));
        $this->db->where_as('is_confirmed', $this->db->esc(0));

        if($phone_number != NULL){
            $this->db->where_as($this->__decrypt('telp'), $this->db->esc($phone_number));
            if($b_user_id != NULL){
                $this->db->where_as('b_user_id', $this->db->esc($b_user_id));
            }
            return $this->db->get_first('', 0);
        }else{
            return $this->db->get();
        }
    }

    public function checkVerificationNumberConfirmed($nation_code, $verification_number, $phone_number = NULL, $b_user_id = NULL)
    {
        $this->db->select("*");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("nation_code", $this->db->esc($nation_code));
        $this->db->where_as('verification_number', $this->db->esc($verification_number));
        $this->db->where_as('is_active', $this->db->esc(1));
        $this->db->where_as('is_confirmed', $this->db->esc(1));
        $this->db->where_as('b_user_id', "is null");

        if($phone_number != NULL){
            $this->db->where_as($this->__decrypt('telp'), $this->db->esc($phone_number));
            if($b_user_id != NULL){
                $this->db->where_as('b_user_id', $this->db->esc($b_user_id));
            }
            return $this->db->get_first('', 0);
        }else{
            return $this->db->get();
        }
    }
}
