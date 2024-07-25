<?php
class Leaderboard extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        // $this->load("api_mobile/g_leaderboard_point_area_model", 'glpam');
        $this->load("api_mobile/g_leaderboard_ranking_model", 'glrm');
 
        //by Donny Dennison - 6 december 2021 17:02
        //add weather api
        $this->load("api_mobile/b_user_model", "bu");
        $this->load("api_mobile/b_user_alamat_model", 'bua');
        // $this->load("api_mobile/g_air_quality_index_model", "gaqim");

    }
    
    private function __page($page)
    {
        if (!is_int($page)) {
          $page = (int) $page;
        }
        if ($page<=0) {
          $page = 1;
        }
        return $page;
    }

    private function __pageSize($page_size)
    {
        $page_size = (int) $page_size;
        if ($page_size<=0) {
          $page_size = 20;
        }
        return $page_size;
    }

    public function index()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['leaderboard_total'] = '0';
        $data['leaderboards'] = array();
        $data['leaderboard_my_ranking'] = 'N/A';
        $data['leaderboard_my_point'] = '0';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
          $this->status = 101;
          $this->message = 'Missing or invalid nation_code';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "leaderboard");
          die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
          $this->status = 400;
          $this->message = 'Missing or invalid API key';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "leaderboard");
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

        $provinsi = trim($this->input->get("provinsi"));

        // $type = $this->input->get("type");
        // if (strlen($type)<=0 || empty($type)){
          $type="All";
        // }

        // if($type == 'sameStreet'){
        //     $type = 'neighborhood';
        // }

        $kelurahan = 'All';
        $kecamatan = 'All';
        $kabkota = 'All';

        // if (!$provinsi || $provinsi == "") {
        //     if(isset($pelanggan->id)){

        //         $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

        //         if($type == 'neighborhood'){

        //             $kelurahan = $pelangganAddress->kelurahan;
        //             $kecamatan = $pelangganAddress->kecamatan;
        //             $kabkota = $pelangganAddress->kabkota;
        //             $provinsi = $pelangganAddress->provinsi;

        //         }else if($type == 'district'){

        //             $kecamatan = $pelangganAddress->kecamatan;
        //             $kabkota = $pelangganAddress->kabkota;
        //             $provinsi = $pelangganAddress->provinsi;

        //         }else if($type == 'city'){
                    
        //             $kabkota = $pelangganAddress->kabkota;
        //             $provinsi = $pelangganAddress->provinsi;

        //         }else if($type == 'province'){
                    
        //             $provinsi = $pelangganAddress->provinsi;

        //         }else{
                    
        //             $provinsi = 'All';

        //         }

        //     }else{
                $provinsi = 'All';
        //     }
        // }

        //populate input get
        // $page = $this->input->get("page");
        $page_size = $this->input->get("page_size");

        $page = $this->__page(1);
        $page_size = $this->__pageSize($page_size);

        $data['leaderboard_total'] = $this->glrm->countAll($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi);

        $data['leaderboards'] = $this->glrm->getAll($nation_code, $page, $page_size, $kelurahan, $kecamatan, $kabkota, $provinsi);

        foreach ($data['leaderboards'] as &$pd) {

            if (isset($pd->b_user_image)) {
                if(file_exists(SENEROOT.$pd->b_user_image) && $pd->b_user_image != 'media/user/default.png'){
                  $pd->b_user_image = $this->cdn_url($pd->b_user_image);
                } else {
                  $pd->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }
            
        }

        if(isset($pelanggan->id)){

            $findRanking = $this->glrm->getByUserId($nation_code, $pelanggan->id, $kelurahan, $kecamatan, $kabkota, $provinsi);

            if(isset($findRanking->ranking)){
                if($findRanking->total_point != 0){
                    $data['leaderboard_my_ranking'] = $findRanking->ranking;
                    $data['leaderboard_my_point'] = $findRanking->total_point;
                }
            }
            unset($findRanking);

        }

        //response
        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "leaderboard");
    }

}
