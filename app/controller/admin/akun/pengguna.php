<?php
class Pengguna extends JI_Controller {
    var $media_pengguna = 'media/pengguna';
    var $apm = 'model/a_pengguna_model';


    public function __construct(){
    parent::__construct();
        $this->setTheme('admin');
        $this->current_parent = 'akun';
        $this->current_page = 'akun_pengguna';
    }


    private function __uploadFoto($admin_id){
        //building path target
        $fldr = $this->media_pengguna;
        $folder = SENEROOT.DIRECTORY_SEPARATOR.$fldr.DIRECTORY_SEPARATOR;
        $folder = str_replace('\\','/',$folder);
        $folder = str_replace('//','/',$folder);
        $ifol = realpath($folder);

        //check folder
        if(!$ifol) mkdir($folder); //create folder
        $ifol = realpath($folder); //get current realpath

        reset($_FILES);
        $temp = current($_FILES);
        if (is_uploaded_file($temp['tmp_name'])){
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                // same-origin requests won't set an origin. If the origin is set, it must be valid.
                header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            }
            header('Access-Control-Allow-Credentials: true');
            header('P3P: CP="There is no P3P policy."');

            // Sanitize input
            if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
                    header("HTTP/1.0 500 Invalid file name.");
                    return 0;
            }
            // Verify extension
            $ext = strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, array("jpg", "png"))) {
                    header("HTTP/1.0 500 Invalid extension.");
                    return 0;
            }

            // Create magento style media directory
            $temp['name'] = md5($admin_id).date('is').'.'.$ext;
            $name  = $temp['name'];
            $name1 = date("Y");
            $name2 = date("m");

            //building directory structure
            if(PHP_OS == "WINNT"){
                if(!is_dir($ifol)) mkdir($ifol);
                $ifol = $ifol.DIRECTORY_SEPARATOR.$name1.DIRECTORY_SEPARATOR;
                if(!is_dir($ifol)) mkdir($ifol);
                $ifol = $ifol.DIRECTORY_SEPARATOR.$name2.DIRECTORY_SEPARATOR;
                if(!is_dir($ifol)) mkdir($ifol);
            }else{
                if(!is_dir($ifol)) mkdir($ifol,0775);
                $ifol = $ifol.DIRECTORY_SEPARATOR.$name1.DIRECTORY_SEPARATOR;
                if(!is_dir($ifol)) mkdir($ifol,0775);
                $ifol = $ifol.DIRECTORY_SEPARATOR.$name2.DIRECTORY_SEPARATOR;
                if(!is_dir($ifol)) mkdir($ifol,0775);
            }

            // Accept upload if there was no origin, or if it is an accepted origin

            $filetowrite = $ifol . $temp['name'];

            if(file_exists($filetowrite)) unlink($filetowrite);
            move_uploaded_file($temp['tmp_name'], $filetowrite);
            if(file_exists($filetowrite)){
                $this->lib("wideimage/WideImage",'wideimage',"inc");
                WideImage::load($filetowrite)->resize(320)->saveToFile($filetowrite);
                return $fldr."/".$name1."/".$name2."/".$name;
            }else{
                return 0;
            }
        } else {
            // Notify editor that the upload failed
            //header("HTTP/1.0 500 Server Error");
            return 0;
        }
    }

    private function __accessModules($nation_code,$id=""){
        $this->load('admin/a_modules_model', 'amm');
        $res = '';
        $amm = $this->amm->getChildModules($nation_code,$id);
        if (!empty($amm))
        {
            $td = '';
            $n  = 1;
            foreach ($amm as $am)
            {
                if (empty($id))
                {
                    $td .= '<td width="50%" valign="top">';
                    $td .= '<label for="'. $am->identifier .'"><input id="'. $am->identifier .'" type="checkbox" name="a_modules_identifier[]" value="'. $am->identifier .'" data-key="parent" />&nbsp; '. $am->name .'</label>';
                    $td .= $this->__accessModules($nation_code,$am->identifier);
                    $td .= '</td>';
                    if ($n == 2)
                    {
                        $res   .= '<tr>'. $td .'</tr>';
                        $td     = '';
                        $n      = 1;
                    }
                    else
                    {
                        $n++;
                    }
                }
                else
                {
                    $res .= '<br><label for="'. $am->identifier .'"><input id="'. $am->identifier .'" type="checkbox" class="'. $id .'" name="a_modules_identifier[]" data-key="child" value="'. $am->identifier .'" />&nbsp; -- '. $am->name .'</label>';
                }
            }
            if (!empty($td))
            {
                $res .= '<tr>'. $td .'<td></td></tr>';
            }
        }
        return $res;
    }

    public function index(){
        $data = $this->__init();
        if(!$this->admin_login){
            redir(base_url_admin('login'));
            die();
        }
        if(!$this->checkPermissionAdmin($this->current_page)){
            redir(base_url_admin('forbidden'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;

        $this->setTitle('Akun Pengguna '.$this->site_suffix);
        $data['access'] = $this->__accessModules($nation_code);

        $this->setTitle("Manage Administrator ".$this->site_suffix_admin);
        $this->putThemeContent("akun/pengguna/home_modal",$data);
        $this->putThemeContent("akun/pengguna/home",$data);

        $this->putJsContent("akun/pengguna/home_bottom",$data);
        $this->loadLayout('col-2-left',$data);
        $this->render();
    }


}
