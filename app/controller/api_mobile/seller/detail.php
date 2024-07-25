<?php
/**
 * API_Mobile/Seller/Detail
 *   view detailed information for seller
 */
class Detail extends JI_Controller
{
    public $is_log = 1;

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_mobile/b_user_model", 'bu');
        $this->load("api_mobile/b_user_alamat_model", "bua");
        $this->load("api_mobile/d_order_detail_model", 'dodm');
        $this->load("api_mobile/d_order_detail_item_model", 'dodim');
        $this->load("api_mobile/e_rating_model", 'erm');
    }
    
    /**
     * View seller detail by id
     * @param  integer $b_user_id ID from table b_user
     * @return [type]            [description]
     */
    public function index($b_user_id="")
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['seller'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_detail");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_detail");
            die();
        }

        // check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $pelanggan = new stdClass();
            if($nation_code == 62){ //indonesia
                $pelanggan->language_id = 2;
            }else if($nation_code == 82){ //korea
                $pelanggan->language_id = 3;
            }else if($nation_code == 66){ //thailand
                $pelanggan->language_id = 4;
            }else {
                $pelanggan->language_id = 1;
            }
        }

        // $b_user_id = (int) $b_user_id;
        if ($b_user_id<='0') {
            $this->status = 571;
            $this->message = 'Missing or invalid B_USER_ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_detail");
            die();
        }

        //get seller profile
        $order_stats = $this->dodm->getSellerStats($nation_code, $b_user_id);
        $rating_stats = $this->erm->getSellerStats($nation_code, $b_user_id);
        $rating_stats2 = $this->erm->getBuyerStats($nation_code, $b_user_id);
        $seller = $this->bu->detail($nation_code, $b_user_id);

        //seller image
        $seller->image = $this->cdn_url($seller->image);

        //fill default value
        $seller->sales_count = 0;
        $seller->sales_qty = 0;
        $seller->sales_sum = 0;
        $seller->rating = 0;
        $seller->rating_count = 0;
        $seller->rating_total = 0;
        $seller->rating_max = 5;

        $default_address = $this->bua->getByUserIdDefault($nation_code, $b_user_id);

        if(isset($default_address->alamat2)){

            $seller->default_address = $default_address->alamat2;
            $seller->kelurahan = $default_address->kelurahan;
            $seller->kecamatan = $default_address->kecamatan;
            $seller->kabkota = $default_address->kabkota;
            $seller->provinsi = $default_address->provinsi;

        }else{

            $seller->default_address = '';
            $seller->kelurahan = '';
            $seller->kecamatan = '';
            $seller->kabkota = '';
            $seller->provinsi = '';

        }

        //put
        if (isset($order_stats->sales_count)) {
            $seller->sales_count = (int) $order_stats->sales_count;
        }
        if (isset($order_stats->sales_qty)) {
            $seller->sales_qty = (int) $order_stats->sales_qty;
        }
        if (isset($order_stats->sales_sum)) {
            $seller->sales_sum = (int) $order_stats->sales_sum;
        }
        if (isset($rating_stats->count)) {
            $seller->rating_count = (int) $rating_stats->count;
        }
        if (isset($rating_stats->rating)) {
            $seller->rating_total = (int) $rating_stats->rating;
        }

        //calculate rating as seller
        if ($seller->rating_count>0 && $seller->rating_total>0) {
            $seller->rating = floor($seller->rating_total/$seller->rating_count);
        }

        //calculate rating as buyer
        if (isset($rating_stats2->count)) {
            $seller->rating_count = (int) $rating_stats->count;
        }
        if (isset($rating_stats2->rating)) {
            $seller->rating_total = (int) $rating_stats->rating;
        }
        if ($seller->rating_count>0 && $seller->rating_total>0) {
            $buyer = floor($seller->rating_total/$seller->rating_count);
            $seller->rating = ($seller->rating + $buyer) / 2;
        }

        $seller->rating = (string) $seller->rating;

        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/seller/Detail::index -> BUID: $b_user_id RATING: ".$seller->rating);
        }


        //object seller
        $data['seller'] = $seller;
        unset($seller);

        //default response
        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_detail");
    }
}
