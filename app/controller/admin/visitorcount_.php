<?php
class Visitorcount_ extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setTheme('admin');
        $this->current_parent = 'visitorcount_';
        $this->current_page = 'visitorcount_';
        $this->load("admin/f_visitor_general_model", "fvgm_model");
        // $this->load("admin/d_order_model", "dom");
        // $this->load("admin/d_order_detail_model", "dodm");
        // $this->load("admin/d_order_detail_item_model", "dodim");
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
        // if (!$this->admin_login) {
        //     redir(base_url_admin('login'));
        //     die();
        // }

        // if (!$this->checkPermissionAdmin($this->current_page)) {
        //     redir(base_url_admin('forbidden'));
        //     die();
        // }
        
        //get initial filtering data
        $data['keyword'] = strip_tags($this->input->get("keyword"));
        if (empty($data['keyword'])) {
            $data['keyword'] = "";
        }

        $this->setTitle('Visitor Count'.$this->site_suffix_admin);
        
        $this->putThemeContent("visitorcount_/home_modal", $data);
        $this->putThemeContent("visitorcount_/home", $data);

        $this->putJsContent("visitorcount_/home_bottom", $data);
        $this->loadLayout('col-2-left-visitor', $data);
        $this->render();
    }

}
