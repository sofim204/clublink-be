<?php
class RejectSeller extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setTheme('admin');
        $this->current_parent = 'ecommerce';
        $this->current_page = 'ecommerce_rejectseller';
        $this->load("admin/d_order_model", "dom");
        $this->load("admin/d_order_detail_model", "dodm");
        $this->load("admin/d_order_detail_item_model", "dodim");
    }

    private function __forceDownload($pathFile)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($pathFile));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($pathFile));
        ob_clean();
        flush();
        readfile($pathFile);
        exit;
    }

    private function __checkDir($periode)
    {
        if (!is_dir(SENEROOT.'media/')) {
            mkdir(SENEROOT.'media/', 0777);
        }
        if (!is_dir(SENEROOT.'media/laporan/')) {
            mkdir(SENEROOT.'media/laporan/', 0777);
        }
        $str = $periode.'/01';
        $periode_y = date("Y", strtotime($str));
        $periode_m = date("m", strtotime($str));
        if (!is_dir(SENEROOT.'media/laporan/'.$periode_y)) {
            mkdir(SENEROOT.'media/laporan/'.$periode_y, 0777);
        }
        if (!is_dir(SENEROOT.'media/laporan/'.$periode_y.'/'.$periode_m)) {
            mkdir(SENEROOT.'media/laporan/'.$periode_y.'/'.$periode_m, 0777);
        }
        return SENEROOT.'media/laporan/'.$periode_y.'/'.$periode_m;
    }

    public function index()
    {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }

        if (!$this->checkPermissionAdmin($this->current_page)) {
            redir(base_url_admin('forbidden'));
            die();
        }
        
        //get initial filtering data
        $data['keyword'] = strip_tags($this->input->get("keyword"));
        if (empty($data['keyword'])) {
            $data['keyword'] = "";
        }

        $this->setTitle('Rejected by Seller'.$this->site_suffix_admin);
        
        $this->putThemeContent("ecommerce/rejectseller/home_modal", $data);
        $this->putThemeContent("ecommerce/rejectseller/home", $data);

        $this->putJsContent("ecommerce/rejectseller/home_bottom", $data);
        $this->loadLayout('col-2-left', $data);
        $this->render();
    }

    // by Muhammad Sofi 9 February 2022 10:00 | fix button export to excel
    public function download_xls(){
        $data = $this->__init();

        if(!$this->admin_login){
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;
        $keyword = '';
        $cdate_start = $this->input->get("cdate_start");
        $cdate_end = $this->input->get("cdate_end");
        $settlement_status = $this->input->get("settlement_status");

        $ddata = $this->dodim->exportXlsRejectSeller($nation_code, $keyword, $cdate_start, $cdate_end, $settlement_status);

        //loading library xls
        $this->lib('phpexcel/PHPExcel','','inc');
        $this->lib('phpexcel/PHPExcel/Writer/Excel2007','','inc');

        //preset array column
        $phpexcel_money = '_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)';
        $judul_pertama_sty = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Arial'
            )
        );
        $style = array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $styleborder = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        //create object xls
        $objPHPExcel = new PHPExcel();

        //===report sheet===//
        $objWorkSheet = $objPHPExcel->setActiveSheetIndex(0);

        $objWorkSheet->getColumnDimension('A')->setAutoSize(false);
        $objWorkSheet->getColumnDimension('B')->setAutoSize(false);
        $objWorkSheet->getColumnDimension('C')->setAutoSize(false);
        $objWorkSheet->getColumnDimension('D')->setAutoSize(false);
        $objWorkSheet->getColumnDimension('E')->setAutoSize(false);
        $objWorkSheet->getColumnDimension('F')->setAutoSize(false);
        $objWorkSheet->getColumnDimension('G')->setAutoSize(false);
        $objWorkSheet->getColumnDimension('H')->setAutoSize(false);
        $objWorkSheet->getColumnDimension('I')->setAutoSize(false);
        $objWorkSheet->getColumnDimension('A')->setWidth(14);
        $objWorkSheet->getColumnDimension('B')->setWidth(20);
        $objWorkSheet->getColumnDimension('C')->setWidth(30);
        $objWorkSheet->getColumnDimension('D')->setWidth(30);
        $objWorkSheet->getColumnDimension('E')->setWidth(20);
        $objWorkSheet->getColumnDimension('F')->setWidth(14);
        $objWorkSheet->getColumnDimension('G')->setWidth(20);
        $objWorkSheet->getColumnDimension('H')->setWidth(20);
        $objWorkSheet->getColumnDimension('I')->setWidth(20);

        //header
        $objWorkSheet
        ->setCellValue('A1', 'Order ID')
        ->setCellValue('B1', 'Order Date')
        ->setCellValue('C1', 'Invoice Number')
        ->setCellValue('D1', 'Product Name')
        ->setCellValue('E1', 'Total Item')
        ->setCellValue('F1', 'Sub Total')
        ->setCellValue('G1', 'Shipping Cost')
        ->setCellValue('H1', 'Refund Amount')
        ->setCellValue('I1', 'Resolution')
        ;

        //styling for header
        $objWorkSheet->getStyle('A1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
        $objWorkSheet->getStyle('B1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
        $objWorkSheet->getStyle('C1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
        $objWorkSheet->getStyle('D1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
        $objWorkSheet->getStyle('E1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
        $objWorkSheet->getStyle('F1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
        $objWorkSheet->getStyle('G1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
        $objWorkSheet->getStyle('H1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);
        $objWorkSheet->getStyle('I1')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style);

        $i=2;
        $dot=".";
        $nomor = 1;
        if(count($ddata)>0){
            foreach($ddata as $pb){
                //fill data to cell
                $objWorkSheet->setCellValue('A'.$i, $pb->id);
                $objWorkSheet->setCellValue('B'.$i, $pb->cdate);
                $objWorkSheet->setCellValue('C'.$i, $pb->invoice_number);
                $objWorkSheet->setCellValue('D'.$i, $pb->product_name);
                $objWorkSheet->setCellValue('E'.$i, $pb->total_item);
                $objWorkSheet->setCellValue('F'.$i, $pb->sub_total);
                $objWorkSheet->setCellValue('G'.$i, $pb->shipping_cost);
                $objWorkSheet->setCellValue('H'.$i, $pb->refund_amount);
                $objWorkSheet->setCellValue('I'.$i, $pb->resolution);

                //set border to each column
                $objWorkSheet->getStyle('A'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('B'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('C'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('D'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('E'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('F'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('G'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('H'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('I'.$i)->applyFromArray($styleborder);

                $nomor++;
                $i++;
            }
        } else {
            $objWorkSheet->setCellValue('A'.$i,'Empty result')->mergeCells('A'.$i.':C'.$i.'');
            $objWorkSheet->getStyle('A'.$i.':C'.$i.'')->getAlignment()->applyFromArray($style);
            $objWorkSheet->getStyle('A'.$i.':C'.$i.'')->applyFromArray($styleborder);
        }
        $objPHPExcel->setActiveSheetIndex(0);

        //save file
        $save_dir = $this->__checkDir(date("Y/m"));
        $save_file = 'report_rejectseller-'.$pb->id;
        $save_file = str_replace(' ','',str_replace('/','',$save_file));

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        if(file_exists($save_dir.'/'.$save_file.'.xlsx')) unlink($save_dir.'/'.$save_file.'.xlsx');
        $objWriter->save($save_dir.'/'.$save_file.'.xlsx');

        $this->__forceDownload($save_dir.'/'.$save_file.'.xlsx');
    }
}
