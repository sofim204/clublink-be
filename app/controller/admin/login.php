<?php
    class Login extends JI_Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->setTheme('admin');
            $this->load("admin/a_pengguna_model", "apm");
            $this->load("admin/a_pengguna_module_model", "apmm");
            $this->load("admin/a_modules_model", "amod");
        }

        private function __passGen($password)
        {
            $password = preg_replace('/[^a-zA-Z0-9]/', '', $password);
            ;
            return password_hash($password, PASSWORD_DEFAULT);
        }
        private function __passClear($password)
        {
            return preg_replace('/[^a-zA-Z0-9]/', '', $password);
        }

        public function index()
        {
            $data = $this->__init();

            $this->setTitle("Login ".$this->site_suffix_admin);

            $this->putJsFooter($this->skins->admin.'js/pages/login.js');

            $this->putThemeContent("login/home", $data);
            $this->putJsContent('login/home_bottom', $data);
            $this->loadLayout('login', $data);
            $this->render();
        }
        public function proses()
        {
            $data = $this->__init();
            $username = $this->input->post("username");
            $password = $this->__passClear($this->input->post("password"));
            if (strlen($username)>3 && strlen($password)>3) {
                $username = iconv('UTF-8', 'ASCII//TRANSLIT', $username);
                $pengguna = $this->apm->auth($username);
                if (isset($pengguna->id)) {
                    $nation_code = $pengguna->nation_code;
                    //check active
                    if (empty($pengguna->is_active)) {
                        $data['pesan_info'] = 'The admin user has been deactivated';
                        $this->putJsFooter($this->skins->admin.'js/pages/login.js');
                        $this->putThemeContent("login/home", $data);
                        $this->putJsContent('login/home_bottom', $data);
                        $this->loadLayout('login', $data);
                        $this->render();
                        die();
                    }

                    //check password
                    $pv1 = 1;
                    $pv2 = 1;
                    if (md5($password) != $pengguna->password) {
                        $pv1=0;
                    }
                    if (!password_verify($password, $pengguna->password)) {
                        $pv2=0;
                    }
                    if (empty($pv1) && empty($pv2)) {
                        $data['pesan_info'] = 'Invalid username or password';
                        $this->putJsFooter($this->skins->admin.'js/pages/login.js');
                        $this->putThemeContent("login/home", $data);
                        $this->putJsContent('login/home_bottom', $data);
                        $this->loadLayout('login', $data);
                        $this->render();
                        die();
                    }

                    //upgrade password  encryption
                    if (!empty($pv1) && empty($pv2)) {
                        $this->apm->update($pengguna->nation_code, $pengguna->id, array("password"=>$this->__passGen($password)));
                    }

                    $sess = $data['sess'];
                    if (!is_object($sess)) {
                        $sess = new stdClass();
                    }
                    if (!isset($sess->admin)) {
                        $sess->admin = new stdClass();
                    }
                    $sess->admin = $pengguna;
                    $sess->admin->modules = $this->apmm->getUserModules($nation_code, $pengguna->id);
                    $sess->admin->menus = new stdClass();
                    $sess->admin->menus->left = array();

                    //get modules
                    $allowed_all = 0;
                    $modules = array();
                    $sess->admin->modules = $this->apmm->getUserModules($nation_code, $pengguna->id);
                    foreach ($sess->admin->modules as $m) {
                        $m->identifier = $m->a_modules_identifier;
                        $id = $m->identifier;
                        if (!isset($modules[$id])) {
                            $modules[$id] = new stdClass();
                        }
                        $modules[$id] = $m;
                        if (empty($id) && $m->rule == 'allowed_except') {
                            $allowed_all = 1;
                            break;
                        } elseif (!empty($id) && $m->rule == 'allowed') {
                            $modules[$id] = $m;
                        }
                    }
                    $sess->admin->modules = $modules;
                    unset($modules,$m);
                    $sess->admin->menus = new stdClass();
                    $sess->admin->menus->left = array();

                    //building menu: left
                    $parmod = $this->amod->getAllParent($nation_code);
                    if ($allowed_all) {
                        $sess->admin->modules = array();
                        foreach ($parmod as $pm) {
                            $pmid = $pm->identifier;
                            if (!isset($sess->admin->menus->left[$pmid])) {
                                $sess->admin->menus->left[$pmid] = new stdClass();
                            }
                            $sess->admin->menus->left[$pmid] = $pm;
                            $sess->admin->menus->left[$pmid]->childs = array();
                            $chimod = $this->amod->getChild($nation_code, $pm->identifier);
                            if (count($chimod)>0) {
                                foreach ($chimod as $cm) {
                                    $cmid = $cm->identifier;
                                    if (!isset($sess->admin->menus->left[$pmid]->childs[$cmid])) {
                                        $sess->admin->menus->left[$pmid]->childs[$cmid] = new stdClass();
                                    }
                                    $sess->admin->menus->left[$pmid]->childs[$cmid] = $cm;
                                    $sess->admin->modules[$cmid] = $cm;
                                }
                            }
                            $sess->admin->modules[$pmid] = $pm;
                        }
                    } else {
                        foreach ($parmod as $pm) {
                            $pmid = $pm->identifier;
                            if (!isset($sess->admin->modules[$pmid])) {
                                continue;
                            }
                            if ($sess->admin->modules[$pmid]->rule != 'allowed') {
                                continue;
                            }
                            if (!isset($sess->admin->menus->left[$pmid])) {
                                $sess->admin->menus->left[$pmid] = new stdClass();
                            }
                            $sess->admin->menus->left[$pmid] = $pm;
                            $sess->admin->menus->left[$pmid]->childs = array();
                            $chimod = $this->amod->getChild($nation_code, $pm->identifier);
                            if (count($chimod)>0) {
                                foreach ($chimod as $cm) {
                                    $cmid = $cm->identifier;
                                    if (!isset($sess->admin->modules[$cmid])) {
                                        continue;
                                    }
                                    if ($sess->admin->modules[$cmid]->rule != 'allowed') {
                                        continue;
                                    }
                                    if (!isset($sess->admin->menus->left[$pmid]->childs[$cmid])) {
                                        $sess->admin->menus->left[$pmid]->childs[$cmid] = new stdClass();
                                    }
                                    $sess->admin->menus->left[$pmid]->childs[$cmid] = $cm;
                                    $sess->admin->modules[$cmid] = $cm;
                                }
                            }
                            $sess->admin->modules[$pmid] = $pm;
                        }
                    }

                    $this->setKey($sess);
                    redir(base_url_admin(""));
                    die();
                } else {
                    $data['pesan_info'] = 'Invalid username or password';
                }
            } else {
                $data['pesan_info'] = 'Invalid username or password';
            }
            $this->putJsFooter($this->skins->admin.'js/pages/login.js');

            $this->putThemeContent("login/home", $data);
            $this->putJsContent('login/home_bottom', $data);
            $this->loadLayout('login', $data);
            $this->render();
        }
        public function auth()
        {
            $data = $this->__init();

            //collect input
            $username = $this->input->post("username");
            $password = $this->input->post("password");

            //default response
            $this->status = 102;
            $this->message = 'Invalid username or password';
            $dt = array();
            $dt['redirect_url'] = base_url_admin('login');

            if (strlen($username)>3 && strlen($password)>3) {
                $pengguna = $this->apm->auth($username);
                if (isset($pengguna->id)) {
                    $nation_code = $pengguna->nation_code;
                    //check admin status
                    if (empty($pengguna->id)) {
                        $this->status = 103;
                        $this->message = 'This admin user has been deactivated';
                        $this->__json_out($dt);
                        die();
                    }

                    //check password
                    $pv1 = 1;
                    $pv2 = 1;
                    if (md5($password) != $pengguna->password) {
                        $pv1=0;
                    }
                    if (!password_verify($password, $pengguna->password)) {
                        $pv2=0;
                    }
                    if (empty($pv1) && empty($pv2)) {
                        $this->status = 104;
                        $this->message = 'Invalid username or password';
                        $this->__json_out($dt);
                        die();
                    }

                    //upgrade password  encryption
                    if (!empty($pv1) && empty($pv2)) {
                        $this->apm->update($pengguna->nation_code, $pengguna->id, array("password"=>$this->__passGen($password)));
                    }

                    //add to session
                    $sess = $data['sess'];
                    if (!is_object($sess)) {
                        $sess = new stdClass();
                    }
                    if (!isset($sess->admin)) {
                        $sess->admin = new stdClass();
                    }
                    $sess->admin = $pengguna;

                    //get modules
                    $allowed_all = 0;
                    $modules = array();
                    $sess->admin->modules = $this->apmm->getUserModules($nation_code, $pengguna->id);
                    foreach ($sess->admin->modules as $m) {
                        $m->identifier = $m->a_modules_identifier;
                        $id = $m->identifier;
                        if (!isset($modules[$id])) {
                            $modules[$id] = new stdClass();
                        }
                        $modules[$id] = $m;
                        if (empty($id) && $m->rule == 'allowed_except') {
                            $allowed_all = 1;
                            break;
                        } elseif (!empty($id) && $m->rule == 'allowed') {
                            $modules[$id] = $m;
                        }
                    }
                    $sess->admin->modules = $modules;
                    unset($modules,$m);
                    $sess->admin->menus = new stdClass();
                    $sess->admin->menus->left = array();

                    //building menu: left
                    $parmod = $this->amod->getAllParent($nation_code);
                    if ($allowed_all) {
                        $sess->admin->modules = array();
                        foreach ($parmod as $pm) {
                            $pmid = $pm->identifier;
                            if (!isset($sess->admin->menus->left[$pmid])) {
                                $sess->admin->menus->left[$pmid] = new stdClass();
                            }
                            $sess->admin->menus->left[$pmid] = $pm;
                            $sess->admin->menus->left[$pmid]->childs = array();
                            $chimod = $this->amod->getChild($nation_code, $pm->identifier);
                            if (count($chimod)>0) {
                                foreach ($chimod as $cm) {
                                    $cmid = $cm->identifier;
                                    if (!isset($sess->admin->menus->left[$pmid]->childs[$cmid])) {
                                        $sess->admin->menus->left[$pmid]->childs[$cmid] = new stdClass();
                                    }
                                    $sess->admin->menus->left[$pmid]->childs[$cmid] = $cm;
                                    $sess->admin->modules[$cmid] = $cm;
                                }
                            }
                            $sess->admin->modules[$pmid] = $pm;
                        }
                    } else {
                        foreach ($parmod as $pm) {
                            $pmid = $pm->identifier;
                            if (!isset($sess->admin->modules[$pmid])) {
                                continue;
                            }
                            if ($sess->admin->modules[$pmid]->rule != 'allowed') {
                                continue;
                            }
                            if (!isset($sess->admin->menus->left[$pmid])) {
                                $sess->admin->menus->left[$pmid] = new stdClass();
                            }
                            $sess->admin->menus->left[$pmid] = $pm;
                            $sess->admin->menus->left[$pmid]->childs = array();
                            $chimod = $this->amod->getChild($nation_code, $pm->identifier);
                            if (count($chimod)>0) {
                                foreach ($chimod as $cm) {
                                    $cmid = $cm->identifier;
                                    if (!isset($sess->admin->modules[$cmid])) {
                                        continue;
                                    }
                                    if ($sess->admin->modules[$cmid]->rule != 'allowed') {
                                        continue;
                                    }
                                    if (!isset($sess->admin->menus->left[$pmid]->childs[$cmid])) {
                                        $sess->admin->menus->left[$pmid]->childs[$cmid] = new stdClass();
                                    }
                                    $sess->admin->menus->left[$pmid]->childs[$cmid] = $cm;
                                    $sess->admin->modules[$cmid] = $cm;
                                }
                            }
                            $sess->admin->modules[$pmid] = $pm;
                        }
                    }

                    //building default result
                    $this->status = 200;
                    $this->message = 'Success';

                    $this->setKey($sess);
                    $dt['redirect_url'] = base_url_admin();
                }
            }
            $this->__json_out($dt);
        }
        public function lupa_lagi()
        {
            $data = $this->__init();
            $email = $this->input->post("email");
            $password = $this->input->post("password");
            if (strlen($username)>3 && strlen($password)>3) {
                $res = $this->apm->auth($username, $password);
                if (isset($res->id)) {
                    $sess = $data['sess'];
                    if (!is_object($sess)) {
                        $sess = new stdClass();
                    }
                    if (!isset($sess->admin)) {
                        $sess->admin = new stdClass();
                    }
                    $sess->admin = $res;
                    $this->login_admin = 1;
                    $this->setKey($sess);
                    redir(base_url_admin(""));
                    die();
                } else {
                    $data['pesan_info'] = 'Username atau password salah';
                }
            } else {
                $data['pesan_info'] = 'Username atau password salah';
            }
            $this->putJsFooter($this->skins->admin.'js/pages/login.js');

            $this->putThemeContent("login/home", $data);
            $this->putJsContent('login/home_bottom', $data);
            $this->loadLayout('login', $data);
            $this->render();
        }
    }
