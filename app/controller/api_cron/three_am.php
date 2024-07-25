<?php
class Three_am extends JI_Controller
{
//     public $email_send = 1;
//     public $is_log = 1;
//     public $is_push = 1;

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_cron/b_user_model", "bu");
        $this->load("api_cron/group/i_group_participant_model", "igparticipantm");
    }

//     public function index()
//     {
//         //open transaction
//         $this->order->trans_start();

//         //change log filename
//         $this->seme_log->changeFilename("cron.log");

//         //put on log
//         $this->seme_log->write("api_cron", 'API_Cron/Three_Am::index --configuration --countdown_timeout: 3 am');

//         $pendings = $this->order->getBuyerPendingCountDown(); //get order havent start countdown
//         $c = count($pendings);
//         $this->seme_log->write("api_cron", 'API_Cron/Three_Am::index --pendingsCount: '.$c);
//         if (count($pendings)>0) {
//           foreach ($pendings as $pending) {
            
//             //update count down start in d_order table
//             $du = array();
//             $du['cdate'] = 'NOW()';
//             $du['is_countdown'] = 1;
//             $this->order->update($pending->nation_code, $pending->d_order_id, $du);

//             //update date begin and date expire 
//             $du = array();
//             $du['date_begin'] = date("Y-m-d H:i:s", strtotime("now"));
//             $du['date_expire'] = date("Y-m-d H:i:s", strtotime("+".$this->payment_timeout." minutes"));
//             $this->dodm->updateByOrderId($pending->nation_code, $pending->d_order_id, $du);


              
//           }//end foreach
//         }//end data count

//         $this->order->trans_commit();
        
//         //end transacation
//         $this->order->trans_end();
//     }
// }

    //request uncomment from mr jackie(7 nov 2023 14:59 by verbal)
    public function index() {
        //get online user list
        $userOnline = $this->bu->getUserOnlineAfter24Hours();
        if (count($userOnline)>0) {
            foreach ($userOnline as $user) {
                $di = array();
                $di['is_online'] = 0;
                $di['last_online'] = 'NOW()';
                $this->bu->update($user->id, $di);

                $dx = array();
                $dx['is_online'] = "0";
                $this->igparticipantm->updateStatusParticipant(62, '0', '1', $user->id, "", $dx);
            }
            unset($userOnline, $user);
        }
    }
}