<?php
class B_User_model extends JI_Model
{
    public $tbl = 'b_user';
    public $tbl_as = 'bu';

    public function __construct()
    {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    // public function getLastId($nation_code)
    // {
    //     // $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    //     // $this->db->from($this->tbl, $this->tbl_as);
    //     // $this->db->where("nation_code", $nation_code);
    //     // $d = $this->db->get_first('', 0);
    //     // if (isset($d->last_id)) {
    //     //     return $d->last_id;
    //     // }
    //     // return 0;
    //     // $sql ="SELECT COALESCE(MAX(id),0)+1 AS id FROM `".$this->tbl."` WHERE nation_code = '".$nation_code."' FOR UPDATE;";
    //     $sql ="SELECT COALESCE(MAX(id),0)+1 AS id FROM `".$this->tbl."` WHERE id >= (SELECT COALESCE(MAX(id),0) FROM `".$this->tbl."` WHERE nation_code = '".$nation_code."') AND nation_code = '".$nation_code."' FOR UPDATE;";
    //     return $this->db->query($sql)[0]->id;
    // }

    public function auth($nation_code, $username)
    {
        $this->db->select_as("*, $this->tbl_as.id", "id");
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama");
        $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl_as.lnama").",'')", "lnama");
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email");
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp");
        $this->db->select_as("COALESCE(`fb_id`,'-')", 'fb_id', 0);
        $this->db->select_as("COALESCE(`apple_id`,'-')", 'apple_id', 0);
        $this->db->select_as("COALESCE(`google_id`,'-')", 'google_id', 0);
        $this->db->select_as("COALESCE(`api_web_token`,'-')", 'api_web_token', 0);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanenet user feature
        // $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        // $this->db->where_as("$this->tbl_as.is_permanent_inactive", $this->db->esc(1));

        $this->db->where_as($this->__decrypt("`email`"), $this->db->esc($username), "OR", "like", 1, 0);
        $this->db->where_as($this->__decrypt("`telp`"), $this->db->esc($username), "OR", "like", 0, 1);
        return $this->db->get_first('object', 0);
    }

    public function checkToken($nation_code, $token, $kind="api_web")
    {
        if (strlen($token)<=4) {
            return false;
        }
        $dt = $this->db->where($kind.'_token', $token)->get();
        if (count($dt)>1) {
            foreach ($dt as $d) {
                $this->setToken($nation_code, $d->id, "NULL", $kind);
            }
            return false;
        } elseif (count($dt)==1) {
            return true;
        } else {
            return false;
        }
    }

    public function setToken($nation_code, $id, $token, $kind="api_web")
    {
        $this->db->where("nation_code", $nation_code)->where("id", $id);
        $du = array($kind.'_token'=>$token);
        return $this->db->update($this->tbl, $du);
    }

    public function getByToken($nation_code, $token, $kind="api_web")
    {
        if (strlen($token)<=4) {
            return new stdClass();
        }
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

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanenet user feature
        // $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_permanent_inactive", $this->db->esc(1));

        $this->db->where($kind.'_token', $token);
        // return $this->db->get_first('object', 0);
        $userData = $this->db->get_first('object', 0);

        // if(isset($userData->id)){
        //     if($userData->is_active == 1){
        //         $du = array();
        //         $du['is_online'] = 1;
        //         $du['last_online'] = date('Y-m-d H:i:s');
        //         $this->update($nation_code, $userData->id, $du);
        //         $userData->is_online = '1';
        //     }
        // }
        return $userData;
    }

    public function getByTokenIgnoreIsActive($nation_code, $token, $kind="api_web")
    {
        if (strlen($token)<=4) {
            return new stdClass();
        }
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
        $this->db->where($kind.'_token', $token);
        // return $this->db->get_first('object', 0);
        $userData = $this->db->get_first('object', 0);

        // if(isset($userData->id)){
        //     if($userData->is_active == 1){
        //         $du = array();
        //         $du['is_online'] = 1;
        //         $du['last_online'] = date('Y-m-d H:i:s');
        //         $this->update($nation_code, $userData->id, $du);
        //         $userData->is_online = '1';
        //     }
        // }
        return $userData;
    }

