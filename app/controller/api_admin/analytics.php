<?php

class Analytics extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load("api_admin/g_sellon_analytics", 'gsa');
        $this->load("api_admin/b_kategori_automotive_model", 'bka');
        $this->load("api_admin/c_produk_model", 'cp');
        $this->load("api_admin/b_user_model", 'bum');
        $this->load("api_admin/c_community_category_model", 'cccm');
        $this->load("api_admin/c_event_banner_model", 'ebm');
    }

    // public function index()
    // {
    //     $d = $this->__init();
    //     $data = [];

    //     if (!$this->admin_login) {
    //         $this->status = 400;
    //         $this->message = 'Authorization required';

    //         header("HTTP/1.0 400 Unauthorized");

    //         $this->__json_out($data);
    //         die();
    //     }

    //     // pagination
    //     $page = (int) $this->input->get("page");
    //     $page_size = (int) $this->input->get("page_size");
    //     $page = $this->__page($page);
    //     $page_size = $this->__pageSize($page_size);

    //     $sSearch = $this->input->post("sSearch");
    //     $keyword = $sSearch;
    //     $type = $this->input->post('corner');
    //     $category = $this->input->post('category');
    //     $fromDate = $this->input->post("from_date");
    //     $toDate = $this->input->post("to_date");

    //     $data_count = $this->gsa->countAll($keyword, $type, $category, $toDate, $toDate);
    //     $data_row = $this->gsa->getAll($page, $page_size, $keyword, $type, $category, $fromDate, $toDate);

    //     $group = [];
    //     foreach ($data_row as $row) {
    //         $corner = ucwords($row->corner);
    //         if (!isset($corner) && !isset($type)) {
    //             $group[$corner] = [];
    //         }

    //         $group[$corner][] = $row;
    //     }

    //     ksort($group);

    //     $this->status = 200;
    //     $this->message = 'Success';
       
    //     $data['countBuyAndSell'] = count($group['Buy&Sell']);
    //     $data['countChat'] = count($group['Chat']);
    //     $data['countCommunity'] = count($group['Community']);
    //     $data['countGNB'] = count($group['GNB']);
    //     $data['countHome'] = count($group['Home']);
    //     $data['countMainBanner'] = count($group['MainBanner']);
    //     $data['countMy'] = count($group['My']);
    //     $data['countSideMenuBar'] = count($group['SideMenuBar']);
    //     $data['countWallet'] = count($group['Wallet']);

    //     $data['gsa_list'] = $group;

    //     $response = [
    //         "data" => $data,
    //         "recordsFiltered" => $data_count,
    //         "recordsTotal" => $data_count,
    //         "message" => "Success",
    //         "status" => 200
    //     ];

    //     echo json_encode($response);
    // }

    public function exportExcel()
    {
        $this->__init();

        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Authorization required';

            header("HTTP/1.0 400 Unauthorized");

            $this->__json_out([]);
            die();
        }

        $fromDate = $this->input->post("from_date");
        $toDate = $this->input->post("to_date");
        $type_report = $this->input->post('type');
        $data = "";

        if ($type_report == "detail") {
            $data = $this->gsa->getAll($fromDate, $toDate, "corner, type, category, detail");
        } else {
            $data = $this->gsa->getAll($fromDate, $toDate, "corner, type, category");
        }        

        $grouped = [];
        $sorted = [];
        $groupedByCategory = [];

        foreach ($data as $row) {
            $corner = $row->corner;
            $type = $row->type;

            if (!empty($row->category) || $row->category !== '') {
                if (!empty($row->corner) && $row->corner == 'Buy&Sell') {
                    if ($row->type == 'Car(View,Brand)' ||
                        $row->type == 'Cars(Register)' ||
                        $row->type == 'MeetUp(View)' ||
                        $row->type == 'MeetUp(Register)' ||
                        $row->type == 'Free Product(View)' ||
                        $row->type == 'Free Product' ||
                        $row->type == 'Motorcycle(View)' ||
                        $row->type == 'Protection(SG)(View)' ||
                        $row->type == "MotorCycle(View,Brand)" ||
                        $row->type == 'MotorCycle(Register)' ||
                        $row->type == 'My Likes'
                    ) {
                        $cat_name = $this->bka->getById(62, $row->category);
                        $prod_name = $this->cp->getById(62, $row->detail);

                        $row->category = $cat_name->nama;
                        $row->detail = $prod_name->nama;
                    } elseif ($row->type == 'Video(View)') {
                        $cat_name = $this->bka->getById(62, $row->category);
                        $prod_name = $this->cp->getById(62, $row->detail);

                        $row->category = $cat_name->nama;
                        $row->detail = $prod_name->nama;
                    } elseif ($row->type == 'Product Share') {
                        $prod_name = $this->cp->getById(62, $row->detail);
                        $row->detail = $prod_name->nama;
                    } elseif ($row->type == 'Seller Shop' || $row->type == 'Seller Shop Share') {
                        $detail = $this->bum->getById(62, $row->detail);
                        $row->detail = $detail->fnama;
                    } else {
                        null;
                    }
                } elseif (!empty($row->corner) && $row->corner == 'Community') {
                    if ($row->type == 'Video(View)' || $row->type == 'Community(View)') {
                        $comm_cat = $this->cccm->getById(62, $row->category);
                        $comm_name = $this->gsa->getByIdCC(62, $row->detail);

                        $row->category = $comm_cat->nama;
                        $row->detail = $comm_name->title;
                    } elseif ($row->type == 'Category Detail' || $row->type == 'Community(Register)') {
                        $comm_cat = $this->cccm->getById(62, $row->category);
                        $row->category = $comm_cat->nama;
                    } elseif ($row->type == 'Post Share') {
                        $comm_name = $this->gsa->getByIdCC(62, $row->detail);
                        $row->detail = $comm_cat->nama;
                    }
                } elseif (!empty($row->corner) && $row->corner == 'mainBanner') {
                    $bannerName = $this->ebm->getById(62, $row->detail);
                    $row->detail = $bannerName->judul;
                } elseif (!empty($row->corner) && $row->corner == 'GNB') {
                    if ($row->type == 'Category Product') {
                        $prod_name = $this->cp->getById(62, $row->detail);
                        $row->detail = $prod_name->nama;
                    } elseif ($row->type == 'Category Community') {
                        $comm_cat = $this->cccm->getById(62, $row->category);
                        $row->category = $comm_cat->nama;
                    }
                }
            }

            $row->type = ucwords($row->type);
            if (!isset($corner) && !isset($type)) {
                $grouped[$corner] = [];
            }

            $grouped[$corner][] = $row;      
            usort($grouped[$corner], function($a, $b) {
                if ($a->type_seq == $b->type_seq) {
                    return 0;
                }

                return ($a->type_seq < $b->type_seq) ? -1 : 1;
            });
        }

        foreach($grouped as $key => $value) {
            foreach($value as $row) {
                $types = ucwords($row->type);
                $sorted[$key][$types][] = $row;

                usort($sorted[$key][$types], function($a, $b) {
                    if ($a->category == $b->category) {
                        return 0;
                    }

                    return ($a->category < $b->category) ? -1 : 1;
                });
            }
        }

        foreach($sorted as $key => $val) {
            foreach($val as $keyType => $row) {
                foreach($row as $value) {
                    $detail = $value->category;
                    $groupedByCategory[$key][$keyType][$detail][] = $value;
                }
            }
        }

        // echo json_encode($groupedByCategory);die();

        $doExport = $this->doExport(
            $type_report == 'detail' ? $groupedByCategory : $sorted, 
            $fromDate, 
            $toDate, 
            $type_report
        );

        echo json_encode($doExport);
    }

    private function doExport($data, $start_date, $end_date, $type)
    {
        $this->lib('phpexcel/PHPExcel', '', 'inc');
        $this->lib('phpexcel/PHPExcel/Writer/Excel2007', '', 'inc');

        $object = new PHPExcel();
        $table_columns = null;
        
        if ($type == 'detail') {
            $table_columns = ["Corner", "Type", "Category", "Detail", "VideoID", "count", "CreatedAt"];
            
        } else {
            $table_columns = ["Corner", "Type", "Category", "count", "CreatedAt"];
        }

        $column = 0;

        foreach ($table_columns as $filed) {
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 3, $filed);

            $column++;
        }

        $object->getActiveSheet()->setCellValue(
            'A1', "Report Analytic $type data from " . $start_date . ' - ' . empty($end_date) ? date("Y-m-d") : $end_date 
        );

        $excel_row = 4;
        foreach ($data as $key => $row) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $key);
            foreach ($row as $keyType => $val) {
                $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $keyType);
                if ($type == 'detail') {
                    foreach($val as $keyCategory => $values) {
                        $object->getActiveSheet()->setCellValueByColumnAndRow(
                            2, $excel_row, empty($keyCategory) ? '' : $this->__convertToEmoji($keyCategory)
                        );
                        foreach($values as $value) {
                            if ($type == 'detail') {
                                $object->getActiveSheet()->setCellValueByColumnAndRow(
                                    3, $excel_row, empty($value->detail) ? '' : $this->__convertToEmoji($value->detail)
                                );
                                $object->getActiveSheet()->setCellValueByColumnAndRow(
                                    4, $excel_row, empty($value->sub_detail) ? '' : $value->sub_detail
                                );   
                            }
                            $object->getActiveSheet()->setCellValueByColumnAndRow(
                                $type == 'detail' ? 5 : 3, $excel_row, empty($value->count) ? '' : $value->count
                            );
                            $object->getActiveSheet()->setCellValueByColumnAndRow(
                                $type == 'detail' ? 6 : 4, $excel_row, empty($value->cdate) ? '' : $value->cdate
                            );
                    
                            $excel_row++;
                        } 
                    }
                } else {
                    foreach($val as $value) {
                        $object->getActiveSheet()->setCellValueByColumnAndRow(
                            2, $excel_row, empty($value->category) ? '' : $this->__convertToEmoji($value->category)
                        );
                        $object->getActiveSheet()->setCellValueByColumnAndRow(
                            3, $excel_row, empty($value->count) ? '' : $value->count
                        );
                        $object->getActiveSheet()->setCellValueByColumnAndRow(
                            4, $excel_row, empty($value->cdate) ? '' : $value->cdate
                        );
                
                        $excel_row++;
                    } 
                }
            }
        }

        if ($type == 'detail') {
            for($col = 'A'; $col !== 'H'; $col++) {
                $object->getActiveSheet()
                    ->getColumnDimension($col)
                    ->setAutoSize(true);
            }
        } else {
            for($col = 'A'; $col !== 'F'; $col++) {
                $object->getActiveSheet()
                    ->getColumnDimension($col)
                    ->setAutoSize(true);
            }
        }
        
        $header = 'A1:G1';
        $header_th = "A$excel_row:H$excel_row";
        $object->getActiveSheet()->mergeCells($header);
        $object->getActiveSheet()->getStyle($header)->getAlignment()->setHorizontal('center');
        $object->getActiveSheet()->getStyle("A1")->getFont()->setSize(15);
        $object->getActiveSheet()->getStyle($header_th)->getAlignment()->setHorizontal('center');

        $objWriter = new PHPExcel_Writer_Excel2007($object);
        $filename = "Report Analytics " . ucwords($type) . '-' . time();
		ob_start();
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
		ob_end_clean();

        $response = [
            'op' => 'ok',
			'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($xlsData),
            'filename' => $filename
        ];

        return $response;
    }

    private function __convertToEmoji($text) {
        $value = $text;
        $readTextWithEmoji = preg_replace("/\\\\u([0-9A-F]{2,5})/i", "&#x$1;", $value);

        return $readTextWithEmoji;
    }
}