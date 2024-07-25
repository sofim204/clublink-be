<?php

class Convert_video_club extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_cron/group/i_group_post_attachment_model", "igpam");
    }

    public function index()
    {
        //open transaction
        // $this->order->trans_start();

        //change log filename
        // $this->seme_log->changeFilename("cron.log");
        $this->seme_log->write("api_cron", 'API_Cron/Convert_Video_Club::index --start');

        $nation_code = 62;
        $videoinprocessing = $this->igpam->getAll($nation_code, "video", "processing");
        if($videoinprocessing){
            $this->seme_log->write("api_cron", 'API_Cron/Convert_Video_Club::index --not doing convert because there is video still processing convert');
        }else{
            $videoNeedConvert = $this->igpam->getAll($nation_code, "video", "waiting", "", "no", 2);
            if($videoNeedConvert){
            	//start transaction
    	        // $this->order->trans_start();

                foreach ($videoNeedConvert as $video) {
    	            $di = array();
    	            $di['convert_status'] = "processing";
    	            $this->igpam->update($video->nation_code, $video->id, $di);
                    // $this->order->trans_commit();
        		}
        		unset($video, $di);

                foreach ($videoNeedConvert as $video) {
                    $fileext = pathinfo($video->url, PATHINFO_EXTENSION);
    	    		$tempName = $video->nation_code.$video->i_group_post_id.$video->id.date('YmdHis').rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).".".$fileext;

                    // exec("ffmpeg -y -i ".SENEROOT.$video->url." -s 540x960 -preset slow -movflags faststart -crf 30 -ar 44100 ".SENEROOT.$this->media_temporary.DIRECTORY_SEPARATOR.$tempName." -hide_banner 2>&1", $responseFFmpeg , $statusFFmpeg);
                    exec("ffmpeg -y -i ".SENEROOT.$video->url." -preset slow -movflags faststart -crf 30 -ar 44100 ".SENEROOT.$this->media_temporary.DIRECTORY_SEPARATOR.$tempName." -hide_banner 2>&1", $responseFFmpeg , $statusFFmpeg);
    				if($statusFFmpeg == 0){
    	      			rename(SENEROOT.$this->media_temporary.DIRECTORY_SEPARATOR.$tempName, SENEROOT.$video->url);
    		            $di = array();
    		            $di['convert_status'] = "processed";
                        $di['file_size'] = filesize(SENEROOT.$video->url);
    		            $this->igpam->update($video->nation_code, $video->id, $di);
    		        }else{
    		            $di = array();
                        $di['convert_status'] = "processed";
                        $di['convert_response'] = json_encode($responseFFmpeg);
    		            $this->igpam->update($video->nation_code, $video->id, $di);
    		        }
        		}
        		unset($videoNeedConvert, $video, $di);
            }else{
                $this->seme_log->write("api_cron", 'API_Cron/Convert_Video_Club::index --not doing convert because there is no video in waiting');
            }
        }

        //end transacation
        // $this->order->trans_end();

        $this->seme_log->write("api_cron", 'API_Cron/Convert_Video_Club::index --stop');
        die();
    }
}
