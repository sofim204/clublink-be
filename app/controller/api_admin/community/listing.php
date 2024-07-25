<?php
class Listing extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->lib("seme_purifier");
		$this->load("api_admin/c_community_list_model",'list_model');
		$this->load("api_admin/c_community_report_model",'list_report_model');
		$this->load("api_admin/g_leaderboard_model",'glm_model');
		$this->load("api_admin/b_user_model",'bu_model');
		$this->load("api_admin/common_code_model", "ccm");
		$this->load("api_admin/g_leaderboard_point_history_model", "glphm");
		$this->load("api_admin/a_pengguna_model", "apm");

		//by Donny Dennison - 28 december 2022 17:46
		//bug take down community reply/discussion dont reduce the total reply/discussion
		$this->load("api_admin/c_community_discussion_model",'discussion_model');

		$this->load("api_mobile/d_pemberitahuan_model", "dpem");
		$this->load("api_mobile/b_user_setting_model", "busm");

		$this->current_parent = 'community';
		$this->current_page = 'community_list';
	}

	//credit: https://www.php.net/manual/en/function.com-create-guid.php#119168
    private function GUIDv4($trim = true)
    {
        // Windows
        if (function_exists('com_create_guid') === true) {
            if ($trim === true)
                return trim(com_create_guid(), '{}');
            else
                return com_create_guid();
        }

        // OSX/Linux
        if (function_exists('openssl_random_pseudo_bytes') === true) {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }

        // Fallback (PHP 4.2+)
        mt_srand((double)microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);                  // "-"
        $lbrace = $trim ? "" : chr(123);    // "{"
        $rbrace = $trim ? "" : chr(125);    // "}"
        $guidv4 = $lbrace.
                  substr($charid,  0,  8).$hyphen.
                  substr($charid,  8,  4).$hyphen.
                  substr($charid, 12,  4).$hyphen.
                  substr($charid, 16,  4).$hyphen.
                  substr($charid, 20, 12).
                  $rbrace;
        return $guidv4;
    }

	private function __imageValidation($imgkey){
		$data = array();
		//image validation
		if(!isset($_FILES[$imgkey])){
			$this->status = 102;
			$this->message = 'Image icon file are required';
			$this->__json_out($data);
			die();
		}
		if(empty($_FILES[$imgkey]['tmp_name'])){
			$this->status = 103;
			$this->message = 'Failed upload image icon';
			$this->__json_out($data);
			die();
		}
		if($_FILES[$imgkey]['size']<=0){
			$this->status = 104;
			$this->message = 'Failed upload image icon';
			$this->__json_out($data);
			die();
		}
		if($_FILES[$imgkey]['size']>100000){
			$this->status = 105;
			$this->message = 'Image icon file size too big, please try another image';
			$this->__json_out($data);
			die();
		}
		if(mime_content_type($_FILES[$imgkey]['tmp_name']) == "image/webp"){
			$this->status = 106;
			$this->message = 'WebP file format currently unsupported by this system, please try another image';
			$this->__json_out($data);
			die();
		}
		if(mime_content_type($_FILES[$imgkey]['tmp_name']) == "image/webp"){
			$this->status = 106;
			$this->message = 'WebP file format currently unsupported by this system, please try another image';
			$this->__json_out($data);
			die();
		}
		$ext = strtolower(pathinfo($_FILES[$imgkey]['name'], PATHINFO_EXTENSION));
		if (!in_array($ext, array("jpg", "png","jpeg"))) {
			$this->status = 107;
			$this->message = 'Invalid file extension, only supported PNG or JPG extension';
			$this->__json_out($data);
			die();
		}
	}

	private function __slugify($text){
	  // replace non letter or digits by -
	  $text = preg_replace('~[^\pL\d]+~u', '-', $text);
	  // transliterate
	  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
	  // remove unwanted characters
	  $text = preg_replace('~[^-\w]+~', '', $text);
	  // trim
	  $text = trim($text, '-');
	  // remove duplicate -
	  $text = preg_replace('~-+~', '-', $text);
	  // lowercase
	  $text = strtolower($text);
	  if (empty($text)) {
	    return 'n-a';
	  }
	  return $text;
	}

	// by Muhammad Sofi 28 December 2021 18:14 | testing remove json_decode
    // private function __convertToEmoji($text){
    //     $value = ($text);
    //     if ($value) {
    //         return ($text);
    //     } else {
    //         return ('"'.$text.'"');
    //     }
    // }

	// by Muhammad Sofi 28 December 2021 20:00 | read text with emoji
	private function __convertToEmoji($text){
		$value = $text;
		$readTextWithEmoji = preg_replace("/\\\\u([0-9A-F]{2,5})/i", "&#x$1;", $value);
		return $readTextWithEmoji;
	}

	public function index(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$draw = $this->input->post("draw");
		$sval = $this->input->post("search");
		$sSearch = $this->input->post("sSearch");
		$sEcho = $this->input->post("sEcho");
		$page = $this->input->post("iDisplayStart");
		$pagesize = $this->input->post("iDisplayLength");

		$iSortCol_0 = $this->input->post("iSortCol_0");
		$sSortDir_0 = $this->input->post("sSortDir_0");

        $fromDate = $this->input->post("from_date");
        $toDate = $this->input->post("to_date");
        $userId = $this->input->post("user_id");
        $statusFilter = $this->input->post("status");

		$sortCol = "date";
		$sortDir = strtoupper($sSortDir_0);
		if(empty($sortDir)) $sortDir = "DESC";
		if(strtolower($sortDir) != "desc"){
			$sortDir = "ASC";
		}

		// START by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date
        //validating date interval
        if (strlen($fromDate)==10) {
            $fromDate = date("Y-m-d", strtotime($fromDate));
        } else {
            $fromDate = "";
        }
        if (strlen($toDate)==10) {
            $toDate = date("Y-m-d", strtotime($toDate));
        } else {
            $toDate = "";
        }
        // END by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date

		switch($iSortCol_0){
			case 0:
				$sortCol = "cdate";
				break;
			case 1:
				$sortCol = "image_icon";
				break;
			case 2:
				$sortCol = "title";
				break;
			case 3:
				$sortCol = "prioritas";
				break;
			case 4:
				$sortCol = "is_active";
				break;
			default:
				$sortCol = "cdate";
		}

		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;

		$keyword = $sSearch;

		$this->status = 200;
		$this->message = 'Success';
		$dcount = $this->list_model->countAll($nation_code, $keyword, $fromDate, $toDate, $userId, $statusFilter);
		$ddata = $this->list_model->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $fromDate, $toDate, $userId, $statusFilter);
		
		foreach($ddata as &$gd){

			if(isset($gd->image_icon)){
				if(strlen($gd->image_icon)<=4) $gd->image_icon = 'media/icon/default-icon.png';
				if($gd->image_icon == 'default.png' || $gd->image_icon== 'default.jpg') $gd->image_icon = 'media/icon/default-icon.png';
				$gd->image_icon = base_url($gd->image_icon);
				$gd->image_icon = '<img src="'.$gd->image_icon.'" class="img-responsive" style="width: 64px;" />';
			}

			if (isset($gd->url_thumb)) {

				//get image thumbnail
				$image_data = $this->list_model->getImageThumbnail($nation_code, $gd->id);

				if(isset($image_data->image_thumb)) {
					$img_thumbnail = $image_data->image_thumb;
				} else {
					$img_thumbnail = 'media/produk/default.png';
				}

				$gd->url_thumb = '<img src="'.$this->cdn_url($img_thumbnail).'" class="img-responsive" style="max-width: 128px;"  onerror="this.onerror=null;this.src=\''.$this->cdn_url('media/produk/default.png').'\';"/>';
            }

			// by Muhammad Sofi - 9 December 2021 | showing emoji on title
			if(isset($gd->title)){
				// if (strlen($gd->title)>255) {
				// 	$gd->title = substr($this->__convertToEmoji($gd->title), 0, 255)." <strong>(More...)<strong>";
				// } else {
				// 	$gd->title = $this->__convertToEmoji($gd->title);
				// }

				$thestring = $gd->title;
				$getlength = strlen($thestring);
				$maxLength = 150;

				if ($getlength > $maxLength) {
					$trimmed = substr($thestring, 0, strrpos($thestring, ' ', $maxLength-$getlength));
					$gd->title = $this->__convertToEmoji($trimmed). " <strong>(More..)</strong>";
				} else {
					$gd->title = $this->__convertToEmoji($thestring);
				}

				$gd->title .= '<br /> <strong>Likes : '.$gd->total_likes.'</strong>';
			}

			if(isset($gd->description)){
				// if (mb_strlen($gd->description)>255) {
				// 	$gd->description = substr($this->__convertToEmoji($gd->description), 0, 255)." <strong>(More...)<strong>";
				// } else {
				// 	$gd->description = $this->__convertToEmoji($gd->description);
				// }

				$thestring = $gd->description;
				$getlength = strlen($thestring);
				$maxLength = 255;

				if ($getlength > $maxLength) {
					$trimmed = substr($thestring, 0, strrpos($thestring, ' ', $maxLength-$getlength));
					$gd->description = $this->__convertToEmoji($trimmed). " <strong>(More..)</strong>";
				} else {
					$gd->description = $this->__convertToEmoji($thestring);
				}
			}

			// by Muhammad Sofi 31 January 2022 11:03 | read special chars
			if(isset($gd->category_name)) {
				$gd->category_name = htmlspecialchars_decode($gd->category_name);
			}

            if (isset($gd->cdate)) {
				$gd->cdate = date("d M Y H:i:s", strtotime($gd->cdate));
            }

            if (isset($gd->user)) {
                $user = $gd->user;
				$email = $gd->email_creator;
                $gd->user = '<span style="font-size: 1.2em; font-weight: bolder;">'.$user.'</span><br />Email: '.$email.'</br>';
				// by Muhammad Sofi - 3 November 2021 10:00
				// remark code
				// if (isset($gd->address2)) $gd->user .= '<span>Address: '.$gd->address2.'</span><br />';
                if (isset($gd->address2)) {
					// $fulladdress = $gd->address2.', '.$gd->kelurahan.', '.$gd->kecamatan.', '.$gd->kabkota.', '.$gd->provinsi;
					// $limitaddress = substr($fulladdress, 0, 40)."<strong>......<strong>";
					$gd->user .= '<span>Address: '.$gd->address2.'</span>';
				} 
            }

            if (isset($gd->is_active)) {
                $status = "";
				if(!empty($gd->is_active)) $status = '<label class="label label-success">Active</label>';
				else $status = '<label class="label label-default">Inactive</label>';
                $gd->is_active = '<span>'.$status.'</span><br /><div style="margin-bottom: 5px;"></div>';

				if(!empty($gd->is_report)) $status = '<label class="label label-warning">Yes</label>';
				else $status = '<label class="label label-default">No</label>';
				$gd->is_active .= '<span>Reported: '.$status.' </span><br /><div style="margin-bottom: 5px;"></div>';

				if(!empty($gd->is_take_down)) $status = '<label class="label label-danger">Yes</label>';
				else $status = '<label class="label label-default">No</label>';
				$gd->is_active .= '<span>Takedown : '.$status.' </span><br />';
            }
		}
		//sleep(3);
		$another = array();
		$this->__jsonDataTable($ddata,$dcount);
	}

	// not used
	// public function detail($id){
	// 	$id = (int) $id;
	// 	$d = $this->__init();
	// 	$data = array();
	// 	if(!$this->admin_login && empty($id)){
	// 		$this->status = 400;
	// 		$this->message = 'Unauthorized access';
	// 		header("HTTP/1.0 400 Unauthorized");
	// 		$this->__json_out($data);
	// 		die();
	// 	}
	// 	$pengguna = $d['sess']->admin;
	// 	$nation_code = $pengguna->nation_code;

	// 	$this->status = 200;
	// 	$this->message = 'Success';
	// 	$data = $this->list_model->getById($nation_code,$id);
		
	// 	if(isset($data->deskripsi)){
	// 		$data->deskripsi = $this->__convertToEmoji($data->deskripsi);
	// 	}

	// 	if(!isset($data->id)){
	// 		$data = new stdClass();
	// 		$this->status = 441;
	// 		$this->message = 'No Data';
	// 		$this->__json_out($data);
	// 		die();
	// 	}
	// 	$this->__json_out($data);
	// }

	// START by Muhammad Sofi 14 January 2022 18:09 | change query to get reported community post data
	public function reported() { // reported community post
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$draw = $this->input->post("draw");
		$sval = $this->input->post("search");
		$sSearch = $this->input->post("sSearch");
		$sEcho = $this->input->post("sEcho");
		$page = $this->input->post("iDisplayStart");
		$pagesize = $this->input->post("iDisplayLength");

		$iSortCol_0 = $this->input->post("iSortCol_0");
		$sSortDir_0 = $this->input->post("sSortDir_0");

		$fromDate = $this->input->post("from_date");
        $toDate = $this->input->post("to_date");
        $userId = $this->input->post("user_id");
        $statusFilter = $this->input->post("status");

		$sortCol = "date";
		$sortDir = strtoupper($sSortDir_0);
		if(empty($sortDir)) $sortDir = "DESC";
		if(strtolower($sortDir) != "desc"){
			$sortDir = "ASC";
		}

		// START by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date
        //validating date interval
        if (strlen($fromDate)==10) {
            $fromDate = date("Y-m-d", strtotime($fromDate));
        } else {
            $fromDate = "";
        }
        if (strlen($toDate)==10) {
            $toDate = date("Y-m-d", strtotime($toDate));
        } else {
            $toDate = "";
        }
        // END by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date

		switch($iSortCol_0){
			case 0:
				$sortCol = "no";
				break;
			case 1:
				$sortCol = "c_community_id";
				break;
			case 2:
				$sortCol = "b_user_id";
				break;
			case 3:
				$sortCol = "check_takedown";
				break;
			case 4:
				$sortCol = "cdate";
				break;
			case 5:
				$sortCol = "title";
				break;
			case 6:
				$sortCol = "deskripsi";
				break;
			case 7:
				$sortCol = "reported_post_owner";
				break;
			case 8:
				$sortCol = "reporter_user_name";
				break;
			case 9:
				$sortCol = "admin_name";
				break;
			case 10:
				$sortCol = "is_active";
				break;
			case 11:
				$sortCol = "total_reported_post";
				break;
			default:
				$sortCol = "no";
		}

		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;

		$keyword = $sSearch;

		$this->status = 200;
		$this->message = 'Success';
		$dcount = $this->list_model->countReportedPost($nation_code, $keyword,$fromDate, $toDate, $userId, $statusFilter);
		$ddata = $this->list_model->getReportedPost($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $fromDate, $toDate, $userId, $statusFilter);

		foreach($ddata as &$gd){
			// START by Muhammad Sofi 27 December 2021 17:09 | reduce title and description if character is more than 255
			if(isset($gd->title)){


				$thestring = $gd->title;
				$getlength = strlen($thestring);
				$maxLength = 50;

				if ($getlength > $maxLength) {
					$trimmed = substr($thestring, 0, strrpos($thestring, ' ', $maxLength-$getlength));
					$gd->title = $this->__convertToEmoji($trimmed). " <strong>(More..)</strong";
				} else {
					$gd->title = $this->__convertToEmoji($thestring);
				}
			}

			if(isset($gd->deskripsi)){
				$thestring = $gd->deskripsi;
				$getlength = strlen($thestring);
				$maxLength = 180;

				if ($getlength > $maxLength) {
					$trimmed = substr($thestring, 0, strrpos($thestring, ' ', $maxLength-$getlength));
					$gd->deskripsi = $this->__convertToEmoji($trimmed). " <strong>(More..)</strong";
				} else {
					$gd->deskripsi = $this->__convertToEmoji($thestring);
				}
			}
			// END by Muhammad Sofi 27 December 2021 17:09 | reduce title and description if character is more than 255

            // if (isset($gd->cdate)) {
			// 	$gd->cdate = date("d F Y h:i:s", strtotime($gd->cdate));
            // }

			if(isset($gd->check_takedown)) {
				if($gd->check_takedown == 1) {
					$gd->check_takedown = "takedown";
				} else {
					$gd->check_takedown = "reported";
				}
			}

            if (isset($gd->reported_post_owner)) {
                $user = $gd->reported_post_owner;
				$email = $gd->reported_post_owner_email;
                $gd->reported_post_owner = '<span style="font-size: 1.2em; font-weight: bolder;">'.$user.'</span><br />'.$email.'<br />';
                if (isset($gd->reported_post_owner_address)) $gd->reported_post_owner .= '<span><strong>Address</strong>: '.$gd->reported_post_owner_address.'</span><br />';
            }

			if (isset($gd->reporter_user_name)) {
                if (isset($gd->reporter_user_email)) {
                    $email = $gd->reporter_user_email.'<br />';
                }
                if (isset($gd->reporter_user_address)) {
					$address = '<span><strong>Address</strong>: '.$gd->reporter_user_address.'</span><br />';
                }

				if($gd->b_user_id_reporter != 0) {
					$result = '<span style="font-size: 1.2em; font-weight: bolder;">'. $gd->reporter_user_name.'</span><br />'.$email.$address;
				} else {
					if(empty($gd->admin_name)) {
						$result = '<span style="font-size: 1.2em; font-weight: bolder;">Admin</span><br />';
					} else {
						$admin_name = strtoupper(str_replace("_", " ", $gd->admin_name));

						$result = '<span style="font-size: 1.2em; font-weight: bolder;">'.$admin_name.'</span><br />';
					}
				}

                $gd->reporter_user_name = $result;
            }

			if (isset($gd->admin_name)) {

				if(empty($gd->admin_name)) {
					$admin_name = "Admin";
				} else {
					$admin_name = strtoupper(str_replace("_", " ", $gd->admin_name));
				}

				$result = '<span style="font-size: 1.2em; font-weight: bolder;">'.$admin_name.'</span><br />';

                $gd->admin_name = $result;
            }

            if (isset($gd->is_active)) {
                $status = "";
				if(!empty($gd->is_active)) $status = '<label class="label label-success">Active</label>';
				else $status = '<label class="label label-default">Inactive</label>';
                $gd->is_active = '<span>'.$status.'</span><br /><div style="margin-bottom: 5px;"></div>';

				if(!empty($gd->is_report)) $status = '<label class="label label-warning">Yes</label>';
				else $status = '<label class="label label-default">No</label>';
				$gd->is_active .= '<span>Reported: '.$status.' </span><br /><div style="margin-bottom: 5px;"></div>';

				if(!empty($gd->is_take_down)) $status = '<label class="label label-danger">Yes</label>';
				else $status = '<label class="label label-default">No</label>';
				$gd->is_active .= '<span>Takedown : '.$status.' </span><br />';
            }
		}
		//sleep(3);
		$another = array();
		$this->__jsonDataTable($ddata,$dcount);
	}
	
    public function report($report)
    {
        $d = $this->__init();
        $data = array();

		// $id = $this->input->get('id') ? $this->input->get('id') : '';
        $c_community_id = $this->input->get('c_community_id') ? $this->input->get('c_community_id') : '';
		$b_user_id_reported = $this->input->get('b_user_id') ? $this->input->get('b_user_id') : '';
		$admin_name = $this->input->get('admin_name') ? $this->input->get('admin_name') : '';

        // if ($id<=0) {
        //     $this->status = 450;
        //     $this->message = 'Invalid ID';
        //     $this->__json_out($data);
        //     die();
        // }
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

		$community = $this->list_model->getById($nation_code, $c_community_id);
		$pelanggan = $this->bu_model->getById($nation_code, $b_user_id_reported);

		$getUserIdReporter = $this->list_model->getUserIdReporterByCommunityId($nation_code, $c_community_id);

		if($report == "0") {
			$this->status = 200;
			$this->message = 'Success';
			$this->statuspost = 'ignore';
			$du = array();
			$du['is_report'] = "0";
			$du['report_date'] = "NULL";
			$du['is_take_down'] = "0";
			$du['take_down_date'] = "NULL";
			$this->list_model->updateStatusIgnore($nation_code, $c_community_id, $du);
		} else if($report == "1") {
			$this->status = 200;
			$this->message = 'Success';
			$this->statuspost = 'takedown';
			$du = array();
			$du['is_active'] = "0";
			$du['is_published'] = "0";
			$du['is_take_down'] = "1";
			$du['take_down_date'] = "NOW()";
			$this->list_model->updateStatusTakedown($nation_code, $c_community_id, $du);
			$di = array();
			$di['is_active'] = "0";
			$di['edate'] = "NOW()"; // takedown date
			if($getUserIdReporter->b_user_id == 0 || $getUserIdReporter->b_user_id == "0" || empty($getUserIdReporter->b_user_id)) {
				// $di['b_user_id'] = 0;
				$di['admin_name'] = $admin_name;
			} else {
				$di['b_user_id'] = $getUserIdReporter->b_user_id;
				$di['admin_name'] = $admin_name;

			}
			$this->list_model->updateStatusTakedownReport($nation_code, $c_community_id, $di);

			//start transaction
			$this->list_model->trans_start();

			$attachments = $this->list_model->getByCommunityId($nation_code, $c_community_id);

			$du = array();
			$du['is_active'] = 0;
			$res2 = $this->list_model->updateByCommunityId($nation_code, $c_community_id, $du);
			if ($res2) {
				$this->list_model->trans_commit();
				$this->status = 200;
				$this->message = 'Success';

			/**   OLD RULE    */

				// //delete attachment file
				// if (count($attachments)) {
				// 	$i = 0;
				// 	foreach ($attachments as $atc) {
				// 		$i++;
				// 		if($atc->jenis == 'image' || $atc->jenis == 'video'){
				// 			if ($atc->url != $this->media_community_video."default.png") {
				// 				$fileloc = SENEROOT.$atc->url;
				// 				if (file_exists($fileloc)) {
				// 					unlink($fileloc);
				// 				}
				// 			}
			
				// 			if ($atc->url_thumb != $this->media_community_video."default.png") {
				// 				$fileloc = SENEROOT.$atc->url_thumb;
				// 				if (file_exists($fileloc)) {
				// 					unlink($fileloc);
				// 				}
				// 			}

				// 			if($atc->jenis == "video" && $atc->convert_status != "uploading"){

				// 				// if(count($attachments) != 0 && count($attachments) < 2) {
				// 				if($i == 1) {
				// 					//get point
				// 					$pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EP");
				// 					if (!isset($pointGet->remark)) {
				// 						$pointGet = new stdClass();
				// 						$pointGet->remark = 10;
				// 					}

				// 					$leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $b_user_id_reported, $community->kelurahan, $community->kecamatan, $community->kabkota, $community->provinsi);
				// 					$di = array();
				// 					$di['nation_code'] = $nation_code;
				// 					$di['id'] = $leaderBoardHistoryId;
				// 					$di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
				// 					$di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
				// 					$di['b_user_alamat_location_kabkota'] = $community->kabkota;
				// 					$di['b_user_alamat_location_provinsi'] = $community->provinsi;
				// 					$di['b_user_id'] = $b_user_id_reported;
				// 					$di['plusorminus'] = "-";
				// 					$di['point'] = $pointGet->remark;
				// 					$di['custom_id'] = $c_community_id;
				// 					$di['custom_type'] = 'community';
				// 					$di['custom_type_sub'] = 'takedown video';
				// 					$di['custom_text'] = 'Admin has '.$di['custom_type_sub'].' '.$pelanggan->fnama.' '.$di['custom_type'].' post and lose '.$di['point'].' point(s)';
				// 					$this->glphm->set($di);
				// 					$this->list_model->trans_commit();
				// 				} 
				// 				// break;
				// 			}
				// 		}

				// 	}
				// 	unset($atc);
				// }

				// //get total community post
				// // $totalPostNow = $this->list_model->countAllByUserId($nation_code, $b_user_id_reported);

				// // if($totalPostNow == 0){

				// // 	//get point
				// // 	$pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EF");
				// // 	if (!isset($pointGet->remark)) {
				// // 	  $pointGet = new stdClass();
				// // 	  $pointGet->remark = 100;
				// // 	}
			
				// // 	$leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $b_user_id_reported, $community->kelurahan, $community->kecamatan, $community->kabkota, $community->provinsi);
				// // 	$di = array();
				// // 	$di['nation_code'] = $nation_code;
				// // 	$di['id'] = $leaderBoardHistoryId;
				// // 	$di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
				// // 	$di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
				// // 	$di['b_user_alamat_location_kabkota'] = $community->kabkota;
				// // 	$di['b_user_alamat_location_provinsi'] = $community->provinsi;
				// // 	$di['b_user_id'] = $b_user_id_reported;
				// // 	$di['plusorminus'] = "-";
				// // 	$di['point'] = $pointGet->remark;
				// // 	$di['custom_id'] = $c_community_id;
				// // 	$di['custom_type'] = 'community';
				// // 	$di['custom_type_sub'] = 'takedown post(first time)';
				// // 	$di['custom_text'] = 'Admin has '.$di['custom_type_sub'].' '.$pelanggan->fnama.' '.$di['custom_type'].' post and lose '.$di['point'].' point(s)';
				// // 	$this->glphm->set($di);
				// // 	$this->list_model->trans_commit();
				// // } else {
				// 	$pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EG");
				// 	if (!isset($pointGet->remark)) {
				// 		$pointGet = new stdClass();
				// 		$pointGet->remark = 10;
				// 	}

				// 	$leaderBoardHistoryId = $this->glphm->getLastIdLeaderboardPointHistory($nation_code, $b_user_id_reported, $community->kelurahan, $community->kecamatan, $community->kabkota, $community->provinsi);
				// 	$di = array();
				// 	$di['nation_code'] = $nation_code;
				// 	$di['id'] = $leaderBoardHistoryId;
				// 	$di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
				// 	$di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
				// 	$di['b_user_alamat_location_kabkota'] = $community->kabkota;
				// 	$di['b_user_alamat_location_provinsi'] = $community->provinsi;
				// 	$di['b_user_id'] = $b_user_id_reported;
				// 	$di['plusorminus'] = "-";
				// 	$di['point'] = $pointGet->remark;
				// 	$di['custom_id'] = $c_community_id;
				// 	$di['custom_type'] = 'community';
				// 	$di['custom_type_sub'] = 'takedown post';
				// 	$di['custom_text'] = 'Admin has '.$di['custom_type_sub'].' '.$pelanggan->fnama.' '.$di['custom_type'].' post and lose '.$di['point'].' point(s)';
				// 	$this->glphm->set($di);
				// 	$this->list_model->trans_commit();
				// // }
			
			/**   OLD RULE    */

			/**   NEW RULE    */
		
				// delete attachment
				if (count($attachments)) {
					$i = 0;
					foreach ($attachments as $atc) {
						$i++;
						if($atc->jenis == 'image' || $atc->jenis == 'video'){
							if ($atc->url != $this->media_community_video."default.png") {
								$fileloc = SENEROOT.$atc->url;
								if (file_exists($fileloc)) {
									unlink($fileloc);
								}
							}

							if ($atc->url_thumb != $this->media_community_video."default.png") {
								$fileloc = SENEROOT.$atc->url_thumb;
								if (file_exists($fileloc)) {
									unlink($fileloc);
								}
							}
						}
					}
					unset($atc);
				}

				// deduct point
				$checkdatafromleaderboard = $this->glphm->getLeaderboardDataByCustomId($nation_code, $c_community_id);

				foreach ($checkdatafromleaderboard as $checkdata) { 
					if($checkdata->custom_type_sub === "post") {

						// deduct point
						$pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EG");
						if (!isset($pointGet->remark)) {
							$pointGet = new stdClass();
							$pointGet->remark = 1;
						}

						// $leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $b_user_id_reported, $community->kelurahan, $community->kecamatan, $community->kabkota, $community->provinsi);
						$di = array();
						$di['nation_code'] = $nation_code;
						// $di['id'] = $leaderBoardHistoryId;
						$di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
						$di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
						$di['b_user_alamat_location_kabkota'] = $community->kabkota;
						$di['b_user_alamat_location_provinsi'] = $community->provinsi;
						$di['b_user_id'] = $b_user_id_reported;
						$di['plusorminus'] = "-";
						$di['point'] = $pointGet->remark;
						$di['custom_id'] = $c_community_id;
						$di['custom_type'] = 'community';
						$di['custom_type_sub'] = 'takedown post';
						$di['custom_text'] = 'Admin has '.$di['custom_type_sub'].' '.$pelanggan->fnama.' '.$di['custom_type'].' post and lose '.$di['point'].' point(s)';
						$endDoWhile = 0;
						do{
							$leaderBoardHistoryId = $this->GUIDv4();
							$checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
							if($checkId == 0){
							$endDoWhile = 1;
							}
						}while($endDoWhile == 0);
						$di['id'] = $leaderBoardHistoryId;
						$this->glphm->set($di);
						$this->list_model->trans_commit();

					} else if($checkdata->custom_type_sub === "upload image") {

						// deduct point
						$pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E13");
						if (!isset($pointGet->remark)) {
							$pointGet = new stdClass();
							$pointGet->remark = 14;
						}

						// $leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $b_user_id_reported, $community->kelurahan, $community->kecamatan, $community->kabkota, $community->provinsi);
						$di = array();
						$di['nation_code'] = $nation_code;
						// $di['id'] = $leaderBoardHistoryId;
						$di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
						$di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
						$di['b_user_alamat_location_kabkota'] = $community->kabkota;
						$di['b_user_alamat_location_provinsi'] = $community->provinsi;
						$di['b_user_id'] = $b_user_id_reported;
						$di['plusorminus'] = "-";
						$di['point'] = $pointGet->remark;
						$di['custom_id'] = $c_community_id;
						$di['custom_type'] = 'community';
						$di['custom_type_sub'] = 'takedown image';
						$di['custom_text'] = 'Admin has '.$di['custom_type_sub'].' '.$pelanggan->fnama.' '.$di['custom_type'].' post and lose '.$di['point'].' point(s)';
						$endDoWhile = 0;
						do{
							$leaderBoardHistoryId = $this->GUIDv4();
							$checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
							if($checkId == 0){
							$endDoWhile = 1;
							}
						}while($endDoWhile == 0);
						$di['id'] = $leaderBoardHistoryId;
						$this->glphm->set($di);
						$this->list_model->trans_commit();

					} else if($checkdata->custom_type_sub === "upload video") { 

						// deduct point
						$pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EP");
						if (!isset($pointGet->remark)) {
							$pointGet = new stdClass();
							$pointGet->remark = 29;
						}

						// $leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $b_user_id_reported, $community->kelurahan, $community->kecamatan, $community->kabkota, $community->provinsi);
						$di = array();
						$di['nation_code'] = $nation_code;
						// $di['id'] = $leaderBoardHistoryId;
						$di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
						$di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
						$di['b_user_alamat_location_kabkota'] = $community->kabkota;
						$di['b_user_alamat_location_provinsi'] = $community->provinsi;
						$di['b_user_id'] = $b_user_id_reported;
						$di['plusorminus'] = "-";
						$di['point'] = $pointGet->remark;
						$di['custom_id'] = $c_community_id;
						$di['custom_type'] = 'community';
						$di['custom_type_sub'] = 'takedown video';
						$di['custom_text'] = 'Admin has '.$di['custom_type_sub'].' '.$pelanggan->fnama.' '.$di['custom_type'].' post and lose '.$di['point'].' point(s)';
						$endDoWhile = 0;
						do{
							$leaderBoardHistoryId = $this->GUIDv4();
							$checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
							if($checkId == 0){
							$endDoWhile = 1;
							}
						}while($endDoWhile == 0);
						$di['id'] = $leaderBoardHistoryId;
						$this->glphm->set($di);
						$this->list_model->trans_commit();
					}
				}

			/**   NEW RULE    */

				// by muhammad sofi 24 January 2023 | send push notif to creator
				// select fcm token
				$user = $this->bu_model->getById($nation_code, $b_user_id_reported);

				$dpe = array();
				$dpe['nation_code'] = $nation_code;
				$dpe['b_user_id'] = $b_user_id_reported;
				$dpe['id'] = $this->dpem->getLastId($nation_code, $b_user_id_reported);
				$dpe['type'] = "community";
				if($user->language_id == 2) {
					$dpe['judul'] = "Perhatian";
					$dpe['teks'] =  "Maaf, Postinganmu (".html_entity_decode($community->title,ENT_QUOTES).") telah dihapus oleh Sellon. Poin SPT dari postinganmu akan dibatalkan. Bijaklah dalam share.";
				} else {
					$dpe['judul'] = "Attention";
					$dpe['teks'] =  "Sorry, your post (".html_entity_decode($community->title,ENT_QUOTES).") is deleted by Sellon. The SPT point from your post will be cancelled. Be wise in sharing.";
				}

				$dpe['gambar'] = 'media/pemberitahuan/community.png';
				$dpe['cdate'] = "NOW()";
				$extras = new stdClass();
				$extras->id = $c_community_id;
				$extras->title = $community->title;
				if($user->language_id == 2) { 
					$extras->judul = "Perhatian";
					$extras->teks =  "Maaf, Postinganmu (".html_entity_decode($community->title,ENT_QUOTES).") telah dihapus oleh Sellon. Poin SPT dari postinganmu akan dibatalkan. Bijaklah dalam share.";
				} else {
					$extras->judul = "Attention";
					$extras->teks =  "Sorry, your post (".html_entity_decode($community->title,ENT_QUOTES).") is deleted by Sellon. The SPT point from your post will be cancelled. Be wise in sharing.";
				}

				$dpe['extras'] = json_encode($extras);
				$this->dpem->set($dpe);

				$classified = 'setting_notification_user';
				$code = 'U6';

				$receiverSettingNotif = $this->busm->getValue($nation_code, $b_user_id_reported, $classified, $code);

				if (!isset($receiverSettingNotif->setting_value)) {
					$receiverSettingNotif->setting_value = 0;
				}

				if ($receiverSettingNotif->setting_value == 1 && $user->is_active == 1) {

				if($user->device == "ios"){
					//push notif to ios
					$device = "ios"; //jenis device
				}else{
					//push notif to android
					$device = "android"; //jenis device
				}

				$tokens = $user->fcm_token; //device token
				if(!is_array($tokens)) $tokens = array($tokens);
					if($user->language_id == 2){
						$title = "Perhatian";
						$message = "Maaf, Postinganmu (".html_entity_decode($community->title,ENT_QUOTES).") telah dihapus oleh Sellon. Poin SPT dari postinganmu akan dibatalkan. Bijaklah dalam share.";
					} else {
						$title = "Attention";
						$message = "Sorry, your post (".html_entity_decode($community->title,ENT_QUOTES).") is deleted by Sellon. The SPT point from your post will be cancelled. Be wise in sharing.";
					}
					
					$image = 'media/pemberitahuan/promotion.png';
					$type = 'community';
					$payload = new stdClass();
					$payload->id = $c_community_id;
					$payload->title = html_entity_decode($community->title,ENT_QUOTES);
					// $payload->harga_jual = $community->harga_jual;
					// $payload->foto = base_url().$community->thumb;
					if($user->language_id == 2) {
						$payload->judul = "Perhatian";
						//by Donny Dennison
						//dicomment untuk handle message too big, response dari fcm
						// $payload->teks = strip_tags(html_entity_decode($di['teks']));
						// $payload->teks = "You get a reply from your neighbors (".$tempTitle->{'title'}.")";
						$payload->teks = "Maaf, Postinganmu (".html_entity_decode($community->title,ENT_QUOTES).") telah dihapus oleh Sellon. Poin SPT dari postinganmu akan dibatalkan. Bijaklah dalam share.";
					} else {
						$payload->judul = "Attention";
						$payload->teks = "Sorry, your post (".html_entity_decode($community->title,ENT_QUOTES).") is deleted by Sellon. The SPT point from your post will be cancelled. Be wise in sharing.";
					}

					$this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);

				}
			}
			$this->list_model->trans_end();
		} else{}
        // if ($res) {
        //     $this->status = 200;
        //     $this->message = 'Success';
        // } else {
        //     $this->status = 920;
        //     $this->message = 'Failed change data to database';
        // }

        $this->__json_out($data);
    }
	// END by Muhammad Sofi 14 January 2022 18:09 | change query to get reported community post data

	// by Muhammad Sofi 20 December 2022 | add feature report from admin
	public function report_from_admin($c_community_id) {
		$d = $this->__init();
        $data = array();

		$pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

		$owner_post = $this->list_model->getById($nation_code, $c_community_id);

		//start transaction and lock table
		$this->list_report_model->trans_start();

		//initial insert with latest ID
		$di = array();
		$di['nation_code'] = $nation_code;
		$di['c_community_id'] = $c_community_id;
		$di['b_user_id'] = 0;
		$di['cdate'] = 'NOW()';
		$res = $this->list_report_model->set($di);

		if (!$res) {
			$this->list_report_model->trans_rollback();
			$this->list_report_model->trans_end();
			$this->status = 1108;
			$this->message = "Error while report community, please try again later";
			$this->__json_out($data);
			die();
		} else {
			$this->list_report_model->trans_commit();
			$this->status = 200;
			$this->message = 'Success';
		}

		//end transaction
		$this->list_report_model->trans_end();

		//update is_report and report_date
		$di = array();
		$di['report_date'] = 'NOW()';
		$di['is_report'] = 1;
		$this->list_model->update($nation_code, $c_community_id, $di);

		// if($res) {
		// 	$this->status = 200;
		// 	$this->message = 'Success';
		// }else {
		// 	$this->status = 920;
		// 	$this->message = 'Failed to report';
		// }

		$this->__json_out($data);
	}

	// by Muhammad Sofi 3 January 2023 | add feature delete from admin
	public function delete_from_admin() {
		$d = $this->__init();
		$data = array();

		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$c_community_id = $this->input->get('community_id') ? $this->input->get('community_id') : '';
		$admin_name = $this->input->get('admin_name') ? $this->input->get('admin_name') : '';

		if(empty($c_community_id)) {
			$this->status = 400;
			$this->message = 'No Community Id';
			$this->__json_out($data);
			die();
		}

		$community = $this->list_model->getById($nation_code, $c_community_id);
		$b_user_id_reported = $community->b_user_id;
		$pelanggan = $this->bu_model->getById($nation_code, $b_user_id_reported);

		//start transaction and lock table
		$this->list_model->trans_start();

		//initial insert with latest ID
		$du = array();
		$du['is_active'] = "0";
		$du['is_published'] = "0";
		$du['report_date'] = 'NOW()';
		$du['is_report'] = 1;
		$du['is_take_down'] = "1";
		$du['take_down_date'] = "NOW()";
		$res = $this->list_model->update($nation_code, $c_community_id, $du);

		if (!$res) {
			$this->list_model->trans_rollback();
			$this->list_model->trans_end();
			$this->status = 1108;
			$this->message = "Error while delete community, please try again later";
			$this->__json_out($data);
			die();
		} else {
			$this->list_model->trans_commit();
			$this->status = 200;
			$this->message = 'Success';
		}

		//insert to c_community_report
		$di = array();
		$di['nation_code'] = $nation_code;
		$di['c_community_id'] = $c_community_id;
		$di['b_user_id'] = 0;
		$di['cdate'] = 'NOW()'; // report date
		$di['edate'] = "NOW()"; // takedown date
		$di['is_active'] = "0";
		$di['admin_name'] = $admin_name;
		$this->list_report_model->set($di);

		$attachments = $this->list_model->getByCommunityId($nation_code, $c_community_id);

		$du = array();
		$du['is_active'] = 1; // set is_active  = 0 means image/video already deleted from server
		$res2 = $this->list_model->updateByCommunityId($nation_code, $c_community_id, $du);
		if ($res2) {
			$this->list_model->trans_commit();
			$this->status = 200;
			$this->message = 'Success';

		/**   OLD RULE    */

			// //delete attachment file
			// if (count($attachments)) {
			// 	$i = 0;
			// 	foreach ($attachments as $atc) {
			// 		$i++;
			// 		if($atc->jenis == 'image' || $atc->jenis == 'video'){
			// 			// don't delete image/video
			// 			// if ($atc->url != $this->media_community_video."default.png") {
			// 			// 	$fileloc = SENEROOT.$atc->url;
			// 			// 	if (file_exists($fileloc)) {
			// 			// 		unlink($fileloc);
			// 			// 	}
			// 			// }
		
			// 			// if ($atc->url_thumb != $this->media_community_video."default.png") {
			// 			// 	$fileloc = SENEROOT.$atc->url_thumb;
			// 			// 	if (file_exists($fileloc)) {
			// 			// 		unlink($fileloc);
			// 			// 	}
			// 			// }

			// 			if($atc->jenis == "video" && $atc->convert_status != "uploading"){

			// 				// if(count($attachments) != 0 && count($attachments) < 2) {
			// 				if($i == 1) {
			// 					//get point
			// 					$pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EP");
			// 					if (!isset($pointGet->remark)) {
			// 						$pointGet = new stdClass();
			// 						$pointGet->remark = 10;
			// 					}

			// 					$leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $b_user_id_reported, $community->kelurahan, $community->kecamatan, $community->kabkota, $community->provinsi);
			// 					$di = array();
			// 					$di['nation_code'] = $nation_code;
			// 					$di['id'] = $leaderBoardHistoryId;
			// 					$di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
			// 					$di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
			// 					$di['b_user_alamat_location_kabkota'] = $community->kabkota;
			// 					$di['b_user_alamat_location_provinsi'] = $community->provinsi;
			// 					$di['b_user_id'] = $b_user_id_reported;
			// 					$di['plusorminus'] = "-";
			// 					$di['point'] = $pointGet->remark;
			// 					$di['custom_id'] = $c_community_id;
			// 					$di['custom_type'] = 'community';
			// 					$di['custom_type_sub'] = 'takedown video';
			// 					$di['custom_text'] = 'Admin has '.$di['custom_type_sub'].' '.$pelanggan->fnama.' '.$di['custom_type'].' post and lose '.$di['point'].' point(s)';
			// 					$this->glphm->set($di);
			// 					$this->list_model->trans_commit();
			// 				} 
			// 				// break;
			// 			}
			// 		}

			// 	}
			// 	unset($atc);
			// }

			// //get total community post
			// // $totalPostNow = $this->list_model->countAllByUserId($nation_code, $b_user_id_reported);

			// // if($totalPostNow == 0){

			// // 	//get point
			// // 	$pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EF");
			// // 	if (!isset($pointGet->remark)) {
			// // 	  $pointGet = new stdClass();
			// // 	  $pointGet->remark = 100;
			// // 	}
		
			// // 	$leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $b_user_id_reported, $community->kelurahan, $community->kecamatan, $community->kabkota, $community->provinsi);
			// // 	$di = array();
			// // 	$di['nation_code'] = $nation_code;
			// // 	$di['id'] = $leaderBoardHistoryId;
			// // 	$di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
			// // 	$di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
			// // 	$di['b_user_alamat_location_kabkota'] = $community->kabkota;
			// // 	$di['b_user_alamat_location_provinsi'] = $community->provinsi;
			// // 	$di['b_user_id'] = $b_user_id_reported;
			// // 	$di['plusorminus'] = "-";
			// // 	$di['point'] = $pointGet->remark;
			// // 	$di['custom_id'] = $c_community_id;
			// // 	$di['custom_type'] = 'community';
			// // 	$di['custom_type_sub'] = 'takedown post(first time)';
			// // 	$di['custom_text'] = 'Admin has '.$di['custom_type_sub'].' '.$pelanggan->fnama.' '.$di['custom_type'].' post and lose '.$di['point'].' point(s)';
			// // 	$this->glphm->set($di);
			// // 	$this->list_model->trans_commit();
			// // } else {
			// 	$pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EG");
			// 	if (!isset($pointGet->remark)) {
			// 		$pointGet = new stdClass();
			// 		$pointGet->remark = 10;
			// 	}

			// 	$leaderBoardHistoryId = $this->glphm->getLastIdLeaderboardPointHistory($nation_code, $b_user_id_reported, $community->kelurahan, $community->kecamatan, $community->kabkota, $community->provinsi);
			// 	$di = array();
			// 	$di['nation_code'] = $nation_code;
			// 	$di['id'] = $leaderBoardHistoryId;
			// 	$di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
			// 	$di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
			// 	$di['b_user_alamat_location_kabkota'] = $community->kabkota;
			// 	$di['b_user_alamat_location_provinsi'] = $community->provinsi;
			// 	$di['b_user_id'] = $b_user_id_reported;
			// 	$di['plusorminus'] = "-";
			// 	$di['point'] = $pointGet->remark;
			// 	$di['custom_id'] = $c_community_id;
			// 	$di['custom_type'] = 'community';
			// 	$di['custom_type_sub'] = 'takedown post';
			// 	$di['custom_text'] = 'Admin has '.$di['custom_type_sub'].' '.$pelanggan->fnama.' '.$di['custom_type'].' post and lose '.$di['point'].' point(s)';
			// 	$this->glphm->set($di);
			// 	$this->list_model->trans_commit();
			// // }

		/**   OLD RULE    */
		
		/**   NEW RULE    */
		
			// delete attachment
			if (count($attachments)) {
				$i = 0;
				foreach ($attachments as $atc) {
					$i++;
					if($atc->jenis == 'image' || $atc->jenis == 'video'){
						if ($atc->url != $this->media_community_video."default.png") {
							$fileloc = SENEROOT.$atc->url;
							if (file_exists($fileloc)) {
								unlink($fileloc);
							}
						}

						if ($atc->url_thumb != $this->media_community_video."default.png") {
							$fileloc = SENEROOT.$atc->url_thumb;
							if (file_exists($fileloc)) {
								unlink($fileloc);
							}
						}
					}
				}
				unset($atc);
			}

			// deduct point
			$checkdatafromleaderboard = $this->glphm->getLeaderboardDataByCustomId($nation_code, $c_community_id);

			foreach ($checkdatafromleaderboard as $checkdata) { 
				if($checkdata->custom_type_sub === "post") {

					// deduct point
					$pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EG");
					if (!isset($pointGet->remark)) {
						$pointGet = new stdClass();
						$pointGet->remark = 1;
					}

					// $leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $b_user_id_reported, $community->kelurahan, $community->kecamatan, $community->kabkota, $community->provinsi);
					$di = array();
					$di['nation_code'] = $nation_code;
					// $di['id'] = $leaderBoardHistoryId;
					$di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
					$di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
					$di['b_user_alamat_location_kabkota'] = $community->kabkota;
					$di['b_user_alamat_location_provinsi'] = $community->provinsi;
					$di['b_user_id'] = $b_user_id_reported;
					$di['plusorminus'] = "-";
					$di['point'] = $pointGet->remark;
					$di['custom_id'] = $c_community_id;
					$di['custom_type'] = 'community';
					$di['custom_type_sub'] = 'takedown post';
					$di['custom_text'] = 'Admin has '.$di['custom_type_sub'].' '.$pelanggan->fnama.' '.$di['custom_type'].' post and lose '.$di['point'].' point(s)';
					$endDoWhile = 0;
					do{
						$leaderBoardHistoryId = $this->GUIDv4();
						$checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
						if($checkId == 0){
						$endDoWhile = 1;
						}
					}while($endDoWhile == 0);
					$di['id'] = $leaderBoardHistoryId;
					$this->glphm->set($di);
					$this->list_model->trans_commit();

				} else if($checkdata->custom_type_sub === "upload image") {

					// deduct point
					$pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E13");
					if (!isset($pointGet->remark)) {
						$pointGet = new stdClass();
						$pointGet->remark = 14;
					}

					// $leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $b_user_id_reported, $community->kelurahan, $community->kecamatan, $community->kabkota, $community->provinsi);
					$di = array();
					$di['nation_code'] = $nation_code;
					// $di['id'] = $leaderBoardHistoryId;
					$di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
					$di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
					$di['b_user_alamat_location_kabkota'] = $community->kabkota;
					$di['b_user_alamat_location_provinsi'] = $community->provinsi;
					$di['b_user_id'] = $b_user_id_reported;
					$di['plusorminus'] = "-";
					$di['point'] = $pointGet->remark;
					$di['custom_id'] = $c_community_id;
					$di['custom_type'] = 'community';
					$di['custom_type_sub'] = 'takedown image';
					$di['custom_text'] = 'Admin has '.$di['custom_type_sub'].' '.$pelanggan->fnama.' '.$di['custom_type'].' post and lose '.$di['point'].' point(s)';
					$endDoWhile = 0;
					do{
						$leaderBoardHistoryId = $this->GUIDv4();
						$checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
						if($checkId == 0){
						$endDoWhile = 1;
						}
					}while($endDoWhile == 0);
					$di['id'] = $leaderBoardHistoryId;
					$this->glphm->set($di);
					$this->list_model->trans_commit();

				} else if($checkdata->custom_type_sub === "upload video") { 

					// deduct point
					$pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EP");
					if (!isset($pointGet->remark)) {
						$pointGet = new stdClass();
						$pointGet->remark = 29;
					}

					// $leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $b_user_id_reported, $community->kelurahan, $community->kecamatan, $community->kabkota, $community->provinsi);
					$di = array();
					$di['nation_code'] = $nation_code;
					// $di['id'] = $leaderBoardHistoryId;
					$di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
					$di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
					$di['b_user_alamat_location_kabkota'] = $community->kabkota;
					$di['b_user_alamat_location_provinsi'] = $community->provinsi;
					$di['b_user_id'] = $b_user_id_reported;
					$di['plusorminus'] = "-";
					$di['point'] = $pointGet->remark;
					$di['custom_id'] = $c_community_id;
					$di['custom_type'] = 'community';
					$di['custom_type_sub'] = 'takedown video';
					$di['custom_text'] = 'Admin has '.$di['custom_type_sub'].' '.$pelanggan->fnama.' '.$di['custom_type'].' post and lose '.$di['point'].' point(s)';
					$endDoWhile = 0;
					do{
						$leaderBoardHistoryId = $this->GUIDv4();
						$checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
						if($checkId == 0){
						$endDoWhile = 1;
						}
					}while($endDoWhile == 0);
					$di['id'] = $leaderBoardHistoryId;
					$this->glphm->set($di);
					$this->list_model->trans_commit();
				} else if($checkdata->custom_type_sub === "post(double point)") {

					// deduct point
					$pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E14");
					if (!isset($pointGet->remark)) {
						$pointGet = new stdClass();
						$pointGet->remark = 2;
					}

					// $leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $b_user_id_reported, $community->kelurahan, $community->kecamatan, $community->kabkota, $community->provinsi);
					$di = array();
					$di['nation_code'] = $nation_code;
					// $di['id'] = $leaderBoardHistoryId;
					$di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
					$di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
					$di['b_user_alamat_location_kabkota'] = $community->kabkota;
					$di['b_user_alamat_location_provinsi'] = $community->provinsi;
					$di['b_user_id'] = $b_user_id_reported;
					$di['plusorminus'] = "-";
					$di['point'] = $pointGet->remark;
					$di['custom_id'] = $c_community_id;
					$di['custom_type'] = 'community';
					$di['custom_type_sub'] = 'takedown post(double point)';
					$di['custom_text'] = 'Admin has '.$di['custom_type_sub'].' '.$pelanggan->fnama.' '.$di['custom_type'].' post and lose '.$di['point'].' point(s)';
					$endDoWhile = 0;
					do{
						$leaderBoardHistoryId = $this->GUIDv4();
						$checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
						if($checkId == 0){
						$endDoWhile = 1;
						}
					}while($endDoWhile == 0);
					$di['id'] = $leaderBoardHistoryId;
					$this->glphm->set($di);
					$this->list_model->trans_commit();

				} else if($checkdata->custom_type_sub === "upload image(double point)") {

					// deduct point
					$pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E15");
					if (!isset($pointGet->remark)) {
						$pointGet = new stdClass();
						$pointGet->remark = 28;
					}

					// $leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $b_user_id_reported, $community->kelurahan, $community->kecamatan, $community->kabkota, $community->provinsi);
					$di = array();
					$di['nation_code'] = $nation_code;
					// $di['id'] = $leaderBoardHistoryId;
					$di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
					$di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
					$di['b_user_alamat_location_kabkota'] = $community->kabkota;
					$di['b_user_alamat_location_provinsi'] = $community->provinsi;
					$di['b_user_id'] = $b_user_id_reported;
					$di['plusorminus'] = "-";
					$di['point'] = $pointGet->remark;
					$di['custom_id'] = $c_community_id;
					$di['custom_type'] = 'community';
					$di['custom_type_sub'] = 'takedown image(double point)';
					$di['custom_text'] = 'Admin has '.$di['custom_type_sub'].' '.$pelanggan->fnama.' '.$di['custom_type'].' post and lose '.$di['point'].' point(s)';
					$endDoWhile = 0;
					do{
						$leaderBoardHistoryId = $this->GUIDv4();
						$checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
						if($checkId == 0){
						$endDoWhile = 1;
						}
					}while($endDoWhile == 0);
					$di['id'] = $leaderBoardHistoryId;
					$this->glphm->set($di);
					$this->list_model->trans_commit();

				} else if($checkdata->custom_type_sub === "upload video(double point)") {

					// deduct point
					$pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E16");
					if (!isset($pointGet->remark)) {
						$pointGet = new stdClass();
						$pointGet->remark = 58;
					}

					// $leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $b_user_id_reported, $community->kelurahan, $community->kecamatan, $community->kabkota, $community->provinsi);
					$di = array();
					$di['nation_code'] = $nation_code;
					// $di['id'] = $leaderBoardHistoryId;
					$di['b_user_alamat_location_kelurahan'] = $community->kelurahan;
					$di['b_user_alamat_location_kecamatan'] = $community->kecamatan;
					$di['b_user_alamat_location_kabkota'] = $community->kabkota;
					$di['b_user_alamat_location_provinsi'] = $community->provinsi;
					$di['b_user_id'] = $b_user_id_reported;
					$di['plusorminus'] = "-";
					$di['point'] = $pointGet->remark;
					$di['custom_id'] = $c_community_id;
					$di['custom_type'] = 'community';
					$di['custom_type_sub'] = 'takedown video(double point)';
					$di['custom_text'] = 'Admin has '.$di['custom_type_sub'].' '.$pelanggan->fnama.' '.$di['custom_type'].' post and lose '.$di['point'].' point(s)';
					$endDoWhile = 0;
					do{
						$leaderBoardHistoryId = $this->GUIDv4();
						$checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
						if($checkId == 0){
						$endDoWhile = 1;
						}
					}while($endDoWhile == 0);
					$di['id'] = $leaderBoardHistoryId;
					$this->glphm->set($di);
					$this->list_model->trans_commit();

				}

			}

		/**   NEW RULE    */

			// by muhammad sofi 24 January 2023 | send push notif to creator
			// select fcm token
			$user = $this->bu_model->getById($nation_code, $b_user_id_reported);

			$dpe = array();
			$dpe['nation_code'] = $nation_code;
			$dpe['b_user_id'] = $b_user_id_reported;
			$dpe['id'] = $this->dpem->getLastId($nation_code, $b_user_id_reported);
			$dpe['type'] = "community";
			if($user->language_id == 2) {
				$dpe['judul'] = "Perhatian";
				$dpe['teks'] =  "Maaf, Postinganmu (".html_entity_decode($community->title,ENT_QUOTES).") telah dihapus oleh Sellon. Poin SPT dari postinganmu akan dibatalkan. Bijaklah dalam share.";
			} else {
				$dpe['judul'] = "Attention";
				$dpe['teks'] =  "Sorry, your post (".html_entity_decode($community->title,ENT_QUOTES).") is deleted by Sellon. The SPT point from your post will be cancelled. Be wise in sharing.";
			}

			$dpe['gambar'] = 'media/pemberitahuan/community.png';
			$dpe['cdate'] = "NOW()";
			$extras = new stdClass();
			$extras->id = $c_community_id;
			$extras->title = $community->title;
			if($user->language_id == 2) { 
				$extras->judul = "Perhatian";
				$extras->teks =  "Maaf, Postinganmu (".html_entity_decode($community->title,ENT_QUOTES).") telah dihapus oleh Sellon. Poin SPT dari postinganmu akan dibatalkan. Bijaklah dalam share.";
			} else {
				$extras->judul = "Attention";
				$extras->teks =  "Sorry, your post (".html_entity_decode($community->title,ENT_QUOTES).") is deleted by Sellon. The SPT point from your post will be cancelled. Be wise in sharing.";
			}

			$dpe['extras'] = json_encode($extras);
			$this->dpem->set($dpe);

			$classified = 'setting_notification_user';
			$code = 'U6';

			$receiverSettingNotif = $this->busm->getValue($nation_code, $b_user_id_reported, $classified, $code);

			if (!isset($receiverSettingNotif->setting_value)) {
				$receiverSettingNotif->setting_value = 0;
			}

			if ($receiverSettingNotif->setting_value == 1 && $user->is_active == 1) {

			if($user->device == "ios"){
				//push notif to ios
				$device = "ios"; //jenis device
			}else{
				//push notif to android
				$device = "android"; //jenis device
			}

			$tokens = $user->fcm_token; //device token
			if(!is_array($tokens)) $tokens = array($tokens);
				if($user->language_id == 2){
					$title = "Perhatian";
					$message = "Maaf, Postinganmu (".html_entity_decode($community->title,ENT_QUOTES).") telah dihapus oleh Sellon. Poin SPT dari postinganmu akan dibatalkan. Bijaklah dalam share.";
				} else {
					$title = "Attention";
					$message = "Sorry, your post (".html_entity_decode($community->title,ENT_QUOTES).") is deleted by Sellon. The SPT point from your post will be cancelled. Be wise in sharing.";
				}
				
				$image = 'media/pemberitahuan/promotion.png';
				$type = 'community';
				$payload = new stdClass();
				$payload->id = $c_community_id;
				$payload->title = html_entity_decode($community->title,ENT_QUOTES);
				// $payload->harga_jual = $community->harga_jual;
				// $payload->foto = base_url().$community->thumb;
				if($user->language_id == 2) {
					$payload->judul = "Perhatian";
					//by Donny Dennison
					//dicomment untuk handle message too big, response dari fcm
					// $payload->teks = strip_tags(html_entity_decode($di['teks']));
					// $payload->teks = "You get a reply from your neighbors (".$tempTitle->{'title'}.")";
					$payload->teks = "Maaf, Postinganmu (".html_entity_decode($community->title,ENT_QUOTES).") telah dihapus oleh Sellon. Poin SPT dari postinganmu akan dibatalkan. Bijaklah dalam share.";
				} else {
					$payload->judul = "Attention";
					$payload->teks = "Sorry, your post (".html_entity_decode($community->title,ENT_QUOTES).") is deleted by Sellon. The SPT point from your post will be cancelled. Be wise in sharing.";
				}

				$this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);

			}
		}

		//end transaction
		$this->list_model->trans_end();

		$this->__json_out($data);
	}

	// by Muhammad Sofi 9 January 2023 | add feature restore post from admin
	public function restore_from_admin() {
		$d = $this->__init();
		$data = array();

		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$c_community_id = $this->input->get('community_id') ? $this->input->get('community_id') : '';
		$admin_name = $this->input->get('admin_name') ? $this->input->get('admin_name') : '';

		if(empty($c_community_id)) {
			$this->status = 400;
			$this->message = 'No Community Id';
			$this->__json_out($data);
			die();
		}

		//start transaction and lock table
		$this->list_model->trans_start();

		//initial insert with latest ID
		$du = array();
		$du['is_active'] = "1";
		$du['is_published'] = "1";
		$du['is_report'] = "0";
		$du['report_date'] = 'null';
		$du['is_take_down'] = "0";
		$du['take_down_date'] = "null";
		$res = $this->list_model->update($nation_code, $c_community_id, $du);

		if (!$res) {
			$this->list_model->trans_rollback();
			$this->list_model->trans_end();
			$this->status = 1108;
			$this->message = "Error while restore community, please try again later";
			$this->__json_out($data);
			die();
		} else {
			$this->list_model->trans_commit();
			$this->status = 200;
			$this->message = 'Success';
		}

		//delete from c_community_report
		$this->list_report_model->delete_by_community_id($nation_code, $c_community_id);

		//end transaction
		$this->list_model->trans_end();

		$this->__json_out($data);
	}

	// START by Muhammad Sofi 14 January 2022 18:09 | move function to get function data reported discussion to community/listing
	public function reported_discussion() { // reported community discussion
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$draw = $this->input->post("draw");
		$sval = $this->input->post("search");
		$sSearch = $this->input->post("sSearch");
		$sEcho = $this->input->post("sEcho");
		$page = $this->input->post("iDisplayStart");
		$pagesize = $this->input->post("iDisplayLength");

		$iSortCol_0 = $this->input->post("iSortCol_0");
		$sSortDir_0 = $this->input->post("sSortDir_0");

		$sortCol = "date";
		$sortDir = strtoupper($sSortDir_0);
		if(empty($sortDir)) $sortDir = "DESC";
		if(strtolower($sortDir) != "desc"){
			$sortDir = "ASC";
		}

		switch($iSortCol_0){
			case 0:
				$sortCol = "id";
				break;
			case 1:
				$sortCol = "image_icon";
				break;
			case 2:
				$sortCol = "title";
				break;
			case 3:
				$sortCol = "prioritas";
				break;
			case 4:
				$sortCol = "is_active";
				break;
			default:
				$sortCol = "id";
		}

		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;

		$keyword = $sSearch;

		$this->status = 200;
		$this->message = 'Success';
		$dcount = $this->list_model->countReportedDiscussion($nation_code, $keyword);
		$ddata = $this->list_model->getReportedDiscussion($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword);

		foreach($ddata as &$gd){

			if(isset($gd->text)){
				if (strlen($gd->text)>255) {
					$gd->text = substr($this->__convertToEmoji($gd->text), 0, 255)." <strong>(More...)<strong>";
				} else {
					$gd->text = $this->__convertToEmoji($gd->text);
				}
			}

            if (isset($gd->cdate)) {
				$gd->cdate = date("d F Y", strtotime($gd->cdate));
            }

            if (isset($gd->user)) {
                $user = $gd->user;
                $gd->user = '<span style="font-size: 1.2em; font-weight: bolder;">'.$user.'</span><br />';
				// by Muhammad Sofi - 3 November 2021 10:00
				// remark code
                if (isset($gd->address2)) $gd->user .= '<span>Address: '.$gd->address2.'</span><br />';
            }

            if (isset($gd->is_active)) {
                $status = "";
				if(!empty($gd->is_active)) $status = 'Active';
				else $status = 'Inactive';
                $gd->is_active = '<span>'.$status.'</span><br />';

				if(!empty($gd->is_report)) $status = 'Yes';
				else $status = 'No';
				$gd->is_active .= '<span> Is Reported: '.$status.' </span><br />';

				if(!empty($gd->is_take_down)) $status = 'Yes';
				else $status = 'No';
				$gd->is_active .= '<span> Is Takedown : '.$status.' </span><br />';
            }
		}
		//sleep(3);
		$another = array();
		$this->__jsonDataTable($ddata,$dcount);
	}

	public function report_discussion($report)
    {
        $d = $this->__init();
        $data = array();

		$id = $this->input->get('id') ? $this->input->get('id') : '';
        $c_community_discussion_id = $this->input->get('c_community_discussion_id') ? $this->input->get('c_community_discussion_id') : '';

        if ($id<=0) {
            $this->status = 450;
            $this->message = 'Invalid ID';
            $this->__json_out($data);
            die();
        }
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

		if($report == "0") {
			$this->status = 200;
			$this->message = 'Success';
			$this->statuspost = 'ignore';
			$du = array();
			$du['is_report'] = "0";
			$du['report_date'] = "NULL";
			$this->list_model->updateStatusDiscussionIgnore($nation_code, $c_community_discussion_id, $du);
		} else if($report == "1") {
			$this->status = 200;
			$this->message = 'Success';
			$this->statuspost = 'takedown';
			$du = array();
			$du['is_take_down'] = "1";
			$du['take_down_date'] = "NOW()";
			$this->list_model->updateStatusDiscussionTakedown($nation_code, $c_community_discussion_id, $du);

			//START by Donny Dennison - 28 december 2022 17:46
			//bug take down community reply/discussion dont reduce the total reply/discussion
        	$list = $this->discussion_model->getById($nation_code, $c_community_discussion_id);

        	//update total_discussion in table c_community
			$this->list_model->updateTotalDiscussion($nation_code, $list->c_community_id, '-', 1);

			//if discussion is a parent, child also deleted
			if($list->parent_c_community_discussion_id == 0){

				$getTotalChildIsActive = $this->discussion_model->countAllChild($nation_code, $list->id, $list->c_community_id);

				//update total_discussion in table c_community
				$this->list_model->updateTotalDiscussion($nation_code, $list->c_community_id, '-', $getTotalChildIsActive);

				$di = array();
				$di['edate'] = 'NOW()';
				$di['is_active'] = 0;
				$this->discussion_model->updateByParentCommunityDiscussionId($nation_code, $list->id, $di);

			}
			//END by Donny Dennison - 28 december 2022 17:46
			//bug take down community reply/discussion dont reduce the total reply/discussion

		} else{}
        // if ($res) {
        //     $this->status = 200;
        //     $this->message = 'Success';
        // } else {
        //     $this->status = 920;
        //     $this->message = 'Failed change data to database';
        // }
        $this->__json_out($data);
    }
	// END by Muhammad Sofi 14 January 2022 18:09 | move function to get function data reported discussion to community/listing

	// by Muhammad Sofi 26 January 2022 13:37 | get data user(b_user_id) from table c_community
	public function getCustomer() {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;

        //standar input
        $search = $this->input->post("search");

        $ddata = $this->list_model->getCustomer($nation_code, $search, 1);

        $data = array();

        foreach ($ddata as $gd) {
            $data[] = array("id"=>$gd->user_id, "text"=>$gd->user_name);
        }
        echo json_encode($data);
    }

	public function getAdminName() {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;

        //standar input
        $search = $this->input->post("search");

        $ddata = $this->apm->getAdminName($nation_code, $search, 1);

        $data = array();

        foreach ($ddata as $gd) {
            $data[] = array("id"=>$gd->user_alias, "text"=> $gd->nama);
        }
        echo json_encode($data);
    }
}
