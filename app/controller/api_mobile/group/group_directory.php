<?php
class Group_Directory extends JI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load("api_mobile/b_user_model", "bu");
		$this->load("api_mobile/group/i_group_model", "igm");
		$this->load("api_mobile/group/i_group_directory_model", 'igdm');
		$this->load("api_mobile/group/i_group_directory_attachment_model", 'igdam');
		$this->load("api_mobile/common_code_model", "ccm");
		$this->load("api_mobile/group/i_group_participant_model", "igparticipantm");
		$this->load("api_mobile/group/i_group_post_model", "igpostm");
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

	//by Donny Dennison - 11 august 2022 10:46
	//fix rotated image after resize(thumb)
	//credit: https://stackoverflow.com/a/18919355/7578520
	// private function correctImageOrientation($filename) {
	// //credit: https://github.com/FriendsOfCake/cakephp-upload/issues/221#issuecomment-50128062
	// $exif = false;
	// $size = getimagesize($filename, $info);
	// if (!isset($info["APP13"])) {
	// 		if (function_exists('exif_read_data')) {
	// 			$exif = exif_read_data($filename);
	// 			if($exif && isset($exif['Orientation'])) {
	// 				$orientation = $exif['Orientation'];
	// 				if($orientation != 1){
	// 						$img = imagecreatefromjpeg($filename);
	// 						$deg = 0;
	// 						switch ($orientation) {
	// 							case 3:
	// 							$deg = 180;
	// 							break;
	// 							case 6:
	// 							$deg = 270;
	// 							break;
	// 							case 8:
	// 							$deg = 90;
	// 							break;
	// 						}
	// 						if ($deg) {
	// 							$img = imagerotate($img, $deg, 0);        
	// 						}
	// 						// then rewrite the rotated image back to the disk as $filename
	// 						imagejpeg($img, $filename, 95);
	// 				} // if there is some rotation necessary
	// 			} // if have the exif orientation info
	// 		} // if function exists
	// }
	// }

	private function __uploadImagex($nation_code, $keyname, $produk_id="0", $ke="")
	{
		$sc = new stdClass();
		$sc->status = 500;
		$sc->message = 'Error';
		$sc->image = '';
		$sc->thumb = '';
		$sc->file_size = '';
		$sc->file_size_thumb = '';
		// $produk_id = (int) $produk_id;
		if (isset($_FILES[$keyname]['name'])) {

			//by Donny Dennison - 30 november 2021 14:59
			//comment check size uploaded file
			// if ($_FILES[$keyname]['size']>2000000) {
			//   $sc->status = 301;
			//   $sc->message = 'Image file Size too big, please try again';
			//   return $sc;
			// }

			$filenames = pathinfo($_FILES[$keyname]['name']);
			if (isset($filenames['extension'])) {
				$fileext = strtolower($filenames['extension']);
			} else {
				$fileext = '';
			}

			if (!in_array($fileext, array("jpg","png","jpeg"))) {
				$sc->status = 303;
				$sc->message = 'Invalid file extension, please try other file.';
				return $sc;
			}
			$filename = "$nation_code-$produk_id-$ke";
			$filethumb = $filename.'-thumb';

			$targetdir = $this->media_group_album;
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

			$sc->status = 998;
			$sc->message = 'Invalid file extension uploaded';
			if (in_array($fileext, array("png", "jpg", "jpeg", "bmp", "heic", "heif", "webp"))) {
				$filecheck = SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename.'.'.$fileext;
				if (file_exists($filecheck)) {
				unlink($filecheck);
				$rand = rand(0, 999);
				$filename = "$nation_code-$produk_id-$ke-".$rand;
				$filecheck = SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename.'.'.$fileext;
				if (file_exists($filecheck)) {
					unlink($filecheck);
					$rand = rand(1000, 99999);
					$filename = "$nation_code-$produk_id-$ke-".$rand;
				}
				}
				$filethumb = $filename."-thumb.".$fileext;
				$filename = $filename.".".$fileext;

				move_uploaded_file($_FILES[$keyname]['tmp_name'], SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
				if (is_file(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename) && file_exists(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)) {
				if (@mime_content_type(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename) == 'image/webp') {
					$sc->status = 302;
					$sc->message = 'WebP image format currently unsupported';
					return $sc;
				}
				if (file_exists(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb) && is_file(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb)) {
					unlink(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
				}
				if (file_exists(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename) && is_file(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)) {

					//START by Donny Dennison - 11 august 2022 10:46
					//fix rotated image after resize(thumb)
					// if (in_array($fileext, array("jpg","jpeg"))) {
					// 	$this->correctImageOrientation(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
					// }
					//END by Donny Dennison - 11 august 2022 10:46
					//fix rotated image after resize(thumb)

					$this->lib("wideimage/WideImage", 'wideimage', "inc");
					WideImage::load(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)->reSize(370)->saveToFile(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
					$sc->status = 200;
					$sc->message = 'Success';
					$sc->thumb = str_replace("//", "/", $targetdir.'/'.$filethumb);
					$sc->image = str_replace("//", "/", $targetdir.'/'.$filename);
					$sc->file_size = filesize(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
					$sc->file_size_thumb = filesize(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
					
				} else {
					$sc->status = 997;
					$sc->message = 'Failed';
				}
				} else {
				$sc->status = 999;
				$sc->message = 'Failed';
				}
			} else {
				$sc->status = 998;
				$sc->message = 'Invalid file extension uploaded';
			}
		} else {
			$sc->status = 988;
			$sc->message = 'Keyname file does not exists';
		}
		// if ($this->is_log) {
		//   $this->seme_log->write("api_mobile", 'API_Mobile/Community::__uploadImagex -- INFO KeyName: '.$keyname.' PID:'.$produk_id.' ke:'.$ke.' '.$sc->status.' '.$sc->message.'');
		// }
		return $sc;
	}

	private function __sortCol($sort_col, $tbl_as)
	{
		switch ($sort_col) {
		case 'id':
			$sort_col = "$tbl_as.id";
			break;
		case 'name':
			$sort_col = "$tbl_as.name";
			break;
		case 'cdate':
			$sort_col = "$tbl_as.cdate";
			break;
		default:
			$sort_col = "$tbl_as.cdate";
		}
		return $sort_col;
	}

	private function __sortDir($sort_dir)
	{
		$sort_dir = strtolower($sort_dir);
		if ($sort_dir == "asc") {
		$sort_dir = "ASC";
		} else {
		$sort_dir = "DESC";
		}
		return $sort_dir;
	}

	private function __page($page)
	{
		if (!is_int($page)) {
		$page = (int) $page;
		}
		if ($page<=0) {
		$page = 1;
		}
		return $page;
	}

	private function __pageSize($page_size)
	{
		$page_size = (int) $page_size;
		if ($page_size<=0) {
		$page_size = 10;
		}
		return $page_size;
	}

	private function __sortColAttachment($sort_col, $tbl_as)
	{
		switch ($sort_col) {
			case 'cdate':
				$sort_col = "$tbl_as.cdate";
				break;
			case 'id':
				$sort_col = "$tbl_as.id";
				break;
			case 'name':
				$sort_col = "$tbl_as.name";
				break;

			default:
			$sort_col = "$tbl_as.cdate";
		}
		return $sort_col;
	}

	private function __sortDirAttachment($sort_dir)
	{
		$sort_dir = strtolower($sort_dir);
		if ($sort_dir == "asc") {
			$sort_dir = "ASC";
		} else {
			$sort_dir = "DESC";
		}
		return $sort_dir;
	}

	private function __pageAttachment($page)
	{
		if (!is_int($page)) {
		$page = (int) $page;
		}
		if ($page<=0) {
		$page = 1;
		}
		return $page;
	}

	private function __pageSizeAttachment($page_size)
	{
		$page_size = (int) $page_size;
		if ($page_size<=0) {
		$page_size = 10;
		}
		return $page_size;
	}

	public function create_directory()
	{
		//initial
		$dt = $this->__init();

		//default result
		$data = array();
		$data['directory'] = new stdClass();

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

		$timezone = $this->input->post('timezone');
		if($this->isValidTimezoneId($timezone) === false){
			$timezone = $this->default_timezone;
		}

		$type_directory = $this->input->get('type_directory');
		if(empty($type_directory)) {
			$type_directory = "album";
		}

		if (empty($type_directory) || !in_array($type_directory, array('album', 'folder'))) {
			$this->status = 1115;
			$this->message = 'Missing or invalid type';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		//collect input
		$group_id = trim($this->input->post('i_group_id'));
		$b_user_id = $pelanggan->id;
		$directory_name = trim($this->input->post('directory_name'));

		if (strlen($group_id)<3){
			$this->status = 1101;
			$this->message = 'Club id not found';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		$queryResult = $this->igm->getById($nation_code, $group_id);
		if (!isset($queryResult->id)){
			$this->status = 1101;
			$this->message = 'Club id not found';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		// check if b_user is owner / admin group
		$check_group_owner = $this->igparticipantm->getStatus($nation_code, $group_id, $b_user_id);
		if($check_group_owner->is_owner == "0" && $check_group_owner->is_co_admin == "0") {
			$this->status = 1125;
			$this->message = "Your priviledge is limited as you're member";
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		if(empty($directory_name)) {
			$this->status = 1133;
			$this->message = "Directory name can't be empty";
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		//start transaction and lock table
		$this->igdm->trans_start();

		$di = array();
		$di['nation_code'] = $nation_code;
		$di['i_group_id'] = $group_id;
		$di['b_user_id'] = $b_user_id;
		$di['directory_name'] = $directory_name;
		$di['type'] = $type_directory;
		$di['cdate'] = 'NOW()';
		$di['is_active'] = 1;
		$di['is_owner_group'] = $check_group_owner->is_owner == "1" ? "1" : "0";
		$di['is_owner_directory'] = 1;
		$endDoWhile = 0;
		do {
			$id = $this->GUIDv4();
			$checkId = $this->igdm->checkId($nation_code, $id);
			if($checkId == 0){
			$endDoWhile = 1;
			}
		} while($endDoWhile == 0);
		$di['id'] = $id;
		$res = $this->igdm->set($di);
		if(!$res)
		{
			$this->igdm->trans_rollback();
			$this->igdm->trans_end();
			$this->status = 1107;
			$this->message = "Error, please try again later";
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		$this->igdm->trans_commit();
		$this->igdm->trans_end();

		$this->status = 200;
		$this->message = "Directory saved successfully";
		$data['directory'] = $this->igdm->getByDirectoryId($nation_code, $id);

		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
	}

	public function add_directory_attachment()
	{
		//initial
		$dt = $this->__init();

		//default result
		$data = array();
		$data['directory'] = new stdClass();

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

		$timezone = $this->input->post('timezone');
		if($this->isValidTimezoneId($timezone) === false){
			$timezone = $this->default_timezone;
		}

		$group_id = trim($this->input->post('i_group_id'));
		$group_directory_id = trim($this->input->post('i_group_directory_id'));

		if (strlen($group_id) < 3){
			$this->status = 1101;
			$this->message = 'Club id not found';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		$queryResult = $this->igm->getById($nation_code, $group_id);
		if (!isset($queryResult->id)){
			$this->status = 1101;
			$this->message = 'Club id not found';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		$type_directory = $this->input->post('type_directory');
		if(empty($type_directory)) {
			$type_directory = "album";
		}

		if (!in_array($type_directory, array('folder', 'album'))) {
			$this->status = 1115;
			$this->message = 'Missing or invalid type';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		$save_to_all = 0;
		if(empty($group_directory_id))
		{ 
			$save_to_all = 1;
		} else {
			$save_to_all = 0;
		}

		//start transaction and lock table
		$this->igdam->trans_start();

		// $file_type = $_FILES['attachment']['type'];
		$filenames = pathinfo($_FILES['attachment']['name']);
		$fileext = '';
		if (isset($filenames['extension'])) {
			$fileext = strtolower($filenames['extension']);
		}

		$keyname = 'attachment';
		$keyname_thumb = 'video_thumb';

		$di = array();

		$endDoWhile = 0;
		do {
			$attachmentId = $this->GUIDv4();
			$checkId = $this->igdam->checkId($nation_code, $attachmentId);
			if($checkId == 0){
				$endDoWhile = 1;
			}
		} while($endDoWhile == 0);


		if(in_array($fileext, array("png", "jpg", "jpeg", "bmp", "heic", "heif", "webp")) && $type_directory == "album")
		{
			$sc = $this->__uploadImagex($nation_code, $keyname, $group_directory_id, $attachmentId);
			if (isset($sc->status)) {
				if ($sc->status==200) {
					$di['nation_code'] = $nation_code;
					$di['id'] = $attachmentId;
					$di['i_group_directory_id'] = $save_to_all === 1 ? $group_id : $group_directory_id;
					$di['i_group_id'] = $group_id;
					$di['b_user_id'] = $pelanggan->id;
					$di['jenis'] = 'image';
					$di['url'] = $sc->image;
					$di['url_thumb'] = $sc->thumb;
					$di['file_name'] = $_FILES[$keyname]['name'];
					$di['file_size'] = $sc->file_size;
					$di['file_size_thumb'] = $sc->file_size_thumb;
					$di['cdate'] = 'NOW()';
					$di['is_active'] = 1;
					$di['is_owner_attachment'] = 1;
					$this->igdam->set($di);
					$getData = $this->igdam->getByData($nation_code, $attachmentId, "photo");
					$getData->url = $this->cdn_url($getData->url);
				}
			}
		}

		if(in_array($fileext, array("mp4", "mov", "mkv")) && $type_directory == "album")
		{
			$filenames = pathinfo($_FILES[$keyname]['name']);
			$fileext = '';
			if (isset($filenames['extension'])) {
				$fileext = strtolower($filenames['extension']);
			}

			$targetdir = $this->media_group_album;
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

			$rand = rand(0, 999);
			$filename = "$nation_code-$attachmentId-".$rand;
			$filethumb = $filename."-thumb.png";
			$filename = $filename.".".$fileext;

			if(move_uploaded_file($_FILES[$keyname]['tmp_name'], SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)) { 
				$video_url = str_replace("//", "/", $targetdir.'/'.$filename);
				$video_url = str_replace("\\", "/", $video_url);

				$video_thumbnail = str_replace("//", "/", $targetdir.'/'.$filethumb);
				$video_thumbnail = str_replace("\\", "/", $video_thumbnail);

				move_uploaded_file($_FILES[$keyname_thumb]['tmp_name'], SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);
		
				$di['nation_code'] = $nation_code;
				$di['id'] = $attachmentId;
				$di['i_group_directory_id'] = $save_to_all === 1 ? $group_id : $group_directory_id;
				$di['i_group_id'] = $group_id;
				$di['b_user_id'] = $pelanggan->id;
				$di['jenis'] = 'video';
				$di['url'] = $video_url;
				$di['url_thumb'] = $video_thumbnail;
				$di['file_name'] = $_FILES[$keyname]['name'];
				$di['file_size'] = filesize(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
				$di['file_size_thumb'] = filesize(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filethumb);;
				$di['cdate'] = 'NOW()';
				$di['is_active'] = 1;
				$di['is_owner_attachment'] = 1;
				// $di['convert_status'] = 1;
				$this->igdam->set($di);
				$getData = $this->igdam->getByData($nation_code, $attachmentId, "video");
				$getData->url = $this->cdn_url($getData->url);
				$getData->url_thumb = $this->cdn_url($getData->url_thumb);
			}
		}

		$listExtensionFile = array(
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

		if(in_array($fileext, $listExtensionFile) && $type_directory == "folder")
		{
			$filenames = pathinfo($_FILES[$keyname]['name']);
			$fileext = '';
			if (isset($filenames['extension'])) {
				$fileext = strtolower($filenames['extension']);
			}

			$targetdir = $this->media_group_folder;
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

			$rand = rand(0, 999);
			$filename = "$nation_code-$attachmentId-".$rand;
			$filename = $filename.".".$fileext;

			if(move_uploaded_file($_FILES[$keyname]['tmp_name'], SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename)) { 
				$file_url = str_replace("//", "/", $targetdir.'/'.$filename);
				$file_url = str_replace("\\", "/", $file_url);
		
				$di['nation_code'] = $nation_code;
				$di['id'] = $attachmentId;
				$di['i_group_directory_id'] = $save_to_all === 1 ? $group_id : $group_directory_id;
				$di['i_group_id'] = $group_id;
				$di['b_user_id'] = $pelanggan->id;
				$di['jenis'] = 'file';
				$di['url'] = $file_url;
				$di['url_thumb'] = 0;
				$di['file_name'] = $_FILES[$keyname]['name'];
				$di['file_size'] = filesize(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.$filename);
				$di['file_size_thumb'] = 0;
				$di['cdate'] = 'NOW()';
				$di['is_active'] = 1;
				$di['is_owner_attachment'] = 1;
				// $di['convert_status'] = 1;
				$this->igdam->set($di);
				$getData = $this->igdam->getByData($nation_code, $attachmentId, "file");
				$getData->url = $this->cdn_url($getData->url);
			}
		}

		$this->igdam->trans_commit();
		$this->igdam->trans_end();

		$this->status = 200;
		$this->message = "Attachment Directory saved successfully";

		// $data['directory'] = $this->igdam->getAll($nation_code);
		$data['directory'] = $getData;

		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
	}

	public function detail()
	{
		//initial
		$dt = $this->__init();

		//default result
		$data = array();

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

		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if (!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
			die();
		}

		$type_directory = $this->input->get('type_directory');
		if(empty($type_directory)) {
			$type_directory = "album";
		}

		if (empty($type_directory) || !in_array($type_directory, array('album', 'folder'))) {
			$this->status = 1115;
			$this->message = 'Missing or invalid type';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		$data['directory_'. $type_directory] = new stdClass();

		$group_id = trim($this->input->post('i_group_id'));

		if (strlen($group_id)<3){
			$this->status = 1101;
			$this->message = 'Club id not found';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		$queryResult = $this->igm->getById($nation_code, $group_id);
		if (!isset($queryResult->id)){
			$this->status = 1101;
			$this->message = 'Club id not found';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		$sort_col = $this->input->post("sort_col");
		$sort_dir = $this->input->post("sort_dir");
		$page = $this->input->post("page");
		// $page_size = $this->input->post("page_size");
		$page_size = 20;
		$timezone = $this->input->post('timezone');
		if($this->isValidTimezoneId($timezone) === false){
			$timezone = $this->default_timezone;
		}

		//sanitize input
		$tbl_attachment_as = $this->igdam->getTblAs();

		$sort_col = $this->__sortColAttachment($sort_col, $tbl_attachment_as);
		$sort_dir = $this->__sortDirAttachment($sort_dir);
		$page = $this->__page($page);
		$page_size = $this->__pageSize($page_size);
		$keyword = trim($this->input->post("keyword"));

		//keyword
		if (mb_strlen($keyword)>1) {
			//$keyword = utf8_encode(trim($keyword));
			$enc = mb_detect_encoding($keyword, 'UTF-8');
			if ($enc == 'UTF-8') {
			} else {
				$keyword = iconv($enc, 'ISO-8859-1//TRANSLIT', $keyword);
			}
		} else {
			$keyword="";
		}
		$keyword = filter_var(strip_tags($keyword), FILTER_SANITIZE_SPECIAL_CHARS);
		$keyword = substr($keyword, 0, 32);

		$group_directory_id = trim($this->input->post('i_group_directory_id'));
		$directory = $this->igdm->getByDirectoryId($nation_code, $group_directory_id);

		if(!isset($directory->is_owner_directory)) {
			$directory->can_publish = 0;
		} else {
			if($directory->is_owner_directory == "1" || $pelanggan->id == $directory->b_user_id) {
				$directory->can_publish = 1;
			} else {
				$directory->can_publish = 0;
			}
		}

		if($type_directory == "album") {
			if(empty($group_directory_id)) {
				$directory->album = new stdClass();
				if($pelanggan->language_id == 2) { 
					$directory->album->album_name = "Semua Foto dan Video";
				} else {
					$directory->album->album_name = "All Photo and Video";
				}

				if(!isset($directory->is_owner_directory)) {
					$directory->can_publish = 0;
				}

			} else {
				if (!isset($directory->id)){
					$this->status = 1116;
					$this->message = 'Directory id not found';
					$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
					die();
				}
			}

			$directory->photo_and_video = array();
			$jenis = 'photo_video';
		} else if($type_directory == "folder") {
			if(empty($group_directory_id)) {
				$directory->folder = new stdClass();
				if($pelanggan->language_id == 2) {
					$directory->folder->folder_name = "Semua File";
				} else {
					$directory->folder->folder_name = "All Files";
				}
			} else {
				if (!isset($directory->id)){
					$this->status = 1116;
					$this->message = 'Directory id not found';
					$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
					die();
				}
			}

			$directory->file = array();
			$jenis = 'folder_file';
		}

		$attachments = $this->igdam->getByDirectoryId($nation_code, empty($group_directory_id) ? $group_id : $group_directory_id, $jenis, $page, $page_size, $sort_col, $sort_dir, $keyword);
		foreach ($attachments as $atc){
			$atc->is_owner_attachment = "0";
			if($atc->i_group_post_id == "0" && $atc->b_user_id == $pelanggan->id){
				$atc->is_owner_attachment = "1";
			}

			if($atc->jenis == 'image' || $atc->jenis == 'video'){
				$atc->url = $this->cdn_url($atc->url);
				$atc->url_thumb = $this->cdn_url($atc->url_thumb);
				$directory->photo_and_video[] = $atc;
			}

			if($atc->jenis == 'file'){ 
				$atc->url = $this->cdn_url($atc->url);
				$directory->file[] = $atc;
			}
			unset($attachments, $atc);
		}

		$data['directory_'. $type_directory] = $directory;

		//response
		$this->status = 200;
		$this->message = 'Success';
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
	}

	public function list_all_directory()
	{
		//initial
		$dt = $this->__init();

		//default result
		$data = array();

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

		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if (!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
			die();
		}

		$sort_col = $this->input->post("sort_col");
		$sort_dir = $this->input->post("sort_dir");
		$page = $this->input->post("page");
		$page_size = $this->input->post("page_size");
		$keyword_folder = trim($this->input->post("keyword_folder"));
		$keyword = trim($this->input->post("keyword"));
		$b_user_id = $this->input->post("b_user_id");

		$timezone = $this->input->post('timezone');
		if($this->isValidTimezoneId($timezone) === false){
			$timezone = $this->default_timezone;
		}

		//sanitize input
		$tbl_as = $this->igdm->getTblAs();
		$tbl_attachment_as = $this->igdam->getTblAs();

		$sort_col = $this->__sortCol($sort_col, $tbl_as);
		$sort_dir = $this->__sortDir($sort_dir);
		$page = $this->__page($page);
		$page_size = $this->__pageSize($page_size);

		// $sort_col_attachment = $this->__sortColAttachment($sort_col, $tbl_attachment_as);
		// $sort_dir_attachment = $this->__sortDirAttachment($sort_dir);
		// $page_attachment = $this->__page($page);
		// $page_size_attachment = $this->__pageSize($page_size);

		//keyword
		// if (mb_strlen($keyword)>1) {
		// 	//$keyword = utf8_encode(trim($keyword));
		// 	$enc = mb_detect_encoding($keyword, 'UTF-8');
		// 	if ($enc == 'UTF-8') {
		// 	} else {
		// 		$keyword = iconv($enc, 'ISO-8859-1//TRANSLIT', $keyword);
		// 	}
		// } else {
		// 	$keyword="";
		// }
		$keyword_folder = filter_var(strip_tags($keyword_folder), FILTER_SANITIZE_SPECIAL_CHARS);
		$keyword_folder = substr($keyword_folder, 0, 32);
		$keyword = filter_var(strip_tags($keyword), FILTER_SANITIZE_SPECIAL_CHARS);
		$keyword = substr($keyword, 0, 32);

		$group_id = trim($this->input->post('group_id'));
		if (strlen($group_id)<3){
			$this->status = 1101;
			$this->message = 'Club id not found';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		$queryResult = $this->igm->getById($nation_code, $group_id);
		if (!isset($queryResult->id)){
			$this->status = 1101;
			$this->message = 'Club id not found';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		$type_directory = $this->input->get('type_directory');
		if(empty($type_directory)) {
			$type_directory = "album";
		}

		if (empty($type_directory) || !in_array($type_directory, array('album', 'folder'))) {
			$this->status = 1115;
			$this->message = 'Missing or invalid type';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		$directory_all = new stdClass();
		$list_directory = new stdClass();

		if($type_directory == "album") { 
			$directory_all->album = new stdClass();
			if($pelanggan->language_id == 2) {
				$directory_all->album->album_name = "Semua Foto dan Video";
			} else {
				$directory_all->album->album_name = "All Photo and Video";
			}
			$directory_all->photo_and_video = array();
			$jenis = 'photo_video';
		} else if($type_directory == "folder") {
			$directory_all->folder = new stdClass();
			if($pelanggan->language_id == 2) {
				$directory_all->folder->folder_name = "Semua File";
			} else {
				$directory_all->folder->folder_name = "All Files";
			}
			$directory_all->file = array();
			$jenis = 'folder_file';
		}

		// count attachment
		$directory_all->count = $this->igdam->countAttachmentBy($nation_code, $group_id, "all");

		$attachment = $this->igdam->getByDirectoryId($nation_code, $group_id, $jenis, 1, 6, "cdate", "desc", $keyword);
		foreach ($attachment as $atc) {
			$atc->is_owner_attachment = "0";
			if($atc->i_group_post_id == "0" && $atc->b_user_id == $pelanggan->id){
				$atc->is_owner_attachment = "1";
			}

			if($atc->jenis == 'image' || $atc->jenis == 'video'){
				$atc->url = $this->cdn_url($atc->url);
				$atc->url_thumb = $this->cdn_url($atc->url_thumb);
				$directory_all->photo_and_video[] = $atc;
			}

			if($atc->jenis == 'file'){ 
				$atc->url = $this->cdn_url($atc->url);
				$directory_all->file[] = $atc;
			}
		}
		unset($attachments, $atc);

		$data['directory_all'] = $directory_all;

		$list_directory = $this->igdm->getByGroupId($nation_code, $page, $page_size, $sort_col, $sort_dir, $group_id, $type_directory, $keyword_folder);

		if($type_directory == "album") { 
			$array = 'photo_and_video';
		} else if($type_directory == "folder") {
			$array = 'file';
		}

		foreach ($list_directory as &$list_dir){ 
			// count attachment
			$count_each_list = $this->igdam->countAttachmentBy($nation_code, $list_dir->id, "list");
			$list_dir->count = $count_each_list;
			if($list_dir->is_owner_directory == "1" || $pelanggan->id == $list_dir->b_user_id) {
				$list_dir->can_publish = 1;
			} else {
				$list_dir->can_publish = 0;
			}

			$list_dir->$array = array();

			$attachments = $this->igdam->getByDirectoryId($nation_code, $list_dir->id, $jenis, 1, 6, "cdate", "desc", $keyword);
			foreach ($attachments as $atc) {
				$atc->is_owner_attachment = "0";
				if($atc->i_group_post_id == "0" && $atc->b_user_id == $pelanggan->id){
					$atc->is_owner_attachment = "1";
				}

				if($atc->jenis == 'image' || $atc->jenis == 'video'){
					$atc->url = $this->cdn_url($atc->url);
					$atc->url_thumb = $this->cdn_url($atc->url_thumb);
					$list_dir->$array[] = $atc;
				} 

				if($atc->jenis == 'file') {
					$atc->url = $this->cdn_url($atc->url);
					$list_dir->$array[] = $atc;
				}
			}
			unset($attachments, $atc);
		}

		$data['list_directory'] = $list_directory;

		$this->status = 200;
		$this->message = 'Success';

		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
	}

	public function edit_directory_name()
	{
	  	//initial
		$dt = $this->__init();

		//default result
		$data = array();
		$data['directory_name'] = new stdClass();

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

		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if (!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
			die();
		}

		$timezone = $this->input->post('timezone');
		if($this->isValidTimezoneId($timezone) === false){
			$timezone = $this->default_timezone;
		}

		$type_directory = $this->input->get('type_directory');
		if(empty($type_directory)) {
			$type_directory = "album";
		}

		if (empty($type_directory) || !in_array($type_directory, array('album', 'folder'))) {
			$this->status = 1115;
			$this->message = 'Missing or invalid type';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		$directory_id = $this->input->post('i_group_directory_id');

		if (strlen($directory_id) < 3){
			$this->status = 1116;
			$this->message = 'Directory id is required';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		$queryResult = $this->igdm->getByDirectoryId($nation_code, $directory_id);
		if (!isset($queryResult->id)){
			$this->status = 1116;
			$this->message = 'Directory id not found';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		// check if user id is owner or not
		$checkIsOwnerDirectory = $this->igdm->IsOwnerDirectory($nation_code, $directory_id, $pelanggan->id);
		if($checkIsOwnerDirectory == 0)
		{
			$this->status = 1117;
			$this->message = "You can't change this directory name";
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		$directory_name = $this->input->post('directory_name');

		//start transaction and lock table
		$this->igdm->trans_start();
		
		$du = array();
		$du['directory_name'] = $directory_name;
		$res = $this->igdm->update($nation_code, $directory_id, $du);
		if(!$res)
		{
			$this->igdm->trans_rollback();
			$this->igdm->trans_end();
			$this->status = 1107;
			$this->message = "Error, please try again later";
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		$this->igdm->trans_commit();
		$this->igdm->trans_end();

		$this->status = 200;
		$this->message = 'Directory renamed successfully';
		$data['directory_name'] = $this->igdm->getByDirectoryId($nation_code, $directory_id);

		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
	}

	public function delete_directory()
	{
	  	//initial
		$dt = $this->__init();

		//default result
		$data = array();
		$data['directory'] = new stdClass();

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

		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if (!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
			die();
		}

		$timezone = $this->input->post('timezone');
		if($this->isValidTimezoneId($timezone) === false){
			$timezone = $this->default_timezone;
		}

		$directory_id = $this->input->post('i_group_directory_id');

		if (strlen($directory_id) < 3){
			$this->status = 1116;
			$this->message = 'Directory id is required';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		$queryResult = $this->igdm->getByDirectoryId($nation_code, $directory_id);
		if (!isset($queryResult->id)){
			$this->status = 1116;
			$this->message = 'Directory id not found';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		// check if user id is owner folder or not
		$checkIsOwnerDirectory = $this->igdm->IsOwnerDirectory($nation_code, $directory_id, $pelanggan->id);
		if($checkIsOwnerDirectory == 0)
		{
			$this->status = 1126;
			$this->message = "You can't delete this directory";
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		//start transaction and lock table
		$this->igdm->trans_start();
		
		$du = array();
		$du['is_active'] = 0;
		$res = $this->igdm->update($nation_code, $directory_id, $du);
		if(!$res){
			$this->igdm->trans_rollback();
			$this->igdm->trans_end();
			$this->status = 1107;
			$this->message = "Error, please try again later";
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		$this->igdm->trans_commit();
		$this->igdm->trans_end();

		$this->status = 200;
		$this->message = 'Directory deleted successfully';
		$data['directory'] = [];

		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
	}

	public function edit_attachment_name()
	{
	  //initial
		$dt = $this->__init();
	
		//default result
		$data = array();
		$data['attachment_name'] = new stdClass();
	
		//check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
		if (empty($nation_code)) {
			$this->status = 101;
			$this->message = 'Missing or invalid nation_code';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->Wlanguage_id)) ? $pelanggan->language_id : "", "general");
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
	
		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if (!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
			die();
		}
	
		$timezone = $this->input->post('timezone');
		if($this->isValidTimezoneId($timezone) === false){
			$timezone = $this->default_timezone;
		}
	
		$attachment_id = $this->input->post('attachment_id');
		if (strlen($attachment_id) < 3){
			$this->status = 1118;
			$this->message = 'Attachment id is required';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}
	
		$queryResult = $this->igdam->getByAttachmentId($nation_code, $attachment_id);
		if (!isset($queryResult->id)){
			$this->status = 1118;
			$this->message = 'Attachment id not found';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}
	
		// check if user id is owner or not
		if($queryResult->b_user_id != $pelanggan->id){
			$this->status = 1119;
			$this->message = "You can't change this file name";
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}
	
		$attachment_name = $this->input->post('attachment_name');
		$get_old_extension = explode(".", $queryResult->file_name);
		$old_extension = $get_old_extension[1];

		$split_attachment_name = explode(".", $attachment_name);

		// credit: https://stackoverflow.com/a/11405509
		$whatIWant = substr($attachment_name, strpos($attachment_name, ".") + 1);
		if(empty($whatIWant)) {
			$this->status = 1134;
			$this->message = "Wrong extension file name";
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		if (!isset($split_attachment_name[1])) {
			$extension_attachment_name = '.'.$old_extension;
		} else if(isset($split_attachment_name[2])) {
			$this->status = 1134;
			$this->message = "Wrong extension file name";
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		} else {
			$extension_attachment_name = $old_extension;
		}

		if(isset($split_attachment_name[1])) {
			if($split_attachment_name[1] == $old_extension) {
				$new_attachment_name = $attachment_name;
			} else {
				$new_attachment_name = $split_attachment_name[0].'.'.$old_extension;
			}
		} else {
			$new_attachment_name = $attachment_name.$extension_attachment_name;
		}
	
		//start transaction and lock table
		$this->igdam->trans_start();
		
		$du = array();
		$du['file_name'] = $new_attachment_name;
		$res = $this->igdam->update($nation_code, $attachment_id, $du);
		if(!$res){
			$this->igdam->trans_rollback();
			$this->igdam->trans_end();
			$this->status = 1107;
			$this->message = "Error, please try again later";
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}
	
		$this->igdam->trans_commit();
		$this->igdam->trans_end();
	
		$this->status = 200;
		$this->message = 'Attachment renamed successfully';
		$data['attachment_name'] = $this->igdam->getByAttachmentId($nation_code, $attachment_id);
		$data['attachment_name']->url = $this->cdn_url($data['attachment_name']->url);
	
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
	}

	public function delete_attachment()
	{
	  	//initial
		$dt = $this->__init();

		//default result
		$data = array();
		$data['attachment'] = new stdClass();

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

		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if (!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
			die();
		}

		$timezone = $this->input->post('timezone');
		if($this->isValidTimezoneId($timezone) === false){
			$timezone = $this->default_timezone;
		}

		$attachment_id = $this->input->post('attachment_id');
		if (strlen($attachment_id) < 3){
			$this->status = 1118;
			$this->message = 'Attachment id is required';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}
	
		$queryResult = $this->igdam->getByAttachmentId($nation_code, $attachment_id);
		if (!isset($queryResult->id)){
			$this->status = 1118;
			$this->message = 'Attachment id not found';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		//start transaction and lock table
		$this->igdam->trans_start();
		
		$du = array();
		$du['is_active'] = 0;
		$res = $this->igdam->update($nation_code, $attachment_id, $du);
		if(!$res){
			$this->igdam->trans_rollback();
			$this->igdam->trans_end();
			$this->status = 1107;
			$this->message = "Error, please try again later";
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		$this->igdam->trans_commit();
		$this->igdam->trans_end();

		$this->status = 200;
		$this->message = 'Attachment deleted successfully';
		$data['attachment'] = [];

		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
	}

	public function publish_unpublish_to_group_post() {
		// /initial
		$dt = $this->__init();
	
		//default result
		$data = array();
	
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
	
		$get_action = $this->input->post('action');
		if (empty($get_action) || !in_array($get_action, array('publish', 'unpublish'))) {
			$this->status = 1112;
			$this->message = 'Missing or invalid action';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}
	
		$directory_id = trim($this->input->post('directory_id'));
		if (strlen($directory_id)<3){
			$this->status = 1116;
			$this->message = 'Directory id is required';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}
	
		$queryResult = $this->igdm->getByDirectoryId($nation_code, $directory_id);
		if (!isset($queryResult->id)){
			$this->status = 1116;
			$this->message = 'Directory id not found';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
			die();
		}

		if($get_action == "publish") {
			$this->igpostm->trans_start();

			// create post with album id
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['i_group_id'] = $queryResult->i_group_id;
			$di['b_user_id'] = $pelanggan->id;
			$di['deskripsi'] = "Come, share your photo in my album : ". $queryResult->directory_name . " " . $directory_id;
			$di['cdate'] = 'NOW()';
	
			$endDoWhile = 0;
			do {
	
				$id_post = $this->GUIDv4();
				$checkId = $this->igpostm->checkId($nation_code, $id_post);
				if($checkId == 0) {
					$endDoWhile = 1;
				}
	
			}	while($endDoWhile == 0);
			$di['id'] = $id_post;
	
			$res = $this->igpostm->set($di);
			if (!$res) {
				$this->igpostm->trans_rollback();
				$this->igpostm->trans_end();
				$this->status = 1107;
				$this->message = "Error, please try again later";
				$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
				die();
			}

			$this->igpostm->trans_commit();
			$this->igpostm->trans_end();

			// update is_publish by directory id
			$du = array();
			$du['is_publish'] = 1;
			$du['i_group_post_id'] = $id_post;
			$this->igdm->update($nation_code, $directory_id, $du);

			$data['post'] = $this->igpostm->getById($nation_code, $id_post);

		} else if($get_action == "unpublish") {
			// check if album haven't publish
			$getData = $this->igdm->getByDirectoryId($nation_code, $directory_id);
			if($getData->is_publish == 0) {
				$this->status = 1128;
				$this->message = "This album hasn't published yet, please publish first";
				$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
				die();
			}

			// update is_publish by directory id
			$du = array();
			$du['is_publish'] = 0;
			$this->igdm->update($nation_code, $directory_id, $du);

			// update set hide post
			$du = array();
			$du['is_active'] = 0;
			$this->igpostm->update($nation_code, $getData->i_group_post_id, $du);

			$data['post'] = $this->igpostm->getById($nation_code, $getData->i_group_post_id);
		}

		$this->status = 200;
		$this->message = "Success";
		
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
	}
}