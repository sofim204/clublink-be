<?php
	class Listing extends JI_Controller{

	public function __construct(){
   		parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'community';
		$this->current_page = 'community_list';
		$this->load("admin/community_list_model","list_model");
		$this->load("api_admin/c_community_list_model",'a_list_model');
		$this->load("admin/community_discussion_model","discussion_model");
		$this->load("admin/community_image_model","comm_image_model");
		$this->load('api_admin/a_pengguna_model','apl');
	}

    // private function __convertToEmoji($text){
    //     $value = json_decode($text);
    //     if ($value) {
    //         return json_decode($text);
    //     } else {
    //         return json_decode('"'.$text.'"');
    //     }
    // }

	// by Muhammad Sofi 28 December 2021 20:00 | read text with emoji
	private function __convertToEmoji($text){
		$value = $text;
		$readTextWithEmoji = preg_replace("/\\\\u([0-9A-F]{2,5})/i", "&#x$1;", $value);
		return $readTextWithEmoji;
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

		$pengguna = $data['sess']->admin;
		$cats = array();
		$cat = array();

		$this->setTitle('Community List '.$this->site_suffix_admin);

		$count = $this->list_model->count_reported();
		$discussion_count = $this->discussion_model->count_reported();

		$data['count'] = $count;
		$data['discussion_count'] = $discussion_count;
		$data['list'] = array();
		$data['admin_name'] = $data['sess']->admin->user_alias;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("community/list/home_modal",$data);
		$this->putThemeContent("community/list/home",$data);
		$this->putJsContent("community/list/home_js",$data);

		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function detail($id){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

        //get current admin
        $pengguna = $data['sess']->admin;
        $nation_code = $pengguna->nation_code; //get nation_code from current admin
		
		// by Muhammad Sofi - 17 November 2021 17:20
		$likes = $this->list_model->getAllLikes($id);
		$list_post = $this->list_model->detail($id);
		// if(isset($list_post[0]->description)){
		// 	$list_post[0]->description = $this->__convertToEmoji($list_post[0]->description);
		// }
		
		// by Muhammad Sofi - 9 December 2021 | showing emoji on title
		if(isset($list_post->title)){
			$list_post->title = $this->__convertToEmoji($list_post->title);
		}

		if(isset($list_post->description)){
			$list_post->description = $this->__convertToEmoji($list_post->description);
		}

		if(isset($list_post->address2)){
			$list_post->address2 = $list_post->kelurahan.', '.$list_post->kecamatan.', '.$list_post->kabkota;
		}
		
		$post_image = $this->comm_image_model->getByCommunityId($nation_code, $id);
		$post_videos = $this->comm_image_model->getVideoByCommunityId($nation_code, $id);
		$data['total_video'] = $this->comm_image_model->getTotalUploadVideo($nation_code, $id, "notlike");
        $data['total_uploading_image'] = $this->comm_image_model->getTotalUploadVideo($nation_code, $id, "like");
		$data['default_image'] = $this->list_model->getCategoryImageById($nation_code, $id);

		$data['likes'] = $likes;
		// $data['list_post'] = $list_post[0];
		$data['list_post'] = $list_post;
		$data['post_image'] = $post_image;
		$data['post_videos'] = $post_videos;
		$data['admin_name'] = $data['sess']->admin->user_alias;

		$this->setTitle('Community: Detail Community'.$this->site_suffix_admin);

		$this->putThemeContent("community/list/detail",$data);
		$this->putJsContent("community/list/detail_js",$data);

		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function reported(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$data['admin_name'] = $data['sess']->admin->user_alias;
        $data['admin_list'] = $this->apl->getAdminName(62, "");
		
		$this->setTitle('Community: Reported Community Post'.$this->site_suffix_admin);

		$this->putThemeContent("community/list/reported_modal",$data);
		$this->putThemeContent("community/list/reported",$data);
		$this->putJsContent("community/list/reported_js",$data);

		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function cs_work_history()
	{
		$this->current_page = 'cs_work_history_community';
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$data['admin_name'] = $data['sess']->admin->user_alias;
        $data['admin_list'] = $this->apl->getAdminName(62, "");
		
		$this->setTitle('Community: CS Work History - Reported Community Post'.$this->site_suffix_admin);

		$this->putThemeContent("community/list/cs_work_history/index",$data);
		$this->putJsContent("community/list/cs_work_history/index_js",$data);

		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function reported_discussion(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$this->setTitle('Community: Reported Community Discussion'.$this->site_suffix_admin);

		$this->putThemeContent("community/discussion/reported_modal",$data);
		$this->putThemeContent("community/discussion/reported",$data);
		$this->putJsContent("community/discussion/reported_js",$data);

		$this->loadLayout('col-2-left', $data);
		$this->render();
	}

	public function export() 
	{
		$this->lib('phpexcel/PHPExcel','','inc');
        $this->lib('phpexcel/PHPExcel/Writer/Excel2007','','inc');

        $object = new PHPExcel();
        $object->setActiveSheetIndex(0);
        $table_columns = [
			"Tanggal Submit", 
			"Title", 
			"Deskripsi", 
			"Nama Creator", 
			"Alamat Creator",
			"Email Creator",
			"Reported By", 
			"Takedown By", 
			"total"
		];

        $column = 0;

		foreach ($table_columns as $filed) {
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $filed);

            $column ++;
        }

		$excel_row = 2;
		$userId =  $this->input->post("userId") == '-' ? "IS NULL" : $this->input->post("userId");
		$from = $this->input->post("fromDate");
		$to = $this->input->post("toDate");

		$data = $this->a_list_model->getAllBy($userId, $from, $to);
		
		 foreach ($data as $row) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $row->cdate);
            $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $row->title);
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, empty($row->deskripsi) ? '-' : $row->deskripsi);
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, empty($row->reported_post_owner) ? '-' : $row->reported_post_owner);
            $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, empty($row->reported_post_owner_address) ? '-' : $row->reported_post_owner_address);
            $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, empty($row->reported_post_owner_email) ? '-' : $row->reported_post_owner_email);
            $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, empty($row->reporter_user_name) ? '-' : $row->reporter_user_name);
            $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $row->admin_name == 0 ? 'admin' : $row->admin_name);
            $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $row->total_reported_post);

            $excel_row ++;
        }

        $objWriter = new PHPExcel_Writer_Excel2007($object);
        $filename = "file" . time();
		ob_start();
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
		ob_end_clean();

		$response =  [
			'op' => 'ok',
			'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($xlsData),
			'filename' => $filename
		];

		echo json_encode($response);

	}
}
