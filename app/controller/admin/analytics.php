<?php

class Analytics extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setTheme('admin');
        $this->current_parent = 'analytics';
        $this->current_page = 'analytics';

        $this->load("api_admin/g_sellon_analytics", 'gsa');
        $this->load("api_admin/b_kategori_automotive_model", 'bka');
        $this->load("api_admin/c_produk_model", 'cp');
        $this->load("api_admin/b_user_model", 'bum');
        $this->load("api_admin/c_community_category_model", 'cccm');
        $this->load("api_admin/c_event_banner_model", 'ebm');
        $this->load("api_admin/i_group_category_model", 'igcm');
    }

    public function index()
    {
        $data = $this->__init();

        if (!$this->admin_login) {
            redir(base_url_admin('login'), 0);
            die();
        }

        if (!$this->checkPermissionAdmin($this->current_page)) {
            redir(base_url_admin('forbidden'));
            die();
        }


        $fromDate = $this->input->get("from_date");
        $toDate = $this->input->get("to_date");
        $group = [];
        $sorted = [];

        if (isset($_GET['submit']) && $fromDate != '') {
            $data_row = $this->gsa->getAll($fromDate, $toDate, "corner, type, category");

            foreach ($data_row as $row) {
                $corner = ucwords($row->corner);
                $type = ucwords($row->type);

                if (!empty($row->category) || $row->category !== '') {
                    if (!empty($row->corner) && $row->corner == 'Buy&Sell') {
                        if (
                            $row->type == 'Car(View,Brand)' ||
                            $row->type == 'Cars(Register)' ||
                            $row->type == 'MeetUp(View)' ||
                            $row->type == 'MeetUp(Register)' ||
                            $row->type == 'MotorCycle(Register)' ||
                            $row->type == 'Free Product(View)' ||
                            $row->type == 'Free Product' ||
                            $row->type == 'Motorcycle(View)' ||
                            $row->type == "MotorCycle(View,Brand)" ||
                            $row->type == 'Protection(SG)(View)' ||
                            $row->type == 'My Likes'
                        ) {
                            $cat_name = $this->bka->getById(62, $row->category);
                            $row->category = $cat_name->nama;
                        } elseif ($row->type == 'Video(View)' && $row->type != "") {
                            $cat_name = $this->bka->getById(62, $row->category);

                            $row->category = $cat_name->nama;
                        }
                    } elseif (!empty($row->corner) && $row->corner == 'Community') {
                        if ($row->type == 'Video(View)' || $row->type == 'Community(View)') {
                            $comm_cat = $this->cccm->getById(62, $row->category);
                            $row->category = $comm_cat->nama;
                        } elseif ($row->type == 'Category Detail' || $row->type == 'Community(Register)') {
                            $comm_cat = $this->cccm->getById(62, $row->category);
                            $row->category = $comm_cat->nama;
                        }
                    } elseif (!empty($row->corner) && $row->corner == 'GNB') {
                        if ($row->type == 'Category Community') {
                            $comm_cat = $this->cccm->getById(62, $row->category);
                            $row->category = $comm_cat->nama;
                        }
                    }  elseif (!empty($row->corner) && $row->corner == 'Club') {
                        if ($row->type == 'Club Category') {
                            $club_category = $this->igcm->getById(62, $row->category);
                            $row->category = $club_category->nama;
                            $row->type = "";
                        }
                    }

                    $row->type = ucwords($row->type);

                    if (!isset($corner) && !isset($type)) {
                        $group[$corner][] = [];
                    }
                }

                $group[$corner][] = $row;
                usort($group[$corner], function ($a, $b) {
                    if ($a->type_seq == $b->type_seq) {
                        return 0;
                    }

                    return ($a->type_seq < $b->type_seq) ? -1 : 1;
                });
            }

            foreach ($group as $key => $value) {
                if ($key == "Chat") {
                    $value = $this->gsa->getChatMain($fromDate, $toDate, "corner_seq", "ASC");
                    // echo json_encode($value);die();
                    foreach ($value as $row) {
                        $types = ucwords($row->type);
                        $sorted[$key][$types][] = $row;

                        usort($sorted[$key][$types], function ($a, $b) {
                            if ($a->category == $b->category) {
                                return 0;
                            }

                            return ($a->category < $b->category) ? -1 : 1;
                        });
                    }

                    $value = $this->gsa->getChatExceptMain($fromDate, $toDate, "corner_seq", "ASC");
                    foreach ($value as $row) {
                        $types = ucwords($row->type);
                        $sorted[$key][$types][] = $row;

                        usort($sorted[$key][$types], function ($a, $b) {
                            if ($a->category == $b->category) {
                                return 0;
                            }

                            return ($a->category < $b->category) ? -1 : 1;
                        });
                    }

                    // echo json_encode($data_row);die();
                    // echo json_encode($sorted[$key][$types]);die();
                } else {
                    foreach ($value as $row) {
                        $types = ucwords($row->type);
                        $sorted[$key][$types][] = $row;

                        usort($sorted[$key][$types], function ($a, $b) {
                            if ($a->category == $b->category) {
                                return 0;
                            }

                            return ($a->category < $b->category) ? -1 : 1;
                        });
                    }
                    // echo json_encode($sorted[$key][$types]);die();
                }
            }
        }

        // echo json_encode($sorted);die();
        $data['gsa_list'] = $sorted;

        // sum all count
        $data['totalView'] = $this->gsa->countBy([], "", $fromDate, $toDate);
        $data['videoView'] = $this->gsa->CountBy(["Community", "Buy&Sell"], "Video", $fromDate, $toDate);

        // sum view
        $data['totalViewHome'] = $this->gsa->CountBy(["Home"], "", $fromDate, $toDate);
        $data['totalViewMy'] = $this->gsa->CountBy(["My"], "", $fromDate, $toDate);
        $data['totalViewChat'] = $this->gsa->CountBy(["Chat"], "", $fromDate, $toDate);
        $data['totalViewWallet'] = $this->gsa->CountBy(["Wallet"], "", $fromDate, $toDate);
        $data['totalViewGNB'] = $this->gsa->CountBy(["GNB"], "", $fromDate, $toDate);
        $data['totalViewMainBanner'] = $this->gsa->CountBy(["mainBanner"], "", $fromDate, $toDate);
        $data['totalViewSMB'] = $this->gsa->CountBy(["SideMenuBar"], "", $fromDate, $toDate);
        $data['totalViewClub'] = $this->gsa->CountBy(["Club"], "", $fromDate, $toDate);
        $data['subtotalView'] = $this->gsa->countBy(["Buy&Sell"], "", $fromDate, $toDate);
        $data['subtotalViewVideo'] = $this->gsa->countBy(["Buy&Sell"], "Video", $fromDate, $toDate);
        $data['subtotalViewComm'] = $this->gsa->countBy(["Community"], "", $fromDate, $toDate);
        $data['subtotalViewCommVideo'] = $this->gsa->countBy(["Community"], "Video", $fromDate, $toDate);

        $data['chatBnSellCount'] = $this->gsa->countBy(["Chat"], "buyandsell", $fromDate, $toDate);
        $data['chatCommCount'] = $this->gsa->countBy(["Chat"], "community", $fromDate, $toDate);
        $data['chatPrivate'] = $this->gsa->countBy(["Chat"], "private", $fromDate, $toDate);
        $data['chatBarter'] = $this->gsa->countBy(["Chat"], "barter", $fromDate, $toDate);
        $data['chatOffer'] = $this->gsa->countBy(["Chat"], "offer", $fromDate, $toDate);

        $this->setTitle("Analytics " . $this->site_suffix_admin);
        $this->putThemeContent("analytics/index", $data);
        $this->putJsContent("analytics/index_js", $data);
        $this->loadLayout('col-2-left', $data);
        $this->render();
    }
}
