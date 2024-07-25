<?php

// class Beginning_Of_The_Month extends JI_Controller
// {

//     public function __construct()
//     {
//         parent::__construct();
//         $this->lib("seme_log");
// 		$this->load("api_mobile/b_user_model", "bu");
//         $this->load("api_mobile/b_user_alamat_model", "bua");
// 	    $this->load("api_mobile/g_check_in_setting_model","gcism");
// 	    $this->load("api_mobile/g_point_check_in_history_model","gpcih");
// 	    // $this->load("api_mobile/g_leaderboard_point_area_model", 'glpam');
// 	    $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
// 	    // $this->load("api_mobile/g_leaderboard_ranking_model", 'glrm');
//     }

//     public function index()
//     {

//         //put on log
//         $this->seme_log->write("api_cron", 'API_Cron/Beginning_Of_The_Month::index START');

//         $nation_code = 62;
//         // $dateCustom = date("Y-m-d", strtotime("-1 month"));
//         $dateCustom = date("Y-m-d");
//         $dateNow = date("Y-m-d");

//         // if($dateNow != date("Y-m-01")){
//         // 	$this->seme_log->write("api_cron", 'API_Cron/Beginning_Of_The_Month::index today is not beginning of the month');
//         // 	$this->seme_log->write("api_cron", 'API_Cron/Beginning_Of_The_Month::index STOP');
//         // 	die();
//         // }

// 		// $detail_event = $this->gcism->getOldest($nation_code, $dateCustom);

// 		// if(isset($detail_event->id)){

// 		// 	if($detail_event->start_date <= $dateCustom && $detail_event->end_date >= $dateCustom){
// 		// 		// $data['event_status'] = "running";
// 		// 	}else{
//   //       		$this->seme_log->write("api_cron", 'API_Cron/Beginning_Of_The_Month::index event not running');
//   //       		$this->seme_log->write("api_cron", 'API_Cron/Beginning_Of_The_Month::index STOP');
// 		// 		die();
// 		// 	}

// 		// }

//         //open transaction
//         $this->glphm->trans_start();

//         $UserLoginOneMonthList = $this->gpcih->getUserLoginOneMonth($nation_code, $dateCustom);
//         $c = count($UserLoginOneMonthList);
//         $this->seme_log->write("api_cron", 'API_Cron/Beginning_Of_The_Month::index --UserLoginOneMonthCount: '.$c);
//         if (count($UserLoginOneMonthList)>0) {

//             foreach ($UserLoginOneMonthList as $user) {

//             	$userData = $this->bu->getById($nation_code, $user->b_user_id);

//                 $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $user->b_user_id);

// 				$di = array();
// 				$di['nation_code'] = $nation_code;
// 		        $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
// 		        $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
// 		        $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
// 		        $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
// 				$di['b_user_id'] = $user->b_user_id;
// 				$di['point'] = $user->total_point;
// 				$di['custom_id'] = 0;
// 				$di['custom_type'] = 'get point from calendar check in';
// 				$di['custom_type_sub'] = '';
// 				$di['custom_text'] = $userData->fnama.' has '.$di['custom_type'].' that run monthly and get '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
// 				$this->glphm->set($di);
// 			    $this->glphm->trans_commit();
// 				// $this->glrm->updateTotal($nation_code, $user->b_user_id, 'total_point', '-', $di['point']);
// 			    // $this->glphm->trans_commit();

// 			    //get last id
//     			$lastId = $this->gpcih->getLastId($nation_code, $user->b_user_id);

// 			    //insert into database
// 			    $di = array();
// 			    $di['nation_code'] = $nation_code;
// 			    $di['id'] = $lastId;
// 			    $di['b_user_id'] = $user->b_user_id;
//       			$di['plusorminus'] = "-";
// 			    $di['point'] = $user->total_point;
// 			    $di['custom_type'] = 'convert to main point';
// 			    $di['custom_type_sub'] = '';
// 			    $di['custom_text'] = $userData->fnama.' has '.$di['custom_type'].' that run monthly and lose '.$di['point'].' point(s)';

// 			    $this->gpcih->set($di);
// 			    $this->glphm->trans_commit();

// 				$this->bu->updateTotal($nation_code, $user->b_user_id, 'total_point_check_in', '-', $di['point']);
// 				$this->glphm->trans_commit();

// 			    $di = array();
// 			    $di['is_calculated'] = 1;
// 			    $this->gpcih->updateForCalculated($nation_code, $user->b_user_id, $dateCustom, $di);
//                 $this->glphm->trans_commit();

//     		}

//         }

//         //end transacation
//         $this->glphm->trans_end();

// 		$this->seme_log->write("api_cron", 'API_Cron/Beginning_Of_The_Month::index STOP');
//     }
// }
