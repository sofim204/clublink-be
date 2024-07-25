<?php

class Whitelistip extends JI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load("api_admin/g_whitelistip_model", 'gwm');
    }

    public function index() {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access, please login';
            header("HTTP/1.0 400 Unauthorized access, please login");
            $this->__json_out($data);
            die();
        }

        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $sSearch = $this->input->request("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->request("iDisplayLength");

        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");

		$type = $this->input->post("type");

        $sortCol = "date";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }

        switch ($iSortCol_0) {
            case 0:
                $sortCol = "no";
                break;
            case 1:
                $sortCol = "id";
                break;
            case 2:
                $sortCol = "type";
                break;
            case 3:
                $sortCol = "text";
                break;
            case 4:
                $sortCol = "admin_name";
                break;
            default:
                $sortCol = "no";
        }

        if (empty($draw)) {
            $draw = 0;
        }
        if (empty($pagesize)) {
            $pagesize=10;
        }
        if (empty($page)) {
            $page=0;
        }

        $keyword = $sSearch;

        $this->status = 200;
        $this->message = 'Success';
        $dcount = $this->gwm->countAll($keyword, $type);
        $ddata = $this->gwm->getAll($page, $pagesize, $sortCol, $sortDir, $keyword, $type);

		foreach ($ddata as &$gd) {
		}

        $this->__jsonDataTable($ddata, $dcount);
    }

    public function tambah()
    {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access, please login';
            header("HTTP/1.0 400 Unauthorized access, please login");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;
        
        $di = $_POST;

        $this->gwm->trans_start();

        $di['nation_code'] = $nation_code;
        $di['cdate'] = 'NOW()';
        $di['id'] = $this->gwm->getLastId($nation_code);

        $res = $this->gwm->set($di);
        if ($res) {
            $this->gwm->trans_commit();
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->gwm->trans_rollback();
            $this->status = 900;
            $this->message = 'Cant add data right now';
        }

        $this->gwm->trans_end();

        $this->__json_out($data);
    }

    public function hapus($id)
    {
        $d = $this->__init();
        $data = array();

        $id = (int) $id;
        if ($id<=0) {
            $this->status = 450;
            $this->message = 'Invalid ID';
            $this->__json_out($data);
            die();
        }

        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access, please login';
            header("HTTP/1.0 400 Unauthorized access, please login");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $res = $this->gwm->del($nation_code, $id);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->status = 902;
            $this->message = 'Failed removing data from database, please try again later';
        }
        $this->__json_out($data);
    }
}