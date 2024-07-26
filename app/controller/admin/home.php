<?php
class Home extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setTheme('admin');
        $this->current_parent = 'dashboard';
        $this->current_page = 'dashboard';
        $this->load('admin/i_group_model', 'igm');
        $this->load('admin/i_group_post_model', 'igpm');
    }
    public function index()
    {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'), 0);
            die();
        }
        $this->setTitle("Dashboard ".$this->site_suffix_admin);

        $data['today_year_month'] = date("Y-m", strtotime('now'));
		$data['user_role'] = $data['sess']->admin->user_role;
        $data['count_total_club'] = $this->igm->countTotalClub();
        $data['count_total_club_post'] = $this->igpm->countTotalClubPost();
        

        $this->putJsFooter($this->skins->admin.'js/pages/index');

        $this->putThemeContent("home/dashboard", $data);
        $this->putJsContent("home/dashboard_bottom", $data);
        $this->loadLayout('col-2-left', $data);
        $this->render();
    }

    public function sample()
    {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $this->putThemeContent("home/sample", $data);
        //$this->putJsContent("home/dashboard_bottom",$data);
        $this->loadLayout('col-2-left', $data);
        $this->render();
    }
}
