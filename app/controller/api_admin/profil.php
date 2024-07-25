<?php
class Profil extends JI_Controller{
  public function __construct(){
    parent::__construct();
    $this->load("api_admin/a_pengguna_model",'apm');
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

      if($temp["size"]>500000){
        $this->status = 500;
        $this->message = 'Image file size too big, please try another image';
        $this->__json_out(array());
        die();
      }

      if(mime_content_type($temp['tmp_name']) == 'image/webp'){
        $this->status = 500;
        $this->message = 'WebP currently unsupported by system, please try another image';
        $this->__json_out(array());
        die();
      }
      // Verify extension
      $ext = strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION));
      if (!in_array($ext, array("jpg", "png"))) {
          header("HTTP/1.0 500 Invalid extension.");
          return 0;
      }

      // Create magento style media directory
      $temp['name'] = ($admin_id).'-'.date('is').'.'.$ext;
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

  private function __passGen($password){
    $password = preg_replace('/[^a-zA-Z0-9]/', '', $password);;
    return password_hash($password, PASSWORD_DEFAULT);
  }
  private function __passClear($password){
    return preg_replace('/[^a-zA-Z0-9]/', '', $password);
  }

  public function index(){
    $this->status = '404';
    header("HTTP/1.0 404 Not Found");
    $data = array();
    $this->__json_out($data);
  }
  public function edit(){
    $d = $this->__init();
    $data = array();

    if(!$this->admin_login){
      $this->status = 409;
      $this->message = 'Access Denied';
      header("HTTP/1.0 409 Access Denied");
      $data = array();
      $this->__json_out($data);
      die();
    }
    $pengguna = $d['sess']->admin;
    $a_pengguna_id = $pengguna->id;
    $nation_code = $pengguna->nation_code;

    $this->status = 900;
    $this->message = 'Failed updating to database';

    //collect updated data
    $du = array();
    $username = $this->input->post('username');
    $username = iconv('UTF-8', 'ASCII//TRANSLIT', $username);
    if(strlen($username>4)) $du['username'] = $username;
    $du['email'] = $this->input->post('email');
    if(strlen($du['email']<=4)) unset($du['email']);
    $du['nama'] = $this->input->post('nama');
    if(empty($du['nama'])) $du['nama'] = '';

    //update to database
    $res = $this->apm->update($nation_code, $a_pengguna_id,$du);
    if($res){
      $admin = $this->apm->getById($nation_code, $a_pengguna_id);
      $this->status = 200;
      $this->message = 'Success';
      $sess = $d['sess'];
      $sess->admin->username = $admin->username;
      $sess->admin->email = $admin->email;
      $sess->admin->nama = $admin->nama;
      $this->setKey($sess);
    }
    $this->__json_out($data);
  }

  public function password_change(){
    $d = $this->__init();
    $data = array();

    if(!$this->admin_login){
      $this->status = 409;
      $this->message = 'Access Denied';
      header("HTTP/1.0 409 Access Denied");
      $this->__json_out($data);
      die();
    }
    $pengguna = $d['sess']->admin;
    $a_pengguna_id = $pengguna->id;
    $nation_code = $pengguna->nation_code;

    //check oldpassword
    $pv1 = 1;
    $pv2 = 1;
    $oldpassword = $this->__passClear($this->input->post('oldpassword'));

    if(md5($oldpassword) != $pengguna->password){
      $pv1 = 0;
    }
    if(!password_verify($oldpassword,$pengguna->password)){
      $pv2 = 0;
    }
    if(empty($pv1) && empty($pv2)){
      $this->status = 1001;
      $this->message = 'Invalid old password, please try again';
      $this->__json_out($data);
      die();
    }
    $newpassword = $this->__passClear($this->input->post('newpassword'));
    if(strlen($newpassword)<=6){
      $this->status = 1002;
      $this->message = 'New password too short, please try again';
      $this->__json_out($data);
      die();
    }
    $confirm_newpassword = $this->__passClear($this->input->post('confirm_newpassword'));
    if($confirm_newpassword != $newpassword){
      $this->status = 1002;
      $this->message = 'New password and confirmation new password does not match';
      $this->__json_out($data);
      die();
    }

    //default response
    $this->status = 900;
    $this->message = 'Failed updating to database';

    //collect updated data
    $du = array();
    $du['password'] = $this->__passGen($newpassword);
    $res = $this->apm->update($nation_code, $a_pengguna_id,$du);
    if($res){
      $admin = $this->apm->getById($nation_code, $a_pengguna_id);
      $this->status = 200;
      $this->message = 'Success';
      $sess = $d['sess'];
      $sess->admin->password = $admin->password;
      $this->setKey($sess);
    }
    $this->__json_out($data);
  }


  public function picture_change(){
    $d = $this->__init();
    $data = array();

    if(!$this->admin_login){
      $this->status = 409;
      $this->message = 'Access Denied';
      header("HTTP/1.0 409 Access Denied");
      $this->__json_out($data);
    }
    $pengguna = $d['sess']->admin;
    $a_pengguna_id = $pengguna->id;
    $nation_code = $pengguna->nation_code;

    $foto = $this->__uploadFoto($a_pengguna_id);
    if(strlen($foto)<=4){
      $this->status = 1001;
      $this->message = 'Failed uploading image file, please try again';
      $this->__json_out($data);
      die();
    }
    //default response
    $this->status = 900;
    $this->message = 'Failed updating to database';

    //collect updated data
    $du = array();
    $du['foto'] = $foto;
    $res = $this->apm->update($nation_code, $a_pengguna_id,$du);
    if($res){
      $foto_file = SENEROOT.DIRECTORY_SEPARATOR.$pengguna->foto;
      if(file_exists($foto_file)) unlink($foto_file);
      $pengguna = $this->apm->getById($nation_code, $a_pengguna_id);
      $this->status = 200;
      $this->message = 'Success';
      $sess = $d['sess'];
      $sess->admin->foto = $pengguna->foto;
      $this->setKey($sess);
    }
    $this->__json_out($data);
  }
}
