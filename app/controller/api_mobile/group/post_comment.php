<?php
class Post_Comment extends JI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->lib("seme_log");
    $this->load("api_mobile/b_user_model", "bu");
    $this->load("api_mobile/b_user_alamat_model", "bua");
    $this->load("api_mobile/group/i_group_post_model", "igpostm");
    $this->load("api_mobile/group/i_group_post_comment_model", "igpcm");
    $this->load("api_mobile/group/i_group_post_comment_attachment_model", "igpcam");
    $this->load("api_mobile/group/i_group_post_like_model", "igplm");
    $this->load("api_mobile/b_user_alamat_model", "bua");
    $this->load("api_mobile/group/i_group_participant_model", "igparticipantm");
    $this->load("api_mobile/group/i_group_model", "igm");
    $this->load("api_mobile/group/i_group_notifications_model", "ignotifm");
    $this->load("api_mobile/common_code_model", "ccm");
    $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
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
			if (in_array($fileext, array("jpg", "png","jpeg"))) {
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

  private function __sortCol($sort_col, $tbl_as, $tbl2_as)
  {
    switch ($sort_col) {
      case 'id':
      $sort_col = "$tbl_as.id";
      break;
      case 'title':
      $sort_col = "$tbl_as.title";
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
    if ($sort_dir == "desc") {
      $sort_dir = "DESC";
    } else {
      $sort_dir = "ASC";
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

  private function __sortColDiscussion($sort_col, $tbl_as, $tbl2_as)
  {
    switch ($sort_col) {
      case 'cdate':
      $sort_col = "$tbl_as.cdate";
      break;
      default:
      $sort_col = "$tbl_as.cdate";
    }
    return $sort_col;
  }

  public function index()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['diskusi_total'] = 0;
    $data['diskusis'] = array();

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

    //populate input get
    $sort_col = $this->input->get("sort_col");
    $sort_dir = $this->input->get("sort_dir");
    $page = $this->input->get("page");
    $page_size = $this->input->get("page_size");
    $parent_i_group_post_comment_id = $this->input->get("parent_i_group_post_comment_id");
    $i_group_post_id = $this->input->get("i_group_post_id");
    $timezone = $this->input->get("timezone");
    
    if ($parent_i_group_post_comment_id<=0) {
      $parent_i_group_post_comment_id = 0;
    }
    if ($i_group_post_id<='0') {
      $i_group_post_id = 0;
    }

    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $group_id = trim($this->input->get("group_id"));
    if (strlen($group_id)>3){
      $queryResult = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $group_id, $pelanggan->id);
      if (!isset($queryResult->b_user_id)){
        $this->status = 1103;
        $this->message = 'You are not the member of this club';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }
    }

    $group_post = $this->igpostm->getById($nation_code, $i_group_post_id);
    if (!isset($group_post->id)) {
      $this->status = 1160;
      $this->message = 'This post is deleted by an author';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    //sanitize input
    $tbl_as = $this->igpcm->getTblAs();
    $tbl2_as = $this->igpcm->getTbl2As();
    $sort_col = $this->__sortColDiscussion($sort_col, $tbl_as, $tbl2_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    // $page_child_discussion = $this->__page(1);
    $page_size = $this->__pageSize($page_size);
    // $page_size_child_discussion = $this->__pageSize(1);

    //get produk data
    // $ddcount = $this->igpcm->countAll($nation_code,$parent_i_group_post_comment_id, $i_group_post_id); //get total discussion without child
    $ddcount = $group_post->total_discussion; //get total discussion with child
    $ddata = $this->igpcm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $parent_i_group_post_comment_id, $i_group_post_id, $pelanggan);
    foreach ($ddata as &$dd) {
      $dd->total_likes = $this->thousandsCurrencyFormat($dd->total_likes);
      $dd->is_liked = '0';
      $dd->currentLikeEmoji = '';
      $dd->is_owner_reply = "0";
      if(isset($pelanggan->id)){
        $checkLike = $this->igplm->getByCustomIdUserId($nation_code, $dd->id, $pelanggan->id, "discussion");
        if(isset($checkLike->id)){
          $dd->is_liked = '1';
          $dd->currentLikeEmoji = $checkLike->i_group_post_like_category_id;
        }
        if($dd->b_user_id == $pelanggan->id){
          $dd->is_owner_reply = "1";
        }
      }

      $dd->is_owner_or_admin = '0';
      if (strlen($group_id)>3){
        if($queryResult->is_owner == "1" || $queryResult->is_co_admin == "1"){
          $dd->is_owner_or_admin = "1";
        }
      }

      $dd->cdate_text = $this->humanTiming($dd->cdate, null, $pelanggan->language_id);
      $dd->cdate = $this->customTimezone($dd->cdate, $timezone);
      $dd->text = html_entity_decode($dd->text,ENT_QUOTES);
      if(file_exists(SENEROOT.$dd->b_user_band_image) && $dd->b_user_band_image != 'media/user/default.png'){
        $dd->b_user_band_image = $this->cdn_url($dd->b_user_band_image);
      } else {
        $dd->b_user_band_image = $this->cdn_url('media/user/default-profile-picture.png');
      }

      $dd->images = new stdClass();
      $dd->locations = new stdClass();
      $attachments = $this->igpcam->getByDiscussionId($nation_code, $dd->id);
      foreach ($attachments as $atc) {
        if($atc->jenis == 'image'){
          $atc->url = $this->cdn_url($atc->url);
          $atc->url_thumb = $this->cdn_url($atc->url_thumb);
          $dd->images = $atc;
        }else{
          $dd->locations = $atc;
        }
      }
      unset($attachments);

      $dd->diskusi_anak_total = $this->igpcm->countAll($nation_code,$dd->id, $i_group_post_id);
      // $dd->diskusi_anak = $this->igpcm->getAll($nation_code, $page_child_discussion, $page_size_child_discussion, $sort_col, $sort_dir, $dd->id, $i_group_post_id, $pelanggan);
      $dd->diskusi_anak = $this->igpcm->getAll($nation_code, 0, 0, $sort_col, 'ASC', $dd->id, $i_group_post_id, $pelanggan);
      foreach ($dd->diskusi_anak as &$value) {
        // $value->can_chat_and_like = $dd->can_chat_and_like;
        $value->total_likes = $this->thousandsCurrencyFormat($value->total_likes);
        $value->is_liked = '0';
        $value->currentLikeEmoji = '';
        $value->is_owner_reply = "0";
        if(isset($pelanggan->id)){
          $checkLike = $this->igplm->getByCustomIdUserId($nation_code, $value->id, $pelanggan->id, "discussion");
          if(isset($checkLike->id)){
            $value->is_liked = '1';
            $value->currentLikeEmoji = $checkLike->i_group_post_like_category_id;
          }
          if($value->b_user_id == $pelanggan->id){
            $value->is_owner_reply = "1";
          }
        }

        $value->is_owner_or_admin = '0';
        if (strlen($group_id)>3){
          if($queryResult->is_owner == "1" || $queryResult->is_co_admin == "1"){
            $value->is_owner_or_admin = "1";
          }
        }

        $value->cdate_text = $this->humanTiming($value->cdate, null, $pelanggan->language_id);
        $value->cdate = $this->customTimezone($value->cdate, $timezone);
        $value->text = html_entity_decode($value->text,ENT_QUOTES);
        if(file_exists(SENEROOT.$value->b_user_band_image) && $value->b_user_band_image != 'media/user/default.png'){
          $value->b_user_band_image = $this->cdn_url($value->b_user_band_image);
        } else {
          $value->b_user_band_image = $this->cdn_url('media/user/default-profile-picture.png');
        }

        $value->images = new stdClass();
        $value->locations = new stdClass();
        $attachments = $this->igpcam->getByDiscussionId($nation_code, $value->id);
        foreach ($attachments as $atc) {
          if($atc->jenis == 'image'){
            $atc->url = $this->cdn_url($atc->url);
            $atc->url_thumb = $this->cdn_url($atc->url_thumb);
            $value->images = $atc;
          }else{
            $value->locations = $atc;
          }
        }
        unset($attachments);
      }
    }
    unset($dd); //free some memory

    //build result
    $data['diskusis'] = $ddata;
    $data['diskusi_total'] = $ddcount;

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function add_comment()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['total_comment'] = 0;
    $data['comments'] = new stdClass();

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

    $this->status = 300;
    $this->message = 'Missing one or more parameters';

    $parent_i_group_post_comment_id = $this->input->post('parent_i_group_post_comment_id');
    $text = trim($this->input->post('text'));
    $i_group_post_id = $this->input->post('i_group_post_id');
    $b_user_id_to = $this->input->post('b_user_id_to');
    $show_nama = $this->input->post('show_nama');
    $location_nama = $this->input->post("location_nama");
    $location_address = $this->input->post("location_address");
    $location_place_id = $this->input->post("location_place_id");
    $location_latitude = $this->input->post("location_latitude");
    $location_longitude = $this->input->post("location_longitude");
    $timezone = $this->input->post("timezone");
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    //input validation
    if (empty($parent_i_group_post_comment_id)) {
      $parent_i_group_post_comment_id = 0;
    }

    if ($b_user_id_to <= '0') {
      $this->status = 1098;
      $this->message = 'Invalid b_user_id_to';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    if($show_nama == '' || $show_nama != 0 || $show_nama == "NULL") {
      $show_nama = 1;
    }

    $text = str_replace('’',"'",$text);
    $text = nl2br($text);
    $text = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $text);
    $text = str_replace("\\n", "<br />", $text);

    if (empty($i_group_post_id)) {
      $i_group_post_id = 0;
    }

    //validating
    if($parent_i_group_post_comment_id<0){
      $this->status = 1099;
      $this->message = 'Invalid parent_i_group_post_comment_id';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    if($i_group_post_id <= '0'){
      $this->status = 1135;
      $this->message = 'Invalid Club Post ID or Club Post not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $group_post = $this->igpostm->getById($nation_code, $i_group_post_id);
    if (!isset($group_post->id)) {
      $this->status = 1160;
      $this->message = 'This post is deleted by an author';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
      die();
    }

    $group_id = trim($this->input->post('group_id'));
    if (strlen($group_id)<3){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $queryResult = $this->igm->getGroupSettings($nation_code, $group_id);
    if (!isset($queryResult->id)){
      $this->status = 1101;
      $this->message = 'Club id not found';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    // //START by Donny Dennison - 21 november 2022 10:02
    // //new feature, block
    // if($pelanggan->id != $community->b_user_id_starter){
    //   $blockDataCommunity = $this->cbm->getById($nation_code, 0, $pelanggan->id, "post_comment", $community->id);
    //   $blockDataAccount = $this->cbm->getById($nation_code, 0, $community->b_user_id_starter, "account", $pelanggan->id);
    //   $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $community->b_user_id_starter);
    //   if(isset($blockDataCommunity->block_id) || isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){
    //     $this->status = 1005;
    //     $this->message = "You can no reply as you're blocked";
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_blocked_reply");
    //     die();
    //   }
    // }
    //END by Donny Dennison - 21 november 2022 10:02
    //new feature, block

    // $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
    // if(isset($pelangganAddress->alamat2)){
    //   if($community->postal_district == $pelangganAddress->postal_district){

    //   }else{
    //     $this->status = 1099;
    //     $this->message = 'You\'re not allowed to join Group Chat outside your neighborhood';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "post_comment");
    //     die();
    //   }
    // }else{
    //   $this->status = 1099;
    //   $this->message = 'You\'re not allowed to join Group Chat outside your neighborhood';
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "post_comment");
    //   die();
    // }

    $user_type = 'follower';
    if($group_post->b_user_id == $pelanggan->id){
      $user_type = 'starter';
    }

    $this->igpcm->trans_start();

    $di = array();
    $di['nation_code'] = $nation_code;
    $di['parent_i_group_post_comment_id'] = $parent_i_group_post_comment_id;
    $di['i_group_post_id'] = $i_group_post_id;
    $di['i_group_id'] = $group_id;
    $di['b_user_id'] = $pelanggan->id;
    $di['b_user_id_to'] = $b_user_id_to;
    $di['show_nama'] = $show_nama;
    $di['user_type'] = $user_type;
    $di['text'] = $text;
    $di['cdate'] = 'NOW()';
    $endDoWhile = 0;
    do {
      $comment_id = $this->GUIDv4();
      $checkId = $this->igpcm->checkId($nation_code, $comment_id);
      if($checkId == 0){
        $endDoWhile = 1;
      }
    } while($endDoWhile == 0);
    $di['id'] = $comment_id;
    $res = $this->igpcm->set($di);
    if (!$res) {
      $this->igpcm->trans_rollback();
      $this->igpcm->trans_end();
      $this->status = 1107;
      $this->message = "Error, please try again later";
      // $this->seme_log->write("api_mobile", 'API_Mobile/Group_Post::Comment::baru -- RollBack -- forceClose '.$this->status.' '.$this->message);
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->status = 200;
    $this->message = 'Success';

    if(isset($_FILES['foto']['name'])){
      $endDoWhile = 0;
      do {
        $photoId_last = $this->GUIDv4();
        $checkId = $this->igpcam->checkId($nation_code, $photoId_last);
        if($checkId == 0){
          $endDoWhile = 1;
        }
      } while($endDoWhile == 0);
      $sc = $this->__uploadImagex($nation_code, 'foto', $comment_id, $photoId_last);
      if (isset($sc->status)) {
        if ($sc->status==200) {
          $dix = array();
          $dix['nation_code'] = $nation_code;
          $dix['i_group_post_comment_id'] = $comment_id;
          $dix['id'] = $photoId_last;
          $dix['jenis'] = 'image';
          $dix['url'] = $sc->image;
          $dix['url_thumb'] = $sc->thumb;
          $this->igpcam->set($dix);
        }
      }
    }

    if($location_nama && $location_address && $location_place_id && $location_latitude && $location_longitude){
      $dix = array();
      $dix['nation_code'] = $nation_code;
      $dix['i_group_post_comment_id'] = $comment_id;
      $dix['jenis'] = 'location';
      $dix['location_nama'] = $location_nama;
      $dix['location_address'] = $location_address;
      $dix['location_place_id'] = $location_place_id;
      $dix['location_latitude'] = $location_latitude;
      $dix['location_longitude'] = $location_longitude;
      $endDoWhile = 0;
      do {
        $locationId_last = $this->GUIDv4();
        $checkId = $this->igpcam->checkId($nation_code, $locationId_last);
        if($checkId == 0){
          $endDoWhile = 1;
        }
      } while($endDoWhile == 0);
      $dix['id'] = $locationId_last;
      $this->igpcam->set($dix);
    }

    if($user_type == 'follower'){
      $totalReplyUser = $this->igpcm->countAllPostIDUserID($nation_code, $pelanggan->id, $i_group_post_id);
      if($totalReplyUser == 1){
        //get point
        $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E25");
        if (!isset($pointGet->remark)) {
          $pointGet = new stdClass();
          $pointGet->remark = 1;
        }

        $di = array();
        $di['nation_code'] = $nation_code;
        $di['b_user_alamat_location_kelurahan'] = "All";
        $di['b_user_alamat_location_kecamatan'] = "All";
        $di['b_user_alamat_location_kabkota'] = "All";
        $di['b_user_alamat_location_provinsi'] = "All";
        $di['b_user_id'] = $group_post->b_user_id;
        $di['point'] = $pointGet->remark;
        $di['custom_id'] = $i_group_post_id;
        $di['custom_type'] = 'club';
        $di['custom_type_sub'] = "reply";
        $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' post and creator post get '.$di['point'].' point(s)';
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
        // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '+', $di['point']);
      }
    }

    // $this->seme_log->write("api_mobile", 'API_Mobile/Group_Post::Comment::baru -- INFO '.$this->status.' '.$this->message);
    $this->igpcm->trans_commit();
    $this->igpcm->trans_end();

    //update total_discussion in table c_community
    $this->igpostm->updateTotal($nation_code, $i_group_post_id, "total_discussion", '+', 1);

    if($b_user_id_to != $pelanggan->id){
      /* start comment */
      //   //START by Donny Dennison - 29 july 2022 13:22
      //   //new feature, block community post or account
      //   $blockDataCommunity = $this->cbm->getById($nation_code, 0, $b_user_id_to, "post_comment", $i_group_post_id);
      //   $blockDataAccount = $this->cbm->getById($nation_code, 0, $b_user_id_to, "account", $pelanggan->id);
      //   $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $b_user_id_to);

      //   if(!isset($blockDataCommunity->block_id) && !isset($blockDataAccount->block_id) && !isset($blockDataAccountReverse->block_id)){
      //   //END by Donny Dennison - 29 july 2022 13:22
      //   //new feature, block community post or account

      // select fcm token
      $user = $this->bu->getById($nation_code, $b_user_id_to);

      $dpe = array();
      $dpe['nation_code'] = $nation_code;
      $dpe['b_user_id'] = $b_user_id_to;
      $dpe['type'] = "band_group_post_comment";
      if($user->language_id == 2) {
        $dpe['judul'] = "Balasan Baru";
        $dpe['teks'] =  "Anda mendapat balasan";
      } else {
        $dpe['judul'] = "New Reply";
        $dpe['teks'] =  "You got a reply";
      }
      $dpe['gambar'] = 'media/pemberitahuan/community.png';
      $dpe['cdate'] = "NOW()";
      $extras = new stdClass();
      $extras->i_group_post_comment_id = $comment_id;
      $extras->i_group_post_id = $i_group_post_id;
      $extras->i_group_id = $group_post->i_group_id;
      if($user->language_id == 2) { 
        $extras->judul = "Balasan Baru";
        $extras->teks =  "Anda mendapat balasan";
      } else {
        $extras->judul = "New Reply";
        $extras->teks =  "You got a reply";
      }
      $dpe['group_name'] = $group_post->group_name;
      $dpe['i_group_id'] = $group_post->i_group_id;
      $dpe['extras'] = json_encode($extras);
      $endDoWhile = 0;
      do{
        $notifId = $this->GUIDv4();
        $checkId = $this->ignotifm->checkId($nation_code, $notifId);
        if($checkId == 0){
          $endDoWhile = 1;
        }
      }while($endDoWhile == 0);
      $dpe['id'] = $notifId;
      $this->ignotifm->set($dpe);

      if ($user->is_band_push_notif == "1" && $user->is_active == 1) {
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
          $title = "Balasan Baru";
          $message = "Anda mendapat balasan";
        } else {
          $title = "New Reply";
          $message = "You got a reply";
        }
        $image = 'media/pemberitahuan/community.png';
        $type = 'band_group_post_comment';
        $payload = new stdClass();
        $payload->i_group_post_comment_id = $comment_id;
        $payload->i_group_post_id = $i_group_post_id;
        $payload->i_group_id = $group_post->i_group_id;
        if($user->language_id == 2) {
          $payload->judul = "Balasan Baru";
          $payload->teks = "Anda mendapat balasan";
        } else {
          $payload->judul = "New Reply";
          $payload->teks = "You got a reply";
        }
        $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
      }

      //   //START by Donny Dennison - 29 july 2022 13:22
      //   //new feature, block community post or account
      //   }
      //   //END by Donny Dennison - 29 july 2022 13:22
      //   //new feature, block community post or account
      // }

      /* start comment */
      // //START by Donny Dennison - 16 december 2021 15:49
      // //get point as leaderboard rule
      // if($user_type == 'follower'){
      //   //get total active reply from $pelanggan
      //   $totalActiveReplyUser = $this->igpcm->countAllCommunityIDUserID($nation_code, $pelanggan->id, $i_group_post_id);
      //   if($totalActiveReplyUser == 1){
      //     //get limit left
      //     $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "EH");
      //     if(!isset($limitLeft->limit_plus)){
      //       $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EH");

      //       $lastID = $this->glplm->getLastId($nation_code, date("Y-m-d"));

      //       $du = array();
      //       $du['nation_code'] = $nation_code;
      //       $du['id'] = $lastID;
      //       $du['cdate'] = date("Y-m-d");
      //       $du['b_user_id'] = $pelanggan->id;
      //       $du['code'] = "EH";
      //       $du['limit_plus'] = $pointGet->remark;
      //       $du['limit_minus'] = $pointGet->remark;
      //       $this->glplm->set($du);

      //       //get limit left
      //       $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "EH");
      //     }

      //     if($limitLeft->limit_plus > 0){
      //       //get point
      //       $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EI");
      //       if (!isset($pointGet->remark)) {
      //         $pointGet = new stdClass();
      //         $pointGet->remark = 1;
      //       }

      //       $di = array();
      //       $di['nation_code'] = $nation_code;
      //       $di['b_user_alamat_location_kelurahan'] = $d_address->kelurahan;
      //       $di['b_user_alamat_location_kecamatan'] = $d_address->kecamatan;
      //       $di['b_user_alamat_location_kabkota'] = $d_address->kabkota;
      //       $di['b_user_alamat_location_provinsi'] = $d_address->provinsi;
      //       $di['b_user_id'] = $pelanggan->id;
      //       $di['point'] = $pointGet->remark;
      //       $di['custom_id'] = $i_group_post_id;
      //       $di['custom_type'] = 'community';
      //       $di['custom_type_sub'] = 'reply';
      //       $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
      //       $this->glphm->set($di);
      //       // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '+', $di['point']);
      //       $this->glplm->updateTotal($nation_code, date("Y-m-d"), $pelanggan->id, 'EH', 'limit_plus', '-', 1);
      //     }
      //   }
      // }
      // //END by Donny Dennison - 16 december 2021 15:49
      /* end comment */
    }

    $data['comments'] = $this->igpcm->getbyDiscussionIDPostID($nation_code, $comment_id, $i_group_post_id);
    // $data['comments']->can_chat_and_like = "1";
    $data['comments']->is_liked = '0';
    $data['comments']->is_owner_reply = "1";
    $data['comments']->is_owner_or_admin = '0';

    $data['comments']->cdate_text = $this->humanTiming($data['comments']->cdate, null, $pelanggan->language_id);
    $data['comments']->cdate = $this->customTimezone($data['comments']->cdate, $timezone);
    $data['comments']->text = html_entity_decode($data['comments']->text,ENT_QUOTES);
    if(file_exists(SENEROOT.$data['comments']->b_user_band_image) && $data['comments']->b_user_band_image != 'media/user/default.png'){
      $data['comments']->b_user_band_image = $this->cdn_url($data['comments']->b_user_band_image);
    } else {
      $data['comments']->b_user_band_image = $this->cdn_url('media/user/default-profile-picture.png');
    }

    $data['comments']->images = new stdClass();
    $data['comments']->locations = new stdClass();
    $attachments = $this->igpcam->getByDiscussionId($nation_code, $data['comments']->discussion_id);
    foreach ($attachments as $atc) {
      if($atc->jenis == 'image'){
        $atc->url = $this->cdn_url($atc->url);
        $atc->url_thumb = $this->cdn_url($atc->url_thumb);
        $data['comments']->images = $atc;
      }else{
        $data['comments']->locations = $atc;
      }
    }
    unset($attachments);

    $data['total_comment'] = $this->igpostm->getById($nation_code, $i_group_post_id)->total_discussion;

    if($parent_i_group_post_comment_id != "0"){
      $data['comments']->diskusi_anak_total = $this->igpcm->countAll($nation_code,$parent_i_group_post_comment_id, $i_group_post_id);
    }else{
      $data['comments']->diskusi_anak_total = "0";
    }

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }

  public function delete_comment()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['total_discussion'] = "0";
    $data['comments'] = array();

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
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //populate input get
    $discussion_id = $this->input->get("discussion_id");

    $group_id = trim($this->input->get('group_id'));
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

    //check discussion id and user id
    $c = $this->igpcm->getbyDiscussionID($nation_code, $discussion_id);
    if ($c->b_user_id != $pelanggan->id) {
      $stillParticipant = $this->igparticipantm->getByGroupIdParticipantId($nation_code, $group_id, $pelanggan->id);
      if ($stillParticipant->is_owner == "0" && $stillParticipant->is_co_admin == "0") {
        $this->status = 1125;
        $this->message = "Your priviledge is limited as you're member";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
        die();
      }
    }

    //start transaction and lock table
    $this->igpcm->trans_start();

    $di = array();
    $di['edate'] = 'NOW()';
    $di['is_active'] = 0;
    $res = $this->igpcm->update($nation_code, $discussion_id, $di);
    if (!$res) {
      $this->igpcm->trans_rollback();
      $this->igpcm->trans_end();
      $this->status = 1107;
      $this->message = "Error, please try again later";
      // $this->seme_log->write("api_mobile", 'API_Mobile/Group_Post::Comment::hapus -- RollBack -- forceClose '.$this->status.' '.$this->message);
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
      die();
    }

    $this->status = 200;
    $this->message = 'Success';

    //update total_discussion in table c_community
    $this->igpostm->updateTotal($nation_code, $c->i_group_post_id, "total_discussion", '-', 1);

    //if discussion is a parent, child also deleted
    if($c->parent_i_group_post_comment_id == 0){
      $getTotalChildIsActive = $this->igpcm->countAll($nation_code, $c->discussion_id, $c->i_group_post_id);
      //update total_discussion in table c_community
      $this->igpostm->updateTotal($nation_code, $c->i_group_post_id, "total_discussion", '-', $getTotalChildIsActive);

      $di = array();
      $di['edate'] = 'NOW()';
      $di['is_active'] = 0;
      $this->igpcm->updateByParentCommunityDiscussionId($nation_code, $discussion_id, $di);
    }

    if($c->user_type == 'follower'){
      $totalReplyUser = $this->igpcm->countAllPostIDUserID($nation_code, $c->b_user_id, $c->i_group_post_id);
      if($totalReplyUser == 0){
        $group_post = $this->igpostm->getById($nation_code, $c->i_group_post_id);

        //get point
        $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E25");
        if (!isset($pointGet->remark)) {
          $pointGet = new stdClass();
          $pointGet->remark = 1;
        }

        $di = array();
        $di['nation_code'] = $nation_code;
        $di['b_user_alamat_location_kelurahan'] = "All";
        $di['b_user_alamat_location_kecamatan'] = "All";
        $di['b_user_alamat_location_kabkota'] = "All";
        $di['b_user_alamat_location_provinsi'] = "All";
        $di['b_user_id'] = $group_post->b_user_id;
        $di['point'] = $pointGet->remark;
        $di['custom_id'] = $c->i_group_post_id;
        $di['custom_type'] = 'club';
        $di['custom_type_sub'] = "reply";
        $di['custom_text'] = $pelanggan->fnama.' has delete '.$di['custom_type_sub'].' '.$di['custom_type'].' post and creator post deduct '.$di['point'].' point(s)';
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
        // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '+', $di['point']);
      }
    }

    // $this->seme_log->write("api_mobile", 'API_Mobile/Group_Post::Comment::hapus -- INFO '.$this->status.' '.$this->message);
    $this->igpcm->trans_commit();
    $this->igpcm->trans_end();

    //START by Donny Dennison - 16 december 2021 15:49
    //get point as leaderboard rule
    // if($c->user_type == 'follower'){

    //   //get total active reply from $pelanggan
    //   $totalActiveReplyUser = $this->igpcm->countAllCommunityIDUserID($nation_code, $pelanggan->id, $c->i_group_post_id);

    //   if($totalActiveReplyUser == 0){

    //     //get limit left
    //     $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "EH");
        
    //     if(!isset($limitLeft->limit_minus)){

    //       $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EH");

    //       $lastID = $this->glplm->getLastId($nation_code, date("Y-m-d"));

    //       $du = array();
    //       $du['nation_code'] = $nation_code;
    //       $du['id'] = $lastID;
    //       $du['cdate'] = date("Y-m-d");
    //       $du['b_user_id'] = $pelanggan->id;
    //       $du['code'] = "EH";
    //       $du['limit_plus'] = $pointGet->remark;
    //       $du['limit_minus'] = $pointGet->remark;
    //       $this->glplm->set($du);

    //       //get limit left
    //       $limitLeft = $this->glplm->getByUserId($nation_code, date("Y-m-d"), $pelanggan->id, "EH");

    //     }

    //     if($limitLeft->limit_minus > 0){

    //       //get point
    //       $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EI");
    //       if (!isset($pointGet->remark)) {
    //         $pointGet = new stdClass();
    //         $pointGet->remark = 1;
    //       }

    //       $di = array();
    //       $di['nation_code'] = $nation_code;
    //       $di['b_user_alamat_location_kelurahan'] = $c->kelurahan;
    //       $di['b_user_alamat_location_kecamatan'] = $c->kecamatan;
    //       $di['b_user_alamat_location_kabkota'] = $c->kabkota;
    //       $di['b_user_alamat_location_provinsi'] = $c->provinsi;
    //       $di['b_user_id'] = $pelanggan->id;
    //       $di['plusorminus'] = "-";
    //       $di['point'] = $pointGet->remark;
    //       $di['custom_id'] = $c->i_group_post_id;
    //       $di['custom_type'] = 'community';
    //       $di['custom_type_sub'] = 'reply';
    //       $di['custom_text'] = $pelanggan->fnama.' has delete '.$di['custom_type'].' '.$di['custom_type_sub'].' and lose '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
    //       $this->glphm->set($di);
    //       // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
    //       $this->glplm->updateTotal($nation_code, date("Y-m-d"), $pelanggan->id, 'EH', 'limit_minus', '-', 1);
    //       $this->glplm->updateTotal($nation_code, date("Y-m-d"), $pelanggan->id, 'EH', 'limit_plus', '+', 1);
    //     }
    //   }
    // }
    //END by Donny Dennison - 16 december 2021 15:49

    $data['total_discussion'] = $this->igpostm->getById($nation_code, $c->i_group_post_id);
    if(isset($data['total_discussion']->total_discussion)){
      $data['total_discussion'] = $data['total_discussion']->total_discussion;
    }else{
      $data['total_discussion'] = "0";
    }

    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
  }
}
