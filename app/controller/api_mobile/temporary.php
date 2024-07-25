<?php
class Temporary extends JI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->lib("seme_log");
    $this->load("api_mobile/b_user_model", 'bu');

  }

  //by Donny Dennison - 11 august 2022 10:46
  //fix rotated image after resize(thumb)
  //credit: https://stackoverflow.com/a/18919355/7578520
  // private function correctImageOrientation($filename) {
  //   //credit: https://github.com/FriendsOfCake/cakephp-upload/issues/221#issuecomment-50128062
  //   $exif = false;
  //   $size = getimagesize($filename, $info);
  //   if (!isset($info["APP13"])) {
  //     if (function_exists('exif_read_data')) {
  //       $exif = exif_read_data($filename);
  //       if($exif && isset($exif['Orientation'])) {
  //         $orientation = $exif['Orientation'];
  //         if($orientation != 1){
  //           $img = imagecreatefromjpeg($filename);
  //           $deg = 0;
  //           switch ($orientation) {
  //             case 3:
  //               $deg = 180;
  //               break;
  //             case 6:
  //               $deg = 270;
  //               break;
  //             case 8:
  //               $deg = 90;
  //               break;
  //           }
  //           if ($deg) {
  //             $img = imagerotate($img, $deg, 0);        
  //           }
  //           // then rewrite the rotated image back to the disk as $filename
  //           imagejpeg($img, $filename, 95);
  //         } // if there is some rotation necessary
  //       } // if have the exif orientation info
  //     } // if function exists
  //   }      
  // }

  // public function index()
  // {
  //     //initial
  //     $dt = $this->__init();

  //     //default result
  //     $data = array();
  //     $data['chat_room_total'] = 0;
  //     $data['chat_room'] = array();

  //     //check nation_code
  //     $nation_code = $this->input->get('nation_code');
  //     $nation_code = $this->nation_check($nation_code);
  //     if (empty($nation_code)) {
  //         $this->status = 101;
  //         $this->message = 'Missing or invalid nation_code';
  //         $this->__json_out($data);
  //         die();
  //     }

  //     //check apikey
  //     $apikey = $this->input->get('apikey');
  //     $c = $this->apikey_check($apikey);
  //     if (!$c) {
  //         $this->status = 400;
  //         $this->message = 'Missing or invalid API key';
  //         $this->__json_out($data);
  //         die();
  //     }

  //     //check apisess
  //     $apisess = $this->input->get('apisess');
  //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //     if (!isset($pelanggan->id)) {
  //         $this->status = 401;
  //         $this->message = 'Missing or invalid API session';
  //         $this->__json_out($data);
  //         die();
  //     }

  //     //default output
  //     $this->status = 200;
  //     $this->message = 'Success';

  //     //render as json
  //     $this->__json_out($data);
  // }

  public function image_add()
  {
    $dt = $this->__init();
    $keyname = 'foto';

    $data = array();
    $data['foto_url'] = '';

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    if (!isset($_FILES[$keyname])) {
      $this->status = 1300;
      $this->message = 'Upload failed';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }
    if ($_FILES[$keyname]['size']<=0) {
      $this->status = 1300;
      $this->message = 'Upload failed';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }
    // if ($_FILES[$keyname]['size']>=2500000) {
    //   $this->status = 1302;
    //   $this->message = 'Image file Size too big';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
    //   die();
    // }
    if (mime_content_type($_FILES[$keyname]['tmp_name'])=="image/webp") {
      $this->status = 1303;
      $this->message = 'WebP image file format is not supported.';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }
    $filenames = pathinfo($_FILES[$keyname]['name']);
    $fileext = '';
    if (isset($filenames['extension'])) {
      $fileext = strtolower($filenames['extension']);
    }
    if (!in_array($fileext, array("jpg", "png", "jpeg", "heic", "heif", "bmp"))) {
      $this->status = 1305;
      $this->message = 'Invalid file extension, please try other file';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $targetdir = $this->media_temporary;
    $targetdircheck = realpath(SENEROOT.$targetdir);
    if (empty($targetdircheck)) {
      if (PHP_OS == "WINNT") {
        if (!is_dir(SENEROOT.$targetdir)) {
          mkdir(SENEROOT.$targetdir);
        }
      } else {
        if (!is_dir(SENEROOT.$targetdir)) {
          mkdir(SENEROOT.$targetdir, 0775);
        }
      }
    }

    $filename = "$nation_code-$pelanggan->id-".date('YmdHis').rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
    $filethumb = $filename."-thumb.".$fileext;
    $filename = $filename.".".$fileext;

    move_uploaded_file($_FILES[$keyname]['tmp_name'], SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);

    //START by Donny Dennison - 11 august 2022 10:46
    //fix rotated image after resize(thumb)
    // if (in_array($fileext, array("jpg","jpeg"))) {
    //   $this->correctImageOrientation(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
    // }
    //END by Donny Dennison - 11 august 2022 10:46
    //fix rotated image after resize(thumb)

    $this->lib("wideimage/WideImage", 'wideimage', "inc");
    if (file_exists(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb)) {
      unlink(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
    }
    WideImage::load(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)->reSize(370)->saveToFile(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);

    $data['foto_url'] = str_replace("//", "/", $targetdir.'/'.$filename);
    $data['foto_url'] = str_replace("\\", "/", $data['foto_url']);
    $data['foto_url'] = $this->cdn_url($data['foto_url']);

    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
  }

  public function video_add(){
    $dt = $this->__init();
    $keyname = 'video';

    $data = array();
    $data['url'] = '';

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    if (!isset($_FILES[$keyname])) {
      $this->status = 1300;
      $this->message = 'Upload failed';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    if ($_FILES[$keyname]['size']<=0) {
      $this->status = 1300;
      $this->message = 'Upload failed';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    if ($_FILES[$keyname]['size'] > 104857600) {
      $this->status = 1308;
      $this->message = 'Video file Size too big, max size 100 MB';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    if (mime_content_type($_FILES[$keyname]['tmp_name'])=="image/webp") {
      $this->status = 1303;
      $this->message = 'WebP image file format is not supported.';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $filenames = pathinfo($_FILES[$keyname]['name']);
    $fileext = '';
    if (isset($filenames['extension'])) {
      $fileext = strtolower($filenames['extension']);
    }
    if (!in_array($fileext, array("mp4", "mov", "mkv"))) {
      $this->status = 1305;
      $this->message = 'Invalid file extension, please try other file';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $targetdir = $this->media_temporary;
    $targetdircheck = realpath(SENEROOT.$targetdir);
    if (empty($targetdircheck)) {
      if (PHP_OS == "WINNT") {
        if (!is_dir(SENEROOT.$targetdir)) {
          mkdir(SENEROOT.$targetdir);
        }
      } else {
        if (!is_dir(SENEROOT.$targetdir)) {
          mkdir(SENEROOT.$targetdir, 0775);
        }
      }
    }

    $filename = "$nation_code-$pelanggan->id-".date('YmdHis').rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
    // $filethumb = $filename."-thumb.".$fileext;
    $filename = $filename.".".$fileext;

    move_uploaded_file($_FILES[$keyname]['tmp_name'], SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);

    $data['url'] = str_replace("//", "/", $targetdir.'/'.$filename);
    $data['url'] = str_replace("\\", "/", $data['url']);
    $data['url'] = $this->cdn_url($data['url']);

    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
  }

  public function file_add()
  {
    $dt = $this->__init();
    $keyname = 'file';

    $data = array();
    $data['url'] = '';

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    if (!isset($_FILES[$keyname])) {
      $this->status = 1300;
      $this->message = 'Upload failed';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    if ($_FILES[$keyname]['size']<=0) {
      $this->status = 1300;
      $this->message = 'Upload failed';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    if ($_FILES[$keyname]['size'] > 104857600) {
      $this->status = 1308;
      $this->message = 'Video file Size too big, max size 100 MB';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    if (mime_content_type($_FILES[$keyname]['tmp_name'])=="image/webp") {
      $this->status = 1303;
      $this->message = 'WebP image file format is not supported.';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $filenames = pathinfo($_FILES[$keyname]['name']);
    $fileext = '';
    if (isset($filenames['extension'])) {
      $fileext = strtolower($filenames['extension']);
    }
    $listExtension = array(
      "png",
      "jpg",
      "jpeg",
      "bmp",
      "heic",
      "heif",
      "webp",
      "csv",
      "doc",
      "docx",
      "pdf",
      "ppt",
      "pptx",
      "xls",
      "xlsx",
      "mp4",
      "mov",
      "mp3",
      "mkv"
    );
    if (!in_array($fileext, $listExtension)) {
      $this->status = 1305;
      $this->message = 'Invalid file extension, please try other file';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $targetdir = $this->media_temporary;
    $targetdircheck = realpath(SENEROOT.$targetdir);
    if (empty($targetdircheck)) {
      if (PHP_OS == "WINNT") {
        if (!is_dir(SENEROOT.$targetdir)) {
          mkdir(SENEROOT.$targetdir);
        }
      } else {
        if (!is_dir(SENEROOT.$targetdir)) {
          mkdir(SENEROOT.$targetdir, 0775);
        }
      }
    }

    $file_name = basename($filenames['basename'],'.'.$filenames['extension']);
    $file_name = str_replace(" ", "_", $file_name);
    $file_name = str_replace("-", "_", $file_name);
    $file_name = str_replace("\\", "_", $file_name);
    $file_name = str_replace("/", "_", $file_name);
    $file_name = str_replace(":", "_", $file_name);
    $file_name = str_replace("*", "_", $file_name);
    $file_name = str_replace("?", "_", $file_name);
    $file_name = str_replace('"', "_", $file_name);
    $file_name = str_replace("<", "_", $file_name);
    $file_name = str_replace(">", "_", $file_name);
    $file_name = str_replace("|", "_", $file_name);
    $file_name = str_replace("&", "_", $file_name);

    $filename = "$file_name-$nation_code-$pelanggan->id-".date('YmdHis').rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
    $filename = $filename.".".$fileext;

    move_uploaded_file($_FILES[$keyname]['tmp_name'], SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);

    $data['url'] = str_replace("//", "/", $targetdir.'/'.$filename);
    $data['url'] = str_replace("\\", "/", $data['url']);
    $data['url'] = $this->cdn_url($data['url']);

    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
  }

}