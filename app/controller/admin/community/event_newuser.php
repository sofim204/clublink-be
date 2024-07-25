<?php

class Event_Newuser extends JI_Controller
{
	public function __construct()
    {
		parent::__construct();
		$this->setTheme('admin');
        $this->load("admin/community_newuser_model", "newuser_model");
        $this->load("admin/community_retargeting_model", "retargeting_model");
        $this->load("admin/b_user_model", "user_model");
	}

	public function retargeting($language_code, $b_user_id)
    {
        $data = $this->__init();
		$this->setTitle("Retargeting". $this->site_suffix_admin);

        if(strtolower($language_code) == "id")
        {
            if(empty($b_user_id)) {
                $data['language_code'] = "id";
                $this->putThemeContent("community/webview/id_retargeting_default_mobile", $data);
            } else {
                // check if user exist in db
                $checkUserExist = $this->user_model->checkUserExist($b_user_id);

                if(isset($checkUserExist->b_user_id)) {
                    $data['language_code'] = "id";
                    // $data['check_registration'] = $this->user_model->checkUserRegistration($b_user_id);
                    $get_data_from_user = $this->user_model->getDataFromUser($b_user_id);
                    $cdate = $get_data_from_user->cdate;
                    $startEventRegistrationDate = date('Y-m-d', strtotime("10/16/2023"));
                    $endEventRegistrationDate = date('Y-m-d', strtotime("10/31/2023"));
            
                    $get_data_from_new_user = $this->newuser_model->getDataFromNewUser($b_user_id);
                    $get_data_from_user_retargeting = $this->retargeting_model->getDataFromOldUser($b_user_id);
                    // if ( ($cdate >= $startEventRegistrationDate && $cdate <= $endEventRegistrationDate) || 
                    //     (!is_null($get_data_from_new_user->cdate_day_1) && !is_null($get_data_from_new_user->cdate_redeem_pulsa))
                    // ) {
                    if ($cdate >= $startEventRegistrationDate && $cdate <= $endEventRegistrationDate) {
                        if($get_data_from_user->b_user_id_recruiter == '0') { 
                            if(!empty(($get_data_from_new_user->cdate_day_1))) {
                                // if(isset($get_data_from_new_user->cdate_day_1) && isset($get_data_from_new_user->cdate_redeem_pulsa))
                                $data['list_day_newuser'] = $this->newuser_model->getAll($b_user_id);
                                if(!empty(($get_data_from_new_user->cdate_day_3))) {
                                    $data['list_day_retargeting'] = 0;
                                    if(!empty(($get_data_from_user_retargeting->cdate_day_1))) {
                                        $data['list_day_retargeting'] = $this->retargeting_model->getAll($b_user_id);
                                    }
                                } else if(!empty(($get_data_from_user_retargeting->cdate_day_1))) {
                                    $data['list_day_retargeting'] = $this->retargeting_model->getAll($b_user_id);
                                } else {
                                    $data['list_day_retargeting'] = 1;
                                }
                            } else {
                                $data['list_day_newuser'] = 0;
                                $data['list_day_retargeting'] = 1;
                            }
                            $this->putThemeContent("community/webview/id_retargeting_new_to_old_mobile", $data);
                        } else if($get_data_from_user->b_user_id_recruiter != '0') {
                            if(!empty(($get_data_from_user_retargeting->cdate_day_1))) {
                                $data['list_day_retargeting'] = $this->retargeting_model->getAll($b_user_id);
                                $data['list_day_newuser'] = 1;
                            } else {
                                $data['list_day_retargeting'] = 0;
                                $data['list_day_newuser'] = 1;
                            }
                            $this->putThemeContent("community/webview/id_retargeting_mobile", $data);
                        }
                    } else if ($cdate <= $startEventRegistrationDate) {
                        // $get_data_from_user_retargeting = $this->retargeting_model->getDataFromOldUser($b_user_id);
                        if(!empty(($get_data_from_user_retargeting->cdate_day_1))) {
                            $data['list_day_retargeting'] = $this->retargeting_model->getAll($b_user_id);
                            $data['list_day_newuser'] = 1;
                        } else {
                            $data['list_day_retargeting'] = 0;
                            $data['list_day_newuser'] = 1;
                        }
                        $data['class_hide'] = 'hide';
                        $this->putThemeContent("community/webview/id_retargeting_mobile", $data);
                    }
                }
            }
        } 
        else if(strtolower($language_code) == "en")
        {
            if(empty($b_user_id)) {
                $data['language_code'] = "en";
                $this->putThemeContent("community/webview/en_retargeting_default_mobile", $data);
            } else {
                // check if user exist in db
                $checkUserExist = $this->user_model->checkUserExist($b_user_id);

                if(isset($checkUserExist->b_user_id)) {
                    $data['language_code'] = "en";
                    // $data['check_registration'] = $this->user_model->checkUserRegistration($b_user_id);
                    $get_data_from_user = $this->user_model->getDataFromUser($b_user_id);
                    $cdate = $get_data_from_user->cdate;
                    $startEventRegistrationDate = date('Y-m-d', strtotime("10/16/2023"));
                    $endEventRegistrationDate = date('Y-m-d', strtotime("10/31/2023"));
            
                    $get_data_from_new_user = $this->newuser_model->getDataFromNewUser($b_user_id);
                    $get_data_from_user_retargeting = $this->retargeting_model->getDataFromOldUser($b_user_id);
                    // if ( ($cdate >= $startEventRegistrationDate && $cdate <= $endEventRegistrationDate) || 
                    //     (!is_null($get_data_from_new_user->cdate_day_1) && !is_null($get_data_from_new_user->cdate_redeem_pulsa))
                    // ) {
                    if ($cdate >= $startEventRegistrationDate && $cdate <= $endEventRegistrationDate) {
                        if($get_data_from_user->b_user_id_recruiter == '0') { 
                            if(!empty(($get_data_from_new_user->cdate_day_1))) {
                                // if(isset($get_data_from_new_user->cdate_day_1) && isset($get_data_from_new_user->cdate_redeem_pulsa))
                                $data['list_day_newuser'] = $this->newuser_model->getAll($b_user_id);
                                if(!empty(($get_data_from_new_user->cdate_day_3))) {
                                    $data['list_day_retargeting'] = 0;
                                    if(!empty(($get_data_from_user_retargeting->cdate_day_1))) {
                                        $data['list_day_retargeting'] = $this->retargeting_model->getAll($b_user_id);
                                    }
                                } else if(!empty(($get_data_from_user_retargeting->cdate_day_1))) {
                                    $data['list_day_retargeting'] = $this->retargeting_model->getAll($b_user_id);
                                } else {
                                    $data['list_day_retargeting'] = 1;
                                }
                            } else {
                                $data['list_day_newuser'] = 0;
                                $data['list_day_retargeting'] = 1;
                            }
                            $this->putThemeContent("community/webview/en_retargeting_new_to_old_mobile", $data);
                        } else if($get_data_from_user->b_user_id_recruiter != '0') {
                            if(!empty(($get_data_from_user_retargeting->cdate_day_1))) {
                                $data['list_day_retargeting'] = $this->retargeting_model->getAll($b_user_id);
                                $data['list_day_newuser'] = 1;
                            } else {
                                $data['list_day_retargeting'] = 0;
                                $data['list_day_newuser'] = 1;
                            }
                            $this->putThemeContent("community/webview/en_retargeting_mobile", $data);
                        }
                    } else if ($cdate <= $startEventRegistrationDate) {
                        // $get_data_from_user_retargeting = $this->retargeting_model->getDataFromOldUser($b_user_id);
                        if(!empty(($get_data_from_user_retargeting->cdate_day_1))) {
                            $data['list_day_retargeting'] = $this->retargeting_model->getAll($b_user_id);
                            $data['list_day_newuser'] = 1;
                        } else {
                            $data['list_day_retargeting'] = 0;
                            $data['list_day_newuser'] = 1;
                        }
                        $data['class_hide'] = 'hide';
                        $this->putThemeContent("community/webview/en_retargeting_mobile", $data);
                    }
                }
            }
        }
		$this->loadLayout('col-2-left-faqtnc', $data);
		$this->render();
	}

