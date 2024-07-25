<?php

class Check_upload_status_video extends JI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->lib("seme_log");
    $this->load("api_cron/common_code_model", "ccm");
    $this->load("api_cron/c_produk_foto_model", "cpfm");
    $this->load("api_cron/c_community_attachment_model", "ccam");
    $this->load("api_cron/group/i_group_post_attachment_model", "igpam");
  }

  public function index()
  {
    //open transaction
    // $this->order->trans_start();

    //change log filename
    // $this->seme_log->changeFilename("cron.log");

    //put on log
    $this->seme_log->write("api_cron", 'API_Cron/Check_upload_status_video::index --Start');

    $nation_code = 62;

    $limit = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C4");
    if (!isset($limit->remark)) {
      $limit->remark = 15;
    }

    //product
    $videoStillUploading = $this->cpfm->getAll($nation_code, "video", "uploading", date("Y-m-d H:i:s",strtotime("-".$limit->remark." minutes")));
    if($videoStillUploading){
    	//start transaction
      // $this->order->trans_start();

      foreach ($videoStillUploading as $video) {
        $this->cpfm->delByIdProdukIdJenis($video->nation_code, $video->id, $video->c_produk_id, $video->jenis);
        // $this->order->trans_commit();

        if($video->url != "media/produk_video/default.png"){
          $file_path = SENEROOT.$video->url;
          if (file_exists($file_path)) {
            unlink($file_path);
          }
        }

        if($video->url_thumb != "media/produk_video/default.png"){
          $file_path = SENEROOT.$video->url_thumb;
          if (file_exists($file_path)) {
            unlink($file_path);
          }
        }
  		}
  		unset($videoStillUploading, $video);
    }

    $videoNeedUploading = $this->cpfm->getAll($nation_code, "video", "uploading","","yes");
    if($videoNeedUploading){
      foreach ($videoNeedUploading as $video) {
        if(file_exists(SENEROOT.$video->tmp_url)){
          $targetdir = $this->media_produk_video;
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

          $tahun = date("Y");
          $targetdir = $targetdir.DIRECTORY_SEPARATOR.$tahun;
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

          $bulan = date("m");
          $targetdir = $targetdir.DIRECTORY_SEPARATOR.$bulan;
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

          $filename = "$video->nation_code-$video->c_produk_id-$video->id-".date('YmdHis');
          $filename = $filename.".".pathinfo($video->tmp_url, PATHINFO_EXTENSION);

          rename(SENEROOT.$video->tmp_url, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
          $video_url = str_replace("//", "/", $targetdir.'/'.$filename);
          $video_url = str_replace("\\", "/", $video_url);

          $di = array();
          $di['tmp_url'] = "";
          $di['convert_status'] = "waiting";
          $di['url'] = $video_url;
          $this->cpfm->update($video->nation_code, $video->c_produk_id, $video->id, $video->jenis, $di);

          if($video->url != "media/produk_video/default.png"){
            $file_path = SENEROOT.$video->url;
            if (file_exists($file_path)) {
              unlink($file_path);
            }
          }
        }else{
          $di = array();
          $di['tmp_url'] = "";
          $this->cpfm->update($video->nation_code, $video->c_produk_id, $video->id, $video->jenis, $di);
        }
      }
      unset($videoNeedUploading, $video);
    }

    //community
    $videoStillUploading = $this->ccam->getAll($nation_code, "video", "uploading", date("Y-m-d H:i:s",strtotime("-".$limit->remark." minutes")));
    if($videoStillUploading){
      //start transaction
      // $this->order->trans_start();

      foreach ($videoStillUploading as $video) {
        $this->ccam->delByIdCommunityId($video->nation_code, $video->id, $video->c_community_id, $video->jenis);
        // $this->order->trans_commit();

        if($video->url != $this->media_community_video."default.png"){
          $file_path = SENEROOT.$video->url;
          if (file_exists($file_path)) {
            unlink($file_path);
          }
        }

        if($video->url_thumb != $this->media_community_video."default.png"){
          $file_path = SENEROOT.$video->url_thumb;
          if (file_exists($file_path)) {
            unlink($file_path);
          }
        }
      }
      unset($videoStillUploading, $video);
    }

    $videoNeedUploading = $this->ccam->getAll($nation_code, "video", "uploading","","yes");
    if($videoNeedUploading){
      foreach ($videoNeedUploading as $video) {
        if(file_exists(SENEROOT.$video->tmp_url)){
          $targetdir = $this->media_produk_video;
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

          $tahun = date("Y");
          $targetdir = $targetdir.DIRECTORY_SEPARATOR.$tahun;
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

          $bulan = date("m");
          $targetdir = $targetdir.DIRECTORY_SEPARATOR.$bulan;
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

          $filename = "$video->nation_code-$video->c_community_id-$video->id-".date('YmdHis');
          $filename = $filename.".".pathinfo($video->tmp_url, PATHINFO_EXTENSION);

          rename(SENEROOT.$video->tmp_url, SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
          $video_url = str_replace("//", "/", $targetdir.'/'.$filename);
          $video_url = str_replace("\\", "/", $video_url);

          $di = array();
          $di['tmp_url'] = "";
          $di['convert_status'] = "waiting";
          $di['url'] = $video_url;
          $this->ccam->update($video->nation_code, $video->c_community_id, $video->id, $video->jenis, $di);

          if($video->url != $this->media_community_video."default.png"){
            $file_path = SENEROOT.$video->url;
            if (file_exists($file_path)) {
              unlink($file_path);
            }
          }
        }else{
          $di = array();
          $di['tmp_url'] = "";
          $this->ccam->update($video->nation_code, $video->c_community_id, $video->id, $video->jenis, $di);
        }
      }
      unset($videoNeedUploading, $video);
    }

    //club post
    $videoStillUploading = $this->igpam->getAll($nation_code, "video", "uploading", date("Y-m-d H:i:s",strtotime("-".$limit->remark." minutes")));
    if($videoStillUploading){
      foreach ($videoStillUploading as $video) {
        $this->igpam->del($video->id);

        if($video->url != $this->media_community_video."default.png"){
          $file_path = SENEROOT.$video->url;
          if (file_exists($file_path)) {
            unlink($file_path);
          }
        }

        if($video->url_thumb != $this->media_community_video."default.png"){
          $file_path = SENEROOT.$video->url_thumb;
          if (file_exists($file_path)) {
            unlink($file_path);
          }
        }
      }
      unset($videoStillUploading, $video);
    }

    //end transacation
    // $this->order->trans_end();

    //put on log
    $this->seme_log->write("api_cron", 'API_Cron/Check_upload_status_video::index --Stop');
    die();
  }
}