    public function setAgree($id)
    {
        $du = array('is_agree'=>'1');
        return $this->db->where("id", $id)->update($this->tbl, $du);
    }

    public function register($di=array())
    {
        $this->db->flushQuery();
        if (isset($di['fnama'])) {
            if (strlen($di['fnama'])) {
                $di['fnama'] = $this->__encrypt($di['fnama']);
            }
        }
        if (isset($di['lnama'])) {
            if (strlen($di['lnama'])) {
                $di['lnama'] = $this->__encrypt($di['lnama']);
            }
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
        if (isset($di['register_place_alamat2'])) {
            if (strlen($di['register_place_alamat2'])) {
                $di['register_place_alamat2'] = $this->__encrypt($di['register_place_alamat2']);
            }
        }
        if (isset($di['band_fnama'])) {
            if (strlen($di['band_fnama'])) {
                $di['band_fnama'] = $this->__encrypt($di['band_fnama']);
            }
        }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    public function update($nation_code, $id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        if (isset($du['fnama'])) {
            if (strlen($du['fnama'])) {
                $du['fnama'] = $this->__encrypt($du['fnama']);
            }
        }
        if (isset($du['lnama'])) {
            if (strlen($du['lnama'])) {
                $du['lnama'] = $this->__encrypt($du['lnama']);
            }
        }
        if (isset($du['email'])) {
            if (strlen($du['email'])) {
                $du['email'] = $this->__encrypt($du['email']);
            }
        }
        if (isset($du['telp'])) {
            if (strlen($du['telp'])) {
                $du['telp'] = $this->__encrypt($du['telp']);
            }
        }
        if (isset($du['band_fnama'])) {
            if (strlen($du['band_fnama'])) {
                $du['band_fnama'] = $this->__encrypt($du['band_fnama']);
            }
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }

    //by Donny Dennison - 12 july 2022 14:56
    //new offer system
    public function updateTotal($nation_code, $b_user_id, $parameter, $operator, $total)
    {
        return $this->db->exec("UPDATE `$this->tbl` SET $parameter = IF('$operator' = '+', $parameter $operator $total, IF($parameter <= 0,0,$parameter $operator $total))
            WHERE nation_code = '$nation_code' AND id = '$b_user_id';");
    }

    public function updateTotalAndBDate($nation_code, $b_user_id, $parameter1, $operator1, $total1, $parameter2, $bdate2)
    {
        return $this->db->exec("UPDATE `$this->tbl` SET $parameter1 = IF('$operator1' = '+', $parameter1 $operator1 $total1, IF($parameter1 <= 0,0,$parameter1 $operator1 $total1)), $parameter2 = '$bdate2'
            WHERE nation_code = '$nation_code' AND id = '$b_user_id';");
    }

    public function flushApisessAndFcmToken($nation_code, $apisess)
    {
        $du = array();
        $du['api_mobile_token'] = '';
        $du['fcm_token'] = '';
        //request uncomment from mr jackie(7 nov 2023 14:59 by verbal)
        $du['is_online'] = 0;
       
        $this->db->where("nation_code", $nation_code);
        $this->db->where("api_mobile_token", $apisess);
        return $this->db->update($this->tbl, $du, 0);
    }

    public function getByEmail($nation_code, $email)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as("COALESCE($this->tbl_as.fb_id,'-')", "fb_id", 0);
        $this->db->select_as("COALESCE($this->tbl_as.apple_id,'-')", "apple_id", 0);
        $this->db->select_as("COALESCE($this->tbl_as.google_id,'-')", "google_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanenet user feature
        // $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_permanent_inactive", $this->db->esc(1));

        $this->db->where_as($this->__decrypt("$this->tbl_as.email"), $this->db->esc($email));
        return $this->db->get_first();
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

    public function getByIds($nation_code, $ids)
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
        $this->db->where_in("id", $ids);
        return $this->db->get();
    }

    public function getByEmailAndSocialID($email, $social_id)
    {
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama");
        $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama");
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email");
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp");
        $this->db->where($this->__decrypt("email"), $email);
        $this->db->where("fb_id", $social_id, 'or', 'like', 1, 0);
        $this->db->where("google_id", $social_id, 'or', 'like', 0, 1);
        $d = $this->db->get_first();
        if (isset($d->id)) {
            return $d;
        }
        return new stdClass();
    }

    public function getKode($a_company_inisial, $a_company_id="", $fnama="")
    {
        $a_company_inisial = strtoupper($a_company_inisial);
        $kode = $a_company_inisial;
        if (strlen($fnama)>0) {
            $fnama = strtoupper($fnama);
            $kode = $a_company_inisial.''.$fnama[0];
        }
        $this->db->flushQuery();
        $this->db->select_as('COUNT(*) total, CAST(COALESCE(SUBSTRING(kode,4),0) AS UNSIGNED)+1', 'urutan', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where('kode', $kode, 'and', 'like%');
        $this->db->order_by('CAST(COALESCE(SUBSTRING(kode,4),0) AS UNSIGNED)', 'desc');
        if (strlen($a_company_id)>0) {
            if (strtolower($a_company_id)=='null') {
                $this->db->where_as('COALESCE(a_company_id,"-")', $this->db->esc('-'), 'and', '=');
            } else {
                $this->db->where('a_company_id', $a_company_id, 'and', '=');
            }
        }
        return $this->db->get_first('object', 0);
    }

    public function getKodeOnline($fnama_inisial)
    {
        $this->db->flushQuery();
        $this->db->select_as('CAST(SUBSTRING(kode,3) AS UNSIGNED)+1', 'urutan', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where('kode', $fnama_inisial, 'and', 'like%');
        $this->db->order_by('CAST(SUBSTRING(kode,3) AS UNSIGNED)+1', 'desc');
        return $this->db->get_first('object', 0);
    }

    public function flushFcm($fcm_token="")
    {
        if (strlen($fcm_token)>50) {
            $sql = 'UPDATE `'.$this->tbl.'` SET fcm_token = "" WHERE fcm_token LIKE "'.$fcm_token.'"';
            $this->db->exec($sql);
        }
    }

    public function auth_sosmed($nation_code, $fb_id, $google_id, $apple_id, $email, $telp)
    {
        $this->db->select("*");
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as("COALESCE(`api_web_token`,'-')", 'api_web_token', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), 'AND', 'LIKE', 0, 0);
        $this->db->where_as("$this->tbl_as.fb_id", $this->db->esc($fb_id), 'OR', 'LIKE', 1, 0);
        $this->db->where_as("$this->tbl_as.apple_id", $this->db->esc($apple_id), 'OR', 'LIKE', 0, 0);
        $this->db->where_as("$this->tbl_as.google_id", $this->db->esc($google_id), 'AND', 'LIKE', 0, 1);
        $this->db->where_as($this->__decrypt("$this->tbl_as.email"), $this->db->esc($email), 'OR', 'LIKE', 1, 0);
        $this->db->where_as($this->__decrypt("$this->tbl_as.telp"), $this->db->esc($telp), 'AND', 'LIKE', 0, 1);

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanenet user feature
        //by Donny Dennison - 14 december 2022 11:55
        //delete permanent user cannot register anymore
        // $this->db->where_as("$this->tbl_as.is_permanent_inactive", $this->db->esc(1));

        $this->db->order_by("id", "ASC");
        return $this->db->get_first('', 0);
    }

    public function checkEmail($nation_code, $email)
    {
        $this->db->select_as("*,COALESCE(google_id,'NULL')", "google_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as("COALESCE(fb_id,'NULL')", "fb_id", 0);
        $this->db->select_as("COALESCE(apple_id,'NULL')", "apple_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("nation_code", $this->db->esc($nation_code), "AND", "LIKE");
        $this->db->where_as($this->__decrypt("email"), $this->db->esc($email), "AND", "LIKE");

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanenet user feature
        // $this->db->where_as("$this->tbl_as.is_permanent_inactive", $this->db->esc(1));

        $this->db->order_by("id", "asc");
        return $this->db->get_first('', 0);
    }

    //START by Donny Dennison - 14 december 2022 11:55
    //delete permanent user cannot register anymore
    public function checkEmailIgnoreActive($nation_code, $email)
    {
        $this->db->select_as("*,COALESCE(google_id,'NULL')", "google_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as("COALESCE(fb_id,'NULL')", "fb_id", 0);
        $this->db->select_as("COALESCE(apple_id,'NULL')", "apple_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("nation_code", $this->db->esc($nation_code), "AND", "LIKE");
        $this->db->where_as($this->__decrypt("email"), $this->db->esc($email), "AND", "LIKE");
        // $this->db->where_as("$this->tbl_as.is_permanent_inactive", $this->db->esc(1));

        $this->db->order_by("id", "asc");
        return $this->db->get_first('', 0);
    }
    //END by Donny Dennison - 14 december 2022 11:55
    //delete permanent user cannot register anymore

    public function checkTelp($nation_code, $telp)
    {
        $this->db->select_as("*,COALESCE(google_id,'NULL')", "google_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as("COALESCE(fb_id,'NULL')", "fb_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("nation_code", $this->db->esc($nation_code), "AND", "LIKE");
        $this->db->where_as($this->__decrypt("telp"), $this->db->esc($telp), "AND", "LIKE");

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanenet user feature
        $this->db->where_as("$this->tbl_as.is_permanent_inactive", $this->db->esc(1));

        $this->db->order_by("id", "asc");
        return $this->db->get_first('', 0);
    }

    //START by Donny Dennison - 14 december 2022 11:55
    //delete permanent user cannot register anymore
    public function checkTelpIgnoreActive($nation_code, $telp)
    {
        $this->db->select_as("*,COALESCE(google_id,'NULL')", "google_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as("COALESCE(fb_id,'NULL')", "fb_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("nation_code", $this->db->esc($nation_code), "AND", "LIKE");
        $this->db->where_as($this->__decrypt("telp"), $this->db->esc($telp), "AND", "LIKE");
        // $this->db->where_as("$this->tbl_as.is_permanent_inactive", $this->db->esc(1));

        $this->db->order_by("id", "asc");
        return $this->db->get_first('', 0);
    }
    //END by Donny Dennison - 14 december 2022 11:55
    //delete permanent user cannot register anymore

    public function checkEmailTelp($nation_code, $email, $telp)
    {
        $this->db->select_as("*,COALESCE(google_id,'NULL')", "google_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as("COALESCE(fb_id,'NULL')", "fb_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("nation_code", $this->db->esc($nation_code), "AND", "LIKE");
        $this->db->where_as($this->__decrypt("email"), $this->db->esc($email), 'AND', 'LIKE', 1, 0);
        $this->db->where_as($this->__decrypt("telp"), $this->db->esc($telp), 'AND', 'LIKE', 0, 1);

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanenet user feature
        $this->db->where_as("$this->tbl_as.is_permanent_inactive", $this->db->esc(1));

        $this->db->order_by("id", "asc");
        return $this->db->get_first('', 0);
    }

    public function checkEmailTelpIgnoreActive($nation_code, $email, $telp)
    {
        $this->db->select_as("*,COALESCE(google_id,'NULL')", "google_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as("COALESCE(fb_id,'NULL')", "fb_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("nation_code", $this->db->esc($nation_code), "AND", "LIKE");
        $this->db->where_as($this->__decrypt("email"), $this->db->esc($email), 'AND', 'LIKE', 1, 0);
        $this->db->where_as($this->__decrypt("telp"), $this->db->esc($telp), 'AND', 'LIKE', 0, 1);
        // $this->db->where_as("$this->tbl_as.is_permanent_inactive", $this->db->esc(1));

        $this->db->order_by("id", "asc");
        return $this->db->get_first('', 0);
    }

    public function checkFBID($nation_code, $fb_id)
    {
        $this->db->select_as("*,COALESCE(google_id,'NULL')", "google_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as("COALESCE(fb_id,'NULL')", "fb_id", 0);
        $this->db->select_as("COALESCE(apple_id,'NULL')", "apple_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("nation_code", $this->db->esc($nation_code), "AND", "LIKE");
        $this->db->where_as("COALESCE(fb_id,'-')", $this->db->esc($fb_id), 'AND', 'LIKE', 0, 0);

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanenet user feature
        // $this->db->where_as("$this->tbl_as.is_permanent_inactive", $this->db->esc(1));

        $this->db->order_by("id", "asc");
        return $this->db->get_first('', 0);
    }

    public function checkAppleID($nation_code, $apple_id)
    {
        $this->db->select_as("*,COALESCE(google_id,'NULL')", "google_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as("COALESCE(fb_id,'NULL')", "fb_id", 0);
        $this->db->select_as("COALESCE(apple_id,'NULL')", "apple_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("nation_code", $this->db->esc($nation_code), "AND", "LIKE");
        $this->db->where_as("COALESCE(apple_id,'-')", $this->db->esc($apple_id), 'AND', 'LIKE', 0, 0);

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanenet user feature
        // $this->db->where_as("$this->tbl_as.is_permanent_inactive", $this->db->esc(1));

        $this->db->order_by("id", "asc");
        return $this->db->get_first('', 0);
    }

    public function checkGoogleID($nation_code, $google_id)
    {
        $this->db->select_as("*,COALESCE(google_id,'NULL')", "google_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as("COALESCE(fb_id,'NULL')", "fb_id", 0);
        $this->db->select_as("COALESCE(apple_id,'NULL')", "apple_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("nation_code", $this->db->esc($nation_code), "AND", "LIKE");
        $this->db->where_as("COALESCE(google_id,'-')", $this->db->esc($google_id), 'AND', 'LIKE', 0, 0);

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanenet user feature
        // $this->db->where_as("$this->tbl_as.is_permanent_inactive", $this->db->esc(1));

        $this->db->order_by("id", "asc");
        return $this->db->get_first('', 0);
    }

    public function detail($nation_code, $id)
    {
        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.id", "b_user_id", 0);
        $this->db->select_as("$this->tbl_as.id", "b_user_id_seller", 0);
        // $this->db->select_as("$this->tbl_as.bio", "bio", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl_as.lnama"), "lnama", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl_as.email"), "email", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->select_as("'0'", "rating", 0);
        $this->db->select_as("$this->tbl_as.image", "image", 0);
        $this->db->select_as("$this->tbl_as.is_online", "is_online", 0);
        $this->db->select_as("$this->tbl_as.is_admin", "is_admin", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=");
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id), "AND", "=");
        return $this->db->get_first('', 0);
    }
    public function flushFcmToken($fcm_token_old)
    {
        $du = array("fcm_token"=>'');
        $this->db->where("fcm_token", $fcm_token_old, 'AND', 'like%');
        return $this->db->update($this->tbl, $du, 0);
    }

    // public function getFcmToken($nation_code, $userid)
    // {
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
    //     $this->db->select_as("$this->tbl_as.fcm_token", "fcm_token", 0);
    //     $this->db->select_as("$this->tbl_as.device", "device", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($userid));
    //     // $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
    //     $this->db->group_by("$this->tbl_as.id");
    //     return $this->db->get('',0);
    // }

    // public function getFcmTokenSeller($nation_code, $sellerid)
    // {
    //     $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
    //     $this->db->select_as($this->__decrypt("$this->tbl_as.fnama"), "fnama", 0);
    //     $this->db->select_as("$this->tbl_as.fcm_token", "fcm_token", 0);
    //     $this->db->select_as("$this->tbl_as.device", "device", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("nation_code", $nation_code);
    //     $this->db->where_as("$this->tbl_as.id", $this->db->esc($sellerid));
    //     return $this->db->get_first();
    // }

    //by Donny Dennison - 6 september 2022 17:50
    //integrate api blockchain
    public function checkWalletCode($nation_code, $user_wallet_code)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.user_wallet_code", $this->db->esc($user_wallet_code));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function checkWalletCodeNew($nation_code, $user_wallet_code_new)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.user_wallet_code_new", $this->db->esc($user_wallet_code_new));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    //by Donny Dennison - 12 september 2022 14:59
    //kode referral
    public function checkKodeReferral($nation_code, $kode_referral)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.kode_referral", $this->db->esc($kode_referral));
        return $this->db->get_first("object", 0);
    }

    //START by Donny Dennison - 5 october 2022 15:47
    //activity dashboard feature
    public function countRecruited($nation_code, $id, $dateCompare)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id_recruiter", $this->db->esc($id));
        $this->db->where_as("DATE($this->tbl_as.cdate)", $this->db->esc($dateCompare), "OR", "=" , 1, 0);
        $this->db->where_as("DATE($this->tbl_as.blockchain_latereferralrewardtransaction_api_called_cdate)", $this->db->esc($dateCompare), "AND", "=" , 0, 1);
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return "0";
    }
    //END by Donny Dennison - 5 october 2022 15:47
    //activity dashboard feature

    //START by Donny Dennison - 11 november 2022 16:30
    //integrate api blockchain
    public function checkMainTransactionId($nation_code, $main_transaction_id)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.main_transaction_id", $this->db->esc($main_transaction_id));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }
    //END by Donny Dennison - 11 november 2022 16:30
    //integrate api blockchain

    public function countbyIpAddress($nation_code, $ip_address)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.ip_address", $this->db->esc($ip_address));
        $this->db->where_in("$this->tbl_as.register_from", array("online", "phone", "undentified"), 1 , "AND");
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getByIdData($nation_code, $id)
    {
        $this->db->select_as("$this->tbl_as.user_wallet_code", "user_wallet_code");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $id);
        return $this->db->get();
    }

    public function checkByIpAddressDate($nation_code, $ip_address, $register_from)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.ip_address", $this->db->esc($ip_address));
        $this->db->where_as("$this->tbl_as.register_from", $this->db->esc($register_from));
        $this->db->where_as("DATE_ADD($this->tbl_as.cdate, INTERVAL 40 SECOND)", "NOW()", "AND", ">=");
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
        // $sql ="SELECT COUNT(*) AS jumlah FROM `".$this->tbl."` WHERE nation_code = '".$nation_code."' AND ip_address = '".$ip_address."' AND register_from = '".$register_from."' AND DATE_ADD(cdate, INTERVAL 40 SECOND) >= NOW() FOR UPDATE;";
        // return $this->db->query($sql)[0]->jumlah;
    }

    public function checkByFcmTokenDate($nation_code, $fcm_token, $register_from)
    {
        $fcm_token = strstr($fcm_token, ':');
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("SUBSTR($this->tbl_as.fcm_token, LOCATE(':', $this->tbl_as.fcm_token))", $this->db->esc($fcm_token));
        $this->db->where_as("$this->tbl_as.register_from", $this->db->esc($register_from));
        $this->db->where_as("DATE_ADD($this->tbl_as.cdate, INTERVAL 40 SECOND)", "NOW()", "AND", ">=");
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
        // $sql ="SELECT COUNT(*) AS jumlah FROM `".$this->tbl."` WHERE nation_code = '".$nation_code."' AND fcm_token = '".$fcm_token."' AND register_from = '".$register_from."' AND DATE_ADD(cdate, INTERVAL 2 MINUTE) >= NOW() FOR UPDATE;";
        // return $this->db->query($sql)[0]->jumlah;
    }

    public function getForRegisterBefore($nation_code, $register_from)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.register_from", $this->db->esc($register_from));
        $this->db->where_as("DATE_ADD($this->tbl_as.cdate, INTERVAL 2 SECOND)", "NOW()", "AND", ">=");
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
        // $sql ="SELECT COUNT(*) AS jumlah FROM `".$this->tbl."` WHERE nation_code = '".$nation_code."' AND register_from = '".$register_from."' AND DATE_ADD(cdate, INTERVAL 2 SECOND) >= NOW() FOR UPDATE;";
        // return $this->db->query($sql)[0]->jumlah;
    }

    public function checkId($nation_code, $id)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function checkByFcmTokenRecommender($nation_code, $fcm_token, $recommenderId)
    {
        $fcm_token = strstr($fcm_token, ':');
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("SUBSTR($this->tbl_as.fcm_token, LOCATE(':', $this->tbl_as.fcm_token))", $this->db->esc($fcm_token));
        $this->db->where_as("$this->tbl_as.b_user_id_recruiter", $this->db->esc($recommenderId));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function checkByIpAddressRecommender($nation_code, $ip_address, $recommenderId)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.ip_address", $this->db->esc($ip_address));
        $this->db->where_as("$this->tbl_as.b_user_id_recruiter", $this->db->esc($recommenderId));
        $this->db->where_as("$this->tbl_as.fcm_token", $this->db->esc(""), "AND", "!=");
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getByUserWalletCodeNew($nation_code, $user_wallet_code_new)
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
        $this->db->where_as("$this->tbl_as.user_wallet_code_new", $this->db->esc($user_wallet_code_new));
        return $this->db->get_first();
    }
}