    public function eventdailymission($language_code)
    {
		$data = $this->__init();
		$this->setTitle("Daily Mission Event Guide". $this->site_suffix_admin);

        if (!in_array(strtolower($language_code), array("en", "id"))) {
            $data['language_code'] = "en";
        } else {
            $data['language_code'] = strtolower($language_code);
        }

        $this->putThemeContent("community/webview/new_user/eventdailymission_mobile", $data);
        
		$this->loadLayout('col-2-left-faqtnc', $data);
		$this->render();
    }

    public function eventdailymissionfullguide($language_code)
    {
        $data = $this->__init();
		$this->setTitle("Daily Mission Event Full Guide". $this->site_suffix_admin);

        if (!in_array(strtolower($language_code), array("en", "id"))) {
            $data['language_code'] = "en";
        } else {
            $data['language_code'] = strtolower($language_code);
        }

        $this->putThemeContent("community/webview/new_user/eventdailymissionfullguide_mobile", $data);

		$this->loadLayout('col-2-left-faqtnc', $data);
		$this->render();
    }

    public function new_user($language_code, $b_user_id)
    {
        $data = $this->__init();
		$this->setTitle("New User". $this->site_suffix_admin);

        if (!in_array(strtolower($language_code), array("en", "id"))) {
            $data['language_code'] = "en";
        } else {
            $data['language_code'] = strtolower($language_code);
        }

        if(empty($b_user_id)) {
            $data['list_day_newuser'] = 0;
            $this->putThemeContent("community/webview/new_user/new_user_mobile", $data);
        } else {
            // check if user exist in db
            $checkUserExist = $this->user_model->checkUserExist($b_user_id);
            if(isset($checkUserExist->b_user_id)) {
                $get_data_from_user = $this->user_model->getDataFromUser($b_user_id);
                $get_data_from_new_user = $this->newuser_model->getDataFromNewUser($b_user_id);

                $registrationDate = $get_data_from_user->cdate;
                $startEventRegistrationDate = date('Y-m-d', strtotime("11/6/2023"));
                $endEventRegistrationDate = date('Y-m-d', strtotime("11/30/2023"));
        
                if ($registrationDate >= $startEventRegistrationDate && $registrationDate <= $endEventRegistrationDate) {
                    if(!empty(($get_data_from_new_user->cdate_day_1))) {
                        $data['list_day_newuser'] = $this->newuser_model->getAll($b_user_id);
                    } else {
                        $data['list_day_newuser'] = 0;
                    }
                } else {
                    $data['list_day_newuser'] = 0;
                }
                $this->putThemeContent("community/webview/new_user/new_user_mobile", $data);
            }
        }

		$this->loadLayout('col-2-left-faqtnc', $data);
		$this->render();
	}
}

