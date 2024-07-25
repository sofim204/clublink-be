<?php
class B_User_Model extends JI_Model
{
    public $tbl = 'b_user';
    public $tbl_as = 'bu';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function getById($nation_code, $id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as("COALESCE($this->tbl_as.fb_id,'-')", "fb_id", 0);
        $this->db->select_as("COALESCE($this->tbl_as.apple_id,'-')", "apple_id", 0);
        $this->db->select_as("COALESCE($this->tbl_as.google_id,'-')", "google_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.band_fnama"), "band_fnama", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        // $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where("id", $id);
        return $this->db->get_first();
    }

    public function getUserOnline()
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as("COALESCE($this->tbl_as.fb_id,'-')", "fb_id", 0);
        $this->db->select_as("COALESCE($this->tbl_as.google_id,'-')", "google_id", 0);
        $this->db->select_as("COALESCE($this->tbl_as.apple_id,'-')", "apple_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.is_online", $this->db->esc(1));
        $this->db->where_as("DATE_ADD($this->tbl_as.last_online, INTERVAL 5 MINUTE)", "NOW()", "AND", "<=");
        return $this->db->get();
    }

    public function getUserOnlineAfter24Hours()
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as("COALESCE($this->tbl_as.fb_id,'-')", "fb_id", 0);
        $this->db->select_as("COALESCE($this->tbl_as.google_id,'-')", "google_id", 0);
        $this->db->select_as("COALESCE($this->tbl_as.apple_id,'-')", "apple_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.is_online", $this->db->esc(1));
        $this->db->where_as("DATE_ADD($this->tbl_as.last_online, INTERVAL 24 HOUR)", "NOW()", "AND", "<=");
        return $this->db->get();
    }

    public function update($b_user_id, $du){
        if(!is_array($du)) return 0;
        $this->db->where("id",$b_user_id);
        return $this->db->update($this->tbl,$du,0);
    }

    public function updateMass($ids, $du){
        if(!is_array($du)) return 0;
        $this->db->where_in("id",$ids);
        return $this->db->update($this->tbl,$du,0);
    }

    //by Donny Dennison - 12 july 2022 14:56
    //new offer system
    public function updateTotal($nation_code, $b_user_id, $parameter, $operator, $total)
    {
        return $this->db->exec("UPDATE `$this->tbl` SET $parameter = IF('$operator' = '+', $parameter $operator $total, IF($parameter <= 0,0,$parameter $operator $total))
            WHERE nation_code = '$nation_code' AND id = '$b_user_id';");
    }

    //START by Donny Dennison - 07 october 2022 15:49
    //integrate api blockchain
    // public function getByBlockChainCreateUserWalletApiHaventCalled($nation_code)
    // {
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where_as("blockchain_createuserwallet_api_called", $this->db->esc(0));
    //     $this->db->order_by("id", "ASC");
    //     $this->db->limit("200");
    //     return $this->db->get();
    // }
    //END by Donny Dennison - 07 october 2022 15:49
    //integrate api blockchain

    public function getByBlockChainCreateUserWalletNewApiHaventCalled($nation_code)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as("COALESCE($this->tbl_as.fb_id,'-')", "fb_id", 0);
        $this->db->select_as("COALESCE($this->tbl_as.apple_id,'-')", "apple_id", 0);
        $this->db->select_as("COALESCE($this->tbl_as.google_id,'-')", "google_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.band_fnama"), "band_fnama", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where_as("user_wallet_code_new_api_called", $this->db->esc(0));
        $this->db->order_by("id", "ASC");
        $this->db->limit("200");
        return $this->db->get();
    }

    //START by Donny Dennison - 11 november 2022 16:59
    //integrate api blockchain
    // public function getByBlockChainLateReferralRewardTransactionApiHaventCalled($nation_code)
    // {
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where_as("blockchain_createuserwallet_api_called", $this->db->esc(1));
    //     $this->db->where_as("main_transaction_id", $this->db->esc(""), "AND", "!=");
    //     $this->db->where_as("blockchain_latereferralrewardtransaction_api_called", $this->db->esc(0));
    //     $this->db->order_by("id", "ASC");
    //     $this->db->limit("200");
    //     return $this->db->get();
    // }
    //END by Donny Dennison - 11 november 2022 16:59
    //integrate api blockchain

    public function updateFreeTicket($nation_code, $du){
        if(!is_array($du)) return 0;
        return $this->db->update($this->tbl,$du,0);
    }

    public function getByBlockChainCreateUserWalletNewApiOldUser($nation_code, $page, $page_size)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        // $this->db->where_as("user_wallet_code_new_api_called", $this->db->esc(0));
        $this->db->order_by("cdate", "ASC");
        // $this->db->limit("200");
        $this->db->page($page, $page_size);
        return $this->db->get();
    }

    public function getForAirdropv2($nation_code)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as("COALESCE($this->tbl_as.fb_id,'-')", "fb_id", 0);
        $this->db->select_as("COALESCE($this->tbl_as.apple_id,'-')", "apple_id", 0);
        $this->db->select_as("COALESCE($this->tbl_as.google_id,'-')", "google_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.band_fnama"), "band_fnama", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_permanent_inactive", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.get_airdropv2", $this->db->esc(0));
        $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc(date("Y-m-d", strtotime("-1 day"))), "AND", "<=");
        $this->db->order_by("cdate", "DESC");
        $this->db->limit("50");
        return $this->db->get();
    }
}
