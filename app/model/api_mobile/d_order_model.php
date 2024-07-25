<?php
class D_Order_Model extends JI_Model
{
    public $tbl = 'd_order';
    public $tbl_as = 'dor';
    public $tbl10 = 'b_user';
    public $tbl10_as = 'bu';
    // public $tbl11 = 'b_user_alamat';
    // public $tbl11_as = 'bua1';
    // public $tbl12 = 'b_user_alamat';
    // public $tbl12_as = 'bua2';
    // public $tbl21 = 'b_lokasi';
    // public $tbl21_as = 'blok1';
    // public $tbl22 = 'b_kodepos';
    // public $tbl22_as = 'bkp1';
    // public $tbl31 = 'b_lokasi';
    // public $tbl31_as = 'blok2';
    // public $tbl32 = 'b_kodepos';
    // public $tbl32_as = 'bkp2';

    public function __construct()
    {
        parent::__construct();
        $this->is_cacheable = 0;
        $this->db->from($this->tbl, $this->tbl_as);
    }
    private function __joinTbl10()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl10_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl10_as.id");
        return $cps;
    }
    // private function __joinTbl11()
    // {
    //     $cps = array();
    //     $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl11.nation_code");
    //     $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl11.b_user_id");
    //     $cps[] = $this->db->composite_create("$this->tbl_as.b_user_alamat_id_billing", "=", "$this->tbl11.id");
    //     return $cps;
    // }
    // private function __joinTbl12()
    // {
    //     $cps = array();
    //     $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl12.nation_code");
    //     $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl12.b_user_id");
    //     $cps[] = $this->db->composite_create("$this->tbl_as.b_user_alamat_id_billing", "=", "$this->tbl12.id");
    //     return $cps;
    // }
    // private function __joinTbl21()
    // {
    //     $cps = array();
    //     $cps[] = $this->db->composite_create("$this->tbl11_as.nation_code", "=", "$this->tbl21.nation_code");
    //     $cps[] = $this->db->composite_create("$this->tbl11_as.b_lokasi_id", "=", "$this->tbl21.id");
    //     return $cps;
    // }
    // private function __joinTbl22()
    // {
    //     $cps = array();
    //     $cps[] = $this->db->composite_create("$this->tbl11_as.nation_code", "=", "$this->tbl22.nation_code");
    //     $cps[] = $this->db->composite_create("$this->tbl11_as.b_kodepos_id", "=", "$this->tbl22.id");
    //     return $cps;
    // }
    // private function __joinTbl31()
    // {
    //     $cps = array();
    //     $cps[] = $this->db->composite_create("$this->tbl11_as.nation_code", "=", "$this->tbl31.nation_code");
    //     $cps[] = $this->db->composite_create("$this->tbl11_as.b_lokasi_id", "=", "$this->tbl31.id");
    //     return $cps;
    // }
    // private function __joinTbl32()
    // {
    //     $cps = array();
    //     $cps[] = $this->db->composite_create("$this->tbl11_as.nation_code", "=", "$this->tbl32.nation_code");
    //     $cps[] = $this->db->composite_create("$this->tbl11_as.b_kodepos_id", "=", "$this->tbl32.id");
    //     return $cps;
    // }
    
    public function getLastId($nation_code)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }
    public function countIncompleted($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("order_status", "incompleted");
        $d = $this->db->get_first('', 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }
    public function getPendings($nation_code, $b_user_id)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("order_status", "pending");
        $this->db->order_by("id", "desc");
        return $this->db->get('', 0);
    }
    public function countPending($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("order_status", "pending");
        $d = $this->db->get_first('', 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }
    public function getPending($nation_code, $b_user_id, $id)
    {
        $this->db->select_as("$this->tbl_as.nation_code", 'nation_code', 0);
        $this->db->select_as("$this->tbl_as.id", 'id', 0);
        $this->db->select_as("$this->tbl_as.b_user_id", 'b_user_id_buyer', 0);
        $this->db->select_as("$this->tbl_as.cdate", 'cdate', 0);
        $this->db->select_as("$this->tbl_as.cdate", 'd_order_cdate', 0);
        $this->db->select_as("$this->tbl_as.invoice_code", 'invoice_code', 0);
        $this->db->select_as("$this->tbl_as.item_total", 'item_total', 0);
        $this->db->select_as("$this->tbl_as.sub_total", 'sub_total', 0);
        $this->db->select_as("$this->tbl_as.grand_total", 'grand_total', 0);
        $this->db->select_as("$this->tbl_as.order_status", 'order_status', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("id", $id);
        $this->db->where("order_status", "pending");
        $this->db->order_by("id", "desc");
        return $this->db->get_first('', 0);
    }
    public function getIncompleted2($nation_code, $b_user_id)
    {
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("order_status", "incompleted");
        $this->db->order_by("id", "desc");
        return $this->db->get_first('', 0);
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
    public function updateByUserAndOrder($nation_code, $b_user_id, $id, $du)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
    }
    public function del($nation_code, $b_user_id, $id)
    {
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->where("id", $id);
        return $this->db->delete($this->tbl);
    }
    public function getDetail($nation_code, $b_user_id, $id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("b_user_id", $b_user_id);
        $this->db->order_by("id", "desc");
        return $this->db->get_first('', 0);
    }
    public function getTotalOrderCount($nation_code)
    {
        $this->db->select_as("COALESCE(COUNT(*),0)+1", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->where("nation_code", $nation_code);
        $this->where_as("DATE(ldate)", "CURRENT_DATE()");
        $d = $this->db->get_first();
        if (isset($d->id)) {
            return $d->id;
        }
        return 0;
    }
    public function getByIdUserid($nation_code, $b_user_id, $d_order_id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.cdate", "d_order_cdate", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($d_order_id));
        return $this->db->get_first();
    }
    public function countWaitingToday($nation_code)
    {
        $this->db->select_as("CAST(SUBSTRING(invoice_code,15) AS UNSIGNED)+1", "last_code", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("DATE(cdate)", "CURRENT_DATE()", "AND", "=");
        $this->db->where_as("nation_code", $this->db->esc($nation_code));
        $this->db->order_by("invoice_code", "DESC");
        $d = $this->db->get_first('', 0);
        if (isset($d->last_code)) {
            return (int) $d->last_code;
        }
        return 0;
    }
    public function getById($nation_code, $id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl_as.id", "d_order_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("nation_code", $this->db->esc($nation_code));
        $this->db->where_as("id", $this->db->esc($id));
        return $this->db->get_first();
    }

    // EDIT By: Aditya Adi Prabowo 16 July 2020 14.23
    // Case for dynamic Selling Fee When Reject Item
    public function getByIdForReject($nation_code, $d_order_id)
    {
        $this->db->select_as("$this->tbl_as.nation_code", 'nation_code', 0);
        $this->db->select_as("$this->tbl_as.id", 'id', 0);
        $this->db->select_as("$this->tbl_as.sub_total", 'sub_total', 0);
        $this->db->select_as("$this->tbl_as.pg_fee", 'pg_fee', 0);
        $this->db->select_as("$this->tbl_as.pg_fee_vat", 'pg_fee_vat', 0);
        $this->db->select_as("$this->tbl_as.profit_amount", 'profit_amount', 0);
        $this->db->select_as("$this->tbl_as.selling_fee", 'selling_fee', 0);
        $this->db->select_as("$this->tbl_as.selling_fee2", 'selling_fee2', 0);
        $this->db->select_as("$this->tbl_as.refund_amount", 'refund_amount', 0);
        
        //by Donny Dennison - 1 august 2020 15:04
        //bug fixing selling fee for rejected product
        $this->db->select_as("$this->tbl_as.selling_fee_percent", 'selling_fee_percent', 0);
        
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("nation_code", $this->db->esc($nation_code));
        $this->db->where_as("id", $this->db->esc($d_order_id));
        return $this->db->get_first();
    }

    public function updateRefund($nation_code, $d_order_id, $value_refund_amount)
    {
        if (!is_array($value_refund_amount)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $d_order_id);
        return $this->db->update($this->tbl, $value_refund_amount, 0);
    }
    public function updateSellingFee($nation_code, $d_order_id, $value_selling_fee2)
    {
        if (!is_array($value_selling_fee2)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $d_order_id);
        return $this->db->update($this->tbl, $value_selling_fee2, 0);
    }
    public function updateReject($nation_code, $d_order_id, $orderreject)
    {
        if (!is_array($orderreject)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where("id", $d_order_id);
        return $this->db->update($this->tbl, $orderreject, 0);
    }

    public function getOrderBuyerById($nation_code, $id)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.cdate", "d_order_cdate", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id_buyer", 0);
        $this->db->select_as($this->__decrypt("$this->tbl10_as.fnama"), "b_user_fnama_buyer", 0);
        $this->db->select_as($this->__decrypt("$this->tbl10_as.email"), "b_user_email_buyer", 0);
        $this->db->select_as($this->__decrypt("$this->tbl10_as.telp"), "b_user_telp_buyer", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'inner');
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $this->db->group_by("$this->tbl_as.id");
        return $this->db->get_first();
    }
    public function setPending2Cancel($nation_code, $b_user_id)
    {
        $du = array();
        $du['order_status'] = 'cancelled';
        $this->db->where('nation_code', $nation_code);
        $this->db->where('b_user_id', $b_user_id);
        $this->db->where('order_status', 'pending');
        return $this->db->update($this->tbl, $du);
    }

    //by Donny Dennison - 2 november 2021 13:45
    //payment call 2c2p in api for flutter version
    public function getByPaymentTranId($nation_code, $invoice_code)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.cdate", "d_order_cdate", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("LOWER($this->tbl_as.payment_tranid)", $this->db->esc(strtolower($invoice_code)));
        return $this->db->get_first();
    }

}
