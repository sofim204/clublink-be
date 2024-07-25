<?php
class Airdropv2 extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_cron/b_user_model", "bum");
    }

    public function index()
    {
        $this->seme_log->write("api_cron", 'API_Cron/Airdropv2::index start');
        // $this->bum->trans_start();

        $nation_code = 62;

        $users = $this->bum->getForAirdropv2($nation_code);
        if (count($users)>0) {
            $postdatas = array();
            $b_user_ids = array();
            foreach ($users as $user) {
                if($user->language_id == 2){
                    $language = 'id';
                }else{
                    $language = 'en';
                }

                $postdatas[] = array(
                  'userWalletCode' => $user->user_wallet_code_new,
                  'bbtGift' => rand(1,2),
                  'countryIsoCode' => strtolower($this->blockchain_api_country),
                  'LanguageIsoCode' => $language,
                  'signupUtcDate' => $user->cdate
                );
                $b_user_ids[] = $user->id;
            }

            $postdata = array(
                "userWalletList" => $postdatas
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_URL, $this->blockchain_new_api_host."api/airdropv2");

            $headers = array();
            $headers[] = 'Content-Type:  application/json';
            $headers[] = 'Accept:  application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
              return 0;
              //echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);

            $this->seme_log->write("api_cron", "url untuk block chain server ". $this->blockchain_new_api_host."api/airdropv2. data send to blockchain api ". json_encode($postdata).". isi response block chain server ". $result);

            $response = json_decode($result);
            if(isset($response->status)){
                if($response->status == 200){
                    $du = array();
                    $du['get_airdropv2'] = "1";
                    $this->bum->updateMass($b_user_ids, $du);
                }
            }
        }
        // $this->bum->trans_end();
        $this->seme_log->write("api_cron", 'API_Cron/Airdropv2::index end');
        die();
    }
}
